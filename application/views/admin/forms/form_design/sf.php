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
                    <th colspan="9" class="daily_report_title">SCREENING FORM</th>
                </tr>
                <tr>

                    <th colspan="5" class="daily_report_head">
                         <span class="daily_report_label" style="display: ruby;">Sr. No. :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="sr_no" name="sr_no" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->sr_no) ? $sf_form->sr_no : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                         <span class="daily_report_label" style="display: ruby;">Date :</span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="date" name="date" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->date) ? $sf_form->date : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Employee :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name_of_employee" name="name_of_employee" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->name_of_employee) ? $sf_form->name_of_employee : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Gender :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="gender" name="gender" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->gender) ? $sf_form->gender : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Age :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="age" name="age" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->age) ? $sf_form->age : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Designation :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="designation" name="designation" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->designation) ? $sf_form->designation : '' ?>"></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Company :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name_of_company" name="name_of_company" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->name_of_company) ? $sf_form->name_of_company : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Company If Sub Contractor :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="sub_contractor" name="sub_contractor" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->sub_contractor) ? $sf_form->sub_contractor : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="6" class="daily_report_head">

                        <span class="daily_report_label" style="display: ruby;">Employee Aadhar Number :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="aadhar_number" name="aadhar_number" class="form-control" style="width:30%;" value="<?php echo isset($sf_form->aadhar_number) ? $sf_form->aadhar_number : '' ?>"></span>

                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Employee Name & Address :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="emp_name" name="emp_name" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->emp_name) ? $sf_form->emp_name : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Present Address :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="present_address" id="present_address"><?php echo isset($sf_form->present_address) ? $sf_form->present_address : '' ?></textarea></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Mobile Number :</span><span class="daily_report_label" style="display: ruby;"><input type="number" id="mobile_number" name="mobile_number" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->mobile_number) ? $sf_form->mobile_number : '' ?>"> </span>
                    </th>
                </tr>

               <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Parmanent Address :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="parmanent_address" id="parmanent_address"><?php echo isset($sf_form->parmanent_address) ? $sf_form->parmanent_address : '' ?></textarea></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                       <span class="daily_report_label" style="display: ruby;">Mobile Number :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="mobile_number_2" name="mobile_number_2" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->mobile_number_2) ? $sf_form->mobile_number_2 : '' ?>"></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Relation Phone :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="relation_phone" name="relation_phone" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->relation_phone) ? $sf_form->relation_phone : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                       <span class="daily_report_label" style="display: ruby;">What Relation :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="relation" name="relation" class="form-control" style="width:40%;" value="<?php echo isset($sf_form->relation) ? $sf_form->relation : '' ?>"></span>
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