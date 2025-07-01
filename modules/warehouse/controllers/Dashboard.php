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

    public function receipt_status_charts()
    {
        $default_project = get_default_project();
        $aColumns = [
            'pr_order_id',
            1,
            2,
            3,
            'date_add',
        ];
        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'goods_receipt';
        $join         = [];
        $where = [];
        if (!empty($default_project)) {
            $where[] = 'AND ' . db_prefix() . 'goods_receipt.project = ' . $default_project;
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'date_add', 'date_c', 'goods_receipt_code', 'supplier_code']);


        $output  = $result['output'];
        $rResult = $result['rResult'];


        foreach ($rResult as $aRow) {
            $row = [];

            for ($i = 0; $i < count($aColumns); $i++) {

                $_data = $aRow[$aColumns[$i]];
                if ($aColumns[$i] == 'date_add') {
                    $_data = date('d M, Y', strtotime($aRow['date_add']));
                } elseif ($aColumns[$i] == 'pr_order_id') {
                    $get_pur_order_name = '';
                    if (get_status_modules_wh('purchase')) {
                        if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                            $get_pur_order_name .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" >' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                        }
                    }

                    $_data = $get_pur_order_name;
                } elseif ($aColumns[$i] == 1) {
                    $_data =  get_documentation_yes_or_no($aRow['id'], 2);
                } elseif ($aColumns[$i] == 2) {
                    $_data =  get_documentation_yes_or_no($aRow['id'], 3);
                } elseif ($aColumns[$i] == 3) {
                    $_data =  get_documentation_yes_or_no($aRow['id'], 4);
                }



                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }

    public function return_details_charts()
    {
        $default_project = get_default_project();
        $aColumns = [
            1,
            'commodity_name',
            db_prefix() . 'goods_delivery_detail.description',
            2,
            'returnable_date',
            3,
        ]; 
        $sIndexColumn = db_prefix() . 'goods_delivery_detail.id';
        $sTable       = db_prefix() . 'goods_delivery_detail';
        $join = [
            'LEFT JOIN ' . db_prefix() . 'goods_delivery gd ON gd.id = ' . db_prefix() . 'goods_delivery_detail.goods_delivery_id'
        ];
        $where = [];

        array_push($where, 'AND returnable = 1');
        array_push($where, 'AND returnable_date != ""');
        if (!empty($default_project)) {
            array_push($where, 'AND gd.project = ' . $default_project);
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'goods_delivery_detail.id', 'goods_delivery_id']);


        $output  = $result['output'];
        $rResult = $result['rResult'];


        foreach ($rResult as $aRow) {
            $row = [];

            for ($i = 0; $i < count($aColumns); $i++) {

                $_data = $aRow[$aColumns[$i]];
                if ($aColumns[$i] == 'commodity_name') {
                    $_data = $aRow['commodity_name'];
                } elseif ($aColumns[$i] == 1) {
                    $_data = get_issued_code($aRow['goods_delivery_id']);
                } elseif ($aColumns[$i] == 'description') {
                    $_data = $aRow['description'];
                } elseif ($aColumns[$i] == 2) {
                    $raw_json = !empty($aRow['returnable_date']) ? $aRow['returnable_date'] : '';
                    $names_output = [];
                    if (!empty($raw_json)) {
                        $decoded = json_decode($raw_json, true);
                        if (is_array($decoded)) {
                            $vendor_ids = array_keys($decoded);

                            foreach ($vendor_ids as $vendor_id) {
                                $company_name = wh_get_vendor_company_name($vendor_id);
                                if (!empty($company_name)) {
                                    $names_output[] = $company_name;
                                }
                            }
                        }
                    }

                    $_data = implode(',<br>', $names_output);
                } elseif ($aColumns[$i] == 'returnable_date') {
                    $raw_json = !empty($aRow['returnable_date']) ? $aRow['returnable_date'] : '';
                    $date_output = [];

                    if (!empty($raw_json)) {
                        $decoded = json_decode($raw_json, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $date) {
                                if (!empty($date)) {
                                    $date_output[] = date('d M, Y', strtotime($date));
                                }
                            }
                        }
                    }

                    $_data = implode(',<br>', $date_output);
                } elseif ($aColumns[$i] == 3) {
                    $raw_json = !empty($aRow['returnable_date']) ? $aRow['returnable_date'] : '';
                    $date_output = [];
                    $past_date_count = 0;

                    if (!empty($raw_json)) {
                        $decoded = json_decode($raw_json, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $date) {
                                if (!empty($date)) {
                                    $date_output[] = $date;

                                    // Compare date with today
                                    $timestamp = strtotime(str_replace('/', '-', $date)); // safe for any format
                                    if ($timestamp < strtotime(date('d-m-Y'))) {
                                        $past_date_count++;
                                    }
                                }
                            }
                        }
                    }

                    $_data = get_return_details_status($aRow['goods_delivery_id'],$past_date_count);
                }

                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }
}
