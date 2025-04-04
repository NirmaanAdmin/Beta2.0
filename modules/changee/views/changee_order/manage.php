<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="panel_s mbot10">
				<div class="panel-body">
	              	<div class="row">    
	                    <div class="_buttons col-md-3">
	                    	<?php if (has_permission('changee_orders', '', 'create') || is_admin()) { ?>
	                        <a href="<?php echo admin_url('changee/pur_order'); ?>"class="btn btn-info pull-left mright10 display-block">
	                            <?php echo _l('new_pur_order'); ?>
	                        </a>
	                        <?php } ?>
                           <div class="btn-group pull-left">
                              <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('co_voucher'); ?>&nbsp;<span class="caret"></span></a>
                              <ul class="dropdown-menu dropdown-menu-right">
                                 <li class="hidden-xs"><a href="<?php echo admin_url('changee/po_voucher?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                                 <li class="hidden-xs"><a href="<?php echo admin_url('changee/po_voucher?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                 <li><a href="<?php echo admin_url('changee/po_voucher'); ?>"><?php echo _l('download'); ?></a></li>
                                 <li>
                                    <a href="<?php echo admin_url('changee/po_voucher?print=true'); ?>" target="_blank">
                                    <?php echo _l('print'); ?>
                                    </a>
                                 </li>
                              </ul>
                           </div>
	                    </div>
	                    
	                    <div class="_buttons col-md-1 pull-right">
	                    <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right" onclick="toggle_small_pur_order_view('.table-table_pur_order','#pur_order'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
	                	</div>
	            	</div>
	              	<div class="row">
	              		<hr>
	              		<div class="col-md-2">
	                        <?php echo render_date_input('from_date',_l('from_date'),''); ?>
	                    </div>
	                    <div class="col-md-2">
	                        <?php echo render_date_input('to_date',_l('to_date'),''); ?>
	                    </div>

                       <div class=" col-md-2 form-group">
                          <label for="co_request"><?php echo _l('co_request'); ?></label>
                          <select name="co_request[]" id="co_request" class="selectpicker" onchange="coppy_co_request(); return false;"  data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                              <?php foreach($co_request as $s) { ?>
                              <option value="<?php echo changee_pur_html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $pur_order->co_request != '' && $pur_order->co_request == $s['id']){ echo 'selected'; } ?> ><?php echo changee_pur_html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
                                <?php } ?>
                          </select>
                        </div>

	                    <div class="col-md-3 form-group">
	                        <?php 
	                        $statuses = [0 => ['id' => '1', 'name' => _l('changee_not_yet_approve')],
	                    	1 => ['id' => '2', 'name' => _l('changee_approved')],
	                		2 => ['id' => '3', 'name' => _l('changee_reject')],
	                		3 => ['id' => '4', 'name' => _l('cancelled')],];

	                        echo render_select('status[]',$statuses,array('id','name'),'approval_status','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false); ?>
	                    </div>
	                    <div class="col-md-3 form-group">
	                        <?php echo render_select('vendor_ft[]',$vendors,array('userid','company'),'vendor','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false); ?>
	                    </div>

                       <div class="col-md-3 form-group">
                        <label for="type"><?php echo _l('type'); ?></label>
                         <select name="type[]" id="type" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                           <option value="capex" ><?php echo _l('capex'); ?></option>
                            <option value="opex" ><?php echo _l('opex'); ?></option> 
                        </select>
                       </div>

                       <div class="col-md-3 form-group">
                        <label for="project"><?php echo _l('project'); ?></label>
                        <select name="project[]" id="project" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                           <?php foreach($projects as $pj){ ?>
                            <option value="<?php echo changee_pur_html_entity_decode($pj['id']); ?>" ><?php echo changee_pur_html_entity_decode($pj['name']); ?></option>
                           <?php } ?>
                        </select>
                       </div>

                       <div class="col-md-3 form-group">
                           <label for="department"><?php echo _l('department'); ?></label>
                          <select name="department[]" readonly="true" id="department" class="selectpicker" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>" >
                                     
                             <?php foreach($departments as $dpm){ ?>
                               <option value="<?php echo changee_pur_html_entity_decode($dpm['departmentid']); ?>" ><?php echo changee_pur_html_entity_decode($dpm['name']); ?></option>
                             <?php } ?>
                          </select>
                       </div>

                       <div class="col-md-3 form-group">
                         <label for="delivery_status"><?php echo _l('delivery_status'); ?></label>
                         <select name="delivery_status[]" id="delivery_status" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                           <option value="0" ><?php echo _l('undelivered'); ?></option>
                           <option value="1" ><?php echo _l('completely_delivered'); ?></option>
                           <option value="2" ><?php echo _l('pending_delivered'); ?></option>
                           <option value="3" ><?php echo _l('partially_delivered'); ?></option>
                        </select>
                       </div>

	                    
	              	</div>
	            </div>
            </div>
            <div class="row">
				<div class="col-md-12" id="small-table">
					<div class="panel_s">
						<div class="panel-body">
	                    <?php echo form_hidden('pur_orderid',$pur_orderid); ?>
	                    
                        <div class="">
                           <table class="dt-table-loading table table-table_pur_order">
                              <thead>
                                 <tr>
                                    <th><?php echo _l('changee_order'); ?></th>
                                    <th><?php echo _l('vendor'); ?></th>
                                    <th><?php echo _l('order_date'); ?></th>
                                    <th><?php echo _l('group_pur'); ?></th>
                                    <th><?php echo _l('sub_groups_pur'); ?></th>
                                    <th><?php echo _l('area_pur'); ?></th>
                                    <th><?php echo _l('type'); ?></th>
                                    <th><?php echo _l('project'); ?></th>
                                    <th><?php echo _l('department'); ?></th>
                                    <th><?php echo _l('co_description'); ?></th>
                                    <th><?php echo _l('co_value'); ?></th>
                                    <th><?php echo _l('tax_value'); ?></th>
                                    <th><?php echo _l('co_value_included_tax'); ?></th>
                                    <th><?php echo _l('tags'); ?></th>
                                    <th><?php echo _l('approval_status'); ?></th>
                                    <th><?php echo _l('payment_status'); ?></th>
                                    <th><?php echo _l('convert_expense'); ?></th>
                                 </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              <tfoot>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td class="total_co_value"></td>
                                 <td class="total_tax_value"></td>
                                 <td class="total_co_value_included_tax"></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                              </tfoot>
                           </table>
                        </div>                    

							
						</div>
					</div>
				</div>
            	
			<div class="col-md-7 small-table-right-col">
			    <div id="pur_order" class="hide">
			    </div>
			 </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="pur_order_expense" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <?php echo form_open(admin_url('changee/add_expense'),array('id'=>'pur_order-expense-form','class'=>'dropzone dropzone-manual')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('add_new', _l('expense_lowercase')); ?></h4>
         </div>
         <div class="modal-body">
            <div id="dropzoneDragArea" class="dz-default dz-message">
               <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
            </div>
            <div class="dropzone-previews"></div>
            <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('expense_name_help'); ?>"></i>
            <?php echo form_hidden('vendor'); ?>
            <?php echo render_input('expense_name','expense_name'); ?>
            <?php echo render_textarea('note','expense_add_edit_note','',array('rows'=>4),array()); ?>
            <?php echo render_select('clientid',$customers,array('userid','company'),'customer'); ?>

            <?php echo render_select('project_id',$projects,array('id','name'),'project'); ?>

            <?php echo render_select('category',$expense_categories,array('id','name'),'expense_category'); ?>
            <?php echo render_date_input('date','expense_add_edit_date',_d(date('Y-m-d'))); ?>
            <?php echo render_input('amount','expense_add_edit_amount','','number'); ?>
            <div class="row mbot15">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php foreach($taxes as $tax){ ?>
                        <option value="<?php echo changee_pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo changee_pur_html_entity_decode($tax['name']); ?>"><?php echo changee_pur_html_entity_decode($tax['taxrate']); ?>%</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" disabled>
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php foreach($taxes as $tax){ ?>
                        <option value="<?php echo changee_pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo changee_pur_html_entity_decode($tax['name']); ?>"><?php echo changee_pur_html_entity_decode($tax['taxrate']); ?>%</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="hide">
               <?php echo render_select('currency',$currencies,array('id','name','symbol'),'expense_currency',$currency->id); ?>
            </div>
           
            <div class="checkbox checkbox-primary">
               <input type="checkbox" id="billable" name="billable" checked>
               <label for="billable"><?php echo _l('expense_add_edit_billable'); ?></label>
            </div>
            <?php echo render_input('reference_no','expense_add_edit_reference_no'); ?>
           
            <?php
               // Fix becuase payment modes are used for invoice filtering and there needs to be shown all
               // in case there is payment made with payment mode that was active and now is inactive
               $expenses_modes = array();
               foreach($payment_modes as $m){
               if(isset($m['invoices_only']) && $m['invoices_only'] == 1) {continue;}
               if($m['active'] == 1){
               $expenses_modes[] = $m;
               }
               }
               ?>
            <?php echo render_select('paymentmode',$expenses_modes,array('id','name'),'payment_mode'); ?>
            <div class="clearfix mbot15"></div>
            <?php echo render_custom_fields('expenses'); ?>
        	<div id="pur_order_additional"></div>
            <div class="clearfix"></div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>


<?php init_tail(); ?>
</body>
</html>