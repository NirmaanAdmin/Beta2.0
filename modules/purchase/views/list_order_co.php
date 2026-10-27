<table class="table dt-table">
   <thead>
      <th><?php echo _l('change_order'); ?></th>
      <th><?php echo _l('date'); ?></th>
      <th><?php echo _l('change_order_value'); ?></th>
      <th><?php echo _l('updated_subtotal_before_tax'); ?></th>
      <th><?php echo _l('total'); ?></th>
      <th><?php echo _l('non_tender_items_in_change_order'); ?></th>
   </thead>
   <tbody>
      <?php foreach ($changes as $change) {
         $co_currency_name = get_currency_name($change['currency']);
         ?>
         <tr>
            <td><?php echo '<a href="' . admin_url('changee/changee_order#' . $change['id']) . '" target="_blank"><p>' . $change['pur_order_number'] . '</p></a>' ?></td>
            <td><?php echo date('d M Y', strtotime($change['datecreated'])) ?></td>
            <td><?php echo app_format_money($change['co_value'], $co_currency_name); ?></td>
            <td><?php echo app_format_money($change['contract_value_incl_co'], $co_currency_name); ?></td>
            <td><?php echo app_format_money($change['co_order_detail_total'], $co_currency_name); ?></td>
            <td><?php echo app_format_money($change['non_tender_total'], $co_currency_name); ?></td>
         </tr>
      <?php } ?>
   </tbody>
</table>