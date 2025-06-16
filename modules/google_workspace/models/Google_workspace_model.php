<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Google_workspace_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get google drive
     * @param  mixed $id        google drive id
     * @return object
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            $google_doc = $this->db->get(db_prefix() . 'google_workspaces')->row();
    
            return $google_doc;
        } else {
            $google_workspaces = $this->db->get(db_prefix() . 'google_workspaces')->result_array();
            return $google_workspaces;
        }
    }

    /**
     * Get google drive
     * @param  mixed $id        google drive id
     * @return object
     */
    public function get_by_driveid($id = '')
    {
        $this->db->where('driveid', $id);
        $google_workspace = $this->db->get(db_prefix() . 'google_workspaces')->row();

        return $google_workspace;
    }

    /**
     * Get and google drives
     * @return object
     */
    public function get_all($type = '')
    {
        if (!is_admin()) {
            $this->db->where('staffid', get_staff_user_id());
        }
        if ($type) {
            $this->db->where('type', $type);
        }
        $google_workspaces = $this->db->get(db_prefix() . 'google_workspaces')->result_array();
        return $google_workspaces;
    }

    /**
     * Update google drive
     * @param  array $data      google drive $_POST data
     * @param  mixed $id        google drive id
     * @return boolean
     */
    public function update($data, $id)
    {
        $this->db->where('id', $id);
        $update_data = [];
        $update_data['title'] = $data['title'];
        if (isset($data['status'])) {
            $update_data['status'] = $data['status'];
        }
        if (isset($data['description'])) {
            $update_data['description'] = $data['description'];
        }
        $this->db->update(db_prefix() . 'google_workspaces', $update_data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Google Drive Updated [ID: ' . $id . ', Title: ' . $data['title'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Add new google drive
     * @param array $data google drive $_POST data
     * @return mixed
     */
    public function add($data)
    {
        $date_created = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'google_workspaces', [
            'staffid'           => $data['staffid'],
            'driveid'           => $data['driveid'],
            'title'             => $data['title'],
            'description'       => isset($data['description']) ? $data['description'] : '',
            'type'              => $data['type'],
            'status'            => $data['status'],
            'date'              => $date_created
        ]);
        $google_workspace_id = $this->db->insert_id();
        if (!$google_workspace_id) {
            log_activity('New Google Drive Added [ID: ' . $google_workspace_id . ', Title: ' . $data['title'] . ']');
        }

        return $google_workspace_id;
    }

    /**
     * Delete google drive
     * @param  mixed $id google drive id
     * @return boolean
     */
    public function delete($id)
    {
        $affectedRows = 0;
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'google_workspaces');
        if ($affectedRows > 0) {
            log_activity('Google Drive Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
}