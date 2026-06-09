<script>
	var dt2 = new DataTransfer();
	(function() {
		"use strict";
		$('.selectpicker').selectpicker('refresh');
		appValidateForm('#create_new_section', {
			name: 'required'
		});

		// Images upload and edit

		$("#search").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$(".table-items tbody tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});

		$('#files').on('change', function(e) {
			var max_file = 6;
			var count = this.files.length;
			var parent = $(this).closest('.file-form');
			for (var i = 0; i < count; i++) {
				let fileBloc = '<li class="list-group-item list-group-item-action display-flex">';
				fileBloc += '<div class="name mtop7 w100">' + this.files.item(i).name + '</div>';
				fileBloc += '<button class="btn btn-sm btn-link remove-attachment" type="button">';
				fileBloc += '<i class="fa fa-times"></i>';
				fileBloc += '</button>';
				fileBloc += '</li>';
				parent.find("#selectedFiles").append(fileBloc);
			};
			parent.find('.file-form-preview').removeClass('hide');
			for (let file of this.files) {
				dt2.items.add(file);
			}
			this.files = dt2.files;
			parent.find('.remove-attachment').unbind('click').bind('click', function(e) {
				var this_obj = $(this);
				let name = this_obj.closest('.list-group-item').find('.name').text().trim();
				$(this).closest('li').remove();
				for (let i = 0; i < dt2.items.length; i++) {
					if (name === dt2.items[i].getAsFile().name) {
						dt2.items.remove(i);
						continue;
					}
				}
				if (parent.find('.list-group-item').length == 0) {
					parent.find('.file-form-preview').addClass('hide');
				}
				document.getElementById('files').files = dt2.files;
				return false;
			});
		});

		$(document).on("click", "#mass_select_all", function() {
			var favorite = [];
			if ($(this).is(':checked')) {
				$('.individual').prop('checked', true);
				$.each($(".individual"), function() {
					favorite.push($(this).data('id'));
				});
			} else {
				$('.individual').prop('checked', false);
				favorite = [];
			}
			$("input[name='check']").val(favorite);
			update_href_url_bulk_download();
			change_tool_ui();
		});

		$('#expiration').on('click', function() {
			var val = $(this).is(':checked');
			if (val == true) {
				$('#expiration_date').removeAttr('disabled').val('').attr('required', true);
			} else {
				$('#expiration_date').attr('disabled', true).val('').removeAttr('required');
			}
		});

		// $('input[name="share_to"]').on('click', function(){
		// 	var val = $(this).val();
		// 	$('select[name="staff[]"]').removeAttr('required');
		// 	$('select[name="vendor[]"]').removeAttr('required');
		// 	$('select[name="customer_group[]"]').removeAttr('required');
		// 	$('.staff_fr').addClass('hide');
		// 	$('.vendor_fr').addClass('hide');
		// 	$('.staff_pr').addClass('hide');
		// 	$('.vendor_pr').addClass('hide');
		// 	$('.customer_group_fr').addClass('hide');
		// 	switch(val){
		// 		case 'staff':
		// 		$('.staff_fr').removeClass('hide');
		// 		$('.staff_pr').removeClass('hide');
		// 		$('select[name="staff[]"]').attr('required', 'required');
		// 		break;
		// 		case 'vendor':
		// 		$('.vendor_fr').removeClass('hide');
		// 		$('.vendor_pr').removeClass('hide');
		// 		$('select[name="vendor[]"]').attr('required', 'required');
		// 		break;
		// 		case 'customer_group':
		// 		$('.customer_group_fr').removeClass('hide');
		// 		$('select[name="customer_group[]"]').attr('required', 'required');
		// 		break;
		// 	}
		// 	$('select[name="staff[]"]').val('').change();
		// 	$('select[name="vendor[]"]').val('').change();
		// 	$('select[name="customer_group[]"]').val('').change();
		// });

		$('input[name="share_to"]').on('click', function() {
			var val = $(this).val();

			$('select[name="staff[]"]').removeAttr('required');
			$('select[name="vendor[]"]').removeAttr('required');
			$('select[name="customer_group[]"]').removeAttr('required');

			$('.staff_fr').addClass('hide');
			$('.vendor_fr').addClass('hide');
			$('.staff_pr').addClass('hide');
			$('.vendor_pr').addClass('hide');
			$('.customer_group_fr').addClass('hide');

			// Disable all permission dropdowns first
			$('.staff_pr select[name="permission"]').prop('disabled', true);
			$('.vendor_pr select[name="permission"]').prop('disabled', true);

			switch (val) {
				case 'staff':
					$('.staff_fr').removeClass('hide');
					$('.staff_pr').removeClass('hide');

					$('select[name="staff[]"]').attr('required', 'required');

					// Enable staff permission dropdown
					$('.staff_pr select[name="permission"]').prop('disabled', false);
					break;

				case 'vendor':
					$('.vendor_fr').removeClass('hide');
					$('.vendor_pr').removeClass('hide');

					$('select[name="vendor[]"]').attr('required', 'required');

					// Enable vendor permission dropdown
					$('.vendor_pr select[name="permission"]').prop('disabled', false);
					break;

				case 'customer_group':
					$('.customer_group_fr').removeClass('hide');
					$('select[name="customer_group[]"]').attr('required', 'required');
					break;
			}

			$('select[name="staff[]"]').val('').change();
			$('select[name="vendor[]"]').val('').change();
			$('select[name="customer_group[]"]').val('').change();
		});

		// Run once on page load
		$('input[name="share_to"]:checked').trigger('click');

		$('select[name="vendor[]"]').on('change', function() {
			var vendor = $(this).val();
			if (!empty(vendor)) {
				$.ajax({
					url: admin_url + 'drawing_management/get_primary_vendors',
					method: 'post',
					data: {
						vendor: vendor
					},
				}).done(function(response) {
					if (!empty(response)) {
						$('.vendor_contact').html('');
						$('.vendor_contact').html(response);
						init_selectpicker();
					} else {
						$('.vendor_contact').html('');
					}
				});
			}
		});

	})(jQuery);

	function validate_share_form() {
		var val = $('input[name="share_to"]').val();
		var data = {};
		switch (val) {
			case 'staff':
				data.staff = 'required';
				break;
			case 'vendor':
				data.vendor = 'required';
				data.vendor_contact = 'required';
				break;
			case 'customer_group':
				data.customer_group = 'required';
				break;
		}
		appValidateForm('#share_document', data);

	}

	function create_new_section() {
		"use strict";
		var modal_obj = $('#create_new_section');
		modal_obj.find('.modal-title').addClass('hide');
		modal_obj.find('#name').val('');
		modal_obj.find('input[name="id"]').val('');
		modal_obj.find('input[name="parent_id"]').val('0');
		modal_obj.find('.modal-title.add-title.title1').removeClass('hide');
		modal_obj.modal('show');
	}

	var selDiv = "";

	function init() {
		"use strict";
		document.querySelector('#files').addEventListener('change', handleFileSelect, false);
		selDiv = document.querySelector("#selectedFiles");
	}

	function handleFileSelect(e) {
		"use strict";
		if (!e.target.files || !window.FileReader) return;
		selDiv.innerHTML = "";
		var files = e.target.files;
		var filesArr = Array.prototype.slice.call(files);

		jQuery.each(filesArr, function(key, file) {
			if (!file.type.match("image.*")) {
				return;
			}
			var reader = new FileReader();
			reader.onload = function(e) {
				var html = "<div class=\"col-md-3\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" + file.name + "\"><div class=\"contain_image\"><img src=\"" + e.target.result + "\"></div><div class=\"file-name\">" + file.name + "<div></div>";
				selDiv.innerHTML += html;
			}
			reader.readAsDataURL(file);
		});
	}

	function create_folder() {
		"use strict";
		var modal_obj = $('#create_new_section');
		modal_obj.find('.modal-title').addClass('hide');
		modal_obj.find('#name').val('');
		modal_obj.find('input[name="id"]').val('');
		var default_parent_id = $('input[name="default_parent_id"]').val();
		modal_obj.find('input[name="parent_id"]').val(default_parent_id);
		modal_obj.find('.modal-title.add-title.title2').removeClass('hide');
		modal_obj.modal('show');
	}

	function edit_folder(el, id) {
		"use strict";
		var name = $(el).data('name');
		var modal_obj = $('#create_new_section');
		modal_obj.find('.modal-title').addClass('hide');
		modal_obj.find('.modal-title.edit-title.title2').removeClass('hide');
		modal_obj.find('input[name="id"]').val(id);
		var default_parent_id = $('input[name="default_parent_id"]').val();
		modal_obj.find('input[name="parent_id"]').val(default_parent_id);
		modal_obj.find('input[name="name"]').val(name);
		modal_obj.modal('show');
	}

	function edit_section(el, id) {
		"use strict";
		var name = $(el).data('name');
		var modal_obj = $('#create_new_section');
		modal_obj.find('.modal-title').addClass('hide');
		modal_obj.find('.modal-title.edit-title.title1').removeClass('hide');
		modal_obj.find('input[name="id"]').val(id);
		modal_obj.find('input[name="parent_id"]').val('0');
		modal_obj.find('input[name="name"]').val(name);
		modal_obj.modal('show');
	}

	function open_upload() {
		"use strict";
		$('input[name="file[]"]').click();
	}

	function checked_add(el) {
		"use strict";
		var id = $(el).data("id");
		var id_product = $(el).data("product");
		if ($(".individual").length == $(".individual:checked").length) {
			$("#mass_select_all").attr("checked", "checked");
			var value = $("input[name='check']").val();
			if (value != '') {
				value = value + ',' + id;
			} else {
				value = id;
			}
		} else {
			$("#mass_select_all").removeAttr("checked");
			var value = $("input[name='check']").val();
			var arr_val = value.split(',');
			if (arr_val.length > 0) {
				$.each(arr_val, function(key, value) {
					if (value == id) {
						arr_val.splice(key, 1);
						value = arr_val.toString();
						$("input[name='check']").val(value);
					}
				});
			}
		}
		if ($(el).is(':checked')) {
			var value = $("input[name='check']").val();
			if (value != '') {
				value = value + ',' + id;
			} else {
				value = id;
			}
			$("input[name='check']").val(value);
		} else {
			var value = $("input[name='check']").val();
			var arr_val = value.split(',');
			if (arr_val.length > 0) {
				$.each(arr_val, function(key, value) {
					if (value == id) {
						arr_val.splice(key, 1);
						value = arr_val.toString();
						$("input[name='check']").val(value);
					}
				});
			}
		}
		update_href_url_bulk_download();
		change_tool_ui();
	}

	function change_tool_ui() {
		"use strict";
		var check = $('input[name="check"]').val();
		if (check == '') {
			$('.default-tool').removeClass('hide');
			$('.bulk-action-btn').addClass('hide');
		} else {
			$('.default-tool').addClass('hide');
			$('.bulk-action-btn').removeClass('hide');
		}
	}

	function bulk_delete_item() {
		"use strict";
		var check = $('input[name="check"]').val();
		if (check != '') {
			Swal.fire({
				title: '<?php echo _l('dmg_are_you_sure') . '?'; ?>',
				text: '<?php echo _l('dmg_do_you_really_want_to_delete_these_items_this_process_cannot_be_undone'); ?>',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '<?php echo _l('dmg_yes_delete_it'); ?>',
				cancelButtonText: '<?php echo _l('dmg_cancel'); ?>',
			}).then((result) => {
				if (result.isConfirmed) {
					show_processing('<?php echo _l('dmg_deleting'); ?>');
					ajaxGet(admin_url + 'drawing_management/bulk_delete_item?id=' + check).done(function(success) {
						location.reload();
					}).fail(function(error) {

					});
				}
			})
		}
	}

	function update_href_url_bulk_download() {
		"use strict";
		var selected_id = $('input[name="check"]').val();
		var bulk_btn_obj = $('.bulk-download-btn');
		var href = bulk_btn_obj.attr('href');
		var split_array = href.split('=');
		var new_href = '';
		var length = split_array.length;
		let i = 0;
		for (i = 0; i < length; i++) {
			if (i < length - 1) {
				new_href += split_array[i] + '=';
			}
		}
		bulk_btn_obj.attr('href', new_href + selected_id);
	}


	function bulk_duplicate_item() {
		"use strict";
		var selected_id = $('input[name="check"]').val();
		var parent_id = $('input[name="parent_id"]').val();
		if (selected_id != '') {
			show_select_folder(parent_id, selected_id);
		}
	}

	function continue_action() {
		"use strict";
		var modal = $('#select_folder');
		modal.modal('hide');
		if (modal.find('input[name="action_type"]').val() == 'duplicate') {
			var selected_item = $('input[name="selected_item"]').val();
			var select_folder = $('input[name="select_folder"]:checked').val();
			if (select_folder != '') {
				if (selected_item != '') {
					show_processing('<?php echo _l('dmg_processing'); ?>');
					ajaxGet(admin_url + 'drawing_management/bulk_duplicate_item?selected_folder=' + select_folder + '&selected_item=' + selected_item).done(function(success) {
						location.reload();
					}).fail(function(error) {

					});
				}
			} else {
				alert_float('warning', '<?php echo _l('dmg_please_select_a_folder'); ?>');
			}
		}
		if (modal.find('input[name="action_type"]').val() == 'move') {
			var selected_item = $('input[name="selected_item"]').val();
			var select_folder = $('input[name="select_folder"]:checked').val();
			if (select_folder != '') {
				if (selected_item != '') {
					show_processing('<?php echo _l('dmg_moving'); ?>');
					ajaxGet(admin_url + 'drawing_management/bulk_move_item?selected_folder=' + select_folder + '&selected_item=' + selected_item).done(function(success) {
						location.reload();
					}).fail(function(error) {

					});
				}
			} else {
				alert_float('warning', '<?php echo _l('dmg_please_select_a_folder'); ?>');
			}
		}
	}

	function bulk_duplicate_item() {
		"use strict";
		var selected_id = $('input[name="check"]').val();
		var parent_id = $('input[name="parent_id"]').val();
		if (selected_id != '') {
			show_select_folder(parent_id, selected_id, 'duplicate');
		}
	}

	function show_select_folder(selected_folder, selected_id, action_type) {
		"use strict";
		var modal_obj = $('#select_folder');
		modal_obj.modal('show');
		modal_obj.find('input[name="selected_item"]').val(selected_id);
		modal_obj.find('input[name="action_type"]').val(action_type);
		ajaxGet('drawing_management/get_folder_list?parent=0&selected_folder=' + selected_folder + '&selected_item=' + selected_id).done(function(response) {
			modal_obj.find('.modal-body .list').html(response);
			$('body').find('.tree').fadeOut(0);
		});
	}

	function duplicate_item(selected_id) {
		"use strict";
		var parent_id = $('input[name="parent_id"]').val();
		if (selected_id != '') {
			show_select_folder(parent_id, selected_id, 'duplicate');
		}
	}

	function bulk_move_item() {
		"use strict";
		var selected_id = $('input[name="check"]').val();
		var parent_id = $('input[name="parent_id"]').val();
		if (selected_id != '') {
			show_select_folder(parent_id, selected_id, 'move');
		}
	}

	function move_item(selected_id) {
		"use strict";
		var parent_id = $('input[name="parent_id"]').val();
		if (selected_id != '') {
			show_select_folder(parent_id, selected_id, 'move');
		}
	}

	function share_document(el, id) {
		"use strict";
		var modal_obj = $('#share_document');
		modal_obj.modal('show');
		if ($(el).data('type') != 'folder') {
			modal_obj.find('.add-title.title1').removeClass('hide');
			modal_obj.find('.add-title.title2').addClass('hide');
			modal_obj.find('select[name="permission"] option[value="upload_only"]').hide().selectpicker('refresh');
			modal_obj.find('select[name="permission"]').val('preview').change().selectpicker('refresh');
		} else {
			modal_obj.find('.add-title.title1').addClass('hide');
			modal_obj.find('.add-title.title2').removeClass('hide');
			modal_obj.find('select[name="permission"] option[value="upload_only"]').show().selectpicker('refresh');
			modal_obj.find('select[name="permission"]').val('preview').change().selectpicker('refresh');
		}
		modal_obj.find('#staff').click();
		modal_obj.find('#expiration').prop('checked', false);
		modal_obj.find('#expiration_date').val('');
		modal_obj.find('input[name="item_id"]').val(id);
		modal_obj.find('select[name="permission"]').val('preview').change();
	}
</script>