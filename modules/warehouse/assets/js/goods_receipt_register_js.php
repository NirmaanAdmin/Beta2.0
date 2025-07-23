<script>
	(function($) {
		"use strict";
		var Params = {};
		var table_goods_receipt_register = $('table.table-table_goods_receipt_register');
		var _table_api = initDataTable(table_goods_receipt_register, admin_url+'warehouse/table_goods_receipt_register', [], [], Params, [6, 'desc']);
})(jQuery);
</script>