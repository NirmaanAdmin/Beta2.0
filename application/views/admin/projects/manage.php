<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'projects'; ?>
<div id="wrapper">
    <div class="content">
        <div id="vueApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="_buttons tw-mb-2 sm:tw-mb-4">
                        <?php if (staff_can('create',  'projects')) { ?>
                        <a href="<?php echo admin_url('projects/project'); ?>"
                            class="btn btn-primary pull-left display-block mright5">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_project'); ?>
                        </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('projects/gantt'); ?>" data-toggle="tooltip"
                            data-title="<?php echo _l('project_gant'); ?>" class="btn btn-default btn-with-tooltip">
                            <i class="fa fa-align-left" aria-hidden="true"></i>
                        </a>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel_s tw-mt-2 sm:tw-mt-4">
                        <div class="panel-body">
                            <div class="row mbot15">
                                <div class="col-md-12">
                                    <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <span>
                                            <?php echo _l('projects_summary'); ?>
                                        </span>
                                    </h4>
                                    <?php
                                        $_where = '';
                                        if (staff_cant('view', 'projects')) {
                                            $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
                                        }
                                    ?>
                                </div>
                            </div>
                            <hr class="hr-panel-separator" />
                            <div class="row all_filters">
                                <?php
                                $project_name_filter = get_module_filter($module_name, 'project_name');
                                $project_name_filter_val = !empty($project_name_filter) ? explode(",", $project_name_filter->filter_value) : [];
                                ?>
                                <div class="col-md-3 form-group">
                                    <label for="project_name"><?php echo _l('project_name'); ?></label>
                                    <select name="project_name[]" id="project_name" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                        <?php foreach ($project_name as $project) { ?>
                                            <option value="<?php echo pur_html_entity_decode($project['id']); ?>"
                                                <?php if (in_array($project['id'], $project_name_filter_val)) {
                                                    echo 'selected';
                                                } ?>>
                                                <?php echo pur_html_entity_decode($project['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <?php
                                $clients_filter = get_module_filter($module_name, 'clients');
                                $clients_filter_val = !empty($clients_filter) ? explode(",", $clients_filter->filter_value) : [];
                                ?>
                                <div class="col-md-3 form-group">
                                    <label for="clients"><?php echo _l('clients'); ?></label>
                                    <select name="clients[]" id="clients" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                        <?php foreach ($clients as $client) { ?>
                                            <option value="<?php echo pur_html_entity_decode($client['userid']); ?>"
                                                <?php if (in_array($client['userid'], $clients_filter_val)) {
                                                    echo 'selected';
                                                } ?>>
                                                <?php echo pur_html_entity_decode($client['company']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <?php
                                $members_filter = get_module_filter($module_name, 'members');
                                $members_filter_val = !empty($members_filter) ? explode(",", $members_filter->filter_value) : [];
                                ?>
                                <div class="col-md-3 form-group">
                                    <label for="project_members"><?php echo _l('project_members'); ?></label>
                                    <select name="members[]" id="members" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                        <?php foreach ($members as $member) { ?>
                                            <option value="<?php echo pur_html_entity_decode($member['staffid']); ?>"
                                                <?php if (in_array($member['staffid'], $members_filter_val)) {
                                                    echo 'selected';
                                                } ?>>
                                                <?php echo pur_html_entity_decode($member['firstname']).' '.pur_html_entity_decode($member['lastname']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <?php
                                $statuses_filter = get_module_filter($module_name, 'statuses');
                                $statuses_filter_val = !empty($statuses_filter) ? explode(",", $statuses_filter->filter_value) : [];
                                ?>
                                <div class="col-md-3 form-group">
                                    <label for="project_status"><?php echo _l('project_status'); ?></label>
                                    <select name="statuses[]" id="statuses" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                        <?php foreach ($statuses as $status) { ?>
                                            <option value="<?php echo pur_html_entity_decode($status['id']); ?>"
                                                <?php if (in_array($status['id'], $statuses_filter_val)) {
                                                    echo 'selected';
                                                } ?>>
                                                <?php echo pur_html_entity_decode($status['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-1 form-group">
                                    <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                                        <?php echo _l('reset_filter'); ?>
                                    </a>
                                </div>
                            </div>
                            <hr class="hr-panel-separator" />
                            <div class="panel-table-full">
                                <?php echo form_hidden('custom_view'); ?>
                                <?php $this->load->view('admin/projects/table_html'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script>
var table_projects;
$(function() {
    table_projects = $('.table-projects');
    var Params = {
        "project_name": "[name='project_name[]']",
        "clients": "[name='clients[]']",
        "members": "[name='members[]']",
        "statuses": "[name='statuses[]']",
    };
    initDataTable('.table-projects', admin_url + 'projects/table_new', [8,9], [8,9], Params,
        <?php echo hooks()->apply_filters('projects_table_default_order', json_encode([5, 'asc'])); ?>);
    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {
            table_projects.DataTable().ajax.reload();
        });
    });

    $('.table-projects').DataTable().on('draw', function() {
        var rows = $('.table-projects').find('tr');
        $.each(rows, function() {
            var td = $(this).find('td').eq(4);
            var percent = $(td).find('input[name="percent"]').val();
            $(td).find('.goal-progress').circleProgress({
                value: percent,
                size: 45,
                animation: false,
                fill: {
                    gradient: ["#28b8da", "#059DC1"]
                }
            })
        })
    });

    $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').selectpicker("val", "");
        table_projects.DataTable().ajax.reload();
    });
    $(document).on('change', 'select[name="project_name[]"]', function() {
        $('select[name="project_name[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="clients[]"]', function() {
        $('select[name="clients[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="members[]"]', function() {
        $('select[name="members[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="statuses[]"]', function() {
        $('select[name="statuses[]"]').selectpicker('refresh');
    });

    init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>
</body>
</html>