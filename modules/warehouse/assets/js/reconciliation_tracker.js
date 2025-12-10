
"use strict";

var GoodsreceiptParams = {
    "day_vouchers": "input[name='date_add']",
    "kind": "select[name='kind']",
    "delivery": "select[name='delivery']",
    "vendors": "[name='vendors[]']",
    "group_pur": "[name='group_pur[]']",
    "tracker_status": "[name='tracker_status[]']",
    "production_status": "[name='production_status[]']",
    "wo_po_order" : "[name='wo_po_order[]']",
    "toggle-filter": "input[name='toggle-filter']"
};

var table_manage_stock_reconciliation_list = $('.table-table_manage_stock_reconciliation_list');

initDataTable(table_manage_stock_reconciliation_list, admin_url + 'warehouse/table_manage_stock_reconciliation_list', [], [], GoodsreceiptParams, [2, 'desc']);

var table_manage_actual_stock_reconciliation = $('.table-table_manage_actual_stock_reconciliation');

initDataTable(table_manage_actual_stock_reconciliation, admin_url + 'warehouse/table_manage_actual_stock_reconciliation', [], [], GoodsreceiptParams, [9, 'desc']);
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        $('.purchase_sm').DataTable().columns([0]).visible(false, false);
        table_manage_stock_reconciliation_list.DataTable().ajax.reload();
    } else {
        $('.purchase_sm').DataTable().columns([0]).visible(true, true);
        table_manage_actual_stock_reconciliation.DataTable().ajax.reload();
    }
});

$('#date_add').on('change', function () {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_stock_reconciliation_list.DataTable().ajax.reload();
    } else {
        table_manage_actual_stock_reconciliation.DataTable().ajax.reload();
    }
});

$('#kind').on('change', function () {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('#delivery').on('change', function () {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('select[name="vendors[]"]').on('change', function () {
    $('select[name="vendors[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('.toggle-filter').on('change', function () {
    var isChecked = $(this).is(':checked') ? 1 : 0;
    $(this).val(isChecked); // Update the value of the checkbox (0 or 1)

    // Trigger DataTable reload to apply the new filter
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('select[name="group_pur[]"]').on('change', function () {
    $('select[name="group_pur[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('select[name="tracker_status[]"]').on('change', function () {
    $('select[name="tracker_status[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('select[name="production_status[]"]').on('change', function () {
    $('select[name="production_status[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});

$('select[name="wo_po_order[]"]').on('change', function () {
    $('select[name="wo_po_order[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }else{
        table_manage_goods_receipt.DataTable().ajax.reload();
    }
});




init_goods_receipt();
function init_goods_receipt(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'purchase/view_purchase', '.purchase_sm');
}
var hidden_columns = [3, 4, 5];

init_po_tracker();
function init_po_tracker(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'purchase/view_po_tracker', '.purchase_sm');
}

init_wo_tracker();
function init_wo_tracker(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'purchase/view_wo_tracker', '.purchase_sm');
}
 
  
function load_small_table_item_proposal(pr_id, selector, input_name, url, table) {
    "use strict";

    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
        pr_id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !pr_id) {
            pr_id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof (pr_id) == 'undefined' || pr_id === '') { return; }
    if (!$("body").hasClass('small-table')) { toggle_small_view_proposal(table, selector); }
    $('input[name="' + input_name + '"]').val(pr_id);
    do_hash_helper(pr_id);
    $(selector).load(admin_url + url + '/' + pr_id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }

}


function toggle_small_view_proposal(table, main_data) {
    "use strict";

    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) { return; }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        $('#heading').addClass('col-md-10').removeClass('col-md-8');
        $('#filter_div').addClass('col-md-2').removeClass('col-md-4');

        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('#heading').removeClass('col-md-10').addClass('col-md-8');
        $('#filter_div').removeClass('col-md-2').addClass('col-md-4');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');

}

function view_purchase_tracker_attachments(rel_id,view_type) {
    "use strict";
    $.post(admin_url + 'purchase/view_purchase_tracker_attachments', {
        rel_id: rel_id,
        view_type: view_type
    }).done(function (response) { 
        response = JSON.parse(response);
        if (response.result) {
            $('.view_purchase_attachment_modal').html(response.result);
        } else {
            $('.view_purchase_attachment_modal').html('');
        }
        $('#viewpurchaseorderAttachmentModal').modal('show');
    });
}

function preview_purchase_tracker_btn(invoker) {
    "use strict";
    var id = $(invoker).attr('id');
    var view_type = $(invoker).attr('view_type');;
    view_purchase_tracker_file(id,view_type);
}

function view_purchase_tracker_file(id,view_type) {
    "use strict";
    $('#purchase_tracker_file_data').empty();
    $("#purchase_tracker_file_data").load(admin_url + 'purchase/view_purchase_tracker_file/' + id + '/' + view_type, function (response, status, xhr) {
        if (status == "error") {
            alert_float('danger', xhr.statusText);
        }
    });
}

function close_modal_preview() {
    "use strict";
    $('._project_file').modal('hide');
}


function delete_purchase_tracker_attachment(id) {
    "use strict";
    if (confirm_delete()) {
        requestGet('purchase/delete_purchase_tracker_attachment/' + id).done(function (success) {
            if (success == 1) {
                $(".view_purchase_attachment_modal").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function (error) {
            alert_float('danger', error.responseText);
        });
    }
}