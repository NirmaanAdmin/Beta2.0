<script>
	(function($) {
		"use strict";
		var Params = {
			"vendors": "select[name='vendors[]']",
			"report_months": '[name="months-report"]',
      		"report_from": '[name="report-from"]',
      		"report_to": '[name="report-to"]',
      		"year_requisition": "[name='year_requisition']",
		};
		var table_stock_movement_ledger = $('table.table-table_stock_movement_ledger');
		var _table_api = initDataTable(table_stock_movement_ledger, admin_url+'warehouse/table_stock_movement_ledger', [], [], Params, [0, 'desc']);

		$('select[name="vendors[]"]').on('change', function() {
			table_stock_movement_ledger.DataTable().ajax.reload();
		});

		var report_from = $('input[name="report-from"]');
  		var report_to = $('input[name="report-to"]');
  		var date_range = $('#date-range');

		$('select[name="months-report"]').on('change', function() {
	      if ($(this).val() != 'custom') {
	        table_stock_movement_ledger.DataTable().ajax.reload();
	      }
	    });

	    $('select[name="year_requisition"]').on('change', function() {
	      table_stock_movement_ledger.DataTable().ajax.reload();
	    });

		report_from.on('change', function() {
	      var val = $(this).val();
	      var report_to_val = report_to.val();
	      if (val != '') {
	        report_to.attr('disabled', false);
	        if (report_to_val != '') {
	          table_stock_movement_ledger.DataTable().ajax.reload();
	        }
	      } else {
	        report_to.attr('disabled', true);
	      }
	    });

	    report_to.on('change', function() {
	      var val = $(this).val();
	      if (val != '') {
	        table_stock_movement_ledger.DataTable().ajax.reload();
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
	      table_stock_movement_ledger.DataTable().ajax.reload();
	    });
	    
})(jQuery);
</script>