<div class="row">
	<div class="col-md-12">
		<?php 
		$table_data = array(
			_l('PO Id'),
			_l('Item'),
			_l('Descriptions'),
			_l('Opening Qty'),
			_l('Inward'),
			_l('Outward'),
			_l('Site Transfers'),
			_l('Closing Qty'),
		);
		render_datatable($table_data,'table_stock_movement_ledger',
			array('customizable-table')
		); ?>
	</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/stock_movement_ledger_js.php'; ?>
</body>
</html>
