<script>
  var purchase;
  var warehouses;
  var lastAddedItemKey = null;
  (function($) {
    "use strict";

    init_goods_delivery_currency(<?php echo html_entity_decode($base_currency_id) ?>);

    appValidateForm($('#add_stock_reconciliation'), {
      date_c: 'required',
      date_add: 'required',
      project: 'required',
      <?php if ($pr_orders_status == true && get_warehouse_option('goods_delivery_required_po') == 1) {  ?>
        pr_order_id: 'required',
      <?php } ?>

    });

    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'warehouse/wh_commodity_code_search/rate');

    wh_calculate_total();


  })(jQuery);



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

    $("body").on('click', '.add_goods_delivery', function() {
      submit_form(false);
    });

    $('.add_goods_delivery_send').on('click', function() {
      submit_form(true);
    });


    $("body").on('change', 'select[name="warehouse_id"]', function() {
      "use strict";

      var data = {};
      data.commodity_id = $('.main input[name="commodity_code"]').val();
      data.warehouse_id = $('.main select[name="warehouse_id"]').val();
      var quantities = $('.main input[name="quantities"]').val();

      if (data.commodity_id != '' && data.warehouse_id != '') {
        $.post(admin_url + 'warehouse/get_quantity_inventory', data).done(function(response) {
          response = JSON.parse(response);
          $('.main input[name="available_quantity"]').val(response.value);
          if (parseFloat(response.value) < parseFloat(quantities)) {}
        });
      } else {
        $('.main input[name="available_quantity"]').val(0);
        alert_float('warning', '<?php echo _l('inventory_quantity_is_not_enough') ?>');
      }

    });

    $('input[name="quantities"]').on('change', function() {
      "use strict";

      var available_quantity = $('.main input[name="available_quantity"]').val();
      var quantities = $('.main input[name="quantities"]').val();
      if (parseFloat(available_quantity) < parseFloat(quantities)) {
        alert_float('warning', '<?php echo _l('inventory_quantity_is_not_enough') ?>');
        $('.main input[name="quantities"]').val(available_quantity);

      }
    });

    $("body").on('change', 'input[name="shipping_fee"]', function() {
      wh_calculate_total();
    });

    $(".sortable.item").each(function() {
      var row = $(this);
      var selectedValues = row.find("select.vendor_list").val() || [];
      row.data("previousValues", selectedValues);
    });

    $("body").on("change", "select.vendor_list", function() {
      var currentElement = $(this);
      var row = currentElement.closest(".sortable.item");
      var previousValues = row.data("previousValues") || [];
      var currentValues = currentElement.val() || [];
      var deselectedValues = previousValues.filter(value => !currentValues.includes(value));
      var selectedValues = currentValues.filter(value => !previousValues.includes(value));
      var vendor = '';
      if (deselectedValues.length > 0) {
        vendor = deselectedValues[0];
      }
      if (selectedValues.length > 0) {
        vendor = selectedValues[0];
      }

      row.data("previousValues", currentValues);
      console.log(currentValues);
      if (currentValues.length > 3) {
        currentElement.find("option:selected").last().prop("selected", false);
        if (currentElement.hasClass("selectpicker")) {
          currentElement.selectpicker("refresh");
        }
        alert("You can select up to 3 vendors only.");
      } else  {
        var data = {
          delivery_id: $('input[name="id"]').val(),
          vendor: vendor,
          item_key: row.find("input.ids").attr("data-id"),
          options: currentValues,
          apply_to_all: $('#apply_to_all_value').val(),
        };

        $.post(admin_url + "warehouse/get_vendor_issued_data", data)
          .done(function(response) {
            try {
              response = JSON.parse(response);
              if (row.find('.vendor-' + vendor).length == 0) {
                row.find("td.quantities").append(response.quantities_html);
                row.find("td.lot_number").append(response.lot_number_html);
                row.find("td.issued_date").append(response.issued_date_html);
              } else {
                row.find('.vendor-' + vendor).remove();
              }
              init_datepicker();
            } catch (e) {
              console.error("Error parsing response: ", e);
            }
          })
          .fail(function(xhr, status, error) {
            console.error("AJAX request failed: ", error);
          });
      }
    });


  })(jQuery);

  function check_quantity_inventory(commodity_id, quantity, warehouse_id, switch_barcode_scanners = false) {
    // body...
    data.commodity_id = commodity_id;
    data.quantity = quantity;
    data.switch_barcode_scanners = switch_barcode_scanners;
    data.warehouse_id = warehouse_id;

    if (commodity_id != '' && warehouse_id != '') {
      $.post(admin_url + 'warehouse/check_quantity_inventory', data).done(function(response) {
        response = JSON.parse(response);

        purchase.setDataAtCell(row, 2, response.value);

      });
    }
  }

  // Add item to preview
  function wh_add_item_to_preview(id) {
    "use strict";

    requestGetJSON('warehouse/get_item_by_id/' + id + '/' + true).done(function(response) {
      clear_item_preview_values();
      $('.main input[name="commodity_code"]').val(response.itemid);
      $('.main textarea[name="commodity_name"]').val(response.code_description);
      $('.main input[name="unit_price"]').val(response.rate);
      $('.main input[name="unit_name"]').val(response.unit_name);
      $('.main input[name="unit_id"]').val(response.unit_id);
      $('.main input[name="quantities"]').val(1);
      $('.main select[name="warehouse_id"]').html(response.warehouses_html);
      $('.main input[name="guarantee_period"]').val(response.guarantee_new);
      $('.main input[name="without_checking_warehouse"]').val(response.without_checking_warehouse);
      $('.selectpicker').selectpicker('refresh');
      // if($('select[name="warehouse_id"]').val() != ''){
      //   $('.main select[name="warehouse_id"]').val($('select[name="warehouse_id"]').val());
      //   init_selectpicker();
      //   $('.selectpicker').selectpicker('refresh');
      // }

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

  function wh_clear_item_preview_values(parent) {
    "use strict";

    var previewArea = $(parent + ' .main');
    previewArea.find('input').val('');
    previewArea.find('textarea').val('');
    previewArea.find('select').val('').selectpicker('refresh');
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
      total_money = 0,
      total_tax_money = 0,
      quantity = 1,
      total_discount_calculated = 0,
      item_discount_percent = 0,
      item_discount = 0,
      item_total_payment,
      rows = $('.table.has-calculations tbody tr.item'),
      subtotal_area = $('#subtotal'),
      discount_area = $('#discount_area'),
      adjustment = $('input[name="adjustment"]').val(),
      // discount_percent = $('input[name="discount_percent"]').val(),
      discount_percent = 'before_tax',
      discount_fixed = $('input[name="discount_total"]').val(),
      discount_total_type = $('.discount-total-type.selected'),
      discount_type = $('select[name="discount_type"]').val(),
      additional_discount = $('input[name="additional_discount"]').val(),
      shipping_fee = $('input[name="shipping_fee"]').val();


    $('.wh-tax-area').remove();

    $.each(rows, function() {

      var item_tax = 0,
        item_amount = 0;

      quantity = $(this).find('[data-quantity]').val();
      if (quantity === '') {
        quantity = 1;
        $(this).find('[data-quantity]').val(1);
      }
      item_discount_percent = $(this).find('td.discount input').val();

      if (isNaN(item_discount_percent) || item_discount_percent == '') {
        item_discount_percent = 0;
      }

      _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
      item_amount = _amount;
      _amount = parseFloat(_amount);

      $(this).find('td.amount').html(format_money(_amount));

      subtotal += _amount;
      row = $(this);
      item_taxes = $(this).find('select.taxes').val();

      if (item_taxes) {
        $.each(item_taxes, function(i, taxname) {
          taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
          calculated_tax = (_amount / 100 * taxrate);
          item_tax += calculated_tax;
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
      //Discount of item
      item_discount = (parseFloat(item_amount) + parseFloat(item_tax)) * parseFloat(item_discount_percent) / 100;
      item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);

      // Append value to item
      total_discount_calculated += item_discount;
      $(this).find('td.discount_money input').val(item_discount);
      $(this).find('td.total_after_discount input').val(item_total_payment);

      $(this).find('td.label_discount_money').html(format_money(item_discount));
      $(this).find('td.label_total_after_discount').html(format_money(item_total_payment));

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
    total_money = total;
    // Discount by percent
    if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-percent')) {
      total_discount_calculated = (total * discount_percent) / 100;
    } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-fixed')) {
      total_discount_calculated = discount_fixed;
    }

    total = total - total_discount_calculated - parseFloat(additional_discount);
    adjustment = parseFloat(adjustment);

    // Check if adjustment not empty
    if (!isNaN(adjustment)) {
      total = total + adjustment;
    }

    if (!isNaN(shipping_fee)) {
      total = total + parseFloat(shipping_fee);
    }

    var discount_html = '-' + format_money(parseFloat(total_discount_calculated) + parseFloat(additional_discount));
    $('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));

    // Append, format to html and display
    $('.wh-total_discount').html(discount_html + hidden_input('total_discount', accounting.toFixed(total_discount_calculated, app.options.decimal_places)));
    $('.adjustment').html(format_money(adjustment));
    $('.wh-subtotal').html(format_money(subtotal) + hidden_input('sub_total', accounting.toFixed(subtotal, app.options.decimal_places)) + hidden_input('total_money', accounting.toFixed(total_money, app.options.decimal_places)));
    $('.wh-total').html(format_money(total) + hidden_input('after_discount', accounting.toFixed(total, app.options.decimal_places)));

    $(document).trigger('wh-receipt-note-total-calculated');

  }

  function get_available_quantity(commodity_code_name, from_stock_name, available_quantity_name) {
    "use strict";

    var data = {};
    data.commodity_id = $('input[name="' + commodity_code_name + '"]').val();
    data.warehouse_id = $('select[name="' + from_stock_name + '"]').val();
    if (data.commodity_id != '' && data.warehouse_id != '') {
      $.post(admin_url + 'warehouse/get_quantity_inventory', data).done(function(response) {
        response = JSON.parse(response);
        $('input[name="' + available_quantity_name + '"]').val(response.value);
      });
    } else {
      $('input[name="' + available_quantity_name + '"]').val(0);
    }

    setTimeout(function() {
      wh_calculate_total();
    }, 15);

  }

  function submit_form(save_and_send_request) {
    "use strict";

    wh_calculate_total();

    var $itemsTable = $('.invoice-items-table');
    var $previewItem = $itemsTable.find('.main');
    var check_warehouse_status = true;
    var check_quantity_status = true;
    var check_row_wise_quantity_status = true;
    var check_available_quantity_status = true;

    if ($itemsTable.length && $itemsTable.find('.item').length === 0) {
      alert_float('warning', '<?php echo _l('wh_enter_at_least_one_product'); ?>', 3000);
      return false;
    }

    $('input[name="save_and_send_request"]').val(save_and_send_request);

    var rows = $('.table.has-calculations tbody tr.item');

    $.each(rows, function() {
      var warehouse_id = $(this).find('td.warehouse_select select').val();
      var available_quantity_value = $(this).find('td.available_quantity input').val();
      var quantity_value = 0;
      var quantity_length = $(this).find('td.quantities .form-group').length;
      var without_checking_warehouse = $(this).find('td.without_checking_warehouse input').val();
      $(this).closest('.sortable.item').css('background-color', 'white');

      if (quantity_length == 0) {
        quantity_value = 0;
      } else {
        var total_quantity = 0;
        $(this).find('td.quantities .form-group').each(function(index, thisElement) {
          var inputValue = parseFloat($(thisElement).find('input').val()) || 0;
          total_quantity += inputValue;
        });
        quantity_value = total_quantity;
      }

      if ((warehouse_id == '' || warehouse_id == undefined) && (without_checking_warehouse == 0 || without_checking_warehouse == '0')) {
        check_warehouse_status = false;
      }
      // if(parseFloat(quantity_value) == 0 && (without_checking_warehouse == 0 || without_checking_warehouse == '0')){
      //   check_quantity_status = false;
      // }
      if (parseFloat(available_quantity_value) < parseFloat(quantity_value) && (without_checking_warehouse == 0 || without_checking_warehouse == '0')) {
        check_available_quantity_status = false;
      }
      if (parseFloat(available_quantity_value) < parseFloat(quantity_value)) {
        check_row_wise_quantity_status = false;
        $(this).closest('.sortable.item').css('background-color', 'lightpink');
      }
    })
    
    if (check_warehouse_status == true && check_quantity_status == true && check_available_quantity_status == true && check_row_wise_quantity_status == true) {
      // Add disabled to submit buttons
      $(this).find('.add_goods_receipt_send').prop('disabled', true);
      $(this).find('.add_goods_receipt').prop('disabled', true);
      $('#add_stock_reconciliation').submit();
    } else {
      if (check_warehouse_status == false) {
        alert_float('warning', '<?php echo _l('please_select_a_warehouse') ?>');
      } else if (check_row_wise_quantity_status == false) {
        alert_float('warning', '<?php echo _l('cannot_increase_the_quantity_beyond_the_available_stock') ?>');
      } else if (check_quantity_status == false) {
        alert_float('warning', '<?php echo _l('please_choose_quantity_export') ?>');
      } else {
        //check_available_quantity
        alert_float('warning', '<?php echo _l('inventory_quantity_is_not_enough') ?>');
      }

    }

    return true;
  }

  // function stock_import_change(){
  //   "use strict";
  //   var goods_receipt_id = $('select[name="goods_receipt_id"]').val();
  //   $.post(admin_url + 'warehouse/copy_manage_receipt/'+goods_receipt_id).done(function(response){
  //     response = JSON.parse(response);

  //     $('input[name="additional_discount"]').val((response.additional_discount));
  //     $('.invoice-item table.invoice-items-table.items tbody').html('');
  //     $('.invoice-item table.invoice-items-table.items tbody').append(response.result);

  //     setTimeout(function () {
  //       wh_calculate_total();
  //     }, 15);

  //     init_selectpicker();
  //     init_datepicker();
  //     wh_reorder_items('.invoice-item');
  //     wh_clear_item_preview_values('.invoice-item');
  //     $('body').find('#items-warning').remove();
  //     $("body").find('.dt-loader').remove();
  //     $('#item_select').selectpicker('val', '');

  //     if(goods_receipt_id != ''){
  //      $('select[name="buyer_id"]').val(response.goods_receipt.buyer).change();
  //      $('select[name="project"]').val(response.goods_receipt.project).change();
  //      $('select[name="type"]').val(response.goods_receipt.type).change();
  //      $('select[name="department"]').val(response.goods_receipt.department).change();
  //      $('select[name="requester"]').val(response.goods_receipt.requester).change();
  //     }else{
  //       $('select[name="buyer_id"]').val('').change();
  //       $('select[name="project"]').val('').change();
  //       $('select[name="type"]').val('').change();
  //       $('select[name="department"]').val('').change();
  //       $('select[name="requester"]').val('').change();
  //     }
  //     $('.selectpicker').selectpicker('refresh');

  //   });
  // }

  function pr_order_change() {
    "use strict";
    var pr_order_id = $('select[name="pr_order_id"]').val();

    $.post(admin_url + 'warehouse/reconciliation_delivery_copy_pur_order/' + pr_order_id).done(function(response) {
      response = JSON.parse(response);
      $('input[name="additional_discount"]').val((response.additional_discount));
      $('.invoice-item table.invoice-items-table.items tbody').html('');
      $('.invoice-item table.invoice-items-table.items tbody').append(response.result);
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

    });
    if (pr_order_id != '') {
      $.post(admin_url + 'warehouse/copy_pur_vender/' + pr_order_id).done(function(response) {
        var response_vendor = JSON.parse(response);
        $('select[name="buyer_id"]').val(response_vendor.buyer).change();
        $('select[name="project"]').val(response_vendor.project).change();
        $('select[name="type"]').val(response_vendor.type).change();
        $('select[name="department"]').val(response_vendor.department).change();
        $('select[name="requester"]').val(response_vendor.requester).change();
      });
    } else {
      $('select[name="buyer_id"]').val('').change();
      $('select[name="project"]').val('').change();
      $('select[name="type"]').val('').change();
      $('select[name="department"]').val('').change();
      $('select[name="requester"]').val('').change();
    }
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

  function wh_change_serial_number(name_commodity_code, name_warehouse_id, name_serial_number, name_commodity_name) {
    "use strict";

    var data_post = {};

    data_post.commodity_id = $('input[name="' + name_commodity_code + '"]').val();
    data_post.warehouse_id = $('select[name="' + name_warehouse_id + '"]').val();
    data_post.serial_number = $('input[name="' + name_serial_number + '"]').val();
    data_post.commodity_name = $('textarea[name="' + name_commodity_name + '"]').val();

    var row_serial_numbers = $('.table.has-calculations tbody tr.item');
    var serial_number_array = [];

    $.each(row_serial_numbers, function() {
      var warehouse_id = $(this).find('td.warehouse_select select').val();
      var commodity_code = $(this).find('td.commodity_code input').val();
      var serial_number = $(this).find('td.serial_number input').val();

      if (data_post.commodity_id == commodity_code && data_post.warehouse_id == warehouse_id && data_post.serial_number != serial_number) {

        serial_number_array.push(serial_number);
      }

    });
    data_post.serial_number_array = serial_number_array;


    // get serial number
    $.post(admin_url + 'warehouse/get_serial_number_for_change_modal', data_post).done(function(response) {
      response = JSON.parse(response);
      if (response.status == true || response.status == 'true') {

        open_change_serial_number_modal(response.table_serial_number, name_commodity_name, name_serial_number);
      } else {
        alert_float('warning', "<?php echo _l('wh_dont_have_any_serial_number_for_this_item'); ?>");
      }
    });

  }

  function open_change_serial_number_modal(table_serial_number, name_commodity_name, name_serial_number) {
    "use strict";

    $("#change_serial_modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/load_change_serial_number_modal'); ?>", {
      table_serial_number: table_serial_number,
      name_commodity_name: name_commodity_name,
      name_serial_number: name_serial_number,
    }, function() {
      $("body").find('#changeSerialNumberModal').modal({
        show: true,
        backdrop: 'static'
      });
    });

  }

  // Set the currency for accounting
  function init_goods_delivery_currency(id, callback) {
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
</script>