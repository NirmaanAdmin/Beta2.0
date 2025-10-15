<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$hasPermission = staff_can('edit', 'expenses') || staff_can('edit', 'expenses');
if ($withBulkActions === true && $hasPermission) { ?>
  <a href="#" data-toggle="modal" data-target="#expenses_bulk_actions" class="hide bulk-actions-btn table-btn"
    data-table=".table-expenses">
    <?php echo _l('bulk_actions'); ?>
  </a>
<?php } ?>
<a onclick="bulk_convert_expense_to_vbt(); return false;" data-table=".table-expenses" class="hide bulk-actions-btn table-btn">Bulk Convert</a>
<div class="row all_ot_filters">
  <hr style="margin-top: 0px !important;">
  <?php
  $module_name = 'expenses';
  $expense_category_filter = get_module_filter($module_name, 'expense_category');
  $expense_category_filter_val = !empty($expense_category_filter) ? explode(",", $expense_category_filter->filter_value) : [];
  ?>
  <div class="col-md-3 form-group">
    <label for="expense_category"><?php echo _l('expense_category'); ?></label>
    <select name="expense_category[]" id="expense_category" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
      <?php foreach ($categories as $s) { ?>
        <option value="<?php echo pur_html_entity_decode($s['id']); ?>"
          <?php if (in_array($s['id'], $expense_category_filter_val)) {
            echo 'selected';
          } ?>>
          <?php echo pur_html_entity_decode($s['name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>
  <?php
  $payment_mode_filter = get_module_filter($module_name, 'payment_mode');
  $payment_mode_filter_val = !empty($payment_mode_filter) ? explode(",", $payment_mode_filter->filter_value) : [];
  ?>

  <div class="col-md-3 form-group">
    <label for="payment_mode"><?php echo _l('payment_mode'); ?></label>
    <select name="payment_mode[]" id="payment_mode" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
      <?php foreach ($payment_modes as $mode) { ?>
        <option value="<?php echo pur_html_entity_decode($mode['id']); ?>"
          <?php if (in_array($mode['id'], $payment_mode_filter_val)) {
            echo 'selected';
          } ?>>
          <?php echo pur_html_entity_decode($mode['name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <?php
  $vendor_filter = get_module_filter($module_name, 'Vendor');
  $vendor_filter_val = !empty($vendor_filter) ? explode(",", $vendor_filter->filter_value) : [];
  ?>
  <div class="col-md-3 form-group">
    <label for="vendor"><?php echo _l('Vendor'); ?></label>
    <select name="vendor[]" id="vendor" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
      <?php foreach ($vendors as $vendor) { ?>
        <option value="<?php echo pur_html_entity_decode($vendor['userid']); ?>"
          <?php if (in_array($vendor['userid'], $vendor_filter_val)) {
            echo 'selected';
          } ?>>
          <?php echo pur_html_entity_decode($vendor['company']); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <div class="col-md-3 form-group" id="report-time">
    <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
    <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
      <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
      <option value="this_month"><?php echo _l('this_month'); ?></option>
      <option value="1"><?php echo _l('last_month'); ?></option>
      <option value="this_year"><?php echo _l('this_year'); ?></option>
      <option value="last_year"><?php echo _l('last_year'); ?></option>
      <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
      <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
      <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
      <option value="custom"><?php echo _l('period_datepicker'); ?></option>
    </select>
  </div>
  <div id="date-range" class="hide mbot15">
    <div class="col-md-2 form-group">
      <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
      <div class="input-group date">
        <input type="text" class="form-control datepicker" id="report-from" name="report-from">
        <div class="input-group-addon">
          <i class="fa fa-calendar calendar-icon"></i>
        </div>
      </div>
    </div>
    <div class="col-md-2 form-group">
      <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
      <div class="input-group date">
        <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
        <div class="input-group-addon">
          <i class="fa fa-calendar calendar-icon"></i>
        </div>
      </div>
    </div>
  </div>
  <?php $current_year = date('Y');
  $y0 = (int)$current_year;
  $y1 = (int)$current_year - 1;
  $y2 = (int)$current_year - 2;
  $y3 = (int)$current_year - 3;
  ?>
  <div class="form-group hide" id="year_requisition">
    <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
    <select name="year_requisition" id="year_requisition" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('filter_by') . ' ' . _l('year'); ?>">
      <option value="<?php echo pur_html_entity_decode($y0); ?>" <?php echo 'selected' ?>><?php echo _l('year') . ' ' . pur_html_entity_decode($y0); ?></option>
      <option value="<?php echo pur_html_entity_decode($y1); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y1); ?></option>
      <option value="<?php echo pur_html_entity_decode($y2); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y2); ?></option>
      <option value="<?php echo pur_html_entity_decode($y3); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y3); ?></option>
    </select>
  </div>
  <div class="col-md-3 form-group">
    <?php
    $order_tagged_filter = get_module_filter($module_name, 'order_tagged');
    $order_tagged_filter_val = !empty($order_tagged_filter) ? $order_tagged_filter->filter_value : '';
    $order_tagged = [
       ['id' => 1, 'name' => _l('Yes')],
       ['id' => 2, 'name' => _l('No')]
    ];
    ?>
    <select name="order_tagged" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('Tagged to Order?'); ?>" data-actions-box="true">
       <option value=""></option>
       <?php foreach ($order_tagged as $value) { ?>
          <option value="<?php echo $value['id']; ?>" <?php echo ($order_tagged_filter_val == $value['id']) ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
       <?php } ?>
    </select>
  </div>
  <div class="col-md-3 form-group">
    <?php
    $order_tagged_detail_filter = get_module_filter($module_name, 'order_tagged_detail');
    $order_tagged_detail_filter_val = !empty($order_tagged_detail_filter) ? explode(",", $order_tagged_detail_filter->filter_value) : '';
    echo render_select('order_tagged_detail[]', $order_tagged_detail, array('id', 'name'), '', $order_tagged_detail_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Order Detail'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
    ?>
  </div>
  <div class="col-md-3 form-group">
    <?php
    $converted_filter = get_module_filter($module_name, 'converted');
    $converted_filter_val = !empty($converted_filter) ? $converted_filter->filter_value : '';
    $converted = [
       ['id' => 1, 'name' => _l('Yes')],
       ['id' => 2, 'name' => _l('No')]
    ];
    ?>
    <select name="converted" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('Converted?'); ?>" data-actions-box="true">
       <option value=""></option>
       <?php foreach ($converted as $value) { ?>
          <option value="<?php echo $value['id']; ?>" <?php echo ($converted_filter_val == $value['id']) ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
       <?php } ?>
    </select>
  </div>
</div>

<div class="row">
  <div class="col-md-1 form-group">
    <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
      <?php echo _l('reset_filter'); ?>
    </a>
  </div>
</div>

<div class="">
  <table data-last-order-identifier="expenses" data-default-order="" id="expenses" class="dt-table-loading table table-expenses">
    <thead>
      <tr>
        <th class=""><span class="hide"> - </span>
          <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="expenses"><label></label></div>
        </th>
        <th><?php echo _l('the_number_sign'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_category'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_amount'); ?></th>
        <th><?php echo _l('expense_name'); ?></th>
        <th><?php echo _l('receipt'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_date'); ?></th>
        <th><?php echo _l('project'); ?></th>
        <th>Converted?</th>
        <th><?php echo _l('invoice'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_reference_no'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_payment_mode'); ?></th>
        <th>Vendor</th>
        <th>Options</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <td></td>
      <td></td>
      <td></td>
      <td class="total_expense_amount"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tfoot>
  </table>
</div>

<?php
echo $this->view('admin/expenses/_bulk_actions_modal');
?>