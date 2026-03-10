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

    img.images_w_table {
        width: 116px;
        height: 73px;
    }
</style>
<div class="col-md-12">
    <hr class="hr-panel-separator" />
</div>
<?php echo form_hidden('isedit'); ?>
<div class="col-md-12 invoice-item">
    <div class="table-responsive">
        <table class="table wpr-items-table items table-main-dpr-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="13" class="daily_report_activity">Work Permit Register</th>
                </tr>
                <tr>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Permit No.</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Date Issued</span>
                    </th>
                    <th rowspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Type of Work</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Location / Area</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Contractor / Agency</span>
                    </th>
                    <th class="daily_report_head daily_center" style="width: 9%;">
                        <span class="daily_report_label">Person-in-Charge (PIC)</span>
                    </th>
                    <th class="daily_report_head daily_center" style="width: 9%;">
                        <span class="daily_report_label">Start Time</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">End Time</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Risk Level</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Safety Measures Implemented</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Permit Status</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Remarks</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"></span>
                    </th>
                </tr>
            </thead>
            <tbody class="dpr_body">
                <?php echo pur_html_entity_decode($wpr_row_template); ?>
            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
</div>

<script type="text/javascript">
    $(document).on('click', '.wpr-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? wpr_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.wpr-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        wpr_get_item_row_template('newitems[' + item_key + ']', data.permit_no, data.date_issued, data.type_of_work, data.area, data.agency, data.pic, data.start_time, data.end_time, data.risk_level, data.safety_measures, data.permit_status, data.remarks, item_key).done(function(output) {
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

    function wpr_get_item_row_template(name, permit_no, date_issued, type_of_work, area, agency, pic, start_time, end_time, risk_level, safety_measures, permit_status, remarks, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_wpr_row_template', {
            name: name,
            permit_no: permit_no,
            date_issued: date_issued,
            type_of_work: type_of_work,
            area: area,
            agency: agency,
            pic: pic,
            start_time: start_time,
            end_time: end_time,
            risk_level: risk_level,
            safety_measures: safety_measures,
            permit_status: permit_status,
            remarks: remarks,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function wpr_get_item_preview_values() {
        "use strict";

        var response = {};
        response.permit_no = $('.wpr-items-table input[name="permit_no"]').val();
        response.date_issued = $('.wpr-items-table input[name="date_issued"]').val();
        response.type_of_work = $('.wpr-items-table input[name="type_of_work"]').val();
        response.area = $('.wpr-items-table input[name="area"]').val();
        response.agency = $('.wpr-items-table select[name="agency"]').selectpicker('val');
        response.pic = $('.wpr-items-table input[name="pic"]').val();
        response.start_time = $('.wpr-items-table input[name="start_time"]').val();
        response.end_time = $('.wpr-items-table input[name="end_time"]').val();
        response.risk_level = $('.wpr-items-table select[name="risk_level"]').selectpicker('val');
        response.safety_measures = $('.wpr-items-table input[name="safety_measures"]').val();
        response.permit_status = $('.wpr-items-table select[name="permit_status"]').selectpicker('val');
        response.remarks = $('.wpr-items-table textarea[name="remarks"]').val();

        return response;
    }

    function pur_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function wpr_delete_item(row, itemid, parent) {
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
    $(document).on('submit', '#new_form_form', function(e) {
        "use strict";

        var formType = $('select[name="form_type"]').val();

        if (formType != 'wpr') {
            return true;
        }

        var isValid = true;
        var rows = $('.dpr_body tr');
        var submitBtn = $(this).find('button[type="submit"]');

        if (rows.length <= 1) {

            alert_float('warning', 'Please add at least one Work Permit record.');

            submitBtn.button('reset').prop('disabled', false);

            e.preventDefault();
            return false;
        }

        rows.slice(1).each(function() {

            var row = $(this);

            row.find('input:visible:enabled, textarea:visible:enabled').each(function() {

                var value = $.trim($(this).val());

                if (value === '') {
                    isValid = false;
                    $(this).css('border', '1px solid red');
                } else {
                    $(this).css('border', '');
                }

            });

            row.find('select.selectpicker:enabled').each(function() {

                var value = $(this).val();

                if (!value || value.length === 0) {

                    isValid = false;

                    $(this)
                        .closest('.bootstrap-select')
                        .find('.dropdown-toggle')
                        .css('border', '1px solid red');

                } else {

                    $(this)
                        .closest('.bootstrap-select')
                        .find('.dropdown-toggle')
                        .css('border', '');

                }

            });

        });

        if (!isValid) {

            alert_float('danger', 'Please fill all required fields in Work Permit Register.');

            submitBtn.button('reset').prop('disabled', false);

            e.preventDefault();
            return false;
        }

    });
</script>