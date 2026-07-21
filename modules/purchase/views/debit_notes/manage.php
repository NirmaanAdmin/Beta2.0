<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'debitnotes'; ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        <div class="_filters _hidden_inputs">
           <?php
            foreach($statuses as $status) {
               echo form_hidden('debit_notes_status_'.$status['id'],isset($status['filter_default'])
                  && $status['filter_default'] ? 'true' : '');
            }
           foreach($years as $year){
              echo form_hidden('year_'.$year['year'],$year['year']);
           }
        ?>
     </div>
     <div class="col-md-12">
      <div class="panel_s mbot10">
         <div class="panel-body _buttons">
            <?php if(has_permission('purchase_debit_notes','','create')){ ?>
            <a href="<?php echo admin_url('purchase/debit_note'); ?>" class="btn btn-info pull-left display-block">
               <?php echo _l('new_debit_note'); ?>
            </a>
            <?php } ?>
            <div class="display-block text-right">
             <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
               <ul class="dropdown-menu width300">
                  <li>
                     <a href="#" data-cview="all" onclick="dt_custom_view('','.table-debit-notes',''); return false;">
                        <?php echo _l('debit_notes_list_all'); ?>
                     </a>
                  </li>
                  <li class="divider"></li>
                  <?php foreach($statuses as $status){ ?>
                  <li class="<?php if(isset($status['filter_default']) && $status['filter_default']){echo 'active';} ?>">
                     <a href="#" data-cview="debit_notes_status_<?php echo $status['id']; ?>" onclick="dt_custom_view('debit_notes_status_<?php echo $status['id']; ?>','.table-debit-notes','debit_notes_status_<?php echo $status['id']; ?>'); return false;">
                        <?php echo format_credit_note_status($status['id'],true); ?>
                     </a>
                  </li>
                  <?php } ?>
                  <div class="clearfix"></div>
                  <?php
                  if(count($years) > 0){ ?>
                  <li class="divider"></li>
                  <?php foreach($years as $year){ ?>
                  <li class="active">
                     <a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-debit-notes','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
                     </a>
                  </li>
                  <?php } ?>
                  <?php } ?>
               </ul>
            </div>
            <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view('.table-debit-notes','#debit_note'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12" id="small-table">
         <div class="panel_s">
            <div class="panel-body">
               <div class="row all_filters">
                  <?php
                  $debit_notes_filter = get_module_filter($module_name, 'debit_notes');
                  $debit_notes_filter_val = !empty($debit_notes_filter) ? explode(",", $debit_notes_filter->filter_value) : [];
                  ?>
                  <div class="col-md-3 form-group">
                      <label for="debit_notes"><?php echo _l('debit_notes'); ?></label>
                      <select name="debit_notes[]" id="debit_notes" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                          <?php foreach ($debit_notes as $debit_note) { ?>
                              <option value="<?php echo pur_html_entity_decode($debit_note['id']); ?>"
                                  <?php if (in_array($debit_note['id'], $debit_notes_filter_val)) {
                                      echo 'selected';
                                  } ?>>
                                  <?php echo format_debit_note_number($debit_note['id']); ?>
                              </option>
                          <?php } ?>
                      </select>
                  </div>

                  <?php
                  $vendors_filter = get_module_filter($module_name, 'vendors');
                  $vendors_filter_val = !empty($vendors_filter) ? explode(",", $vendors_filter->filter_value) : [];
                  ?>
                  <div class="col-md-3 form-group">
                      <label for="vendors"><?php echo _l('vendors'); ?></label>
                      <select name="vendors[]" id="vendors" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                          <?php foreach ($vendors as $vendor) { ?>
                              <option value="<?php echo pur_html_entity_decode($vendor['userid']); ?>"
                                  <?php if (in_array($vendor['userid'], $vendors_filter_val)) {
                                      echo 'selected';
                                  } ?>>
                                  <?php echo pur_html_entity_decode($vendor['company']); ?>
                              </option>
                          <?php } ?>
                      </select>
                  </div>

                  <?php
                  $statuses_filter = get_module_filter($module_name, 'statuses');
                  $statuses_filter_val = !empty($statuses_filter) ? explode(",", $statuses_filter->filter_value) : [];
                  ?>
                  <div class="col-md-3 form-group">
                      <label for="status"><?php echo _l('Status'); ?></label>
                      <select name="statuses[]" id="statuses" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                          <?php foreach ($statuses as $status) { ?>
                              <option value="<?php echo pur_html_entity_decode($status['id']); ?>"
                                  <?php if (in_array($status['id'], $statuses_filter_val)) {
                                      echo 'selected';
                                  } ?>>
                                  <?php echo pur_html_entity_decode($status['name']); ?>
                              </option>
                          <?php } ?>
                      </select>
                  </div>
              </div>

              <div class="row">
                  <div class="col-md-1 form-group">
                      <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                          <?php echo _l('reset_filter'); ?>
                      </a>
                  </div>
              </div>
              <hr class="hr-panel-separator" />
               <!-- if credit not id found in url -->
               <?php echo form_hidden('debit_note_id',$debit_note_id); ?>
               <?php $this->load->view('debit_notes/table_html'); ?>
            </div>
         </div>
      </div>
      <div class="col-md-7 small-table-right-col">
         <div id="debit_note" class="hide">
         </div>
      </div>
   </div>
</div>
</div>
</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
   var hidden_columns = [4,5,6];
</script>
<?php init_tail(); ?>
<?php require 'modules/purchase/assets/js/manage_debit_note_js.php';?>  
</body>
</html>
