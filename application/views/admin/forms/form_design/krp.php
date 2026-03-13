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
                    <th colspan="9" class="daily_report_title">KEY REQUEST (PERMIT)</th>
                </tr>
                <tr>

                    <th colspan="9" class="daily_report_head">
                         <span class="daily_report_label" style="display: ruby;">Sr. No. :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="sr_no" name="sr_no" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->sr_no) ? $krp_form->sr_no : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Contractor :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('name_of_contractor', get_vendor_list_for_forms(), array('userid', 'company'), '', isset($krp_form->name_of_contractor) ? $krp_form->name_of_contractor : ''); ?></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Key Receiver :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name_of_key_receiver" name="name_of_key_receiver" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->name_of_key_receiver) ? $krp_form->name_of_key_receiver : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Key Reciver Contact Number :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="key_receiver_contact_number" name="key_receiver_contact_number" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->key_receiver_contact_number) ? $krp_form->key_receiver_contact_number : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Key Number :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="key_number" name="key_number" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->key_number) ? $krp_form->key_number : '' ?>"></span>
                    </th>

                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Location :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="location" name="location" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->location) ? $krp_form->location : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="6" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Valid Date And Time :</span>

                        <span class="daily_report_label" style="display: ruby;">From :</span><span class="daily_report_label" style="display: ruby;"> <input type="datetime-local" id="valid_from" name="valid_from" class="form-control" style="width:30%;" value="<?php echo isset($krp_form->valid_from) ? $krp_form->valid_from : '' ?>"></span>

                        <span class="daily_report_label" style="display: ruby;">To :</span><span class="daily_report_label" style="display: ruby;"> <input type="datetime-local" id="valid_to" name="valid_to" class="form-control" style="width:30%;" value="<?php echo isset($krp_form->valid_to) ? $krp_form->valid_to : '' ?>"></span>
                    </th>

                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Total Hours :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="total_hours" name="total_hours" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->total_hours) ? $krp_form->total_hours : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Key Return Date and time :</span><span class="daily_report_label" style="display: ruby;"> <input type="datetime-local" id="key_return_date_time" name="key_return_date_time" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->key_return_date_time) ? $krp_form->key_return_date_time : '' ?>"></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Department :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('department_key_permit', $departments, ['departmentid', 'name'], ''); ?></span>
                    </th>
                </tr>

               <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Job Description :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="job_description" id="job_description"><?php echo isset($krp_form->job_description) ? $krp_form->job_description : '' ?></textarea></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                       <span class="daily_report_label" style="display: ruby;">Name Of Equipment Use :</span><span class="daily_report_label" style="display: ruby;"> <textarea class="daily_report_label" name="name_of_equipment_used" id="name_of_equipment_used"><?php echo isset($krp_form->name_of_equipment_used) ? $krp_form->name_of_equipment_used : '' ?></textarea></span>
                    </th>
                </tr>

                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name of Issuer (Key) PM (Basilius) :</span><span class="daily_report_label" style="display: ruby;">  <?php echo render_select('name_of_issuer', get_staff_list(), array('staffid', 'name'), '', isset($krp_form->name_of_issuer) ? $krp_form->name_of_issuer : ''); ?></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Issuer (Key) Athorizer (Basilius) :</span><span class="daily_report_label" style="display: ruby;"><?php echo render_select('name_of_issuer_athorizer', get_staff_list(), array('staffid', 'name'), '', isset($krp_form->name_of_issuer_athorizer) ? $krp_form->name_of_issuer_athorizer : ''); ?></span>
                    </th>
                     <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Name Of Receiver (Key) (Contractor) :</span><span class="daily_report_label" style="display: ruby;"><input type="text" id="name_of_receiver" name="name_of_receiver" class="form-control" style="width:40%;" value="<?php echo isset($krp_form->name_of_receiver) ? $krp_form->name_of_receiver : '' ?>"></span>
                    </th>
                </tr>

                <tr>
                     <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Permit Closure Athorizer Person Of Basilius :</span><span class="daily_report_label" style="display: ruby;">  <?php echo render_select('permit_closer', get_staff_list(), array('staffid', 'name'), '', isset($krp_form->permit_closer) ? $krp_form->permit_closer : ''); ?></span>
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


    $(document).on('click', '.st-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? st_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.st-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        st_get_item_row_template('newitems[' + item_key + ']', data.name_staff, data.contractor, data.signature, item_key).done(function(output) {
            table_row += output;

            $('.dpr_body').append(table_row);

            init_selectpicker();
            pur_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');

            return true;
        });
        return false;
    });

    function st_get_item_row_template(name, name_staff, contractor, signature, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_st_row_template', {
            name: name,
            name_staff: name_staff,
            contractor: contractor,
            signature: signature,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function st_get_item_preview_values() {
        "use strict";

        var response = {};
        response.name_staff = $('.st-items-table input[name="name_staff"]').val();
        response.contractor = $('.st-items-table select[name="contractor"]').selectpicker('val');
        response.signature = $('.st-items-table input[name="signature"]').val();
        return response;
    }

    function pur_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function st_delete_item(row, itemid, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
                pur_calculate_total();
            }, 50);
        });
        if (itemid && $('input[name="isedit"]').length > 0) {
            $(parent + ' #removed-items').append(hidden_input('removed_items[]', itemid));
        }
    }
</script>