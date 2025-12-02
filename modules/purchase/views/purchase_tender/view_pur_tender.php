<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php


            $csrf = array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash(),
            );
            ?>

            <input type="hidden" id="csrf_token_name" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
            <?php if ($pur_tender->currency != 0) {

              $base_currency = pur_get_currency_by_id($pur_tender->currency);
            } else {
              $base_currency = $base_currency;
            } ?>
            <?php if ($pur_tender->status == 1) { ?>
              <div class="ribbon info"><span class="fontz9"><?php echo _l('purchase_draft'); ?></span></div>
            <?php } elseif ($pur_tender->status == 2) { ?>
              <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
            <?php } elseif ($pur_tender->status == 3) { ?>
              <div class="ribbon danger"><span><?php echo _l('purchase_reject'); ?></span></div>
            <?php } ?>
            <div class="row">
              <div class="col-md-3">
                <h4 class=""><?php echo _l($title); ?></h4>
              </div>
              <div class="col-md-6">
                <!-- <label for="send_to_vendors"><?php echo _l('Select Vendor'); ?></label> -->
                <select name="send_to_vendors" id="send_to_vendors" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" required>
                  <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                  <?php
                  if (isset($pur_tender)) {
                    $vendors_arr = explode(',', $pur_tender->send_to_vendors ?? '');

                    // Only show vendors that are in the vendors_arr
                    foreach ($vendors as $s) {
                      if (in_array($s['userid'], $vendors_arr)) {
                  ?>
                        <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" selected>
                          <?php echo pur_html_entity_decode($s['company']); ?>
                        </option>
                      <?php
                      }
                    }
                  } else {
                    // If no tender is set, show all vendors (or you can leave it empty)
                    foreach ($vendors as $s) {
                      ?>
                      <option value="<?php echo pur_html_entity_decode($s['userid']); ?>">
                        <?php echo pur_html_entity_decode($s['company']); ?>
                      </option>
                  <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-3">
                <div class="btn-group mright5">
                  <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Resend
                  </a>
                  
                </div>
                <div class="btn-group mright5">
                  <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Book Order <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pur_order?pur_tender='.$pur_tender->id.''); ?>" target="_blank">Purchase Order</a></li>
                    <li class="hidden-xs"><a href="<?php echo admin_url('purchase/wo_order?pur_tender='.$pur_tender->id.''); ?>" target="_blank">Work Order</a></li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="row">
              <div class="horizontal-scrollable-tabs preview-tabs-top">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">

                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="<?php if ($this->input->get('tab') != 'attachment') {
                                                      echo 'active';
                                                    } ?>">
                      <a href="#information" aria-controls="information" role="tab" data-toggle="tab">
                        <?php echo _l('pur_information'); ?>
                      </a>
                    </li>

                    <li role="presentation" class="<?php if ($this->input->get('tab') == 'attachment') {
                                                      echo 'active';
                                                    } ?> ">
                      <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                        <?php echo _l('pur_attachment'); ?>
                      </a>
                    </li>
                    <li role="presentation" class="<?php if ($this->input->get('tab') == 'tender_document') {
                                                      echo 'active';
                                                    } ?> ">
                      <a href="#tender_document" aria-controls="tender_document" role="tab" data-toggle="tab">
                        <?php echo _l('tender_document'); ?>
                      </a>
                    </li>
                    <li role="presentation" class="<?php if ($this->input->get('tab') == 'task_tab') {
                                                      echo 'active';
                                                    } ?> ">
                      <a href="#task_tab" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($pur_tender->id); ?>,'purchase_request'); return false;" aria-controls="task_tab" role="tab" data-toggle="tab">
                        <?php echo _l('task'); ?>
                      </a>

                    </li>
                    <li role="presentation">
                      <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                        <?php echo _l('invoice_view_activity_tooltip'); ?>
                      </a>
                    </li>

                    <?php $quotations = get_quotations_by_pur_tender($pur_tender->id); ?>
                    <li role="presentation" class="">
                      <a href="#compare_quotes" aria-controls="compare_quotes" role="tab" data-toggle="tab">
                        <?php echo _l('compare_quotes') . '(' . count($quotations) . ')'; ?>
                      </a>
                    </li>



                  </ul>
                </div>
              </div>

              <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="information">

                  <div class="row">
                    <div class="col-md-12">
                      <p class="bold col-md-4 p_style"><?php echo _l('Tender Information'); ?></p>
                      <div>
                        <?php if ($pur_tender->status == 2) { ?>
                          <!-- <a href="<?php echo admin_url('purchase/pur_order?pr=' . $pur_tender->id); ?>" class="btn btn-info save_detail pull-right" target="_blank"><?php echo _l('convert_to_po'); ?></a> -->
                        <?php } ?>
                      </div>
                      <div class="col-md-3 pull-right">
                        <div class="task-info task-status task-info-status pull-right">
                          <?php if ($check_approval_setting) { ?>
                            <h5>
                              <i class="fa fa-<?php if ($pur_tender->status == 2) {
                                                echo 'star';
                                              } else if ($pur_tender->status == 1) {
                                                echo 'star-o';
                                              } else {
                                                echo 'star-half-o';
                                              } ?> pull-left task-info-icon fa-fw fa-lg mtop10"></i><?php echo _l('task_status'); ?>:
                              <?php if (has_permission('purchase_request_change_approve_status', '', 'edit')) { ?>
                                <span class="task-single-menu task-menu-status">
                                  <span class="trigger pointer manual-popover text-has-action">
                                    <?php echo pur_format_approve_status($pur_tender->status, true); ?>
                                  </span>
                                  <span class="content-menu hide">
                                    <ul>
                                      <?php
                                      for ($pur_status = 1; $pur_status <= 4; $pur_status++) { ?>
                                        <?php if ($pur_tender->status != $pur_status) { ?>
                                          <li>
                                            <a href="#" onclick="purchase_request_mark_as(<?php echo $pur_status; ?>,<?php echo $pur_tender->id; ?>); return false;">
                                              <?php echo _l('purchase_request_mark_as', get_status_approve_str($pur_status)); ?>
                                            </a>
                                          </li>
                                        <?php } ?>
                                      <?php } ?>
                                    </ul>
                                  </span>
                                </span>
                              <?php } else { ?>
                                <?php echo pur_format_approve_status($pur_tender->status, true); ?>
                              <?php } ?>
                            </h5>
                          <?php } ?>
                        </div>

                      </div>
                      <div class=" col-md-12">
                        <hr class="hr_style" />
                      </div>
                    </div>
                  </div>

                  <div class=" col-md-12">

                    <table class="table border table-striped martop0">
                      <tbody>
                        <!-- Row 1 -->
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('Tender Code'); ?> :</span>
                            <span><?php echo pur_html_entity_decode($pur_tender->pur_tn_code); ?></span>
                          </td>
                          <td width="50%">
                            <span class="bold"><?php echo _l('group_pur'); ?> :</span>
                            <span><?php foreach ($commodity_groups_request as $group) {
                                    if ($group['id'] == $pur_tender->group_pur) {
                                      echo $group['name'];
                                    }
                                  } ?></span>
                          </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('Tender Name'); ?> :</span>
                            <span><?php echo pur_html_entity_decode($pur_tender->pur_tn_name); ?></span>
                          </td>
                          <!-- <td width="50%">
                            <span class="bold"><?php echo _l('sub_groups_pur'); ?> :</span>
                            <span><?php foreach ($sub_groups_request as $group) {
                                    if ($group['id'] == $pur_tender->sub_groups_pur) {
                                      echo $group['sub_group_name'];
                                    }
                                  } ?></span>
                          </td> -->
                        </tr>
                        <!-- Row 3 -->
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('purchase_requestor'); ?> :</span>
                            <span>
                              <?php $_data = '<a href="' . admin_url('staff/profile/' . $pur_tender->requester) . '">' . staff_profile_image($pur_tender->requester, ['staff-profile-image-small']) . '</a>';
                              $_data .= ' <a href="' . admin_url('staff/profile/' . $pur_tender->requester) . '">' . get_staff_full_name($pur_tender->requester) . '</a>';
                              echo pur_html_entity_decode($_data); ?>
                            </span>
                          </td>
                          <!-- <td width="50%">
                            <span class="bold"><?php echo _l('area_pur'); ?> :</span>
                            <span><?php foreach ($area_request as $area) {
                                    if ($area['id'] == $pur_tender->area_pur) {
                                      echo $area['area_name'];
                                    }
                                  } ?></span>
                          </td> -->
                          <td width="50%">
                            <span class="bold"><?php echo _l('PDF'); ?> : </span>
                            <span>
                              <div class="btn-group">
                                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if (is_mobile()) {
                                                                                                                                                                                      echo ' PDF';
                                                                                                                                                                                    } ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                  <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pur_request_pdf/' . $pur_tender->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                                  <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pur_request_pdf/' . $pur_tender->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                  <li><a href="<?php echo admin_url('purchase/pur_request_pdf/' . $pur_tender->id); ?>"><?php echo _l('download'); ?></a></li>
                                </ul>
                            </span>


                          </td>
                        </tr>

                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('Tender Date'); ?> :</span>
                            <span><?php echo _dt($pur_tender->request_date); ?></span>
                          </td>

                          </td>
                        </tr>
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('project'); ?> :</span>
                            <span><?php echo get_project_name_by_id($pur_tender->project); ?></span>
                          </td>
                          <td width="100%" class="d-flex">
                            <span class="bold"><?php echo _l('public_link'); ?></span>
                            <span>
                              <div class="pull-right _buttons mright5">
                                <a href="javascript:void(0)" onclick="copy_public_link(<?php echo pur_html_entity_decode($pur_tender->id); ?>); return false;" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('copy_public_link'); ?>" data-placement="bottom"><i class="fa fa-clone "></i></a>
                              </div>
                              <div class="col-md-9">
                                <?php if ($pur_tender->hash != '' && $pur_tender->hash != null) {
                                  echo render_input('link_public', '', site_url('purchase/vendors_portal/pur_tender/' . $pur_tender->id . '/' . $pur_tender->hash));
                                } else {
                                  echo render_input('link_public', '', '');
                                } ?>
                              </div>
                            </span>
                          </td>
                        </tr>
                        <tr>

                        </tr>
                        <tr class="project-overview">

                        </tr>
                        <!-- <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('pur_send_to_vendors'); ?> :</span>
                            <span><?php echo get_pur_send_to_vendors_list($pur_tender->send_to_vendors); ?></span>
                          </td>
                        </tr> -->

                        <tr class="project-overview">
                          <td class="bold"><?php echo _l('rq_description'); ?></td>
                          <td><?php echo pur_html_entity_decode($pur_tender->tn_description); ?></td>
                        </tr>

                      </tbody>
                    </table>
                  </div>



                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-10 pull-right" style="z-index: 99999; display: flex; justify-content: end;">
                        <span style="margin-right: 10px;">
                          <button class="btn btn-primary" id="settings-toggle">Columns</button>
                          <div id="settings-dropdown" style="display: none; position: absolute; background: #fff; border: 1px solid #ccc; padding: 10px; width: 130px;">
                            <label><input type="checkbox" class="column-toggle" data-column="1" checked style="width: 20px;"> <?php echo _l('Uniclass Code'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="2" checked style="width: 20px;"> <?php echo _l('decription'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="2" checked style="width: 20px;"> <?php echo _l('Sub Head'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="3" checked style="width: 20px;"> <?php echo _l('area'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="4" checked style="width: 20px;"> <?php echo _l('Image'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="5" checked style="width: 20px;"> <?php echo _l('purchase_quantity'); ?></label><br>
                            <!-- <label><input type="checkbox" class="column-toggle" data-column="6" checked style="width: 20px;"> <?php echo _l('unit_price'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="7" checked style="width: 20px;"> <?php echo _l('subtotal_before_tax'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="8" checked style="width: 20px;"> <?php echo _l('debit_note_table_tax_heading'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="9" checked style="width: 20px;"> <?php echo _l('tax_value'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="10" checked style="width: 20px;"> <?php echo _l('debit_note_total'); ?></label> -->
                          </div>
                        </span>
                        <span style="padding: 0px;">
                          <button id="export-csv" class="btn btn-primary pull-right">Export to CSV</button>
                        </span>
                      </div>
                    </div>

                    <p class="p_style"><?php echo _l('pur_detail'); ?></p>
                    <hr class="hr_style" />

                    <div class="table-responsive">
                      <table class="table items items-preview estimate-items-preview" data-type="estimate">
                        <thead>
                          <tr>

                            <th width="20%" align="left"><?php echo _l('Uniclass Code'); ?></th>
                            <th width="15%" align="right" class="qty"><?php echo _l('decription'); ?></th>
                            <th width="10%" align="right" class="qty"><?php echo _l('Sub Head'); ?></th>
                            <th width="20%" align="right"><?php echo _l('area'); ?></th>
                            <th width="20%" align="right"><?php echo _l('Image'); ?></th>
                            <th width="10%" align="right" class="qty"><?php echo _l('purchase_quantity'); ?></th>
                            <th width="10%" align="right"><?php echo _l('unit_price'); ?></th>
                            <th><?php echo _l('Quotated Price'); ?></th>
                            <th width="20%" align="right"><?php echo _l('Remarks'); ?></th>
                            <!-- <th width="10%" align="right"><?php echo _l('subtotal_before_tax'); ?></th>
                            <th width="15%" align="right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
                            <th width="10%" align="right"><?php echo _l('tax_value'); ?></th>
                            <th width="10%" align="right"><?php echo _l('debit_note_total'); ?></th> -->
                          </tr>
                        </thead>
                        <tbody class="ui-sortable">
                          <?php
                          $_subtotal = 0;
                          $_total = 0;
                          if (count($pur_tender_detail) > 0) {
                            foreach ($pur_tender_detail as $es) {
                              $_subtotal += $es['into_money'];
                              $_total += $es['total'];
                          ?>
                              <tr nobr="true" class="sortable">
                                <td class="description" align="left"><strong>
                                    <?php
                                    $item = get_tender_item_name_from_itemable($es['item_code']);
                                    echo isset($item) ? pur_html_entity_decode($item)
                                      : pur_html_entity_decode($es['item_text']);
                                    ?>
                                  </strong></td>
                                <td align="right"><?php echo nl2br($es['description']); ?></td>
                                <td align="right"><?php echo get_sub_head_name_by_id($es['sub_head']); ?></td>
                                <td align="right"><?php echo get_area_name_by_id($es['area']); ?></td>
                                <td align="right">
                                  <?php
                                  if (!empty($es['image'])) {
                                    $img_url = base_url('uploads/purchase/pur_tender/' . $es['pur_tender'] . '/' . $es['tn_id'] . '/' . $es['image']);
                                    echo '<img class="images_w_table" width="100" src="' . $img_url . '" alt="' . $es['image'] . '">';
                                  }
                                  ?>
                                </td>
                                <td align="right"><?php echo pur_html_entity_decode($es['quantity']) . ' ' . pur_get_unit_name($es['unit_id']); ?></td>
                                <td align="right"><?php echo app_format_money($es['unit_price'], $base_currency->symbol); ?></td>
                                <td align="right">0</td>
                                <td align="right"><?php echo $es['remarks']; ?></td>

                                <!-- <td align="right"><?php echo app_format_money($es['into_money'], $base_currency->symbol); ?></td>
                                <td align="right">
                                  <?php
                                  if ($es['tax_name'] != '') {
                                    echo pur_html_entity_decode($es['tax_name']);
                                  } else {
                                    $this->load->model('purchase/purchase_model');
                                    $tax_str = '';
                                    $tax_arr = $es['tax'] != '' ? explode('|', $es['tax']) : [];
                                    foreach ($tax_arr as $index => $tax_id) {
                                      $tax_str .= $this->purchase_model->get_tax_name($tax_id);
                                      if ($index < count($tax_arr) - 1) {
                                        $tax_str .= '|';
                                      }
                                    }
                                    echo pur_html_entity_decode($tax_str);
                                  }
                                  ?>
                                </td>
                                <td align="right"><?php echo app_format_money($es['tax_value'], $base_currency->symbol); ?></td>
                                <td class="amount" align="right"><?php echo app_format_money($es['total'], $base_currency->symbol); ?></td> -->
                              </tr>
                          <?php }
                          } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- <div class="col-md-6 col-md-offset-6">
                    <table class="table text-right mbot0">
                      <tbody>
                        <tr id="subtotal">
                          <td class="td_style"><span class="bold"><?php echo _l('subtotal'); ?></span>
                          </td>
                          <td width="65%" id="total_td">

                            <?php echo app_format_money($_subtotal, $base_currency->symbol); ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table text-right">
                      <tbody id="tax_area_body">
                        <?php if (isset($pur_tender)) {
                          echo $taxes_data['html'];
                        ?>
                        <?php } ?>
                      </tbody>
                    </table>

                    <table class="table text-right">
                      <tbody id="tax_area_body">
                        <tr id="total">
                          <td class="td_style"><span class="bold"><?php echo _l('total'); ?></span>
                          </td>
                          <td width="65%" id="total_td">
                            <?php echo app_format_money($_total, $base_currency->symbol); ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                  </div> -->
                  <?php echo form_hidden('request_detail'); ?>

                  <div class=" col-md-12">
                    <?php if (count($list_approve_status) > 0) { ?>
                      <p class=" p_style"><?php echo _l('pur_approval_infor'); ?></p>
                      <hr class="hr_style" />
                      <div class="project-overview-right">


                        <div class="row">
                          <div class="col-md-12 project-overview-expenses-finance">
                            <?php
                            $this->load->model('staff_model');
                            $enter_charge_code = 0;
                            foreach ($list_approve_status as $value) {
                              $value['staffid'] = explode(', ', $value['staffid'] ?? '');
                              if ($value['action'] == 'sign') {
                            ?>
                                <div class="col-md-3 apr_div">
                                  <p class="text-uppercase text-muted no-mtop bold">
                                    <?php
                                    $staff_name = '';
                                    $st = _l('status_0');
                                    $color = 'warning';
                                    foreach ($value['staffid'] as $key => $val) {
                                      if ($staff_name != '') {
                                        $staff_name .= ' or ';
                                      }
                                      $staff_name .= isset($this->staff_model->get($val)->firstname) ? $this->staff_model->get($val)->firstname : '';
                                    }
                                    echo pur_html_entity_decode($staff_name);
                                    ?></p>
                                  <?php if ($value['approve'] == 2) {
                                  ?>
                                    <img src="<?php echo site_url(PURCHASE_PATH . 'pur_request/signature/' . $pur_tender->id . '/signature_' . $value['id'] . '.png'); ?>" class="img_style">
                                    <br><br>
                                    <p class="bold text-center text-success"><?php echo _l('signed') . ' ' . _dt($value['date']); ?></p>
                                  <?php } ?>

                                </div>
                              <?php } else { ?>
                                <div class="col-md-3 apr_div">
                                  <p class="text-uppercase text-muted no-mtop bold">
                                    <?php
                                    $staff_name = '';
                                    foreach ($value['staffid'] as $key => $val) {
                                      if ($staff_name != '') {
                                        $staff_name .= ' or ';
                                      }
                                      $staff_name .= isset($this->staff_model->get($val)->firstname) ? $this->staff_model->get($val)->firstname : '';
                                    }
                                    echo pur_html_entity_decode($staff_name);
                                    ?></p>
                                  <?php if ($value['approve'] == 2) {
                                  ?>
                                    <?php if ($value['approve_by_admin'] == 1) { ?>
                                      <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved_by_admin.png'); ?>" class="img_style">
                                    <?php } else { ?>
                                      <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved.png'); ?>" class="img_style">
                                    <?php } ?>
                                  <?php } elseif ($value['approve'] == 3) { ?>
                                    <img src="<?php echo site_url(PURCHASE_PATH . 'approval/rejected.png'); ?>" class="img_style">
                                  <?php } ?>
                                  <br><br>
                                  <p><?php echo pur_html_entity_decode($value['note']) ?></p>
                                  <p class="bold text-center text-<?php if ($value['approve'] == 2) {
                                                                    echo 'success';
                                                                  } elseif ($value['approve'] == 3) {
                                                                    echo 'danger';
                                                                  } ?>"><?php echo _dt($value['date']); ?></p>
                                </div>
                            <?php }
                            } ?>
                          </div>
                        </div>


                      </div>
                    <?php } ?>
                    <div class="pull-right">
                      <?php
                      if ($check_appr && $check_appr != false) {
                        if ($pur_tender->status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')) { ?>
                          <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo pur_html_entity_decode($pur_tender->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
                        <?php }
                      }
                      if (isset($check_approve_status['staffid'])) {
                        ?>
                        <?php
                        if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $pur_tender->status == 1) { ?>
                          <div class="btn-group">
                            <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right ul_style">
                              <li>
                                <div class="col-md-12">
                                  <?php echo render_textarea('reason', 'reason'); ?>
                                </div>
                              </li>
                              <li>
                                <div class="row text-right col-md-12">
                                  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo pur_html_entity_decode($pur_tender->id); ?>); return false;" class="btn btn-success mright15"><?php echo _l('approve'); ?></a>
                                  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo pur_html_entity_decode($pur_tender->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
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

                <div role="tabpanel" class="tab-pane  <?php if ($this->input->get('tab') == 'attachment') {
                                                        echo 'active';
                                                      } ?>" id="attachment">
                  <div class="col-md-12">
                    <?php
                    if (isset($attachments) && count($attachments) > 0) {
                      foreach ($attachments as $value) {
                        echo '<div class="col-md-3">';
                        $path = get_upload_path_by_type('purchase') . 'pur_tender/' . $value['rel_id'] . '/' . $value['file_name'];
                        $is_image = is_image($path);
                        if ($is_image) {
                          echo '<div class="preview_image">';
                        }
                    ?>
                        <a href="<?php echo site_url('download/file/purchase/' . $value['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-purchase-<?php echo $value['rel_id']; ?>" <?php } ?>>
                          <i class="<?php echo get_mime_class($value['filetype']); ?>"></i> <?php echo $value['file_name']; ?>
                          <?php if ($is_image) { ?>
                            <img class="mtop5" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']); ?>" style="height: 165px;">
                          <?php } ?>
                        </a>
                        <?php if ($is_image) {
                          echo '</div>';
                          echo '<a href="' . admin_url('purchase/delete_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                        } ?>
                    <?php echo '</div>';
                      }
                    } ?>
                  </div>
                </div>

                <div role="tabpanel" class="tab-pane  <?php if ($this->input->get('tab') == 'tender_document') {
                                                        echo 'active';
                                                      } ?>" id="tender_document">


                  <a href="javascript:void(0);" class="btn btn-primary pull-left mright5" onclick="add_document(); return false;">
                    <i class="fa-regular fa-plus tw-mr-1"></i>Add Document
                  </a>

                  <?php if ($tender_document_detail) { ?>
                    <table border="1" style="width: 100%; border-collapse: collapse;">
                      <thead>
                        <tr style="background-color: #f2f2f2;">
                          <th style="padding: 10px; text-align: left;">Sr No.</th>
                          <th style="padding: 10px; text-align: left;">Document Name</th>
                          <th style="padding: 10px; text-align: left;">Date</th>
                          <th style="padding: 10px; text-align: left;">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $counter = 1;
                        $document_types = [
                          "Letter Of Agreement" => "view_latter_of_agreement",
                          "Appendix to Contract" => "view_appendix_to_contract",
                          "Particular Conditions of Contract" => "view_particular_conditions_of_contract",
                          "Contractor General Obligations" => "view_contractor_general_obligations",
                          "FIDIC Condition of Contract" => "view_fidic_condition_of_contract",
                          "Environmental Health & Safety Manual" => "view_environmental_health_safety"
                        ];

                        foreach ($document_types as $doc_type => $view_function) { ?>
                          <tr>
                            <td style="padding: 8px;"><?php echo $counter++; ?></td>
                            <td style="padding: 8px;"><?php echo $doc_type; ?></td>
                            <td style="padding: 8px;">
                              <?php
                              echo date('d M, Y', strtotime($tender_document_detail[0]['date']));
                              ?>
                            </td>
                            <td style="padding: 8px;">
                              <div class="btn-group">
                                <a href="<?php echo admin_url('purchase/' . $view_function . '/' . $pur_tender->id); ?>" class="btn btn-default btn-icon" style="padding: 10px !important">
                                  <i class="fa fa-pencil-square"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 7px !important">
                                  <i class="fa fa-file-pdf"></i>
                                  <?php if (is_mobile()) {
                                    echo ' PDF';
                                  } ?>
                                  <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                  <li class="hidden-xs">
                                    <a href="javascript:void(0)"><?php echo _l('view_pdf'); ?></a>
                                  </li>
                                  <li class="hidden-xs">
                                    <a href="javascript:void(0)" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a>
                                  </li>
                                  <li>
                                    <a href="javascript:void(0)"><?php echo _l('download'); ?></a>
                                  </li>
                                  <li>
                                    <a href="javascript:void(0)" target="_blank"><?php echo _l('print'); ?></a>
                                  </li>
                                </ul>
                              </div>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  <?php } else { ?>
                    <p>No documents found.</p>
                  <?php } ?>

                </div>
                <div role="tabpanel" class="tab-pane  <?php if ($this->input->get('tab') == 'task_tab') {
                                                        echo 'active';
                                                      } ?>" id="task_tab">
                  <?php init_relation_tasks_table(array('data-new-rel-id' => $pur_tender->id, 'data-new-rel-type' => 'purchase_request')); ?>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="activity-feed">
                        <?php foreach ($activity as $activity) {
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

                              $_formatted_activity = _l($activity['description'], $additional_data);

                              if ($_custom_data !== false) {
                                $_formatted_activity .= ' - ' . $_custom_data;
                              }

                              if (!empty($activity['full_name'])) {
                                $_formatted_activity = e($activity['full_name']) . ' - ' . $_formatted_activity;
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

                <div role="tabpanel" class="tab-pane ptop10 " id="compare_quotes">
                  <?php if (total_rows(db_prefix() . 'pur_estimates', ['pur_tender' => $pur_tender->id]) > 0) { ?>

                    <!-- <div class="btn-group pull-right mright5" data-toggle="tooltip" title="<?php echo _l('compare_quotation_tooltip'); ?>">
                      <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li class="hidden-xs"><a href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                        <li class="hidden-xs"><a href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                        <li><a href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id); ?>"><?php echo _l('download'); ?></a></li>
                      </ul>
                    </div> -->

                    <div class="col-md-6">
                      <table class="table border table-striped martop0">
                        <tbody>
                          <tr class="project-overview">
                            <td class="bold" width="30%">Tender Code</td>
                            <td><?php echo pur_html_entity_decode($pur_tender->pur_tn_code); ?><div class="btn-group  mright5" style="margin-left: 10px;" data-toggle="tooltip" title="" data-original-title="Preview/Download compare quotation pdf">
                                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fa fa-file-pdf"></i><span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                  <li class="hidden-xs"><a
                                      href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a>
                                  </li>
                                  <li class="hidden-xs"><a
                                      href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id . '?output_type=I'); ?>"
                                      target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                  <li><a
                                      href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id); ?>"><?php echo _l('download'); ?></a>
                                  </li>
                                  <li>
                                    <a href="<?php echo admin_url('purchase/compare_quotation_pdf/' . $pur_tender->id . '?print=true'); ?>"
                                      target="_blank">
                                      <?php echo _l('print'); ?>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </td>
                          </tr>
                          <tr class="project-overview">
                            <td class="bold">Tender Name</td>
                            <td><?php echo _l($pur_tender->pur_tn_name); ?></td>
                          </tr>
                          <tr class="project-overview">
                            <td class="bold"><?php echo _l('purchase_requestor'); ?></td>
                            <td><?php $_data = '<a href="' . admin_url('staff/profile/' . $pur_tender->requester) . '">' . staff_profile_image($pur_tender->requester, [
                                  'staff-profile-image-small',
                                ]) . '</a>';
                                $_data .= ' <a href="' . admin_url('staff/profile/' . $pur_tender->requester) . '">' . get_staff_full_name($pur_tender->requester) . '</a>';
                                echo pur_html_entity_decode($_data);
                                ?></td>
                          </tr>

                          <tr class="project-overview">
                            <td class="bold"><?php echo _l('request_date'); ?></td>
                            <td><?php echo date('d M, Y H:i A', strtoTime($pur_tender->request_date)); ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-6">
                      <table class="table border table-striped martop0">
                        <tbody>

                          <tr class="project-overview">
                            <td class=""><span class="bold"><?= _l('group_pur') ?> :</span></td>
                            <td>
                              <?php foreach ($commodity_groups_request as $group) {
                                if ($group['id'] == $pur_tender->group_pur) {
                                  echo $group['name'];
                                }
                              } ?>
                            </td>
                            <td>

                            </td>
                          </tr>


                          <tr class="project-overview">
                            <td class="bold "><?php echo _l('vendors'); ?></td>
                            <td class="bold "></td>
                            <td class="bold "></td>
                          </tr>
                          <?php

                          $arr_vendors = get_arr_vendors_by_tender($pur_tender->id); ?>
                          <?php foreach ($arr_vendors as $vendor) { ?>
                            <tr class="project-overview">
                              <td class=""><span class="bold"><?php echo pur_html_entity_decode($vendor->company); ?></span></td>
                              <td class=""><span class="bold"><?php echo _l('vendor_code') . ': ' ?></span><span class=""><?php echo pur_html_entity_decode($vendor->vendor_code); ?></span></td>
                              <td class=""><span class="bold"><?php echo _l('phonenumber') . ': '; ?></span><span class=""><?php echo pur_html_entity_decode($vendor->phonenumber); ?></span></td>
                            </tr>
                          <?php } ?>

                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-12">
                      <hr>
                    </div>
                    <div class="col-md-12">
                      <div class="table-responsive">
                        <?php echo form_open(admin_url('purchase/compare_quote_pur_tender/' . $pur_tender->id), array('id' => 'compare_quote_pur_tender-form'));  ?>
                        <table class="table table-bordered compare_quotes_table">
                          <thead class="bold">
                            <tr>
                              <th rowspan="2" scope="col"><span class="bold"><?php echo _l('items'); ?></span></th>
                              <th rowspan="2" scope="col"><span class="bold"><?php echo _l('pur_qty'); ?></span></th>
                              <th rowspan="2" scope="col"><span class="bold"><?php echo _l('unit'); ?></span></th>
                              <th rowspan="2" scope="col"><span class="bold"><?php echo _l('description'); ?></span></th>

                              <?php foreach ($quotations as $quote) { ?>
                                <th colspan="2" class="text-center"><span class="bold text-danger"><?php echo format_pur_estimate_number($quote['id']) . ' - ' . get_vendor_company_name($quote['vendor']); ?></span></th>
                              <?php } ?>
                            </tr>

                            <tr>
                              <?php foreach ($quotations as $quote) { ?>
                                <th class="text-right"><span class="bold"><?php echo _l('purchase_unit_price'); ?></span></th>
                                <th class="text-right"><span class="bold"><?php echo _l('total') ?></span></th>
                              <?php } ?>

                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $this->load->model('purchase/purchase_model');
                            $list_items = $this->purchase_model->get_pur_tender_detail($pur_tender->id);
                            ?>
                            <?php foreach ($list_items as $key => $item) { ?>
                              <tr>
                                <td><?php echo pur_html_entity_decode($key + 1); ?></td>
                                <td><?php echo pur_html_entity_decode($item['quantity']); ?></td>
                                <td><?php $unit_name = isset(get_unit_type_item($item['unit_id'])->unit_name) ? get_unit_type_item($item['unit_id'])->unit_name : '';
                                    echo pur_html_entity_decode($unit_name); ?></td>
                                <td><?php $item_name = isset(get_item_hp($item['item_code'])->description) ? get_item_hp($item['item_code'])->description : '';
                                    echo pur_html_entity_decode($item_name); ?></td>

                                <?php foreach ($quotations as $quote) { ?>
                                  <?php
                                  $_currency = $base_currency;
                                  if ($quote['currency'] != 0) {
                                    $_currency = pur_get_currency_by_id($quote['currency']);
                                  }
                                  ?>
                                  <?php $item_quote = get_item_detail_in_quote($item['item_code'], $quote['id']); ?>
                                  <?php if (isset($item_quote)) { ?>
                                    <td class="text-right"><?php echo app_format_money($item_quote->unit_price, $_currency->name); ?></td>
                                    <td class="text-right"><?php echo app_format_money($item_quote->total_money, $_currency->name); ?></td>
                                  <?php } else { ?>
                                    <td>-</td>
                                    <td>-</td>
                                  <?php } ?>
                                <?php } ?>

                              </tr>
                            <?php } ?>
                            <tr>
                              <td colspan="4" class="text-center"><span class="bold"><?php echo _l('mark_a_contract'); ?></span></td>
                              <?php foreach ($quotations as $quote) { ?>
                                <td colspan="2"><input name="mark_a_contract[<?php echo pur_html_entity_decode($quote['id']); ?>]" type="text" value="<?php echo pur_html_entity_decode($quote['make_a_contract']); ?>" /></td>
                              <?php } ?>
                            </tr>
                            <tr>
                              <td colspan="4" class="text-center"><span class="bold"><?php echo _l('total_purchase_amount'); ?></span></td>
                              <?php foreach ($quotations as $quote) { ?>
                                <?php
                                $_currency = $base_currency;
                                if ($quote['currency'] != 0) {
                                  $_currency = pur_get_currency_by_id($quote['currency']);
                                }
                                ?>
                                <td colspan="2" class="text-right">
                                  <span class="bold text-info"><?php echo app_format_money($quote['total'], $_currency->name); ?></span>
                                  <?php
                                  if ($_currency->id != $base_currency->id) {
                                    $convert_rate = pur_get_currency_rate($_currency->name, $base_currency->name);
                                    $convert_value = round(($quote['total'] * $convert_rate), 2);
                                    echo '<br><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="' . _l('pur_convert_from') . ' ' . $_currency->name . ' ' . _l('pur_to') . ' ' . $base_currency->name . ' ' . _l('pur_with_currency_rate') . ': ' . $convert_rate . '"></i>&nbsp;&nbsp;<span class="bold text-info">' . app_format_money($convert_value, $base_currency->name) . '</span>';
                                  }
                                  ?>
                                </td>
                              <?php } ?>
                            </tr>
                          </tbody>

                        </table>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <br>
                      <!-- <p><span class="bold"><?php echo _l('purchase_request_description') . ': '; ?></span><span><?php echo pur_html_entity_decode($pur_tender->tn_description); ?></span></p> -->
                    </div>
                    <div class="col-md-12">
                      <button id="sm_btn" class="btn btn-info save_detail pull-right"><?php echo _l('pur_confirm'); ?></button>
                    </div>
                    <?php echo form_close(); ?>

                  <?php } else { ?>

                    <div class="col-md-12">
                      <span class="text-bold"><?php echo _l('this_purchase_request_does_not_have_a_quote_yet'); ?></span>
                    </div>


                  <?php } ?>
                </div>

              </div>


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
        <input type="text" class="ip_style" tabindex="-1" name="signature" id="signatureInput">
        <div class="dispay-block">
          <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>

        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
        <button onclick="sign_request(<?php echo pur_html_entity_decode($pur_tender->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
      </div>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open(admin_url('purchase/add_tender_document/' . $pur_tender->id), array('id' => 'add_ternder_document-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>
          <?php echo _l('Add Document'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <input type="hidden" name="tender_id" value="<?php echo pur_html_entity_decode($pur_tender->id); ?>">
          <?php echo render_date_input('date', 'Date'); ?>
          <?php echo render_select('vendor_id', $vendors, array('userid', 'company'), 'Vendor'); ?>
          <?php echo render_select('project_id', $projects, array('id', 'name'), 'Project'); ?>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-info btn-missed-punch"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<?php init_tail(); ?>
<script>
  function add_document() {
    "use strict";
    $('#addDocumentModal').modal();
  }
  // Toggle dropdown
  document.getElementById('settings-toggle').addEventListener('click', function() {
    const dropdown = document.getElementById('settings-dropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  });

  // Column toggle
  document.querySelectorAll('.column-toggle').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      const columnIndex = parseInt(this.getAttribute('data-column'));
      const table = document.querySelector('.items-preview');
      table.querySelectorAll('tr').forEach(function(row) {
        const cells = row.querySelectorAll('th, td');
        if (cells[columnIndex]) {
          cells[columnIndex].style.display = checkbox.checked ? '' : 'none';
        }
      });
    });
  });
</script>
<script>
  document.getElementById('export-csv').addEventListener('click', function() {
    // Select the table
    const table = document.querySelector('.items-preview');
    const rows = Array.from(table.querySelectorAll('tr'));

    // Initialize CSV content
    let csvContent = '';

    // Loop through each row
    rows.forEach(row => {
      const cells = Array.from(row.querySelectorAll('th, td'));
      const rowContent = cells.map(cell => `"${cell.textContent.trim()}"`).join(',');
      csvContent += rowContent + '\n';
    });

    // Add UTF-8 BOM
    const bom = '\uFEFF';

    // Create a Blob and downloadable link
    const blob = new Blob([bom + csvContent], {
      type: 'text/csv;charset=utf-8;'
    });
    const url = URL.createObjectURL(blob);

    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'items_export.csv');
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
</script>

</body>

</html>
<?php require 'modules/purchase/assets/js/view_pur_tender_js.php'; ?>