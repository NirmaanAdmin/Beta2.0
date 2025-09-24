<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Bill_bifurcation_pdf extends App_pdf
{
    protected $bill_bifurcation;

    public function __construct($bill_bifurcation)
    {
        $bill_bifurcation                = hooks()->apply_filters('request_html_pdf_data', $bill_bifurcation);
        $GLOBALS['bill_bifurcation_pdf'] = $bill_bifurcation;

        parent::__construct();

        $this->bill_bifurcation = $bill_bifurcation;

        $this->SetTitle(_l('bill_bifurcation'));
        # Don't remove these lines - important for the PDF layout
        $this->bill_bifurcation = $this->fix_editor_html($this->bill_bifurcation);
    }

    public function prepare()
    {
        $this->set_view_vars('bill_bifurcation', $this->bill_bifurcation);

        return $this->build();
    }

    protected function type()
    {
        return 'bill_bifurcation';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/pur_bills/pur_billpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}