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
                  <h4 class="no-margin font-bold"><i class="fa fa-ship menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                  <hr />
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
              <div class="col-md-12">
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                      <a href="#tracker_1" aria-controls="tracker_1" role="tab" id="tab_tracker_1" data-toggle="tab">
                        Listing
                      </a>
                    </li>
                    <li role="presentation">
                      <a href="#tracker_2" aria-controls="tracker_2" role="tab" id="tab_tracker_2" data-toggle="tab">
                        General Information
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
                    _l('Voucher Code'),
                    _l('Choose From Order'),
                    _l('Reconciliation Date'),
                    _l('Reconciliation Status'),
                  ), 'table_manage_stock_reconciliation_list', ['purchase_sm' => 'purchase_sm']); ?>
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
                    // _l('Choose From Order'),
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

<!-- <script>
  var hidden_columns = [3, 4, 5];
</script> -->
<?php init_tail(); ?>
<script>
  $(document).ready(function() {
    var table = $('.table-table_manage_stock_reconciliation_list').DataTable();
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
<?php require 'modules/warehouse/assets/js/view_purchase_js.php'; ?>
</body>

</html>