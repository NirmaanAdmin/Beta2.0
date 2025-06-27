<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .dashboard_stat_title {
    font-size: 18px;
    font-weight: bold;
  }
</style>
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
			                        <a href="<?php echo admin_url('warehouse/goods_delivery'); ?>"class="btn btn-info pull-left mright10 display-block">
			                            Add New
			                        </a>
			                        <?php } ?>
			                    <?php } ?>
                          <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#si-charts-section" aria-expanded="true"aria-controls="si-charts-section">
                          <?php echo _l('Stock Issued Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                          </button>

		                    </div>
		                     <div class="col-md-1 pull-right">
		                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.delivery_sm','#delivery_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
		                    </div>
                    	</div>

                      <div id="si-charts-section" class="collapse in">
                        <div class="row">
                             <div class="col-md-12 mtop20">
                                <div class="row">
                                   <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                            <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                  </path>                          
                                               </svg>                          
                                               <span class="tw-truncate">Total Issued Quantity</span>                 
                                            </div>                      
                                            <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_issued_quantity"></span>  
                                         </div>                    
                                         <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                            <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                            </div>                  
                                         </div>              
                                      </div>          
                                   </div>

                                   <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                            <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                  </path>                          
                                               </svg>                          
                                               <span class="tw-truncate">Number of Stock Issued Entries</span>                 
                                            </div>                      
                                            <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_issued_entries"></span>  
                                         </div>                    
                                         <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                            <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                            </div>                  
                                         </div>              
                                      </div>          
                                   </div>

                                   <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                            <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                  </path>                          
                                               </svg>                          
                                               <span class="tw-truncate">Returnable Items</span>                 
                                            </div>                      
                                            <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_returnable_items"></span>  
                                         </div>                    
                                         <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                            <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                            </div>                  
                                         </div>              
                                      </div>          
                                   </div>

                                </div>
                             </div>
                        </div>
                        <div class="row mtop20">
                          <div class="col-md-5">
                              <p class="mbot15 dashboard_stat_title">Issued Quantity by Material</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="barChartTopMaterials"></canvas>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <p class="mbot15 dashboard_stat_title">Consumption Over Time</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="lineChartOverTime"></canvas>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <p class="mbot15 dashboard_stat_title">Returnable vs Non-Returnable</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="returnablevsnonreturnable"></canvas>
                              </div>
                           </div>
                        </div>
                      </div>

                        <div class="row mtop20">
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
                              <select name="delivery_status" id="delivery_status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('delivery_status_new'); ?>">
                                  <option value=""></option>
                                  <option value="ready_to_deliver"><?php echo _l('wh_ready_to_deliver_new'); ?></option>
                                  <option value="delivery_in_progress"><?php echo _l('wh_delivery_in_progress_new'); ?></option>
                                  <option value="delivered"><?php echo _l('wh_delivered_new'); ?></option>
                                  <option value="received"><?php echo _l('wh_received'); ?></option>
                                  <option value="returned"><?php echo _l('wh_returned'); ?></option>
                                  <option value="not_delivered"><?php echo _l('wh_not_delivered_new'); ?></option>
                              </select>
                            </div>

                        </div>

                    <br/>
                    <?php render_datatable(array(
                        _l('id'),
                        _l('goods_delivery_code_new'),
                        _l('reference_purchase_order'),
                        _l('Issue Date'),
                        // _l('invoices'),
                        // _l('staff_id'),
                        _l('status_label'),
                        _l('delivery_status_new'),
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
<?php require 'modules/warehouse/assets/js/manage_delivery_js.php';?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>
