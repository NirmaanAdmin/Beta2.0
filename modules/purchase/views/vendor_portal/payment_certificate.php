<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">

  <div class="col-md-12">
    <div class="panel_s">
      <div class="panel-body">
        <h4><?php echo pur_html_entity_decode($title) ?></h4>
        <hr class="mtop5">
        <table class="table dt-table">
          <thead>
            <tr>
              <th><?php echo _l('Payment certificate number'); ?></th>
              <th><?php echo _l('Order name'); ?></th>
              <th><?php echo _l('Order date'); ?></th>
              <th><?php echo _l('Budget Head'); ?></th>
              <th><?php echo _l('This Bill'); ?></th>
              <th><?php echo _l('Submission Date'); ?></th>
              <th><?php echo _l('options'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payment_certificate as $pc) { ?>
              <tr>
                <td><?php echo $pc['pc_number']; ?></td>
                <td>
                  <?php
                  // Order name logic
                  $order_name = '';

                  if (!empty($pc['po_id'])) {
                    // Get PO name by ID
                    $order_name = get_po_name_by_id($pc['po_id']);
                  } elseif (!empty($pc['wo_id'])) {
                    // Get WO name by ID
                    $order_name = get_wo_name_by_id($pc['wo_id']);
                  } elseif (!empty($pc['ot_id'])) {
                    // Get OT name by ID (assuming function name)
                    $order_name = get_ot_name_by_id($pc['ot_id']);
                  }

                  echo !empty($order_name) ? $order_name : 'N/A';
                  ?>
                </td>
                <td><?php echo date('d-m-Y', strtotime($pc['order_date'])); ?></td>
                <td>
                  <?php
                  // Get budget head from pay_cert_options (e.g., "ad_hoc")
                  $budget_head = ucwords(str_replace('_', ' ', $pc['pay_cert_options']));
                  echo $budget_head;
                  ?>
                </td>
                <td>
                  <?php
                  // This Bill amount - using pay_cert_c1_3 as it's the only non-zero value (108590.00)
                  $this_bill = $pc['pay_cert_c1_3'] + $pc['pay_cert_c2_3'];
                  echo '₹' . number_format($this_bill, 2);
                  ?>
                </td>
                <td><?php echo date('d-m-Y', strtotime($pc['bill_received_on'])); ?></td>
                <td>
                  <?php
                  // PDF Dropdown
                  $pdf = '';
                  $pdf = '<div class="btn-group display-flex">';
                  $pdf .= '<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>';
                  $pdf .= '<ul class="dropdown-menu dropdown-menu-right">';
                  $pdf .= '<li class="hidden-xs"><a href="' . site_url('purchase/vendors_portal/payment_certificate_pdf/' . $pc['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>';
                  $pdf .= '<li class="hidden-xs"><a href="' . site_url('purchase/vendors_portal/payment_certificate_pdf/' . $pc['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
                  $pdf .= '<li><a href="' . site_url('purchase/vendors_portal/payment_certificate_pdf/' . $pc['id']) . '">' . _l('download') . '</a></li>';
                  $pdf .= '<li><a href="' . site_url('purchase/vendors_portal/payment_certificate_pdf/' . $pc['id'] . '?print=true') . '" target="_blank">' . _l('print') . '</a></li>';
                  $pdf .= '</ul>';
                  $pdf .= '</div>';
                  echo $pdf;
                  ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>



<?php hooks()->do_action('app_admin_footer'); ?>
</body>

</html>
<?php require 'modules/purchase/assets/js/manage_order_vendor_js.php'; ?>