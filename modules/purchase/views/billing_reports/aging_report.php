<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_aging_report" class="hide">
   <div class="col-md-3 form-group" style="padding-left: 0px;">
      <?php echo get_projects_list('aging_project', ''); ?>
   </div>
   <div class="col-md-3 form-group">
      <select name="aging_vendor[]" id="aging_vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
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
         1 => ['id' => '0', 'name' => _l('unpaid')],
         2 => ['id' => '2', 'name' => _l('recevied_with_comments')],
         3 => ['id' => '3', 'name' => _l('bill_verification_in_process')],
         4 => ['id' => '4', 'name' => _l('bill_verification_on_hold')],
         5 => ['id' => '5', 'name' => _l('Pending')],
      ];
      echo render_select('aging_status[]', $statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
   </div>
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
         </div>
      </div>
      <div class="clearfix"></div>
   </div>

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
      <tfoot>
         <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"></td>
            <td></td>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>