<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
   .group-name-cell {

      font-size: 20px;
      font-weight: bold;
      /* Optional, for better visibility */
   }
</style>
<div id="list_item_tracker_report" class="hide">
   <div class="col-md-3 form-group">
      <!-- <label for="type"><?php echo _l('type'); ?></label> -->
      <select name="pur_order[]" id="pur_order" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('pur_order'); ?>">
         <option value=""></option>
         <?php
         $pur_order = get_pur_all_orders();
         foreach ($pur_order as $order) { ?>
            <option value="<?php echo $order['id']; ?>"><?php echo  $order['pur_order_number'] . '-' . $order['pur_order_name']; ?></option>
         <?php  } ?>
      </select>
   </div>
   <div class="col-md-3 form-group">
      <select name="vendor[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
         <option value=""></option>
         <?php
         $vendor = get_pur_vendor_list();
         foreach ($vendor as $vendors) { ?>
            <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
         <?php  } ?>
      </select>
   </div>
   <div class="col-md-3 form-group">
      <?php
      $pruduction_statuses = [
         0 => ['id' => '1', 'name' => _l('not_started')],
         1 => ['id' => '2', 'name' => _l('on_going')],
         2 => ['id' => '3', 'name' => _l('approved')],
      ];
      echo render_select('production_status[]', $pruduction_statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('production_status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
   </div>
   <div class="col-md-3 form-group">
     <select name="delivery" id="delivery" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('delivery_status'); ?>">
         <option value=""></option>
         <option value="undelivered"><?php echo _l('undelivered'); ?></option>
         <option value="partially_delivered"><?php echo _l('partially_delivered'); ?></option>
         <option value="completely_delivered"><?php echo _l('completely_delivered'); ?></option>
     </select>
   </div>
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">

         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <table class="table table-item-tracker-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('purchase_order'); ?></th>
            <th><?php echo _l('Uniclass Code'); ?></th>
            <th><?php echo _l('decription'); ?></th>
            <th><?php echo _l('po_quantity'); ?></th>
            <th><?php echo _l('received_quantity'); ?></th>
            <th><?php echo _l('remaining'); ?></th>
            <th><?php echo _l('production_status'); ?></th>
            <th><?php echo _l('payment_date'); ?></th>
            <th><?php echo _l('est_delivery_date'); ?></th>
            <th><?php echo _l('delivery_date'); ?></th>
            <th><?php echo _l('delivery_status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>