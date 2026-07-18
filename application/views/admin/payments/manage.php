<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'payments'; ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row all_filters">
                    <?php
                    $invoices_filter = get_module_filter($module_name, 'invoices');
                    $invoices_filter_val = !empty($invoices_filter) ? explode(",", $invoices_filter->filter_value) : [];
                    ?>
                    <div class="col-md-3 form-group">
                        <label for="invoices"><?php echo _l('invoices'); ?></label>
                        <select name="invoices[]" id="invoices" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                            <?php foreach ($invoices as $invoice) { ?>
                                <option value="<?php echo pur_html_entity_decode($invoice['id']); ?>"
                                    <?php if (in_array($invoice['id'], $invoices_filter_val)) {
                                        echo 'selected';
                                    } ?>>
                                    <?php echo format_invoice_number($invoice['id']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <?php
                    $last_action_by_filter = get_module_filter($module_name, 'last_action_by');
                    $last_action_by_filter_val = !empty($last_action_by_filter) ? explode(",", $last_action_by_filter->filter_value) : [];
                    ?>
                    <div class="col-md-3 form-group">
                        <label for="last_action_by"><?php echo _l('last_action_by'); ?></label>
                        <select name="last_action_by[]" id="last_action_by" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                            <?php foreach ($staffs as $staff) { ?>
                                <option value="<?php echo pur_html_entity_decode($staff['staffid']); ?>"
                                    <?php if (in_array($staff['staffid'], $last_action_by_filter_val)) {
                                        echo 'selected';
                                    } ?>>
                                    <?php echo pur_html_entity_decode($staff['firstname']).' '.pur_html_entity_decode($staff['lastname']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-1 form-group">
                        <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                            <?php echo _l('reset_filter'); ?>
                        </a>
                    </div>
                </div>
                <hr class="hr-panel-separator" />

                <div class="panel-table-full">
                    <?php $this->load->view('admin/payments/table_html'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
var table_payments;
$(function() {
    table_payments = $('.table-payments');
    var Params = {
        "invoices": "[name='invoices[]']",
        "last_action_by": "[name='last_action_by[]']",
    };
    initDataTable('.table-payments', admin_url + 'payments/table', undefined, undefined, Params,
        <?php echo hooks()->apply_filters('payments_table_default_order', json_encode([0, 'desc'])); ?>);
    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {
            table_payments.DataTable().ajax.reload();
        });
    });

    $('.table-payments').on('draw.dt', function () {
        var reportsTable = $(this).DataTable();
        var sums = reportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(1).html("Total (Per Page)");
        $(this).find('tfoot td.total_payments_amount').html(sums.total_payments_amount);
    });

    $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').selectpicker("val", "");
        table_payments.DataTable().ajax.reload();
    });
    $(document).on('change', 'select[name="invoices[]"]', function() {
        $('select[name="invoices[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="last_action_by[]"]', function() {
        $('select[name="last_action_by[]"]').selectpicker('refresh');
    });
});
</script>
</body>

</html>