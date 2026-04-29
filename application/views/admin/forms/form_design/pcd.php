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
                    <th colspan="8" class="daily_report_activity">Pest Control Details</th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label">Project Name & Address : <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="5" class="daily_report_head">
                        Month & Year:
                        <input type="month"
                            id="date"
                            name="date"
                            class="form-control"
                            style="width:40%;"
                            value="<?php echo date('Y-m'); ?>"
                            readonly>
                    </th>

                </tr>


            </thead>
            <tbody class="dpr_body">

            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
    <div class="horizontal-scrollable-tabs preview-tabs-top">
        <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                <li role="presentation" class="active">
                    <a href="#ground_floor" aria-controls="ground_floor" role="tab" data-toggle="tab">
                        <?php echo _l('Ground Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#ug_floor" aria-controls="ug_floor" role="tab" data-toggle="tab">
                        <?php echo _l('UG Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#service_floor" aria-controls="service_floor" role="tab" data-toggle="tab">
                        <?php echo _l('Service Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#first_floor" aria-controls="first_floor" role="tab" data-toggle="tab">
                        <?php echo _l('First Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#second_floor" aria-controls="second_floor" role="tab" data-toggle="tab">
                        <?php echo _l('Second Floor'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 active" id="ground_floor">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table ground-floor-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Treatment Type</th>
                                <th>Chemical used</th>
                                <th>Area in charge</th>
                                <th>Technician</th>
                            </tr>
                        </thead>
                        <tbody class="ground_floor_body">
                            <?php
                            $form_items = get_ground_floor_items();
                            $sr = 1;

                            foreach ($form_items as $key => $value):
                                $id = isset($pcd_form_detail) ? $pcd_form_detail[$key]['id'] : '';
                            ?>

                                <tr>
                                    <!-- Location -->
                                    <td>
                                        <?= $value['name'] ?>
                                        <input type="hidden" name="groundflooritems[<?= $sr ?>][id]" value="<?= $id ?>">
                                        <input type="hidden" name="groundflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                    </td>

                                    <!-- Treatment Type -->
                                    <td>
                                        <input type="text"
                                            name="groundflooritems[<?= $sr ?>][treatment_type]"
                                            value="<?=  $pcd_form_detail[$key]['treatment_type'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Chemical used -->
                                    <td>
                                        <input type="text"
                                            name="groundflooritems[<?= $sr ?>][chemical_used]"
                                            value="<?=  $pcd_form_detail[$key]['chemical_used'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Area in charge -->
                                    <td>
                                        <input type="text"
                                            name="groundflooritems[<?= $sr ?>][area_in_charge]"
                                            value="<?=  $pcd_form_detail[$key]['area_in_charge'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Technician -->
                                    <td>
                                        <input type="text"
                                            name="groundflooritems[<?= $sr ?>][technician]"
                                            value="<?=  $pcd_form_detail[$key]['technician'] ?>"
                                            class="form-control">
                                    </td>
                                </tr>

                            <?php
                                $sr++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="ug_floor">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table ug-floor-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Treatment Type</th>
                                <th>Chemical used</th>
                                <th>Area in charge</th>
                                <th>Technician</th>
                            </tr>
                        </thead>
                        <tbody class="ug_floor_body">
                            <?php
                            $form_items = get_ug_floor_items();
                            $sr = 1;

                            foreach ($form_items as $key => $value):
                                $id = isset($pcd_form_detail) ? $pcd_form_detail[$key]['id'] : '';
                            ?>

                                <tr>
                                    <!-- Location -->
                                    <td>
                                        <?= $value['name'] ?>
                                        <input type="hidden" name="ugflooritems[<?= $sr ?>][id]" value="<?= $id ?>">
                                        <input type="hidden" name="ugflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                    </td>

                                    <!-- Treatment Type -->
                                    <td>
                                        <input type="text"
                                            name="ugflooritems[<?= $sr ?>][treatment_type]"
                                            value="<?=  $pcd_form_detail[$key]['treatment_type'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Chemical used -->
                                    <td>
                                        <input type="text"
                                            name="ugflooritems[<?= $sr ?>][chemical_used]"
                                            value="<?=  $pcd_form_detail[$key]['chemical_used'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Area in charge -->
                                    <td>
                                        <input type="text"
                                            name="ugflooritems[<?= $sr ?>][area_in_charge]"
                                            value="<?=  $pcd_form_detail[$key]['area_in_charge'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Technician -->
                                    <td>
                                        <input type="text"
                                            name="ugflooritems[<?= $sr ?>][technician]"
                                            value="<?=  $pcd_form_detail[$key]['technician'] ?>"
                                            class="form-control">
                                    </td>
                                </tr>

                            <?php
                                $sr++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="service_floor">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table service-floor-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Treatment Type</th>
                                <th>Chemical used</th>
                                <th>Area in charge</th>
                                <th>Technician</th>
                            </tr>
                        </thead>
                        <tbody class="service_floor_body">
                            <?php
                            $form_items = get_service_floor_items();
                            $sr = 1;

                            foreach ($form_items as $key => $value):
                                $id = isset($pcd_form_detail) ? $pcd_form_detail[$key]['id'] : '';
                            ?>

                                <tr>
                                    <!-- Location -->
                                    <td>
                                        <?= $value['name'] ?>
                                        <input type="hidden" name="serviceflooritems[<?= $sr ?>][id]" value="<?= $id ?>">
                                        <input type="hidden" name="serviceflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                    </td>

                                    <!-- Treatment Type -->
                                    <td>
                                        <input type="text"
                                            name="serviceflooritems[<?= $sr ?>][treatment_type]"
                                            value="<?=  $pcd_form_detail[$key]['treatment_type'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Chemical used -->
                                    <td>
                                        <input type="text"
                                            name="serviceflooritems[<?= $sr ?>][chemical_used]"
                                            value="<?=  $pcd_form_detail[$key]['chemical_used'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Area in charge -->
                                    <td>
                                        <input type="text"
                                            name="serviceflooritems[<?= $sr ?>][area_in_charge]"
                                            value="<?=  $pcd_form_detail[$key]['area_in_charge'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Technician -->
                                    <td>
                                        <input type="text"
                                            name="serviceflooritems[<?= $sr ?>][technician]"
                                            value="<?=  $pcd_form_detail[$key]['technician'] ?>"
                                            class="form-control">
                                    </td>
                                </tr>

                            <?php
                                $sr++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="first_floor">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table first-floor-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Treatment Type</th>
                                <th>Chemical used</th>
                                <th>Area in charge</th>
                                <th>Technician</th>
                            </tr>
                        </thead>
                        <tbody class="first_floor_body">
                            <?php
                            $form_items = get_first_floor_items();
                            $sr = 1;

                            foreach ($form_items as $key => $value):
                                $id = isset($pcd_form_detail) ? $pcd_form_detail[$key]['id'] : '';
                            ?>

                                <tr>
                                    <!-- Location -->
                                    <td>
                                        <?= $value['name'] ?>
                                        <input type="hidden" name="firstflooritems[<?= $sr ?>][id]" value="<?= $id ?>">
                                        <input type="hidden" name="firstflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                    </td>

                                    <!-- Treatment Type -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][treatment_type]"
                                            value=" <?=  $pcd_form_detail[$key]['treatment_type'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Chemical used -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][chemical_used]"
                                            value=" <?=  $pcd_form_detail[$key]['chemical_used'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Area in charge -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][area_in_charge]"
                                            value=" <?=  $pcd_form_detail[$key]['area_in_charge'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Technician -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][technician]"
                                            value=" <?=  $pcd_form_detail[$key]['technician'] ?>"
                                            class="form-control">
                                    </td>
                                </tr>

                            <?php
                                $sr++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="second_floor">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table second-floor-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Treatment Type</th>
                                <th>Chemical used</th>
                                <th>Area in charge</th>
                                <th>Technician</th>
                            </tr>
                        </thead>
                        <tbody class="second_floor_body">
                            <?php
                            $form_items = get_second_floor_items();
                            $sr = 1;

                            foreach ($form_items as $key => $value):
                                $id = isset($pcd_form_detail) ? $pcd_form_detail[$key]['id'] : '';
                            ?>

                                <tr>
                                    <!-- Location -->
                                    <td>
                                        <?= $value['name'] ?>
                                        <input type="hidden" name="firstflooritems[<?= $sr ?>][id]" value="<?= $id ?>">
                                        <input type="hidden" name="firstflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                    </td>

                                    <!-- Treatment Type -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][treatment_type]"
                                            value=" <?=  $pcd_form_detail[$key]['treatment_type'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Chemical used -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][chemical_used]"
                                            value=" <?=  $pcd_form_detail[$key]['chemical_used'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Area in charge -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][area_in_charge]"
                                            value=" <?=  $pcd_form_detail[$key]['area_in_charge'] ?>"
                                            class="form-control">
                                    </td>

                                    <!-- Technician -->
                                    <td>
                                        <input type="text"
                                            name="firstflooritems[<?= $sr ?>][technician]"
                                            value=" <?=  $pcd_form_detail[$key]['technician'] ?>"
                                            class="form-control">
                                    </td>
                                </tr>

                            <?php
                                $sr++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="horizontal-scrollable-tabs preview-tabs-top">
        <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                <li role="presentation" class="active">
                    <a href="#ground_floor_dates" aria-controls="ground_floor_dates" role="tab" data-toggle="tab">
                        <?php echo _l('Ground Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#ug_floor_dates" aria-controls="ug_floor_dates" role="tab" data-toggle="tab">
                        <?php echo _l('UG Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#service_floor_dates" aria-controls="service_floor_dates" role="tab" data-toggle="tab">
                        <?php echo _l('Service Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#first_floor_dates" aria-controls="first_floor_dates" role="tab" data-toggle="tab">
                        <?php echo _l('First Floor'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#second_floor_dates" aria-controls="second_floor_dates" role="tab" data-toggle="tab">
                        <?php echo _l('Second Floor'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 active" id="ground_floor_dates">
            <div id="estimate-preview">
                <div class="row">

                    <!-- SCROLL WRAPPER -->
                    <div style="overflow-x:auto; width:100%;">

                        <table class="table ground-floor-table items has-calculations no-mtop table-bordered" style="min-width:1200px;">

                            <thead>
                                <tr>
                                    <th style="min-width:150px;">Location</th>

                                    <?php
                                    $currentMonth = date('m');
                                    $currentYear  = date('Y');
                                    $totalDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                                    for ($d = 1; $d <= $totalDays; $d++):
                                        $fullDate = date('d-m-Y', strtotime("$currentYear-$currentMonth-$d"));
                                    ?>
                                        <th style="min-width:100px; text-align:center;">
                                            <?= $fullDate ?>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>

                            <tbody class="ground_floor_body">
                                <?php
                                $form_items = get_ground_floor_items();
                                $sr = 1;

                                foreach ($form_items as $key => $value):
                                ?>
                                    <tr>
                                        <td style="min-width:150px;">
                                            <?= $value['name'] ?>
                                            <input type="hidden" name="groundflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                        </td>

                                        <?php for ($d = 1; $d <= $totalDays; $d++):
                                            $date = date('Y-m-d', strtotime("$currentYear-$currentMonth-$d"));
                                        ?>
                                            <td style="min-width:80px;">
                                                <select
                                                    name="groundflooritems[<?= $sr ?>][dates][<?= $date ?>]"
                                                    class="form-control input-sm"
                                                    style="width:90px;">

                                                    <option value="">--</option>
                                                    <option value="1">Done</option>
                                                    <option value="2">NOT REQUIRED</option>
                                                    <option value="3">ABSENT</option>

                                                </select>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php
                                    $sr++;
                                endforeach;
                                ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- END SCROLL -->

                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="ug_floor_dates">
            <div id="estimate-preview">
                <div class="row">

                    <!-- SCROLL WRAPPER -->
                    <div style="overflow-x:auto; width:100%;">

                        <table class="table ground-floor-table items has-calculations no-mtop table-bordered" style="min-width:1200px;">

                            <thead>
                                <tr>
                                    <th style="min-width:150px;">Location</th>

                                    <?php
                                    $currentMonth = date('m');
                                    $currentYear  = date('Y');
                                    $totalDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                                    for ($d = 1; $d <= $totalDays; $d++):
                                        $fullDate = date('d-m-Y', strtotime("$currentYear-$currentMonth-$d"));
                                    ?>
                                        <th style="min-width:100px; text-align:center;">
                                            <?= $fullDate ?>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>

                            <tbody class="ground_floor_body">
                                <?php
                                $form_items = get_ug_floor_items();
                                $sr = 1;

                                foreach ($form_items as $key => $value):
                                ?>
                                    <tr>
                                        <td style="min-width:150px;">
                                            <?= $value['name'] ?>
                                            <input type="hidden" name="ugflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                        </td>

                                        <?php for ($d = 1; $d <= $totalDays; $d++):
                                            $date = date('Y-m-d', strtotime("$currentYear-$currentMonth-$d"));
                                        ?>
                                            <td style="min-width:80px;">
                                                <select
                                                    name="ugflooritems[<?= $sr ?>][dates][<?= $date ?>]"
                                                    class="form-control input-sm"
                                                    style="width:90px;">

                                                    <option value="">--</option>
                                                    <option value="1">Done</option>
                                                    <option value="2">NOT REQUIRED</option>
                                                    <option value="3">ABSENT</option>

                                                </select>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php
                                    $sr++;
                                endforeach;
                                ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- END SCROLL -->

                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="service_floor_dates">
            <div id="estimate-preview">
                <div class="row">

                    <!-- SCROLL WRAPPER -->
                    <div style="overflow-x:auto; width:100%;">

                        <table class="table ground-floor-table items has-calculations no-mtop table-bordered" style="min-width:1200px;">

                            <thead>
                                <tr>
                                    <th style="min-width:150px;">Location</th>

                                    <?php
                                    $currentMonth = date('m');
                                    $currentYear  = date('Y');
                                    $totalDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                                    for ($d = 1; $d <= $totalDays; $d++):
                                        $fullDate = date('d-m-Y', strtotime("$currentYear-$currentMonth-$d"));
                                    ?>
                                        <th style="min-width:100px; text-align:center;">
                                            <?= $fullDate ?>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>

                            <tbody class="ground_floor_body">
                                <?php
                                $form_items = get_service_floor_items();
                                $sr = 1;

                                foreach ($form_items as $key => $value):
                                ?>
                                    <tr>
                                        <td style="min-width:150px;">
                                            <?= $value['name'] ?>
                                            <input type="hidden" name="serviceflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                        </td>

                                        <?php for ($d = 1; $d <= $totalDays; $d++):
                                            $date = date('Y-m-d', strtotime("$currentYear-$currentMonth-$d"));
                                        ?>
                                            <td style="min-width:80px;">
                                                <select
                                                    name="serviceflooritems[<?= $sr ?>][dates][<?= $date ?>]"
                                                    class="form-control input-sm"
                                                    style="width:90px;">

                                                    <option value="">--</option>
                                                    <option value="1">Done</option>
                                                    <option value="2">NOT REQUIRED</option>
                                                    <option value="3">ABSENT</option>

                                                </select>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php
                                    $sr++;
                                endforeach;
                                ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- END SCROLL -->

                </div>
            </div>
        </div>
         <div role="tabpanel" class="tab-pane ptop10" id="first_floor_dates">
            <div id="estimate-preview">
                <div class="row">

                    <!-- SCROLL WRAPPER -->
                    <div style="overflow-x:auto; width:100%;">

                        <table class="table ground-floor-table items has-calculations no-mtop table-bordered" style="min-width:1200px;">

                            <thead>
                                <tr>
                                    <th style="min-width:150px;">Location</th>

                                    <?php
                                    $currentMonth = date('m');
                                    $currentYear  = date('Y');
                                    $totalDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                                    for ($d = 1; $d <= $totalDays; $d++):
                                        $fullDate = date('d-m-Y', strtotime("$currentYear-$currentMonth-$d"));
                                    ?>
                                        <th style="min-width:100px; text-align:center;">
                                            <?= $fullDate ?>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>

                            <tbody class="ground_floor_body">
                                <?php
                                $form_items = get_first_floor_items();
                                $sr = 1;

                                foreach ($form_items as $key => $value):
                                ?>
                                    <tr>
                                        <td style="min-width:150px;">
                                            <?= $value['name'] ?>
                                            <input type="hidden" name="firstflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                        </td>

                                        <?php for ($d = 1; $d <= $totalDays; $d++):
                                            $date = date('Y-m-d', strtotime("$currentYear-$currentMonth-$d"));
                                        ?>
                                            <td style="min-width:80px;">
                                                <select
                                                    name="firstflooritems[<?= $sr ?>][dates][<?= $date ?>]"
                                                    class="form-control input-sm"
                                                    style="width:90px;">

                                                    <option value="">--</option>
                                                    <option value="1">Done</option>
                                                    <option value="2">NOT REQUIRED</option>
                                                    <option value="3">ABSENT</option>

                                                </select>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php
                                    $sr++;
                                endforeach;
                                ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- END SCROLL -->

                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="second_floor_dates">
            <div id="estimate-preview">
                <div class="row">

                    <!-- SCROLL WRAPPER -->
                    <div style="overflow-x:auto; width:100%;">

                        <table class="table ground-floor-table items has-calculations no-mtop table-bordered" style="min-width:1200px;">

                            <thead>
                                <tr>
                                    <th style="min-width:150px;">Location</th>

                                    <?php
                                    $currentMonth = date('m');
                                    $currentYear  = date('Y');
                                    $totalDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                                    for ($d = 1; $d <= $totalDays; $d++):
                                        $fullDate = date('d-m-Y', strtotime("$currentYear-$currentMonth-$d"));
                                    ?>
                                        <th style="min-width:100px; text-align:center;">
                                            <?= $fullDate ?>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>

                            <tbody class="ground_floor_body">
                                <?php
                                $form_items = get_first_floor_items();
                                $sr = 1;

                                foreach ($form_items as $key => $value):
                                ?>
                                    <tr>
                                        <td style="min-width:150px;">
                                            <?= $value['name'] ?>
                                            <input type="hidden" name="secondflooritems[<?= $sr ?>][location]" value="<?= $value['name'] ?>">
                                        </td>

                                        <?php for ($d = 1; $d <= $totalDays; $d++):
                                            $date = date('Y-m-d', strtotime("$currentYear-$currentMonth-$d"));
                                        ?>
                                            <td style="min-width:80px;">
                                                <select
                                                    name="secondflooritems[<?= $sr ?>][dates][<?= $date ?>]"
                                                    class="form-control input-sm"
                                                    style="width:90px;">

                                                    <option value="">--</option>
                                                    <option value="1">Done</option>
                                                    <option value="2">NOT REQUIRED</option>
                                                    <option value="3">ABSENT</option>

                                                </select>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php
                                    $sr++;
                                endforeach;
                                ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- END SCROLL -->

                </div>
            </div>
        </div>
    </div>
</div>