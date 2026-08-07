<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'warehouse_id',
	'warehouse_code',
	'warehouse_name',
	'warehouse_address',
	db_prefix().'warehouse.order as wh_order',
	'display',
	'note',
];
$sIndexColumn = 'warehouse_id';
$sTable = db_prefix() . 'warehouse';

$where = [];

$join= [];



$custom_fields = get_custom_fields('warehouse_name', [
    'show_on_table' => 1,
    ]);

$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $i . ' ON '.db_prefix().'warehouse.warehouse_id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="warehouse_name" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['warehouse_id','warehouse_code','warehouse_name','warehouse_address','city', 'state', 'zip_code', 'country']);

$output = $result['output'];
$rResult = $result['rResult'];



	foreach ($rResult as $aRow) {
		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {

			if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
	            $_data = $aRow[strafter($aColumns[$i], 'as ')];
	        }

	        if($aColumns[$i] == 'warehouse_id') {
	        	$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['warehouse_id'] . '"><label></label></div>';
	        } elseif ($aColumns[$i] == 'warehouse_code') {
				$code = '<a href="' . admin_url('warehouse/view_warehouse_detail/' . $aRow['warehouse_id']) . '">' . $aRow['warehouse_code'] . '</a>';
				$code .= '<div class="row-options">';

				$code .= '<a href="' . admin_url('warehouse/view_warehouse_detail/' . $aRow['warehouse_id']) . '" >' . _l('view') . '</a>';

				if (has_permission('warehouse', '', 'edit') || is_admin()) {

					$code .= ' | <a href="#" onclick="edit_warehouse_type(this, '.$aRow['warehouse_id'] .'); return false;"  data-commodity_id="' . $aRow['warehouse_id'] . '"  >' . _l('edit') . '</a>';
				}
				if (has_permission('warehouse', '', 'delete') || is_admin()) {
					$code .= ' | <a href="' . admin_url('warehouse/delete_warehouse/' . $aRow['warehouse_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
				}


				$code .= '</div>';

				$_data = $code;

			} elseif ($aColumns[$i] == 'warehouse_name') {

				$_data = $aRow['warehouse_name'];

			}elseif($aColumns[$i] == 'warehouse_address'){

				$address='';

				$warehouse_address = [];
				$warehouse_address[0] =  $aRow['warehouse_address'];
				$warehouse_address[1] = $aRow['city'];
				$warehouse_address[2] =  $aRow['state'];
				$warehouse_address[3] =  $aRow['country'];
				$warehouse_address[4] =  $aRow['zip_code'];

				foreach ($warehouse_address as $key => $add_value) {
				    if(isset($add_value) && $add_value != ''){
				    	switch ($key) {
				    		case 0:
				    			$address .= $add_value.'<br>';
				    			break;
				    		case 1:
				    			$address .= $add_value;
				    			break;
				    		case 2:
				    			$address .= ', '.$add_value.'<br>';
				    			break;
				    		case 3:
				    			$address .= get_country_name($add_value);
				    			break;
				    		case 4:
				    			$address .= ', '.$add_value;
				    			break;

				    		default:
				    			# code...
				    			break;
				    	}

				    }
				}

				$_data = $address;

			} elseif ($aColumns[$i] == 'wh_order') {
				$_data = $aRow['wh_order'];

			} elseif ($aColumns[$i] == 'display') {

        		if($aRow['display'] == 0){
        		 $_data =  _l('not_display'); 
        		}else{
        			$_data = _l('display');
        		}

			} elseif ($aColumns[$i] == 'note') {

				$_data = $aRow['note'];
			}

			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

