<script>
	(function($) {
		"use strict";
		var Params = {};
		var table_stock_movement_ledger = $('table.table-table_stock_movement_ledger');
		var _table_api = initDataTable(table_stock_movement_ledger, admin_url+'warehouse/table_stock_movement_ledger', [], [], Params, [0, 'desc']);
})(jQuery);
</script>