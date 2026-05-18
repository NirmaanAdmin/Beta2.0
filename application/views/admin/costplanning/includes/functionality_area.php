<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_vendor_cate(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new'); ?>
    </a>
</div>
<div class="clearfix"></div>

<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-3">
        <?php echo form_open_multipart(admin_url('estimates/import_file_xlsx_functionality_area'), array('id' => 'import_form')); ?>
        <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>
        <div class="form-group">
          <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfunctionalityareafilecsv(this);"><?php echo _l('import'); ?></button>
          <a href="<?php echo site_url('uploads/estimates/file_sample/Sample_functionality_area_en.xlsx') ?>" class="btn btn-primary">Template</a>
        </div>
        <?php echo form_close(); ?>
        <div class="form-group" id="file_upload_response" style="padding-left: 20px;">
        </div>
    </div>
</div>

<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('name'); ?></th>
    <th><?php echo _l('description'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
  <?php foreach($functionality_area as $key => $vc){ ?>
    <tr>
      <td><?php echo $key + 1; ?></td>
      <td><?php echo pur_html_entity_decode($vc['category_name']); ?></td>
      <td><?php echo pur_html_entity_decode($vc['description']); ?></td>
      <td>
        <a href="#" onclick="edit_vendor_cate(this,<?php echo pur_html_entity_decode($vc['id']); ?>); return false" data-name="<?php echo pur_html_entity_decode($vc['category_name']); ?>" data-description="<?php echo pur_html_entity_decode($vc['description']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>

          <a href="<?php echo admin_url('costplanning/delete_functionality_area/' . $vc['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
      </td>
    </tr>
  <?php } ?>
 </tbody>
</table>
<div class="modal fade" id="vendor_cate" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('costplanning/functionality_area')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_functionality_area'); ?></span>
                    <span class="add-title"><?php echo _l('new_functionality_area'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_vendor_cate"></div>
                     <div class="form">
                        <?php echo render_input('category_name', 'name'); ?>

                        <?php echo render_textarea('description', 'description', '') ?>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
</body>
</html>
