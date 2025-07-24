<script>
	(function($) {
		"use strict";
		var Params = {};
		var table_material_issue_report = $('table.table-table_material_issue_report');
		var _table_api = initDataTable(table_material_issue_report, admin_url+'warehouse/table_material_issue_report', [], [], Params, [6, 'desc']);
})(jQuery);
</script>