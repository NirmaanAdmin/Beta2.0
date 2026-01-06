<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
.unawarded-title {
    text-align: center;
    font-weight: bold;
}
</style>
<?php echo form_hidden('_attachment_sale_id', $estimate->id); ?>
<?php echo form_hidden('_attachment_sale_type', 'estimate'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="preview-tabs-top panel-full-width-tabs">
                <!-- <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div> -->
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                                <?php echo _l('estimate'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#budget_dashboard" aria-controls="budget_dashboard" role="tab" data-toggle="tab">
                                Budget Dashboard
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tender_strategy" aria-controls="tender_strategy" role="tab" data-toggle="tab">
                                Tender Strategy
                            </a>
                        </li>

                        <?php
                        $revisions = get_estimate_revision_chain($estimate->id);
                        if(!empty($revisions)) { ?>
                            <li role="presentation" class="dropdown">
                                <a href="#" class="dropdown-toggle" id="tab_child_items" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo _l('revisions'); ?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tab_child_items" style="width: max-content;">
                                    <?php
                                    foreach ($revisions as $key => $revision) { ?>
                                        <li>
                                            <a href="#tab_revisions_<?php echo $revision; ?>" aria-controls="tab_revisions_<?php echo $revision; ?>" role="tab" data-toggle="tab">Revision <?php echo $key; ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>

                        <li role="presentation">
                            <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                            <?php echo _l('attachment'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_tasks"
                                onclick="init_rel_tasks_table(<?php echo e($estimate->id); ?>,'estimate'); return false;"
                                aria-controls="tab_tasks" role="tab" data-toggle="tab">
                                <?php echo _l('tasks'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                                <?php echo _l('estimate_view_activity_tooltip'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_reminders"
                                onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $estimate->id ; ?> + '/' + 'estimate', undefined, undefined, undefined,[1,'asc']); return false;"
                                aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                <?php echo _l('estimate_reminders'); ?>
                                <?php
                        $total_reminders = total_rows(
    db_prefix() . 'reminders',
    [
                           'isnotified' => 0,
                           'staff'      => get_staff_user_id(),
                           'rel_type'   => 'estimate',
                           'rel_id'     => $estimate->id,
                           ]
);
                        if ($total_reminders > 0) {
                            echo '<span class="badge">' . $total_reminders . '</span>';
                        }
                        ?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator">
                            <a href="#tab_notes"
                                onclick="get_sales_notes(<?php echo e($estimate->id); ?>,'estimates'); return false"
                                aria-controls="tab_notes" role="tab" data-toggle="tab">
                                <?php echo _l('estimate_notes'); ?>
                                <span class="notes-total">
                                    <?php if ($totalNotes > 0) { ?>
                                    <span class="badge"><?php echo e($totalNotes); ?></span>
                                    <?php } ?>
                                </span>
                            </a>
                        </li>
                        <?php /*
                        <li role="presentation" data-toggle="tooltip" title="<?php echo _l('emails_tracking'); ?>"
                            class="tab-separator">
                            <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab"
                                data-toggle="tab">
                                <?php if (!is_mobile()) { ?>
                                <i class="fa-regular fa-envelope-open" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?php echo _l('emails_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('view_tracking'); ?>"
                            class="tab-separator">
                            <a href="#tab_views" aria-controls="tab_views" role="tab" data-toggle="tab">
                                <?php if (!is_mobile()) { ?>
                                <i class="fa fa-eye"></i>
                                <?php } else { ?>
                                <?php echo _l('view_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        */ ?>
                        <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>"
                            class="tab-separator toggle_view">
                            <a href="#" onclick="small_table_full_view(); return false;">
                                <i class="fa fa-expand"></i></a>
                        </li>
                        <?php hooks()->do_action('after_admin_estimate_preview_template_tab_menu_last_item', $estimate); ?>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
                    <div class="row">
                        <div class="col-md-3">
                            <?php echo format_estimate_status($estimate->status, 'mtop5 inline-block'); ?>
                        </div>
                        <div class="col-md-9">
                            <div class="visible-xs">
                                <div class="mtop10"></div>
                            </div>
                            <div class="pull-right _buttons">
                                <a href="#" class="btn btn-primary" onclick="assign_unawarded_capex(<?php echo $estimate->id; ?>); return false;">Tender Strategy</a>
                                <a href="#" class="btn btn-primary" onclick="create_new_revision(<?php echo $estimate->id; ?>); return false;"><i class="fa-regular fa-plus tw-mr-1"></i><?php echo _l('create_new_revision'); ?></a>
                                <?php if (staff_can('edit', 'estimates')) { ?>
                                <?php
                                $tooltip_text = $estimate->lock_budget == 1 ? "Unlock the budget for editing" : _l('edit_estimate_tooltip');
                                $disabled_class = $estimate->lock_budget == 1 ? 'disabled' : '';
                                ?>
                                <span data-toggle="tooltip" title="<?php echo $tooltip_text; ?>" data-placement="bottom">
                                    <a href="<?php echo admin_url('estimates/estimate/' . $estimate->id); ?>"
                                       class="btn btn-default btn-with-tooltip <?php echo $disabled_class; ?>"
                                       >
                                       <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                </span>
                                <?php } ?>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"><i class="fa-regular fa-file-pdf"></i><?php if (is_mobile()) {
                                    echo ' PDF';
                                } ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li class="hidden-xs">
                                            <a
                                                href="<?php echo admin_url('estimates/pdf/' . $estimate->id . '?output_type=I'); ?>">
                                                <?php echo _l('view_pdf'); ?>
                                            </a>
                                        </li>
                                        <li class="hidden-xs">
                                            <a
                                                href="<?php echo admin_url('estimates/pdf/' . $estimate->id . '?output_type=I'); ?>"
                                                target="_blank">
                                                <?php echo _l('view_pdf_in_new_window'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a
                                                href="<?php echo admin_url('estimates/pdf/' . $estimate->id); ?>">
                                                <?php echo _l('download'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo admin_url('estimates/pdf/' . $estimate->id . '?print=true'); ?>"
                                                target="_blank">
                                                <?php echo _l('print'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <?php
                             $_tooltip              = _l('estimate_sent_to_email_tooltip');
                             $_tooltip_already_send = '';
                             if ($estimate->sent == 1) {
                                 $_tooltip_already_send = _l('estimate_already_send_to_client_tooltip', time_ago($estimate->datesend));
                             }
                             ?>
                                <?php if (!empty($estimate->clientid)) { ?>
                                <a href="#" class="estimate-send-to-client btn btn-default btn-with-tooltip"
                                    data-toggle="tooltip" title="<?php echo e($_tooltip); ?>" data-placement="bottom"><span
                                        data-toggle="tooltip" data-title="<?php echo e($_tooltip_already_send); ?>"><i
                                            class="fa-regular fa-envelope"></i></span></a>
                                <?php } ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default pull-left dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo _l('more'); ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <?php /*
                                        <li>
                                            <a href="<?php echo site_url('estimate/' . $estimate->id . '/' . $estimate->hash) ?>"
                                                target="_blank">
                                                <?php echo _l('view_estimate_as_client'); ?>
                                            </a>
                                        </li>
                                        */ ?>
                                        <?php hooks()->do_action('after_estimate_view_as_client_link', $estimate); ?>
                                        <?php if ((!empty($estimate->expirydate) && date('Y-m-d') < $estimate->expirydate && ($estimate->status == 2 || $estimate->status == 5)) && is_estimates_expiry_reminders_enabled()) { ?>
                                        <li>
                                            <a
                                                href="<?php echo admin_url('estimates/send_expiry_reminder/' . $estimate->id); ?>">
                                                <?php echo _l('send_expiry_reminder'); ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <li>
                                            <a href="#" data-toggle="modal"
                                                data-target="#sales_attach_file"><?php echo _l('invoice_attach_file'); ?></a>
                                        </li>
                                        <?php if (staff_can('create', 'projects') && $estimate->project_id == 0) { ?>
                                        <li>
                                            <a
                                                href="<?php echo admin_url("projects/project?via_estimate_id={$estimate->id}&customer_id={$estimate->clientid}") ?>">
                                                <?php echo _l('estimate_convert_to_project'); ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if ($estimate->invoiceid == null) {
                                 if (staff_can('edit', 'estimates')) {
                                     foreach ($estimate_statuses as $status) {
                                         if ($estimate->status != $status) { ?>
                                        <li>
                                            <a
                                                href="<?php echo admin_url() . 'estimates/mark_action_status/' . $status . '/' . $estimate->id; ?>">
                                                <?php echo e(_l('estimate_mark_as', format_estimate_status($status, '', false))); ?></a>
                                        </li>
                                        <?php }
                                     } ?>
                                        <?php } ?>
                                        <?php } ?>
                                        <?php if (staff_can('create', 'estimates')) { ?>
                                        <li>
                                            <a href="<?php echo admin_url('estimates/copy/' . $estimate->id); ?>">
                                                <?php echo _l('copy_estimate'); ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if (!empty($estimate->signature) && staff_can('delete', 'estimates')) { ?>
                                        <li>
                                            <a href="<?php echo admin_url('estimates/clear_signature/' . $estimate->id); ?>"
                                                class="_delete">
                                                <?php echo _l('clear_signature'); ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if (staff_can('delete', 'estimates')) { ?>
                                        <?php
                                        if ((get_option('delete_only_on_last_estimate') == 1 && is_last_estimate($estimate->id)) || 
                                            (get_option('delete_only_on_last_estimate') == 0)) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('estimates/delete/' . $estimate->id); ?>"
                                                class="text-danger delete-text _delete">
                                                    <?php echo _l('delete_estimate_tooltip'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php if ($estimate->invoiceid == null) { ?>
                                <?php if (staff_can('create', 'invoices') && !empty($estimate->clientid)) { ?>
                                <div class="btn-group pull-right mleft5 hide">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <?php echo _l('estimate_convert_to_invoice'); ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a
                                                href="<?php echo admin_url('estimates/convert_to_invoice/' . $estimate->id . '?save_as_draft=true'); ?>"><?php echo _l('convert_and_save_as_draft'); ?>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a
                                                href="<?php echo admin_url('estimates/convert_to_invoice/' . $estimate->id); ?>"><?php echo _l('convert'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <?php } ?>
                                <?php } else { ?>
                                <a href="<?php echo admin_url('invoices/list_invoices/' . $estimate->invoice->id); ?>"
                                    data-placement="bottom" data-toggle="tooltip"
                                    title="<?php echo e(_l('estimate_invoiced_date', _dt($estimate->invoiced_date))); ?>"
                                    class="btn btn-primary mleft10"><?php echo e(format_invoice_number($estimate->invoice->id)); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-separator" />

                    <?php if (isset($estimate->scheduled_email) && $estimate->scheduled_email) { ?>
                    <div class="alert alert-warning">
                        <?php echo e(_l('invoice_will_be_sent_at', _dt($estimate->scheduled_email->scheduled_at))); ?>
                        <?php if (staff_can('edit', 'estimates') || $estimate->addedfrom == get_staff_user_id()) { ?>
                        <a href="#"
                            onclick="edit_estimate_scheduled_email(<?php echo $estimate->scheduled_email->id; ?>); return false;">
                            <?php echo _l('edit'); ?>
                        </a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <div id="estimate-preview">
                        <div class="row">
                            <?php if ($estimate->status == 4 && !empty($estimate->acceptance_firstname) && !empty($estimate->acceptance_lastname) && !empty($estimate->acceptance_email)) { ?>
                            <div class="col-md-12">
                                <div class="alert alert-info mbot15">
                                    <?php echo _l('accepted_identity_info', [
                                        _l('estimate_lowercase'),
                                        '<b>' . e($estimate->acceptance_firstname) . ' ' . e($estimate->acceptance_lastname) . '</b> (<a href="mailto:' . e($estimate->acceptance_email) . '">' . e($estimate->acceptance_email) . '</a>)',
                                        '<b>' . e(_dt($estimate->acceptance_date)) . '</b>',
                                        '<b>' . e($estimate->acceptance_ip) . '</b>' . (is_admin() ? '&nbsp;<a href="' . admin_url('estimates/clear_acceptance_info/' . $estimate->id) . '" class="_delete text-muted" data-toggle="tooltip" data-title="' . _l('clear_this_information') . '"><i class="fa fa-remove"></i></a>' : ''),
                                    ]); ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($estimate->project_id) { ?>
                            <div class="col-md-12">
                            <h4 class="font-medium mbot15">
                                <?php echo _l('related_to_project', [
                                    _l('estimate_lowercase'),
                                    _l('project_lowercase'),
                                    '<a href="' . admin_url('projects/view/' . $estimate->project_id) . '" target="_blank">' . e($estimate->project_data->name) . '</a>',
                                ]); ?>
                            </h4>
                            </div>
                            <?php } ?>
                            <div class="col-md-6 col-sm-6">
                                <h4 class="bold">
                                    <?php
                              $tags = get_tags_in($estimate->id, 'estimate');
                              if (count($tags) > 0) {
                                  echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="' . e(implode(', ', $tags)) . '"></i>';
                              }
                              ?>
                                    <a href="<?php echo admin_url('estimates/estimate/' . $estimate->id); ?>">
                                        <span id="estimate-number">
                                            <?php echo e(format_estimate_number($estimate->id)); ?>
                                            <?php
                                            if(!empty($estimate->budget_description)) {
                                                echo " (".$estimate->budget_description.")";
                                            }
                                            ?>
                                            <?php echo get_estimate_revision_no($estimate->id); ?>
                                        </span>
                                    </a>
                                </h4>
                                <address class="tw-text-neutral-500">
                                    <?php echo format_organization_info(); ?>
                                </address>
                            </div>
                            <div class="col-sm-6 text-right">
                                <?php
                                if($estimate->lock_budget == 1) { ?>
                                    <a href="#" class="btn btn-primary" onclick="update_lock_budget(<?php echo $estimate->id; ?>, '0'); return false;"><i class="fa fa-unlock"></i> Click Here for Unlock the Budget</a>
                                    <br>
                                <?php } else { ?>
                                    <a href="#" class="btn btn-primary" onclick="update_lock_budget(<?php echo $estimate->id; ?>, '1'); return false;"><i class="fa fa-lock"></i> Click Here for Lock the Budget</a>
                                    <br>
                                <?php } ?>
                                <?php
                                if($estimate->total_unalloc_cost != null) { 
                                    if($estimate->total_unalloc_cost > 0) { ?>
                                        <h4 class="bold text-warning">Budget is partially assigned.</h4>
                                    <?php } else if($estimate->total_unalloc_cost == 0) { ?>
                                        <h4 class="bold text-success">Budget is fully assigned.</h4>
                                    <?php } else {}
                                } ?>
                                <span class="bold"><?php echo _l('estimate_to'); ?></span>
                                <address class="tw-text-neutral-500">
                                    <?php echo format_customer_info($estimate, 'estimate', 'billing', true); ?>
                                </address>
                                <?php if ($estimate->include_shipping == 1 && $estimate->show_shipping_on_estimate == 1) { ?>
                                <span class="bold"><?php echo _l('ship_to'); ?></span>
                                <address class="tw-text-neutral-500">
                                    <?php echo format_customer_info($estimate, 'estimate', 'shipping'); ?>
                                </address>
                                <?php } ?>
                                <p class="no-mbot">
                                    <span class="bold">
                                        <?php echo _l('estimate_data_date'); ?>:
                                    </span>
                                    <?php echo e($estimate->date); ?>
                                </p>
                                <?php if (!empty($estimate->expirydate)) { ?>
                                <p class="no-mbot">
                                    <span class="bold"><?php echo _l('estimate_data_expiry_date'); ?>:</span>
                                    <?php echo e($estimate->expirydate); ?>
                                </p>
                                <?php } ?>
                                <?php if (!empty($estimate->reference_no)) { ?>
                                <p class="no-mbot">
                                    <span class="bold"><?php echo _l('reference_no'); ?>:</span>
                                    <?php echo e($estimate->reference_no); ?>
                                </p>
                                <?php } ?>
                                <?php if ($estimate->sale_agent && get_option('show_sale_agent_on_estimates') == 1) { ?>
                                <p class="no-mbot">
                                    <span class="bold"><?php echo _l('sale_agent_string'); ?>:</span>
                                    <?php echo e(get_staff_full_name($estimate->sale_agent)); ?>
                                </p>
                                <?php } ?>
                                <?php if ($estimate->project_id && get_option('show_project_on_estimate') == 1) { ?>
                                <p class="no-mbot">
                                    <span class="bold"><?php echo _l('project'); ?>:</span>
                                    <?php echo e(get_project_name_by_id($estimate->project_id)); ?>
                                </p>
                                <?php } ?>
                                <?php $pdf_custom_fields = get_custom_fields('estimate', ['show_on_pdf' => 1]);
                           foreach ($pdf_custom_fields as $field) {
                               $value = get_custom_field_value($estimate->id, $field['id'], 'estimate');
                               if ($value == '') {
                                   continue;
                               } ?>
                                <p class="no-mbot">
                                    <span class="bold"><?php echo e($field['name']); ?>: </span>
                                    <?php echo $value; ?>
                                </p>
                                <?php
                           } ?>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-separator" />
                        <div class="row">
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#final_estimate" aria-controls="final_estimate" role="tab" id="tab_final_estimate" data-toggle="tab">
                                            <?php echo _l('project_brief'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#area_summary" aria-controls="area_summary" role="tab" id="tab_area_summary" data-toggle="tab">
                                            <?php echo _l('area_summary'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#area_working" aria-controls="area_working" role="tab" id="tab_area_working" data-toggle="tab">
                                            <?php echo _l('area_working'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#budget_summary" aria-controls="budget_summary" role="tab" id="tab_budget_summary" data-toggle="tab">
                                            <?php echo _l('cost_plan_summary'); ?>
                                        </a>
                                    </li>
                                    <?php
                                    $annexures = get_all_annexures(); ?>
                                    <li role="presentation" class="dropdown">
                                        <a href="#" class="dropdown-toggle" id="tab_child_items" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo _l('detailed_costing_technical_assumptions'); ?>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu estimate-annexture-list" aria-labelledby="tab_child_items" style="width: max-content;">
                                            <?php
                                            foreach ($annexures as $key => $annexure) { ?>
                                                <li>
                                                    <a href="#<?php echo $annexure['annexure_key']; ?>" aria-controls="<?php echo $annexure['annexure_key']; ?>" role="tab" id="tab_<?php echo $annexure['annexure_key']; ?>" data-toggle="tab" class="tab_child_annexures">
                                                        <?php echo $annexure['name']; ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <li role="presentation">
                                        <a href="#project_timelines" aria-controls="project_timelines" role="tab" id="tab_project_timelines" data-toggle="tab">
                                            <?php echo _l('project_timelines'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="final_estimate">
                                    <div class="col-md-12">
                                        <?php echo $cost_planning_details['estimate_detail']['project_brief']; ?>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="project_timelines">
                                    <div class="col-md-12">
                                        <?php if (count($gantt_data) > 0) { ?>
                                        <div class="form-group">
                                            <select class="selectpicker" name="gantt_view">
                                                <option value="Day"><?php echo _l('gantt_view_day'); ?></option>
                                                <option value="Week"><?php echo _l('gantt_view_week'); ?></option>
                                                <option value="Month" selected><?php echo _l('gantt_view_month'); ?></option>
                                                <option value="Year"><?php echo _l('gantt_view_year'); ?></option>
                                            </select>
                                        </div>
                                        <div id="gantt"></div>
                                        <?php } else { ?>
                                        <p><?php echo _l('This project budget has no any milestones'); ?></p>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="budget_summary">
                                    <div class="table-responsive s_table">
                                        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                                            <thead>
                                                <tr>
                                                    <th width="25%" align="left"><?php echo _l('group_pur'); ?></th>
                                                    <th width="15%" align="right">Cost (INR)</th>
                                                    <th width="15%" align="right">Cost/BUA</th>
                                                    <th width="15%" align="right">Booked Amount</th>
                                                    <th width="15%" align="right">Pending Amount</th>
                                                    <th width="15%" align="right"><?php echo _l('remarks'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(!empty($cost_planning_details['annexure_estimate'])) {
                                                    $annexure_estimate = $cost_planning_details['annexure_estimate'];
                                                    $total_amount = 0;
                                                    $total_bua = 0;
                                                    $total_booked = 0;
                                                    $total_pending = 0;
                                                    foreach($annexure_estimate as $ikey => $svalue) {
                                                    $total_amount = $total_amount + $svalue['amount'];
                                                    $total_bua = $total_bua + $svalue['total_bua'];
                                                    $total_booked = $total_booked + $svalue['booked_amount'];
                                                    $total_pending = $total_pending + $svalue['pending_amount'];
                                                    ?>
                                                        <tr>
                                                            <td align="left">
                                                                <?php echo $svalue['name']; ?>
                                                            </td>
                                                            <td align="right">
                                                                <?php echo app_format_money($svalue['amount'], $base_currency); ?>
                                                            </td>
                                                            <td align="right">
                                                                <?php 
                                                                echo app_format_money($svalue['total_bua'], $base_currency); 
                                                                ?>
                                                            </td>
                                                            <td align="right">
                                                                <?php echo app_format_money($svalue['booked_amount'], $base_currency); ?>
                                                            </td>
                                                            <td align="right">
                                                                <?php echo app_format_money($svalue['pending_amount'], $base_currency); ?>
                                                            </td>
                                                            <td align="right">
                                                                <?php
                                                                if(!empty($cost_planning_details['budget_info'])) 
                                                                {
                                                                foreach ($cost_planning_details['budget_info'] as $cpkey => $cpvalue) 
                                                                {
                                                                    if($cpvalue['budget_id'] == $svalue['annexure']) {
                                                                        echo $cpvalue['budget_summary_remarks'];
                                                                    }
                                                                }
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php } 
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;">
                                                    <td align="left">Total</td>
                                                    <td align="right"><?php echo app_format_money($total_amount, $base_currency); ?></td>
                                                    <td align="right"><?php echo app_format_money($total_bua, $base_currency); ?></td>
                                                    <td align="right"><?php echo app_format_money($total_booked, $base_currency); ?></td>
                                                    <td align="right"><?php echo app_format_money($total_pending, $base_currency); ?></td>
                                                    <td align="right"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <?php echo $cost_planning_details['estimate_detail']['cost_plan_summary']; ?>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="area_summary">
                                    <?php
                                    $show_as_unit_name = $cost_planning_details['estimate_detail']['show_as_unit'] == 1 ? 'sqft' : 'sqm';
                                    ?>
                                    <div class="horizontal-tabs">
                                        <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                            <?php
                                            if(!empty($cost_planning_details['area_summary_tabs'])) { 
                                                foreach ($cost_planning_details['area_summary_tabs'] as $akey => $avalue) { ?>
                                                    <li role="presentation" class="<?php echo ($akey == 0) ? 'active' : ''; ?>">
                                                        <a href="#area_summary_<?php echo $avalue['id']; ?>" aria-controls="area_summary_<?php echo $avalue['id']; ?>" role="tab" id="tab_area_summary_<?php echo $avalue['id']; ?>" class="tab_sub_area_summary" data-toggle="tab" data-tab-id="<?php echo $avalue['id']; ?>">
                                                            <?php echo $avalue['name']; ?>
                                                        </a>
                                                    </li>
                                                <?php }
                                            } ?>
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        <?php
                                        if(!empty($cost_planning_details['area_summary_tabs'])) { 
                                            foreach ($cost_planning_details['area_summary_tabs'] as $akey => $avalue) { ?>
                                                <div role="tabpanel" class="tab-pane area_summary_tab <?php echo ($akey == 0) ? 'active' : ''; ?>" id="area_summary_<?php echo $avalue['id']; ?>" data-id="<?php echo $avalue['id']; ?>">
                                                    <div class="table-responsive s_table">
                                                        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                                                            <thead>
                                                                <tr>
                                                                    <th width="50%" align="left"><?php echo _l('floor'); ?>/<?php echo _l('area'); ?></th>
                                                                    <th width="50%" align="left"><?php echo _l('area'); ?> (<span class="show_as_unit_name"><?php echo $show_as_unit_name; ?></span>)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="area_summary">
                                                                <?php
                                                                if(!empty($cost_planning_details['all_area_summary'])) {
                                                                    $total_area_summary = 0;
                                                                    foreach ($cost_planning_details['all_area_summary'] as $askey => $item) {
                                                                    if($item['area_id'] == $avalue['id']) {
                                                                    $total_area_summary = $total_area_summary + $item['area'];
                                                                    $old_master_area = isset($root_estimate_data['all_area_summary'][$askey]['master_area']) ? $root_estimate_data['all_area_summary'][$askey]['master_area'] : '';
                                                                    $old_area = isset($root_estimate_data['all_area_summary'][$askey]['area']) ? $root_estimate_data['all_area_summary'][$askey]['area'] : '';
                                                                    ?>
                                                                    <tr>
                                                                        <td <?php echo find_estimate_revision_bold($old_master_area, $item['master_area']); ?>>
                                                                        <?php 
                                                                        if($avalue['id'] == 3) {
                                                                            echo get_functionality_area($item['master_area']); 
                                                                        } else {
                                                                            echo get_master_area($item['master_area']); 
                                                                        }
                                                                        ?></td>
                                                                        <td <?php echo find_estimate_revision_bold($old_area, $item['area']); ?>><?php echo $item['area']; ?></td>
                                                                    </tr>

                                                                    <?php } }
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-8 col-md-offset-4">
                                                        <table class="table text-right">
                                                            <tbody>
                                                                <tr>
                                                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('total_area'); ?> :</span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="total_area"></span> <?php echo $total_area_summary; ?><span class="show_as_unit_name"> <?php echo $show_as_unit_name; ?></span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="area_working">
                                    <?php
                                    $show_aw_unit_name = $cost_planning_details['estimate_detail']['show_aw_unit'] == 1 ? 'sqft' : 'sqm';
                                    ?>
                                    <div class="horizontal-tabs">
                                        <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                            <?php
                                            if(!empty($cost_planning_details['area_statement_tabs'])) { 
                                                foreach ($cost_planning_details['area_statement_tabs'] as $akey => $avalue) { ?>
                                                    <li role="presentation" class="<?php echo ($akey == 0) ? 'active' : ''; ?>">
                                                        <a href="#area_working_<?php echo $avalue['id']; ?>" aria-controls="area_working_<?php echo $avalue['id']; ?>" role="tab" id="tab_area_working_<?php echo $avalue['id']; ?>" class="tab_sub_area_working" data-toggle="tab" data-tab-id="<?php echo $avalue['id']; ?>">
                                                            <?php echo $avalue['name']; ?>
                                                        </a>
                                                    </li>
                                                <?php }
                                            } ?>
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        <?php
                                        if(!empty($cost_planning_details['area_statement_tabs'])) {
                                            foreach ($cost_planning_details['area_statement_tabs'] as $akey => $avalue) { ?>
                                                <div role="tabpanel" class="tab-pane area_working_tab <?php echo ($akey == 0) ? 'active' : ''; ?>" id="area_working_<?php echo $avalue['id']; ?>" data-id="<?php echo $avalue['id']; ?>">
                                                    <div class="table-responsive s_table">
                                                        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                                                            <thead>
                                                                <tr>
                                                                    <th width="40%" align="left">Room/Spaces</th>
                                                                    <th width="20%" align="left">Length (<?php echo $show_aw_unit_name; ?>)</th>
                                                                    <th width="20%" align="left">Width (<?php echo $show_aw_unit_name; ?>)</th>
                                                                    <th width="20%" align="left">Carpet Area (<?php echo $show_aw_unit_name; ?>)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="area_working">
                                                                <?php
                                                                if(!empty($cost_planning_details['area_working'])) {
                                                                $total_carpet_area = 0;
                                                                foreach ($cost_planning_details['area_working'] as $awkey => $item) {
                                                                if($item['area_id'] == $avalue['id']) {
                                                                $carpet_area = $item['area_length'] * $item['area_width'];
                                                                $total_carpet_area = $total_carpet_area + $carpet_area;
                                                                $old_area_description = isset($root_estimate_data['area_working'][$awkey]['area_description']) ? $root_estimate_data['area_working'][$awkey]['area_description'] : '';
                                                                $old_area_length = isset($root_estimate_data['area_working'][$awkey]['area_length']) ? $root_estimate_data['area_working'][$awkey]['area_length'] : '';
                                                                $old_area_width = isset($root_estimate_data['area_working'][$awkey]['area_width']) ? $root_estimate_data['area_working'][$awkey]['area_width'] : '';
                                                                ?>
                                                                <tr>
                                                                    <td <?php echo find_estimate_revision_bold($old_area_description, $item['area_description']); ?>>
                                                                        <?php echo clear_textarea_breaks($item['area_description']);?>
                                                                    </td>
                                                                    <td <?php echo find_estimate_revision_bold($old_area_length, $item['area_length']); ?>>
                                                                        <?php echo $item['area_length']; ?>
                                                                    </td>
                                                                    <td <?php echo find_estimate_revision_bold($old_area_width, $item['area_width']); ?>>
                                                                        <?php echo $item['area_width']; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $carpet_area; ?>
                                                                    </td>
                                                                </tr>
                                                                <?php } } } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-8 col-md-offset-4">
                                                        <table class="table text-right">
                                                            <tbody>
                                                                <tr>
                                                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('total_carpet_area'); ?> :</span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="total_carpet_area"><?php echo $total_carpet_area; ?></span> <span class="show_aw_unit_name"><?php echo $show_aw_unit_name; ?></span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>

            <?php
            $annexures = get_all_annexures(); 
            foreach ($annexures as $key => $annexure) { ?>
                <div role="tabpanel" class="tab-pane detailed-costing-tab" id="<?php echo $annexure['annexure_key']; ?>" data-id="<?php echo $annexure['id']; ?>">
                        <div class="col-md-4">
                            <p><?php echo _l('budget_head').': '.$annexure['name']; ?></p>
                            <p>Overall area (sqft):
                            <?php
                            $estimate_overall_budget_area = 1;
                            if(!empty($cost_planning_details['budget_info'])) 
                            {
                            foreach ($cost_planning_details['budget_info'] as $cpkey => $cpvalue) 
                            {
                                if($cpvalue['budget_id'] == $annexure['id']) {
                                    echo $cpvalue['overall_budget_area'];
                                    if(!empty($cpvalue['overall_budget_area'])) {
                                        $estimate_overall_budget_area = $cpvalue['overall_budget_area'];
                                    }
                                }
                            }
                            }
                            ?>
                            </p>
                        </div>
                        <div class="col-md-8 pull-right">
                            <button type="button" class="btn btn-info pull-right" id="download_historical_data" style="margin-left: 7px;"><?php echo _l('download_historical_data'); ?></button>
                            <button type="button" class="btn btn-info pull-right" id="cost_control_sheet"><?php echo _l('cost_control_sheet'); ?></button>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-3" style="padding-left: 0px;">
                                <?php 
                                $get_sub_group = get_budget_sub_head_project_wise();
                                echo render_select('estimate_sub_head_'.$annexure['id'], $get_sub_group, array('id', 'sub_group_name'), 'sub_head'); 
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                $get_area = get_area_project_wise();
                                echo render_select('estimate_area_'.$annexure['id'], $get_area, array('id', 'area_name'), 'area'); 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive s_table">
                                <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop table-table_estimate_items_<?php echo $annexure['id']; ?> scroll-responsive">
                                    <thead>
                                        <tr>
                                            <th width="13%"><?php echo _l('estimate_table_item_heading'); ?></th>
                                            <th width="14%"><?php echo _l('estimate_table_item_description'); ?></th>
                                            <th width="10%" class="qty"><?php echo _l('sub_head'); ?></th>
                                            <th width="12%" class="area"><?php echo _l('area'); ?></th>
                                            <th width="10%" class="qty"><?php echo e(_l('estimate_table_quantity_heading')); ?></th>
                                            <th width="11%"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                            <th width="10%"><?php echo _l('estimate_table_amount_heading'); ?></th>
                                            <th width="10%"><?php echo _l('Packages'); ?></th>
                                            <th width="9%"><?php echo _l('remarks'); ?></th>
                                        </tr>
                                        <tbody></tbody>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <?php
                                $estimate_item_amount = 0;
                                if(!empty($cost_planning_details['estimate_items'])) {
                                    foreach ($cost_planning_details['estimate_items'] as $ankey => $item) {
                                        if($item['annexure'] == $annexure['id']) {
                                            $amount = $item['rate'] * $item['qty'];
                                            $estimate_item_amount = $estimate_item_amount + $amount;
                                        }
                                    }
                                }
                                ?>
                                <tbody>
                                    <tr id="subtotal">
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('cost_overall_area'); ?> :</span>
                                        </td>
                                        <td>
                                            <?php 
                                            $cost_overall_area = $estimate_item_amount / $estimate_overall_budget_area;
                                            echo app_format_money($cost_overall_area, $base_currency);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('cost'); ?> :</span>
                                        </td>
                                        <td>
                                            <?php 
                                            echo app_format_money($estimate_item_amount, $base_currency);
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <?php
                            $detailed_costing_value = '';
                            if(!empty($cost_planning_details['budget_info'])) {
                                foreach ($cost_planning_details['budget_info'] as $ekey => $evalue) {
                                    if($evalue['budget_id'] == $annexure['id']) {
                                        $detailed_costing_value = $evalue['detailed_costing'];
                                    }
                                }
                            }
                            echo $detailed_costing_value; 
                            ?>
                        </div>
                </div>
            <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="cost_complete_modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 98%;">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">View Items</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <div class="col-md-3" style="padding-left: 0px; padding-top: 5px;">
                            <?php
                            echo render_select('cost_sub_head', $sub_groups_pur, array('id', 'sub_group_name'), 'Sub Head');
                            ?>
                          </div>
                        </div>

                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-12">
                              <div class="view_cost_control_sheet">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="tender_strategy">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-info pull-left mright10 display-block" data-toggle="modal" data-target="#addNewBulkPackage"><i class="fa fa-plus"></i> Add Bulk Package
                             </button>
                            <a href="#" class="btn btn-primary" onclick="view_package(<?php echo $estimate->id; ?>); return false;"><i class="fa-regular fa-plus tw-mr-1"></i>Add Package</a>
                            <hr />

                            <div class="col-md-2 form-group" style="padding-left: 0px;">
                               <select name="package_budget_head" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('group_pur'); ?>" data-actions-box="true">
                                  <option value=""></option>
                                  <?php foreach ($estimate_budget_listing as $head) { ?>
                                     <option value="<?php echo $head['annexure']; ?>"><?php echo $head['budget_head']; ?></option>
                                  <?php } ?>
                               </select>
                            </div>

                            <table class="dt-table-loading table table-table_unawarded_tracker">
                               <thead>
                                  <tr>
                                     <th><?php echo _l('Package Name'); ?></th>
                                     <th><?php echo _l('Preview'); ?></th>
                                     <th><?php echo _l('Budget Head'); ?></th>
                                     <th><?php echo _l('cat'); ?></th>
                                     <th><?php echo _l('rli_filter'); ?></th>
                                     <th><?php echo _l('Package Value'); ?></th>
                                     <th><?php echo _l('Awarded Value'); ?></th>
                                     <th><?php echo _l('Secured Deposit Value'); ?></th>
                                     <th><?php echo _l('Pending Value In Package'); ?></th>
                                     <th><?php echo _l('Package Status'); ?></th>
                                     <th><?php echo _l('Book Order'); ?></th>
                                  </tr>
                               </thead>
                               <tbody>
                               </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="budget_dashboard">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-2 form-group" style="padding-left: 0px;">
                               <select name="dashboard_budget_head" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('group_pur'); ?>" data-actions-box="true">
                                  <option value=""></option>
                                  <?php foreach ($estimate_budget_listing as $head) { ?>
                                     <option value="<?php echo $head['annexure']; ?>"><?php echo $head['budget_head']; ?></option>
                                  <?php } ?>
                               </select>
                            </div>

                            <table class="dt-table-loading table table-table_budget_usage">
                               <thead>
                                  <tr>
                                     <th><?php echo _l('Order Id'); ?></th>
                                     <th><?php echo _l('Description'); ?></th>
                                     <th><?php echo _l('Budget Head'); ?></th>
                                     <th><?php echo _l('Vendor Name'); ?></th>
                                     <th><?php echo _l('Amount'); ?></th>
                                  </tr>
                               </thead>
                               <tbody>
                               </tbody>
                            </table>
                        </div>
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <tr>
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('Total budgeted amount'); ?> :</span>
                                        </td>
                                        <td class="usage_budgeted_amount"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('Total order amount'); ?> :</span>
                                        </td>
                                        <td class="usage_order_amount"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('Total remaining amount'); ?> :</span>
                                        </td>
                                        <td class="usage_remaining_amount"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="attachment">
                    <?php if (count($estimate->attachments) > 0) { ?>
                    <div class="col-md-12">
                        <p class="bold text-muted"><?php echo _l('estimate_files'); ?></p>
                    </div>
                    <?php foreach ($estimate->attachments as $attachment) {
                        $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                        if (!empty($attachment['external'])) {
                            $attachment_url = $attachment['external_link'];
                        } ?>
                    <div class="mbot15 row col-md-12" data-attachment-id="<?php echo e($attachment['id']); ?>">
                        <div class="col-md-8">
                            <div class="pull-left"><i
                                    class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
                            <a href="<?php echo e($attachment_url); ?>"
                                target="_blank"><?php echo e($attachment['file_name']); ?></a>
                            <br />
                            <small class="text-muted"> <?php echo e($attachment['filetype']); ?></small>
                        </div>
                        <div class="col-md-4 text-right tw-space-x-2">
                            <?php if ($attachment['visible_to_customer'] == 0) {
                                $icon    = 'fa fa-toggle-off';
                                $tooltip = _l('show_to_customer');
                            } else {
                                $icon    = 'fa fa-toggle-on';
                                $tooltip = _l('hide_from_customer');
                            } ?>
                            <a href="#" data-toggle="tooltip"
                                onclick="toggle_file_visibility(<?php echo e($attachment['id']); ?>,<?php echo e($estimate->id); ?>,this); return false;"
                                data-title="<?php echo e($tooltip); ?>"><i class="<?php echo e($icon); ?> fa-lg"
                                    aria-hidden="true"></i></a>
                            <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
                            <a href="#" class="text-danger"
                                onclick="delete_estimate_attachment(<?php echo e($attachment['id']); ?>); return false;"><i
                                    class="fa fa-times fa-lg"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>

                <?php
                $revisions = get_estimate_revision_chain($estimate->id);
                if(!empty($revisions)) {
                    foreach ($revisions as $key => $revision) { ?>
                        <div role="tabpanel" class="tab-pane" id="tab_revisions_<?php echo $revision; ?>">
                        <?php echo render_estimate_revision_template($revision); ?>
                        </div>
                    <?php } 
                } ?>

                <div role="tabpanel" class="tab-pane" id="tab_tasks">
                    <?php init_relation_tasks_table(['data-new-rel-id' => $estimate->id, 'data-new-rel-type' => 'estimate'], 'tasksFilters'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_reminders">
                    <a href="#" data-toggle="modal" class="btn btn-primary"
                        data-target=".reminder-modal-estimate-<?php echo e($estimate->id); ?>"><i
                            class="fa-regular fa-bell"></i>
                        <?php echo _l('estimate_set_reminder_title'); ?></a>
                    <hr />
                    <?php render_datatable([ _l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                    <?php $this->load->view('admin/includes/modals/reminder', ['id' => $estimate->id, 'name' => 'estimate', 'members' => $members, 'reminder_title' => _l('estimate_set_reminder_title')]); ?>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_emails_tracking">
                <?php
                    $this->load->view('admin/includes/emails_tracking', [
                        'tracked_emails' => get_tracked_emails($estimate->id, 'estimate'), 
                    ]);
                ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_notes">
                    <?php echo form_open(admin_url('estimates/add_note/' . $estimate->id), ['id' => 'sales-notes', 'class' => 'estimate-notes-form']); ?>
                    <?php echo render_textarea('description'); ?>
                    <div class="text-right">
                        <button type="submit"
                            class="btn btn-primary mtop15 mbot15">
                            <?php echo _l('estimate_add_note'); ?>
                        </button>
                    </div>
                    <?php echo form_close(); ?>
                    <hr />
                    <div class="mtop20" id="sales_notes_area">
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_activity">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="activity-feed">
                            <?php foreach ($activity as $activity) {
                             $_custom_data = false; ?>
                                <div class="feed-item" data-sale-activity-id="<?php echo e($activity['id']); ?>">
                                    <div class="date">
                                        <span class="text-has-action" data-toggle="tooltip"
                                            data-title="<?php echo e(_dt($activity['date'])); ?>">
                                            <?php echo e(time_ago($activity['date'])); ?>
                                        </span>
                                    </div>
                                    <div class="text">
                                        <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                                        <a href="<?php echo admin_url('profile/' . $activity['staffid']); ?>">
                                            <?php echo staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                 ?>
                                        </a>
                                        <?php } ?>
                                        <?php
                                 $additional_data = '';
                      if (!empty($activity['additional_data'])) {
                          $additional_data = app_unserialize($activity['additional_data']);
                          $i               = 0;
                          foreach ($additional_data as $data) {
                              if (strpos($data, '<original_status>') !== false) {
                                  $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                                  $additional_data[$i] = format_estimate_status($original_status, '', false);
                              } elseif (strpos($data, '<new_status>') !== false) {
                                  $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                                  $additional_data[$i] = format_estimate_status($new_status, '', false);
                              } elseif (strpos($data, '<status>') !== false) {
                                  $status              = get_string_between($data, '<status>', '</status>');
                                  $additional_data[$i] = format_estimate_status($status, '', false);
                              } elseif (strpos($data, '<custom_data>') !== false) {
                                  $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                  unset($additional_data[$i]);
                              }
                              $i++;
                          }
                      }

                      $_formatted_activity = _l($activity['description'], $additional_data);

                      if ($_custom_data !== false) {
                          $_formatted_activity .= ' - ' . $_custom_data;
                      }

                      if (!empty($activity['full_name'])) {
                          $_formatted_activity = e($activity['full_name']) . ' - ' . $_formatted_activity;
                      }

                      echo $_formatted_activity;

                      if (is_admin()) {
                          echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa fa-remove"></i></a>';
                      } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_views">
                    <?php
                  $views_activity = get_views_tracking('estimate', $estimate->id);
                  if (count($views_activity) === 0) {
                      echo '<h4 class="tw-m-0 tw-text-base tw-font-medium tw-text-neutral-500">' . _l('not_viewed_yet', _l('estimate_lowercase')) . '</h4>';
                  }
                  foreach ($views_activity as $activity) { ?>
                    <p class="text-success no-margin">
                        <?php echo _l('view_date') . ': ' . _dt($activity['date']); ?>
                    </p>
                    <p class="text-muted">
                        <?php echo _l('view_ip') . ': ' . $activity['view_ip']; ?>
                    </p>
                    <hr />
                    <?php } ?>
                </div>
                <?php hooks()->do_action('after_admin_estimate_preview_template_tab_content_last_item', $estimate); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unawarded_capex_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <?php echo form_open(admin_url('estimates/add_assign_unawarded_capex'), array('id' => 'unawarded_capex_form', 'class' => '')); ?>
         <?php echo form_hidden('estimate_id', $estimate->id); ?>
         <div class="modal-header">
            <h4 class="modal-title"><div class="unawarded_capex_title"></div></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="col-md-3 form-group" style="padding-left: 0px;">
                <label for="unawarded_budget_head" class="control-label"><?php echo _l('Budget Head'); ?></label>
                <select name="unawarded_budget_head" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                    <?php foreach ($estimate_budget_listing as $item) { ?>
                        <option value="<?php echo $item['annexure']; ?>"><?php echo $item['budget_head']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <?php 
                $get_sub_group = get_budget_sub_head_project_wise();
                echo render_select('unawarded_sub_head', $get_sub_group, array('id', 'sub_group_name'), 'sub_head'); 
                ?>
            </div>
            <div class="col-md-3">
                <?php 
                $get_area = get_area_project_wise();
                echo render_select('unawarded_area[]', $get_area, array('id', 'area_name'), 'area', [], array('multiple' => true, 'data-actions-box' => true, 'data-width' => '100%'));
                ?>
            </div>
         </div>
         <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <table class="table items table-table_unawarded_capex_items scroll-responsive">
                            <thead>
                                <tr>
                                    <th><?php echo _l('estimate_table_item_heading'); ?></th>
                                    <th><?php echo _l('estimate_table_item_description'); ?></th>
                                    <th><?php echo _l('area'); ?></th>
                                    <th><?php echo _l('sub_groups_pur'); ?></th>
                                    <th><?php echo _l('budgeted_qty'); ?></th>
                                    <th><?php echo _l('budgeted_rate'); ?></th>
                                    <th><?php echo _l('budgeted_amount'); ?></th>
                                    <th>Packages</th>
                                    <!-- <th>Remaining Amount In Budget</th>
                                    <th>Amount Booked In Package</th>
                                    <th>Amount Booked In Order</th> -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<div class="modal fade" id="package_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <?php echo form_open(admin_url('estimates/save_package'), array('id' => 'unawarded_package_form', 'class' => '')); ?>
         <div class="modal-header">
            <h4 class="modal-title"><div class="package_title"></div></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="package-head"></div>
         </div>
         <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="package-body">
                    </div>
                </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<div class="modal fade" id="cost_control_sheet_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><div class="cost_control_sheet_title"></div></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="cost-control-sheet-body">
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

<div class="modal fade" id="addNewBulkPackage" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 80%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('Add Bulk Package'); ?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="col-md-4 pull-right">
                <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>
                <div class="form-group">
                  <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadbulkpackagecsv(this);"><?php echo _l('import'); ?></button>
                  <a href="<?php echo site_url('uploads/estimates/file_sample/Sample_bulk_package_en.xlsx') ?>" class="btn btn-primary">Template</a>
                </div>
            </div>
            <div class="col-md-12">
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
                     echo form_open_multipart(admin_url('estimates/add_bulk_package'), array('id' => 'bulk_package_form')); 
                     ?>
                     <table class="table items table_bulk_package">
                        <thead>
                           <tr>
                              <th align="left" width="19%"><?php echo _l('Budget Head'); ?></th>
                              <th align="left" width="19%"><?php echo _l('Project Awarded Date'); ?></th>
                              <th align="left" width="19%"><?php echo _l('Package Name'); ?></th>
                              <th align="left" width="19%"><?php echo _l('cat'); ?></th>
                              <th align="left" width="19%"><?php echo _l('rli_filter'); ?></th>
                              <th align="center" width="5%"><i class="fa fa-cog"></i></th>
                           </tr>
                        </thead>
                        <tbody style="border: 1px solid #ddd;">
                            <?php echo form_hidden('bulk_estimate_id', $estimate->id); ?>
                            <tr class="main">
                                <td align="left">
                                    <?php echo get_package_budget_head_dropdown($estimate->id, 'bulk_budget_head', ''); ?>
                                </td>
                                <td align="left">
                                    <?php echo render_date_input('bulk_project_awarded_date', '', _d(date('Y-m-d'))); ?>
                                </td>
                                <td align="left">
                                    <?php echo render_input('bulk_package_name'); ?>
                                </td>
                                <td align="left">
                                    <?php echo get_package_kind_dropdown('bulk_kind', ''); ?>
                                </td>
                                <td align="left">
                                    <?php echo get_package_rli_filter_dropdown('bulk_rli_filter', ''); ?>
                                </td>
                                <td align="center">
                                    <button type="button"
                                        onclick="add_bulk_package_item_to_table(
                                            <?php echo $estimate->id; ?>,
                                            undefined,
                                            undefined
                                        ); return false;"
                                        class="btn pull-right btn-primary">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                     </table>
                     <button type="submit" class="btn btn-info pull-right"><?php echo _l('Save'); ?></button>
                     <?php echo form_close(); ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
init_items_sortable(true);
init_btn_with_tooltips();
init_datepicker();
init_selectpicker();
init_form_reminder();
init_tabs_scrollable();
<?php if ($send_later) { ?>
schedule_estimate_send(<?php echo e($estimate->id); ?>);
<?php } ?>
$("body").on('click', '#cost_control_sheet', function () {
  cost_control_sheet(this);
});

$("body").on('change', 'select[name="cost_sub_head"]', function () {
    cost_control_sheet(this);
});

$("body").on('click', '#download_historical_data', function() {
  var estimate_id = <?php echo e($estimate->id); ?>;
  var budget_head_id = $(this).closest('.detailed-costing-tab').data('id');
  if (estimate_id !== '' && budget_head_id !== '') {
    var url = admin_url + 'purchase/download_revision_historical_data?estimate_id='+encodeURIComponent(estimate_id)+'&budget_head_id=' + encodeURIComponent(budget_head_id);
    window.location.href = url;
  }
});

function cost_control_sheet(el) {
  var estimate_id = <?php echo e($estimate->id); ?>;
  var budget_head_id = $(el).closest('.detailed-costing-tab').data('id');
  $.post(admin_url + "estimates/cost_control_sheet", {
    id: estimate_id,
    unawarded_budget: budget_head_id,
  }).done(function (res) {
    var response = JSON.parse(res);
    if (response.itemhtml) {
      $('.cost-control-sheet-body').html('');
      $('.cost-control-sheet-body').html(response.itemhtml);
      $('.cost_control_sheet_title').html('View Items');
      init_selectpicker();
      calculate_cost_control_sheet();
      $('#cost_control_sheet_modal').modal('show');
    }
  });
}

var table_unawarded_capex_items;
function assign_unawarded_capex(id) {
    var estimate_id = <?php echo e($estimate->id); ?>;
    var tableSelector = '.table-table_unawarded_capex_items';
    var unawardedParams = {
        "unawarded_budget_head": "[name='unawarded_budget_head']",
        "unawarded_sub_head": "[name='unawarded_sub_head']",
        "unawarded_area": "[name='unawarded_area[]']",
    };
    if ($.fn.DataTable.isDataTable(tableSelector)) {
        $(tableSelector).DataTable().destroy();
    }
    table_unawarded_capex_items = initDataTable(
        tableSelector,
        admin_url + 'estimates/table_unawarded_capex_items/' + estimate_id,
        [],
        [],
        unawardedParams,
        [0, 'desc']
    );
    $(tableSelector).on('draw.dt', function () {
        $('select.selectpicker').selectpicker('render').selectpicker('refresh');
    });
    $('#unawarded_capex_modal').modal('show');
}
$(document).on(
    'change',
    'select[name="unawarded_budget_head"], select[name="unawarded_sub_head"], select[name="unawarded_area[]"]',
    function () {
        $(this).selectpicker('render').selectpicker('refresh');
        if (table_unawarded_capex_items) {
            table_unawarded_capex_items.ajax.reload(function () {
                $('select.selectpicker').selectpicker('render').selectpicker('refresh');
            }, false);
        }
    }
);

$('#unawarded_capex_form').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    var $submitBtn = $form.find('button[type="submit"]');
    $submitBtn.prop('disabled', true).text('<?php echo _l('Processing'); ?>');
    this.submit();
});

function calculate_unawarded_capex() {
  var total_budgeted_amount = 0;
  var rows = $(".unawarded-capex-body tbody tr");
  $.each(rows, function () {
    var row = $(this);
    var budgeted_qty = parseFloat(row.find(".all_budgeted_qty input").val()) || 0;
    var budgeted_rate = parseFloat(row.find(".all_budgeted_rate input").val()) || 0;
    var budgeted_amount = parseFloat(row.find(".all_budgeted_amount input").val()) || 0;
    total_budgeted_amount += budgeted_amount;
  });
  $(".total_budgeted_amount").html(format_money(total_budgeted_amount));
  $(document).trigger("sales-total-calculated");
}

function calculate_cost_control_sheet() {
  var total_budgeted_amount = 0,
  total_used_amount = 0,
  total_remaining_amount = 0;
  var rows = $(".cost-control-sheet-body tbody tr");
  $.each(rows, function () {
    var row = $(this);
    var budgeted_amount = parseFloat(row.find(".all_budgeted_amount input").val()) || 0;
    var used_amount = parseFloat(row.find(".all_used_amount input").val()) || 0;
    var remaining_amount = parseFloat(row.find(".all_remaining_amount input").val()) || 0;
    total_budgeted_amount += budgeted_amount;
    total_used_amount += used_amount;
    total_remaining_amount += remaining_amount;
  });
  $(".total_budgeted_amount").html(format_money(total_budgeted_amount));
  $(".total_used_amount").html(format_money(total_used_amount));
  $(".total_remaining_amount").html(format_money(total_remaining_amount));
  $(document).trigger("sales-total-calculated");
}

var table_unawarded_tracker;
var estimate_id = <?php echo e($estimate->id); ?>;
table_unawarded_tracker = $('.table-table_unawarded_tracker');
var packageParams = {
    "budget_head": "[name='package_budget_head']"
};
initDataTable('.table-table_unawarded_tracker', admin_url + 'purchase/table_unawarded_tracker/' + estimate_id, [], [], packageParams, [0, 'desc']);
$(document).on('change', 'select[name="package_budget_head"]', function () {
    $('select[name="package_budget_head"]').selectpicker('refresh');
    table_unawarded_tracker.DataTable().ajax.reload();
});

var table_budget_usage;
table_budget_usage = $('.table-table_budget_usage');
var dashboardParams = {
    "budget_head": "[name='dashboard_budget_head']"
};
initDataTable('.table-table_budget_usage', admin_url + 'estimates/table_budget_usage/' + estimate_id, [], [], dashboardParams, [0, 'desc']);
$(document).on('change', 'select[name="dashboard_budget_head"]', function () {
    $('select[name="dashboard_budget_head"]').selectpicker('refresh');
    table_budget_usage.DataTable().ajax.reload();
});
$('.table-table_budget_usage').on('draw.dt', function () {
    var reportsTable = $(this).DataTable();
    var sums = reportsTable.ajax.json().sums;
    $('tbody td.usage_budgeted_amount').html(sums.usage_budgeted_amount);
    $('tbody td.usage_order_amount').html(sums.usage_order_amount);
    $('tbody td.usage_remaining_amount').html(sums.usage_remaining_amount);
});

$('#unawarded_package_form').on('submit', function (e) {
    e.preventDefault();
    const form = this;
    const $form = $(form);
    if ($form.find('.pack_items').length > 0) {
        $('#package_modal').modal('hide');
        $('#package_modal').one('hidden.bs.modal', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to proceed? This action involves a budget revision, as differences between the current items and the locked budget have been detected.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const $submitBtn = $form.find('button[type="submit"]');
                    $submitBtn.prop('disabled', true).text('<?php echo _l('Processing'); ?>');
                    form.submit(); // use native submit to avoid recursion
                }
            });
        });
    } else {
        const $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('<?php echo _l('Processing'); ?>');
        form.submit();
    }
});

function update_lock_budget(id, status) {
    var lock_budget = status === '0' ? 'Unlock' : 'Lock';
    Swal.fire({
        title: 'Are you sure?',
        text: 'Are you sure you want to ' + lock_budget + ' the budget?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, ' + lock_budget + ' it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(admin_url + "estimates/update_lock_budget", {
                id: id,
                lock_budget: status 
            }).done(function (response) {
                response = JSON.parse(response);
                if (response && response.id) {
                    alert_float("success", lock_budget + " the budget has been successfully updated.");
                    window.location.href = admin_url + "estimates";
                } else {
                    alert_float("warning", "Failed to update the budget lock status.");
                }
            }).fail(function () {
                alert_float("warning", "An error occurred while updating the budget status.");
            });
        }
    });
}

var tabKeyCounts = 0;
function getNextItemKey() {
  if (!tabKeyCounts) {
    tabKeyCounts = 0;
  }
  tabKeyCounts += 1;
  return tabKeyCounts;
}

function add_bulk_package_item_to_table(estimateid, data, itemid) {
  data =
    typeof data == "undefined" || data == "undefined"
      ? get_bulk_package_item_preview_values()
      : data;

  var table_row = "";
  var item_key = getNextItemKey();

  table_row += '<tr class="items">';

  var bulk_budget_head = "newbulkpackageitems[" + item_key + "][bulk_budget_head]";
  var bulk_kind = "newbulkpackageitems[" + item_key + "][bulk_kind]";
  var bulk_rli_filter = "newbulkpackageitems[" + item_key + "][bulk_rli_filter]";
  $.when(
    get_package_budget_head_dropdown(estimateid, bulk_budget_head, data.bulk_budget_head),
    get_package_kind_dropdown(bulk_kind, data.bulk_kind),
    get_package_rli_filter_dropdown(bulk_rli_filter, data.bulk_rli_filter)
  ).done(function (package_budget_head_dropdown, package_kind_dropdown, package_rli_filter_dropdown) {
    table_row += '<td>' + package_budget_head_dropdown[0] + '</td>';

    table_row +=
      '<td><input type="text" name="newbulkpackageitems[' +
      item_key +
      '][bulk_project_awarded_date]" value="' +
      data.bulk_project_awarded_date +
      '" class="form-control datepicker"></td>';

    table_row +=
      '<td><input type="text" name="newbulkpackageitems[' +
      item_key +
      '][bulk_package_name]" value="' +
      data.bulk_package_name +
      '" class="form-control"></td>';

    table_row += '<td>' + package_kind_dropdown[0] + '</td>';

    table_row += '<td>' + package_rli_filter_dropdown[0] + '</td>';

    table_row +=
      '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_bulk_package_item(this,' +
      itemid +
      '); return false;"><i class="fa fa-trash"></i></a></td>';

    table_row += "</tr>";

    $('.table_bulk_package tbody').append(table_row);

    $(document).trigger({
      type: "item-added-to-table",
      data: data,
      row: table_row,
    });

    if (
      $("#item_select").hasClass("ajax-search") &&
      $("#item_select").selectpicker("val") !== ""
    ) {
      $("#item_select").prepend("<option></option>");
    }

    init_selectpicker();
    init_datepicker();
    init_color_pickers();
    clear_bulk_package_item_preview_values();

    $("body").find("#items-warning").remove();
    $("body").find(".dt-loader").remove();
    $("#item_select").selectpicker("val", "");

    return true;
  });
  return false;
}

function get_bulk_package_item_preview_values() {
  var response = {};
  var tab = $('.table_bulk_package tbody');
  response.bulk_budget_head = tab.find('select[name="bulk_budget_head"]').val();
  response.bulk_project_awarded_date = tab.find('input[name="bulk_project_awarded_date"]').val();
  response.bulk_package_name = tab.find('input[name="bulk_package_name"]').val();
  response.bulk_kind = tab.find('select[name="bulk_kind"]').val();
  response.bulk_rli_filter = tab.find('select[name="bulk_rli_filter"]').val();
  return response;
}

function clear_bulk_package_item_preview_values(tab) {
  var previewArea = $('.table_bulk_package tbody').find("tr").eq(0);
  previewArea.find('input[name="bulk_package_name"]').val("");
  previewArea.find('select[name="bulk_kind"]').selectpicker("val", "");
  previewArea.find('select[name="bulk_rli_filter"]').selectpicker("val", "");
}

function get_package_budget_head_dropdown(estimateid, name, value) {
  return $.post(admin_url + "estimates/get_package_budget_head_dropdown", {
    estimateid: estimateid,
    name: name,
    value: value,
  });
}

function get_package_kind_dropdown(name, value) {
  return $.post(admin_url + "estimates/get_package_kind_dropdown", {
    name: name,
    value: value,
  });
}

function get_package_rli_filter_dropdown(name, value) {
  return $.post(admin_url + "estimates/get_package_rli_filter_dropdown", {
    name: name,
    value: value,
  });
}

function delete_bulk_package_item(row, itemid) {
    $(row)
    .parents("tr")
    .addClass("animated fadeOut", function () {
      setTimeout(function () {
        $(row).parents("tr").remove();
      }, 50);
    });
}

function uploadbulkpackagecsv() {
    "use strict";
    var fileInput = $('#file_csv')[0];
    var file = fileInput?.files[0];
    if (!file) {
      alert("Please select a file.");
      return;
    }
    var fileExtension = file.name.split('.').pop();
    if (fileExtension !== 'xlsx') {
      alert("Please upload a valid .xlsx file.");
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: 'array' });
        var firstSheetName = workbook.SheetNames[0];
        var worksheet = workbook.Sheets[firstSheetName];
        var jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });
        var totalRows = jsonData.length;

        if(totalRows > 0) {
          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0');
          var yyyy = today.getFullYear();
          var formattedDate = dd + '-' + mm + '-' + yyyy;
          var cleanedData = jsonData.map(row => ({
            bulk_package_name: row["Package Name"]?.trim() || "",
            bulk_project_awarded_date: formattedDate,
            bulk_budget_head: "",
            bulk_kind: "",
            bulk_rli_filter: "",
          }));
          if(cleanedData.length > 0) {
            cleanedData.forEach(function (row) {
                add_bulk_package_item_to_table(estimate_id, row);
            });
            fileInput.value = '';
          }
        }
    };
    reader.readAsArrayBuffer(file);
}

var currentTable = null;
var currentBudgetHead = null;
$('a.tab_child_annexures').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr("href"); 
    var $tabPane = $(target);
    var budget_head_id = $tabPane.data('id');
    var tableSelector = '.table-table_estimate_items_' + budget_head_id;
    if (currentTable !== null) {
        $(currentTable).DataTable().destroy();
    }
    var EstimateParams = {
        "sub_head": "[name='estimate_sub_head_" + budget_head_id + "']",
        "area": "[name='estimate_area_" + budget_head_id + "']"
    };
    initDataTable(
        tableSelector,
        admin_url + 'estimates/table_estimate_items/' + estimate_id + '/' + budget_head_id,
        [],
        [],
        EstimateParams,
        [0, 'desc']
    );
    currentTable = tableSelector;
    currentBudgetHead = budget_head_id;
    $(EstimateParams.sub_head + ', ' + EstimateParams.area).off('change').on('change', function () {
        $(tableSelector).DataTable().ajax.reload();
        $(EstimateParams.sub_head).selectpicker('refresh');
        $(EstimateParams.area).selectpicker('refresh');
    });
    $(EstimateParams.sub_head).selectpicker('refresh');
    $(EstimateParams.area).selectpicker('refresh');
    $(tableSelector).on('draw.dt', function () {
        $('select.selectpicker').selectpicker('render').selectpicker('refresh');
    });
});
$("body").on('change', 'select[name="view_item_package"]', function () {
    var budget_head_id = $(this).closest('.detailed-costing-tab').data('id');
    var item_id = $(this).data('item_id');
    var old_packages = $(this).data('old_packages');
    var packages = $(this).val();
    $.post(admin_url + "estimates/update_estimate_item_package", {
        estimate_id: estimate_id,
        unawarded_budget_head: budget_head_id,
        newitems: [
            {
                item_id: item_id,
                old_packages: old_packages,
                packages: packages
            }
        ]
    }).done(function (res) {
        var response = JSON.parse(res);
        if (response.success) {
            alert_float("success", response.message);
            $('.table-table_estimate_items_' + budget_head_id).DataTable().ajax.reload();
            table_unawarded_tracker.DataTable().ajax.reload();
        }
    });
});

var gantt_data = <?php echo json_encode($gantt_data); ?>;

if (gantt_data.length > 0) {
    var gantt = new Gantt("#gantt", gantt_data, {
        view_modes: ['Day', 'Week', 'Month', 'Year'],
        view_mode: 'Month',
        date_format: 'YYYY-MM-DD',
        popup_trigger: 'click mouseover',
        on_date_change: function(data, start, end) {
            if (typeof(data.task_id) != 'undefined') {
                $.post(admin_url + 'tasks/gantt_date_update/' + data.task_id, {
                    startdate: moment(start).format('YYYY-MM-DD'),
                    duedate: moment(end).format('YYYY-MM-DD'),
                });
            }
        },
        on_click: function(data) {
            if (typeof(data.task_id) != 'undefined') {
                init_task_modal(data.task_id);
            }
        }
    });

    $('body').on('mouseleave', '.grid-row', function() {
        gantt.hide_popup();
    })

    $('select[name$="gantt_view"').change(function(el) {
        let view = $(el.target).val();
        gantt.change_view_mode(view);
    })
}

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<?php require 'modules/purchase/assets/js/cost_planning_js.php'; ?>
<?php $this->load->view('admin/estimates/estimate_send_to_client'); ?>
