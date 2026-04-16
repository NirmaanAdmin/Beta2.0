<?php
if($file->rel_type == 'pur_order') {
	redirect(admin_url('purchase/purorder_pdf/' . $file->rel_id . '?output_type=I'));
} else if($file->rel_type == 'wo_order') {
	redirect(admin_url('purchase/woorder_pdf/' . $file->rel_id . '?output_type=I'));
} else if($file->rel_type == 'payment_certificate') {
	redirect(admin_url('purchase/payment_certificate_pdf/' . $file->rel_id . '?output_type=I'));
} else if($file->rel_type == 'pur_bill') {
	redirect(admin_url('purchase/bill_bifurcation_pdf/' . $file->rel_id . '?output_type=I'));
} else if($file->rel_type == 'co_order') {
	redirect(admin_url('changee/purorder_pdf/' . $file->rel_id . '?output_type=I'));
} else if($file->rel_type == 'goods_receipt') {
	redirect(admin_url('warehouse/stock_import_pdf/' . $file->rel_id . '?output_type=I'));
} else if($file->rel_type == 'goods_delivery') {
	redirect(admin_url('warehouse/stock_export_pdf/' . $file->rel_id . '?output_type=I'));
}
?>