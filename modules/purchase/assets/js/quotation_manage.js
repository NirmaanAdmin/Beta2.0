var hidden_columns = [2, 4, 5, 6];
(function ($) {
    "use strict";
    var Params = {
        "pur_request": "[name='pur_request[]']",
        "vendor": "[name='vendor[]']",
        "project": "[name='project[]']",
        "group_pur": "[name='group_pur[]']",
        "sub_groups_pur": "[name='sub_groups_pur[]']",
        "status": "[name='status[]']",
    };
    var table_estimates = $('.table-pur_estimates');
    initDataTable(table_estimates, admin_url + 'purchase/table_estimates', [0], [0], Params, [9, 'desc']);
    init_pur_estimate();

    $.each(Params, function (i, obj) {
        $('select' + obj).on('change', function () {
            table_estimates.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

    $('.table-pur_estimates').on('draw.dt', function () {
        var reportsTable = $(this).DataTable();
        var sums = reportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("Total (Per Page)");
        $(this).find('tfoot td.total_estimate_amount').html(sums.total_estimate_amount);
        $(this).find('tfoot td.total_estimate_tax').html(sums.total_estimate_tax);
    });

    $(document).on('click', '.reset_all_ot_filters', function () {
        var filterArea = $('.all_ot_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="project[]"]').selectpicker("val", "");
        table_estimates.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });
})(jQuery);

function init_pur_estimate(id) {
    "use strict";
    load_small_estimate_table_item(id, '#estimate', 'estimateid', 'purchase/get_estimate_data_ajax', '.table-pur_estimates');
}
function load_small_estimate_table_item(id, selector, input_name, url, table) {
    "use strict";
    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
        id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof (id) == 'undefined' || id === '') { return; }
    destroy_dynamic_scripts_in_element($(selector))
    if (!$("body").hasClass('small-table')) { toggle_small_estimate_view(table, selector); }
    $('input[name="' + input_name + '"]').val(id);
    do_hash_helper(id);
    $(selector).load(admin_url + url + '/' + id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }
}

function toggle_small_estimate_view(table, main_data) {
    "use strict";
    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) { return; }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
}
