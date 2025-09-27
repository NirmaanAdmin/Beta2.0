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

		.column_sortable {
			cursor: pointer;
			user-select: none;
			position: relative;
			padding-right: 20px;
		}

		.column_sortable .sort-arrow {
			position: absolute;
			right: 5px;
			top: 50%;
			transform: translateY(-50%);
			font-size: 12px;
			opacity: 0.7;
		}
	</style>
	<table class="table table-items scroll-responsive no-mtop tablechilditemssorter">
		<thead class="bg-light-gray">
			<tr>
				<th scope="col"></th>
				<th scope="col"><input type="checkbox" id="mass_select_all" data-to-table="checkout_managements"></th>
				<th scope="col" class="column_sortable" data-sort="name"><?php echo _l('dmg_name'); ?></th>
				<th scope="col"><?php echo _l('dmg_date'); ?></th>
				<th scope="col"><?php echo _l('Last Updated By'); ?></th>
				<th scope="col" align="right"><?php echo _l('dmg_option'); ?></th>
			</tr>
		</thead>
		<tbody id="sortable-tbody">
			<?php
			foreach ($child_items as $key => $value) {
				$item_icon = '';
				if ($value['filetype'] == 'folder') {
					$item_icon = '<i class="fa fa-folder text-yellow fs-19"></i> ';
				} else {
					$item_icon = '<i class="fa fa-file text-primary fs-14"></i> ';
				}
				$a1 = '<a href="' . admin_url('document_management?id=' . $value['id']) . '" >';
				$a2 = '</a>';
			?>
				<tr class="sortable item" data-id="<?php echo htmldecode($value['id']); ?>">
					<td class="draggerer"></td>
					<td>
						<input type="checkbox" class="individual" class="w-100" data-id="<?php echo htmldecode($value['id']); ?>" onchange="checked_add(this); return false;" />
					</td>
					<td>
						<?php echo htmldecode('<div class="display-flex">' . $item_icon . $a1 . '<strong class="fs-14 mleft10">' . $value['name'] . '</strong>' . $a2 . '</div>'); ?>
					</td>
					<td>
						<?php echo htmldecode($a1 . _dt($value['dateadded']) . $a2); ?>
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
						<div class="dropdown pull-right">
							<button class="btn btn-tool pull-right dropdown-toggle" role="button" id="dropdown_menu_<?php echo htmldecode($value['id']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal">
									<circle cx="12" cy="12" r="1" />
									<circle cx="19" cy="12" r="1" />
									<circle cx="5" cy="12" r="1" />
								</svg>
							</button>
							<ul class="dropdown-menu" aria-labelledby="dropdown_menu_<?php echo htmldecode($value['id']); ?>">
								<?php
								$download = '';
								if ($value['filetype'] == 'folder') {
									$download = '<a href="' . admin_url('document_management/download_folder/' . $value['id']) . '" >' . _l('dmg_dowload') . '</a>';
								?>
									<li class="no-padding">
										<a href="#" data-name="<?php echo htmldecode($value['name']); ?>" onclick="edit_folder(this, '<?php echo htmldecode($value['id']); ?>')"><?php echo _l('dmg_edit') ?></a>
									</li>
								<?php } else {
									$download = '<a href="' . site_url('modules/document_management/uploads/files/' . $parent_id . '/' . $value['name']) . '" download>' . _l('dmg_dowload') . '</a>';
								?>
									<?php
									if (!check_file_locked($value['id'])) { ?>
										<li class="no-padding">
											<a href="<?php echo admin_url('document_management?id=' . $value['id'] . '&edit=1') ?>" data-name="<?php echo htmldecode($value['name']); ?>"><?php echo _l('dmg_edit_metadata') ?></a>
										</li>
								<?php }
								}
								?>
								<li class="no-padding">
									<a href="#" data-type="<?php echo htmldecode($value['filetype']); ?>" onclick="share_document(this, '<?php echo htmldecode($value['id']); ?>')"><?php echo _l('dmg_share') ?></a>
								</li>
								<li class="no-padding">
									<a href="#" onclick="duplicate_item('<?php echo htmldecode($value['id']); ?>')"><?php echo _l('dmg_duplicate') ?></a>
								</li>
								<li class="no-padding">
									<a href="#" onclick="move_item('<?php echo htmldecode($value['id']); ?>')"><?php echo _l('dmg_move') ?></a>
								</li>
								<li class="no-padding">
									<?php echo htmldecode($download); ?>
								</li>
								<li class="no-padding">
									<a class="_swaldelete" href="<?php echo admin_url('document_management/delete_section/' . $value['id'] . '/' . $parent_id) ?>"><?php echo _l('dmg_delete') ?></a>
								</li>
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
			const savedOrder = JSON.parse(localStorage.getItem('documentOrder')) || [];
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
					localStorage.setItem('documentOrder', JSON.stringify(newOrder));
					// Send AJAX to save order in DB
					$.ajax({
						url: admin_url + 'document_management/update_order',
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

			// Column Sorting with arrows
			const sortState = {};
			const headers = document.querySelectorAll('th.column_sortable');

			headers.forEach(th => {
				th.addEventListener('click', function() {
					const sortKey = th.dataset.sort;
					const isAsc = !sortState[sortKey];
					sortState[sortKey] = isAsc;

					// Clear existing arrows
					headers.forEach(h => {
						const arrow = h.querySelector('.sort-arrow');
						if (arrow) arrow.remove();
					});

					// Add current arrow
					const arrowSpan = document.createElement('span');
					arrowSpan.className = 'sort-arrow';
					arrowSpan.textContent = isAsc ? '▲' : '▼';
					th.appendChild(arrowSpan);

					const rows = Array.from(tbody.querySelectorAll('tr'));

					rows.sort((a, b) => {
						let valA = '',
							valB = '';

						if (sortKey === 'name') {
							valA = a.querySelector('td:nth-child(3)').textContent.trim().toLowerCase();
							valB = b.querySelector('td:nth-child(3)').textContent.trim().toLowerCase();
						} else if (sortKey === 'date') {
							valA = new Date(a.querySelector('td:nth-child(4)').textContent.trim());
							valB = new Date(b.querySelector('td:nth-child(4)').textContent.trim());
						}

						if (valA < valB) return isAsc ? -1 : 1;
						if (valA > valB) return isAsc ? 1 : -1;
						return 0;
					});

					tbody.append(...rows);
				});
			});
		});
	</script>