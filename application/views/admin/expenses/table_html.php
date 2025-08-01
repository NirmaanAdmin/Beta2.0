<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$hasPermission = staff_can('edit', 'expenses') || staff_can('edit', 'expenses');
if ($withBulkActions === true && $hasPermission) { ?>
  <a href="#" data-toggle="modal" data-target="#expenses_bulk_actions" class="hide bulk-actions-btn table-btn"
    data-table=".table-expenses">
    <?php echo _l('bulk_actions'); ?>
  </a>
<?php } ?>
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

  <div class="col-md-2 form-group">
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
  <div class="row">
    <div class="col-md-1 form-group" style="margin-top: 24px;">
      <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
        <?php echo _l('reset_filter'); ?>
      </a>
    </div>
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
        <th><?php echo _l('expense_dt_table_heading_customer'); ?></th>
        <th><?php echo _l('invoice'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_reference_no'); ?></th>
        <th><?php echo _l('expense_dt_table_heading_payment_mode'); ?></th>
        <th>Vendor</th>
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
    </tfoot>
  </table>
</div>

<?php
echo $this->view('admin/expenses/_bulk_actions_modal');
?>