<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">


      <div class="col-md-3">
        <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
          <li>
            <a href="javascript:void(0)" class="header">
              <h4>
                <i class="fa fa-gears menu-icon"></i> <?php echo _l('settings'); ?>                
              </h4>
            </a>
          </li>
          <?php
          $i = 0;
          foreach($tab as $gr){
            $url = admin_url('timesheets/setting?group='.$gr);
            $target = '';
            if($gr == 'api_integration'){
              $url = site_url('modules/timesheets/apidoc/index.html');
              $target = 'target="_blank"';
            }
            ?>
            <li<?php if($i == 0){echo " class='active'"; } ?>>
            <a href="<?php echo html_entity_decode($url); ?>" <?php echo html_entity_decode($target); ?> data-group="<?php echo html_entity_decode($gr); ?>">
              <?php 
              if($gr == 'payroll'){
                echo _l('_salary_form'); 
              }
              elseif($gr == 'manage_leave'){
                echo '<i class="fa fa-clipboard"></i> '._l('ts_norms_of_leave'); 
              }
              elseif($gr == 'manage_dayoff'){
                echo '<i class="fa fa-map-pin"></i> '._l('manage_dayoff'); 
              }
              elseif($gr == 'approval_process'){
                echo '<i class="fa fa-check"></i> '._l('approval_process'); 
              }
              elseif($gr == 'timekeeping_settings'){
                echo '<i class="fa fa-regular fa-pen-to-square"></i> '._l('timekeeping_settings'); 
              }
              elseif($gr == 'default_settings'){
                echo '<i class="fa fa-gear"></i> '._l('default_settings'); 
              }
              elseif($gr == 'permission'){
                echo '<i class="fa fa-user"></i> '._l('ts_permission'); 
              }
              elseif($gr == 'reset_data'){
                echo '<i class="fa fa-power-off"></i> '._l('ts_reset_data'); 
              }
              elseif($gr == 'type_of_leave'){
                echo '<i class="fa fa-pen-to-square"></i> '._l('ts_type_of_leave'); 
              }
              elseif($gr == 'valid_ip'){
                echo '<i class="fa fa-wifi"></i> '._l('ts_valid_ip'); 
              }
              elseif($gr == 'api_integration'){
                echo '<i class="fa fa-book"></i> '._l('ts_api_integration'); 
              }
              else{
                echo _l($gr); 
              }
              ?></a>
            </li>
            <?php $i++; } ?>
          </ul>
        </div>
        <div class="col-md-9">
          <div class="panel_s">
            <div class="panel-body">
              <?php  $this->load->view($tabs['view']); ?>
            </div>
          </div>
        </div>
      </div>



      <div class="clearfix"></div>
    </div>
    <div class="btn-bottom-pusher"></div>
  </div>
</div>




<div id="new_version"></div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/timesheets/assets/js/setting_js.php';?>

