<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
  <a onclick="bulk_debit_notes_delete(); return false;" data-table=".table-debit-notes" class=" hide bulk-actions-btn table-btn">Bulk Delete</a>
</div>
<table class="table table-striped table-debit-notes" id="table-debit-notes" data-last-order-identifier="debit-notes" data-default-order="<?php echo get_table_last_order('debit-notes'); ?>">
  <thead>
    <tr>
      <th>
        <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="debit-notes"><label></label></div>
      </th>
      <th><?php echo _l('debit_note_number'); ?></th>
      <th><?php echo _l('debit_note_date'); ?></th>
      <?php if (!isset($client)) { ?>
        <th><?php echo _l('vendor'); ?></th>
      <?php } else { ?>
        <th class="not_visible">
          <?php echo _l('vendor'); ?>
        </th>
      <?php } ?>
      <th><?php echo _l('debit_note_status'); ?></th>
      <th><?php echo _l('reference_no'); ?></th>
      <th><?php echo _l('debit_note_amount'); ?></th>
      <th><?php echo _l('debit_note_remaining_debits'); ?></th>
    </tr>
  </thead>
  <tbody></tbody>
</table>