<style type="text/css">
    .daily_report_title,
    .daily_report_activity {
        font-weight: bold;
        text-align: center;
        background-color: lightgrey;
    }

    .daily_report_title {
        font-size: 17px;
    }

    .daily_report_activity {
        font-size: 16px;
    }

    .daily_report_head {
        font-size: 14px;
    }

    .daily_report_label {
        font-weight: bold;
    }

    .daily_center {
        text-align: center;
    }

    .table-responsive {
        overflow-x: visible !important;
        scrollbar-width: none !important;
    }

    .laber-type .dropdown-menu .open,
    .agency .dropdown-menu .open {
        width: max-content !important;
    }

    .agency .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 90px !important;
    }
</style>
<div class="col-md-12">
    <hr class="hr-panel-separator" />
</div>

<div class="col-md-12">
    <div class="table-responsive">
        <table class="table dpr-items-table items table-main-dpr-edit has-calculations no-mtop">

            <thead>
                <tr>
                    <th colspan="9" class="daily_report_title">Quality Observation Report</th>
                </tr>
                <tr>
                    <?php
                    $user = get_staff_user_id();
                    $where = 'staffid = ' . $user;
                    $get_login_user_name =  get_staff_list($where);
                    ?>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Raised by :
                            <input type="text" class="form-control" name="raised_by" value="<?= isset($qor_form->raised_by) ? $qor_form->raised_by : $get_login_user_name[0]['name'] ?>" style="width: 80%;">
                        </span>
                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: flex;align-items: baseline;">Issue Date:
                            <div class="form-group" style="margin-left: 13px;">
                                <input type="date" class="form-control" name="issue_date" value="<?= isset($qor_form->issue_date) && !empty($qor_form->issue_date) ? date('Y-m-d', strtotime($qor_form->issue_date)) : date('Y-m-d') ?>">
                            </div>
                        </span>
                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: flex;align-items: baseline;">Observation No.:
                            <div class="form-group" style="margin-left: 13px;">
                                <input type="text" class="form-control" name="observation_no" value="<?= isset($qor_form->observation_no) ? $qor_form->observation_no : '' ?>">
                            </div>
                        </span>
                    </th>
                </tr>

                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Material or Works Involved : <?php echo render_input('material_or_works_involved', '', isset($qor_form->material_or_works_involved) ? $qor_form->material_or_works_involved : '', 'text'); ?></span>

                    </th>
                    <th colspan="4" class="daily_report_head">
                        <?php $vendor_list = get_vendor_list_for_forms(); ?>
                        <span class="daily_report_label" style="display: ruby;">Supplier/Contractor in Charge: <?php echo render_select('supplier_contractor_in_charge', $vendor_list, array('userid', 'company'), '', isset($qor_form->supplier_contractor_in_charge) ? $qor_form->supplier_contractor_in_charge : $vendor_id); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Specification/Drawing Reference : <?php echo render_input('specification_drawing_reference', '', isset($qor_form->specification_drawing_reference) ? $qor_form->specification_drawing_reference : '', 'text'); ?></span>

                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Procedure or ITP Reference: <?php echo render_input('procedure_or_itp_reference', '', isset($qor_form->procedure_or_itp_reference) ? $qor_form->procedure_or_itp_reference : '', 'text'); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="8" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Location : <span class="view_project_name"></span></span>
                    </th>

                </tr>

                <tr>
                    <th colspan="8" class="daily_report_head">
                        <div class="daily_report_label col-md-12" style="display: flex;padding:0px;">
                            <div class="col-md-1" style="padding: 0px;">
                                Observation Description:
                            </div>
                            <div class="col-md-11" style="padding: 0px;">
                                <input
                                    type="text"
                                    name="observation_description"
                                    value="<?php echo isset($qor_form->observation_description) ? htmlspecialchars($qor_form->observation_description) : ''; ?>"
                                    class="form-control" />
                            </div>
                        </div>

                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <div class="daily_report_label col-md-12" style="display: flex; padding: 0px;">
                            <div class="col-md-2" style="padding: 0px;">
                                Design Consultant Recommendation:
                            </div>
                            <div class="col-md-10" style="padding: 0px;">
                                <input
                                    type="text"
                                    name="design_consultant_recommendation"
                                    value="<?php echo isset($qor_form->design_consultant_recommendation) ? htmlspecialchars($qor_form->design_consultant_recommendation) : ''; ?>"
                                    class="form-control" />
                            </div>
                        </div>


                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: flex;align-items: baseline;">Ref. & Date :
                            <div class="form-group">
                                <input type="date" class="form-control" name="ref_date1" value="<?= isset($qor_form->ref_date1) ? date('Y-m-d', strtotime($qor_form->ref_date1)) : '' ?>">
                            </div>
                        </span>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <div class="daily_report_label col-md-12" style="display: flex; padding: 0px;">
                            <div class="col-md-2" style="padding: 0px;">
                                Client Instruction:
                            </div>
                            <div class="col-md-10" style="padding: 0px;">
                                <input
                                    type="text"
                                    name="client_instruction"
                                    value="<?php echo isset($qor_form->client_instruction) ? htmlspecialchars($qor_form->client_instruction) : ''; ?>"
                                    class="form-control" />
                            </div>
                        </div>


                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: flex;align-items: baseline;">Ref. & Date :
                            <div class="form-group">
                                <input type="date" class="form-control" name="ref_date2" value="<?= isset($qor_form->ref_date2) ? date('Y-m-d', strtotime($qor_form->ref_date2)) : '' ?>">
                            </div>
                        </span>
                    </th>
                </tr>
                <tr>
                    <th colspan="1" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Supplier/Contractor’s
                            Proposed Corrective Action : </span>

                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Immediate Action :
                            <?php echo render_input('suppliers_proposed_corrective_action1', '', isset($qor_form->suppliers_proposed_corrective_action1) ? $qor_form->suppliers_proposed_corrective_action1 : '', 'text'); ?></span>

                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Measure to prevent recurrence: <?php echo render_input('suppliers_proposed_corrective_action2', '', isset($qor_form->suppliers_proposed_corrective_action2) ? $qor_form->suppliers_proposed_corrective_action2 : '', 'text'); ?></span>

                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: flex;align-items: baseline;">Date:
                            <div class="form-group">
                                <input type="date" class="form-control" name="proposed_date" value="<?= isset($qor_form) ? date('Y-m-d', strtotime($qor_form->proposed_date)) : '' ?>">
                            </div>
                        </span>
                    </th>
                </tr>

            </thead>


            <tbody>


            </tbody>
        </table>
        <div class="col-md-12 display-flex">
            <label>
                <input type="checkbox" name="approval" value="proceed" class="single-checkbox"
                    <?php echo (isset($qor_form->approval) && $qor_form->approval === 'proceed') ? 'checked' : ''; ?>>
                Approved to Proceed
            </label>
            <br>
            <label style="margin-left: 2%;">
                <input type="checkbox" name="approval" value="proceed_comments" class="single-checkbox"
                    <?php echo (isset($qor_form->approval) && $qor_form->approval === 'proceed_comments') ? 'checked' : ''; ?>>
                Approved to Proceed with Comments
            </label>
            <br>
            <label style="margin-left: 2%;">
                <input type="checkbox" name="approval" value="not_approved" class="single-checkbox"
                    <?php echo (isset($qor_form->approval) && $qor_form->approval === 'not_approved') ? 'checked' : ''; ?>>
                Not Approved
            </label>
        </div>


    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo render_textarea('staff_comments', 'Comments', isset($qor_form) ? $qor_form->staff_comments : '',); ?>
            </div>
        </div>

    </div>
    <table class="table dpr-items-table items table-main-dpr-edit has-calculations no-mtop">
        <thead>
            <tr>
                <th colspan="4" class="daily_report_head">
                    <span class="daily_report_label" style="display: ruby;">Name : <?php echo render_select('staff_name', get_staff_list(), array('staffid', 'name'), '', isset($qor_form->staff_name) ? $qor_form->staff_name : ''); ?></span>

                </th>
                <th colspan="4" class="daily_report_head">
                    <span class="daily_report_label" style="display: flex;align-items: baseline;">Date :
                        <div class="form-group">
                            <input type="date" class="form-control" name="staff_name_date" value="<?= isset($qor_form->staff_name_date) ? date('Y-m-d', strtotime($qor_form->staff_name_date)) : '' ?>">
                        </div>
                    </span>
                </th>
            </tr>
            <tr>
                <th colspan="2" class="daily_report_head">
                    <div class="daily_report_label col-md-12" style="display: flex; align-items: center; padding: 0px;">
                        <div class="col-md-3" style="padding: 0px;">
                            Observation Close-Out:
                        </div>
                        <div class="col-md-9" style="padding: 0px;">
                            <label style="margin-right: 20px;">
                                <input
                                    type="checkbox"
                                    name="close_out"
                                    value="corrective_action"
                                    class="single-checkbox1"
                                    <?php echo (isset($qor_form->close_out) && $qor_form->close_out === 'corrective_action') ? 'checked' : ''; ?>>
                                Corrective Action Accepted
                            </label>

                            <label>
                                <input
                                    type="checkbox"
                                    name="close_out"
                                    value="corrective_action_not_accepted"
                                    class="single-checkbox1"
                                    <?php echo (isset($qor_form->close_out) && $qor_form->close_out === 'corrective_action_not_accepted') ? 'checked' : ''; ?>>
                                Corrective Action Not Accepted
                            </label>
                        </div>
                    </div>
                </th>

                <th colspan="2" class="daily_report_head">
                    <span class="daily_report_label" style="display: flex;align-items: baseline;">Date :
                        <div class="form-group">
                            <input type="date" class="form-control" name="observation_date" value="<?= isset($qor_form->observation_date) ? date('Y-m-d', strtotime($qor_form->observation_date)) : '' ?>">
                        </div>
                    </span>
                </th>
                <th colspan="2" class="daily_report_head">
                    <span class="daily_report_label" style="display: flex;align-items: baseline;">Comments :
                        <div class="form-group">
                            <?php echo render_textarea('comments1', '', isset($qor_form) ? $qor_form->comments1 : '',); ?>
                        </div>
                    </span>
                </th>
            </tr>
        </thead>
    </table>
    <?php $isedit = isset($isedit) && $isedit; ?>
    <div class="table-responsive">
        <div id="sectionsContainer">
            <?php if ($isedit && !empty($qor_form_detail)) : ?>
                <?php foreach ($qor_form_detail as $i => $detail) : ?>
                    <div class="section">
                        <h4>Quality Observtion Photo Section <span class="secIndex"><?php echo $i + 1; ?></span>
                            <a href="javascript:void(0);" class="btn btn-danger removeSection pull-right" style="margin-bottom:10px;"><i class="fa fa-trash"></i></a>
                        </h4>
                        <table class="table qor-items-table items table-main-qor-edit has-calculations no-mtop">
                            <thead>
                                <tr>
                                    <th class="daily_report_head daily_center">Comments</th>
                                    <th class="daily_report_head daily_center">Attachments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="commentRow">
                                    <td>
                                        <textarea name="comments[<?php echo $i; ?>]" class="commentInput form-control" required><?php echo htmlspecialchars($detail['comments']); ?></textarea>
                                    </td>
                                    <td>
                                        <div class="attachmentsList">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="input-group" style="width: 50%; margin-bottom: 10px;">
                                                        <input type="file"
                                                            name="attachments[<?php echo $i + 1; ?>][]"
                                                            extension="<?= str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')) ?>"
                                                            filesize="<?= file_upload_max_size(); ?>"
                                                            class="form-control"
                                                            accept="<?= get_form_form_accepted_mimes(); ?>">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="addAttachmentBtn btn btn-default"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($qor_attachments)) : ?>
                                                <?php foreach ($qor_attachments as $attachment) : ?>
                                                    <?php if ($attachment['form_detail_id'] == $detail['id']) : ?>
                                                        <div class="col-md-12">
                                                            <div class="preview_image" style="margin-bottom: 10px; display: flex; align-items: center;width: 100%;">
                                                                <a href="<?= site_url('uploads/form_attachments/qorattachments/' . $form_id . '/' . $attachment['form_detail_id'] . '/' . $attachment['file_name']); ?>"
                                                                    target="_blank" download style="margin-right: 10px;">
                                                                    <i class="<?= get_mime_class($attachment['filetype']); ?>"></i>
                                                                    <?= $attachment['file_name']; ?>
                                                                </a>
                                                                <a href="<?= admin_url('forms/delete_qor_attachment/' . $attachment['id']); ?>"
                                                                    class="text-danger _delete">
                                                                    <i class="fa fa-remove"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr />
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>

        <button type="button" id="addSectionBtn" class="btn pull-right btn-info" style="margin-bottom: 10px;">Add</button>
    </div>
</div>

<template id="sectionTemplate">
    <div class="section">
        <h4>Quality Observtion Photo Section <span class="secIndex"></span>
            <a href="javascript:void(0);" class="btn btn-danger removeSection pull-right" style="margin-bottom:10px;"><i class="fa fa-trash"></i></a>
        </h4>
        <table class="table qor-items-table items table-main-qor-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th class="daily_report_head daily_center">Comments</th>
                    <th class="daily_report_head daily_center">Attachments</th>
                </tr>
            </thead>
            <tbody>
                <tr class="commentRow">
                    <td>
                        <textarea class="commentInput form-control" required></textarea>
                    </td>
                    <td>
                        <div class="attachmentsList">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group" style="width: 50%; margin-bottom: 10px;">
                                        <input type="file"
                                            extension="<?= str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')) ?>"
                                            filesize="<?= file_upload_max_size(); ?>"
                                            class="form-control"
                                            accept="<?= get_form_form_accepted_mimes(); ?>">
                                        <span class="input-group-btn">
                                            <button type="button" class="addAttachmentBtn btn btn-default"><i class="fa fa-plus"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </td>
                </tr>
            </tbody>
        </table>
        <hr />
    </div>
</template>


<script type="text/javascript">
    $(function() {
        // Initialize sectionCount based on existing rendered PHP sections
        let sectionCount = $('#sectionsContainer .section').length;

        function refreshIndices() {
            $('#sectionsContainer .section').each(function(i) {
                const sectionIndex = i + 1; // Start from 1
                const $sec = $(this);

                $sec.find('.secIndex').text(sectionIndex);
                $sec.find('.commentInput').attr('name', `comments[${sectionIndex}]`);

                $sec.find('.attachmentsList input[type=file]').each(function() {
                    $(this).attr('name', `attachments[${sectionIndex}][]`);
                });
            });
        }

        $('#addSectionBtn').click(function() {
            const $tpl = $($('#sectionTemplate').html());
            $('#sectionsContainer').append($tpl);
            sectionCount++;
            refreshIndices();
        });

        $('#sectionsContainer').on('click', '.removeSection', function() {
            $(this).closest('.section').remove();
            refreshIndices();
        });

        $('#sectionsContainer').on('click', '.addAttachmentBtn', function() {
            const $grp = $(this).closest('.input-group');
            const $clone = $grp.clone();
            $clone.find('input[type=file]').val('');
            $clone.find('button')
                .removeClass('addAttachmentBtn btn-default')
                .addClass('removeAttachmentBtn btn-danger')
                .html('<i class="fa fa-minus"></i>');
            $grp.after($clone);
            refreshIndices();
        });

        $('#sectionsContainer').on('click', '.removeAttachmentBtn', function() {
            $(this).closest('.input-group').remove();
            refreshIndices();
        });

        // If not in edit mode and no sections, initialize with one
        if (sectionCount === 0) {
            $('#addSectionBtn').trigger('click');
        }
    });



    $('.single-checkbox').on('change', function() {
        if (this.checked) {
            $('.single-checkbox').not(this).prop('checked', false);
        }
    });
    $('.single-checkbox1').on('change', function() {
        if (this.checked) {
            $('.single-checkbox1').not(this).prop('checked', false);
        }
    });


    $('#project_id').on('change', function() {
        // var project_id = $(this).val();
        var project_name = $('#project_id option:selected').text();
        $('.view_project_name').html(project_name);
    });


    $(document).ready(function() {
        $('input.number').keypress(function(e) {
            var code = e.which || e.keyCode;

            // Allow backspace, tab, delete, and '/'
            if (code === 8 || code === 9 || code === 46 || code === 47) {
                return true;
            }

            // Allow letters (A-Z, a-z) and numbers (0-9)
            if (
                (code >= 48 && code <= 57) || // Numbers 0-9
                (code >= 65 && code <= 90) || // Uppercase A-Z
                (code >= 97 && code <= 122) // Lowercase a-z
            ) {
                return true;
            }

            // Block all other characters
            return false;
        });
    });
</script>