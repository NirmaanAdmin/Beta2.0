<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'tickets'; ?>
<div id="wrapper">
    <div class="content">
        <div id="vueApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="_buttons tw-mb-2 sm:tw-mb-4">
                        <?php
                        if (staff_can('create',  'rfi_tracker')) { ?>
                            <a href="<?php echo admin_url('tickets/add'); ?>"
                                class="btn btn-primary pull-left display-block mright5">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_ticket'); ?>
                            </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('purchase/activity_log?module=rfi'); ?>" class="btn btn-primary pull-left display-block mright5" target="_blank"><?php echo _l('activity_log'); ?></a>

                        <a href="#" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" data-placement="bottom"
                            data-title="<?php echo _l('tickets_chart_weekly_opening_stats'); ?>"
                            onclick="slideToggle('.weekly-ticket-opening', init_tickets_weekly_chart); return false;">
                            <i class="fa fa-bar-chart"></i>
                        </a>


                    </div>
                    <div class="row all_ot_filters">
                        <div class="col-md-2 form-group">
                            <?php
                            $task_status_type_filter = get_module_filter($module_name, 'ticket_status');
                            $task_status_type_filter_val = !empty($task_status_type_filter) ? explode(",", $task_status_type_filter->filter_value) : '';

                            echo render_select('task_status[]', $statuses, array('ticketstatusid', 'name'), '', $task_status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                            ?>
                        </div>
                        <div class="col-md-2 form-group">
                            <?php
                            $task_assignees_type_filter = get_module_filter($module_name, 'contact');

                            $task_assignees_type_filter_val = [];
                            if (!empty($task_assignees_type_filter) && !empty($task_assignees_type_filter->filter_value)) {
                                $task_assignees_type_filter_val = array_map('trim', explode(",", $task_assignees_type_filter->filter_value));
                            }

                            echo render_select(
                                'contact[]',
                                $contact_list,
                                array('name', 'name'),
                                '',
                                $task_assignees_type_filter_val,
                                array(
                                    'data-width' => '100%',
                                    'data-none-selected-text' => _l('Contact'),
                                    'multiple' => true,
                                    'data-actions-box' => true
                                ),
                                array(),
                                'no-mbot',
                                '',
                                false
                            );
                            ?>
                        </div>
                        <div class="col-md-2 form-group">
                            <?php
                            $task_priority_type_filter = get_module_filter($module_name, 'department');
                            $task_priority_type_filter_val = !empty($task_priority_type_filter) ? explode(",", $task_priority_type_filter->filter_value) : '';

                            echo render_select('department[]', $departments, array('departmentid', 'name'), '', $task_priority_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Department'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                            ?>
                        </div>



                        <div class="col-md-1 form-group">
                            <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                                <?php echo _l('reset_filter'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="weekly-ticket-opening no-shadow tw-mb-10" style="display:none;">
                                <h4 class="tw-font-semibold tw-mb-8 tw-flex tw-items-center tw-text-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="tw-w-5 tw-h-5 tw-mr-1.5 tw-text-neutral-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>

                                    <?php echo _l('home_weekend_ticket_opening_statistics'); ?>
                                </h4>
                                <div class="relative" style="max-height:350px;">
                                    <canvas class="chart" id="weekly-ticket-openings-chart" height="350"></canvas>
                                </div>
                            </div>

                            <?php hooks()->do_action('before_render_tickets_list_table'); ?>
                            <?php $this->load->view('admin/tickets/summary'); ?>
                            <hr class="hr-panel-separator" />
                            <a href="#" data-toggle="modal" data-target="#tickets_bulk_actions"
                                class="bulk-actions-btn table-btn hide"
                                data-table=".table-tickets"><?php echo _l('bulk_actions'); ?></a>
                            <a onclick="bulk_rfi_public_link(); return false;" class="bulk-actions-btn table-btn hide" data-table=".table-tickets"><?php echo _l('Bulk Public Link'); ?></a>
                            <a onclick="bulk_rfi_pdf_download(); return false;" class="bulk-actions-btn table-btn hide" data-table=".table-tickets"><?php echo _l('Bulk PDF Download'); ?></a>
                            <div class="clearfix"></div>
                            <div class="panel-table-full">
                                <?php echo AdminTicketsTableStructure('', true); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bulk_actions" id="tickets_bulk_actions" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="checkbox checkbox-primary merge_tickets_checkbox">
                    <input type="checkbox" name="merge_tickets" id="merge_tickets">
                    <label for="merge_tickets"><?php echo _l('merge_tickets'); ?></label>
                </div>
                <?php if (can_staff_delete_ticket()) { ?>
                    <div class="checkbox checkbox-danger mass_delete_checkbox">
                        <input type="checkbox" name="mass_delete" id="mass_delete">
                        <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                    </div>
                    <hr class="mass_delete_separator" />
                <?php } ?>
                <div id="bulk_change">
                    <?php echo render_select('move_to_status_tickets_bulk', $statuses, ['ticketstatusid', 'name'], 'ticket_single_change_status'); ?>
                    <?php echo render_select('move_to_department_tickets_bulk', $departments, ['departmentid', 'name'], 'department'); ?>
                    <?php echo render_select('move_to_priority_tickets_bulk', $priorities, ['priorityid', 'name'], 'ticket_priority'); ?>
                    <div class="form-group">
                        <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                        <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value=""
                            data-role="tagsinput">
                    </div>
                    <?php if (get_option('services') == 1) { ?>
                        <?php echo render_select('move_to_service_tickets_bulk', $services, ['serviceid', 'name'], 'service'); ?>
                    <?php } ?>
                </div>
                <div id="merge_tickets_wrapper">
                    <div class="form-group">
                        <label for="primary_ticket_id">
                            <span class="text-danger">*</span> <?php echo _l('primary_ticket'); ?>
                        </label>
                        <select id="primary_ticket_id" class="selectpicker" name="primary_ticket_id" data-width="100%"
                            data-live-search="true"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>" required>
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="primary_ticket_status">
                            <span class="text-danger">*</span> <?php echo _l('primary_ticket_status'); ?>
                        </label>
                        <select id="primary_ticket_status" class="selectpicker" name="primary_ticket_status"
                            data-width="100%" data-live-search="true"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>" required>
                            <?php foreach ($statuses as $status) { ?>
                                <option value="<?php echo e($status['ticketstatusid']); ?>"><?php echo e($status['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-primary"
                    onclick="tickets_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="bulk_public_link_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('Bulk Public Link'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="view_bulk_public_link">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var chart;
    var chart_data = <?php echo $weekly_tickets_opening_statistics; ?>;

    function init_tickets_weekly_chart() {
        if (typeof(chart) !== 'undefined') {
            chart.destroy();
        }
        // Weekly ticket openings statistics
        chart = new Chart($('#weekly-ticket-openings-chart'), {
            type: 'line',
            data: chart_data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false,
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                }
            }
        });
    }

    function bulk_rfi_public_link() {
     "use strict";
     var print_id = '';
     var rows = $('.table-tickets').find('tbody tr');
     $.each(rows, function() {
       var checkbox = $($(this).find('td').eq(0)).find('input');
       if (checkbox.prop('checked') === true) {
           if (print_id !== '') {
               print_id += ',';
           }
           print_id += checkbox.val();
       }
     });
     if (print_id !== '') {
       $.post(admin_url + 'tickets/bulk_rfi_public_link', {
         ids: print_id,
       }).done(function (response) {
         response = JSON.parse(response);
         if (response.success) {
           $('.view_bulk_public_link').html('');
           $('.view_bulk_public_link').html(response.bulk_html);
           $('#bulk_public_link_modal').modal('show');
         } else {
           alert_float('danger', response.message);
         }
       });
     } else {
       alert_float('danger', 'Please select at least one item from the list.');
     }
   }

   function bulk_rfi_pdf_download() {
     "use strict";
     var print_id = '';
     var rows = $('.table-tickets').find('tbody tr');
     $.each(rows, function() {
       var checkbox = $($(this).find('td').eq(0)).find('input');
       if (checkbox.prop('checked') === true) {
           if (print_id !== '') {
               print_id += ',';
           }
           print_id += checkbox.val();
       }
     });
     if (print_id !== '') {
        $.ajax({
            url: admin_url + 'tickets/bulk_rfi_pdf_download/?ids='+encodeURIComponent(print_id),
            type: 'GET',
            dataType: 'json',   
            success: function(res) {
                if (res.status === true) {
                    var urls = res.download_urls;
                    var delay = 500;
                    urls.forEach((url, index) => {
                        setTimeout(() => {
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = "";
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                        }, index * delay);
                    });
                }
            }
        });
     } else {
       alert_float('danger', 'Please select at least one item from the list.');
     }
   }
</script>
<script>
    var table_rec_task;
    (function($) {
        table_rec_task = $('.tickets-table');

        var Params = {
            "ticket_status": "[name='task_status[]']",
            "contact": "[name='contact[]']",
            "department": "[name='department[]']",
        };

        initDataTable('.tickets-table', admin_url + 'tickets/table_tasks_details', [], [], Params, [8, 'desc']);


        $.each(Params, function(i, obj) {
            $('select' + obj).on('change', function() {
                table_rec_task.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });

        $(document).on('click', '.reset_all_ot_filters', function() {
            var filterArea = $('.all_ot_filters');
            filterArea.find('input').val("");
            filterArea.find('select').selectpicker("val", "");
            table_rec_task.DataTable().ajax.reload().columns.adjust().responsive.recalc();
        });

    })(jQuery);
</script>
</body>

</html>