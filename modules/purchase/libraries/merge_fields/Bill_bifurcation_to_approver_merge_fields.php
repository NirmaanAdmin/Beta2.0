<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bill_bifurcation_to_approver_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contact firstname',
                'key'       => '{contact_firstname}',
                'available' => [
                    
                ],
                'templates' => [
                    'bill-bifurcation-to-approver',
                ],
            ],
            [
                'name'      => 'Contact lastname',
                'key'       => '{contact_lastname}',
                'available' => [
                    
                ],
                'templates' => [
                    'bill-bifurcation-to-approver',
                ],
            ],
            [
                'name'      => 'Bill bifurcation link',
                'key'       => '{bill_bifurcation_link}',
                'available' => [
                    
                ],
                'templates' => [
                    'bill-bifurcation-to-approver',
                ],
            ],
            [
                'name'      => 'Bill bifurcation title',
                'key'       => '{bill_bifurcation_title}',
                'available' => [
                    
                ],
                'templates' => [
                    'bill-bifurcation-to-approver',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($data)
    {
        $id = $data->pb_id;
        $this->ci->load->model('purchase/purchase_model');

        $fields = [];

        $this->ci->db->where('id', $id);
        $pc = $this->ci->db->get(db_prefix() . 'pur_bills')->row();

        if (!$pc) {
            return $fields;
        }

        $fields['{contact_firstname}'] =  $data->contact_firstname;
        $fields['{contact_lastname}'] =  $data->contact_lastname;
        $fields['{bill_bifurcation_title}'] = site_url('purchase/edit_pur_bills/'.$pc->id);
        $fields['{bill_bifurcation_link}'] = site_url('purchase/edit_pur_bills/'.$pc->id);

        return $fields;
    }
}
