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
                  <div class="row">
                    <div class="col-md-12">
                      <div class="panel_s">
                        <div class="panel-body">
                          <div class="dt-loader hide"></div>
                          <div id="calendars"></div>
                        </div>
                      </div>
                    </div>
                  </div>
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
  <div class="modal fade" id="newActivityModal" tabindex="-1" role="dialog" aria-labelledby="newActivityModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <?php echo form_open(admin_url('purchase/add_wklookahead_activity'), [
          'id' => 'newActivityForm'
        ]); ?>

        <!-- Hidden field for edit mode -->
        <input type="hidden" name="activity_id" id="activity_id" value="">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title" id="newActivityModalLabel">
            <i class="fa fa-plus" id="modalIcon"></i>
            <span id="modalTitle"><?php echo _l('New Activity'); ?></span>
          </h4>
        </div>

        <div class="modal-body">
          <!-- Keep all your existing fields exactly the same -->
          <div class="form-group">
            <label for="activity"><?php echo _l('Activity'); ?></label>
            <input type="text" name="activity" id="activity" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="vendor_id"><?php echo _l('Vendor'); ?></label>
            <select name="vendor_id" id="vendor_id" class="form-control selectpicker" data-live-search="true">
              <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
              <?php
              $vendors = get_all_vendors();
              foreach ($vendors as $vendor) {
                echo '<option value="' . $vendor['userid'] . '">' . html_escape($vendor['company']) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="start_date"><?php echo _l('Start Date'); ?></label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="due_date"><?php echo _l('Due Date'); ?></label>
                <input type="date" name="due_date" id="due_date" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="percentage"><?php echo _l('Complete'); ?></label>
            <div class="input-group">
              <input type="number" name="percentage" id="percentage" class="form-control" min="0" max="100" value="0">
              <span class="input-group-addon">%</span>
            </div>
          </div>

          <div id="activity_form_message"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
          <button type="submit" class="btn btn-primary" id="saveActivityBtn">
            <i class="fa fa-save"></i>
            <span id="saveBtnText"><?php echo _l('Save Activity'); ?></span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

  <?php init_tail(); ?>
  <script>
    $(document).ready(function() {

      // ========== RESET MODAL WHEN OPENING FOR "NEW" ==========
      $('[data-target="#newActivityModal"]').not('.edit-activity').on('click', function() {
        resetActivityModal();
      });

      // ========== EDIT BUTTON CLICK ==========
      $(document).on('click', '.edit-activity', function(e) {
        e.preventDefault();

        var activityId = $(this).data('id');

        // Change modal to Edit mode
        $('#modalTitle').text('<?php echo _l("Edit Activity"); ?>');
        $('#modalIcon').removeClass('fa-plus').addClass('fa-pencil');
        $('#saveBtnText').text('<?php echo _l("Update Activity"); ?>');
        $('#activity_id').val(activityId);

        // Change form action to update
        $('#newActivityForm').attr('action', admin_url + 'purchase/update_wklookahead_activity');

        // Fetch data from database
        $.ajax({
          url: admin_url + 'purchase/get_wklookahead_activity/' + activityId,
          type: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var data = response.data;

              $('#activity').val(data.activity);
              $('#vendor_id').selectpicker('val', data.vendor_id);
              $('#start_date').val(data.start_date);
              $('#due_date').val(data.due_date);
              $('#percentage').val(data.percentage);

              // Open modal
              $('#newActivityModal').modal('show');
            } else {
              alert_float('danger', response.message || 'Failed to load activity');
            }
          },
          error: function() {
            alert_float('danger', 'Error loading activity data');
          }
        });
      });

      // ========== RESET FUNCTION ==========
      function resetActivityModal() {
        $('#modalTitle').text('<?php echo _l("New Activity"); ?>');
        $('#modalIcon').removeClass('fa-pencil').addClass('fa-plus');
        $('#saveBtnText').text('<?php echo _l("Save Activity"); ?>');
        $('#activity_id').val('');
        $('#newActivityForm')[0].reset();
        $('#vendor_id').selectpicker('val', '');
        $('#newActivityForm').attr('action', admin_url + 'purchase/add_wklookahead_activity');
        $('#activity_form_message').html('');
      }

      // Optional: also reset when modal is closed
      $('#newActivityModal').on('hidden.bs.modal', function() {
        resetActivityModal();
      });
    });
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

      // Initialize calendar when calendar tab is clicked
      $('a[href="#tab_calendar"]').on('shown.bs.tab', function(e) {
        setTimeout(function() {
          initializeCalendar();
        }, 200);
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
  <script>
    (function() {
      "use strict";

      var calendarInstance = null;

      // Function to initialize the calendar
      window.initializeCalendar = function() {
        var calendar_selector = $('#calendars');

        if (calendar_selector.length > 0) {
          // Destroy existing instance if any
          if (calendarInstance) {
            calendarInstance.destroy();
            calendarInstance = null;
          }

          // Check if FullCalendar is available
          if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar library not loaded');
            return;
          }

          var calendar_settings = {
            locale: app.locale,
            headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: false,
            dayMaxEventRows: parseInt(app.options.calendar_events_limit) + 1,
            direction: (isRTL == 'true' ? 'rtl' : 'ltr'),
            eventStartEditable: false,
            firstDay: parseInt(app.options.calendar_first_day),
            initialView: app.options.default_view_calendar,
            timeZone: app.options.timezone,
            loading: function(isLoading) {
              !isLoading ? $('.dt-loader').addClass('hide') : $('.dt-loader').removeClass('hide');
            },
            events: function(info, successCallback, failureCallback) {
              // Fetch events from your controller
              $.ajax({
                url: admin_url + 'purchase/get_calendar_events',
                type: 'GET',
                dataType: 'json',
                data: {
                  start: info.startStr,
                  end: info.endStr
                },
                success: function(data) {
                  successCallback(data);
                },
                error: function(xhr, status, error) {
                  console.error('Error fetching calendar events:', error);
                  failureCallback(error);
                }
              });
            }
          };

          try {
            calendarInstance = new FullCalendar.Calendar(calendar_selector[0], calendar_settings);
            calendarInstance.render();
            console.log('Calendar rendered successfully');
          } catch (error) {
            console.error('Error rendering calendar:', error);
          }
        }
      };

      // Initialize calendar when DOM is ready if calendar tab is active
      $(document).ready(function() {
        // Check if calendar tab is active by default
        if ($('#tab_calendar').hasClass('active')) {
          setTimeout(initializeCalendar, 300);
        }
      });

      // Also initialize when modal or other elements might affect visibility
      $(document).on('shown.bs.modal', function() {
        if ($('#tab_calendar').hasClass('active')) {
          setTimeout(initializeCalendar, 300);
        }
      });

      // Trigger calendar initialization when window is fully loaded
      $(window).on('load', function() {
        if ($('#tab_calendar').hasClass('active')) {
          setTimeout(initializeCalendar, 400);
        }
      });

    })(jQuery);

    // In your calendar initialization
    $('#calendar').fullCalendar({
      // ... other options
      eventClick: function(event) {
        // Open the edit modal with the event data
        $('#newActivityModal').modal('show');

        // Populate the modal fields
        $('#activity_id').val(event.activity_id);
        $('#activity').val(event.activity_name);
        $('#vendor_id').val(event.vendor_id).selectpicker('refresh');
        $('#due_date').val(event.due_date);
        $('#percentage').val(event.percentage);
        $('#start_date').val(event.start_date);
        $('#lookahead_id').val(event.lookahead_id);
      }
    });
  </script>
  </body>

  </html>