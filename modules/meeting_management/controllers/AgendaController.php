<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;

class AgendaController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Load language file and models
        $this->lang->load('meeting_management', 'english');
        $this->load->model('Meeting_model');
        $this->load->model('Projects_model'); // Built-in Perfex CRM Projects model
        $this->load->model('Clients_model'); // Built-in Perfex CRM Clients model

    }

    // View the list of all agendas
    // View the list of all agendas
    public function index()
    {
        // 1. Get project filter from GET parameters
        $project_id = $this->input->get('project_filter');

        // 2. Pass the project filter to model if set
        if (!empty($project_id)) {
            $data['agendas'] = $this->Meeting_model->get_all_minutes($project_id);
        } else {
            $data['agendas'] = $this->Meeting_model->get_all_minutes();
        }
        $data['projects'] = $this->projects_model->get_items();
        $data['title'] = _l('meeting_agenda');
        $this->load->view('meeting_management/agendas_list', $data);
    }

    public function filter_minutes()
    {
        $project_id = $this->input->get('project_filter');

        $this->db->select('tblmeeting_management.*, tblprojects.name as project_name');
        $this->db->from('tblmeeting_management');
        $this->db->join('tblprojects', 'tblprojects.id = tblmeeting_management.project_id', 'left');

        if (!empty($project_id)) {
            $this->db->where('tblmeeting_management.project_id', $project_id);
        }

        $query = $this->db->get();
        $agendas = $query->result_array();

        // Convert date format for consistent display
        foreach ($agendas as &$agenda) {
            if (isset($agenda['meeting_date'])) {
                $agenda['meeting_date'] = date('Y-m-d H:i:s', strtotime($agenda['meeting_date']));
            }
        }

        header('Content-Type: application/json');
        echo json_encode($agendas);
        exit;
    }

    // Create or edit an agenda
    public function create($id = '')
    {
        $data['clients'] = $this->Clients_model->get();

        if ($this->input->post()) {

            $agenda_data = [
                'meeting_title' => $this->input->post('meeting_title'),
                'project_id' => $this->input->post('project_id'),
                'agenda' => $this->input->post('agenda', false),
                'meeting_date' => $this->input->post('meeting_date'),
                'created_by' => get_staff_user_id(),
            ];
            $agenda_data_new = $this->input->post();
            $agenda_data_new['agenda'] = $this->input->post('agenda', false);
            $agenda_data_new['additional_note'] = $this->input->post('additional_note', false);
            $agenda_data_new['created_by'] = get_staff_user_id();

            if ($id == '') {
                // Insert new agenda
                $this->Meeting_model->create_agenda($agenda_data_new);
                set_alert('success', _l('meeting_agenda_created_success'));
            } else {
                // Update existing agenda
                $this->Meeting_model->update_agenda($id, $agenda_data_new);
                set_alert('success', _l('meeting_agenda_updated_success'));
            }

            redirect(admin_url('meeting_management/agendaController/index'));
        }
        $mom_row_template = $this->Meeting_model->create_mom_row_template();
        if ($id == '') {
            $is_edit = false;
        } else {
            $get_mom_detials = $this->Meeting_model->get_mom_detials($id);

            if (count($get_mom_detials) > 0) {
                $index_order = 0;
                foreach ($get_mom_detials as $mom_detail) {
                    $index_order++;
                    $mom_row_template .= $this->Meeting_model->create_mom_row_template('items[' . $index_order . ']', $mom_detail['area'], $mom_detail['description'], $mom_detail['decision'], $mom_detail['action'], $mom_detail['staff'], $mom_detail['vendor'], $mom_detail['target_date'], $mom_detail, $mom_detail['id']);
                }
            }
            $is_edit = true;
        }
        $data['is_edit'] = $is_edit;
        $data['mom_row_template'] = $mom_row_template;
        $data['agenda'] = $this->Meeting_model->get_agenda($id);
        $data['title'] = _l('meeting_create_agenda');
        $this->load->model('staff_model');
        $data['staff_list'] = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('meeting_management/agenda_form', $data);
    }

    public function get_mom_row_template()
    {
        $name = $this->input->post('name');
        $area = $this->input->post('area');
        $description = $this->input->post('description');
        $decision = $this->input->post('decision');
        $action = $this->input->post('action');
        $staff = $this->input->post('staff');
        $vendor = $this->input->post('vendor');
        $target_date = $this->input->post('target_date');
        $attachments = $this->input->post('attachments');
        $item_key = $this->input->post('item_key');
        $critical = $this->input->post('critical');
        echo $this->Meeting_model->create_mom_row_template($name, $area, $description, $decision, $action, $staff, $vendor, $target_date, $attachments, $item_key, '','',$critical);
    }
    // Delete an agenda
    public function delete($id)
    {
        $this->Meeting_model->delete_agenda($id);
        set_alert('success', _l('meeting_agenda_deleted_success'));
        redirect(admin_url('meeting_management/agendaController/index'));
    }

    // Handle the Ajax request to fetch projects based on client ID
    public function get_projects_by_client($client_id)
    {
        if ($this->input->is_ajax_request()) {
            $projects = $this->Projects_model->get('', ['clientid' => $client_id]);
            echo json_encode($projects);
            exit;
        }
    }
    public function view_meeting($agenda_id)
    {
        // Fetch meeting details
        $data['meeting'] = $this->Meeting_model->get_meeting_details($agenda_id);
        $data['agenda_id'] = $agenda_id;
        // Fetch participants using the detailed participant function
        $data['participants'] = $this->Meeting_model->get_detailed_participants($agenda_id);
        $data['meeting_notes'] = $this->Meeting_model->get_meeting_notes($agenda_id);  // Assuming the method fetches notes


        // Fetch tasks
        $data['tasks'] = $this->Meeting_model->get_tasks_by_agenda($agenda_id);
        $data['title'] = _l('view_meeting');
        // Load the view
        $data['attachments'] = $this->Meeting_model->get_meeting_attachments('agenda_meeting', $agenda_id);
        $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);
        $data['minutes_data'] = $this->Meeting_model->get_minutes_detials($agenda_id);
        $data['agenda_data'] = $this->Meeting_model->get_mom_detials($agenda_id);
        $this->load->view('meeting_management/view_meeting', $data);
    }

    public function export_to_pdf($agenda_id)
    {
        // Initialize Dompdf
        $pdf = new Dompdf();

        // Fetch meeting details and other data
        $meeting_details = $this->Meeting_model->get_meeting_details($agenda_id);
        $participants = $this->Meeting_model->get_detailed_participants($agenda_id);
        $tasks = $this->Meeting_model->get_tasks_by_agenda($agenda_id);
        $attachments = $this->Meeting_model->get_meeting_attachments('agenda_meeting', $agenda_id);
        // Fetch the meeting notes
        $meeting_notes = $this->Meeting_model->get_meeting_notes($agenda_id);
        $get_minutes_detials = $this->Meeting_model->get_minutes_detials($agenda_id);
        $check_image = $this->Meeting_model->check_image($agenda_id);
        $check_desc = $this->Meeting_model->check_desc($agenda_id);
        $check_decision = $this->Meeting_model->check_decision($agenda_id);
        $check_action = $this->Meeting_model->check_action($agenda_id);
        $check_action_by = $this->Meeting_model->check_action_by($agenda_id);
        $check_target_date = $this->Meeting_model->check_target_date($agenda_id);
        
        // Load your HTML view for the PDF content
        $data = [
            'meeting' => $meeting_details, 
            'participants' => $participants,
            'tasks' => $tasks,
            'meeting_notes' => $meeting_notes,
            'minutes_data' => $get_minutes_detials,
            'check_attachment' => $check_image,
            'check_desc' => $check_desc,
            'check_decision' => $check_decision, 
            'attachments' => $attachments,
            'check_action' => $check_action,
            'check_action_by' => $check_action_by,
            'check_target_date' => $check_target_date,
        ];
        $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);
        $html_content = $this->load->view('meeting_management/pdf_template', $data, true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->set_option('isHtml5ParserEnabled', true);
        // Set the PDF content
        $pdf->loadHtml($html_content);

        $pdf->setPaper('A4', 'portrait');

        // Render the PDF
        $pdf->render();

        // Output the PDF to the browser
        $pdf->stream("Meeting_Agenda_{$agenda_id}.pdf", array("Attachment" => true));  // Download the PDF
    }

    public function update_mom_list()
    {
        $data = $this->input->post();
        $this->Meeting_model->update_mom_list($data);
        echo json_encode(['success' => true]);
        die();
    }

    public function import_file_xlsx_mom_items()
    {
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('area/head') => 'string',
                            _l('description') => 'string',
                            _l('decision')    => 'string',
                            _l('action')    => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;

                        $total_rows_actualy = 0;
                        $list_item = $this->Meeting_model->create_mom_row_template();
                        //get data for compare
                        $index_quote = 1;

                        for ($row = 1; $row < count($data); $row++) {
                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;
                            $flag_mail = 0;
                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_item_description;

                            // $value_cell_commodity_code = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_area = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_description = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_decision = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_cell_action = isset($data[$row][3]) ? $data[$row][3] : '';
                            /*check null*/
                            if (is_null($value_cell_area) == true) {
                                $string_error .= _l('area/head') . _l('not_yet_entered');
                                $flag = 1;
                            }
                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_area,
                                    $value_cell_description,
                                    $value_cell_decision,
                                    $value_cell_action,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }
                            if (($flag == 0) && ($flag2 == 0)) {

                                $rows[] = $row;
                                $list_item .= $this->Meeting_model->create_mom_row_template('newitems[' . $index_quote . ']', $value_cell_area, $value_cell_description, $value_cell_decision, $value_cell_action, '', '', '', [], $index_quote);

                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Import Item successfully';
                            }
                        }
                        // die('sadad');
                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        // $total_row_false = '';
                        if (($total_rows_data_error > 0)) {

                            $filename = 'MEETING_MANAGEMENT_MOM_ERROR' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, MEETING_MANAGEMENT_MOM_ERROR . $filename, $filename));

                            $filename = MEETING_MANAGEMENT_MOM_ERROR . $filename;
                        }
                        $list_item = $list_item;

                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }
}
