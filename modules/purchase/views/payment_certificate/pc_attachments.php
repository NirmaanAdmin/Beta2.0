<div class="panel-body mtop15">
  <?php if ($is_view == 0) { ?>
    <label for="attachment"><?php echo _l('attachment'); ?></label>
    <div class="attachments">
      <div class="attachment">
        <div class="col-md-5 form-group" style="padding-left: 0px;">
          <div class="input-group">
            <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
            <span class="input-group-btn">
              <button class="btn btn-success add_more_attachments p8" type="button"><i class="fa fa-plus"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  <div class="clearfix"></div>
</div>

<?php if (isset($attachments) && count($attachments) > 0) { ?>
  <div class="panel-body">
    <label for="orders"><?php echo _l('Orders'); ?></label>
    <hr>
    <table class="table table-attachments scroll-responsive no-mtop">
      <thead class="bg-light-gray">
        <tr>
          <th></th>
          <th><?php echo _l('Name'); ?></th>
          <th><?php echo _l('Preview'); ?></th>
          <th><?php echo _l('Delete'); ?></th>
        </tr>
      </thead>
      <tbody id="sortable-tbody">
        <?php foreach ($attachments as $value) {
          $path = get_upload_path_by_type('purchase') . 'payment_certificate/' . $value['rel_id'] . '/' . $value['file_name'];
          $is_image = is_image($path);
          $preview_img = $is_image ? '<img src="' . site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']) . '" style="height: 50px;">' : '';
          ?>
          <tr class="sortable item" data-id="<?php echo $value['id']; ?>">
            <td class="draggerer"></td>
            <td>
              <a href="<?php echo site_url('download/file/payment_certificate/' . $value['id']); ?>" <?php if ($is_image) { ?> data-lightbox="attachment-payment_certificate-<?php echo $value['rel_id']; ?>" <?php } ?>>
                <?php echo $value['file_name']; ?>
              </a>
            </td>
            <td>
              <a name="preview-payment-cert-btn" onclick="preview_paymentcert_btn(this); return false;" rel_id="<?php echo $value['rel_id']; ?>" id="<?php echo $value['id']; ?>" href="javascript:void(0);" class="btn btn-success btn-sm mright5" data-toggle="tooltip" title="<?php echo _l('preview_file'); ?>">
                <i class="fa fa-eye"></i>
              </a>
              <?php echo $preview_img; ?>
            </td>
            <td>
              <a href="<?php echo admin_url('purchase/delete_payment_certificate_files/' . $value['id']); ?>" class="btn btn-danger btn-sm _delete">
                <?php echo _l('delete'); ?>
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if (isset($goods_receipt) && count($goods_receipt) > 0) { 
          foreach ($goods_receipt as $value) { ?>
          <tr>
            <td>
            </td>
            <td>
              <?php if(!empty($value['pr_order_id'])) { ?>
                <a href="<?php echo admin_url('purchase/purchase_order/' . $value['pr_order_id']) ?>"><?php echo get_pur_order_name($value['pr_order_id']).' ('._l('goods_receipt').')' ?></a>
              <?php } elseif (!empty($value['wo_order_id'])) { ?>
                <a href="<?php echo admin_url('purchase/work_order/' . $value['wo_order_id']) ?>"><?php echo get_wo_order_name($value['wo_order_id']).' ('._l('goods_receipt').')' ?></a>
              <?php } else {

              } ?>
            </td> 
            <td>
              <a href="<?php echo admin_url('warehouse/stock_import_pdf/' . $value['id'] . '/?output_type=I') ?>" 
                 target="_blank"
                 class="btn btn-success btn-sm mright5" 
                 data-toggle="tooltip" 
                 title="<?php echo _l('preview_file'); ?>">
                 <i class="fa fa-eye"></i>
              </a>
            </td>
            <td>
            </td>
          </tr>
        <?php } } ?>
        <?php if (isset($goods_delivery) && count($goods_delivery) > 0) { 
          foreach ($goods_delivery as $value) { ?>
          <tr>
            <td>
            </td>
            <td>
              <?php if(!empty($value['pr_order_id'])) { ?>
                <a href="<?php echo admin_url('purchase/purchase_order/' . $value['pr_order_id']) ?>"><?php echo get_pur_order_name($value['pr_order_id']).' ('._l('goods_delivery').')' ?></a>
              <?php } elseif (!empty($value['wo_order_id'])) { ?>
                <a href="<?php echo admin_url('purchase/work_order/' . $value['wo_order_id']) ?>"><?php echo get_wo_order_name($value['wo_order_id']).' ('._l('goods_delivery').')' ?></a>
              <?php } else {

              } ?>
            </td>
            <td>
              <a href="<?php echo admin_url('warehouse/stock_export_pdf/' . $value['id'] . '/?output_type=I') ?>" 
                 target="_blank"
                 class="btn btn-success btn-sm mright5" 
                 data-toggle="tooltip" 
                 title="<?php echo _l('preview_file'); ?>">
                 <i class="fa fa-eye"></i>
              </a>
            </td>
            <td>
            </td>
          </tr>
        <?php } } ?>

        <?php if (isset($stock_reconciliation) && count($stock_reconciliation) > 0) { 
          foreach ($stock_reconciliation as $value) { ?>
          <tr>
            <td>
            </td>
            <td>
              <?php if(!empty($value['pr_order_id'])) { ?>
                <a href="<?php echo admin_url('purchase/purchase_order/' . $value['pr_order_id']) ?>"><?php echo get_pur_order_name($value['pr_order_id']).' ('._l('Stock Reconciliation').')' ?></a>
              <?php } elseif (!empty($value['wo_order_id'])) { ?>
                <a href="<?php echo admin_url('purchase/work_order/' . $value['wo_order_id']) ?>"><?php echo get_wo_order_name($value['wo_order_id']).' ('._l('Stock Reconciliation').')' ?></a>
              <?php } else {

              } ?>
            </td>
            <td>
              <a href="<?php echo admin_url('warehouse/stock_reconcile_export_pdf/' . $value['id'] . '/?output_type=I') ?>" 
                 target="_blank"
                 class="btn btn-success btn-sm mright5" 
                 data-toggle="tooltip" 
                 title="<?php echo _l('preview_file'); ?>">
                 <i class="fa fa-eye"></i>
              </a>
            </td>
            <td>
            </td>
          </tr>
        <?php } } ?>
      </tfoot>
    </table>
  </div>
<?php } ?>
<div id="paymentcert_file_data"></div>
<?php if ($is_view == 0) { ?>
  <div class="btn-bottom-toolbar text-right">
    <button type="button" class="btn-tr btn btn-info mleft10 pay-cert-submit">
      <?php echo _l('submit'); ?>
    </button>
  </div>
<?php } ?>