<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style type="text/css">
  .pc-table .table {
    width: 100%;
    border-collapse: collapse;
  }

  .pc-table .table th {
    text-align: left;
    font-weight: bold !important;
  }

  .pc-table .table th,
  .pc-table .table td {
    border: 1px solid black !important;
    padding: 8px;
    text-align: left;
    color: black !important;
  }

  .pc-table .table thead,
  .pc-table .table_head {
    background-color: #f2f2f2;
    font-weight: bold;
  }

  .payment_certificate_body .form-group {
    margin-bottom: 0px !important;
  }

  .works_executed_on_a_class .bootstrap-select {
    width: 100px !important;
  }

  .labour_cess_class .bootstrap-select {
    width: 100px !important;
  }

  .mobilization_advance_class {
    white-space: nowrap;
  }

  .mobilization_advance_class .form-group {
    display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
  }

  .mobilization_advance_class input[name="mobilization_text"].form-control {
    width: 263px !important;
    display: inline-block !important;
  }

  .mobilization_advance_class input[name="payment_clause"].form-control {
    width: 120px !important;
    display: inline-block !important;
  }

  .cgst_tax_class .bootstrap-select,
  .sgst_tax_class .bootstrap-select {
    width: 100px !important;
  }

  .igst_tax_class .bootstrap-select {
    width: 100px !important;
  }

  .draggerer {
    width: 30px;
    cursor: move;
    position: relative;
  }
  .draggerer::after {
    content: "☰";
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.4;
  }
  .draggerer:hover::after {
    opacity: 0.8;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php
      echo form_open_multipart($this->uri->uri_string(), array('id' => 'payment_certificate_form', 'class' => '_payment_transaction_form'));
      if (isset($payment_certificate)) {
        echo form_hidden('isedit');
      }
      ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
          <div class="panel-body">
            <div class="row">
              <div class="horizontal-scrollable-tabs preview-tabs-top">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="<?php if ($this->input->get('tab') != 'attachment') {
                                                      echo 'active';
                                                    } ?>">
                      <a href="#information" aria-controls="information" role="tab" data-toggle="tab">
                        <?php echo _l('pur_information'); ?>
                      </a>
                    </li>
                    <li role="presentation">
                      <a href="#tab_documentation" aria-controls="tab_documentation" role="tab" data-toggle="tab">
                        Documentation
                      </a>
                    </li>
                    <?php
                    if (isset($payment_certificate)) { ?>
                      <li role="presentation">
                        <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($payment_certificate->id); ?>, 'payment_certificate'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                          <?php echo _l('tasks'); ?>
                        </a>
                      </li>
                    <?php } ?>
                    <li role="presentation">
                      <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                        <?php echo _l('invoice_view_activity_tooltip'); ?>
                      </a>
                    </li>
                    <?php
                    if (isset($payment_certificate)) { ?>
                      <li role="presentation">
                         <a href="#tab_notes" onclick="get_pc_notes(<?php echo pur_html_entity_decode($payment_certificate->id); ?>,'wo_payment_certificate'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                            <?php echo _l('estimate_notes'); ?>
                            <span class="notes-total">
                               <?php $totalNotes = total_rows(db_prefix() . 'notes', ['rel_id' => $estimate->id, 'rel_type' => 'purchase_order']);
                               if ($totalNotes > 0) { ?>
                                  <span class="badge"><?php echo ($totalNotes); ?></span>
                               <?php } ?>
                            </span>
                         </a>
                      </li>
                      <li role="presentation">
                       <?php
                       $totalComments = total_rows(db_prefix() . 'payment_certificate_comments', ['rel_id' => $payment_certificate->id, 'rel_type' => 'wo_payment_certificate']);
                       ?>
                       <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                          <?php echo _l('pur_discuss'); ?>
                          <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                       </a>
                      </li>
                    <?php } ?>
                  </ul>
                </div>
              </div>
            </div>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane ptop10 active" id="information">
                <div class="panel-body">
                  <div class="row">
                    <?php echo form_hidden('wo_id', $wo_id); ?>
                    <?php echo form_hidden('payment_certificate_id', $payment_certificate_id); ?>
                    <div class="col-md-3">
                      <?php $serial_no = (isset($payment_certificate) ? $payment_certificate->serial_no : get_payment_certificate_serial_no());
                      echo render_input('serial_no', 'payment_certificate_no', $serial_no, 'text', ['readonly' => true]); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $pc_number = (isset($payment_certificate) ? $payment_certificate->pc_number : '');
                      echo render_input('pc_number', 'Payment certificate number', $pc_number, 'text', ['readonly' => true]); ?>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="type"><?php echo _l('type'); ?></label>
                        <select name="pay_cert_options" id="pay_cert_options" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="interim" <?php if (isset($payment_certificate) && $payment_certificate->pay_cert_options == 'interim') {
                                                    echo 'selected';
                                                  } ?>><?php echo _l('option_interim'); ?></option>
                          <option value="ad_hoc" <?php if (isset($payment_certificate) && $payment_certificate->pay_cert_options == 'ad_hoc') {
                                                    echo 'selected';
                                                  } ?>><?php echo _l('option_ad_hoc'); ?></option>
                          <option value="final" <?php if (isset($payment_certificate) && $payment_certificate->pay_cert_options == 'final') {
                                                  echo 'selected';
                                                } ?>><?php echo _l('option_final'); ?></option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <?php $vendor_name = get_vendor_company_name($wo_order->vendor);
                      echo render_input('vendor', 'vendor', $vendor_name, 'text', ['disabled' => 'disabled']); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $po_no = $wo_order->wo_order_number;
                      echo render_input('po_no', 'wo_no', $po_no, 'text', ['disabled' => 'disabled']); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $po_date = _d($wo_order->order_date);
                      echo render_date_input('po_date', 'wo_date', $po_date, ['disabled' => 'disabled']); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $po_description = $wo_order->wo_order_name;
                      echo render_input('po_description', 'wo_description', $po_description, 'text', ['disabled' => 'disabled']); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $project = get_project_name_by_id($wo_order->project);
                      echo render_input('project', 'project', $project, 'text', ['disabled' => 'disabled']); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $location = (isset($payment_certificate) ? $payment_certificate->location : '');
                      echo render_input('location', 'Location', $location); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $invoice_ref = (isset($payment_certificate) ? $payment_certificate->invoice_ref : '');
                      echo render_input('invoice_ref', 'invoice_ref', $invoice_ref); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $bill_period_upto = (isset($payment_certificate) ? _d($payment_certificate->bill_period_upto) : '');
                      echo render_date_input('bill_period_upto', 'bill_period_upto', $bill_period_upto); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $bill_received_on = (isset($payment_certificate) ? _d($payment_certificate->bill_received_on) : _d(date('Y-m-d')));
                      echo render_date_input('bill_received_on', 'bill_received_on', $bill_received_on); ?>
                    </div>
                    <div class="col-md-3">
                      <?php $invoice_date = (isset($payment_certificate) ? _d($payment_certificate->invoice_date) : _d(date('Y-m-d')));
                      echo render_date_input('invoice_date', 'invoice_date', $invoice_date); ?>
                    </div>
                    <?php if (!empty($payment_certificate_id) && is_admin()) { ?>
                      <div class="col-md-3 form-group">
                        <label for="pur_change_status_to"><?php echo _l('pur_change_status_to'); ?></label>
                        <select name="status" id="status" class="selectpicker" onchange="change_status_pay_cert(this,<?php echo ($payment_certificate_id); ?>); return false;" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="1"><?php echo _l('purchase_draft'); ?></option>
                          <option value="4"><?php echo _l('approval_request_sent'); ?></option>
                          <option value="2"><?php echo _l('purchase_approved'); ?></option>
                          <option value="3"><?php echo _l('pur_rejected'); ?></option>
                        </select>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <div class="col-md-10 pull-right" style="z-index: 999;display: flex;justify-content: end;">

                  <span style="margin-right: 10px;">
                    <button class="btn btn-primary" id="settings-toggle-payment-certificate">Columns</button>
                    <div id="settings-dropdown-payment-certificate" style="display: none; position: absolute; background: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204); padding: 10px;width:130px;">

                      <label><input type="checkbox" class="column-toggle" data-column="1" checked=""> <?php echo _l('decription'); ?></label><br>
                      <label><input type="checkbox" class="column-toggle" data-column="2" checked=""> <?php echo _l('contract_amount'); ?></label><br>
                      <label><input type="checkbox" class="column-toggle" data-column="3" checked=""> <?php echo _l('previous'); ?></label><br>
                      <label><input type="checkbox" class="column-toggle" data-column="4" checked=""> <?php echo _l('this_bill'); ?></label><br>
                      <label><input type="checkbox" class="column-toggle" data-column="5" checked=""> <?php echo _l('comulative'); ?></label><br>
                       <label><input type="checkbox" class="column-toggle" data-column="6" checked=""> <?php echo _l('remarks'); ?></label><br>
                    </div>
                  </span>
                  <span style="padding: 0px;">
                    <button id="export-csv" class="btn btn-primary  pull-right">Export to CSV</button>
                  </span>
                </div>
                <div class="panel-body mtop15">
                  <div class="row">
                    <div class="col-md-12 pc-table">
                      <div class="table-responsive">
                        <table class="table wo-payment-certificate-table items no-mtop">
                          <thead>
                            <tr>
                              <th width="5%"><?php echo _l('serial_no'); ?></th>
                              <th width="30%"><?php echo _l('decription'); ?></th>
                              <th width="13%"><?php echo _l('contract_amount'); ?></th>
                              <th width="13%"><?php echo _l('previous'); ?></th>
                              <th width="13%"><?php echo _l('this_bill'); ?></th>
                              <th width="13%"><?php echo _l('comulative'); ?></th>
                              <th width="20%"><?php echo _l('remarks'); ?></th>
                            </tr>
                          </thead>
                          <tbody class="payment_certificate_body">
                            <tr>
                              <td>A1</td>
                              <td class="po_name"></td>
                              <td class="wo_contract_amount"></td>
                              <td>
                                <?php
                                $po_previous = (isset($payment_certificate) ? format_amount_cert($payment_certificate->po_previous) : '');
                                echo render_input('po_previous', '', $po_previous, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $po_this_bill = (isset($payment_certificate) ? format_amount_cert($payment_certificate->po_this_bill) : '');
                                echo render_input('po_this_bill', '', $po_this_bill, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="po_comulative"></td>
                              <td>
                                <?php
                                $a1_remarks = (isset($payment_certificate) ? $payment_certificate->a1_remarks : '');
                                echo render_textarea('a1_remarks', '', $a1_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>A</td>
                              <td><?php echo _l('total_value_of_works_executed'); ?></td>
                              <td class="wo_contract_amount"></td>
                              <td class="total_po_previous"></td>
                              <td class="total_po_this_bill"></td>
                              <td class="po_comulative"></td>
                              <td>
                                <?php
                                $a_remarks = (isset($payment_certificate) ? $payment_certificate->a_remarks : '');
                                echo render_textarea('a_remarks', '', $a_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>B</td>
                              <td><?php echo _l('pay_cert_b_title'); ?></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                            <tr>
                              <td>C1</td>
                              <td class="mobilization_advance_class">
                                Advance
                                <?php
                                if (isset($payment_certificate) && !empty($payment_certificate->mobilization_text)) {
                                  $mobilization_text = $payment_certificate->mobilization_text;
                                } else {
                                  $mobilization_text = 'Mobilization payment as per clause';
                                }
                                echo render_input('mobilization_text', '', $mobilization_text);
                                ?>
                                <?php
                                $payment_clause = (isset($payment_certificate) ? $payment_certificate->payment_clause  : '14.2');
                                echo render_input('payment_clause', '', $payment_clause, 'number');
                                ?>
                              </td>
                              <td>
                                <?php
                                $pay_cert_c1_1 = (isset($payment_certificate) ? $payment_certificate->pay_cert_c1_1 : '');
                                echo render_input('pay_cert_c1_1', '', $pay_cert_c1_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $pay_cert_c1_2 = (isset($payment_certificate) ? $payment_certificate->pay_cert_c1_2 : '');
                                echo render_input('pay_cert_c1_2', '', $pay_cert_c1_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $pay_cert_c1_3 = (isset($payment_certificate) ? $payment_certificate->pay_cert_c1_3 : '');
                                echo render_input('pay_cert_c1_3', '', $pay_cert_c1_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="pay_cert_c1_4"></td>
                              <td>
                                <?php
                                $c1_remarks = (isset($payment_certificate) ? $payment_certificate->c1_remarks : '');
                                echo render_textarea('c1_remarks', '', $c1_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>C2</td>
                              <td><?php echo _l('pay_cert_c2_title'); ?></td>
                              <td>
                                <?php
                                $pay_cert_c2_1 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->pay_cert_c2_1) : '');
                                echo render_input('pay_cert_c2_1', '', $pay_cert_c2_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $pay_cert_c2_2 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->pay_cert_c2_2) : '');
                                echo render_input('pay_cert_c2_2', '', $pay_cert_c2_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $pay_cert_c2_3 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->pay_cert_c2_3) : '');
                                echo render_input('pay_cert_c2_3', '', $pay_cert_c2_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="pay_cert_c2_4"></td>
                              <td>
                                <?php
                                $c2_remarks = (isset($payment_certificate) ? $payment_certificate->c2_remarks : '');
                                echo render_textarea('c2_remarks', '', $c2_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>C</td>
                              <td><?php echo _l('net_advance'); ?></td>
                              <td class="net_advance_1"></td>
                              <td class="net_advance_2"></td>
                              <td class="net_advance_3"></td>
                              <td class="net_advance_4"></td>
                              <td></td>
                            </tr>
                            <tr class="table_head">
                              <td>D</td>
                              <td><?php echo _l('sub_total_ac'); ?></td>
                              <td class="sub_total_ac_1"></td>
                              <td class="sub_total_ac_2"></td>
                              <td class="sub_total_ac_3"></td>
                              <td class="sub_total_ac_4"></td>
                              <td>
                                <?php
                                $d_remarks = (isset($payment_certificate) ? $payment_certificate->d_remarks : '');
                                echo render_textarea('d_remarks', '', $d_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>E1</td>
                              <td><?php echo _l('retention_fund'); ?></td>
                              <td>
                                <?php
                                $ret_fund_1 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->ret_fund_1) : '');
                                echo render_input('ret_fund_1', '', $ret_fund_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $ret_fund_2 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->ret_fund_2) : '');
                                echo render_input('ret_fund_2', '', $ret_fund_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $ret_fund_3 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->ret_fund_3) : '');
                                echo render_input('ret_fund_3', '', $ret_fund_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="ret_fund_4"></td>
                              <td>
                                <?php
                                $e1_remarks = (isset($payment_certificate) ? $payment_certificate->e1_remarks : '');
                                echo render_textarea('e1_remarks', '', $e1_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>E2</td>
                              <td class="works_executed_on_a_class">
                                <?php echo _l('works_executed_5_of_A'); ?>
                                <select name="works_executed_on_a" id="works_executed_on_a" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" onchange="calculate_payment_certificate()">
                                  <option value="0%" <?php if (isset($payment_certificate) && $payment_certificate->works_executed_on_a == '0%') {
                                                        echo 'selected';
                                                      } ?>>0%</option>
                                  <option value="5%" <?php if (isset($payment_certificate) && $payment_certificate->works_executed_on_a == '5%') {
                                                        echo 'selected';
                                                      } ?>>5%</option>
                                  <option value="10%" <?php if (isset($payment_certificate) && $payment_certificate->works_executed_on_a == '10%') {
                                                        echo 'selected';
                                                      } ?>>10%</option>
                                </select>
                              </td>
                              <td>
                                <?php
                                $works_exe_a_1 = (isset($payment_certificate) ? $payment_certificate->works_exe_a_1 : '');
                                echo render_input('works_exe_a_1', '', $works_exe_a_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $works_exe_a_2 = (isset($payment_certificate) ? $payment_certificate->works_exe_a_2 : '');
                                echo render_input('works_exe_a_2', '', $works_exe_a_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $works_exe_a_3 = (isset($payment_certificate) ? $payment_certificate->works_exe_a_3 : '');
                                echo render_input('works_exe_a_3', '', $works_exe_a_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="works_exe_a_4"></td>
                              <td>
                                <?php
                                $e2_remarks = (isset($payment_certificate) ? $payment_certificate->e2_remarks : '');
                                echo render_textarea('e2_remarks', '', $e2_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>E</td>
                              <td><?php echo _l('less_total_retention'); ?></td>
                              <td class="less_ret_1"></td>
                              <td class="less_ret_2"></td>
                              <td class="less_ret_3"></td>
                              <td class="less_ret_4"></td>
                              <td></td>
                            </tr>
                            <tr class="table_head">
                              <td>F</td>
                              <td><?php echo _l('sub_total_de'); ?></td>
                              <td class="sub_t_de_1"></td>
                              <td class="sub_t_de_2"></td>
                              <td class="sub_t_de_3"></td>
                              <td class="sub_t_de_4"></td>
                              <td>
                                <?php
                                $f_remarks = (isset($payment_certificate) ? $payment_certificate->f_remarks : '');
                                echo render_textarea('f_remarks', '', $f_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>G1</td>
                              <td><?php echo _l('less_title'); ?></td>
                              <td>
                                <?php
                                $less_1 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_1) : '');
                                echo render_input('less_1', '', $less_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $less_2 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_2) : '');
                                echo render_input('less_2', '', $less_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $less_3 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_3) : '');
                                echo render_input('less_3', '', $less_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="less_4"></td>
                               <td>
                                <?php
                                $g1_remarks = (isset($payment_certificate) ? $payment_certificate->g1_remarks : '');
                                echo render_textarea('g1_remarks', '', $g1_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>G2</td>
                              <td><?php echo _l('less_amount_hold_for_quality_ncr'); ?></td>
                              <td>
                                <?php
                                $less_ah_1 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_ah_1) : '');
                                echo render_input('less_ah_1', '', $less_ah_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $less_ah_2 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_ah_2) : '');
                                echo render_input('less_ah_2', '', $less_ah_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $less_ah_3 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_ah_3) : '');
                                echo render_input('less_ah_3', '', $less_ah_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="less_ah_4"></td>
                              <td>
                                <?php
                                $g2_remarks = (isset($payment_certificate) ? $payment_certificate->g2_remarks : '');
                                echo render_textarea('g2_remarks', '', $g2_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>G3</td>
                              <td><?php echo _l('less_amount_hold_for_testing_and_comissioning'); ?></td>
                              <td>
                                <?php
                                $less_aht_1 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_aht_1) : '');
                                echo render_input('less_aht_1', '', $less_aht_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $less_aht_2 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_aht_2) : '');
                                echo render_input('less_aht_2', '', $less_aht_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $less_aht_3 = (isset($payment_certificate) ? format_amount_cert($payment_certificate->less_aht_3) : '');
                                echo render_input('less_aht_3', '', $less_aht_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="less_aht_4"></td>
                              <td>
                                <?php
                                $g3_remarks = (isset($payment_certificate) ? $payment_certificate->g3_remarks : '');
                                echo render_textarea('g3_remarks', '', $g3_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>G</td>
                              <td><?php echo _l('less_deductions'); ?></td>
                              <td class="less_ded_1"></td>
                              <td class="less_ded_2"></td>
                              <td class="less_ded_3"></td>
                              <td class="less_ded_4"></td>
                              <td></td>
                            </tr>
                            <tr class="table_head">
                              <td>H</td>
                              <td><?php echo _l('sub_total_exclusive_of_taxes'); ?></td>
                              <td class="sub_fg_1"></td>
                              <td class="sub_fg_2"></td>
                              <td class="sub_fg_3"></td>
                              <td class="sub_fg_4"></td>
                              <td>
                                <?php
                                $h_remarks = (isset($payment_certificate) ? $payment_certificate->h_remarks : '');
                                echo render_textarea('h_remarks', '', $h_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>I1</td>
                              <td class="cgst_tax_class">
                                CGST @
                                <select name="cgst_tax" id="cgst_tax" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" onchange="calculate_payment_certificate()">
                                  <?php
                                  $taxes = get_taxes_list();
                                  if (!empty($taxes)) {
                                    foreach ($taxes as $key => $value) { ?>
                                      <option value="<?php echo $value['name']; ?>" <?php if (isset($payment_certificate) && $payment_certificate->cgst_tax == $value['name']) {
                                                                                      echo 'selected';
                                                                                    } ?>><?php echo $value['name']; ?></option>
                                  <?php }
                                  } ?>
                                </select>
                                on A
                              </td>
                              <td class="cgst_on_a1"></td>
                              <td class="cgst_on_a2">
                                <?php
                                $cgst_prev_bill = (isset($payment_certificate) ? $payment_certificate->cgst_prev_bill : '');
                                echo render_input('cgst_prev_bill', '', $cgst_prev_bill, 'number', ['oninput' => "calculate_payment_certificate()"], [], '', 'text-right');
                                ?>
                              </td>
                              <td class="cgst_on_a3">
                                <?php
                                $cgst_this_bill = (isset($payment_certificate) ? $payment_certificate->cgst_this_bill : '');
                                echo render_input('cgst_this_bill', '', $cgst_this_bill, 'number', ['oninput' => "calculate_payment_certificate()"], [], '', 'text-right');
                                ?>
                              </td>
                              <td class="cgst_on_a4"></td>
                              <td>
                                <?php
                                $i1_remarks = (isset($payment_certificate) ? $payment_certificate->i1_remarks : '');
                                echo render_textarea('i1_remarks', '', $i1_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>I2</td>
                              <td class="sgst_tax_class">
                                SGST @
                                <select name="sgst_tax" id="sgst_tax" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" onchange="calculate_payment_certificate()">
                                  <?php
                                  $taxes = get_taxes_list();
                                  if (!empty($taxes)) {
                                    foreach ($taxes as $key => $value) { ?>
                                      <option value="<?php echo $value['name']; ?>" <?php if (isset($payment_certificate) && $payment_certificate->sgst_tax == $value['name']) {
                                                                                      echo 'selected';
                                                                                    } ?>><?php echo $value['name']; ?></option>
                                  <?php }
                                  } ?>
                                </select>
                                on A
                              </td>
                              <td class="sgst_on_a1"></td>
                              <td class="sgst_on_a2">
                                <?php
                                $sgst_prev_bill = (isset($payment_certificate) ? $payment_certificate->sgst_prev_bill : '');
                                echo render_input('sgst_prev_bill', '', $sgst_prev_bill, 'number', ['oninput' => "calculate_payment_certificate()"], [], '', 'text-right');
                                ?>
                              </td>
                              <td class="sgst_on_a3">
                                <?php
                                $sgst_this_bill = (isset($payment_certificate) ? $payment_certificate->sgst_this_bill : '');
                                echo render_input('sgst_this_bill', '', $sgst_this_bill, 'number', ['oninput' => "calculate_payment_certificate()"], [], '', 'text-right');
                                ?>
                              </td>
                              <td class="sgst_on_a4"></td>
                              <td>
                                <?php
                                $i2_remarks = (isset($payment_certificate) ? $payment_certificate->i2_remarks : '');
                                echo render_textarea('i2_remarks', '', $i2_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                              <td>I3</td>
                              <td class="igst_tax_class">
                                IGST @
                                <select name="igst_tax" id="igst_tax" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" onchange="calculate_payment_certificate()">
                                  <?php
                                  $taxes = get_taxes_list();
                                  if (!empty($taxes)) {
                                    foreach ($taxes as $key => $value) { ?>
                                      <option value="<?php echo $value['name']; ?>" <?php if (isset($payment_certificate) && $payment_certificate->igst_tax == $value['name']) {
                                                                                      echo 'selected';
                                                                                    } ?>><?php echo $value['name']; ?></option>
                                  <?php }
                                  } ?>
                                </select>
                                on A
                              </td>
                              <td class="igst_on_a1"></td>
                              <td class="igst_on_a2">
                                <?php
                                $igst_prev_bill = (isset($payment_certificate) ? $payment_certificate->igst_prev_bill : '');
                                echo render_input('igst_prev_bill', '', $igst_prev_bill, 'number', ['oninput' => "calculate_payment_certificate()"], [], '', 'text-right');
                                ?>
                              </td>
                              <td class="igst_on_a3">
                                <?php
                                $igst_this_bill = (isset($payment_certificate) ? $payment_certificate->igst_this_bill : '');
                                echo render_input('igst_this_bill', '', $igst_this_bill, 'number', ['oninput' => "calculate_payment_certificate()"], [], '', 'text-right');
                                ?>
                              </td>
                              <td class="igst_on_a4"></td>
                              <td>
                                <?php
                                $i3_remarks = (isset($payment_certificate) ? $payment_certificate->i3_remarks : '');
                                echo render_textarea('i3_remarks', '', $i3_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr>
                            <tr>
                              <td>I4</td>
                              <td class="labour_cess_class">
                                <?php echo _l('labour_cess'); ?>
                                <select name="labour_cess" id="labour_cess" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" onchange="calculate_payment_certificate()">
                                  <option value="0%" <?php if (isset($payment_certificate) && $payment_certificate->labour_cess == '0%') {
                                                        echo 'selected';
                                                      } ?>>0%</option>
                                  <option value="1%" <?php if (isset($payment_certificate) && $payment_certificate->labour_cess == '1%') {
                                                        echo 'selected';
                                                      } ?>>1%</option>
                                </select>
                              </td>
                              <td>
                                <?php
                                $labour_cess_1 = (isset($payment_certificate) ? $payment_certificate->labour_cess_1 : '');
                                echo render_input('labour_cess_1', '', $labour_cess_1, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $labour_cess_2 = (isset($payment_certificate) ? $payment_certificate->labour_cess_2 : '');
                                echo render_input('labour_cess_2', '', $labour_cess_2, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td>
                                <?php
                                $labour_cess_3 = (isset($payment_certificate) ? $payment_certificate->labour_cess_3 : '');
                                echo render_input('labour_cess_3', '', $labour_cess_3, 'number', ['oninput' => "calculate_payment_certificate()"]);
                                ?>
                              </td>
                              <td class="labour_cess_4"></td>
                              <td>
                                <?php
                                $i4_remarks = (isset($payment_certificate) ? $payment_certificate->i4_remarks : '');
                                echo render_textarea('i4_remarks', '', $i4_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>I</td>
                              <td><?php echo _l('total_applicable_taxes'); ?></td>
                              <td class="tot_app_tax_1"></td>
                              <td class="tot_app_tax_2"></td>
                              <td class="tot_app_tax_3"></td>
                              <td class="tot_app_tax_4"></td>
                              <td>
                                <?php
                                $i_remarks = (isset($payment_certificate) ? $payment_certificate->i_remarks : '');
                                echo render_textarea('i_remarks', '', $i_remarks, ['rows' => 1]);
                                ?>
                              </td>
                            </tr>
                            <tr class="table_head">
                              <td>J</td>
                              <td><?php echo _l('amount_recommended'); ?></td>
                              <td class="amount_rec_1"></td>
                              <td class="amount_rec_2"></td>
                              <td class="amount_rec_3"></td>
                              <td class="amount_rec_4"></td>
                              <td>
                                <?php
                                $j_remarks = (isset($payment_certificate) ? $payment_certificate->j_remarks : '');
                                echo render_textarea('j_remarks', '', $j_remarks, ['rows' => 1]);
                                ?>
                              </td> 
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <?php if ($is_view == 0) { ?>
                  <div class="btn-bottom-toolbar text-right">
                    <?php if (count($list_approve_status) == 0) { ?>
                      <input type="submit" name="save_and_send" class="btn-tr btn btn-info mleft10 pay-cert-submit" value="Save & Send" />
                    <?php } ?>
                    <button type="button" class="btn-tr btn btn-info mleft10 pay-cert-submit">
                      <?php echo _l('submit'); ?>
                    </button>
                  </div>
                <?php } ?>
              </div>

              <?php
              if (isset($payment_certificate)) { ?>
                <div role="tabpanel" class="tab-pane" id="tab_tasks">
                  <?php init_relation_tasks_table(array('data-new-rel-id' => $payment_certificate->id, 'data-new-rel-type' => 'payment_certificate')); ?>
                </div>
              <?php } ?>

              <div role="tabpanel" class="tab-pane ptop10" id="tab_documentation">
                <?php $this->load->view('pc_attachments', [
                  'is_view'     => $is_view,
                  'attachments' => isset($attachments) ? $attachments : [],
                  'goods_receipt' => $goods_receipt,
                  'goods_delivery' => $goods_delivery,
                ]); ?>
              </div>

              <div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
                <div class="row">
                  <div class="col-md-12">
                    <div class="activity-feed">
                      <?php foreach ($activity as $activity) {
                        $_custom_data = false; ?>
                        <div class="feed-item" data-sale-activity-id="<?php echo e($activity['id']); ?>">
                          <div class="date">
                            <span class="text-has-action" data-toggle="tooltip"
                              data-title="<?php echo e(_dt($activity['date'])); ?>">
                              <?php echo e(time_ago($activity['date'])); ?>
                            </span>
                          </div>
                          <div class="text">
                            <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                              <a href="<?php echo admin_url('profile/' . $activity['staffid']); ?>">
                                <?php echo staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                ?>
                              </a>
                            <?php } ?>
                            <?php
                            $additional_data = '';
                            if (!empty($activity['additional_data']) && $additional_data = unserialize($activity['additional_data'])) {
                              $i               = 0;
                              foreach ($additional_data as $data) {
                                if (strpos($data, '<original_status>') !== false) {
                                  $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                                  $additional_data[$i] = format_invoice_status($original_status, '', false);
                                } elseif (strpos($data, '<new_status>') !== false) {
                                  $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                                  $additional_data[$i] = format_invoice_status($new_status, '', false);
                                } elseif (strpos($data, '<custom_data>') !== false) {
                                  $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                  unset($additional_data[$i]);
                                }
                                $i++;
                              }
                            }

                            $_formatted_activity = _l($activity['description'], $additional_data);

                            if ($_custom_data !== false) {
                              $_formatted_activity .= ' - ' . $_custom_data;
                            }

                            if (!empty($activity['full_name'])) {
                              $_formatted_activity = e($activity['full_name']) . ' - ' . $_formatted_activity;
                            }

                            echo $_formatted_activity;

                            // if (is_admin()) {
                            //   echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa fa-remove"></i></a>';
                            // } 
                            ?>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php echo form_close(); ?>

              <?php
              if (isset($payment_certificate)) { ?>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_notes">
                 <?php echo form_open(admin_url('purchase/add_pc_note/' . $payment_certificate->id.'/wo_payment_certificate'), array('id' => 'pc-notes', 'class' => 'pc-notes-form')); ?>
                 <?php echo render_textarea('description'); ?>
                 <div class="text-right">
                    <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('estimate_add_note'); ?></button>
                 </div>
                 <?php echo form_close(); ?>
                 <hr />
                 <div class="panel_s mtop20 no-shadow" id="pc_notes_area">
                 </div>
                </div>

                <div role="tabpanel" class="tab-pane ptop10" id="discuss">
                 <div class="row contract-comments mtop15">
                    <div class="col-md-12">
                       <div id="contract-comments"></div>
                       <div class="clearfix"></div>
                       <textarea name="content" id="comment" rows="4" class="form-control mtop15 contract-comment"></textarea>
                       <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_contract_comment();"><?php echo _l('proposal_add_comment'); ?></button>
                    </div>
                 </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php if (count($list_approve_status) > 0) { ?>
      <div class="row">
        <div class="col-md-12">
          <div class="panel_s">
            <div class="panel-body">
              <div class="project-overview-right">
                <div class="row">
                  <div class="col-md-12 project-overview-expenses-finance">
                    <?php
                    $this->load->model('staff_model');
                    $enter_charge_code = 0;
                    foreach ($list_approve_status as $value) {
                      $value['staffid'] = explode(', ', $value['staffid'] ?? '');

                      if ($value['action'] == 'sign') { ?>
                        <div class="col-md-4 apr_div">
                          <p class="text-uppercase text-muted no-mtop bold">
                            <?php
                            $staff_name = '';
                            $st = _l('status_0');
                            $color = 'warning';
                            foreach ($value['staffid'] as $key => $val) {
                              if ($staff_name != '') {
                                $staff_name .= ' or ';
                              }
                              $staff_name .= $this->staff_model->get($val)->firstname;
                            }
                            echo pur_html_entity_decode($staff_name);
                            ?>
                          </p>
                          <?php if ($value['approve'] == 2) {
                          ?>
                            <img src="<?php echo site_url(PURCHASE_PATH . 'pur_order/signature/' . $estimate->id . '/signature_' . $value['id'] . '.png'); ?>" class="img_style">
                            <br><br>
                            <p class="bold text-center text-success"><?php echo _l('signed') . ' ' . _dt($value['date']); ?></p>
                          <?php } ?>
                        </div>
                      <?php } else { ?>
                        <div class="col-md-4 apr_div">
                          <p class="text-uppercase text-muted no-mtop bold">
                            <?php
                            $staff_name = '';
                            foreach ($value['staffid'] as $key => $val) {
                              if ($staff_name != '') {
                                $staff_name .= ' or ';
                              }
                              $staff_name .= $this->staff_model->get($val)->firstname;
                            }
                            echo pur_html_entity_decode($staff_name);
                            ?>
                          </p>

                          <?php if ($value['approve'] == 2) {
                          ?>
                            <?php if ($value['approve_by_admin'] == 1) { ?>
                              <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved_by_admin.png'); ?>" class="img_style">
                            <?php } else { ?>
                              <img src="<?php echo site_url(PURCHASE_PATH . 'approval/approved.png'); ?>" class="img_style">
                            <?php } ?>
                          <?php } elseif ($value['approve'] == 3) { ?>
                            <img src="<?php echo site_url(PURCHASE_PATH . 'approval/rejected.png'); ?>" class="img_style">
                          <?php } ?>
                          <br><br>
                          <p><?php echo pur_html_entity_decode($value['note']) ?></p>
                          <p class="bold text-center text-<?php if ($value['approve'] == 2) {
                                                            echo 'success';
                                                          } elseif ($value['approve'] == 3) {
                                                            echo 'danger';
                                                          } ?>"><?php echo _dt($value['date']); ?>
                          </p>

                          <?php
                          if (isset($check_approve_status['staffid'])) {
                            if (in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $value['staffid'] == $check_approve_status['staffid']) { ?>
                              <div class="btn-group">
                                <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>

                                <ul class="dropdown-menu dropdown-menu-right ul_style" style="width: max-content;">
                                  <li>
                                    <div class="col-md-12">
                                      <?php echo render_textarea('reason', 'reason'); ?>
                                    </div>
                                  </li>
                                  <li>
                                    <div class="row text-right col-md-12">
                                      <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_payment_certificate_request(<?php echo pur_html_entity_decode($payment_certificate_id); ?>); return false;" class="btn btn-success mright15"><?php echo _l('approve'); ?></a>
                                      <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_payment_certificate_request(<?php echo pur_html_entity_decode($payment_certificate_id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                          <?php }
                          } ?>
                        </div>
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

  </div>
</div>
</div>
<?php init_tail(); ?>
</body>

</html>

<script type="text/javascript">
</script>
<?php require 'modules/purchase/assets/js/wo_payment_certificate_js.php'; ?>
<script>
  $('#cgst_tax').on('change', function() {
    var prev_bill = $('#po_previous').val();
    var cgst_tax_new = $('#cgst_tax').val();
    var cgst_on_tax_new = prev_bill * (cgst_tax_new / 100);
    $('#cgst_prev_bill').val(cgst_on_tax_new);

    var this_bill = $('#po_this_bill').val();
    var cgst_tax_new1 = $('#cgst_tax').val();
    var cgst_on_this_tax_new = this_bill * (cgst_tax_new1 / 100);
    $('#cgst_this_bill').val(cgst_on_this_tax_new);
  });
  $('#sgst_tax').on('change', function() {
    var prev_bill = $('#po_previous').val();
    var sgst_tax_new = $('#sgst_tax').val();
    var sgst_on_tax_new = prev_bill * (sgst_tax_new / 100);
    $('#sgst_prev_bill').val(sgst_on_tax_new);

    var this_bill = $('#po_this_bill').val();
    var sgst_tax_new1 = $('#sgst_tax').val();
    var sgst_on_this_tax_new = this_bill * (sgst_tax_new1 / 100);
    $('#sgst_this_bill').val(sgst_on_this_tax_new);
  });
  $('#igst_tax').on('change', function() {
    var prev_bill = $('#po_previous').val();
    var igst_tax_new = $('#igst_tax').val();
    var igst_on_tax_new = prev_bill * (igst_tax_new / 100);
    $('#igst_prev_bill').val(igst_on_tax_new);

    var this_bill = $('#po_this_bill').val();
    var igst_tax_new1 = $('#igst_tax').val();
    var igst_on_this_tax_new = this_bill * (igst_tax_new1 / 100);
    $('#igst_this_bill').val(igst_on_this_tax_new);
  });
  $(document).ready(function() {
    "use strict";
    var is_view = <?php echo $is_view; ?>;
    if (is_view == 1) {
      $('input, select').not('select[name="status"]').prop('disabled', true);
      $('.selectpicker').selectpicker('refresh');
    } else {
      $('.selectpicker').selectpicker('refresh');
    }
  });
</script>
<script>
  document.getElementById('export-csv').addEventListener('click', function(event) {
    event.preventDefault();

    const table = document.querySelector('.wo-payment-certificate-table');
    const rows = Array.from(table.querySelectorAll('tr'));
    let csvContent = '';

    // Helper function to clean and escape CSV values
    const cleanCSVValue = (value) => {
      if (value === null || value === undefined) return '""';

      return `"${value.toString()
            .replace(/₹/g, '')              // Remove ₹ symbol
            .replace(/,/g, '')             // Remove commas (for numbers)
            .replace(/\s+/g, ' ')          // Normalize spaces
            .replace(/"/g, '""')           // Escape double quotes for CSV
            .trim()}"`;
    };

    // Helper to get combined content for C1 cell
    const getC1Content = (cell) => {
      const label = '<?php echo _l("mobilization_advance"); ?>';
      const select = cell.querySelector('select');
      const selectedOption = select ? select.options[select.selectedIndex].text : '';
      const clauseInput = cell.querySelector('input[name="payment_clause"]');
      const clauseValue = clauseInput ? clauseInput.value : '14.2';
      const mobilizationInput = cell.querySelector('input[name="mobilization_text"]');
      const mobilizationValue = mobilizationInput ? mobilizationInput.value : '';

      return `Advance ${mobilizationValue} ${clauseValue}`;
    };

    // Process each row
    rows.forEach(row => {
      const cells = Array.from(row.querySelectorAll('th, td'));
      const rowContent = cells.map(cell => {
        // Special handling for mobilization advance cell
        if (cell.classList.contains('mobilization_advance_class')) {
          return cleanCSVValue(getC1Content(cell));
        }

        const input = cell.querySelector('input:not([type="hidden"])');
        const select = cell.querySelector('select');
        const staticText = Array.from(cell.childNodes)
          .filter(node => node.nodeType === Node.TEXT_NODE)
          .map(node => node.textContent.trim())
          .join(' ')
          .trim();

        // Case 1: Both input and select exist
        if (input && select) {
          const inputValue = input.value;
          const selectValue = select.options[select.selectedIndex].text;
          const combined = `${staticText} ${selectValue} ${inputValue}`.trim();
          return cleanCSVValue(combined);
        }
        // Case 2: Only input exists
        else if (input) {
          return cleanCSVValue(input.value);
        }
        // Case 3: Only select exists
        else if (select) {
          return cleanCSVValue(select.options[select.selectedIndex].text);
        }
        // Case 4: Only static text
        else {
          return cleanCSVValue(cell.textContent);
        }
      }).join(',');

      csvContent += rowContent + '\n';
    });

    // Create and trigger download with BOM for Excel compatibility
    const bom = '\uFEFF';
    const blob = new Blob([bom + csvContent], {
      type: 'text/csv;charset=utf-8;'
    });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'payment_certificate_export_' + new Date().toISOString().slice(0, 10) + '.csv';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    setTimeout(() => {
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }, 100);
  });
  // Toggle settings dropdown visibility
  document.getElementById('settings-toggle-payment-certificate').addEventListener('click', function() {
    event.preventDefault(); // Prevent page reload

    const dropdown = document.getElementById('settings-dropdown-payment-certificate');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  });

  // Add event listener to toggle column visibility
  document.querySelectorAll('.column-toggle').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      const columnIndex = this.getAttribute('data-column');
      const table = document.querySelector('.wo-payment-certificate-table');

      // Iterate through all rows and toggle column visibility
      table.querySelectorAll('tr').forEach(function(row) {
        const cells = row.querySelectorAll('th, td');
        if (cells[columnIndex]) {
          cells[columnIndex].style.display = checkbox.checked ? '' : 'none';
        }
      });
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#sortable-tbody');
    if (!tbody) {
      return;
    }
    new Sortable(tbody, {
      handle: '.draggerer',
      animation: 150,
      ghostClass: 'sortable-ghost',
      onUpdate: function () {
        const newOrder = Array.from(tbody.querySelectorAll('tr'))
          .map(row => row.dataset.id)
          .filter(Boolean);

        $.ajax({
          url: admin_url + 'purchase/update_payment_certificate_file_order',
          type: 'POST',
          data: { order: newOrder },
          success: function(response) {
            alert_float('success', 'The order has been saved successfully.');
          },
          error: function(xhr) {
            console.error('AJAX error:', xhr.responseText);
          }
        });
      }
    });
  });
</script>