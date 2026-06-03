<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $related = [ 
	0 => ['id' => 'co_request', 'name' => _l('co_request')],
	1 => ['id' => 'pur_quotation', 'name' => _l('pur_quotation')],
	2 => ['id' => 'pur_order', 'name' => _l('Change order')],
	3 => ['id' => 'payment_request', 'name' => _l('payment_request')],
]; ?>
<div class="_buttons">
	<a href="#" class="btn btn-info pull-left" onclick="new_approval_setting(); return false;"><?php echo _l('new_approval_setting'); ?></a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
	<thead>
		<th><?php echo _l('id'); ?></th>
		<th><?php echo _l('project'); ?></th>
		<th><?php echo _l('name'); ?></th>
		<th><?php echo _l('related'); ?></th>
		<th><?php echo _l('options'); ?></th>
	</thead>
	<tbody>
	<?php foreach($approval_setting as $key => $value){ ?>
		<tr>
		   <td><?php echo $key + 1; ?></td>
		   <td><?php echo get_project_name_by_id($value['project_id']); ?></td>
		   <td><?php echo changee_pur_html_entity_decode($value['name']); ?></td>
		   <td>
		   <?php
		   foreach ($related as $item) {
			    if ($item['id'] === $value['related']) {
			        echo $item['name'];
			        break;
			    }
		    }
		    ?>
		   </td>
		   <td>
		     <a href="#" onclick="edit_approval_setting(this,<?php echo changee_pur_html_entity_decode($value['id']); ?>); return false" data-name="<?php echo changee_pur_html_entity_decode($value['name']); ?>" data-related="<?php echo html_entity_decode($value['related']); ?>" data-project='<?php echo html_entity_decode($value['project_id']); ?>' data-approver='<?php echo html_entity_decode($value['approver']); ?>' class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
		      <a href="<?php echo admin_url('changee/delete_approval_setting/'.$value['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
		   </td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<?php
$hr_record_status = 0; 
if(changee_get_status_modules_pur('hr_profile') == true){
	$hr_record_status = 1;
} ?>

<div class="modal fade" id="approval_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog withd_1k" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('edit_approval_setting'); ?></span>
					<span class="add-title"><?php echo _l('new_approval_setting'); ?></span>
				</h4>
			</div>
			<?php echo form_open('changee/approval_setting',array('id'=>'approval-setting-form')); ?>
			<?php echo form_hidden('approval_setting_id'); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo render_select('project_id', $projects, array('id','name'), 'project'); ?>
						<?php echo render_input('name','subject','','text'); ?>
						<?php echo render_select('related',$related,array('id','name'),'task_single_related'); ?>
						<div class="select-placeholder form-group">
							<label for="approver" class="control-label"><?php echo _l('approver'); ?></label>
							<select name="approver[]" id="approver" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" data-actions-box="true">
	                        </select>
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

