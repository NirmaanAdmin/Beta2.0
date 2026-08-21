<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sitephotos_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function upload_timeline()
    {
        $upload_path = SITEPHOTOS_TIMELINE_UPLOAD_PATH;
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }
        $count = count($_FILES['files']['name']);
        $uploaded = 0;
        for ($i = 0; $i < $count; $i++) {
            $_FILES['single_file'] = [
                'name'     => $_FILES['files']['name'][$i],
                'type'     => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error'    => $_FILES['files']['error'][$i],
                'size'     => $_FILES['files']['size'][$i],
            ];
            $config = [
                'upload_path'   => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif|webp',
                'max_size'      => 10240,
                'encrypt_name'  => true,
            ];
            $this->load->library('upload');
            $this->upload->initialize($config);
            if ($this->upload->do_upload('single_file')) {
                $file = $this->upload->data();
                $this->db->insert(db_prefix() . 'site_timeline_photos', [
                    'file_name'     => $file['file_name'],
                    'original_name' => $file['orig_name'],
                    'title'         => $this->input->post('title', true) ?: $file['orig_name'],
                    'description'   => $this->input->post('description', true),
                    'uploaded_by'   => get_staff_user_id(),
                    'uploaded_at'   => date('Y-m-d H:i:s'),
                ]);
                $uploaded++;
            }
        }
        return $uploaded;
    }

    public function listing_timeline()
    {
        $data = $this->input->post();
        $search = !empty($data['search']) ? $data['search'] : NULL;
        if (!empty($search)) {
            $this->db->group_start()
            ->like('title', $search)
            ->or_like('original_name', $search)
            ->group_end();
        }
        return $this->db
        ->order_by('uploaded_at', 'DESC')
        ->get(db_prefix() . 'site_timeline_photos')
        ->result_array();
    }

    public function get_timeline($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'site_timeline_photos')->row();
    }

    public function get_timeline_detail($id)
    {
        $this->db->where('id', $id);
        $timeline = $this->db->get(db_prefix() . 'site_timeline_photos')->row();
        $timeline->uploaded_on = !empty($timeline->uploaded_at) ? date('d M, Y h:i A',strtotime($timeline->uploaded_at)) : '';
        $timeline->uploaded_by_name = !empty($timeline->uploaded_by) ? get_last_action_full_name($timeline->uploaded_by) : '';
        return $timeline;
    }

    public function update_timeline($id)
    {
        $input = $this->input->post();
        $data['title'] = !empty($input['title']) ? $input['title'] : NULL;
        $data['description'] = !empty($input['description']) ? $input['description'] : NULL;
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'site_timeline_photos', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function delete_timeline($ids)
    {
        if (empty($ids)) {
            return false;
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function ($id) {
            return $id > 0;
        });
        $ids = array_unique($ids);
        if (empty($ids)) {
            return false;
        }
        $this->db->where_in('id', $ids);
        $photos = $this->db->get(db_prefix() . 'site_timeline_photos')->result_array();
        if (empty($photos)) {
            return false;
        }
        foreach ($photos as $photo) {
            if (!empty($photo['file_name'])) {
                $path = SITEPHOTOS_TIMELINE_UPLOAD_PATH . $photo['file_name'];
                if (file_exists($path) && is_file($path)) {
                    unlink($path);
                }
            }
        }
        $this->db->where_in('id', $ids);
        $this->db->delete(db_prefix() . 'site_timeline_photos');
        return $this->db->affected_rows() > 0;
    }
}

?>