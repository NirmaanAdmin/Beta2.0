<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'new_ticket_form']); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-mr-4">
                        <?php echo _l('clients_single_ticket_information_heading'); ?>
                    </h4>
                    <?php /*
                    <?php if (!isset($project_id) && !isset($contact)) { ?>
                    <a href="#" id="ticket_no_contact" class="label label-default">
                        <i class="fa-regular fa-envelope tw-mr-1"></i> <?php echo _l('ticket_create_no_contact'); ?>
                    </a>
                    <a href="#" class="hide label label-default" id="ticket_to_contact">
                        <i class="fa-regular fa-user tw-mr-1"></i>
                        <?php echo _l('ticket_create_to_contact'); ?>
                    </a>
                    <?php } ?> */ ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">

                                <?php echo render_input('subject', 'ticket_settings_subject', '', 'text', ['required' => 'true']); ?>

                                <div class="form-group projects-wrapper">
                                    <?php
                                        $project_selected = !empty($this->input->get('project_id', TRUE)) ? $this->input->get('project_id', TRUE) : '';
                                        echo render_select('project_id', $projects, array('id','name'), 'project', $project_selected, array('required'=>'true'));
                                    ?>
                                </div>

                                <?php /*
                                <div class="form-group select-placeholder" id="ticket_contact_w">
                                    <label for="contactid"><?php echo _l('contact'); ?></label>
                                    <select name="contactid" id="contactid" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-actions-box="true" required="true">
                                    </select>
                                    <?php echo form_hidden('userid'); ?>
                                </div> */ ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_input('name', 'ticket_settings_to', '', 'text'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('email', 'ticket_settings_email', '', 'email'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_select('department', $departments, ['departmentid', 'name'], 'ticket_settings_departments', (count($departments) == 1) ? $departments[0]['departmentid'] : ''); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('cc', 'CC'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $priorities['callback_translate'] = 'ticket_priority_translate';
                                        echo render_select('priority', $priorities, ['priorityid', 'name'], 'ticket_settings_priority', hooks()->apply_filters('new_ticket_priority_selected', 2), ['required' => 'true']); 
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php 
                                        $value = '';
                                        echo render_date_input('duedate', 'task_add_edit_due_date', $value, array('required'=>'true'));
                                        ?>
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
                                        <?php echo _l('ticket_settings_assign_to'); ?>
                                    </label>
                                    <select name="assigned" id="assigned" class="form-control selectpicker"
                                        data-live-search="true"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                        data-width="100%" required="true">
                                        <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                                        <?php foreach ($staff as $member) { ?>
                                        <option value="<?php echo e($member['staffid']); ?>" <?php if ($member['staffid'] == get_staff_user_id()) { echo 'selected';} ?>>
                                            <?php echo e($member['firstname'] . ' ' . $member['lastname']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="created_by" class="control-label">
                                                <?php echo _l('created_by'); ?>
                                            </label>
                                            <select name="created_by" id="created_by" class="form-control selectpicker"
                                                data-live-search="true"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                data-width="100%" required="true">
                                                <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                                                <?php foreach ($staff as $member) { ?>
                                                <option value="<?php echo e($member['staffid']); ?>" <?php if ($member['staffid'] == get_staff_user_id()) { echo 'selected';} ?>>
                                                    <?php echo e($member['firstname'] . ' ' . $member['lastname']); ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('rfi_to', 'rfi_to'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        $area = get_area_project_wise(); 
                                        echo render_select('area[]', $area, array('id','area_name'), 'area', [], array('multiple' => true));
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php 
                                        $disciplines = get_all_discipline();
                                        echo render_select('discipline', $disciplines, array('id','name'), 'discipline'); 
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_input('ref_drawing', 'reference_drawings'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_select('dms_items[]', $dms_items, array('id', 'name'), 'Drawings', '', array('data-width' => '100%', 'data-none-selected-text' => _l('Drawings'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <?php echo render_custom_fields('tickets'); ?>
                            </div>

                            <div class="col-md-12">
                                <hr class="hr-panel-separator" />
                            </div>
                            <div class="col-md-12 tw-mt-3">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-base tw-text-neutral-700">
                                    <?php echo _l('ticket_add_body'); ?>
                                </h4>
                                <div class="row">
                                    <div class="col-md-12 mbot20 before-ticket-message">
                                        <div class="row">
                                            <div class="col-md-6 hide">
                                                <select id="insert_predefined_reply" data-width="100%"
                                                    data-live-search="true" class="selectpicker"
                                                    data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
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
                                                    onchange="insert_ticket_knowledgebase_link(this);"
                                                    data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
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
                                                        class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                            extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>"
                                                            filesize="<?php echo file_upload_max_size(); ?>"
                                                            class="form-control" name="attachments[0]"
                                                            accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default add_more_attachments"
                                                                data-max="<?php echo get_option('maximum_allowed_ticket_attachments'); ?>"
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

                            <button type="submit" data-form="#new_ticket_form" autocomplete="off"
                                data-loading-text="<?php echo _l('wait_text'); ?>"
                                class="btn btn-primary"><?php echo _l('open_ticket'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="tw-py-10"></div>
    <?php $this->load->view('admin/tickets/services/service'); ?>
    <?php init_tail(); ?>
    <?php hooks()->do_action('new_ticket_admin_page_loaded'); ?>
    <script>
    $(function(){
        $('#project_id').trigger('change');
        validate_new_ticket_form();
        
    });
    </script>
    </body>

    </html>