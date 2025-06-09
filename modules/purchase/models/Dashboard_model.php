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
	                SELECT po_order_id, SUM(co_value) AS co_total 
	                FROM tblco_orders 
	                WHERE po_order_id IS NOT NULL 
	                GROUP BY po_order_id
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
	                SELECT wo_order_id, SUM(co_value) AS co_total 
	                FROM tblco_orders 
	                WHERE wo_order_id IS NOT NULL 
	                GROUP BY wo_order_id
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

	    $conditions = [];
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
			$from_date = date('Y-m-d', strtotime($from_date));
			$conditions[] = "combined_orders.order_date >= '" . $from_date . "'";
		}
		if (!empty($to_date)) {
			$to_date = date('Y-m-d', strtotime($to_date));
			$conditions[] = "combined_orders.order_date <= '" . $to_date . "'";
		}

	    if (!empty($conditions)) {
		    $sql .= " WHERE " . implode(" AND ", $conditions);
		}

	    $query = $this->db->query($sql);
	    $result = $query->result_array();

	    $response['cost_to_complete'] = 0;
	    $cost_to_complete = 0;
	    if(!empty($result)) {
	    	$cost_to_complete = array_sum(array_column($result, 'cost_to_complete'));
	    	$response['cost_to_complete'] = app_format_money($cost_to_complete, $base_currency);
	    }

	    $response['rev_contract_value'] = 0;
	    $rev_contract_value = 0;
	    if(!empty($result)) {
	    	$rev_contract_value = array_sum(array_column($result, 'total_rev_contract_value'));
	    	$response['rev_contract_value'] = app_format_money($rev_contract_value, $base_currency);
	    }
	    $response['percentage_utilized'] = 0;
	    if($cost_to_complete > 0) {
	    	$response['percentage_utilized'] = round(($rev_contract_value / $cost_to_complete) * 100);
	    }
	    $response['cost_to_complete_ratio'] = $response['percentage_utilized'];
	    $response['rev_contract_value_ratio'] = 100 - $response['cost_to_complete_ratio'];
	    $response['budgeted_procurement_net_value'] = app_format_money(($cost_to_complete - $rev_contract_value), $base_currency);

	    $response['budgeted_actual_category_labels'] = array();
	    $response['budgeted_category_value'] = array();
	    $response['actual_category_value'] = array();

	    if(!empty($result)) {
		    $grouped_filter = array_values(array_reduce($result, function($carry, $item) {
			    $key = trim($item['group_name']);
			    $carry[$key]['group_name'] = $key;
			    $carry[$key]['cost_to_complete'] = ($carry[$key]['cost_to_complete'] ?? 0) + (float)$item['cost_to_complete'];
			    $carry[$key]['total_rev_contract_value'] = ($carry[$key]['total_rev_contract_value'] ?? 0) + (float)$item['total_rev_contract_value'];
			    return $carry;
			}, []));

			if(!empty($grouped_filter)) {
				foreach ($grouped_filter as $key => $value) {
					$response['budgeted_actual_category_labels'][] = $value['group_name'];
					$response['budgeted_category_value'][] = $value['cost_to_complete'];
					$response['actual_category_value'][] = $value['total_rev_contract_value'];
				}
			}
		}

		$response['procurement_table_data'] = array();
		if(!empty($result)) {
			$monthlyData = array_reduce($result, function($carry, $item) {
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

			if(!empty($monthlyData)) {
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

	    return $response;
	}

}

?>