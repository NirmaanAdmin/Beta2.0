<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="delivery_performance_report" class="hide">
   
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">

         </div>
      </div>
      <div class="clearfix"></div>
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