<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Render admin forms table
 * @param string  $name        table name
 * @param boolean $bulk_action include checkboxes on the left side for bulk actions
 */
function AdminFormsTableStructure($name = '', $bulk_action = false)
{
    $table = '<table class="table customizable-table number-index-' . ($bulk_action ? '2' : '1') . ' dt-table-loading ' . ($name == '' ? 'forms-table' : $name) . ' table-forms" id="forms" data-last-order-identifier="forms" data-default-order="' . get_table_last_order('forms') . '">';
    $table .= '<thead>';
    $table .= '<tr>';

    $table .= '<th class="' . ($bulk_action == true ? '' : 'not_visible') . '">';
    $table .= '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="forms"><label></label></div>';
    $table .= '</th>';

    $table .= '<th class="toggleable" id="th-number">' . _l('the_number_sign') . '</th>';
    $table .= '<th class="toggleable" id="th-subject">' . _l('form_dt_subject') . '</th>';
    $table .= '<th class="toggleable" id="th-tags">' . _l('tags') . '</th>';
    $table .= '<th class="toggleable" id="th-department">' . _l('form_dt_department') . '</th>';
    $services_th_attrs = '';
    if (get_option('services') == 0) {
        $services_th_attrs = ' class="not_visible"';
    }
    $table .= '<th' . $services_th_attrs . '>' . _l('form_dt_service') . '</th>';
    $table .= '<th class="toggleable" id="th-submitter">' . _l('form_dt_submitter') . '</th>';
    $table .= '<th class="toggleable" id="th-status">' . _l('form_dt_status') . '</th>';
    $table .= '<th class="toggleable" id="th-priority">' . _l('form_dt_priority') . '</th>';
    $table .= '<th class="toggleable" id="th-last-reply">' . _l('form_dt_last_reply') . '</th>';
    $table .= '<th class="toggleable form_created_column" id="th-created">' . _l('form_date_created') . '</th>';
    $table .= '<th class="toggleable ticket_options" id="th-options">' . _l('options') . '</th>';
    $custom_fields = get_table_custom_fields('forms');

    foreach ($custom_fields as $field) {
        $table .= '<th>' . $field['name'] . '</th>';
    }

    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table>';

    $table .= '<script id="hidden-columns-table-forms" type="text/json">';
    $table .= get_staff_meta(get_staff_user_id(), 'hidden-columns-table-forms');
    $table .= '</script>';

    return $table;
}

/**
 * Function to translate form status
 * The app offers ability to translate form status no matter if they are stored in database
 * @param  mixed $id
 * @return string
 */
function form_status_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }

    $line = _l('form_status_db_' . $id, '', false);

    if ($line == 'db_translate_not_found') {
        $CI = &get_instance();
        $CI->db->where('formstatusid', $id);
        $status = $CI->db->get(db_prefix() . 'forms_status')->row();

        return !$status ? '' : $status->name;
    }

    return $line;
}

/**
 * Function to translate form priority
 * The apps offers ability to translate form priority no matter if they are stored in database
 * @param  mixed $id
 * @return string
 */
function form_priority_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }

    $line = _l('form_priority_db_' . $id, '', false);

    if ($line == 'db_translate_not_found') {
        $CI = &get_instance();
        $CI->db->where('priorityid', $id);
        $priority = $CI->db->get(db_prefix() . 'forms_priorities')->row();

        return !$priority ? '' : $priority->name;
    }

    return $line;
}

/**
 * When form will be opened automatically set to open
 * @param integer  $current Current status
 * @param integer  $id      formid
 * @param boolean $admin   Admin opened or client opened
 */
function set_form_open($current, $id, $admin = true)
{
    if ($current == 1) {
        return;
    }

    $field = ($admin == false ? 'clientread' : 'adminread');

    $CI = &get_instance();
    $CI->db->where('formid', $id);

    $CI->db->update(db_prefix() . 'forms', [
        $field => 1,
    ]);
}

/**
 * Check whether to show form submitter on clients area table based on applied settings and contact
 * @since  2.3.2
 * @return boolean
 */
function show_form_submitter_on_clients_area_table()
{
    $show_submitter_on_table = true;
    if (!can_logged_in_contact_view_all_forms()) {
        $show_submitter_on_table = false;
    }

    return hooks()->apply_filters('show_form_submitter_on_clients_area_table', $show_submitter_on_table);
}

/**
 * Check whether the logged in contact can view all forms in customers area
 * @since  2.3.2
 * @return boolean
 */
function can_logged_in_contact_view_all_forms()
{
    return !(!is_primary_contact() && get_option('only_show_contact_forms') == 1);
}

/**
 * Get clients area form summary statuses data
 * @since  2.3.2
 * @param  array $statuses  current statuses
 * @return array
 */
function get_clients_area_forms_summary($statuses)
{
    foreach ($statuses as $key => $status) {
        $where = ['userid' => get_client_user_id(), 'status' => $status['formstatusid']];
        if (!can_logged_in_contact_view_all_forms()) {
            $where[db_prefix() . 'forms.contactid'] = get_contact_user_id();
        }
        $statuses[$key]['total_forms']   = total_rows(db_prefix() . 'forms', $where);
        $statuses[$key]['translated_name'] = form_status_translate($status['formstatusid']);
        $statuses[$key]['url']             = site_url('clients/forms/' . $status['formstatusid']);
    }

    return hooks()->apply_filters('clients_area_forms_summary', $statuses);
}

/**
 * Check whether contact can change the form status (single form) in clients area
 * @since  2.3.2
 * @param  mixed $status  the status id, if not passed, will first check from settings
 * @return boolean
 */
function can_change_form_status_in_clients_area($status = null)
{
    $option = get_option('allow_customer_to_change_form_status');

    if (is_null($status)) {
        return $option == 1;
    }

    /*
    *   For all cases check the option too again, because if the option is set to No, no status changes on any status is allowed
     */
    if ($option == 0) {
        return false;
    }

    $forbidden = hooks()->apply_filters('forbidden_form_statuses_to_change_in_clients_area', [3, 4]);

    if (in_array($status, $forbidden)) {
        return false;
    }

    return true;
}

/**
 * For html5 form accepted attributes
 * This function is used for the forms form attachments
 * @return string
 */
function get_form_form_accepted_mimes()
{
    $form_allowed_extensions = get_option('form_attachments_file_extensions');

    $_form_allowed_extensions = array_map(function ($ext) {
        return trim($ext);
    }, explode(',', $form_allowed_extensions));

    $all_form_ext = str_replace([' '], '', $form_allowed_extensions);

    if (is_array($_form_allowed_extensions)) {
        foreach ($_form_allowed_extensions as $ext) {
            $all_form_ext .= ',' . get_mime_by_extension($ext);
        }
    }

    return $all_form_ext;
}

function form_message_save_as_predefined_reply_javascript()
{
    if (!is_admin() && get_option('staff_members_save_forms_predefined_replies') == '0') {
        return false;
    } ?>
    <div class="modal fade" id="savePredefinedReplyFromMessageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo _l('predefined_replies_dt_name'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo render_input('name', 'predefined_reply_add_edit_name'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-primary"
                        id="saveFormMessagePredefinedReply"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        $(function() {
            var editorMessage = tinymce.get('message');
            if (typeof(editorMessage) != 'undefined') {
                editorMessage.on('change', function() {
                    if (editorMessage.getContent().trim() != '') {
                        if ($('#savePredefinedReplyFromMessage').length == 0) {
                            $('[app-field-wrapper="message"] [role="menubar"]:first')
                                .append(
                                    "<button id=\"savePredefinedReplyFromMessage\" data-toggle=\"modal\" type=\"button\" data-target=\"#savePredefinedReplyFromMessageModal\" class=\"tox-mbtn save_predefined_reply_from_message pointer\" href=\"#\"></button>"
                                );
                        }
                        // For open is handled on contact select
                        if ($('#single-form-form').length > 0) {
                            var contactIDSelect = $('#contactid');
                            if (contactIDSelect.data('no-contact') == undefined && contactIDSelect.data(
                                    'form-emails') == '0') {
                                show_form_no_contact_email_warning($('input[name="userid"]').val(),
                                    contactIDSelect.val());
                            } else {
                                clear_form_no_contact_email_warning();
                            }
                        }
                    } else {
                        $('#savePredefinedReplyFromMessage').remove();
                        clear_form_no_contact_email_warning();
                    }
                });

                if (editorMessage.getContent().trim() == '') {
                    $('button[data-form=#single-form-form]').attr('disabled', true);
                } else {
                    $('button[data-form=#single-form-form]').attr('disabled', false);
                }
                editorMessage.on('keyup', function() {
                    if (editorMessage.getContent().trim() == '') {
                        $('button[data-form=#single-form-form]').attr('disabled', true);
                    } else {
                        $('button[data-form=#single-form-form]').attr('disabled', false);
                    }
                });
            }
            $('body').on('click', '#saveFormMessagePredefinedReply', function(e) {
                e.preventDefault();
                var data = {}
                data.message = editorMessage.getContent();
                data.name = $('#savePredefinedReplyFromMessageModal #name').val();
                data.form_area = true;
                $.post(admin_url + 'forms/predefined_reply', data).done(function(response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        var predefined_reply_select = $('#insert_predefined_reply');
                        predefined_reply_select.find('option:first').after('<option value="' + response
                            .id + '">' + data.name + '</option>');
                        predefined_reply_select.selectpicker('refresh');
                    }
                    $('#savePredefinedReplyFromMessageModal').modal('hide');
                });
            });
        });
    </script>
<?php
}

function get_form_public_url($form)
{
    if (is_array($form)) {
        $form = array_to_object($form);
    }

    $CI = &get_instance();

    if (!$form->formkey) {
        $CI->db->where('formid', $form->formid);
        $CI->db->update('forms', ['formkey' => $key = app_generate_hash()]);
    } else {
        $key = $form->formkey;
    }

    return site_url('forms/forms/' . $key);
}

function can_staff_delete_form_reply()
{
    return can_staff_delete_form();
}

function can_staff_delete_form()
{
    if (is_admin()) {
        return true;
    }

    if (!is_staff_member() && get_option('access_forms_to_none_staff_members') == '0') {
        return false;
    }

    return get_option('allow_non_admin_members_to_delete_forms_and_replies') == '1';
}

function can_staff_edit_form_message()
{
    if (is_admin()) {
        return true;
    }

    if (!is_staff_member() && get_option('access_forms_to_none_staff_members') == '0') {
        return false;
    }

    return get_option('allow_non_admin_members_to_edit_form_messages') == '1';
}

function form_public_form_customers_footer()
{
    // Create new listeners for the public_form
    // removes the one from clients.js (if loaded) and using new ones
?>
    <style>
        .single-form-project-area {
            display: none !important;
        }
    </style>
    <script>
        $(function() {
            setTimeout(function() {
                $('#form-reply').appFormValidator();

                $('.toggle-change-form-status').off('click');
                $('.toggle-change-form-status').on('click', function() {
                    $('.form-status,.form-status-inline').toggleClass('hide');
                });

                $('#form_status_single').off('change');
                $('#form_status_single').on('change', function() {
                    data = {};
                    data.status_id = $(this).val();
                    data.form_id = $('input[name="form_id"]').val();
                    $.post(site_url + 'clients/change_form_status/', data).done(function() {
                        window.location.reload();
                    });
                });
            }, 2000)
        })
    </script>
<?php
}


function get_weather_listing()
{
    $result = array();
    $result = [
        [
            'id' => 'Clear',
            'name' => 'Clear',
        ],
        [
            'id' => 'Cloudy',
            'name' => 'Cloudy',
        ],
        [
            'id' => 'Rain',
            'name' => 'Rain',
        ],
    ];
    return $result;
}

function get_work_stop_listing()
{
    $result = array();
    $result = [
        [
            'id' => 'Y',
            'name' => 'Y',
        ],
        [
            'id' => 'N',
            'name' => 'N',
        ],
    ];
    return $result;
}

function get_vendor($name_agency, $agency)
{
    $id = '';
    $where = [];
    $CI = &get_instance();

    $CI->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'pur_vendor')) . ',' . get_sql_select_vendor_company());

    if (is_numeric($id)) {

        $CI->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
        $CI->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $CI->db->where($where);
        }

        $CI->db->where(db_prefix() . 'pur_vendor.userid', $id);
        $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();

        if ($vendor && get_option('company_requires_vat_number_field') == 0) {
            $vendor->vat = null;
        }

        return $vendor;
    } else {

        if (!has_permission('purchase_vendors', '', 'view') && is_staff_logged_in()) {

            $CI->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
            $CI->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

            if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
                $CI->db->where($where);
            }

            $CI->db->where(db_prefix() . 'pur_vendor.userid IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ')');
        } else {
            $CI->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
            $CI->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

            if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
                $CI->db->where($where);
            }
        }
    }

    $CI->db->order_by('company', 'asc');

    $result = $CI->db->get(db_prefix() . 'pur_vendor')->result_array();

    return render_select($name_agency, $result, array('userid', 'company'), '', $agency);
}
function get_vendor_list_for_forms($vendor_id = '')
{
    $CI = &get_instance();
    if($vendor_id != ''){
        $CI->db->where('userid', $vendor_id);
    }
    $result = $CI->db->get(db_prefix() . 'pur_vendor')->result_array();
    return $result;
}
function get_client_listing()
{
    $CI = &get_instance();
    $CI->db->select('userid,company');
    return $CI->db->get(db_prefix() . 'clients')->result_array();
}


function get_items_required_amount_mfa()
{
    $result = array();
    $result = [
        [
            'id' => 1,
            'name' => '24/12/12',
        ],
        [
            'id' => 2,
            'name' => '15',
        ],
        [
            'id' => 3,
            'name' => '12/12',
        ],
        [
            'id' => 4,
            'name' => '15',
        ],
        [
            'id' => 5,
            'name' => '1',
        ],
        [
            'id' => 6,
            'name' => '10',
        ],
        [
            'id' => 7,
            'name' => '1',
        ],
        [
            'id' => 8,
            'name' => '3 Packets',
        ],
        [
            'id' => 9,
            'name' => '2 Packets',
        ],
        [
            'id' => 10,
            'name' => '1',
        ],
        [
            'id' => 11,
            'name' => '12',
        ],
        [
            'id' => 12,
            'name' => '12',
        ],
        [
            'id' => 13,
            'name' => '2',
        ],
        [
            'id' => 14,
            'name' => '1 bottle',
        ],
        [
            'id' => 15,
            'name' => '1',
        ]
    ];
    return $result;
}

function get_item_status_listing()
{
    $result = array();
    $result = [
        [
            'id' => '1',
            'name' => 'Yes',
        ],
        [
            'id' => '2',
            'name' => 'No',
        ],
        [
            'id' => '3',
            'name' => 'May Be',
        ]
    ];
    return $result;
}
function get_staff_list($where = '')
{
    $CI = &get_instance();
    $CI->db->select('staffid,concat(firstname," ",lastname) as name');
    if ($where != '') {
        $CI->db->where($where);
    }
    return $CI->db->get(db_prefix() . 'staff')->result_array();
}

function get_qcr_category($name_category, $category)
{
    $result = array();
    $result = [
        [
            'id' => 1,
            'name' => 'Morderate',
        ],
        [
            'id' => 2,
            'name' => 'Major',
        ],
        [
            'id' => 3,
            'name' => 'Critical',
        ],
    ];
    return render_select($name_category, $result, array('id', 'name'), '', $category);
}

function get_qcr_status($name_status, $status)
{
    $result = array();
    $result = [
        [
            'id' => 1,
            'name' => 'Close',
        ],
        [
            'id' => 2,
            'name' => 'Open',
        ],
        [
            'id' => 3,
            'name' => 'Temporary work done for event',
        ],
    ];
    return render_select($name_status, $result, array('id', 'name'), '', $status);
}
function get_qcr_category_by_id($category_id)
{
    $category = '';
    if ($category_id == 1) {
        $category = 'Morderate';
    } elseif ($category_id == 2) {
        $category = 'Major';
    } elseif ($category_id == 3) {
        $category = 'Critical';
    }

    return $category;
}

function get_qcr_status_by_id($status_id)
{
    $status = '';
    if ($status_id == 1) {
        $status = 'Close';
    } elseif ($status_id == 2) {
        $status = 'Open';
    } elseif ($status_id == 3) {
        $status = 'Temporary work done for event';
    }

    return $status;
}

function chcek_formid_is_qcr($formid)
{
    $CI = &get_instance();
    // Use your DB prefix helper (Perfex = db_prefix())
    $table = db_prefix() . 'qcr_form_detail';

    // Count how many rows match this formid
    $count = $CI->db
        ->where('form_id', $formid)
        ->count_all_results($table);

    return $count > 0;
}

function check_formid_is_qor($formid)
{

    $CI = &get_instance();
    // Use your DB prefix helper (Perfex = db_prefix())
    $table = db_prefix() . 'qor_form_detail';

    // Count how many rows match this formid
    $count = $CI->db
        ->where('form_id', $formid)
        ->count_all_results($table);

    return $count > 0;
}

function get_progress_report_type_listing($name_type, $type)
{
    $CI = &get_instance();
    $result = $CI->db->get(db_prefix() . 'progress_report_type')->result_array();
    return render_select($name_type, $result, array('id', 'name'), '', $type);
}

function get_progress_report_sub_type_listing($name_sub_type, $sub_type)
{
    $CI = &get_instance();
    $result = $CI->db->get(db_prefix() . 'progress_report_sub_type')->result_array();
    return render_select($name_sub_type, $result, array('id', 'name'), '', $sub_type);
}

function get_progress_report_machinary_listing($name_machinery, $machinery)
{
    $CI = &get_instance();
    $result = $CI->db->get(db_prefix() . 'progress_report_machinary')->result_array();
    return render_select($name_machinery, $result, array('id', 'name'), '', $machinery);
}

/**
 * Render admin forms table
 * @param string  $name        table name
 * @param boolean $bulk_action include checkboxes on the left side for bulk actions
 */
function AdminReportsTableStructure($name = '', $bulk_action = false)
{
    $table = '<table class="table customizable-table number-index-' . ($bulk_action ? '2' : '1') . ' dt-table-loading ' . ($name == '' ? 'preports-table' : $name) . ' table-forms" id="forms" data-last-order-identifier="forms" data-default-order="' . get_table_last_order('forms') . '">';
    $table .= '<thead>';
    $table .= '<tr>';

    $table .= '<th class="' . ($bulk_action == true ? '' : 'not_visible') . '">';
    $table .= '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="forms"><label></label></div>';
    $table .= '</th>';

    $table .= '<th class="toggleable" id="th-number">' . _l('the_number_sign') . '</th>';
    $table .= '<th class="toggleable" id="th-subject">' . _l('form_dt_subject') . '</th>';
    $table .= '<th class="toggleable" id="th-project">' . _l('project') . '</th>';
    $table .= '<th class="toggleable" id="th-department">' . _l('form_dt_department') . '</th>';
    $table .= '<th class="toggleable" id="th-status">' . _l('form_dt_status') . '</th>';
    $table .= '<th class="toggleable" id="th-priority">' . _l('form_dt_priority') . '</th>';
    $table .= '<th class="toggleable" id="th-last-reply">' . _l('form_dt_last_reply') . '</th>';
    $table .= '<th class="toggleable report_created_column" id="th-created">' . _l('form_date_created') . '</th>';
    $table .= '<th class="toggleable" id="th-tags">' . _l('tags') . '</th>';
    $table .= '<th class="toggleable ticket_options" id="th-options">' . _l('options') . '</th>';
    $custom_fields = get_table_custom_fields('forms');

    foreach ($custom_fields as $field) {
        $table .= '<th>' . $field['name'] . '</th>';
    }

    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table>';

    $table .= '<script id="hidden-columns-table-forms" type="text/json">';
    $table .= get_staff_meta(get_staff_user_id(), 'hidden-columns-table-forms');
    $table .= '</script>';

    return $table;
}
function get_progress_report_type_name($type)
{
    $CI = &get_instance();
    $CI->db->where('id', $type);
    $CI->db->select('name');
    $result = $CI->db->get(db_prefix() . 'progress_report_type')->result_array();
    return !empty($result) ? $result[0]['name'] : '';
}

function get_progress_report_sub_type_name($sub_type)
{
    $CI = &get_instance();
    $CI->db->where('id', $sub_type);
    $CI->db->select('name');
    $result = $CI->db->get(db_prefix() . 'progress_report_sub_type')->result_array();
    return !empty($result) ? $result[0]['name'] : '';
}

function get_progress_report_machinary_name($machinery)
{
    $CI = &get_instance();
    if (empty($machinery)) {
        return '';
    }
    $CI->db->where('id', $machinery);
    $CI->db->select('name');
    $result = $CI->db->get(db_prefix() . 'progress_report_machinary')->result_array();
    return !empty($result) ? $result[0]['name'] : '';
}