<script> 

function new_sub_group_type(){
  "use strict";
  $('#sub_group_type').modal('show');
  $('.edit-title').addClass('hide');
  $('.add-title').removeClass('hide');
  $('input[name="sub_group_type_id"]').val('');
  $('input[name="sub_group_code"]').val('');
  $('input[name="sub_group_name"]').val('');
  $('select[name="group_id"]').val('').selectpicker('refresh');
}

function edit_sub_group_type(invoker,id) {
  "use strict";
  appValidateForm($('#add_sub_group'),{sub_group_code:'required', sub_group_name:'required'});
  var sub_group_code = $(invoker).data('sub_group_code');
  var sub_group_name = $(invoker).data('sub_group_name');
  var group_id = $(invoker).data('group_id');
  $('input[name="sub_group_type_id"]').val(id);
  $('input[name="sub_group_code"]').val(sub_group_code);
  $('input[name="sub_group_name"]').val(sub_group_name);
  if(group_id) {
    $('select[name="group_id"]').val(group_id).selectpicker('refresh');
  } else {
    $('select[name="group_id"]').val('').selectpicker('refresh');
  }
  $('#sub_group_type').modal('show');
  $('#sub_group_type .add-title').addClass('hide');
  $('#sub_group_type .edit-title').removeClass('hide');
}

appValidateForm($('#add_sub_group'),{sub_group_code:'required', sub_group_name:'required'});

var sub_group_table;
sub_group_table = $('.sub-group-table');
var Params = {
  "project": "[name='select_project']"
};
initDataTable('.sub-group-table', admin_url + 'purchase/table_pur_sub_group', [], [], Params, [0, 'asc']);
$('select[name="select_project"]').on('change', function () {
  sub_group_table.DataTable().ajax.reload();
});

$(document).on('click', '.active_sub_group', function(e) {
   e.preventDefault();
   var url = $(this).attr('href');
   Swal.fire({
      title: 'Are you sure?',
      text: 'Do you want to activate this sub budget head?',
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

$(document).on('click', '.deactive_sub_group', function(e) {
   e.preventDefault();
   var url = $(this).attr('href');
   Swal.fire({
      title: 'Are you sure?',
      text: 'Do you want to deactivate this sub budget head?',
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

function uploadpurchasesubgroupfilecsv() {
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
    url: admin_url + 'purchase/import_file_xlsx_purchase_sub_group',
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
        sub_group_table.DataTable().ajax.reload();
    },
    error: function() {
      alert_float('danger', 'Error uploading file. Please try again.');
    }
  });
  return false;
}

</script>