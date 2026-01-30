<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Export_pur_invoice_pdf extends App_pdf
{
    protected $pur_invoice;

    public function __construct($pur_invoice)
    {
        $pur_invoice                = hooks()->apply_filters('request_html_pdf_data', $pur_invoice);
        $GLOBALS['Export_pur_invoice_pdf'] = $pur_invoice;
        parent::__construct();
        $this->pur_invoice = $pur_invoice;

        $this->SetTitle(_l('Vendor Billing Tracker'));
        # Don't remove these lines - important for the PDF layout
        $this->pur_invoice = $this->fix_editor_html($this->pur_invoice);
    }

    public function Header()
    {
        if ($this->page != 1) {
            return;
        }
        $default_project = get_default_project();
        $project_name = get_project_name_by_id($default_project);
        $this->SetPageOrientation('L', true);
        $pageWidth  = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();
        $this->SetAutoPageBreak(false, 0);
        $this->Image(
            FCPATH . 'uploads/export_table_company_images/landscape.jpeg',
            0,
            0,
            $pageWidth,
            $pageHeight
        );

        $this->SetTextColor(255, 255, 255);
        $this->SetFont($this->get_font_name(), 'B', 17);
        $this->SetXY(30, 190);
        $this->Cell(80, 12, _l('Vendor Billing Tracker'), 0, 0, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->get_font_name(), 'B', 13);
        $this->SetXY(222, 132);
        $this->Cell(80, 12, $project_name, 0, 0, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->get_font_name(), 'B', 10);
        $this->SetXY(224, 200);
        $this->Cell(80, 12, 'BASILIUS INTERNATIONAL LLP', 0, 0, 'C');

        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->setPageMark();
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
        $this->set_view_vars('pur_invoice', $this->pur_invoice);

        return $this->build();
    }

    protected function type()
    {
        return 'pur_invoice';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/invoices/export_pur_invoice_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}

?>