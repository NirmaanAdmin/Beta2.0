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
            // Get staff assignments (staffid is stored in staff column)
            $this->db->select([
                'tblcritical_mom.staff',
                'CONCAT(staff.firstname, " ", staff.lastname) as assigned_to',
                'COUNT(CASE WHEN tblcritical_mom.status = 1 THEN 1 END) as open_count',
                'COUNT(CASE WHEN tblcritical_mom.status = 2 THEN 1 END) as closed_count',
                'COUNT(*) as total'
            ]);
            $this->db->from('tblcritical_mom');
            $this->db->join('tblstaff as staff', 'staff.staffid = tblcritical_mom.staff', 'left');
            $this->db->where('tblcritical_mom.staff IS NOT NULL');
            $this->db->group_by('tblcritical_mom.staff');
            $staff_results = $this->db->get()->result_array();

            // Get vendor assignments (vendor name is stored in vendor column)
            $this->db->select([
                'tblcritical_mom.vendor as vendor_name',
                'tblcritical_mom.vendor as assigned_to', // Using vendor name directly
                'COUNT(CASE WHEN tblcritical_mom.status = 1 THEN 1 END) as open_count',
                'COUNT(CASE WHEN tblcritical_mom.status = 2 THEN 1 END) as closed_count',
                'COUNT(*) as total'
            ]);
            $this->db->from('tblcritical_mom');
            $this->db->where('tblcritical_mom.vendor IS NOT NULL');
            $this->db->where('tblcritical_mom.vendor !=', ''); // Exclude empty vendor names
            $this->db->group_by('tblcritical_mom.vendor');
            $vendor_results = $this->db->get()->result_array();

            // Combine results
            $combined_results = array_merge($staff_results, $vendor_results);

            // Prepare DataTable response
            $output = [
                'data' => [],
                'recordsTotal' => count($combined_results),
                'recordsFiltered' => count($combined_results),
                'draw' => $this->input->post('draw')
            ];
            foreach ($combined_results as $row) {
                $closed_percentage = $row['total'] > 0 ? round(($row['closed_count'] / $row['total']) * 100) : 0;

                // Determine if this is a staff or vendor record
                $is_staff = isset($row['staff']);

                $output['data'][] = [
                    // Assigned To column
                    $is_staff
                        ? '<a href="' . admin_url('profile/' . $row['staff']) . '">' . $row['assigned_to'] . '</a>'
                        : $row['vendor_name'], // Vendor name as plain text

                    // Open count
                    $row['open_count'],

                    // Closed count
                    $row['closed_count'],

                    // Closed percentage with color coding
                    '<span class="' . ($closed_percentage >= 75 ? 'text-success' : ($closed_percentage >= 50 ? 'text-warning' : 'text-danger')) . '">'
                        . $closed_percentage . '%</span>'
                ];
            }

            echo json_encode($output);
            die();
        }
    }

    public function upcoming_deadlines()
    {
        if ($this->input->is_ajax_request()) {
            $select = [
                'tblcritical_mom.id',
                'tblcritical_mom.description as description',
                'tblcritical_mom.target_date as target_date',
                'tblcritical_mom.staff',
                'tblcritical_mom.vendor as vendor',
                'tblcritical_mom.department',
                'tblcritical_mom.area as area', // Include the area field
                'tblstaff.firstname as staff_firstname',
                'tblstaff.lastname as staff_lastname',
                'tbldepartments.name as department_name'
            ];

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = 'tblcritical_mom';

            $join = [
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblcritical_mom.staff',
                'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tblcritical_mom.department'
            ];

            $where = ['AND tblcritical_mom.target_date >= CURDATE()'];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                // Department name
                $department = !empty($aRow['department_name']) ? $aRow['department_name'] : _l('not_assigned');

                // Action by (BOTH staff AND vendor if available)
                $action_by = [];
                if (!empty($aRow['staff'])) {
                    $first = $aRow['staff_firstname'] ?? '';
                    $last = $aRow['staff_lastname'] ?? '';
                    $full_name = trim($first . ' ' . $last);
                    $action_by[] = '<a href="' . admin_url('profile/' . $aRow['staff']) . '">' . $full_name . '</a>';
                }
                if (!empty($aRow['vendor'])) {
                    $action_by[] = $aRow['vendor'];
                }
                $action_by_display = !empty($action_by) ? implode(', ', $action_by) : _l('not_assigned');

                // Target date and badge (unchanged)
                $target_date_raw = $aRow['target_date'] ?? null;
                $target_date = $target_date_raw ? _d($target_date_raw) : '';
                $date_display = $target_date;

                if ($target_date_raw) {
                    $today = new DateTime();
                    $target = new DateTime($target_date_raw);
                    $days_remaining = $today->diff($target)->days;

                    if ($target >= $today && $days_remaining <= 7) {
                        $badge_class = $days_remaining <= 3 ? 'label-danger' : 'label-warning';
                        $date_display .= ' <span class="label ' . $badge_class . ' pull-right">' .
                            _l('days_remaining', $days_remaining) . '</span>';
                    }
                }

                $output['aaData'][] = [
                    $department ?? '',
                    $aRow['area'] ?? '',
                    $aRow['description'] ?? '',
                    $action_by_display ?? '', // Now shows BOTH staff & vendor if available
                    $date_display ?? ''
                ];
            }

            echo json_encode($output);
            die();
        }
    }
}
