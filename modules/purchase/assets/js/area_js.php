<script>
  var area_value = {};

  function new_area() {
    "use strict";
    $('#area_model').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('input[name="area_id"]').val('');
  }

  function edit_area(invoker, id) {
    "use strict";
    appValidateForm($('#add_area'),{area_name:'required', project:'required'});
    var name = $(invoker).data('name');
    var project_id = $(invoker).data('project');
    $('input[name="area_id"]').val(id);
    $('input[name="area_name"]').val(name);
    $('select[name="project"]').val(project_id).selectpicker('refresh');
    $('#area_model').modal('show');
    $('#area_model .add-title').addClass('hide');
    $('#area_model .edit-title').removeClass('hide');
  }

  appValidateForm($('#add_area'),{area_name:'required', project:'required'});

  var area_table;
  area_table = $('.area-table');
  var Params = {
    "project": "[name='select_project']"
  };
  initDataTable('.area-table', admin_url + 'purchase/table_pur_area', [], [0], Params, [1, 'desc']);
  $('select[name="select_project"]').on('change', function () {
    area_table.DataTable().ajax.reload();
  });

  function uploadpurchaseareafilecsv() {
    "use strict";
    var fileInput = $('#file_csv')[0];
    var file = fileInput.files[0];
    var project = $('#select_project').val();

    if (!file || file.name.split('.').pop().toLowerCase() !== 'xlsx') {
      alert_float('warning', "<?php echo _l('_please_select_a_file') ?>");
      return false;
    }
    if(!project) {
      alert_float('warning', "Please select the project from above filter");
      return false;
    }
    var formData = new FormData();
    formData.append("file_csv", file);
    formData.append("project", project);

    if (<?php echo  pur_check_csrf_protection(); ?>) {
      formData.append(csrfData.token_name, csrfData.hash);
    }

    $.ajax({
      url: admin_url + 'purchase/import_file_xlsx_purchase_area',
      method: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
          response = JSON.parse(response);
          $('#file_csv').val(null);
          $('#file_upload_response').empty();

          $('#file_upload_response').append(
              `<h4><?php echo _l("_Result") ?></h4>
              <h5><?php echo _l('import_line_number') ?>: ${response.total_rows}</h5>
              <h5><?php echo _l('import_line_number_success') ?>: ${response.total_row_success}</h5>
              <h5><?php echo _l('import_line_number_failed') ?>: ${response.total_row_false}</h5>`
          );
          if (response.total_row_false > 0 || response.total_rows_data_error > 0) {
              $('#file_upload_response').append(
                `<a href="${site_url + response.filename}" class="btn btn-warning"><?php echo _l('download_file_error') ?></a>`
              );
          }
          if (response.total_rows < 1) {
            alert_float('warning', response.message);
          }
      },
      error: function() {
        alert_float('danger', 'Error uploading file. Please try again.');
      }
    });
    return false;
  }

  function bulk_area_delete() {
   "use strict";
   var print_id = '';
   var rows = $('.area-table').find('tbody tr');
   $.each(rows, function() {
     var checkbox = $($(this).find('td').eq(0)).find('input');
     if (checkbox.prop('checked') === true) {
         if (print_id !== '') {
             print_id += ','; // Append a comma before adding the next value
         }
         print_id += checkbox.val();
     }
   });
   if (print_id !== '') {
     if (!confirm('Are you sure you want to delete the selected records?')) {
      return false;
     }
     $.post(admin_url + 'purchase/bulk_area_delete', {
       ids: print_id,
     }).done(function (response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
        } else {
          alert_float('danger', response.message);
        }
        location.reload();
     });
   } else {
     alert_float('danger', 'Please select at least one item from the list');
   }
  }

</script>