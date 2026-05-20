<script>
	var dt2 = new DataTransfer();
	(function() {
		"use strict";


		appValidateForm('#form_create_remider', {
			date: 'required',
			email: 'required'
		});

		$('#file_version').on('change', function(e) {
			var max_file = 6;
			var count = this.files.length;
			var parent = $(this).closest('.file-form-update-version');
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
			$(this).addClass('hide');
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
					parent.find('input[type="file"]').removeClass('hide');
				}
				document.getElementById('file_version').files = dt2.files;
				return false;
			});
		});

		$('#move_after_approval').on('click', function() {
			var val = $(this).is(':checked');
			var modal_obj = $('#send_request_approve_modal');
			if (val == true) {
				modal_obj.find('.modal-body .list').removeClass('hide');
				modal_obj.find('input[name="select_folder"]').attr('required', true);
			} else {
				modal_obj.find('.modal-body .list').addClass('hide');
				modal_obj.find('input[name="select_folder"]').removeAttr('required');
			}
		});


	})(jQuery);

	function upload_new_version(id) {
		"use strict";
		$('#upload_new_version').modal('show');
	}

	function restore_item_version(id) {
		"use strict";
		if (id != '') {
			Swal.fire({
				title: '<?php echo _l('dmg_are_you_sure') . '?'; ?>',
				text: '<?php echo _l('dmg_are_you_sure_you_want_to_restore') . '?'; ?>',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '<?php echo _l('dmg_yes_restore_it'); ?>',
				cancelButtonText: '<?php echo _l('dmg_cancel'); ?>',
			}).then((result) => {
				if (result.isConfirmed) {
					show_processing('<?php echo _l('dmg_restoring'); ?>');
					ajaxGet(admin_url + 'drawing_management/restore_item/' + id).done(function(success) {
						location.reload();
					}).fail(function(error) {

					});
				}
			})
		}
	}

	function remider(id) {
		"use strict";
		var modal_obj = $('#remider');
		modal_obj.modal('show');
		modal_obj.find('#email').val('');
		modal_obj.find('#message').val('');
		modal_obj.find('input[name="file_id"]').val(id);
		modal_obj.find('input[name="id"]').val('');
		modal_obj.find('.add-title').removeClass('hide');
		modal_obj.find('.edit-title').addClass('hide');
	}

	function edit_remind(el, id) {
		"use strict";
		var btn_obj = $(el);
		var file_id = btn_obj.data('file_id');
		var date = btn_obj.data('date');
		var email = btn_obj.data('email');
		var message = btn_obj.data('message');

		var modal_obj = $('#remider');
		modal_obj.modal('show');
		modal_obj.find('#date').val(date);
		modal_obj.find('#email').val(email);
		modal_obj.find('#message').val(message);
		modal_obj.find('input[name="file_id"]').val(file_id);
		modal_obj.find('input[name="id"]').val(id);
		modal_obj.find('.edit-title').removeClass('hide');
		modal_obj.find('.add-title').addClass('hide');
	}

	function lock_unlock_file(id, type) {
		"use strict";
		if (type == 'lock') {
			Swal.fire({
				title: '<?php echo _l('dmg_are_you_sure'); ?>',
				text: '<?php echo _l('dmg_other_editors_will_not_be_able_to_change_the_metadata'); ?>',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '<?php echo _l('dmg_yes_lock_it'); ?>',
				cancelButtonText: '<?php echo _l('dmg_cancel'); ?>',
			}).then((result) => {
				if (result.isConfirmed) {
					show_processing('<?php echo _l('dmg_processing'); ?>');
					ajaxGet(admin_url + 'drawing_management/lock_unlock_item/' + id + '/' + type).done(function(success) {
						location.reload();
					}).fail(function(error) {

					});
				}
			});
		}
		if (type == 'unlock') {
			Swal.fire({
				title: '<?php echo _l('dmg_are_you_sure'); ?>',
				text: '',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '<?php echo _l('dmg_yes_unlock_it'); ?>',
				cancelButtonText: '<?php echo _l('dmg_cancel'); ?>',
			}).then((result) => {
				if (result.isConfirmed) {
					show_processing('<?php echo _l('dmg_processing'); ?>');
					ajaxGet(admin_url + 'drawing_management/lock_unlock_item/' + id + '/' + type).done(function(success) {
						location.reload();
					}).fail(function(error) {

					});
				}
			});
		}
	}

	function edit_share(el, id) {
		"use strict";
		var btn_obj = $(el);
		var id = btn_obj.data('id');
		var item_id = btn_obj.data('item_id');
		var type = btn_obj.data('type');
		var share_to = btn_obj.data('share_to');
		var vendor = btn_obj.data('vendor');
		var vendor_contact = btn_obj.data('vendor_contact');
		var staff = btn_obj.data('staff');
		var customer_group = btn_obj.data('customer_group');
		var expiration = btn_obj.data('expiration');
		var expiration_date = btn_obj.data('expiration_date');
		var permission = btn_obj.data('permission');
		var message = btn_obj.data('message');

		var modal_obj = $('#share_document');
		modal_obj.modal('show');
		modal_obj.find('.modal-title').addClass('hide');
		if (type == 'folder') {
			modal_obj.find('.edit-title.title2').removeClass('hide');
		} else {
			modal_obj.find('.edit-title.title1').removeClass('hide');
		}

		modal_obj.find('input[name="id"]').val(id);
		modal_obj.find('input[name="item_id"]').val(item_id);
		if (share_to == 'staff') {
			modal_obj.find('#staff').click();
			modal_obj.find('select[name="staff[]"]').val(parse_string_to_array(staff)).change();
		}
		if (share_to == 'vendor') {
			modal_obj.find('#vendor').click();
			modal_obj.find('select[name="vendor[]"]').val(parse_string_to_array(vendor)).change();
			if (!empty(vendor_contact)) {
				setTimeout(function() {
					var $select = modal_obj.find('select[name="vendor_contact[]"]');
					if ($select.length > 0) {
						var selectedValues = parse_string_to_array(vendor_contact);
						$select.val(selectedValues);
						$select.selectpicker('refresh');
						$select.trigger('change');
					}
				}, 300);
			}
		}
		if (share_to == 'customer_group') {
			modal_obj.find('#customer_group').click();
			modal_obj.find('select[name="customer_group[]"]').val(parse_string_to_array(customer_group)).change();
		}

		if (expiration == '1') {
			modal_obj.find('#expiration').prop('checked', true);
			modal_obj.find('#expiration_date').removeAttr('disabled').val(expiration_date).attr('required');
		} else {
			modal_obj.find('#expiration').prop('checked', false);
			modal_obj.find('#expiration_date').attr('disabled', true).val('').removeAttr('required');
		}
		modal_obj.find('select[name="permission"] option[value="upload_only"]').hide().selectpicker('refresh');
		modal_obj.find('select[name="permission"]').val(permission).change().selectpicker('refresh');
		modal_obj.find('#message').val(message);
	}

	function parse_string_to_array(string) {
		"use strict";
		var list_id = [];
		try {
			jQuery.each(string.split(','), function(key, value) {
				list_id.push(value);
			});
		} catch (err) {
			list_id.push(string);
		}
		return list_id;
	}

	function new_share(el, id) {
		"use strict";
		var btn_obj = $(el);
		var item_id = btn_obj.data('item_id');
		var modal_obj = $('#share_document');

		modal_obj.modal('show');
		modal_obj.find('.modal-title').addClass('hide');
		modal_obj.find('.add-title.title1').removeClass('hide');
		modal_obj.find('#staff').click();
		modal_obj.find('#expiration').prop('checked', false);
		modal_obj.find('#expiration_date').val('');
		modal_obj.find('input[name="item_id"]').val(id);
		modal_obj.find('input[name="id"]').val('');
		modal_obj.find('select[name="permission"] option[value="upload_only"]').hide().selectpicker('refresh');
		modal_obj.find('select[name="permission"]').val('preview').selectpicker('refresh');
	}

	function send_request_approve(id, type) {
		"use strict";
		var modal_obj = $('#send_request_approve_modal');
		modal_obj.modal('show');
		modal_obj.find('#move_after_approval').prop('checked', false);
		modal_obj.find('#show_files_metadata').prop('checked', false);
		modal_obj.find('.modal-body .list').addClass('hide');

		if (type == 'eid') {
			modal_obj.find('.add-title.title1').addClass('hide');
			modal_obj.find('.add-title.title2').removeClass('hide');
		} else {
			modal_obj.find('.add-title.title1').removeClass('hide');
			modal_obj.find('.add-title.title2').addClass('hide');
		}

		modal_obj.find('input[name="approve_type"]').val(type);
		var selected_folder = $('input[name="folder_id"]').val();
		var selected_id = '';

		ajaxGet('drawing_management/get_folder_list?parent=0&selected_folder=' + selected_folder + '&selected_item=' + selected_id).done(function(response) {
			modal_obj.find('.modal-body .list .panel-body').html(response);
			$('body').find('.tree').fadeOut(0);
		});
	}

	function upload_old_version(id) {
		"use strict";
		$('#upload_old_version').modal('show');
	}
</script>