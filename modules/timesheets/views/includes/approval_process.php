
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="row">
 <div class="col-md-12" >

  <div class="_buttons">
      <a href="<?php echo admin_url('timesheets/new_approval_setting'); ?>" class="btn btn-info pull-left" ><?php echo _l('new'); ?></a>
  </div>
  <div class="clearfix"></div>
  <hr class="hr-panel-heading" />
  <div class="clearfix"></div>
  <table class="table dt-table">
    <thead>
      <th><?php echo _l('name'); ?></th>
      <th><?php echo _l('related'); ?></th>
      <th><?php echo _l('options'); ?></th>
    </thead>
    <tbody>
      <?php foreach($approval_setting as $value){ ?>
        <tr>
         <td><?php echo html_entity_decode($value['name']); ?></td>
         <td><?php
         $related = '';
         switch ($value['related']) {
           case 'additional_timesheets':
           $related = _l('additional_timesheets');
           break;
           case 'missed_punch':
           $related = _l('missed_punch');
           break;
           case 'Leave':
           $related = _l('annual_leave');
           break;
           case 'maternity_leave':
           $related = _l('maternity_leave');
           break;
           case 'private_work_without_pay':
           $related = _l('private_work_without_pay');
           break;
           case 'sick_leave':
           $related = _l('sick_leave');
           break;
           case 'late':
           $related = _l('late');
           break;
           case 'early':
           $related = _l('early');
           break;
           case 'Go_out':
           $related = _l('Go_out');
           break;
           case 'Go_on_bussiness':
           $related = _l('Go_on_bussiness');
           break;
         }

         if($related == ''){
          $related = $this->timesheets_model->get_custom_leave_name_by_slug($value['related']);
        }

        echo html_entity_decode($related); ?>

      </td>
      <td>
         <a href="<?php echo admin_url('timesheets/edit_approval_setting/'.$value['id']); ?>" data-name="<?php echo html_entity_decode($value['name']); ?>" data-related="<?php echo html_entity_decode($value['related']); ?>" data-setting='<?php echo html_entity_decode($value['setting']); ?>' class="btn btn-default btn-icon"><i class="fa fa-pen-to-square"></i></a>

        <a href="<?php echo admin_url('timesheets/delete_approval_setting/'.$value['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
    </td>
  </tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

<div class="modal" id="approval_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog max-width-1000" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          <span class="edit-title"><?php echo _l('edit_approval_setting'); ?></span>
          <span class="add-title"><?php echo _l('new_approval_setting'); ?></span>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <?php echo form_open('accounting/approval_setting',array('id'=>'approval-setting-form')); ?>
      <?php echo form_hidden('approval_setting_id'); ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php echo render_input('name','subject','','text'); ?>
            <?php $related = [ 
              0 => ['id' => 'acc_receipt', 'name' => _l('acc_receipt')],
              1 => ['id' => 'acc_payslip', 'name' => _l('acc_payslip')],
              2 => ['id' => 'acc_debit', 'name' => _l('acc_debit')],
              3 => ['id' => 'acc_credit', 'name' => _l('acc_credit')]
            ]; ?>
            <?php echo render_select('related',$related,array('id','name'),'task_single_related'); ?>
            <div class="list_approve">
              <div id="item_approve">
                <div class="col-md-11">
                  <div class="col-md-6">
                    <div class="select-placeholder form-group">
                      <label for="approver[0]"><?php echo _l('approver'); ?></label>
                      <select name="approver[0]" id="approver[0]" data-id="0" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" required>
                        <option value=""></option>
                        <option value="direct_manager"><?php echo _l('direct_manager'); ?></option>
                        <option value="department_manager"><?php echo _l('department_manager'); ?></option>
                        <option value="staff"><?php echo _l('staff'); ?></option>
                      </select>
                    </div> 
                  </div>
                  <div class="col-md-6 hide" id="is_staff_0">
                    <div class="select-placeholder form-group">
                      <label for="staff[0]"><?php echo _l('staff'); ?></label>
                      <select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true">
                        <option value=""></option>
                        <?php foreach($staffs as $val){
                         $selected = '';
                         ?>
                         <option value="<?php echo html_entity_decode($val['staffid']); ?>">
                           <?php echo get_staff_full_name($val['staffid']); ?>
                         </option>
                       <?php } ?>
                     </select>
                   </div> 
                 </div>
               </div>
               <div class="col-md-1 content-nowrap">
                <span class="pull-bot">
                  <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
</div>

