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
        $this->load->model('purchase/purchase_model');
    }

    public function index()
    {
        $this->load->model('projects_model');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['commodity_groups_pur'] = get_budget_head_project_wise();
        $data['projects'] = $this->projects_model->get();
        $this->load->view('dashboard/dashboard', $data);
    }

    public function get_critical_tracker_dashboard()
    {
        $data = $this->input->post();
        $result = $this->dashboard_model->get_critical_tracker_dashboard($data);
        echo json_encode($result);
        die;
    }

    public function action_by_responsibility_tracker()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('staff_model');

            // Get unique staff
            $this->db->select("staff");
            $this->db->from(db_prefix() . 'critical_mom');
            $this->db->where("staff IS NOT NULL");
            $this->db->group_by("staff");
            $staff_list = $this->db->get()->result_array();

            // Get unique vendors (vendor is name, not ID)
            $this->db->select("vendor");
            $this->db->from(db_prefix() . 'critical_mom');
            $this->db->where("vendor IS NOT NULL");
            $this->db->group_by("vendor");
            $vendor_list = $this->db->get()->result_array();

            $table_data = [];

            // Loop through staff
            foreach ($staff_list as $row) {
                $staff_id = $row['staff'];

                // Count open items
                $this->db->where('staff', $staff_id);
                $this->db->where('status', 1);
                $open = $this->db->count_all_results(db_prefix() . 'critical_mom');

                // Count closed items
                $this->db->where('staff', $staff_id);
                $this->db->where('status', 2);
                $closed = $this->db->count_all_results(db_prefix() . 'critical_mom');

                $total = $open + $closed;
                $closed_percent = $total > 0 ? round(($closed / $total) * 100, 2) : 0;

                $table_data[] = [
                    get_staff_full_name($staff_id),
                    $open,
                    $closed,
                    $closed_percent . '%'
                ];
            }

            // Loop through vendors
            foreach ($vendor_list as $row) {
                $vendor_name = $row['vendor'];

                // Count open items
                $this->db->where('vendor', $vendor_name);
                $this->db->where('status', 1);
                $open = $this->db->count_all_results(db_prefix() . 'critical_mom');

                // Count closed items
                $this->db->where('vendor', $vendor_name);
                $this->db->where('status', 2);
                $closed = $this->db->count_all_results(db_prefix() . 'critical_mom');

                $total = $open + $closed;
                $closed_percent = $total > 0 ? round(($closed / $total) * 100, 2) : 0;

                $table_data[] = [
                    $vendor_name,
                    $open,
                    $closed,
                    $closed_percent . '%'
                ];
            }

            echo json_encode(['aaData' => $table_data]);
            die();
        }
    }
}
