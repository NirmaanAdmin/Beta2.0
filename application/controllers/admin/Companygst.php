<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Companygst extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('company_gst_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('company_gst');
        }
        $data['title'] = _l('company_gst');
        $this->load->view('admin/company_gst/manage_company_gst', $data);
    }

    public function company_gst()
    {
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->company_gst_model->add_company_gst($this->input->post());
                echo json_encode([
                    'success' => $id ? true : false,
                    'message' => $id ? _l('added_successfully') : '',
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->company_gst_model->update_company_gst($data, $id);
                $message = _l('updated_successfully');
                echo json_encode(['success' => $success, 'message' => $message]);
            }
        }
    }

    public function delete_company_gst($id)
    {
        $response = $this->company_gst_model->delete_company_gst($id);
        set_alert('success', _l('deleted', _l('company_gst')));
        redirect(admin_url('companygst'));
    }

    public function make_default_gst($id)
    {
        $response = $this->company_gst_model->make_default_gst($id);
        set_alert('success', _l('default_gst_set'));
        redirect(admin_url('companygst'));
    }
}

?>