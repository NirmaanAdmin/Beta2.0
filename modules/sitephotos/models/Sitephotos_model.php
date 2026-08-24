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
        $default_project = get_default_project();
        $upload_path = SITEPHOTOS_TIMELINE_UPLOAD_PATH;
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0755, true)) {
                return 0;
            }
        }
        $areas = $this->input->post('area');
        $rfis = $this->input->post('rfi');
        $drawings = $this->input->post('drawing');
        $areas = is_array($areas) ? $areas : [];
        $rfis = is_array($rfis) ? $rfis : [];
        $drawings = is_array($drawings) ? $drawings : [];
        $area_value = !empty($areas) ? implode(',', array_map('intval', $areas)) : null;
        $rfi_value = !empty($rfis) ? implode(',', array_map('intval', $rfis)) : null;
        $drawing_value = !empty($drawings) ? implode(',', array_map('intval', $drawings)) : null;
        $title = $this->input->post('title', true);
        $description = $this->input->post('description', true);

        if (!isset($_FILES['files']) || !isset($_FILES['files']['name']) || empty($_FILES['files']['name'])
        ) {
            return 0;
        }
        $count = count($_FILES['files']['name']);
        $uploaded = 0;
        for ($i = 0; $i < $count; $i++) {
            if (empty($_FILES['files']['name'][$i]) || $_FILES['files']['error'][$i] != UPLOAD_ERR_OK
            ) {
                continue;
            }
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
                $photo_title = !empty($title) ? $title : $file['orig_name'];
                $insert_data = [
                    'file_name'     => $file['file_name'],
                    'original_name' => $file['orig_name'],
                    'title'         => $photo_title,
                    'description'   => !empty($description) ? $description : null,
                    'area'          => $area_value,
                    'rfi'           => $rfi_value,
                    'drawing'       => $drawing_value,
                    'project_id'    => $default_project,
                    'uploaded_by'   => get_staff_user_id(),
                    'uploaded_at'   => date('Y-m-d H:i:s'),
                ];
                $this->db->insert(db_prefix() . 'site_timeline_photos', $insert_data);
                if ($this->db->affected_rows() > 0) {
                    $uploaded++;
                }
            }
        }
        return $uploaded;
    }

    public function listing_timeline()
    {
        $default_project = get_default_project();
        $data = $this->input->post();
        $search = !empty($data['search']) ? $data['search'] : NULL;
        $areas = !empty($data['area']) && is_array($data['area']) ? $data['area'] : [];
        $rfis = !empty($data['rfi']) && is_array($data['rfi']) ? $data['rfi'] : [];
        $drawings = !empty($data['drawing']) && is_array($data['drawing']) ? $data['drawing'] : [];

        $this->db->where('project_id', $default_project);
        if (!empty($search)) {
            $this->db->group_start()
            ->like('title', $search)
            ->or_like('original_name', $search)
            ->group_end();
        }
        if (!empty($areas)) {
            $this->db->group_start();
            foreach ($areas as $area) {
                $this->db->or_where('FIND_IN_SET(' . $this->db->escape($area) . ', area) > 0', NULL, FALSE);
            }
            $this->db->group_end();
        }
        if (!empty($rfis)) {
            $this->db->group_start();
            foreach ($rfis as $rfi) {
                $this->db->or_where('FIND_IN_SET(' . $this->db->escape($rfi) . ', rfi) > 0', NULL, FALSE);
            }
            $this->db->group_end();
        }
        if (!empty($drawings)) {
            $this->db->group_start();
            foreach ($drawings as $drawing) {
                $this->db->or_where('FIND_IN_SET(' . $this->db->escape($drawing) . ', drawing) > 0', NULL, FALSE);
            }
            $this->db->group_end();
        }
        $this->db->order_by('uploaded_at', 'DESC');
        return $this->db->get(db_prefix() . 'site_timeline_photos')->result_array();
    }

    public function get_timeline($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'site_timeline_photos')->row();
    }

    public function get_timeline_detail($id)
    {
        $this->db->where('id', (int) $id);
        $timeline = $this->db->get(db_prefix() . 'site_timeline_photos')->row();
        if (!$timeline) {
            return null;
        }
        $timeline->uploaded_on = !empty($timeline->uploaded_at) ? date('d M, Y h:i A', strtotime($timeline->uploaded_at)) : '';
        $timeline->uploaded_by_name = !empty($timeline->uploaded_by) ? get_last_action_full_name($timeline->uploaded_by) : '';
        return $timeline;
    }

    public function update_timeline($id)
    {
        $input = $this->input->post();
        $areas = !empty($input['edit_area']) && is_array($input['edit_area']) ? $input['edit_area'] : [];
        $rfis = !empty($input['edit_rfi']) && is_array($input['edit_rfi']) ? $input['edit_rfi'] : [];
        $drawings = !empty($input['edit_drawing']) && is_array($input['edit_drawing']) ? $input['edit_drawing'] : [];
        $data = [
            'title' => !empty($input['edit_title']) ? $input['edit_title'] : NULL,
            'description' => !empty($input['edit_description']) ? $input['edit_description'] : NULL,
            'area' => !empty($areas) ? implode(',', $areas) : NULL,
            'rfi' => !empty($rfis) ? implode(',', $rfis) : NULL,
            'drawing' => !empty($drawings) ? implode(',', $drawings) : NULL,
        ];
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'site_timeline_photos', $data);
        return $this->db->affected_rows() > 0;
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

    public function get_timeline_comments($photo_id)
    {
        $current_staff_id = (int) get_staff_user_id();
        $this->db->select('
            c.id,
            c.timeline_photo_id,
            c.staffid,
            c.comment,
            c.created_at,
            c.updated_at,
            s.firstname,
            s.lastname
        ');
        $this->db->from(db_prefix() . 'site_timeline_photo_comments c');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = c.staffid', 'left');
        $this->db->where('c.timeline_photo_id', (int) $photo_id);
        $this->db->order_by('c.created_at', 'ASC');
        $comments = $this->db->get()->result();
        foreach ($comments as &$comment) {
            $comment->staff_name = trim($comment->firstname . ' ' . $comment->lastname);
            $comment->can_edit = ((int) $comment->staffid === $current_staff_id);
            $comment->can_delete = ((int) $comment->staffid === $current_staff_id);
            $comment->created_on = !empty($comment->created_at) ? date('d M, Y h:i A', strtotime($comment->created_at)) : '';
            $comment->updated_on = !empty($comment->updated_at) ? date('d M, Y h:i A', strtotime($comment->updated_at)) : '';
        }
        return $comments;
    }

    public function get_timeline_comment($comment_id)
    {
        $this->db->where('id', (int) $comment_id);
        return $this->db->get(db_prefix() . 'site_timeline_photo_comments')->row();
    }

    public function add_timeline_comment($data)
    {
        $this->db->insert(db_prefix() . 'site_timeline_photo_comments', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_timeline_comment($comment_id, $data)
    {
        $this->db->where('id', (int) $comment_id);
        $this->db->update(db_prefix() . 'site_timeline_photo_comments', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete_timeline_comment($comment_id)
    {
        $this->db->where('id', (int) $comment_id);
        $this->db->delete(db_prefix() . 'site_timeline_photo_comments');
        return $this->db->affected_rows() > 0;
    }

    public function get_rfis()
    {
        $default_project = get_default_project();
        $this->db->select('ticketid, subject');
        $this->db->where('project_id', $default_project);
        $this->db->order_by('ticketid', 'DESC');
        return $this->db->get(db_prefix() . 'tickets')->result_array();
    }
}

?>