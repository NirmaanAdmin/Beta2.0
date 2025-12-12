<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'stock_reconciliation.goods_delivery_code',  
    3,
    db_prefix() . 'stock_reconciliation_detail.commodity_name',
    db_prefix() . 'stock_reconciliation_detail.description',
    db_prefix() . 'stock_reconciliation_detail.area',
    db_prefix() . 'stock_reconciliation_detail.warehouse_id',
    2,
    1,                                         
    db_prefix() . 'stock_reconciliation_detail.received_quantity',
    db_prefix() . 'stock_reconciliation_detail.issued_quantities',
    db_prefix() . 'stock_reconciliation_detail.returnable_date',
    db_prefix() . 'stock_reconciliation_detail.return_date',                                          
    db_prefix() . 'stock_reconciliation_detail.reconciliation_date',
    db_prefix() . 'stock_reconciliation_detail.return_quantity',
    db_prefix() . 'stock_reconciliation_detail.used_quantity',
    db_prefix() . 'stock_reconciliation_detail.location',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'stock_reconciliation';
$join         = ['LEFT JOIN ' . db_prefix() . 'stock_reconciliation_detail ON ' . db_prefix() . 'stock_reconciliation.id = ' . db_prefix() . 'stock_reconciliation_detail.goods_delivery_id'];
$where = [];




if (get_default_project()) {
    $where[] = 'AND ' . db_prefix() . 'stock_reconciliation.project = ' . get_default_project() . '';
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'stock_reconciliation.id', db_prefix() . 'stock_reconciliation.pr_order_id', db_prefix() . 'stock_reconciliation.wo_order_id', db_prefix() . 'stock_reconciliation_detail.id as detail_id',db_prefix() . 'stock_reconciliation_detail.commodity_code',db_prefix() . 'stock_reconciliation.delivery_status as delivery_status']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 3) {
            $get_order_name = '';
            if (get_status_modules_wh('purchase')) {
                if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                    $get_order_name .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" >' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                }
            }
            if (get_status_modules_wh('purchase')) {
                if (($aRow['wo_order_id'] != '') && ($aRow['wo_order_id'] != 0)) {
                    $get_order_name .= '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order_id']) . '" >' . get_wo_order_name($aRow['wo_order_id']) . '</a>';
                }
            }

            $_data = $get_order_name;
        }
        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.area') {
            $name = get_area_name_by_id($aRow[db_prefix() . 'stock_reconciliation_detail.area']);

            $_data = $name;
        }

        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.warehouse_id') {
            $name = get_warehouse_name($aRow[db_prefix() . 'stock_reconciliation_detail.warehouse_id']);
            $_data = $name->warehouse_name;
        }

        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.issued_quantities') {
            $all_issued_quantities = '';
            if (!empty($aRow[db_prefix() . 'stock_reconciliation_detail.issued_quantities'])) {
                $issued_quantities_json = json_decode($aRow[db_prefix() . 'stock_reconciliation_detail.issued_quantities'], true);

                foreach ($issued_quantities_json as $key => $value) {
                    $all_issued_quantities .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                }
                $all_issued_quantities = rtrim($all_issued_quantities, ',</br>');
            }
            $_data = $all_issued_quantities;
        }

        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.reconciliation_date') {

            $raw = $aRow[db_prefix() . 'stock_reconciliation_detail.reconciliation_date'];
            $_data = '';

            // If no value, show nothing
            if (!empty($raw) && $raw != '""' && $raw != '[]' && $raw != '{}') {

                $decoded = json_decode($raw, true);

                // If somehow decoding fails, avoid breaking
                if (is_array($decoded)) {

                    foreach ($decoded as $vendorId => $value) {

                        $vendorName = get_vendor_name($vendorId);

                        // If value is empty OR missing: show blank editable field
                        $val = (!empty($value)) ? $value : '';

                        $_data .= '
                    <div style="margin-bottom:6px;">
                        <label >' . $vendorName . '</label>

                        <input type="date"
                            class="form-control reconciliation_input"
                            name="reconciliation[' . $aRow['detail_id'] . '][' . $vendorId . ']"
                            value="' . $val . '"
                            data-vendor="' . $vendorId . '"
                            data-rowid="' . $aRow['detail_id'] . '"
                            placeholder="dd-mm-yyyy">

                    </div>
                ';
                    }
                }
            }
        }

        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.return_date') {

            $raw = $aRow[db_prefix() . 'stock_reconciliation_detail.return_date'];
            $_data = '';

            // If no value, show nothing
            if (!empty($raw) && $raw != '""' && $raw != '[]' && $raw != '{}') {

                $decoded = json_decode($raw, true);

                // If somehow decoding fails, avoid breaking
                if (is_array($decoded)) {

                    foreach ($decoded as $vendorId => $value) {

                        $vendorName = get_vendor_name($vendorId);

                        // If value is empty OR missing: show blank editable field
                        $val = (!empty($value)) ? $value : '';

                        $_data .= '
                    <div style="margin-bottom:6px;">
                        <label >' . $vendorName . '</label>

                        <input type="date"
                            class="form-control return_date_input"
                            name="returndate[' . $aRow['detail_id'] . '][' . $vendorId . ']"
                            value="' . $val . '"
                            data-vendor="' . $vendorId . '"
                            data-rowid="' . $aRow['detail_id'] . '"
                            placeholder="dd-mm-yyyy">

                    </div>
                ';
                    }
                }
            }
        }




        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.return_quantity') {

            $raw = $aRow[db_prefix() . 'stock_reconciliation_detail.return_quantity'];
            $_data = '';

            if (!empty($raw) && $raw != '""' && $raw != '[]' && $raw != '{}') {

                $decoded = json_decode($raw, true);

                if (is_array($decoded)) {
                    foreach ($decoded as $vendorId => $value) {

                        $vendorName = get_vendor_name($vendorId);
                        $val = (!empty($value)) ? $value : '';

                        $_data .= '
                    <div style="margin-bottom:6px;">
                        <label>' . $vendorName . '</label>

                        <input type="number"
                               class="form-control return_qty_input"
                               name="return_qty[' . $aRow['detail_id'] . '][' . $vendorId . ']"
                               value="' . $val . '"
                               data-vendor="' . $vendorId . '"
                               data-rowid="' . $aRow['detail_id'] . '"
                               placeholder="Qty"
                               min="0"
                               style="margin-top:3px;">
                    </div>
                ';
                    }
                }
            }
        }


        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.used_quantity') {
            $all_used_quantity = '';
            if (!empty($aRow[db_prefix() . 'stock_reconciliation_detail.used_quantity'])) {
                $used_quantity_json = json_decode($aRow[db_prefix() . 'stock_reconciliation_detail.used_quantity'], true);

                foreach ($used_quantity_json as $key => $value) {
                    $all_used_quantity .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                }
                $all_used_quantity = rtrim($all_used_quantity, ',</br>');
                $_data = $all_used_quantity;
            }
        }

        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.location') {

            $raw = $aRow[db_prefix() . 'stock_reconciliation_detail.location'];
            $_data = '';

            if (!empty($raw) && $raw != '""' && $raw != '[]' && $raw != '{}') {

                $decoded = json_decode($raw, true);

                if (is_array($decoded)) {
                    foreach ($decoded as $vendorId => $value) {

                        $vendorName = get_vendor_name($vendorId);
                        $val = (!empty($value)) ? $value : '';

                        $_data .= '
                    <div style="margin-bottom:6px;">
                        <label>' . $vendorName . '</label>

                        <input type="text"
                               class="form-control location_input"
                               name="location[' . $aRow['detail_id'] . '][' . $vendorId . ']"
                               value="' . $val . '"
                               data-vendor="' . $vendorId . '"
                               data-rowid="' . $aRow['detail_id'] . '"
                               placeholder="Remarks"
                               style="margin-top:3px;">
                    </div>
                ';
                    }
                }
            }
        }


        if ($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.returnable_date') {
            $all_returnable_date = '';
            if (!empty($aRow[db_prefix() . 'stock_reconciliation_detail.returnable_date'])) {
                $returnable_date_json = json_decode($aRow[db_prefix() . 'stock_reconciliation_detail.returnable_date'], true);

                foreach ($returnable_date_json as $key => $value) {
                    $all_returnable_date .= get_vendor_name($key) . ": <strong style='font-weight: 700'>" . $value . "<strong>,</br>";
                }
                $all_returnable_date = rtrim($all_returnable_date, ',</br>');
                $_data = $all_returnable_date;
            }
        }

        if($aColumns[$i] == 1){
           $_data = get_ordered_quantity($aRow['pr_order_id'],$aRow['wo_order_id'],$aRow[db_prefix() . 'stock_reconciliation_detail.description'],$aRow[db_prefix() . 'stock_reconciliation_detail.commodity_code']);
        }

        if($aColumns[$i] == 2){
            $_data = _l($aRow['delivery_status']);
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
