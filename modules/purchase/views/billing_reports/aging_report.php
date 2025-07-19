<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_aging_report" class="hide">
   <table class="table table-aging-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Vendor Name'); ?></th>
            <th><?php echo _l('Invoice No'); ?></th>
            <th><?php echo _l('Invoice Date'); ?></th>
            <th><?php echo _l('Amount'); ?></th>
            <th><?php echo _l('Days Since Invoice'); ?></th>
            <th><?php echo _l('Status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>