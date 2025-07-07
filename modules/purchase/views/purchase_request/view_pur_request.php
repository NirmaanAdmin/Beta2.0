<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php $csrf = array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash(),
            );
            ?>

            <input type="hidden" id="csrf_token_name" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
            <?php if ($pur_request->currency != 0) {

              $base_currency = pur_get_currency_by_id($pur_request->currency);
            } else {
              $base_currency = $base_currency;
            } ?>
            <?php if ($pur_request->status == 1) { ?>
              <div class="ribbon info"><span class="fontz9"><?php echo _l('purchase_draft'); ?></span></div>
            <?php } elseif ($pur_request->status == 2) { ?>
              <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
            <?php } elseif ($pur_request->status == 3) { ?>
              <div class="ribbon danger"><span><?php echo _l('purchase_reject'); ?></span></div>
            <?php } ?>
            <h4 class=""><?php echo _l($title); ?>
            </h4>
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
                    <li role="presentation" class="<?php if ($this->input->get('tab') == 'task_tab') {
                                                      echo 'active';
                                                    } ?> ">
                      <a href="#task_tab" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($pur_request->id); ?>,'purchase_request'); return false;" aria-controls="task_tab" role="tab" data-toggle="tab">
                        <?php echo _l('task'); ?>
                      </a>

                    </li>
                    <li role="presentation">
                      <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                        <?php echo _l('invoice_view_activity_tooltip'); ?>
                      </a>
                    </li>





                  </ul>
                </div>
              </div>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="information">

                  <div class="row">
                    <div class="col-md-12">
                      <p class="bold col-md-4 p_style"><?php echo _l('information'); ?></p>
                      <div>
                        <?php if ($pur_request->status == 2) { ?>
                          <!-- <a href="<?php echo admin_url('purchase/pur_order?pr=' . $pur_request->id); ?>" class="btn btn-info save_detail pull-right" target="_blank"><?php echo _l('convert_to_po'); ?></a> -->
                        <?php } ?>
                      </div>
                      <div class="col-md-3 pull-right">
                        <div class="task-info task-status task-info-status pull-right">
                          <?php if ($check_approval_setting) { ?>
                            <h5>
                              <i class="fa fa-<?php if ($pur_request->status == 2) {
                                                echo 'star';
                                              } else if ($pur_request->status == 1) {
                                                echo 'star-o';
                                              } else {
                                                echo 'star-half-o';
                                              } ?> pull-left task-info-icon fa-fw fa-lg mtop10"></i><?php echo _l('task_status'); ?>:
                              <?php if (has_permission('purchase_request_change_approve_status', '', 'edit')) { ?>
                                <span class="task-single-menu task-menu-status">
                                  <span class="trigger pointer manual-popover text-has-action">
                                    <?php echo pur_format_approve_status($pur_request->status, true); ?>
                                  </span>
                                  <span class="content-menu hide">
                                    <ul>
                                      <?php
                                      for ($pur_status = 1; $pur_status <= 4; $pur_status++) { ?>
                                        <?php if ($pur_request->status != $pur_status) { ?>
                                          <li>
                                            <a href="#" onclick="purchase_request_mark_as(<?php echo $pur_status; ?>,<?php echo $pur_request->id; ?>); return false;">
                                              <?php echo _l('purchase_request_mark_as', get_status_approve_str($pur_status)); ?>
                                            </a>
                                          </li>
                                        <?php } ?>
                                      <?php } ?>
                                    </ul>
                                  </span>
                                </span>
                              <?php } else { ?>
                                <?php echo pur_format_approve_status($pur_request->status, true); ?>
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
                            <span class="bold"><?php echo _l('pur_rq_code'); ?> :</span>
                            <span><?php echo pur_html_entity_decode($pur_request->pur_rq_code); ?></span>
                          </td>
                          <td width="50%">
                            <span class="bold"><?php echo _l('group_pur'); ?> :</span>
                            <span><?php foreach ($commodity_groups_request as $group) {
                                    if ($group['id'] == $pur_request->group_pur) {
                                      echo $group['name'];
                                    }
                                  } ?></span>
                          </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('pur_rq_name'); ?> :</span>
                            <span><?php echo pur_html_entity_decode($pur_request->pur_rq_name); ?></span>
                          </td>
                          <td width="50%">
                            <span class="bold"><?php echo _l('sub_groups_pur'); ?> :</span>
                            <span><?php foreach ($sub_groups_request as $group) {
                                    if ($group['id'] == $pur_request->sub_groups_pur) {
                                      echo $group['sub_group_name'];
                                    }
                                  } ?></span>
                          </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('purchase_requestor'); ?> :</span>
                            <span>
                              <?php $_data = '<a href="' . admin_url('staff/profile/' . $pur_request->requester) . '">' . staff_profile_image($pur_request->requester, ['staff-profile-image-small']) . '</a>';
                              $_data .= ' <a href="' . admin_url('staff/profile/' . $pur_request->requester) . '">' . get_staff_full_name($pur_request->requester) . '</a>';
                              echo pur_html_entity_decode($_data); ?>
                            </span>
                          </td>
                          <td width="50%">
                            <span class="bold"><?php echo _l('area_pur'); ?> :</span>
                            <span><?php foreach ($area_request as $area) {
                                    if ($area['id'] == $pur_request->area_pur) {
                                      echo $area['area_name'];
                                    }
                                  } ?></span>
                          </td>
                        </tr>

                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('request_date'); ?> :</span>
                            <span><?php echo _dt($pur_request->request_date); ?></span>
                          </td>
                          <td width="50%">
                            <span class="bold"><?php echo _l('pdf'); ?> : </span>
                            <span>
                              <div class="btn-group">
                                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if (is_mobile()) {
                                                                                                                                                                                      echo ' PDF';
                                                                                                                                                                                    } ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                  <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pur_request_pdf/' . $pur_request->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                                  <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pur_request_pdf/' . $pur_request->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                  <li><a href="<?php echo admin_url('purchase/pur_request_pdf/' . $pur_request->id); ?>"><?php echo _l('download'); ?></a></li>
                                </ul>
                            </span>


                          </td>
                          </td>
                        </tr>
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('project'); ?> :</span>
                            <span><?php echo get_project_name_by_id($pur_request->project); ?></span>
                          </td>
                          <td width="100%" class="d-flex">
                            <span class="bold"><?php echo _l('public_link'); ?></span>
                            <span>
                              <div class="pull-right _buttons mright5">
                                <a href="javascript:void(0)" onclick="copy_public_link(<?php echo pur_html_entity_decode($pur_request->id); ?>); return false;" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('copy_public_link'); ?>" data-placement="bottom"><i class="fa fa-clone "></i></a>
                              </div>
                              <div class="col-md-9">
                                <?php if ($pur_request->hash != '' && $pur_request->hash != null) {
                                  echo render_input('link_public', '', site_url('purchase/vendors_portal/pur_request/' . $pur_request->id . '/' . $pur_request->hash));
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
                        <tr class="project-overview">
                          <td width="50%">
                            <span class="bold"><?php echo _l('pur_send_to_vendors'); ?> :</span>
                            <span><?php echo get_pur_send_to_vendors_list($pur_request->send_to_vendors); ?></span>
                          </td>
                          <td width="50%">
                            <?php echo get_pur_order_by_id_pur_request($pur_request->id); ?>
                            
                          </td>
                        </tr>

                        <tr class="project-overview">
                          <td class="bold"><?php echo _l('rq_description'); ?></td>
                          <td><?php echo pur_html_entity_decode($pur_request->rq_description); ?></td>
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
                            <label><input type="checkbox" class="column-toggle" data-column="1" checked style="width: 20px;"> <?php echo _l('debit_note_table_item_heading'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="2" checked style="width: 20px;"> <?php echo _l('decription'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="3" checked style="width: 20px;"> <?php echo _l('area'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="4" checked style="width: 20px;"> <?php echo _l('Image'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="5" checked style="width: 20px;"> <?php echo _l('purchase_quantity'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="6" checked style="width: 20px;"> <?php echo _l('unit_price'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="7" checked style="width: 20px;"> <?php echo _l('subtotal_before_tax'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="8" checked style="width: 20px;"> <?php echo _l('debit_note_table_tax_heading'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="9" checked style="width: 20px;"> <?php echo _l('tax_value'); ?></label><br>
                            <label><input type="checkbox" class="column-toggle" data-column="10" checked style="width: 20px;"> <?php echo _l('debit_note_total'); ?></label>
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
                            <th width="20%" align="left" class="hide"><?php echo _l('debit_note_table_item_heading'); ?></th>
                            <th width="20%" align="left"><?php echo _l('debit_note_table_item_heading'); ?></th>
                            <th width="15%" align="right" class="qty"><?php echo _l('decription'); ?></th>
                            <th width="20%" align="right"><?php echo _l('area'); ?></th>
                            <th width="20%" align="right"><?php echo _l('Image'); ?></th>
                            <th width="10%" align="right" class="qty"><?php echo _l('purchase_quantity'); ?></th>
                            <th width="10%" align="right"><?php echo _l('unit_price'); ?></th>
                            <th width="10%" align="right"><?php echo _l('subtotal_before_tax'); ?></th>
                            <th width="15%" align="right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
                            <th width="10%" align="right"><?php echo _l('tax_value'); ?></th>
                            <th width="10%" align="right"><?php echo _l('debit_note_total'); ?></th>
                          </tr>
                        </thead>
                        <tbody class="ui-sortable">
                          <?php
                          $_subtotal = 0;
                          $_total = 0;
                          if (count($pur_request_detail) > 0) {
                            foreach ($pur_request_detail as $es) {
                              $_subtotal += $es['into_money'];
                              $_total += $es['total'];
                          ?>
                              <tr nobr="true" class="sortable">
                                <td class="description hide" align="left"><strong>
                                    <?php
                                    $item = get_item_hp($es['item_code']);
                                    echo isset($item) && isset($item->commodity_code) && isset($item->description)
                                      ? pur_html_entity_decode($item->commodity_code . ' - ' . $item->description)
                                      : pur_html_entity_decode($es['item_text']);
                                    ?>
                                  </strong></td>
                                <td class="description" align="left"><strong>
                                    <?php
                                    $item = get_item_hp($es['item_code']);
                                    echo isset($item) && isset($item->commodity_code) && isset($item->description)
                                      ? pur_html_entity_decode($item->commodity_code . ' - ' . $item->description)
                                      : pur_html_entity_decode($es['item_text']);
                                    ?>
                                  </strong></td>
                                <td align="right"><?php echo nl2br($es['description']); ?></td>
                                <td align="right"><?php echo get_area_name_by_id($es['area']); ?></td>
                                <td align="right">
                                  <?php
                                  if (!empty($es['image'])) {
                                    $img_url = base_url('uploads/purchase/pur_request/' . $es['pur_request'] . '/' . $es['prd_id'] . '/' . $es['image']);
                                    echo '<img class="images_w_table" src="' . $img_url . '" alt="' . $es['image'] . '">';
                                  }
                                  ?>
                                </td>
                                <td align="right"><?php echo pur_html_entity_decode($es['quantity']) . ' ' . pur_get_unit_name($es['unit_id']); ?></td>
                                <td align="right"><?php echo app_format_money($es['unit_price'], $base_currency->symbol); ?></td>
                                <td align="right"><?php echo app_format_money($es['into_money'], $base_currency->symbol); ?></td>
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
                                <td class="amount" align="right"><?php echo app_format_money($es['total'], $base_currency->symbol); ?></td>
                              </tr>
                          <?php }
                          } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="col-md-6 col-md-offset-6">
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
                        <?php if (isset($pur_request)) {
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

                  </div>
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
                                    <img src="<?php echo site_url(PURCHASE_PATH . 'pur_request/signature/' . $pur_request->id . '/signature_' . $value['id'] . '.png'); ?>" class="img_style">
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
                        if ($pur_request->status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')) { ?>
                          <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo pur_html_entity_decode($pur_request->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
                        <?php }
                      }
                      if (isset($check_approve_status['staffid'])) {
                        ?>
                        <?php
                        if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $pur_request->status == 1) { ?>
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
                                  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo pur_html_entity_decode($pur_request->id); ?>); return false;" class="btn btn-success mright15"><?php echo _l('approve'); ?></a>
                                  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo pur_html_entity_decode($pur_request->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
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
                        $path = get_upload_path_by_type('purchase') . 'pur_request/' . $value['rel_id'] . '/' . $value['file_name'];
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
                <div role="tabpanel" class="tab-pane  <?php if ($this->input->get('tab') == 'task_tab') {
                                                        echo 'active';
                                                      } ?>" id="task_tab">
                  <?php init_relation_tasks_table(array('data-new-rel-id' => $pur_request->id, 'data-new-rel-type' => 'purchase_request')); ?>
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
        <button onclick="sign_request(<?php echo pur_html_entity_decode($pur_request->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
      </div>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php init_tail(); ?>
<script>
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
<?php require 'modules/purchase/assets/js/view_pur_request_js.php'; ?>