<!-- <style type="text/css">
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
        width: 138px !important;
    }

    .laber-type .dropdown-menu .open,
    .progress_report_type .dropdown-menu .open {
        width: max-content !important;
    }

    .progress_report_type .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 140px !important;
    }

    .laber-type .dropdown-menu .open,
    .machinery .dropdown-menu .open {
        width: max-content !important;
    }

    .machinery .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 140px !important;
    }
</style> -->
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
        width: 138px !important;
    }

    .laber-type .dropdown-menu .open,
    .progress_report_type .dropdown-menu .open {
        width: max-content !important;
    }

    .progress_report_type .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 140px !important;
    }

    .laber-type .dropdown-menu .open,
    .machinery .dropdown-menu .open {
        width: max-content !important;
    }

    .machinery .dropdown-toggle,
    .laber-type .dropdown-toggle {
        width: 140px !important;
    }

    /* ========== RESPONSIVE STACKED LAYOUT ========== */
    @media (max-width: 768px) {

        /* Make all tables block level */
        .dpr-items-table,
        .dpr-rmc-table,
        .dpr-material-table,
        .dpr-department-labour-table,
        .rack-cement-table,
        .block-mortar-table,
        .tile-adhesive-table,
        .tile-coupler-table,
        .tile-wires-table,
        .tile-coucil-table {
            display: block;
            width: 100%;
        }

        /* Hide thead for all tables */
        .dpr-items-table thead,
        .dpr-rmc-table thead,
        .dpr-material-table thead,
        .dpr-department-labour-table thead,
        .rack-cement-table thead,
        .block-mortar-table thead,
        .tile-adhesive-table thead,
        .tile-coupler-table thead,
        .tile-wires-table thead,
        .tile-coucil-table thead {
            display: none;
        }

        /* Make tbody block */
        .dpr-items-table tbody,
        .dpr-rmc-table tbody,
        .dpr-material-table tbody,
        .dpr-department-labour-table tbody,
        .rack-cement-table tbody,
        .block-mortar-table tbody,
        .tile-adhesive-table tbody,
        .tile-coupler-table tbody,
        .tile-wires-table tbody,
        .tile-coucil-table tbody {
            display: block;
        }

        /* Style each row as a card */
        .dpr-items-table tbody tr,
        .dpr-rmc-table tbody tr,
        .dpr-material-table tbody tr,
        .dpr-department-labour-table tbody tr,
        .rack-cement-table tbody tr,
        .block-mortar-table tbody tr,
        .tile-adhesive-table tbody tr,
        .tile-coupler-table tbody tr,
        .tile-wires-table tbody tr,
        .tile-coucil-table tbody tr {
            display: block;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        /* Style each td as a flex row with label */
        .dpr-items-table tbody tr td,
        .dpr-rmc-table tbody tr td,
        .dpr-material-table tbody tr td,
        .dpr-department-labour-table tbody tr td,
        .rack-cement-table tbody tr td,
        .block-mortar-table tbody tr td,
        .tile-adhesive-table tbody tr td,
        .tile-coupler-table tbody tr td,
        .tile-wires-table tbody tr td,
        .tile-coucil-table tbody tr td {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 8px 5px;
            border: none;
            border-bottom: 1px solid #eee;
            width: 100%;
        }

        .dpr-items-table tbody tr td:last-child,
        .dpr-rmc-table tbody tr td:last-child,
        .dpr-material-table tbody tr td:last-child,
        .dpr-department-labour-table tbody tr td:last-child {
            border-bottom: none;
        }

        /* Add labels before each field using data-label attribute */
        .dpr-items-table tbody tr td:before,
        .dpr-rmc-table tbody tr td:before,
        .dpr-material-table tbody tr td:before,
        .dpr-department-labour-table tbody tr td:before,
        .rack-cement-table tbody tr td:before,
        .block-mortar-table tbody tr td:before,
        .tile-adhesive-table tbody tr td:before,
        .tile-coupler-table tbody tr td:before,
        .tile-wires-table tbody tr td:before,
        .tile-coucil-table tbody tr td:before {
            content: attr(data-label);
            font-weight: bold;
            min-width: 120px;
            padding-right: 10px;
            font-size: 13px;
            color: #333;
        }

        /* Make inputs and selects full width */
        .dpr-items-table tbody tr td input,
        .dpr-items-table tbody tr td select,
        .dpr-rmc-table tbody tr td input,
        .dpr-rmc-table tbody tr td select,
        .dpr-material-table tbody tr td input,
        .dpr-material-table tbody tr td select,
        .dpr-department-labour-table tbody tr td input,
        .dpr-department-labour-table tbody tr td select,
        .rack-cement-table tbody tr td input,
        .rack-cement-table tbody tr td select,
        .block-mortar-table tbody tr td input,
        .block-mortar-table tbody tr td select,
        .tile-adhesive-table tbody tr td input,
        .tile-adhesive-table tbody tr td select,
        .tile-coupler-table tbody tr td input,
        .tile-coupler-table tbody tr td select,
        .tile-wires-table tbody tr td input,
        .tile-wires-table tbody tr td select,
        .tile-coucil-table tbody tr td input,
        .tile-coucil-table tbody tr td select {
            flex: 1;
            min-width: 150px;
            width: 100% !important;
        }

        /* Bootstrap select fixes */
        .dpr-items-table tbody tr td .bootstrap-select,
        .dpr-rmc-table tbody tr td .bootstrap-select,
        .dpr-material-table tbody tr td .bootstrap-select,
        .dpr-department-labour-table tbody tr td .bootstrap-select,
        .rack-cement-table tbody tr td .bootstrap-select,
        .block-mortar-table tbody tr td .bootstrap-select,
        .tile-adhesive-table tbody tr td .bootstrap-select,
        .tile-coupler-table tbody tr td .bootstrap-select,
        .tile-wires-table tbody tr td .bootstrap-select,
        .tile-coucil-table tbody tr td .bootstrap-select {
            width: 100% !important;
            flex: 1;
            min-width: 150px;
        }

        .dpr-items-table tbody tr td .dropdown-toggle,
        .dpr-rmc-table tbody tr td .dropdown-toggle,
        .dpr-material-table tbody tr td .dropdown-toggle,
        .dpr-department-labour-table tbody tr td .dropdown-toggle,
        .rack-cement-table tbody tr td .dropdown-toggle,
        .block-mortar-table tbody tr td .dropdown-toggle,
        .tile-adhesive-table tbody tr td .dropdown-toggle,
        .tile-coupler-table tbody tr td .dropdown-toggle,
        .tile-wires-table tbody tr td .dropdown-toggle,
        .tile-coucil-table tbody tr td .dropdown-toggle {
            width: 100% !important;
        }

        .dpr-items-table tbody tr td .btn-group,
        .dpr-rmc-table tbody tr td .btn-group,
        .dpr-material-table tbody tr td .btn-group,
        .dpr-department-labour-table tbody tr td .btn-group,
        .rack-cement-table tbody tr td .btn-group,
        .block-mortar-table tbody tr td .btn-group,
        .tile-adhesive-table tbody tr td .btn-group,
        .tile-coupler-table tbody tr td .btn-group,
        .tile-wires-table tbody tr td .btn-group,
        .tile-coucil-table tbody tr td .btn-group {
            width: 100% !important;
        }

        /* Action buttons - last column */
        .dpr-items-table tbody tr td:last-child,
        .dpr-rmc-table tbody tr td:last-child,
        .dpr-material-table tbody tr td:last-child,
        .dpr-department-labour-table tbody tr td:last-child {
            justify-content: flex-end;
        }

        .dpr-items-table tbody tr td:last-child:before,
        .dpr-rmc-table tbody tr td:last-child:before,
        .dpr-material-table tbody tr td:last-child:before,
        .dpr-department-labour-table tbody tr td:last-child:before {
            display: none;
        }

        /* ===== HEADER SECTIONS ===== */
        .table-main-dpr-edit,
        .table-responsive {
            display: block;
            overflow-x: visible !important;
        }

        /* Header rows stack vertically */
        .table-main-dpr-edit thead tr:first-child th,
        .table-main-dpr-edit thead tr:nth-child(2) th,
        .table-main-dpr-edit thead tr:nth-child(3) th,
        .table-main-dpr-edit thead tr:nth-child(4) th {
            display: block;
            width: 100%;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .table-main-dpr-edit thead tr:first-child th {
            text-align: center;
        }

        .table-main-dpr-edit thead tr:nth-child(2) th,
        .table-main-dpr-edit thead tr:nth-child(3) th,
        .table-main-dpr-edit thead tr:nth-child(4) th {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
        }

        .table-main-dpr-edit thead tr:nth-child(2) th .daily_report_label,
        .table-main-dpr-edit thead tr:nth-child(3) th .daily_report_label,
        .table-main-dpr-edit thead tr:nth-child(4) th .daily_report_label {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
            width: 100%;
        }

        .table-main-dpr-edit thead tr:nth-child(2) th .daily_report_label select,
        .table-main-dpr-edit thead tr:nth-child(3) th .daily_report_label select,
        .table-main-dpr-edit thead tr:nth-child(4) th .daily_report_label select,
        .table-main-dpr-edit thead tr:nth-child(2) th .daily_report_label input,
        .table-main-dpr-edit thead tr:nth-child(3) th .daily_report_label input,
        .table-main-dpr-edit thead tr:nth-child(4) th .daily_report_label input {
            width: auto !important;
            min-width: 120px;
            flex: 1;
        }

        /* Activity header */
        .table-main-dpr-edit thead tr:nth-child(5) th {
            display: block;
            width: 100%;
            text-align: center;
        }

        /* Hide the second header row with column labels */
        .table-main-dpr-edit thead tr:nth-child(6) {
            display: none;
        }

        /* ===== DATA LABELS FOR EACH COLUMN ===== */
        /* DPR Items Table */
        .dpr-items-table tbody tr td:nth-child(1):before {
            content: "Location";
        }

        .dpr-items-table tbody tr td:nth-child(2):before {
            content: "Agency";
        }

        .dpr-items-table tbody tr td:nth-child(3):before {
            content: "Type";
        }

        .dpr-items-table tbody tr td:nth-child(4):before {
            content: "Remarks";
        }

        .dpr-items-table tbody tr td:nth-child(5):before {
            content: "Work Execute";
        }

        .dpr-items-table tbody tr td:nth-child(6):before {
            content: "Material Consumption";
        }

        .dpr-items-table tbody tr td:nth-child(7):before {
            content: "Skilled";
        }

        .dpr-items-table tbody tr td:nth-child(8):before {
            content: "Unskilled";
        }

        .dpr-items-table tbody tr td:nth-child(9):before {
            content: "Total";
        }

        .dpr-items-table tbody tr td:nth-child(10):before {
            content: "Machinery";
        }

        .dpr-items-table tbody tr td:nth-child(11):before {
            content: "Total";
        }

        /* RMC Table */
        .dpr-rmc-table tbody tr td:nth-child(1):before {
            content: "Challan No";
        }

        .dpr-rmc-table tbody tr td:nth-child(2):before {
            content: "Grade";
        }

        .dpr-rmc-table tbody tr td:nth-child(3):before {
            content: "Structure Work";
        }

        .dpr-rmc-table tbody tr td:nth-child(4):before {
            content: "Quantity(CMT)";
        }

        /* Material Table */
        .dpr-material-table tbody tr td:nth-child(1):before {
            content: "Challan No/ Truck No";
        }

        .dpr-material-table tbody tr td:nth-child(2):before {
            content: "Supplier Name";
        }

        .dpr-material-table tbody tr td:nth-child(3):before {
            content: "Material Description";
        }

        .dpr-material-table tbody tr td:nth-child(4):before {
            content: "Total";
        }

        /* Department Labour Table */
        .dpr-department-labour-table tbody tr td:nth-child(1):before {
            content: "Name";
        }

        .dpr-department-labour-table tbody tr td:nth-child(2):before {
            content: "Attendance";
        }

        .dpr-department-labour-table tbody tr td:nth-child(3):before {
            content: "Over Time";
        }

        .dpr-department-labour-table tbody tr td:nth-child(4):before {
            content: "Kharchi";
        }

        /* Rack Cement Table */
        .rack-cement-table tbody tr td:nth-child(1):before {
            content: "Inward Inventory";
        }

        .rack-cement-table tbody tr td:nth-child(2):before {
            content: "Todays usage";
        }

        .rack-cement-table tbody tr td:nth-child(3):before {
            content: "Total Remaining";
        }

        .rack-cement-table tbody tr td:nth-child(4):before {
            content: "Notes";
        }

        /* Block Mortar Joint */
        .block-mortar-table tbody tr td:nth-child(1):before {
            content: "Inward Inventory";
        }

        .block-mortar-table tbody tr td:nth-child(2):before {
            content: "Todays usage";
        }

        .block-mortar-table tbody tr td:nth-child(3):before {
            content: "Total Remaining";
        }

        .block-mortar-table tbody tr td:nth-child(4):before {
            content: "Notes";
        }

        /* Tile Adhesive */
        .tile-adhesive-table tbody tr td:nth-child(1):before {
            content: "Inward Inventory";
        }

        .tile-adhesive-table tbody tr td:nth-child(2):before {
            content: "Todays usage";
        }

        .tile-adhesive-table tbody tr td:nth-child(3):before {
            content: "Total Remaining";
        }

        .tile-adhesive-table tbody tr td:nth-child(4):before {
            content: "Notes";
        }

        /* Coupler */
        .tile-coupler-table tbody tr td:nth-child(1):before {
            content: "Inward Inventory";
        }

        .tile-coupler-table tbody tr td:nth-child(2):before {
            content: "Type";
        }

        .tile-coupler-table tbody tr td:nth-child(3):before {
            content: "Todays usage";
        }

        .tile-coupler-table tbody tr td:nth-child(4):before {
            content: "Total Remaining";
        }

        .tile-coupler-table tbody tr td:nth-child(5):before {
            content: "Notes";
        }

        /* Wires */
        .tile-wires-table tbody tr td:nth-child(1):before {
            content: "Inward Inventory";
        }

        .tile-wires-table tbody tr td:nth-child(2):before {
            content: "Type";
        }

        .tile-wires-table tbody tr td:nth-child(3):before {
            content: "Todays usage";
        }

        .tile-wires-table tbody tr td:nth-child(4):before {
            content: "Total Remaining";
        }

        .tile-wires-table tbody tr td:nth-child(5):before {
            content: "Notes";
        }

        /* Council Box */
        .tile-coucil-table tbody tr td:nth-child(1):before {
            content: "Inward Inventory";
        }

        .tile-coucil-table tbody tr td:nth-child(2):before {
            content: "Todays usage";
        }

        .tile-coucil-table tbody tr td:nth-child(3):before {
            content: "Total Remaining";
        }

        .tile-coucil-table tbody tr td:nth-child(4):before {
            content: "Notes";
        }

        /* Remove labels for action columns */
        .dpr-rmc-table tbody tr td:last-child:before,
        .dpr-material-table tbody tr td:last-child:before,
        .dpr-department-labour-table tbody tr td:last-child:before,
        .rack-cement-table tbody tr td:last-child:before,
        .block-mortar-table tbody tr td:last-child:before,
        .tile-adhesive-table tbody tr td:last-child:before,
        .tile-coupler-table tbody tr td:last-child:before,
        .tile-wires-table tbody tr td:last-child:before,
        .tile-coucil-table tbody tr td:last-child:before {
            display: none;
        }

        /* Tab content responsive */
        .horizontal-scrollable-tabs {
            overflow-x: auto;
            white-space: nowrap;
        }

        .horizontal-scrollable-tabs .nav-tabs {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .horizontal-scrollable-tabs .nav-tabs li {
            float: none;
            display: inline-block;
        }
    }

    /* Extra small screens */
    @media (max-width: 480px) {

        .dpr-items-table tbody tr td:before,
        .dpr-rmc-table tbody tr td:before,
        .dpr-material-table tbody tr td:before,
        .dpr-department-labour-table tbody tr td:before,
        .rack-cement-table tbody tr td:before,
        .block-mortar-table tbody tr td:before,
        .tile-adhesive-table tbody tr td:before,
        .tile-coupler-table tbody tr td:before,
        .tile-wires-table tbody tr td:before,
        .tile-coucil-table tbody tr td:before {
            min-width: 80px;
            font-size: 12px;
        }

        .dpr-items-table tbody tr td input,
        .dpr-items-table tbody tr td select,
        .dpr-rmc-table tbody tr td input,
        .dpr-rmc-table tbody tr td select,
        .dpr-material-table tbody tr td input,
        .dpr-material-table tbody tr td select,
        .dpr-department-labour-table tbody tr td input,
        .dpr-department-labour-table tbody tr td select,
        .rack-cement-table tbody tr td input,
        .rack-cement-table tbody tr td select,
        .block-mortar-table tbody tr td input,
        .block-mortar-table tbody tr td select,
        .tile-adhesive-table tbody tr td input,
        .tile-adhesive-table tbody tr td select,
        .tile-coupler-table tbody tr td input,
        .tile-coupler-table tbody tr td select,
        .tile-wires-table tbody tr td input,
        .tile-wires-table tbody tr td select,
        .tile-coucil-table tbody tr td input,
        .tile-coucil-table tbody tr td select {
            min-width: 100px;
            font-size: 12px;
        }

        .table-main-dpr-edit thead tr:nth-child(2) th .daily_report_label,
        .table-main-dpr-edit thead tr:nth-child(3) th .daily_report_label,
        .table-main-dpr-edit thead tr:nth-child(4) th .daily_report_label {
            flex-direction: column;
            align-items: flex-start;
        }

        .table-main-dpr-edit thead tr:nth-child(2) th .daily_report_label select,
        .table-main-dpr-edit thead tr:nth-child(3) th .daily_report_label select,
        .table-main-dpr-edit thead tr:nth-child(4) th .daily_report_label select,
        .table-main-dpr-edit thead tr:nth-child(2) th .daily_report_label input,
        .table-main-dpr-edit thead tr:nth-child(3) th .daily_report_label input,
        .table-main-dpr-edit thead tr:nth-child(4) th .daily_report_label input {
            width: 100% !important;
        }

        .dpr-items-table tbody tr,
        .dpr-rmc-table tbody tr,
        .dpr-material-table tbody tr,
        .dpr-department-labour-table tbody tr,
        .rack-cement-table tbody tr,
        .block-mortar-table tbody tr,
        .tile-adhesive-table tbody tr,
        .tile-coupler-table tbody tr,
        .tile-wires-table tbody tr,
        .tile-coucil-table tbody tr {
            padding: 8px;
        }
    }
</style>
<div class="col-md-12">
    <hr class="hr-panel-separator" />
</div>

<div class="col-md-12">
    <div class="table-responsive invoice-item">
        <?php
        if (isset($dpr_form)) {
            echo form_hidden('isedit');
        }
        ?>
        <table class="table dpr-items-table items table-main-dpr-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="13" class="daily_report_title">DAILY PROGRESS REPORT</th>
                </tr>
                <tr>
                    <th colspan="9" class="daily_report_head">
                        <span class="daily_report_label">Project: <span class="view_project_name"></span></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label">DPR Date: </span>
                        <?php echo isset($dpr_main_form->date) ? date('d-m-Y', strtotime($dpr_main_form->date)) : date('d-m-Y'); ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Client: <?php echo render_select('client_id', get_client_listing(), array('userid', 'company'), '', isset($dpr_form->client_id) ? $dpr_form->client_id : ''); ?></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">PMC: <?php echo render_input('pmc', '', isset($dpr_form->pmc) ? $dpr_form->pmc : '', 'text', ['style' => 'width:150px;']); ?></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Weather: <?php echo render_select('weather', get_weather_listing(), array('id', 'name'), '', isset($dpr_form->weather) ? $dpr_form->weather : ''); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Consultant: <?php echo render_input('consultant', '', isset($dpr_form->consultant) ? $dpr_form->consultant : '', 'text', ['style' => 'width:150px;']); ?></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Contractor: <?php echo render_input('contractor', '', isset($dpr_form->contractor) ? $dpr_form->contractor : '', 'text', ['style' => 'width:150px;']); ?></span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Work Stop: <?php echo render_select('work_stop', get_work_stop_listing(), array('id', 'name'), '', isset($dpr_form->work_stop) ? $dpr_form->work_stop : ''); ?></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="13" class="daily_report_activity">ACTIVITY WITH LOCATION & OUTPUT</th>
                </tr>
                <tr>
                    <th rowspan="2" class="daily_report_head daily_center" style="width: 160px;">
                        <span class="daily_report_label">Location</span>
                    </th>
                    <th rowspan="2" class="daily_report_head daily_center" style="width: 160px;">
                        <span class="daily_report_label">Agency</span>
                    </th>
                    <th rowspan="2" class="daily_report_head daily_center" style="width: 160px;">
                        <span class="daily_report_label">Type</span>
                    </th>
                    <th rowspan="2" class="daily_report_head daily_center" style="width: 17%;">
                        <span class="daily_report_label">Remarks</span>
                    </th>
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Work Progress</span>
                    </th>
                    <th colspan="3" class="daily_report_head daily_center">
                        <span class="daily_report_label">Type Of Manpower</span>
                    </th>
                    <th colspan="3" class="daily_report_head daily_center">
                        <span class="daily_report_label"></span>
                    </th>
                </tr>
                <tr>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Work Execute (smt/Rmt/Cmt)</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Material Consumption</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Skilled</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Unskilled</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Total</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Machinary</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label">Total</span>
                    </th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"><i class="fa fa-cog"></i></span>
                    </th>
                </tr>
            </thead>
            <tbody class="dpr_body">
                <?php echo pur_html_entity_decode($dpr_row_template); ?>
            </tbody>
        </table>
        <div id="removed-items"></div>
        <table class="table dpr-rmc-table items  has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="5" class="daily_report_title">RMC PLANT</th>
                </tr>
                <tr>
                    <th>Challan No</th>
                    <th>Grade</th>
                    <th>Structure Work</th>
                    <th>Quantity(CMT)</th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"><i class="fa fa-cog"></i></span>
                    </th>
                </tr>
            </thead>
            <tbody class="dpr_rmc_body">
                <?php echo pur_html_entity_decode($dpr_rmc_row_template); ?>
            </tbody>
        </table>
        <div id="removed-rmc-items"></div>
        <table class="table dpr-material-table items  has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="5" class="daily_report_title">MATERIAL INWARD</th>
                </tr>
                <tr>
                    <th>Challan No/ Truck No</th>
                    <th>Supplier Name</th>
                    <th>Material Description</th>
                    <th>Total</th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"><i class="fa fa-cog"></i></span>
                    </th>
                </tr>
            </thead>
            <tbody class="dpr_material_body">
                <?php echo pur_html_entity_decode($dpr_material_row_template); ?>
            </tbody>
        </table>
        <div id="removed-material-items"></div>
        <table class="table dpr-department-labour-table items  has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="5" class="daily_report_title">DEPARTMENT LABOUR</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Attendance</th>
                    <th>Over Time</th>
                    <th>Kharchi</th>
                    <th class="daily_report_head daily_center">
                        <span class="daily_report_label"><i class="fa fa-cog"></i></span>
                    </th>
                </tr>
            </thead>
            <tbody class="dpr_department_body">
                <?php echo pur_html_entity_decode($dpr_department_row_template); ?>
            </tbody>
        </table>
        <div id="removed-department-items"></div>
        <table class="table rack-cement-table items has-calculations no-mtop">
            <thead>
                <tr>
                    <th colspan="4" class="daily_report_title">ON RACK CEMENT BAG</th>
                </tr>
                <tr>
                    <th>Inward Inventory</th>
                    <th>Todays usage</th>
                    <th>Total Remaining cement</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody class="rack_cement_body">
                <?php
                $inward_inventory = isset($dpr_cement_data) ? $dpr_cement_data[0]['inward_inventory'] : '';
                $today_usage = isset($dpr_cement_data) ? $dpr_cement_data[0]['today_usage'] : '';
                $remaining_cement = isset($dpr_cement_data) ? $dpr_cement_data[0]['remaining_cement'] : '';
                $notes = isset($dpr_cement_data) ? $dpr_cement_data[0]['notes'] : '';
                ?>
                <td class="inward_inventory"><?php echo render_input('inward_inventory', '', $inward_inventory, 'number', ['id' => 'inward_inventory', 'oninput' => 'calculateRemaining()']) ?></td>
                <td class="today_usage"><?php echo render_input('today_usage', '', $today_usage, 'number', ['id' => 'today_usage', 'oninput' => 'calculateRemaining()']) ?></td>
                <td class="remaining_cement"><?php echo render_input('remaining_cement', '', $remaining_cement, 'number', ['id' => 'remaining_cement', 'readonly' => true]) ?></td>
                <td class="nores"><?php echo render_input('notes', '', $notes, 'text', ['id' => 'notes']) ?></td>
                <input type="hidden" name="rack_cement_id" value="<?php echo isset($dpr_cement_data) ? $dpr_cement_data[0]['id'] : ''; ?>">
            </tbody>
        </table>


        <div id="removed-cement-items"></div>
    </div>
    <div class="horizontal-scrollable-tabs preview-tabs-top">
        <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                <li role="presentation" class="active">
                    <a href="#block_mortar_joint" aria-controls="block_mortar_joint" role="tab" data-toggle="tab">
                        <?php echo _l('Block mortar joint'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tile_adhesive" aria-controls="tile_adhesive" role="tab" data-toggle="tab">
                        <?php echo _l('Tile Adhesive'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#coupler" aria-controls="coupler" role="tab" data-toggle="tab">
                        <?php echo _l('Coupler'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#wires" aria-controls="wires" role="tab" data-toggle="tab">
                        <?php echo _l('Wires'); ?>
                    </a>
                </li>
                <!-- <li role="presentation">
                    <a href="#council_box" aria-controls="council_box" role="tab" data-toggle="tab">
                        <?php echo _l('Council Box'); ?>
                    </a>
                </li> -->
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 active" id="block_mortar_joint">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table block-mortar-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th colspan="4" class="daily_report_title">Block mortar joint</th>
                            </tr>
                            <tr>
                                <th>Inward Inventory</th>
                                <th>Todays usage</th>
                                <th>Total Remaining cement</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody class="block_mortar_body">
                            <?php
                            $inward_inventory_bmj = isset($dpr_block_data) ? $dpr_block_data[0]['inward_inventory_bmj'] : '';
                            $today_usage_bmj = isset($dpr_block_data) ? $dpr_block_data[0]['today_usage_bmj'] : '';
                            $remaining_cement_bmj = isset($dpr_block_data) ? $dpr_block_data[0]['remaining_cement_bmj'] : '';
                            $notes_bmj = isset($dpr_block_data) ? $dpr_block_data[0]['notes_bmj'] : '';
                            ?>
                            <td class="inward_inventory_bmj"><?php echo render_input('inward_inventory_bmj', '', $inward_inventory_bmj, 'number', ['id' => 'inward_inventory_bmj', 'oninput' => 'calculateRemainingbmj()']) ?></td>
                            <td class="today_usage_bmj"><?php echo render_input('today_usage_bmj', '', $today_usage_bmj, 'number', ['id' => 'today_usage_bmj', 'oninput' => 'calculateRemainingbmj()']) ?></td>
                            <td class="remaining_cement_bmj"><?php echo render_input('remaining_cement_bmj', '', $remaining_cement_bmj, 'number', ['id' => 'remaining_cement_bmj', 'readonly' => true]) ?></td>
                            <td class="notes_bmj"><?php echo render_input('notes_bmj', '', $notes_bmj, 'text', ['id' => 'notes_bmj']) ?></td>
                            <input type="hidden" name="block_mortar_id" value="<?php echo isset($dpr_block_data) ? $dpr_block_data[0]['id'] : ''; ?>">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="tile_adhesive">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table tile-adhesive-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th colspan="4" class="daily_report_title">Tile Adhesive</th>
                            </tr>
                            <tr>
                                <th>Inward Inventory</th>
                                <th>Todays usage</th>
                                <th>Total Remaining cement</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody class="tile_adhesive_body">
                            <?php
                            $inward_inventory_ta = isset($dpr_tile_data) ? $dpr_tile_data[0]['inward_inventory_ta'] : '';
                            $today_usage_ta = isset($dpr_tile_data) ? $dpr_tile_data[0]['today_usage_ta'] : '';
                            $remaining_cement_ta = isset($dpr_tile_data) ? $dpr_tile_data[0]['remaining_cement_ta'] : '';
                            $notes_ta = isset($dpr_tile_data) ? $dpr_tile_data[0]['notes_ta'] : '';
                            ?>
                            <td class="inward_inventory_ta"><?php echo render_input('inward_inventory_ta', '', $inward_inventory_ta, 'number', ['id' => 'inward_inventory_ta', 'oninput' => 'calculateRemainingta()']) ?></td>
                            <td class="today_usage_ta"><?php echo render_input('today_usage_ta', '', $today_usage_ta, 'number', ['id' => 'today_usage_ta', 'oninput' => 'calculateRemainingta()']) ?></td>
                            <td class="remaining_cement_ta"><?php echo render_input('remaining_cement_ta', '', $remaining_cement_ta, 'number', ['id' => 'remaining_cement_ta', 'readonly' => true]) ?></td>
                            <td class="notes_bmj"><?php echo render_input('notes_ta', '', $notes_ta, 'text', ['id' => 'notes_ta']) ?></td>
                            <input type="hidden" name="tile_adhesive_id" value="<?php echo isset($dpr_tile_data) ? $dpr_tile_data[0]['id'] : ''; ?>">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="coupler">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table tile-coupler-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th colspan="5" class="daily_report_title">Coupler</th>
                            </tr>
                            <tr>
                                <th>Inward Inventory</th>
                                <th>Type</th>
                                <th>Todays usage</th>
                                <th>Total Remaining</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody class="tile_coupler_body">
                            <?php
                            $types = [
                                1 => '16mm',
                                2 => '20mm',
                                3 => '25mm',
                                4 => '20x16mm',
                                5 => '25x20mm'
                            ];

                            foreach ($types as $key => $label) {

                                $inward_inventory_ca = isset($dpr_coupler_data[$key - 1]['inward_inventory_ca']) ? $dpr_coupler_data[$key - 1]['inward_inventory_ca'] : '';
                                $today_usage_ca      = isset($dpr_coupler_data[$key - 1]['today_usage_ca']) ? $dpr_coupler_data[$key - 1]['today_usage_ca'] : '';
                                $remaining_cement_ca = isset($dpr_coupler_data[$key - 1]['remaining_cement_ca']) ? $dpr_coupler_data[$key - 1]['remaining_cement_ca'] : '';
                                $notes_ca            = isset($dpr_coupler_data[$key - 1]['notes_ca']) ? $dpr_coupler_data[$key - 1]['notes_ca'] : '';
                                $id                  = isset($dpr_coupler_data[$key - 1]['id']) ? $dpr_coupler_data[$key - 1]['id'] : '';
                            ?>
                                <tr>
                                    <td>
                                        <?php echo render_input('inward_inventory_ca[]', '', $inward_inventory_ca, 'number', [
                                            'oninput' => 'calculateRemainingca(this)'
                                        ]); ?>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" value="<?php echo $label; ?>" readonly>
                                        <input type="hidden" name="coupler_type[]" value="<?php echo $key; ?>">
                                    </td>

                                    <td>
                                        <?php echo render_input('today_usage_ca[]', '', $today_usage_ca, 'number', [
                                            'oninput' => 'calculateRemainingca(this)'
                                        ]); ?>
                                    </td>

                                    <td>
                                        <?php echo render_input('remaining_cement_ca[]', '', $remaining_cement_ca, 'number', [
                                            'readonly' => true
                                        ]); ?>
                                    </td>

                                    <td>
                                        <?php echo render_input('notes_ca[]', '', $notes_ca, 'text'); ?>
                                    </td>

                                    <input type="hidden" name="coupler_id[]" value="<?php echo $id; ?>">
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="wires">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table tile-wires-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th colspan="5" class="daily_report_title">Wires</th>
                            </tr>
                            <tr>
                                <th>Inward Inventory</th>
                                <th>Type</th>
                                <th>Todays usage</th>
                                <th>Total Remaining</th>
                                <th>Notes</th>
                            </tr>
                        </thead>

                        <tbody class="tile_wires_body">
                            <?php
                            $types = [
                                1 => '1 sqmm',
                                2 => '1.5 sqmm',
                                3 => '2.5 sqmm',
                                4 => '4 sqmm',
                                5 => '6 sqmm'
                            ];

                            foreach ($types as $key => $label) {

                                $inward = isset($dpr_wires_data[$key - 1]['inward_inventory_wi']) ? $dpr_wires_data[$key - 1]['inward_inventory_wi'] : '';
                                $usage  = isset($dpr_wires_data[$key - 1]['today_usage_wi']) ? $dpr_wires_data[$key - 1]['today_usage_wi'] : '';
                                $remain = isset($dpr_wires_data[$key - 1]['remaining_cement_wi']) ? $dpr_wires_data[$key - 1]['remaining_cement_wi'] : '';
                                $notes  = isset($dpr_wires_data[$key - 1]['notes_wi']) ? $dpr_wires_data[$key - 1]['notes_wi'] : '';
                                $id     = isset($dpr_wires_data[$key - 1]['id']) ? $dpr_wires_data[$key - 1]['id'] : '';
                            ?>
                                <tr>
                                    <td>
                                        <?php echo render_input('inward_inventory_wi[]', '', $inward, 'number', [
                                            'oninput' => 'calculateRemainingwi(this)'
                                        ]); ?>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" value="<?php echo $label; ?>" readonly>
                                        <input type="hidden" name="wire_type[]" value="<?php echo $key; ?>">
                                    </td>

                                    <td>
                                        <?php echo render_input('today_usage_wi[]', '', $usage, 'number', [
                                            'oninput' => 'calculateRemainingwi(this)'
                                        ]); ?>
                                    </td>

                                    <td>
                                        <?php echo render_input('remaining_cement_wi[]', '', $remain, 'number', [
                                            'readonly' => true
                                        ]); ?>
                                    </td>

                                    <td>
                                        <?php echo render_input('notes_wi[]', '', $notes, 'text'); ?>
                                    </td>

                                    <input type="hidden" name="wires_id[]" value="<?php echo $id; ?>">
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane ptop10" id="council_box">
            <div id="estimate-preview">
                <div class="row">
                    <table class="table tile-coucil-table items has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th colspan="4" class="daily_report_title">Council Box</th>
                            </tr>
                            <tr>
                                <th>Inward Inventory</th>
                                <th>Todays usage</th>
                                <th>Total Remaining cement</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody class="tile_wires_body">
                            <?php
                            $inward_inventory_cb = isset($dpr_council_data) ? $dpr_council_data[0]['inward_inventory_cb'] : '';
                            $today_usage_cb = isset($dpr_council_data) ? $dpr_council_data[0]['today_usage_cb'] : '';
                            $remaining_cement_cb = isset($dpr_council_data) ? $dpr_council_data[0]['remaining_cement_cb'] : '';
                            $notes_cb = isset($dpr_council_data) ? $dpr_council_data[0]['notes_cb'] : '';
                            ?>
                            <td class="inward_inventory_cb"><?php echo render_input('inward_inventory_cb', '', $inward_inventory_cb, 'number', ['id' => 'inward_inventory_cb', 'oninput' => 'calculateRemainingcb()']) ?></td>
                            <td class="today_usage_cb"><?php echo render_input('today_usage_cb', '', $today_usage_cb, 'number', ['id' => 'today_usage_cb', 'oninput' => 'calculateRemainingcb()']) ?></td>
                            <td class="remaining_cement_cb"><?php echo render_input('remaining_cement_cb', '', $remaining_cement_cb, 'number', ['id' => 'remaining_cement_cb', 'readonly' => true]) ?></td>
                            <td class="notes_cb"><?php echo render_input('notes_cb', '', $notes_cb, 'text', ['id' => 'notes_cb']) ?></td>
                            <input type="hidden" name="cb_id" value="<?php echo isset($dpr_wires_data) ? $dpr_wires_data[0]['id'] : ''; ?>">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    get_selected_project();
    $(document).on('change', "select[name='project_id']", function(event) {
        get_selected_project();
    });

    function get_selected_project() {
        var selectedText = $("select[name='project_id']").find("option:selected").text();
        $('.view_project_name').html(selectedText);
    }

    $(document).on('click', '.dpr-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? dpr_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.dpr-items-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        dpr_get_item_row_template('newitems[' + item_key + ']', data.location, data.agency, data.type, data.sub_type, data.work_execute, data.material_consumption, data.male, data.female, data.total, data.machinery, data.total_machinery, item_key).done(function(output) {
            table_row += output;

            $('.dpr_body').append(table_row);

            setTimeout(function() {
                dpr_calculate_total();
            }, 15);

            init_selectpicker();
            pur_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');

            return true;
        });
        return false;
    });

    function dpr_get_item_row_template(name, location, agency, type, sub_type, work_execute, material_consumption, male, female, total, machinery, total_machinery, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_dpr_row_template', {
            name: name,
            location: location,
            agency: agency,
            type: type,
            sub_type: sub_type,
            work_execute: work_execute,
            material_consumption: material_consumption,
            male: male,
            female: female,
            total: total,
            machinery: machinery,
            total_machinery: total_machinery,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function dpr_get_item_preview_values() {
        "use strict";

        var response = {};
        response.location = $('.dpr-items-table input[name="location"]').val();
        response.agency = $('.dpr-items-table select[name="agency"]').selectpicker('val');
        response.type = $('.dpr-items-table select[name="type"]').selectpicker('val');
        response.sub_type = $('.dpr-items-table textarea[name="sub_type"]').val();
        response.work_execute = $('.dpr-items-table input[name="work_execute"]').val();
        response.material_consumption = $('.dpr-items-table input[name="material_consumption"]').val();
        response.male = $('.dpr-items-table input[name="male"]').val();
        response.female = $('.dpr-items-table input[name="female"]').val();
        response.total = $('.dpr-items-table input[name="total"]').val();
        response.machinery = $('.dpr-items-table select[name="machinery"]').val();
        response.total_machinery = $('.dpr-items-table input[name="total_machinery"]').val();

        return response;
    }

    function pur_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function dpr_calculate_total() {
        "use strict";
        var rows = $('.dpr_body tr.item');

        $.each(rows, function() {
            var male = parseFloat($(this).find('td.male input').val()) || 0;
            var female = parseFloat($(this).find('td.female input').val()) || 0;
            var total = male + female;
            $(this).find('td.total input').val(total);
        });
    }

    function dpr_delete_item(row, itemid, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
                dpr_calculate_total();
            }, 50);
        });
        if (itemid && $('input[name="isedit"]').length > 0) {
            $(parent + ' #removed-items').append(hidden_input('removed_items[]', itemid));
        }
    }


    $(document).on('click', '.dpr-department-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? dpr_department_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.dpr-department-labour-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        dpr_department_get_item_row_template('newitemsdept[' + item_key + ']', data.staff, data.attendance, data.over_time, data.kharchi, item_key).done(function(output) {
            table_row += output;

            $('.dpr_department_body').append(table_row);



            init_selectpicker();
            pur_department_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');

            return true;
        });
        return false;
    });

    function dpr_department_get_item_preview_values() {
        "use strict";

        var response = {};
        response.staff = $('.dpr-department-labour-table select[name="staff"]').selectpicker('val');
        response.attendance = $('.dpr-department-labour-table input[name="attendance"]').val();
        response.over_time = $('.dpr-department-labour-table input[name="over_time"]').val();
        response.kharchi = $('.dpr-department-labour-table input[name="kharchi"]').val();
        return response;
    }

    function dpr_department_get_item_row_template(name, staff, attendance, over_time, kharchi, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_department_dpr_row_template', {
            name: name,
            staff: staff,
            attendance: attendance,
            over_time: over_time,
            kharchi: kharchi,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function pur_department_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_department_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function dpr_department_delete_item(row, itemid, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
            }, 50);
        });
        if (itemid && $('input[name="isedit"]').length > 0) {
            $(parent + ' #removed-department-items').append(hidden_input('removed_department_items[]', itemid));
        }
    }

    $(document).on('click', '.dpr-rmc-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? dpr_rmc_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.dpr-department-labour-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        dpr_rmc_get_item_row_template('newitemsrmc[' + item_key + ']', data.challan, data.grade, data.structure, data.quantity, item_key).done(function(output) {
            table_row += output;

            $('.dpr_rmc_body').append(table_row);



            init_selectpicker();
            pur_rmc_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');

            return true;
        });
        return false;
    });

    function dpr_rmc_get_item_preview_values() {
        "use strict";

        var response = {};
        response.challan = $('.dpr-rmc-table input[name="challan"]').val();
        response.grade = $('.dpr-rmc-table select[name="grade"]').selectpicker('val');
        response.structure = $('.dpr-rmc-table input[name="structure"]').val();
        response.quantity = $('.dpr-rmc-table input[name="quantity"]').val();
        return response;
    }

    function dpr_rmc_get_item_row_template(name, challan, grade, structure, quantity, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_rmc_dpr_row_template', {
            name: name,
            challan: challan,
            grade: grade,
            structure: structure,
            quantity: quantity,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function pur_rmc_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_rmc_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function dpr_rmc_delete_item(row, itemid, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
            }, 50);
        });
        if (itemid && $('input[name="isedit"]').length > 0) {
            $(parent + ' #removed-rmc-items').append(hidden_input('removed_rmc_items[]', itemid));
        }
    }


    $(document).on('click', '.dpr-material-add-item-to-table', function(event) {
        "use strict";

        var data = 'undefined';
        data = typeof(data) == 'undefined' || data == 'undefined' ? dpr_material_get_item_preview_values() : data;
        var table_row = '';
        var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.dpr-department-labour-table tbody .item').length + 1;
        lastAddedItemKey = item_key;

        dpr_material_get_item_row_template('newitemsmaterial[' + item_key + ']', data.challan, data.supplier, data.material_description, data.total, item_key).done(function(output) {
            table_row += output;

            $('.dpr_material_body').append(table_row);



            init_selectpicker();
            pur_material_clear_item_preview_values();
            $('body').find('#items-warning').remove();
            $("body").find('.dt-loader').remove();
            $('#item_select').selectpicker('val', '');

            return true;
        });
        return false;
    });

    function dpr_material_get_item_preview_values() {
        "use strict";

        var response = {};
        response.challan = $('.dpr-material-table input[name="challan"]').val();
        response.supplier = $('.dpr-material-table input[name="supplier"]').val();
        response.material_description = $('.dpr-material-table input[name="material_description"]').val();
        response.total = $('.dpr-material-table input[name="total"]').val();
        return response;
    }

    function dpr_material_get_item_row_template(name, challan, supplier, material_description, total, item_key) {
        "use strict";

        jQuery.ajaxSetup({
            async: false
        });

        var d = $.post(admin_url + 'forms/get_material_dpr_row_template', {
            name: name,
            challan: challan,
            supplier: supplier,
            material_description: material_description,
            total: total,
            item_key: item_key
        });
        jQuery.ajaxSetup({
            async: true
        });
        return d;
    }

    function pur_material_clear_item_preview_values() {
        "use strict";

        var previewArea = $('.dpr_material_body .main');
        previewArea.find('input').val('');
        previewArea.find('textarea').val('');
        previewArea.find('select').val('').selectpicker('refresh');
    }

    function dpr_material_delete_item(row, itemid, parent) {
        "use strict";

        $(row).parents('tr').addClass('animated fadeOut', function() {
            setTimeout(function() {
                $(row).parents('tr').remove();
            }, 50);
        });
        if (itemid && $('input[name="isedit"]').length > 0) {
            $(parent + ' #removed-material-items').append(hidden_input('removed_material_items[]', itemid));
        }
    }
</script>


<script>
    function calculateRemaining() {
        // Get the values from input fields
        let inwardInventory = document.getElementById('inward_inventory').value;
        let todayUsage = document.getElementById('today_usage').value;
        let remainingField = document.getElementById('remaining_cement');

        // Convert to numbers (empty values become 0)
        inwardInventory = inwardInventory === '' ? 0 : parseFloat(inwardInventory);
        todayUsage = todayUsage === '' ? 0 : parseFloat(todayUsage);

        // Calculate remaining
        let remaining = inwardInventory - todayUsage;

        // Update the remaining field
        remainingField.value = remaining;

        // Optional: Add visual feedback for negative values
        if (remaining < 0) {
            remainingField.style.backgroundColor = '#ffebee'; // Light red for negative
            remainingField.style.color = '#c62828'; // Dark red text
        } else {
            remainingField.style.backgroundColor = ''; // Reset to default
            remainingField.style.color = ''; // Reset to default
        }
    }

    // Add event listeners for real-time calculation
    document.addEventListener('DOMContentLoaded', function() {
        // Initial calculation if fields have values
        calculateRemaining();

        // Optional: Add keyup event for even more responsive updates
        document.getElementById('inward_inventory').addEventListener('keyup', calculateRemaining);
        document.getElementById('today_usage').addEventListener('keyup', calculateRemaining);
    });

    function calculateRemainingbmj() {
        // Get the values from input fields
        let inwardInventorybmj = document.getElementById('inward_inventory_bmj').value;
        let todayUsagebmj = document.getElementById('today_usage_bmj').value;
        let remainingFieldbmj = document.getElementById('remaining_cement_bmj');

        // Convert to numbers (empty values become 0)
        inwardInventorybmj = inwardInventorybmj === '' ? 0 : parseFloat(inwardInventorybmj);
        todayUsagebmj = todayUsagebmj === '' ? 0 : parseFloat(todayUsagebmj);

        // Calculate remaining
        let remaining = inwardInventorybmj - todayUsagebmj;

        // Update the remaining field
        remainingFieldbmj.value = remaining;

        // Optional: Add visual feedback for negative values
        if (remaining < 0) {
            remainingFieldbmj.style.backgroundColor = '#ffebee'; // Light red for negative
            remainingFieldbmj.style.color = '#c62828'; // Dark red text
        } else {
            remainingFieldbmj.style.backgroundColor = ''; // Reset to default
            remainingFieldbmj.style.color = ''; // Reset to default
        }
    }

    // Add event listeners for real-time calculation
    document.addEventListener('DOMContentLoaded', function() {
        // Initial calculation if fields have values
        calculateRemainingbmj();

        // Optional: Add keyup event for even more responsive updates
        document.getElementById('inward_inventory_bmj').addEventListener('keyup', calculateRemainingbmj);
        document.getElementById('today_usage_bmj').addEventListener('keyup', calculateRemainingbmj);
    });

    function calculateRemainingta() {
        // Get the values from input fields
        let inwardInventoryta = document.getElementById('inward_inventory_ta').value;
        let todayUsageta = document.getElementById('today_usage_ta').value;
        let remainingFieldta = document.getElementById('remaining_cement_ta');

        // Convert to numbers (empty values become 0)
        inwardInventoryta = inwardInventoryta === '' ? 0 : parseFloat(inwardInventoryta);
        todayUsageta = todayUsageta === '' ? 0 : parseFloat(todayUsageta);

        // Calculate remaining
        let remaining = inwardInventoryta - todayUsageta;

        // Update the remaining field
        remainingFieldta.value = remaining;

        // Optional: Add visual feedback for negative values
        if (remaining < 0) {
            remainingFieldta.style.backgroundColor = '#ffebee'; // Light red for negative
            remainingFieldta.style.color = '#c62828'; // Dark red text
        } else {
            remainingFieldta.style.backgroundColor = ''; // Reset to default
            remainingFieldta.style.color = ''; // Reset to default
        }
    }

    // Add event listeners for real-time calculation
    document.addEventListener('DOMContentLoaded', function() {
        // Initial calculation if fields have values
        calculateRemainingta();

        // Optional: Add keyup event for even more responsive updates
        document.getElementById('inward_inventory_ta').addEventListener('keyup', calculateRemainingta);
        document.getElementById('today_usage_ta').addEventListener('keyup', calculateRemainingta);
    });

    // Row-wise calculation
    function calculateRemainingca(el) {
        let row = el.closest('tr');

        let inwardField = row.querySelector('[name="inward_inventory_ca[]"]');
        let usageField = row.querySelector('[name="today_usage_ca[]"]');
        let remainingField = row.querySelector('[name="remaining_cement_ca[]"]');

        let inward = inwardField.value === '' ? 0 : parseFloat(inwardField.value);
        let usage = usageField.value === '' ? 0 : parseFloat(usageField.value);

        let remaining = inward - usage;

        remainingField.value = remaining;

        // Highlight negative values
        if (remaining < 0) {
            remainingField.style.backgroundColor = '#ffebee';
            remainingField.style.color = '#c62828';
        } else {
            remainingField.style.backgroundColor = '';
            remainingField.style.color = '';
        }
    }


    // Auto calculate on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tile_coupler_body tr').forEach(function(row) {
            let inward = row.querySelector('[name="inward_inventory_ca[]"]');
            if (inward) {
                calculateRemainingca(inward);
            }
        });
    });

    function calculateRemainingwi(el) {
        let row = el.closest('tr');

        let inwardField = row.querySelector('[name="inward_inventory_wi[]"]');
        let usageField = row.querySelector('[name="today_usage_wi[]"]');
        let remainingField = row.querySelector('[name="remaining_cement_wi[]"]');

        let inward = inwardField.value === '' ? 0 : parseFloat(inwardField.value);
        let usage = usageField.value === '' ? 0 : parseFloat(usageField.value);

        let remaining = inward - usage;

        remainingField.value = remaining;

        if (remaining < 0) {
            remainingField.style.backgroundColor = '#ffebee';
            remainingField.style.color = '#c62828';
        } else {
            remainingField.style.backgroundColor = '';
            remainingField.style.color = '';
        }
    }


    // Auto calculate on load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tile_wires_body tr').forEach(function(row) {
            let inward = row.querySelector('[name="inward_inventory_wi[]"]');
            if (inward) {
                calculateRemainingwi(inward);
            }
        });
    });

    function calculateRemainingcb() {
        // Get the values from input fields
        let inwardInventorycb = document.getElementById('inward_inventory_cb').value;
        let todayUsagecb = document.getElementById('today_usage_cb').value;
        let remainingFieldcb = document.getElementById('remaining_cement_cb');

        // Convert to numbers (empty values become 0)
        inwardInventorycb = inwardInventorycb === '' ? 0 : parseFloat(inwardInventorycb);
        todayUsagecb = todayUsagecb === '' ? 0 : parseFloat(todayUsagecb);

        // Calculate remaining
        let remaining = inwardInventorycb - todayUsagecb;

        // Update the remaining field
        remainingFieldcb.value = remaining;

        // Optional: Add visual feedback for negative values
        if (remaining < 0) {
            remainingFieldcb.style.backgroundColor = '#ffebee'; // Light red for negative
            remainingFieldcb.style.color = '#c62828'; // Dark red text
        } else {
            remainingFieldcb.style.backgroundColor = ''; // Reset to default
            remainingFieldcb.style.color = ''; // Reset to default
        }
    }

    // Add event listeners for real-time calculation
    document.addEventListener('DOMContentLoaded', function() {
        // Initial calculation if fields have values
        calculateRemainingcb();

        // Optional: Add keyup event for even more responsive updates
        document.getElementById('inward_inventory_cb').addEventListener('keyup', calculateRemainingcb);
        document.getElementById('today_usage_cb').addEventListener('keyup', calculateRemainingcb);
    });
</script>