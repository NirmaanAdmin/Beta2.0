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

  .error-border {
    border: 1px solid red;
  }

  .loader-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
  }

  .loader-gif {
    width: 100px;
    /* Adjust the size as needed */
    height: 100px;
  }
  .cost_fetch_pur_item {
    cursor: pointer;
  }
  .remaining_qty_red_class {
    color: red !important;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="loader-container hide" id="loader-container">
      <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
    </div>

    <div class="row">
      <?php
      echo form_open_multipart($this->uri->uri_string(), array('id' => 'pur_order-form', 'class' => '_transaction_form'));
      if (isset($pur_order)) {
        echo form_hidden('isedit');
      }
      ?>
      <?php
      if ($book_order) {
        $prefix = get_purchase_option('pur_order_prefix');
        $next_number = get_purchase_option('next_po_number');
        $pur_order_number = $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('M-Y');
        if (get_option('po_only_prefix_and_number') == 1) {
          $pur_order_number = $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT);
        }
        $pur_order['pur_order_name'] = $cost_package_detail->package_name;
        $pur_order['pur_order_number'] = $pur_order_number;
        $pur_order['number'] = $next_number;
        $pur_order['estimate'] = $cost_package_detail->estimate_id;
        $pur_order['group_pur'] = $cost_package_detail->budget_head;
        $pur_order['project'] = $cost_package_detail->project_id;
        $pur_order['kind'] = $cost_package_detail->kind;
        $pur_order['budget'] = $cost_package_detail->total_package;
        $pur_order['order_date'] = _d(date('Y-m-d'));
        $pur_order['buyer'] = get_staff_user_id();
        $pur_order = (object) $pur_order;
        echo form_hidden('package_id', $cost_package_detail->id);
      }
      ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
          <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top">
              <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
              <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
              <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                    <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                      <?php echo _l('pur_general_infor'); ?>
                    </a>
                  </li>
                  <?php
                  $customer_custom_fields = false;
                  if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'pur_order', 'active' => 1)) > 0) {
                    $customer_custom_fields = true;
                  ?>

                  <?php } ?>

                  <li role="presentation" class="">
                    <a href="#shipping_infor" aria-controls="shipping_infor" role="tab" data-toggle="tab">
                      <?php echo _l('pur_shipping_infor'); ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">
                  <?php $additional_discount = 0; ?>
                  <input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                        <?php $pur_order_name = (isset($pur_order) ? $pur_order->pur_order_name : '');
                        echo render_input('pur_order_name', 'pur_order_description', $pur_order_name); ?>

                      </div>
                      <div class="col-md-6 form-group">
                        <?php $prefix = get_purchase_option('pur_order_prefix');
                        $next_number = get_purchase_option('next_po_number');

                        $pur_order_number = (isset($pur_order) ? $pur_order->pur_order_number : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('M-Y'));
                        if (get_option('po_only_prefix_and_number') == 1) {
                          $pur_order_number = (isset($pur_order) ? $pur_order->pur_order_number : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT));
                        }


                        $number = (isset($pur_order) ? $pur_order->number : $next_number);
                        echo form_hidden('number', $number); ?>

                        <label for="pur_order_number"><?php echo _l('pur_order_number'); ?></label>

                        <input type="text" readonly class="form-control" name="pur_order_number" value="<?php echo pur_html_entity_decode($pur_order_number); ?>">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">

                        <label for="vendor"><?php echo _l('vendor'); ?></label>
                        <select name="vendor" id="vendor" class="selectpicker" <?php if (isset($pur_order)) {
                          echo '';
                        } ?> onchange="estimate_by_vendor(this); return  false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php foreach ($vendors as $s) { ?>
                          <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if (isset($pur_order) && $pur_order->vendor == $s['userid']) {
                            echo 'selected';
                          } else {
                            if (isset($ven) && $ven == $s['userid']) {
                              echo 'selected';
                            }
                          } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
                        <?php } ?>
                      </select>

                      </div>

                      <?php
                      if ($convert_po && $selected_pr && $selected_project) {
                        $pur_order['pur_request'] = $selected_pr;
                        $pur_order['project'] = $selected_project;
                        $pur_order = (object) $pur_order;
                      }
                      ?>
                      <div class="col-md-6 form-group">
                        <label for="pur_request"><?php echo _l('pur_request'); ?></label>
                        <select name="pur_request" id="pur_request" class="selectpicker" onchange="coppy_pur_request(); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($pur_request as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_order) && $pur_order->pur_request != '' && $pur_order->pur_request == $s['id']) {
                              echo 'selected';
                            } ?>><?php echo pur_html_entity_decode($s['pur_rq_code'] . ' - ' . $s['pur_rq_name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                          <label for="budget"><?php echo _l('budget'); ?></label>
                          <select name="estimate" id="estimate" class="selectpicker  <?php if (isset($pur_order)) { echo 'disabled';} ?>" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value=""></option>
                              <?php foreach ($budgets as $s) { ?>
                                <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_order) && $pur_order->estimate != '' && $pur_order->estimate == $s['id']) { echo 'selected';} ?>>
                                <?php echo format_estimate_number($s['id']); ?>
                                <?php echo !empty($s['budget_description']) ? ' - '.$s['budget_description'] : ''; ?>
                                </option>
                              <?php } ?>
                          </select>

                        </div>

                      <div class="col-md-6 form-group">
                        <label for="department"><?php echo _l('department'); ?></label>
                        <select name="department" id="department" class="selectpicker" <?php if (isset($pur_order)) {
                          
                        } ?> data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php foreach ($departments as $s) { ?>
                          <option value="<?php echo pur_html_entity_decode($s['departmentid']); ?>" <?php if (isset($pur_order) && $s['departmentid'] == $pur_order->department) {
                            echo 'selected';
                          } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>

                    <?php
                    $project_id = '';
                    if ($this->input->get('project')) {
                      $project_id = $this->input->get('project');
                    }
                    ?>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <input type="hidden" name="project" id="project_val" value="">
                        <label for="project"><?php echo _l('project'); ?></label>
                        <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($projects as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_order) && $s['id'] == $pur_order->project) {
                              echo 'selected';
                            } else if (!isset($pur_order) && $s['id'] == $project_id) {
                              echo 'selected';
                            } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <div class="col-md-6 form-group">
                        <label for="type"><?php echo _l('type'); ?></label>
                        <select name="type" id="type" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="capex" <?php if (isset($pur_order) && $pur_order->type == 'capex') {
                            echo 'selected';
                          } ?>><?php echo _l('capex'); ?></option>
                          <option value="opex" <?php if (isset($pur_order) && $pur_order->type == 'opex') {
                            echo 'selected';
                          } ?>><?php echo _l('opex'); ?></option>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12 form-group">
                        <div id="inputTagsWrapper">
                          <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                          <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($pur_order) ? prep_tags_input(get_tags_in($pur_order->id, 'pur_order')) : ''); ?>" data-role="tagsinput">
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6 ">
                        <?php
                        $currency_attr = array('disabled' => true, 'data-show-subtext' => true);

                        $selected = '';
                        foreach ($currencies as $currency) {
                          if (isset($pur_order) && $pur_order->currency != 0) {
                            if ($currency['id'] == $pur_order->currency) {
                              $selected = $currency['id'];
                            }
                          } else {
                            if ($currency['isdefault'] == 1) {
                              $selected = $currency['id'];
                            }
                          }
                        }

                        ?>
                        <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                      </div>

                      <!-- <div class="col-md-6 mbot10 form-group">
                        <?php
                        $selected = '';
                        foreach ($staff as $member) {
                          if (isset($pur_order)) {
                            if ($pur_order->delivery_person == $member['staffid']) {
                              $selected = $member['staffid'];
                            }
                          } else {
                            if ($member['staffid'] == get_staff_user_id()) {
                              $selected = $member['staffid'];
                            }
                          }
                        }
                        echo render_select('delivery_person', $staff, array('staffid', array('firstname', 'lastname')), 'delivery_person', $selected);
                        ?>
                      </div> -->
                      <div class="col-md-6">
                        <?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : _d(date('Y-m-d')));
                        echo render_date_input('order_date', 'order_date', $order_date); ?>
                      </div>
                    </div>
                    <div class="row">


                      <div class="col-md-6 ">
                        <?php
                        $selected = '';
                        foreach ($staff as $member) {
                          if (isset($pur_order)) {
                            if ($pur_order->buyer == $member['staffid']) {
                              $selected = $member['staffid'];
                            }
                          } else {
                            if ($member['staffid'] == get_staff_user_id()) {
                              $selected = $member['staffid'];
                            }
                          }
                        }
                        echo render_select('buyer', $staff, array('staffid', array('firstname', 'lastname')), 'buyer', $selected);
                        ?>
                      </div>
                      <div class="col-md-6 ">
                        <div class="form-group select-placeholder">
                          <label for="discount_type"
                          class="control-label"><?php echo _l('discount_type'); ?></label>
                          <select name="discount_type" class="selectpicker" data-width="100%"
                          data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                          <option value="before_tax" <?php
                          if (isset($pur_order)) {
                            if ($pur_order->discount_type == 'before_tax') {
                              echo 'selected';
                            }
                          } ?>><?php echo _l('discount_type_before_tax'); ?></option>
                          <option value="after_tax" <?php if (isset($pur_order)) {
                            if ($pur_order->discount_type == 'after_tax' || $pur_order->discount_type == null) {
                              echo 'selected';
                            }
                          } else {
                            echo 'selected';
                          } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 ">
                        <?php
                        $selected = '';
                        foreach ($commodity_groups_pur as $group) {
                          if (isset($pur_order)) {
                            if ($pur_order->group_pur == $group['id']) {
                              $selected = $group['id'];
                            }
                          }
                          if (isset($selected_head)) {
                            if ($selected_head == $group['id']) {
                              $selected = $group['id'];
                            }
                          }
                        }
                        echo render_select('group_pur', $commodity_groups_pur, array('id', 'name'), 'Budget Head', $selected);
                        ?>
                      </div>
                      <div class="col-md-6">
                        <?php $budget = (isset($pur_order) ? $pur_order->budget : '');
                        echo render_input('budget', 'budget_ro_projection', $budget, 'number'); ?>
                      </div>
                      <!-- <div class="col-md-6 ">

                        <?php

                        $selected = '';
                        foreach ($sub_groups_pur as $sub_group) {
                          if (isset($pur_order)) {
                            if ($pur_order->sub_groups_pur == $sub_group['id']) {
                              $selected = $sub_group['id'];
                            }
                          }
                          if (isset($selected_sub_head)) {
                            if ($selected_sub_head == $sub_group['id']) {
                              $selected = $sub_group['id'];
                            }
                          }
                        }
                        echo render_select('sub_groups_pur', $sub_groups_pur, array('id', 'sub_group_name'), 'Budget Sub Head', $selected);
                        ?>
                      </div> -->
                      <!-- <div class="col-md-6 form-group select-placeholder">
                        <label for="clients" class="control-label"><?php echo _l('clients'); ?></label>
                        <select id="clients" name="clients[]" data-live-search="true" onchange="client_change(this); return false;" multiple data-width="100%" class="ajax-search client-ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                          <?php
                          foreach ($clients_ed as $client_id) {
                            $selected = (is_numeric($client_id) ? $client_id : '');
                            if ($selected != '') {
                              $rel_data = get_relation_data('customer', $selected);
                              $rel_val = get_relation_values($rel_data, 'customer');
                              echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                            }
                          }
                          ?>
                        </select>
                      </div> -->
                      <!-- <div class="col-md-6 form-group ">
                        <label for="sale_invoice"><?php echo _l('sale_invoice'); ?></label>
                        <select name="sale_invoice" id="sale_invoice" class="selectpicker" onchange="coppy_sale_invoice(); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($invoices as $inv) { ?>
                            <option value="<?php echo pur_html_entity_decode($inv['id']); ?>" <?php if (isset($pur_order) && $inv['id'] == $pur_order->sale_invoice) {
                                                                                                echo 'selected';
                                                                                              } ?>><?php echo format_invoice_number($inv['id']); ?></option>
                          <?php } ?>
                        </select>
                      </div> -->
                    </div>

                    <!-- <div class="row">
                      <div class="col-md-6 ">
                        <?php $days_owed = (isset($pur_order) ? $pur_order->days_owed : '');
                        echo render_input('days_owed', 'days_owed', $days_owed, 'number'); ?>
                      </div>
                      <div class="col-md-6 ">
                        <?php $delivery_date = (isset($pur_order) ? _d($pur_order->delivery_date) : '');
                        echo render_date_input('delivery_date', 'delivery_date', $delivery_date); ?>
                      </div>

                    </div> -->

                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="kind"><?php echo _l('cat'); ?></label>
                        <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="Client Supply" <?php if (isset($pur_order) && $pur_order->kind == 'Client Supply') {
                            echo 'selected';
                          } ?>><?php echo _l('client_supply'); ?></option>
                          <option value="Bought out items" <?php if (isset($pur_order) && $pur_order->kind == 'Bought out items') {
                            echo 'selected';
                          } ?>><?php echo _l('bought_out_items'); ?></option>
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="hsn_sac" class="control-label"><?php echo _l('hsn_sac') ?></label>
                        <select name="hsn_sac" id="hsn_sac" class="selectpicker" data-live-search="true" data-width="100%">
                          <option value=""></option>
                          <?php foreach ($get_hsn_sac_code as $item): ?>
                            <?php
                            $selected = '';
                            if (isset($pur_order)) {
                              if ($pur_order->hsn_sac == $item['id']) {
                                $selected = 'selected';
                              }
                            }

                            $words = explode(' ', $item['name']);
                            $shortName = implode(' ', array_slice($words, 0, 7));
                            ?>
                            <option value="<?= $item['id'] ?>" <?= $selected  ?>>
                              <?= htmlspecialchars($shortName) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <?php if ($customer_custom_fields) { ?>

                  <?php $rel_id = (isset($pur_order) ? $pur_order->id : false); ?>
                  <?php echo render_custom_fields('pur_order', $rel_id); ?>

                <?php } ?>
              </div>

              <div role="tabpanel" class="tab-pane" id="shipping_infor">
                <div class="row">
                  <div class="col-md-6">
                    <?php $shipping_address = isset($pur_order) ? $pur_order->shipping_address : get_option('pur_company_address');
                    if ($shipping_address == '') {
                      $shipping_address = get_option('pur_company_address');
                    }

                    echo render_textarea('shipping_address', 'pur_company_address', $shipping_address, ['rows' => 7]); ?>

                    <?php $shipping_zip = isset($pur_order) ? $pur_order->shipping_zip : get_option('pur_company_zipcode');
                    if ($shipping_zip == '') {
                      $shipping_zip = get_option('pur_company_zipcode');
                    }
                    echo render_input('shipping_zip', 'pur_company_zipcode', $shipping_zip, 'text'); ?>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-12">
                        <?php $shipping_city = isset($pur_order) ? $pur_order->shipping_city : get_option('pur_company_zipcode');
                        if ($shipping_city == '') {
                          $shipping_city = get_option('pur_company_city');
                        }
                        echo render_input('shipping_city', 'pur_company_city', $shipping_city, 'text'); ?>
                      </div>
                      <div class="col-md-12">
                        <?php $shipping_state = isset($pur_order) ? $pur_order->shipping_state : get_option('pur_company_state');
                        if ($shipping_state == '') {
                          $shipping_state = get_option('pur_company_state');
                        }
                        echo render_input('shipping_state', 'pur_company_state', $shipping_state, 'text'); ?>
                      </div>

                      <div class="col-md-12">
                        <?php $shipping_country_text = isset($pur_order) ? $pur_order->shipping_country_text : get_option('pur_company_country_text');
                        if ($shipping_country_text == '') {
                          $shipping_country_text = get_option('pur_company_country_text');
                        }
                        echo render_input('shipping_country_text', 'pur_company_country_text', $shipping_country_text, 'text'); ?>
                      </div>

                      <div class="col-md-12">
                        <?php $countries = get_all_countries();
                        $pur_company_country_code = get_option('pur_company_country_code');
                        $selected = isset($pur_order) ? $pur_order->shipping_country : $pur_company_country_code;
                        if ($selected == '') {
                          $selected = $pur_company_country_code;
                        }

                        echo render_select('shipping_country', $countries, array('country_id', array('short_name')), 'pur_company_country_code', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                        ?>

                      </div>
                    </div>
                  </div>
                </div>

              </div>


            </div>
          </div>

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
                $path = get_upload_path_by_type('purchase') . 'pur_order/' . $value['rel_id'] . '/' . $value['file_name'];
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
                <?php
                // echo '</div>';
                echo '<a href="' . admin_url('purchase/delete_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                ?>
            <?php if ($is_image) { echo '</div>'; } ?>
            <?php echo '</div>';
              }
            } ?>
          </div>

          <div class="cost_complete_sheet" style="display: none;">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3 pull-right">
                  <button type="button" class="btn btn-info pull-right" id="download_historical_data" style="margin-left: 7px;"><?php echo _l('download_historical_data'); ?></button>
                  <button type="button" class="btn btn-info pull-right" id="cost_control_sheet"><?php echo _l('cost_control_sheet'); ?></button>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>

          <div class="modal fade" id="cost_complete_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: 98%;">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">View Items</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <div class="col-md-3" style="padding-left: 0px; padding-top: 5px;">
                    <?php
                    echo render_select('cost_sub_head', $sub_groups_pur, array('id', 'sub_group_name'), 'Sub Head');
                    ?>
                  </div>
                </div>

                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="view_cost_control_sheet">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="panel-body mtop10 invoice-item">

            <div class="row">
              <div class="col-md-4">
                <div class="cost_complete_sheet" style="display: none;">
                  <button type="button" class="btn btn-info enable_item_select mbot5">
                    <?php echo _l('add_non_budgeted_items'); ?>
                  </button>
                </div>
              </div>
              <?php if (!$is_edit) { ?>
                <div class="col-md-8">
                  <div class="col-md-2 pull-right">
                    <div id="dowload_file_sample" style="margin-top: 22px;">
                      <label for="file_csv" class="control-label"> </label>
                      <a href="<?php echo site_url('modules/purchase/uploads/file_sample/Sample_import_item_en.xlsx') ?>" class="btn btn-primary">Template</a>
                    </div>
                  </div>
                  <div class="col-md-4 pull-right" style="display: flex;align-items: end;padding: 0px;">
                    <?php echo form_open_multipart(admin_url('purchase/import_file_xlsx_pur_order_items'), array('id' => 'import_form')); ?>
                    <?php echo form_hidden('leads_import', 'true'); ?>
                    <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>

                    <div class="form-group" style="margin-left: 10px;">
                      <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);"><?php echo _l('import'); ?></button>
                    </div>
                    <?php echo form_close(); ?>
                  </div>

                </div>
                <div class="col-md-12 ">
                  <div class="form-group pull-right" id="file_upload_response">

                  </div>

                </div>
                <div id="box-loading" class="pull-right">

                </div>
              <?php } ?>

              <?php
              $po_currency = $base_currency;
              if (isset($pur_order) && $pur_order->currency != 0) {
                $po_currency = pur_get_currency_by_id($pur_order->currency);
              }

              $from_currency = (isset($pur_order) && $pur_order->from_currency != null) ? $pur_order->from_currency : $base_currency->id;
              echo form_hidden('from_currency', $from_currency);

              ?>
              <div class="col-md-8 <?php if ($po_currency->id == $base_currency->id) {
                                      echo 'hide';
                                    } ?>" id="currency_rate_div">
                <div class="col-md-10 text-right">

                  <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' (' . $base_currency->name . ' => ' . $po_currency->name . '): ';  ?></span></p>
                </div>
                <div class="col-md-2 pull-right">
                  <?php $currency_rate = 1;
                  if (isset($pur_order) && $pur_order->currency != 0) {
                    $currency_rate = pur_get_currency_rate($base_currency->name, $po_currency->name);
                  }
                  echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right');
                  ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                    <thead>
                      <tr>
                        <th></th>
                        <th align="left" style="min-width: 76px"><?php echo _l('serial_no'); ?></th>
                        <th align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> Uniclass Code</th>
                        <th align="left"><?php echo _l('item_description'); ?></th>
                        <th align="left"><?php echo _l('sub_groups_pur'); ?></th>
                        <th align="right"><?php echo _l('area'); ?></th>
                        <th align="right"><?php echo _l('Image'); ?></th>
                        <th align="right" class="qty"><?php echo _l('quantity'); ?></th>
                        <th align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>

                        <th align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                        <!-- <th align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th> -->
                        <th align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo $pur_order_row_template; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="col-md-8 col-md-offset-4">
                <table class="table text-right">
                  <tbody>
                    <tr id="subtotal">
                      <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
                        <?php echo form_hidden('total_mn', ''); ?>
                      </td>
                      <td class="wh-subtotal">
                      </td>
                    </tr>

                    <tr id="order_discount_percent">
                      <td>
                        <div class="row">
                          <div class="col-md-7">
                            <span class="bold"><?php echo _l('pur_discount'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_percent_note'); ?>"></i></span>
                          </div>
                          <div class="col-md-3">
                            <?php $discount_total = isset($pur_order) ? $pur_order->discount_total : '';
                            echo render_input('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
                          </div>
                          <div class="col-md-2">
                            <select name="add_discount_type" id="add_discount_type" class="selectpicker" onchange="pur_calculate_total(); return false;" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value="percent">%</option>
                              <option value="amount" selected><?php echo _l('amount'); ?></option>
                            </select>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-7">
                            <span class="bold"><?php echo _l('discount_remarks'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_remarks_note'); ?>"></i></span>
                          </div>
                          <div class="col-md-3">
                            <?php $discount_remarks = isset($pur_order) ? $pur_order->discount_remarks : '';
                            echo render_input('discount_remarks', '', $discount_remarks); ?>
                          </div>
                          <div class="col-md-2">
                          </div>
                        </div>
                      </td>
                      <td class="order_discount_value">

                      </td>
                    </tr>

                    <tr id="total_discount">
                      <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
                        <?php echo form_hidden('dc_total', ''); ?>
                      </td>
                      <td class="wh-total_discount">
                      </td>
                    </tr>

                    <tr>
                      <td>
                        <div class="row">
                          <div class="col-md-9">
                            <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
                          </div>
                          <div class="col-md-3">
                            <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if (isset($pur_order)) {
                              echo $pur_order->shipping_fee;
                            } else {
                              echo '0';
                            } ?>" class="form-control pull-left text-right" name="shipping_fee">
                          </div>
                        </div>
                      </td>
                      <td class="shiping_fee">
                      </td>
                    </tr>

                    <tr id="totalmoney">
                      <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
                        <?php echo form_hidden('grand_total', ''); ?>
                      </td>
                      <td class="wh-total">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div id="removed-items"></div>
            </div>
          </div>
          <?php
          if ($book_order) {
            unset($pur_order);
          } ?>
          <div class="row">
            <div class="col-md-12 mtop15">
              <div class="panel-body bottom-transaction">
                <?php $value = (isset($pur_order) ? $pur_order->order_summary : get_purchase_option('order_summary'));
                $day = date("j");
                $month = date("F");
                $year = date("Y");
                function getOrdinalSuffix($day) {
                    if ($day > 3 && $day < 21) return $day . "th";
                    switch ($day % 10) {
                        case 1: return $day . "st";
                        case 2: return $day . "nd";
                        case 3: return $day . "rd";
                        default: return $day . "th";
                    }
                }
                $formatted_date = getOrdinalSuffix($day) . " " . $month . " " . $year;
                if (!isset($pur_order) && $pur_order->order_summary == '') {
                  $value = '<strong>PURCHASE ORDER</strong><br><br>

                <strong>M/S <span class="vendor_name"></span></strong><br>
                <span class="vendor_address"></span><br>
                <span class="vendor_city"></span><span class="vendor_state"></span><span class="vendor_pincode"></span></span><span class="vendor_country"></span><br><br>

                <strong>P.O. Number:</strong> BI/JAMNAGAR/24-25/'.str_pad($next_number, 5, '0', STR_PAD_LEFT).'<br>
                <strong>P.O. Date:</strong> <span class="order_full_date">'.date("d-M-y").'</span><br>
                <strong>Rev. No.:</strong><br>
                <strong>Rev. Date:</strong><br><br>

                <strong>PAN No.:</strong><span class="pan_number"></span><br>
                <strong>Beneficiary name:</strong> <br>
                <strong>GST No.:</strong> <span class="vendor_gst"></span><br>
                <strong>Bank Details:</strong> <span class="vendor_bank_details"></span>
                <br>
                <br>

                <strong>Contact Person:</strong> <span class="vendor_contact"></span><br>
                <strong>Telephone:</strong> <span class="vendor_contact_phone"></span><br>
                <strong>Email:</strong> <span class="vendor_contact_email"></span><br><br>

                <strong>Project:</strong> <span class="project_name"></span><br>
                <strong>Subject:</strong> <span class="pur_order_name"></span><br><br>

                Dear Sir/Madam,<br>
                This is with reference to your final offer dated <span class="order_date">'.$formatted_date.'</span> and further our subsequent discussions with regards to <span class="pur_order_name"></span> for our above-mentioned project. We are pleased to issue you the order of <strong>INR <span class="subtotal_in_value"></span>/-</strong> (<span class="subtotal_in_words"></span>) (Exclusive of GST) on the following terms and conditions and specifications for the same as annexed.<br><br>

                <strong>Currency:</strong> INR<br><br>

                <strong>Terms:</strong> F.O.R. at Site<br><br>

                <strong>Type of Order:</strong> Item Rate Order<br><br>

                <strong>Price Escalation:</strong> The agreed amount shall remain fixed for this project. No escalation shall be paid in contract duration and for this project for any reason whatsoever. Price shall be valid for 6 Months from the date of Sign of PO.<br><br>

                <strong>Destination:</strong> M/s BASILIUS INTERNATIONAL<br>
                3rd Floor, 304, Benison Commercial Complex, Old Padra Road, Opposite Hari Bhakti, Vadodara, Gujarat – 390007<br><br>

                <strong>Delivery Schedule:</strong> Within 65 to 70 days from the date of receipt of confirm order.<br><br>

                <strong>Payment Terms:</strong><br>
                100% advance along with the confirm order.<br><br>

                <strong>Part Shipment:</strong> Allowed.<br><br>

                <strong>Trans-Shipment:</strong> Allowed<br><br>

                <strong>Shipping, Delivery & Acceptance:</strong><br>
                The supplier will export quality packaging and ship all goods in industry standards as may be applicable to ensure that the goods are received by the buyer in good condition. The applicable Purchase Order number must appear on all shipping containers, packing lists, delivery tickets, and Invoice.<br><br>

                <strong>Packing Charges:</strong> Inclusive. Material must be preserved, packaged, handled, and packed to permit efficient handling, provide protection from loss or damage, and comply with industry standards and carrier requirements. Supplier will be liable for any loss or damage due to its failure to properly preserve, package, handle, or pack any shipment. You shall provide the packing list along with Invoice and other documents.<br><br>

                <strong>Other Terms & Conditions:</strong><br>
                (a) All items should be as per approved specification, ratings, and protection level of by consultant / Basilius team.<br>
                (b) All material & accessories should be delivered as per purchase order only and subject to MAS/ Consultant approval.<br>
                (c) Third party inspection can be organized from our side, pre dispatch inspection will be done, if required.<br>
                (d) Replacement/Rectification: Within 7-10 days if found any damage.<br>
                (e) All material should be strictly as per applicable standard.<br>
                (f) Goods shall be packaged properly to ensure safe arrival at the project site.<br>
                (g) Transportation to the site and transit insurance shall be in your scope.<br>
                (h) Service / complain response time: Within 48 hours from the time of complaint logged, if required.<br>
                Supplier shall provide all the documents incl. delivery challan, invoice, materials test certificate/ Declaration of Conformity, Serial no, technical details of the product being supplied being asked by Basilius etc.<br><br>

                <strong>Annexures:</strong><br>
                Annexure A (Order Summary)<br>
                Annexure B (Special conditions of purchase)<br>
                Annexure C (General terms & conditions of purchase)<br><br>

                We hereby acknowledge and accept the purchase order and the annexures.<br><br>

                <table border="1" style="width: 99.9909%; height: 287px;"><colgroup><col style="width: 49.9602%;"><col style="width: 49.9602%;"></colgroup>
                <tbody>
                <tr style="height: 92.8px;">
                <td style="height: 92.8px;">
                <p><span style="color: rgb(0,0,0); font-family: verdana, geneva, sans-serif; font-size: 10pt;"><b>On behalf of Basilius International<span class="Apple-tab-span"> </span></b></span></p>
                <p><span style="color: rgb(0,0,0); font-family: verdana, geneva, sans-serif; font-size: 10pt;"><b>the annexures</b></span></p>
                </td>
                <td style="height: 92.8px;"><span style="color: rgb(0,0,0); font-family: verdana, geneva, sans-serif; font-size: 10pt;"><b><span class="Apple-tab-span">W</span>e hereby acknowledge and accept the purchase order</b></span></td>
                </tr>
                <tr>
                <td>
                <p><span style="font-size: 10pt; font-family: verdana, geneva, sans-serif;"><span style="color: rgb(0,0,0);"></span></span></p>
                <p><span style="font-size: 10pt; font-family: verdana, geneva, sans-serif;"><span style="color: rgb(0,0,0);">Authorized Signatory</span></span></p>
                <p><span style="font-size: 10pt; font-family: verdana, geneva, sans-serif;"><span style="color: rgb(0,0,0);"><span style="color: rgb(0,0,0); font-family: verdana, geneva, sans-serif; font-size: 10pt;">(Affix stamp)</span></span></span></p>
                <p><span style="font-size: 10pt; font-family: verdana, geneva, sans-serif;"><span style="color: rgb(0,0,0);"> </span></span></p>
                </td>
                <td>
                <p></p>
                <p>Authorized Signatory of the supplier</p>
                <p><span style="font-size: 10pt; font-family: verdana, geneva, sans-serif;"><span style="color: rgb(0,0,0);"><span style="color: rgb(0,0,0); font-family: verdana, geneva, sans-serif; font-size: 10pt;">(Affix stamp)</span></span></span></p>
                <p><span style="font-size: 10pt; font-family: verdana, geneva, sans-serif;"></span></p>
                </td>
                </tr>
                </tbody>
                </table>';
                }

                ?>


                <?php echo render_textarea('order_summary', 'estimate_add_edit_order_summary', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                <?php $value = (isset($pur_order) ? $pur_order->vendornote : get_purchase_option('vendor_note'));
                if (!isset($pur_order) && $pur_order->vendornote == '') {
                  $value = '

                <h4 style="font-size: 20px;text-align: center;"><strong>ANNEXURE - B</strong><br>
                <strong>SPECIAL CONDITIONS OF PURCHASE</strong></h4><br><br>

                <strong>BUYER ADDRESS:</strong><br>
                BASILIUS INTERNATIONAL LLP<br>
                3rd Floor, 304 Benison Commercial Complex, Old PADRA Road<br>
                Opposite Hari Bhakti, Vadodara, Gujarat - 390007<br>
                <strong>GST:</strong> 24AAYFB0472D1ZJ<br><br>

                <strong>SITE ADDRESS:</strong><br>
                BASILIUS INTERNATIONAL<br>
                Guest House Property at Jamnagar, Gujarat<br><br>

                <strong>Contact Person:</strong> Rupesh Singh, Project Coordinator (+91)7300618065<br><br>

                <strong>CORRESPONDENCE</strong><br>
                Acknowledgment of this Purchase Order, invoices, and commercial correspondence shall be sent by courier to the above address, attention Rupesh, with a scanned copy sent by mail to <strong>bgj.project@basilius.in</strong>.<br>
                All telephone communications and email correspondence for coordination purposes shall be addressed to our Project Head, M/s Basilius International (<strong>bgj.project@basilius.in</strong>), +91 7300618065.<br>
                All documents should be sent to the following correspondence address:<br><br>

                Mr. Rupesh Singh: Project Coordinator: +91 7300618065<br>
                Address: BASILIUS INTERNATIONAL LLP<br>
                Guest House Property at Jamnagar, Gujarat<br><br>

                <strong>WARRANTY:</strong> 18 months from date of Installation.<br><br>

                <strong>SPECIAL CONDITIONS</strong><br>
                <strong>TEST CERTIFICATE:</strong><br>
                Vendor shall provide all the test certificate, certificate of origin, warranty certificate etc.<br><br>

                <strong>PACKAGING LABELLING / MARKS</strong><br>
                - Name of Purchaser<br>
                - Description of Goods<br>
                - Name of Supplier<br>
                - Quantity / Volume<br>
                - Delivery Address<br>
                - Gross / Net Weight<br><br>

                <strong>SPECIAL REMARKS:</strong><br>
                <strong>PACKAGING LABELLING / MARKS</strong><br>
                Name of Purchaser<br>
                Description of Goods<br>
                Name of Supplier<br>
                Delivery Address<br>
                Quantity / Volume<br>
                Gross / Net Weight<br>
                ';
                }

                ?>


                <?php echo render_textarea('vendornote', 'estimate_add_edit_vendor_note', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                <?php $value = (isset($pur_order) ? $pur_order->terms :  get_purchase_option('terms_and_conditions'));

                if (!isset($pur_order) && $pur_order->terms == '') {
                  $value = "<h4 style='font-size: 20px;text-align: center;'><strong>ANNEXURE - C<br>GENERAL TERMS & CONDITIONS OF PURCHASE</strong></h4><br><br>

                <strong>1. Definitions.</strong><br>
                1.1 In these Conditions: 'BASILLUS' means “BASILIUS INTERNATIONAL LLP INTERNATIONAL LLP”; 'Completion Date' means the date or dates shown on the Order Form overleaf; 'The Vendor' means the supplier or service provider to whom the Order overleaf is addressed; 'The Contract' means terms and conditions signed between BASILLUS and the Vendor, governing the subject matter of this Order (inclusive of the Order Form set out overleaf); 'The Goods' means the goods (if any) to be supplied under the Contract, and; 'The Services' means the services (if any) to be provided and any work carried out under the Contract.<br><br>

                <strong>2. Subcontracting.</strong><br>
                2.1 The Vendor shall not assign, transfer or encumber any part or all of the Vendor's rights and obligations under this Order, directly or indirectly, without the written approval of BASILLUS. Any assignment or transfer of this Order or any interest herein, without the written consent of BASILLUS, shall be void and of no effect and will, at the option of BASILLUS, render this Order void. Notwithstanding any such consent or approval, the Vendor shall continue to remain liable for and under this Contract.<br><br>

                <strong>3. Delivery.</strong><br>
                3.1 Unless otherwise specified by BASILLUS in writing, the Services must be provided, and the Goods must be delivered (allowing sufficient time for unloading) in accordance with the Order, for time of delivery and in case of no time specification in the Order, the Vendor shall deliver Goods as agreed between the parties. The Vendor shall be responsible for delivering Goods in good working condition at the address for delivery shown overleaf or as per the terms specified in the Order in this regard.<br><br>

                3.2 Delivery terms of this Purchase Order shall be interpreted in accordance with 'Incoterms (International Rules for the Interpretation of Trade Terms) 2000' and any amendment and supplement thereto.<br><br>

                3.3 Time is of the essence and the Vendor shall be liable for damages as mentioned herein incurred due to delays in delivery. If Goods are not available for delivery or the Services cannot be provided at the due time, the Vendor shall (without prejudice to BASILLUS's rights under the Contract) immediately inform BASILLUS by telephone, facsimile or e-mail and confirm such communication in writing. BASILLUS also reserves the right to charge the Vendor liquidated damages for each day of such delay, which shall be equivalent to 1% of the value of Goods or Services per week of delay subject to a Maximum 5% of the value of the order.<br><br>

                3.4 The Parties acknowledge that the liquidated damages are genuine pre-estimates of reasonable compensation for the loss and damage that will be suffered by BASILLUS in the event of any failure on the part of the Vendor to complete the supply of the Goods or performance of the Services. The Vendor irrevocably undertakes that it will not, whether by legal proceedings or otherwise, contend that the levels of liquidated damages are not reasonable, nor will it put BASILLUS to the proof thereof.<br><br>

                <strong>4. Quality of goods and services.</strong><br>
                4.1 The Vendor warrants to BASILLUS that:<br>
                (a) The Goods and Services would conform in all respects with the Order and to recognized international or equivalent standards and codes (where applicable) and be to the reasonable satisfaction of BASILLUS; and<br>
                (b) The Goods and Services would be as per the required specifications of BASILLUS as set out in the Order; and conform to the IS codes, drawings samples or other description(s) furnished or adopted by BASILLUS. Goods furnished to BASILLUS's patterns, specifications, drawings or fabricated with its tools shall not be furnished or quoted to any other person or concern.<br>
                (c) The Goods must be of sound materials and good manufacture i.e., they must be obtained by the Vendor from authorized dealers and free from defects and encumbrances; and<br>
                (d) The Goods so required by BASILLUS, must be original and not second hand when delivered.<br>
                (e) The Goods and Services manufactured and shipped or performed are in compliance with all applicable laws, rules and regulations including but not limited to any foreign exchange regulations, pollution control, occupation safety, hazardous materials transportation regulations and any other statutory rules, regulations, codes, ordinances, statutes and laws that may be introduced from time to time.<br>
                (f) Vendor shall not make any expenditure for any unlawful purposes (i.e., unlawful under the laws or regulations of India or any law which BASILLUS or the Vendor shall be subject to including the Foreign Corrupt Practices Act) in the performance of its Services /Supply of Goods under this Order and in connection with its activities in relation thereto. Neither the Vendor nor any person acting for or on its behalf shall bribe or offer to bribe any government official, any political party or official thereof, or any candidate for political office, for the purpose of influencing any action or decision of such person in their official capacity or any governmental authority of any jurisdiction.<br><br>

                4.2 If any part of the Goods or Services is not in accordance with this Condition 4 or as per BASILLUS's satisfaction, BASILLUS may by a written notice to the Vendor return or reject all or part of the Goods and Services at the cost and expense of the Vendor.<br><br>
                4.3 BASILLUS or its duly authorized representative shall, with reasonable notice, have reasonable access to the Vendor's works and full cooperation to assess standards during manufacture.<br><br>

                <strong>5. Guarantee.</strong><br>
                5.1 The Vendor shall at its own cost promptly remedy (by, at BASILLUS's option, repair, replacement, modification or refund of the full purchase price) any defects in Goods notified by BASILLUS and which become apparent within 15 months from the date of Installation and 18 months from the date of supply, whichever is earlier, against any manufacturing defect (or such period as may be agreed in writing) from delivery (in case of Goods) or completion (in case of services), due to:<br>
                (a) Poor or defective workmanship or materials.<br>
                (b) Faulty design, other than a design made or furnished or specified by BASILLUS and for which the Vendor has previously disclaimed responsibility in writing within a reasonable time of receipt; or<br>
                (c) Any act, neglect, or omission by the Vendor.<br><br>

                5.2 The Vendor shall:<br>
                (a) Ensure that where the manufacturer provides a warranty along with the Goods, then the same shall be passed on to BASILLUS as was provided by the manufacturer; and<br>
                (b) Ensure that any remedied part of Goods is compatible with all Goods; and<br>
                (c) Complete the remedy to the satisfaction of BASILLUS within the timescales specified in the Order (or, if none are specified, within a reasonable time); and<br>
                (d) Ensure that defective Goods are not remedied on BASILLUS premises without BASILLUS's consent, unless, for operational or technical reasons they can only be removed or replaced with difficulty; and<br>
                (e) Cause the minimum of disruption to BASILLUS and/or its customers in effecting any remedy. The time at which any remedy is to be effected shall be agreed with BASILLUS, and BASILLUS may, at its discretion, direct the Vendor to work outside normal working hours at no cost to BASILLUS.<br><br>

                5.3 All repaired or replaced Goods shall benefit from the provisions of this Condition, and a new guarantee period shall apply to them from their respective date of delivery to BASILLUS. Carriage charges for the return of the faulty items will be charged to the Vendor.<br><br>

                5.4 This Condition shall survive the Order.<br><br>

                <strong>6. Damage or loss in transit.</strong><br>
                6.1 Subject to the terms of the Order, the Vendor undertakes at its own expense to repair or replace (at the option of BASILLUS) Goods lost or damaged in transit, and delivery will not be deemed to have taken place until replacement or repaired items have been delivered to the satisfaction of BASILLUS. The Vendor shall procure and maintain adequate insurance for the Goods while in transit.<br><br>

                <strong>7. Ownership and risk.</strong><br>
                7.1 Subject to the terms of delivery and without prejudice to BASILLUS's other rights under the Conditions of the Order:<br>
                (a) Ownership in the Goods shall pass to BASILLUS on delivery.<br>
                (b) Risk in the Goods shall pass to BASILLUS on delivery, whereas the Order includes installation, in which case risk shall not pass to BASILLUS until completion of the installation work.<br><br>

                <strong>8. Price.</strong><br>
                8.1 The price(s) payable by BASILLUS for Goods and Services, unless otherwise expressly stated in the Order, shall be inclusive, where relevant, of all packing, delivery to Site, off-loading, any license fees, use of any intellectual property for purposes of delivering the Order, installation, testing and commissioning, and all other charges associated with Supplies, GST is included in rates quoted and also included intra-site shifting and resifting whenever required - after off-loading.<br><br>

                <strong>9. Invoice payment.</strong><br>
                9.1 Subject to the terms of the Order, the Vendor shall, following supply of all or (where agreed by BASILLUS in writing) each accepted (not rejected in accordance with Condition 4) installment of the Goods or Services, submit an invoice, within 10 days, for the price of the Goods and Services supplied in accordance with the Order, the Order number shown overleaf, and any other particulars prescribed in the Order and shall be sent to the address specified in the Order.<br><br>

                9.2 Payment of an undisputed invoice submitted in accordance with this Condition shall be made in an average of 21 calendar days from the date of the invoice.<br><br>

                9.3 BASILLUS reserves the right to refuse payment of any invoice which is not submitted in accordance with the Order. In such a situation, the Vendor shall correct the invoice and submit it to BASILLUS within 15 days of notification or error by BASILLUS.<br><br>

                9.4 BASILLUS shall be entitled to deduct any payments and accrued liquidated or other damages against the Vendor's invoices. Furthermore, any amounts owed by the Vendor to BASILLUS may be set-off against the Vendor's invoices.<br><br>

                <strong>10. BASILLUS's Property.</strong><br>
                10.1 Unless otherwise agreed in writing, all tools, equipment, or materials of every description, if any, furnished to the Vendor by BASILLUS or specifically paid for by BASILLUS and any replacement thereof, or any materials affixed or attached thereto, shall be and shall remain the sole property of BASILLUS. Such property:<br>
                (a) Shall be clearly marked “Property of BASILLUS.”<br>
                (b) Shall not be used except in performing BASILLUS's Orders.<br>
                (c) Shall be held at Vendor's risk, and<br>
                (d) Shall be delivered without costs to BASILLUS promptly at its written request.<br><br>

                10.2 Whenever applicable, the Vendor shall supply BASILLUS with an inventory of such property quarterly. Any specification, drawings, sketches, models, samples, tools, technical information or data, and any other confidential or proprietary information, written, oral or otherwise (all hereinafter designated “information”) furnished to Vendor hereunder or in contemplation hereof shall remain BASILLUS's property. All copies of such information in written, graphic, or other tangible form shall be immediately returned to BASILLUS without cost upon its request. The information shall be kept confidential by the Vendor, employing the same security precautions as it takes to safeguard its own confidential information.<br><br>

                <strong>11. Drawings.</strong><br>
                11.1 BASILLUS's review and approval of drawings submitted by the Vendor will cover only general conformity to the specifications. Such approval will not constitute approval of any dimensions, quantities, or details of the material shown by such drawings and shall not relieve the Vendor of its responsibility for meeting all specifications of this Order or as may have been specified by BASILLUS in any other document. BASILLUS retains rights of final approval for all finished products.<br><br>

                <strong>12. Inspection.</strong><br>
                12.1 The Vendor shall be solely responsible for the inspection of all Goods and Services, whether supplied by Vendor or any approved sub-Vendor, and shall ensure that Goods and Services conform in every respect to this Order and that Goods accord with good design, engineering, and manufacturing practices. If any inspection or test of the Goods/Services is required by laws, ordinances, or public activity, the Vendor shall promptly have such inspection or test performed pursuant to such laws, ordinance, or public authority at Vendor's expense.<br><br>

                12.2 BASILLUS shall have the right to inspect or test any of the Goods/Services whenever deemed necessary for conformance to this Order. Inspection or failure to inspect by BASILLUS shall not relieve the Vendor from its responsibilities or liabilities under this Order, nor be interpreted in any way as implying acceptance of such Goods/Services. On request by BASILLUS, the Vendor shall provide all test certificates and documents relating to quality and performance and carry out further tests as BASILLUS may request.<br><br>

                12.3 The Vendor shall advise BASILLUS in writing immediately upon becoming aware of any defect or deficiency in the Goods/Services during execution of this Order and shall not modify the Goods/Services unless and until instructed to do so in writing by BASILLUS.<br><br>

                12.4 Unless otherwise directed by BASILLUS, the Vendor shall not delay the fabrication or manufacturing of Goods/Services pending inspection by BASILLUS.<br><br>

                12.5 BASILLUS may reject any Goods/Services which fail in any way to conform to this Order. Any rejection of Goods/Services or parts thereof by BASILLUS shall be final, and any act of the Vendor with respect to delivery or provision of non-conforming Goods/Services shall constitute a breach by Vendor of this Order as a whole.<br><br>

                <strong>13. Cancellation.</strong><br>
                BASILLUS may cancel this Order at any time for any reason whatsoever by giving the Vendor seven days' written notice of cancellation. In the event BASILLUS cancels the Order, BASILLUS shall reimburse the Vendor for all its direct costs reasonably incurred, at actuals, in performing the Order up to the date of cancellation, including unavoidable cancellation charges from its Vendors and sub-Vendors. BASILLUS shall, however, be credited by the Vendor for the realizable value of the Goods and works and materials appropriated to this Order at the cancellation date in reduction of such repayment costs. Alternatively, at BASILLUS's option, the Vendor shall deliver/perform the Goods/Services to the Purchaser. The Vendor shall not, however, be entitled to any sums in respect of profit. If BASILLUS requests the Vendor to give details of its cancellation costs prior to such proposed cancellation, then BASILLUS shall review and agree on the quantum of such costs. In the absence of agreement, the parties may refer such to arbitration in accordance with this Order. The same is to be done after mutual discussion only.<br><br>

                <strong>14. Work on BASILLUS's premises.</strong><br>
                14.1 If the Vendor's performance under this Order involves operations by the Vendor on the premises of the Purchaser, the Vendor shall comply with all applicable provisions of central, state, and local laws and regulations and shall take all necessary precautions to prevent occurrence of any injury to persons or property during the progress of such performances.<br><br>

                14.2 Except to the extent that any such injury is attributable solely and directly to BASILLUS's gross negligence, the Vendor shall indemnify BASILLUS against all loss and damage which may result from any act or omission of the Vendor, its agents, employees, or sub-Vendors.<br><br>

                14.3 The Vendor shall maintain adequate insurance, including but not limited to public liability property damage and employees liability insurance policies, to protect BASILLUS from such risks and from any claims under any applicable central, state, and local laws and regulations.<br><br>

                <strong>15. Confidentiality.</strong><br>
                15.1 The Vendor shall keep confidential all information belonging to, or held by, BASILLUS which may come into the Vendor's possession while the Order is placed and all obligations of the parties are discharged to the fullest ('The Confidential Information') and shall not without the prior written consent of BASILLUS divulge the existence of the Order or disclose any of the Confidential Information to a third party or use the Confidential Information for any purpose, other than is necessary for performance of its obligations under the terms and conditions of the Order.<br><br>

                15.2 The above provisions of this Condition shall not apply to:<br>
                (a) Information which is in the public domain/published otherwise than through a breach of this Condition; or<br>
                (b) Information lawfully known to the Vendor prior to disclosure hereunder and not the subject of any other obligation of confidentiality; or<br>
                (c) Information obtained from a third party who is free to disclose the same; and<br>
                (d) Information required to be disclosed by applicable law or in relation to any regulatory permission, governmental body or regulatory body, provided that the Vendor uses all reasonable endeavors to ensure that the party receiving the Confidential Information maintains the information in the strictest of confidence and does not use it except for the purposes for which the disclosure is made.<br><br>

                15.3 The Vendor shall ensure that any sub-Vendor used in relation to this Order is bound by confidentiality provisions on similar terms to this Condition in relation to information belonging to, or held by, BASILLUS.<br><br>

                15.4 This Condition shall survive the Order.<br><br>

                <strong>16. Intellectual property.</strong><br>
                16.1 Neither the Vendor nor BASILLUS acquires any rights to the other's patents, copyrights, or other intellectual property under this Order.<br><br>

                16.2 Without prejudice to any other rights or remedies available to BASILLUS, the Vendor warrants that neither the Services nor any of the Goods infringe any intellectual property rights (including, without limitation, patents, copyright, registered designs, and design rights) and undertakes to indemnify BASILLUS against any claims in respect of any such infringement or alleged infringement.<br><br>

                16.3 This condition shall survive the Order.<br><br>
                <strong>17. Indemnity.</strong><br>
                17.1 Without prejudice to any other rights or remedies available to BASILLUS, the Vendor shall indemnify BASILLUS against all claims, liability, demands, proceedings, costs, and expenses arising as a result of the negligence or willful acts or omissions of the Vendor, its employees, agents, or sub-Vendors (or their employees or agents) in respect of:<br>
                (a) Loss of or damage to any property; or<br>
                (b) Death or personal injury of any person,<br>
                While performing or purporting to perform the Conditions of this Order, except to the extent such loss, damage, death, or personal injury is caused directly and solely by the gross negligence of BASILLUS.<br><br>

                <strong>18. Insurance.</strong><br>
                18.1 The Vendor shall have in force and shall maintain (at its own cost and expense) a policy of insurance in respect of its liabilities under Condition 17, with a limit of indemnity not less than 110% of total value of goods for any one claim arising out of any one incident or event and without limit as to the number of claims during the period of insurance.<br><br>

                18.2 Upon request by BASILLUS, the Vendor shall provide reasonable satisfactory summarized evidence of the insurance cover in force.<br><br>

                <strong>19. Termination.</strong><br>
                19.1 Without prejudice to any other remedies that it may have, BASILLUS shall have the right to terminate the Order forthwith, with a written notice and to claim the excess cost of obtaining replacement goods and services if:<br>
                (a) The Vendor commits a breach of any of the Conditions and fails to remedy the breach within 15 days of receipt of a written notice by BASILLUS to make such remedies; or<br>
                (b) The Vendor becomes insolvent or ceases to trade, or compounds with its creditors or, commits an act of bankruptcy, or a bankruptcy petition or bankruptcy order is presented or made in relation to the Vendor, or the Vendor has a receiver or receiver, and manager appointed, or a petition for a management order is presented or such an order is made in relation to the Vendor, or a resolution or petition to wind up the Vendor is passed or presented (otherwise than for reconstruction or amalgamation); or<br>
                (c) The Vendor's ownership or control is materially changed to (in BASILLUS's reasonable opinion) BASILLUS's detriment.<br><br>

                <strong>20. Compliance with legislation and instructions.</strong><br>
                20.1 The Vendor shall comply with all applicable legislation including and without prejudice to the generality thereof the provisions of any relevant occupational Health and Safety Acts and any modifications thereof plus any homologation requirements and any other applicable regulation or By-Law of any Local or other Authority as well as any BASILLUS site regulations that may be notified to the Vendor.<br><br>

                20.2 BASILLUS shall bear no liability for claims arising due to the Vendor's non-compliance with legislation and BASILLUS's site regulations.<br><br>

                20.3 The Vendor indemnifies BASILLUS against all claims, actions, costs, damages, and proceedings arising out of the Vendor's obligations under this Condition 20.<br><br>

                20.4 This condition shall survive the Order.<br><br>

                <strong>21. General.</strong><br>
                21.1 The terms of the Order are in addition to and shall not be deemed to prejudice or affect any terms or rights implied by or available under statute or common law. Otherwise, the Order forms overleaf and these Conditions set out the entire Order between BASILLUS and the Vendor.<br><br>

                21.2 No variation to the Order shall have any effect unless agreed in writing by duly authorized representatives of BASILLUS and the Vendor, which shall not be unreasonably withheld.<br><br>

                21.3 The headings in these Conditions are for ease of reference only and shall not affect their interpretation or construction of this Order.<br><br>

                21.4 If any Condition of this Order is held to be void, illegal, unenforceable, or inconsistent, then such Condition (so far as it is invalid or unenforceable) shall be severable to the Condition of the Order and not given effect to and deemed to be severable to this Order without invalidating any of the remaining provisions of this Order.<br><br>

                21.5 If the Order form overleaf is used to place orders against a Contract which already exists between BASILLUS and the Vendor, then the provisions of that contract shall apply and shall prevail over these Conditions to the extent of inconsistency between the two contracts.<br><br>

                <strong>22. Dispute Resolution.</strong><br>
                22.1 BASILLUS and the Vendor will work together in good faith to amicably resolve any dispute or differences arising out of or related to the subject matter of this Order or their relationship thereto. Failing which, both the parties shall be entitled to refer such dispute or differences to binding arbitration in accordance with the Arbitration and Conciliation Act, 1996. The arbitration shall be conducted by an arbitration tribunal consisting of three (03) arbitrators. BASILLUS and the Vendor shall appoint one (01) arbitrator each, and the third arbitrator shall be appointed by mutual agreement between the two arbitrators so appointed. The venue of the arbitration proceedings shall be Goa and the proceedings shall be conducted in English. The cost of arbitration shall be borne by the losing party as arrived at after the conclusion of the proceedings.<br><br>

                <strong>23. Governing Law and Jurisdiction.</strong><br>
                23.1 This Order and the rights and obligations of BASILLUS and the Vendor under or arising out of this Order shall be governed and construed in accordance with the laws of India.<br><br>

                23.2 Subject to Clause 22, the Purchaser and the Vendor irrevocably submit to the exclusive jurisdiction of any competent Court situated at Goa on all matters arising out of, concerning to, or related with this Order and waive any objection to such proceedings having been brought in an inconvenient forum.<br><br>

                <strong>24. Water & Electricity.</strong><br>
                BASILLUS Shall provide the Water and Electricity free of cost for executing this order at one point. Vendor shall arrange the further distribution/ extension at their own cost.<br>

                ";
                }


                ?>
                <?php echo render_textarea('terms', 'terms_and_conditions', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                <div id="vendor_data">

                </div>

                <div class="btn-bottom-toolbar text-right">

                  <button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit">
                    <?php echo _l('submit'); ?>
                  </button>
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
<?php init_tail(); ?>
</body>

</html>

<script type="text/javascript">
  var convert_po = '<?php echo $convert_po; ?>';
  if (convert_po) {
    $('#project').attr('disabled', true);
    $('#pur_request').attr('disabled', true);
    $('#project_val').css('display', 'block');
    $('#project_val').val($('#project').val());
  } else {
    $('#project').attr('disabled', false);
    $('#pur_request').attr('disabled', false);
    $('#project_val').remove();
  }


  var pur_request = $('select[name="pur_request"]').val();
  var vendor = $('select[name="vendor"]').val();
  if (pur_request != '') {
    $.post(admin_url + 'purchase/coppy_pur_request_for_po/' + pur_request + '/' + vendor).done(function(response) {
      response = JSON.parse(response);
      if (response) {
        // $('select[name="estimate"]').html(response.estimate_html);
        // $('select[name="estimate"]').selectpicker('refresh');

        $('select[name="currency"]').val(response.currency).change();
        $('input[name="currency_rate"]').val(response.currency_rate).change();

        // $('.invoice-item table.invoice-items-table.items tbody').html('');
        // $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

        setTimeout(function() {
          pur_calculate_total();
        }, 15);

        init_selectpicker();
        pur_reorder_items('.invoice-item');
        pur_clear_item_preview_values('.invoice-item');
        $('body').find('#items-warning').remove();
        $("body").find('.dt-loader').remove();
        $('#item_select').selectpicker('val', '');
      }
    });
  }
</script>
<?php require 'modules/purchase/assets/js/import_excel_items_pur_order_js.php'; ?>
<?php require 'modules/purchase/assets/js/pur_order_js.php'; ?>
<script>
  $(document).ready(function() {
    "use strict";

    // Initialize item select input logic
    initItemSelect();
  });
  /**
   * Initializes the logic for handling item selection and input events.
   */
  function initItemSelect() {
    // Listen for input events on the search box of specific dropdowns
    $(document).on('input', '.item-select  .bs-searchbox input', function() {
      let query = $(this).val(); // Get the user's query
      let $bootstrapSelect = $(this).closest('.bootstrap-select'); // Get the parent bootstrap-select wrapper
      let $selectElement = $bootstrapSelect.find('select.item-select'); // Get the associated select element

      // console.log("Target Select Element:", $selectElement); // Debug the target <select> element

      if (query.length >= 3) {
        fetchItems(query, $selectElement); // Fetch items dynamically
      }
    });

    // Handle the change event for the item-select dropdown
    $(document).on('change', '.item-select', function() {
      handleItemChange($(this)); // Handle item selection change
    });
  }

  /**
   * Fetches items dynamically based on the search query and populates the target select element.
   * @param {string} query - The search query entered by the user.
   * @param {jQuery} $selectElement - The select element to populate.
   */

  function fetchItems(query, $selectElement) {
    var admin_url = '<?php echo admin_url(); ?>';
    $.ajax({
      url: admin_url + 'purchase/fetch_items', // Controller method URL
      type: 'GET',
      data: {
        search: query
      },
      success: function(data) {
        // console.log("Raw Response Data:", data); // Debug the raw data

        try {
          let items = JSON.parse(data); // Parse JSON response
          // console.log("Parsed Items:", items); // Debug parsed items

          if ($selectElement.length === 0) {
            console.error("Target select element not found.");
            return;
          }

          // Clear existing options in the specific select element
          $selectElement.empty();

          // Add default "Type to search..." option
          $selectElement.append('<option value="">Type to search...</option>');

          // Get the pre-selected ID if available (from a data attribute or a hidden field)
          let preSelectedId = $selectElement.data('selected-id') || null;

          // Populate the specific select element with new options
          items.forEach(function(item) {
            let isSelected = preSelectedId && item.id === preSelectedId ? 'selected' : '';
            let option = `<option  data-commodity-code="${item.id}" value="${item.id}"> ${item.commodity_code} ${item.description}</option>`;
            // console.log("Appending Option:", option); // Debug each option
            $selectElement.append(option);
          });

          // Refresh the selectpicker to reflect changes
          $selectElement.selectpicker('refresh');

          // console.log("Updated Select Element HTML:", $selectElement.html()); // Debug the final HTML
        } catch (error) {
          console.error("Error Processing Response:", error);
        }
      },
      error: function() {
        console.error('Failed to fetch items.');
      }
    });
  }

  /**
   * Handles the change event for the item-select dropdown.
   * @param {jQuery} $selectElement - The select element that triggered the change.
   */
  function handleItemChange($selectElement) {
    let selectedId = $selectElement.val(); // Get the selected item's ID
    let selectedCommodityCode = $selectElement.find(':selected').data('commodity-code'); // Get the commodity code
    let $inputField = $selectElement.closest('tr').find('input[name="item_code"]'); // Find the associated input field

    if ($inputField.length > 0) {
      $inputField.val(selectedCommodityCode || ''); // Update the input field with the commodity code
      // console.log("Updated Input Field:", $inputField, "Value:", selectedCommodityCode); // Debug input field
    }
  }
  $(document).ready(function() {
    // Attach click handler to the submit button
    $('.save_detail').on('click', function(e) {
      let isValid = true; // Track overall validation state

      // Target only `select` elements with the `item-select` class
      $('select.item-select').each(function(index) {
        if (index === 0) return; // Skip the first element
        let $this = $(this);
        let value = $this.val() || $this.data('selected-id'); // Use value or fallback to data-selected-id

        // console.log(`Validating select with id: ${$this.attr('id')}, value: ${value}`); // Debugging

        // Check if the value is empty or null
        if (!value || value.trim() === '') {
          isValid = false; // Mark as invalid

          // Add error message and class if not already added
          if (!$this.next('.error-message').length) {
            $this.after('<span class="error-message" style="color: red;">This field is required.</span>');
          }
          $this.addClass('error-border'); // Highlight the invalid field
          $this.addClass('error-border'); // Highlight the Bootstrap select wrapper
        } else {
          // If valid, remove any error messages or classes
          $this.siblings('.error-message').remove();
          $this.removeClass('error-border');
          $this.closest('.bootstrap-select').removeClass('error-border');
        }
      });

      // Prevent form submission if validation fails
      if (!isValid) {
        // console.log('Form validation failed.'); // Debugging
        // e.preventDefault(); // Explicitly prevent form submission
        return false;
      }

      // If all validations pass
      // console.log('Form validation passed.');
    });


  });
</script>