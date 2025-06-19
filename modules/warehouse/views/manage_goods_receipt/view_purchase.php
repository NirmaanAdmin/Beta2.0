<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id', $goods_receipt->id); ?>
<?php echo form_hidden('_attachment_sale_type', 'estimate'); ?>
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
                <?php echo _l('stock_import'); ?>
              </a>
            </li>

            <li role="presentation">
              <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo html_entity_decode($goods_receipt->id); ?>,'stock_import'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                <?php echo _l('tasks'); ?>
              </a>
            </li>

            <li role="presentation" class="tab-separator">
              <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                <?php echo _l('Documentation'); ?>
              </a>
            </li>
            <li role="presentation">
              <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                <?php echo _l('invoice_view_activity_tooltip'); ?>
              </a>
            </li>
            <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
              <a href="#" onclick="small_table_full_view(); return false;">
                <i class="fa fa-expand"></i></a>
            </li>

          </ul>
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
          <div class="row">
            <div class="col-md-4">

            </div>
            <div class="col-md-8">
              <div class="pull-right _buttons">
                <?php if (has_permission('warehouse', '', 'edit')) { ?>
                  <a href="<?php echo admin_url('warehouse/edit_purchase/' . $goods_receipt->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('view'); ?>" data-placement="bottom"><i class="fa fa-eye"></i></a>
                <?php } ?>

              </div>

            </div>
          </div>

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
                    <td class="bold" width="30%"><?php echo _l('deliver_name'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->deliver_name); ?></td>
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
                    <td class="bold"><?php echo _l('note_'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->description); ?></td>
                  </tr>
                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('category'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->kind); ?></td>
                  </tr>

                  <?php
                  if (get_status_modules_wh('purchase')) { ?>
                    <tr class="project-overview">
                      <td class="bold"><?php echo _l('reference_order'); ?></td>
                      <td>
                        <?php 
                        if(!empty($goods_receipt->pr_order_id)) { ?>
                          <a href="<?php echo admin_url('purchase/purchase_order/' . $goods_receipt->pr_order_id) ?>"><?php echo get_pur_order_name($goods_receipt->pr_order_id) ?></a>
                        <?php } if(!empty($goods_receipt->wo_order_id)) { ?>
                          <a href="<?php echo admin_url('purchase/work_order/' . $goods_receipt->wo_order_id) ?>"><?php echo get_wo_order_name($goods_receipt->wo_order_id) ?></a>
                        <?php } ?>

                      </td>
                    </tr>
                  <?php } ?>
                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('Print Goods Receipt Note'); ?></td>
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
                  </tr>
                  <tr class="project-overview">
                    <td class="bold"><?php echo _l('Print QR Codes'); ?></td>
                    <?php
                    $vendor = html_entity_decode(wh_get_vendor_company_name($goods_receipt->supplier_code));
                    $pur_order_name = get_pur_order_name($goods_receipt->pr_order_id);
                    $get_project_id = get_pur_order_project_id($goods_receipt->pr_order_id);
                    $project_name = get_project($get_project_id);


                    ?>
                    <input type="hidden" id="vendor_name" value="<?= htmlspecialchars($vendor, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" id="pur_order_name" value="<?= htmlspecialchars($pur_order_name, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" id="project_name" value="<?= htmlspecialchars($project_name->name, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" id="purchase_id" value="<?= htmlspecialchars(json_encode($goods_receipt->id), ENT_QUOTES, 'UTF-8'); ?>">
                    <td>
                      <div class="btn-group">
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" onclick="print_qrcodes()" aria-haspopup="true" aria-expanded="false"><i class="fa fa-qrcode"></i></a>

                      </div>

                    </td>
                  </tr>
                  </tr>

                </tbody>
              </table>
            </div>
            <?php
            $commodity_code_ids = implode(',', array_column($goods_receipt_detail, 'commodity_code'));
            $commodity_descriptions = array_column($goods_receipt_detail, 'description');

            ?>
            <input type="hidden" id="commodity_code_ids" value="<?= htmlspecialchars($commodity_code_ids, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" id="commodity_descriptions" value="<?= htmlspecialchars(json_encode($commodity_descriptions), ENT_QUOTES, 'UTF-8'); ?>">

            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table items items-preview estimate-items-preview" data-type="estimate">
                    <thead>
                      <tr>
                        <th align="center">#</th>
                        <th colspan="1"><?php echo _l('commodity_code') ?></th>
                        <th colspan="1"><?php echo _l('description') ?></th>
                        <th colspan="1"><?php echo _l('area') ?></th>
                        <th colspan="1"><?php echo _l('warehouse_name') ?></th>
                        <th colspan="1"><?php echo _l('unit_name') ?></th>
                        <th colspan="2" class="text-center"><?php echo _l('po_quantity') ?></th>
                        <th colspan="2" class="text-center"><?php echo _l('received_quantity') ?></th>
                        <th colspan="2" class="text-center"><?php echo _l('remaining_quantity') ?></th>
                        <th align="right" colspan="1"><?php echo _l('lot_number') ?></th>
                      </tr>
                    </thead>
                    <tbody class="ui-sortable">

                      <?php
                      foreach ($goods_receipt_detail as $receipt_key => $receipt_value) {

                        $receipt_key++;
                        $po_quantities = (isset($receipt_value) ? $receipt_value['po_quantities'] : 0);
                        $quantities = (isset($receipt_value) ? $receipt_value['quantities'] : 0);
                        $remaining_quantities = app_format_number($po_quantities - $quantities, true);
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
                      ?>

                        <tr data-toggle="tooltip" data-original-title="<?php echo html_entity_decode($name_serial_number_tooltip); ?>">
                          <td><?php echo html_entity_decode($receipt_key) ?></td>
                          <td><?php echo html_entity_decode($commodity_name) ?></td>
                          <td><?php echo html_entity_decode($description) ?></td>
                          <td><?php echo get_area_name_by_id($receipt_value['area']); ?></td>
                          <td><?php echo html_entity_decode($warehouse_code) ?></td>
                          <td><?php echo html_entity_decode($unit_name) ?></td>
                          <td></td>
                          <td class="text-right"><?php echo html_entity_decode($po_quantities) ?></td>
                          <td></td>
                          <td class="text-right"><?php echo html_entity_decode($quantities) ?></td>
                          <td></td>
                          <td class="text-right"><?php echo html_entity_decode($remaining_quantities) ?></td>
                          <td class="text-right"><?php echo html_entity_decode($lot_number) ?></td>
                        </tr>
                      <?php  } ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- <div class="col-md-6 col-md-offset-6">
                      <table class="table text-right table-margintop">
                        <tbody>
                          <tr class="project-overview" id="subtotal">
                            <td class="td_style"><span class="bold"><?php echo _l('total_goods_money'); ?></span>
                            </td>
                            <?php $total_goods_money = (isset($goods_receipt) ? $goods_receipt->total_goods_money : ''); ?>
                            <td><?php echo app_format_money((float)$total_goods_money, $base_currency); ?></td>
                          </tr>

                          <tr class="project-overview">
                            <td class="td_style"><span class="bold"><?php echo _l('value_of_inventory'); ?></span>
                            </td>
                            <?php $value_of_inventory = (isset($goods_receipt) ? $goods_receipt->value_of_inventory : ''); ?>
                            <td><?php echo app_format_money((float)$value_of_inventory, $base_currency); ?></td>
                          </tr>
                          
                          <?php if (isset($goods_receipt) && $tax_data['html_currency'] != '') {
                            echo html_entity_decode($tax_data['html_currency']);
                          } ?>
                          
                          <tr class="project-overview">
                            <td class="td_style"><span class="bold"><?php echo _l('total_tax_money'); ?></span>
                            </td>
                            <?php $total_tax_money = (isset($goods_receipt) ? $goods_receipt->total_tax_money : ''); ?>
                            <td><?php echo app_format_money((float)$total_tax_money, $base_currency); ?></td>
                          </tr>

                          <tr class="project-overview">
                            <td class="td_style"><span class="bold"><?php echo _l('total_money'); ?></span>
                            </td>
                            <?php $total_money = (isset($goods_receipt) ? $goods_receipt->total_money : ''); ?>
                            <td><?php echo app_format_money((float)$total_money, $base_currency); ?></td>

                          </tr>
                        </tbody>
                      </table>
                    </div> -->


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

                <div class="pull-right">

                  <?php
                  if ($goods_receipt->approval != 1 && ($check_approve_status == false)) { ?>
                    <?php if ($check_appr && $check_appr != false) { ?>

                      <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo html_entity_decode($goods_receipt->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
                    <?php } ?>

                  <?php }
                  if (isset($check_approve_status['staffid'])) {
                  ?>
                    <?php
                    if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)) { ?>
                      <div class="btn-group">
                        <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right menu-width-height">
                          <li>
                            <div class="col-md-12">
                              <?php echo render_textarea('reason', 'reason'); ?>
                            </div>
                          </li>
                          <li>
                            <div class="row text-right col-md-12">
                              <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo html_entity_decode($goods_receipt->id); ?>); return false;" class="btn btn-success button-margin"><?php echo _l('approve'); ?></a>
                              <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo html_entity_decode($goods_receipt->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
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
          </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="tab_tasks">
          <?php init_relation_tasks_table(array('data-new-rel-id' => $goods_receipt->id, 'data-new-rel-type' => 'stock_import')); ?>
        </div>

        <div role="tabpanel" class="tab-pane" id="attachment">
          <div class="col-md-12">
            <div class="table-responsive">
              <?php echo form_open_multipart(admin_url('warehouse/goods_receipt_documentetion/' . $goods_receipt->id), array('id' => 'partograph-attachments-upload'));
              ?>
              <table class="table items items-preview estimate-items-preview" data-type="estimate">
                <thead>
                  <tr>
                    <th colspan="1" style="width: 7%;">Sr. No</th>
                    <th colspan="1"><?php echo _l('Checklist') ?></th>
                    <th colspan="1"><?php echo _l('Required') ?></th>
                    <th colspan="1"><?php echo _l('Attachments') ?></th>
                    <th colspan="1"><?php echo _l('Download') ?></th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  // Define the checklist items
                  $checklist_items = [
                    '1' => 'Stock Import Images',
                    '2' => 'Technical/Security Staff sign',
                    '3' => 'Transport Document',
                    '4' => 'Production Certificate',
                  ];

                  // Initialize serial number
                  $sr = 1;

                  // Loop through each checklist item
                  foreach ($checklist_items as $key => $value) {
                    // Find the corresponding entry in $goods_documentations
                    $is_required = 1; // Default to required
                    $is_attachemnt = $file_id =  0;
                    if (!empty($goods_documentitions)) {
                      foreach ($goods_documentitions as $doc) {
                        if ($doc->checklist_id == $key) {
                          $is_required = $doc->required;
                          $is_attachemnt = $doc->attachments;
                          $file_id = $doc->id;
                          $rel_id = $doc->goods_receipt_id;
                        }
                      }
                    }
                  ?>
                    <input type="hidden" name="checklist_id[<?= $key ?>]" value="<?= $key ?>">

                    <tr>
                      <td><?= $sr ?></td>

                      <td><?= $value ?></td>
                      <td style="text-align: center;">
                        <div class="checkbox">
                          <input type="checkbox" name="required[<?= $key ?>]"
                            <?= $is_required ? 'checked="checked"' : '' ?> style="opacity: unset;">
                        </div>
                      </td>


                      <td>
                        <div class="attachment_new">
                          <div class="col-md-12">
                            <div class="form-group">
                              <div class="input-group">
                                <input type="file"
                                  extension="<?php echo str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')); ?>"
                                  filesize="<?php echo file_upload_max_size(); ?>"
                                  class="form-control" name="items[<?= $sr ?>][attachments_new][<?= $sr ?>]"
                                  accept="<?php echo get_form_form_accepted_mimes(); ?>">
                                <span class="input-group-btn">
                                  <button class="btn btn-default add_more_attachments_goods" data-item="<?= $sr ?>"
                                    data-max="<?php echo get_option('maximum_allowed_form_attachments'); ?>"
                                    type="button"><i class="fa fa-plus"></i></button>
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <?php if ($is_attachemnt == 1) : ?>
                          <a href="javascript:void(0)" onclick="view_goods_receipt_attachments('<?= $file_id ?>','<?= $rel_id ?>','goods_receipt_checkl'); return false;" class="btn btn-info btn-icon">View Files</a>
                        <?php endif; ?>
                       
                      </td>

                    </tr>
                  <?php
                    // Increment serial number
                    $sr++;
                  }

                  ?>
                </tbody>
              </table>
              <div class="col-md-12">
                <button id="obgy_btn2" type="submit" class="btn btn-info pull-right"><?php echo _l('update'); ?></button>
              </div>
              <?php echo form_close(); ?>
            </div>
            <?php
            if (isset($attachments) && count($attachments) > 0) {
              foreach ($attachments as $value) {
                echo '<div class="col-md-6" style="padding-bottom: 10px">';
                $path = get_upload_path_by_type('inventory') . 'goods_receipt/' . $value['rel_id'] . '/' . $value['file_name'];
                $is_image = is_image($path);
                if ($is_image) {
                  echo '<div class="preview_image">';
                } ?>
                <a href="<?php echo site_url('download/file/inventory/' . $value['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-inventory-<?php echo $value['rel_id']; ?>" <?php } ?>>
                  <i class="<?php echo get_mime_class($value['filetype']); ?>"></i> <?php echo $value['file_name']; ?>
                  <?php if ($is_image) { ?>
                    <img class="mtop5" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']); ?>" style="height: 165px;">
                  <?php } ?>
                </a>
                <?php if ($is_image) {
                  echo '</div>';
                } ?>
            <?php echo '</div>';
              }
            } ?>
          </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
          <div class="row">
            <div class="col-md-12">
              <div class="activity-feed">
                <?php
                // echo '<pre>';
                // print_r($activity);
                // die;
                foreach ($activity as $activity) {
                  $_custom_data = false; ?>
                  <div class="feed-item" data-sale-activity-id="<?php echo e($activity['id']); ?>">
                    <div class="date">
                      <span class="text-has-action" data-toggle="tooltip"
                        data-title="<?php echo e(_dt($activity['date'])); ?>">
                        <?php echo e(time_ago($activity['date'])); ?>
                      </span>
                    </div>
                    <div class="text">
                      <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                        <a href="<?php echo admin_url('profile/' . $activity['staffid']); ?>">
                          <?php echo staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                          ?>
                        </a>
                      <?php } ?>
                      <?php
                      $additional_data = '';
                      if (!empty($activity['additional_data']) && $additional_data = unserialize($activity['additional_data'])) {
                        $i               = 0;
                        foreach ($additional_data as $data) {
                          if (strpos($data, '<original_status>') !== false) {
                            $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                            $additional_data[$i] = format_invoice_status($original_status, '', false);
                          } elseif (strpos($data, '<new_status>') !== false) {
                            $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                            $additional_data[$i] = format_invoice_status($new_status, '', false);
                          } elseif (strpos($data, '<custom_data>') !== false) {
                            $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                            unset($additional_data[$i]);
                          }
                          $i++;
                        }
                      }

                      $_formatted_activity = _l($activity['note'], $additional_data);

                      if ($_custom_data !== false) {
                        $_formatted_activity .= ' - ' . $_custom_data;
                      }

                      if (!empty($activity['staffid'])) {
                        $get_staff = get_staff_by_id($activity['staffid']);
                        $_formatted_activity = $get_staff->firstname . ' ' . $get_staff->lastname . ' - ' . $_formatted_activity;
                      }

                      echo $_formatted_activity;

                      // if (is_admin()) {
                      //   echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa fa-remove"></i></a>';
                      // } 

                      ?>
                    </div>
                  </div>
                <?php } ?>
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
              <button onclick="sign_request(<?php echo html_entity_decode($goods_receipt->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
            </div>


          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="viewgoodsReceiptAttachmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo _l('attachment'); ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="view_goods_receipt_attachments">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="goods_receipt_file_data"></div>
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

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_payment_date', {
      id: rowId,
      payment_date: paymentDate
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

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_est_delivery_date', {
      id: rowId,
      est_delivery_date: estDeliveryDate
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

    // Perform AJAX request to update the completion date
    $.post(admin_url + 'warehouse/update_delivery_date', {
      id: rowId,
      delivery_date: DeliveryDate
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
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_production_status/' + status + '/' + id)
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
  let addMoreAttachmentsInputKey = 2;

  // Handle adding attachments
  $("body").on("click", ".add_more_attachments_goods", function() {
    if ($(this).hasClass("disabled")) {
      return false;
    }

    const itemIndex = $(this).data("item"); // Fetch the current item index
    if (typeof itemIndex === "undefined") {
      console.error("Item index is undefined. Please ensure the data-item attribute is set correctly.");
      return;
    }

    const parentContainer = $(this).closest(".attachment_new");
    const newAttachment = parentContainer.clone();

    // Update the name attribute with the correct item and attachment index
    newAttachment
      .find("input[type='file']")
      .attr(
        "name",
        `items[${itemIndex}][attachments_new][${addMoreAttachmentsInputKey}]`
      )
      .val("");

    // Replace the "+" button with a "-" button for removing
    newAttachment.find(".fa").removeClass("fa-plus").addClass("fa-minus");
    newAttachment
      .find("button")
      .removeClass("add_more_attachments_goods")
      .addClass("remove_attachment")
      .removeClass("btn-default")
      .addClass("btn-danger");

    // Append the new attachment container after the current one
    parentContainer.after(newAttachment);

    // Increment the attachment key for unique naming
    addMoreAttachmentsInputKey++;
  });

  // Handle removing an attachment
  $("body").on("click", ".remove_attachment", function() {
    // Remove the parent `.attachment_new` container
    $(this).closest(".attachment_new").remove();
    // Reset addMoreAttachmentsInputKey based on the number of existing attachments
    resetAttachmentKeys();
  });

  // Function to recalculate and reset attachment keys
  function resetAttachmentKeys() {
    addMoreAttachmentsInputKey = 1; // Reset the counter
    $(".attachment_new").each(function() {
      const itemIndex = $(this).find(".add_more_attachments_goods").data("item");

      // Update the file input's name with the new sequential key
      $(this)
        .find("input[type='file']")
        .attr(
          "name",
          `items[${itemIndex}][attachments_new][${addMoreAttachmentsInputKey}]`
        );

      addMoreAttachmentsInputKey++; // Increment for the next attachment
    });
  }
</script>