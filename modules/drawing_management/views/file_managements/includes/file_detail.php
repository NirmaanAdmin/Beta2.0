<?php if (isset($item)) {
?>
	<style>
		#vueApp,
		#related_tasks {
			display: none;
		}
	</style>
	<div class="row">
		<div class="col-md-8">
			<input type="hidden" name="id" value="<?php echo drawing_htmldecode($item->id); ?>">
			<input type="hidden" name="default_parent_id" value="<?php echo drawing_htmldecode($parent_id); ?>">
			<input type="hidden" name="folder_id" value="<?php echo drawing_htmldecode($item->parent_id); ?>">
			<?php if ($file_locked) { ?>
				<div class="alert alert-warning">
					<?php echo _l('dmg_the_file_is_locked_by') . ' ' . get_staff_full_name($item->lock_user) . ' ' . _l('dmg_for_editing'); ?>
				</div>
			<?php } ?>

			<h4><?php echo drawing_htmldecode($item->name); ?></h4>
			<table class="table">
				<tr>
					<?php /* <td class="text-nowrap"><?php echo _l('dmg_tags'); ?></td>
					<td><?php 
					$tag_html = '';
					if(!($item->tag == '' && $item->tag == null)){
						$tag_arr = explode(',', $item->tag);
						foreach ($tag_arr as $key => $text) {
							$tag_html .= '<span class="badge badge-light mleft5">'.$text.'</span>';
						}
					}
					echo drawing_htmldecode($tag_html); ?></td> */ ?>
					<td class="text-nowrap"><?php echo _l('discipline'); ?></td>
					<td>
						<?php
						if (!empty($item->discipline)) {
							echo drawing_discipline($item->discipline);
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('dmg_signed_by'); ?></td>
					<td><?php
						$signed_by_html = '';
						if (!($item->signed_by == '' && $item->signed_by == null)) {
							$signed_by_arr = explode(',', $item->signed_by);
							foreach ($signed_by_arr as $key => $text) {
								$signed_by_html .= '<span class="badge badge-light mleft5">' . $text . '</span>';
							}
						}
						echo drawing_htmldecode($signed_by_html); ?></td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('dms_date'); ?></td>
					<td><?php echo _dt($item->dateadded); ?></td>
				</tr>
				<?php /* <tr>
					<td class="text-nowrap"><?php echo _l('dmg_due_date'); ?></td>
					<td><?php echo _dt($item->duedate); ?></td>
				</tr> */ ?>
				<tr>
					<td class="text-nowrap"><?php echo _l('issue_date'); ?></td>
					<td><?php echo _dt($item->issue_date); ?></td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('design_stage'); ?></td>
					<td>
						<?php
						$design_stages = [
							1 => 'Documents Under Review',
							2 => 'Briefs',
							3 => 'Concept',
							4 => 'Schematic',
							5 => 'Design Development',
							6 => 'Tender Documents',
							7 => 'Construction Documents',
							8 => 'Shop Drawings',
							9 => 'As-Built'
						];

						echo ($item->design_stage != null && isset($design_stages[$item->design_stage]))
							? $design_stages[$item->design_stage]
							: '';
						?>
					</td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('purpose'); ?></td>
					<td><?php echo ($item->purpose != null ? $item->purpose : ''); ?></td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('dmg_ocr_language'); ?></td>
					<td><?php echo ($item->ocr_language != null ? drawing_ufirst($item->ocr_language) : ''); ?></td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('dmg_document_number'); ?></td>
					<td><?php echo ($item->document_number != null ? drawing_ufirst($item->document_number) : ''); ?></td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('dmg_notes'); ?></td>
					<td><?php echo ($item->note != null ? drawing_nlbr($item->note) : ''); ?></td>
				</tr>
				<?php
				if (strpos($item->name, '.pdf') !== false) { ?>
					<tr>
						<td class="text-nowrap"><?php echo _l('DWG Drawing'); ?></td>
						<td><?php if (isset($item->pdf_attachment) && !empty($item->pdf_attachment)) : ?>

								<?php
								// Build the correct file path and URLs
								$file_path = FCPATH . 'modules/drawing_management/uploads/pdf_attachments/' . $item->id . '/' . $item->pdf_attachment;
								$download_url = base_url('modules/drawing_management/uploads/pdf_attachments/' . $item->id . '/' . rawurlencode($item->pdf_attachment));

								// Only show if file exists
								if (file_exists($file_path)) : ?>
									<a href="<?php echo $download_url; ?>" class="display-block mbot5" target="_blank" download>
										<?php echo htmlspecialchars($item->pdf_attachment); ?>
									</a>

								<?php endif; ?>

							<?php endif; ?>
						</td>
					</tr>

				<?php } ?>
				<tr>
					<td class="text-nowrap"><?php echo _l('dmg_controlled_document'); ?></td>
					<td><?php echo ($item->controlled_document == 1 ? 'Yes' : 'No'); ?></td>
				</tr>
				<tr>
					<td class="text-nowrap"><?php echo _l('status'); ?></td>
					<td><?php echo _l($item->status); ?></td>
				</tr>


				<?php
				$data_custom_field = [];
				if (!($item->custom_field == '' || $item->custom_field == null)) {
					$data_custom_field = json_decode($item->custom_field);
					if (count($data_custom_field) > 0) {
						foreach ($data_custom_field as $key => $customfield) {
							$item_html = '<tr>';
							$item_html .= '<td class="text-nowrap">' . $customfield->title . '</td>';
							$item_html .= '<td>' . drawing_dmg_convert_custom_field_value_to_string($customfield->value, $customfield->type) . '</td>';
							$item_html .= '</tr>';
							echo drawing_htmldecode($item_html);
						}
					}
				}
				?>


				<?php
				$data_file_selected = [];
				if ($item->related_file != '') {
					$data_file_selected = explode(',', $item->related_file);
				}
				if (count($data_file_selected) > 0) {
				?>
					<tr>
						<td class="text-nowrap" colspan="2">
							<?php echo _l('dmg_related_files'); ?>
							<table class="table no-mtop table-striped table-bordered">
								<thead>
									<tr>
										<th class="bold"><?php echo _l('dmg_file_name'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($data_file_selected as $key => $file_id) {
										$file_name = drawing_dmg_get_file_name($file_id);
										if ($file_name != '') {
									?>
											<tr>
												<td>
													<a href="<?php echo admin_url('drawing_management?id=' . $file_id); ?>" class="name mtop5 w100"><?php echo drawing_htmldecode($file_name); ?></a>
												</td>
											</tr>
									<?php
										}
									} ?>
								</tbody>
							</table>

						</td>
					</tr>
				<?php } ?>

				<?php
				$folder_id = $item->parent_id;
				$data_log_version = $this->drawing_management_model->get_log_version_by_parent($item->id);
				if (count($data_log_version) > 0) { ?>
					<tr>
						<td class="text-nowrap" colspan="2">
							<?php echo _l('dms_other_version'); ?>
							<table class="table no-mtop table-striped">
								<thead>
									<tr>
										<th class="bold"><?php echo _l('dms_date'); ?></th>
										<th class="bold"><?php echo _l('dmg_file_name'); ?></th>
										<th class="bold" width="5%"><?php echo _l('dmg_action'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($data_log_version as $key => $log) { ?>
										<tr>
											<td><?php echo _dt($log['dateadded']); ?></td>
											<td><?php echo drawing_htmldecode($log['name']); ?></td>
											<td>
												<div class="display-flex">
													<a href="<?php echo site_url('modules/drawing_management/uploads/log_versions/' . $log['parent_id'] . '/' . $log['name']); ?>" download class="mleft10 mright10" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('dmg_download'); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud">
															<polyline points="8 17 12 21 16 17" />
															<line x1="12" y1="12" x2="12" y2="21" />
															<path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29" />
														</svg>
													</a>
													<?php if (!$file_locked) { ?>
														<a href="javascript:void(0)" class="mleft10 mright10" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('dmg_restore'); ?>" onclick="restore_item_version(<?php echo drawing_htmldecode($log['id']); ?>)">
															<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw">
																<polyline points="1 4 1 10 7 10" />
																<polyline points="23 20 23 14 17 14" />
																<path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
															</svg>
														</a>

														<a href="<?php echo admin_url('drawing_management/delete_log/' . $log['id'] . '/' . $parent_id) ?>" class="mleft10 mright10 _swaldelete" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('dmg_delete'); ?>">
															<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
																<polyline points="3 6 5 6 21 6" />
																<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
																<line x1="10" y1="11" x2="10" y2="17" />
																<line x1="14" y1="11" x2="14" y2="17" />
															</svg>
														</a>
													<?php } ?>

												</div>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php } ?>

				<?php
				$data_log = drawing_get_audit_log_file($item->id);
				if (count($data_log) > 0) { ?>
					<tr>
						<td class="" colspan="2">
							<?php echo _l('dmg_audit_log'); ?>
							<table class="table no-mtop table-striped">
								<thead>
									<tr>
										<th class="bold"><?php echo _l('dms_date'); ?></th>
										<th class="bold"><?php echo _l('dmg_user'); ?></th>
										<th class="bold"><?php echo _l('dmg_action'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($data_log as $key => $log) { ?>
										<tr>
											<td><?php echo _dt($log['date']); ?></td>
											<td><?php echo drawing_htmldecode($log['user_name']); ?></td>
											<td><?php echo drawing_htmldecode($log['action']); ?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php } ?>

			</table>


		</div>
		<div class="col-md-4" style="background: #ffffff;">
			<!-- <div class="panel panel-default">
				<div class="panel-heading"><?php echo _l('dmg_reminders'); ?></div>
				<div class="panel-body no-border">
					<?php
					$data_reminder = $this->drawing_management_model->get_file_reminder($item->id);
					if (is_array($data_reminder) && count($data_reminder) > 0) { ?>
						<ul class="list-group list-group-flush list-group-custom" role="tablist">
							<?php foreach ($data_reminder as $key => $value) { ?>
								<li class="list-group-item list-group-item-action display-flex no-padding-left no-padding-right" data-toggle="list" role="tab">
									<div class="w100">
										<?php echo drawing_htmldecode($value['date']); ?>
									</div>
									<div class="display-flex">

										<a href="javascript:void(0)" class="mleft10"
											data-toggle="tooltip"
											data-placement="top"
											data-original-title="<?php echo _l('dmg_edit'); ?>"

											data-file_id="<?php echo drawing_htmldecode($item->id); ?>"
											data-date="<?php echo drawing_htmldecode($value['date']); ?>"
											data-email="<?php echo drawing_htmldecode($value['email']); ?>"
											data-message="<?php echo drawing_htmldecode($value['message']); ?>"

											onclick="edit_remind(this, <?php echo drawing_htmldecode($value['id']); ?>)">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
												<path d="M12 20h9" />
												<path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
											</svg>
										</a>

										<a href="<?php echo admin_url('drawing_management/delete_remider/' . $value['id'] . '/' . $item->id) ?>" class="mleft10 _swaldelete" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('dmg_delete'); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
												<polyline points="3 6 5 6 21 6" />
												<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
												<line x1="10" y1="11" x2="10" y2="17" />
												<line x1="14" y1="11" x2="14" y2="17" />
											</svg>
										</a>
									</div>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>

					<button class="btn btn-default display-flex bulk-action-btn w100 justify-content-center" onclick="remider(<?php echo drawing_htmldecode($parent_id); ?>)">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
							<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
							<path d="M13.73 21a2 2 0 0 1-3.46 0" />
						</svg>
						<span class="mleft5 mtop2">
							<?php echo _l('dmg_new_reminder'); ?>
						</span>
					</button>

				</div>
			</div> -->

			<!-- Share -->
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo _l('dmg_share_to'); ?></div>
				<div class="panel-body no-border">
					<?php
					$data_share = $this->drawing_management_model->get_share_log('', 'item_id = ' . $item->id);
					if (is_array($data_share) && count($data_share) > 0) { ?>
						<ul class="list-group list-group-flush list-group-custom" role="tablist">
							<?php foreach ($data_share as $key => $value) { ?>
								<li class="list-group-item list-group-item-action display-flex no-padding-left no-padding-right" data-toggle="list" role="tab">
									<div class="w100">
										<?php
										$data_list = $value;
										echo drawing_htmldecode($this->drawing_management_model->get_share_user_list($data_list)); ?>
									</div>
									<div class="display-flex">

										<a href="javascript:void(0)" class="mleft10"
											data-toggle="tooltip"
											data-placement="top"
											data-original-title="<?php echo _l('dmg_edit'); ?>"

											data-id="<?php echo drawing_htmldecode($value['id']); ?>"
											data-type="<?php echo drawing_htmldecode($item->filetype); ?>"
											data-item_id="<?php echo drawing_htmldecode($value['item_id']); ?>"
											data-share_to="<?php echo drawing_htmldecode($value['share_to']); ?>"
											data-permission="<?php echo drawing_htmldecode($value['permission']); ?>"
											data-vendor="<?php echo drawing_htmldecode($value['vendor']); ?>"
											data-vendor_contact="<?php echo drawing_htmldecode($value['vendor_contact']); ?>"
											data-staff="<?php echo drawing_htmldecode($value['staff']); ?>"
											data-customer_group="<?php echo drawing_htmldecode($value['customer_group']); ?>"
											data-expiration="<?php echo drawing_htmldecode($value['expiration']); ?>"
											data-expiration_date="<?php echo drawing_htmldecode($value['expiration_date']); ?>"
											data-message="<?php echo drawing_htmldecode($value['message']); ?>"

											onclick="edit_share(this, <?php echo drawing_htmldecode($value['id']); ?>)">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
												<path d="M12 20h9" />
												<path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
											</svg>
										</a>

										<a href="<?php echo admin_url('drawing_management/delete_share/' . $value['id'] . '/' . $item->id) ?>" class="mleft10 _swaldelete" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('dmg_delete'); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
												<polyline points="3 6 5 6 21 6" />
												<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
												<line x1="10" y1="11" x2="10" y2="17" />
												<line x1="14" y1="11" x2="14" y2="17" />
											</svg>
										</a>
									</div>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>

					<button class="btn btn-default display-flex bulk-action-btn w100 justify-content-center" onclick="new_share(this,<?php echo drawing_htmldecode($parent_id); ?>)">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2">
							<circle cx="18" cy="5" r="3" />
							<circle cx="6" cy="12" r="3" />
							<circle cx="18" cy="19" r="3" />
							<line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
							<line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
						</svg>
						<span class="mleft5 mtop2">
							<?php echo _l('dmg_new_share'); ?>
						</span>
					</button>

				</div>
			</div>
			<!-- End share -->
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo _l('Task'); ?></div>
				<div class="panel-body no-border">
					<?php init_relation_tasks_table(array('data-new-rel-id' => $item->id, 'data-new-rel-type' => 'drawing')); ?>
				</div>
			</div>
			<div class="panel-default no-border">
				<div class="panel-body no-border">

					<?php if ($item->creator_id == get_staff_user_id() && $item->creator_type == 'staff') { ?>
						<hr>
					<?php } ?>

					<?php if (false) { ?>
						<?php if ($item->sign_approve == 0) { ?>
							<a href="javascript:void(0)" class="btn btn-default w100 mtop5 mbot5" onclick="send_request_approve(<?php echo drawing_htmldecode($item->id); ?>, 'eid')">
								<i class="fa fa-check-square"></i> <?php echo _l('dmg_request_approve_with_eid'); ?>
							</a>
						<?php } ?>
						<?php if ($item->sign_approve == -1 || $item->sign_approve == 1 || $item->sign_approve == 2) {
							$color_class = ' btn-warning';
							if ($item->sign_approve == 1) {
								$color_class = ' btn-success';
							}
							if ($item->sign_approve == 2) {
								$color_class = ' btn-danger';
							}
						?>
							<a href="<?php echo admin_url('drawing_management/detail_sign_approve/' . $item->hash); ?>" class="btn w100 mtop5 mbot5<?php echo drawing_htmldecode($color_class); ?>">
								<i class="fa fa-check-square"></i> <?php echo _l('dmg_view_eid_request_approve'); ?>
							</a>
						<?php } ?>
					<?php } ?>

					<hr>
					<?php if (!(strpos($item->name, '.xlsx') === false) || !(strpos($item->name, '.xls') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> <?php echo _l('dmg_view_in_excel'); ?>
						</a>
					<?php } ?>

					<?php if (!(strpos($item->name, '.docx') === false) || !(strpos($item->name, '.doc') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> <?php echo _l('dmg_view_in_word'); ?>
						</a>
						<?php if (!$file_locked) { ?>
							<a href="<?php echo admin_url('drawing_management/editdocument?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
								<i class="fa fa-pencil-square"></i> <?php echo _l('dmg_edit_in_word'); ?>
							</a>
						<?php } ?>
					<?php } ?>

					<?php if (!(strpos($item->name, '.pdf') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> <?php echo _l('dmg_view_pdf'); ?>
						</a>
					<?php } ?>
					<?php if (!(strpos($item->pdf_attachment, '.dwg') === false) || !(strpos($item->pdf_attachment, '.xref') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview_file_pdf_dwg?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> View DWG
						</a>
					<?php } ?>
					<?php if (!(strpos($item->name, '.dwg') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> View DWG
						</a>
					<?php } ?>

					<?php if (!(strpos($item->name, '.dxf') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> View DXF
						</a>
					<?php } ?>

					<?php if (!(strpos($item->filetype, 'image') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> <?php echo _l('dmg_view_image'); ?>
						</a>
					<?php } ?>
					<?php
					$video_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $item->parent_id . '/' . $item->name;
					if (is_html5_video($video_path)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview?id=' . $item->id) ?>" target="_blank" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-eye"></i> <?php echo _l('dmg_view_video'); ?>
						</a>
					<?php } ?>

					<?php if (!$file_locked) {
						$edit_url = '';
						if ($share_to_me == 1) {
							$edit_url = admin_url('drawing_management?share_to_me=1&id=' . $item->id . '&edit=1');
						} else {
							$edit_url = admin_url('drawing_management?id=' . $item->id . '&edit=1&pid=' . $item->$parent_id);
						}
					?>
						<a href="<?php echo drawing_htmldecode($edit_url); ?>" class="btn btn-default w100 mtop5 mbot5">
							<i class="fa fa-pencil-square"></i> <?php echo _l('dmg_edit_metadata'); ?>
						</a>
					<?php } ?>

					<?php
					if (!$file_locked) { ?>
						<a href="javascript:void(0)" class="btn btn-primary w100 mtop5 mbot5" onclick="upload_new_version(<?php echo drawing_htmldecode($item->id); ?>)">
							<i class="fa fa-file"></i> <?php echo _l('dmg_upload_new_version'); ?>
						</a>
					<?php } ?>

					<?php if ($item->creator_type == 'staff') { ?>
						<?php if ($item->approve == 0) { ?>
							<a href="javascript:void(0)" class="btn btn-default w100 mtop5 mbot5" onclick="send_request_approve(<?php echo drawing_htmldecode($item->id); ?>, 'normal')">
								<i class="fa fa-check-square"></i> <?php echo _l('dmg_send_request_approve'); ?>
							</a>
						<?php } ?>
						<?php if ($item->approve == -1 || $item->approve == 1 || $item->approve == 2) {
							$color_class = ' btn-warning';
							if ($item->approve == 1) {
								$color_class = ' btn-success';
							}
							if ($item->approve == 2) {
								$color_class = ' btn-danger';
							}
						?>
							<a href="<?php echo admin_url('drawing_management/detail_approve/' . $item->hash); ?>" class="btn w100 mtop5 mbot5<?php echo drawing_htmldecode($color_class); ?>">
								<i class="fa fa-check-square"></i> <?php echo _l('dmg_view_request_approve'); ?>
							</a>
						<?php } ?>
					<?php } ?>
					<?php if (!(strpos($item->name, '.pdf') === false)) { ?>
						<a href="<?php echo admin_url('drawing_management/preview_superseder?id=' . $item->id) ?>"
							target="_blank"
							class="btn btn-default w100 mtop5 mbot5"
							onclick="return confirm('<?php echo _l('Are you sure you want to supersede this document?'); ?>')">
							<i class="fa fa-arrow-up"></i> <?php echo _l('Supersede'); ?>
						</a>
					<?php } ?>
					<?php
					if (!$file_locked) {
						$parameter = $item->id;
						if ($item->locked == 1) {
							$parameter = $parameter . ',\'unlock\'';
						} else {
							$parameter = $parameter . ',\'lock\'';
						}
						$lock_function = 'lock_unlock_file(' . $parameter . ')';
					?>
						<a href="javascript:void(0)" class="btn btn-primary w100 mtop5 mbot5" onclick="<?php echo drawing_htmldecode($lock_function); ?>">
							<?php echo ($item->locked == 1 ? '<i class="fa fa-unlock"></i> ' . _l('dmg_unlock_file') : '<i class="fa fa-lock"></i> ' . _l('dmg_lock_file')); ?>
						</a>
					<?php } ?>

				</div>
			</div>

		</div>
	</div>

	<div class="modal upload_new_version" id="upload_new_version" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title add-title title1"><?php echo _l('dmg_upload_new_version'); ?></h4>
				</div>
				<?php echo form_open_multipart(admin_url('drawing_management/upload_version_file/' . $parent_id), array('id' => 'form_upload_file')); ?>
				<div class="modal-body">
					<?php
					$redirect_type = '';
					if ($share_to_me == 1) {
						$redirect_type = 'share_to_me';
					}
					?>
					<input type="hidden" id="redirect" name="redirect" value="<?php echo drawing_htmldecode($redirect_type); ?>">
					<div class="file-form-group file-form-update-version">
						<input type="file" id="file_version" name="file[]" multiple="">
						<div class="file-form-preview hide">
							<ul class="selectedFiles list-group list-group-flush mtop15" id="selectedFiles"></ul>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-primary" onclick="continue_action()"><?php echo _l('dmg_continue'); ?></button>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>


	<div class="modal remider" id="remider" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title add-title title1"><?php echo _l('dmg_create_remider'); ?></h4>
					<h4 class="modal-title edit-title title1"><?php echo _l('dmg_edit_remider'); ?></h4>
				</div>
				<?php echo form_open(admin_url('drawing_management/create_remider/' . $parent_id), array('id' => 'form_create_remider')); ?>
				<div class="modal-body">
					<div class="row">
						<?php
						$redirect_type = '';
						if ($share_to_me == 1) {
							$redirect_type = 'share_to_me';
						}
						?>
						<input type="hidden" id="redirect" name="redirect" value="<?php echo drawing_htmldecode($redirect_type); ?>">
						<div class="col-md-12">
							<input type="hidden" name="file_id" value="">
							<input type="hidden" name="id" value="">
							<?php echo render_datetime_input('date', 'dms_date', date('Y-m-d H:i')); ?>
						</div>
						<div class="col-md-12">
							<?php echo render_input('email', 'dmg_email', '', 'email'); ?>
						</div>
						<div class="col-md-12">
							<?php echo render_textarea('message', 'dmg_message'); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-primary"><?php echo _l('dmg_submit'); ?></button>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>

	<div class="modal send_request_approve_modal" id="send_request_approve_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title add-title title1"><?php echo _l('dmg_send_request_approve'); ?></h4>
					<h4 class="modal-title add-title title2 hide"><?php echo _l('dmg_request_approve_with_eid'); ?></h4>
				</div>
				<?php echo form_open_multipart(admin_url('drawing_management/send_request_approve/' . $parent_id), array('id' => 'form_upload_file')); ?>
				<div class="modal-body">
					<input type="hidden" name="id" value="<?php echo drawing_htmldecode($parent_id); ?>">
					<input type="hidden" name="approve_type" value="">
					<div class="row">
						<div class="col-md-12">
							<?php echo render_textarea('resolution', 'dmg_resolution'); ?>
						</div>
						<div class="col-md-6">
							<div class="checkbox checkbox-inline checkbox-primary">
								<input type="checkbox" name="move_after_approval" id="move_after_approval" value="1">
								<label for="move_after_approval"><?php echo _l('dmg_move_after_approval'); ?></label>
							</div>
						</div>

						<div class="col-md-6">
							<div class="checkbox checkbox-inline checkbox-primary">
								<input type="checkbox" name="show_files_metadata" id="show_files_metadata" value="1">
								<label for="show_files_metadata"><?php echo _l('dmg_show_files_metadata'); ?></label>
							</div>
						</div>
						<div class="col-md-12 list hide">
							<div class="panel panel-default no-mbot mtop15">
								<div class="panel-heading">
									<?php echo _l('dmg_select_folder'); ?>
								</div>
								<div class="panel-body no-border">

								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-primary" onclick="continue_action()"><?php echo _l('dmg_send_request'); ?></button>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>

<?php } ?>