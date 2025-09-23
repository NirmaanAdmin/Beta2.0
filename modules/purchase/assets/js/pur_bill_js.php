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

    $("body").on('submit', 'form._transaction_form', function (e) {
      e.preventDefault();
      var form = $(this);
      var is_submit = true;
      $('.all_bill_row_model').each(function () {
        var modal = $(this);
        var total_bill_percentage = 0;
        modal.find("tbody .bill_items").each(function () {
          var row = $(this);
          var bill_percentage = parseFloat(row.find(".all_bill_percentage input").val()) || 0;
          total_bill_percentage += bill_percentage;
        });
        if (total_bill_percentage > 100) {
          is_submit = false;
          alert_float(
            'warning',
            "The percentages cannot exceed 100 in modal"
          );
          form.find('button.transaction-submit:disabled').prop('disabled', false);
          return false;
        }
        if (total_bill_percentage < 0) {
          is_submit = false;
          alert_float(
            'warning',
            "The percentages cannot be negative in modal"
          );
          form.find('button.transaction-submit:disabled').prop('disabled', false);
          return false;
        }
      });
      var grand_total = pur_calculate_total();
      var payment_certificate_total = parseFloat($("input[name='payment_certificate_total']").val()) || 0;
      if(grand_total > payment_certificate_total) {
        is_submit = false;
        alert_float(
          'warning',
          "The grand total should not be greater than the payment certificate total."
        );
        form.find('button.transaction-submit:disabled').prop('disabled', false);
        return false;
      }
      if (is_submit) {
        if (clickedButton) {
          form.append('<input type="hidden" name="' + clickedButton + '" value="1">');
        }
        form.off('submit');
        form[0].submit();
      }
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
    var rows = $('.table.has-calculations tbody tr.item');
    $.each(rows, function() {
      var row = $(this);
      var bill_amount = 0;
      var hold_amount = 0;
      var final_amount = 0;
      var bill_percentage = parseFloat(row.find("td.row_bill_percentage input").val()) || 0;
      var hold_percentage = parseFloat(row.find('td.hold input').val()) || 0;
      var billed_quantity = parseFloat(row.find('td.billed_quantity input[type="number"]').val()) || 0;
      var rate = parseFloat(row.find('td.rate input').val()) || 0;
      var amount = billed_quantity * rate;
      if (bill_percentage > 0) {
        bill_amount = (amount * bill_percentage) / 100;
      }
      if (hold_percentage > 0) {
        hold_amount = (amount * hold_percentage) / 100;
      }
      final_amount = bill_amount - hold_amount;
      row.find("td.hold_amount").html(format_money(hold_amount));
      row.find("td.label_row_total").html(format_money(final_amount));
      row.find("td.row_total input").val(final_amount);
      total += final_amount;
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

  function calculate_bill_bifurcation(id, unit_price) {
    var total_bill_unit_price = 0;
    var total_bill_percentage = 0;
    var rows = $('#bill_modal_' + id + ' table tbody .bill_items');
    $.each(rows, function () {
      var row = $(this);
      var bill_percentage = parseFloat(row.find(".all_bill_percentage input").val()) || 0;
      var bill_unit_price = 0;
      if (bill_percentage > 0) {
        bill_unit_price = (unit_price * bill_percentage) / 100;
      }
      total_bill_unit_price += bill_unit_price;
      row.find(".all_bill_unit_price").html(format_money(bill_unit_price));
      total_bill_percentage += bill_percentage;
    });
    $('#bill_modal_' + id + ' .total_bill_unit_price').html(format_money(total_bill_unit_price));
    $('#bill_modal_' + id + ' .total_bill_percentage').html(total_bill_percentage.toFixed(2)+'%');
    $('#bill_modal_' + id + ' input[name="final_percentage"]').val(total_bill_percentage.toFixed(2));
  }

  function save_bill_row_model(id) {
    var final_percentage = parseFloat($('#bill_modal_' + id + ' input[name="final_percentage"]').val()) || 0;
    if (final_percentage > 100) {
      alert_float('warning', "The percentages cannot exceed 100.");
      return false;
    } else if (final_percentage < 0) {
      alert_float('warning', "The percentages cannot be negative.");
      return false;
    } else {
      $('.list_item_' + id + ' .label_row_bill_percentage').html(final_percentage.toFixed(2)+'%');
      $('.list_item_' + id + ' .row_bill_percentage input').val(final_percentage.toFixed(2));
      $('#bill_modal_' + id).modal('hide');
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
</script>