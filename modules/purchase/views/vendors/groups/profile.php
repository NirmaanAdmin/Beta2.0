<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="mtop5"><?php echo _l('pur_vendor_add_edit_profile'); ?></h4>
<hr />
<div class="row">
   <?php echo form_hidden('userid', (isset($client) ? $client->userid : '')); ?>
   <?php echo form_open($this->uri->uri_string(), array('class' => 'vendor-form', 'autocomplete' => 'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <div class="horizontal-scrollable-tabs">
         <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
         <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
         <div class="horizontal-tabs">
            <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
               <li role="presentation" class="<?php if (!$this->input->get('tab')) {
                                                   echo 'active';
                                                }; ?>">
                  <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                     <?php echo _l('pur_vendor_detail'); ?>
                  </a>
               </li>
               <?php
               $customer_custom_fields = false;
               if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'vendors', 'active' => 1)) > 0) {
                  $customer_custom_fields = true;
               ?>
                  <li role="presentation" class="<?php if ($this->input->get('tab') == 'custom_fields') {
                                                      echo 'active';
                                                   }; ?>">
                     <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
                        <?php echo _l('custom_fields'); ?>
                     </a>
                  </li>
               <?php } ?>
               <!-- <li role="presentation">
                  <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                  <?php echo _l('billing_shipping'); ?>
                  </a>
               </li> -->

               <!-- <li role="presentation">
                  <a href="#return_policies" aria-controls="return_policies" role="tab" data-toggle="tab">
                  <?php echo _l('pur_return_policies'); ?>
                  </a>
               </li> -->

               <?php if (isset($client)) { ?>
                  <li role="presentation">
                     <a href="#vendor_admins" aria-controls="vendor_admins" role="tab" data-toggle="tab">
                        <?php echo _l('vendor_admins'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#vendor_ratings" aria-controls="vendor_ratings" role="tab" data-toggle="tab">
                        <?php echo _l('vendor_ratings'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#vendor_information" aria-controls="vendor_information" role="tab" data-toggle="tab">
                        <?php echo _l('vendor_information'); ?>
                     </a>
                  </li>

               <?php } ?>
            </ul>
         </div>
      </div>
      <div class="tab-content">

         <?php if ($customer_custom_fields) { ?>
            <div role="tabpanel" class="tab-pane <?php if ($this->input->get('tab') == 'custom_fields') {
                                                      echo ' active';
                                                   }; ?>" id="custom_fields">
               <?php $rel_id = (isset($client) ? $client->userid : false); ?>
               <?php echo render_custom_fields('vendors', $rel_id); ?>
            </div>
         <?php } ?>
         <div role="tabpanel" class="tab-pane<?php if (!$this->input->get('tab')) {
                                                echo ' active';
                                             }; ?>" id="contact_info">
            <div class="row">
               <div class="col-md-12<?php if (isset($client) && (!is_empty_customer_company($client->userid) && total_rows(db_prefix() . 'contacts', array('userid' => $client->userid, 'is_primary' => 1)) > 0)) {
                                       echo '';
                                    } else {
                                       echo ' hide';
                                    } ?>" id="client-show-primary-contact-wrapper">
                  <div class="checkbox checkbox-info mbot20 no-mtop">
                     <input type="checkbox" name="show_primary_contact" <?php if (isset($client) && $client->show_primary_contact == 1) {
                                                                           echo ' checked';
                                                                        } ?> value="1" id="show_primary_contact">
                     <label for="show_primary_contact"><?php echo _l('show_primary_contact', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('payments') . ', ' . _l('credit_notes')); ?></label>
                  </div>
               </div>
               <div class="col-md-6">
                  <?php $vendor_code = (isset($client) ? $client->vendor_code : '');
                  echo render_input('vendor_code', 'vendor_code', $vendor_code, 'text'); ?>
                  <?php $value = (isset($client) ? $client->company : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus' => true)); ?>
                  <?php echo render_input('company', 'client_company', $value, 'text', $attrs); ?>
                  <div id="company_exists_info" class="hide"></div>
                  <!-- <?php hooks()->do_action('after_pur_vendor_profile_company_field', $client ?? null); ?> -->
                  <?php
                  $value = (isset($client) ? $client->com_email : '');
                  echo render_input('com_email', 'Company Email', $value, 'email');
                  ?>
                  <?php
                  $value = (isset($client) ? $client->pan_number : '');
                  echo render_input('pan_number', 'Pan Number', $value);
                  ?>
                  <?php
                  $value = (isset($client) ? $client->vat : '');
                  echo render_input('vat', 'vendor_vat', $value);
                  ?>
                  <?php $value = (isset($client) ? $client->phonenumber : ''); ?>
                  <?php echo render_input('phonenumber', 'client_phonenumber', $value); ?>
                  <?php if ((isset($client) && empty($client->website)) || !isset($client)) {
                     $value = (isset($client) ? $client->website : '');
                     echo render_input('website', 'client_website', $value);
                  } else { ?>
                     <div class="form-group">
                        <label for="website"><?php echo _l('client_website'); ?></label>
                        <div class="input-group">
                           <input type="text" name="website" id="website" value="<?php echo pur_html_entity_decode($client->website); ?>" class="form-control">
                           <div class="input-group-addon">
                              <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                           </div>
                        </div>
                     </div>
                  <?php } ?>

                  <div class="form-group">

                     <label for="category"><?php echo _l('vendor_category'); ?></label>
                     <select name="category[]" id="category" class="selectpicker" data-live-search="true" multiple data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <?php foreach ($vendor_categories as $vc) { ?>
                           <option value="<?php echo pur_html_entity_decode($vc['id']); ?>" <?php if (isset($client) && in_array($vc['id'], explode(',', $client->category))) {
                                                                                                echo 'selected';
                                                                                             } ?>><?php echo pur_html_entity_decode($vc['category_name']); ?></option>
                        <?php } ?>
                     </select>
                  </div>

                  <?php if (!isset($client)) { ?>
                     <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                  <?php }
                  $s_attrs = array('data-none-selected-text' => _l('system_default_string'));
                  $selected = '';

                  foreach ($currencies as $currency) {
                     if (isset($client)) {
                        if ($currency['id'] == $client->default_currency) {
                           $selected = $currency['id'];
                        }
                     }
                  }
                  // Do not remove the currency field from the customer profile!
                  echo render_select('default_currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $s_attrs); ?>
                  <?php if (get_option('disable_language') == 0) { ?>
                     <div class="form-group select-placeholder">
                        <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                        </label>
                        <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('system_default_string'); ?></option>
                           <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                              $selected = '';
                              if (isset($client)) {
                                 if ($client->default_language == $availableLanguage) {
                                    $selected = 'selected';
                                 }
                              }
                           ?>
                              <option value="<?php echo pur_html_entity_decode($availableLanguage); ?>" <?php echo pur_html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  <?php } ?>
                  <div class="form-group">
                     <label for="category"><?php echo _l('Created By'); ?> :-</label>
                     <?php 
                     if($client->addedfrom > 0){
                        echo $staff_name = get_staff_full_name($client->addedfrom); 
                     }else{
                        echo $client->company;
                     }
                     ?>
                  </div>
               </div>
               <div class="col-md-6">
                  <?php $value = (isset($client) ? $client->address : ''); ?>
                  <?php echo render_textarea('address', 'client_address', $value); ?>
                  <?php $value = (isset($client) ? $client->city : ''); ?>
                  <?php echo render_input('city', 'client_city', $value); ?>
                  <?php
                  $states = [
                      'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                      'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
                      'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
                      'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
                      'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
                      'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Andaman and Nicobar Islands',
                      'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi',
                      'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
                  ];
                  $state_options = [];
                  foreach ($states as $state) {
                     $state_options[] = ['id' => $state, 'name' => $state];
                  }
                  $selected = isset($client) ? $client->state : '';
                  echo render_select('state', $state_options, ['id', 'name'], 'client_state', $selected);
                  ?>
                  <?php $value = (isset($client) ? $client->zip : ''); ?>
                  <?php echo render_input('zip', 'client_postal_code', $value); ?>
                  <?php $countries = get_all_countries();
                  $customer_default_country = get_option('customer_default_country');
                  $selected = (isset($client) ? $client->country : $customer_default_country);
                  echo render_select('country', $countries, array('country_id', array('short_name')), 'clients_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                  ?>
                  <?php $bank_detail = (isset($client) ? $client->bank_detail : ''); ?>
                  <?php echo render_textarea('bank_detail', 'bank_detail', $bank_detail); ?>
                  <?php $preferred_location = (isset($client) ? $client->preferred_location : ''); ?>
                  <?php echo render_textarea('preferred_location', 'Preferred Location', $preferred_location); ?>

               </div>
            </div>
         </div>
         <?php if (isset($client)) { ?>
            <div role="tabpanel" class="tab-pane" id="vendor_admins">
               <?php if (has_permission('purchase_vendors', '', 'create') || has_permission('purchase_vendors', '', 'edit')) { ?>
                  <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
               <?php } ?>
               <table class="table dt-table">
                  <thead>
                     <tr>
                        <th><?php echo _l('staff_member'); ?></th>
                        <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                        <?php if (has_permission('purchase_vendors', '', 'create') || has_permission('purchase_vendors', '', 'edit')) { ?>
                           <th><?php echo _l('options'); ?></th>
                        <?php } ?>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($customer_admins as $c_admin) { ?>
                        <tr>
                           <td><a href="<?php echo admin_url('profile/' . $c_admin['staff_id']); ?>">
                                 <?php echo staff_profile_image($c_admin['staff_id'], array(
                                    'staff-profile-image-small',
                                    'mright5'
                                 ));
                                 echo get_staff_full_name($c_admin['staff_id']); ?></a>
                           </td>
                           <td data-order="<?php echo pur_html_entity_decode($c_admin['date_assigned']); ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                           <?php if (has_permission('purchase_vendors', '', 'create') || has_permission('purchase_vendors', '', 'edit')) { ?>
                              <td>
                                 <a href="<?php echo admin_url('purchase/delete_vendor_admin/' . $client->userid . '/' . $c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                              </td>
                           <?php } ?>
                        </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
         <?php } ?>
         <div role="tabpanel" class="tab-pane" id="vendor_ratings">
            <?php //load view 
            if ($is_edit) {
               $this->load->view('vendor_ratings');
            }

            ?>
         </div>
         <div role="tabpanel" class="tab-pane" id="vendor_information">
            <?php //load view 
            if ($is_edit) {
               $this->load->view('vendor_information');
            }

            ?>
         </div>
         <!-- <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-6">
                        <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                        <hr />
                        <?php $value = (isset($client) ? $client->billing_street : ''); ?>
                        <?php echo render_textarea('billing_street', 'billing_street', $value); ?>
                        <?php $value = (isset($client) ? $client->billing_city : ''); ?>
                        <?php echo render_input('billing_city', 'billing_city', $value); ?>
                        <?php $value = (isset($client) ? $client->billing_state : ''); ?>
                        <?php echo render_input('billing_state', 'billing_state', $value); ?>
                        <?php $value = (isset($client) ? $client->billing_zip : ''); ?>
                        <?php echo render_input('billing_zip', 'billing_zip', $value); ?>
                        <?php $selected = (isset($client) ? $client->billing_country : ''); ?>
                        <?php echo render_select('billing_country', $countries, array('country_id', array('short_name')), 'billing_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <div class="col-md-6">
                        <h4 class="no-mtop">
                           <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                           <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                        </h4>
                        <hr />
                        <?php $value = (isset($client) ? $client->shipping_street : ''); ?>
                        <?php echo render_textarea('shipping_street', 'shipping_street', $value); ?>
                        <?php $value = (isset($client) ? $client->shipping_city : ''); ?>
                        <?php echo render_input('shipping_city', 'shipping_city', $value); ?>
                        <?php $value = (isset($client) ? $client->shipping_state : ''); ?>
                        <?php echo render_input('shipping_state', 'shipping_state', $value); ?>
                        <?php $value = (isset($client) ? $client->shipping_zip : ''); ?>
                        <?php echo render_input('shipping_zip', 'shipping_zip', $value); ?>
                        <?php $selected = (isset($client) ? $client->shipping_country : ''); ?>
                        <?php echo render_select('shipping_country', $countries, array('country_id', array('short_name')), 'shipping_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <?php if (
                        isset($client) &&
                        (total_rows(db_prefix() . 'invoices', array('clientid' => $client->userid)) > 0 || total_rows(db_prefix() . 'estimates', array('clientid' => $client->userid)) > 0 || total_rows(db_prefix() . 'creditnotes', array('clientid' => $client->userid)) > 0)
                     ) { ?>
                     <div class="col-md-12">
                        <div class="alert alert-warning">
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_all_other_transactions" id="update_all_other_transactions">
                              <label for="update_all_other_transactions">
                              <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                              </label>
                           </div>
                           <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_credit_notes" id="update_credit_notes">
                              <label for="update_credit_notes">
                              <?php echo _l('customer_profile_update_credit_notes'); ?><br />
                              </label>
                           </div>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div> -->

         <!-- <div role="tabpanel" class="tab-pane" id="return_policies">
            <div class="row">
               <div class="col-md-6">
                   <?php $return_within_day = (isset($client->return_within_day) &&  $client->return_within_day != null) ? $client->return_within_day : get_option('pur_return_request_within_x_day');
                     echo render_input('return_within_day', 'pur_return_request_within_x_day', $return_within_day, 'number', ['min' => 1]); ?>
               </div>
               <div class="col-md-6">
                   <?php $return_order_fee = (isset($client) ? $client->return_order_fee : '');
                     echo render_input('return_order_fee', 'pur_fee_for_return_order', $return_order_fee, 'number'); ?>
               </div>
               <div class="col-md-12">
                   <?php $return_policies = (isset($client) ? $client->return_policies : '');
                     echo render_textarea('return_policies', 'pur_return_policies_information', $return_policies, array(), array()); ?>  
               </div>
            </div>
         </div> -->

      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<?php if (isset($client)) { ?>
   <?php if (has_permission('purchase_vendors', '', 'create') || has_permission('purchase_vendors', '', 'edit')) { ?>
      <div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
         <div class="modal-dialog">
            <?php echo form_open(admin_url('purchase/assign_vendor_admins/' . $client->userid)); ?>
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
               </div>
               <div class="modal-body">
                  <?php
                  $selected = array();
                  foreach ($customer_admins as $c_admin) {
                     array_push($selected, $c_admin['staff_id']);
                  }
                  echo render_select('customer_admins[]', $staff, array('staffid', array('firstname', 'lastname')), '', $selected, array('multiple' => true), array(), '', '', false); ?>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
               </div>
            </div>
            <!-- /.modal-content -->
            <?php echo form_close(); ?>
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
   <?php } ?>
<?php } ?>