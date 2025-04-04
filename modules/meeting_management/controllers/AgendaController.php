<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;

class AgendaController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Load language file and models
        $this->lang->load('meeting_management', 'english');
        $this->load->model('Meeting_model');
        $this->load->model('Projects_model'); // Built-in Perfex CRM Projects model
        $this->load->model('Clients_model'); // Built-in Perfex CRM Clients model

    }

    // View the list of all agendas
    // View the list of all agendas
    public function index()
    {
        $data['agendas'] = $this->Meeting_model->get_all_agendas();
        $data['title'] = _l('meeting_agenda');
        $this->load->view('meeting_management/agendas_list', $data);
    }

    // Create or edit an agenda
    public function create($id = '')
    {
        $data['clients'] = $this->Clients_model->get();

        if ($this->input->post()) {

            $agenda_data = [
                'meeting_title' => $this->input->post('meeting_title'),
                'project_id' => $this->input->post('project_id'),
                'agenda' => $this->input->post('agenda', false),
                'meeting_date' => $this->input->post('meeting_date'),
                'created_by' => get_staff_user_id(),
            ];
            $agenda_data_new = $this->input->post();
            $agenda_data_new['agenda'] = $this->input->post('agenda', false);
            $agenda_data_new['created_by'] = get_staff_user_id();
            
            if ($id == '') {
                // Insert new agenda
                $this->Meeting_model->create_agenda($agenda_data_new);
                set_alert('success', _l('meeting_agenda_created_success'));
            } else {
                // Update existing agenda
                $this->Meeting_model->update_agenda($id, $agenda_data_new);
                set_alert('success', _l('meeting_agenda_updated_success'));
            }

            redirect(admin_url('meeting_management/agendaController/index'));
        }

        $data['agenda'] = $this->Meeting_model->get_agenda($id);
        $data['title'] = _l('meeting_create_agenda');
        $this->load->view('meeting_management/agenda_form', $data);
    }

    // Delete an agenda
    public function delete($id)
    {
        $this->Meeting_model->delete_agenda($id);
        set_alert('success', _l('meeting_agenda_deleted_success'));
        redirect(admin_url('meeting_management/agendaController/index'));
    }

    // Handle the Ajax request to fetch projects based on client ID
    public function get_projects_by_client($client_id)
    {
        if ($this->input->is_ajax_request()) {
            $projects = $this->Projects_model->get('', ['clientid' => $client_id]);
            echo json_encode($projects);
            exit;
        }
    }
    public function view_meeting($agenda_id)
    {
        // Fetch meeting details
        $data['meeting'] = $this->Meeting_model->get_meeting_details($agenda_id);

        // Fetch participants using the detailed participant function
        $data['participants'] = $this->Meeting_model->get_detailed_participants($agenda_id);
        $data['meeting_notes'] = $this->Meeting_model->get_meeting_notes($agenda_id);  // Assuming the method fetches notes


        // Fetch tasks
        $data['tasks'] = $this->Meeting_model->get_tasks_by_agenda($agenda_id);
        $data['title'] = _l('view_meeting');
        // Load the view
        $data['attachments'] = $this->Meeting_model->get_meeting_attachments('agenda_meeting', $agenda_id);
        $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);
        $this->load->view('meeting_management/view_meeting', $data);
    }

    public function export_to_pdf($agenda_id)
    {
        // Initialize Dompdf
        $pdf = new Dompdf();

        // Fetch meeting details and other data
        $meeting_details = $this->Meeting_model->get_meeting_details($agenda_id);
        $participants = $this->Meeting_model->get_detailed_participants($agenda_id);
        $tasks = $this->Meeting_model->get_tasks_by_agenda($agenda_id);

        // Fetch the meeting notes
        $meeting_notes = $this->Meeting_model->get_meeting_notes($agenda_id);

        // Load your HTML view for the PDF content
        $data = [
            'meeting' => $meeting_details,
            'participants' => $participants,
            'tasks' => $tasks,
            'meeting_notes' => $meeting_notes  // Add meeting notes here
        ];
        $data['other_participants'] = $this->Meeting_model->get_participants($agenda_id);
        $html_content = $this->load->view('meeting_management/pdf_template', $data, true);

        // Set the PDF content
        $pdf->loadHtml($html_content);
        $pdf->setPaper('A4', 'portrait');

        // Render the PDF
        $pdf->render();

        // Output the PDF to the browser
        $pdf->stream("Meeting_Agenda_{$agenda_id}.pdf", array("Attachment" => 1));  // Download the PDF
    }

    public function update_mom_list()
    {
        $data = $this->input->post();
        $this->Meeting_model->update_mom_list($data);
        echo json_encode(['success' => true]);
        die();
    }
}
