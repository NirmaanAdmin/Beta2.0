<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sitephotos extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sitephotos_model');
    }

    public function photos()
    {
        $group = $this->input->get('group', true) ?: 'timeline';

        $allowed = ['timeline', 'albums', 'recycle_bin'];
        if (!in_array($group, $allowed, true)) {
            $group = 'timeline';
        }

        $data['group'] = $group;
        $data['title'] = _l('Site Photos');
        $data['tab'][] = 'timeline';
        $data['tab'][] = 'albums';
        $data['tab'][] = 'recycle_bin';
        $data['tabs']['view'] = 'sitephoto/includes/' . $group;

        if($group == 'timeline') {
            $this->load->model('tickets_model');
            $this->load->model('purchase/purchase_model');
            $data['rfis'] = $this->sitephotos_model->get_rfis();
            $data['drawings'] = $this->tickets_model->get_dms_items();
            $data['staffs'] = $this->staff_model->get();
            $data['vendors'] = $this->purchase_model->get_vendor('', db_prefix() . 'pur_contacts.id IS NOT NULL');
        }

        $this->load->view('sitephoto/manage_setting', $data);
    }

    public function upload_timeline()
    {
        if (empty($_FILES['files']['name'][0])) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one file.']);
            return;
        }
        $uploaded = $this->sitephotos_model->upload_timeline();
        echo json_encode([
            'success' => $uploaded > 0,
            'uploaded' => $uploaded,
            'message' => $uploaded . ' photo(s) uploaded successfully.',
        ]);
    }

    public function listing_timeline()
    {
        $items = $this->sitephotos_model->listing_timeline();
        $html = $this->load->view('sitephoto/includes/timeline_items.php', [
            'photos' => $items,
        ], true);
        echo json_encode([
            'success' => true,
            'html'    => $html,
            'count'   => count($items),
        ]);
    }

    public function get_timeline_detail()
    {
        $id = (int) $this->input->post('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid photo ID.'
            ]);
            return;
        }
        $timeline = $this->sitephotos_model->get_timeline_detail($id);
        if (!$timeline) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo not found.'
            ]);
            return;
        }
        echo json_encode([
            'success' => true,
            'data'    => $timeline
        ]);
    }

    public function update_timeline()
    {
        $id = (int) $this->input->post('edit_id');
        $updated = $this->sitephotos_model->update_timeline($id);
        echo json_encode([
            'success' => $updated,
            'message' => $updated ? 'Photo information updated.' : 'Unable to update photo.',
        ]);
    }

    public function delete_timeline()
    {
        $ids = $this->input->post('ids');
        if (empty($ids)) {
            echo json_encode([
                'success' => false,
                'message' => 'No photos selected.',
            ]);
            return;
        }
        $deleted = $this->sitephotos_model->delete_timeline($ids);
        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? 'Selected photo(s) deleted successfully.' : 'Nothing was deleted.',
        ]);
    }

    public function download_timeline($id)
    {
        $photo = $this->sitephotos_model->get_timeline((int) $id);
        if (!$photo) {
            show_404();
        }
        $path = SITEPHOTOS_TIMELINE_UPLOAD_PATH.$photo->file_name;
        if (!is_file($path)) {
            show_404();
        }
        $this->load->helper('download');
        force_download($photo->original_name, file_get_contents($path));
    }

    public function get_timeline_comments()
    {
        $photo_id = (int) $this->input->post('photo_id');
        if (!$photo_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid photo ID.',
                'data'    => []
            ]);
            return;
        }
        $comments = $this->sitephotos_model->get_timeline_comments($photo_id);
        echo json_encode([
            'success' => true,
            'data'    => $comments
        ]);
    }

    public function add_timeline_comment()
    {
        $photo_id = (int) $this->input->post('photo_id');
        $comment  = trim($this->input->post('comment', false));
        if (!$photo_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid photo ID.'
            ]);
            return;
        }
        if ($comment === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter a comment.'
            ]);
            return;
        }
        $photo = $this->sitephotos_model->get_timeline_detail($photo_id);
        if (!$photo) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo not found.'
            ]);
            return;
        }
        $data = [
            'timeline_photo_id' => $photo_id,
            'staffid'            => get_staff_user_id(),
            'comment'            => $comment,
            'created_at'         => date('Y-m-d H:i:s'),
        ];
        $comment_id = $this->sitephotos_model->add_timeline_comment($data);
        if ($comment_id) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment added successfully.',
                'id'      => $comment_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to add comment.'
            ]);
        }
    }

    public function update_timeline_comment()
    {
        $comment_id = (int) $this->input->post('comment_id');
        $comment    = trim($this->input->post('comment', false));
        if (!$comment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid comment ID.'
            ]);
            return;
        }
        if ($comment === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter a comment.'
            ]);
            return;
        }
        $existing_comment = $this->sitephotos_model->get_timeline_comment($comment_id);
        if (!$existing_comment) {
            echo json_encode([
                'success' => false,
                'message' => 'Comment not found.'
            ]);
            return;
        }
        if ((int) $existing_comment->staffid !== (int) get_staff_user_id()) {
            echo json_encode([
                'success' => false,
                'message' => 'You are not allowed to edit this comment.'
            ]);
            return;
        }
        $updated = $this->sitephotos_model->update_timeline_comment(
            $comment_id, [
                'comment'    => $comment,
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment updated successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to update comment.'
            ]);
        }
    }

    public function delete_timeline_comment()
    {
        $comment_id = (int) $this->input->post('comment_id');
        if (!$comment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid comment ID.'
            ]);
            return;
        }
        $existing_comment = $this->sitephotos_model->get_timeline_comment($comment_id);
        if (!$existing_comment) {
            echo json_encode([
                'success' => false,
                'message' => 'Comment not found.'
            ]);
            return;
        }
        if ((int) $existing_comment->staffid !== (int) get_staff_user_id()) {
            echo json_encode([
                'success' => false,
                'message' => 'You are not allowed to delete this comment.'
            ]);
            return;
        }
        $deleted = $this->sitephotos_model->delete_timeline_comment($comment_id);
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to delete comment.'
            ]);
        }
    }

    public function get_primary_vendors()
    {
        $response = '';
        if (!empty($this->input->post('vendor'))) {
            $data = $this->input->post('vendor');
            $response = $this->sitephotos_model->get_primary_vendors($data);
        }
        echo $response;
    }

    public function share_timeline()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $result = $this->sitephotos_model->share_timeline($data);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email sent successfully.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send email.'
                ]);
            }
            return;
        }
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request.'
        ]);
    }
}

?>