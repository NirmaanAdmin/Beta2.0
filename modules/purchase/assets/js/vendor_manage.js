(function($) {
	"use strict";  
	var table_vendor = $('.table-vendors');

    var VendorsServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          VendorsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       VendorsServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

	var tAPI = initDataTable('.table-vendors', admin_url+'purchase/table_vendor',[0], [0],VendorsServerParams,  [1, 'desc']);
	$('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });

	get_vendors_dashboard();

})(jQuery);

function staff_bulk_actions(){
	"use strict";
	$('#table_vendors_list_bulk_actions').modal('show');
}

function purchase_delete_bulk_action(event) {
	"use strict";

	if (confirm_delete()) {
		var mass_delete = $('#mass_delete').prop('checked');

		if(mass_delete == true){
			var ids = [];
			var data = {};

			data.mass_delete = true;
			data.rel_type = 'vendors';

			var rows = $('.table-vendors').find('tbody tr');
			$.each(rows, function() {
				var checkbox = $($(this).find('td').eq(0)).find('input');
				if (checkbox.prop('checked') === true) {
					ids.push(checkbox.val());
				}
			});

			data.ids = ids;
			$(event).addClass('disabled');
			setTimeout(function() {
				$.post(admin_url + 'purchase/purchase_delete_bulk_action', data).done(function() {
					window.location.reload();
				}).fail(function(data) {
					$('#table_vendors_list_bulk_actions').modal('hide');
					alert_float('danger', data.responseText);
				});
			}, 200);
		}else{
			window.location.reload();
		}

	}
}

function get_vendors_dashboard() {
  "use strict";

  var data = {}

  $.post(admin_url + 'purchase/get_vendors_charts', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_vendors').text(response.total_vendors);
    $('.total_active').text(response.total_active);
    $('.total_inactive').text(response.total_inactive);
    $('.onboarded_this_week').text(response.onboarded_this_week);

    // Top 10 Vendors by State
    var stateBarCtx = document.getElementById('barChartState').getContext('2d');
    var stateLabels = response.bar_state_name;
    var stateData = response.bar_state_value;

    if (window.barTopPOStatus) {
      barTopPOStatus.data.labels = stateLabels;
      barTopPOStatus.data.datasets[0].data = stateData;
      barTopPOStatus.update();
    } else {
      window.barTopPOStatus = new Chart(stateBarCtx, {
        type: 'bar',
        data: {
          labels: stateLabels,
          datasets: [{
            label: 'Value',
            data: stateData,
            backgroundColor: '#00008B',
            borderColor: '#00008B',
            borderWidth: 1
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Value'
              }
            },
            y: {
              ticks: {
                autoSkip: false
              },
              title: {
                display: true,
                text: 'States'
              }
            }
          }
        }
      });
   }

   // Top 10 Vendors by Category
    var categoryBarCtx = document.getElementById('barChartCategory').getContext('2d');
    var categoryLabels = response.bar_category_name;
    var categoryData = response.bar_category_value;

    if (window.barTopPOCategory) {
      barTopPOCategory.data.labels = categoryLabels;
      barTopPOCategory.data.datasets[0].data = categoryData;
      barTopPOCategory.update();
    } else {
        window.barTopPOCategory = new Chart(categoryBarCtx, {
          type: 'bar',
          data: {
              labels: categoryLabels,
              datasets: [{
                  label: 'Value',
                  data: categoryData,
                  backgroundColor: '#1E90FF',
                  borderColor: '#1E90FF',
                  borderWidth: 1
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  legend: {
                      display: false
                  }
              },
              scales: {
                  x: {
                      ticks: {
                          autoSkip: false
                      },
                      title: {
                          display: true,
                          text: 'Category'
                      }
                  },
                  y: {
                      beginAtZero: true,
                      title: {
                          display: true,
                          text: 'Value'
                      }
                  }
              }
          }
      });
    }

   vendors_missing_info();

  });
}

var fnServerParams;

function vendors_missing_info() {
  "use strict";
  var table_missing_info = $('.table-missing-info');
  if ($.fn.DataTable.isDataTable('.table-missing-info')) {
    $('.table-missing-info').DataTable().destroy();
  }
  initDataTable('.table-missing-info', admin_url + 'purchase/vendors_missing_info', false, false, fnServerParams, [0, 'asc'], true);
  $.each(fnServerParams, function(i, obj) {
    $('select' + obj).on('change', function() {
      table_missing_info.DataTable().ajax.reload();
    });
  });
}