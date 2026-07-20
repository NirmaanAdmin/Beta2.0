<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>

</style>
<?php $module_name = 'estimates'; ?>
<div class="col-md-12">
    <?php $this->load->view('admin/estimates/estimates_top_stats'); ?>
    <?php if (staff_can('create',  'estimates')) { ?>
        <a href="<?php echo admin_url('estimates/estimate'); ?>" class="btn btn-primary pull-left new new-estimate-btn">
            <i class="fa-regular fa-plus tw-mr-1"></i>
            <?php echo 'Create New Budget'; ?>
        </a>
    <?php } ?>
    <a href="<?php echo admin_url('purchase/activity_log?module=bud'); ?>" class="btn btn-primary pull-left mleft5" target="_blank"><?php echo _l('activity_log'); ?></a>
    <a href="<?php echo admin_url('estimates/pipeline/' . $switch_pipeline); ?>"
        class="btn btn-default mleft5 pull-left switch-pipeline hidden-xs" data-toggle="tooltip" data-placement="top"
        data-title="<?php echo _l('switch_to_pipeline'); ?>">
        <i class="fa-solid fa-grip-vertical"></i>
    </a>
    <div class="display-block pull-right tw-space-x-0 sm:tw-space-x-1.5">
        <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
            onclick="toggle_small_view('.table-estimates','#estimate'); return false;" data-toggle="tooltip"
            title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
        <a href="#" class="btn btn-default btn-with-tooltip estimates-total"
            onclick="slideToggle('#stats-top'); init_estimates_total(true); return false;" data-toggle="tooltip"
            title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
    </div>
    <div class="clearfix"></div>
    <div class="row tw-mt-2 sm:tw-mt-4">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row all_filters">
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
                        $statuses_filter = get_module_filter($module_name, 'statuses');
                        $statuses_filter_val = !empty($statuses_filter) ? explode(",", $statuses_filter->filter_value) : [];
                        ?>
                        <div class="col-md-3 form-group">
                            <label for="status"><?php echo _l('Status'); ?></label>
                            <select name="statuses[]" id="statuses" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                <?php foreach (estimate_statuses() as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if (in_array($key, $statuses_filter_val)) { echo 'selected'; } ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-1 form-group">
                            <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                                <?php echo _l('reset_filter'); ?>
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-separator" />

                    <div class="btn-group show_hide_columns" id="show_hide_columns" style="position: absolute !important;
        z-index: 99999;
        left: 204px !important">
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
                                'Budget #',
                                'Budgeted Amount',
                                'Change Order Amount',
                                'Total Amount',
                                'Invoiced Amount',
                                'Remaining Amount',
                                _l('estimates_total_tax'),
                                _l('invoice_estimate_year'),
                                _l('estimate_dt_table_heading_client'),
                                _l('project'),
                                _l('estimate_dt_table_heading_date'),
                                _l('estimate_dt_table_heading_status'),
                                _l('tags'),
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
                    <!-- if estimateid found in url -->
                    <?php echo form_hidden('estimateid', $estimateid); ?>
                    <?php $this->load->view('admin/estimates/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="estimate" class="hide">
            </div>
        </div>
    </div>
</div>