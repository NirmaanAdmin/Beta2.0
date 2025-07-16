<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="https://cdn.jsdelivr.net/npm/handsontable@7.2.2/dist/handsontable.full.min.js"></script>
<link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@7.2.2/dist/handsontable.full.min.css">

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12" id="small-table">
        <div class="panel_s">
          <div class="panel-body">

            <div class="row">
              <div class="col-md-12">
                <h4 class="no-margin font-bold h4-color"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                <hr class="hr-color">
              </div>
            </div>
            <div class="row row-margin">

              <div class="col-md-9">
                <div class="panel panel-info col-md-12  panel-padding">

                  <div class="panel-body">
                    <div class="col-md-10">
                      <?php $company_name = get_option('invoice_company_name');
                      $address = get_option('invoice_company_address');
                      $day = date('d', strtotime($goods_delivery->date_add));
                      $month = date('m', strtotime($goods_delivery->date_add));
                      $year = date('Y', strtotime($goods_delivery->date_add)); ?>

                    </div>

                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4">
                      <p>
                      <h2 class="bold text-center"><?php echo mb_strtoupper(_l('reconciliation_output_slip')); ?></h2>
                      </p>
                    </div>
                    <div class="col-md-4">
                    </div>

                    <div class="col-md-3">
                    </div>

                    <div class="col-md-12 pull-right">
                      <br>
                      <div class="row">
                        <div class="col-md-3 pull-right">
                        </div>
                        <div class="col-md-4 pull-right">
                          <p><span class="span-font-style"><?php echo _l('days') . ' ' . $day . ' ' . _l('month') . ' ' . $month . ' ' . _l('year') . ' ' . $year; ?></p>
                          <p><span class="bold"><?php echo _l('goods_reconciliation_code'); ?>: </span><?php echo html_entity_decode($goods_delivery->goods_delivery_code) ?></p>
                        </div>
                      </div>
                    </div>

                    <table class="table">
                      <tbody>
                        <?php
                        $customer_name = '';
                        if ($goods_delivery) {

                          if (is_numeric($goods_delivery->customer_code)) {
                            $customer_value = $this->clients_model->get($goods_delivery->customer_code);
                            if ($customer_value) {
                              $customer_name .= $customer_value->company;
                            }
                          }
                        }
                        ?>


                        <?php
                        if (($goods_delivery->invoice_id != '') && ($goods_delivery->invoice_id != 0)) { ?>

                          <tr class="project-overview">
                            <td class="bold"><?php echo _l('invoices'); ?></td>
                            <td>
                              <a href="<?php echo admin_url('invoices#' . $goods_delivery->invoice_id) ?>"><?php echo format_invoice_number($goods_delivery->invoice_id) ?></a>

                            </td>
                          </tr>

                        <?php   }
                        ?>

                        <?php
                        if (get_status_modules_wh('purchase')) {
                          if (($goods_delivery->pr_order_id != '') && ($goods_delivery->pr_order_id != 0)) { ?>

                            <tr class="project-overview">
                              <td class="bold"><?php echo _l('reference_purchase_order'); ?></td>
                              <td>
                                <a href="<?php echo admin_url('purchase/purchase_order/' . $goods_delivery->pr_order_id) ?>"><?php echo get_pur_order_name($goods_delivery->pr_order_id) ?></a>

                              </td>
                            </tr>

                        <?php   }
                        }
                        ?>

                        <tr>
                          <td class="bold"><?php echo _l('print'); ?></td>
                          <td>
                            <div class="btn-group">
                              <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if (is_mobile()) {
                                                                                                                                                                                    echo ' PDF';
                                                                                                                                                                                  } ?> <span class="caret"></span></a>
                              <ul class="dropdown-menu dropdown-menu-right">
                                <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_reconcile_export_pdf/' . $goods_delivery->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                                <li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_reconcile_export_pdf/' . $goods_delivery->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                <li><a href="<?php echo admin_url('warehouse/stock_reconcile_export_pdf/' . $goods_delivery->id); ?>"><?php echo _l('download'); ?></a></li>
                                <li>
                                  <a href="<?php echo admin_url('warehouse/stock_reconcile_export_pdf/' . $goods_delivery->id . '?print=true'); ?>" target="_blank">
                                    <?php echo _l('print'); ?>
                                  </a>
                                </li>
                              </ul>
                            </div>

                          </td>
                        </tr>




                      </tbody>
                    </table>

                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <tbody>
                          <tr>
                            <th align="center">#</th>
                            <th colspan="1"><?php echo _l('commodity_code') ?></th>
                            <th colspan="1"><?php echo _l('item_description') ?></th>
                            <th colspan="1"><?php echo _l('area') ?></th>
                            <th colspan="1"><?php echo _l('warehouse_name') ?></th>
                            <th colspan="1"><?php echo _l('Issued Quantity') ?></th>
                            <th colspan="1"><?php echo _l('Return Date') ?></th>
                            <th colspan="1" c><?php echo _l('Reconciliation Date') ?></th>
                            <th colspan="1"><?php echo _l('Return Quantity') ?></th>
                            <th colspan="1"><?php echo _l('Used Quantity') ?></th>
                            <th colspan="1"><?php echo _l('Location') ?></th>

                          </tr>

                          <?php $subtotal = 0;
                          foreach ($goods_delivery_detail as $delivery => $delivery_value) {
                            $delivery++;
                            $available_quantity = (isset($delivery_value) ? $delivery_value['available_quantity'] : '');
                            $total_money = (isset($delivery_value) ? $delivery_value['total_money'] : '');
                            $discount = (isset($delivery_value) ? $delivery_value['discount'] : '');
                            $discount_money = (isset($delivery_value) ? $delivery_value['discount_money'] : '');
                            $guarantee_period = (isset($delivery_value) ? _d($delivery_value['guarantee_period']) : '');

                            $quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
                            $unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');
                            $total_after_discount = (isset($delivery_value) ? $delivery_value['total_after_discount'] : '');

                            $commodity_code = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';
                            $commodity_name = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->description : '';
                            $subtotal += (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
                            $item_subtotal = (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];



                            $warehouse_name = '';

                            if (isset($delivery_value['warehouse_id']) && ($delivery_value['warehouse_id'] != '')) {
                              $arr_warehouse = explode(',', $delivery_value['warehouse_id']);

                              $str = '';
                              if (count($arr_warehouse) > 0) {

                                foreach ($arr_warehouse as $wh_key => $warehouseid) {
                                  $str = '';
                                  if ($warehouseid != '' && $warehouseid != '0') {

                                    $team = get_warehouse_name($warehouseid);
                                    if ($team) {
                                      $value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

                                      $str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide">, </span></span>&nbsp';

                                      $warehouse_name .= $str;
                                      if ($wh_key % 3 == 0) {
                                        $warehouse_name .= '<br/>';
                                      }
                                    }
                                  }
                                }
                              } else {
                                $warehouse_name = '';
                              }
                            }



                            $unit_name = '';
                            if (is_numeric($delivery_value['unit_id'])) {
                              $unit_name = get_unit_type($delivery_value['unit_id']) != null ? get_unit_type($delivery_value['unit_id'])->unit_name : '';
                            }

                            $lot_number = '';
                            if (($delivery_value['lot_number'] != null) && ($delivery_value['lot_number'] != '')) {
                              $array_lot_number = explode(',', $delivery_value['lot_number']);
                              foreach ($array_lot_number as $key => $lot_value) {

                                if ($key % 2 == 0) {
                                  $lot_number .= $lot_value;
                                } else {
                                  $lot_number .= ' : ' . $lot_value . ' ';
                                }
                              }
                            }

                            $commodity_name = $delivery_value['commodity_name'];
                            if (strlen($commodity_name) == 0) {
                              $commodity_name = wh_get_item_variatiom($delivery_value['commodity_code']);
                            }


                            $all_issued_quantities = '';
                            if (!empty($delivery_value['issued_quantities'])) {
                              $issued_quantities_json = json_decode($delivery_value['issued_quantities'], true);

                              foreach ($issued_quantities_json as $key => $value) {
                                $all_issued_quantities .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                              }
                              $all_issued_quantities = rtrim($all_issued_quantities, ',</br>');
                            }

                            $all_returnable_date = '';
                            if (!empty($delivery_value['returnable_date'])) {
                              $returnable_date_json = json_decode($delivery_value['returnable_date'], true);

                              foreach ($returnable_date_json as $key => $value) {
                                $all_returnable_date .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                              }
                              $all_returnable_date = rtrim($all_returnable_date, ',</br>');
                            }

                            $all_reconciliation_date = '';
                            if (!empty($delivery_value['reconciliation_date'])) {
                              $reconciliation_date_json = json_decode($delivery_value['reconciliation_date'], true);

                              foreach ($reconciliation_date_json as $key => $value) {
                                $all_reconciliation_date .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                              }
                              $all_reconciliation_date = rtrim($all_reconciliation_date, ',</br>');
                            }

                            $all_return_quantity = '';
                            if (!empty($delivery_value['return_quantity'])) {
                              $return_quantity_json = json_decode($delivery_value['return_quantity'], true);

                              foreach ($return_quantity_json as $key => $value) {
                                $all_return_quantity .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                              }
                              $all_return_quantity = rtrim($all_return_quantity, ',</br>');
                            }

                            $all_used_quantity = '';
                            if (!empty($delivery_value['used_quantity'])) {
                              $used_quantity_json = json_decode($delivery_value['used_quantity'], true);

                              foreach ($used_quantity_json as $key => $value) {
                                $all_used_quantity .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                              }
                              $all_used_quantity = rtrim($all_used_quantity, ',</br>');
                            }

                            $all_location = '';
                            if (!empty($delivery_value['location'])) {
                              $location_json = json_decode($delivery_value['location'], true);

                              foreach ($location_json as $key => $value) {
                                $all_location .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                              }
                              $all_location = rtrim($all_location, ',</br>');
                            }

                          ?>

                            <tr>
                              <td><?php echo html_entity_decode($delivery) ?></td>
                              <td><?php echo html_entity_decode($commodity_name) ?></td>
                              <td><?php echo html_entity_decode($delivery_value['description']) ?></td>
                              <td><?php echo get_area_name_by_id($delivery_value['area']); ?></td>
                              <td><?php echo html_entity_decode($warehouse_name) ?></td>
                              <td><?php echo html_entity_decode($all_issued_quantities) ?></td>
                              <td><?php echo html_entity_decode($all_returnable_date) ?></td>
                              <td><?php echo html_entity_decode($all_reconciliation_date) ?></td>
                              <td><?php echo html_entity_decode($all_return_quantity) ?></td>
                              <td><?php echo html_entity_decode($all_used_quantity) ?></td>
                              <td><?php echo html_entity_decode($all_location) ?></td>
                            </tr>
                          <?php  } ?>
                        </tbody>
                      </table>



                    </div>





                    <br>


                    <div class="row">
                      <div class="col-md-12 ">
                        <div class="col-md-4 pull-right">
                          <p><span class="span-font-style"><?php echo _l('days') . ' ......... ' . _l('month') . ' ......... ' . _l('year') . ' .......... '; ?></p>
                        </div>
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-1">
                      </div>
                      <div class="col-md-4">
                        <p><span class="bold"><?php echo _l('receiver') ?></p>
                        <p><span class="span-font-style"><?php echo _l('sign_full_name') ?></p>
                      </div>
                      <div class="col-md-4">
                        <p><span class="bold"><?php echo _l('stocker') ?></p>
                        <p><span class="span-font-style"><?php echo _l('sign_full_name') ?></p>

                      </div>
                      <div class="col-md-3">
                        <p><span class="bold"><?php echo _l('chief_accountant') ?></p>
                        <p><span class="span-font-style"><?php echo _l('sign_full_name') ?></p>

                      </div>
                    </div>

                    <br>
                    <br>
                    <br>
                    <br>


                    <div class="project-overview-right">
                      <?php if (count($list_approve_status) > 0) { ?>

                        <div class="row">
                          <div class="col-md-12 project-overview-expenses-finance">
                            <div class="col-md-4 text-sm-center ">
                            </div>
                            <?php
                            $this->load->model('staff_model');
                            $enter_charge_code = 0;
                            foreach ($list_approve_status as $value) {
                              $value['staffid'] = explode(', ', $value['staffid']);
                              if ($value['action'] == 'sign') {
                            ?>
                                <div class="col-md-3 text-sm-center">
                                  <p class="text-uppercase text-muted no-mtop bold">
                                    <?php
                                    $staff_name = '';
                                    $st = _l('not_yet_approve');
                                    $color = 'warning';
                                    foreach ($value['staffid'] as $key => $val) {
                                      if ($staff_name != '') {
                                        $staff_name .= ' or ';
                                      }
                                      $staff_name .= $this->staff_model->get($val)->firstname;
                                    }
                                    echo html_entity_decode($staff_name);
                                    ?></p>
                                  <?php if ($value['approve'] == 1) {
                                  ?>

                                    <?php if (file_exists(WAREHOUSE_STOCK_EXPORT_MODULE_UPLOAD_FOLDER . $goods_delivery->id . '/signature_' . $value['id'] . '.png')) { ?>

                                      <img src="<?php echo site_url('modules/warehouse/uploads/stock_export/' . $goods_delivery->id . '/signature_' . $value['id'] . '.png'); ?>" class="img-width-height">

                                    <?php } else { ?>
                                      <img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
                                    <?php } ?>



                                  <?php }
                                  ?>
                                </div>
                              <?php } else { ?>
                                <div class="col-md-3 text-sm-center ">
                                  <p class="text-uppercase text-muted no-mtop bold">
                                    <?php
                                    $staff_name = '';
                                    foreach ($value['staffid'] as $key => $val) {
                                      if ($staff_name != '') {
                                        $staff_name .= ' or ';
                                      }
                                      $staff_name .= $this->staff_model->get($val)->firstname;
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
                    <div class="pull-right">

                      <?php
                      if ($goods_delivery->approval != 1 && ($check_approve_status == false)) { ?>
                        <?php if ($check_appr && $check_appr != false) { ?>
                          <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo html_entity_decode($goods_delivery->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
                        <?php } ?>

                      <?php }
                      if (isset($check_approve_status['staffid'])) {
                      ?>
                        <?php
                        if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)) { ?>
                          <div class="btn-group">
                            <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right menu-with-heght">
                              <li>
                                <div class="col-md-12">
                                  <?php echo render_textarea('reason', 'reason'); ?>
                                </div>
                              </li>
                              <li>
                                <div class="row text-right col-md-12">
                                  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo html_entity_decode($goods_delivery->id); ?>); return false;" class="btn btn-success button-margin"><?php echo _l('approve'); ?></a>
                                  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo html_entity_decode($goods_delivery->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
                                </div>
                              </li>
                            </ul>
                          </div>
                        <?php }
                        ?>

                        <?php
                        if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign)) { ?>
                          <button onclick="accept_action();" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
                        <?php }
                        ?>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>
                <?php $value = (isset($payslip->record) ? $payslip->record : []) ?>
                <?php $record = $value; ?>
                <?php $value = (isset($payslip->spending) ? $payslip->spending : []) ?>
                <?php $spending = $value; ?>


              </div>


              <div class="col-md-3">
                <div class="panel panel-info col-md-12 panel-padding">
                  <div class="panel-heading "><?php echo _l('approval_information'); ?></div>
                  <div class="panel-body">
                    <h5 class="no-margin">
                      <?php echo _l('approver_list'); ?>:
                    </h5>
                    <?php
                    $stt = 1;
                    foreach ($list_approve_status as $key => $value) {
                      $value['staffid'] = explode(', ', $value['staffid']);
                      $approve = '<span class="label label-tag tag-id-1 label-tab2" >' . _l('not_yet_approve') . '</span>';
                      if ($value['approve'] == 1) {
                        $approve = '<span class="label label-tag tag-id-1 label-tab1" >' . _l('approved') . '</span>';
                      } elseif ($value['approve'] == -1) {
                        $approve = '<span class="label label-tag tag-id-1 label-tab3" >' . _l('reject') . '</span>';
                      }
                      $staff_name = '';
                      foreach ($value['staffid'] as $key => $val) {
                        if ($staff_name != '') {
                          $staff_name .= ' or ';
                        }
                        $staff_name .= get_staff_full_name($val);
                      }
                      echo html_entity_decode($stt . ': ' . $staff_name . ' ' . $approve) . '<br>';
                      $stt++;
                    }
                    ?>

                    <hr class="hr-panel-heading" />
                    <h5 class="no-margin">
                      <?php echo _l('activity_log'); ?>
                    </h5>
                    <div class="activity-feed">
                      <?php $enter_code = 0;
                      foreach ($payslip_log as $log) {
                        $approve = '';



                      ?>
                        <div class="feed-item">
                          <div class="row">
                            <div class="col-md-12">
                              <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>"><?php echo time_ago($log['date']); ?></span></div>
                              <div class="text">
                                <?php

                                $fullname = get_staff_full_name($log['staffid']);
                                if ($log['staffid'] != 0) { ?>
                                  <a href="<?php echo admin_url('profile/' . $log['staffid']); ?>"><?php echo staff_profile_image($log['staffid'], array('staff-profile-xs-image', 'pull-left mright10')); ?></a>
                                <?php } ?>

                                <p class="mtop10 no-mbot"><?php echo html_entity_decode($fullname) . ' - <b>' .
                                                            _l($log['note']) . '</b>'; ?></p>

                              </div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="col-md-12">
                              <hr class="hr-10" />
                            </div>
                          </div>
                        </div>
                      <?php

                      } ?>
                    </div>
                  </div>
                </div>
              </div>


            </div>


            <div class="col-md-12 hide">

              <h5 class="no-margin font-bold h4-color"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('stock_export_detail'); ?></h5>
              <hr class="hr-color">

              <div class="panel-body ">
                <div class="horizontal-scrollable-tabs preview-tabs-top">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                  <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                      <li role="presentation" class="active">
                        <a href="#commodity" aria-controls="commodity" role="tab" data-toggle="tab" aria-controls="commodity" id="ac_commodity">
                          <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('commodity'); ?>
                        </a>
                      </li>

                    </ul>
                  </div>
                </div>

                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="commodity">
                    <div class="form">
                      <div id="hot_purchase" class="hot handsontable htColumnHeaders">

                      </div>
                      <?php echo form_hidden('hot_purchase'); ?>
                    </div>

                  </div>

                </div>

                <div class="row">
                  <div class="col-md-3 pull-right panel-padding">
                    <table class="table border table-striped table-margintop">
                      <tbody>

                        <tr class="project-overview">
                          <?php $total_money = isset($goods_delivery) ?  $goods_delivery->total_money : 0; ?>
                          <td><?php echo render_input('total_money', 'subtotal', $total_money, '', array('disabled' => 'true')) ?>


                          </td>

                        </tr>
                        <tr class="project-overview">
                          <?php $total_discount = isset($goods_delivery) ?  $goods_delivery->total_discount : 0; ?>
                          <td><?php echo render_input('total_discount', 'total_discount', $total_discount, '', array('disabled' => 'true')) ?>

                          </td>

                        </tr>
                        <tr class="project-overview">
                          <?php
                          $after_discount = isset($goods_delivery) ?  $goods_delivery->after_discount : 0;
                          if ($goods_delivery->after_discount == null) {
                            $after_discount = $goods_delivery->total_money;
                          }
                          ?>
                          <td><?php echo render_input('after_discount', 'total_money', $after_discount, '', array('disabled' => 'true')) ?>


                          </td>

                        </tr>

                      </tbody>
                    </table>
                  </div>
                </div>


              </div>

            </div>

            <hr>
            <div class="modal-footer">
              <a href="<?php echo admin_url('warehouse/manage_delivery'); ?>" class="btn btn-default pull-right mright10 display-block close_button"><?php echo _l('close'); ?></a>
            </div>


          </div>

        </div>

      </div>

    </div>
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
        <button onclick="sign_request(<?php echo html_entity_decode($goods_delivery->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
      </div>
    </div>
  </div>
</div>

</div>
</div>
</div>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/edit_reconciliation_js.php'; ?>
</body>

</html>