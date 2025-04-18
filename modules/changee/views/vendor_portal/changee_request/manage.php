<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo changee_pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				<table class="table dt-table">
			       <thead>
			       	<th><?php echo '# '._l('pur_number'); ?></th>
			         <th><?php echo _l('pur_name'); ?></th>
			          <th><?php echo _l('co_requester'); ?></th>
			          <th><?php echo _l('co_request_time'); ?></th>
			          <th><?php echo _l('amount'); ?></th>
			          <th><?php echo _l('convert_to_quotation'); ?></th>
			       </thead>
			      <tbody>
			         <?php foreach($changee_request as $pr) { ?>
			         	<?php 
			         		$base_currency = changee_get_base_currency_pur(); 
			         		if($pr['currency'] != 0){
			         			$base_currency = changee_pur_get_currency_by_id($pr['currency']);
			         		}
			         	?>
			         <tr class="inv_tr">
			         	<td class="inv_tr"><a href="<?php echo site_url('changee/vendors_portal/co_request/'.$pr['id'].'/'.$pr['hash']); ?>"><?php echo changee_pur_html_entity_decode($pr['pur_rq_code']); ?></a></td>
			         	<td><?php echo changee_pur_html_entity_decode($pr['pur_rq_name']); ?></td>
			         	<td><?php echo get_staff_full_name($pr['requester']); ?></td>
			         	<td><?php echo _dt($pr['request_date']); ?></td>
			         	<td><?php echo app_format_money($pr['total'], $base_currency); ?></td>
			         	<td>
			         	<?php 
			         		if(total_rows(db_prefix().'co_estimates', ['co_request'=> $pr['id']]) > 0){
			         			echo '<span class="label label-success">'._l('converted').'</span>';
			         		}else{
			         			echo '<a href="'.site_url('changee/vendors_portal/add_update_quotation?changee_request='.$pr['id']).'" class="btn btn-info">'._l('convert').'</a>';
			         		} 
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