<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Load the Google API PHP Client Library.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define a path to the credentials file.
define('CREDENTIALS_PATH', dirname(__DIR__) . '/config/credentials.json');
define('TOKEN_PATH', dirname(__DIR__) . '/config/token.json');

use Spatie\Dropbox\Client as Dropbox_Client;

class Google_workspace extends AdminController
{
    private $client;
    private $service;
    private $docsService;
    private $sheetsService;
    public function __construct()
    {
        parent::__construct();

        if (staff_cant('setting', 'google_workspace') && staff_cant('view', 'google_workspace') && staff_cant('create', 'google_workspace') && staff_cant('edit', 'google_workspace') && staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }

        $this->load->model('google_workspace_model');
    }

    public function _create_client($type = '')
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Google Workspace - ' . get_option('companyname'));
        $this->client->setScopes([
            Google_Service_Docs::DOCUMENTS,
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Slides::PRESENTATIONS,
            Google_Service_Drive::DRIVE,
            Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE_METADATA,
            'email',
            'profile',
        ]);
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig(CREDENTIALS_PATH);
    }

    public function _set_service()
    {
        if (file_exists(TOKEN_PATH)) {
            $accessToken = json_decode(file_get_contents(TOKEN_PATH), true);
            $_SESSION['access_token'] = $accessToken;
            $this->client->setAccessToken($accessToken);
        }

        if (!isset($_SESSION['access_token'])) {
            $auth_url = $this->client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $_SESSION['access_token'] = $this->client->getAccessToken();
            } else {
                redirect($this->client->createAuthUrl());
                die;
            }
        }

        // Get a new instance of the Google Drive service
        $this->service = new Google_Service_Drive($this->client);
        $this->docsService = new Google_Service_Docs($this->client);
        $this->sheetsService = new Google_Service_Sheets($this->client);
        $this->slidesService = new Google_Service_Slides($this->client);
        $this->formsService = new Google_Service_Sheets($this->client);
    }

    public function docs()
    {
        if (staff_cant('setting', 'google_workspace') && staff_cant('view', 'google_workspace') && staff_cant('create', 'google_workspace') && staff_cant('edit', 'google_workspace') && staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }



        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_workspace', 'tables/docs'));
        }

        $data['title'] = _l('google_workspace');

        $this->load->view('docs', $data);
    }

    public function sheets()
    {
        if (staff_cant('setting', 'google_workspace') && staff_cant('view', 'google_workspace') && staff_cant('create', 'google_workspace') && staff_cant('edit', 'google_workspace') && staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_workspace', 'tables/sheets'));
        }

        $data['title'] = _l('google_workspace');

        $this->load->view('sheets', $data);
    }

    public function slides()
    {
        if (staff_cant('setting', 'google_workspace') && staff_cant('view', 'google_workspace') && staff_cant('create', 'google_workspace') && staff_cant('edit', 'google_workspace') && staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }



        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_workspace', 'tables/slides'));
        }

        $data['title'] = _l('google_workspace');

        $this->load->view('slides', $data);
    }

    public function forms()
    {
        if (staff_cant('setting', 'google_workspace') && staff_cant('view', 'google_workspace') && staff_cant('create', 'google_workspace') && staff_cant('edit', 'google_workspace') && staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }



        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_workspace', 'tables/forms'));
        }

        $data['title'] = _l('google_workspace');

        $this->load->view('forms', $data);
    }

    public function drives()
    {
        if (staff_cant('setting', 'google_workspace') && staff_cant('view', 'google_workspace') && staff_cant('create', 'google_workspace') && staff_cant('edit', 'google_workspace') && staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }

		

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_workspace', 'tables/drives'));
        }

        $data['title'] = _l('google_workspace');

        $this->load->view('drives', $data);
    }

    public function save()
    {
        $this->_create_client();

        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $type = $this->input->post('type');

        if ($title) {
            $this->_set_service();

            if ($type == 'doc') {
                if ($id) {
                    $google_doc = $this->google_workspace_model->get($id);

                    if ($google_doc) {
                        try {
                            // Update the doc properties
                            $fileMetadata = new Google_Service_Drive_DriveFile([
                                'name' => $title
                            ]);

                            $this->service->files->update($google_doc->driveid, $fileMetadata);

                            $requests = [
                                new Google_Service_Docs_Request([
                                    'insertText' => [
                                        'location' => [
                                            'index' => 1, // Insert at the beginning of the document
                                        ],
                                        'text' => $description,
                                    ]
                                ])
                            ];
                            // Execute the batchUpdate request
                            $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                                'requests' => $requests
                            ]);
                            $this->docsService->documents->batchUpdate($google_doc->driveid, $batchUpdateRequest);

                            $this->google_workspace_model->update([
                                'title' => $title,
                                'description' => $description
                            ], $google_doc->id);

                            echo json_encode([
                                'success' => true,
                                'id' => $id,
                                'message' => _l('google_workspace_doc_saved_successfully', _l('google_workspace')),
                            ]);
                        } catch (Exception $e) {
                            echo json_encode([
                                'success' => false,
                                'message' => _l('google_workspace_integrate_again', _l('google_workspace')),
                                'redirect_url' => admin_url('google_workspace/settings')
                            ]);
                        }
                    }
                } else {
                    try {
                        // Create a new document
                        $fileMetadata = new Google_Service_Drive_DriveFile([
                            'name' => $title,
                            'mimeType' => 'application/vnd.google-apps.document'
                        ]);

                        $document = $this->service->files->create($fileMetadata, ['fields' => 'id']);

                        // Get the document ID
                        $documentId = $document->id;

                        $requests = [
                            new Google_Service_Docs_Request([
                                'insertText' => [
                                    'location' => [
                                        'index' => 1, // Insert at the beginning of the document
                                    ],
                                    'text' => $description,
                                ]
                            ])
                        ];
                        // Execute the batchUpdate request
                        $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                            'requests' => $requests
                        ]);
                        $this->docsService->documents->batchUpdate($documentId, $batchUpdateRequest);

                        $new_doc_id = $this->google_workspace_model->add([
                            'staffid' => get_staff_user_id(),
                            'driveid' => $documentId,
                            'title' => $title,
                            'type' => 'doc',
                            'description' => $description
                        ]);

                        echo json_encode([
                            'success' => true,
                            'id' => $new_doc_id,
                            'message' => _l('google_workspace_doc_created_successfully', _l('google_workspace')),
                        ]);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => _l('google_workspace_integrate_again', _l('google_workspace')),
                            'redirect_url' => admin_url('google_workspace/settings')
                        ]);
                    }
                }
            } else if ($type == 'sheet') {
                if ($id) {
                    $google_sheet = $this->google_workspace_model->get($id);

                    if ($google_sheet) {
                        try {
                            // Update the spreadsheet properties
                            $requests = [
                                new Google_Service_Sheets_Request([
                                    'updateSpreadsheetProperties' => [
                                        'properties' => [
                                            'title' => $title,
                                        ],
                                        'fields' => 'title',
                                    ],
                                ]),
                                new Google_Service_Sheets_Request([
                                    'createDeveloperMetadata' => [
                                        'developerMetadata' => [
                                            'metadataKey' => 'description',
                                            'metadataValue' => $description,
                                            'location' => [
                                                'spreadsheet' => true,
                                            ],
                                            'visibility' => 'DOCUMENT',
                                        ],
                                    ],
                                ]),
                            ];

                            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                                'requests' => $requests,
                            ]);

                            $response = $this->sheetsService->spreadsheets->batchUpdate($google_sheet->driveid, $batchUpdateRequest);

                            $this->google_workspace_model->update([
                                'title' => $title,
                                'description' => $description
                            ], $google_sheet->id);

                            echo json_encode([
                                'success' => true,
                                'id' => $id,
                                'message' => _l('google_workspace_sheet_saved_successfully', _l('google_workspace')),
                            ]);
                        } catch (Exception $e) {
                            echo json_encode([
                                'success' => false,
                                'message' => _l('google_workspace_integrate_again', _l('google_workspace')),
                                'redirect_url' => admin_url('google_workspace/settings')
                            ]);
                        }
                    }
                } else {
                    try {
                        // Create a new spreadsheet
                        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                            'properties' => [
                                'title' => $title
                            ]
                        ]);

                        // Execute the request
                        $spreadsheet = $this->sheetsService->spreadsheets->create($spreadsheet, [
                            'fields' => 'spreadsheetId'
                        ]);
                        // Get the spreadsheet ID
                        $spreadsheetId = $spreadsheet->spreadsheetId;

                        if ($description) {
                            // Add a new sheet to the spreadsheet
                            $requests = [
                                new Google_Service_Sheets_Request([
                                    'createDeveloperMetadata' => [
                                        'developerMetadata' => [
                                            'metadataKey' => 'description',
                                            'metadataValue' => $description,
                                            'location' => [
                                                'spreadsheet' => true
                                            ],
                                            'visibility' => 'DOCUMENT'
                                        ]
                                    ]
                                ])
                            ];
                            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                                'requests' => $requests
                            ]);

                            $this->sheetsService->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
                        }

                        $sheet_id = $this->google_workspace_model->add([
                            'staffid' => get_staff_user_id(),
                            'driveid' => $spreadsheetId,
                            'title' => $title,
                            'type' => 'sheet',
                            'description' => $description
                        ]);

                        echo json_encode([
                            'success' => true,
                            'id' => $sheet_id,
                            'message' => _l('google_workspace_sheet_created_successfully', _l('google_workspace')),
                        ]);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => _l('google_workspace_integrate_again', _l('google_workspace')),
                            'redirect_url' => admin_url('google_workspace/settings')
                        ]);
                    }
                }
            } else {
                if ($id) {
                    $google_workspace = $this->google_workspace_model->get($id);

                    if ($google_workspace) {
                        try {
                            if ($type == 'drive') {
                                $fileName = uniqid() . '_' . $_FILES['file']['name'];
                                $temp_url = TEMP_FOLDER . $fileName;

                                move_uploaded_file($_FILES['file']['tmp_name'], $temp_url);
                            }

                            // Update the drive properties
                            $driveFile = $this->service->files->get($google_workspace->driveid, ['fields' => 'name, description']);
                            $driveFile->setName($title);
                            $driveFile->setDescription($description);

                            if ($type == 'drive') {
                                $this->service->files->update(
                                    $google_workspace->driveid,
                                    $driveFile,
                                    [
                                        'data' => file_get_contents($temp_url),
                                        'mimeType' => mime_content_type($temp_url),
                                        'uploadType' => 'multipart'
                                    ]
                                );
                            } else {
                                $this->service->files->update(
                                    $google_workspace->driveid,
                                    $driveFile
                                );
                            }

                            $this->google_workspace_model->update([
                                'title' => $title,
                                'description' => $description
                            ], $google_workspace->id);

                            if ($type == 'drive') {
                                unlink($temp_url);

                                echo json_encode([
                                    'success' => true,
                                    'id' => $id,
                                    'message' => _l('google_workspace_drive_uploaded_successfully', _l('google_workspace')),
                                ]);
                            } else {
                                echo json_encode([
                                    'success' => true,
                                    'id' => $id,
                                    'message' => _l('google_workspace_' . $type . '_saved_successfully', _l('google_workspace')),
                                ]);
                            }
                        } catch (Exception $e) {
                            echo json_encode([
                                'success' => false,
                                'message' => _l('google_workspace_integrate_again', _l('google_workspace')),
                                'redirect_url' => admin_url('google_workspace/settings')
                            ]);
                        }
                    }
                } else {
                    try {
                        if ($type == 'drive') {
                            $fileName = uniqid() . '_' . $_FILES['file']['name'];
                            $temp_url = TEMP_FOLDER . $fileName;

                            move_uploaded_file($_FILES['file']['tmp_name'], $temp_url);
                        }

                        // Create a new drive
                        if ($type == 'drive') {
                            if ($description) {
                                $drive = new Google_Service_Drive_DriveFile([
                                    'name' => $title,
                                    'description' => $description
                                ]);
                            } else {
                                $drive = new Google_Service_Drive_DriveFile([
                                    'name' => $title
                                ]);
                            }
                        } else {
                            $mime_type = '';
                            if ($type == 'slide') {
                                $mime_type = 'presentation';
                            } else if ($type == 'form') {
                                $mime_type = 'form';
                            }
                            if ($description) {
                                $drive = new Google_Service_Drive_DriveFile([
                                    'name' => $title,
                                    'description' => $description,
                                    'mimeType' => 'application/vnd.google-apps.' . $mime_type
                                ]);
                            } else {
                                $drive = new Google_Service_Drive_DriveFile([
                                    'name' => $title,
                                    'mimeType' => 'application/vnd.google-apps.' . $mime_type
                                ]);
                            }
                        }

                        if ($type == 'drive') {
                            $drive = $this->service->files->create($drive, [
                                'data' => file_get_contents($temp_url),
                                'mimeType' => mime_content_type($temp_url),
                                'uploadType' => 'multipart'
                            ]);
                        } else {
                            $drive = $this->service->files->create($drive);
                        }

                        // Get the drive ID
                        $driveId = $drive->id;

                        $drive_id = $this->google_workspace_model->add([
                            'staffid' => get_staff_user_id(),
                            'driveid' => $driveId,
                            'title' => $title,
                            'type' => $type,
                            'description' => $description
                        ]);

                        if ($type == 'drive') {
                            unlink($temp_url);
                        }

                        echo json_encode([
                            'success' => true,
                            'id' => $drive_id,
                            'message' => _l('google_workspace_drive_uploaded_successfully', _l('google_workspace')),
                        ]);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => _l('google_workspace_integrate_again', _l('google_workspace')),
                            'redirect_url' => admin_url('google_workspace/settings')
                        ]);
                    }
                }
            }
        }
    }

    public function view($id)
    {
        if (staff_cant('view', 'google_workspace')) {
            access_denied('google_workspace');
        }

        $google_workspace = $this->google_workspace_model->get($id);

        if ($google_workspace) {
            $data['id']                     = $id;
            $data['title']                  = _l('google_workspace') . ' - ' . $google_workspace->title;
            $data['driveid']                = $google_workspace->driveid;
            $data['type']                   = $google_workspace->type;
            $data['status']                 = $google_workspace->status;
            $this->load->view('view', $data);
        } else {
            redirect(admin_url('google_workspace/docs'));
        }
    }

    public function delete($id)
    {
        if (staff_cant('delete', 'google_workspace')) {
            access_denied('google_workspace');
        }

        $this->_create_client();
        $this->_set_service();

        $google_workspace = $this->google_workspace_model->get($id);
        $drive_type = $google_workspace->type;
        $drive = new Google_Service_Drive($this->client);
        try {
            $drive->files->delete($google_workspace->driveid);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        $this->google_workspace_model->delete($id);

        set_alert('success', _l('google_workspace_' . $drive_type . '_deleted_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/' . $drive_type . 's'));
    }

    public function update_doc($docId, $contents)
    {
        if (staff_cant('view', 'google_workspace') || staff_cant('create', 'google_workspace') || staff_cant('edit', 'google_workspace')) {
            access_denied('google_workspace');
        }

        $this->_create_client();

        try {
            // Prepare the requests to update the document content
            $requests = [
                new Google_Service_Docs_Request([
                    'insertText' => [
                        'location' => [
                            'index' => 1,
                        ],
                        'text' => $contents
                    ],
                ])
            ];

            // Execute the batch update
            $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                'requests' => $requests
            ]);
            $result = $this->service->documents->batchUpdate($docId, $batchUpdateRequest);
        } catch (Exception $e) {
            echo 'Error writing to document: ', $e->getMessage(), "\n";
            return null;
        }

        set_alert('success', _l('google_workspace_doc_updated_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/docs'));
    }

    public function update_sheet($sheetId, $range, $values)
    {
        if (staff_cant('view', 'google_workspace') || staff_cant('create', 'google_workspace') || staff_cant('edit', 'google_workspace')) {
            access_denied('google_workspace');
        }

        $this->_create_client();

        try {
           $body = new Google_Service_Sheets_ValueRange([
               'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'RAW'
            ];

            $result = $this->sheetsService->spreadsheets_values->update($sheetId, $range, $body, $params);

            return $result->getUpdatedCells();
        } catch (Exception $e) {
            echo 'Error writing to sheet: ', $e->getMessage(), "\n";
            return null;
        }

        set_alert('success', _l('google_workspace_sheet_updated_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/sheets'));
    }

    public function integrate()
    {
        if (staff_cant('setting', 'google_workspace')) {
            access_denied('google_workspace');
        }

        foreach(['client_id', 'client_secret'] as $option) {
            $$option = $this->input->post($option);
            $$option = trim($$option);
            $$option = nl2br($$option);
        }

        if (file_exists(CREDENTIALS_PATH)) {
            $credentials = json_decode(file_get_contents(CREDENTIALS_PATH), true);
        } else {
            $credentials = [
                'web' => [
                    "client_id" => "",
                    "project_id" => "themesic-linkforsa",
                    "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
                    "token_uri" => "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
                    "client_secret" => "",
                    "redirect_uris" => [admin_url('google_workspace/redirects')]
                ]
            ];
        }
        $credentials['web']['client_id'] = $client_id;
        $credentials['web']['client_secret'] = $client_secret;
        file_put_contents(CREDENTIALS_PATH, json_encode($credentials));

        update_option('google_workspace_client_id', $client_id);
        update_option('google_workspace_client_secret', $client_secret);

        $this->_create_client();
        $authUrl = $this->client->createAuthUrl();

        echo json_encode([
            'status' => 'success',
            'auth_url' => $authUrl,
        ]);
    }

    public function redirects()
    {
        $code = $this->input->get('code');

        if ($code) {
            $this->_create_client();

            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);

            // Save the token to a file.
            if (!file_exists(dirname(TOKEN_PATH))) {
                mkdir(dirname(TOKEN_PATH), 0700, true);
            }
            file_put_contents(TOKEN_PATH, json_encode($this->client->getAccessToken()));

            set_alert('success', _l('google_workspace_integration_successfully', _l('google_workspace')));
        }


        redirect(admin_url('google_workspace/settings?tab=tab_integration'));
    }

    public function settings()
    {
        if (staff_cant('setting', 'google_workspace')) {
            access_denied('google_workspace');
        }

        $data['title'] = _l('google_workspace_settings');
        $this->load->view('settings', $data);
    }

    public function fetch_docs()
    {
        $this->_create_client();
        $this->_set_service();

        $files = [];
        $pageToken = null;

        try {
            do {
                $response = $this->service->files->listFiles([
                    'q' => "mimeType = 'application/vnd.google-apps.document' and trashed=false",
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'pageToken' => $pageToken
                ]);

                foreach ($response->getFiles() as $file) {
                    $files[] = $file;
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);
        } catch (Exception $e) {
            set_alert('success', _l('google_workspace_integrate_again', _l('google_workspace')));
            redirect(admin_url('google_workspace/settings'));
        }

        $google_doc_ids = [];
        foreach ($files as $file) {
            $google_doc_id = $file->getId();
            $google_doc_status = 'Private';
            $google_doc_ids[] = $google_doc_id;
            try {
                $google_doc_permissions = $this->service->permissions->listPermissions($google_doc_id);
                foreach ($google_doc_permissions as $google_doc_permission) {
                    if ($google_doc_permission->type == 'anyone') {
                        $google_doc_status = 'Public';
                    }
                }
            } catch (Exception $e) {
            }
            $google_doc = $this->google_workspace_model->get_by_driveid($google_doc_id);
            if ($google_doc) {
                $this->google_workspace_model->update([
                    'title' => $file->getName(),
                    'status' => $google_doc_status,
                ], $google_doc->id);
            } else {
                $this->google_workspace_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $google_doc_id,
                    'title' => $file->getName(),
                    'type' => 'doc',
                    'status' => $google_doc_status,
                    'description' => ''
                ]);
            }
        }

        $google_docs = $this->google_workspace_model->get_all('doc');
        foreach ($google_docs as $google_doc) {
            if (!in_array($google_doc['driveid'], $google_doc_ids)) {
                $this->google_workspace_model->delete($google_doc['id']);
            }
        }

        set_alert('success', _l('google_workspace_docs_fetched_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/docs'));
    }

    public function fetch_sheets()
    {
        $this->_create_client();
        $this->_set_service();

        $files = [];
        $pageToken = null;

        try {
            do {
                $response = $this->service->files->listFiles([
                    'q' => "mimeType = 'application/vnd.google-apps.spreadsheet' and trashed=false",
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'pageToken' => $pageToken
                ]);

                foreach ($response->getFiles() as $file) {
                    $files[] = $file;
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);
        } catch (Exception $e) {
            set_alert('success', _l('google_workspace_integrate_again', _l('google_workspace')));
            redirect(admin_url('google_workspace/settings'));
        }

        $google_sheet_ids = [];
        foreach ($files as $file) {
            $google_sheet_id = $file->getId();
            $google_sheet_status = 'Private';
            $google_sheet_ids[] = $google_sheet_id;
            try {
                $google_sheet_permissions = $this->service->permissions->listPermissions($google_sheet_id);
                foreach ($google_sheet_permissions as $google_sheet_permission) {
                    if ($google_sheet_permission->type == 'anyone') {
                        $google_sheet_status = 'Public';
                    }
                }
            } catch (Exception $e) {
                set_alert('success', _l('google_workspace_integrate_again', _l('google_workspace')));
                redirect(admin_url('google_workspace/settings'));
            }
            $google_sheet = $this->google_workspace_model->get_by_driveid($google_sheet_id);
            if ($google_sheet) {
                $this->google_workspace_model->update([
                    'title' => $file->getName(),
                    'status' => $google_sheet_status,
                ], $google_sheet->id);
            } else {
                $this->google_workspace_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $google_sheet_id,
                    'title' => $file->getName(),
                    'type' => 'sheet',
                    'status' => $google_sheet_status,
                    'description' => ''
                ]);
            }
        }

        $google_sheets = $this->google_workspace_model->get_all('sheet');
        foreach ($google_sheets as $google_sheet) {
            if (!in_array($google_sheet['driveid'], $google_sheet_ids)) {
                $this->google_workspace_model->delete($google_sheet['id']);
            }
        }

        set_alert('success', _l('google_workspace_sheets_fetched_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/sheets'));
    }

    public function fetch_slides()
    {
        $this->_create_client();
        $this->_set_service();

        $files = [];
        $pageToken = null;

        try {
            do {
                $response = $this->service->files->listFiles([
                    'q' => "mimeType = 'application/vnd.google-apps.presentation' and trashed=false",
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'pageToken' => $pageToken
                ]);

                foreach ($response->getFiles() as $file) {
                    $files[] = $file;
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);
        } catch (Exception $e) {
            set_alert('success', _l('google_workspace_integrate_again', _l('google_workspace')));
            redirect(admin_url('google_workspace/settings'));
        }

        $google_slide_ids = [];
        foreach ($files as $file) {
            $google_slide_id = $file->getId();
            $google_slide_status = 'Private';
            $google_slide_ids[] = $google_slide_id;
            try {
                $google_slide_permissions = $this->service->permissions->listPermissions($google_slide_id);
                foreach ($google_slide_permissions as $google_slide_permission) {
                    if ($google_slide_permission->type == 'anyone') {
                        $google_slide_status = 'Public';
                    }
                }
            } catch (Exception $e) {
            }
            $google_slide = $this->google_workspace_model->get_by_driveid($google_slide_id);
            if ($google_slide) {
                $this->google_workspace_model->update([
                    'title' => $file->getName(),
                    'status' => $google_slide_status,
                ], $google_slide->id);
            } else {
                $this->google_workspace_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $google_slide_id,
                    'title' => $file->getName(),
                    'type' => 'slide',
                    'status' => $google_slide_status,
                    'description' => ''
                ]);
            }
        }

        $google_slides = $this->google_workspace_model->get_all('slide');
        foreach ($google_slides as $google_slide) {
            if (!in_array($google_slide['driveid'], $google_slide_ids)) {
                $this->google_workspace_model->delete($google_slide['id']);
            }
        }

        set_alert('success', _l('google_workspace_slides_fetched_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/slides'));
    }

    public function fetch_forms()
    {
        $this->_create_client();
        $this->_set_service();

        $files = [];
        $pageToken = null;

        try {
            do {
                $response = $this->service->files->listFiles([
                    'q' => "mimeType = 'application/vnd.google-apps.form' and trashed=false",
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'pageToken' => $pageToken
                ]);

                foreach ($response->getFiles() as $file) {
                    $files[] = $file;
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);
        } catch (Exception $e) {
            set_alert('success', _l('google_workspace_integrate_again', _l('google_workspace')));
            redirect(admin_url('google_workspace/settings'));
        }

        $google_form_ids = [];
        foreach ($files as $file) {
            $google_form_id = $file->getId();
            $google_form_status = 'Private';
            $google_form_ids[] = $google_form_id;
            try {
                $google_form_permissions = $this->service->permissions->listPermissions($google_form_id);
                foreach ($google_form_permissions as $google_form_permission) {
                    if ($google_form_permission->type == 'anyone') {
                        $google_form_status = 'Public';
                    }
                }
            } catch (Exception $e) {
            }
            $google_form = $this->google_workspace_model->get_by_driveid($google_form_id);
            if ($google_form) {
                $this->google_workspace_model->update([
                    'title' => $file->getName(),
                    'status' => $google_form_status,
                ], $google_form->id);
            } else {
                $this->google_workspace_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $google_form_id,
                    'title' => $file->getName(),
                    'type' => 'form',
                    'status' => $google_form_status,
                    'description' => ''
                ]);
            }
        }

        $google_forms = $this->google_workspace_model->get_all('form');
        foreach ($google_forms as $google_form) {
            if (!in_array($google_form['driveid'], $google_form_ids)) {
                $this->google_workspace_model->delete($google_form['id']);
            }
        }

        set_alert('success', _l('google_workspace_forms_fetched_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/forms'));
    }

    public function fetch_drives()
    {
        $this->_create_client();
        $this->_set_service();

        $files = [];
        $pageToken = null;

        try {
            do {
                $response = $this->service->files->listFiles([
                    'q' => "mimeType != 'application/vnd.google-apps.document' and mimeType != 'application/vnd.google-apps.spreadsheet' and mimeType != 'application/vnd.google-apps.presentation' and mimeType != 'application/vnd.google-apps.form' and trashed=false",
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'pageToken' => $pageToken
                ]);

                foreach ($response->getFiles() as $file) {
                    $files[] = $file;
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);
        } catch (Exception $e) {
            set_alert('success', _l('google_workspace_integrate_again', _l('google_workspace')));
            redirect(admin_url('google_workspace/settings'));
        }

        $google_drive_ids = [];
        foreach ($files as $file) {
            $google_drive_id = $file->getId();
            $google_drive_status = 'Private';
            $google_drive_ids[] = $google_drive_id;
            try {
                $google_drive_permissions = $this->service->permissions->listPermissions($google_drive_id);
                foreach ($google_drive_permissions as $google_drive_permission) {
                    if ($google_drive_permission->type == 'anyone') {
                        $google_drive_status = 'Public';
                    }
                }
            } catch (Exception $e) {
            }
            $google_drive = $this->google_workspace_model->get_by_driveid($google_drive_id);
            if ($google_drive) {
                $this->google_workspace_model->update([
                    'title' => $file->getName(),
                    'status' => $google_drive_status,
                ], $google_drive->id);
            } else {
                $this->google_workspace_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $google_drive_id,
                    'title' => $file->getName(),
                    'type' => 'drive',
                    'status' => $google_drive_status,
                    'description' => ''
                ]);
            }
        }

        $google_workspaces = $this->google_workspace_model->get_all('drive');
        foreach ($google_workspaces as $google_workspace) {
            if (!in_array($google_workspace['driveid'], $google_drive_ids)) {
                $this->google_workspace_model->delete($google_workspace['id']);
            }
        }

        set_alert('success', _l('google_workspace_drive_fetched_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/drives'));
    }

    public function reset_settings()
    {
        if (staff_cant('setting', 'google_workspace')) {
            access_denied('google_workspace');
        }

        update_option('google_workspace_client_id', '');
        update_option('google_workspace_client_secret', '');

        unlink(CREDENTIALS_PATH);
        unlink(TOKEN_PATH);

        set_alert('success', _l('google_workspace_reseted_successfully', _l('google_workspace')));

        redirect(admin_url('google_workspace/settings'));
    }

    public function save_settings()
    {
        if (staff_cant('setting', 'google_workspace')) {
            access_denied('google_workspace');
        }

        hooks()->do_action('before_save_google_doc');

        foreach(['can_access', 'can_manage'] as $option) {
            // Also created the variables
            $$option = $this->input->post($option);
            $$option = trim($$option);
            $$option = nl2br($$option);
        }
    }
}
