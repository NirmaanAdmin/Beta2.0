<script>
	var purchase;
	var lastAddedItemKey = null;
	(function($) {
		"use strict";
		init_goods_receipt_currency(<?php echo html_entity_decode($base_currency_id) ?>);

		// Maybe items ajax search
		init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'warehouse/wh_commodity_code_search');

		appValidateForm($('#add_goods_receipt'), {
			date_c: 'required',
			date_add: 'required',
			project: 'required',
			<?php if ($pr_orders_status == true && get_warehouse_option('goods_receipt_required_po') == 1) { ?>
				pr_order_id: 'required',

			<?php } ?>

		});

		wh_calculate_total();

	})(jQuery);

	function get_tax_name_by_id(tax_id) {
		"use strict";
		var taxe_arr = <?php echo json_encode($taxes); ?>;
		var name_of_tax = '';
		$.each(taxe_arr, function(i, val) {
			if (val.id == tax_id) {
				name_of_tax = val.label;
			}
		});
		return name_of_tax;
	}

	function tax_rate_by_id(tax_id) {
		"use strict";
		var taxe_arr = <?php echo json_encode($taxes); ?>;
		var tax_rate = 0;
		$.each(taxe_arr, function(i, val) {
			if (val.id == tax_id) {
				tax_rate = val.taxrate;
			}
		});
		return tax_rate;
	}

	function numberWithCommas(x) {
		"use strict";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}


	//version2
	(function($) {
		"use strict";

		// Add item to preview from the dropdown for invoices estimates
		$("body").on('change', 'select[name="item_select"]', function() {
			var itemid = $(this).selectpicker('val');
			if (itemid != '') {
				wh_add_item_to_preview(itemid);
			}
		});

		// Recaulciate total on these changes
		$("body").on('change', 'select.taxes', function() {
			wh_calculate_total();
		});

		$("body").on('click', '.add_goods_receipt', function() {
			submit_form(false);
		});

		$('.add_goods_receipt_send').on('click', function() {
			submit_form(true);
		});

		$('select[name="pr_order_id"]').on('change', function() {
			"use strict";

			$('select[name="wo_order_id"]').val('').selectpicker('refresh');
			$('select[name="wo_order_id"]').prop('disabled', true).selectpicker('refresh');
			$('select[name="warehouse_id_m"]').on('change', function() {
				"use strict";
				var warehouse_id = $(this).val();
				if (warehouse_id) {
					$('.warehouse_select select').each(function(index) {
						if (index !== 0) { // Skip the first row
							$(this).val(warehouse_id).trigger('change');
						}
					});
				}
			});

			var pr_order_id = $('select[name="pr_order_id"]').val();
			$.get(admin_url + 'warehouse/coppy_pur_request/' + pr_order_id).done(function(response) {
				response = JSON.parse(response);

				if (response) {
					$('.invoice-item table.invoice-items-table.items tbody').html('');
					$('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);
					// $('.invoice-item table.invoice-production-approvals-table.items tbody').html('');
					// $('.invoice-item table.invoice-production-approvals-table.items tbody').append(response.production_approval_item);

					var warehouse_id = $('#warehouse_id_m').val();
					if (warehouse_id) {
						$('.warehouse_select select').each(function(index) {
							if (index !== 0) { // Skip the first row
								$(this).val(warehouse_id).trigger('change');
							}
						});
					}

					setTimeout(function() {
						wh_calculate_total();
					}, 15);

					init_selectpicker();
					init_datepicker();
					wh_reorder_items('.invoice-item');
					wh_clear_item_preview_values('.invoice-item');
					$('body').find('#items-warning').remove();
					$("body").find('.dt-loader').remove();
					$('#item_select').selectpicker('val', '');

				}

			}).fail(function(error) {

			});

			if (pr_order_id != '') {

				$.post(admin_url + 'warehouse/copy_pur_vender/' + pr_order_id).done(function(response) {
					var response_vendor = JSON.parse(response);

					$('select[name="supplier_code"]').val(response_vendor.userid).change();
					$('select[name="buyer_id"]').val(response_vendor.buyer).change();

					$('select[name="project"]').val(response_vendor.project).change();
					$('select[name="type"]').val(response_vendor.type).change();
					$('select[name="department"]').val(response_vendor.department).change();
					$('select[name="requester"]').val(response_vendor.requester).change();
					$('input[name="kind"]').val(response_vendor.kind);

					if (response_vendor.kind === 'Bought out items') {
						$('#tab_production_approvals').removeClass('hide');
					} else {
						$('#tab_production_approvals').addClass('hide');
					}

				});
			} else {
				$('select[name="supplier_code"]').val('').change();
				$('select[name="buyer_id"]').val('').change();

				$('select[name="project"]').val('').change();
				$('select[name="type"]').val('').change();
				$('select[name="department"]').val('').change();
				$('select[name="requester"]').val('').change();
			}

		});

		$('select[name="wo_order_id"]').on('change', function() {
			"use strict";
			$('select[name="pr_order_id"]').val('').selectpicker('refresh');
			$('select[name="pr_order_id"]').prop('disabled', true).selectpicker('refresh');
			$('select[name="warehouse_id_m"]').on('change', function() {
				"use strict";
				var warehouse_id = $(this).val();
				if (warehouse_id) {
					$('.warehouse_select select').each(function(index) {
						if (index !== 0) {
							$(this).val(warehouse_id).trigger('change');
						}
					});
				}
			});
			var wo_order_id = $('select[name="wo_order_id"]').val();
			$.get(admin_url + 'warehouse/copy_wo_order_items/' + wo_order_id).done(function(response) {
				response = JSON.parse(response);
				if (response) {
					$('.invoice-item table.invoice-items-table.items tbody').html('');
					$('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);
					var warehouse_id = $('#warehouse_id_m').val();
					if (warehouse_id) {
						$('.warehouse_select select').each(function(index) {
							if (index !== 0) {
								$(this).val(warehouse_id).trigger('change');
							}
						});
					}
					setTimeout(function() {
						wh_calculate_total();
					}, 15);
					init_selectpicker();
					init_datepicker();
					wh_reorder_items('.invoice-item');
					wh_clear_item_preview_values('.invoice-item');
					$('body').find('#items-warning').remove();
					$("body").find('.dt-loader').remove();
					$('#item_select').selectpicker('val', '');
				}
			}).fail(function(error) {
			});
			if (wo_order_id != '') {
				$.post(admin_url + 'warehouse/copy_wo_vender/' + wo_order_id).done(function(response) {
					var response_vendor = JSON.parse(response);
					$('select[name="supplier_code"]').val(response_vendor.userid).change();
					$('select[name="buyer_id"]').val(response_vendor.buyer).change();
					$('select[name="project"]').val(response_vendor.project).change();
					$('select[name="type"]').val(response_vendor.type).change();
					$('select[name="department"]').val(response_vendor.department).change();
					$('select[name="requester"]').val(response_vendor.requester).change();
					$('input[name="kind"]').val(response_vendor.kind);
					if (response_vendor.kind === 'Bought out items') {
						$('#tab_production_approvals').removeClass('hide');
					} else {
						$('#tab_production_approvals').addClass('hide');
					}
				});
			} else {
				$('select[name="supplier_code"]').val('').change();
				$('select[name="buyer_id"]').val('').change();
				$('select[name="project"]').val('').change();
				$('select[name="type"]').val('').change();
				$('select[name="department"]').val('').change();
				$('select[name="requester"]').val('').change();
			}
		});

		var pur_order_value = $('select[name="pr_order_id"]').val();
		var wo_order_value = $('select[name="wo_order_id"]').val();
		if(!empty(pur_order_value)) {
			$('select[name="wo_order_id"]').prop('disabled', true).selectpicker('refresh');
		}
		if(!empty(wo_order_value)) {
			$('select[name="pr_order_id"]').prop('disabled', true).selectpicker('refresh');
		}
	})(jQuery);

	// Add item to preview
	function wh_add_item_to_preview(id) {
		"use strict";

		requestGetJSON('warehouse/get_item_by_id/' + id).done(function(response) {
			clear_item_preview_values();

			$('.main input[name="commodity_code"]').val(response.itemid);
			$('.main textarea[name="commodity_name"]').val(response.code_description);
			$('.main textarea[name="description"]').val(response.description);
			$('.main input[name="unit_price"]').val(response.purchase_price);
			$('.main input[name="unit_name"]').val(response.unit_name);
			$('.main input[name="unit_id"]').val(response.unit_id);
			$('.main input[name="quantities"]').val(1);

			if ($('select[name="warehouse_id_m"]').val() != '') {
				$('.main select[name="warehouse_id"]').val($('select[name="warehouse_id_m"]').val());
				init_selectpicker();
				$('.selectpicker').selectpicker('refresh');
			}

			var taxSelectedArray = [];
			if (response.taxname && response.taxrate) {
				taxSelectedArray.push(response.taxname + '|' + response.taxrate);
			}
			if (response.taxname_2 && response.taxrate_2) {
				taxSelectedArray.push(response.taxname_2 + '|' + response.taxrate_2);
			}

			$('.main select.taxes').selectpicker('val', taxSelectedArray);
			$('.main input[name="unit"]').val(response.unit_name);

			var $currency = $("body").find('.accounting-template select[name="currency"]');
			var baseCurency = $currency.attr('data-base');
			var selectedCurrency = $currency.find('option:selected').val();
			var $rateInputPreview = $('.main input[name="rate"]');

			if (baseCurency == selectedCurrency) {
				$rateInputPreview.val(response.rate);
			} else {
				var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
				if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
					$rateInputPreview.val(response.rate);
				} else {
					$rateInputPreview.val(itemCurrencyRate);
				}
			}

			$(document).trigger({
				type: "item-added-to-preview",
				item: response,
				item_type: 'item',
			});
		});
	}

	function wh_add_item_to_table(data, itemid) {
		"use strict";

		data = typeof(data) == 'undefined' || data == 'undefined' ? wh_get_item_preview_values() : data;

		if (data.warehouse_id == "" || data.quantities == "" || data.commodity_code == "") {
			if (data.warehouse_id == "") {
				alert_float('warning', '<?php echo _l('please_select_a_warehouse') ?>');
			}
			return;
		}
		var table_row = '';
		var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
		lastAddedItemKey = item_key;
		$("body").append('<div class="dt-loader"></div>');
		wh_get_item_row_template('newitems[' + item_key + ']', data.commodity_name, data.warehouse_id, data.po_quantities, data.quantities, data.unit_name, data.unit_price, data.taxname, data.lot_number, data.vendor_id, data.delivery_date, data.date_manufacture, data.expiry_date, data.commodity_code, data.unit_id, data.tax_rate, data.tax_money, data.goods_money, data.note, itemid, data.description).done(function(output) {
			table_row += output;

			$('.invoice-item table.invoice-items-table.items tbody').append(table_row);

			setTimeout(function() {
				wh_calculate_total();
			}, 15);
			init_selectpicker();
			init_datepicker();
			wh_reorder_items('.invoice-item');
			wh_clear_item_preview_values('.invoice-item');
			$('body').find('#items-warning').remove();
			$("body").find('.dt-loader').remove();
			$('#item_select').selectpicker('val', '');

			<?php if (get_option('wh_products_by_serial')) { ?>
				// open serial modal
				// fill_multiple_serial_number_modal(data.quantities, 'newitems[' + item_key + ']');
			<?php } ?>

			return true;
		});
		return false;
	}

	function wh_get_item_preview_values() {
		"use strict";

		var response = {};
		response.commodity_name = $('.invoice-item .main textarea[name="commodity_name"]').val();
		response.description = $('.invoice-item .main textarea[name="description"]').val();
		response.warehouse_id = $('.invoice-item .main select[name="warehouse_id"]').val();
		response.po_quantities = $('.invoice-item .main input[name="po_quantities"]').val();
		response.quantities = $('.invoice-item .main input[name="quantities"]').val();
		response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
		response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
		response.taxname = $('.main select.taxes').selectpicker('val');
		response.lot_number = $('.invoice-item .main input[name="lot_number"]').val();
		response.vendor_id = $('.invoice-item .main select[name="vendor_id"]').val();
		response.delivery_date = $('.invoice-item .main input[name="delivery_date"]').val();
		response.date_manufacture = $('.invoice-item .main input[name="date_manufacture"]').val();
		response.expiry_date = $('.invoice-item .main input[name="expiry_date"]').val();
		response.commodity_code = $('.invoice-item .main input[name="commodity_code"]').val();
		response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
		response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
		response.tax_money = $('.invoice-item .main input[name="tax_money"]').val();
		response.goods_money = $('.invoice-item .main input[name="goods_money"]').val();
		response.note = $('.invoice-item .main input[name="note"]').val();

		return response;
	}

	function wh_clear_item_preview_values(parent) {
		"use strict";

		var previewArea = $(parent + ' .main');
		previewArea.find('input').val('');
		previewArea.find('textarea').val('');
		previewArea.find('select').val('').selectpicker('refresh');
	}

	function wh_get_item_row_template(name, commodity_name, warehouse_id, po_quantities, quantities, unit_name, unit_price, taxname, lot_number, vendor_id, delivery_date, date_manufacture, expiry_date, commodity_code, unit_id, tax_rate, tax_money, goods_money, note, item_key, description) {
		"use strict";

		jQuery.ajaxSetup({
			async: false
		});

		var d = $.post(admin_url + 'warehouse/get_good_receipt_row_template', {
			name: name,
			commodity_name: commodity_name,
			description: description,
			warehouse_id: warehouse_id,
			po_quantities: po_quantities,
			quantities: quantities,
			unit_name: unit_name,
			unit_price: unit_price,
			taxname: taxname,
			lot_number: lot_number,
			vendor_id: vendor_id,
			delivery_date: delivery_date,
			date_manufacture: date_manufacture,
			expiry_date: expiry_date,
			commodity_code: commodity_code,
			unit_id: unit_id,
			tax_rate: tax_rate,
			tax_money: tax_money,
			goods_money: goods_money,
			note: note,
			item_key: item_key
		});
		jQuery.ajaxSetup({
			async: true
		});
		return d;
	}

	function wh_delete_item(row, itemid, parent) {
		"use strict";

		$(row).parents('tr').addClass('animated fadeOut', function() {
			setTimeout(function() {
				$(row).parents('tr').remove();
				wh_calculate_total();
			}, 50);
		});
		if (itemid && $('input[name="isedit"]').length > 0) {
			$(parent + ' #removed-items').append(hidden_input('removed_items[]', itemid));
		}
	}

	function wh_reorder_items(parent) {
		"use strict";

		var rows = $(parent + ' .table.has-calculations tbody tr.item');
		var i = 1;
		$.each(rows, function() {
			$(this).find('input.order').val(i);
			i++;
		});
	}

	function wh_calculate_total() {
		"use strict";
		if ($('body').hasClass('no-calculate-total')) {
			return false;
		}

		var calculated_tax,
			taxrate,
			item_taxes,
			row,
			_amount,
			_tax_name,
			taxes = {},
			taxes_rows = [],
			subtotal = 0,
			total = 0,
			total_tax_money = 0,
			quantity = 1,
			total_discount_calculated = 0,
			rows = $('.table.has-calculations tbody tr.item'),
			subtotal_area = $('#subtotal'),
			discount_area = $('#discount_area'),
			adjustment = $('input[name="adjustment"]').val(),
			// discount_percent = $('input[name="discount_percent"]').val(),
			discount_percent = 'before_tax',
			discount_fixed = $('input[name="discount_total"]').val(),
			discount_total_type = $('.discount-total-type.selected'),
			discount_type = $('select[name="discount_type"]').val();

		$('.wh-tax-area').remove();

		$.each(rows, function() {

			quantity = $(this).find('[data-quantity]').val();
			if (quantity === '') {
				quantity = 1;
				$(this).find('[data-quantity]').val(1);
			}

			_amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
			_amount = parseFloat(_amount);

			$(this).find('td.amount').html(format_money(_amount, true));
			var variation_unit = $(this).find('td.po_quantities input').val() - $(this).find('td.quantities input').val();

			$(this).find('td.po_quantities span').html('Rem: ' + variation_unit);
			subtotal += _amount;
			row = $(this);
			item_taxes = $(this).find('select.taxes').val();

			if (item_taxes) {
				$.each(item_taxes, function(i, taxname) {
					taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
					calculated_tax = (_amount / 100 * taxrate);
					if (!taxes.hasOwnProperty(taxname)) {
						if (taxrate != 0) {
							_tax_name = taxname.split('|');
							var tax_row = '<tr class="wh-tax-area"><td>' + _tax_name[0] + '(' + taxrate + '%)</td><td id="tax_id_' + slugify(taxname) + '"></td></tr>';
							$(subtotal_area).after(tax_row);
							taxes[taxname] = calculated_tax;
						}
					} else {
						// Increment total from this tax
						taxes[taxname] = taxes[taxname] += calculated_tax;
					}
				});
			}
		});

		// Discount by percent
		if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
			total_discount_calculated = (subtotal * discount_percent) / 100;
		} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
			total_discount_calculated = discount_fixed;
		}

		$.each(taxes, function(taxname, total_tax) {
			if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
				total_tax_calculated = (total_tax * discount_percent) / 100;
				total_tax = (total_tax - total_tax_calculated);
			} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
				var t = (discount_fixed / subtotal) * 100;
				total_tax = (total_tax - (total_tax * t) / 100);
			}

			total += total_tax;
			total_tax_money += total_tax;
			total_tax = format_money(total_tax);
			$('#tax_id_' + slugify(taxname)).html(total_tax);
		});

		total = (total + subtotal);

		// Discount by percent
		if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-percent')) {
			total_discount_calculated = (total * discount_percent) / 100;
		} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-fixed')) {
			total_discount_calculated = discount_fixed;
		}

		total = total - total_discount_calculated;
		adjustment = parseFloat(adjustment);

		// Check if adjustment not empty
		if (!isNaN(adjustment)) {
			total = total + adjustment;
		}

		var discount_html = '-' + format_money(total_discount_calculated);
		$('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));

		// Append, format to html and display
		$('.discount-total').html(discount_html);
		$('.adjustment').html(format_money(adjustment));
		$('.wh-subtotal').html(format_money(subtotal) + hidden_input('total_goods_money', accounting.toFixed(subtotal, app.options.decimal_places)) + hidden_input('value_of_inventory', accounting.toFixed(subtotal, app.options.decimal_places)));

		$('.inventory_value').remove();
		var total_inventory_value = '<tr class="inventory_value"><td><span class="bold"><?php echo _l('value_of_inventory'); ?> :</span></td><td class="">' + format_money(subtotal) + '</td></tr>';
		$('#subtotal').after(total_inventory_value);

		$('.total_tax_value').remove();
		var total_tax_value = '<tr class="total_tax_value"><td><span class="bold"><?php echo _l('total_tax_money'); ?> :</span></td><td class="">' + format_money(total_tax_money) + '</td></tr>';
		$('#totalmoney').before(total_tax_value);

		$('.wh-total').html(format_money(total) + hidden_input('total_tax_money', accounting.toFixed(total_tax_money, app.options.decimal_places)) + hidden_input('total_money', accounting.toFixed(total, app.options.decimal_places)));

		$(document).trigger('wh-receipt-note-total-calculated');

	}


	function submit_form(save_and_send_request) {
		"use strict";

		wh_calculate_total();

		var $itemsTable = $('.invoice-items-table');
		var $previewItem = $itemsTable.find('.main');

		if ($itemsTable.length && $itemsTable.find('.item').length === 0) {
			alert_float('warning', '<?php echo _l('wh_enter_at_least_one_product'); ?>', 3000);
			return false;
		}


		// Check for required items without attachments
		var hasMissingAttachments = false;
		var $checklistTable = $('.table.items-preview');

		$checklistTable.find('tbody tr').each(function() {
			var $row = $(this);
			var $checkbox = $row.find('input[type="checkbox"]');
			var isRequired = $checkbox.is(':checked');
			var hasAttachment = $row.find('input[type="file"]').val() || $row.find('.btn-info').length > 0;

			if (isRequired && !hasAttachment) {
				hasMissingAttachments = true;
				$row.addClass('has-error'); // Highlight the row
				return false; // Break out of the loop
			}
		});

		if (hasMissingAttachments) {
			// Show modal if not already visible
			// $('#documentationModal').modal('show');

			// Scroll to the first error
			$('html, body').animate({
				scrollTop: $('.has-error').first().offset().top - 100
			}, 500);

			alert_float('danger', '<?php echo _l("Required items must have attachments"); ?>', 5000);
			return false;
		}

		// Convert all checkboxes to 1/0 values before submission
		$('input[type="checkbox"][name^="required"]').each(function() {
			var $hidden = $(this).prev('input[type="hidden"]');
			$hidden.val($(this).is(':checked') ? '1' : '0');
			$(this).prop('disabled', true); // Disable checkbox so only hidden input is submitted
		});


		$('input[name="save_and_send_request"]').val(save_and_send_request);

		var rows = $('.table.has-calculations tbody tr.item');
		var check_warehouse_status = true;
		var check_available_quantity_status = true;
		$.each(rows, function() {
			var warehouse_id = $(this).find('td.warehouse_select select').val();
			var available_quantity_value = $(this).find('td.po_quantities input').val();
			var quantity_value = $(this).find('td.quantities input').val();
			if (warehouse_id == '' || warehouse_id == undefined) {
				check_warehouse_status = false;
			}
			if (parseFloat(available_quantity_value) < parseFloat(quantity_value)) {
				check_available_quantity_status = false;
			}
		})
		if (check_warehouse_status == true && check_available_quantity_status == true) {
			// Add disabled to submit buttons
			$(this).find('.add_goods_receipt_send').prop('disabled', true);
			$(this).find('.add_goods_receipt').prop('disabled', true);
			$('#add_goods_receipt').submit();
		} else {
			if (check_warehouse_status == false) {
				alert_float('warning', '<?php echo _l('please_select_a_warehouse') ?>');
			} else {
				alert_float('warning', '<?php echo _l('inventory_quantity_is_not_enough') ?>');
			}

		}

		return true;
	}

	/*scanner barcode*/
	$(document).ready(function() {
		var pressed = false;
		var chars = [];
		$(window).keypress(function(e) {
			if (e.key == '%') {
				pressed = true;
			}
			chars.push(String.fromCharCode(e.which));
			if (pressed == false) {
				setTimeout(function() {
					if (chars.length >= 8) {
						var barcode = chars.join('');
						requestGetJSON('warehouse/wh_get_item_by_barcode/' + barcode).done(function(response) {
							if (response.status == true || response.status == 'true') {
								wh_add_item_to_preview(response.id);
								alert_float('success', response.message);
							} else {
								alert_float('warning', '<?php echo _l('no_matching_products_found') ?>');
							}
						});

					}
					chars = [];
					pressed = false;
				}, 200);
			}
			pressed = true;
		});
	});

	function fill_multiple_serial_number_modal(quantity, prefix_name) {
		"use strict";

		if (quantity > 0) {
			$("#modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/fill_multiple_serial_number_modal'); ?>", {
				slug: 'add',
				quantity: quantity,
				prefix_name: prefix_name,
			}, function() {
				$("body").find('#serialNumberModal').modal({
					show: true,
					backdrop: 'static'
				});
			});
		} else {
			alert_float('warning', "<?php echo _l('please_choose_quantity_more_than_0') ?>");
		}

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');
	}

	function wh_view_serial_number(name_quantities, serial_input, prefix_name) {
		"use strict";

		var serial_input_value = $('input[name="' + serial_input + '"]').val();
		if (serial_input_value == '') {
			var quantity = $('input[name="' + name_quantities + '"]').val();
			fill_multiple_serial_number_modal(quantity, prefix_name);
		} else {

			$("#modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/fill_multiple_serial_number_modal'); ?>", {
				slug: 'edit',
				serial_input_value: serial_input_value,
				prefix_name: prefix_name,

			}, function() {
				$("body").find('#serialNumberModal').modal({
					show: true,
					backdrop: 'static'
				});
			});
		}

	}

	// Set the currency for accounting
	function init_goods_receipt_currency(id, callback) {
		var $accountingTemplate = $("body").find('.accounting-template');

		if ($accountingTemplate.length || id) {
			var selectedCurrencyId = !id ? $accountingTemplate.find('select[name="currency"]').val() : id;

			requestGetJSON('misc/get_currency/' + selectedCurrencyId)
				.done(function(currency) {
					// Used for formatting money
					accounting.settings.currency.decimal = currency.decimal_separator;
					accounting.settings.currency.thousand = currency.thousand_separator;
					accounting.settings.currency.symbol = currency.symbol;
					accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';

					wh_calculate_total();

					if (callback) {
						callback();
					}
				});
		}
	}

	function view_goods_receipt_attachments(file_id, rel_id, rel_type) {
		"use strict";
		$.post(admin_url + 'warehouse/view_goods_receipt_attachments', {
			rel_id: rel_id,
			rel_type: rel_type,
			file_id: file_id
		}).done(function(response) {
			response = JSON.parse(response);
			if (response.result) {
				$('.view_goods_receipt_attachments').html(response.result);
			} else {
				$('.view_goods_receipt_attachments').html('');
			}
			$('#viewgoodsReceiptAttachmentModal').modal('show');
		});
	}

	function preview_goods_receipt_btn(invoker) {
		"use strict";
		var id = $(invoker).attr('id');
		view_goods_receipt_file(id);
	}

	function view_goods_receipt_file(id) {
		"use strict";
		$('#goods_receipt_file_data').empty();
		$("#goods_receipt_file_data").load(admin_url + 'warehouse/view_goods_receipt_file/' + id, function(response, status, xhr) {
			if (status == "error") {
				alert_float('danger', xhr.statusText);
			}
		});
	}

	function close_modal_preview() {
		"use strict";
		$('._project_file').modal('hide');
	}

	function delete_goods_receipt_attachment(id) {
		"use strict";
		if (confirm_delete()) {
			requestGet('warehouse/delete_goods_receipt_attachment/' + id).done(function(success) {
				if (success == 1) {
					$(".view_goods_receipt_attachments").find('[data-attachment-id="' + id + '"]').remove();
				}
			}).fail(function(error) {
				alert_float('danger', error.responseText);
			});
		}
	}
</script>