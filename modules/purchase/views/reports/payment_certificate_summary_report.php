<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="payment_certificate_summary_report" class="hide">
   
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">

         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <table class="table table-payment-certificate-summary-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('PO No.'); ?></th>
            <th><?php echo _l('Vendor Name'); ?></th>
            <th><?php echo _l('PO Value (₹)'); ?></th>
            <th><?php echo _l('Paid via PC (₹)'); ?></th>
            <th><?php echo _l('Balance (₹)'); ?></th>
            <th><?php echo _l('Paid (%)'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>