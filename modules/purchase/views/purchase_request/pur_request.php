<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<style type="text/css">
  .table-responsive {
    overflow-x: visible !important;
    scrollbar-width: none !important;
  }
  .area .dropdown-menu .open {
    width: max-content !important;
  }
</style>
<div id="wrapper">
  <div class="content">
    <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'add_edit_pur_request-form', 'class' => '_transaction_form')); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="customer-profile-group-heading"><?php if (isset($pur_request)) {
              echo pur_html_entity_decode($pur_request->pur_rq_code);
            } else {
              echo _l($title) . ' ' . _l('purchase_request');
            } ?></h4>
            <?php

            if (isset($pur_request)) {
              echo form_hidden('isedit');
            } ?>
            <div class="row accounting-template">


              <div class="row ">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <?php
                    $prefix = get_purchase_option('pur_request_prefix');
                    $next_number = get_purchase_option('next_pr_number');
                    $number = (isset($pur_request) ? $pur_request->number : $next_number);
                    echo form_hidden('number', $number); ?>

                    <?php $pur_rq_code = (isset($pur_request) ? $pur_request->pur_rq_code : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('Y'));
                    echo render_input('pur_rq_code', 'pur_rq_code', $pur_rq_code, 'text', array('readonly' => '')); ?>
                  </div>
                  <div class="col-md-6">
                    <?php $pur_rq_name = (isset($pur_request) ? $pur_request->pur_rq_name : '');
                    echo render_input('pur_rq_name', 'pur_rq_name', $pur_rq_name); ?>
                  </div>

                  <?php
                  $project_id = '';
                  if ($this->input->get('project')) {
                    $project_id = $this->input->get('project');
                  }
                  ?>
                  <div class="row ">
                    <div class="col-md-12">
                      <div class="col-md-3 form-group">
                        <label for="project"><?php echo _l('project'); ?></label>
                        <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($projects as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_request) && $s['id'] == $pur_request->project) {
                              echo 'selected';
                            } else if (!isset($pur_request) && $s['id'] == $project_id) {
                              echo 'selected';
                            } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        </select>
                        <br><br>
                      </div>

                      <div class="col-md-3 form-group">
                        <label for="sale_estimate"><?php echo _l('sale_estimate'); ?></label>
                        <select name="sale_estimate" id="sale_estimate" onchange="coppy_sale_estimate(); return false;" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($salse_estimates as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_request) && $s['id'] == $pur_request->sale_estimate) {
                              echo 'selected';
                            } ?>><?php echo format_estimate_number($s['id']); ?></option>
                          <?php } ?>
                        </select>
                        <br><br>
                      </div>

                      <div class="col-md-3 form-group">
                        <label for="type"><?php echo _l('type'); ?></label>
                        <select name="type" id="type" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="capex" <?php if (isset($pur_request) && $pur_request->type == 'capex') {
                            echo 'selected';
                          } ?>><?php echo _l('capex'); ?></option>
                          <option value="opex" <?php if (isset($pur_request) && $pur_request->type == 'opex') {
                            echo 'selected';
                          } ?>><?php echo _l('opex'); ?></option>
                        </select>
                        <br><br>
                      </div>

                      <div class="col-md-3 ">
                        <?php
                        $currency_attr = array('disabled' => true, 'data-show-subtext' => true);

                        $selected = (isset($pur_request) && $pur_request->currency != 0) ? $pur_request->currency : '';
                        if ($selected == '') {
                          foreach ($currencies as $currency) {

                            if ($currency['isdefault'] == 1) {
                              $selected = $currency['id'];
                            }
                          }
                        }
                        ?>
                        <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3 form-group">
                    <label for="department"><?php echo _l('department'); ?></label>
                    <select name="department" id="department" class="selectpicker" onchange="department_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <?php foreach ($departments as $s) { ?>
                        <option value="<?php echo pur_html_entity_decode($s['departmentid']); ?>" <?php if (isset($pur_request) && $s['departmentid'] == $pur_request->department) {
                          echo 'selected';
                        } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                      <?php } ?>
                    </select>
                    <br><br>
                  </div>


                  <!-- <div class="col-md-3 form-group ">
                    <label for="sale_invoice"><?php echo _l('sale_invoice'); ?></label>
                    <select name="sale_invoice" onchange="coppy_sale_invoice(); return false;" id="sale_invoice" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <?php foreach ($invoices as $inv) { ?>
                        <option value="<?php echo pur_html_entity_decode($inv['id']); ?>" <?php if (isset($pur_request) && $inv['id'] == $pur_request->sale_invoice) {
                                                                                            echo 'selected';
                                                                                          } ?>><?php echo format_invoice_number($inv['id']); ?></option>
                      <?php } ?>
                    </select>

                  </div> -->


                  <div class="col-md-3 form-group">
                    <label for="requester"><?php echo _l('requester'); ?></label>
                    <select name="requester" id="requester" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <?php foreach ($staffs as $s) { ?>
                        <option value="<?php echo pur_html_entity_decode($s['staffid']); ?>" <?php if (isset($pur_request) && $s['staffid'] == $pur_request->requester) {
                          echo 'selected';
                        } elseif ($s['staffid'] == get_staff_user_id()) {
                          echo 'selected';
                        } ?>><?php echo pur_html_entity_decode($s['lastname'] . ' ' . $s['firstname']); ?></option>
                      <?php } ?>
                    </select>
                    <br><br>
                  </div>

                  <div class="col-md-3 form-group">
                    <label for="send_to_vendors"><?php echo _l('pur_send_to_vendors'); ?></label>
                    <select name="send_to_vendors[]" id="send_to_vendors" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <?php
                      if (isset($pur_request)) {
                        $vendors_arr = explode(',', $pur_request->send_to_vendors ?? '');
                      }
                      ?>

                      <?php foreach ($vendors as $s) { ?>
                        <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if (isset($pur_request) && in_array($s['userid'], $vendors_arr)) {
                          echo 'selected';
                        } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-3 form-group" style="clear: both;">
                    <?php
                    $selected = '';
                    foreach ($commodity_groups_pur_request as $group) {
                      if (isset($pur_request)) {
                        if ($pur_request->group_pur == $group['id']) {
                          $selected = $group['id'];
                        }
                      }
                    }
                    echo render_select('group_pur', $commodity_groups_pur_request, array('id', 'name'), 'Budget Head', $selected);
                    ?>
                  </div>
                  <div class="col-md-3 form-group">
                    <?php

                    $selected = '';
                    foreach ($sub_groups_pur_request as $sub_group) {
                      if (isset($pur_request)) {
                        if ($pur_request->sub_groups_pur == $sub_group['id']) {
                          $selected = $sub_group['id'];
                        }
                      }
                    }
                    echo render_select('sub_groups_pur', $sub_groups_pur_request, array('id', 'sub_group_name'), 'Budget Sub Head', $selected);
                    ?>
                  </div>
                  <?php /* <div class="col-md-3 form-group">
                    <?php
                    $selected = '';
                    foreach ($area_pur_request as $area) {
                      if (isset($pur_request)) {
                        if ($pur_request->area_pur == $area['id']) {
                          $selected = $area['id'];
                        }
                      }
                    }
                    echo render_select('area_pur', $area_pur_request, array('id', 'area_name'), 'Area', $selected);
                    ?>
                  </div> */ ?>


                  <div class="col-md-12">
                    <?php $rq_description = (isset($pur_request) ? $pur_request->rq_description : '');
                    echo render_textarea('rq_description', 'rq_description', $rq_description); ?>
                  </div>
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

        <div class="row ">
          <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <div class="mtop10 invoice-item">


                  <div class="row">
                    <div class="col-md-4">
                      <?php $this->load->view('purchase/item_include/main_item_select'); ?>
                    </div>

                    <?php
                    $pur_request_currency = $base_currency;
                    if (isset($pur_request) && $pur_request->currency != 0) {
                      $pur_request_currency = pur_get_currency_by_id($pur_request->currency);
                    }

                    $from_currency = (isset($pur_request) && $pur_request->from_currency != null) ? $pur_request->from_currency : $base_currency->id;
                    echo form_hidden('from_currency', $from_currency);

                    ?>
                    <div class="col-md-8 <?php if ($pur_request_currency->id == $base_currency->id) {
                      echo 'hide';
                    } ?>" id="currency_rate_div">
                    <div class="col-md-10 text-right">

                      <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' (' . $base_currency->name . ' => ' . $pur_request_currency->name . '): ';  ?></span></p>
                    </div>
                    <div class="col-md-2 pull-right">
                      <?php $currency_rate = 1;
                      if (isset($pur_request) && $pur_request->currency != 0) {
                        $currency_rate = pur_get_currency_rate($base_currency->name, $pur_request_currency->name);
                      }
                      echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right');
                      ?>
                    </div>
                  </div>

                </div>
                <div class="table-responsive">
                  <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                    <thead>
                      <tr>
                        <th></th>
                        <th align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> Product code</th>
                        <th align="right"><?php echo _l('description'); ?></th>
                        <th align="right"><?php echo _l('area'); ?></th>
                        <th align="right"><?php echo _l('Image'); ?></th>
                        <th align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '(' . $pur_request_currency->name . ')'; ?></span></th>
                        <th align="right" class="qty"><?php echo _l('purchase_quantity'); ?></th>
                        <th align="right"><?php echo _l('subtotal'); ?><span class="th_currency"><?php echo '(' . $pur_request_currency->name . ')'; ?></span></th>
                        <th align="right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
                        <th align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '(' . $pur_request_currency->name . ')'; ?></span></th>
                        <th align="right"><?php echo _l('debit_note_total'); ?><span class="th_currency"><?php echo '(' . $pur_request_currency->name . ')'; ?></span></th>
                        <th align="right"><i class="fa fa-cog"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo pur_html_entity_decode($purchase_request_row_template); ?>
                    </tbody>
                  </table>
                </div>




                <div class="col-md-6 pright0 col-md-offset-6">
                  <table class="table text-right mbot0">
                    <tbody>
                      <tr id="subtotal">
                        <td class="td_style"><span class="bold"><?php echo _l('subtotal'); ?></span>
                        </td>
                        <td width="65%" id="total_td">

                          <div class="input-group" id="discount-total">

                            <input type="text" readonly="true" class="form-control text-right" name="subtotal" value="<?php if (isset($pur_request)) {
                              echo app_format_money($pur_request->subtotal, '');
                            } ?>">

                            <div class="input-group-addon">
                              <div class="dropdown">

                                <span class="discount-type-selected currency_span" id="subtotal_currency">
                                  <?php
                                  if (!isset($pur_request)) {
                                    echo pur_html_entity_decode($base_currency->symbol);
                                  } else {
                                    if ($pur_request->currency != 0) {
                                      $_currency_symbol = pur_get_currency_name_symbol($pur_request->currency, 'symbol');
                                      echo pur_html_entity_decode($_currency_symbol);
                                    } else {
                                      echo pur_html_entity_decode($base_currency->symbol);
                                    }
                                  }
                                  ?>
                                </span>


                              </div>
                            </div>

                          </div>
                        </td>
                      </tr>

                      <tr id="total">
                        <td class="td_style"><span class="bold"><?php echo _l('total'); ?></span>
                        </td>
                        <td width="65%" id="total_td">
                          <div class="input-group" id="total">
                            <input type="text" readonly="true" class="form-control text-right" name="total_mn" value="<?php if (isset($pur_request)) {
                              echo app_format_money($pur_request->total, '');
                            } ?>">
                            <div class="input-group-addon">
                              <div class="dropdown">

                                <span class="discount-type-selected currency_span">
                                  <?php
                                  if (!isset($pur_request)) {
                                    echo pur_html_entity_decode($base_currency->symbol);
                                  } else {
                                    if ($pur_request->currency != 0) {
                                      $_currency_symbol = pur_get_currency_name_symbol($pur_request->currency, 'symbol');
                                      echo pur_html_entity_decode($_currency_symbol);
                                    } else {
                                      echo pur_html_entity_decode($base_currency->symbol);
                                    }
                                  }
                                  ?>
                                </span>
                              </div>
                            </div>

                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>


                </div>

                <div id="removed-items"></div>
              </div>

            </div>

            <div class="row">
              <div class="col-md-12 mtop15">
                <div class="panel-body bottom-transaction">
                  <?php 
                  $value = (isset($pur_request) ? $pur_request->delivery_terms : '');
                  echo render_textarea('delivery_terms', 'delivery_terms', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                  <?php 
                  $value = (isset($pur_request) ? $pur_request->remarks : '');
                  echo render_textarea('remarks', 'remarks', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="btn-bottom-toolbar text-right">
              <button type="submit" class="btn-tr save_detail btn btn-info mleft10">
                <?php echo _l('submit'); ?>
              </button>

            </div>
            <div class="btn-bottom-pusher"></div>


          </div>

        </div>

      </div>
    </div>
  </div>
  <?php echo form_close(); ?>
</div>
</div>

<?php init_tail(); ?>
</body>

</html>
<?php require 'modules/purchase/assets/js/pur_request_js.php'; ?>