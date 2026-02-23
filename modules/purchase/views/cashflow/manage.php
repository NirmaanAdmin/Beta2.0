<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <table class="table dt-table border">
                     <thead>
                        <th><?php echo _l('Timeline'); ?></th>
                        <th><?php echo _l('Cumulative Cashflow (%)'); ?></th>
                        <th><?php echo _l('Months'); ?></th>
                        <th><?php echo _l('Month'); ?></th>
                        <th><?php echo _l('Monthly Cashflow ('.$base_currency->name.')'); ?></th>
                        <th><?php echo _l('Cumulative Cashflow ('.$base_currency->name.')'); ?></th>
                     </thead>
                     <tbody>
                        <?php
                        if(!empty($cashflow_data)) {
                           foreach ($cashflow_data as $key => $value) { ?>
                              <tr>
                                 <td><?php echo $value['timeline']; ?>%</td>
                                 <td><?php echo $value['cumulative_cashflow']; ?>%</td>
                                 <td><?php echo $value['months_cal']; ?></td>
                                 <td><?php echo $value['months_cal_name']; ?></td>
                                 <td><?php echo app_format_money($value['monthly_cashflow_value'], $base_currency->symbol); ?></td>
                                 <td><?php echo app_format_money($value['cumulative_cashflow_value'], $base_currency->symbol); ?></td>
                              </tr>
                           <?php }
                        } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php init_tail(); ?>
</body>
</html>