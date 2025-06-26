<script>
  var height_window = $(window).height();
  var data_lack = <?php echo json_encode($data_lack); ?>;
  var dataObject = <?php echo json_encode($staff_row_tk); ?>;
  var dataCol = <?php echo html_entity_decode($set_col_tk); ?>;
  var dataHeader = <?php echo html_entity_decode($day_by_month_tk); ?>;
  var dataCellBackground = <?php echo json_encode($cell_background); ?>;
  var height_window = $(window).height();
  var show_popup_when_cell_click = false;
  (function() {
    "use strict";


    var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
    var hotSettings = {
      data: dataObject,
      columns: dataCol,
      licenseKey: 'non-commercial-and-evaluation',
      height: height_window - 200,
      stretchH: 'all',
      autoWrapRow: true,
      headerTooltips: true,
      minHeight: '100%',
      maxHeight: '500px',
      rowHeaders: true,
      cells: function(row, col, prop) {
        var cellProperties = {};
        if (col > 1) {
          cellProperties.renderer = firstRowRenderer;
          cellProperties.className = 'htCenter htMiddle';
        }
        return cellProperties;
      },

      width: '100%',
      rowHeights: 25,
      height: height_window - 280,
      rowHeaders: true,
      colHeaders: dataHeader,
      columnSorting: {
        indicator: true
      },
      dropdownMenu: true,
      mergeCells: true,
      fixedColumnsLeft: 2,
      contextMenu: true,
      multiColumnSorting: {
        indicator: true
      },
      hiddenColumns: {
        columns: [0],
        indicators: true
      },
      filters: true,
      afterSelection: function(r, c) {
        var data = {};
        data.value = this.getValue();
        data.ColHeader = this.getColHeader(c);
        data.staffid = hot.getDataAtCell(r, 0);
        if (c > 1) {
          show_detail_timesheets(data);
        }
      }
    };
    var hot = new Handsontable(hotElement, hotSettings);

    appValidateForm($('#import-timesheets-form'), {
      file_timesheets: 'required',
    })

    $('.timesheets_filter').on('click', function() {
      var data = {};
      data.month = $("#month_timesheets").val();
      data.staff = $('select[name="staff_timesheets[]"]').val();
      data.department = $('#department_timesheets').val();
      data.job_position = $('#job_position_timesheets').val();
      $('#loader-container').removeClass('hide');
      $.post(admin_url + 'timesheets/reload_timesheets_byfilter', data).done(function(response) {
        $('#loader-container').addClass('hide');
        response = JSON.parse(response);
        dataObject = response.arr;
        dataCol = response.set_col_tk;
        dataHeader = response.day_by_month_tk;
        data_lack = response.data_lack;
        dataCellBackground = response.cell_background;
        hot.updateSettings({
          data: dataObject,
          columns: dataCol,
          colHeaders: dataHeader,
          cells: function(row, col, prop) {
            var cellProperties = {};
            if (col > 1) {
              cellProperties.renderer = firstRowRenderer;
              cellProperties.className = 'htCenter htMiddle';
            }
            return cellProperties;
          }
        })

        const month = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        var d = new Date();
        var current_month = d.getMonth();
        var current_year = d.getFullYear();

        var current_date = current_year + '-' + month[current_month]

        if (data.month === current_date) {
          show_popup_when_cell_click = false;

          var current_date_index = parseInt(d.getDate()) + 1;
          hot.selectColumns(current_date_index);

          setTimeout(function() {
            show_popup_when_cell_click = true;
          }, 800);

        }
        if (response.notes == null || response.notes == undefined) {
          $('#note').val('');
        } else {
          $('#note').val(response.notes);
        }

        $('input[name="month"]').val(response.month);
        if (response.check_latch_timesheet) {
          $('#btn_unlatch').removeClass('hide');
          $('#btn_latch').addClass('hide');
          $('.edit_timesheets').addClass('hide');
          $('.exit_edit_timesheets').addClass('hide');
          $('.save_time_sheet').addClass('hide');
        } else {
          $('#btn_latch').removeClass('hide');
          $('#btn_unlatch').addClass('hide');
          $('.edit_timesheets').removeClass('hide');
          $('.exit_edit_timesheets').addClass('hide');
          $('.save_time_sheet').addClass('hide');
        }
      });
    });
    $('.save_time_sheet').on('click', function() {
      $('input[name="time_sheet"]').val(JSON.stringify(hot.getData()));
    });

    $('.latch_time_sheet').on('click', function() {
      $('input[name="latch"]').val(1);
      $('input[name="time_sheet"]').val(JSON.stringify(hot.getData()));
    });

    $('.unlatch_time_sheet').on('click', function() {
      $('input[name="unlatch"]').val(1);
      $('input[name="month"]').val($("#month_timesheets").val());
    });

    $('.edit_timesheets').on('click', function() {
      $('input[name="is_edit"]').val(1);
      $('.latch_time_sheet').addClass('hide');
      $('.edit_timesheets').addClass('hide');
      $('.exit_edit_timesheets').removeClass('hide');
      $('.save_time_sheet').removeClass('hide');
    });

    $('.exit_edit_timesheets').on('click', function() {
      $('input[name="is_edit"]').val(0);
      $('.latch_time_sheet').removeClass('hide');
      $('.edit_timesheets').removeClass('hide');
      $('.exit_edit_timesheets').addClass('hide');
      $('.save_time_sheet').addClass('hide');
    });
    $('.export_excel').click(function() {
      var department = $('select[name="department_timesheets"]').val();
      var role = $('select[name="job_position_timesheets"]').val();
      var staff = $('select[name="staff_timesheets[]"]').val();

      if (typeof department != 'undefined' && typeof department != undefined) {
        department = '';
      }
      if (typeof role != 'undefined' && typeof role != undefined) {
        role = '';
      }
      if (typeof staff != 'undefined' && typeof staff != undefined) {
        staff = '';
      }

      var data = {};
      data.month = $('input[name="month_timesheets"]').val();
      data.department = department;
      data.role = role;
      data.staff = staff;



      $.post(admin_url + 'timesheets/export_attendance_excel', data).done(function(response) {
        response = JSON.parse(response);
        window.location.href = response.site_url + response.filename;
      });
    });
    $(window).load(function() {
      var d = new Date();
      var month = new Array();
      month[0] = "01";
      month[1] = "02";
      month[2] = "03";
      month[3] = "04";
      month[4] = "05";
      month[5] = "06";
      month[6] = "07";
      month[7] = "08";
      month[8] = "09";
      month[9] = "10";
      month[10] = "11";
      month[11] = "12";
      $('#month_timesheets').val(d.getFullYear() + '-' + month[d.getMonth()]);

      var current_date_index = parseInt(d.getDate()) + 1;
      hot.selectColumns(current_date_index);

      setTimeout(function() {
        show_popup_when_cell_click = true;
      }, 800);
    });
    // Custom Scroll Slider Logic
    var slider = document.getElementById('scroll-slider');
    var thumb = document.getElementById('scroll-thumb');
    var sliderWidth = slider.clientWidth;
    var thumbWidth = thumb.clientWidth;

    const updateThumbPosition = () => {
      var scrollLeft = hot.getInstance().view.wt.wtOverlays.leftOverlay.clone.wtTable.holder.scrollLeft;
      var scrollableWidth = hot.getInstance().view.wt.wtTable.holder.scrollWidth - hot.getInstance().view.wt.wtTable.holder.clientWidth;
      // var position = (scrollLeft / scrollableWidth) * (sliderWidth - thumbWidth);
      // thumb.style.left = position + 'px';
    };
    // Debounced function to handle smoother updates
    const debounce = (func, delay) => {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), delay);
      };
    };
    const debouncedUpdateThumbPosition = debounce(updateThumbPosition, 50); // Adjust delay as needed

    const updateTableScroll = (event) => {
      var rect = slider.getBoundingClientRect();
      var x = event.clientX - rect.left;
      x = Math.max(0, Math.min(x, sliderWidth - thumbWidth)); // Clamp within bounds
      thumb.style.left = x + 'px';
      var scrollableWidth = hot.getInstance().view.wt.wtTable.holder.scrollWidth - hot.getInstance().view.wt.wtTable.holder.clientWidth;
      var scrollPosition = (x / (sliderWidth - thumbWidth)) * scrollableWidth;
      hot.getInstance().view.wt.wtTable.holder.scrollLeft = scrollPosition;
    };

    // Sync the thumb on table scroll
    hot.addHook('afterScrollHorizontally', debouncedUpdateThumbPosition);

    // Add event listeners for slider
    slider.addEventListener('mousedown', (e) => {
      const moveHandler = (event) => updateTableScroll(event);
      const upHandler = () => {
        document.removeEventListener('mousemove', moveHandler);
        document.removeEventListener('mouseup', upHandler);
      };
      document.addEventListener('mousemove', moveHandler);
      document.addEventListener('mouseup', upHandler);
    });

    // Initialize the thumb position
    updateThumbPosition();
  })(jQuery);


  function firstRowRenderer(instance, td, row, col, prop, value, cellProperties) {
    "use strict";
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    if (dataCellBackground != null) {
      td.style.background = dataCellBackground[row][prop];
    } else {
      td.style.background = '#fff';
    }
  }

  function firstRowRenderer_2(instance, td, row, col, prop, value, cellProperties) {
    "use strict";
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    td.style.fontWeight = 'bold';

  }

  function show_detail_timesheets(data) {
    "use strict";
    if (show_popup_when_cell_click) {
      if ($('input[name="is_edit"]').val() == 0) {
        var month = $("#month_timesheets").val();
        if (typeof month == 'undefined') {
          month = $('input[name="current_month"]').val();
        }
        data.month = month;
        $.post(admin_url + 'timesheets/show_detail_timesheets', data).done(function(response) {
          response = JSON.parse(response);
          $('#title_detail').html(response.title);
          $('#ul_timesheets_detail_modal').html('');
          $('#ul_timesheets_detail_modal').append(response.html);
          $('#timesheets_detail_modal').modal('show');
        });
      }
    }
  }

  function import_timesheets() {
    "use strict";
    $('#timesheets_detail_modal').modal('show');
  }
</script>