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

            <input type="hidden" name="action" value="apc">
            <thead>
                <tr>
                    <th colspan="5" class="daily_report_title">CIRCULAR SAW</th>
                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Project: <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="2" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Contractor Name :</span><span class="daily_report_label" style="display: ruby;"> <?php echo render_select('name_of_contractor', get_vendor_list_for_forms(), array('userid', 'company'), '', isset($krp_form->name_of_contractor) ? $krp_form->name_of_contractor : ''); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Checklist No.: <?php echo render_input('checklist_no', '', isset($esc_form->checklist_no) ? $esc_form->checklist_no : '', 'text', ['style' => 'width:150px;']); ?></span>
                    </th>
                    <th colspan="2" class="daily_report_head">

                        <span class="daily_report_label">Date: </span><input type="datetime-local" class="form-control" name="date" value="<?= isset($esc_form->date) ? date('Y-m-d\TH:i', strtotime($esc_form->date)) : '' ?>">

                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label">Equipment Name & Number: 31 - CIRCULAR SAW &</span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label">Note: Please write Yes or No in the given box and if some comments write in remarks column.</span>
                    </th>
                </tr>
                <tr class="main">
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">S.No.</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Description</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Status</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Remarks</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php $sr = 1;
                foreach ($form_items as $key => $value):
                    $id = isset($esc_form_detail) ? $esc_form_detail[$key]['id'] : ''; ?>
                    <tr class="main">
                        <input type="hidden" class="ids" name="items[<?= $sr ?>][id]" value="<?= $id  ?>">
                        <td><?= $sr ?></td>
                        <td style="font-weight: 600;font-size: 16px;"><?= $value['name'] ?></td>
                        <td>
                            <span class="daily_report_label" style="display: ruby;">
                                <?php echo render_select('items[' . $sr . '][status]', get_item_status_listing(), array('id', 'name'), '', isset($esc_form_detail) ? $esc_form_detail[$key]['status'] : ''); ?>
                            </span>
                        </td>
                        <td>
                            <span class="daily_report_label" style="display: ruby;">
                                <?php echo render_input('items[' . $sr . '][remarks]', '', isset($esc_form_detail) ? $esc_form_detail[$key]['remarks'] : '', 'text', ['style' => 'width:150px;']); ?>
                            </span>
                        </td>
                    </tr>
                <?php $sr++;
                endforeach; ?>
            </tbody>
        </table>


    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?php echo render_textarea('remarks', 'Remarks', isset($msh_form) ? $msh_form->remarks : '',  ['style' => 'height:267px;resize: none;']); ?>
            </div>
        </div>
        <div class="col-md-6">
            <img src="<?php echo base_url('assets/images/pgrjcb.jpg') ?>" alt="">
        </div>
    </div>

    <table class="table items table-main-dpr-edit no-mtop" style="margin-top:20px;">
        <!-- Top Row -->
        <tr>
            <td class="daily_report_head daily_center">
                <label>
                    <span class="daily_report_label">FIT</span>
                    <input type="radio" name="fit_status" value="fit" style="margin-left:10px;">
                </label>
            </td>

            <td class="daily_report_head daily_center">
                <label>
                    <span class="daily_report_label">PARTIALLY FIT</span>
                    <input type="radio" name="fit_status" value="partial" style="margin-left:10px;">
                </label>
            </td>

            <td class="daily_report_head daily_center">
                <label>
                    <span class="daily_report_label">UNFIT</span>
                    <input type="radio" name="fit_status" value="unfit" style="margin-left:10px;">
                </label>
            </td>
        </tr>

        <!-- Section Headers -->
        <tr>
            <td colspan="2" class="daily_report_head">
                <span class="daily_report_label" style="display: ruby;">Inspected By Name: <?php echo render_select('inspected_by', get_staff_list(), array('staffid', 'name'), '', isset($msh_form->inspected_by) ? $msh_form->inspected_by : ''); ?></span>
            </td>
            <td class="daily_report_head">
                <span class="daily_report_label" style="display: ruby;">Reviewed By Name: <?php echo render_select('reviewed_by', get_staff_list(), array('staffid', 'name'), '', isset($msh_form->reviewed_by) ? $msh_form->reviewed_by : ''); ?></span>
            </td>
        </tr>

        
    </table>
</div>

<script type="text/javascript">
    $('#project_id').on('change', function() {
        // var project_id = $(this).val();
        var project_name = $('#project_id option:selected').text();
        $('.view_project_name').html(project_name);
    });
</script>