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
              <th><?php echo _l('Bill Code'); ?></th>
              <th><?php echo _l('order_name'); ?></th>
              <th><?php echo _l('created'); ?></th>
              <th><?php echo _l('Amount'); ?></th>
              <th><?php echo _l('approval_status'); ?></th>
              <th><?php echo _l('options'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($payment_certificate)) { ?>

              <?php foreach ($payment_certificate as $pc) { ?>

                <tr>

                  <!-- Bill Code -->
                  <td>
                    <?php echo html_escape($pc['bill_number']); ?>
                  </td>

                  <!-- Order Name -->
                  <td>
                    <?php
                    $order_name = '';

                    if (!empty($pc['pur_order'])) {

                      // Get PO name by ID
                      $order_name = get_po_name_by_id($pc['pur_order']);
                    } elseif (!empty($pc['wo_order'])) {

                      // Get WO name by ID
                      $order_name = get_wo_name_by_id($pc['wo_order']);
                    }

                    echo !empty($order_name)
                      ? html_escape($order_name)
                      : 'N/A';
                    ?>
                  </td>

                  

                  <!-- Created -->
                  <td>
                    <?php echo _d($pc['date_add']); ?>
                  </td>

                  <!-- Amount -->
                  <td>
                    <?php
                    echo app_format_money(
                      $pc['total'],
                      get_base_currency_pur()->symbol
                    );
                    ?>
                  </td>

                  <!-- Approval Status -->
                  <td>
                    <?php
                    if ($pc['approve_status'] == 1) {

                      echo '<span class="label label-primary">'
                        . _l('pur_draft') .
                        '</span>';
                    } elseif ($pc['approve_status'] == 2) {

                      echo '<span class="label label-success">'
                        . _l('approved') .
                        '</span>';
                    } elseif ($pc['approve_status'] == 3) {

                      echo '<span class="label label-danger">'
                        . _l('rejected') .
                        '</span>';
                    } else {

                      echo '<span class="label label-primary">'
                        . _l('pur_draft') .
                        '</span>';
                    }
                    ?>
                  </td>

                  <!-- Options -->
                  <td>
                    <a href="<?php echo admin_url('purchase/edit_pur_bills/' . $pc['id']); ?>"
                      class="btn btn-default btn-sm"
                      target="_blank">
                      <i class="fa fa-eye"></i>
                    </a>
                  </td>

                </tr>

              <?php } ?>

            <?php } else { ?>

              <tr>
                <td colspan="6" class="text-center">
                  <?php echo _l('no_data_found'); ?>
                </td>
              </tr>

            <?php } ?>
          </tbody>
        </table>
        </table>
      </div>
    </div>
  </div>
</div>



<?php hooks()->do_action('app_admin_footer'); ?>
</body>

</html>
<?php require 'modules/purchase/assets/js/manage_order_vendor_js.php'; ?>