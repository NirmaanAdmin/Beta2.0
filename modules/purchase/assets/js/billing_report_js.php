<script>
  var fnServerParams, report_from_choose, report_summary, report_aging, report_mapping, report_invoicing, report_client_aging;
  var report_from = $('input[name="report-from"]');
  var report_to = $('input[name="report-to"]');
  var date_range = $('#date-range');
  (function($) {
    "use strict";
    report_from_choose = $('#report-time');
    report_summary = $('#list_summary_report');
    report_aging = $('#list_aging_report');
    report_mapping = $('#list_mapping_report');
    report_invoicing = $('#list_invoicing_report');
    report_client_aging = $('#list_client_aging_report');
    fnServerParams = {
      "report_months": '[name="months-report"]',
      "report_from": '[name="report-from"]',
      "report_to": '[name="report-to"]',
      "year_requisition": "[name='year_requisition']",
      "report_currency": '[name="currency"]',
      "summary_project": '[name="summary_project"]',
      "summary_vendor": '[name="summary_vendor[]"]',
      "summary_status": '[name="summary_status[]"]',
      "aging_project": '[name="aging_project"]',
      "aging_vendor": '[name="aging_vendor[]"]',
      "aging_status": '[name="aging_status[]"]',
      "mapping_project": '[name="mapping_project"]',
      "mapping_vendor": '[name="mapping_vendor[]"]',
      "mapping_status": '[name="mapping_status[]"]',
      "invoicing_project": '[name="invoicing_project"]',
      "invoicing_status": '[name="invoicing_status[]"]',
      "client_aging_project": '[name="client_aging_project"]',
      "client_aging_status": '[name="client_aging_status[]"]',
    }

    $('select[name="currency"]').on('change', function() {
      gen_reports();
    });

    $('select[name="months-report"]').on('change', function() {
      if ($(this).val() != 'custom') {
        gen_reports();
      }
    });

    $('select[name="year_requisition"]').on('change', function() {
      gen_reports();
    });

    $('select[name="summary_project"]').on('change', function() {
      gen_reports();
    });

    $('select[name="summary_vendor[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="summary_status[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="aging_project"]').on('change', function() {
      gen_reports();
    });

    $('select[name="aging_vendor[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="aging_status[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="mapping_project"]').on('change', function() {
      gen_reports();
    });

    $('select[name="mapping_vendor[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="mapping_status[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="invoicing_project"]').on('change', function() {
      gen_reports();
    });

    $('select[name="invoicing_status[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="client_aging_project"]').on('change', function() {
      gen_reports();
    });

    $('select[name="client_aging_status[]"]').on('change', function() {
      gen_reports();
    });
   
    report_from.on('change', function() {
      var val = $(this).val();
      var report_to_val = report_to.val();
      if (val != '') {
        report_to.attr('disabled', false);
        if (report_to_val != '') {
          gen_reports();
        }
      } else {
        report_to.attr('disabled', true);
      }
    });

    report_to.on('change', function() {
      var val = $(this).val();
      if (val != '') {
        gen_reports();
      }
    });

    $('select[name="months-report"]').on('change', function() {
      var val = $(this).val();
      report_to.attr('disabled', true);
      report_to.val('');
      report_from.val('');
      if (val == 'custom') {
        date_range.addClass('fadeIn').removeClass('hide');
        return;
      } else {
        if (!date_range.hasClass('hide')) {
          date_range.removeClass('fadeIn').addClass('hide');
        }
      }
      gen_reports();
    });

    $('.table-summary-report').on('draw.dt', function() {
      var summaryReportsTable = $(this).DataTable();
      var sums = summaryReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total_billed').html(sums.total_billed);
      $(this).find('tfoot td.total_paid').html(sums.total_paid);
      $(this).find('tfoot td.total_balance').html(sums.total_balance);
    });

    $('.table-aging-report').on('draw.dt', function() {
      var agingReportsTable = $(this).DataTable();
      var sums = agingReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total_amount').html(sums.total_amount);
    });

    $('.table-mapping-report').on('draw.dt', function() {
      var mappingReportsTable = $(this).DataTable();
      var sums = mappingReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total_vendor_amount').html(sums.total_vendor_amount);
    });

    $('.table-invoicing-report').on('draw.dt', function() {
      var invoicingReportsTable = $(this).DataTable();
      var sums = invoicingReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total_amount').html(sums.total_amount);
      $(this).find('tfoot td.total_paid').html(sums.total_paid);
    });

    $('.table-client-aging-report').on('draw.dt', function() {
      var clientAgingReportsTable = $(this).DataTable();
      var sums = clientAgingReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total_amount_due').html(sums.total_amount_due);
    });

  })(jQuery);


  function init_report(e, type) {
    "use strict";
    var report_wrapper = $('#report');
    if (report_wrapper.hasClass('hide')) {
      report_wrapper.removeClass('hide');
    }
    $('head title').html($(e).text());
    report_from_choose.addClass('hide');
    $('#year_requisition').addClass('hide');
    report_summary.addClass('hide');
    report_aging.addClass('hide');
    report_mapping.addClass('hide');
    report_invoicing.addClass('hide');
    report_client_aging.addClass('hide');
    $('select[name="months-report"]').selectpicker('val', '');
    $('#currency').removeClass('hide');
    report_from_choose.removeClass('hide');
    if (type == 'summary_report') {
      report_summary.removeClass('hide');
    } else if (type == 'aging_report') {
      report_aging.removeClass('hide');
    } else if (type == 'mapping_report') {
      report_mapping.removeClass('hide');
    } else if (type == 'invoicing_report') {
      report_invoicing.removeClass('hide');
    } else if (type == 'client_aging_report') {
      report_client_aging.removeClass('hide');
    }
    gen_reports();
  }

  function summary_report() {
    "use strict";
    var table_summary_report = $('.table-summary-report');
    if ($.fn.DataTable.isDataTable('.table-summary-report')) {
      $('.table-summary-report').DataTable().destroy();
    }
    initDataTable('.table-summary-report', admin_url + 'purchase/billing_summary_report', false, false, fnServerParams, [1,'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_summary_report.DataTable().ajax.reload();
      });
    });
  }

  function aging_report() {
    "use strict";
    var table_aging_report = $('.table-aging-report');
    if ($.fn.DataTable.isDataTable('.table-aging-report')) {
      $('.table-aging-report').DataTable().destroy();
    }
    initDataTable('.table-aging-report', admin_url + 'purchase/billing_aging_report', false, false, fnServerParams, [2,'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_aging_report.DataTable().ajax.reload();
      });
    });
  }

  function mapping_report() {
    "use strict";
    var table_mapping_report = $('.table-mapping-report');
    if ($.fn.DataTable.isDataTable('.table-mapping-report')) {
      $('.table-mapping-report').DataTable().destroy();
    }
    initDataTable('.table-mapping-report', admin_url + 'purchase/billing_mapping_report', false, false, fnServerParams, [3,'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_mapping_report.DataTable().ajax.reload();
      });
    });
  }

  function invoicing_report() {
    "use strict";
    var table_invoicing_report = $('.table-invoicing-report');
    if ($.fn.DataTable.isDataTable('.table-invoicing-report')) {
      $('.table-invoicing-report').DataTable().destroy();
    }
    initDataTable('.table-invoicing-report', admin_url + 'purchase/billing_invoicing_report', false, false, fnServerParams, [1,'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_invoicing_report.DataTable().ajax.reload();
      });
    });
  }

  function client_aging_report() {
    "use strict";
    var table_client_aging_report = $('.table-client-aging-report');
    if ($.fn.DataTable.isDataTable('.table-client-aging-report')) {
      $('.table-client-aging-report').DataTable().destroy();
    }
    initDataTable('.table-client-aging-report', admin_url + 'purchase/billing_client_aging_report', false, false, fnServerParams, [2,'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_client_aging_report.DataTable().ajax.reload();
      });
    });
  }

  // Main generate report function
  function gen_reports() {
    "use strict";
    if (!report_summary.hasClass('hide')) {
      summary_report();
    } else if (!report_aging.hasClass('hide')) {
      aging_report();
    } else if (!report_mapping.hasClass('hide')) {
      mapping_report();
    } else if (!report_invoicing.hasClass('hide')) {
      invoicing_report();
    } else if (!report_client_aging.hasClass('hide')) {
      client_aging_report();
    }
  }
</script>