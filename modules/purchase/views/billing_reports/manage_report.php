<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6 border-right">
								<h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('report_by_table'); ?></h4>
								<hr />
								<p><a href="#" class="font-medium" onclick="init_report(this,'summary_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Vendor Billing Summary Report'); ?></a></p>
								<hr class="hr-10" />
								<p><a href="#" class="font-medium" onclick="init_report(this,'aging_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Billing Status Aging Report'); ?></a></p>
								<hr class="hr-10" />
								<p><a href="#" class="font-medium" onclick="init_report(this,'mapping_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Vendor Billing vs Client Invoice Mapping Report'); ?></a></p>
								<hr class="hr-10" />
								<p><a href="#" class="font-medium" onclick="init_report(this,'invoicing_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Project-wise Invoicing Report'); ?></a></p>
								<hr class="hr-10" />
								<p><a href="#" class="font-medium" onclick="init_report(this,'client_aging_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Client Invoice Aging Report'); ?></a></p>
							</div>
							<div class="col-md-6">
								<?php if (isset($currencies)) { ?>
									<div id="currency" class="form-group hide">
										<label for="currency"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('report_sales_base_currency_select_explanation'); ?>"></i> <?php echo _l('currency'); ?></label><br />
										<select class="selectpicker" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
											<?php foreach ($currencies as $currency) {
												$selected = '';
												if ($currency['isdefault'] == 1) {
													$selected = 'selected';
												}
											?>
												<option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>

								<div class="form-group" id="report-time">
									<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
									<select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
										<option value="this_month"><?php echo _l('this_month'); ?></option>
										<option value="1"><?php echo _l('last_month'); ?></option>
										<option value="this_year"><?php echo _l('this_year'); ?></option>
										<option value="last_year"><?php echo _l('last_year'); ?></option>
										<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
										<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
										<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
										<option value="custom"><?php echo _l('period_datepicker'); ?></option>
									</select>
								</div>
								<div id="date-range" class="hide mbot15">
									<div class="row">
										<div class="col-md-6">
											<label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
											<div class="input-group date">
												<input type="text" class="form-control datepicker" id="report-from" name="report-from">
												<div class="input-group-addon">
													<i class="fa fa-calendar calendar-icon"></i>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
											<div class="input-group date">
												<input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
												<div class="input-group-addon">
													<i class="fa fa-calendar calendar-icon"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php $current_year = date('Y');
								$y0 = (int)$current_year;
								$y1 = (int)$current_year - 1;
								$y2 = (int)$current_year - 2;
								$y3 = (int)$current_year - 3;
								?>
								<div class="form-group hide" id="year_requisition">
									<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
									<select name="year_requisition" id="year_requisition" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('filter_by') . ' ' . _l('year'); ?>">
										<option value="<?php echo pur_html_entity_decode($y0); ?>" <?php echo 'selected' ?>><?php echo _l('year') . ' ' . pur_html_entity_decode($y0); ?></option>
										<option value="<?php echo pur_html_entity_decode($y1); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y1); ?></option>
										<option value="<?php echo pur_html_entity_decode($y2); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y2); ?></option>
										<option value="<?php echo pur_html_entity_decode($y3); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y3); ?></option>

									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" id="container1"></div>
							<div class="col-md-12" id="container2"></div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-6" id="container4"></div>
							<div class="col-md-6" id="container3"></div>
						</div>
						<div id="report" class="hide">
							<div class="col-md-12">
								<?php $this->load->view('summary_report'); ?>
							</div>
							<div class="col-md-12">
								<?php $this->load->view('aging_report'); ?>
							</div>
							<div class="col-md-12">
								<?php $this->load->view('mapping_report'); ?>
							</div>
							<div class="col-md-12">
								<?php $this->load->view('invoicing_report'); ?>
							</div>
							<div class="col-md-12">
								<?php $this->load->view('client_aging_report'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php init_tail(); ?>
</body>

</html>
<?php require 'modules/purchase/assets/js/billing_report_js.php'; ?>