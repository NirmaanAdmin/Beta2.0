<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_client_aging_report" class="hide">
   <div class="col-md-3 form-group" style="padding-left: 0px;">
      <?php echo get_projects_list('client_aging_project', ''); ?>
   </div>
   <div class="col-md-3 form-group">
      <?php
      $statuses = [
         1 => ['id' => '1', 'name' => _l('invoice_status_unpaid')],
         2 => ['id' => '2', 'name' => _l('invoice_status_paid')],
         3 => ['id' => '3', 'name' => _l('invoice_status_not_paid_completely')],
         4 => ['id' => '4', 'name' => _l('invoice_status_overdue')],
         5 => ['id' => '5', 'name' => _l('invoice_status_cancelled')],
         6 => ['id' => '6', 'name' => _l('invoice_status_draft')],
      ];
      echo render_select('client_aging_status[]', $statuses, array('id', 'name'), '', [], array('data-width' => '100%', 'data-none-selected-text' => _l('status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
      ?>
   </div>
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
         </div>
      </div>
      <div class="clearfix"></div>
   </div>

   <table class="table table-client-aging-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Invoice No'); ?></th>
            <th><?php echo _l('Project Name'); ?></th>
            <th><?php echo _l('Invoice Date'); ?></th>
            <th><?php echo _l('Amount Due'); ?></th>
            <th><?php echo _l('Days Outstanding'); ?></th>
            <th><?php echo _l('Status'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount_due"></td>
            <td></td>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>