<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .onoffswitch-label:before {

        height: 20px !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_hidden('purchase_id', $purchase_id); ?>
                        <div class="row">
                            <div class="col-md-12" style="padding: 0px;">
                                <div class="col-md-12" id="heading">
                                    <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> Stock Received</h4>
                                    <hr />
                                </div>
                                <?php /* <div class="col-md-2 display-flex" id="filter_div">
                                    <label>PO Not received</label>
                                    <div class="onoffswitch" style="margin-left: 10px;">
                                        <input type="checkbox" name="toggle-filter" class="onoffswitch-checkbox toggle-filter" id="c_' . $aRow['staffid'] . '" value="0">
                                        <label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
                                    </div>

                                    <hr />
                                </div> */ ?>

                            </div>

                        </div>
                        <div class="row">
                            <div class="_buttons col-md-3">
                                <?php if (has_permission('warehouse', '', 'create') || is_admin()) { ?>
                                    <a href="<?php echo admin_url('warehouse/manage_goods_receipt'); ?>" class="btn btn-info pull-left mright10 display-block">
                                        Stock Received
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="col-md-1 pull-right">
                                <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal(' .purchase_sm','#purchase_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-md-3 pull-right">
                                <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('cat'); ?>">
                                    <option value=""></option>
                                    <option value="Client Supply"><?php echo _l('client_supply'); ?></option>
                                    <option value="Bought out items"><?php echo _l('bought_out_items'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3 pull-right">
                                <?php
                                $input_attr_e = [];
                                $input_attr_e['placeholder'] = _l('day_vouchers');

                                echo render_date_input('date_add', '', '', $input_attr_e); ?>
                            </div>
                            <div class="col-md-3 pull-right">
                                <select name="vendor[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
                                    <option value=""></option>
                                    <?php
                                    $vendor = get_pur_vendor_list();
                                    foreach ($vendor as $vendors) { ?>
                                        <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="col-md-3 pull-right">
                                <select name="status" id="status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
                                    <option value=""></option>
                                    <option value="approved"><?php echo _l('approved'); ?></option>
                                    <option value="not_yet_approve"><?php echo _l('not_yet_approve'); ?></option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <?php render_datatable(array(
                            _l('id'),
                            _l('stock_received_docket_code'),
                            _l('supplier_name'),
                            _l('Buyer'),
                            _l('category'),
                            _l('reference_purchase_order'),
                            _l('day_vouchers'),

                            // _l('total_tax_money'),
                            // _l('total_goods_money'),
                            // _l('value_of_inventory'),
                            // _l('total_money'),
                            _l('status_label'),
                            _l('options'),
                        ), 'table_manage_goods_receipt', ['purchase_sm' => 'purchase_sm']); ?>

                    </div>
                </div>
            </div>

            <div class="col-md-7 small-table-right-col">
                <div id="purchase_sm_view" class="hide">
                </div>
            </div>

        </div>
    </div>
</div>



<script>
    var hidden_columns = [3, 4, 5];
</script>
<?php init_tail(); ?>
</body>

</html>