<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'warehouse_stock_reconciliation'; ?>
<style>
  .onoffswitch-label:before {

    height: 20px !important;
  }

  .show_hide_columns {
    position: absolute;
    z-index: 99999;
    left: 190px
  }

  .n_width {
    width: 20% !important;
  }

  .dashboard_stat_title {
    font-size: 19px;
    font-weight: bold;
  }

  .dashboard_stat_value {
    font-size: 19px;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12" id="small-table">
        <div class="panel_s">
          <div class="panel-body">
            <?php echo form_hidden('delivery_id', $delivery_id); ?>
            <div class="row">
              <div class="col-md-12 ">
                <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                <hr />
              </div>
            </div>
            <div class="row">
              <div class="_buttons col-md-3">
                <!-- <?php if (!isset($invoice_id)) { ?>
                  <?php if (has_permission('warehouse', '', 'create') || is_admin()) { ?>
                    <a href="<?php echo admin_url('warehouse/add_stock_reconciliation'); ?>" class="btn btn-info pull-left mright10 display-block">
                      Add New
                    </a>
                  <?php } ?>
                <?php } ?> -->

              </div>
              <div class="col-md-1 pull-right">
                <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.delivery_sm','#delivery_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
              </div>
            </div>
            <br />
            <div class="row all_ot_filters">
              <div class="col-md-2">
                <?php
                $input_attr_e = [];
                $input_attr_e['placeholder'] = _l('day_vouchers');
                $day_vouchers_filter = get_module_filter($module_name, 'day_vouchers');
                $day_vouchers_filter_val = !empty($day_vouchers_filter) ?  $day_vouchers_filter->filter_value : '';

                echo render_date_input('date_add', '', $day_vouchers_filter_val, $input_attr_e); ?>
              </div>
              <!-- <div class="col-md-3">
                <?php
                $approval_filter = get_module_filter($module_name, 'approval');
                $approval_filter_val = !empty($approval_filter) ? $approval_filter->filter_value : '';
                ?>
                <select name="approval" id="approval" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('status_label'); ?>">
                  <option value="" <?php echo ($approval_filter_val === '') ? 'selected' : ''; ?>></option>
                  <option value="0" <?php echo ($approval_filter_val === '0') ? 'selected' : ''; ?>><?php echo _l('not_yet_approve'); ?></option>
                  <option value="1" <?php echo ($approval_filter_val === '1') ? 'selected' : ''; ?>><?php echo _l('approved'); ?></option>
                  <option value="-1" <?php echo ($approval_filter_val === '-1') ? 'selected' : ''; ?>><?php echo _l('reject'); ?></option>
                </select>
              </div> -->
              <div class="col-md-2">
                <?php
                $delivery_status_filter = get_module_filter($module_name, 'delivery_status');
                $delivery_status_filter_val = !empty($delivery_status_filter) ? $delivery_status_filter->filter_value : '';
                ?>
                <select name="delivery_status" id="delivery_status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('reconciliation_status_new'); ?>">
                  <option value="" <?php echo ($delivery_status_filter_val === '') ? 'selected' : ''; ?>></option>
                  <option value="ready_to_deliver" <?php echo ($delivery_status_filter_val === 'ready_to_deliver') ? 'selected' : ''; ?>><?php echo _l('wh_ready_to_reconcile_new'); ?></option>
                  <option value="delivery_in_progress" <?php echo ($delivery_status_filter_val === 'delivery_in_progress') ? 'selected' : ''; ?>><?php echo _l('wh_reconciliation_in_progress_new'); ?></option>
                  <option value="delivered" <?php echo ($delivery_status_filter_val === 'delivered') ? 'selected' : ''; ?>><?php echo _l('wh_reconciled_new'); ?></option>
                  <option value="received" <?php echo ($delivery_status_filter_val === 'received') ? 'selected' : ''; ?>><?php echo _l('wh_received'); ?></option>
                  <option value="returned" <?php echo ($delivery_status_filter_val === 'returned') ? 'selected' : ''; ?>><?php echo _l('wh_returned'); ?></option>
                  <option value="not_delivered" <?php echo ($delivery_status_filter_val === 'not_delivered') ? 'selected' : ''; ?>><?php echo _l('wh_not_delivered_new'); ?></option>
                </select>
              </div>
              <div class="col-md-3 form-group">
                <?php
                $vendor_type_filter = get_module_filter($module_name, 'vendor');
                $vendor_type_filter_val = !empty($vendor_type_filter) ? explode(",", $vendor_type_filter->filter_value) : [];
                ?>
                <select name="vendor[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
                  <?php
                  $vendor = get_pur_vendor_list();
                  foreach ($vendor as $vendors) {
                    $selected = in_array($vendors['userid'], $vendor_type_filter_val) ? 'selected' : '';
                  ?>
                    <option value="<?php echo $vendors['userid']; ?>" <?php echo $selected; ?>>
                      <?php echo $vendors['company']; ?>
                    </option>
                  <?php  } ?>
                </select>
              </div>
              <div class="col-md-3 form-group">
                <?php
                $wo_po_orders_filter = get_module_filter($module_name, 'wo_po_order');
                $wo_po_orders_filter_val = !empty($wo_po_orders_filter) ? explode(",", $wo_po_orders_filter->filter_value) : [];
                ?>
                <select name="wo_po_order[]" id="wo_po_order" multiple class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('Choose From Order'); ?>">
                  <?php
                  $po_wo_orders = get_purchase_work_order();
                  foreach ($po_wo_orders as $key => $value) {
                    $option_value = $value['id'] . '-' . $value['type'] . '-' . $value['goods_id'];
                    $selected = in_array($option_value, $wo_po_orders_filter_val) ? 'selected' : '';
                    echo '<option value="' . $option_value . '" ' . $selected . '>' . $value['name'] . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="row">
                <div class="col-md-1 form-group">
                  <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                    <?php echo _l('reset_filter'); ?>
                  </a>
                </div>
              </div>
            </div>

            <br />



            <div class="row">
              <div class="col-md-12">
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation">
                      <a href="#tracker_1" aria-controls="tracker_1" role="tab" id="tab_tracker_1" data-toggle="tab">
                        Listing
                      </a>
                    </li>
                    <li role="presentation" class="active">
                      <a href="#tracker_2" aria-controls="tracker_2" role="tab" id="tab_tracker_2" data-toggle="tab">
                        General Information
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="tab-content">
                <div role="tabpanel" class="col-md-12 tab-pane tracker-pane " id="tracker_1">
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
                        _l('Voucher Code'),
                        _l('Choose From Order'),
                        _l('Reconciliation Date'),
                        _l('Reconciliation Status'),
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
                  <?php render_datatable(array(
                    _l('id'),
                    _l('Reconciliation Voucher Code'),
                    _l('Choose From Order'),
                    _l('Reconciliation Date'),
                    // _l('invoices'),
                    // _l('staff_id'),
                    // _l('status_label'),
                    _l('Reconciliation Status'),
                    _l('options'),
                  ), 'table_manage_delivery', ['delivery_sm' => 'delivery_sm']); ?>
                </div>
                <div role="tabpanel" class="col-md-12 tab-pane tracker-pane active" id="tracker_2">
                  <div class="btn-group show_hide_columns" id="show_hide_columns">
                    <!-- Settings Icon -->
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                      <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                    </button>
                    <!-- Dropdown Menu with Checkboxes -->
                    <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                      <!-- Select All / Deselect All -->
                      <div>
                        <input type="checkbox" id="select-all-goods-receipt-columns"> <strong><?php echo _l('select_all'); ?></strong>
                      </div>
                      <hr>
                      <!-- Column Checkboxes -->
                      <?php
                      $columns = [
                        _l('Voucher Code'),
                        _l('commodity_code'),
                        _l('description'),
                        _l('area'),
                        _l('Stock At Store'),
                        _l('Ordered quantity'),
                        _l('Received quantity'),
                        _l('Issued Quantity'),
                        _l('Expected Return Date'),
                        _l('Return Date'),
                        _l('Reconciliation Date'),
                        _l('Return Quantity'),
                        _l('Used Quantity'),
                        _l('Remarks'),
                      ];
                      ?>
                      <div>
                        <?php foreach ($columns as $key => $label): ?>
                          <input type="checkbox" class="toggle-goods-receipt-column" value="<?php echo $key; ?>" checked>
                          <?php echo $label; ?><br>
                        <?php endforeach; ?>
                      </div>

                    </div>
                  </div>
                  <?php render_datatable(array(
                    _l('Voucher Code'),
                    _l('Choose From Order'),
                    _l('commodity_code'),
                    _l('description'),
                    _l('area'),
                    _l('Stock At Store'),
                    _l('Ordered quantity'),
                    _l('Received quantity'),
                    _l('Issued Quantity'),
                    _l('Expected Return Date'),
                    _l('Return Date'),
                    _l('Reconciliation Date'),
                    _l('Return Quantity'),
                    _l('Used Quantity'),
                    _l('Remarks'),
                  ), 'table_manage_actual_stock_reconciliation', ['purchase_sm' => 'purchase_sm']); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-7 small-table-right-col">
        <div id="delivery_sm_view" class="hide">
        </div>
      </div>
      <?php $invoice_value = isset($invoice_id) ? $invoice_id : ''; ?>
      <?php echo form_hidden('invoice_id', $invoice_value) ?>

    </div>
  </div>
</div>

<div class="modal fade" id="send_goods_delivery" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open_multipart(admin_url('warehouse/send_goods_delivery'), array('id' => 'send_goods_delivery-form')); ?>
    <div class="modal-content modal_withd">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span><?php echo _l('send_delivery_note_by_email'); ?></span>
        </h4>
      </div>
      <div class="modal-body">
        <div id="additional_goods_delivery"></div>
        <div id="goods_delivery_invoice_id"></div>
        <div class="row">
          <div class="col-md-12 form-group">
            <label for="customer_name"><span class="text-danger">* </span><?php echo _l('customer_name'); ?></label>
            <select name="customer_name" id="customer_name" class="selectpicker" required data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">

            </select>
            <br>
          </div>

          <div class="col-md-12">
            <label for="email"><span class="text-danger">* </span><?php echo _l('email'); ?></label>
            <?php echo render_input('email', '', '', '', array('required' => 'true')); ?>
          </div>

          <div class="col-md-12">
            <label for="subject"><span class="text-danger">* </span><?php echo _l('_subject'); ?></label>
            <?php echo render_input('subject', '', '', '', array('required' => 'true')); ?>
          </div>
          <div class="col-md-12">
            <label for="attachment"><span class="text-danger">* </span><?php echo _l('acc_attach'); ?></label>
            <?php echo render_input('attachment', '', '', 'file', array('required' => 'true')); ?>
          </div>
          <div class="col-md-12">
            <?php echo render_textarea('content', 'email_content', '', array(), array(), '', 'tinymce') ?>
          </div>
          <div id="type_care">

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
    </div><!-- /.modal-content -->
    <?php echo form_close(); ?>
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
  var hidden_columns = [3, 4, 5];
</script>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/manage_stock_reconciliation_js.php'; ?>
</body>

</html>
<script>
  $(document).ready(function() {
    var table = $('.table-table_manage_delivery').DataTable();
    var actual_table = $('.table-table_manage_actual_stock_reconciliation').DataTable();

    // Handle "Select All" checkbox
    $('#select-all-goods-receipt-columns').on('change', function() {
      var isChecked = $(this).is(':checked');
      $('.toggle-goods-receipt-column').prop('checked', isChecked).trigger('change');
    });

    // Handle "Select All" checkbox
    $('#select-all-columns').on('change', function() {
      var isChecked = $(this).is(':checked');
      $('.toggle-column').prop('checked', isChecked).trigger('change');
    });

    // Handle individual column visibility toggling
    $('.toggle-goods-receipt-column').on('change', function() {
      var column = table.column($(this).val());
      column.visible($(this).is(':checked'));

      // Sync "Select All" checkbox state
      var allChecked = $('.toggle-goods-receipt-column').length === $('.toggle-goods-receipt-column:checked').length;
      $('#select-all-goods-receipt-columns').prop('checked', allChecked);
    });

    // Handle individual column visibility toggling
    $('.toggle-column').on('change', function() {
      var column = actual_table.column($(this).val());
      column.visible($(this).is(':checked'));

      // Sync "Select All" checkbox state
      var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
      $('#select-all-columns').prop('checked', allChecked);
    });

    // Sync checkboxes with column visibility on page load
    table.columns().every(function(index) {
      var column = this;
      $('.toggle-goods-receipt-column[value="' + index + '"]').prop('checked', column.visible());
    });

    // Sync checkboxes with column visibility on page load
    actual_table.columns().every(function(index) {
      var column = this;
      $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
    });

    // Prevent dropdown from closing when clicking inside
    $('.dropdown-menu').on('click', function(e) {
      e.stopPropagation();
    });

    $('#pt-charts-section').on('shown.bs.collapse', function() {
      $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    $('#pt-charts-section').on('hidden.bs.collapse', function() {
      $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
  });
</script>
<script>
  var table_manage_actual_stock_reconciliation = $('.table-table_manage_actual_stock_reconciliation');
  $(document).on('change', '.reconciliation_input', function() {
    let $el = $(this);

    let rowId = $el.data('rowid');
    let vendorId = $el.data('vendor');
    let newValue = $el.val().trim();

    $.post(admin_url + 'warehouse/update_reconciliation_date', {
        row_id: rowId,
        vendor_id: vendorId,
        value: newValue,
      })
      .done(function(response) {
        alert_float('success', 'Updated');
      })
      .fail(function() {
        alert_float('danger', 'Update failed');
      });
  });

  $(document).on('change', '.return_qty_input', function() {

    let $el = $(this);

    let rowId = $el.data('rowid');
    let vendorId = $el.data('vendor');
    let newValue = $el.val().trim();

    $.post(admin_url + 'warehouse/update_return_quantity', {
        row_id: rowId,
        vendor_id: vendorId,
        value: newValue,
      })
      .done(function(response) {
        alert_float('success', 'Updated');
        table_manage_actual_stock_reconciliation.DataTable().ajax.reload();
      })
      .fail(function() {
        alert_float('danger', 'Update failed');
      });

  });

  $(document).on('change', '.location_input', function() {

    let $el = $(this);
    let rowId = $el.data('rowid');
    let vendorId = $el.data('vendor');
    let value = $el.val().trim();

    $.post(admin_url + 'warehouse/update_location', {
        row_id: rowId,
        vendor_id: vendorId,
        value: value,
      })
      .done(function(response) {
        alert_float('success', 'Location updated');
      })
      .fail(function() {
        alert_float('danger', 'Update failed');
      });

  });

  $(document).on('change', '.return_date_input', function() {

    let $el = $(this);
    let rowId = $el.data('rowid');
    let vendorId = $el.data('vendor');
    let value = $el.val(); // date value (YYYY-MM-DD)

    $.post(admin_url + 'warehouse/update_return_date', {
        row_id: rowId,
        vendor_id: vendorId,
        value: value,
      })
      .done(function(response) {
        alert_float('success', 'Return date updated');
      })
      .fail(function() {
        alert_float('danger', 'Update failed');
      });

  });
</script>