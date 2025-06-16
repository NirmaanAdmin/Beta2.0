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
}
