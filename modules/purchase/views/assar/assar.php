<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'module_activity_log'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 200px
   }

   .n_width {
      width: 20% !important;
   }

   .dashboard_stat_title {
      font-size: 19px;
      font-weight: bold;
   }

   .dashboard_stat_value {
      font-size: 19px;
   }

   .bulk-title {
      font-weight: bold;
   }

   b {
      font-weight: 700;
   }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">

         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">
                     <div class="row">
                        <div class="col-md-12">
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('ASSAR'); ?></h4>
                           <hr />
                        </div>
                        <div class="_buttons col-md-3">
                           <a href="<?php echo admin_url('purchase/add_assar'); ?>" class="btn btn-info pull-left mright10 display-block">
                              <?php echo _l('New'); ?>
                           </a>
                        </div>
                        <div class="_buttons col-md-9">
                           <div class="_buttons col-md-3 pull-right">
                              <div class="form-group">
                                 <select name="month_filter" id="month_filter" class="form-control">
                                    <option value="">Select Month</option>

                                    <?php
                                    $start    = new DateTime('2025-09-01');
                                    $end      = new DateTime('2028-09-01');
                                    $current  = date('Y-m'); // current month

                                    while ($start <= $end) {
                                       $value = $start->format('Y-m');
                                       $label = $start->format('F Y');
                                       $selected = ($value === $current) ? 'selected' : '';
                                    ?>
                                       <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                                          <?php echo $label; ?>
                                       </option>
                                    <?php
                                       $start->modify('+1 month');
                                    }
                                    ?>
                                 </select>

                              </div>

                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="horizontal-tabs">
                              <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                 <li role="presentation" class="active">
                                    <a href="#master" aria-controls="master" role="tab" id="tab_master" data-toggle="tab">
                                       Master
                                    </a>
                                 </li>
                                 <li role="presentation">
                                    <a href="#main_sheet" aria-controls="main_sheet" role="tab" id="tab_main_sheet" data-toggle="tab">
                                       Main Sheet
                                    </a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                        <div class="tab-content">
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane active" id="master">
                              <div class="btn-group show_hide_columns" id="show_hide_columns">
                                 <!-- Settings Icon -->
                                 <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                                    <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                                 </button>
                                 <!-- Dropdown Menu with Checkboxes -->
                                 <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                                    <!-- Select All / Deselect All -->
                                    <div>
                                       <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                                    </div>
                                    <hr>
                                    <!-- Column Checkboxes -->
                                    <?php
                                    $columns = [
                                       'Client ID',
                                       'Name',
                                       'Phone',
                                       'Start Date',
                                       'Investment Amount',
                                       'Status (Active/Inactive)',
                                       'Referred by',
                                       'Remarks',
                                    ];
                                    ?>
                                    <div>
                                       <?php foreach ($columns as $key => $label): ?>
                                          <input type="checkbox" class="toggle-column" data-id="<?php echo $label; ?>" value="<?php echo $key; ?>" checked>
                                          <?php echo _l($label); ?><br>
                                       <?php endforeach; ?>
                                    </div>

                                 </div>
                              </div>

                              <table class="dt-table-loading table table-table_assar">
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Client ID'); ?></th>
                                       <th><?php echo _l('Name'); ?></th>
                                       <th><?php echo _l('Phone'); ?></th>
                                       <th><?php echo _l('Start Date'); ?></th>
                                       <th><?php echo _l('Investment Amount'); ?></th>
                                       <th><?php echo _l('Status (Active/Inactive)'); ?></th>
                                       <th><?php echo _l('Referred by'); ?></th>
                                       <th><?php echo _l('Remarks'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                                 <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="investment"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                 </tfoot>
                              </table>
                           </div>
                           <div role="tabpanel" class="col-md-12 tab-pane tracker-pane" id="main_sheet">
                              <table class="dt-table-loading table table-table_main_sheet" >
                                 <thead>
                                    <tr>
                                       <th><?php echo _l('Client ID'); ?></th>
                                       <th><?php echo _l('Name'); ?></th>
                                       <th><?php echo _l('Assar Holds'); ?></th>
                                       <button id="apply_to_all" class="btn btn-sm btn-primary" style="position: absolute; left: 57.5%;top: 44px;z-index: 9999;">
                                          Apply To All
                                       </button>
                                       <th style="position: relative;"><?php echo _l('Earnings Forecast %'); ?></th>
                                       <th><?php echo _l('Client Earnings Forecast ₹'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                                 <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td class="investment"></td>
                                    <td></td>
                                    <td class="client_earnings_forecast"></td>
                                 </tfoot>
                              </table>
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

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table_assar = $('.table-table_assar');
      var Params = {};
      initDataTable(table_assar, admin_url + 'purchase/table_assar', [], [], Params, [3, 'asc']);
      $('.table-table_assar').on('draw.dt', function() {
         var reportsTable = $(this).DataTable();
         var sums = reportsTable.ajax.json().sums;
         $(this).find('tfoot').addClass('bold');
         $(this).find('tfoot td').eq(0).html("Total (Per Page)");
         $(this).find('tfoot td.investment').html(sums.investment);
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table_assar.DataTable().column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table_assar.DataTable().columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      table_assar.on('draw.dt', function() {
         $('.toggle-column[data-id="group_pur"]').prop('checked', false).trigger('change');
         $('.selectpicker').selectpicker('refresh');
      });

      var table_main_sheet = $('.table-table_main_sheet');
      var Params_main_sheet = {
         "month": "[name='month_filter']",
      };
      initDataTable(table_main_sheet, admin_url + 'purchase/table_main_sheet', [], [], Params_main_sheet, [0, 'asc']);
      $('#month_filter').on('change', function() {
         table_main_sheet.DataTable().ajax.reload();
      });


      $('.table-table_main_sheet').on('draw.dt', function() {
         var reportsTable = $(this).DataTable();
         var sums = reportsTable.ajax.json().sums;
         $(this).find('tfoot').addClass('bold');
         $(this).find('tfoot td').eq(0).html("Total (Per Page)");
         $(this).find('tfoot td.investment').html(sums.investment);
         $(this).find('tfoot td.client_earnings_forecast').html(sums.client_earnings_forecast);
      });
   });


   $(document).on('blur', '.assar-input', function() {

      let client_id = $(this).data('client');
      let holds = $(this).val();
      let month = $('#month_filter').val();
      var table_main_sheet = $('.table-table_main_sheet');
      if (month == '') {
         alert('Please select month first');
         return;
      }

      $.post(admin_url + 'purchase/save_main_sheet', {
         client_id: client_id,
         assar_holds: holds,
         month: month
      }, function(response) {

         // ✅ reload main sheet table after save
         table_main_sheet.DataTable().ajax.reload(null, false);

      });

   });
   $('#apply_to_all').on('click', function() {

      let firstValue = $('.assar-input').first().val();

      if (firstValue === '') {
         alert('Enter value in first row first');
         return;
      }

      $('.assar-input').each(function() {

         $(this).val(firstValue).trigger('blur');

      });

   });
</script>
</body>

</html>