<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>


<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12" id="small-table">
        <div class="panel_s">
          <?php echo form_open_multipart(admin_url('warehouse/add_stock_reconciliation'), array('id' => 'add_stock_reconciliation')); ?>
          <div class="panel-body">

            <div class="row">
              <div class="col-md-12">
                <h4 class="no-margin font-bold "><i class="fa fa-th-large menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                <hr>
              </div>
            </div>

            <?php
            $id = '';
            $additional_discount = 0;
            $shipping_fee = 0;
            $shipping_fee_number_attr = ['min' => '0.00', 'step' => 'any'];

            if (isset($goods_delivery)) {
              $id = $goods_delivery->id;
              echo form_hidden('isedit');
              $additional_discount = $goods_delivery->additional_discount;
              $shipping_fee = $goods_delivery->shipping_fee;
            }
            ?>
            <input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
            <input type="hidden" name="edit_approval" value="<?php echo html_entity_decode($edit_approval); ?>">
            <input type="hidden" name="save_and_send_request" value="false">
            <input type="hidden" name="additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">

            <!-- start -->
            <div class="row">
              <div class="col-md-12">
                <div class="row">

                  <div class="col-md-6">
                    <?php  $goods_delivery_code = isset($goods_delivery) ? $goods_delivery->goods_delivery_code : (isset($stock_reconciliation_code) ? $stock_reconciliation_code : ''); ?>
                    <?php echo render_input('goods_reconciliation_code', 'document_number', $goods_delivery_code, '', array('disabled' => 'true')) ?>
                  </div>

                  <!-- <div class="col-md-3">
                    <?php $date_c = isset($goods_delivery) ? $goods_delivery->date_c : $current_day; ?>
                    <?php $disabled = []; ?>

                    <?php if ($edit_approval == 'true') {
                      $disabled['disabled'] = 'true';
                    } ?>
                    <?php echo render_date_input('date_c', 'accounting_date', _d($date_c), $disabled) ?>

                  </div> -->
                  <div class="col-md-3">
                    <?php $date_add = isset($goods_delivery) ? $goods_delivery->date_add : $current_day; ?>
                    <?php echo render_date_input('date_add', 'Reconciliation Date', _d($date_add), $disabled) ?>
                  </div>
                  <?php if (ACTIVE_PROPOSAL == true) { ?>
                  <div class="col-md-3 form-group <?php if ($pr_orders_status == false) {
                                                      echo 'hide';
                                                    }; ?>">
                      <label for="project"><?php echo _l('project'); ?></label>
                      <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>

                        <?php if (isset($projects)) { ?>
                          <?php foreach ($projects as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if (isset($goods_delivery) && $s['id'] == $goods_delivery->project) {
                                                                                          echo 'selected';
                                                                                        } ?>><?php echo html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  <?php } ?>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pr_order_id"><?php echo _l('reference_purchase_order'); ?></label>
                      <select onchange="pr_order_change(this); return false;" name="pr_order_id" id="pr_order_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>"
                        <?php echo ($edit_approval == 'true') ? 'disabled' : ''; ?>>
                        <option value=""></option>
                        <?php foreach ($pr_orders as $pr_order) { ?>
                          <option value="<?php echo html_entity_decode($pr_order['id']); ?>" <?php if (isset($goods_delivery) && ($goods_delivery->pr_order_id == $pr_order['id'])) {
                                                                                                echo 'selected';
                                                                                              } ?>><?php echo html_entity_decode($pr_order['pur_order_number'] . ' - ' . $pr_order['pur_order_name'] . ' - ' . get_vendor_name($pr_order['vendor'])); ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <?php if (ACTIVE_PROPOSAL == true) { ?>

                    

                    <div class="col-md-3 form-group <?php if ($pr_orders_status == false) {
                                                      echo 'hide';
                                                    }; ?>">
                      <label for="type"><?php echo _l('type_label'); ?></label>
                      <select name="type" id="type" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <option value="capex" <?php if (isset($goods_delivery) && $goods_delivery->type == 'capex') {
                                                echo 'selected';
                                              } ?>><?php echo _l('capex'); ?></option>
                        <option value="opex" <?php if (isset($goods_delivery) && $goods_delivery->type == 'opex') {
                                                echo 'selected';
                                              } ?>><?php echo _l('opex'); ?></option>
                      </select>
                    </div>

                    <div class="col-md-3 form-group <?php if ($pr_orders_status == false) {
                                                      echo 'hide';
                                                    }; ?>">
                      <label for="department"><?php echo _l('department'); ?></label>
                      <select name="department" id="department" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php if (isset($departments)) { ?>
                          <?php foreach ($departments as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['departmentid']); ?>" <?php if (isset($goods_delivery) && $s['departmentid'] == $goods_delivery->department) {
                                                                                                    echo 'selected';
                                                                                                  } ?>><?php echo html_entity_decode($s['name']); ?></option>
                          <?php } ?>

                        <?php } ?>

                      </select>
                    </div>

                    <div class="col-md-3 form-group <?php if ($pr_orders_status == false) {
                                                      echo 'hide';
                                                    }; ?>">
                      <label for="requester"><?php echo _l('requester'); ?></label>
                      <select name="requester" id="requester" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php if (isset($staffs)) { ?>

                          <?php foreach ($staffs as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['staffid']); ?>" <?php if (isset($goods_delivery) && $s['staffid'] == $goods_delivery->requester) {
                                                                                                echo 'selected';
                                                                                              } ?>><?php echo html_entity_decode($s['lastname'] . ' ' . $s['firstname']); ?></option>
                          <?php } ?>

                        <?php } ?>

                      </select>
                    </div>

                  <?php } ?>

                  <div class="col-md-3 hide">
                    <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('goods_delivery_warehouse_tooltip'); ?>"></i></a>

                    <div class="form-group">
                      <label for="warehouse_id"><?php echo _l('warehouse_name'); ?></label>
                      <select name="warehouse_id" id="warehouse_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" <?php if ($edit_approval == 'true') {
                                                                                                                                                                                                                  echo 'disabled';
                                                                                                                                                                                                                }; ?>>

                        <option value=""></option>
                        <?php foreach ($warehouses as $wh_value) { ?>
                          <option value="<?php echo html_entity_decode($wh_value['warehouse_id']); ?>" <?php if (isset($goods_delivery) && $goods_delivery->warehouse_id == $wh_value['warehouse_id']) {
                                                                                                          echo 'selected';
                                                                                                        } ?>><?php echo ($wh_value['warehouse_name']); ?></option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>



                  <!-- <div class=" col-md-6">
                    <div class="form-group">
                      <label for="staff_id" class="control-label"><?php echo _l('salesman'); ?></label>
                      <select name="staff_id" class="selectpicker" id="staff_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php if ($edit_approval == 'true') {
                                                                                                                                                                              echo 'disabled';
                                                                                                                                                                            }; ?>>
                        <option value=""></option>
                        <?php foreach ($staff as $s) { ?>
                          <option value="<?php echo html_entity_decode($s['staffid']); ?>" <?php if (isset($goods_delivery) && $goods_delivery->staff_id == $s['staffid']) {
                                                                                              echo 'selected';
                                                                                            } ?>> <?php echo html_entity_decode($s['firstname']) . '' . html_entity_decode($s['lastname']); ?></option>
                        <?php } ?>
                      </select>

                    </div>
                  </div> -->


                  <div class="col-md-3 form-group">
                    <?php $invoice_no = (isset($goods_delivery) ? $goods_delivery->invoice_no : '');
                    echo render_input('invoice_no', 'invoice_no', $invoice_no, '', $disabled) ?>

                  </div>

                </div>


              </div>

            </div>

          </div>
          <div class="panel_s">
            <div class="panel-body">
              <label for="attachment"><?php echo _l('attachment'); ?></label>
              <div class="attachments">
                <div class="attachment">
                  <div class="col-md-5 form-group" style="padding-left: 0px;">
                    <div class="input-group">
                      <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                      <span class="input-group-btn">
                        <button class="btn btn-success add_more_attachments p8" type="button"><i class="fa fa-plus"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <br /> <br />

              <?php
              if (isset($attachments) && count($attachments) > 0) {
                foreach ($attachments as $value) {
                  echo '<div class="col-md-3">';
                  $path = get_upload_path_by_type('inventory') . 'goods_delivery/' . $value['rel_id'] . '/' . $value['file_name'];
                  $is_image = is_image($path);
                  if ($is_image) {
                    echo '<div class="preview_image">';
                  }
              ?>
                  <a href="<?php echo site_url('download/file/inventory/' . $value['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-inventory-<?php echo $value['rel_id']; ?>" <?php } ?>>
                    <i class="<?php echo get_mime_class($value['filetype']); ?>"></i> <?php echo $value['file_name']; ?>
                    <?php if ($is_image) { ?>
                      <img class="mtop5" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']); ?>" style="height: 165px;">
                    <?php } ?>
                  </a>
                  <?php if ($is_image) {
                    echo '</div>';
                    echo '<a href="' . admin_url('warehouse/delete_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                  } ?>
              <?php echo '</div>';
                }
              } ?>
            </div>
          </div>
          <div class="panel-body mtop10 invoice-item">
            <div class="row">
              <!-- <div class="col-md-4">
                <?php $this->load->view('warehouse/item_include/main_item_select'); ?>
              </div> -->
              <div class="col-md-12 text-right">
                <label class="bold mtop10 text-right" data-toggle="tooltip" title="" data-original-title="<?php echo _l('support_barcode_scanner_tooltip'); ?>"><?php echo _l('support_barcode_scanner'); ?>
                  <i class="fa fa-question-circle i_tooltip"></i></label>
              </div>
            </div>

            <div class="table-responsive s_table ">
              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                  <tr>
                    <th width="1%"></th>
                    <th width="13%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('Uniclass Code'); ?></th>
                    <th width="13%" align="left"><?php echo _l('item_description'); ?></th>
                    <th width="9%" align="left"><?php echo _l('area'); ?></th>
                    <th width="10%" align="left"><?php echo _l('warehouse_name'); ?></th>
                    <th width="9%" align="left" class="available_quantity"><?php echo _l('Issued Quantity'); ?></th>
                    <th width="9%" align="left"><?php echo _l('Return Date'); ?></th>
                    <th width="11%" align="left"><?php echo _l('Reconciliation Date'); ?></th>
                    <th width="8%" align="left" ><?php echo _l('Return Quantity'); ?></th>
                    <th width="8%" align="left" ><?php echo _l('Used Quantity'); ?></th>
                    <th width="8%" align="left" ><?php echo _l('Location'); ?></th>
                    <!-- <th align="center" width='1%'><i class="fa fa-cog"></i></th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php echo html_entity_decode($stock_reconciliation_row_template); ?>
                </tbody>
              </table>
            </div>
            <div id="removed-items"></div>
          </div>


          <div class="row">
            <div class="col-md-12 mtop15">
              <div class="panel-body bottom-transaction">

                <?php $description = (isset($goods_delivery) ? $goods_delivery->description : ''); ?>
                <?php echo render_textarea('description', 'note_', $description, array(), array(), 'mtop15'); ?>


                <div class="btn-bottom-toolbar text-right">
                  <a href="<?php echo admin_url('warehouse/manage_delivery'); ?>" class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>

                  <?php if (wh_check_approval_setting('2') != false) { ?>
                    <?php if (isset($goods_delivery) && $goods_delivery->approval != 1) { ?>
                      <a href="javascript:void(0)" class="btn btn-info pull-right mright5 add_goods_delivery_send"><?php echo _l('save_send_request'); ?></a>
                    <?php } elseif (!isset($goods_delivery)) { ?>
                      <a href="javascript:void(0)" class="btn btn-info pull-right mright5 add_goods_delivery_send"><?php echo _l('save_send_request'); ?></a>
                    <?php } ?>
                  <?php } ?>

                  <?php if (is_admin() || has_permission('warehouse', '', 'edit') || has_permission('warehouse', '', 'create')) { ?>
                    <?php if (isset($goods_delivery) && $goods_delivery->approval == 0) { ?>
                      <a href="javascript:void(0)" class="btn btn-info pull-right mright5 add_goods_delivery"><?php echo _l('save'); ?></a>
                    <?php } elseif (!isset($goods_delivery)) { ?>
                      <a href="javascript:void(0)" class="btn btn-info pull-right mright5 add_goods_delivery"><?php echo _l('save'); ?></a>
                    <?php } elseif (isset($goods_delivery) && $goods_delivery->approval == 1 && is_admin()) { ?>
                      <a href="javascript:void(0)" class="btn btn-info pull-right mright5 add_goods_delivery"><?php echo _l('save'); ?></a>
                    <?php } ?>

                  <?php } ?>

                </div>
              </div>
              <div class="btn-bottom-pusher"></div>
            </div>
          </div>


        </div>




      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
</div>
</div>
<div id="modal_wrapper"></div>
<div id="change_serial_modal_wrapper"></div>
<input type="hidden" id="apply_to_all_value" value="0">
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/goods_reconciliation_js.php'; ?>
</body>
<script>
  let userSelectedVendorOptions = {}; // Store user-selected vendor options per item key

  function handleVendorSelection(selectElement, itemKey) {
    let selectedValues = $(selectElement).val() || [];

    // Store user-selected vendor options per dropdown
    userSelectedVendorOptions[itemKey] = selectedValues;
  }

  $(document).ready(function() {
    let selectedVendorOptions = {}; // Store user-selected vendors

    // Capture vendor selection changes
    $(document).on("change", ".vendor_list", function() {
      let itemKey = $(this).data("item-key");
      let selectedValues = $(this).val() || [];

      // Store selection for the specific itemKey
      selectedVendorOptions[itemKey] = selectedValues;

    });

    // Handle "Apply to All" button click
    $(document).on("click", ".apply-to-all-btn", function() {
      let itemKey = $(this).data("item-key");
      $('#apply_to_all_value').val('1');
      if (!selectedVendorOptions[itemKey] || selectedVendorOptions[itemKey].length === 0) {
        alert("Please select at least one vendor before applying.");
        return;
      }

      let selectedVendors = selectedVendorOptions[itemKey];

      // Apply to all vendor dropdowns
      $(".vendor_list").each(function() {
        $(this).val(selectedVendors).trigger("change"); // Ensure change event triggers
      });

      // Refresh all dropdowns (if using selectpicker or similar plugin)
      $(".vendor_list").selectpicker("refresh");
      $('#apply_to_all_value').val('0');
    });
  });
</script>

</html>