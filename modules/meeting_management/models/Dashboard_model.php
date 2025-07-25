<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a dashboard model.
 */
class Dashboard_model extends App_Model
{
	public function get_critical_tracker_dashboard($data)
	{
		$response = [];

		// Get total count of all critical items
		$total_sql = "SELECT COUNT(*) as total FROM tblcritical_mom";
		$total_query = $this->db->query($total_sql);
		$total_result = $total_query->row_array();
		$total = $total_result['total'];

		// Get count of open items (status = 1)
		$open_sql = "SELECT COUNT(*) as open_count FROM tblcritical_mom WHERE status = 1";
		$open_query = $this->db->query($open_sql);
		$open_result = $open_query->row_array();
		$open_count = $open_result['open_count'];

		// Get count of closed items (status = 2)
		$closed_sql = "SELECT COUNT(*) as closed_count FROM tblcritical_mom WHERE status = 2";
		$closed_query = $this->db->query($closed_sql);
		$closed_result = $closed_query->row_array();
		$closed_count = $closed_result['closed_count'];

		// Calculate ratios (percentage)
		$response['open_ratio'] = $total > 0 ? round(($open_count / $total) * 100, 1) : 0;
		$response['closed_ratio'] = $total > 0 ? round(($closed_count / $total) * 100, 1) : 0;


		$dept_sql = "SELECT d.name as department_name, COUNT(c.id) as issue_count 
                FROM tblcritical_mom c
                LEFT JOIN tbldepartments d ON d.departmentid = c.department
                WHERE c.department IS NOT NULL AND c.department != ''
                GROUP BY c.department
                ORDER BY issue_count DESC";
		$dept_query = $this->db->query($dept_sql);
		$dept_results = $dept_query->result_array();

		$response['dept_labels'] = array_column($dept_results, 'department_name');
		$response['dept_data'] = array_column($dept_results, 'issue_count');


		// $priority_sql = "SELECT 
		//             SUM(CASE WHEN priority = 4 THEN 1 ELSE 0 END) as urgent,
		//             SUM(CASE WHEN priority = 1 THEN 1 ELSE 0 END) as high,
		//             SUM(CASE WHEN priority = 3 THEN 1 ELSE 0 END) as medium,
		//             SUM(CASE WHEN priority = 2 THEN 1 ELSE 0 END) as low
		//             FROM tblcritical_mom";
		// $priority_query = $this->db->query($priority_sql);
		// $priority_result = $priority_query->row_array();

		// $response['priority_heatmap'] = [
		// 	['Urgent', $priority_result['urgent']],
		// 	['High', $priority_result['high']],
		// 	['Medium', $priority_result['medium']],
		// 	['Low', $priority_result['low']]
		// ];

		$this->db->select('target_date');
		$this->db->from('tblcritical_mom');
		$this->db->where('target_date !=', '');
		$query = $this->db->get();
		$all_dates = $query->result_array();

		// Initialize counters
		$overdue_counts = [
			'< 1 Week' => 0,
			'1-2 Weeks' => 0,
			'2-4 Weeks' => 0,
			'> 4 Weeks' => 0
		];
		// Process each record in PHP
		foreach ($all_dates as $record) {
			$target_date = $record['target_date'];

			// Skip if empty string
			if ($target_date === '') {
				continue;
			}

			// Convert to DateTime object
			$target_date = new DateTime($target_date);
			$today = new DateTime();

			// Skip future dates
			if ($target_date > $today) {
				continue;
			}

			// Calculate days overdue
			$interval = $today->diff($target_date);
			$days_overdue = $interval->days;

			// Categorize
			if ($days_overdue <= 7) {
				$overdue_counts['< 1 Week']++;
			} elseif ($days_overdue <= 14) {
				$overdue_counts['1-2 Weeks']++;
			} elseif ($days_overdue <= 28) {
				$overdue_counts['2-4 Weeks']++;
			} else {
				$overdue_counts['> 4 Weeks']++;
			}
		}

		$response['overdue_tracker'] = $overdue_counts;

		return $response;
	}
}
