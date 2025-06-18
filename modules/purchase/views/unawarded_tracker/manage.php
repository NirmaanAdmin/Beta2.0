<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .form-inline textarea.form-control {

      width: auto !important;
   }

   .label-purple {
      background-color: rgb(205, 180, 252) !important;
      color: rgb(109, 0, 159);
   }

   .label-teal {
      background-color: #baf8ff;
      color: #0097A7;
   }

   .label-green {
      background-color: #d0fdd2;
      color: #0f8c14;
   }

   .label-secondary {
      background-color: #e1eef9;
      color: #6c757d;
   }

   .label-orange {
      background-color: #f8eedb;
      color: #FFA500;
   }

   .show_hide_columns {
      position: absolute;
      z-index: 999;
      left: 140px;
   }

   .export-btn-div {
      position: absolute;
      z-index: 999;
      left: 189px;
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
<?php $module_name = 'unawareded_tracker'; ?>
<div id="wrapper">
   <div class="content">
      <div class="loader-container hide" id="loader-container">
         <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
      </div>
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-12">
                     <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('unawarded_tracker'); ?></h4>
                     <hr />
                  </div>
               </div>

               <div class="row">
                  <div class="_buttons col-md-12">
                     <!-- <button class="btn btn-info pull-left mright10 display-block" style="margin-right: 10px;" data-toggle="modal" data-target="#addNewRowModal">
                        <i class="fa fa-plus"></i> <?php echo _l('New'); ?>
                     </button> -->

                     <div class="all_ot_filters">

                        <!-- <div class="col-md-3 form-group">
                           <?php
                           $rli_filter = get_module_filter($module_name, 'rli_filter');
                           $rli_filter_val = !empty($rli_filter) ? $rli_filter->filter_value : '';
                           ?>
                           <select name="rli_filter" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('rli_filter'); ?>" data-actions-box="true">
                              <option value=""></option>
                              <option value="None">None</option>
                              <?php foreach ($rli_filters as $rli) { ?>
                                 <option value="<?php echo $rli['id']; ?>" <?php echo ($rli_filter_val == $rli['id']) ? 'selected' : ''; ?>><?php echo $rli['name']; ?></option>
                              <?php } ?>
                           </select>
                        </div> -->


                        <!-- <div class="col-md-2 form-group">
                              <?php
                              $order_tracker_kind_filter = get_module_filter($module_name, 'order_tracker_kind');
                              $order_tracker_kind_filter_val = !empty($order_tracker_kind_filter) ? $order_tracker_kind_filter->filter_value : '';
                              ?>
                              <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('cat'); ?>">
                                 <option value=""></option>
                                 <option value="Client Supply" <?php echo ($order_tracker_kind_filter_val == "Client Supply") ? 'selected' : ''; ?>><?php echo _l('client_supply'); ?></option>
                                 <option value="Bought out items" <?php echo ($order_tracker_kind_filter_val == "Bought out items") ? 'selected' : ''; ?>><?php echo _l('bought_out_items'); ?></option>
                              </select>
                           </div> -->

                        <div class="col-md-2 form-group" style="padding-left: 0px;">
                           <?php
                           $budget_head_filter = get_module_filter($module_name, 'budget_head');
                           $budget_head_filter_val = !empty($budget_head_filter) ? $budget_head_filter->filter_value : '';
                           ?>
                           <select name="budget_head" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('group_pur'); ?>" data-actions-box="true">
                              <option value=""></option>
                              <option value="None">None</option>
                              <?php foreach ($budget_head as $head) { ?>
                                 <option value="<?php echo $head['id']; ?>" <?php echo ($budget_head_filter_val == $head['id']) ? 'selected' : ''; ?>><?php echo $head['name']; ?></option>
                              <?php } ?>
                           </select>
                        </div>


                        <div class="col-md-2">
                           <?php
                           $projects_filter = get_module_filter($module_name, 'projects');
                           if (!empty($projects_filter) && $projects_filter->filter_value != '') {
                              $projects_filter_val = !empty($projects_filter) ? explode(",", $projects_filter->filter_value) : '';
                           } else {
                              $projects_filter_val = explode(",", 1);
                           }

                           echo render_select('projects[]', $projects, array('id', 'name'), '', $projects_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('project'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>



                        <div class="col-md-1 form-group">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                              <?php echo _l('reset_filter'); ?>
                           </a>
                        </div>

                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">
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
                              _l('project'),
                              _l('Package Name'),
                              _l('Preview'),
                              _l('Budget Head'),
                              _l('cat'),
                              _l('rli_filter'),
                              _l('Package Value'),
                              _l('Awarded Value'),
                              _l('Secured Deposit Value'),
                              _l('Pending Value In Package'),
                              _l('Package Status'),
                              _l('Percentage of Capex Used'),
                              _l('Book Order')
                           ];
                           ?>
                           <div>
                              <?php foreach ($columns as $key => $label): ?>
                                 <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                                 <?php echo $label; ?><br>
                              <?php endforeach; ?>
                           </div>

                        </div>
                     </div>
                     <!-- <div class="btn-group export-btn-div" id="export-btn-div">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                           <i class="fa fa-download"></i> <?php echo _l('Export'); ?> <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu" style="padding: 10px;min-width: 94px;">
                           <a class="dropdown-item export-btn" href="<?php echo admin_url('purchase/unawarded_tracker_pdf'); ?>" data-type="pdf">
                              <i class="fa fa-file-pdf text-danger"></i> PDF
                           </a><br>
                           <a class="dropdown-item export-btn" href="<?php echo admin_url('purchase/unawarded_tracker_excel'); ?>" data-type="excel">
                              <i class="fa fa-file-excel text-success"></i> Excel
                           </a>
                        </div>
                     </div> -->

                     <div class="">
                        <table class="dt-table-loading table table-table_unawarded_tracker">
                           <thead>
                              <tr>
                                 <th><?php echo _l('project'); ?></th>
                                 <th><?php echo _l('Package Name'); ?></th>
                                 <th><?php echo _l('Preview'); ?></th>
                                 <th><?php echo _l('Budget Head'); ?></th>
                                 <th><?php echo _l('cat'); ?></th>
                                 <th><?php echo _l('rli_filter'); ?></th>
                                 <th><?php echo _l('Package Value'); ?></th>
                                 <th><?php echo _l('Awarded Value'); ?></th>
                                 <th><?php echo _l('Secured Deposit Value'); ?></th>
                                 <th><?php echo _l('Pending Value In Package'); ?></th>
                                 <th><?php echo _l('Package Status'); ?></th>
                                 <th><?php echo _l('Percentage of Capex Used'); ?></th>
                                 <th><?php echo _l('Book Order'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="addNewRowModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('Add New Order'); ?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="col-md-8 pull-right">
               <div class="col-md-2 pull-right">
                  <div id="dowload_file_sample" style="margin-top: 22px;">
                     <label for="file_csv" class="control-label"> </label>
                     <a href="<?php echo site_url('modules/purchase/uploads/file_sample/Sample_import_order_tracker_en.xlsx') ?>" class="btn btn-primary">Template</a>
                  </div>
               </div>
               <div class="col-md-4 pull-right" style="display: flex;align-items: end;padding: 0px;">
                  <?php echo form_open_multipart(admin_url('purchase/import_file_xlsx_order_tracker_items'), array('id' => 'import_form')); ?>
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
         </div>
         <div class="modal-body invoice-item">
            <div class="row">
               <div class="col-md-12">
                  <div class="table-responsive" style="overflow-x: unset !important;">
                     <?php
                     echo form_open_multipart('', array('id' => 'order_tracker-form'));
                     ?>
                     <table class="table order-tracker-items-table items table-main-invoice-edit has-calculations no-mtop">
                        <thead>
                           <tr>
                              <th align="left"><?php echo _l('order_scope'); ?></th>
                              <th align="left"><?php echo _l('order_date'); ?></th>
                              <th align="left"><?php echo _l('completion_date'); ?></th>
                              <th align="left"><?php echo _l('budget_ro_projection'); ?></th>
                              <th align="left"><?php echo _l('project'); ?></th>
                              <th align="left"><?php echo _l('category'); ?></th>
                              <th align="left"><?php echo _l('group_pur'); ?></th>
                              <th align="left"><?php echo _l('remarks'); ?></th>
                              <th align="center"><i class="fa fa-cog"></i></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php echo  $order_tracker_row_template; ?>
                        </tbody>
                     </table>
                     <button type="submit" class="btn btn-info pull-right"><?php echo _l('Save'); ?></button>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="viewOrderAttachmentModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('attachment'); ?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <div class="view_order_attachment_modal">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="order_tracker_file_data"></div>
<div class="modal fade" id="cost_complete_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">View Items</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

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

<div class="modal fade" id="package_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <?php echo form_open(admin_url('estimates/save_package'), array('id' => 'unawarded_capex_form', 'class' => '')); ?>
         <div class="modal-header">
            <h4 class="modal-title"><div class="package_title"></div></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="package-head"></div>
         </div>
         <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="package-body">
                    </div>
                </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<?php require 'modules/purchase/assets/js/import_excel_items_unawarded_tracker_js.php'; ?>
<?php require 'modules/purchase/assets/js/cost_planning_js.php'; ?>
</body>

</html>