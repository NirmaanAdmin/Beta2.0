<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Dprr_pdf extends App_pdf
{
    protected $form_data;
    protected $form_type;
    protected $formid;
    protected $ci;

    public function __construct($form_data)
    {
        parent::__construct();
        
        $this->ci =& get_instance();
        $this->form_data = $form_data;
        $this->form_type = $form_data->form_type;
        $this->formid = $form_data->formid;

        $this->SetTitle("DPR");
    }

    public function prepare()
    {
        $this->set_view_vars([
            'form_data' => $this->form_data,
            'form_basic_info' => $this->get_dpr_form($this->formid),
            'form_rows_info' => $this->get_dpr_form_detail($this->formid),
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'Dprr';
    }

    protected function file_path()
    {
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/form_pdf/dprrpdf.php';
        return $actualPath;
    }

    private function get_dpr_form($form_id)
    {
        $this->ci->db->where('form_id', $form_id);
        return $this->ci->db->get(db_prefix() . 'dpr_form')->row();
    }

    private function get_dpr_form_detail($form_id)
    {
        $this->ci->db->where('form_id', $form_id);
        return $this->ci->db->get(db_prefix() . 'dpr_form_detail')->result_array();
    }
}
