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
        $this->load->model('drawing_management_model');
        $this->load->model('purchase/purchase_model');
        hooks()->do_action('purchase_init');
    }

    public function index()
    {

        $this->load->model('projects_model');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['projects'] = $this->projects_model->get();
        $this->load->view('dashboard/dashboard', $data);
    }

    public function get_inventory_dashboard()
    {
        $data = $this->input->post();
        $result = $this->dashboard_model->get_inventory_dashboard($data);

        echo json_encode($result);
        die;
    }

    public function dicipline_status_charts()
    {
        $this->load->database();
        $default_project = get_default_project();
        $master_id = $this->drawing_management_model->get_default_dms_project($default_project);

        // Fetch all disciplines
        $disciplines = $this->db->select('id, name')->get(db_prefix() . 'dms_discipline')->result_array();

        // Fetch counts in one query to avoid loop hits
        $this->db->select('discipline, design_stage, COUNT(*) as total');
        if (!empty($master_id)) {
            $this->db->where('master_id', $master_id);
        }
        $this->db->group_by(['discipline', 'design_stage']);
        $counts = $this->db->get(db_prefix() . 'dms_items')->result_array();

        // Organize counts into an associative array
        $count_map = [];
        foreach ($counts as $row) {
            $count_map[$row['discipline']][$row['design_stage']] = $row['total'];
        }

        // Define expected stages (1 to 9)
        $designStages = range(1, 9);

        $output = [
            'aaData' => [],
            'iTotalRecords' => count($disciplines),
            'iTotalDisplayRecords' => count($disciplines),
        ];

        foreach ($disciplines as $discipline) {
            $row = [$discipline['name']];
            foreach ($designStages as $stage) {
                $row[] = isset($count_map[$discipline['id']][$stage]) ? $count_map[$discipline['id']][$stage] : 0;
            }
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        die();
    }

    public function get_drawing_management_dashboard()
    {
        $default_project = get_default_project();
        $module_name = 'drawing_dashboard';
        $data = $this->input->post();
        $projects = $data['projects'];

        $project_filter_name = 'project';
        update_module_filter($module_name, $project_filter_name, NULL);
        if (!empty($projects)) {
			update_module_filter($module_name, $project_filter_name, $projects);
		}

        $master_id = $this->drawing_management_model->get_default_dms_project($default_project);
        // Fetch all disciplines
        $disciplines = $this->db->select('id, name')->get(db_prefix() . 'dms_discipline')->result_array();

        // Fetch counts grouped by discipline and design stage
        $this->db->select('discipline, design_stage, COUNT(*) as total');
        if (!empty($master_id)) {
            $this->db->where('master_id', $master_id);
        }
        $this->db->group_by(['discipline', 'design_stage']);

        $counts = $this->db->get(db_prefix() . 'dms_items')->result_array();


        // Get approval status counts
        $this->db->select('approve, COUNT(*) as count');
        if (!empty($master_id)) {
            $this->db->where('master_id', $master_id);
        }
        $this->db->group_by('approve');
        $approval_counts = $this->db->get(db_prefix() . 'dms_items')->result_array();

        // Initialize approval counts
        $approved = 0;
        $draft = 0;
        $rejected = 0;

        foreach ($approval_counts as $row) {
            if ($row['approve'] == 1) {
                $approved = $row['count'];
            } elseif ($row['approve'] == 0) {
                $draft = $row['count'];
            } elseif ($row['approve'] == 2) {
                $rejected = $row['count'];
            }
        }

        // Calculate total count and percentages
        $total = $approved + $draft + $rejected;

        // Initialize percentages
        $approved_percent = 0;
        $draft_percent = 0;
        $rejected_percent = 0;

        if ($total > 0) {
            // Calculate raw percentages
            $approved_percent = ($approved / $total) * 100;
            $draft_percent = ($draft / $total) * 100;
            $rejected_percent = ($rejected / $total) * 100;

            // Apply minimum 1% rule for non-zero values
            if ($approved > 0 && $approved_percent < 1) $approved_percent = 1;
            if ($draft > 0 && $draft_percent < 1) $draft_percent = 1;
            if ($rejected > 0 && $rejected_percent < 1) $rejected_percent = 1;

            // Calculate total after minimum adjustments
            $adjusted_total = $approved_percent + $draft_percent + $rejected_percent;

            // Normalize to ensure exact 100% total
            if ($adjusted_total > 0) {
                $approved_percent = round(($approved_percent / $adjusted_total) * 100);
                $draft_percent = round(($draft_percent / $adjusted_total) * 100);
                $rejected_percent = 100 - $approved_percent - $draft_percent;
            }

            // Final check to ensure no negative percentages
            if ($approved_percent < 0) $approved_percent = 0;
            if ($draft_percent < 0) $draft_percent = 0;
            if ($rejected_percent < 0) $rejected_percent = 0;
        }

        $designStages = range(1, 9);
        $stageLabels = [
            1 => 'Documents Under Review',
            2 => 'Briefs',
            3 => 'Concept',
            4 => 'Schematic',
            5 => 'Design Development',
            6 => 'Tender Documents',
            7 => 'Construction Documents',
            8 => 'Shop Drawings',
            9 => 'As-Built'
        ];

        // Initialize stage-wise discipline counts
        $disciplineMap = [];

        foreach ($disciplines as $disc) {
            $disciplineMap[$disc['id']] = [
                'name' => $disc['name'],
                'stages' => array_fill_keys($designStages, 0)
            ];
        }

        foreach ($counts as $row) {
            if (isset($disciplineMap[$row['discipline']]) && isset($disciplineMap[$row['discipline']]['stages'][$row['design_stage']])) {
                $disciplineMap[$row['discipline']]['stages'][$row['design_stage']] = (int)$row['total'];
            }
        }

        // Filter out disciplines with all 0s
        $filteredDisciplineMap = [];
        foreach ($disciplineMap as $discId => $disc) {
            if (array_sum($disc['stages']) > 0) {
                $filteredDisciplineMap[$discId] = $disc;
            }
        }

        // Transpose to stage-wise datasets, only keep stages that have non-zero values in at least one discipline
        $stacked_labor_values = [];
        $stacked_labor_labels = array_values(array_column($filteredDisciplineMap, 'name'));

        foreach ($stageLabels as $stage => $label) {
            $values = [];
            foreach ($filteredDisciplineMap as $disc) {
                $values[] = $disc['stages'][$stage];
            }

            // Only include stages that have non-zero values across disciplines
            if (array_sum($values) > 0) {
                $stacked_labor_values[$label] = $values;
            }
        }

        echo json_encode([
            'stacked_labor_labels' => $stacked_labor_labels,
            'stacked_labor_values' => $stacked_labor_values,
            'approved_percent' => $approved_percent,
            'draft_percent' => $draft_percent,
            'rejected_percent' => $rejected_percent
        ]);
    }
}
