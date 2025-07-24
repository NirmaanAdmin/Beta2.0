<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Send_critical_tracker_mail extends App_mail_template
{
    // must match the slug in your `tblemailtemplates` row
    public $slug = 'send-critical-tracker-mail';

    // module/context
    protected $for = 'purchase';

    protected $data;

    public function __construct($data)
    {
        parent::__construct();

        $this->data = $data;
        // our merge‑fields class from the previous step
        $this->set_merge_fields(
            'critical_tracker_target_date_merge_fields',
            $this->data
        );
    }

    public function build()
    {
        // send to the single address we pass in controller
        $this->to($this->data->mail_to);
        // subject/from/fromname/fromemail are pulled from the DB template
    }
}
