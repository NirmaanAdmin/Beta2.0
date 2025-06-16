<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (staff_can('create', 'google_workspace')) { ?>
                    <div class="tw-mb-2 sm:tw-mb-4">
                        <a href="#" class="btn btn-primary tw-mr-4" data-toggle="modal" data-target="#google_workspace_sheet_modal">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('google_workspace_new_google_sheet'); ?>
                        </a>
                        <a href="<?php echo admin_url('google_workspace/fetch_sheets'); ?>" class="btn btn-success">
                            <i class="fa-solid fa-arrows-rotate tw-mr-1"></i>
                            <?php echo _l('google_workspace_fetch'); ?>
                        </a>
                    </div>
                <?php } ?>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                            render_datatable([
                                _l('google_workspace_title'),
                                _l('google_workspace_description'),
                                _l('google_workspace_created_at'),
                                _l('google_workspace_created_by'),
                                _l('google_workspace_options'),
                            ], 'google-drive-sheets');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="google_workspace_sheet_modal" tabindex="-1" role="dialog" aria-labelledby="googleSpreadSheetModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="googleSpreadSheetModalLabel">
                    <span class="edit-title"><?php echo _l('google_workspace_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('google_workspace_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/google_workspace/save', ['id' => 'google_workspace_sheet_form']); ?>
            <input type="hidden" name="type" value="sheet" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('title', 'google_workspace_title'); ?>
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('description', 'google_workspace_description'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    window.addEventListener('load',function() {
        appValidateForm($('#google_workspace_sheet_form'), { name: 'required' }, manage_google_workspaces);

        $('#google_workspace_sheet_modal').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var google_workspace_id = $(invoker).data('id');
            $('#google_workspace_sheet_modal .add-title').removeClass('hide');
            $('#google_workspace_sheet_modal .edit-title').addClass('hide');
            $('#google_workspace_sheet_modal input[name="id"]').val('');
            $('#google_workspace_sheet_modal input[name="title"]').val('');
            $('#google_workspace_sheet_modal input[name="description"]').val('');
            // is from the edit button
            if (typeof(google_workspace_id) !== 'undefined') {
                $('#google_workspace_sheet_modal input[name="id"]').val(google_workspace_id);
                $('#google_workspace_sheet_modal .add-title').addClass('hide');
                $('#google_workspace_sheet_modal .edit-title').removeClass('hide');
                $('#google_workspace_sheet_modal input[name="title"]').val($(invoker).parents('tr').find('td').eq(0).text());
                $('#google_workspace_sheet_modal input[name="description"]').val($(invoker).parents('tr').find('td').eq(1).text());
            }
        });
    });

    function manage_google_workspaces(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if ($.fn.DataTable.isDataTable('.table-google-drive-sheets')) {
                    $('.table-google-drive-sheets').DataTable().ajax.reload();
                }
                if ($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    var groups = $('select[name="groups_in[]"]');
                    groups.prepend('<option value="' + response.id + '">' + response.name + '</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            } else {
                alert_float('error', response.message);
                setTimeout(function() {
                    window.location.href = response.redirect_url;
                }, 1000);
            }
            $('#google_workspace_sheet_modal').modal('hide');
        });
        return false;
    }

    $(function() {
        initDataTable('.table-google-drive-sheets', window.location.href, [1], [1]);
    });
</script>

</body>

</html>