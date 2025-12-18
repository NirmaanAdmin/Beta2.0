<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="company-gst-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('companygst/company_gst'), ['id' => 'company-gst-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_company_gst'); ?></span>
                    <span class="add-title"><?php echo _l('new_company_gst'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'name'); ?>
                        <?php echo render_textarea('description', 'description'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
  window.addEventListener('load',function(){
     appValidateForm($('#company-gst-form'),{name:'required'},manage_company_gst);
        $('#company-gst-modal').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#company-gst-modal input[name="name"]').val('');
            $('#company-gst-modal textarea').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
  });
   function manage_company_gst(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success) {
                alert_float('success', response.message);
            } else {
                alert_float('warning', response.message);
            }
            if($.fn.DataTable.isDataTable('.table-company-gst')){
                $('.table-company-gst').DataTable().ajax.reload();
            }
            $('#company-gst-modal').modal('hide');
        });
        return false;
    }

    function new_company_gst(){
        $('#company-gst-modal').modal('show');
        $('.edit-title').addClass('hide');
    }

    function edit_company_gst(invoker,id){
        var name = $(invoker).data('name');
        var description = $(invoker).data('description');
        $('#additional').append(hidden_input('id',id));
        $('#company-gst-modal input[name="name"]').val(name);
        $('#company-gst-modal textarea').val(description);
        $('#company-gst-modal').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
