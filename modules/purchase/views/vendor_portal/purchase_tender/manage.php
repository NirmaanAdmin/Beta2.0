<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4>Tender List</h4>
				<hr class="mtop5">
				<table class="table dt-table">
			       <thead>
			       	<th><?php echo '# '._l('pur_number'); ?></th>
			         <th><?php echo _l('pur_name'); ?></th>
			          <th><?php echo _l('pur_requester'); ?></th>
			          <th><?php echo _l('pur_request_time'); ?></th>
			          <th><?php echo _l('project'); ?></th>
					  <th><?php echo _l('Budget Head'); ?></th>
			          <th><?php echo _l('convert_to_quotation'); ?></th>
			       </thead>
			      <tbody>
			         <?php foreach($purchase_tender as $pr) { ?>
			         	<?php 
			         		$base_currency = get_base_currency_pur(); 
			         		if($pr['currency'] != 0){
			         			$base_currency = pur_get_currency_by_id($pr['currency']);
			         		}
			         	?>
			         <tr class="inv_tr">
			         	<td class="inv_tr"><a href="<?php echo site_url('purchase/vendors_portal/pur_tender/'.$pr['id'].'/'.$pr['hash']); ?>"><?php echo pur_html_entity_decode($pr['pur_tn_code']); ?></a></td>
			         	<td><?php echo pur_html_entity_decode($pr['pur_tn_name']); ?></td>
			         	<td><?php echo get_staff_full_name($pr['requester']); ?></td>
			         	<td><?php echo date('d M, Y H:i A', strtotime($pr['request_date'])); ?></td>
			         	<td><?php echo get_project_name_by_id($pr['project']); ?></td>
						<td><?php echo get_group_name_by_id($pr['group_pur']); ?></td>
			         	<td>
			         	<?php 
			         		// if(total_rows(db_prefix().'pur_estimates', ['pur_tender'=> $pr['id']]) > 0){
			         		// 	echo '<span class="label label-success">'._l('converted').'</span>';
			         		// }else{
			         			echo '<a href="'.site_url('purchase/vendors_portal/add_update_quotation?purchase_tender='.$pr['id']).'" class="btn btn-info">'._l('convert').'</a>';
			         		// } 
			         	 ?></td>
			         </tr>
			         <?php } ?>
			      </tbody>
			   </table>	
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>