<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<title><?php echo _l('view_meeting'); ?></title>
<?php init_head(); ?>
<style>
   table {
      width: 100%;
      border-collapse: collapse;
      font-family: Arial, sans-serif;
      font-size: 14px;
   }

   th {
      border: 1px solid #ccc;
   }

   th,
   td {
      text-align: left;
      padding: 8px;
   }

   thead {
      background-color: #f2f2f2;
   }

   button {
      padding: 5px 10px;
   }

   img.images_w_table {
      width: 116px;
      height: 73px;
   }

   .new-task-relation {
      display: none;
   }

   .mom_body td {
      border: 1px solid #ccc;
   }

   p {
      margin: 0px;
   }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="horizontal-scrollable-tabs preview-tabs-top">
                     <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                     <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                     <div class="horizontal-tabs">
                        <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                           <li role="presentation" class="active">
                              <a href="#meeting_details" aria-controls="meeting_details" role="tab" data-toggle="tab">
                                 <?php echo _l('meeting_details'); ?>
                              </a>
                           </li>

                           <li role="presentation">
                              <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                                 <?php echo _l('invoice_view_activity_tooltip'); ?>
                              </a>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <!-- Meeting Details Section -->
                  <!-- <h4><?php echo _l('meeting_details'); ?></h4> -->
                  <div class="clearfix"></div>
                  <div class="tab-content">
                     <div role="tabpanel" class="tab-pane ptop10 active" id="meeting_details">
                        <table class="table table-bordered">
                           <tr>
                              <td style="width: 20%;"><span style="font-weight: bold;"><?php echo _l('meeting_title'); ?>:</span>
                                 <?php echo isset($meeting['meeting_title']) ? $meeting['meeting_title'] : 'N/A'; ?>
                              </td>
                              <td style="width: 80%;"><span style="font-weight: bold;"><?php echo _l('meeting_date'); ?>:</span>
                                 <?php echo isset($meeting['meeting_date']) ? date('d M, Y h:i A', strtotime($meeting['meeting_date'])) : 'N/A'; ?>
                              </td>
                           </tr>

                           <tr>
                              <td style="width: 20%;"><span style="font-weight: bold;"><?php echo _l('project'); ?>:</span>
                                 <?php echo isset($meeting['project_id']) ? get_project_name_by_id($meeting['project_id']) : 'N/A'; ?>
                              </td>
                              <td style="width: 80%;"><span style="font-weight: bold;"><?php echo _l('Venue'); ?>:</span>
                                 <?php echo isset($meeting['venue']) ? $meeting['venue'] : 'N/A'; ?>
                              </td>
                           </tr>

                           <!-- <tr>
                        <td><strong><?php echo _l('Meeting Link'); ?>:</strong></td>
                        <td><?php echo isset($meeting['meeting_link']) ? $meeting['meeting_link'] : 'N/A'; ?></td>
                        </tr> -->
                           <!-- <tr>
                        <td><strong><?php echo _l('agenda'); ?>:</strong></td>
                        <td>
                           <table class="mom-items-table items table-main-dpr-edit has-calculations no-mtop">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Area/Head</th>
                                    <th>Description</th>
                                    <th>Decision</th>
                                    <th>Action</th>
                                    <th>Action By</th>
                                    <th>Target Date</th>
                                    <th>Attachments</th>
                                 </tr>
                              </thead>
                              <tbody class="mom_body">
                                 <?php
                                 $sr = 1;
                                 foreach ($agenda_data as $data) {
                                    if (!empty($data['attachments']) && !empty($data['agenda_id'])) {
                                       $item_base_url = base_url('uploads/meetings/mom_attachments/' . $data['agenda_id'] . '/' . $data['id'] . '/' . $data['attachments']);
                                       $full_item_image = '<img class="images_w_table" src="' . $item_base_url . '" alt="' . $data['attachments'] . '" >';
                                    }
                                 ?>
                                    <tr>
                                       <td><?php echo $sr++; ?></td>
                                       <td><?php echo $data['area']; ?></td>
                                       <td><?php echo $data['description']; ?></td>
                                       <td><?php echo $data['decision']; ?></td>
                                       <td><?php echo $data['action']; ?></td>
                                       <td>
                                          <?php echo getStaffNamesFromCSV($data['staff']); ?><br>
                                          <?php echo $data['vendor']; ?>
                                       </td>
                                       <td><?php echo date('d M, Y', strtotime($data['target_date'])); ?></td>
                                       <td><?php echo $full_item_image; ?></td>
                                    </tr>
                                 <?php }
                                 ?>
                              </tbody>
                           </table>
                        </td>
                        </tr> -->
                           <!-- New Row for Meeting Notes -->
                           <tr>
                              <td><span style="font-weight: bold;"><?php echo _l('meeting_notes'); ?></span></td>
                              <td>
                                 <table class="mom-items-table items table-main-dpr-edit has-calculations no-mtop">
                                    <thead>
                                       <tr>
                                          <th>No</th>
                                          <th>
                                             <?php
                                             if ($meeting['area_head'] == 1) {
                                                echo "Area";
                                             } elseif ($meeting['area_head'] == 2) {
                                                echo "Head";
                                             } else {
                                                echo "None";
                                             }
                                             ?>

                                          </th>
                                          <th>Description</th>
                                          <th>Decision</th>
                                          <th>Action</th>
                                          <th>Action By</th>
                                          <th>Target Date</th>
                                          <th>Attachments</th>
                                       </tr>
                                    </thead>
                                    <tbody class="mom_body">
                                       <?php
                                       $prev_area = ''; // Initialize the previous area value
                                       $last_serial = 0;
                                       foreach ($minutes_data as $data) {
                                          $full_item_image = '';
                                          // Process attachments if available
                                          if (!empty($data['attachments']) && !empty($data['minute_id'])) {
                                             $item_base_url = base_url('uploads/meetings/minutes_attachments/' . $data['minute_id'] . '/' . $data['id'] . '/' . $data['attachments']);
                                             $full_item_image = '<img class="images_w_table" src="' . $item_base_url . '" alt="' . $data['attachments'] . '" >';
                                          }

                                          // Format the target date
                                          if (!empty($data['target_date'])) {
                                             $target_date = date('d M, Y', strtotime($data['target_date']));
                                          } else {
                                             $target_date = '';
                                          }
                                          if (empty($data['serial_no'])) {
                                             $serial = $last_serial + 1;
                                          } else {
                                             $serial = $data['serial_no'];
                                          }
                                          // Update last serial for the next iteration.
                                          $last_serial = $serial;
                                          // Compare current area with the previous one.
                                          // If they match then set $area as an empty string.
                                          // Otherwise, use the current area's value.
                                          if ($data['area'] == $prev_area) {
                                             $area = '';
                                          } else {
                                             $area = $data['area'];
                                          }
                                          // Update the previous area for the next iteration
                                          $prev_area = $data['area'];
                                       ?>
                                          <tr>
                                             <?php
                                             // Check if a section break exists, and if so, display it.
                                             if (!empty($data['section_break'])) {
                                                // Determine the colspan based on whether the attachment column exists.
                                                $colspan = $check_attachment ? 8 : 7;
                                                echo '<tr>
                                <td colspan="8" style="text-align:center;font-size:18px;font-weight:600">' . $data['section_break'] . '</td>
                            </tr>';
                                             }
                                             ?>
                                             <td><?php echo $serial; ?></td>
                                             <td><?php echo $area; ?></td>
                                             <td><?php echo $data['description']; ?></td>
                                             <td><?php echo $data['decision']; ?></td>
                                             <td><?php echo $data['action']; ?></td>
                                             <td>
                                                <?php echo getStaffNamesFromCSV($data['staff']); ?><br>
                                                <?php echo $data['vendor']; ?>
                                             </td>
                                             <td><?php echo $target_date; ?></td>
                                             <td><?php echo $full_item_image; ?></td>
                                          </tr>
                                       <?php } ?>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </table>

                        <!-- Participants Table -->
                        <h4><?php echo _l('participants'); ?></h4>
                        <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th><?php echo _l('Participant Name'); ?></th>
                                 <th><?php echo _l('Email'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              if (!empty($participants)) : ?>
                                 <?php foreach ($participants as $participant) : ?>
                                    <?php
                                    // Check if the participant has at least one valid field (non-empty firstname, lastname, or email)
                                    if (!empty($participant['firstname']) || !empty($participant['lastname']) || !empty($participant['email'])) :
                                    ?>
                                       <tr>
                                          <td><?php echo isset($participant['firstname']) || isset($participant['lastname'])
                                                   ? trim($participant['firstname'] . ' ' . $participant['lastname'])
                                                   : 'N/A'; ?></td>
                                          <td><?php echo isset($participant['email']) && !empty($participant['email'])
                                                   ? $participant['email']
                                                   : 'N/A'; ?></td>
                                       </tr>
                                    <?php endif; ?>
                                 <?php endforeach; ?>
                              <?php else : ?>
                                 <tr>
                                    <td colspan="2" class="text-center"><?php echo _l('No Participants Found'); ?></td>
                                 </tr>
                              <?php endif; ?>
                           </tbody>
                        </table>
                        <h4><?php echo _l('Participants'); ?></h4>

                        <?php
                        // Extract all 'other_participants' and 'company_name' values into a single array
                        $all_other_participants = [];
                        foreach ($other_participants as $participant) {
                           if (!empty($participant['other_participants']) || !empty($participant['company_names'])) {
                              $all_other_participants[] = [
                                 'name' => $participant['other_participants'] ?? '',
                                 'company_name' => $participant['company_names'] ?? '',
                              ];
                           }
                        }
                        ?>

                        <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th><?php echo _l('Name'); ?></th>
                                 <th><?php echo _l('Company'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php if (!empty($all_other_participants)) : ?>
                                 <?php foreach ($all_other_participants as $participant) : ?>
                                    <tr>
                                       <td><?php echo htmlspecialchars($participant['name']); ?></td>
                                       <td><?php echo htmlspecialchars($participant['company_name']); ?></td>
                                    </tr>
                                 <?php endforeach; ?>
                              <?php else : ?>
                                 <tr>
                                    <td colspan="2">No participants found</td>
                                 </tr>
                              <?php endif; ?>
                           </tbody>
                        </table>
                        <h4>Additional Note</h4>
                        <?php echo $meeting['additional_note']; ?>
                        <!-- Tasks Section -->
                        <h4><?php echo _l('tasks'); ?></h4>
                        <?php
                        if ($agenda_id > 0) { ?>
                           <hr>
                           <div>
                              <h4><?php echo _l('task_overview'); ?></h4>
                              <?php init_relation_tasks_table(array('data-new-rel-id' => $agenda_id, 'data-new-rel-type' => 'meeting_minutes')); ?>
                           </div>
                           <hr>
                        <?php }
                        ?>
                        <!-- <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th><?php echo _l('task_title'); ?></th>
                                 <th><?php echo _l('assigned_to'); ?></th>
                                 <th><?php echo _l('due_date'); ?></th>
                                 <th><?php echo _l('status'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php if (!empty($tasks)) : ?>
                                 <?php foreach ($tasks as $task) : ?>
                                    <tr>
                                       <td><?php echo isset($task['task_title']) ? $task['task_title'] : 'N/A'; ?></td>
                                       <td><?php echo isset($task['firstname']) ? $task['firstname'] . ' ' . $task['lastname'] : 'N/A'; ?></td>
                                       <td><?php echo isset($task['due_date']) ? $task['due_date'] : 'N/A'; ?></td>
                                       <td><?php echo isset($task['status']) && $task['status'] == 1 ? _l('completed') : _l('not_completed'); ?></td>
                                    </tr>
                                 <?php endforeach; ?>
                              <?php else : ?>
                                 <tr>
                                    <td colspan="4" class="text-center"><?php echo _l('no_tasks_found'); ?></td>
                                 </tr>
                              <?php endif; ?>
                           </tbody>
                        </table> -->
                        <div class="col-md-12" id="meeting_attachments">
                           <?php
                           $file_html = '';
                           if (isset($attachments) && count($attachments) > 0) {
                              $file_html .= '<hr /><p class="bold text-muted">' . _l('Meeting Attachments') . '</p>';

                              foreach ($attachments as $value) {
                                 $path = get_upload_path_by_type('meeting_management') . 'agenda_meeting/' . $value['rel_id'] . '/' . $value['file_name'];
                                 $is_image = is_image($path);

                                 $download_url = site_url('download/file/meeting_management/' . $value['id']);

                                 $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="' . $value['id'] . '">
                <div class="col-md-8">';

                                 // Preview button for images
                                 if ($value['filetype'] != 'application/vnd.openxmlformats-officedoc  ') {
                                    $file_html .= '<a name="preview-meeting-btn" 
                    onclick="preview_meeting_attachment(this); return false;" 
                    rel_id="' . $value['rel_id'] . '" 
                    id="' . $value['id'] . '" 
                    href="javascript:void(0);" 
                    class="mbot10 mright5 btn btn-success pull-left" 
                    data-toggle="tooltip" 
                    title="' . _l('preview_file') . '">
                    <i class="fa fa-eye"></i>
                </a>';
                                 }

                                 $file_html .= '<div class="pull-left"><i class="' . get_mime_class($value['filetype']) . '"></i></div>
                <a href="' . $download_url . '" target="_blank" download>
                    ' . $value['file_name'] . '
                </a>
                <br />
                <small class="text-muted">' . $value['filetype'] . '</small>
                </div>
                <div class="col-md-4 text-right">';

                                 // Delete button with permission check
                                 if ($value['staffid'] == get_staff_user_id() || is_admin()) {
                                    $file_html .= '<a href="' . admin_url('meeting_management/minutesController/delete_attachment/' . $value['id']) . '" class="text-danger _delete"><i class="fa fa-times"></i></a>';
                                 }

                                 $file_html .= '</div></div>';
                              }

                              $file_html .= '<hr />';
                              echo pur_html_entity_decode($file_html);
                           }
                           ?>
                        </div>

                        <div id="meeting_file_data"></div>

                        <!-- Export as PDF Button -->
                        <div class="btn-bottom-toolbar text-right">
                           <a href="javascript:void(0);" id="export-csv" class="btn btn-info">
                              Export as CSV
                           </a>
                           <a href="<?php echo admin_url('meeting_management/agendaController/export_to_pdf/' . $meeting['meeting_id']); ?>" class="btn btn-info">
                              <?php echo _l('export_as_pdf'); ?>
                           </a>
                        </div>
                     </div>
                     <div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="activity-feed">
                                 <?php
                                 // echo '<pre>';
                                 // print_r($activity);
                                 // die;
                                 foreach ($activity as $activity) {
                                    $_custom_data = false; ?>
                                    <div class="feed-item" data-sale-activity-id="<?php echo e($activity['id']); ?>">
                                       <div class="date">
                                          <span class="text-has-action" data-toggle="tooltip"
                                             data-title="<?php echo e(_dt($activity['date'])); ?>">
                                             <?php echo e(time_ago($activity['date'])); ?>
                                          </span>
                                       </div>
                                       <div class="text">
                                          <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                                             <a href="<?php echo admin_url('profile/' . $activity['staffid']); ?>">
                                                <?php echo staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                                ?>
                                             </a>
                                          <?php } ?>
                                          <?php
                                          $additional_data = '';
                                          if (!empty($activity['additional_data']) && $additional_data = unserialize($activity['additional_data'])) {
                                             $i               = 0;
                                             foreach ($additional_data as $data) {
                                                if (strpos($data, '<original_status>') !== false) {
                                                   $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                                                   $additional_data[$i] = format_invoice_status($original_status, '', false);
                                                } elseif (strpos($data, '<new_status>') !== false) {
                                                   $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                                                   $additional_data[$i] = format_invoice_status($new_status, '', false);
                                                } elseif (strpos($data, '<custom_data>') !== false) {
                                                   $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                                   unset($additional_data[$i]);
                                                }
                                                $i++;
                                             }
                                          }

                                          $_formatted_activity = _l($activity['note'], $additional_data);

                                          if ($_custom_data !== false) {
                                             $_formatted_activity .= ' - ' . $_custom_data;
                                          }

                                          if (!empty($activity['staffid'])) {
                                             $get_staff = get_staff_by_id($activity['staffid']);
                                             $_formatted_activity = $get_staff->firstname . ' ' . $get_staff->lastname . ' - ' . $_formatted_activity;
                                          }

                                          echo $_formatted_activity;

                                          // if (is_admin()) {
                                          //   echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa fa-remove"></i></a>';
                                          // } 

                                          ?>
                                       </div>
                                    </div>
                                 <?php } ?>
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
</div>
<script type="text/javascript">
   document.getElementById('export-csv').addEventListener('click', function() {
      // Select the table based on its class.
      const table = document.querySelector('.mom-items-table');

      // Get all rows (both header and body rows) then filter out section break rows.
      // A section break row has at least one cell with a colspan attribute.
      const rows = Array.from(table.querySelectorAll('tr')).filter(row => {
         return !row.querySelector('th[colspan], td[colspan]');
      });

      // Initialize CSV content string.
      let csvContent = '';

      // Loop through each row.
      rows.forEach(row => {
         // Get all cells (both header and data cells).
         let cells = Array.from(row.querySelectorAll('th, td'));

         // Remove the attachments column (last cell) if it exists.
         if (cells.length > 0) {
            cells = cells.slice(0, -1);
         }

         // Map through the remaining cells to get their text content, trim any whitespace and wrap in double quotes.
         const rowContent = cells.map(cell => `"${cell.textContent.trim()}"`).join(',');

         // Append the row's CSV string followed by a newline.
         csvContent += rowContent + "\n";
      });

      // Add a UTF-8 BOM to support Excel (so that special characters display correctly).
      const bom = "\uFEFF";

      // Create a Blob from the CSV content with the appropriate MIME type.
      const blob = new Blob([bom + csvContent], {
         type: 'text/csv;charset=utf-8;'
      });
      const url = URL.createObjectURL(blob);

      // Create a temporary link element that will trigger the download.
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', 'mom_export.csv');
      link.style.display = 'none';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
   });
</script>


<?php init_tail(); ?>
<script>
   function preview_meeting_attachment(invoker) {
      "use strict";
      var id = $(invoker).attr('id');
      var rel_id = $(invoker).attr('rel_id');
      view_preview_meeting_attachment(id, rel_id);
   }

   function view_preview_meeting_attachment(id, rel_id) {
      "use strict";
      $('#meeting_file_data').empty();
      $("#meeting_file_data").load(admin_url + 'meeting_management/minutesController/file_meeting_preview/' + id + '/' + rel_id, function(response, status, xhr) {
         if (status == "error") {
            alert_float('danger', xhr.statusText);
         }
      });
   }

   function close_modal_preview() {
      "use strict";
      $('._project_file').modal('hide');
   }
</script>
</body>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      init_rel_tasks_table(<?php echo pur_html_entity_decode($agenda_id); ?>, 'meeting_minutes');
   });
</script>

</html>