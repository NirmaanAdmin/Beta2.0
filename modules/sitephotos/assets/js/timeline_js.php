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
    var id = $(this).data('id');
    $.post(admin_url + 'sitephotos/get_timeline_detail', {
        id:id
    }, function(response) {
        var photo = response;
        $('#view_title').text(photo.title || photo.original_name);
        $('#view_image').attr('src', <?= json_encode(SITEPHOTOS_TIMELINE_URL_PATH); ?> + encodeURIComponent(photo.file_name));
        $('#edit_id').val(photo.id);
        $('#edit_title').val(photo.title || '');
        $('#edit_description').val(photo.description || '');
        $('#uploaded_on').text(photo.uploaded_on || '-');
        $('#uploaded_by_name').text(photo.uploaded_by_name || '-');
        $('#single_download').attr('href', admin_url + 'sitephotos/download_timeline/'+photo.id);
        $('#single_delete').attr('data-id', photo.id);
        $('#view_modal').modal('show');
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

</script>