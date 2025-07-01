<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * timesheets
 */
class timesheets extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('timesheets_model');
		$this->load->model('departments_model');
		$this->load->model('hr_payroll/hr_payroll_model');
		hooks()->do_action('timesheets_init');
		if (!class_exists('qrstr')) {
			include_once(TIMESHEETS_PATH_PLUGIN . '/phpqrcode/qrlib.php');
		}
	}

	/* List all announcements */
	public function index()
	{
		if (!has_permission('timesheets_dashboard', '', 'view')) {
			access_denied('timesheets');
		}
		$data['google_ids_calendars'] = $this->misc_model->get_google_calendar_ids();

		$data['title'] = _l('timesheets');
		$this->load->view('timesheets_dashboard', $data);
	}

	/**
	 * setting
	 * @return
	 */
	public function setting()
	{
		if (!has_permission('timesheet_settings', '', 'view')) {
			access_denied('timesheets');
		}

		$this->load->model('staff_model');
		$this->load->model('roles_model');
		$this->load->model('contracts_model');

		$data['group'] = $this->input->get('group');

		$data['title'] = _l('setting');
		$data['tab'][] = 'manage_leave';
		$data['tab'][] = 'manage_dayoff';
		$data['tab'][] = 'approval_process';
		$data['tab'][] = 'timekeeping_settings';
		$data['tab'][] = 'default_settings';
		$data['tab'][] = 'type_of_leave';
		$data['tab'][] = 'permission';
		$data['tab'][] = 'valid_ip';
		$data['tab'][] = 'reset_data';
		$data['tab'][] = 'api_integration';
		if ($data['group'] == '') {
			$data['group'] = 'contract_type';
		} elseif ($data['group'] == 'manage_dayoff') {
			$data['holiday'] = $this->timesheets_model->get_break_dates();
		} elseif ($data['group'] == 'overtime_setting') {
			$data['overtime_setting'] = $this->timesheets_model->get_overtime_setting();
		} elseif ($data['group'] == 'shift') {
			$data['shift'] = $this->timesheets_model->get_shift_sc();
		}
		$data['tabs']['view'] = 'includes/' . $data['group'];
		$data['month'] = $this->timesheets_model->get_month();

		$data['staff'] = $this->staff_model->get('', 'active = 1');
		$data['department'] = $this->departments_model->get();

		$data['role'] = $this->roles_model->get();
		if ($data['group'] == 'approval_process') {
			if ($this->input->post()) {
				$data = $this->input->post();
				$id = $data['approval_setting_id'];
				unset($data['approval_setting_id']);
				if ($id == '') {
					if (!has_permission('staffmanage_approval', '', 'create')) {
						access_denied('approval_process');
					}
					$id = $this->timesheets_model->add_approval_process($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('approval_process')));
					}
				} else {
					if (!has_permission('staffmanage_approval', '', 'edit')) {
						access_denied('approval_process');
					}
					$success = $this->timesheets_model->update_approval_process($id, $data);
					if ($success) {
						set_alert('success', _l('ts_updated_successfully', _l('approval_process')));
					}
				}
			}
			$data['approval_setting'] = $this->timesheets_model->get_approval_process();
			$data['title'] = _l('approval_process');
			$data['staffs'] = $this->staff_model->get();
		}
		if ($data['group'] == 'manage_leave') {
			$type_of_leave_selected = 8;
			$data_type_of_leave = get_timesheets_option('type_of_leave_selected');
			if ($data_type_of_leave) {
				$type_of_leave_selected = $data_type_of_leave;
			}
			$start_year_for_annual_leave_cycle = date('Y');
			$data_option = get_timesheets_option('start_year_for_annual_leave_cycle');
			if ($data_option) {
				$start_year_for_annual_leave_cycle = $data_option;
			}

			$data_leave = $this->get_setting_annual_leave($start_year_for_annual_leave_cycle, $type_of_leave_selected);
			$data['max_row'] = $data_leave['max_row'];
			$data['leave_of_the_year'] = $data_leave['leave_of_the_year'];
			$data['type_of_leave'] = $this->timesheets_model->get_type_of_leave();
		}

		if ($data['group'] == 'type_of_leave') {
			$data['type_of_leave'] = $this->timesheets_model->get_type_of_leave();
		}

		if ($data['group'] == 'valid_ip') {
			$data_leave = $this->get_setting_valid_ip();
			$data['max_row'] = $data_leave['max_row'];
			$data['list_ip_data'] = $data_leave['list_ip_data'];
		}

		$this->load->view('manage_setting', $data);
	}

	/**
	 * leave
	 * leave
	 * @return view
	 */
	public function leave()
	{
		$data['title'] = _l('manage_leave');

		$data['positions'] = $this->timesheets_model->get_job_position();
		$data['workplace'] = $this->timesheets_model->get_workplace();
		$data['contract_type'] = $this->timesheets_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['allowance_type'] = $this->timesheets_model->get_allowance_type();
		$data['salary_form'] = $this->timesheets_model->get_salary_form();

		$this->load->view('timesheets/manage_leave', $data);
	}

	/**
	 * table leave
	 * @return view
	 */
	public function table_leave()
	{

		$this->app->get_table_data(module_views_path('timesheets', 'table_leave'));
	}

	/**
	 * timesheets file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return view
	 */
	public function timesheets_file($id, $rel_id)
	{
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->timesheets_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('timesheets/includes/_file', $data);
	}

	/**
	 * get staff role
	 * @return json
	 */
	public function get_staff_role()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {

				$id = $this->input->post('id');
				$name_object = $this->db->query('select r.name from ' . db_prefix() . 'staff as s join ' . db_prefix() . 'roles as r on s.role = r.roleid where s.staffid = ' . $id)->row();
			}
		}
		if ($name_object) {
			echo json_encode([
				'name' => $name_object->name,
			]);
		}
	}

	/**
	 * timekeeping
	 * @return view
	 */
	public function timekeeping()
	{
		if (!(has_permission('attendance_management', '', 'view_own') || has_permission('attendance_management', '', 'view') || is_admin())) {
			access_denied('timekeeping');
		}

		$this->load->model('staff_model');
		$data['title'] = _l('timesheets');
		$month = date('m');
		$month_year = date('Y');
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $month_year);
		$data['check_latch_timesheet'] = $this->timesheets_model->check_latch_timesheet(date('m-Y'));

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['day_by_month_tk'] = [];
		$data['day_by_month_tk'][] = _l('staff_id');
		$data['day_by_month_tk'][] = _l('staff');
		$data['set_col_tk'] = [];
		$data['set_col_tk'][] = ['data' => _l('staff_id'), 'type' => 'text'];
		$data['set_col_tk'][] = ['data' => _l('staff'), 'type' => 'text', 'readOnly' => true, 'width' => 200];

		for ($d = 1; $d <= $days_in_month; $d++) {
			$time = mktime(12, 0, 0, $month, $d, $month_year);
			if (date('m', $time) == $month) {
				array_push($data['day_by_month_tk'], date('D d', $time));
				array_push($data['set_col_tk'], ['data' => date('D d', $time), 'type' => 'text']);
			}
		}
		$data['day_by_month_tk'] = json_encode($data['day_by_month_tk']);
		$data_map = [];
		$data_timekeeping_form = get_timesheets_option('timekeeping_form');
		$data_timekeeping_manually_role = get_timesheets_option('timekeeping_manually_role');
		$data['data_timekeeping_form'] = $data_timekeeping_form;
		$data['staff_row_tk'] = [];
		$staffs = $this->timesheets_model->get_staff_timekeeping_applicable_object();
		$data['staffs'] = $staffs;
		$data['cell_background'] = [];
		if ($data_timekeeping_form == 'timekeeping_task' && $data['check_latch_timesheet'] == false) {
			$result = $this->timesheets_model->get_attendance_task($staffs, $month, $month_year);
			$data['staff_row_tk'] = $result['staff_row_tk'];
			$data['cell_background'] = $result['cell_background'];
		} else {
			if ($data['check_latch_timesheet'] == false) {
				$result = $this->timesheets_model->get_attendance_manual($staffs, $month, $month_year);
				$data['staff_row_tk'] = $result['staff_row_tk'];
				$data['cell_background'] = $result['cell_background'];
			}
		}
		$data_lack = [];
		$data['data_lack'] = $data_lack;
		$data['set_col_tk'] = json_encode($data['set_col_tk']);

		$data['notes'] = $this->timesheets_model->get_notes(date('m-Y'));
		$this->load->view('timekeeping/manage_timekeeping', $data);
	}
	/**
	 * add or update day off
	 */
	public function day_off()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$add = $this->timesheets_model->add_day_off($data);
				if ($add > 0) {
					$message = _l('added_successfully', _l('day_off'));
					set_alert('success', $message);
				}
				redirect(admin_url('timesheets/setting?group=manage_dayoff'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->timesheets_model->update_day_off($data, $id);
				if ($success == true) {
					$message = _l('ts_updated_successfully', _l('day_off'));
					set_alert('success', $message);
				}
				redirect(admin_url('timesheets/setting?group=manage_dayoff'));
			}
		}
	}
	/**
	 * delete day off
	 * @param  int $id
	 */
	public function delete_day_off($id)
	{
		if (!$id) {
			redirect(admin_url('timesheets/setting?group=manage_dayoff'));
		}
		$response = $this->timesheets_model->delete_day_off($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced') . ' ' . _l('day_off'));
		} elseif ($response == true) {
			set_alert('success', _l('deleted') . ' ' . _l('day_off'));
		} else {
			set_alert('warning', _l('problem_deleting') . ' ' . _l('day_off'));
		}
		redirect(admin_url('timesheets/setting?group=manage_dayoff'));
	}
	/**
	 * add or edit shifts
	 */
	public function shifts()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['id'] == '') {
				$add = $this->timesheets_model->add_work_shift($data);
				if ($add > 0) {
					$message = _l('added_successfully', _l('shift'));
					set_alert('success', $message);
				}
				redirect(admin_url('timesheets/shift_management'));
			} else {
				$success = $this->timesheets_model->update_work_shift($data);
				if ($success == true) {
					$message = _l('ts_updated_successfully', _l('shift'));
					set_alert('success', $message);
				}
				redirect(admin_url('timesheets/shift_management'));
			}
		}
	}
	/**
	 * get_data_edit_shift
	 * @param int $id
	 */
	public function get_data_edit_shift($id)
	{
		$shift_handson = $this->timesheets_model->get_data_edit_shift($id);
		$result = [];
		$node = [];
		foreach ($shift_handson as $key => $value) {
			foreach ($value as $col => $val) {
				if ($col == 'detail') {
					if ($key == 0) {
						$node[_l($col)] = _l('time_start_work');
					} elseif ($key == 1) {
						$node[_l($col)] = _l('time_end_work');
					} elseif ($key == 2) {
						$node[_l($col)] = _l('start_lunch_break_time');
					} elseif ($key == 3) {
						$node[_l($col)] = _l('end_lunch_break_time');
					} elseif ($key == 4) {
						$node[_l($col)] = _l('late_latency_allowed');
					}
				} else {

					$node[_l($col)] = $val;
				}
			}
			$result[] = $node;
		}
		echo json_encode([
			'handson' => $result,
		]);
	}
	/**
	 * delete shift
	 * @param int $id
	 */
	public function delete_shift($id)
	{
		if (!$id) {
			redirect(admin_url('timesheets/shift_management'));
		}
		$response = $this->timesheets_model->delete_shift($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced') . ' ' . _l('shift'));
		} elseif ($response == true) {
			set_alert('success', _l('deleted') . ' ' . _l('shift'));
		} else {
			set_alert('warning', _l('problem_deleting') . ' ' . _l('shift'));
		}
		redirect(admin_url('timesheets/shift_management'));
	}
	/**
	 * manage timesheets
	 */
	public function manage_timesheets()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if (isset($data)) {
				if ($data['latch'] == 1) {
					if (isset($data['month']) && $data['month'] != "") {
						$data_month = explode("-", $data['month']);
						if (strlen($data['month'][0]) == 4) {
							$month_latch = $data_month[1] . '-' . $data_month[0];
						} else {
							$month_latch = $data_month[0] . '-' . $data_month[1];
						}
					} else {
						$month_latch = date("m-Y");
					}

					$day_month = [];
					$day_by_month_tk = [];
					$day_month_tk[] = 'staff_id';
					$day_month_tk[] = 'staff_name';

					$month = explode('-', $data['month'])[0];
					$month_year = explode('-', $data['month'])[1];

					for ($d = 1; $d <= 31; $d++) {
						$time = mktime(12, 0, 0, $month, $d, $month_year);
						if (date('m', $time) == $month) {
							array_push($day_month, date('Y-m-d', $time));
							array_push($day_month_tk, date('Y-m-d', $time));
						}
					}
					$data['time_sheet'] = json_decode($data['time_sheet']);
					$ts_val = [];
					foreach ($data['time_sheet'] as $key => $value) {
						$ts_val[] = array_combine($day_month_tk, $value);
					}

					unset($data['time_sheet']);

					$add = $this->timesheets_model->add_update_timesheet($ts_val, true);

					$success = $this->timesheets_model->latch_timesheet($month_latch);

					if ($success) {
						set_alert('success', _l('timekeeping_latch_successful'));
					} else {
						set_alert('warning', _l('timekeeping_latch_false'));
					}
					redirect(admin_url('timesheets/timekeeping?group=timesheets'));
				} elseif ($data['unlatch'] == 1) {
					if (isset($data['month']) && $data['month'] != "") {
						$data['month'] = explode("-", $data['month']);
						if (strlen($data['month'][0]) == 4) {
							$month = $data['month'][1] . '-' . $data['month'][0];
						} else {
							$month = $data['month'][0] . '-' . $data['month'][1];
						}
					} else {
						$month = date("m-Y");
					}
					$success = $this->timesheets_model->unlatch_timesheet($month);

					if ($success) {
						set_alert('success', _l('timekeeping_unlatch_successful'));
					} else {
						set_alert('warning', _l('timekeeping_unlatch_false'));
					}
					redirect(admin_url('timesheets/timekeeping?group=timesheets'));
				} else {
					$day_month = [];
					$day_by_month_tk = [];
					$day_month_tk[] = 'staff_id';
					$day_month_tk[] = 'staff_name';

					$month = explode('-', $data['month'])[0];
					$month_year = explode('-', $data['month'])[1];

					for ($d = 1; $d <= 31; $d++) {
						$time = mktime(12, 0, 0, $month, $d, $month_year);
						if (date('m', $time) == $month) {
							array_push($day_month, date('Y-m-d', $time));
							array_push($day_month_tk, date('Y-m-d', $time));
						}
					}
					$data['time_sheet'] = json_decode($data['time_sheet']);
					$ts_val = [];
					foreach ($data['time_sheet'] as $key => $value) {
						$ts_val[] = array_combine($day_month_tk, $value);
					}
					$this->timesheets_model->add_update_note($data);
					$add = $this->timesheets_model->add_update_timesheet($ts_val, true);
					if ($add > 0) {
						set_alert('success', _l('timekeeping') . ' ' . _l('successfully'));
					} else {
						set_alert('warning', _l('alert_ts'));
					}
					redirect(admin_url('timesheets/timekeeping?group=timesheets'));
				}
			}
		}
	}

	/**
	 * approval status
	 * @return json
	 */
	public function approval_status()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$data = $this->input->post();

				$success = $this->timesheets_model->update_approval_status($data);
				if ($success) {
					$message = _l('success');
					echo json_encode([
						'success' => true,
						'message' => $message,
					]);
				} else {
					$message = _l('payslip_latch_false');
					echo json_encode([
						'success' => false,
						'message' => $message,
					]);
				}
			}
		}
	}
	/**
	 * reload timesheets byfilter
	 * @return json
	 */
	public function reload_timesheets_byfilter()
	{
		$data = $this->input->post();
		$date_ts = $this->timesheets_model->format_date($data['month'] . '-01');
		$date_ts_end = $this->timesheets_model->format_date($data['month'] . '-' . date('t'));
		$year = date('Y', strtotime($date_ts));
		$g_month = date('m', strtotime($date_ts));
		$month_filter = date('Y-m', strtotime($date_ts));

		$querystring = 'active=1';
		$department = $data['department'];
		$job_position = $data['job_position'];

		$data['month'] = date('m-Y', strtotime($date_ts));
		$data['check_latch_timesheet'] = $this->timesheets_model->check_latch_timesheet($data['month']);
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}

		$staff_querystring = '';
		$job_position_querystring = '';
		$department_querystring = '';
		$month_year_querystring = '';

		if ($department != '') {
			$arrdepartment = $this->staff_model->get('', 'staffid in (select ' . db_prefix() . 'staff_departments.staffid from ' . db_prefix() . 'staff_departments where departmentid = ' . $department . ')');
			$temp = '';
			foreach ($arrdepartment as $value) {
				$temp = $temp . $value['staffid'] . ',';
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}
		if ($job_position != '') {
			$job_position_querystring = 'role = "' . $job_position . '"';
		}
		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		} else {
			$data_timekeeping_form = get_timesheets_option('timekeeping_form');

			$timekeeping_applicable_object = [];
			if ($data_timekeeping_form == 'timekeeping_task') {
				if (get_timesheets_option('timekeeping_task_role') != '') {
					$timekeeping_applicable_object = get_timesheets_option('timekeeping_task_role');
				}
			} elseif ($data_timekeeping_form == 'timekeeping_manually') {
				if (get_timesheets_option('timekeeping_manually_role') != '') {
					$timekeeping_applicable_object = get_timesheets_option('timekeeping_manually_role');
				}
			} elseif ($data_timekeeping_form == 'csv_clsx') {
				if (get_timesheets_option('csv_clsx_role') != '') {
					$timekeeping_applicable_object = get_timesheets_option('csv_clsx_role');
				}
			}
			$staff_querystring != '';
			if ($data['job_position'] != '') {
				$staff_querystring .= 'role = ' . $data['job_position'];
			} else {
				if ($timekeeping_applicable_object) {
					if ($timekeeping_applicable_object != '') {
						$staff_querystring .= 'FIND_IN_SET(role, "' . $timekeeping_applicable_object . '")';
					}
				}
			}
		}

		$arrQuery = array($staff_querystring, $department_querystring, $month_year_querystring, $job_position_querystring, $querystring);
		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $g_month, $year);
		if ($year != '') {
			$month_new = (string) $g_month;
			if (strlen($month_new) == 1) {
				$month_new = '0' . $month_new;
			}
			$g_month = $month_new;
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs_li'] = $this->staff_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['job_position'] = $this->roles_model->get();
		$data['positions'] = $this->roles_model->get();

		$data['shifts'] = $this->timesheets_model->get_shifts();

		$data['day_by_month_tk'] = [];
		$data['day_by_month_tk'][] = _l('staff_id');
		$data['day_by_month_tk'][] = _l('staff');

		$data['set_col_tk'] = [];
		$data['set_col_tk'][] = ['data' => _l('staff_id'), 'type' => 'text'];
		$data['set_col_tk'][] = ['data' => _l('staff'), 'type' => 'text', 'readOnly' => true, 'width' => 200];

		for ($d = 1; $d <= $days_in_month; $d++) {
			$time = mktime(12, 0, 0, $g_month, $d, (int) $year);
			if (date('m', $time) == $g_month) {
				array_push($data['day_by_month_tk'], date('D d', $time));
				array_push($data['set_col_tk'], ['data' => date('D d', $time), 'type' => 'text']);
			}
		}

		$data['day_by_month_tk'] = $data['day_by_month_tk'];

		$data_map = [];
		$data_timekeeping_form = get_timesheets_option('timekeeping_form');
		$data['staff_row_tk'] = [];

		$staffs = $this->timesheets_model->getStaff('', $newquerystring);
		$data['staffs_setting'] = $this->staff_model->get();
		$data['staffs'] = $staffs;
		if ($data_timekeeping_form == 'timekeeping_task' && $data['check_latch_timesheet'] == false) {
			$result = $this->timesheets_model->get_attendance_task($staffs, $g_month, $year);
			$data['staff_row_tk'] = $result['staff_row_tk'];
			$data['cell_background'] = $result['cell_background'];
		} else {
			if ($data['check_latch_timesheet'] == false) {
				$result = $this->timesheets_model->get_attendance_manual($staffs, $g_month, $year);
				$data['staff_row_tk'] = $result['staff_row_tk'];
				$data['cell_background'] = $result['cell_background'];
			}
		}
		$monthandyear = $g_month . '-' . $year;
		$data['notes'] = $this->timesheets_model->get_notes($monthandyear);
		$data_lack = [];
		$data['data_lack'] = $data_lack;

		echo json_encode([
			'arr' => $data['staff_row_tk'],
			'set_col_tk' => $data['set_col_tk'],
			'day_by_month_tk' => $data['day_by_month_tk'],
			'check_latch_timesheet' => $data['check_latch_timesheet'],
			'month' => $data['month'],
			'data_lack' => $data['data_lack'],
			'cell_background' => $data['cell_background'],
			'notes' => $data['notes']['note'],
		]);
		die;
	}

	/**
	 * add requisition ajax
	 */
	public function add_requisition_ajax()
	{
		if ($_FILES['file']['name'] != '') {
			$_FILES = $_FILES;
		} else {
			unset($_FILES);
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			$redirect_calendar = 0;
			if (isset($data['redirect_calendar'])) {
				unset($data['redirect_calendar']);
				$redirect_calendar = 1;
			}
			unset($data['number_day_off']);
			if ($data['rel_type'] == 1) {
				$data['start_time'] = $this->timesheets_model->format_date_time($data['start_time']);
				$data['end_time'] = $this->timesheets_model->format_date_time($data['end_time']);
			} else {
				$data['start_time'] = $this->timesheets_model->format_date_time($data['start_time_s']);
				$data['end_time'] = $this->timesheets_model->format_date_time($data['end_time_s']);
			}

			unset($data['start_time_s']);
			unset($data['end_time_s']);
			if (!isset($data['staff_id'])) {
				$data['staff_id'] = get_staff_user_id();
			}
			if (isset($data['according_to_the_plan'])) {
				$data['according_to_the_plan'] = 0;
			}

			$rel_type = '';
			if ($data['rel_type'] == '1') {
				switch ($data['type_of_leave']) {
					case 8:
						$rel_type = 'leave';
						break;
					case 2:
						$rel_type = 'maternity_leave';
						break;
					case 4:
						$rel_type = 'private_work_without_pay';
						break;
					case 1:
						$rel_type = 'sick_leave';
						break;
				}
				// Rel type is custom type
				if ($rel_type == '') {
					$rel_type = $data['type_of_leave'];
				}
			} elseif ($data['rel_type'] == '2') {
				$data['end_time'] = $data['start_time'];
				$rel_type = 'late';
			} elseif ($data['rel_type'] == '3') {
				$data['end_time'] = $data['start_time'];
				$rel_type = 'Go_out';
			} elseif ($data['rel_type'] == '4') {
				$rel_type = 'Go_on_bussiness';
			} elseif ($data['rel_type'] == '5') {
				$rel_type = 'quit_job';
			} elseif ($data['rel_type'] == '6') {
				$data['end_time'] = $data['start_time'];
				$rel_type = 'early';
			}
			$data['type_of_leave_text'] = $rel_type;
			$result = $this->timesheets_model->add_requisition_ajax($data);
			if ($result != '') {
				$data_app['rel_id'] = $result;
				$data_app['rel_type'] = $rel_type;
				$data_app['addedfrom'] = $data['staff_id'];
				$data['rel_id'] = $result;
				$data['rel_type'] = $rel_type;
				$data['addedfrom'] = $data['staff_id'];
				$data['has_send_mail'] = 0;
				$check_proccess = $this->timesheets_model->get_approve_setting($rel_type, false, $data['staff_id']);
				$check = '';
				if ($check_proccess) {
					if ($check_proccess->choose_when_approving == 0) {
						$this->timesheets_model->send_request_approve($data_app, $data['staff_id']);
						$data['has_send_mail'] = 1;
						$check = 'not_choose';
					} else {
						$check = 'choose';
					}
				} else {
					$check = 'no_proccess';
				}
				$this->session->set_userdata(['send_mail_approve' => $data]);

				$followers_id = $data['followers_id'];
				$staffid = $data['staff_id'];
				$subject = $data['subject'];
				$link = 'timesheets/requisition_detail/' . $result;

				if ($followers_id != '') {
					if ($staffid != $followers_id) {
						$notification_data = [
							'description' => _l('you_are_added_to_follow_the_leave_application') . '-' . $subject,
							'touserid' => $followers_id,
							'link' => $link,
						];

						$notification_data['additional_data'] = serialize([
							$subject,
						]);

						if (add_notification($notification_data)) {
							pusher_trigger_notification([$followers_id]);
						}
					}
				}

				// Send to receipient
				$this->timesheets_model->notify_create_new_leave($result, $rel_type);

				if ($redirect_calendar == 1) {
					redirect(admin_url('timesheets/calendar_leave_application'));
				} else {
					redirect(admin_url('timesheets/requisition_detail/' . $result . '?check=' . $check));
				}
			} else {
				redirect(admin_url('timesheets/requisition_manage'));
			}
		}
	}

	/**
	 * table registration leave
	 * @return
	 */
	public function table_registration_leave()
	{
		$this->app->get_table_data(module_views_path('timesheets', 'table_registration_leave'));
	}

	/**
	 * table additional timesheets
	 * @return
	 */
	public function table_additional_timesheets()
	{
		$this->app->get_table_data(module_views_path('timesheets', 'timekeeping/table_additional_timesheets'));
	}

	/**
	 * get request leave data ajax
	 * @return
	 */
	public function get_request_leave_data_ajax()
	{
		$data[] = $this->timesheets_model->get_category_for_leave();
	}

	/**
	 * requisition detail
	 * @param  int $id
	 * @return view
	 */
	public function requisition_detail($id)
	{
		if (!(has_permission('leave_management', '', 'view_own') || has_permission('leave_management', '', 'view') || is_admin())) {
			access_denied('approval_process');
		}
		$send_mail_approve = $this->session->userdata("send_mail_approve");
		$data['has_send_mail'] = 0;
		if ((isset($send_mail_approve)) && $send_mail_approve != '') {
			$data['send_mail_approve'] = $send_mail_approve;
			$data['has_send_mail'] = (isset($send_mail_approve['has_send_mail']) ? $send_mail_approve['has_send_mail'] : 0);
			$this->session->unset_userdata("send_mail_approve");
		}


		$data['request_leave'] = $this->timesheets_model->get_request_leave($id);
		$status_leave = $this->timesheets_model->get_number_of_days_off($data['request_leave']->staff_id);
		$day_off = $this->timesheets_model->get_day_off($data['request_leave']->staff_id);
		$data['number_day_off'] = 0;
		if ($day_off != null) {
			$data['number_day_off'] = $day_off->remain;
		}

		$leave_isset = $this->db->query('select * from ' . db_prefix() . 'timesheets_requisition_leave')->result_array();
		$data['id'] = $id;
		$data['leave_isset'] = $leave_isset;

		$rel_type = '';
		if ($data['request_leave']->rel_type == 1) {
			switch ($data['request_leave']->type_of_leave) {
				case 8:
					$rel_type = 'Leave';
					break;
				case 2:
					$rel_type = 'maternity_leave';
					break;
				case 4:
					$rel_type = 'private_work_without_pay';
					break;
				case 1:
					$rel_type = 'sick_leave';
					break;
			}
			if ($rel_type == '') {
				$rel_type = $data['request_leave']->type_of_leave_text;
			}
		} elseif ($data['request_leave']->rel_type == 2) {
			$rel_type = 'late';
		} elseif ($data['request_leave']->rel_type == 3) {
			$rel_type = 'Go_out';
		} elseif ($data['request_leave']->rel_type == 4) {
			$rel_type = 'Go_on_bussiness';
		} elseif ($data['request_leave']->rel_type == 5) {
			$rel_type = 'quit_job';
		} elseif ($data['request_leave']->rel_type == 6) {
			$rel_type = 'early';
		}
		$this->load->model('staff_model');

		if ($data['request_leave']->rel_type == '4') {
			$data['advance_payment'] = $this->timesheets_model->get_go_bussiness_advance_payment($id);
		}

		$id_file = $this->db->query('select id from ' . db_prefix() . 'files where rel_id =' . $id)->row();
		$data['id_file'] = $id_file;
		$data['rel_type'] = $rel_type;
		$data['list_staff'] = $this->staff_model->get();
		$data['check_approve_status'] = $this->timesheets_model->check_approval_details($id, $rel_type);
		$data['list_approve_status'] = $this->timesheets_model->get_list_approval_details($id, $rel_type);
		$this->load->view('timesheets/requisition_detail', $data);
	}

	/**
	 * delete requisition
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_requisition($id)
	{
		$response = $this->timesheets_model->delete_requisition($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('lead_source_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('lead_source')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('lead_source_lowercase')));
		}
		redirect(admin_url('timesheets/requisition_manage'));
	}

	/**
	 * infor staff
	 * @param  int $id
	 * @return data
	 */
	public function infor_staff($id)
	{
		$this->db->select('s.email,r.name as name_role, d.name');
		$this->db->from('staff as s');
		$this->db->join('staff_departments as sd', 's.staffid = sd.staffid');
		$this->db->join('departments as d', 'sd.departmentid = d.departmentid');
		$this->db->join('roles as r', 's.role = r.roleid');
		$this->db->where('s.staffid', $id);
		$data = $this->db->get()->row();
		json_encode($data);
		return $data;
	}

	/**
	 * approve request leave
	 * @param  int $status
	 * @param  int $id
	 * @return redirect
	 */
	public function approve_request_leave($status, $id)
	{
		$result = $this->timesheets_model->approve_request_leave($status, $id);
		if ($result == 'approved') {
			set_alert('success', _l('approved') . ' ' . _l('request_leave') . ' ' . _l('successfully'));
		} elseif ($result == 'reject') {
			set_alert('success', _l('reject') . ' ' . _l('request_leave') . ' ' . _l('successfully'));
		} else {
			set_alert('warning', _l('action') . ' ' . _l('fail'));
		}
		redirect(admin_url('timesheets/requisition_detail/' . $id));
	}

	/**
	 * file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return view
	 */
	public function file($id, $rel_id)
	{
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->timesheets_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('_file', $data);
	}

	/**
	 * requisition manage
	 * @return view
	 */
	public function requisition_manage()
	{
		if (!(has_permission('leave_management', '', 'view_own') || has_permission('leave_management', '', 'view') || is_admin())) {
			access_denied('approval_process');
		}
		$send_mail_approve = $this->session->userdata("send_mail_approve");
		if ((isset($send_mail_approve)) && $send_mail_approve != '') {
			$data['send_mail_approve'] = $send_mail_approve;
			$this->session->unset_userdata("send_mail_approve");
		}
		$data['userid'] = get_staff_user_id();
		$status_leave = $this->timesheets_model->get_number_of_days_off();
		$day_off = $this->timesheets_model->get_current_date_off($data['userid']);
		$data['days_off'] = $day_off->days_off;
		$data['number_day_off'] = $day_off->number_day_off;

		$data['data_timekeeping_form'] = get_timesheets_option('timekeeping_form');
		$this->load->model('departments_model');
		$data['departments'] = $this->departments_model->get();
		$data['current_date'] = date('Y-m-d H:i:s');
		$status_leave = $this->timesheets_model->get_option_val();
		$this->load->model('staff_model');
		$data['pro'] = $this->staff_model->get('', 'active = 1');
		$data['tab'] = $this->input->get('tab');
		$data['title'] = _l('leave');
		$data['additional_timesheets_id'] = $this->input->get('additional_timesheets_id');
		$data['additional_timesheets'] = $this->timesheets_model->get_additional_timesheets();
		$data['type_of_leave'] = $this->timesheets_model->get_type_of_leave();
		$data['staffs'] = $this->staff_model->get();

		$this->load->view('timesheets/timekeeping/manage_requisition_hrm', $data);
	}

	/**
	 * automatic timekeeping
	 * @param  $data
	 * @return json
	 */
	public function automatic_timekeeping($data)
	{

		$success = $this->timesheets_model->automatic_timekeeping($data);

		if ($success) {
			set_alert('success', _l('successfully'));
		} else {
			set_alert('warning', _l('fail'));
		}

		echo json_encode([
			'success' => $success,
		]);
		die();
	}

	/**
	 * setting timekeeper
	 * @return redirect
	 */
	public function setting_timekeeper()
	{
		$data = $this->input->post();
		$success = $this->timesheets_model->setting_timekeeper($data);
		if ($success) {
			set_alert('success', _l('save_setting_success'));
		} else {
			set_alert('danger', _l('no_data_changes'));
		}
		redirect(admin_url('timesheets/setting?group=timekeeping_settings'));
	}

	/**
	 * edit timesheets
	 * @return redirect
	 */
	public function edit_timesheets()
	{
		$data = $this->input->post();
		$success = $this->timesheets_model->edit_timesheets($data);
		if ($success) {
			set_alert('success', _l('save_setting_success'));
		} else {
			set_alert('danger', _l('save_setting_fail'));
		}
		redirect(admin_url('timesheets/timekeeping?group=timesheets'));
	}

	/**
	 * send additional timesheets
	 * @return redirect
	 */
	public function send_additional_timesheets()
	{
		$data = $this->input->post();
		$success = false;
		if (isset($data['additional_day'])) {
			$check_latch_timesheet = $this->timesheets_model->check_latch_timesheet(date('m-Y', strtotime(to_sql_date($data['additional_day']))));
			if ($check_latch_timesheet) {
				set_alert('danger', _l('timekeeping_latched'));
				redirect(admin_url('timesheets/member/' . get_staff_user_id() . '?tab=timekeeping'));
			}
			$staff_id = '';
			if (is_admin() || has_permission('additional_timesheets_specific_employees', '', 'create')) {
				if (isset($data['staff_id'])) {
					$staff_id = $data['staff_id'];
				}
			}

			$success = $this->timesheets_model->add_additional_timesheets($data, '', $staff_id);
		}
		if ($success) {
			set_alert('success', _l('added_successfully', _l('additional_timesheets')));
		} else {
			set_alert('warning', _l('fail'));
		}
		redirect(admin_url('timesheets/requisition_manage?tab=additional_timesheets&additional_timesheets_id=' . $success));
	}

	/**
	 * approve additional timesheets
	 * @param  int $id
	 * @return json
	 */
	public function approve_additional_timesheets($id)
	{
		$data = $this->input->post();

		$message = _l('rejected_successfully');

		$success = $this->timesheets_model->update_additional_timesheets($data, $id);

		if ($success) {
			$this->db->where('id', $id);
			$additional_timesheet = $this->db->get(db_prefix() . 'timesheets_additional_timesheet')->row();

			$this->timesheets_model->edit_timesheets($additional_timesheet);

			$ci = &get_instance();
			$staff_id = get_staff_user_id();
			$additional_data = '';
			if ($data['status'] == 1) {
				$mes_creator = 'notify_send_creator_additional_timesheet_approved';
			} else {
				$mes_creator = 'notify_send_creator_additional_timesheet_rejected';
			}

			$title_creator = 'approval';

			$link = 'timesheets/requisition_manage?tab=additional_timesheets?additional_timesheets_id=' . $id;

			if (isset($additional_timesheet->creator) && $additional_timesheet->creator != $staff_id) {
				$notified = add_notification([
					'description' => $mes_creator,
					'touserid' => $additional_timesheet->creator,
					'link' => $link,
					'additional_data' => serialize([
						$additional_data,
					]),
				]);
				if ($notified) {
					pusher_trigger_notification([$additional_timesheet->creator]);
				}

				$ci->email->initialize();
				$ci->load->library('email');
				$ci->email->clear(true);
				$ci->email->from(get_staff_email_by_id($staff_id), get_staff_full_name($staff_id));
				$ci->email->to(get_staff_email_by_id($additional_timesheet->creator));

				$ci->email->subject(_l($title_creator));
				$ci->email->message(_l($mes_creator) . ' <a target="_blank" class="u6" href="' . admin_url($link) . '">Link</a>');

				$ci->email->send(true);
			}

			if ($data['status'] == 1) {
				$message = _l('approved_successfully');
			} else {
				$message = _l('rejected_successfully');
			}
		}
		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
		die();
	}

	/**
	 * show detail timesheets
	 * @return json
	 */
	public function show_detail_timesheets()
	{
		$data = $this->input->post();
		$d = substr($data['ColHeader'], 4, 2);
		$time = $data['month'] . '-' . $d;
		$d = _d($time);
		$st = $this->staff_model->get($data['staffid']);
		if (!isset($st->staffid)) {
			echo json_encode([
				'title' => '',
				'html' => '',
			]);
			die();
		}
		$title = get_staff_full_name($st->staffid) . ' - ' . $d;

		$data['value'] = explode('; ', $data['value']);
		$html = '';
		foreach ($data['value'] as $key => $value) {
			$value = explode(':', $value);
			if (isset($value[1]) && $value[1] > 0 || $value[0] == 'M' || $value[0] == 'HO' || $value[0] == 'B') {
				switch ($value[0]) {
					case 'AL':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('p_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_p">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'W':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('W_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_w" data-toggle="tooltip" data-placement="top" data-original-title="' . $this->timesheets_model->calculate_working_hour($value[1]) . '">' . $value[1] . '</span>
				</li>';
						break;
					case 'A':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('A_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_a">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'HO':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('Le_timekeeping') . '
				</li>';
						break;
					case 'E':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('E_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_e">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'L':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('L_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_l">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'B':
						$tripid = '';
						$bstrip = $this->timesheets_model->get_bussiness_trip_info($time);
						if ($bstrip) {
							$tripid = $bstrip->id;
						}
						$html .= '<li class="list-group-item justify-content-between"><a href="' . admin_url('timesheets/requisition_detail/' . $tripid) . '">
				' . _l('CT_timekeeping') . '
				</a><span class="badgetext badge badge-primary badge-pill style_b"></span>
				</li>';
						break;
					case 'U':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('U_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'OM':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('OM_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_om">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'M':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('maternity_leave') . '
				</li>';
						break;
					case 'R':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('R_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'P':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('P_timekeepings') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'SI':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('CD_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'CO':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('CO_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'ME':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('H_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_me">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'OT':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('OT_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_me">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'PO':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('PN_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_po">' . round($value[1], 2) . '</span>
				</li>';
						break;
					default:
						$custome_name = $this->timesheets_model->get_custom_leave_name_by_symbol($value[0]);
						if ($custome_name) {
							$html .= '<li class="list-group-item justify-content-between">
					' . $custome_name . '
					<span class="badgetext badge badge-primary badge-pill style_po">' . round($value[1], 2) . '</span>
					</li>';
						}
						break;
				}
			}
		}

		if (!($value[0] == 'HO' || $value[0] == 'EB' || $value[0] == 'UB')) {
			$ws_day = '';
			$color = '';
			$list_shift = $this->timesheets_model->get_shift_work_staff_by_date($data['staffid'], $time);
			foreach ($list_shift as $ss) {
				$data_shift_type = $this->timesheets_model->get_shift_type($ss);
				if ($data_shift_type) {
					$ws_day .= '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $data_shift_type->time_start_work . ' - ' . $data_shift_type->time_end_work . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $data_shift_type->start_lunch_break_time . ' - ' . $data_shift_type->end_lunch_break_time . '</li>';
				}
			}

			if ($ws_day != '') {
				$html .= $ws_day;
			}
			$access_history_string = '';
			$access_history = $this->timesheets_model->get_list_check_in_out($time, $data['staffid']);
			if ($access_history) {
				foreach ($access_history as $key => $value) {
					if ($value['type_check'] == '1') {
						$access_history_string .= '<li class="list-group-item"><i class="fa fa-sign-in text-success" aria-hidden="true"></i> ' . _dt($value['date']) . '</li>';
					} else {
						$access_history_string .= '<li class="list-group-item"><i class="fa fa-sign-out text-danger" aria-hidden="true"></i> ' . _dt($value['date']) . '</li>';
					}
				}
			}
			if ($access_history_string != '') {
				$html .= '<li class="list-group-item justify-content-between"><ul class="list-group">
			<li class="list-group-item active">' . _l('access_history') . '</li>
			' . $access_history_string . '
			</ul></li>';
			}
		}
		echo json_encode([
			'title' => $title,
			'html' => $html,
		]);
		die();
	}

	/**
	 * approval process
	 * @param  string $id
	 * @return redirect
	 */
	public function approval_process($id = '')
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$id = $data['approval_setting_id'];
			unset($data['approval_setting_id']);
			if ($id == '') {
				$id = $this->timesheets_model->add_approval_process($data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('approval_process')));
				}
			} else {
				$success = $this->timesheets_model->update_approval_process($id, $data);
				if ($success) {
					set_alert('success', _l('ts_updated_successfully', _l('approval_process')));
				}
			}
			redirect(admin_url('timesheets/setting?group=approval_process'));
		}
	}

	/**
	 * table approval process
	 * @return
	 */
	public function table_approval_process()
	{
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('timesheets', 'approval_process/table_approval_process'));
		}
	}

	/**
	 * get html approval setting
	 * @param  string $id
	 * @return
	 */
	public function get_html_approval_setting($id = '')
	{
		$html = '';
		$staffs = $this->staff_model->get();
		$approver = [
			0 => ['id' => 'direct_manager', 'name' => _l('direct_manager')],
			1 => ['id' => 'department_manager', 'name' => _l('department_manager')],
			2 => ['id' => 'staff', 'name' => _l('staff')]
		];
		if (is_numeric($id)) {
			$approval_setting = $this->accounting_model->get_approval_setting($id);

			$setting = json_decode($approval_setting->setting);

			foreach ($setting as $key => $value) {
				if ($key == 0) {
					$html .= '<div id="item_approve">
					<div class="col-md-11">
					<div class="col-md-4"> ' .
						render_select('approver[' . $key . ']', $approver, array('id', 'name'), 'task_single_related', $value->approver) . '
					</div>
					<div class="col-md-4">
					' . render_select('staff[' . $key . ']', $staffs, array('staffid', 'full_name'), 'staff', $value->staff) . '
					</div>
					<div class="col-md-4">
					' . render_select('action[' . $key . ']', $action, array('id', 'name'), 'action', $value->action) . '
					</div>
					</div>
					<div class="col-md-1 contents-nowrap">
					<span class="pull-bot">
					<button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
					</span>
					</div>
					</div>';
				} else {
					$direct_manager = '';
					$department_manager = '';
					$staff = '';
					if ($value->approver == 'direct_manager') {
						$direct_manager = 'selected';
					} elseif ($value->approver == 'department_manager') {
						$department_manager = 'selected';
					} elseif ($value->approver == 'staff') {
						$staff = 'selected';
					}
					$html .= '<div id="item_approve">
					<div class="col-md-11">
					<div class="col-md-4">
					' .
						render_select('approver[' . $key . ']', $approver, array('id', 'name'), 'task_single_related', $value->approver) . '
					</div>

					<div class="col-md-6">
					<div class="select-placeholder form-group">
					<label for="approver[' . $key . ']">' . _l('approver') . '</label>
					<select name="approver[' . $key . ']" id="approver[' . $key . ']" data-id="' . $key . '" class="selectpicker" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '" data-hide-disabled="true" required>
					<option value=""></option>
					<option value="direct_manager" ' . $direct_manager . '>' . _l('direct_manager') . '</option>
					<option value="department_manager" ' . $department_manager . '>' . _l('department_manager') . '</option>
					<option value="staff" ' . $staff . '>' . _l('staff') . '</option>
					</select>
					</div>
					</div>
					<div class="col-md-6 hide" id="is_staff_' . $key . '">
					<div class="select-placeholder form-group">
					<label for="staff[' . $key . ']">' . _l('staff') . '</label>
					<select name="staff[' . $key . ']" id="staff[' . $key . ']" class="selectpicker" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '" data-hide-disabled="true" data-live-search="true">
					<option value=""></option>';
					foreach ($staffs as $val) {
						if ($value->staff == $val) {
							$html .= '<option value="' . $val['staffid'] . '" selected>
							' . get_staff_full_name($val['staffid']) . '
							</option>';
						} else {
							$html .= '<option value="' . $val['staffid'] . '">
							' . get_staff_full_name($val['staffid']) . '
							</option>';
						}
					}
					$html .= '</select>
					</div>
					</div>
					</div>
					<div class="col-md-1 contents-nowrap">
					<span class="pull-bot">
					<button name="add" class="btn remove_vendor_requests btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
					</span>
					</div>
					</div>';
				}
			}
		} else {
			$html .= '<div id="item_approve">
			<div class="col-md-11">
			<div class="col-md-6">
			<div class="select-placeholder form-group">
			<label for="approver[0]">' . _l('approver') . '</label>
			<select name="approver[0]" id="approver[0]" data-id="0" class="selectpicker" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '" data-hide-disabled="true" required>
			<option value=""></option>
			<option value="direct_manager">' . _l('direct_manager') . '</option>
			<option value="department_manager">' . _l('department_manager') . '</option>
			<option value="staff">' . _l('staff') . '</option>
			</select>
			</div>
			</div>
			<div class="col-md-6 hide" id="is_staff_0">
			<div class="select-placeholder form-group">
			<label for="staff[0]">' . _l('staff') . '</label>
			<select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '" data-hide-disabled="true" data-live-search="true">
			<option value=""></option>';
			foreach ($staffs as $val) {
				$html .= '<option value="' . $val['staffid'] . '">
				' . get_staff_full_name($val['staffid']) . '
				</option>';
			}
			$html .= '</select>
			</div>
			</div>
			</div>
			<div class="col-md-1 contents-nowrap">
			<span class="pull-bot">
			<button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
			</span>
			</div>
			</div>';
		}

		echo json_encode([
			$html,
		]);
	}

	/**
	 * new approval setting
	 * @return view
	 */
	public function new_approval_setting()
	{

		$data['title'] = _l('add_approval_process');
		$this->load->model('roles_model');
		$data['staffs'] = $this->staff_model->get();
		$data['departments'] = $this->departments_model->get();
		$data['job_positions'] = $this->roles_model->get();
		$data['type_of_leave'] = $this->timesheets_model->get_type_of_leave();
		$this->load->view('approval_process/add_edit_approval_process', $data);
	}

	/**
	 * edit approval setting
	 * @param  string $id
	 * @return
	 */
	public function edit_approval_setting($id = '')
	{
		$data['approval_setting'] = $this->timesheets_model->get_approval_process($id);
		$data['title'] = _l('edit_approval_process');

		$data['departments'] = $this->departments_model->get();
		$data['job_positions'] = $this->roles_model->get();

		$data['staffs'] = $this->staff_model->get();
		$data['type_of_leave'] = $this->timesheets_model->get_type_of_leave();
		$this->load->view('approval_process/add_edit_approval_process', $data);
	}

	/**
	 * delete approval setting
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete_approval_setting($id)
	{
		if (!$id) {
			redirect(admin_url('timesheets/approval_process'));
		}
		$response = $this->timesheets_model->delete_approval_setting($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('approval_process')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('approval_process')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('approval_process')));
		}
		redirect(admin_url('timesheets/setting?group=approval_process'));
	}

	/**
	 * send request approve
	 * @return json
	 */
	public function send_request_approve()
	{
		$data = $this->input->post();
		$message = 'Send request approval fail';
		$check = $this->timesheets_model->check_choose_when_approving($data['rel_type']);
		if ($check == 0) {
			$success = $this->timesheets_model->send_request_approve($data);
			if ($success === true) {
				$message = _l('send_request_approval_success');
				$data_new = [];
				$data_new['send_mail_approve'] = $data;
				$this->session->set_userdata($data_new);
			} elseif ($success === false) {
				$message = _l('no_matching_process_found');
				$success = false;
			} else {
				$message = _l('could_not_find_approver_with', _l($success));
				$success = false;
			}
			echo json_encode([
				'type' => 'choose',
				'success' => $success,
				'message' => $message,
			]);
			die;
		} else {
			$this->load->model('staff_model');
			$list_staff = $this->staff_model->get();

			$html = '<div class="col-md-12">';
			$html .= '<div class="col-md-9"><select name="approver_c" class="selectpicker" data-live-search="true" id="approver_c" data-width="100%" data-none-selected-text="' . _l('please_choose_approver') . '" required>
		<option value=""></option>';
			foreach ($list_staff as $staff) {
				$html .= '<option value="' . $staff['staffid'] . '">' . $staff['firstname'] . ' ' . $staff['lastname'] . '</option>';
			}
			$html .= '</select></div>';
			if ($data['rel_type'] == 'additional_timesheets') {
				$html .= '<div class="col-md-3"><a href="#" onclick="choose_approver(' . $data['rel_id'] . ',' . $data['addedfrom'] . ');" class="btn btn-success lead-top-btn lead-view" data-loading-text="' . _l('wait_text') . '">' . _l('choose') . '</a></div>';
			} else {
				$html .= '<div class="col-md-3"><a href="#" onclick="choose_approver();" class="btn btn-success lead-top-btn lead-view" data-loading-text="' . _l('wait_text') . '">' . _l('choose') . '</a></div>';
			}
			$html .= '</div>';

			echo json_encode([
				'type' => 'not_choose',
				'html' => $html,
				'message' => _l('please_choose_approver'),
			]);
		}
	}

	/**
	 * send request approve requisition
	 * @param  data
	 * @return
	 */
	public function send_request_approve_requisition($data)
	{

		$message = 'Send request approval fail';

		$success = $this->timesheets_model->send_request_approve($data);

		if ($success === true) {
			$message = _l('send_request_approval_success');
			$data_new = [];
			$data_new['send_mail_approve'] = $data;
			$this->session->set_userdata($data_new);
		} elseif ($success === false) {
			$message = _l('no_matching_process_found');
			$success = false;
		} else {
			$message = _l('could_not_find_approver_with', _l($success));
			$success = false;
		}
	}

	/**
	 * approve request
	 * @return json
	 */
	public function approve_request()
	{
		$data = $this->input->post();
		$data['staff_approve'] = get_staff_user_id();
		$success = false;
		$message = '';
		$addedfrom = get_staff_user_id();
		$this->db->where('id', $data['rel_id']);
		$requisition = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
		if ($requisition) {
			$addedfrom = $requisition->staff_id;
		}
		$check_approve_status = $this->timesheets_model->check_approval_details($data['rel_id'], $data['rel_type']);
		if (isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])) {
			$success = $this->timesheets_model->update_approval_details($check_approve_status['id'], $data);
			$message = _l('approved_successfully');
			if ($success) {
				if ($data['approve'] == 1) {
					$message = _l('approved_successfully');
					$data_log = [];
					$data_log['note'] = "approve_request";
					$check_approve_status = $this->timesheets_model->check_approval_details($data['rel_id'], $data['rel_type']);
					if ($check_approve_status === true) {
						$this->timesheets_model->update_approve_request($data['rel_id'], $data['rel_type'], 1);
						if ($data['rel_type'] == 'quit_job') {
							$this->load->model('staff_model');
							if ($requisition) {
								$data_quitting_work = [];
								$staff = $this->staff_model->get($requisition->staff_id);
								if ($staff) {
									$department = $this->departments_model->get_staff_departments($requisition->staff_id);
									$role_name = $this->roles_model->get($requisition->staff_id);
									$data_quitting_work['staffs'] = array('0' => $requisition->staff_id);
									$data_quitting_work['email'] = $staff->email;
									$data_quitting_work['department'] = '';
									$data_quitting_work['role'] = '';
									if (count($department) > 0) {
										$data_quitting_work['department'] = $department[0]['name'];
									}
									if ($role_name) {
										$data_quitting_work['role'] = $role_name->name;
									}
									$this->timesheets_model->add_tbllist_staff_quitting_work($data_quitting_work);
								}
							}
						}
					}
				} else {
					$message = _l('rejected_successfully');
					$this->timesheets_model->update_approve_request($data['rel_id'], $data['rel_type'], 2);
				}
			}
		}

		$data_new = [];
		$data['addedfrom'] = $addedfrom;
		$data['has_send_mail'] = 1;
		$data_new['send_mail_approve'] = $data;
		$this->session->set_userdata($data_new);
		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
		die();
	}

	/**
	 * send mail
	 * @return json
	 */
	public function send_mail()
	{
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			if ((isset($data)) && $data != '') {
				$this->timesheets_model->send_mail($data, $data['addedfrom']);
				$success = 'success';
				echo json_encode([
					'success' => $success,
				]);
			}
		}
	}

	/**
	 * choose approver
	 * @return json
	 */
	public function choose_approver()
	{
		$data = $this->input->post();
		$message = 'Send request approval fail';

		$success = $this->timesheets_model->choose_approver($data);
		if ($success === true) {
			$message = 'Send request approval success';
			$data_new = [];
			$data['has_send_mail'] = 1;
			$data_new['send_mail_approve'] = $data;
			$this->session->set_userdata($data_new);
		} elseif ($success === false) {
			$message = _l('no_matching_process_found');
			$success = false;
		} else {
			$message = _l('could_not_find_approver_with', _l($success));
			$success = false;
		}
		echo json_encode([
			'type' => 'choose',
			'success' => $success,
			'message' => $message,
		]);
		die;
	}

	public function get_data_additional_timesheets($id)
	{
		$check_approve_status = $this->timesheets_model->check_approval_details($id, 'additional_timesheets');
		$list_approve_status = $this->timesheets_model->get_list_approval_details($id, 'additional_timesheets');

		$additional_timesheets = $this->timesheets_model->get_additional_timesheets($id);

		$html = '
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">
	<span>' . _l('additional_timesheets') . '</span>
	</h4>
	</div>
	<div class="modal-body">';

		$html .= '<div class="col-md-12">';
		if ($additional_timesheets) {
			$status_class = 'info';
			$status_text = 'status_0';
			if ($additional_timesheets->status == 1) {
				$status_class = 'success';
				$status_text = 'status_1';
			} elseif ($additional_timesheets->status == 2) {
				$status_class = 'danger';
				$status_text = 'status_-1';
			}

			$creator = '';
			if (isset($additional_timesheets->creator)) {
				$creator = '<a href="' . admin_url('staff/profile/' . $additional_timesheets->creator) . '">' . staff_profile_image($additional_timesheets->creator, [
					'staff-profile-image-small',
				]) . '</a> <a href="' . admin_url('staff/profile/' . $additional_timesheets->creator) . '">' . get_staff_full_name($additional_timesheets->creator) . '</a>';
			}
			$html .= '<table class="table border table-striped margin-top-0">
		<tbody>
		<tr class="project-overview">
		<td class="bold" width="30%">' . _l('creator') . '</td>
		<td><a href="' . admin_url('staff/profile/' . $additional_timesheets->creator) . '">' . staff_profile_image($additional_timesheets->creator, [
				'staff-profile-image-small',
			]) . '</a> <a href="' . admin_url('staff/profile/' . $additional_timesheets->creator) . '">' . get_staff_full_name($additional_timesheets->creator) . '</a>
		</td>
		</tr>
		<tr class="project-overview">
		<td class="bold" width="30%">' . _l('status') . '</td>
		<td><span class="label label-' . $status_class . ' mr-1 mb-1 mt-1">' . _l($status_text) . '</span></td>
		</tr>
		<tr class="project-overview">
		<td class="bold">' . _l('additional_day') . '</td>
		<td>' . _d($additional_timesheets->additional_day) . '</td>
		</tr>
		<tr class="project-overview">
		<td class="bold">' . _l('time_in') . '</td>
		<td>' . $additional_timesheets->time_in . '</td>
		</tr>
		<tr class="project-overview">
		<td class="bold">' . _l('time_out') . '</td>
		<td>' . $additional_timesheets->time_out . '</td>
		</tr>
		';

			$html .= '  <tr class="project-overview">
		<td class="bold" width="30%">' . _l('timekeeping_value') . '</td>
		<td>' . $additional_timesheets->timekeeping_value . '</td>
		</tr>
		<tr class="project-overview">
		<td class="bold" width="30%">' . _l('reason_') . '</td>
		<td>' . $additional_timesheets->reason . '</td>
		</tr>
		</tbody>
		</table>';
		}
		$html .= '
	<p class="bold margin-top-15">' . _l('approval_infor') . '</p>
	<hr class="border-0-5" /><div>

	<div class="project-overview-right">';
		if (count($list_approve_status) > 0) {

			$html .= '<div class="row">
		<div class="col-md-12 project-overview-expenses-finance">';

			$this->load->model('staff_model');
			$enter_charge_code = 0;
			foreach ($list_approve_status as $value) {
				$value['staffid'] = explode(', ', $value['staffid']);

				$html .= '<div class="col-md-6" class="font-15">
			<p class="text-uppercase text-muted no-mtop bold">';
				$staff_name = '';
				foreach ($value['staffid'] as $key => $val) {
					if ($staff_name != '') {
						$staff_name .= ' or ';
					}
					$get_staff = $this->staff_model->get($val);
					if ($get_staff) {
						$staff_name .= $get_staff->firstname . ' ' . $get_staff->lastname;
					}
				}
				$html .= $staff_name . '</p>';

				if ($value['approve'] == 1) {
					$html .= '<img src="' . site_url(TIMESHEETS_PATH . 'approval/approved.png') . '" class="wh-150-80">';
					$html .= '<br><br>
				<p class="bold text-center text-success">' . _dt($value['date']) . '</p>
				';
				} elseif ($value['approve'] == 2) {
					$html .= '<img src="' . site_url(TIMESHEETS_PATH . 'approval/rejected.png') . '" class="wh-150-80">';
					$html .= '<br><br>
				<p class="bold text-center text-danger">' . _dt($value['date']) . '</p>
				';
				}
				$html .= '</div>';
			}
			$html .= '</div></div>';
		}

		$html .= '</div>
	<div class="clearfix"></div></br>
	<div class="modal-footer">';

		$check_proccess = $this->timesheets_model->get_approve_setting('additional_timesheets', false);
		$check = '';
		if ($check_proccess) {
			if ($check_proccess->choose_when_approving == 0) {
				$check = 'not_choose';
			} else {
				$check = 'choose';
			}
		} else {
			$check = 'no_proccess';
		}

		if ($additional_timesheets->status == 0 && ($check_approve_status == false || $check_approve_status == 'reject')) {
			if ($check != 'choose') {
				$html .= '<a data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(' . $additional_timesheets->id . ',' . $additional_timesheets->creator . '); return false;">' . _l('send_request_approve') . '</a>';
			}

			if ($check == 'choose') {
				$this->load->model('staff_model');
				$list_staff = $this->staff_model->get();
				$html .= '<div class="row"><div class="row"><div class="col-md-7"><select name="approver_c" class="selectpicker" data-live-search="true" id="approver_c" data-width="100%" data-none-selected-text="' . _l('please_choose_approver') . '" required>';
				$current_user = get_staff_user_id();
				foreach ($list_staff as $staff) {
					if ($staff['staffid'] != $current_user || is_admin()) {
						$html .= '<option value="' . $staff['staffid'] . '">' . $staff['staff_identifi'] . ' - ' . $staff['firstname'] . ' ' . $staff['lastname'] . '</option>';
					}
				}
				$html .= '</select></div>';
				$html .= '<div class="col-md-5"><a href="#" class="btn btn-default pull-right mleft15" data-toggle="modal" data-target=".additional-timesheets-sidebar">' . _l('close') . '</a>';
				$html .= '<a href="#" onclick="choose_approver(' . $additional_timesheets->id . ',' . $additional_timesheets->creator . ');" class="btn btn-success lead-top-btn lead-view pull-right" data-loading-text="' . _l('wait_text') . '">' . _l('choose') . '</a></div></div></div>';
			}
		}
		if (isset($check_approve_status['staffid'])) {
			if (in_array(get_staff_user_id(), $check_approve_status['staffid'])) {
				$html .= '<div class="btn-group pull-left" >
			<a href="#" class="btn btn-success dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . _l('approve') . '<span class="caret"></span></a>
			<ul class="dropdown-menu dropdown-menu-left wh-500-190">
			<li>
			<div class="col-md-12">
			' . render_textarea('reason', 'reason') . '
			</div>
			</li>
			<li>
			<div class="row text-right col-md-12">
			<a href="#" data-loading-text="' . _l('wait_text') . '" onclick="approve_request(' . $additional_timesheets->id . ',\'additional_timesheets\'); return false;" class="btn btn-success margin-left-right-15">' . _l('approve') . '</a>
			<a href="#" data-loading-text="' . _l('wait_text') . '" onclick="deny_request(' . $additional_timesheets->id . ',\'additional_timesheets\'); return false;" class="btn btn-warning">' . _l('deny') . '</a>
			</div>
			</li>
			</ul>
			</div>';
			}
		}
		if ($check != 'choose') {
			$html .= '<a href="#" class="btn btn-default pull-right" data-toggle="modal" data-target=".additional-timesheets-sidebar">' . _l('close') . '</a>';
		}

		$html .= '</div></div>
	</div>


	<div class="clearfix"></div>
	</div>
	</div>';
		echo json_encode([
			'html' => $html,
		]);
		die();
	}

	/**
	 * reports
	 * @return view
	 */
	public function reports()
	{
		if (!(has_permission('report_management', '', 'view_own') || has_permission('report_management', '', 'view') || is_admin())) {
			access_denied('reports');
		}
		$this->load->model('staff_model');
		$this->load->model('departments_model');
		$this->load->model('roles_model');
		$data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
		$data['sqlMode'] = $this->db->query('SELECT @@sql_mode as mode')->row();
		$data['staff'] = $this->staff_model->get('', 'active = 1');
		$data['department'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['workplace'] = $this->timesheets_model->get_workplace();
		$data['route_point'] = $this->timesheets_model->get_route_point();
		$data['word_shift'] = $this->timesheets_model->get_shift_type();
		$data['title'] = _l('hr_reports');
		$this->load->view('reports/manage_reports', $data);
	}

	/**
	 * report by leave statistics
	 * @return json
	 */
	public function report_by_leave_statistics()
	{
		echo json_encode($this->timesheets_model->report_by_leave_statistics());
	}

	/**
	 * report by working hours
	 * @return json
	 */
	public function report_by_working_hours()
	{
		echo json_encode($this->timesheets_model->report_by_working_hours());
	}

	/**
	 * [HR_is_working description]
	 */
	public function HR_is_working()
	{
		if ($this->input->is_ajax_request()) {
			$year = (string) date('Y');
			$months_report = $this->input->post('months_report');

			if ($months_report == '' || !isset($months_report)) {
			}
			if ($months_report == 'this_month') {
			}
			if ($months_report == '1') {
			}
			if ($months_report == 'this_year') {
				$year = (string) date('Y');
			}
			if ($months_report == 'last_year') {
				$year = (string) ((int) date('Y') - 1);
			}

			if ($months_report == '3') {
			}
			if ($months_report == '6') {
			}
			if ($months_report == '12') {
			}
			$month_default = 12;

			$list_data = array();
			for ($i = 1; $i <= $month_default; $i++) {
				$staff_list = $this->timesheets_model->get_dstafflist_by_year($year, $i);
				$count = count($staff_list);
				array_push($list_data, $count);
			}

			echo json_encode([
				'data' => $list_data,
				'data_ratio' => $list_data,
			]);
		}
	}

	/**
	 * file view requisition
	 * @param  int $id
	 * @param  int $rel_id
	 * @return
	 */
	public function file_view_requisition($id, $rel_id)
	{
		$data['file'] = $this->timesheets_model->get_file_requisition($id, $rel_id);
		$data['rel_id'] = $rel_id;
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('includes/_file', $data);
	}

	/**
	 * leave reports
	 * @return json
	 */
	public function leave_reports()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');

				$role_filter = $this->input->post('role_filter');
				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');

				$year_filter = $this->input->post('year_period');
				$year = date('Y');
				if ($months_report == 'last_year') {
					$year = (int) $year - 1;
				}
				$select = [
					'staffid',
					'firstname',

					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
				];
				$query = '';
				if (isset($role_filter)) {
					$position_list = implode(',', $role_filter);
					$query .= ' role in (' . $position_list . ') and';
				}
				if (has_permission('report_management', '', 'view') || is_admin()) {
					if (isset($staff_filter)) {
						$staffid_list = implode(',', $staff_filter);
						$query .= ' staffid in (' . $staffid_list . ') and';
					}
				} else {
					$query .= ' ' . timesheet_staff_manager_query('report_management', 'staffid', '') . ' and';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= ' staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				$from_date = date("Y-m-01");
				$to_date = date("Y-12-t");
				$year_leave = date('Y');
				if (isset($year_filter)) {
					$plit_date = explode('|', $year_filter);
					$from_date = $this->timesheets_model->format_date($plit_date[0]);
					$to_date = $this->timesheets_model->format_date($plit_date[1]);
					$year_leave = date('Y', strtotime($from_date));
				}

				$list_date = $this->timesheets_model->get_list_month($from_date, $to_date);

				$query .= ' active = 1 and';
				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}
				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'lastname',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];

				foreach ($rResult as $aRow) {

					$day_off = $this->timesheets_model->get_requisition_number_day_off_manual_leave($aRow['staffid'], $year_leave);
					$timesheets_total_day_off = 0;

					$row = [];
					$row[] = $aRow['staffid'];
					$row[] = trim($aRow['firstname'] . ' ' . $aRow['lastname']);
					$row[] = $day_off['total'];

					foreach ($list_date as $date) {
						$string_length = strlen($date);
						$months_filter = substr($date, 0, ($string_length - 3));
						$count = $this->timesheets_model->get_date_leave_in_month($aRow['staffid'], $months_filter);
						$row[] = $count;
						$timesheets_total_day_off += $count;
					}
					$row[] = $timesheets_total_day_off;
					$row[] = $day_off['remain'];
					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * general summation
	 * @param  int $month
	 * @param  int $year
	 * @param  int $staffid
	 * @return
	 */
	public function general_summation($month, $year, $staffid)
	{
		$result = 0;
		$data_leave = $this->timesheets_model->get_timesheets_day_leave_by_staffid($staffid, $year);
		foreach ($data_leave as $key => $value) {
			$start_month = $value['start_month'];
			$end_month = $value['end_month'];
			if ($start_month == $end_month) {
				if ($month == $start_month) {
					$result += $value['day_leave'];
				}
			}
			if ($start_month != $end_month) {
				if ($month == $start_month) {
					$result += $value['day_start_for'];
				}
				if ($month == $end_month) {
					$result += $value['day_end_for'];
				}
			}
		}
		return $result;
	}

	/**
	 * general public report
	 * @return json
	 */
	public function general_public_report()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');
				$role_filter = $this->input->post('role_filter');

				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');
				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}

				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				}

				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}

				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				}

				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				}

				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				}

				if ($months_report == 'custom') {
					$from_date = $this->timesheets_model->format_date($this->input->post('report_from'));
					$to_date = $this->timesheets_model->format_date($this->input->post('report_to'));
				}

				$select = [
					'staffid',
					'firstname',
					'lastname',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
				];
				$query = '';
				if (isset($role_filter)) {
					$position_list = implode(',', $role_filter);
					$query .= ' role in (' . $position_list . ') and';
				}
				if (has_permission('report_management', '', 'view') || is_admin()) {
					if (isset($staff_filter)) {
						$staffid_list = implode(',', $staff_filter);
						$query .= ' staffid in (' . $staffid_list . ') and';
					}
				} else {
					$query .= ' ' . timesheet_staff_manager_query('report_management', 'staffid', '') . ' and';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= ' staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}
				$query .= ' active = 1 and';
				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}
				$where = [$total_query];
				$aColumns = $select;
				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];
				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'staffid',
					'firstname',
					'lastname',
					'email',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];

				$data_timekeeping_form = get_timesheets_option('timekeeping_form');
				$data_timesheet = [];
				if ($data_timekeeping_form == 'timekeeping_task') {
					$data_timesheet = $this->timesheets_model->get_attendance_task($rResult, '', '', $from_date, $to_date);
				} else {
					$data_timesheet = $this->timesheets_model->get_attendance_manual($rResult, '', '', $from_date, $to_date);
				}
				$index_hr_code = _l('staff_id');

				if ($data_timesheet) {
					foreach ($rResult as $aRow) {
						$row = [];
						$row[] = $aRow['staffid'];
						$row[] = $aRow['firstname'];
						$row[] = $aRow['lastname'];
						$total = 0;
						$total2 = 0;
						$total3 = 0;
						$total7 = 0;
						$total8 = 0;
						$total9 = 0;
						$total10 = 0;
						$total11 = 0;
						$total12 = 0;
						$total13 = 0;
						$total14 = 0;
						$data_row_attendance = [];
						$data_working_hour = [];
						$index = 0;
						$total_shift = 0;
						foreach ($data_timesheet['staff_row_tk'] as $attendance_row) {
							if ($attendance_row[$index_hr_code] == $aRow['staffid']) {

								foreach ($data_timesheet['staff_row_tk_detailt'][$index] as $date => $list_attendance) {
									$shift_hour = $this->timesheets_model->get_hour_shift_staff($aRow['staffid'], $date);
									if ($shift_hour > 0) {
										$total_shift++;
									}
									if (($list_attendance != '') && ($shift_hour > 0)) {
										$list_tks = explode(';', $list_attendance);
										foreach ($list_tks as $key_tk => $tk) {
											$split_val = explode(':', trim($tk));
											if (strtolower($split_val[0]) == 'w') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total += $cal;
													$data_working_hour[] = $this->timesheets_model->calculate_working_hour($split_val[1]);
												}
											}
											if (strtolower($split_val[0]) == 'al') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total2 += $cal;
												}
											}

											if (strtolower($split_val[0]) == 'p') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total3 += $cal;
												}
											}
											if (strtolower($split_val[0]) == 'b') {
												$total7 += 1;
											}
											if (strtolower($split_val[0]) == 'si') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total8 += $cal;
												}
											}
											if (strtolower($split_val[0]) == 'm') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total9 += $cal;
												}
											}
											if (strtolower($split_val[0]) == 'u') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total10 += $cal;
												}
											}
											if (strtolower($split_val[0]) == 'ho') {
												$total11 += 1;
											}
											if (strtolower($split_val[0]) == 'e') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total12 += $cal;
												}
											}
											if (strtolower($split_val[0]) == 'l') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total13 += $cal;
												}
											}
											if (strtolower($split_val[0]) == 'me') {
												if (isset($split_val[1]) && is_numeric($split_val[1]) && $shift_hour > 0) {
													$cal = $split_val[1] / $shift_hour;
													$total14 += $cal;
												}
											}
										}
									}
								}
							}

							$index++;
						}

						$row[] = $total_shift;
						$row[] = ($total > 0) ? ceil($total * 2) / 2 : 0;
						$row[] = ($total2 > 0) ? (float) number_format($total2, 2) : 0;
						$row[] = ($total3 > 0) ? (float) number_format($total3, 2) : 0;
						$row[] = ($total7 > 0) ? (float) number_format($total7, 2) : 0;
						$row[] = ($total8 > 0) ? (float) number_format($total8, 2) : 0;
						$row[] = ($total9 > 0) ? (float) number_format($total9, 2) : 0;
						$row[] = ($total10 > 0) ? (float) number_format($total10, 2) : 0;
						$row[] = ($total11 > 0) ? (float) number_format($total11, 2) : 0;
						$row[] = ($total12 > 0) ? (float) number_format($total12, 2) : 0;
						$row[] = ($total13 > 0) ? (float) number_format($total13, 2) : 0;
						$row[] = ($total14 > 0) ? (float) number_format($total14, 2) : 0;
						// $row[] = (count($data_working_hour) > 0) ? $this->timesheets_model->calculate_total_working_hour($data_working_hour) : '';
						$output['aaData'][] = $row;
					}
				}
				echo json_encode($output);
				die();
			}
		}
	}

	/*mass delete for multiple feature*/
	public function timesheets_delete_bulk_action()
	{
		if (!is_staff_member()) {
			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {

			$ids = $this->input->post('ids');
			$rel_type = $this->input->post('rel_type');

			/*check permission*/
			switch ($rel_type) {
				case 'timesheets_requisition':
					if (!has_permission('timesheets_manage_requisition', '', 'delete') && !is_admin()) {
						access_denied('manage_requisition');
					}
					break;
				default:
					# code...
					break;
			}

			/*delete data*/
			if ($this->input->post('mass_delete')) {
				if (is_array($ids)) {
					foreach ($ids as $id) {

						switch ($rel_type) {
							case 'timesheets_requisition':
								if ($this->timesheets_model->delete_requisition($id)) {
									$total_deleted++;
									break;
								} else {
									break;
								}
							default:
								# code...
								break;
						}
					}
				}

				/*return result*/
				switch ($rel_type) {
					case 'timesheets_requisition':
						set_alert('success', _l('total_requisition') . ": " . $total_deleted);
						break;
					default:
						# code...
						break;
				}
			}
		}
	}

	/**
	 * get rest time
	 * @return json
	 */
	public function get_rest_time()
	{
		$data = $this->input->post();
		$rest_time = $this->timesheets_model->get_rest_time($data['date']);
		echo json_encode($rest_time);
	}

	/**
	 * delete additional timesheets
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_additional_timesheets($id)
	{
		$response = $this->timesheets_model->delete_additional_timesheets($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced'));
		} elseif ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('timesheets/requisition_manage?tab=additional_timesheets'));
	}

	/**
	 * table shiftwork
	 * @return view
	 */
	public function table_shiftwork()
	{
		if (!(has_permission('table_shiftwork_management', '', 'view_own') || has_permission('table_shiftwork_management', '', 'view') || is_admin())) {
			access_denied('table_shiftwork');
		}
		$this->load->model('staff_model');
		$data['title'] = _l('table_shiftwork');
		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $this->staff_model->get('', 'active = 1');
		$data['roles'] = $this->roles_model->get();
		$data['job_position'] = $this->roles_model->get();
		$data['positions'] = $this->roles_model->get();
		$data['shifts'] = $this->timesheets_model->get_shifts();
		$date = date('Y-m-01');
		$month = date('m', strtotime($date));
		$month_year = date('Y', strtotime($date));
		$this->load->model('staff_model');
		$list_staff_id = [];
		$data_staff_list = $this->staff_model->get('', 'active = 1' . timesheet_staff_manager_query('table_shiftwork_management'));
		foreach ($data_staff_list as $key => $value) {
			$list_staff_id[] = $value['staffid'];
		}
		$data_hs = $this->set_col_tk(1, 31, $month, $month_year, true, $list_staff_id);
		$data['day_by_month'] = json_encode($data_hs->day_by_month);
		$data['list_data'] = json_encode($data_hs->list_data);
		$list_date = $this->timesheets_model->get_list_date($date, date('Y-m-t'));
		$data_object = [];
		foreach ($list_staff_id as $key => $value) {

			$row_data_staff = new stdClass();
			$row_data_staff->staffid = $value;
			$row_data_staff->staff = get_staff_full_name($value);
			$row_data_color = new stdClass();
			$row_data_color->staffid = '';
			$row_data_color->staff = '';

			foreach ($list_date as $kdbm => $day) {
				$shift_s = '';
				$color = '';
				$list_shift = $this->timesheets_model->get_shift_work_staff_by_date($value, $day);
				foreach ($list_shift as $ss) {
					$data_shift_type = $this->timesheets_model->get_shift_type($ss);
					if ($data_shift_type) {
						if ($color == '') {
							$color = $data_shift_type->color;
						}
						$start_date = $data_shift_type->time_start_work;
						$st_1 = explode(':', $start_date);
						$st_time = $st_1[0] . 'h' . $st_1[1];

						$end_date = $data_shift_type->time_end_work;
						$e_2 = explode(':', $end_date);
						$e_time = $e_2[0] . 'h' . $e_2[1];

						$shift_s .= $data_shift_type->shift_type_name . ' (' . $st_time . ' - ' . $e_time . ')' . "\n";
					}
				}
				$day_s = date('D d', strtotime($day));
				$row_data_staff->$day_s = $shift_s;
				$row_data_color->$day_s = $color;
			}
			$data_object[] = $row_data_staff;
			$data_color[] = $row_data_color;
		}
		$data['data_object'] = $data_object;
		$data['data_color'] = $data_color;
		$this->load->view('timekeeping/manage_table_shiftwork', $data);
	}

	/**
	 * reload shift work byfilter
	 * @return json
	 */
	public function reload_shift_work_byfilter()
	{
		$data = $this->input->post();
		$year = date('Y', strtotime(to_sql_date('01/' . $data['month'])));
		$g_month = date('m', strtotime(to_sql_date('01/' . $data['month'])));
		$department = $data['department'];
		$role = $data['role'];

		$data['month'] = date('m-Y', strtotime(to_sql_date('01/' . $data['month'])));
		$data['check_latch_timesheet'] = $this->timesheets_model->check_latch_timesheet($data['month']);

		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$staff_querystring = '';
		$role_querystring = '';
		$department_querystring = '';
		$month_year_querystring = '';
		$month = date('m');
		$month_year = date('Y');
		$cmonth = date('m');
		$cyear = date('Y');
		if ($year != '') {
			$month_new = (string) $g_month;
			if (strlen($month_new) == 1) {
				$month_new = '0' . $month_new;
			}
			$month = $month_new;
			$month_year = (int) $year;
		}
		if ($department != '') {
			$arrdepartment = $this->staff_model->get('', 'staffid in (select ' . db_prefix() . 'staff_departments.staffid from ' . db_prefix() . 'staff_departments where departmentid = ' . $department . ')');
			$temp = '';
			foreach ($arrdepartment as $value) {
				$temp = $temp . $value['staffid'] . ',';
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		if ($role != '') {
			$role_querystring = 'role = "' . $role . '"';
		}

		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		} else {
			$staff_querystring = 'FIND_IN_SET(staffid, "' . get_timesheets_option('timekeeping_applicable_object') . '")';
		}

		$arrQuery = array($staff_querystring, $department_querystring, $month_year_querystring, $role_querystring);
		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		$data['staff_row'] = [];
		$shift_staff = [];
		if ($newquerystring != '') {

			$data['day_by_month'] = [];
			$data['day_by_month'][] = _l('staff');

			$data['set_col'] = [];
			$data['set_col'][] = ['data' => _l('staff'), 'type' => 'text'];

			$month = $g_month;
			$month_year = $year;
			for ($d = 1; $d <= 31; $d++) {
				$time = mktime(12, 0, 0, $month, $d, $month_year);
				if (date('m', $time) == $month) {

					array_push($data['day_by_month'], date('D d', $time));
					array_push($data['set_col'], ['data' => date('D d', $time), 'type' => 'text']);
				}
			}

			$data['staffs_setting'] = $this->timesheets_model->getStaff('', $newquerystring);

			foreach ($data['staffs_setting'] as $ss) {

				$work_shift['shift_s'] = $this->timesheets_model->get_data_edit_shift_by_staff($ss['staffid']);
				$shift_staff = [_l('staff') => $ss['firstname'] . ' ' . $ss['lastname']];

				if (isset($work_shift['shift_s'])) {
					for ($d = 1; $d <= 31; $d++) {
						$time = mktime(12, 0, 0, $g_month, $d, $year);
						if (date('m', $time) == $g_month) {
							if (date('N', $time) == 1) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['monday'] . ' - ' . $work_shift['shift_s'][1]['monday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['monday'] . ' - ' . $work_shift['shift_s'][3]['monday'];
							} elseif (date('N', $time) == 2) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['tuesday'] . ' - ' . $work_shift['shift_s'][1]['tuesday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['tuesday'] . ' - ' . $work_shift['shift_s'][3]['tuesday'];
							} elseif (date('N', $time) == 3) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['wednesday'] . ' - ' . $work_shift['shift_s'][1]['wednesday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['wednesday'] . ' - ' . $work_shift['shift_s'][3]['wednesday'];
							} elseif (date('N', $time) == 4) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['thursday'] . ' - ' . $work_shift['shift_s'][1]['thursday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['thursday'] . ' - ' . $work_shift['shift_s'][3]['thursday'];
							} elseif (date('N', $time) == 5) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['friday'] . ' - ' . $work_shift['shift_s'][1]['friday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['friday'] . ' - ' . $work_shift['shift_s'][3]['friday'];
							} elseif (date('N', $time) == 7) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['sunday'] . ' - ' . $work_shift['shift_s'][1]['sunday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['sunday'] . ' - ' . $work_shift['shift_s'][3]['sunday'];
							} elseif (date('N', $time) == 6 && (date('d', $time) % 2) == 1) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['saturday_odd'] . ' - ' . $work_shift['shift_s'][1]['saturday_odd'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['saturday_odd'] . ' - ' . $work_shift['shift_s'][3]['saturday_odd'];
							} elseif (date('N', $time) == 6 && (date('d', $time) % 2) == 0) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['saturday_even'] . ' - ' . $work_shift['shift_s'][1]['saturday_even'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['saturday_even'] . ' - ' . $work_shift['shift_s'][3]['saturday_even'];
							}
						}
					}
				}

				if ($shift_staff != 'null' && $shift_staff != '') {

					array_push($data['staff_row'], $shift_staff);
				}
			}
		}
		echo json_encode([
			'staff_row' => $data['staff_row'],
			'day_by_month_n' => $data['day_by_month'],
			'set_col_n' => $data['set_col'],
		]);
		die;
	}

	/**
	 * show detail timesheets mem
	 * @return
	 */
	public function show_detail_timesheets_mem()
	{
		$data = $this->input->post();
		$year = date("Y");

		$day = $data['day'];
		$month = implode($data['month']);
		$member_id = $data['member_id'];

		$t = $day . '/' . $month;
		$time = strtotime(to_sql_date($t . '/' . date('Y')));
		$d = date('Y-m-d', strtotime($year . '-' . $month . '-' . $day));

		$title = get_staff_full_name($member_id) . ' - ' . _d($d);
		$work_shift = $this->timesheets_model->get_data_edit_shift_by_staff($member_id);

		$data['value'] = explode('; ', $data['value']);
		$html = '';
		foreach ($data['value'] as $key => $value) {
			$value = explode(':', $value);
			if (isset($value[1]) && $value[1] > 0 || $value[0] == 'M' || $value[0] == 'HO') {
				switch ($value[0]) {
					case 'L':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('p_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_p">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'W':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('W_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_w">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'U':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('A_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_a">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'HO':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('Le_timekeeping') . '
				</li>';
						break;
					case 'E':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('E_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_e">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'L':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('L_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_l">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'B':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('CT_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_l">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'OM':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('OM_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'M':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('TS_timekeeping') . '
				</li>';
						break;
					case 'R':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('R_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'P':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('P_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'SI':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('CD_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'CO':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('CO_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_u">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'H':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('H_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_me">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'OT':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('OT_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_me">' . round($value[1], 2) . '</span>
				</li>';
						break;
					case 'PN':
						$html .= '<li class="list-group-item justify-content-between">
				' . _l('PN_timekeeping') . '
				<span class="badgetext badge badge-primary badge-pill style_p">' . round($value[1], 2) . '</span>
				</li>';
						break;
				}
			}
		}

		$ws_day = '';
		$data['staff_sc'] = $this->timesheets_model->get_staff_shift_applicable_object();
		$list_staff_sc = [];
		foreach ($data['staff_sc'] as $key => $value) {
			$list_staff_sc[] = $value['staffid'];
		}
		if (in_array($member_id, $list_staff_sc)) {
			$shift = $this->timesheets_model->get_shiftwork_sc_date_and_staff($d, $member_id);
			if (isset($shift)) {
				$work_shift = $this->timesheets_model->get_shift_sc($shift);

				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift->time_start_work . ' - ' . $work_shift->time_end_work . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift->start_lunch_break_time . ' - ' . $work_shift->end_lunch_break_time . '</li>';
			}
		} else {
			if (date('N', $time) == 1) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['monday'] . ' - ' . $work_shift[1]['monday'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['monday'] . ' - ' . $work_shift[3]['monday'] . '</li>';
			} elseif (date('N', $time) == 2) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['tuesday'] . ' - ' . $work_shift[1]['tuesday'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['tuesday'] . ' - ' . $work_shift[3]['tuesday'] . '</li>';
			} elseif (date('N', $time) == 3) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['wednesday'] . ' - ' . $work_shift[1]['wednesday'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['wednesday'] . ' - ' . $work_shift[3]['wednesday'] . '</li>';
			} elseif (date('N', $time) == 4) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['thursday'] . ' - ' . $work_shift[1]['thursday'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['thursday'] . ' - ' . $work_shift[3]['thursday'] . '</li>';
			} elseif (date('N', $time) == 5) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['friday'] . ' - ' . $work_shift[1]['friday'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['friday'] . ' - ' . $work_shift[3]['friday'] . '</li>';
			} elseif (date('N', $time) == 7) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['sunday'] . ' - ' . $work_shift[1]['sunday'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['sunday'] . ' - ' . $work_shift[3]['sunday'] . '</li>';
			} elseif (date('N', $time) == 6 && (date('d', $time) % 2) == 1) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['saturday_odd'] . ' - ' . $work_shift[1]['saturday_odd'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['saturday_odd'] . ' - ' . $work_shift[3]['saturday_odd'] . '</li>';
			} elseif (date('N', $time) == 6 && (date('d', $time) % 2) == 0) {
				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift[0]['saturday_even'] . ' - ' . $work_shift[1]['saturday_even'] . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift[2]['saturday_even'] . ' - ' . $work_shift[3]['saturday_even'] . '</li>';
			}
		}
		if ($ws_day != '') {
			$html .= $ws_day;
		}

		$access_history_string = '';
		$staff_identifi = $this->timesheets_model->get_staff_identifi($member_id);
		$access_history = $this->timesheets_model->get_access_history($staff_identifi, $d);

		if ($access_history) {
			foreach ($access_history as $key => $value) {
				if ($value['type'] == 'in') {
					$access_history_string .= '<li class="list-group-item"><i class="fa fa-sign-in text-success" aria-hidden="true"></i> ' . _dt($value['time']) . '</li>';
				} else {
					$access_history_string .= '<li class="list-group-item"><i class="fa fa-sign-out text-danger" aria-hidden="true"></i> ' . _dt($value['time']) . '</li>';
				}
			}
		}
		if ($access_history_string != '') {
			$html .= '<li class="list-group-item justify-content-between"><ul class="list-group">
		<li class="list-group-item active">' . _l('access_history') . '</li>
		' . $access_history_string . '
		</ul></li>';
		}
		echo json_encode([
			'title' => $title,
			'html' => $html,
		]);
		die();
	}

	/**
	 * Calculates the number days off.
	 */
	public function calculate_number_days_off()
	{
		$data = $this->input->post();
		$start_time = $this->timesheets_model->format_date($data['start_time']);
		$end_time = $this->timesheets_model->format_date($data['end_time']);
		$list_af_date = [];
		if ($start_time != '' && $end_time != '') {
			if ($start_time && $end_time) {
				if (strtotime($start_time) <= strtotime($end_time)) {
					$list_date = $this->timesheets_model->get_list_date($start_time, $end_time);
					foreach ($list_date as $key => $next_start_date) {
						$data_work_time = $this->timesheets_model->get_hour_shift_staff($data['staffid'], $next_start_date);
						$data_day_off = $this->timesheets_model->get_day_off_staff_by_date($data['staffid'], $next_start_date);
						if ($data_work_time > 0 && count($data_day_off) == 0) {
							$list_af_date[] = $next_start_date;
						}
					}
				}
			}
		}
		$count = count($list_af_date);
		echo json_encode($count);
	}

	/**
	 * table registration leave
	 */
	public function table_registration_leave_by_staff()
	{
		$this->app->get_table_data(module_views_path('timesheets', 'table_registration_leave_by_staff'));
	}

	/**
	 * [get_data_date_leave description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_data_date_leave()
	{
		$memberid = $this->input->post('memberid');
		$year_requisition = $this->input->post('year_requisition');

		$list = $this->timesheets_model->get_requisition_number_of_day_off($memberid, $year_requisition);

		echo json_encode([
			'total_day_off_in_year' => $list['total_day_off_in_year'],
			'total_day_off' => $list['total_day_off'],
			'total_day_off_allowed_in_year' => $list['total_day_off_allowed_in_year'],

		]);
	}

	/**
	 * shifts sc
	 * @return [type] [description]
	 */
	public function shifts_sc()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$add = $this->timesheets_model->add_shift_sc($data);
				if ($add > 0) {
					$message = _l('added_successfully', _l('shift'));
					set_alert('success', $message);
				}
				redirect(admin_url('timesheets/setting?group=shift'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->timesheets_model->update_shift_sc($data, $id);
				if ($success == true) {
					$message = _l('ts_updated_successfully', _l('shift'));
					set_alert('success', $message);
				}
				redirect(admin_url('timesheets/setting?group=shift'));
			}
		}
	}

	/**
	 * delete shift sc
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_shift_sc($id)
	{
		if (!$id) {
			redirect(admin_url('timesheets/setting?group=shift'));
		}
		$response = $this->timesheets_model->delete_shift_sc($id);
		if ($response == true) {
			set_alert('success', _l('deleted', _l('shift')));
		} else {
			set_alert('warning', _l('problem_deleting') . ' ' . _l('shift'));
		}
		redirect(admin_url('timesheets/setting?group=shift'));
	}

	/**
	 * setting shift
	 * @return redirect
	 */
	public function setting_shift()
	{
		$data = $this->input->post();
		$success = $this->timesheets_model->setting_shift($data);
		if ($success) {
			set_alert('success', _l('save_setting_success'));
		} else {
			set_alert('danger', _l('save_setting_fail'));
		}
		redirect(admin_url('timesheets/setting?group=shift_setting'));
	}

	/**
	 * [shiftwork_sc description]
	 * @return [type] [description]
	 */
	public function shiftwork_sc()
	{
		$data = $this->input->post();
		$success = $this->timesheets_model->update_shiftwork_sc($data);
		if ($success > 0) {
			$message = _l('ts_updated_successfully');
			set_alert('success', $message);
		}
		redirect(admin_url('timesheets/timekeeping?group=table_shiftwork_sc'));
	}

	/**
	 * reload shiftwork sc by filter
	 * @return json
	 */
	public function reload_shiftwork_sc_byfilter()
	{
		$data = $this->input->post();
		$year = date('Y', strtotime(to_sql_date('01/' . $data['month'])));
		$g_month = date('m', strtotime(to_sql_date('01/' . $data['month'])));
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $g_month, $year);

		$month_filter = date('Y-m', strtotime(to_sql_date('01/' . $data['month'])));
		$querystring = '(select count(*) from ' . db_prefix() . 'staff_contract where staff = ' . db_prefix() . 'staff.staffid and DATE_FORMAT(start_valid, "%Y-%m") <="' . $month_filter . '" and IF(end_valid != null, DATE_FORMAT(end_valid, "%Y-%m") >="' . $month_filter . '",1=1)) > 0 and status_work="working" and active=1';
		$department = $data['department'];
		$job_position = $data['job_position'];

		$data['month'] = date('m-Y', strtotime(to_sql_date('01/' . $data['month'])));

		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}

		$staff_querystring = '';
		$job_position_querystring = '';
		$department_querystring = '';
		$month_year_querystring = '';
		$month = date('m');
		$month_year = date('Y');
		$cmonth = date('m');
		$cyear = date('Y');
		if ($year != '') {
			$month_new = (string) $g_month;
			if (strlen($month_new) == 1) {
				$month_new = '0' . $month_new;
			}
			$month = $month_new;
			$month_year = (int) $year;
		}

		if ($department != '') {
			$arrdepartment = $this->staff_model->get('', 'staffid in (select ' . db_prefix() . 'staff_departments.staffid from ' . db_prefix() . 'staff_departments where departmentid = ' . $department . ')');
			$temp = '';
			foreach ($arrdepartment as $value) {
				$temp = $temp . $value['staffid'] . ',';
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}
		if ($job_position != '') {
			$job_position_querystring = 'job_position = "' . $job_position . '"';
		}
		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		} else {
			$staff_querystring = 'FIND_IN_SET(job_position, "' . get_timesheets_option('shift_applicable_object') . '")';
		}

		$arrQuery = array($staff_querystring, $department_querystring, $month_year_querystring, $job_position_querystring, $querystring);
		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		$data['staff_row_sc'] = [];
		$data['days_in_month'] = $days_in_month;

		if ($newquerystring != '') {
			$staffs = $this->timesheets_model->getStaff('', $newquerystring);
			$shift_staff = [];
			foreach ($staffs as $s) {
				$work_shift['shift_s'] = $this->timesheets_model->get_data_edit_shift_by_staff($s['staffid'], $month_year . '-' . $month . '-01');

				$shift_staff = ['staffid' => $s['staffid'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
				if (isset($work_shift['shift_s'])) {
					for ($d = 1; $d <= $days_in_month; $d++) {
						$time = mktime(12, 0, 0, $month, $d, $month_year);
						$shift_staff[date('d/m D', $time)] = $this->timesheets_model->get_shiftwork_sc_date_and_staff(date('Y-m-d', $time), $s['staffid']);
					}
				}
				array_push($data['staff_row_sc'], $shift_staff);
			}
		}

		$data['set_col_sc'] = [];

		$data['shift_sc'] = $this->timesheets_model->get_shift_sc();
		$data['select_shift_sc'] = [];
		foreach ($data['shift_sc'] as $key => $value) {
			$node = [];
			$node['id'] = $value['id'];
			$node['label'] = $value['shift_symbol'];
			$data['select_shift_sc'][] = $node;
		}

		for ($d = 1; $d <= $days_in_month; $d++) {
			$time = mktime(12, 0, 0, $month, $d, $month_year);
			if (date('m', $time) == $month) {
				$data['set_col_sc'][] = date('d/m D', $time);
			}
		}
		$data['set_col_sc'] = $data['set_col_sc'];

		$data['day_by_month'] = [];
		$data['day_by_month'][] = _l('staff_id');
		$data['day_by_month'][] = _l('staff');

		for ($d = 1; $d <= 31; $d++) {
			$time = mktime(12, 0, 0, $month, $d, $month_year);
			if (date('m', $time) == $month) {
				array_push($data['day_by_month'], date('d/m D', $time));
			}
		}

		$data['day_by_month'] = $data['day_by_month'];

		echo json_encode([
			'arr' => $data['staff_row_sc'],
			'set_col_sc' => $data['set_col_sc'],
			'select_shift_sc' => $data['select_shift_sc'],
			'month' => $data['month'],
			'day_by_month' => $data['day_by_month'],
			'days_in_month' => $data['days_in_month'],
		]);
		die;
	}

	/**
	 * cancel request
	 * @return
	 */
	public function cancel_request()
	{
		$data = $this->input->post();
		$success = false;
		$message = '';
		$success = $this->timesheets_model->cancel_request($data);
		if ($success == true) {
			$message = _l('cancel_successful');
		} else {
			$message = _l('cancel_failed');
		}
		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
		die();
	}

	/**
	 * add allocate shiftwork
	 * @param string $id
	 */
	public function add_allocate_shiftwork($id = '')
	{
		$this->load->model('staff_model');

		$data['additional_timesheets_id'] = $this->input->get('additional_timesheets_id');
		$data['group'] = $this->input->get('group');
		$data['title'] = _l($data['group']);

		$status_leave = $this->timesheets_model->get_option_val();

		$data['tab'][] = 'table_shiftwork';
		$data['tab'][] = 'allocate_shiftwork';

		if ($data['group'] == '') {
			$data['group'] == 'table_shiftwork';
		}

		if ($data['group'] == 'timesheets') {
			$data['check_latch_timesheet'] = $this->timesheets_model->check_latch_timesheet(date('m-Y'));
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs_li'] = $this->staff_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['job_position'] = $this->roles_model->get();
		$data['positions'] = $this->roles_model->get();
		$data['additional_timesheets'] = $this->timesheets_model->get_additional_timesheets();
		$data['holiday'] = $this->timesheets_model->get_break_dates('holiday');
		$data['event_break'] = $this->timesheets_model->get_break_dates('event_break');
		$data['unexpected_break'] = $this->timesheets_model->get_break_dates('unexpected_break');
		$data['shifts'] = $this->timesheets_model->get_shifts();

		$data['day_by_month'] = [];
		$data['day_by_month_tk'] = [];
		$data['day_by_month'][] = _l('staff');
		$data['day_by_month_tk'][] = _l('staff_id');
		$data['day_by_month_tk'][] = _l('hr_code');
		$data['day_by_month_tk'][] = _l('staff');

		$data['set_col'] = [];
		$data['set_col_tk'] = [];
		$data['set_col_tk'][] = ['data' => _l('staff_id'), 'type' => 'text'];
		$data['set_col_tk'][] = ['data' => _l('hr_code'), 'type' => 'text', 'readOnly' => true, 'width' => 55];
		$data['set_col_tk'][] = ['data' => _l('staff'), 'type' => 'text', 'readOnly' => true, 'width' => 200];
		$data['set_col'][] = ['data' => _l('staff'), 'type' => 'text'];

		$month = date('m');
		$month_year = date('Y');
		for ($d = 1; $d <= 31; $d++) {
			$time = mktime(12, 0, 0, $month, $d, $month_year);
			if (date('m', $time) == $month) {
				array_push($data['day_by_month_tk'], date('D d', $time));
				array_push($data['day_by_month'], date('D d', $time));
				array_push($data['set_col'], ['data' => date('D d', $time), 'type' => 'text']);
				array_push($data['set_col_tk'], ['data' => date('D d', $time), 'type' => 'text']);
			}
		}

		$data['day_by_month'] = json_encode($data['day_by_month']);
		$data['day_by_month_tk'] = json_encode($data['day_by_month_tk']);

		$data['set_col'] = json_encode($data['set_col']);
		$data['set_col_tk'] = json_encode($data['set_col_tk']);

		$data_ts = $this->timesheets_model->get_timesheets_ts_by_month(date('m'), date('Y'));

		$data_map = [];
		foreach ($data_ts as $ts) {
			$staff_info = array();
			$staff_info['date'] = date('D d', strtotime($ts['date_work']));

			$ts_type = $this->timesheets_model->get_ts_by_date_and_staff($ts['date_work'], $ts['staff_id']);
			if (count($ts_type) <= 1) {
				$staff_info['ts'] = $ts['type'] . ':' . $ts['value'];
			} else {
				$str = '';
				foreach ($ts_type as $tp) {
					if ($str == '') {
						$str .= $tp['type'] . ':' . $tp['value'];
					} else {
						$str .= '-' . $tp['type'] . ':' . $tp['value'];
					}
				}
				$staff_info['ts'] = $str;
			}

			if (!isset($data_map[$ts['staff_id']])) {
				$data_map[$ts['staff_id']] = array();
			}
			$data_map[$ts['staff_id']][$staff_info['date']] = $staff_info;
		}

		$data['staff_row_tk'] = [];
		$data['staff_row'] = [];
		$staffs = $this->timesheets_model->get_staff_timekeeping_applicable_object();
		$data['staffs_setting'] = $this->staff_model->get();
		$data['staffs'] = $staffs;

		$shift_staff = [];
		foreach ($data['staffs_setting'] as $ss) {
			$work_shift['shift_s'] = $this->timesheets_model->get_data_edit_shift_by_staff($ss['staffid']);
			$shift_staff = [_l('staff') => $ss['firstname'] . ' ' . $ss['lastname']];
			if (isset($work_shift['shift_s'])) {
				if ($work_shift['shift_s']) {
					for ($d = 1; $d <= 31; $d++) {
						$time = mktime(12, 0, 0, $month, $d, $month_year);
						if (date('m', $time) == $month) {
							if (date('N', $time) == 1) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['monday'] . ' - ' . $work_shift['shift_s'][1]['monday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['monday'] . ' - ' . $work_shift['shift_s'][3]['monday'];
							} elseif (date('N', $time) == 2) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['tuesday'] . ' - ' . $work_shift['shift_s'][1]['tuesday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['tuesday'] . ' - ' . $work_shift['shift_s'][3]['tuesday'];
							} elseif (date('N', $time) == 3) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['wednesday'] . ' - ' . $work_shift['shift_s'][1]['wednesday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['wednesday'] . ' - ' . $work_shift['shift_s'][3]['wednesday'];
							} elseif (date('N', $time) == 4) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['thursday'] . ' - ' . $work_shift['shift_s'][1]['thursday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['thursday'] . ' - ' . $work_shift['shift_s'][3]['thursday'];
							} elseif (date('N', $time) == 5) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['friday'] . ' - ' . $work_shift['shift_s'][1]['friday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['friday'] . ' - ' . $work_shift['shift_s'][3]['friday'];
							} elseif (date('N', $time) == 7) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['sunday'] . ' - ' . $work_shift['shift_s'][1]['sunday'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['sunday'] . ' - ' . $work_shift['shift_s'][3]['sunday'];
							} elseif (date('N', $time) == 6 && (date('d', $time) % 2) == 1) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['saturday_odd'] . ' - ' . $work_shift['shift_s'][1]['saturday_odd'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['saturday_odd'] . ' - ' . $work_shift['shift_s'][3]['saturday_odd'];
							} elseif (date('N', $time) == 6 && (date('d', $time) % 2) == 0) {
								$shift_staff[date('D d', $time)] = _l('time_working') . ': ' . $work_shift['shift_s'][0]['saturday_even'] . ' - ' . $work_shift['shift_s'][1]['saturday_even'] . '  ' . _l('time_lunch') . ': ' . $work_shift['shift_s'][2]['saturday_even'] . ' - ' . $work_shift['shift_s'][3]['saturday_even'];
							}
						}
					}
				}
			}
			array_push($data['staff_row'], $shift_staff);
		}
		foreach ($staffs as $s) {
			$ts_date = '';
			$ts_ts = '';
			$result_tb = [];
			if (isset($data_map[$s['staffid']])) {
				foreach ($data_map[$s['staffid']] as $key => $value) {
					$ts_date = $data_map[$s['staffid']][$key]['date'];
					$ts_ts = $data_map[$s['staffid']][$key]['ts'];
					$result_tb[] = [$ts_date => $ts_ts];
				}
			}
			$dt_ts = [];
			$dt_ts = [_l('staff_id') => $s['staffid'], _l('hr_code') => $s['staff_identifi'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
			foreach ($result_tb as $key => $rs) {
				foreach ($rs as $day => $val) {
					$dt_ts[$day] = $val;
				}
			}
			array_push($data['staff_row_tk'], $dt_ts);
		}

		$data['tabs']['view'] = 'timekeeping/' . $data['group'];
		$this->load->view('timekeeping/add_allocate_shiftwork', $data);
	}

	/**
	 * get date leave
	 * @return date
	 */
	public function get_date_leave()
	{
		$data = $this->input->post();
		$staffid = $data['staffid'];
		$number_of_days = $data['number_of_days'];
		$start_date = date('Y-m-d');
		if (!$this->timesheets_model->check_format_date_ymd($data['startdate'])) {
			$start_date = to_sql_date($data['startdate']);
		} else {
			$start_date = $data['startdate'];
		}
		$ceiling_number_of_days = ceil($number_of_days);

		$list_date = [];
		$i = 0;
		while (count($list_date) != $ceiling_number_of_days) {

			$next_start_date = date('Y-m-d', strtotime($start_date . ' +' . $i . ' day'));
			$data_work_time = $this->timesheets_model->get_hour_shift_staff($staffid, $next_start_date);
			$data_day_off = $this->timesheets_model->get_day_off_staff_by_date($staffid, $next_start_date);
			if ($data_work_time > 0 && count($data_day_off) == 0) {
				$list_date[] = $next_start_date;
			}
			$i++;
			if ($i > 100) {
				break;
			}
		}
		$end_date = $start_date;
		if (count($list_date) > 0) {
			if (isset($list_date[count($list_date) - 1])) {
				$end_date = ($list_date[count($list_date) - 1]);
			}
		} else {
			$end_date = '';
			$start_date = '';
		}
		echo json_encode([
			'end_date' => _d($end_date),
		]);
		die;
	}

	/**
	 * table shift type
	 * @return json
	 */
	public function table_shift_type()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$select = [
					'id',
					'shift_type_name',
					'description',
					'id',
				];
				$where = [];
				$aColumns = $select;
				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'shift_type';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'shift_type_name',
					'color',
					'time_start',
					'time_end',
					'time_start_work',
					'time_end_work',
					'start_lunch_break_time',
					'end_lunch_break_time',
					'description',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$row[] = $aRow['id'];
					$row[] = $aRow['shift_type_name'];
					$row[] = $aRow['description'];

					$option = '';
					if (has_permission('table_shiftwork_management', '', 'view') || is_admin()) {
						$option .= '<a href="#" class="btn btn-default btn-icon" onclick="edit_shift_type(this); return false;" data-id="' . $aRow['id'] . '" data-shift_type_name="' . $aRow['shift_type_name'] . '" data-color="' . $aRow['color'] . '" data-time_start="' . $aRow['time_start'] . '" data-time_end="' . $aRow['time_end'] . '" data-time_start_work="' . $aRow['time_start_work'] . '" data-time_end_work="' . $aRow['time_end_work'] . '" data-start_lunch_break_time="' . $aRow['start_lunch_break_time'] . '" data-end_lunch_break_time="' . $aRow['end_lunch_break_time'] . '" data-description="' . $aRow['description'] . '" >';
						$option .= '<i class="fa fa-edit"></i>';
						$option .= '</a>';
						$option .= '<a href="' . admin_url('timesheets/delete_shift_type/' . $aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
						$option .= '<i class="fa fa-remove"></i>';
						$option .= '</a>';
					}
					$row[] = $option;
					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}
	public function manage_shift_type()
	{
		if (!(has_permission('table_shiftwork_management', '', 'view_own') || has_permission('table_shiftwork_management', '', 'view') || is_admin())) {
			access_denied('timekeeping');
		}
		$data['title'] = _l('manage_shift_type');
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['datecreated'] = date('Y-m-d');
			$data['add_from'] = get_staff_user_id();
			$message = '';
			if ($data['id'] == '') {
				$result = $this->timesheets_model->add_shift_type($data);
				if ($result > 0) {
					$message = _l('added_successfully');
				}
			} else {

				$success = $this->timesheets_model->update_shift_type($data);
				if ($success == true) {
					$message = _l('ts_updated_successfully');
				}
			}
			set_alert('success', $message);
			redirect(admin_url('timesheets/manage_shift_type'));
		}
		$this->load->view('manage_shift_type', $data);
	}
	public function shift_management()
	{
		if (!(has_permission('table_shiftwork_management', '', 'view_own') || has_permission('table_shiftwork_management', '', 'view') || is_admin())) {
			access_denied('timekeeping');
		}
		$data['title'] = _l('shift_management');
		$this->load->view('shift_management', $data);
	}

	public function set_col_tk($from_day, $to_day, $month, $month_year, $absolute_type = true, $stafflist = '', $work_shift_id = '')
	{
		$list_data = [];
		$data_day_by_month = [];
		$data_time = [];
		$data_day_by_month_tk = [];
		$data_set_col = [];
		$data_set_col_tk = [];
		$data_object = [];
		$data_shift_type = $this->timesheets_model->get_shift_type();
		$new_list_shift = [];

		if ($absolute_type == true) {
			if ($stafflist) {
				array_push($data_day_by_month, 'staffid');
				array_push($data_day_by_month, _l('staff'));
				array_push($list_data, [
					'data' => 'staffid',
					'type' => 'text',
					'readOnly' => true,
				]);
				array_push($list_data, [
					'data' => 'staff',
					'type' => 'text',
					'readOnly' => true,
				]);
			}
			for ($d = $from_day; $d <= $to_day; $d++) {
				$time = mktime(12, 0, 0, $month, $d, $month_year);

				if (date('m', $time) == $month) {
					array_push($data_time, $time);
					array_push($data_day_by_month_tk, date('D d', $time));
					array_push($data_day_by_month, date('D d', $time));
					array_push($data_set_col, ['data' => date('D d', $time), 'type' => 'text']);
					array_push($data_set_col_tk, ['data' => date('D d', $time), 'type' => 'text']);

					array_push($data_set_col_tk, ['data' => date('D d', $time), 'type' => 'text']);
					array_push($list_data, [
						'data' => date('D d', $time),
						'editor' => "chosen",
						'chosenOptions' => [
							'data' => $new_list_shift,
						],
					]);
				}
			}
			if ($stafflist) {
				$this->load->model('staff_model');
				foreach ($stafflist as $key => $value) {
					$data_staff = $this->staff_model->get($value);
					$staff_id = $data_staff->staffid;
					$staff_name = $data_staff->firstname . ' ' . $data_staff->lastname;
					$data_shift_staff = [];

					$row_data_staff = new stdClass();
					$row_data_staff->staffid = $staff_id;
					$row_data_staff->staff = $staff_name;
					foreach ($data_time as $k => $time) {
						$times = date('D d', $time);
						$date_s = date('Y-m-d', $time);
						$row_data_staff->$times = $this->timesheets_model->get_id_shift_type_by_date_and_master_id($staff_id, $date_s, $work_shift_id);
					}
					$data_object[] = $row_data_staff;
				}
			} else {
				$row_data_staff = new stdClass();
				foreach ($data_time as $k => $time) {
					$times = date('D d', $time);
					$date_s = date('Y-m-d', $time);
					$id_shift_type = '';
					$staff_id = '';
					$first_staff = $this->timesheets_model->get_first_staff_work_shift($work_shift_id);
					if ($first_staff) {
						$staff_id = $first_staff->staff_id;
					}
					$data_s = $this->timesheets_model->get_id_shift_type_by_date_and_master_id($staff_id, $date_s, $work_shift_id);
					$row_data_staff->$times = $data_s;
				}
				$data_object[] = $row_data_staff;
			}
		} else {
			$day_list = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
			if ($stafflist) {
				array_push($data_day_by_month, 'staffid');
				array_push($data_day_by_month, _l('staff'));
				array_push($list_data, [
					'data' => 'staffid',
					'type' => 'text',
					'readOnly' => true,
				]);
				array_push($list_data, [
					'data' => 'staff',
					'type' => 'text',
					'readOnly' => true,
				]);
			}
			foreach ($day_list as $key => $value) {
				array_push($data_day_by_month_tk, $value);
				array_push($data_day_by_month, $value);
				array_push($data_set_col, ['data' => $value, 'type' => 'text']);
				array_push($data_set_col_tk, ['data' => $value, 'type' => 'text']);
				array_push($list_data, [
					'data' => $value,
					'editor' => "chosen",
					'chosenOptions' => [
						'data' => $new_list_shift,
					],
				]);
			}
			if ($stafflist) {
				$this->load->model('staff_model');
				foreach ($stafflist as $key => $value) {
					$data_staff = $this->staff_model->get($value);
					$staff_id = $data_staff->staffid;
					$staff_name = $data_staff->firstname . ' ' . $data_staff->lastname;

					$data_shift_staff = [];
					$row_data_staff = new stdClass();
					$row_data_staff->staffid = $staff_id;
					$row_data_staff->staff = $staff_name;
					for ($i = 1; $i <= 7; $i++) {
						$shift_type_id = '';
						$data_shift_type = $this->timesheets_model->get_shift_type_id_by_number_day($work_shift_id, $i, $staff_id);
						if ($data_shift_type) {
							$shift_type_id = $data_shift_type->shift_id;
						}
						$day_name = $day_list[$i - 1];
						$row_data_staff->$day_name = $shift_type_id;
					}
					$data_object[] = $row_data_staff;
				}
			} else {
				$row_data_staff = new stdClass();
				for ($i = 1; $i <= 7; $i++) {
					$shift_type_id = '';
					$data_shift_type = $this->timesheets_model->get_shift_type_id_by_number_day($work_shift_id, $i);

					if ($data_shift_type) {
						$shift_type_id = $data_shift_type->shift_id;
					}
					$day_name = $day_list[$i - 1];
					$row_data_staff->$day_name = $shift_type_id;
				}
				$data_object[] = $row_data_staff;
			}
		}
		$obj = new stdClass();
		$obj->day_by_month = $data_day_by_month;
		$obj->day_by_month_tk = $data_day_by_month_tk;
		$obj->set_col = $data_set_col;
		$obj->set_col_tk = $data_set_col_tk;
		$obj->list_data = $list_data;
		$obj->data_object = $data_object;
		return $obj;
	}
	/**
	 * add allocation shiftwork
	 * @param integer $id
	 * @param view
	 */
	public function add_allocation_shiftwork($id = '')
	{
		$data['title'] = _l('new_shift');
		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $this->staff_model->get('', 'active = 1');
		$data['roles'] = $this->roles_model->get();

		$month = date('m');
		$month_year = date('Y');
		$data_hs = $this->set_col_tk(1, 8, $month, $month_year, false);
		$data['head_data'] = $data_hs->day_by_month;
		$data['list_data'] = $data_hs->list_data;
		$data['data_object'] = $data_hs->data_object;

		$data_shift_type = $this->timesheets_model->get_shift_type();
		$new_list_shift = [];
		foreach ($data_shift_type as $key => $value) {
			$start_date = $value['time_start_work'];
			$st_1 = explode(':', $start_date);
			$st_time = $st_1[0] . 'h' . $st_1[1];

			$end_date = $value['time_end_work'];
			$e_2 = explode(':', $end_date);
			$e_time = $e_2[0] . 'h' . $e_2[1];

			array_push($new_list_shift, array(
				'id' => $value['id'],
				'label' => $value['shift_type_name'] . ' (' . $st_time . ' - ' . $e_time . ')',

			));
		}
		if ($id != '') {
			$data['word_shift'] = $this->timesheets_model->get_workshiftms($id);
			$month = date('m');
			$month_year = date('Y');
			$department = $data['word_shift']->department;
			$role = $data['word_shift']->position;
			if ($data['word_shift']->staff != '') {
				$staff = explode(',', $data['word_shift']->staff);
			} else {
				$staff = '';
			}
			$from_date = $data['word_shift']->from_date;
			$to_date = $data['word_shift']->to_date;

			if ($data['word_shift']->type_shiftwork == 'repeat_periodically') {
				$data_hs = $this->set_col_tk(1, 8, $month, $month_year, false, $staff, $id);
				$data['head_data'] = $data_hs->day_by_month;
				$data['list_data'] = $data_hs->list_data;
				$data['data_object'] = $data_hs->data_object;
			}

			if ($data['word_shift']->type_shiftwork == 'by_absolute_time') {
				$start_month = 1;
				$end_month = 31;
				if ($from_date) {
					$temp = explode('-', $from_date);
					$start_month = $temp[2];
					$month = $temp[1];
					$month_year = $temp[0];
				}
				if ($to_date) {
					$temp = explode('-', $to_date);
					$end_month = $temp[2];
					$month = $temp[1];
					$month_year = $temp[0];
				}
				$data_hs = $this->set_col_tk($start_month, $end_month, $month, $month_year, true, $staff, $id);
				$data['head_data'] = $data_hs->day_by_month;
				$data['list_data'] = $data_hs->list_data;
				$data['data_object'] = $data_hs->data_object;
			}
			$data['title'] = _l('edit_shift');
		}
		$data['shift_type'] = $new_list_shift;
		$this->load->view('timekeeping/add_allocate_shiftwork', $data);
	}

	public function delete_shift_type($id)
	{
		if ($id != '') {
			$message = '';
			$result = $this->timesheets_model->delete_shift_type($id);
			if ($result == true) {
				$message = _l('deleted');
			} else {
				$message = _l('problem_deleting');
			}
			set_alert('success', $message);
			redirect(admin_url('timesheets/manage_shift_type'));
		}
	}

	/**
	 * get hanson shiftwork
	 * @return json
	 */
	function get_hanson_shiftwork()
	{
		$month = date('m');
		$month_year = date('Y');
		$department = $this->input->post('department');
		$role = $this->input->post('role');
		$staff = $this->input->post('staff');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');

		$type_shiftwork = $this->input->post('type_shiftwork');
		if ($type_shiftwork == 'repeat_periodically') {
			$data_hs = $this->set_col_tk(1, 8, $month, $month_year, false, $staff);
			echo json_encode([
				'head_data' => $data_hs->day_by_month,
				'list_data' => $data_hs->list_data,
				'data_object' => $data_hs->data_object,
			]);
		}
		if ($type_shiftwork == 'by_absolute_time') {
			$start_day = 1;
			$end_day = 31;
			if (!$this->timesheets_model->check_format_date_ymd($from_date)) {
				$from_date = to_sql_date($from_date);
			}
			if (!$this->timesheets_model->check_format_date_ymd($to_date)) {
				$to_date = to_sql_date($to_date);
			}
			if ($from_date) {
				$temp = explode('-', $from_date);
				$start_day = $temp[2];
				$month = $temp[1];
				$month_year = $temp[0];
			}
			if ($to_date) {
				$temp = explode('-', $to_date);
				$end_day = $temp[2];
			}
			$data_hs = $this->set_col_tk($start_day, $end_day, $month, $month_year, true, $staff);
			echo json_encode([
				'head_data' => $data_hs->day_by_month,
				'list_data' => $data_hs->list_data,
				'data_object' => $data_hs->data_object,
			]);
		}
		die;
	}

	function get_custom_type_shiftwork()
	{
		$department = $this->input->post('department');
		$role = $this->input->post('role');
		$staff = $this->input->post('staff');
	}
	/**
	 * shift table
	 * @return json
	 */
	function shift_table()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$this->load->model('departments_model');
				$this->load->model('roles_model');

				$query = '';

				$select = [
					'from_date',
					'to_date',
					'department',
					'position',
					'staff',
					'date_create',
					'add_from',
				];

				$where = [(($query != '') ? ' where ' . rtrim($query, ' and ') : '')];

				$aColumns = $select;
				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'work_shift';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'shift_code',
					'shift_name',
					'shift_type',
					'department',
					'position',
					'staff',
					'add_from',
					'date_create',
					'from_date',
					'to_date',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$row[] = _d($aRow['from_date']);
					$row[] = _d($aRow['to_date']);
					$department_name = '';
					if ($aRow['department'] != 0 && $aRow['department'] != '') {
						$departmentid = explode(',', $aRow['department']);
						foreach ($departmentid as $key => $value) {
							$data_department = $this->departments_model->get($value);
							if ($data_department) {
								$department_name .= $data_department->name . ', ';
							}
						}
					}
					if ($department_name != '') {
						$department_name = rtrim($department_name, ', ');
					}

					$position_name = '';
					if ($aRow['position'] != 0 && $aRow['position'] != '') {
						$positionid = explode(',', $aRow['position']);
						foreach ($positionid as $key => $value) {
							$data_position = $this->roles_model->get($value);
							if ($data_position) {
								$position_name .= $data_position->name . ', ';
							}
						}
					}
					if ($position_name != '') {
						$position_name = rtrim($position_name, ', ');
					}

					$staff_name = '';
					if ($aRow['staff'] != 0 && $aRow['staff'] != '') {
						$staffid = explode(',', $aRow['staff']);
						foreach ($staffid as $key => $value) {
							$staff_name .= get_staff_full_name($value) . ', ';
						}
					}

					if ($staff_name != '') {
						$staff_name = rtrim($staff_name, ', ');
					}

					if ($department_name != '') {
						$row[] = $department_name;
					} else {
						$row[] = _l('all');
					}

					if ($position_name != '') {
						$row[] = $position_name;
					} else {
						$row[] = _l('all');
					}

					if ($staff_name != '') {
						$row[] = $staff_name;
					} else {
						$row[] = _l('all');
					}

					$row[] = _d($aRow['date_create']);

					$option = '';
					if (has_permission('table_shiftwork_management', '', 'view') || is_admin()) {
						$option .= '<a href="' . admin_url('timesheets/add_allocation_shiftwork/' . $aRow['id']) . '" class="btn btn-default btn-icon">';
						$option .= '<i class="fa fa-regular fa-pen-to-square"></i>';
						$option .= '</a>';

						$option .= '<a href="' . admin_url('timesheets/delete_shift/' . $aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
						$option .= '<i class="fa fa-remove"></i>';
						$option .= '</a>';
					}
					$row[] = $option;

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}
	/**
	 * check in timesheet
	 */
	public function check_in_ts()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$type = $data['type_check'];
			$data['send_notify'] = true;
			$re = $this->timesheets_model->check_in($data);
			if (is_numeric($re)) {
				// Error
				if ($re == 2) {
					set_alert('warning', _l('your_current_location_is_not_allowed_to_take_attendance'));
				}
				if ($re == 3) {
					set_alert('warning', _l('location_information_is_unknown'));
				}
				if ($re == 4) {
					set_alert('warning', _l('route_point_is_unknown'));
				}
				if ($re == 5) {
					set_alert('danger', _l('ts_access_denie'));
				}
				if ($re == 6) {
					set_alert('danger', _l('ts_cannot_get_client_ip_address'));
				}
			} else {
				if ($re == true) {
					if ($type == 1) {
						set_alert('success', _l('check_in_successfull'));
					} else {
						set_alert('success', _l('check_out_successfull'));
					}
				} else {
					if ($type == 1) {
						set_alert('warning', _l('check_in_not_successfull'));
					} else {
						set_alert('warning', _l('check_out_not_successfull'));
					}
				}
			}
			redirect(admin_url('timesheets/timekeeping?group=timesheets'));
		}
	}
	/**
	 * get leave setting
	 * @return json
	 */
	public function get_leave_setting()
	{
		$new_array_obj = [];
		$data = $this->input->post();
		$staffid = isset($data['staffid']) ? $data['staffid'] : '';
		$departmentid = isset($data['departmentid']) ? $data['departmentid'] : '';
		$roleid = isset($data['roleid']) ? $data['roleid'] : '';
		$query = '';
		if ($staffid != '') {
			$list = implode(',', $staffid);
			$query .= ' staffid in (' . $list . ') and';
		}
		if ($departmentid != '') {
			$list = implode(',', $departmentid);
			$query .= ' staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $list . ')) and';
		}
		if ($roleid != '') {
			$list = implode(',', $roleid);
			$query .= ' role in (' . $list . ') and';
		}
		$query = rtrim($query, ' and');

		$year = date('year');
		$start_year = get_timesheets_option('start_year_for_annual_leave_cycle');
		if ($start_year) {
			$year = $start_year;
		}

		if (isset($data['year']) && $data['year'] && $data['year'] != '') {
			$year = $data['year'];
		}

		$type_of_leave = '';
		if (isset($data['type_of_leave']) && $data['type_of_leave'] && $data['type_of_leave'] != '') {
			$type_of_leave = $data['type_of_leave'];
		}

		$data_staff = $this->timesheets_model->get_staff_query($query);
		foreach ($data_staff as $key => $value) {
			$result = $this->get_norms_of_leave_staff($value, $year, $type_of_leave);
			if ($result) {
				array_push($new_array_obj, $result);
			}
		}
		echo json_encode([
			'data' => $new_array_obj,
		]);
	}

	/**
	 * requisition report
	 * @return
	 */
	public function requisition_report()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');
				$position_filter = $this->input->post('role_filter');
				$department_filter = $this->input->post('department_filter');
				$rel_type = $this->input->post('rel_type');
				$staff_filter = $this->input->post('staff_filter');
				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				} //thang nay
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				} //Trang truoc
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				} //nam nay
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				} //năm truoc

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				} //3 thang qua
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				} //6 thang qua
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				} //12 thang qua
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				} //12 thang qua

				$select = [
					'staff_id',
					'subject',
					'start_time',
					'end_time',
					'number_of_leaving_day',
					'reason',
					'rel_type',
				];

				$query = '';
				if (has_permission('report_management', '', 'view') || is_admin()) {
					if (isset($staff_filter)) {
						$staffid_list = implode(',', $staff_filter);
						$query .= ' staff_id in (' . $staffid_list . ') and';
					}
				} else {
					$query .= ' ' . timesheet_staff_manager_query('report_management', 'staff_id', '') . ' and';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= ' staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				if (isset($months_report)) {
					if ($months_report != '') {
						$query .= ' date_format(start_time, "%Y-%m-%d") >= "' . $from_date . '" AND date_format(end_time, "%Y-%m-%d") <= "' . $to_date . '" and';
					}
				}
				if (isset($rel_type)) {
					$rel_type = implode(',', $rel_type);
					$query .= ' rel_type in (' . $rel_type . ') and';
				}

				$data_staff = $this->staff_model->get('', 'active = 1');
				if ($data_staff) {
					$staff_id_list = [];
					foreach ($data_staff as $key => $value) {
						$staff_id_list[] = $value['staffid'];
					}
					if (count($staff_id_list) > 0) {
						$query .= ' staff_id in (' . implode(',', $staff_id_list) . ') and';
					} else {
						$query .= ' staff_id in (0) and';
					}
				} else {
					$query .= ' staff_id in (0) and';
				}

				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}
				$where = [$total_query];

				$aColumns = $select;

				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'timesheets_requisition_leave';
				$join = [];

				/*get requisition approval*/
				$where_status = ' AND status = "1"';
				array_push($where, $where_status);

				if (isset($position_filter)) {
					$position_list = implode(',', $position_filter);
					$where[] = 'and ' . db_prefix() . 'timesheets_requisition_leave.staff_id IN (SELECT  staffid FROM ' . db_prefix() . 'staff where role  IN (' . $position_list . '))';
				}

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'subject',
					'start_time',
					'end_time',
					'reason',
					'number_of_leaving_day',
					'rel_type',

				]);

				$output = $result['output'];
				$rResult = $result['rResult'];

				foreach ($rResult as $aRow) {

					$row = [];
					$row[] = $aRow['staff_id'];

					$row[] = '<a data-toggle="tooltip" data-title="' . get_staff_full_name($aRow['staff_id']) . '" href="' . admin_url('profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], [
						'staff-profile-image-small',
					]) . ' ' . get_staff_full_name($aRow['staff_id']) . '</a><span class="hide">' . get_staff_full_name($aRow['staff_id']) . '</span>';

					$row[] = $aRow['subject'];
					$row[] = _d($aRow['start_time']);
					$row[] = _d($aRow['end_time']);
					$row[] = $aRow['number_of_leaving_day'];
					$row[] = $aRow['reason'];

					if ($aRow['rel_type'] == 1) {
						$row[] = '<p>' . _l('Leave') . '</p>';
					} else if ($aRow['rel_type'] == 2) {
						$row[] = '<p>' . _l('late') . '</p>';
					} else if ($aRow['rel_type'] == 3) {
						$row[] = '<p>' . _l('Go_out') . '</p>';
					} else if ($aRow['rel_type'] == 4) {
						$row[] = '<p>' . _l('Go_on_bussiness') . '</p>';
					} else if ($aRow['rel_type'] == 5) {
						$row[] = '<p>' . _l('quit_job') . '</p>';
					} else {
						$row[] = '<p>' . _l('early') . '</p>';
					}
					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * import timesheets
	 * @return
	 */
	public function import_timesheets()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		if (isset($_FILES['file_timesheets']['name']) && $_FILES['file_timesheets']['name'] != '') {
			// Get the temp file path
			$tmpFilePath = $_FILES['file_timesheets']['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				$rows = [];
				// Setup our new file path
				$newFilePath = TIMESHEETS_PATH . 'timesheets/' . $_FILES['file_timesheets']['name'];
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$xlsx = new XLSXReader_fin($newFilePath);
					$sheetNames = $xlsx->getSheetNames();
					$data = $xlsx->getSheetData($sheetNames[1]);

					for ($row = 1; $row < count($data); $row++) {
						$flag = 0;
						$rd = [];
						$rd['staffid'] = isset($data[$row][0]) ? $data[$row][0] : '';
						$rd['time_in'] = isset($data[$row][1]) ? $data[$row][1] : '';
						$rd['time_out'] = isset($data[$row][2]) ? $data[$row][2] : '';
						if ($rd['staffid'] == '' && $rd['time_in'] == '' && $rd['time_out'] == '') {
							$flag = 1;
						}
						if ($flag == 0) {
							$rows[] = $rd;
						}
					}
					$this->timesheets_model->import_timesheets($rows);
					set_alert('success', _l('import_timesheets'));
				}
			} else {
				set_alert('warning', _l('import_upload_failed'));
			}
		}
		redirect(admin_url('timesheets/timekeeping'));
	}

	/**
	 * Sets the leave.
	 */
	public function set_leave()
	{
		if ($this->input->post()) {
			$data = $this->input->post();

			$not_show_notify = 0;
			if (isset($data['not_show_notify']) && $data['not_show_notify'] == '1') {
				$not_show_notify = 1;
				unset($data['not_show_notify']);
			}

			$success = $this->timesheets_model->set_leave($data);
			if ($success > 0) {
				$message = _l('ts_updated_successfully', _l('setting'));
				if ($not_show_notify == 0) {
					set_alert('success', $message);
				}
			}
			redirect(admin_url('timesheets/setting?group=manage_leave'));
		}
	}

	/**
	 * send notifi handover recipients
	 * @return
	 */
	public function send_notifi_handover_recipients()
	{
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			if ((isset($data)) && $data != '') {
				$this->timesheets_model->send_notifi_handover_recipients($data);

				$success = 'success';
				echo json_encode([
					'success' => $success,
				]);
			}
		}
	}

	/**
	 * send notification recipient
	 * @return [type] [description]
	 */
	public function send_notification_recipient()
	{
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			if ((isset($data)) && $data != '') {
				$this->timesheets_model->send_notification_recipient($data);

				$success = 'success';
				echo json_encode([
					'success' => $success,
				]);
			}
		}
	}

	/**
	 * delete timesheets attachment file
	 * @param  int $attachment_id
	 * @return
	 */
	public function delete_timesheets_attachment_file($attachment_id)
	{
		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->timesheets_model->delete_timesheets_attachment_file($attachment_id),
		]);
	}

	/**
	 * reload shiftwork byfilter
	 * @return json
	 */
	public function reload_shiftwork_byfilter()
	{
		$data = $this->input->post();
		$query = "";
		if (isset($data["staff"])) {
			if ($data["staff"] != '') {
				$list_id = implode(',', $data["staff"]);
				$query .= 'FIND_IN_SET(staffid, "' . $list_id . '") and ';
			}
		}

		if (isset($data["department"])) {
			if ($data["department"] != '') {
				$query .= 'staffid in (select ' . db_prefix() . 'staff_departments.staffid from ' . db_prefix() . 'staff_departments where departmentid = ' . $data["department"] . ') and ';
			}
		}

		if (isset($data["role"])) {
			if ($data["role"] != '') {
				$query .= 'role = "' . $data["role"] . '" and ';
			}
		}

		if ($query != '') {
			$query = rtrim($query, ' and ');
		}

		$date = $data['month'] . '-01';
		$month = date('m', strtotime($date));
		$month_year = date('Y', strtotime($date));
		$this->load->model('staff_model');
		$list_staff_id = [];
		if (is_admin()) {
			$data_staff_list = $this->timesheets_model->get_staff_list($query != '' ? $query = 'where ' . $query . ' AND active = 1' : 'where active = 1');
			foreach ($data_staff_list as $key => $value) {
				$list_staff_id[] = $value['staffid'];
			}
		} else {
			$list_staff_id[] = get_staff_user_id();
		}
		$data_hs = $this->set_col_tk(1, 31, $month, $month_year, true, $list_staff_id);
		$data['day_by_month'] = $data_hs->day_by_month;
		$data['list_data'] = $data_hs->list_data;
		$list_date = $this->timesheets_model->get_list_date($date, date('Y-m-t', strtotime($date)));
		$data_object = [];
		foreach ($list_staff_id as $key => $value) {
			$row_data_staff = new stdClass();
			$row_data_staff->staffid = $value;
			$row_data_staff->staff = get_staff_full_name($value);
			$row_data_color = new stdClass();
			$row_data_color->staffid = '';
			$row_data_color->staff = '';
			foreach ($list_date as $kdbm => $day) {
				$shift_s = '';
				$color = '';
				$list_shift = $this->timesheets_model->get_shift_work_staff_by_date($value, $day);
				foreach ($list_shift as $ss) {
					$data_shift_type = $this->timesheets_model->get_shift_type($ss);
					if ($data_shift_type) {
						if ($color == '') {
							$color = $data_shift_type->color;
						}

						$start_date = $data_shift_type->time_start_work;
						$st_1 = explode(':', $start_date);
						$st_time = $st_1[0] . 'h' . $st_1[1];

						$end_date = $data_shift_type->time_end_work;
						$e_2 = explode(':', $end_date);
						$e_time = $e_2[0] . 'h' . $e_2[1];

						$shift_s .= $data_shift_type->shift_type_name . ' (' . $st_time . ' - ' . $e_time . ')' . "\n";
					}
				}
				$day_s = date('D d', strtotime($day));
				$row_data_staff->$day_s = $shift_s;
				$row_data_color->$day_s = $color;
			}
			$data_object[] = $row_data_staff;
			$data_color[] = $row_data_color;
		}

		$data['data_object'] = $data_object;
		$data['data_color'] = $data_color;
		echo json_encode([
			'data_object' => $data_object,
			'data_color' => $data_color,
			'day_by_month' => $data['day_by_month'],
			'list_data' => $data['list_data'],
		]);
		die;
	}
	/**
	 * advance payment go on bussiness update
	 */
	public function advance_payment_update()
	{
		if ($this->input->post()) {
			$this->load->model('expenses_model');
			$data = $this->input->post();
			$id = $data['id'];
			unset($data['id']);
			$id_expense = '';
			if (!has_permission('expenses', '', 'create')) {
				set_alert('danger', _l('access_denied'));
				redirect(admin_url('timesheets/requisition_detail/' . $id));
			} else {
				if ($data['amount_received'] != '' && $data['received_date'] != '') {
					$data_payment['amount_received'] = $data['amount_received'];
					$data_payment['received_date'] = $data['received_date'];
					unset($data['amount_received']);
					unset($data['received_date']);
					$success = $this->timesheets_model->advance_payment_update($id, $data_payment);
					$id_expense = $this->expenses_model->add($data);
				}
			}
			if (is_numeric($id_expense)) {
				set_alert('success', _l('added_successfully'));
			} else {
				set_alert('danger', _l('added_fail'));
			}
			echo json_encode([
				'url' => admin_url('timesheets/requisition_detail/' . $id),
				'expenseid' => $id_expense,
			]);
			die;
		}
	}

	public function add_expense_category()
	{
		if (!is_admin() && get_option('staff_members_create_inline_expense_categories') == '0') {
			access_denied('expenses');
		}
		if ($this->input->post()) {
			$this->load->model('expenses_model');
			$data = $this->input->post();
			$id = $data['leave_id'];
			unset($data['leave_id']);
			$id_category = $this->expenses_model->add_category($data);
			if ($id_category) {
				set_alert('success', _l('added_successfully'));
			}
			redirect(admin_url('timesheets/requisition_detail/' . $id));
		}
	}
	/**
	 * setting timekeeper
	 * @return redirect
	 */
	public function default_settings()
	{
		$data = $this->input->post();
		$success = $this->timesheets_model->default_settings($data);
		if ($success) {
			set_alert('success', _l('save_setting_success'));
		} else {
			set_alert('danger', _l('no_data_changes'));
		}
		redirect(admin_url('timesheets/setting?group=default_settings'));
	}
	/**
	 * get data attendance
	 * @return json
	 */
	public function get_data_attendance()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if ((isset($data['date']) && $data['date'] == '') || !isset($data['date'])) {
				$data['date'] = date('Y-m-d H:i:s');
			}
			$allows_updating_check_in_time = 0;
			$data_allows_updating = get_timesheets_option('allows_updating_check_in_time');
			if ($data_allows_updating) {
				$allows_updating_check_in_time = $data_allows_updating;
			}
			$data['date'] = $this->timesheets_model->format_date_time($data['date']);
			$split_date = explode(' ', $data['date']);
			$data_check_in_out = $this->timesheets_model->get_list_check_in_out($split_date[0], $data['staff_id']);
			$html_list = '';
			$type_check_in_out = '';
			foreach ($data_check_in_out as $key => $value) {
				$type_check_in_out = $value['type_check'];
				$alert_type = 'alert-success';
				$type_check = _l('checked_in_at');
				if ($value['type_check'] == 2) {
					$type_check = _l('checked_out_at');
					$alert_type = 'alert-warning';
				}
				$html_list .= '<div class="row"><div class="col-md-12"><div class="alert ' . $alert_type . '">' . $type_check . ': ' . _dt($value['date']) . '</div></div></div>';
			}
			echo json_encode([
				'html_list' => $html_list,
				'allows_updating_check_in_time' => $allows_updating_check_in_time,
				'type_check_in_out' => $type_check_in_out,
			]);
			die;
		}
	}
	/**
	 * workplace management
	 * @return view
	 */
	public function workplace_mgt()
	{
		if (!(has_permission('table_workplace_management', '', 'view_own') || has_permission('table_workplace_management', '', 'view') || is_admin())) {
			access_denied('timekeeping');
		}
		$data_attendance_by_coordinates = get_timesheets_option('allow_attendance_by_coordinates');
		if (!$data_attendance_by_coordinates) {
			access_denied();
		}
		$this->load->model('staff_model');
		$data['group'] = $this->input->get('group');
		$data['title'] = _l('workplace_mgt');
		$data['tab'][] = 'workplace_assign';
		$data['tab'][] = 'workplace';
		if ($data['group'] == '') {
			$data['group'] = 'workplace_assign';
		} elseif ($data['group'] == 'workplace') {
			$data['workplace'] = $this->timesheets_model->get_workplace();
		}
		$data['staffs'] = $this->staff_model->get('', 'active = 1');
		$this->load->view('workplace_mgt/management', $data);
	}
	/**
	 * add workplace
	 **/
	public function add_workplace()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['id'] == '') {
				$add = $this->timesheets_model->add_workplace($data);
				if ($add > 0) {
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
			} else {
				$success = $this->timesheets_model->update_workplace($data);
				if ($success == true) {
					$message = _l('ts_updated_successfully');
					set_alert('success', $message);
				}
			}
			redirect(admin_url('timesheets/workplace_mgt?group=workplace'));
		}
	}
	/**
	 * delete workplace
	 * @param  integer $id
	 */
	public function delete_workplace($id)
	{
		if (!$id) {
			redirect(admin_url('timesheets/workplace_mgt?group=workplace'));
		}
		$response = $this->timesheets_model->delete_workplace($id);
		if ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('timesheets/workplace_mgt?group=workplace'));
	}
	/**
	 * add workplace assign
	 */
	public function add_workplace_assign()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->timesheets_model->add_workplace_assign($data);
			if ($success == true) {
				$message = _l('added_successfully');
				set_alert('success', $message);
			} else {
				$message = _l('added_failed');
				set_alert('warning', $message);
			}
			redirect(admin_url('timesheets/workplace_mgt?group=workplace_assign'));
		}
	}
	/**
	 * delete workplace assign
	 * @param  integer $id
	 */
	public function delete_workplace_assign($id)
	{
		if (!$id) {
			redirect(admin_url('timesheets/workplace_mgt?group=workplace_assign'));
		}
		$response = $this->timesheets_model->delete_workplace_assign($id);
		if ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('timesheets/workplace_mgt?group=workplace_assign'));
	}
	/**
	 * table registration leave
	 * @return
	 */
	public function table_workplace_assign()
	{
		$this->app->get_table_data(module_views_path('timesheets', 'workplace_mgt/includes/table_workplace_assign'));
	}

	/**
	 * @return view
	 */
	public function timekeeping_data()
	{
		if (!(has_permission('attendance_management', '', 'view_own') || has_permission('attendance_management', '', 'view') || is_admin())) {
			access_denied('timekeeping');
		}
		$this->load->model('staff_model');
		$data['title'] = _l('timesheets');
		$type_valid = ['AL', 'W', 'U', 'HO', 'E', 'L', 'B', 'SI', 'M', 'ME', 'NS', 'P'];
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
		$month = date('m');
		$month_year = date('Y');

		$data['check_latch_timesheet'] = $this->timesheets_model->check_latch_timesheet(date('m-Y'));

		$data['departments'] = $this->departments_model->get();
		$data['staffs_li'] = $this->staff_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['positions'] = $this->roles_model->get();

		$data['day_by_month_tk'] = [];
		$data['day_by_month_tk'][] = _l('staff_id');
		$data['day_by_month_tk'][] = _l('staff');

		$data['set_col_tk'] = [];
		$data['set_col_tk'][] = ['data' => _l('staff_id'), 'type' => 'text'];
		$data['set_col_tk'][] = ['data' => _l('staff'), 'type' => 'text', 'readOnly' => true, 'width' => 200];

		for ($d = 1; $d <= $days_in_month; $d++) {
			$time = mktime(12, 0, 0, $month, $d, $month_year);
			if (date('m', $time) == $month) {
				array_push($data['day_by_month_tk'], date('D d', $time));
				array_push($data['set_col_tk'], ['data' => date('D d', $time), 'type' => 'text']);
			}
		}

		$data['day_by_month_tk'] = json_encode($data['day_by_month_tk']);
		$data_map = [];
		$data_timekeeping_form = get_timesheets_option('timekeeping_form');
		$data_timekeeping_manually_role = get_timesheets_option('timekeeping_manually_role');
		$data['data_timekeeping_form'] = $data_timekeeping_form;
		$data['staff_row_tk'] = [];
		$staffs = $this->timesheets_model->get_staff_timekeeping_applicable_object();
		$data['staffs_setting'] = $this->staff_model->get();
		$data['staffs'] = $staffs;

		if ($data_timekeeping_form == 'timekeeping_task' && $data['check_latch_timesheet'] == false) {
			foreach ($staffs as $s) {
				$ts_date = '';
				$ts_ts = '';
				$result_tb = [];
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
				$staffsTasksWhere = [];
				if ($from_date != '' && $to_date != '') {
					$staffsTasksWhere = 'IF(duedate IS NOT NULL,((startdate <= "' . $from_date . '" and duedate >= "' . $from_date . '") or (startdate <= "' . $to_date . '" and duedate >= "' . $to_date . '") or (startdate > "' . $to_date . '" and duedate < "' . $from_date . '")), IF(datefinished IS NOT NULL,IF(status = 5 ,((startdate <= "' . $from_date . '" and date_format(datefinished, "%Y-%m-%d") >= "' . $from_date . '") or (startdate <= "' . $to_date . '" and date_format(datefinished, "%Y-%m-%d") >= "' . $to_date . '") or (startdate > "' . $to_date . '" and date_format(datefinished, "%Y-%m-%d") < "' . $from_date . '")), (startdate <= "' . $from_date . '" or (startdate > "' . $from_date . '" and startdate <= "' . $to_date . '"))),(startdate <= "' . $from_date . '" or (startdate > "' . $from_date . '" and startdate <= "' . $to_date . '"))))';
				}
				$staff_task = $this->tasks_model->get_tasks_by_staff_id($s['staffid'], $staffsTasksWhere);
				$list_in_out = [];
				foreach ($staff_task as $key_task => $task) {
					$list_taskstimers = $this->timesheets_model->get_taskstimers($task['id'], $s['staffid']);
					foreach ($list_taskstimers as $taskstimers) {
						$list_date = $this->timesheets_model->get_list_date(date('Y-m-d', $taskstimers['start_time']), date('Y-m-d', $taskstimers['end_time']));
						foreach ($list_date as $curent_date) {
							$start_work_time = "";
							$end_work_time = "";
							$data_shift_list = $this->timesheets_model->get_shift_work_staff_by_date($s['staffid'], $curent_date);

							foreach ($data_shift_list as $ss) {
								$data_shift_type = $this->timesheets_model->get_shift_type($ss);
								if ($start_work_time == "" || strtotime($start_work_time) > strtotime($curent_date . ' ' . $data_shift_type->time_start_work . ':00')) {
									$start_work_time = $curent_date . ' ' . $data_shift_type->time_start_work . ':00';
								}
								if ($end_work_time == "" || strtotime($end_work_time) < strtotime($curent_date . ' ' . $data_shift_type->time_end_work . ':00')) {
									$end_work_time = $curent_date . ' ' . $data_shift_type->time_end_work . ':00';
								}
							}

							if (strtotime($start_work_time) < strtotime($curent_date . ' ' . date('H:i:s', $taskstimers['start_time']))) {
								$start_work_time = $curent_date . ' ' . date('H:i:s', $taskstimers['start_time']);
							}
							if (strtotime($end_work_time) > strtotime($curent_date . ' ' . date('H:i:s', $taskstimers['end_time'])) && strtotime(date('Y-m-d', $taskstimers['end_time'])) == strtotime($curent_date)) {
								$end_work_time = $curent_date . ' ' . date('H:i:s', $taskstimers['end_time']);
							}
							if (strtotime($from_date) <= strtotime(date('Y-m-d', strtotime($start_work_time))) && strtotime($to_date) >= strtotime(date('Y-m-d', strtotime($start_work_time)))) {
								if (isset($list_in_out[date('Y-m-d', strtotime($start_work_time))]['in'])) {
									if (strtotime($list_in_out[date('Y-m-d', strtotime($start_work_time))]['in']) > strtotime($start_work_time)) {
										$list_in_out[date('Y-m-d', strtotime($start_work_time))]['in'] = $start_work_time;
									}
								} else {
									$list_in_out[date('Y-m-d', strtotime($start_work_time))]['in'] = $start_work_time;
								}

								if (isset($list_in_out[date('Y-m-d', strtotime($start_work_time))]['out'])) {
									if (strtotime($list_in_out[date('Y-m-d', strtotime($start_work_time))]['out']) < strtotime($start_work_time)) {
										$list_in_out[date('Y-m-d', strtotime($start_work_time))]['out'] = $start_work_time;
									}
								} else {
									$list_in_out[date('Y-m-d', strtotime($start_work_time))]['out'] = $start_work_time;
								}
							}

							if (strtotime($from_date) <= strtotime(date('Y-m-d', strtotime($end_work_time))) && strtotime($to_date) >= strtotime(date('Y-m-d', strtotime($end_work_time)))) {
								if (isset($list_in_out[date('Y-m-d', strtotime($end_work_time))]['in'])) {
									if (strtotime($list_in_out[date('Y-m-d', strtotime($end_work_time))]['in']) > strtotime($end_work_time)) {
										$list_in_out[date('Y-m-d', strtotime($end_work_time))]['in'] = $end_work_time;
									}
								} else {
									$list_in_out[date('Y-m-d', strtotime($end_work_time))]['in'] = $end_work_time;
								}

								if (isset($list_in_out[date('Y-m-d', strtotime($end_work_time))]['out'])) {
									if (strtotime($list_in_out[date('Y-m-d', strtotime($end_work_time))]['out']) < strtotime($end_work_time)) {
										$list_in_out[date('Y-m-d', strtotime($end_work_time))]['out'] = $end_work_time;
									}
								} else {
									$list_in_out[date('Y-m-d', strtotime($end_work_time))]['out'] = $end_work_time;
								}
							}
						}
					}
				}
				foreach ($list_in_out as $date_ => $in_out) {
					$vl = $this->timesheets_model->get_data_insert_timesheets($s['staffid'], $in_out['in'], $in_out['out']);
					if (!isset($data_map[$s['staffid']][$date_]['ts'])) {
						$data_map[$s['staffid']][$date_]['date'] = date('D d', strtotime($date_));
						$data_map[$s['staffid']][$date_]['ts'] = '';
					}
					if ($vl['late'] > 0) {
						$data_map[$s['staffid']][$date_]['ts'] .= 'L:' . $vl['late'] . '; ';
					}
					if ($vl['early'] > 0) {
						$data_map[$s['staffid']][$date_]['ts'] .= 'E:' . $vl['early'] . '; ';
					}
					if ($vl['work'] > 0) {
						$data_map[$s['staffid']][$date_]['ts'] .= 'W:' . $vl['work'] . '; ';
					}
					$data_map[$s['staffid']][$date_]['ts'] = rtrim($data_map[$s['staffid']][$date_]['ts'], '; ');
				}

				if (isset($data_map[$s['staffid']])) {
					foreach ($data_map[$s['staffid']] as $key => $value) {
						$ts_date = $data_map[$s['staffid']][$key]['date'];
						$ts_ts = $data_map[$s['staffid']][$key]['ts'];
						$result_tb[] = [$ts_date => $ts_ts];
					}
				}

				$dt_ts = [];
				$dt_ts = [_l('staff_id') => $s['staffid'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
				$note = [];
				$list_dtts = [];
				foreach ($result_tb as $key => $rs) {
					foreach ($rs as $day => $val) {
						$list_dtts[$day] = $val;
					}
				}
				$list_date = $this->timesheets_model->get_list_date(date('Y-m-01'), date('Y-m-t'));
				foreach ($list_date as $key => $value) {
					$date_s = date('D d', strtotime($value));
					$max_hour = $this->timesheets_model->get_hour_shift_staff($s['staffid'], $value);
					$check_holiday = $this->timesheets_model->check_holiday($s['staffid'], $value);
					$result_lack = '';
					if ($max_hour > 0) {
						if (!$check_holiday) {
							$ts_lack = '';
							if (isset($list_dtts[$date_s])) {
								$ts_lack = $list_dtts[$date_s] . '; ';
							}
							$total_lack = $ts_lack;
							if ($total_lack) {
								$total_lack = rtrim($total_lack, '; ');
							}
							$result_lack = $this->timesheets_model->merge_ts($total_lack, $max_hour, $type_valid);
						} else {
							if ($check_holiday->off_type == 'holiday') {
								$result_lack = "HO";
							}
							if ($check_holiday->off_type == 'event_break') {
								$result_lack = "EB";
							}
							if ($check_holiday->off_type == 'unexpected_break') {
								$result_lack = "UB";
							}
						}
					} else {
						$result_lack = 'NS';
					}
					$dt_ts[$date_s] = $result_lack;
				}
				array_push($data['staff_row_tk'], $dt_ts);
			}
		} elseif ($data_timekeeping_form == 'timekeeping_manually' && $data['check_latch_timesheet'] == false) {

			$data_ts = $this->timesheets_model->get_timesheets_ts_by_month(date('m'), date('Y'));

			foreach ($data_ts as $ts) {
				$staff_info = array();
				$staff_info['date'] = date('D d', strtotime($ts['date_work']));
				$ts_type = $this->timesheets_model->get_ts_by_date_and_staff($ts['date_work'], $ts['staff_id']);

				if (count($ts_type) <= 1) {
					if ($ts['value'] > 0) {
						$staff_info['ts'] = $ts['type'] . ':' . $ts['value'];
					} else {
						$staff_info['ts'] = '';
					}
				} else {
					$str = '';
					foreach ($ts_type as $tp) {
						if ($tp['value'] > 0) {
							if ($tp['type'] == 'HO' || $tp['type'] == 'M') {
								if ($str == '') {
									$str .= $tp['type'];
								} else {
									$str .= "; " . $tp['type'];
								}
							} else {
								if ($str == '') {
									$str .= $tp['type'] . ':' . round($tp['value'], 2);
								} else {
									$str .= "; " . $tp['type'] . ':' . round($tp['value'], 2);
								}
							}
						}
					}
					$staff_info['ts'] = $str;
				}
				if (!isset($data_map[$ts['staff_id']])) {
					$data_map[$ts['staff_id']] = array();
				}
				$data_map[$ts['staff_id']][$staff_info['date']] = $staff_info;
			}
			foreach ($staffs as $s) {
				$ts_date = '';
				$ts_ts = '';
				$result_tb = [];
				if (isset($data_map[$s['staffid']])) {
					foreach ($data_map[$s['staffid']] as $key => $value) {
						$ts_date = $data_map[$s['staffid']][$key]['date'];
						$ts_ts = $data_map[$s['staffid']][$key]['ts'];
						$result_tb[] = [$ts_date => $ts_ts];
					}
				}

				$dt_ts = [];
				$dt_ts = [_l('staff_id') => $s['staffid'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
				$note = [];
				$list_dtts = [];
				foreach ($result_tb as $key => $rs) {
					foreach ($rs as $day => $val) {
						$list_dtts[$day] = $val;
					}
				}
				$list_date = $this->timesheets_model->get_list_date(date('Y-m-01'), date('Y-m-t'));
				foreach ($list_date as $key => $value) {
					$date_s = date('D d', strtotime($value));
					$max_hour = $this->timesheets_model->get_hour_shift_staff($s['staffid'], $value);
					$check_holiday = $this->timesheets_model->check_holiday($s['staffid'], $value);
					$result_lack = '';
					if ($max_hour > 0) {
						if (!$check_holiday) {
							$ts_lack = '';
							if (isset($list_dtts[$date_s])) {
								$ts_lack = $list_dtts[$date_s] . '; ';
							}
							$total_lack = $ts_lack;
							if ($total_lack) {
								$total_lack = rtrim($total_lack, '; ');
							}
							$result_lack = $this->timesheets_model->merge_ts($total_lack, $max_hour, $type_valid);
						} else {
							if ($check_holiday->off_type == 'holiday') {
								$result_lack = "HO";
							}
							if ($check_holiday->off_type == 'event_break') {
								$result_lack = "EB";
							}
							if ($check_holiday->off_type == 'unexpected_break') {
								$result_lack = "UB";
							}
						}
					} else {
						$result_lack = 'NS';
					}
					$dt_ts[$date_s] = $result_lack;
				}
				array_push($data['staff_row_tk'], $dt_ts);
			}
		} else {
			$data_ts = $this->timesheets_model->get_timesheets_ts_by_month(date('m'), date('Y'));
			foreach ($data_ts as $ts) {
				$staff_info = array();
				$staff_info['date'] = date('D d', strtotime($ts['date_work']));
				$ts_type = $this->timesheets_model->get_ts_by_date_and_staff($ts['date_work'], $ts['staff_id']);
				if (count($ts_type) <= 1) {
					if ($ts['value'] > 0) {
						$staff_info['ts'] = $ts['type'] . ':' . $ts['value'];
					} else {
						$staff_info['ts'] = '';
					}
				} else {
					$str = '';
					foreach ($ts_type as $tp) {
						if ($tp['value'] > 0) {
							if ($tp['type'] == 'HO' || $tp['type'] == 'M') {
								if ($str == '') {
									$str .= $tp['type'];
								} else {
									$str .= "; " . $tp['type'];
								}
							} else {
								if ($str == '') {
									$str .= $tp['type'] . ':' . round($tp['value'], 2);
								} else {
									$str .= "; " . $tp['type'] . ':' . round($tp['value'], 2);
								}
							}
						}
					}
					$staff_info['ts'] = $str;
				}

				if (!isset($data_map[$ts['staff_id']])) {
					$data_map[$ts['staff_id']] = array();
				}
				$data_map[$ts['staff_id']][$staff_info['date']] = $staff_info;
			}

			foreach ($staffs as $s) {
				$ts_date = '';
				$ts_ts = '';
				$result_tb = [];
				if (isset($data_map[$s['staffid']])) {
					foreach ($data_map[$s['staffid']] as $key => $value) {
						$ts_date = $data_map[$s['staffid']][$key]['date'];
						$ts_ts = $data_map[$s['staffid']][$key]['ts'];
						$result_tb[] = [$ts_date => $ts_ts];
					}
				}

				$dt_ts = [];
				$dt_ts = [_l('staff_id') => $s['staffid'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
				$note = [];
				$list_dtts = [];
				foreach ($result_tb as $key => $rs) {
					foreach ($rs as $day => $val) {
						$list_dtts[$day] = $val;
					}
				}
				$list_date = $this->timesheets_model->get_list_date(date('Y-m-01'), date('Y-m-t'));
				foreach ($list_date as $key => $value) {
					$date_s = date('D d', strtotime($value));
					$max_hour = $this->timesheets_model->get_hour_shift_staff($s['staffid'], $value);
					$check_holiday = $this->timesheets_model->check_holiday($s['staffid'], $value);
					$result_lack = '';
					if ($max_hour > 0) {
						if (!$check_holiday) {
							$ts_lack = '';
							if (isset($list_dtts[$date_s])) {
								$ts_lack = $list_dtts[$date_s] . '; ';
							}
							$total_lack = $ts_lack;
							if ($total_lack) {
								$total_lack = rtrim($total_lack, '; ');
							}
							$result_lack = $this->timesheets_model->merge_ts($total_lack, $max_hour, $type_valid);
						} else {
							if ($check_holiday->off_type == 'holiday') {
								$result_lack = "HO";
							}
							if ($check_holiday->off_type == 'event_break') {
								$result_lack = "EB";
							}
							if ($check_holiday->off_type == 'unexpected_break') {
								$result_lack = "UB";
							}
						}
					} else {
						$result_lack = 'NS';
					}
					$dt_ts[$date_s] = $result_lack;
				}
				array_push($data['staff_row_tk'], $dt_ts);
			}
		}
		$data_lack = [];
		$data['data_lack'] = $data_lack;
		$data['set_col_tk'] = json_encode($data['set_col_tk']);
		return $data;
	}
	/**
	 * timesheets task by staff
	 * @param  integer $list_in_out
	 * @param  integer $staffid
	 * @return array
	 */
	public function timesheets_task_by_staff($list_in_out, $staffid)
	{
		$data_map = [];
		foreach ($list_in_out as $date_ => $in_out) {
			$vl = $this->timesheets_model->get_data_insert_timesheets($staffid, $in_out['in'], $in_out['out']);
			if (!isset($data_map[$staffid][$date_]['ts'])) {
				$data_map[$staffid][$date_]['date'] = date('D d', strtotime($date_));
				$data_map[$staffid][$date_]['ts'] = '';
			}
			if ($vl['late'] > 0) {
				$data_map[$staffid][$date_]['ts'] .= 'L:' . $vl['late'] . '; ';
			}
			if ($vl['early'] > 0) {
				$data_map[$staffid][$date_]['ts'] .= 'E:' . $vl['early'] . '; ';
			}
			if ($vl['work'] > 0) {
				$data_map[$staffid][$date_]['ts'] .= 'W:' . $vl['work'] . '; ';
			}
			$data_map[$staffid][$date_]['ts'] = rtrim($data_map[$staffid][$date_]['ts'], '; ');
		}
		return $data_map;
	}
	/**
	 * get layout timesheets
	 * @param  string $from_date
	 * @param  string $to_date
	 * @return array
	 */
	public function get_layout_timesheets($from_date, $to_date)
	{
		$data['day_by_month_tk'] = [];
		$data['day_by_month_tk'][] = _l('staff_id');
		$data['day_by_month_tk'][] = _l('staff');

		$data['set_col_tk'] = [];
		$data['set_col_tk'][] = ['data' => _l('staff_id'), 'type' => 'text'];
		$data['set_col_tk'][] = ['data' => _l('staff'), 'type' => 'text', 'readOnly' => true, 'width' => 200];

		$list_date = $this->timesheets_model->get_list_date($from_date, $to_date);
		foreach ($list_date as $k_date => $date) {
			$time = strtotime($date);
			array_push($data['day_by_month_tk'], date('D d', $time));
			array_push($data['set_col_tk'], ['data' => date('D d', $time), 'type' => 'text']);
		}
		return $data;
	}
	function delete_mass_workplace_assign()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->timesheets_model->delete_mass_workplace_assign($data);
			if ($success == true) {
				$message = _l('ts_delete_successfully');
				set_alert('success', $message);
			} else {
				$message = _l('ts_delete_failed');
				set_alert('warning', $message);
			}
			redirect(admin_url('timesheets/workplace_mgt?group=workplace_assign'));
		}
	}
	public function route_management()
	{
		$allow_attendance_by_route = 0;
		$data_by_route = get_timesheets_option('allow_attendance_by_route');
		if ($data_by_route) {
			$allow_attendance_by_route = $data_by_route;
		}
		if ($allow_attendance_by_route != 1) {
			access_denied('timesheets');
		}
		if (!(has_permission('route_management', '', 'view_own') || has_permission('route_management', '', 'view') || is_admin())) {
			access_denied('timesheets');
		}

		$data['title'] = _l('route_management');
		$data['tab'] = 'route';
		if ($this->input->get('tab')) {
			$data['tab'] = $this->input->get('tab');
		}
		if ($data['tab'] == 'route_point') {
			$this->load->model('clients_model');
			$data['route_point'] = $this->timesheets_model->get_route_point();
			$data['client'] = $this->clients_model->get();
			$data['workplace'] = $this->timesheets_model->get_workplace();
		}
		if ($data['tab'] == 'route') {
			$this->load->model('staff_model');
			$this->load->model('departments_model');
			$data['department'] = $this->departments_model->get();
			$header = [];
			$data_object = [];
			array_push($header, _l('staffid'));
			array_push($header, _l('staff'));
			$list_date = $this->timesheets_model->get_list_date(date('Y-m-01'), date('Y-m-t'));
			foreach ($list_date as $key => $date) {
				array_push($header, _d($date));
			}

			$data['header'] = $header;
			$data['staff'] = $this->staff_model->get('', 'active = 1' . timesheet_staff_manager_query('route_management', 'staffid', 'AND'));
			$list_end_week_index = '';
			$has_data = false;
			$old_value = 0;
			foreach ($data['staff'] as $key => $staff) {
				$row_data_staff = new stdClass();
				$staffid_text = _l('staffid');
				$staff_text = _l('staff');

				$row_data_staff->$staffid_text = $staff['staffid'];
				$row_data_staff->$staff_text = $staff['firstname'] . ' ' . $staff['lastname'];
				foreach ($list_date as $key => $date) {
					$root_text_by_date = $this->timesheets_model->get_route_text($staff['staffid'], $date)->result;
					$col = _d($date);
					$row_data_staff->$col = $root_text_by_date;
					if ($old_value > $key) {
						$has_data = true;
					}
					if (($has_data == false) && (date('N', strtotime($date)) == 7)) {
						$list_end_week_index .= $key . ',';
						$old_value = $key;
					}
				}
				$data_object[] = $row_data_staff;
			}
			$data['end_week_index'] = (($list_end_week_index != '') ? rtrim($list_end_week_index, ',') : '');
			$data['route_point'] = $this->timesheets_model->get_route_point();
			$data['data_object'] = $data_object;
		}

		if ($data['tab'] == 'map') {
			$this->load->model('staff_model');
			$data['staff'] = $this->staff_model->get('', 'active = 1');
			$data['route_point'] = $this->timesheets_model->get_route_point();

			$data['coordinates_list'][] = $this->get_route_map_data();
		}
		$this->load->view('route_management/manage', $data);
	}

	public function get_route_map_data($staffid = '', $current_date = '')
	{
		if ($staffid == '') {
			$staffid = get_staff_user_id();
		}
		if ($current_date == '') {
			$current_date = date('Y-m-d');
		}
		$staff_route_list = $this->timesheets_model->get_route_by_fillter($staffid, $current_date);
		$coordinates_list = [];
		$end_weeek = [];
		foreach ($staff_route_list as $key => $row) {
			$add_staffid_name = '';
			$count_checked = 0;
			$staff_id_list = $this->timesheets_model->staff_at_same_route($staff_route_list, $staffid, $current_date);
			if ($staff_id_list) {
				foreach ($staff_id_list as $stk => $stid) {
					$valid_head_name = '';
					$valid_tail_name = '';
					$valid_check_same_route = $this->timesheets_model->check_full_check_in_out_route_point($current_date, $stid, $row['route_point_id']);
					if ($valid_check_same_route == true) {
						$count_checked++;
						$valid_head_name = '<span class="text-success">';
						$valid_tail_name = '</span>';
					}
					$add_staffid_name .= ', ' . $valid_head_name . get_staff_full_name($stid) . $valid_tail_name;
				}
				array_push($staff_id_list, $staffid);
				$staff_id_list = array_reverse($staff_id_list);
			} else {
				array_push($staff_id_list, $staffid);
			}
			$data_route = $this->timesheets_model->get_route_point($row['route_point_id']);
			if ($data_route) {
				$head_name = '';
				$tail_name = '';
				$valid_check = $this->timesheets_model->check_full_check_in_out_route_point($current_date, $staffid, $row['route_point_id']);
				if ($valid_check == true) {
					$count_checked++;
					$head_name = '<span class="text-success">';
					$tail_name = '</span>';
				}
				$related_name = "";
				if ($data_route->related_to == 1) {
					$related_name = get_company_name($data_route->related_id);
				}
				if ($data_route->related_to == 2) {
					$related_name = get_workplace_name($data_route->related_id);
				}
				$coordinates_list[] = [
					'lat' => (float) $data_route->latitude,
					'lng' => (float) $data_route->longitude,
					'name' => $data_route->name,
					'staff_name' => $head_name . get_staff_full_name($staffid) . $tail_name . '' . $add_staffid_name,
					'route_point_address' => $data_route->route_point_address,
					'take_attendance' => (count($staff_id_list) == $count_checked) ? true : false,
					'staffid' => $staff_id_list,
					'date_work' => (string) _d($current_date),
					'related_to' => $data_route->related_to,
					'related_name' => $related_name,
					'route_point_id' => $row['route_point_id'],
				];
			}
		}
		return $coordinates_list;
	}
	/**
	 * add route point
	 */
	public function add_route_point()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$add = $this->timesheets_model->add_route_point($data);
				if ($add > 0) {
					$message = _l('added_successfully', _l('route_point'));
					set_alert('success', $message);
				} else {
					$message = _l('added_failed', _l('route_point'));
					set_alert('warning', $message);
				}
				redirect(admin_url('timesheets/route_management?tab=route_point'));
			} else {
				$success = $this->timesheets_model->update_route_point($data);
				if ($success == true) {
					$message = _l('ts_updated_successfully', _l('route_point'));
					set_alert('success', $message);
				} else {
					$message = _l('updated_failed', _l('route_point'));
					set_alert('warning', $message);
				}
				redirect(admin_url('timesheets/route_management?tab=route_point'));
			}
		}
	}
	/**
	 * table route point
	 * @return view
	 */
	public function table_route_point()
	{
		$this->app->get_table_data(module_views_path('timesheets', 'route_management/table_route_point'));
	}
	/**
	 * delete shift
	 * @param int $id
	 */
	public function delete_route_point($id)
	{
		$response = $this->timesheets_model->delete_route_point($id);
		if ($response == true) {
			set_alert('success', _l('deleted') . ' ' . _l('route_point'));
		} else {
			set_alert('warning', _l('problem_deleting') . ' ' . _l('route_point'));
		}
		redirect(admin_url('timesheets/route_management?tab=route_point'));
	}
	/**
	 * table route
	 * @return view
	 */
	public function table_route()
	{
		$this->app->get_table_data(module_views_path('timesheets', 'route_management/table_route'));
	}
	/**
	 * add route
	 */
	public function add_route()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$add = $this->timesheets_model->add_route($data);
			if ($add == true) {
				$message = _l('saved_successfully', _l('route'));
				set_alert('success', $message);
			} else {
				$message = _l('saved_failed', _l('route'));
				set_alert('warning', $message);
			}
			redirect(admin_url('timesheets/route_management?tab=route'));
		}
	}
	/**
	 * add new root
	 */
	public function add_new_root()
	{
		$data['title'] = _l('route_management');
		$this->load->model('staff_model');
		$header = [];
		$data_object = [];
		array_push($header, _l('staffid'));
		array_push($header, _l('staff'));
		$list_date = $this->timesheets_model->get_list_date(date('Y-m-01'), date('Y-m-t'));
		foreach ($list_date as $key => $date) {
			array_push($header, _d($date));
		}
		$data['header'] = $header;
		$data['staff'] = $this->staff_model->get();
		foreach ($data['staff'] as $key => $staff) {
			$row_data_staff = new stdClass();
			$staffid_text = _l('staffid');
			$staff_text = _l('staff');

			$row_data_staff->$staffid_text = $staff['staffid'];
			$row_data_staff->$staff_text = $staff['lastname'] . ' ' . $staff['firstname'];
			foreach ($list_date as $key => $date) {
				$col = _d($date);
				$row_data_staff->$col = "";
			}
			$data_object[] = $row_data_staff;
		}
		$data['route_point'] = $this->timesheets_model->get_route_point();
		$data['data_object'] = $data_object;
		$this->load->view('route_management/add_new_root', $data);
	}
	/**
	 * get ui create root
	 * @return json
	 */
	public function get_ui_create_root()
	{
		$staff = $this->input->post('staff');
		$date = $this->input->post('date');
		$route_point = $this->input->post('route_point');
		$department = $this->input->post('department');
		$start_date = date('Y-m-01', strtotime($date));
		$end_date = date('Y-m-t', strtotime($date));

		$data_object = [];
		$where = '';

		$header = [];
		$data_object = [];
		$list_date = $this->timesheets_model->get_list_date($start_date, $end_date);
		array_push($header, _l('staffid'));
		array_push($header, _l('staff'));
		foreach ($list_date as $key => $date) {
			array_push($header, _d($date));
		}
		$where .= 'active = 1';
		if ($staff) {
			if (count($staff) > 0) {
				$where .= ' AND staffid in (' . implode(',', $staff) . ')';
			}
		}
		//department
		if ($department) {
			if ($where != '') {
				$where .= ' AND ';
			}
			$where .= 'staffid in (select ' . db_prefix() . 'staff_departments.staffid from ' . db_prefix() . 'staff_departments where departmentid in (' . implode(',', $department) . '))';
		}

		$data['staff'] = $this->staff_model->get('', ($where != '' ? $where . timesheet_staff_manager_query('route_management') : []));
		foreach ($data['staff'] as $key => $staff) {
			$row_data_staff = new stdClass();
			$staffid_text = _l('staffid');
			$staff_text = _l('staff');

			$row_data_staff->$staffid_text = $staff['staffid'];
			$row_data_staff->$staff_text = $staff['firstname'] . ' ' . $staff['lastname'];

			$route_fillter = false;
			$valid = true;
			if ($route_point) {
				$valid = false;
			}
			foreach ($list_date as $key => $date) {
				$route_data = $this->timesheets_model->get_route_text($staff['staffid'], $date);
				if ($route_point) {
					if (count(array_intersect($route_data->list_route_id, $route_point)) > 0) {
						$valid = true;
						$col = _d($date);
						$row_data_staff->$col = $route_data->result;
					}
				} else {
					$col = _d($date);
					$row_data_staff->$col = $route_data->result;
				}
			}
			if ($valid == true) {
				$data_object[] = $row_data_staff;
			}
		}
		echo json_encode([
			'data_object' => $data_object,
			'data_header' => $header,
		]);
		die;
	}
	/**
	 * get cordinate
	 * @return json
	 */
	public function get_coordinate()
	{
		$address = $this->input->post('address');
		$coordinate = address2geo($address);
		echo $coordinate;
		die;
	}
	/**
	 * get data relate
	 * @return json
	 */
	public function get_data_relate()
	{
		$data = $this->input->post();
		$route_point_address = '';
		$latitude = '';
		$longitude = '';
		$distance = '';
		if ($data['related_to'] == 1) {
			// Related to customer
			$this->load->model('clients_model');
			$data_customer = $this->clients_model->get($data['related_id']);
			$route_point_address = $data_customer->address .
				(($data_customer->city != '' || $data_customer->city != null) ? ', ' . $data_customer->city : '') .
				(($data_customer->state != '' || $data_customer->state != null) ? ', ' . $data_customer->state : '');
		}
		if ($data['related_to'] == 2) {
			// Related to workplace
			$data_workplace = $this->timesheets_model->get_workplace($data['related_id']);
			$latitude = $data_workplace->latitude;
			$longitude = $data_workplace->longitude;
			$distance = $data_workplace->distance;
		}

		echo json_encode([
			'route_point_address' => $route_point_address,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'distance' => $distance,
		]);
		die;
	}
	/**
	 * get data map
	 * @return json
	 */
	public function get_data_map()
	{
		$data = $this->input->post();
		$route_point_list = (isset($data['route_point']) ? $data['route_point'] : []);
		$flightPlanCoordinates = [];
		$current_date = (($data['date'] != '') ? $this->timesheets_model->format_date($data['date']) : date('Y-m-d'));
		if (isset($data['staff'])) {
			if ($data['staff'] && $current_date) {
				$staff_list = $data['staff'];
				if (!is_array($staff_list)) {
					$staff_list = [];
					$staff_list[] = $data['staff'];
				}
				foreach ($staff_list as $k_staff => $staffid) {
					$staff_route_list = $this->timesheets_model->get_route_by_fillter($staffid, $current_date);
					$coordinates_list = [];
					$allow_add_array = false;
					if (count($route_point_list) == 0) {
						$allow_add_array = true;
					} else {
						$allow_add_array = false;
					}
					foreach ($staff_route_list as $key => $row) {
						if ($allow_add_array == false) {
							if (in_array($row['route_point_id'], $route_point_list)) {
								$allow_add_array = true;
							}
						}
						$add_staffid_name = '';
						$count_checked = 0;
						$staff_id_list = $this->timesheets_model->staff_at_same_route($staff_route_list, $staffid, $current_date);
						if ($staff_id_list) {
							foreach ($staff_id_list as $stk => $stid) {
								$valid_head_name = '';
								$valid_tail_name = '';
								$valid_check_same_route = $this->timesheets_model->check_full_check_in_out_route_point($current_date, $stid, $row['route_point_id']);
								if ($valid_check_same_route == true) {
									$count_checked++;
									$valid_head_name = '<span class="text-success">';
									$valid_tail_name = '</span>';
								}
								$add_staffid_name .= ', ' . $valid_head_name . get_staff_full_name($stid) . $valid_tail_name;
							}
							array_push($staff_id_list, $staffid);
							$staff_id_list = array_reverse($staff_id_list);
						} else {
							array_push($staff_id_list, $staffid);
						}

						$data_route = $this->timesheets_model->get_route_point($row['route_point_id']);
						if ($data_route) {
							$head_name = '';
							$tail_name = '';
							$valid_check = $this->timesheets_model->check_full_check_in_out_route_point($current_date, $staffid, $row['route_point_id']);
							if ($valid_check == true) {
								$count_checked++;
								$head_name = '<span class="text-success">';
								$tail_name = '</span>';
							}
							$related_name = "";
							if ($data_route->related_to == 1) {
								$related_name = get_company_name($data_route->related_id);
							}
							if ($data_route->related_to == 2) {
								$related_name = get_workplace_name($data_route->related_id);
							}
							$coordinates_list[] = [
								'lat' => (float) $data_route->latitude,
								'lng' => (float) $data_route->longitude,
								'name' => $data_route->name,
								'staff_name' => $head_name . get_staff_full_name($staffid) . $tail_name . '' . $add_staffid_name,
								'route_point_address' => $data_route->route_point_address,
								'take_attendance' => (count($staff_id_list) == $count_checked) ? true : false,
								'staffid' => $staff_id_list,
								'date_work' => (string) _d($current_date),
								'related_to' => $data_route->related_to,
								'related_name' => $related_name,
								'route_point_id' => $row['route_point_id'],
							];
						}
					}
					if ($allow_add_array == true) {
						if (count($coordinates_list) > 0) {
							$flightPlanCoordinates[] = $coordinates_list;
						}
					}
				}
			}
		}
		echo json_encode($flightPlanCoordinates);
		die;
	}
	/**
	 * get route point combobox
	 * @return json
	 */
	public function get_route_point_combobox()
	{
		$data = $this->input->post();
		$latitude = $data['lat'];
		$longitude = $data['lng'];
		$staff = '';
		$date = '';
		if (!isset($data['staff']) || ($data['staff'] == '')) {
			$staff = get_staff_user_id();
		} else {
			$staff = $data['staff'];
		}
		if (!isset($data['date']) || ($data['date'] == '')) {
			$date = date('Y-m-d');
		} else {
			$date = date('Y-m-d', strtotime($this->timesheets_model->format_date_time($data['date'])));
		}
		$list_option = '';
		$point_id = '';
		$data_setting_rooute = get_timesheets_option('allow_attendance_by_route');
		if ($data_setting_rooute && $data_setting_rooute == 1) {
			if ($staff != '' && $date != '') {
				$obj = $this->timesheets_model->get_next_point($staff, $date, $latitude, $longitude);
				$point_id = $obj->id;
				$data_route = $this->timesheets_model->get_route_by_fillter($staff, $date);
				foreach ($data_route as $key => $val) {
					$route = $this->timesheets_model->get_route_point($val['route_point_id']);
					if ($route) {
						$route_point_id = $route->id;
						$list_option .= '<option value="' . $route_point_id . '" ' . (($point_id == $route_point_id) ? 'selected' : '') . '>' . $route->name . '</option>';
						if ($obj->type == 'order') {
							if ($point_id == $route_point_id) {
								break;
							}
						}
					}
				}
			}
		}
		echo json_encode([
			'point_id' => $point_id,
			'option' => $list_option,
		]);
		die;
	}
	/**
	 * check route point name
	 * @return json
	 */
	public function check_route_point_name()
	{
		$data = $this->input->post();
		$exist = false;
		$message = "";
		if ($data) {
			$name = $data['name'];
			$id = $data['id'];
			$exist = $this->timesheets_model->check_exist_route_point_name($name, $id);
			if ($exist == true) {
				$message = _l('the_name_of_the_route_point_was_duplicated');
			}
		}
		echo json_encode([
			'result' => $exist,
			'message' => $message,
		]);
		die;
	}
	/**
	 * get default lat long
	 * @return json
	 */
	public function get_default_lat_long()
	{
		$latitude = 40.90011966771429;
		$longitude = -74.10928986604924;
		$data = $this->timesheets_model->get_route_point();
		if ($data) {
			$latitude = (float) $data[0]['latitude'];
			$longitude = (float) $data[0]['longitude'];
		}
		echo json_encode([
			'latitude' => $latitude,
			'longitude' => $longitude,
		]);
		die;
	}
	/**
	 * get check in out history
	 * @return json
	 */
	public function get_check_in_out_history()
	{
		$data = $this->input->post();
		$staffid = explode(',', $data['list_staffid']);
		$content = '';
		$list_option_staff = '';
		foreach ($staffid as $key => $id) {
			$selected = '';
			if ($key == 0) {
				$selected = 'selected';
			}
			$list_option_staff .= '<option value="' . $id . '" ' . $selected . '>' . get_staff_full_name($id) . '</option>';
		}
		$data_check_in_out = $this->timesheets_model->get_check_in_out_by_route_point($staffid[0], $this->timesheets_model->format_date($data['date']), $data['route_point_id']);
		$content = '';
		foreach ($data_check_in_out as $key => $value) {
			$alert_type = 'alert-success';
			$type_check_in_out = $value['type_check'];
			$type_check = _l('checked_in_at');
			if ($value['type_check'] == 2) {
				$type_check = _l('checked_out_at');
				$alert_type = 'alert-warning';
			}
			$content .= '<div class="col-md-12"><div class="alert ' . $alert_type . '">' . $type_check . ': ' . _dt($value['date']) . '</div></div>';
		}
		echo json_encode([
			'staff_option_list' => $list_option_staff,
			'content' => $content,
		]);
	}

	/**
	 * export attendance excel
	 * @return json
	 */

	public function export_attendance_excel()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		if (!$this->input->post()) {
			return;
		}

		// 1) Filters
		$month_filter      = $this->input->post('month');
		$department_filter = $this->input->post('department');
		$role_filter       = $this->input->post('role');
		$staff_filter      = $this->input->post('staff');

		// 2) Default staff list
		if (empty($staff_filter)) {
			$staff_filter = array_column(
				$this->timesheets_model->get_staff_timekeeping_applicable_object(),
				'staffid'
			);
		}

		// 3) Fetch data
		$list = $this->timesheets_model->get_data_attendance_export(
			$month_filter,
			$department_filter,
			$role_filter,
			$staff_filter
		);

		// 4) Parse month
		$dt = DateTime::createFromFormat('Y-m', $month_filter);
		if (!$dt) {
			$parts = explode('-', $month_filter);
			if (count($parts) === 2) {
				list($m, $y) = $parts;
				$dt = DateTime::createFromFormat('!m-Y', "{$m}-{$y}");
			}
		}
		if (!$dt) {
			$dt = new DateTime();
		}
		$month         = (int)$dt->format('m');
		$month_year    = (int)$dt->format('Y');
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $month_year);

		// 5) Lookup maps
		$emp_code_map            = $this->build_map('Staff', 'staff_identifi',   $this->timesheets_model->get_emp_code());
		$emp_position_map        = $this->build_map('Staff', 'job_position',     $this->timesheets_model->get_emp_position());
		$emp_date_of_joining_map = $this->build_map('Staff', 'joining_date',     $this->timesheets_model->get_emp_date_of_joining());
		$emp_active_status_map   = $this->build_map('Staff', 'active',           $this->timesheets_model->get_emp_active_status());

		// 6) Build header
		$header = [
			'S.NO',
			'Status',
			"Employee's Name as per Aadhar",
			'Emp. Code',
			'Designation',
			'SITE',
			'DOJ'
		];
		for ($d = 1; $d <= $days_in_month; $d++) {
			$time = mktime(12, 0, 0, $month, $d, $month_year);
			if ((int)date('m', $time) === $month) {
				$header[] = date('D d', $time);
			}
		}
		// Append only the TOTAL columns
		$header = array_merge($header, [
			'TOTAL "P"',
			'TOTAL "L"',
			'TOTAL S/L',
			'TOTAL OW',
			'TOTAL CL',
			'TOTAL OFF',
			'TOTAL "H"',
			'TOTAL C/OFF',
			'TOTAL H/F',
			'TOTAL W/H',
			'TOTAL N/A',
			'TOTAL'
		]);

		// Calculate indices
		$numTotals       = 12;
		$totalStartIndex = count($header) - $numTotals;
		$dayStartIndex   = 7;
		$dayCount        = $totalStartIndex - $dayStartIndex;

		// 7) Writer setup
		$writer = new XLSXWriter();

		// Company title
		$company = get_company_name_for_attendance();
		$writer->writeSheetRow('Sheet1', [$company], [
			'height'    => 30,
			'font-size' => 16,
			'font'      => 'Calibri',
			'bold'      => true,
			'halign'    => 'center',
		]);
		$writer->markMergedCell('Sheet1', 0, 0, 0, count($header) - 1);

		// Month title
		$month_name = strtoupper($dt->format('F'));
		$title      = "STAFF ATTENDANCE FOR THE MONTH OF {$month_name} - {$month_year}";
		$writer->writeSheetRow('Sheet1', [$title], [
			'height'    => 25,
			'font-size' => 14,
			'font'      => 'Calibri',
			'bold'      => true,
			'halign'    => 'center',
		]);
		$writer->markMergedCell('Sheet1', 1, 0, 1, count($header) - 1);

		// Single color for day columns
		$dayColor = '#DDEBF7';

		// Define styles
		$defaultHeaderStyle = [
			'fill'         => '#C65911',
			'font-style'   => 'bold',
			'color'        => '#FFFFFF',
			'border'       => 'left,right,top,bottom',
			'border-color' => '#FFFFFF',
			'height'       => 25,
			'font-size'    => 13,
			'font'         => 'Calibri',
			'halign'       => 'center',
			'valign'       => 'center',
		];
		$greenHeaderStyle  = [
			'fill'         => '#C6EFCE',
			'font-style'   => 'bold',
			'color'        => '#000000',
			'border'       => 'left,right,top,bottom',
			'border-color' => '#000000',
			'font-size'    => 13,
			'font'         => 'Calibri',
			'halign'       => 'center',
			'valign'       => 'center',
		];
		$dayHeaderStyle   = [
			'fill'         => $dayColor,
			'font-style'   => 'bold',
			'color'        => '#000000',
			'border'       => 'left,right,top,bottom',
			'border-color' => '#000000',
			'font-size'    => 13,
			'font'         => 'Calibri',
			'halign'       => 'center',
			'valign'       => 'center',
		];

		// Build header style map
		$headerStyleMap = [];
		foreach ($header as $i => $_h) {
			if ($i < $dayStartIndex) {
				$headerStyleMap[$i] = $defaultHeaderStyle;
			} elseif ($i < $totalStartIndex) {
				$headerStyleMap[$i] = $dayHeaderStyle;
			} else {
				$headerStyleMap[$i] = $greenHeaderStyle;
			}
		}
		$writer->writeSheetRow('Sheet1', $header, $headerStyleMap);

		// Data row styles
		$defaultOdd  = ['fill' => '#F8CBAD', 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font' => 'Calibri', 'font-size' => 12];
		$defaultEven = ['fill' => '#FCE4D6', 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font' => 'Calibri', 'font-size' => 12];
		$dayDataStyle = [
			'fill'         => $dayColor,
			'border'       => 'left,right,top,bottom',
			'border-color' => '#FFFFFF',
			'font-size'    => 12,
			'font'         => 'Calibri',
			'halign'       => 'center',
			'valign'       => 'center',
		];
		$greenData    = [
			'fill'         => '#C6EFCE',
			'border'       => 'left,right,top,bottom',
			'border-color' => '#000000',
			'font-size'    => 12,
			'font'         => 'Calibri',
			'halign'       => 'center',
			'valign'       => 'center',
		];

		// 8) Data rows
		$sr_no = 1;
		foreach ($list as $idx => $row) {
			$staff_name = trim($row['Staff'] ?? '');
			if (in_array($staff_name, ['Admin N360', 'Trial Demo ID'], true)) {
				continue;
			}
			$status = ($emp_active_status_map[$staff_name] ?? 0) == 1 ? 'CONTINUING' : 'DISCONTINUED';

			$out = [];
			$out[] = $sr_no++;
			$out[] = $status;
			$out[] = $staff_name;
			$out[] = $emp_code_map[$staff_name]     ?? '';
			$out[] = $emp_position_map[$staff_name] ?? '';
			$out[] = 'Jamnagar';
			$doj_raw = $emp_date_of_joining_map[$staff_name] ?? '';
			$out[] = $doj_raw ? date('d M, Y', strtotime($doj_raw)) : '';

			$counters = array_fill_keys(['P', 'L', 'SL', 'OW', 'CL', 'OFF', 'H', 'COFF', 'HF', 'WH', 'NA'], 0);
			foreach (array_slice($header, $dayStartIndex, $dayCount) as $col) {
				$val = $row[$col] ?? '';
				switch (true) {
					case strpos($val, 'P') === 0:
						$val = 'P';
						$counters['P']++;
						break;
					case $val === '':
						$val = 'L';
						$counters['L']++;
						break;
					case $val === 'OFF':
						$counters['OFF']++;
						break;
					case $val === 'H':
						$counters['H']++;
						break;
					case $val === 'C/OFF':
						$counters['COFF']++;
						break;
					case $val === 'H/F':
						$counters['HF']++;
						break;
					case $val === 'W/H':
						$counters['WH']++;
						break;
					case $val === 'N/A':
						$counters['NA']++;
						break;
					case $val === 'OW':
						$counters['OW']++;
						break;
					case $val === 'CL':
						$counters['CL']++;
						break;
				}
				$out[] = $val;
			}
			$totals = array_merge(
				array_values($counters),
				[array_sum($counters)]
			);
			$out = array_merge($out, $totals);

			// Build style map for each cell
			$rowStyleMap = [];
			foreach ($out as $i => $_val) {
				if ($i < $dayStartIndex) {
					$rowStyleMap[$i] = ($idx % 2) ? $defaultEven : $defaultOdd;
				} elseif ($i < $totalStartIndex) {
					$rowStyleMap[$i] = $dayDataStyle;
				} else {
					$rowStyleMap[$i] = $greenData;
				}
			}

			$writer->writeSheetRow('Sheet1', $out, $rowStyleMap);
		}

		// ── 9) LEGENDS + NOTE ──
		$writer->writeSheetRow('Sheet1', []);
		$legends = [
			['HO', 'Public Holiday'],
			['P', 'Present'],
			['L', 'On Leave'],
			['OFF', 'Sunday Off'],
			['H/F', 'Half Day'],
			['SL', 'Sick Leave'],
			['ML', 'Maternity Leave'],
			['W/H', 'Work From Home'],
			['C/OFF', 'Compensatory Off'],
			['N/A', 'Not Applicable']
		];
		$legendStyle = [
			'font'         => 'Calibri',
			'halign'       => 'center',
			'valign'       => 'center',
			'border'       => 'left,right,top,bottom',
			'border_style' => 'medium',
			'border_color' => '000000',
		];
		foreach ($legends as $lg) {
			$writer->writeSheetRow('Sheet1', $lg, $legendStyle);
		}

		// NOTE
		$month_filter_for_note = date('m-Y', strtotime($month_filter));
		$notes = $this->timesheets_model->get_notes($month_filter_for_note);
		$noteText = $notes['note'] ?? '';
		if ($noteText) {
			$writer->writeSheetRow('Sheet1', []);
			$noteStyle = [
				'font'       => 'Calibri',
				'font-style' => 'italic',
				'halign'     => 'center',
				'valign'     => 'center',
			];
			$noteRow = $writer->countSheetRows('Sheet1');
			$writer->markMergedCell('Sheet1', $noteRow, 0, $noteRow, 3);
			$writer->writeSheetRow('Sheet1', ['NOTE: ' . $noteText], $noteStyle);
		}

		// 10) Cleanup & save
		foreach (glob(TIMESHEETS_PATH_EXPORT_FILE . '*') as $f) {
			is_file($f) && @unlink($f);
		}
		$filename = 'attendance_' . str_replace('-', '_', $month_filter) . '.xlsx';
		$writer->writeToFile(TIMESHEETS_PATH_EXPORT_FILE . $filename);

		echo json_encode([
			'site_url' => site_url(),
			'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
		]);
		exit;
	}


	/**
	 * Helper to build [ key => value ] map from a result set
	 */
	private function build_map($keyCol, $valCol, array $rows)
	{
		$map = [];
		foreach ($rows as $r) {
			$map[trim($r[$keyCol])] = $r[$valCol];
		}
		return $map;
	}

	/**
	 * history check in out report
	 * @return
	 */
	public function history_check_in_out_report()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');

				$staff_fillter = $this->input->post("staff_2_fillter");
				$department_fillter = $this->input->post("department_2_fillter");
				$roles_fillter = $this->input->post("roles_2_fillter");

				$workplace_fillter = $this->input->post("workplace_2_fillter");
				$route_point_fillter = $this->input->post("route_point_2_fillter");
				$word_shift_fillter = $this->input->post("word_shift_2_fillter");
				$type_fillter = $this->input->post("type_2_fillter");

				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				} //thang nay
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				} //Trang truoc
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				} //nam nay
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				} //năm truoc

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				} //3 thang qua
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				} //6 thang qua
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				} //12 thang qua
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				} //12 thang qua

				$select = [
					'staff_id',
					'date',
					'type_check',
					'id',
					'workplace_id',
					'route_point_id',
				];

				$query = '';

				if (has_permission('report_management', '', 'view') || is_admin()) {
					if (isset($staff_fillter)) {
						$staffid_list = implode(',', $staff_fillter);
						$query .= ' staff_id in (' . $staffid_list . ') and';
					}
				} else {
					$query .= ' ' . timesheet_staff_manager_query('report_management', 'staff_id', '') . ' and';
				}

				if (isset($department_fillter)) {
					$department_list = implode(',', $department_fillter);
					$query .= ' staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				if (isset($workplace_fillter)) {
					$workplace_id_list = implode(',', $workplace_fillter);
					$query .= ' workplace_id in (' . $workplace_id_list . ') and';
				}
				if (isset($route_point_fillter)) {
					$route_point_id_list = implode(',', $route_point_fillter);
					$query .= ' route_point_id in (' . $route_point_id_list . ') and';
				}
				if (isset($type_fillter)) {
					if ($type_fillter != 3) {
						$query .= ' type_check = ' . $type_fillter . ' and';
					}
				}

				if (isset($months_report)) {
					if ($months_report != '') {
						$query .= ' date_format(date, "%Y-%m-%d") between "' . $from_date . '" AND "' . $to_date . '" and';
					}
				}

				if (isset($roles_fillter)) {
					$roles_id_list = implode(',', $roles_fillter);
					$query .= ' staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff where role IN (' . $roles_id_list . ')) and';
				}

				$data_staff = $this->staff_model->get('', 'active = 1');
				if ($data_staff) {
					$staff_id_list = [];
					foreach ($data_staff as $key => $value) {
						$staff_id_list[] = $value['staffid'];
					}
					if (count($staff_id_list) > 0) {
						$query .= ' staff_id in (' . implode(',', $staff_id_list) . ') and';
					} else {
						$query .= ' staff_id in (0) and';
					}
				} else {
					$query .= ' staff_id in (0) and';
				}
				/*get requisition approval*/
				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				$where = [$total_query];

				$aColumns = $select;

				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'check_in_out';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'staff_id',
					'date',
					'type_check',
					'workplace_id',
					'route_point_id',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];

				foreach ($rResult as $aRow) {
					$allow_add = true;
					$row = [];
					$row[] = get_staff_full_name($aRow['staff_id']);
					$row[] = _dt($aRow['date']);
					$type_check = '';
					if ($aRow['type_check'] == 1) {
						$type_check = '<p>' . _l('check_in') . '</p>';
					} else if ($aRow['type_check'] == 2) {
						$type_check = '<p>' . _l('check_out') . '</p>';
					}
					$row[] = $type_check;
					$shift_name = '';
					// Shift
					$list_shift = $this->timesheets_model->get_shift_work_staff_by_date($aRow['staff_id'], date('Y-m-d', strtotime($aRow['date'])));
					if (isset($word_shift_fillter) && $word_shift_fillter != '') {
						if (count(array_intersect($list_shift, $word_shift_fillter)) == 0) {
							$allow_add = false;
						}
					}
					$shift_s = '';
					foreach ($list_shift as $ss) {
						$data_shift_type = $this->timesheets_model->get_shift_type($ss);
						if ($data_shift_type) {
							$shift_s .= $data_shift_type->shift_type_name . "\n";
						}
					}
					// End shift
					$row[] = $shift_s;

					// Workplace
					$workplace_name = '';
					if ($aRow['workplace_id'] && $aRow['workplace_id'] != '' && $aRow['workplace_id'] != 0) {
						$datawplace = $this->timesheets_model->get_workplace($aRow['workplace_id']);
						if ($datawplace) {
							$workplace_name = $datawplace->name;
						}
					}
					// End workplace
					$row[] = $workplace_name;

					// Route
					$route_name = '';
					if ($aRow['route_point_id'] && $aRow['route_point_id'] != '' && $aRow['route_point_id'] != 0) {
						$route_data = $this->timesheets_model->get_route_point($aRow['route_point_id']);
						if ($route_data) {
							$route_name = $route_data->name;
						}
					}
					// End route
					$row[] = $route_name;
					if ($allow_add == true) {
						$output['aaData'][] = $row;
					}
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * check in out progress according to the route report
	 * @return
	 */
	public function check_in_out_progress_according_to_the_route_report()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$month = $this->input->post('months_2_report');
				$year = $this->input->post('year_requisition');
				$staff_fillter = $this->input->post("staff_2_fillter");
				$department_fillter = $this->input->post("department_2_fillter");
				$role_fillter = $this->input->post("roles_2_fillter");
				$route_point_fillter = $this->input->post("route_point_2_fillter");

				$select = [];
				$columns = [];
				$select[] = 'staffid';
				$columns[] = 'staff';
				$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

				for ($d = 1; $d <= $days_in_month; $d++) {
					$time = mktime(12, 0, 0, $month, $d, (int) $year);
					if (date('m', $time) == $month) {
						$select[] = 'staffid';
						$columns[] = $year . '-' . $month . '-' . (strlen($d) == 1 ? '0' . $d : $d);
					}
				}
				$query = '';

				if (has_permission('report_management', '', 'view') || is_admin()) {
					if (isset($staff_fillter)) {
						$staffid_list = implode(',', $staff_fillter);
						$query .= ' staffid in (' . $staffid_list . ') and';
					}
				} else {
					$query .= ' ' . timesheet_staff_manager_query('report_management', 'staffid', '') . ' and';
				}

				if (isset($department_fillter)) {
					$department_list = implode(',', $department_fillter);
					$query .= ' staffid in (select staffid from ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				if (isset($role_fillter)) {
					$roles_id_list = implode(',', $role_fillter);
					$query .= ' role in (' . $roles_id_list . ') and';
				}

				$query .= ' staffid in (SELECT distinct(staffid) FROM ' . db_prefix() . 'timesheets_route) and active = 1 and';
				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}
				$where = [$total_query];

				$aColumns = $select;

				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'staffid',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];

				foreach ($rResult as $aRow) {
					$count_effect = 1;
					if (isset($route_point_fillter) && $route_point_fillter != '') {
						$count_effect = 0;
					}
					$row = [];
					foreach ($columns as $key => $col) {
						if ($col == 'staff') {
							$row[] = '<div class="min-width-200">' . get_staff_full_name($aRow['staffid']) . '</div>';
						} else {
							$progress = '0/0';
							$ratio = ' (0%)';
							$count_progress = 0;
							$count_progress_complete = 0;

							$staff_route_list = $this->timesheets_model->get_route_by_fillter($aRow['staffid'], $col);
							foreach ($staff_route_list as $route_key => $route) {
								if ($count_effect == 0 && in_array($route['route_point_id'], $route_point_fillter)) {
									$count_effect = 1;
								}
								$count_progress++;
								$valid_check = $this->timesheets_model->check_full_check_in_out_route_point($col, $aRow['staffid'], $route['route_point_id']);
								if ($valid_check == true) {
									$count_progress_complete++;
								}
							}
							if ($count_progress != 0) {
								$ratio = ' (' . number_format($count_progress_complete * 100 / $count_progress, 2) . '%)';
							}
							$progress = $count_progress_complete . '/' . $count_progress;

							$row[] = '<div class="min-width-100">' . $progress . $ratio . '</div>';
						}
					}

					if ($count_effect == 1) {
						$output['aaData'][] = $row;
					}
				}
				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * report by working hours
	 * @return json
	 */
	public function report_of_leave()
	{
		echo json_encode($this->timesheets_model->report_of_leave_by_month());
	}
	/**
	 * leave by department report
	 * @return json
	 */
	public function leave_by_department()
	{
		echo json_encode($this->timesheets_model->report_leave_by_department());
	}
	/**
	 * ratio check in out by workplace
	 * @return json
	 */
	public function ratio_check_in_out_by_workplace()
	{
		echo json_encode($this->timesheets_model->report_ratio_check_in_out_by_workplace());
	}
	/**
	 * get header report check in out
	 * @return json
	 */
	public function get_header_report_check_in_out($month, $year)
	{
		if (isset($month) && isset($year)) {
			if ($month != '' && $year != '') {
				$col_header = '';
				$col_footer = '';
				$list_fillter = [];
				$col_header .= '<th>' . _l('staff') . '</th>';
				$col_footer .= '<td></td>';
				$month = $month;
				$year = $year;
				$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
				$index = 0;
				for ($d = 1; $d <= 31; $d++) {
					$time = mktime(12, 0, 0, $month, $d, (int) $year);
					if (date('m', $time) == $month) {
						$col_header .= '<th>' . date('D d', $time) . '</th>';
						$col_footer .= '<td></td>';
						$index++;
						$list_fillter[] = $index;
					}
				}

				echo json_encode([
					'col_header' => $col_header,
					'list_fillter' => $list_fillter,
					'col_footer' => $col_footer,
				]);
			}
		}
	}

	/**
	 * check in out progress according to the route report
	 * @return
	 */
	public function check_in_out_progress_report()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$month = $this->input->post('months_2_report');
				$year = $this->input->post('year_requisition');
				$staff_fillter = $this->input->post("staff_2_fillter");
				$department_fillter = $this->input->post("department_2_fillter");
				$role_fillter = $this->input->post("roles_2_fillter");
				$type_fillter = $this->input->post("type_22_fillter");

				$select = [];
				$columns = [];
				$select[] = 'staffid';
				$columns[] = 'staff';
				$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

				for ($d = 1; $d <= $days_in_month; $d++) {
					$time = mktime(12, 0, 0, $month, $d, (int) $year);
					if (date('m', $time) == $month) {
						$select[] = 'staffid';
						$columns[] = $year . '-' . $month . '-' . (strlen($d) == 1 ? '0' . $d : $d);
					}
				}
				$query = '';

				if (has_permission('report_management', '', 'view') || is_admin()) {
					if (isset($staff_fillter)) {
						$staffid_list = implode(',', $staff_fillter);
						$query .= ' staffid in (' . $staffid_list . ') and';
					}
				} else {
					$query .= ' ' . timesheet_staff_manager_query('report_management', 'staffid', '') . ' and';
				}

				if (isset($department_fillter)) {
					$department_list = implode(',', $department_fillter);
					$query .= ' staffid in (select staffid from ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				if (isset($role_fillter)) {
					$roles_id_list = implode(',', $role_fillter);
					$query .= ' role in (' . $roles_id_list . ') and';
				}

				$query .= ' staffid in (SELECT distinct(staffid) FROM ' . db_prefix() . 'timesheets_route) and active = 1 and';
				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}
				$where = [$total_query];

				$aColumns = $select;

				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'staffid',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];

				foreach ($rResult as $aRow) {
					$row = [];
					foreach ($columns as $key => $col) {
						if ($col == 'staff') {
							$row[] = '<div class="min-width-200">' . get_staff_full_name($aRow['staffid']) . '</div>';
						} else {
							$in = '';
							$out = '';
							$progress = '';
							$data_check_ts = $this->timesheets_model->check_ts($aRow['staffid'], $col);
							if ($data_check_ts) {
								if ($data_check_ts->check_in != 0) {
									$in = '<strong class="text-success">I</strong>';
								}
								if ($data_check_ts->check_out != 0) {
									$out = '<strong class="text-danger">O</strong>';
								}
								if (isset($type_fillter)) {
									if ($type_fillter == 1 && $in != '') {
										$progress = $in . ' / ' . $out;
									}
									if ($type_fillter == 2 && $out != '') {
										$progress = $in . ' / ' . $out;
									}
									if ($type_fillter == 3 && ($in != '' || $out != '')) {
										$progress = $in . ' / ' . $out;
									}
									if ($type_fillter == 4 && ($in == '' && $out != '')) {
										$progress = $in . ' / ' . $out;
									}
									if ($type_fillter == 5 && ($in != '' && $out == '')) {
										$progress = $in . ' / ' . $out;
									}
									if ($type_fillter == 6 && ($in != '' && $out != '')) {
										$progress = $in . ' / ' . $out;
									}
								}
							}
							$row[] = '<div class="min-width-100">' . $progress . '</div>';
						}
					}
					$output['aaData'][] = $row;
				}
				echo json_encode($output);
				die();
			}
		}
	}
	/**
	 * get remain day of
	 * @param  integer $id
	 * @return json
	 */
	public function get_remain_day_of($id, $type_of_leave)
	{
		$html = '';
		$day_off = $this->timesheets_model->get_current_date_off($id, $type_of_leave);
		$number_day_off = $day_off->number_day_off;
		$days_off = $day_off->days_off;

		$valid_cur_date = $this->timesheets_model->get_next_shift_date($id, date('Y-m-d'));
		// $html .= '<label class="control-label">' . _l('number_of_days_off') . ': ' . $days_off . '</label><br>';
		$html .= '<label class="control-label' . ($number_day_off == 0 ? ' text-danger' : '') . '">' . _l('number_of_leave_days_allowed') . ': ' . $number_day_off . '</label>';
		$html .= '<input type="hidden" name="number_day_off" value="' . $number_day_off . '">';
		echo json_encode([
			'html' => $html,
			'valid_date' => _d($valid_cur_date),
		]);
		die;
	}
	/**
	 * calendar leave application
	 */
	public function calendar_leave_application()
	{
		$this->load->model('staff_model');
		$this->load->model('departments_model');

		if (!(has_permission('leave_management', '', 'view_own') || has_permission('leave_management', '', 'view') || is_admin())) {
			access_denied('approval_process');
		}
		$send_mail_approve = $this->session->userdata("send_mail_approve");
		if ((isset($send_mail_approve)) && $send_mail_approve != '') {
			$data['send_mail_approve'] = $send_mail_approve;
			$this->session->unset_userdata("send_mail_approve");
		}
		$data['userid'] = get_staff_user_id();
		$status_leave = $this->timesheets_model->get_number_of_days_off();
		$day_off = $this->timesheets_model->get_current_date_off($data['userid']);
		$data['days_off'] = $day_off->days_off;
		$data['number_day_off'] = $day_off->number_day_off;

		$data['data_timekeeping_form'] = get_timesheets_option('timekeeping_form');
		$data['departments'] = $this->departments_model->get();
		$data['current_date'] = date('Y-m-d H:i:s');


		$data['pro'] = $this->staff_model->get('', 'active = 1');
		$data['staff'] = $data['pro'];

		$data['google_calendar_api'] = get_option('google_calendar_api_key');
		add_calendar_assets();
		$data['title'] = _l('ts_calendar_view');
		$data['type_of_leave'] = $this->timesheets_model->get_type_of_leave();
		$dta = $this->input->post();
		$data['chose'] = (isset($dta['chose']) ? $dta['chose'] : 'all');
		$data['status_filter'] = (isset($dta['status_filter']) ? $dta['status_filter'] : []);
		$data['rel_type_filter'] = (isset($dta['rel_type_filter']) ? $dta['rel_type_filter'] : []);
		$data['department_filter'] = (isset($dta['department_filter']) ? $dta['department_filter'] : []);
		$this->load->view('leave/calendar', $data);
	}
	/**
	 * get calendar data
	 */
	public function get_calendar_data()
	{
		if ($this->input->is_ajax_request()) {
			$data = $this->input->get();
			$data_calendar = $this->timesheets_model->get_calendar_leave_data($data);
			echo json_encode($data_calendar);
			die();
		}
	}

	/**
	 * timesheet permission table
	 */
	public function timesheet_permission_table()
	{
		if ($this->input->is_ajax_request()) {

			$select = [
				'staffid',
				'CONCAT(firstname," ",lastname) as full_name',
				'firstname', //for role name
				'email',
				'phonenumber',
			];
			$where = [];
			$where[] = 'AND ' . db_prefix() . 'staff.admin != 1';

			$arr_staff_id = timesheet_get_staff_id_permissions();

			if (count($arr_staff_id) > 0) {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN (' . implode(', ', $arr_staff_id) . ')';
			} else {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN ("")';
			}

			$aColumns = $select;
			$sIndexColumn = 'staffid';
			$sTable = db_prefix() . 'staff';
			$join = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];

			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'roles.name as role_name', db_prefix() . 'staff.role']);

			$output = $result['output'];
			$rResult = $result['rResult'];

			$not_hide = '';

			foreach ($rResult as $aRow) {
				$row = [];

				$row[] = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['full_name'] . '</a>';

				$row[] = $aRow['role_name'];
				$row[] = $aRow['email'];
				$row[] = $aRow['phonenumber'];

				$options = '';

				$options = '<a href="#" onclick="permissions_update(' . $aRow['staffid'] . ', ' . $aRow['role'] . ', ' . $not_hide . '); return false" 
					class="btn btn-default btn-icon" data-toggle="sidebar-right" ><i class="fa fa-regular fa-pen-to-square"></i></a>
					
					<a href="' . admin_url('timesheets/delete_permission/' . $aRow['staffid']) . '" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';

				$row[] = $options;

				$output['aaData'][] = $row;
			}

			echo json_encode($output);
			die();
		}
	}

	/**
	 * permission modal
	 */
	public function permission_modal()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->input->post('slug') === 'update') {
			$staff_id = $this->input->post('staff_id');
			$role_id = $this->input->post('role_id');

			$data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

			if (isset($staff_id)) {
				$data['member'] = $this->staff_model->get($staff_id);
			}

			$data['roles_value'] = $this->roles_model->get();
			$data['staffs'] = timesheet_get_staff_id_not_permissions();
			$add_new = $this->input->post('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}

			$this->load->view('settings/includes/permission_modal', $data);
		}
	}

	/**
	 * staff id changed
	 * @param  integer $staff_id
	 * @return json
	 */
	public function staff_id_changed($staff_id)
	{
		$role_id = '';
		$status = 'false';

		$staff = $this->staff_model->get($staff_id);
		if ($staff) {
			$role_id = $staff->role;
			$status = 'true';
		}

		echo json_encode([
			'role_id' => $role_id,
			'status' => $status,
		]);
		die;
	}

	/**
	 * hr profile update permissions
	 * @param  string $id
	 */
	public function update_permissions($id = '')
	{
		$data = $this->input->post();

		if (!isset($id) || $id == '') {
			$id = $data['staff_id'];
		}

		if (isset($id) && $id != '') {
			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
				} else {
					if ($id != get_staff_user_id()) {
						if ($id == 1) {
							return [
								'cant_remove_main_admin' => true,
							];
						}
					} else {
						return [
							'cant_remove_yourself_from_admin' => true,
						];
					}
					$data['admin'] = 0;
				}
			}

			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'staff', [
				'role' => $data['role'],
			]);

			$response = $this->staff_model->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $id);
		} else {
			$this->load->model('roles_model');
			$role_id = $data['role'];
			unset($data['role']);
			unset($data['staff_id']);
			$data['update_staff_permissions'] = true;
			$response = $this->roles_model->update($data, $role_id);
		}

		if (is_array($response)) {
			if (isset($response['cant_remove_main_admin'])) {
				set_alert('warning', _l('staff_cant_remove_main_admin'));
			} elseif (isset($response['cant_remove_yourself_from_admin'])) {
				set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
			}
		} elseif ($response == true) {
			set_alert('success', _l('ts_updated_successfully', _l('staff_member')));
		}
		redirect(admin_url('timesheets/setting?group=permission'));
	}

	/**
	 * delete permission
	 * @param  integer $id
	 */
	public function delete_permission($id)
	{
		$response = $this->timesheets_model->delete_permission($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('department_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('timesheets/setting?group=permission'));
	}

	/**
	 * reset data
	 */
	public function reset_data()
	{
		if (!is_admin()) {
			access_denied('timesheets');
		}

		// Workplace
		$this->db->truncate(db_prefix() . 'timesheets_workplace_assign');

		// Shift
		$this->db->truncate(db_prefix() . 'work_shift');
		$this->db->truncate(db_prefix() . 'work_shift_detail_number_day');
		$this->db->truncate(db_prefix() . 'work_shift_detail');

		// Leave
		$this->db->truncate(db_prefix() . 'timesheets_requisition_leave');
		$this->db->truncate(db_prefix() . 'timesheets_approval_details');
		$this->db->truncate(db_prefix() . 'timesheets_go_bussiness_advance_payment');

		// Additional work hours
		$this->db->truncate(db_prefix() . 'timesheets_additional_timesheet');

		// Work route
		$this->db->truncate(db_prefix() . 'timesheets_route');

		// Attendance
		$this->db->truncate(db_prefix() . 'timesheets_timesheet');
		$this->db->truncate(db_prefix() . 'check_in_out');

		//delete folder leave application
		foreach (glob(TIMESHEETS_MODULE_UPLOAD_FOLDER . '/requisition_leave/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);
			if (is_dir($file)) {
				delete_dir(TIMESHEETS_MODULE_UPLOAD_FOLDER . '/requisition_leave/' . $filename);
			}
		}

		// delete file
		$this->db->where('rel_type', 'requisition');
		$this->db->delete(db_prefix() . 'files');

		echo json_encode([
			'success' => true,
			'message' => _l('ts_reset_data_successful'),
		]);
	}
	/**
	 * get setting annual leave
	 * @param  string $year
	 * @return array
	 */
	public function get_setting_annual_leave($year = '', $type_of_leave = '')
	{
		$max_row = 0;
		$new_array_obj = [];
		$this->load->model('staff_model');
		$data_staff = $this->staff_model->get('', 'active = 1');
		foreach ($data_staff as $key => $value) {
			$max_row++;
			$result = $this->get_norms_of_leave_staff($value, $year, $type_of_leave);
			if ($result) {
				array_push($new_array_obj, $result);
			}
		}
		$data['max_row'] = $max_row;
		$data['leave_of_the_year'] = json_encode($new_array_obj);
		return $data;
	}
	/**
	 * add type of leave
	 */
	public function add_type_of_leave()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$is_redirect = 0;
			if (isset($data['is_setting'])) {
				$is_redirect = 1;
				unset($data['is_setting']);
			}
			if (isset($data['is_calendar_page'])) {
				$is_redirect = 2;
				unset($data['is_calendar_page']);
			}

			$is_valid = true;
			if (!isset($data['id']) || (isset($data['id']) && $data['id'] == '')) {
				unset($data['id']);
				// Check duplicate character
				$before_check = $this->timesheets_model->check_duplicate_character_type_of_leave($data['symbol']);
				if ($before_check == true) {
					$message = _l('ts_this_character_already_exists');
					set_alert('danger', $message);
					$is_valid = false;
				}

				// Check duplicate slug
				if (isset($data['type_name'])) {
					$data['slug'] = slug_it($data['type_name']);
					$before_check = $this->timesheets_model->check_duplicate_slug_type_of_leave($data['slug']);
					if ($before_check == true) {
						$message = _l('ts_this_type_name_already_exists');
						set_alert('danger', $message);
						$is_valid = false;
					}
				}

				if ($is_valid == true) {
					$res = $this->timesheets_model->add_type_of_leave($data);
					if (is_numeric($res)) {
						$message = _l('added_successfully');
						set_alert('success', $message);
					} else {
						$message = _l('added_failed');
						set_alert('danger', $message);
					}
				}
			} else {
				// Check duplicate character
				$before_check = $this->timesheets_model->check_duplicate_character_type_of_leave($data['symbol'], $data['id']);
				if ($before_check == true) {
					$message = _l('ts_this_character_already_exists');
					set_alert('danger', $message);
					$is_valid = false;
				}

				// Check duplicate slug
				if (isset($data['type_name'])) {
					$data['slug'] = slug_it($data['type_name']);
					$before_check = $this->timesheets_model->check_duplicate_slug_type_of_leave($data['slug'], $data['id']);
					if ($before_check == true) {
						$message = _l('ts_this_type_name_already_exists');
						set_alert('danger', $message);
						$is_valid = false;
					}
				}

				if ($is_valid == true) {
					$res = $this->timesheets_model->update_type_of_leave($data);
					if ($res == true) {
						$message = _l('ts_updated_successfully');
						set_alert('success', $message);
					} else {
						$message = _l('ts_update_fail');
						set_alert('danger', $message);
					}
				}
			}
		}
		if ($is_redirect == 1) {
			redirect(admin_url('timesheets/setting?group=type_of_leave'));
		}
		if ($is_redirect == 2) {
			redirect(admin_url('timesheets/calendar_leave_application'));
		} else {
			redirect(admin_url('timesheets/requisition_manage'));
		}
	}
	/**
	 * delete type of leave
	 * @param  integer $id
	 */
	public function delete_type_of_leave($id)
	{
		$response = $this->timesheets_model->delete_type_of_leave($id);
		if ($response == true) {
			set_alert('success', _l('ts_delete_successfully'));
		} else {
			set_alert('danger', _l('ts_delete_failed'));
		}
		redirect(admin_url('timesheets/setting?group=type_of_leave'));
	}

	/**
	 * table leave
	 * @return view
	 */
	public function table_holiday()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$select = [
					'id',
					'id',
					'break_date',
					'off_reason',
					'id',
					'id',
					'id',
					'id',
					'id',
				];
				$where = [];
				$aColumns = $select;
				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'day_off';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'break_date',
					'department',
					'position',
					'repeat_by_year',
					'off_type',
					'add_from',
					'timekeeping',
					'off_reason',

				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$row[] = $aRow['id'];
					$row[] = _d($aRow['break_date']);
					$row[] = ts_htmldecode($aRow['off_reason']);
					$row[] = _l($aRow['off_type']);
					$department_name = '';
					if ($aRow['department']) {
						if ($aRow['department'] != '') {
							$list_department = explode(',', $aRow['department']);
							foreach ($list_department as $key => $value) {
								$data_department = $this->departments_model->get($value);
								if ($data_department) {
									$department_name .= $data_department->name . ', ';
								}
							}
							if ($department_name != '') {
								$department_name = rtrim($department_name, ', ');
							}
						}
					}
					$row[] = ts_htmldecode($department_name);

					$role_name = '';
					if ($aRow['position']) {
						if ($aRow['position'] != '') {
							$list_position = explode(',', $aRow['position']);
							foreach ($list_position as $key => $value) {
								$aRowata_role = $this->roles_model->get($value);
								if ($aRowata_role) {
									$role_name .= $aRowata_role->name . ', ';
								}
							}
							if ($role_name != '') {
								$role_name = rtrim($role_name, ', ');
							}
						}
					}

					$row[] = ts_htmldecode($role_name);

					$on_off = '';
					if ((int) $aRow['repeat_by_year'] == 1) {
						$on_off = _l('ts_repeat');
					}
					if ((int) $aRow['repeat_by_year'] == 0) {
						$on_off = _l('ts_do_not_repeat');
					}
					$row[] = ts_htmldecode($on_off);

					$row[] = '<a href="' . admin_url('timesheets/member/' . $aRow["add_from"]) . '">' . staff_profile_image($aRow['add_from'], ['staff-profile-image-small mright5'], 'small', ['data-toggle' => 'tooltip', 'data-title' => get_staff_full_name($aRow['add_from'])]) . '</a>';

					$option = '';
					$option = '<a href="#" onclick="edit_day_off(this,' . ts_htmldecode($aRow['id']) . '); return false" data-off_reason="' . ts_htmldecode($aRow['off_reason']) . '" data-off_type="' . ts_htmldecode($aRow['off_type']) . '" data-break_date="' . ts_htmldecode($aRow['break_date']) . '" data-timekeeping="' . ts_htmldecode($aRow['timekeeping']) . '" data-department="' . ts_htmldecode($aRow['department']) . '"
						data-repeat_by_year="' . $aRow['repeat_by_year'] . '"
						data-position="' . ts_htmldecode($aRow['position']) . '" class="btn btn-default btn-icon" data-toggle="sidebar-right" data-target=".leave_modal_update-edit-modal"><i class="fa fa-regular fa-pen-to-square"></i></a>
						<a href="' . admin_url('timesheets/delete_day_off/' . $aRow['id']) . '" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';
					$row[] = $option;
					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}


	/**
	 * set valid ip
	 */
	public function set_valid_ip()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->timesheets_model->set_valid_ip($data);
			if ($success > 0) {
				$message = _l('ts_updated_successfully', _l('setting'));
				set_alert('success', $message);
			} else {
				set_alert('danger', _l('no_data_changes'));
			}
			redirect(admin_url('timesheets/setting?group=valid_ip'));
		}
	}


	/**
	 * get setting valid ip
	 * @return array
	 */
	public function get_setting_valid_ip()
	{
		$new_array_obj = [];
		$max_row = 0;
		$data_ip = $this->timesheets_model->get_valid_ip();
		foreach ($data_ip as $key => $value) {
			$max_row++;
			array_push($new_array_obj, array('ip_address' => $value['ip']));
		}
		$data['max_row'] = $max_row;
		$data['list_ip_data'] = json_encode($new_array_obj);
		return $data;
	}
	/**
	 * get norms of leave staff
	 * @param  array $data_staff    
	 * @param  string $year          
	 * @param  string $type_of_leave 
	 * @return array                
	 */
	public function get_norms_of_leave_staff($data_staff, $year, $type_of_leave = '')
	{
		$department_name = '';
		$data_department = $this->departments_model->get_staff_departments($data_staff['staffid']);
		if ($data_department) {
			foreach ($data_department as $key => $dep) {
				$department_name .= $dep['name'] . ', ';
			}
			if ($department_name != '') {
				$department_name = rtrim($department_name, ', ');
			}
		}

		$role_name = '';
		if ($data_staff['role'] != '') {
			$data_role = $this->timesheets_model->get_role($data_staff['role']);
			if (isset($data_role)) {
				if ($data_role) {
					if (isset($data_role->name)) {
						$role_name = $data_role->name;
					}
				}
			}
		}
		$day = 0;
		$remain_day = 0;
		$data_leave = $this->timesheets_model->get_day_off($data_staff['staffid'], $year, $type_of_leave);
		if ($data_leave) {
			if ($data_leave->total != '') {
				$day = $data_leave->total;
			}
			if ($data_leave->remain != '') {
				$remain_day = $data_leave->remain;
			}
		}
		return array('staffid' => $data_staff['staffid'], 'staffcode' => $data_staff['staff_identifi'], 'staff' => $data_staff['firstname'] . ' ' . $data_staff['lastname'], 'department' => $department_name, 'role' => $role_name, 'maximum_leave_of_the_year' => $day, 'number_of_leave_days_remaining' => $remain_day);
	}

	/**
	 * api document
	 */
	public function api_document()
	{
		$data['title'] = _l('ts_api_document');
		$this->load->view('../apidoc/index.html');
	}

	/**
	 * get staff qr code
	 * @return string
	 */
	public function get_staff_qr_code()
	{
		$user_id = get_staff_user_id();
		if ($user_id) {
			$data_staff = $this->staff_model->get($user_id);
			if ($data_staff) {
				$staff = $data_staff->firstname . ' ' . $data_staff->lastname;
				$tempDir = TIMESHEETS_PATH . 'staff_qrcodes/';
				$code_content = '';
				$code_content .= '#' . _l('ts_id') . ': ' . $user_id . "\n";
				$code_content .= _l('ts_staff') . ': ' . $staff . "\n";
				$code_content .= _l('ts_department') . ': ' . ts_get_staff_department_names($user_id) . "\n";
				$code_content .= _l('ts_email') . ': ' . $data_staff->email . "\n";
				$code_content .= _l('ts_phonenumber') . ': ' . $data_staff->phonenumber . "\n";
				$code_content .= _l('ts_position') . ': ' . $data_staff->job_position . "\n";
				$file_name = $user_id . '.png';
				$image_path = $tempDir . $file_name;
				QRcode::png($code_content, $image_path, "L", 4, 4);
				$data['user_id'] = $user_id;
				$data['image_path'] = site_url($image_path);
				$html = $this->load->view('includes/modals/includes/qr_infor_content', $data, true);
				echo $html;
				die;
			}
		}
		echo '';
		die;
	}

	/**
	 * get scan qr code
	 * @return string
	 */
	public function get_scan_qr_code_content()
	{
		$data['user_id'] = get_staff_user_id();
		$html = $this->load->view('includes/modals/includes/scan_qr_content', $data, true);
		echo $html;
		die;
	}

	/**
	 * checkin from qr code
	 * @return json
	 */
	public function checkin_from_qr_code($staff_id)
	{
		$success = false;
		$message = '';
		$html_list = '';
		if (is_numeric($staff_id) && $staff_id > 0) {
			$date = date('Y-m-d H:i:s');
			$data['staff_id'] = $staff_id;
			$data['date'] = $date;
			$type_check = $this->timesheets_model->get_type_check($staff_id, $date);
			$data['type_check'] = $type_check;
			if (is_admin() || (!is_admin() && get_staff_user_id() == $staff_id)) {
				$success = $this->timesheets_model->checkin_timesheet($data);
				if ($success == true) {
					$data_check_in_out = $this->timesheets_model->get_list_check_in_out(date('Y-m-d', strtotime($date)), $staff_id);
					foreach ($data_check_in_out as $key => $value) {
						$alert_type = 'alert-success';
						$type_check_st = _l('checked_in_at');
						if ($value['type_check'] == 2) {
							$type_check_st = _l('checked_out_at');
							$alert_type = 'alert-warning';
						}
						$html_list .= '<div class="row"><div class="col-md-12"><div class="alert ' . $alert_type . '">' . $type_check_st . ': ' . _dt($value['date']) . '</div></div></div>';
					}
					if ($type_check == 1) {
						$message = _l('check_in_successfull');
					} else {
						$message = _l('check_out_successfull');
					}
				} else {
					if ($type_check == 1) {
						$message = _l('check_in_not_successfull');
					} else {
						$message = _l('check_out_not_successfull');
					}
				}
			} else {
				$message = _l('ts_permision_denied');
			}
		} else {
			$message = _l('ts_unable_to_identify_staff');
		}
		echo json_encode([
			'data_attendance' => $html_list,
			'success' => $success,
			'message' => $message
		]);
		die;
	}


	public function update_leave()
	{
		$data = $this->input->post();
		$result = $this->timesheets_model->update_leave($data);
		if ($result == true) {
			redirect(admin_url('timesheets/requisition_manage'));
		} else {
			set_alert('danger', _l('ts_update_fail'));
			redirect(admin_url('timesheets/requisition_manage'));
		}
	}
	public function add_overtime()
	{
		// 1. Get today's date
		$today = date('Y-m-d');

		// 2. Fetch today's timesheets
		$this->db->where('DATE(date_work)', $today);
		$timesheets = $this->db->get('tbltimesheets_timesheet')->result_array();

		// 3. Process each staff member
		foreach ($timesheets as $timesheet) {
			$staff_id = $timesheet['staff_id'];

			// 4. Get staff's shift information
			$this->db->select('tblshift_type.time_start_work, tblshift_type.time_end_work');
			$this->db->join('tblshift_type', 'tblshift_type.id = tblwork_shift_detail_number_day.shift_id', 'left');
			$this->db->where('tblwork_shift_detail_number_day.staff_id', $staff_id);
			$shift = $this->db->get('tblwork_shift_detail_number_day')->row_array();

			if (!$shift) {
				log_message('warning', "No shift found for staff ID: $staff_id");
				continue;
			}

			// 5. Calculate scheduled vs actual hours
			$scheduled_start = strtotime($shift['time_start_work']);
			$scheduled_end = strtotime($shift['time_end_work']);
			$scheduled_hours = ($scheduled_end - $scheduled_start) / 3600;

			$actual_hours = (float) $timesheet['value'];
			if ($scheduled_hours < $actual_hours) {
				$overtime_difference = $actual_hours - $scheduled_hours;
			} else {
				$overtime_difference = 0;
			}
			// 6. Apply only if overtime is 2.5 hours or more
			if ($overtime_difference >= 2.5) {
				$timesheets_additional_timesheet_arr = [
					'staff_id' => $staff_id,
					'additional_day' => $today,
					'time_in' => $shift['time_start_work'],
					'time_out' => $shift['time_end_work'],
					'timekeeping_value' => $overtime_difference,
					'reason' => 'Overtime',
					'status' => 0,
					'creator' => $staff_id
				];

				$this->db->insert(db_prefix() . 'timesheets_additional_timesheet', $timesheets_additional_timesheet_arr);
				$insert_id = $this->db->insert_id();

				if ($insert_id) {
					$check_proccess = $this->timesheets_model->get_approve_setting('additional_timesheets');
					if ($check_proccess) {
						$checks = $this->timesheets_model->check_choose_when_approving('additional_timesheets');
						if ($checks == 0) {
							$data_new = [
								'rel_id' => $insert_id,
								'rel_type' => 'additional_timesheets',
								'addedfrom' => $staff_id,
							];
							$success = $this->timesheets_model->send_request_approve($data_new, $staff_id);
						}
					}
				}
			}
		}
	}

	/**
	 * import xlsx attendance
	 * @return [type]
	 */
	public function import_xlsx_attendance()
	{
		$this->load->model('staff_model');
		$data_staff = $this->staff_model->get(get_staff_user_id());
		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;
			} else {
				$data['active_language'] = get_option('active_language');
			}
		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('timekeeping/import_attendance', $data);
	}

	/**
	 * import attendance excel
	 * @return [type]
	 */
	public function import_attendance_excel()
	{
		// Permission check
		if (
			!has_permission('hrp_employee', '', 'create')
			&& !has_permission('hrp_employee', '', 'edit')
			&& !is_admin()
		) {
			access_denied('hrp_employee');
		}

		// Load required libraries
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		$message = '';
		$total_rows = 0;
		$total_row_success = 0;
		$total_row_false = 0;
		$total_days_updated = 0;
		$error_rows = [];

		if ($this->input->post() && !empty($_FILES['file_csv']['tmp_name'])) {
			$this->delete_error_file_day_before();

			// Process uploaded file
			$tmpDir = TEMP_FOLDER . '/' . time() . uniqid();
			@mkdir($tmpDir, 0755, true);
			$newPath = $tmpDir . '/' . basename($_FILES['file_csv']['name']);

			if (!move_uploaded_file($_FILES['file_csv']['tmp_name'], $newPath)) {
				echo json_encode(['message' => 'Failed to upload file']);
				return;
			}

			try {
				// Read Excel data
				$xlsx = new XLSXReader_fin($newPath);
				$sheetNames = $xlsx->getSheetNames();
				$data = $xlsx->getSheetData($sheetNames[1]);
			} catch (Exception $e) {
				@unlink($newPath);
				@rmdir($tmpDir);
				echo json_encode(['message' => 'Error reading Excel file: ' . $e->getMessage()]);
				return;
			}

			@unlink($newPath);
			@rmdir($tmpDir);

			$arr_update = [];

			if (empty($data) || !isset($data[0]) || !is_array($data[0])) {
				echo json_encode(['message' => 'No data or invalid sheet.']);
				return;
			}

			$column_key = $data[0];

			// Process each employee row
			for ($r = 1; $r < count($data); $r++) {
				if (!isset($data[$r]) || !is_array($data[$r]) || count(array_filter($data[$r])) === 0) {
					continue;
				}

				$total_rows++;
				$row = $data[$r];
				$errors = '';

				// Get row data
				$staff_id_raw = $row[0] ?? '';
				$id_raw = $row[1] ?? '';
				$hr_code_raw = $row[4] ?? '';
				$month_raw = $row[3] ?? '';
				$staff_name_raw = $row[5] ?? '';

				// Validate required fields
				if (trim($staff_id_raw) === '') {
					$errors .= _l('staff_id') . ' ' . _l('not_empty') . '; ';
				}
				if (trim($month_raw) === '') {
					$errors .= _l('month') . ' ' . _l('not_empty') . '; ';
				}

				if ($errors !== '') {
					$error_rows[] = [
						$staff_id_raw,
						$id_raw,
						$hr_code_raw,
						$staff_name_raw,
						$month_raw,
						$errors
					];
					$total_row_false++;
					continue;
				}

				// Parse month
				$base_date = date('Y-m-d', strtotime($month_raw));
				if ($base_date === false) {
					$error_rows[] = [
						$staff_id_raw,
						$id_raw,
						$hr_code_raw,
						$staff_name_raw,
						$month_raw,
						_l('invalid_date_format')
					];
					$total_row_false++;
					continue;
				}

				// Get month info
				$month_start = date('Y-m-01', strtotime($base_date));
				$days_in_month = date('t', strtotime($base_date));

				// Process each day column (columns G onward)
				for ($excel_col = 6; $excel_col < count($column_key) && ($excel_col - 6) < $days_in_month; $excel_col++) {
					$day_of_month = $excel_col - 5; // Column G (6) = Day 1
					$calendar_date = date('Y-m-d', strtotime("$month_start +" . ($day_of_month - 1) . " days"));

					$cell = strtoupper(trim($row[$excel_col] ?? ''));

					// Skip only if completely empty (not even '0')
					if ($cell === '') {
						continue;
					}

					// Validate attendance codes
					// $valid_codes = ['P', 'L', 'OFF', 'N/A', 'W/H', 'H/F', '0'];
					// if (!in_array($cell, $valid_codes, true)) {
					// 	$errors .= sprintf(_l('invalid_attendance_code'), $cell, $calendar_date) . '; ';
					// 	continue;
					// }

					// Only process if not '0' (0 means no update needed)
					if ($cell !== '0') {
						$arr_update[] = [
							'staff_id' => $staff_id_raw,
							'date_work' => $calendar_date,
							'value' => '',
							'type' => $cell,
							'add_from' => get_staff_user_id(),
						];
						$total_days_updated++;
					}
				}

				if ($errors !== '') {
					$error_rows[] = [
						$staff_id_raw,
						$id_raw,
						$hr_code_raw,
						$staff_name_raw,
						$month_raw,
						$errors
					];
					$total_row_false++;
				} else {
					$total_row_success++;
				}
			}

			// Bulk update attendance data
			if (!empty($arr_update)) {
				try {
					$this->timesheets_model->import_attendance_data($arr_update);
					$message = sprintf(_l('import_successful_with_days'), count($arr_update));
				} catch (Exception $e) {
					$message = 'Error saving data: ' . $e->getMessage();
					log_message('error', $message);
				}
			}

			// Generate error file if needed
			if (!empty($error_rows)) {
				try {
					$writer = new XLSXWriter();
					$writer->writeSheetHeader('Sheet1', [
						_l('staffid') => 'string',
						_l('id') => 'string',
						_l('hr_code') => 'string',
						_l('staff_name') => 'string',
						_l('month') => 'string',
						_l('error') => 'string',
					], ['widths' => [40, 40, 40, 50, 50, 50]]);

					foreach ($error_rows as $error_row) {
						$writer->writeSheetRow('Sheet1', $error_row);
					}

					// Ensure error directory exists
					if (!file_exists(TIMESHEETS_ERROR)) {
						mkdir(TIMESHEETS_ERROR, 0755, true);
					}

					$filename = 'Import_attendance_error_' . get_staff_user_id() . '_' . time() . '.xlsx';
					$filepath = TIMESHEETS_ERROR . $filename;

					if (is_writable(dirname($filepath))) {
						$writer->writeToFile($filepath);
					} else {
						log_message('error', 'Directory not writable: ' . TIMESHEETS_ERROR);
						$filename = '';
					}
				} catch (Exception $e) {
					log_message('error', 'Error creating error file: ' . $e->getMessage());
					$filename = '';
				}
			}
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'total_days_updated' => $total_days_updated,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => $filename,
		]);
	}




	/**
	 * delete_error file day before
	 * @return [type]
	 */
	public function delete_error_file_day_before($before_day = '', $folder_name = '')
	{
		if ($before_day != '') {
			$day = $before_day;
		} else {
			$day = '7';
		}

		if ($folder_name != '') {
			$folder = $folder_name;
		} else {
			$folder = HR_PAYROLL_ERROR;
		}

		//Delete old file before 7 day
		$date = date_create(date('Y-m-d H:i:s'));
		date_sub($date, date_interval_create_from_date_string($day . " days"));
		$before_7_day = strtotime(date_format($date, "Y-m-d H:i:s"));

		foreach (glob($folder . '*') as $file) {

			$file_arr = new_explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					$file_name_arr = new_explode("_", $filename);
					$date_create_file = array_pop($file_name_arr);
					$date_create_file = new_str_replace('.xlsx', '', $date_create_file);

					if ((float) $date_create_file <= (float) $before_7_day) {
						unlink($folder . $filename);
					}
				}
			}
		}
		return true;
	}

	/**
	 * create attendance sample file
	 * @return [type]
	 */
	public function create_attendance_sample_file()
	{
		// Load models
		$this->load->model('staff_model');
		// no more departments_model, since we removed staff_departments

		$month_attendance = $this->input->post('month_attendance');

		// Ensure XLSX libraries are loaded
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		// Cleanup old sample files
		$this->delete_error_file_day_before('1', TIMESHEETS_CREATE_ATTENDANCE_SAMPLE);

		$rel_type = hrp_get_timesheets_status();
		$current_month = date('Y-m-d', strtotime($month_attendance . '-01'));

		// Fetch headers for the month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($current_month, $rel_type);

		// ── REMOVE staff_departments COLUMN ── //
		// 1a) Remove header "Department name"
		$days_header_in_month['headers'] = array_values(array_filter(
			$days_header_in_month['headers'],
			fn($h) => strtolower($h) !== 'department name'
		));
		// 1b) Remove code-key from staff_key
		if (isset($days_header_in_month['staff_key'])) {
			$days_header_in_month['staff_key'] = array_values(array_filter(
				$days_header_in_month['staff_key'],
				fn($k) => $k !== 'staff_departments'
			));
		}
		// 1c) Strip from columns_type
		foreach ($days_header_in_month['columns_type'] as $i => $col) {
			if ($col['data'] === 'staff_departments') {
				unset($days_header_in_month['columns_type'][$i]);
			}
		}
		$days_header_in_month['columns_type'] = array_values($days_header_in_month['columns_type']);

		// Define other headers/data to remove
		$headers_to_remove = [
			'Actual working time of probation contract (hours)',
			'Actual working time of formal contract (hours)',
			'Paid leave time (hours)',
			'Unpaid leave time (hours)',
			'Standard working time of the month (hours)',
		];
		$data_keys_to_remove = [
			'actual_workday_probation',
			'actual_workday',
			'paid_leave',
			'unpaid_leave',
			'standard_workday'
		];
		// Remove unwanted headers
		$days_header_in_month['headers']        = array_values(array_diff($days_header_in_month['headers'], $headers_to_remove));
		$days_header_in_month['attendance_key'] = array_values(array_diff($days_header_in_month['attendance_key'], $data_keys_to_remove));
		foreach ($days_header_in_month['columns_type'] as $idx => $column) {
			if (in_array($column['data'], $data_keys_to_remove)) {
				unset($days_header_in_month['columns_type'][$idx]);
			}
		}
		$days_header_in_month['columns_type'] = array_values($days_header_in_month['columns_type']);
		foreach ($data_keys_to_remove as $key) {
			if (isset($days_header_in_month['days_header'][$key])) {
				unset($days_header_in_month['days_header'][$key]);
			}
		}

		// Build header_key for writer
		$header_key = array_merge(
			$days_header_in_month['staff_key'],
			$days_header_in_month['days_key'],
			$days_header_in_month['attendance_key']
		);

		// Fetch existing attendance records
		$attendances       = $this->hr_payroll_model->get_hrp_attendance($current_month);
		$attendances_value = [];
		foreach ($attendances as $rec) {
			$attendances_value[$rec['staff_id'] . '_' . $rec['month']] = $rec;
		}

		// Load staff list
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		// Prepare XLSXWriter
		$writer_header = [];
		$widths        = [];
		foreach ($days_header_in_month['headers'] as $h) {
			$writer_header[$h] = 'string';
			$widths[]         = 30;
		}
		$writer     = new XLSXWriter();
		$col_style1 = [0, 1, 2, 3, 4, 5];
		$style1     = [
			'widths'       => $widths,
			'fill'         => '#ff9800',
			'font-style'   => 'bold',
			'color'        => '#0a0a0a',
			'border'       => 'left,right,top,bottom',
			'border-color' => '#0a0a0a',
			'font-size'    => 13
		];
		$writer->writeSheetHeader_v2(
			'Sheet1',
			$writer_header,
			['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1,
			$style1
		);

		// Write rows per staff
		foreach ($staffs as $i => $staff) {
			$data_object_kpi = [];
			// Basic staff info
			$info = $this->hr_payroll_model->get_staff_info($staff['staffid']);
			$data_object_kpi['hr_code']    = $info->staff_identifi ?? '';
			$data_object_kpi['staff_name'] = trim(($info->firstname ?? '') . ' ' . ($info->lastname ?? ''));

			// ID and existing days
			$key = $staff['staffid'] . '_' . $current_month;
			if (isset($attendances_value[$key])) {
				$rec                        = $attendances_value[$key];
				$data_object_kpi['id']      = $rec['id'];
				$data_object_kpi             = array_merge($data_object_kpi, $rec);
			} else {
				$data_object_kpi['id']      = 0;
				$data_object_kpi             = array_merge($data_object_kpi, $days_header_in_month['days_header']);
			}
			// Strip out unwanted attendance keys
			foreach ($data_keys_to_remove as $k) {
				unset($data_object_kpi[$k]);
			}

			// Mandatory keys
			$data_object_kpi['rel_type'] = $rel_type;
			$data_object_kpi['month']    = $current_month;
			$data_object_kpi['staff_id'] = $staff['staffid'];

			// Write header row once
			if ($i === 0) {
				$writer->writeSheetRow('Sheet1', $header_key);
			}
			$row = $this->get_values_for_keys($data_object_kpi, $header_key);
			$writer->writeSheetRow('Sheet1', $row);
		}

		// Save file
		$filename = 'attendance_sample_file' . get_staff_user_id() . '_' . time() . '.xlsx';
		$writer->writeToFile(TIMESHEETS_CREATE_ATTENDANCE_SAMPLE . $filename);

		// Return JSON
		echo json_encode([
			'success'  => true,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => TIMESHEETS_CREATE_ATTENDANCE_SAMPLE . $filename,
		]);
	}

	/**
	 * get values for keys
	 * @param  [type] $mapping
	 * @param  [type] $keys
	 * @return [type]
	 */
	function get_values_for_keys($data, $keys)
	{
		$result = [];
		foreach ($keys as $key) {
			$result[] = isset($data[$key]) ? $data[$key] : '';
		}
		return $result;
	}
}
