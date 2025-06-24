<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<input type="hidden" name="parent_id" value="<?php echo drawing_htmldecode($parent_id); ?>">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style>
	/* Basic styles for dropdown */
	.dropdown-menu {
		width: 100%;
		border: 1px solid #ddd;
		background-color: #fff;
		max-height: 200px;
		overflow-y: auto;
	}

	.dropdown-item {
		padding: 8px 12px;
		display: block;
		color: #333;
		text-decoration: none;
	}

	.dropdown-item:hover {
		background-color: #f0f0f0;
	}

	.dropdown-item.disabled {
		color: #999;
		pointer-events: none;
	}

	.vendor_contact {
		padding-left: 0px;
		padding-right: 1px;
	}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<div class="col-md-3">
									<h4>
										<?php echo drawing_htmldecode($title); ?>
									</h4>
								</div>

								<?php if ($share_to_me == 0) { ?>
									<div class="col-md-9 btn-tool">
										<?php if (isset($item) && $item->filetype == 'folder') {	?>
											<button class="btn btn-default pull-right mright10 display-flex default-tool" onclick="open_upload()">
												<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload-cloud">
													<polyline points="16 16 12 12 8 16" />
													<line x1="12" y1="12" x2="12" y2="21" />
													<path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
													<polyline points="16 16 12 12 8 16" />
												</svg>
												<span class="mleft5 mtop2">
													<?php echo _l('dmg_upload'); ?>
												</span>
											</button>

											<button class="btn btn-default pull-right mright10 display-flex default-tool" onclick="create_folder()">
												<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder-plus">
													<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
													<line x1="12" y1="11" x2="12" y2="17" />
													<line x1="9" y1="14" x2="15" y2="14" />
												</svg>
												<span class="mleft5 mtop2">
													<?php echo _l('dmg_new_folder'); ?>
												</span>
											</button>


											<!-- <?php echo render_input('search_new', '', '', 'text', ['placeholder' => _l('dmg_search_name_tag_etc')], [], 'pull-right default-tool'); ?> -->
											<div class="input-group">
												<!-- <input type="text" class="form-control" id="searchBox" placeholder="Type to search files or folders" autocomplete="off"> -->
												<?php echo render_input('searchBox', '', '', 'text', ['placeholder' => 'Type to search files or folder', 'style' => 'width: 700px;'], [], 'pull-right default-tool'); ?>
												<div id="dropdown" class="dropdown-menu" style="display: none; position: absolute; z-index: 1000;"></div>
											</div>



											<?php } else {
											if (isset($item) && $edit != 1) {
											?>
												<!-- <button class="btn btn-default pull-right mright10 display-flex bulk-action-btn" onclick="remider(<?php echo drawing_htmldecode($parent_id); ?>)">
													<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
														<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
														<path d="M13.73 21a2 2 0 0 1-3.46 0" />
													</svg>
													<span class="mleft5 mtop2">
														<?php echo _l('dmg_remind'); ?>
													</span>
												</button> -->
										<?php
											}
										}
										?>

										<!-- For bulk select -->
										<button class="btn btn-default pull-right mright10 display-flex bulk-action-btn hide" onclick="bulk_move_item()">
											<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-right">
												<polyline points="13 17 18 12 13 7" />
												<polyline points="6 17 11 12 6 7" />
											</svg>
											<span class="mleft5 mtop2">
												<?php echo _l('dmg_move'); ?>
											</span>
										</button>
										<a href="<?php echo admin_url('drawing_management/bulk_download_item?parent_id=' . $parent_id . '&id='); ?>" class="btn btn-default pull-right mright10 display-flex bulk-action-btn bulk-download-btn hide">
											<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
												<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
												<polyline points="7 10 12 15 17 10" />
												<line x1="12" y1="15" x2="12" y2="3" />
											</svg>
											<span class="mleft5 mtop2">
												<?php echo _l('dmg_dowload'); ?>
											</span>
										</a>
										<button class="btn btn-default pull-right mright10 display-flex bulk-action-btn hide" onclick="bulk_duplicate_item()">
											<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy">
												<rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
												<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
											</svg>
											<span class="mleft5 mtop2">
												<?php echo _l('dmg_duplicate'); ?>
											</span>
										</button>
										<button class="btn btn-default pull-right mright10 display-flex bulk-action-btn hide" onclick="bulk_delete_item()">
											<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
												<polyline points="3 6 5 6 21 6" />
												<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
												<line x1="10" y1="11" x2="10" y2="17" />
												<line x1="14" y1="11" x2="14" y2="17" />
											</svg>
											<span class="mleft5 mtop2">
												<?php echo _l('dmg_delete'); ?>
											</span>
										</button>
										<!-- For bulk select -->
									</div>
									<?php } else {
									if (($share_to_me == 1 && $parent_id == 0) || (isset($item) && $item->filetype == 'folder' && !drawing_check_share_permission($parent_id, 'upload_only'))) { ?>
										<?php echo render_input('search', '', '', 'text', ['placeholder' => _l('dmg_search_name_tag_etc')], [], 'pull-right default-tool'); ?>
									<?php } ?>
								<?php } ?>
								<div class="col-md-12">
									<hr>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 border-right">
								<ul class="list-group list-group-flush list-group-custom" role="tablist">
									<?php
									foreach ($root_folder as $key => $value) {
										$active = '';

										if ($master_parent_id == $value['id'] && $share_to_me == 0) {
											$active = ' active';
										}
									?>
										<li class="list-group-item list-group-item-action display-flex<?php echo drawing_htmldecode($active); ?>" data-toggle="list" role="tab">
											<a href="<?php echo admin_url('drawing_management?id=' . $value['id']); ?>" class="w100">
												<?php echo drawing_htmldecode($value['name']); ?>
											</a>

											<div class="dropdown">
												<button class="btn btn-tool pull-right dropdown-toggle" role="button" id="dropdown_menu_<?php echo drawing_htmldecode($value['id']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal">
														<circle cx="12" cy="12" r="1" />
														<circle cx="19" cy="12" r="1" />
														<circle cx="5" cy="12" r="1" />
													</svg>
												</button>
												<ul class="dropdown-menu" aria-labelledby="dropdown_menu_<?php echo drawing_htmldecode($value['id']); ?>">
													<!-- <li class="no-padding">
														<a href="#" data-name="<?php echo drawing_htmldecode($value['name']); ?>" onclick="edit_section(this, '<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_edit') ?></a>
													</li> -->
													<li class="no-padding">
														<a href="#" data-type="<?php echo drawing_htmldecode($value['filetype']); ?>" onclick="share_document(this, '<?php echo drawing_htmldecode($value['id']); ?>')"><?php echo _l('dmg_share') ?></a>
													</li>
													<li class="no-padding">
														<a href="<?php echo admin_url('drawing_management/download_folder/' . $value['id']); ?>"><?php echo _l('dmg_dowload') ?></a>
													</li>
													<?php if ($value['is_primary'] == 0) { ?>
														<li class="no-padding">
															<a class="_swaldelete" href="<?php echo admin_url('drawing_management/delete_section/' . $value['id'] . '/' . $parent_id) ?>"><?php echo _l('dmg_delete') ?></a>
														</li>
													<?php } ?>
												</ul>
											</div>

										</li>
									<?php } ?>
									<?php /* <li class="list-group-item list-group-item-action">
										<a href="javascript:void(0)" onclick="create_new_section()">
											<i class="fa fa-plus"></i> <?php echo _l('dmg_create_new_section'); ?>											
										</a>
									</li> */ ?>
								</ul>
								<hr>
								<ul class="list-group list-group-flush list-group-custom" role="tablist">
									<?php /* <li class="list-group-item list-group-item-action display-flex<?php echo ($share_to_me == 1 ? ' active' : ''); ?>" data-toggle="list" role="tab">
										<a href="<?php echo admin_url('drawing_management?share_to_me=1&id=0'); ?>" class="w100 display-flex">
											<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>  
											<span class="mtop2 mleft5">
												<?php echo _l('dmg_share_to_me'); ?>
												<?php 
												$share_items = $this->drawing_management_model->get_item('','id IN ('.$share_id.')', 'name, id, dateadded, filetype');											
												if($share_items && is_array($share_items) && count($share_items) > 0){ ?>
													<span class="label bg-warning mleft10"><strong><?php echo count($share_items); ?></strong></span>
												<?php } ?>
											</span>
										</a>
									</li> */ ?>
									<li class="list-group-item list-group-item-action display-flex<?php echo ($my_approval == 1 ? ' active' : ''); ?>" data-toggle="list" role="tab">
										<a href="<?php echo admin_url('drawing_management?my_approval=1&id=0'); ?>" class="w100 display-flex">
											<svg viewBox="0 0 24 24" width="23" height="23" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
												<polyline points="9 11 12 14 22 4"></polyline>
												<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
											</svg>
											<span class="mtop2 mleft5">
												<?php echo _l('dmg_my_approval'); ?>
												<?php if ($approve_items && is_array($approve_items) && count($approve_items) > 0) { ?>
													<span class="label bg-warning mleft10"><strong><?php echo count($approve_items); ?></strong></span>
												<?php } ?>
											</span>
										</a>
									</li>
									<li class="list-group-item list-group-item-action display-flex<?php echo ($electronic_signing == 1 ? ' active' : ''); ?>" data-toggle="list" role="tab">
										<a href="<?php echo admin_url('drawing_management?electronic_signing=1&id=0'); ?>" class="w100 display-flex">
											<svg viewBox="0 0 24 24" width="23" height="23" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
												<polyline points="9 11 12 14 22 4"></polyline>
												<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
											</svg>
											<span class="mtop2 mleft5">
												<?php echo _l('dmg_electronic_signing'); ?>
												<?php if ($approve_item_eids && is_array($approve_item_eids) && count($approve_item_eids) > 0) { ?>
													<span class="label bg-warning mleft10"><strong><?php echo count($approve_item_eids); ?></strong></span>
												<?php } ?>
											</span>
										</a>
									</li>
								</ul>
							</div>


							<div class="col-md-9">

								<div class="col-md-12 mtop15">
									<div class="col-md-2">
										<?php
										$module_name = 'drawing_management';
										$design_stage_filter = get_module_filter($module_name, 'design_stage');
										$design_stage_filter_val = !empty($design_stage_filter) ? $design_stage_filter->filter_value : '';
										$statuses = [
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
										?>
										<label for="design_stage">Design Stage</label>
										<select name="design_stage" id="design_stage_filter" class="form-control selectpicker" style="width: 100%;" data-actions-box="true" data-live-search="true">
											<option value=""></option>
											<?php foreach ($statuses as $value => $label): ?>
												<option value="<?php echo htmlspecialchars($value); ?>"
													<?php echo ($label == $design_stage_filter_val) ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($label); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-2">
										<label for="discipline">Discipline</label>
										<select id="discipline_filter" name="discipline[]" data-live-search="true" class="form-control selectpicker" multiple>
											<option value=""></option>
											<?php
											$discipline_filter = get_module_filter($module_name, 'discipline');
											$discipline_filter_val = !empty($discipline_filter) ? $discipline_filter->filter_value : '';

											// Convert comma-separated values to an array
											$selected = !empty($discipline_filter_val) ? explode(',', $discipline_filter_val) : [];

											foreach ($discipline as $option) : ?>
												<option value="<?= $option['id']; ?>" <?= in_array($option['id'], $selected) ? 'selected' : ''; ?>>
													<?= $option['name']; ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-2">
										<?php
										$purpose_filter = get_module_filter($module_name, 'purpose');
										$purpose_filter_val = !empty($purpose_filter) ? $purpose_filter->filter_value : '';
										?>

										<label for="purpose" class="control-label"><?php echo _l('purpose'); ?></label>
										<select id="purpose_filter" name="purpose" class="selectpicker" data-width="100%" data-none-selected-text="None selected" tabindex="-98">
											<option value=""></option>
											<option value="Issued for Information" <?= ($purpose_filter_val == "Issued for Information") ? 'selected' : ''; ?>>Issued for Information</option>
											<option value="Issued for review" <?= ($purpose_filter_val == "Issued for review") ? 'selected' : ''; ?>>Issued for review</option>
											<option value="Issued for approval" <?= ($purpose_filter_val == "Issued for approval") ? 'selected' : ''; ?>>Issued for approval</option>
											<option value="Issued for tender" <?= ($purpose_filter_val == "Issued for tender") ? 'selected' : ''; ?>>Issued for tender</option>
											<option value="Issued for construction" <?= ($purpose_filter_val == "Issued for construction") ? 'selected' : ''; ?>>Issued for construction</option>
										</select>
									</div>
									<div class="col-md-2">
										<?php
										$status_filter = get_module_filter($module_name, 'status');
										$status_filter_val = !empty($status_filter) ? $status_filter->filter_value : '';
										?>
										<label for="status" class="control-label"><?php echo _l('status'); ?></label>
										<select id="status_filter" name="status" class="selectpicker" data-width="100%" data-none-selected-text="None selected" tabindex="-98">
											<option value=""></option>
											<option value="under_review" <?= ($status_filter_val == "under_review") ? 'selected' : ''; ?>>Under Review</option>
											<option value="released" <?= ($status_filter_val == "released") ? 'selected' : ''; ?>>Released</option>
											<option value="released_with_comments" <?= ($status_filter_val == "released_with_comments") ? 'selected' : ''; ?>>Released with comments</option>
											<option value="rejected" <?= ($status_filter_val == "rejected") ? 'selected' : ''; ?>>Rejected</option>
										</select>
									</div>
									<div class="col-md-2">
										<?php
										$controlled_document_filter = get_module_filter($module_name, 'controlled_document');
										$controlled_document_filter_val = !empty($controlled_document_filter) ? $controlled_document_filter->filter_value : '';
										?>
										<label for="controlled_document" class="control-label"><?php echo _l('Controlled Document'); ?></label>
										<select id="controlled_document_filter" name="controlled_document" class="selectpicker" data-width="100%" data-none-selected-text="None selected" tabindex="-98">
											<option value=""></option>
											<option value="1" <?= ($controlled_document_filter_val == 1) ? 'selected' : ''; ?>>Yes</option>
											<option value="0" <?= ($controlled_document_filter_val === 0) ? 'selected' : ''; ?>>No</option>
										</select>
									</div>
									<div class="col-md-2" style="margin-top: 22px;">
										<a href="javascript:void(0)" class="btn btn-info btn-icon reset_vbt_all_filters">
											Reset filter </a>
									</div>
								</div>

								<div class="col-md-12 mtop15">
									<div id="append_fillter_data">
										<?php if ($share_to_me == 0 && $my_approval == 0 && $electronic_signing == 0) { ?>
											<div class="row">
												<div class="col-md-12">
													<?php
													$html_breadcrumb = '';
													$data_breadcrumb = $this->drawing_management_model->breadcrum_array($parent_id);
													foreach ($data_breadcrumb as $key => $value) {
														$html_breadcrumb = '<li class="breadcrumb-item"><a href="' . admin_url('drawing_management?id=' . $value['id']) . '">' . $value['name'] . '</a></li>' . $html_breadcrumb;
													}
													$col_class = '';
													?>
													<?php if ($value['id'] == 1) {
														$col_class = 'col-md-9';
													} elseif ($value['id'] == 2) {
														$col_class = 'col-md-7';
													} else {
														$col_class = 'col-md-8';
													} ?>
													<nav aria-label="breadcrumb">
														<ol class="breadcrumb <?= $col_class ?>">
															<?php echo drawing_htmldecode($html_breadcrumb); ?>
														</ol>
														<?php if ($value['id'] == 1) { ?>
															<h5 class="text-muted display-flex col-md-3" style="border-bottom: 1px solid #f0f0f0;padding-bottom: 18px !important;justify-content: end;padding: 0px; margin-top: 0px;">

																<span class="mtop3 mleft5">These are your private files</span>
															</h5>
														<?php	} elseif ($value['id'] == 2) { ?>
															<h5 class="text-muted display-flex col-md-5" style="border-bottom: 1px solid #f0f0f0;padding-bottom: 18px !important;justify-content: end;padding: 0px; margin-top: 0px;">

																<span class="mtop3 mleft5">These files are viewable by entire company</span>
															</h5>
														<?php } else { ?>
															<h5 class="text-muted display-flex col-md-4" style="border-bottom: 1px solid #f0f0f0;padding-bottom: 18px !important;justify-content: end;padding: 0px; margin-top: 0px;">

																<span class="mtop3 mleft5">These are project specific design files</span>
															<?php 	}
															?>

													</nav>
												</div>
											</div>
											<?php
											if (isset($item)) {
												if (isset($item) && $item->filetype == 'folder') {
													$child_items = $this->drawing_management_model->get_item('', 'parent_id = ' . $parent_id, 'name, id, dateadded, filetype,parent_id');
													if (count($child_items)) {
														$this->load->view('file_managements/includes/item_list.php', ['child_items' => $child_items]);
													} else { ?>
														<div class="row mbot20">
															<div class="col-md-12">
																<h5 class="text-muted display-flex">
																	<span class="text-warning">
																		<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap">
																			<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
																		</svg>
																	</span>
																	<span class="mtop3 mleft5"><?php echo _l('dmg_the_folder_is_empty_you_can_create_a_folder_or_upload_a_file') . '.'; ?></span>
																</h5>
															</div>
														</div>
													<?php } ?>
													<div class="file-form-group file-form">
														<?php echo form_open_multipart(admin_url('drawing_management/upload_file/' . $parent_id), array('id' => 'form_upload_file')); ?>
														<input type="file" id="files" name="file[]" multiple="">
														<div class="file-form-preview hide">
															<ul class="selectedFiles list-group list-group-flush mtop15" id="selectedFiles"></ul>
															<hr>
															<button class="btn btn-primary pull-right mright10 display-flex">
																<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload">
																	<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
																	<polyline points="17 8 12 3 7 8" />
																	<line x1="12" y1="3" x2="12" y2="15" />
																</svg>
																<span class="mleft5 mtop2">
																	<?php echo _l('dmg_upload_all'); ?>
																</span>
															</button>
														</div>
														<?php echo form_close(); ?>
													</div>
											<?php } else {
													if ($edit == 1) {
														$this->load->view('file_managements/includes/file_edit.php');
													} else {
														$this->load->view('file_managements/includes/file_detail.php');
													}
												}
											} ?>
										<?php } else { ?>

											<!-- Share to me -->
											<?php if ($share_to_me == 1) { ?>
												<?php if ($parent_id > 0) { ?>
													<div class="row">
														<div class="col-md-12">
															<?php
															$html_breadcrumb = '';
															$data_breadcrumb = $this->drawing_management_model->breadcrum_array2($parent_id);
															foreach ($data_breadcrumb as $key => $value) {
																$html_breadcrumb = '<li class="breadcrumb-item"><a href="' . admin_url('drawing_management?share_to_me=1&id=' . $value['id']) . '">' . $value['name'] . '</a></li>' . $html_breadcrumb;
															}
															?>
															<nav aria-label="breadcrumb">
																<ol class="breadcrumb">
																	<?php echo drawing_htmldecode($html_breadcrumb); ?>
																</ol>
															</nav>
														</div>
													</div>
												<?php
												}
												if (drawing_check_share_permission($parent_id, 'upload_only')) { ?>
													<div class="file-form-group file-form">
														<?php echo form_open_multipart(admin_url('drawing_management/upload_file/' . $parent_id . '/share_to_me'), array('id' => 'form_upload_file')); ?>
														<input type="file" id="files" name="file[]" multiple="">
														<div class="file-form-preview hide">
															<ul class="selectedFiles list-group list-group-flush mtop15" id="selectedFiles"></ul>
															<hr>
															<button class="btn btn-primary pull-right mright10 display-flex">
																<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload">
																	<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
																	<polyline points="17 8 12 3 7 8" />
																	<line x1="12" y1="3" x2="12" y2="15" />
																</svg>
																<span class="mleft5 mtop2">
																	<?php echo _l('dmg_upload_all'); ?>
																</span>
															</button>
														</div>
														<?php echo form_close(); ?>
													</div>
													<?php
												} else {
													$child_items = [];
													if ($parent_id == 0) {
														$child_items = $this->drawing_management_model->get_item('', 'id IN (' . $share_id . ')', 'name, id, dateadded, filetype, parent_id');
													} else {
														$child_items = $this->drawing_management_model->get_item('', 'parent_id = ' . $parent_id, 'name, id, dateadded, filetype, parent_id');
													}
													if ($parent_id == 0 || (is_numeric($parent_id) && $parent_id > 0 && drawing_dmg_get_file_type($parent_id) == 'folder')) {
														if (count($child_items)) {
															$this->load->view('file_managements/includes/item_list_share_to_me.php', ['child_items' => $child_items]);
														} else { ?>
															<div class="row mbot20">
																<div class="col-md-12">
																	<h5 class="text-muted display-flex">
																		<span class="text-warning">
																			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap">
																				<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
																			</svg>
																		</span>
																		<span class="mtop3 mleft5"><?php echo _l('dmg_you_dont_have_any_files_or_folders_shared') . '.'; ?></span>
																	</h5>
																</div>
															</div>
														<?php
														}
														if (drawing_check_share_permission($parent_id, 'editor')) { ?>
															<div class="file-form-group file-form">
																<?php echo form_open_multipart(admin_url('drawing_management/upload_file/' . $parent_id . '/share_to_me'), array('id' => 'form_upload_file')); ?>
																<input type="file" id="files" name="file[]" multiple="">
																<div class="file-form-preview hide">
																	<ul class="selectedFiles list-group list-group-flush mtop15" id="selectedFiles"></ul>
																	<hr>
																	<button class="btn btn-primary pull-right mright10 display-flex">
																		<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload">
																			<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
																			<polyline points="17 8 12 3 7 8" />
																			<line x1="12" y1="3" x2="12" y2="15" />
																		</svg>
																		<span class="mleft5 mtop2">
																			<?php echo _l('dmg_upload_all'); ?>
																		</span>
																	</button>
																</div>
																<?php echo form_close(); ?>
															</div>
													<?php
														}
													} else {
														if ($edit == 1) {
															$this->load->view('file_managements/includes/file_edit.php');
														} else {
															$this->load->view('file_managements/includes/file_share_detail.php');
														}
													}
												}
											} elseif ($my_approval == 1) {
												if (count($approve_items)) {
													$this->load->view('file_managements/includes/item_list_approval.php', ['child_items' => $approve_items]);
												} else { ?>
													<div class="row mbot20">
														<div class="col-md-12">
															<h5 class="text-muted display-flex">
																<span class="text-warning">
																	<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap">
																		<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
																	</svg>
																</span>
																<span class="mtop3 mleft5"><?php echo _l('dmg_you_dont_have_any_approval_requests') . '.'; ?></span>
															</h5>
														</div>
													</div>
												<?php
												}
											} elseif ($electronic_signing == 1) {
												if (count($approve_item_eids)) {
													$this->load->view('file_managements/includes/item_list_sign_approval.php', ['child_items' => $approve_item_eids]);
												} else { ?>
													<div class="row mbot20">
														<div class="col-md-12">
															<h5 class="text-muted display-flex">
																<span class="text-warning">
																	<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap">
																		<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
																	</svg>
																</span>
																<span class="mtop3 mleft5"><?php echo _l('dmg_you_dont_have_any_approval_requests') . '.'; ?></span>
															</h5>
														</div>
													</div>
										<?php
												}
											}
										}
										?>
									</div>
								</div>
							</div>


						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="modal create_new_section" id="create_new_section" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<?php echo form_open(admin_url('drawing_management/create_new_section'), array('id' => 'create_new_section')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title add-title title1"><?php echo _l('dmg_new_section'); ?></h4>
				<h4 class="modal-title edit-title title1"><?php echo _l('dmg_edit_section'); ?></h4>
				<h4 class="modal-title add-title title2 hide"><?php echo _l('dmg_new_folder'); ?></h4>
				<h4 class="modal-title edit-title title2 hide"><?php echo _l('dmg_edit_folder'); ?></h4>
			</div>
			<div class="modal-body">
				<input type="hidden" name="default_parent_id" value="<?php echo drawing_htmldecode($parent_id); ?>">
				<input type="hidden" name="parent_id" value="">
				<input type="hidden" name="id" value="">
				<?php echo render_input('name', '', '', 'text', ['required' => true]); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>


<div class="modal select_folder" id="select_folder" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title add-title title1"><?php echo _l('dmg_select_folder'); ?></h4>
			</div>
			<div class="modal-body">
				<input type="hidden" name="selected_item" value="">
				<input type="hidden" name="action_type" value="">
				<input type="hidden" name="default_parent_id" value="<?php echo drawing_htmldecode($parent_id); ?>">
				<div class="list"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-primary" onclick="continue_action()"><?php echo _l('dmg_continue'); ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal share_document" id="share_document" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<?php echo form_open(admin_url('drawing_management/share_document'), array('id' => 'share_document')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title add-title title1 hide"><?php echo _l('dmg_share_file'); ?></h4>
				<h4 class="modal-title add-title title2 hide"><?php echo _l('dmg_share_folder'); ?></h4>
				<h4 class="modal-title edit-title title1 hide"><?php echo _l('dmg_edit_share_file'); ?></h4>
				<h4 class="modal-title edit-title title2 hide"><?php echo _l('dmg_edit_share_folder'); ?></h4>
			</div>
			<div class="modal-body">
				<?php
				$redirect_type = '';
				if ($share_to_me == 1) {
					$redirect_type = 'share_to_me';
				}
				?>
				<input type="hidden" id="redirect" name="redirect" value="<?php echo drawing_htmldecode($redirect_type); ?>">
				<input type="hidden" name="parent_id" value="<?php echo drawing_htmldecode($parent_id); ?>">
				<input type="hidden" name="item_id" value="">
				<input type="hidden" name="id" value="">
				<div class="row">
					<div class="col-md-12">
						<label for="asm_format_code" class="control-label clearfix"><?php echo _l('dmg_share_to') ?></label>
						<div class="radio-toolbar">
							<input type="radio" id="staff" name="share_to" value="staff" checked>
							<label for="staff"><i class="fa fa-user-circle"></i> <?php echo _l('dmg_staff') ?></label>

							<input type="radio" id="vendor" name="share_to" value="vendor">
							<label for="vendor"><i class="fa fa-user-o"></i> <?php echo _l('dmg_vendor') ?></label>

							<?php /* <input type="radio" id="customer_group" name="share_to" value="customer_group">
							<label for="customer_group"><i class="fa fa-users" aria-hidden="true"></i> <?php echo _l('dmg_customer_group') ?></label>
							*/ ?>
						</div>
					</div>

					<div class="col-md-12 staff_fr">
						<?php echo render_select('staff[]', $staffs, array('staffid', array('firstname', 'lastname')), '<small class="req text-danger">* </small>' . _l('dmg_staff'), '', ['multiple' => 1, 'required' => true, 'data-actions-box' => true], [], '', '', false); ?>
					</div>
					<div class="col-md-12 vendor_fr hide">
						<?php echo render_select('vendor[]', $vendors, array('userid', 'company'), '<small class="req text-danger">* </small>' . _l('dmg_vendor'), '', ['multiple' => 1, 'data-actions-box' => true], [], '', '', false); ?>
						<div class="vendor_basic_details">
						</div>
						<div class="col-md-12 vendor_contact">

						</div>
					</div>
					<div class="col-md-12 customer_group_fr hide">
						<?php echo render_select('customer_group[]', $customer_groups, array('id', 'name'), '<small class="req text-danger">* </small>' . _l('dmg_customer_group'), '', ['multiple' => 1, 'data-actions-box' => true], [], '', '', false); ?>
					</div>
					<div class="col-md-12 staff_pr">
						<?php
						$permission_list = [
							['id' => 'preview', 'name' => _l('dmg_preview')],
							['id' => 'viewer', 'name' => _l('dmg_viewer')],
							['id' => 'editor', 'name' => _l('dmg_editor')],
							['id' => 'upload_only', 'name' =>  _l('dmg_upload_only')]
						];
						echo render_select('permission', $permission_list, array('id', 'name'), '<small class="req text-danger">* </small>' . _l('dmg_permission'), 'preview', ['required' => true, 'data-actions-box' => true], [], '', '', false); ?>
					</div>
					<div class="col-md-12 vendor_pr hide">
						<?php
						$permission_list = [
							['id' => 'preview', 'name' => _l('dmg_preview')]
						];
						echo render_select('permission', $permission_list, array('id', 'name'), '<small class="req text-danger">* </small>' . _l('dmg_permission'), 'preview', ['required' => true, 'data-actions-box' => true], [], '', '', false); ?>
					</div>
					<div class="col-md-4">
						<div class="checkbox checkbox-primary mtop25">
							<input type="checkbox" name="expiration" id="expiration" value="1">
							<label for="expiration"><?php echo _l('dmg_expiration'); ?></label>
						</div>
					</div>
					<div class="col-md-8">
						<?php
						$current_date = date('Y-m-d H:i');
						echo render_datetime_input('expiration_date', 'dmg_expiration_date', '', ['data-date-min-date' => $current_date, 'disabled' => true]);
						?>
					</div>
					<div class="col-md-12">
						<?php echo render_textarea('message', 'dmg_message'); ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-primary"><?php echo _l('dmg_share'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>



<input type="hidden" name="check" value="">
<?php init_tail(); ?>
<?php
require 'modules/drawing_management/assets/js/file_managements/file_management_js.php';
if ($edit == 1) {
	require 'modules/drawing_management/assets/js/file_managements/edit_file_js.php';
}
if (isset($item) && $item->filetype != 'folder' && $edit != 1) {
	require 'modules/drawing_management/assets/js/file_managements/file_detail_js.php';
}
?>
</body>

</html>
<script>
	$(document).ready(function() {
		var initialTableContent = $('#append_fillter_data').html();
		$('#searchBox').on('keyup', function() {
			let query = $(this).val();

			if (query.length >= 3) {
				$.ajax({
					url: '<?= base_url("drawing_management/get_file_and_folder") ?>',
					type: 'GET',
					data: {
						query: query
					},
					dataType: 'json',
					success: function(data) {
						$('#dropdown').empty().show(); // Clear previous results and show dropdown
						if (data.length > 0) {
							$.each(data, function(index, item) {
								// let typeLabel = item.type === 'folder' ? 'Folder' : 'File';
								let breadcrumb = item.breadcrumb.join(' > '); // Join breadcrumb with ">"
								let url = `<?= admin_url('drawing_management?id=') ?>${item.id}`;
								$('#dropdown').append(
									`<a href="${url}" class="dropdown-item">
                                    ${breadcrumb ? breadcrumb + ' > ' : ''}${item.name} 
                                </a>`
								);
							});
						} else {
							$('#dropdown').append('<a href="#" class="dropdown-item disabled">No results found</a>');
						}
					}
				});
			} else {
				$('#dropdown').hide(); // Hide dropdown if query is less than 3 characters
			}
		});

		// Hide dropdown when clicking outside the search box or dropdown
		$(document).on('click', function(e) {
			if (!$(e.target).closest('#searchBox').length && !$(e.target).closest('#dropdown').length) {
				$('#dropdown').hide();
			}
		});


		$(document).ready(function() {
			// fetchFilteredData();

			function fetchFilteredData() {
				let designStage = $('#design_stage_filter').val();
				let discipline = $('#discipline_filter').val(); // For multi-select, this returns an array
				let purpose = $('#purpose_filter').val();
				let status = $('#status_filter').val();
				let controlled_document = $('#controlled_document_filter').val();
				// if (designStage == '' && discipline == '' && purpose == '' && status == '' && controlled_document == '') {
				// 	$('#append_fillter_data').html(initialTableContent);
				// 	return;
				// }
				$.ajax({
					url: '<?= base_url("drawing_management/get_file_and_folder_by_filter") ?>',
					type: 'GET',
					data: {
						design_stage: designStage,
						discipline: discipline,
						purpose: purpose,
						status: status,
						controlled_document: controlled_document
					},
					dataType: 'json',
					success: function(data) {
						$('#append_fillter_data').empty().show(); // Clear previous results and show dropdown
						if (!empty(data)) {
							if (data.length > 0) {
								$.each(data, function(index, item) {
									let breadcrumb = item.breadcrumb.join(' > '); // Join breadcrumb with ">"
									let url = `<?= admin_url('drawing_management?id=') ?>${item.id}`;
									$('#append_fillter_data').append(
										`<a href="${url}" class="dropdown-item bg-light-gray" style="color: rgb(37 99 235/var(--tw-text-opacity));outline: none!important;text-decoration: none!important;">
                                <i class="fa fa-file text-primary fs-14"></i><strong class="fs-14 mleft10">${item.name} </strong>
                            </a>`
									);
								});
							} else {
								if (empty(designStage) && empty(discipline) && empty(purpose) && empty(status) && empty(controlled_document)) {
									$('#append_fillter_data').html(initialTableContent); // Restore the original table content
								} else {
									$('#append_fillter_data').append('<a href="#" class="dropdown-item disabled">No results found</a>');
								}
							}
						} else {
							if (empty(designStage) && empty(discipline) && empty(purpose) && empty(status) && empty(controlled_document)) {
								$('#append_fillter_data').html(initialTableContent); // Restore the original table content
							} else {
								$('#append_fillter_data').append('<a href="#" class="dropdown-item disabled">No results found</a>');
							}
						}
					}

				});
			}

			// Listen for changes in both dropdowns
			$('#design_stage_filter, #discipline_filter, #purpose_filter, #status_filter, #controlled_document_filter').on('change', function() {
				fetchFilteredData();
			});
			$(".reset_vbt_all_filters").click(function() {
				$('#design_stage_filter, #purpose_filter, #status_filter, #controlled_document_filter').val('');
				$('#discipline_filter').val([]);
				$('#append_fillter_data').html(initialTableContent); // Restore the original table content
				$('select').selectpicker('refresh');
				fetchFilteredData();
			});
		});


	});
</script>
<script>
	$(document).ready(function() {
		$('#pdf_attachment').change(function() {
			var fileInput = $(this);
			var filePath = fileInput.val();
			var allowedExtensions = /(\.dwg|\.xref)$/i;

			if (!allowedExtensions.exec(filePath)) {
				alert('Please upload files with .dwg or .xref extensions only.');
				fileInput.val(''); // Clear the file input
				return false;
			}

			return true;
		});
	});
</script>