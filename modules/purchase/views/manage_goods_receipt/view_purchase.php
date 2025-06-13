<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style type="text/css">
  /* .purchase-head th,
  .purchase-body td {
    white-space: nowrap;
  } */
  /* .items-preview {
    table-layout: fixed; 
    width: 100%; 
  } */
</style>
<div class="col-md-12 no-padding">
  <div class="panel_s">
    <div class="panel-body">
      <?php if ($goods_receipt->approval == 0) { ?>
        <div class="ribbon info"><span><?php echo _l('not_yet_approve'); ?></span></div>
      <?php } elseif ($goods_receipt->approval == 1) { ?>
        <div class="ribbon success"><span><?php echo _l('approved'); ?></span></div>
      <?php } elseif ($goods_receipt->approval == -1) { ?>
        <div class="ribbon danger"><span><?php echo _l('reject'); ?></span></div>
      <?php } ?>
      <div class="horizontal-scrollable-tabs preview-tabs-top">
        <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
          <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
            <li role="presentation" class="active">
              <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                Item Tracking
              </a>
            </li>
            <li role="presentation">
              <a href="#tab_attachment" aria-controls="tab_attachment" role="tab" data-toggle="tab">
                Attachment
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="pull-right" style="margin-right: 10px;font-size: 18px;margin-top: 4px;">
        <a href="#" onclick="small_table_full_view(); return false;">
          <i class="fa fa-expand" style="color: #000000 !important;"></i></a>
      </div>

      <div class="clearfix"></div>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
          <div id="estimate-preview">
            <div class="col-md-12 panel-padding">
              <table class="table border table-striped table-margintop">
                <tbody>

                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('supplier_name'); ?></td>

                    <?php
                    if (get_status_modules_wh('purchase') && ($goods_receipt->supplier_code != '') && ($goods_receipt->supplier_code != 0)) { ?>
                      <td><?php echo html_entity_decode(wh_get_vendor_company_name($goods_receipt->supplier_code)); ?></td>
                    <?php   } else { ?>
                      <td><?php echo html_entity_decode($goods_receipt->supplier_name); ?></td>
                    <?php   }

                    ?>

                  </tr>
                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('Buyer'); ?></td>
                    <td><?php echo get_staff_full_name($goods_receipt->buyer_id); ?></td>
                  </tr>
                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('stock_received_docket_code'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->goods_receipt_code); ?></td>
                  </tr>
                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('category'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->kind); ?></td>
                  </tr>

                  <?php
                  if (get_status_modules_wh('purchase')) {
                    if (($goods_receipt->pr_order_id != '') && ($goods_receipt->pr_order_id != 0)) { ?>

                      <tr class="project-overview">
                        <td class="bold"><?php echo _l('reference_purchase_order'); ?></td>
                        <td>
                          <a href="<?php echo admin_url('purchase/purchase_order/' . $goods_receipt->pr_order_id) ?>"><?php echo get_pur_order_name($goods_receipt->pr_order_id) ?></a>

                        </td>
                      </tr>

                  <?php   }
                  }
                  ?>

                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('group_pur'); ?></td>
                    <td><?php echo get_group_name_by_id($pur_order->group_pur); ?></td>
                  </tr>

                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('po_date'); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($pur_order->order_date)); ?></td>
                  </tr>

                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('po_amount'); ?></td>
                    <td><?php echo app_format_money($pur_order->total, $base_currency); ?></td>
                  </tr>

                  <?php
                  if (isset($purchase_tracker)) { ?>
                    <td class="bold"><?php echo _l('print'); ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if (is_mobile()) {
                                                                                                                                                                              echo ' PDF';
                                                                                                                                                                            } ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                          <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                          <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                          <li><a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id); ?>"><?php echo _l('download'); ?></a></li>
                          <li>
                            <a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id . '?print=true'); ?>" target="_blank">
                              <?php echo _l('print'); ?>
                            </a>
                          </li>
                        </ul>
                      </div>

                    </td>
                  <?php } ?>
                  </tr>


                </tbody>
              </table>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                      <a href="#general_information" aria-controls="general_information" role="tab" id="tab_general_information" data-toggle="tab">
                        General Information
                      </a>
                    </li>
                    <?php
                    // if (empty($goods_receipt->goods_receipt_code)) { 

                    ?>
                    <li role="presentation">
                      <a href="#actual" aria-controls="actual" role="tab" id="tab_actual" data-toggle="tab">
                        Actual
                      </a>
                    </li>
                    <?php
                    // } 
                    ?>
                  </ul>
                </div>
              </div>

              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general_information">
                  <div class="col-md-10 pull-right" style="z-index: 99999;display: flex;justify-content: end;">

                    <span style="margin-right: 10px;">
                      <button class="btn btn-primary" id="settings-toggle">Columns</button>
                      <div id="settings-dropdown" style="display: none; position: absolute; background: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204); padding: 10px;width:165px;right: 24px;">

                        <label><input type="checkbox" class="column-toggle" data-column="1" checked=""> <?php echo _l('commodity_code') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="2" checked=""> <?php echo _l('description') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="3" checked=""> <?php echo _l('area') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="4" checked=""> <?php echo _l('po_quantity') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="5" checked=""> <?php echo _l('received_quantity') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="6" checked=""> <?php echo _l('imported_local') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="7" checked=""> <?php echo _l('status') ?></label><br>
                        <label><input type="checkbox" class="column-toggle" data-column="8" checked=""> <?php echo _l('production_status') ?></label>
                        <label><input type="checkbox" class="column-toggle" data-column="9" checked=""> <?php echo _l('payment_date') ?></label>
                        <label><input type="checkbox" class="column-toggle" data-column="10" checked=""> <?php echo _l('est_delivery_date') ?></label>
                        <label><input type="checkbox" class="column-toggle" data-column="11" checked=""><?php echo _l('delivery_date') ?></label>
                        <label><input type="checkbox" class="column-toggle" data-column="12" checked=""><?php echo _l('remarks') ?></label>
                      </div>
                    </span>
                  </div>
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table class="table items items-preview estimate-items-preview" data-type="estimate">
                        <thead class="purchase-head">
                          <tr>
                            <th align="center">#</th>
                            <th><?php echo _l('commodity_code') ?></th>
                            <th><?php echo _l('description') ?></th>
                            <th><?php echo _l('area') ?></th>
                            <th><?php echo _l('po_quantity') ?></th>
                            <th><?php echo _l('received_quantity') ?></th>
                            <th><?php echo _l('imported_local') ?></th>
                            <th><?php echo _l('status') ?> <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="SPC - Specification Installation<br>RFQ - RFQ Sent<br>FQR - Final Quotes received<br>POI - Purchase order Issued<br>PIR - PI Received"></i></th>
                            <th><?php echo _l('production_status') ?></th>
                            <th><?php echo _l('payment_date') ?></th>
                            <th><?php echo _l('est_delivery_date') ?></th>
                            <th><?php echo _l('delivery_date') ?></th>
                            <th><?php echo _l('remarks') ?></th>
                          </tr>
                        </thead>
                        <tbody class="ui-sortable purchase-body">

                          <?php
                          foreach ($goods_receipt_detail as $receipt_key => $receipt_value) {

                            $receipt_key++;
                            $po_quantities = (isset($receipt_value) ? $receipt_value['po_quantities'] : 0);
                            $quantities = (isset($receipt_value) ? $receipt_value['quantities'] : 0);
                            $remaining_quantities = $po_quantities - $quantities;
                            $unit_price = (isset($receipt_value) ? $receipt_value['unit_price'] : '');
                            $unit_price = (isset($receipt_value) ? $receipt_value['unit_price'] : '');
                            $goods_money = (isset($receipt_value) ? $receipt_value['goods_money'] : '');

                            $commodity_code = get_commodity_name($receipt_value['commodity_code']) != null ? get_commodity_name($receipt_value['commodity_code'])->commodity_code : '';
                            $commodity_name = get_commodity_name($receipt_value['commodity_code']) != null ? get_commodity_name($receipt_value['commodity_code'])->description : '';

                            $unit_name = '';
                            if (is_numeric($receipt_value['unit_id'])) {
                              $unit_name = (get_unit_type($receipt_value['unit_id']) != null && isset(get_unit_type($receipt_value['unit_id'])->unit_name)) ? get_unit_type($receipt_value['unit_id'])->unit_name : '';
                            }

                            $warehouse_code = get_warehouse_name($receipt_value['warehouse_id']) != null ? get_warehouse_name($receipt_value['warehouse_id'])->warehouse_name : '';
                            $tax_money = (isset($receipt_value) ? $receipt_value['tax_money'] : '');
                            $expiry_date = (isset($receipt_value) ? $receipt_value['expiry_date'] : '');
                            $lot_number = (isset($receipt_value) ? $receipt_value['lot_number'] : '');
                            $commodity_name = $receipt_value['commodity_name'];
                            $description = $receipt_value['description'];
                            if (strlen($commodity_name) == 0) {
                              $commodity_name = wh_get_item_variatiom($receipt_value['commodity_code']);
                            }

                            if (strlen($receipt_value['serial_number']) > 0) {
                              $name_serial_number_tooltip = _l('wh_serial_number') . ': ' . $receipt_value['serial_number'];
                            } else {
                              $name_serial_number_tooltip = '';
                            }

                            $vendor_name = !empty($receipt_value['vendor_id']) ? wh_get_vendor_company_name($receipt_value['vendor_id']) : '';
                            $delivery_date = !empty($receipt_value['delivery_date']) ? $receipt_value['delivery_date'] : null;
                            $payment_date = !empty($receipt_value['payment_date']) ? $receipt_value['payment_date'] : null;
                            $est_delivery_date = !empty($receipt_value['est_delivery_date']) ? $receipt_value['est_delivery_date'] : null;
                            $production_status = '';
                            $production_labels = [
                              1 => ['label' => 'danger', 'table' => 'not_started', 'text' => _l('not_started')],
                              2 => ['label' => 'success', 'table' => 'approved', 'text' => _l('approved')],
                              3 => ['label' => 'info', 'table' => 'on_going', 'text' => _l('on_going')],
                              4 => ['label' => 'warning', 'table' => 'delivered', 'text' => _l('Delivered')],
                            ];
                            if ($receipt_value['production_status'] > 0) {

                              $status = $production_labels[$receipt_value['production_status']];
                              $production_status = '<span class="inline-block label label-' . $status['label'] . '" id="status_span_' . $receipt_value['id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];

                              $production_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                              $production_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                              $production_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                              $production_status .= '</a>';
                              $production_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';
                              foreach ($production_labels as $key => $status) {
                                if ($key != $receipt_value['production_status']) {
                                  $production_status .= '<li>
                                           <a href="#" onclick="change_production_status(' . $key . ', ' . $receipt_value['id'] . '); return false;">
                                               ' . $status['text'] . '
                                           </a>
                                       </li>';
                                }
                              }
                              $production_status .= '</ul>';
                              $production_status .= '</div>';

                              $production_status .= '</span>';
                            }

                            $imp_local_status = '';
                            $imp_local_labels = [
                              1 => ['label' => 'danger', 'table' => 'not_set', 'text' => _l('not_set')],
                              2 => ['label' => 'success', 'table' => 'imported', 'text' => _l('imported')],
                              3 => ['label' => 'info', 'table' => 'local', 'text' => _l('local')],
                            ];
                            if ($receipt_value['imp_local_status'] > 0) {
                              $status = $imp_local_labels[$receipt_value['imp_local_status']];
                              $imp_local_status = '<span class="inline-block label label-' . $status['label'] . '" id="imp_status_span_' . $receipt_value['id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];

                              $imp_local_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                              $imp_local_status .= '<a href="#" class="dropdown-toggle text-dark" id="tableImpLocalStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                              $imp_local_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                              $imp_local_status .= '</a>';
                              $imp_local_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableImpLocalStatus-' . $aRow['id'] . '">';
                              foreach ($imp_local_labels as $key => $status) {
                                if ($key != $receipt_value['imp_local_status']) {
                                  $imp_local_status .= '<li>
                                           <a href="#" onclick="change_imp_local_status(' . $key . ', ' . $receipt_value['id'] . '); return false;">
                                               ' . $status['text'] . '
                                           </a>
                                       </li>';
                                }
                              }
                              $imp_local_status .= '</ul>';
                              $imp_local_status .= '</div>';
                              $imp_local_status .= '</span>';
                            }

                            $tracker_status = '';
                            $tracker_status_labels = [
                              1 => ['label' => 'danger', 'table' => 'not_set', 'text' => _l('not_set')],
                              2 => ['label' => 'info', 'table' => 'SPC', 'text' => 'SPC'],
                              3 => ['label' => 'info', 'table' => 'RFQ', 'text' => 'RFQ'],
                              4 => ['label' => 'info', 'table' => 'FQR', 'text' => 'FQR'],
                              5 => ['label' => 'info', 'table' => 'POI', 'text' => 'POI'],
                              6 => ['label' => 'info', 'table' => 'PIR', 'text' => 'PIR'],
                            ];
                            if ($receipt_value['tracker_status'] > 0) {
                              $status = $tracker_status_labels[$receipt_value['tracker_status']];
                              $tracker_status = '<span class="inline-block label label-' . $status['label'] . '" id="tracker_status_span_' . $receipt_value['id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];

                              $tracker_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                              $tracker_status .= '<a href="#" class="dropdown-toggle text-dark" id="tableTrackerStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                              $tracker_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                              $tracker_status .= '</a>';
                              $tracker_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTrackerStatus-' . $aRow['id'] . '">';
                              foreach ($tracker_status_labels as $key => $status) {
                                if ($key != $receipt_value['tracker_status']) {
                                  $tracker_status .= '<li>
                                           <a href="#" onclick="change_tracker_status(' . $key . ', ' . $receipt_value['id'] . '); return false;">
                                               ' . $status['text'] . '
                                           </a>
                                       </li>';
                                }
                              }
                              $tracker_status .= '</ul>';
                              $tracker_status .= '</div>';
                              $tracker_status .= '</span>';
                            }
                            $remarks = $receipt_value['remarks'];
                          ?>

                            <tr data-toggle="tooltip" data-original-title="<?php echo html_entity_decode($name_serial_number_tooltip); ?>">
                              <td><?php echo html_entity_decode($receipt_key) ?></td>
                              <td><?php echo html_entity_decode($commodity_name) ?></td>
                              <td><?php echo html_entity_decode($description) ?></td>
                              <td><?php echo get_area_name_by_id($receipt_value['area']); ?></td>
                              <td><?php echo html_entity_decode($po_quantities) . ' ' . html_entity_decode($unit_name) ?></td>
                              <td><?php echo html_entity_decode($quantities) . ' ' . html_entity_decode($unit_name) ?></td>
                              <td><?php echo $imp_local_status ?></td>
                              <td><?php echo $tracker_status ?></td>
                              <td><?php echo $production_status ?></td>
                              <td>
                                <?php
                                echo '<input type="date" class="form-control payment-date-input"
                                  value="' . $payment_date . '"
                                  data-id="' . $receipt_value['id'] . '"
                                  ">';
                                ?>
                              </td>
                              <td>
                                <?php
                                echo '<input type="date" class="form-control est-delivery-date-input"
                                  value="' . $est_delivery_date . '"
                                  data-id="' . $receipt_value['id'] . '"
                                  ">';
                                ?>
                              </td>
                              <td>
                                <?php
                                echo '<input type="date" class="form-control delivery-date-input"
                                  value="' . $delivery_date . '"
                                  data-id="' . $receipt_value['id'] . '"
                                  ">';
                                ?>
                              </td>
                              <td><?php echo '<textarea style="width: 154px;height: 50px;" class="form-control  remarks-input" data-id="' . $receipt_value['id'] . '">' . $remarks . '</textarea>' ?></td>
                            </tr>
                          <?php  } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="project-overview-right">
                      <?php if (count($list_approve_status) > 0) { ?>

                        <div class="row">
                          <div class="col-md-12 project-overview-expenses-finance">
                            <div class="col-md-4 text-center">
                            </div>
                            <?php
                            $this->load->model('staff_model');
                            $enter_charge_code = 0;
                            foreach ($list_approve_status as $value) {
                              $value['staffid'] = explode(', ', $value['staffid']);
                              if ($value['action'] == 'sign') {
                            ?>
                                <div class="col-md-3 text-center">
                                  <p class="text-uppercase text-muted no-mtop bold">
                                    <?php
                                    $staff_name = '';
                                    $st = _l('status_0');
                                    $color = 'warning';
                                    foreach ($value['staffid'] as $key => $val) {
                                      if ($staff_name != '') {
                                        $staff_name .= ' or ';
                                      }
                                      if ($this->staff_model->get($val)) {
                                        $staff_name .= $this->staff_model->get($val)->full_name;
                                      }
                                    }
                                    echo html_entity_decode($staff_name);
                                    ?></p>
                                  <?php if ($value['approve'] == 1) {
                                  ?>
                                    <?php if (file_exists(WAREHOUSE_STOCK_IMPORT_MODULE_UPLOAD_FOLDER . $goods_receipt->id . '/signature_' . $value['id'] . '.png')) { ?>

                                      <img src="<?php echo site_url('modules/warehouse/uploads/stock_import/' . $goods_receipt->id . '/signature_' . $value['id'] . '.png'); ?>" class="img-width-height">

                                    <?php } else { ?>
                                      <img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
                                    <?php } ?>


                                  <?php }
                                  ?>
                                </div>
                              <?php } else { ?>
                                <div class="col-md-3 text-center">
                                  <p class="text-uppercase text-muted no-mtop bold">
                                    <?php
                                    $staff_name = '';
                                    foreach ($value['staffid'] as $key => $val) {
                                      if ($staff_name != '') {
                                        $staff_name .= ' or ';
                                      }
                                      if ($this->staff_model->get($val)) {
                                        $staff_name .= $this->staff_model->get($val)->full_name;
                                      }
                                    }
                                    echo html_entity_decode($staff_name);
                                    ?></p>
                                  <?php if ($value['approve'] == 1) {
                                  ?>
                                    <img src="<?php echo site_url('modules/warehouse/uploads/approval/approved.png'); ?>" class="img-width-height">
                                  <?php } elseif ($value['approve'] == -1) { ?>
                                    <img src="<?php echo site_url('modules/warehouse/uploads/approval/rejected.png'); ?>" class="img-width-height">
                                  <?php }
                                  ?>
                                  <p class="text-muted no-mtop bold">
                                    <?php echo html_entity_decode($value['note']) ?>
                                  </p>
                                </div>
                            <?php }
                            } ?>
                          </div>
                        </div>

                      <?php } ?>
                    </div>

                  </div>
                </div>

                <?php
                // if (empty($goods_receipt->goods_receipt_code)) { 
                ?>
                <div role="tabpanel" class="tab-pane" id="actual">
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table class="table items items-preview estimate-items-preview" data-type="estimate">
                        <thead class="purchase-head">
                          <tr>
                            <th align="center">#</th>
                            <th><?php echo _l('commodity_code') ?></th>
                            <th><?php echo _l('description') ?></th>
                            <th><?php echo _l('lead_time_days') ?></th>
                            <th><?php echo _l('advance_payment') ?></th>
                            <th><?php echo _l('shop_drawings_upload') ?></th>
                            <th><?php echo _l('shop_drawings_download') ?></th>
                            <th><?php echo _l('shop_drawings_submission') ?></th>
                            <th><?php echo _l('shop_drawings_approval') ?></th>
                            <th><?php echo _l('remarks') ?></th>
                          </tr>
                        </thead>
                        <tbody class="ui-sortable purchase-body">
                          <?php
                          foreach ($goods_receipt_detail as $receipt_key => $receipt_value) {
                            $receipt_key++;
                            $commodity_name = $receipt_value['commodity_name'];
                            $description = $receipt_value['description'];
                            if (strlen($commodity_name) == 0) {
                              $commodity_name = wh_get_item_variatiom($receipt_value['commodity_code']);
                            }
                            $lead_time_days = $receipt_value['lead_time_days'];
                            $advance_payment = $receipt_value['advance_payment'];
                            $shop_submission = $receipt_value['shop_submission'];
                            $shop_approval = $receipt_value['shop_approval'];
                            $actual_remarks = $receipt_value['actual_remarks'];
                          ?>
                            <tr>
                              <td><?php echo html_entity_decode($receipt_key) ?></td>
                              <td><?php echo html_entity_decode($commodity_name) ?></td>
                              <td><?php echo html_entity_decode($description) ?></td>
                              <td>
                                <div class="form-group">
                                  <input type="number" id="lead_time_days" name="lead_time_days" class="form-control" min="0" max="100" value="<?php echo $lead_time_days; ?>" data-id="<?php echo $receipt_value['id']; ?>">
                                </div>
                              </td>
                              <td>
                                <div class="form-group">
                                  <input type="number" id="advance_payment" name="advance_payment" class="form-control" min="0" max="100" value="<?php echo $advance_payment; ?>" data-id="<?php echo $receipt_value['id']; ?>">
                                </div>
                              </td>
                              <td>
                                <div class="input-group" style="width: 100%;">
                                  <input type="file"
                                    name="attachments[]"
                                    class="form-control upload_shop_drawings_files"
                                    data-id="<?php echo $receipt_value['id']; ?>"
                                    multiple
                                    style="min-width: 20px; width: 100%;">
                                  <span class="input-group-btn">
                                    <button type="button"
                                      class="btn btn-success upload_shop_drawings_attachments"
                                      data-id="<?php echo $receipt_value['id']; ?>"
                                      title="Upload Attachments">
                                      <i class="fa fa-upload"></i>
                                    </button>
                                  </span>
                                </div>
                              </td>
                              <td>
                                <?php
                                $this->load->model('warehouse/warehouse_model');
                                $attachments = $this->warehouse_model->get_inventory_shop_drawing_attachments('goods_receipt_shop_d', $receipt_value['id']);
                                $file_html = '';
                                if (!empty($attachments)) {
                                 $file_html = '<a href="javascript:void(0)" onclick="view_purchase_tracker_attachments(' . $receipt_value['id'] . '); return false;" class="btn btn-info btn-icon">View Files</a>';
                                }else{
                                  $file_html = '';
                                }
                              
                                echo $file_html;  

                                ?>
                              </td>
                              <td>
                                <input type="date" id="shop_submission" name="shop_submission" class="form-control" value="<?php echo $shop_submission; ?>" data-id="<?php echo $receipt_value['id']; ?>">
                              </td>
                              <td>
                                <input type="date" id="shop_approval" name="shop_approval" class="form-control" value="<?php echo $shop_approval; ?>" data-id="<?php echo $receipt_value['id']; ?>">
                              </td>
                              <td>
                                <textarea style="width: 154px;height: 50px;" class="form-control" name="actual_remarks" data-id="<?php echo $receipt_value['id']; ?>"><?php echo $actual_remarks; ?></textarea>
                              </td>
                            </tr>
                          <?php  } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <?php
                //  }
                ?>
              </div>

            </div>
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab_attachment">.
          <form id="attachmentForm" action="<?php echo admin_url('purchase/add_attachment_pur_tracker'); ?>" method="post" enctype="multipart/form-data">
            <div class="panel-body">

              <div class="col-md-12">

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
              </div>
              <div class="col-md-12" style="margin-top: 10px;">
                <button type="submit" class="btn-tr save_detail btn btn-info mleft10  pull-right">
                  <?php echo _l('submit'); ?>
                </button>
              </div>
            </div>
          </form>
        </div>

      </div>

      <div class="modal fade" id="add_action" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">

            <div class="modal-body">
              <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
              <div class="signature-pad--body">
                <canvas id="signature" height="130" width="550"></canvas>
              </div>
              <input type="text" class="sig-input-style" tabindex="-1" name="signature" id="signatureInput">
              <div class="dispay-block">
                <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
              <button onclick="sign_request(<?php echo html_entity_decode($goods_receipt->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
            </div>


          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="viewpurchaseorderAttachmentModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('attachment'); ?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <div class="view_purchase_attachment_modal">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="purchase_tracker_file_data"></div>
<?php require 'modules/warehouse/assets/js/view_purchase_js.php'; ?>
</body>

</html>
<script>
  var table_order_tracker = $('.table-items-preview').DataTable();

  // Inline editing for "Payment Date"
  $('body').on('change', '.payment-date-input', function(e) {
    e.preventDefault();

    var rowId = $(this).data('id');
    var paymentDate = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_payment_date', {
      id: rowId,
      payment_date: paymentDate,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  // Inline editing for "EST delivery Date"
  $('body').on('change', '.est-delivery-date-input', function(e) {
    e.preventDefault();

    var rowId = $(this).data('id');
    var estDeliveryDate = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_est_delivery_date', {
      id: rowId,
      est_delivery_date: estDeliveryDate,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
      } else {
        alert_float('danger', response.message);
      }
    });
  });


  // Inline editing for "Delivery Date"
  $('body').on('change', '.delivery-date-input', function(e) {
    e.preventDefault();

    var rowId = $(this).data('id');
    var DeliveryDate = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_delivery_date', {
      id: rowId,
      delivery_date: DeliveryDate,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  // Inline editing for "Remarks"
  $('body').on('change', '.remarks-input', function(e) {
    e.preventDefault();

    var rowId = $(this).data('id');
    var remarks = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_remarks', {
      id: rowId,
      remarks: remarks,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  function change_production_status(status, id) {
    "use strict";
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_production_status/' + status + '/' + id + '/' + purchase_tracker)
        .done(function(response) {
          try {
            response = JSON.parse(response);

            if (response.success) {
              var $statusSpan = $('#status_span_' + id);

              // Remove all status-related classes
              $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

              // Add the new class and update content
              if (response.class) {
                $statusSpan.addClass('label-' + response.class);
              }
              if (response.status_str) {
                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
              }

              // Display success message
              alert_float('success', response.mess);
            } else {
              // Display warning message if the operation fails
              alert_float('warning', response.mess);
            }
          } catch (e) {
            console.error('Error parsing server response:', e);
            alert_float('danger', 'Invalid server response');
          }
        })
        .fail(function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert_float('danger', 'Failed to update status');
        });
    }
  }

  function change_imp_local_status(status, id) {
    "use strict";
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_imp_local_status/' + status + '/' + id + '/' + purchase_tracker)
        .done(function(response) {
          try {
            response = JSON.parse(response);

            if (response.success) {
              var $statusSpan = $('#imp_status_span_' + id);

              // Remove all status-related classes
              $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

              // Add the new class and update content
              if (response.class) {
                $statusSpan.addClass('label-' + response.class);
              }
              if (response.status_str) {
                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
              }

              // Display success message
              alert_float('success', response.mess);
            } else {
              // Display warning message if the operation fails
              alert_float('warning', response.mess);
            }
          } catch (e) {
            console.error('Error parsing server response:', e);
            alert_float('danger', 'Invalid server response');
          }
        })
        .fail(function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert_float('danger', 'Failed to update status');
        });
    }
  }

  function change_tracker_status(status, id) {
    "use strict";
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_tracker_status/' + status + '/' + id + '/' + purchase_tracker)
        .done(function(response) {
          try {
            response = JSON.parse(response);

            if (response.success) {
              var $statusSpan = $('#tracker_status_span_' + id);

              // Remove all status-related classes
              $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

              // Add the new class and update content
              if (response.class) {
                $statusSpan.addClass('label-' + response.class);
              }
              if (response.status_str) {
                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
              }

              // Display success message
              alert_float('success', response.mess);
            } else {
              // Display warning message if the operation fails
              alert_float('warning', response.mess);
            }
          } catch (e) {
            console.error('Error parsing server response:', e);
            alert_float('danger', 'Invalid server response');
          }
        })
        .fail(function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert_float('danger', 'Failed to update status');
        });
    }
  }

  $('[data-toggle="tooltip"]').tooltip({
    html: true
  });

  $('body').on('change', 'input[name="lead_time_days"]', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var lead_time_days = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    $.post(admin_url + 'warehouse/update_lead_time_days', {
      id: rowId,
      lead_time_days: lead_time_days,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('change', 'input[name="advance_payment"]', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var advance_payment = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    $.post(admin_url + 'warehouse/update_advance_payment', {
      id: rowId,
      advance_payment: advance_payment,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('change', 'input[name="shop_submission"]', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var shop_submission = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    $.post(admin_url + 'warehouse/update_shop_submission', {
      id: rowId,
      shop_submission: shop_submission,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('change', 'input[name="shop_approval"]', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var shop_approval = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    $.post(admin_url + 'warehouse/update_shop_approval', {
      id: rowId,
      shop_approval: shop_approval,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('change', 'textarea[name="actual_remarks"]', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var actual_remarks = $(this).val();
    var purchase_tracker = <?php echo isset($purchase_tracker) ? json_encode($purchase_tracker) : 'false'; ?>;
    $.post(admin_url + 'warehouse/update_actual_remarks', {
      id: rowId,
      actual_remarks: actual_remarks,
      purchase_tracker: purchase_tracker
    }).done(function(response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
      } else {
        alert_float('danger', response.message);
      }
    });
  });
</script>
<script>
  // Toggle settings dropdown visibility
  document.getElementById('settings-toggle').addEventListener('click', function() {
    const dropdown = document.getElementById('settings-dropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  });

  // Add event listener to toggle column visibility
  document.querySelectorAll('.column-toggle').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      const columnIndex = this.getAttribute('data-column');
      const table = document.querySelector('.items-preview');

      // Iterate through all rows and toggle column visibility
      table.querySelectorAll('tr').forEach(function(row) {
        const cells = row.querySelectorAll('th, td');
        if (cells[columnIndex]) {
          cells[columnIndex].style.display = checkbox.checked ? '' : 'none';
        }
      });
    });
  });
  $(document).ready(function() {
    $('#attachmentForm').submit(function(e) {
      e.preventDefault(); // Prevent default form submission

      var formData = new FormData(this); // Create FormData object
      $.ajax({
        url: $(this).attr('action'), // Get form action URL
        type: 'POST',
        data: formData,
        contentType: false, // Prevent jQuery from setting content type
        processData: false, // Prevent jQuery from processing data
        beforeSend: function() {
          $('.save_detail').prop('disabled', true).text('Submitting...'); // Disable button & show loading
        },
        success: function(response) {
          response = JSON.parse(response);
          if (response.success) {
            alert_float('success', 'Attachment uploaded successfully!');
            $('#attachmentForm')[0].reset(); // Reset form
          } else {
            alert_float('danger', 'Error uploading attachment: ' + response.message);
          }
        },
        error: function() {
          alert_float('danger', 'Something went wrong, please try again.');
        },
        complete: function() {
          $('.save_detail').prop('disabled', false).text('Submit'); // Re-enable button
        }
      });
    });
  });
  $(document).off('click', '.upload_shop_drawings_attachments')
    .on('click', '.upload_shop_drawings_attachments', function(e) {
      e.preventDefault();

      // *** Log once, at handler entry ***
      console.log('upload clicked');

      var rowId = $(this).data('id');
      var input = $(this).closest('.input-group').find('.upload_shop_drawings_files')[0];

      if (!input.files.length) {
        alert_float('warning', "Please select at least one file to upload.");
        return;
      }

      var formData = new FormData();
      // now loop only to append, no logging inside here
      for (var i = 0; i < input.files.length; i++) {
        formData.append('attachments[]', input.files[i]);
      }
      formData.append('id', rowId);
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());

      $.ajax({
        url: admin_url + 'warehouse/upload_purchase_tracker_attachments',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
      }).done(function(response) {
        var res = JSON.parse(response);
        if (res.status) {
          alert_float('success', "Attachments are uploaded successfully.");
        } else {
          alert_float('warning', "Upload failed.");
        }
      }).fail(function() {
        alert_float('warning', "Upload failed.");
      });
    });
</script>