	<table class="table table-items scroll-responsive no-mtop">
		<thead class="bg-light-gray">
			<tr>
				<th scope="col"><input type="checkbox" id="mass_select_all" data-to-table="checkout_managements"></th>
				<th scope="col"><?php echo _l('dmg_name'); ?></th>
				<th scope="col"><?php echo _l('dms_date'); ?></th>
				<?php /* 
				<th scope="col"><?php echo _l('dmg_option'); ?></th>
				*/ ?>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ($child_items as $key => $value) { 
				$item_icon = '';
				if($value['filetype'] == 'folder'){
					$item_icon = '<i class="fa fa-folder text-yellow fs-19"></i> ';
				} 
				else{
					$item_icon = '<i class="fa fa-file text-primary fs-14"></i> ';
				}
				if($value['filetype'] == 'folder') {
					$a1 = '<a href="'.admin_url('purchase/vendors_portal/drawing_management?id='.$value['id']).'" >';
					$a2 = '</a>';
				} else if($value['filetype'] == 'application/pdf') {
					$a1 = '<a class="preview-vendor-pdf" data-id="'.$value['id'].'" href="javascript:void(0);">';
					$a2 = '</a>';
				} else {
					$a1 = '<a>';
					$a2 = '</a>';
				}
				?>
				<tr>
					<td>
						<input type="checkbox" class="individual" class="w-100" data-id="<?php echo drawing_htmldecode($value['id']); ?>" onchange="checked_add(this); return false;"/>
					</td>
					<td>
						<?php echo drawing_htmldecode('<div class="display-flex">'.$item_icon.$a1.'<strong class="fs-14 mleft10">'.$value['name'].'</strong>'.$a2.'</div>'); ?>											
					</td>
					<td>
						<?php echo drawing_htmldecode($a1._dt($value['dateadded']).$a2); ?>											
					</td>

					<?php /* 
					<td>
						<div class="dropdown pull-right">
							<button class="btn btn-tool pull-right dropdown-toggle" role="button" id="dropdown_menu_<?php echo drawing_htmldecode($value['id']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
							</button>	
							<ul class="dropdown-menu" aria-labelledby="dropdown_menu_<?php echo drawing_htmldecode($value['id']); ?>">
								<?php 
								$download = '';
								if($value['filetype'] == 'folder'){ 
									$download = '<a href="'.admin_url('drawing_management/download_folder/'.$value['id']).'" >'._l('dmg_dowload').'</a>';
									?> 
									<li class="no-padding">
										<a href="#" data-name="<?php echo drawing_htmldecode($value['name']); ?>" onclick="edit_folder(this, '<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_edit') ?></a>											
									</li>
								<?php }
								else{ 
									$download = '<a href="'.site_url('modules/drawing_management/uploads/files/'.$parent_id.'/'.$value['name']).'" download>'._l('dmg_dowload').'</a>';
									?>
									<?php
									if(!drawing_check_file_locked($value['id'])){ ?>
										<li class="no-padding">
											<a href="<?php echo admin_url('drawing_management?id='.$value['id'].'&edit=1&pid='.$value['parent_id']) ?>" data-name="<?php echo drawing_htmldecode($value['name']); ?>"><?php echo _l('dmg_edit_metadata') ?></a>											
										</li>
									<?php } 
								}
								?>
								<li class="no-padding">
									<a href="#" data-type="<?php echo drawing_htmldecode($value['filetype']); ?>" onclick="share_document(this, '<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_share') ?></a>
								</li>
								<li class="no-padding">
									<a href="#" onclick="duplicate_item('<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_duplicate') ?></a>
								</li>
								<li class="no-padding">
									<a href="#" onclick="move_item('<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_move') ?></a>
								</li>
								<li class="no-padding">
									<?php echo drawing_htmldecode($download); ?>
								</li>
								<li class="no-padding">
									<a class="_swaldelete" href="<?php echo admin_url('drawing_management/delete_section/'.$value['id'].'/'.$parent_id) ?>" ><?php echo _l('dmg_delete') ?></a>
								</li>
							</ul>
						</div>
					</td>
					*/ ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<div id="view_vendor_pdf"></div>

<script>
	$(document).on('click', '.preview-vendor-pdf', function(e) {
	    var id = $(this).data('id');
	    view_vendor_pdf(id);
	});

	function view_vendor_pdf(id) {
	  "use strict"; 
	      $('#view_vendor_pdf').empty();
	      $("#view_vendor_pdf").load(site_url + 'purchase/vendors_portal/view_vendor_pdf/' + id, function(response, status, xhr) {
	          if (status == "error") {
	              alert_float('danger', xhr.statusText);
	          }
	      });
	}
</script>