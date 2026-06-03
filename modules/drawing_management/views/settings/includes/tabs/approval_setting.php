
<div class="_buttons">
  <a href="#" onclick="add(); return false;" class="btn btn-info pull-left" ><?php echo _l('add'); ?></a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>

<table class="table table-approve scroll-responsive">
  <thead>
    <th><?php echo _l('name'); ?></th>
    <th><?php echo _l('related'); ?></th>
  </thead>
  <tbody></tbody>
  <tfoot>
   <td></td>
   <td></td>                    
 </tfoot>
</table>

<div class="modal" id="approve_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title add-title"><?php echo _l('dmg_add_approval'); ?></h4>
        <h4 class="modal-title edit-title hide"><?php echo _l('dmg_edit_approval'); ?></h4>
      </div>

      <?php 
      $setting = []; 
      ?>
      <?php echo form_open(admin_url('drawing_management/approver_setting'),array('id'=>'approval-setting-form')); ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
           <?php echo render_input('name','subject','','text'); ?>
           <?php echo form_hidden('approval_setting_id', ''); ?>
           <?php echo form_hidden('related', 'drawing'); ?>
           <?php $choose_when_approving = 0;
           if(isset($approval_setting)){
            $choose_when_approving = $approval_setting->choose_when_approving;
          } 
          ?>
          <!-- use for only Leave  start-->

          <!-- <div id="notification_recipient" class="notification_recipient hide"> -->
           <div id="notification_recipient" class="notification_recipient">
            <div class="select-placeholder form-group">
              <label for="notification_recipient[]"><?php echo _l('notification_recipient'); ?></label>
              <select name="notification_recipient[]" id="notification_recipient[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" data-action-box="true" data-hide-disabled="true" data-live-search="true">
                <?php foreach($staffs as $val){
                 $selected = '';
                 ?>
                 <option value="<?php echo drawing_htmldecode($val['staffid']); ?>">
                   <?php echo get_staff_full_name($val['staffid']); ?>
                 </option>
               <?php } ?>
             </select>
           </div> 
         </div>
         <?php echo render_input('number_day_approval','maximum_number_of_days_to_sign','','number'); ?>
         <!-- use for only Leave  start-->
         <div class="row hide">
           <div class="col-md-12">
             <div class="checkbox checkbox-inline checkbox-primary">
              <input type="checkbox" name="choose_when_approving" id="choose_when_approving" value="1">
              <label for="choose_when_approving"><?php echo _l('dmg_choose_when_approving'); ?></label>
            </div>              
          </div>
        </div>

        <div class="list_approve">
          <h5><?php echo _l('approval_process'); ?></h5>
          <hr/> 
          <div class="clearfix"></div>
          <div id="item_approve">
            <div class="row">
              <div class="col-md-11">                            
                <div id="is_staff_0">
                  <div class="select-placeholder form-group">
                    <label for="staff[0]"><?php echo _l('staff'); ?></label>
                    <select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true"  required="required">
                      <option value=""></option>
                      <?php foreach($staffs as $val){
                       $selected = '';
                       ?>
                       <option value="<?php echo drawing_htmldecode($val['staffid']); ?>">
                         <?php echo get_staff_full_name($val['staffid']); ?>
                       </option>
                     <?php } ?>
                   </select>
                 </div> 
               </div>
             </div>
             <div class="col-md-1 ptop10">
              <button name="add" class="btn new_vendor_requests btn-success mtop15 pull-right" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
  <?php echo form_close(); ?>
</div>
</div>
</div>
</div>


