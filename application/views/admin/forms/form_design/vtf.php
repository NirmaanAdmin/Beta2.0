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
                    <th colspan="9" class="daily_report_title">CERTIFICATE OF FITNESS FOR HEIGHT PASSAGE</th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="date" name="date" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->date) ? $vtf_form->date : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">This is to certify that Mr :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name_of_employee" name="name_of_employee" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->name_of_employee) ? $vtf_form->name_of_employee : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">son of</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="son_of" name="son_of" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->son_of) ? $vtf_form->son_of : '' ?>"></span><br><span class="daily_report_label" style="display: ruby;">
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">working under M/s </span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="working_under" name="working_under" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->working_under) ? $vtf_form->working_under : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <?php $area = isset($vtf_form->area) ? $vtf_form->area : ''; ?>
                        <input type="hidden" id="selected_area" value="<?= $area ?>">
                        <span class="daily_report_label" style="display: ruby;">Area:</span><span
                            class="daily_report_label" style="display: ruby;"><?php echo render_select('area', [], ['id', 'area_name'], '', ''); ?></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Department :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('department_vtf', $departments, array('departmentid', 'name'), '', isset($vtf_form->department_vtf) ? $vtf_form->department_vtf : ''); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        EXAMINATION:
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Weight :</span><span class="daily_report_label" style="display: ruby;"><input type="number" id="weight" name="weight" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->weight) ? $vtf_form->weight : '' ?>"> Kg </span><br>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Height :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="height" name="height" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->height) ? $vtf_form->height : '' ?>"> CM </span>
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Vision :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="vision" name="vision" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->vision) ? $vtf_form->vision : '' ?>"></span><br>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Colour Vision :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="color_vision" name="color_vision" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->color_vision) ? $vtf_form->color_vision : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Pulse :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="pulse" name="pulse" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->pulse) ? $vtf_form->pulse : '' ?>"></span><br>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">BP :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="bp" name="bp" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->bp) ? $vtf_form->bp : '' ?>"></span><br>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Blood Group :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="blood_group" name="blood_group" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->blood_group) ? $vtf_form->blood_group : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Vertigo/Fits :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="vertigo_fits" name="vertigo_fits" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->vertigo_fits) ? $vtf_form->vertigo_fits : '' ?>"></span><br>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Gait :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="gait" name="gait" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->gait) ? $vtf_form->gait : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Giddiness :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="giddiness" name="giddiness" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->giddiness) ? $vtf_form->giddiness : '' ?>"></span><br>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">other height related disease (If any) :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="other_height_related_disease" name="other_height_related_disease" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->other_height_related_disease) ? $vtf_form->other_height_related_disease : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Hearing :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="hearing" name="hearing" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->hearing) ? $vtf_form->hearing : '' ?>"></span><br>
                    </th>
                </tr>
                <tr class="main">
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Parameters</span>
                    </th>
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Before Test</span>
                    </th>
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">After Test</span>
                    </th>
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Deviation Observed</span>
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Blood Pressure</span>
                    </td>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label daily_center"><input type="text" id="blood_p_be" name="blood_p_be" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->blood_p_be) ? $vtf_form->blood_p_be : '' ?>"></span><br>
                    </td>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label daily_center"><input type="text" id="blood_p_af" name="blood_p_af" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->blood_p_af) ? $vtf_form->blood_p_af : '' ?>"></span><br>
                    </td>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label"><input type="text" id="blood_p_dev" name="blood_p_dev" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->blood_p_dev) ? $vtf_form->blood_p_dev : '' ?>"></span><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Pulse</span>
                    </td>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label"><input type="text" id="pulse_before_test" name="pulse_before_test" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->pulse_before_test) ? $vtf_form->pulse_before_test : '' ?>"></span><br>
                    </td>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label"><input type="text" id="pulse_after_test" name="pulse_after_test" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->pulse_after_test) ? $vtf_form->pulse_after_test : '' ?>"></span><br>
                    </td>
                    <td colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label"><input type="text" id="pulse_deviation_observed" name="pulse_deviation_observed" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->pulse_deviation_observed) ? $vtf_form->pulse_deviation_observed : '' ?>"></span><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" class="daily_report_head daily_center">
                        <span class="daily_report_label">HEIGHT PASS ISSUE DETAILS</span>
                    </td>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">No. :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="no" name="no" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->no) ? $vtf_form->no : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="date_pass" name="date_pass" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->date_pass) ? $vtf_form->date_pass : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <td colspan="9" class="daily_report_head daily_center">
                        <span class="daily_report_label">This is to certify that the under mentioned person has been permitted to work at height.</span>
                    </td>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name" name="name" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->name) ? $vtf_form->name : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Age :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="age" name="age" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->age) ? $vtf_form->age : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Father's Name :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="father_name" name="father_name" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->father_name) ? $vtf_form->father_name : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Designation :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="designation" name="designation" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->designation) ? $vtf_form->designation : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Mobile No :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="mobile_no" name="mobile_no" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->mobile_no) ? $vtf_form->mobile_no : '' ?>"></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Valid from :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="valid_from" name="valid_from" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->valid_from) ? $vtf_form->valid_from : '' ?>"></span>
                        <span class="daily_report_label" style="display: ruby;">To :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="valid_to" name="valid_to" class="form-control" style="width:40%;" value="<?php echo isset($vtf_form->valid_to) ? $vtf_form->valid_to : '' ?>"></span>
                    </th>
                </tr>
            </thead>


            <tbody class="dpr_body">

            </tbody>


        </table>

    </div>
    <div id="removed-items"></div>
</div>

<script type="text/javascript">
    $('#project_id').on('change', function() {
        // var project_id = $(this).val();
        var project_name = $('#project_id option:selected').text();
        $('.view_project_name').html(project_name);
    });
</script>