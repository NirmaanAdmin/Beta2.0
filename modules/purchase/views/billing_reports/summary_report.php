<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_summary_report" class="hide">
   <div class="col-md-3 form-group" style="padding-left: 0px;">
      <?php echo get_projects_list('summary_project', ''); ?>
   </div>
   <div class="col-md-3 form-group">
      <select name="summary_vendor[]" id="summary_vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
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
         1 => ['id' => '1', 'name' => _l('unpaid')],
         2 => ['id' => '2', 'name' => _l('Partial')],
         3 => ['id' => '3', 'name' => _l('Paid')],
      ];
      echo render_select('summary_status[]', $statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
   </div>
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
         </div>
      </div>
      <div class="clearfix"></div>
   </div>

   <table class="table table-summary-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Vendor Name'); ?></th>
            <th><?php echo _l('Total Billed'); ?></th>
            <th><?php echo _l('Total Paid'); ?></th>
            <th><?php echo _l('Balance'); ?></th>
            <th><?php echo _l('Paid'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <tr>
            <td></td>
            <td class="total_billed"></td>
            <td class="total_paid"></td>
            <td class="total_balance"></td>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>