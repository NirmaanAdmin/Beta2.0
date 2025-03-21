<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div  class="row">
 <div class="col-md-12">
  <?php if (has_permission('fixed_equipment_inventory', '', 'create') || is_admin()) { ?>
    <button class="btn btn-primary" onclick="add(); return false;"><?php echo _l('add'); ?></button>
  <?php } ?>
</div>
<div class="clearfix"></div>
<br>
<div class="clearfix"></div>
<div  class="col-md-12">
  <table class="table table-warehouses scroll-responsive">
   <thead>
     <tr>
      <th><?php echo _l('id'); ?></th>
      <th><?php echo _l('fe_name'); ?></th>
      <th ><?php echo _l('fe_address'); ?></th>
      <th ><?php echo _l('fe_sequence'); ?></th>
      <th ><?php echo _l('fe_display'); ?></th>
      <th ><?php echo _l('fe_note'); ?></th>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
 </tfoot>
</table>
</div>
</div>




<div class="modal fade" id="add" tabindex="-1" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">
     <span class="add-title"><?php echo _l('fe_add_warehouse'); ?></span>
     <span class="edit-title hide"><?php echo _l('fe_edit_warehouse'); ?></span>
   </h4>
 </div>
 <?php echo form_open(admin_url('fixed_equipment/add_warehouse'),array('id'=>'form_add_warehouse')); ?>              
 <div class="modal-body">
  <?php $this->load->view('settings/includes/warehouse_modal_content'); ?>
 </div>
 <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>                   
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
