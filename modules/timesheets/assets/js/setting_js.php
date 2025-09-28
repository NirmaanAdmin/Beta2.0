<script>
  <?php if ($group == 'manage_dayoff') { ?>
      (function() {
        "use strict";

        var table = '#holiday-data-table';
        initDataTable(table, admin_url + 'timesheets/table_holiday', false, false, [], [0, 'desc']);

        $('#time_start_work').datetimepicker({
          datepicker: false,
          format: 'H:i'
        });
        $('#time_end_work').datetimepicker({
          datepicker: false,
          format: 'H:i'
        });
        $('#start_lunch_break_time').datetimepicker({
          datepicker: false,
          format: 'H:i'
        });
        $('#end_lunch_break_time').datetimepicker({
          datepicker: false,
          format: 'H:i'
        });
        $('#late_latency_allowed').datetimepicker({
          datepicker: false,
          format: 'H:i'
        });

        $('.date-picker').datepicker({
          changeMonth: true,
          changeYear: true,
          showButtonPanel: true,
          dateFormat: 'mm/yy',
          onClose: function(dateText, inst) {
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
          }
        });
      })(jQuery);

    function new_leave() {
      "use strict";
      $('input[name="id"]').val();
      $('#leave_modal').modal('show');
      $('.edit-title').addClass('hide');
      $('.add-title').removeClass('hide');
      $('#leave_modal-form input[name="break_date"]').val('');
      $('#leave_modal-form textarea').val('');
      $('#leave_modal-form select').val('').change();
      $('input[name="repeat_by_year"]').prop('checked', false);
      appValidateForm($('#leave_modal-form'), {
        leave_reason: 'required',
        leave_type: 'required'
      });
      init_datepicker();
    }

    function edit_day_off(invoker, id) {
      "use strict";
      $('#leave_modal').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');
      $('input[name="id"]').val(id);
      $('textarea[name="leave_reason"]').val($(invoker).data('off_reason'));
      $('select[name="leave_type"]').val($(invoker).data('off_type'));
      $('select[name="leave_type"]').change();
      $('select[name="timekeeping"]').val($(invoker).data('timekeeping'));
      $('select[name="timekeeping"]').change();
      var list_id = [];
      var departmentid = $(invoker).data('department').toString();
      if (departmentid.search(',')) {
        var list_dpm = departmentid.split(",");
        jQuery.each(list_dpm, function(key, value) {
          list_id.push(value);
        });
      } else {
        list_id.push(departmentid);
      }
      $('select[name="department[]"]').val(list_id).change();
      $('select[name="position[]"]').val($(invoker).data('position')).change();
      $('input[name="break_date"]').val($(invoker).data('break_date'));
      if ($(invoker).data('repeat_by_year') == 1) {
        $('input[name="repeat_by_year"]').prop('checked', true);
      } else {
        $('input[name="repeat_by_year"]').prop('checked', false);
      }
      appValidateForm($('#leave_modal_update-form'), {
        leave_reason: 'required',
        leave_type: 'required'
      });
      init_datepicker();
    }
  <?php } ?>

  <?php if ($group == 'manage_leave') { ?>
    var data_array = <?php echo html_entity_decode($leave_of_the_year); ?>;
    var hotElement = document.querySelector('#example'),
      hot;
    (function() {
      "use strict";
      hot = new Handsontable(hotElement, hanson_table(data_array));
      $('.handle_year').click(function() {
        $('input[name="start_year_for_annual_leave_cycle"]').val($(this).data('year'));
        $('input[name="not_show_notify"]').val(1);
        $('#setting-leave-form').submit();
      });
    })(jQuery);

    // function hanson_table(obj){
    //   "use strict";
    //   var hotElementContainer = hotElement.parentNode;
    //   return {
    //     data: obj,
    //     columns: [
    //     {
    //       data: 'staffid',
    //       type: 'text',
    //       readOnly: true,
    //     },
    //     {
    //       data: 'staffcode',
    //       type: 'text',
    //       readOnly: true
    //     },
    //     {
    //       data: 'staff',
    //       type: 'text',
    //       readOnly: true
    //     },
    //     {
    //       data: 'department',
    //       type: 'text',
    //       readOnly: true
    //     },
    //     {
    //       data: 'role',
    //       type: 'text',
    //       readOnly: true      
    //     },
    //     {
    //       data: 'maximum_leave_of_the_year',
    //       type: 'numeric',
    //     },
    //     {
    //       data: 'number_of_leave_days_remaining',
    //       type: 'numeric',
    //       readOnly: true      
    //     }     
    //     ],
    //     contextMenu: true,
    //     manualRowMove: true,
    //     manualColumnMove: true,
    //     stretchH: 'all',
    //     autoWrapRow: true,
    //     rowHeights: 30,
    //     defaultRowHeight: 100,
    //     headerTooltips: true,
    //     maxRows: 22,
    //     minHeight:'100%',
    //     maxHeight:'500px',

    //     width: '100%',
    //     height: 330,

    //     rowHeaders: true,

    //     autoColumnSize: {
    //       samplingRatio: 23
    //     },

    //     filters: true,
    //     manualRowResize: true,
    //     manualColumnResize: true,
    //     allowInsertRow: true,
    //     allowRemoveRow: true,
    //     columnHeaderHeight: 40,

    //     licenseKey: 'non-commercial-and-evaluation',
    //     stretchH: 'all',
    //     width: '100%',
    //     autoWrapRow: true,
    //     rowHeights: 30,
    //     columnHeaderHeight: 40,
    //     minRows: 10,
    //     maxRows: <?php echo html_entity_decode($max_row); ?>,
    //     rowHeaders: true,
    //     colWidths: [80,200,200,200,200,200],
    //     colHeaders: [
    //     "<?php echo _l('staffid'); ?>",
    //     "Employee Code",
    //     "<?php echo _l('staff'); ?>",
    //     "<?php echo _l('department'); ?>",
    //     "<?php echo _l('role'); ?>",
    //     "<?php echo _l('maximum_leave_of_the_year'); ?>",
    //     "<?php echo _l('ts_number_of_leave_days_remaining'); ?>",
    //     ],
    //     columnSorting: {
    //       indicator: true
    //     },
    //     autoColumnSize: {
    //       samplingRatio: 23
    //     },
    //     dropdownMenu: true,
    //     mergeCells: true,
    //     contextMenu: true,
    //     manualRowMove: true,
    //     manualColumnMove: true,
    //     multiColumnSorting: {
    //       indicator: true
    //     },
    //     filters: true,
    //     manualRowResize: true,
    //     manualColumnResize: true
    //   };
    // }

    function hanson_table(obj) {
      "use strict";

      // exact widths for 7 columns (match your columns[] count)
      const colWidths = [80, 140, 200, 180, 80, 220, 180];

      return {
        data: obj,

        // define width on each column to be explicit
        columns: [{
            data: 'staffid',
            type: 'text',
            readOnly: true,
            width: colWidths[0]
          },
          {
            data: 'staffcode',
            type: 'text',
            readOnly: true,
            width: colWidths[1]
          },
          {
            data: 'staff',
            type: 'text',
            readOnly: true,
            width: colWidths[2]
          },
          {
            data: 'department',
            type: 'text',
            readOnly: true,
            width: colWidths[3]
          },
          {
            data: 'role',
            type: 'text',
            readOnly: true,
            width: colWidths[4]
          },
          {
            data: 'maximum_leave_of_the_year',
            type: 'numeric',
            width: colWidths[5]
          },
          {
            data: 'number_of_leave_days_remaining',
            type: 'numeric',
            readOnly: true,
            width: colWidths[6]
          }
        ],

        // keep for completeness; column.width above takes precedence
        colWidths: colWidths,

        // IMPORTANT: turn off autosize & stretching to respect fixed widths
        autoColumnSize: false,
        stretchH: 'none',

        // usual UI options (deduped)
        rowHeaders: true,
        colHeaders: [
          "<?php echo _l('staffid'); ?>",
          "Employee Code",
          "<?php echo _l('staff'); ?>",
          "<?php echo _l('department'); ?>",
          "<?php echo _l('role'); ?>",
          "<?php echo _l('maximum_leave_of_the_year'); ?>",
          "<?php echo _l('ts_number_of_leave_days_remaining'); ?>",
        ],
        columnHeaderHeight: 40,
        rowHeights: 30,
        height: 330,
        maxRows: <?php echo html_entity_decode($max_row); ?>,
        minRows: 10,

        manualColumnResize: true,
        manualRowResize: true,
        manualColumnMove: true,
        manualRowMove: true,
        columnSorting: {
          indicator: true
        },
        multiColumnSorting: {
          indicator: true
        },
        filters: true,
        dropdownMenu: true,
        contextMenu: true,

        licenseKey: 'non-commercial-and-evaluation'
      };
    }


    function get_data_hanson() {
      "use strict";
      $('input[name="leave_of_the_year_data"]').val(JSON.stringify(hot.getData()));
    }

    function filter_hanson() {
      "use strict";
      var type_of_leave = $('select[name="type_of_leave"]').val();
      var staffid = $('select[name="leave_filter_staff[]"]').val();
      var departmentid = $('select[name="leave_filter_department[]"]').val();
      var roleid = $('select[name="leave_filter_roles[]"]').val();
      var year = $('select[name="start_year_for_annual_leave_cycle"]').val();

      var data = {};
      data.type_of_leave = type_of_leave;
      data.staffid = staffid;
      data.departmentid = departmentid;
      data.roleid = roleid;
      data.year = year;
      $.post(admin_url + 'timesheets/get_leave_setting', data).done(function(response) {
        response = JSON.parse(response);
        hot = new Handsontable(hotElement, hanson_table(response.data));
      });
    }
  <?php } ?>



  <?php if ($group == 'valid_ip') { ?>
    var data_array = <?php echo html_entity_decode($list_ip_data); ?>;
    var hotElement = document.querySelector('#example'),
      hot;
    (function() {
      "use strict";
      hot = new Handsontable(hotElement, hanson_table(data_array));
    })(jQuery);

    function hanson_table(obj) {
      "use strict";
      var hotElementContainer = hotElement.parentNode;
      return {
        data: obj,
        columns: [{
          data: 'ip_address',
          type: 'text'
        }],
        contextMenu: true,
        manualRowMove: true,
        manualColumnMove: true,
        stretchH: 'all',
        autoWrapRow: true,
        rowHeights: 30,
        defaultRowHeight: 100,
        headerTooltips: true,
        minHeight: '100%',

        width: '100%',

        rowHeaders: true,

        autoColumnSize: {
          samplingRatio: 23
        },

        filters: true,
        manualRowResize: true,
        manualColumnResize: true,
        allowInsertRow: true,
        allowRemoveRow: true,
        columnHeaderHeight: 40,

        licenseKey: 'non-commercial-and-evaluation',
        stretchH: 'all',
        width: '100%',
        autoWrapRow: true,
        rowHeights: 30,
        columnHeaderHeight: 40,
        minRows: 10,
        rowHeaders: true,
        colWidths: [200],
        colHeaders: [
          "<?php echo _l('ts_list_of_valid_ip_address'); ?>"
        ],
        columnSorting: {
          indicator: true
        },
        autoColumnSize: {
          samplingRatio: 23
        },
        dropdownMenu: true,
        mergeCells: true,
        contextMenu: true,
        manualRowMove: true,
        manualColumnMove: true,
        multiColumnSorting: {
          indicator: true
        },
        filters: true,
        manualRowResize: true,
        manualColumnResize: true
      };
    }

    function get_data_hanson() {
      "use strict";
      $('input[name="list_ip_data"]').val(JSON.stringify(hot.getData()));
    }


  <?php } ?>
</script>