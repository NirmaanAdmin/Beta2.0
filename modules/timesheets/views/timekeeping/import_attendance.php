<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php

$file_header = array();
$file_header[] = _l('staffid');
$file_header[] = _l('id');
$file_header[] = _l('hr_code');
$file_header[] = _l('staff_name');
$file_header[] = _l('department');
$file_header[] = _l('integration_actual_workday');
$file_header[] = _l('integration_paid_leave');
$file_header[] = _l('integration_unpaid_leave');
$file_header[] = _l('standard_working_time_of_month');

?>
 
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div id ="dowload_file_sample">


						</div>


						<div class="row">
							<div class="col-md-2">
								<?php echo render_input('month_attendance','month_attendance_create',date('Y-m'), 'month'); ?>   
							</div>
						</div>

						<!-- button download file -->
						<button id="export-file" onclick="dowload_contract_excel(); return false;" class="btn btn-warning btn-xs mleft5 staff_contract_create " data-toggle="tooltip" title="" data-original-title="<?php echo _l('create_attendance_file_download'); ?>"><i class="fa fa-download"></i><?php echo _l('create_attendance_file_download') ?></button>

						<a href="#" id="dowload-file" class="btn btn-success btn-xs mleft5 staff_contract_download hide " data-toggle="tooltip" title="" data-original-title="<?php echo _l('download_sample'); ?>"><?php echo _l('download_sample'); ?></a>

						<hr>

						<?php if(!isset($simulate)) { ?>
							<ul>
								<li class="text-danger">1. <?php echo _l('file_xlsx_attendance1'); ?></li>
								<li class="text-danger">2. <?php echo _l('file_xlsx_attendance2'); ?></li>
								<li class="text-danger">3. Enter P for Present, Enter L for Leave, Enter OFF for day-off/no shift, Enter WFH for Work from Home, Enter OW for Out for Work</li>

							</ul>
							<!-- <div class="table-responsive no-dt">
								<table class="table table-hover table-bordered">
									<thead>
										<tr>
											<?php
											$total_fields = 0;
											for($i=0;$i<5;$i++){
												if($i == 0 || $i == 1 ||$i == 2){
													?>
													<th class="bold"><span class="text-danger">*</span> <?php echo new_html_entity_decode($file_header[$i]) ?> </th>
													<?php 
												} else {
													?>
													<th class="bold"><?php echo new_html_entity_decode($file_header[$i]) ?> </th>
													
													<?php

												} 
												$total_fields++;
											}

											?>

										</tr>
									</thead>
									<tbody>
										<?php for($i = 0; $i<1;$i++){
											echo '<tr>';
											for($x = 0; $x<5;$x++){
												echo '<td>- </td>';
											}
											echo '</tr>';
										}
										?>
									</tbody>
								</table>
							</div> -->
							<hr>

						<?php } ?>
						
						<div class="row">
							<div class="col-md-4">
								<?php echo form_open_multipart(admin_url('timesheets/import_attendance_excel_new'),array('id'=>'import_form')) ;?>
								<?php echo form_hidden('leads_import','true'); ?>
								<?php echo render_input('file_csv','choose_excel_file','','file'); ?> 

								<div class="form-group">
									
									<a href="<?php echo admin_url('timesheets/timekeeping'); ?>" class=" btn  btn-default ">
										<?php echo _l('hrp_back'); ?>
									</a>
									<button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);" ><?php echo _l('import'); ?></button>
								</div>
								<?php echo form_close(); ?>
							</div>
							<div class="col-md-8">
								<div class="form-group" id="file_upload_response">
									
								</div>
								
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<!-- box loading -->
			<div id="box-loading"></div>

		</div>
	</div>
</div>
<?php init_tail(); ?>
<?php require('modules/timesheets/assets/js/import_attendance_js.php'); ?>
</body>
</html>
