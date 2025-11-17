<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php echo form_open_multipart(admin_url('purchase/pur_bill_form'),array('id'=>'pur_bill-form','class'=>'_pur_bill_form _transaction_form')); ?>
			<?php
			if(isset($pur_bill)){
				echo form_hidden('isedit'); 
			}
			?>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold"><i class="fa <?php if(isset($pur_bill)){ echo 'fa-pencil-square';}else{ echo 'fa-plus';} ?>" aria-hidden="true"></i> <?php echo _l($title); ?> <?php if(isset($pur_bill)){ echo ' '.pur_html_entity_decode($pur_bill->$bill_code); } ?></h4>
								<hr />
							</div>
						</div> 
						<div class="row">
							<?php $additional_discount = 0; ?>
							<input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">
							<div class="col-md-6">
								<?php echo form_hidden('id', (isset($pur_bill) ? $pur_bill->id : '') ); ?>
								<div class="col-md-6 pad_left_0">
									<label for="bill_code"><span class="text-danger">* </span><?php echo _l('bill_code'); ?></label>
									<?php
									$prefix = get_purchase_option('pur_bill_prefix');
									$next_number = get_purchase_option('next_bill_number');
									$number = (isset($pur_bill) ? $pur_bill->number : $next_number);
									echo form_hidden('number',$number); ?> 
									<?php $bill_code = ( isset($pur_bill) ? $pur_bill->bill_code : $prefix.str_pad($next_number,5,'0',STR_PAD_LEFT));
									echo render_input('bill_code','',$bill_code ,'text',array('readonly' => '', 'required' => 'true')); ?>
								</div>

								<div class="col-md-6 pad_right_0">
									<?php $bill_number = ( (isset($pur_bill) && $pur_bill->bill_number != '') ? $pur_bill->bill_number : $bill_code);
									echo render_input('bill_number','bill_number',$bill_number ,'text',array()); ?>
								</div>

								<div class="col-md-6 pad_left_0 form-group">
									<label for="vendor"><span class="text-danger">* </span><?php echo _l('pur_vendor'); ?></label>
									<select name="vendor" id="vendor" class="selectpicker" disabled required="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach($vendors as $ven){ ?>
											<option value="<?php echo pur_html_entity_decode($ven['userid']); ?>" <?php if(isset($vendor_id) && $vendor_id == $ven['userid']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($ven['vendor_code'].' '.$ven['company']); ?></option>
										<?php } 
										?>
									</select>
									<?php echo form_hidden('vendor',$vendor_id); ?>
								</div>
								<?php
								if(!empty($pur_bill->pur_order)) { ?>
									<div class="col-md-6 pad_right_0 form-group">
										<label for="pur_order"><?php echo _l('pur_order'); ?></label>
										<select name="pur_order" id="pur_order" class="selectpicker" disabled data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
											<option value=""></option>
											<?php foreach($pur_orders as $ct){ ?>
												<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if(isset($pur_bill) && $pur_bill->pur_order == $ct['id']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($ct['pur_order_number'] . ' - ' . $ct['pur_order_name']); ?></option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>
								<?php
								if(!empty($pur_bill->wo_order)) { ?>
									<div class="col-md-6 pad_right_0 form-group">
										<label for="wo_order"><?php echo _l('wo_order'); ?></label>
										<select name="wo_order" id="wo_order" class="selectpicker" disabled data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
											<option value=""></option>
											<?php foreach($wo_orders as $ct){ ?>
												<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if(isset($pur_bill) && $pur_bill->wo_order == $ct['id']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($ct['wo_order_number'] . ' - ' . $ct['wo_order_name']); ?></option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>
								<div class="col-md-6 pad_left_0">
									<label for="invoice_date"><span class="text-danger">* </span><?php echo _l('Bill Date'); ?></label>
									<?php $invoice_date = ( isset($pur_bill) ? _d($pur_bill->invoice_date) : _d(date('Y-m-d')) );
									echo render_date_input('invoice_date','',$invoice_date,array( 'required' => 'true')); ?>
								</div>
								<div class="col-md-6 pad_right_0">
									<label for="invoice_date"><?php echo _l('pur_due_date'); ?></label>
									<?php $duedate = ( isset($pur_bill) ? _d($pur_bill->duedate) : _d(date('Y-m-d')) );
									echo render_date_input('duedate','',$duedate); ?>
								</div>
								<?php if (is_admin()) { ?>
			                      <div class="col-md-6 form-group">
			                        <label for="pur_change_status_to"><?php echo _l('pur_change_status_to'); ?></label>
			                        <select name="status" id="status" class="selectpicker" onchange="change_status_pur_bill(this,<?php echo ($pur_bill->id); ?>); return false;" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
			                          <option value=""></option>
			                          <option value="1"><?php echo _l('purchase_draft'); ?></option>
			                          <option value="4"><?php echo _l('approval_request_sent'); ?></option>
			                          <option value="2"><?php echo _l('purchase_approved'); ?></option>
			                          <option value="3"><?php echo _l('pur_rejected'); ?></option>
			                        </select>
			                      </div>
			                    <?php } ?>
							</div>
							<div class="col-md-6">
								<div class="col-md-12 pad_left_0 pad_right_0 form-group">
									<div id="inputTagsWrapper">
										<label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
										<input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($pur_bill) ? prep_tags_input(get_tags_in($pur_bill->id,'pur_invoice')) : ''); ?>" data-role="tagsinput">
									</div>
								</div>
								<div class="col-md-6 pad_left_0">
									<?php $transactionid = ( isset($pur_bill) ? $pur_bill->transactionid : '');
									echo render_input('transactionid','transaction_id',$transactionid); ?>
								</div>
								<div class="col-md-6 pad_right_0">
									<?php $transaction_date = ( isset($pur_bill) ? $pur_bill->transaction_date : '');
									echo render_date_input('transaction_date','transaction_date',$transaction_date); ?>
								</div>
								<div class="col-md-6 pad_left_0">
									<div class="form-group select-placeholder">
										<label for="discount_type"
										class="control-label"><?php echo _l('discount_type'); ?></label>
										<select name="discount_type" class="selectpicker" data-width="100%"
										data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value="before_tax" <?php
										if (isset($pur_bill)) {
											if ($pur_bill->discount_type == 'before_tax') {
												echo 'selected';
											}
										} ?>><?php echo _l('discount_type_before_tax'); ?></option>
										<option value="after_tax" <?php if (isset($pur_bill)) {
											if ($pur_bill->discount_type == 'after_tax' || $pur_bill->discount_type == null) {
												echo 'selected';
											}
										}else {
											echo 'selected';
										} ?>><?php echo _l('discount_type_after_tax'); ?></option>
										</select>
									</div>
								</div>
								<div class="col-md-6 pad_right_0">
									<label for="project"><span class="text-danger">* </span><?php echo _l('project'); ?></label>
									<select name="project_id" id="project" class="selectpicker" disabled data-live-search="true" data-width="100%" required="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($projects as $s) { ?>
											<option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($project_id) && $s['id'] == $project_id) {                                                                                              echo 'selected';
										} else if (!isset($pur_bill) && $s['id'] == $project_id) {echo 'selected';
									} ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
									<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body">
			            <label for="attachment"><?php echo _l('attachment'); ?></label>
			            <div class="attachments">
			              <div class="attachment">
			                <div class="col-md-5 form-group" style="padding-left: 0px;">
			                  <div class="input-group">
			                    <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
			                    <span class="input-group-btn">
			                      <button class="btn btn-success add_more_attachments p8" type="button"><i class="fa fa-plus"></i></button>
			                    </span>
			                  </div>
			                </div>
			              </div>
			            </div>
			            <br /> <br />

			            <?php
			            if (isset($attachments) && count($attachments) > 0) {
			              foreach ($attachments as $value) {
			                echo '<div class="col-md-3">';
			                $path = get_upload_path_by_type('purchase') . 'pur_bills/' . $value['rel_id'] . '/' . $value['file_name'];
			                $is_image = is_image($path);
			                if ($is_image) {
			                  echo '<div class="preview_image">';
			                }
			            ?>
			                <a href="<?php echo site_url('download/file/pur_bills/' . $value['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-purchase-<?php echo $value['rel_id']; ?>" <?php } ?>>
			                  <i class="<?php echo get_mime_class($value['filetype']); ?>"></i> <?php echo $value['file_name']; ?>
			                  <?php if ($is_image) { ?>
			                    <img class="mtop5" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']); ?>" style="height: 165px;">
			                  <?php } ?>
			                </a>
			                <?php
			                // echo '</div>';
			                echo '<a href="' . admin_url('purchase/delete_pur_bills_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			                ?>
			            <?php if ($is_image) { echo '</div>'; } ?>
			            <?php echo '</div>';
			              }
			            } ?>
			        </div>
					<div class="panel-body mtop10 invoice-item">
				        <div class="row">
				        	<div class="col-md-12">
				        		<div class="table-responsive s_table ">
				        			<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				        				<thead>
				        					<tr>
				        						<th></th>
				        						<th align="left"><?php echo _l('Uniclass Code'); ?></th>
				        						<th align="left"><?php echo _l('item_description'); ?></th>
				        						<th align="right"><?php echo _l('unit_price'); ?></th>
				        						<th align="right" class="qty"><?php echo _l('Ordered Quantity'); ?></th>
				        						<th align="right"><?php echo _l('bill_bifurcation'); ?></th>
				        						<?php
				        						if(!empty($payment_certificates)) {
				        							foreach ($payment_certificates as $pkey => $pvalue) { ?>
				        								<th align="right">PC<?php echo $pkey+1; ?> Bifurcation <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo $pvalue['pc_number']; ?>"></i></th>
				        							<?php }
				        						} ?>
				        					</tr>
				        				</thead>
				        				<tbody>
				        					<?php echo $pur_bill_row_template; ?>
				        				</tbody>
				        			</table>
				        		</div>
				        	</div>
				        	<div class="col-md-8 col-md-offset-4">
				        		<table class="table text-right">
				        			<tbody>
				        				<tr id="totalmoney">
				        					<td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
				        						<?php echo form_hidden('grand_total', ''); ?>
				        					</td>
				        					<td class="wh-total">
				        					</td>
				        				</tr>
				        			</tbody>
				        		</table>
				        	</div>
				        	<div id="removed-items"></div> 
				        </div>
					</div>
				    <div class="row">
				      	<div class="col-md-12 mtop15">
				      		<div class="panel-body bottom-transaction">
				      			<div class="col-md-12 pad_left_0 pad_right_0">
				      				<?php $adminnote = ( isset($pur_bill) ? $pur_bill->adminnote : '');
				      				echo render_textarea('adminnote','adminnote',$adminnote, array('rows' => 7)) ?>
				      			</div>
				      			<div class="btn-bottom-toolbar text-right">
				      				<?php if (count($list_approve_status) == 0) { ?>
				                      <input type="submit" name="save_and_send" class="btn-tr save_detail btn btn-info mleft10 transaction-submit" value="Save & Send" />
				                    <?php } ?>
				      				<button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit">
				      					<?php echo _l('submit'); ?>
				      				</button>
				      			</div>
				      		</div>
				      		<div class="btn-bottom-pusher"></div>
				      	</div>
				    </div>
		    	</div>
		    	<?php echo $pur_bill_row_model; ?>
		    	<?php echo $pur_pc_bill_row_model; ?>
		    	<?php echo form_close(); ?>
		  	</div>
		</div>
		<?php if (count($list_approve_status) > 0) { ?>
	      <div class="row">
	        <div class="col-md-12">
	          <div class="panel_s">
	            <div class="panel-body">
	              <div class="project-overview-right">
	                <div class="row">
	                  <div class="col-md-12 project-overview-expenses-finance">
	                    <?php
	                    $this->load->model('staff_model');
	                    $enter_charge_code = 0;
	                    foreach ($list_approve_status as $value) {
	                      $value['staffid'] = explode(', ', $value['staffid'] ?? '');

	                      if ($value['action'] == 'sign') { ?>
	                        <div class="col-md-4 apr_div">
	                          <p class="text-uppercase text-muted no-mtop bold">
	                            <?php
	                            $staff_name = '';
	                            $st = _l('status_0');
	                            $color = 'warning';
	                            foreach ($value['staffid'] as $key => $val) {
	                              if ($staff_name != '') {
	                                $staff_name .= ' or ';
	                              }
	                              $staff_name .= $this->staff_model->get($val)->firstname;
	                            }
	                            echo pur_html_entity_decode($staff_name);
	                            ?>
	                          </p>
	                          <?php if ($value['approve'] == 2) {
	                          ?>
	                            <img src="<?php echo site_url(PURCHASE_PATH . 'pur_order/signature/' . $estimate->id . '/signature_' . $value['id'] . '.png'); ?>" class="img_style">
	                            <br><br>
	                            <p class="bold text-center text-success"><?php echo _l('signed') . ' ' . _dt($value['date']); ?></p>
	                          <?php } ?>
	                        </div>
	                      <?php } else { ?>
	                        <div class="col-md-4 apr_div">
	                          <p class="text-uppercase text-muted no-mtop bold">
	                            <?php
	                            $staff_name = '';
	                            foreach ($value['staffid'] as $key => $val) {
	                              if ($staff_name != '') {
	                                $staff_name .= ' or ';
	                              }
	                              $staff_name .= $this->staff_model->get($val)->firstname;
	                            }
	                            echo pur_html_entity_decode($staff_name);
	                            ?>
	                          </p>

	                          <?php if ($value['approve'] == 2) {
	                          ?>
	                            <?php if ($value['approve_by_admin'] == 1) { ?>
	                              <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved_by_admin.png'); ?>" class="img_style">
	                            <?php } else { ?>
	                              <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved.png'); ?>" class="img_style">
	                            <?php } ?>
	                          <?php } elseif ($value['approve'] == 3) { ?>
	                            <img src="<?php echo site_url(PURCHASE_PATH . 'approval/rejected.png'); ?>" class="img_style">
	                          <?php } ?>
	                          <br><br>
	                          <p><?php echo pur_html_entity_decode($value['note']) ?></p>
	                          <p class="bold text-center text-<?php if ($value['approve'] == 2) {
	                                                            echo 'success';
	                                                          } elseif ($value['approve'] == 3) {
	                                                            echo 'danger';
	                                                          } ?>"><?php echo _dt($value['date']); ?>
	                          </p>

	                          <?php
	                          if (isset($check_approve_status['staffid'])) {
	                            if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $value['staffid'] == $check_approve_status['staffid']) { ?>
	                              <div class="btn-group">
	                                <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>

	                                <ul class="dropdown-menu dropdown-menu-right ul_style" style="width: max-content;">
	                                  <li>
	                                    <div class="col-md-12">
	                                      <?php echo render_textarea('reason', 'reason'); ?>
	                                    </div>
	                                  </li>
	                                  <li>
	                                    <div class="row text-right col-md-12">
	                                      <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_bill_bifurcation_request(<?php echo pur_html_entity_decode($pur_bill->id); ?>); return false;" class="btn btn-success mright15"><?php echo _l('approve'); ?></a>
	                                      <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_bill_bifurcation_request(<?php echo pur_html_entity_decode($pur_bill->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
	                                    </div>
	                                  </li>
	                                </ul>
	                              </div>
	                          <?php }
	                          } ?>
	                        </div>
	                    <?php }
	                    } ?>
	                  </div>
	                </div>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	    <?php } ?>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/pur_bill_js.php';?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
