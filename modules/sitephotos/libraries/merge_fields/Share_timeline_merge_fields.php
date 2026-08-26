<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Share_timeline_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Link',
                'key'       => '{link}',
                'available' => [
                    'sitephotos',
                ],
            ],
            [
                'name'      => 'Message',
                'key'       => '{message}',
                'available' => [
                    'sitephotos',
                ],
            ]
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $attendance 
     * @return array
     */
    public function format($data)
    {        
        $fields['{link}'] = $data->link;
        $fields['{message}'] = $data->message;
        return $fields;
    }
}

?>