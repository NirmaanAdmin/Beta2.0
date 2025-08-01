<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						 <?php echo form_hidden('delivery_id',$delivery_id); ?>
						<div class="row">
		                 <div class="col-md-12 ">
		                  <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
		                  <hr />
		                 </div>
		              	</div>
		              	<div class="row">
	                        <div class="_buttons col-md-3">
	                        	<?php if(!isset($invoice_id)){ ?>
		                        	<?php if (has_permission('warehouse', '', 'create') || is_admin()) { ?>
			                        <a href="<?php echo admin_url('warehouse/add_stock_reconciliation'); ?>"class="btn btn-info pull-left mright10 display-block">
			                            Add New
			                        </a>
			                        <?php } ?>
			                    <?php } ?>

		                    </div>
		                     <div class="col-md-1 pull-right">
		                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.delivery_sm','#delivery_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
		                    </div>
                    	</div>
                    	<br/>
                        <div class="row">
                            <div  class="col-md-3">
                                <?php
                                 $input_attr_e = [];
                                 $input_attr_e['placeholder'] = _l('day_vouchers');

                             echo render_date_input('date_add','','',$input_attr_e ); ?>
                            </div>
                            <div class="col-md-3">
                              <select name="approval" id="approval" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('status_label'); ?>">
                                  <option value=""></option>
                                  <option value="0"><?php echo _l('not_yet_approve'); ?></option>
                                  <option value="1"><?php echo _l('approved'); ?></option>
                                  <option value="-1"><?php echo _l('reject'); ?></option>
                              </select>
                            </div>
                            <div class="col-md-3">
                              <select name="delivery_status" id="delivery_status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('reconciliation_status_new'); ?>">
                                  <option value=""></option>
                                  <option value="ready_to_deliver"><?php echo _l('wh_ready_to_reconcile_new'); ?></option>
                                  <option value="delivery_in_progress"><?php echo _l('wh_reconciliation_in_progress_new'); ?></option>
                                  <option value="delivered"><?php echo _l('wh_reconciled_new'); ?></option>
                                  <option value="received"><?php echo _l('wh_received'); ?></option>
                                  <option value="returned"><?php echo _l('wh_returned'); ?></option>
                                  <option value="not_delivered"><?php echo _l('wh_not_delivered_new'); ?></option>
                              </select>
                            </div>

                        </div>

                    <br/>
                    <?php render_datatable(array(
                        _l('id'),
                        _l('Reconciliation Voucher Code'),
                        _l('reference_purchase_order'),
                        _l('Reconciliation Date'),
                        // _l('invoices'),
                        // _l('staff_id'),
                        _l('status_label'),
                        _l('Reconciliation Status'),
                        _l('options'),
                        ),'table_manage_delivery',['delivery_sm' => 'delivery_sm']); ?>

					</div>
				</div>
			</div>
		<div class="col-md-7 small-table-right-col">
            <div id="delivery_sm_view" class="hide">
            </div>
        </div>
        <?php $invoice_value = isset($invoice_id) ? $invoice_id: '' ;?>
        <?php echo form_hidden('invoice_id', $invoice_value) ?>

		</div>
	</div>
</div>

<div class="modal fade" id="send_goods_delivery" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('warehouse/send_goods_delivery'),array('id'=>'send_goods_delivery-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_delivery_note_by_email'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_goods_delivery"></div>
              <div id="goods_delivery_invoice_id"></div>
              <div class="row">
                <div class="col-md-12 form-group">
                  <label for="customer_name"><span class="text-danger">* </span><?php echo _l('customer_name'); ?></label>
                    <select name="customer_name" id="customer_name" class="selectpicker" required  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >

                    </select>
                    <br>
                </div>

                <div class="col-md-12">
                	<label for="email"><span class="text-danger">* </span><?php echo _l('email'); ?></label>
                  	<?php echo render_input('email','','','',array('required' => 'true')); ?>
                </div>

                <div class="col-md-12">
                  <label for="subject"><span class="text-danger">* </span><?php echo _l('_subject'); ?></label>
                  <?php echo render_input('subject','','','',array('required' => 'true')); ?>
                </div>
                <div class="col-md-12">
                  <label for="attachment"><span class="text-danger">* </span><?php echo _l('acc_attach'); ?></label>
                  <?php echo render_input('attachment','','','file',array('required' => 'true')); ?>
                </div>
                <div class="col-md-12">
                  <?php echo render_textarea('content','email_content','',array(),array(),'','tinymce') ?>
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
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/manage_stock_reconciliation_js.php';?>
</body>
</html>
