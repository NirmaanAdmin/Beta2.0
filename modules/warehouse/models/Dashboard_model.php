<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a dashboard model.
 */
class Dashboard_model extends App_Model
{
	public function get_inventory_dashboard($data)
	{
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		$vendors = $data['vendors'];
		$projects = $data['projects'];
		$group_pur = $data['group_pur'];
		$kind = $data['kind'];
		$from_date = $data['from_date'];
		$to_date = $data['to_date'];



		$response = [];


		$module_name = 'inventory_dashboard';
		$vendor_filter_name = 'vendor';
		$project_filter_name = 'project';
		$group_pur_filter_name = 'group_pur';
		$kind_filter_name = 'kind';
		$from_date_filter_name = 'from_date';
		$to_date_filter_name = 'to_date';


		$conditions = [];
		update_module_filter($module_name, $vendor_filter_name, NULL);
		update_module_filter($module_name, $project_filter_name, NULL);
		update_module_filter($module_name, $group_pur_filter_name, NULL);
		update_module_filter($module_name, $kind_filter_name, NULL);
		update_module_filter($module_name, $from_date_filter_name, NULL);
		update_module_filter($module_name, $to_date_filter_name, NULL);
		if (!empty($vendors)) {
			update_module_filter($module_name, $vendor_filter_name, $vendors);
		}
		if (!empty($projects)) {
			update_module_filter($module_name, $project_filter_name, $projects);
		}
		if (!empty($group_pur)) {
			update_module_filter($module_name, $group_pur_filter_name, $group_pur);
		}
		if (!empty($kind)) {
			update_module_filter($module_name, $kind_filter_name, $kind);
		}
		if (!empty($from_date)) {
			update_module_filter($module_name, $from_date_filter_name, date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			update_module_filter($module_name, $to_date_filter_name, date('Y-m-d', strtotime($to_date)));
		}
		$response['total_inventory_count'] = 0;

		$this->db->select('COUNT(im.id) as total_inventory_count');
		$this->db->from(db_prefix() . 'items i');
		$this->db->join(db_prefix() . 'inventory_manage im', 'im.commodity_id = i.id', 'inner');
		$this->db->where('im.inventory_number >', 0);


		$result = $this->db->get()->row_array();
		$response['total_inventory_count'] = isset($result['total_inventory_count']) ? (int)$result['total_inventory_count'] : 0;

		$response['fully_po_material_receipt'] = 0;


		$result = $this->get_pr_order_fully_delivered();
		$total_count_fully_po_delivered = !empty($result) ? $result : 0;


		$response['fully_po_material_receipt'] = $total_count_fully_po_delivered;

		$response['missing_security_signature'] = 0;

		$this->db->select('COUNT(i.id) as total_missing_security_count');
		$this->db->from(db_prefix() . 'goods_receipt_documentation i');
		$this->db->where('i.checklist_id', 2);
		$this->db->where('i.required >', 0);
		$this->db->where('i.attachments', 0); // 🔹 fix: looking for missing attachments

		$get_missing_security = $this->db->get()->row_array();

		$response['missing_security_signature'] = isset($get_missing_security['total_missing_security_count'])
			? (int)$get_missing_security['total_missing_security_count']
			: 0;



		$response['missing_production_certificate'] = 0;

		$this->db->select('COUNT(i.id) as total_production_certificate_count');
		$this->db->from(db_prefix() . 'goods_receipt_documentation i');
		$this->db->where('i.checklist_id  ', 4);
		$this->db->where('i.required   >', 0);
		$this->db->where('i.attachments', 0);

		$get_production_certificate_count = $this->db->get()->row_array();

		$response['missing_production_certificate'] = isset($get_production_certificate_count['total_production_certificate_count']) ? (int)$get_production_certificate_count['total_production_certificate_count'] : 0;



		$response['missing_transport_document'] = 0;

		$this->db->select('COUNT(i.id) as transport_document_count');
		$this->db->from(db_prefix() . 'goods_receipt_documentation i');
		$this->db->where('i.checklist_id  ', 3);
		$this->db->where('i.required   >', 0);
		$this->db->where('i.attachments', 0);

		$get_transport_document_count = $this->db->get()->row_array();

		$response['missing_transport_document'] = isset($get_transport_document_count['transport_document_count']) ? (int)$get_transport_document_count['transport_document_count'] : 0;

		$response['fully_documented'] = 0;
		$response['incompleted'] = 0;

		// Get total distinct goods_receipt_id from documentation table
		$this->db->select('COUNT(*) as total_received');
		$this->db->from(db_prefix() . 'goods_receipt_documentation');
		$total_received_row = $this->db->get()->row_array();
		$total_received = isset($total_received_row['total_received']) ? (int)$total_received_row['total_received'] : 0;

		if ($total_received > 0) {

			$this->db->select('COUNT(*) as attached_rows');
			$this->db->from(db_prefix() . 'goods_receipt_documentation');
			$this->db->where("(`attachments` = 1 OR (`required` = 0 AND `attachments` = 0))", null, false);
			$attached_rows_result = $this->db->get()->row_array();

			$attached_rows = isset($attached_rows_result['attached_rows']) ? (int)$attached_rows_result['attached_rows'] : 0;

			$response['fully_documented'] = ($total_received > 0 && $total_received === $attached_rows)
				? 100
				: round(($attached_rows / $total_received) * 100);


			$subquery_incomplete = '(SELECT goods_receipt_id
				FROM ' . db_prefix() . 'goods_receipt_documentation
				WHERE required = 1 AND attachments = 0
			) AS incomplete_gr';

			$this->db->select('COUNT(*) AS incomplete_count');
			$this->db->from($subquery_incomplete, null, false);
			$incomplete_result = $this->db->get()->row_array();
			$incomplete_count = isset($incomplete_result['incomplete_count']) ? (int)$incomplete_result['incomplete_count'] : 0;
			$response['incompleted'] = round(($incomplete_count / $total_received) * 100);
		}


		$response['getconsumption_months'] = $response['getconsumption_data'] = [];

		$this->db->select("DATE_FORMAT(gd.date_add, '%b') AS month_name, MONTH(gd.date_add) AS month_number, ROUND(SUM(gdd.quantities)) AS total_quantity");
		$this->db->from(db_prefix() . 'goods_delivery gd');
		$this->db->join(db_prefix() . 'goods_delivery_detail gdd', 'gdd.goods_delivery_id = gd.id', 'left');
		$this->db->group_by(['month_number', 'month_name']);
		$this->db->order_by('month_number', 'ASC');


		$get_getconsumption_data = $this->db->get()->result_array();

		$response['getconsumption_months'] = array_column($get_getconsumption_data, 'month_name');
		$response['getconsumption_data'] = array_column($get_getconsumption_data, 'total_quantity');

		$response['total_materials_issued'] = 0;

		// Step 1: Get distinct pr_order_id counts and ids
		$sql = "SELECT COUNT(*) AS total_unique_count, GROUP_CONCAT(id) AS ids 
        FROM (
            SELECT MIN(id) AS id 
            FROM tblgoods_delivery 
            WHERE pr_order_id IS NOT NULL AND approval = 1
            GROUP BY pr_order_id
        ) AS unique_deliveries";

		$get_total_issued_unique_count = $this->db->query($sql)->row_array();

		// Step 2: Get count from goods_delivery_detail for those goods_delivery_id
		if (!empty($get_total_issued_unique_count['ids'])) {
			$final_records = "SELECT COUNT(*) AS final_unique_count 
                      FROM `tblgoods_delivery_detail` 
                      WHERE `goods_delivery_id` IN (" . $get_total_issued_unique_count['ids'] . ")";

			$get_total_issued_final_unique_count = $this->db->query($final_records)->row_array();

			$response['total_materials_issued'] = isset($get_total_issued_final_unique_count['final_unique_count'])
				? (int)$get_total_issued_final_unique_count['final_unique_count']
				: 0;
		}


		$response['total_material_return'] = 0;

		$sql_new = "SELECT COUNT(*) AS total_unique_count, GROUP_CONCAT(id) AS ids 
        FROM (
            SELECT MIN(id) AS id 
            FROM tblstock_reconciliation 
            WHERE pr_order_id IS NOT NULL 
            GROUP BY pr_order_id
        ) AS unique_deliveries";

		$get_total_issued_reconcilied_unique_count = $this->db->query($sql_new)->row_array();


		if (!empty($get_total_issued_reconcilied_unique_count['ids'])) {
			$final_records = "SELECT COUNT(*) AS final_unique_count 
                      FROM `tblstock_reconciliation_detail` 
                      WHERE `goods_delivery_id` IN (" . $get_total_issued_reconcilied_unique_count['ids'] . ")";

			$get_total_issued_final_reconcilied_unique_count = $this->db->query($final_records)->row_array();

			$response['total_material_return'] = isset($get_total_issued_final_reconcilied_unique_count['final_unique_count'])
				? (int)$get_total_issued_final_reconcilied_unique_count['final_unique_count']
				: 0;
		}


		$response['returnable_past_dates'] = 0;

		$this->db->select('returnable_date,returnable')
			->from('tblgoods_delivery_detail')
			->where('returnable', 1)
			->where('returnable_date IS NOT NULL')
			->where("returnable_date != ''");
		$get_all_records_with_returnable_date = $this->db->get()->result_array();

		$passed_count = 0;
		$current_date = strtotime(date('d-m-Y')); // today's date for comparison

		foreach ($get_all_records_with_returnable_date as $item) {
			if ($item['returnable'] == 1) {
				$dates = json_decode($item['returnable_date'], true);
				
				foreach ($dates as $date) {
					if (strtotime($date) < $current_date) {
						$passed_count++;
						break; // only count once per entry
					}
				}
			}
		}
		$response['returnable_past_dates'] = $passed_count;

		return $response;
	}



	public function  get_pr_order_fully_delivered()
	{
		$result = $result1 = [];
		$pur_orders = $this->db->query('select * from tblpur_orders where approve_status = 2 order by id desc')->result_array();
		if (!empty($pur_orders)) {
			foreach ($pur_orders as $key => $value) {
				$po_id = $value['id'];
				$get_pur_order = $this->goods_delivery_get_pur_order($po_id);
				$pur_order_detail = $get_pur_order['goods_delivery_exist'] ? $get_pur_order['goods_delivery_exist'] : 0;
				if ($pur_order_detail > 0) {
					$result[] = $value;
				} else {
					$result1[] = $value;
				}
			}
		}
		$result1 = !empty($result1) ? array_values($result1) : [];

		$total_fully_po_delivered = !empty($result1) ? count($result1) : 0;

		return $total_fully_po_delivered;
	}


	public function goods_delivery_get_pur_order($pur_order)
	{
		$arr_pur_resquest = [];
		$subtotal = 0;
		$total_discount = 0;
		$total_payment = 0;
		$total_tax_money = 0;
		$additional_discount = 0;
		$pur_total_money = 0;
		$goods_delivery_exist = 0;
		$goods_delivery_row_template = '';
		// $goods_delivery_row_template = $this->warehouse_model->create_goods_delivery_row_template();

		$this->db->select('item_code as commodity_code, ' . db_prefix() . 'pur_order_detail.description, ' . db_prefix() . 'pur_order_detail.unit_id , unit_price as rate, quantity as quantities, ' . db_prefix() . 'pur_order_detail.tax as tax_id, ' . db_prefix() . 'pur_order_detail.total as total_money, ' . db_prefix() . 'pur_order_detail.total, ' . db_prefix() . 'pur_order_detail.discount_% as discount, ' . db_prefix() . 'pur_order_detail.discount_money, ' . db_prefix() . 'pur_order_detail.total_money as total_after_discount, ' . db_prefix() . 'items.guarantee, , ' . db_prefix() . 'pur_order_detail.area as area, ' . db_prefix() . 'pur_order_detail.tax_rate');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'pur_order_detail.item_code = ' . db_prefix() . 'items.id', 'left');
		$this->db->where(db_prefix() . 'pur_order_detail.pur_order = ' . $pur_order);
		$arr_results = $this->db->get(db_prefix() . 'pur_order_detail')->result_array();
		$this->db->where('id', $pur_order);
		$get_pur_order = $this->db->get(db_prefix() . 'pur_orders')->row();
		$index = 0;
		$status = false;
		$item_index = 0;

		if (count($arr_results) > 0) {
			$status = false;
			foreach ($arr_results as $key => $value) {
				$tax_rate = null;
				$tax_name = null;
				$tax_id = null;
				$tax_rate_value = 0;
				$pur_total_money += (float)$value['total_after_discount'];
				/*caculatoe guarantee*/
				$guarantee_period = '';
				if ($value) {
					if (($value['guarantee'] != '') && (($value['guarantee'] != null)))
						$guarantee_period = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $value['guarantee'] . ' months'));
				}
				/*caculator subtotal*/
				/*total discount*/
				/*total payment*/
				$total_goods_money = (float)$value['quantities'] * (float)$value['rate'];
				//get tax value
				if ($value['tax_id'] != null && $value['tax_id'] != '') {
					$tax_id = $value['tax_id'];
					$arr_tax = explode('|', $value['tax_id']);
					$arr_tax_rate = explode('|', $value['tax_rate']);
					foreach ($arr_tax as $key => $tax_id) {
						$get_tax_name = $this->get_tax_name($tax_id);
						if (isset($arr_tax_rate[$key])) {
							$get_tax_rate = $arr_tax_rate[$key];
						} else {
							$tax = $this->get_taxe_value($tax_id);
							$get_tax_rate = (float)$tax->taxrate;
						}
						$tax_rate_value += (float)$get_tax_rate;
						if (strlen($tax_rate) > 0) {
							$tax_rate .= '|' . $get_tax_rate;
						} else {
							$tax_rate .= $get_tax_rate;
						}
						if (strlen($tax_name) > 0) {
							$tax_name .= '|' . $get_tax_name;
						} else {
							$tax_name .= $get_tax_name;
						}
					}
				}

				$index++;
				$unit_name = wh_get_unit_name($value['unit_id']);
				$unit_id = $value['unit_id'];
				$vendor_id =  '';
				$taxname = '';
				$issued_date = null;
				$lot_number = null;
				$note = null;
				$commodity_name = wh_get_item_variatiom($value['commodity_code']);
				$description = $value['description'];
				$total_money = 0;
				$total_after_discount = 0;
				$quantities = (float)$value['quantities'];
				$unit_price = (float)$value['rate'];
				$commodity_code = $value['commodity_code'];
				$discount_money = $value['discount_money'];

				if ((float)$tax_rate_value != 0) {
					$tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
					$total_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
					$amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
					$discount_money = (float)$amount * (float)$value['discount'] / 100;
					$total_after_discount = (float)$unit_price * (float)$quantities + (float)$tax_money - (float)$discount_money;
				} else {
					$total_money = (float)$unit_price * (float)$quantities;
					$amount = (float)$unit_price * (float)$quantities;
					$discount_money = (float)$amount * (float)$value['discount'] / 100;
					$total_after_discount = (float)$unit_price * (float)$quantities - (float)$discount_money;
				}

				$sub_total = (float)$unit_price * (float)$quantities;

				if ((float)$quantities > 0) {
					$temporaty_quantity = $quantities;
					$inventory_warehouse_by_commodity = $this->get_inventory_warehouse_by_commodity($commodity_code);

					foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
						if ($temporaty_quantity > 0) {
							$available_quantity = (float)$inventory_warehouse['inventory_number'];
							$warehouse_id = $inventory_warehouse['warehouse_id'];
							$temporaty_available_quantity = $available_quantity;
							$list_temporaty_serial_numbers = $this->get_list_temporaty_serial_numbers($commodity_code, $inventory_warehouse['warehouse_id'], $quantities);
							foreach ($list_temporaty_serial_numbers as $value) {
								if ($temporaty_available_quantity > 0) {
									$temporaty_commodity_name = $commodity_name . ' SN: ' . $value['serial_number'];
									$quantities = 1;
									$name = 'newitems[' . $item_index . ']';
									$goods_delivery_exist++;
									// $goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $temporaty_commodity_name, $warehouse_id, $temporaty_available_quantity, 0, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, '', $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $issued_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', false, false, $value['serial_number'], 0, $description, '', $value['area']);
									$temporaty_quantity--;
									$temporaty_available_quantity--;
									$item_index++;
									$inventory_warehouse_by_commodity[$key]['inventory_number'] = $temporaty_available_quantity;
								}
							}
						}
					}

					if ($temporaty_quantity > 0) {
						$quantities = $temporaty_quantity;
						$available_quantity = 0;
						$name = 'newitems[' . $item_index . ']';
						foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
							if ((float)$inventory_warehouse['inventory_number'] > 0 && $temporaty_quantity > 0) {
								$available_quantity = (float)$inventory_warehouse['inventory_number'];
								$warehouse_id = $inventory_warehouse['warehouse_id'];
								if ($temporaty_quantity >= $inventory_warehouse['inventory_number']) {
									$temporaty_quantity = (float) $temporaty_quantity - (float) $inventory_warehouse['inventory_number'];
									$quantities = (float)$inventory_warehouse['inventory_number'];
								} else {
									$quantities = (float)$temporaty_quantity;
									$temporaty_quantity = 0;
								}
								$duplicates = array_filter($arr_results, function ($item) use ($commodity_code) {
									return $item['commodity_code'] == $commodity_code;
								});
								if (count($duplicates) > 1) {
									$non_break_description = str_replace("<br />", "", $description);
									$this->db->select(db_prefix() . 'goods_receipt_detail.quantities');
									$this->db->like(db_prefix() . 'goods_receipt_detail.description', $non_break_description);
									$this->db->where(db_prefix() . 'goods_receipt.approval', 1);
									$this->db->where('pr_order_id', $pur_order);
									$this->db->join(db_prefix() . 'goods_receipt', db_prefix() . 'goods_receipt.id = ' . db_prefix() . 'goods_receipt_detail.goods_receipt_id', 'left');
									$goods_receipt_description = $this->db->get(db_prefix() . 'goods_receipt_detail')->result_array();
									if (!empty($goods_receipt_description)) {
										$available_quantity = array_sum(array_column($goods_receipt_description, 'quantities'));
									}

									$this->db->select(db_prefix() . 'goods_delivery_detail.quantities');
									$this->db->like(db_prefix() . 'goods_delivery_detail.description', $non_break_description);
									$this->db->where(db_prefix() . 'goods_delivery.approval', 1);
									$this->db->where('pr_order_id', $pur_order);
									$this->db->join(db_prefix() . 'goods_delivery', db_prefix() . 'goods_delivery.id = ' . db_prefix() . 'goods_delivery_detail.goods_delivery_id', 'left');
									$goods_delivery_description = $this->db->get(db_prefix() . 'goods_delivery_detail')->result_array();
									if (!empty($goods_delivery_description)) {
										$total_quantity = 0;
										foreach ($goods_delivery_description as $qitem) {
											$total_quantity += $qitem['quantities'];
										}
										$available_quantity = $available_quantity - $total_quantity;
									}
								}
								$goods_delivery_exist++;
								// $goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $commodity_name, $warehouse_id, $available_quantity, 0, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, '', $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $issued_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', false, false, $value['serial_number'], 0, $description, '', $value['area']);
								$item_index++;
							}
						}
					}
				}
			}

			if ($get_pur_order) {
				if ((float)$get_pur_order->discount_percent > 0) {
					$additional_discount = (float)$get_pur_order->discount_percent * (float)$pur_total_money / 100;
				}
			}
		}

		$arr_pur_resquest['result'] = $goods_delivery_row_template;
		$arr_pur_resquest['additional_discount'] = $additional_discount;
		$arr_pur_resquest['goods_delivery_exist'] = $goods_delivery_exist;

		return $arr_pur_resquest;
	}


	/**
	 * get list temporaty serial numbers
	 * @param  [type] $commodity_id 
	 * @param  [type] $warehouse_id 
	 * @param  [type] $quantity     
	 * @return [type]               
	 */
	public function get_list_temporaty_serial_numbers($commodity_id, $warehouse_id, $quantity = '', $where = [])
	{
		$this->db->where('commodity_id', $commodity_id);
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('is_used', 'no');
		if (count($where) > 0) {
			$this->db->where('serial_number NOT IN ("' . implode('","', $where) . '") ');
		}
		$this->db->order_by('id', 'asc');
		if (is_numeric($quantity)) {
			$this->db->limit((int)$quantity);
		}
		$inventory_serial_numbers = $this->db->get(db_prefix() . 'wh_inventory_serial_numbers')->result_array();
		return $inventory_serial_numbers;
	}

	/**
	 * get inventory warehouse by commodity
	 * @param  boolean $commodity_id 
	 * @return [type]                
	 */
	public function get_inventory_warehouse_by_commodity($commodity_id = false)
	{
		$arr_inventory_number = [];
		$sql = 'SELECT ' . db_prefix() . 'warehouse.warehouse_name, ' . db_prefix() . 'warehouse.warehouse_id, ' . db_prefix() . 'inventory_manage.inventory_number FROM ' . db_prefix() . 'inventory_manage
		LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id
		where ' . db_prefix() . 'inventory_manage.commodity_id = ' . $commodity_id . ' order by ' . db_prefix() . 'inventory_manage.id asc';
		$inventory_number = $this->db->query($sql)->result_array();

		foreach ($inventory_number as $value) {
			if (isset($arr_inventory_number[$value['warehouse_id']])) {
				$arr_inventory_number[$value['warehouse_id']]['inventory_number'] += $value['inventory_number'];
			} else {
				$arr_inventory_number[$value['warehouse_id']] = $value;
			}
		}
		return $arr_inventory_number;
	}


	/**
	 * Gets the tax name.
	 *
	 * @param        $tax    The tax
	 *
	 * @return     string  The tax name.
	 */
	public function get_tax_name($tax)
	{
		$this->db->where('id', $tax);
		$tax_if = $this->db->get(db_prefix() . 'taxes')->row();
		if ($tax_if) {
			return $tax_if->name;
		}
		return '';
	}

	/**
	 * Gets the taxes.
	 *
	 * @return     <array>  The taxes.
	 */
	public function get_taxe_value($id)
	{
		return $this->db->query('select id, name as label, taxrate from ' . db_prefix() . 'taxes where id = ' . $id)->row();
	}
}
