<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="po_wo_aging_report" class="hide">
   
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">

         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <table class="table table-po-wo-aging-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('PO No.'); ?></th>
            <th><?php echo _l('Vendor Name'); ?></th>
            <th><?php echo _l('Date Issued'); ?></th>
            <th><?php echo _l('Delivery Status'); ?></th>
            <th><?php echo _l('Days Since Issued'); ?></th>
            <th><?php echo _l('Risk'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>