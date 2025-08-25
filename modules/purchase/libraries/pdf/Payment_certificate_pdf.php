<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Payment_certificate_pdf extends App_pdf
{
    protected $payment_certificate;
    protected $footer_text;

    public function __construct($payment_certificate, $footer_text = '')
    {
        $payment_certificate = hooks()->apply_filters('request_html_pdf_data', $payment_certificate);
        $GLOBALS['payment_certificate_pdf'] = $payment_certificate;
        parent::__construct();
        
        $this->footer_text = $footer_text;
        $this->payment_certificate = $payment_certificate;
        
        $this->SetTitle(_l('payment_certificate'));
        $this->SetAutoPageBreak(false, 0);
        # Don't remove these lines - important for the PDF layout
        $this->payment_certificate = $this->fix_editor_html($this->payment_certificate);
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

        if($this->footer_text !== '') {
            // Set default footer position and font (if additional styling needed)
            $this->SetX(15); // 15mm from the bottom
            $this->SetY(-15); // 15mm from the bottom
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, $this->footer_text, 0, 0, 'L');
        }
    }


    public function prepare()
    {
        $this->set_view_vars('payment_certificate', $this->payment_certificate);

        return $this->build();
    }

    protected function type()
    {
        return 'payment_certificate';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/payment_certificate/payment_certificatepdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}