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

            <input type="hidden" name="action" value="apc">
            <thead>
                <tr>
                    <th colspan="5" class="daily_report_title">Air Pollution Control Checklist</th>
                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Project: <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label">Date: </span><input type="datetime-local" class="form-control" name="date" value="<?= isset($apc_form->date) ? date('Y-m-d\TH:i', strtotime($apc_form->date)) : '' ?>">
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Location: <?php echo render_input('location', '', isset($apc_form->location) ? $apc_form->location : '', 'text', ['style' => 'width:150px;']); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Inspected by: <?php echo render_select('inspected_by', get_staff_list(), array('staffid', 'name'), '', isset($apc_form->inspected_by) ? $apc_form->inspected_by : ''); ?></span>

                    </th>
                </tr>
                <tr class="main">
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">S.No.</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Items</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Status</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Remarks</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Attachment</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php $sr = 1;  
                foreach ($form_items as $key => $value):
                    $id = isset($apc_form_detail) ? $apc_form_detail[$key]['id'] : ''; ?>
                    <tr class="main">
                        <input type="hidden" class="ids" name="items[<?= $sr ?>][id]" value="<?= $id  ?>">
                        <td><?= $sr ?></td>
                        <td style="font-weight: 600;font-size: 16px;"><?= $value['name'] ?></td>
                        <td>
                            <span class="daily_report_label" style="display: ruby;">
                                <?php echo render_select('items[' . $sr . '][status]', get_item_status_listing(), array('id', 'name'), '', isset($apc_form_detail) ? $apc_form_detail[$key]['status'] : ''); ?>
                            </span>
                        </td>
                        <td>
                            <span class="daily_report_label" style="display: ruby;">
                                <?php echo render_input('items[' . $sr . '][remarks]', '', isset($apc_form_detail) ? $apc_form_detail[$key]['remarks'] : '', 'text', ['style' => 'width:150px;']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="attachment_new">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="file"
                                                extension="<?php echo str_replace(['.', ' '], '', get_option('form_attachments_file_extensions')); ?>"
                                                filesize="<?php echo file_upload_max_size(); ?>"
                                                class="form-control" name="items[<?= $sr ?>][attachments_new][<?= $sr ?>]"
                                                accept="<?php echo get_form_form_accepted_mimes(); ?>">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default add_more_attachments_apc" data-item="<?= $sr ?>"
                                                    data-max="<?php echo get_option('maximum_allowed_form_attachments'); ?>"
                                                    type="button"><i class="fa fa-plus"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (isset($apc_attachments) && count($apc_attachments) > 0) {
                                foreach ($apc_attachments as $attachment) {
                                    if ($attachment['form_detail_id'] == $id) {
                                        echo '<div class="col-md-12">';

                                        // Generate the path to the file
                                        $path = get_upload_path_by_type('form') . 'apc_checklist/' . $form_id . '/' . $attachment['form_detail_id'] . '/' . $attachment['file_name'];

                                        // Display the image and delete link
                                        echo '<div class="preview_image" style="margin-bottom: 10px;display: flex;">';
                            ?>
                                        <a href="<?php echo site_url('uploads/form_attachments/apc_checklist/' . $form_id . '/' . $attachment['form_detail_id'] . '/' . $attachment['file_name']); ?>"
                                            class="display-block mbot5" download>
                                            <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?>
                                        </a>
                                        <a href="<?php echo admin_url('forms/delete_apc_attachment/' . $attachment['id']); ?>"
                                            class="text-danger _delete" style="margin-left: 10px;">
                                            <i class="fa fa-remove"></i>
                                        </a>
                            <?php
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                }
                            }
                            ?>

                        </td>
                    </tr>
                <?php $sr++;
                endforeach; ?>
            </tbody>
        </table>


    </div>
</div>

<script type="text/javascript">
    $('#project_id').on('change', function() {
        // var project_id = $(this).val();
        var project_name = $('#project_id option:selected').text();
        $('.view_project_name').html(project_name);
    });
    let addMoreAttachmentsInputKey = 2;

    // Handle adding attachments
    $("body").on("click", ".add_more_attachments_apc", function() {
        if ($(this).hasClass("disabled")) {
            return false;
        }

        const itemIndex = $(this).data("item"); // Fetch the current item index
        if (typeof itemIndex === "undefined") {
            console.error("Item index is undefined. Please ensure the data-item attribute is set correctly.");
            return;
        }

        const parentContainer = $(this).closest(".attachment_new");
        const newAttachment = parentContainer.clone();

        // Update the name attribute with the correct item and attachment index
        newAttachment
            .find("input[type='file']")
            .attr(
                "name",
                `items[${itemIndex}][attachments_new][${addMoreAttachmentsInputKey}]`
            )
            .val("");

        // Replace the "+" button with a "-" button for removing
        newAttachment.find(".fa").removeClass("fa-plus").addClass("fa-minus");
        newAttachment
            .find("button")
            .removeClass("add_more_attachments_apc")
            .addClass("remove_attachment")
            .removeClass("btn-default")
            .addClass("btn-danger");

        // Append the new attachment container after the current one
        parentContainer.after(newAttachment);

        // Increment the attachment key for unique naming
        addMoreAttachmentsInputKey++;
    });

    // Handle removing an attachment
    $("body").on("click", ".remove_attachment", function() {
        // Remove the parent `.attachment_new` container
        $(this).closest(".attachment_new").remove();
        // Reset addMoreAttachmentsInputKey based on the number of existing attachments
        resetAttachmentKeys();
    });

    // Function to recalculate and reset attachment keys
    function resetAttachmentKeys() {
        addMoreAttachmentsInputKey = 1; // Reset the counter
        $(".attachment_new").each(function() {
            const itemIndex = $(this).find(".add_more_attachments_apc").data("item");

            // Update the file input's name with the new sequential key
            $(this)
                .find("input[type='file']")
                .attr(
                    "name",
                    `items[${itemIndex}][attachments_new][${addMoreAttachmentsInputKey}]`
                );

            addMoreAttachmentsInputKey++; // Increment for the next attachment
        });
    }
</script>