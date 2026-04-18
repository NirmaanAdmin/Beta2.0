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
        <table class="table rccb-items-table items table-main-dpr-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="8" class="daily_report_activity">EHS Induction</th>
                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Project Name & Address : <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Contractor Name :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('name_of_contractor', get_vendor_list_for_forms(), array('userid', 'company'), '', isset($rccb_form->name_of_contractor) ? $rccb_form->name_of_contractor : ''); ?></span>
                    </th>

                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Date </span><span class="daily_report_label" style="display: ruby;"> <input type="date" id="date" name="date" class="form-control" style="width:40%;" value="<?php echo isset($rccb_form->date) ? $rccb_form->date : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Location</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">RCCB/ELCB NO</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Brief Details of equipment's connected</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Date of Testing</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Sensitivity of RCCB</span>
                    </th>
                    <th class="daily_report_head daily_center" style="width: 9%;">
                        <span class="daily_report_label">Test Remarks</span>
                    </th>
                    <th class="daily_report_head daily_center" style="width: 9%;">
                        <span class="daily_report_label">Name of the Contractor Electrical Engineer/ Authorized Electrician</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"></span>
                    </th>
                </tr>

            </thead>
            <tbody class="dpr_body">
                <?php echo pur_html_entity_decode($rccb_row_template); ?>
            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
</div>

<script type="text/javascript">
    $(document).on('click', '.rccb-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? rccb_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.wpr-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        rccb_get_item_row_template('newitems[' + item_key + ']', data.location, data.rccb, data.connected, data.date_of_testing, data.sensitivity_of_rccb, data.test_remarks, data.electrical, item_key).done(function(output) {
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

    function rccb_get_item_row_template(name, location, rccb, connected, date_of_testing, sensitivity_of_rccb, test_remarks, electrical, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_rccb_row_template', {
            name: name,
            location: location,
            rccb: rccb,
            connected: connected,
            date_of_testing: date_of_testing,
            sensitivity_of_rccb: sensitivity_of_rccb,
            test_remarks: test_remarks,
            electrical: electrical,
            item_key: item_key
        });
            
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function rccb_get_item_preview_values() {
        "use strict";

        var response = {};
        response.location = $('.rccb-items-table input[name="location"]').val();
        response.rccb = $('.rccb-items-table input[name="rccb"]').val();
        response.connected = $('.rccb-items-table input[name="connected"]').val();
        response.date_of_testing = $('.rccb-items-table input[name="date_of_testing"]').val();
        response.sensitivity_of_rccb = $('.rccb-items-table input[name="sensitivity_of_rccb"]').val();
        response.test_remarks = $('.rccb-items-table input[name="test_remarks"]').val();
        response.electrical = $('.rccb-items-table input[name="electrical"]').val();
        return response;
    }

    function pur_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function rccb_delete_item(row, itemid, parent) {
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