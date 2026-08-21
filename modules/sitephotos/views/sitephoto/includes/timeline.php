<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link href="<?php echo module_dir_url(SITEPHOTOS_MODULE_NAME, 'assets/css/timeline.css'); ?>?v=<?php echo SITEPHOTO_REVISION; ?>" rel="stylesheet" type="text/css"/>

<div class="row mtop20">
    <div class="col-md-3">
        <?php echo render_input('timeline_search', '', '', '', ['placeholder' => 'Search photos...']); ?>
    </div>
    <div class="col-md-3 form-group">
        <?php
        $timeline_interval = [
            ['id' => 'day', 'name' => _l('Day')],
            ['id' => 'week', 'name' => _l('Week')],
            ['id' => 'month', 'name' => _l('Month')],
            ['id' => 'custom_range', 'name' => _l('Custom Range')],
        ];
        echo render_select('timeline_interval', $timeline_interval, ['id', 'name'], '', 'month', ['data-width' => '100%', 'data-none-selected-text' => _l('Select Interval')], [], 'no-mbot', '', false);
        ?>
    </div>
    <div class="col-md-2 form-group custom_range_field hide">
        <?php echo render_date_input('timeline_from_date', '', '', ['placeholder' => 'From Date']); ?>
    </div>
    <div class="col-md-2 form-group custom_range_field hide">
        <?php echo render_date_input('timeline_to_date', '', '', ['placeholder' => 'To Date']); ?>
    </div>
    <div class="col-md-2 form-group">
        <button type="button" class="btn btn-primary" id="upload_timeline_photos">
            <i class="fa fa-upload"></i> Upload Photos
        </button>
    </div>
</div>

<div class="row mtop20">
    <div class="col-md-12">
        <div class="selection_toolbar hide">
            <span><strong id="selected_count">0</strong> selected</span>
            <div>
                <button type="button" class="btn btn-default btn-sm" id="download_selected">
                    <i class="fa fa-download"></i> Download
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="delete_selected">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mtop20">
    <div class="col-md-12">
        <div id="gallery"></div>
    </div>
    <div class="col-md-12">
        <div class="empty_gallery hide">
            <i class="fa fa-picture-o"></i>
            <h4>No photos found</h4>
            <p>Upload your first site photo to start the timeline.</p>
        </div>
    </div>
</div>

<div class="modal fade" id="upload_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="upload_form" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upload Site Photos</h4>
                </div>
                <div class="modal-body">
                    <div class="dropzone" id="dropzone">
                        <i class="fa fa-cloud-upload fa-3x"></i>
                        <h4>Drag & Drop photos here</h4>
                        <p>or click to select files</p>
                        <input type="file" name="files[]" id="files" multiple accept="image/*">
                    </div>
                    <div id="file_preview" class="row mtop15"></div>
                    <div class="row mtop15">
                        <div class="col-md-6">
                            <?php echo render_input('title', _l('Title'), '', 'text', array('placeholder' => 'Photo title')); ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo render_textarea('description', _l('Description'), '', array('rows' => 3)); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="view_modal">
    <div class="modal-dialog" role="document" style="width: 98%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="view_title"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <h4>File Information</h4>
                        <hr>
                        <form id="edit_form">
                            <input type="hidden" id="edit_id">

                            <div class="mtop10">
                                <?php echo render_input('edit_title', _l('Title'), '', 'text', array('placeholder' => 'Photo title')); ?>
                            </div>

                            <div class="mtop15">
                                <?php echo render_textarea('edit_description', _l('Description'), '', array('rows' => 5)); ?>
                            </div>

                            <div class="mtop15 file_meta">
                                <p><strong>Uploaded On:</strong></p>
                                <p><span id="uploaded_on">-</span></p>
                            </div>

                            <div class="mtop10 file_meta">
                                <p><strong>Uploaded By:</strong></p>
                                <p><span id="uploaded_by_name">-</span></p>
                            </div>

                            <div class="mtop20">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a id="single_download" href="#" class="btn btn-default">
                                    <i class="fa fa-download"></i> Download
                                </a>
                                <a id="single_delete" href="#" class="btn btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-center">
                        <img id="view_image" src="" class="img-responsive view_image">
                    </div>
                    <div class="col-md-4">
                        <h4>Comments</h4>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>