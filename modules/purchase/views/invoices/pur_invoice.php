<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">

			<?php echo form_open_multipart(admin_url('purchase/pur_invoice_form'), array('id' => 'pur_invoice-form', 'class' => '_pur_invoice_form _transaction_form')); ?>
			<?php
			if (isset($pur_order)) {
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold"><i class="fa <?php if (isset($pur_invoice)) {
																					echo 'fa-pencil-square';
																				} else {
																					echo 'fa-plus';
																				} ?>" aria-hidden="true"></i> <?php echo _l($title); ?> <?php if (isset($pur_invoice)) {
																																			echo ' ' . pur_html_entity_decode($pur_invoice->invoice_number);
																																		} ?></h4>
								<hr />
							</div>
						</div>

						<div class="row">
							<?php $additional_discount = 0; ?>
							<input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">
							<div class="col-md-6">
								<?php echo form_hidden('id', (isset($pur_invoice) ? $pur_invoice->id : '')); ?>
								<div class="col-md-6 pad_left_0">
									<label for="invoice_number"><span class="text-danger">* </span><?php echo _l('invoice_code'); ?></label>
									<?php
									$prefix = get_purchase_option('pur_inv_prefix');
									$next_number = get_purchase_option('next_inv_number');
									$number = (isset($pur_invoice) ? $pur_invoice->number : $next_number);
									echo form_hidden('number', $number); ?>

									<?php $invoice_number = (isset($pur_invoice) ? $pur_invoice->invoice_number : $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT));
									echo render_input('invoice_number', '', $invoice_number, 'text', array('readonly' => '', 'required' => 'true')); ?>
								</div>

								<div class="col-md-6 pad_right_0">
									<?php $vendor_invoice_number = ((isset($pur_invoice) && $pur_invoice->vendor_invoice_number != '') ? $pur_invoice->vendor_invoice_number : $invoice_number);
									echo render_input('vendor_invoice_number', 'invoice_number', $vendor_invoice_number, 'text', array()); ?>
								</div>

								<div class="col-md-6 pad_left_0 form-group">
									<label for="vendor"><span class="text-danger">* </span><?php echo _l('pur_vendor'); ?></label>
									<select name="vendor" id="vendor" class="selectpicker" onchange="pur_vendor_change(this); return false;" required="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($vendors as $ven) { ?>
											<option value="<?php echo pur_html_entity_decode($ven['userid']); ?>" <?php if (isset($pur_invoice) && $pur_invoice->vendor == $ven['userid']) {
																														echo 'selected';
																													} ?>><?php echo pur_html_entity_decode($ven['vendor_code'] . ' ' . $ven['company']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="col-md-6 form-group pad_right_0">
									<?php /*
									<label for="contract"><?php echo _l('contract'); ?></label>
									<select name="contract" id="contract" class="selectpicker" onchange="contract_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($contracts as $ct) { ?>
											<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if (isset($pur_invoice) && $pur_invoice->contract == $ct['id']) {
												echo 'selected';
											} ?>><?php echo pur_html_entity_decode($ct['contract_number']); ?></option>
										<?php } ?>
									</select> */ ?>
									<?php
									$selected = '';

									foreach ($commodity_groups_pur as $group) {
										if (isset($pur_invoice)) {
											if ($pur_invoice->group_pur == $group['id']) {
												$selected = $group['id'];
											}
										}
										if (isset($selected_head)) {
											if ($selected_head == $group['id']) {
												$selected = $group['id'];
											}
										}
									}
									echo render_select('group_pur', $commodity_groups_pur, array('id', 'name'), '<span class="text-danger">* </span>Budget Head', $selected, ['required' => 'true']);
									?>
								</div>

								<div class="col-md-6 form-group pad_left_0">
									<label for="pur_order"><?php echo _l('pur_order'); ?></label>
									<select name="pur_order" id="pur_order" class="selectpicker" onchange="pur_order_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($pur_orders as $ct) { ?>
											<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if (isset($pur_invoice) && $pur_invoice->pur_order == $ct['id']) {
												echo 'selected';
											} ?>><?php echo html_entity_decode($ct['pur_order_number'] . ' - ' . get_vendor_company_name($ct['vendor']) . ' - ' . $ct['pur_order_name']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="col-md-6 form-group pad_right_0">
									<label for="wo_order"><?php echo _l('wo_order'); ?></label>
									<select name="wo_order" id="wo_order" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($wo_orders as $ct) { ?>
											<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if (isset($pur_invoice) && $pur_invoice->wo_order == $ct['id']) {
												echo 'selected';
											} ?>><?php echo html_entity_decode($ct['wo_order_number'] . ' - ' . get_vendor_company_name($ct['vendor']) . ' - ' . $ct['wo_order_name']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="col-md-6 pad_left_0">
									<label for="invoice_date"><span class="text-danger">* </span><?php echo _l('invoice_date'); ?></label>
									<?php $invoice_date = (isset($pur_invoice) ? _d($pur_invoice->invoice_date) : _d(date('Y-m-d')));
									echo render_date_input('invoice_date', '', $invoice_date, array('required' => 'true')); ?>
								</div>

								<div class="col-md-6 pad_right_0">
									<label for="invoice_date"><?php echo _l('pur_due_date'); ?></label>
									<?php $duedate = (isset($pur_invoice) ? _d($pur_invoice->duedate) : '');
									echo render_date_input('duedate', '', $duedate); ?>
								</div>
								<div class="col-md-12 pad_left_0 pad_right_0" style="margin-bottom: 14px;">
									<label for="project"><span class="text-danger">* </span><?php echo _l('project'); ?></label>
									<select name="project_id" id="project" class="selectpicker" data-live-search="true" data-width="100%" required="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($projects as $s) { ?>
											<option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_invoice) && $s['id'] == $pur_invoice->project_id) {
												echo 'selected';
											} else if (!isset($pur_invoice) && $s['id'] == $project_id) {
												echo 'selected';
											} ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="col-md-6 pad_left_0" style="clear: both;">
									<div class="form-group">
										<label for="vendor submitted amount" class="control-label"> <?php echo _l('amount_without_tax'); ?> ( ₹ )</label>
										<input type="number" class="form-control" id="vendor_submitted_amount_without_tax" name="vendor_submitted_amount_without_tax" value="<?= (isset($pur_invoice) ? $pur_invoice->vendor_submitted_amount_without_tax : '') ?>">
									</div>
								</div>
								<div class="col-md-6 pad_right_0">
									<div class="form-group">
										<label for="vendor submitted amount" class="control-label"> <?php echo _l('vendor_submitted_tax_amount'); ?> ( ₹ )</label>
										<input type="number" class="form-control" id="vendor_submitted_tax_amount" name="vendor_submitted_tax_amount" value="<?= (isset($pur_invoice) ? $pur_invoice->vendor_submitted_tax_amount : '') ?>">
									</div>
								</div>
								<div class="col-md-6 pad_left_0" style="clear: both;">
									<label for="bill accept date"><?php echo _l('bill_accept_date'); ?></label>
									<?php $bill_accept_date = (isset($pur_invoice) ? _d($pur_invoice->bill_accept_date) : '');
									echo render_date_input('bill_accept_date', '', $bill_accept_date, array()); ?>
								</div>
								<div class="col-md-6 pad_right_0 ">
									<label for="certified bill date"><?php echo _l('certified_bill_date'); ?></label>
									<?php $certified_bill_date = (isset($pur_invoice) ? _d($pur_invoice->certified_bill_date) : '');
									echo render_date_input('certified_bill_date', '', $certified_bill_date, array()); ?>
								</div>
								<div class="col-md-12 pad_left_0 pad_right_0">
									<!-- <label for="bank transcation detail"><?php echo _l('bank_transcation_detail'); ?></label> -->
									<?php $bank_transcation_detail = (isset($pur_invoice) ? $pur_invoice->bank_transcation_detail : '');
									echo render_textarea('bank_transcation_detail', 'bank_transcation_detail', $bank_transcation_detail); ?>
								</div>

							</div>

							<div class="col-md-6">
								<div class="col-md-6 pad_left_0">
									<?php
									$currency_attr = array('data-show-subtext' => true, 'required' => true);

									$selected = '';
									foreach ($currencies as $currency) {
										if (isset($pur_invoice) && $pur_invoice->currency != 0) {
											if ($currency['id'] == $pur_invoice->currency) {
												$selected = $currency['id'];
											}
										} else {
											if ($currency['isdefault'] == 1) {
												$selected = $currency['id'];
											}
										}
									}

									?>
									<label for="currency"><span class="text-danger">* </span><?php echo _l('invoice_add_edit_currency') ?></label>
									<?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), '', $selected, $currency_attr); ?>
								</div>
								<div class="col-md-6 form-group">
									<div id="inputTagsWrapper">
										<label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
										<input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($pur_invoice) ? prep_tags_input(get_tags_in($pur_invoice->id, 'pur_invoice')) : ''); ?>" data-role="tagsinput">
									</div>
								</div>

								<div class="col-md-12 form-group pad_left_0 pad_right_0">
									<div class="attachments">
										<div class="attachment">
											<div class="mbot15">
												<div class="form-group">
													<label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
													<div class="input-group">
														<input type="file" extension="<?php echo str_replace('.', '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
														<span class="input-group-btn">
															<button class="btn btn-success add_more_attachments p8-half" data-max="10" type="button"><i class="fa fa-plus"></i></button>
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<?php /*
								<div class="col-md-6 pad_left_0">
									<?php $transactionid = (isset($pur_invoice) ? $pur_invoice->transactionid : '');
									echo render_input('transactionid', 'transaction_id', $transactionid); ?>
								</div>
								<div class="col-md-6 pad_right_0">
									<?php $transaction_date = (isset($pur_invoice) ? $pur_invoice->transaction_date : '');
									echo render_date_input('transaction_date', 'transaction_date', $transaction_date); ?>
								</div>


								<div class="col-md-12  pad_left_0 pad_right_0">
									<div class="form-group select-placeholder">
										<label for="discount_type"
										class="control-label"><?php echo _l('discount_type'); ?></label>
										<select name="discount_type" class="selectpicker" data-width="100%"
										data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

										<option value="before_tax" <?php
										if (isset($pur_invoice)) {
											if ($pur_invoice->discount_type == 'before_tax') {
												echo 'selected';
											}
										} ?>><?php echo _l('discount_type_before_tax'); ?></option>
										<option value="after_tax" <?php if (isset($pur_invoice)) {
											if ($pur_invoice->discount_type == 'after_tax' || $pur_invoice->discount_type == null) {
												echo 'selected';
											}
										} else {
											echo 'selected';
										} ?>><?php echo _l('discount_type_after_tax'); ?></option>
									</select>
								</div>
							</div>
							*/ ?>


								<div class="col-md-12 form-group pad_left_0 pad_right_0">
									<label for="get_from_order_tracker"><?php echo _l('get_from_order_tracker'); ?></label>
									<?php $order_tracker_list = get_order_tracker_list(); ?>
									<select name="order_tracker_id" id="order_tracker_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach ($order_tracker_list as $s) { ?>
											<option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($pur_invoice) && $pur_invoice->order_tracker_id == $s['id']) {
												echo 'selected';
											} ?>> <?php echo pur_html_entity_decode($s['vendor']); ?> - <?php echo pur_html_entity_decode($s['order_name']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="col-md-6 pad_left_0 hide" style="margin-top: 0%;">
									<div class="form-group">
										<label for="vendor submitted amount" class="control-label"> <?php echo _l('vendor_submitted_amount'); ?> ( ₹ )</label>
										<input type="number" class="form-control" id="vendor_submitted_amount" name="vendor_submitted_amount" readonly value="<?= (isset($pur_invoice) ? $pur_invoice->vendor_submitted_amount : '') ?>">
									</div>
								</div>

								<div class="col-md-6 pad_left_0">
									<div class="form-group">
										<label for="payment_date_reliance"><?php echo _l('payment_date_reliance'); ?></label>
										<?php $payment_date = (isset($pur_invoice) ? _d($pur_invoice->payment_date) : '');
										echo render_date_input('payment_date', '', $payment_date, array()); ?>
									</div>
								</div>
								<div class="col-md-6 pad_left_0">
									<div class="form-group">
										<label for="payment_date_basilius"><?php echo _l('payment_date_basilius'); ?></label>
										<?php $payment_date_basilius = (isset($pur_invoice) ? _d($pur_invoice->payment_date_basilius) : '');
										echo render_date_input('payment_date_basilius', '', $payment_date_basilius, array()); ?>
									</div>
								</div>
								<div class="col-md-6 pad_left_0" style="margin-top: 0%;">
									<div class="form-group">
										<label for="final certified amount" class="control-label"> <?php echo _l('final_certified_amount'); ?> ( ₹ )</label>
										<input type="number" class="form-control" id="final_certified_amount" name="final_certified_amount" readonly value="<?= (isset($pur_invoice) ? $pur_invoice->final_certified_amount : '') ?>">
									</div>
								</div>

								<div class="col-md-12 pad_left_0">
									<?php $description_services = (isset($pur_invoice) ? $pur_invoice->description_services : '');
									echo render_textarea('description_services', 'description_of_services', $description_services, ['rows' => 2, 'required' => true]); ?>
								</div>
								<div class="col-md-12 pad_left_0 pad_right_0">
									<?php $adminnote = (isset($pur_invoice) ? $pur_invoice->adminnote : '');
									echo render_textarea('adminnote', 'adminnote', $adminnote, array('rows' => 7)) ?>
								</div>

							</div>

						</div>

						<?php $rel_id = (isset($pur_invoice) ? $pur_invoice->id : false); ?>
						<?php echo render_custom_fields('pur_invoice', $rel_id); ?>

					</div>



					<div class="panel-body mtop10 invoice-item">

						<!-- <div class="row">
		          <div class="col-md-4">
		            <?php $this->load->view('purchase/item_include/main_item_select'); ?>
		          </div>

				          <?php
							$base_currency = get_base_currency();

							$po_currency = $base_currency;
							if (isset($pur_invoice) && $pur_invoice->currency != 0) {
								$po_currency = pur_get_currency_by_id($pur_invoice->currency);
							}

							$from_currency = (isset($pur_invoice) && $pur_invoice->from_currency != null) ? $pur_invoice->from_currency : $base_currency->id;
							echo form_hidden('from_currency', $from_currency);

							?>
		          <div class="col-md-8 <?php if ($po_currency->id == $base_currency->id) {
											echo 'hide';
										} ?>" id="currency_rate_div">
		            <div class="col-md-10 text-right">
		              
		              <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' (' . $base_currency->name . ' => ' . $po_currency->name . '): ';  ?></span></p>
		            </div>
		            <div class="col-md-2 pull-right">
		              <?php $currency_rate = 1;
						if (isset($pur_invoice) && $pur_invoice->currency != 0) {
							$currency_rate = pur_get_currency_rate($base_currency->name, $po_currency->name);
						}
						echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right');
						?>
		            </div>
		          </div>
		      </div>  -->
						<!-- <div class="row">
		          <div class="col-md-12">
		            <div class="table-responsive s_table ">
		              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
		                <thead>
		                  <tr>
		                    <th></th>
		                    <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
		                    <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
		                    <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
		                    <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
		                    <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
		                    <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
		                    <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
		                    <th width="7%" align="right"><?php echo _l('discount') . '(%)'; ?></th>
		                    <th width="10%" align="right"><?php echo _l('discount(money)'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
		                    <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
		                    <th align="center"><i class="fa fa-cog"></i></th>
		                  </tr>
		                </thead>
		                <tbody>
		                  <?php echo $pur_invoice_row_template; ?>
		                </tbody>
		              </table>
		            </div>
		          </div>
		         <div class="col-md-8 col-md-offset-4">
		          <table class="table text-right">
		            <tbody>
		              <tr id="subtotal">
		                <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
		                  <?php echo form_hidden('total_mn', ''); ?>
		                </td>
		                <td class="wh-subtotal">
		                </td>
		              </tr>
		              
		              <tr id="order_discount_percent">
		                <td>
		                  <div class="row">
		                    <div class="col-md-7">
		                      <span class="bold"><?php echo _l('pur_discount'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_percent_note'); ?>" ></i></span>
		                    </div>
		                    <div class="col-md-3">
		                      <?php $discount_total = isset($pur_invoice) ? $pur_invoice->discount_total : '';
								echo render_input('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
		                    </div>
		                     <div class="col-md-2">
		                        <select name="add_discount_type" id="add_discount_type" class="selectpicker" onchange="pur_calculate_total(); return false;" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
		                            <option value="percent">%</option>
		                            <option value="amount" selected><?php echo _l('amount'); ?></option>
		                        </select>
		                     </div>
		                  </div>
		                </td>
		                <td class="order_discount_value">

		                </td>
		              </tr>

		              <tr id="total_discount">
		                <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
		                  <?php echo form_hidden('dc_total', ''); ?>
		                </td>
		                <td class="wh-total_discount">
		                </td>
		              </tr>

		              <tr>
		                <td>
		                 <div class="row">
		                  <div class="col-md-9">
		                   <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
		                 </div>
		                 <div class="col-md-3">
		                  <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if (isset($pur_invoice)) {
																													echo $pur_invoice->shipping_fee;
																												} else {
																													echo '0';
																												} ?>" class="form-control pull-left text-right" name="shipping_fee">
		                </div>
		              </div>
		              </td>
		              <td class="shiping_fee">
		              </td>
		              </tr>
		              
		              <tr id="totalmoney">
		                <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
		                  <?php echo form_hidden('grand_total', ''); ?>
		                </td>
		                <td class="wh-total">
		                </td>
		              </tr>
		            </tbody>
		          </table>
		      </div> -->
						<div id="removed-items"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 mtop15">
						<div class="panel-body bottom-transaction">


							<div class="col-md-12 pad_left_0 pad_right_0">
								<?php $vendor_note = (isset($pur_invoice) ? $pur_invoice->vendor_note : '');
								echo render_textarea('vendor_note', 'vendor_note', $vendor_note, array('rows' => 7)) ?>
							</div>
							<div class="col-md-12 pad_left_0 pad_right_0">
								<?php $terms = (isset($pur_invoice) ? $pur_invoice->terms : '');
								echo render_textarea('terms', 'terms', $terms, array('rows' => 7)) ?>
							</div>

							<div class="btn-bottom-toolbar text-right">

								<button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit">
									<?php echo _l('submit'); ?>
								</button>
							</div>

						</div>
						<div class="btn-bottom-pusher"></div>
					</div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<?php init_tail(); ?>
</body>

</html>
<?php require 'modules/purchase/assets/js/pur_invoice_js.php'; ?>
<script>
	$(document).ready(function() {
		var expense_id = <?php echo isset($pur_invoice) && $pur_invoice->expense_id ? $pur_invoice->expense_id : 'null'; ?>;
		// Function to calculate sum and update input
		function calculateSum() {
			// Get the values from the input fields
			var value1 = parseFloat($('#vendor_submitted_amount_without_tax').val()) || 0;
			var value2 = parseFloat($('#vendor_submitted_tax_amount').val()) || 0;

			// Calculate the sum
			var sum = value1 + value2;
			sum = sum.toFixed(2);

			// Update the sum in another input field
			$('#vendor_submitted_amount').val(sum);
			$('#final_certified_amount').val(sum);
		}

		// Attach the function to the keypress event of the second input
		$('#vendor_submitted_amount_without_tax,#vendor_submitted_tax_amount').on('input', calculateSum);

		$("body").on('change', 'select[name="pur_order"]', function() {
			"use strict";
			var pur_order = $(this).val();
			$('select[name="wo_order"]').val('').selectpicker('refresh');
			$('select[name="order_tracker_id"]').val('').selectpicker('refresh');
			if (pur_order != '') {
				$.post(admin_url + 'purchase/get_pur_order/' + pur_order).done(function(response) {
					response = JSON.parse(response);
					var vendor_submitted_amount_without_tax = (parseFloat(response.total) - parseFloat(response.total_tax)).toFixed(2);
					$('select[name="wo_order"]').prop('disabled', true).selectpicker('refresh');
					$('select[name="order_tracker_id"]').prop('disabled', true).selectpicker('refresh');
					$('select[name="vendor"]').val(response.vendor).trigger('change');
					$('select[name="group_pur"]').val(response.group_pur).trigger('change');
					$('select[name="project_id"]').val(response.project).trigger('change');
					if(empty(expense_id)) {
						$('input[name="vendor_submitted_amount_without_tax"]').val(vendor_submitted_amount_without_tax).trigger('change');
						$('input[name="vendor_submitted_tax_amount"]').val(response.total_tax).trigger('change');
					}
					$('textarea[name="description_services"]').val(response.pur_order_name);
					calculateSum();
					init_selectpicker();
				});
			} else {
				$('select[name="wo_order"]').prop('disabled', false).selectpicker('refresh');
				$('select[name="order_tracker_id"]').prop('disabled', false).selectpicker('refresh');
				// $('select[name="vendor"]').val('').trigger('change');
				// $('select[name="group_pur"]').val('').trigger('change');
				// $('select[name="project_id"]').val('').trigger('change');
				// if(empty(expense_id)) {
				// 	$('input[name="vendor_submitted_amount_without_tax"]').val('').trigger('change');
				// 	$('input[name="vendor_submitted_tax_amount"]').val('').trigger('change');
				// }
				// $('textarea[name="description_services"]').val('');
				// calculateSum();
				init_selectpicker();
			}
		});

		$("body").on('change', 'select[name="wo_order"]', function() {
			"use strict";
			var wo_order = $(this).val();
			$('select[name="pur_order"]').val('').selectpicker('refresh');
			$('select[name="order_tracker_id"]').val('').selectpicker('refresh');
			if (wo_order != '') {
				$.post(admin_url + 'purchase/get_wo_order/' + wo_order).done(function(response) {
					response = JSON.parse(response);
					var vendor_submitted_amount_without_tax = (parseFloat(response.total) - parseFloat(response.total_tax)).toFixed(2);
					$('select[name="pur_order"]').prop('disabled', true).selectpicker('refresh');
					$('select[name="order_tracker_id"]').prop('disabled', true).selectpicker('refresh');
					$('select[name="vendor"]').val(response.vendor).trigger('change');
					$('select[name="group_pur"]').val(response.group_pur).trigger('change');
					$('select[name="project_id"]').val(response.project).trigger('change');
					if(empty(expense_id)) {
						$('input[name="vendor_submitted_amount_without_tax"]').val(vendor_submitted_amount_without_tax).trigger('change');
						$('input[name="vendor_submitted_tax_amount"]').val(response.total_tax).trigger('change');
					}
					$('textarea[name="description_services"]').val(response.wo_order_name);
					calculateSum();
					init_selectpicker();
				});
			} else {
				$('select[name="pur_order"]').prop('disabled', false).selectpicker('refresh');
				$('select[name="order_tracker_id"]').prop('disabled', false).selectpicker('refresh');
				// $('select[name="vendor"]').val('').trigger('change');
				// $('select[name="group_pur"]').val('').trigger('change');
				// $('select[name="project_id"]').val('').trigger('change');
				// if(empty(expense_id)) {
				// 	$('input[name="vendor_submitted_amount_without_tax"]').val('').trigger('change');
				// 	$('input[name="vendor_submitted_tax_amount"]').val('').trigger('change');
				// }
				// $('textarea[name="description_services"]').val('');
				// calculateSum();
				init_selectpicker();
			}
		});

		$("body").on('change', 'select[name="order_tracker_id"]', function() {
			"use strict";
			var order_tracker = $(this).val();
			$('select[name="pur_order"]').val('').selectpicker('refresh');
			$('select[name="wo_order"]').val('').selectpicker('refresh');
			if (order_tracker != '') {
				$.post(admin_url + 'purchase/order_tracker_id/' + order_tracker).done(function(response) {
					response = JSON.parse(response);
					$('select[name="pur_order"]').prop('disabled', true).selectpicker('refresh');
					$('select[name="wo_order"]').prop('disabled', true).selectpicker('refresh');
					$('select[name="vendor"]').val(response.vendor).trigger('change');
					$('select[name="group_pur"]').val(response.group_pur).trigger('change');
					if(empty(expense_id)) {
						$('input[name="vendor_submitted_amount_without_tax"]').val(parseFloat(response.total)).trigger('change');
					}
					calculateSum();
					init_selectpicker();

				});
			} else {
				$('select[name="pur_order"]').prop('disabled', false).selectpicker('refresh');
				$('select[name="wo_order"]').prop('disabled', false).selectpicker('refresh');
				// $('select[name="vendor"]').val('').trigger('change');
				// $('select[name="group_pur"]').val('').trigger('change');
				// $('select[name="project_id"]').val('').trigger('change');
				// if(empty(expense_id)) {
				// 	$('input[name="vendor_submitted_amount_without_tax"]').val('').trigger('change');
				// 	$('input[name="vendor_submitted_tax_amount"]').val('').trigger('change');
				// }
				// $('textarea[name="description_services"]').val('');
				// calculateSum();
				init_selectpicker();
			}
		});

		var pur_order_value = $('select[name="pur_order"]').val();
		var wo_order_value = $('select[name="wo_order"]').val();
		var order_tracker_id_value = $('select[name="order_tracker_id"]').val();
		if(!empty(pur_order_value)) {
			$('select[name="wo_order"]').prop('disabled', true).selectpicker('refresh');
			$('select[name="order_tracker_id"]').prop('disabled', true).selectpicker('refresh');
		}
		if(!empty(wo_order_value)) {
			$('select[name="pur_order"]').prop('disabled', true).selectpicker('refresh');
			$('select[name="order_tracker_id"]').prop('disabled', true).selectpicker('refresh');
		}
		if(!empty(order_tracker_id_value)) {
			$('select[name="pur_order"]').prop('disabled', true).selectpicker('refresh');
			$('select[name="wo_order"]').prop('disabled', true).selectpicker('refresh');
		}
	});
</script>