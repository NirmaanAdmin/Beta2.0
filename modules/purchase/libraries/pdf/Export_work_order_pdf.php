<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Export_work_order_pdf extends App_pdf
{
    protected $work_order;

    public function __construct($work_order)
    {
        $work_order                = hooks()->apply_filters('request_html_pdf_data', $work_order);
        $GLOBALS['Export_work_order_pdf'] = $work_order;
        parent::__construct();
        $this->work_order = $work_order;

        $this->SetTitle(_l('Vendor Billing Tracker'));
        # Don't remove these lines - important for the PDF layout
        $this->work_order = $this->fix_editor_html($this->work_order);
    }

    // Override the Footer method from TCPDF or FPDI
    public function Footer()
    {
        // Trigger the custom hook for the footer content
        hooks()->do_action('pdf_footer', ['pdf_instance' => $this, 'type' => $this->type]);
       
        $this->SetY(-20); // 15mm from the bottom
        $this->SetX(-15); // 15mm from the bottom
        $this->SetFont($this->get_font_name(), 'I', 8);
        $this->SetTextColor(142, 142, 142);
        $this->Cell(0, 15, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

    }


    public function prepare()
    {
        $this->set_view_vars('work_order', $this->work_order);

        return $this->build();
    }

    protected function type()
    {
        return 'work_order';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/work_order/export_work_order_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}

?>