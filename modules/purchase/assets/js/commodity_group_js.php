<script>  

function new_commodity_group_type(){
  "use strict";
  $('#commodity_group_type').modal('show');
  $('.edit-title').addClass('hide');
  $('.add-title').removeClass('hide');
  $('input[name="commodity_group_type_id"]').val('');
  $('input[name="commodity_group_code"]').val('');
  $('input[name="name"]').val('');
}

function edit_commodity_group_type(invoker, id) {
  "use strict";
  appValidateForm($('#add_commodity_group_type'),{commodity_group_code:'required', name:'required'});
  var commodity_group_code = $(invoker).data('commodity_group_code');
  var name = $(invoker).data('name');
  $('input[name="commodity_group_type_id"]').val(id);
  $('input[name="commodity_group_code"]').val(commodity_group_code);
  $('input[name="name"]').val(name);
  $('#commodity_group_type').modal('show');
  $('#commodity_group_type .add-title').addClass('hide');
  $('#commodity_group_type .edit-title').removeClass('hide');
}

appValidateForm($('#add_commodity_group_type'),{commodity_group_code:'required', name:'required'});

var commodity_group_table;
commodity_group_table = $('.commodity-group-table');
var Params = {
  "project": "[name='select_project']"
};
initDataTable('.commodity-group-table', admin_url + 'purchase/table_pur_commodity_group', [], [0], Params, [1, 'asc']);
$('select[name="select_project"]').on('change', function () {
  commodity_group_table.DataTable().ajax.reload();
});

$(document).on('click', '.delete_commodity_group_type', function(e) {
   e.preventDefault();
   var url = $(this).attr('href');
   Swal.fire({
      title: 'Are you sure?',
      text: 'Are you sure you want to remove this budget head?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, remove it!',
      cancelButtonText: 'Cancel'
   }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
   });
});

$(document).on('click', '.active_commodity_group', function(e) {
   e.preventDefault();
   var url = $(this).attr('href');
   Swal.fire({
      title: 'Are you sure?',
      text: 'Do you want to activate this budget head?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, activate it!',
      cancelButtonText: 'Cancel'
   }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
   });
});

$(document).on('click', '.deactive_commodity_group', function(e) {
   e.preventDefault();
   var url = $(this).attr('href');
   Swal.fire({
      title: 'Are you sure?',
      text: 'Do you want to deactivate this budget head?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, deactivate it!',
      cancelButtonText: 'Cancel'
   }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
   });
});

function uploadpurchasecommoditygroupfilecsv() {
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
    url: admin_url + 'purchase/import_file_xlsx_purchase_commodity_group',
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
        commodity_group_table.DataTable().ajax.reload();
    },
    error: function() {
      alert_float('danger', 'Error uploading file. Please try again.');
    }
  });
  return false;
}

function bulk_commodity_group_delete() {
 "use strict";
 var print_id = '';
 var rows = $('.commodity-group-table').find('tbody tr');
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
   $.post(admin_url + 'purchase/bulk_commodity_group_delete', {
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