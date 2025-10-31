<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'pur_bills'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 200px
   }
   .n_width {
      width: 25% !important;
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
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('bill_bifurcation'); ?></h4>
                           <hr />
                        </div>
                     </div>

                     <div class="row all_ot_filters mtop20">
                        <div class="col-md-3">
                           <?php
                           $vendors_type_filter = get_module_filter($module_name, 'vendors');
                           $vendors_type_filter_val = !empty($vendors_type_filter) ? explode(",", $vendors_type_filter->filter_value) : [];
                           echo render_select('vendors[]', $vendors, array('userid', 'company'), '', $vendors_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('pur_vendor'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-3">
                           <?php
                           $order_tagged_detail_filter = get_module_filter($module_name, 'order_tagged_detail');
                           $order_tagged_detail_filter_val = !empty($order_tagged_detail_filter) ? explode(",", $order_tagged_detail_filter->filter_value) : '';
                           echo render_select('order_tagged_detail[]', $order_tagged_detail, array('id', 'name'), '', $order_tagged_detail_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Order Detail'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-3">
                           <?php
                           $approval_status_type_filter = get_module_filter($module_name, 'approval_status');
                           $approval_status_type_filter_val = !empty($approval_status_type_filter) ? explode(",", $approval_status_type_filter->filter_value) : [];
                           $payment_status = [
                              ['id' => 1, 'name' => 'Draft'],
                              ['id' => 2, 'name' => 'Approved'],
                              ['id' => 3, 'name' => 'Rejected'],
                           ];
                           echo render_select('approval_status[]', $payment_status, array('id', 'name'), '', $approval_status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('approval_status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                              <?php echo _l('reset_filter'); ?>
                           </a>
                        </div>
                     </div>
                     <br>

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
                              'Checkbox',
                              'bill_bifurcation',
                              'Bill Code',
                              'order_name',
                              'vendor',
                              'payment_certificate_date',
                              'Amount',
                              'approval_status',
                              'options',
                              'last_action_by',
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

                     <table class="dt-table-loading table table-table_pur_bills">
                        <thead>
                           <tr>
                              <th style="width: 5px"><span class="hide"> - </span>
                                 <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_pur_bills"><label></label></div>
                              </th>
                              <th><?php echo _l('bill_bifurcation'); ?></th>
                              <th><?php echo _l('Bill Code'); ?></th>
                              <th><?php echo _l('order_name'); ?></th>
                              <th><?php echo _l('vendor'); ?></th>
                              <th><?php echo _l('created'); ?></th>
                              <th><?php echo _l('Amount'); ?></th>
                              <th><?php echo _l('approval_status'); ?></th>
                              <th><?php echo _l('options'); ?></th>
                              <th><?php echo _l('last_action_by'); ?></th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                        <tbody></tbody>
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
      var table_pur_bills = $('.table-table_pur_bills');
      var Params = {
         "vendors": "[name='vendors[]']",
         "approval_status": "[name='approval_status[]']",
         "order_tagged_detail": "[name='order_tagged_detail[]']",
      };
      initDataTable(table_pur_bills, admin_url + 'purchase/table_pur_bills', [], [], Params, [2, 'desc']);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_pur_bills.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_ot_filters', function() {
         var filterArea = $('.all_ot_filters');
         filterArea.find('input').val("");
         filterArea.find('select').selectpicker("val", "");
         table_pur_bills.DataTable().ajax.reload();
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table_pur_bills.DataTable().column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table_pur_bills.DataTable().columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      table_pur_bills.on('draw.dt', function () {
         $('.toggle-column[data-id="group_pur"]').prop('checked', false).trigger('change');
         $('.selectpicker').selectpicker('refresh');
      });

      $(document).on('click', '.delete_bill', function(e) {
         e.preventDefault();
         var url = $(this).attr('href');
         Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to remove this bill bifurcation?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
         }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = url;
            }
         });
      });
   });
</script>
<script>
   function send_bill_bifurcation_approve(id, rel_type){
     "use strict";
     var data = {};
     data.rel_id = id;
     data.rel_type = rel_type;
     $("body").append('<div class="dt-loader"></div>');
       $.post(admin_url + 'purchase/send_bill_bifurcation_approve', data).done(function(response){
           response = JSON.parse(response);
           $("body").find('.dt-loader').remove();
           if (response.success === true || response.success == 'true') {
               alert_float('success', response.message);
               window.location.reload();
           }else{
             alert_float('warning', response.message);
               window.location.reload();
           }
       });
   }
</script>
</body>

</html>