<?php

$folder = 'files';

$route = admin_url('drawing_management') . '?id=' . $file->id;

$tokenName = $this->security->get_csrf_token_name();
$token = $this->security->get_csrf_hash();

$src =
    DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER .
    '/' .
    $folder .
    '/' .
    $file->parent_id .
    '/' .
    $file->name;

$backup =
     DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER .
        '/original_files/' .
        $file->parent_id . '/' .$file->name;

if (file_exists($backup)) {

    if (file_exists($src)) {
        unlink($src);
    }

    if (copy($backup, $src)) {

        // Update DB
        $CI = &get_instance();

        $CI->db
            ->where('id', $file->id)
            ->update(
                'tbldms_items',
                [
                    'superseder' => 0
                ]
            );

        // Remove backup
        unlink($backup);

        // Open restored PDF
        $url = base_url(
            DRAWING_MANAGEMENT_PATH .
            $folder .
            '/' .
            $file->parent_id .
            '/' .
            $file->name
        );
?>

<iframe
    src="<?= base_url(
        'pdfjs/web/viewer.html?file=' .
        urlencode($url) .
        '&name=' .
        urlencode($file->name) .
        '&folder=' .
        $folder .
        '&parent_id=' .
        $file->parent_id
    ) .
    '&back_route=' .
    urlencode($route) .
    '&token_name=' .
    urlencode($tokenName) .
    '&csrf_token=' .
    urlencode($token) .
    '&base_url=' .
    urlencode(base_url()) ?>"
    width="100%"
    height="100%">
</iframe>

<?php

    } else {

        echo '<div class="alert alert-danger">
                Failed to restore original file.
              </div>';
    }

} else {

    echo '<div class="alert alert-warning">
            Backup file not found.
          </div>';
}

?>