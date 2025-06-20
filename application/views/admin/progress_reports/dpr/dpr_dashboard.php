<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <div class="col-md-12">

          <div class="row">
             <div class="col-md-12">
                <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('daily_progress_report'); ?></h4>
                <hr />
             </div>
          </div>

          <div class="row">
             <div class="col-md-12">
                <div class="col-md-2 pull-right" style="padding-right: 0px;">
                  <?php
                  $default_project = !empty($projects) ? $projects[0]['id'] : '';
                  echo render_select('projects', $projects, array('id', 'name'), 'projects', $default_project);
                  ?>
                </div>
             </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="preport_sub_type_html">
              </div>
            </div>
          </div>

          <br><br>

          <div class="row">
            <div class="col-md-12">
              <div class="preport_type_html">
              </div>
            </div>
          </div>

          <br><br>

          <div class="row">
            <div class="col-md-12">
                <canvas id="totalWorkforceChart" height="120"></canvas>
            </div>
          </div>

          <br><br>

          <div class="row">
            <div class="col-md-12">
              <canvas id="stackedLaborChart" height="130"></canvas>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $('select[name="projects"]').on('change', function() {
    get_dpr_dashboard();
  });

  get_dpr_dashboard();

  function get_dpr_dashboard() {
    "use strict";
    var data = {
      projects: $('select[name="projects"]').val(),
    };
    $.post(admin_url + 'forms/get_dpr_dashboard', data).done(function(res) {
      var response = JSON.parse(res);
      $('.preport_sub_type_html').html(response.preport_sub_type_html);
      $('.preport_type_html').html(response.preport_type_html);

      // === Total Workforce Chart ===
      if (window.totalWorkforceChartInstance) {
        window.totalWorkforceChartInstance.destroy();
      }
      const ctx = document.getElementById('totalWorkforceChart').getContext('2d');
      const totalDatasets = response.total_workforce_values.map(function(ds, i, arr) {
        var hue = (i * 360 / arr.length) % 360;
        var bg = 'hsl(' + hue + ', 70%, 60%)';
        return {
          label: ds.label,
          data: ds.data,
          backgroundColor: bg,
          borderColor: bg,
          borderWidth: 1
        };
      });
      window.totalWorkforceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: response.total_workforce_labels,
          datasets: totalDatasets
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Total Workforce'
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      // === Stacked Labor Chart ===
      if (window.stackedLaborChartInstance) {
        window.stackedLaborChartInstance.destroy();
      }
      const stackedLaborCtx = document.getElementById('stackedLaborChart').getContext('2d');
      const stackedDatasets = Object.keys(response.stacked_labor_values).map(function(label, i, arr) {
        var hue = (i * 360 / arr.length) % 360;
        var bg = 'hsl(' + hue + ', 70%, 60%)';
        return {
          label: label,
          data: response.stacked_labor_values[label],
          backgroundColor: bg,
          borderWidth: 1
        };
      });
      window.stackedLaborChartInstance = new Chart(stackedLaborCtx, {
        type: 'bar',
        data: {
          labels: response.stacked_labor_labels,
          datasets: stackedDatasets
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Stacked Workforce by Category'
            },
            tooltip: {
              mode: 'index',
              intersect: false
            }
          },
          scales: {
            x: {
              stacked: true
            },
            y: {
              stacked: true,
              beginAtZero: true
            }
          }
        }
      });

    }).fail(function(xhr) {
      console.error("Error loading dashboard data:", xhr.responseText);
    });
  }
</script>

