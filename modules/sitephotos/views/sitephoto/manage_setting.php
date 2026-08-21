<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .btn-outline-info {
      background-color: transparent !important;
      color: #337ab7 !important;
      border: 1px solid #337ab7 !important;
   }
   .btn-outline-info:hover {
      background-color: #337ab7 !important;
      color: #fff !important;
   }
   .site_btn {
      transition: all 0.2s ease;
   }
   .site_btn.active {
      background-color: #337ab7 !important;
      color: #fff !important;
      border-color: #337ab7 !important;
   }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="col-md-12 mbot10">
                        <a href="<?php echo admin_url('sitephotos/photos?group=timeline'); ?>" class="btn btn-outline-info pull-left display-block site_btn <?php echo $group === 'timeline' ? 'active' : ''; ?>">Timeline</a>
                        <a href="<?php echo admin_url('sitephotos/photos?group=albums'); ?>" class="btn btn-outline-info pull-left display-block mleft10 site_btn <?php echo $group === 'albums' ? 'active' : ''; ?>">Albums</a>
                        <a href="<?php echo admin_url('sitephotos/photos?group=recycle_bin'); ?>" class="btn btn-outline-info pull-left display-block mleft10 site_btn <?php echo $group === 'recycle_bin' ? 'active' : ''; ?>">Recycle Bin</a>
                    </div>
                    <div class="col-md-12">
                        <?php $this->load->view($tabs['view']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php if($group == 'timeline') {
    require 'modules/sitephotos/assets/js/timeline_js.php';
} ?>