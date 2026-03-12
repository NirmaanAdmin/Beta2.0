<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php set_form_open($form->adminread, $form->formid); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if ($form->merged_form_id !== null) { ?>
                    <div class="alert alert-info" role="alert">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <p class="tw-font-semibold tw-mb-0">
                                <?php echo _l('form_merged_notice'); ?>:
                                <?php echo e($form->merged_form_id); ?>
                            </p>
                            <a href="<?php echo admin_url('forms/form/' . $form->merged_form_id); ?>"
                                class="btn btn-info btn-sm">
                                <?php echo _l('view_primary_form'); ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
                <div class="tw-mb-4">
                    <div class="md:tw-flex md:tw-items-center">
                        <div class="tw-inline-flex tw-items-center tw-grow md:tw-mr-4">
                            <h3 class="tw-font-semibold tw-text-xl tw-my-0">
                                <span id="form_subject">
                                    #<?php echo e($form->formid); ?> - <?php echo e($form->subject); ?>
                                </span>
                            </h3>
                            <?php echo '<span class="tw-self-start md:tw-self-center label' . (is_mobile() ? ' ' : ' mleft15 ') . 'single-form-status-label" style="color:' . $form->statuscolor . ';border: 1px solid ' . adjust_hex_brightness($form->statuscolor, 0.4) . '; background:' . adjust_hex_brightness($form->statuscolor, 0.04) . ';">' . form_status_translate($form->formstatusid) . '</span>'; ?>

                        </div>
                        <?php
                        echo render_select('status_top', $statuses, ['formstatusid', 'name'], '', $form->status, [], [], 'no-mbot tw-flex-1 tw-max-w-sm', '', false);
                        ?>
                    </div>
                    <?php
                    if ($form->project_id) {
                        echo '<p class="tw-text-base tw-font-normal tw-mb-0">' . _l('form_linked_to_project', '<a href="' . admin_url('projects/view/' . $form->project_id) . '">' . get_project_name_by_id($form->project_id) . '</a>') . '</p>';
                    }
                    ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <!-- <li role="presentation" class="<?php if (!$this->session->flashdata('active_tab')) {
                                                                                echo 'active';
                                                                            } ?>">
                                            <a href="#addreply" aria-controls="addreply" role="tab" data-toggle="tab">
                                                <?php echo _l('form_single_add_reply'); ?>
                                            </a>
                                        </li> -->
                                        <li role="presentation" class="<?php if ($this->session->flashdata('active_tab_settings')) {
                                                                            echo 'active';
                                                                        } ?>">
                                            <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                                                <?php echo _l('Details'); ?>
                                            </a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#note" aria-controls="note" role="tab" data-toggle="tab">
                                                <?php echo _l('form_single_add_note'); ?>
                                            </a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#tab_reminders"
                                                onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $form->formid; ?> + '/' + 'form', undefined, undefined, undefined,[1,'asc']); return false;"
                                                aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                                <?php echo _l('form_reminders'); ?>
                                                <?php
                                                $total_reminders = total_rows(
                                                    db_prefix() . 'reminders',
                                                    [
                                                        'isnotified' => 0,
                                                        'staff'      => get_staff_user_id(),
                                                        'rel_type'   => 'form',
                                                        'rel_id'     => $form->formid,
                                                    ]
                                                );
                                                if ($total_reminders > 0) {
                                                    echo '<span class="badge">' . $total_reminders . '</span>';
                                                }
                                                ?>
                                            </a>
                                        </li>
                                        <!-- <li role="presentation">
                                            <a href="#otherforms" onclick="init_table_forms(true);"
                                                aria-controls="otherforms" role="tab" data-toggle="tab">
                                                <?php echo _l('form_single_other_user_forms'); ?>
                                            </a>
                                        </li> -->
                                        <li role="presentation">
                                            <a href="#tasks"
                                                onclick="init_rel_tasks_table(<?php echo e($form->formid); ?>,'form'); return false;"
                                                aria-controls="tasks" role="tab" data-toggle="tab">
                                                <?php echo _l('tasks'); ?>
                                            </a>
                                        </li>

                                        <?php do_action_deprecated('add_single_form_tab_menu_item', $form, '3.0.7', 'after_admin_single_form_tab_menu_last_item'); ?>
                                        <?php hooks()->do_action('after_admin_single_form_tab_menu_last_item', $form); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?php if (!$this->session->flashdata('active_tab')) {
                                                                        echo 'active';
                                                                    } ?>" id="addreply">
                                <?php $tags = get_tags_in($form->formid, 'form'); ?>
                                <?php if (count($tags) > 0) { ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php echo '<p><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</p> ' . render_tags($tags); ?>
                                            <hr class="hr-panel-separator" />
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (count($form->form_notes) > 0) { ?>
                                    <div class="mbot15">
                                        <h4 class="tw-font-semibold tw-text-base tw-mt-0">
                                            <?php echo _l('form_single_private_staff_notes'); ?>
                                        </h4>
                                        <div class="formstaffnotes tw-mb-1 tw-inline-block tw-w-full">
                                            <?php foreach ($form->form_notes as $note) { ?>
                                                <div
                                                    class="tw-rounded-md tw-bg-warning-50 tw-p-4 tw-mb-2 tw-group tw-border tw-border-solid tw-border-warning-100">
                                                    <div class="tw-flex">
                                                        <div class="tw-flex-shrink-0">
                                                            <?php echo staff_profile_image($note['addedfrom'], ['staff-profile-xs-image']); ?>
                                                        </div>
                                                        <div class="tw-ml-2 tw-flex-1">
                                                            <div class="tw-flex">
                                                                <h3
                                                                    class="tw-text-sm tw-font-medium tw-text-warning-800 tw-mb-0 tw-mt-1 tw-grow">
                                                                    <a href="<?php echo admin_url('staff/profile/' . $note['addedfrom']); ?>"
                                                                        class="tw-text-warning-700 hover:tw-text-warning-900">
                                                                        <?php echo e(_l('form_single_form_note_by', get_staff_full_name($note['addedfrom']))); ?>
                                                                    </a>
                                                                    <br />
                                                                    <span class="tw-text-xs tw-text-warning-600">
                                                                        <?php echo e(_l('form_single_note_added', _dt($note['dateadded']))); ?>
                                                                    </span>
                                                                </h3>

                                                                <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                                                    <div class="tw-space-x-1 tw-hidden group-hover:tw-block">
                                                                        <a href="#"
                                                                            class="tw-text-warning-600 hover:tw-text-warning-700 focus:tw-text-warning-700"
                                                                            onclick="toggle_edit_note(<?php echo e($note['id']); ?>);return false;">
                                                                            <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                                        </a>
                                                                        <a href="<?php echo admin_url('misc/delete_note/' . $note['id']); ?>"
                                                                            class="tw-text-warning-600 hover:tw-text-warning-700 focus:tw-text-warning-700 _delete">
                                                                            <i class="fa-regular fa-trash-can fa-lg"></i>
                                                                        </a>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>

                                                            <div class="tw-mt-2 tw-text-sm tw-text-warning-700">
                                                                <div data-note-description="<?php echo e($note['id']); ?>">
                                                                    <?php echo process_text_content_for_display($note['description']); ?>
                                                                </div>
                                                                <div data-note-edit-textarea="<?php echo e($note['id']); ?>"
                                                                    class="hide">
                                                                    <textarea name="description" class="form-control"
                                                                        rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                                                    <div class="text-right tw-mt-3">
                                                                        <button type="button" class="btn btn-default"
                                                                            onclick="toggle_edit_note(<?php echo e($note['id']); ?>);return false;">
                                                                            <?php echo _l('cancel'); ?>
                                                                        </button>
                                                                        <button type="button" class="btn btn-primary"
                                                                            onclick="edit_note(<?php echo e($note['id']); ?>);">
                                                                            <?php echo _l('update_note'); ?>
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div>
                                    <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'single-form-form', 'novalidate' => true]); ?>
                                    <?php if (can_staff_delete_form()) { ?>
                                        <a href="<?php echo admin_url('forms/delete/' . $form->formid); ?>"
                                            data-toggle="tooltip" data-title="<?= _l('delete', _l('form_lowercase')); ?>"
                                            class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete tw-mr-2">
                                            <i class="fa-regular fa-trash-can fa-lg"></i>
                                        </a>
                                    <?php } ?>

                                    <?php if (!empty($form->priority_name)) { ?>
                                        <span class="form-label label label-default inline-block">
                                            <?php echo e(_l('form_single_priority', form_priority_translate($form->priorityid))); ?>
                                        </span>
                                    <?php } ?>
                                    <?php if (!empty($form->service_name)) { ?>
                                        <span class="form-label label label-default inline-block">
                                            <?php echo _l('service') . ': ' . $form->service_name; ?>
                                        </span>
                                    <?php } ?>
                                    <?php echo form_hidden('formid', $form->formid); ?>
                                    <span class="form-label label label-default inline-block">
                                        <?php echo _l('department') . ': ' . $form->department_name; ?>
                                    </span>
                                    <?php if ($form->assigned != 0) { ?>
                                        <span class="form-label label label-info inline-block">
                                            <?php echo _l('form_assigned'); ?>:
                                            <?php echo e(get_staff_full_name($form->assigned)); ?>
                                        </span>
                                    <?php } ?>
                                    <?php if ($form->lastreply !== null) { ?>
                                        <span class="form-label label label-success inline-block" data-toggle="tooltip"
                                            title="<?php echo e(_dt($form->lastreply)); ?>">
                                            <span class="text-has-action">
                                                <?php echo e(_l('form_single_last_reply', time_ago($form->lastreply))); ?>
                                            </span>
                                        </span>
                                    <?php } ?>

                                    <a class="form-label label label-info inline-block"
                                        href="<?php echo get_form_public_url($form); ?>" target="_blank">
                                        <?php echo _l('view_public_form'); ?>
                                    </a>

                                    <div class="mtop15">
                                        <?php
                                        $use_knowledge_base = get_option('use_knowledge_base');
                                        ?>
                                        <div class="row mbot15">
                                            <div class="col-md-6 hide">
                                                <select data-width="100%" id="insert_predefined_reply"
                                                    data-live-search="true" class="selectpicker"
                                                    data-title="<?php echo _l('form_single_insert_predefined_reply'); ?>">
                                                    <?php foreach ($predefined_replies as $predefined_reply) { ?>
                                                        <option value="<?php echo e($predefined_reply['id']); ?>">
                                                            <?php echo e($predefined_reply['name']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php if ($use_knowledge_base == 1) { ?>
                                                <div class="visible-xs">
                                                    <div class="mtop15"></div>
                                                </div>
                                                <div class="col-md-6 hide">
                                                    <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                                    <select data-width="100%" id="insert_knowledge_base_link"
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
                                        <?php echo render_textarea('message', '', '', [], [], '', 'tinymce'); ?>
                                        <div
                                            class="alert alert-warning staff_replying_notice <?php echo ($form->staff_id_replying === null || $form->staff_id_replying === get_staff_user_id()) ? 'hide' : '' ?>">
                                            <?php if ($form->staff_id_replying !== null && $form->staff_id_replying !== get_staff_user_id()) { ?>
                                                <p><?php echo e(_l('staff_is_currently_replying', get_staff_full_name($form->staff_id_replying))); ?>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-reply-tools">
                                        <?php if ($form->merged_form_id === null) { ?>
                                            <div class="btn-bottom-toolbar text-right">
                                                <button type="submit" class="btn btn-primary"
                                                    data-form="#single-form-form" autocomplete="off"
                                                    data-loading-text="<?php echo _l('wait_text'); ?>">
                                                    <?php echo _l('form_single_add_response'); ?>
                                                </button>
                                            </div>
                                        <?php } ?>
                                        <div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <?php echo render_select('status', $statuses, ['formstatusid', 'name'], 'form_single_change_status', $form->status, [], [], '', '', false); ?>
                                                    <?php echo render_input('cc', 'CC', $form->cc); ?>
                                                    <?php if ($form->assigned !== get_staff_user_id()) { ?>
                                                        <div class="checkbox">
                                                            <input type="checkbox" name="assign_to_current_user"
                                                                id="assign_to_current_user">
                                                            <label
                                                                for="assign_to_current_user"><?php echo _l('form_single_assign_to_me_on_update'); ?></label>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox"
                                                            <?php echo hooks()->apply_filters('form_add_response_and_back_to_list_default', 'checked'); ?>
                                                            name="form_add_response_and_back_to_list" value="1"
                                                            id="form_add_response_and_back_to_list">
                                                        <label
                                                            for="form_add_response_and_back_to_list"><?php echo _l('form_add_response_and_back_to_list'); ?></label>
                                                    </div>
                                                </div>
                                                <?php
                                                $totalMergedForms = count($merged_forms);
                                                if ($totalMergedForms > 0) { ?>
                                                    <div class="col-md-7">
                                                        <div class="mtop25">
                                                            <p class="alert alert-info">
                                                                <?php echo _l('form_merged_forms_header', $totalMergedForms) ?>
                                                            </p>
                                                            <ul class="list-group">
                                                                <?php foreach ($merged_forms as $merged_form) { ?>
                                                                    <a href="<?php echo admin_url('forms/form/' . $merged_form['formid']) ?>"
                                                                        class="list-group-item tw-font-medium">
                                                                        #<?php echo $merged_form['formid'] ?> -
                                                                        <?php echo $merged_form['subject'] ?>
                                                                    </a>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <hr class="hr-panel-separator" />
                                            <div class="row attachments">
                                                <div class="attachment">
                                                    <div class="col-md-5 mbot15">
                                                        <div class="form-group">
                                                            <label for="attachment" class="control-label">
                                                                <?php echo _l('form_single_attachments'); ?>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="file"
                                                                    extension="<?php echo str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')); ?>"
                                                                    filesize="<?php echo file_upload_max_size(); ?>"
                                                                    class="form-control" name="attachments[0]"
                                                                    accept="<?php echo get_form_form_accepted_mimes(); ?>">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default add_more_attachments"
                                                                        data-max="<?php echo get_option('maximum_allowed_form_attachments'); ?>"
                                                                        type="button">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="note">
                                <hr class="no-mtop" />
                                <div class="form-group">
                                    <label
                                        for="note_description"><?php echo _l('form_single_note_heading'); ?></label>
                                    <textarea class="form-control" name="note_description" rows="5"></textarea>
                                </div>
                                <a
                                    class="btn btn-primary pull-right add_note_form"><?php echo _l('form_single_add_note'); ?></a>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_reminders">
                                <a href="#" class="btn btn-default" data-toggle="modal"
                                    data-target=".reminder-modal-form-<?php echo e($form->formid); ?>"><i
                                        class="fa-regular fa-bell"></i>
                                    <?php echo _l('form_set_reminder_title'); ?></a>
                                <hr />
                                <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="otherforms">
                                <hr class="no-mtop" />
                                <div class="_filters _hidden_inputs hidden forms_filters">
                                    <?php echo form_hidden('via_form', $form->formid); ?>
                                    <?php echo form_hidden('via_form_email', $form->email); ?>
                                    <?php echo form_hidden('via_form_userid', $form->userid); ?>
                                </div>
                                <?php echo AdminFormsTableStructure(); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tasks">
                                <hr class="no-mtop" />
                                <?php init_relation_tasks_table(['data-new-rel-id' => $form->formid, 'data-new-rel-type' => 'form']); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php if ($this->session->flashdata('active_tab_settings')) {
                                                                        echo 'active';
                                                                    } ?>" id="settings">
                                <hr class="no-mtop" />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <?php echo render_input('subject', 'form_settings_subject', $form->subject); ?>
                                        </div>
                                        <div class="col-md-6 form-group projects-wrapper">
                                            <?php
                                            echo render_select('project_id', $projects, array('id', 'name'), 'project', $form->project_id, array('required' => 'true'));
                                            ?>
                                        </div>
                                        <div class="col-md-6 form-group select-placeholder">
                                            <label for="assigned" class="control-label">
                                                <?php echo _l('form_settings_assign_to'); ?>
                                            </label>
                                            <select name="assigned" data-live-search="true" id="assigned"
                                                class="form-control selectpicker"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?php echo _l('form_settings_none_assigned'); ?>
                                                </option>
                                                <?php foreach ($staff as $member) {
                                                    // Form is assigned to member
                                                    // Member is set to inactive
                                                    // We should show the member in the dropdown too
                                                    // Otherwise, skip this member
                                                    if ($member['active'] == 0 && $form->assigned != $member['staffid']) {
                                                        continue;
                                                    } ?>
                                                    <option value="<?php echo e($member['staffid']); ?>" <?php if ($form->assigned == $member['staffid']) {
                                                                                                                echo 'selected';
                                                                                                            } ?>>
                                                        <?php echo e($member['firstname'] . ' ' . $member['lastname']); ?>
                                                    </option>
                                                <?php
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6" style="clear: both;">
                                            <?php echo render_select('department', $departments, ['departmentid', 'name'], 'form_settings_departments', $form->department); ?>
                                        </div>
                                        <div class="col-md-6 form-group select-placeholder">
                                            <label for="form_type" class="control-label"></label>
                                            <select name="form_type" class="selectpicker no-margin" data-width="100%" id="form_type" data-none-selected-text="None selected" data-live-search="true" disabled>
                                                <option value=""></option>
                                                <?php
                                                // $form_listing = get_form_listing(); 
                                                foreach ($form_listing as $group_id => $_items) { ?>
                                                    <optgroup data-group-id="<?php echo $_items['id']; ?>" label="<?php echo $_items['name']; ?>">
                                                        <?php
                                                        foreach ($_items['options'] as $item) { ?>
                                                            <option value="<?php echo $item['id']; ?>" <?php echo ($item['id'] == $form->form_type) ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>
                                                        <?php } ?>
                                                    </optgroup>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-6">

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <?php
                                                $pr_orders = get_pr_order();
                                                $selected_pur = '';
                                                if (isset($form) && $form->pur_order_id != 0 && !empty($form->pur_order_id)) {
                                                    $selected_pur = $form->pur_order_id;
                                                }
                                                ?>
                                                <label for="pur_order_id"><?php echo _l('reference_purchase_order'); ?></label>
                                                <select name="pur_order_id" id="pur_order_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($pr_orders as $pr_order) { ?>
                                                        <option value="<?php echo html_entity_decode($pr_order['id']); ?>" <?php echo ($selected_pur == $pr_order['id']) ? 'selected' : ''; ?>>
                                                            <?php echo html_entity_decode($pr_order['pur_order_number'] . ' - ' . $pr_order['pur_order_name'] . ' - ' . get_vendor_name($pr_order['vendor'])); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <?php
                                                $wo_orders = get_wo_order();
                                                $selected_wo = '';
                                                if (isset($form) && $form->wo_order_id != 0 && !empty($form->wo_order_id)) {
                                                    $selected_wo = $form->wo_order_id;
                                                }
                                                ?>
                                                <label for="wo_order_id"><?php echo _l('reference_work_order'); ?></label>
                                                <select name="wo_order_id" id="wo_order_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($wo_orders as $wo_order) { ?>
                                                        <option value="<?php echo html_entity_decode($wo_order['id']); ?>" <?php echo ($selected_wo == $wo_order['id']) ? 'selected' : ''; ?>>
                                                            <?php echo html_entity_decode($wo_order['wo_order_number'] . ' - ' . $wo_order['wo_order_name'] . ' - ' . get_vendor_name($wo_order['vendor'])); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php
                                                $priorities['callback_translate'] = 'form_priority_translate';
                                                echo render_select('priority', $priorities, ['priorityid', 'name'], 'form_settings_priority', $form->priority); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php
                                                $value = (isset($form) ? _d($form->duedate) : '');
                                                echo render_date_input('duedate', 'task_add_edit_due_date', $value);
                                                ?>
                                            </div>
                                            <?php if (get_option('services') == 1) { ?>
                                                <div class="col-md-6 hide">
                                                    <?php if (is_admin() || get_option('staff_members_create_inline_form_services') == '1') {
                                                        echo render_select_with_input_group('service', $services, ['serviceid', 'name'], 'form_settings_service', $form->service, '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_service();return false;"><i class="fa fa-plus"></i></a></div>');
                                                    } else {
                                                        echo render_select('service', $services, ['serviceid', 'name'], 'form_settings_service', $form->service);
                                                    }
                                                    ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php echo render_input('merge_form_ids', 'merge_form_ids_field_label', '', 'text', $form->merged_form_id === null ? ['placeholder' => _l('merge_form_ids_field_placeholder')] : ['disabled' => true]); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mbot20">
                                                    <label for="tags" class="control-label"><i class="fa fa-tag"
                                                            aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                                                    <input type="text" class="tagsinput" id="tags" name="tags"
                                                        value="<?php echo prep_tags_input(get_tags_in($form->formid, 'form')); ?>"
                                                        data-role="tagsinput">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <?php echo render_custom_fields('forms', $form->formid); ?>
                                    </div>

                                    <div class="view_form_design"></div>

                                </div>
                                <?php do_action_deprecated('add_single_form_tab_menu_content', $form, '3.0.7', 'after_admin_single_form_tab_menu_last_content'); ?>

                                <div
                                    class="tw-bg-neutral-50 text-right tw-px-6 tw-py-3 -tw-mx-6 -tw-mb-6 tw-border-t tw-border-solid tw-border-neutral-200 tw-rounded-b-md">
                                    <a href="#" class="btn btn-primary save_changes_settings_single_form">
                                        <?php echo _l('submit'); ?>
                                    </a>
                                </div>
                            </div>
                            <?php hooks()->do_action('after_admin_single_form_tab_menu_last_content', $form); ?>

                        </div>
                    </div>
                </div>
                <div class="panel_s mtop20">
                    <div class="panel-body <?php if ($form->admin == null) {
                                                echo 'client-reply';
                                            } ?>">
                        <div class="row">
                            <div class="col-md-3 border-right form-submitter-info form-submitter-info">
                                <p>
                                    <?php if ($form->admin == null || $form->admin == 0) { ?>
                                        <?php if ($form->userid != 0) { ?>
                                            <a
                                                href="<?php echo admin_url('clients/client/' . $form->userid . '?contactid=' . $form->contactid); ?>"><?php echo e($form->submitter); ?>
                                            </a>
                                        <?php } else {
                                            echo e($form->submitter); ?>
                                            <br />
                                            <a
                                                href="mailto:<?php echo e($form->form_email); ?>"><?php echo e($form->form_email); ?></a>
                                            <hr />
                                            <?php
                                            if (total_rows(db_prefix() . 'spam_filters', ['type' => 'sender', 'value' => $form->form_email, 'rel_type' => 'forms']) == 0) { ?>
                                                <button type="button" data-sender="<?php echo e($form->form_email); ?>"
                                                    class="btn btn-danger block-sender btn-sm"> <?php echo _l('block_sender'); ?>
                                                </button>
                                        <?php
                                            } else {
                                                echo '<span class="label label-danger">' . _l('sender_blocked') . '</span>';
                                            }
                                        }
                                    } else {  ?>
                                        <a
                                            href="<?php echo admin_url('profile/' . $form->admin); ?>">
                                            <?php echo e($form->opened_by); ?>
                                        </a>
                                    <?php } ?>
                                </p>
                                <p class="text-muted">
                                    <?php if ($form->admin !== null || $form->admin != 0) {
                                        echo _l('form_staff_string');
                                    } else {
                                        if ($form->userid != 0) {
                                            echo _l('form_client_string');
                                        }
                                    }
                                    ?>
                                </p>
                                <?php if (staff_can('create',  'tasks')) { ?>
                                    <a href="#" class="btn btn-default btn-sm"
                                        onclick="convert_form_to_task(<?php echo e($form->formid); ?>,'form'); return false;"><?php echo _l('convert_to_task'); ?></a>
                                <?php } ?>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12 text-right tw-mb-6 tw-space-x-2">
                                        <?php if (!empty($form->message)) { ?>
                                            <a href="#"
                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 active:tw-text-neutral-600"
                                                onclick="print_form_message(<?php echo e($form->formid); ?>, 'form'); return false;"
                                                class="mright5"><i class="fa fa-print"></i></a>
                                        <?php } ?>
                                        <?php if (can_staff_edit_form_message()) { ?>
                                            <a href="#"
                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 active:tw-text-neutral-600"
                                                onclick="edit_form_message(<?php echo e($form->formid); ?>,'form'); return false;"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div data-form-id="<?php echo e($form->formid); ?>" class="tc-content">
                                    <?php
                                    if (empty($form->admin)) {
                                        echo process_text_content_for_display($form->message);
                                    } else {
                                        echo check_for_links($form->message);
                                    }
                                    ?>
                                </div>
                                <?php if (count($form->attachments) > 0) {
                                    echo '<hr />';
                                    foreach ($form->attachments as $attachment) {
                                        $path     = get_upload_path_by_type('form') . $form->formid . '/' . $attachment['file_name'];
                                        $is_image = is_image($path);

                                        if ($is_image) {
                                            echo '<div class="preview_image">';
                                        } ?>
                                        <a href="<?php echo site_url('download/file/form/' . $attachment['id']); ?>"
                                            class="display-block mbot5" <?php if ($is_image) { ?>
                                            data-lightbox="attachment-form-<?php echo e($form->formid); ?>" <?php } ?>>
                                            <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                                            <?php echo e($attachment['file_name']); ?>
                                            <?php if ($is_image) { ?>
                                                <img class="mtop5"
                                                    src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>">
                                            <?php } ?>
                                        </a>
                                        <?php if ($is_image) {
                                            echo '</div>';
                                        }
                                        if (is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_form_attachments') == '1')) {
                                            echo '<a href="' . admin_url('forms/delete_attachment/' . $attachment['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                                        }
                                        echo '<hr />'; ?>
                                <?php
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <?php echo e(_l('form_posted', _dt($form->date))); ?>
                    </div>
                </div>
                <?php foreach ($form_replies as $reply) { ?>
                    <div class="panel_s">
                        <div class="panel-body <?php if ($reply['admin'] == null) {
                                                    echo 'client-reply';
                                                } ?>">
                            <div class="row">
                                <div class="col-md-3 border-right form-submitter-info">
                                    <p>
                                        <?php if ($reply['admin'] == null || $reply['admin'] == 0) { ?>
                                            <?php if ($reply['userid'] != 0) { ?>
                                                <a
                                                    href="<?php echo admin_url('clients/client/' . $reply['userid'] . '?contactid=' . $reply['contactid']); ?>"><?php echo e($reply['submitter']); ?></a>
                                            <?php } else { ?>
                                                <?php echo e($reply['submitter']); ?>
                                                <br />
                                                <a
                                                    href="mailto:<?php echo e($reply['reply_email']); ?>">
                                                    <?php echo e($reply['reply_email']); ?>
                                                </a>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <a
                                                href="<?php echo admin_url('profile/' . $reply['admin']); ?>">
                                                <?php echo e($reply['submitter']); ?>
                                            </a>
                                        <?php } ?>
                                    </p>
                                    <p class="text-muted">
                                        <?php
                                        if ($reply['admin'] !== null || $reply['admin'] != 0) {
                                            echo _l('form_staff_string');
                                        } else {
                                            if ($reply['userid'] != 0) {
                                                echo _l('form_client_string');
                                            }
                                        }
                                        ?>
                                    </p>
                                    <hr />
                                    <?php if (can_staff_delete_form_reply()) { ?>
                                        <a href="<?php echo admin_url('forms/delete_form_reply/' . $form->formid . '/' . $reply['id']); ?>"
                                            class="btn btn-danger pull-left _delete mright5 btn-sm">
                                            <?php echo _l('delete_form_reply'); ?>
                                        </a>
                                    <?php } ?>
                                    <div class="clearfix"></div>
                                    <?php if (staff_can('create',  'tasks')) { ?>
                                        <a href="#" class="pull-left btn btn-default mtop5 btn-sm"
                                            onclick="convert_form_to_task(<?php echo e($reply['id']); ?>,'reply'); return false;">
                                            <?php echo _l('convert_to_task'); ?>
                                        </a>
                                        <div class="clearfix"></div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-12 text-right tw-mb-6 tw-space-x-2">
                                            <?php if (!empty($reply['message'])) { ?>
                                                <a href="#"
                                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 active:tw-text-neutral-600"
                                                    onclick="print_form_message(<?php echo e($reply['id']); ?>, 'reply'); return false;"
                                                    class="mright5"><i class="fa fa-print"></i></a>
                                            <?php } ?>
                                            <a href="#"
                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 active:tw-text-neutral-600"
                                                onclick="edit_form_message(<?php echo e($reply['id']); ?>,'reply'); return false;"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div data-reply-id="<?php echo e($reply['id']); ?>" class="tc-content">
                                        <?php
                                        if (empty($reply['admin'])) {
                                            echo process_text_content_for_display($reply['message']);
                                        } else {
                                            echo check_for_links($reply['message']);
                                        }
                                        ?>
                                    </div>
                                    <?php if (count($reply['attachments']) > 0) {
                                        echo '<hr />';
                                        foreach ($reply['attachments'] as $attachment) {
                                            $path     = get_upload_path_by_type('form') . $form->formid . '/' . $attachment['file_name'];
                                            $is_image = is_image($path);

                                            if ($is_image) {
                                                echo '<div class="preview_image">';
                                            } ?>
                                            <a href="<?php echo site_url('download/file/form/' . $attachment['id']); ?>"
                                                class="display-block mbot5" <?php if ($is_image) { ?>
                                                data-lightbox="attachment-reply-<?php echo e($reply['id']); ?>" <?php } ?>>
                                                <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                                                <?php echo e($attachment['file_name']); ?>
                                                <?php if ($is_image) { ?>
                                                    <img class="mtop5"
                                                        src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>">
                                                <?php } ?>
                                            </a>
                                    <?php if ($is_image) {
                                                echo '</div>';
                                            }
                                            if (is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_form_attachments') == '1')) {
                                                echo '<a href="' . admin_url('forms/delete_attachment/' . $attachment['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                                            }
                                            echo '<hr />';
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <span><?php echo e(_l('form_posted', _dt($reply['date']))); ?></span>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="btn-bottom-pusher"></div>
        <?php if (count($form_replies) > 1) { ?>
            <a href="#top" id="toplink">↑</a>
            <a href="#bot" id="botlink">↓</a>
        <?php } ?>
    </div>
</div>
<!-- The reminders modal -->
<?php $this->load->view(
    'admin/includes/modals/reminder',
    [
        'id'             => $form->formid,
        'name'           => 'form',
        'members'        => $staff,
        'reminder_title' => _l('form_set_reminder_title'),
    ]
); ?>

<?php if (can_staff_edit_form_message()) { ?>
    <!-- Edit Form Messsage Modal -->
    <div class="modal fade" id="form-message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open(admin_url('forms/edit_message')); ?>
            <div class="modal-content">
                <div id="edit-form-message-additional"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo _l('form_message_edit'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo render_textarea('data', '', '', [], [], '', 'tinymce-form-edit'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
<?php } ?>
<script>
    var _form_message;
</script>
<?php $this->load->view('admin/forms/services/service'); ?>
<?php init_tail(); ?>
<?php hooks()->do_action('form_admin_single_page_loaded', $form); ?>
<script>
    $(function() {
        $('#project_id').trigger('change');
        $('#single-form-form').appFormValidator();
        $('body').on('shown.bs.modal', '#_task_modal', function() {
            if (typeof(_form_message) != 'undefined') {
                // Init the task description editor
                if (!is_mobile()) {
                    $(this).find('#description').click();
                } else {
                    $(this).find('#description').focus();
                }
                setTimeout(function() {
                    tinymce.get('description').execCommand('mceInsertContent', false,
                        _form_message);
                    $('#_task_modal input[name="name"]').val($('#form_subject').text().trim());
                }, 100);
            }
        });
        var editorMessage = tinymce.get('message');
        if (typeof(editorMessage) != 'undefined') {
            var firstTypeCheckPerformed = false;

            editorMessage.on('change', function() {
                if (!firstTypeCheckPerformed) {
                    // make AJAX Request
                    $.get(admin_url + 'forms/check_staff_replying/<?php echo e($form->formid); ?>',
                        function(result) {
                            var data = JSON.parse(result)
                            if (data.is_other_staff_replying === true || data
                                .is_other_staff_replying === 'true') {
                                $('.staff_replying_notice').html('<p>' + data.message + '</p>');
                                $('.staff_replying_notice').removeClass('hide');
                            } else {
                                $('.staff_replying_notice').addClass('hide');
                            }
                        });

                    firstTypeCheckPerformed = true;
                }

                $.post(admin_url +
                    'forms/update_staff_replying/<?php echo e($form->formid); ?>/<?php echo get_staff_user_id() ?>'
                );
            });

            $(document).on('pagehide, beforeunload', function() {
                $.post(admin_url + 'forms/update_staff_replying/<?php echo e($form->formid); ?>');
            })

            $(document).on('visibilitychange', function() {
                if (document.visibilityState === 'visible' || (editorMessage.getContent().trim() != ''))
                    return;
                $.post(admin_url + 'forms/update_staff_replying/<?php echo e($form->formid); ?>');
            })
        }
    });


    var Form_message_editor;
    var edit_form_message_additional = $('#edit-form-message-additional');

    function edit_form_message(id, type) {
        edit_form_message_additional.empty();
        // type is either form or reply
        _form_message = $('[data-' + type + '-id="' + id + '"]').html();
        init_form_edit_editor();
        $('#form-message').modal('show');
        setTimeout(function() {
            tinyMCE.activeEditor.setContent(_form_message);
        }, 1000)
        edit_form_message_additional.append(hidden_input('type', type));
        edit_form_message_additional.append(hidden_input('id', id));
        edit_form_message_additional.append(hidden_input('main_form', $('input[name="formid"]').val()));
    }

    function init_form_edit_editor() {
        if (typeof(Form_message_editor) !== 'undefined') {
            return true;
        }
        Form_message_editor = init_editor('.tinymce-form-edit');
    }
    <?php if (staff_can('create',  'tasks')) { ?>

        function convert_form_to_task(id, type) {
            if (type == 'form') {
                _form_message = $('[data-form-id="' + id + '"]').html();
            } else {
                _form_message = $('[data-reply-id="' + id + '"]').html();
            }
            var new_task_url = admin_url +
                'tasks/task?rel_id=<?php echo e($form->formid); ?>&rel_type=form&form_to_task=true';
            new_task(new_task_url);
        }
    <?php } ?>

    var form_type = $('select[name="form_type"]').val();
    if (form_type != '') {
        find_form_design(form_type);
    }

    function find_form_design(form_type) {
        var form_id = $('input[name="formid"]').val();
        $.post(admin_url + 'forms/find_form_design/' + form_type + '/' + form_id).done(function(response) {
            $('.view_form_design').html('');
            $('.view_form_design').html(response);
            $('.view_project_name').html('');
            var project_name = $('#project_id option:selected').text();
            $('.view_project_name').html(project_name);
            $('.selectpicker').selectpicker('refresh');
        });
    }
    $(document).ready(function() {
        // Function to handle disabling/enabling selects
        function handleSelectDisable() {
            var purOrderValue = $('#pur_order_id').val();
            var woOrderValue = $('#wo_order_id').val();

            // If both have values on page load
            if (purOrderValue && woOrderValue) {
                // Clear both and enable both (or handle as per your requirement)
                $('#pur_order_id, #wo_order_id').val('').selectpicker('refresh');
                $('#pur_order_id, #wo_order_id').prop('disabled', false).selectpicker('refresh');
            }
            // If only purchase order is selected
            else if (purOrderValue) {
                $('#wo_order_id').prop('disabled', true).selectpicker('refresh');
                $('#pur_order_id').prop('disabled', false).selectpicker('refresh');
            }
            // If only work order is selected
            else if (woOrderValue) {
                $('#pur_order_id').prop('disabled', true).selectpicker('refresh');
                $('#wo_order_id').prop('disabled', false).selectpicker('refresh');
            }
            // If none are selected
            else {
                $('#pur_order_id, #wo_order_id').prop('disabled', false).selectpicker('refresh');
            }
        }

        // Initial check on page load
        handleSelectDisable();

        // Handle purchase order change
        $('#pur_order_id').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
            var currentValue = $(this).val();

            if (currentValue) {
                // If purchase order is selected, disable work order
                $('#wo_order_id').prop('disabled', true).selectpicker('refresh');
            } else {
                // If purchase order is deselected, enable work order if it's empty
                if (!$('#wo_order_id').val()) {
                    $('#wo_order_id').prop('disabled', false).selectpicker('refresh');
                }
            }
        });

        // Handle work order change
        $('#wo_order_id').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
            var currentValue = $(this).val();

            if (currentValue) {
                // If work order is selected, disable purchase order
                $('#pur_order_id').prop('disabled', true).selectpicker('refresh');
            } else {
                // If work order is deselected, enable purchase order if it's empty
                if (!$('#pur_order_id').val()) {
                    $('#pur_order_id').prop('disabled', false).selectpicker('refresh');
                }
            }
        });

        // Optional: Handle manual clearing if needed
        $('#pur_order_id, #wo_order_id').on('click', function() {
            var $this = $(this);
            var currentValue = $this.val();
            var otherSelect = $this.attr('id') === 'pur_order_id' ? '#wo_order_id' : '#pur_order_id';

            // If this select is being cleared and the other is disabled
            if (!currentValue && $(otherSelect).is(':disabled')) {
                // Only enable the other if it's empty
                if (!$(otherSelect).val()) {
                    $(otherSelect).prop('disabled', false).selectpicker('refresh');
                }
            }
        });
    });
</script>
</body>

</html>