<script>
  (function($) {
    "use strict";
    $("input[data-type='currency']").on({
      keyup: function() {

        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });

    var vendor = $('select[name="vendor"]').val();

    pur_calculate_total();

    <?php if (get_purchase_option('item_by_vendor') != 1) { ?>
      init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'purchase/pur_commodity_code_search');
    <?php } ?>

    $("body").on('change', 'select[name="item_select"]', function() {
      var itemid = $(this).selectpicker('val');
      if (itemid != '') {
        pur_add_item_to_preview(itemid);
      }
    });

    $("body").on('change', 'select.taxes', function() {
      pur_calculate_total();
    });

    $("body").on('change', 'select[name="currency"]', function() {

      var currency_id = $(this).val();
      if (currency_id != '') {
        $.post(admin_url + 'purchase/get_currency_rate/' + currency_id).done(function(response) {
          response = JSON.parse(response);
          if (response.currency_rate != 1) {
            $('#currency_rate_div').removeClass('hide');

            $('input[name="currency_rate"]').val(response.currency_rate).change();

            $('#convert_str').html(response.convert_str);
            $('.th_currency').html(response.currency_name);
          } else {
            $('input[name="currency_rate"]').val(response.currency_rate).change();
            $('#currency_rate_div').addClass('hide');
            $('#convert_str').html(response.convert_str);
            $('.th_currency').html(response.currency_name);

          }

        });
      } else {
        alert_float('warning', "<?php echo _l('please_select_currency'); ?>")
      }
      init_pi_currency();
    });

    $("input[name='currency_rate']").on('change', function() {
      var currency_rate = $(this).val();
      var rows = $('.table.has-calculations tbody tr.item');
      $.each(rows, function() {
        var old_price = $(this).find('td.rate input[name="og_price"]').val();
        var new_price = currency_rate * old_price;
        $(this).find('td.rate input[type="number"]').val(accounting.toFixed(new_price, app.options.decimal_places)).change();

      });
    });

    $("body").on("change", 'select[name="discount_type"]', function() {
      // if discount_type == ''
      if ($(this).val() === "") {
        $('input[name="order_discount"]').val(0);
      }
      // Recalculate the total
      pur_calculate_total();
    });

    var clickedButton = null;
    $("body").on("click", "form._transaction_form input[type=submit]", function () {
        clickedButton = $(this).attr("name");
    });

  })(jQuery);

  var lastAddedItemKey = null;

  function formatNumber(n) {
    "use strict";
    // format number 1000000 to 1,234,567
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "<?php echo get_option('thousand_separator'); ?>");
  }

  function formatCurrency(input, blur) {
    "use strict";
    var input_val = input.val();
    if (input_val === "") {
      return;
    }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionStart");
    if (input_val.indexOf("<?php echo get_option('decimal_separator'); ?>") >= 0) {
      var decimal_pos = input_val.indexOf("<?php echo get_option('decimal_separator'); ?>");
      var left_side = input_val.substring(0, decimal_pos);
      var right_side = input_val.substring(decimal_pos);
      left_side = formatNumber(left_side);
      right_side = formatNumber(right_side);
      right_side = right_side.substring(0, 2);
      input_val = left_side + "<?php echo get_option('decimal_separator'); ?>" + right_side;

    } else {
      input_val = formatNumber(input_val);
      input_val = input_val;

    }
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
  }


  function numberWithCommas(x) {
    "use strict";
    x = x.toString().replace('.', "<?php echo get_option('decimal_separator'); ?>");

    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "<?php echo get_option('thousand_separator'); ?>");
  }

  function removeCommas(str) {
    "use strict";
    var decimal_separator = '<?php echo get_option('decimal_separator'); ?>';

    if (decimal_separator == ',') {
      str = str.split('.').join('');
      return (str.replace(',', '.'));
    } else {
      return (str.replace(/,/g, ''));
    }
  }

  function contract_change(el) {
    "use strict";
    if (el.value != '') {
      $.post(admin_url + 'purchase/contract_change/' + el.value).done(function(response) {
        response = JSON.parse(response);
        $('select[name="pur_order"]').val(response.purchase_order).change();
      });
    }
  }

  function pur_order_change(el) {
    "use strict";
    if (el.value != '') {
      $.post(admin_url + 'purchase/pur_order_change/' + el.value).done(function(response) {
        response = JSON.parse(response);
        if (response) {
          $('select[name="currency"]').val(response.currency).change();
          $('input[name="currency_rate"]').val(response.currency_rate).change();
          $('input[name="shipping_fee"]').val(response.shipping_fee);
          $('input[name="order_discount"]').val(response.order_discount);
          $('select[name="add_discount_type"]').val('amount');

          $('select[name="discount_type"]').val(response.discount_type).change();

          $('.invoice-item table.invoice-items-table.items tbody').html('');
          $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

          $('input[name="discount_percent"]').val(response.discount_percent).change();

          setTimeout(function() {
            pur_calculate_total();
          }, 15);

          init_selectpicker();
          pur_reorder_items('.invoice-item');
          pur_clear_item_preview_values('.invoice-item');
          $('body').find('#items-warning').remove();
          $("body").find('.dt-loader').remove();
          $('#item_select').selectpicker('val', '');
        }
      });

      $('#recurring_div').addClass('hide');
      $('select[name="recurring"]').val(0).change();
    } else {
      $('#recurring_div').removeClass('hide');
      $('select[name="recurring"]').val(0).change();
    }
  }

  function pur_vendor_change(el) {
    "use strict";
    if (el.value != '') {
      $.post(admin_url + 'purchase/pur_vendors_change/' + el.value).done(function(response) {
        response = JSON.parse(response);
        $('select[name="currency"]').val(response.currency_id).change();

        // $('select[name="pur_order"]').html('');
        // $('select[name="pur_order"]').append(response.po_html);
        // $('select[name="pur_order"]').selectpicker('refresh');

        $('select[name="contract"]').html(response.html);
        $('select[name="contract"]').selectpicker('refresh');

        <?php if (get_purchase_option('item_by_vendor') == 1) { ?>
          if (response.option_html != '') {
            $('#item_select').html(response.option_html);
            $('.selectpicker').selectpicker('refresh');
          } else if (response.option_html == '') {
            init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'purchase/pur_commodity_code_search/purchase_price/can_be_purchased/' + invoker.value);
          }

        <?php } ?>

      });
    }
  }

  function subtotal_change(el) {
    "use strict";
    var tax = $('#tax').val();
    if (tax == '') {
      tax = '0';
    }
    var total_value = parseFloat(removeCommas(el.value)) + parseFloat(removeCommas(tax));
    $('#total').val(numberWithCommas(total_value));
  }

  function tax_rate_change(el) {
    "use strict";
    var subtotal = $('#subtotal').val();
    var tax = $('#tax').val();
    var total = $('#total').val();
    if (el.value != '') {
      $.post(admin_url + 'purchase/tax_rate_change/' + el.value).done(function(response) {
        response = JSON.parse(response);
        var tax_value = parseFloat(removeCommas(subtotal) * response.rate) / 100;
        var total_value = parseFloat(removeCommas(subtotal)) + tax_value;
        $('#tax').val(numberWithCommas(tax_value));
        $('#total').val(numberWithCommas(total_value));
      });
    }
  }

  function pur_calculate_total() {
    "use strict";
    if ($('body').hasClass('no-calculate-total')) {
      return false;
    }
    var total = 0;
    // var rows = $('.all_bill_row_model');
    // if (rows.length > 0) {
    //   $.each(rows, function () {
    //     var row = $(this);
    //     var item_key = parseFloat(row.data('item_key')) || 0;
    //     var unit_price = parseFloat(row.data('unit_price')) || 0;
    //     total += calculate_bill_bifurcation(item_key, unit_price);
    //   });
    // }
    // var rows = $('.all_pc_bill_row_model');
    // if (rows.length > 0) {
    //   $.each(rows, function () {
    //     var row = $(this);
    //     var item_key = parseFloat(row.data('item_key')) || 0;
    //     var unit_price = parseFloat(row.data('unit_price')) || 0;
    //     var pc_id = parseFloat(row.data('pc_id')) || 0;
    //     total += calculate_pc_bill_bifurcation(item_key, unit_price, pc_id);
    //   });
    // }
    var rows = $('.table.has-calculations tbody tr.item');
    $.each(rows, function () {
      var quantity = parseFloat($(this).find('td.quantities input').val()) || 0;
      var rate = parseFloat($(this).find('td.rate input').val()) || 0;
      var item_amount = quantity * rate;
      total += item_amount;
    });
    $('.wh-total').html(
      format_money(total) +
      hidden_input('grand_total', accounting.toFixed(total, app.options.decimal_places))
    );
    return total;
  }

  // Set the currency for accounting
  function init_pi_currency(id, callback) {
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

          pur_calculate_total();

          if (callback) {
            callback();
          }
        });
    }
  }

  function pur_add_item_to_preview(id) {
    "use strict";
    var currency_rate = $('input[name="currency_rate"]').val();
    requestGetJSON('purchase/get_item_by_id/' + id + '/' + currency_rate).done(function(response) {
      clear_item_preview_values();

      $('.main input[name="item_code"]').val(response.itemid);
      $('.main textarea[name="item_name"]').val(response.code_description);
      $('.main textarea[name="description"]').val(response.long_description);
      $('.main input[name="unit_price"]').val(response.purchase_price);
      $('.main input[name="unit_name"]').val(response.unit_name);
      $('.main input[name="unit_id"]').val(response.unit_id);
      $('.main input[name="quantity"]').val(1);

      $('.selectpicker').selectpicker('refresh');


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
        $rateInputPreview.val(response.purchase_price);
      } else {
        var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
        if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
          $rateInputPreview.val(response.purchase_price);
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

  function pur_add_item_to_table(data, itemid) {
    "use strict";

    data = typeof(data) == 'undefined' || data == 'undefined' ? pur_get_item_preview_values() : data;

    if (data.quantity == "") {

      return;
    }
    var currency_rate = $('input[name="currency_rate"]').val();
    var to_currency = $('select[name="currency"]').val();
    var table_row = '';
    var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
    lastAddedItemKey = item_key;
    $("body").append('<div class="dt-loader"></div>');
    pur_get_item_row_template('newitems[' + item_key + ']', data.item_name, data.description, data.quantity, data.unit_name, data.unit_price, data.taxname, data.item_code, data.unit_id, data.tax_rate, data.discount, itemid, currency_rate, to_currency).done(function(output) {
      table_row += output;

      $('.invoice-item table.invoice-items-table.items tbody').append(table_row);

      setTimeout(function() {
        pur_calculate_total();
      }, 15);
      init_selectpicker();
      pur_reorder_items('.invoice-item');
      pur_clear_item_preview_values('.invoice-item');
      $('body').find('#items-warning').remove();
      $("body").find('.dt-loader').remove();
      $('#item_select').selectpicker('val', '');

      return true;
    });
    return false;
  }

  function pur_get_item_preview_values() {
    "use strict";

    var response = {};
    response.item_name = $('.invoice-item .main textarea[name="item_name"]').val();
    response.description = $('.invoice-item .main textarea[name="description"]').val();
    response.quantity = $('.invoice-item .main input[name="quantity"]').val();
    response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
    response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
    response.taxname = $('.main select.taxes').selectpicker('val');
    response.item_code = $('.invoice-item .main input[name="item_code"]').val();
    response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
    response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
    response.discount = $('.invoice-item .main input[name="discount"]').val();


    return response;
  }


  function pur_clear_item_preview_values(parent) {
    "use strict";

    var previewArea = $(parent + ' .main');
    previewArea.find('input').val('');
    previewArea.find('textarea').val('');
    previewArea.find('select').val('').selectpicker('refresh');
  }

  function pur_reorder_items(parent) {
    "use strict";

    var rows = $(parent + ' .table.has-calculations tbody tr.item');
    var i = 1;
    $.each(rows, function() {
      $(this).find('input.order').val(i);
      i++;
    });
  }

  function pur_delete_item(row, itemid, parent) {
    "use strict";

    $(row).parents('tr').addClass('animated fadeOut', function() {
      setTimeout(function() {
        $(row).parents('tr').remove();
        pur_calculate_total();
      }, 50);
    });
    if (itemid && $('input[name="isedit"]').length > 0) {
      $(parent + ' #removed-items').append(hidden_input('removed_items[]', itemid));
    }
  }

  function pur_get_item_row_template(name, item_name, description, quantity, unit_name, unit_price, taxname, item_code, unit_id, tax_rate, discount, item_key, currency_rate, to_currency) {
    "use strict";

    jQuery.ajaxSetup({
      async: false
    });

    var d = $.post(admin_url + 'purchase/get_purchase_invoice_row_template', {
      name: name,
      item_name: item_name,
      item_description: description,
      quantity: quantity,
      unit_name: unit_name,
      unit_price: unit_price,
      taxname: taxname,
      item_code: item_code,
      unit_id: unit_id,
      tax_rate: tax_rate,
      discount: discount,
      item_key: item_key,
      currency_rate: currency_rate,
      to_currency: to_currency
    });
    jQuery.ajaxSetup({
      async: true
    });
    return d;
  }

  function add_bill_bifurcation(id, unit_price) {
    calculate_bill_bifurcation(id, unit_price);
    $('#bill_modal_'+id).modal('show');
  }

  function add_pc_bill_bifurcation(id, unit_price, pc_id) {
    calculate_pc_bill_bifurcation(id, unit_price, pc_id);
    $('#pc_bill_modal_'+id+'_'+pc_id).modal('show');
  }

  function calculate_bill_bifurcation(id, unit_price) {
    var total_bill_unit_price = 0;
    var total_bill_percentage = 0;
    var total_hold_percentage = 0;
    var total_billed_amount = 0;
    var rows = $('#bill_modal_' + id + ' table tbody .bill_items');
    $.each(rows, function () {
      var row = $(this);
      var bill_percentage = parseFloat(row.find(".all_bill_percentage input").val()) || 0;
      var bill_hold = parseFloat(row.find(".all_bill_hold input").val()) || 0;
      var bill_billed_quantity = parseFloat(row.find(".all_bill_billed_quantity input").val()) || 0;
      var bill_unit_price = 0;
      if (bill_percentage > 0) {
        bill_unit_price = (unit_price * bill_percentage) / 100;
      }
      total_bill_unit_price += bill_unit_price;
      row.find(".all_bill_unit_price").html(format_money(bill_unit_price));
      total_bill_percentage += bill_percentage;
      total_hold_percentage += bill_hold;
      var bill_hold_percentage = bill_percentage - bill_hold;
      var billed_amount = 0;
      if (bill_hold_percentage > 0) {
        billed_amount = bill_billed_quantity * ((unit_price * bill_hold_percentage) / 100);
      }
      row.find(".all_bill_billed_amount").html(format_money(billed_amount));
      total_billed_amount += billed_amount; 
    });
    $('#bill_modal_' + id + ' .total_bill_unit_price').html(format_money(total_bill_unit_price));
    $('#bill_modal_' + id + ' .total_bill_percentage').html(total_bill_percentage.toFixed(2)+'%');
    $('#bill_modal_' + id + ' .total_hold_percentage').html(total_hold_percentage.toFixed(2)+'%');
    $('#bill_modal_' + id + ' .total_billed_amount').html(format_money(total_billed_amount));
    return total_billed_amount;
  }

  function calculate_pc_bill_bifurcation(id, unit_price, pc_id) {
    var total_pc_bill_unit_price = 0;
    var total_pc_bill_percentage = 0;
    var total_pc_hold_percentage = 0;
    var total_pc_billed_amount = 0;
    var rows = $('#pc_bill_modal_' + id + '_' + pc_id + ' table tbody .pc_bill_items');
    $.each(rows, function () {
      var row = $(this);
      var pc_bill_percentage = parseFloat(row.find(".all_pc_bill_percentage input").val()) || 0;
      var pc_bill_hold = parseFloat(row.find(".all_pc_bill_hold input").val()) || 0;
      var pc_bill_billed_quantity = parseFloat(row.find(".all_pc_bill_billed_quantity input").val()) || 0;
      var pc_amount_to_be_used = parseFloat(row.find(".all_pc_amount_to_be_used input").val()) || 0;
      var pc_remaining_amount = 0;
      var pc_bill_unit_price = 0;
      if (pc_bill_percentage > 0) {
        pc_bill_unit_price = (unit_price * pc_bill_percentage) / 100;
      }
      total_pc_bill_unit_price += pc_bill_unit_price;
      row.find(".all_pc_bill_unit_price").html(format_money(pc_bill_unit_price));
      total_pc_bill_percentage += pc_bill_percentage;
      total_pc_hold_percentage += pc_bill_hold;
      var pc_bill_hold_percentage = pc_bill_percentage - pc_bill_hold;
      var pc_billed_amount = 0;
      if (pc_bill_hold_percentage > 0) {
        pc_billed_amount = pc_bill_billed_quantity * ((unit_price * pc_bill_hold_percentage) / 100);
      }
      row.find(".all_pc_bill_billed_amount").html(format_money(pc_billed_amount));
      total_pc_billed_amount += pc_billed_amount; 
      pc_remaining_amount = pc_amount_to_be_used - pc_billed_amount;
      row.find(".all_pc_bill_remaining_amount").html(format_money(pc_remaining_amount));
    });
    $('#pc_bill_modal_' + id + '_' + pc_id + ' .total_pc_bill_unit_price').html(format_money(total_pc_bill_unit_price));
    $('#pc_bill_modal_' + id + '_' + pc_id + ' .total_pc_bill_percentage').html(total_pc_bill_percentage.toFixed(2)+'%');
    $('#pc_bill_modal_' + id + '_' + pc_id + ' .total_pc_hold_percentage').html(total_pc_hold_percentage.toFixed(2)+'%');
    $('#pc_bill_modal_' + id + '_' + pc_id + ' .total_pc_billed_amount').html(format_money(total_pc_billed_amount));
    return total_pc_billed_amount;
  }

  function save_bill_row_model(id) {
    var item_bill_hold_percentage = 0;
    var rows = $('#bill_modal_' + id + ' table tbody .bill_items');
    $.each(rows, function () {
      var row = $(this);
      var bill_percentage = parseFloat(row.find(".all_bill_percentage input").val()) || 0;
      var bill_hold = parseFloat(row.find(".all_bill_hold input").val()) || 0;
      var bill_hold_percentage = bill_percentage - bill_hold;
      item_bill_hold_percentage += bill_hold_percentage;
    });
    if (item_bill_hold_percentage > 100) {
      alert_float('warning', "The percentages cannot exceed 100.");
      return false;
    } else if (item_bill_hold_percentage < 0) {
      alert_float('warning', "The percentages cannot be negative.");
      return false;
    } else {
      $('#bill_modal_' + id).modal('hide');
    }
    pur_calculate_total();
  }

  function save_pc_bill_row_model(id, pc_id) {
    var item_pc_bill_hold_percentage = 0;
    var rows = $('#pc_bill_modal_' + id + '_' + pc_id + ' table tbody .pc_bill_items');
    $.each(rows, function () {
      var row = $(this);
      var pc_bill_percentage = parseFloat(row.find(".all_pc_bill_percentage input").val()) || 0;
      var pc_bill_hold = parseFloat(row.find(".all_pc_bill_hold input").val()) || 0;
      var pc_bill_hold_percentage = pc_bill_percentage - pc_bill_hold;
      item_pc_bill_hold_percentage += pc_bill_hold_percentage;
    });
    if (item_pc_bill_hold_percentage > 100) {
      alert_float('warning', "The percentages cannot exceed 100.");
      return false;
    } else if (item_pc_bill_hold_percentage < 0) {
      alert_float('warning', "The percentages cannot be negative.");
      return false;
    } else {
      $('#pc_bill_modal_' + id + '_' + pc_id).modal('hide');
    }
    pur_calculate_total();
  }

  function approve_bill_bifurcation_request(id) {
    "use strict";
    bill_bifurcation_request_approval_status(id, 2);
  }

  function deny_bill_bifurcation_request(id) {
    "use strict";
    bill_bifurcation_request_approval_status(id, 3);
  }

  function bill_bifurcation_request_approval_status(id, status) {
    "use strict";
    var data = {};
    data.rel_id = id;
    data.approve = status;
    data.note = $('textarea[name="reason"]').val();
    $.post(admin_url + 'purchase/bill_bifurcation_request/' + id, data).done(function(response) {
      response = JSON.parse(response);
      if (response.success === true || response.success == 'true') {
        alert_float('success', response.message);
        window.location.reload();
      }
    });
  }

  function export_bill_excel(item_key) {
    var modal = $('#bill_modal_' + item_key);
    if (modal.length === 0) {
      alert('Bill modal not found for item ' + item_key);
      return;
    }
    var table = modal.find('.table_bill_rows').clone()[0];
    $(table).find('th.hide, td.hide, tr.hide, [style*="display: none"]').remove();
    $(table).find('input, textarea').each(function() {
      var value = $(this).val();
      var td = $(this).closest('td');
      if (td.length) {
        td.text(value);
      }
    });
    var wb = XLSX.utils.table_to_book(table, { sheet: "Bill Data" });
    var fileName = "bill_data.xlsx";
    XLSX.writeFile(wb, fileName);
  }

  function export_pc_bill_excel(item_key, pc_id) {
    var modal = $('#pc_bill_modal_' + item_key + '_' + pc_id);
    if (modal.length === 0) {
      alert('PC Bill modal not found for item ' + item_key + ', PC ID ' + pc_id);
      return;
    }
    var table = modal.find('.table_pc_bill_rows').clone()[0];
    $(table).find('th.hide, td.hide, tr.hide, [style*="display: none"]').remove();
    $(table).find('input, textarea').each(function() {
      var value = $(this).val();
      var td = $(this).closest('td');
      if (td.length) {
        td.text(value);
      }
    });
    var wb = XLSX.utils.table_to_book(table, { sheet: "PC Bill Data" });
    var fileName = "pc_bill_data.xlsx";
    XLSX.writeFile(wb, fileName);
  }

  function upload_bulk_pur_bill(item_key) {
    "use strict";
    var $modal = $('#bill_modal_' + item_key);
    var unit_price = parseFloat($modal.attr('data-unit_price') || 0) || 0;
    var fileInput = $modal.find('#file_csv')[0];
    var file = fileInput?.files[0];
    if (!file) {
      alert("Please select a file.");
      return;
    }
    var fileExtension = file.name.split('.').pop().toLowerCase();
    if (fileExtension !== 'xlsx') {
      alert("Please upload a valid .xlsx file.");
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
      var data = new Uint8Array(e.target.result);
      var workbook = XLSX.read(data, { type: 'array' });
      var firstSheetName = workbook.SheetNames[0];
      var worksheet = workbook.Sheets[firstSheetName];
      var jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });
      if (jsonData.length === 0) {
        alert("No data found in Excel file.");
        return;
      }
      jsonData.forEach(function (row) {
        var billId = parseInt(row["Bill Id"]);
        if (!billId || isNaN(billId)) return;
        var $tr = $modal.find('.table_bill_rows tbody tr').eq(billId - 1);
        if (!$tr.length) return;
        var $bill_percentage_input = $tr.find('input[name*="[bill_percentage]"]');
        var $hold_input = $tr.find('input[name*="[hold]"]');
        var $qty_input = $tr.find('input[name*="[billed_quantity]"]');
        var bill_percentage = row["Bill Percentage"] ? parseFloat(row["Bill Percentage"]) : 0.00;
        var hold = row["Hold %"] ? parseFloat(row["Hold %"]) : 0.00;
        var qty = row["Qty"] ? parseFloat(row["Qty"]) : 0.00;
        $bill_percentage_input.val(bill_percentage.toFixed(2));
        $hold_input.val(hold.toFixed(2));
        $qty_input.val(qty.toFixed(2));
      });
      calculate_bill_bifurcation(item_key, unit_price);
      alert_float('success', "Bill data updated successfully from Excel!");
    };
    reader.readAsArrayBuffer(file);
  }

  function upload_bulk_pur_pc_bill(item_key, pc_id) {
    "use strict";
    var $modal = $('#pc_bill_modal_' + item_key + '_' + pc_id);
    var unit_price = parseFloat($modal.attr('data-unit_price') || 0) || 0;
    var fileInput = $modal.find('#file_csv')[0];
    var file = fileInput?.files[0];
    if (!file) {
      alert("Please select a file.");
      return;
    }
    var fileExtension = file.name.split('.').pop().toLowerCase();
    if (fileExtension !== 'xlsx') {
      alert("Please upload a valid .xlsx file.");
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
      var data = new Uint8Array(e.target.result);
      var workbook = XLSX.read(data, { type: 'array' });
      var firstSheetName = workbook.SheetNames[0];
      var worksheet = workbook.Sheets[firstSheetName];
      var jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });
      if (jsonData.length === 0) {
        alert("No data found in Excel file.");
        return;
      }
      jsonData.forEach(function (row) {
        var billId = parseInt(row["Bill Id"]);
        if (!billId || isNaN(billId)) return;
        var $tr = $modal.find('.table_pc_bill_rows tbody tr').eq(billId - 1);
        if (!$tr.length) return;
        var $hold_input = $tr.find('input[name*="[hold]"]');
        var $qty_input = $tr.find('input[name*="[billed_quantity]"]');
        var hold = row["Hold %"] ? parseFloat(row["Hold %"]) : 0.00;
        var qty = row["Qty"] ? parseFloat(row["Qty"]) : 0.00;
        $hold_input.val(hold.toFixed(2));
        $qty_input.val(qty.toFixed(2));
      });
      calculate_pc_bill_bifurcation(item_key, unit_price, pc_id);
      alert_float('success', "PC Bill data updated successfully from Excel!");
    };
    reader.readAsArrayBuffer(file);
  }

  function change_status_pur_bill(invoker, id) {
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
        $.post(admin_url + 'purchase/change_status_pur_bill/' + invoker.value + '/' + id)
        .done(function(response) {
            response = JSON.parse(response);
            alert_float('success', response.result);
            window.location.reload();
        });
      }
    });
  }
</script>