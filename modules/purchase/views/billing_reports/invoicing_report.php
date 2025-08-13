<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_invoicing_report" class="hide">
   <div class="col-md-3 form-group" style="padding-left: 0px;">
      <?php echo get_projects_list('invoicing_project', ''); ?>
   </div>
   <div class="col-md-3 form-group">
      <?php
      $statuses = [
         1 => ['id' => 'Unpaid', 'name' => _l('unpaid')],
         2 => ['id' => 'Partial', 'name' => _l('Partial')],
         3 => ['id' => 'Paid', 'name' => _l('Paid')],
      ];
      echo render_select('invoicing_status[]', $statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
   </div>
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
         </div>
      </div>
      <div class="clearfix"></div>
   </div>

   <table class="table table-invoicing-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Project Name'); ?></th>
            <th><?php echo _l('Amount'); ?></th>
            <th><?php echo _l('Paid Amount'); ?></th>
            <th><?php echo _l('Status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <tr>
            <td></td>
            <td class="total_amount"></td>
            <td class="total_paid"></td>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>