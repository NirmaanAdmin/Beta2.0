<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .sortable.item, .detailed_costing {
        margin-top: 35px;
        border-right: 1px solid #e2e8f0;
        border-left: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
</style>
<div class="panel_s accounting-template estimate">
    <div class="panel-body">
        <div class="row">
            <?php
                if (isset($estimate_request_id) && $estimate_request_id != '') {
                    echo form_hidden('estimate_request_id', $estimate_request_id);
                }
            ?>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <?php $budget_description = (isset($estimate) ? $estimate->budget_description : '');
                        echo render_input('budget_description', 'budget_description', $budget_description); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="f_client_id">
                            <div class="form-group select-placeholder">
                                <label for="clientid"
                                    class="control-label"><?php echo _l('estimate_select_customer'); ?></label>
                                <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if (isset($estimate) && empty($estimate->clientid)) {
                                    echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php $selected = (isset($estimate) ? $estimate->clientid : '');
                                     if ($selected == '') {
                                         $selected = (isset($customer_id) ? $customer_id: '');
                                     }
                                     if ($selected != '') {
                                         $rel_data = get_relation_data('customer', $selected);
                                         $rel_val  = get_relation_values($rel_data, 'customer');
                                         echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                     } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group select-placeholder projects-wrapper<?php if ((!isset($estimate)) || (isset($estimate) && !customer_has_projects($estimate->clientid))) {
                     echo (isset($customer_id) && (!isset($project_id) || !$project_id)) ? ' hide' : '';
                 } ?>">
                    <label for="project_id"><?php echo _l('project'); ?></label>
                    <div id="project_ajax_search_wrapper">
                        <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true"
                            data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php
                 if (!isset($project_id)) {
                     $project_id = '';
                 }
                  if (isset($estimate) && $estimate->project_id) {
                      $project_id = $estimate->project_id;
                  }
                  if ($project_id) {
                      echo '<option value="' . $project_id . '" selected>' . e(get_project_name_by_id($project_id)) . '</option>';
                  }
                ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="edit_shipping_billing_info" data-toggle="modal"
                            data-target="#billing_and_shipping_details"><i class="fa-regular fa-pen-to-square"></i></a>
                        <?php include_once(APPPATH . 'views/admin/estimates/billing_and_shipping_template.php'); ?>
                    </div>
                    <div class="col-md-6">
                        <p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
                        <address>
                            <span class="billing_street">
                                <?php $billing_street = (isset($estimate) ? $estimate->billing_street : '--'); ?>
                                <?php $billing_street = ($billing_street == '' ? '--' :$billing_street); ?>
                                <?php echo process_text_content_for_display($billing_street); ?></span><br>
                            <span class="billing_city">
                                <?php $billing_city = (isset($estimate) ? $estimate->billing_city : '--'); ?>
                                <?php $billing_city = ($billing_city == '' ? '--' :$billing_city); ?>
                                <?php echo e($billing_city); ?></span>,
                            <span class="billing_state">
                                <?php $billing_state = (isset($estimate) ? $estimate->billing_state : '--'); ?>
                                <?php $billing_state = ($billing_state == '' ? '--' :$billing_state); ?>
                                <?php echo e($billing_state); ?></span>
                            <br />
                            <span class="billing_country">
                                <?php $billing_country = (isset($estimate) ? get_country_short_name($estimate->billing_country) : '--'); ?>
                                <?php $billing_country = ($billing_country == '' ? '--' :$billing_country); ?>
                                <?php echo e($billing_country); ?></span>,
                            <span class="billing_zip">
                                <?php $billing_zip = (isset($estimate) ? $estimate->billing_zip : '--'); ?>
                                <?php $billing_zip = ($billing_zip == '' ? '--' :$billing_zip); ?>
                                <?php echo e($billing_zip); ?></span>
                        </address>
                    </div>
                    <div class="col-md-6">
                        <p class="bold"><?php echo _l('ship_to'); ?></p>
                        <address>
                            <span class="shipping_street">
                                <?php $shipping_street = (isset($estimate) ? $estimate->shipping_street : '--'); ?>
                                <?php $shipping_street = ($shipping_street == '' ? '--' :$shipping_street); ?>
                                <?php echo process_text_content_for_display($shipping_street); ?></span><br>
                            <span class="shipping_city">
                                <?php $shipping_city = (isset($estimate) ? $estimate->shipping_city : '--'); ?>
                                <?php $shipping_city = ($shipping_city == '' ? '--' :$shipping_city); ?>
                                <?php echo e($shipping_city); ?></span>,
                            <span class="shipping_state">
                                <?php $shipping_state = (isset($estimate) ? $estimate->shipping_state : '--'); ?>
                                <?php $shipping_state = ($shipping_state == '' ? '--' :$shipping_state); ?>
                                <?php echo e($shipping_state); ?></span>
                            <br />
                            <span class="shipping_country">
                                <?php $shipping_country = (isset($estimate) ? get_country_short_name($estimate->shipping_country) : '--'); ?>
                                <?php $shipping_country = ($shipping_country == '' ? '--' :$shipping_country); ?>
                                <?php echo e($shipping_country); ?></span>,
                            <span class="shipping_zip">
                                <?php $shipping_zip = (isset($estimate) ? $estimate->shipping_zip : '--'); ?>
                                <?php $shipping_zip = ($shipping_zip == '' ? '--' :$shipping_zip); ?>
                                <?php echo e($shipping_zip); ?></span>
                        </address>
                    </div>
                </div>
                <?php
               $next_estimate_number = get_option('next_estimate_number');
               $format               = get_option('estimate_number_format');

                if (isset($estimate)) {
                    $format = $estimate->number_format;
                }

               $prefix = get_option('estimate_prefix');

               if ($format == 1) {
                   $__number = $next_estimate_number;
                   if (isset($estimate)) {
                       $__number = $estimate->number;
                       $prefix   = '<span id="prefix">' . $estimate->prefix . '</span>';
                   }
               } elseif ($format == 2) {
                   if (isset($estimate)) {
                       $__number = $estimate->number;
                       $prefix   = $estimate->prefix;
                       $prefix   = '<span id="prefix">' . $prefix . '</span><span id="prefix_year">' . date('Y', strtotime($estimate->date)) . '</span>/';
                   } else {
                       $__number = $next_estimate_number;
                       $prefix   = $prefix . '<span id="prefix_year">' . date('Y') . '</span>/';
                   }
               } elseif ($format == 3) {
                   if (isset($estimate)) {
                       $yy       = date('y', strtotime($estimate->date));
                       $__number = $estimate->number;
                       $prefix   = '<span id="prefix">' . $estimate->prefix . '</span>';
                   } else {
                       $yy       = date('y');
                       $__number = $next_estimate_number;
                   }
               } elseif ($format == 4) {
                   if (isset($estimate)) {
                       $yyyy     = date('Y', strtotime($estimate->date));
                       $mm       = date('m', strtotime($estimate->date));
                       $__number = $estimate->number;
                       $prefix   = '<span id="prefix">' . $estimate->prefix . '</span>';
                   } else {
                       $yyyy     = date('Y');
                       $mm       = date('m');
                       $__number = $next_estimate_number;
                   }
               }

               $_estimate_number     = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               $isedit               = isset($estimate) ? 'true' : 'false';
               $data_original_number = isset($estimate) ? $estimate->number : 'false';
               ?>
                <div class="form-group">
                    <label for="number"><?php echo 'Budget Number'; ?></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <?php if (isset($estimate)) { ?>
                            <a href="#" onclick="return false;" data-toggle="popover"
                                data-container='._transaction_form' data-html="true"
                                data-content="<label class='control-label'><?php echo _l('settings_sales_estimate_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo e($estimate->prefix); ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('estimates/update_number_settings/' . $estimate->id); ?>' class='btn btn-primary btn-block mtop15'><?php echo _l('submit'); ?></button>"><i
                                    class="fa fa-cog"></i></a>
                            <?php }
                    echo $prefix;
                  ?>
                        </span>
                        <input type="text" name="number" class="form-control" value="<?php echo e($_estimate_number); ?>"
                            data-isedit="<?php echo e($isedit); ?>"
                            data-original-number="<?php echo e($data_original_number); ?>">
                        <?php if ($format == 3) { ?>
                        <span class="input-group-addon">
                            <span id="prefix_year" class="format-n-yy"><?php echo e($yy); ?></span>
                        </span>
                        <?php } elseif ($format == 4) { ?>
                        <span class="input-group-addon">
                            <span id="prefix_month" class="format-mm-yyyy"><?php echo e($mm); ?></span>
                            /
                            <span id="prefix_year" class="format-mm-yyyy"><?php echo e($yyyy); ?></span>
                        </span>
                        <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $value = (isset($estimate) ? _d($estimate->date) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('date', 'Budget Date', $value); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                  $value = '';
                  if (isset($estimate)) {
                      $value = _d($estimate->expirydate);
                  } else {
                      if (get_option('estimate_due_after') != 0) {
                          $value = _d(date('Y-m-d', strtotime('+' . get_option('estimate_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                      }
                  }
                  echo render_date_input('expirydate', 'estimate_add_edit_expirydate', $value); ?>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>
                <?php $rel_id = (isset($estimate) ? $estimate->id : false); ?>
                <?php
                  if (isset($custom_fields_rel_transfer)) {
                      $rel_id = $custom_fields_rel_transfer;
                  }
             ?>
                <?php echo render_custom_fields('estimate', $rel_id); ?>
            </div>
            <div class="col-md-6">
                <div class="tw-ml-3">
                    <div class="form-group">
                        <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                            <?php echo _l('tags'); ?></label>
                        <input type="text" class="tagsinput" id="tags" name="tags"
                            value="<?php echo(isset($estimate) ? prep_tags_input(get_tags_in($estimate->id, 'estimate')) : ''); ?>"
                            data-role="tagsinput">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php

                        $currency_attr = ['disabled' => true, 'data-show-subtext' => true];
                        $currency_attr = apply_filters_deprecated('estimate_currency_disabled', [$currency_attr], '2.3.0', 'estimate_currency_attributes');
                        foreach ($currencies as $currency) {
                            if ($currency['isdefault'] == 1) {
                                $currency_attr['data-base'] = $currency['id'];
                            }
                            if (isset($estimate)) {
                                if ($currency['id'] == $estimate->currency) {
                                    $selected = $currency['id'];
                                }
                            } else {
                                if ($currency['isdefault'] == 1) {
                                    $selected = $currency['id'];
                                }
                            }
                        }
                        $currency_attr = hooks()->apply_filters('estimate_currency_attributes', $currency_attr);
                        ?>
                            <?php echo render_select('currency', $currencies, ['id', 'name', 'symbol'], 'estimate_add_edit_currency', $selected, $currency_attr); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label class="control-label"><?php echo _l('estimate_status'); ?></label>
                                <select class="selectpicker display-block mbot15" name="status" data-width="100%"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach ($estimate_statuses as $status) { ?>
                                    <option value="<?php echo e($status); ?>" <?php if (isset($estimate) && $estimate->status == $status) {
                            echo 'selected';
                        } ?>><?php echo format_estimate_status($status, '', false); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php $value = (isset($estimate) ? $estimate->reference_no : ''); ?>
                            <?php echo render_input('reference_no', 'reference_no', $value); ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                                $selected = !isset($estimate) && get_option('automatically_set_logged_in_staff_sales_agent') == '1' ? get_staff_user_id() : '';
                                foreach ($staff as $member) {
                                    if (isset($estimate)) {
                                        if ($estimate->sale_agent == $member['staffid']) {
                                            $selected = $member['staffid'];
                                        }
                                    }
                                }
                                echo render_select('sale_agent', $staff, ['staffid', ['firstname', 'lastname']], 'sale_agent_string', $selected);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="discount_type"
                                    class="control-label"><?php echo _l('discount_type'); ?></label>
                                <select name="discount_type" class="selectpicker" data-width="100%"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="" selected><?php echo _l('no_discount'); ?></option>
                                    <option value="before_tax" <?php
                              if (isset($estimate)) {
                                  if ($estimate->discount_type == 'before_tax') {
                                      echo 'selected';
                                  }
                              }?>><?php echo _l('discount_type_before_tax'); ?></option>
                                    <option value="after_tax" <?php if (isset($estimate)) {
                                  if ($estimate->discount_type == 'after_tax') {
                                      echo 'selected';
                                  }
                              } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php $value = (isset($estimate) ? $estimate->adminnote : ''); ?>
                    <?php echo render_textarea('adminnote', 'estimate_add_edit_admin_note', $value, array('rows' => 2)); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hsn_sac" class="control-label"><?php echo _l('hsn_sac') ?></label>
                                <select name="hsn_sac" id="hsn_sac" class="selectpicker" data-live-search="true" data-width="100%">
                                    <option value=""></option>
                                    <?php foreach ($get_hsn_sac_code as $item): ?>
                                        <?php
                                        $selected = '';
                                        if (isset($estimate)) {
                                            if ($estimate->hsn_sac == $item['id']) {
                                                $selected = 'selected';
                                            }
                                        }

                                        $words = explode(' ', $item['name']);
                                        $shortName = implode(' ', array_slice($words, 0, 7));
                                        ?>
                                        <option value="<?= $item['id'] ?>" <?= $selected  ?>>
                                            <?= htmlspecialchars($shortName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr class="hr-panel-separator" />

    <div class="panel-body" style="padding-bottom: 0px !important;">
        <div class="row">
            <div class="col-md-4">
                <?php $this->load->view('admin/invoice_items/item_select'); ?>
            </div>
            <div class="col-md-8 text-right show_quantity_as_wrapper">
            </div>
        </div>
    </div>

    <div class="panel-body">
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
                        <?php echo _l('area_statement'); ?>
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
                <?php /*
                <div class="table-responsive s_table">
                    <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1"
                                aria-hidden="true" data-toggle="tooltip"
                                data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i>
                                <?php echo _l('estimate_table_item_heading'); ?></th>
                                <th width="25%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
                                <th width="10%" align="right" class="qty"><?php echo e(_l('estimate_table_quantity_heading')); ?></th>
                                <th width="15%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                <th width="20%" align="right"><?php echo _l('estimate_table_tax_heading'); ?></th>
                                <th width="10%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="main">
                                <td></td>
                                <td align="left">
                                    <?php echo $annexure_estimate['final_estimate']['name']; ?>
                                </td>
                                <td align="left">
                                    <?php echo $annexure_estimate['final_estimate']['description']; ?>
                                </td>
                                <td align="right">
                                    <?php echo $annexure_estimate['final_estimate']['qty']; ?>
                                </td>
                                <td align="right">
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['subtotal'], $base_currency); ?>
                                </td>
                                <td align="right">
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['tax'], $base_currency); ?>
                                </td>
                                <td align="right">
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['amount'], $base_currency); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div> */ ?>
                <?php
                $project_brief = (isset($estimate) ? $estimate->project_brief : '');
                echo render_textarea('project_brief', '', $project_brief, [], [], '', 'tinymce'); 
                ?>

                <div class="col-md-8 col-md-offset-4">
                    <table class="table text-right">
                        <tbody>
                            <tr id="subtotal">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['subtotal'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr id="total_tax">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['tax'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['amount'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php hooks()->do_action('after_admin_estimate_form_total_field', $estimate ?? null); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="budget_summary">
                <div class="table-responsive s_table">
                    <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="25%" align="left"><?php echo _l('group_pur'); ?></th>
                                <th width="25%" align="right">Cost (INR)</th>
                                <th width="25%" align="right">Cost/BUA</th>
                                <th width="25%" align="right"><?php echo _l('remarks'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!empty($annexure_estimate['summary'])) {
                                $summary = $annexure_estimate['summary'];
                                foreach($summary as $ikey => $svalue) { ?>
                                    <tr class="main">
                                        <td></td>
                                        <td align="left">
                                            <?php echo $svalue['name']; ?>
                                        </td>
                                        <td align="right">
                                            <?php echo app_format_money($svalue['amount'], $base_currency); ?>
                                        </td>
                                        <td align="right">
                                            <?php echo app_format_money($svalue['rate'], $base_currency); ?>
                                        </td>
                                        <td align="right">
                                            
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
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['subtotal'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr id="total_tax">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['tax'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_estimate['final_estimate']['amount'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php hooks()->do_action('after_admin_estimate_form_total_field', $estimate ?? null); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="area_summary">
            </div>

            <div role="tabpanel" class="tab-pane" id="project_timelines">
            </div>

            <?php
            $annexures = get_all_annexures(); 
            $i = 1;
            foreach ($annexures as $key => $annexure) { ?>
                <div role="tabpanel" class="tab-pane" id="<?php echo $annexure['annexure_key']; ?>" data-id="<?php echo $annexure['id']; ?>">
                    <div class="col-md-4">
                        <p><?php echo _l('budget_head').': '.$annexure['name']; ?></p>
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>
                        <div class="form-group">
                          <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploaddetailedcostingcsv(this);"><?php echo _l('import'); ?></button>
                          <a href="<?php echo site_url('uploads/estimates/file_sample/Sample_detailed_costing_technical_assumptions_en.xlsx') ?>" class="btn btn-primary">Template</a>
                        </div>
                    </div>
                    <div class="table-responsive s_table">
                        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                            <thead>
                                <tr>
                                    <th width="1%"></th>
                                    <th width="15%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i><?php echo _l('estimate_table_item_heading'); ?></th>
                                    <th width="23%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
                                    <th width="10%" class="qty" align="right"><?php echo e(_l('estimate_table_quantity_heading')); ?></th>
                                    <th width="17%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                    <th width="17%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                                    <th width="17%" align="right"><?php echo _l('remarks'); ?></th>
                                    <th align="center"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="main">
                                    <td></td>
                                    <td>
                                        <textarea name="description" rows="4" class="form-control" placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
                                    </td>
                                    <td>
                                        <textarea name="long_description" rows="4" class="form-control" placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
                                        <?php
                                        $select = '';
                                        $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="unit_id" data-none-selected-text="' . _l('unit') . '">';
                                        $select .= '<option value=""></option>';
                                        foreach ($units as $unit) {
                                            $select .= '<option value="'.$unit['unit_type_id'].'">'.$unit['unit_name'].'</option>';
                                        }
                                        $select .= '</select>';
                                        echo $select;
                                        ?>
                                    </td>
                                    <td>
                                        <input type="number" name="rate" class="form-control" placeholder="<?php echo _l('item_rate_placeholder'); ?>">
                                    </td>
                                    <td class="hide">
                                        <?php
                                        $default_tax = unserialize(get_option('default_tax'));
                                        $select      = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="' . _l('no_tax') . '">';
                                        foreach ($taxes as $tax) {
                                             $selected = '';
                                             if (is_array($default_tax)) {
                                                if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                                    $selected = ' selected ';
                                                }
                                             }
                                             $select .= '<option value="' . $tax['name'] . '|' . $tax['taxrate'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                                        }
                                        $select .= '</select>';
                                        echo $select;
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>
                                        <textarea name="remarks" rows="4" class="form-control" placeholder="<?php echo _l('remarks'); ?>"></textarea>
                                    </td>
                                    <td>
                                        <?php
                                        $new_item = 'undefined';
                                        if (isset($estimate)) {
                                            $new_item = true;
                                        } ?>
                                        <button type="button" onclick="add_estimate_item_to_table('undefined','undefined',<?php echo e($new_item); ?>); return false;"
                                            class="btn pull-right btn-primary"><i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                </tr>

                                <?php if (isset($estimate) || isset($add_items)) {
                                    $items_indicator = 'newitems';
                                    if (isset($estimate)) {
                                        $add_items = $estimate->items;
                                        $items_indicator = 'items';
                                    }

                                    foreach ($add_items as $item) {
                                        if($item['annexure'] == $annexure['id']) {
                                            $manual = false;
                                            $table_row = '<tr class="sortable item">';
                                            $table_row .= '<td class="">';
                                            if ($item['qty'] == '' || $item['qty'] == 0) {
                                                $item['qty'] = 1;
                                            }
                                            if (!isset($is_proposal)) {
                                                $estimate_item_taxes = get_estimate_item_taxes($item['id']);
                                            } else {
                                                $estimate_item_taxes = get_proposal_item_taxes($item['id']);
                                            }
                                            if ($item['id'] == 0) {
                                                $estimate_item_taxes = $item['taxname'];
                                                $manual = true;
                                            }
                                            $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                                            $amount = $item['rate'] * $item['qty'];
                                            $amount = app_format_number($amount);
                                             // order input
                                            $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                                            $table_row .= '<input type="hidden" class="annexure" name="' . $items_indicator . '[' . $i . '][annexure]" value="'.$item['annexure'].'">';
                                            $table_row .= '</td>';
                                            $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
                                            $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
                                            $table_row .= '<td><input type="number" min="0" onblur="calculate_estimate_total();" onchange="calculate_estimate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
                                            
                                            $select = '';
                                            $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="' . $items_indicator . '[' . $i . '][unit_id]" data-none-selected-text="' . _l('unit') . '">';
                                            $select .= '<option value=""></option>';
                                            foreach ($units as $unit) {
                                                $selected = ($unit['unit_type_id'] == $item['unit_id']) ? ' selected' : '';
                                                $select .= '<option value="' . $unit['unit_type_id'] . '"' . $selected . '>' . $unit['unit_name'] . '</option>';

                                            }
                                            $select .= '</select>';
                                            $table_row .= $select;
                                            $table_row .= '</td>';

                                            $table_row .= '</td>';
                                            $table_row .= '<td class="rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_estimate_total();" onchange="calculate_estimate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
                                            $table_row .= '<td class="taxrate hide">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $estimate_item_taxes, (isset($is_proposal) ? 'proposal' : 'estimate'), $item['id'], true, $manual) . '</td>';
                                            $table_row .= '<td class="amount" align="right">' . $amount . '</td>';
                                            $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][remarks]" class="form-control" rows="5">' . clear_textarea_breaks($item['remarks']) . '</textarea></td>';
                                            $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_estimate_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                                            $table_row .= '</tr>';
                                            echo $table_row;
                                            $i++;
                                        }
                                    }
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr class="detailed_costing">
                                    <td colspan="15">
                                        <?php
                                        $detailed_costing_name = 'detailed_costing['.$annexure['id'].']';
                                        $detailed_costing_value = '';
                                        if(isset($estimate_detailed_costing)) {
                                            if(!empty($estimate_detailed_costing)) {
                                                foreach ($estimate_detailed_costing as $ekey => $evalue) {
                                                    if($evalue['budget_id'] == $annexure['id']) {
                                                        $detailed_costing_value = $evalue['detailed_costing'];
                                                    }
                                                }
                                            }
                                        }
                                        echo render_textarea($detailed_costing_name, '', $detailed_costing_value, [], [], '', 'tinymce'); 
                                        ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-8 col-md-offset-4">
                        <table class="table text-right">
                            <tbody>
                                <tr id="subtotal">
                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                                    </td>
                                    <td class="annexure_subtotal">
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                                    </td>
                                    <td class="annexure_total">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="removed-items"></div>
                </div>
            <?php } ?>

            <div role="tabpanel" class="tab-pane" id="area_working">
                <div class="table-responsive s_table">
                    <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th width="18%" align="left"><?php echo _l('master_area'); ?></th>
                                <th width="18%" align="left"><?php echo _l('functionality_area'); ?></th>
                                <th width="23%" align="left"><?php echo _l('area_description'); ?></th>
                                <th width="18%" align="left"><?php echo _l('carpet_area'); ?></th>
                                <th width="18%" align="left"><?php echo _l('surface_area'); ?></th>
                                <th width="5%" align="center"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody class="area_working">
                            <tr class="main">
                                <td>
                                    <?php
                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="master_area" data-none-selected-text="' . _l('master_area') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($master_area as $area) {
                                        $select .= '<option value="'.$area['id'].'">'.$area['category_name'].'</option>';
                                    }
                                    $select .= '</select>';
                                    echo $select;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="functionality_area" data-none-selected-text="' . _l('functionality_area') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($functionality_area as $area) {
                                        $select .= '<option value="'.$area['id'].'">'.$area['category_name'].'</option>';
                                    }
                                    $select .= '</select>';
                                    echo $select;
                                    ?>
                                </td>
                                <td>
                                    <textarea name="area_description" rows="4" class="form-control" placeholder="<?php echo _l('area_description'); ?>"></textarea>
                                </td>
                                <td>
                                    <input type="number" name="carpet_area" class="form-control" placeholder="<?php echo _l('carpet_area'); ?>">
                                    <?php
                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="carpet_area_unit" data-none-selected-text="' . _l('unit') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($units as $unit) {
                                        $select .= '<option value="'.$unit['unit_type_id'].'">'.$unit['unit_name'].'</option>';
                                    }
                                    $select .= '</select>';
                                    echo $select;
                                    ?>
                                </td>
                                <td>
                                    <input type="number" name="surface_area" class="form-control" placeholder="<?php echo _l('surface_area'); ?>">
                                    <?php
                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="surface_area_unit" data-none-selected-text="' . _l('unit') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($units as $unit) {
                                        $select .= '<option value="'.$unit['unit_type_id'].'">'.$unit['unit_name'].'</option>';
                                    }
                                    $select .= '</select>';
                                    echo $select;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $new_area_working = 'undefined';
                                    if (isset($estimate)) {
                                        $new_area_working = true;
                                    } ?>
                                    <button type="button" onclick="add_area_working_item_to_table('undefined','undefined',<?php echo e($new_area_working); ?>); return false;"
                                        class="btn pull-right btn-primary"><i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>

                            <?php if (isset($estimate) && isset($estimate_master_area)) {
                                $items_indicator = 'areaworkingitems';

                                foreach ($estimate_master_area as $item) {
                                    $table_row = '<tr class="item">';
                                    $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);

                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="' . $items_indicator . '[' . $i . '][master_area]" data-none-selected-text="' . _l('master_area') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($master_area as $area) {
                                        $selected = ($area['id'] == $item['master_area']) ? ' selected' : '';
                                        $select .= '<option value="' . $area['id'] . '"' . $selected . '>' . $area['category_name'] . '</option>';
                                    }
                                    $select .= '</select>';
                                    $table_row .= '<td>'.$select.'</td>';

                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="' . $items_indicator . '[' . $i . '][functionality_area]" data-none-selected-text="' . _l('functionality_area') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($functionality_area as $area) {
                                        $selected = ($area['id'] == $item['functionality_area']) ? ' selected' : '';
                                        $select .= '<option value="' . $area['id'] . '"' . $selected . '>' . $area['category_name'] . '</option>';
                                    }
                                    $select .= '</select>';
                                    $table_row .= '<td>'.$select.'</td>';

                                    $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][area_description]" class="form-control" rows="4">' . clear_textarea_breaks($item['area_description']) . '</textarea></td>';

                                    $table_row .= '<td><input type="number" onblur="calculate_area_working_total();" onchange="calculate_area_working_total();" name="' . $items_indicator . '[' . $i . '][carpet_area]" value="' . $item['carpet_area'] . '" class="form-control" id="carpet_area">';

                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="' . $items_indicator . '[' . $i . '][carpet_area_unit]" data-none-selected-text="' . _l('unit') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($units as $unit) {
                                        $selected = ($unit['unit_type_id'] == $item['carpet_area_unit']) ? ' selected' : '';
                                        $select .= '<option value="' . $unit['unit_type_id'] . '"' . $selected . '>' . $unit['unit_name'] . '</option>';

                                    }
                                    $select .= '</select>';
                                    $table_row .= $select;
                                    $table_row .= '</td>';

                                    $table_row .= '<td><input type="number" onblur="calculate_area_working_total();" onchange="calculate_area_working_total();" name="' . $items_indicator . '[' . $i . '][surface_area]" value="' . $item['surface_area'] . '" class="form-control" id="surface_area">';

                                    $select = '';
                                    $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="' . $items_indicator . '[' . $i . '][surface_area_unit]" data-none-selected-text="' . _l('unit') . '">';
                                    $select .= '<option value=""></option>';
                                    foreach ($units as $unit) {
                                        $selected = ($unit['unit_type_id'] == $item['surface_area_unit']) ? ' selected' : '';
                                        $select .= '<option value="' . $unit['unit_type_id'] . '"' . $selected . '>' . $unit['unit_name'] . '</option>';

                                    }
                                    $select .= '</select>';
                                    $table_row .= $select;
                                    $table_row .= '</td>';
                        
                                    $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_area_working_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                                    $table_row .= '</tr>';
                                    echo $table_row;
                                    $i++;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-8 col-md-offset-4">
                    <table class="table text-right">
                        <tbody>
                            <tr>
                                <td><span class="bold tw-text-neutral-700"><?php echo _l('total_carpet_area'); ?> :</span>
                                </td>
                                <td class="total_carpet_area">
                                </td>
                            </tr>
                            <tr>
                                <td><span class="bold tw-text-neutral-700"><?php echo _l('total_surface_area'); ?> :</span>
                                </td>
                                <td class="total_surface_area">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="removed-area-working-items"></div>
            </div>
        </div>

    </div>

    <hr class="hr-panel-separator" />

    <div class="panel-body">
        <?php
            $value = (isset($estimate) ? $estimate->clientnote : get_option('predefined_clientnote_estimate'));
            echo render_textarea('clientnote', 'estimate_add_edit_client_note', $value);
            $value = (isset($estimate) ? $estimate->terms : get_option('predefined_terms_estimate'));
            echo render_textarea('terms', 'terms_and_conditions', $value, [], [], 'mtop15');
        ?>
    </div>
</div>

<div class="btn-bottom-pusher"></div>
<div class="btn-bottom-toolbar text-right">
    <div class="btn-group dropup">
        <button type="button" class="btn-tr btn btn-primary estimate-form-submit transaction-submit">
            <?php echo _l('submit'); ?>
        </button>
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right width200">
            <li>
                <a href="#" class="estimate-form-submit save-and-send transaction-submit">
                    <?php echo _l('save_and_send'); ?>
                </a>
            </li>
            <?php if (!isset($estimate)) { ?>
            <li>
                <a href="#" class="estimate-form-submit save-and-send-later transaction-submit">
                    <?php echo _l('save_and_send_later'); ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

