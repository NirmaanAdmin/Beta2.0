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
initDataTable('.commodity-group-table', admin_url + 'purchase/table_pur_commodity_group', [], [], Params, [0, 'asc']);
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

</script>