<div class="row">
	<div class="col-md-12">
		<?php echo form_hidden('purchase_id',$purchase_id); ?>
		<div class="row">    
			<div class="_buttons col-md-3">
				<?php if (has_permission('fixed_equipment_inventory', '', 'create') || is_admin()) { ?>
					<a href="<?php echo admin_url('fixed_equipment/manage_goods_receipt'); ?>"class="btn btn-info pull-left mright10 display-block">
						<?php echo _l('stock_received_docket'); ?>
					</a>
				<?php } ?>
			</div>
			<div class="col-md-1 pull-right">
				<a href="#" class="btn btn-warning pull-right btn-with-tooltip toggle-small-view hidden-xs back-to-management hide" onclick="toggle_small_view_proposal('.purchase_sm','#purchase_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('fe_back_to_management'); ?>">
					<i class="fa fa-angle-double-left"></i> <?php echo _l('fe_back_to_management'); ?>
				</a>
			</div>
		</div>
	</div>
	<div class="col-md-12" id="small-table">
		<div class="row">
			<div  class="col-md-3 pull-right">
				<?php 
				$input_attr_e = [];
				$input_attr_e['placeholder'] = _l('day_vouchers');

				echo render_date_input('date_add','','',$input_attr_e ); ?>
			</div> 
		</div>
		<br/>
		<?php render_datatable(array(
			_l('id'),
			_l('stock_received_docket_code'),
			_l('supplier_name'),
			_l('Buyer'),
			_l('day_vouchers'),
			_l('total_tax_money'),
			_l('total_goods_money'),
			_l('value_of_inventory'),
			_l('total_money'),
			_l('status_label'),
		),'table_manage_goods_receipt',['purchase_sm' => 'purchase_sm']); ?>
	</div>
	<div class="col-md-12 small-table-right-col mtop20 mbot25">
		<div id="purchase_sm_view" class="hide mbot25">
		</div>
	</div>
</div>

<div class="modal fade" id="send_goods_received" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(admin_url('warehouse/send_goods_received'),array('id'=>'send_goods_received-form')); ?>
		<div class="modal-content modal_withd">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span><?php echo _l('send_received_note'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="additional_goods_received"></div>
				<div class="row">
					<div class="col-md-12 form-group">
						<label for="vendor"><span class="text-danger">* </span><?php echo _l('vendor'); ?></label>
						<select name="vendor[]" id="vendor" class="selectpicker" required multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
							<?php foreach($vendors as $s) { ?>
								<option value="<?php echo fe_htmldecode($s['userid']); ?>"><?php echo fe_htmldecode($s['company']); ?></option>
							<?php } ?>
						</select>
						<br>
					</div>     
					
					<div class="col-md-12">
						<label for="subject"><span class="text-danger">* </span><?php echo _l('subject'); ?></label>
						<?php echo render_input('subject','','','',array('required' => 'true')); ?>
					</div>
					<div class="col-md-12">
						<label for="attachment"><span class="text-danger">* </span><?php echo _l('attachment'); ?></label>
						<?php echo render_input('attachment','','','file',array('required' => 'true')); ?>
					</div>
					<div class="col-md-12">
						<?php echo render_textarea('content','content','',array(),array(),'','tinymce') ?>
					</div>     
					<div id="type_care">
						
					</div>        
				</div>
			</div>
			<div class="modal-footer">
				<button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>var hidden_columns = [3,4,5];</script>
