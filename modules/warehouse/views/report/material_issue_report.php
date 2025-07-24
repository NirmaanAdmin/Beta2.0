<div class="row">
	<div class="col-md-12">
		<?php 
		$table_data = array(
			_l('PO Id'),
			_l('Issue Id'),
			_l('Item'),
			_l('Descriptions'),
			_l('Vendor'),
			_l('Qty'),
			_l('Issued On'),
			_l('Return able'),
			_l('Returned?'),
			_l('Return Date'),
		);
		render_datatable($table_data,'table_material_issue_report',
			array('customizable-table')
		); ?>
	</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/material_issue_report_js.php';?>
</body>
</html>
