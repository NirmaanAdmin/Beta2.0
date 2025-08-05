<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="delivery_performance_report" class="hide">
   <div class="col-md-3 form-group">
      <select name="vendor_ids[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
         <option value=""></option>
         <?php
         $vendor = get_pur_vendor_list();
         foreach ($vendor as $vendors) { ?>
            <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
         <?php  } ?>
      </select>
   </div>

   <div class="col-md-3 form-group">
         <select name="delivery_status_filter" id="delivery_status_filter" class="form-control selectpicker" plcaceholder="<?php echo _l('delivery_status'); ?>" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('delivery_status'); ?>">
            <option value=""></option>
            <option value="on_time"><?php echo _l('On Time'); ?></option>
            <option value="delayed"><?php echo _l('Delayed'); ?></option>
            <option value="pending"><?php echo _l('Pending'); ?></option>
         </select>
   </div>
   <table class="table table-delivery-performance-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Item'); ?></th>
            <th><?php echo _l('Vendor'); ?></th>
            <th><?php echo _l('Expected Delivery'); ?></th>
            <th><?php echo _l('Actual Delivery'); ?></th>
            <th><?php echo _l('Delay'); ?></th>
            <th><?php echo _l('Delivery Status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>