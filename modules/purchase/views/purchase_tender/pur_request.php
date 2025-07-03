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
</style>
<div id="wrapper">
  <div class="content">
    <div class="loader-container hide" id="loader-container">
      <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
    </div>
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

                      <!-- <div class="col-md-3 form-group">
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
                      </div> -->

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
                      <!-- <?php $this->load->view('purchase/item_include/main_item_select'); ?> -->
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
                          <th align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> Uniclass Code</th>
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
<?php require 'modules/purchase/assets/js/import_excel_items_pur_request_js.php'; ?>
<?php require 'modules/purchase/assets/js/pur_request_js.php'; ?>

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
      console.log("Updated Input Field:", $inputField, "Value:", selectedCommodityCode); // Debug input field
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