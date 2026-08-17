<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Wklookahead_pdf extends App_pdf
{
    protected $wklookahead;

    public function __construct($wklookahead)
    {
        $wklookahead                = hooks()->apply_filters('request_html_pdf_data', $wklookahead);
        $GLOBALS['wklookahead_pdf'] = $wklookahead;
        parent::__construct();
        
        $this->wklookahead = $wklookahead;
        
        $this->SetTitle(_l('3 WK Lookahead'));
        # Don't remove these lines - important for the PDF layout
        $this->wklookahead = $this->fix_editor_html($this->wklookahead);
    }


    public function prepare()
    {
        $this->set_view_vars('wklookahead', $this->wklookahead);

        return $this->build();
    }

    protected function type()
    {
        return 'wklookahead';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/wklookahead/wklookaheadpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}