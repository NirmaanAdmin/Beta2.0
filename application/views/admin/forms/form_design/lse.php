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

            <thead>
                <tr>
                    <th colspan="4" class="daily_report_title">Lifting & Erection</th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        FN:
                        <input type="text" class="form-control" name="fn"
                            value="<?= isset($lse_form->fn) ? $lse_form->fn : '' ?>" style="width: 40%;">
                    </th>

                    <th class="daily_report_head">
                        Rev. No:
                        <input type="text" class="form-control" name="rev_no"
                            value="<?= isset($lse_form->rev_no) ? $lse_form->rev_no : '' ?>">
                    </th>

                    <th class="daily_report_head">
                        Date:
                        <input type="date" class="form-control" name="date"
                            value="<?= isset($lse_form->date) ? $lse_form->date : '' ?>">
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        Name of Project:
                        <span class="view_project_name"></span>
                    </th>

                    <th colspan="2" class="daily_report_head">
                        Work Location:
                        <?php echo render_input('location', '', isset($lse_form->location) ? $lse_form->location : ''); ?>
                    </th>
                </tr>

                <tr>
                    <th colspan="2" class="daily_report_head">
                        Sub-Contractor:
                        <input type="text" class="form-control" name="sub_contractor"
                            value="<?= isset($lse_form->sub_contractor) ? $lse_form->sub_contractor : '' ?>" style="width: 40%;">
                    </th>

                    <th class="daily_report_head">
                        Date & Time:
                        <?php echo render_input('datetime', '', isset($lse_form->datetime) ? $lse_form->datetime : '', 'datetime-local'); ?>
                    </th>

                    <th class="daily_report_head">
                        Shift:
                        <select name="shift" class="form-control">
                            <option value=""></option>
                            <option value="1" <?= (isset($lse_form->shift) && $lse_form->shift == 1) ? 'selected' : '' ?>>Day</option>
                            <option value="2" <?= (isset($lse_form->shift) && $lse_form->shift == 2) ? 'selected' : '' ?>>Night</option>
                        </select>
                    </th>
                </tr>

                <tr>
                    <th class="daily_report_head text-center">S.No.</th>
                    <th class="daily_report_head text-center">Description</th>
                    <th class="daily_report_head text-center">Options</th>
                    <th class="daily_report_head text-center">Remarks</th>
                </tr>
            </thead>

            <tbody>
                <?php $sr = 1;
                foreach ($form_items as $key => $value):
                    $id = isset($lse_form_detail) ? $lse_form_detail[$key]['id'] : ''; ?>

                    <tr>
                        <td>
                            <?= $sr ?>
                            <input type="hidden" name="items[<?= $sr ?>][id]" value="<?= $id ?>">
                        </td>

                        <td style="font-weight:600;"><?= $value['name'] ?></td>

                        <td>
                            <?php echo render_select(
                                'items[' . $sr . '][status]',
                                get_item_status_lse_listing(),
                                ['id', 'name'],
                                '',
                                isset($lse_form_detail) ? $lse_form_detail[$key]['description'] : ''
                            ); ?>
                        </td>

                        <td>
                            <span class="daily_report_label" style="display: ruby;">
                                <?php echo render_textarea('items[' . $sr . '][remarks]', '', isset($lse_form_detail) ? $lse_form_detail[$key]['remarks'] : '',  ['style' => 'width:150px;height:35px']); ?>
                            </span>
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
</script>