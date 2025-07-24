<div class="row">
	<div class="col-md-12">
		<?php 
		$table_data = array(
			_l('PO Id'),
			_l('Item'),
			_l('Descriptions'),
			_l('Issue Date'),
			_l('Return Date'),
			_l('Returned?'),
			_l('Days Overdue'),
			_l('Status'),
		);
		render_datatable($table_data,'table_returnable_material_alert',
			array('customizable-table')
		); ?>
	</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/returnable_material_alert_js.php'; ?>
</body>
</html>
