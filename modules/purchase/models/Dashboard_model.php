<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a dashboard model.
 */
class Dashboard_model extends App_Model
{
	public function get_purchase_order_dashboard($data)
	{
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		$vendors = $data['vendors'];
		$projects = $data['projects'];
		$group_pur = $data['group_pur'];
		$kind = $data['kind'];
		$from_date = $data['from_date'];
		$to_date = $data['to_date'];

		$response = array();
		$sql = "SELECT 
	        combined_orders.aw_unw_order_status,
	        combined_orders.order_name,
	        combined_orders.vendor,
	        combined_orders.order_date,
	        combined_orders.completion_date,
	        combined_orders.budget,
	        combined_orders.total,
	        combined_orders.co_total,
	        combined_orders.total_rev_contract_value,
	        combined_orders.anticipate_variation,
	        combined_orders.cost_to_complete,
	        combined_orders.vendor_submitted_amount_without_tax,
	        combined_orders.project,
	        combined_orders.project_id,
	        combined_orders.rli_filter,
	        combined_orders.kind,
	        tblassets_group.group_name,
	        combined_orders.remarks,
	        combined_orders.id,
	        combined_orders.vendor_id,
	        combined_orders.group_pur,
	        combined_orders.source_table,
	        combined_orders.order_number,
	        combined_orders.subtotal
	        FROM (
	            -- FIRST BLOCK: tblpur_orders
	            SELECT DISTINCT 
	                po.id,
	                po.aw_unw_order_status,
	                po.pur_order_number AS order_number,
	                po.pur_order_name AS order_name,
	                po.rli_filter,
	                pv.company AS vendor,
	                pv.userid AS vendor_id,
	                po.order_date,
	                po.completion_date,
	                po.budget,
	                po.order_value,
	                po.total,
	                IFNULL(co_sum.co_total, 0) AS co_total,
	                (po.subtotal + IFNULL(co_sum.co_total, 0)) AS total_rev_contract_value,
	                po.anticipate_variation,
	                (IFNULL(po.anticipate_variation, 0) + (po.subtotal + IFNULL(co_sum.co_total, 0))) AS cost_to_complete,
	                COALESCE(inv_po_sum.vendor_submitted_amount_without_tax, 0) AS vendor_submitted_amount_without_tax,
	                po.group_pur,
	                po.kind,
	                po.remarks,
	                po.subtotal,
	                pr.name AS project,
	                pr.id AS project_id,
	                'pur_orders' AS source_table
	            FROM tblpur_orders po
	            LEFT JOIN tblpur_vendor pv ON pv.userid = po.vendor
	            LEFT JOIN (
		            SELECT
		                po_order_id,
		                co_value AS co_total
		            FROM tblco_orders
		            WHERE po_order_id IS NOT NULL
		            AND id IN (
		                SELECT MAX(id)
		                FROM tblco_orders
		                WHERE po_order_id IS NOT NULL
		                GROUP BY po_order_id
		            )
		        ) AS co_sum ON co_sum.po_order_id = po.id
	            LEFT JOIN tblprojects pr ON pr.id = po.project
	            LEFT JOIN (
	                SELECT pur_order, SUM(vendor_submitted_amount_without_tax) AS vendor_submitted_amount_without_tax 
	                FROM tblpur_invoices 
	                WHERE pur_order IS NOT NULL 
	                GROUP BY pur_order
	            ) AS inv_po_sum ON inv_po_sum.pur_order = po.id

	            UNION ALL

	            -- SECOND BLOCK: tblwo_orders
	            SELECT DISTINCT 
	                wo.id,
	                wo.aw_unw_order_status,
	                wo.wo_order_number AS order_number,
	                wo.wo_order_name AS order_name,
	                wo.rli_filter,
	                pv.company AS vendor,
	                pv.userid AS vendor_id,
	                wo.order_date,
	                wo.completion_date,
	                wo.budget,
	                wo.order_value,
	                wo.total,
	                IFNULL(co_sum.co_total, 0) AS co_total,
	                (wo.subtotal + IFNULL(co_sum.co_total, 0)) AS total_rev_contract_value,
	                wo.anticipate_variation,
	                (IFNULL(wo.anticipate_variation, 0) + (wo.subtotal + IFNULL(co_sum.co_total, 0))) AS cost_to_complete,
	                COALESCE(inv_wo_sum.vendor_submitted_amount_without_tax, 0) AS vendor_submitted_amount_without_tax,
	                wo.group_pur,
	                wo.kind,
	                wo.remarks,
	                wo.subtotal,
	                pr.name AS project,
	                pr.id AS project_id,
	                'wo_orders' AS source_table
	            FROM tblwo_orders wo
	            LEFT JOIN tblpur_vendor pv ON pv.userid = wo.vendor
	            LEFT JOIN (
		            SELECT
		                wo_order_id,
		                co_value AS co_total
		            FROM tblco_orders
		            WHERE wo_order_id IS NOT NULL
		            AND id IN (
		                SELECT MAX(id)
		                FROM tblco_orders
		                WHERE wo_order_id IS NOT NULL
		                GROUP BY wo_order_id
		            )
		        ) AS co_sum ON co_sum.wo_order_id = wo.id
	            LEFT JOIN tblprojects pr ON pr.id = wo.project
	            LEFT JOIN (
	                SELECT wo_order, SUM(vendor_submitted_amount_without_tax) AS vendor_submitted_amount_without_tax 
	                FROM tblpur_invoices 
	                WHERE wo_order IS NOT NULL 
	                GROUP BY wo_order
	            ) AS inv_wo_sum ON inv_wo_sum.wo_order = wo.id

	            UNION ALL

	            -- THIRD BLOCK: tblpur_order_tracker
	            SELECT DISTINCT 
	                t.id,
	                t.aw_unw_order_status,
	                t.pur_order_number AS order_number,
	                t.pur_order_name AS order_name,
	                t.rli_filter,
	                pv.company AS vendor,
	                pv.userid AS vendor_id,
	                t.order_date,
	                t.completion_date,
	                t.budget,
	                t.order_value,
	                t.total,
	                t.co_total,
	                (t.total + IFNULL(t.co_total, 0)) AS total_rev_contract_value,
	                t.anticipate_variation,
	                (IFNULL(t.anticipate_variation, 0) + (t.total + IFNULL(t.co_total, 0))) AS cost_to_complete,
	                COALESCE(inv_ot_sum.vendor_submitted_amount_without_tax, 0) AS vendor_submitted_amount_without_tax,
	                t.group_pur,
	                t.kind,
	                t.remarks,
	                t.subtotal,
	                pr.name AS project,
	                pr.id AS project_id,
	                'order_tracker' AS source_table
	            FROM tblpur_order_tracker t
	            LEFT JOIN tblpur_vendor pv ON pv.userid = t.vendor
	            LEFT JOIN tblprojects pr ON pr.id = t.project
	            LEFT JOIN (
	                SELECT order_tracker_id, SUM(vendor_submitted_amount_without_tax) AS vendor_submitted_amount_without_tax 
	                FROM tblpur_invoices 
	                WHERE order_tracker_id IS NOT NULL 
	                GROUP BY order_tracker_id
	            ) AS inv_ot_sum ON inv_ot_sum.order_tracker_id = t.id
	        ) AS combined_orders
	        LEFT JOIN tblassets_group ON tblassets_group.group_id = combined_orders.group_pur";

		$module_name = 'purchase_dashboard';
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
		if (!empty($vendors)) {
			$conditions[] = "combined_orders.vendor_id = '" . $vendors . "'";
		}
		if (!empty($projects)) {
			$conditions[] = "combined_orders.project_id = '" . $projects . "'";
		}
		if (!empty($group_pur)) {
			$conditions[] = "combined_orders.group_pur = '" . $group_pur . "'";
		}
		if (!empty($kind)) {
			$conditions[] = "combined_orders.kind = '" . $kind . "'";
		}
		if (!empty($from_date)) {
			$conditions[] = "combined_orders.order_date >= '" . date('Y-m-d', strtotime($from_date)) . "'";
		}
		if (!empty($to_date)) {
			$conditions[] = "combined_orders.order_date <= '" . date('Y-m-d', strtotime($to_date)) . "'";
		}

		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(" AND ", $conditions);
		}

		$query = $this->db->query($sql);
		$result = $query->result_array();

		$response['cost_to_complete'] = 0;
		$cost_to_complete = 0;
		if (!empty($result)) {
			$cost_to_complete = array_sum(array_column($result, 'cost_to_complete'));
		}
		$response['cost_to_complete'] = app_format_money($cost_to_complete, $base_currency);
		$response['rev_contract_value'] = 0;
		$rev_contract_value = 0;
		if (!empty($result)) {
			$rev_contract_value = array_sum(array_column($result, 'total_rev_contract_value'));
		}
		$response['rev_contract_value'] = app_format_money($rev_contract_value, $base_currency);

		$response['percentage_utilized'] = 0;
		if ($cost_to_complete > 0) {
			$response['percentage_utilized'] = round(($rev_contract_value / $cost_to_complete) * 100);
		}
		$response['cost_to_complete_ratio'] = $response['percentage_utilized'];
		$response['rev_contract_value_ratio'] = 100 - $response['cost_to_complete_ratio'];
		$response['budgeted_procurement_net_value'] = app_format_money(($cost_to_complete - $rev_contract_value), $base_currency);

		$response['budgeted_actual_category_labels'] = array();
		$response['budgeted_category_value'] = array();
		$response['actual_category_value'] = array();

		if (!empty($result)) {
			$grouped_filter = array_values(array_reduce($result, function ($carry, $item) {
				$key = trim($item['group_name']);
				$carry[$key]['group_name'] = $key;
				$carry[$key]['cost_to_complete'] = ($carry[$key]['cost_to_complete'] ?? 0) + (float)$item['cost_to_complete'];
				$carry[$key]['total_rev_contract_value'] = ($carry[$key]['total_rev_contract_value'] ?? 0) + (float)$item['total_rev_contract_value'];
				return $carry;
			}, []));

			if (!empty($grouped_filter)) {
				foreach ($grouped_filter as $key => $value) {
					$response['budgeted_actual_category_labels'][] = $value['group_name'];
					$response['budgeted_category_value'][] = $value['cost_to_complete'];
					$response['actual_category_value'][] = $value['total_rev_contract_value'];
				}
			}
		}

		$response['procurement_table_data'] = array();
		if (!empty($result)) {
			$monthlyData = array_reduce($result, function ($carry, $item) {
				$timestamp = strtotime($item['order_date']);
				if (!$timestamp || $timestamp <= 0) {
					return $carry;
				}
				$key = date('m-Y', $timestamp);
				$label = date('F-Y', $timestamp);
				$carry[$key]['month'] = $label;
				$carry[$key]['cost_to_complete'] = ($carry[$key]['cost_to_complete'] ?? 0) + (float)$item['cost_to_complete'];
				$carry[$key]['total_rev_contract_value'] = ($carry[$key]['total_rev_contract_value'] ?? 0) + (float)$item['total_rev_contract_value'];
				return $carry;
			}, []);

			if (!empty($monthlyData)) {
				uksort($monthlyData, function ($a, $b) {
					$dateA = DateTime::createFromFormat('m-Y', $a);
					$dateB = DateTime::createFromFormat('m-Y', $b);
					return $dateA <=> $dateB;
				});
				$monthlyData = array_values($monthlyData);
			}

			$response['procurement_table_data'] = '
				<div class="table-responsive s_table">
				  <table class="table items table-bordered">
				    <thead>
				      <tr>
				        <th align="left">Month</th>
				        <th align="right">Budgeted</th>
				        <th align="right">Actual</th>
				      </tr>
				    </thead>
				    <tbody>';
			if (!empty($monthlyData)) {
				foreach ($monthlyData as $row) {
					$response['procurement_table_data'] .= '
			      <tr>
			        <td align="left">' . htmlspecialchars($row['month']) . '</td>
			        <td align="right">' . app_format_money($row['cost_to_complete'], $base_currency) . '</td>
			        <td align="right">' . app_format_money($row['total_rev_contract_value'], $base_currency) . '</td>
			      </tr>';
				}
			} else {
				$response['procurement_table_data'] .= '
			      <tr>
			        <td colspan="3" align="center">No data available</td>
			      </tr>';
			}
			$response['procurement_table_data'] .= '
			    </tbody>
			  </table>
			</div>';
		}

		$response['on_time_deliveries_percentage'] = 0;
		$response['delivery_delay_po'] = array();
		$response['delivery_delay_days'] = array();
		$response['delivery_performance_labels'] = array();
		$response['delivery_performance_values'] = array();
		$response['delivery_table_data'] = array();
		$response['average_delay'] = 0;

		$this->db->select(
			db_prefix() . 'goods_receipt.pr_order_id as po_id, ' .
				db_prefix() . 'pur_orders.pur_order_number as pur_order_number, ' .
				'CONCAT(' . db_prefix() . 'items.commodity_code, "_", ' . db_prefix() . 'items.description) as commodity_name, ' .
				db_prefix() . 'goods_receipt_detail.description as description, ' .
				db_prefix() . 'goods_receipt_detail.est_delivery_date as est_delivery_date, ' .
				db_prefix() . 'goods_receipt_detail.delivery_date as delivery_date'
		);
		$this->db->from(db_prefix() . 'goods_receipt_detail');
		$this->db->join(db_prefix() . 'goods_receipt', db_prefix() . 'goods_receipt.id = ' . db_prefix() . 'goods_receipt_detail.goods_receipt_id', 'left');
		$this->db->join(db_prefix() . 'pur_orders', db_prefix() . 'pur_orders.id = ' . db_prefix() . 'goods_receipt.pr_order_id', 'left');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'goods_receipt_detail.commodity_code', 'left');
		$this->db->where(db_prefix() . 'goods_receipt_detail.est_delivery_date IS NOT NULL');
		$this->db->where(db_prefix() . 'goods_receipt_detail.delivery_date IS NOT NULL');

		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$this->db->group_by(db_prefix() . 'goods_receipt_detail.id');
		$goods_receipt_detail = $this->db->get()->result_array();

		if (!empty($goods_receipt_detail)) {
			$po_ids = array_column($goods_receipt_detail, 'po_id');

			$this->db->select(
				db_prefix() . 'pur_orders.id as po_id, ' .
					db_prefix() . 'pur_orders.pur_order_number as pur_order_number, ' .
					'CONCAT(' . db_prefix() . 'items.commodity_code, "_", ' . db_prefix() . 'items.description) as commodity_name, ' .
					db_prefix() . 'pur_order_detail.description as description, ' .
					db_prefix() . 'pur_order_detail.est_delivery_date as est_delivery_date, ' .
					db_prefix() . 'pur_order_detail.delivery_date as delivery_date'
			);
			$this->db->from(db_prefix() . 'pur_order_detail');
			$this->db->join(db_prefix() . 'pur_orders', db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order', 'left');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'pur_order_detail.item_code', 'left');
			$this->db->where(db_prefix() . 'pur_order_detail.est_delivery_date IS NOT NULL');
			$this->db->where(db_prefix() . 'pur_order_detail.delivery_date IS NOT NULL');

			if (!empty($po_ids)) {
				$this->db->where_not_in(db_prefix() . 'pur_orders.id', $po_ids);
			}
			if (!empty($vendors)) {
				$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
			}
			if (!empty($projects)) {
				$this->db->where(db_prefix() . 'pur_orders.project', $projects);
			}
			if (!empty($group_pur)) {
				$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
			}
			if (!empty($kind)) {
				$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
			}
			if (!empty($from_date)) {
				$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
			}
			if (!empty($to_date)) {
				$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
			}

			$this->db->group_by(db_prefix() . 'pur_order_detail.id');
			$pur_order_detail = $this->db->get()->result_array();

			$delivery_schedule = array_merge($goods_receipt_detail, $pur_order_detail);

			$all_schedule_count = count($delivery_schedule);
			$est_delivery_count = count(array_filter($delivery_schedule, function ($item) {
				return strtotime($item['est_delivery_date']) >= strtotime($item['delivery_date']);
			}));

			if ($all_schedule_count > 0) {
				$response['on_time_deliveries_percentage'] = round(($est_delivery_count / $all_schedule_count) * 100);
			}

			$response['delivery_performance_labels'] = ['On-Time', 'Delayed'];
			$response['delivery_performance_values'] = [$response['on_time_deliveries_percentage'], round(100 - $response['on_time_deliveries_percentage'])];

			$delay_delivery_data = array_filter($delivery_schedule, function ($item) {
				return strtotime($item['est_delivery_date']) < strtotime($item['delivery_date']);
			});

			if (!empty($delay_delivery_data)) {
				$delay_delivery_data = array_values(array_filter(
					array_map(function ($item) {
						$days = (strtotime($item['delivery_date']) - strtotime($item['est_delivery_date'])) / (60 * 60 * 24);
						if ($days > 0) {
							$item['delay_days'] = (int)$days;
							return $item;
						}
						return null;
					}, $delay_delivery_data),
					fn($item) => !is_null($item)
				));

				$response['delivery_table_data'] = '
		        <div class="table-responsive s_table">
		          <table class="table items table-bordered">
		            <thead>
		              <tr>
		                <th align="left" width="25%">PO Name</th>
		                <th align="left" width="30%">' . _l('description') . '</th>
		                <th align="right" width="15%">' . _l('est_delivery_date') . '</th>
		                <th align="right" width="15%">' . _l('delivery_date') . '</th>
		                <th align="right" width="15%">Delay (Days)</th>
		              </tr>
		            </thead>
		            <tbody>';

				if (!empty($delay_delivery_data)) {
					foreach ($delay_delivery_data as $drow) {
						$response['delivery_table_data'] .= '
		                  <tr>
		                  	<td align="left">' . html_entity_decode($drow['pur_order_number']) . '</td>
		                    <td align="left">' . html_entity_decode($drow['commodity_name']) . '</td>
		                    <td align="right">' . date('d-m-Y', strtotime($drow['est_delivery_date'])) . '</td>
		                    <td align="right">' . date('d-m-Y', strtotime($drow['delivery_date'])) . '</td>
		                    <td align="right">' . html_entity_decode($drow['delay_days']) . '</td>
		                  </tr>';
					}
				} else {
					$response['delivery_table_data'] .= '
		              <tr>
		                <td colspan="5" align="center">No data available</td>
		              </tr>';
				}

				$response['delivery_table_data'] .= '
		            </tbody>
		          </table>
		        </div>';

				$delay_delivery_data = array_values(array_reduce($delay_delivery_data, function ($carry, $item) {
					$key = $item['po_id'];
					if (!isset($carry[$key])) {
						$carry[$key] = [
							'po_id' => $item['po_id'],
							'pur_order_number' => $item['pur_order_number'],
							'delay_days' => $item['delay_days']
						];
					} else {
						$carry[$key]['delay_days'] += $item['delay_days'];
					}
					return $carry;
				}, []));

				$response['delivery_delay_po'] = array_column($delay_delivery_data, 'pur_order_number');
				$response['delivery_delay_days'] = array_column($delay_delivery_data, 'delay_days');

				$total_delay_days = array_sum($response['delivery_delay_days']);
				$response['average_delay'] = count($response['delivery_delay_days']) > 0 ? round($total_delay_days / count($response['delivery_delay_days']), 2) : 0;
			}
		}


		$response['total_procurement_items'] = 0;
		$response['late_deliveries'] = 0;
		$response['shop_drawing_approved'] = 0;
		$this->db->select('count(' . db_prefix() . 'pur_order_detail.id) as total_procurement_items');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		// Apply conditions before running the query
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$get_po_total_items = $this->db->get()->result_array();

		if (!empty($get_po_total_items)) {
			$response['total_procurement_items'] = $get_po_total_items[0]['total_procurement_items'];
		}



		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as late_deliveries');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		// Late deliveries condition (delivery_date > est_delivery_date OR not delivered but past estimated date)
		$this->db->group_start();
		$this->db->where(db_prefix() . 'pur_order_detail.est_delivery_date >', 0);
		$this->db->where(db_prefix() . 'pur_order_detail.delivery_date >', db_prefix() . 'pur_order_detail.est_delivery_date', false);
		$this->db->or_where(db_prefix() . 'pur_order_detail.est_delivery_date <', 'NOW()', false);
		$this->db->group_end();

		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$get_po_late_deliveries = $this->db->get()->row_array(); // Using row_array() since COUNT returns a single row

		if (!empty($get_po_late_deliveries)) {
			$response['late_deliveries'] = (int) $get_po_late_deliveries['late_deliveries'];
		}


		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as shop_approval');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');


		$this->db->group_start();
		$this->db->where(db_prefix() . 'pur_order_detail.shop_approval >', 0);
		$this->db->group_end();

		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$get_po_shop_approval = $this->db->get()->row_array();

		if (!empty($get_po_shop_approval)) {
			$response['shop_drawing_approved'] = (int) $get_po_shop_approval['shop_approval'];
		}

		$response['shop_drawing_pending_approval'] = 0;

		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as shop_submission_count');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		// Conditions:
		// 1. shop_submission is not empty (either a valid date or > 0, depending on your DB structure)
		// 2. shop_approval is empty (NULL or 0)
		$this->db->where(db_prefix() . 'pur_order_detail.shop_submission IS NOT NULL', null, false); // Check for non-NULL
		$this->db->where(db_prefix() . 'pur_order_detail.shop_submission >', 0); // Optional: if stored as timestamp
		$this->db->group_start();
		$this->db->where(db_prefix() . 'pur_order_detail.shop_approval IS NULL', null, false); // NULL check
		$this->db->or_where(db_prefix() . 'pur_order_detail.shop_approval', 0); // If stored as 0 when empty
		$this->db->group_end();
		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$get_po_shop_approval = $this->db->get()->row_array();

		if (!empty($get_po_shop_approval)) {
			$response['shop_drawing_pending_approval'] = (int) $get_po_shop_approval['shop_submission_count'];
		}


		$response['production_status_approved'] = 0;

		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as production_status');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		$this->db->where(db_prefix() . 'pur_order_detail.production_status', 2);

		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$result = $this->db->get()->row_array();
		$response['production_status_approved'] = isset($result['production_status']) ? (int)$result['production_status'] : 0;


		$response['rfq_sent'] = 0;

		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as rfq_sent');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		$this->db->where(db_prefix() . 'pur_order_detail.tracker_status', 3);

		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$result = $this->db->get()->row_array();
		$response['rfq_sent'] = isset($result['rfq_sent']) ? (int)$result['rfq_sent'] : 0;

		$response['poi_sent'] = 0;

		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as poi_sent');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		$this->db->where(db_prefix() . 'pur_order_detail.tracker_status', 5);

		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$result = $this->db->get()->row_array();
		$response['poi_sent'] = isset($result['poi_sent']) ? (int)$result['poi_sent'] : 0;


		$response['pir_sent'] = 0;

		$this->db->select('COUNT(' . db_prefix() . 'pur_order_detail.id) as pir_sent');
		$this->db->from(db_prefix() . 'pur_orders');
		$this->db->join(db_prefix() . 'pur_order_detail', db_prefix() . 'pur_order_detail.pur_order = ' . db_prefix() . 'pur_orders.id', 'left');

		$this->db->where(db_prefix() . 'pur_order_detail.tracker_status', 6);

		// Apply filters
		if (!empty($vendors)) {
			$this->db->where(db_prefix() . 'pur_orders.vendor', $vendors);
		}
		if (!empty($projects)) {
			$this->db->where(db_prefix() . 'pur_orders.project', $projects);
		}
		if (!empty($group_pur)) {
			$this->db->where(db_prefix() . 'pur_orders.group_pur', $group_pur);
		}
		if (!empty($kind)) {
			$this->db->where(db_prefix() . 'pur_orders.kind', $kind);
		}
		if (!empty($from_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date >=', date('Y-m-d', strtotime($from_date)));
		}
		if (!empty($to_date)) {
			$this->db->where(db_prefix() . 'pur_orders.order_date <=', date('Y-m-d', strtotime($to_date)));
		}

		$result = $this->db->get()->row_array();
		$response['pir_sent'] = isset($result['pir_sent']) ? (int)$result['pir_sent'] : 0;


		return $response;
	}

	public function get_billing_dashboard($data)
	{
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		$vendors = isset($data['vendors']) ? $data['vendors'] : '';
		$projects = isset($data['projects']) ? $data['projects'] : get_default_project();
		$order_tagged_detail = isset($data['order_tagged_detail']) ? $data['order_tagged_detail'] : array();

		$response = array();
		$sql = "SELECT 
			pi.id, 
			pi.vendor_submitted_amount_without_tax, 
			pi.invoice_date,
			pi.payment_status,
			ril.id AS ril_invoice_id,
			ril.status AS ril_status,
			CASE 
		        WHEN ril.id IS NOT NULL THEN DATE(ril.date)
		        ELSE NULL
		    END AS rli_invoice_date,
		    SUM(
                CASE 
                    WHEN ril.id IS NOT NULL THEN (itm.qty * itm.rate)
                    ELSE 0
                END
            ) AS ril_certified_amount,
            SUM(
                CASE 
                    WHEN ril.total > 0 THEN (ip.amount * pi.vendor_submitted_amount_without_tax) / ril.total
                    ELSE 0
                END
            ) AS ril_payment,
            CASE 
		        WHEN ip.payment_date IS NOT NULL THEN DATE(ip.payment_date)
		        ELSE NULL
		    END AS rli_payment_date
	    FROM tblpur_invoices pi
	    LEFT JOIN tblitemable itm ON itm.vbt_id = pi.id AND itm.rel_type = 'invoice'
        LEFT JOIN tblinvoices ril ON ril.id = itm.rel_id
        LEFT JOIN (
            SELECT invoiceid, SUM(amount) AS amount, MAX(date) as payment_date
            FROM tblinvoicepaymentrecords
            GROUP BY invoiceid
        ) ip ON ip.invoiceid = ril.id
	    ";

		$module_name = 'billing_dashboard';
		$vendor_filter_name = 'vendor';
		$project_filter_name = 'project';
		$order_tagged_detail_filter_name = 'order_tagged_detail';
		$conditions = [];
		update_module_filter($module_name, $vendor_filter_name, NULL);
		update_module_filter($module_name, $project_filter_name, NULL);
		update_module_filter($module_name, $order_tagged_detail_filter_name, NULL);
		if (!empty($vendors)) {
			update_module_filter($module_name, $vendor_filter_name, $vendors);
		}
		if (!empty($projects)) {
			update_module_filter($module_name, $project_filter_name, $projects);
		}
		if (!empty($order_tagged_detail)) {
			update_module_filter($module_name, $order_tagged_detail_filter_name, implode(',', $order_tagged_detail));
		}
		if (!empty($vendors)) {
			$conditions[] = "pi.vendor = '" . $vendors . "'";
		}
		if (!empty($projects)) {
			$conditions[] = "pi.project_id = '" . $projects . "'";
		}
		if (!empty($order_tagged_detail)) {
		    $or_conditions = [];
		    foreach ($order_tagged_detail as $t) {
		        if (!empty($t)) {
		            if (strpos($t, 'po_') === 0) {
		                $id = str_replace('po_', '', $t);
		                $or_conditions[] = "pi.pur_order = '$id'";
		            } elseif (strpos($t, 'wo_') === 0) {
		                $id = str_replace('wo_', '', $t);
		                $or_conditions[] = "pi.wo_order = '$id'";
		            } elseif (strpos($t, 'ot_') === 0) {
		                $id = str_replace('ot_', '', $t);
		                $or_conditions[] = "pi.order_tracker_id = '$id'";
		            }
		        }
		    }
		    if (!empty($or_conditions)) {
		        $conditions[] = '(' . implode(' OR ', $or_conditions) . ')';
		    }
		}
		$custom_date_select = $this->purchase_model->get_where_report_period('pi.invoice_date');
		if (!empty($custom_date_select)) {
		    $custom_date_select = preg_replace('/^\s*AND\s*/i', '', $custom_date_select);
		    $conditions[] = $custom_date_select;
		}
		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(" AND ", $conditions);
		}
		$sql .= " GROUP BY pi.id";
		$sql .= " ORDER BY pi.invoice_date ASC";
		$query = $this->db->query($sql);
		$result = $query->result_array();

		$default_project = get_default_project();
		$pc_conditions = [];
		$pc_sql = "
		SELECT 
		    pc.id AS id
		FROM 
		    tblpayment_certificate pc
		LEFT JOIN tblpur_orders po 
		    ON pc.po_id IS NOT NULL 
		    AND po.id = pc.po_id
		LEFT JOIN tblwo_orders wo 
		    ON pc.wo_id IS NOT NULL 
		    AND wo.id = pc.wo_id
		LEFT JOIN tblpur_order_tracker ot 
		    ON pc.ot_id IS NOT NULL 
		    AND ot.id = pc.ot_id
		WHERE 
		    pc.approve_status IN (2)
		    AND pc.pur_invoice_id IS NULL
		    AND (
		        (pc.po_id IS NOT NULL AND po.project IN ($default_project)) 
		        OR (pc.wo_id IS NOT NULL AND wo.project IN ($default_project)) 
		        OR (pc.ot_id IS NOT NULL AND ot.project IN ($default_project))
		    )
		";
		if (!empty($vendors)) {
		    $pc_conditions[] = "pc.vendor = " . $this->db->escape($vendors);
		}
		$pc_custom_date_select = $this->purchase_model->get_where_report_period('pc.order_date');
		if (!empty($pc_custom_date_select)) {
		    $pc_custom_date_select = preg_replace('/^\s*AND\s*/i', '', $pc_custom_date_select);
		    $pc_conditions[] = $pc_custom_date_select;
		}
		if (!empty($pc_conditions)) {
		    $pc_sql .= " AND " . implode(" AND ", $pc_conditions);
		}
		$pc_sql .= " GROUP BY pc.id";
		$pc_result = $this->db->query($pc_sql)->result_array();

		$response['total_bil_count'] = 0;
		$total_bil_amount = 0;
		$response['total_ril_count'] = 0;
		$total_ril_amount = 0;
		$response['total_paid_count'] = 0;
		$total_paid_amount = 0;
		$response['total_unpaid_count'] = 0;
		$total_unpaid_amount = 0;
		$response['bill_pending_by_bil'] = count($pc_result);
		$response['bill_pending_by_ril'] = 0;
		if(!empty($result)) {
			$response['total_bil_count'] = count($result);
			$total_bil_amount = array_reduce($result, function ($carry, $item) {
                return $carry + (float)$item['vendor_submitted_amount_without_tax'];
            }, 0);
            $response['total_ril_count'] = count(array_filter($result, fn($item) =>
			    isset($item['ril_invoice_id']) && $item['ril_invoice_id'] !== NULL
			));
            $total_ril_amount = array_reduce($result, function ($carry, $item) {
			    if (!empty($item['ril_invoice_id'])) {
			        $carry += (float) $item['vendor_submitted_amount_without_tax'];
			    }
			    return $carry;
			}, 0);
            $response['total_paid_count'] = count(array_filter($result, fn($item) =>
			    (float)$item['ril_payment'] > 0
			));
			$total_paid_amount = array_reduce($result, function ($carry, $item) {
                return $carry + (float)$item['ril_payment'];
            }, 0);
			$response['total_unpaid_count'] = count(
			    array_filter($result, fn($item) =>
			        isset($item['ril_invoice_id']) &&
			        $item['ril_invoice_id'] !== NULL &&
			        !in_array($item['ril_status'], [2, 3])
			    )
			);
			$total_unpaid_amount = array_reduce($result, function ($carry, $item) {
			    if (
			        isset($item['ril_invoice_id']) &&
			        $item['ril_invoice_id'] !== NULL &&
			        !in_array($item['ril_status'], [2, 3])
			    ) {
			        $carry += (float) $item['vendor_submitted_amount_without_tax'];
			    }
			    return $carry;
			}, 0);
			$response['bill_pending_by_ril'] = count(
			    array_filter($result, fn($item) => empty($item['ril_invoice_id']))
			);
		}
		$response['total_bil_amount'] = app_format_money($total_bil_amount, $base_currency);
		$response['total_ril_amount'] = app_format_money($total_ril_amount, $base_currency);
		$response['total_paid_amount'] = app_format_money($total_paid_amount, $base_currency);
		$response['total_unpaid_amount'] = app_format_money($total_unpaid_amount, $base_currency);

		$response['line_bil_order_date'] = $response['line_bil_order_total'] = array();
		$response['line_ril_order_date'] = $response['line_ril_order_total'] = array();
		$response['line_paid_order_date'] = $response['line_paid_order_total'] = array();
		$response['line_unpaid_order_date'] = $response['line_unpaid_order_total'] = array();

		$line_bil_order_total = array();
        foreach ($result as $key => $value) {
            if (!empty($value['invoice_date'])) {
                $timestamp = strtotime($value['invoice_date']);
                if ($timestamp !== false && $timestamp > 0) {
                    $month = date('Y-m', $timestamp);
                } elseif ($timestamp === false || $timestamp <= 0) {
                    $month = date('Y') . '-01';
                }
            } else {
                $month = date('Y') . '-01';
            }
            if (!isset($line_bil_order_total[$month])) {
                $line_bil_order_total[$month] = 0;
            }
            $line_bil_order_total[$month] += $value['vendor_submitted_amount_without_tax'];
        }
        if (!empty($line_bil_order_total)) {
            ksort($line_bil_order_total);
            $cumulative = 0;
            foreach ($line_bil_order_total as $month => $value) {
                $cumulative += $value;
                $line_bil_order_total[$month] = $cumulative;
            }
            $response['line_bil_order_date'] = array_map(function ($month) {
                return date('M-y', strtotime($month . '-01'));
            }, array_keys($line_bil_order_total));
            $response['line_bil_order_total'] = array_values($line_bil_order_total);
        }

        $rli_invoice_result = array_values(array_filter($result, function ($item) {
		    return !empty($item['ril_invoice_id']);
		}));
		$line_ril_order_total = array();
		if(!empty($rli_invoice_result)) {
			foreach ($rli_invoice_result as $key => $value) {
	            if (!empty($value['rli_invoice_date'])) {
	                $timestamp = strtotime($value['rli_invoice_date']);
	                if ($timestamp !== false && $timestamp > 0) {
	                    $month = date('Y-m', $timestamp);
	                } elseif ($timestamp === false || $timestamp <= 0) {
	                    $month = date('Y') . '-01';
	                }
	            } else {
	                $month = date('Y') . '-01';
	            }
	            if (!isset($line_ril_order_total[$month])) {
	                $line_ril_order_total[$month] = 0;
	            }
	            $line_ril_order_total[$month] += $value['ril_certified_amount'];
	        }
		}
        if (!empty($line_ril_order_total)) {
            ksort($line_ril_order_total);
            $cumulative = 0;
            foreach ($line_ril_order_total as $month => $value) {
                $cumulative += $value;
                $line_ril_order_total[$month] = $cumulative;
            }
            $response['line_ril_order_date'] = array_map(function ($month) {
                return date('M-y', strtotime($month . '-01'));
            }, array_keys($line_ril_order_total));
            $response['line_ril_order_total'] = array_values($line_ril_order_total);
        }

        $rli_paid_result = array_values(array_filter($result, fn($item) =>
		    !empty($item['rli_payment_date'])
		));
		$line_paid_order_total = array();
		if(!empty($rli_paid_result)) {
			foreach ($rli_paid_result as $key => $value) {
	            if (!empty($value['rli_payment_date'])) {
	                $timestamp = strtotime($value['rli_payment_date']);
	                if ($timestamp !== false && $timestamp > 0) {
	                    $month = date('Y-m', $timestamp);
	                } elseif ($timestamp === false || $timestamp <= 0) {
	                    $month = date('Y') . '-01';
	                }
	            } else {
	                $month = date('Y') . '-01';
	            }
	            if (!isset($line_paid_order_total[$month])) {
	                $line_paid_order_total[$month] = 0;
	            }
	            $line_paid_order_total[$month] += $value['ril_payment'];
	        }
		}
        if (!empty($line_paid_order_total)) {
            ksort($line_paid_order_total);
            $cumulative = 0;
            foreach ($line_paid_order_total as $month => $value) {
                $cumulative += $value;
                $line_paid_order_total[$month] = $cumulative;
            }
            $response['line_paid_order_date'] = array_map(function ($month) {
                return date('M-y', strtotime($month . '-01'));
            }, array_keys($line_paid_order_total));
            $response['line_paid_order_total'] = array_values($line_paid_order_total);
        }

        $rli_unpaid_result = array_values(
		    array_filter($result, fn($item) =>
		        isset($item['ril_invoice_id']) &&
		        $item['ril_invoice_id'] !== NULL &&
		        !in_array($item['ril_status'], [2, 3])
		    )
		);
        $line_unpaid_order_total = array();
		if(!empty($rli_unpaid_result)) {
			foreach ($rli_unpaid_result as $key => $value) {
	            if (!empty($value['invoice_date'])) {
	                $timestamp = strtotime($value['invoice_date']);
	                if ($timestamp !== false && $timestamp > 0) {
	                    $month = date('Y-m', $timestamp);
	                } elseif ($timestamp === false || $timestamp <= 0) {
	                    $month = date('Y') . '-01';
	                }
	            } else {
	                $month = date('Y') . '-01';
	            }
	            if (!isset($line_unpaid_order_total[$month])) {
	                $line_unpaid_order_total[$month] = 0;
	            }
	            $line_unpaid_order_total[$month] += $value['vendor_submitted_amount_without_tax'];
	        }
		}
		if (!empty($line_unpaid_order_total)) {
            ksort($line_unpaid_order_total);
            $cumulative = 0;
            foreach ($line_unpaid_order_total as $month => $value) {
                $cumulative += $value;
                $line_unpaid_order_total[$month] = $cumulative;
            }
            $response['line_unpaid_order_date'] = array_map(function ($month) {
                return date('M-y', strtotime($month . '-01'));
            }, array_keys($line_unpaid_order_total));
            $response['line_unpaid_order_total'] = array_values($line_unpaid_order_total);
        }

		return $response;
	}
}
