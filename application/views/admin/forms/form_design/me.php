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
                    <th colspan="9" class="daily_report_title">CERTIFICATE OF MEDICAL EXAMINATION</th>
                </tr>
                <tr>

                    <th colspan="5" class="daily_report_head">
                         <span class="daily_report_label" style="display: ruby;">Certificate Serial No. :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="sr_no" name="sr_no" class="form-control" style="width:40%;" value="<?php echo isset($me_form->sr_no) ? $me_form->sr_no : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                         <span class="daily_report_label" style="display: ruby;">Date :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="date" name="date" class="form-control" style="width:40%;" value="<?php echo isset($me_form->date) ? $me_form->date : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name_of_employee" name="name_of_employee" class="form-control" style="width:40%;" value="<?php echo isset($me_form->name_of_employee) ? $me_form->name_of_employee : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Identification marks: (1)</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="identification_marks" name="identification_marks" class="form-control" style="width:40%;" value="<?php echo isset($me_form->identification_marks) ? $me_form->identification_marks : '' ?>"></span><br><span class="daily_report_label" style="display: ruby;">(2)</span></span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="identification_marks_2" name="identification_marks_2" class="form-control" style="width:40%;" value="<?php echo isset($me_form->identification_marks_2) ? $me_form->identification_marks_2 : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Father's Name :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="father_name" name="father_name" class="form-control" style="width:40%;" value="<?php echo isset($me_form->father_name) ? $me_form->father_name : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Sex :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="sex" name="sex" class="form-control" style="width:40%;" value="<?php echo isset($me_form->sex) ? $me_form->sex : '' ?>"></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Residence :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="residence" name="residence" class="form-control" style="width:40%;" value="<?php echo isset($me_form->residence) ? $me_form->residence : '' ?>"></span><br><span class="daily_report_label" style="display: ruby;">Son/daughter of</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="son_and_daughter" name="son_and_daughter" class="form-control" style="width:40%;" value="<?php echo isset($me_form->son_and_daughter) ? $me_form->son_and_daughter : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date of birth, if available :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="sub_contractor" name="sub_contractor" class="form-control" style="width:40%;" value="<?php echo isset($me_form->sub_contractor) ? $me_form->sub_contractor : '' ?>"></span><br><span class="daily_report_label" style="display: ruby;">And/or certificate age :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="age_certificate" name="age_certificate" class="form-control" style="width:40%;" value="<?php echo isset($me_form->age_certificate) ? $me_form->age_certificate : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">I hereby certify that I have personally examined (name) :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="personal_examined" id="personal_examined"><?php echo isset($me_form->personal_examined) ? $me_form->personal_examined : '' ?></textarea></span><br>
                        <span class="daily_report_label" style="display: ruby;">son/daughter/wife of :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="personal_examined_1" id="personal_examined_1"><?php echo isset($me_form->personal_examined_1) ? $me_form->personal_examined_1 : '' ?></textarea></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Residing at :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="residing_at" name="residing_at" class="form-control" style="width:40%;" value="<?php echo isset($me_form->residing_at) ? $me_form->residing_at : '' ?>"> </span>
                    </th>
                </tr>

               <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Who is desirous of being employed in building and construction work and that his/her age as nearlyas can be ascertained from my examination is :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="ascertain_examination" id="ascertain_examination"><?php echo isset($me_form->ascertain_examination) ? $me_form->ascertain_examination : '' ?></textarea></span><br>
                        <span class="daily_report_label" style="display: ruby;">Years and that he/she is fit for employment in :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="emloyment_in" id="emloyment_in"><?php echo isset($me_form->emloyment_in) ? $me_form->emloyment_in : '' ?></textarea></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Reason for--</span><br>
                        <span class="daily_report_label" style="display: ruby;">(1) Refusal of certificate :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="refusal_of_certificate" name="refusal_of_certificate" class="form-control" style="width:40%;" value="<?php echo isset($me_form->refusal_of_certificate) ? $me_form->refusal_of_certificate : '' ?>"></span><br>
                        <span class="daily_report_label" style="display: ruby;">(2) Certificate being revoked :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="certificate_being_revoked" name="certificate_being_revoked" class="form-control" style="width:40%;" value="<?php echo isset($me_form->certificate_being_revoked) ? $me_form->certificate_being_revoked : '' ?>"></span>
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