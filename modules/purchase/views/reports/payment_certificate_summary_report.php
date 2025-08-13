<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="payment_certificate_summary_report" class="hide">

   <div class="col-md-3 form-group">
      <select name="vendors[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
         <option value=""></option>
         <?php
         $vendor = get_pur_vendor_list();
         foreach ($vendor as $vendors) { ?>
            <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
         <?php  } ?>
      </select>
   </div>
   <div class="clearfix"></div>
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
      <tfoot>
         <tr>
            <td></td>
            <td></td>
            <td class="total_po_value"></td>
            <td class="total_paid_value"></td>
            <td class="total_balance_value"></td>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>