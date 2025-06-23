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
	  var rows = $(".package-body table.items tbody .items");
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

	initItemSelect();

	/**
    * Initializes the logic for handling item selection and input events.
    */
    function initItemSelect() {
	    // Listen for input events on the search box of specific dropdowns
	    $(document).on('input', '.item-select  .bs-searchbox input', function() {
	      let tab = $('.detailed-costing-tab.active').attr('id');
	      let query = $(this).val(); // Get the user's query
	      let $bootstrapSelect = $(this).closest('.bootstrap-select'); // Get the parent bootstrap-select wrapper
	      let $selectElement = $bootstrapSelect.find('select.item-select'); // Get the associated select element

	      // console.log("Target Select Element:", $selectElement); // Debug the target <select> element

	      if (query.length >= 3) {
	        fetchItems(query, $selectElement); // Fetch items dynamically
	      }
	    });

	    // Handle the change event for the item-select dropdown
	    $(document).on('change', '.item-select', function() {
	      handleItemChange($(this)); // Handle item selection change
	    });
    }

    /**
    * Fetches items dynamically based on the search query and populates the target select element.
    * @param {string} query - The search query entered by the user.
    * @param {jQuery} $selectElement - The select element to populate.
    */

    function fetchItems(query, $selectElement) {
        var admin_url = '<?php echo admin_url(); ?>';
        $.ajax({
          url: admin_url + 'purchase/fetch_items', // Controller method URL
          type: 'GET',
          data: {
            search: query
          },
          success: function(data) {
            // console.log("Raw Response Data:", data); // Debug the raw data

            try {
              let items = JSON.parse(data); // Parse JSON response
              // console.log("Parsed Items:", items); // Debug parsed items

              if ($selectElement.length === 0) {
                console.error("Target select element not found.");
                return;
              }

              // Clear existing options in the specific select element
              $selectElement.empty();

              // Add default "Type to search..." option
              $selectElement.append('<option value="">Type to search...</option>');

              // Get the pre-selected ID if available (from a data attribute or a hidden field)
              let preSelectedId = $selectElement.data('selected-id') || null;

              // Populate the specific select element with new options
              items.forEach(function(item) {
                let isSelected = preSelectedId && item.id === preSelectedId ? 'selected' : '';
                let option = `<option  data-commodity-code="${item.id}" value="${item.id}"> ${item.commodity_code} ${item.description}</option>`;
                // console.log("Appending Option:", option); // Debug each option
                $selectElement.append(option);
              });

              // Refresh the selectpicker to reflect changes
              $selectElement.selectpicker('refresh');

              // console.log("Updated Select Element HTML:", $selectElement.html()); // Debug the final HTML
            } catch (error) {
              console.error("Error Processing Response:", error);
            }
          },
          error: function() {
            console.error('Failed to fetch items.');
          }
        });
    }

    /**
    * Handles the change event for the item-select dropdown.
    * @param {jQuery} $selectElement - The select element that triggered the change.
    */
    function handleItemChange($selectElement) {
        let selectedId = $selectElement.val(); // Get the selected item's ID
        let selectedCommodityCode = $selectElement.find(':selected').data('commodity-code'); // Get the commodity code
        let $inputField = $selectElement.closest('tr').find('input[name="item_code"]'); // Find the associated input field

        if ($inputField.length > 0) {
          $inputField.val(selectedCommodityCode || ''); // Update the input field with the commodity code
          // console.log("Updated Input Field:", $inputField, "Value:", selectedCommodityCode); // Debug input field
        }
    }

    var tabKeyCounts = 0;
	function getNextItemKey() {
	  if (!tabKeyCounts) {
	    tabKeyCounts = 0;
	  }
	  tabKeyCounts += 1;
	  return tabKeyCounts;
	}

	function add_package_item_to_table(data, itemid) {

	  data =
	    typeof data == "undefined" || data == "undefined"
	      ? get_package_item_preview_values()
	      : data;

	  var table_row = "";
	  var item_key = getNextItemKey();

	  table_row +=
	    '<tr class="items pack_items">';

	  var unawarded_amount = data.unawarded_qty * data.unawarded_rate;
	  var package_amount = data.package_qty * data.package_rate;

	  var item_name = "newpackageitems[" + item_key + "][item_name]";
	  var regex = /<br[^>]*>/gi;
	  $.when(
	    get_estimate_purchase_items(data.item_name, item_name)
	  ).done(function (estimate_purchase_dropdown) {

	    if(data.item_name) {
	      table_row += '<td class="item_name">' + estimate_purchase_dropdown + '</td>';
	    } else {
	      table_row += '<td class="item_name"><select id="'+item_name+'" name="'+item_name+'" data-selected-id="" class="form-control selectpicker item-select" data-live-search="true" ><option value="">Type at least 3 letters...</option></select></td>';
	    }

	    table_row +=
	      '<td><textarea name="newpackageitems[' +
	      item_key +
	      '][long_description]" class="form-control long_description" rows="2">' +
	      data.long_description.replace(regex, "\n") +
	      "</textarea></td>";

	    table_row +=
	      '<td></td>';

	    table_row +=
	      '<td class="all_unawarded_qty"><input type="number" name="newpackageitems[' +
	      item_key +
	      '][unawarded_qty]" value="' +
	      data.unawarded_qty +
	      '" class="form-control" readonly></td>';

	    table_row +=
	      '<td class="all_unawarded_rate"><input type="number" name="newpackageitems[' +
	      item_key +
	      '][unawarded_rate]" value="' +
	      data.unawarded_rate +
	      '" class="form-control" readonly></td>';

	    table_row +=
	      '<td class="all_unawarded_amount"><input type="number" name="newpackageitems[' +
	      item_key +
	      '][unawarded_amount]" value="' +
	      unawarded_amount +
	      '" class="form-control" readonly></td>';

	    table_row +=
	      '<td class="all_package_qty"><input type="number" onblur="calculate_package();" onchange="calculate_package();" name="newpackageitems[' +
	      item_key +
	      '][package_qty]" value="' +
	      data.package_qty +
	      '" class="form-control"></td>';

	    table_row +=
	      '<td class="all_package_rate"><input type="number" onblur="calculate_package();" onchange="calculate_package();" name="newpackageitems[' +
	      item_key +
	      '][package_rate]" value="' +
	      data.package_rate +
	      '" class="form-control"></td>';

	    table_row +=
	      '<td class="all_package_amount"><input type="number" name="newpackageitems[' +
	      item_key +
	      '][package_amount]" value="' +
	      package_amount +
	      '" class="form-control" readonly></td>';

	    table_row +=
	      '<td><textarea name="newpackageitems[' +
	      item_key +
	      '][remarks]" class="form-control remarks" rows="2">' +
	      data.remarks.replace(regex, "\n") +
	      "</textarea></td>";

	    table_row +=
	      '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_package_item(this,' +
	      itemid +
	      '); return false;"><i class="fa fa-trash"></i></a></td>';

	    table_row += "</tr>";

	    $('.package-body table.items tbody').append(table_row);

	    $(document).trigger({
	      type: "item-added-to-table",
	      data: data,
	      row: table_row,
	    });

	    setTimeout(function () {
	      calculate_package();
	    }, 15);

	    if (
	      $("#item_select").hasClass("ajax-search") &&
	      $("#item_select").selectpicker("val") !== ""
	    ) {
	      $("#item_select").prepend("<option></option>");
	    }

	    init_selectpicker();
	    init_datepicker();
	    init_color_pickers();
	    clear_package_item_preview_values();

	    $("body").find("#items-warning").remove();
	    $("body").find(".dt-loader").remove();
	    $("#item_select").selectpicker("val", "");

	    if (cf_has_required && $(".estimate-form").length) {
	      validate_estimate_form();
	    }
	    return true;
	  });
	  return false;
	}

	function get_package_item_preview_values() {
	  var response = {};
	  var tab = $('.package-body table.items tbody');
	  response.item_name = tab.find('select[name="item_name"]').val();
	  response.long_description = tab.find('textarea[name="long_description"]').val();
	  response.unawarded_qty = tab.find('input[name="unawarded_qty"]').val();
	  response.unawarded_rate = tab.find('input[name="unawarded_rate"]').val();
	  response.unawarded_amount = tab.find('input[name="unawarded_amount"]').val();
	  response.package_qty = tab.find('input[name="package_qty"]').val();
	  response.package_rate = tab.find('input[name="package_rate"]').val();
	  response.package_amount = tab.find('input[name="package_amount"]').val();
	  response.remarks = tab.find('textarea[name="remarks"]').val();
	  return response;
	}

	function clear_package_item_preview_values(tab) {
	  var previewArea = $('.package-body table.items tbody').find("tr").eq(0);
	  previewArea.find("textarea").val("");
	  previewArea.find('select').selectpicker("val", "");
	  previewArea.find('input').val(0);
	}

	function delete_package_item(row, itemid) {
	  	$(row)
	    .parents("tr")
	    .addClass("animated fadeOut", function () {
	      setTimeout(function () {
	        $(row).parents("tr").remove();
	        calculate_package();
	      }, 50);
	    });
	}
</script>