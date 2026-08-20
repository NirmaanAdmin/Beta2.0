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

                  <div class="row mbot15">
                    <div class="col-md-12">
                      <button type="button"
                        class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#newActivityModal">
                        <i class="fa fa-plus"></i>
                        <?php echo _l('New Activity'); ?>
                      </button>
                    </div>
                  </div>

                  <div class="clearfix"></div>
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
  <div class="modal fade"
    id="newActivityModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="newActivityModalLabel">

    <div class="modal-dialog" role="document">

      <div class="modal-content">

        <?php echo form_open(
          admin_url('purchase/add_wklookahead_activity'),
          [
            'id' => 'newActivityForm'
          ]
        ); ?>

        <div class="modal-header">

          <button type="button"
            class="close"
            data-dismiss="modal">
            <span>&times;</span>
          </button>

          <h4 class="modal-title" id="newActivityModalLabel">
            <i class="fa fa-plus"></i>
            <?php echo _l('New Activity'); ?>
          </h4>

        </div>

        <div class="modal-body">

          <div class="form-group">

            <label for="activity">
              <?php echo _l('Activity'); ?>
            </label>

            <input type="text"
              name="activity"
              id="activity"
              class="form-control"
              required>

          </div>


          <div class="form-group">

            <label for="vendor_id">
              <?php echo _l('Vendor'); ?>
            </label>

            <select name="vendor_id"
              id="vendor_id"
              class="form-control selectpicker"
              data-live-search="true">

              <option value="">
                <?php echo _l('dropdown_non_selected_tex'); ?>
              </option>

              <?php
              $vendors = get_all_vendors();

              foreach ($vendors as $vendor) {
              ?>

                <option value="<?php echo $vendor['userid']; ?>">
                  <?php echo html_escape($vendor['company']); ?>
                </option>

              <?php
              }
              ?>

            </select>

          </div>


          <div class="row">

            <div class="col-md-6">

              <div class="form-group">

                <label for="start_date">
                  <?php echo _l('Start Date'); ?>
                </label>

                <input type="date"
                  name="start_date"
                  id="start_date"
                  class="form-control"
                  required>

              </div>

            </div>


            <div class="col-md-6">

              <div class="form-group">

                <label for="due_date">
                  <?php echo _l('Due Date'); ?>
                </label>

                <input type="date"
                  name="due_date"
                  id="due_date"
                  class="form-control">

              </div>

            </div>

          </div>


          <div class="form-group">

            <label for="percentage">
              <?php echo _l('Complete'); ?>
            </label>

            <div class="input-group">

              <input type="number"
                name="percentage"
                id="percentage"
                class="form-control"
                min="0"
                max="100"
                value="0">

              <span class="input-group-addon">
                %
              </span>

            </div>

          </div>


          <div id="activity_form_message"></div>

        </div>


        <div class="modal-footer">

          <button type="button"
            class="btn btn-default"
            data-dismiss="modal">
            <?php echo _l('close'); ?>
          </button>

          <button type="submit"
            class="btn btn-primary"
            id="saveActivityBtn">

            <i class="fa fa-save"></i>
            <?php echo _l('Save Activity'); ?>

          </button>

        </div>

        <?php echo form_close(); ?>

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

    $(document).ready(function() {

      $('#newActivityForm').on('submit', function(e) {

        e.preventDefault();

        var form = $(this);
        var button = $('#saveActivityBtn');

        button.prop('disabled', true);

        $.ajax({

          url: form.attr('action'),

          type: 'POST',

          data: form.serialize(),

          dataType: 'json',

          success: function(response) {

            if (response.success) {

              alert_float(
                'success',
                response.message
              );

              $('#newActivityModal').modal('hide');

              form[0].reset();

              $('.selectpicker').selectpicker('refresh');

              /*
               * Reload DataTable
               */
              $('.table-table_wklookahead_list')
                .DataTable()
                .ajax.reload(null, false);

            } else {

              alert_float(
                'danger',
                response.message
              );
            }
          },

          error: function(xhr) {

            alert_float(
              'danger',
              'Something went wrong while creating the activity.'
            );

            console.log(xhr.responseText);
          },

          complete: function() {

            button.prop('disabled', false);
          }

        });

      });

    });
  </script>

  </body>

  </html>