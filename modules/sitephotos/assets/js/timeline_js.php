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
        to_date: $('#timeline_to_date').val()
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
        $('#view_image').attr('src', <?= json_encode(SITEPHOTOS_TIMELINE_URL_PATH); ?> +
            encodeURIComponent(photo.file_name));
        $('#edit_id').val(photo.id);
        $('#edit_title').val(photo.title || '');
        $('#edit_description').val(photo.description || '');
        $('#uploaded_on').text(photo.uploaded_on || '-');
        $('#uploaded_by_name').text(photo.uploaded_by_name || '-');
        $('#single_download').attr('href', admin_url + 'sitephotos/download_timeline/'+photo.id);
        $('#single_delete').attr('data-id', photo.id);
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
    var id = $('#edit_id').val();
    $.post(admin_url + 'sitephotos/update_timeline/'+id, {
        title: $('#edit_title').val(),
        description: $('#edit_description').val()
    }, function(response) {
        if (response.success) {
            alert_float('success', response.message);
            $('#view_modal').modal('hide');
            loadPhotos();
        } else {
            alert_float('danger', response.message);
        }
    }, 'json');
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