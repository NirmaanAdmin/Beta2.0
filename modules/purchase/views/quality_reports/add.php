<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'new_form_form']); 
        
        ?>
        <input type="hidden" name="form_type" value="qor">
        <?php if(isset($po_id)) { ?>
        <input type="hidden" name="pur_order_id" value="<?php echo $po_id; ?>">
        <?php } ?>
        <?php if(isset($wo_id)) { ?>
        <input type="hidden" name="wo_order_id" value="<?php echo $wo_id; ?>">
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-mr-4">
                        <?php echo _l('Quality Observation Report'); ?>
                    </h4>
                    <?php if (!isset($project_id) && !isset($contact)) { ?>
                        
                        <a href="#" class="hide label label-default" id="form_to_contact">
                            <i class="fa-regular fa-user tw-mr-1"></i>
                            <?php echo _l('form_create_to_contact'); ?>
                        </a>
                    <?php } ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">

                                <?php 
                                $subject = $form_name ?? '';
                                echo render_input('subject', 'form_settings_subject', $subject, 'text', ['required' => 'true']); ?>

                                <div class="form-group projects-wrapper">
                                    <?php
                                    $project_selected = !empty($this->input->get('project_id', TRUE)) ? $this->input->get('project_id', TRUE) : '';
                                    echo render_select('project_id', $projects, array('id','name'), 'project', $project_selected, array('required'=>'true'));
                                    ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_select('department', $departments, ['departmentid', 'name'], 'form_settings_departments', (count($departments) == 1) ? $departments[0]['departmentid'] : $selected_dept); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('cc', 'CC'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                        <?php echo _l('tags'); ?></label>
                                        <input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">
                                    </div>

                                    <div class="form-group select-placeholder">
                                        <label for="assigned" class="control-label">
                                            <?php echo _l('form_settings_assign_to'); ?>
                                        </label>
                                        <select name="assigned" id="assigned" class="form-control selectpicker"
                                        data-live-search="true"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                        data-width="100%" required="true">
                                        <option value=""><?php echo _l('form_settings_none_assigned'); ?></option>
                                        <?php foreach ($staff as $member) { ?>
                                            <option value="<?php echo e($member['staffid']); ?>" <?php if ($member['staffid'] == get_staff_user_id()) {
                                                echo 'selected';
                                            } ?>>
                                            <?php echo e($member['firstname'] . ' ' . $member['lastname']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $priorities['callback_translate'] = 'form_priority_translate';
                                    echo render_select('priority', $priorities, ['priorityid', 'name'], 'form_settings_priority', hooks()->apply_filters('new_form_priority_selected', 2), ['required' => 'true']); ?>
                                </div>

                                <div class="col-md-6">
                                    <?php 
                                    $value = '';
                                    echo render_date_input('duedate', 'task_add_edit_due_date', $value, array('required'=>'true'));
                                    ?>
                                </div>

                                <?php if (get_option('services') == 1) { ?>
                                    <div class="col-md-6 hide">
                                        <?php if (is_admin() || get_option('staff_members_create_inline_form_services') == '1') {
                                            echo render_select_with_input_group('service', $services, ['serviceid', 'name'], 'form_settings_service', '', '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_service();return false;"><i class="fa fa-plus"></i></a></div>');
                                        } else {
                                            echo render_select('service', $services, ['serviceid', 'name'], 'form_settings_service');
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_custom_fields('forms'); ?>
                        </div>

                        <div class="view_form_design">
                            <?php $this->load->view('quality_reports/qoc') ?>
                        </div>

                        <div class="col-md-12">
                            <hr class="hr-panel-separator" />
                        </div>
                        <div class="col-md-12 tw-mt-3">
                            <h4 class="tw-mt-0 tw-font-semibold tw-text-base tw-text-neutral-700">
                                <?php echo _l('form_add_body'); ?>
                            </h4>
                            <div class="row">
                                <div class="col-md-12 mbot20 before-form-message">
                                    <div class="row">
                                        <div class="col-md-6 hide">
                                            <select id="insert_predefined_reply" data-width="100%"
                                            data-live-search="true" class="selectpicker"
                                            data-title="<?php echo _l('form_single_insert_predefined_reply'); ?>">
                                            <?php foreach ($predefined_replies as $predefined_reply) { ?>
                                                <option value="<?php echo e($predefined_reply['id']); ?>">
                                                    <?php echo e($predefined_reply['name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php if (get_option('use_knowledge_base') == 1) { ?>
                                            <div class="visible-xs">
                                                <div class="mtop15"></div>
                                            </div>
                                            <div class="col-md-6 hide">
                                                <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                                <select id="insert_knowledge_base_link" data-width="100%"
                                                class="selectpicker" data-live-search="true"
                                                onchange="insert_form_knowledgebase_link(this);"
                                                data-title="<?php echo _l('form_single_insert_knowledge_base_link'); ?>">
                                                <option value=""></option>
                                                <?php foreach ($groups as $group) { ?>
                                                    <?php if (count($group['articles']) > 0) { ?>
                                                        <optgroup label="<?php echo e($group['name']); ?>">
                                                            <?php foreach ($group['articles'] as $article) { ?>
                                                                <option value="<?php echo e($article['articleid']); ?>">
                                                                    <?php echo e($article['subject']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php echo render_textarea('message', '', '', [], [], '', 'tinymce'); ?>
                        <div class="attachments_area">
                            <div class="row attachments">
                                <div class="attachment">
                                    <div class="col-md-4 col-md-offset-8 mtop10">
                                        <div class="form-group">
                                            <label for="attachment"
                                            class="control-label"><?php echo _l('form_add_attachments'); ?></label>
                                            <div class="input-group">
                                                <input type="file"
                                                extension="<?php echo str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')); ?>"
                                                filesize="<?php echo file_upload_max_size(); ?>"
                                                class="form-control" name="attachments[0]"
                                                accept="<?php echo get_form_form_accepted_mimes(); ?>">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default add_more_attachments"
                                                    data-max="<?php echo get_option('maximum_allowed_form_attachments'); ?>"
                                                    type="button"><i class="fa fa-plus"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-bottom-toolbar text-right">

                    <button type="submit" data-form="#new_form_form" autocomplete="off"
                    data-loading-text="<?php echo _l('wait_text'); ?>"
                    class="btn btn-primary"><?php echo _l('open_form'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
<div class="tw-py-10"></div>
<?php $this->load->view('admin/forms/services/service'); ?>
<?php init_tail(); ?>
<?php hooks()->do_action('new_form_admin_page_loaded'); ?>
<script>
    $(function(){
        $('#project_id').trigger('change');
        validate_new_form_form();
    });
</script>
</body>

</html>