<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Changee_quotation_to_contact extends App_mail_template
{
    protected $for = 'contact';

    protected $data;

    public $slug = 'changee-quotation-to-contact';

    public function __construct($data)
    {
        parent::__construct();

        $this->data = $data;
        // For SMS and merge fields for email
        $this->set_merge_fields('changee_quotation_merge_fields', $this->data);
    }
    public function build()
    {
        $this->to($this->data->mail_to);
    }
}
