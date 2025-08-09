<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;

class MinutesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Load language file and models
        $this->lang->load('meeting_management', 'english');
        $this->load->model('Meeting_model');
        $this->load->model('Task_model');
        $this->load->model('Clients_model');  // Load the default clients model
        $this->load->model('Departments_model');
        $this->load->model('Staff_model');
    }

    // View and manage the minutes of a meeting
    public function index($agenda_id)
    {
        // Fetch the agenda and the minutes for this agenda
        $data['agenda'] = $this->Meeting_model->get_agenda($agenda_id);
        $data['minutes'] = $this->Meeting_model->get_minutes($agenda_id);
        $data['agenda_id'] = $agenda_id;  // Pass the agenda_id to the view

        // Fetch the list of tasks associated with this agenda
        $data['tasks'] = $this->Meeting_model->get_tasks_by_agenda($agenda_id);

        // Fetch the list of staff members for task assignment

        $data['staff_members'] = $this->Staff_model->get();  // Load all staff members
        // $data['clients'] = $this->Clients_model->get();      // Load all clients

        // Fetch selected participants
        $data['selected_participants'] = $this->Meeting_model->get_selected_participants($agenda_id) ?? [];  // Ensure it's always an array


        $data['attachments'] = $this->Meeting_model->get_meeting_attachments('agenda_meeting', $agenda_id);
        $data['title'] = _l('meeting_minutes');
        $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);

        $mom_row_template = $this->Meeting_model->create_mom_row_template();
        if ($agenda_id == '') {
            $is_edit = false;
        } else {
            $get_mom_detials = $this->Meeting_model->get_minutes_detials($agenda_id);

            if (count($get_mom_detials) > 0) {
                $index_order = 1;
                foreach ($get_mom_detials as $mom_detail) {
                    $index_order++;
                    $mom_row_template .= $this->Meeting_model->create_mom_row_template('items[' . $index_order . ']', $mom_detail['area'], $mom_detail['description'], $mom_detail['decision'], $mom_detail['action'], $mom_detail['staff'], $mom_detail['vendor'], $mom_detail['target_date'], $mom_detail, $mom_detail['id'], $mom_detail['section_break'], $mom_detail['serial_no'], $mom_detail['critical']);
                }
            }
            $is_edit = true;
        }
        $data['is_edit'] = $is_edit;
        $data['mom_row_template'] = $mom_row_template;
        $data['projects'] = $this->projects_model->get_items();

        // Load the minutes form view (with tasks form and task list added)
        $this->load->view('meeting_management/minutes_form', $data);
    }


    public function save_all($agenda_id)
    {
        // Handle task deletions
        $deleted_tasks = $this->input->post('deleted_tasks');
        if (!empty($deleted_tasks)) {
            $task_ids_to_delete = explode(',', $deleted_tasks);
            foreach ($task_ids_to_delete as $task_id) {
                $this->Task_model->delete_task($task_id);
            }
        }

        // Handle new task additions
        $new_tasks = json_decode($this->input->post('new_tasks'), true);  // Decode the JSON string
        if (!empty($new_tasks)) {
            foreach ($new_tasks as $task_data) {
                $task_data['agenda_id'] = $agenda_id;  // Ensure the task is linked to the correct agenda
                $this->Task_model->create_task($task_data);
            }
        }

        // Handle saving the minutes
        $minutes_data = [
            'minutes' => $this->input->post('minutes'),
            'updated_by' => get_staff_user_id(),
        ];
        $this->Meeting_model->save_minutes($agenda_id, $minutes_data);

        set_alert('success', 'All changes saved successfully!');
        redirect(admin_url('minutesController/convert_to_minutes/' . $agenda_id));
    }


    // Convert an agenda to minutes of meeting
    public function convert_to_minutes($agenda_id = '')
    {


        // Load necessary models
        $this->load->model('Meeting_model');
        $this->load->model('Task_model');

        // Determine if we're in "edit" mode (existing agenda) or "new" mode.
        if (!empty($agenda_id)) {
            // Existing agenda: Fetch from database.
            $agenda = $this->Meeting_model->get_agenda($agenda_id);
            if (!$agenda) {
                show_error('Agenda not found.');
                return;
            }

            $data['agenda']             = $agenda;
            $data['minutes']            = $this->Meeting_model->get_minutes($agenda_id);
            $data['tasks']              = $this->Meeting_model->get_tasks_by_agenda($agenda_id);
            $data['attachments']        = $this->Meeting_model->get_meeting_attachments('agenda_meeting', $agenda_id);
            $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);
        } else {
            // New agenda: setup empty/default arrays.
            $data['agenda']             = null;
            $data['minutes']            = [];
            $data['tasks']              = [];
            $data['attachments']        = [];
            $data['other_participants'] = [];
        }
        // Pass agenda_id along (it can be empty).
        $data['agenda_id'] = $agenda_id;

        // Load the staff members (common to both cases)
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);

        // Handle form submission for meeting minutes
        if ($this->input->post('minutes')) {
            $minutes_data = [
                'minutes' => $this->input->post('minutes')
            ];

            // For new agenda, create a new record and get back its id.
            if (empty($agenda_id)) {
                // Assumes that create_agenda() creates a new agenda and returns its ID.
                $agenda_id = $this->Meeting_model->create_agenda($minutes_data);
                $data['agenda_id'] = $agenda_id;
            } else {
                // Existing agenda: update minutes.
                $this->Meeting_model->save_minutes($agenda_id, $minutes_data);
            }

            set_alert('success', _l('meeting_minutes_created_success'));
            redirect(admin_url('meeting_management/minutesController/index/' . $agenda_id));
        }

        // Handle form submission for adding a new task
        if ($this->input->post('task_title')) {
            $task_data = [
                'agenda_id'   => $agenda_id,
                'task_title'  => $this->input->post('task_title'),
                'assigned_to' => $this->input->post('assigned_to'),
                'due_date'    => $this->input->post('due_date')
            ];
            $this->Task_model->create_task($task_data);

            set_alert('success', _l('meeting_task_created_success'));
            redirect(admin_url('meeting_management/minutesController/index/' . $agenda_id));
        }

        // Create the Minutes of Meeting (MOM) row template.
        $mom_row_template = $this->Meeting_model->create_mom_row_template();
        if (!empty($agenda_id)) {
            $get_mom_detials = $this->Meeting_model->get_mom_detials($agenda_id);
            if (!empty($get_mom_detials)) {
                $index_order = 1;
                foreach ($get_mom_detials as $mom_detail) {
                    $index_order++;
                    $mom_row_template .= $this->Meeting_model->create_mom_row_template(
                        'items[' . $index_order . ']',
                        $mom_detail['area'],
                        $mom_detail['description'],
                        $mom_detail['decision'],
                        $mom_detail['action'],
                        $mom_detail['staff'],
                        $mom_detail['vendor'],
                        $mom_detail['target_date'],
                        $mom_detail,
                        $mom_detail['id']
                    );
                }
                $is_edit = true;
            } else {
                $is_edit = false;
            }
        } else {
            // New agenda: No MOM details exist yet.
            $is_edit = false;
        }
        $data['is_edit']         = $is_edit;
        $data['mom_row_template'] = $mom_row_template;
        $data['projects'] = $this->projects_model->get_items();

        $data['title'] = _l('meeting_minutes');

        // Load the view with the assembled data.
        $this->load->view('meeting_management/minutes_form', $data);
    }


    public function save_minutes_and_tasks($agenda_id = '')
    {

        // Ensure agenda_id is retrieved correctly
        // if (!$agenda_id) {
        //     throw new Exception('Agenda ID is not valid');
        // }

        // // Debugging to check if agenda_id is correct
        // log_message('error', 'Agenda ID during save: ' . $agenda_id);

        // Get minutes data from POST
        // $minutes_data = [
        //     'minutes' => $this->input->post('minutes', false),
        // ];
        $data = $this->input->post();
        $agenda_data_new = $data;
        $agenda_data_new['additional_note'] = $this->input->post('additional_note', false);
        $agenda_data_new['created_by'] = get_staff_user_id();

        if ($agenda_id == '') {

            // Insert new agenda
            $agenda_id = $this->Meeting_model->create_agenda($agenda_data_new);
            set_alert('success', _l('meeting_minutes_created_success'));
        } else {
            // Update existing agenda
            $this->Meeting_model->update_minutes($agenda_id, $data);
            set_alert('success', _l('meeting_updated_success'));
        }
        // Update the minutes for this meeting


        // Handle deleted tasks
        $deleted_task_ids = $this->input->post('deleted_tasks');
        if (!empty($deleted_task_ids)) {
            // Only delete if there are tasks to delete
            $this->Meeting_model->delete_tasks(explode(',', $deleted_task_ids));
        }
        // Save the participants
        $participants = $this->input->post('participants');
        $other_participants = $this->input->post('other_participants');
        $company_name = $this->input->post('company_names');

        if ($participants) {
            $this->Meeting_model->save_participants($agenda_id, $participants, $other_participants, $company_name);
        }
        $this->Meeting_model->save_participants($agenda_id, $participants, $other_participants, $company_name);
        // Handle new tasks if any

        $new_tasks = $this->input->post('new_tasks');  // Corrected
        if (!empty($new_tasks)) {
            foreach ($new_tasks as $task_data) {
                if (!empty($task_data['title']) && !empty($task_data['assigned_to']) && !empty($task_data['due_date'])) {
                    $task = [
                        'agenda_id' => $agenda_id,
                        'task_title' => $task_data['title'],
                        'assigned_to' => $task_data['assigned_to'],
                        'due_date' => $task_data['due_date'],
                    ];
                    $this->Task_model->add($task);
                }
            }
        }

        set_alert('success', 'Minutes and tasks saved successfully.');
        redirect(admin_url('meeting_management/minutesController/index/' . $agenda_id));
    }


    public function share_meeting($agenda_id)
    {
        $this->load->model('Meeting_model');
        $this->load->model('Misc_model');  // Load the correct model for notifications
        $this->load->library('email');

        // Fetch participants
        $participants = $this->Meeting_model->get_selected_participants($agenda_id);

        // Validate that participants is an array
        if (!is_array($participants)) {
            set_alert('danger', 'Participants data is not valid.');
            redirect(admin_url('meeting_management/minutesController/index/' . $agenda_id));
            return;
        }

        // Fetch meeting details to include in the email
        $meeting_details = $this->Meeting_model->get_meeting_details($agenda_id);

        foreach ($participants as $participant) {
            // Check if participant is an array before accessing its fields
            if (is_array($participant)) {
                $email = $participant['email']; // Ensure that the participant has an email field
                $name = isset($participant['firstname']) ? $participant['firstname'] . ' ' . $participant['lastname'] : '';

                // Prepare email data
                $this->email->from('no-reply@yourcrm.com', 'CRM Meeting Management');
                $this->email->to($email);
                $this->email->subject('Meeting Details');
                $this->email->message('Dear ' . $name . ', here are the meeting details: ' . json_encode($meeting_details));

                // Send email and handle errors
                if ($this->email->send()) {
                    // If email is sent, also add a notification
                    $notification_data = [
                        'description'     => 'New meeting details shared',
                        'touserid'        => $participant['staffid'] ?? $participant['userid'],  // Check if it's a staff or client ID
                        'fromcompany'     => 1,  // 1 means from the company
                        'link'            => 'meeting_management/view/' . $agenda_id,  // Link to view the meeting
                        'additional_data' => serialize(['Meeting Title: ' . $meeting_details['meeting_title']]),
                    ];
                    $this->Misc_model->add_notification($notification_data);  // Use Misc_model to add notification

                    // Trigger notification to send
                    pusher_trigger_notification([$participant['staffid'] ?? $participant['userid']]);
                } else {
                    log_message('error', 'Email to ' . $email . ' failed to send.');
                }
            } else {
                log_message('error', 'Participant data is invalid: ' . print_r($participant, true));
            }
        }

        set_alert('success', 'Meeting details have been shared successfully.');
        redirect(admin_url('meeting_management/minutesController/index/' . $agenda_id));
    }





    private function _send_meeting_email($email, $agenda, $minutes, $tasks)
    {
        $this->email->from('no-reply@yourcrm.com', 'CRM Meeting Management');
        $this->email->to($email);

        $this->email->subject(_l('meeting_minutes_subject') . ': ' . $agenda->meeting_title);

        // Prepare the email body
        $email_body = '<h3>' . _l('meeting_minutes') . '</h3>';
        $email_body .= '<p>' . $minutes->minutes . '</p>';
        $email_body .= '<h4>' . _l('meeting_tasks') . '</h4>';

        if (!empty($tasks)) {
            $email_body .= '<ul>';
            foreach ($tasks as $task) {
                $email_body .= '<li>' . $task['task_title'] . ' - ' . _l('assigned_to') . ': ' . $task['firstname'] . ' ' . $task['lastname'] . ', ' . _l('due_date') . ': ' . $task['due_date'] . '</li>';
            }
            $email_body .= '</ul>';
        } else {
            $email_body .= '<p>' . _l('no_tasks_found') . '</p>';
        }

        // Set the email body and send the email
        $this->email->message($email_body);
        $this->email->send();
    }


    // Add tasks for the participants based on meeting discussions
    public function assign_tasks($agenda_id)
    {
        if ($this->input->post()) {
            $task_data = [
                'agenda_id' => $agenda_id,
                'task_title' => $this->input->post('task_title'),
                'assigned_to' => $this->input->post('assigned_to'),
                'due_date' => $this->input->post('due_date'),
                'status' => 0, // Default to "not completed"
            ];
            $this->Meeting_model->assign_task($task_data);

            set_alert('success', _l('meeting_task_created_success'));
            redirect(admin_url('minutesController/index/' . $agenda_id));
        }

        $data['participants'] = $this->Meeting_model->get_participants($agenda_id);
        $data['title'] = _l('assign_meeting_task');
        $this->load->view('meeting_management/assign_task_form', $data);
    }

    public function delete_task()
    {
        $task_id = $this->input->post('task_id');

        if (!empty($task_id)) {
            $this->load->model('Task_model');

            $deleted = $this->Task_model->delete_task($task_id);

            if ($deleted) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
    }
    public function export_meeting_to_pdf()
    {
        $this->load->library('pdf');  // Load PDF library (e.g., dompdf)

        $content = $this->input->post('content');
        $pdf_content = '<html><body>' . $content . '</body></html>';

        // Set the PDF settings
        $this->pdf->load_html($pdf_content);
        $this->pdf->render();
        $this->pdf->stream('meeting_details.pdf', array('Attachment' => 1)); // Force download PDF
    }


    public function view_meeting($agenda_id)
    {
        // Fetch meeting details
        $data['meeting'] = $this->Meeting_model->get_meeting_details($agenda_id);
        $data['agenda_id'] = $agenda_id;
        // Fetch participants
        $data['participants'] = $this->Meeting_model->get_selected_participants($agenda_id);

        // Fetch tasks
        $data['tasks'] = $this->Meeting_model->get_tasks_by_agenda($agenda_id);

        $data['attachments'] = $this->Meeting_model->get_meeting_attachments('agenda_meeting', $agenda_id);

        $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);
        $data['minutes_data'] = $this->Meeting_model->get_minutes_detials($agenda_id);
        $data['activity'] = $this->Meeting_model->get_activity_log($agenda_id, 'mom_agenda');
        // Load the view
        $this->load->view('meeting_management/view_meeting', $data);
    }

    public function delete_attachment($id)
    {
        $this->Meeting_model->delete_meeting_attachment($id);
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function file_meeting_preview($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->Meeting_model->get_meeting_attachments_with_id($id);
        // echo '<pre>';
        // print_r($data);
        // die;
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('meeting_management/_file', $data);
    }

    public function update_minutes_of_meeting()
    {
        $data = $this->input->post();
        $this->Meeting_model->update_minutes_of_meeting($data);
        echo json_encode(['success' => true]);
        die();
    }

    public function assign_task_in_mom()
    {

        $this->load->model('tasks_model');

        // Build the query to fetch task assignment data along with meeting title and minutes details.
        $this->db->select('atm.*, mm.id as minute_id, mm.meeting_title, md.description, md.target_date');
        $this->db->from('tbltask_assigned_mom atm');
        $this->db->join('tblmeeting_management mm', 'mm.id = atm.agenda_id', 'left');
        $this->db->join('tblminutes_details md', 'md.id = atm.minutes_detail_id', 'left');
        $query = $this->db->get();

        $assigned_tasks = $query->result();

        if (!empty($assigned_tasks)) {
            foreach ($assigned_tasks as $task) {
                // Build the task name using the description (adjust as needed)
                $taskName = $task->description;

                // Prepare task data array.
                $taskData = [
                    'name'      => $taskName,
                    'is_public' => 1,
                    'startdate' => _d(date('Y-m-d')),
                    'duedate'   => $task->target_date,
                    'priority'  => 3,
                    'rel_type'  => 'meeting_minutes',
                    'rel_id'    => $task->minute_id,
                ];

                // Insert the new task and get the inserted task's ID.
                $task_id = $this->tasks_model->add($taskData);

                // Manage comma-separated staff ids.
                if (isset($task->staff_ids)) {
                    $staff_ids = explode(',', $task->staff_ids);
                    foreach ($staff_ids as $staff_id) {
                        $staff_id = trim($staff_id);
                        if (!empty($staff_id)) {
                            $assignData = [
                                'staffid' => $staff_id,
                                'taskid'  => $task_id
                            ];
                            // $this->db->insert('tbltask_assigned', $assignData);

                            $this->tasks_model->add_task_assignees([
                                'taskid'   => $task_id,
                                'assignee' => $staff_id,
                            ]);
                        }
                    }
                } else {
                    log_message('error', 'Staff ID not found for task assignment in assign_task_in_mom()');
                }

                // Delete the processed record from tbltask_assigned_mom.
                $this->db->where('id', $task->id);
                $this->db->delete('tbltask_assigned_mom');
            }
        } else {
            log_message('info', 'No records found in tbltask_assigned_mom to process in assign_task_in_mom()');
        }
    }

    public function critical_agenda()
    {

        $data['critical_agenda'] = $this->Meeting_model->get_critical_agenda();
        $data['department'] = $this->Departments_model->get();
        $data['mom_row_template'] = $this->Meeting_model->create_mom_critical_row_template();
        $data['staff_list'] = $this->Staff_model->get();
        $data['title'] = _l('meeting_critical_agenda');
        $data['total'] = $this->Meeting_model->get_total_critical_agenda('total');
        $data['open'] = $this->Meeting_model->get_total_critical_agenda('open');
        $data['completed'] = $this->Meeting_model->get_total_critical_agenda('completed');

        $this->load->view('meeting_management/critical_agenda', $data);
    }

    public function change_status_mom($status, $id)
    {
        // Define an array of statuses with their corresponding labels and texts
        $status_labels = [
            1 => ['label' => 'label-danger', 'table' => 'open', 'text' => _l('Open')],
            2 => ['label' => 'label-success', 'table' => 'close', 'text' => _l('close')],
        ];
        $success = $this->Meeting_model->change_status_mom($status, $id);
        $message = $success ? _l('change_status_successfully') : _l('change_status_fail');

        $html = '';
        $status_str = $status_labels[$status]['text'] ?? '';
        $class = $status_labels[$status]['label'] ?? '';


        $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $html .= '</a>';

        $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $id . '">';

        // Generate the dropdown menu options dynamically
        foreach ($status_labels as $key => $label) {
            if ($key != $status) {
                $html .= '<li>
                    <a href="#" onclick="change_status_mom(' . $key . ', ' . $id . '); return false;">
                        ' . $label['text'] . '
                    </a>
                </li>';
            }
        }

        $html .= '</ul>';
        $html .= '</div>';


        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }



    public function change_priority_mom($status, $id)
    {
        // Define an array of statuses with their corresponding labels and texts
        $priority_labels = [
            1 => ['label' => 'label-warning', 'table' => 'high', 'text' => _l('High')],
            2 => ['label' => 'label-default', 'table' => 'low', 'text' => _l('Low')],
            3 => ['label' => 'label-info', 'table' => 'medium', 'text' => _l('Medium')],
            4 => ['label' => 'label-danger', 'table' => 'urgent', 'text' => _l('Urgent')],
        ];
        $success = $this->Meeting_model->change_priority_mom($status, $id);
        $message = $success ? _l('change_priority_successfully') : _l('change_priority_fail');

        $html = '';
        $status_str = $priority_labels[$status]['text'] ?? '';
        $class = $priority_labels[$status]['label'] ?? '';


        $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderPriority-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $html .= '</a>';

        $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderPriority-' . $id . '">';

        // Generate the dropdown menu options dynamically
        foreach ($priority_labels as $key => $label) {
            if ($key != $status) {
                $html .= '<li>
                    <a href="#" onclick="change_priority_mom(' . $key . ', ' . $id . '); return false;">
                        ' . $label['text'] . '
                    </a>
                </li>';
            }
        }

        $html .= '</ul>';
        $html .= '</div>';


        echo json_encode([
            'success' => $success,
            'priority_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }

    public function change_department($department_id, $agenda_id)
    {
        // 1. Fetch and re-index departments by departmentid
        $departments       = $this->Departments_model->get();                // your original fetch
        $departments_by_id = array_column($departments, null, 'departmentid');

        // 2. Update in database
        $success = $this->Meeting_model->update_agenda_department($agenda_id, $department_id);
        $message = $success
            ? _l('department_changed_successfully')
            : _l('department_change_failed');

        // 3. Prepare response payload
        $department_name = '';
        $html            = '';

        if (isset($departments_by_id[$department_id])) {
            $department_name = $departments_by_id[$department_id]['name'];

            // Build just the dropdown toggle HTML (you return department_name separately)
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark"'
                .  ' id="tableDepartment-' . $agenda_id . '"'
                .  ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                .  '<span data-toggle="tooltip" title="' . _l('change_department') . '">'
                .  '<i class="fa fa-caret-down"></i></span>'
                .  '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right"'
                .  ' aria-labelledby="tableDepartment-' . $agenda_id . '">';

            // Iterate the re-indexed list so the key is the true departmentid
            foreach ($departments_by_id as $id => $dept) {
                if ($id != $department_id) {
                    $html .= '<li>'
                        . '<a href="#" onclick="change_department('
                        .   $id . ', ' . $agenda_id . '); return false;">'
                        .   $dept['name']
                        . '</a>'
                        . '</li>';
                }
            }

            $html .= '</ul></div>';
        }

        // 4. Output JSON for your AJAX success handler
        echo json_encode([
            'success'         => $success,
            'department_name' => $department_name,
            'message'         => $message,
            'html'            => $html,
        ]);
    }

    public function update_closed_date()
    {
        $id = $this->input->post('id');
        $closedDate = $this->input->post('closedDate');

        if (!$id || !$closedDate) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['date_closed' => $closedDate]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('closed_date_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_target_date()
    {
        $id = $this->input->post('id');
        $targetDate = $this->input->post('targetDate');

        if (!$id || !$targetDate) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['target_date' => $targetDate]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('Target Date Updated Successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function get_mom_critical_row_template()
    {
        $name = $this->input->post('name');
        $area = $this->input->post('area');
        $description = $this->input->post('description');
        $decision = $this->input->post('decision');
        $action = $this->input->post('action');
        $staff = $this->input->post('staff');
        $vendor = $this->input->post('vendor');
        $target_date = $this->input->post('target_date');
        $item_key = $this->input->post('item_key');
        $department = $this->input->post('department');
        $date_closed = $this->input->post('date_closed');
        $status = $this->input->post('status');
        $priority = $this->input->post('priority');
        $project_id = $this->input->post('project_id');
        echo $this->Meeting_model->create_mom_critical_row_template($name, $area, $description, $decision, $action, $staff, $vendor, $target_date, $item_key, '', $department, $date_closed, $status, $priority, $project_id);
    }

    public function add_critical_mom()
    {
        $data = $this->input->post();

        if ($data) {
            $id = $this->Meeting_model->add_critical_mom($data);
            if ($id) {


                // Return a JSON success response
                echo json_encode([
                    'success' => true,
                    'row_template' => $this->Meeting_model->create_mom_critical_row_template(),
                    'message' => _l('added_successfully', _l('critical_mom'))
                ]);
            } else {
                // Return a JSON error response if the insert failed
                echo json_encode([
                    'success' => false,
                    'message' => _l('problem_adding_critical_mom')
                ]);
            }
        } else {
            // Return a JSON error response if no data was posted
            echo json_encode([
                'success' => false,
                'message' => _l('no_data_received')
            ]);
        }
        exit; // Stop further execution
    }

    public function table_critical_tracker()
    {
        $this->app->get_table_data(module_views_path('meeting_management', 'table_critical_agenda'));
    }


    public function update_critical_area()
    {
        $id = $this->input->post('id');
        $area = $this->input->post('area');

        if (!$id  || !$area) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['area' => $area]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('area_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_critical_description()
    {
        $id = $this->input->post('id');
        $description = $this->input->post('description');

        if (!$id  || !$description) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['description' => $description]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('description_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_critical_decision()
    {
        $id = $this->input->post('id');
        $decision = $this->input->post('decision');

        if (!$id  || !$decision) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['decision' => $decision]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('decision_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    /**
     * Updates the action of a critical MOM item via AJAX request.
     *
     * Retrieves the item ID and the new action from the POST data,
     * performs validation, and updates the action in the database.
     * Responds with a JSON object indicating success or failure.
     */

    public function update_critical_action()
    {
        $id = $this->input->post('id');
        $action = $this->input->post('action');

        if (!$id  || !$action) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['action' => $action]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('action_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    /**
     * AJAX endpoint to update the vendor on a critical MOM item
     *
     * @param int|string $id   The ID of the critical MOM item to update
     * @param array|string $vendorRaw  The new vendor: either an array of IDs or a CSV string
     *
     * @return JSON with success status and message
     */
    public function update_critical_vendor()
    {
        $id = $this->input->post('id');
        $vendor = $this->input->post('vendor');

        if (!$id  || !$vendor) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'critical_mom', ['vendor' => $vendor]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('vendor_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    /**
     * AJAX endpoint to update the staff on a critical MOM item
     *
     * @param int|string $id   The ID of the critical MOM item to update
     * @param array|string $staffRaw  The new staff: either an array of IDs or a CSV string
     *
     * @return JSON with success status and message
     */
    public function change_staff()
    {
        $id        = $this->input->post('id');
        $staffRaw  = $this->input->post('staff'); // may be array or string
        // Basic validation
        if (! $id || $staffRaw === null) {
            echo json_encode([
                'success' => false,
                'message' => _l('invalid_request')
            ]);
            return;
        }

        // Normalize to CSV
        if (is_array($staffRaw)) {
            // cast each to int and filter out any empties just in case
            $staffIds = array_filter(array_map('intval', $staffRaw));
            $staffCsv = implode(',', $staffIds);
        } else {
            // already a string (e.g. "35,11,30")
            $staffCsv = trim($staffRaw);
        }

        // Update
        $this->db->where('id', $id);
        $success = $this->db->update(
            db_prefix() . 'critical_mom',
            ['staff' => $staffCsv]
        );

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => _l('staff_updated'),
                'staff'   => $staffCsv
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => _l('update_failed')
            ]);
        }
    }


    /**
     * Import xlsx critical tracker items
     *
     * @return void
     */
    public function import_file_xlsx_critical_tracker_items()
    {
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('area/head') => 'string',
                            _l('description') => 'string',
                            _l('decision')    => 'string',
                            _l('action')    => 'string',
                            _l('Target Date') => 'string',
                            _l('Date Closed') => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;

                        $total_rows_actualy = 0;
                        $list_item = $this->Meeting_model->create_mom_critical_row_template();
                        //get data for compare
                        $index_quote = 1;

                        for ($row = 1; $row < count($data); $row++) {
                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;
                            $flag_mail = 0;
                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_item_description;

                            // $value_cell_commodity_code = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_area = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_description = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_decision = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_cell_action = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_cell_target_date = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_cell_date_close = isset($data[$row][5]) ? $data[$row][5] : '';
                            /*check null*/
                            if (is_null($value_cell_area) == true) {
                                $string_error .= _l('area/head') . _l('not_yet_entered');
                                $flag = 1;
                            }
                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_area,
                                    $value_cell_description,
                                    $value_cell_decision,
                                    $value_cell_action,
                                    $value_cell_target_date,
                                    $value_cell_date_close,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }
                            if (($flag == 0) && ($flag2 == 0)) {
                                // Initialize with empty values
                                $target_date = '';
                                $date_closed = '';

                                // Only set target_date if $value_cell_target_date is not empty
                                if (!empty($value_cell_target_date)) {
                                    $target_date = date('Y-m-d', strtotime($value_cell_target_date));
                                }

                                // Only set date_closed if $value_cell_date_close is not empty
                                if (!empty($value_cell_date_close)) {
                                    $date_closed = date('Y-m-d', strtotime($value_cell_date_close));
                                }
                                $rows[] = $row;
                                $list_item .= $this->Meeting_model->create_mom_critical_row_template('newitems[' . $index_quote . ']', $value_cell_area, $value_cell_description, $value_cell_decision, $value_cell_action, '', '', $target_date, $index_quote, '', '', $date_closed);

                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Import Item successfully';
                            }
                        }
                        // die('sadad');
                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        // $total_row_false = '';
                        if (($total_rows_data_error > 0)) {

                            $filename = 'MEETING_MANAGEMENT_MOM_ERROR' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, MEETING_MANAGEMENT_MOM_ERROR . $filename, $filename));

                            $filename = MEETING_MANAGEMENT_MOM_ERROR . $filename;
                        }
                        $list_item = $list_item;

                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }

    /**
     * Get user preferences for critical agenda
     *
     * @return void
     */
    public function getPreferences()
    {

        // Retrieve user preferences using the model
        $preferences = $this->Meeting_model->get_datatable_preferences_critical();
        // If no preferences exist, return an empty array (or set defaults)
        if (!$preferences) {
            $preferences = array();
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['preferences' => $preferences]));
    }

    /**
     * Save user preferences for critical agenda
     *
     * @return void
     */
    public function savePreferences()
    {
        $data = $this->input->post();

        $id = $this->Meeting_model->add_update_preferences($data);
        if ($id) {
            set_alert('success', _l('added_successfully', _l('pur_order')));

            redirect(admin_url('meeting_management/minutesController/critical_agenda'));
        }
    }
    /**
     * Generates a PDF of the critical tracker
     *
     * @return void
     */
    public function critical_tracker_pdf()
    {
        // Initialize Dompdf
        $pdf = new Dompdf();

        // Enable remote files and HTML5 parser
        $options = $pdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $pdf->setOptions($options);

        // Get HTML content
        $html_content = $this->load->view('meeting_management/pdf_template_critical', null, true);

        // Load HTML
        $pdf->loadHtml($html_content);

        // Set paper size to A4 landscape
        $pdf->setPaper('A4', 'landscape');

        // Render the PDF
        $pdf->render();

        // Output the PDF to the browser
        $pdf->stream("Critical Tracker.pdf", ["Attachment" => true]);
    }
    /**
     * Exports critical tracker data to a CSV file.
     *
     * This function generates a CSV file containing critical tracker data
     * including details such as department, area, description, decision,
     * action, responsible staff/vendor, project, target and closed dates,
     * status, priority, and meeting name. The data is retrieved from the
     * database and formatted for CSV export, with HTML tags stripped from
     * text fields and special characters decoded.
     *
     * The CSV file is output directly to the browser with specific headers
     * for download as an attachment.
     *
     * @return void
     */

    public function critical_tracker_excel()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Critical_Tracker_Export_' . date('Y-m-d') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');
        $get_critical_tracker = get_critical_tracker_pdf();

        // CSV Headers (same as PDF table columns)
        $headers = [
            'Department',
            'Area/Head',
            'Description',
            'Decision',
            'Action',
            'Action By',
            'Project',
            'Target Date',
            'Date Closed',
            'Status',
            'Priority',
            'Fetched From'
        ];

        // Write headers to CSV
        fputcsv($output, $headers);

        // Status and Priority labels
        $status_labels = [
            1 => ['text' => _l('Open')],
            2 => ['text' => _l('Close')]
        ];

        $priority_labels = [
            1 => ['text' => _l('High')],
            2 => ['text' => _l('Low')],
            3 => ['text' => _l('Medium')],
            4 => ['text' => _l('Urgent')]
        ];

        // Data rows
        foreach ($get_critical_tracker as $key => $item) {
            // Format dates
            $target_date = '';
            if (!empty($item['target_date']) && $item['target_date'] != '0000-00-00') {
                $target_date = date('d M, Y', strtotime($item['target_date']));
            }

            $date_closed = '';
            if (!empty($item['date_closed']) && $item['date_closed'] != '0000-00-00') {
                $date_closed = date('d M, Y', strtotime($item['date_closed']));
            }

            // Format staff name and vendor
            $staff_name = trim(($item['firstname'] ?? '') . ' ' . ($item['lastname'] ?? ''));
            $action_by = $staff_name;
            if (!empty($item['vendor'])) {
                $action_by .= "\n" . $item['vendor'];
            }

            // Get status and priority text
            $status_text = $status_labels[$item['status']]['text'] ?? '';
            $priority_text = $priority_labels[$item['priority']]['text'] ?? '';

            // Get project name and meeting name
            $project_name = get_project_name_by_id_mom($item['project_id'] ?? '');
            $meeting_name = get_meeting_name_by_id($item['minute_id'] ?? '');
            // Clean HTML from description, decision, and action fields
            $description = strip_tags($item['description'] ?? '');
            $decision = strip_tags($item['decision'] ?? '');
            $action = strip_tags($item['action'] ?? '');

            // Replace any remaining HTML entities
            $description = html_entity_decode($description);
            $decision = html_entity_decode($decision);
            $action = html_entity_decode($action);

            // Remove multiple spaces and trim
            $description = trim(preg_replace('/\s+/', ' ', $description));
            $decision = trim(preg_replace('/\s+/', ' ', $decision));
            $action = trim(preg_replace('/\s+/', ' ', $action));

            // Write row data
            fputcsv($output, [
                $item['department_name'] ?? '',
                $item['area'] ?? '',
                $description,
                $decision,
                $action,
                $action_by,
                $project_name,
                $target_date,
                $date_closed,
                $status_text,
                $priority_text,
                $meeting_name
            ]);
        }

        // Close output stream
        fclose($output);
        exit;
    }
}
