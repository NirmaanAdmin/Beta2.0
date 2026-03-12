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
                    <th colspan="9" class="daily_report_title">Safety Training</th>
                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Project: <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="6" class="daily_report_head">
                        <span class="daily_report_label">Date / Time :
                            <div class="form-group">
                                <input type="datetime-local" class="form-control" name="date" style="width:50%;" value="<?= !empty($st_form->date) ? date('Y-m-d\TH:i', strtotime($st_form->date)) : '' ?>">
                            </div>
                        </span>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Safety Training Given by :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('training_given_by', get_staff_list(), array('staffid', 'name'), '', isset($st_form->training_given_by) ? $st_form->training_given_by : ''); ?></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Number of Participants :</span><span class="daily_report_label" style="display: ruby;"> <input type="text" id="no_of_participants" name="no_of_participants" class="form-control" style="width:40%;" value="<?php echo isset($st_form->no_of_participants) ? $st_form->no_of_participants : '' ?>"></span>
                    </th>

                </tr>
                <tr>
                    <td colspan="4">
                        Following points have been detailed and understood by us :
                    </td>
                </tr>
                <tr>
                    <td>1. PPE</td>
                    <td>4. Hot Works & Gas Cutting</td>
                    <td>7. Working @ Heights</td>
                    <td>10. Housekeeping & Waste Disposal</td>
                </tr>

                <tr>
                    <td>2. Work Permits</td>
                    <td>5. Scaffolds</td>
                    <td>8. Fire Prevention & Protection</td>
                    <td>11. First Aid & Accident Reporting</td>
                </tr>

                <tr>
                    <td>3. Electrical Safety</td>
                    <td>6. SWMS</td>
                    <td>9. Lifting Practice</td>
                    <td>12. Others</td>
                </tr>

                <tr class="main">
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Name</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Contractor Name</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Signature</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"></span>
                    </th>
                </tr>
            </thead>


            <tbody class="dpr_body">
                <?php echo pur_html_entity_decode($st_row_template); ?>
            </tbody>

            <tr>
                <th colspan="2" class="daily_report_head">
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