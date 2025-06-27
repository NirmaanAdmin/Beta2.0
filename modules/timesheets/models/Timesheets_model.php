<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * timesheets model
 */
class timesheets_model extends app_model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * get staff
	 * @param  string $id
	 * @param  array  $where
	 * @return array
	 */
	public function get_staff($id = '', $where = [])
	{
		$select_str = '*,concat(firstname," ",lastname) as full_name';

		if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
			$select_str .= ',(select count(*) from ' . db_prefix() . 'notifications where touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (select count(*) from ' . db_prefix() . 'todos where finished=0 and staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
		}

		$this->db->select($select_str);
		$this->db->where($where);

		if (is_numeric($id)) {
			$this->db->where('staffid', $id);
			$staff = $this->db->get(db_prefix() . 'staff')->row();

			if ($staff) {
				$staff->permissions = $this->get_staff_permissions($id);
			}

			return $staff;
		}
		$this->db->order_by('firstname', 'desc');

		return $this->db->get(db_prefix() . 'staff')->result_array();
	}
	/**
	 * get staff role
	 * @param  integer $staff_id
	 * @return object
	 */
	public function get_staff_role($staff_id)
	{

		return $this->db->query('select r.name
			from ' . db_prefix() . 'staff as s
			left join ' . db_prefix() . 'roles as r on r.roleid = s.role
			where s.staffid =' . $staff_id)->row();
	}

	/**
	 * get department name
	 * @param   $departmentid
	 * @return
	 */
	public function get_department_name($departmentid)
	{
		return $this->db->query('select ' . db_prefix() . 'departments.name from ' . db_prefix() . 'departments where departmentid = ' . $departmentid)->result_array();
	}

	/**
	 * get month
	 * @return month
	 */
	public function get_month()
	{
		$date = getdate();
		$date_1 = mktime(0, 0, 0, ($date['mon'] - 5), 1, $date['year']);
		$date_2 = mktime(0, 0, 0, ($date['mon'] - 4), 1, $date['year']);
		$date_3 = mktime(0, 0, 0, ($date['mon'] - 3), 1, $date['year']);
		$date_4 = mktime(0, 0, 0, ($date['mon'] - 2), 1, $date['year']);
		$date_5 = mktime(0, 0, 0, ($date['mon'] - 1), 1, $date['year']);
		$date_6 = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
		$date_7 = mktime(0, 0, 0, ($date['mon'] + 1), 1, $date['year']);
		$date_8 = mktime(0, 0, 0, ($date['mon'] + 2), 1, $date['year']);
		$date_9 = mktime(0, 0, 0, ($date['mon'] + 3), 1, $date['year']);
		$date_10 = mktime(0, 0, 0, ($date['mon'] + 4), 1, $date['year']);
		$date_11 = mktime(0, 0, 0, ($date['mon'] + 5), 1, $date['year']);
		$date_12 = mktime(0, 0, 0, ($date['mon'] + 6), 1, $date['year']);
		$month = [
			'1' => ['id' => date('Y-m-d', $date_1), 'name' => date('m/y', $date_1)],
			'2' => ['id' => date('Y-m-d', $date_2), 'name' => date('m/y', $date_2)],
			'3' => ['id' => date('Y-m-d', $date_3), 'name' => date('m/y', $date_3)],
			'4' => ['id' => date('Y-m-d', $date_4), 'name' => date('m/y', $date_4)],
			'5' => ['id' => date('Y-m-d', $date_5), 'name' => date('m/y', $date_5)],
			'6' => ['id' => date('Y-m-d', $date_6), 'name' => date('m/y', $date_6)],
			'7' => ['id' => date('Y-m-d', $date_7), 'name' => date('m/y', $date_7)],
			'8' => ['id' => date('Y-m-d', $date_8), 'name' => date('m/y', $date_8)],
			'9' => ['id' => date('Y-m-d', $date_9), 'name' => date('m/y', $date_9)],
			'10' => ['id' => date('Y-m-d', $date_10), 'name' => date('m/y', $date_10)],
			'11' => ['id' => date('Y-m-d', $date_11), 'name' => date('m/y', $date_11)],
			'12' => ['id' => date('Y-m-d', $date_12), 'name' => date('m/y', $date_12)],
		];
		return $month;
	}
	/**
	 * set leave
	 * @param object $data
	 */
	public function set_leave($data)
	{
		$affectedrows = 0;
		foreach (json_decode($data['leave_of_the_year_data']) as $key => $value) {
			// Remove the second value (index 1) from the array
			unset($value[1]);
			$value = array_values($value); // Re-index the array after unset
			if ($value[0] != null) {
				$this->db->where('staffid', $value[0]);
				$this->db->where('year', $data['start_year_for_annual_leave_cycle']);
				$this->db->where('type_of_leave', $data['type_of_leave']);
				$data_staff_leave = $this->db->get(db_prefix() . 'timesheets_day_off')->row();
				if ($data_staff_leave) {
					$data_update['total'] = $value[4];
					$data_update['year'] = $data['start_year_for_annual_leave_cycle'];
					$data_update['type_of_leave'] = $data['type_of_leave'];
					$data_update['staffid'] = $value[0];
					$days_off = $data_staff_leave->days_off;
					if ($days_off == null || $days_off == '') {
						$days_off = 0;
					}
					$data_update['remain'] = $value[4] - $days_off;
					$this->db->where('id', $data_staff_leave->id);
					$this->db->update(db_prefix() . 'timesheets_day_off', $data_update);
					$affectedrows++;
				} else {
					$data_update['total'] = $value[4];
					$data_update['remain'] = $value[4];
					$data_update['days_off'] = 0;
					$data_update['year'] = $data['start_year_for_annual_leave_cycle'];
					$data_update['type_of_leave'] = $data['type_of_leave'];
					$data_update['staffid'] = $value[0];
					$this->db->insert(db_prefix() . 'timesheets_day_off', $data_update);
					$affectedrows++;
				}
			}
		}

		if (isset($data['start_year_for_annual_leave_cycle'])) {
			$this->db->where('option_name', 'start_year_for_annual_leave_cycle');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['start_year_for_annual_leave_cycle'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'start_year_for_annual_leave_cycle');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => date('Y'),
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}

		if (isset($data['type_of_leave'])) {
			$this->db->where('option_name', 'type_of_leave_selected');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['type_of_leave'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'type_of_leave_selected');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => 8,
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}

		return $affectedrows;
	}
	/**
	 * add_day_off
	 * @param  array $data
	 */
	public function add_day_off($data)
	{
		$department = '';
		$position = '';
		$repeat_by_year = 0;

		if (isset($data['department'])) {
			$department = implode(',', $data['department']);
		}
		if (isset($data['position'])) {
			$position = implode(',', $data['position']);
		}
		if (isset($data['repeat_by_year'])) {
			$repeat_by_year = $data['repeat_by_year'];
		}

		$this->db->insert(db_prefix() . 'day_off', [
			'off_reason' => $data['leave_reason'],
			'off_type' => $data['leave_type'],
			'break_date' => $this->format_date($data['break_date']),
			'department' => $department,
			'position' => $position,
			'repeat_by_year' => $repeat_by_year,
			'add_from' => get_staff_user_id(),
		]);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return $insert_id;
		}
		return 0;
	}
	/**
	 * update day off
	 * @param   array $data
	 * @param   int $id
	 * @return   bool
	 */
	public function update_day_off($data, $id)
	{
		$department = '';
		$position = '';
		$repeat_by_year = 0;

		if (isset($data['department'])) {
			$department = implode(',', $data['department']);
		}
		if (isset($data['position'])) {
			$position = implode(',', $data['position']);
		}
		if (isset($data['repeat_by_year'])) {
			$repeat_by_year = $data['repeat_by_year'];
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'day_off', [
			'off_reason' => $data['leave_reason'],
			'off_type' => $data['leave_type'],
			'break_date' => $this->format_date($data['break_date']),
			'department' => $department,
			'position' => $position,
			'repeat_by_year' => $repeat_by_year,
			'add_from' => get_staff_user_id(),

		]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * get break dates
	 * @param  string $type
	 * @return array
	 */
	public function get_break_dates($type = '')
	{
		if ($type != '') {
			$this->db->where('off_type', $type);
			return $this->db->get(db_prefix() . 'day_off')->result_array();
		} else {
			return $this->db->get(db_prefix() . 'day_off')->result_array();
		}
	}
	/**
	 * delete day off
	 * @param  $id
	 * @return bool
	 */
	public function delete_day_off($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'day_off');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get_requisition
	 * @param  $type
	 * @return
	 */
	public function get_requisition($type)
	{
		return $this->db->query('select rq.id, rq.name, rq.addedfrom, rq.date_create, rq.approval_deadline, rq.status
			from ' . db_prefix() . 'request rq
			left join ' . db_prefix() . 'request_type rqt on rqt.id = rq.request_type_id
			where rqt.related_to = ' . $type)->result_array();
	}

	/**
	 * add work shift
	 * @param object $data
	 * @return integer
	 */
	public function add_work_shift($data)
	{
		unset($data['id']);
		if (!$this->check_format_date_ymd($data['from_date'])) {
			$data_insert['from_date'] = to_sql_date($data['from_date']);
		} else {
			$data_insert['from_date'] = $data['from_date'];
		}
		if (!$this->check_format_date_ymd($data['to_date'])) {
			$data_insert['to_date'] = to_sql_date($data['to_date']);
		} else {
			$data_insert['to_date'] = $data['to_date'];
		}
		if (isset($data['department'])) {
			$data_insert['department'] = implode(',', $data['department']);
		}
		if (isset($data['role'])) {
			$data_insert['position'] = implode(',', $data['role']);
		}
		if (isset($data['staff'])) {
			$data_insert['staff'] = implode(',', $data['staff']);
		}
		if (isset($data['type_shiftwork'])) {
			$data_insert['type_shiftwork'] = $data['type_shiftwork'];
		}
		$data_insert['shift_code'] = strtotime(date('Y-m-d'));
		$data_insert['shift_name'] = '';
		$data_insert['shift_type'] = '';
		$data_insert['date_create'] = date('Y-m-d');
		$data_insert['add_from'] = get_staff_user_id();
		$this->db->insert(db_prefix() . 'work_shift', $data_insert);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			$new_list_id = [];
			$staff_id_list = [];
			$has_staff = false;
			if (isset($data['staff'])) {
				$has_staff = true;
				foreach ($data['staff'] as $key => $staff_id) {
					if (!in_array($staff_id, $staff_id_list)) {
						$staff_id_list[] = $staff_id;
					}
				}
			}

			$data_shift_hanson = $this->get_shift_repeat_periodically($data['shifts_detail'], $has_staff);
			$list_date = $this->get_list_date($data_insert['from_date'], $data_insert['to_date']);
			$list_data_shift = [];
			if ($data['type_shiftwork'] == 'repeat_periodically') {

				$data_shift_hanson = $this->get_shift_repeat_periodically($data['shifts_detail'], $has_staff);
				if ($has_staff == true) {
					foreach ($staff_id_list as $key => $staffid) {
						foreach ($data_shift_hanson as $h => $r_item) {
							if ($staffid == $r_item['staff_id']) {
								for ($i = 1; $i <= 7; $i++) {
									$shift_id = $r_item[$i];
									if ($shift_id != '') {
										$data_detail['staff_id'] = $staffid;
										$data_detail['number'] = $i;
										$data_detail['shift_id'] = $shift_id;
										$data_detail['work_shift_id'] = $insert_id;
										$this->db->insert(db_prefix() . 'work_shift_detail_number_day', $data_detail);
									}
								}
							}
						}
					}
				} else {
					foreach ($data_shift_hanson as $h => $r_item) {
						for ($i = 1; $i <= 7; $i++) {
							$shift_id = $r_item[$i];
							if ($shift_id != '') {
								$data_detail['staff_id'] = '';
								$data_detail['number'] = $i;
								$data_detail['shift_id'] = $shift_id;
								$data_detail['work_shift_id'] = $insert_id;
								$this->db->insert(db_prefix() . 'work_shift_detail_number_day', $data_detail);
							}
						}
					}
				}
			} elseif ($data['type_shiftwork'] == 'by_absolute_time') {

				$staff_id_list = [];
				$has_staff = false;
				if (isset($data['staff'])) {
					$has_staff = true;
					foreach ($data['staff'] as $key => $staff_id) {
						if (!in_array($staff_id, $staff_id_list)) {
							$staff_id_list[] = $staff_id;
						}
					}
				}
				$data_shift_hanson = $this->get_shift_by_absolute_time($data['shifts_detail'], $list_date, $has_staff);
				if ($has_staff == true) {
					foreach ($staff_id_list as $key => $staffid) {
						foreach ($data_shift_hanson as $h => $r_item) {
							if ($staffid == $r_item['staff_id']) {
								foreach ($list_date as $k => $date) {
									$shift_id = $r_item[date('Y-m-d', strtotime($date))];
									if ($shift_id != '' && (int) $shift_id > 0) {
										$data_detail['staff_id'] = $staffid;
										$data_detail['date'] = $date;
										$data_detail['shift_id'] = $shift_id;
										$data_detail['work_shift_id'] = $insert_id;
										$this->db->insert(db_prefix() . 'work_shift_detail', $data_detail);
									}
								}
							}
						}
					}
				} else {
					foreach ($data_shift_hanson as $h => $r_item) {
						foreach ($list_date as $k => $date) {
							$shift_id = $r_item[date('Y-m-d', strtotime($date))];
							if ($shift_id != '' && (int) $shift_id > 0) {
								$data_detail['staff_id'] = '';
								$data_detail['date'] = $date;
								$data_detail['shift_id'] = $shift_id;
								$data_detail['work_shift_id'] = $insert_id;
								$this->db->insert(db_prefix() . 'work_shift_detail', $data_detail);
							}
						}
					}
				}
			}
			return $insert_id;
		}
	}
	/**
	 *  get staff id by department
	 * @param  $id
	 * @return
	 */
	public function get_staff_id_by_department($id)
	{
		$this->db->select('staffid');
		$this->db->where('departmentid', $id);
		return $this->db->get(db_prefix() . 'staff_departments')->result_array();
	}
	/**
	 * get staff id by role
	 * @param  integer $id
	 * @return  array
	 */
	public function get_staff_id_by_role($id)
	{
		$this->db->select('staffid');
		$this->db->where('role', $id);
		return $this->db->get(db_prefix() . 'staff')->result_array();
	}
	/**
	 * get list date
	 * @param   $from_date
	 * @param   $to_date
	 */
	public function get_list_date($from_date, $to_date)
	{
		$list_date = [];
		if (strtotime($from_date) > strtotime($to_date)) {
			return [];
		}
		$i = 0;
		$to_date_s = '';
		$to_date = date('Y-m-d', strtotime($to_date));
		while ($to_date_s != $to_date) {
			$next_date = date('Y-m-d', strtotime($from_date . ' +' . $i . ' day'));
			$list_date[] = $next_date;
			$to_date_s = $next_date;
			$i++;
		}
		return $list_date;
	}
	/**
	 * get shift repeat periodically
	 * @param  string  $shifts_detail
	 * @param  boolean $has_staff
	 * @return array
	 */
	public function get_shift_repeat_periodically($shifts_detail, $has_staff = true)
	{
		$shifts_detail = explode(',', $shifts_detail);
		$es_detail = [];
		$row = [];
		$rq_val = [];
		$header = [];
		if ($has_staff == true) {
			$header[] = 'staff_id';
			$header[] = 'staff';
			$header[] = '1';
			$header[] = '2';
			$header[] = '3';
			$header[] = '4';
			$header[] = '5';
			$header[] = '6';
			$header[] = '7';
			for ($i = 0; $i < count($shifts_detail); $i++) {
				$row[] = $shifts_detail[$i];
				if ((($i + 1) % 9) == 0) {
					$rq_val[] = array_combine($header, $row);
					$row = [];
				}
			}
		} else {
			$header[] = '1';
			$header[] = '2';
			$header[] = '3';
			$header[] = '4';
			$header[] = '5';
			$header[] = '6';
			$header[] = '7';
			for ($i = 0; $i < count($shifts_detail); $i++) {
				$row[] = $shifts_detail[$i];
				if ((($i + 1) % 7) == 0) {
					$rq_val[] = array_combine($header, $row);
					$row = [];
				}
			}
		}
		return $rq_val;
	}
	/**
	 * [get_shift_by_absolute_time
	 * @param  integer $shifts_detail
	 * @param  array $list_date
	 * @param  boolean $has_staff
	 * @return integer
	 */

	public function get_shift_by_absolute_time($shifts_detail, $list_date, $has_staff = true)
	{
		$shifts_detail = explode(',', $shifts_detail);
		$es_detail = [];
		$row = [];
		$rq_val = [];
		$header = [];
		if ($has_staff == true) {
			$header[] = 'staff_id';
			$header[] = 'staff';
			$total_date = 0;
			foreach ($list_date as $date) {
				$header[] = $date;
				$total_date++;
			}

			for ($i = 0; $i < count($shifts_detail); $i++) {
				$row[] = $shifts_detail[$i];

				if ((($i + 1) % ($total_date + 2)) == 0) {
					$rq_val[] = array_combine($header, $row);
					$row = [];
				}
			}
		} else {
			$total_date = 0;
			foreach ($list_date as $date) {
				$header[] = $date;
				$total_date++;
			}
			for ($i = 0; $i < count($shifts_detail); $i++) {
				$row[] = $shifts_detail[$i];
				if ((($i + 1) % $total_date) == 0) {
					$rq_val[] = array_combine($header, $row);
					$row = [];
				}
			}
		}
		return $rq_val;
	}
	/**
	 * update work shift
	 * @param   $data
	 * @return  boolean
	 */
	public function update_work_shift($data)
	{
		if (!$this->check_format_date_ymd($data['from_date'])) {
			$data_insert['from_date'] = to_sql_date($data['from_date']);
		} else {
			$data_insert['from_date'] = $data['from_date'];
		}
		if (!$this->check_format_date_ymd($data['to_date'])) {
			$data_insert['to_date'] = to_sql_date($data['to_date']);
		} else {
			$data_insert['to_date'] = $data['to_date'];
		}
		if (isset($data['department'])) {
			$data_insert['department'] = implode(',', $data['department']);
		} else {
			$data_insert['department'] = '';
		}
		if (isset($data['role'])) {
			$data_insert['position'] = implode(',', $data['role']);
		} else {
			$data_insert['position'] = '';
		}
		if (isset($data['staff'])) {
			$data_insert['staff'] = implode(',', $data['staff']);
		} else {
			$data_insert['staff'] = '';
		}
		if (isset($data['type_shiftwork'])) {
			$data_insert['type_shiftwork'] = $data['type_shiftwork'];
		}
		$data_insert['shift_code'] = strtotime(date('Y-m-d'));
		$data_insert['shift_name'] = '';
		$data_insert['shift_type'] = '';
		$data_insert['date_create'] = date('Y-m-d');
		$data_insert['add_from'] = get_staff_user_id();
		$old_type_shiftwork = '';
		$data_old = $this->get_work_shift($data['id']);
		if ($data_old) {
			$old_type_shiftwork = $data_old->type_shiftwork;
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'work_shift', $data_insert);

		$new_list_id = [];
		$staff_id_list = [];

		$has_staff = false;
		if (isset($data['staff'])) {
			$has_staff = true;
			foreach ($data['staff'] as $key => $staff_id) {
				if (!in_array($staff_id, $staff_id_list)) {
					$staff_id_list[] = $staff_id;
				}
			}
		}

		$list_date = $this->get_list_date($data_insert['from_date'], $data_insert['to_date']);

		$list_data_shift = [];
		if ($data['type_shiftwork'] == 'repeat_periodically') {
			$this->db->where('work_shift_id', $data['id']);
			$this->db->delete(db_prefix() . 'work_shift_detail_number_day');
			$data_shift_hanson = $this->get_shift_repeat_periodically($data['shifts_detail'], $has_staff);
			if ($has_staff == true) {
				foreach ($staff_id_list as $key => $staffid) {
					foreach ($data_shift_hanson as $h => $r_item) {
						if ($staffid == $r_item['staff_id']) {
							for ($i = 1; $i <= 7; $i++) {
								$shift_id = $r_item[$i];
								if ($shift_id != '') {
									$data_detail['number'] = $i;
									$data_detail['staff_id'] = $staffid;
									$data_detail['shift_id'] = $shift_id;
									$data_detail['work_shift_id'] = $data['id'];
									$this->db->insert(db_prefix() . 'work_shift_detail_number_day', $data_detail);
								}
							}
						}
					}
				}
			} else {
				foreach ($data_shift_hanson as $h => $r_item) {
					for ($i = 1; $i <= 7; $i++) {
						$shift_id = $r_item[$i];
						if ($shift_id != '' && $shift_id != 0) {
							$data_detail['staff_id'] = '';
							$data_detail['number'] = $i;
							$data_detail['shift_id'] = $shift_id;
							$data_detail['work_shift_id'] = $data['id'];
							$this->db->insert(db_prefix() . 'work_shift_detail_number_day', $data_detail);
						}
					}
				}
			}
			if ($old_type_shiftwork != 'repeat_periodically') {
				$this->db->where('work_shift_id', $data['id']);
				$this->db->delete(db_prefix() . 'work_shift_detail');
			}
		} elseif ($data['type_shiftwork'] == 'by_absolute_time') {
			$this->db->where('work_shift_id', $data['id']);
			$this->db->delete(db_prefix() . 'work_shift_detail');
			$data_shift_hanson = $this->get_shift_by_absolute_time($data['shifts_detail'], $list_date, $has_staff);
			if ($has_staff == true) {
				foreach ($staff_id_list as $key => $staffid) {
					foreach ($data_shift_hanson as $h => $r_item) {
						if ($staffid == $r_item['staff_id']) {
							foreach ($list_date as $k => $date) {
								$shift_id = $r_item[date('Y-m-d', strtotime($date))];
								if ($shift_id != '' && $shift_id != 0) {
									$data_detail['staff_id'] = $staffid;
									$data_detail['date'] = $date;
									$data_detail['shift_id'] = $shift_id;
									$data_detail['work_shift_id'] = $data['id'];
									$this->db->insert(db_prefix() . 'work_shift_detail', $data_detail);
								}
							}
						}
					}
				}
			} else {
				foreach ($data_shift_hanson as $h => $r_item) {
					foreach ($list_date as $k => $date) {
						$shift_id = $r_item[date('Y-m-d', strtotime($date))];
						if ($shift_id != '' && $shift_id != 0) {
							$data_detail['date'] = $date;
							$data_detail['staff_id'] = 0;
							$data_detail['shift_id'] = $shift_id;
							$data_detail['work_shift_id'] = $data['id'];
							$this->db->insert(db_prefix() . 'work_shift_detail', $data_detail);
						}
					}
				}
			}
			if ($old_type_shiftwork != 'by_absolute_time') {
				$this->db->where('work_shift_id', $data['id']);
				$this->db->delete(db_prefix() . 'work_shift_detail_number_day');
			}
		}
		return true;
	}
	/**
	 * get data edit shift
	 * @param  integer $work_shift
	 * @return array
	 */
	public function get_data_edit_shift($work_shift)
	{
		$this->db->where('id', $work_shift);
		$shift = $this->db->get(db_prefix() . 'work_shift')->row();
		$data['shifts_detail'] = $shift->shifts_detail;
		if (isset($data['shifts_detail'])) {
			$data['shifts_detail'] = explode(',', $data['shifts_detail']);
			$shifts_detail_col = ['detail', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday_even', 'saturday_odd', 'sunday'];
			$row = [];
			$shifts_detail = [];
			for ($i = 0; $i < count($data['shifts_detail']); $i++) {
				$row[] = $data['shifts_detail'][$i];
				if ((($i + 1) % 9) == 0) {
					$shifts_detail[] = array_combine($shifts_detail_col, $row);
					$row = [];
				}
			}
			unset($data['shifts_detail']);
		}
		return $shifts_detail;
	}

	/**
	 * get shift
	 * @param integer $id
	 * @return object or array
	 */
	public function get_shifts($id = '')
	{
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'work_shift')->row();
		} else {
			return $this->db->get(db_prefix() . 'work_shift')->result_array();
		}
	}
	public function delete_shift($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'work_shift');
		if ($this->db->affected_rows() > 0) {
			$this->db->where('work_shift_id', $id);
			$this->db->delete(db_prefix() . 'work_shift_detail');
			$this->db->where('work_shift_id', $id);
			$this->db->delete(db_prefix() . 'work_shift_detail_number_day');
			return true;
		}
		return false;
	}
	/**
	 * get timesheets ts by month
	 * @param integer $month
	 * @param integer $year
	 * @param integer $staffid
	 * @return array
	 */
	public function get_timesheets_ts_by_month($month, $year)
	{
		$check_latch_timesheet = $this->check_latch_timesheet($month . '-' . $year);
		if ($check_latch_timesheet) {
			$query = 'select * from ' . db_prefix() . 'timesheets_timesheet where month(date_work) = ' . $month . ' and year(date_work) = ' . $year . ' and latch = 1';
		} else {
			$query = 'select * from ' . db_prefix() . 'timesheets_timesheet where month(date_work) = ' . $month . ' and year(date_work) = ' . $year . '';
		}
		return $this->db->query($query)->result_array();
	}

	/**
	 * get ts by date and staff
	 * @param  $date
	 * @param  $staff
	 * @return
	 */
	public function get_ts_by_date_and_staff($date, $staff)
	{
		$this->db->where('date_work', $date);
		$this->db->where('staff_id', $staff);
		return $this->db->get(db_prefix() . 'timesheets_timesheet')->result_array();
	}

	/**
	 * staff chart by age
	 */
	public function staff_chart_by_age()
	{
		$staffs = $this->staff_model->get();

		$chart = [];
		$status_1 = ['name' => _l('18-24'), 'color' => '#777', 'y' => 0, 'z' => 100];
		$status_2 = ['name' => _l('25-29'), 'color' => '#fc2d42', 'y' => 0, 'z' => 100];
		$status_3 = ['name' => _l('30 - 39'), 'color' => '#03a9f4', 'y' => 0, 'z' => 100];
		$status_4 = ['name' => _l('40-60'), 'color' => '#ff6f00', 'y' => 0, 'z' => 100];

		foreach ($staffs as $staff) {

			$diff = date_diff(date_create(), date_create($staff['birthday']));
			$age = $diff->format('%y');

			if ($age >= 18 && $age <= 24) {
				$status_1['y'] += 1;
			} elseif ($age >= 25 && $age <= 29) {
				$status_2['y'] += 1;
			} elseif ($age >= 30 && $age <= 39) {
				$status_3['y'] += 1;
			} elseif ($age >= 40 && $age <= 60) {
				$status_4['y'] += 1;
			}
		}
		if ($status_1['y'] > 0) {
			array_push($chart, $status_1);
		}
		if ($status_2['y'] > 0) {
			array_push($chart, $status_2);
		}
		if ($status_3['y'] > 0) {
			array_push($chart, $status_3);
		}
		if ($status_4['y'] > 0) {
			array_push($chart, $status_4);
		}

		return $chart;
	}

	/**
	 * get_timesheets_ts_by_year
	 * @param integre $staff_id
	 * @param integre $year
	 * @return integre
	 */
	public function get_timesheets_ts_by_year($staff_id, $year)
	{

		return $this->db->query('select * from ' . db_prefix() . 'timesheets_timesheet where year(date_work) = ' . $year . ' and staff_id = ' . $staff_id . ' order by date_work asc ')->result_array();
	}
	/**
	 * get request leave
	 * @param  integer $id
	 */
	public function get_request_leave($id = '')
	{
		$this->load->model('staff_model');
		if ($id == '') {
			return $this->db->get(db_prefix() . 'timesheets_requisition_leave')->result_array();
		} else {
			$this->db->where('id', $id);
			$requisition = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
			if ($requisition) {
				$staff = $this->staff_model->get($requisition->staff_id);
				if ($staff) {
					$requisition->email = $staff->email;

					$requisition->name = $this->getdepartment_name($requisition->staff_id)->name;
					$requisition->name_role = $this->get_staff_role($requisition->staff_id)->name;
				}
				$requisition->attachments = $this->get_requisition_attachments($id);
			}
			return $requisition;
		}
	}
	/**
	 * getdepartment name
	 * @param  integer $staffid
	 * @return object
	 */
	public function getdepartment_name($staffid)
	{
		return $this->db->query('select s.staffid, d.departmentid ,d.name
			from ' . db_prefix() . 'staff as s
			left join ' . db_prefix() . 'staff_departments as sd on sd.staffid = s.staffid
			left join ' . db_prefix() . 'departments d on d.departmentid = sd.departmentid
			where s.staffid in (' . $staffid . ')
			order by d.departmentid,s.staffid')->row();
	}
	/**
	 * get requisition attachments
	 * @param  integer $id
	 * @param  integer $attachment_id
	 * @param  array  $where
	 * @return array
	 */
	public function get_requisition_attachments($id = '', $attachment_id = '', $where = [])
	{
		$this->db->where($where);
		$idishash = !is_numeric($attachment_id) && strlen($attachment_id) == 32;
		if (is_numeric($attachment_id) || $idishash) {
			$this->db->where($idishash ? 'attachment_key' : 'id', $attachment_id);

			return $this->db->get(db_prefix() . 'files')->row();
		}
		$this->db->where('rel_id', $id);
		$this->db->where('rel_type', 'requisition');
		$this->db->order_by('dateadded', 'desc');

		return $this->db->get(db_prefix() . 'files')->result_array();
	}
	/**
	 * get number of days off
	 * @param  integer $staffid
	 * @return integer
	 */
	public function get_number_of_days_off($staffid = 0, $year = '')
	{
		if ($staffid == 0) {
			$staffid = get_staff_user_id();
		}
		$staff = $this->staff_model->get($staffid);

		$leave_position = $this->get_timesheets_option_api('timesheets_leave_position');
		// $leave_contract_type = $this->get_timesheets_option_api('timesheets_leave_contract_type');
		// $leave_start_date = $this->get_timesheets_option_api('timesheets_leave_start_date');
		// $max_leave_in_year = $this->get_timesheets_option_api('timesheets_max_leave_in_year');
		// $start_leave_from_month = $this->get_timesheets_option_api('timesheets_start_leave_from_month');
		// $start_leave_to_month = $this->get_timesheets_option_api('timesheets_start_leave_to_month');

		// $add_new_leave_month_from_date = $this->get_timesheets_option_api('timesheets_add_new_leave_month_from_date');

		// $accumulated_leave_to_month = $this->get_timesheets_option_api('timesheets_accumulated_leave_to_month');
		// $leave_contract_sign_day = $this->get_timesheets_option_api('timesheets_leave_contract_sign_day');
		// $add_leave_after_probationary_period = $this->get_timesheets_option_api('add_leave_after_probationary_period');
		// $start_date_seniority = $this->get_timesheets_option_api('timesheets_start_date_seniority');
		// $seniority_year = $this->get_timesheets_option_api('timesheets_seniority_year');
		// $seniority_year_leave = $this->get_timesheets_option_api('timesheets_seniority_year_leave');
		// $next_year = $this->get_timesheets_option_api('timesheets_next_year');
		// $next_year_leave = $this->get_timesheets_option_api('timesheets_next_year_leave');

		$alow_borrow_leave = $this->get_timesheets_option_api('alow_borrow_leave');
		if ($leave_position != '') {
			$leave_position = explode(', ', $leave_position);
			if (!in_array($staff->role, $leave_position)) {
				return 0;
			}
		}

		$count = 0;
		$day_off = $this->get_day_off($staffid, $year, 8);
		if ($day_off) {
			if ($alow_borrow_leave == 1) {
				$count = $day_off->total;
			}
		}
		return $count;
	}
	/**
	 * check whether column exists in a table
	 * custom function because codeigniter is caching the tables and this is causing issues in migrations
	 * @param  string $column column name to check
	 * @param  string $table table name to check
	 * @return boolean
	 */

	public function get_timesheets_option_api($name)
	{
		$options = [];
		$val = '';
		$name = trim($name);

		if (!isset($options[$name])) {
			// is not auto loaded
			$this->db->select('option_val');
			$this->db->where('option_name', $name);
			$row = $this->db->get(db_prefix() . 'timesheets_option')->row();
			if ($row) {
				$val = $row->option_val;
			}
		} else {
			$val = $options[$name];
		}

		return $val;
	}

	/**
	 * gets the day off.
	 * @param      integer  $staffid  the staffid
	 * @param      integer  $year     the year
	 * @return     <type>          the day off.
	 */
	public function get_day_off($staffid = 0, $year = 0, $type_of_leave = '')
	{
		if ($staffid == 0 || $staffid == '') {
			$staffid = get_staff_user_id();
		}
		if ($year == 0 || $year == '') {
			$year = date('Y');
		}
		if ($type_of_leave == '') {
			$type_of_leave = '8';
		}
		$day_off = $this->db->query('select * from ' . db_prefix() . 'timesheets_day_off where staffid = ' . $staffid . ' and year = ' . $year . ' and type_of_leave = "' . $type_of_leave . '"')->row();
		return $day_off;
	}
	/**
	 * check_approval_details
	 * @param   $rel_id
	 * @param   $rel_type
	 * @return   bool
	 */
	public function check_approval_details($rel_id, $rel_type)
	{
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$approve_status = $this->db->get(db_prefix() . 'timesheets_approval_details')->result_array();
		if ($approve_status && count($approve_status) > 0) {
			$count = count($approve_status);
			foreach ($approve_status as $value) {
				if ($value['approve'] == 2) {
					return 'reject';
				}
				if ($value['approve'] == 0) {
					$value['staffid'] = explode(', ', $value['staffid']);
					return $value;
				}
			}
			return true;
		}
		return false;
	}
	/**
	 * get list approval details
	 * @param  integer $rel_id
	 * @param  string $rel_type
	 * @return  array
	 */
	public function get_list_approval_details($rel_id, $rel_type)
	{
		$this->db->select('*');
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		return $this->db->get(db_prefix() . 'timesheets_approval_details')->result_array();
	}

	/**
	 * cancel request
	 * @param object $data
	 * @param  integer $staffid
	 * @return
	 */
	public function cancel_request($data, $staffid = '')
	{
		$rel_id = $data['rel_id'];
		$rel_type = $data['rel_type'];
		$data_update = [];

		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->delete(db_prefix() . 'timesheets_approval_details');

		switch (strtolower($rel_type)) {

			case 'leave':
				$data_update['status'] = 0;
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'timesheets_requisition_leave', $data_update);

				$this->db->where('id', $rel_id);
				$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();

				$st = $requisition_leave->start_time;
				$et = $requisition_leave->end_time;

				if ($staffid != '') {
					$staff_id = $staffid;
				} else {
					$staff_id = get_staff_user_id();
				}

				$type = '';
				switch ($requisition_leave->type_of_leave) {
					case 1:
						$type = 'SI';
						break;
					case 2:
						$type = 'M';
						break;
					case 3:
						$type = 'R';
						break;
					case 4:
						$type = 'P';
						break;
					case 6:
						$type = 'PO';
						break;
					case 7:
						$type = 'ME';
						break;
					case 8:
						$type = 'AL';
						$year = $this->get_first_year_of_period($st);
						$day_off = $this->get_day_off($requisition_leave->staff_id, $year);
						// Number of leaving day
						$dd = $requisition_leave->number_of_leaving_day;
						$update_days_off = $day_off->days_off - $dd;
						$update_remain = $day_off->total - $update_days_off;
						$this->db->where('staffid', $requisition_leave->staff_id);
						$this->db->where('year', $year);
						$this->db->update(db_prefix() . 'timesheets_day_off', [
							'remain' => $update_remain,
							'days_off' => $update_days_off,
						]);
						break;
				}

				$this->db->where('relate_id', $rel_id);
				$this->db->where('relate_type', 'leave');
				$this->db->delete(db_prefix() . 'timesheets_timesheet');

				return true;
				break;
			default:
				return false;
				break;
		}
	}
	/**
	 * get value by time
	 * @param  integer $st
	 * @param  string $et
	 * @param  string $staff_id
	 * @return  decimal
	 */
	public function get_value_by_time($st, $et = '', $staff_id = '')
	{

		if ($staff_id == '') {
			$staffid = get_staff_user_id();
		} else {
			$staffid = $staff_id;
		}

		$work_shift = $this->get_data_edit_shift_by_staff($staffid);
		$date_time = $this->get_date_time($work_shift);

		$time = strtotime($st);

		$lunch_break = 0;

		$work_time = 0;

		$t = 0;

		$staff_sc = $this->timesheets_model->get_staff_shift_applicable_object();
		$list_staff_sc = [];
		foreach ($staff_sc as $key => $value) {
			$list_staff_sc[] = $value['staffid'];
		}

		if (in_array($staffid, $list_staff_sc)) {
			$shift = $this->timesheets_model->get_shiftwork_sc_date_and_staff(date('Y-m-d', $time), $staffid);
			if (isset($shift)) {
				$work_shift = $this->timesheets_model->get_shift_sc($shift);

				$ws_day = '<li class="list-group-item justify-content-between">' . _l('work_times') . ': ' . $work_shift->time_start_work . ' - ' . $work_shift->time_end_work . '</li><li class="list-group-item justify-content-between">' . _l('lunch_break') . ': ' . $work_shift->start_lunch_break_time . ' - ' . $work_shift->end_lunch_break_time . '</li>';

				$work_time = (strtotime($work_shift->time_end_work) - strtotime($work_shift->time_start_work)) / 60;
				$lunch_break = (strtotime($work_shift->end_lunch_break_time) - strtotime($work_shift->start_lunch_break_time)) / 60;

				if (strtotime(date('H:i:s', strtotime($st))) < strtotime($work_shift->time_start_work . ':00')) {
					$stime = strtotime($work_shift->time_start_work . ':00');
				} else {
					$stime = strtotime(date('H:i:s', strtotime($st)));
				}

				if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
					$_st = strtotime(date('H:i:s', strtotime($st)));
					$_et = strtotime(date('H:i:s', strtotime($et)));
					if ($_st < strtotime($work_shift->time_start_work . ':00')) {
						$_st = strtotime($work_shift->time_start_work . ':00');
					} elseif ($_st > strtotime($work_shift->start_lunch_break_time . ':00') && $_st < strtotime($work_shift->end_lunch_break_time . ':00')) {
						$_st = strtotime($work_shift->end_lunch_break_time . ':00');
					}
					if ($_et > strtotime($work_shift->time_end_work . ':00')) {
						$_et = strtotime($work_shift->time_end_work . ':00');
					}
					if (strtotime($work_shift->start_lunch_break_time . ':00') > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
						$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
					} else {
						$t = ($_et - $_st) / 3600;
					}
				} else {

					if (strtotime($work_shift->start_lunch_break_time . ':00') > $stime) {
						$t = (strtotime($work_shift->time_end_work . ':00') - $stime) / 3600 - ($lunch_break / 60);
					} else {
						$t = (strtotime($work_shift->time_end_work . ':00') - $stime) / 3600;
					}
				}
			}
		} else {
			switch (date('n', $time)) {
				case 1:
					$lunch_break = $date_time['lunch_break']['monday'];
					$work_time = $date_time['work_time']['monday'];

					if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['monday'])) {
						$stime = strtotime($date_time['late_for_work']['monday']);
					} else {
						$stime = strtotime(date('H:i:s', strtotime($st)));
					}

					if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
						$_st = strtotime(date('H:i:s', strtotime($st)));
						$_et = strtotime(date('H:i:s', strtotime($et)));
						if ($_st < strtotime($date_time['late_for_work']['monday'])) {
							$_st = strtotime($date_time['late_for_work']['monday']);
						} elseif ($_st > strtotime($date_time['start_lunch_break_time']['monday']) && $_st < strtotime($date_time['start_afternoon_shift']['monday'])) {
							$_st = strtotime($date_time['start_afternoon_shift']['monday']);
						}
						if ($_et > strtotime($date_time['come_home_early']['monday'])) {
							$_et = strtotime($date_time['come_home_early']['monday']);
						}
						if (strtotime($date_time['start_lunch_break_time']['monday']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
							$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
						} else {
							$t = ($_et - $_st) / 3600;
						}
					} else {

						if (strtotime($date_time['start_lunch_break_time']['monday']) > $stime) {
							$t = (strtotime($date_time['come_home_early']['monday']) - $stime) / 3600 - ($lunch_break / 60);
						} else {
							$t = (strtotime($date_time['come_home_early']['monday']) - $stime) / 3600;
						}
					}

					break;
				case 2:
					$lunch_break = $date_time['lunch_break']['tuesday'];
					$work_time = $date_time['work_time']['tuesday'];

					if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['tuesday'])) {
						$stime = strtotime($date_time['late_for_work']['tuesday']);
					} else {
						$stime = strtotime(date('H:i:s', strtotime($st)));
					}
					if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
						$_st = strtotime(date('H:i:s', strtotime($st)));
						$_et = strtotime(date('H:i:s', strtotime($et)));
						if ($_st < strtotime($date_time['late_for_work']['tuesday'])) {
							$_st = strtotime($date_time['late_for_work']['tuesday']);
						} elseif ($_st > strtotime($date_time['start_lunch_break_time']['tuesday']) && $_st < strtotime($date_time['start_afternoon_shift']['tuesday'])) {
							$_st = strtotime($date_time['start_afternoon_shift']['tuesday']);
						}
						if ($_et > strtotime($date_time['come_home_early']['tuesday'])) {
							$_et = strtotime($date_time['come_home_early']['tuesday']);
						}
						if (strtotime($date_time['start_lunch_break_time']['tuesday']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
							$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
						} else {
							$t = ($_et - $_st) / 3600;
						}
					} else {
						if (strtotime($date_time['start_lunch_break_time']['tuesday']) > $stime) {
							$t = (strtotime($date_time['come_home_early']['tuesday']) - $stime) / 3600 - ($lunch_break / 60);
						} else {
							$t = (strtotime($date_time['come_home_early']['tuesday']) - $stime) / 3600;
						}
					}
					break;
				case 3:
					$lunch_break = $date_time['lunch_break']['wednesday'];
					$work_time = $date_time['work_time']['wednesday'];

					if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['wednesday'])) {
						$stime = strtotime($date_time['late_for_work']['wednesday']);
					} else {
						$stime = strtotime(date('H:i:s', strtotime($st)));
					}

					if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
						$_st = strtotime(date('H:i:s', strtotime($st)));
						$_et = strtotime(date('H:i:s', strtotime($et)));
						if ($_st < strtotime($date_time['late_for_work']['wednesday'])) {
							$_st = strtotime($date_time['late_for_work']['wednesday']);
						} elseif ($_st > strtotime($date_time['start_lunch_break_time']['wednesday']) && $_st < strtotime($date_time['start_afternoon_shift']['wednesday'])) {
							$_st = strtotime($date_time['start_afternoon_shift']['wednesday']);
						}
						if ($_et > strtotime($date_time['come_home_early']['wednesday'])) {
							$_et = strtotime($date_time['come_home_early']['wednesday']);
						}
						if (strtotime($date_time['start_lunch_break_time']['wednesday']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
							$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
						} else {
							$t = ($_et - $_st) / 3600;
						}
					} else {

						if (strtotime($date_time['start_lunch_break_time']['wednesday']) > $stime) {
							$t = (strtotime($date_time['come_home_early']['wednesday']) - $stime) / 3600 - ($lunch_break / 60);
						} else {
							$t = (strtotime($date_time['come_home_early']['wednesday']) - $stime) / 3600;
						}
					}
					break;
				case 4:
					$lunch_break = $date_time['lunch_break']['thursday'];
					$work_time = $date_time['work_time']['thursday'];

					if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['thursday'])) {
						$stime = strtotime($date_time['late_for_work']['thursday']);
					} else {
						$stime = strtotime(date('H:i:s', strtotime($st)));
					}
					if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
						$_st = strtotime(date('H:i:s', strtotime($st)));
						$_et = strtotime(date('H:i:s', strtotime($et)));
						if ($_st < strtotime($date_time['late_for_work']['thursday'])) {
							$_st = strtotime($date_time['late_for_work']['thursday']);
						} elseif ($_st > strtotime($date_time['start_lunch_break_time']['thursday']) && $_st < strtotime($date_time['start_afternoon_shift']['thursday'])) {
							$_st = strtotime($date_time['start_afternoon_shift']['thursday']);
						}
						if ($_et > strtotime($date_time['come_home_early']['thursday'])) {
							$_et = strtotime($date_time['come_home_early']['thursday']);
						}
						if (strtotime($date_time['start_lunch_break_time']['thursday']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
							$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
						} else {
							$t = ($_et - $_st) / 3600;
						}
					} else {
						if (strtotime($date_time['start_lunch_break_time']['thursday']) > $stime) {
							$t = (strtotime($date_time['come_home_early']['thursday']) - $stime) / 3600 - ($lunch_break / 60);
						} else {
							$t = (strtotime($date_time['come_home_early']['thursday']) - $stime) / 3600;
						}
					}
					break;
				case 5:
					$lunch_break = $date_time['lunch_break']['friday'];
					$work_time = $date_time['work_time']['friday'];
					if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['friday'])) {
						$stime = strtotime($date_time['late_for_work']['friday']);
					} else {
						$stime = strtotime(date('H:i:s', strtotime($st)));
					}
					if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
						$_st = strtotime(date('H:i:s', strtotime($st)));
						$_et = strtotime(date('H:i:s', strtotime($et)));

						if ($_st < strtotime($date_time['late_for_work']['friday'])) {
							$_st = strtotime($date_time['late_for_work']['friday']);
						} elseif ($_st > strtotime($date_time['start_lunch_break_time']['friday']) && $_st < strtotime($date_time['start_afternoon_shift']['friday'])) {
							$_st = strtotime($date_time['start_afternoon_shift']['friday']);
						}
						if ($_et > strtotime($date_time['come_home_early']['friday'])) {
							$_et = strtotime($date_time['come_home_early']['friday']);
						}
						if (strtotime($date_time['start_lunch_break_time']['friday']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
							$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
						} else {
							$t = ($_et - $_st) / 3600;
						}
					} else {

						if (strtotime($date_time['start_lunch_break_time']['friday']) > $stime) {
							$t = (strtotime($date_time['come_home_early']['friday']) - $stime) / 3600 - ($lunch_break / 60);
						} else {
							$t = (strtotime($date_time['come_home_early']['friday']) - $stime) / 3600;
						}
					}
					break;
				case 6:
					if ((date('d', $time) % 2) == 1) {
						$lunch_break = $date_time['lunch_break']['saturday_odd'];
						$work_time = $date_time['work_time']['saturday_odd'];

						if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['saturday_odd'])) {
							$stime = strtotime($date_time['late_for_work']['saturday_odd']);
						} else {
							$stime = strtotime(date('H:i:s', strtotime($st)));
						}

						if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
							$_st = strtotime(date('H:i:s', strtotime($st)));
							$_et = strtotime(date('H:i:s', strtotime($et)));
							if ($_st < strtotime($date_time['late_for_work']['saturday_odd'])) {
								$_st = strtotime($date_time['late_for_work']['saturday_odd']);
							} elseif ($_st > strtotime($date_time['start_lunch_break_time']['saturday_odd']) && $_st < strtotime($date_time['start_afternoon_shift']['saturday_odd'])) {
								$_st = strtotime($date_time['start_afternoon_shift']['saturday_odd']);
							}
							if ($_et > strtotime($date_time['come_home_early']['saturday_odd'])) {
								$_et = strtotime($date_time['come_home_early']['saturday_odd']);
							}
							if (strtotime($date_time['start_lunch_break_time']['saturday_odd']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
								$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
							} else {
								$t = ($_et - $_st) / 3600;
							}
						} else {
							if (strtotime($date_time['start_lunch_break_time']['saturday_odd']) > $stime) {
								$t = (strtotime($date_time['come_home_early']['saturday_odd']) - $stime) / 3600 - ($lunch_break / 60);
							} else {
								$t = (strtotime($date_time['come_home_early']['saturday_odd']) - $stime) / 3600;
							}
						}
					} elseif ((date('d', $time) % 2) == 0) {
						$lunch_break = $date_time['lunch_break']['saturday_even'];
						$work_time = $date_time['work_time']['saturday_even'];

						if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['saturday_even'])) {
							$stime = strtotime($date_time['late_for_work']['saturday_even']);
						} else {
							$stime = strtotime(date('H:i:s', strtotime($st)));
						}

						if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
							$_st = strtotime(date('H:i:s', strtotime($st)));
							$_et = strtotime(date('H:i:s', strtotime($et)));
							if ($_st < strtotime($date_time['late_for_work']['saturday_even'])) {
								$_st = strtotime($date_time['late_for_work']['saturday_even']);
							} elseif ($_st > strtotime($date_time['start_lunch_break_time']['saturday_even']) && $_st < strtotime($date_time['start_afternoon_shift']['saturday_even'])) {
								$_st = strtotime($date_time['start_afternoon_shift']['saturday_even']);
							}
							if ($_et > strtotime($date_time['come_home_early']['saturday_even'])) {
								$_et = strtotime($date_time['come_home_early']['saturday_even']);
							}
							if (strtotime($date_time['start_lunch_break_time']['saturday_even']) > $stime && strtotime($date_time['start_afternoon_shift']['friday']) < $_et) {
								$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
							} else {
								$t = ($_et - $_st) / 3600;
							}
						} else {
							if (strtotime($date_time['start_lunch_break_time']['saturday_even']) > $stime) {
								$t = (strtotime($date_time['come_home_early']['saturday_even']) - $stime) / 3600 - ($lunch_break / 60);
							} else {
								$t = (strtotime($date_time['come_home_early']['saturday_even']) - $stime) / 3600;
							}
						}
					}
					break;
				case 7:
					$lunch_break = $date_time['lunch_break']['sunday'];
					$work_time = $date_time['work_time']['sunday'];

					if (strtotime(date('H:i:s', strtotime($st))) < strtotime($date_time['late_for_work']['sunday'])) {
						$stime = strtotime($date_time['late_for_work']['sunday']);
					} else {
						$stime = strtotime(date('H:i:s', strtotime($st)));
					}
					if (date('Y-m-d', strtotime($st)) == date('Y-m-d', strtotime($et))) {
						$_st = strtotime(date('H:i:s', strtotime($st)));
						$_et = strtotime(date('H:i:s', strtotime($et)));
						if ($_st < strtotime($date_time['late_for_work']['sunday'])) {
							$_st = strtotime($date_time['late_for_work']['sunday']);
						} elseif ($_st > strtotime($date_time['start_lunch_break_time']['sunday']) && $_st < strtotime($date_time['start_afternoon_shift']['sunday'])) {
							$_st = strtotime($date_time['start_afternoon_shift']['sunday']);
						}
						if ($_et > strtotime($date_time['come_home_early']['sunday'])) {
							$_et = strtotime($date_time['come_home_early']['sunday']);
						}
						if (strtotime($date_time['start_lunch_break_time']['sunday']) > $stime) {
							$t = ($_et - $_st) / 3600 - ($lunch_break / 60);
						} else {
							$t = ($_et - $_st) / 3600;
						}
					} else {
						if (strtotime($date_time['start_lunch_break_time']['sunday']) > $stime) {
							$t = (strtotime($date_time['come_home_early']['sunday']) - $stime) / 3600 - ($lunch_break / 60);
						} else {
							$t = (strtotime($date_time['come_home_early']['sunday']) - $stime) / 3600;
						}
					}
					break;
			}
		}
		return number_format($t, 2);
	}
	/**
	 * check choose when approving
	 * @param   $related
	 */
	public function check_choose_when_approving($related)
	{
		$this->db->select('choose_when_approving');
		$this->db->where('related', $related);
		$rs = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
		if ($rs) {
			return $rs->choose_when_approving;
		} else {
			return 0;
		}
	}
	/**
	 * send request approve
	 * @param  array $data
	 * @param  integer $staff_id
	 * @return bool
	 */
	public function send_request_approve($data, $staff_id = '')
	{
		if (!isset($data['status'])) {
			$data['status'] = '';
		}

		$staff_addedfrom = $data['addedfrom'];
		$date_send = date('Y-m-d H:i:s');

		$data_new = $this->get_approve_setting($data['rel_type'], true, $staff_addedfrom);
		$data_setting = $this->get_approve_setting($data['rel_type'], false, $staff_addedfrom);

		if (!$data_new) {
			$this->update_approve_request($data['rel_id'], $data['rel_type'], 1);
			return false;
		}

		$this->delete_approval_details($data['rel_id'], $data['rel_type']);
		$sender = $staff_addedfrom;

		foreach ($data_new as $value) {
			$row = [];
			$row['notification_recipient'] = $data_setting->notification_recipient;
			$row['approval_deadline'] = date('Y-m-d', strtotime('+' . $data_setting->number_day_approval . ' days'));

			if ($value->approver !== 'specific_personnel') {
				$value->staff_addedfrom = $staff_addedfrom;
				$value->rel_type = $data['rel_type'];
				$value->rel_id = $data['rel_id'];

				$staff_ids = $this->get_staff_id_by_approve_value($value, $value->approver); // Returns array

				if (empty($staff_ids)) {
					// Delete and return if no valid approvers found
					$this->db->where('rel_id', $data['rel_id']);
					$this->db->where('rel_type', $data['rel_type']);
					$this->db->delete(db_prefix() . 'timesheets_approval_details');

					return $value->approver;
				}

				foreach ($staff_ids as $staff_id) {
					$staff_info = $this->staff_model->get($staff_id);
					if (!$staff_info) {
						continue; // Skip invalid staff IDs
					}

					$row['approve_value'] = $staff_info->email; // Email for notifications
					$row['staffid'] = $staff_id;
					$row['date_send'] = $date_send;
					$row['rel_id'] = $data['rel_id'];
					$row['rel_type'] = $data['rel_type'];
					$row['sender'] = $sender;

					$this->db->insert(db_prefix() . 'timesheets_approval_details', $row);
				}
			} elseif ($value->approver == 'specific_personnel') {
				$row['staffid'] = $value->staff;
				$row['date_send'] = $date_send;
				$row['rel_id'] = $data['rel_id'];
				$row['rel_type'] = $data['rel_type'];
				$row['sender'] = $sender;

				$this->db->insert(db_prefix() . 'timesheets_approval_details', $row);
			}
		}
		return true;
	}

	/**
	 * get approve setting
	 * @param  integer $type
	 * @param  boolean $only_setting
	 * @param  string  $staff_id
	 * @return bool
	 */
	public function get_approve_setting($type, $only_setting = true, $staff_id = '')
	{
		if ($staff_id == '') {
			$staff_id = get_staff_user_id();
		}
		$this->load->model('departments_model');
		$staff = $this->staff_model->get($staff_id);
		$departments = $this->departments_model->get_staff_departments($staff_id, true);

		$where_job_position = '';
		if ($staff) {
			if ($staff->role != '' && $staff->role != 0) {
				$where_job_position = 'find_in_set(' . $staff->role . ',job_positions)';
			} else {
				$where_job_position = '(job_positions is null OR job_positions = "")';
			}
		}

		$where_departments = '';
		foreach ($departments as $key => $value) {
			if ($where_departments != '') {
				$where_departments .= ' OR find_in_set(' . $value . ',departments)';
			} else {
				$where_departments = 'find_in_set(' . $value . ',departments)';
			}
		}

		if ($where_departments != '') {
			$where_departments = '(' . $where_departments . ')';
		} else {
			$where_departments = '(departments is null OR departments = "")';
		}
		$this->db->select('*');
		if ($where_job_position != '' && $where_departments != '') {
			$this->db->where($where_job_position . ' AND ' . $where_departments . ' AND related="' . $type . '"');
		}
		$approval_setting = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
		if ($approval_setting) {
			if ($only_setting == false) {
				return $approval_setting;
			} else {
				return json_decode($approval_setting->setting);
			}
		} else {
			$this->db->select('*');
			$this->db->where('related', $type);
			if ($where_job_position != '') {
				$this->db->where($where_job_position . ' AND (departments is null OR departments = "") AND related="' . $type . '"');
			}
			$approval_setting = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
			if ($approval_setting) {
				if ($only_setting == false) {
					return $approval_setting;
				} else {
					return json_decode($approval_setting->setting);
				}
			} else {
				$this->db->select('*');
				if ($where_departments != '') {
					$this->db->where($where_departments . ' AND (job_positions is null OR job_positions = "") AND related="' . $type . '"');
				}
				$approval_setting = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
				if ($approval_setting) {
					if ($only_setting == false) {
						return $approval_setting;
					} else {
						return json_decode($approval_setting->setting);
					}
				} else {
					$this->db->select('*');
					$this->db->where('(departments is null OR departments = "") AND (job_positions is null OR job_positions = "") AND related="' . $type . '"');
					$approval_setting = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
					if ($approval_setting) {
						if ($only_setting == false) {
							return $approval_setting;
						} else {
							return json_decode($approval_setting->setting);
						}
					}
				}
			}
		}
		return false;
	}
	/**
	 * delete_approval_details
	 * @param  integer $rel_id
	 * @param  integer $rel_type
	 * @return  bool
	 */
	public function delete_approval_details($rel_id, $rel_type)
	{
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->delete(db_prefix() . 'timesheets_approval_details');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * update approval details
	 * @param  integer $id
	 * @param  array $data
	 * @return bool
	 */
	public function update_approval_details($id, $data)
	{
		$data['date'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'timesheets_approval_details', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * update approve request
	 * @param  integer $rel_id
	 * @param  integer $rel_type
	 * @param  integer $status
	 * @param  string $staffid
	 * @return integer
	 */
	public function update_approve_request($rel_id, $rel_type, $status, $staffid = '')
	{
		$data_update = [];
		$find_a_case = false;
		switch (strtolower($rel_type)) {
			case 'late':
				$data_update['status'] = $status;
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'timesheets_requisition_leave', $data_update);
				if ($status == 1) {
					//Get
					$this->db->where('id', $rel_id);
					$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
					if ($requisition_leave) {
						$start_time = $requisition_leave->start_time;
						$check_latch_timesheet = $this->timesheets_model->check_latch_timesheet(date('m-Y', strtotime($start_time)));
						if (!$check_latch_timesheet) {
							$staffid = $requisition_leave->staff_id;
							// Get current hour in shift
							$shift_info = $this->get_info_hour_shift_staff($staffid, date('Y-m-d', strtotime($start_time)));

							if ($shift_info->start_working != '') {
								// Caculate hour
								$value_ts = $this->get_hour($shift_info->start_working, date('H:i:s', strtotime($start_time)));
								if ($value_ts > 0) {
									// Save to timesheets
									$this->db->insert(db_prefix() . 'timesheets_timesheet', [
										'staff_id' => $staffid,
										'date_work' => date('Y-m-d', strtotime($start_time)),
										'value' => $value_ts,
										'add_from' => $staffid,
										'relate_id' => $rel_id,
										'relate_type' => 'leave',
										'type' => 'L',
									]);
									// Save to timesheets
								}
							}
						}
					}
				}
				return true;
				break;
			case 'early':
				$data_update['status'] = $status;
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'timesheets_requisition_leave', $data_update);
				if ($status == 1) {
					//Get
					$this->db->where('id', $rel_id);
					$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
					if ($requisition_leave) {
						$start_time = $requisition_leave->start_time;
						$check_latch_timesheet = $this->timesheets_model->check_latch_timesheet(date('m-Y', strtotime($start_time)));
						if (!$check_latch_timesheet) {
							$staffid = $requisition_leave->staff_id;
							// Get current hour in shift
							$shift_info = $this->get_info_hour_shift_staff($staffid, date('Y-m-d', strtotime($start_time)));
							if ($shift_info->end_working != '') {
								// Caculate hour
								$value_ts = $this->get_hour($shift_info->end_working, date('H:i:s', strtotime($start_time)));
								if ($value_ts > 0) {
									// Save to timesheets
									$this->db->insert(db_prefix() . 'timesheets_timesheet', [
										'staff_id' => $staffid,
										'date_work' => date('Y-m-d', strtotime($start_time)),
										'value' => $value_ts,
										'add_from' => $staffid,
										'relate_id' => $rel_id,
										'relate_type' => 'leave',
										'type' => 'E',
									]);
									// // Save to timesheets
								}
							}
						}
					}
				}
				break;
			case 'go_out':
				$data_update['status'] = $status;
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'timesheets_requisition_leave', $data_update);
				return true;
				break;
			case 'go_on_bussiness':
				$data_update['status'] = $status;
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'timesheets_requisition_leave', $data_update);
				if ($status == 1) {
					$this->db->where('id', $rel_id);
					$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();

					$start_time = strtotime($requisition_leave->start_time);
					$end_time = strtotime($requisition_leave->end_time);

					$st = date('Y-m-d', $start_time);
					$et = date('Y-m-d', $end_time);

					if ($staffid != '') {
						$staff_id = $staffid;
					} else {
						$staff_id = get_staff_user_id();
					}

					$type = 'B';
					if ($requisition_leave->end_time != '') {
						for ($i = 0; $i < 5; $i++) {
							if (strtotime($st) <= strtotime($et)) {
								$this->db->insert(db_prefix() . 'timesheets_timesheet', [
									'staff_id' => $requisition_leave->staff_id,
									'date_work' => $st,
									'value' => 0,
									'add_from' => $staff_id,
									'relate_id' => $rel_id,
									'relate_type' => 'leave',
									'type' => $type,
								]);
								$st = date('Y-m-d', strtotime($st . ' + 1 days'));
								$i = 0;
							} else {
								$i = 10;
							}
						}
					} else {
						$this->db->insert(db_prefix() . 'timesheets_timesheet', [
							'staff_id' => $requisition_leave->staff_id,
							'date_work' => $st,
							'value' => 0,
							'add_from' => $staff_id,
							'relate_id' => $rel_id,
							'relate_type' => 'leave',
							'type' => $type,
						]);
					}
				}
				return true;
				break;
			case 'additional_timesheets':
				$data_update['status'] = $status;
				$this->db->where('id', $rel_id);
				$this->db->update(db_prefix() . 'timesheets_additional_timesheet', $data_update);
				if ($status == 1) {
					$this->db->where('id', $rel_id);
					$additional_timesheet = $this->db->get(db_prefix() . 'timesheets_additional_timesheet')->row();

					$check_latch_timesheet = $this->timesheets_model->check_latch_timesheet(date('m-y', strtotime($additional_timesheet->additional_day)));
					if (!$check_latch_timesheet) {
						$staffid = '';
						$data_addts = $this->get_additional_timesheets($rel_id);
						if ($data_addts) {
							$this->db->insert(db_prefix() . 'timesheets_timesheet', [
								'staff_id' => $data_addts->creator,
								'date_work' => $data_addts->additional_day,
								'value' => $data_addts->timekeeping_value,
								'add_from' => $data_addts->creator,
								'relate_id' => $rel_id,
								'relate_type' => 'additional_timesheet',
								'type' => 'W',
							]);
						}
					}
				}

				return true;
				break;
			default:
				$this->update_requisition_after_approve($rel_id, $status, $rel_type);
				break;
		}
	}
	/**
	 * add requisition ajax
	 * @param array $data
	 */
	public function add_requisition_ajax($data)
	{
		$staff_quit_job = $data['staff_id'];
		$type = '';
		if ($data['rel_type'] == '1') {
			switch ($data['type_of_leave']) {
				case 8:
					$type = 'leave';
					break;
				case 2:
					$type = 'maternity_leave';
					break;
				case 4:
					$type = 'private_work_without_pay';
					break;
				case 1:
					$type = 'sick_leave';
					break;
			}
			// Rel type is custom type
			if ($type == '') {
				$type = $data['type_of_leave'];
			}
		} elseif ($data['rel_type'] == '2') {
			$data['end_time'] = $data['start_time'];
			$type = 'late';
		} elseif ($data['rel_type'] == '3') {
			$data['end_time'] = $data['start_time'];
			$type = 'go_out';
		} elseif ($data['rel_type'] == '4') {
			$type = 'go_on_bussiness';
		} elseif ($data['rel_type'] == '5') {
			$type = 'quit_job';
			$data['start_time'] = date('Y-m-d H:i:s');
			$data['end_time'] = date('Y-m-d H:i:s');
		} elseif ($data['rel_type'] == '6') {
			$type = 'early';
		}

		$data['datecreated'] = date('Y-m-d H:i:s');
		if (isset($data['used_to'])) {
			$used_to = $data['used_to'];
			unset($data['used_to']);
		}

		if (isset($data['amoun_of_money'])) {
			$amoun_of_money = $data['amoun_of_money'];
			unset($data['amoun_of_money']);
		}

		if (isset($data['request_date'])) {
			$request_date = to_sql_date($data['request_date']);
			unset($data['request_date']);
		}

		if (isset($data['received_date'])) {
			$received_date = $data['received_date'];
			unset($data['received_date']);
		}

		if (isset($data['advance_payment_reason'])) {
			$advance_payment_reason = $data['advance_payment_reason'];
			unset($data['advance_payment_reason']);
		}
		$day_off = $this->timesheets_model->get_day_off($staff_quit_job, '', $data['type_of_leave']);
		$number_day_off = 0;
		if ($day_off != null) {
			$number_day_off = $day_off->remain;
			if ($number_day_off < 0) {
				$number_day_off = 0;
			}
		}
		$data['number_of_days'] = $number_day_off;
		$check_proccess = $this->get_approve_setting($type, true, $staff_quit_job);
		if ($check_proccess) {
			$this->db->insert(db_prefix() . 'timesheets_requisition_leave', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				handle_requisition_attachments($insert_id);
				if ($data['rel_type'] == 4) {
					foreach ($used_to as $key => $val) {
						$this->db->insert(db_prefix() . 'timesheets_go_bussiness_advance_payment', [
							'requisition_leave' => $insert_id,
							'used_to' => $val,
							'amoun_of_money' => timesheets_reformat_currency_asset($amoun_of_money[$key]),
							'request_date' => $request_date,
							'advance_payment_reason' => $advance_payment_reason,
						]);
					}
				}
				return $insert_id;
			} else {
				return false;
			}
		} else {
			$data['status'] = 1;
			$this->db->insert(db_prefix() . 'timesheets_requisition_leave', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {

				handle_requisition_attachments($insert_id);
				if ($data['rel_type'] == 4) {
					foreach ($used_to as $key => $val) {
						$this->db->insert(db_prefix() . 'timesheets_go_bussiness_advance_payment', [
							'requisition_leave' => $insert_id,
							'used_to' => $val,
							'amoun_of_money' => timesheets_reformat_currency_asset($amoun_of_money[$key]),
							'request_date' => $request_date,
							'advance_payment_reason' => $advance_payment_reason,
						]);
					}
				}
				$this->update_approve_request($insert_id, $type, $data['status']);
				return $insert_id;
			} else {
				return false;
			}
		}
	}

	/**
	 * delete requisition
	 * @param  integer $id
	 */
	public function delete_requisition($id)
	{
		$this->db->where('id', $id);
		$this->db->where('status', 1);
		$data_requisition = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
		if ($data_requisition) {
			if ($data_requisition->rel_type == '4') {
				$this->db->where('requisition_leave', $id);
				$this->db->delete(db_prefix() . 'timesheets_go_bussiness_advance_payment');
			}
			if ($data_requisition->rel_type == '1' && $data_requisition->status == '1') {
				$year = $this->get_first_year_of_period($data_requisition->start_time);
				$type_of_leave_data = $this->get_type_of_leave_id($data_requisition->type_of_leave_text);
				// Get total leave in year of staff
				$day_off = $this->get_day_off($data_requisition->staff_id, $year, $type_of_leave_data->type_id);
				// Number of leaving day
				$dd = $data_requisition->number_of_leaving_day;

				$update_days_off = 0;
				if ($day_off->days_off >= $dd) {
					$update_days_off = $day_off->days_off - $dd;
				} else {
					$update_days_off = 0;
				}

				$update_remain = abs($day_off->total - $update_days_off);
				$this->db->where('type_of_leave', $type_of_leave_data->type_id);
				$this->db->where('staffid', $data_requisition->staff_id);
				$this->db->where('year', $year);
				$this->db->update(db_prefix() . 'timesheets_day_off', [
					'remain' => $update_remain,
					'days_off' => $update_days_off,
				]);
			}
			$this->db->where('relate_id', $id);
			$this->db->where('relate_type', 'leave');
			$this->db->delete(db_prefix() . 'timesheets_timesheet');
		}
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_requisition_leave');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get option val
	 * @return object
	 */
	public function get_option_val()
	{
		$this->db->select('option_val');
		$this->db->from('timesheets_option');
		$where_opt = 'option_name = "leave_according_process"';

		$this->db->where($where_opt);
		$query = $this->db->get()->row();
		return $query;
	}
	/**
	 * get staff shift applicable object
	 * @return array
	 */
	public function get_staff_shift_applicable_object()
	{
		$shift_applicable_object = [];
		$this->db->select('option_val');
		$this->db->where('option_name', 'shift_applicable_object');
		$row = $this->db->get(db_prefix() . 'timesheets_option')->row();
		if ($row) {
			if ($row->option_val != '') {
				$shift_applicable_object = explode(',', $row->option_val);
			} else {
				return [];
			}
		}

		$where = '';
		if ($shift_applicable_object) {
			foreach ($shift_applicable_object as $key => $value) {
				if ($where == '') {
					$where = '(role = ' . $value;
				} else {
					$where .= ' or role = ' . $value;
				}
			}
		}

		if ($where != '') {
			$where .= ')';
		}

		if ($where == '') {
			$where .= '(select count(*) from ' . db_prefix() . 'staff_contract where staff = ' . db_prefix() . 'staff.staffid and date_format(start_valid, "%Y-%m") <="' . date('y-m') . '" and if(end_valid = null, date_format(end_valid, "%Y-%m") >="' . date('Y-m') . '",1=1)) > 0 and status_work="working" and active=1';
		} else {
			$where .= ' and (select count(*) from ' . db_prefix() . 'staff_contract where staff = ' . db_prefix() . 'staff.staffid and date_format(start_valid, "%Y-%m") <="' . date('Y-m') . '" and if(end_valid = null, date_format(end_valid, "%Y-%m") >="' . date('Y-m') . '",1=1)) > 0 and status_work="working" and active=1';
		}
		$this->db->where($where);

		return $this->db->get(db_prefix() . 'staff')->result_array();
	}
	/**
	 * check latch timesheet
	 * @param  integer $month
	 * @return bool
	 */
	public function check_latch_timesheet($month)
	{
		if ($month != '') {
			$this->db->where('month_latch', $month);
			$count = $this->db->count_all_results(db_prefix() . 'timesheets_latch_timesheet');

			if ($count > 0) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}
	/**
	 * get staff timekeeping applicable object
	 * @return array
	 */
	public function get_staff_timekeeping_applicable_object($for_cronjob = false)
	{
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
		$where = '';
		if ($timekeeping_applicable_object && $timekeeping_applicable_object != '' && $timekeeping_applicable_object != null) {
			$where .= 'find_in_set(role, "' . $timekeeping_applicable_object . '")';
		}
		if ($for_cronjob == false) {
			if ($where != '') {
				$where .= timesheet_staff_manager_query('attendance_management');
			} else {
				$where .= timesheet_staff_manager_query('attendance_management', 'staffid', '');
			}
		}
		if ($where != '') {
			$where .= ' and active = 1';
		} else {
			$where .= ' active = 1';
		}
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
			$this->db->where($where);
			$this->db->order_by('firstname', 'ASC');
		}
		$result = $this->db->get(db_prefix() . 'staff')->result_array();
		return $result;
	}
	/**
	 * unlatch timesheet
	 * @param  integer $month
	 * @return bool
	 */
	public function unlatch_timesheet($month)
	{
		if ($month != '') {
			$this->db->where('month_latch', $month);
			$this->db->delete(db_prefix() . 'timesheets_latch_timesheet');
			if ($this->db->affected_rows() > 0) {
				$m = date('m', strtotime('01-' . $month));
				$y = date('Y', strtotime('01-' . $month));
				$this->db->where('month(date_work) = ' . $m . ' and year(date_work) = ' . $y . ' and type="NS"');
				$this->db->delete(db_prefix() . 'timesheets_timesheet');
				$this->db->where('month(date_work) = ' . $m . ' and year(date_work) = ' . $y);
				$this->db->update(db_prefix() . 'timesheets_timesheet', ['latch' => 0]);
				return true;
			}
			return false;
		}

		return false;
	}
	/**
	 * get hour check in out staff
	 * @param  integer $staff_id
	 * @param  date $datetime
	 * @return integer
	 */
	public function get_hour_check_in_out_staff($staff_id, $datetime)
	{
		$list_check_in_out = $this->db->query('select date from ' . db_prefix() . 'check_in_out where staff_id = ' . $staff_id . ' and date(date) = \'' . $datetime . '\'')->result_array();
		$hour = 0;
		$lunch_time = 0;
		if (isset($list_check_in_out[0]['date']) && isset($list_check_in_out[1]['date'])) {
			$d1 = $this->format_date_time($list_check_in_out[0]['date']);
			$d2 = $this->format_date_time($list_check_in_out[1]['date']);

			$time_in = strtotime(date('H:i:s', strtotime($d1)));
			$time_out = strtotime(date('H:i:s', strtotime($d2)));

			$list_shift = $this->get_shift_work_staff_by_date($staff_id, $datetime);
			foreach ($list_shift as $ss) {
				$data_shift_type = $this->timesheets_model->get_shift_type($ss);

				$time_in_ = $time_in;
				$time_out_ = $time_out;
				if ($data_shift_type) {
					$d1 = $this->format_date_time($data_shift_type->time_start_work);
					$d2 = $this->format_date_time($data_shift_type->time_end_work);
					$d3 = $this->format_date_time($data_shift_type->start_lunch_break_time);
					$d4 = $this->format_date_time($data_shift_type->end_lunch_break_time);

					$start_work = strtotime(date('H:i:s', strtotime($d1)));
					$end_work = strtotime(date('H:i:s', strtotime($d2)));
					$start_lunch_break = strtotime(date('H:i:s', strtotime($d3)));
					$end_lunch_break = strtotime(date('H:i:s', strtotime($d4)));

					if ($time_in < $start_work && $time_out > $start_work) {
						$time_in_ = $start_work;
					}

					if ($time_out > $end_work && $time_in < $end_work) {
						$time_out_ = $end_work;
					}
					if ($time_out < $start_work) {
						continue;
					}
					if ($time_out_ >= $end_lunch_break) {
						$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
					}
					$hour += round(abs($time_out_ - $time_in_) / (60 * 60), 2);
				}
			}
		}

		$result = abs($lunch_time - $hour);
		if ($result == 0) {
			return '';
		}
		return $result;
	}
	/**
	 * report by leave statistics
	 */
	public function report_by_leave_statistics()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		if ($months_report != '') {

			if (is_numeric($months_report)) {
				// last month
				if ($months_report == '1') {
					$beginmonth = date('y-m-01', strtotime('first day of last month'));
					$endmonth = date('y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$beginmonth = date('y-m-01', strtotime("-$months_report month"));
					$endmonth = date('y-m-t');
				}

				$custom_date_select = '(hrl.start_time between "' . $beginmonth . '" and "' . $endmonth . '")';
			} elseif ($months_report == 'this_month') {
				$custom_date_select = '(hrl.start_time between "' . date('y-m-01') . '" and "' . date('y-m-t') . '")';
			} elseif ($months_report == 'this_year') {
				$custom_date_select = '(hrl.start_time between "' .
					date('Y-m-d', strtotime(date('y-01-01'))) .
					'" and "' .
					date('Y-m-d', strtotime(date('y-12-31'))) . '")';
			} elseif ($months_report == 'last_year') {
				$custom_date_select = '(hrl.start_time between "' .
					date('Y-m-d', strtotime(date(date('y', strtotime('last year')) . '-01-01'))) .
					'" and "' .
					date('Y-m-d', strtotime(date(date('y', strtotime('last year')) . '-12-31'))) . '")';
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date = to_sql_date($this->input->post('report_to'));
				if ($from_date == $to_date) {
					$custom_date_select = 'hrl.start_time ="' . $from_date . '"';
				} else {
					$custom_date_select = '(hrl.start_time between "' . $from_date . '" and "' . $to_date . '")';
				}
			}
		}

		$chart = [];
		$dpm = $this->departments_model->get();
		foreach ($dpm as $d) {
			$chart['categories'][] = $d['name'];

			$chart['sick_leave'][] = $this->count_type_leave($d['departmentid'], 1, $custom_date_select);
			$chart['maternity_leave'][] = $this->count_type_leave($d['departmentid'], 2, $custom_date_select);
			$chart['private_work_with_pay'][] = $this->count_type_leave($d['departmentid'], 3, $custom_date_select);
			$chart['private_work_without_pay'][] = $this->count_type_leave($d['departmentid'], 4, $custom_date_select);
			$chart['child_sick'][] = $this->count_type_leave($d['departmentid'], 5, $custom_date_select);
			$chart['power_outage'][] = $this->count_type_leave($d['departmentid'], 6, $custom_date_select);
			$chart['meeting_or_studying'][] = $this->count_type_leave($d['departmentid'], 7, $custom_date_select);
		}

		return $chart;
	}

	/**
	 * count type leave
	 * @param   integer $department
	 * @param   integer $type
	 * @param   date  $custom_date_select
	 * @return array
	 */
	public function count_type_leave($department, $type, $custom_date_select)
	{

		if ($custom_date_select != '') {
			$query = $this->db->query('select hrl.id, hrl.subject from ' . db_prefix() . 'timesheets_requisition_leave hrl left join ' . db_prefix() . 'staff_departments sd on sd.staffid = hrl.staff_id where sd.departmentid = ' . $department . ' and hrl.type_of_leave = ' . $type . ' and ' . $custom_date_select)->result_array();
		} else {
			$query = $this->db->query('select hrl.id, hrl.subject from ' . db_prefix() . 'timesheets_requisition_leave hrl left join ' . db_prefix() . 'staff_departments sd on sd.staffid = hrl.staff_id where sd.departmentid = ' . $department . ' and hrl.type_of_leave = ' . $type)->result_array();
		}

		return count($query);
	}
	/**
	 * count timekeeping by month
	 * @param  integer $staffid
	 * @param  integer $month
	 * @return integer
	 */
	public function count_timekeeping_by_month($staffid, $month)
	{
		if ($staffid != '' && $staffid != 0) {
			$month = date('m-y', strtotime($month));

			$check_latch_timesheet = $this->check_latch_timesheet($month);

			if (!$check_latch_timesheet) {
				return 0;
			}

			$this->db->where('date_format(date_work, "%m-%y") = "' . $month . '" and staff_id = ' . $staffid);
			$timekeeping = $this->db->get(db_prefix() . 'timesheets_timesheet')->result_array();
			$count_timekeeping = 0;
			$count_result = 0;
			foreach ($timekeeping as $key => $value) {
				if ($value['type'] == 'W' || $value['type'] == 'H' || $value['type'] == 'R' || $value['type'] == 'CT' || $value['type'] == 'CD') {
					$count_timekeeping += $value['value'];
				}
			}

			$work_shift = $this->get_data_edit_shift_by_staff($staffid);

			$lunch_break = (($work_shift[3]['monday'][0] - $work_shift[2]['monday'][0]) * 60) + ($work_shift[3]['monday'][1] - $work_shift[2]['monday'][1]);
			$work_time = (($work_shift[1]['monday'][0] - $work_shift[0]['monday'][0]) * 60) + ($work_shift[1]['monday'][1] - $work_shift[0]['monday'][1]);

			$count_result += $count_timekeeping / (round($work_time - $lunch_break) / 60);
			return $count_result;
		} else {
			return 1;
		}
	}

	/**
	 * get timesheets task ts by month
	 * @param  date $date
	 * @return array
	 */
	public function get_timesheets_task_ts_by_month($date)
	{
		$query = 'select date_format(from_unixtime(`start_time`), \'%y-%m-%d\') as date_work, date_format(from_unixtime(`end_time`), \'%y-%m-%d\') as end_date, staff_id, start_time, end_time from ' . db_prefix() . 'taskstimers where date_format(from_unixtime(`start_time`), \'%y-%m-%d\') <= \'' . $date . '\' and date_format(from_unixtime(`end_time`), \'%y-%m-%d\') >= \'' . $date . '\'';
		$data_task = $this->db->query($query)->result_array();
	}
	/**
	 * get ts by task and staff
	 * @param  date $date
	 * @param  imteger $staff
	 * @return array
	 */
	public function get_ts_by_task_and_staff($date, $staff)
	{
		$query = 'select date_format(from_unixtime(`start_time`), \'%y-%m-%d\') as date_work, date_format(from_unixtime(`end_time`), \'%y-%m-%d\') as end_date, staff_id, start_time, end_time from ' . db_prefix() . 'taskstimers where date_format(from_unixtime(`start_time`), \'%y-%m-%d\') <= \'' . $date . '\' and date_format(from_unixtime(`end_time`), \'%y-%m-%d\') >= \'' . $date . '\' and staff_id = ' . $staff;
		$data_timesheets = $this->db->query($query)->result_array();
	}
	/**
	 * [check_format_date_ymd
	 * @param  date $date
	 * @return boolean
	 */
	public function check_format_date_ymd($date)
	{
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * check format date
	 * @param  date $date
	 * @return boolean
	 */
	public function check_format_date($date)
	{
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/", $date)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * get_role
	 * @param  integer $roleid
	 * @return object or array
	 */
	public function get_role($roleid)
	{
		$this->db->where('roleid', $roleid);
		return $this->db->get(db_prefix() . 'roles')->row();
	}

	/**
	 * leave of the year
	 * @param  integer $staff_id
	 * @return object or array
	 */
	public function leave_of_the_year($staff_id = '')
	{
		if ($staff_id != '') {
			$this->db->where('staff_id', $staff_id);
			return $this->db->get(db_prefix() . 'leave_of_the_year')->row();
		} else {
			return $this->db->get(db_prefix() . 'leave_of_the_year')->result_array();
		}
	}
	/**
	 * get staff query
	 * @param  string $query
	 * @return array
	 */
	public function get_staff_query($query = '')
	{
		// Ensure the 'active = 1' condition is always included
		$base_condition = 'active = 1';

		if ($query != '') {
			$query = ' where ' . $base_condition . ' and (' . $query . ')';
		} else {
			$query = ' where ' . $base_condition;
		}


		return $this->db->query('select * from ' . db_prefix() . 'staff' . $query . ' order by firstname desc')->result_array();
	}
	/**
	 * add shift type
	 * @param integer
	 */
	public function add_shift_type($data)
	{
		if (isset($data['time_start'])) {
			if (!$this->check_format_date_ymd($data['time_start'])) {
				$data['time_start'] = to_sql_date($data['time_start']);
			}
		}
		if (isset($data['time_end'])) {
			if (!$this->check_format_date_ymd($data['time_end'])) {
				$data['time_end'] = to_sql_date($data['time_end']);
			}
		}
		$this->db->insert(db_prefix() . 'shift_type', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return $insert_id;
		}
		return 0;
	}
	/**
	 * update shift type
	 * @param integer
	 */
	public function update_shift_type($data)
	{
		if (isset($data['time_start'])) {
			if (!$this->check_format_date_ymd($data['time_start'])) {
				$data['time_start'] = to_sql_date($data['time_start']);
			}
		}
		if (isset($data['time_end'])) {
			if (!$this->check_format_date_ymd($data['time_end'])) {
				$data['time_end'] = to_sql_date($data['time_end']);
			}
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'shift_type', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete shift type
	 * @param  integer
	 * @return bool
	 */
	public function delete_shift_type($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'shift_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * get shift type
	 * @param  integer
	 * @return bool
	 */
	public function get_shift_type($id = '')
	{
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'shift_type')->row();
		} else {
			return $this->db->get(db_prefix() . 'shift_type')->result_array();
		}
	}
	/**
	 * get staff shift list
	 * @return db
	 */
	public function get_staff_shift_list()
	{
		return $this->db->query('select distinct(staff_id) from ' . db_prefix() . 'work_shift_detail')->result_array();
	}

	/**
	 * get data staff shift list
	 * @return db
	 */
	public function get_data_staff_shift_list($staff_id)
	{
		return $this->db->query('select * from ' . db_prefix() . 'work_shift_detail where staff_id = ' . $staff_id)->result_array();
	}
	/**
	 * check in
	 * @param  array $data
	 * @return integer
	 */
	public function check_in($data)
	{

		if ($data['not_api'] == 0) {
			$get_staff_id =  get_staff_id($data['staff_id']);
			$data['staff_id'] =  $get_staff_id;
		}
		unset($data['not_api']);

		// Check valid IP 
		$enable_check_valid_ip = get_timesheets_option('timekeeping_enable_valid_ip');
		if ($enable_check_valid_ip && $enable_check_valid_ip == 1) {
			$client_ip = '';
			if (isset($data['ip_address'])) {
				$client_ip = $data['ip_address'];
				unset($data['ip_address']);
			} else {
				$client_ip = get_client_ip();
			}
			if ($client_ip != '') {
				$ip_list = $this->get_valid_ip();
				if ($ip_list && count($ip_list) > 0) {
					$registered = false;
					foreach ($ip_list as $key => $row) {
						if ($client_ip == $row['ip']) {
							$registered = !$registered;
							break;
						}
					}
					if (!$registered) {
						// Access denie
						return 5;
					}
				} else {
					// Access denie
					return 5;
				}
			} else {
				// Cannot get client IP address
				return 6;
			}
		}
		// 
		$id_admin = 0;
		$date = '';
		$affectedrows = 0;
		if (!isset($data['date'])) {
			$data['date'] = date('Y-m-d');
			$date = $data['date'];
		}
		if (!isset($data['staff_id'])) {
			$data['staff_id'] = get_staff_user_id();
		}
		if ($data['edit_date'] != '') {
			$temp = $this->format_date_time($data['edit_date']);
			$split_date = explode(' ', $temp);
			$date = $split_date[0];
			$data['date'] = $temp;
		} else {
			$date = date('Y-m-d');
			$data['date'] = $date . ' ' . date('H:i:s');
		}
		unset($data['edit_date']);

		if (($date != '') && ($data['staff_id'] != '')) {
			$check_more = '';
			$count_st = 0;
			$data_setting_coordinates = get_timesheets_option('allow_attendance_by_coordinates');
			if ($data_setting_coordinates && $data_setting_coordinates == 1) {
				$check_more = 'check_coordinates';
				$count_st++;
			}
			$data_setting_rooute = get_timesheets_option('allow_attendance_by_route');
			if ($data_setting_rooute && $data_setting_rooute == 1) {
				$check_more = 'check_route';
				$count_st++;
			}
			$point_id = '';
			$workplace_id = '';
			if ($check_more != '') {
				if (isset($data['location_user'])) {
					$data_location = explode(',', $data['location_user']);
					if (isset($data_location[0]) && isset($data_location[1])) {
						$latitude = $data_location[0];
						$longitude = $data_location[1];
						if ($count_st == 2) {
							if (isset($data['point_id'])) {
								if ($data['point_id'] != '') {
									$point_id = $data['point_id'];
								}
							}
							if ($point_id == '') {
								$point_id = $this->get_next_point($data['staff_id'], $date, $latitude, $longitude)->id;
							}
							if ($point_id == '') {
								$check_more = 'check_coordinates';
							}
						}
						switch ($check_more) {
							case 'check_route':
								// Attendance by route point
								// Get geolocation of this route point and caculation distance to location of you
								// If valid will return route id to insert in check_in_out table
								// Else return error:
								// Error 2: Current location is not allowed to attendance
								// Error 3: Location information is unknown
								if ($point_id == '') {
									if (isset($data['point_id'])) {
										if ($data['point_id'] != '') {
											$point_id = $data['point_id'];
										}
									}
									if ($point_id == '') {
										$point_id = $this->get_next_point($data['staff_id'], $date, $latitude, $longitude)->id;
									}
								}
								if ($point_id != '') {
									$route_point_latitude = '';
									$route_point_longitude = '';
									$max_distance = '';
									$data_route_point = $this->get_route_point($point_id);
									if ($data_route_point) {
										$route_point_latitude = $data_route_point->latitude;
										$route_point_longitude = $data_route_point->longitude;
										$max_distance = $data_route_point->distance;
									}
									if ($latitude != '' && $longitude != '' && $route_point_latitude != '' && $route_point_longitude != '' && $max_distance != '') {
										$cal_distance = $this->compute_distance($route_point_latitude, $route_point_longitude, $latitude, $longitude);
										if ((float) $cal_distance > (float) $max_distance) {
											// Invalid distance
											// Error 2: Current location is not allowed to attendance
											return 2;
										}
									} else {
										// Error 3: Location information is unknown
										return 3;
									}
								} else {
									// Error 4: Route point is unknown
									return 4;
								}
								break;
							case 'check_coordinates':
								// Attendance by geolocation
								$res_coordinates = $this->check_attendance_by_coordinates($data['staff_id'], $latitude, $longitude);
								$error = $res_coordinates->error_code;
								$workplace_id = $res_coordinates->workplace_id;
								if ($error == 2 || $error == 3) {
									// Error 2: Current location is not allowed to attendance
									// Error 3: Location information is unknown
									return $error;
								}
								break;
						}
					} else {
						// Error 3: Location information is unknown
						return 3;
					}
				} else {
					// Error 3: Location information is unknown
					return 3;
				}
			}
			$send_notify = false;
			if (isset($data['send_notify']) && $data['send_notify'] == 1) {
				$send_notify = true;
				unset($data['send_notify']);
			}
			$data['route_point_id'] = $point_id;
			$data['workplace_id'] = $workplace_id;
			unset($data['location_user']);
			unset($data['point_id']);
			if (isset($data['ip_address'])) {
				unset($data['ip_address']);
			}
			$this->db->insert(db_prefix() . 'check_in_out', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				$affectedrows++;
			}

			$this->add_check_in_out_value_to_timesheet($data['staff_id'], $date);
			if ($affectedrows > 0) {
				$type_check_email = '';
				$type_check_notify = '';
				if ($data['type_check'] == 1) {
					$type_check_email = strtolower(_l('checked_in'));
					$type_check_notify = strtolower(_l('checked_in_at'));
				} else {
					$type_check_email = strtolower(_l('checked_out'));
					$type_check_notify = strtolower(_l('checked_out_at'));
				}
				// Send email and notify to notice recipient
				$staff_receive = get_timesheets_option('attendance_notice_recipient');
				if ($staff_receive && $staff_receive != '' && $staff_array_id = explode(',', $staff_receive)) {
					foreach ($staff_array_id as $key => $staffid) {
						$email = $this->get_staff_email($staffid);
						if ($email != '') {
							$staff_name = get_staff_full_name($data['staff_id']);
							$data_send_mail = new stdClass();
							$data_send_mail->receiver = $email;
							$data_send_mail->staff_name = $staff_name;
							$data_send_mail->type_check = $type_check_email;
							$data_send_mail->date_time = _d($data['date']);
							$template = mail_template('attendance_notice', 'timesheets', $data_send_mail);
							$template->send();
							$this->notifications($staffid, 'timesheets/requisition_manage', $type_check_notify . ' ' . _d($data['date']));
						}
					}
				}

				// Send email to customer when staff check in/out at customer location
				if ($send_notify && $check_more == 'check_route' && (get_timesheets_option('send_email_check_in_out_customer_location') == 1)) {
					$customer_email = $this->get_customer_email_route_point($point_id);
					if ($customer_email != '') {
						$staff_name = get_staff_full_name($data['staff_id']);
						$data_send_mail = new stdClass();
						$data_send_mail->receiver = $customer_email;
						$data_send_mail->staff_name = $staff_name;
						$data_send_mail->type_check = $type_check_email;
						$data_send_mail->date_time = _d($data['date']);
						$template = mail_template('attendance_notice', 'timesheets', $data_send_mail);
						$template->send();
					}
				}
				return true;
			}
		}
		return false;
	}
	/**
	 * check_ts check checked in and checked out
	 * @param  integer $staff_id
	 * @param  date $date
	 * @return stdclass
	 */
	public function check_ts($staff_id, $date)
	{
		$check_in = 0;
		$check_out = 0;
		$date_check_in = '';
		$date_check_out = '';
		$data_check_in = $this->db->query('select id, date from ' . db_prefix() . 'check_in_out where staff_id = "' . $staff_id . '" and date(date) = \'' . $date . '\' and type_check = 1')->row();
		if ($data_check_in) {
			$check_in = $data_check_in->id;
			$date_check_in = $data_check_in->date;
		}
		$data_check_out = $this->db->query('select id, date from ' . db_prefix() . 'check_in_out where staff_id = "' . $staff_id . '" and date(date) = \'' . $date . '\' and type_check = 2')->row();
		if ($data_check_out) {
			$check_out = $data_check_out->id;
			$date_check_out = $data_check_out->date;
		}
		$data_check_result = new stdclass();
		$data_check_result->check_in = $check_in;
		$data_check_result->check_out = $check_out;
		$data_check_result->date_check_in = $date_check_in;
		$data_check_result->date_check_out = $date_check_out;
		return $data_check_result;
	}
	/**
	 * get shift data
	 * @param  integer $id
	 * @return stdclass
	 */
	public function get_shift_data($id)
	{
		$data_shift_res = new stdclass();
		$data_shift_res->name = '';
		$data_shift_res->color = '';
		$data_shift_res->description = '';
		$data_shift_res->datecreated = '';

		$data_shift_res->time_start_work = '';
		$data_shift_res->time_end_work = '';

		$data_shift_res->start_lunch_break_time = '';
		$data_shift_res->end_lunch_break_time = '';

		$data_shift_res->start_work_hour = '';
		$data_shift_res->end_work_hour = '';

		$data_shift_res->start_lunch_hour = '';
		$data_shift_res->end_lunch_hour = '';

		$this->db->where('id', $id);
		$data_shift = $this->db->get(db_prefix() . 'shift_type')->row();
		if ($data_shift) {
			$data_shift_res->name = $data_shift->shift_type_name;
			$data_shift_res->color = $data_shift->color;
			$data_shift_res->description = $data_shift->description;
			$data_shift_res->datecreated = $data_shift->datecreated;

			$time_start_sp = explode(' ', $data_shift->time_start_work);
			$time_end_sp = explode(' ', $data_shift->time_end_work);
			$start_lunch_sp = explode(' ', $data_shift->start_lunch_break_time);
			$end_lunch_sp = explode(' ', $data_shift->end_lunch_break_time);

			$data_shift_res->time_start_work = $data_shift->time_start_work;
			$data_shift_res->time_end_work = $data_shift->time_end_work;

			$data_shift_res->start_lunch_break_time = $data_shift->start_lunch_break_time;
			$data_shift_res->end_lunch_break_time = $data_shift->end_lunch_break_time;

			$data_shift_res->start_work_hour = isset($time_start_sp[1]) ? $time_start_sp[1] : '';
			$data_shift_res->end_work_hour = isset($time_end_sp[1]) ? $time_end_sp[1] : '';

			$data_shift_res->start_lunch_hour = isset($start_lunch_sp[1]) ? $start_lunch_sp[1] : '';
			$data_shift_res->end_lunch_hour = isset($end_lunch_sp[1]) ? $end_lunch_sp[1] : '';
		}
		return $data_shift_res;
	}
	/**
	 * get hour shift staff
	 * @param  integer $staff_id
	 * @param  integer $date
	 * @return integer
	 */
	public function get_hour_shift_staff($staff_id, $date)
	{

		$result = 0;
		$data_shift_list = $this->get_shift_work_staff_by_date($staff_id, $date);


		foreach ($data_shift_list as $ss) {
			$data_shift_type = $this->get_shift_type($ss);
			if ($data_shift_type) {
				$hour = $this->get_hour($data_shift_type->time_start_work, $data_shift_type->time_end_work);
				$lunch_hour = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
				$result += abs($hour - $lunch_hour);
			}
		}


		return $result;
	}
	/**
	 * get hour
	 * @param date $date1
	 * @param date $date2
	 * @return decimal
	 */
	public function get_hour($date1, $date2)
	{
		$result = 0;
		if ($date1 != '' && $date2 != '') {
			$timestamp1 = strtotime($date1);
			$timestamp2 = strtotime($date2);
			$result = number_format(abs($timestamp2 - $timestamp1) / (60 * 60), 2);
		}
		return $result;
	}
	/**
	 * format date
	 * @param  date $date
	 * @return date
	 */
	public function format_date($date)
	{
		if (!$this->check_format_date_ymd($date)) {
			$date = to_sql_date($date);
		}
		return $date;
	}

	/**
	 * format date time
	 * @param  date $date
	 * @return date
	 */
	public function format_date_time($date)
	{
		if (!$this->check_format_date($date)) {
			$date = to_sql_date($date, true);
		}
		return $date;
	}
	/**
	 * get workshiftms
	 * @param integer $id
	 * @return integer
	 */
	public function get_workshiftms($id)
	{
		$this->db->where('id', $id);
		return $this->db->get(db_prefix() . 'work_shift')->row();
	}
	/**
	 * get id shift type by date and master id
	 * @param  integer $staff_id
	 * @param  integer $date
	 * @param  integer $work_shift_id
	 * @return integer
	 */
	public function get_id_shift_type_by_date_and_master_id($staff_id, $date, $work_shift_id)
	{
		if ($staff_id != '' && $date != '' && $work_shift_id != '') {
			$this->db->where('staff_id', $staff_id);
			$this->db->where('date', $date);
			$this->db->where('work_shift_id', $work_shift_id);
			$this->db->select('shift_id');
			$data = $this->db->get(db_prefix() . 'work_shift_detail')->row();
			if ($data) {
				return $data->shift_id;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	/**
	 * gets the total standard workload.
	 *
	 * @param      int  $staffid  the staffid
	 * @param      date  $f_date   the f date
	 * @param      date  $t_date   the t date
	 *
	 * @return     array   the staff select.
	 */
	public function get_total_work_time($staffid, $f_date, $t_date)
	{
		$total = 0;
		while (strtotime($f_date) <= strtotime($t_date)) {
			$standard_workload = $this->get_hour_shift_staff($staffid, $f_date);
			$total += $standard_workload;

			$f_date = date('Y-m-d', strtotime('+1 day', strtotime($f_date)));
		}

		return $total;
	}

	/**
	 * get_shift_type_id_by_number_day
	 * @param  int $work_shift_id
	 * @param  int $number
	 * @param  int $staff_id
	 * @return int object
	 */
	public function get_shift_type_id_by_number_day($work_shift_id, $number, $staff_id = '')
	{
		if ($work_shift_id != '') {
			if ($staff_id != '') {
				$query = 'select shift_id from ' . db_prefix() . 'work_shift_detail_number_day where work_shift_id = ' . $work_shift_id . ' and number = \'' . $number . '\' and staff_id = ' . $staff_id;
				return $this->db->query($query)->row();
			} else {
				$query = 'select shift_id from ' . db_prefix() . 'work_shift_detail_number_day where work_shift_id = ' . $work_shift_id . ' and number = \'' . $number . '\'';
				return $this->db->query($query)->row();
			}
		}
	}

	public function get_first_staff_work_shift($work_shift_id)
	{
		if ($work_shift_id) {
			if ($work_shift_id != '') {
				$this->db->where('work_shift_id', $work_shift_id);
				return $this->db->get(db_prefix() . 'work_shift_detail')->row();
			}
		}
	}
	/**
	 * gets the number day.
	 *
	 * @param      string   $from_date  the from date
	 * @param      string   $to_date    to date
	 *
	 * @return     integer  the number day.
	 */
	public function get_number_day($from_date, $to_date, $staffid)
	{
		$count = 0;
		if ($to_date == '') {
			$to_date = date('Y-m-d');
		}
		for ($i = 0; $i < 5; $i++) {
			if (strtotime($from_date) <= strtotime($to_date)) {
				if ($this->get_hour_shift_staff($staffid, $from_date)) {
					$count++;
				}

				$from_date = date('Y-m-d', strtotime($from_date . ' + 1 days'));
				$i = 0;
			} else {
				$i = 10;
			}
		}
		return $count;
	}

	/**
	 * delete shift staff by day name
	 * @param   int $work_shift_id
	 * @param   string $day_name
	 * @param   int $staff_id
	 */
	public function get_shift_staff_by_day_name($work_shift_id, $day_name, $staff_id = '')
	{
		if ($work_shift_id != '' && $day_name != '') {
			if ($staff_id == '') {
				$this->db->where('number', $day_name);
				$this->db->where('work_shift_id', $work_shift_id);
				return $this->db->get(db_prefix() . 'work_shift_detail_number_day')->row();
			} else {
				$this->db->where('staff_id', $staff_id);
				$this->db->where('number', $day_name);
				$this->db->where('work_shift_id', $work_shift_id);
				return $this->db->get(db_prefix() . 'work_shift_detail_number_day')->row();
			}
		}
	}
	/**
	 * convert_day_to_number
	 * @param  int $day
	 * @return  int
	 */
	public function convert_day_to_number($day)
	{
		switch ($day) {
			case 'mon':
				return '1';
			case 'tue':
				return '2';
			case 'wed':
				return '3';
			case 'thu':
				return '4';
			case 'fri':
				return '5';
			case 'sat':
				return '6';
			case 'sun':
				return '7';
		}
	}

	/**
	 * get shift staff by date
	 * @param   int $work_shift_id
	 * @param   string $day_name
	 * @param   int $staff_id
	 */
	public function get_shift_staff_by_date($work_shift_id, $date, $staff_id = '')
	{
		if ($work_shift_id != '' && $date != '') {
			if ($staff_id == '') {
				$this->db->where('date', $date);
				$this->db->where('work_shift_id', $work_shift_id);
				return $this->db->get(db_prefix() . 'work_shift_detail')->row();
			} else {
				$this->db->where('staff_id', $staff_id);
				$this->db->where('date', $date);
				$this->db->where('work_shift_id', $work_shift_id);
				return $this->db->get(db_prefix() . 'work_shift_detail')->row();
			}
		}
	}
	/**
	 * get_work_shift
	 * @param  int $id
	 * @return object or array object
	 */
	public function get_work_shift($id = "")
	{
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'work_shift')->row();
		} else {
			return $this->db->get(db_prefix() . 'work_shift')->result_array();
		}
	}

	/**
	 * get day off staff by date
	 * @param  integer $staff
	 * @param  date $date
	 * @return array
	 */
	public function get_day_off_staff_by_date($staff, $date)
	{
		if ($staff != '' && $date != '') {
			$this->db->where('staffid', $staff);
			$this->db->select('role');
			$role_data = $this->db->get(db_prefix() . 'staff')->row();
			$role_id = 0;
			$add_query = '';
			if ($role_data) {
				if ($role_data->role != '') {
					if ($role_data->role != null) {
						$add_query .= 'find_in_set(' . $role_data->role . ',position)';
					}
				}
			}
			$add_query = '';
			$data_department = $this->departments_model->get_staff_departments($staff, true);
			if ($data_department) {
				$department_query = '';
				foreach ($data_department as $key => $value) {
					if ($department_query == '') {
						$department_query .= 'find_in_set(' . $value . ', department)';
					} else {
						$department_query .= ' or find_in_set(' . $value . ', department)';
					}
				}

				if ($department_query != '') {
					if ($add_query != '') {
						$add_query = '(' . $add_query . ' OR (' . $department_query . '))';
					} else {
						$add_query = $department_query;
					}
				}
			}

			if ($add_query != '') {
				$add_query = $add_query . ' and';
			}

			$query = 'select * from ' . db_prefix() . 'day_off where ' . $add_query . ' break_date = \'' . $date . '\' and repeat_by_year = 0';
			$query2 = 'select * from ' . db_prefix() . 'day_off where ' . $add_query . ' day(break_date) = day(\'' . $date . '\') and month(break_date) = month(\'' . $date . '\') and repeat_by_year = 1';
			$result = $this->db->query($query)->result_array();
			$result2 = $this->db->query($query2)->result_array();
			if (!($result && $result2)) {
				$query = 'select * from ' . db_prefix() . 'day_off where department="" and position="" and break_date = \'' . $date . '\' and repeat_by_year = 0';
				$query2 = 'select * from ' . db_prefix() . 'day_off where department="" and position="" and day(break_date) = day(\'' . $date . '\') and month(break_date) = month(\'' . $date . '\') and repeat_by_year = 1';
				$result = $this->db->query($query)->result_array();
				$result2 = $this->db->query($query2)->result_array();
			}
			$list_shift_id = [];
			foreach ($result as $key => $value) {
				$list_shift_id[] = $value;
			}
			foreach ($result2 as $key => $value) {
				$list_shift_id[] = $value;
			}
			return $list_shift_id;
		}
	}
	/**
	 * get_staffid_ts_by_year
	 * @param  string $month_array
	 * @return array
	 */
	public function get_staffid_ts_by_year($month_array)
	{
		$string = 'select * from ' . db_prefix() . 'timesheets_timesheet where year(date_work)="' . $month_array . '"';
		return $this->db->query($string)->result_array();
	}
	/**
	 * get staffid ts by month
	 * @param  integer $month
	 * @return array
	 */
	public function get_staffid_ts_by_month($month)
	{
		$string = 'select * from ' . db_prefix() . 'timesheets_timesheet where month(date_work)="' . $month . '"';
		return $this->db->query($string)->result_array();
	}
	/**
	 * getstaff
	 * @param  string $id
	 * @param  array  $where
	 * @return array
	 */
	public function getstaff($id = '', $where = [])
	{
		$select_str = '*,concat(firstname," ",lastname) as full_name';

		if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
			$select_str .= ',(select count(*) from ' . db_prefix() . 'notifications where touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (select count(*) from ' . db_prefix() . 'todos where finished=0 and staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
		}
		$this->db->select($select_str);
		$this->db->where($where);

		if (is_numeric($id)) {
			$this->db->where('staffid', $id);
			$staff = $this->db->get(db_prefix() . 'staff')->row();

			if ($staff) {
				$staff->permissions = $this->get_staff_permissions($id);
			}

			return $staff;
		}
		$this->db->join(db_prefix() . 'timesheets_timesheet', db_prefix() . 'timesheets_timesheet.id = (select ' . db_prefix() . 'timesheets_timesheet.id from ' . db_prefix() . 'timesheets_timesheet where ' . db_prefix() . 'timesheets_timesheet.staff_id = ' . db_prefix() . 'staff.staffid limit 1)', 'left');

		$this->db->order_by('firstname', 'ASC');

		return $this->db->get(db_prefix() . 'staff')->result_array();
	}
	/**
	 * fetch all timesheet
	 */
	function fetch_all_timesheet()
	{
		$this->db->select('*');
		$this->db->order_by('id');
		$this->db->from('timesheets_timesheet');
		$this->db->join('staff', 'timesheets_timesheet.staff_id = staff.staffid');
		return $this->db->get();
	}
	/**
	 * get timesheets option
	 * @param  string $option
	 * @return object or array
	 */
	public function get_timesheets_option($option)
	{
		if ($option != '') {
			$this->db->where('option_name', $option);
			return $this->db->get(db_prefix() . 'timesheets_option')->row();
		} else {
			return $this->db->get(db_prefix() . 'timesheets_option')->result_array();
		}
	}
	/**
	 * get data edit shift by staff
	 * @param  integer $staff
	 * @param  date $date
	 * @return array
	 */
	public function get_shift_work_staff_by_date($staff, $date = '')
	{
		$nv = $this->staff_model->get($staff);
		$dpm = $this->departments_model->get_staff_departments($staff, true);
		$sql_dpm = '';
		if ($dpm) {
			foreach ($dpm as $key => $value) {
				if ($sql_dpm == '') {
					$sql_dpm .= 'find_in_set(' . $value . ', department)';
				} else {
					$sql_dpm .= ' or find_in_set(' . $value . ', department)';
				}
			}
		}

		if ($date == '') {
			$date = date('Y-m-d');
		}
		$sql_where = 'find_in_set(' . $staff . ', staff)';
		$this->db->where($sql_where);
		$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
		$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();

		if (!$shift) {
			if ($sql_dpm != '' && ($nv) && ($nv->role != 0 && $nv->role != '')) {
				$this->db->where('find_in_set(' . $nv->role . ', position)');
				$this->db->where('(' . $sql_dpm . ')');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
				$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();
				if (!$shift) {
					$this->db->where('(position = "" or position is null)');
					$this->db->where('(' . $sql_dpm . ')');
					$this->db->where('(staff = "" or staff is null)');
					$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
					$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();

					if (!$shift) {
						$this->db->where('find_in_set(' . $nv->role . ', position)');
						$this->db->where('(department = "" or department is null)');
						$this->db->where('(staff = "" or staff is null)');
						$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
						$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();

						if (!$shift) {
							$this->db->where('(position = "" or position is null)');
							$this->db->where('(department = "" or department is null)');
							$this->db->where('(staff = "" or staff is null)');
							$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
							$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();
						}
					}
				}
			} elseif ($sql_dpm == '' && ($nv) && ($nv->role != 0 && $nv->role != '')) {

				$this->db->where('find_in_set(' . $nv->role . ', position)');
				$this->db->where('(department = "" or department is null)');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
				$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();

				if (!$shift) {
					$this->db->where('(position = "" or position is null)');
					$this->db->where('(department = "" or department is null)');
					$this->db->where('(staff = "" or staff is null)');
					$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
					$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();
				}
			} elseif ($sql_dpm != '' && ($nv) && ($nv->role == 0 || $nv->role == '')) {

				$this->db->where('(position = "" or position is null)');
				$this->db->where('(' . $sql_dpm . ')');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
				$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();
				if (!$shift) {
					$this->db->where('(position = "" or position is null)');
					$this->db->where('(department = "" or department is null)');
					$this->db->where('(staff = "" or staff is null)');
					$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
					$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();
				}
			} elseif ($sql_dpm == '' && ($nv) && ($nv->role == 0 || $nv->role == '')) {
				$this->db->where('(position = "" or position is null)');
				$this->db->where('(department = "" or department is null)');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("' . $date . '" >=  from_date and "' . $date . '" <= to_date)');
				$shift = $this->db->get(db_prefix() . 'work_shift')->result_array();
			}
		}
		$list_shift_id = [];
		$day_number = (int) date('d', strtotime($date));
		$Day = (int) date('N', strtotime($date));
		if ($shift) {
			foreach ($shift as $key => $value) {
				if ($value['type_shiftwork'] == 'by_absolute_time') {
					$this->db->where('(staff_id = ' . $staff . ' or staff_id = 0)');
					$this->db->where('date', $date);
					$this->db->where('work_shift_id', $value['id']);
					$shift_detail = $this->db->get(db_prefix() . 'work_shift_detail')->row();
					if ($shift_detail) {
						if (!in_array($shift_detail->shift_id, $list_shift_id)) {
							$list_shift_id[] = $shift_detail->shift_id;
						}
					}
				} else {
					$this->db->where('(staff_id = ' . $staff . ' or staff_id = 0)');
					$this->db->where('number', $Day);
					$this->db->where('work_shift_id', $value['id']);
					$shift_detail = $this->db->get(db_prefix() . 'work_shift_detail_number_day')->row();
					if ($shift_detail) {
						if (!in_array($shift_detail->shift_id, $list_shift_id)) {
							$list_shift_id[] = $shift_detail->shift_id;
						}
					}
				}
			}
		}
		return $list_shift_id;
	}

	/**
	 * get shift work staff by date
	 * @param   int $staff
	 * @param   date $date
	 * @return  array
	 */
	public function get_shift_work_staff_by_date_2($staff, $date)
	{
		if ($staff != '' && $date != '') {
			$this->db->where('staffid', $staff);
			$this->db->select('role');
			$role_data = $this->db->get(db_prefix() . 'staff')->row();
			$role_id = 0;
			if ($role_data) {
				$role_id = $role_data->role;
			}
			$data_department = $this->db->query('select * from ' . db_prefix() . 'staff_departments where staffid = ' . $staff)->result_array();
			$list_department = '';
			foreach ($data_department as $key => $group) {
				$list_department .= ' find_in_set(' . $group['departmentid'] . ',department) or';
			}
			$day_name = date('d', strtotime($date));
			$day_number = $this->convert_day_to_number(strtolower($day_name));
			$query = 'select shift_id from ' . db_prefix() . 'work_shift_detail_number_day where (work_shift_id in (select id from ' . db_prefix() . 'work_shift where ((' . $list_department . ' position = ' . $role_id . ')  or (department = "" and position = "" and position = "")) and type_shiftwork = \'repeat_periodically\' ) or staff_id = ' . $staff . ') and number = ' . $day_number;
			$query2 = 'select shift_id from ' . db_prefix() . 'work_shift_detail where (work_shift_id in (select id from ' . db_prefix() . 'work_shift where ((' . $list_department . ' position = ' . $role_id . ') or (department = "" and position = "" and position = "")) and type_shiftwork = \'by_absolute_time\' ) or staff_id = ' . $staff . ') and date = \'' . $date . '\'';
			$result = $this->db->query($query)->result_array();
			$result2 = $this->db->query($query2)->result_array();
			$list_shift_id = [];
			foreach ($result as $key => $value) {
				if (!in_array($value['shift_id'], $list_shift_id)) {
					$list_shift_id[] = $value['shift_id'];
				}
			}
			foreach ($result2 as $key => $value) {
				if (!in_array($value['shift_id'], $list_shift_id)) {
					$list_shift_id[] = $value['shift_id'];
				}
			}
			return $list_shift_id;
		}
	}

	/**
	 * send mail
	 * @param  array $data
	 * @param  integer $staffid
	 */
	public function send_mail($data, $staffid = '')
	{
		if ($staffid == '') {
			$staff_id = $staffid;
		} else {
			$staff_id = get_staff_user_id();
		}
		$this->load->model('emails_model');
		if (!isset($data['status'])) {
			$data['status'] = '';
		}
		$get_staff_enter_charge_code = '';
		$mes = 'notify_send_request_approve_project';
		$staff_addedfrom = 0;
		$additional_data = $data['rel_type'];
		$object_type = $data['rel_type'];
		$type = '';
		$link = '';
		$rel_type = strtolower($data['rel_type']);
		switch ($rel_type) {
			case 'hr_planning':
				$hrplanning = $this->get_proposal_hrplanning($data['rel_id']);
				$staff_addedfrom = '';
				$additional_data = '';
				if ($hrplanning) {
					$staff_addedfrom = $hrplanning->requester;
					$additional_data = $hrplanning->proposal_name;
				}
				$type = _l('hr_planning');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve_hr_planning_proposal';
				$mes_approve = 'notify_send_approve_hr_planning_proposal';
				$mes_reject = 'notify_send_rejected_hr_planning_proposal';
				$link = 'timesheets/hr_planning?tab=hr_planning_proposal#' . $data['rel_id'];
				break;

			case 'candidate_evaluation':
				$this->load->model('recruitment/recruitment_model');

				$candidate = $this->recruitment_model->get_candidates($data['candidate']);
				$additional_data = '';
				if ($recruitment) {
					$staff_addedfrom = $candidate->cp_add_from;
					$additional_data = $candidate->candidate_name;
				}

				$type = _l('interview_result');

				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'recruitment/candidate/' . $data['candidate'] . '?evaluation=' . $data['rel_id'];
				break;

			case 'recruitment_campaign':
				$this->load->model('recruitment/recruitment_model');
				$staff_addedfrom = $data['addedfrom'];
				$recruitment = $this->recruitment_model->get_campaign_by_id($data['rel_id']);
				$additional_data = '';
				if ($recruitment) {
					$additional_data = $recruitment->campaign_name;
				}

				$type = _l('recruitment_campaign');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'recruitment/recruitment_campaign/' . $data['rel_id'];

				break;
			case 'leave':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('leave');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'maternity_leave':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('leave');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'private_work_without_pay':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('leave');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'sick_leave':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('leave');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'late':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('late');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'early':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('early');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'go_out':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('go_out');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'go_on_bussiness':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('go_on_bussiness');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			case 'additional_timesheets':
				$additional_timesheets = $this->get_additional_timesheets($data['rel_id']);
				$data['addedfrom'] = $additional_timesheets->creator;
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = '';
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve_additional_timesheets';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_manage?tab=additional_timesheets&additional_timesheets_id=' . $data['rel_id'];
				break;
			case 'recruitment_proposal':
				$this->load->model('recruitment/recruitment_model');
				$additional_data = '';
				$staff_addedfrom = $data['addedfrom'];
				$proposal = $this->recruitment_model->get_rec_proposal($data['rel_id']);
				if ($proposal) {
					$additional_data = $proposal->proposal_name;
				}
				$type = _l('recruitment_proposal');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'recruitment/recruitment_proposal/' . $data['rel_id'];
				break;
			case 'quit_job':
				$staff_addedfrom = $data['addedfrom'];
				$additional_data = _l('quit_job');
				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
				$mes = 'notify_send_request_approve';
				$mes_approve = 'notify_send_approve';
				$mes_reject = 'notify_send_rejected';
				$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				break;
			default:
				if ($data['rel_type'] != '' && $data['rel_id'] != '') {
					$staff_addedfrom = $data['addedfrom'];
					$additional_data = _l('leave');
					$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
					$mes = 'notify_send_request_approve';
					$mes_approve = 'notify_send_approve';
					$mes_reject = 'notify_send_rejected';
					$link = 'timesheets/requisition_detail/' . $data['rel_id'];
				}
				break;
		}
		$check_approve_status = $this->check_approval_details($data['rel_id'], $data['rel_type'], $data['status']);
		if (isset($check_approve_status['staffid'])) {
			if (!in_array($staff_id, $check_approve_status['staffid'])) {
				foreach ($check_approve_status['staffid'] as $value) {
					$staff = $this->staff_model->get($value);
					$notified = add_notification([
						'description' => $mes,
						'touserid' => $staff->staffid,
						'link' => $link,
						'additional_data' => serialize([
							$additional_data,
						]),
					]);
					if ($notified) {
						pusher_trigger_notification([$staff->staffid]);
					}
					$email = $this->get_staff_email($value);
					if ($email != '') {
						$staff_request = '';
						$data_request_leave = $this->timesheets_model->get_request_leave($data['rel_id']);
						if ($data_request_leave) {
							$staff_request = $data_request_leave->staff_id;
						}
						$data_send_mail['receiver'] = $email;
						$data_send_mail['approver'] = get_staff_full_name($value);
						$data_send_mail['staff_name'] = get_staff_full_name($staff_request);
						if ($rel_type == 'additional_timesheets') {
							$data_send_mail['link'] = admin_url('timesheets/requisition_manage?tab=additional_timesheets&additional_timesheets_id=' . $data['rel_id']);
						} else {
							$data_send_mail['link'] = admin_url('timesheets/requisition_detail/' . $data['rel_id']);
						}
						$template = mail_template('send_request_approval', 'timesheets', array_to_object($data_send_mail));
						$template->send();
					}
				}
			}
		}

		if (isset($data['approve'])) {
			if ($data['approve'] == 1) {
				$mes = $mes_approve;
				$mes_email = 'email_send_approve';
			} else {
				$mes = $mes_reject;
				$mes_email = 'email_send_rejected';
			}

			$staff = $this->staff_model->get($staff_addedfrom);
			$notified = add_notification([
				'description' => $mes,
				'touserid' => $staff->staffid,
				'link' => $link,
				'additional_data' => serialize([
					$additional_data,
				]),
			]);
			if ($notified) {
				pusher_trigger_notification([$staff->staffid]);
			}
			$this->emails_model->send_simple_email($staff->email, _l('approval_notification'), _l($mes_email, $type . ' <a href="' . admin_url($link) . '">' . $additional_data . '</a> ') . ' ' . _l('by_staff', get_staff_full_name($staff_id)));
			foreach ($list_approve_status as $key => $value) {
				$value['staffid'] = explode(', ', $value['staffid']);
				if ($value['approve'] == 1 && !in_array(get_staff_user_id(), $value['staffid'])) {
					foreach ($value['staffid'] as $staffid) {
						$staff = $this->staff_model->get($staffid);
						$notified = add_notification([
							'description' => $mes,
							'touserid' => $staff->staffid,
							'link' => $link,
							'additional_data' => serialize([
								$additional_data,
							]),
						]);
						if ($notified) {
							pusher_trigger_notification([$staff->staffid]);
						}

						$this->emails_model->send_simple_email($staff->email, _l('approval_notification'), _l($mes_email, $type . ' <a href="' . admin_url($link) . '">' . $additional_data . '</a>') . ' ' . _l('by_staff', get_staff_full_name($staff_id)));
					}
				}
			}

			// $mes_approve_n = 'notify_send_approve_n';
			// $mes_reject_n = 'notify_send_rejected_n';

			// if ($data['approve'] == 1) {
			// 	$mes_ar_n = $mes_approve_n;
			// } else {
			// 	$mes_ar_n = $mes_reject_n;
			// }

			// $this->db->select('*');
			// $this->db->where('related', $data['rel_type']);
			// $approval_setting = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();

			// if ($approval_setting) {
			// 	$notification_recipient = $approval_setting->notification_recipient;
			// 	$arr_notification_recipient = explode(",", $notification_recipient);
			// } else {
			// 	$arr_notification_recipient = [];
			// }

			// if (count($arr_notification_recipient) > 0) {

			// 	$mail_template = 'send-request-approve';

			// 	if (!in_array($staff_id, $arr_notification_recipient)) {
			// 		foreach ($arr_notification_recipient as $value1) {

			// 			$notified = add_notification([
			// 				'description' => $mes_ar_n,
			// 				'touserid' => $value1,
			// 				'link' => $link,
			// 				'additional_data' => serialize([
			// 					$additional_data,
			// 				]),
			// 			]);

			// 			if ($notified) {
			// 				pusher_trigger_notification([$value1]);
			// 			}

			// 			$email = $this->get_staff_email($value1);
			// 			if ($email != '') {
			// 				$this->emails_model->send_simple_email($email, _l('approval_notification'), _l($mes_email, $type . ' <a href="' . admin_url($link) . '">' . $additional_data . '</a>') . ' ' . _l('by_staff', get_staff_full_name($staff_id)));
			// 			}
			// 		}
			// 	}
			// }
		}
	}
	/**
	 * send notifi handover recipients
	 * @param  array $data
	 * @return array
	 */
	public function send_notifi_handover_recipients($data)
	{
		$this->load->model('emails_model');
		if (!isset($data['status'])) {
			$data['status'] = '';
		}

		$get_staff_enter_charge_code = '';
		$mes = 'notify_send_request_approve_project';
		$staff_addedfrom = 0;
		$additional_data = $data['rel_type'];
		$object_type = $data['rel_type'];

		$staff_addedfrom = $data['addedfrom'];
		$additional_data = _l('leave');
		$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);
		$mes = 'chosen_you_to_handover_recipients_requisition';

		$mes_approve = 'notify_send_approve';
		$mes_reject = 'notify_send_rejected';
		$link = 'timesheets/requisition_detail/' . $data['rel_id'];

		$this->db->select('*');
		$this->db->where('id', $data['rel_id']);
		$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
		if ($requisition_leave) {
			$staff_handover_recipients = $requisition_leave->handover_recipients;
			$additional_data = $requisition_leave->subject;
		} else {
			$staff_handover_recipients = 'false';
		}

		if (is_numeric($staff_handover_recipients)) {

			$mail_template = 'send-request-approve';

			if (get_staff_user_id() != $staff_handover_recipients) {
				$notified = add_notification([
					'description' => $mes,
					'touserid' => $staff_handover_recipients,
					'link' => $link,
					'additional_data' => serialize([
						$additional_data,
					]),
				]);
				if ($notified) {
					pusher_trigger_notification([$staff_handover_recipients]);
				}
			}
		}
	}
	/**
	 * send notification recipient
	 * @param  array $data
	 */
	public function send_notification_recipient($data)
	{
		if (!isset($data['status'])) {
			$data['status'] = '';
		}
		$this->db->select('subject');
		$this->db->where('id', $data['rel_id']);
		$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
		if ($requisition_leave) {
			$this->load->model('emails_model');
			$additional_data = $requisition_leave->subject;
			$mes = '';
			$link = 'timesheets/requisition_detail/' . $data['rel_id'];
			if (isset($data['staff_approve']) && isset($data['approve']) && is_numeric($data['approve'])) {
				if ($data['approve'] == 1) {
					$mes = $additional_data . ' ' . _l('ts_approved_by') . ' ' . get_staff_full_name($data['staff_approve']);
				} else {
					$mes = $additional_data . ' ' . _l('ts_rejected_by') . ' ' . get_staff_full_name($data['staff_approve']);
				}
			}
			if ($mes != '') {
				$this->db->select('notification_recipient');
				$this->db->where('related', "leave");
				$approval_setting = $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
				if ($approval_setting) {
					$notification_recipient = $approval_setting->notification_recipient;
					$arr_notification_recipient = explode(",", $notification_recipient);
					if (!in_array(get_staff_user_id(), $arr_notification_recipient)) {
						foreach ($arr_notification_recipient as $value) {
							$notified = add_notification([
								'description'     => $mes,
								'touserid'        => $value,
								'link'            => $link,
								'additional_data' => serialize([
									$additional_data,
								]),
							]);
							if ($notified) {
								pusher_trigger_notification([$value]);
							}
							$email = $this->get_staff_email($value);
							if ($email != '') {
								$this->emails_model->send_simple_email($email, _l('approval_notification'), $mes . '<br>' . _l('detail') . ': <a href="' . admin_url($link) . '">' . $additional_data . '</a>');
							}
						}
					}
				}
			}
		}
	}
	/**
	 * get date time
	 * @param  integer $work_shift
	 * @return array
	 */
	public function get_date_time($work_shift)
	{
		$day = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'sunday', 'saturday_odd', 'saturday_even'];
		$date_return = [];
		foreach ($day as $value) {
			$date_return['lunch_break'][$value] = (($work_shift[3][$value][0] - $work_shift[2][$value][0]) * 60) + ($work_shift[3][$value][1] - $work_shift[2][$value][1]);
			$date_return['work_time'][$value] = (($work_shift[1][$value][0] - $work_shift[0][$value][0]) * 60) + ($work_shift[1][$value][1] - $work_shift[0][$value][1]);
			$date_return['late_for_work'][$value] = $work_shift[0][$value][0] . ':' . $work_shift[0][$value][1] . ':00';
			$date_return['start_lunch_break_time'][$value] = $work_shift[2][$value][0] . ':' . $work_shift[2][$value][1] . ':00';
			$date_return['come_home_early'][$value] = $work_shift[1][$value][0] . ':' . $work_shift[1][$value][1] . ':00';
			$date_return['late_latency_allowed'][$value] = $work_shift[4][$value][0] . ':' . $work_shift[4][$value][1] . ':00';
			$date_return['start_afternoon_shift'][$value] = $work_shift[3][$value][0] . ':' . $work_shift[3][$value][1] . ':00';
		}
		return $date_return;
	}
	/**
	 * gets the overtime setting.
	 *
	 * @param      string  $id     the identifier
	 *
	 * @return     <type>  the overtime setting.
	 */
	public function get_overtime_setting($id = '')
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_overtime_setting')->row();
		}

		return $this->db->get(db_prefix() . 'timesheets_overtime_setting')->result_array();
	}
	public function add_overtime_setting($data)
	{
		$this->db->insert(db_prefix() . 'timesheets_overtime_setting', $data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}
	/**
	 * update overtime setting
	 * @param  array $data
	 * @param  integer $id
	 * @return bool
	 */
	public function update_overtime_setting($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'timesheets_overtime_setting', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete overtime setting
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_overtime_setting($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_overtime_setting');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get additional timesheets
	 * @param  integer $id
	 * @return array
	 */
	public function get_additional_timesheets($id = '')
	{
		if (is_numeric($id)) {
			$this->db->select('*');
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_additional_timesheet')->row();
		}

		if (!is_admin() && !has_permission('additional_timesheets_management', '', 'view')) {
			$this->db->where(' ' . get_staff_user_id() . ' in (select staffid from ' . db_prefix() . 'timesheets_approval_details where rel_type = "additional_timesheets" and rel_id = ' . db_prefix() . 'timesheets_additional_timesheet.id)');
		}
		$this->db->order_by('id', 'desc');
		return $this->db->get(db_prefix() . 'timesheets_additional_timesheet')->result_array();
	}
	/**
	 * get vacation days of the year
	 * @param  integer $staff_id
	 * @return integer $year
	 */
	public function get_requisition_number_of_day_off($staff_id, $year = false)
	{
		if ($year == false) {
			$year = date('Y');
		}
		$result_total_day_off = $this->get_day_off_by_year($staff_id, $year);
		if ($result_total_day_off) {
			$total_day_off_in_year = (float) $result_total_day_off->total;
			$total_day_off_allowed_in_year = (float) $result_total_day_off->remain;
		} else {
			$total_day_off_in_year = 0;
			$total_day_off_allowed_in_year = 0;
		}
		$data = [];
		$data['total_day_off_in_year'] = $total_day_off_in_year;
		$status_leave = $this->timesheets_model->get_number_of_days_off($staff_id, $year);
		$data['total_day_off_allowed_in_year'] = 0;
		$data['total_day_off'] = 0;
		if ($result_total_day_off != null) {
			$data['total_day_off_allowed_in_year'] = $status_leave - ($result_total_day_off->total - $result_total_day_off->remain);
			if ($data['total_day_off_allowed_in_year'] < 0) {
				$data['total_day_off_allowed_in_year'] = 0;
			}
			$data['total_day_off'] = $result_total_day_off->total - $result_total_day_off->remain;
		}
		return $data;
	}

	/**
	 * get date leave in month
	 * @param  [int] $staff_id
	 * @return [y-mm] $month
	 */
	public function get_date_leave_in_month($staff_id, $month)
	{
		if ($staff_id != '' && $staff_id != 0) {
			$this->db->where('date_format(date_work, "%Y-%m") = "' . $month . '" and staff_id = ' . $staff_id . ' and type = \'al\'');
			$timekeeping = $this->db->get(db_prefix() . 'timesheets_timesheet')->result_array();
			$count_timekeeping = 0;
			$count_result = 0;
			foreach ($timekeeping as $key => $value) {
				$hour_shift = $this->get_hour_shift_staff($staff_id, $value['date_work']);
				if ($hour_shift > 0) {
					$count_result += $value['value'] / $hour_shift;
				}
			}
			return number_format($count_result, 2);
		} else {
			return 1;
		}
	}

	/**
	 * gets the day off by year.
	 *
	 * @param      <type>  $staffid  the staffid
	 * @param      <type>  $year     the year
	 *
	 * @return     <type>  the day off by year.
	 */
	public function get_day_off_by_year($staffid, $year)
	{
		$this->db->where('staffid', $staffid);
		$this->db->where('year', $year);
		return $this->db->get(db_prefix() . 'timesheets_day_off')->row();
	}

	/**
	 * get allowance type
	 * @param  integer $id
	 * @return object or array
	 */
	public function get_allowance_type($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('type_id', $id);

			return $this->db->get(db_prefix() . 'allowance_type')->row();
		}

		if ($id == false) {
			return $this->db->get(db_prefix() . 'allowance_type')->result_array();
		}
	}
	/**
	 * update allowance type
	 * @param  $data
	 * @param  $id
	 * @return  boolean
	 */
	public function update_allowance_type($data, $id)
	{
		$data['allowance_val'] = reformat_currency($data['allowance_val']);
		$this->db->where('type_id', $id);
		$this->db->update(db_prefix() . 'allowance_type', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete allowance type
	 * @param  $id
	 * @return  boolean
	 */
	public function delete_allowance_type($id)
	{
		$this->db->where('type_id', $id);
		$this->db->delete(db_prefix() . 'allowance_type');
		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}
	/**
	 * get salary form
	 * @param  boolean $id
	 * @return object or array
	 */
	public function get_salary_form($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('form_id', $id);

			return $this->db->get(db_prefix() . 'salary_form')->row();
		}

		if ($id == false) {
			return $this->db->query('select * from ' . db_prefix() . 'salary_form')->result_array();
		}
	}
	/**
	 * add salary form
	 * @param array $data
	 * @return object or array
	 */
	public function add_salary_form($data)
	{
		$data['salary_val'] = reformat_currency($data['salary_val']);
		$this->db->insert(db_prefix() . 'salary_form', $data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}
	/**
	 * update salary form
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_salary_form($data, $id)
	{
		$data['salary_val'] = reformat_currency($data['salary_val']);
		$this->db->where('form_id', $id);
		$this->db->update(db_prefix() . 'salary_form', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete salary form
	 * @param $id
	 * @return boolean
	 */
	public function delete_salary_form($id)
	{
		$this->db->where('form_id', $id);
		$this->db->delete(db_prefix() . 'salary_form');
		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get province
	 * @return [type]
	 */
	public function get_province()
	{
		return $this->db->get(db_prefix() . 'province_city')->result_array();
	}
	public function get_procedure_retire($id = '')
	{
		if ($id == '') {
			return $this->db->get(db_prefix() . 'procedure_retire')->result_array();
		} else {
			$this->db->where('procedure_retire_id', $id);
			return $this->db->get(db_prefix() . 'procedure_retire')->result_array();
		}
	}
	/**
	 * get staff info
	 * @param  integer $staffid
	 * @return integer
	 */
	public function get_staff_info($staffid)
	{
		$this->db->where('staffid', $staffid);
		$results = $this->db->get(db_prefix() . 'staff')->row();
		return $results;
	}

	/**
	 * timesheets setting get allowance key
	 * @return array
	 */
	public function timesheets_setting_get_allowance_key()
	{
		$allowance_no_taxable = json_decode(get_timesheets_option('allowance_no_taxable'), true);
		$arr_allowance_key = [];
		if ($allowance_no_taxable) {
			foreach ($allowance_no_taxable as $allowance_key) {
				array_push($arr_allowance_key, $allowance_key['allowance_name']);
			}
		}
		return $arr_allowance_key;
	}

	/**
	 * get approval process
	 * @param  integer $id
	 * @return object array
	 */
	public function get_approval_process($id = '')
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_approval_setting')->row();
		}
		return $this->db->get(db_prefix() . 'timesheets_approval_setting')->result_array();
	}

	/**
	 * get_timesheet
	 * @param  integer $staffid
	 * @param  date $from_date
	 * @param  date $to_date
	 * @return array
	 */
	public function get_timesheet($staffid = '', $from_date = '', $to_date = '')
	{
		return $this->db->query('select * from ' . db_prefix() . 'timesheets_timesheet where staff_id = ' . $staffid . ' and date_work between \'' . $from_date . '\' and \'' . $to_date . '\'')->result_array();
	}
	/**
	 * report by working hours
	 */
	public function report_by_working_hours()
	{
		$months_report = $this->input->post('months_report');

		// $custom_date_select = '';
		// $custom_date_select1 = '';
		// if ($months_report != '') {
		// 	if (is_numeric($months_report)) {
		// 		// last month
		// 		if ($months_report == '1') {
		// 			$beginmonth = date('Y-m-01', strtotime('first day of last month'));
		// 			$endmonth   = date('Y-m-t', strtotime('last day of last month'));
		// 		} else {
		// 			$months_report = (int) $months_report;
		// 			$months_report--;
		// 			$beginmonth = date('Y-m-01', strtotime("-$months_report month"));
		// 			$endmonth   = date('Y-m-t');
		// 		}
		// 		$custom_date_select = '(ht.date_work between "' . $beginmonth . '" and "' . $endmonth . '")';
		// 		$custom_date_select1 = '(ht.additional_day between "' . $beginmonth . '" and "' . $endmonth . '")';
		// 	} elseif ($months_report == 'this_month') {
		// 		$custom_date_select = '(ht.date_work between "' . date('Y-m-01') . '" and "' . date('Y-m-t') . '")';
		// 		$custom_date_select1 = '(ht.additional_day between "' . date('Y-m-01') . '" and "' . date('Y-m-t') . '")';
		// 	} elseif ($months_report == 'this_year') {
		// 		$custom_date_select = '(ht.date_work between "' .
		// 		date('Y-m-d', strtotime(date('Y-01-01'))) .
		// 		'" and "' .
		// 		date('Y-m-d', strtotime(date('Y-12-31'))) . '")';

		// 		$custom_date_select1 = '(ht.additional_day between "' .
		// 		date('Y-m-d', strtotime(date('Y-01-01'))) .
		// 		'" and "' .
		// 		date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
		// 	} elseif ($months_report == 'last_year') {
		// 		$custom_date_select = '(ht.date_work between "' .
		// 		date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
		// 		'" and "' .
		// 		date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';

		// 		$custom_date_select1 = '(ht.additional_day between "' .
		// 		date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
		// 		'" and "' .
		// 		date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
		// 	} elseif ($months_report == 'custom') {
		// 		$from_date = to_sql_date($this->input->post('report_from'));
		// 		$to_date   = to_sql_date($this->input->post('report_to'));
		// 		if ($from_date == $to_date) {
		// 			$custom_date_select =  'ht.date_work ="' . $from_date . '"';
		// 			$custom_date_select1 =  'ht.additional_day ="' . $from_date . '"';
		// 		} else {
		// 			$custom_date_select = '(ht.date_work between "' . $from_date . '" and "' . $to_date . '")';
		// 			$custom_date_select1 = '(ht.additional_day between "' . $from_date . '" and "' . $to_date . '")';
		// 		}
		// 	}

		// }

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

		$staffquery = '';
		$staff = $this->get_staff_timekeeping_applicable_object();
		foreach ($staff as $k => $staffitem) {
			$staffquery .= $staffitem['staffid'] . ',';
		}
		if ($staffquery != '') {
			$staffquery = ' and staffid in (' . rtrim($staffquery, ',') . ')';
		}

		$data_timekeeping_form = get_timesheets_option('timekeeping_form');
		$data_timesheet = [];

		// $custom_date_select = ' AND '.$custom_date_select.$staffquery;
		// $custom_date_select1 = ' AND '.$custom_date_select1.$staffquery;

		$chart = [];
		$dpm = $this->departments_model->get();
		foreach ($dpm as $d) {
			$staff_list = $this->db->query('select staffid, firstname, lastname from ' . db_prefix() . 'staff where staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid = ' . $d['departmentid'] . ')' . $staffquery)->result_array();
			$data_timesheet = [];
			if ($data_timekeeping_form == 'timekeeping_task') {
				$data_timesheet = $this->timesheets_model->get_attendance_task($staff_list, '', '', $from_date, $to_date);
			} else {
				$data_timesheet = $this->timesheets_model->get_attendance_manual($staff_list, '', '', $from_date, $to_date);
			}

			$total = 0;
			foreach ($data_timesheet['staff_row_tk'] as $row_tk) {
				foreach ($row_tk as $value) {
					$split_value = explode(':', $value);
					if (isset($split_value[0])) {
						if ($split_value[0] == 'W') {
							$total += isset($split_value[1]) && is_numeric($split_value[1]) ? $split_value[1] : 0;
						}
					}
				}
			}
			$chart['categories'][] = $d['name'];
			$chart['total_work_hours'][] = $total;
			$chart['total_work_hours_approved'][] = $this->count_work_hours_approve($d['departmentid'], $from_date, $to_date);
		}
		return $chart;
	}

	/**
	 * count work hours approve
	 * @param  integer $department
	 * @param  date $custom_date_select1
	 * @return integer
	 */
	public function count_work_hours_approve($department, $from_date, $to_date)
	{
		$list_app = $this->db->query('select ht.creator, ht.additional_day, ht.timekeeping_value from ' . db_prefix() . 'timesheets_additional_timesheet ht left join ' . db_prefix() . 'staff_departments sd on sd.staffid = ht.creator where sd.departmentid = ' . $department . ' and ht.status = 1 and ht.additional_day between "' . $from_date . '" and "' . $to_date . '"')->result_array();
		$sum = 0;
		if (count($list_app) > 0) {
			foreach ($list_app as $lis) {
				if (is_numeric($lis['timekeeping_value'])) {
					$sum += $lis['timekeeping_value'];
				}
			}
		}
		return $sum;
	}
	/**
	 * add approval process
	 * @param array $data
	 * @return boolean
	 */
	public function add_approval_process($data)
	{
		unset($data['approval_setting_id']);

		if (isset($data['staff'])) {
			$setting = [];
			foreach ($data['staff'] as $key => $value) {
				$node = [];
				$node['approver'] = (isset($data['approver'][$key]) ? $data['approver'][$key] : 'specific_personnel');
				$node['staff'] = $data['staff'][$key];

				$setting[] = $node;
			}
			unset($data['approver']);
			unset($data['staff']);
		}

		if (!isset($data['choose_when_approving'])) {
			$data['choose_when_approving'] = 0;
		}

		if (isset($data['departments'])) {
			$data['departments'] = implode(',', $data['departments']);
		} else {
			$data['departments'] = '';
		}

		if (isset($data['job_positions'])) {
			$data['job_positions'] = implode(',', $data['job_positions']);
		} else {
			$data['job_positions'] = '';
		}

		$data['setting'] = json_encode($setting);

		if (isset($data['notification_recipient'])) {
			$data['notification_recipient'] = implode(",", $data['notification_recipient']);
		} else {
			$data['notification_recipient'] = '';
		}

		$this->db->insert(db_prefix() . 'timesheets_approval_setting', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return true;
		}
		return false;
	}
	/**
	 * update approval process
	 * @param  integer $id
	 * @param  array $data
	 * @return boolean
	 */
	public function update_approval_process($id, $data)
	{
		if (isset($data['staff'])) {
			$setting = [];
			foreach ($data['staff'] as $key => $value) {
				$node = [];
				$node['approver'] = (isset($data['approver'][$key]) ? $data['approver'][$key] : 'specific_personnel');
				$node['staff'] = $data['staff'][$key];

				$setting[] = $node;
			}
			unset($data['approver']);
			unset($data['staff']);
		}

		if (!isset($data['choose_when_approving'])) {
			$data['choose_when_approving'] = 0;
		}

		$data['setting'] = json_encode($setting);

		if (isset($data['departments'])) {
			$data['departments'] = implode(',', $data['departments']);
		} else {
			$data['departments'] = '';
		}

		if (isset($data['job_positions'])) {
			$data['job_positions'] = implode(',', $data['job_positions']);
		} else {
			$data['job_positions'] = '';
		}

		if (isset($data['notification_recipient'])) {
			$data['notification_recipient'] = implode(",", $data['notification_recipient']);
		} else {
			$data['notification_recipient'] = '';
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'timesheets_approval_setting', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete approval setting
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_approval_setting($id)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'timesheets_approval_setting');

			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
		return false;
	}
	public function setting_timekeeper($data)
	{
		$affectedrows = 0;
		if (isset($data['timekeeping_task_role'])) {

			$timekeeping_task_role = implode(',', $data['timekeeping_task_role']);

			$this->db->where('option_name', 'timekeeping_task_role');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $timekeeping_task_role,
			]);
		} else {
			$this->db->where('option_name', 'timekeeping_task_role');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '',
			]);
		}
		if ($this->db->affected_rows() > 0) {
			$affectedrows++;
		}

		if (isset($data['timekeeping_manually_role'])) {
			$timekeeping_manually_role = implode(',', $data['timekeeping_manually_role']);
			$this->db->where('option_name', 'timekeeping_manually_role');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $timekeeping_manually_role,
			]);
		} else {
			$this->db->where('option_name', 'timekeeping_manually_role');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '',
			]);
		}
		if ($this->db->affected_rows() > 0) {
			$affectedrows++;
		}

		if (isset($data['csv_clsx_role'])) {

			$csv_clsx_role = implode(',', $data['csv_clsx_role']);

			$this->db->where('option_name', 'csv_clsx_role');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $csv_clsx_role,
			]);
		} else {
			$this->db->where('option_name', 'csv_clsx_role');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '',
			]);
		}
		if ($this->db->affected_rows() > 0) {
			$affectedrows++;
		}

		if (isset($data['timekeeping_form'])) {

			$timekeeping_form = $data['timekeeping_form'];

			$this->db->where('option_name', 'timekeeping_form');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $timekeeping_form,
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}

		if ($affectedrows > 0) {
			return true;
		}
		return false;
	}

	public function edit_timesheets($data, $staffid = '')
	{
		if ($staffid != '') {
			$staff_id = $staffid;
		} else {
			$staff_id = get_staff_user_id();
		}
		$additional_day = $data->additional_day;
		$data_ts = $data->time_in . ':00';
		$data_te = $data->time_out . ':00';

		$time_in = $additional_day . ' ' . $data_ts;
		$time_out = $additional_day . ' ' . $data_te;
		$staff = $this->staff_model->get($data->creator);
		$data_new = [];

		if ($data->time_in != '' && $data->time_out != '') {
			$data_work_time = $this->get_hour_shift_staff($staff_id, $data->additional_day);
			if ($data_work_time != 0) {
				$this->db->where('staff_id', $staff_id);
				$this->db->where('date_work', $data->additional_day);
				$this->db->where('type', 'W');
				$tslv = $this->db->get(db_prefix() . 'timesheets_timesheet')->row();
				if ($tslv) {
					$new_value = $tslv->value + $data->timekeeping_value;
					if ($new_value > $data_work_time) {
						$new_value = $data_work_time;
					}
					$this->db->where('id', $tslv->id);
					$this->db->update(db_prefix() . 'timesheets_timesheet', ['value' => $new_value]);
				} else {
					$this->automatic_insert_timesheets($staff_id, $time_in, $time_out);
				}
			}
		} else {
			if ($data->timekeeping_value > 0) {
				$data_work_time = $this->get_hour_shift_staff($staff_id, $data->additional_day);
				if ($data_work_time != 0) {

					$this->db->where('staff_id', $staff_id);
					$this->db->where('date_work', $data->additional_day);
					$this->db->where('type', 'w');
					$tslv = $this->db->get(db_prefix() . 'timesheets_timesheet')->row();
					if ($tslv) {
						$new_value = $tslv->value + $data->timekeeping_value;
						if ($new_value > $data_work_time) {
							$new_value = $data_work_time;
						}
						$this->db->where('id', $tslv->id);
						$this->db->update(db_prefix() . 'timesheets_timesheet', ['value' => $new_value]);
					} else {
						$this->db->insert(db_prefix() . 'timesheets_timesheet', [
							'value' => $data->timekeeping_value,
							'type' => 'w',
							'staff_id' => $staff_id,
							'add_from' => get_staff_user_id(),
							'date_work' => $data->additional_day,
						]);
					}
				}
			}
		}
		return true;
	}
	/**
	 * add additional timesheets
	 * @param array $data
	 * @param integer $staffid
	 */
	public function add_additional_timesheets($data, $staffid = '', $created_id = '')
	{
		if ($staffid == '') {
			$staff_id = get_staff_user_id();
		} else {
			$staff_id = $staffid;
		}
		if ($data['time_in'] == 'null' || $data['time_in'] == '') {
			$data['time_in'] = '';
		}

		if ($data['time_out'] == 'null' || $data['time_out'] == '') {
			$data['time_out'] = '';
		}
		if (($data['timekeeping_value'] == '0' || $data['timekeeping_value'] == '') && $data['time_in'] != '' && $data['time_out'] != '') {
			if (isset($data['timekeeping_type']) && $data['timekeeping_type'] == 'w') {
				$rest_time = number_format($this->get_rest_time($data['additional_day'], $staff_id) / 60, 2);
				$data['timekeeping_value'] = ((strtotime($data['time_out'] . ':00') - strtotime($data['time_in'] . ':00')) / 3600) - $rest_time;
			} else {
				$data['timekeeping_value'] = (strtotime($data['time_out'] . ':00') - strtotime($data['time_in'] . ':00')) / 3600;
			}
		}

		if ($data['timekeeping_value'] < 0) {
			$data['timekeeping_value'] = 0;
		}

		if ($data['timekeeping_value'] != '0' && $data['timekeeping_value'] != '') {
			$data['timekeeping_value'] = number_format($data['timekeeping_value'], 1);
		}

		if (is_numeric($created_id)) {
			$data['creator'] = $created_id;
		} else {
			$data['creator'] = $staff_id;
		}

		$data['staff_id'] = get_staff_user_id();
		$data['additional_day'] = to_sql_date($data['additional_day']);
		$data['status'] = '0';
		$this->db->insert(db_prefix() . 'timesheets_additional_timesheet', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			$check_proccess = $this->timesheets_model->get_approve_setting('additional_timesheets');
			if ($check_proccess) {
				$checks = $this->timesheets_model->check_choose_when_approving('additional_timesheets');
				if ($checks == 0) {
					// Has approve setting but not choose when approve
					$data_new = [];
					$data_new['rel_id'] = $insert_id;
					$data_new['rel_type'] = 'additional_timesheets';
					$data_new['addedfrom'] = $data['creator'];
					$success = $this->send_request_approve($data_new, $staffid);
					if ($success) {
						if ($staffid == '') {
							$this->send_mail($data_new);
						}
					}
				}
			}
			return $insert_id;
		}
		return false;
	}
	/**
	 * get hour shift staff
	 * @param  integer $staff_id
	 * @param  integer $date
	 * @return integer
	 */
	public function get_info_hour_shift_staff($staff_id, $date)
	{
		$result = new stdclass();
		$result->woking_hour = 0;
		$result->lunch_break_hour = 0;

		$result->start_working = '';
		$result->end_working = '';
		$result->start_lunch_break = '';
		$result->end_lunch_break = '';

		$woking_hour = 0;
		$lunch_break_hour = 0;

		$data_shift_list = $this->get_shift_work_staff_by_date($staff_id, $date);
		foreach ($data_shift_list as $ss) {
			$data_shift_type = $this->get_shift_type($ss);
			if ($data_shift_type) {
				$woking_hour = $this->get_hour($data_shift_type->time_start_work, $data_shift_type->time_end_work);
				$lunch_break_hour = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
				$result->start_working = $data_shift_type->time_start_work;
				$result->end_working = $data_shift_type->time_end_work;
				$result->start_lunch_break = $data_shift_type->start_lunch_break_time;
				$result->end_lunch_break = $data_shift_type->end_lunch_break_time;
				$result->woking_hour = abs($woking_hour - $lunch_break_hour);
				$result->lunch_break_hour = $lunch_break_hour;
			}
		}
		return $result;
	}

	/**
	 * gets the file requisition.
	 *
	 * @param      int   $id      the identifier
	 * @param      boolean  $rel_id  the relative identifier
	 *
	 * @return     object   the file requisition.
	 */
	public function get_file_requisition($id, $rel_id = false)
	{
		if (is_client_logged_in()) {
			$this->db->where('visible_to_customer', 1);
		}
		$this->db->where('id', $id);
		$file = $this->db->get(db_prefix() . 'files')->row();

		if ($file && $rel_id) {
			if ($file->rel_id != $rel_id) {
				return $file;
			}
		}

		return $file;
	}

	/**
	 * automatic insert timesheets
	 *
	 * @param      int   $staffid   the staffid
	 * @param      integer  $time_in   the time in
	 * @param      integer  $time_out  the time out
	 *
	 * @return     boolean
	 */
	public function automatic_insert_timesheets($staffid, $time_in, $time_out)
	{

		$date_work = date('Y-m-d', strtotime($time_in));
		$work_time = $this->get_hour_shift_staff($staffid, $date_work);
		$affectedrows = 0;
		if ($work_time > 0 && $work_time != '') {
			$list_shift = $this->get_shift_work_staff_by_date($staffid, $date_work);

			$d1 = strtotime($this->format_date_time($time_in));
			$d2 = strtotime($this->format_date_time($time_out));
			if ($d1 > $d2) {
				$temp = $time_in;
				$time_in = $time_out;
				$time_out = $temp;
			}
			$hour1 = explode(' ', $time_in);
			$hour2 = explode(' ', $time_out);
			$time_in = strtotime($hour1[1]);
			$time_out = strtotime($hour2[1]);

			$hour = 0;
			$late = 0;
			$early = 0;
			$lunch_time = 0;
			foreach ($list_shift as $shift) {
				$data_shift_type = $this->timesheets_model->get_shift_type($shift);

				$time_in_ = $time_in;
				$time_out_ = $time_out;

				if ($data_shift_type) {
					$start_work = strtotime($data_shift_type->time_start_work);
					$end_work = strtotime($data_shift_type->time_end_work);
					$start_lunch_break = strtotime($data_shift_type->start_lunch_break_time);
					$end_lunch_break = strtotime($data_shift_type->end_lunch_break_time);
					if ($time_out < $start_work) {
						continue;
					}

					if ($time_out > $start_lunch_break && $time_out < $end_lunch_break) {
						$time_out_ = $start_lunch_break;
					}

					if ($time_in > $start_lunch_break && $time_in < $end_lunch_break) {
						$time_in_ = $end_lunch_break;
					}

					if ($time_in_ < $start_lunch_break && $time_out_ > $end_lunch_break) {
						$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
					}
					if ($time_in_ == $start_lunch_break && $time_out_ == $end_lunch_break) {
						continue;
					}
					if ($time_in_ == $start_lunch_break && $time_out_ > $end_lunch_break) {
						$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
					}
					if ($time_in_ < $start_lunch_break && $time_out_ == $end_lunch_break) {
						$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
					}

					if ($time_in < $start_work && $time_out > $start_work) {
						$time_in_ = $start_work;
					} elseif ($time_in > $start_work && $time_out > $start_work) {
						if ($time_in >= $start_lunch_break && $time_in <= $end_lunch_break) {
							$time_in = $start_lunch_break;
						}
						$lunch_time_s = 0;
						if ($time_in > $end_lunch_break) {
							$lunch_time_s = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
						}
						$late += round(abs($time_in - $start_work) / (60 * 60), 2) - $lunch_time_s;
					}

					if ($time_out > $end_work && $time_in < $end_work) {
						$time_out_ = $end_work;
					} elseif ($time_out < $end_work && $time_in < $end_work) {
						if ($time_out >= $start_lunch_break && $time_out <= $end_lunch_break) {
							$time_out = $end_lunch_break;
						}
						$lunch_time_s = 0;
						if ($time_out < $end_lunch_break) {
							$lunch_time_s = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
						}
						$early += round(abs($time_out - $end_work) / (60 * 60), 2) - $lunch_time_s;
					}
					$hour += round(abs($time_out_ - $time_in_) / (60 * 60), 2);
				}
			}
			$value = abs($hour - $lunch_time);
			$this->db->where('date_work', $date_work);
			$this->db->where('staff_id', $staffid);
			$this->db->where('type', 'L');
			$this->db->delete(db_prefix() . 'timesheets_timesheet');

			$this->db->where('date_work', $date_work);
			$this->db->where('staff_id', $staffid);
			$this->db->where('type', 'E');
			$this->db->delete(db_prefix() . 'timesheets_timesheet');

			$this->db->where('date_work', $date_work);
			$this->db->where('staff_id', $staffid);
			$this->db->where('type', 'W');
			$this->db->delete(db_prefix() . 'timesheets_timesheet');

			if ($value > 0) {
				$this->db->insert(
					db_prefix() . 'timesheets_timesheet',
					[
						'value' => $value,
						'date_work' => $date_work,
						'staff_id' => $staffid,
						'type' => 'W',
						'add_from' => get_staff_user_id(),
					]
				);

				$insert_id = $this->db->insert_id();

				if ($insert_id) {
					$affectedrows++;
				}
			}

			if ($late > 0) {
				$this->db->insert(
					db_prefix() . 'timesheets_timesheet',
					[
						'value' => $late,
						'date_work' => $date_work,
						'staff_id' => $staffid,
						'type' => 'L',
						'add_from' => get_staff_user_id(),
					]
				);

				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$affectedrows++;
				}
			}

			if ($early > 0) {
				$this->db->insert(
					db_prefix() . 'timesheets_timesheet',
					[
						'value' => $early,
						'date_work' => $date_work,
						'staff_id' => $staffid,
						'type' => 'E',
						'add_from' => get_staff_user_id(),
					]
				);
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$affectedrows++;
				}
			}
		}
		if ($affectedrows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * adds an update timesheet.
	 *
	 * @param      object   $data           the data
	 * @param      boolean  $is_timesheets  indicates if timesheets
	 *
	 * @return     integer
	 */
	public function add_update_timesheet($data, $is_timesheets = false)
	{
		$type_valid = ['AL', 'W', 'U', 'HO', 'E', 'L', 'B', 'SI', 'M', 'ME', 'NS', 'P', 'OFF', 'H/F', 'W/H', 'N/A'];
		$data_type_of_leave = $this->get_type_of_leave();
		foreach ($data_type_of_leave as $key => $value) {
			$type_valid[] = $value['symbol'];
		}

		$results = 0;
		foreach ($data as $row) {
			foreach ($row as $key => $val) {
				if ($key != 'staff_id' && $key != 'staff_name') {
					if ($is_timesheets === true) {
						$this->db->where('staff_id', $row['staff_id']);
						$this->db->where('date_work', $key);
						$this->db->delete(db_prefix() . 'timesheets_timesheet');
					}

					// Handle simple values like "P" without colon
					if (in_array(strtoupper(trim($val)), $type_valid)) {
						if (strtoupper(trim($val)) == 'P' || strtoupper(trim($val)) == 'L' || strtoupper(trim($val)) == 'W/H' || strtoupper(trim($val)) == 'H/F' || strtoupper(trim($val)) == 'N/A' || strtoupper(trim($val)) == 'OFF') {
							$this->db->insert(db_prefix() . 'timesheets_timesheet', [
								'staff_id' => $row['staff_id'],
								'date_work' => $key,
								'value' => '',
								'add_from' => get_staff_user_id(),
								'type' => strtoupper(trim($val)),
							]);
							$insert_id = $this->db->insert_id();
							if ($insert_id) {
								$results++;
							}
						}
						continue; // Skip the rest of processing for this value
					}

					// Original processing for values with colon (like "W:9.5")
					$ts = explode(";", $val);
					foreach ($ts as $ex) {
						$value = explode(':', trim($ex));

						if ((isset($value[0]) && ctype_alpha($value[0]) && in_array(strtoupper($value[0]), $type_valid)) && (isset($value[1]) && is_numeric($value[1]))) {
							$this->db->where('staff_id', $row['staff_id']);
							$this->db->where('date_work', $key);
							$this->db->where('type', strtoupper($value[0]));
							$isset = $this->db->get(db_prefix() . 'timesheets_timesheet')->row();

							if ($isset) {
								$this->db->where('staff_id', $row['staff_id']);
								$this->db->where('date_work', $key);
								$this->db->where('type', strtoupper($value[0]));
								$this->db->update(db_prefix() . 'timesheets_timesheet', [
									'value' => isset($value[1]) ? $value[1] : '',
									'add_from' => get_staff_user_id(),
									'type' => strtoupper(isset($value[0]) ? $value[0] : $ex),
								]);
								if ($this->db->affected_rows() > 0) {
									$results++;
								}
							} else {
								if ($val != '') {
									$this->db->insert(db_prefix() . 'timesheets_timesheet', [
										'staff_id' => $row['staff_id'],
										'date_work' => $key,
										'value' => isset($value[1]) ? $value[1] : '',
										'add_from' => get_staff_user_id(),
										'type' => strtoupper(isset($value[0]) ? $value[0] : $ex),
									]);
									$insert_id = $this->db->insert_id();
									if ($insert_id) {
										$results++;
									}
								}
							}

							if ($val == '') {
								$this->db->where('staff_id', $row['staff_id']);
								$this->db->where('date_work', $key);
								$this->db->delete(db_prefix() . 'timesheets_timesheet');
								if ($this->db->affected_rows() > 0) {
									$results++;
								}
							}
						}
					}
				}
			}
		}
		return $results;
	}

	/**
	 * latch timesheet
	 *
	 * @param      string   $month  the month
	 *
	 * @return     boolean
	 */
	public function latch_timesheet($month)
	{
		if ($month != '') {
			$this->db->insert(db_prefix() . 'timesheets_latch_timesheet', [
				'month_latch' => $month,
			]);
			$insert_id = $this->db->insert_id();

			if ($insert_id) {
				$m = date('m', strtotime('01-' . $month));
				$y = date('y', strtotime('01-' . $month));
				$this->db->where('month(date_work) = ' . $m . ' and year(date_work) = ' . $y);
				$this->db->update(db_prefix() . 'timesheets_timesheet', ['latch' => 1]);

				return true;
			} else {
				return false;
			}
		}

		return false;
	}

	/**
	 * gets the taskstimers.
	 *
	 * @param      int  $task_id   the task identifier
	 * @param      int  $staff_id  the staff identifier
	 *
	 * @return     array  the taskstimers.
	 */
	public function get_taskstimers($staff_id, $where)
	{
		$this->db->where($where);
		$this->db->where('staff_id', $staff_id);
		$this->db->select('*, CASE
			WHEN end_time is NULL THEN (' . time() . '-start_time) / 60 / 60
			ELSE (end_time-start_time) / 60 / 60
			END as total_logged_time, from_unixtime(start_time, \'%Y-%m-%d %H:%i:%s\') as start_time, from_unixtime(end_time, \'%Y-%m-%d %H:%i:%s\') as end_time');
		$this->db->order_by('id', 'desc');
		return $this->db->get(db_prefix() . 'taskstimers')->result_array();
	}

	public function get_data_insert_timesheets($staffid, $time_in, $time_out)
	{
		$date_work = date('Y-m-d', strtotime($time_in));
		$work_time = $this->get_hour_shift_staff($staffid, $date_work);
		$affectedrows = 0;
		$hour = 0;
		$late = 0;
		$early = 0;
		$lunch_time = 0;
		if ($work_time > 0 && $work_time != '') {
			$list_shift = $this->get_shift_work_staff_by_date($staffid, $date_work);
			$d1 = strtotime($this->format_date_time($time_in));
			$d2 = strtotime($this->format_date_time($time_out));
			if ($d1 > $d2) {
				$temp = $time_in;
				$time_in = $time_out;
				$time_out = $temp;
			}
			$hour1 = explode(' ', $time_in);
			$hour2 = explode(' ', $time_out);
			$time_in = strtotime($hour1[1]);
			$time_out = strtotime($hour2[1]);
			foreach ($list_shift as $shift) {
				$data_shift_type = $this->timesheets_model->get_shift_type($shift);
				$time_in_ = $time_in;
				$time_out_ = $time_out;
				if ($data_shift_type) {

					$start_work = strtotime($data_shift_type->time_start_work);
					$end_work = strtotime($data_shift_type->time_end_work);
					$start_lunch_break = strtotime($data_shift_type->start_lunch_break_time);
					$end_lunch_break = strtotime($data_shift_type->end_lunch_break_time);
					if ($time_out < $start_work) {
						continue;
					}

					if ($time_in < $start_work && $time_out > $start_work) {
						$time_in_ = $start_work;
					} elseif ($time_in > $start_work && $time_out > $start_work) {
						$late += round(abs($time_in - $start_work) / (60 * 60), 2);
					}

					if ($time_out > $end_work && $time_in < $end_work) {
						$time_out_ = $end_work;
					} elseif ($time_out < $end_work && $time_in < $end_work) {
						$early += round(abs($time_out - $end_work) / (60 * 60), 2);
					}

					if ($time_out_ >= $end_lunch_break) {
						$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
					}
					$hour += round(abs($time_out_ - $time_in_) / (60 * 60), 2);
				}
			}
		}

		$value = abs($hour - $lunch_time);
		$data = [];
		$data['work'] = $value;
		$data['early'] = $early;
		$data['late'] = $late;
		return $data;
	}

	public function import_timesheets($data)
	{
		foreach ($data as $key => $value) {
			$test = $this->check_ts($value['staffid'], date('Y-m-d', strtotime($value['time_in'])));
			if ($test->check_in != 0) {
				$this->db->where('id', $test->check_in);
				$this->db->update(db_prefix() . 'check_in_out', ['date' => $value['time_in']]);
			} else {
				$this->db->insert(
					db_prefix() . 'check_in_out',
					[
						'staff_id' => $value['staffid'],
						'date' => $value['time_in'],
						'type_check' => 1
					]
				);
			}

			if ($test->check_out != 0) {
				$this->db->where('id', $test->check_out);
				$this->db->update(db_prefix() . 'check_in_out', ['date' => $value['time_out']]);
			} else {
				$this->db->insert(
					db_prefix() . 'check_in_out',
					[
						'staff_id' => $value['staffid'],
						'date' => $value['time_out'],
						'type_check' => 2
					]
				);
			}
			$this->automatic_insert_timesheets($value['staffid'], $value['time_in'], $value['time_out']);
		}

		return true;
	}

	/**
	 * delete additional timesheets
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_additional_timesheets($id)
	{

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_additional_timesheet');
		if ($this->db->affected_rows() > 0) {
			$this->db->where('relate_id', $id);
			$this->db->where('relate_type', 'additional_timesheet');
			$this->db->delete(db_prefix() . 'timesheets_timesheet');
			return true;
		}
		return false;
	}

	/**
	 * delete timesheets attchement file for any
	 *
	 * @param      <type>   $attachment_id  the attachment identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function delete_timesheets_attachment_file($attachment_id)
	{
		$deleted = false;
		$attachment = $this->get_timesheets_attachments_delete($attachment_id);
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(timesheets_module_upload_folder . '/requisition_leave/' . $attachment->rel_id . '/' . $attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('attachment deleted [requisition leave id: ' . $attachment->rel_id . ']');
			}

			if (is_dir(timesheets_module_upload_folder . '/requisition_leave/' . $attachment->rel_id)) {
				// check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(timesheets_module_upload_folder . '/requisition_leave/' . $attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(timesheets_module_upload_folder . '/requisition_leave/' . $attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * gets the timesheets attachments delete.
	 *
	 * @param      int  $id     the identifier
	 *
	 * @return     object  the timesheets attachments delete.
	 */
	public function get_timesheets_attachments_delete($id)
	{

		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'files')->row();
		} else {
			return [];
		}
	}

	/**
	 * get list check in/out
	 * @param  $date
	 * @param  string $staffid
	 * @return
	 */
	public function get_list_check_in_out($date, $staffid = '', $route_point_id = '')
	{
		if ($staffid != '') {
			$this->db->where('staff_id', $staffid);
		}
		if ($route_point_id != '') {
			$this->db->where('route_point_id', $route_point_id);
		}
		$this->db->where('date(date) = "' . $date . '"');
		$this->db->order_by('id');
		return $this->db->get(db_prefix() . 'check_in_out')->result_array();
	}
	/**
	 * get ts staff by date
	 * @param  integer $staff_id
	 * @param  date $date_work
	 */
	public function get_ts_staff_by_date($staff_id, $date_work)
	{
		return $this->db->query('select * from ' . db_prefix() . 'timesheets_timesheet where staff_id = ' . $staff_id . ' and date_work = \'' . $date_work . '\'')->result_array();
	}
	/**
	 * merge timesheet
	 * @param  string $string
	 * @param  string $max_hour
	 * @return string
	 */
	public function merge_ts($string, $max_hour, $type_valid)
	{
		if ($string != '') {
			$array = explode(';', $string);
			$list_type = [];
			$special_cases = ['H/F', 'W/H', 'N/A']; // Add any other special cases here

			// First check if this is a special case that shouldn't be processed
			if (count($array) == 1) {
				$trimmed = trim($array[0]);
				if (in_array($trimmed, $special_cases)) {
					return $trimmed; // Return the special case as-is
				}
			}

			foreach ($array as $key => $value) {
				$value = trim($value);
				$split = explode(':', $value);

				// Skip processing if this is a special case
				if (in_array($value, $special_cases)) {
					continue;
				}

				if (isset($split[0]) && isset($split[1])) {
					if (count($list_type) == 0) {
						array_push($list_type, $split[0]);
					} elseif (!in_array($split[0], $list_type)) {
						array_push($list_type, $split[0]);
					}
				} else {
					if ((isset($split[0]) && ctype_alpha($split[0]) && in_array(strtoupper($split[0]), $type_valid))) {
						return $string;
					}
				}
			}

			// If we have special cases mixed with regular entries, return original
			foreach ($array as $value) {
				$value = trim($value);
				if (in_array($value, $special_cases)) {
					return $string;
				}
			}

			$array_result = [];
			foreach ($list_type as $key => $type) {
				$type = str_replace(' ', '', $type);
				$total = 0;
				foreach ($array as $key => $value) {
					$split = explode(':', trim($value));

					if ((isset($split[0]) && ctype_alpha($split[0]) && in_array(strtoupper($split[0]), $type_valid)) && (isset($split[1]) && is_numeric($split[1]))) {
						if (str_replace(' ', '', $split[0]) == $type) {
							$total += $split[1];
						}
					}
				}

				if ($total > 0) {
					$array_result[] = $type . ':' . $total;
				}
			}

			if (count($array_result) > 0) {
				return implode('; ', $array_result);
			} else {
				// Check if we had only special cases that were skipped
				foreach ($array as $value) {
					$value = trim($value);
					if (in_array($value, $special_cases)) {
						return $value;
					}
				}
				return '';
			}
		} else {
			return '';
		}
	}
	/**
	 * get staff member/s
	 * @param  mixed $id optional - staff id
	 * @param  mixed $where where in query
	 * @return mixed if id is passed return object else array
	 */
	public function get_staff_list($where = '')
	{
		return $this->db->query('select * from ' . db_prefix() . 'staff ' . $where)->result_array();
	}
	/**
	 * gets the go bussiness advance payment.
	 *
	 * @param      <type>  $request_leave  the request leave
	 */
	public function get_go_bussiness_advance_payment($request_leave)
	{
		$this->db->where('requisition_leave', $request_leave);
		return $this->db->get(db_prefix() . 'timesheets_go_bussiness_advance_payment')->result_array();
	}
	/**
	 * advance payment update     * @param  $id   integer
	 * @param  $data array
	 * @return boolean
	 */
	public function advance_payment_update($id, $data)
	{
		$data['amount_received'] = timesheets_reformat_currency_asset($data['amount_received']);
		$data['received_date'] = to_sql_date($data['received_date']);
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'timesheets_requisition_leave', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * check_holiday
	 * @param  integer $staffid
	 * @param  dagte $date
	 * @return object
	 */
	public function check_holiday($staffid, $date)
	{
		$department_id = '';
		$role_id = '';
		$departments = $this->departments_model->get_staff_departments($staffid, true);
		$staff_data = $this->staff_model->get($staffid);
		if ($departments) {
			$department_id = $departments[0];
		}
		if ($staff_data) {
			$role_id = $staff_data->role;
		}
		$add_query = '';
		if ($department_id != '' && $role_id != '') {
			$add_query .= 'and (find_in_set(' . $departments[0] . ',department) or find_in_set(' . $role_id . ', position))';
		}
		if ($department_id == '' && $role_id != '') {
			$add_query .= 'and find_in_set(' . $role_id . ', position)';
		}
		if ($department_id != '' && $role_id == '') {
			$add_query .= 'and find_in_set(' . $departments[0] . ',department)';
		}
		$data = $this->db->query('select * from ' . db_prefix() . 'day_off where break_date = \'' . $date . '\' ' . $add_query . ' and repeat_by_year = 0 limit 1')->row();
		if (!$data) {
			$data = $this->db->query('select * from ' . db_prefix() . 'day_off where break_date = \'' . $date . '\' and department = \'\' and position = \'\' and repeat_by_year = 0 limit 1')->row();
			if (!$data) {
				$data = $this->db->query('select * from ' . db_prefix() . 'day_off where day(break_date) = day(\'' . $date . '\') and month(break_date) = month(\'' . $date . '\') ' . $add_query . ' and repeat_by_year = 1 limit 1')->row();
				if (!$data) {
					$data = $this->db->query('select * from ' . db_prefix() . 'day_off where day(break_date) = day(\'' . $date . '\') and month(break_date) = month(\'' . $date . '\') and department = \'\' and position = \'\' and repeat_by_year = 1 limit 1')->row();
				}
			}
		}
		return $data;
	}
	/**
	 * get staff email
	 * @param  integer $staffid
	 * @return string $email
	 */
	public function get_staff_email($staffid)
	{
		$this->db->where('staffid', $staffid);
		$this->db->select('email');
		$email = '';
		$data = $this->db->get(db_prefix() . 'staff')->row();
		if ($data) {
			$email = $data->email;
		}
		return $email;
	}
	/**
	 * default settings
	 * @param  array $data
	 * @return boolean
	 */
	public function default_settings($data)
	{
		$affectedrows = 0;
		if (isset($data['attendance_notice_recipient'])) {
			$attendance_notice_recipient = implode(',', $data['attendance_notice_recipient']);
			$this->db->where('option_name', 'attendance_notice_recipient');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $attendance_notice_recipient,
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'attendance_notice_recipient');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['allows_updating_check_in_time'])) {
			$this->db->where('option_name', 'allows_updating_check_in_time');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['allows_updating_check_in_time'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'allows_updating_check_in_time');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['allows_to_choose_an_older_date'])) {
			$this->db->where('option_name', 'allows_to_choose_an_older_date');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['allows_to_choose_an_older_date'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'allows_to_choose_an_older_date');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['allow_attendance_by_coordinates'])) {
			$this->db->where('option_name', 'allow_attendance_by_coordinates');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['allow_attendance_by_coordinates'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'allow_attendance_by_coordinates');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['allow_attendance_by_route'])) {
			$this->db->where('option_name', 'allow_attendance_by_route');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['allow_attendance_by_route'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'allow_attendance_by_route');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['googlemap_api_key'])) {
			$this->db->where('option_name', 'googlemap_api_key');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['googlemap_api_key'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['auto_checkout'])) {
			$this->db->where('option_name', 'auto_checkout');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['auto_checkout'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'auto_checkout');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}

		if (isset($data['auto_checkout_type'])) {
			$this->db->where('option_name', 'auto_checkout_type');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['auto_checkout_type'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'auto_checkout_type');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['auto_checkout_value'])) {
			$this->db->where('option_name', 'auto_checkout_value');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['auto_checkout_value'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}

		if (isset($data['send_notification_if_check_in_forgotten'])) {
			$this->db->where('option_name', 'send_notification_if_check_in_forgotten');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['send_notification_if_check_in_forgotten'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'send_notification_if_check_in_forgotten');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['send_notification_if_check_in_forgotten_value'])) {
			$this->db->where('option_name', 'send_notification_if_check_in_forgotten_value');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['send_notification_if_check_in_forgotten_value'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}

		if (isset($data['start_month_for_annual_leave_cycle'])) {
			$this->db->where('option_name', 'start_month_for_annual_leave_cycle');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['start_month_for_annual_leave_cycle'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'start_month_for_annual_leave_cycle');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '1',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['hour_notification_approval_exp'])) {
			$this->db->where('option_name', 'hour_notification_approval_exp');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['hour_notification_approval_exp'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['send_email_check_in_out_customer_location'])) {
			$this->db->where('option_name', 'send_email_check_in_out_customer_location');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['send_email_check_in_out_customer_location'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'send_email_check_in_out_customer_location');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if (isset($data['allow_employees_to_create_work_points'])) {
			$this->db->where('option_name', 'allow_employees_to_create_work_points');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['allow_employees_to_create_work_points'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		} else {
			$this->db->where('option_name', 'allow_employees_to_create_work_points');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedrows++;
			}
		}
		if ($affectedrows > 0) {
			return true;
		}
		return false;
	}
	/**
	 * notifications
	 * @param  integer id staff
	 * @param  string $link
	 * @param  string $description
	 */
	public function notifications($id_staff, $link, $description)
	{
		$notifiedusers = [];
		$id_userlogin = get_staff_user_id();

		$notified = add_notification([
			'fromuserid' => $id_userlogin,
			'description' => $description,
			'link' => $link,
			'touserid' => $id_staff,
			'additional_data' => serialize([
				$description,
			]),
		]);
		if ($notified) {
			array_push($notifiedusers, $id_staff);
		}
		pusher_trigger_notification($notifiedusers);
	}
	/**
	 * add check in out value to timesheet
	 * @param integer $staff_id 
	 */
	public function add_check_in_out_value_to_timesheet($staff_id, $date)
	{
		$data_check_in_out = $this->get_list_check_in_out($date, $staff_id);
		$check_in_date = '';
		$check_out_date = '';
		$total_work_hours = 0;
		$next_key = '';
		$sr = 1;
		// Get the first check-in (always array[0])
		if (!empty($data_check_in_out) && $data_check_in_out[0]['type_check'] == 1) {
			$check_in_date = $data_check_in_out[0]['date'];
		}

		// Find the last check-out in the array
		foreach (array_reverse($data_check_in_out) as $value) {
			if ($value['type_check'] == 2) {
				$check_out_date = $value['date'];
				break; // Stop after finding the last check-out
			}
		}

		// Calculate working hours if we have both check-in and check-out
		if ($check_in_date && $check_out_date) {
			$data_hour = $this->calculate_attendance_timesheets($staff_id, $check_in_date, $check_out_date);
			$total_work_hours = $data_hour->working_hour;
		}


		$data_ts = $this->get_ts_staff($staff_id, $date, 'W');
		if ($total_work_hours > 0) {
			if ($data_ts) {
				$this->db->where('id', $data_ts->id);
				$this->db->update(db_prefix() . 'timesheets_timesheet', [
					'value' => $total_work_hours,
					'type' => 'W',
				]);
				if ($this->db->affected_rows() > 0) {
					return true;
				}
			} else {
				$data_insert['staff_id'] = $staff_id;
				$data_insert['date_work'] = $date;
				$data_insert['type'] = 'W';
				$data_insert['add_from'] = ((get_staff_user_id() && get_staff_user_id() != 0 && get_staff_user_id() != '') ? get_staff_user_id() : $staff_id);
				$data_insert['value'] = $total_work_hours;
				$this->db->insert(db_prefix() . 'timesheets_timesheet', $data_insert);
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					return true;
				}
			}
		} else {
			if ($data_ts) {
				$this->db->where('id', $data_ts->id);
				$this->db->delete(db_prefix() . 'timesheets_timesheet');
				if ($this->db->affected_rows() > 0) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * get timesheet staff
	 * @param  $date
	 * @param  $staff
	 * @param  $type
	 * @return
	 */
	public function get_ts_staff($staff, $date, $type = '')
	{
		if ($type != '') {
			$this->db->where('type', $type);
		}
		$this->db->where('date_work', $date);
		$this->db->where('staff_id', $staff);
		return $this->db->get(db_prefix() . 'timesheets_timesheet')->row();
	}
	/**
	 * get_client_ip
	 * @return string $ipaddress
	 */
	public function get_client_ip()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}
	/**
	 * get location
	 * @param  string $ip
	 * @return json
	 */
	public function get_location($ip = '')
	{
		if ($ip == '') {
			$ip = $this->get_client_ip();
		}
		return json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
	}

	/**
	 * get workplace
	 * @param  integer $id
	 * @return object or object array
	 */
	public function get_workplace($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_workplace')->row();
		}
		if ($id == false) {
			return $this->db->get(db_prefix() . 'timesheets_workplace')->result_array();
		}
	}
	/**
	 * add workplace
	 * @param array $data
	 */
	public function add_workplace($data)
	{
		if (!isset($data['default'])) {
			$data['default'] = 0;
		}
		if ($data['default'] == 1) {
			$this->db->where('id != ' . $data['id']);
			$this->db->update(db_prefix() . 'timesheets_workplace', ['default' => 0]);
		} else {
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_workplace')->row();
			if (!$data_saved) {
				$data['default'] = 1;
			} else {
				if ($data_saved->id == $data['id']) {
					$data['default'] = 1;
				}
			}
		}
		$this->db->insert(db_prefix() . 'timesheets_workplace', $data);
		return $this->db->insert_id();
	}
	/**
	 * update workplace
	 * @param  array $data
	 * @return boolean
	 */
	public function update_workplace($data)
	{
		if (!isset($data['default'])) {
			$data['default'] = 0;
		}
		if ($data['default'] == 1) {
			$this->db->where('id != ' . $data['id']);
			$this->db->update(db_prefix() . 'timesheets_workplace', ['default' => 0]);
		} else {
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_workplace')->row();
			if (!$data_saved) {
				$data['default'] = 1;
			} else {
				if ($data_saved->id == $data['id']) {
					$data['default'] = 1;
				}
			}
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'timesheets_workplace', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete workplace
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_workplace($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_workplace');
		if ($this->db->affected_rows() > 0) {
			$data_assign = $this->get_workplace_assign_by_workplace_id($id);
			foreach ($data_assign as $key => $value) {
				$this->delete_workplace_assign($value['id']);
			}
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_workplace')->row();
			if (!$data_saved) {
				$first_row = $this->db->get(db_prefix() . 'timesheets_workplace')->row();
				$this->db->where('id', $first_row->id);
				$this->db->update(db_prefix() . 'timesheets_workplace', ['default' => 1]);
			}
			return true;
		}
		return false;
	}
	/**
	 * get workplace assign
	 * @param  integer $id
	 * @return object or object array
	 */
	public function get_workplace_assign($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_workplace_assign')->row();
		}
		if ($id == false) {
			return $this->db->get(db_prefix() . 'timesheets_workplace_assign')->result_array();
		}
	}
	/**
	 * add workplace assign
	 * @param array $data
	 */
	public function add_workplace_assign($data)
	{
		$workplace_id = '';
		$affectedrows = 0;
		if (isset($data['workplace_id'])) {
			if ($data['workplace_id'] == '') {
				$this->db->where('default', 1);
				$this->db->select('id');
				$data_wp = $this->db->get(db_prefix() . 'timesheets_workplace')->row();
				$workplace_id = $data_wp->id;
			} else {
				$workplace_id = $data['workplace_id'];
			}
		}
		if ($workplace_id != '') {
			foreach ($data['staffid'] as $key => $staffid) {
				$this->db->where('staffid', $staffid);
				$data_saved = $this->db->get(db_prefix() . 'timesheets_workplace_assign')->row();
				if ($data_saved) {
					$this->db->where('id', $data_saved->id);
					$this->db->update(db_prefix() . 'timesheets_workplace_assign', ['workplace_id' => $workplace_id]);
					if ($this->db->affected_rows() > 0) {
						$affectedrows++;
					}
				} else {

					$this->db->insert(db_prefix() . 'timesheets_workplace_assign', ['staffid' => $staffid, 'workplace_id' => $workplace_id]);
					if (is_numeric($this->db->insert_id())) {
						$affectedrows++;
					}
				}
			}
		}
		if ($affectedrows != 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete workplace assign
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_workplace_assign($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_workplace_assign');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * get workplace assign by workplace id
	 * @param  integer $id
	 * @return object or object array
	 */
	public function get_workplace_assign_by_workplace_id($workplace_id)
	{
		$this->db->where('workplace_id', $workplace_id);
		return $this->db->get(db_prefix() . 'timesheets_workplace_assign')->result_array();
	}
	/**
	 * compute distance
	 * @param  string  $lat1
	 * @param  string  $lng1
	 * @param  string  $lat2
	 * @param  string  $lng2
	 * @param  integer $radius
	 * @return float (m)
	 */
	public function compute_distance($lat1, $lng1, $lat2, $lng2, $radius = 6378137)
	{
		static $x = M_PI / 180;
		$lat1 *= $x;
		$lng1 *= $x;
		$lat2 *= $x;
		$lng2 *= $x;
		$distance = 2 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2), 2) + cos($lat1) * cos($lat2) * pow(sin(($lng1 - $lng2) / 2), 2)));
		return $distance * $radius;
	}
	/**
	 * get current location
	 * @return object
	 */
	public function get_current_location()
	{
		$data = $this->get_location();
		$obj = new stdclass();
		$obj->latitude = '';
		$obj->longitude = '';
		if (isset($data->loc)) {
			$data_latitude_longitude = explode(',', $data->loc);
			if (isset($data_latitude_longitude[0]) && isset($data_latitude_longitude[1])) {
				$obj->latitude = $data_latitude_longitude[0];
				$obj->longitude = $data_latitude_longitude[1];
			}
		}
		return $obj;
	}
	/**
	 * get location staff
	 * @param  integer $staffid
	 * @return integer
	 */
	public function get_location_staff($staffid)
	{
		$latitude = '';
		$longitude = '';
		$distance = '';
		$workplace_id = '';
		$this->db->where('staffid', $staffid);
		$data = $this->db->get(db_prefix() . 'timesheets_workplace_assign')->row();
		if ($data) {
			$data_workplace = $this->get_workplace($data->workplace_id);
			if ($data_workplace) {
				$latitude = $data_workplace->latitude;
				$longitude = $data_workplace->longitude;
				$distance = $data_workplace->distance;
				$workplace_id = $data->workplace_id;
			}
		}
		$obj = new stdClass();
		$obj->latitude = $latitude;
		$obj->longitude = $longitude;
		$obj->distance = $distance;
		$obj->workplace_id = $workplace_id;
		return $obj;
	}
	/**
	 * check attendance by coordinates
	 * @param  integer $staffid
	 * @param  string $cur_latitude
	 * @param  string $cur_longitude
	 * @return integer
	 */
	public function check_attendance_by_coordinates($staffid, $cur_latitude, $cur_longitude)
	{
		$latitude = '';
		$longitude = '';
		$max_distance = '';
		$workplace_id = '';
		$obj = new stdClass();
		$obj->error_code = 0;
		$obj->workplace_id = '';
		$data_location = $this->get_location_staff($staffid);
		if ($data_location) {
			$latitude = $data_location->latitude;
			$longitude = $data_location->longitude;
			$max_distance = $data_location->distance;
			$workplace_id = $data_location->workplace_id;
		}
		if ($latitude != '' && $longitude != '' && $cur_latitude != '' && $cur_longitude != '' && $max_distance != '') {
			$cal_distance = $this->compute_distance($latitude, $longitude, $cur_latitude, $cur_longitude);
			if ($cal_distance <= $max_distance) {
				// Valid distance
				$obj->error_code = 1;
				$obj->workplace_id = $workplace_id;
			} else {
				// Invalid distance
				$obj->error_code = 2;
				$obj->workplace_id = $workplace_id;
			}
		} else {
			$obj->error_code = 3;
			$obj->workplace_id = $workplace_id;
		}
		// No time attendance according to location
		return $obj;
	}

	/**
	 * get ts by date and staff leave
	 * @param  $date
	 * @param  $staff
	 * @return array object
	 */
	public function get_ts_by_date_and_staff_leave($date, $staff)
	{
		$this->db->where('(relate_type = "leave")');
		$this->db->where('date_work', $date);
		$this->db->where('staff_id', $staff);
		return $this->db->get(db_prefix() . 'timesheets_timesheet')->result_array();
	}
	/**
	 * delete mass workplace assign
	 * @param  array $data
	 * @return boolean
	 */
	public function delete_mass_workplace_assign($data)
	{
		$affectedrows = 0;
		if (isset($data['mass_delete'])) {
			if ($data['mass_delete'] == 'on') {
				if ($data['check_id'] != '') {
					$list_id = explode(',', $data['check_id']);
					foreach ($list_id as $key => $id) {
						$res = $this->delete_workplace_assign($id);
						if ($res == true) {
							$affectedrows++;
						}
					}
				}
			}
		}
		if ($affectedrows != 0) {
			return true;
		}
		return false;
	}
	/**
	 * get route point
	 * @param  integer $id
	 * @return object or array object
	 */
	public function get_route_point($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_route_point')->row();
		}
		if ($id == false) {
			$new_array = [];
			$data = $this->db->get(db_prefix() . 'timesheets_route_point')->result_array();
			foreach ($data as $key => $value) {
				$value['name'] = trim($value['name']);
				$new_array[] = $value;
			}
			return $new_array;
		}
	}

	/**
	 * add route_point
	 * @param array $data
	 */
	public function add_route_point($data)
	{
		if (isset($data['related_to'])) {
			// If related is workplace
			if ($data['related_to'] == 2) {
				$data['related_id'] = $data['related_id2'];
			}
		}
		unset($data['related_id2']);

		if (!isset($data['default'])) {
			$data['default'] = 0;
		}
		if ($data['default'] == 1) {
			$this->db->where('id != ' . $data['id']);
			$this->db->update(db_prefix() . 'timesheets_route_point', ['default' => 0]);
		} else {
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_route_point')->row();
			if (!$data_saved) {
				$data['default'] = 1;
			} else {
				if ($data_saved->id == $data['id']) {
					$data['default'] = 1;
				}
			}
		}
		$this->db->insert(db_prefix() . 'timesheets_route_point', $data);
		return $this->db->insert_id();
	}
	/**
	 * update route_point
	 * @param  array $data
	 * @return boolean
	 */
	public function update_route_point($data)
	{
		if (isset($data['related_to'])) {
			// If related is workplace
			if ($data['related_to'] == 2) {
				$data['related_id'] = $data['related_id2'];
			}
		}
		unset($data['related_id2']);
		if (!isset($data['default'])) {
			$data['default'] = 0;
		}
		if ($data['default'] == 1) {
			$this->db->where('id != ' . $data['id']);
			$this->db->update(db_prefix() . 'timesheets_route_point', ['default' => 0]);
		} else {
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_route_point')->row();
			if (!$data_saved) {
				$data['default'] = 1;
			} else {
				if ($data_saved->id == $data['id']) {
					$data['default'] = 1;
				}
			}
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'timesheets_route_point', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete route_point
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_route_point($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_route_point');
		if ($this->db->affected_rows() > 0) {
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_route_point')->row();
			if (!$data_saved) {
				$first_row = $this->db->get(db_prefix() . 'timesheets_route_point')->row();
				$this->db->where('id', $first_row->id);
				$this->db->update(db_prefix() . 'timesheets_route_point', ['default' => 1]);
			}
			return true;
		}
		return false;
	}
	/**
	 * get route
	 * @param  integer $id
	 * @return object or array object
	 */
	public function get_route($id = false)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_route')->row();
		}
		if ($id == false) {
			return $this->db->get(db_prefix() . 'timesheets_route')->result_array();
		}
	}

	/**
	 * add route
	 * @param array $data
	 */
	public function add_route($data)
	{
		$data_detail = json_decode($data['data_hanson']);
		$new_rowdb = [];
		foreach ($data_detail as $key => $row) {
			$staffid = $row[0];
			for ($i = 1; $i < count($row) - 2; $i++) {
				$cell_data = $row[$i + 1];
				if ($cell_data != '') {
					$date_col = $data['month'] . '-' . $i;

					// explode route point string to find id by name
					// and create into a row to save database

					$ex_route_point = explode(', ', $cell_data);
					$not_to_be_in_order = true;
					foreach ($ex_route_point as $r_key => $route_point) {
						$name = '';
						$obj = $this->get_content_between_character($route_point, '(', ')');
						if ($obj != '') {
							$name = $obj->remain;
							if ($r_key == 0) {
								$not_to_be_in_order = false;
							}
						} else {
							$name = $route_point;
						}
						$order = 0;
						if ($not_to_be_in_order == false) {
							$order = ($r_key + 1);
						}
						$route_point_id = $this->get_route_point_id_by_name($name);
						if ($route_point_id != '') {
							array_push($new_rowdb, array(
								'staffid' => $staffid,
								'route_point_id' => $route_point_id,
								'date_work' => $date_col,
								'order' => $order,
							));
						}
					}
					// end row
				}
			}
			$this->db->where('staffid', $staffid);
			$this->db->where('date_format(date_work, "%Y-%m") = "' . $data['month'] . '"');
			$this->db->delete(db_prefix() . 'timesheets_route');
		}
		$this->db->insert_batch(db_prefix() . 'timesheets_route', $new_rowdb);
		if (is_numeric($this->db->insert_id())) {
			return true;
		}
		return false;
	}
	/**
	 * get route point id by name
	 * @param  string $name
	 * @return integer
	 */
	public function get_route_point_id_by_name($name)
	{
		$id = '';
		$data = $this->db->query('select id from ' . db_prefix() . 'timesheets_route_point where name like \'%' . $name . '%\'')->row();
		if ($data) {
			$id = $data->id;
		}
		return $id;
	}

	/**
	 * update route
	 * @param  array $data
	 * @return boolean
	 */
	public function update_route($data)
	{
		if (isset($data['related_to'])) {
			// If related is workplace
			if ($data['related_to'] == 2) {
				$data['related_id'] = $data['related_id2'];
			}
		}
		unset($data['related_id2']);
		if (!isset($data['default'])) {
			$data['default'] = 0;
		}
		if ($data['default'] == 1) {
			$this->db->where('id != ' . $data['id']);
			$this->db->update(db_prefix() . 'timesheets_route', ['default' => 0]);
		} else {
			$this->db->where('default', 1);
			$data_saved = $this->db->get(db_prefix() . 'timesheets_route')->row();
			if (!$data_saved) {
				$data['default'] = 1;
			} else {
				if ($data_saved->id == $data['id']) {
					$data['default'] = 1;
				}
			}
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'timesheets_route', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	/**
	 * delete route
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_route($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_route');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get content between character
	 * @param  $str
	 * @param  $start_char
	 * @param  $end_char
	 * @return string
	 */
	public function get_content_between_character($str, $start_char, $end_char)
	{
		$start_index = '';
		$end_index = '';
		for ($i = (strlen($str) - 1); $i >= 0; $i--) {
			if ($start_index == '') {
				if ($str[$i] == $end_char) {
					$start_index = $i;
				}
			} else {
				if ($str[$i] == $start_char) {
					$end_index = $i;
				}
			}
		}

		if ($end_index != '' && $start_index != '') {
			$obj = new stdClass();
			$obj->content = substr($str, ($end_index + 1), (($start_index - 1) - $end_index));
			$obj->remain = trim(substr($str, 0, $end_index));
			return $obj;
		}
		return '';
	}

	public function get_route_text($staffid, $date_work, $where = '')
	{
		$this->db->where('staffid', $staffid);
		$this->db->where('date_work', $date_work);
		if ($where != '') {
			$this->db->where($where);
		}
		$this->db->order_by('order', 'ASC');
		$data = $this->db->get(db_prefix() . 'timesheets_route')->result_array();

		$result = '';
		$list_route_id = [];
		$has_order = true;
		foreach ($data as $key => $value) {
			if ($value['route_point_id'] && $value['route_point_id'] != '') {
				$route = $this->get_route_point($value['route_point_id']);
				if ($route) {
					if ($key == 0 && $value['order'] == 0) {
						$has_order = false;
					}
					$result .= $route->name . '' . (($has_order == true) ? ' (' . $value['order'] . ')' : '') . ', ';
					$list_route_id[] = $value['route_point_id'];
				}
			}
		}
		if ($result != '') {
			$result = rtrim($result, ', ');
		}
		$obj = new stdClass();
		$obj->result = $result;
		$obj->list_route_id = $list_route_id;
		return $obj;
	}
	public function get_route_by_fillter($staffid = '', $date_work = '')
	{
		if ($staffid != '') {
			$this->db->where('staffid', $staffid);
		} else {
			$this->db->where('staffid', get_staff_user_id());
		}
		if ($date_work != '') {
			$this->db->where('date_work', $date_work);
		} else {
			$this->db->where('date_work', date('Y-m-d'));
		}
		$this->db->order_by('order', 'ASC');
		return $this->db->get(db_prefix() . 'timesheets_route')->result_array();
	}
	public function get_next_point($staff_id, $date, $user_latitude, $user_longitude)
	{
		$return_id = '';
		$type = 'order';
		$data_route = $this->get_route_by_fillter($staff_id, $date);
		if ($data_route) {
			$unorrdered = false;
			if (($unorrdered == false) && ($data_route[0]['order'] == 0)) {
				$unorrdered = true;
			}
			if ($unorrdered == false) {
				// Route in order

				foreach ($data_route as $key => $rowitem) {
					// Check if current date of this route point not yet check in and check out
					// Return this route point id
					$valid_check = $this->check_full_check_in_out_route_point($date, $staff_id, $rowitem['route_point_id']);
					if ($valid_check == false) {
						$return_id = $rowitem['route_point_id'];
						break;
					}
					if ($valid_check == true) {
						continue;
					}
				}
			} else {
				// Route not in order
				$type = 'unorrdered';
				$list_distance = [];
				$min_distance = 0;
				foreach ($data_route as $key => $rowitem) {
					$data_route_point = $this->get_route_point($rowitem['route_point_id']);
					if ($data_route_point) {
						$latitude = $data_route_point->latitude;
						$longitude = $data_route_point->longitude;
						// Caculate distance
						if ($user_latitude != '' && $user_longitude != '' && $latitude != '' && $longitude != '') {
							$cal_distance = $this->compute_distance($user_latitude, $user_longitude, $latitude, $longitude);
							if ($cal_distance <= $data_route_point->distance) {
								if ($min_distance == 0 && $cal_distance > 0) {
									$min_distance = $cal_distance;
									$return_id = $rowitem['route_point_id'];
								} else {
									if ($cal_distance < $min_distance) {
										$min_distance = $cal_distance;
										$return_id = $rowitem['route_point_id'];
									}
								}
							}
						}
					}
				}
			}
		}
		$obj = new stdClass();
		$obj->type = $type;
		$obj->id = $return_id;
		return $obj;
	}
	/**
	 * check full check in out route point
	 * @param  date $date
	 * @param  integer $staff_id
	 * @param  integer $route_point_id
	 * @return boolean
	 */
	public function check_full_check_in_out_route_point($date, $staff_id, $route_point_id)
	{
		$valid_check = 0;
		$return_id = '';
		$check = $this->get_list_check_in_out($date, $staff_id, $route_point_id);
		$next_key = 0;
		foreach ($check as $key_val => $val) {
			if (($valid_check == 0) && ($val['type_check'] == 1)) {
				$valid_check = 1;
				$next_key = ($key_val + 1);
			}
			if (($next_key == $key_val) && ($valid_check == 1) && ($val['type_check'] == 2)) {
				$valid_check++;
				break;
			}
		}
		if ($valid_check != 2) {
			return false;
		}
		if ($valid_check == 2) {
			// Is full check
			return true;
		}
	}
	/**
	 * check exist route point name
	 * @param  string $name
	 * @return boolean
	 */
	public function check_exist_route_point_name($name, $id = '')
	{
		if ($id != '') {
			$this->db->where('id !=' . $id);
		}
		$this->db->where('name', $name);
		$res = $this->db->get(db_prefix() . 'timesheets_route_point')->row();
		if ($res) {
			return true;
		}
		return false;
	}
	/**
	 * staff at same route
	 * @param  integer $route
	 * @param  integer $staffid
	 * @param  date $date
	 * @return array
	 */
	public function staff_at_same_route($route, $staffid, $date)
	{
		$list_staff = $this->db->query('select distinct(staffid) as staffid from ' . db_prefix() . 'timesheets_route where staffid != ' . $staffid . ' and date_work=\'' . $date . '\'')->result_array();
		$list = [];
		foreach ($list_staff as $key => $staff) {
			$this->db->where('staffid', $staff['staffid']);
			$this->db->where('date_work', $date);
			$this->db->order_by('order', 'ASC');
			$list_date = $this->db->get(db_prefix() . 'timesheets_route')->result_array();
			if ($list_date) {
				$list[] = $list_date;
			}
		}
		$staffid = [];
		$count = count($route);

		$list1 = [];
		$length_test = 0;
		foreach ($list as $key => $value) {
			if (count($value) == $count) {
				$list1[] = $value;
			}
		}

		$list_result = [];
		foreach ($list1 as $lkey => $item) {
			$count_valid = 0;
			$staffid = '';
			foreach ($route as $rkey => $value) {
				if (($value['order'] == $item[$rkey]['order']) && ($value['route_point_id'] == $item[$rkey]['route_point_id'])) {
					$staffid = $item[$rkey]['staffid'];
					$count_valid++;
				}
			}
			if ($count_valid) {
				$list_result[] = $staffid;
			}
		}
		return $list_result;
	}
	/**
	 * get check in out by route point
	 * @param  integer $staff_id
	 * @param  date $date
	 * @param  integer $route_point_id
	 * @return array
	 */
	public function get_check_in_out_by_route_point($staff_id, $date, $route_point_id)
	{
		return $this->db->query('SELECT * FROM ' . db_prefix() . 'check_in_out where date(' . db_prefix() . 'check_in_out.date) = \'' . $date . '\' and staff_id = ' . $staff_id . ' and route_point_id = ' . $route_point_id)->result_array();
	}
	/**
	 * choose approver
	 * @param  array $data
	 * @return boolean
	 */
	public function choose_approver($data)
	{
		$date_send = date('Y-m-d H:i:s');
		$this->delete_approval_details($data['rel_id'], $data['rel_type']);
		$list_staff = $this->staff_model->get();
		$list = [];
		$staff_addedfrom = $data['addedfrom'];
		$sender = get_staff_user_id();
		$data_setting = $this->get_approve_setting($data['rel_type'], false, $staff_addedfrom);
		$row = [];

		$row['notification_recipient'] = isset($data_setting->notification_recipient) ? $data_setting->notification_recipient : null;
		$row['approval_deadline'] = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $data_setting->number_day_approval . ' day'));
		$row['staffid'] = $data['staffid'];
		$row['date_send'] = $date_send;
		$row['rel_id'] = $data['rel_id'];
		$row['rel_type'] = $data['rel_type'];
		$row['sender'] = $sender;

		$this->db->insert(db_prefix() . 'timesheets_approval_details', $row);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * get attendance task
	 * @param  array $staffs_list
	 * @param  string $month
	 * @param  string $year
	 * @param  string $from_date
	 * @param  string $to_date
	 * @return array
	 */
	public function get_attendance_task($staffs_list, $month = '', $year = '', $from_date = '', $to_date = '')
	{
		$data['staff_row_tk'] = [];
		$data['staff_row_tk_detailt'] = [];

		if ($month != '' && $year != '') {
			$from_date = $year . '-' . $month . '-01';
			$to_date = $year . '-' . $month . '-' . date('t', strtotime($from_date));
		}

		$list_date = $this->get_list_date($from_date, $to_date);
		foreach ($staffs_list as $s) {
			$ts_date = '';
			$ts_ts = '';
			$result_tb = [];

			$taskstimesWhere = '';
			if ($from_date != '' && $to_date != '') {
				$taskstimesWhere = 'IF(end_time IS NOT NULL,(((from_unixtime(start_time, \'%Y-%m-%d\') <= "' . $from_date . '") and FROM_UNIXTIME(end_time, \'%Y-%m-%d\') >= "' . $from_date . '") or (from_unixtime(start_time, \'%Y-%m-%d\') <= "' . $to_date . '" and FROM_UNIXTIME(end_time, \'%Y-%m-%d\') >= "' . $to_date . '") or (from_unixtime(start_time, \'%Y-%m-%d\') > "' . $from_date . '" and FROM_UNIXTIME(end_time, \'%Y-%m-%d\') < "' . $to_date . '")), (from_unixtime(start_time, \'%Y-%m-%d\') <= "' . $from_date . '" or (from_unixtime(start_time, \'%Y-%m-%d\') > "' . $from_date . '" and from_unixtime(start_time, \'%Y-%m-%d\') <= "' . $to_date . '")))';
			} elseif ($from_date != '') {
				$taskstimesWhere = '(from_unixtime(start_time, \'%Y-%m-%d\') >= "' . $from_date . '" or IF(FROM_UNIXTIME(end_time, \'%Y-%m-%d\') IS NOT NULL, FROM_UNIXTIME(end_time, \'%Y-%m-%d\') >= "' . $from_date . '",  1=1))';
			} elseif ($to_date != '') {
				$taskstimesWhere = '(from_unixtime(start_time, \'%Y-%m-%d\') <= "' . $to_date . '" or IF(FROM_UNIXTIME(end_time, \'%Y-%m-%d\') IS NOT NULL,FROM_UNIXTIME(end_time, \'%Y-%m-%d\') <= "' . $to_date . '", 1=1))';
			}
			$list_taskstimers = $this->get_taskstimers($s['staffid'], $taskstimesWhere);
			$dt_ts = [];
			$dt_ts_detail = [];
			$dt_cell_bg = [];
			$dt_ts = [_l('staff_id') => $s['staffid'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
			foreach ($list_date as $key => $value) {
				$maints = strtotime($value);
				$date_s = date('D d', $maints);
				$working_hour = 0;
				foreach ($list_taskstimers as $tk => $task) {
					$startts = strtotime(date('Y-m-d', strtotime($task['start_time'])));
					$endts = strtotime(date('Y-m-d', strtotime($task['end_time'] ?? date('Y-m-d H-i-s'))));
					if (($maints >= $startts) && ($maints <= $endts)) {
						if ($startts == $endts) {
							$working_hour += $task['total_logged_time'];
						} else {
							$hour_in_date = $this->get_hour_range_between_date($task['start_time'], $task['end_time'] ?? date('Y-m-d H-i-s'), $value);
							if (isset($hour_in_date[0]) && isset($hour_in_date[1])) {
								$working_hour += $this->get_hour($value . ' ' . $hour_in_date[0], $value . ' ' . $hour_in_date[1]);
							}
						}
					}
				}

				$check_holiday = $this->check_holiday($s['staffid'], $value);
				$result_lack = '';
				if (!$check_holiday) {
					$has_business_trip = false;
					$ts_lack = '';
					if ($working_hour > 0) {
						$ts_lack .= 'W:' . round($working_hour, 1) . '; ';
					}
					$ts_type = $this->get_ts_by_date_and_staff_leave($value, $s['staffid']);
					if ($ts_type) {
						foreach ($ts_type as $kts => $ts_row) {
							if ($ts_row['type'] == 'B') {
								$has_business_trip = true;
								break;
							}
							$ts_lack .= $ts_row['type'] . ':' . round($ts_row['value'], 1) . '; ';
						}
					}

					$total_lack = $ts_lack;
					if ($total_lack != '') {
						$total_lack = rtrim($total_lack, '; ');
					}
					if ($has_business_trip == true) {
						$result_lack = 'B';
					} else {
						$result_lack = $total_lack;
					}
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
				$dt_ts[$date_s] = $result_lack;
				$dt_ts_detail[$value] = $result_lack;
				$dt_cell_bg[$date_s] = '#fff';
			}
			$data['staff_row_tk'][] = $dt_ts;
			$data['staff_row_tk_detailt'][] = $dt_ts_detail;
			$data['cell_background'][] = $dt_cell_bg;
		}
		return $data;
	}

	public function get_hour_range_between_date($date1, $date2, $curr_date)
	{
		$list_date = $this->get_list_date(date('Y-m-d', strtotime($date1)), date('Y-m-d', strtotime($date2)));
		$start_hour = date('H:i:s', strtotime($date1));
		$end_hour = date('H:i:s', strtotime($date2));

		$list_hour = [];
		$count = count($list_date);
		foreach ($list_date as $key => $date) {
			if ($date == $curr_date) {
				$list_hour = '';
				if ($key == 0) {
					$list_hour = [$start_hour, '23:59:59'];
				} elseif (($key + 1) == $count) {
					$list_hour = ['00:00:00', $end_hour];
				} else {
					$list_hour = ['00:00:00', '23:59:59'];
				}
				return $list_hour;
			}
		}
	}

	/**
	 * get attendance manual
	 * @param  [type] $staffs_list
	 * @param  string $month
	 * @param  string $year
	 * @param  string $from_date
	 * @param  string $to_date
	 * @return [type]
	 */
	public function get_attendance_manual($staffs_list, $month = '', $year = '', $from_date = '', $to_date = '')
	{
		$type_valid = ['AL', 'W', 'U', 'HO', 'E', 'L', 'B', 'SI', 'M', 'ME', 'NS', 'P', 'OFF', 'H/F', 'W/H', 'N/A'];;
		$data_type_of_leave = $this->get_type_of_leave();
		foreach ($data_type_of_leave as $key => $value) {
			$type_valid[] = $value['symbol'];
		}
		$data['cell_background'] = [];
		$data['staff_row_tk'] = [];
		$data['staff_row_tk_detailt'] = [];
		if ($month != '' && $year != '') {
			$from_date = $year . '-' . $month . '-01';
			$to_date = $year . '-' . $month . '-' . date('t', strtotime($from_date));
			$data_ts = $this->get_timesheets_ts_by_month($month, $year);
		} elseif ($from_date != '' && $to_date != '') {
			$data_ts = $this->get_timesheets_between_date($from_date, $to_date);
		}
		$list_date = $this->get_list_date($from_date, $to_date);
		foreach ($data_ts as $ts) {
			$staff_info = array();
			$staff_info['date'] = date('D d', strtotime($ts['date_work']));
			$ts_type = $this->get_ts_by_date_and_staff($ts['date_work'], $ts['staff_id']);
			if (count($ts_type) <= 1) {
				$staff_info['ts'] = $ts['type'] . (($ts['value'] != '' && $ts['value'] > 0) ? ':' . round($ts['value'], 2) : '');
			} else {
				$str = '';
				foreach ($ts_type as $tp) {
					if ($tp['type'] == 'HO' || $tp['type'] == 'M') {
						if ($str == '') {
							$str .= $tp['type'];
						} else {
							$str .= "; " . $tp['type'];
						}
					} else {
						if ($str == '') {
							$str .= $tp['type'] . (($tp['value'] != '' && $tp['value'] > 0) ? ':' . round($tp['value'], 2) : '');
						} else {
							$str .= "; " . $tp['type'] . (($tp['value'] != '' && $tp['value'] > 0) ? ':' . round($tp['value'], 2) : '');
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
		foreach ($staffs_list as $s) {
			$list_hour_shift = $this->get_list_hour_shift_staff($s['staffid'], $list_date);
			$list_holiday = $this->get_list_holiday($s['staffid'], $from_date, $to_date);
			$list_color = $this->list_cell_color_checkin_out($s['staffid'], $list_date, $from_date, $to_date);
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
			$dt_ts_detail = [];
			$dt_cell_bg = [];
			$dt_ts = [_l('staff_id') => $s['staffid'], _l('staff') => $s['firstname'] . ' ' . $s['lastname']];
			$list_dtts = [];

			foreach ($result_tb as $key => $rs) {
				foreach ($rs as $day => $val) {
					if ($val == "NS" || $val == "HO") {
						continue;
					}
					$list_dtts[$day] = $val;
				}
			}

			foreach ($list_date as $key => $value) {
				$date_s = date('D d', strtotime($value));
				$date_md = date('m-d', strtotime($value));
				$max_hour = $list_hour_shift[$value] ?? 0;

				// Check if current date is a holiday
				$holiday_type = $list_holiday[$date_md] ?? false;

				$ts_lack = $list_dtts[$date_s] ?? '';
				if ($ts_lack) {
					$ts_lack .= '; ';
				}
				$total_lack = rtrim($ts_lack, '; ');

				if ($holiday_type) {
					// Handle holiday cases
					switch ($holiday_type) {
						case 'holiday':
							$result_lack = "HO";
							break;
						case 'event_break':
							$result_lack = "EB";
							break;
						case 'unexpected_break':
							$result_lack = "UB";
							break;
						default:
							$result_lack = $this->merge_ts($total_lack, $max_hour, $type_valid);
					}
				} else {
					// Normal day processing
					if ($max_hour > 0) {
						$result_lack = $this->merge_ts($total_lack, $max_hour, $type_valid);
					} else {
						$result_lack = $this->merge_ts($total_lack, $max_hour, $type_valid);
						if (empty($result_lack)) {
							$result_lack = 'OFF';
						}
					}
				}

				$dt_ts[$date_s] = $result_lack;
				$dt_ts_detail[$value] = $result_lack;
				$dt_cell_bg[$date_s] = $list_color[$value];
			}

			$data['staff_row_tk'][] = $dt_ts;
			$data['staff_row_tk_detailt'][] = $dt_ts_detail;
			$data['cell_background'][] = $dt_cell_bg;
		}

		return $data;
	}
	/**
	 * get list month
	 * @param   $from_date
	 * @param   $to_date
	 */
	public function get_list_month($from_date, $to_date)
	{
		$start = new DateTime($from_date);
		$start->modify('first day of this month');
		$end = new DateTime($to_date);
		$end->modify('first day of next month');
		$interval = DateInterval::createFromDateString('1 month');
		$period = new DatePeriod($start, $interval, $end);
		$result = [];
		foreach ($period as $dt) {
			$result[] = $dt->format("Y-m-01");
		}
		return $result;
	}

	/**
	 * get timesheets ts by from date and to date
	 * @param integer $from_date
	 * @param integer $to_date
	 * @return array
	 */
	public function get_timesheets_between_date($from_date, $to_date)
	{
		$query = 'select * from ' . db_prefix() . 'timesheets_timesheet where date(date_work) between "' . $from_date . '" and "' . $to_date . '"';
		return $this->db->query($query)->result_array();
	}

	/**
	 * round to next hour
	 * @param  string $datestring 
	 * @param  integer $max_hour   
	 * @return date             
	 */
	function round_to_next_hour($datestring, $max_hour)
	{
		$nextHour = strtotime($datestring . ' +' . $max_hour . ' hours');
		return date('Y-m-d H:i:s', $nextHour);
	}

	/**
	 * get hour auto checkout type
	 * @param  string $type 
	 * @return array       
	 */
	public function get_hour_auto_checkout_type($type)
	{
		$staffs = $this->get_staff_timekeeping_applicable_object(true);
		$result_list = [];
		$auto_checkout_value = 1;
		$data_auto_checkout_value = get_timesheets_option('auto_checkout_value');
		if ($data_auto_checkout_value) {
			$auto_checkout_value = $data_auto_checkout_value;
		}
		$current_date = date('Y-m-d');
		foreach ($staffs as $skey => $staff) {
			$hour_info = $this->get_info_hour_shift_staff($staff['staffid'], $current_date);
			if ($hour_info && $hour_info->woking_hour > 0) {
				$check_result = $this->check_check_out($staff['staffid'], $current_date);
				if ($hour_info->end_working != '' && $check_result->result) {
					$end_work_hour = $hour_info->end_working;
					$time_checkout = '';
					$start_time = '';
					$time_checkout = $current_date . ' ' . $end_work_hour;
					if ($type == 1) {
						//after x hours of end shift
						$start_time = $time_checkout;
					} elseif ($type == 2) {
						//after checkin +x hour
						$start_time = $check_result->date;
					} else {
						//login time +x hour
						$start_time = $staff['last_login'];
					}
					if ($time_checkout != '' && $start_time != '') {
						$effective_time = $this->round_to_next_hour($start_time, $auto_checkout_value);
						if (strtotime($effective_time) > strtotime($current_date . ' 23:59:59')) {
							$effective_time = $current_date . ' 23:59:59';
						}
						$result_list[] = array(
							'staffid' => $staff['staffid'],
							'checkout_date' => $current_date,
							'time_checkout' => date('Y-m-d H:i:s'),
							'effective_time' => $effective_time,
						);
					}
				}
			}
		}
		return $result_list;
	}

	/**
	 * check check out
	 * @param  integer $staff 
	 * @param  date $date  
	 * @return object        
	 */
	function check_check_out($staff, $date)
	{
		$obj = new stdClass();
		$data_check_in_out = $this->db->query('select id, type_check, ' . db_prefix() . 'check_in_out.date from ' . db_prefix() . 'check_in_out where staff_id = ' . $staff . ' and date(' . db_prefix() . 'check_in_out.date) = "' . $date . '" order by id desc limit 1')->row();
		if ($data_check_in_out) {
			if ($data_check_in_out->type_check == 2) {
				//Has check out
				$obj->date = $data_check_in_out->date;
				$obj->result = false;
			}
			if ($data_check_in_out->type_check == 1) {
				//Has check in
				$obj->date = $data_check_in_out->date;
				$obj->result = true;
			}
		} else {
			$obj->date = '';
			$obj->result = false;
		}
		return $obj;
	}

	/**
	 * get bussiness trip info
	 * @param  date $date 
	 * @return object       
	 */
	public function get_bussiness_trip_info($date)
	{
		return $this->db->query('select * from ' . db_prefix() . 'timesheets_requisition_leave where rel_type = 4 and status = 1 and "' . $date . '" between date(start_time) and date(end_time)')->row();
	}

	/**
	 * report by working hours
	 */
	public function report_leave_by_department()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$custom_date_select1 = '';
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-t');
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// last month
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report month"));
					$to_date = date('Y-m-t');
				}
			} elseif ($months_report == 'this_month') {
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
			} elseif ($months_report == 'this_year') {
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			} elseif ($months_report == 'last_year') {
				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date = to_sql_date($this->input->post('report_to'));
			}
		}
		$chart = [];
		$dpm = $this->departments_model->get();

		$list_leave_type = [
			['id' => 1, 'name' => _l('Leave')],
			['id' => 2, 'name' => _l('late')],
			['id' => 6, 'name' => _l('early')],
			['id' => 3, 'name' => _l('Go_out')],
			['id' => 4, 'name' => _l('Go_on_bussiness')],
		];

		foreach ($dpm as $d) {
			$chart['categories'][] = $d['name'];
		}

		$list_type = [];
		foreach ($list_leave_type as $type) {
			$list_temp = [];
			foreach ($dpm as $d) {
				$list_temp[] = $this->count_leave_by_department($d['departmentid'], $type['id'], $from_date, $to_date);
			}
			$list_type[] = ['name' => $type['name'], 'data' => $list_temp];
		}
		$chart['series'] = $list_type;
		return $chart;
	}
	/**
	 * count leave by department
	 * @param  string $departmentid
	 * @param  string $type
	 * @param  string $from_date
	 * @param  string $to_date
	 * @return integer $count
	 */
	public function count_leave_by_department($departmentid, $type, $from_date, $to_date)
	{
		$count = 0;
		$query = 'select count(1) as count from ' . db_prefix() . 'timesheets_requisition_leave where rel_type = ' . $type . ' and staff_id in (select staffid from ' . db_prefix() . 'staff_departments where departmentid = ' . $departmentid . ') and ((date(start_time) between "' . $from_date . '" and "' . $to_date . '") or (date(end_time) between "' . $from_date . '" and "' . $to_date . '"))';
		$data = $this->db->query($query)->row();
		if ($data) {
			$count = $data->count;
		}
		return (int) $count;
	}
	/**
	 * report ratio check in out by workplace
	 */
	public function report_ratio_check_in_out_by_workplace()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$custom_date_select1 = '';
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-t');
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// last month
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report month"));
					$to_date = date('Y-m-t');
				}
			} elseif ($months_report == 'this_month') {
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
			} elseif ($months_report == 'this_year') {
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			} elseif ($months_report == 'last_year') {
				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date = to_sql_date($this->input->post('report_to'));
			}
		}
		$chart = [];
		$list_type = [
			['id' => 1, 'name' => _l('check_in')],
			['id' => 2, 'name' => _l('check_out')],
		];

		$data_workplace = $this->timesheets_model->get_workplace();
		foreach ($data_workplace as $d) {
			$chart['categories'][] = $d['name'];
		}

		$list_serial = [];
		foreach ($list_type as $type) {
			$list_temp = [];
			foreach ($data_workplace as $d) {
				$list_temp[] = $this->count_check_by_workplace($d['id'], $type['id'], $from_date, $to_date);
			}
			$list_serial[] = ['name' => $type['name'], 'data' => $list_temp];
		}
		$chart['series'] = $list_serial;
		return $chart;
	}
	/**
	 * count check by workplace
	 * @param  string $workplace_id
	 * @param  string $type
	 * @param  string $from_date
	 * @param  string $to_date
	 * @return integer $count
	 */
	public function count_check_by_workplace($workplace_id, $type, $from_date, $to_date)
	{
		$count = 0;
		$query = 'select count(1) as count from ' . db_prefix() . 'check_in_out where workplace_id = ' . $workplace_id . ' and type_check = ' . $type . ' and date(date) between "' . $from_date . '" and "' . $to_date . '"';
		$data = $this->db->query($query)->row();
		if ($data) {
			$count = $data->count;
		}
		return (int) $count;
	}
	/**
	 * report of leave by month
	 */
	public function report_of_leave_by_month()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$custom_date_select1 = '';
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-t');
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// last month
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report month"));
					$to_date = date('Y-m-t');
				}
			} elseif ($months_report == 'this_month') {
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
			} elseif ($months_report == 'this_year') {
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			} elseif ($months_report == 'last_year') {
				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date = to_sql_date($this->input->post('report_to'));
			}
		}
		$chart = [];

		$list_month = $this->get_list_month($from_date, $to_date);

		$list_leave_type = [
			['id' => 1, 'name' => _l('Leave')],
			['id' => 2, 'name' => _l('late')],
			['id' => 6, 'name' => _l('early')],
			['id' => 3, 'name' => _l('Go_out')],
			['id' => 4, 'name' => _l('Go_on_bussiness')],
		];

		foreach ($list_month as $d) {
			$dateObj = DateTime::createFromFormat('!m', date('m', strtotime($d)));
			$monthName = $dateObj->format('F');
			$chart['categories'][] = $monthName;
		}

		$list_type = [];
		foreach ($list_leave_type as $type) {
			$list_temp = [];
			foreach ($list_month as $d) {
				$list_temp[] = $this->count_leave_by_month(date('Y-m', strtotime($d)), $type['id']);
			}
			$list_type[] = ['name' => $type['name'], 'data' => $list_temp];
		}
		$chart['series'] = $list_type;
		return $chart;
	}
	/**
	 * count leave by month
	 * @param  string $year_month
	 * @param  string $type
	 * @return  integer $count
	 */
	public function count_leave_by_month($year_month, $type)
	{
		$count = 0;
		$query = 'select count(1) as count from ' . db_prefix() . 'timesheets_requisition_leave where rel_type = ' . $type . ' and "' . $year_month . '" between date_format(start_time, "%Y-%m") and date_format(end_time, "%Y-%m")';
		$data = $this->db->query($query)->row();
		if ($data) {
			$count = $data->count;
		}
		return (int) $count;
	}

	function round_to_next_minutes($datestring, $max_hour)
	{
		$nextHour = strtotime($datestring . ' +' . $max_hour . ' minute');
		return date('Y-m-d H:i:s', $nextHour);
	}

	public function get_datetime_send_notification_forgotten_value($minute)
	{
		$staffs = $this->get_staff_timekeeping_applicable_object(true);
		$result_list = [];
		$current_date = date('Y-m-d');
		foreach ($staffs as $skey => $staff) {
			if (!$this->check_log_send_notify($staff['staffid'], 1, $current_date, 'check_in')) {
				$hour_info = $this->get_info_hour_shift_staff($staff['staffid'], $current_date);
				if ($hour_info && $hour_info->woking_hour > 0) {
					$check_result = $this->has_check_in($staff['staffid'], $current_date);
					if (!$check_result) {
						if ($hour_info->start_working != '') {
							$start_datetime = $current_date . ' ' . $hour_info->start_working;
							$effective_time = $this->round_to_next_minutes($start_datetime, $minute);
							$result_list[] = array(
								'staffid' => $staff['staffid'],
								'effective_time' => $effective_time,
							);
						}
					}
				}
			}
		}
		return $result_list;
	}
	function has_check_in($staff, $date)
	{
		$data_check_in_out = $this->db->query('select id from ' . db_prefix() . 'check_in_out where staff_id = ' . $staff . ' and date(' . db_prefix() . 'check_in_out.date) = "' . $date . '" and type_check = 1 order by id desc limit 1')->row();
		if ($data_check_in_out) {
			return true;
		}
		return false;
	}
	public function send_mail_remider_check_in($staffid)
	{
		$email = $this->get_staff_email($staffid);
		if ($email != '') {
			$this->add_log_send_notify($staffid, 1, date('Y-m-d'), 'check_in');
			$staff_name = get_staff_full_name($staffid);
			$data_send_mail['receiver'] = $email;
			$data_send_mail['staff_name'] = $staff_name;
			$data_send_mail['date_time'] = _d(date('Y-m-d'));
			$template = mail_template('remind_user_check_in', 'timesheets', array_to_object($data_send_mail));
			$template->send();
			$this->notifications($staffid, 'timesheets/timekeeping', _l('remind_you_to_check_in_today_to_record_the_start_time_of_the_shift') . ' ' . _d(date('Y-m-d')));
		}
	}
	public function add_log_send_notify($staffid, $status, $date, $type)
	{
		$this->db->insert(db_prefix() . 'timesheets_log_send_notify', [
			'staffid' => $staffid,
			'sent' => $status,
			'date' => $date,
			'type' => $type,
		]);
	}

	public function check_log_send_notify($staffid, $status, $date, $type)
	{
		$this->db->where('staffid', $staffid);
		$this->db->where('date', $date);
		$this->db->where('sent', $status);
		$this->db->where('type', $type);
		$data = $this->db->get(db_prefix() . 'timesheets_log_send_notify')->row();
		if ($data) {
			return true;
		}
		return false;
	}
	public function get_data_attendance_export($month_filter, $department_filter, $role_filter, $staff_filter)
	{
		$data = $this->input->post();
		$date_ts = $this->format_date($month_filter . '-01');

		$date_ts_end = $this->format_date($month_filter . '-' . date('t'));
		$year = date('Y', strtotime($date_ts));
		$g_month = date('m', strtotime($date_ts));
		$month_filter = date('Y-m', strtotime($date_ts));

		$querystring = 'active=1';
		$department = $department_filter;
		$job_position = $role_filter;

		$month_filter = date('m-Y', strtotime($date_ts));
		$data['check_latch_timesheet'] = $this->check_latch_timesheet($month_filter);
		$staff = '';
		if (isset($staff_filter)) {
			$staff = $staff_filter;
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
			if ($role_filter != '') {
				$staff_querystring .= 'role = ' . $role_filter;
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

		$data['shifts'] = $this->get_shifts();

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
		$staff_row_tk = [];
		$staffs = $this->getStaff('', $newquerystring);

		$data['staffs_setting'] = $this->staff_model->get();
		$data['staffs'] = $staffs;
		if ($data_timekeeping_form == 'timekeeping_task' && $data['check_latch_timesheet'] == false) {
			$result = $this->get_attendance_task($staffs, $g_month, $year);
			$staff_row_tk = $result['staff_row_tk'];
		} else {

			if ($data['check_latch_timesheet'] == false) {
				$result = $this->get_attendance_manual($staffs, $g_month, $year);
				$staff_row_tk = $result['staff_row_tk'];
			}
		}
		return $staff_row_tk;
	}

	/**
	 * automatic insert timesheets
	 * @param      int   $staffid   the staffid
	 * @param      datetime  $time_in   the time in
	 * @param      datetime  $time_out  the time out
	 * @return     boolean
	 */
	public function calculate_attendance_timesheets($staffid, $time_in, $time_out)
	{
		$obj = new stdclass();
		$obj->working_hour = 0;
		$obj->late_hour = 0;
		$obj->early_hour = 0;

		// Ensure time_in is before time_out
		$d1 = strtotime($this->format_date_time($time_in));
		$d2 = strtotime($this->format_date_time($time_out));
		if ($d1 > $d2) {
			$temp = $time_in;
			$time_in = $time_out;
			$time_out = $temp;
		}

		// Calculate total working hours
		$time_in_ts = strtotime($time_in);
		$time_out_ts = strtotime($time_out);
		$total_hours = round(abs($time_out_ts - $time_in_ts) / (60 * 60), 2);

		$obj->working_hour = $total_hours;
		$obj->late_hour = 0; // Not checking against shift times
		$obj->early_hour = 0; // Not checking against shift times

		return $obj;
	}
	/**
	 * get next shift date
	 * @param  integer  $staff_id
	 * @param  integer  $date
	 * @param  integer $count
	 */
	public function get_next_shift_date($staff_id, $date, $count = 0)
	{
		if ($count == 30) {
			return '';
		}
		$count++;
		$date = $this->format_date($date);
		$data_work_time = $this->get_hour_shift_staff($staff_id, $date);
		$data_day_off = $this->get_day_off_staff_by_date($staff_id, $date);

		if ($data_work_time > 0 && count($data_day_off) == 0) {
			return $date;
		} else {
			$next_date = date('Y-m-d', strtotime($date . ' +1 day'));
			return $this->get_next_shift_date($staff_id, $next_date, $count);
		}
	}

	/**
	 * get date leave
	 * @return object
	 */
	public function get_date_leave($year = '', $check_period = false)
	{
		$start_month_for_annual_leave_cycle = '1';
		$start_month = get_timesheets_option('start_month_for_annual_leave_cycle');
		if ($start_month) {
			$start_month_for_annual_leave_cycle = $start_month;
		}

		$start_year_for_annual_leave_cycle = date('Y');
		if ($year != '') {
			$start_year_for_annual_leave_cycle = $year;
		}
		$from_date = $start_year_for_annual_leave_cycle . '-' . (strlen($start_month_for_annual_leave_cycle) == 1 ? '0' : '') . $start_month_for_annual_leave_cycle . '-01';

		if ($check_period == true) {
			if (strtotime(date('Y-m-d')) < strtotime($from_date)) {
				$start_year_for_annual_leave_cycle = date('Y') - 1;
				$from_date = $start_year_for_annual_leave_cycle . '-' . (strlen($start_month_for_annual_leave_cycle) == 1 ? '0' : '') . $start_month_for_annual_leave_cycle . '-01';
			}
		}
		$to_date = date('Y-m-d', strtotime('+11 month', strtotime($from_date)));
		$obj = new stdclass();
		$obj->from_date = $from_date;
		$obj->ending_date = date('Y-m', strtotime($to_date)) . '-' . date('t', strtotime($to_date));
		return $obj;
	}

	/**
	 * get current date off
	 * @param  integer $staff_id
	 * @return integer
	 */
	public function get_current_date_off($staff_id, $type_of_leave = '')
	{
		$obj = new stdclass();
		$obj->number_day_off = 0;
		$obj->days_off = 0;
		$data_date = $this->get_date_leave('', true);
		$current_date = date('Y-m-d');
		if ((strtotime($current_date) >= strtotime($data_date->from_date)) && (strtotime($current_date) <= strtotime($data_date->ending_date))) {
			$year = date('Y', strtotime($data_date->from_date));
			$day_off = $this->get_day_off($staff_id, $year, $type_of_leave);
			if ($day_off != null) {
				$obj->number_day_off = $day_off->remain;
				if ($obj->number_day_off < 0) {
					$obj->number_day_off = 0;
				}
				$obj->days_off = $day_off->days_off;
				if ($obj->days_off > $day_off->total) {
					$obj->days_off = $day_off->total;
				}
			}
		}
		return $obj;
	}

	/**
	 * get list leave application
	 * @param  array $data
	 */
	public function get_list_leave_application($data)
	{
		$from_date = $data['start'];
		$to_date = $data['end'];
		$user_id = get_staff_user_id();
		$where = '';

		if (!is_admin() && !has_permission('leave_management', '', 'view')) {
			$add_query = ' IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 8,"Leave",IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 2,"maternity_leave",IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 4,"private_work_without_pay",IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 1,"sick_leave", IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 0,' . db_prefix() . 'timesheets_requisition_leave.type_of_leave_text,"")))))';

			$type_of_leave = 'IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 1,' . $add_query . ', IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 2,"late", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 3,"Go_out", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 4,"Go_on_bussiness", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 5,"quit_job", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 6,"early",""))))))';
			$where = ' AND ((' . $user_id . ' in (select staffid from ' . db_prefix() . 'timesheets_approval_details where rel_type = ' . $type_of_leave . ' and rel_id = ' . db_prefix() . 'timesheets_requisition_leave.id) or ' . db_prefix() . 'timesheets_requisition_leave.staff_id = ' . $user_id . ')' . timesheet_staff_manager_query('leave_management', 'staff_id', 'OR') . ')';
		}

		if (isset($data['status'])) {
			$where_status = '';
			$status = $data['status'];
			foreach ($status as $statues) {
				if ($status != '') {
					if ($where_status == '') {
						$where_status .= ' AND (status = "' . $statues . '"';
					} else {
						$where_status .= ' OR status = "' . $statues . '"';
					}
				}
			}
			if ($where_status != '') {
				$where_status .= ')';
				$where .= $where_status;
			}
		}

		if (isset($data['department'])) {
			$where_dpm = '';
			$department = $data['department'];
			foreach ($department as $statues) {
				if ($department != '') {
					if ($where_dpm == '') {
						$where_dpm = ' AND (staff_id IN (SELECT staffid FROM ' . db_prefix() . 'staff_departments WHERE departmentid = ' . $statues . ')';
					} else {
						$where_dpm .= ' OR staff_id IN (SELECT staffid FROM ' . db_prefix() . 'staff_departments WHERE departmentid = ' . $statues . ')';
					}
				}
			}
			if ($where_dpm != '') {
				$where_dpm .= ')';
				$where .= $where_dpm;
			}
		}
		if (isset($data['rel_type'])) {
			$where_rel_type = '';
			$rel_type = $data['rel_type'];
			foreach ($rel_type as $statues) {
				if ($rel_type != '') {
					if ($where_rel_type == '') {
						$where_rel_type .= ' AND (rel_type = "' . $statues . '"';
					} else {
						$where_rel_type .= ' OR rel_type = "' . $statues . '"';
					}
				}
			}
			if ($where_rel_type != '') {
				$where_rel_type .= ')';
				$where .= $where_rel_type;
			}
		}
		if (isset($data['chose'])) {
			$chose = $data['chose'];
			$sql_where = '';
			if ($chose != 'all') {
				$sql_where .= ' AND (' . get_staff_user_id() . ' IN (SELECT staffid FROM ' . db_prefix() . 'timesheets_approval_details where ' . db_prefix() . 'timesheets_approval_details.rel_type IN ("Leave","maternity_leave","private_work_without_pay","sick_leave","late","early","Go_out","Go_on_bussiness") AND ' . db_prefix() . 'timesheets_approval_details.rel_id = ' . db_prefix() . 'timesheets_requisition_leave.id ))';
			}
			if ($sql_where != '') {
				$where .= $sql_where;
			}
		}

		$total_query = 'select * from ' . db_prefix() . 'timesheets_requisition_leave where ((date(start_time) between "' . $from_date . '" and "' . $to_date . '") or (date(end_time) between "' . $from_date . '" and "' . $to_date . '"))' . $where;
		return $this->db->query($total_query)->result_array();
	}

	/**
	 * get calendar leave data
	 * @param  array $data
	 * @return array
	 */
	public function get_calendar_leave_data($data)
	{
		$data_calendar = [];
		$data_leave_application = $this->get_list_leave_application($data);
		foreach ($data_leave_application as $data_row) {
			$staff = '';
			if ($data_row['staff_id'] != '') {
				$staff = get_staff_full_name($data_row['staff_id']);
			}
			$from_date = date('Y-m-d', strtotime($data_row['start_time']));
			$to_date = date('Y-m-d', strtotime($data_row['end_time']));
			$color = "#43c2e8";
			if ($data_row['status'] == 1) {
				$color = "#60c259";
			}
			if ($data_row['status'] == 2) {
				$color = "#db5c53";
			}

			$list_date = $this->get_list_date($from_date, $to_date);
			foreach ($list_date as $date) {
				if ($this->valid_date($data_row['staff_id'], $date)) {
					$data_calendar[] = [
						'title' => $data_row['subject'],
						'color' => $color,
						'_tooltip' => $data_row['subject'] . ' -' . _l('staff') . ': ' . $staff . ' -' . _l('From_Date') . ': ' . _d($from_date) . ' -' . _l('To_Date') . ': ' . _d($to_date),
						'url' => admin_url('timesheets/requisition_detail/' . $data_row['id']),
						'date' => $date,
						'start' => $date,
						'end' => $date,
					];
				}
			}
		}
		return $data_calendar;
	}

	/**
	 * delete permission
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_permission($id)
	{
		$str_permissions = '';
		foreach (list_timesheet_permisstion() as $per_key => $per_value) {
			if (strlen($str_permissions) > 0) {
				$str_permissions .= ",'" . $per_value . "'";
			} else {
				$str_permissions .= "'" . $per_value . "'";
			}
		}
		$sql_where = " feature IN (" . $str_permissions . ") ";
		$this->db->where('staff_id', $id);
		$this->db->where($sql_where);
		$this->db->delete(db_prefix() . 'staff_permissions');

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get staff id by approve value
	 * @param  array $data
	 * @param  array $approve_value
	 * @return array
	 */
	public function get_staff_id_by_approve_value($data, $approve_value)
	{
		$this->load->model('departments_model');
		$this->load->model('staff_model');

		$staff_ids = []; // Store multiple IDs if applicable

		if ($approve_value == 'head_of_department') {
			$department_info = $this->departments_model->get_staff_departments($data->staff_addedfrom);

			if (!empty($department_info) && isset($department_info[0]['manager_id'])) {
				$staff_ids[] = $department_info[0]['manager_id'];
			}
		} elseif ($approve_value == 'direct_manager') {

			$team_manage = get_team_manage($data->staff_addedfrom);

			if (!empty($team_manage)) {
				foreach ($team_manage as $team_member) {
					$staff_ids[] = $team_member['team_manage_id']; // Extract all team managers
				}
			}
		}

		return $staff_ids; // Return an array of IDs (can be single or multiple)
	}

	/**
	 * add timesheet leave
	 * @param string $type
	 * @param integer $rel_id
	 */
	public function add_timesheet_leave($type, $rel_id)
	{
		$this->db->where('id', $rel_id);
		$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
		$st = $requisition_leave->start_time;
		$et = $requisition_leave->end_time;
		$staffid = $requisition_leave->staff_id;
		if ($staffid == '') {
			$staff_id = get_staff_user_id();
		}
		$number_of_day = $requisition_leave->number_of_leaving_day;
		if ($requisition_leave->start_time != '' && $requisition_leave->end_time != '') {
			$start_time = date('Y-m-d', strtotime($requisition_leave->start_time));
			$end_time = date('Y-m-d', strtotime($requisition_leave->end_time));
			$list_date = $this->get_list_date($start_time, $end_time);
			$list_af_date = [];
			foreach ($list_date as $key => $next_start_date) {
				$data_work_time = $this->get_hour_shift_staff($staffid, $next_start_date);
				$data_day_off = $this->get_day_off_staff_by_date($staffid, $next_start_date);
				if ($data_work_time > 0 && count($data_day_off) == 0) {
					$list_af_date[] = $next_start_date;
				}
			}
			if (count($list_af_date) == 1) {
				$date_work = $start_time;
				$work_time = $this->get_hour_shift_staff($staffid, $date_work);

				$this->db->where('staff_id', $staffid);
				$this->db->where('date_work', $date_work);
				$this->db->where('type', 'W');
				$tslv = $this->db->get(db_prefix() . 'timesheets_timesheet')->row();

				if ($tslv) {
					if ($number_of_day < 1 && $tslv->value > ($work_time * $number_of_day)) {
						$this->db->where('staff_id', $staffid);
						$this->db->where('date_work', $date_work);
						$this->db->where('type', 'W');
						$this->db->update(db_prefix() . 'timesheets_timesheet', ['value' => ($work_time * $number_of_day)]);
					} else {
						$this->db->where('staff_id', $staffid);
						$this->db->where('date_work', $date_work);
						$this->db->where('type', 'W');
						$this->db->delete(db_prefix() . 'timesheets_timesheet');
					}
				}
				if ($number_of_day < 1) {
					$work_time = $work_time * $number_of_day;
				}

				$this->db->insert(db_prefix() . 'timesheets_timesheet', [
					'staff_id' => $staffid,
					'date_work' => $date_work,
					'value' => $work_time,
					'add_from' => $staffid,
					'relate_id' => $rel_id,
					'relate_type' => 'leave',
					'type' => $type,
				]);
			} else {
				$count_array = count($list_af_date);
				$date_end = '';
				$count_day = $number_of_day;
				foreach ($list_af_date as $key => $date_work) {
					$work_time = $this->get_hour_shift_staff($staffid, $date_work);
					$this->db->where('staff_id', $staffid);
					$this->db->where('date_work', $date_work);
					$this->db->where('type', 'W');
					$tslv = $this->db->get(db_prefix() . 'timesheets_timesheet')->row();

					if ($tslv) {
						if ($count_day < 1 && $tslv->value > ($work_time / 2)) {
							$this->db->where('id', $tslv->id);
							$this->db->update(db_prefix() . 'timesheets_timesheet', ['value' => ($work_time / 2)]);
						} else {
							$this->db->where('id', $tslv->id);
							$this->db->delete(db_prefix() . 'timesheets_timesheet');
						}
					}
					if ($count_day < 1) {
						$work_time = $work_time / 2;
					}
					$this->db->insert(db_prefix() . 'timesheets_timesheet', [
						'staff_id' => $staffid,
						'date_work' => $date_work,
						'value' => $work_time,
						'add_from' => $staffid,
						'relate_id' => $rel_id,
						'relate_type' => 'leave',
						'type' => $type,
					]);
					$count_day -= 1;
				}
			}
		}
		return true;
	}
	/**
	 * send notify approval expiration
	 * @param  string $curr_date
	 */
	function send_notify_approval_expiration($curr_date)
	{
		if (!$this->check_log_send_notify(0, 1, $curr_date, 'approval_expiration')) {
			$this->add_log_send_notify(0, 1, $curr_date, 'approval_expiration');
			$this->db->where('approval_deadline', $curr_date);
			$this->db->where('approve is null');
			$data_approve_detail = $this->db->get(db_prefix() . 'timesheets_approval_details')->result_array();
			foreach ($data_approve_detail as $key => $detail) {
				$approve_id = $detail['staffid'];
				$link = 'timesheets/requisition_detail/' . $detail['rel_id'];
				$this->notifications($approve_id, $link, _l('ts_there_is_an_expired_approval_request_please_approve_it'));
			}
		}
	}

	/**
	 * add type of leave
	 * @param array $data
	 */
	public function add_type_of_leave($data)
	{
		$this->db->insert(db_prefix() . 'timesheets_type_of_leave', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return $insert_id;
		}
		return 0;
	}

	/**
	 * update type of leave
	 * @param array $data
	 */
	public function update_type_of_leave($data)
	{
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'timesheets_type_of_leave', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get type of leave
	 * @param  string $id
	 * @return array or object
	 */
	public function get_type_of_leave($id = '')
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
		} else {
			return $this->db->get(db_prefix() . 'timesheets_type_of_leave')->result_array();
		}
	}
	/**
	 * get custom leave name by slug
	 * @param  string $slug
	 * @return string
	 */
	public function get_custom_leave_name_by_slug($slug = '')
	{
		$slug_name = '';
		$this->db->where('slug', $slug);
		$data = $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
		if ($data) {
			$slug_name = $data->type_name;
		}
		return $slug_name;
	}
	/**
	 * get custom leave name by symbol
	 * @param  string $slug
	 * @return string
	 */
	public function get_custom_leave_name_by_symbol($symbol = '')
	{
		$symbol_name = '';
		$this->db->where('symbol', $symbol);
		$data = $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
		if ($data) {
			$symbol_name = $data->type_name;
		}
		return $symbol_name;
	}
	/**
	 * get custom leave by slug
	 * @param  string $slug
	 * @return string
	 */
	public function get_custom_leave_by_slug($slug = '')
	{
		$this->db->where('slug', $slug);
		return $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
	}

	/**
	 * delete type of leave
	 * @param  integer $id
	 * @return integer
	 */
	public function delete_type_of_leave($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'timesheets_type_of_leave');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * check duplicate character type of leave
	 * @param  string $character
	 * @param  integer $id
	 * @return boolean
	 */
	public function check_duplicate_character_type_of_leave($character, $id = '')
	{
		$res = false;
		$this->db->where('symbol', $character);
		if ($id != '') {
			$this->db->where('id != ' . $id);
		}
		$data = $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
		if ($data) {
			$res = true;
		}
		return $res;
	}

	/**
	 * check duplicate slug type of leave
	 * @param  string $slug
	 * @param  integer $id
	 * @return boolean
	 */
	public function check_duplicate_slug_type_of_leave($slug, $id = '')
	{
		$res = false;
		$this->db->where('slug', $slug);
		if ($id != '') {
			$this->db->where('id != ' . $id);
		}
		$data = $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
		if ($data) {
			$res = true;
		}
		return $res;
	}

	/**
	 * get list approver
	 * @param  integer $rel_id
	 * @param  string $rel_type
	 * @return  array
	 */
	public function get_list_approver($rel_id, $rel_type)
	{
		$result = [];
		$this->db->select('staffid');
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$data = $this->db->get(db_prefix() . 'timesheets_approval_details')->result_array();
		foreach ($data as $key => $row) {
			$result[] = $row['staffid'];
		}
		return $result;
	}
	/**
	 * valid_date
	 * @param  integer $staffid
	 * @param  date $date
	 * @return boolean
	 */
	public function valid_date($staffid, $date)
	{
		$max_hour = $this->get_hour_shift_staff($staffid, $date);
		$check_holiday = $this->check_holiday($staffid, $date);
		$result_lack = '';
		if ($max_hour > 0) {
			if (!$check_holiday) {
				return true;
			} else {
				// Is holiday
				return false;
			}
			return true;
		}
		return false;
	}
	/**
	 * notify create new leave
	 * @param  integer $rel_id
	 * @param  string $rel_type
	 * @param  integer $status
	 * @return integer
	 */
	public function notify_create_new_leave($rel_id, $rel_type)
	{
		$additional_data = '';
		$rel_type = strtolower($rel_type);
		switch ($rel_type) {
			case 'leave':
				$additional_data = _l('leave');
				break;
			case 'maternity_leave':
				$additional_data = _l('leave');
				break;
			case 'private_work_without_pay':
				$additional_data = _l('leave');
				break;
			case 'sick_leave':
				$additional_data = _l('sick_leave');
				break;
			case 'late':
				$additional_data = _l('late');
				break;
			case 'early':
				$additional_data = _l('early');
				break;
			case 'go_out':
				$additional_data = _l('go_out');
				break;
			case 'go_on_bussiness':
				$additional_data = _l('go_on_bussiness');
				break;
			case 'additional_timesheets':
				$additional_data = _l('additional_timesheets');
				break;
			default:
				if ($rel_type != '' && $rel_id != '') {
					$this->db->select('type_of_leave_text');
					$this->db->where('id', $rel_id);
					$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
					if ($requisition_leave) {
						$slug = $requisition_leave->type_of_leave_text;
						$this->db->where('slug', $slug);
						$data_type_custom = $this->db->get(db_prefix() . 'timesheets_type_of_leave')->row();
						if ($data_type_custom) {
							$additional_data = $data_type_custom->type_name;
						}
					}
				}
				break;
		}
		$check_approve_status = $this->check_approval_details($rel_id, $rel_type);
		if (isset($check_approve_status['notification_recipient'])) {
			$notification_recipient = explode(',', $check_approve_status['notification_recipient']);
			$link = admin_url('timesheets/requisition_detail/' . $rel_id);
			foreach ($notification_recipient as $recipient_id) {
				$notified_rc = add_notification([
					'description' => 'created_a_new_leave_application',
					'touserid' => $recipient_id,
					'link' => 'timesheets/requisition_detail/' . $rel_id,
					'additional_data' => serialize([
						$additional_data,
					]),
				]);
				if ($notified_rc) {
					pusher_trigger_notification($recipient_id);
				}
				$email = $this->get_staff_email($recipient_id);
				if ($email != '') {
					$staff_request = '';
					$request_name = '';
					$data_request_leave = $this->timesheets_model->get_request_leave($rel_id);
					if ($data_request_leave) {
						$staff_request = $data_request_leave->staff_id;
						$request_name = $data_request_leave->subject;
					}
					$data_send_mail['receiver'] = $email;
					$data_send_mail['date_time'] = _dt(date('Y-m-d H:i:s'));
					$data_send_mail['staff_name'] = get_staff_full_name($staff_request);
					$data_send_mail['link'] = '<a href="' . $link . '">' . $request_name . '</a>';
					$template = mail_template('new_leave_application_send_to_notification_recipient', 'timesheets', array_to_object($data_send_mail));
					$template->send();
				}
			}
		}
	}

	/**
	 * set leave
	 * @param object $data
	 */
	public function set_valid_ip($data)
	{
		if (isset($data['timekeeping_enable_valid_ip'])) {
			$this->db->where('option_name', 'timekeeping_enable_valid_ip');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => $data['timekeeping_enable_valid_ip']
			]);
		} else {
			$this->db->where('option_name', 'timekeeping_enable_valid_ip');
			$this->db->update(db_prefix() . 'timesheets_option', [
				'option_val' => '0',
			]);
		}
		$list_client_ip = json_decode($data['list_ip_data']);
		$list_db_ip = $this->get_valid_ip();
		$affectedrows = 0;

		$this->db->from(db_prefix() . 'timesheets_valid_ip');
		$this->db->truncate();

		foreach ($list_client_ip as $key => $value) {
			if (isset($value[0]) && ($value[0] != null)) {
				$data_update['ip'] = $value[0];
				$this->db->insert(db_prefix() . 'timesheets_valid_ip', $data_update);
				$affectedrows++;
			}
		}

		return $affectedrows;
	}

	/**
	 * get valid ip
	 * @param  integer
	 * @return array or object
	 */
	public function get_valid_ip($id = '')
	{
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'timesheets_valid_ip')->row();
		} else {
			return $this->db->get(db_prefix() . 'timesheets_valid_ip')->result_array();
		}
	}

	/**
	 * get customer email route point
	 * @param  string $id 
	 * @return      
	 */
	public function get_customer_email_route_point($id = '')
	{
		$email = '';
		$data = $this->db->query('select email from ' . db_prefix() . 'timesheets_route_point a left join ' . db_prefix() . 'clients b on a.related_id = b.userid left join ' . db_prefix() . 'contacts c on b.userid = c.userid and is_primary = 1 where a.id = ' . $id . ' and a.related_to = 1')->row();
		if ($data) {
			$email = $data->email;
		}
		return $email;
	}

	/**
	 * update requisition after approve
	 * @param  integer $id            
	 * @param  integer $status        
	 * @param  string $type_of_leave 
	 */
	public function update_requisition_after_approve($id, $status, $type_of_leave)
	{
		$type_of_leave_data = $this->get_type_of_leave_id($type_of_leave);
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'timesheets_requisition_leave', ['status' => $status]);
		if ($status == 1) {
			$this->db->where('id', $id);
			$requisition_leave = $this->db->get(db_prefix() . 'timesheets_requisition_leave')->row();
			if ($requisition_leave) {
				$year = $this->get_first_year_of_period($requisition_leave->start_time);
				// Get total leave in year of staff
				$day_off = $this->get_day_off($requisition_leave->staff_id, $year, $type_of_leave_data->type_id);
				// Number of leaving day
				$dd = $requisition_leave->number_of_leaving_day;
				$year = $this->get_first_year_of_period($requisition_leave->start_time);
				$update_days_off = abs($day_off->days_off + $dd);
				$update_remain = abs($day_off->total) - $update_days_off;
				$this->db->where('type_of_leave', $type_of_leave_data->type_id);
				$this->db->where('staffid', $requisition_leave->staff_id);
				$this->db->where('year', $year);
				$this->db->update(db_prefix() . 'timesheets_day_off', [
					'remain' => abs($update_remain),
					'days_off' => $update_days_off,
				]);
				$this->add_timesheet_leave($type_of_leave_data->timesheet_type, $id);
			}
		}
	}

	/**
	 * get type of leave id
	 * @param  string $type 
	 */
	public function get_type_of_leave_id($type)
	{
		$obj = new stdClass();
		$obj->type_id = 0;
		$obj->timesheet_type = '';
		switch (strtolower($type)) {
			case 'leave':
				$obj->type_id = 8;
				$obj->timesheet_type = 'AL';
				break;
			case 'maternity_leave':
				$obj->type_id = 2;
				$obj->timesheet_type = 'M';
				break;
			case 'private_work_without_pay':
				$obj->type_id = 4;
				$obj->timesheet_type = 'P';
				break;
			case 'sick_leave':
				$obj->type_id = 1;
				$obj->timesheet_type = 'SI';
				break;
			default:
				$data_custome_type = $this->get_custom_leave_by_slug($type);
				if ($data_custome_type) {
					$obj->type_id = $type;
					$obj->timesheet_type = $data_custome_type->symbol;
				}
				break;
		}
		return $obj;
	}

	/**
	 * login
	 * @param  string $email    
	 * @param  string $password 
	 * @return boolean           
	 */
	public function login($email, $password)
	{
		$this->load->model('staff_model');
		$user = $this->staff_model->get('', ['email' => $email]);
		if ($user) {
			// Email is okey lets check the password now
			if (app_hasher()->CheckPassword($password, $user[0]['password'])) {
				if ($user[0]['active'] == 0) {
					return [
						'status' => false,
						'message' => 'Inactive user.',
					];
				} else {
					$user_data = [
						'staff_user_id'   => $user[0]['staffid'],
						'staff_logged_in' => true,
					];

					$this->session->set_userdata($user_data);

					$user[0]['permissions'] = $this->staff_model->get_staff_permissions($user[0]['staffid']);
					return $user[0];
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}
	/**
	 * logout
	 */
	public function logout()
	{
		$this->session->unset_userdata('staff_user_id');
		$this->session->unset_userdata('staff_logged_in');

		$this->session->sess_destroy();
	}
	/**
	 * check token logout
	 * @param  string $token 
	 * @return object or boolean        
	 */
	public function check_token_logout($token)
	{
		$this->db->where('token', $token);
		$user = $this->db->get(db_prefix() . 'staff')->row();
		if ($user) {
			return $user;
		}
		return false;
	}

	/**
	 * calculate working hour
	 * @param  string $value 
	 * @return string        
	 */
	public function calculate_working_hour($value)
	{
		$hours = round($value, 2);
		$h = intval($hours);
		$m = round(($hours - $h) * 60);
		return $h . ':' . ((strlen($m) == 1) ? $m . '0' : $m);
	}

	public function calculate_total_working_hour($time_array)
	{
		// Array containing time in string format
		$sum = strtotime('00:00:00');
		$totaltime = 0;
		foreach ($time_array as $element) {
			// Converting the time into seconds
			$timeinsec = strtotime($element) - $sum;
			// Sum the time with previous value
			$totaltime = $totaltime + $timeinsec;
		}
		// Totaltime is the summation of all
		// time in seconds
		// Hours is obtained by dividing
		// totaltime with 3600
		$h = intval($totaltime / 3600);
		$totaltime = $totaltime - ($h * 3600);
		// Minutes is obtained by dividing
		// remaining total time with 60
		$m = intval($totaltime / 60);
		// Remaining value is seconds
		$s = $totaltime - ($m * 60);
		// Printing the result
		return "$h:$m";
	}

	/**
	 * cell color checkin out
	 * @param  integer $staffid 
	 * @param  string $date    
	 * @return string          
	 */
	public function cell_color_checkin_out($staffid, $date)
	{
		$color = '#fff';
		if ($this->has_check_in($staffid, $date)) {
			$color = '#f8e3d3';
		}
		if ($this->full_check_in_out($staffid, $date)) {
			$color = '#dff0d8';
		}
		return $color;
	}

	/**
	 * full check in out
	 * @param  integer $staffid 
	 * @param  string $date    
	 * @return boolean          
	 */
	public function full_check_in_out($staffid, $date)
	{
		$data_check_in_out = $this->get_list_check_in_out($date, $staffid);
		$next_key = '';
		foreach ($data_check_in_out as $key => $value) {
			if ($value['type_check'] == 2 && $next_key == $key) {
				return true;
			}
			if ($value['type_check'] == 1) {
				$next_key = $key + 1;
			}
		}
		return false;
	}

	/**
	 * @param  integer ID (option)
	 * @param  boolean (optional)
	 * @return mixed
	 * Get departments where staff belongs
	 * If $onlyids passed return only departmentsID (simple array) if not returns array of all departments
	 */
	public function get_staff_departments($userid = false, $onlyids = false)
	{
		if ($userid == false) {
			$userid = get_staff_user_id();
		}
		if ($onlyids == false) {
			$this->db->select();
		} else {
			$this->db->select(db_prefix() . 'staff_departments.departmentid');
		}
		$this->db->from(db_prefix() . 'staff_departments');
		$this->db->join(db_prefix() . 'departments', db_prefix() . 'staff_departments.departmentid = ' . db_prefix() . 'departments.departmentid', 'left');
		$this->db->where('staffid', $userid);
		$departments = $this->db->get()->result_array();
		if ($onlyids == true) {
			$departmentsid = [];
			foreach ($departments as $department) {
				array_push($departmentsid, $department['departmentid']);
			}
			return $departmentsid;
		}
		return $departments;
	}

	public function checkin_timesheet($data)
	{
		$affectedrows = 0;
		if (!isset($data['date'])) {
			$data['date'] = date('Y-m-d H:i:s');
		}
		$date = date('Y-m-d', strtotime($data['date']));
		if (!isset($data['staff_id'])) {
			$data['staff_id'] = get_staff_user_id();
		}
		$affectedrows = 0;
		$data['route_point_id'] = '';
		$data['workplace_id'] = '';
		$this->db->insert(db_prefix() . 'check_in_out', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			$affectedrows++;
		}

		$this->add_check_in_out_value_to_timesheet($data['staff_id'], $date);
		if ($affectedrows > 0) {
			$staff_receive = get_timesheets_option('attendance_notice_recipient');
			if ($staff_receive && $staff_receive != '' && $staff_array_id = explode(',', $staff_receive)) {
				$type_check_email = '';
				$type_check_notify = '';
				if ($data['type_check'] == 1) {
					$type_check_email = strtolower(_l('checked_in'));
					$type_check_notify = strtolower(_l('checked_in_at'));
				} else {
					$type_check_email = strtolower(_l('checked_out'));
					$type_check_notify = strtolower(_l('checked_out_at'));
				}
				foreach ($staff_array_id as $key => $staffid) {
					$email = $this->get_staff_email($staffid);
					if ($email != '') {
						$staff_name = get_staff_full_name($data['staff_id']);
						$data_send_mail = new stdClass();
						$data_send_mail->receiver = $email;
						$data_send_mail->staff_name = $staff_name;
						$data_send_mail->type_check = $type_check_email;
						$data_send_mail->date_time = _d($data['date']);
						$template = mail_template('attendance_notice', 'timesheets', $data_send_mail);
						$template->send();
						$this->notifications($staffid, 'timesheets/requisition_manage', $type_check_notify . ' ' . _d($data['date']));
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * get type check
	 * @param  integer $staffid 
	 * @param  string $date    
	 * @return boolean          
	 */
	public function get_type_check($staffid, $date)
	{
		$check_result = $this->check_check_out($staffid, date('Y-m-d', strtotime($date)));
		if (!$check_result->result) {
			return 1;
		} else {
			return 2;
		}
	}

	/**
	 * get date leave
	 * @return object
	 */
	public function get_date_leave_from_date($date = '')
	{
		if ($date == '') {
			$date = date('Y-m-d');
		}
		$start_month_for_annual_leave_cycle = '1';
		$start_month = get_timesheets_option('start_month_for_annual_leave_cycle');
		if ($start_month) {
			$start_month_for_annual_leave_cycle = $start_month;
		}
		$year = date('Y', strtotime($date));
		$start_year_for_annual_leave_cycle = $year;
		$from_date = $start_year_for_annual_leave_cycle . '-' . (strlen($start_month_for_annual_leave_cycle) == 1 ? '0' : '') . $start_month_for_annual_leave_cycle . '-01';
		if (strtotime(date('Y-m-d', strtotime($date))) < strtotime($from_date)) {
			$start_year_for_annual_leave_cycle = ($year - 1);
			$from_date = $start_year_for_annual_leave_cycle . '-' . (strlen($start_month_for_annual_leave_cycle) == 1 ? '0' : '') . $start_month_for_annual_leave_cycle . '-01';
		}
		$to_date = date('Y-m-d', strtotime('+11 month', strtotime($from_date)));
		$obj = new stdclass();
		$obj->from_date = $from_date;
		$obj->ending_date = date('Y-m', strtotime($to_date)) . '-' . date('t', strtotime($to_date));
		return $obj;
	}

	/**
	 * get first year of period
	 */
	public function get_first_year_of_period($date = '')
	{
		if ($date == '') {
			$data_date = $this->get_date_leave('', true);
			$year = date('Y', strtotime($data_date->from_date));
			return $year;
		} else {
			$data_date = $this->get_date_leave_from_date($date);
			$year = date('Y', strtotime($data_date->from_date));
			return $year;
		}
	}

	/**
	 * get vacation days of the year
	 * @param  integer $staff_id
	 * @return integer $year
	 */
	public function get_requisition_number_day_off_manual_leave($staff_id, $year = false)
	{
		if ($year == false) {
			$year = date('Y');
		}
		$result_total_day_off = $this->get_day_off($staff_id, $year, 8);
		if ($result_total_day_off) {
			$total = (float) $result_total_day_off->total;
			$remain = (float) $result_total_day_off->remain;
		} else {
			$total = 0;
			$remain = 0;
		}
		$data['total'] = $total;
		$data['remain'] = $remain;
		return $data;
	}

	/**
	 * get_list_holiday
	 * @param  integer $staffid
	 * @param  string $from_date
	 * @param  string $to_date
	 * @return array
	 */
	public function get_list_holiday($staffid, $from_date, $to_date)
	{
		$department_id = '';
		$role_id = '';
		$departments = $this->departments_model->get_staff_departments($staffid, true);
		$staff_data = $this->staff_model->get($staffid);

		if ($staff_data) {
			$role_id = $staff_data->role;
		}

		$where_group = '';
		if ($role_id != '') {
			$where_group .= 'IF(position != "", find_in_set(' . $role_id . ',position), 1=1)';
		} else {
			$where_group .= 'position = ""';
		}

		foreach ($departments as $key => $value) {
			if ($where_group != '') {
				$where_group .= ' AND IF(department != "",find_in_set(' . $value . ',department), 1=1)';
			} else {
				$where_group = 'IF(department != "", find_in_set(' . $value . ',department), 1=1)';
			}
		}

		if ($where_group != '') {
			$where_group = ' AND (' . $where_group . ')';
		}

		$day_off = $this->db->query('select * from ' . db_prefix() . 'day_off where ((repeat_by_year = 0 AND break_date BETWEEN \'' . $from_date . '\' AND \'' . $to_date . '\') OR (repeat_by_year = 1 AND MONTH(break_date) >= MONTH(\'' . $from_date . '\') AND MONTH(break_date) <= MONTH(\'' . $to_date . '\'))) ' . $where_group)->result_array();

		$list_date = [];
		foreach ($day_off as $key => $value) {
			$list_date[date('m-d', strtotime($value['break_date']))] = $value['off_type'];
		}

		return $list_date;
	}

	/**
	 * get hour shift staff
	 * @param  integer $staff_id
	 * @param  array $list_date
	 * @return array
	 */
	public function get_list_hour_shift_staff($staff_id, $list_date)
	{

		$list_hour_shift = [];
		foreach ($list_date as $date) {
			$result = 0;
			$data_shift_list = $this->get_shift_work_staff_by_date($staff_id, $date);

			foreach ($data_shift_list as $ss) {
				$data_shift_type = $this->get_shift_type($ss);
				if ($data_shift_type) {
					$hour = $this->get_hour($data_shift_type->time_start_work, $data_shift_type->time_end_work);
					$lunch_hour = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
					$result += abs($hour - $lunch_hour);
				}
			}

			$list_hour_shift[$date] = $result;
		}
		return $list_hour_shift;
	}

	/**
	 * list cell color checkin out
	 * @param  integer $staffid 
	 * @param  array $list_date    
	 * @param  string $from_date    
	 * @param  string $to_date    
	 * @return array          
	 */
	public function list_cell_color_checkin_out($staffid, $list_date, $from_date, $to_date)
	{
		$list_color = [];

		$check_in_out = $this->db->query('SELECT DISTINCT date_format(date, \'%Y-%m-%d\') as date, type_check FROM ' . db_prefix() . 'check_in_out WHERE date BETWEEN \'' . $from_date . '\' AND \'' . $to_date . '\' AND staff_id = "' . $staffid . '"')->result_array();;

		$has_check_in = [];
		$has_check_out = [];
		foreach ($check_in_out as $value) {
			if ($value['type_check'] == 1 && !isset($has_check_in[$value['date']])) {
				$has_check_in[$value['date']] = 1;
			}

			if ($value['type_check'] == 2 && !isset($has_check_out[$value['date']])) {
				$has_check_out[$value['date']] = 1;
			}
		}

		foreach ($list_date as $date) {
			$color = '#fff';

			if (isset($has_check_in[$date])) {
				$color = '#f8e3d3';

				if (isset($has_check_out[$date])) {
					$color = '#dff0d8';
				}
			}

			$list_color[$date] = $color;
		}

		return $list_color;
	}

	public function update_leave($data)
	{
		if (!empty($data)) {
			if ($data['leave_type'] != 4) {

				// Calculate the number of days between the start and end dates
				$startDate = new DateTime($data['update_start_time']);
				$endDate = new DateTime($data['update_end_time']);
				$daysRequested = $endDate->diff($startDate)->days + 1; // Include the start date

				// Get the staff's remaining leave balance for the type of leave being updated
				$this->db->where('staffid', $data['update_staff_id']);
				$this->db->where('year', date('Y')); // Current year
				$this->db->where('type_of_leave', $data['leave_type']);
				$leaveBalance = $this->db->get('tbltimesheets_day_off')->row_array();

				// Check if the leave balance exists and is sufficient
				if (!$leaveBalance || $leaveBalance['remain'] < $daysRequested) {
					return false;
				}

				// Deduct the requested days from the remaining leave balance
				$newRemain = $leaveBalance['remain'] - $daysRequested;

				// Update the leave balance
				$this->db->where('id', $leaveBalance['id']);
				$this->db->update('tbltimesheets_day_off', ['remain' => $newRemain, 'days_off' => $leaveBalance['days_off'] + $daysRequested]);
			}

			if ($data['leave_type'] == 1) {
				$data['leave_type'] = 'sick_leave';
				$type_of_leave = 1;
			} elseif ($data['leave_type'] == 4) {
				$data['leave_type'] = 'private_work_without_pay';
				$type_of_leave = 4;
			} else {
				$data['leave_type'] = $data['leave_type'];
				$type_of_leave = 0;
			}
			// Update the leave record
			$this->db->where('id', $data['update_leave_id']);
			$this->db->update(db_prefix() . 'timesheets_requisition_leave', [
				'type_of_leave_text' => $data['leave_type'],
				'type_of_leave' => $type_of_leave,
			]);

			return true;
		} else {
			return false;
		}
	}

	public function get_emp_code()
	{
		$this->db->select('concat(firstname, " ", lastname) as Staff,staff_identifi');
		$this->db->from('tblstaff');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_emp_position()
	{
		$this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Staff, tblhr_job_position.position_name AS job_position, tblstaff.staffid');
		$this->db->from('tblstaff');
		$this->db->join('tblhr_job_position', 'tblhr_job_position.position_id = tblstaff.job_position', 'left');
		$this->db->where('tblstaff.active', 1); // Only active staff if needed
		$query = $this->db->get();

		return $query->result_array();
	}

	public function get_emp_date_of_joining()
	{
		$this->db->select('concat(firstname, " ", lastname) as Staff,joining_date');
		$this->db->from('tblstaff');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_emp_active_status()
	{
		$this->db->select('concat(firstname, " ", lastname) as Staff,active');
		$this->db->from('tblstaff');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function import_attendance_data($timesheet_entries)
	{
		$affectedRows = 0;

		// 1) Flatten nested arrays
		$all_entries = [];
		foreach ($timesheet_entries as $e) {
			if (is_array($e) && isset($e[0]) && is_array($e[0])) {
				$all_entries = array_merge($all_entries, $e);
			} else {
				$all_entries[] = $e;
			}
		}

		// 2) Loop and insert/update per exact date_work
		foreach ($all_entries as $row) {
			// — check for THIS exact day
			$exists = (bool) $this->db
				->where('staff_id',  $row['staff_id'])
				->where('date_work', $row['date_work'])
				->count_all_results(db_prefix() . 'timesheets_timesheet');

			if ($exists) {
				// update the existing record for this staff + this date
				$this->db
					->where('staff_id',  $row['staff_id'])
					->where('date_work', $row['date_work'])
					->update(db_prefix() . 'timesheets_timesheet', [
						'value'       => $row['value'],
						'type'        => $row['type'],
						'add_from'    => $row['add_from'],
						'relate_id'   => $row['relate_id']   ?? null,
						'relate_type' => $row['relate_type'] ?? null,
					]);
			} else {
				// insert a brand new row for THIS date
				$this->db->insert(db_prefix() . 'timesheets_timesheet', $row);
			}

			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
		}

		return $affectedRows > 0;
	}

	public function add_update_note()
	{
		if ($this->input->post()) {
			// 1) Grab only month & note
			$month = $this->input->post('month');
			$note  = $this->input->post('note');

			// 2) Prepare data array
			$data = [
				'month' => $month,
				'note'  => $note,
			];

			// 3) Check for existing entry by month
			$existing = $this->db
				->where('month', $month)
				->get('tbltimesheets_notes')
				->row();

			if ($existing) {
				// 4a) If found, do an update
				$this->db
					->where('month', $month)
					->update('tbltimesheets_notes', $data);
			} else {
				// 4b) Otherwise, insert new
				$this->db
					->insert('tbltimesheets_notes', $data);
			}

			// (optional) return success/failure
			return $this->db->affected_rows() > 0;
		}

		return false;
	}
	public function get_notes($month = null)
	{
		$this->db->from('tbltimesheets_notes');

		if ($month !== null) {
			$this->db->where('month', $month);
			$query = $this->db->get();
			$row   = $query->row_array();
			return $row ?: [];
		}

		return $this->db->get()->result_array();
	}
}
