<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_client_aging_report" class="hide">
   <table class="table table-client-aging-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Invoice No'); ?></th>
            <th><?php echo _l('Project Name'); ?></th>
            <th><?php echo _l('Invoice Date'); ?></th>
            <th><?php echo _l('Amount Due'); ?></th>
            <th><?php echo _l('Days Outstanding'); ?></th>
            <th><?php echo _l('Status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>