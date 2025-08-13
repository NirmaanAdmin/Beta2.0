<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<?php
  $table_data = array(
      _l('creator'),
      _l('department'),
      _l('Missed Punch Day'),
      _l('approver'),
      _l('status'),       
      _l('options'),
      );
  render_datatable($table_data,'table_missed_punch');
  ?>

<!-- <div class="modal fade additional-timesheets-sidebar" id="additional_timesheets_modal" >
</div> -->
