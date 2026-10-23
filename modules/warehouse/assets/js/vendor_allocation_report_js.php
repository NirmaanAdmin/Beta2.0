<script>
(function($) {
	"use strict";
	get_data_vendor_allocation_report();

	function get_data_vendor_allocation_report() {
	  "use strict";
	  var formData = new FormData();
	  formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
	  formData.append("vendors", $('select[name="vendors[]"]').val());
	  $.ajax({ 
	    url: admin_url + 'warehouse/get_vendor_allocation_report_view', 
	    method: 'post', 
	    data: formData, 
	    contentType: false, 
	    processData: false
	  }).done(function(response) {
	      var response = JSON.parse(response);
	      $('#vendor_a_report').html('');
	      $('#vendor_a_report').append(response.value);
	  });   
	}

	$('select[name="vendors[]"]').on('change', function() {
      get_data_vendor_allocation_report();
    });

})(jQuery);

function vendor_allocation_submit(invoker) {
    var $form = $('#print_allocation_report');
    var vendors = $('#vendors').val() || [];
    $form.find('.vendor-export-input').remove();
    var vendorValue = vendors.length ? vendors.join(',') : null;
    $('<input>', {
        type: 'hidden',
        name: 'vendors',
        value: vendorValue,
        class: 'vendor-export-input'
    }).appendTo($form);
    $form.attr('method', 'POST').attr('target', '_blank').trigger('submit');
}
	
</script>