<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$custom_fields = get_custom_fields(
    'credit_note',
    array('show_on_table' => 1)
);
?>
<div class="row">
  <a onclick="bulk_credit_notes_delete(); return false;" data-table=".table-credit-notes" class=" hide bulk-actions-btn table-btn">Bulk Delete</a>
</div>
<table class="table table-striped table-credit-notes" id="<?php echo $table_id ?? 'credit_notes'; ?>">
    <thead>
        <tr>
            <th>
                <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="credit-notes"><label></label></div>
            </th>
            <th><?php echo _l('credit_note_number'); ?></th>
            <th><?php echo _l('credit_note_date'); ?></th>
            <?php if (!isset($client)) { ?>
                <th><?php echo _l('client'); ?></th>
            <?php } else { ?>
                <th class="not_visible">
                    <?php echo _l('client'); ?>
                </th>
            <?php } ?>
            <th><?php echo _l('credit_note_status'); ?></th>
            <?php if (!isset($project)) { ?>
                <th><?php echo _l('project'); ?></th>
            <?php } else { ?>
                <th class="not_visible">
                    <?php echo _l('project'); ?>
                </th>
            <?php } ?>
            <th><?php echo _l('reference_no'); ?></th>
            <th><?php echo _l('credit_note_amount'); ?></th>
            <th><?php echo _l('credit_note_remaining_credits'); ?></th>
            <?php foreach ($custom_fields as $field) { ?>
                <th data-type="<?php echo htmlspecialchars($field['type']); ?>" data-custom-field="1"
                >
                    <?php echo $field['name']; ?>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody></tbody>
</table>