<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$valid_cur_date = $this->timesheets_model->get_next_shift_date(get_staff_user_id(), date('Y-m-d'));
?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6">
                <h4><?php echo '<i class=" fa fa-clipboard"></i> ' . _l('manage_requisition') ?></h4>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="horizontal-scrollable-tabs preview-tabs-top">
              <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
              <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
              <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="<?php if (!isset($tab) && $tab == 'registration_on_leave') {
                                                    echo 'active';
                                                  } ?>">
                    <a href="#registration_on_leave" aria-controls="registration_on_leave" role="tab" data-toggle="tab">
                      <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('registration_on_leave'); ?>
                    </a>
                  </li>
                  <li role="presentation" class="<?php if (isset($tab) && $tab == 'missed_punch') {
                                                    echo 'active';
                                                  } ?>">
                    <a href="#missed_punch" aria-controls="missed_punch" role="tab" data-toggle="tab">
                      <span class="glyphicon glyphicon-time"></span>&nbsp;<?php echo _l('Miss Punch'); ?>
                    </a>
                  </li>
                  <li role="presentation" class="<?php if (isset($tab)  && $tab == 'additional_timesheets') {
                                                    echo 'active';
                                                  } ?>">
                    <a href="#additional_timesheets" aria-controls="additional_timesheets" role="tab" data-toggle="tab">
                      <span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo _l('additional_timesheets'); ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <input type="hidden" name="userid" value="<?php echo html_entity_decode($userid); ?>">

            <div class="tab-content active">
              <div role="tabpanel" class="tab-pane <?php if (!isset($tab) && $tab == 'registration_on_leave') {
                                                      echo 'active';
                                                    } ?>" id="registration_on_leave">
                <div class="row">
                  <div class="col-md-12 mtop15">
                    <a href="#" onclick="new_requisition(); return false;" class="btn mright5 btn-info pull-left display-block" data-toggle="sidebar-right" data-target=".requisition_m">
                      <?php echo _l('Create_requisition'); ?>
                    </a>
                    <a href="<?php echo admin_url('timesheets/calendar_leave_application'); ?>" class="btn btn-default">
                      <i class="fa fa-calendar menu-icon"></i>&nbsp;
                      <?php echo _l('ts_calendar_view'); ?>
                    </a>
                    <div class="clearfix"></div>
                    <br>
                    <br>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <select name="chose" class="selectpicker" data-width="100%" id="chose" data-none-selected-text="<?php echo _l('filter_by'); ?>">
                      <option value="all"><?php echo _l('all') ?></option>
                      <option value="my_approve"><?php echo _l('my_approve') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="status_filter[]" class="selectpicker" data-width="100%" id="status_filter" multiple data-none-selected-text="<?php echo _l('filter_by_status'); ?>">
                      <option value="0"><?php echo _l('Create') ?></option>
                      <option value="1"><?php echo _l('approved') ?></option>
                      <option value="2"><?php echo _l('Reject') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="rel_type_filter[]" class="selectpicker" data-width="100%" id="rel_type_filter" multiple data-none-selected-text="<?php echo _l('filter_by_type'); ?>">
                      <option value="1"><?php echo _l('Leave') ?></option>
                      <option value="2"><?php echo _l('late') ?></option>
                      <option value="6"><?php echo _l('early') ?></option>
                      <option value="3"><?php echo _l('Go_out') ?></option>
                      <option value="4"><?php echo _l('Go_on_bussiness') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="department_filter[]" class="selectpicker" data-width="100%" id="department_filter" multiple data-live-search="true" data-none-selected-text="<?php echo _l('filter_by_department'); ?>">
                      <?php foreach ($departments as $dpm) { ?>
                        <option value="<?php echo html_entity_decode($dpm['departmentid']); ?>"><?php echo html_entity_decode($dpm['name']); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>


                <div class="clearfix"></div>
                <br>
                <div class="modal bulk_actions fade" id="table_registration_leave_bulk_actions" tabindex="-1" role="dialog">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                      </div>
                      <div class="modal-body">
                        <?php if (is_admin()) { ?>
                          <div class="checkbox checkbox-danger">
                            <input type="checkbox" name="mass_delete" id="mass_delete">
                            <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                          </div>
                        <?php } ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                        <?php if (is_admin()) { ?>
                          <a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
                <a href="#" onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_registration_leave" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>

                <?php
                $table_data = array(
                  _l('id'),
                  '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_registration_leave"><label></label></div>',
                  _l('Subject'),
                  _l('name'),
                  _l('start_time'),
                  _l('end_time'),
                  _l('approver'),
                  _l('Follower'),
                  _l('reason'),
                  _l('Type'),
                  _l('status'),
                  _l('date_created'),
                  _l('options'),
                );
                render_datatable(
                  $table_data,
                  'table_registration_leave',
                  array('customizable-table'),
                  array(
                    'id' => 'table-table_registration_leave',
                    'data-last-order-identifier' => 'table_registration_leave',
                    'data-default-order' => get_table_last_order('table_registration_leave'),
                  )
                ); ?>
              </div>
              <div role="tabpanel" class="tab-pane <?php if (isset($tab) && $tab == 'missed_punch') {
                                                      echo 'active';
                                                    } ?>" id="missed_punch">
                <div class="row mtop15">
                  <div class="col-md-12">
                    <?php
                    if (has_permission('additional_timesheets_management', '', 'view') || is_admin()) {
                    ?>
                      <a href="#" onclick="btn_missed_punch(); return false;" class="btn mright5 btn-info pull-left display-block">
                        <?php echo _l('add'); ?>
                      </a>
                    <?php } ?>
                  </div>
                  <div class="clearfix"></div>
                  <br>
                  <br>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <select name="chose_mp" class="selectpicker" id="chose_mp" data-width="100%" data-none-selected-text="<?php echo _l('filter_by'); ?>">
                      <option value="all"><?php echo _l('all') ?></option>
                      <option value="my_approve"><?php echo _l('my_approve') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="status_filter_mp[]" class="selectpicker" id="status_filter_mp" multiple data-width="100%" data-none-selected-text="<?php echo _l('filter_by_status'); ?>">
                      <option value="0"><?php echo _l('status_0') ?></option>
                      <option value="1"><?php echo _l('status_1') ?></option>
                      <option value="2"><?php echo _l('status_-1') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3 leads-filter-column pull-left">
                    <select name="department_mp[]" class="selectpicker" id="department_mp" data-width="100%" multiple data-live-search="true" data-none-selected-text="<?php echo _l('filter_by_department'); ?>">
                      <?php foreach ($departments as $dpm) { ?>
                        <option value="<?php echo html_entity_decode($dpm['departmentid']); ?>"><?php echo html_entity_decode($dpm['name']); ?></option>
                      <?php } ?>
                    </select>

                  </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <?php $this->load->view('missed_punch'); ?>
              </div>
              <div role="tabpanel" class="tab-pane <?php if (isset($tab) && $tab == 'additional_timesheets') {
                                                      echo 'active';
                                                    } ?>" id="additional_timesheets">

                <div class="row mtop15">
                  <div class="col-md-12">
                    <?php
                    if (has_permission('additional_timesheets_management', '', 'view') || has_permission('additional_timesheets_management', '', 'view_own') || is_admin()) {
                    ?>
                      <a href="#" onclick="btn_additional_timesheets(); return false;" class="btn mright5 btn-info pull-left display-block">
                        <?php echo _l('add'); ?>
                      </a>
                    <?php } ?>
                  </div>
                  <div class="clearfix"></div>
                  <br>
                  <br>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <select name="chose_ats" class="selectpicker" id="chose_ats" data-width="100%" data-none-selected-text="<?php echo _l('filter_by'); ?>">
                      <option value="all"><?php echo _l('all') ?></option>
                      <option value="my_approve"><?php echo _l('my_approve') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="status_filter_ats[]" class="selectpicker" id="status_filter_ats" multiple data-width="100%" data-none-selected-text="<?php echo _l('filter_by_status'); ?>">
                      <option value="0"><?php echo _l('status_0') ?></option>
                      <option value="1"><?php echo _l('status_1') ?></option>
                      <option value="2"><?php echo _l('status_-1') ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="rel_type_filter_ats[]" class="selectpicker" id="rel_type_filter_ats" data-width="100%" multiple data-none-selected-text="<?php echo _l('filter_by_type'); ?>">
                      <option value="W"><?php echo _l('W') ?></option>
                      <option value="OT"><?php echo _l('OT') ?></option>

                    </select>
                  </div>
                  <div class="col-md-3 leads-filter-column pull-left">
                    <select name="department_ats[]" class="selectpicker" id="department_ats" data-width="100%" multiple data-live-search="true" data-none-selected-text="<?php echo _l('filter_by_department'); ?>">
                      <?php foreach ($departments as $dpm) { ?>
                        <option value="<?php echo html_entity_decode($dpm['departmentid']); ?>"><?php echo html_entity_decode($dpm['name']); ?></option>
                      <?php } ?>
                    </select>

                  </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <?php $this->load->view('additional_timesheets'); ?>
              </div>

              <!-- The Modal -->
              <!-- start -->
              <div class="modal fade" id="requisition_m" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">
                        <span class="edit-title"><?php echo _l('edit_requisition_m'); ?></span>
                        <span class="add-title"><?php echo _l('new_requisition_m'); ?></span>
                      </h4>
                    </div>
                    <?php echo form_open_multipart(admin_url('timesheets/add_requisition_ajax'), array('id' => 'requisition-form')); ?>
                    <div class="modal-body">
                      <div id="additional_contract_type"></div>
                      <div class="form">

                        <div class="row">
                          <div class="col-md-12">
                            <div id="additional_contract_type"></div>
                            <div class="form" id="new_requisition">
                              <div class="row">
                                <div class="col-md-12">
                                  <label for="subject" class="control-label"><?php echo _l('Subject'); ?></label>
                                  <?php echo render_input('subject') ?>
                                </div>
                              </div>
                              <?php
                              if (is_admin() || has_permission('leave_management', '', 'view')) { ?>
                                <div class="row">
                                  <div class="col-md-12">
                                    <?php echo render_select('staff_id', $pro, array('staffid', array('firstname', 'lastname')), 'staff', get_staff_user_id(), [], [], '', '', false); ?>
                                  </div>
                                </div>
                              <?php } else { ?>
                                <input type="hidden" name="staff_id" value="<?php echo get_staff_user_id(); ?>">
                              <?php } ?>
                              <div class="row mtop10">
                                <div class="col-md-6 pb-4" id="type">
                                  <label for="rel_type" class="control-label"><?php echo _l('Type'); ?></label>
                                  <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('none_type'); ?>">
                                    <option value="1"><?php echo _l('Leave') ?></option>
                                    <option value="2"><?php echo _l('late') ?></option>
                                    <option value="6"><?php echo _l('early') ?></option>
                                    <option value="3"><?php echo _l('Go_out') ?></option>
                                    <option value="4"><?php echo _l('Go_on_bussiness') ?></option>
                                  </select>
                                </div>
                                <div class="col-md-6 pb-4" id="type_of_leave">
                                  <div class="form-group">
                                    <label for="type_of_leave" class="control-label"><?php echo _l('type_of_leave'); ?></label>
                                    <div class="<?php if (is_admin()) {
                                                  echo 'input-group';
                                                } ?>">
                                      <select name="type_of_leave" class="selectpicker" id="type_of_leave" data-width="100%" data-none-selected-text="<?php echo _l('none_type'); ?>">
                                        <!-- <option value="8"><?php echo _l('annual_leave') ?></option> -->
                                        <!-- <option value="2"><?php echo _l('maternity_leave') ?></option>                   -->
                                        <option value="4">Leave Without Pay(LWP)</option>
                                        <option value="1"><?php echo _l('sick_leave') ?>(SL)</option>
                                        <?php
                                        foreach ($type_of_leave as $value) { ?>
                                          <option value="<?php echo html_entity_decode($value['slug']); ?>"><?php echo html_entity_decode($value['type_name']); ?></option>
                                        <?php } ?>
                                      </select>
                                      <?php
                                      if (is_admin()) { ?>
                                        <span class="input-group-addon btn add_new_type_of_leave">
                                          <i class="fa fa-plus"></i>
                                        </span>
                                      <?php } ?>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12 hide" id="div_according_to_the_plan">
                                  <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                      <input type="checkbox" name="according_to_the_plan" id="according_to_the_plan" value="1">
                                      <label for="according_to_the_plan"><?php echo _l('according_to_the_plan'); ?></label>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-md-12 hide" id="advance_payment_rq">
                                  <div class="panel panel-warning">
                                    <div class="panel-heading"><?php echo _l('advance_payment_request'); ?></div>
                                    <div class="panel-body">
                                      <div id="list_jp">
                                        <div class="new-kpi-al">

                                          <div class="col-md-6">
                                            <label for="used_to[0]" class="control-label"><?php echo _l('used_to') ?></label>
                                          </div>
                                          <div class="col-md-6">
                                            <label for="amoun_of_money[0]" class="control-label"><?php echo _l('amoun_of_money') ?></label>
                                          </div>

                                          <div id="new_kpi" class="row new_kpi_row">

                                            <div class="col-md-6">
                                              <input type="text" id="used_to[0]" name="used_to[0]" class="form-control" value="" aria-invalid="false">
                                            </div>
                                            <div class="col-md-5">
                                              <input type="text" id="amoun_of_money[0]" name="amoun_of_money[0]" class="form-control" value="" aria-invalid="false" data-type="currency">
                                            </div>

                                            <div class="col-md-1 button_add_kpi" name="button_add_kpi">
                                              <button name="add" class="btn new_kpi btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                            </div>

                                          </div>

                                        </div>
                                      </div>
                                      <br><br>
                                      <?php echo render_date_input('request_date', 'request_date', ''); ?>
                                      <?php echo render_textarea('advance_payment_reason', 'advance_payment_reason'); ?>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="row approx-fr">
                                <div class="col-md-6 start_time">
                                  <br>
                                  <?php echo render_date_input('start_time', 'From_Date', _d($valid_cur_date)) ?>
                                </div>
                                <div class="col-md-6">
                                  <br>
                                  <?php
                                  $value_number_day = 0.5;
                                  ?>
                                  <div class="form-group" app-field-wrapper="number_of_leaving_day">
                                    <label for="number_of_leaving_day" class="control-label"><?php echo _l('number_of_days'); ?></label>
                                    <input type="number" id="number_of_leaving_day" name="number_of_leaving_day" class="form-control" step="0.5" value="<?php echo html_entity_decode($value_number_day); ?>" aria-invalid="false">
                                  </div>
                                </div>
                                <div class="col-md-12 mtop10" id="number_days_off_2">
                                  <label class="control-label "><?php echo _l('number_of_days_off') . ': ' . $days_off; ?></label><br>
                                  <label class="control-label <?php if ($number_day_off == 0) {
                                                                echo 'text-danger';
                                                              } ?>"><?php echo _l('number_of_leave_days_allowed') . ': ' . $number_day_off; ?></label>
                                  <input type="hidden" name="number_day_off" value="<?php echo html_entity_decode($number_day_off); ?>">
                                </div>
                              </div>
                              <br>
                              <div class="row mtop10 date_input">

                                <div class="col-md-6 end_time">
                                  <?php echo render_date_input('end_time', 'To_Date', _d($valid_cur_date), ['readonly' => true]) ?>
                                </div>
                              </div>

                              <div class="row mtop10 datetime_input hide">
                                <div class="col-md-6 start_time">
                                  <?php echo render_datetime_input('start_time_s', 'From_Date', _d(date('Y-m-d H:i:s'))) ?>
                                </div>
                                <div class="col-md-6 end_time">
                                  <?php echo render_datetime_input('end_time_s', 'To_Date', _d(date('Y-m-d H:i:s'))) ?>
                                </div>
                              </div>




                              <div class="row mtop10">
                                <div class="col-md-12 pb-4" id="leave_">
                                  <label for="followers_id" class="control-label"><?php echo _l('Follower'); ?></label>
                                  <select name="followers_id" id="followers_id" data-live-search="true" class="selectpicker" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('none'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($pro as $s) { ?>
                                      <option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'] . ' ' . $s['lastname']); ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                                <div class="col-md-12 pb-4" id="leave_handover_recipients">
                                  <br>
                                  <label for="handover_recipients" class="control-label"><?php echo _l('handover_recipients'); ?></label>
                                  <select name="handover_recipients" id="handover_recipients" data-live-search="true" class="selectpicker" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($pro as $s) { ?>
                                      <option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'] . ' ' . $s['lastname']); ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>


                              <div class="row mtop10">
                                <div class="col-md-12">
                                  <?php echo render_textarea('reason', 'reason_') ?>
                                </div>
                              </div>
                              <div class="mtop10">
                                <label for="file" class="control-label"><?php echo _l('requisition_files'); ?></label>
                                <input type="file" id="file" name="file" class="form-control" value="">
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                      <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
                    </div>
                    <?php echo form_close(); ?>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->
              <!-- end -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_new_type_of_leave" tabindex="1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open(admin_url('timesheets/add_type_of_leave'), array('id' => 'add_type_of_leave-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>
          <?php echo _l('ts_input_new_type_of_leave'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="col-md-6">
          <?php echo render_input('type_name', 'type_of_leave') ?>
        </div>
        <div class="col-md-6">
          <?php echo render_input('symbol', _l('ts_character') . ' <i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="' . _l('ts_it_will_be_displayed_on_the_timesheet') . '"></i>') ?>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-info add_type_of_leave"><?php echo _l('ts_add'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="additional_timesheets_modalss" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open(admin_url('timesheets/send_additional_timesheets'), array('id' => 'edit_timesheets-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>
          <?php echo _l('additional_timesheets'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12">

          <?php if (is_admin() || has_permission('additional_timesheets_specific_employees', '', 'create')) { ?>
            <?php echo render_select('staff_id', $staffs, ['staffid', ['firstname', 'lastname']], '<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="' . _l('ts_specific_employees_tooltip') . '"></i> ' . _l('ts_specific_employees')); ?>
          <?php } ?>

          <?php echo render_date_input('additional_day', 'additional_day'); ?>
          <?php echo render_input('time_in', 'time_in', '', 'time'); ?>
          <?php echo render_input('time_out', 'time_out', '', 'time'); ?>
          <?php echo render_input('timekeeping_value', 'timekeeping_value', ''); ?>
          <?php echo render_textarea('reason', 'reason_'); ?>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-info btn-additional-timesheets"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="missed_punch_modalss" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <?php echo form_open(admin_url('timesheets/send_missed_punch'), array('id' => 'edit_missed_punch-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>
          <?php echo _l('Add Missed Punch'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12">

          <?php if (is_admin() || has_permission('additional_timesheets_specific_employees', '', 'create')) { ?>
            <?php echo render_select('staff_id', $staffs, ['staffid', ['firstname', 'lastname']], '<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="' . _l('ts_specific_employees_tooltip') . '"></i> ' . _l('ts_specific_employees')); ?>
          <?php } ?>

          <?php echo render_date_input('additional_day', 'additional_day'); ?>
          <?php echo render_textarea('reason', 'reason_'); ?>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-info btn-missed-punch"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<!-- Edit Leave Modal -->
<div class="modal fade" id="editLeaveModal" tabindex="-1" role="dialog" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header display-flex">
        <h4 class="modal-title" id="editLeaveModalLabel" style="width: 96%;">Edit Leave</h4>

        <span class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true">&times;</span>

      </div>
      <div class="modal-body">

        <?php echo form_open_multipart(admin_url('timesheets/update_leave'), array('id' => 'editLeaveForm')); ?>
        <div class="form-group">
          <label for="leaveType"><?php echo _l('leave_type'); ?></label>
          <select id="leaveType" name="leave_type" class="form-control" required>
            <!-- Leave type options will be populated dynamically -->
            <option value="">Select Option</option>
            <option value="1"><?php echo _l('sick_leave') ?>(SL)</option>
            <option value="4">Leave Without Pay(LWP)</option>
            <?php foreach ($type_of_leave as $leave) { ?>
              <option value="<?php echo $leave['slug']; ?>"><?php echo $leave['type_name']; ?></option>
            <?php } ?>
          </select>
        </div>
        <input type="hidden" name="update_staff_id" id="update_staff_id">
        <input type="hidden" name="update_start_time" id="update_start_time">
        <input type="hidden" name="update_end_time" id="update_end_time">
        <input type="hidden" name="update_leave_id" id="update_leave_id">
        <!-- Additional fields can be added here -->

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary" id="saveEditLeave">Update</button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<input type="hidden" name="current_date" value="<?php echo _d(date('Y-m-d')); ?>">
<?php
$date_format = '';
$data_date_format = get_option('dateformat');
if ($data_date_format) {
  $date_format = $data_date_format;
}
?>
<input type="hidden" name="date_format" value="<?php echo html_entity_decode($date_format); ?>">
<?php init_tail(); ?>
<?php require 'modules/timesheets/assets/js/requisition_manage_js.php'; ?>


</body>

</html>