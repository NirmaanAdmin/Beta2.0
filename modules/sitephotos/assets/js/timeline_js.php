<script>
var selected = [];

function esc(value) {
    return $('<div>').text(value || '').html();
}

loadPhotos();
function loadPhotos() {
    $.post(admin_url + 'sitephotos/listing_timeline', {
        search: $('#timeline_search').val(),
        interval: $('#timeline_interval').val(),
        from_date: $('#timeline_from_date').val(),
        to_date: $('#timeline_to_date').val(),
        area: $('select[name="timeline_area[]"]').val() || [],
        rfi: $('select[name="timeline_rfi[]"]').val() || [],
        drawing: $('select[name="timeline_drawing[]"]').val() || []
    }, function(response) {
        $('#gallery').empty();
        if (response.count > 0) {
            $('.empty_gallery').addClass('hide');
            $('#gallery').html(response.html);
        } else {
            $('.empty_gallery').removeClass('hide');
        }
    }, 'json');
}

$(document).on('click', '.timeline_photo', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $.post(admin_url + 'sitephotos/get_timeline_detail', {
        id: id
    }, function(response) {
        if (typeof response === 'string') {
            response = JSON.parse(response);
        }
        if (!response.success) {
            alert_float('danger', response.message || 'Photo not found.');
            return;
        }
        var photo = response.data;
        $('#view_title').text(photo.title || photo.original_name);
        $('#view_image').attr('src', <?= json_encode(SITEPHOTOS_TIMELINE_URL_PATH); ?> + encodeURIComponent(photo.file_name));
        $('#edit_id').val(photo.id);
        $('#edit_title').val(photo.title || '');
        $('#edit_description').val(photo.description || '');
        $('#uploaded_on').text(photo.uploaded_on || '-');
        $('#uploaded_by_name').text(photo.uploaded_by_name || '-');
        $('#single_download').attr('href', admin_url + 'sitephotos/download_timeline/' + photo.id);
        $('#single_email').attr('data-id', photo.id);
        $('#single_delete').attr('data-id', photo.id);
        var areas = photo.area ? String(photo.area).split(',') : [];
        $('select[name="edit_area[]"]').val(areas).selectpicker('refresh');
        var rfis = photo.rfi ? String(photo.rfi).split(',') : [];
        $('select[name="edit_rfi[]"]').val(rfis).selectpicker('refresh');
        var drawings = photo.drawing ? String(photo.drawing).split(',') : [];
        $('select[name="edit_drawing[]"]').val(drawings).selectpicker('refresh');
        $('#comment_photo_id').val(photo.id);
        $('#timeline_comment').val('');
        $('#view_modal').modal('show');
        loadTimelineComments(photo.id);
    }, 'json');
});

$(document).on('click', '.timeline_check', function(e) {
    e.stopPropagation();
});

$(document).on('change', '.timeline_check', function(e) {
    var id = $(this).data('id');
    if ($(this).is(':checked')) {
        if (!selected.includes(id)) {
            selected.push(id);
        }
    } else {
        selected = selected.filter(function(item) {
            return item != id;
        });
    }
    if (selected.length > 0) {
        $('#selected_count').text(selected.length);
        $('.selection_toolbar').removeClass('hide');
    } else {
        $('#selected_count').text(0);
        $('.selection_toolbar').addClass('hide');
    }
});

$('#upload_timeline_photos').on('click', function() {
    $('#upload_form')[0].reset();
    $('#file_preview').empty();
    $('#upload_modal').modal('show');
});

$('#dropzone').on('click', function(e) {
    if(e.target.id !== 'files') $('#files').trigger('click');
});

$('#files').on('change', function() {
    var html = '';
    Array.from(this.files).forEach(function(file) {
        if (file.type.indexOf('image/') !== 0) return;
        var url = URL.createObjectURL(file);
        html += '<div class="col-md-2 preview_item"><img src="'+url+'"><div class="small text-muted">'+esc(file.name)+'</div></div>';
    });
    $('#file_preview').html(html);
});

$('#upload_form').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    if (<?php echo pur_check_csrf_protection(); ?>) {
        formData.append(csrfData.token_name, csrfData.hash);
    }
    $.ajax({
        url: admin_url + 'sitephotos/upload_timeline',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success:function(response) {
            if (response.success) {
                $('#upload_modal').modal('hide');
                alert_float('success', response.message);
                loadPhotos();
            } else {
                alert_float('danger', response.message || 'Upload failed.');
            }
        }
    });
});

$('#edit_form').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    if (<?php echo pur_check_csrf_protection(); ?>) {
        formData.append(csrfData.token_name, csrfData.hash);
    }
    $.ajax({
        url: admin_url + 'sitephotos/update_timeline/' + $('#edit_id').val(),
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#view_modal').modal('hide');
                alert_float('success', response.message);
                loadPhotos();
            } else {
                alert_float('danger', response.message || 'Update failed.');
            }
        },
        error: function() {
            alert_float('danger', 'Something went wrong while updating the photo.');
        }
    });
});

$(document).on('click', '#delete_selected', function(e) {
    e.preventDefault();
    if (selected.length === 0) {
        return;
    }
    var count = selected.length;
    var message = count === 1 ? 'Are you sure you want to delete this photo?' : 'Are you sure you want to delete these ' + count + ' photos?';
    if (!confirm(message)) {
        return;
    }
    $.post(admin_url + 'sitephotos/delete_timeline', {
        ids: selected
    }, function(response) {
        if (response.success) {
            selected = [];
            $('#selected_count').text(0);
            $('.selection_toolbar').addClass('hide');
            alert_float('success', response.message);
            loadPhotos();
        } else {
            alert_float('danger', response.message);
        }
    }, 'json');
});

$(document).on('click', '#single_delete', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    if (!id) {
        alert_float('danger', 'Photo ID is missing.');
        return;
    }
    if (!confirm('Are you sure you want to delete this photo?')) {
        return;
    }
    $.post(admin_url + 'sitephotos/delete_timeline', {
        ids: [id]
    }, function(response) {
        if (response.success) {
            alert_float('success', response.message);
            $('#view_modal').modal('hide');
            loadPhotos();
            selected = [];
            $('#selected_count').text(0);
            $('.selection_toolbar').addClass('hide');
        } else {
            alert_float(
                'danger',
                response.message || 'Unable to delete photo.'
            );
        }
    }, 'json');
});

$(document).on('click', '#download_selected', function(e) {
    e.preventDefault();
    if (selected.length === 0) {
        return;
    }
    selected.forEach(function(id) {
        window.open(
            admin_url + 'sitephotos/download_timeline/' + id,
            '_blank'
        );
    });
});

var shareDocumentIds = [];

function setShareDocumentIds(ids) {
    $('#share_document_ids').html('');
    $.each(ids, function(index, id) {
        if (id) {
            $('#share_document_ids').append(
                '<input type="hidden" name="document_ids[]" value="' + id + '">'
            );
        }
    });
}

function openShareDocumentModal(ids) {
    if (!ids || ids.length === 0) {
        alert_float('warning', 'Please select at least one document.');
        return;
    }
    shareDocumentIds = ids.slice();
    setShareDocumentIds(shareDocumentIds);
    $('#share_document_form select[name="staff[]"]').val([]).selectpicker('refresh');
    $('#share_document_form select[name="vendor[]"]').val([]).selectpicker('refresh');
    $('#share_document_form textarea[name="message"]').val('');
    $('#share_to_staff').prop('checked', true).trigger('change');
    $('#share_document_modal').modal('show');
}

$(document).on('click', '#email_selected', function(e) {
    e.preventDefault();
    if (typeof selected === 'undefined' || selected.length === 0) {
        alert_float('warning', 'Please select at least one document.');
        return;
    }
    openShareDocumentModal(selected);
});

$(document).on('click', '#single_email', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    if (!id) {
        alert_float('danger', 'Photo ID is missing.');
        return;
    }
    openShareDocumentModal([id]);
});

$(document).on('change', 'input[name="share_to"]', function(e) {
    var val = $(this).val();
    $('select[name="staff[]"]').removeAttr('required');
    $('select[name="vendor[]"]').removeAttr('required');
    $('.staff_fr').addClass('hide');
    $('.vendor_fr').addClass('hide');
    if (val === 'staff') {
        $('.staff_fr').removeClass('hide');
        $('select[name="staff[]"]').attr('required', 'required');
    } else if (val === 'vendor') {
        $('.vendor_fr').removeClass('hide');
        $('select[name="vendor[]"]').attr('required', 'required');
    }
});

$('input[name="share_to"]:checked').trigger('change');

$(document).on('change', 'select[name="vendor[]"]', function(e) {
    e.preventDefault();
    var vendor = $(this).val();
    if (!empty(vendor)) {
        $.ajax({
            url: admin_url + 'sitephotos/get_primary_vendors',
            method: 'post',
            data: {
                vendor: vendor
            }
        }).done(function(response) {
            if (!empty(response)) {
                $('.vendor_contact').html(response);
                init_selectpicker();
            } else {
                $('.vendor_contact').html('');
            }
        });
    } else {
        $('.vendor_contact').html('');
    }
});

$(document).on('submit', '#share_document_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var button = $('#share_document_submit');
    if (!shareDocumentIds || shareDocumentIds.length === 0) {
        alert_float('danger', 'No document selected.');
        return false;
    }
    setShareDocumentIds(shareDocumentIds);
    var shareTo = form.find('input[name="share_to"]:checked').val();
    if (shareTo === 'staff') {
        var staff = form.find('select[name="staff[]"]').val();
        if (!staff || staff.length === 0) {
            alert_float('warning', 'Please select at least one staff member.');
            return false;
        }
    }
    if (shareTo === 'vendor') {
        var vendor = form.find('select[name="vendor[]"]').val();
        if (!vendor || vendor.length === 0) {
            alert_float('warning', 'Please select at least one vendor.');
            return false;
        }
    }
    button.prop('disabled', true);
    button.html('<i class="fa fa-spinner fa-spin"></i> Sharing...');
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if (response.success) {
                alert_float('success', response.message || 'Document(s) shared successfully.');
                $('#share_document_modal').modal('hide');
                form[0].reset();
                form.find('select[name="staff[]"]').val([]).selectpicker('refresh');
                form.find('select[name="vendor[]"]').val([]).selectpicker('refresh');
                $('.vendor_contact').html('');
                shareDocumentIds = [];
                $('#share_document_ids').html('');
            } else {
                alert_float('danger', response.message || 'Unable to share document(s).');
            }
        },
        error: function(xhr) {
            var message = 'Something went wrong while sharing the document.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            alert_float('danger', message);
        },
        complete: function() {
            button.prop('disabled', false);
            button.html('<i class="fa fa-paper-plane"></i> Share');
        }
    });
    return false;
});

$('#share_document_modal').on('hidden.bs.modal', function() {
    var form = $('#share_document_form');
    form[0].reset();
    shareDocumentIds = [];
    $('#share_document_ids').html('');
    form.find('select[name="staff[]"]').val([]).selectpicker('refresh');
    form.find('select[name="vendor[]"]').val([]).selectpicker('refresh');
    $('.vendor_contact').html('');
    $('#share_to_staff').prop('checked', true).trigger('change');
});

var timer;
$('#timeline_search').on('keyup', function() {
    clearTimeout(timer);
    timer = setTimeout(loadPhotos, 350);
});

$(document).on('change', '#timeline_interval', function() {
    var interval = $(this).val();
    if (interval === 'custom_range') {
        $('.custom_range_field').removeClass('hide');
    } else {
        $('.custom_range_field').addClass('hide');
        $('#timeline_from_date').val('');
        $('#timeline_to_date').val('');
    }
    loadPhotos();
});

$(document).on('change', '#timeline_from_date, #timeline_to_date', function() {
    if (
        $('#timeline_interval').val() === 'custom_range' &&
        $('#timeline_from_date').val() &&
        $('#timeline_to_date').val()
    ) {
        loadPhotos();
    }
});

$(document).on('change', 'select[name="timeline_area[]"], select[name="timeline_rfi[]"], select[name="timeline_drawing[]"]', function() {
    loadPhotos();
});

function escapeHtml(text) {
    return $('<div>').text(text || '').html();
}

function renderComment(comment) {
    var staffName = escapeHtml(
        comment.staff_name || 'Unknown User'
    );
    var commentText = escapeHtml(
        comment.comment || ''
    );
    var createdOn = escapeHtml(
        comment.created_on || ''
    );
    var updatedLabel = '';
    if (comment.updated_at) {
        updatedLabel =
        '<span class="timeline_comment_edited">' +
        '(edited)' +
        '</span>';
    }
    var actions = '';
    if (comment.can_edit) {
        actions +=
        '<button type="button" ' +
        'class="btn btn-link btn-xs timeline_comment_edit" ' +
        'data-id="' + comment.id + '" ' +
        'title="Edit">' +
        '<i class="fa fa-pencil"></i>' +
        '</button>';
    }
    if (comment.can_delete) {
        actions +=
        '<button type="button" ' +
        'class="btn btn-link btn-xs text-danger timeline_comment_delete" ' +
        'data-id="' + comment.id + '" ' +
        'title="Delete">' +
        '<i class="fa fa-trash"></i>' +
        '</button>';
    }
    var html =
        '<div class="timeline_comment" ' +
        'data-comment-id="' + comment.id + '">' +
            '<div class="timeline_comment_header">' +
                '<div class="timeline_comment_user">' +
                    '<div class="timeline_comment_avatar">' +
                        '<i class="fa fa-user"></i>' +
                    '</div>' +
                    '<div>' +
                        '<div class="timeline_comment_author">' +
                            staffName +
                        '</div>' +
                        '<div class="timeline_comment_date">' +
                            createdOn +
                            ' ' +
                            updatedLabel +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="timeline_comment_actions">' +
                    actions +
                '</div>' +
            '</div>' +
            '<div class="timeline_comment_body">' +
                '<div class="timeline_comment_text">' +
                    commentText +
                '</div>' +
                '<div class="timeline_comment_edit_box" ' +
                'style="display:none;">' +
                    '<textarea ' +
                    'class="form-control timeline_edit_textarea" ' +
                    'rows="3"></textarea>' +
                    '<div class="timeline_comment_edit_buttons">' +
                        '<button ' +
                        'type="button" ' +
                        'class="btn btn-primary btn-sm timeline_comment_save" ' +
                        'data-id="' + comment.id + '">' +
                            '<i class="fa fa-save"></i> Save' +
                        '</button> ' +
                        '<button ' +
                        'type="button" ' +
                        'class="btn btn-default btn-sm timeline_comment_cancel">' +
                            'Cancel' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    var $html = $(html);
    $html.find('.timeline_edit_textarea').val(comment.comment || '');
    return $html;
}

function loadTimelineComments(photoId) {
    var $list = $('#timeline_comments_list');
    $list.html(
        '<div class="timeline_comments_loading">' +
            '<i class="fa fa-spinner fa-spin"></i> ' +
            'Loading comments...' +
        '</div>'
    );
    $.post(admin_url + 'sitephotos/get_timeline_comments', {
        photo_id: photoId
    }, function(response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if (!response.success) {
                $list.html(
                    '<div class="timeline_comments_empty">' +
                        escapeHtml(
                            response.message ||
                            'Unable to load comments.'
                        ) +
                    '</div>'
                );
                return;
            }
            var comments = response.data || [];
            $list.empty();
            if (comments.length === 0) {
                $list.html(
                    '<div class="timeline_comments_empty">' +
                        '<i class="fa fa-comments-o"></i>' +
                        '<span>No comments yet.</span>' +
                    '</div>'
                );
                return;
            }
            $.each(comments, function(index, comment) {
                $list.append(renderComment(comment));
            });
            $list.scrollTop($list[0].scrollHeight);
        }, 'json'
    ).fail(function() {
        $list.html(
            '<div class="timeline_comments_empty">' +
                'Unable to load comments.' +
            '</div>'
        );
    });
}

$(document).on('submit', '#timeline_comment_form', function(e) {
    e.preventDefault();
    var photoId = $('#comment_photo_id').val();
    var comment = $.trim($('#timeline_comment').val());
    if (!photoId) {
        alert_float('danger', 'Invalid photo.');
        return;
    }
    if (!comment) {
        alert_float('warning', 'Please enter a comment.');
        $('#timeline_comment').focus();
        return;
    }
    var $button = $('#add_comment_btn');
    $button.prop('disabled', true);
    $button.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
    $.post(admin_url + 'sitephotos/add_timeline_comment', {
        photo_id: photoId,
        comment: comment
    }, function(response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if (response.success) {
                $('#timeline_comment').val('');
                alert_float('success', response.message);
                loadTimelineComments(photoId);
            } else {
                alert_float('danger', response.message || 'Unable to add comment.');
            }
        }, 'json'
    ).fail(function() {
        alert_float('danger', 'An error occurred while adding the comment.');
    }).always(function() {
        $button.prop('disabled', false);
        $button.html('<i class="fa fa-paper-plane"></i> Add Comment');
    });
});

$(document).on('click', '.timeline_comment_edit', function(e) {
    e.preventDefault();
    var $comment = $(this).closest('.timeline_comment');
    $comment.find('.timeline_comment_text').hide();
    $comment.find('.timeline_comment_edit_box').show();
    $comment.find('.timeline_edit_textarea').val(
        $comment.find('.timeline_comment_text').text()
    ).focus();
});

$(document).on('click', '.timeline_comment_cancel', function(e) {
    e.preventDefault();
    var $comment = $(this).closest('.timeline_comment');
    $comment.find('.timeline_comment_edit_box').hide();
    $comment.find('.timeline_comment_text').show();
});

$(document).on('click', '.timeline_comment_save', function(e) {
    e.preventDefault();
    var $button = $(this);
    var commentId = $button.data('id');
    var $comment = $button.closest('.timeline_comment');
    var newComment = $.trim($comment.find('.timeline_edit_textarea').val());
    if (!newComment) {
        alert_float('warning', 'Please enter a comment.');
        return;
    }
    $button.prop('disabled', true);
    $button.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    $.post(admin_url + 'sitephotos/update_timeline_comment', {
        comment_id: commentId,
        comment: newComment
    }, function(response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if (response.success) {
                alert_float('success', response.message);
                loadTimelineComments($('#comment_photo_id').val());
            } else {
                alert_float('danger', response.message || 'Unable to update comment.');
            }
        }, 'json'
    ).fail(function() {
        alert_float('danger', 'An error occurred while updating the comment.');
    }).always(function() {
        $button.prop('disabled', false);
        $button.html('<i class="fa fa-save"></i> Save');
    });
});

$(document).on('click', '.timeline_comment_delete', function(e) {
    e.preventDefault();
    var commentId = $(this).data('id');
    var photoId = $('#comment_photo_id').val();
    if (!confirm(
        'Are you sure you want to delete this comment?'
    )) {
        return;
    }
    var $button = $(this);
    $button.prop('disabled', true);
    $.post(admin_url + 'sitephotos/delete_timeline_comment', {
        comment_id: commentId
    }, function(response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if (response.success) {
                alert_float('success', response.message);
                loadTimelineComments(photoId);
            } else {
                alert_float('danger', response.message || 'Unable to delete comment.');
            }
        }, 'json'
    ).fail(function() {
        alert_float('danger', 'An error occurred while deleting the comment.');
    }).always(function() {
        $button.prop('disabled', false);
    });
});

$('#view_modal').on('hidden.bs.modal', function() {
    $('#comment_photo_id').val('');
    $('#timeline_comment').val('');
    $('#timeline_comments_list').html('');
});

</script>