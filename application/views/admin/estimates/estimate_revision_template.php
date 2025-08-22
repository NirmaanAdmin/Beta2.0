<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .revision_bold {
        font-weight: bold;
    }
</style>
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
        <h4 class="bold"><i class="fa-solid fa-lock tw-mr-1"></i> This Revision is locked</h4>
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
	            <a href="#revision_<?php echo $unique_id; ?>_final_estimate" aria-controls="revision_<?php echo $unique_id; ?>_final_estimate" role="tab" id="tab_revision_<?php echo $unique_id; ?>_final_estimate" data-toggle="tab">
	                <?php echo _l('project_brief'); ?>
	            </a>
	        </li>
	        <li role="presentation">
	            <a href="#revision_<?php echo $unique_id; ?>_area_summary" aria-controls="revision_<?php echo $unique_id; ?>_area_summary" role="tab" id="tab_area_summary" data-toggle="tab">
	                <?php echo _l('area_summary'); ?>
	            </a>
	        </li>
	        <li role="presentation">
	            <a href="#revision_<?php echo $unique_id; ?>_area_working" aria-controls="revision_<?php echo $unique_id; ?>_area_working" role="tab" id="tab_area_working" data-toggle="tab">
	                <?php echo _l('area_working'); ?>
	            </a>
	        </li>
	        <li role="presentation">
	            <a href="#revision_<?php echo $unique_id; ?>_budget_summary" aria-controls="revision_<?php echo $unique_id; ?>_budget_summary" role="tab" id="tab_budget_summary" data-toggle="tab">
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
	                        <a href="#revision_<?php echo $unique_id; ?>_<?php echo $annexure['annexure_key']; ?>" aria-controls="revision_<?php echo $unique_id; ?>_<?php echo $annexure['annexure_key']; ?>" role="tab" id="revision_<?php echo $unique_id; ?>_tab_<?php echo $annexure['annexure_key']; ?>" data-toggle="tab">
	                            <?php echo $annexure['name']; ?>
	                        </a>
	                    </li>
	                <?php } ?>
	            </ul>
	        </li>
	        <li role="presentation">
	            <a href="#revision_<?php echo $unique_id; ?>_project_timelines" aria-controls="revision_<?php echo $unique_id; ?>_project_timelines" role="tab" id="tab_project_timelines" data-toggle="tab">
	                <?php echo _l('project_timelines'); ?>
	            </a>
	        </li>
	    </ul>
	</div>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="revision_<?php echo $unique_id; ?>_final_estimate">
            <div class="col-md-12">
                <?php echo $cost_planning_details['estimate_detail']['project_brief']; ?>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="revision_<?php echo $unique_id; ?>_project_timelines">
            <div class="col-md-12">
                <?php echo $cost_planning_details['estimate_detail']['project_timelines']; ?>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="revision_<?php echo $unique_id; ?>_budget_summary">
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
                            $total_amount = 0;
                            $total_bua = 0;
                            foreach($annexure_estimate as $ikey => $svalue) {
                            $total_amount = $total_amount + $svalue['amount'];
                            $total_bua = $total_bua + $svalue['total_bua'];
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
                            <td align="right"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-12">
                <?php echo $cost_planning_details['estimate_detail']['cost_plan_summary']; ?>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="revision_<?php echo $unique_id; ?>_area_summary">
            <?php
            $show_as_unit_name = $cost_planning_details['estimate_detail']['show_as_unit'] == 1 ? 'sqft' : 'sqm';
            ?>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <?php
                    if(!empty($cost_planning_details['area_summary_tabs'])) { 
                        foreach ($cost_planning_details['area_summary_tabs'] as $akey => $avalue) { ?>
                            <li role="presentation" class="<?php echo ($akey == 0) ? 'active' : ''; ?>">
                                <a href="#revision_<?php echo $unique_id; ?>_area_summary_<?php echo $avalue['id']; ?>" aria-controls="revision_<?php echo $unique_id; ?>_area_summary_<?php echo $avalue['id']; ?>" role="tab" id="tab_revision_<?php echo $unique_id; ?>_area_summary_<?php echo $avalue['id']; ?>" class="tab_sub_area_summary" data-toggle="tab" data-tab-id="<?php echo $avalue['id']; ?>">
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
                        <div role="tabpanel" class="tab-pane area_summary_tab <?php echo ($akey == 0) ? 'active' : ''; ?>" id="revision_<?php echo $unique_id; ?>_area_summary_<?php echo $avalue['id']; ?>" data-id="<?php echo $avalue['id']; ?>">
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

        <div role="tabpanel" class="tab-pane" id="revision_<?php echo $unique_id; ?>_area_working">
            <?php
            $show_aw_unit_name = $cost_planning_details['estimate_detail']['show_aw_unit'] == 1 ? 'sqft' : 'sqm';
            ?>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <?php
                    if(!empty($cost_planning_details['area_statement_tabs'])) { 
                        foreach ($cost_planning_details['area_statement_tabs'] as $akey => $avalue) { ?>
                            <li role="presentation" class="<?php echo ($akey == 0) ? 'active' : ''; ?>">
                                <a href="#revision_<?php echo $unique_id; ?>_area_working_<?php echo $avalue['id']; ?>" aria-controls="revision_<?php echo $unique_id; ?>_area_working_<?php echo $avalue['id']; ?>" role="tab" id="tab_revision_<?php echo $unique_id; ?>_area_working_<?php echo $avalue['id']; ?>" class="tab_sub_area_working" data-toggle="tab" data-tab-id="<?php echo $avalue['id']; ?>">
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
                        <div role="tabpanel" class="tab-pane area_working_tab <?php echo ($akey == 0) ? 'active' : ''; ?>" id="revision_<?php echo $unique_id; ?>_area_working_<?php echo $avalue['id']; ?>" data-id="<?php echo $avalue['id']; ?>">
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
            <div role="tabpanel" class="tab-pane detailed-costing-tab" id="revision_<?php echo $unique_id; ?>_<?php echo $annexure['annexure_key']; ?>" data-id="<?php echo $annexure['id']; ?>">
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
                                    <th width="16%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
                                    <th width="10%" class="qty" align="right"><?php echo _l('sub_head'); ?></th>
                                    <th width="12%" class="area" align="right"><?php echo _l('area'); ?></th>
                                    <th width="10%" class="qty" align="right"><?php echo e(_l('estimate_table_quantity_heading')); ?></th>
                                    <th width="13%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                    <th width="13%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                                    <th width="13%" align="right"><?php echo _l('remarks'); ?></th>
                                </tr>
                                <tbody>
                                    <?php
                                    $estimate_item_rate = 0;
                                    $estimate_item_amount = 0;
                                    if(!empty($cost_planning_details['estimate_items'])) {
                                        foreach ($cost_planning_details['estimate_items'] as $ankey => $item) {
                                            if($item['annexure'] == $annexure['id']) { 
                                                $amount = $item['rate'] * $item['qty'];
                                                $estimate_item_rate = $estimate_item_rate + $item['rate'];
                                                $estimate_item_amount = $estimate_item_amount + $amount;
                                                $old_item_code = isset($root_estimate_data['estimate_items'][$ankey]['item_code']) ? $root_estimate_data['estimate_items'][$ankey]['item_code'] : '';
                                                $old_long_description = isset($root_estimate_data['estimate_items'][$ankey]['long_description']) ? $root_estimate_data['estimate_items'][$ankey]['long_description'] : '';
                                                $old_sub_head = isset($root_estimate_data['estimate_items'][$ankey]['sub_head']) ? $root_estimate_data['estimate_items'][$ankey]['sub_head'] : '';
                                                $old_area = isset($root_estimate_data['estimate_items'][$ankey]['area']) ? $root_estimate_data['estimate_items'][$ankey]['area'] : '';
                                                $old_unit_id = isset($root_estimate_data['estimate_items'][$ankey]['unit_id']) ? $root_estimate_data['estimate_items'][$ankey]['unit_id'] : '';
                                                $old_qty = isset($root_estimate_data['estimate_items'][$ankey]['qty']) ? $root_estimate_data['estimate_items'][$ankey]['qty'] : '';
                                                $old_rate = isset($root_estimate_data['estimate_items'][$ankey]['rate']) ? $root_estimate_data['estimate_items'][$ankey]['rate'] : '';
                                                $old_remarks = isset($root_estimate_data['estimate_items'][$ankey]['remarks']) ? $root_estimate_data['estimate_items'][$ankey]['remarks'] : '';
                                            ?>
                                                <tr>
                                                    <td <?php echo find_estimate_revision_bold($old_item_code, $item['item_code']); ?>>
                                                        <?php echo get_purchase_items($item['item_code']); ?>
                                                    </td>
                                                    <td <?php echo find_estimate_revision_bold($old_long_description, $item['long_description']); ?>>
                                                        <?php echo clear_textarea_breaks($item['long_description']); ?>
                                                    </td>
                                                    <td align="right" <?php echo find_estimate_revision_bold($old_sub_head, $item['sub_head']); ?>>
                                                        <?php echo get_sub_head($item['sub_head']); ?>
                                                    </td>
                                                    <td align="right" <?php echo find_estimate_revision_bold($old_area, $item['area']); ?>>
                                                        <?php echo get_area_name_by_id($item['area']); ?>
                                                    </td>
                                                    <td align="right">
                                                        <?php 
                                                        $purchase_unit_name = get_purchase_unit($item['unit_id']);
                                                        $purchase_unit_name = !empty($purchase_unit_name) ? ' '.$purchase_unit_name : '';
                                                        ?>
                                                        <span <?php echo find_estimate_revision_bold(number_format((float)$old_qty, 2), number_format((float)$item['qty'], 2)); ?>>
                                                            <?php echo number_format((float)$item['qty'], 2); ?>
                                                        </span>
                                                        <span <?php echo find_estimate_revision_bold((int)$old_unit_id, (int)$item['unit_id']); ?>>
                                                            <?php echo $purchase_unit_name; ?>
                                                        </span>
                                                    </td>
                                                    <td align="right" <?php echo find_estimate_revision_bold($old_rate, $item['rate']); ?>>
                                                        <?php echo app_format_money($item['rate'], $base_currency); ?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo app_format_money($amount, $base_currency); ?>
                                                    </td>
                                                    <td align="right" <?php echo find_estimate_revision_bold($old_remarks, $item['remarks']); ?>>
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