<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meeting_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get all agendas with the project name
    public function get_all_agendas()
    {
        $this->db->select('tblagendas.*, tblprojects.name as project_name');
        $this->db->from('tblagendas');
        $this->db->join('tblprojects', 'tblprojects.id = tblagendas.project_id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_all_minutes($project_id = null)
    {
        if ($project_id !== null) {
            $this->db->where('project_id', $project_id);
        }
        $this->db->select('tblmeeting_management.*');
        $this->db->from('tblmeeting_management');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function filter_minutes()
    {
        $project_id = $this->input->get('project_filter');

        $this->db->select('tblmeeting_management.*, tblprojects.name as project_name');
        $this->db->from('tblmeeting_management');
        $this->db->join('tblprojects', 'tblprojects.id = tblmeeting_management.project_id', 'left');

        if (!empty($project_id)) {
            $this->db->where('tblmeeting_management.project_id', $project_id);
        }

        $query = $this->db->get();
        $agendas = $query->result_array();

        // Convert date format for consistent display
        foreach ($agendas as &$agenda) {
            if (isset($agenda['meeting_date'])) {
                $agenda['meeting_date'] = date('Y-m-d H:i:s', strtotime($agenda['meeting_date']));
            }
        }

        header('Content-Type: application/json');
        echo json_encode($agendas);
        exit;
    }
    public function get_all_minutes_task($id)
    {
        if ($id > 0) {
            $this->db->where('id', $id);
        }
        $this->db->select('tblmeeting_management.*');
        $this->db->from('tblmeeting_management');
        return $query = $this->db->get()->row();
    }
    public function get_mom_detials($id)
    {
        $this->db->where('agenda_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'agendas_details')->result_array();
        return $mom_details;
    }
    public function get_minutes_detials($id)
    {
        $this->db->where('minute_id', $id);
        $this->db->order_by('reorder', 'ASC');
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();
        return $mom_details;
    }

    public function check_image($id)
    {
        $this->db->where('minute_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();

        // Check if any record has a non-empty attachments field.
        foreach ($mom_details as $detail) {
            if (!empty($detail['attachments'])) {
                return 1;
            }
        }
        return 0;
    }

    public function check_desc($id)
    {
        $this->db->where('minute_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();

        // Check if any record has a non-empty attachments field.
        foreach ($mom_details as $detail) {
            if (!empty($detail['description'])) {
                return 1;
            }
        }
        return 0;
    }

    public function check_action($id)
    {
        $this->db->where('minute_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();

        // Check if any record has a non-empty attachments field.
        foreach ($mom_details as $detail) {
            if (!empty($detail['action'])) {
                return 1;
            }
        }
        return 0;
    }

     public function check_action_by($id)
    {
        $this->db->where('minute_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();

        // Check if any record has a non-empty attachments field.
        foreach ($mom_details as $detail) {
            if (!empty($detail['staff'] || $detail['vendor'])) {
                return 1;
            }
        }
        return 0;
    }



    public function check_target_date($id)
    {
        $this->db->where('minute_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();

        // Check if any record has a non-empty attachments field.
        foreach ($mom_details as $detail) {
            if (!empty($detail['target_date'])) {
                return 1;
            }
        }
        return 0;
    }

    public function check_decision($id)
    {
        $this->db->where('minute_id', $id);
        $mom_details = $this->db->get(db_prefix() . 'minutes_details')->result_array();

        // Check if any record has a non-empty attachments field.
        foreach ($mom_details as $detail) {
            if (!empty($detail['decision'])) {
                return 1;
            }
        }
        return 0;
    }

    // Create a new agenda
    public function create_agenda($data)
    {
        // Save detail items (MOM details) if provided.
        $mom_detail = [];
        if (isset($data['newitems'])) {
            $mom_detail = $data['newitems'];
            unset($data['newitems']);
        }

        // Remove agenda detail keys from main data array,
        // so that they are not inserted into the main agendas table.
        unset(
            $data['client_id'],
            $data['area'],
            $data['description'],
            $data['decision'],
            $data['action'],
            $data['staff'],
            $data['vendor'],
            $data['target_date'],
            $data['leads_import'],
            $data['participants'],
            $data['other_participants'],
            $data['company_names'],
            $data['agenda_id'],
            $data['section_break'],
            $data['serial_no'],
        );

        // Insert into the agendas table.
        $this->db->insert(db_prefix() . 'agendas', $data);
        $agenda_id = $this->db->insert_id();

        // Prepare meeting_management data using fields that remain in $data.
        $meeting_data = [
            'meeting_title'   => isset($data['meeting_title']) ? $data['meeting_title'] : '',
            'agenda'          => isset($data['agenda']) ? $data['agenda'] : '',
            'meeting_date'    => isset($data['meeting_date']) ? $data['meeting_date'] : '',
            'created_by'      => isset($data['created_by']) ? $data['created_by'] : '',
            'project_id'      => isset($data['project_id']) ? $data['project_id'] : '',
            'additional_note' => isset($data['additional_note']) ? $data['additional_note'] : '',
            'area_head'       => isset($data['area_head']) ? $data['area_head'] : '',
            'meeting_link'    => isset($data['meeting_link']) ? $data['meeting_link'] : '',
            'venue'           => isset($data['venue']) ? $data['venue'] : '',
        ];

        $this->db->insert(db_prefix() . 'meeting_management', $meeting_data);
        $minute_id = $this->db->insert_id();

        // Save agenda meeting files.
        $this->save_agends_files('agenda_meeting', $agenda_id);

        // Process the MOM detail items if provided.
        if (!empty($mom_detail) && is_array($mom_detail)) {
            foreach ($mom_detail as $key => $value) {
                // Process the staff field if it is an array.
                $staff = $critical =  '';
                if (isset($value['staff']) && !empty($value['staff']) && is_array($value['staff'])) {
                    $staff = implode(',', $value['staff']);
                }

                if (isset($value['critical']) && !empty($value['critical'])) {
                    $critical = $value['critical'];
                }


                // Build and insert the record into the agendas_details table.
                $agenda_detail = [
                    'agenda_id'   => $agenda_id,
                    'area'        => isset($value['area']) ? $value['area'] : '',
                    'description' => isset($value['description']) ? $value['description'] : '',
                    'decision'    => isset($value['decision']) ? $value['decision'] : '',
                    'action'      => isset($value['action']) ? $value['action'] : '',
                    'staff'       => $staff,
                    'vendor'      => isset($value['vendor']) ? $value['vendor'] : '',
                    'target_date' => isset($value['target_date']) ? $value['target_date'] : '',
                    'section_break' => isset($value['section_break']) ? $value['section_break'] : '',
                    'serial_no' => isset($value['serial_no']) ? $value['serial_no'] : '',
                    'reorder' => isset($value['order']) ? $value['order'] : '',
                    'critical' => $critical,
                ];

                $this->db->insert(db_prefix() . 'agendas_details', $agenda_detail);
                $agenda_detail_id = $this->db->insert_id();

                // Upload the file into the 'mom_attachments' folder.
                $iuploadedFiles = handle_mom_item_attachment_array('mom_attachments', $agenda_id, $agenda_detail_id, 'newitems', $key);
                if (!empty($iuploadedFiles) && is_array($iuploadedFiles)) {
                    foreach ($iuploadedFiles as $ifile) {
                        // Update the agendas_details record with the attachment.
                        $idata = ['attachments' => $ifile['file_name']];
                        $this->db->where('id', $agenda_detail_id);
                        $this->db->update(db_prefix() . 'agendas_details', $idata);
                    }
                }

                // Prepare data for minutes_details (copying most fields from agenda).
                $minutes_detail = $agenda_detail;
                $minutes_detail['minute_id'] = $minute_id;
                unset($minutes_detail['agenda_id']);

                // Insert the minutes_details record.
                $this->db->insert(db_prefix() . 'minutes_details', $minutes_detail);
                $minutes_detail_id = $this->db->insert_id();
                if (isset($value['staff']) && !empty($value['staff']) && is_array($value['staff'])) {

                    $task_arr = [
                        'staff_ids' => $staff,
                        'agenda_id' => $agenda_id,
                        'minutes_detail_id' => $minutes_detail_id
                    ];
                    $this->db->insert(db_prefix() . 'task_assigned_mom', $task_arr);
                }
                if ($critical > 0 && $critical != null) {
                    unset(
                        $minutes_detail['reorder'],
                        $minutes_detail['section_break'],
                        $minutes_detail['serial_no'],
                    );
                    $minutes_detail['meeting_detail_id'] = $minutes_detail_id;
                    $minutes_detail['project_id'] = $data['project_id'];
                    $this->db->insert(db_prefix() . 'critical_mom', $minutes_detail);
                }

                // If an attachment was uploaded for the agenda detail,
                // copy the file from the mom_attachments folder to the minutes_attachments folder.
                if (!empty($iuploadedFiles) && is_array($iuploadedFiles)) {
                    foreach ($iuploadedFiles as $ifile) {
                        // Build the source path based on the earlier upload.
                        $sourcePath = get_upload_path_by_type('meeting_management')
                            . 'mom_attachments' . '/' . $agenda_id . '/' . $agenda_detail_id . '/' . $ifile['file_name'];

                        // Build the destination folder path for minutes attachments.
                        $destinationFolder = get_upload_path_by_type('meeting_management')
                            . 'minutes_attachments' . '/' . $minute_id . '/' . $minutes_detail_id . '/';
                        if (!is_dir($destinationFolder)) {
                            mkdir($destinationFolder, 0755, true);
                        }
                        // Define the full destination file path.
                        $destinationPath = $destinationFolder . $ifile['file_name'];

                        // Copy the uploaded file to the minutes folder.
                        if (copy($sourcePath, $destinationPath)) {
                            // Update the minutes_details record with the attachment filename.
                            $idata = ['attachments' => $ifile['file_name']];
                            $this->db->where('id', $minutes_detail_id);
                            $this->db->update(db_prefix() . 'minutes_details', $idata);
                        }
                    }
                }
            }
        }

        return $agenda_id;
    }


    public function save_agends_files($related, $id)
    {
        // die('asdas');
        $uploadedFiles = handle_agends_attachments_array($related, $id);
        if ($uploadedFiles && is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $data = array();
                $data['dateadded'] = date('Y-m-d H:i:s');
                $data['rel_type'] = $related;
                $data['rel_id'] = $id;
                $data['staffid'] = get_staff_user_id();
                $data['attachment_key'] = app_generate_hash();
                $data['file_name'] = $file['file_name'];
                $data['filetype']  = $file['filetype'];
                $this->db->insert(db_prefix() . 'purchase_files', $data);
            }
        }
        return true;
    }
    public function get_meeting_attachments($related, $id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', $related);
        $this->db->order_by('dateadded', 'desc');
        $attachments = $this->db->get(db_prefix() . 'purchase_files')->result_array();
        return $attachments;
    }
    public function get_meeting_attachments_with_id($id)
    {
        $this->db->where('id', $id);
        $this->db->order_by('dateadded', 'desc');
        $attachments = $this->db->get(db_prefix() . 'purchase_files')->row();
        return $attachments;
    }
    // Update an existing agenda
    public function update_agenda($id, $data)
    {

        $affectedRows = 0;

        unset($data['client_id']);
        unset($data['area']);
        unset($data['description']);
        unset($data['decision']);
        unset($data['action']);
        unset($data['staff']);
        unset($data['vendor']);
        unset($data['target_date']);
        unset($data['isedi+`t']);

        $new_mom = [];
        if (isset($data['newitems'])) {
            $new_mom = $data['newitems'];
            unset($data['newitems']);
        }

        $update_mom = [];
        if (isset($data['items'])) {
            $update_mom = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if (isset($data['removed_items'])) {
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }
        // Update the agenda in the 'tblagendas' table
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'agendas', $data);
        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            // Also update the 'meeting_management' table
            $meeting_data = [
                'meeting_title' => $data['meeting_title'],
                'agenda' => $data['agenda'],
                'meeting_date' => $data['meeting_date'],
                'updated_by' => $data['updated_by']  // Ensure you're tracking who updated the record
            ];
            $this->db->where('id', $id);  // Use the same ID as the agenda
            $this->db->update(db_prefix() . 'meeting_management', $meeting_data);
        }

        if (count($new_mom) > 0) {
            foreach ($new_mom as $key => $value) {
                if (!empty($value['staff']) && isset($value['staff'])) {
                    $staff = implode(',', $value['staff']);
                } else {
                    $staff = '';
                }
                $mom_arr = [];
                $mom_arr['agenda_id'] = $id;
                $mom_arr['area'] = $value['area'];
                $mom_arr['description'] = $value['description'];
                $mom_arr['decision'] = $value['decision'];
                $mom_arr['action'] = $value['action'];
                $mom_arr['staff'] = $staff;
                $mom_arr['vendor'] = $value['vendor'];
                $mom_arr['target_date'] = $value['target_date'];

                $this->db->insert(db_prefix() . 'agendas_details', $mom_arr);
                $last_insert_id = $this->db->insert_id();

                $iuploadedFiles = handle_mom_item_attachment_array('mom_attachments', $id, $last_insert_id, 'newitems', $key);
                if ($iuploadedFiles && is_array($iuploadedFiles)) {
                    foreach ($iuploadedFiles as $ifile) {
                        $idata = array();
                        $idata['attachments'] = $ifile['file_name'];
                        $this->db->where('id', $ifile['item_id']);
                        $this->db->update(db_prefix() . 'agendas_details', $idata);
                    }
                }
            }
        }

        if (count($update_mom) > 0) {
            foreach ($update_mom as $key => $value) {
                if (!empty($value['staff']) && isset($value['staff'])) {
                    $staff = implode(',', $value['staff']);
                } else {
                    $staff = '';
                }
                $mom_arr = [];
                $mom_arr['agenda_id'] = $id;
                $mom_arr['area'] = $value['area'];
                $mom_arr['description'] = $value['description'];
                $mom_arr['decision'] = $value['decision'];
                $mom_arr['action'] = $value['action'];
                $mom_arr['staff'] = $staff;
                $mom_arr['vendor'] = $value['vendor'];
                $mom_arr['target_date'] = $value['target_date'];

                $this->db->where('id', $value['id']);
                $this->db->update(db_prefix() . 'agendas_details', $mom_arr);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
                $iuploadedFiles = handle_mom_item_attachment_array('mom_attachments', $id, $value['id'], 'items', $key);
                if ($iuploadedFiles && is_array($iuploadedFiles)) {
                    foreach ($iuploadedFiles as $ifile) {
                        $idata = array();
                        $idata['attachments'] = $ifile['file_name'];
                        $this->db->where('id', $ifile['item_id']);
                        $this->db->update(db_prefix() . 'agendas_details', $idata);
                    }
                }
            }
        }
        if (count($remove_order) > 0) {
            foreach ($remove_order as $remove_id) {
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'agendas_details')) {
                    $affectedRows++;
                }
            }
        }
        return $affected_rows;
    }
    public function delete_agenda($id)
    {
        // Delete the agenda from the 'tblagendas' table
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'agendas');
        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            // Also delete the corresponding entry from the 'meeting_management' table
            $this->db->where('id', $id);  // Use the same ID as the agenda
            $this->db->delete(db_prefix() . 'meeting_management');
        }

        return $affected_rows;
    }


    // Get a single agenda by ID
    public function get_agenda($id)
    {
        $this->db->select('tblagendas.*, tblprojects.name as project_name');
        $this->db->from('tblagendas');
        $this->db->join('tblprojects', 'tblprojects.id = tblagendas.project_id', 'left');
        $this->db->where('tblagendas.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    // Get existing minutes for the agenda
    public function get_minutes($agenda_id)
    {
        $this->db->select('meeting_title, minutes, agenda,additional_note,area_head,meeting_date,meeting_link,project_id,venue');
        $this->db->where('id', $agenda_id);
        $query = $this->db->get(db_prefix() . 'meeting_management');  // Use the correct table name here
        return $query->row();
    }

    // Save minutes for the agenda
    public function save_minutes($agenda_id, $minutes_data)
    {
        // Update the minutes in the database for the given agenda
        $this->db->where('id', $agenda_id);
        return $this->db->update(db_prefix() . 'meeting_management', $minutes_data);
    }

    // Fetch participants for the agenda
    // Restored the original function for convert_to_minutes
    public function get_selected_participants($agenda_id)
    {
        $this->db->select('participant_id');
        $this->db->from(db_prefix() . 'meeting_participants');
        $this->db->where('meeting_id', $agenda_id);
        $query = $this->db->get();

        return array_column($query->result_array(), 'participant_id');  // Return array of participant IDs
    }

    // Fetch detailed participants for viewing in view_meeting
    public function get_detailed_participants($agenda_id)
    {
        // Fetch staff participants
        $this->db->select('tblstaff.staffid as participant_id, tblstaff.firstname, tblstaff.lastname, tblstaff.email');
        $this->db->from(db_prefix() . 'meeting_participants');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'meeting_participants.participant_id', 'left');
        $this->db->where('meeting_id', $agenda_id);
        $staff_query = $this->db->get_compiled_select();

        // Fetch client participants
        $this->db->select('tblclients.userid as participant_id, tblclients.company as firstname, "" as lastname, tblcontacts.email');
        $this->db->from(db_prefix() . 'meeting_participants');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'meeting_participants.participant_id', 'left');
        $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid', 'left');
        $this->db->where('meeting_id', $agenda_id);
        $client_query = $this->db->get_compiled_select();

        // Combine both queries using UNION
        $query = $this->db->query($staff_query);

        return $query->result_array();
    }

    // Assign tasks to participants
    public function assign_task($task_data)
    {
        $this->db->insert(db_prefix() . 'meeting_tasks', $task_data);
        return $this->db->insert_id();
    }

    // Save a digital signature for the participant
    public function save_signature($agenda_id, $signature_path)
    {
        $this->db->where('id', $agenda_id);
        $this->db->update(db_prefix() . 'meeting_management', ['signature_path' => $signature_path]);
        return $this->db->affected_rows();
    }

    // Create a task for the meeting agenda
    public function create_task($task_data)
    {
        $this->db->insert(db_prefix() . 'meeting_tasks', $task_data);
        return $this->db->insert_id();
    }

    // Fetch tasks for a given agenda
    public function get_tasks_by_agenda($agenda_id)
    {
        $this->db->select('tblmeeting_tasks.*, tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tblmeeting_tasks');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmeeting_tasks.assigned_to');
        $this->db->where('agenda_id', $agenda_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    // Delete tasks by task IDs
    public function delete_tasks($task_ids)
    {
        if (!empty($task_ids)) {
            $this->db->where_in('id', $task_ids);
            return $this->db->delete(db_prefix() . 'meeting_tasks');
        }
    }

    // Update minutes for a given agenda
    public function update_minutes($agenda_id, $minutes_data)
    {

        $affectedRows = 0;
        unset(
            $minutes_data['isedit'],
            $minutes_data['area'],
            $minutes_data['description'],
            $minutes_data['decision'],
            $minutes_data['action'],
            $minutes_data['staff'],
            $minutes_data['vendor'],
            $minutes_data['target_date'],
            $minutes_data['participants'],
            $minutes_data['other_participants'],
            $minutes_data['company_names'],
            $minutes_data['agenda_id'],
            $minutes_data['leads_import'],
            $minutes_data['section_break'],
            $minutes_data['related_tasks_length'],
            $minutes_data['serial_no'],
        );

        $new_mom = [];
        if (isset($minutes_data['newitems'])) {
            $new_mom = $minutes_data['newitems'];
            unset($minutes_data['newitems']);
        }

        $update_mom = [];
        if (isset($minutes_data['items'])) {
            $update_mom = $minutes_data['items'];
            unset($minutes_data['items']);
        }

        $remove_order = [];
        if (isset($minutes_data['removed_items'])) {
            $remove_order = $minutes_data['removed_items'];
            unset($minutes_data['removed_items']);
        }


        $this->save_agends_files('agenda_meeting', $agenda_id);


        if (!empty($minutes_data)) {
            $this->db->where('id', $agenda_id);
            $this->db->update(db_prefix() . 'meeting_management', $minutes_data);
        }

        // echo '<pre>';
        // print_r($minutes_data);
        // die;
        if (count($new_mom) > 0) {
            foreach ($new_mom as $key => $value) {
                if (!empty($value['staff']) && isset($value['staff'])) {
                    $staff = implode(',', $value['staff']);
                } else {
                    $staff = '';
                }
                if (isset($value['critical']) && !empty($value['critical'])) {
                    $critical = $value['critical'];
                } else {
                    $critical = '';
                }
                $mom_arr = [];
                $mom_arr['minute_id'] = $agenda_id;
                $mom_arr['area'] = $value['area'];
                $mom_arr['description'] = $value['description'];
                $mom_arr['decision'] = $value['decision'];
                $mom_arr['action'] = $value['action'];
                $mom_arr['staff'] = $staff;
                $mom_arr['vendor'] = $value['vendor'];
                $mom_arr['target_date'] = $value['target_date'];
                $mom_arr['section_break'] = $value['section_break'];
                $mom_arr['serial_no'] = $value['serial_no'];
                $mom_arr['reorder'] = isset($value['order']) ? $value['order'] : null;
                $mom_arr['critical'] = $critical;
                $this->db->insert(db_prefix() . 'minutes_details', $mom_arr);
                $last_insert_id = $this->db->insert_id();
                if ($critical > 0 && $critical != null) {
                    unset(
                        $mom_arr['reorder'],
                        $mom_arr['section_break'],
                        $mom_arr['serial_no'],
                    );
                    $mom_arr['meeting_detail_id'] = $last_insert_id;
                    $mom_arr['project_id'] = $minutes_data['project_id'];
                    $this->db->insert(db_prefix() . 'critical_mom', $mom_arr);
                }
                $iuploadedFiles = handle_mom_item_attachment_array('minutes_attachments', $agenda_id, $last_insert_id, 'newitems', $key);
                if ($iuploadedFiles && is_array($iuploadedFiles)) {
                    foreach ($iuploadedFiles as $ifile) {
                        $idata = array();
                        $idata['attachments'] = $ifile['file_name'];
                        $this->db->where('id', $ifile['item_id']);
                        $this->db->update(db_prefix() . 'minutes_details', $idata);
                    }
                }
            }
        }

        if (count($update_mom) > 0) {
            foreach ($update_mom as $key => $value) {
                if (!empty($value['staff']) && isset($value['staff'])) {
                    $staff = implode(',', $value['staff']);
                } else {
                    $staff = '';
                }
                if (isset($value['critical']) && !empty($value['critical'])) {
                    $critical = $value['critical'];
                } else {
                    $critical = '';
                }
                $mom_arr = [];
                $mom_arr['minute_id'] = $agenda_id;
                $mom_arr['area'] = $value['area'];
                $mom_arr['description'] = $value['description'];
                $mom_arr['decision'] = $value['decision'];
                $mom_arr['action'] = $value['action'];
                $mom_arr['staff'] = $staff;
                $mom_arr['vendor'] = $value['vendor'];
                $mom_arr['target_date'] = $value['target_date'];
                $mom_arr['section_break'] = $value['section_break'];
                $mom_arr['serial_no'] = $value['serial_no'];
                $mom_arr['reorder'] = isset($value['order']) ? $value['order'] : '';
                $mom_arr['critical'] = $critical;


                $this->db->where('id', $value['id']);
                $this->db->update(db_prefix() . 'minutes_details', $mom_arr);

                $this->db->select('tblcritical_mom.*');
                $this->db->from('tblcritical_mom');
                $this->db->where('critical', 1);
                $this->db->where('meeting_detail_id', $value['id']);
                $query = $this->db->get()->result_array();
                unset(
                    $mom_arr['reorder'],
                    $mom_arr['section_break'],
                    $mom_arr['serial_no']
                );

                if ($critical > 0 && $critical != null) {
                    if (!empty($query)) {
                        // Record exists - update it
                        // $this->db->where('meeting_detail_id', $value['id']);
                        // $this->db->where('critical', 1);
                        // $this->db->update(db_prefix() . 'critical_mom', $mom_arr);
                    } else {
                        // Record doesn't exist - insert it
                        $mom_arr['meeting_detail_id'] = $value['id'];
                        $mom_arr['critical'] = 1; // Ensure critical flag is set
                        $mom_arr['project_id'] = $minutes_data['project_id'];
                        $this->db->insert(db_prefix() . 'critical_mom', $mom_arr);
                    }
                }
                // Record exists - update it
                $this->db->where('meeting_detail_id', $value['id']);
                $this->db->where('critical', 1);
                $this->db->update(db_prefix() . 'critical_mom', $mom_arr);

                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
                $iuploadedFiles = handle_mom_item_attachment_array('minutes_attachments', $agenda_id, $value['id'], 'items', $key);
                if ($iuploadedFiles && is_array($iuploadedFiles)) {
                    foreach ($iuploadedFiles as $ifile) {
                        $idata = array();
                        $idata['attachments'] = $ifile['file_name'];
                        $this->db->where('id', $ifile['item_id']);
                        $this->db->update(db_prefix() . 'minutes_details', $idata);
                    }
                }
            }
        }
        if (count($remove_order) > 0) {
            foreach ($remove_order as $remove_id) {
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'minutes_details')) {
                    $affectedRows++;
                }
            }
        }
    }

    // Save participants for a given agenda
    public function save_participants($agenda_id, $participants, $other_participants, $company_name)
    {
        // First, ensure the meeting ID exists in the database
        $this->db->where('id', $agenda_id);
        $meeting_exists = $this->db->get(db_prefix() . 'meeting_management')->row();

        if (!$meeting_exists) {
            throw new Exception('Meeting ID does not exist');
        }

        // First, delete all existing participants for the agenda
        $this->db->where('meeting_id', $agenda_id);
        $this->db->delete(db_prefix() . 'meeting_participants');

        // Insert new participants
        if (!empty($participants)) {
            foreach ($participants as $participant_id) {
                $data = [
                    'meeting_id' => $agenda_id,
                    'participant_id' => $participant_id, // Could be either staff or client ID
                ];

                // Insert participants
                $this->db->insert(db_prefix() . 'meeting_participants', $data);
            }
        }
        if (!empty($other_participants)) {
            foreach ($other_participants as $index => $participant) {
                // Trim and validate the participant name
                $participant_name = trim($participant);
                if (!empty($participant_name)) {
                    // Get the corresponding company name (or an empty string if not set)
                    $company = isset($company_name[$index]) ? trim($company_name[$index]) : '';

                    $data = [
                        'meeting_id'        => $agenda_id,
                        'other_participants' => $participant_name, // Store participant name
                        'company_names'       => $company,          // Store company name
                    ];

                    // Insert participant into the database
                    $this->db->insert(db_prefix() . 'meeting_participants', $data);
                }
            }
        }
    }


    // Get all tasks for an agenda (used in different contexts)
    public function get_all_tasks($agenda_id)
    {
        $this->db->select('tblmeeting_tasks.*, tblstaff.firstname, tblstaff.lastname');
        $this->db->from(db_prefix() . 'meeting_tasks');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'meeting_tasks.assigned_to', 'left');
        $this->db->where('agenda_id', $agenda_id);
        $query = $this->db->get();
        return $query->result_array();  // Returns an array of tasks
    }

    // Get meeting details for a given agenda
    public function get_meeting_details($agenda_id)
    {
        $this->db->select('id as meeting_id, meeting_title, agenda, meeting_date, project_id, minutes, created_by, signature_path, updated_by, additional_note,area_head,meeting_link,venue'); // Make sure 'id' is included as 'meeting_id'
        $this->db->from(db_prefix() . 'meeting_management'); // Replace with your actual table name
        $this->db->where('id', $agenda_id); // Assuming 'id' is the primary key of the meeting table
        $query = $this->db->get();

        return $query->row_array(); // Return the meeting details as an associative array
    }

    public function get_client_meetings($client_id)
    {
        $this->db->select('meeting_management.id, meeting_management.meeting_title, meeting_management.meeting_date, meeting_management.agenda');
        $this->db->from(db_prefix() . 'meeting_management as meeting_management');
        $this->db->join(db_prefix() . 'meeting_participants as participants', 'participants.meeting_id = meeting_management.id');
        $this->db->where('participants.participant_id', $client_id);  // Make sure this references the correct participant_id


        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_meeting_details_for_client($meeting_id, $client_id)
    {
        $this->db->select('m.meeting_title, m.agenda, m.meeting_date, m.minutes');
        $this->db->from(db_prefix() . 'meeting_management as m');
        $this->db->join(db_prefix() . 'meeting_participants as p', 'p.meeting_id = m.id', 'left');
        $this->db->where('m.id', $meeting_id);
        $this->db->where('p.participant_id', $client_id);  // Ensure the client is a participant

        return $this->db->get()->row_array();
    }

    public function get_meeting_notes($agenda_id)
    {
        // Select the 'minutes' field that holds the meeting notes
        $this->db->select('minutes');
        $this->db->from(db_prefix() . 'meeting_management');  // Ensure correct table name
        $this->db->where('id', $agenda_id);  // Assuming 'id' is the primary key
        $query = $this->db->get();

        // Return the 'minutes' field if found, otherwise return an empty string
        if ($query->num_rows() > 0) {
            $minutes = $query->row()->minutes;
            return !empty($minutes) ? $minutes : 'No meeting notes available.';
        }
        return 'No meeting found with the provided ID.';
    }
    public function delete_meeting_attachment($id)
    {
        $deleted = false;
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'purchase_files')->row();
        if ($attachment) {
            if (unlink(get_upload_path_by_type('meeting_management') . $attachment->rel_type . '/' . $attachment->rel_id . '/' . $attachment->file_name)) {
                $this->db->where('id', $attachment->id);
                $this->db->delete(db_prefix() . 'purchase_files');
                $deleted = true;
            }
            // Check if no attachments left, so we can delete the folder also
            $other_attachments = list_files(get_upload_path_by_type('meeting_management') . $attachment->rel_type . '/' . $attachment->rel_id);
            if (count($other_attachments) == 0) {
                delete_dir(get_upload_path_by_type('meeting_management') . $attachment->rel_type . '/' . $attachment->rel_id);
            }
        }

        return $deleted;
    }

    public function get_participants($meeting_id)
    {
        $this->db->where('meeting_id', $meeting_id);
        $this->db->where("other_participants != ''");
        $this->db->where("other_participants IS NOT NULL");
        $participants = $this->db->get(db_prefix() . 'meeting_participants')->result_array();
        return $participants;
    }

    public function update_mom_list($data)
    {
        $id = $data['id'];
        $agenda = $data['agenda'];
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'agendas', ['agenda' => $agenda]);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'meeting_management', ['agenda' => $agenda]);
        return true;
    }

    public function update_minutes_of_meeting($data)
    {
        $id = $data['id'];
        $minutes = $data['minutes'];
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'meeting_management', ['agenda' => $minutes]);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'agendas', ['agenda' => $minutes]);
        return true;
    }

    public function create_mom_row_template($name = '', $area = '', $description = '', $decision = '', $action = '', $staff = '', $vendor = '', $target_date = '', $attachments = [], $item_key = '', $section_break = '', $serial_no = '', $critical = '')
    {
        $row = '';

        $name_area = 'area';
        $name_description = 'description';
        $name_decision = 'decision';
        $name_action = 'action';
        $name_staff = 'staff';
        $name_vendor = 'vendor';
        $name_target_date  = 'target_date';
        $name_attachments = 'attachments';
        $name_section_break = 'section_break';
        $name_serial_no = 'serial_no';
        $name_critical = 'critical';

        // Add section break row first if it exists
        if ($section_break && $name != '') {
            $name_section_break = $name . '[section_break]';
            $row .= '<tr class="section-break-row"><td colspan="11" style="text-align:center;"><input type="text" class="form-control" name="' . $name_section_break . '" value="' . $section_break . '" placeholder="Section Break" style="text-align:center;width:100%;" /></td></tr>';
        }

        if ($name == '') {
            $row .= '<tr class="main"><td></td>';
            $manual = true;
        } else {
            $row .= '<tr class="sortable item">
        <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_area = $name . '[area]';
            $name_description = $name . '[description]';
            $name_decision = $name . '[decision]';
            $name_action = $name . '[action]';
            $name_staff = $name . '[staff][]';
            $name_vendor = $name . '[vendor]';
            $name_target_date = $name . '[target_date]';
            $name_attachments = $name . '[attachments]';
            $name_section_break = $name . '[section_break]';
            $name_serial_no = $name . '[serial_no]';
            $name_critical = $name . '[critical]';
            $manual = false;
        }

        $full_item_image = '';
        if (!empty($attachments['attachments']) && !empty($attachments['agenda_id'])) {
            $item_base_url = base_url('uploads/meetings/mom_attachments/' . $attachments['agenda_id'] . '/' . $attachments['id'] . '/' . $attachments['attachments']);
            $full_item_image = '<img class="images_w_table" src="' . $item_base_url . '" alt="' . $attachments . '" >';
        } elseif (!empty($attachments['attachments']) && !empty($attachments['minute_id'])) {
            $item_base_url = base_url('uploads/meetings/minutes_attachments/' . $attachments['minute_id'] . '/' . $attachments['id'] . '/' . $attachments['attachments']);
            $full_item_image = '<img class="images_w_table" src="' . $item_base_url . '" alt="' . $attachments . '" >';
        }
        if (!empty($name)) {
            if (!empty($serial_no) && $serial_no > 0) {
                $row .= '<td class="serial_no">' . render_input($name_serial_no, '', $serial_no, 'number', []) . '</td>';
            } else {
                $serial_no_updated = preg_replace("/[^0-9]/", "", $name);
                $row .= '<td class="serial_no">' . render_input($name_serial_no, '', $serial_no_updated, 'number', []) . '</td>';
            }
        } else {
            $row .= '<td class="serial_no"></td>';
        }
        $value   = ($critical > 0 ? '1' : '');
        $checked = ($critical > 0 ? 'checked' : '');
        $row .= '<td class="critical">'
            . '<input type="checkbox" '
            . $checked
            . ' class="form-check-input critical-checkbox" '
            . 'name="' . $name_critical . '" '
            . 'value="' . $value . '">'
            . '</td>';

        $row .= '<td class="area" style="text-align:left">
    <div class="form-group" app-field-wrapper="Area/Head" style="margin-bottom:2px;">
        <textarea name="' . $name_area . '" id="' . $name_area . '" class="form-control " rows="2" placeholder="Area/Head">' .
            htmlspecialchars($area, ENT_QUOTES, 'UTF-8') .
            '</textarea>
    </div>';
        if ($name != '' && $section_break == '') {
            $row .= '<a href="javascript:void(0)" onclick="add_section_break(this, \'' . htmlspecialchars($name_section_break, ENT_QUOTES, 'UTF-8') . '\');">Section break</a></td>';
        }

        $row .= '<td class="description">' . render_textarea($name_description, '', $description, ['rows' => 2, 'placeholder' => _l('description')], [], '', 'tinymce') . '</td>';
        $row .= '<td class="decision">' . render_textarea($name_decision, '', $decision, ['rows' => 2, 'placeholder' => _l('Decision')], [], '', 'tinymce') . '</td>';
        $row .= '<td class="action">' . render_textarea($name_action, '', $action, ['rows' => 2, 'placeholder' => _l('action')], [], '', 'tinymce') . '</td>';

        $getstaff = getstafflist();
        $selectedstaff = !empty($staff) ? $staff : array();
        if (!is_array($selectedstaff)) {
            $selectedstaff = explode(",", $selectedstaff);
        }

        $row .= '<td class="action_by staff-vendor-group">' .
            render_select($name_staff, $getstaff, ['staffid', 'fullname'], '', $selectedstaff, ['multiple' => 'multiple', 'data-none-selected-text' => 'Staff'], [], '', 'staff-select') .
            render_input($name_vendor, '', $vendor, '', ['placeholder' => 'Vendor/Customer Name']) .
            '</td>';
        $row .= '<td class="target_date">' . render_input($name_target_date, '', $target_date, 'date') . '</td>';
        $row .= '<td class=""><input type="file" extension="' . str_replace(['.', ' '], '', '.png,.jpg,.jpeg') . '" filesize="' . file_upload_max_size() . '" class="form-control" name="' . $name_attachments . '" accept="' . get_item_form_accepted_mimes() . '">' . $full_item_image . '</td>';

        if ($name == '') {
            $row .= '&nbsp;<td><button type="button" class="btn pull-right btn-info mom-add-item-to-table"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="mom_delete_item(this,' . $item_key . ',\'.mom-items\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }

        $row .= '</tr>';

        return $row;
    }

    public function get_critical_agenda()
    {
        $this->db->select('tblcritical_mom.*');
        $this->db->from('tblcritical_mom');
        $this->db->where('critical', 1);

        return $query = $this->db->get()->result_array();
    }
    public function change_status_mom($status, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'critical_mom', ['status' => $status]);
        return true;
    }

    /**
     * Changes the priority of a critical agenda item
     *
     * @param int $status The new priority status
     * @param int $id The ID of the critical agenda item to update
     * @return bool
     */
    public function change_priority_mom($status, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'critical_mom', ['priority' => $status]);
        return true;
    }

    /**
     * Updates the department of a critical agenda item
     *
     * @param int $id The ID of the critical agenda item to update
     * @param int $department_id The new department ID
     * @return bool
     */
    public function update_agenda_department($id, $department_id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'critical_mom', ['department' => $department_id]);
        return true;
    }

    /**
     * Generates a template row for the critical minutes of a meeting
     *
     * @param string $name
     * @param string $area
     * @param string $description
     * @param string $decision
     * @param string $action
     * @param string $staff
     * @param string $vendor
     * @param string $target_date
     * @param string $item_key
     * @param string $serial_no
     * @param string $department
     * @param string $date_closed
     * @param string $status
     * @param string $priority
     * @return string
     */
    public function create_mom_critical_row_template($name = '', $area = '', $description = '', $decision = '', $action = '', $staff = '', $vendor = '', $target_date = '', $item_key = '', $serial_no = '', $department = '', $date_closed = '', $status = '', $priority = '', $project_id = '')
    {
        $row = '';

        $name_area = 'area';
        $name_description = 'description';
        $name_decision = 'decision';
        $name_action = 'action';
        $name_staff = 'staff';
        $name_vendor = 'vendor';
        $name_target_date  = 'target_date';
        $name_serial_no = 'serial_no';
        $name_department = 'department';
        $name_date_closed = 'date_closed';
        $name_status = 'status';
        $name_priority = 'priority';
        $name_project_id = 'project_id';
        if ($name == '') {
            $row .= '<tr class="main">';
            $manual = true;
        } else {
            $row .= '<tr class="item">';
            $name_area = $name . '[area]';
            $name_description = $name . '[description]';
            $name_decision = $name . '[decision]';
            $name_action = $name . '[action]';
            $name_staff = $name . '[staff][]';
            $name_vendor = $name . '[vendor]';
            $name_target_date = $name . '[target_date]';
            $name_serial_no = $name . '[serial_no]';
            $name_department = $name . '[department]';
            $name_date_closed = $name . '[date_closed]';
            $name_status = $name . '[status]';
            $name_priority = $name . '[priority]';
            $name_project_id = $name . '[project_id]';
            $manual = false;
        }

        $getdept = getdeptmom();
        $selectedepartment = !empty($department) ? $department : [];

        if (!is_array($selectedepartment)) {
            $selectedepartment = explode(",", $selectedepartment);
        }

        // Ensure $selectedepartment is an array of IDs (not names)
        $selectedepartment = array_map('intval', $selectedepartment);

        $row .= '<td class="action_by staff-vendor-group">' .
            render_select($name_department, $getdept, ['departmentid', 'name'], '', $selectedepartment, [
                'data-none-selected-text' => 'Department'
            ], [], '', 'department-select') .
            '</td>';
        $row .= '<td class="area" style="text-align:left">
        <div class="form-group" app-field-wrapper="Area/Head" style="margin-bottom:2px;">
            <textarea name="' . $name_area . '" id="' . $name_area . '" class="form-control" rows="2" placeholder="Area/Head">' .
            htmlspecialchars($area, ENT_QUOTES, 'UTF-8') .
            '</textarea>
        </div></td>';

        $row .= '<td class="description">' . render_textarea($name_description, '', $description, ['rows' => 2, 'placeholder' => _l('description')]) . '</td>';
        $row .= '<td class="decision">' . render_textarea($name_decision, '', $decision, ['rows' => 2, 'placeholder' => _l('decision')]) . '</td>';
        $row .= '<td class="action">' . render_textarea($name_action, '', $action, ['rows' => 2, 'placeholder' => _l('action')]) . '</td>';

        $getstaff = getstafflist();
        $selectedstaff = !empty($staff) ? $staff : [];
        if (!is_array($selectedstaff)) {
            $selectedstaff = explode(",", $selectedstaff);
        }

        $row .= '<td class="action_by staff-vendor-group">' .
            render_select($name_staff, $getstaff, ['staffid', 'fullname'], '', $selectedstaff, ['multiple' => 'multiple', 'data-none-selected-text' => 'Staff'], [], '', 'staff-select') .
            render_input($name_vendor, '', $vendor, '', ['placeholder' => 'Vendor/Customer Name']) .
            '</td>';

        $row .= '<td class="target_date">' . render_input($name_target_date, '', $target_date, 'date') . '</td>';
        $row .= '<td class="date_closed">' . render_input($name_date_closed, '', $date_closed, 'date') . '</td>';
        $status_arr = [
            ['id' => 1, 'name' => 'Open'],
            ['id' => 2, 'name' => 'Closed']
        ];

        $row .= '<td class="status">' . render_select($name_status, $status_arr, ['id', 'name'], '', $status) . '</td>';

        $priority_arr = [
            ['id' => 2, 'name' => 'Low'],
            ['id' => 3, 'name' => 'Medium'],
            ['id' => 1, 'name' => 'High'],
            ['id' => 4, 'name' => 'Urgent']
        ];

        $row .= '<td class="priority">' . render_select($name_priority, $priority_arr, ['id', 'name'], '', $priority) . '</td>';
        $row .= '<td class="project_id">' . render_select($name_project_id, $this->projects_model->get_items(), ['id', 'name'], '', $project_id) . '</td>';
        if ($name == '') {
            $row .= '&nbsp;<td><button type="button" class="btn pull-right btn-info mom-critical-add-item-to-table"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="mom_critical_delete_item(this,' . $item_key . ',\'.mom-items\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }

        $row .= '</tr><div class="section-break-container"></div>';

        return $row;
    }

    /**
     * Add critical mom
     *
     * @param array $data Data for the critical mom
     * @return int|false Insert ID if successful, false otherwise
     */

    public function add_critical_mom($data)
    {

        unset($data['department']);
        unset($data['area']);
        unset($data['description']);
        unset($data['decision']);
        unset($data['action']);
        unset($data['staff']);
        unset($data['vendor']);
        unset($data['target_date']);
        unset($data['date_closed']);
        unset($data['status']);
        unset($data['priority']);
        $critical_arr = [];
        if (isset($data['newitems'])) {
            $critical_arr = $data['newitems'];
            unset($data['newitems']);
        }

        $last_insert_id = [];
        if (count($critical_arr) > 0) {
            foreach ($critical_arr as $key => $rqd) {
                if (isset($rqd['staff']) && !empty($rqd['staff']) && is_array($rqd['staff'])) {
                    $staff = implode(',', $rqd['staff']);
                }
                $dt_data = [
                    'department' => $rqd['department'],
                    'area' => $rqd['area'],
                    'description' => $rqd['description'],
                    'decision' => $rqd['decision'],
                    'action' => $rqd['action'],
                    'staff' => $staff,
                    'vendor' => $rqd['vendor'],
                    'target_date' => $rqd['target_date'],
                    'date_closed' => $rqd['date_closed'],
                    'status' => $rqd['status'],
                    'priority' => $rqd['priority'],
                    'critical' => 1,
                    'project_id' => isset($rqd['project_id']) ? $rqd['project_id'] : null,
                ];

                $this->db->insert(db_prefix() . 'critical_mom', $dt_data);
                $last_insert_id[] = $this->db->insert_id();
            }
            return $last_insert_id;
        }
        return false;
    }

    public function get_total_critical_agenda($type = '')
    {
        // always count rows
        $this->db->select('COUNT(*) AS total')
            ->from(db_prefix() . 'critical_mom');

        // filter by status if requested
        if ($type === 'open') {
            $this->db->where('status', 1);
        } elseif ($type === 'completed') {
            $this->db->where('status', 2);
        }

        $row = $this->db->get()->row();

        return $row ? (int) $row->total : 0;
    }

    public function get_datatable_preferences_critical()
    {
        $this->db->select('datatable_preferences');
        $this->db->from('tbluser_preferences_critical');
        $this->db->where('staff_id', get_staff_user_id());
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            // Decode the JSON string into an associative array if it's not empty
            if (!empty($row->datatable_preferences)) {
                return json_decode($row->datatable_preferences, true);
            }
        }
        // Return an empty array if no preferences are set
        return array();
    }
    public function add_update_preferences($data)
    {
        // Get the preferences data and the current user's ID.
        $preferences = $data['preferences'];
        $user_id = get_staff_user_id();

        // Convert the preferences to JSON if necessary.
        $preferences_json = is_array($preferences) || is_object($preferences)
            ? json_encode($preferences)
            : $preferences;

        // Prepare the data array for the query.
        $data = array(
            'staff_id' => $user_id, // Assuming the 'id' column holds the user ID.
            'datatable_preferences' => $preferences_json
        );

        // Check if a record already exists for the user.
        $this->db->where('staff_id', $user_id);
        $query = $this->db->get('tbluser_preferences_critical');

        if ($query->num_rows() > 0) {
            // Record exists, so update it.
            $this->db->where('staff_id', $user_id);
            return $this->db->update('tbluser_preferences_critical', array('datatable_preferences' => $preferences_json));
        } else {
            // No record found, so insert a new one.
            return $this->db->insert('tbluser_preferences_critical', $data);
        }
    }

    public function get_critical_tracker_pdf_content(){
        
    }
}
