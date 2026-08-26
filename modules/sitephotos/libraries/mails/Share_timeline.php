<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Share_timeline extends App_mail_template
{
    protected $for = 'staff';

    protected $share_timeline;

    public $slug = 'share_timeline';

    public function __construct($share_timeline)
    {
        parent::__construct();

        $this->share_timeline = $share_timeline;
        // For SMS and merge fields for email
        $this->set_merge_fields('share_timeline_merge_fields', $this->share_timeline);
    }

    public function build()
    {
        $this->to($this->share_timeline->email);
    }
    
}

?>