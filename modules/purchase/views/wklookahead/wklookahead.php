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

                <div class="row">
                  <div class="_buttons col-md-3">

                    <a href="<?php echo admin_url('purchase/add_update_wklookahead'); ?>" class="btn btn-info pull-left mright10 display-block">
                      <?php echo _l('New'); ?>
                    </a>

                  </div>
                </div>
<br>

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




    });
  </script>

  </body>

  </html>