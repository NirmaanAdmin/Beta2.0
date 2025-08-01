<script>
    $(function() {
        "use strict";

        // Initialize the DataTable
        var table_order_tracker = $('.table-table_order_tracker').DataTable();

        // Inline editing for "Completion Date"
        $('body').on('change', '.completion-date-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type'); // wo_order or pur_order
            var completionDate = $(this).val();

            // Perform AJAX request to update the completion date
            $.post(admin_url + 'purchase/update_completion_date', {
                id: rowId,
                table: tableType,
                completion_date: completionDate
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
                } else {
                    alert_float('danger', response.message);
                }
            });
        });
        $('body').on('change', '.order-date-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type'); // wo_order or pur_order
            var orderDate = $(this).val();

            // Perform AJAX request to update the oder date
            $.post(admin_url + 'purchase/update_order_date', {
                id: rowId,
                table: tableType,
                orderDate: orderDate
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
                } else {
                    alert_float('danger', response.message);
                }
            });
        });
        // Inline editing for "budget"
        $('body').on('change', '.budget-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type'); // wo_order or pur_order
            var budget = $(this).val();

            // Perform AJAX request to update the budget
            $.post(admin_url + 'purchase/update_budget', {
                id: rowId,
                table: tableType,
                budget: budget
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    table_order_tracker.ajax.reload(null, false); // Reload table without refreshing the page
                } else {
                    alert_float('danger', response.message);
                }
            });
        });

        // Inline editing for "Co Total Display" (toggle span to input)
        $('body').on('click', '.co-total-display', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var currentAmount = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

            // Replace the span with an input field
            $(this).replaceWith('<input type="number" class="form-control co-total-input" value="' + currentAmount + '" data-id="' + rowId + '" data-type="' + tableType + '">');
        });
        // Save updated "Anticipate Variation" to the database
        $('body').on('change', '.co-total-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var changeOrderAmount = $(this).val();

            // Perform AJAX request to update the anticipate_variation
            $.post(admin_url + 'purchase/update_change_order_amount', {
                id: rowId,
                table: tableType,
                changeOrderAmount: changeOrderAmount
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);

                    // Replace input back with formatted value
                    var formattedValue = new Intl.NumberFormat('en-IN', {
                        style: 'currency',
                        currency: 'INR'
                    }).format(changeOrderAmount);

                    $('.co-total-input[data-id="' + rowId + '"]').replaceWith('<span class="co-total-display" data-id="' + rowId + '" data-type="' + tableType + '">' + formattedValue + '</span>');

                    // Optionally reload the table if necessary
                    table_order_tracker.ajax.reload(null, false);
                } else {
                    alert_float('danger', response.message);
                }
            });
        });
        // Inline editing for "Amount" (toggle span to input)
        $('body').on('click', '.budget-display', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var currentAmount = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

            // Replace the span with an input field
            $(this).replaceWith('<input type="number" class="form-control budget-input" value="' + currentAmount + '" data-id="' + rowId + '" data-type="' + tableType + '">');
        });
        // Inline editing for "Anticipate Variation" (toggle span to input)
        $('body').on('click', '.anticipate-variation-display', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var currentValue = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

            // Replace the span with an input field
            $(this).replaceWith('<input type="number" class="form-control anticipate-variation-input" value="' + currentValue + '" data-id="' + rowId + '" data-type="' + tableType + '">');
        });

        // Save updated "Anticipate Variation" to the database
        $('body').on('change', '.anticipate-variation-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var anticipateVariation = $(this).val();

            // Perform AJAX request to update the anticipate_variation
            $.post(admin_url + 'purchase/update_anticipate_variation', {
                id: rowId,
                table: tableType,
                anticipate_variation: anticipateVariation
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);

                    // Replace input back with formatted value
                    var formattedValue = new Intl.NumberFormat('en-IN', {
                        style: 'currency',
                        currency: 'INR'
                    }).format(anticipateVariation);

                    $('.anticipate-variation-input[data-id="' + rowId + '"]').replaceWith('<span class="anticipate-variation-display" data-id="' + rowId + '" data-type="' + tableType + '">' + formattedValue + '</span>');

                    // Optionally reload the table if necessary
                    table_order_tracker.ajax.reload(null, false);
                } else {
                    alert_float('danger', response.message);
                }
            });
        });
        $('body').on('click', '.final-certified-amount-display', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var currentValue = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

            // Replace the span with an input field
            $(this).replaceWith('<input type="number" class="form-control final-certified-amount-input" value="' + currentValue + '" data-id="' + rowId + '" data-type="' + tableType + '">');
        });

        // Save updated "Final Certified Amount" to the database
        $('body').on('change', '.final-certified-amount-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var finalCertifiedAmount = $(this).val();

            // Perform AJAX request to update the anticipate_variation
            $.post(admin_url + 'purchase/update_final_certified_amount', {
                id: rowId,
                table: tableType,
                finalCertifiedAmount: finalCertifiedAmount
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);

                    // Replace input back with formatted value
                    var formattedValue = new Intl.NumberFormat('en-IN', {
                        style: 'currency',
                        currency: 'INR'
                    }).format(finalCertifiedAmount);

                    $('.final-certified-amount-input[data-id="' + rowId + '"]').replaceWith('<span class="final-certified-amount-display" data-id="' + rowId + '" data-type="' + tableType + '">' + formattedValue + '</span>');

                    // Optionally reload the table if necessary
                    table_order_tracker.ajax.reload(null, false);
                } else {
                    alert_float('danger', response.message);
                }
            });
        });
        // Inline editing for "Remarks" (toggle span to textarea)
        $('body').on('click', '.remarks-display', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var currentRemarks = $(this).text();

            // Replace the span with a textarea for editing
            $(this).replaceWith('<textarea class="form-control remarks-input" data-id="' + rowId + '" data-type="' + tableType + '">' + currentRemarks + '</textarea>');
        });

        // Save updated "Remarks" to the database
        $('body').on('change', '.remarks-input', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var remarks = $(this).val();

            // Perform AJAX request to update the remarks
            $.post(admin_url + 'purchase/update_remarks', {
                id: rowId,
                table: tableType,
                remarks: remarks
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);

                    // Replace textarea back with formatted remarks
                    $('.remarks-input[data-id="' + rowId + '"]').replaceWith('<span class="remarks-display" data-id="' + rowId + '" data-type="' + tableType + '">' + remarks + '</span>');

                    // Optionally reload the table if necessary
                    table_order_tracker.ajax.reload(null, false);
                } else {
                    alert_float('danger', response.message);
                }
            });
        });

        $('body').on('click', '.order-value-display', function(e) {
            e.preventDefault();
            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var currentAmount = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting
            // Replace the span with an input field
            $(this).replaceWith('<input type="number" class="form-control order-value-input" value="' + currentAmount + '" data-id="' + rowId + '" data-type="' + tableType + '">');
        });

        $('body').on('change', '.order-value-input', function(e) {
            e.preventDefault();
            var rowId = $(this).data('id');
            var tableType = $(this).data('type');
            var orderValueAmount = $(this).val();

            $.post(admin_url + 'purchase/update_order_value_amount', {
                id: rowId,
                table: tableType,
                orderValueAmount: orderValueAmount
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    table_order_tracker.ajax.reload(null, false);
                } else {
                    alert_float('danger', response.message);
                }
            });
        });
    });
    $(document).ready(function() {
        var table = $('.table-table_order_tracker').DataTable();

        // On page load, fetch and apply saved preferences for the logged-in user
        $.ajax({
            url: admin_url + 'purchase/getPreferences',
            type: 'GET',
            data: {
                module: 'order_tracker'
            },
            dataType: 'json',
            success: function(data) {
                console.log("Retrieved preferences:", data);

                // Ensure DataTable is initialized
                let table = $('.table-table_order_tracker').DataTable();

                // Loop through each toggle checkbox to update column visibility
                $('.toggle-column').each(function() {
                    // Parse the column index (ensuring it's a number)
                    let colIndex = parseInt($(this).val(), 10);

                    // Use the saved preference if available; otherwise, default to visible ("true")
                    let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
                        data.preferences[colIndex] :
                        "true";

                    // Convert string to boolean if needed
                    let isVisible = (typeof prefValue === "string") ?
                        (prefValue.toLowerCase() === "true") :
                        prefValue;

                    // Set column visibility but prevent immediate redraw (redraw = false)
                    table.column(colIndex).visible(isVisible, false);
                    // Update the checkbox state accordingly
                    $(this).prop('checked', isVisible);
                });

                // Finally, adjust columns and redraw the table once
                table.columns.adjust().draw();

                // Update the "Select All" checkbox based on individual toggle states
                let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
                $('#select-all-columns').prop('checked', allChecked);
            },
            error: function() {
                console.error('Could not retrieve column preferences.');
            }
        });



        // Handle "Select All" checkbox
        $('#select-all-columns').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.toggle-column').prop('checked', isChecked).trigger('change');
        });

        // Handle individual column visibility toggling
        $('.toggle-column').on('change', function() {
            var column = table.column($(this).val());
            column.visible($(this).is(':checked'));

            // Sync "Select All" checkbox state
            var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
            $('#select-all-columns').prop('checked', allChecked);

            // Save updated preferences
            saveColumnPreferences();
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        $('#ot-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });

        $('#ot-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });

        $(document).on('change', 'select[name="type[]"], select[name="rli_filter"], select[name="vendors[]"], select[name="kind"], select[name="budget_head"], select[name="order_type_filter"], select[name="projects[]"], select[name="aw_unw_order_status[]"]', function() {
            get_order_tracker_dashboard();
        });

        get_order_tracker_dashboard();

        // Function to collect and save preferences via AJAX
        function saveColumnPreferences() {
            var preferences = {};
            $('.toggle-column').each(function() {
                preferences[$(this).val()] = $(this).is(':checked');
            });

            $.ajax({

                url: admin_url + 'purchase/savePreferences',
                type: 'POST',
                data: {
                    preferences: preferences,
                    module: 'order_tracker'

                },
                success: function(response) {
                    console.log('Preferences saved successfully.');
                },
                error: function() {
                    console.error('Failed to save preferences.');
                }
            });
        }
    });



    function order_add_item_to_table(data, itemid) {

        "use strict";

        data = typeof(data) == 'undefined' || data == 'undefined' ? order_get_item_preview_values() : data;

        // if (data.quantity == "" || data.item_code == "" ) {

        //   return;
        // }
        if (data.order_scope == "") {
            alert_float('warning', "Please enter Order Scope");
            return;
        }

        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.order-tracker-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;
        // $("body").append('<div class="dt-loader"></div>');
        order_get_item_row_template('newitems[' + item_key + ']', data.order_scope, data.vendor, data.order_date, data.completion_date, data.budget_ro_projection, data.committed_contract_amount, data.change_order_amount, data.anticipate_variation, data.final_certified_amount, data.kind, data.project, data.group_pur, data.remarks, data.order_value).done(function(output) {
            table_row += output;

            $('.invoice-item table.order-tracker-items-table.items tbody').append(table_row);

            init_selectpicker();
            // pur_reorder_items('.invoice-item');
            order_clear_item_preview_values('.invoice-item');
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');


            return true;
        });
        return false;
    }

    function order_get_item_preview_values() {
        "use strict";

        var response = {};
        response.order_scope = $('.invoice-item .main textarea[name="order_scope"]').val();
        response.vendor = $('.invoice-item .main select[name="vendor"]').val();
        response.order_date = $('.invoice-item .main input[name="order_date"]').val();
        response.completion_date = $('.invoice-item .main input[name="completion_date"]').val();
        response.budget_ro_projection = $('.invoice-item .main input[name="budget_ro_projection"]').val();
        response.order_value = $('.invoice-item .main input[name="order_value"]').val();
        response.committed_contract_amount = $('.invoice-item .main input[name="committed_contract_amount"]').val();
        response.change_order_amount = $('.invoice-item .main input[name="change_order_amount"]').val();
        response.anticipate_variation = $('.invoice-item .main input[name="anticipate_variation"]').val();
        response.final_certified_amount = $('.invoice-item .main input[name="final_certified_amount"]').val();
        response.project = $('.invoice-item .main select[name="project"]').val();
        response.kind = $('.invoice-item .main select[name="kind"]').val();
        response.group_pur = $('.invoice-item .main select[name="group_pur"]').val();
        response.remarks = $('.invoice-item .main textarea[name="remarks"]').val();

        return response;
    }

    function order_clear_item_preview_values(parent) {
        "use strict";

        var previewArea = $(parent + ' .main');
        console.log(previewArea);
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function order_get_item_row_template(name, order_scope, vendor, order_date, completion_date, budget_ro_projection, committed_contract_amount, change_order_amount, anticipate_variation, final_certified_amount, kind, project, group_pur, remarks, order_value) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'purchase/get_order_tracker_row_template', {
            name: name,
            order_scope: order_scope,
            vendor: vendor,
            order_date: order_date,
            completion_date: completion_date,
            budget_ro_projection: budget_ro_projection,
            committed_contract_amount: committed_contract_amount,
            change_order_amount: change_order_amount,
            anticipate_variation: anticipate_variation,
            final_certified_amount: final_certified_amount,
            project: project,
            kind: kind,
            group_pur: group_pur,
            remarks: remarks,
            order_value: order_value
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function order_delete_item(row, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
            }, 50);
        });

    }
    $(document).ready(function() {
        $("#order_tracker-form").on("submit", function(e) {
            e.preventDefault();

            var $form = $(this);

            // Simple validation for each row's order_scope
            var isValid = true;
            $(".table.items tbody tr").each(function(index) {
                if (index === 0) return; // Skip the first element
                var scopeVal = $(this)
                    .find("textarea[name='order_scope'], textarea[name$='[order_scope]']")
                    .val();
                console.log(scopeVal);
                // if (!scopeVal) {
                //     alert_float("danger", "Order Scope is required in row " + (index + 1) + "!");
                //     isValid = false;
                //     return false; // break out of .each
                // }
            });

            if (!isValid) return;

            // Submit via AJAX
            $.post(admin_url + "purchase/add_order", $form.serialize(), function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float("success", response.message);

                    // Hide the modal (if using one)
                    $("#addNewRowModal").modal("hide");

                    // Reload DataTable
                    $(".table-table_order_tracker").DataTable().ajax.reload();

                    // Reset the form after successful submission
                    $('.invoice-item table.order-tracker-items-table.items tbody').html('');
                    $('.invoice-item table.order-tracker-items-table.items tbody').append(response.row_template);
                    init_selectpicker();
                    order_clear_item_preview_values('.invoice-item');
                    $('body').find('#items-warning').remove();
                    $("body").find('.dt-loader').remove();
                    $('#item_select').selectpicker('val', '');
                    // Optionally reset the form or do other tasks here
                } else {
                    alert_float("danger", response.message);
                }
            });
        });

    });

    $('body').on('click', '.contract-amount-display', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var tableType = $(this).data('type');
        var currentAmount = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

        // Replace the span with an input field
        $(this).replaceWith('<input type="number" class="form-control contract-amount-input" value="' + currentAmount + '" data-id="' + rowId + '" data-type="' + tableType + '">');
    });
    $('body').on('change', '.contract-amount-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var tableType = $(this).data('type'); // wo_order or pur_order
        var total = $(this).val();

        // Perform AJAX request to update the budget
        $.post(admin_url + 'purchase/update_order_tracker_contract_amount', {
            id: rowId,
            table: tableType,
            total: total
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                let table_order_tracker1 = $('.table-table_order_tracker').DataTable();
                table_order_tracker1.ajax.reload(null, false); // Reload table without refreshing the page
            } else {
                alert_float('danger', response.message);
            }
        });
    });

    // Build a JS array of all vendors (no trailing comma)
    const VENDORS_LIST = [
        <?php foreach ($vendors as $st): ?> {
                userid: "<?php echo $st['userid']; ?>",
                name: "<?php echo addslashes($st['company']); ?>"
            }
            <?php if (end($vendors) !== $st) echo ','; ?>
        <?php endforeach; ?>
    ];

    // Provide a human-friendly default
    const DATA = {
        select_vendor: "<?php echo addslashes(_l('select_vendor', 'Select vendor')); ?>"
    };

    // On click, swap the span for a selectpicker
    $('body').on('click', '.vendor-display', function(e) {
        e.preventDefault();
        const $span = $(this);
        const id = $span.data('id');
        const selId = ($span.data('vendor') || '').toString();

        // Build <select>
        let sel = `<select
                   class="form-control vendor-input selectpicker"
                   data-live-search="true"
                   data-width="100%"
                   data-id="${id}">
                   <option value="">${DATA.select_vendor}</option>`;

        VENDORS_LIST.forEach(v => {
            const selected = (v.userid.toString() === selId) ? ' selected' : '';
            sel += `<option value="${v.userid}"${selected}>${v.name}</option>`;
        });
        sel += '</select>';

        // Replace span → select and init
        $span.replaceWith(sel);
        const $new = $(`select.vendor-input[data-id="${id}"]`);
        $new.selectpicker().focus();
    });

    // On change, post the new vendor, then reload the row via DataTables
    $('body').on('changed.bs.select', '.vendor-input', function(e, clickedIndex, isSelected, previousValue) {
        const $sel = $(this);
        const id = $sel.data('id');
        const val = $sel.val();

        // show loader…
        $('#box-loading').html('<div class="Box"><span><span></span></span></div>');
        $('#loader-container').removeClass('hide');

        $.post(admin_url + 'purchase/change_vendor', {
                id,
                vendor: val
            })
            .done(function() {
                const table = $('.table-table_order_tracker').DataTable();
                table.ajax.reload(function() {
                    $('#box-loading').empty();
                    $('#loader-container').addClass('hide');
                }, false);
            });
    });

    var budgetedVsActualCategory;
    var lineChartOverTime;
    var lineChartCertifiedOverTime;

    function get_order_tracker_dashboard() {
      "use strict";

      var data = {
        type: $('select[name="type[]"]').val(),
        rli_filter: $('select[name="rli_filter"]').val(),
        vendors: $('select[name="vendors[]"]').val(),
        kind: $('select[name="kind"]').val(),
        budget_head: $('select[name="budget_head"]').val(),
        order_type_filter: $('select[name="order_type_filter"]').val(),
        projects: $('select[name="projects[]"]').val(),
        aw_unw_order_status: $('select[name="aw_unw_order_status[]"]').val(),
      }

      $.post(admin_url + 'purchase/get_order_tracker_charts', data).done(function(response){
        response = JSON.parse(response);

        // Update value summaries
        $('.cost_to_complete').text(response.cost_to_complete);
        $('.rev_contract_value').text(response.rev_contract_value);
        $('.percentage_utilized').text(response.percentage_utilized + '%');
        $('.budgeted_procurement_net_value').text(response.budgeted_procurement_net_value);

        // PIE CHART - Order Status Distribution
        var statusPieCtx = document.getElementById('pieChartForStatus').getContext('2d');
        var statusData = response.pie_status_value;
        var statusLabels = response.pie_status_name;

        if (window.statusChart) {
          statusChart.data.labels = statusLabels;
          statusChart.data.datasets[0].data = statusData;
          statusChart.update();
        } else {
          window.statusChart = new Chart(statusPieCtx, {
            type: 'pie',
            data: {
              labels: statusLabels,
              datasets: [{
                data: statusData,
                backgroundColor: [
                  'rgba(75, 192, 192, 0.7)',
                  'rgba(255, 206, 86, 0.7)',
                  'rgba(255, 99, 132, 0.7)'
                ],
                borderColor: [
                  'rgba(75, 192, 192, 1)',
                  'rgba(255, 206, 86, 1)',
                  'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: {
                  position: 'bottom'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return context.label + ': ' + context.formattedValue;
                    }
                  }
                }
              }
            }
          });
        }

        // COLUMN CHART - Budgeted vs Actual Procurement by Category
        var barCtx = document.getElementById('budgetedVsActualCategory').getContext('2d');
        var barData = {
          labels: response.budgeted_actual_category_labels,
          datasets: [{
              label: 'Budgeted',
              data: response.budgeted_category_value,
              backgroundColor: '#00008B',
              borderColor: '#00008B',
              borderWidth: 1
            },
            {
              label: 'Actual',
              data: response.actual_category_value,
              backgroundColor: '#1E90FF',
              borderColor: '#1E90FF',
              borderWidth: 1
            }
          ]
        };

        if (budgetedVsActualCategory) {
          budgetedVsActualCategory.data.labels = barData.labels;
          budgetedVsActualCategory.data.datasets[0].data = barData.datasets[0].data;
          budgetedVsActualCategory.data.datasets[1].data = barData.datasets[1].data;
          budgetedVsActualCategory.update();
        } else {
          budgetedVsActualCategory = new Chart(barCtx, {
            type: 'bar',
            data: barData,
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom'
                }
              },
              scales: {
                x: {
                  title: {
                    display: false,
                    text: 'Order Date'
                  }
                },
                y: {
                  beginAtZero: true,
                  title: {
                    display: false,
                    text: 'Amount'
                  }
                }
              }
            }
          });
        }

        // LINE CHART - Order Value Over Time
        var lineCtx = document.getElementById('lineChartOverTime').getContext('2d');

        if (lineChartOverTime) {
          lineChartOverTime.data.labels = response.line_order_date;
          lineChartOverTime.data.datasets[0].data = response.line_order_total;
          lineChartOverTime.update();
        } else {
          lineChartOverTime = new Chart(lineCtx, {
            type: 'line',
            data: {
              labels: response.line_order_date,
              datasets: [{
                label: 'Order Value',
                data: response.line_order_total,
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: true,
                  position: 'bottom'
                },
                tooltip: {
                  mode: 'index',
                  intersect: false
                }
              },
              scales: {
                x: {
                  title: {
                    display: true,
                    text: 'Month'
                  }
                },
                y: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Order Value'
                  }
                }
              }
            }
          });
        }

        // LINE CHART - Total Certified Amount Over Period of Time
        var lineCertifiedCtx = document.getElementById('lineChartCertifiedOverTime').getContext('2d');

        if (lineChartCertifiedOverTime) {
          lineChartCertifiedOverTime.data.labels = response.line_certified_date;
          lineChartCertifiedOverTime.data.datasets[0].data = response.line_certified_total;
          lineChartCertifiedOverTime.update();
        } else {
          lineChartCertifiedOverTime = new Chart(lineCertifiedCtx, {
            type: 'line',
            data: {
              labels: response.line_certified_date,
              datasets: [{
                label: 'Total Certified Amount',
                data: response.line_certified_total,
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: true,
                  position: 'bottom'
                },
                tooltip: {
                  mode: 'index',
                  intersect: false
                }
              },
              scales: {
                x: {
                  title: {
                    display: true,
                    text: 'Month'
                  }
                },
                y: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Total Certified Amount'
                  }
                }
              }
            }
          });
        }

      });
    }
</script>