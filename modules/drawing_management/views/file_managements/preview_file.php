<?php
$folder = 'files';
$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER.'/'.$folder.'/'.$file->parent_id.'/'.$file->name;
if(is_image($path)){ ?>
   <img src="<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name); ?>" class="img img-responsive img_style">
<?php } else if(!empty($file->external) && !empty($file->thumbnail_link)){ ?>
   <img src="<?php echo optimize_dropbox_thumbnail($file->thumbnail_link); ?>" class="img img-responsive">
<?php } else if(strpos($file->name,'.pdf') !== false && empty($file->external)){ ?>
<!--   <iframe src="<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name); ?>" height="100%" width="100%" frameborder="0"></iframe>-->
    <?php
    $route = admin_url('drawing_management').'?id='.$file->id;
    $tokenName = $this->security->get_csrf_token_name();
    $token = $this->security->get_csrf_hash();
    ?>
    <iframe src="<?= base_url('pdfjs/web/viewer.html?file=' . base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name).'&name='.$file->name.'&folder='.$folder.'&parent_id='.$file->parent_id).'&back_route='.$route.'&token_name='.$tokenName.'&csrf_token='.$token.'&base_url='.base_url() ?>" width="100%" height="100%"></iframe>
<?php } else if(strpos($file->name,'.xls') !== false && empty($file->external)){ ?>
   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
   </iframe>
<?php } else if(strpos($file->name,'.xlsx') !== false && empty($file->external)){ ?>
   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
   </iframe>
<?php } else if(strpos($file->name,'.doc') !== false && empty($file->external)){ ?>
   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
   </iframe>
<?php } else if(strpos($file->name,'.docx') !== false && empty($file->external)){ ?>
   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
   </iframe>
<?php } else if(is_html5_video($path)) { ?>
   <video width="100%" height="100%" src="<?php echo site_url('download/preview_video?path='.protected_file_url_by_path($path).'&type='.$file->filetype); ?>" controls>
      Your browser does not support the video tag.
   </video>
<?php } else if(is_markdown_file($path) && $previewMarkdown = markdown_parse_preview($path)) {
   echo drawing_htmldecode($previewMarkdown);
} else if(strpos($file->name,'.dwg') !== false && empty($file->external)) {
    $rand = substr(uniqid('', true), -8);
    ?>
   <div style="position: relative; height: 100vh;">
      <iframe id="cadViewer"
           src="https://sharecad.org/cadframe/load?url=<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name); ?>?v=<?php echo $rand; ?>"
           style="width: 100%; height: 100%; border: none;"
           allowfullscreen
           webkitallowfullscreen
           mozallowfullscreen>
      </iframe>
   </div>
<?php } else if(strpos($file->name,'.dxf') !== false && empty($file->external)) {
    $rand = substr(uniqid('', true), -8);
    ?>
   <div style="position: relative; height: 100vh;">
      <iframe id="cadViewer"
           src="https://sharecad.org/cadframe/load?url=<?php echo base_url(DRAWING_MANAGEMENT_PATH.$folder.'/'.$file->parent_id.'/'.$file->name); ?>?v=<?php echo $rand; ?>"
           style="width: 100%; height: 100%; border: none;"
           allowfullscreen
           webkitallowfullscreen
           mozallowfullscreen>
      </iframe>
   </div>
<?php } else {
   echo '<p class="text-muted">'._l('no_preview_available_for_file').'</p>';
} ?>
