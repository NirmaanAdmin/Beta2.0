<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_invoicing_report" class="hide">
   <table class="table table-invoicing-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('purchase_order'); ?></th>
            <th><?php echo _l('date'); ?></th>
            <th><?php echo _l('department'); ?></th>
            <th><?php echo _l('vendor'); ?></th>
            <th><?php echo _l('approval_status'); ?></th>
            <th><?php echo _l('po_value'); ?></th>
            <th><?php echo _l('tax_value'); ?></th>
            <th><?php echo _l('po_value_included_tax'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>