<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Per_clients_pdf extends App_pdf
{
    protected $per_clients;
    protected $footer_text;

    public function __construct($per_clients, $footer_text = '')
    {
        $per_clients                = hooks()->apply_filters('request_html_pdf_data', $per_clients);
        $GLOBALS['Per_clients_pdf'] = $per_clients;
        parent::__construct();
        
        $this->footer_text = $footer_text;
        $this->per_clients = $per_clients;
        
        $this->SetTitle(_l('Client Data'));
        # Don't remove these lines - important for the PDF layout
        $this->per_clients = $this->fix_editor_html($this->per_clients);
    }


    public function prepare()
    {
        $this->set_view_vars('per_clients', $this->per_clients);

        return $this->build();
    }

    protected function type()
    {
        return 'per_clients';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/personal_client/per_clientspdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}