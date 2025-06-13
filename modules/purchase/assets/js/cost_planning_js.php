<script>
	$(function() {
		$("body").on("change", "select[name='package_budget_head']", function (e) {
		  var id = $(this).find('option:selected').data('estimateid');
		  var package_budget = $(this).val();
		  if(package_budget != '') {
		    $.post(admin_url + "estimates/view_package", {
		      id: id,
		      package_budget: package_budget,
		    }).done(function (res) {
		      var response = JSON.parse(res);
		      if (response.itemhtml) {
		        $('.package-body').html('');
		        $('.package-body').html(response.itemhtml);
		        init_selectpicker();
		        init_datepicker();
		        calculate_package();
		      }
		    });
		  } else {
		    $('.package-body').html('');
		    init_selectpicker();
		  }
		});
	});

	function view_package(id) {
	  $.post(admin_url + "estimates/view_package", {
	    id: id,
	  }).done(function (res) {
	    var response = JSON.parse(res);
	    if (response.budgetsummaryhtml) {
	      $('.package-head').html('');
	      $('.package-head').html(response.budgetsummaryhtml);
	      $('.package-body').html('');
	      $('.package-body').html(response.itemhtml);
	      $('.package_title').html('Add Package');
	      init_selectpicker();
	      init_datepicker();
	      calculate_package();
	      $('#package_modal').modal('show');
	    }
	  });
	}

	function calculate_package() {
	  var total_unawarded_amount = 0,
	  total_package_amount = 0;
	  var rows = $(".package-body tbody tr");
	  $.each(rows, function () {
	    var row = $(this);
	    var unawarded_qty = parseFloat(row.find(".all_unawarded_qty input").val()) || 0;
	    var unawarded_rate = parseFloat(row.find(".all_unawarded_rate input").val()) || 0;
	    var package_qty = parseFloat(row.find(".all_package_qty input").val()) || 0;
	    var package_rate = parseFloat(row.find(".all_package_rate input").val()) || 0;
	    var unawarded_amount = unawarded_qty * unawarded_rate;
	    var package_amount = package_qty * package_rate;
	    row.find(".all_unawarded_amount input").val(unawarded_amount.toFixed(2));
	    row.find(".all_package_amount input").val(package_amount.toFixed(2));
	    total_unawarded_amount += unawarded_amount;
	    total_package_amount += package_amount;
	  });
	  var sdeposit_percent = parseFloat($("input[name='sdeposit_percent']").val()) || 0;
	  var sdeposit_value = 0;
	  if (sdeposit_percent > 0) {
	    var package_without_secured = total_package_amount;
	    total_package_amount += (total_package_amount * sdeposit_percent) / 100;
	    sdeposit_value = total_package_amount - package_without_secured;
	  }
	  var percentage_of_capex_used = 0;
	  if(total_unawarded_amount > 0) {
	    percentage_of_capex_used = (total_package_amount / total_unawarded_amount) * 100;
	    percentage_of_capex_used = Math.round(percentage_of_capex_used);
	  }
	  $(".percentage_of_capex_used").html(percentage_of_capex_used+'%');
	  $(".total_unawarded_amount").html(format_money(total_unawarded_amount));
	  $(".total_package").html(
	    format_money(total_package_amount) +
	    hidden_input("total_package", total_package_amount)
	  );
	  $(".sdeposit_value").html(
	    hidden_input("sdeposit_value", sdeposit_value)
	  );
	  $(document).trigger("sales-total-calculated");
	}

	function get_package_info(package_id, estimate_id, package_budget) {
	    if(package_id != '' && estimate_id != '' && package_budget != '') {
	      $.post(admin_url + "estimates/view_package", {
	        id: estimate_id,
	        package_id: package_id,
	      }).done(function (res) {
	        var response = JSON.parse(res);
	        if (response.budgetsummaryhtml) {
	          $('.package-head').html('');
	          $('.package-head').html(response.budgetsummaryhtml);
	          $('.package-body').html('');
	          $('.package-body').html(response.itemhtml);
	          $('.package_title').html('Add Package');
	          init_selectpicker();
	          init_datepicker();
	          calculate_package();
	          $('#package_modal').modal('show');
	        }
	      });
	    }
	}
</script>