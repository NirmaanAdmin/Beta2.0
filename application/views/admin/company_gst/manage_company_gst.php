<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_company_gst(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('new_company_gst'); ?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([_l('id'), _l('name'), _l('description'), _l('options')], 'company-gst'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/company_gst/company_gst_modal'); ?>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-company-gst', window.location.href, [3], [3], 'undefined', [0, 'asc']);
});
</script>
</body>

</html>