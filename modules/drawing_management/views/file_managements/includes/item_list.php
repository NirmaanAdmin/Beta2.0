<style>
	.draggerer {
		width: 30px;
		cursor: move;
		position: relative;
	}

	.draggerer::after {
		content: "☰";
		position: absolute;
		left: 8px;
		top: 50%;
		transform: translateY(-50%);
		opacity: 0.4;
	}

	.draggerer:hover::after {
		opacity: 0.8;
	}
</style>

<table class="table table-items scroll-responsive no-mtop">
	<thead class="bg-light-gray">
		<tr>
			<th></th>
			<th scope="col"><input type="checkbox" id="mass_select_all" data-to-table="checkout_managements"></th>
			<th scope="col"><?php echo _l('dmg_name'); ?></th>
			<th scope="col"><?php echo _l('dms_date'); ?></th>
			<th scope="col"><?php echo _l('Last Updated By'); ?></th>
			<th scope="col"><?php echo _l('Last Modified'); ?></th>
			<th scope="col"><?php echo _l('dmg_option'); ?></th>
		</tr>
	</thead>
	<tbody id="sortable-tbody">
		<?php foreach ($child_items as $key => $value) {
			$item_icon = ($value['filetype'] == 'folder')
				? '<i class="fa fa-folder text-yellow fs-19"></i>'
				: '<i class="fa fa-file text-primary fs-14"></i>';

			$a1 = '<a href="' . admin_url('drawing_management?id=' . $value['id']) . '" >';
			$a2 = '</a>';
		?>
			<tr class="sortable item" data-id="<?php echo drawing_htmldecode($value['id']); ?>">
				<td class="draggerer"></td>
				<td>
					<input type="checkbox" class="individual" data-id="<?php echo drawing_htmldecode($value['id']); ?>" onchange="checked_add(this); return false;" />
				</td>
				<td>
					<?php
					if ($value['document_number'] > 0 && $value['orginal_filename'] == '') {
						$document_number = $value['document_number'] . '-' . $value['name'];
					} else {
						$document_number = $value['name'];
					}
					echo drawing_htmldecode('<div class="display-flex">' . $item_icon . $a1 . '<strong class="fs-14 mleft10">' . $document_number . '</strong>' . $a2 . '</div>'); ?>
				</td>
				<td>
					<?php $recent_log = drawing_get_audit_log_file($value['id']); ?>
					<?php echo drawing_htmldecode($a1 . _dt($recent_log['0']['date']) . $a2); ?>
				</td>

				<td>
					<?php
					if ($value['last_action'] > 0) {
						echo get_last_action_full_name($value['last_action']);
					} else {
						echo get_last_action_full_name($value['creator_id']);
					}
					?>
				</td>
				<td>
					<?php

					$recently_updated = false;

					// Check current item
					if (!empty($recent_log[0]['date'])) {
						$hours_diff = (time() - strtotime($recent_log[0]['date'])) / 3600;

						if ($hours_diff <= 72) {
							$recently_updated = true;
						}
					}

					// If not updated, check child items
					if (!$recently_updated) {

						$CI = &get_instance();

						$child_records = $CI->db
							->select('id')
							->where('parent_id', $value['id'])
							->get(db_prefix() . 'dms_items')
							->result_array();

						foreach ($child_records as $child) {

							$recent_log_new = drawing_get_audit_log_file($child['id']);

							if (!empty($recent_log_new[0]['date'])) {

								$hours_diff = (time() - strtotime($recent_log_new[0]['date'])) / 3600;

								if ($hours_diff <= 72) {
									$recently_updated = true;
									break;
								}
							}
						}
					}

					if ($recently_updated) {
						echo '<span class="label label-success">Recently Updated</span>';
					} else {
						echo '-';
					}

					?>
				</td>
				<td>
					<div class="dropdown pull-right">
						<button class="btn btn-tool pull-right dropdown-toggle" role="button" id="dropdown_menu_<?php echo drawing_htmldecode($value['id']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal">
								<circle cx="12" cy="12" r="1" />
								<circle cx="19" cy="12" r="1" />
								<circle cx="5" cy="12" r="1" />
							</svg>
						</button>
						<ul class="dropdown-menu" aria-labelledby="dropdown_menu_<?php echo drawing_htmldecode($value['id']); ?>">
							<?php
							if ($value['filetype'] == 'folder') {
								echo '<li class="no-padding"><a href="#" data-name="' . drawing_htmldecode($value['name']) . '" onclick="edit_folder(this, \'' . drawing_htmldecode($value['id']) . '\')">' . _l('dmg_edit') . '</a></li>';
								$download = '<a href="' . admin_url('drawing_management/download_folder/' . $value['id']) . '" >' . _l('dmg_dowload') . '</a>';
							} else {
								if (!drawing_check_file_locked($value['id'])) {
									echo '<li class="no-padding"><a href="' . admin_url('drawing_management?id=' . $value['id'] . '&edit=1&pid=' . $value['parent_id']) . '" data-name="' . drawing_htmldecode($value['name']) . '">' . _l('dmg_edit_metadata') . '</a></li>';
								}
								$download = '<a href="' . site_url('modules/drawing_management/uploads/files/' . $parent_id . '/' . $value['name']) . '" download onclick="download_all_rfi_pdf(' . $value['id'] . ')">' . _l('dmg_dowload') . '</a>';
							}
							?>
							<li class="no-padding"><a href="#" data-type="<?php echo drawing_htmldecode($value['filetype']); ?>" onclick="share_document(this, '<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_share') ?></a></li>
							<li class="no-padding"><a href="#" onclick="duplicate_item('<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_duplicate') ?></a></li>
							<li class="no-padding"><a href="#" onclick="move_item('<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_move') ?></a></li>
							<li class="no-padding"><?php echo drawing_htmldecode($download); ?></li>
							<li class="no-padding"><a class="_swaldelete" href="<?php echo admin_url('drawing_management/delete_section/' . $value['id'] . '/' . $parent_id) ?>"><?php echo _l('dmg_delete') ?></a></li>
						</ul>
					</div>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const tbody = document.querySelector('#sortable-tbody');

		// Restore order if available
		const savedOrder = JSON.parse(localStorage.getItem('drawingOrder')) || [];
		if (savedOrder.length) {
			const rows = Array.from(tbody.querySelectorAll('tr'));
			const rowMap = new Map();
			rows.forEach(row => {
				const id = row.dataset.id;
				if (id) {
					rowMap.set(id, row);
				}
			});
			const sortedRows = savedOrder.map(id => rowMap.get(id)).filter(Boolean);
			tbody.append(...sortedRows);
		}

		// Enable row sorting
		new Sortable(tbody, {
			handle: '.draggerer',
			animation: 150,
			ghostClass: 'sortable-ghost',
			onUpdate: function(evt) {
				const newOrder = Array.from(tbody.querySelectorAll('tr'))
					.map(row => row.dataset.id)
					.filter(Boolean);
				localStorage.setItem('drawingOrder', JSON.stringify(newOrder));
				// Send AJAX to save order in DB
				$.ajax({
					url: admin_url + 'drawing_management/update_order',
					type: 'POST',
					data: {
						order: newOrder
					},
					success: function(response) {
						console.log('Order saved:', response);
					},
					error: function(xhr) {
						console.error('AJAX error:', xhr.responseText);
					}
				});
			}
		});
	});

	function download_all_rfi_pdf(file_id) {
		$.ajax({
			url: admin_url + 'drawing_management/download_all_rfi_pdf/' + file_id,
			type: 'GET',
			dataType: 'json',
			success: function(res) {
				console.log(res);
				if (res.status === true) {
					triggerMultipleDownloads(res.download_urls);
				}
			}
		});
	}

	function triggerMultipleDownloads(urls) {
		let delay = 500;
		urls.forEach((url, index) => {
			setTimeout(() => {
				let a = document.createElement('a');
				a.href = url;
				a.download = "";
				document.body.appendChild(a);
				a.click();
				a.remove();
			}, index * delay);
		});
	}
</script>