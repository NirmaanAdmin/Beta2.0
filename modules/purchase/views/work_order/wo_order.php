<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
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
</style>
<div id="wrapper">
  <div class="content">
    <div class="loader-container hide" id="loader-container">
      <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
    </div>
    <div class="row">
      <?php
      echo form_open_multipart($this->uri->uri_string(), array('id' => 'wo_order-form', 'class' => '_transaction_form'));
      if (isset($wo_order)) {
        echo form_hidden('isedit');
      }
      ?>
      <?php
      if ($book_order) {
        $prefix = get_purchase_option('wo_order_prefix');
        $next_number = get_purchase_option('next_wo_number');
        $wo_order_number = $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('M-Y');
        if (get_option('po_only_prefix_and_number') == 1) {
          $wo_order_number = $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT);
        }
        $wo_order['wo_order_name'] = $cost_package_detail->package_name;
        $wo_order['wo_order_number'] = $wo_order_number;
        $wo_order['number'] = $next_number;
        $wo_order['estimate'] = $cost_package_detail->estimate_id;
        $wo_order['group_pur'] = $cost_package_detail->budget_head;
        $wo_order['project'] = $cost_package_detail->project_id;
        $wo_order['kind'] = $cost_package_detail->kind;
        $wo_order['budget'] = $cost_package_detail->total_package;
        $wo_order['order_date'] = _d(date('Y-m-d'));
        $wo_order['buyer'] = get_staff_user_id();
        $wo_order = (object) $wo_order;
        echo form_hidden('package_id', $cost_package_detail->id);
      }
      if ($book_order_tender) {
        $prefix = get_purchase_option('wo_order_prefix');
        $next_number = get_purchase_option('next_wo_number');
        $wo_order_number = $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('M-Y');
        if (get_option('po_only_prefix_and_number') == 1) {
          $wo_order_number = $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT);
        }
        $wo_order['wo_order_name'] = $pur_tender_data->pur_tn_name;
        $wo_order['wo_order_number'] = $wo_order_number;
        $wo_order['number'] = $next_number;
        $wo_order['estimate'] = $pur_tender_data->estimate_id;
        $wo_order['group_pur'] = $pur_tender_data->group_pur;
        $wo_order['project'] = $pur_tender_data->project;
        $wo_order['order_date'] = _d(date('Y-m-d'));
        $wo_order['buyer'] = get_staff_user_id();
        $wo_order = (object) $wo_order;
        echo form_hidden('tender_id', $pur_tender_data->id);
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
                  if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'wo_order', 'active' => 1)) > 0) {
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
                        <?php $wo_order_name = (isset($wo_order) ? $wo_order->wo_order_name : '');
                        echo render_input('wo_order_name', 'wo_order_description', $wo_order_name); ?>

                      </div>
                      <div class="col-md-6 form-group">
                        <?php $prefix = get_purchase_option('wo_order_prefix');
                        $next_number = get_purchase_option('next_wo_number');

                        $wo_order_number = (isset($wo_order) ? $wo_order->wo_order_number : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('M-Y'));
                        if (get_option('po_only_prefix_and_number') == 1) {
                          $wo_order_number = (isset($wo_order) ? $wo_order->wo_order_number : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT));
                        }


                        $number = (isset($wo_order) ? $wo_order->number : $next_number);
                        echo form_hidden('number', $number); ?>

                        <label for="wo_order_number"><?php echo _l('wo_order_number'); ?></label>

                        <input type="text" readonly class="form-control" name="wo_order_number" value="<?php echo pur_html_entity_decode($wo_order_number); ?>">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="vendor"><?php echo _l('vendor'); ?></label>
                        <select name="vendor" id="vendor" class="selectpicker" <?php if (isset($wo_order)) {
                          echo '';
                        } ?> onchange="estimate_by_vendor(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php foreach ($vendors as $s) { ?>
                          <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if (isset($wo_order) && $wo_order->vendor == $s['userid']) {
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
                        $wo_order['pur_request'] = $selected_pr;
                        $wo_order['project'] = $selected_project;
                        $wo_order = (object) $wo_order;
                      }
                      ?>
                      <div class="col-md-6 form-group">
                        <label for="pur_request"><?php echo _l('pur_request'); ?></label>
                        <select name="pur_request" id="pur_request" class="selectpicker" onchange="coppy_pur_request(); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($pur_request as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($wo_order) && $wo_order->pur_request != '' && $wo_order->pur_request == $s['id']) {
                              echo 'selected';
                            } ?>><?php echo pur_html_entity_decode($s['pur_rq_code'] . ' - ' . $s['pur_rq_name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="budget"><?php echo _l('budget'); ?></label>
                        <select name="estimate" id="estimate" class="selectpicker  <?php if (isset($wo_order)) { echo 'disabled';} ?>" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value=""></option>
                            <?php foreach ($budgets as $s) { ?>
                              <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($wo_order) && $wo_order->estimate != '' && $wo_order->estimate == $s['id']) { echo 'selected';} ?>>
                              <?php echo format_estimate_number($s['id']); ?>
                              <?php echo !empty($s['budget_description']) ? ' - '.$s['budget_description'] : ''; ?>
                              </option>
                            <?php } ?>
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="department"><?php echo _l('department'); ?></label>
                        <select name="department" id="department" class="selectpicker" <?php if (isset($wo_order)) {
                          
                        } ?> data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php foreach ($departments as $s) { ?>
                          <option value="<?php echo pur_html_entity_decode($s['departmentid']); ?>" <?php if (isset($wo_order) && $s['departmentid'] == $wo_order->department) {
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
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($wo_order) && $s['id'] == $wo_order->project) {
                              echo 'selected';
                            } else if (!isset($wo_order) && $s['id'] == $project_id) {
                              echo 'selected';
                            } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <div class="col-md-6 form-group">
                        <label for="type"><?php echo _l('type'); ?></label>
                        <select name="type" id="type" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="capex" <?php if (isset($wo_order) && $wo_order->type == 'capex') {
                            echo 'selected';
                          } ?>><?php echo _l('capex'); ?></option>
                          <option value="opex" <?php if (isset($wo_order) && $wo_order->type == 'opex') {
                            echo 'selected';
                          } ?>><?php echo _l('opex'); ?></option>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12 form-group">
                        <div id="inputTagsWrapper">
                          <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                          <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($wo_order) ? prep_tags_input(get_tags_in($wo_order->id, 'wo_order')) : ''); ?>" data-role="tagsinput">
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
                          if (isset($wo_order) && $wo_order->currency != 0) {
                            if ($currency['id'] == $wo_order->currency) {
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
                          if (isset($wo_order)) {
                            if ($wo_order->delivery_person == $member['staffid']) {
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
                        <?php $order_date = (isset($wo_order) ? _d($wo_order->order_date) : _d(date('Y-m-d')));
                        echo render_date_input('order_date', 'order_date', $order_date); ?>
                      </div>
                    </div>
                    <div class="row">


                      <div class="col-md-6 ">
                        <?php
                        $selected = '';
                        foreach ($staff as $member) {
                          if (isset($wo_order)) {
                            if ($wo_order->buyer == $member['staffid']) {
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
                          if (isset($wo_order)) {
                            if ($wo_order->discount_type == 'before_tax' || $wo_order->discount_type == null) {
                              echo 'selected';
                            }
                          } else {
                            echo 'selected';
                          } ?>><?php echo _l('discount_type_before_tax'); ?></option>
                          <option value="after_tax" <?php if (isset($wo_order)) {
                            if ($wo_order->discount_type == 'after_tax') {
                              echo 'selected';
                            }
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
                          if (isset($wo_order)) {
                            if ($wo_order->group_pur == $group['id']) {
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
                        <?php $budget = (isset($wo_order) ? $wo_order->budget : '');
                        echo render_input('budget', 'budget_ro_projection', $budget, 'number'); ?>
                      </div>
                      <!-- <div class="col-md-6 ">

                        <?php

                        $selected = '';
                        foreach ($sub_groups_pur as $sub_group) {
                          if (isset($wo_order)) {
                            if ($wo_order->sub_groups_pur == $sub_group['id']) {
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
                      <div class="col-md-6 ">
                        <label for="hsn_sac" class="control-label"><?php echo _l('hsn_sac') ?></label>
                        <select name="hsn_sac" id="hsn_sac" class="selectpicker" data-live-search="true" data-width="100%">
                          <option value=""></option>
                          <?php foreach ($get_hsn_sac_code as $item): ?>
                            <?php
                            $selected = '';
                            if (isset($wo_order)) {
                              if ($wo_order->hsn_sac == $item['id']) {
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

                    <!-- <div class="row">
                      <div class="col-md-6 ">
                        <?php $days_owed = (isset($wo_order) ? $wo_order->days_owed : '');
                        echo render_input('days_owed', 'days_owed', $days_owed, 'number'); ?>
                      </div>
                      <div class="col-md-6 ">
                        <?php $delivery_date = (isset($wo_order) ? _d($wo_order->delivery_date) : '');
                        echo render_date_input('delivery_date', 'delivery_date', $delivery_date); ?>
                      </div>

                    </div> -->

                    <!-- <div class="row">
                      <div class="col-md-6 ">

                        <?php

                        $selected = '';
                        foreach ($area_pur as $area) {
                          if (isset($wo_order)) {
                            if ($wo_order->area_pur == $area['id']) {
                              $selected = $area['id'];
                            }
                          }
                          if (isset($selected_area)) {
                            if ($selected_area == $area['id']) {
                              $selected = $area['id'];
                            }
                          }
                        }
                        echo render_select('area_pur', $area_pur, array('id', 'area_name'), 'Area', $selected);
                        ?>
                      </div>
                    </div> -->

                  </div>


                </div>

                <?php if ($customer_custom_fields) { ?>

                  <?php $rel_id = (isset($wo_order) ? $wo_order->id : false); ?>
                  <?php echo render_custom_fields('wo_order', $rel_id); ?>

                <?php } ?>
              </div>

              <div role="tabpanel" class="tab-pane" id="shipping_infor">
                <div class="row">
                  <div class="col-md-6">
                    <?php $shipping_address = isset($wo_order) ? $wo_order->shipping_address : get_option('pur_company_address', true);
                    if ($shipping_address == '') {
                      $shipping_address = get_option('pur_company_address', true);
                    }

                    echo render_textarea('shipping_address', 'pur_company_address', $shipping_address, ['rows' => 7]); ?>

                    <?php $shipping_zip = isset($wo_order) ? $wo_order->shipping_zip : get_option('pur_company_zipcode', true);
                    if ($shipping_zip == '') {
                      $shipping_zip = get_option('pur_company_zipcode', true);
                    }
                    echo render_input('shipping_zip', 'pur_company_zipcode', $shipping_zip, 'text'); ?>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-12">
                        <?php $shipping_city = isset($wo_order) ? $wo_order->shipping_city : get_option('pur_company_city', true);
                        if ($shipping_city == '') {
                          $shipping_city = get_option('pur_company_city', true);
                        }
                        echo render_input('shipping_city', 'pur_company_city', $shipping_city, 'text'); ?>
                      </div>
                      <div class="col-md-12">
                        <?php $shipping_state = isset($wo_order) ? $wo_order->shipping_state : get_option('pur_company_state', true);
                        if ($shipping_state == '') {
                          $shipping_state = get_option('pur_company_state', true);
                        }
                        echo render_input('shipping_state', 'pur_company_state', $shipping_state, 'text'); ?>
                      </div>

                      <div class="col-md-12">
                        <?php $shipping_country_text = isset($wo_order) ? $wo_order->shipping_country_text : get_option('pur_company_country_text', true);
                        if ($shipping_country_text == '') {
                          $shipping_country_text = get_option('pur_company_country_text', true);
                        }
                        echo render_input('shipping_country_text', 'pur_company_country_text', $shipping_country_text, 'text'); ?>
                      </div>

                      <div class="col-md-12">
                        <?php $countries = get_all_countries();
                        $pur_company_country_code = get_option('pur_company_country_code', true);
                        $selected = isset($wo_order) ? $wo_order->shipping_country : $pur_company_country_code;
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
                $path = get_upload_path_by_type('purchase') . 'wo_order/' . $value['rel_id'] . '/' . $value['file_name'];

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
                echo '<a href="' . admin_url('purchase/delete_work_order_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
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
                    <?php echo form_open_multipart(admin_url('purchase/import_file_xlsx_wo_order_items'), array('id' => 'import_form')); ?>
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
              if (isset($wo_order) && $wo_order->currency != 0) {
                $po_currency = pur_get_currency_by_id($wo_order->currency);
              }

              $from_currency = (isset($wo_order) && $wo_order->from_currency != null) ? $wo_order->from_currency : $base_currency->id;
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
                  if (isset($wo_order) && $wo_order->currency != 0) {
                    $currency_rate = pur_get_currency_rate($base_currency->name, $po_currency->name);
                  }
                  echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right');
                  ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive s_table ">
                  <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                    <thead>
                      <tr>
                        <th></th>
                        <th align="left" style="min-width: 76px"><?php echo _l('serial_no'); ?></th>
                        <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> Uniclass code</th>
                        <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
                        <th align="left"><?php echo _l('sub_groups_pur'); ?></th>
                        <th width="10%" align="right"><?php echo _l('area'); ?></th>
                        <th align="right"><?php echo _l('Image'); ?></th>
                        <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
                        <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>

                        <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                        <!-- <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th> -->
                        <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>

                        <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo $wo_order_row_template; ?>
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
                            <?php $discount_total = isset($wo_order) ? $wo_order->discount_total : '';
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
                            <?php $discount_remarks = isset($wo_order) ? $wo_order->discount_remarks : '';
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
                            <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if (isset($wo_order)) {
                              echo $wo_order->shipping_fee;
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
            unset($wo_order);
          }

          if ($book_order_tender) {
            unset($wo_order);
          }
          
          ?>
          <div class="row">
            <div class="col-md-12 mtop15">
              <div class="panel-body bottom-transaction">
                <?php $value = (isset($wo_order) ? $wo_order->order_summary : get_purchase_option('order_summary'));
                if (!isset($wo_order) && $wo_order->order_summary == '') {
                  $value = get_by_deafult_order_summary();
                }

                ?>


                <?php echo render_textarea('order_summary', 'estimate_add_edit_order_summary', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                <?php $value = (isset($wo_order) ? $wo_order->vendornote : get_purchase_option('vendor_note')); ?>
                <?php echo render_textarea('vendornote', 'estimate_add_edit_vendor_note', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                <?php $value = (isset($wo_order) ? $wo_order->terms :  get_purchase_option('terms_and_conditions')); ?>
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

        $('.invoice-item table.invoice-items-table.items tbody').html('');
        $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

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
<?php require 'modules/purchase/assets/js/import_excel_items_wo_order_js.php'; ?>
<?php require 'modules/purchase/assets/js/wo_order_js.php'; ?>
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