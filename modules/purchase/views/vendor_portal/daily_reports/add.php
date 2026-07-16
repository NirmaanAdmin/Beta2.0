<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<style type="text/css">
    .daily_report_title,
    .daily_report_activity {
        font-weight: bold;
        text-align: center;
        background-color: lightgrey;
    }

    .daily_report_title {
        font-size: 17px;
    }

    .daily_report_activity {
        font-size: 16px;
    }

    .daily_report_head {
        font-size: 14px;
    }

    .daily_report_label {
        font-weight: bold;
    }

    .daily_center {
        text-align: center;
    }

    .table-responsive {
        overflow-x: visible !important;
        scrollbar-width: none !important;
    }

    .laber-type .dropdown-menu .open,
    .agency .dropdown-menu .open {
        width: max-content !important;
    }

    .agency .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 138px !important;
    }

    .laber-type .dropdown-menu .open,
    .progress_report_type .dropdown-menu .open {
        width: max-content !important;
    }

    .progress_report_type .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 140px !important;
    }

    .laber-type .dropdown-menu .open,
    .machinery .dropdown-menu .open {
        width: max-content !important;
    }

    .machinery .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 140px !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <form action="<?php echo $this->uri->uri_string(); ?>" method="POST" enctype="multipart/form-data" id="new_form_form">
            <div class="row">
                <div class="col-md-12">
                    <div class="tw-flex tw-items-center tw-mb-2">
                        <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-mr-4">
                            <?php echo _l('daily_progress_report'); ?>
                        </h4>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">

                                    <!-- Subject Input -->
                                    <div class="form-group">
                                        <label for="subject" class="control-label"><?php echo _l('form_settings_subject'); ?></label>
                                        <input type="text" id="subject" name="subject" value="DPR" class="form-control" required="required">
                                    </div>

                                    <!-- Project Select -->
                                    
                                    <div class="form-group projects-wrapper">
                                        <label for="project_id" class="control-label"><?php echo _l('project'); ?></label>
                                        <select id="project_id" name="project_id" class="form-control" required="required">
                                            <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                            <?php
                                            
                                            foreach ($ven_project_ids as $project) { ?>
                                                <option value="<?php echo e($project['id']); ?>" selected>
                                                    <?php echo e($project['name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Department Select -->
                                            <div class="form-group">
                                                <label for="department" class="control-label"><?php echo _l('form_settings_departments'); ?></label>
                                                <select id="department" name="department" class="form-control">
                                                    <?php
                                                    $default_dept = (count($departments) == 1) ? $departments[0]['departmentid'] : '';
                                                    foreach ($departments as $dept) { ?>
                                                        <option value="<?php echo e($dept['departmentid']); ?>" <?php echo ($default_dept == $dept['departmentid']) ? 'selected' : ''; ?>>
                                                            <?php echo e($dept['name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cc" class="control-label"><?php echo _l('CC'); ?></label>
                                                <input type="text" id="cc" name="cc" class="form-control">
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-md-6">



                                    <!-- Assigned Select -->
                                    <!-- <div class="form-group select-placeholder">
                                        <label for="assigned" class="control-label">
                                            <?php echo _l('form_settings_assign_to'); ?>
                                        </label>
                                        <select id="assigned" name="assigned" class="form-control" required="required">
                                            <option value=""><?php echo _l('form_settings_none_assigned'); ?></option>
                                            <?php foreach ($staff as $member) {
                                                $selected = ($member['staffid'] == get_staff_user_id()) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo e($member['staffid']); ?>" <?php echo $selected; ?>>
                                                    <?php echo e($member['firstname'] . ' ' . $member['lastname']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div> -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Priority Select -->
                                            <div class="form-group">
                                                <label for="priority" class="control-label"><?php echo _l('form_settings_priority'); ?></label>
                                                <select id="priority" name="priority" class="form-control" required="required">
                                                    <?php
                                                    $priority_selected = hooks()->apply_filters('new_form_priority_selected', 2);
                                                    foreach ($priorities as $priority) { ?>
                                                        <option value="<?php echo e($priority['priorityid']); ?>" <?php echo ($priority_selected == $priority['priorityid']) ? 'selected' : ''; ?>>
                                                            <?php echo e($priority['name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Due Date Input -->
                                            <div class="form-group">
                                                <label for="duedate" class="control-label"><?php echo _l('task_add_edit_due_date'); ?></label>
                                                <input type="date" id="duedate" name="duedate" class="form-control" required="required">
                                            </div>
                                        </div>

                                        <?php if (get_option('services') == 1) { ?>
                                            <div class="col-md-6 hide">
                                                <!-- Service Select (hidden) -->
                                                <div class="form-group">
                                                    <label for="service" class="control-label"><?php echo _l('form_settings_service'); ?></label>
                                                    <select id="service" name="service" class="form-control">
                                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                                        <?php foreach ($services as $service) { ?>
                                                            <option value="<?php echo e($service['serviceid']); ?>">
                                                                <?php echo e($service['name']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <?php echo render_custom_fields('forms'); ?>
                            </div>

                            <div class="view_form_design">
                                <div class="col-md-12">
                                    <div class="table-responsive invoice-item">
                                        <?php
                                        if (isset($dpr_form)) {
                                            echo form_hidden('isedit');
                                        }
                                        ?>
                                        <table class="table dpr-items-table items table-main-dpr-edit has-calculations no-mtop">
                                            <thead>
                                                <tr>
                                                    <th colspan="13" class="daily_report_title">DAILY PROGRESS REPORT</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="9" class="daily_report_head">
                                                        <span class="daily_report_label">Project: <span class="view_project_name"></span></span>
                                                    </th>
                                                    <th colspan="4" class="daily_report_head">
                                                        <span class="daily_report_label">DPR Date: </span>
                                                        <?php echo isset($dpr_main_form->date) ? date('d-m-Y', strtotime($dpr_main_form->date)) : date('d-m-Y'); ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="5" class="daily_report_head">
                                                        <span class="daily_report_label" style="display: ruby;">Client: <?php echo render_select('client_id', get_client_listing(), array('userid', 'company'), '', isset($dpr_form->client_id) ? $dpr_form->client_id : ''); ?></span>
                                                    </th>
                                                    <th colspan="4" class="daily_report_head">
                                                        <span class="daily_report_label" style="display: ruby;">PMC: <?php echo render_input('pmc', '', isset($dpr_form->pmc) ? $dpr_form->pmc : '', 'text', ['style' => 'width:150px;']); ?></span>
                                                    </th>
                                                    <th colspan="4" class="daily_report_head">
                                                        <span class="daily_report_label" style="display: ruby;">Weather: <?php echo render_select('weather', get_weather_listing(), array('id', 'name'), '', isset($dpr_form->weather) ? $dpr_form->weather : ''); ?></span>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="5" class="daily_report_head">
                                                        <span class="daily_report_label" style="display: ruby;">Consultant: <?php echo render_input('consultant', '', isset($dpr_form->consultant) ? $dpr_form->consultant : '', 'text', ['style' => 'width:150px;']); ?></span>
                                                    </th>
                                                    <th colspan="4" class="daily_report_head">
                                                        <span class="daily_report_label" style="display: ruby;">Contractor: <?php echo render_input('contractor', '', isset($dpr_form->contractor) ? $dpr_form->contractor : '', 'text', ['style' => 'width:150px;']); ?></span>
                                                    </th>
                                                    <th colspan="4" class="daily_report_head">
                                                        <span class="daily_report_label" style="display: ruby;">Work Stop: <?php echo render_select('work_stop', get_work_stop_listing(), array('id', 'name'), '', isset($dpr_form->work_stop) ? $dpr_form->work_stop : ''); ?></span>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="13" class="daily_report_activity">ACTIVITY WITH LOCATION & OUTPUT</th>
                                                </tr>
                                                <tr>
                                                    <th rowspan="2" class="daily_report_head daily_center" style="width: 160px;">
                                                        <span class="daily_report_label">Location</span>
                                                    </th>
                                                    <th rowspan="2" class="daily_report_head daily_center" style="width: 160px;">
                                                        <span class="daily_report_label">Agency</span>
                                                    </th>
                                                    <th rowspan="2" class="daily_report_head daily_center" style="width: 160px;">
                                                        <span class="daily_report_label">Type</span>
                                                    </th>
                                                    <th rowspan="2" class="daily_report_head daily_center" style="width: 17%;">
                                                        <span class="daily_report_label">Remarks</span>
                                                    </th>
                                                    <th colspan="2" class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Work Progress</span>
                                                    </th>
                                                    <th colspan="3" class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Type Of Manpower</span>
                                                    </th>
                                                    <th colspan="3" class="daily_report_head daily_center">
                                                        <span class="daily_report_label"></span>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Work Execute (smt/Rmt/Cmt)</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Material Consumption</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Skilled</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Unskilled</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Total</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Machinary</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label">Total</span>
                                                    </th>
                                                    <th class="daily_report_head daily_center">
                                                        <span class="daily_report_label"><i class="fa fa-cog"></i></span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="dpr_body">
                                                <?php echo pur_html_entity_decode($dpr_row_template); ?>
                                            </tbody>
                                        </table>
                                        <div id="removed-items"></div>

                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <hr class="hr-panel-separator" />
                            </div>

                            <div class="col-md-12">
                                <div class="attachments_area">
                                    <div class="row attachments">
                                        <div class="attachment">
                                            <div class="col-md-4 mtop10">
                                                <div class="form-group">
                                                    <label for="attachment" class="control-label"><?php echo _l('form_add_attachments'); ?></label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                            extension="<?php echo str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')); ?>"
                                                            filesize="<?php echo file_upload_max_size(); ?>"
                                                            class="form-control" name="attachments[0]"
                                                            accept="<?php echo get_form_form_accepted_mimes(); ?>">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default add_more_attachments"
                                                                data-max="<?php echo get_option('maximum_allowed_form_attachments'); ?>"
                                                                type="button"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 tw-mt-3">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-base tw-text-neutral-700 mtop10">
                                    <?php echo _l('additional_notes'); ?>
                                </h4>
                                <div class="row">
                                    <div class="col-md-12 mbot20 before-form-message">
                                        <div class="row">
                                            <div class="col-md-6 hide">
                                                <!-- Predefined Reply Select (hidden) -->
                                                <select id="insert_predefined_reply" class="form-control">
                                                    <option value=""><?php echo _l('form_single_insert_predefined_reply'); ?></option>
                                                    <?php foreach ($predefined_replies as $predefined_reply) { ?>
                                                        <option value="<?php echo e($predefined_reply['id']); ?>">
                                                            <?php echo e($predefined_reply['name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php if (get_option('use_knowledge_base') == 1) { ?>
                                                <div class="visible-xs">
                                                    <div class="mtop15"></div>
                                                </div>
                                                <div class="col-md-6 hide">
                                                    <!-- Knowledge Base Select (hidden) -->
                                                    <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                                    <select id="insert_knowledge_base_link" class="form-control" onchange="insert_form_knowledgebase_link(this);">
                                                        <option value=""><?php echo _l('form_single_insert_knowledge_base_link'); ?></option>
                                                        <?php foreach ($groups as $group) { ?>
                                                            <?php if (count($group['articles']) > 0) { ?>
                                                                <optgroup label="<?php echo e($group['name']); ?>">
                                                                    <?php foreach ($group['articles'] as $article) { ?>
                                                                        <option value="<?php echo e($article['articleid']); ?>">
                                                                            <?php echo e($article['subject']); ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </optgroup>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <!-- Message Textarea -->
                            <div class="form-group">
                                <label for="message" class="control-label"><?php echo _l('message'); ?></label>
                                <textarea id="message" name="message" class="form-control tinymce" rows="10"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="btn-bottom-toolbar text-right">
                        <button type="submit" data-form="#new_form_form" autocomplete="off"
                            data-loading-text="<?php echo _l('wait_text'); ?>"
                            class="btn btn-primary"><?php echo _l('save_report'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="tw-py-10"></div>

<?php hooks()->do_action('app_admin_footer'); ?>

</body>

</html>

<script type="text/javascript">
    var lastAddedItemKey = 0;
    var admin_url = "<?php echo site_url('admin/purchase/vendors_portal'); ?>";
    $(document).on('click', '.dpr-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? dpr_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.dpr-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        dpr_get_item_row_template('newitems[' + item_key + ']', data.location, data.agency, data.type, data.work_execute, data.material_consumption, data.machinery, data.skilled, data.unskilled, data.depart, data.total, data.male, data.female, item_key).done(function(output) {
            table_row += output;

            $('.dpr_body').append(table_row);

            init_selectpicker();
            pur_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');

            return true;
        });
        return false;
    });

    function dpr_get_item_row_template(name, location, agency, type, work_execute, material_consumption, machinery, skilled, unskilled, depart, total, male, female, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + '/get_dpr_row_template', {
            name: name,
            location: location,
            agency: agency,
            type: type,
            work_execute: work_execute,
            material_consumption: material_consumption,
            machinery: machinery,
            skilled: skilled,
            unskilled: unskilled,
            depart: depart,
            total: total,
            male: male,
            female: female,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function dpr_get_item_preview_values() {
        "use strict";

        var response = {};
        response.location = $('.dpr-items-table input[name="location"]').val();
        response.agency = $('.dpr-items-table select[name="agency"]').selectpicker('val');
        response.type = $('.dpr-items-table select[name="type"]').selectpicker('val');
        response.work_execute = $('.dpr-items-table input[name="work_execute"]').val();
        response.material_consumption = $('.dpr-items-table input[name="material_consumption"]').val();
        response.machinery = $('.dpr-items-table input[name="machinery"]').val();
        response.skilled = $('.dpr-items-table input[name="skilled"]').val();
        response.unskilled = $('.dpr-items-table input[name="unskilled"]').val();
        response.depart = $('.dpr-items-table input[name="depart"]').val();
        response.total = $('.dpr-items-table input[name="total"]').val();
        response.male = $('.dpr-items-table input[name="male"]').val();
        response.female = $('.dpr-items-table input[name="female"]').val();

        return response;
    }

    function pur_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }
</script>