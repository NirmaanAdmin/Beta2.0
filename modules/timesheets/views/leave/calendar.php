<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$valid_cur_date = $this->timesheets_model->get_next_shift_date(get_staff_user_id(), date('Y-m-d'));
?>
<div id="wrapper">
  <div class="content">
    <?php echo form_open(); ?>
    <?php echo form_hidden('calendar_filters', true); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="col-md-2">
              <?php
              $choses = [
                ['id' => 'all', 'name' => _l('all')],
                ['id' => 'my_approve', 'name' => _l('my_approve')],
              ];
              ?>
              <?php echo render_select('chose', $choses, array('id', 'name'), '', $chose, ['data-none-selected-text' => _l('filter_by')], array(), '', '', false); ?>
            </div>
            <div class="col-md-2" id='status_filter'>
              <?php
              $status_filters = [
                ['id' => 0, 'name' => _l('Create')],
                ['id' => 1, 'name' => _l('approved')],
                ['id' => 2, 'name' => _l('Reject')],
              ];
              ?>
              <?php echo render_select('status_filter[]', $status_filters, array('id', 'name'), '', $status_filter, ['data-none-selected-text' => _l('filter_by_status'), 'multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
            </div>
            <div class="col-md-2" id='rel_type_filter'>
              <?php
              $rel_type_filters = [
                ['id' => 1, 'name' => _l('Leave')],
                ['id' => 2, 'name' => _l('late')],
                ['id' => 6, 'name' => _l('early')],
                ['id' => 3, 'name' => _l('Go_out')],
                ['id' => 4, 'name' => _l('Go_on_bussiness')],
              ];
              ?>
              <?php echo render_select('rel_type_filter[]', $rel_type_filters, array('id', 'name'), '', $rel_type_filter, ['data-none-selected-text' => _l('filter_by_type'), 'multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
            </div>
            <div class="col-md-2" id='department_filter'>
              <?php echo render_select('department_filter[]', $departments, array('departmentid', 'name'), '', $department_filter, ['data-none-selected-text' => _l('filter_by_department'), 'multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
            </div>
            <div class="col-md-2" id='staff_filter'>
              <?php echo render_select('staff_filter[]', $staff_list, array('staffid', array('firstname', 'lastname')), '', $staff_filter , ['data-none-selected-text' => _l('filter_by_staff'), 'multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
            </div>
            <div class="col-md-2 text-right">
              <button class="btn btn-success" type="submit"><?php echo _l('apply'); ?></button>
              <a class="btn btn-default" href="<?php echo admin_url('timesheets/requisition_manage'); ?>"><?php echo _l('ts_back'); ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php echo form_close(); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="dt-loader hide"></div>
            <div id="calendars"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="userid" value="<?php echo get_staff_user_id() ?>">
<!-- The Modal -->
<!-- start -->
<div class="modal fade" id="requisition_m" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="add-title"><?php echo _l('new_requisition_m'); ?></span>
        </h4>
      </div>
      <?php echo form_open_multipart(admin_url('timesheets/add_requisition_ajax'), array('id' => 'requisition-form')); ?>
      <div class="modal-body">
        <div id="additional_contract_type"></div>
        <div class="form">
          <input type="hidden" name="redirect_calendar" value="1">
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
                      <?php echo render_select('staff_id', $staff, array('staffid', array('firstname', 'lastname')), 'staff', get_staff_user_id(), [], [], '', '', false); ?>
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
                        <select name="type_of_leave" class="selectpicker" id="rel_type1" data-width="100%" data-none-selected-text="<?php echo _l('none_type'); ?>">
                          <option value="8"><?php echo _l('annual_leave') ?></option>
                          <option value="2"><?php echo _l('maternity_leave') ?></option>
                          <option value="4"><?php echo _l('private_work_without_pay') ?></option>
                          <option value="1"><?php echo _l('sick_leave') ?></option>
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
                  <div class="col-md-12">
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
                  <div class="col-md-6 start_time">
                    <?php echo render_date_input('start_time', 'From_Date', _d($valid_cur_date)) ?>
                  </div>
                  <div class="col-md-6 end_time">
                    <?php echo render_date_input('end_time', 'To_Date', _d($valid_cur_date)) ?>
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
        <input type="hidden" name="is_calendar_page" value="1">
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


<?php
$date_format = '';
$data_date_format = get_option('dateformat');
if ($data_date_format) {
  $date_format = $data_date_format;
}
?>
<input type="hidden" name="date_format" value="<?php echo html_entity_decode($date_format); ?>">
<?php init_tail(); ?>
<?php
require 'modules/timesheets/assets/js/leave/calendar_leave_application_js.php';
?>
</body>

</html>