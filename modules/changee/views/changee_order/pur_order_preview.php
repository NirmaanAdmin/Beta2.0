<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id', $estimate->id); ?>
<?php echo form_hidden('_attachment_sale_type', 'estimate'); ?>
<?php
$base_currency = changee_get_base_currency_pur();
if ($estimate->currency != 0) {
   $base_currency = changee_pur_get_currency_by_id($estimate->currency);
}
?>
<div class="col-md-12 no-padding">
   <div class="panel_s">
      <div class="panel-body">
         <?php if ($estimate->approve_status == 1) { ?>
            <div class="ribbon info span_style"><span><?php echo _l('changee_draft'); ?></span></div>
         <?php } elseif ($estimate->approve_status == 2) { ?>
            <div class="ribbon success"><span><?php echo _l('changee_approved'); ?></span></div>
         <?php } elseif ($estimate->approve_status == 3) { ?>
            <div class="ribbon warning"><span><?php echo _l('pur_rejected'); ?></span></div>
         <?php } elseif ($estimate->approve_status == 4) { ?>
            <div class="ribbon danger"><span><?php echo _l('cancelled'); ?></span></div>
         <?php  } ?>
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                        <?php echo _l('pur_order'); ?>
                     </a>
                  </li>
                  <li role="presentation" class="tab-separator">
                     <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                        <?php echo _l('attachment'); ?>
                     </a>
                  </li>
                  <!-- <li role="presentation">
                     <a href="#payment_record" aria-controls="payment_record" role="tab" data-toggle="tab">
                        <?php echo _l('payment_record'); ?>
                     </a>
                  </li> -->

                  <li role="presentation">
                     <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                        <?php echo _l('invoice_view_activity_tooltip'); ?>
                     </a>
                  </li>

                  <li role="presentation">
                     <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo changee_pur_html_entity_decode($estimate->id); ?> + '/' + 'changee_order', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                        <?php echo _l('estimate_reminders'); ?>
                        <?php
                        $total_reminders = total_rows(
                           db_prefix() . 'reminders',
                           array(
                              'isnotified' => 0,
                              'staff' => get_staff_user_id(),
                              'rel_type' => 'changee_order',
                              'rel_id' => $estimate->id
                           )
                        );
                        if ($total_reminders > 0) {
                           echo '<span class="badge">' . $total_reminders . '</span>';
                        }
                        ?>
                     </a>
                  </li>
                  <?php
                  $customer_custom_fields = false;
                  if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'pur_order', 'active' => 1)) > 0) {
                     $customer_custom_fields = true;
                  ?>
                     <li role="presentation">
                        <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
                           <?php echo _l('custom_fields'); ?>
                        </a>
                     </li>
                  <?php } ?>
                  <li role="presentation">
                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo changee_pur_html_entity_decode($estimate->id); ?>,'changee_order'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                        <?php echo _l('tasks'); ?>
                     </a>
                  </li>
                  <li role="presentation" class="tab-separator">
                     <a href="#tab_notes" onclick="get_sales_notes(<?php echo changee_pur_html_entity_decode($estimate->id); ?>,'changee'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                        <?php echo _l('estimate_notes'); ?>
                        <span class="notes-total">
                           <?php $totalNotes       = total_rows(db_prefix() . 'notes', ['rel_id' => $estimate->id, 'rel_type' => 'changee_order']);
                           if ($totalNotes > 0) { ?>
                              <span class="badge"><?php echo ($totalNotes); ?></span>
                           <?php } ?>
                        </span>
                     </a>
                  </li>

                  <li role="presentation" class="tab-separator">
                     <?php
                     $totalComments = total_rows(db_prefix() . 'co_comments', ['rel_id' => $estimate->id, 'rel_type' => 'pur_order']);
                     ?>
                     <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                        <?php echo _l('pur_discuss'); ?>
                        <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                     </a>
                  </li>

                  <!-- <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                     <a href="#" onclick="small_table_full_view(); return false;">
                     <i class="fa fa-expand"></i></a>
                  </li> -->
               </ul>
            </div>
         </div>
         <div class="row">
            <div class="col-md-4" style="padding: 0px 0px 0px 10px">
               <p class="bold p_mar"><?php echo _l('vendor') . ': ' ?><a href="<?php echo admin_url('changee/vendor/' . $estimate->vendor); ?>"><?php echo changee_get_vendor_company_name($estimate->vendor); ?></a></p>
               <?php
               $order_status_class = '';
               $order_status_text = '';
               if ($estimate->order_status == 'new') {
                  $order_status_class = 'label-info';
                  $order_status_text = _l('new_order');
               } else if ($estimate->order_status == 'delivered') {
                  $order_status_class = 'label-success';
                  $order_status_text = _l('delivered');
               } else if ($estimate->order_status == 'confirmed') {
                  $order_status_class = 'label-warning';
                  $order_status_text = _l('confirmed');
               } else if ($estimate->order_status == 'cancelled') {
                  $order_status_class = 'label-danger';
                  $order_status_text = _l('cancelled');
               } else if ($estimate->order_status == 'return') {
                  $order_status_class = 'label-warning';
                  $order_status_text = _l('pur_return');
               }
               ?>

               <?php if ($estimate->order_status != null) { ?>
                  <p class="bold p_mar"><?php echo _l('order_status') . ': '; ?><span class="label <?php echo changee_pur_html_entity_decode($order_status_class); ?>"><?php echo changee_pur_html_entity_decode($order_status_text); ?></span></p>
               <?php } ?>

               <!-- <?php $clients_ids = explode(',', $estimate->clients ?? ''); ?>
               <?php if (count($clients_ids) > 0) { ?>

                  <p class="bold p_mar"><?php echo _l('clients') . ': ' ?></p>
                  <?php foreach ($clients_ids as $ids) {
                  ?>
                     <a href="<?php echo admin_url('clients/client/' . $ids); ?>"><span class="label label-tag"><?php echo get_company_name($ids); ?></span></a>
                  <?php } ?>
               <?php } ?> -->
               <?php
               if ($pur_order->group_pur > 0) { ?>
               <p class="bold p_mar"><?php echo _l('group_pur') . ': ' ?> <?php foreach ($commodity_groups as $group) {
                  if ($group['id'] == $pur_order->group_pur) {
                     echo $group['name'];
                  }
               } ?> </p>
               <?php }
               if ($pur_order->sub_groups_pur > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('sub_groups_pur') . ': ' ?> <?php foreach ($sub_groups as $group) {
                     if ($group['id'] == $pur_order->sub_groups_pur) {
                        echo $group['sub_group_name'];
                     }
                  } ?> </p>
               <?php }
               if ($pur_order->area_pur > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('area_pur') . ': ' ?> <?php foreach ($area as $area) {
                     if ($area['id'] == $pur_order->area_pur) {
                        echo $area['area_name'];
                     }
                  } ?> </p>
               <?php }
               if ($pur_order->po_order_id > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('releted_to') . ': ' ?><a href="<?php echo admin_url('purchase/purchase_order/' . $pur_order->po_order_id); ?>"><?php echo get_pur_name_by_id($pur_order->po_order_id); ?></a></p>
               <?php }
               if ($pur_order->wo_order_id > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('releted_to') . ': ' ?><a href="<?php echo admin_url('purchase/work_order/' . $pur_order->wo_order_id); ?>"><?php echo get_wo_order_name_by_id($pur_order->wo_order_id); ?></a></p>
               <?php } ?>
               <?php
               if ($pur_order->kind > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('kind') . ': ' ?> <?php echo $pur_order->kind ?> </p>
               <?php }
               if ($pur_order->buyer > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('buyer') . ': ' ?> <?php echo get_staff_full_name($pur_order->buyer); ?> </p>
               <?php }
               if ($pur_order->project > 0) { ?>
                  <p class="bold p_mar"><?php echo _l('project') . ': ' ?> <?php echo get_project_name_by_id($pur_order->project); ?> </p>
               <?php } ?>

            </div>
            <div class="col-md-8">
               <div class="btn-group pull-right">
                  <a href="javascript:void(0)" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if (is_mobile()) {
                     echo ' PDF';
                  } ?> <span class="caret"></span></a>
                  <ul class="dropdown-menu dropdown-menu-right">
                     <li class="hidden-xs"><a href="<?php echo admin_url('changee/purorder_pdf/' . $estimate->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                     <li class="hidden-xs"><a href="<?php echo admin_url('changee/purorder_pdf/' . $estimate->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                     <li><a href="<?php echo admin_url('changee/purorder_pdf/' . $estimate->id); ?>"><?php echo _l('download'); ?></a></li>
                     <li>
                        <a href="<?php echo admin_url('changee/purorder_pdf/' . $estimate->id . '?print=true'); ?>" target="_blank">
                           <?php echo _l('print'); ?>
                        </a>
                     </li>
                  </ul>

                  <a href="javascript:void(0)" onclick="send_po('<?php echo changee_pur_html_entity_decode($estimate->id); ?>'); return false;" class="btn btn-success mleft10"><i class="fa fa-envelope" data-toggle="tooltip" title="<?php echo _l('send_to_vendor') ?>"></i></a>
               </div>

               <?php if (is_admin()) { ?>
                  <div class="btn-group pull-right mright5">
                     <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo _l('more'); ?> <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right">
                        <?php if (has_permission('changee_order_return', '', 'edit') &&  $estimate->status == 'confirm') { ?>
                           <li>
                              <a href="#" onclick="refund_order_return(); return false;" id="order_return_refund">
                                 <?php echo _l('refund'); ?>
                              </a>
                           </li>
                        <?php } ?>

                        <?php if (is_admin()) { ?>
                           <?php
                           $statuses = [
                              'new',
                              'delivered',
                              'confirmed',
                              'cancelled',
                              'return',
                           ];
                           ?>

                           <?php foreach ($statuses as $status) { ?>
                              <?php if ($status != $estimate->order_status) { ?>
                                 <li>
                                    <a href="<?php echo admin_url('changee/mark_pur_order_as/' . $status . '/' . $estimate->id); ?>"><?php echo _l('invoice_mark_as', _l($status)); ?></a>
                                 </li>
                              <?php } ?>
                           <?php } ?>
                        <?php } ?>

                        <?php if (has_permission('changee_order_return', '', 'edit') && $estimate->approve_status != 2) { ?>
                           <li>
                              <a href="<?php echo admin_url('changee/pur_order/' . $estimate->id); ?>"><?php echo _l('edit'); ?></a>
                           </li>
                        <?php } ?>


                        <?php if (has_permission('changee_order_return', '', 'delete')) { ?>
                           <li>
                              <a href="<?php echo admin_url('changee/delete_pur_order/' . $estimate->id); ?>" class="text-danger delete-text _delete"><?php echo _l('delete_invoice'); ?></a>
                           </li>
                        <?php } ?>


                     </ul>
                  </div>
               <?php } ?>

               <?php if ($estimate->approve_status != 2) { ?>
                  <div class="pull-right _buttons mright10">
                     <?php if (has_permission('changee_orders', '', 'edit')) { ?>
                        <a href="<?php echo admin_url('changee/pur_order/' . $estimate->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('edit'); ?>" data-placement="bottom"><i class="fa fa-pencil-square"></i></a>
                     <?php } ?>

                  </div>
               <?php } ?>

               <?php if (has_permission('changee_order_change_approve_status', '', 'edit') && $check_approval_setting == true) { ?>
                  <select name="status" id="status" class="selectpicker pull-right mright10" onchange="change_status_pur_order(this,<?php echo ($estimate->id); ?>); return false;" data-live-search="true" data-width="35%" data-none-selected-text="<?php echo _l('pur_change_status_to'); ?>">
                     <option value=""></option>
                     <option value="1" class="<?php if ($estimate->approve_status == 1) {
                                                   echo 'hide';
                                                } ?>"><?php echo _l('changee_draft'); ?></option>
                     <option value="2" class="<?php if ($estimate->approve_status == 2) {
                                                   echo 'hide';
                                                } ?>"><?php echo _l('changee_approved'); ?></option>
                     <option value="3" class="<?php if ($estimate->approve_status == 3) {
                                                   echo 'hide';
                                                } ?>"><?php echo _l('pur_rejected'); ?></option>
                     <option value="4" class="<?php if ($estimate->approve_status == 4) {
                                                   echo 'hide';
                                                } ?>"><?php echo _l('pur_canceled'); ?></option>
                  </select>
               <?php } ?>
               <div class="pull-right" style="margin-right: 10px;font-size: 18px;margin-top: 4px;">
                  <a href="#" onclick="small_table_full_view(); return false;">
                     <i class="fa fa-expand" style="color: #000000 !important;"></i></a>
               </div>
               <div class="col-md-12 padr_div_0">
                  <br>
                  <div class="pull-right _buttons  ">
                     <a href="javascript:void(0)" onclick="copy_public_link(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-warning btn-with-tooltip mleft10" data-toggle="tooltip" title="<?php if ($estimate->hash == '') {
                        echo _l('create_public_link');
                     } else {
                        echo _l('copy_public_link');
                     } ?>" data-placement="bottom"><i class="fa fa-clone "></i></a>
                  </div>
                  <div class="pull-right col-md-6">
                     <?php if ($estimate->hash != '' && $estimate->hash != null) {
                        echo render_input('link_public', '', site_url('changee/vendors_portal/pur_order/' . $estimate->id . '/' . $estimate->hash));
                     } else {
                        echo render_input('link_public', '', '');
                     } ?>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">

            <?php if ($customer_custom_fields) { ?>
               <div role="tabpanel" class="tab-pane" id="custom_fields">
                  <?php echo form_open(admin_url('changee/update_customfield_po/' . $estimate->id)); ?>
                  <?php $rel_id = (isset($estimate) ? $estimate->id : false); ?>
                  <?php echo render_custom_fields('pur_order', $rel_id); ?>

                  <div class="bor_top_0">
                     <button id="obgy_btn2" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  </div>
                  <?php echo form_close(); ?>
               </div>
            <?php } ?>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
               <?php init_relation_tasks_table(array('data-new-rel-id' => $estimate->id, 'data-new-rel-type' => 'changee_order')); ?>
            </div>
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
               <div id="estimate-preview">
                  <div class="row">

                     <div class="<?php if (!is_mobile()) {
                                    echo 'pull-right';
                                 } ?> mleft5 mright5">
                        <?php if ($check_appr && $check_appr != false) {
                           if ($estimate->approve_status == 1) { ?>
                              <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
                           <?php }
                        }
                        if (isset($check_approve_status['staffid'])) {
                           ?>
                           <?php
                           if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $estimate->status == 1) { ?>
                              <div class="btn-group">
                                 <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                                 <ul class="dropdown-menu dropdown-menu-<?php if (is_mobile()) {
                                                                           echo 'left';
                                                                        } else {
                                                                           echo 'right';
                                                                        } ?> ul_style">
                                    <li>
                                       <div class="col-md-12">
                                          <?php echo render_textarea('reason', 'reason'); ?>
                                       </div>
                                    </li>
                                    <li>
                                       <div class="row text-right col-md-12">
                                          <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success mright15"><?php echo _l('approve'); ?></a>
                                          <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                           <?php }
                           ?>

                           <?php
                           if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign)) { ?>
                              <button onclick="accept_action();" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
                           <?php }
                           ?>
                        <?php
                        }
                        ?>
                     </div>

                     <div class="project-overview-right">
                        <?php if (count($list_approve_status) > 0) { ?>

                           <div class="row">
                              <div class="col-md-12 project-overview-expenses-finance">
                                 <?php
                                 $this->load->model('staff_model');
                                 $enter_charge_code = 0;
                                 foreach ($list_approve_status as $value) {
                                    $value['staffid'] = explode(', ', $value['staffid'] ?? '');
                                    if ($value['action'] == 'sign') {
                                 ?>
                                       <div class="col-md-4 apr_div">
                                          <p class="text-uppercase text-muted no-mtop bold">
                                             <?php
                                             $staff_name = '';
                                             $st = _l('status_0');
                                             $color = 'warning';
                                             foreach ($value['staffid'] as $key => $val) {
                                                if ($staff_name != '') {
                                                   $staff_name .= ' or ';
                                                }
                                                $staff_name .= $this->staff_model->get($val)->firstname;
                                             }
                                             echo changee_pur_html_entity_decode($staff_name);
                                             ?></p>
                                          <?php if ($value['approve'] == 2) {
                                          ?>
                                             <img src="<?php echo site_url(PURCHASE_PATH . 'pur_order/signature/' . $estimate->id . '/signature_' . $value['id'] . '.png'); ?>" class="img_style">
                                             <br><br>
                                             <p class="bold text-center text-success"><?php echo _l('signed') . ' ' . _dt($value['date']); ?></p>
                                          <?php } ?>

                                       </div>
                                    <?php } else { ?>
                                       <div class="col-md-4 apr_div">
                                          <p class="text-uppercase text-muted no-mtop bold">
                                             <?php
                                             $staff_name = '';
                                             foreach ($value['staffid'] as $key => $val) {
                                                if ($staff_name != '') {
                                                   $staff_name .= ' or ';
                                                }
                                                $staff_name .= $this->staff_model->get($val)->firstname;
                                             }
                                             echo changee_pur_html_entity_decode($staff_name);
                                             ?></p>
                                          <?php if ($value['approve'] == 2) {
                                          ?>
                                             <?php if ($value['approve_by_admin'] == 1) { ?>
                                                <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved_by_admin.png'); ?>" class="img_style">
                                             <?php } else { ?>
                                                <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved.png'); ?>" class="img_style">
                                             <?php } ?>
                                          <?php } elseif ($value['approve'] == 3) { ?>
                                             <img src="<?php echo site_url(PURCHASE_PATH . 'approval/rejected.png'); ?>" class="img_style">
                                          <?php } ?>
                                          <br><br>
                                          <p><?php echo changee_pur_html_entity_decode($value['note']) ?></p>
                                          <p class="bold text-center text-<?php if ($value['approve'] == 2) {
                                                                              echo 'success';
                                                                           } elseif ($value['approve'] == 3) {
                                                                              echo 'danger';
                                                                           } ?>"><?php echo _dt($value['date']); ?></p>
                                       </div>
                                 <?php }
                                 } ?>
                              </div>
                           </div>

                        <?php } ?>
                     </div>

                     <?php if ($estimate->estimate != 0) { ?>
                        <div class="col-md-12">
                           <h4 class="font-medium mbot15"><?php echo _l('', array(
                            '',
                            '',
                            '<a href="' . admin_url('changee/quotations/' . $estimate->estimate) . '" target="_blank">' . changee_format_pur_estimate_number($estimate->id) . '</a>',
                           )); ?></h4>
                        </div>
                     <?php } ?>
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold mbot5">

                           <a href="<?php echo admin_url('changee/changee_order/' . $estimate->id); ?>">
                              <span id="estimate-number">
                                 <?php echo changee_pur_html_entity_decode($estimate->pur_order_number . ' - ' . $estimate->pur_order_name); ?>
                              </span>
                           </a>
                        </h4>

                        <address class="mbot5">
                           <?php echo format_organization_info(); ?>
                        </address>

                        <?php $custom_fields = get_custom_fields('pur_order');
                        foreach ($custom_fields as $field) { ?>
                           <?php $value = get_custom_field_value($estimate->id, $field['id'], 'pur_order');
                           if ($value == '') {
                              continue;
                           } ?>
                           <div class="task-info">
                              <h5 class="task-info-custom-field task-info-custom-field-<?php echo $field['id']; ?>">
                                 <i class="fa task-info-icon fa-fw fa-lg fa-pencil-square"></i>
                                 <?php echo $field['name']; ?>: <?php echo $value; ?>
                              </h5>
                           </div>
                        <?php } ?>
                     </div>


                  </div>
                  <div class="row">
                     <div class="col-md-12">

                        <?php if ($estimate->approve_status != 2) { ?>
                           <a href="javascript:void(0)" onclick="refresh_order_value(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-sm btn-warning hide" data-toggle="tooltip" data-placement="top" title="<?php echo _l('refresh_value_note'); ?>"><i class="fa fa-refresh"></i> <?php echo ' ' . _l('refresh_order_value'); ?></a>
                        <?php } ?>


                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center"><?php echo _l('serial_no'); ?></th>
                                    <th width="15%" align="left"><?php echo _l('debit_note_table_item_heading'); ?></th>
                                    <th width="25%" align="left" class="qty"><?php echo _l('decription'); ?></th>
                                    <th width="15%" align="left"><?php echo _l('area'); ?></th>
                                    <th width="10%" align="right" class="qty"><?php echo _l('awarded_qty'); ?></th>
                                    <th width="10%" align="right" class="qty"><?php echo _l('qty_after_incl_co'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('awarded_rate'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('rate_after_incl_co'); ?></th>
                                    <?php /* 
                                    <th width="10%" align="right"><?php echo _l('contract_value_before_tax'); ?></th> */ ?>
                                    <th width="10%" align="right"><?php echo _l('updated_subtotal_before_tax'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('tax_value'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('debit_note_total'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('remarks'); ?></th>
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">
                                 <?php $_subtotal = 0;
                                 $_total = 0;
                                 if (count($estimate_detail) > 0) {
                                    $count = 1;
                                    $t_mn = 0;
                                    foreach ($estimate_detail as $es) {
                                       $_subtotal += $es['into_money_updated'];
                                       $_total += $es['total']; ?>
                                       <tr nobr="true" class="sortable">
                                          <td align="center">
                                             <?php if (!empty($es['serial_no'])) {
                                                echo $es['serial_no'];
                                             } else {
                                                echo pur_html_entity_decode($count);
                                             } ?>
                                          </td>
                                          <td class="description" align="left;"><span><strong><?php
                                          $item = changee_get_item_hp($es['item_code']);
                                          if (isset($item) && isset($item->commodity_code) && isset($item->description)) {
                                             echo changee_pur_html_entity_decode($item->commodity_code . ' - ' . $item->description);
                                          } else {
                                             echo changee_pur_html_entity_decode($es['item_text']);
                                          }
                                          ?></strong></td>
                                          <?php
                                          $diff =  $es['unit_price'] - $es['original_unit_price'];
                                          $diff_unit = $es['quantity'] - $es['original_quantity'];
                                          $unit_name = changee_pur_get_unit_name($es['unit_id']);
                                          $qty_after_incl_co = changee_pur_html_entity_decode($es['quantity']) . ' ' . $unit_name;
                                          $rate_after_incl_co = app_format_money($es['unit_price'], $base_currency->symbol);
                                          $align = 'right';
                                          if ($diff_unit == 0) {
                                             $align = 'center';
                                             $qty_after_incl_co = '-';
                                          }
                                          if ($diff == 0) {
                                             $rate_after_incl_co = '-';
                                          }
                                          ?>
                                          <td align="left"><?php echo nl2br($es['description']); ?></td>
                                          <td align="left">
                                             <div style="width: 120px"><?php echo get_area_name_by_id($es['area']); ?></div>
                                          </td>
                                          <td align="right"><?php echo changee_pur_html_entity_decode($es['original_quantity']) . ' ' . $unit_name; ?></br><span>Amendment :<?php echo  $diff_unit; ?></span></td>
                                          <td align="<?php echo $align; ?>"><?php echo $qty_after_incl_co; ?></td>
                                          <td align="right"><?php echo app_format_money($es['original_unit_price'], $base_currency->symbol); ?></br><span>Amendment :<?php echo  $diff; ?></span></td>
                                          <td align="<?php echo $align; ?>"><?php echo $rate_after_incl_co; ?></td>
                                          <?php /*
                                          <td align="right"><?php echo app_format_money($es['into_money'], $base_currency->symbol); ?></td> */ ?>
                                          <td align="right"><?php echo app_format_money($es['into_money_updated'], $base_currency->symbol); ?></td>
                                          <td align="right"><?php
                                                            if ($es['tax_name'] != '') {
                                                               echo changee_pur_html_entity_decode($es['tax_name']);
                                                            } else {
                                                               $this->load->model('changee/changee_model');
                                                               if ($es['tax'] != '') {
                                                                  $tax_arr =  $es['tax'] != '' ? explode('|', $es['tax'] ?? '') : [];
                                                                  $tax_str = '';
                                                                  if (count($tax_arr) > 0) {
                                                                     foreach ($tax_arr as $key => $tax_id) {
                                                                        if (($key + 1) < count($tax_arr)) {
                                                                           $tax_str .= $this->changee_model->get_tax_name($tax_id) . '|';
                                                                        } else {
                                                                           $tax_str .= $this->changee_model->get_tax_name($tax_id);
                                                                        }
                                                                     }
                                                                  }

                                                                  echo changee_pur_html_entity_decode($tax_str);
                                                               }
                                                            }
                                                            ?></td>
                                          <td align="right"><?php echo app_format_money($es['tax_value'], $base_currency->symbol); ?></td>
                                          <td class="amount" align="right"><?php echo app_format_money($es['total'], $base_currency->symbol); ?></td>
                                          <td class="remarks" align="right"><?php echo $es['remarks']; ?></td>
                                       </tr>
                                 <?php
                                    }
                                 } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="col-md-5 col-md-offset-7">
                        <table class="table text-right">
                           <tbody>
                              <tr id="subtotal">
                                 <td><span class="bold"><?php echo _l('subtotal_wo_tax'); ?></span>
                                 </td>
                                 <td class="subtotal">
                                    <?php echo app_format_money($estimate->subtotal, $base_currency->symbol); ?>
                                 </td>
                              </tr>

                              <?php if ($tax_data['preview_html'] != '') {
                                 echo changee_pur_html_entity_decode($tax_data['preview_html']);
                              } ?>


                              <?php if (($estimate->discount_total + $item_discount) > 0) { ?>

                                 <tr id="subtotal">
                                    <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
                                    </td>
                                    <td class="subtotal">
                                       <?php echo '-' . app_format_money(($estimate->discount_total + $item_discount), $base_currency->symbol); ?>
                                    </td>
                                 </tr>
                              <?php } ?>

                              <?php if ($estimate->shipping_fee > 0) { ?>
                                 <tr id="subtotal">
                                    <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span></td>
                                    <td class="subtotal">
                                       <?php echo app_format_money($estimate->shipping_fee, $base_currency->symbol); ?>
                                    </td>
                                 </tr>
                              <?php } ?>


                              <tr id="subtotal">
                                 <td><span class="bold"><?php echo _l('total'); ?></span>
                                 </td>
                                 <td class="subtotal bold">
                                    <?php echo app_format_money($estimate->total, $base_currency->symbol); ?>
                                 </td>
                              </tr>

                              <tr id="co_value">
                                 <td><span class="bold"><?php echo _l('change_order_value'); ?></span>
                                 </td>
                                 <td class="co_value bold">
                                    <?php echo app_format_money($estimate->co_value, $base_currency->symbol); ?>
                                 </td>
                              </tr>

                              <tr id="non_tender_total">
                                 <td><span class="bold"><?php echo _l('non_tender_items_in_change_order'); ?></span>
                                 </td>
                                 <td class="non_tender_total bold">
                                    <?php echo app_format_money($estimate->non_tender_total, $base_currency->symbol); ?>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <?php if ($estimate->order_summary != '') { ?>
                        <div class="col-md-12 mtop15">
                           <p class="bold text-muted"><?php echo _l('order_summary'); ?></p>
                           <p><?php echo nl2br($estimate->order_summary); ?></p>
                        </div>
                     <?php } ?>
                     <?php if ($estimate->vendornote != '') { ?>
                        <div class="col-md-12 mtop15">
                           <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
                           <p><?php echo nl2br($estimate->vendornote); ?></p>
                        </div>
                     <?php } ?>

                     <?php if ($estimate->terms != '') { ?>
                        <div class="col-md-12 mtop15">
                           <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                           <p><?php echo changee_pur_html_entity_decode($estimate->terms); ?></p>
                        </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_reminders">
               <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-changee_order-<?php echo changee_pur_html_entity_decode($estimate->id); ?>"><i class="fa fa-bell-o"></i> <?php echo _l('estimate_set_reminder_title'); ?></a>
               <hr />
               <?php render_datatable(array(_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')), 'reminders'); ?>
               <?php $this->load->view('admin/includes/modals/reminder', array('id' => $estimate->id, 'name' => 'changee_order', 'members' => $members, 'reminder_title' => _l('estimate_set_reminder_title'))); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_notes">
               <?php echo form_open(admin_url('changee/add_note/' . $estimate->id), array('id' => 'sales-notes', 'class' => 'estimate-notes-form')); ?>
               <?php echo render_textarea('description'); ?>
               <div class="text-right">
                  <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('estimate_add_note'); ?></button>
               </div>
               <?php echo form_close(); ?>
               <hr />
               <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="discuss">
               <div class="row contract-comments mtop15">
                  <div class="col-md-12">
                     <div id="contract-comments"></div>
                     <div class="clearfix"></div>
                     <textarea name="content" id="comment" rows="4" class="form-control mtop15 contract-comment"></textarea>
                     <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_contract_comment();"><?php echo _l('proposal_add_comment'); ?></button>
                  </div>
               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="attachment">
               <div class="col-md-12">
                  <?php
                  $file_html = '';
                  if (isset($attachments) && count($attachments) > 0) {
                     $file_html .= '<hr /><p class="bold text-muted">' . _l('Changee Attachments') . '</p>';

                     foreach ($attachments as $value) {
                        $path = get_upload_path_by_type('changee') . 'pur_order/' . $value['rel_id'] . '/' . $value['file_name'];
                        $is_image = is_image($path);

                        $download_url = site_url('download/file/changee/' . $value['id']);

                        $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="' . $value['id'] . '">
            <div class="col-md-8">';

                        // Preview button for images
                        // if ($is_image) {
                        $file_html .= '<a name="preview-changee-btn" 
                onclick="preview_changee_attachment(this); return false;" 
                rel_id="' . $value['rel_id'] . '" 
                id="' . $value['id'] . '" 
                href="javascript:void(0);" 
                class="mbot10 mright5 btn btn-success pull-left" 
                data-toggle="tooltip" 
                title="' . _l('preview_file') . '">
                <i class="fa fa-eye"></i>
            </a>';
                        // }

                        $file_html .= '<div class="pull-left"><i class="' . get_mime_class($value['filetype']) . '"></i></div>
            <a href="' . $download_url . '" target="_blank" download>
                ' . $value['file_name'] . '
            </a>
            <br />
            <small class="text-muted">' . $value['filetype'] . '</small>
            </div>
            <div class="col-md-4 text-right">';

                        // Delete button with permission check
                        if ($value['staffid'] == get_staff_user_id() || is_admin()) {
                           $file_html .= '<a href="' . admin_url('changee/delete_attachment/' . $value['id']) . '" class="text-danger _delete"><i class="fa fa-times"></i></a>';
                        }

                        $file_html .= '</div></div>';
                     }

                     $file_html .= '<hr />';
                     echo pur_html_entity_decode($file_html);
                  }
                  ?>
               </div>
            </div>
            <div id="changee_file_data"></div>
            <div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
               <div class="row">
                  <div class="col-md-12">
                     <div class="activity-feed">
                        <?php foreach ($activity as $activity) {
                           $_custom_data = false; ?>
                           <div class="feed-item" data-sale-activity-id="<?php echo e($activity['id']); ?>">
                              <div class="date">
                                 <span class="text-has-action" data-toggle="tooltip"
                                    data-title="<?php echo e(_dt($activity['date'])); ?>">
                                    <?php echo e(time_ago($activity['date'])); ?>
                                 </span>
                              </div>
                              <div class="text">
                                 <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                                    <a href="<?php echo admin_url('profile/' . $activity['staffid']); ?>">
                                       <?php echo staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                       ?>
                                    </a>
                                 <?php } ?>
                                 <?php
                                 $additional_data = '';
                                 if (!empty($activity['additional_data']) && $additional_data = unserialize($activity['additional_data'])) {
                                    $i               = 0;
                                    foreach ($additional_data as $data) {
                                       if (strpos($data, '<original_status>') !== false) {
                                          $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                                          $additional_data[$i] = format_invoice_status($original_status, '', false);
                                       } elseif (strpos($data, '<new_status>') !== false) {
                                          $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                                          $additional_data[$i] = format_invoice_status($new_status, '', false);
                                       } elseif (strpos($data, '<custom_data>') !== false) {
                                          $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                          unset($additional_data[$i]);
                                       }
                                       $i++;
                                    }
                                 }

                                 $_formatted_activity = _l($activity['description'], $additional_data);

                                 if ($_custom_data !== false) {
                                    $_formatted_activity .= ' - ' . $_custom_data;
                                 }

                                 if (!empty($activity['full_name'])) {
                                    $_formatted_activity = e($activity['full_name']) . ' - ' . $_formatted_activity;
                                 }

                                 echo $_formatted_activity;

                                 if (is_admin()) {
                                    echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa fa-remove"></i></a>';
                                 } ?>
                              </div>
                           </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="payment_record">
               <div class="col-md-6 pad_div_0">
                  <h4 class="font-medium mbot15 bold text-success"><?php echo _l('payment_for_pur_order') . ' ' . $estimate->pur_order_number; ?></h4>
               </div>
               <div class="col-md-6 padr_div_0">

                  <!-- <?php if (changee_purorder_left_to_pay($estimate->id) > 0) { ?>
               <a href="#" onclick="add_payment(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus"></i><?php echo ' ' . _l('payment'); ?></a>
               <?php } ?> -->

                  <?php if (changee_purorder_left_to_pay($estimate->id) < $estimate->total) { ?>
                     <a href="#" onclick="convert_to_changee_inv(<?php echo changee_pur_html_entity_decode($estimate->id); ?> ); return false;" class="btn btn-info pull-right mright5" data-toggle="tooltip" data-placement="top" title="<?php echo _l('convert_to_payment_of_changee_inv'); ?>"><i class="fa fa-refresh"></i></a>
                  <?php } ?>

                  <?php if (changee_purorder_inv_left_to_pay($estimate->id) > 0) { ?>
                     <?php if (changee_total_inv_value_by_pur_order($estimate->id) > 0) { ?>

                        <a href="#" onclick="add_payment_with_inv(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus"></i><?php echo ' ' . _l('payment'); ?></a>

                     <?php } else { ?>

                        <a href="#" onclick="add_payment(<?php echo changee_pur_html_entity_decode($estimate->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus"></i><?php echo ' ' . _l('payment'); ?></a>

                     <?php } ?>
                  <?php } ?>
               </div>
               <div class="clearfix"></div>
               <table class="table dt-table">
                  <thead>
                     <th><?php echo _l('payments_table_amount_heading'); ?></th>
                     <th><?php echo _l('payments_table_mode_heading'); ?></th>
                     <th><?php echo _l('payment_transaction_id'); ?></th>

                     <th><?php echo _l('payments_table_date_heading'); ?></th>
                     <th><?php echo _l('options'); ?></th>
                  </thead>
                  <tbody>
                     <?php foreach ($payment as $pay) { ?>
                        <?php
                        $base_currency = $base_currency;
                        $invoice_currency_id = changee_get_invoice_currency_id($pay['pur_invoice']);
                        if ($invoice_currency_id != 0) {
                           $base_currency = changee_pur_get_currency_by_id($invoice_currency_id);
                        }
                        ?>
                        <tr>
                           <td><?php echo app_format_money($pay['amount'], $base_currency->symbol); ?></td>
                           <td><?php echo changee_get_payment_mode_by_id($pay['paymentmode']); ?></td>
                           <td><?php echo changee_pur_html_entity_decode($pay['transactionid']); ?></td>
                           <td><?php echo _d($pay['date']); ?></td>
                           <td>
                              <?php if (has_permission('changee_invoices', '', 'edit') || is_admin()) { ?>
                                 <a href="<?php echo admin_url('changee/payment_invoice/' . $pay['id']); ?>" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('view'); ?>"><i class="fa fa-eye "></i></a>
                              <?php } ?>
                              <?php if (has_permission('changee_invoices', '', 'delete') || is_admin()) { ?>
                                 <a href="<?php echo admin_url('changee/delete_payment/' . $pay['id'] . '/' . $estimate->id); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                              <?php } ?>
                           </td>
                        </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>

         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="payment_record_pur" tabindex="-1" role="dialog">
   <div class="modal-dialog dialog_30">
      <?php echo form_open(admin_url('changee/add_payment_on_po/' . $estimate->id), array('id' => 'purorder-add_payment-form')); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
               <span class="edit-title"><?php echo _l('edit_payment'); ?></span>
               <span class="add-title"><?php echo _l('new_payment'); ?></span>
            </h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <div id="additional"></div>
                  <?php echo render_input('amount', 'amount', changee_purorder_inv_left_to_pay($estimate->id), 'number', array('max' => changee_purorder_inv_left_to_pay($estimate->id))); ?>
                  <?php echo render_date_input('date', 'payment_edit_date'); ?>
                  <?php echo render_select('paymentmode', $payment_modes, array('id', 'name'), 'payment_mode'); ?>

                  <?php echo render_input('transactionid', 'payment_transaction_id'); ?>
                  <?php echo render_textarea('note', 'note', '', array('rows' => 7)); ?>

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

<div class="modal fade" id="payment_record_pur_with_inv" tabindex="-1" role="dialog">
   <div class="modal-dialog dialog_30">
      <?php echo form_open(admin_url('changee/add_payment_on_po_with_inv/' . $estimate->id), array('id' => 'purorder-add_payment_with_inv-form')); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
               <span class="add-title"><?php echo _l('new_payment'); ?></span>
            </h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <div id="inv_additional"></div>

                  <?php $selected = $invoices[0]['id'];
                  echo render_select('pur_invoice', $invoices, array('id', 'invoice_number', 'total'), 'pur_invoice', $selected, array('onchange' => 'pur_inv_payment_change(this); return false;')); ?>

                  <?php echo render_input('amount', 'amount', changee_purinvoice_left_to_pay($invoices[0]['id']), 'number', array('max' => changee_purinvoice_left_to_pay($invoices[0]['id']))); ?>
                  <?php echo render_date_input('date', 'payment_edit_date'); ?>
                  <?php echo render_select('paymentmode', $payment_modes, array('id', 'name'), 'payment_mode'); ?>

                  <?php echo render_input('transactionid', 'payment_transaction_id'); ?>
                  <?php echo render_textarea('note', 'note', '', array('rows' => 7)); ?>

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


<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">

         <div class="modal-body">
            <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body">
               <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="ip_style" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
               <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>

            </div>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
            <button onclick="sign_request(<?php echo changee_pur_html_entity_decode($estimate->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
         </div>

      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="send_po" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('changee/send_po'), array('id' => 'send_po-form')); ?>
      <div class="modal-content modal_withd">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
               <span><?php echo _l('send_a_po'); ?></span>
            </h4>
         </div>
         <div class="modal-body">
            <div id="additional_po"></div>
            <div class="row">
               <div class="col-md-12 form-group">
                  <label for="send_to"><span class="text-danger">* </span><?php echo _l('send_to'); ?></label>
                  <select name="send_to[]" id="send_to" class="selectpicker" required multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                     <?php foreach ($vendor_contacts as $s) { ?>
                        <option value="<?php echo changee_pur_html_entity_decode($s['email']); ?>" data-subtext="<?php echo changee_pur_html_entity_decode($s['firstname'] . ' ' . $s['lastname']); ?>" selected><?php echo changee_pur_html_entity_decode($s['email']); ?></option>
                     <?php } ?>
                  </select>
                  <br>
               </div>
               <div class="col-md-12">
                  <div class="checkbox checkbox-primary">
                     <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                     <label for="attach_pdf"><?php echo _l('attach_changee_order_pdf'); ?></label>
                  </div>
               </div>

               <div class="col-md-12">
                  <?php echo render_textarea('content', 'additional_content', '', array('rows' => 6, 'data-task-ae-editor' => true, !is_mobile() ? 'onclick' : 'onfocus' => (!isset($routing) || isset($routing) && $routing->description == '' ? 'routing_init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')), array(), 'no-mbot', 'tinymce-task'); ?>
               </div>
               <div id="type_care">

               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button id="sm_btn" type="submit" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('pur_send'); ?></button>
         </div>
      </div><!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php require 'modules/changee/assets/js/pur_order_preview_js.php'; ?>

<script>
   function preview_changee_attachment(invoker) {
      "use strict";
      var id = $(invoker).attr('id');
      var rel_id = $(invoker).attr('rel_id');
      view_preview_changee_attachment(id, rel_id);
   }

   function view_preview_changee_attachment(id, rel_id) {
      "use strict";
      $('#changee_file_data').empty();
      $("#changee_file_data").load(admin_url + 'changee/file_changee_preview/' + id + '/' + rel_id, function(response, status, xhr) {
         if (status == "error") {
            alert_float('danger', xhr.statusText);
         }
      });
   }

   function close_modal_preview() {
      "use strict";
      $('._project_file').modal('hide');
   }
</script>