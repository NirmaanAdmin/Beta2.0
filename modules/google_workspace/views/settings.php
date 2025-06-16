<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body pickers">
                        <div class="form-group">
                            <label class="bold" for="google_workspace_client_id">
                                <?php echo _l('google_workspace_client_id'); ?>
                            </label>
                            <input type="text" name="google_workspace_client_id" id="google_workspace_client_id" class="form-control" value="<?php echo get_option('google_workspace_client_id'); ?>" required />
                        </div>
                        <div class="form-group">
                            <label class="bold" for="google_workspace_client_secret">
                                <?php echo _l('google_workspace_client_secret'); ?>
                            </label>
                            <input type="text" name="google_workspace_client_secret" id="google_workspace_client_secret" class="form-control" value="<?php echo get_option('google_workspace_client_secret'); ?>" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-bottom-pusher"></div>
        <div class="btn-bottom-toolbar text-right">
            <a href="<?php echo admin_url('google_workspace/reset_settings'); ?>" data-toggle="tooltip" data-title="<?php echo _l('google_workspace_reset_info'); ?>" class="btn btn-default">
                <?php echo _l('reset'); ?>
            </a>
            <a href="#" onclick="save_google_workspace_settings(); return false;" class="btn btn-primary">
                <?php echo _l('save'); ?>
            </a>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    function save_google_workspace_settings() {
        $.post(admin_url + 'google_workspace/integrate', {
            client_id: $('[name="google_workspace_client_id"]').val(),
            client_secret: $('[name="google_workspace_client_secret"]').val(),
        }).done(function(resp) {
            resp = JSON.parse(resp);
            if (resp.status === 'success') {
                window.open(resp.auth_url);
            }
        });
    }

    $('.radio-info label').click(function() {
        $(this).closest('.radio-info').find('input[type="radio"]').click();
    });
</script>

</body>
</html>