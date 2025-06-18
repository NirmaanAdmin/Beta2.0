<script>
  $(function() {
    "use strict";

    init_ajax_search("customer", ".client-ajax-search");
    init_po_currency();
    // Maybe items ajax search
    <?php if (get_purchase_option('item_by_vendor') != 1) { ?>
      init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'purchase/pur_commodity_code_search');
    <?php } ?>

    pur_calculate_total();

    validate_purorder_form();

    function validate_purorder_form(selector) {

      selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;

      appValidateForm($(selector), {
        pur_order_name: 'required',
        pur_order_number: 'required',
        order_date: 'required',
        vendor: 'required',
        project: 'required',
        group_pur: 'required',

      });
    }

    $("body").on('change', 'select[name="item_select"]', function() {
      var itemid = $(this).selectpicker('val');
      if (itemid != '') {
        // pur_add_item_to_preview(itemid);
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
      init_po_currency();
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

    $("body").on('change', 'select[name="vendor"]', function() {
      var vendorid = $(this).selectpicker('val');
      $.post(admin_url + 'purchase/get_vendor_detail/' + vendorid).done(function(response) {
        response = JSON.parse(response);
        setTimeout(function() {
          var editor = tinymce.get('order_summary');
          if (editor) {
            var currentContent = editor.getContent();

            if (response.pur_vendor.company) {
              currentContent = currentContent.replace(
                /<span class="vendor_name">.*?<\/span>/g,
                '<span class="vendor_name">' + response.pur_vendor.company + '</span>'
              );
            }

            if (response.pur_vendor.address) {
              currentContent = currentContent.replace(
                /<span class="vendor_address">.*?<\/span>/g,
                '<span class="vendor_address">' + response.pur_vendor.address + '</span>'
              );
            }

            if (response.pur_vendor.city) {
              currentContent = currentContent.replace(
                /<span class="vendor_city">.*?<\/span>/g,
                '<span class="vendor_city">' + response.pur_vendor.city + '</span> '
              );
            }

            if (response.pur_vendor.state) {
              currentContent = currentContent.replace(
                /<span class="vendor_state">.*?<\/span>/g,
                '<span class="vendor_state">' + response.pur_vendor.state + '</span> '
              );
            }

            if (response.pur_vendor.vat) {
              currentContent = currentContent.replace(
                /<span class="vendor_gst">.*?<\/span>/g,
                '<span class="vendor_gst">' + response.pur_vendor.vat + '</span>'
              );
            }
            if (response.pur_vendor.pan_number) {
              currentContent = currentContent.replace(
                /<span class="pan_number">.*?<\/span>/g,
                '<span class="pan_number">' + response.pur_vendor.pan_number + '</span>'
              );
            }


            if (response.pur_vendor.bank_detail) {
              var formattedBankDetails = response.pur_vendor.bank_detail.replace(/\n/g, '<br>');
              currentContent = currentContent.replace(
                /<span class="vendor_bank_details">.*?<\/span>/g,
                '<span class="vendor_bank_details">' + formattedBankDetails + '</span>'
              );
            }

            if (response.pur_contacts) {
              // 1) Normalize your first/last names into safe strings
              var first = response.pur_contacts.firstname || response.pur_contacts.first_name || '';
              var last = response.pur_contacts.lastname || response.pur_contacts.last_name || '';

              // 2) Trim off any extra whitespace
              var fullName = (first + ' ' + last).trim();

              // 3) Only replace if there is actually something to show
              if (fullName) {
                currentContent = currentContent.replace(
                  /<span class="vendor_contact">.*?<\/span>/g,
                  '<span class="vendor_contact">' + fullName + '</span>'
                );
              }
            }


            if (response.pur_vendor) {
              if (response.pur_vendor.phonenumber)
                currentContent = currentContent.replace(
                  /<span class="vendor_contact_phone">.*?<\/span>/g,
                  '<span class="vendor_contact_phone">' + response.pur_vendor.phonenumber + '</span>'
                );
            }

            if (response.pur_vendor) {
              if (response.pur_vendor.com_email)
                currentContent = currentContent.replace(
                  /<span class="vendor_contact_email">.*?<\/span>/g,
                  '<span class="vendor_contact_email">' + response.pur_vendor.com_email + '</span>'
                );
            }

            editor.setContent(currentContent);
          } else {
            console.error("TinyMCE is not initialized yet.");
          }
        }, 500);
      });
    });

    $("body").on('change', 'select[name="project"]', function() {
      var project_name = $('select[name="project"] option:selected').text();
      setTimeout(function() {
        var editor = tinymce.get('order_summary');
        if (editor) {
          var currentContent = editor.getContent();
          if (project_name) {
            currentContent = currentContent.replace(
              /<span class="project_name">.*?<\/span>/g,
              '<span class="project_name">' + project_name + '</span>'
            );
          }
          editor.setContent(currentContent);
        } else {
          console.error("TinyMCE is not initialized yet.");
        }
      }, 500);
    });

    get_project_areas();

    $("body").on('change', 'select[name="project"]', function() {
      get_project_areas();
    });

    $("body").on('change', 'input[name="pur_order_name"]', function() {
      var pur_order_name = $(this).val();
      setTimeout(function() {
        var editor = tinymce.get('order_summary');
        if (editor) {
          var currentContent = editor.getContent();
          if (pur_order_name) {
            currentContent = currentContent.replace(
              /<span class="pur_order_name">.*?<\/span>/g,
              '<span class="pur_order_name">' + pur_order_name + '</span>'
            );
          }
          editor.setContent(currentContent);
        } else {
          console.error("TinyMCE is not initialized yet.");
        }
      }, 500);
    });

    function get_order_date(order_date) {
      var [day, month, year] = order_date.split('-');
      const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ];

      function getOrdinalSuffix(day) {
        if (day > 3 && day < 21) return day + "th"; // Special case for 11-20
        switch (day % 10) {
          case 1:
            return day + "st";
          case 2:
            return day + "nd";
          case 3:
            return day + "rd";
          default:
            return day + "th";
        }
      }
      var order_date_updated = `${getOrdinalSuffix(parseInt(day))} ${monthNames[parseInt(month) - 1]} ${year}`;

      var [day, month, year] = order_date.split('-'); // Extract day, month, year
      var monthAbbr = [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
      ];
      day = day.padStart(2, '0');
      var formattedDate = `${day}-${monthAbbr[parseInt(month) - 1]}-${year.slice(-2)}`;

      setTimeout(function() {
        var editor = tinymce.get('order_summary');
        if (editor) {
          var currentContent = editor.getContent();
          if (order_date_updated) {
            currentContent = currentContent.replace(
              /<span class="order_date">.*?<\/span>/g,
              '<span class="order_date">' + order_date_updated + '</span>'
            );
          }
          if (formattedDate) {
            currentContent = currentContent.replace(
              /<span class="order_full_date">.*?<\/span>/g,
              '<span class="order_full_date">' + formattedDate + '</span>'
            );
          }
          editor.setContent(currentContent);
        } else {
          console.error("TinyMCE is not initialized yet.");
        }
      }, 500);
    }

    $("body").on('change', 'input[name="order_date"]', function() {
      var order_date = $(this).val();
      get_order_date(order_date);
    });

    toggleCostDownloadButtons();

    $("body").on('change', 'select[name="estimate"], select[name="group_pur"]', function() {
      toggleCostDownloadButtons();
    });

    function toggleCostDownloadButtons() {
      var estimate = $('select[name="estimate"]').val();
      var budgetHead = $('select[name="group_pur"]').val();

      if (estimate && budgetHead) {
        $('.cost_complete_sheet').show();
        $('select[name="item_name"]').prop('disabled', true);
      } else {
        $('.cost_complete_sheet').hide();
        $('select[name="item_name"]').prop('disabled', false);
      }
      $('select[name="item_name"]').selectpicker('refresh');
    }

    $("body").on('click', '#cost_control_sheet', function () {
      cost_control_sheet();
    });

    $("body").on('change', 'select[name="cost_sub_head"]', function () {
      cost_control_sheet();
    });

    function cost_control_sheet() {
      var estimate_id = $('select[name="estimate"]').val();
      var budget_head_id = $('select[name="group_pur"]').val();
      var cost_sub_head = $('select[name="cost_sub_head"]').val();
      if (estimate_id !== '' && budget_head_id !== '') {
        $.post(admin_url + 'purchase/get_cost_control_sheet', {
          estimate_id: estimate_id,
          budget_head_id: budget_head_id,
          cost_sub_head: cost_sub_head,
          module: 'pur_orders'
        }).done(function (response) {
          response = JSON.parse(response);
          if (response.result) {
            $('.view_cost_control_sheet').html('');
            $('.view_cost_control_sheet').html(response.result);
            $('#cost_complete_modal').modal('show');
          } else {
            $('.view_cost_control_sheet').html('');
            alert_float('warning', "No any items have found");
          }
        });
      }
    }

    $("body").on('click', '#download_historical_data', function() {
      var estimate_id = $('select[name="estimate"]').val();
      var budget_head_id = $('select[name="group_pur"]').val();
      if (estimate_id !== '' && budget_head_id !== '') {
        var url = admin_url + 'purchase/download_revision_historical_data?estimate_id='+encodeURIComponent(estimate_id)+'&budget_head_id=' + encodeURIComponent(budget_head_id);
        window.location.href = url;
      }
    });

    $("body").on('click', '.cost_fetch_pur_item', function() {
      var itemableid = $(this).data('itemableid');
      var package_id = $(this).data('package');
      if (package_id) {
        $.post(admin_url + 'purchase/cost_fetch_pur_item', {
          package_id: package_id,
          itemableid: itemableid,
        }).done(function (res) {
          var response = JSON.parse(res);
          if (response.result && Array.isArray(response.result) && response.result.length > 0) {
            var item = response.result[0];
            var itemData = {
              area: [],
              description: item.long_description,
              discount: undefined,
              item_code: item.item_code,
              item_name: undefined,
              quantity: item.package_qty,
              sub_groups_pur: item.sub_head,
              tax_rate: undefined,
              taxname: [],
              unit_id: item.unit_id,
              unit_name: item.unit_id,
              unit_price: item.package_rate
            };
            pur_add_item_to_table(itemData, 'undefined', '0');
            alert_float('success', "Cost item has been added to the table");
          } else {
            alert_float('warning', "No item found for the selected package.");
          }
        });
      }
    });

    $("body").on('click', '.enable_item_select', function() {
      $('select[name="item_name"]').prop('disabled', false);
      $('select[name="item_name"]').selectpicker('refresh');
      if ($('select[name="item_name"]').prop('disabled')) {
        $('button[onclick*="pur_add_item_to_table"]').attr('onclick', "pur_add_item_to_table('undefined','undefined','0'); return false;");
      } else {
        $('button[onclick*="pur_add_item_to_table"]').attr('onclick', "pur_add_item_to_table('undefined','undefined','1'); return false;");
      }
    });

  });

  var lastAddedItemKey = null;

  function estimate_by_vendor(invoker) {
    "use strict";
    var po_number = '<?php echo pur_html_entity_decode($pur_order_number); ?>';
    if (invoker.value != 0) {
      $.post(admin_url + 'purchase/estimate_by_vendor/' + invoker.value).done(function(response) {
        response = JSON.parse(response);
        // $('select[name="estimate"]').html('');
        // $('select[name="estimate"]').append(response.result);
        // $('select[name="estimate"]').selectpicker('refresh');
        $('#vendor_data').html('');
        $('#vendor_data').append(response.ven_html);
        $('select[name="currency"]').val(response.currency_id).change();

        <?php if (get_option('po_only_prefix_and_number') != 1) { ?>
          $('input[name="pur_order_number"]').val(po_number + '-' + response.company);
        <?php } ?>
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

  function coppy_pur_request() {
    "use strict";
    var pur_request = $('select[name="pur_request"]').val();
    var vendor = $('select[name="vendor"]').val();
    if (pur_request != '') {
      $.post(admin_url + 'purchase/coppy_pur_request_for_po/' + pur_request + '/' + vendor).done(function(response) {
        response = JSON.parse(response);
        if (response) {
          // $('select[name="estimate"]').html(response.estimate_html);
          // $('select[name="estimate"]').selectpicker('refresh');

          $('select[name="currency"]').val(response.currency).change();
          $('input[name="currency_rate"]').val(response.currency_rate).change();

          $('.invoice-item table.invoice-items-table.items tbody').html('');
          $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

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
    }
  }


  function client_change(el) {
    "use strict";

    var client = $(el).val();
    var data = {};
    data.client = client;

    $.post(admin_url + 'purchase/inv_by_client', data).done(function(response) {
      response = JSON.parse(response);
      $('select[name="sale_invoice"]').html(response.html);
      $('select[name="sale_invoice"]').selectpicker('refresh');
    });

  }

  /**
   * { coppy sale invoice }
   */
  function coppy_sale_invoice() {
    "use strict";
    var sale_invoice = $('select[name="sale_invoice"]').val();

    if (sale_invoice != '') {
      $.post(admin_url + 'purchase/coppy_sale_invoice_po/' + sale_invoice).done(function(response) {
        response = JSON.parse(response);

        if (response) {
          $('select[name="currency"]').val(response.currency).change();
          $('input[name="currency_rate"]').val(response.currency_rate).change();

          $('select[name="discount_type"]').val(response.discount_type).change();
          $('input[name="order_discount"]').val(response.discount_total).change();

          $('.invoice-item table.invoice-items-table.items tbody').html('');
          $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

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
    } else {
      alert_float('warning', '<?php echo _l('please_chose_sale_invoice'); ?>');
    }

  }

  function pur_calculate_total(from_discount_money) {
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
      add_discount_type = $('select[name="add_discount_type"]').val();

    var shipping_fee = $('input[name="shipping_fee"]').val();
    if (shipping_fee == '') {
      shipping_fee = 0;
      $('input[name="shipping_fee"]').val(0);
    }

    $('.wh-tax-area').remove();

    $.each(rows, function() {
      var item_discount = 0;
      var item_discount_money = 0;
      var item_discount_from_percent = 0;
      var item_discount_percent = 0;
      var item_tax = 0,
        item_amount = 0;

      quantity = $(this).find('[data-quantity]').val();
      if (quantity === '') {
        quantity = 1;
        $(this).find('[data-quantity]').val(1);
      }
      item_discount_percent = $(this).find('td.discount input').val();
      item_discount_money = $(this).find('td.discount_money input').val();

      if (isNaN(item_discount_percent) || item_discount_percent == '') {
        item_discount_percent = 0;
      }

      if (isNaN(item_discount_money) || item_discount_money == '') {
        item_discount_money = 0;
      }

      if (from_discount_money == 1 && item_discount_money > 0) {
        $(this).find('td.discount input').val('');
      }

      _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
      item_amount = _amount;
      _amount = parseFloat(_amount);

      $(this).find('td.into_money').html(format_money(_amount));
      $(this).find('td._into_money input').val(_amount);

      subtotal += _amount;
      row = $(this);
      item_taxes = $(this).find('select.taxes').val();

      if (discount_type == 'after_tax') {
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
      }

      //Discount of item
      if (item_discount_percent > 0 && from_discount_money != 1) {

        if (discount_type == 'after_tax') {
          item_discount_from_percent = (parseFloat(item_amount) + parseFloat(item_tax)) * parseFloat(item_discount_percent) / 100;
        } else if (discount_type == 'before_tax') {
          item_discount_from_percent = parseFloat(item_amount) * parseFloat(item_discount_percent) / 100;
        }

        if (item_discount_from_percent != item_discount_money) {
          item_discount_money = item_discount_from_percent;
        }
      }

      if (item_discount_money > 0) {
        item_discount = parseFloat(item_discount_money);
      }


      // Append value to item
      total_discount_calculated += parseFloat(item_discount);
      $(this).find('td.discount_money input').val(item_discount);


      if (discount_type == 'before_tax') {
        if (item_taxes) {
          var after_dc_amount = _amount - parseFloat(item_discount);
          $.each(item_taxes, function(i, taxname) {
            taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
            calculated_tax = (after_dc_amount / 100 * taxrate);
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
      }

      var after_tax = _amount + item_tax;
      var before_tax = _amount;

      item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);

      $(this).find('td.total_after_discount input').val(item_total_payment);

      $(this).find('td.label_total_after_discount').html(format_money(item_total_payment));

      $(this).find('td._total').html(format_money(after_tax));
      $(this).find('td._total_after_tax input').val(after_tax);

      $(this).find('td.tax_value input').val(item_tax);

    });

    var order_discount_percent = $('input[name="order_discount"]').val();
    var order_discount_percent_val = 0;
    // Discount by percent
    if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'percent') {
      total_discount_calculated += parseFloat((subtotal * order_discount_percent) / 100);
      order_discount_percent_val = (subtotal * order_discount_percent) / 100;
    } else if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'amount') {
      total_discount_calculated += parseFloat(order_discount_percent);
      order_discount_percent_val = order_discount_percent;
    }

    $.each(taxes, function(taxname, total_tax) {
      if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'percent') {
        var total_tax_calculated = (total_tax * order_discount_percent) / 100;
        total_tax = (total_tax - total_tax_calculated);
      } else if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'amount') {
        var t = (order_discount_percent / subtotal) * 100;
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

    if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'after_tax' && add_discount_type == 'percent') {
      total_discount_calculated += parseFloat((total * order_discount_percent) / 100);
      order_discount_percent_val = (total * order_discount_percent) / 100;
    } else if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'after_tax' && add_discount_type == 'amount') {
      total_discount_calculated += parseFloat(order_discount_percent);
      order_discount_percent_val = order_discount_percent;
    }


    //total_discount_calculated = total_discount_calculated;

    total = parseFloat(total) - parseFloat(total_discount_calculated) - parseFloat(additional_discount);
    adjustment = parseFloat(adjustment);

    // Check if adjustment not empty
    if (!isNaN(adjustment)) {
      total = total + adjustment;
    }

    total += parseFloat(shipping_fee);

    var discount_html = '-' + format_money(parseFloat(total_discount_calculated) + parseFloat(additional_discount));
    $('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));

    // Append, format to html and display
    $('.shiping_fee').html(format_money(shipping_fee));
    $('.order_discount_value').html(format_money(order_discount_percent_val));
    $('.wh-total_discount').html(discount_html + hidden_input('dc_total', accounting.toFixed(order_discount_percent_val, app.options.decimal_places)));
    $('.adjustment').html(format_money(adjustment));
    $('.wh-subtotal').html(format_money(subtotal) + hidden_input('total_mn', accounting.toFixed(subtotal, app.options.decimal_places)));
    $('.wh-total').html(format_money(total) + hidden_input('grand_total', accounting.toFixed(total, app.options.decimal_places)));
    subtotal_value_order_detail(subtotal);
    subtotal_amount_order_detail(subtotal);

    $(document).trigger('purchase-quotation-total-calculated');

  }

  function subtotal_value_order_detail(subtotal) {
    var subtotal = Math.round(subtotal);
    setTimeout(function() {
      var editor = tinymce.get('order_summary');
      if (editor && editor.initialized) {
        var currentContent = editor.getContent();
        if (subtotal) {
          currentContent = currentContent.replace(
            /<span class="subtotal_in_value">.*?<\/span>/g,
            '<span class="subtotal_in_value">' + subtotal + '</span>'
          );
        }
        editor.setContent(currentContent);
      } else {
        setTimeout(function() {
          editor = tinymce.get('order_summary');
          if (editor && editor.initialized) {
            var currentContent = editor.getContent();
            if (subtotal) {
              currentContent = currentContent.replace(
                /<span class="subtotal_in_value">.*?<\/span>/g,
                '<span class="subtotal_in_value">' + subtotal + '</span>'
              );
            }
            editor.setContent(currentContent);
          }
        }, 1000);
      }
    }, 0);
  }

  function subtotal_amount_order_detail(total) {
    var total = Math.round(total);
    var total_word = numberToWords(total);
    setTimeout(function() {
      var editor = tinymce.get('order_summary');
      if (editor && editor.initialized) {
        var currentContent = editor.getContent();
        if (total_word) {
          currentContent = currentContent.replace(
            /<span class="subtotal_in_words">.*?<\/span>/g,
            '<span class="subtotal_in_words">' + total_word + '</span>'
          );
        }
        editor.setContent(currentContent);
      } else {
        setTimeout(function() {
          editor = tinymce.get('order_summary');
          if (editor && editor.initialized) {
            var currentContent = editor.getContent();
            if (total_word) {
              currentContent = currentContent.replace(
                /<span class="subtotal_in_words">.*?<\/span>/g,
                '<span class="subtotal_in_words">' + total_word + '</span>'
              );
            }
            editor.setContent(currentContent);
          }
        }, 1000);
      }
    }, 0);
  }

  function numberToWords(amount) {
    var words = {
      0: '',
      1: 'one',
      2: 'two',
      3: 'three',
      4: 'four',
      5: 'five',
      6: 'six',
      7: 'seven',
      8: 'eight',
      9: 'nine',
      10: 'ten',
      11: 'eleven',
      12: 'twelve',
      13: 'thirteen',
      14: 'fourteen',
      15: 'fifteen',
      16: 'sixteen',
      17: 'seventeen',
      18: 'eighteen',
      19: 'nineteen',
      20: 'twenty',
      30: 'thirty',
      40: 'forty',
      50: 'fifty',
      60: 'sixty',
      70: 'seventy',
      80: 'eighty',
      90: 'ninety'
    };

    function getWords(n) {
      if (n <= 20) return words[n];
      if (n < 100) return words[Math.floor(n / 10) * 10] + (n % 10 ? ' ' + words[n % 10] : '');
      return '';
    }

    function toTitleCase(str) {
      return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.slice(1);
      });
    }

    function convertToWords(num) {
      if (num === 0) return 'Zero';
      var result = '';
      var crore = Math.floor(num / 10000000);
      num = num % 10000000;
      var lakh = Math.floor(num / 100000);
      num = num % 100000;
      var thousand = Math.floor(num / 1000);
      num = num % 1000;
      var hundred = Math.floor(num / 100);
      var rest = num % 100;
      if (crore) result += getWords(crore) + ' crore ';
      if (lakh) result += getWords(lakh) + ' lakh ';
      if (thousand) result += getWords(thousand) + ' thousand ';
      if (hundred) result += getWords(hundred) + ' hundred ';
      if (rest) {
        if (result !== '') result += 'and ';
        result += getWords(rest);
      }
      return toTitleCase(result.trim());
    }
    return convertToWords(amount);
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
      $('.main select#unit_name').selectpicker('val', response.unit_id);

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

  function pur_add_item_to_table(data, itemid, non_budget_item) {

    "use strict";

    data = typeof(data) == 'undefined' || data == 'undefined' ? pur_get_item_preview_values() : data;

    // if (data.quantity == "" || data.item_code == "" ) {

    //   return;
    // }
    if (data.item_name == "" || data.item_code == "") {
      alert_float('warning', "Please select item");
      return;
    }
    var currency_rate = $('input[name="currency_rate"]').val();
    var to_currency = $('select[name="currency"]').val();
    var table_row = '';
    var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
    lastAddedItemKey = item_key;
    $("body").append('<div class="dt-loader"></div>');
    pur_get_item_row_template('newitems[' + item_key + ']', data.item_name, data.description, data.area, data.image, data.quantity, data.unit_name, data.unit_price, data.taxname, data.item_code, data.unit_id, data.tax_rate, data.discount, itemid, currency_rate, to_currency, data.sub_groups_pur, non_budget_item).done(function(output) {
      table_row += output;

      $('.invoice-item table.invoice-items-table.items tbody').append(table_row);
      var sourceInput = $("input[name='image']")[0];
      var targetInput = $("input[name='newitems[" + lastAddedItemKey + "][image]']")[0];
      if (sourceInput.files.length > 0) {
        var dataTransfer = new DataTransfer();
        for (var i = 0; i < sourceInput.files.length; i++) {
          dataTransfer.items.add(sourceInput.files[i]);
        }
        targetInput.files = dataTransfer.files;
      }
      setTimeout(function() {
        pur_calculate_total();
      }, 15);
      init_selectpicker();
      pur_reorder_items('.invoice-item');
      pur_clear_item_preview_values('.invoice-item');
      $('body').find('#items-warning').remove();
      $("body").find('.dt-loader').remove();
      $('#item_select').selectpicker('val', '');
      get_project_areas(data.area);


      return true;
    });
    return false;
  }

  function pur_get_item_preview_values() {
    "use strict";

    var response = {};
    response.item_name = $('.invoice-item .main textarea[name="item_name"]').val();
    response.description = $('.invoice-item .main textarea[name="description"]').val();
    response.area = $('.invoice-item .main select[name="area"]').val();
    response.quantity = $('.invoice-item .main input[name="quantity"]').val();
    response.unit_name = $('.invoice-item .main select[name="unit_name"]').val();
    response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
    response.taxname = $('.main select.taxes').selectpicker('val');
    response.item_code = $('.invoice-item .main input[name="item_code"]').val();
    response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
    response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
    response.discount = $('.invoice-item .main input[name="discount"]').val();
    response.sub_groups_pur = $('.invoice-item .main select[name="sub_groups_pur"]').val();

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

  function pur_get_item_row_template(name, item_name, description, area, image, quantity, unit_name, unit_price, taxname, item_code, unit_id, tax_rate, discount, item_key, currency_rate, to_currency, sub_groups_pur, non_budget_item) {
    "use strict";

    jQuery.ajaxSetup({
      async: false
    });

    var d = $.post(admin_url + 'purchase/get_purchase_order_row_template', {
      name: name,
      item_name: item_name,
      item_description: description,
      area: area,
      image: image,
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
      to_currency: to_currency,
      sub_groups_pur: sub_groups_pur,
      non_budget_item: non_budget_item,
    });
    jQuery.ajaxSetup({
      async: true
    });
    return d;
  }

  // Set the currency for accounting
  function init_po_currency(id, callback) {
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

  function get_project_areas(selected_area = '') {
    var project = $('select[name="project"]').val();
    if (project !== '' && $('input[name="isedit"]').length == 0) {
      $.ajax({
        url: admin_url + 'purchase/get_project_areas',
        method: 'POST',
        data: {
          project: project
        },
        dataType: 'json',
        success: function(response) {
          var options = '';
          $.each(response, function(index, area) {
            var selected = (selected_area !== '' && selected_area == area.id) ? ' selected' : '';
            options += '<option value="' + area.id + '"' + selected + '>' + area.area_name + '</option>';
          });
          var $select = $('#project_area select');
          $select.html(options);
          $select.selectpicker('refresh');

          $('.invoice-item .main select[name="area"]').val('');
          var $select = $('.invoice-item .main select[name="area"]');
          $select.selectpicker('refresh');
        },
        error: function() {}
      });
    } else if (project == '' && $('input[name="isedit"]').length == 0) {
      var $select = $('#project_area select');
      $select.html('');
      $select.selectpicker('refresh');
    }
  }
</script>