<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), ['id' => 'invoice-form', 'class' => '_transaction_form invoice-form']);
            if (isset($invoice)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e(isset($invoice) ? format_invoice_number($invoice) : _l('create_new_invoice')); ?>
                    </span>
                    <?php echo isset($invoice) ? format_invoice_status($invoice->status) : ''; ?>
                </h4>
                <?php $this->load->view('admin/invoices/invoice_template'); ?>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view('admin/invoice_items/item'); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    validate_invoice_form();
    // Init accountacy currency symbol
    init_currency();
    // Project ajax search
    init_ajax_project_search_by_customer_id();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');

    toggle_invoice_cgst();
    toggle_invoice_sgst();
    $("body").on("change", "#cgst_type", function (e) {
      toggle_invoice_cgst();
    });
    $("body").on("change", "#sgst_type", function (e) {
      toggle_invoice_sgst();
    });
    function toggle_invoice_cgst() {
      var cgst_type = $('#cgst_type').val();
      if (cgst_type == '1') {
        $('#cgst_percentage_wrapper').show();
        $('#cgst_amount_wrapper').hide();
      } else if (cgst_type == '2') {
        $('#cgst_percentage_wrapper').hide();
        $('#cgst_amount_wrapper').show();
      }
    }
    function toggle_invoice_sgst() {
      var sgst_type = $('#sgst_type').val();
      if (sgst_type == '1') {
        $('#sgst_percentage_wrapper').show();
        $('#sgst_amount_wrapper').hide();
      } else if (sgst_type == '2') {
        $('#sgst_percentage_wrapper').hide();
        $('#sgst_amount_wrapper').show();
      }
    }
});
</script>
</body>

</html>