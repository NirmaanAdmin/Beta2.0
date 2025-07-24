<div class="row">
	<div class="col-md-12">
		<?php 
		$table_data = array(
			_l('Order Id'),
			_l('Receipt Id'),
			_l('Item'),
			_l('Descriptions'),
			_l('Vendor'),
			_l('Qty'),
			_l('Received On'),
			_l('Stock Import'),
			_l('Tech sign'),
			_l('Transport Doc'),
			_l('Production Certificate'),
			_l('Status'),
		);
		render_datatable($table_data,'table_goods_receipt_register',
			array('customizable-table')
		); ?>
	</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/goods_receipt_register_js.php'; ?>
</body>
</html>
