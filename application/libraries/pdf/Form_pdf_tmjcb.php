<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Form_pdf_tmjcb extends App_pdf
{
    protected $form;
    protected $subject;
    protected $tmjcb_data;
    protected $tmjcb_details;
    protected $form_data;
    protected $form_itmes;

    public function __construct($form)
    {
        // store the form object globally if some hooks need it
        $GLOBALS['Form_pdf_tmjcb'] = $form;

        parent::__construct();

        // $this->SetPageOrientation('L');

        $this->ci->load->model('forms_model');
        // assign to your property
        $this->form = $form;

        // <-- fix is here: use the object directly, not $this->$form
        $this->subject = $this->form->subject;
        $this->tmjcb_data = $this->ci->forms_model->get_tmjcb_form($this->form->formid);
        $this->tmjcb_details = $this->ci->forms_model->get_tmjcb_form_detail($this->form->formid);
        $this->form_data = $this->ci->forms_model->get_form_by_id($this->form->formid);
        $this->form_itmes = $this->ci->forms_model->get_form_items('tmjcb');

        $this->SetTitle($this->subject);
    }

    public function prepare()
    {
        $this->set_view_vars([
            'subject' => $this->subject,
            'form'    => $this->form,
            'tmjcb_data' => $this->tmjcb_data,
            'tmjcb_details' => $this->tmjcb_details,
            'form_data' => $this->form_data,
            'form_items' => $this->form_itmes
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
            . '/views/formpdftmjcb.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
