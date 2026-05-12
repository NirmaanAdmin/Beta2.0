<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Costplanning_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_commodity_group_type($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'items_groups')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from tblitems_groups')->result_array();
        }
    }

    public function get_sub_group($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'wh_sub_group')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from tblwh_sub_group')->result_array();
        }
    }

    public function get_area($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'area')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from tblarea')->result_array();
        }
    }

    public function get_master_area($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'master_area')->row();
        } else {
            return $this->db->get(db_prefix() . 'master_area')->result_array();
        }
    }

    public function add_master_area($data)
    {
        $this->db->insert(db_prefix() . 'master_area', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    public function update_master_area($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'master_area', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function delete_master_area($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'master_area');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function get_functionality_area($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'functionality_area')->row();
        } else {
            return $this->db->get(db_prefix() . 'functionality_area')->result_array();
        }
    }

    public function add_functionality_area($data)
    {
        $this->db->insert(db_prefix() . 'functionality_area', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    public function update_functionality_area($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'functionality_area', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function delete_functionality_area($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'functionality_area');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function get_master_area_dropdown($name, $value)
    {
        $select = '';
        $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="'.$name.'" data-none-selected-text="' . _l('master_area') . '">';
        $select .= '<option value=""></option>';
        $master_area = $this->get_master_area();
        foreach ($master_area as $area) {
            $selected = ($area['id'] == $value) ? ' selected' : '';
            $select .= '<option value="' . $area['id'] . '"' . $selected . '>' . $area['category_name'] . '</option>';

        }
        $select .= '</select>';
        return $select;
    }

    public function get_functionality_area_dropdown($name, $value)
    {
        $select = '';
        $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="'.$name.'" data-none-selected-text="' . _l('functionality_area') . '">';
        $select .= '<option value=""></option>';
        $functionality_area = $this->get_functionality_area();
        foreach ($functionality_area as $area) {
            $selected = ($area['id'] == $value) ? ' selected' : '';
            $select .= '<option value="' . $area['id'] . '"' . $selected . '>' . $area['category_name'] . '</option>';

        }
        $select .= '</select>';
        return $select;
    }

    public function get_units($id = '')
    {
        if ($id != '') {
            $this->db->where('unit_type_id', $id);
            return $this->db->get(db_prefix() . 'ware_unit_type')->row();
        } else {
            return $this->db->get(db_prefix() . 'ware_unit_type')->result_array();
        }
    }

    public function get_area_unit_dropdown($name, $value)
    {
        $select = '';
        $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="'.$name.'" data-none-selected-text="' . _l('unit') . '">';
        $select .= '<option value=""></option>';
        $units = $this->get_units();
        foreach ($units as $unit) {
            $selected = ($unit['unit_type_id'] == $value) ? ' selected' : '';
            $select .= '<option value="' . $unit['unit_type_id'] . '"' . $selected . '>' . $unit['unit_name'] . '</option>';

        }
        $select .= '</select>';
        return $select;
    }

    public function get_area_statement_tabs($id)
    {
        $this->db->where('estimate_id', $id);
        return $this->db->get(db_prefix() . 'area_statement_tabs')->result_array();
    }

    public function get_area_summary_tabs()
    {
        return $this->db->get(db_prefix() . 'area_summary_tabs')->result_array();
    }

    public function get_sub_head_dropdown($name, $value)
    {
        $sub_head = $this->get_sub_group();
        return render_select($name, $sub_head, array('id', 'sub_group_name'), '', $value);
    }
}

?>