<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 999;
      left: 204px
   }
   table {
      table-layout: auto !important;
      width: 100%;
      border-collapse: collapse;
   }
   th,
   td {
      white-space: normal;
      word-wrap: break-word;
      overflow-wrap: break-word;
      vertical-align: top;
   }
   .tags-labels {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      max-width: 100%;
      align-items: center;
   }
   .label-tag {
      display: inline-block;
      max-width: 100%;
      white-space: nowrap;
      /* Prevent text from stacking */
      overflow: hidden;
      text-overflow: ellipsis;
      padding: 5px 10px;
      background: #f0f0f0;
      border-radius: 5px;
   }
   .label-tag .tag {
      display: inline;
   }
   .table-table_pur_invoice_payments {
      font-size: 12px !important;
   }
   .table-table_pur_invoice_payments th,
   .table-table_pur_invoice_payments td {
      font-size: 12px !important;
   }
   #scroll-slider {
      position: absolute;
      right: 10px;
      width: 200px;
      height: 2px;
      background-color: #000000;
      border-radius: 5px;
      z-index: 10000;
      cursor: pointer;
  }

  #scroll-thumb {
      width: 15px;
      height: 15px;
      background-color: #ad729f;
      border-radius: 15px;
      position: relative;
      top: -6px;
  }
  .n_width {
      width: 33% !important;
   }
   .dashboard_stat_title {
      font-size: 19px;
      font-weight: bold;
   }
   .dashboard_stat_value {
      font-size: 19px;
   }
</style>
<?php $module_name = 'vendor_billing_payments'; ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                        <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                        <hr />
                     </div>
                  </div>
                  <div class="row">
                     <div class="_buttons col-md-12">
                        <button class="btn btn-info pull-left display-block" type="button" data-toggle="collapse" data-target="#vpt-charts-section" aria-expanded="true"aria-controls="vpt-charts-section">
                         <?php echo _l('Vendor Payment Tracker Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                        </button>
                     </div>
                  </div>

                  <div id="vpt-charts-section" class="collapse in">
                     <div class="row">
                        <div class="col-md-12 mtop20">
                           <div class="row">
                              <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                <div class="top_stats_wrapper">
                                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                      <span class="tw-truncate dashboard_stat_title">Total Billed</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                  </div>
                                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                      <span class="tw-truncate dashboard_stat_value total_billed"></span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                  </div>
                                </div>
                              </div>
                              <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                <div class="top_stats_wrapper">
                                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                      <span class="tw-truncate dashboard_stat_title">Total Paid</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                  </div>
                                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                      <span class="tw-truncate dashboard_stat_value total_paid"></span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                  </div>
                                </div>
                              </div>
                              <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                <div class="top_stats_wrapper">
                                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                      <span class="tw-truncate dashboard_stat_title">Total Unpaid</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                  </div>
                                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                      <span class="tw-truncate dashboard_stat_value total_unpaid"></span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                  </div>
                                </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row mtop20">
                        <div class="col-md-4">
                           <p class="mbot15 dashboard_stat_title">Vendor wise Payments Summary</p>
                           <div style="width: 100%; height: 500px;">
                             <canvas id="barChartTopVendors"></canvas>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <p class="mbot15 dashboard_stat_title">Budget Head Utilization</p>
                           <div style="width: 100%; height: 500px;">
                             <canvas id="barChartTopBudgetHead"></canvas>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <p class="mbot15 dashboard_stat_title">Vendor Billing vs Vendor Payment Timeline</p>
                           <div style="width: 100%; height: 500px;">
                             <canvas id="vendorLineChartOverTime"></canvas>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row mtop20">
                        <div class="vbt_all_filters">

                           <div class="col-md-3">
                              <?php
                              $from_date_filter = get_module_filter($module_name, 'from_date');
                              $from_date_filter_val = !empty($from_date_filter) ? $from_date_filter->filter_value : '';
                              echo render_date_input('from_date', '', $from_date_filter_val, array('placeholder' => _l('from_date')));
                              ?>
                           </div>

                           <div class="col-md-3">
                              <?php
                              $to_date_filter = get_module_filter($module_name, 'to_date');
                              $to_date_filter_val = !empty($to_date_filter) ? $to_date_filter->filter_value : '';
                              echo render_date_input('to_date', '', $to_date_filter_val, array('placeholder' => _l('to_date')));
                              ?>
                           </div>

                           <div class="col-md-3 form-group">
                              <?php
                              $vendors_filter = get_module_filter($module_name, 'vendors');
                              $vendors_filter_val = !empty($vendors_filter) ? explode(",", $vendors_filter->filter_value) : '';
                              echo render_select('vendor_ft[]', $vendors, array('userid', 'company'), '', $vendors_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('vendors'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                              ?>
                           </div>

                           <div class="col-md-3 form-group">
                              <?php
                              $budget_head_filter = get_module_filter($module_name, 'budget_head');
                              $budget_head_filter_val = !empty($budget_head_filter) ? explode(",", $budget_head_filter->filter_value) : [];
                              $budget_head_options   = [];
                              $budget_head_options[] = ['id' => 'None', 'value' => 'None'];
                              foreach ($budget_head as $head) {
                                 $budget_head_options[] = ['id' => $head['id'], 'value' => $head['name']];
                              }
                              echo render_select('budget_head[]', $budget_head_options, ['id', 'value'], '', $budget_head_filter_val, ['data-width' => '100%', 'data-none-selected-text' => _l('group_pur'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                              ?>
                           </div>

                           <div class="col-md-3 form-group">
                              <?php
                              $billing_invoices_filter = get_module_filter($module_name, 'billing_invoices');
                              $billing_invoices_filter_val = !empty($billing_invoices_filter) ? explode(",", $billing_invoices_filter->filter_value) : [];
                              $billing_invoice_options = [];
                              $billing_invoice_options[] = ['id' => 'to_be_converted', 'value' => _l('To Be Converted')];
                              $billing_invoice_options[] = ['id' => 'converted', 'value' => _l('Converted')];
                              foreach ($billing_invoices as $invoice) {
                                 $billing_invoice_options[] = ['id' => $invoice['id'], 'value' => $invoice['value']];
                              }
                              echo render_select('billing_invoices[]', $billing_invoice_options, ['id', 'value'], '', $billing_invoices_filter_val, ['data-width' => '100%', 'data-none-selected-text' => _l('pur_invoices'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                              ?>
                           </div>

                           <div class="col-md-3 form-group">
                              <?php
                              $bil_payment_status_filter = get_module_filter($module_name, 'bil_payment_status');
                              $bil_payment_status_filter_val = !empty($bil_payment_status_filter) ? $bil_payment_status_filter->filter_value : '';
                              ?>
                              <select name="bil_payment_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('bil_payment_status'); ?>" data-actions-box="true">
                                 <option value=""></option>
                                 <option value="unpaid" <?php echo ($bil_payment_status_filter_val == 'unpaid') ? 'selected' : ''; ?>><?php echo _l('unpaid'); ?></option>
                                 <option value="partially_paid" <?php echo ($bil_payment_status_filter_val == 'partially_paid') ? 'selected' : ''; ?>><?php echo _l('partially_paid'); ?></option>
                                 <option value="paid" <?php echo ($bil_payment_status_filter_val == 'paid') ? 'selected' : ''; ?>><?php echo _l('paid'); ?></option>
                              </select>
                           </div>

                           <div class="col-md-3">
                              <?php
                              $order_tagged_detail_filter = get_module_filter($module_name, 'order_tagged_detail');
                              $order_tagged_detail_filter_val = !empty($order_tagged_detail_filter) ? explode(",", $order_tagged_detail_filter->filter_value) : '';
                              echo render_select('order_tagged_detail[]', $order_tagged_detail, array('id', 'name'), '', $order_tagged_detail_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Order Detail'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                              ?>
                           </div>

                           <div class="col-md-1 form-group">
                              <a href="javascript:void(0)" class="btn btn-info btn-icon reset_vbt_all_filters">
                                 <?php echo _l('reset_filter'); ?>
                              </a>
                           </div>
                        </div></br>

                        <div class="col-md-offset-9 col-md-3">
                           <div style="align-items: end;padding: 0px;">
                              <?php echo form_open_multipart(admin_url('purchase/import_file_xlsx_vendor_billing_tracker'), array('id' => 'import_form')); ?>
                              <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>
                              <div class="form-group">
                                 <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);"><?php echo _l('import'); ?></button>
                                 <a href="<?php echo site_url('modules/purchase/uploads/file_sample/Sample_vendor_payment_tracker_item_en.xlsx') ?>" class="btn btn-primary">Template</a>
                              </div>
                              <?php echo form_close(); ?>
                              <div class="form-group" id="file_upload_response">
                              </div>
                           </div>
                        </div>
                  </div>

                  <!-- <div class="row">
                     <div id="scroll-slider">
                        <div id="scroll-thumb"></div>
                     </div>
                  </div> -->
                  </br>

                  <div class="btn-group show_hide_columns" id="show_hide_columns">
                     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                        <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                     </button>
                     <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                        <div>
                           <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                        </div>
                        <hr>
                        <?php
                        $columns = [
                           'id',
                           'invoice_code',
                           'invoice_number',
                           'vendor',
                           'group_pur',
                           'invoice_date',
                           'amount_without_tax',
                           'vendor_submitted_tax_amount',
                           'certified_amount',
                           'vbt_order_name',
                           'bil_payment_date',
                           'bil_payment_made',
                           'bil_tds',
                           'bil_total',
                           'ril_bill_no',
                           'ril_previous',
                           'ril_this_bill',
                           'ril_date',
                           'ril_amount',
                           'remarks',
                           'last_action_by',
                        ];
                        ?>
                        <div>
                           <?php foreach ($columns as $key => $label): ?>
                              <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                              <?php echo _l($label); ?><br>
                           <?php endforeach; ?>
                        </div>
                     </div>
                  </div>
                  <div class="">
                     <table class="dt-table-loading table table-table_pur_invoice_payments">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th><?php echo _l('invoice_code'); ?></th>
                              <th><?php echo _l('invoice_number'); ?></th>
                              <th><?php echo _l('vendor'); ?></th>
                              <th><?php echo _l('group_pur'); ?></th>
                              <th><?php echo _l('invoice_date'); ?></th>
                              <th><?php echo _l('amount_without_tax'); ?></th>
                              <th><?php echo _l('vendor_submitted_tax_amount'); ?></th>
                              <th><?php echo _l('final_certified_amount'); ?></th>
                              <th><?php echo _l('vbt_order_name'); ?></th>
                              <th><?php echo _l('bil_payment_date'); ?></th>
                              <th><?php echo _l('bil_payment_made'); ?></th>
                              <th><?php echo _l('bil_tds'); ?></th>
                              <th><?php echo _l('bil_total'); ?></th>
                              <th><?php echo _l('ril_bill_no'); ?></th>
                              <th><?php echo _l('ril_previous'); ?></th>
                              <th><?php echo _l('ril_this_bill'); ?></th>
                              <th><?php echo _l('ril_date'); ?></th>
                              <th><?php echo _l('ril_amount'); ?></th>
                              <th><?php echo _l('remarks'); ?></th>
                              <th><?php echo _l('last_action_by'); ?></th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                           <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td class="total_vendor_submitted_amount_without_tax"></td>
                              <td class="total_vendor_submitted_tax_amount"></td>
                              <td class="total_final_certified_amount"></td>
                              <td></td>
                              <td></td>
                              <td class="total_payment_made"></td>
                              <td class="total_bil_tds"></td>
                              <td class="total_bil_total"></td>
                              <td></td>
                              <td class="total_ril_previous"></td>
                              <td class="total_ril_this_bill"></td>
                              <td></td>
                              <td class="total_ril_amount"></td>
                              <td></td>
                              <td></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table = $('.table-table_pur_invoice_payments').DataTable();

      // On page load, fetch and apply saved preferences for the logged-in user
      $.ajax({
         url: admin_url + 'purchase/getPreferences',
         type: 'GET',
         dataType: 'json',
         success: function(data) {
            console.log("Retrieved preferences:", data);

            // Ensure DataTable is initialized
            let table = $('.table-table_pur_invoice_payments').DataTable();

            // Loop through each toggle checkbox to update column visibility
            $('.toggle-column').each(function() {
               // Parse the column index (ensuring it's a number)
               let colIndex = parseInt($(this).val(), 10);

               // Use the saved preference if available; otherwise, default to visible ("true")
               let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
                  data.preferences[colIndex] :
                  "true";

               // Convert string to boolean if needed
               let isVisible = (typeof prefValue === "string") ?
                  (prefValue.toLowerCase() === "true") :
                  prefValue;

               // Set column visibility but prevent immediate redraw (redraw = false)
               table.column(colIndex).visible(isVisible, false);
               // Update the checkbox state accordingly
               $(this).prop('checked', isVisible);
            });

            // Finally, adjust columns and redraw the table once
            table.columns.adjust().draw();

            // Update the "Select All" checkbox based on individual toggle states
            let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
            $('#select-all-columns').prop('checked', allChecked);
         },
         error: function() {
            console.error('Could not retrieve column preferences.');
         }
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table.column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);

         // Save updated preferences
         saveColumnPreferences();
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      // Function to collect and save preferences via AJAX
      function saveColumnPreferences() {
         var preferences = {};
         $('.toggle-column').each(function() {
            preferences[$(this).val()] = $(this).is(':checked');
         });

         $.ajax({

            url: admin_url + 'purchase/savePreferences',
            type: 'POST',
            data: {
               preferences: preferences
            },
            success: function(response) {
               console.log('Preferences saved successfully.');
            },
            error: function() {
               console.error('Failed to save preferences.');
            }
         });
      }

      $('#vpt-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#vpt-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      });
   });

   function uploadfilecsv() {
      "use strict";

      if (($("#file_csv").val() != '') && ($("#file_csv").val().split('.').pop() == 'xlsx')) {
         var formData = new FormData();
         formData.append("file_csv", $('#file_csv')[0].files[0]);
         if (<?php echo  pur_check_csrf_protection(); ?>) {
            formData.append(csrfData.token_name, csrfData.hash);
         }

         $.ajax({
            url: admin_url + 'purchase/import_file_xlsx_vendor_payment_tracker',
            method: 'post',
            data: formData,
            contentType: false,
            processData: false

         }).done(function(response) {
            response = JSON.parse(response);
            $("#file_csv").val(null);
            $("#file_csv").change();
            $(".panel-body").find("#file_upload_response").html();

            if ($(".panel-body").find("#file_upload_response").html() != '') {
               $(".panel-body").find("#file_upload_response").empty();
            };
            $("#file_upload_response").append("<h4><?php echo _l("_Result") ?></h4><h5><?php echo _l('import_line_number') ?> :" + response.total_rows + " </h5>");
            $("#file_upload_response").append("<h5><?php echo _l('import_line_number_success') ?> :" + response.total_row_success + " </h5>");
            $("#file_upload_response").append("<h5><?php echo _l('import_line_number_failed') ?> :" + response.total_row_false + " </h5>");
            if ((response.total_row_false > 0) || (response.total_rows_data_error > 0)) {
               $("#file_upload_response").append('<a href="' + site_url + response.filename + '" class="btn btn-warning"  ><?php echo _l('download_file_error') ?></a>');
            }
            if (response.total_rows < 1) {
               alert_float('warning', response.message);
            }
         });
         return false;

      } else if ($("#file_csv").val() != '') {
         alert_float('warning', "<?php echo _l('_please_select_a_file') ?>");
      }

   }
   // Initialize the DataTable
   var table_pur_invoice_payments = $('.table-table_pur_invoice_payments').DataTable();
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>