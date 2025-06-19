<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr>
							</div>
						</div>
						<div class="row row-margin">

							<div class="col-md-9">
								<div class="panel panel-info col-md-12 panel-padding">

									<div class="panel-body">
										<div class="col-md-6">
											<?php $company_name = get_option('invoice_company_name');
											$address = get_option('invoice_company_address');
											$day = date('d', strtotime($goods_receipt->date_add));
											$month = date('m', strtotime($goods_receipt->date_add));
											$year = date('Y', strtotime($goods_receipt->date_add)); ?>

										</div>

										<div class="col-md-4">

										</div>
										<div class="col-md-12">
											<p>
											<h2 class="bold text-center"><?php echo mb_strtoupper(_l('store_input_slip')); ?></h2>
											</p>
										</div>
										<div class="col-md-4">
										</div>

										<div class="col-md-3">
										</div>

										<div class="col-md-12 pull-right">
											<br>
											<div class="row">
												<!-- <div class="col-md-3 pull-right">
													<p><span class="bold"><?php echo _l('debit'); ?>: </span>.....................</p>
													<p><span class="bold"><?php echo _l('credit'); ?>: </span>.....................</p>
												</div> -->
												<div class="col-md-4 pull-right">
													<p><span class="span-font-style"><?php echo _l('days') . ' ' . $day . ' ' . _l('month') . ' ' . $month . ' ' . _l('year') . ' ' . $year; ?></p>
													<p><span class="bold"><?php echo _l('stock_received_docket_number'); ?>: </span><?php echo html_entity_decode($goods_receipt->goods_receipt_code) ?></p>
												</div>
											</div>
										</div>

										<table class="table">
											<tbody>

												<tr class="project-overview">
													<td class="bold" width="30%"><?php echo _l('supplier_name'); ?></td>
													<?php
													if (get_status_modules_wh('purchase') && ($goods_receipt->supplier_code != '') && ($goods_receipt->supplier_code != 0)) { ?>
														<td><?php echo html_entity_decode(wh_get_vendor_company_name($goods_receipt->supplier_code)); ?></td>
													<?php   } else { ?>
														<td><?php echo html_entity_decode($goods_receipt->supplier_name); ?></td>
													<?php   }


													?>

												</tr>
												<tr class="project-overview">
													<td class="bold" width="30%"><?php echo _l('deliver_name'); ?></td>
													<td><?php echo html_entity_decode($goods_receipt->deliver_name); ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('Buyer'); ?></td>
													<td><?php echo get_staff_full_name($goods_receipt->buyer_id); ?></td>
												</tr>

												<?php
								                  if (get_status_modules_wh('purchase')) { ?>
								                    <tr class="project-overview">
								                      <td class="bold"><?php echo _l('reference_order'); ?></td>
								                      <td>
								                        <?php 
								                        if(!empty($goods_receipt->pr_order_id)) { ?>
								                          <a href="<?php echo admin_url('purchase/purchase_order/' . $goods_receipt->pr_order_id) ?>"><?php echo get_pur_order_name($goods_receipt->pr_order_id) ?></a>
								                        <?php } if(!empty($goods_receipt->wo_order_id)) { ?>
								                          <a href="<?php echo admin_url('purchase/work_order/' . $goods_receipt->wo_order_id) ?>"><?php echo get_wo_order_name($goods_receipt->wo_order_id) ?></a>
								                        <?php } ?>

								                      </td>
								                    </tr>
								                <?php } ?>

												<tr class="project-overview">
													<td class="bold"><?php echo _l('note_'); ?></td>
													<td><?php echo html_entity_decode($goods_receipt->description); ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('category'); ?></td>
													<td><?php echo html_entity_decode($goods_receipt->kind); ?></td>
												</tr>
												<tr>

													<td class="bold"><?php echo _l('print'); ?></td>
													<td>
														<div class="btn-group">
															<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if (is_mobile()) {
																																																					echo ' PDF';
																																																				} ?> <span class="caret"></span></a>
															<ul class="dropdown-menu dropdown-menu-right">
																<li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
																<li class="hidden-xs"><a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id . '?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
																<li><a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id); ?>"><?php echo _l('download'); ?></a></li>
																<li>
																	<a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $goods_receipt->id . '?print=true'); ?>" target="_blank">
																		<?php echo _l('print'); ?>
																	</a>
																</li>
															</ul>
														</div>

													</td>
												</tr>





											</tbody>
										</table>
										<div class="table-responsive">
											<table class="table table-bordered">
												<tbody>
													<tr>

														<th align="center">#</th>
														<th colspan="1"><?php echo _l('commodity_code') ?></th>
														<th colspan="1"><?php echo _l('description') ?></th>
														<th colspan="1"><?php echo _l('warehouse_name') ?></th>
														<th colspan="1"><?php echo _l('unit_name') ?></th>
														<th colspan="1" class="text-center"><?php echo _l('po_quantity') ?></th>
														<th colspan="1" class="text-center"><?php echo _l('received_quantity') ?></th>
														<!-- <th align="right" colspan="1"><?php echo _l('unit_price') ?></th>
														<th align="right" colspan="1"><?php echo _l('total_money') ?></th>
														<th align="right" colspan="1"><?php echo _l('tax_money') ?></th> -->
														<th align="right" colspan="1"><?php echo _l('lot_number') ?></th>
														<!-- <th colspan="1"><?php echo _l('vendor') ?></th> -->
														<!-- <th align="right" colspan="1"><?php echo _l('production_status') ?></th>
														<th align="right" colspan="1"><?php echo _l('payment_date') ?></th>
														<th align="right" colspan="1"><?php echo _l('est_delivery_date') ?></th>
														<th align="right" colspan="1"><?php echo _l('delivery_date') ?></th> -->
														<!-- <th align="right" colspan="1"><?php echo _l('expiry_date') ?></th> -->

													</tr>


													<?php foreach (json_decode($goods_receipt_detail) as $receipt_key => $receipt_value) {
														$receipt_key++;
														$po_quantities = (isset($receipt_value) ? $receipt_value->po_quantities : '');
														$quantities = (isset($receipt_value) ? $receipt_value->quantities : '');
														$unit_price = (isset($receipt_value) ? $receipt_value->unit_price : '');
														$unit_price = (isset($receipt_value) ? $receipt_value->unit_price : '');
														$goods_money = (isset($receipt_value) ? $receipt_value->goods_money : '');

														$commodity_code = get_commodity_name($receipt_value->commodity_code) != null ? get_commodity_name($receipt_value->commodity_code)->commodity_code : '';

														$commodity_name = get_commodity_name($receipt_value->commodity_code) != null ? get_commodity_name($receipt_value->commodity_code)->description : '';
														$unit_name = '';
														if (is_numeric($receipt_value->unit_id) && (float)$receipt_value->unit_id > 0) {
															$unit_name = get_unit_type($receipt_value->unit_id) != null ? get_unit_type($receipt_value->unit_id)->unit_name : '';
														}

														$warehouse_code = get_warehouse_name($receipt_value->warehouse_id) != null ? get_warehouse_name($receipt_value->warehouse_id)->warehouse_name : '';
														$tax_money = (isset($receipt_value) ? $receipt_value->tax_money : '');
														$expiry_date = (isset($receipt_value) ? $receipt_value->expiry_date : '');
														$lot_number = (isset($receipt_value) ? $receipt_value->lot_number : '');
														$vendor_name = !empty($receipt_value->vendor_id) ? wh_get_vendor_company_name($receipt_value->vendor_id) : '';
														$delivery_date = !empty($receipt_value->delivery_date) ? $receipt_value->delivery_date : null;
														$payment_date = !empty($receipt_value->payment_date) ? $receipt_value->payment_date : null;
														$est_delivery_date = !empty($receipt_value->est_delivery_date) ? $receipt_value->est_delivery_date : null;
														$production_status = '';
														if ($receipt_value->production_status > 0) {
															if ($receipt_value->production_status == 1) {
																$production_status = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">' . _l('not_started') . '</span><span class="hide">, </span></span>&nbsp';
															} elseif ($receipt_value->production_status == 2) {
																$production_status = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">' .  _l('on_going') . '</span><span class="hide">, </span></span>&nbsp';
															} elseif ($receipt_value->production_status == 3) {
																$production_status = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">' . _l('approved') . '</span><span class="hide">, </span></span>&nbsp';
															} else {
																$production_status = '';
															}
														}

													?>
														<tr>
															<td><?php echo html_entity_decode($receipt_key) ?></td>
															<td><?php echo html_entity_decode(wh_get_item_variatiom($receipt_value->commodity_code)) ?></td>
															<td><?php echo html_entity_decode($receipt_value->description) ?></td>
															<td><?php echo html_entity_decode($warehouse_code) ?></td>
															<td><?php echo html_entity_decode($unit_name) ?></td>
															<td class="text-right"><?php echo html_entity_decode($po_quantities) ?></td>
															<td class="text-right"><?php echo html_entity_decode($quantities) ?></td>
															<!-- <td class="text-right"><?php echo app_format_money((float)$unit_price, '') ?></td>
															<td class="text-right"><?php echo app_format_money((float)$goods_money, '') ?></td>
															<td class="text-right"><?php echo app_format_money((float)$tax_money, '') ?></td> -->
															<td class="text-right"><?php echo html_entity_decode($lot_number) ?></td>
															<!-- <td><?php echo $vendor_name ?></td> -->
																<!-- <td class="text-right"><?php echo $production_status ?></td>
																<td class="text-right">
																	<?php echo $payment_date ? date('d M, Y', strtotime($payment_date)) : '-'; ?>
																</td>
																<td class="text-right">
																	<?php echo $est_delivery_date ? date('d M, Y', strtotime($est_delivery_date)) : '-'; ?>
																</td>
																<td class="text-right">
																	<?php echo $delivery_date ? date('d M, Y', strtotime($delivery_date)) : '-'; ?>
																</td> -->
															<!-- <td class="text-right"><?php echo _d($expiry_date) ?></td> -->
														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
										<!-- <div class="row pull-right mbot10">
												<div class="col-md-12 ">
													<table class="table">
														<tbody>
															<tr>
																<td class="bold" width="30%"><?php echo _l('total_goods_money')  ?> :</td>
																<td><?php echo  app_format_money((float) $goods_receipt->total_goods_money, $base_currency)  ?></td>
															</tr>
															<tr>
																<td class="bold"  width="30%"><?php echo  _l('value_of_inventory')  ?> :</td>
																<td><?php echo app_format_money((float) $goods_receipt->value_of_inventory, $base_currency) ?></td>
															</tr>

															<?php if (isset($goods_receipt) && $tax_data['html_currency'] != '') {
																echo html_entity_decode($tax_data['html_currency']);
															} ?>

															<tr>
																<td class="bold " ><?php echo  _l('total_tax_money')  ?> :</td>
																<td><?php echo app_format_money((float) $goods_receipt->total_tax_money, $base_currency) ?></td>
															</tr>
															<tr>
																<td class="bold " ><?php echo  _l('total_money')  ?>:</td>
																<td><?php echo  app_format_money((float) $goods_receipt->total_money, $base_currency)  ?></td>
															</tr>
															<tr></tr>
														</tbody>
													</table>
												</div>
											</div> -->
										<br>


										<div class="row">
											<div class="col-md-12 ">
												<div class="col-md-4 pull-right">
													<p><span class="span-font-style"><?php echo _l('days') . ' ......... ' . _l('month') . ' ......... ' . _l('year') . ' .......... '; ?>
												</div>
											</div>
										</div>
										<br>
										<div class="row">
											<div class="col-md-1">
											</div>
											<!-- <div class="col-md-4">
												<p><span class="bold"><?php echo _l('deliver_name') ?></p>
												<p><span class="span-font-style"><?php echo _l('sign_full_name') ?></p>
											</div> -->
											<div class="col-md-4">
												<p><span class="bold">Stock Keeper</p>
												<p><span class="span-font-style"><?php echo _l('sign_full_name') ?></p>

											</div>
											<div class="col-md-3">
												<p><span class="bold"><?php echo _l('chief_accountant') ?></p>
												<p><span class="span-font-style"><?php echo _l('sign_full_name') ?></p>

											</div>
										</div>

										<br>
										<br>
										<br>
										<br>


										<div class="project-overview-right">
											<?php if (count($list_approve_status) > 0) { ?>

												<div class="row">
													<div class="col-md-12 project-overview-expenses-finance">
														<div class="col-md-4 text-center">
														</div>
														<?php
														$this->load->model('staff_model');
														$enter_charge_code = 0;
														foreach ($list_approve_status as $value) {
															$value['staffid'] = explode(', ', $value['staffid']);
															if ($value['action'] == 'sign') {
														?>
																<div class="col-md-3 text-center">
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
																		echo html_entity_decode($staff_name);
																		?></p>
																	<?php if ($value['approve'] == 1) {
																	?>
																		<?php if (file_exists(WAREHOUSE_STOCK_IMPORT_MODULE_UPLOAD_FOLDER . $goods_receipt->id . '/signature_' . $value['id'] . '.png')) { ?>

																			<img src="<?php echo site_url('modules/warehouse/uploads/stock_import/' . $goods_receipt->id . '/signature_' . $value['id'] . '.png'); ?>" class="img-width-height">

																		<?php } else { ?>
																			<img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
																		<?php } ?>


																	<?php }
																	?>
																</div>
															<?php } else { ?>
																<div class="col-md-3 text-center">
																	<p class="text-uppercase text-muted no-mtop bold">
																		<?php
																		$staff_name = '';
																		foreach ($value['staffid'] as $key => $val) {
																			if ($staff_name != '') {
																				$staff_name .= ' or ';
																			}
																			$staff_name .= $this->staff_model->get($val)->firstname;
																		}
																		echo html_entity_decode($staff_name);
																		?></p>
																	<?php if ($value['approve'] == 1) {
																	?>
																		<img src="<?php echo site_url('modules/warehouse/uploads/approval/approved.png'); ?>" class="img-width-height">
																	<?php } elseif ($value['approve'] == -1) { ?>
																		<img src="<?php echo site_url('modules/warehouse/uploads/approval/rejected.png'); ?>" class="img-width-height">
																	<?php }
																	?>
																	<p class="text-muted no-mtop bold">
																		<?php echo html_entity_decode($value['note']) ?>
																	</p>
																</div>
														<?php }
														} ?>
													</div>

												</div>

											<?php } ?>
										</div>
										<div class="pull-right">

											<?php
											if ($goods_receipt->approval != 1 && ($check_approve_status == false)) { ?>
												<?php if ($check_appr && $check_appr != false) { ?>
													<a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo html_entity_decode($goods_receipt->id); ?>); return false;"><?php echo _l('send_request_approve'); ?></a>
												<?php } ?>

											<?php }
											if (isset($check_approve_status['staffid'])) {
											?>
												<?php
												if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)) { ?>
													<div class="btn-group">
														<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
														<ul class="dropdown-menu dropdown-menu-right menu-width-height">
															<li>
																<div class="col-md-12">
																	<?php echo render_textarea('reason', 'reason'); ?>
																</div>
															</li>
															<li>
																<div class="row text-right col-md-12">
																	<a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo html_entity_decode($goods_receipt->id); ?>); return false;" class="btn btn-success button-margin"><?php echo _l('approve'); ?></a>
																	<a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo html_entity_decode($goods_receipt->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
																</div>
															</li>
														</ul>
													</div>
												<?php }
												?>

												<?php
												if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign)) { ?>
													<button onclick="accept_action();" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
												<?php }
												?>
											<?php
											}
											?>
										</div>
									</div>
								</div>
								<?php $value = (isset($payslip->record) ? $payslip->record : []) ?>
								<?php $record = $value; ?>
								<?php $value = (isset($payslip->spending) ? $payslip->spending : []) ?>
								<?php $spending = $value; ?>


							</div>

							<div class="col-md-3">
								<div class="panel panel-info col-md-12 panel-padding">
									<div class="panel-heading "><?php echo _l('approval_information'); ?></div>
									<div class="panel-body">
										<h5 class="no-margin">
											<?php echo _l('approver_list'); ?>:
										</h5>
										<?php
										$stt = 1;
										foreach ($list_approve_status as $key => $value) {
											$value['staffid'] = explode(', ', $value['staffid']);
											$approve = '<span class="label label-tag tag-id-1 label-tab2" class="status-border-color">' . _l('not_yet_approve') . '</span>';
											if ($value['approve'] == 1) {
												$approve = '<span class="label label-tag tag-id-1 label-tab1" class="status-border-color1">' . _l('approved') . '</span>';
											} elseif ($value['approve'] == -1) {
												$approve = '<span class="label label-tag tag-id-1 label-tab3" class="status-border-color2">' . _l('reject') . '</span>';
											}
											$staff_name = '';
											foreach ($value['staffid'] as $key => $val) {
												if ($staff_name != '') {
													$staff_name .= ' or ';
												}
												$staff_name .= get_staff_full_name($val);
											}
											echo html_entity_decode($stt . ': ' . $staff_name . ' ' . $approve . '<br>');
											$stt++;
										}
										?>

										<hr class="hr-panel-heading" />
										<h5 class="no-margin">
											<?php echo _l('activity_log'); ?>
										</h5>
										<div class="activity-feed">
											<?php $enter_code = 0;
											foreach ($payslip_log as $log) {
												$approve = '';



											?>
												<div class="feed-item">
													<div class="row">
														<div class="col-md-12">
															<div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>"><?php echo time_ago($log['date']); ?></span></div>
															<div class="text">
																<?php

																$fullname = get_staff_full_name($log['staffid']);
																if ($log['staffid'] != 0) { ?>
																	<a href="<?php echo admin_url('profile/' . $log['staffid']); ?>"><?php echo staff_profile_image($log['staffid'], array('staff-profile-xs-image', 'pull-left mright10')); ?></a>
																<?php } ?>

																<p class="mtop10 no-mbot"><?php echo html_entity_decode($fullname) . ' - <b>' .
																								_l($log['note']) . '</b>'; ?></p>

															</div>
														</div>

														<div class="clearfix"></div>
														<div class="col-md-12">
															<hr class="hr-10" />
														</div>
													</div>
												</div>
											<?php

											} ?>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="panel-body ">
								<div class="btn-bottom-toolbar text-right">
									<a href="<?php echo admin_url('warehouse/manage_purchase'); ?>" class="btn btn-default text-right mright5 close_button"><?php echo _l('close'); ?></a>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>
	</div>
</div>
</div>

<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
				<div class="signature-pad--body">
					<canvas id="signature" height="130" width="550"></canvas>
				</div>
				<input type="text" class="sig-input-style" tabindex="-1" name="signature" id="signatureInput">
				<div class="dispay-block">
					<button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
				<button onclick="sign_request(<?php echo html_entity_decode($goods_receipt->id); ?>);" autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
			</div>
		</div>
	</div>
</div>

</div>
</div>
</div>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/edit_purchase_js.php'; ?>
</body>

</html>