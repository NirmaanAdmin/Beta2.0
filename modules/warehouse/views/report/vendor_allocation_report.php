<?php echo form_open_multipart(admin_url('warehouse/vendor_allocation_report_pdf'), array('id'=>'print_allocation_report')); ?>
<div class="row">
  <div class="col-md-3 form-group">
    <select name="vendors[]" id="vendors" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>" data-actions-box="true">
      <option value=""></option>
      <?php
      $vendor = get_pur_vendor_list();
      foreach ($vendor as $vendors) { ?>
        <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
      <?php  } ?>
    </select>
  </div>
  <div class="col-md-1">
    <div class="form-group">
      <div class="btn-group">
       <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf-o"></i>Export <span class="caret"></span></a>
       <ul class="dropdown-menu dropdown-menu-right">
          <li class="hidden-xs">
            <a href="#" onclick="vendor_allocation_submit(this); return false;">
              <?php echo _l('download_pdf'); ?>
            </a>
          </li>
       </ul>
       </div>
     </div>
  </div>
</div>
<?php echo form_close(); ?>

<hr class="hr-panel-heading" />
<div class="col-md-12" id="report">
    <div class="panel panel-info col-md-12 panel-padding">
      <div class="panel-body" id="vendor_a_report">
      </div>
    </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/vendor_allocation_report_js.php'; ?>
</body>
</html>
