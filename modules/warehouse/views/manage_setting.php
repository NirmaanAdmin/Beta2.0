<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
 <div class="content">
    <div class="row">
  
   <div class="col-md-3">
    <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
      <?php
      $i = 0;
      foreach($tab as $gr){
        ?>
        <li<?php if($i == 0){echo " class='active'"; } ?>>
        <a href="<?php echo admin_url('warehouse/setting?group='.$gr); ?>" data-group="<?php echo html_entity_decode($gr); ?>">

          <?php if($gr == 'warehouse' ){
            echo _l('_warehouse');

          }elseif($gr == 'rule_sale_price'){
            echo _l('rule_sale_price_export_type');
          }elseif($gr == 'bodys'){
            echo _l('_models');
          }else{
            echo _l($gr);

          }
           ?>
            
          </a>
        </li>
        <?php $i++; } ?>
      </ul>
      
      
  </div>
  <div class="col-md-9">
    <div class="panel_s">
     <div class="panel-body">

        <?php $this->load->view($tabs['view']); ?>
        
     </div>
  </div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<?php if($group == 'inventory' ){
require 'modules/warehouse/assets/js/inventory_js.php';
} else if($group == 'commodity_group'){
require require 'modules/purchase/assets/js/commodity_group_js.php';
} else if($group == 'sub_group'){
require 'modules/purchase/assets/js/sub_group_js.php';
} ?>
</body>
</html>
