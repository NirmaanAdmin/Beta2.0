<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="<?php if (!isset($invoice) || (isset($invoice) && count($invoices_to_merge) == 0 && (isset($invoice) && !isset($invoice_from_project) && count($expenses_to_bill) == 0 || $invoice->status == Invoices_model::STATUS_CANCELLED))) {
                echo ' hide';
            } ?>" id="invoice_top_info">
    <div class="alert alert-info hide">
        <div class="row">
            <div id="merge" class="col-md-6">
                <?php
                if (isset($invoice)) {
                    $this->load->view('admin/invoices/merge_invoice', ['invoices_to_merge' => $invoices_to_merge]);
                }
                ?>
            </div>
            <!--  When invoicing from project area the expenses are not visible here because you can select to bill expenses while trying to invoice project -->
            <?php if (!isset($invoice_from_project)) { ?>
                <div id="expenses_to_bill" class="col-md-6">
                    <?php if (isset($invoice) && $invoice->status != Invoices_model::STATUS_CANCELLED) {
                        $this->load->view('admin/invoices/bill_expenses', ['expenses_to_bill' => $expenses_to_bill, 'invoice' => $invoice]);
                    } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="panel_s invoice accounting-template">
    <div class="additional"></div>
    <div class="panel-body">
        <?php hooks()->do_action('before_render_invoice_template', $invoice ?? null); ?>
        <?php if (isset($invoice)) {
            echo form_hidden('merge_current_invoice', $invoice->id);
        } ?>
        <div class="row">
            <div class="col-md-6">
                <?php $invoice_title = (isset($invoice) ? $invoice->title : '');
                echo render_input('title', 'invoice_title', $invoice_title); ?>
                <div class="f_client_id">
                    <div class="form-group select-placeholder">
                        <label for="clientid" class="control-label"><?php echo _l('invoice_select_customer'); ?></label>
                        <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if (isset($invoice) && empty($invoice->clientid)) {
                                                                                                                                echo ' customer-removed';
                                                                                                                            } ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php $selected = (isset($invoice) ? $invoice->clientid : '');
                            if ($selected == '') {
                                $selected = (isset($customer_id) ? $customer_id : '');
                            }
                            if ($selected != '') {
                                $rel_data = get_relation_data('customer', $selected);
                                $rel_val  = get_relation_values($rel_data, 'customer');
                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <?php
                if (!isset($invoice_from_project)) { ?>
                    <div class="form-group select-placeholder projects-wrapper<?php if ((!isset($invoice)) || (isset($invoice) && !customer_has_projects($invoice->clientid))) {
                                                                                    echo (isset($customer_id) && (!isset($project_id) || !$project_id)) ?  ' hide' : '';
                                                                                } ?>">
                        <label for="project_id"><?php echo _l('project'); ?></label>
                        <div id="project_ajax_search_wrapper">
                            <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true"
                                data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php
                                if (!isset($project_id)) {
                                    $project_id = '';
                                }
                                if (isset($invoice) && $invoice->project_id) {
                                    $project_id = $invoice->project_id;
                                }
                                if ($project_id) {
                                    echo '<option value="' . $project_id . '" selected>' . e(get_project_name_by_id($project_id)) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                        <hr class="hr-10" />
                        <a href="#" class="edit_shipping_billing_info" data-toggle="modal"
                            data-target="#billing_and_shipping_details"><i class="fa-regular fa-pen-to-square"></i></a>
                        <?php include_once(APPPATH . 'views/admin/invoices/billing_and_shipping_template.php'); ?>
                    </div>
                    <div class="col-md-6">
                        <p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
                        <address>
                            <span class="billing_street">
                                <?php $billing_street = (isset($invoice) ? $invoice->billing_street : '--'); ?>
                                <?php $billing_street = ($billing_street == '' ? '--' : $billing_street); ?>
                                <?php echo process_text_content_for_display($billing_street); ?></span><br>
                            <span class="billing_city">
                                <?php $billing_city = (isset($invoice) ? $invoice->billing_city : '--'); ?>
                                <?php $billing_city = ($billing_city == '' ? '--' : $billing_city); ?>
                                <?php echo e($billing_city); ?></span>,
                            <span class="billing_state">
                                <?php $billing_state = (isset($invoice) ? $invoice->billing_state : '--'); ?>
                                <?php $billing_state = ($billing_state == '' ? '--' : $billing_state); ?>
                                <?php echo e($billing_state); ?></span>
                            <br />
                            <span class="billing_country">
                                <?php $billing_country = (isset($invoice) ? get_country_short_name($invoice->billing_country) : '--'); ?>
                                <?php $billing_country = ($billing_country == '' ? '--' : $billing_country); ?>
                                <?php echo e($billing_country); ?></span>,
                            <span class="billing_zip">
                                <?php $billing_zip = (isset($invoice) ? $invoice->billing_zip : '--'); ?>
                                <?php $billing_zip = ($billing_zip == '' ? '--' : $billing_zip); ?>
                                <?php echo e($billing_zip); ?></span>
                        </address>
                    </div>
                    <div class="col-md-6">
                        <p class="bold"><?php echo _l('ship_to'); ?></p>
                        <address>
                            <span class="shipping_street">
                                <?php $shipping_street = (isset($invoice) ? $invoice->shipping_street : '--'); ?>
                                <?php $shipping_street = ($shipping_street == '' ? '--' : $shipping_street); ?>
                                <?php echo process_text_content_for_display($shipping_street); ?></span><br>
                            <span class="shipping_city">
                                <?php $shipping_city = (isset($invoice) ? $invoice->shipping_city : '--'); ?>
                                <?php $shipping_city = ($shipping_city == '' ? '--' : $shipping_city); ?>
                                <?php echo e($shipping_city); ?></span>,
                            <span class="shipping_state">
                                <?php $shipping_state = (isset($invoice) ? $invoice->shipping_state : '--'); ?>
                                <?php $shipping_state = ($shipping_state == '' ? '--' : $shipping_state); ?>
                                <?php echo e($shipping_state); ?></span>
                            <br />
                            <span class="shipping_country">
                                <?php $shipping_country = (isset($invoice) ? get_country_short_name($invoice->shipping_country) : '--'); ?>
                                <?php $shipping_country = ($shipping_country == '' ? '--' : $shipping_country); ?>
                                <?php echo e($shipping_country); ?></span>,
                            <span class="shipping_zip">
                                <?php $shipping_zip = (isset($invoice) ? $invoice->shipping_zip : '--'); ?>
                                <?php $shipping_zip = ($shipping_zip == '' ? '--' : $shipping_zip); ?>
                                <?php echo e($shipping_zip); ?></span>
                        </address>
                    </div>
                </div>
                <?php
                $next_invoice_number = get_option('next_invoice_number');
                $format              = get_option('invoice_number_format');

                if (isset($invoice)) {
                    $format = $invoice->number_format;
                }

                $prefix = get_option('invoice_prefix');

                if ($format == 1) {
                    $__number = $next_invoice_number;
                    if (isset($invoice)) {
                        $__number = $invoice->number;
                        $prefix   = '<span id="prefix">' . $invoice->prefix . '</span>';
                    }
                } elseif ($format == 2) {
                    if (isset($invoice)) {
                        $__number = $invoice->number;
                        $prefix   = $invoice->prefix;
                        $prefix   = '<span id="prefix">' . $prefix . '</span><span id="prefix_year">' . date('Y', strtotime($invoice->date)) . '</span>/';
                    } else {
                        $__number = $next_invoice_number;
                        $prefix   = $prefix . '<span id="prefix_year">' . date('Y') . '</span>/';
                    }
                } elseif ($format == 3) {
                    if (isset($invoice)) {
                        $yy       = date('y', strtotime($invoice->date));
                        $__number = $invoice->number;
                        $prefix   = '<span id="prefix">' . $invoice->prefix . '</span>';
                    } else {
                        $yy       = date('y');
                        $__number = $next_invoice_number;
                    }
                } elseif ($format == 4) {
                    if (isset($invoice)) {
                        $yyyy     = date('Y', strtotime($invoice->date));
                        $mm       = date('m', strtotime($invoice->date));
                        $__number = $invoice->number;
                        $prefix   = '<span id="prefix">' . $invoice->prefix . '</span>';
                    } else {
                        $yyyy     = date('Y');
                        $mm       = date('m');
                        $__number = $next_invoice_number;
                    }
                }

                $_is_draft            = (isset($invoice) && $invoice->status == Invoices_model::STATUS_DRAFT) ? true : false;
                $_invoice_number      = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                $isedit               = isset($invoice) ? 'true' : 'false';
                $data_original_number = isset($invoice) ? $invoice->number : 'false';

                ?>
                <div class="form-group">
                    <label for="number">
                        <?php echo _l('invoice_add_edit_number'); ?>
                        <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                            data-title="<?php echo _l('invoice_number_not_applied_on_draft') ?>"
                            data-placement="top"></i>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <?php if (isset($invoice)) { ?>
                                <a href="#" onclick="return false;" data-toggle="popover"
                                    data-container='._transaction_form' data-html="true"
                                    data-content="<label class='control-label'><?php echo _l('settings_sales_invoice_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo e($invoice->prefix); ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('invoices/update_number_settings/' . $invoice->id); ?>' class='btn btn-primary btn-block mtop15'><?php echo _l('submit'); ?></button>">
                                    <i class="fa fa-cog"></i>
                                </a>
                            <?php }
                            echo $prefix;
                            ?>
                        </span>
                        <input type="text" name="number" class="form-control"
                            value="<?php echo ($_is_draft) ? 'PROFORMA' : $_invoice_number; ?>"
                            data-isedit="<?php echo e($isedit); ?>"
                            data-original-number="<?php echo e($data_original_number); ?>"
                            <?php echo ($_is_draft) ? 'disabled' : '' ?>>
                        <?php if ($format == 3) { ?>
                            <span class="input-group-addon">
                                <span id="prefix_year" class="format-n-yy"><?php echo e($yy); ?></span>
                            </span>
                        <?php } elseif ($format == 4) { ?>
                            <span class="input-group-addon">
                                <span id="prefix_month" class="format-mm-yyyy"><?php echo e($mm); ?></span>
                                /
                                <span id="prefix_year" class="format-mm-yyyy"><?php echo e($yyyy); ?></span>
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php $value = (isset($invoice) ? _d($invoice->date) : _d(date('Y-m-d')));
                        $date_attrs        = [];
                        if (isset($invoice) && $invoice->recurring > 0 && $invoice->last_recurring_date != null) {
                            $date_attrs['disabled'] = true;
                        }
                        ?>
                        <?php echo render_date_input('date', 'invoice_add_edit_date', $value, $date_attrs); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $value = '';
                        if (isset($invoice)) {
                            $value = _d($invoice->duedate);
                        } else {
                            if (get_option('invoice_due_after') != 0) {
                                $value = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                            }
                        }
                        ?>
                        <?php echo render_date_input('duedate', 'invoice_add_edit_duedate', $value); ?>
                    </div>
                </div>

                <?php if (is_invoices_overdue_reminders_enabled()) { ?>
                    <div class="form-group">
                        <div class="checkbox checkbox-danger">
                            <input type="checkbox" <?php if (isset($invoice) && $invoice->cancel_overdue_reminders == 1) {
                                                        echo 'checked';
                                                    } ?> id="cancel_overdue_reminders" name="cancel_overdue_reminders">
                            <label
                                for="cancel_overdue_reminders"><?php echo _l('cancel_overdue_reminders_invoice') ?></label>
                        </div>
                    </div>
                <?php } ?>
                <?php $rel_id = (isset($invoice) ? $invoice->id : false); ?>
                <?php
                if (isset($custom_fields_rel_transfer)) {
                    $rel_id = $custom_fields_rel_transfer;
                }
                ?>
                <?php echo render_custom_fields('invoice', $rel_id); ?>
            </div>
            <div class="col-md-6">
                <div class="tw-ml-3">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                <?php echo _l('tags'); ?></label>
                            <input type="text" class="tagsinput" id="tags" name="tags"
                                value="<?php echo (isset($invoice) ? prep_tags_input(get_tags_in($invoice->id, 'invoice')) : ''); ?>"
                                data-role="tagsinput">
                        </div>
                        <div class="col-md-4">
                            <label for="place_of_supply_of_services"
                                class="control-label"><?php echo _l('place_of_supply_of_services'); ?></label>
                            <?php $value = (isset($invoice)  ? $invoice->place_of_supply_of_services : ''); ?>
                            <?php echo render_input('place_of_supply_of_services', '', $value, ''); ?>

                        </div>
                        <div class="col-md-4">
                            <label for="services_provided_location"
                                class="control-label"><?php echo _l('services_provided_location'); ?></label>
                            <?php $value = (isset($invoice)  ? $invoice->services_provided_location : ''); ?>
                            <?php echo render_input('services_provided_location', '', $value, ''); ?>

                        </div>


                    </div>
                    <div class="form-group mbot15<?= count($payment_modes) > 0 ? ' select-placeholder' : ''; ?>">
                        <label for="allowed_payment_modes"
                            class="control-label"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></label>
                        <br />
                        <?php if (count($payment_modes) > 0) { ?>
                            <select class="selectpicker"
                                data-toggle="<?php echo $this->input->get('allowed_payment_modes'); ?>"
                                name="allowed_payment_modes[]" data-actions-box="true" multiple="true" data-width="100%"
                                data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php foreach ($payment_modes as $mode) {
                                    $selected = '';
                                    if (isset($invoice)) {
                                        if ($invoice->allowed_payment_modes) {
                                            $inv_modes = unserialize($invoice->allowed_payment_modes);
                                            if (is_array($inv_modes)) {
                                                foreach ($inv_modes as $_allowed_payment_mode) {
                                                    if ($_allowed_payment_mode == $mode['id']) {
                                                        $selected = ' selected';
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if ($mode['selected_by_default'] == 1) {
                                            $selected = ' selected';
                                        }
                                    } ?>
                                    <option value="<?php echo e($mode['id']); ?>" <?php echo e($selected); ?>>
                                        <?php echo e($mode['name']); ?></option>
                                <?php
                                } ?>
                            </select>
                        <?php } else { ?>
                            <p class="tw-text-neutral-500">
                                <?php echo _l('invoice_add_edit_no_payment_modes_found'); ?>
                            </p>
                            <a class="btn btn-primary btn-sm" href="<?php echo admin_url('paymentmodes'); ?>">
                                <?php echo _l('new_payment_mode'); ?>
                            </a>
                        <?php } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $currency_attr = ['disabled' => true, 'data-show-subtext' => true];
                            $currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');

                            foreach ($currencies as $currency) {
                                if ($currency['isdefault'] == 1) {
                                    $currency_attr['data-base'] = $currency['id'];
                                }
                                if (isset($invoice)) {
                                    if ($currency['id'] == $invoice->currency) {
                                        $selected = $currency['id'];
                                    }
                                } else {
                                    if ($currency['isdefault'] == 1) {
                                        $selected = $currency['id'];
                                    }
                                }
                            }
                            $currency_attr = hooks()->apply_filters('invoice_currency_attributes', $currency_attr);
                            ?>
                            <?php echo render_select('currency', $currencies, ['id', 'name', 'symbol'], 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                        </div>
                        <!-- <div class="col-md-6">
                                                        <?php
                                                        $selected = !isset($invoice) && get_option('automatically_set_logged_in_staff_sales_agent') == '1' ? get_staff_user_id() : '';
                                                        foreach ($staff as $member) {
                                                            if (isset($invoice)) {
                                                                if ($invoice->sale_agent == $member['staffid']) {
                                                                    $selected = $member['staffid'];
                                                                }
                                                            }
                                                        }
                                                        echo render_select('sale_agent', $staff, ['staffid', ['firstname', 'lastname']], 'sale_agent_string', $selected);
                                                        ?>
                                                    </div> -->
                        <div class="col-md-6">
                            <label for="state_name_code"
                                class="control-label"><?php echo _l('state_name_code'); ?></label>
                            <?php $value = (isset($invoice)  ? $invoice->state_name_code : ''); ?>
                            <?php echo render_input('state_name_code', '', $value, ''); ?>

                        </div>
                        <div class="col-md-6">
                            <label for="principles_place_of_business"
                                class="control-label"><?php echo _l('Principles Place of Business'); ?></label>
                            <?php $value = (isset($invoice)  ? $invoice->principles_place_of_business : ''); ?>
                            <?php echo render_input('principles_place_of_business', '', $value, ''); ?>

                        </div>
                        <!-- <div class="col-md-6">
                                                        <div class="form-group select-placeholder"
                                                        <?php if (isset($invoice) && !empty($invoice->is_recurring_from)) { ?>
                                                            data-toggle="tooltip"
                                                            data-title="<?php echo _l('create_recurring_from_child_error_message', [_l('invoice_lowercase'), _l('invoice_lowercase'), _l('invoice_lowercase')]); ?>"
                                                            <?php } ?>>
                                                            <label for="recurring" class="control-label">
                                                                <?php echo _l('invoice_add_edit_recurring'); ?>
                                                            </label>
                                                            <select class="selectpicker" data-width="100%" name="recurring"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php
                                                                                                                                        if (isset($invoice) && !empty($invoice->is_recurring_from)) {
                                                                                                                                            echo 'disabled';
                                                                                                                                        } ?>>
                                                            <?php for ($i = 0; $i <= 12; $i++) { ?>
                                                                <?php
                                                                $selected = '';
                                                                if (isset($invoice)) {
                                                                    if ($invoice->custom_recurring == 0) {
                                                                        if ($invoice->recurring == $i) {
                                                                            $selected = 'selected';
                                                                        }
                                                                    }
                                                                }
                                                                if ($i == 0) {
                                                                    $reccuring_string = _l('invoice_add_edit_recurring_no');
                                                                } elseif ($i == 1) {
                                                                    $reccuring_string = _l('invoice_add_edit_recurring_month', $i);
                                                                } else {
                                                                    $reccuring_string = _l('invoice_add_edit_recurring_months', $i);
                                                                }
                                                                ?>
                                                                <option value="<?php echo e($i); ?>" <?php echo e($selected); ?>>
                                                                    <?php echo e($reccuring_string); ?></option>
                                                                <?php } ?>
                                                                <option value="custom" <?php if (isset($invoice) && $invoice->recurring != 0 && $invoice->custom_recurring == 1) {
                                                                                            echo 'selected';
                                                                                        } ?>><?php echo _l('recurring_custom'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div> -->
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="discount_type"
                                    class="control-label"><?php echo _l('discount_type'); ?></label>
                                <select name="discount_type" class="selectpicker" data-width="100%"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="" selected><?php echo _l('no_discount'); ?></option>
                                    <option value="before_tax" <?php
                                                                if (isset($invoice)) {
                                                                    if ($invoice->discount_type == 'before_tax') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>><?php echo _l('discount_type_before_tax'); ?></option>
                                    <option value="after_tax" <?php if (isset($invoice)) {
                                                                    if ($invoice->discount_type == 'after_tax') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="recurring_custom <?php if ((isset($invoice) && $invoice->custom_recurring != 1) || (!isset($invoice))) {
                                                            echo 'hide';
                                                        } ?>">
                            <div class="col-md-6">
                                <?php $value = (isset($invoice) && $invoice->custom_recurring == 1 ? $invoice->recurring : 1); ?>
                                <?php echo render_input('repeat_every_custom', '', $value, 'number', ['min' => 1]); ?>
                            </div>
                            <div class="col-md-6">
                                <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker"
                                    data-width="100%"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="day" <?php if (isset($invoice) && $invoice->custom_recurring == 1 && $invoice->recurring_type == 'day') {
                                                            echo 'selected';
                                                        } ?>><?php echo _l('invoice_recurring_days'); ?></option>
                                    <option value="week" <?php if (isset($invoice) && $invoice->custom_recurring == 1 && $invoice->recurring_type == 'week') {
                                                                echo 'selected';
                                                            } ?>><?php echo _l('invoice_recurring_weeks'); ?></option>
                                    <option value="month" <?php if (isset($invoice) && $invoice->custom_recurring == 1 && $invoice->recurring_type == 'month') {
                                                                echo 'selected';
                                                            } ?>><?php echo _l('invoice_recurring_months'); ?></option>
                                    <option value="year" <?php if (isset($invoice) && $invoice->custom_recurring == 1 && $invoice->recurring_type == 'year') {
                                                                echo 'selected';
                                                            } ?>><?php echo _l('invoice_recurring_years'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="cycles_wrapper" class="<?php if (!isset($invoice) || (isset($invoice) && $invoice->recurring == 0)) {
                                                            echo ' hide';
                                                        } ?>">
                            <div class="col-md-12">
                                <?php $value = (isset($invoice) ? $invoice->cycles : 0); ?>
                                <div class="form-group recurring-cycles">
                                    <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
                                        <?php if (isset($invoice) && $invoice->total_cycles > 0) {
                                            echo '<small>' . e(_l('cycles_passed', $invoice->total_cycles)) . '</small>';
                                        }
                                        ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" <?php if ($value == 0) {
                                                                                        echo ' disabled';
                                                                                    } ?> name="cycles" id="cycles" value="<?php echo e($value); ?>" <?php if (isset($invoice) && $invoice->total_cycles > 0) {
                                                                                                                                                        echo 'min="' . e($invoice->total_cycles) . '"';
                                                                                                                                                    } ?>>
                                        <div class="input-group-addon">
                                            <div class="checkbox">
                                                <input type="checkbox" <?php if ($value == 0) {
                                                                            echo ' checked';
                                                                        } ?> id="unlimited_cycles">
                                                <label
                                                    for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $value = (isset($invoice) ? $invoice->adminnote : ''); ?>
                    <?php echo render_textarea('adminnote', 'invoice_add_edit_admin_note', $value); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="budget"><?php echo _l('budget'); ?></label>
                            <select name="estimate" id="estimate" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                <option value=""></option>
                                <?php foreach ($estimates as $s) { ?>
                                    <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($invoice) && $s['id'] == $invoice->estimate) {
                                                                                                        echo 'selected';
                                                                                                    } ?>><?php echo format_estimate_number($s['id']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hsn_sac" class="control-label"><?php echo _l('hsn_sac') ?></label>
                                <select name="hsn_sac" id="hsn_sac" class="selectpicker" data-live-search="true" data-width="100%">
                                    <option value=""></option>
                                    <?php foreach ($get_hsn_sac_code as $item): ?>
                                        <?php
                                        $selected = '';
                                        if (isset($invoice)) {
                                            if ($invoice->hsn_sac == $item['id']) {
                                                $selected = 'selected';
                                            }
                                        }

                                        $words = explode(' ', $item['name']);
                                        $shortName = implode(' ', array_slice($words, 0, 7));
                                        ?>
                                        <option value="<?= $item['id'] ?>" <?= $selected  ?>>
                                            <?= htmlspecialchars($shortName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php $deal_slip_no = (isset($invoice) && $invoice->deal_slip_no != '' ? $invoice->deal_slip_no : ''); ?>
                            <?php echo render_input('deal_slip_no', 'deal_slip_no', $deal_slip_no) ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr class="hr-panel-separator" />

    <div class="panel-body" style="padding-bottom: 0px !important;">
        <div class="row">
            <div class="col-md-4">
                <?php /* <?php $this->load->view('admin/invoice_items/item_select'); ?> */ ?>
            </div>
            <div class="col-md-8 text-right show_quantity_as_wrapper">
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                <li role="presentation" class="active">
                    <a href="#final_invoice" aria-controls="final_invoice" role="tab" id="tab_final_invoice" data-toggle="tab">
                        Final invoice
                    </a>
                </li>
                <li role="presentation">
                    <a href="#budget_summary" aria-controls="budget_summary" role="tab" id="tab_budget_summary" data-toggle="tab">
                        <?php echo _l('budget_summary'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#indexa" aria-controls="indexa" role="tab" id="tab_indexa" data-toggle="tab">
                        Index - A
                    </a>
                </li>

                <?php
                $annexures = get_all_annexures(); ?>
                <li role="presentation" class="dropdown">
                    <a href="#" class="dropdown-toggle" id="tab_child_items" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Annexures
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu annexture-list" aria-labelledby="tab_child_items" style="width: max-content;">
                        <?php
                        foreach ($annexures as $key => $annexure) { ?>
                            <li>
                                <a href="#<?php echo $annexure['annexure_key']; ?>" aria-controls="<?php echo $annexure['annexure_key']; ?>" role="tab" id="tab_<?php echo $annexure['annexure_key']; ?>" data-toggle="tab">
                                    <?php echo $annexure['name'] . " (" . $annexure['annexure_name'] . ")" ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="final_invoice">
                <div class="table-responsive s_table">
                    <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="25%" align="left"><?php echo _l('description_of_services'); ?></th>
                                <?php
                                $custom_fields = get_custom_fields('items');
                                foreach ($custom_fields as $cf) {
                                    echo '<th width="15%" align="left" class="custom_field">' . e($cf['name']) . '</th>';
                                }
                                $qty_heading = _l('invoice_table_quantity_heading');
                                if (isset($invoice) && $invoice->show_quantity_as == 2 || isset($hours_quantity)) {
                                    $qty_heading = _l('invoice_table_hours_heading');
                                } elseif (isset($invoice) && $invoice->show_quantity_as == 3) {
                                    $qty_heading = _l('invoice_table_quantity_heading') . '/' . _l('invoice_table_hours_heading');
                                }
                                ?>
                                <?php /* <th width="10%" align="right" class="qty"><?php echo e($qty_heading); ?></th> */ ?>
                                <th width="15%" align="right"><?php echo _l('rate_without_tax'); ?></th>
                                <th width="15%" align="right"><?php echo _l('cgst_tax'); ?></th>
                                <th width="15%" align="right"><?php echo _l('sgst_tax'); ?></th>
                                <th width="15%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>
                                <th width="15%" align="right"><?php echo _l('remarks'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="main">
                                <td></td>
                                <td align="left">
                                    <?php echo '<textarea name="final_inv_desc" class="form-control" rows="5"> ' . clear_textarea_breaks($annexure_invoice['final_invoice']['description']) . '</textarea>';
                                    ?>
                                </td>
                                <?php /* <td align="right">
                                            <?php echo $annexure_invoice['final_invoice']['qty']; ?>
                                        </td> */ ?>
                                <td align="right">
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['subtotal'], $base_currency); ?>
                                </td>
                                <td align="right">
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['cgst_tax'], $base_currency); ?>
                                </td>
                                <td align="right">
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['sgst_tax'], $base_currency); ?>
                                </td>
                                <td align="right">
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['amount'], $base_currency); ?>
                                </td>
                                <td align="right">
                                    <?php echo '<textarea name="remarks" class="form-control" rows="5"> ' . clear_textarea_breaks($annexure_invoice['final_invoice']['remarks']) . '</textarea>';
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-8 col-md-offset-4">
                    <table class="table text-right">
                        <tbody>
                            <tr id="subtotal">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('subtotal_without_tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['subtotal'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php /*
                                    <tr id="discount_area">
                                        <td>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <span class="bold tw-text-neutral-700">
                                                        <?php echo _l('invoice_discount'); ?>
                                                    </span>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="input-group" id="discount-total">
                                                        <input type="number"
                                                        value="<?php echo (isset($invoice) ? $invoice->discount_percent : 0); ?>"
                                                        class="form-control pull-left input-discount-percent<?php if (isset($invoice) && !is_sale_discount($invoice, 'percent') && is_sale_discount_applied($invoice)) {
                                                            echo ' hide';
                                                        } ?>" min="0" max="100" name="discount_percent">

                                                        <input type="number" data-toggle="tooltip"
                                                        data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>"
                                                        value="<?php echo (isset($invoice) ? $invoice->discount_total : 0); ?>"
                                                        class="form-control pull-left input-discount-fixed<?php if (!isset($invoice) || (isset($invoice) && !is_sale_discount($invoice, 'fixed'))) {
                                                            echo ' hide';
                                                        } ?>" min="0" name="discount_total">

                                                        <div class="input-group-addon">
                                                            <div class="dropdown">
                                                                <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type"
                                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                    <span class="discount-total-type-selected">
                                                                        <?php if (!isset($invoice) || isset($invoice) && (is_sale_discount($invoice, 'percent') || !is_sale_discount_applied($invoice))) {
                                                                            echo '%';
                                                                        } else {
                                                                            echo _l('discount_fixed_amount');
                                                                        }
                                                                        ?>
                                                                    </span>
                                                                    <span class="caret"></span>
                                                                </a>
                                                                <ul class="dropdown-menu" id="discount-total-type-dropdown"
                                                                aria-labelledby="dropdown_menu_tax_total_type">
                                                                    <li>
                                                                        <a href="#" class="discount-total-type discount-type-percent<?php if (!isset($invoice) || (isset($invoice) && is_sale_discount($invoice, 'percent')) || (isset($invoice) && !is_sale_discount_applied($invoice))) {
                                                                            echo ' selected';
                                                                        } ?>">%</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" class="discount-total-type discount-type-fixed<?php if (isset($invoice) && is_sale_discount($invoice, 'fixed')) {
                                                                        echo ' selected';} ?>"><?php echo _l('discount_fixed_amount'); ?>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="discount-total"></td>
                                    </tr>
                                    */ ?>
                            <tr id="total_tax">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('cgst_tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['cgst_tax'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr id="total_tax">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('sgst_tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['sgst_tax'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php /*
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <span
                                                    class="bold tw-text-neutral-700"><?php echo _l('invoice_adjustment'); ?></span>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" data-toggle="tooltip"
                                                    data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if (isset($invoice)) {
                                                        echo $invoice->adjustment;
                                                    } else {
                                                        echo 0;
                                                    } ?>" class="form-control pull-left" name="adjustment">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="adjustment"></td>
                                    </tr>
                                    */ ?>
                            <tr>
                                <td><span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['amount'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="indexa">
                <div class="table-responsive s_table">
                    <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1"
                                        aria-hidden="true" data-toggle="tooltip"
                                        data-title="<?php echo _l('budget_head'); ?>"></i>
                                    <?php echo _l('budget_head'); ?></th>
                                <th width="25%" align="left"><?php echo _l('invoice_table_item_description'); ?></th>
                                <?php
                                $custom_fields = get_custom_fields('items');
                                foreach ($custom_fields as $cf) {
                                    echo '<th width="15%" align="left" class="custom_field">' . e($cf['name']) . '</th>';
                                }
                                $qty_heading = _l('invoice_table_quantity_heading');
                                if (isset($invoice) && $invoice->show_quantity_as == 2 || isset($hours_quantity)) {
                                    $qty_heading = _l('invoice_table_hours_heading');
                                } elseif (isset($invoice) && $invoice->show_quantity_as == 3) {
                                    $qty_heading = _l('invoice_table_quantity_heading') . '/' . _l('invoice_table_hours_heading');
                                }
                                ?>
                                <?php /*<th width="10%" align="right" class="qty"><?php echo e($qty_heading); ?></th> */ ?>
                                <th width="15%" align="right"><?php echo _l('rate_without_tax'); ?></th>
                                <th width="20%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                                <th width="20%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($annexure_invoice['indexa'])) {
                                $indexa = $annexure_invoice['indexa'];
                                foreach ($indexa as $ikey => $ivalue) { ?>
                                    <tr class="main">
                                        <td></td>
                                        <td align="left">
                                            <?php echo $ivalue['name']; ?>
                                        </td>
                                        <td align="left">
                                            <?php echo $ivalue['description']; ?>
                                        </td>
                                        <?php /* <td align="right">
                                                    <?php echo $ivalue['qty']; ?>
                                                </td> */ ?>
                                        <td align="right">
                                            <?php echo app_format_money($ivalue['subtotal'], $base_currency); ?>
                                        </td>
                                        <td align="right">
                                            <?php echo app_format_money($ivalue['tax'], $base_currency); ?>
                                        </td>
                                        <td align="right">
                                            <?php echo app_format_money($ivalue['amount'], $base_currency); ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-8 col-md-offset-4">
                    <table class="table text-right">
                        <tbody>
                            <tr id="subtotal">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('subtotal_without_management_fees_and_tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['total_without_man_fees'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr id="subtotal">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('grand_subtotal_without_tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['subtotal'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr id="total_tax" class="hide">
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('tax'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['tax'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr class="hide">
                                <td><span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['final_invoice']['amount'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="budget_summary">
                <div class="table-responsive s_table">
                    <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                        <thead>
                            <tr>
                                <th width="20%" align="left"><?php echo _l('budget_head'); ?></th>
                                <th width="16%" align="right"><?php echo _l('budgeted_amount'); ?></th>
                                <th width="16%" align="right"><?php echo _l('total_previous_billing'); ?></th>
                                <th width="16%" align="right"><?php echo _l('total_current_billing_amount'); ?></th>
                                <th width="16%" align="right"><?php echo _l('total_cumulative_billing'); ?></th>
                                <th width="16%" align="right"><?php echo _l('balance_available'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($annexure_invoice['budgetsummary'])) {
                                $budgetsummary = $annexure_invoice['budgetsummary'];
                                foreach ($budgetsummary as $bkey => $bvalue) { ?>
                                    <tr class="main">
                                        <td align="left">
                                            <?php echo $bvalue['name']; ?>
                                        </td>
                                        <td align="right">
                                            <span class="budgeted-amount-display" data-invoice="<?php echo $bvalue['invoiceid']; ?>" data-annexure="<?php echo $bvalue['annexure']; ?>" data-id="<?php echo $bkey + 1; ?>">
                                                <?php echo app_format_money($bvalue['budgeted_amount'], $base_currency); ?>
                                            </span>
                                        </td>
                                        <td align="right">
                                            <span class="total-previous-billing-display" data-invoice="<?php echo $bvalue['invoiceid']; ?>" data-annexure="<?php echo $bvalue['annexure']; ?>" data-id="<?php echo $bkey + 1; ?>">
                                                <?php echo app_format_money($bvalue['total_previous_billing'], $base_currency); ?>
                                            </span>
                                        </td>
                                        <td align="right">
                                            <span class="total-current-billing-amount-display" data-id="<?php echo $bkey + 1; ?>">
                                                <?php echo app_format_money($bvalue['total_current_billing_amount'], $base_currency); ?>
                                            </span>
                                        </td>
                                        <td align="right">
                                            <span class="total-cumulative-billing-display" data-id="<?php echo $bkey + 1; ?>">
                                                <?php echo app_format_money($bvalue['total_cumulative_billing'], $base_currency); ?>
                                            </span>
                                        </td>
                                        <td align="right">
                                            <span class="balance-available-display" data-id="<?php echo $bkey + 1; ?>">
                                                <?php echo app_format_money($bvalue['balance_available'], $base_currency); ?>
                                            </span>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-8 col-md-offset-4">
                    <table class="table text-right">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('total') . ' ' . _l('budgeted_amount'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['total_budget_summary']['budgeted_amount'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('total_previous_billing'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['total_budget_summary']['total_previous_billing'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('total_current_billing_amount'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['total_budget_summary']['total_current_billing_amount'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('total_cumulative_billing'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['total_budget_summary']['total_cumulative_billing'], $base_currency); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="bold tw-text-neutral-700"><?php echo _l('total') . ' ' . _l('balance_available'); ?> :</span>
                                </td>
                                <td>
                                    <?php echo app_format_money($annexure_invoice['total_budget_summary']['balance_available'], $base_currency); ?>
                                </td>
                            </tr>
                            <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            $annexures = get_all_annexures();
            $i = 1;
            foreach ($annexures as $key => $annexure) { ?>
                <div role="tabpanel" class="tab-pane" id="<?php echo $annexure['annexure_key']; ?>">
                    <div class="table-responsive s_table">
                        <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th width="13%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1"
                                            aria-hidden="true" data-toggle="tooltip"
                                            data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i>
                                        <?php echo _l('budget_head'); ?></th>
                                    <th width="15%" align="left"><?php echo _l('description_of_services'); ?></th>
                                    <?php
                                    $custom_fields = get_custom_fields('items');
                                    foreach ($custom_fields as $cf) {
                                        echo '<th width="15%" align="left" class="custom_field">' . e($cf['name']) . '</th>';
                                    }
                                    $qty_heading = _l('invoice_table_quantity_heading');
                                    if (isset($invoice) && $invoice->show_quantity_as == 2 || isset($hours_quantity)) {
                                        $qty_heading = _l('invoice_table_hours_heading');
                                    } elseif (isset($invoice) && $invoice->show_quantity_as == 3) {
                                        $qty_heading = _l('invoice_table_quantity_heading') . '/' . _l('invoice_table_hours_heading');
                                    }
                                    ?>
                                    <th width="13%" align="left"><?php echo _l('vendor'); ?></th>
                                    <th width="13%" align="left"><?php echo _l('invoice_no'); ?></th>
                                    <?php /* <th width="5%" align="right" class="qty"><?php echo e($qty_heading); ?></th> */ ?>
                                    <th width="13%" align="right"><?php echo _l('rate_without_tax'); ?></th>
                                    <th width="10%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                                    <th width="13%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>
                                    <th width="13%" align="right"><?php echo _l('remarks'); ?></th>
                                    <th align="center"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($invoice) || isset($add_items)) {
                                    $items_indicator = 'newitems';
                                    if (isset($invoice)) {
                                        $add_items       = $invoice->items;
                                        $items_indicator = 'items';
                                    }
                                    foreach ($add_items as $item) {
                                        if ($item['annexure'] == $annexure['id']) {
                                            $manual    = false;
                                            $vendor_name = '';
                                            $invoice_no = '';
                                            if (!empty($item['po_id'])) {
                                                $pur_orders = get_pur_orders($item['po_id']);
                                                $vendor = get_vendor_details($pur_orders->vendor);
                                                $vendor_name = $vendor->company;
                                                $invoice_no = $pur_orders->pur_order_number;
                                            }
                                            if (!empty($item['wo_id'])) {
                                                $wo_orders = get_wo_orders($item['wo_id']);
                                                $vendor = get_vendor_details($wo_orders->vendor);
                                                $vendor_name = $vendor->company;
                                                $invoice_no = $wo_orders->wo_order_number;
                                            }
                                            if (!empty($item['vbt_id'])) {
                                                $pur_invoices = get_pur_invoices($item['vbt_id']);
                                                $vendor = get_vendor_details($pur_invoices->vendor);
                                                $vendor_name = $vendor->company;
                                                $invoice_no = $pur_invoices->vendor_invoice_number;
                                            }
                                            $table_row = '<tr class="sortable item">';
                                            $table_row .= '<td class="dragger">';
                                            if (!is_numeric($item['qty'])) {
                                                $item['qty'] = 1;
                                            }
                                            $invoice_item_taxes = get_invoice_item_taxes($item['id']);
                                            if ($item['id'] == 0) {
                                                $invoice_item_taxes = $item['taxname'];
                                                $manual             = true;
                                            }
                                            $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                                            $amount = $item['rate'] * $item['qty'];
                                            $amount = app_format_number($amount);
                                            $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                                            $table_row .= '</td>';
                                            $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5" disabled>' . clear_textarea_breaks($item['description']) . '</textarea></td>';
                                            $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';

                                            $table_row .= render_custom_fields_items_table_in($item, $items_indicator . '[' . $i . ']');

                                            $table_row .= '<td>' . $vendor_name . '</td>';
                                            $table_row .= '<td>' . $invoice_no . '</td>';

                                            $table_row .= '<td class="hide"><input type="hidden" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control" disabled>';

                                            $unit_placeholder = '';
                                            if (!$item['unit']) {
                                                $unit_placeholder = _l('unit');
                                                $item['unit']     = '';
                                            }

                                            $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item['unit'] . '">';

                                            $table_row .= '</td>';
                                            $table_row .= '<td class="rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control" disabled></td>';
                                            $table_row .= '<td class="taxrate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][taxname]" value="' . $item['tax'] . '" class="form-control" disabled></td>';
                                            // $table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item['id'], true, $manual, true) . '</td>';
                                            $table_row .= '<td class="amount" align="right">' . $amount . '</td>';
                                            $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][remarks]" class="form-control" rows="5">' . clear_textarea_breaks($item['remarks']) . '</textarea></td>';
                                            $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                                            if (isset($item['task_id'])) {
                                                if (!is_array($item['task_id'])) {
                                                    $table_row .= form_hidden('billed_tasks[' . $i . '][]', $item['task_id']);
                                                } else {
                                                    foreach ($item['task_id'] as $task_id) {
                                                        $table_row .= form_hidden('billed_tasks[' . $i . '][]', $task_id);
                                                    }
                                                }
                                            } elseif (isset($item['expense_id'])) {
                                                $table_row .= form_hidden('billed_expenses[' . $i . '][]', $item['expense_id']);
                                            }
                                            $table_row .= '</tr>';
                                            echo $table_row;
                                            $i++;
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8 col-md-offset-4">
                        <table class="table text-right">
                            <tbody>
                                <tr id="subtotal">
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('invoice_subtotal'); ?> :</span>
                                    </td>
                                    <td class="annexture_subtotal">
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('tax'); ?> :</span>
                                    </td>
                                    <td class="annexture_tax">
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?> :</span>
                                    </td>
                                    <td class="annexture_total">
                                    </td>
                                </tr>
                                <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="removed-items"></div>
                </div>
            <?php } ?>

        </div>
    </div>

    <hr class="hr-panel-separator" />

    <div class="panel-body">
        <?php $value = (isset($invoice) ? $invoice->clientnote : get_option('predefined_clientnote_invoice')); ?>
        <?php echo render_textarea('clientnote', 'invoice_add_edit_client_note', $value); ?>
        <?php $value = (isset($invoice) ? $invoice->terms : get_option('predefined_terms_invoice')); ?>
        <?php echo render_textarea('terms', 'terms_and_conditions', $value, [], [], 'mtop15'); ?>
    </div>

    <?php hooks()->do_action('after_render_invoice_template', isset($invoice) ? $invoice : false); ?>
</div>

<div class="btn-bottom-pusher"></div>
<div class="btn-bottom-toolbar text-right">
    <button class="btn-tr btn btn-default mright5 text-right invoice-form-submit save-as-draft transaction-submit">
        <?php echo _l('save_as_draft'); ?>
    </button>
    <div class="btn-group dropup">
        <button type="button"
            class="btn-tr btn btn-primary invoice-form-submit transaction-submit"><?php echo _l('submit'); ?></button>
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right width200">
            <li>
                <a href="#" class="invoice-form-submit save-and-send transaction-submit">
                    <?php echo _l('save_and_send'); ?>
                </a>
            </li>
            <?php if (!isset($invoice)) { ?>
                <li>
                    <a href="#" class="invoice-form-submit save-and-send-later transaction-submit">
                        <?php echo _l('save_and_send_later'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="invoice-form-submit save-and-record-payment transaction-submit">
                        <?php echo _l('save_and_record_payment'); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>