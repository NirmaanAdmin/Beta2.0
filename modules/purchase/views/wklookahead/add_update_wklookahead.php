<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .holiday-cell {
        font-weight: 600;
        vertical-align: middle !important;
    }
</style>
<div id="wrapper">

    <div class="content">

        <div class="row">

            <div class="col-md-12">

                <div class="panel_s">

                    <div class="panel-body">

                        <h4 class="no-margin">
                            <?php echo html_escape($title); ?>
                        </h4>

                        <hr>

                        <?php echo form_open(
                            current_url(),
                            [
                                'id' => 'wklookahead-form'
                            ]
                        ); ?>

                        <!-- PROJECT -->
                        <div class="row">

                            <div class="col-md-6">

                                <?php
                                $selected_project =
                                    !empty($lookahead)
                                    ? $lookahead->project_id
                                    : '';

                                echo render_select(
                                    'project_id',
                                    $projects,
                                    ['id', 'name'],
                                    'Project',
                                    $selected_project,
                                    [
                                        'required' => true
                                    ]
                                );
                                ?>

                            </div>


                            <!-- WEEK START DATE -->
                            <div class="col-md-6">

                                <?php

                                $week_date =
                                    !empty($lookahead)
                                    ? $lookahead->week_start_date
                                    : date('Y-m-d');

                                ?>

                                <?php echo render_date_input(
                                    'week_start_date',
                                    'Week Starting Date',
                                    $week_date,
                                    [
                                        'required' => true
                                    ]
                                ); ?>

                            </div>

                        </div>

                        <hr>
                        <div class="row">
                            <!-- LOOKAHEAD TABLE -->
                            <div class="col-md-12">
                                <div class="table-responsive s_table">

                                    <table
                                        class="table table-bordered wklookahead-table"
                                        id="wklookahead-table">

                                        <thead id="wklookahead-head">
                                        </thead>

                                        <tbody id="wklookahead-body">

                                            <?php if (!empty($activities)) : ?>

                                                <?php foreach ($activities as $key => $row) : ?>

                                                    <tr>

                                                        <td>

                                                            <input
                                                                type="text"
                                                                name="activity[]"
                                                                class="form-control"
                                                                value="<?php echo html_escape($row->activity); ?>"
                                                                placeholder="Activity">

                                                        </td>

                                                        <td>

                                                            <select
                                                                name="vendor_id[]"
                                                                class="form-control selectpicker"
                                                                data-live-search="true">

                                                                <option value="">
                                                                   
                                                                </option>

                                                                <?php foreach ($vendors as $vendor) : ?>

                                                                    <option
                                                                        value="<?php echo $vendor['userid']; ?>"
                                                                        <?php echo $row->vendor_id == $vendor['userid'] ? 'selected' : ''; ?>>

                                                                        <?php echo html_escape(
                                                                            $vendor['company']
                                                                                ?? $vendor['company']
                                                                                ?? ''
                                                                        ); ?>

                                                                    </option>

                                                                <?php endforeach; ?>

                                                            </select>

                                                        </td>

                                                        <?php for ($i = 1; $i <= 21; $i++) : ?>

                                                            <td class="day-cell">

                                                                <input
                                                                    type="checkbox"
                                                                    name="day_<?php echo $i; ?>[]"
                                                                    value="1"
                                                                    <?php echo !empty($row->{'day_' . $i}) ? 'checked' : ''; ?>>

                                                            </td>

                                                        <?php endfor; ?>

                                                        <td>

                                                            <button
                                                                type="button"
                                                                class="btn btn-danger btn-sm remove-row">
                                                                <i class="fa fa-trash"></i>
                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>
                            </div>


                            <div class="mtop20 col-md-12">

                                <button
                                    type="button"
                                    class="btn btn-default pull-right"
                                    id="add-activity">

                                    <i class="fa fa-plus"></i>
                                    Add Activity

                                </button>

                            </div>
                        </div>

                        <hr>


                        <div class="text-right">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <?php echo $lookahead
                                    ? 'Update'
                                    : 'Save'; ?>

                            </button>

                        </div>


                        <?php echo form_close(); ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php init_tail(); ?>

<script>
    $(function() {

        let existingRows = <?php echo !empty($activities) ? count($activities) : 0; ?>;


        /*
         * FORMAT DATE
         */
        function formatDate(date) {
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();

            return year + '-' + month + '-' + day;
        }


        /*
         * DISPLAY DATE
         */
        function displayDate(date) {
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();

            return day + '-' + month + '-' + year;
        }


        /*
         * GET SELECTED DATE
         */
        function getStartDate() {
            let value = $('#week_start_date').val();

            let selectedDate;

            if (!value) {
                selectedDate = new Date();
            } else {

                /*
                 * Handle DD-MM-YYYY
                 */
                if (value.indexOf('-') !== -1) {

                    let parts = value.split('-');

                    if (parts[0].length === 2) {

                        selectedDate = new Date(
                            parseInt(parts[2]),
                            parseInt(parts[1]) - 1,
                            parseInt(parts[0])
                        );

                    } else {

                        selectedDate = new Date(
                            parseInt(parts[0]),
                            parseInt(parts[1]) - 1,
                            parseInt(parts[2])
                        );
                    }

                } else {

                    selectedDate = new Date(value);
                }
            }

            /*
             * Find Monday of selected date's week
             *
             * JS:
             * Sunday = 0
             * Monday = 1
             * Tuesday = 2
             * ...
             * Saturday = 6
             */
            let day = selectedDate.getDay();

            /*
             * Calculate how many days to go back to Monday
             */
            let difference = day === 0 ? 6 : day - 1;

            selectedDate.setDate(
                selectedDate.getDate() - difference
            );

            /*
             * Remove time portion
             */
            selectedDate.setHours(0, 0, 0, 0);

            return selectedDate;
        }


        /*
         * GENERATE HEADER
         */
        function generateHeader() {
            let startDate = getStartDate();

            let html = '';

            /*
             * First row
             */
            html += '<tr>';

            html += '<th rowspan="2" style="min-width:250px;">';
            html += 'Activities';
            html += '</th>';

            html += '<th rowspan="2" style="min-width:180px;">';
            html += 'Vendor';
            html += '</th>';


            for (let week = 0; week < 3; week++) {

                let weekStart = new Date(startDate);

                weekStart.setDate(
                    startDate.getDate() + (week * 7)
                );

                let weekEnd = new Date(weekStart);

                weekEnd.setDate(
                    weekStart.getDate() + 6
                );

                html += '<th colspan="7" class="text-center">';

                html += 'Week ' + (week + 1);

                html += '<br>';

                html += '<small>';

                html += displayDate(weekStart);

                html += ' - ';

                html += displayDate(weekEnd);

                html += '</small>';

                html += '</th>';
            }


            html += '<th rowspan="2">';

            html += 'Action';

            html += '</th>';

            html += '</tr>';


            /*
             * Second row
             */
            html += '<tr>';

            let days = [
                'M',
                'T',
                'W',
                'T',
                'F',
                'S',
                'S'
            ];


            for (let week = 0; week < 3; week++) {

                for (let day = 0; day < 7; day++) {

                    let currentDate = new Date(startDate);

                    currentDate.setDate(
                        startDate.getDate() +
                        (week * 7) +
                        day
                    );

                    html += '<th class="text-center day-heading">';

                    html += days[day];

                    html += '<br>';

                    html += '<small>';

                    html += currentDate.getDate();

                    html += '</small>';

                    html += '</th>';
                }
            }

            html += '</tr>';

            $('#wklookahead-head').html(html);
        }


        /*
         * CREATE ACTIVITY ROW
         */
        function createRow() {
            let row = $('<tr>');

            /*
             * Activity
             */
            row.append(`
            <td>
                <input
                    type="text"
                    name="activity[]"
                    class="form-control"
                    placeholder="Activity"
                    required
                >
            </td>
        `);


            /*
             * Vendor
             */
            let vendorOptions = `
            <option value=""></option>
        `;

            <?php foreach ($vendors as $vendor) : ?>

                vendorOptions += `
                <option value="<?php echo $vendor['userid']; ?>">
                    <?php echo addslashes(
                        $vendor['company']
                            ?? $vendor['company']
                            ?? ''
                    ); ?>
                </option>
            `;

            <?php endforeach; ?>


            row.append(`
            <td>
                <select
                    name="vendor_id[]"
                    class="form-control selectpicker"
                    data-live-search="true"
                >
                    ${vendorOptions}
                </select>
            </td>
        `);


            /*
             * 21 Days
             */
            for (let i = 1; i <= 21; i++) {

                /*
                 * Sunday = 7, 14, 21
                 */
                let isSunday = (
                    i === 7 ||
                    i === 14 ||
                    i === 21
                );

                if (isSunday) {

                    row.append(`
            <td class="text-center day-cell holiday-cell">
                <strong>H</strong>
            </td>
        `);

                } else {

                    row.append(`
            <td class="text-center day-cell">

                <input
                    type="checkbox"
                    name="day_${i}[]"
                    value="1"
                >

            </td>
        `);
                }
            }


            /*
             * Delete
             */
            row.append(`
            <td>

                <button
                    type="button"
                    class="btn btn-danger btn-sm remove-row"
                >

                    <i class="fa fa-trash"></i>

                </button>

            </td>
        `);


            $('#wklookahead-body').append(row);

            $('.selectpicker').selectpicker('refresh');
        }


        /*
         * ADD ROW
         */
        $('#add-activity').on('click', function() {

            createRow();

        });


        /*
         * REMOVE ROW
         */
        $(document).on(
            'click',
            '.remove-row',
            function() {

                $(this)
                    .closest('tr')
                    .remove();

            }
        );


        /*
         * DATE CHANGE
         */
        $('#week_start_date').on(
            'change',
            function() {

                generateHeader();

            }
        );


        /*
         * INITIALIZE
         */
        generateHeader();


        /*
         * If Add mode and no activities
         */
        if (existingRows === 0) {

            createRow();

        }

    });

    $('#wklookahead-form').on('submit', function(e) {

        let date = $('#week_start_date').val();
        let project = $('#project_id').val();

        /*
         * Date validation
         */
        if (!date) {

            e.preventDefault();

            alert_float(
                'danger',
                'Please select Week Starting Date.'
            );

            $('#week_start_date').focus();

            return false;
        }


        /*
         * Project validation
         */
        if (!project) {

            e.preventDefault();

            alert_float(
                'danger',
                'Please select Project.'
            );

            $('#project_id').selectpicker('toggle');

            return false;
        }


        /*
         * At least one activity row
         */
        let rows = $('#wklookahead-body tr');

        if (rows.length === 0) {

            e.preventDefault();

            alert_float(
                'danger',
                'Please add at least one activity.'
            );

            return false;
        }


        /*
         * Check activity names
         */
        let valid = true;

        $('input[name="activity[]"]').each(function() {

            if ($.trim($(this).val()) === '') {

                valid = false;

                $(this).addClass('has-error');

                $(this).focus();

                return false;
            }

        });


        if (!valid) {

            e.preventDefault();

            alert_float(
                'danger',
                'Please enter activity name.'
            );

            return false;
        }


        return true;
    });
</script>