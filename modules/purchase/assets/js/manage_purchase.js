
"use strict";

var GoodsreceiptParams = {
    "day_vouchers": "input[name='date_add']",
    "kind": "select[name='kind']",
    "delivery": "select[name='delivery']",
    "vendors": "[name='vendors[]']",
    "toggle-filter": "input[name='toggle-filter']"
};

var table_manage_goods_receipt = $('.table-table_manage_goods_receipt');

initDataTable(table_manage_goods_receipt, admin_url + 'purchase/table_manage_goods_receipt', [], [], GoodsreceiptParams, [5, 'desc']);


$('.purchase_sm').DataTable().columns([0]).visible(false, false);

$('#date_add').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
});

$('#kind').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('#delivery').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('select[name="vendors[]"]').on('change', function () {
    $('select[name="vendors[]"]').selectpicker('refresh');
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('.toggle-filter').on('change', function () {
    var isChecked = $(this).is(':checked') ? 1 : 0;
    $(this).val(isChecked); // Update the value of the checkbox (0 or 1)

    // Trigger DataTable reload to apply the new filter
    table_manage_goods_receipt.DataTable().ajax.reload();
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

function view_purchase_tracker_attachments(rel_id) {
    "use strict";
    $.post(admin_url + 'purchase/view_purchase_tracker_attachments', {
        rel_id: rel_id
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
    view_purchase_tracker_file(id);
}

function view_purchase_tracker_file(id) {
    "use strict";
    $('#purchase_tracker_file_data').empty();
    $("#purchase_tracker_file_data").load(admin_url + 'purchase/view_purchase_tracker_file/' + id, function (response, status, xhr) {
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