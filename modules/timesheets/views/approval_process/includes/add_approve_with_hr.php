    <?php $setting = []; ?>
    <?php echo form_open('timesheets/approval_process',array('id'=>'approval-setting-form')); ?>
    <?php $value = (isset($approval_setting)) ? $approval_setting->id : ''; ?>
    <?php $notification_recipient_select = (isset($approval_setting)) ? $approval_setting->notification_recipient : ''; 
    $notification_recipient_select = explode(',', $notification_recipient_select);    ?>
    <?php echo form_hidden('approval_setting_id', $value); ?>
    <div class="row">
      <div class="col-md-6">
        <?php $value = (isset($approval_setting)) ? $approval_setting->name : ''; ?>
        <?php echo render_input('name','subject',$value,'text'); ?>
      </div>
      <div class="col-md-6">
        <?php 
        // $related = [ 
        //   ['id' => 'additional_timesheets', 'name' => _l('additional_timesheets')],
        //   ['id' => 'Leave', 'name' => _l('annual_leave')],
        //   ['id' => 'maternity_leave', 'name' => _l('maternity_leave')],
        //   ['id' => 'private_work_without_pay', 'name' => _l('private_work_without_pay')],
        //   ['id' => 'sick_leave', 'name' => _l('sick_leave')],
        //   ['id' => 'late', 'name' => _l('late')],
        //   ['id' => 'early', 'name' => _l('early')],
        //   ['id' => 'Go_out', 'name' => _l('Go_out')],
        //   ['id' => 'Go_on_bussiness', 'name' => _l('Go_on_bussiness')],
        // ]; 
        $related = [ 
          ['id' => 'additional_timesheets', 'name' => _l('additional_timesheets')],
          ['id' => 'missed_punch', 'name' => _l('Missed Punch')],
          ['id' => 'private_work_without_pay', 'name' => _l('Leave Without Pay (lWP)')],
          ['id' => 'sick_leave', 'name' => _l('sick_leave')],
          ['id' => 'late', 'name' => _l('late')],
          ['id' => 'early', 'name' => _l('early')],
          ['id' => 'Go_out', 'name' => _l('Go_out')],
          ['id' => 'Go_on_bussiness', 'name' => _l('Go_on_bussiness')],
        ];

        foreach ($type_of_leave as $value) {
          $related[] =  ['id' => $value['slug'], 'name' => $value['type_name']];
        }

        $value = (isset($approval_setting)) ? $approval_setting->related : '';
        ?>
      </div>
      <div class="col-md-6">

        <?php echo render_select('related',$related,array('id','name'),'task_single_related',$value); ?>
      </div>
      <div class="col-md-6">
        <?php $selected = (isset($approval_setting)) ? explode(',', $approval_setting->departments) : '';  ?>
        <?php echo render_select('departments[]',$departments,array('departmentid','name'),'departments',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
      </div>
      <div class="col-md-6">
        <?php $selected = (isset($approval_setting)) ? explode(',', $approval_setting->job_positions) : ''; ?>
        <?php echo render_select('job_positions[]',$job_positions,array('roleid','name'),'role',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
      </div>

      <div class="col-md-6">

        <?php $choose_when_approving = 0;
        if(isset($approval_setting)){
          $choose_when_approving = $approval_setting->choose_when_approving;
        } 
        ?>
        <div id="notification_recipient" class="notification_recipient">
          <div class="select-placeholder form-group">
            <label for="notification_recipient[]"><?php echo _l('notification_recipient'); ?></label>
            <select name="notification_recipient[]" id="notification_recipient[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" data-action-box="true" data-hide-disabled="true" data-live-search="true">
              <?php foreach($staffs as $val){
               $selected = '';
               if(in_array($val['staffid'], $notification_recipient_select)){
                $selected = 'selected';
              }
              ?>
              <option value="<?php echo html_entity_decode($val['staffid']); ?>" <?php echo html_entity_decode($selected); ?>>
               <?php echo get_staff_full_name($val['staffid']); ?>
             </option>
           <?php } ?>
         </select>
       </div> 
     </div>
   </div>
   <div class="col-md-6">

    <?php
    $number_day_approval = 0;
    if(isset($approval_setting)){
      $number_day_approval = $approval_setting->number_day_approval;
    }
    echo render_input('number_day_approval','maximum_number_of_days_to_sign',$number_day_approval,'number'); ?>
  </div>

  <div class="col-md-12 mtop5">
    <strong><?php echo _l('approval_process'); ?></strong>
    <div class="checkbox checkbox-inline checkbox-primary pull-right">
      <input type="checkbox" name="choose_when_approving" id="choose_when_approving" value="1" <?php if($choose_when_approving == 1){echo 'checked';} ?>>
      <label for="choose_when_approving"><?php echo _l('choose_when_approving'); ?></label>
    </div>
    <div class="clearfix"></div>
    <hr>
  </div>

  <div class="list_approve <?php if($choose_when_approving == 1){echo 'hide';} ?>">
    <hr/>

    <?php if(!isset($approval_setting)) { ?>
      <div class="item_approve" class="row">
        <div class="col-md-11">
          <div class="col-md-6">
            <div class="select-placeholder form-group">
              <label for="approver[0]"><?php echo _l('approver'); ?></label>
              <select name="approver[0]" id="approver[0]" data-id="0" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" required="required">
                <option value=""></option>
                <option value="head_of_department"><?php echo _l('head_of_department'); ?></option>
                <option value="direct_manager"><?php echo _l('direct_manager'); ?></option>
                <option value="specific_personnel"><?php echo _l('specific_personnel'); ?></option>
              </select>
            </div> 
          </div>
          <div class="col-md-6 hide" id="is_staff_0">
            <div class="select-placeholder form-group">
              <label for="staff[0]"><?php echo _l('staff'); ?></label>
              <select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                <option value=""></option>
                <?php foreach($staffs as $val){ ?>
                  <option value="<?php echo $val['staffid']; ?>" <?php echo html_entity_decode($selected); ?>>
                   <?php echo get_staff_full_name($val['staffid']); ?>
                 </option>
               <?php } ?>
             </select>
           </div> 
         </div>
       </div>
       <div class="col-md-1">
        <span class="pull-bot">
          <br>
          <button name="add" class="btn new_vendor_requests btn-success mtop7" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
        </span>
      </div>
    </div>
  <?php }else{ 
    $setting = json_decode($approval_setting->setting);
    ?>
    <?php foreach ($setting as $key => $value) {
      $required = '';
      if($value->approver == 'specific_personnel'){
        $required = 'required="required"';          
      }
      ?>
      <div class="item_approve" class="row">
        <div class="col-md-11">
          <div class="col-md-6">
            <div class="select-placeholder form-group">
              <label for="approver[<?php echo html_entity_decode($key); ?>]"><?php echo _l('approver'); ?></label>
              <select name="approver[<?php echo html_entity_decode($key); ?>]" id="approver[<?php echo html_entity_decode($key); ?>]" data-id="<?php echo html_entity_decode($key); ?>" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" required="required">
                <option value=""></option>
                <option value="head_of_department" <?php if($value->approver == 'head_of_department'){ echo 'selected'; } ?>><?php echo _l('head_of_department'); ?></option>
                <option value="direct_manager" <?php if($value->approver == 'direct_manager'){ echo 'selected'; } ?>><?php echo _l('direct_manager'); ?></option>
                <option value="specific_personnel" <?php if($value->approver == 'specific_personnel'){ echo 'selected'; } ?>><?php echo _l('specific_personnel'); ?></option>
              </select>
            </div> 
          </div>
          <div class="col-md-6 <?php if($value->approver != 'specific_personnel'){ echo 'hide'; } ?>" id="is_staff_<?php echo html_entity_decode($key); ?>">
            <div class="select-placeholder form-group">
              <label for="staff[<?php echo html_entity_decode($key); ?>]"><?php echo _l('staff'); ?></label>
              <select name="staff[<?php echo html_entity_decode($key); ?>]" id="staff[<?php echo html_entity_decode($key); ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true" <?php echo html_entity_decode($required); ?>>
                <option value=""></option>
                <?php foreach($staffs as $val){
                  $selected = '';
                  if($val['staffid'] == $value->staff){
                    $selected = 'selected';
                  }
                  ?>
                  <option value="<?php echo html_entity_decode($val['staffid']); ?>" <?php echo html_entity_decode($selected); ?>>
                   <?php echo get_staff_full_name($val['staffid']); ?>
                 </option>
               <?php } ?>
             </select>
           </div> 
         </div>
       </div>
       <div class="col-md-1">
        <span class="pull-bot">
          <br>
          <?php if($key != 0){ ?>
            <button name="add" class="btn remove_vendor_requests btn-danger mtop7" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
          <?php }else{ ?>
            <button name="add" class="btn new_vendor_requests btn-success mtop7" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
          <?php } ?>
        </span>
      </div>
    </div>
  <?php } ?>

<?php } ?>
</div>


</div>