<?php
if (isset($item)) {
	$redirect_type = '';
	if ($share_to_me == 1) {
		$redirect_type = 'share_to_me';
	} ?>
	<?php echo form_open_multipart(admin_url('drawing_management/edit_file'), array('id' => 'edit_file_form')); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<input type="hidden" name="id" value="<?php echo drawing_htmldecode($item->id); ?>">
				<input type="hidden" name="default_parent_id" value="<?php echo drawing_htmldecode($parent_id); ?>">
				<input type="hidden" name="redirect_type" value="<?php echo drawing_htmldecode($redirect_type); ?>">
				<div class="col-md-12">
					<?php echo render_input('name', 'dmg_name', $item->name); ?>
				</div>
				<div class="col-md-12">
					<?php /* <div class="form-group">
						<div id="inputTagsWrapper">
							<label for="tag" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
							<input type="text" class="tagsinput" id="tag" name="tag" value="<?php echo ($item->tag != null ? drawing_htmldecode($item->tag) : ''); ?>" data-role="tagsinput">
						</div>
					</div> */ ?>
					<?php
					$selected = [];
					if (!empty($item->discipline)) {
						$selected = explode(',', $item->discipline);
					}
					echo render_select('discipline[]', $discipline, ['id', 'name'], 'discipline', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
					?>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<div id="inputTagsWrapper">
							<label for="signed_by" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('dmg_signed_by'); ?></label>
							<input type="text" class="tagsinput" id="signed_by" name="signed_by" value="<?php echo ($item->signed_by != null ? drawing_htmldecode($item->signed_by) : ''); ?>" data-role="tagsinput">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<?php echo render_datetime_input('dateadded', 'dms_date', $item->dateadded); ?>
				</div>
				<?php /* <div class="col-sm-6">
					<?php echo render_datetime_input('duedate', 'dmg_due_date', $item->duedate); ?>				
				</div> */ ?>
				<div class="col-sm-6">
					<?php echo render_datetime_input('issue_date', 'issue_date', $item->issue_date); ?>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="design_stage" class="control-label"><?php echo _l('design_stage'); ?></label>
						<select id="design_stage" name="design_stage" class="selectpicker" data-width="100%" data-none-selected-text="None selected" tabindex="-98">
							<option value=""></option>
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

							foreach ($design_stages as $value => $label) {
								$selected = ($item->design_stage == $value) ? 'selected' : '';
								echo "<option value=\"$value\" $selected>$label</option>";
							}
							?>
						</select>
					</div>
				</div>
				<?php

				$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : null;

				// Set selected status based on database value or default to "Issued for Review" when `pid` is 19
				$selectedStatus = !empty($item->purpose) ? $item->purpose : ($pid === 19 ? 'Issued for review' : '');

				$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : null;
				$selectedStatus = !empty($item->purpose) ? $item->purpose : ($pid === 23 ? 'Issued for approval' : '');

				$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : null;
				$selectedStatus = !empty($item->purpose) ? $item->purpose : ($pid === 23 ? 'Issued for approval' : '');

				$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : null;
				$selectedStatus = !empty($item->purpose) ? $item->purpose : ($pid === 24 ? 'Issued for tender' : '');

				$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : null;
				$selectedStatus = !empty($item->purpose) ? $item->purpose : ($pid === 25 ? 'Issued for construction' : '');
				?>

				<div class="col-sm-6">
					<div class="form-group">
						<label for="purpose" class="control-label"><?php echo _l('purpose'); ?></label>
						<select id="purpose" name="purpose" class="selectpicker" data-width="100%" data-none-selected-text="None selected" tabindex="-98">
							<option value=""></option>
							<option value="Issued for Information" <?php echo ($item->purpose == "Issued for Information" ? 'selected' : '') ?>>Issued for Information</option>
							<option value="Issued for review" <?php echo $selectedStatus === 'Issued for review' ? 'selected' : ''; ?>>Issued for review</option>
							<option value="Issued for approval" <?php echo $selectedStatus === 'Issued for approval' ? 'selected' : ''; ?>>Issued for approval</option>
							<option value="Issued for tender" <?php echo $selectedStatus === 'Issued for tender	' ? 'selected' : ''; ?>>Issued for tender</option>
							<option value="Issued for construction" <?php echo $selectedStatus === 'Issued for construction' ? 'selected' : ''; ?>>Issued for construction</option>


						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" app-field-wrapper="duedate">
						<label for="ocr_language" class="control-label"><?php echo _l('dmg_ocr_language'); ?></label>
						<select id="ocr_language" name="ocr_language" class="selectpicker" data-width="100%" data-none-selected-text="None selected" data-live-search="true" tabindex="-98">
							<option value=""></option>
							<?php foreach ($this->app->get_available_languages() as $user_lang) { ?>
								<option value="<?php echo drawing_htmldecode($user_lang); ?>" <?php echo ($item->ocr_language == $user_lang ? 'selected' : '') ?>><?php echo drawing_ufirst($user_lang); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<?php echo render_input('document_number', 'dmg_document_number', $item->document_number); ?>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="status" class="control-label"><?php echo _l('status'); ?></label>
						<select id="status" name="status" class="selectpicker" data-width="100%" data-none-selected-text="None selected" tabindex="-98">
							<option value=""></option>
							<option value="under_review" <?php echo ($item->status == "under_review" ? 'selected' : '') ?>>Under Review</option>
							<option value="released" <?php echo ($item->status == "released" ? 'selected' : '') ?>>Released</option>
							<option value="released_with_comments" <?php echo ($item->status == "released_with_comments" ? 'selected' : '') ?>>Released with comments</option>
							<option value="rejected" <?php echo ($item->status == "rejected" ? 'selected' : '') ?>>Rejected</option>
						</select>
					</div>
				</div>
				<?php
				$id = isset($_GET['pid']) ? (int)$_GET['pid'] : null;
				?>

				<div class="col-sm-6">
					<label>
						Controlled Document
						<input type="checkbox" id="controlled_document" name="controlled_document"
							<?php echo ($item->controlled_document || $id === 25) ? 'checked' : ''; ?>>
					</label>
				</div>
				<div class="col-md-6" style="clear: both;">
					<?php echo render_textarea('note', 'dmg_notes', $item->note); ?>
				</div>
				<?php if (strpos($item->name, '.pdf') !== false) { ?>
					<div class="col-md-6">
						CAD Drawing
						<input type="file" name="pdf_attachment" id="pdf_attachment" class="form-control" accept=".dwg,.xref">
					</div>
					<?php if (isset($item->pdf_attachment) && !empty($item->pdf_attachment)) : ?>
						<div class="col-md-3" style="margin-top: 13px; display: flex;justify-content: space-between;">
							<?php
							// Build the correct file path and URLs
							$file_path = FCPATH . 'modules/drawing_management/uploads/pdf_attachments/' . $item->id . '/' . $item->pdf_attachment;
							$download_url = base_url('modules/drawing_management/uploads/pdf_attachments/' . $item->id . '/' . rawurlencode($item->pdf_attachment));
							$delete_url = admin_url('drawing_management/delete_pdf_attachment/' . $item->id);

							// Only show if file exists
							if (file_exists($file_path)) : ?>
								<a href="<?php echo $download_url; ?>" class="display-block mbot5" target="_blank" download>
									<?php echo htmlspecialchars($item->pdf_attachment); ?>
								</a>
								<a href="<?php echo $delete_url; ?>" class="text-danger _delete">
									<?php echo _l('delete'); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				<?php } ?>

				<div class="col-md-12">
					<div class="row">
						<div class="col-md-6">
							<?php
							$data_custom_field_list = $this->drawing_management_model->get_custom_fields('', '', 'id, title');
							echo render_select('all_custom_field', $data_custom_field_list, array('id', 'title'), 'dmg_custom_fields'); ?>
						</div>
						<div class="col-md-6 ptop5">
							<button class="btn btn-default pull-left mtop25" id="add_custom_field" type="button"><i class="fa fa-plus mtop5"></i></button>
							<a class=" pull-left mtop35 mleft20" href="<?php echo admin_url('drawing_management/settings?tab=custom_field'); ?>"><span class="mtop5">&#8594; <?php echo _l('dmg_go_to_manage'); ?></span></a>
						</div>
						<div class="col-md-12 custom_field_fr">
							<?php
							$data_custom_field = [];
							if (!($item->custom_field == '' || $item->custom_field == null)) {
								$data_custom_field = json_decode($item->custom_field);
							} ?>
							<ul class="selectedFiles list-group list-group-flush mtop10 <?php echo (count($data_custom_field) == 0 ? 'hide' : '') ?>" id="custom_field_list">
								<?php
								if (count($data_custom_field) > 0) {
									$required = 1;
									foreach ($data_custom_field as $key => $customfield) {
										$html = '';
										switch ($customfield->type) {
											case 'select':
												$data['option'] = $customfield->option;
												$data['title'] = $customfield->title;
												$data['id'] = $customfield->custom_field_id;
												$data['required'] = $required;
												$data['select'] = $customfield->value;
												$html = $this->load->view('includes/controls/select', $data, true);
												break;
											case 'multi_select':
												$data['option'] = $customfield->option;
												$data['title'] = $customfield->title;
												$data['id'] = $customfield->custom_field_id;
												$data['required'] = $required;
												$data['select'] = $customfield->value;
												$html = $this->load->view('includes/controls/multi_select', $data, true);
												break;
											case 'checkbox':
												$data['option'] = $customfield->option;
												$data['title'] = $customfield->title;
												$data['id'] = $customfield->custom_field_id;
												$data['required'] = $required;
												$data['select'] = $customfield->value;
												$html = $this->load->view('includes/controls/checkbox', $data, true);
												break;
											case 'radio_button':
												$data['option'] = $customfield->option;
												$data['title'] = $customfield->title;
												$data['id'] = $customfield->custom_field_id;
												$data['required'] = $required;
												$data['select'] = $customfield->value;
												$html = $this->load->view('includes/controls/radio_button', $data, true);
												break;
											case 'textarea':
												$data['id'] = $customfield->custom_field_id;
												$data['title'] = $customfield->title;
												$data['required'] = $required;
												$data['value'] = $customfield->value;
												$html = $this->load->view('includes/controls/textarea', $data, true);
												break;
											case 'numberfield':
												$data['id'] = $customfield->custom_field_id;
												$data['title'] = $customfield->title;
												$data['required'] = $required;
												$data['value'] = $customfield->value;
												$html = $this->load->view('includes/controls/numberfield', $data, true);
												break;
											case 'textfield':
												$data['id'] = $customfield->custom_field_id;
												$data['title'] = $customfield->title;
												$data['required'] = $required;
												$data['value'] = $customfield->value;
												$html = $this->load->view('includes/controls/textfield', $data, true);
												break;
										}
										$item_class = 'field-item-' . $customfield->custom_field_id;
										$item_html = '<li class="list-group-item list-group-item-action display-flex ' . $item_class . '">';
										$item_html .= '<div class="control w100">' . $html . '</div>';
										$item_html .= '<input type="hidden" name="field_id[]" value="' . $customfield->custom_field_id . '">';
										$item_html .= '<button class="btn btn-sm btn-link remove-attachment" onclick="remove_attachment(this,\'customfield\')" type="button">';
										$item_html .= '<i class="fa fa-times"></i>';
										$item_html .= '</button>';
										$item_html .= '</li>';
										echo drawing_htmldecode($item_html);
									}
								}
								?>
							</ul>
						</div>
					</div>
				</div>

				<div class="col-md-12">
					<div class="row">
						<div class="col-md-6">
							<?php
							$data_file_list = $this->drawing_management_model->get_item('', 'filetype != \'folder\' AND parent_id = ' . $item->parent_id . ' AND id != ' . $item->id, 'id, name');
							echo render_select('all_file', $data_file_list, array('id', 'name'), 'dmg_related_files'); ?>
						</div>
						<div class="col-md-6 ptop5">
							<button class="btn btn-default pull-left mtop25" id="add_related_file" type="button"><i class="fa fa-plus mtop5"></i></button>
						</div>
						<div class="col-md-12 related_file_fr">
							<?php
							$data_file_selected = [];
							if ($item->related_file != '') {
								$data_file_selected = explode(',', $item->related_file); ?>
							<?php } ?>
							<ul class="selectedFiles list-group list-group-flush mtop10 <?php echo (count($data_file_selected) == 0 ? 'hide' : '') ?>" id="related_file_list">
								<?php
								if (count($data_file_selected) > 0) {
									foreach ($data_file_selected as $key => $file_id) {
										$file_name = drawing_dmg_get_file_name($file_id);
										if ($file_name != '') {
								?>
											<li class="list-group-item list-group-item-action display-flex">
												<div class="name mtop7 w100"><?php echo drawing_htmldecode($file_name); ?></div>
												<input type="hidden" name="related_file[]" value="<?php echo drawing_htmldecode($file_id); ?>">
												<button class="btn btn-sm btn-link remove-attachment" onclick="remove_attachment(this,'file')" type="button">
													<i class="fa fa-times"></i>
												</button>
											</li>
								<?php
										}
									}
								}
								?>
							</ul>
						</div>
					</div>
				</div>

				<div class="col-md-12">
					<hr>
					<button class="btn btn-primary pull-right"><?php echo _l('dmg_save'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
<?php } ?>