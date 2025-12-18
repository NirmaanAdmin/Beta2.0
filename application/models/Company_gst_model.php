<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Company_gst_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_company_gst($data)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'company_gst_details', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    public function update_company_gst($data, $id)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'company_gst_details', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function delete_company_gst($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'company_gst_details');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function make_default_gst($id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'company_gst_details', [
            'isdefault' => 1,
        ]);
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id !=', $id);
            $this->db->update(db_prefix() . 'company_gst_details', [
                'isdefault' => 0,
            ]);
            return true;
        }
        return false;
    }
}

?>