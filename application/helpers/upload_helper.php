<?php

defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Handle Estimate request attachments if any
 * @param  mixed $estimateRequestId
 * @return boolean
 */
function handle_estimate_request_attachments($estimateRequestId, $index_name = 'file')
{
    $hookData = hooks()->apply_filters('before_handle_estimate_request_attachment', [
        'estimate_request_id' => $estimateRequestId,
        'index_name' => $index_name,
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    $totalUploaded = 0;
    if (
        (isset($_FILES[$index_name]['name']) && !empty($_FILES[$index_name]['name'])) ||
        (isset($_FILES[$index_name]) && is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name'][$i])) {
                continue;
            }

            if (isset($_FILES[$index_name][$i]) && _perfex_upload_error($_FILES[$index_name]['error'][$i])) {
                header('HTTP/1.0 400 Bad error');
                echo _perfex_upload_error($_FILES[$index_name]['error'][$i]);
                die;
            }

            $CI = &get_instance();
            if (isset($_FILES[$index_name]['name'][$i]) && $_FILES[$index_name]['name'][$i] != '') {
                hooks()->do_action('before_upload_estimate_request_attachment', $estimateRequestId);
                $path = get_upload_path_by_type('estimate_request') . $estimateRequestId . '/';
                // Get the temp file path
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
                // Make sure we have a filepath

                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    if (!_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
                        continue;
                    }

                    _maybe_create_upload_path($path);

                    $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                    $newFilePath = $path . $filename;
                    // Upload the file into the company uploads dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $CI = &get_instance();
                        $CI->load->model('estimate_request_model');
                        $data   = [];
                        $data[] = [
                            'file_name' => $filename,
                            'filetype'  => $_FILES[$index_name]['type'][$i],
                        ];
                        $CI->estimate_request_model->add_attachment_to_database($estimateRequestId, $data, false);
                        $totalUploaded++;
                    }
                }
            }
        }
    }

    if ($totalUploaded > 0) {
        return true;
    }

    return false;
}

/**
 * Handles uploads error with translation texts
 * @param  mixed $error type of error
 * @return mixed
 */
function _perfex_upload_error($error)
{
    $uploadErrors = [
        0 => _l('file_uploaded_success'),
        1 => _l('file_exceeds_max_filesize'),
        2 => _l('file_exceeds_maxfile_size_in_form'),
        3 => _l('file_uploaded_partially'),
        4 => _l('file_not_uploaded'),
        6 => _l('file_missing_temporary_folder'),
        7 => _l('file_failed_to_write_to_disk'),
        8 => _l('file_php_extension_blocked'),
    ];

    if (isset($uploadErrors[$error]) && $error != 0) {
        return $uploadErrors[$error];
    }

    return false;
}
/**
 * Newsfeed post attachments
 * @param  mixed $postid Post ID to add attachments
 * @return void  - Result values
 */
function handle_newsfeed_post_attachments($postid)
{
    $hookData = hooks()->apply_filters('before_handle_newsfeed_post_attachments', [
        'newsfeed_post_id' => $postid,
        'index_name' => 'file',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        echo json_encode([
            'success' => $hookData['handled_externally_successfully'],
            'postid'  => $postid,
        ]);
        return;
    }

    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = get_upload_path_by_type('newsfeed') . $postid . '/';
    $CI   = &get_instance();
    if (isset($_FILES['file']['name'])) {
        hooks()->do_action('before_upload_newsfeed_attachment', $postid);
        $uploaded_files = false;
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES['file']['name']);
            // In case client side validation is bypassed
            if (_upload_extension_allowed($filename)) {
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $file_uploaded = true;
                    $attachment    = [];
                    $attachment[]  = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'],
                    ];
                    $CI->misc_model->add_attachment_to_database($postid, 'newsfeed_post', $attachment);
                }
            }
        }
        if ($file_uploaded == true) {
            echo json_encode([
                'success' => true,
                'postid'  => $postid,
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'postid'  => $postid,
            ]);
        }
    }
}
/**
 * Handles upload for project files
 * @param  mixed $project_id project id
 * @return boolean
 */
function handle_project_file_uploads($project_id)
{
    $hookData = hooks()->apply_filters('before_handle_project_file_uploads', [
        'project_id' => $project_id,
        'index_name' => 'file',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    $filesIDS = [];
    $errors   = [];

    if (
        isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
    ) {
        hooks()->do_action('before_upload_project_attachment', $project_id);

        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name']     = [$_FILES['file']['name']];
            $_FILES['file']['type']     = [$_FILES['file']['type']];
            $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
            $_FILES['file']['error']    = [$_FILES['file']['error']];
            $_FILES['file']['size']     = [$_FILES['file']['size']];
        }

        $path = get_upload_path_by_type('project') . $project_id . '/';

        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if (_perfex_upload_error($_FILES['file']['error'][$i])) {
                $errors[$_FILES['file']['name'][$i]] = _perfex_upload_error($_FILES['file']['error'][$i]);

                continue;
            }

            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $originalFilename = unique_filename($path, $_FILES['file']['name'][$i]);
                $filename = app_generate_hash() . '.' . get_file_extension($originalFilename);

                // In case client side validation is bypassed
                if (!_upload_extension_allowed($filename)) {
                    continue;
                }

                $newFilePath = $path . $filename;
                // Upload the file into the company uploads dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $CI = &get_instance();
                    if (is_client_logged_in()) {
                        $contact_id = get_contact_user_id();
                        $staffid    = 0;
                    } else {
                        $staffid    = get_staff_user_id();
                        $contact_id = 0;
                    }
                    $data = [
                        'project_id' => $project_id,
                        'file_name'  => $filename,
                        'original_file_name'  => $originalFilename,
                        'filetype'   => $_FILES['file']['type'][$i],
                        'dateadded'  => date('Y-m-d H:i:s'),
                        'staffid'    => $staffid,
                        'contact_id' => $contact_id,
                        'subject'    => $originalFilename,
                    ];
                    if (is_client_logged_in()) {
                        $data['visible_to_customer'] = 1;
                    } else {
                        $data['visible_to_customer'] = ($CI->input->post('visible_to_customer') == 'true' ? 1 : 0);
                    }
                    $CI->db->insert(db_prefix() . 'project_files', $data);

                    $insert_id = $CI->db->insert_id();
                    if ($insert_id) {
                        if (is_image($newFilePath)) {
                            create_img_thumb($path, $filename);
                        }
                        array_push($filesIDS, $insert_id);
                    } else {
                        unlink($newFilePath);

                        return false;
                    }
                }
            }
        }
    }

    if (count($filesIDS) > 0) {
        $CI->load->model('projects_model');
        end($filesIDS);
        $lastFileID = key($filesIDS);
        $CI->projects_model->new_project_file_notification($filesIDS[$lastFileID], $project_id);
    }

    if (count($errors) > 0) {
        $message = '';
        foreach ($errors as $filename => $error_message) {
            $message .= $filename . ' - ' . $error_message . '<br />';
        }
        header('HTTP/1.0 400 Bad error');
        echo $message;
        die;
    }

    if (count($filesIDS) > 0) {
        return true;
    }

    return false;
}
/**
 * Handle contract attachments if any
 * @param  mixed $contractid
 * @return boolean
 */
function handle_contract_attachment($id)
{
    $hookData = hooks()->apply_filters('before_handle_contract_attachment', [
        'contract_id' => $id,
        'index_name' => 'file',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = get_upload_path_by_type('contract') . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = &get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];
                $CI->misc_model->add_attachment_to_database($id, 'contract', $attachment);

                return true;
            }
        }
    }

    return false;
}
/**
 * Handle lead attachments if any
 * @param  mixed $leadid
 * @return boolean
 */
function handle_lead_attachments($leadid, $index_name = 'file', $form_activity = false)
{
    $hookData = hooks()->apply_filters('before_handle_lead_attachment', [
        'lead_id' => $leadid,
        'index_name' => $index_name,
        'handled_externally' => false, // e.g. module upload to s3
        'form_activity' => $form_activity,
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    $path           = get_upload_path_by_type('lead') . $leadid . '/';
    $CI             = &get_instance();
    $CI->load->model('leads_model');

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != ''
            || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);

        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);

                $newFilePath = $path . $filename;

                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $CI->leads_model->add_attachment_to_database($leadid, [[
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]], false, $form_activity);
                }
            }
        }
    }

    return true;
}

/**
 * Task attachments upload array
 * Multiple task attachments can be upload if input type is array or dropzone plugin is used
 * @param  mixed $taskid     task id
 * @param  string $index_name attachments index, in different forms different index name is used
 * @return array|false
 */
function handle_task_attachments_array($taskid, $index_name = 'attachments')
{
    $hookData = hooks()->apply_filters('before_handle_task_attachments_array', [
        'task_id' => $taskid,
        'index_name' => $index_name,
        'uploaded_files' => [],
        'handled_externally' => false, // e.g. module upload to s3
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return count($hookData['uploaded_files']) > 0 ? $hookData['uploaded_files'] : false;
    }

    $uploaded_files = [];
    $path           = get_upload_path_by_type('task') . $taskid . '/';

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Purchase attachments upload array
 * Multiple purchase attachments can be upload if input type is array or dropzone plugin is used
 * @param  mixed $related related
 * @param  mixed $id id
 * @param  string $index_name attachments index, in different forms different index name is used
 * @return mixed
 */
function handle_purchase_attachments_array($related, $id, $index_name = 'attachments')
{
    if (!is_dir(get_upload_path_by_type('purchase'))) {
        mkdir(get_upload_path_by_type('purchase'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related)) {
        mkdir(get_upload_path_by_type('purchase') . $related, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('purchase') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Purchase attachment upload array
 * Multiple purchase item attachment can be upload if input type is array or dropzone plugin is used
 * @param  mixed $related related
 * @param  mixed $id id
 * @param  string $index_name attachments index, in different forms different index name is used
 * @return mixed
 */
function handle_purchase_item_attachment_array($related, $id, $item_path, $index_name, $key)
{
    if (!is_dir(get_upload_path_by_type('purchase'))) {
        mkdir(get_upload_path_by_type('purchase'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related)) {
        mkdir(get_upload_path_by_type('purchase') . $related, 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related . '/' . $id)) {
        mkdir(get_upload_path_by_type('purchase') . $related . '/' . $id, 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related . '/' . $id . '/' . $item_path)) {
        mkdir(get_upload_path_by_type('purchase') . $related . '/' . $id . '/' . $item_path, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('purchase') . $related . '/' . $id . '/' . $item_path . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {

        // _file_attachments_index_fix($index_name);
        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'][$key]['image'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            if (
                _perfex_upload_error($_FILES[$index_name]['error'][$key]['image'])
                || !_upload_extension_allowed($_FILES[$index_name]['name'][$key]['image'])
            ) {
                return false;
            }

            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES[$index_name]['name'][$key]['image']);
            $newFilePath = $path . $filename;

            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                array_push($uploaded_files, [
                    'item_id' => $item_path,
                    'file_name' => $filename,
                    'filetype'  => $_FILES[$index_name]['type'][$key]['image'],
                ]);

                if (is_image($newFilePath)) {
                    // create_img_thumb($path, $filename);
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    } else {
        $other_attachments = list_files(get_upload_path_by_type('purchase') . $related . '/' . $id . '/' . $item_path);
        if (count($other_attachments) == 0) {
            delete_dir(get_upload_path_by_type('purchase') . $related . '/' . $id . '/' . $item_path);
        }
    }

    return false;
}
/**
 * Handles the upload of an attachment.
 *
 * @param string $related    The type of attachment folder (for example, 'mom_attachments' or 'minutes_attachments').
 * @param mixed  $id         The parent record ID (either $agenda_id or $minute_id).
 * @param mixed  $item_path  The sub-folder record ID (agenda_detail_id or minutes_detail_id).
 * @param string $index_name The $_FILES index name.
 * @param int    $key        The index to use within the multi-dimensional $_FILES array.
 *
 * @return array|false       Returns array with file info on success, false if no upload occurred.
 */
function handle_mom_item_attachment_array($related, $id, $item_path, $index_name, $key)
{
    // Base directory for meeting management uploads.
    $base_path = get_upload_path_by_type('meeting_management');
    if (!is_dir($base_path)) {
        mkdir($base_path, 0755, true);
    }

    // Create the directory structure:
    // - Related folder (e.g. 'mom_attachments' or 'minutes_attachments')
    // - Parent ID folder (e.g. agenda_id or minute_id)
    // - Detail ID folder (e.g. agenda_detail_id or minutes_detail_id)
    $related_path = $base_path . $related . '/';
    if (!is_dir($related_path)) {
        mkdir($related_path, 0755, true);
    }

    $id_path = $related_path . $id . '/';
    if (!is_dir($id_path)) {
        mkdir($id_path, 0755, true);
    }

    $item_dir = $id_path . $item_path . '/';
    if (!is_dir($item_dir)) {
        mkdir($item_dir, 0755, true);
    }

    $uploaded_files = [];
    $path = $item_dir;
    $CI = &get_instance();

    if (
        isset($_FILES[$index_name]['name']) &&
        (
            $_FILES[$index_name]['name'] != '' ||
            (is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
        )
    ) {
        // Get the temporary file path from the passed key.
        $tmpFilePath = $_FILES[$index_name]['tmp_name'][$key]['attachments'];

        if (!empty($tmpFilePath)) {
            // Check for any upload errors or invalid extensions.
            if (
                _perfex_upload_error($_FILES[$index_name]['error'][$key]['attachments']) ||
                !_upload_extension_allowed($_FILES[$index_name]['name'][$key]['attachments'])
            ) {
                return false;
            }

            // Ensure the upload path exists (a helper may already do this).
            _maybe_create_upload_path($path);
            // Generate a unique filename.
            $filename    = unique_filename($path, $_FILES[$index_name]['name'][$key]['attachments']);
            $newFilePath = $path . $filename;

            // Move the temporary file to the new location.
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $uploaded_files[] = [
                    'item_id'   => $item_path,  // To be used later for database updates.
                    'file_name' => $filename,
                    'filetype'  => $_FILES[$index_name]['type'][$key]['attachments'],
                    'file_path' => $newFilePath, // Full path, which is useful for copying.
                ];

                // Optionally create a thumbnail if the file is an image.
                if (is_image($newFilePath)) {
                    // create_img_thumb($path, $filename);
                }
            }
        }
    }

    // If at least one file is uploaded, return its details.
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    } else {
        // No files uploaded. If the folder is empty, remove the directory.
        $other_attachments = list_files($item_dir);
        if (count($other_attachments) == 0) {
            delete_dir($item_dir);
        }
    }

    return false;
}
function handle_qcr_item_attachment_array($related, $id, $item_path, $index_name, $key)
{
    // Base directory for meeting management uploads.
    $base_path = get_upload_path_by_type('form');
    if (!is_dir($base_path)) {
        mkdir($base_path, 0755, true);
    }

    // Create the directory structure:
    // - Related folder (e.g. 'mom_attachments' or 'minutes_attachments')
    // - Parent ID folder (e.g. agenda_id or minute_id)
    // - Detail ID folder (e.g. agenda_detail_id or minutes_detail_id)
    $related_path = $base_path . $related . '/';
    if (!is_dir($related_path)) {
        mkdir($related_path, 0755, true);
    }

    $id_path = $related_path . $id . '/';
    if (!is_dir($id_path)) {
        mkdir($id_path, 0755, true);
    }

    $item_dir = $id_path . $item_path . '/';
    if (!is_dir($item_dir)) {
        mkdir($item_dir, 0755, true);
    }

    $uploaded_files = [];
    $path = $item_dir;
    $CI = &get_instance();

    if (
        isset($_FILES[$index_name]['name']) &&
        (
            $_FILES[$index_name]['name'] != '' ||
            (is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
        )
    ) {
        // Get the temporary file path from the passed key.
        $tmpFilePath = $_FILES[$index_name]['tmp_name'][$key]['photograph'];

        if (!empty($tmpFilePath)) {
            // Check for any upload errors or invalid extensions.
            if (
                _perfex_upload_error($_FILES[$index_name]['error'][$key]['photograph']) ||
                !_upload_extension_allowed($_FILES[$index_name]['name'][$key]['photograph'])
            ) {
                return false;
            }

            // Ensure the upload path exists (a helper may already do this).
            _maybe_create_upload_path($path);
            // Generate a unique filename.
            $filename    = unique_filename($path, $_FILES[$index_name]['name'][$key]['photograph']);
            $newFilePath = $path . $filename;

            // Move the temporary file to the new location.
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $uploaded_files[] = [
                    'item_id'   => $item_path,  // To be used later for database updates.
                    'file_name' => $filename,
                    'filetype'  => $_FILES[$index_name]['type'][$key]['photograph'],
                    'file_path' => $newFilePath, // Full path, which is useful for copying.
                ];

                // Optionally create a thumbnail if the file is an image.
                if (is_image($newFilePath)) {
                    // create_img_thumb($path, $filename);
                }
            }
        }
    }

    // If at least one file is uploaded, return its details.
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    } else {
        // No files uploaded. If the folder is empty, remove the directory.
        $other_attachments = list_files($item_dir);
        if (count($other_attachments) == 0) {
            delete_dir($item_dir);
        }
    }

    return false;
}

function handle_qcr_second_item_attachment_array($related, $id, $item_path, $index_name, $key)
{
    // Base directory for meeting management uploads.
    $base_path = get_upload_path_by_type('form');
    if (!is_dir($base_path)) {
        mkdir($base_path, 0755, true);
    }

    // Create the directory structure:
    // - Related folder (e.g. 'mom_attachments' or 'minutes_attachments')
    // - Parent ID folder (e.g. agenda_id or minute_id)
    // - Detail ID folder (e.g. agenda_detail_id or minutes_detail_id)
    $related_path = $base_path . $related . '/';
    if (!is_dir($related_path)) {
        mkdir($related_path, 0755, true);
    }

    $id_path = $related_path . $id . '/';
    if (!is_dir($id_path)) {
        mkdir($id_path, 0755, true);
    }

    $item_dir = $id_path . $item_path . '/';
    if (!is_dir($item_dir)) {
        mkdir($item_dir, 0755, true);
    }

    $uploaded_files = [];
    $path = $item_dir;
    $CI = &get_instance();

    if (
        isset($_FILES[$index_name]['name']) &&
        (
            $_FILES[$index_name]['name'] != '' ||
            (is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
        )
    ) {
        // Get the temporary file path from the passed key.
        $tmpFilePath = $_FILES[$index_name]['tmp_name'][$key]['compliance_photograph'];

        if (!empty($tmpFilePath)) {
            // Check for any upload errors or invalid extensions.
            if (
                _perfex_upload_error($_FILES[$index_name]['error'][$key]['compliance_photograph']) ||
                !_upload_extension_allowed($_FILES[$index_name]['name'][$key]['compliance_photograph'])
            ) {
                return false;
            }

            // Ensure the upload path exists (a helper may already do this).
            _maybe_create_upload_path($path);
            // Generate a unique filename.
            $filename    = unique_filename($path, $_FILES[$index_name]['name'][$key]['compliance_photograph']);
            $newFilePath = $path . $filename;

            // Move the temporary file to the new location.
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $uploaded_files[] = [
                    'item_id'   => $item_path,  // To be used later for database updates.
                    'file_name' => $filename,
                    'filetype'  => $_FILES[$index_name]['type'][$key]['compliance_photograph'],
                    'file_path' => $newFilePath, // Full path, which is useful for copying.
                ];

                // Optionally create a thumbnail if the file is an image.
                if (is_image($newFilePath)) {
                    // create_img_thumb($path, $filename);
                }
            }
        }
    }

    // If at least one file is uploaded, return its details.
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    } else {
        // No files uploaded. If the folder is empty, remove the directory.
        $other_attachments = list_files($item_dir);
        if (count($other_attachments) == 0) {
            delete_dir($item_dir);
        }
    }

    return false;
}
/**
 * Change order attachments upload array
 * Multiple changee attachments can be upload if input type is array or dropzone plugin is used
 * @param  mixed $related related
 * @param  mixed $id id
 * @param  string $index_name attachments index, in different forms different index name is used
 * @return mixed
 */
function handle_changee_attachments_array($related, $id, $index_name = 'attachments')
{
    // Ensure the upload directory for "changee" exists
    if (!is_dir(get_upload_path_by_type('changee'))) {
        mkdir(get_upload_path_by_type('changee'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('changee') . $related)) {
        mkdir(get_upload_path_by_type('changee') . $related, 0755);
    }

    $uploaded_files = [];
    $path           = get_upload_path_by_type('changee') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    // Check if files are uploaded and process them
    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        // Ensure $_FILES[$index_name] is treated as an array
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);

        // Process each uploaded file
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Skip if the file path is empty
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                // Ensure the upload path exists
                _maybe_create_upload_path($path);

                // Generate a unique filename and move the file
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    // Handle image thumbnails (optional)
                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    // Return the list of uploaded files, or false if none were uploaded
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

function handle_invetory_attachments_array($related, $id, $index_name = 'attachments')
{
    if (!is_dir(get_upload_path_by_type('inventory'))) {
        mkdir(get_upload_path_by_type('inventory'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('inventory') . $related)) {
        mkdir(get_upload_path_by_type('inventory') . $related, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('inventory') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}
/**
 * Invoice attachments
 * @param  mixed $invoiceid invoice ID to add attachments
 * @return void  - Result values
 */
function handle_sales_attachments($rel_id, $rel_type)
{
    $hookData = hooks()->apply_filters('before_handle_sales_attachments', [
        'rel_id' => $rel_id,
        'rel_type' => $rel_type,
        'index_name' => 'file',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        echo $hookData['handled_externally_successfully'];
        return;
    }

    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }

    $path = get_upload_path_by_type($rel_type) . $rel_id . '/';

    $CI = &get_instance();
    if (isset($_FILES['file']['name'])) {
        $uploaded_files = false;
        $file_uploaded  = false;
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $type = $_FILES['file']['type'];
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $file_uploaded = true;
                $attachment    = [];
                $attachment[]  = [
                    'file_name' => $filename,
                    'filetype'  => $type,
                ];
                $insert_id = $CI->misc_model->add_attachment_to_database($rel_id, $rel_type, $attachment);
                // Get the key so we can return to ajax request and show download link
                $CI->db->where('id', $insert_id);
                $_attachment = $CI->db->get(db_prefix() . 'files')->row();
                $key         = $_attachment->attachment_key;

                if ($rel_type == 'invoice') {
                    $CI->load->model('invoices_model');
                    $CI->invoices_model->log_invoice_activity($rel_id, 'invoice_activity_added_attachment');
                } elseif ($rel_type == 'estimate') {
                    $CI->load->model('estimates_model');
                    $CI->estimates_model->log_estimate_activity($rel_id, 'estimate_activity_added_attachment');
                }
            }
        }

        if ($file_uploaded == true) {
            echo json_encode([
                'success'       => true,
                'attachment_id' => $insert_id,
                'filetype'      => $type,
                'rel_id'        => $rel_id,
                'file_name'     => $filename,
                'key'           => $key,
            ]);
            add_budget_attachment_activity_log($insert_id, $filename, true);
        } else {
            echo json_encode([
                'success'   => false,
                'rel_id'    => $rel_id,
                'file_name' => $filename,
            ]);
        }
    }
}
/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function handle_client_attachments_upload($id, $customer_upload = false)
{
    $hookData = hooks()->apply_filters('before_handle_client_attachment', [
        'customer_id' => $id,
        'index_name' => 'file',
        'customer_upload' => $customer_upload,
        'total_uploaded' => 0,
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['total_uploaded'];
    }

    $path          = get_upload_path_by_type('customer') . $id . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
    ) {
        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name']     = [$_FILES['file']['name']];
            $_FILES['file']['type']     = [$_FILES['file']['type']];
            $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
            $_FILES['file']['error']    = [$_FILES['file']['error']];
            $_FILES['file']['size']     = [$_FILES['file']['size']];
        }

        _file_attachments_index_fix('file');
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            hooks()->do_action('before_upload_client_attachment', $id);
            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['file']['error'][$i])
                    || !_upload_extension_allowed($_FILES['file']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['file']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'][$i],
                    ];

                    if (is_image($newFilePath)) {
                        create_img_thumb($newFilePath, $filename);
                    }

                    if ($customer_upload == true) {
                        $attachment[0]['staffid']          = 0;
                        $attachment[0]['contact_id']       = get_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($id, 'customer', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}
/**
 * Handles upload for expenses receipt (supports multiple files)
 * @param  mixed $id expense id
 * @return void
 */
function handle_expense_attachments($id)
{
    $CI   = &get_instance();
    $path = get_upload_path_by_type('expense') . $id . '/';

    // Validate if files exist
    if (isset($_FILES['file']) && is_array($_FILES['file']['name'])) {

        // Loop through each file
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

            if (_perfex_upload_error($_FILES['file']['error'][$i])) {
                continue; // Skip files with errors
            }

            $hookData = hooks()->apply_filters('before_handle_expense_attachment', [
                'expense_id' => $id,
                'index_name' => 'file',
                'handled_externally' => false, // e.g. module upload to s3
                'handled_externally_successfully' => false,
                'files' => $_FILES
            ]);

            if ($hookData['handled_externally']) {
                return $hookData['handled_externally_successfully'];
            }

            hooks()->do_action('before_upload_expense_attachment', $id);

            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            $filename    = $_FILES['file']['name'][$i];

            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $newFilePath = $path . $filename;

                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, 'expense', $attachment);
                    $CI->expenses_model->add_expense_receipt_activity_log($id, $filename, true);
                }
            }
        }

    } elseif (isset($_FILES['file']['name'])) {
        // ✅ Fallback: single file upload (for backward compatibility)
        if (_perfex_upload_error($_FILES['file']['error'])) {
            header('HTTP/1.0 400 Bad error');
            echo _perfex_upload_error($_FILES['file']['error']);
            die;
        }

        $tmpFilePath = $_FILES['file']['tmp_name'];
        $filename    = $_FILES['file']['name'];

        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $newFilePath = $path . $filename;

            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];

                $CI->misc_model->add_attachment_to_database($id, 'expense', $attachment);
                $CI->expenses_model->add_expense_receipt_activity_log($id, $filename, true);
            }
        }
    }
}
/**
 * Check for ticket attachment after inserting ticket to database
 * @param  mixed $ticketid
 * @return mixed           false if no attachment || array uploaded attachments
 */
function handle_ticket_attachments($ticketid, $index_name = 'attachments')
{

    $hookData = hooks()->apply_filters('before_handle_ticket_attachment', [
        'ticket_id' => $ticketid,
        'index_name' => $index_name,
        'uploaded_files' => [],
        'handled_externally' => false, // e.g. module upload to s3
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return count($hookData['uploaded_files']) > 0 ? $hookData['uploaded_files'] : false;
    }

    $path           = get_upload_path_by_type('ticket') . $ticketid . '/';
    $uploaded_files = [];

    if (isset($_FILES[$index_name])) {
        _file_attachments_index_fix($index_name);

        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            hooks()->do_action('before_upload_ticket_attachment', $ticketid);

            if ($i <= get_option('maximum_allowed_ticket_attachments')) {
                // Get the temp file path
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Getting file extension
                    $extension = strtolower(pathinfo($_FILES[$index_name]['name'][$i], PATHINFO_EXTENSION));

                    $allowed_extensions = explode(',', get_option('ticket_attachments_file_extensions'));
                    $allowed_extensions = array_map('trim', $allowed_extensions);
                    // Check for all cases if this extension is allowed
                    if (!in_array('.' . $extension, $allowed_extensions)) {
                        continue;
                    }
                    _maybe_create_upload_path($path);
                    $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                    $newFilePath = $path . $filename;
                    // Upload the file into the temp dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        array_push($uploaded_files, [
                            'file_name' => $filename,
                            'filetype'  => $_FILES[$index_name]['type'][$i],
                        ]);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Check for company logo upload
 * @return boolean
 */
function handle_company_logo_upload()
{
    $logoIndex = ['logo', 'logo_dark'];
    $success   = false;

    foreach ($logoIndex as $logo) {
        $index = 'company_' . $logo;

        if (isset($_FILES[$index]) && !empty($_FILES[$index]['name']) && _perfex_upload_error($_FILES[$index]['error'])) {
            set_alert('warning', _perfex_upload_error($_FILES[$index]['error']));

            return false;
        }

        $hookData = hooks()->apply_filters('before_handle_company_logo_upload', [
            'index_name' => $index,
            'handled_externally' => false, // e.g. module upload to s3
            'handled_externally_successfully' => false,
            'files' => $_FILES
        ]);

        if (!$hookData['handled_externally']) {
            if (isset($_FILES[$index]['name']) && $_FILES[$index]['name'] != '') {
                hooks()->do_action('before_upload_company_logo_attachment');
                $path = get_upload_path_by_type('company');
                // Get the temp file path
                $tmpFilePath = $_FILES[$index]['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Getting file extension
                    $extension          = strtolower(pathinfo($_FILES[$index]['name'], PATHINFO_EXTENSION));

                    $allowed_extensions = [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'svg',
                    ];

                    $allowed_extensions = array_unique(
                        hooks()->apply_filters('company_logo_upload_allowed_extensions', $allowed_extensions)
                    );

                    if (!in_array($extension, $allowed_extensions)) {
                        set_alert('warning', 'Image extension not allowed.');

                        continue;
                    }

                    // Setup our new file path
                    $filename    = md5($logo . time()) . '.' . $extension;
                    $newFilePath = $path . $filename;
                    _maybe_create_upload_path($path);
                    // Upload the file into the company uploads dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        update_option($index, $filename);
                        $success = true;
                    }
                }
            }
        } else {
            $success = $hookData['handled_externally_successfully'];
        }
    }

    return $success;
}
/**
 * Check for company logo upload
 * @return boolean
 */
function handle_company_signature_upload()
{
    if (isset($_FILES['signature_image']) && _perfex_upload_error($_FILES['signature_image']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['signature_image']['error']));

        return false;
    }

    $hookData = hooks()->apply_filters('before_handle_company_signature_upload', [
        'index_name' => 'signature_image',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    if (isset($_FILES['signature_image']['name']) && $_FILES['signature_image']['name'] != '') {
        hooks()->do_action('before_upload_signature_image_attachment');
        $path = get_upload_path_by_type('company');
        // Get the temp file path
        $tmpFilePath = $_FILES['signature_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES['signature_image']['name']);
            $extension  = $path_parts['extension'];
            $extension  = strtolower($extension);

            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png',
            ];
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }
            // Setup our new file path
            $filename    = 'signature' . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('signature_image', $filename);

                return true;
            }
        }
    }

    return false;
}
/**
 * Handle company favicon upload
 * @return boolean
 */
function handle_favicon_upload()
{
    $hookData = hooks()->apply_filters('before_handle_favicon_upload', [
        'index_name' => 'favicon',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] != '') {
        hooks()->do_action('before_upload_favicon_attachment');
        $path = get_upload_path_by_type('company');
        // Get the temp file path
        $tmpFilePath = $_FILES['favicon']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES['favicon']['name']);
            $extension  = $path_parts['extension'];
            $extension  = strtolower($extension);
            // Setup our new file path
            $filename    = 'favicon' . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('favicon', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Maybe upload staff profile image
 * @param  string $staff_id staff_id or current logged in staff id will be used if not passed
 * @return boolean
 */
function handle_staff_profile_image_upload($staff_id = '')
{
    if (!is_numeric($staff_id)) {
        $staff_id = get_staff_user_id();
    }

    $hookData = hooks()->apply_filters('before_handle_staff_profile_image_upload', [
        'staff_id' => $staff_id,
        'index_name' => 'profile_image',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        hooks()->do_action('before_upload_staff_profile_image');
        $path = get_upload_path_by_type('staff') . $staff_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $extension          = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png',
            ];

            $allowed_extensions = hooks()->apply_filters('staff_profile_image_upload_allowed_extensions', $allowed_extensions);

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['profile_image']['name']);
            $newFilePath = $path . '/' . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI                       = &get_instance();
                $config                   = [];
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width']          = hooks()->apply_filters('staff_profile_image_thumb_width', 320);
                $config['height']         = hooks()->apply_filters('staff_profile_image_thumb_height', 320);
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width']          = hooks()->apply_filters('staff_profile_image_small_width', 96);
                $config['height']         = hooks()->apply_filters('staff_profile_image_small_height', 96);
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('staffid', $staff_id);
                $CI->db->update(db_prefix() . 'staff', [
                    'profile_image' => $filename,
                ]);
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Maybe upload contact profile image
 * @param  string $contact_id contact_id or current logged in contact id will be used if not passed
 * @return boolean
 */
function handle_contact_profile_image_upload($contact_id = '')
{
    $hookData = hooks()->apply_filters('before_handle_contact_profile_image_upload', [
        'contact_id' => $contact_id,
        'index_name' => 'profile_image',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $hookData['handled_externally_successfully'];
    }

    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        hooks()->do_action('before_upload_contact_profile_image');
        if ($contact_id == '') {
            $contact_id = get_contact_user_id();
        }
        $path = get_upload_path_by_type('contact_profile_images') . $contact_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png',
            ];

            $allowed_extensions = hooks()->apply_filters('contact_profile_image_upload_allowed_extensions', $allowed_extensions);

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['profile_image']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI                       = &get_instance();
                $config                   = [];
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width']          = hooks()->apply_filters('contact_profile_image_thumb_width', 320);
                $config['height']         = hooks()->apply_filters('contact_profile_image_thumb_height', 320);
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width']          = hooks()->apply_filters('contact_profile_image_small_width', 32);
                $config['height']         = hooks()->apply_filters('contact_profile_image_small_height', 32);
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();

                $CI->db->where('id', $contact_id);
                $CI->db->update(db_prefix() . 'contacts', [
                    'profile_image' => $filename,
                ]);
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}
/**
 * Handle upload for project discussions comment
 * Function for jquery-comment plugin
 * @param  mixed $discussion_id discussion id
 * @param  mixed $post_data     additional post data from the comment
 * @param  array $insert_data   insert data to be parsed if needed
 * @return array
 */
function handle_project_discussion_comment_attachments($discussion_id, $post_data, $insert_data)
{
    if (isset($_FILES['file']['name']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo json_encode(['message' => _perfex_upload_error($_FILES['file']['error'])]);
        die;
    }

    $hookData = hooks()->apply_filters('before_handle_project_discussion_comment_attachment', [
        'discussion_id' => $discussion_id,
        'post_data' => $post_data,
        'insert_data' => $insert_data,
        'index_name' => 'file',
        'handled_externally' => false, // e.g. module upload to s3
        'handled_externally_successfully' => false,
        'files' => $_FILES
    ]);

    if ($hookData['handled_externally']) {
        return $insert_data;
    }

    if (isset($_FILES['file']['name'])) {
        hooks()->do_action('before_upload_project_discussion_comment_attachment');
        $path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER . $discussion_id . '/';

        // Check for all cases if this extension is allowed
        if (!_upload_extension_allowed($_FILES['file']['name'])) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(['message' => _l('file_php_extension_blocked')]);
            die;
        }

        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $insert_data['file_name'] = $filename;

                if (isset($_FILES['file']['type'])) {
                    $insert_data['file_mime_type'] = $_FILES['file']['type'];
                } else {
                    $insert_data['file_mime_type'] = get_mime_by_extension($filename);
                }
            }
        }
    }

    return $insert_data;
}

/**
 * Create thumbnail from image
 * @param  string  $path     imat path
 * @param  string  $filename filename to store
 * @param  integer $width    width of thumb
 * @param  integer $height   height of thumb
 * @return null
 */
function create_img_thumb($path, $filename, $width = 300, $height = 300)
{
    $CI = &get_instance();

    $source_path  = rtrim($path, '/') . '/' . $filename;
    $target_path  = $path;
    $config_manip = [
        'image_library'  => 'gd2',
        'source_image'   => $source_path,
        'new_image'      => $target_path,
        'maintain_ratio' => true,
        'create_thumb'   => true,
        'thumb_marker'   => '_thumb',
        'width'          => $width,
        'height'         => $height,
    ];

    $CI->image_lib->initialize($config_manip);
    $CI->image_lib->resize();
    $CI->image_lib->clear();
}

/**
 * Check if extension is allowed for upload
 * @param  string $filename filename
 * @return boolean
 */
function _upload_extension_allowed($filename)
{
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $browser = get_instance()->agent->browser();

    $allowed_extensions = explode(',', get_option('allowed_files'));
    $allowed_extensions = array_map('trim', $allowed_extensions);

    //  https://discussions.apple.com/thread/7229860
    //  Used in main.js too for Dropzone
    if (
        strtolower($browser) === 'safari'
        && in_array('.jpg', $allowed_extensions)
        && !in_array('.jpeg', $allowed_extensions)
    ) {
        $allowed_extensions[] = '.jpeg';
    }
    // Check for all cases if this extension is allowed
    if (!in_array('.' . $extension, $allowed_extensions)) {
        return false;
    }

    return true;
}

/**
 * Performs fixes when $_FILES is array and the index is messed up
 * Eq user click on + then remove the file and then added new file
 * In this case the indexes will be 0,2 - 1 is missing because it's removed but they should be 0,1
 * @param  string $index_name $_FILES index name
 * @return null
 */
function _file_attachments_index_fix($index_name)
{
    if (isset($_FILES[$index_name]['name']) && is_array($_FILES[$index_name]['name'])) {
        $_FILES[$index_name]['name'] = array_values($_FILES[$index_name]['name']);
    }

    if (isset($_FILES[$index_name]['type']) && is_array($_FILES[$index_name]['type'])) {
        $_FILES[$index_name]['type'] = array_values($_FILES[$index_name]['type']);
    }

    if (isset($_FILES[$index_name]['tmp_name']) && is_array($_FILES[$index_name]['tmp_name'])) {
        $_FILES[$index_name]['tmp_name'] = array_values($_FILES[$index_name]['tmp_name']);
    }

    if (isset($_FILES[$index_name]['error']) && is_array($_FILES[$index_name]['error'])) {
        $_FILES[$index_name]['error'] = array_values($_FILES[$index_name]['error']);
    }

    if (isset($_FILES[$index_name]['size']) && is_array($_FILES[$index_name]['size'])) {
        $_FILES[$index_name]['size'] = array_values($_FILES[$index_name]['size']);
    }
}

/**
 * Check if path exists if not exists will create one
 * This is used when uploading files
 * @param  string $path path to check
 * @return null
 */
function _maybe_create_upload_path($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0755);
        fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
    }
}

/**
 * Function that return full path for upload based on passed type
 * @param  string $type
 * @return string
 */
function get_upload_path_by_type($type)
{
    $path = '';
    switch ($type) {
        case 'lead':
            $path = LEAD_ATTACHMENTS_FOLDER;

            break;
        case 'expense':
            $path = EXPENSE_ATTACHMENTS_FOLDER;

            break;
        case 'project':
            $path = PROJECT_ATTACHMENTS_FOLDER;

            break;
        case 'proposal':
            $path = PROPOSAL_ATTACHMENTS_FOLDER;

            break;
        case 'estimate':
            $path = ESTIMATE_ATTACHMENTS_FOLDER;

            break;
        case 'invoice':
            $path = INVOICE_ATTACHMENTS_FOLDER;

            break;
        case 'credit_note':
            $path = CREDIT_NOTES_ATTACHMENTS_FOLDER;

            break;
        case 'task':
            $path = TASKS_ATTACHMENTS_FOLDER;

            break;
        case 'purchase':
            $path = PURCHASE_ATTACHMENTS_FOLDER;

            break;
        case 'changee':
            $path = CHANGEE_ATTACHMENTS_FOLDER;

            break;
        case 'inventory':
            $path = INVENTORY_ATTACHMENTS_FOLDER;

            break;
        case 'contract':
            $path = CONTRACTS_UPLOADS_FOLDER;

            break;
        case 'customer':
            $path = CLIENT_ATTACHMENTS_FOLDER;

            break;
        case 'staff':
            $path = STAFF_PROFILE_IMAGES_FOLDER;

            break;
        case 'company':
            $path = COMPANY_FILES_FOLDER;

            break;
        case 'ticket':
            $path = TICKET_ATTACHMENTS_FOLDER;
            break;
        case 'form':
            $path = FORM_ATTACHMENTS_FOLDER;
            break;
        case 'contact_profile_images':
            $path = CONTACT_PROFILE_IMAGES_FOLDER;

            break;
        case 'newsfeed':
            $path = NEWSFEED_FOLDER;

            break;
        case 'estimate_request':
            $path = NEWSFEED_FOLDER;

            break;
        case 'meeting_management':
            $path = MEETING_FOLDER;

            break;
    }

    return hooks()->apply_filters('get_upload_path_by_type', $path, $type);
}

function handle_form_attachments($formid, $index_name = 'attachments')
{
    $hookData = hooks()->apply_filters('before_handle_form_attachment', [
        'form_id' => $formid,
        'index_name' => $index_name,
        'uploaded_files' => [],
        'handled_externally' => false, // e.g. module upload to s3
        'files' => $_FILES
    ]);
    if ($hookData['handled_externally']) {
        return count($hookData['uploaded_files']) > 0 ? $hookData['uploaded_files'] : false;
    }
    if (!is_dir(get_upload_path_by_type('form'))) {
        mkdir(get_upload_path_by_type('form'), 0755);
    }
    $path           = get_upload_path_by_type('form') . $formid . '/';
    $uploaded_files = [];
    if (isset($_FILES[$index_name])) {
        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            hooks()->do_action('before_upload_form_attachment', $formid);
            if ($i <= get_option('maximum_allowed_form_attachments')) {
                // Get the temp file path
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Getting file extension
                    $extension = strtolower(pathinfo($_FILES[$index_name]['name'][$i], PATHINFO_EXTENSION));
                    $allowed_extensions = explode(',', get_option('form_attachments_file_extensions'));
                    $allowed_extensions = array_map('trim', $allowed_extensions);
                    // Check for all cases if this extension is allowed
                    if (!in_array('.' . $extension, $allowed_extensions)) {
                        continue;
                    }
                    _maybe_create_upload_path($path);
                    $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                    $newFilePath = $path . $filename;
                    // Upload the file into the temp dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        array_push($uploaded_files, [
                            'file_name' => $filename,
                            'filetype'  => $_FILES[$index_name]['type'][$i],
                        ]);
                    }
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }
    return false;
}
function handle_ckecklist_item_attachment_array($related, $form_id, $item_id, $index_name, $itemIndex)
{
    // Define the base path for the attachments
    $base_path = get_upload_path_by_type('form');
    $item_path = $base_path . $related . '/' . $form_id . '/' . $item_id . '/';

    $uploaded_files = [];
    $CI = &get_instance();

    // Ensure $item_path directory exists only when needed
    if (!is_dir($item_path)) {
        mkdir($item_path, 0755, true);
    }

    // Process the $_FILES array for the specific item index
    if (isset($_FILES[$index_name]['name'][$itemIndex]['attachments_new']) && is_array($_FILES[$index_name]['name'][$itemIndex]['attachments_new'])) {
        foreach ($_FILES[$index_name]['name'][$itemIndex]['attachments_new'] as $attachmentKey => $attachmentFileName) {
            if (!empty($attachmentFileName)) {
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$itemIndex]['attachments_new'][$attachmentKey];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Skip if there's an error or the file type is not allowed
                    if (
                        _perfex_upload_error($_FILES[$index_name]['error'][$itemIndex]['attachments_new'][$attachmentKey])
                        || !_upload_extension_allowed($attachmentFileName)
                    ) {
                        continue;
                    }

                    // Generate a unique filename and move the uploaded file
                    $filename = unique_filename($item_path, $attachmentFileName);
                    $newFilePath = $item_path . $filename;

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $uploaded_files[] = [
                            'item_id'  => $item_id, // Map to the item ID
                            'file_name' => $filename,
                            'filetype'  => $_FILES[$index_name]['type'][$itemIndex]['attachments_new'][$attachmentKey],
                        ];
                    }
                }
            }
        }
    }

    // Return uploaded files or clean up the empty directory if no files were uploaded
    if (!empty($uploaded_files)) {
        return $uploaded_files;
    } else {
        if (is_dir($item_path) && count(list_files($item_path)) == 0) {
            delete_dir($item_path);
        }
    }

    return false;
}
function handle_goods_receipt_ckecklist_item_attachment_array($related, $form_id, $item_id, $index_name, $itemIndex)
{
    // Validate inputs to prevent directory traversal or injection attacks
    $sanitized_related = preg_replace('/[^a-zA-Z0-9_-]/', '', $related);
    $sanitized_form_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $form_id);
    $sanitized_item_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $item_id);

    // Define the base path for inventory attachments
    $base_path = get_upload_path_by_type('inventory');
    $path = $base_path . $sanitized_related . '/' . $sanitized_form_id . '/' . $sanitized_item_id . '/';

    // Create necessary directories if they don't exist
    if (!is_dir($base_path)) {
        mkdir($base_path, 0755, true);
    }
    if (!is_dir($base_path . $sanitized_related)) {
        mkdir($base_path . $sanitized_related, 0755, true);
    }
    if (!is_dir($base_path . $sanitized_related . '/' . $sanitized_form_id)) {
        mkdir($base_path . $sanitized_related . '/' . $sanitized_form_id, 0755, true);
    }
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $uploaded_files = [];
    $CI = &get_instance();

    // Check if files exist for this item index
    if (isset($_FILES[$index_name]['name'][$itemIndex]['attachments_new'])) {
        $attachments = $_FILES[$index_name]['name'][$itemIndex]['attachments_new'];

        // Normalize to an array if it's a single file
        if (!is_array($attachments)) {
            $attachments = [$attachments];
            foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $key) {
                $_FILES[$index_name][$key][$itemIndex]['attachments_new'] = [$attachments[0]];
            }
        }

        // Process each attachment
        foreach ($attachments as $attachmentKey => $attachmentFileName) {
            if (empty($attachmentFileName)) {
                continue; // Skip empty file names
            }

            $file = [
                'name'     => $_FILES[$index_name]['name'][$itemIndex]['attachments_new'][$attachmentKey],
                'type'     => $_FILES[$index_name]['type'][$itemIndex]['attachments_new'][$attachmentKey],
                'tmp_name' => $_FILES[$index_name]['tmp_name'][$itemIndex]['attachments_new'][$attachmentKey],
                'error'    => $_FILES[$index_name]['error'][$itemIndex]['attachments_new'][$attachmentKey],
                'size'     => $_FILES[$index_name]['size'][$itemIndex]['attachments_new'][$attachmentKey],
            ];

            // Skip if there's an upload error or invalid file type
            if ($file['error'] !== UPLOAD_ERR_OK || !_upload_extension_allowed($file['name'])) {
                continue;
            }

            // Generate a unique filename and move the uploaded file
            $filename = unique_filename($path, $file['name']);
            $newFilePath = $path . $filename;

            if (move_uploaded_file($file['tmp_name'], $newFilePath)) {
                $uploaded_files[] = [
                    'item_id'   => $sanitized_item_id,
                    'file_name' => $filename,
                    'filetype'  => $file['type'],
                ];

                // Optionally create a thumbnail for images
                if (is_image($newFilePath)) {
                    create_img_thumb($path, $filename);
                }
            }
        }
    }

    // Return uploaded files or clean up empty directories if no files were uploaded
    if (!empty($uploaded_files)) {
        return $uploaded_files;
    } else {
        // Clean up empty directories
        if (is_dir($path) && count(list_files($path)) === 0) {
            @rmdir($path); // Remove item directory
            $parent_dir = dirname($path);
            if (is_dir($parent_dir) && count(list_files($parent_dir)) === 0) {
                @rmdir($parent_dir); // Remove form_id directory if empty
            }
        }
    }

    return false;
}
function handle_qor_item_attachment_array($related, $form_id, $item_id, $itemIndex)
{
    $base_path = get_upload_path_by_type('form');
    $item_path = $base_path . $related . '/' . $form_id . '/' . $item_id . '/';
    $uploaded_files = [];

    $CI = &get_instance();

    // Validate and create path
    if (!is_dir($item_path)) {
        mkdir($item_path, 0755, true);
    }

    // Check if there are any attachments for this index
    if (!empty($_FILES['attachments']['name'][$itemIndex]) && is_array($_FILES['attachments']['name'][$itemIndex])) {
        foreach ($_FILES['attachments']['name'][$itemIndex] as $key => $fileName) {
            if (!empty($fileName)) {
                $tmpFilePath = $_FILES['attachments']['tmp_name'][$itemIndex][$key];
                $fileError   = $_FILES['attachments']['error'][$itemIndex][$key];
                $fileType    = $_FILES['attachments']['type'][$itemIndex][$key];

                // Validate file
                if (!empty($tmpFilePath) && $fileError === 0 && _upload_extension_allowed($fileName)) {
                    $filename = unique_filename($item_path, $fileName);
                    $newFilePath = $item_path . $filename;

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $uploaded_files[] = [
                            'item_id'   => $item_id,
                            'file_name' => $filename,
                            'filetype'  => $fileType,
                        ];
                    }
                }
            }
        }
    }

    // Clean up if no files
    if (!empty($uploaded_files)) {
        return $uploaded_files;
    } else {
        if (is_dir($item_path) && count(list_files($item_path)) == 0) {
            delete_dir($item_path);
        }
    }

    return false;
}

function handle_agends_attachments_array($related, $id, $index_name = 'attachments')
{
    if (!is_dir(get_upload_path_by_type('meeting_management'))) {
        mkdir(get_upload_path_by_type('meeting_management'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('meeting_management') . $related)) {
        mkdir(get_upload_path_by_type('meeting_management') . $related, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('meeting_management') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

function handle_payment_certificate_attachments_array($id, $index_name = 'attachments')
{
    $related = 'payment_certificate';
    if (!is_dir(get_upload_path_by_type('purchase'))) {
        mkdir(get_upload_path_by_type('purchase'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related)) {
        mkdir(get_upload_path_by_type('purchase') . $related, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('purchase') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Debit Notes attachments upload array
 * Multiple debit notes attachments can be upload if input type is array or dropzone plugin is used
 * @param  mixed $id id
 * @param  string $index_name attachments index, in different forms different index name is used
 * @return mixed
 */
function handle_debit_notes_attachments_array($id, $index_name = 'attachments')
{
    $related = 'debit_note';
    if (!is_dir(get_upload_path_by_type('purchase'))) {
        mkdir(get_upload_path_by_type('purchase'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related)) {
        mkdir(get_upload_path_by_type('purchase') . $related, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('purchase') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Bill Bifurcation attachments upload array
 * Multiple Bill Bifurcation attachments can be upload if input type is array or dropzone plugin is used
 * @param  mixed $id id
 * @param  string $index_name attachments index, in different forms different index name is used
 * @return mixed
 */
function handle_pur_bills_attachments_array($id, $index_name = 'attachments')
{
    $related = 'pur_bills';
    if (!is_dir(get_upload_path_by_type('purchase'))) {
        mkdir(get_upload_path_by_type('purchase'), 0755);
    }
    if (!is_dir(get_upload_path_by_type('purchase') . $related)) {
        mkdir(get_upload_path_by_type('purchase') . $related, 0755);
    }
    $uploaded_files = [];
    $path           = get_upload_path_by_type('purchase') . $related . '/' . $id . '/';
    $CI             = &get_instance();

    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        // create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}
