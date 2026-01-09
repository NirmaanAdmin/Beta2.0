<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="dropdown-menu search-results animated fadeIn no-mtop display-block" id="top_search_dropdown">
    <?php
    $total = 0;
    foreach($result as $data){
       if(count($data['result']) > 0){
           $total++;
           ?>
           <li role="separator" class="divider"></li>
           <li class="dropdown-header"><?php echo e($data['search_heading']); ?></li>
       <?php } ?>
       <?php foreach($data['result'] as $_result){
        $output = '';
        switch($data['type']){
            case 'staff':
            $output = '<a href="'.admin_url('staff/member/'.$_result['staffid']).'">'.e($_result['firstname']. ' ' . $_result['lastname']) .'</a>';
            break;
            case 'clients':
            $output = '<a href="'.admin_url('clients/client/'.$_result['userid']).'">'.e($_result['company']) .'</a>';
            break;
            case 'contacts':
            $output = '<a href="'.admin_url('clients/client/'.$_result['userid'].'?contactid='.$_result['id']).'">'. e($_result['firstname'] .' ' . $_result['lastname']) .' <small>'.e(get_company_name($_result['userid'])).'</small></a>';
            break;
            case 'projects':
            $output = '<a href="'.admin_url('projects/view/'.$_result['id']).'">'.e($_result['name']).'</a>';
            break;
            case 'estimates':
            $output = '<a href="'.admin_url('estimates/list_estimates/'.$_result['estimateid']).'">'.e(format_estimate_number($_result['estimateid'])).'<span class="pull-right">'.e(date('Y',strtotime($_result['date']))).'</span></a>';
            break;
            case 'estimate_items':
            $output = '<a href="'.admin_url('estimates/list_estimates/'.$_result['rel_id']).'">'.e(format_estimate_number($_result['rel_id']));
            $output .= '<br />';
            $output .= '<small>'.e($_result['description']).'</small>';
            $output .= '</a>';
            break;
            case 'estimate_commodity_groups':
            $output = '<a href="'.admin_url('costplanning/setting?group=commodity_group').'">'.e($_result['name']).'</a>';
            break;
            case 'estimate_sub_groups':
            $output = '<a href="'.admin_url('costplanning/setting?group=sub_group').'">'.e($_result['sub_group_name']).'</a>';
            break;
            case 'estimate_master_areas':
            $output = '<a href="'.admin_url('costplanning/setting?group=master_area').'">'.e($_result['category_name']).'</a>';
            break;
            case 'estimate_functionality_areas':
            $output = '<a href="'.admin_url('costplanning/setting?group=functionality_area').'">'.e($_result['category_name']).'</a>';
            break;
            case 'items':
            $output = '<a href="'.admin_url('purchase/items/'.$_result['id']).'">'.e($_result['commodity_code']).' '.e($_result['description']).'</a>';
            break;
            case 'vendors':
            $output = '<a href="'.admin_url('purchase/vendor/'.$_result['userid']).'">'.e($_result['company']).'</a>';
            break;
            case 'vendor_contacts':
            $output = '<a href="'.admin_url('purchase/vendor/'.$_result['userid'].'?group=contacts').'">'.e($_result['firstname']).' '.e($_result['lastname']).'</a>';
            break;
            case 'unawarded_trackers':
            $output = '<a href="'.admin_url('purchase/unawarded_tracker').'">'.e($_result['package_name']).'</a>';
            break;
            case 'purchase_requests':
            $output = '<a href="'.admin_url('purchase/view_pur_request/'.$_result['id']).'">'.e($_result['pur_rq_code']).'</a>';
            break;
            case 'purchase_request_items':
            $output = '<a href="'.admin_url('purchase/view_pur_request/'.$_result['id']).'">'.e($_result['pur_rq_code']).'</a>';
            break;
            case 'quotations':
            $output = '<a href="'.admin_url('purchase/quotations/'.$_result['id']).'">'.e(format_pur_estimate_number($_result['id'])).'</a>';
            break;
            case 'purchase_orders':
            $output = '<a href="'.admin_url('purchase/purchase_order/'.$_result['id']).'">'.e($_result['pur_order_number']).'</a>';
            break;
            case 'purchase_order_items':
            $output = '<a href="'.admin_url('purchase/purchase_order/'.$_result['id']).'">'.e($_result['pur_order_number']).'</a>';
            break;
            case 'work_orders':
            $output = '<a href="'.admin_url('purchase/work_order/'.$_result['id']).'">'.e($_result['wo_order_number']).'</a>';
            break;
            case 'work_order_items':
            $output = '<a href="'.admin_url('purchase/work_order/'.$_result['id']).'">'.e($_result['wo_order_number']).'</a>';
            break;
            case 'payment_certificate':
            if(!empty($_result['po_id'])) {
                $output = '<a href="'.admin_url('purchase/payment_certificate/'.$_result['po_id'].'/'.$_result['id']).'">'.e($_result['pc_number']).'</a>';
            } else if(!empty($_result['wo_id'])) {
                $output = '<a href="'.admin_url('purchase/wo_payment_certificate/'.$_result['wo_id'].'/'.$_result['id']).'">'.e($_result['pc_number']).'</a>';
            } else if(!empty($_result['ot_id'])) {
                $output = '<a href="'.admin_url('purchase/ot_payment_certificate/'.$_result['ot_id'].'/'.$_result['id']).'">'.e($_result['pc_number']).'</a>';
            } else {
                $output = '';
            }
            break;
            case 'pur_bills':
            $output = '<a href="'.admin_url('purchase/edit_pur_bills/'.$_result['id']).'">'.e($_result['bill_code']).'</a>';
            break;
            case 'pur_bill_items':
            $output = '<a href="'.admin_url('purchase/edit_pur_bills/'.$_result['id']).'">'.e($_result['bill_code']).'</a>';
            break;
            case 'order_tracker':
            $output = '<a href="'.admin_url('purchase/order_tracker').'">'.e($_result['order_name']).'</a>';
            break;
            case 'purchase_tracker':
            if($_result['type'] == 1) {
                $output = '<a href="'.admin_url('purchase/view_purchase/'.$_result['id']).'">'.e($_result['order_name']).'</a>';
            } else if($_result['type'] == 2) {
                $output = '<a href="'.admin_url('purchase/view_po_tracker/'.$_result['id']).'">'.e($_result['order_name']).'</a>';
            } else if($_result['type'] == 3) {
                $output = '<a href="'.admin_url('purchase/view_wo_tracker/'.$_result['id']).'">'.e($_result['order_name']).'</a>';
            } else {
                $output = '';
            }
            break;
            case 'change_orders':
            $output = '<a href="'.admin_url('changee/changee_order/'.$_result['id']).'">'.e($_result['pur_order_number']).'</a>';
            break;
            case 'change_order_items':
            $output = '<a href="'.admin_url('changee/changee_order/'.$_result['id']).'">'.e($_result['pur_order_number']).'</a>';
            break;
            case 'invoices':
            $output = '<a href="'.admin_url('invoices/list_invoices/'.$_result['invoiceid']).'">'.e(format_invoice_number($_result['invoiceid'])).'<span class="pull-right">'.e(date('Y',strtotime($_result['date']))).'</span></a>';
            break;
            case 'invoice_items':
            $output = '<a href="'.admin_url('invoices/list_invoices/'.$_result['rel_id']).'">'.e(format_invoice_number($_result['rel_id']));
            $output .= '<br />';
            $output .= '<small>'.e($_result['description']).'</small>';
            $output .= '</a>';
            break;
            case 'invoice_payment_records':
            $output = '<a href="'.admin_url('payments/payment/'.$_result['paymentid']).'">#'.$_result['paymentid'].'<span class="pull-right">'.e(date('Y',strtotime($_result['date']))).'</span></a>';
            break;
            case 'pur_invoices':
            $output = '<a href="'.admin_url('purchase/purchase_invoice/'.$_result['id']).'">'.e($_result['invoice_number']).'</a>';
            break;
            case 'pur_invoice_payments':
            $output = '<a href="'.admin_url('purchase/purchase_invoice/'.$_result['id']).'">'.e($_result['invoice_number']).'</a>';
            break;
            case 'debit_note':
            $output = '<a href="'.admin_url('purchase/debit_notes/'.$_result['id']).'">'.e(format_debit_note_number($_result['id'])).'<span class="pull-right">'.e(date('Y',strtotime($_result['date']))).'</span></a>';
            break;
            case 'debit_note_items':
            $output = '<a href="'.admin_url('purchase/debit_notes/'.$_result['rel_id']).'">'.e(format_debit_note_number($_result['rel_id']));
            $output .= '<br />';
            $output .= '<small>'.e($_result['description']).'</small>';
            $output .= '</a>';
            break;
            case 'credit_note':
            $output = '<a href="'.admin_url('credit_notes/list_credit_notes/'.$_result['credit_note_id']).'">'.e(format_credit_note_number($_result['credit_note_id'])).'<span class="pull-right">'.e(date('Y',strtotime($_result['date']))).'</span></a>';
            break;
            case 'credit_note_items':
            $output = '<a href="'.admin_url('credit_notes/list_credit_notes/'.$_result['rel_id']).'">'.e(format_credit_note_number($_result['rel_id']));
            $output .= '<br />';
            $output .= '<small>'.e($_result['description']).'</small>';
            $output .= '</a>';
            break;
            case 'stock_import':
            $output = '<a href="'.admin_url('warehouse/manage_purchase/'.$_result['id']).'">'.e($_result['goods_receipt_code']).'</a>';
            break;
            case 'stock_import_items':
            $output = '<a href="'.admin_url('warehouse/manage_purchase/'.$_result['id']).'">'.e($_result['goods_receipt_code']).'</a>';
            break;
            case 'stock_export':
            $output = '<a href="'.admin_url('warehouse/manage_delivery/'.$_result['id']).'">'.e($_result['goods_delivery_code']).'</a>';
            break;
            case 'stock_export_items':
            $output = '<a href="'.admin_url('warehouse/manage_delivery/'.$_result['id']).'">'.e($_result['goods_delivery_code']).'</a>';
            break;
            case 'internal_delivery_note':
            $output = '<a href="'.admin_url('warehouse/view_internal_delivery/'.$_result['id']).'">'.e($_result['internal_delivery_code']).' - '.e($_result['internal_delivery_name']).'</a>';
            break;
            case 'loss_adjustment':
            $output = '<a href="'.admin_url('warehouse/view_lost_adjustment/'.$_result['id']).'">'.e($_result['type']).' - '.e($_result['time']).'</a>';
            break;
            case 'expenses':
            $output = '<a href="'.admin_url('expenses/list_expenses/'.$_result['expenseid']).'">'.e($_result['category_name']). ' - ' .e(app_format_money($_result['amount'], $_result['currency_name'])).'</a>';
            break;
            case 'tasks':
            $task_link = 'init_task_modal('.$_result['id'].'); return false;';
            $output = '<a href="#" onclick="'.$task_link.'">'.e($_result['name']).'</a>';
            break;
            case 'tickets':
            $output = '<a href="'.admin_url('tickets/ticket/'.$_result['ticketid']).'">#'.e($_result['ticketid'].' - '.$_result['subject']).'</a>';
            break;
            case 'contracts':
            $output = '<a href="'.admin_url('contracts/contract/'.$_result['id']).'">'.e($_result['subject']).'</a>';
            break;
            case 'custom_fields':
            $rel_data   = get_relation_data($_result['fieldto'], $_result['relid']);
            $rel_values = get_relation_values($rel_data, $_result['fieldto']);
            $output      = '<a class="pull-left" href="' . $rel_values['link'] . '">' . e($rel_values['name']) .'<span class="pull-right">'._l($_result['fieldto']).'</span></a>';
            break;
            case 'leads':
            $output = '<a href="#" onclick="init_lead('.$_result['id'].');return false;">'.e($_result['name']).'</a>';
            break;
            case 'proposals':
            $output = '<a href="'.admin_url('proposals/list_proposals/'.$_result['id']).'">'.e(format_proposal_number($_result['id']) .' - ' . $_result['subject']) .'</a>';
            break;
            case 'knowledge_base_articles':
            if($_result['staff_article']) {
                $output = '<a href="'.admin_url('knowledge_base/view/'.$_result['slug']).'">'.e($_result['subject']).'</a>';
            } else {
                $output = '<a href="'.site_url('knowledge_base/'.$_result['slug']).'">'.e($_result['subject']).'</a>';
            }
            break;
        }
        ?>
        <li><?php echo hooks()->apply_filters('global_search_result_output', $output, ['result'=>$_result, 'type'=>$data['type']]); ?></li>
    <?php } ?>
<?php } ?>
<?php if($total == 0){ ?>
    <li class="padding-5 text-center search-no-results"><?php echo _l('not_results_found'); ?></li>
<?php } ?>
</ul>
