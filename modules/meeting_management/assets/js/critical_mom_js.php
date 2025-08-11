<script>
    var table_rec_campaign;
    (function($) {
        "use strict";
        table_rec_campaign = $('.table-table_critical_tracker');

        var Params = {
            "department": "[name='department[]']",
            "status": "[name='status[]']",
            "priority": "[name='priority[]']",
        };

        initDataTable('.table-table_critical_tracker', admin_url + 'meeting_management/minutesController/table_critical_tracker', [], [], Params, [8, 'desc']);
        $.each(Params, function(i, obj) {
            // console.log(obj);
            $('select' + obj).on('change', function() {
                table_rec_campaign.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });

        $(document).on('click', '.reset_all_ot_filters', function() {
            var filterArea = $('.all_ot_filters');
            filterArea.find('input').val("");
            filterArea.find('select').selectpicker("val", "");
            table_rec_campaign.DataTable().ajax.reload().columns.adjust().responsive.recalc();
        });

        table_rec_campaign.on('draw.dt', function() {
            $('.selectpicker').selectpicker('refresh');
        });
        $('.buttons-collection').hide()
    })(jQuery);
    $(document).ready(function() {
        $('#project_filter').change(function() {
            const selectedProject = $(this).val();

            $.ajax({
                url: '<?php echo admin_url('meeting_management/agendaController/filter_minutes'); ?>',
                type: 'GET',
                data: {
                    project_filter: selectedProject
                },
                dataType: 'json',
                success: function(response) {
                    updateTableBody(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        function updateTableBody(data) {
            const tbody = $('table tbody');
            tbody.empty();

            if (data.length > 0) {
                $.each(data, function(index, agenda) {
                    const row = `
                    <tr>
                        <td>${agenda.meeting_title}</td>
                        <td>${agenda.project_name || 'N/A'}</td>
                        <td>${formatDate(agenda.meeting_date)}</td>
                        <td>
                            <a href="<?php echo admin_url('meeting_management/minutesController/index/'); ?>${agenda.id}" class="btn btn-primary"><?php echo _l('edit_converted_metting'); ?></a>
                            <a href="<?php echo admin_url('meeting_management/agendaController/delete/'); ?>${agenda.id}" class="btn btn-danger"><?php echo _l('delete'); ?></a>
                            <a href="<?php echo admin_url('meeting_management/agendaController/view_meeting/'); ?>${agenda.id}" class="btn btn-secondary"><?php echo _l('view_meeting'); ?></a>
                        </td>
                    </tr>
                `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="4" class="text-center"><?php echo _l("no_agendas_found"); ?></td></tr>');
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    });
    var table_critical_tracker = $('.table-table_critical_tracker');

    function change_status_mom(status, id) {
        "use strict";
        if (id > 0) {
            $.post(admin_url + 'meeting_management/minutesController/change_status_mom/' + status + '/' + id)
                .done(function(response) {
                    try {
                        response = JSON.parse(response);

                        if (response.success) {
                            var $statusSpan = $('#status_span_' + id);

                            // Debugging
                            // console.log('Before:', $statusSpan.attr('class'));

                            // Remove all status-related classes
                            $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

                            // Add the new class and update content
                            if (response.class) {
                                $statusSpan.addClass(response.class);
                            }
                            if (response.status_str) {
                                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
                            }

                            // Debugging
                            // console.log('After:', $statusSpan.attr('class'));

                            // Display success message
                            table_critical_tracker.DataTable().ajax.reload();
                            alert_float('success', response.mess);
                        } else {
                            // Display warning message if the operation fails
                            alert_float('warning', response.mess);
                        }
                    } catch (e) {
                        console.error('Error parsing server response:', e);
                        alert_float('danger', 'Invalid server response');
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert_float('danger', 'Failed to update status');
                });
        }
    }


    function change_priority_mom(status, id) {
        "use strict";
        if (id > 0) {
            $.post(admin_url + 'meeting_management/minutesController/change_priority_mom/' + status + '/' + id)
                .done(function(response) {
                    try {
                        response = JSON.parse(response);

                        if (response.success) {
                            var statusSpan = $('#priority_span_' + id);



                            // Remove all status-related classes
                            statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

                            // Add the new class and update content
                            if (response.class) {
                                statusSpan.addClass(response.class);
                            }
                            if (response.priority_str) {
                                statusSpan.html(response.priority_str + ' ' + (response.html || ''));
                            }



                            // Display success message
                            table_critical_tracker.DataTable().ajax.reload();
                            alert_float('success', response.mess);
                        } else {
                            // Display warning message if the operation fails
                            alert_float('warning', response.mess);
                        }
                    } catch (e) {
                        console.error('Error parsing server response:', e);
                        alert_float('danger', 'Invalid server response');
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert_float('danger', 'Failed to update status');
                });
        }
    }


    function change_department(departmentId, agendaId) {
        "use strict";
        if (agendaId > 0) {
            $.post(admin_url + 'meeting_management/minutesController/change_department/' + departmentId + '/' + agendaId)
                .done(function(response) {
                    try {
                        response = JSON.parse(response);

                        if (response.success) {
                            var deptSpan = $('#department_span_' + agendaId);

                            // Update the department name
                            if (response.department_name) {
                                // Remove the dropdown and keep just the department name
                                deptSpan.html(response.department_name);

                                // Re-add the dropdown HTML
                                deptSpan.append(response.html);
                                table_critical_tracker.DataTable().ajax.reload();
                            }

                            alert_float('success', response.message);
                        } else {
                            alert_float('warning', response.message);
                        }
                    } catch (e) {
                        console.error('Error parsing server response:', e);
                        alert_float('danger', 'Invalid server response');
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert_float('danger', 'Failed to update department');
                });
        }
    }

    $('body').on('change', '.closed-date-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var closedDate = $(this).val();

        // Perform AJAX request to update the completion date
        $.post(admin_url + 'meeting_management/minutesController/update_closed_date', {
            id: rowId,
            closedDate: closedDate
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                table_critical_tracker.reload(null, false); // Reload table without refreshing the page
            } else {
                alert_float('danger', response.message);
            }
        });
    });

    $('body').on('change', '.target-date-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var targetDate = $(this).val();

        // Perform AJAX request to update the target date
        $.post(admin_url + 'meeting_management/minutesController/update_target_date', {
            id: rowId,
            targetDate: targetDate
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                table_critical_tracker.reload(null, false); // Reload table without refreshing the page
            } else {
                alert_float('danger', response.message);
            }
        });
    });

    $(document).on('click', '.mom-critical-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? mom_critical_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.critical-tracker-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;
        mom_critical_get_item_row_template('newitems[' + item_key + ']', data.area, data.description, data.decision, data.action, data.staff, data.vendor, data.target_date, item_key, data.department, data.date_closed, data.status, data.priority, data.project_id).done(function(output) {
            table_row += output;

            $('.mom_body').append(table_row);
            init_selectpicker();
            pur_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            return true;
        });
        return false;
    });

    function mom_critical_get_item_row_template(name, area, description, decision, action, staff, vendor, target_date, item_key, department, date_closed, status, priority, project_id) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'meeting_management/minutesController/get_mom_critical_row_template', {
            name: name,
            area: area,
            description: description,
            decision: decision,
            action: action,
            staff: staff,
            vendor: vendor,
            target_date: target_date,
            item_key: item_key,
            department: department,
            date_closed: date_closed,
            status: status,
            priority: priority,
            project_id: project_id
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function mom_critical_get_item_preview_values() {
        "use strict";

        var response = {};
        response.area = $('.critical-tracker-items-table .main textarea[name="area"]').val();
        response.description = $('.critical-tracker-items-table .main textarea[name="description"]').val();
        response.decision = $('.critical-tracker-items-table .main textarea[name="decision"]').val();
        response.action = $('.critical-tracker-items-table .main textarea[name="action"]').val();
        response.staff = $('.critical-tracker-items-table .main select[name="staff"]').val();
        response.vendor = $('.critical-tracker-items-table .main input[name="vendor"]').val();
        response.target_date = $('.critical-tracker-items-table .main input[name="target_date"]').val();
        response.department = $('.critical-tracker-items-table .main select[name="department"]').val();
        response.date_closed = $('.critical-tracker-items-table .main input[name="date_closed"]').val();
        response.status = $('.critical-tracker-items-table .main select[name="status"]').val();
        response.priority = $('.critical-tracker-items-table .main select[name="priority"]').val();
        response.project_id = $('.critical-tracker-items-table .main select[name="project_id"]').val();
        return response;
    }

    function pur_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.mom_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('input[type="checkbox"]').prop('checked', false);
        previewArea.find('select')
            .prop('disabled', false) // Remove the disabled attribute
            .val('') // Clear the value
            .selectpicker('refresh'); // Refresh the selectpicker UI
    }

    function mom_critical_delete_item(row, itemid, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
            }, 50);
        });
        if (itemid && $('input[name="isedit"]').length > 0) {
            $(parent + ' #removed-items').append(hidden_input('removed_items[]', itemid));
        }
    }

    $(document).ready(function() {
        $("#critical_tracker-form").on("submit", function(e) {
            e.preventDefault();

            var $form = $(this);

            // Simple validation for each row's area
            var isValid = true;
            $(".table.items tbody tr").each(function(index) {
                if (index === 0) return; // Skip the first element
                var scopeVal = $(this)
                    .find("textarea[name='area'], textarea[name$='[area]']")
                    .val();
                console.log(scopeVal);
                if (!scopeVal) {
                    alert_float("danger", "Are/head is required in row " + (index + 1) + "!");
                    isValid = false;
                    return false; // break out of .each
                }
            });

            if (!isValid) return;

            // Submit via AJAX
            $.post(admin_url + "meeting_management/minutesController/add_critical_mom", $form.serialize(), function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float("success", response.message);

                    // Hide the modal (if using one)
                    $("#addNewRowModal").modal("hide");

                    // Reload DataTable
                    $(".table-table_critical_tracker").DataTable().ajax.reload();

                    // Reset the form after successful submission
                    $('.invoice-item table.critical-tracker-items-table.items tbody').html('');
                    $('.invoice-item table.critical-tracker-items-table.items tbody').append(response.row_template);

                    // $('#critical-agenda-tbody').html('');

                    init_selectpicker();
                    critical_clear_item_preview_values('.invoice-item');
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

    function critical_clear_item_preview_values(parent) {
        "use strict";

        var previewArea = $(parent + ' .main');
        console.log(previewArea);
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }
    var table_order_tracker = $('.table-table_critical_tracker').DataTable();
    $('body').on('click', '.area-display', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var currentValue = $(this).text();

        // Create a <textarea> with the same class/data-id and prefill the text
        var textarea = '<textarea ' +
            'rows="3" ' +
            'class="form-control area-input" ' +
            'data-id="' + rowId + '">' +
            currentValue +
            '</textarea>';

        $(this).replaceWith(textarea);

        // Optional: immediately focus the new textarea
        $('textarea.area-input[data-id="' + rowId + '"]').focus();
    });


    $('body').on('change', '.area-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var area = $(this).val();

        // Perform AJAX request to update the area
        $.post(admin_url + 'meeting_management/minutesController/update_critical_area', {
            id: rowId,
            area: area
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);


                $('.area-input[data-id="' + rowId + '"]').replaceWith('<span class="area-display" data-id="' + rowId + '">' + area + '</span>');

                // Optionally reload the table if necessary
                table_order_tracker.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message);
            }
        });
    });


    $('body').on('click', '.description-display', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var text = $(this).text();
        var ta = '<textarea rows="4" cols="80" ' +
            'class="form-control description-input" ' +
            'data-id="' + id + '">' +
            text +
            '</textarea>';
        $(this).replaceWith(ta);
        $('textarea.description-input[data-id="' + id + '"]').focus();
    });


    $('body').on('change', '.description-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var description = $(this).val();

        // Perform AJAX request to update the description
        $.post(admin_url + 'meeting_management/minutesController/update_critical_description', {
            id: rowId,
            description: description
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);


                $('.description-input[data-id="' + rowId + '"]').replaceWith('<span class="description-display" data-id="' + rowId + '">' + description + '</span>');

                // Optionally reload the table if necessary
                table_order_tracker.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message);
            }
        });
    });

    $('body').on('click', '.decision-display', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var text = $(this).text();
        var ta = '<textarea rows="4" cols="80" ' +
            'class="form-control decision-input" ' +
            'data-id="' + id + '">' +
            text +
            '</textarea>';
        $(this).replaceWith(ta);
        $('textarea.decision-input[data-id="' + id + '"]').focus();
    });


    $('body').on('change', '.decision-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var decision = $(this).val();

        // Perform AJAX request to update the decision
        $.post(admin_url + 'meeting_management/minutesController/update_critical_decision', {
            id: rowId,
            decision: decision
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);


                $('.decision-input[data-id="' + rowId + '"]').replaceWith('<span class="decision-display" data-id="' + rowId + '">' + decision + '</span>');

                // Optionally reload the table if necessary
                table_order_tracker.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message);
            }
        });
    });

    $('body').on('click', '.action-display', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var text = $(this).text();
        var ta = '<textarea rows="4" cols="80" ' +
            'class="form-control action-input" ' +
            'data-id="' + id + '">' +
            text +
            '</textarea>';
        $(this).replaceWith(ta);
        $('textarea.action-input[data-id="' + id + '"]').focus();
    });


    $('body').on('change', '.action-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var action = $(this).val();

        // Perform AJAX request to update the action
        $.post(admin_url + 'meeting_management/minutesController/update_critical_action', {
            id: rowId,
            action: action
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);


                $('.action-input[data-id="' + rowId + '"]').replaceWith('<span class="action-display" data-id="' + rowId + '">' + action + '</span>');

                // Optionally reload the table if necessary
                table_order_tracker.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message);
            }
        });
    });
    const STAFF_LIST = [
        <?php foreach ($staff_list as $st): ?> {
                staffid: <?php echo $st['staffid']; ?>,
                name: "<?php echo addslashes($st['firstname'] . ' ' . $st['lastname']); ?>"
            },
        <?php endforeach; ?>
    ];
    const DATA = {
        select_staff: "<?php echo addslashes(_l('select_staff')); ?>"
    };

    // click-to-edit: span → selectpicker
    $('body').on('click', '.staff-display', function(e) {
        e.preventDefault();
        const $span = $(this);
        const id = $span.data('id');
        const staffRaw = ($span.data('staff') || '').toString();
        const selectedIds = staffRaw.split(',').filter(Boolean);
        // build select
        let sel = '<select multiple ' +
            'class="form-control staff-input selectpicker" ' +
            'data-live-search="true" ' +
            'data-width="100%" ' +
            'data-id="' + id + '">';
        STAFF_LIST.forEach(st => {
            const selAttr = selectedIds.includes(st.staffid.toString()) ? ' selected' : '';
            sel += '<option value="' + st.staffid + '"' + selAttr + '>' + st.name + '</option>';
        });
        sel += '</select>';
        $span.replaceWith(sel);
        // init the plugin
        const $new = $('select.staff-input[data-id="' + id + '"]');
        $new.selectpicker().focus();
    });

    // on change, post and swap back to span
    // remove any previous handlers, then bind once to changed.bs.select
    $('body')
        .off('changed.bs.select', '.staff-input')
        .on('changed.bs.select', '.staff-input', function(e, clickedIndex, isSelected, previousValue) {
            const $sel = $(this);
            const id = $sel.data('id');
            const vals = $sel.val() || []; // array of IDs

            $.post(admin_url + 'meeting_management/minutesController/change_staff', {
                id: id,
                staff: vals
            }).done(function() {
                // rebuild display span
                const names = vals
                    .map(sid => {
                        const u = STAFF_LIST.find(u => u.staffid == sid);
                        return u ? u.name : '';
                    })
                    .filter(Boolean)
                    .join(', ');

                const span = '<span class="staff-display" ' +
                    'data-id="' + id + '" ' +
                    'data-staff="' + vals.join(',') + '">' +
                    names +
                    '</span>';

                // tear down the picker and swap in the span
                $sel.selectpicker('destroy').replaceWith(span);
                table_critical_tracker.DataTable().ajax.reload();
            });
        });

    $('body').on('click', '.vendor-display', function(e) {
        e.preventDefault();
        const $span = $(this);
        const id = $span.data('id');
        const text = $span.text().trim();

        const input = '<input type="text" ' +
            'class="form-control vendor-input" ' +
            'data-id="' + id + '" ' +
            'value="' + text + '">';

        $span.replaceWith(input);
        $('input.vendor-input[data-id="' + id + '"]').focus();
    });



    $('body').on('change', '.vendor-input', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var vendor = $(this).val();

        // Perform AJAX request to update the vendor
        $.post(admin_url + 'meeting_management/minutesController/update_critical_vendor', {
            id: rowId,
            vendor: vendor
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);


                $('.vendor-input[data-id="' + rowId + '"]').replaceWith('<br><span class="vendor-display" data-id="' + rowId + '">' + vendor + '</span>');

                // Optionally reload the table if necessary
                table_order_tracker.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message);
            }
        });
    });

    // $('.selectpicker').selectpicker();
</script>