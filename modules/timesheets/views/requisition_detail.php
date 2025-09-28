<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$check = $this->input->get('check'); ?>
<?php if(isset($id)) { ?>
  <div id="wrapper">
    <div class="content">
      <div class="row">
        <div class="content mt-6 p-2 row">
          <div class="panel_s">
           <div class="panel-body w-100">
            <div class="wrap">

              <?php 
              $status['name'] = '';
              $status['color'] = '';

              ?>
              <div class="ribbonc" ><span><?php echo ts_htmldecode($status['name']); ?></span></div>
            </div>
            <div class="row">

            </div>
            <h4><?php echo _l('general_information'); ?></h4>
            <hr/>
            <div class="col-md-6">
              <table class="table border table-striped ">
                <tbody>
                  <tr>
                    <td><?php echo _l('subject'); ?></td>
                    <td id="subject_name"><?php echo ts_htmldecode($request_leave->subject); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('category_for_leave'); ?></td>
                    <td>
                      <?php   
                      $rel_type_text = '';
                      $type_of_leave = '';
                      if($request_leave->rel_type == 1){
                        $rel_type_text = 'Leave';
                        switch ($request_leave->type_of_leave) {
                          case 8:
                          $type_of_leave = _l('annual_leave');
                          break;
                          case 2:
                          $type_of_leave = _l('maternity_leave');
                          break;
                          case 4:
                          $type_of_leave = _l('private_work_without_pay');
                          break;
                          case 1:
                          $type_of_leave = _l('sick_leave');
                          break;
                        } 
                        if($type_of_leave == ''){
                          $type_of_leave = $this->timesheets_model->get_custom_leave_name_by_slug($request_leave->type_of_leave_text);
                        }
                      }elseif($request_leave->rel_type == 2){
                        $rel_type_text = 'late';
                      }elseif($request_leave->rel_type == 3){
                        $rel_type_text = 'Go_out';
                      }elseif($request_leave->rel_type == 4){
                        $rel_type_text = 'Go_on_bussiness';
                      }elseif($request_leave->rel_type == 5){
                        $rel_type_text = 'quit_job'; 
                      }elseif($request_leave->rel_type == 6){
                        $rel_type_text = 'early'; 
                      }
                      echo _l($rel_type_text); 
                      ?>                      
                    </td>
                  </tr>
                  <?php 
                  if($type_of_leave != ''){ 
                    ?>
                    <tr>
                      <td><?php echo _l('type_of_leave'); ?></td>
                      <td><?php echo ts_htmldecode($type_of_leave); ?></td>
                    </tr>
                  <?php }
                  ?>

                  <tr>
                    <td><?php echo _l('follower'); ?></td>
                    <td>
                      <?php 
                      if(is_numeric($request_leave->followers_id) && $request_leave->followers_id > 0){
                        $followers = explode(',', $request_leave->followers_id);
                        $views = '';
                        if(count($followers) > 0){
                          foreach($followers as $fl){
                            $views .= '<a href="' . admin_url('staff/profile/' . $fl) . '">' . staff_profile_image($fl,[
                              'staff-profile-image-small mright5',
                            ], 'small', [
                              'data-toggle' => 'tooltip',
                              'data-title'  => get_staff_full_name($fl),
                            ]) . '</a>';
                          }
                        }
                        echo ts_htmldecode($views);
                      }
                      ?>
                    </td>
                  </tr>

                  <!-- handover recipients -->
                  <?php if($rel_type == 'Leave'){  ?>
                    <tr>
                      <td><?php echo _l('handover_recipients'); ?></td>
                      <td>

                        <?php 
                        $handover_recipient = $request_leave->handover_recipients;
                        $views_handover_recipient = '';
                        if(($handover_recipient != null ) && $handover_recipient != ''){
                          $views_handover_recipient .= '<a href="' . admin_url('staff/profile/' . $handover_recipient) . '">' . staff_profile_image($handover_recipient,[
                            'staff-profile-image-small mright5',
                          ], 'small', [
                            'data-toggle' => 'tooltip',
                            'data-title'  => get_staff_full_name($handover_recipient),
                          ]) . '</a>';
                        }
                        echo ts_htmldecode($views_handover_recipient);
                      ?></td>
                    </tr>
                  <?php } ?>

                </tbody>
              </table>
            </div>

            <div class="col-md-6">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <td><?php echo _l('project_datecreated'); ?></td>
                    <td><?php 
                    $datecreated = $request_leave->datecreated;
                    if($datecreated == ''){
                      $datecreated = $request_leave->start_time;
                    }
                    echo _d($datecreated); ?></td>
                  </tr>
                  <tr>
                    <td><?php echo _l('time'); ?></td>
                    <?php if($request_leave->rel_type == 1){ ?>
                      <td>
                        <?php
                        if(strtotime($request_leave->start_time) == strtotime($request_leave->end_time)){
                          echo _d(date('Y-m-d', strtotime($request_leave->start_time)));                              
                        }
                        else{
                          echo _d(date('Y-m-d', strtotime($request_leave->start_time))).' - '._d(date('Y-m-d', strtotime($request_leave->end_time)));                               
                        }
                        ?>
                      </td>
                    <?php }
                    else if($request_leave->rel_type == 4){ ?>
                      <td>
                        <?php
                        if(strtotime($request_leave->start_time) == strtotime($request_leave->end_time)){
                          echo _dt(date('Y-m-d H:i:s', strtotime($request_leave->start_time)));                              
                        }
                        else{
                          echo _dt(date('Y-m-d H:i:s', strtotime($request_leave->start_time))).' - '._dt(date('Y-m-d H:i:s', strtotime($request_leave->end_time)));                               
                        }
                        ?>
                      </td>
                    <?php }
                    else
                      { ?>
                        <td><?php echo _dt($request_leave->start_time) ?></td>
                      <?php  } ?>
                    </tr>
                    <?php if($request_leave->rel_type == 1){ ?>
                     <tr>
                      <td><?php echo _l('Number_of_leaving_day'); ?></td>
                      <td><?php echo ts_htmldecode($request_leave->number_of_leaving_day); ?></td>
                    </tr>
                    <!-- <tr>
                      <td><?php echo _l('number_of_leave_days_allowed'); ?></td>
                      <td><?php 
                      if($request_leave->number_of_days != ''){
                        $number_day_off = $request_leave->number_of_days;
                      }
                      echo ts_htmldecode($number_day_off); 
                    ?></td>
                  </tr> -->
                <?php } ?>
                <tr>
                  <td><?php echo _l('reason'); ?></td>
                  <td><?php echo ts_htmldecode($request_leave->reason); ?></td>
                </tr>
              </tbody>
            </table>
          </div>


          <div class="col-md-12">
            <h4><?php echo _l('other_information') ?></h4>
            <hr/>
          </div>


          <div class="col-md-6">
            <table class="table table-striped">  
              <tbody>
                <tr>
                  <td><?php echo _l('requester'); ?></td>
                  <td>
                    <?php 
                    $_data = '<a href="' . admin_url('staff/profile/' . $request_leave->staff_id) . '">' . staff_profile_image($request_leave->staff_id, [
                      'staff-profile-image-small',
                    ]) . '</a>';
                    $_data .= ' <a href="' . admin_url('staff/profile/' . $request_leave->staff_id) . '">' . get_staff_full_name($request_leave->staff_id) . '</a>';
                    echo ts_htmldecode($_data);
                  ?></td>
                </tr>

                <tr>
                  <td><?php echo _l('email'); ?></td>
                  <td><?php echo ts_htmldecode($request_leave->email); ?></td>
                </tr>
                <tr>
                  <td><?php echo _l('department'); ?></td>
                  <td><?php echo ts_htmldecode($request_leave->name); ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <?php 
          if($request_leave->rel_type == 4 && count($advance_payment) > 0){ ?>
            <div class="col-md-6">
              <p class="bold text-success"><?php echo _l('advance_payment_money').': '; ?></p>
              <table class="table table-striped">  
                <tbody>
                  <tr>
                    <td><?php echo _l('used_to'); ?></td>
                    <td><?php echo _l('amount_of_money'); ?></td>
                  </tr>
                  <?php 
                  $sum_mn = 0;
                  foreach($advance_payment as $ad){
                    if($ad['amoun_of_money'] != ''){
                     ?>
                     <tr>
                      <td class="row_expense_name"><?php echo ts_htmldecode($ad['used_to']); ?></td>
                      <td><?php echo app_format_money($ad['amoun_of_money'],''); ?></td>
                    </tr>
                    <?php 
                    $sum_mn += $ad['amoun_of_money'];
                  }
                } ?>
                <tr>
                  <td><?php echo _l('total'); ?></td>
                  <td id="total_advance_payment"><?php echo app_format_money($sum_mn,''); ?></td>
                </tr>
                <tr>
                  <td><?php echo _l('advance_payment_reason'); ?></td>
                  <td><?php echo ts_htmldecode($advance_payment[0]['advance_payment_reason']); ?></td>
                </tr>
                <tr>
                  <td><?php echo _l('request_date'); ?></td>
                  <td id="request_date"><?php echo _d($advance_payment[0]['request_date']); ?></td>
                </tr>
              </tbody>
            </table>

            <?php if($request_leave->status == 1){  ?>
              <div class="row">
                <div class="col-lg-4">
                  <?php $amount_received = (isset($request_leave) ? app_format_money($request_leave->amount_received,'') : '');
                  ?>

                  <label for="amount_received" class="control-label"><?php echo _l('amount_received') ?></label>
                  <input type="text" id="amount_received" name="amount_received" class="form-control"  value="<?php echo ts_htmldecode($amount_received); ?>" aria-invalid="false" data-type="currency" required>
                </div>
                <div class="col-lg-4">
                  <?php $received_date = (isset($request_leave) ? _d($request_leave->received_date): '');
                  echo render_date_input('received_date','received_date',$received_date); ?>
                </div>
                <div class="col-lg-4 update-btn">          
                  <a href="javascript:void(0)" onclick="convert_to_expenses(<?php echo ts_htmldecode($request_leave->id); ?>); return false;" class="btn btn-info pull-right"><?php echo _l('convert_to_expenses'); ?></a>
                </div>
              </div>
            <?php } ?>

          </div>
        <?php } ?>     
        <div class="row col-md-12">
         <div class="col-md-12 ">
          <h4><?php echo _l('Authentication_Info') ?></h4>
          <hr/>
        </div>
        <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
          <?php echo _l('contract_attachments'); ?>            
        </a>                 

        <div id="contract_attachments" class="mtop30">
         <?php    
         $href_url = '';
         $data = '<div class="row">';
         foreach($request_leave->attachments as $attachment) {
          $href_url = site_url('modules/timesheets/uploads/requisition_leave/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
          $data .= '<div class="display-block contract-attachment-wrapper">';
          $data .= '<div class="col-md-10">';
          $data .= '<div class="col-md-1">';
          $data .= '<a class="btn btn-info pull-right display-block" data-file='.$attachment['id'].' data-id='.$attachment['rel_id'].' onclick="preview_asset_btn(this)">';
          $data .= '<i class="fa fa-eye" ></i>'; 
          $data .= '</a>';
          $data .= '</div>';
          $data .= '<div class=col-md-9>';
          $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
          $data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
          $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
          $data .= '</div>';
          $data .= '</div>';
          $data .= '<div class="col-md-2 text-right">';
          if($attachment['staffid'] == get_staff_user_id() || is_admin()){
           $data .= '<a href="#" class="text-danger" onclick="delete_requisition_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
         }
         $data .= '</div>';
         $data .= '<div class="clearfix"></div><hr/>';
         $data .= '</div>';
       }
       $data .= '</div>';
       echo ts_htmldecode($data);
       ?>

     </div>
   </div>
   <div class="col-md-12">
    <h4 class="bold"><?php echo _l('approval_information'); ?></h4>
    <hr/>

    <div class="project-overview-right">
      <?php 
      if(count($list_approve_status) > 0){ ?>

       <div class="row">
         <div class="col-md-12 project-overview-expenses-finance">
          <?php 
          $this->load->model('staff_model');
          foreach ($list_approve_status as $value) { 
            $value['staffid'] = explode(', ',$value['staffid']);
            ?>
            <div class="col-md-3 text-center">
             <p class="text-uppercase text-muted no-mtop bold">
              <?php
              $staff_name = '';
              foreach ($value['staffid'] as $key => $val) {
                if($staff_name != '')
                {
                  $staff_name .= ' or ';
                }
                $staff_name .= get_staff_full_name($val);
              }
              echo ts_htmldecode($staff_name); 
            ?></p>
            <?php if($value['approve'] == 1){ 
              ?>
              <img src="<?php echo site_url(TIMESHEETS_PATH.'approval/approved.png'); ?>" >
            <?php }elseif($value['approve'] == 2){ ?>
              <img src="<?php echo site_url(TIMESHEETS_PATH.'approval/rejected.png'); ?>" >
            <?php } ?> 
            <br><br>  
            <p class="bold text-center text-<?php if($value['approve'] == 1){ echo 'success'; }elseif($value['approve'] == 2){ echo 'danger'; } ?>"><?php echo _dt($value['date']); ?></p> 
            <p class="text-center"><?php echo ts_htmldecode($value['note']); ?></p> 
          </div>
          <?php 
        } ?>
      </div>
    </div>
  <?php } ?>
</div>
<div class="pull-left">
  <?php if($request_leave->status == 0 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
    <div id="choose_approver">
      <?php if($check != 'choose'){ ?>
        <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo ts_htmldecode($request_leave->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
      <?php } ?>

      <?php if($check == 'choose'){ 
       $html = '<div class="col-md-12">';
       $html .= '<div class="col-md-9"><select name="approver_c" class="selectpicker" data-live-search="true" id="approver_c" data-width="100%" data-none-selected-text="'. _l('please_choose_approver').'" required> 
       <option value=""></option>'; 
       $current_user = get_staff_user_id();
       foreach($list_staff as $staff){ 
        if($staff['staffid'] != $current_user){
          $html .= '<option value="'.$staff['staffid'].'">'.$staff['staff_identifi'].' - '.$staff['firstname'].' '.$staff['lastname'].'</option>';                  
        }
      }
      $html .= '</select></div>';

      $html .= '<div class="col-md-3"><a href="#" onclick="choose_approver();" class="btn btn-success lead-top-btn lead-view" data-loading-text="'._l('wait_text').'">'._l('choose').'</a></div>';

      $html .= '</div>';

      echo ts_htmldecode($html);
    }
    ?>


  </div>
<?php } 
if(isset($check_approve_status['staffid'])){
  ?>
  <?php 
  if(in_array(get_staff_user_id(), $check_approve_status['staffid'])){ ?>
    <div class="btn-group" >
     <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
     <ul class="dropdown-menu dropdown-menu-left w300px">
      <li>
        <div class="col-md-12">
          <?php echo render_textarea('reason', 'reason'); ?>
        </div>
      </li>
      <li>
        <div class="col-md-12 text-right bottom-approve">
          <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo ts_htmldecode($request_leave->id); ?>); return false;" class="btn btn-success mright5"><?php echo _l('approve'); ?></a>
          <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo ts_htmldecode($request_leave->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
          <div class="clearfix"></div>
          <br>
        </li>
      </ul>

    </div>
  <?php }
  ?>
  <?php 
}
?>
<?php if($rel_type == 'Leave' && $request_leave->status == 1 && is_admin() && date('Y-m-t', strtotime($datecreated)) == date('Y-m-t')){ ?>
  <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="cancel_request(<?php echo ts_htmldecode($request_leave->id); ?>); return false;" class="btn btn-warning"><?php echo _l('cancel_approval'); ?></a>
<?php } ?>
</div>

</div>   

<div class="clearfix"></div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>

</div>
</div>
</div>
<div id="asset_file_data">
</div>
</div>
</div>
<div class="modal fade" id="convert_expense" tabindex="-1" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <?php echo form_open(admin_url('timesheets/advance_payment_update'),array('id'=>'advance_payment_update-form','class'=>'dropzone dropzone-manual')); ?>
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title"><?php echo _l('add_new', _l('expense_lowercase')); ?></h4>
  </div>
  <div class="modal-body">
    <div id="dropzoneDragArea" class="dz-default dz-message">
     <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
   </div>
   <div class="dropzone-previews"></div>
   <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('expense_name_help'); ?>"></i>
   <?php echo render_input('expense_name','expense_name'); ?>
   <?php echo render_textarea('note','expense_add_edit_note','',array('rows'=>4),array()); ?>
   <?php
   $this->load->model('clients_model');
   $customers = $this->clients_model->get();
   echo render_select('clientid',$customers,array('userid','company'),'customer'); ?>
   <?php
   $this->load->model('expenses_model');
   $categories = $this->expenses_model->get_category();

   if(is_admin() || get_option('staff_members_create_inline_expense_categories') == '1'){
    echo render_select_with_input_group('category',$categories,array('id','name'),'expense_category', '','<a href="#" onclick="new_category();return false;"><i class="fa fa-plus"></i></a>');
  } else {
    echo render_select('category',$categories,array('id','name'),'expense_category', '');
  }
  ?>
  <?php echo render_date_input('date','expense_add_edit_date',_d(date('Y-m-d'))); ?>
  <?php echo render_input('amount','expense_add_edit_amount','','number');
  $this->load->model('taxes_model');
  $taxes = $this->taxes_model->get();
  ?>
  <div class="row mbot15">
   <div class="col-md-6">
    <div class="form-group">
     <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
     <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
      <option value=""><?php echo _l('no_tax'); ?></option>
      <?php foreach($taxes as $tax){ ?>
        <option value="<?php echo ts_htmldecode($tax['id']); ?>" data-subtext="<?php echo ts_htmldecode($tax['name']); ?>"><?php echo ts_htmldecode($tax['taxrate']); ?>%</option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="col-md-6">
  <div class="form-group">
   <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
   <select class="selectpicker display-block" data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" disabled>
    <option value=""><?php echo _l('no_tax'); ?></option>
    <?php foreach($taxes as $tax){ ?>
      <option value="<?php echo ts_htmldecode($tax['id']); ?>" data-subtext="<?php echo ts_htmldecode($tax['name']); ?>"><?php echo ts_htmldecode($tax['taxrate']); ?>%</option>
    <?php } ?>
  </select>
</div>
</div>
</div>
<?php
$this->load->model('currencies_model');
$currencies = $this->currencies_model->get();
$currency_attr = array('disabled'=>true,'data-show-subtext'=>true);

$currency_attr = apply_filters_deprecated('expense_currency_disabled', [$currency_attr], '2.3.0', 'expense_currency_attributes');

foreach($currencies as $currency){
  if($currency['isdefault'] == 1){
    $currency_attr['data-base'] = $currency['id'];
  }
  if(isset($expense)){
    if($currency['id'] == $expense->currency){
      $selected = $currency['id'];
    }
    if($expense->billable == 0){
      if($expense->clientid != 0){
        $c = $this->clients_model->get_customer_default_currency($expense->clientid);
        if($c != 0){
          $customer_currency = $c;
        }
      }
    }
  } else {
    if(isset($customer_id)){
      $c = $this->clients_model->get_customer_default_currency($customer_id);
      if($c != 0){
        $customer_currency = $c;
      }
    }
    if($currency['isdefault'] == 1){
      $selected = $currency['id'];
    }
  }
}
$currency_attr = hooks()->apply_filters('expense_currency_attributes', $currency_attr);
?>
<input type="hidden" name="currency" value="<?php echo ts_htmldecode($selected); ?>">
<div id="expense_currency">
 <?php echo render_select('currency', $currencies, array('id','name','symbol'), 'expense_currency', $selected, $currency_attr); ?>
</div>
<div class="checkbox checkbox-primary">
 <input type="checkbox" id="billable" name="billable" checked>
 <label for="billable"><?php echo _l('expense_add_edit_billable'); ?></label>
</div>
<?php echo render_input('reference_no','expense_add_edit_reference_no'); ?>
<?php
// Fix becuase payment modes are used for invoice filtering and there needs to be shown all
// in case there is payment made with payment mode that was active and now is inactive
$this->load->model('payment_modes_model');
$payment_modes = $this->payment_modes_model->get('', [
  'invoices_only !=' => 1,
]);
$expenses_modes = array();
foreach($payment_modes as $m){
 if(isset($m['invoices_only']) && $m['invoices_only'] == 1) {continue;}
 if($m['active'] == 1){
   $expenses_modes[] = $m;
 }
}
?>
<?php

echo render_select('paymentmode',$expenses_modes,array('id','name'),'payment_mode'); ?>
<div class="clearfix mbot15"></div>
<?php echo render_custom_fields('expenses'); ?>
<div id="pur_order_additional"></div>
<div class="clearfix"></div>
</div>


<input type="hidden" name="amount_received" value="">
<input type="hidden" name="received_date" value="">
<input type="hidden" name="id" value="">

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>

<div class="modal fade" id="expense-category-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open(admin_url('timesheets/add_expense_category'),array('id'=>'expense-category-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="edit-title"><?php echo _l('edit_expense_category'); ?></span>
          <span class="add-title"><?php echo _l('new_expense_category'); ?></span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <input type="hidden" name="leave_id" value="<?php echo ts_htmldecode($request_leave->id); ?>">
            <?php echo render_input('name','expense_add_edit_name'); ?>
            <?php echo render_textarea('description','expense_add_edit_description'); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
    </div><!-- /.modal-content -->
    <?php echo form_close(); ?>
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<input type="hidden" name="has_send_mail" value="<?php echo ts_htmldecode($has_send_mail); ?>">

<?php } ?>
<?php init_tail(); ?>
<?php require 'modules/timesheets/assets/js/requisition_detail_js.php'; ?>
