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

    .ppe-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .ppe-box img {
        width: 32px;
        height: 32px;
        display: block;
    }

    .ppe-box input[type="checkbox"] {
        margin-top: 6px;
    }

    .text-center {
        text-align: center;
        vertical-align: middle;
    }
</style>
<div class="col-md-12">
    <hr class="hr-panel-separator" />
</div>

<div class="col-md-12">
    <div class="table-responsive">
        <table class="table wpf-items-table items table-main-dpr-edit has-calculations no-mtop">

            <thead>
                <tr>
                    <th colspan="9" class="daily_report_title">PERMIT TO WORK SYSTEM (PTW)</th>
                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">

                        <span class="daily_report_label">Date :

                            <input type="date" class="form-control" name="date" style="width:30%;" value="<?= !empty($st_form->date) ? date('Y-m-d\TH:i', strtotime($st_form->date)) : '' ?>">

                        </span>
                    </th>
                    <th colspan="6" class="daily_report_head">

                        <span class="daily_report_label">Permit No. :

                            <input type="text" class="form-control" name="permit_no" style="width:50%;" value="<?= !empty($st_form->permit_no) ? $st_form->permit_no : '' ?>">

                        </span>
                    </th>
                </tr>

                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Person Name undertaking work :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="person_name" name="person_name" class="form-control" style="width:40%;" value="<?php echo isset($st_form->person_name) ? $st_form->person_name : '' ?>"></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">EHS PERSON :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="no_of_participants" name="ehs_person" class="form-control" style="width:40%;" value="<?php echo isset($st_form->ehs_person) ? $st_form->ehs_person : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Contractor :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('name_of_contractor', get_vendor_list_for_forms(), array('userid', 'company'), '', isset($st_form->name_of_contractor) ? $st_form->name_of_contractor : ''); ?></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label">Project Name & Address : <span class="view_project_name"></span></span>
                    </th>

                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Risk :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('risk', get_risk_level(), array('id', 'name'), '', isset($st_form->risk) ? $st_form->risk : ''); ?></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">NUMBER OF WORKMEN :</span><span class="daily_report_label" style="display: ruby;"> <input type="number" id="no_of_workmen" name="no_of_workmen" class="form-control" style="width:40%;" value="<?php echo isset($st_form->no_of_workmen) ? $st_form->no_of_workmen : '' ?>"></span>
                    </th>

                </tr>

                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Permit From (Time &Date) :</span><span class="daily_report_label" style="display: ruby;"> <input type="datetime-local" id="permit_from" name="permit_from" class="form-control" style="width:40%;" value="<?php echo isset($st_form->permit_from) ? $st_form->permit_from : '' ?>"></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">To (Time & Date) :</span><span class="daily_report_label" style="display: ruby;"> <input type="datetime-local" id="permit_to" name="permit_to" class="form-control" style="width:40%;" value="<?php echo isset($st_form->permit_to) ? $st_form->permit_to : '' ?>"></span>
                    </th>

                </tr>

                <tr class="ppe-row">
                    <th colspan="2" class="text-center">
                        <div class="ppe-box">
                            <img src="<?php echo site_url('assets/images/wpf/labour.png'); ?>">
                            <input type="checkbox" name="ppe_labour" value="1" <?= !empty($st_form->ppe_labour) ? 'checked' : '' ?>>
                        </div>
                    </th>

                    <th colspan="2" class="text-center">
                        <div class="ppe-box">
                            <img src="<?php echo site_url('assets/images/wpf/safety.png'); ?>">
                            <input type="checkbox" name="ppe_safety" value="1" <?= !empty($st_form->ppe_safety) ? 'checked' : '' ?>>
                        </div>
                    </th>

                    <th colspan="2" class="text-center">
                        <div class="ppe-box">
                            <img src="<?php echo site_url('assets/images/wpf/face-mask.png'); ?>">
                            <input type="checkbox" name="ppe_mask" value="1" <?= !empty($st_form->ppe_mask) ? 'checked' : '' ?>>
                        </div>
                    </th>

                    <th colspan="2" class="text-center">
                        <div class="ppe-box">
                            <img src="<?php echo site_url('assets/images/wpf/high-visibility-vest.png'); ?>">
                            <input type="checkbox" name="ppe_vest" value="1" <?= !empty($st_form->ppe_vest) ? 'checked' : '' ?>>
                        </div>
                    </th>

                    <th colspan="1" class="text-center">
                        <div class="ppe-box">
                            <img src="<?php echo site_url('assets/images/wpf/boot.png'); ?>">
                            <input type="checkbox" name="ppe_boot" value="1" <?= !empty($st_form->ppe_boot) ? 'checked' : '' ?>>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Work to be done :</span><span class="daily_report_label" style="display: ruby;"><textarea class="daily_report_label" name="work_to_be_done" id="work_to_be_done"><?php echo isset($st_form->work_to_be_done) ? $st_form->work_to_be_done : '' ?></textarea></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Tools / equipment to be used (e.g. hand tools, power tools, mechanical tools etc.) :</span><span class="daily_report_label" style="display: ruby;"><textarea class="daily_report_label" name="tools_equipment" id="tools_equipment"><?php echo isset($st_form->tools_equipment) ? $st_form->tools_equipment : '' ?></textarea></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label">Note: For Medium or High-Risk activity refer to the RA & MS for further details. The Predetermined RA must be reviewed for the work that will occur EACH DAY that the permit is open. The Permit Holder/Acceptor must ensure that any changes have been Risk assessed and the Permit and/or associated RA is updated to reflect changing conditions.</span>
                    </th>
                </tr>
                <tr class="main">
                    <th colspan="3" class="daily_report_head daily_center">
                        <span class="daily_report_label">Hazards</span>
                    </th>
                    <th colspan="3" class="daily_report_head daily_center">
                        <span class="daily_report_label">Controls</span>
                    </th>
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Remark</span>
                    </th>
                    <th colspan="1" class="daily_report_head daily_center">
                        <span class="daily_report_label"></span>
                    </th>
                </tr>
            </thead>


            <tbody class="dpr_body">
                <?php echo pur_html_entity_decode($wpf_row_template); ?>
            </tbody>

            <tr>
                <th colspan="4" class="daily_report_head">
                    <span class="daily_report_label" style="display: ruby;">Name :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="name_footer" name="name_footer" class="form-control" style="width:40%;" value="<?php echo isset($st_form->name_footer) ? $st_form->name_footer : '' ?>"></span>
                </th>
                <th colspan="5" class="daily_report_head">
                    <span class="daily_report_label" style="display: ruby;">Designation :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="designation" name="designation" class="form-control" style="width:40%;" value="<?php echo isset($st_form->designation) ? $st_form->designation : '' ?>"></span>
                </th>

            </tr>

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