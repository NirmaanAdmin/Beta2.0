<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'creditnotes'; ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <div class="_buttons">
                        <?php if (staff_can('create',  'credit_notes')) { ?>
                        <a href="<?php echo admin_url('credit_notes/credit_note'); ?>"
                            class="btn btn-primary pull-left display-block">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_credit_note'); ?>
                        </a>
                        <?php } ?>
                        <div class="display-block pull-right">
                            <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
                                onclick="toggle_small_view('.table-credit-notes','#credit_note'); return false;"
                                data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                    class="fa fa-angle-double-left"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body panel-table-full">
                                <div class="row all_filters">
                                    <?php
                                    $clients_filter = get_module_filter($module_name, 'clients');
                                    $clients_filter_val = !empty($clients_filter) ? explode(",", $clients_filter->filter_value) : [];
                                    ?>
                                    <div class="col-md-3 form-group">
                                        <label for="clients"><?php echo _l('clients'); ?></label>
                                        <select name="clients[]" id="clients" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                            <?php foreach ($clients as $client) { ?>
                                                <option value="<?php echo pur_html_entity_decode($client['userid']); ?>"
                                                    <?php if (in_array($client['userid'], $clients_filter_val)) {
                                                        echo 'selected';
                                                    } ?>>
                                                    <?php echo pur_html_entity_decode($client['company']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <?php
                                    $statuses_filter = get_module_filter($module_name, 'statuses');
                                    $statuses_filter_val = !empty($statuses_filter) ? explode(",", $statuses_filter->filter_value) : [];
                                    ?>
                                    <div class="col-md-3 form-group">
                                        <label for="status"><?php echo _l('Status'); ?></label>
                                        <select name="statuses[]" id="statuses" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
                                            <?php foreach ($statuses as $status) { ?>
                                                <option value="<?php echo pur_html_entity_decode($status['id']); ?>"
                                                    <?php if (in_array($status['id'], $statuses_filter_val)) {
                                                        echo 'selected';
                                                    } ?>>
                                                    <?php echo pur_html_entity_decode($status['name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- if credit not id found in url -->
                                <?php echo form_hidden('credit_note_id', $credit_note_id); ?>
                                <?php $this->load->view('admin/credit_notes/table_html'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 small-table-right-col">
                        <div id="credit_note" class="hide">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
var hidden_columns = [4, 5, 6, 7];
</script>
<?php init_tail(); ?>
<script>
var table_credit_notes;
$(function() {
    table_credit_notes = $('.table-credit-notes');
    var Params = {
        "clients": "[name='clients[]']",
        "statuses": "[name='statuses[]']",
    };
    initDataTable('.table-credit-notes', admin_url + 'credit_notes/table_new', ['undefined'], ['undefined'],
        Params, [
            [1, 'desc'],
            [0, 'desc']
        ]);
    init_credit_note();
    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {
          table_credit_notes.DataTable().ajax.reload();
        });
    });

    $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').selectpicker("val", "");
        table_credit_notes.DataTable().ajax.reload();
    });
    $(document).on('change', 'select[name="clients[]"]', function() {
        $('select[name="clients[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="statuses[]"]', function() {
        $('select[name="statuses[]"]').selectpicker('refresh');
    });
});
</script>
</body>

</html>