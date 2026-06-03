<?php if(isset($item)){ 
	?>
	<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
	<?php init_head(); 
	?>
	<div id="wrapper">
		<div class="content">

			<div class="panel_s">
				<div class="panel-body">
					<div class="row">

						<div class="col-md-12">
							<h4 class="pull-left"><?php echo drawing_htmldecode($item->name); ?></h4>
							<?php if(!(strpos($item->name, '.xlsx') === false) || !(strpos($item->name, '.xls') === false)){ ?>
								<a href="<?php echo admin_url('drawing_management/preview?id='.$item->id) ?>" target="_blank" class="btn btn-default pull-right mleft5">
									<i class="fa fa-eye"></i> <?php echo _l('dmg_view_in_excel'); ?>
								</a>
							<?php } ?>

							<?php if(!(strpos($item->name, '.docx') === false) || !(strpos($item->name, '.doc') === false)){ ?>
								<a href="<?php echo admin_url('drawing_management/preview?id='.$item->id) ?>" target="_blank" class="btn btn-default pull-right mleft5">
									<i class="fa fa-eye"></i> <?php echo _l('dmg_view_in_word'); ?>
								</a>
							<?php } ?>

							<?php if(!(strpos($item->name, '.pdf') === false)){ ?>
								<a href="<?php echo admin_url('drawing_management/preview?id='.$item->id) ?>" target="_blank" class="btn btn-default pull-right mleft5">
									<i class="fa fa-eye"></i> <?php echo _l('dmg_view_pdf'); ?>
								</a>
							<?php } ?>

							<?php if(!(strpos($item->filetype, 'image') === false)){ ?>
								<a href="<?php echo admin_url('drawing_management/preview?id='.$item->id) ?>" target="_blank" class="btn btn-default pull-right mleft5">
									<i class="fa fa-eye"></i> <?php echo _l('dmg_view_image'); ?>
								</a>
							<?php } ?>
							<?php 
							$video_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER.'/files/'.$item->parent_id.'/'.$item->name;
							if(is_html5_video($video_path)){ ?>
								<a href="<?php echo admin_url('drawing_management/preview?id='.$item->id) ?>" target="_blank" class="btn btn-default pull-right mleft5">
									<i class="fa fa-eye"></i> <?php echo _l('dmg_view_video'); ?>
								</a>
							<?php } ?>
							<a class="btn btn-default pull-right" href="<?php echo site_url('modules/drawing_management/uploads/files/'.$item->parent_id.'/'.$item->name); ?>" download><i class="fa fa-download"></i> <?php echo _l('dmg_dowload'); ?></a>

							<div class="clearfix"></div>
							<hr>
						</div>
						<div class="col-md-12">
							<input type="hidden" name="id" value="<?php echo drawing_htmldecode($item->id); ?>">
							<input type="hidden" name="folder_id" value="<?php echo drawing_htmldecode($item->parent_id); ?>">

							<?php if($item->show_files_metadata == 1){ ?>
								<table class="table">

									<tr>
										<td class="text-nowrap"><?php echo _l('dmg_tags'); ?></td>
										<td><?php 
										$tag_html = '';
										if(!($item->tag == '' && $item->tag == null)){
											$tag_arr = explode(',', $item->tag);
											foreach ($tag_arr as $key => $text) {
												$tag_html .= '<span class="badge badge-light mleft5">'.$text.'</span>';
											}
										}
										echo drawing_htmldecode($tag_html); ?></td>
									</tr>
									<tr>
										<td class="text-nowrap"><?php echo _l('dmg_signed_by'); ?></td>
										<td><?php 
										$signed_by_html = '';
										if(!($item->signed_by == '' && $item->signed_by == null)){
											$signed_by_arr = explode(',', $item->signed_by);
											foreach ($signed_by_arr as $key => $text) {
												$signed_by_html .= '<span class="badge badge-light mleft5">'.$text.'</span>';
											}
										}
										echo drawing_htmldecode($signed_by_html); ?></td>
									</tr>
									<tr>
										<td class="text-nowrap"><?php echo _l('dms_date'); ?></td>
										<td><?php echo _dt($item->dateadded); ?></td>
									</tr>
									<tr>
										<td class="text-nowrap"><?php echo _l('dmg_due_date'); ?></td>
										<td><?php echo _dt($item->duedate); ?></td>
									</tr>
									<tr>
										<td class="text-nowrap"><?php echo _l('dmg_ocr_language'); ?></td>
										<td><?php echo drawing_ufirst($item->ocr_language); ?></td>
									</tr>
									<tr>
										<td class="text-nowrap"><?php echo _l('dmg_document_number'); ?></td>
										<td><?php echo drawing_ufirst($item->document_number); ?></td>
									</tr>
									<tr>
										<td class="text-nowrap"><?php echo _l('dmg_notes'); ?></td>
										<td><?php echo drawing_nlbr($item->note); ?></td>
									</tr>

									<?php 
									$data_custom_field = [];
									if(!($item->custom_field == '' || $item->custom_field == null)){
										$data_custom_field = json_decode($item->custom_field); 
										if(count($data_custom_field) > 0){
											foreach ($data_custom_field as $key => $customfield) { 
												$item_html = '<tr>';
												$item_html .= '<td class="text-nowrap">'.$customfield->title.'</td>';
												$item_html .= '<td>'.drawing_dmg_convert_custom_field_value_to_string($customfield->value, $customfield->type).'</td>';
												$item_html .= '</tr>';
												echo drawing_htmldecode($item_html);
											} 
										} 
									} 
									?>


								</table>
							<?php } ?>
							<!-- Resolution -->
							<div class="panel panel-default">
								<div class="panel-heading"><?php echo _l('dmg_resolution'); ?></div>
								<div class="panel-body no-border"><?php echo drawing_nlbr($item->resolution); ?></div>
							</div>
							<!-- Approve area -->
							<div class="col-md-12">
								<div class="project-overview-right">
									<div class="project-overview-right">
										<?php
										if(count($data_approve) > 0){ ?>
											<div class="row">
												<div class="col-md-12 project-overview-expenses-finance">
													<?php 
													$has_deny = false;
													$current_approve = false;
													foreach ($data_approve as $value) {
														?>
														<div class="col-md-4 text-center">
															<p class="text-uppercase text-muted no-mtop bold"><?php echo get_staff_full_name($value['staffid']); ?></p>

															<?php if($value['approve'] == 1){ 
																?>
																<img src="<?php echo site_url(DRAWING_MANAGEMENT_PATH.'approves/approved.png'); ?>">
																<br><br>
																<p class="bold text-center"><?php echo drawing_htmldecode($value['note']); ?></p> 
																<p class="bold text-center text-<?php if($value['approve'] == 1){ echo 'success'; }elseif($value['approve'] == 2){ echo 'danger'; } ?>"><?php echo _dt($value['date']); ?>
															<?php }elseif($value['approve'] == 2){ $has_deny = true;?>
																<img src="<?php echo site_url(DRAWING_MANAGEMENT_PATH.'approves/rejected.png'); ?>">
																<br><br>
																<p class="bold text-center"><?php echo drawing_htmldecode($value['note']); ?></p> 
																<p class="bold text-center text-<?php if($value['approve'] == 1){ echo 'success'; }elseif($value['approve'] == 2){ echo 'danger'; } ?>"><?php echo _dt($value['date']); ?>
															<?php }else{
																if($current_approve == false && $has_deny == false){ 
																	$current_approve = true;
																	if(get_staff_user_id() == $value['staffid']){ 
																		?>
																		<div class="row text-center" >
																			<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
																			<div class="dropdown-menu dropdown-menu-left">
																				<div class="col-md-12">
																					<?php echo render_textarea('reason', 'reason'); ?>
																					<div class="clearfix"></div>
																				</div>
																				<div class="col-md-12 text-center">
																					<a href="javascript:void(0)" data-loading-text="<?php echo _l('fe_waiting'); ?>" onclick="approve_request(<?php echo drawing_htmldecode($id); ?>);" class="btn btn-success"><?php echo _l('approve'); ?></a>
																					<a href="javascript:void(0)"  data-loading-text="<?php echo _l('fe_waiting'); ?>" onclick="deny_request(<?php echo drawing_htmldecode($id); ?>);" class="btn btn-warning"><?php echo _l('deny'); ?></a>
																				</div>
																				<div class="clearfix"></div>
																				<br>
																				<div class="clearfix"></div>
																			</div>
																		</div>
																		<?php 
																	}
																}
															} ?> 
														</p>
													</div>
													<?php
												} ?>
											</div>
										</div>
									<?php }else{
										if(isset($process)){
											if($process == 'choose'){
												$html = '<div class="row">';
												$html .= '<div class="col-md-9"><select name="approver" class="selectpicker" data-live-search="true" id="approver_c" data-width="100%" data-none-selected-text="'. _l('fe_please_choose_approver').'"> 
												<option value=""></option>'; 
												$current_user = get_staff_user_id();
												foreach($staffs as $staff){ 
													if($staff['staffid'] != $current_user){
														$html .= '<option value="'.$staff['staffid'].'">'.$staff['staff_identifi'].' - '.$staff['firstname'].' '.$staff['lastname'].'</option>';                  
													}
												}
												$html .= '</select></div>';
												$html .= '<div class="col-md-3"><a href="javascript:void(0)" onclick="choose_approver();" class="btn btn-success lead-top-btn lead-view">'._l('choose').'</a></div>';
												$html .= '</div>';
												echo drawing_htmldecode($html);
											}
										}
									} ?>
								</div>
							</div>
						</div>
						<!-- End approve area -->


					</div>


				</div>
			</div>
		</div>
	</div>
</div>
<?php 
init_tail();
} ?>     

