(function(){
  "use strict";
  var fnServerParams = {
      "product_filter": "[name='product_filter']",
      "invoices": "[name='invoice']",
      "customers": "[name='customer']",
      "channel": "[name='channel']",
      "status": "[name='status']",
      "order_type": "[name='order_type']",
      "end_date": "[name='end_date']",
      "start_date": "[name='start_date']",
      "seller": "[name='seller']",
  }
 initDataTable('.table-order_list', admin_url + 'fixed_equipment/order_list_table', false, false, fnServerParams, [0, 'desc']);

  $('select[name="channel"], select[name="status"], select[name="order_type"],select[name="customer"], select[name="invoice"], input[name="start_date"], input[name="end_date"] , select[name="seller"]').on('change', function() {
   $('.table-order_list').DataTable().ajax.reload()
                    .columns.adjust();
  });

})(jQuery);
function view_order(el){
  "use strict";
  var id = $(el).data('id');

   var requestURL = (typeof(url) != 'undefined' ? url : 'fixed_equipment/get_cart_data/') + (typeof(id) != 'undefined' ? id : '');
      requestGetJSON(requestURL).done(function(response) {
        $('#content_order').html(response.data); 
  		$('#view_order').modal();
      }).fail(function(data) {
        alert_float('danger', data.responseText);
        $('#loading').hide();

    });    

}