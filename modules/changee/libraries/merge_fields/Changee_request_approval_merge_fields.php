<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Changee_request_approval_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Link',
                'key'       => '{link}',
                'available' => [
                    
                ],
                'templates' => [
                    'changee-request-approval',
                ],
            ],
            [
                'name'      => 'From staff',
                'key'       => '{from_staff_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'changee-request-approval',
                ],
            ],
            [
                'name'      => 'Staff name',
                'key'       => '{staff_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'changee-request-approval',
                ],
            ],
            [
                'name'      => 'Changee Request/Pruchase Order/Quotation/Payment Request',
                'key'       => '{type}',
                'available' => [
                    
                ],
                'templates' => [
                    'changee-request-approval',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $data 
     * @return array
     */
    public function format($data)
    {
       
        $fields['{link}']                  = $data->link;
        $fields['{from_staff_name}']                  =  $data->from_staff_name;
        $fields['{staff_name}']                  =  $data->staff_name;
        $fields['{type}']                   =  $data->type;
       

        return $fields;
    }
}
