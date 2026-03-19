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
        <table class="table st-items-table items table-main-dpr-edit has-calculations no-mtop">

            <thead>
                <tr>
                    <th colspan="9" class="daily_report_title">NON-CONFORMANCE REPORT</th>
                </tr>
                <tr>

                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label">Project Name & Address : <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">NCR No :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="ncr_no" name="ncr_no" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->ncr_no) ? $ncr_form->ncr_no : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Department :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('department_ncr', $departments, array('departmentid', 'name'), '', isset($ncr_form->department_ncr) ? $ncr_form->department_ncr : ''); ?></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Activity: (Document / Work) :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="activity" name="activity" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->activity) ? $ncr_form->activity : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">System Ref. :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="system_ref" name="system_ref" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->system_ref) ? $ncr_form->system_ref : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Description of findings (Mention Evidences):</span>
                    </th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <?php $des_of_findings = isset($ncr_form->des_of_findings) ? $ncr_form->des_of_findings : ''; ?>
                        <span class="daily_report_label" style="display: ruby;"><?php echo render_textarea(
                                                                                    'des_of_findings',
                                                                                    '',
                                                                                    $des_of_findings,
                                                                                    ['id' => 'des_of_findings'], // IMPORTANT
                                                                                    [],
                                                                                    '',
                                                                                    'tinymce'
                                                                                );  ?></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="3" class="daily_report_head">
                        <?php $area = isset($ncr_form->area) ? $ncr_form->area : ''; ?>
                        <input type="hidden" id="selected_area" value="<?= $area ?>">
                        <span class="daily_report_label" style="display: ruby;">Location of Non-Conformance:</span><span 
                        class="daily_report_label" style="display: ruby;"><?php echo render_select('area', [], ['id', 'area_name'], '', ''); ?></span>

                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">NCR Type :</span>

                        <label>
                            <input type="radio" name="ncr_type" value="1"
                                <?= (isset($ncr_form->ncr_type) && $ncr_form->ncr_type == 1) ? 'checked' : '' ?>>
                            Major NCR
                        </label>

                        <label style="margin-left:20px;">
                            <input type="radio" name="ncr_type" value="2"
                                <?= (isset($ncr_form->ncr_type) && $ncr_form->ncr_type == 2) ? 'checked' : '' ?>>
                            Minor NCR
                        </label>

                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Reference Document Clause No. :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="reference_document_clause_no" name="reference_document_clause_no" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->reference_document_clause_no) ? $ncr_form->reference_document_clause_no : '' ?>"></span>
                    </th>
                </tr>

                <tr>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Assessor (s) :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="assessor" name="assessor" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->assessor) ? $ncr_form->assessor : '' ?>"></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Assessee :</span><span class="daily_report_label" style="display: ruby;"> Contractor - <?php echo render_select('name_of_contractor', get_vendor_list_for_forms(), array('userid', 'company'), '', isset($ncr_form->name_of_contractor) ? $ncr_form->name_of_contractor : ''); ?></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="date" name="date" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->date) ? $ncr_form->date : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Immediate action to rectify the work (Correction) :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="immediate_action" id="immediate_action"><?php echo isset($ncr_form->immediate_action) ? $ncr_form->immediate_action : '' ?></textarea></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Investigation (Root Cause, Extent of Problem, Impact of Problem) :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="investigation" id="investigation"><?php echo isset($ncr_form->investigation) ? $ncr_form->investigation : '' ?></textarea></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Corrective / Preventive Action to be taken (Elimination of Root Cause):</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="corrective_action" id="corrective_action"><?php echo isset($ncr_form->corrective_action) ? $ncr_form->corrective_action : '' ?></textarea></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Resp :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="resp" name="resp" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->resp) ? $ncr_form->resp : '' ?>"></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Target Date :</span><span class="daily_report_label" style="display: ruby;"><input type="date" id="target_date" name="target_date" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->target_date) ? $ncr_form->target_date : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Follow- up Report:</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="followup_report" name="followup_report" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->followup_report) ? $ncr_form->followup_report : '' ?>"> </span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">NC: </span><span class="daily_report_label" style="display: ruby;">
                            <select class="form-control" name="nc" id="nc" style="width:40%">
                                <option value=""></option>
                                <option value="1" <?php echo isset($ncr_form->nc) && $ncr_form->nc == 1 ? 'selected' : '' ?>>Open</option>
                                <option value="2" <?php echo isset($ncr_form->nc) && $ncr_form->nc == 2 ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Assessor (s):</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="assessor_footer" name="assessor_footer" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->assessor_footer) ? $ncr_form->assessor_footer : '' ?>"> </span>
                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date:</span><span class="daily_report_label" style="display: ruby;"><input type="date" id="footer_date_1" name="footer_date_1" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->footer_date_1) ? $ncr_form->footer_date_1 : '' ?>"> </span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Reviewed By Project In charge:</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="reviewed_by" name="reviewed_by" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->reviewed_by) ? $ncr_form->reviewed_by : '' ?>"> </span>
                    </th>

                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date:</span><span class="daily_report_label" style="display: ruby;"><input type="date" id="footer_date_2" name="footer_date_2" class="form-control" style="width:40%;" value="<?php echo isset($ncr_form->footer_date_2) ? $ncr_form->footer_date_2 : '' ?>"> </span>
                    </th>
                </tr>


            </thead>


            <tbody class="dpr_body">

            </tbody>


        </table>
        <?php $isedit = isset($isedit) && $isedit; ?>
        <div class="table-responsive">
            <div id="sectionsContainer">
                <?php if ($isedit && !empty($ncr_form_detail)) : ?>
                    <?php foreach ($ncr_form_detail as $i => $detail) : ?>
                        <div class="section">
                            <h4>NON-CONFORMANCE Photo Section <span class="secIndex"><?php echo $i + 1; ?></span>
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
                                                                    <a href="<?= site_url('uploads/form_attachments/ncrattachments/' . $form_id . '/' . $attachment['form_detail_id'] . '/' . $attachment['file_name']); ?>"
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
</div>
<template id="sectionTemplate">
    <div class="section">
        <h4>NON-CONFORMANCE Photo Section <span class="secIndex"></span>
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
    $('#project_id').on('change', function() {
        var project_id = $(this).val();
        var project_name = $('#project_id option:selected').text();

        $('.view_project_name').html(project_name);


    });
</script>