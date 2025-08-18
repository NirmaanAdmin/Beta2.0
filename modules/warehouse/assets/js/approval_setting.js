	
  var addMoreVendorsInputKey;
  (function($) {
  "use strict";

   addMoreVendorsInputKey = $('.list_approve select[name*="approver"]').length;
  $("body").on('click', '.new_wh_approval', function() {
       if ($(this).hasClass('disabled')) { return false; }
      
      
      var newattachment = $('.list_approve').find('#item_approve').eq(0).clone().appendTo('.list_approve');
      newattachment.find('button[data-toggle="dropdown"]').remove();
      newattachment.find('select').selectpicker('refresh');

      newattachment.find('button[data-id="approver[0]"]').attr('data-id', 'approver[' + addMoreVendorsInputKey + ']');
      newattachment.find('label[for="approver[0]"]').attr('for', 'approver[' + addMoreVendorsInputKey + ']');
      newattachment.find('select[name="approver[0]"]').attr('name', 'approver[' + addMoreVendorsInputKey + ']');
      newattachment.find('select[id="approver[0]"]').attr('id', 'approver[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

      newattachment.find('button[data-id="staff[0]"]').attr('data-id', 'staff[' + addMoreVendorsInputKey + ']');
      newattachment.find('label[for="staff[0]"]').attr('for', 'staff[' + addMoreVendorsInputKey + ']');
      newattachment.find('select[name="staff[0]"]').attr('name', 'staff[' + addMoreVendorsInputKey + ']');
      newattachment.find('select[id="staff[0]"]').attr('id', 'staff[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

      newattachment.find('button[data-id="action[0]"]').attr('data-id', 'action[' + addMoreVendorsInputKey + ']');
      newattachment.find('label[for="action[0]"]').attr('for', 'action[' + addMoreVendorsInputKey + ']');
      newattachment.find('select[name="action[0]"]').attr('name', 'action[' + addMoreVendorsInputKey + ']');
      newattachment.find('select[id="action[0]"]').attr('id', 'action[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

      newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
      newattachment.find('button[name="add"]').removeClass('new_wh_approval').addClass('remove_wh_approval').removeClass('btn-success').addClass('btn-danger');
      addMoreVendorsInputKey++;

  });
  $("body").on('click', '.remove_wh_approval', function() {
      $(this).parents('#item_approve').remove();
  });

  $('.account-template-form-submiter').on('click', function() {
    $('input[name="account_template"]').val(account_template.getData());
  });

  $(document).on('change', '#related', function() {
    var data = {};
    data.approval_setting_id = $('input[name="approval_setting_id"]').val();
    data.related = $('select[name="related"]').val();
    $('.submit_approval_setting').prop('disabled', false);
    if(data.related) {
        $.post(admin_url+'warehouse/find_approval_setting',data).done(function(response){
            response = JSON.parse(response);
            if(response.success == true) {
                alert_float('warning', 'Approval settings already exists on this project');
                $('.submit_approval_setting').prop('disabled', true);
            } else {
                $('.submit_approval_setting').prop('disabled', false);
            }
        });
    }
  });

})(jQuery);

function edit_approval_setting(invoker,id){
  "use strict";
  appValidateForm($('#approval-setting-form'),{name:'required', related:'required', "approver[]": "required"});

  var name = $(invoker).data('name');
  var related = $(invoker).data('related');
  
  $('input[name="approval_setting_id"]').val(id);
  $('#approval_setting_modal input[name="name"]').val(name);
  $('select[name="related"]').val(related).selectpicker('refresh');

  var approver = $(invoker).data('approver');
  var approver_array = [];
  if(approver) {
    approver_array = approver.toString().split(',');
  }

  $('#approver').empty().selectpicker('refresh');
  $.post(admin_url+'warehouse/find_project_members').done(function(response){
    response = JSON.parse(response);
    if(response.length > 0) {
        $.each(response, function(idx, member) {
            var approver_view = $('#approver');
            if(approver_array.includes(member.id)) {
                approver_view.prepend('<option value="'+member.id+'" selected>'+member.full_name+'</option>');
            } else {
                approver_view.prepend('<option value="'+member.id+'">'+member.full_name+'</option>');
            }
        });
        $('#approver').selectpicker('refresh');
    }
  });

  // $.post(admin_url + 'warehouse/get_html_approval_setting/'+ id).done(function(response) {
  //    response = JSON.parse(response);

  //     $('.list_approve').html('');
  //     $('.list_approve').append(response);
  // init_selectpicker();

  // });
  
  $('#approval_setting_modal').modal('show');
  $('#approval_setting_modal .add-title').addClass('hide');
  $('#approval_setting_modal .edit-title').removeClass('hide');
}

function new_approval_setting(){
  "use strict";
  appValidateForm($('#approval-setting-form'),{name:'required', related:'required', "approver[]": "required"});

  $('#approval_setting_modal input[name="name"]').val('');
  $('select[name="related"]').val('').selectpicker('refresh');
  $('#approver').empty().selectpicker('refresh');
  
  // $.post(admin_url + 'warehouse/get_html_approval_setting').done(function(response) {
  //    response = JSON.parse(response);

  //     $('.list_approve').html('');
  //     $('.list_approve').append(response);
  //     init_selectpicker();

  // });

  $('#approval_setting_modal').modal('show');
  $('#approval_setting_modal .add-title').removeClass('hide');
  $('#approval_setting_modal .edit-title').addClass('hide');
}