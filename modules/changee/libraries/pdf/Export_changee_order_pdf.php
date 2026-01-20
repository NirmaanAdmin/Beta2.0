<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Export_changee_order_pdf extends App_pdf
{
    protected $changee_order;

    public function __construct($changee_order)
    {
        $changee_order                = hooks()->apply_filters('request_html_pdf_data', $changee_order);
        $GLOBALS['Export_changee_order_pdf'] = $changee_order;
        parent::__construct();
        $this->changee_order = $changee_order;

        $this->SetTitle(_l('changee'));
        # Don't remove these lines - important for the PDF layout
        $this->changee_order = $this->fix_editor_html($this->changee_order);
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
        $this->set_view_vars('changee_order', $this->changee_order);

        return $this->build();
    }

    protected function type()
    {
        return 'changee_order';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/changee/views/changee_order/export_changee_order_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}

?>