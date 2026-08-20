<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>

</style>
<div id="wrapper">
  <div class="content">
    <div class="row">

      <div class="row">
        <div class="col-md-12" id="small-table">
          <div class="panel_s">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-12">
                  <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('3 WK Lookahead'); ?></h4>
                  <hr />
                </div>


                <br>
              </div>
              <div class="horizontal-scrollable-tabs preview-tabs-top">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                      <a href="#tab_lookahead" aria-controls="tab_lookahead" role="tab" data-toggle="tab">
                        <?php echo _l('Lookahead'); ?>
                      </a>
                    </li>
                    <li role="presentation">
                      <a href="#tab_calendar" aria-controls="tab_calendar" role="tab" data-toggle="tab">
                        <?php echo _l('Calendar'); ?>
                      </a>
                    </li>
                    <li role="presentation">
                      <a href="#tab_list" aria-controls="tab_list" role="tab" data-toggle="tab">
                        <?php echo _l('List'); ?>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_lookahead">
                  <div class="row">
                    <div class="_buttons col-md-3">

                      <a href="<?php echo admin_url('purchase/add_update_wklookahead'); ?>" class="btn btn-info pull-left mright10 display-block">
                        <?php echo _l('New'); ?>
                      </a>

                    </div>
                    <br>

                    <hr>
                    <table class="dt-table-loading table table-table_wklookahead">
                      <thead>
                        <tr>
                          <th><?php echo _l('Date'); ?></th>
                          <th class="text-right"><?php echo _l('Option'); ?></th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>

                  </div>
                </div>
                <div role="tabpanel" class="tab-pane ptop10 " id="tab_calendar">
                </div>
                <div role="tabpanel" class="tab-pane ptop10 " id="tab_list">
                  <table class="dt-table-loading table table-table_wklookahead_list">
                    <thead>
                      <tr>
                        <th><?php echo _l('Activity'); ?></th>
                        <th><?php echo _l('Vendor'); ?></th>
                        <th><?php echo _l('Start Date'); ?></th>
                        <th><?php echo _l('Due Date'); ?></th>
                        <th><?php echo _l('Complete'); ?></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php init_tail(); ?>
  <script>
    $(document).ready(function() {
      var table_wklookahead = $('.table-table_wklookahead');
      var Params = {

      };
      initDataTable(table_wklookahead, admin_url + 'purchase/table_wklookahead', [], [], Params, [0, 'desc']);
      $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {
          table_wklookahead.DataTable().ajax.reload();
        });
      });


      var table_wklookahead_list = $('.table-table_wklookahead_list');
      var Paramslist = {

      };
      initDataTable(table_wklookahead_list, admin_url + 'purchase/table_wklookahead_list', [], [], Paramslist, [0, 'desc']);
      $.each(Paramslist, function(i, obj) {
        $('select' + obj).on('change', function() {
          table_wklookahead_list.DataTable().ajax.reload();
        });
      });




    });
  </script>

  </body>

  </html>