<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="_buttons">
    <?php if (has_permission('purchase_settings', '', 'edit') || is_admin() ) { ?>

    <a href="#" onclick="new_sub_group_type(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('add_sub_group_type'); ?>
    </a>
<?php } ?>

</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="row">
    <div class="col-md-3">
        <?php
        echo render_select('select_project', $projects, array('id', 'name'), 'project'); 
        ?>
    </div>
</div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table border sub-group-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('sub_group_code'); ?></th>
    <th><?php echo _l('sub_group_name'); ?></th>
    <th><?php echo _l('group_name'); ?></th>
    <th><?php echo _l('project'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
 </tbody>
</table>   

<div class="modal1 fade" id="sub_group_type" tabindex="-1" role="dialog">
    <div class="modal-dialog setting-handsome-table">
      <?php echo form_open_multipart(admin_url('purchase/sub_group'), array('id'=>'add_sub_group')); ?>
      <?php echo form_hidden('sub_group_type_id'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('add_sub_group_type'); ?></span>
                    <span class="edit-title"><?php echo _l('edit_sub_group'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form">
                            <?php 
                            $budget_head = get_budget_head_project_wise();
                            echo render_input('sub_group_code', 'sub_group_code');
                            echo render_input('sub_group_name', 'sub_group_name');
                            echo render_select('group_id', $budget_head, array('id', 'name'), 'group_name'); 
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

</body>
</html>
