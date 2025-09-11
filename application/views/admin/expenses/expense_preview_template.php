<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_expense" aria-controls="tab_expense" role="tab" data-toggle="tab">
                                <?php echo _l('expense'); ?>
                            </a>
                        </li>
                        <?php if (count($child_expenses) > 0 || $expense->recurring != 0) { ?>
                        <li role="presentation">
                            <a href="#tab_child_expenses" aria-controls="tab_child_expenses" role="tab"
                                data-toggle="tab">
                                <?php echo _l('child_expenses'); ?>
                            </a>
                        </li>
                        <?php } ?>
                        <li role="presentation">
                            <a href="#tab_tasks"
                                onclick="init_rel_tasks_table(<?php echo e($expense->expenseid); ?>,'expense'); return false;"
                                aria-controls="tab_tasks" role="tab" data-toggle="tab">
                                <?php echo _l('tasks'); ?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator">
                            <a href="#tab_reminders"
                                onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $expense->id ; ?> + '/' + 'expense', undefined, undefined,undefined,[1,'ASC']); return false;"
                                aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                <?php echo _l('expenses_reminders'); ?>
                                <?php
                        $total_reminders = total_rows(
    db_prefix() . 'reminders',
    [
                           'isnotified' => 0,
                           'staff'      => get_staff_user_id(),
                           'rel_type'   => 'expense',
                           'rel_id'     => $expense->expenseid,
                           ]
);
                        if ($total_reminders > 0) {
                            echo '<span class="badge">' . $total_reminders . '</span>';
                        }
                        ?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator toggle_view">
                            <a href="#" onclick="small_table_full_view(); return false;" data-placement="left"
                                data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>">
                                <i class="fa fa-expand"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row mtop20">
                <div class="col-md-6" id="expenseHeadings">
                    <h3 class="tw-font-semibold tw-text-lg tw-text-neutral-700 tw-mt-0 tw-mb-1" id="expenseCategory">
                        <?php echo e($expense->category_name); ?>
                    </h3>
                    <?php if (!empty($expense->expense_name)) { ?>
                    <h4 class="tw-text-sm tw-m-0 tw-text-neutral-500" id="expenseName">
                        <?php echo e($expense->expense_name); ?>
                    </h4>
                    <?php } ?>
                    <h4 class="tw-text-sm tw-m-0 tw-text-neutral-500" id="expenserCreator">
                        <?php echo _l('created_by'); ?>: <a
                            href="<?php echo admin_url('staff/profile/' . $expense->addedfrom) ?>">
                            <?php echo e(get_staff_full_name($expense->addedfrom)); ?>
                        </a>
                    </h4>
                </div>
                <div class="col-md-6 _buttons text-right">
                    <div class="visible-xs">
                        <div class="mtop10"></div>
                    </div>
                    <div class="pull-right">
                        <?php if(empty($expense->vbt_id)) { ?>
                            <a href="<?php echo admin_url('expenses/convert_pur_invoice_from_expense/' . $expense->id); ?>" class="btn btn-info convert-pur-invoice" data-url="<?php echo admin_url('expenses/convert_pur_invoice_from_expense/' . $expense->id); ?>">
                               <?php echo _l('convert_to_vendor_bill'); ?>
                            </a>
                        <?php } else { ?>
                            <span class="btn btn-success">Converted</span>
                        <?php } ?>
                        <?php if (staff_can('edit',  'expenses')) { ?>
                        <a class="btn btn-default btn-with-tooltip"
                            href="<?php echo admin_url('expenses/expense/' . $expense->expenseid); ?>"
                            data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('expense_edit'); ?>"><i
                                class="fa-regular fa-pen-to-square"></i></a>
                        <?php } ?>
                        <a class="btn btn-default btn-with-tooltip" href="<?php echo admin_url('expenses/pdf/' . $expense->expenseid . '?print=true'); ?>" target="_blank" data-toggle="tooltip"
                            data-placement="bottom" title="<?php echo _l('print'); ?>">
                            <i class="fa fa-print"></i>
                        </a>
                        <?php if (staff_can('create',  'expenses')) { ?>
                        <a class="btn btn-default btn-with-tooltip"
                            href="<?php echo admin_url('expenses/copy/' . $expense->expenseid); ?>"
                            data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('expense_copy'); ?>"><i
                                class="fa-regular fa-copy"></i></a>
                        <?php } ?>
                        <?php if (staff_can('delete',  'expenses')) { ?>
                        <a class="btn btn-danger btn-with-tooltip _delete"
                            href="<?php echo admin_url('expenses/delete/' . $expense->expenseid); ?>"
                            data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('expense_delete'); ?>"><i
                                class="fa fa-remove"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="hr-panel-separator" />
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_expense"
                    data-empty-note="<?php echo empty($expense->note); ?>"
                    data-empty-name="<?php echo empty($expense->expense_name); ?>">
                    <div class="row">
                        <?php
                if ($expense->recurring > 0 || $expense->recurring_from != null) {
                    echo '<div class="col-md-12">';

                    $recurring_expense           = $expense;
                    $show_recurring_expense_info = true;

                    if ($expense->recurring_from != null) {
                        $recurring_expense = $this->expenses_model->get($expense->recurring_from);
                        // Maybe recurring expense not longer recurring?
                        if ($recurring_expense->recurring == 0) {
                            $show_recurring_expense_info = false;
                        } else {
                            $next_recurring_date_compare = $recurring_expense->last_recurring_date;
                        }
                    } else {
                        $next_recurring_date_compare = $recurring_expense->date;
                        if ($recurring_expense->last_recurring_date) {
                            $next_recurring_date_compare = $recurring_expense->last_recurring_date;
                        }
                    }
                    if ($show_recurring_expense_info) {
                        $next_date = date('Y-m-d', strtotime('+' . $recurring_expense->repeat_every . ' ' . strtoupper($recurring_expense->recurring_type), strtotime($next_recurring_date_compare)));
                    } ?>
                        <?php if ($expense->recurring_from == null && $recurring_expense->cycles > 0 && $recurring_expense->cycles == $recurring_expense->total_cycles) { ?>
                        <div class="alert alert-info mbot15">
                            <?php echo e(_l('recurring_has_ended', _l('expense_lowercase'))); ?>
                        </div>
                        <?php } elseif ($show_recurring_expense_info) { ?>
                        <span class="label label-info">
                            <?php echo _l('cycles_remaining'); ?>:
                            <b class="tw-ml-2">
                                <?php
                                    echo e($recurring_expense->cycles == 0 ? _l('cycles_infinity') : $recurring_expense->cycles - $recurring_expense->total_cycles);
                                ?>
                            </b>
                        </span>
                        <?php if ($recurring_expense->cycles == 0 || $recurring_expense->cycles != $recurring_expense->total_cycles) {
                       echo '<span class="label label-info tw-ml-1"><i class="fa-regular fa-circle-question fa-fw" data-toggle="tooltip" data-title="' . _l('recurring_recreate_hour_notice', _l('expense')) . '"></i> ' . _l('next_expense_date', '<b class="tw-ml-1">' . e(_d($next_date)) . '</b>') . '</span>';
                   }
            }
                    if ($expense->recurring_from != null) { ?>
                        <?php echo '<p class="text-muted no-mbot' . ($show_recurring_expense_info ? ' mtop15': '') . '">' . _l('expense_recurring_from', '<a href="' . admin_url('expenses/list_expenses/' . $expense->recurring_from) . '" onclick="init_expense(' . $expense->recurring_from . ');return false;">' . e($recurring_expense->category_name) . (!empty($recurring_expense->expense_name) ? ' (' . e($recurring_expense->expense_name) . ')' : '') . '</a></p>'); ?>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-separator !tw-my-6" />
                    <?php
                } ?>
                    <div class="col-md-6">
                        <p>
                        <div id="amountWrapper">
                            <span class="bold font-medium"><?php echo _l('expense_amount'); ?></span>
                            <span
                                class="text-danger bold font-medium">
                                <?php echo e(app_format_money($expense->amount, $expense->currency_data)); ?>
                            </span>
                        </div>
                        <?php if ($expense->paymentmode != '0' && !empty($expense->paymentmode)) {
                    ?>
                        <span class="text-muted text-sm">
                            <?php echo e(_l('expense_paid_via', $expense->payment_mode_name)); ?>
                        </span><br />
                        <?php
                } ?>
                        <?php
                        if ($expense->tax != 0) {
                            echo '<br /><span class="bold">' . _l('tax_1') . ':</span> ' . e($expense->taxrate) . '% (' . e($expense->tax_name ). ')';
                            $total = $expense->amount;
                            $total += ($total / 100 * $expense->taxrate);
                        }
                        if ($expense->tax2 != 0) {
                            echo '<br /><span class="bold">' . _l('tax_2') . ':</span> ' . e($expense->taxrate2) . '% (' . e($expense->tax_name2) . ')';
                            $total += ($expense->amount / 100 * $expense->taxrate2);
                        }
                        if ($expense->tax != 0 || $expense->tax2 != 0) {
                            echo '<p class="font-medium bold text-danger">' . _l('total_with_tax') . ': ' . e(app_format_money($total, $expense->currency_data)) . '</p>';
                        }
                        ?>
                        <p><span class="bold"><?php echo _l('expense_date'); ?></span> <span
                                class="text-muted"><?php echo e(_d($expense->date)); ?></span></p>
                        <?php if (!empty($expense->reference_no)) { ?>
                            <p class="bold mbot15"><?php echo _l('expense_ref_noe'); ?> 
                                <span class="text-muted">
                                    <?php echo e($expense->reference_no); ?>
                                </span>
                            </p>
                        <?php } ?>
                        <?php if ($expense->clientid) { ?>
                        <p class="bold mbot5"><?php echo _l('expense_customer'); ?></p>
                        <p class="mbot15">
                            <a
                                href="<?php echo admin_url('clients/client/' . $expense->clientid); ?>">
                                <?php echo e($expense->company); ?>
                            </a>
                        </p>
                        <?php } ?>
                        <?php if ($expense->project_id) { ?>
                        <p class="bold mbot5"><?php echo _l('project'); ?></p>
                        <p class="mbot15">
                            <a
                                href="<?php echo admin_url('projects/view/' . $expense->project_id); ?>">
                                <?php echo e($expense->project_data->name); ?>
                            </a>
                        </p>
                        <?php } ?>
                        <?php if ($expense->vendor) { ?>
                        <p class="bold mbot5"><?php echo _l('vendor'); ?></p>
                        <p class="mbot15">
                            <a
                                href="<?php echo admin_url('purchase/vendor/' . $expense->vendor); ?>">
                                <?php echo get_vendor_company_name($expense->vendor); ?>
                            </a>
                        </p>
                        <?php } ?>
                        <?php
                     $custom_fields = get_custom_fields('expenses');
                     foreach ($custom_fields as $field) { ?>
                        <?php $value = get_custom_field_value($expense->expenseid, $field['id'], 'expenses');
                     if ($value == '') {
                         continue;
                     } ?>
                        <div class="row mbot10">
                            <div class="col-md-12 mtop5">
                                <p class="mbot5">
                                    <span class="bold"><?php echo e(ucfirst($field['name'])); ?></span>
                                </p>
                                <div class="text-left">
                                    <?php echo $value; ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if ($expense->note != '') { ?>
                            <p class="bold mbot5"><?php echo _l('expense_note'); ?></p>
                            <div class="text-muted mbot15">
                                <?php echo process_text_content_for_display($expense->note); ?>
                            </div>
                        <?php } ?>
                        <?php hooks()->do_action('after_left_panel_expense_preview_template', $expense); ?>
                    </div>
                    <div class="col-md-6" id="expenseReceipt">

                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <i class="fa-solid fa-receipt tw-lg tw-text-neutral-400 tw-mr-1.5"></i>
                            <span>
                                <?php echo _l('expense_receipt'); ?>
                            </span>
                        </h4>

                        <?php if (empty($attachments)) { ?>
                        <?php echo form_open('admin/expenses/add_expense_attachment/' . $expense->expenseid, ['class' => 'mtop10 dropzone dropzone-expense-preview dropzone-manual', 'id' => 'expense-receipt-upload']); ?>
                        <div id="dropzoneDragArea" class="dz-default dz-message">
                            <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
                        </div>
                        <?php echo form_close(); ?>
                        <?php } else { ?>
                            <?php
                            if(!empty($attachments)) { 
                                foreach ($attachments as $akey => $avalue) { ?>
                                    <div style="margin-bottom: 10px;">
                                        <div class="col-md-10">
                                            <?php
                                            $path = get_upload_path_by_type('expense') . $expense->expenseid . '/' . $avalue['file_name'];
                                            $is_image = is_image($path);
                                            if ($is_image) {
                                               echo '<div class="preview_image">';
                                            }
                                            ?>
                                            <a href="<?php echo site_url('download/file/expense/' . $avalue['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-expense-<?php echo $avalue['id']; ?>" <?php } ?>>
                                               <a name="expense-btn" onclick="preview_expense_btn(this); return false;" id = "<?php echo $avalue['id']; ?>" href="Javascript:void(0);" class="mbot10 mright5 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="<?php echo _l('preview_file'); ?>"><i class="fa fa-eye"></i></a>
                                               <?php echo $avalue['file_name']; ?>
                                               <?php if ($is_image) { ?>
                                                  <img class="mtop5 hide" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $avalue['filetype']); ?>" style="height: 165px;">
                                               <?php } ?>
                                            </a>
                                        </div>

                                        <?php if ($avalue['staffid'] == get_staff_user_id() || is_admin()) { ?>
                                            <a class="_delete text-danger" href="<?php echo admin_url('expenses/delete_expense_attachment/' . $avalue['id'] . '/' . 'preview'); ?>" class="text-danger"><i class="fa fa fa-times"></i></a>
                                            <a class="text-danger mleft5" href="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $avalue['filetype']); ?>"
                                               download>
                                               <i class="fa fa-solid fa-download"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <?php hooks()->do_action('after_right_panel_expense_preview_template', $expense); ?>
                    </div>
                </div>
            </div>
            </div>

            <div id="expense_file_data"></div>

            <?php if (count($child_expenses) > 0 || $expense->recurring != 0) { ?>
            <div role="tabpanel" class="tab-pane" id="tab_child_expenses">
                <?php if (count($child_expenses) > 0) { ?>
                <h4 class="mbot25 mtop25"><?php echo _l('expenses_created_from_this_recurring_expense'); ?></h4>
                <ul class="list-group">
                    <?php foreach ($child_expenses as $recurring) { ?>
                    <li class="list-group-item">
                        <a href="<?php echo admin_url('expenses/list_expenses/' . $recurring->expenseid); ?>"
                            onclick="init_expense(<?php echo e($recurring->expenseid); ?>); return false;"
                            target="_blank"><?php echo e($recurring->category_name . (!empty($recurring->expense_name) ? ' (' . e($recurring->expense_name) . ')' : '')); ?>
                        </a>
                        <br />
                        <span class="inline-block mtop10">
                            <?php echo '<span class="bold">' . e(_d($recurring->date)) . '</span>'; ?><br />
                            <p><span class="bold font-medium"><?php echo _l('expense_amount'); ?></span>
                                <span
                                    class="text-danger bold font-medium">
                                    <?php echo e(app_format_money($recurring->amount, $recurring->currency_data)); ?>
                                </span>
                                <?php
                           if ($recurring->tax != 0) {
                               echo '<br /><span class="bold">' . _l('tax_1') . ':</span> ' . e($recurring->taxrate) . '% (' . e($recurring->tax_name) . ')';
                               $total = $recurring->amount;
                               $total += ($total / 100 * $recurring->taxrate);
                           }
                           if ($recurring->tax2 != 0) {
                               echo '<br /><span class="bold">' . _l('tax_2') . ':</span> ' . e($recurring->taxrate2) . '% (' . e($recurring->tax_name2) . ')';
                               $total += ($recurring->amount / 100 * $recurring->taxrate2);
                           }
                           if ($recurring->tax != 0 || $recurring->tax2 != 0) {
                               echo '<p class="font-medium bold text-danger">' . _l('total_with_tax') . ': ' . e(app_format_money($total, $recurring->currency_data)) . '</p>';
                           }
                           ?>
                        </span>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                    <p class="bold"><?php echo e(_l('no_child_found', _l('expenses'))); ?></p>
                <?php } ?>
            </div>
            <?php } ?>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
                <?php init_relation_tasks_table(['data-new-rel-id' => $expense->expenseid, 'data-new-rel-type' => 'expense'], 'tasksFilters'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_reminders">
                <a href="#" data-toggle="modal" class="btn btn-default"
                    data-target=".reminder-modal-expense-<?php echo e($expense->id); ?>"><i class="fa-regular fa-bell"></i>
                    <?php echo _l('expense_set_reminder_title'); ?></a>
                <hr />
                <?php render_datatable([ _l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                <?php $this->load->view('admin/includes/modals/reminder', ['id' => $expense->id, 'name' => 'expense', 'members' => $members, 'reminder_title' => _l('expense_set_reminder_title')]); ?>
            </div>
        </div>
    </div>
</div>
</div>
<script>
init_btn_with_tooltips();
init_selectpicker();
init_datepicker();
init_form_reminder();
init_tabs_scrollable();

if ($('#dropzoneDragArea').length > 0) {
    if (typeof(expensePreviewDropzone) != 'undefined') {
        expensePreviewDropzone.destroy();
    }
    expensePreviewDropzone = new Dropzone("#expense-receipt-upload", appCreateDropzoneOptions({
        clickable: '#dropzoneDragArea',
        uploadMultiple: true,
        success: function(file, response) {
            init_expense(<?php echo e($expense->expenseid); ?>);
        }
    }));
}

$("body").on('change', 'select[name="applied_to_invoice"]', function () {
    var invoice_id = $(this).val();
    if(invoice_id != '') {
        if (confirm("Are you sure?")) {
            $.post(admin_url + 'expenses/applied_to_invoice', {invoice_id: invoice_id, expense_id: <?php echo e($expense->expenseid); ?>}).done(function(response) {
                response = JSON.parse(response);
                if(response.status) {
                    alert_float('success',response.message);
                } else {
                    alert_float('warning',response.message);
                }
                window.location.assign(response.url);
            });
        }
    } else {
        alert_float('warning', "Please select the valid invoice." );
    }
});

$(document).on('click', '.convert-pur-invoice', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    Swal.fire({
      title: 'Are you sure?',
      text: "Do you want to convert this to a vendor bill?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, convert it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        window.open(url, '_blank');
      }
    });
});

function preview_expense_btn(invoker){
  "use strict"; 
  var id = $(invoker).attr('id');
  view_expense_file(id);
}

function view_expense_file(id) {
  "use strict"; 
  $('#expense_file_data').empty();
  $("#expense_file_data").load(admin_url + 'expenses/view_expense_file/' + id, function(response, status, xhr) {
      if (status == "error") {
          alert_float('danger', xhr.statusText);
      }
  });
}

function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

</script>