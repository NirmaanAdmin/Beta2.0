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
} else {
	$CI =& get_instance();
	if($file->rel_type == 'pur_order_attachment') {
		$CI->db->where('id', $file->rel_id);
		$purchase_file = $CI->db->get(db_prefix() . 'purchase_files')->row();
		$path = 'uploads/purchase/pur_order/' . $purchase_file->rel_id . '/' . $file->name;
	} else if($file->rel_type == 'wo_order_attachment') {
		$CI->db->where('id', $file->rel_id);
		$purchase_file = $CI->db->get(db_prefix() . 'purchase_files')->row();
		$path = 'uploads/purchase/wo_order/' . $purchase_file->rel_id . '/' . $file->name;
	} else if($file->rel_type == 'payment_certificate_attachment') {
		$CI->db->where('id', $file->rel_id);
		$payment_certificate_file = $CI->db->get(db_prefix() . 'payment_certificate_files')->row();
		$path = 'uploads/purchase/payment_certificate/' . $payment_certificate_file->rel_id . '/' . $file->name;
	} else if($file->rel_type == 'co_order_attachment') {
		$CI->db->where('id', $file->rel_id);
		$changee_file = $CI->db->get(db_prefix() . 'changee_files')->row();
		$path = 'uploads/changee/pur_order/' . $changee_file->rel_id . '/' . $file->name;
	} else if($file->rel_type == 'goods_receipt_attachment') {
		$CI->db->where('id', $file->rel_id);
		$invetory_file = $CI->db->get(db_prefix() . 'invetory_files')->row();
		$path = 'uploads/inventory/goods_receipt/' . $invetory_file->rel_id . '/' . $file->name;
	} else if($file->rel_type == 'goods_delivery_attachment') {
		$CI->db->where('id', $file->rel_id);
		$invetory_file = $CI->db->get(db_prefix() . 'invetory_files')->row();
		$path = 'uploads/inventory/goods_delivery/' . $invetory_file->rel_id . '/' . $file->name;
	} else {
		$path = '';
	}
	if(!empty($path)) {
		if(is_image($path)){ ?>
		   <img src="<?php echo base_url($path); ?>" class="img img-responsive img_style">
		<?php } else if(!empty($file->external) && !empty($file->thumbnail_link)){ ?>
		   <img src="<?php echo optimize_dropbox_thumbnail($file->thumbnail_link); ?>" class="img img-responsive">
		<?php } else if(strpos($file->name,'.pdf') !== false && empty($file->external)){ ?>
		   <iframe src="<?php echo base_url($path); ?>" height="100%" width="100%" frameborder="0"></iframe>
		<?php } else if(strpos($file->name,'.xls') !== false && empty($file->external)){ ?>
		   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url($path).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
		   </iframe>
		<?php } else if(strpos($file->name,'.xlsx') !== false && empty($file->external)){ ?>
		   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url($path).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
		   </iframe>
		<?php } else if(strpos($file->name,'.doc') !== false && empty($file->external)){ ?>
		   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url($path).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
		   </iframe>
		<?php } else if(strpos($file->name,'.docx') !== false && empty($file->external)){ ?>
		   <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url($path).'?v='.date('H.i.s'); ?>' width='100%' height='100%' frameborder='0'>
		   </iframe>
		<?php } else if(is_html5_video($path)) { ?>
		   <video width="100%" height="100%" src="<?php echo site_url('download/preview_video?path='.protected_file_url_by_path($path).'&type='.$file->filetype); ?>" controls>
		      Your browser does not support the video tag.
		   </video>
		<?php } else if(is_markdown_file($path) && $previewMarkdown = markdown_parse_preview($path)) {
		   echo htmldecode($previewMarkdown);
		} else {
		   echo '<p class="text-muted">'._l('no_preview_available_for_file').'</p>';
		}
	} else {
		echo '<p class="text-muted">'._l('no_preview_available_for_file').'</p>';
	}
}
?>