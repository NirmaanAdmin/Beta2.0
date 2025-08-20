<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<?php
  $table_data = array(
      _l('creator'),
      _l('department'),
      _l('additional_day'),
      _l('timekeeping_type'),
      _l('timekeeping_value'),
      _l('comoff_value'),
      _l('approver'),
      _l('status'),       
      _l('options'),
      );
  render_datatable($table_data,'table_additional_timesheets');
  ?>

<div class="modal fade additional-timesheets-sidebar" id="additional_timesheets_modal" >
</div>
