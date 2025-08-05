<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="po_wo_aging_report" class="hide">

   <div class="col-md-3 form-group">
      <select name="vendor_id[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
         <option value=""></option>
         <?php
         $vendor = get_pur_vendor_list();
         foreach ($vendor as $vendors) { ?>
            <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
         <?php  } ?>
      </select>
   </div>

   <div class="col-md-3 form-group">
      <select name="delivery_status[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('delivery_status'); ?>">
         <option value=""></option>
         <option value="0"><?php echo _l('Not Delivered'); ?></option>
         <option value="1"><?php echo _l('partially_delivered'); ?></option>
         <option value="2"><?php echo _l('Full Delivered'); ?></option>
      </select>
   </div>

   <div class="col-md-3 form-group">
      <select name="risk[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('Risk'); ?>">
         <option value=""></option>
         <option value="1"><?php echo _l('Low'); ?></option>
         <option value="2"><?php echo _l('Medium'); ?></option>
         <option value="3"><?php echo _l('High'); ?></option>
      </select>
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