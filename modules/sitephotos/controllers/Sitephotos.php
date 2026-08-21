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
        $id = $this->input->post('id');
        $timeline = $this->sitephotos_model->get_timeline_detail($id);
        echo json_encode($timeline);
    }

    public function update_timeline($id)
    {
        $updated = $this->sitephotos_model->update_timeline((int) $id);
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
}
