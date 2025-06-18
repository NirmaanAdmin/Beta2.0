<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();
$module_name = 'critical_mom'; ?>
<style>
    .table-responsive {
        scrollbar-width: none !important;
    }

    .loader-container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 9999999;
    }

    .loader-gif {
        width: 100px;
        /* Adjust the size as needed */
        height: 100px;
    }

    .show_hide_columns {
        position: absolute;
        z-index: 999;
        left: 140px;
    }

    .export-btn-div {
        position: absolute;
        z-index: 999;
        left: 189px;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row" style="margin-bottom: 16px;">

            <div class="col-md-1">
                <?php if (has_permission('critical_agenda', '', 'create') || is_admin()) { ?>
                    <button class="btn btn-info pull-left mright10 display-block" data-toggle="modal" data-target="#addNewRowModal">
                        <i class="fa fa-plus"></i> <?php echo _l('New'); ?>
                    </button>
                <?php } ?>
            </div>

            <div class="row all_ot_filters">
                <div class="col-md-2 form-group">
                    <?php
                    $department_type_filter = get_module_filter($module_name, 'department');
                    $department_type_filter_val = !empty($department_type_filter) ? explode(",", $department_type_filter->filter_value) : '';

                    echo render_select('department[]', $department, array('departmentid', 'name'), '', $department_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('department'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                    ?>
                </div>
                <div class="col-md-2 form-group">
                    <?php
                    $status_type_filter = get_module_filter($module_name, 'status');
                    $status_type_filter_val = !empty($status_type_filter) ? explode(",", $status_type_filter->filter_value) : '';
                    $status_labels = [
                        ['id' => '1', 'name' => 'Open'],
                        ['id' => '2', 'name' => 'Close'],
                    ];
                    echo render_select('status[]', $status_labels, array('id', 'name'), '', $status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                    ?>
                </div>
                <div class="col-md-2 form-group">
                    <?php
                    $priority_type_filter = get_module_filter($module_name, 'priority');
                    $priority_type_filter_val = !empty($priority_type_filter) ? explode(",", $priority_type_filter->filter_value) : '';

                    $priorities_labels = [
                        ['id' => '1', 'name' => 'High'],
                        ['id' => '2', 'name' => 'Low'],
                        ['id' => '3', 'name' => 'Medium'],
                        ['id' => '4', 'name' => 'Urgent'],
                    ];

                    echo render_select('priority[]', $priorities_labels, array('id', 'name'), '', $priority_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Priority'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                    ?>
                </div>
                <!-- <div class="col-md-2 form-group">
                        <?php

                        ?>
                    </div> -->


                <div class="col-md-1 form-group">
                    <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                        <?php echo _l('reset_filter'); ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="loader-container hide" id="loader-container">
                <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
            </div>
            <div class="col-md-12">

                <div class="panel_s invoice-item-table">
                    <div class="panel-body">

                        <div class="row">
                            <div class="_filters _hidden_inputs hidden tickets_filters"><input type="hidden" name="ticket_status_1" value="1"><input type="hidden" name="ticket_status_2" value="1"><input type="hidden" name="ticket_status_3"><input type="hidden" name="ticket_status_4" value="1"><input type="hidden" name="ticket_status_5"></div>
                            <div class="col-md-12">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                    </svg><span> <?php echo _l('meeting_critical_agenda'); ?> </span></h4>
                            </div>
                            <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0 tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center">
                                <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg"><?php echo $open; ?></span>
                                <span style="color: rgb(255, 45, 66);">Open</span>
                            </div>

                            <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0 tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center">
                                <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg"><?php echo $completed; ?></span>
                                <span style="color: rgb(34, 197, 94);">Completed</span>
                            </div>
                            <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0 tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center">
                                <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg"><?php echo $total; ?></span>
                                <span style="color: rgb(3, 169, 244);">Total</span>
                            </div>

                        </div>
                        <hr class="hr-panel-separator" />
                        <div class="btn-group show_hide_columns" id="show_hide_columns">
                            <!-- Settings Icon -->
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                                <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                            </button>
                            <!-- Dropdown Menu with Checkboxes -->
                            <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                                <!-- Select All / Deselect All -->
                                <div>
                                    <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                                </div>
                                <hr>
                                <!-- Column Checkboxes -->
                                <?php
                                $columns = [
                                    _l('No'),
                                    _l('Department'),
                                    _l('Area/Head'),
                                    _l('Description'),
                                    _l('Decision'),
                                    _l('Action'),
                                    _l('Action By'),
                                    _l('Project'),
                                    _l('Target Date'),
                                    _l('Date Closed'),
                                    _l('Status'),
                                    _l('Priority'),
                                    _l('Fetched From'),
                                ];
                                ?>
                                <div>
                                    <?php foreach ($columns as $key => $label): ?>
                                        <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                                        <?php echo $label; ?><br>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                        <div class="btn-group export-btn-div" id="export-btn-div">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                                <i class="fa fa-download"></i> <?php echo _l('Export'); ?> <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu" style="padding: 10px;min-width: 94px;">
                                <a class="dropdown-item export-btn" href="<?php echo admin_url('meeting_management/minutesController/critical_tracker_pdf'); ?>" data-type="pdf">
                                    <i class="fa fa-file-pdf text-danger"></i> PDF
                                </a><br>
                                <a class="dropdown-item export-btn" href="<?php echo admin_url('meeting_management/minutesController/critical_tracker_excel'); ?>" data-type="excel">
                                    <i class="fa fa-file-excel text-success"></i> Excel
                                </a>
                            </div>
                        </div>

                        <table class="table table-bordered table-table_critical_tracker">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Department</th>
                                    <th>Area/Head</th>
                                    <th>Description</th>
                                    <th>Decision</th>
                                    <th>Action</th>
                                    <th>Action By</th>
                                    <th>Project</th>
                                    <th>Target Date</th>
                                    <th>Date Closed</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Fetched From</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<div class="modal fade" id="addNewRowModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 98%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo _l('Add New'); ?></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="col-md-8 pull-right">
                    <div class="col-md-2 pull-right">
                        <div id="dowload_file_sample" style="margin-top: 22px;">
                            <label for="file_csv" class="control-label"> </label>
                            <a href="<?php echo site_url('modules/meeting_management/uploads/file_sample/Sample_import_critical_tracker_en.xlsx') ?>" class="btn btn-primary">Template</a>
                        </div>
                    </div>
                    <div class="col-md-4 pull-right" style="display: flex;align-items: end;padding: 0px;">
                        <?php echo form_open_multipart(admin_url('meeting_management/minutesController/import_file_xlsx_critical_tracker_items'), array('id' => 'import_form')); ?>
                        <?php echo form_hidden('leads_import', 'true'); ?>
                        <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>

                        <div class="form-group" style="margin-left: 10px;">
                            <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);"><?php echo _l('import'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>

                </div>
                <div class="col-md-12 ">
                    <div class="form-group pull-right" id="file_upload_response">

                    </div>

                </div>
                <div id="box-loading" class="pull-right">

                </div>
            </div>
            <div class="modal-body invoice-item">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" style="overflow-x: unset !important;">
                            <?php
                            echo form_open_multipart('', array('id' => 'critical_tracker-form'));
                            ?>
                            <table class="table critical-tracker-items-table items table-main-invoice-edit has-calculations no-mtop">
                                <thead>
                                    <tr>
                                    <tr>
                                        <th>Department</th>
                                        <th>Area/Head</th>
                                        <th><strong>Description</strong></th>
                                        <th><strong>Decision</strong></th>
                                        <th><strong>Action</strong></th>
                                        <th><strong>Action By</strong></th>
                                        <th width="5%"><strong>Target Date</strong></th>
                                        <th width="5%"><strong>Date Closed</strong></th>
                                        <th width="5%"><strong>Status</strong></th>
                                        <th width="5%"><strong>Priority</strong></th>
                                        <th width="5%"><strong>Project</strong></th>
                                        <th width="3%"></th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody class="mom_body">
                                    <?php echo pur_html_entity_decode($mom_row_template); ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-info pull-right"><?php echo _l('Save'); ?></button>
                            </form>
                        </div>
                        <div id="removed-items"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/meeting_management/assets/js/import_excel_critical_items_mom_js.php'; ?>
<?php require 'modules/meeting_management/assets/js/critical_mom_js.php'; ?>
</body>

</html>
<script>
    $(document).ready(function() {
        let table = $('.table-table_critical_tracker').DataTable();

        // On page load, fetch and apply saved preferences for the logged-in user
        $.ajax({
            url: admin_url + 'meeting_management/minutesController/getPreferences',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("Retrieved preferences:", data);

                // Ensure DataTable is initialized
                let table = $('.table-table_critical_tracker').DataTable();

                // Loop through each toggle checkbox to update column visibility
                $('.toggle-column').each(function() {
                    // Parse the column index (ensuring it's a number)
                    let colIndex = parseInt($(this).val(), 10);

                    // Use the saved preference if available; otherwise, default to visible ("true")
                    let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
                        data.preferences[colIndex] :
                        "true";

                    // Convert string to boolean if needed
                    let isVisible = (typeof prefValue === "string") ?
                        (prefValue.toLowerCase() === "true") :
                        prefValue;

                    // Set column visibility but prevent immediate redraw (redraw = false)
                    table.column(colIndex).visible(isVisible, false);
                    // Update the checkbox state accordingly
                    $(this).prop('checked', isVisible);
                });

                // Finally, adjust columns and redraw the table once
                table.columns.adjust().draw();

                // Update the "Select All" checkbox based on individual toggle states
                let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
                $('#select-all-columns').prop('checked', allChecked);
            },
            error: function() {
                console.error('Could not retrieve column preferences.');
            }
        });

        // Handle "Select All" checkbox
        $('#select-all-columns').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.toggle-column').prop('checked', isChecked).trigger('change');
        });

        // Handle individual column visibility toggling
        $('.toggle-column').on('change', function() {
            var column = table.column($(this).val());
            column.visible($(this).is(':checked'));

            // Sync "Select All" checkbox state
            var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
            $('#select-all-columns').prop('checked', allChecked);

            // Save updated preferences
            saveColumnPreferences();
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Function to collect and save preferences via AJAX
        function saveColumnPreferences() {
            var preferences = {};
            $('.toggle-column').each(function() {
                preferences[$(this).val()] = $(this).is(':checked');
            });

            $.ajax({

                url: admin_url + 'meeting_management/minutesController/savePreferences',
                type: 'POST',
                data: {
                    preferences: preferences
                },
                success: function(response) {
                    console.log('Preferences saved successfully.');
                },
                error: function() {
                    console.error('Failed to save preferences.');
                }
            });
        }
    });
</script>