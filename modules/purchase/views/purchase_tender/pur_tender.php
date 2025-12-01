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

  .error-border {
    border: 1px solid red !important;
    border-radius: 4px;
  }

  .bootstrap-select.error-border .dropdown-toggle {
    border-color: red !important;
  }

  .error-message {
    display: block;
    margin-top: 5px;
    font-size: 12px;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="loader-container hide" id="loader-container">
      <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
    </div>
    <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'add_edit_pur_tender-form', 'class' => '_transaction_form')); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3">
                <h4 class=""><?php if (isset($pur_tender)) {
                                echo pur_html_entity_decode($pur_tender->pur_tn_code);
                              } else {
                                echo _l($title) . ' ' . _l('purchase_tender');
                              } ?></h4>
              </div>
              <div class="col-md-3 form-group">
                <!-- <label for="send_to_vendors"><?php echo _l('pur_send_to_vendors'); ?></label> -->
                <select name="send_to_vendors[]" id="send_to_vendors" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" required>
                  <?php
                  if (isset($pur_tender)) {
                    $vendors_arr = explode(',', $pur_tender->send_to_vendors ?? '');
                  }
                  ?>

                  <?php foreach ($vendors as $s) { ?>
                    <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if (isset($pur_tender) && in_array($s['userid'], $vendors_arr)) {
                                                                                          echo 'selected';
                                                                                        } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>


            <?php

            if (isset($pur_tender)) {
              echo form_hidden('isedit');
            } ?>
            <hr />
            <div class="row accounting-template">
              <div class="row ">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <?php
                    $prefix = get_purchase_option('pur_tender_prefix');
                    $next_number = get_purchase_option('next_tender_number');
                    $number = (isset($pur_tender) ? $pur_tender->number : $next_number);
                    echo form_hidden('number', $number); ?>

                    <?php $pur_tender_code = (isset($pur_tender) ? $pur_tender->pur_tn_code : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('Y'));
                    echo render_input('pur_tn_code', 'Tender Code', $pur_tender_code, 'text', array('readonly' => '')); ?>
                  </div>
                  <div class="col-md-6">
                    <?php $pur_tender_name = (isset($pur_tender) ? $pur_tender->pur_tn_name : '');
                    echo render_input('pur_tn_name', 'Tender Name', $pur_tender_name, ''); ?>
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
                        <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" disabled="true">
                          <option value=""></option>
                          <?php foreach ($projects as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_tender) && $s['id'] == $pur_tender->project) {
                                                                                              echo 'selected';
                                                                                            } else if (!isset($pur_tender) && $s['id'] == $project_id) {
                                                                                              echo 'selected';
                                                                                            } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        </select>
                        <br><br>
                      </div>
                      <div class="col-md-3 form-group">
                        <label for="requester"><?php echo _l('requester'); ?></label>
                        <select name="requester" id="requester" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" disabled="true">
                          <option value=""></option>
                          <?php foreach ($staffs as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['staffid']); ?>" <?php if (isset($pur_tender) && $s['staffid'] == $pur_tender->requester) {
                                                                                                    echo 'selected';
                                                                                                  } elseif ($s['staffid'] == get_staff_user_id()) {
                                                                                                    echo 'selected';
                                                                                                  } ?>><?php echo pur_html_entity_decode($s['lastname'] . ' ' . $s['firstname']); ?></option>
                          <?php } ?>
                        </select>
                        <br><br>
                      </div>
                      <div class="col-md-3 form-group">
                        <?php
                        $selected = '';
                        foreach ($commodity_groups_pur_tender as $group) {
                          if (isset($pur_tender)) {
                            if ($pur_tender->group_pur == $group['id']) {
                              $selected = $group['id'];
                            }
                          }
                        }
                        echo render_select('group_pur', $commodity_groups_pur_tender, array('id', 'name'), 'Budget Head', $selected);
                        ?>
                      </div>



                    </div>
                  </div>

                  <div class="col-md-12">
                    <?php $tn_description = (isset($pur_tender) ? $pur_tender->tn_description : '');
                    echo render_textarea('tn_description', 'rq_description', $tn_description); ?>
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

        <div class="row ">
          <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <div class="mtop10 invoice-item">


                  <div class="row">
                    <div class="col-md-4">
                      <!-- <?php $this->load->view('purchase/item_include/main_item_select'); ?> -->
                    </div>
                    <!-- <?php if (!$is_edit) { ?>
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
                    <?php } ?> -->
                    <?php
                    $pur_tender_currency = $base_currency;
                    if (isset($pur_tender) && $pur_tender->currency != 0) {
                      $pur_tender_currency = pur_get_currency_by_id($pur_tender->currency);
                    }

                    $from_currency = (isset($pur_tender) && $pur_tender->from_currency != null) ? $pur_tender->from_currency : $base_currency->id;
                    echo form_hidden('from_currency', $from_currency);

                    ?>
                    <div class="col-md-8 <?php if ($pur_tender_currency->id == $base_currency->id) {
                                            echo 'hide';
                                          } ?>" id="currency_rate_div">
                      <div class="col-md-10 text-right">

                        <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' (' . $base_currency->name . ' => ' . $pur_tender_currency->name . '): ';  ?></span></p>
                      </div>
                      <div class="col-md-2 pull-right">
                        <?php $currency_rate = 1;
                        if (isset($pur_tender) && $pur_tender->currency != 0) {
                          $currency_rate = pur_get_currency_rate($base_currency->name, $pur_tender_currency->name);
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
                          <th align="left"><?php echo _l('description'); ?></th>
                          <th align="left"><?php echo _l('area'); ?></th>
                          <th align="left"><?php echo _l('Image'); ?></th>
                          <th align="left" class="qty"><?php echo _l('purchase_quantity'); ?></th>
                          <th align="left" class="qty"><?php echo _l('unit_price'); ?></th>
                          <th align="left"><?php echo _l('Remarks'); ?></th>
                          <th><?php echo _l('Quotated Price'); ?></th>
                          <th align="center"><i class="fa fa-cog"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php echo pur_html_entity_decode($purchase_request_row_template); ?>
                      </tbody>
                    </table>
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
  <div class="modal fade" id="vendorWarningModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 450px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Select Vendors</h4>
        </div>

        <div class="modal-body">
          <p>Please select at least one vendor to continue:</p>

          <select id="modalVendorSelect"
            class="selectpicker"
            multiple
            data-live-search="true"
            data-width="100%">
          </select>
        </div>

        <div class="modal-footer">
          <button type="button" id="saveVendorSelection" class="btn btn-primary">
            Save Vendors
          </button>
        </div>
      </div>
    </div>
  </div>


</div>

<?php init_tail(); ?>
</body>

</html>
<?php
$is_edit_mode = isset($pur_tender);
$vendors_arr = $is_edit_mode ? explode(',', ($pur_tender->send_to_vendors ?? '')) : [];
$vendors_arr = array_filter($vendors_arr); // removes empty values

$show_vendor_modal = $is_edit_mode && empty($vendors_arr);

$vendor_list = [];
foreach ($vendors as $v) {
  $vendor_list[] = [
    'id' => $v['userid'],
    'name' => $v['company']
  ];
}
?>
<script>
  var SHOW_VENDOR_MODAL = <?php echo $show_vendor_modal ? 'true' : 'false'; ?>;
  var VENDOR_LIST = <?php echo json_encode($vendor_list); ?>;
</script>

<script>
  $(document).ready(function() {

    if (SHOW_VENDOR_MODAL) {

      // Build modal vendor list
      let html = '';
      VENDOR_LIST.forEach(v => {
        html += `<option value="${v.id}">${v.name}</option>`;
      });

      $("#modalVendorSelect").html(html);
      $("#modalVendorSelect").selectpicker('refresh');

      // Show modal
      $('#vendorWarningModal').modal({
        backdrop: 'static',
        keyboard: false
      });
    }

  });

  $(document).on('click', '#saveVendorSelection', function() {

    let selectedVendors = $("#modalVendorSelect").val();

    if (!selectedVendors || selectedVendors.length === 0) {
      alert_float('warning', 'Please select at least one vendor.');
      return;
    }

    // Apply selected vendors to the main multi-select
    $("#send_to_vendors").selectpicker('val', selectedVendors);
    $("#send_to_vendors").selectpicker('refresh');

    // Close modal
    $('#vendorWarningModal').modal('hide');
  });


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

      // Validate send_to_vendors select
      let $vendorsSelect = $('#send_to_vendors');
      let vendorsValue = $vendorsSelect.val();

      if (!vendorsValue || vendorsValue.length === 0) {
        isValid = false;

        // Add error message and class if not already added
        if (!$vendorsSelect.next('.error-message').length) {
          $vendorsSelect.after('<span class="error-message" style="color: red;">Please select at least one vendor.</span>');
        }
        $vendorsSelect.addClass('error-border');
        $vendorsSelect.closest('.bootstrap-select').addClass('error-border');
      } else {
        // If valid, remove any error messages or classes
        $vendorsSelect.siblings('.error-message').remove();
        $vendorsSelect.removeClass('error-border');
        $vendorsSelect.closest('.bootstrap-select').removeClass('error-border');
      }

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
          $this.closest('.bootstrap-select').addClass('error-border'); // Highlight the Bootstrap select wrapper
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
        e.preventDefault(); // Explicitly prevent form submission
        return false;
      }

      // If all validations pass
      // console.log('Form validation passed.');
    });
  });

  function pur_add_item_to_table(data, itemid) {
    "use strict";

    data = typeof(data) == 'undefined' || data == 'undefined' ? pur_get_item_preview_values() : data;
    // if (data.warehouse_id == "" || data.quantities == "" || data.commodity_code == "" ) {
    //   if(data.warehouse_id == ""){
    //     alert_float('warning', '<?php echo _l('please_select_a_warehouse') ?>');
    //   }
    //   return;
    // }
    if (data.item_name == "" || data.item_code == "") {
      alert_float('warning', "Please select item");
      return;
    }
    var currency_rate = $('input[name="currency_rate"]').val();
    var to_currency = $('select[name="currency"]').val();
    var table_row = '';
    var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
    lastAddedItemKey = item_key;
    $("body").append('<div class="dt-loader"></div>');
    console.log(data);
    pur_get_item_row_template('newitems[' + item_key + ']', data.item_code, data.item_text, data.description, data.area, data.image, data.quantity, data.unit_price, data.remarks, item_key).done(function(output) {
      table_row += output;

      $('.invoice-item table.invoice-items-table.items tbody').append(table_row);
      var sourceInput = $("input[name='image']")[0];
      var targetInput = $("input[name='newitems[" + lastAddedItemKey + "][image]']")[0];
      if (sourceInput.files.length > 0) {
        var dataTransfer = new DataTransfer();
        for (var i = 0; i < sourceInput.files.length; i++) {
          dataTransfer.items.add(sourceInput.files[i]);
        }
        targetInput.files = dataTransfer.files;
      }
      init_selectpicker();
      init_datepicker();
      pur_reorder_items('.invoice-item');
      pur_clear_item_preview_values('.invoice-item');
      $('body').find('#items-warning').remove();
      $("body").find('.dt-loader').remove();
      $('#item_select').selectpicker('val', '');
      return true;
    });
    return false;
  }

  function pur_clear_item_preview_values(parent) {
    "use strict";

    var previewArea = $(parent + ' .main');
    previewArea.find('input').val('');
    previewArea.find('textarea').val('');
    previewArea.find('select').val('').selectpicker('refresh');
  }

  function pur_get_item_row_template(name, item_code, item_text, description, area, image, quantity, unit_price, remarks, item_key) {
    "use strict";

    jQuery.ajaxSetup({
      async: false
    });

    var d = $.post(admin_url + 'purchase/get_purchase_tender_row_template', {
      name: name,
      item_code: item_text,
      item_text: item_text,
      item_description: description,
      area: area,
      image: image,
      quantity: quantity,
      unit_price: unit_price,
      remarks: remarks,
      item_key: item_key
    });
    jQuery.ajaxSetup({
      async: true
    });
    return d;
  }

  function pur_get_item_preview_values() {
    "use strict";

    var response = {};
    response.item_text = $('.invoice-item .main select[name="item_text"]').val();
    response.item_code = $('.invoice-item .main input[name="item_code"]').val();
    response.description = $('.invoice-item .main textarea[name="description"]').val();
    response.area = $('.invoice-item .main select[name="area"]').val();
    response.quantity = $('.invoice-item .main input[name="quantity"]').val();
    response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
    response.remarks = $('.invoice-item .main textarea[name="remarks"]').val();

    return response;
  }

  function pur_reorder_items(parent) {
    "use strict";

    var rows = $(parent + ' .table.has-calculations tbody tr.item');
    var i = 1;
    $.each(rows, function() {
      $(this).find('input.order').val(i);
      i++;
    });
  }

  function pur_delete_item(row, itemid, parent) {
    "use strict";

    $(row).parents('tr').addClass('animated fadeOut', function() {
      setTimeout(function() {
        $(row).parents('tr').remove();
        pur_calculate_total();
      }, 50);
    });
    if (itemid && $('input[name="isedit"]').length > 0) {
      $(parent + ' #removed-items').append(hidden_input('removed_items[]', itemid));
    }
  }
</script>