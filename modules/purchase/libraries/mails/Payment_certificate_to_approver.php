<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_certificate_to_approver extends App_mail_template
{
    protected $for = 'purchase';

    protected $data;

    public $slug = 'payment-certificate-to-approver';

    public function __construct($data)
    {
        parent::__construct();

        $this->data = $data;
        $this->set_merge_fields('payment_certificate_to_approver_merge_fields', $this->data);
    }
    public function build()
    {
        $this->to($this->data->mail_to);
    }
}

?>