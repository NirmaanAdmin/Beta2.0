<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">

       <?php echo form_open_multipart(admin_url('warehouse/add_update_internal_delivery'), array('id'=>'add_update_internal_delivery')); ?>

			<div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">
          <div class="row">
              <div class="col-md-12">
                <h4 class="no-margin font-bold"><i class="fa fa-rss-square menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                <hr>
              </div>
            </div>
                  <?php 
                    $id = '';
                    if(isset($internal_delivery)){
                      $id = $internal_delivery->id;
                      echo form_hidden('isedit');
                    }
                   ?>
                <input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">

          <div class="row">
             <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <?php $internal_delivery_name = (isset($internal_delivery) ? $internal_delivery->internal_delivery_name : $internal_delivery_name_ex);
                    echo render_input('internal_delivery_name','internal_delivery_name',$internal_delivery_name); ?>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12 form-group">
                      <label for="project"><?php echo _l('project'); ?></label>
                      <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <?php if(isset($projects)){ ?>
                          <?php foreach($projects as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($internal_delivery) && $s['id'] == $internal_delivery->project){ echo 'selected'; } ?>><?php echo html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-12">
                    
                    <?php $prefix = get_warehouse_option('internal_delivery_number_prefix');
                          $next_number = get_warehouse_option('next_internal_delivery_mumber');

                    $internal_delivery_code = (isset($internal_delivery) ? $internal_delivery->internal_delivery_code : $next_number);
                    $internal_delivery_code = (isset($internal_delivery) ? $internal_delivery->internal_delivery_code : $next_number);
                    echo form_hidden('internal_delivery_code',$internal_delivery_code); ?> 
                    
                    <label for="internal_delivery_code"><?php echo _l('internal_delivery_note_number'); ?></label>
                    <div class="input-group" id="discount-total"><div class="input-group-addon">
                          <div class="dropdown">
                             <span class="discount-type-selected">
                              <?php echo html_entity_decode($prefix) ;?>
                             </span>
                          </div>
                       </div>
                        <input type="text" readonly class="form-control" name="internal_delivery_code" value="<?php echo html_entity_decode($internal_delivery_code); ?>">
                    </div>
                  </div>
                </div>
             </div>

             <div class="col-md-6">
                <div class="col-md-6">
                  <?php $date_c = isset($internal_delivery) ? $internal_delivery->date_c : $current_day ;?>
                    <?php echo render_date_input('date_c','accounting_date', _d($date_c)) ?>
                </div>

                <div class="col-md-6">
                  <?php $date_add = isset($internal_delivery) ? $internal_delivery->date_add : $current_day ;?>
                  <?php echo render_date_input('date_add','day_vouchers', _d($date_add)) ?>
                </div>

                <div class="col-md-12">
                     <?php
                    $selected = '';
                    foreach($staff as $member){
                     if(isset($internal_delivery)){
                       if($internal_delivery->staff_id == $member['staffid']) {
                         $selected = $member['staffid'];
                       }
                     }
                    }
                    echo render_select('staff_id',$staff,array('staffid',array('firstname','lastname')),'deliver_name',$selected);
                    ?>
                </div>
             </div>  
               
          </div>
        </div>

        <div class="panel_s">
          <div class="panel-body">
            <label for="attachment"><?php echo _l('attachment'); ?></label>
            <div class="attachments">
              <div class="attachment">
                <div class="col-md-5 form-group" style="padding-left: 0px;">
                  <div class="input-group">
                    <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                    <span class="input-group-btn">
                      <button class="btn btn-success add_more_attachments p8" type="button"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <br /> <br />

            <?php
            if (isset($attachments) && count($attachments) > 0) {
              foreach ($attachments as $value) {
                echo '<div class="col-md-3">';
                $path = get_upload_path_by_type('inventory') . 'internal_delivery/' . $value['rel_id'] . '/' . $value['file_name'];
                $is_image = is_image($path);
                if ($is_image) {
                  echo '<div class="preview_image">';
                }
            ?>
                <a href="<?php echo site_url('download/file/inventory/' . $value['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-inventory-<?php echo $value['rel_id']; ?>" <?php } ?>>
                  <i class="<?php echo get_mime_class($value['filetype']); ?>"></i> <?php echo $value['file_name']; ?>
                  <?php if ($is_image) { ?>
                    <img class="mtop5" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']); ?>" style="height: 165px;">
                  <?php } ?>
                </a>
                <?php if ($is_image) {
                  echo '</div>';
                  echo '<a href="' . admin_url('warehouse/delete_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                } ?>
            <?php echo '</div>';
              }
            } ?>
          </div>
        </div>

        <div class="panel-body mtop10 invoice-item">
            <div class="row">
              <div class="col-md-4">
                <?php $this->load->view('warehouse/item_include/main_item_select'); ?>
              </div>
              <div class="col-md-8 text-right">
                <label class="bold mtop10 text-right" data-toggle="tooltip" title="" data-original-title="<?php echo _l('support_barcode_scanner_tooltip'); ?>"><?php echo _l('support_barcode_scanner'); ?>
                <i class="fa fa-question-circle i_tooltip"></i></label>
              </div>
            </div>

            <div class="table-responsive s_table ">
              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                  <tr>
                    <th></th>
                    <th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('Uniclass Code'); ?></th>
                    <th width="15%" align="left"><?php echo _l('from_stock_name'); ?></th>
                    <th width="15%" align="left"><?php echo _l('to_stock_name'); ?></th>
                    <th width="10%" align="right" class="qty"><?php echo _l('available_quantity'); ?></th>
                    <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
                    <th width="10%" align="right"><?php echo _l('unit_price'); ?></th>
                    <th width="10%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>

                    <th align="center"><i class="fa fa-cog"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <?php echo html_entity_decode($internal_delivery_row_template); ?>
                </tbody>
              </table>
            </div>
            <div class="col-md-8 col-md-offset-4">
              <table class="table text-right">
                <tbody>
                  <tr id="totalmoney">
                    <td><span class="bold"><?php echo _l('total_amount'); ?> :</span>
                    </td>
                    <td class="wh-total">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="removed-items"></div>
          </div>

        <div class="row">
          <div class="col-md-12 mtop15">
             <div class="panel-body bottom-transaction">

                <?php $description = (isset($internal_delivery) ? $internal_delivery->description : ''); ?>
                <?php echo render_textarea('description','note_',$description,array(),array(),'mtop15'); ?>

                <div class="btn-bottom-toolbar text-right">
                  <a href="<?php echo admin_url('warehouse/manage_internal_delivery'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>
                  <?php if (is_admin() || has_permission('warehouse', '', 'edit') || has_permission('warehouse', '', 'create')) { ?>
                    <?php if(isset($internal_delivery) && $internal_delivery->approval == 0){ ?>
                      <button type="button" class="btn btn-info btn_add_internal_delivery ">
                        <?php echo _l('save'); ?>
                      </button>
                    <?php }elseif(!isset($internal_delivery)){ ?>
                      <button type="button" class="btn btn-info btn_add_internal_delivery ">
                        <?php echo _l('save'); ?>
                      </button>
                    <?php } ?>
                  <?php } ?>
                </div>
             </div>
               <div class="btn-bottom-pusher"></div>
          </div>
        </div>
        </div>

			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/warehouse/assets/js/add_edit_internal_delivery_js.php';?>
