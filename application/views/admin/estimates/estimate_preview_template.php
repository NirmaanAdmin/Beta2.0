<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id', $estimate->id); ?>
<?php echo form_hidden('_attachment_sale_type', 'estimate'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                                <?php echo _l('estimate'); ?>
                            </a>
                        </li>
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
            <div class="row mtop20">
                <div class="col-md-3">
                    <?php echo format_estimate_status($estimate->status, 'mtop5 inline-block'); ?>
                </div>
                <div class="col-md-9">
                    <div class="visible-xs">
                        <div class="mtop10"></div>
                    </div>
                    <div class="pull-right _buttons">
                        <?php if (staff_can('edit', 'estimates')) { ?>
                        <a href="<?php echo admin_url('estimates/estimate/' . $estimate->id); ?>"
                            class="btn btn-default btn-with-tooltip" data-toggle="tooltip"
                            title="<?php echo _l('edit_estimate_tooltip'); ?>" data-placement="bottom"><i
                                class="fa-regular fa-pen-to-square"></i></a>
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
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
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
                                        </span>
                                    </a>
                                </h4>
                                <address class="tw-text-neutral-500">
                                    <?php echo format_organization_info(); ?>
                                </address>
                            </div>
                            <div class="col-sm-6 text-right">
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
                                                    <a href="#<?php echo $annexure['annexure_key']; ?>" aria-controls="<?php echo $annexure['annexure_key']; ?>" role="tab" id="tab_<?php echo $annexure['annexure_key']; ?>" data-toggle="tab">
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
                                        <?php echo $cost_planning_details['estimate_detail']['project_timelines']; ?>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="budget_summary">
                                    <div class="table-responsive s_table">
                                        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                                            <thead>
                                                <tr>
                                                    <th width="25%" align="left"><?php echo _l('group_pur'); ?></th>
                                                    <th width="25%" align="right">Cost (INR)</th>
                                                    <th width="25%" align="right">Cost/BUA</th>
                                                    <th width="25%" align="right"><?php echo _l('remarks'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(!empty($cost_planning_details['annexure_estimate'])) {
                                                    $annexure_estimate = $cost_planning_details['annexure_estimate'];
                                                    foreach($annexure_estimate as $ikey => $svalue) { ?>
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
                                                                    foreach ($cost_planning_details['all_area_summary'] as $item) {
                                                                    if($item['area_id'] == $avalue['id']) {
                                                                    $total_area_summary = $total_area_summary + $item['area'];
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                        <?php 
                                                                        if($avalue['id'] == 3) {
                                                                            echo get_functionality_area($item['master_area']); 
                                                                        } else {
                                                                            echo get_master_area($item['master_area']); 
                                                                        }
                                                                        ?></td>
                                                                        <td><?php echo $item['area']; ?></td>
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
                                                                foreach ($cost_planning_details['area_working'] as $item) {
                                                                if($item['area_id'] == $avalue['id']) {
                                                                $carpet_area = $item['area_length'] * $item['area_width'];
                                                                $total_carpet_area = $total_carpet_area + $carpet_area;
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php echo clear_textarea_breaks($item['area_description']);?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $item['area_length']; ?>
                                                                    </td>
                                                                    <td>
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
                    <?php if($annexure['id'] == 7) { ?>
                        <div class="col-md-4">
                            <p><?php echo _l('budget_head').': '.$annexure['name']; ?></p>
                        </div>
                        <div class="col-md-8">
                        </div>
                        <div class="table-responsive s_table">
                            <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                                <thead>
                                    <tr>
                                        <th width="15%" align="left"><?php echo _l('master_area'); ?></th>
                                        <th width="18%" align="left"><?php echo _l('functionality_area'); ?></th>
                                        <th width="15%" align="right"><?php echo _l('area'); ?></th>
                                        <th width="17%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                        <th width="17%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                                        <th width="18%" align="right"><?php echo _l('remarks'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $estimate_item_sub = 0;
                                    if(!empty($cost_planning_details['multilevel_items'])) {
                                        foreach ($cost_planning_details['multilevel_items'] as $iitem) { 
                                            $int_amount = $iitem['int_area'] * $iitem['int_rate'];
                                            $estimate_item_sub = $estimate_item_sub + $int_amount;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo get_master_area($iitem['int_master_area']); ?>
                                                    <br><br>
                                                    <button type="button" class="btn btn-info pull-left mright10 display-block" data-toggle="modal" data-target="#multilevelExpand_<?php echo $iitem['id']; ?>">Expand</button>
                                                </td>
                                                <td>
                                                    <?php echo get_functionality_area($iitem['int_fun_area']); ?>
                                                </td>
                                                <td align="right">
                                                    <?php echo $iitem['int_area']; ?>
                                                </td>
                                                <td align="right">
                                                    <?php echo $iitem['int_rate']; ?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($int_amount, 2); ?>
                                                </td>
                                                <td align="right">
                                                    <?php echo clear_textarea_breaks($iitem['int_remarks']); ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <tr id="subtotal">
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                                        </td>
                                        <td>
                                            <?php 
                                            echo app_format_money($estimate_item_sub, $base_currency);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                                        </td>
                                        <td>
                                            <?php 
                                            echo app_format_money($estimate_item_sub, $base_currency);
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
                    <?php } else { ?>
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
                        <div class="col-md-8">
                        </div>
                        <div class="table-responsive s_table">
                            <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                                <thead>
                                    <tr>
                                        <th width="13%" align="left"><?php echo _l('estimate_table_item_heading'); ?></th>
                                        <th width="18%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
                                        <th width="10%" class="qty" align="right"><?php echo e(_l('area')); ?></th>
                                        <th width="10%" class="qty" align="right"><?php echo e(_l('estimate_table_quantity_heading')); ?></th>
                                        <th width="16%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                        <th width="16%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                                        <th width="17%" align="right"><?php echo _l('remarks'); ?></th>
                                    </tr>
                                    <tbody>
                                        <?php
                                        $estimate_item_sub = 0;
                                        if(!empty($cost_planning_details['estimate_items'])) {
                                            foreach ($cost_planning_details['estimate_items'] as $item) {
                                                if($item['annexure'] == $annexure['id']) { 
                                                    $amount = $item['rate'] * $item['qty'];
                                                    $estimate_item_sub = $estimate_item_sub + $amount;
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo get_purchase_items($item['item_code']); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo clear_textarea_breaks($item['long_description']); ?>
                                                        </td>
                                                        <td align="right">
                                                            <?php echo $item['budget_area']; ?>
                                                        </td>
                                                        <td align="right">
                                                            <?php echo number_format($item['qty'], 2); ?>
                                                        </td>
                                                        <td align="right">
                                                            <?php echo $item['rate']; ?>
                                                        </td>
                                                        <td align="right">
                                                            <?php echo number_format($amount, 2); ?>
                                                        </td>
                                                        <td align="right">
                                                            <?php echo clear_textarea_breaks($item['remarks']); ?>
                                                        </td>
                                                    </tr>
                                            <?php } }
                                        } ?>
                                    </tbody>
                                </thead>
                            </table>
                        </div>
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <tr id="subtotal">
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                                        </td>
                                        <td>
                                            <?php 
                                            $annexure_subtotal = $estimate_item_sub / $estimate_overall_budget_area;
                                            echo app_format_money($annexure_subtotal, $base_currency);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                                        </td>
                                        <td>
                                            <?php 
                                            $annexure_total = $estimate_item_sub / $estimate_overall_budget_area;
                                            echo app_format_money($annexure_total, $base_currency);
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
                    <?php } ?>
                </div>
            <?php } ?>

            <?php
            foreach ($annexures as $key => $annexure) { ?>
                <div class="multilevel-sub-items-tab" id="<?php echo $annexure['annexure_key']; ?>" data-id="<?php echo $annexure['id']; ?>">
                    <?php if ($annexure['id'] == 7) {
                        if (!empty($cost_planning_details['multilevel_items'])) {
                            foreach ($cost_planning_details['multilevel_items'] as $iitem) {
                                $modals_html = '';
                                $modals_html .= '
                                <div class="modal fade" id="multilevelExpand_'.$iitem['id'].'" tabindex="-1" role="dialog">
                                  <div class="modal-dialog" role="document" style="width: 98%;">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h4 class="modal-title">View Items</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <div class="col-md-3" style="padding-left: 0px">
                                            <p>Overall area: '.$iitem['sub_overall_budget_area'].'</p>
                                        </div>
                                        <div class="col-md-9">
                                        </div>
                                      </div>
                                      <div class="modal-body multilevel-sub-item">
                                        <div class="row">
                                          <div class="col-md-12">
                                            <div class="table-responsive s_table">
                                              <table class="table estimate-sub-items-table items table-main-estimate-edit has-calculations no-mtop">
                                                <thead>
                                                  <tr>
                                                    <th width="13%" align="left">'._l('estimate_table_item_heading').'</th>
                                                    <th width="18%" align="left">'._l('estimate_table_item_description').'</th>
                                                    <th width="10%" class="qty" align="right">'._l('area').'</th>
                                                    <th width="10%" class="qty" align="right">'._l('estimate_table_quantity_heading').'</th>
                                                    <th width="16%" align="right">'._l('estimate_table_rate_heading').'</th>
                                                    <th width="16%" align="right">'._l('estimate_table_amount_heading').'</th>
                                                    <th width="17%" align="right">'._l('remarks').'</th>
                                                  </tr>
                                                </thead>
                                                <tbody>';
                                                $estimate_item_sub = 0;
                                                if (!empty($cost_planning_details['sub_multilevel_items'])) {
                                                foreach ($cost_planning_details['sub_multilevel_items'] as $sitem) {
                                                if($sitem['parent_id'] == $iitem['id']) {
                                                    $sub_amount = $sitem['sub_rate'] * $sitem['sub_qty'];
                                                    $estimate_item_sub = $estimate_item_sub + $sub_amount;
                                                   $modals_html .= '<tr>
                                                        <td>
                                                            '.get_purchase_items($sitem['item_name']).'
                                                        </td>
                                                        <td>
                                                            '.clear_textarea_breaks($sitem['sub_long_description']).'
                                                        </td>
                                                        <td align="right">
                                                            '.$sitem['sub_budget_area'].'
                                                        </td>
                                                        <td align="right">
                                                            '.number_format($sitem['sub_qty'], 2).'
                                                        </td>
                                                        <td align="right">
                                                            '.$sitem['sub_rate'].'
                                                        </td>
                                                        <td align="right">
                                                            '.number_format($sub_amount, 2).'
                                                        </td>
                                                        <td align="right">
                                                            '.clear_textarea_breaks($sitem['sub_remarks']).'
                                                        </td>
                                                    </tr>';
                                                } } }
                                                $sub_overall_budget_area = $iitem['sub_overall_budget_area'];
                                                if($sub_overall_budget_area == 0 || empty($sub_overall_budget_area)) {
                                                    $sub_overall_budget_area = 1;
                                                }
                                                $modal_total = $estimate_item_sub / $sub_overall_budget_area;
                                                $modals_html .= '</tbody>
                                              </table>
                                            </div>
                                            <div class="col-md-8 col-md-offset-4">
                                                <table class="table text-right">
                                                    <tbody>
                                                        <tr id="subtotal">
                                                            <td><span class="bold tw-text-neutral-700">'._l('estimate_subtotal').' :</span>
                                                            </td>
                                                            <td>
                                                                '.app_format_money($modal_total, $base_currency).'
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="bold tw-text-neutral-700">'._l('estimate_total').' :</span>
                                                            </td>
                                                            <td>
                                                                '.app_format_money($modal_total, $base_currency).'
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>';
                              echo $modals_html;
                            }
                        }
                    } ?>
                </div>
            <?php } ?>

                            </div>
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
</script>
<?php $this->load->view('admin/estimates/estimate_send_to_client'); ?>
