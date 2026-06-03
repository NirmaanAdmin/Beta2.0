// Approve

function approve_request(id){
  "use strict";
  change_request_approval_status(id,1);
}

function deny_request(id){
  "use strict";
  change_request_approval_status(id,2);
}

function change_request_approval_status(id, status){
  "use strict";
  var data = {};
  data.rel_id = id;
  data.rel_type = 'drawing';
  data.approve = status;
  data.note = $('textarea[name="reason"]').val();
  $.post(admin_url + 'drawing_management/change_approve_document/' + id, data).done(function(response){
    response = JSON.parse(response);
    if (response.success === true || response.success == 'true') {
      alert_float('success', response.message);
      window.location.reload();
    }
    else{
      alert_float('danger', response.message);
      window.location.reload();
    }
  });
}
