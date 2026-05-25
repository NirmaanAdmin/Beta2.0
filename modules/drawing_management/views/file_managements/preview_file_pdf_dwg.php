<?php
$folder = 'files';
$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/' . $folder . '/' . $file->parent_id . '/' . $file->name;
if (is_image($path)) { ?>
   <img src="<?php echo base_url(DRAWING_MANAGEMENT_PATH . $folder . '/' . $file->parent_id . '/' . $file->name); ?>" class="img img-responsive img_style">
<?php } else if (!empty($file->external) && !empty($file->thumbnail_link)) { ?>
   <img src="<?php echo optimize_dropbox_thumbnail($file->thumbnail_link); ?>" class="img img-responsive">
<?php } else if (strpos($file->pdf_attachment, '.dwg') !== false) { ?>
   <?php
   $route = admin_url('drawing_management') . '?id=' . $file->id;
   $tokenName = $this->security->get_csrf_token_name();
   $token = $this->security->get_csrf_hash();

   if (strpos($file->pdf_attachment, '.dwg') !== false) {
      $rand = substr(uniqid('', true), -8);
   ?>
      <div style="position: relative; height: 100vh;">
         <iframe id="cadViewer"
            src="https://iframe.sharecad.org/cadframe/load?url=<?php echo base_url(DRAWING_MANAGEMENT_PATH . 'pdf_attachments' . '/' . $file->id . '/' . $file->pdf_attachment); ?>?v=<?php echo $rand; ?>"
            style="width: 100%; height: 100%; border: none;"
            allowfullscreen
            webkitallowfullscreen
            mozallowfullscreen>
         </iframe>
      </div>
<?php }
} else {
   echo '<p class="text-muted">' . _l('no_preview_available_for_file') . '</p>';
} ?>