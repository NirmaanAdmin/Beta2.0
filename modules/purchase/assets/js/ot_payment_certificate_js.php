<script>
	var pc_id = '<?php echo isset($payment_certificate) ? pur_html_entity_decode($payment_certificate->id) : NULL; ?>';
	var order_tracker_id = '<?php echo isset($payment_certificate) ? pur_html_entity_decode($payment_certificate->ot_id) : NULL; ?>';
	$(function() {});

	if(pc_id) {
		get_contract_comments();
	}
	get_ot_pc_format();

	function calculate_payment_certificate() {
		"use strict";
		var ot_id = $('select[name="ot_id"]').val();
		var payment_certificate_id = $('input[name="payment_certificate_id"]').val();

		if (ot_id != '') {
			$.post(admin_url + 'purchase/get_ot_contract_data/' + ot_id + '/' + payment_certificate_id).done(function(response) {
				response = JSON.parse(response);
				$('.ot_name').html(response.ot_name);

				var ot_contract_amount = response.ot_contract_amount;
				ot_contract_amount = ot_contract_amount != "" ? ot_contract_amount : 0;
				$('.ot_contract_amount').html(format_money_cert(ot_contract_amount, true));

				if (response.ot_previous) {
					$('input[name="ot_previous"]').val(format_amount_cert(response.ot_previous));
				}
				var ot_previous = $('input[name="ot_previous"]').val();
				ot_previous = ot_previous.trim() != "" ? ot_previous : 0;
				$('.total_ot_previous').html(format_money_cert(ot_previous, true));

				var ot_this_bill = $('input[name="ot_this_bill"]').val();
				ot_this_bill = ot_this_bill.trim() != "" ? ot_this_bill : 0;
				$('.total_ot_this_bill').html(format_money_cert(ot_this_bill, true));

				var ot_comulative = parseFloat(ot_previous) + parseFloat(ot_this_bill);
				$('.ot_comulative').html(format_money_cert(ot_comulative, true));

				var pay_cert_c1_1 = parseFloat($('input[name="pay_cert_c1_1"]').val()) || 0;
				// var mobilization_advance = $('select[name="mobilization_advance"]').val();
				// if (mobilization_advance) {
				// 	mobilization_advance = mobilization_advance.replace('%', '');
				// 	pay_cert_c1_1 = ot_contract_amount * (mobilization_advance / 100);
				// 	$('input[name="pay_cert_c1_1"]').val(format_amount_cert(pay_cert_c1_1));
				// } else {
				// 	pay_cert_c1_1 = 0;
				// }

				var pay_cert_c1_2 = parseFloat($('input[name="pay_cert_c1_2"]').val()) || 0;
				// var mobilization_advance = $('select[name="mobilization_advance"]').val();
				// if (mobilization_advance) {
				// 	mobilization_advance = mobilization_advance.replace('%', '');
				// 	pay_cert_c1_2 = ot_previous * (mobilization_advance / 100);
				// 	$('input[name="pay_cert_c1_2"]').val(format_amount_cert(pay_cert_c1_2));
				// } else {
				// 	pay_cert_c1_2 = 0;
				// }

				var pay_cert_c1_3 = parseFloat($('input[name="pay_cert_c1_3"]').val()) || 0;
				// var mobilization_advance = $('select[name="mobilization_advance"]').val();
				// if (mobilization_advance) {
				// 	mobilization_advance = mobilization_advance.replace('%', '');
				// 	pay_cert_c1_3 = ot_this_bill * (mobilization_advance / 100);
				// 	$('input[name="pay_cert_c1_3"]').val(format_amount_cert(pay_cert_c1_3));
				// } else {
				// 	pay_cert_c1_3 = 0;
				// }

				var pay_cert_c2_1 = $('input[name="pay_cert_c2_1"]').val();
				pay_cert_c2_1 = pay_cert_c2_1.trim() != "" ? pay_cert_c2_1 : 0;
				var net_advance_1 = parseFloat(pay_cert_c1_1) - parseFloat(pay_cert_c2_1);
				$('.net_advance_1').html(format_money_cert(net_advance_1, true));

				var pay_cert_c2_2 = $('input[name="pay_cert_c2_2"]').val();
				pay_cert_c2_2 = pay_cert_c2_2.trim() != "" ? pay_cert_c2_2 : 0;
				var net_advance_2 = parseFloat(pay_cert_c1_2) - parseFloat(pay_cert_c2_2);
				$('.net_advance_2').html(format_money_cert(net_advance_2, true));

				var pay_cert_c2_3 = $('input[name="pay_cert_c2_3"]').val();
				pay_cert_c2_3 = pay_cert_c2_3.trim() != "" ? pay_cert_c2_3 : 0;
				var net_advance_3 = parseFloat(pay_cert_c1_3) - parseFloat(pay_cert_c2_3);
				$('.net_advance_3').html(format_money_cert(net_advance_3, true));

				var pay_cert_c1_4 = parseFloat(pay_cert_c1_2) + parseFloat(pay_cert_c1_3);
				$('.pay_cert_c1_4').html(format_money_cert(pay_cert_c1_4, true));
				var pay_cert_c2_4 = parseFloat(pay_cert_c2_2) + parseFloat(pay_cert_c2_3);
				$('.pay_cert_c2_4').html(format_money_cert(pay_cert_c2_4, true));
				var net_advance_4 = parseFloat(pay_cert_c1_4) - parseFloat(pay_cert_c2_4);
				$('.net_advance_4').html(format_money_cert(net_advance_4, true));

				var sub_total_ac_1 = parseFloat(ot_contract_amount) + parseFloat(net_advance_1);
				$('.sub_total_ac_1').html(format_money_cert(sub_total_ac_1, true));

				var sub_total_ac_2 = parseFloat(ot_previous) + parseFloat(net_advance_2);
				$('.sub_total_ac_2').html(format_money_cert(sub_total_ac_2, true));

				var sub_total_ac_3 = parseFloat(ot_this_bill) + parseFloat(net_advance_3);
				$('.sub_total_ac_3').html(format_money_cert(sub_total_ac_3, true));

				var sub_total_ac_4 = parseFloat(ot_comulative) + parseFloat(net_advance_4);
				$('.sub_total_ac_4').html(format_money_cert(sub_total_ac_4, true));

				var works_exe_a_1 = $('input[name="works_exe_a_1"]').val();
				var works_executed_on_a = $('select[name="works_executed_on_a"]').val();
				if (works_executed_on_a) {
					works_executed_on_a = works_executed_on_a.replace('%', '');
					works_exe_a_1 = sub_total_ac_1 * (works_executed_on_a / 100);
					$('input[name="works_exe_a_1"]').val(format_amount_cert(works_exe_a_1));
				} else {
					works_exe_a_1 = 0;
				}

				var works_exe_a_2 = $('input[name="works_exe_a_2"]').val();
				var works_executed_on_a = $('select[name="works_executed_on_a"]').val();
				if (works_executed_on_a) {
					works_executed_on_a = works_executed_on_a.replace('%', '');
					works_exe_a_2 = sub_total_ac_2 * (works_executed_on_a / 100);
					$('input[name="works_exe_a_2"]').val(format_amount_cert(works_exe_a_2));
				} else {
					works_exe_a_2 = 0;
				}

				var works_exe_a_3 = $('input[name="works_exe_a_3"]').val();
				var works_executed_on_a = $('select[name="works_executed_on_a"]').val();
				if (works_executed_on_a) {
					works_executed_on_a = works_executed_on_a.replace('%', '');
					works_exe_a_3 = sub_total_ac_3 * (works_executed_on_a / 100);
					$('input[name="works_exe_a_3"]').val(format_amount_cert(works_exe_a_3));
				} else {
					works_exe_a_3 = 0;
				}

				var works_exe_a_4 = parseFloat(works_exe_a_2) + parseFloat(works_exe_a_3);
				$('.works_exe_a_4').html(format_money_cert(works_exe_a_4, true));

				var ret_fund_1 = $('input[name="ret_fund_1"]').val();
				ret_fund_1 = ret_fund_1.trim() != "" ? ret_fund_1 : 0;
				var less_ret_1 = parseFloat(ret_fund_1) + parseFloat(works_exe_a_1);
				$('.less_ret_1').html(format_money_cert(less_ret_1, true));

				var ret_fund_2 = $('input[name="ret_fund_2"]').val();
				ret_fund_2 = ret_fund_2.trim() != "" ? ret_fund_2 : 0;
				var less_ret_2 = parseFloat(ret_fund_2) + parseFloat(works_exe_a_2);
				$('.less_ret_2').html(format_money_cert(less_ret_2, true));

				var ret_fund_3 = $('input[name="ret_fund_3"]').val();
				ret_fund_3 = ret_fund_3.trim() != "" ? ret_fund_3 : 0;
				var less_ret_3 = parseFloat(ret_fund_3) + parseFloat(works_exe_a_3);
				$('.less_ret_3').html(format_money_cert(less_ret_3, true));

				var ret_fund_4 = parseFloat(ret_fund_2) + parseFloat(ret_fund_3);
				$('.ret_fund_4').html(format_money_cert(ret_fund_4, true));
				var less_ret_4 = parseFloat(ret_fund_4) + parseFloat(works_exe_a_4);
				$('.less_ret_4').html(format_money_cert(less_ret_4, true));

				var sub_t_de_1 = parseFloat(sub_total_ac_1) - parseFloat(less_ret_1);
				$('.sub_t_de_1').html(format_money_cert(sub_t_de_1, true));

				var sub_t_de_2 = parseFloat(sub_total_ac_2) - parseFloat(less_ret_2);
				$('.sub_t_de_2').html(format_money_cert(sub_t_de_2, true));

				var sub_t_de_3 = parseFloat(sub_total_ac_3) - parseFloat(less_ret_3);
				$('.sub_t_de_3').html(format_money_cert(sub_t_de_3, true));

				var sub_t_de_4 = parseFloat(sub_total_ac_4) - parseFloat(less_ret_4);
				$('.sub_t_de_4').html(format_money_cert(sub_t_de_4, true));

				var less_1 = $('input[name="less_1"]').val();
				less_1 = less_1.trim() != "" ? less_1 : 0;
				var less_ded_1 = less_1;
				var less_ah_1 = $('input[name="less_ah_1"]').val();
				less_ah_1 = less_ah_1.trim() != "" ? less_ah_1 : 0;
				less_ded_1 = parseFloat(less_ded_1) + parseFloat(less_ah_1);
				var less_aht_1 = $('input[name="less_aht_1"]').val();
				less_aht_1 = less_aht_1.trim() != "" ? less_aht_1 : 0;
				less_ded_1 = parseFloat(less_ded_1) + parseFloat(less_aht_1);
				$('.less_ded_1').html(format_money_cert(less_ded_1, true));

				var less_2 = $('input[name="less_2"]').val();
				less_2 = less_2.trim() != "" ? less_2 : 0;
				var less_ded_2 = less_2;
				var less_ah_2 = $('input[name="less_ah_2"]').val();
				less_ah_2 = less_ah_2.trim() != "" ? less_ah_2 : 0;
				less_ded_2 = parseFloat(less_ded_2) + parseFloat(less_ah_2);
				var less_aht_2 = $('input[name="less_aht_2"]').val();
				less_aht_2 = less_aht_2.trim() != "" ? less_aht_2 : 0;
				less_ded_2 = parseFloat(less_ded_2) + parseFloat(less_aht_2);
				$('.less_ded_2').html(format_money_cert(less_ded_2, true));

				var less_3 = $('input[name="less_3"]').val();
				less_3 = less_3.trim() != "" ? less_3 : 0;
				var less_ded_3 = less_3;
				var less_ah_3 = $('input[name="less_ah_3"]').val();
				less_ah_3 = less_ah_3.trim() != "" ? less_ah_3 : 0;
				less_ded_3 = parseFloat(less_ded_3) + parseFloat(less_ah_3);
				var less_aht_3 = $('input[name="less_aht_3"]').val();
				less_aht_3 = less_aht_3.trim() != "" ? less_aht_3 : 0;
				less_ded_3 = parseFloat(less_ded_3) + parseFloat(less_aht_3);
				$('.less_ded_3').html(format_money_cert(less_ded_3, true));

				var less_4 = parseFloat(less_2) + parseFloat(less_3);
				$('.less_4').html(format_money_cert(less_4, true));
				var less_ded_4 = less_4;
				less_ded_4 = parseFloat(less_ded_4) + parseFloat(less_ah_2) + parseFloat(less_ah_3);
				less_ded_4 = parseFloat(less_ded_4) + parseFloat(less_aht_2) + parseFloat(less_aht_3);
				$('.less_ded_4').html(format_money_cert(less_ded_4, true));

				var less_ah_2 = $('input[name="less_ah_2"]').val();
				less_ah_2 = less_ah_2.trim() != "" ? less_ah_2 : 0;
				var less_ah_3 = $('input[name="less_ah_3"]').val();
				less_ah_3 = less_ah_3.trim() != "" ? less_ah_3 : 0;
				var less_ah_4 = parseFloat(less_ah_2) + parseFloat(less_ah_3);
				$('.less_ah_4').html(format_money_cert(less_ah_4, true));

				var less_aht_2 = $('input[name="less_aht_2"]').val();
				less_aht_2 = less_aht_2.trim() != "" ? less_aht_2 : 0;
				var less_aht_3 = $('input[name="less_aht_3"]').val();
				less_aht_3 = less_aht_3.trim() != "" ? less_aht_3 : 0;
				var less_aht_4 = parseFloat(less_aht_2) + parseFloat(less_aht_3);
				$('.less_aht_4').html(format_money_cert(less_aht_4, true));

				var sub_fg_1 = parseFloat(sub_t_de_1) - parseFloat(less_ded_1);
				$('.sub_fg_1').html(format_money_cert(sub_fg_1, true));

				var sub_fg_2 = parseFloat(sub_t_de_2) - parseFloat(less_ded_2);
				$('.sub_fg_2').html(format_money_cert(sub_fg_2, true));

				var sub_fg_3 = parseFloat(sub_t_de_3) - parseFloat(less_ded_3);
				$('.sub_fg_3').html(format_money_cert(sub_fg_3, true));

				var sub_fg_4 = parseFloat(sub_t_de_4) - parseFloat(less_ded_4);
				$('.sub_fg_4').html(format_money_cert(sub_fg_4, true));
				
				var cgst_on_a1, cgst_on_tax, cgst_on_tax_2 = 0;
				var cgst_tax_1 = $('select[name="cgst_tax"]').val();
				if (cgst_tax_1) {
					cgst_tax_1 = cgst_tax_1.replace('%', '');
					cgst_on_a1 = ot_contract_amount * (cgst_tax_1 / 100);
					cgst_on_tax = ot_previous * (cgst_tax_1 / 100);
					cgst_on_tax_2 = ot_this_bill * (cgst_tax_1 / 100);

				}
				$('.cgst_on_a1').html(format_money_cert(cgst_on_a1, true));

				var cgst_on_a2 = $('input[name="cgst_prev_bill"]').val();

				if ((cgst_on_a2 == cgst_on_tax) || (cgst_on_a2 == '')) {
					$('input[name="cgst_prev_bill"]').val(cgst_on_tax);
					cgst_on_a2 = cgst_on_tax;
				} else {
					cgst_on_a2 = cgst_on_a2.trim() != "" ? cgst_on_a2 : 0;
				}

				var cgst_on_a3 = $('input[name="cgst_this_bill"]').val();

				if ((cgst_on_a3 == cgst_on_tax_2) || (cgst_on_a3 == '')) {
					$('input[name="cgst_this_bill"]').val(cgst_on_tax_2);
					cgst_on_a3 = cgst_on_tax_2;
				} else {
					cgst_on_a3 = cgst_on_a3.trim() != "" ? cgst_on_a3 : 0;
				}

				var cgst_on_a4 = parseFloat(cgst_on_a2) + parseFloat(cgst_on_a3);
				$('.cgst_on_a4').html(format_money_cert(cgst_on_a4, true));

				var sgst_on_a1, sgst_on_tax, sgst_on_tax_2 = 0;
				var sgst_tax_1 = $('select[name="sgst_tax"]').val();
				if (sgst_tax_1) {
					sgst_tax_1 = sgst_tax_1.replace('%', '');
					sgst_on_a1 = ot_contract_amount * (sgst_tax_1 / 100);
					sgst_on_tax = ot_previous * (sgst_tax_1 / 100);
					sgst_on_tax_2 = ot_this_bill * (sgst_tax_1 / 100);
				}
				$('.sgst_on_a1').html(format_money_cert(sgst_on_a1, true));

				var sgst_on_a2 = $('input[name="sgst_prev_bill"]').val();

				if ((sgst_on_a2 == sgst_on_tax) || (sgst_on_a2 == '')) {
					$('input[name="sgst_prev_bill"]').val(sgst_on_tax);
					sgst_on_a2 = sgst_on_tax;
				} else {
					sgst_on_a2 = sgst_on_a2.trim() != "" ? sgst_on_a2 : 0;
				}

				var sgst_on_a3 = $('input[name="sgst_this_bill"]').val();

				if ((sgst_on_a3 == sgst_on_tax_2) || (sgst_on_a3 == '')) {
					$('input[name="sgst_this_bill"]').val(sgst_on_tax_2);
					sgst_on_a3 = sgst_on_tax_2;
				} else {
					sgst_on_a3 = sgst_on_a3.trim() != "" ? sgst_on_a3 : 0;
				}

				var sgst_on_a4 = parseFloat(sgst_on_a2) + parseFloat(sgst_on_a3);
				$('.sgst_on_a4').html(format_money_cert(sgst_on_a4, true));

				var igst_on_a1, igst_on_tax, igst_on_tax_2 = 0;
				var igst_tax_1 = $('select[name="igst_tax"]').val();
				if (igst_tax_1) {
					igst_tax_1 = igst_tax_1.replace('%', '');
					igst_on_a1 = ot_contract_amount * (igst_tax_1 / 100);
					igst_on_tax = ot_previous * (igst_tax_1 / 100);
					igst_on_tax_2 = ot_this_bill * (igst_tax_1 / 100);
				}
				$('.igst_on_a1').html(format_money_cert(igst_on_a1, true));

				var igst_on_a2 = $('input[name="igst_prev_bill"]').val();
				// igst_on_a2 = igst_on_a2.trim() != "" ? igst_on_a2 : 0;
				if ((igst_on_a2 == igst_on_tax) || (igst_on_a2 == '')) {
					$('input[name="igst_prev_bill"]').val(igst_on_tax);
					igst_on_a2 = igst_on_tax;
				} else {
					igst_on_a2 = igst_on_a2.trim() != "" ? igst_on_a2 : 0;
				}

				var igst_on_a3 = $('input[name="igst_this_bill"]').val();
				// igst_on_a3 = igst_on_a3.trim() != "" ? igst_on_a3 : 0;

				if ((igst_on_a3 == igst_on_tax_2) || (igst_on_a3 == '')) {
					$('input[name="igst_this_bill"]').val(igst_on_tax_2);
					igst_on_a3 = igst_on_tax_2;
				} else {
					igst_on_a3 = igst_on_a3.trim() != "" ? igst_on_a3 : 0;
				}

				var igst_on_a4 = parseFloat(igst_on_a2) + parseFloat(igst_on_a3);
				$('.igst_on_a4').html(format_money_cert(igst_on_a4, true));


				var labour_cess_1 = $('input[name="labour_cess_1"]').val();
				var labour_cess = $('select[name="labour_cess"]').val();
				if (labour_cess) {
					labour_cess = labour_cess.replace('%', '');
					labour_cess_1 = ot_contract_amount * (labour_cess / 100);
					$('input[name="labour_cess_1"]').val(format_amount_cert(labour_cess_1));
				} else {
					labour_cess_1 = 0;
				}

				var labour_cess_2 = $('input[name="labour_cess_2"]').val();
				var labour_cess = $('select[name="labour_cess"]').val();
				if (labour_cess) {
					labour_cess = labour_cess.replace('%', '');
					labour_cess_2 = ot_previous * (labour_cess / 100);
					$('input[name="labour_cess_2"]').val(format_amount_cert(labour_cess_2));
				} else {
					labour_cess_2 = 0;
				}

				var labour_cess_3 = $('input[name="labour_cess_3"]').val();
				var labour_cess = $('select[name="labour_cess"]').val();
				if (labour_cess) {
					labour_cess = labour_cess.replace('%', '');
					labour_cess_3 = ot_this_bill * (labour_cess / 100);
					$('input[name="labour_cess_3"]').val(format_amount_cert(labour_cess_3));
				} else {
					labour_cess_3 = 0;
				}

				var labour_cess_4 = parseFloat(labour_cess_2) + parseFloat(labour_cess_3);
				$('.labour_cess_4').html(format_money_cert(labour_cess_4, true));

				var tot_app_tax_1 = parseFloat(cgst_on_a1) + parseFloat(sgst_on_a1) + parseFloat(igst_on_a1) + parseFloat(labour_cess_1);
				$('.tot_app_tax_1').html(format_money_cert(tot_app_tax_1, true));

				var tot_app_tax_2 = parseFloat(cgst_on_a2) + parseFloat(sgst_on_a2) + parseFloat(igst_on_a2) + parseFloat(labour_cess_2);
				$('.tot_app_tax_2').html(format_money_cert(tot_app_tax_2, true));

				var tot_app_tax_3 = parseFloat(cgst_on_a3) + parseFloat(sgst_on_a3) + parseFloat(igst_on_a3) + parseFloat(labour_cess_3);
				$('.tot_app_tax_3').html(format_money_cert(tot_app_tax_3, true));

				var tot_app_tax_4 = parseFloat(tot_app_tax_2) + parseFloat(tot_app_tax_3);
				$('.tot_app_tax_4').html(format_money_cert(tot_app_tax_4, true));

				var amount_rec_1 = parseFloat(sub_fg_1) + parseFloat(tot_app_tax_1);
				$('.amount_rec_1').html(format_money_cert(amount_rec_1, true));

				var amount_rec_2 = parseFloat(sub_fg_2) + parseFloat(tot_app_tax_2);
				$('.amount_rec_2').html(format_money_cert(amount_rec_2, true));

				var amount_rec_3 = parseFloat(sub_fg_3) + parseFloat(tot_app_tax_3);
				$('.amount_rec_3').html(format_money_cert(amount_rec_3, true));

				var amount_rec_4 = parseFloat(amount_rec_2) + parseFloat(amount_rec_3);
				$('.amount_rec_4').html(format_money_cert(amount_rec_4, true));
			});
		}
	}

	function format_amount_cert(value) {
		if (!isNaN(value)) {
			var decimalPart = value.toString().split('.')[1];

			if (decimalPart && decimalPart.length >= 3) {
				return parseFloat(value.toFixed(2));
			}
		}
		return value != 0 ? value : '';
	}

	function format_money_cert(total, excludeSymbol) {
		var amount_value = '';
		if (typeof excludeSymbol != "undefined" && excludeSymbol) {
			amount_value = amount_format(total, 1, "");
		}
		amount_value = amount_format(total, 1, "₹");
		return total != 0 ? amount_value : '';
	}

	function approve_payment_certificate_request(id) {
		"use strict";
		payment_certificate_request_approval_status(id, 2);
	}

	function deny_payment_certificate_request(id) {
		"use strict";
		payment_certificate_request_approval_status(id, 3);
	}

	function payment_certificate_request_approval_status(id, status) {
		"use strict";
		var data = {};
		data.rel_id = id;
		data.rel_type = 'ot_payment_certificate';
		data.approve = status;
		data.note = $('textarea[name="reason"]').val();
		$.post(admin_url + 'purchase/payment_certificate_request/' + id, data).done(function(response) {
			response = JSON.parse(response);
			if (response.success === true || response.success == 'true') {
				alert_float('success', response.message);
				window.location.reload();
			}
		});
	}

	function change_status_pay_cert(invoker, id) {
	    "use strict";
	    if (!invoker.value) {
	        Swal.fire({
	            icon: 'warning',
	            title: 'Warning',
	            text: 'Please select a status before proceeding.'
	        });
	        return;
	    }
	    Swal.fire({
	        title: 'Are you sure?',
	        text: 'Do you want to change the status?',
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonText: 'Yes, change it',
	        cancelButtonText: 'Cancel'
	    }).then((result) => {
	        if (result.isConfirmed) {
	            $.post(admin_url + 'purchase/change_status_pay_cert/' + invoker.value + '/' + id)
	            .done(function(response) {
	                response = JSON.parse(response);
	                alert_float('success', response.result);
	                window.location.reload();
	            });
	        }
	    });
	}

	function preview_paymentcert_btn(invoker) {
		"use strict";
		var id = $(invoker).attr('id');
		var rel_id = $(invoker).attr('rel_id');
		view_paymentcert_file(id, rel_id);
	}

	function view_paymentcert_file(id, rel_id) {
		"use strict";
		$('#paymentcert_file_data').empty();
		$("#paymentcert_file_data").load(admin_url + 'purchase/view_paymentcert_file/' + id + '/' + rel_id, function(response, status, xhr) {
			if (status == "error") {
				alert_float('danger', xhr.statusText);
			}
		});
	}

	function close_modal_preview() {
		"use strict";
		$('._project_file').modal('hide');
	}

	$("body").on('click', '.pay-cert-submit', function() {
		var that = $(this);
		var form = that.parents('form._payment_transaction_form');
		form.submit();
	});

	var selected_ot_id = $('select[name="ot_id"]').val();
	if(selected_ot_id) {
		update_ot_payment_certificate_table(selected_ot_id);
	}

	$("body").on('change', 'select[name="ot_id"]', function () {
		var ot_id = $(this).selectpicker('val');
		update_ot_payment_certificate_table(ot_id);
    });

    function update_ot_payment_certificate_table(ot_id) {
    	if(ot_id) {
			calculate_payment_certificate();
			$.post(admin_url + 'purchase/get_order_tracker_detail/' + ot_id).done(function (response) {
            response = JSON.parse(response);
            	$('select[name="project"]').val(response.project).change();
            	if (response.order_date !== '0000-00-00') {
				  var parts = response.order_date.split('-');
				  var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
				  $('input[name="order_date"]').val(formattedDate).change();
				} else {
				  var today = new Date();
				  var day = String(today.getDate()).padStart(2, '0');
				  var month = String(today.getMonth() + 1).padStart(2, '0');
				  var year = today.getFullYear();
				  var todayFormatted = day + '-' + month + '-' + year;
				  $('input[name="order_date"]').val(todayFormatted).change();
				}
            });
        } else {
        	var today = new Date();
		    var day = String(today.getDate()).padStart(2, '0');
		    var month = String(today.getMonth() + 1).padStart(2, '0');
		    var year = today.getFullYear();
		    var todayFormatted = day + '-' + month + '-' + year;
		    $('input[name="order_date"]').val(todayFormatted).change();
        }
    }

    $("body").on('change', 'select[name="vendor"]', function () {
	    var vendor = $(this).selectpicker('val');
	    var ot_view = $('select[name="ot_id"]');
	    ot_view.empty().append('<option value=""></option>').selectpicker('refresh');
	    if (vendor) {
	        $.post(admin_url + 'purchase/get_all_vendor_created_order_tracker/' + vendor).done(function (response) {
	            response = JSON.parse(response);
	            if (response.length > 0) {
	                $.each(response, function (id, value) {
	                    if (order_tracker_id == value.id) {
	                        ot_view.append('<option value="' + value.id + '" selected>' + value.pur_order_name + '</option>');
	                    } else {
	                        ot_view.append('<option value="' + value.id + '">' + value.pur_order_name + '</option>');
	                    }
	                });
	                ot_view.selectpicker('refresh').trigger('change');
	            } else {
	                ot_view.empty().append('<option value=""></option>').selectpicker('refresh').trigger('change');
	            }
	        });
	    }
	});
	$('select[name="vendor"]').trigger('change');

    function add_contract_comment() {
	  "use strict";
	    var comment = $('#comment').val();
	    if (comment == '') {
	       return;
	    }
	    var data = {};
	    data.content = comment;
	    data.rel_id = pc_id;
	    data.rel_type = 'ot_payment_certificate';
	    $('body').append('<div class="dt-loader"></div>');
	    $.post(admin_url + 'purchase/add_pc_comment', data).done(function (response) {
	       response = JSON.parse(response);
	       $('body').find('.dt-loader').remove();
	       if (response.success == true) {
	       	  alert_float('success', response.message);
	          location.reload(); 
	       }
	    });
	}

	function get_contract_comments() {
	    "use strict";
	    if (typeof (pc_id) == 'undefined') {
	       return;
	    }
	    requestGet('purchase/get_pc_comments/' + pc_id+'/ot_payment_certificate').done(function (response) {
	       $('#contract-comments').html(response);
	       var totalComments = $('[data-commentid]').length;
	       var commentsIndicator = $('.comments-indicator');
	       if(totalComments == 0) {
	            commentsIndicator.addClass('hide');
	       } else {
	         commentsIndicator.removeClass('hide');
	         commentsIndicator.text(totalComments);
	       }
	    });
    }

    function toggle_contract_comment_edit(id) {
      "use strict";
       $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
       $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
   	}

   	function edit_contract_comment(id) {
	    "use strict";
	    var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
	    if (content != '') {
	       $.post(admin_url + 'purchase/edit_pc_comment/' + id, {
	          content: content
	       }).done(function (response) {
	          response = JSON.parse(response);
	          if (response.success == true) {
	            alert_float('success', response.message);
	            location.reload();
	          }
	       });
	    }
   	}

   	function remove_contract_comment(commentid) {
    	"use strict";
    	if (confirm_delete()) {
	       requestGetJSON('purchase/remove_pc_comment/' + commentid).done(function (response) {
	          if (response.success == true) {
	            alert_float('success', response.message);
	          	location.reload(); 
	          }
	       });
    	}
   	}

   	function get_ot_pc_format() {
   		"use strict";
   		var ot_id = $('select[name="ot_id"]').val();
   		var options = $('select[name="pay_cert_options"]').val();
   		var vendor = $('select[name="vendor"]').val();
   		if (ot_id != '') {
   			$.post(admin_url + "purchase/get_ot_pc_format", {
			    ot_id: ot_id,
			    options: options,
			    vendor: vendor,
			    pc_id: pc_id,
			}).done(function (response) {
   				response = JSON.parse(response);
   				$('input[name="pc_number"]').val(response.ot_pc_format);
   			});
   		}
   	}
   	$("body").on('change', 'select[name="ot_id"], select[name="pay_cert_options"], select[name="vendor"]', function() {
	    get_ot_pc_format();
	});
</script>