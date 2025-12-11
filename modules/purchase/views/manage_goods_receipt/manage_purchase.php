<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
            <?php echo form_hidden('purchase_id', $purchase_id); ?>
            <div class="row">
              <div class="col-md-12" style="padding: 0px;">
                <div class="col-md-12" id="heading">
                  <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l('client_supply_tracker'); ?></h4>
                  <hr />
                </div>
              </div>
              <div class="col-md-12">
                <a href="<?php echo admin_url('purchase/activity_log?module=pt'); ?>" class="btn btn-info pull-left mright10 display-block" target="_blank"><?php echo _l('activity_log'); ?></a>
                <button class="btn btn-info display-block" type="button" data-toggle="collapse" data-target="#pt-charts-section" aria-expanded="true" aria-controls="pt-charts-section">
                  <?php echo _l('Purchase Tracker Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                </button>
              </div>
            </div>

            <div id="pt-charts-section" class="collapse in">
              <div class="row">
                <div class="col-md-12 mtop20">
                  <div class="row">
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title">Total PO</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value total_po"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title">Avg Lead Time (Days)</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value average_lead_time"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title">% Delivered (On Time)</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value percentage_delivered"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title">% Adv Payments Made</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value average_advance_payments"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title">% Shop Dwg Approval</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value shop_drawings_approval"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mtop20">
                <div class="col-md-4">
                  <p class="mbot15 dashboard_stat_title">PO Status Breakdown</p>
                  <div style="width: 100%; height: 400px;">
                    <canvas id="barChartPOStatus"></canvas>
                  </div>
                </div>
                <div class="col-md-4">
                  <p class="mbot15 dashboard_stat_title">Procurement by Category</p>
                  <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                    <canvas id="pieChartForCategory"></canvas>
                  </div>
                </div>
                <div class="col-md-4">
                  <p class="mbot15 dashboard_stat_title">Delivery Performance</p>
                  <div style="width: 100%; height: 450px;">
                    <canvas id="pieChartDeliveryPerformance"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mtop20">
              <div class="col-md-1 pull-right">
                <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal(' .purchase_sm','#purchase_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
              </div>
            </div>
            <br />
            <div class="row">
              <div class="col-md-3">
                <?php
                $input_attr_e = [];
                $input_attr_e['placeholder'] = _l('day_vouchers');

                echo render_date_input('date_add', '', '', $input_attr_e); ?>
              </div>

              <div class="col-md-3">
                <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('cat'); ?>">
                  <option value=""></option>
                  <option value="Client Supply"><?php echo _l('client_supply'); ?></option>
                  <option value="Bought out items"><?php echo _l('bought_out_items'); ?></option>
                </select>
              </div>

              <div class="col-md-3">
                <select name="delivery" id="delivery" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('delivery_status'); ?>">
                  <option value=""></option>
                  <option value="undelivered"><?php echo _l('undelivered'); ?></option>
                  <option value="partially_delivered"><?php echo _l('partially_delivered'); ?></option>
                  <option value="completely_delivered"><?php echo _l('completely_delivered'); ?></option>
                </select>
              </div>

              <div class="col-md-3 form-group">
                <?php
                echo render_select('vendors[]', $vendors, array('userid', 'company'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('vendor'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                ?>
              </div>

              <div class="col-md-3 form-group">
                <?php
                $group_pur = get_budget_head_project_wise();
                echo render_select('group_pur[]', $group_pur, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('group_pur'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                ?>
              </div>

              <div class="col-md-3 form-group">
                <?php
                $tracker_status = get_purchase_tracker_status();
                echo render_select('tracker_status[]', $tracker_status, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                ?>
              </div>

              <div class="col-md-3 form-group">
                <?php
                $production_status = get_purchase_tracker_production_status();
                echo render_select('production_status[]', $production_status, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('production_status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                ?>
              </div>

              <div class="col-md-3 form-group"> 
                <select name="wo_po_order[]" id="wo_po_order" multiple class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('Choose From Order'); ?>">
                  <option value=""></option>

                  <?php
                  $po_wo_orders = get_purchase_work_order();
                  // echo render_select('wo_po_order[]', $po_wo_orders, array('id', 'name','type'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('Choose From Order'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);

                  foreach ($po_wo_orders as $key => $value) {
                    echo '<option value="' . $value['id'] . '-' . $value['type'].'-'.$value['goods_id'] . '">' . $value['name'] . '</option>';
                  }
                  ?>
                </select>
              </div>

            </div>
            <br />

            <div class="row">
              <div class="col-md-12">
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                      <a href="#tracker_1" aria-controls="tracker_1" role="tab" id="tab_tracker_1" data-toggle="tab">
                        General Information
                      </a>
                    </li>
                    <li role="presentation">
                      <a href="#tracker_2" aria-controls="tracker_2" role="tab" id="tab_tracker_2" data-toggle="tab">
                        Listing
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="tab-content">
                <div role="tabpanel" class="col-md-12 tab-pane tracker-pane active" id="tracker_1">
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
                        _l('Docket code'),
                        _l('Choose From Order'),
                        _l('commodity_code'),
                        _l('description'),
                        _l('area'),
                        _l('po_quantity'),
                        _l('received_quantity'),
                        _l('remaining_quantity'),
                        _l('supplier_name'),
                        _l('category'),
                        _l('day_vouchers'),
                        _('Last Action By'),
                        _l('imported_local'),
                        _l('status'),
                        _l('production_status'),
                        _l('payment_date'),
                        _l('est_delivery_date'),
                        _l('delivery_date'),
                        _l('management_remarks'),
                        _l('lead_time_days'),
                        _l('advance_payment'),
                        _l('shop_drawings_upload'),
                        _l('shop_drawings_download'),
                        _l('shop_drawings_submission'),
                        _l('shop_drawings_approval'),
                        _l('procurement_remarks'),
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
                    _l('Docket code'),
                    _l('Choose From Order'),
                    _l('commodity_code'),
                    _l('description'),
                    _l('area'),
                    _l('po_quantity'),
                    _l('received_quantity'),
                    _l('remaining_quantity'),
                    _l('supplier_name'),
                    _l('category'),
                    _l('day_vouchers'),
                    _('Last Action By'),
                    _l('imported_local'),
                    _l('status'),
                    _l('production_status'),
                    _l('payment_date'),
                    _l('est_delivery_date'),
                    _l('delivery_date'),
                    _l('management_remarks'),
                    _l('lead_time_days'),
                    _l('advance_payment'),
                    _l('shop_drawings_upload'),
                    _l('shop_drawings_download'),
                    _l('shop_drawings_submission'),
                    _l('shop_drawings_approval'),
                    _l('procurement_remarks'),
                  ), 'table_manage_actual_goods_receipt', ['purchase_sm' => 'purchase_sm']); ?>
                </div>
                <div role="tabpanel" class="col-md-12 tab-pane tracker-pane" id="tracker_2">
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
                        _l('id'),
                        _l('stock_received_docket_code'),
                        _l('Choose From Order'),
                        _l('supplier_name'),
                        // _l('Buyer'),
                        _l('category'),
                        _l('day_vouchers'),
                        _l('production_status'),
                        _l('status_label'),
                        _('Last Action By'),
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
                    _l('id'),
                    _l('stock_received_docket_code'),
                    _l('Choose From Order'),
                    _l('supplier_name'),
                    _l('category'),
                    _l('day_vouchers'),
                    _l('production_status'),
                    _l('status_label'),
                    _('Last Action By'),
                  ), 'table_manage_goods_receipt', ['purchase_sm' => 'purchase_sm']); ?>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="col-md-7 small-table-right-col">
        <div id="purchase_sm_view" class="hide">
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="send_goods_received" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open_multipart(admin_url('warehouse/send_goods_received'), array('id' => 'send_goods_received-form')); ?>
    <div class="modal-content modal_withd">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span><?php echo _l('send_received_note'); ?></span>
        </h4>
      </div>
      <div class="modal-body">
        <div id="additional_goods_received"></div>
        <div class="row">
          <div class="col-md-12 form-group">
            <label for="vendor"><span class="text-danger">* </span><?php echo _l('vendor'); ?></label>
            <select name="vendor[]" id="vendor" class="selectpicker" required multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
              <?php foreach ($vendors as $s) { ?>
                <option value="<?php echo html_entity_decode($s['userid']); ?>"><?php echo html_entity_decode($s['company']); ?></option>
              <?php } ?>
            </select>
            <br>
          </div>

          <div class="col-md-12">
            <label for="subject"><span class="text-danger">* </span><?php echo _l('subject'); ?></label>
            <?php echo render_input('subject', '', '', '', array('required' => 'true')); ?>
          </div>
          <div class="col-md-12">
            <label for="attachment"><span class="text-danger">* </span><?php echo _l('attachment'); ?></label>
            <?php echo render_input('attachment', '', '', 'file', array('required' => 'true')); ?>
          </div>
          <div class="col-md-12">
            <?php echo render_textarea('content', 'content', '', array(), array(), '', 'tinymce') ?>
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
<script>
  $(document).ready(function() {
    var table = $('.table-table_manage_goods_receipt').DataTable();
    var actual_table = $('.table-table_manage_actual_goods_receipt').DataTable();

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
<?php require 'modules/warehouse/assets/js/view_purchase_js.php'; ?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>