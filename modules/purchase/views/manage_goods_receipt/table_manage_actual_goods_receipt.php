<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'goods_receipt_code',
    'pr_order_id',
    'commodity_code',
    'description',
    'area',
    'po_quantities',
    'quantities',
    'remaining_quantities',
    'supplier_name',
    'kind',
    'date_add',
    'last_action',
    'imp_local_status',
    'tracker_status',
    'production_status',
    'payment_date',
    'est_delivery_date',
    'delivery_date',
    'remarks',
    'lead_time_days',
    'advance_payment',
    1,
    2,
    'shop_submission',
    'shop_approval',
    'actual_remarks',
];
$join = [];
$where = [];

if ($this->ci->input->post('day_vouchers')) {
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if ($this->ci->input->post('kind')) {
    $kind = $this->ci->input->post('kind');
}

if ($this->ci->input->post('delivery')) {
    $delivery = $this->ci->input->post('delivery');
}

if ($this->ci->input->post('toggle-filter')) {
    $where[] = 'AND type = 2';
}

if (isset($day_vouchers)) {
    $where[] = 'AND date_add <= "' . $day_vouchers . '"';
}

if (isset($kind)) {
    $where[] = 'AND kind = "' . $kind . '"';
}

if (isset($delivery)) {
    if ($delivery == "undelivered") {
        $where[] = 'AND delivery_status = "0"';
    } else if ($delivery == "partially_delivered") {
        $where[] = 'AND delivery_status = "1"';
    } else if ($delivery == "completely_delivered") {
        $where[] = 'AND delivery_status = "2"';
    } else {
        $where[] = 'AND delivery_status = "0"';
    }
}

if (
    $this->ci->input->post('vendors')
    && count($this->ci->input->post('vendors')) > 0
) {
    $where[] = 'AND supplier_name IN (' . implode(',', $this->ci->input->post('vendors')) . ')';
}

if (
    $this->ci->input->post('group_pur')
    && count($this->ci->input->post('group_pur')) > 0
) {
    $where[] = 'AND group_pur IN (' . implode(',', $this->ci->input->post('group_pur')) . ')';
}

if (
    $this->ci->input->post('tracker_status')
    && count($this->ci->input->post('tracker_status')) > 0
) {
    $where[] = 'AND tracker_status IN (' . implode(',', $this->ci->input->post('tracker_status')) . ')';
}

if (
    $this->ci->input->post('production_status')
    && count($this->ci->input->post('production_status')) > 0
) {
    $where[] = 'AND production_status IN (' . implode(',', $this->ci->input->post('production_status')) . ')';
}
$wo_po_orders = $this->ci->input->post('wo_po_order') ? $this->ci->input->post('wo_po_order') : [];
if (!empty($wo_po_orders)) {
    $where_conditions = [];
    foreach ($wo_po_orders as $order_value) {
        $parts = explode('-', $order_value);
        if (count($parts) === 3) {
            $order_type = (int)$parts[1];
            $order_id = (int)$parts[0];
            $goods_id = (int)$parts[2];

            if ($order_type === 2 && $goods_id === 0) { // Purchase Order
                $where_conditions[] = '(pr_order_id = ' . $order_id . ' AND type = 2 )';
            } elseif ($order_type === 3 && $goods_id === 0) { // Work Order
                $where_conditions[] = '(wo_order_id = ' . $order_id . ' AND type = 3 )';
            } elseif ($order_type === 2 && $goods_id === 1) {
                $where_conditions[] = '(pr_order_id = ' . $order_id . ' AND type = 1 )';
            } elseif ($order_type === 3 && $goods_id === 1) {
                $where_conditions[] = '(wo_order_id = ' . $order_id . ' AND type = 1 )';
            }
        }
    }
    if (!empty($where_conditions)) {
        $where[] = 'AND (' . implode(' OR ', $where_conditions) . ')';
    }
}
if (get_default_project()) {
    $where[] = 'AND project = "' . get_default_project() . '"';
}
$this->ci->load->model('warehouse/warehouse_model');

$result = data_tables_actual_purchase_tracker_init($aColumns, $join, $where, [
    'id',
    'unit_id',
    'item_detail_id',
    'type',
    'wo_order_id',
    'last_action'
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $pur_order = ($aRow['type'] == 2);
    $wo_order = ($aRow['type'] == 3);
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'goods_receipt_code') {
            $name = '';
            if (!empty($aRow['goods_receipt_code'])) {
                $name .= '<a href="' . admin_url('purchase/view_purchase/' . $aRow['id']) . '" 
                onclick="init_goods_receipt(' . $aRow['id'] . '); small_table_full_view(); return false;">' .
                    $aRow['goods_receipt_code'] . '</a>';
            } else {
                if ($aRow['type'] == 2) {
                    $name .= '<a href="' . admin_url('purchase/view_po_tracker/' . $aRow['id']) . '" onclick="init_po_tracker(' . $aRow['id'] . '); small_table_full_view(); return false;">' . _l('Update') . '</a>';
                } elseif ($aRow['type'] == 3) {
                    $name .= '<a href="' . admin_url('purchase/view_wo_tracker/' . $aRow['id']) . '" onclick="init_wo_tracker(' . $aRow['id'] . '); small_table_full_view(); return false;">' . _l('Update') . '</a>';
                }
            }
            $_data = $name;
        } elseif ($aColumns[$i] == 'pr_order_id') {
            $name = '';
            if ($aRow['type'] == 2) {
                if (($aRow['id'] != '') && ($aRow['id'] != 0)) {
                    $name = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; display: inline-block;">' . get_pur_order_name($aRow['id']) . '</a>';
                }
            } elseif ($aRow['type'] == 3) {
                if (($aRow['id'] != '') && ($aRow['id'] != 0)) {
                    $name = '<a href="' . admin_url('purchase/work_order/' . $aRow['id']) . '" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; display: inline-block;">' . get_work_order_name($aRow['id']) . '</a>';
                }
            } else {
                if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                    $name = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; display: inline-block;">' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                } elseif (($aRow['wo_order_id'] != '') && ($aRow['wo_order_id'] != 0)) {
                    $name = '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order_id']) . '" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; display: inline-block;">' . get_work_order_name($aRow['wo_order_id']) . '</a>';
                }
            }
            $_data = $name;
        } elseif ($aColumns[$i] == 'commodity_code') {
            $_data = '<div style="width: 200px">' . wh_get_item_variatiom($aRow['commodity_code']) . '</div>';
        } elseif ($aColumns[$i] == 'description') {
            $_data = '<div style="width: 300px">' . html_entity_decode($aRow['description']) . '</div>';
        } elseif ($aColumns[$i] == 'area') {
            $_data = get_area_name_by_id($aRow['area']);
        } elseif ($aColumns[$i] == 'po_quantities') {
            $unit_name = '';
            if (is_numeric($aRow['unit_id'])) {
                $unit_name = (get_unit_type($aRow['unit_id']) != null && isset(get_unit_type($aRow['unit_id'])->unit_name)) ? get_unit_type($aRow['unit_id'])->unit_name : '';
            }
            $_data = html_entity_decode($aRow['po_quantities']) . ' ' . html_entity_decode($unit_name);
        } elseif ($aColumns[$i] == 'quantities') {
            $unit_name = '';
            if (is_numeric($aRow['unit_id'])) {
                $unit_name = (get_unit_type($aRow['unit_id']) != null && isset(get_unit_type($aRow['unit_id'])->unit_name)) ? get_unit_type($aRow['unit_id'])->unit_name : '';
            }
            $_data = html_entity_decode($aRow['quantities']) . ' ' . html_entity_decode($unit_name);
        } elseif ($aColumns[$i] == 'remaining_quantities') {
            $unit_name = '';
            if (is_numeric($aRow['unit_id'])) {
                $unit_name = (get_unit_type($aRow['unit_id']) != null && isset(get_unit_type($aRow['unit_id'])->unit_name)) ? get_unit_type($aRow['unit_id'])->unit_name : '';
            }
            $_data = html_entity_decode($aRow['remaining_quantities']) . ' ' . html_entity_decode($unit_name);
        } elseif ($aColumns[$i] == 'supplier_name') {
            $_data = wh_get_vendor_company_name($aRow['supplier_name']);
        } elseif ($aColumns[$i] == 'kind') {
            $_data = $aRow['kind'];
        } elseif ($aColumns[$i] == 'date_add') {
            $_data = date('d M, Y', strtotime($aRow['date_add']));
        } elseif($aColumns[$i] == 'last_action'){
            $_data = get_last_action_full_name($aRow['last_action']);
        } elseif ($aColumns[$i] == 'imp_local_status') {
            $imp_local_status = '';
            $imp_local_labels = [
                1 => ['label' => 'danger', 'table' => 'not_set', 'text' => _l('not_set')],
                2 => ['label' => 'success', 'table' => 'imported', 'text' => _l('imported')],
                3 => ['label' => 'info', 'table' => 'local', 'text' => _l('local')],
            ];
            if ($aRow['imp_local_status'] > 0) {
                $status = $imp_local_labels[$aRow['imp_local_status']];
                $imp_local_status = '<span class="inline-block label label-' . $status['label'] . '" id="imp_status_span_' . $aRow['item_detail_id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];

                $imp_local_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $imp_local_status .= '<a href="#" class="dropdown-toggle text-dark" id="tableImpLocalStatus-' . $aRow['item_detail_id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $imp_local_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $imp_local_status .= '</a>';
                $imp_local_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableImpLocalStatus-' . $aRow['item_detail_id'] . '">';
                $purOrder = ($aRow['type'] == 2 ? 'true' : 'false');
                foreach ($imp_local_labels as $key => $status) {
                    if ($key != $aRow['imp_local_status']) {
                        $imp_local_status .= '<li>
                    <a href="#" onclick="change_imp_local_status(' . $key . ', ' . $aRow['item_detail_id'] . ', ' . ($aRow['type'] == 1 ? 'true' : 'false') . ', ' . $purOrder . '); return false;">
                            ' . $status['text'] . '
                        </a>
                    </li>';
                    }
                }
                $imp_local_status .= '</ul>';
                $imp_local_status .= '</div>';
                $imp_local_status .= '</span>';
            }
            $_data = $imp_local_status;
        } elseif ($aColumns[$i] == 'tracker_status') {
            $tracker_status = '';
            $tracker_status_labels = [
                1 => ['label' => 'danger', 'table' => 'not_set', 'text' => _l('not_set')],
                2 => ['label' => 'info', 'table' => 'SPC', 'text' => 'SPC'],
                3 => ['label' => 'info', 'table' => 'RFQ', 'text' => 'RFQ'],
                4 => ['label' => 'info', 'table' => 'FQR', 'text' => 'FQR'],
                5 => ['label' => 'info', 'table' => 'POI', 'text' => 'POI'],
                6 => ['label' => 'info', 'table' => 'PIR', 'text' => 'PIR'],
            ];
            if ($aRow['tracker_status'] > 0) {
                $status = $tracker_status_labels[$aRow['tracker_status']];
                $tracker_status = '<span class="inline-block label label-' . $status['label'] . '" id="tracker_status_span_' . $aRow['item_detail_id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];

                $tracker_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $tracker_status .= '<a href="#" class="dropdown-toggle text-dark" id="tableTrackerStatus-' . $aRow['item_detail_id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $tracker_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $tracker_status .= '</a>';
                $tracker_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTrackerStatus-' . $aRow['item_detail_id'] . '">';
                $purOrder = ($aRow['type'] == 2 ? 'true' : 'false');
                foreach ($tracker_status_labels as $key => $status) {
                    if ($key != $aRow['tracker_status']) {
                        $tracker_status .= '<li>
                      <a href="#" onclick="change_tracker_status(' . $key . ', ' . $aRow['item_detail_id'] . ', ' . ($aRow['type'] == 1 ? 'true' : 'false') . ', ' . $purOrder . '); return false;"> 
                          ' . $status['text'] . '
                      </a>
                  </li>';
                    }
                }
                $tracker_status .= '</ul>';
                $tracker_status .= '</div>';
                $tracker_status .= '</span>';
            }
            $_data = $tracker_status;
        } elseif ($aColumns[$i] == 'production_status') {
            $production_status = '';
            $production_labels = [
                1 => ['label' => 'danger', 'table' => 'not_started', 'text' => _l('not_started')],
                2 => ['label' => 'success', 'table' => 'approved', 'text' => _l('approved')],
                3 => ['label' => 'info', 'table' => 'on_going', 'text' => _l('on_going')],
                4 => ['label' => 'warning', 'table' => 'delivered', 'text' => _l('Delivered')],
            ];
            if ($aRow['production_status'] > 0) {
                $status = $production_labels[$aRow['production_status']];
                $production_status = '<span class="inline-block label label-' . $status['label'] . '" id="status_span_' . $aRow['item_detail_id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];
                $production_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $production_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['item_detail_id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $production_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $production_status .= '</a>';
                $production_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['item_detail_id'] . '">';

                $purOrder = ($aRow['type'] == 2 ? 'true' : 'false');
                foreach ($production_labels as $key => $status) {
                    if ($key != $aRow['production_status']) {
                        $production_status .= '<li>
                      <a href="#" onclick="change_production_status(' . $key . ', ' . $aRow['item_detail_id'] . ', ' . ($aRow['type'] == 1 ? 'true' : 'false') . ', ' . $purOrder . '); return false;"> 
                          ' . $status['text'] . '
                      </a>
                  </li>';
                    }
                }
                $production_status .= '</ul>';
                $production_status .= '</div>';
                $production_status .= '</span>';
            }
            $_data = $production_status;
        } elseif ($aColumns[$i] == 'payment_date') {
            $_data = '<input type="date" class="form-control payment-date-input"
              value="' . htmlspecialchars($aRow['payment_date']) . '"
              data-id="' . $aRow['item_detail_id'] . '"
              data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
              data-purorder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">';
        } elseif ($aColumns[$i] == 'est_delivery_date') {
            $_data = '<input type="date" class="form-control est-delivery-date-input"
              value="' . htmlspecialchars($aRow['est_delivery_date']) . '"
              data-id="' . $aRow['item_detail_id'] . '"
              data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
              data-purorder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">';
        } elseif ($aColumns[$i] == 'delivery_date') {
            $_data = '<input type="date" class="form-control delivery-date-input"
              value="' . htmlspecialchars($aRow['delivery_date']) . '"
              data-id="' . $aRow['item_detail_id'] . '"
              data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
              data-purorder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">';
        } elseif ($aColumns[$i] == 'remarks') {
            $remarks = $aRow['remarks'];
            $_data = '<textarea style="width: 154px;height: 50px;" 
                class="form-control remarks-input"
                data-id="' . $aRow['item_detail_id'] . '" 
                data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
                data-purorder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">' .
                htmlspecialchars($remarks) .
                '</textarea>';
        } elseif ($aColumns[$i] == 'lead_time_days') {
            $_data = '<div class="form-group">
                <input type="number" id="lead_time_days" name="lead_time_days" class="form-control" min="0" max="100" 
                       value="' . $aRow['lead_time_days'] . '" 
                       data-id="' . $aRow['item_detail_id'] . '" 
                       data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
                       data-purOrder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">
            </div>';
        } elseif ($aColumns[$i] == 'advance_payment') {
            $_data = '<div class="form-group">
                <input type="number" id="advance_payment" name="advance_payment" class="form-control" min="0" max="100" 
                       value="' . $aRow['advance_payment'] . '" 
                       data-id="' . $aRow['item_detail_id'] . '" 
                       data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
                       data-purOrder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">
            </div>';
        } elseif ($aColumns[$i] == 1) {

            $_data = '<div class="input-group" style="width: 100%;">
                <input type="file"
                    name="attachments[]"
                    class="form-control upload_shop_drawings_files"
                    data-id="' . $aRow['item_detail_id'] . '"
                    multiple
                    style="min-width: 20px; width: 100%;">
                <span class="input-group-btn">
                    <button type="button"
                        class="btn btn-success upload_shop_drawings_attachments"
                        data-id="' . $aRow['item_detail_id'] . '"
                        data-purOrder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '"
                        data-workOrder="' . ($aRow['type'] == 3 ? 'true' : 'false') . '"
                        title="Upload Attachments">
                        <i class="fa fa-upload"></i>
                    </button>
                </span>
            </div>';
        } elseif ($aColumns[$i] == 2) {
            $view_type = null;
            if (($aRow['type'] == 2 ? 'true' : 'false') == 'true') {
                $view_type = 'purchase_orders';
            } elseif (($aRow['type'] == 2 ? 'true' : 'false') == 'true') {
                $view_type = 'work_orders';
            }

            $attachments = $this->ci->warehouse_model->get_inventory_shop_drawing_attachments(
                'goods_receipt_shop_d',
                $aRow['item_detail_id'],
                $view_type
            );

            if (!empty($attachments)) {
                $_data = '<a href="javascript:void(0)" onclick="view_purchase_tracker_attachments(' . $attachments[0]['rel_id'] . ', \'' . $attachments[0]['view_type'] . '\'); return false;" class="btn btn-info btn-icon">View Files</a>';
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == 'shop_submission') {
            $_data = '<input type="date" id="shop_submission" name="shop_submission" class="form-control"
               value="' . htmlspecialchars($aRow['shop_submission']) . '"
               data-id="' . $aRow['item_detail_id'] . '"
               data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
               data-purOrder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">';
        } elseif ($aColumns[$i] == 'shop_approval') {
            $_data = '<input type="date" id="shop_approval" name="shop_approval" class="form-control"
               value="' . htmlspecialchars($aRow['shop_approval']) . '"
               data-id="' . $aRow['item_detail_id'] . '"
               data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
               data-purOrder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">';
        } elseif ($aColumns[$i] == 'actual_remarks') {
            $_data = '<textarea style="width: 154px;height: 50px;" 
                class="form-control" 
                name="actual_remarks"
                data-id="' . $aRow['item_detail_id'] . '" 
                data-tracker="' . ($aRow['type'] == 1 ? 'true' : 'false') . '"
                data-purOrder="' . ($aRow['type'] == 2 ? 'true' : 'false') . '">' .
                htmlspecialchars($aRow['actual_remarks']) .
                '</textarea>';
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
