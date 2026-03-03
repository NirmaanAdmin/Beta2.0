<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="settings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_input('subject', 'form_settings_subject', $form->subject); ?>

                                        <div class="form-group projects-wrapper">
                                            <?php
                                            echo render_select('project_id', $projects, array('id', 'name'), 'project', $form->project_id, array('required' => 'true'));
                                            ?>
                                        </div>
                                        <?php echo form_hidden('formid', $form->formid); ?>
                                        <?php echo render_select('department', $departments, ['departmentid', 'name'], 'form_settings_departments', $form->department); ?>

                                        <div class="form-group select-placeholder">
                                            <select name="form_type" class="selectpicker no-margin" data-width="100%" id="form_type" data-none-selected-text="None selected" data-live-search="true" disabled>
                                                <option value=""></option>
                                                <?php
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

                                        <div class="form-group select-placeholder">
                                            <label for="assigned" class="control-label">
                                                <?php echo _l('form_settings_assign_to'); ?>
                                            </label>
                                            <select name="assigned" data-live-search="true" id="assigned"
                                                class="form-control selectpicker"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?php echo _l('form_settings_none_assigned'); ?>
                                                </option>
                                                <?php foreach ($staff as $member) {
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var admin_url = window.location.origin + '/' + window.location.pathname.split('/')[1] + '/admin/';
    var form_type = $('select[name="form_type"]').val();
    if (form_type != '') {
        find_form_design(form_type);
    }

    function find_form_design(form_type) {
        var form_id = $('input[name="formid"]').val();
        
        $.post(admin_url + 'purchase/vendors_portal/find_form_design/' + form_type + '/' + form_id).done(function(response) {
            $('.view_form_design').html('');
            $('.view_form_design').html(response);
            $('.view_project_name').html('');
            var project_name = $('#project_id option:selected').text();
            $('.view_project_name').html(project_name);
            $('.selectpicker').selectpicker('refresh');
        });
    }

    $(".save_changes_settings_single_form").on("click", function(e) {
        e.preventDefault();
        var data = {};

        var $settingsArea = $("#settings");
        var errors = false;

        if ($settingsArea.find('input[name="subject"]').val() == "") {
            errors = true;
            $settingsArea
                .find('input[name="subject"]')
                .parents(".form-group")
                .addClass("has-error");
        } else {
            $settingsArea
                .find('input[name="subject"]')
                .parents(".form-group")
                .removeClass("has-error");
        }

        var selectRequired = ["department", "priority"];

        if ($("#contactid").data("no-contact") != true) {
            selectRequired.push("contactid");
        }

        for (var i = 0; i < selectRequired.length; i++) {
            var $select = $settingsArea.find(
                'select[name="' + selectRequired[i] + '"]'
            );
            if ($select.selectpicker("val") == "") {
                errors = true;
                $select.parents(".form-group").addClass("has-error");
            } else {
                $select.parents(".form-group").removeClass("has-error");
            }
        }

        var cf_required = $settingsArea.find('[data-custom-field-required="1"]');

        $.each(cf_required, function() {
            var cf_field = $(this);
            var parent = cf_field.parents(".form-group");
            if (cf_field.is(":checkbox")) {
                var checked = parent.find('input[type="checkbox"]:checked');
                if (checked.length == 0) {
                    errors = true;
                    parent.addClass("has-error");
                } else {
                    parent.removeClass("has-error");
                }
            } else if (cf_field.is("input") || cf_field.is("textarea")) {
                if (cf_field.val() === "") {
                    errors = true;
                    parent.addClass("has-error");
                } else {
                    parent.removeClass("has-error");
                }
            } else if (cf_field.is("select")) {
                if (cf_field.selectpicker("val") == "") {
                    errors = true;
                    parent.addClass("has-error");
                } else {
                    parent.removeClass("has-error");
                }
            }
        });

        if (errors == true) {
            return;
        }

        // Create a FormData object
        var formData = new FormData();

        // Serialize the form data
        $("#settings *")
            .serializeArray()
            .forEach(function(field) {
                formData.append(field.name, field.value);
            });

        // Add the form ID
        formData.append("formid", $('input[name="formid"]').val());

        // Add the CSRF token if available
        if (typeof csrfData !== "undefined") {
            formData.append(csrfData["token_name"], csrfData["hash"]);
        }

        // Append all dynamic file inputs
        $('.attachment_new input[type="file"]').each(function() {
            var fileInput = $(this)[0]; // Get the file input element
            if (fileInput.files.length > 0) {
                // Use the dynamic name attribute for the key
                formData.append($(this).attr('name'), fileInput.files[0]);
            }
        });
        // Send the AJAX request
        $.ajax({
            url: admin_url + "purchase/vendors_portal/update_single_form_settings",
            type: "POST",
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from setting the Content-Type header
            success: function(response) {
                response = JSON.parse(response);
                if (response.success === true) {
                    if (typeof response.department_reassigned !== "undefined") {
                        window.location.href = admin_url + "forms/";
                    } else {
                        window.location.reload();
                    }
                } else if (typeof response.message !== "undefined") {
                    alert_float("warning", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert_float("danger", "An error occurred while processing your request.");
            },
        });

    });
</script>