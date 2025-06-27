<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .export-btn-div {
        position: absolute;
        z-index: 999; 
        left: 140px;
    }
</style>
<a href="#" onclick="new_project_dir();return false;" class="btn btn-primary tw-mb-2 sm:tw-mb-4">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?php echo _l('new'); ?>
</a>
<div class="panel_s">
    <div class="panel-body panel-table-full">
        <div class="btn-group export-btn-div" id="export-btn-div">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                <i class="fa fa-download"></i> <?php echo _l('Export'); ?> <span class="caret"></span>
            </button>
            <div class="dropdown-menu" style="padding: 10px;min-width: 94px;">
                <a class="dropdown-item export-btn" href="<?php echo admin_url('projects/project_directory_pdf/' . $project->id); ?>" data-type="pdf">
                    <i class="fa fa-file-pdf text-danger"></i> PDF
                </a><br>
                <!-- <a class="dropdown-item export-btn" href="<?php echo admin_url('projects/project_directory_excel/' . $project->id); ?>" data-type="excel">
                    <i class="fa fa-file-excel text-success"></i> Excel
                </a> -->
            </div>
        </div>
        <input type="hidden" id="project_id" name="project_id" value="<?php echo $project->id; ?>">
        <?php $table_data = [
            _l('#'),
            _l('Company/Consultant'),
            _l('Address'),
            _l('Name'),
            _l('Designation'),
            _l('Contact'),
            _l('Email Account'),
        ];
        $table_data = hooks()->apply_filters('projects_directory_table_columns', $table_data);
        render_datatable($table_data, 'projectdirectory');
        ?>
        <?php $this->load->view('admin/projects/directory'); ?>
    </div>
</div>
<script>
    function new_project_dir() {
        $("#project_dir").modal("show");
    }
</script>

<script>
    
</script>