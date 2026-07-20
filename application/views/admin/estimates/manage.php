<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <div id="vueApp">
                    <?php $this->load->view('admin/estimates/list_template'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
    var hidden_columns = [2, 5, 6, 8, 9];
</script>
<?php init_tail(); ?>
<script>
var table_estimates;
$(function(){
    init_estimate();
    table_estimates = $('.table-estimates');
    var Params = {
        "clients": "[name='clients[]']",
        "statuses": "[name='statuses[]']",
    };
    initDataTable('.table-estimates', admin_url + 'estimates/table_new', [], [], Params, [0, 'desc']);
    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {
          table_estimates.DataTable().ajax.reload();
        });
    });

    $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').selectpicker("val", "");
        table_estimates.DataTable().ajax.reload();
    });
    $(document).on('change', 'select[name="clients[]"]', function() {
        $('select[name="clients[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="statuses[]"]', function() {
        $('select[name="statuses[]"]').selectpicker('refresh');
    });
});
</script>
<script>
    $(document).ready(function() {
        var table = $('.table-estimates').DataTable();

        // Handle "Select All" checkbox
        $('#select-all-columns').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.toggle-column').prop('checked', isChecked).trigger('change');
        });

        // Handle individual column visibility toggling
        $('.toggle-column').on('change', function() {
            var column = table.column($(this).val());
            column.visible($(this).is(':checked'));

            // Sync "Select All" checkbox state
            var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
            $('#select-all-columns').prop('checked', allChecked);
        });

        // Sync checkboxes with column visibility on page load
        table.columns().every(function(index) {
            var column = this;
            $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
</body>

</html>