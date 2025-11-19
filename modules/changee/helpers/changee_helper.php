<?php
defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('after_email_templates', 'add_changee_email_templates');
/**
 * Check whether column exists in a table
 * Custom function because Codeigniter is caching the tables and this is causing issues in migrations
 * @param  string $column column name to check
 * @param  string $table table name to check
 * @return boolean
 */
/**
 * Determines whether the specified identifier is empty vendor company.
 *
 * @param      <type>   $id     The identifier
 *
 * @return     boolean  True if the specified identifier is empty vendor company, False otherwise.
 */
function changee_is_empty_vendor_company($id)
{
    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from(db_prefix() . 'pur_vendor');
    $CI->db->where('userid', $id);
    $row = $CI->db->get()->row();
    if ($row) {
        if ($row->company == '') {
            return true;
        }

        return false;
    }

    return true;
}

/**
 * Gets the sql select vendor company.
 *
 * @return     string  The sql select vendor company.
 */
function changee_get_sql_select_vendor_company()
{
    return 'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'pur_contacts WHERE ' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid and is_primary = 1) ELSE company END as company';
}

/**
 * Determines if vendor admin.
 *
 * @param      string   $id        The identifier
 * @param      string   $staff_id  The staff identifier
 *
 * @return     integer  True if vendor admin, False otherwise.
 */
function changee_is_vendor_admin($id, $staff_id = '')
{
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $CI       = &get_instance();
    $cache    = $CI->app_object_cache->get($id . '-is-vendor-admin-' . $staff_id);

    if ($cache) {
        return $cache['retval'];
    }

    $total = total_rows(db_prefix() . 'pur_vendor_admin', [
        'vendor_id' => $id,
        'staff_id'    => $staff_id,
    ]);

    $retval = $total > 0 ? true : false;
    $CI->app_object_cache->add($id . '-is-vendor-admin-' . $staff_id, ['retval' => $retval]);

    return $retval;
}

/**
 * Gets the vendor company name.
 *
 * @param      string   $userid                 The userid
 * @param      boolean  $prevent_empty_company  The prevent empty company
 *
 * @return     string   The vendor company name.
 */
function changee_get_vendor_company_name($userid, $prevent_empty_company = false)
{
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI = &get_instance();

    $client = $CI->db->select('company')
        ->where('userid', $_userid)
        ->from(db_prefix() . 'pur_vendor')
        ->get()
        ->row();
    if ($client) {
        return $client->company;
    }

    return '';
}

/**
 * Gets the status approve.
 *
 * @param      integer|string  $status  The status
 *
 * @return     string          The status approve.
 */
function changee_get_status_approve($status)
{
    $result = '';
    if ($status == 1) {
        $result = '<span class="label label-primary"> ' . _l('changee_draft') . ' </span>';
    } elseif ($status == 2) {
        $result = '<span class="label label-success"> ' . _l('changee_approved') . ' </span>';
    } elseif ($status == 3) {
        $result = '<span class="label label-warning"> ' . _l('pur_rejected') . ' </span>';
    } elseif ($status == 4) {
        $result = '<span class="label label-danger"> ' . _l('pur_canceled') . ' </span>';
    }

    return $result;
}

/**
 * Gets the status approve string.
 *
 * @param      integer  $status  The status
 *
 * @return     string   The status approve string.
 */
function changee_get_status_approve_str($status)
{
    $result = '';
    if ($status == 1) {
        $result = _l('changee_draft');
    } elseif ($status == 2) {
        $result = _l('changee_approved');
    } elseif ($status == 3) {
        $result = _l('pur_rejected');
    } elseif ($status == 4) {
        $result = _l('pur_canceled');
    }

    return $result;
}

/**
 * Gets the status pur order.
 *
 * @param      integer|string  $status  The status
 *
 * @return     string          The status pur order.
 */
function changee_get_status_pur_order($status)
{
    $result = '';
    if ($status == 1) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-1"> ' . _l('not_start') . ' </span>';
    } elseif ($status == 2) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-2"> ' . _l('in_proccess') . ' </span>';
    } elseif ($status == 3) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-3"> ' . _l('complete') . ' </span>';
    } elseif ($status == 4) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-4"> ' . _l('cancel') . ' </span>';
    }

    return $result;
}

/**
 * { format pur estimate number }
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  ( estimate number )
 */
function changee_format_pur_estimate_number($id)
{
    $CI = &get_instance();
    $CI->db->select('date,number,prefix,number_format')->from(db_prefix() . 'co_estimates')->where('id', $id);
    $estimate = $CI->db->get()->row();

    if (!$estimate) {
        return '';
    }

    $number = sales_number_format($estimate->number, $estimate->number_format, $estimate->prefix, $estimate->date);

    return hooks()->apply_filters('format_estimate_number', $number, [
        'id'       => $id,
        'estimate' => $estimate,
    ]);
}

/**
 * Gets the item hp.
 *
 * @param      string  $id     The identifier
 *
 * @return     <type>  a item or list item.
 */
function changee_get_item_hp($id = '')
{
    $CI           = &get_instance();
    if ($id != '') {
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix() . 'items')->row();
    } elseif ($id == '') {
        return $CI->db->get(db_prefix() . 'items')->result_array();
    }
}

/**
 * Gets the item hp.
 *
 * @param      string  $id     The identifier
 *
 * @return     <type>  a item or list item.
 */
function changee_get_item_hp2($id)
{
    $CI           = &get_instance();

    $CI->db->where('id', $id);
    return $CI->db->get(db_prefix() . 'items')->row();
}

/**
 * Gets the status modules pur.
 *
 * @param      string   $module_name  The module name
 *
 * @return     boolean  The status modules pur.
 */
function changee_get_status_modules_pur($module_name)
{
    $CI             = &get_instance();
    $sql = 'select * from ' . db_prefix() . 'modules where module_name = "' . $module_name . '" AND active =1 ';
    $module = $CI->db->query($sql)->row();
    if ($module) {
        return true;
    } else {
        return false;
    }
}

/**
 * { reformat currency pur }
 *
 * @param      <string>  $value  The value
 *
 * @return     <string>  ( string replace ',' )
 */
function changee_reformat_currency_pur($value, $currency = 0)
{
    $CI             = &get_instance();
    $CI->load->model('currencies_model');

    $base_currency = $CI->currencies_model->get_base_currency();

    if ($currency != 0) {
        $base_currency = changee_pur_get_currency_by_id($currency);
    }

    if ($base_currency->decimal_separator == ',') {
        $new_val = str_replace('.', '', $value);
        return str_replace(',', '.', $new_val);
    }
    return str_replace(',', '', $value);
}

/**
 * { pur contract pdf }
 *
 * @param      <type>  $contract  The contract
 *
 * @return     <type>  ( pdf )
 */
function changee_pur_contract_pdf($contract)
{
    return app_pdf('changee_contract',  module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_contract_pdf'), $contract);
}

/**
 * { changee process digital signature image }
 *
 * @param      <type>   $partBase64  The part base 64
 * @param      <type>   $path        The path
 * @param      string   $image_name  The image name
 *
 * @return     boolean  
 */
function changee_changee_process_digital_signature_image($partBase64, $path, $image_name)
{
    if (empty($partBase64)) {
        return false;
    }

    _maybe_create_upload_path($path);
    $filename = unique_filename($path, $image_name . '.png');

    $decoded_image = base64_decode($partBase64);

    $retval = false;

    $path = rtrim($path, '/') . '/' . $filename;

    $fp = fopen($path, 'w+');

    if (fwrite($fp, $decoded_image)) {
        $retval                                 = true;
        $GLOBALS['processed_digital_signature'] = $filename;
    }

    fclose($fp);

    return $retval;
}

/**
 * { handle request quotation upload file quotation }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean   
 */
function changee_handle_request_quotation($id)
{
    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {

        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/request_quotation/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, str_replace(" ", "_", $_FILES['attachment']['name']));
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the staff email by identifier pur.
 *
 * @param      <type>   $id     The identifier
 *
 * @return     boolean  The staff email by identifier pur.
 */
function changee_get_staff_email_by_id_pur($id)
{
    $CI = &get_instance();

    $staff = $CI->app_object_cache->get('staff-email-by-id-' . $id);

    if (!$staff) {
        $CI->db->where('staffid', $id);
        $staff = $CI->db->select('email')->from(db_prefix() . 'staff')->get()->row();
        $CI->app_object_cache->add('staff-email-by-id-' . $id, $staff);
    }

    return ($staff ? $staff->email : '');
}

/**
 * Gets the changee option.
 *
 * @param      <type>        $name   The name
 *
 * @return     array|string  The changee option.
 */
function changee_get_changee_option($name)
{
    $CI = &get_instance();
    $options = [];
    $val  = '';
    $name = trim($name);


    if (!isset($options[$name])) {
        // is not auto loaded
        $CI->db->select('option_val');
        $CI->db->where('option_name', $name);
        $row = $CI->db->get(db_prefix() . 'changee_option')->row();
        if ($row) {
            $val = $row->option_val;
        }
    } else {
        $val = $options[$name];
    }

    return $val;
}

/**
 * { row changee options exist }
 *
 * @param      <type>   $name   The name
 *
 * @return     integer  ( 1 or 0 )
 */
function changee_row_changee_options_exist($name)
{
    $CI = &get_instance();
    $i = count($CI->db->query('Select * from ' . db_prefix() . 'changee_option where option_name = ' . $name)->result_array());
    if ($i == 0) {
        return 0;
    }
    if ($i > 0) {
        return 1;
    }
}

/**
 * { handle changee order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function changee_handle_changee_order_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $id . '/';
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
                $CI->misc_model->add_attachment_to_database($id, 'pur_order', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { handle changee order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function changee_handle_changee_request_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/co_request/' . $id . '/';
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
                $CI->misc_model->add_attachment_to_database($id, 'co_request', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { purorder left to pay }
 *
 * @param      <type>   $id     The changee order
 *
 * @return     integer  ( changee order left to pay )
 */
function changee_purorder_left_to_pay($id)
{
    $CI = &get_instance();


    $CI->db->select('total')
        ->where('id', $id);
    $invoice_total = $CI->db->get(db_prefix() . 'co_orders')->row()->total;



    $CI->load->model('changee_model');

    $payments = $CI->changee_model->get_payment_changee_order($id);

    $totalPayments = 0;



    foreach ($payments as $payment) {

        $totalPayments += $payment['amount'];
    }

    return ($invoice_total - $totalPayments);
}

/**
 * Gets the payment mode by identifier.
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  The payment mode by identifier.
 */
function changee_get_payment_mode_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $mode = $CI->db->get(db_prefix() . 'payment_modes')->row();
    if ($mode) {
        return $mode->name;
    } else {
        return '';
    }
}

/**
 * get unit type
 * @param  integer $id
 * @return array or row
 */
function changee_get_unit_type_item($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('unit_type_id', $id);
        return $CI->db->get(db_prefix() . 'ware_unit_type')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblware_unit_type')->result_array();
    }
}


/**
 * handle commodity attchment
 * @param  integer $id
 * @return array or row
 */
function changee_handle_item_attachments($id)
{

    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $id . '/';
    $CI   = &get_instance();

    if (isset($_FILES['file']['name'])) {

        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            _maybe_create_upload_path($path);
            $filename    = $_FILES['file']['name'];
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];

                $CI->misc_model->add_attachment_to_database($id, 'commodity_item_file', $attachment);
            }
        }
    }
}


/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
function changee_get_tax_rate_item($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'taxes')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tbltaxes')->result_array();
    }
}


/**
 * get group name
 * @param  integer $id
 * @return array or row
 */
function changee_get_group_name_item($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items_groups')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems_groups')->result_array();
    }
}

/**
 * { function_description }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function changee_max_number_pur_order()
{
    $CI           = &get_instance();
    $max = $CI->db->query('select MAX(number) as max from ' . db_prefix() . 'co_orders')->row();
    return $max->max;
}

/**
 * Gets all pur vendor attachments.
 *
 * @param      <type>  $id     The identifier
 *
 * @return     array   All pur vendor attachments.
 */
function changee_get_all_pur_vendor_attachments($id)
{
    $CI = &get_instance();

    $attachments                = [];
    $attachments['changee_vendor']    = [];

    $CI->db->where('rel_id', $id);
    $CI->db->where('rel_type', 'pur_vendor');
    $client_main_attachments = $CI->db->get(db_prefix() . 'files')->result_array();

    $attachments['changee_vendor'] = $client_main_attachments;

    return $attachments['changee_vendor'];
}

/**
 * { handle changee vendor attachments upload }
 *
 * @param      string   $id               The identifier
 * @param      boolean  $customer_upload  The customer upload
 *
 * @return     boolean  
 */
function changee_handle_pur_vendor_attachments_upload($id, $customer_upload = false)
{

    $path           = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $id . '/';
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
                        $attachment[0]['contact_id']       = changee_get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($id, 'pur_vendor', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Gets the template part.
 *
 * @param      string   $name    The name
 * @param      array    $data    The data
 * @param      boolean  $return  The return
 *
 * @return     string   The template part.
 */
function changee_get_template_part_pur($name, $data = [], $return = false)
{
    if ($name === '') {
        return '';
    }

    $CI   = &get_instance();
    $path = 'vendor_portal/template_parts/';

    if ($return == true) {
        return $CI->load->view($path . $name, $data, true);
    }

    $CI->load->view($path . $name, $data);
}

/**
 * Maybe upload contact profile image
 * @param  string $contact_id contact_id or current logged in contact id will be used if not passed
 * @return boolean
 */
function changee_handle_vendor_contact_profile_image_upload($contact_id = '')
{
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        hooks()->do_action('before_upload_contact_profile_image');
        if ($contact_id == '') {
            $contact_id = changee_get_vendor_contact_user_id();
        }
        $path =  PURCHASE_MODULE_UPLOAD_FOLDER . '/contact_profile/' . $contact_id . '/';
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
                $CI->db->update(db_prefix() . 'pur_contacts', [
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
 * Return contact profile image url
 * @param  mixed $contact_id
 * @param  string $type
 * @return string
 */
function changee_vendor_contact_profile_image_url($contact_id, $type = 'small')
{
    $url  = base_url('assets/images/user-placeholder.jpg');
    $CI   = &get_instance();
    $path = $CI->app_object_cache->get('contact-profile-image-path-' . $contact_id);

    if (!$path) {
        $CI->app_object_cache->add('contact-profile-image-path-' . $contact_id, $url);

        $CI->db->select('profile_image');
        $CI->db->from(db_prefix() . 'pur_contacts');
        $CI->db->where('id', $contact_id);
        $contact = $CI->db->get()->row();

        if ($contact && !empty($contact->profile_image)) {
            $path = PURCHASE_PATH . 'contact_profile/' . $contact_id . '/' . $type . '_' . $contact->profile_image;
            $CI->app_object_cache->set('contact-profile-image-path-' . $contact_id, $path);
        }
    }

    if ($path && file_exists($path)) {
        $url = base_url($path);
    }

    return $url;
}

/**
 * Gets the pur order subject.
 *
 * @param      <type>  $pur_order  The pur order
 *
 * @return     string  The pur order subject.
 */
function changee_get_pur_order_subject($pur_order)
{
    $CI   = &get_instance();
    $CI->db->where('id', $pur_order);
    $po = $CI->db->get(db_prefix() . 'co_orders')->row();

    if ($po) {
        return $po->pur_order_number;
    } else {
        return '';
    }
}

/**
 * { function_description }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function changee_max_number_estimates()
{
    $CI           = &get_instance();
    $max = $CI->db->query('select MAX(number) as max from ' . db_prefix() . 'co_estimates')->row();
    return $max->max;
}

/**
 * Check if the document should be RTL or LTR
 * The checking are performed in multiple ways eq Contact/Staff Direction from profile or from general settings *
 * @param  boolean $client_area
 * @return boolean
 */
function changee_is_rtl_pur($client_area = false)
{
    $CI = &get_instance();
    if (changee_is_vendor_logged_in()) {
        $CI->db->select('direction')->from(db_prefix() . 'pur_contacts')->where('id', changee_get_vendor_contact_user_id());
        $direction = $CI->db->get()->row()->direction;

        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_client') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == true) {
        // Client not logged in and checked from clients area
        if (get_option('rtl_support_client') == 1) {
            return true;
        }
    } elseif (is_staff_logged_in()) {
        if (isset($GLOBALS['current_user'])) {
            $direction = $GLOBALS['current_user']->direction;
        } else {
            $CI->db->select('direction')->from(db_prefix() . 'staff')->where('staffid', get_staff_user_id());
            $direction = $CI->db->get()->row()->direction;
        }

        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_admin') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == false) {
        if (get_option('rtl_support_admin') == 1) {
            return true;
        }
    }

    return false;
}

/**
 * init vendor area assets.
 */
function changee_init_vendor_area_assets()
{
    // Used by themes to add assets
    hooks()->do_action('app_vendor_assets');

    hooks()->do_action('app_client_assets_added');
}

/**
 * { register theme vendor assets hook }
 *
 * @param      <type>   $function  The function
 *
 * @return     boolean  
 */
function changee_register_theme_vendor_assets_hook($function)
{
    if (hooks()->has_action('app_vendor_assets', $function)) {
        return false;
    }

    return hooks()->add_action('app_vendor_assets', $function, 1);
}

/**
 * { app customers head }
 *
 * @param      <type>  $language  The language
 */
function changee_app_vendor_head($language = null)
{
    // $language param is deprecated
    if (is_null($language)) {
        $language = $GLOBALS['language'];
    }

    if (file_exists(FCPATH . 'assets/css/custom.css')) {
        echo '<link href="' . base_url('assets/css/custom.css') . '" rel="stylesheet" type="text/css" id="custom-css">' . PHP_EOL;
    }

    hooks()->do_action('changee_app_vendor_head');
}

/**
 * { app theme head hook }
 */
function changee_app_theme_vendor_head_hook()
{
    $CI = &get_instance();
    ob_start();
    echo get_custom_fields_hyperlink_js_function();

    if (
        get_option('use_recaptcha_customers_area') == 1
        && get_option('recaptcha_secret_key') != ''
        && get_option('recaptcha_site_key') != ''
    ) {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }

    $isRTL = (changee_is_rtl_pur(true) ? 'true' : 'false');

    $locale = get_locale_key($GLOBALS['language']);

    $maxUploadSize = file_upload_max_size();

    $date_format = get_option('dateformat');
    $date_format = explode('|', $date_format);
    $date_format = $date_format[0]; ?>
    <script>
        <?php if (is_staff_logged_in()) {
        ?>
            var admin_url = '<?php echo admin_url(); ?>';
        <?php
        } ?>

        var site_url = '<?php echo site_url(''); ?>',
            app = {},
            cfh_popover_templates = {};

        app.isRTL = '<?php echo changee_pur_html_entity_decode($isRTL); ?>';
        app.is_mobile = '<?php echo is_mobile(); ?>';
        app.months_json = '<?php echo json_encode([_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December')]); ?>';

        app.browser = "<?php echo strtolower($CI->agent->browser()); ?>";
        app.max_php_ini_upload_size_bytes = "<?php echo changee_pur_html_entity_decode($maxUploadSize); ?>";
        app.locale = "<?php echo changee_pur_html_entity_decode($locale); ?>";

        app.options = {
            calendar_events_limit: "<?php echo get_option('calendar_events_limit'); ?>",
            calendar_first_day: "<?php echo get_option('calendar_first_day'); ?>",
            tables_pagination_limit: "<?php echo get_option('tables_pagination_limit'); ?>",
            enable_google_picker: "<?php echo get_option('enable_google_picker'); ?>",
            google_client_id: "<?php echo get_option('google_client_id'); ?>",
            google_api: "<?php echo get_option('google_api_key'); ?>",
            default_view_calendar: "<?php echo get_option('default_view_calendar'); ?>",
            timezone: "<?php echo get_option('default_timezone'); ?>",
            allowed_files: "<?php echo get_option('allowed_files'); ?>",
            date_format: "<?php echo changee_pur_html_entity_decode($date_format); ?>",
            time_format: "<?php echo get_option('time_format'); ?>",
        };

        app.lang = {
            file_exceeds_maxfile_size_in_form: "<?php echo _l('file_exceeds_maxfile_size_in_form'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            file_exceeds_max_filesize: "<?php echo _l('file_exceeds_max_filesize'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            validation_extension_not_allowed: "<?php echo _l('validation_extension_not_allowed'); ?>",
            sign_document_validation: "<?php echo _l('sign_document_validation'); ?>",
            dt_length_menu_all: "<?php echo _l('dt_length_menu_all'); ?>",
            drop_files_here_to_upload: "<?php echo _l('drop_files_here_to_upload'); ?>",
            browser_not_support_drag_and_drop: "<?php echo _l('browser_not_support_drag_and_drop'); ?>",
            confirm_action_prompt: "<?php echo _l('confirm_action_prompt'); ?>",
            datatables: <?php echo json_encode(get_datatables_language_array()); ?>,
            discussions_lang: <?php echo json_encode(get_project_discussions_language_array()); ?>,
        };
        window.addEventListener('load', function() {
            custom_fields_hyperlink();
        });
    </script>
<?php

    _do_clients_area_deprecated_js_vars($date_format, $locale, $maxUploadSize, $isRTL);

    $contents = ob_get_contents();
    ob_end_clean();
    echo changee_pur_html_entity_decode($contents);
}

/**
 * Get customer id by passed contact id
 * @param  mixed $id
 * @return mixed
 */
function changee_get_user_id_by_contact_id_pur($id)
{
    $CI = &get_instance();

    $userid = $CI->app_object_cache->get('user-id-by-contact-id-' . $id);
    if (!$userid) {
        $CI->db->select('userid')
            ->where('id', $id);
        $client = $CI->db->get(db_prefix() . 'pur_contacts')->row();

        if ($client) {
            $userid = $client->userid;
            $CI->app_object_cache->add('user-id-by-contact-id-' . $id, $userid);
        }
    }

    return $userid;
}

/**
 * get group name
 * @param  integer $id
 * @return array or row
 */
function changee_get_group_name_pur($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items_groups')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems_groups')->result_array();
    }
}

/**
 * { check changee order restrictions }
 *
 * @param        $id     The identifier
 * @param        $hash   The hash
 */
function changee_check_pur_order_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if (!$hash || !$id) {
        show_404();
    }


    $pur_order = $CI->changee_model->get_pur_order($id);
    if (!$pur_order || ($pur_order->hash != $hash)) {
        show_404();
    }
}

/**
 * { check changee request restrictions }
 *
 * @param        $id     The identifier
 * @param        $hash   The hash
 */
function changee_check_co_request_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if (!$hash || !$id) {
        show_404();
    }


    $co_request = $CI->changee_model->get_changee_request($id);
    if (!$co_request || ($co_request->hash != $hash)) {
        show_404();
    }
}


function changee_get_pur_order_by_client($client)
{
    $CI = &get_instance();
    $CI->db->where('find_in_set(' . $client . ', clients)');
    return $CI->db->get(db_prefix() . 'co_orders')->result_array();
}

/**
 * { handle changee contract file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean 
 */
function changee_handle_pur_contract_file($id)
{

    $path           = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $id . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)
    ) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name']     = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type']     = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error']    = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size']     = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['attachments']['error'][$i])
                    || !_upload_extension_allowed($_FILES['attachments']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['attachments']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, 'pur_contract', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Is vendor logged in
 * @return boolean
 */
function changee_is_vendor_logged_in()
{
    return get_instance()->session->has_userdata('vendor_logged_in');
}

/**
 * Return logged vendor User ID from session
 * @return mixed
 */
function changee_get_vendor_user_id()
{
    if (!changee_is_vendor_logged_in()) {
        return false;
    }

    return get_instance()->session->userdata('vendor_user_id');
}

/**
 * Get contact user id
 * @return mixed
 */
function changee_get_vendor_contact_user_id()
{
    $CI = &get_instance();
    if (!$CI->session->has_userdata('vendor_contact_user_id')) {
        return false;
    }

    return $CI->session->userdata('vendor_contact_user_id');
}

/**
 * Check if contact id passed is primary contact
 * If you dont pass $contact_id the current logged in contact will be checked
 * @param  string  $contact_id
 * @return boolean
 */
function changee_is_primary_contact_pur($contact_id = '')
{
    if (!is_numeric($contact_id)) {
        $contact_id = changee_get_vendor_contact_user_id();
    }

    if (total_rows(db_prefix() . 'pur_contacts', ['id' => $contact_id, 'is_primary' => 1]) > 0) {
        return true;
    }

    return false;
}

/**
 * { handle changee order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function changee_handle_changee_estimate_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $id . '/';
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
                $CI->misc_model->add_attachment_to_database($id, 'pur_estimate', $attachment);

                return true;
            }
        }
    }

    return false;
}

function changee_get_vendor_cate_name_by_id($id)
{
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');
    $category = $CI->changee_model->get_vendor_category($id);
    if ($category) {
        return $category->category_name;
    } else {
        return '';
    }
}

/**
 * Gets the vendor category html.
 *
 * @param      string  $category  The category
 */
function changee_get_vendor_category_html($category)
{
    $rs = '';
    if ($category != '') {
        $cates = explode(',', $category);
        foreach ($cates as $cat) {
            $cat_name = changee_get_vendor_cate_name_by_id($cat);
            if ($cat_name != '') {
                $rs .= '<span class="label label-tag">' . $cat_name . '</span>';
            }
        }
    }
    return $rs;
}

/**
 * { department pur request name }
 *
 * @param       $dpm    The dpm
 *
 * @return     string  
 */
function changee_department_co_request_name($dpm)
{
    $CI = &get_instance();
    $CI->load->model('departments_model');
    $department = $CI->departments_model->get($dpm);
    $name_rs = '';
    if ($department) {
        $name_repl = str_replace(' ', '', $department->name);
        $name_rs = strtoupper($name_repl);
    }

    return $name_rs;
}

/**
 * Gets the po html by pur request.
 *
 * @param  $co_request  The pur request
 */
function changee_get_po_html_by_co_request($co_request)
{
    $CI = &get_instance();
    $CI->db->where('co_request', $co_request);
    $list = $CI->db->get(db_prefix() . 'co_orders')->result_array();
    $rs = '';
    $count = 0;
    if (count($list) > 0) {
        foreach ($list as $li) {
            $rs .= '<a href="' . admin_url('changee/changee_order/' . $li['id']) . '" ><span class="label label-tag mbot5">' . $li['pur_order_number'] . '</span></a>&nbsp;';
        }
    }
    return $rs;
}

/**
 * Gets the pur contract number.
 *
 * @param        $id     The identifier
 *
 * @return       The pur contract number.
 */
function changee_get_pur_contract_number($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $contract = $CI->db->get(db_prefix() . 'pur_contracts')->row();
    if ($contract) {
        return $contract->contract_number;
    } else {
        return '';
    }
}

/**
 * Gets the pur invoice number.
 *
 * @param        $id     The identifier
 *
 * @return     string  The pur invoice number.
 */
function changee_get_pur_invoice_number($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $inv = $CI->db->get(db_prefix() . 'pur_invoices')->row();
    if ($inv) {
        return $inv->invoice_number;
    } else {
        return '';
    }
}

/**
 * { changee invoice left to pay }
 *
 * @param      <type>   $id     The changee order
 *
 * @return     integer  ( changee order left to pay )
 */
function changee_purinvoice_left_to_pay($id)
{
    $CI = &get_instance();


    $CI->db->select('total')
        ->where('id', $id);
    $invoice_total = $CI->db->get(db_prefix() . 'pur_invoices')->row()->total;


    $CI->db->where('pur_invoice', $id);
    $CI->db->where('approval_status', 2);
    $payments = $CI->db->get(db_prefix() . 'pur_invoice_payment')->result_array();

    $debits  = $CI->changee_model->get_applied_invoice_debits($id);

    $payments = array_merge($payments, $debits);


    $totalPayments = 0;


    foreach ($payments as $payment) {

        $totalPayments += $payment['amount'];
    }

    return ($invoice_total - $totalPayments);
}

/**
 * Gets the payment mode name by identifier.
 *
 * @param        $id     The identifier
 *
 * @return     string  The payment mode name by identifier.
 */
function changee_get_payment_mode_name_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $mode = $CI->db->get(db_prefix() . 'payment_modes')->row();
    if ($mode) {
        return $mode->name;
    } else {
        return '';
    }
}

/**
 * Gets the payment request status by inv.
 *
 * @param        $id     The identifier
 *
 * @return     string  The payment request status by inv.
 */
function changee_get_payment_request_status_by_inv($id)
{
    $CI = &get_instance();
    $CI->db->where('pur_invoice', $id);
    $payments = $CI->db->get(db_prefix() . 'pur_invoice_payment')->result_array();
    $status = '';
    $class = '';
    if (count($payments) > 0) {
        $status = 'created';
        $class = 'info';
        $CI->db->where('pur_invoice', $id);
        $CI->db->where('approval_status', 2);
        $payments_approved = $CI->db->get(db_prefix() . 'pur_invoice_payment')->result_array();
        if (count($payments_approved)) {
            $status = 'approved';
            $class = 'success';
        }
    } else {
        $status = 'blank';
        $class = 'warning';
    }

    if ($status != '') {
        return '<span class="label label-' . $class . ' s-status invoice-status-3">' . _l($status) . '</span>';
    } else {
        return '';
    }
}

/**
 * { handle pur invoice file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function changee_handle_pur_invoice_file($id)
{
    $type = 'pur_invoice';
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/' . $type . '/' . $id . '/';
    $CI = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)
    ) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name'] = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type'] = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error'] = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size'] = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['attachments']['error'][$i])
                    || !_upload_extension_allowed($_FILES['attachments']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $_FILES['attachments']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, $type, $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * { handle send quotation upload file  }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean   
 */
function changee_handle_send_quotation($id)
{
    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/send_quotation/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, str_replace(" ", "_", $_FILES['attachment']['name']));
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * { handle send quotation upload file  }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean   
 */
function changee_handle_send_po($id)
{
    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/send_po/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, str_replace(" ", "_", $_FILES['attachment']['name']));
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return true;
            }
        }
    }

    return false;
}

if (!function_exists('add_changee_email_templates')) {
    /**
     * Init appointly email templates and assign languages
     * @return void
     */
    function add_changee_email_templates()
    {
        $CI = &get_instance();

        $data['changee_templates'] = $CI->emails_model->get(['type' => 'changee_order', 'language' => 'english']);

        $CI->load->view('changee/email_templates', $data);
    }
}

/*
* php delete function that deals with directories recursively
*/
function changee_delete_files_pur($target)
{
    if (is_dir($target)) {
        $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
        foreach ($files as $file) {
            if ($file != $target . 'signature\\' && is_dir($file)) {
                delete_dir($file);
            }
        }
    }
}


/**
 * { handle changee order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function changee_handle_po_logo()
{
    if (isset($_FILES['po_logo']['name']) && $_FILES['po_logo']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . '0/';
        // Get the temp file path
        $tmpFilePath = $_FILES['po_logo']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['po_logo']['name']);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $filename = 'po_logo_' . time() . '.' . $extension;
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = &get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['po_logo']['type'],
                ];
                $CI->misc_model->add_attachment_to_database(0, 'po_logo', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the po logo.
 */
function changee_get_po_logo($width = 120, $class = '', $type = 'pdf')
{
    $CI           = &get_instance();
    $CI->db->where('rel_id', 0);
    $CI->db->where('rel_type', 'po_logo');
    $logo = $CI->db->get(db_prefix() . 'files')->result_array();

    $logoUrl                   = '';
    if (count($logo) > 0) {
        $logoUrl = APP_MODULES_PATH . 'changee/uploads/po_logo/0/' . $logo[0]['file_name'];
        if ($type != 'pdf') {
            $logoUrl = base_url(PURCHASE_PATH . 'po_logo/0/' . $logo[0]['file_name']);
        }
    }

    $logoImage = '';
    if ($logoUrl != '') {
        $logoImage = '<img style="width:' . $width . 'px" src="' . $logoUrl . '" class="' . $class . '">';
    }


    return  $logoImage;
}

/**
 * { total inv value by pur order }
 *
 * @param        $pur_order  The pur order
 *
 * @return     int     ( description of the return value )
 */
function changee_total_inv_value_by_pur_order($pur_order)
{
    $CI           = &get_instance();
    $CI->db->where('pur_order', $pur_order);
    $list_inv = $CI->db->get(db_prefix() . 'pur_invoices')->result_array();
    $rs = 0;
    if (count($list_inv) > 0) {
        foreach ($list_inv as $inv) {
            $rs += $inv['total'];
        }
    }
    return $rs;
}

/**
 * Gets the item identifier by description.
 *
 * @param        $des       The description
 * @param        $long_des  The long description
 *
 * @return     string  The item identifier by description.
 */
function changee_get_item_id_by_des($des, $long_des = '')
{
    $CI           = &get_instance();
    $CI->db->where('description', $des);

    $item = $CI->db->get(db_prefix() . 'items')->row();

    if ($item) {
        return $item->id;
    }
    return '';
}

/**
 * { purorder inv left to pay }
 *
 * @param        $pur_order  The pur order
 */
function changee_purorder_inv_left_to_pay($pur_order)
{
    $CI           = &get_instance();
    $CI->load->model('changee/changee_model');
    $list_payment = $CI->changee_model->get_inv_payment_changee_order($pur_order);
    $po = $CI->changee_model->get_pur_order($pur_order);

    $list_applied_debit = $CI->changee_model->get_inv_debit_changee_order($pur_order);
    $paid = 0;
    foreach ($list_payment as $payment) {
        if ($payment['approval_status'] == 2) {
            $paid += $payment['amount'];
        }
    }

    foreach ($list_applied_debit as $debit) {
        $paid += $debit['amount'];
    }

    if ($po) {
        return $po->total - $paid;
    }
    return 0;
}

/**
 * { row purcahse options exist }
 *
 * @param      <type>   $name   The name
 *
 * @return     integer  ( 1 or 0 )
 */
function changee_row_changee_tbl_options_exist($name)
{
    $CI = &get_instance();
    $i = count($CI->db->query('Select * from ' . db_prefix() . 'options where name = ' . $name)->result_array());
    if ($i == 0) {
        return 0;
    }
    if ($i > 0) {
        return 1;
    }
}

/**
 * Gets the base currency pur.
 *
 * @return       The base currency pur.
 */
function changee_get_base_currency_pur()
{
    $CI           = &get_instance();
    $CI->load->model('currencies_model');
    $base_currency = $CI->currencies_model->get_base_currency();
    return $base_currency;
}

/**
 * Gets the arr vendors by pr.
 *
 * @param        $co_request  The pur request
 *
 * @return     array   The arr vendors by pr.
 */
function changee_get_arr_vendors_by_pr($co_request)
{
    $CI           = &get_instance();
    $CI->load->model('changee/changee_model');

    $CI->db->where('co_request', $co_request);
    $quotes = $CI->db->get(db_prefix() . 'co_estimates')->result_array();
    $arr_vendor = [];
    $arr_vendor_rs = [];
    if (count($quotes) > 0) {
        foreach ($quotes as $quote) {
            if (!in_array($quote['vendor'], $arr_vendor)) {
                $arr_vendor[] = $quote['vendor'];
                $arr_vendor_rs[] = $CI->changee_model->get_vendor($quote['vendor']);
            }
        }
    }
    return $arr_vendor_rs;
}

/**
 * Gets the quotations by pur request.
 */
function changee_get_quotations_by_co_request($co_request)
{
    $CI           = &get_instance();

    $CI->db->where('co_request', $co_request);
    $quotes = $CI->db->get(db_prefix() . 'co_estimates')->result_array();
    return $quotes;
}

/**
 * Gets the item detail in quote.
 *
 * @param        $item           The item
 * @param        $co_estimates  The pur estimates
 */
function changee_get_item_detail_in_quote($item, $co_estimates)
{
    $CI           = &get_instance();
    $CI->db->where('pur_estimate', $co_estimates);
    $CI->db->where('item_code', $item);
    $item_row = $CI->db->get(db_prefix() . 'co_estimate_detail')->row();
    return $item_row;
}

/**
 * Gets the changee request item taxes.
 *
 * @param       $itemid  The itemid
 *
 * @return       The invoice item taxes.
 */
function changee_get_debit_note_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'debit_note');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Function that format credit note number based on the prefix option and the credit note number
 * @param  mixed $id credit note id
 * @return string
 */
function changee_format_debit_note_number($id)
{
    $CI = &get_instance();
    $CI->db->select('date,number,prefix,number_format')
        ->from(db_prefix() . 'pur_debit_notes')
        ->where('id', $id);
    $debit_note = $CI->db->get()->row();

    if (!$debit_note) {
        return '';
    }

    $number = sales_number_format($debit_note->number, $debit_note->number_format, $debit_note->prefix, $debit_note->date);

    return $number;
}

/**
 * Format debit note status
 * @param  mixed  $status credit note current status
 * @param  boolean $text   to return text or with applied styles
 * @return string
 */
function changee_format_debit_note_status($status, $text = false)
{
    $CI = &get_instance();
    if (!class_exists('changee_model')) {
        $CI->load->model('changee/changee_model');
    }

    $statuses    = $CI->changee_model->get_debit_note_statuses();
    $statusArray = false;
    foreach ($statuses as $s) {
        if ($s['id'] == $status) {
            $statusArray = $s;

            break;
        }
    }

    if (!$statusArray) {
        return $status;
    }

    if ($text) {
        return $statusArray['name'];
    }

    $style = 'border: 1px solid ' . $statusArray['color'] . ';color:' . $statusArray['color'] . ';';
    $class = 'label s-status';

    return '<span class="' . $class . '" style="' . $style . '">' . $statusArray['name'] . '</span>';
}

/**
 * Format vendor address info
 * @param  object  $data        vendor object from database
 * @param  string  $for         where this format will be used? Eq statement invoice etc
 * @param  string  $type        billing/shipping
 * @param  boolean $companyLink company link to be added on vendor company/name, this is used in admin area only
 * @return string
 */
function changee_format_vendor_info($data, $for, $type, $companyLink = false)
{
    $format   = get_option('customer_info_format');
    $vendorId = '';

    if ($for == 'statement') {
        $vendorId = $data->userid;
    } elseif ($type == 'billing') {
        $vendorId = $data->vendorid;
    }

    $filterData = [
        'data'         => $data,
        'for'          => $for,
        'type'         => $type,
        'client_id'    => $vendorId,
        'company_link' => $companyLink,
    ];

    $companyName = '';
    if ($for == 'statement') {
        $companyName = changee_get_vendor_company_name($vendorId);
    } elseif ($type == 'billing') {
        $companyName = $data->vendor->company;
    }

    $acceptsPrimaryContactDisplay = ['debit_note'];

    $street  = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_street'} : '';
    $city    = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_city'} : '';
    $state   = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_state'} : '';
    $zipCode = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_zip'} : '';

    $countryCode = '';
    $countryName = '';

    if ($country = in_array($type, ['billing', 'shipping']) ? get_country($data->{$type . '_country'}) : '') {
        $countryCode = $country->iso2;
        $countryName = $country->short_name;
    }

    $phone = '';
    if ($for == 'statement' && isset($data->phonenumber)) {
        $phone = $data->phonenumber;
    } elseif ($type == 'billing' && isset($data->client->phonenumber)) {
        $phone = $data->client->phonenumber;
    }

    $vat = '';
    if ($for == 'statement' && isset($data->vat)) {
        $vat = $data->vat;
    } elseif ($type == 'billing' && isset($data->client->vat)) {
        $vat = $data->client->vat;
    }

    if ($companyLink && (!isset($data->deleted_customer_name) ||
        (isset($data->deleted_customer_name) &&
            empty($data->deleted_customer_name)))) {
        $companyName = '<a href="' . admin_url('changee/vendor/' . $vendorId) . '" target="_blank"><b>' . $companyName . '</b></a>';
    } elseif ($companyName != '') {
        $companyName = '<b>' . $companyName . '</b>';
    }

    $format = _info_format_replace('company_name', $companyName, $format);
    $format = _info_format_replace('customer_id', $vendorId, $format);
    $format = _info_format_replace('street', $street, $format);
    $format = _info_format_replace('city', $city, $format);
    $format = _info_format_replace('state', $state, $format);
    $format = _info_format_replace('zip_code', $zipCode, $format);
    $format = _info_format_replace('country_code', $countryCode, $format);
    $format = _info_format_replace('country_name', $countryName, $format);
    $format = _info_format_replace('phone', $phone, $format);
    $format = _info_format_replace('vat_number', $vat, $format);
    $format = _info_format_replace('vat_number_with_label', $vat == '' ? '' : _l('client_vat_number') . ': ' . $vat, $format);


    // Remove multiple white spaces
    $format = preg_replace('/\s+/', ' ', $format);
    $format = trim($format);

    return hooks()->apply_filters('customer_info_text', $format, $filterData);
}

/**
 * Prepare general debit note pdf
 * @param  object $debit_note Debit note as object with all necessary fields
 * @return mixed object
 */
function changee_debit_note_pdf($debit_note)
{
    return app_pdf('debit_note', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Debit_note_pdf'), $debit_note);
}

/**
 * Return debit note status color RGBA for pdf
 * @param  mixed $status_id current credit note status id
 * @return string
 */
function changee_debit_note_status_color_pdf($status_id)
{
    $statusColor = '';

    if ($status_id == 1) {
        $statusColor = '3, 169, 244';
    } elseif ($status_id == 2) {
        $statusColor = '132, 197, 41';
    } else {
        // Status VOID
        $statusColor = '119, 119, 119';
    }

    return $statusColor;
}

/**
 * Check if debit can be applied to invoice based on the invoice status
 * @param  mixed $status_id invoice status id
 * @return boolean
 */
function changee_debits_can_be_applied_to_invoice($status)
{
    if (in_array($status, ["unpaid", "partially_paid"])) {
        return true;
    }
    return false;
}

/**
 * Prepare customer statement pdf
 * @param  object $statement statement
 * @return mixed
 */
function changee_changee_statement_pdf($statement)
{
    return app_pdf('vendor_statement', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Vendor_statement_pdf'), $statement);
}

/**
 * changee get staff id hr permissions
 * @return [type] 
 */
function changee_changee_get_staff_id_permissions()
{
    $CI = &get_instance();
    $array_staff_id = [];
    $index = 0;

    $str_permissions = '';
    foreach (changee_list_changee_permisstion() as $per_key =>  $per_value) {
        if (strlen($str_permissions) > 0) {
            $str_permissions .= ",'" . $per_value . "'";
        } else {
            $str_permissions .= "'" . $per_value . "'";
        }
    }


    $sql_where = "SELECT distinct staff_id FROM " . db_prefix() . "staff_permissions
    where feature IN (" . $str_permissions . ")
    ";

    $staffs = $CI->db->query($sql_where)->result_array();

    if (count($staffs) > 0) {
        foreach ($staffs as $key => $value) {
            $array_staff_id[$index] = $value['staff_id'];
            $index++;
        }
    }
    return $array_staff_id;
}


/**
 * list changee permisstion
 * @return [type] 
 */
function changee_list_changee_permisstion()
{
    $hr_profile_permissions = [];
    $hr_profile_permissions[] = 'changee_items';
    $hr_profile_permissions[] = 'changee_vendors';
    $hr_profile_permissions[] = 'changee_vendor_items';
    $hr_profile_permissions[] = 'changee_request';
    $hr_profile_permissions[] = 'changee_quotations';
    $hr_profile_permissions[] = 'changee_orders';
    $hr_profile_permissions[] = 'changee_order_return';
    $hr_profile_permissions[] = 'changee_contracts';
    $hr_profile_permissions[] = 'changee_invoices';
    $hr_profile_permissions[] = 'changee_reports';
    $hr_profile_permissions[] = 'changee_debit_notes';
    $hr_profile_permissions[] = 'changee_settings';
    $hr_profile_permissions[] = 'changee_order_change_approve_status';
    $hr_profile_permissions[] = 'changee_estimate_change_approve_status';
    $hr_profile_permissions[] = 'changee_request_change_approve_status';


    return $hr_profile_permissions;
}

/**
 * changee get staff id dont permissions
 * @return [type] 
 */
function changee_changee_get_staff_id_dont_permissions()
{
    $CI = &get_instance();

    $CI->db->where('admin != ', 1);

    if (count(changee_changee_get_staff_id_permissions()) > 0) {
        $CI->db->where_not_in('staffid', changee_changee_get_staff_id_permissions());
    }
    return $CI->db->get(db_prefix() . 'staff')->result_array();
}

function changee_check_valid_number_with_setting($number)
{
    $decimal_separator = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');

    $decimal_separator_index = strpos($number, $decimal_separator);
    $thousand_separator_index = strpos($number, $thousand_separator);

    if ($decimal_separator_index == false || $thousand_separator_index == false) {
        return true;
    }

    if ($decimal_separator_index <= $thousand_separator_index) {
        return false;
    }

    return true;
}

function changee_pur_convert_item_taxes($tax, $tax_rate, $tax_name)
{
    /*taxrate taxname
    5.00    TAX5
    id      rate        name
    2|1 ; 6.00|10.00 ; TAX5|TAX10%*/
    $CI           = &get_instance();
    $taxes = [];
    if ($tax != null && strlen($tax) > 0) {
        $arr_tax_id = explode('|', $tax);
        if ($tax_name != null && strlen($tax_name) > 0) {
            $arr_tax_name = explode('|', $tax_name);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_name as $key => $value) {
                $taxes[]['taxname'] = $value . '|' .  $arr_tax_rate[$key];
            }
        } elseif ($tax_rate != null && strlen($tax_rate) > 0) {
            $CI->load->model('changee/changee_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->changee_model->get_tax_name($value);
                if (isset($arr_tax_rate[$key])) {
                    $taxes[]['taxname'] = $_tax_name . '|' .  $arr_tax_rate[$key];
                } else {
                    $taxes[]['taxname'] = $_tax_name . '|' .  $CI->changee_model->tax_rate_by_id($value);
                }
            }
        } else {
            $CI->load->model('changee/changee_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->changee_model->get_tax_name($value);
                $_tax_rate = $CI->changee_model->tax_rate_by_id($value);
                $taxes[]['taxname'] = $_tax_name . '|' .  $_tax_rate;
            }
        }
    }

    return $taxes;
}

/**
 * pur get unit name
 * @param  boolean $id 
 * @return [type]      
 */
function changee_pur_get_unit_name($id = false)
{
    $CI           = &get_instance();
    if (is_numeric($id)) {
        $CI->db->where('unit_type_id', $id);

        $unit = $CI->db->get(db_prefix() . 'ware_unit_type')->row();
        if ($unit) {
            return $unit->unit_name;
        }
        return '';
    }
}

/**
 * wh get item variatiom
 * @param  [type] $id 
 * @return [type]     
 */
function changee_pur_get_item_variatiom($id)
{
    $CI           = &get_instance();

    $CI->db->where('id', $id);
    $item_value = $CI->db->get(db_prefix() . 'items')->row();

    $name = '';
    if ($item_value) {
        $CI->load->model('changee/changee_model');
        $new_item_value = $CI->changee_model->row_item_to_variation($item_value);

        $name .= $item_value->commodity_code . '_' . $new_item_value->new_description;
    }

    return $name;
}

/**
 * Function that return invoice item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function changee_pur_get_invoice_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'invoice');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;


    return $taxes;
}

/**
 * { handle item password file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean
 */
function changee_handle_vendor_item_attachment($id)
{

    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/vendor_items/' . $id . '/';
    $CI = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)
    ) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name'] = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type'] = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error'] = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size'] = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['attachments']['error'][$i])
                    || !_upload_extension_allowed($_FILES['attachments']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $_FILES['attachments']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, 'vendor_items', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * { vendor item images }
 *
 * @param        $item_id  The item identifier
 */
function changee_vendor_item_images($item_id)
{
    $CI = &get_instance();

    $CI->db->order_by('dateadded', 'desc');
    $CI->db->where('rel_id', $item_id);
    $CI->db->where('rel_type', 'vendor_items');

    return $CI->db->get(db_prefix() . 'files')->result_array();
}

/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
function changee_pur_get_tax_rate($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'taxes')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tbltaxes')->result_array();
    }
}

/**
 * Changee get currency name symbol
 * @param  [type] $id     
 * @param  string $column 
 * @return [type]         
 */
function changee_pur_get_currency_name_symbol($id, $column = '')
{
    $CI   = &get_instance();
    $currency_value = '';

    if ($column == '') {
        $column = 'name';
    }

    $CI->db->select($column);
    $CI->db->from(db_prefix() . 'currencies');
    $CI->db->where('id', $id);
    $currency = $CI->db->get()->row();
    if ($currency) {
        $currency_value = $currency->$column;
    }

    return $currency_value;
}

/**
 * get currency rate
 * @param  [type] $from
 * @param  [type] $to
 * @return [type]           
 */
function changee_pur_get_currency_rate($from, $to)
{
    $CI   = &get_instance();
    if ($from == $to) {
        return 1;
    }

    $amount_after_convertion = 1;

    $CI->db->where('from_currency_name', strtoupper($from));
    $CI->db->where('to_currency_name', strtoupper($to));
    $currency_rates = $CI->db->get(db_prefix() . 'currency_rates')->row();

    if ($currency_rates) {
        $amount_after_convertion = $currency_rates->to_currency_rate;
    }

    return $amount_after_convertion;
}


/**
 * { pur get currency by id }
 *
 * @param        $id     The identifier
 */
function changee_pur_get_currency_by_id($id)
{
    $CI   = &get_instance();

    $CI->db->where('id', $id);
    return  $CI->db->get(db_prefix() . 'currencies')->row();
}

/**
 * Gets the vendor currency.
 *
 * @param        $vendor_id  The vendor identifier
 */
function changee_get_vendor_currency($vendor_id)
{
    $CI   = &get_instance();

    $CI->db->where('userid', $vendor_id);
    $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();

    if ($vendor) {
        return $vendor->default_currency;
    }
    return 0;
}

function changee_get_invoice_currency_id($invoice_id)
{
    $CI   = &get_instance();
    $CI->db->where('id', $invoice_id);
    $invoice = $CI->db->get(db_prefix() . 'pur_invoices')->row();
    if ($invoice) {
        return $invoice->currency;
    }
    return 0;
}

/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function changee_handle_vendor_po_attachments_upload($id, $customer_upload = false, $purorder = '')
{
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $purorder . '/';
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
                        $attachment[0]['contact_id']       = changee_get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($purorder, 'pur_order', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function changee_handle_vendor_estimate_attachments_upload($id, $customer_upload = false, $purorder = '')
{
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $purorder . '/';
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
                        $attachment[0]['contact_id']       = changee_get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($purorder, 'pur_estimate', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * wh check approval setting
 * @param  integer $type 
 * @return [type]       
 */
function changee_pur_check_approval_setting($type)
{
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    $check_appr = $CI->changee_model->get_approve_setting($type);

    return $check_appr;
}

/**
 * Gets the warehouse option.
 *
 * @param      <type>        $name   The name
 *
 * @return     array|string  The warehouse option.
 */
function changee_changee_get_changee_option_v2($name)
{
    $CI = &get_instance();
    $options = [];
    $val  = '';
    $name = trim($name);


    if (!isset($options[$name])) {
        // is not auto loaded
        $CI->db->select('value');
        $CI->db->where('name', $name);
        $row = $CI->db->get(db_prefix() . 'options')->row();
        if ($row) {
            $val = $row->value;
        }
    } else {
        $val = $options[$name];
    }

    return $val;
}

/**
 * get commodity name
 * @param  integer $id
 * @return array or row
 */
function changee_pur_get_commodity_name($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems')->result_array();
    }
}

/**
 * wh render taxes html
 * @param  [type] $item_tax 
 * @param  [type] $width    
 * @return [type]           
 */
function changee_pur_render_taxes_html($item_tax, $width)
{
    $itemHTML = '';
    $itemHTML .= '<td align="right" width="' . $width . '%">';

    if (is_array($item_tax) && isset($item_tax)) {
        if (count($item_tax) > 0) {
            foreach ($item_tax as $tax) {

                $item_tax = '';
                if (get_option('remove_tax_name_from_item_table') == false || multiple_taxes_found_for_item($item_tax)) {
                    $tmp      = explode('|', $tax['taxname']);
                    $item_tax = $tmp[0] . ' ' . app_format_number($tmp[1]) . '%<br />';
                } else {
                    $item_tax .= app_format_number($tax['taxrate']) . '%';
                }
                $itemHTML .= $item_tax;
            }
        } else {
            $itemHTML .=  app_format_number(0) . '%';
        }
    }
    $itemHTML .= '</td>';

    return $itemHTML;
}

/**
 * Function that format task status for the final user
 * @param  string  $id    status id
 * @param  boolean $text
 * @param  boolean $clean
 * @return string
 */
function changee_pur_format_approve_status($status, $text = false, $clean = false)
{

    $status_name = '';
    if ($status == 1) {
        $status_name = _l('changee_draft');
    } elseif ($status == 2) {
        $status_name = _l('changee_approved');
    } elseif ($status == 3) {
        $status_name = _l('pur_rejected');
    } elseif ($status == 4) {
        $status_name = _l('pur_canceled');
    }

    if ($clean == true) {
        return $status_name;
    }

    $style = '';
    $class = '';
    if ($text == false) {
        if ($status == 1) {
            $class = 'label label-primary';
        } elseif ($status == 2) {
            $class = 'label label-success';
        } elseif ($status == 3) {
            $class = 'label label-warning';
        } elseif ($status == 4) {
            $class = 'label label-danger';
        }
    } else {
        if ($status == 1) {
            $class = 'label text-info';
        } elseif ($status == 2) {
            $class = 'label text-success';
        } elseif ($status == 3) {
            $class = 'label text-warning';
        } elseif ($status == 4) {
            $class = 'label text-danger';
        }
    }

    return '<span class="' . $class . '" >' . $status_name . '</span>';
}


/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function changee_handle_vendor_pr_attachments_upload($id, $customer_upload = false, $purorder = '')
{
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/co_request/' . $purorder . '/';
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
                        $attachment[0]['contact_id']       = changee_get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($purorder, 'co_request', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Gets the total order return refunded.
 *
 * @param        $order_return  The order return
 *
 * @return     int     The total order return refunded.
 */
function changee_get_total_order_return_refunded($order_return)
{
    $CI            = &get_instance();
    $CI->db->where('order_return_id', $order_return);
    $refunds = $CI->db->get(db_prefix() . 'wh_order_returns_refunds')->result_array();

    $total_refunded = 0;
    if (count($refunds) > 0) {
        foreach ($refunds as $key => $refund) {
            $total_refunded += $refund['amount'];
        }
    }

    return $total_refunded;
}

function changee_get_order_return_remaining_refund($order_return)
{
    $CI            = &get_instance();
    $CI->load->model('changee/changee_model');

    $order = $CI->changee_model->get_order_return($order_return);

    $vendor = $CI->changee_model->get_vendor($order->company_id);

    $remaining_refund = 0;
    $total_refunded = changee_get_total_order_return_refunded($order_return);

    $remaining_refund = $order->total_after_discount - $total_refunded;

    return $remaining_refund;
}

function changee_get_object_comment($rel_id, $rel_type)
{
    $CI            = &get_instance();
    $table = '';
    if ($rel_type == 'pur_order') {
        $table = db_prefix() . 'co_orders';
    } else if ($rel_type == 'pur_quotation') {
        $table = db_prefix() . 'co_estimates';
    } else if ($rel_type == 'pur_contract') {
        $table = db_prefix() . 'pur_contracts';
    } else if ($rel_type == 'pur_invoice') {
        $table = db_prefix() . 'pur_invoices';
    }

    $CI->db->where('id', $rel_id);
    $object = $CI->db->get($table)->row();

    return $object;
}

/**
 * Get primary contact user id for specific customer
 * @param  mixed $userid
 * @return mixed
 */
function changee_pur_get_primary_contact_user_id($userid)
{
    $CI = &get_instance();
    $CI->db->where('userid', $userid);
    $CI->db->where('is_primary', 1);
    $row = $CI->db->get(db_prefix() . 'pur_contacts')->row();

    if ($row) {
        return $row->id;
    }

    return false;
}

/**
 * Gets the vendor language by email.
 */
function changee_changee_get_vendor_language_by_email($email)
{
    $CI = &get_instance();
    $CI->db->where('email', $email);
    $contact = $CI->db->get(db_prefix() . 'pur_contacts')->row();

    if ($contact) {
        $CI->db->where('userid', $contact->userid);
        $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();
        if ($vendor) {
            return $vendor->default_language;
        }
    }

    return '';
}

/**
 * Send contract signed notification to staff members
 *
 * @param  int $contract_id
 *
 * @return void
 */
function changee_pur_send_contract_signed_notification_to_staff($contract_id)
{
    $CI = &get_instance();
    $CI->db->where('id', $contract_id);
    $contract = $CI->db->get(db_prefix() . 'pur_contracts')->row();

    if (!$contract) {
        return false;
    }

    // Get creator
    $CI->db->select('staffid, email');
    $CI->db->where('staffid', $contract->add_from);
    $staff_contract = $CI->db->get(db_prefix() . 'staff')->result_array();

    $notifiedUsers = [];

    foreach ($staff_contract as $member) {
        $notified = add_notification([
            'description'     => 'not_contract_signed',
            'touserid'        => $member['staffid'],
            'fromcompany'     => 1,
            'fromuserid'      => 0,
            'link'            => 'changee/contract/' . $contract->id,
            'additional_data' => serialize([
                '<b>' . $contract->contract_number . '</b>',
            ]),
        ]);

        if ($notified) {
            array_push($notifiedUsers, $member['staffid']);
        }
    }

    pusher_trigger_notification($notifiedUsers);
}

/**
 * Gets the vendor language.
 */
function changee_get_vendor_language()
{
    $vendor_id = changee_get_vendor_user_id();
    $CI = &get_instance();
    $CI->db->where('userid', $vendor_id);
    $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();

    if ($vendor) {
        return $vendor->default_language;
    }

    return '';
}

/**
 * { pur_changee_pur_html_entity_decode }
 *
 * @param      string  $str    The string
 *
 * @return     <string>  
 */
function changee_pur_html_entity_decode($str)
{
    return html_entity_decode($str ?? '');
}

/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function changee_pur_render_date_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
    }
    $input .= '<div class="input-group date">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-regular fa-calendar calendar-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}

/**
 * Gets the vendor admin list.
 *
 * @param        $staffid  The staffid
 */
function changee_get_vendor_admin_list($staffid)
{

    $CI = &get_instance();
    $CI->db->where('staff_id', $staffid);
    $list = $CI->db->get(db_prefix() . 'pur_vendor_admin')->result_array();

    $vendor_ids = [];
    if (count($list) > 0) {
        foreach ($list as $row) {
            if (!in_array($row['vendor_id'], $vendor_ids)) {
                $vendor_ids[] = $row['vendor_id'];
            }
        }
    }

    return $vendor_ids;
}

/**
 * { changee_pur_check_csrf_protection }
 *
 * @return     string  (  )
 */
function changee_pur_check_csrf_protection()
{
    if (config_item('csrf_protection')) {
        return 'true';
    }
    return 'false';
}

function changee_format_po_ship_to_info($pur_order)
{
    $html = '';
    $CI = &get_instance();

    if (!empty($pur_order->shipping_address) || !empty($pur_order->shipping_city) || !empty($pur_order->shipping_state) || !empty($pur_order->shipping_zip)) {
        $html .= '<b>' . _l('ship_to') . '</b>';
        if (!empty($pur_order->shipping_address)) {
            $html .= '<br />' . $pur_order->shipping_address;
        }
        if (!empty($pur_order->shipping_city) || !empty($pur_order->shipping_state)) {
            $html .= '<br />';
            if (!empty($pur_order->shipping_city)) {
                $html .= $pur_order->shipping_city . " ";
            }
            if (!empty($pur_order->shipping_state)) {
                $html .= $pur_order->shipping_state;
            }
        }
        if (!empty($pur_order->shipping_country) || !empty($pur_order->shipping_zip)) {
            $html .= '<br />';
            if (!empty($pur_order->shipping_country)) {
                $shipping_country = $CI->db->select('short_name')
                    ->where('country_id', $pur_order->shipping_country)
                    ->from(db_prefix() . 'countries')
                    ->get()
                    ->row();
                $html .= $shipping_country->short_name . " ";
            }
            if (!empty($pur_order->shipping_zip)) {
                $html .= $pur_order->shipping_zip;
            }
        }
    }

    return $html;
}

function changee_format_pdf_vendor_info($vendor_id)
{
    $html = '';
    $CI = &get_instance();

    if (!empty($vendor_id)) {
        $vendor = $CI->db->select('*')
            ->where('userid', $vendor_id)
            ->from(db_prefix() . 'pur_vendor')
            ->get()
            ->row();

        if (!empty($vendor)) {
            $html .= '<b>' . _l('vendor') . '</b>';
            $html .= '<br /><b>' . $vendor->company . '</b>';
            if (!empty($vendor->address)) {
                $html .= '<br />' . $vendor->address;
            }
            if (!empty($vendor->city) || !empty($vendor->state)) {
                $html .= '<br />';
                if (!empty($vendor->city)) {
                    $html .= $vendor->city . " ";
                }
                if (!empty($vendor->state)) {
                    $html .= $vendor->state;
                }
            }
            if (!empty($vendor->country) || !empty($vendor->zip)) {
                $html .= '<br />';
                if (!empty($vendor->country)) {
                    $country = $CI->db->select('short_name')
                        ->where('country_id', $vendor->country)
                        ->from(db_prefix() . 'countries')
                        ->get()
                        ->row();
                    $html .= $country->short_name . " ";
                }
                if (!empty($vendor->zip)) {
                    $html .= $vendor->zip;
                }
            }
            if (!empty($vendor->vat)) {
                $html .= '<br />' . _l('company_vat_number') . ': ' . $vendor->vat;
            }
            if (!empty($vendor->phonenumber)) {
                $html .= '<br />' . $vendor->phonenumber;
            }
        }

        return $html;
    }
}
function get_po_order($id = false)
{
    $CI = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix() . 'pur_orders')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblpur_orders where approve_status = 2')->result_array();
    }
}
function get_wo_order($id = false)
{
    $CI = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix() . 'wo_orders')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblwo_orders where approve_status = 2')->result_array();
    }
}


function get_status_modules_co($module_name)
{
    $CI             = &get_instance();

    $sql = 'select * from ' . db_prefix() . 'modules where module_name = "' . $module_name . '" AND active =1 ';
    $module = $CI->db->query($sql)->row();
    if ($module) {
        return true;
    } else {
        return false;
    }
}

function get_pur_order_name_by_id($po_order)
{
    if (!empty($po_order)) {

        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from(db_prefix() . 'pur_orders');
        $CI->db->where('id', $po_order);
        $result = $CI->db->get()->row_array();

        if (!empty($result)) {
            return $result['pur_order_number'] . '-' . $result['pur_order_name'];
        }
    }
    return '';
}
function get_wo_order_name_by_id($wo_order)
{
    if (!empty($wo_order)) {

        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from(db_prefix() . 'wo_orders');
        $CI->db->where('id', $wo_order);
        $result = $CI->db->get()->row_array();

        if (!empty($result)) {
            return $result['wo_order_number'] . ' - ' . $result['wo_order_name'];
        }
    }
    return '';
}
function get_pur_name_by_id($pur_id)
{
    if (!empty($pur_id)) {

        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from(db_prefix() . 'pur_orders');
        $CI->db->where('id', $pur_id);
        $result = $CI->db->get()->row_array();

        if (!empty($result)) {
            return $result['pur_order_number'] . ' - ' . $result['pur_order_name'];
        }
    }
    return '';
}

function check_any_order_exitsin_po($id, $type)
{
    if (!empty($id)) {

        if ($type == 'pur_order') {
            $CI = &get_instance();
            $CI->db->select('*');
            $CI->db->from(db_prefix() . 'co_orders');
            $CI->db->where('po_order_id', $id);
            $CI->db->order_by('id', 'DESC');
            $result = $CI->db->get()->result_array();
            
        }elseif ($type == 'wo_order') {
            $CI = &get_instance();
            $CI->db->select('*');
            $CI->db->from(db_prefix() . 'co_orders');
            $CI->db->where('wo_order_id', $id);
            $CI->db->order_by('id', 'DESC');
            $result = $CI->db->get()->result_array();
        }

        if (!empty($result)) {
            return $result;
        }
    }
    return false;
}

function get_po_co_sum_values($id)
{
    $CI = &get_instance();
    $CI->db->select_sum('co_value');
    $CI->db->from(db_prefix() . 'co_orders');
    $CI->db->where('po_order_id', $id);
    $result = $CI->db->get()->row();
    return $result;
}

function get_wo_co_sum_values($id)
{
    $CI = &get_instance();
    $CI->db->select_sum('co_value');
    $CI->db->from(db_prefix() . 'co_orders');
    $CI->db->where('wo_order_id', $id);
    $result = $CI->db->get()->row();
    return $result; 
}

function get_area_list_changee($name_area, $area)
{
    
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    $get_area = get_area_project_wise();

    $selected = !empty($area) ? $area : array();
    if (!is_array($selected)) {
        $selected = explode(",", $selected);
    }
    return render_select($name_area, $get_area, array('id', 'area_name'), '', $selected, array('multiple' => true), array('id' => 'project_area'), '', '', false);
}

function add_co_activity_log($id)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
        if(!empty($co_orders)) {
            $description = "Change order <b>".$co_orders->pur_order_number."</b> has been created.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'co',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function remove_co_activity_log($id)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
        if(!empty($co_orders)) {
            $description = "Change order <b>".$co_orders->pur_order_number."</b> has been deleted.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'co',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_co_attachment_activity_log($id, $file_name, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
        if(!empty($co_orders)) {
            $is_create_value = $is_create ? 'added' : 'removed';
            $description = "Attachment <b>".$file_name."</b> has been ".$is_create_value." for change order <b>".$co_orders->pur_order_number."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'co',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_co_comments_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_comments = $CI->db->get(db_prefix() . 'co_comments')->row();
        if(!empty($co_comments)) {
            $module_name = '';
            $rel_id = '';
            $description = '';
            $is_create_value = $is_create ? 'added' : 'removed';
            if($co_comments->rel_type == 'pur_order') {
                $CI->db->where('id', $co_comments->rel_id);
                $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
                $description = "Discuss <b>".$co_comments->content."</b> has been ".$is_create_value." for change order <b>".$co_orders->pur_order_number."</b>.";
                $module_name = 'co';
                $rel_id = $co_orders->id;
            }
            if(!empty($description)) {
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => $module_name,
                    'rel_id' => $rel_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function update_co_comments_activity_log($id, $old_value, $new_value)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_comments = $CI->db->get(db_prefix() . 'co_comments')->row();
        if(!empty($co_comments)) {
            $module_name = '';
            $rel_id = '';
            $description = '';
            $old_value = !empty($old_value) ? $old_value : 'None';
            $new_value = !empty($new_value) ? $new_value : 'None';
            if($co_comments->rel_type == 'pur_order') {
                $CI->db->where('id', $co_comments->rel_id);
                $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
                $description = "Discuss field is updated from <b>".$old_value."</b> to <b>".$new_value."</b> in change order <b>".$co_orders->pur_order_number."</b>.";
                $module_name = 'co';
                $rel_id = $co_orders->id;
            }
            if(!empty($description)) {
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => $module_name,
                    'rel_id' => $rel_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function add_co_status_activity_log($id)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_approval_details = $CI->db->get(db_prefix() . 'co_approval_details')->row();
        if(!empty($co_approval_details)) {
            $module_name = '';
            $rel_id = '';
            $description = '';
            if($co_approval_details->rel_type == 'pur_order') {
                $CI->db->where('id', $co_approval_details->rel_id);
                $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
                if(empty($co_approval_details->approve)) {
                    $description = "An approval request has been created for change order <b>".$co_orders->pur_order_number."</b> by <b>".get_last_action_full_name($co_approval_details->sender)."</b>.";
                } else if($co_approval_details->approve == 2) {
                    $description = "Change order <b>".$co_orders->pur_order_number."</b> has been approved by <b>".get_last_action_full_name($co_approval_details->staff_approve)."</b>.";
                } else if($co_approval_details->approve == 3) {
                    $description = "Change order <b>".$co_orders->pur_order_number."</b> has been rejected by <b>".get_last_action_full_name($co_approval_details->staff_approve)."</b>.";
                }
                $module_name = 'co';
                $rel_id = $co_orders->id;
            }
            if(!empty($description)) {
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => $module_name,
                    'rel_id' => $rel_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function update_co_approval_status_activity_log($id, $to_status, $rel_type)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        if($to_status == 1) {
            $to_status_name = _l('purchase_draft');
        } else if($to_status == 2) {
            $to_status_name = _l('purchase_approved');
        } else if($to_status == 3) {
            $to_status_name = _l('pur_rejected');
        } else {
            $to_status_name = _l('purchase_draft');
        }
        if($rel_type == 'pur_order') {
            $CI->db->where('id', $id);
            $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
            $from_status = $co_orders->approve_status;
            if($from_status == 1) {
                $from_status_name = _l('purchase_draft');
            } else if($from_status == 2) {
                $from_status_name = _l('purchase_approved');
            } else if($from_status == 3) {
                $from_status_name = _l('pur_rejected');
            } else {
                $from_status_name = _l('purchase_draft');
            }
            $description = "An approval status is updated from <b>".$from_status_name."</b> to <b>".$to_status_name."</b> in change order <b>".$co_orders->pur_order_number."</b>.";
            $module_name = 'co';
            $rel_id = $co_orders->id;
        }
        if(!empty($description)) {
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => $module_name,
                'rel_id' => $rel_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function update_all_co_fields_activity_log($id, $new_data)
{
    $CI = &get_instance();
    $CI->load->model('currencies_model');
    $CI->load->model('staff_model');
    $CI->load->model('departments_model');
    $base_currency = $CI->currencies_model->get_base_currency();
    if (empty($id)) {
        return false;
    }
    $co_orders = $CI->db->where('id', $id)
        ->get(db_prefix() . 'co_orders')
        ->row();
    if (!$co_orders) {
        return false;
    }
    $old_data = (array)$co_orders;
    $normalize = function ($value) {
        $value = trim((string)$value);
        if (in_array(strtolower($value), ['null', 'none', 'nil', 'n/a', '-', '--'])) {
            return '';
        }
        if ($value === '0000-00-00') {
            return '';
        }
        if (is_numeric($value)) {
            $num = (float)$value;
            return ($num == 0.0) ? '' : $num;
        }
        return strtolower($value);
    };
    $norm_old = array_map($normalize, $old_data);
    $norm_new = array_map($normalize, $new_data);
    $changes = array_diff_assoc($norm_new, $norm_old);
    if (empty($changes)) {
        return true;
    }
    $field_map = [
        'po_order_id' => _l('purchase_order'),
        'wo_order_id' => _l('wo_order'),
        'order_date' => _l('order_date'),
        'pur_order_name' => _l('co_order_description'),
        'buyer' => _l('buyer'),
        'discount_type' => _l('discount_type'),
        'vendor' => _l('vendor'),
        'group_pur' => _l('Budget Head'),
        'sub_groups_pur' => _l('Budget Sub Head'),
        'estimate' => _l('budget'),
        'department' => _l('department'),
        'kind' => _l('kind'),
        'type' => _l('type'),
        'shipping_address' => _l('pur_company_address'),
        'shipping_zip' => _l('pur_company_zipcode'),
        'shipping_city' => _l('pur_company_city'),
        'shipping_state' => _l('pur_company_state'),
        'shipping_country_text' => _l('pur_company_country_text'),
        'shipping_country' => _l('pur_company_country_code'),
    ];
    foreach ($changes as $field => $dummy) {
        if (!isset($field_map[$field])) {
            continue;
        }
        $old_value = $old_data[$field] ?? '';
        $new_value = $new_data[$field] ?? '';
        if ($field === 'po_order_id') {
            if(!empty($old_value)) {
                $old_value_query = $CI->db->select('concat(pur_order_number, " - ", pur_order_name) as name')
                ->where('id', $old_value)
                ->from(db_prefix() . 'pur_orders')
                ->get()
                ->row();
                $old_value = !empty($old_value_query) ? $old_value_query->name : '';
            }
            if(!empty($new_value)) {
                $new_value_query = $CI->db->select('concat(pur_order_number, " - ", pur_order_name) as name')
                ->where('id', $new_value)
                ->from(db_prefix() . 'pur_orders')
                ->get()
                ->row();
                $new_value = !empty($new_value_query) ? $new_value_query->name : '';
            }
        }
        if ($field === 'wo_order_id') {
            if(!empty($old_value)) {
                $old_value_query = $CI->db->select('concat(wo_order_number, " - ", wo_order_name) as name')
                ->where('id', $old_value)
                ->from(db_prefix() . 'wo_orders')
                ->get()
                ->row();
                $old_value = !empty($old_value_query) ? $old_value_query->name : '';
            }
            if(!empty($new_value)) {
                $new_value_query = $CI->db->select('concat(wo_order_number, " - ", wo_order_name) as name')
                ->where('id', $new_value)
                ->from(db_prefix() . 'wo_orders')
                ->get()
                ->row();
                $new_value = !empty($new_value_query) ? $new_value_query->name : '';
            }
        }
        if ($field === 'buyer') {
            $staff_list = $CI->staff_model->get('', ['active' => 1]);
            $opts = array_combine(
                array_column($staff_list, 'staffid'),
                array_map(fn($a) => $a['firstname'] . ' ' . $a['lastname'], $staff_list)
            );
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'discount_type') {
            $opts = [
                'before_tax' => _l('discount_type_before_tax'),
                'after_tax' => _l('discount_type_after_tax'),
            ];
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'vendor') {
            $old_value = !empty($old_value) ? get_vendor_company_name($old_value) : '';
            $new_value = !empty($new_value) ? get_vendor_company_name($new_value) : '';
        }
        if ($field === 'group_pur') {
            $old_value = !empty($old_value) ? get_group_name_by_id($old_value) : '';
            $new_value = !empty($new_value) ? get_group_name_by_id($new_value) : '';
        }
        if ($field === 'sub_groups_pur') {
            $old_value = !empty($old_value) ? get_sub_head_name_by_id($old_value) : '';
            $new_value = !empty($new_value) ? get_sub_head_name_by_id($new_value) : '';
        }
        if ($field === 'estimate') {
            $old_value = !empty($old_value) ? format_estimate_number($old_value) : '';
            $new_value = !empty($new_value) ? format_estimate_number($new_value) : '';
        }
        if ($field === 'department') {
            $departments_list = $CI->departments_model->get();
            $opts = array_column($departments_list, 'name', 'departmentid');
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'kind') {
            $opts = [
                'Client Supply' => _l('client_supply'),
                'Bought out items' => _l('bought_out_items'),
            ];
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'type') {
            $opts = [
                'capex' => _l('capex'),
                'opex' => _l('opex'),
            ];
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'shipping_country') {
            $countries = get_all_countries();
            $opts = array_column($countries, 'short_name', 'country_id');
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        update_co_activity_log($id, $field_map[$field], $old_value, $new_value);
    }
    return true;
}

function update_co_activity_log($id, $field, $old_value, $new_value)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
        if(!empty($co_orders)) {
            $old_value = !empty($old_value) ? $old_value : 'None';
            $new_value = !empty($new_value) ? $new_value : 'None';
            $description = "".$field." field is updated from <b>".$old_value."</b> to <b>".$new_value."</b> in change order <b>".$co_orders->pur_order_number."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'co',
                'rel_id' => $co_orders->id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_co_item_activity_log($id, $rel_type, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $module_name = '';
        $rel_id = '';
        $description = '';
        $is_create_value = $is_create ? 'added' : 'removed';
        if($rel_type == 'pur_order') {
            $CI->db->where('id', $id);
            $co_order_detail = $CI->db->get(db_prefix() . 'co_order_detail')->row();
            if(!empty($co_order_detail)) {
                $CI->db->where('id', $co_order_detail->pur_order);
                $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
                $CI->db->where('id', $co_order_detail->item_code);
                $items = $CI->db->get(db_prefix() . 'items')->row();
                if(!empty($items)) {
                    $description = "Item <b>".$items->commodity_code." ".$items->description."</b> has been ".$is_create_value." for change order <b>".$co_orders->pur_order_number."</b>.";
                    $module_name = 'co';
                    $rel_id = $co_orders->id;
                }
            }
        }
        if(!empty($description)) {
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => $module_name,
                'rel_id' => $rel_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function update_co_item_activity_log($new_data, $rel_type)
{
    $CI = &get_instance();
    $CI->load->model('currencies_model');
    $base_currency = $CI->currencies_model->get_base_currency();
    $default_project = get_default_project();
    $old_data = array();
    if($rel_type == 'pur_order') {
        $co_order_detail = $CI->db->where('id', $new_data['id'])
            ->get(db_prefix() . 'co_order_detail')
            ->row();
        if (!$co_order_detail) {
            return false;
        }
        $old_data = (array) $co_order_detail;
    }
    if (isset($old_data['area'])) {
        $areaArray = is_array($old_data['area']) ? $old_data['area'] : explode(',', $old_data['area']);
        $areaArray = array_map('trim', $areaArray);
        $areaArray = array_filter($areaArray, fn($v) => $v !== '');
        sort($areaArray, SORT_NUMERIC);
        $old_data['area'] = implode(',', $areaArray);
    }
    if (isset($old_data['tax_name'])) {
        $names = is_array($old_data['tax_name']) ? $old_data['tax_name'] : explode('|', $old_data['tax_name']);
        $old_data['tax_select'] = implode(',', $names);
    }
    if (isset($old_data['unit_id'])) {
        $old_data['unit_name'] = $old_data['unit_id'];
    }
    if (isset($new_data['area']) && is_array($new_data['area'])) {
        $areaArray = array_map('trim', $new_data['area']);
        $areaArray = array_filter($areaArray, fn($v) => $v !== '');
        sort($areaArray, SORT_NUMERIC);
        $new_data['area'] = implode(',', $areaArray);
    }
    if (isset($new_data['tax_select']) && is_array($new_data['tax_select'])) {
        $new_data['tax_select'] = implode(',', array_map(fn($v) => strtok($v, '|'), $new_data['tax_select']));
    }
    if (isset($new_data['item_description'])) {
        $new_data['description'] = $new_data['item_description'];
        unset($new_data['item_description']);
    }
    $normalize = function ($value) {
        $value = trim((string)$value);
        if (in_array(strtolower($value), ['null', 'none', 'nil', 'n/a', '-', '--'])) {
            return '';
        }
        if ($value === '0000-00-00') {
            return '';
        }
        if (is_numeric($value)) {
            $num = (float)$value;
            return ($num == 0.0) ? '' : $num;
        }
        return strtolower($value);
    };
    $norm_old = array_map($normalize, $old_data);
    $norm_new = array_map($normalize, $new_data);
    $changes = array_diff_assoc($norm_new, $norm_old);
    if (empty($changes)) {
        return true;
    }
    $field_map = [
        'serial_no' => _l('serial_no'),
        'item_code' => _l('Uniclass Code'),
        'area' => _l('area'),
        'original_unit_price' => _l('awarded_rate'),
        'unit_price' => _l('rate_after_incl_co'),
        'original_quantity' => _l('awarded_qty'),
        'quantity' => _l('qty_after_incl_co'),
        'unit_name' => _l('pur_unit'),
        'into_money' => _l('contract_value'),
        'into_money_updated' => _l('updated_subtotal_before_tax'),
        'tax_select' => _l('debit_note_table_tax_heading'),
        'total' => _l('debit_note_total'),
        'variation' => _l('variation'),
    ];
    foreach ($changes as $field => $dummy) {
        if (!isset($field_map[$field])) {
            continue;
        }
        $old_value = $old_data[$field] ?? '';
        $new_value = $new_data[$field] ?? '';
        if ($field === 'item_code') {
            $old_value = !empty($old_value) ? pur_get_item_variatiom($old_value) : '';
            $new_value = !empty($new_value) ? pur_get_item_variatiom($new_value) : '';
        }
        if ($field === 'area') {
            $old_value = !empty($old_value) ? get_area_name_by_id($old_value) : '';
            $new_value = !empty($new_value) ? get_area_name_by_id($new_value) : '';
        }
        if ($field === 'unit_name') {
            $old_value = !empty($old_value) ? pur_get_unit_name($old_value) : '';
            $new_value = !empty($new_value) ? pur_get_unit_name($new_value) : '';
        }
        if ($field === 'original_unit_price' || $field === 'unit_price' || $field === 'into_money' || $field === 'into_money_updated' || $field === 'total' || $field === 'variation') {
            $old_value = app_format_money($old_value, $base_currency->symbol);
            $new_value = app_format_money($new_value, $base_currency->symbol);
        }
        $module_name = '';
        $rel_id = '';
        $description = '';
        if($rel_type == 'pur_order') {
            $CI->db->where('id', $old_data['pur_order']);
            $co_orders = $CI->db->get(db_prefix() . 'co_orders')->row();
            $CI->db->where('id', $new_data['item_code']);
            $items = $CI->db->get(db_prefix() . 'items')->row();
            $description = "".$field_map[$field]." field is updated from <b>".$old_value."</b> to <b>".$new_value."</b> for item <b>".$items->commodity_code." ".$items->description."</b> in change order <b>".$co_orders->pur_order_number."</b>.";
            $module_name = 'co';
            $rel_id = $co_orders->id;
        }
        if(!empty($description)) {
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => $module_name,
                'rel_id' => $rel_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}
