<?php

use setasign\Fpdi\Fpdi;

$folder = 'files';
$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/' . $folder . '/' . $file->parent_id . '/' . $file->name;

if (is_image($path)) { ?>

    <img
        src="<?= base_url(DRAWING_MANAGEMENT_PATH . $folder . '/' . $file->parent_id . '/' . $file->name); ?>"
        class="img img-responsive img_style">

<?php } elseif (!empty($file->external) && !empty($file->thumbnail_link)) { ?>

    <img
        src="<?= optimize_dropbox_thumbnail($file->thumbnail_link); ?>"
        class="img img-responsive">

<?php } elseif (strpos(strtolower($file->name), '.pdf') !== false && empty($file->external)) {

    $route = admin_url('drawing_management') . '?id=' . $file->id;

    $tokenName = $this->security->get_csrf_token_name();
    $token = $this->security->get_csrf_hash();

    require_once(APPPATH . 'third_party/fpdf/fpdf.php');
    require_once(APPPATH . 'third_party/fpdi/autoload.php');

    if (!function_exists('stampFooter')) {

        function stampFooter($srcPath, $destPath)
        {
            try {

                $pdf = new Fpdi();

                $pageCount = $pdf->setSourceFile($srcPath);

                for ($page = 1; $page <= $pageCount; $page++) {

                    $tpl = $pdf->importPage($page);

                    $size = $pdf->getTemplateSize($tpl);

                    $pdf->AddPage(
                        $size['orientation'],
                        [$size['width'], $size['height']]
                    );

                    $pdf->useTemplate($tpl);

                    $footer =
                        FCPATH .
                        'assets/images/pdf-footer-logo.png';

                    if (file_exists($footer)) {

                        $targetWidth = 50;

                        list($w, $h) =
                            getimagesize($footer);

                        $targetHeight =
                            ($h / $w) * $targetWidth;

                        $x =
                            ($size['width'] - $targetWidth) / 2;

                        $y =
                            $size['height'] -
                            $targetHeight -
                            10;

                        $pdf->Image(
                            $footer,
                            $x,
                            $y,
                            $targetWidth,
                            $targetHeight
                        );
                    }
                }

                $pdf->Output($destPath, 'F');

                return true;
            } catch (Exception $e) {

                log_message(
                    'error',
                    'PDF stamp failed: ' .
                        $e->getMessage()
                );

                return false;
            }
        }
    }

    $src =
        DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER .
        '/' .
        $folder .
        '/' .
        $file->parent_id .
        '/' .
        $file->name;

    $tempStamped =
        DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER .
        '/' .
        $folder .
        '/' .
        $file->parent_id .
        '/temp_' .
        $file->name;

    /*
    |--------------------------------------------------------------------------
    | Backup original PDF
    |--------------------------------------------------------------------------
    */

    $backupDir =
        DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER .
        '/original_files/' .
        $file->parent_id;

    $backupFile =
        $backupDir .
        '/' .
        $file->name;

    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    if (
        file_exists($src) &&
        !file_exists($backupFile)
    ) {
        copy($src, $backupFile);
    }

    /*
    |--------------------------------------------------------------------------
    | Stamp PDF
    |--------------------------------------------------------------------------
    */

    if (
        !file_exists($tempStamped) ||
        filemtime($tempStamped) < filemtime($src)
    ) {

        stampFooter($src, $tempStamped);

        if (file_exists($tempStamped)) {

            // Delete original
            if (file_exists($src)) {
                unlink($src);
            }

            // Replace original with stamped PDF
            if (rename($tempStamped, $src)) {

                // Update superseder = 1
                $CI = &get_instance();

                $CI->db->where('id', $file->id);

                $CI->db->update(
                    'tbldms_items',
                    [
                        'superseder' => 1
                    ]
                );
            }
        }
    }

    $url =
        base_url(
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

<?php } else {

    echo '<p class="text-muted">'
        . _l('no_preview_available_for_file')
        . '</p>';
}
?>