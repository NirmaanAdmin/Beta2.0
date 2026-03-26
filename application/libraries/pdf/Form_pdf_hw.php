<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Form_pdf_hw extends App_pdf
{
    protected $form;
    protected $subject;
    protected $hw_data;
    protected $hw_details;
    protected $form_data;
    

    public function __construct($form)
    {
        // store the form object globally if some hooks need it
        $GLOBALS['Form_pdf_hw'] = $form;

        parent::__construct();

        // $this->SetPageOrientation('L');

        $this->ci->load->model('forms_model');
        // assign to your property
        $this->form = $form;

        // <-- fix is here: use the object directly, not $this->$form
        $this->subject = $this->form->subject;
        $this->hw_data = $this->ci->forms_model->get_hw_form($this->form->formid);
        $this->hw_details = $this->ci->forms_model->get_hw_form_detail($this->form->formid);
        $this->form_data = $this->ci->forms_model->get_form_by_id($this->form->formid);

        $this->SetTitle($this->subject);
    }

    public function prepare()
    {
        $this->set_view_vars([
            'subject' => $this->subject,
            'form'    => $this->form,
            'hw_data' => $this->hw_data,
            'hw_details' => $this->hw_details,
            'form_data' => $this->form_data
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'form';
    }

    protected function file_path()
    {
        $customPath = APPPATH
            . 'views/themes/'
            . active_clients_theme()
            . '/views/my_formpdf.php';

        $actualPath = APPPATH
            . 'views/themes/'
            . active_clients_theme() 
            . '/views/formpdfhw.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
