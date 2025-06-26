<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
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

  #scroll-slider {
    position: absolute;
    top: 64px;
    right: 10px;
    width: 200px;
    height: 2px;
    background-color: #000000;
    border-radius: 5px;
    z-index: 10000;
    cursor: pointer;
  }

  #scroll-thumb {
    width: 15px;
    height: 15px;
    background-color: #bca455;
    border-radius: 15px;
    position: relative;
    top: -6px;
    /* transition: left 0.2s ease; */
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="loader-container hide" id="loader-container">
      <img src="<?php echo site_url('modules/timesheets/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4><?php echo _l('timekeeping') ?>
              <hr>
            </h4>
            <div class="horizontal-tabs mb-5">
            </div>
            <input type="hidden" name="current_month" value="<?php echo date('Y-m'); ?>">
            <?php
            if (has_permission('attendance_management', '', 'view') || is_admin()) {
            ?>
              <div class="row filter_by">
                <div class="col-md-2 leads-filter-column">
                  <?php echo render_input('month_timesheets', 'month', date('Y-m'), 'month'); ?>
                </div>
                <div class="col-md-3 leads-filter-column">
                  <?php echo render_select('department_timesheets', $departments, array('departmentid', 'name'), 'department'); ?>
                </div>
                <div class="col-md-3 leads-filter-column">
                  <?php
                  $new_role = [];
                  $role_setting = get_timesheets_option('timekeeping_manually_role');
                  if ($role_setting != '') {
                    $exp_role_id = explode(',', $role_setting);
                    foreach ($roles as $key => $role) {
                      if (in_array($role['roleid'], $exp_role_id)) {
                        $new_role[] = $role;
                      }
                    }
                  } else {
                    $new_role = $roles;
                  }

                  echo render_select('job_position_timesheets', $new_role, array('roleid', 'name'), 'role'); ?>
                </div>
                <div class="col-md-3 leads-filter-column">
                  <?php echo render_select('staff_timesheets[]', $staffs, array('staffid', array('firstname', 'lastname')), 'staff', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                </div>
                <div class="col-md-1 mtop25">
                  <button type="button" class="btn btn-info timesheets_filter"><?php echo _l('filter'); ?></button>
                </div>
              </div>
            <?php } else {
              echo form_hidden('month_timesheets', date('Y-m'));
            ?>
            <?php } ?>
            <?php echo form_open(admin_url('timesheets/manage_timesheets'), array('id' => 'timesheets-form')); ?>
            <hr class="hr-panel-heading no-margin" />
            <div class="row mtop15" style="position: relative;">
              <div class="col-md-8 line-suggestion">

                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('Le_x_timekeeping'); ?>" class="btn">HO</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('P_timekeeping'); ?>" class="btn">P</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('L_x_timekeeping'); ?>" class="btn">L</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Sunday Off" class="btn">OFF</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Half Day" class="btn">H/F</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Sick Leave" class="btn">SL</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Casual Leave" class="btn">CL</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Out For Work" class="btn">OW</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Maternity Leave" class="btn">ML</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Work From Home" class="btn">W/H</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="Compensatory Off" class="btn">CO</button>
                <button type="button" data-toggle="tooltip" data-placement="top" data-original-title=" NOT APPLICABLE" class="btn">N/A</button>

                <div class="clearfix"></div>
              </div>
              <div class="col-md-4" style="margin-bottom: 30px;">
                <a href="javascript:void(0)" class="btn btn-default pull-right mtop5 mleft10 export_excel">
                  <i class="fa fa-file-excel"></i> <?php echo _l('export_to_excel'); ?>
                </a>
                <?php if ($data_timekeeping_form == 'timekeeping_manually') { ?>
                  <button type="button" onclick="open_check_in_out();" class="btn btn-info pull-right display-block mtop5 check_in_out_timesheet" data-toggle="tooltip" title="" data-original-title="<?php echo _l('check_in') . ' / ' . _l('check_out'); ?>"><?php echo _l('check_in'); ?> / <?php echo _l('check_out'); ?></button>
                <?php } elseif ($data_timekeeping_form == 'csv_clsx') { ?>
                  <button type="button" class="btn btn-info pull-right display-block mtop5 check_in_out_timesheet" onclick="window.location.href='<?php echo admin_url('timesheets/import_xlsx_attendance'); ?>'" title="<?php echo _l('import_timesheets'); ?>">
                    <?php echo _l('import_timesheets'); ?>
                  </button>
                <?php } ?>
              </div>
              <div class="clearfix"></div>
              <br>
              <div class="col-md-12">
                <div class="form">
                  <div class="hot handsontable htColumnHeaders" id="example">
                  </div>
                  <?php echo form_hidden('time_sheet'); ?>
                  <?php echo form_hidden('month', date('m-Y')); ?>
                  <?php echo form_hidden('latch'); ?>
                  <?php echo form_hidden('unlatch'); ?>
                </div>
                <hr class="hr-panel-heading" />
                <?php
                if ($check_latch_timesheet) {
                  $latched = '';
                  $latch = 'hide';
                } else {
                  $latched = 'hide';
                  $latch = '';
                } ?>


                <?php 
                  if ($notes['note'] != '') {
                    $notes_val = $notes['note'];
                  } else {
                    $notes_val = '';
                  }
                ?>

                  <?php echo render_textarea('note', 'Note', $notes_val, ['rows' => 5]); ?>
                  <button class="btn btn-danger pull-right unlatch_time_sheet mleft5 <?php echo html_entity_decode($latched); ?>" id="btn_unlatch" onclick="return confirm('<?php echo _l('timekeeping_unlatch'); ?>')"><?php echo _l('reopen_attendance'); ?></button>

                  <button class="btn btn-info pull-right latch_time_sheet mleft5 <?php echo html_entity_decode($latch); ?>" id="btn_latch" onclick="return confirm('<?php echo _l('timekeeping_latch'); ?>')"><?php echo _l('close_attendance'); ?></button>

                  <?php
                  $data_timekeeping_form = get_timesheets_option('timekeeping_form');
                  if ($data_timekeeping_form != 'timekeeping_task') { ?>
                    <a class="btn btn-info pull-right edit_timesheets mleft5 <?php echo html_entity_decode($latch); ?>"><?php echo _l('edit'); ?></a>
                  <?php } ?>
                  <button class="btn btn-info pull-right save_time_sheet mleft5 hide"><?php echo _l('submit'); ?></button>

                  <a class="btn btn-default pull-right exit_edit_timesheets mleft5 hide"><?php echo _l('close'); ?></a>


              </div>
              <!-- Custom Scroll Slider -->
              <div id="scroll-slider">
                <div id="scroll-thumb"></div>
              </div>
            </div>
            <?php echo form_hidden('is_edit', 0); ?>
            <?php echo form_close(); ?>

            <div class="modal" id="timesheets_detail_modal" tabindex="-1" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content width-100">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 id='title_detail'>
                      <?php echo _l('detail'); ?>
                    </h4>
                  </div>
                  <div class="modal-body">
                    <ul class="list-group" id="ul_timesheets_detail_modal">
                    </ul>
                  </div>
                  <div class="modal-footer">
                    <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="import_timesheets_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <?php echo form_open_multipart(admin_url('timesheets/import_timesheets'), array('id' => 'import-timesheets-form')); ?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel"><?php echo _l('import_timesheets'); ?></h4>
      </div>
      <div class="modal-body">
        <?php echo render_input('file_timesheets', 'file', '', 'file', ['accept' => ".xlsx, .xls, .csv"]); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <a href="<?php echo site_url('modules/timesheets/uploads/timesheets/import_timesheets.xlsx'); ?>" class="btn btn-primary"><?php echo _l('download_sample'); ?></a>
        <button class="btn btn-primary"><?php echo _l('submit'); ?></button>
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
</body>

</html>
<?php require 'modules/timesheets/assets/js/timesheets.php'; ?>