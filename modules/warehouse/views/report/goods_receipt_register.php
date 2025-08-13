<div class="row row-margin-bottom">
	<div class="col-md-3 form-group">
      <select name="vendors[]" id="vendors" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>" data-actions-box="true">
         <option value=""></option>
         <?php
         $vendor = get_pur_vendor_list();
         foreach ($vendor as $vendors) { ?>
            <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
         <?php  } ?>
      </select>
    </div>
    <div class="col-md-2 form-group">
      <?php
      $statuses = [
         1 => ['id' => 'approved', 'name' => _l('approved')],
         2 => ['id' => 'not_yet_approve', 'name' => _l('not_yet_approve')],
         3 => ['id' => 'reject', 'name' => _l('reject')],
      ];
      echo render_select('status[]', $statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
    </div>
    <div class="col-md-3 form-group" id="report-time">
			<select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
				<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
				<option value="this_month"><?php echo _l('this_month'); ?></option>
				<option value="1"><?php echo _l('last_month'); ?></option>
				<option value="this_year"><?php echo _l('this_year'); ?></option>
				<option value="last_year"><?php echo _l('last_year'); ?></option>
				<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
				<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
				<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
				<option value="custom"><?php echo _l('period_datepicker'); ?></option>
			</select>
		</div>
		<div id="date-range" class="hide mbot15">
			<div class="col-md-2 form-group">
				<div class="input-group date">
					<input type="text" class="form-control datepicker" id="report-from" name="report-from">
					<div class="input-group-addon">
						<i class="fa fa-calendar calendar-icon"></i>
					</div>
				</div>
			</div>
			<div class="col-md-2 form-group">
				<div class="input-group date">
					<input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
					<div class="input-group-addon">
						<i class="fa fa-calendar calendar-icon"></i>
					</div>
				</div>
			</div>
		</div>
		<?php $current_year = date('Y');
		$y0 = (int)$current_year;
		$y1 = (int)$current_year - 1;
		$y2 = (int)$current_year - 2;
		$y3 = (int)$current_year - 3;
		?>
		<div class="form-group hide" id="year_requisition">
			<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
			<select name="year_requisition" id="year_requisition" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('filter_by') . ' ' . _l('year'); ?>">
				<option value="<?php echo pur_html_entity_decode($y0); ?>" <?php echo 'selected' ?>><?php echo _l('year') . ' ' . pur_html_entity_decode($y0); ?></option>
				<option value="<?php echo pur_html_entity_decode($y1); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y1); ?></option>
				<option value="<?php echo pur_html_entity_decode($y2); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y2); ?></option>
				<option value="<?php echo pur_html_entity_decode($y3); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y3); ?></option>

			</select>
		</div>
</div>

<div class="row">
   <div class="col-md-12">
      <table class="dt-table-loading table table-table_goods_receipt_register customizable-table">
         <thead>
            <tr>
              <th><?php echo _l('Order Id'); ?></th>
              <th><?php echo _l('Receipt Id'); ?></th>
              <th><?php echo _l('Item'); ?></th>
              <th><?php echo _l('Descriptions'); ?></th>
              <th><?php echo _l('Vendor'); ?></th>
              <th><?php echo _l('Qty'); ?></th>
              <th><?php echo _l('Received On'); ?></th>
              <th><?php echo _l('Stock Import'); ?></th>
              <th><?php echo _l('Tech sign'); ?></th>
              <th><?php echo _l('Transport Doc'); ?></th>
              <th><?php echo _l('Production Certificate'); ?></th>
              <th><?php echo _l('Status'); ?></th>
            </tr>
         </thead>
         <tbody>
         </tbody>
         <tfoot>
         	<tr>
         		<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
            	<td class="total_quantity"></td>
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
         	</tr>
         </tfoot>
      </table>
   </div>
</div>

<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/goods_receipt_register_js.php'; ?>
</body>
</html>
