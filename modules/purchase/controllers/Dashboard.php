<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a dashboard.
 */
class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('purchase_model');
        hooks()->do_action('purchase_init');
    }

    public function index()
    {
        $this->load->model('projects_model');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['commodity_groups_pur'] = get_budget_head_project_wise();
        $data['projects'] = $this->projects_model->get();
        $this->load->view('dashboard/dashboard', $data);
    }

    public function get_purchase_order_dashboard()
    {
        $data = $this->input->post();
        $result = $this->dashboard_model->get_purchase_order_dashboard($data);
        echo json_encode($result);
        die;
    }

    public function billing_dashboard()
    {
        $this->load->model('projects_model');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['projects'] = $this->projects_model->get();
        $data['order_tagged_detail'] = $this->purchase_model->get_order_tagged_detail();
        $this->load->view('dashboard/billing_dashboard', $data);
    }

    public function get_billing_dashboard()
    {
        $data = $this->input->post();
        $result = $this->dashboard_model->get_billing_dashboard($data);
        echo json_encode($result);
        die;
    }
}

?>