<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_mapping_report" class="hide">
   <div class="col-md-3 form-group" style="padding-left: 0px;">
      <?php echo get_projects_list('mapping_project', ''); ?>
   </div>
   <div class="col-md-3 form-group">
      <select name="mapping_vendor[]" id="mapping_vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
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
      $statuses = [
         1 => ['id' => '5', 'name' => _l('bill_verified_by_ril')],
         2 => ['id' => '7', 'name' => _l('payment_processed')],
         3 => ['id' => '8', 'name' => _l('Paid')],
      ];
      echo render_select('mapping_status[]', $statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
   </div>
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
         </div>
      </div>
      <div class="clearfix"></div>
   </div>

   <table class="table table-mapping-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Vendor Invoice No'); ?></th>
            <th><?php echo _l('Vendor Name'); ?></th>
            <th><?php echo _l('Linked Client Invoice'); ?></th>
            <th><?php echo _l('Vendor Amount'); ?></th>
            <th><?php echo _l('Status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_vendor_amount"></td>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>