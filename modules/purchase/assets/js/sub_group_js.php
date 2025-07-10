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
</script>