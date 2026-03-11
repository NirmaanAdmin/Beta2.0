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

    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .form-grid .form-group {
        width: 48%;
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
                    <th colspan="8" class="daily_report_title">Accident Report Format</th>
                </tr>

                <tr>
                    <th colspan="4" class="daily_report_title">General Information</th>
                    <th colspan="4" class="daily_report_title">Injured Person(s) Details</th>
                </tr>

                <tr>
                    <!-- General Information -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('report_no', 'Report No.:', isset($arf_form->report_no) ? $arf_form->report_no : '', 'text'); ?>

                            <?php echo render_input('date_of_report', 'Date of Report:', isset($arf_form->date_of_report) ? $arf_form->date_of_report : '', 'date'); ?>

                            <?php echo render_input('date_time_accident', 'Date & Time of Accident', isset($arf_form->date_time_accident) ? $arf_form->date_time_accident : '', 'datetime-local'); ?>

                            <?php echo render_input('location', 'Exact Location:', isset($arf_form->location) ? $arf_form->location : '', 'text'); ?>

                            <?php echo render_input('department_site', 'Department / Site:', isset($arf_form->department_site) ? $arf_form->department_site : '', 'text'); ?>

                        </div>
                    </td>

                    <!-- Injured Person Details -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('name', 'Name:', isset($arf_form->name) ? $arf_form->name : '', 'text'); ?>

                            <?php echo render_input('age_gender', 'Age / Gender:', isset($arf_form->age_gender) ? $arf_form->age_gender : '', 'text'); ?>

                            <?php echo render_input('designation', 'Designation:', isset($arf_form->designation) ? $arf_form->designation : '', 'text'); ?>

                            <?php echo render_input('emp_id', 'Employee ID:', isset($arf_form->emp_id) ? $arf_form->emp_id : '', 'text'); ?>

                            <?php echo render_input('nature_of_emp', 'Nature of Employment (Permanent/Contract):', isset($arf_form->nature_of_emp) ? $arf_form->nature_of_emp : '', 'text'); ?>

                        </div>
                    </td>
                </tr>
                <tr>
                    <th colspan="4" class="daily_report_title">Description of Accident</th>
                    <th colspan="4" class="daily_report_title">Injury / Damage Details</th>
                </tr>

                <tr>
                    <!-- General Information -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('detailed_description', 'Detailed description of what happened: ', isset($arf_form->detailed_description) ? $arf_form->detailed_description : '', 'text'); ?>

                            <?php echo render_input('equipment_tools_involved', 'Equipment / tools involved:', isset($arf_form->equipment_tools_involved) ? $arf_form->equipment_tools_involved : '', 'text'); ?>

                            <?php echo render_input('weather_environmental_conditions', 'Weather / environmental conditions (if relevant):', isset($arf_form->weather_environmental_conditions) ? $arf_form->weather_environmental_conditions : '', 'text'); ?>

                        </div>
                    </td>

                    <!-- Injured Person Details -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('type_of_injury_damage', 'Type of injury or damage: ', isset($arf_form->type_of_injury_damage) ? $arf_form->type_of_injury_damage : '', 'text'); ?>

                            <?php echo render_input('body_part_affected', 'Body part affected:', isset($arf_form->body_part_affected) ? $arf_form->body_part_affected : '', 'text'); ?>

                            <?php echo render_input('severity', 'Severity (Minor / Major / Fatal):', isset($arf_form->severity) ? $arf_form->severity : '', 'text'); ?>

                            <?php echo render_input('property_damage', 'Property damage (if any):', isset($arf_form->property_damage) ? $arf_form->property_damage : '', 'text'); ?>

                        </div>
                    </td>
                </tr>

                <tr>
                    <th colspan="4" class="daily_report_title">Immediate Action & First Aid</th>
                    <th colspan="4" class="daily_report_title">Witness Details</th>
                </tr>

                <tr>
                    <!-- General Information -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('first_aid_provided', 'First aid provided:', isset($arf_form->first_aid_provided) ? $arf_form->first_aid_provided : '', 'text'); ?>

                            <?php echo render_input('medical_treatment_details', 'Medical treatment details:', isset($arf_form->medical_treatment_details) ? $arf_form->medical_treatment_details : '', 'text'); ?>

                            <?php echo render_input('hospital_clinic_name', 'Hospital / Clinic name:', isset($arf_form->hospital_clinic_name) ? $arf_form->hospital_clinic_name : '', 'text'); ?>

                            <?php echo render_input('time_taken_to_respond', 'Time taken to respond:', isset($arf_form->time_taken_to_respond) ? $arf_form->time_taken_to_respond : '', 'text'); ?>

                        </div>
                    </td>

                    <!-- Injured Person Details -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('name_s', 'Name(s):', isset($arf_form->name_s) ? $arf_form->name_s : '', 'text'); ?>

                            <?php echo render_input('designation', 'Designation:', isset($arf_form->designation) ? $arf_form->designation : '', 'text'); ?>

                            <?php echo render_input('contact_information', 'Contact information:', isset($arf_form->contact_information) ? $arf_form->contact_information : '', 'text'); ?>

                        </div>
                    </td>
                </tr>

                <tr>
                    <th colspan="4" class="daily_report_title">Root Cause Analysis </th>
                    <th colspan="4" class="daily_report_title">Corrective & Preventive Actions</th>
                </tr>

                <tr>
                    <!-- General Information -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('immediate_cause', 'Immediate cause:', isset($arf_form->immediate_cause) ? $arf_form->immediate_cause : '', 'text'); ?>

                            <?php echo render_input('underlying_cause', 'Underlying cause:', isset($arf_form->underlying_cause) ? $arf_form->underlying_cause : '', 'text'); ?>

                            <?php echo render_input('human_equipment_environmental_factors', 'Human / Equipment / Environmental factors:', isset($arf_form->human_equipment_environmental_factors) ? $arf_form->human_equipment_environmental_factors : '', 'text'); ?>

                        </div>
                    </td>

                    <!-- Injured Person Details -->
                    <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('actions_to_prevent_recurrence', 'Actions to prevent recurrence:', isset($arf_form->actions_to_prevent_recurrence) ? $arf_form->actions_to_prevent_recurrence : '', 'text'); ?>

                            <?php echo render_input('responsible_person', 'Responsible person:', isset($arf_form->responsible_person) ? $arf_form->responsible_person : '', 'text'); ?>

                            <?php echo render_input('target_date', 'Target date: ', isset($arf_form->target_date) ? $arf_form->target_date : '', 'date'); ?>

                        </div>
                    </td>
                </tr>

                <tr>
                    <th colspan="4" class="daily_report_title">Reporting & Approval</th>
                </tr>

                <td colspan="4">
                        <div class="form-grid">

                            <?php echo render_input('reported_by', 'Reported by (Name & Signature)', isset($arf_form->reported_by) ? $arf_form->reported_by : '', 'text'); ?>

                            <?php echo render_input('reviewed_by', 'Reviewed by:', isset($arf_form->reviewed_by) ? $arf_form->reviewed_by : '', 'text'); ?>

                            <?php echo render_input('approved_by', 'Approved by: ', isset($arf_form->approved_by) ? $arf_form->approved_by : '', 'text'); ?>

                            <?php echo render_input('approved_date', 'Date: ', isset($arf_form->approved_date) ? $arf_form->approved_date : '', 'date'); ?>

                        </div>
                    </td>

            </thead>

            <tbody></tbody>

        </table>

    </div>

</div>

<script type="text/javascript">
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