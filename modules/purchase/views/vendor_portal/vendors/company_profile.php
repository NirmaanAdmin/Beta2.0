<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open_multipart('admin/purchase/vendors_portal/company',['id'=>'company-profile-form']); ?>
        <!-- Required hidden field -->
        <?php echo form_hidden('company_form',true); ?>
        <div class="panel_s section-heading section-company-profile">
            <div class="panel-body">
             <h4 class="no-margin section-text"><?php echo _l('clients_profile_heading'); ?></h4>
         </div>
     </div>
     <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group company-profile-company-group">
                        <label for="company" class="control-label"><?php echo _l('clients_company'); ?></label>
                        <?php
                        $company_val = $client->company;
                        if(!empty($company_val)){
                                // Check if is realy empty client company so we can set this field to empty
                                // The query where fetch the client auto populate firstname and lastname if company is empty
                            if(is_empty_vendor_company($client->userid)){
                                $company_val = '';
                            }
                        }
                        ?>
                        <input type="text" class="form-control" name="company" value="<?php echo set_value('company',$company_val); ?>">
                        <?php echo form_error('company'); ?>
                    </div>
                    <?php if(get_option('company_requires_vat_number_field') == 1){ ?>
                        <div class="form-group company-profile-vat-group">
                            <label for="vat" class="control-label"><?php echo _l('vendor_vat'); ?></label>
                            <input type="text" class="form-control" name="vat" value="<?php echo pur_html_entity_decode($client->vat); ?>">
                        </div>
                    <?php } ?>
                    <div class="form-group company-profile-phone-group">
                        <label for="phonenumber"><?php echo _l('clients_phone'); ?></label>
                        <input type="text" class="form-control" name="phonenumber" id="phonenumber" value="<?php echo pur_html_entity_decode($client->phonenumber); ?>">
                    </div>
                    <div class="form-group company-profile-website-group">
                        <label class="control-label" for="website"><?php echo _l('client_website'); ?></label>
                        <input type="text" class="form-control" name="website" id="website" value="<?php echo pur_html_entity_decode($client->website); ?>">
                    </div>
                    <div class="form-group company-profile-country-group">
                        <label for="lastname"><?php echo _l('clients_country'); ?></label>
                        <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" name="country" class="form-control" id="country">
                            <option value=""></option>
                            <?php foreach(get_all_countries() as $country){ ?>
                                <?php
                                $selected = '';
                                if($client->country == $country['country_id']){echo pur_html_entity_decode($selected = true);}
                                ?>
                                <option value="<?php echo pur_html_entity_decode($country['country_id']); ?>" <?php echo set_select('country', $country['country_id'],$selected); ?>><?php echo pur_html_entity_decode($country['short_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group company-profile-city-group">
                        <label for="city"><?php echo _l('clients_city'); ?></label>
                        <input type="text" class="form-control" name="city" id="city" value="<?php echo pur_html_entity_decode($client->city); ?>">
                    </div>
                    <div class="form-group company-profile-address-group">
                        <label for="address"><?php echo _l('clients_address'); ?></label>
                        <textarea name="address" id="address" class="form-control" rows="7"><?php echo clear_textarea_breaks($client->address); ?></textarea>
                    </div>
                    <div class="form-group company-profile-zip-group">
                        <label for="zip"><?php echo _l('clients_zip'); ?></label>
                        <input type="text" class="form-control" name="zip" id="zip" value="<?php echo pur_html_entity_decode($client->zip); ?>">
                    </div>
                    <div class="form-group company-profile-state-group">
                        <label for="state"><?php echo _l('clients_state'); ?></label>
                        <input type="text" class="form-control" name="state" id="state" value="<?php echo pur_html_entity_decode($client->state); ?>">
                    </div>
                    <?php if(get_option('disable_language') == 0){ ?>
                        <div class="form-group company-profile-language-group">
                            <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                        </label>
                        <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name="default_language" id="default_language" class="form-control selectpicker">
                            <option value="" <?php if($client->default_language == ''){echo 'selected';} ?>><?php echo _l('system_default_string'); ?></option>
                            <?php foreach($this->app->get_available_languages() as $availableLanguage){
                                  $selected = '';
                                  if($client->default_language == $availableLanguage){
                                      $selected = 'selected';
                                  }
                              ?>
                              <option value="<?php echo pur_html_entity_decode($availableLanguage); ?>" <?php echo pur_html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
                          <?php } ?>
                      </select>
                  </div>
              <?php } ?>
          </div>
          <div class="col-md-12 custom-fields">
            <?php echo render_custom_fields('vendors',$client->userid); ?>
        </div>
        <?php if( is_primary_contact_pur()){ ?>
            <div class="col-md-12">
                <h3 class="company-profile-billing-shipping-heading"><?php echo _l('pur_return_policies'); ?></h3>
                <hr class="mbot15"/>
            </div>
            <div class="col-md-6">
                <?php $return_within_day = ($client->return_within_day != null) ? $client->return_within_day : get_option('pur_return_request_within_x_day');
                echo render_input('return_within_day','pur_return_request_within_x_day', $return_within_day , 'number', ['min' => 1]); ?>
            </div>
            <div class="col-md-6">
                <?php echo render_input('return_order_fee','pur_fee_for_return_order',$client->return_order_fee, 'number'); ?>
            </div>
            <div class="col-md-12">
                <?php echo render_textarea('return_policies', 'pur_return_policies_information', $client->return_policies, array(), array(), '', 'tinymce'); ?>  
            </div>

            <div class="col-md-12">
                <h3 class="company-profile-billing-shipping-heading"><?php echo _l('billing_shipping'); ?></h3>
                <hr class="no-mbot"/>
            </div>
            <div class="col-md-6">
                <?php $countries= get_all_countries(); ?>
                <h4 class="mbot15 mtop20 company-profile-billing-address-heading"><?php echo _l('billing_address'); ?></h4>
                <div class="form-group company-profile-billing-street-group">
                    <label for="billing_street"><?php echo _l('billing_street'); ?></label>
                    <textarea name="billing_street" id="billing_street" class="form-control" rows="4"><?php echo clear_textarea_breaks($client->billing_street); ?></textarea>
                </div>
                <div class="form-group company-profile-billing-city-group">
                    <label for="billing_city"><?php echo _l('billing_city'); ?></label>
                    <input type="text" class="form-control" name="billing_city" id="billing_city" value="<?php echo pur_html_entity_decode($client->billing_city); ?>">
                </div>
                <div class="form-group company-profile-billing-state-group">
                    <label for="billing_state"><?php echo _l('billing_state'); ?></label>
                    <input type="text" class="form-control" name="billing_state" id="billing_state" value="<?php echo pur_html_entity_decode($client->billing_state); ?>">
                </div>
                <div class="form-group company-profile-billing-zip-group">
                    <label for="billing_zip"><?php echo _l('billing_zip'); ?></label>
                    <input type="text" class="form-control" name="billing_zip" id="billing_zip" value="<?php echo pur_html_entity_decode($client->billing_zip); ?>">
                </div>
                <div class="form-group company-profile-billing-country-group">
                    <label for="billing_country"><?php echo _l('billing_country'); ?></label>
                    <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name="billing_country" id="billing_country" class="form-control">
                        <option value=""></option>
                        <?php foreach($countries as $country){ ?>
                            <option value="<?php echo pur_html_entity_decode($country['country_id']); ?>"<?php if($client->billing_country == $country['country_id']){echo ' selected';} ?>><?php echo pur_html_entity_decode($country['short_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <h4 class="mbot15 mtop20 company-profile-shipping-address-heading"><?php echo _l('shipping_address'); ?></h4>
                <div class="form-group company-profile-shipping-street-group">
                    <label for="shipping_street"><?php echo _l('shipping_street'); ?></label>
                    <textarea name="shipping_street" id="shipping_street" class="form-control" rows="4"><?php echo clear_textarea_breaks($client->shipping_street); ?></textarea>
                </div>
                <div class="form-group company-profile-shipping-city-group">
                    <label for="shipping_city"><?php echo _l('shipping_city'); ?></label>
                    <input type="text" class="form-control" name="shipping_city" id="shipping_city" value="<?php echo pur_html_entity_decode($client->shipping_city); ?>">
                </div>
                <div class="form-group company-profile-shipping-state-group">
                    <label for="shipping_state"><?php echo _l('shipping_state'); ?></label>
                    <input type="text" class="form-control" name="shipping_state" id="shipping_state" value="<?php echo pur_html_entity_decode($client->shipping_state); ?>">
                </div>
                <div class="form-group company-profile-shipping-zip-group">
                    <label for="shipping_zip"><?php echo _l('shipping_zip'); ?></label>
                    <input type="text" class="form-control" name="shipping_zip" id="shipping_zip" value="<?php echo pur_html_entity_decode($client->shipping_zip); ?>">
                </div>
                <div class="form-group company-profile-shipping-country-group">
                    <label for="shipping_country"><?php echo _l('shipping_country'); ?></label>
                    <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name="shipping_country" id="shipping_country" class="form-control">
                        <option value=""></option>
                        <?php foreach($countries as $country){ ?>
                            <option value="<?php echo pur_html_entity_decode($country['country_id']); ?>"<?php if($client->shipping_country == $country['country_id']){echo ' selected';} ?>><?php echo pur_html_entity_decode($country['short_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>

        <div class="col-md-12">
            <h4 class="mbot15 mtop20 company-profile-shipping-address-heading"><?php echo _l('details_of_work_completed'); ?></h4>
            <div class="table-responsive s_table">
                <table class="table items no-mtop" style="font-size: 15px;">
                    <thead>
                        <tr>
                            <th align="center"><?php echo _l('client'); ?></th>
                            <th align="center"><?php echo _l('type_of_project'); ?></th>
                            <th align="center"><?php echo _l('location'); ?></th>
                            <th align="center"><?php echo _l('mini_contractor'); ?></th>
                            <th align="center"><?php echo _l('scope_of_works'); ?></th>
                            <th align="center"><?php echo _l('contract_prices'); ?></th>
                            <th align="center"><?php echo _l('start_date'); ?></th>
                            <th align="center"><?php echo _l('end_date'); ?></th>
                            <th align="center"><?php echo _l('size_of_project'); ?></th>
                            <th align="center"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody class="work_completed_main">
                        <tr class="item">
                            <td>
                                <input type="text" name="client" class="form-control" placeholder="<?php echo _l('client'); ?>">
                            </td>
                            <td>
                                <input type="text" name="type_of_project" class="form-control" placeholder="<?php echo _l('type_of_project'); ?>">
                            </td>
                            <td>
                                <input type="text" name="location" class="form-control" placeholder="<?php echo _l('location'); ?>">
                            </td>
                            <td>
                                <input type="text" name="mini_contractor" class="form-control" placeholder="<?php echo _l('mini_contractor'); ?>">
                            </td>
                            <td>
                                <input type="text" name="scope_of_works" class="form-control" placeholder="<?php echo _l('scope_of_works'); ?>">
                            </td>
                            <td>
                                <input type="text" name="contract_prices" class="form-control" placeholder="<?php echo _l('contract_prices'); ?>">
                            </td>
                            <td>
                                <input type="text" name="start_date" class="form-control" placeholder="<?php echo _l('start_date'); ?>">
                            </td>
                            <td>
                                <input type="text" name="end_date" class="form-control" placeholder="<?php echo _l('end_date'); ?>">
                            </td>
                            <td>
                                <input type="text" name="size_of_project" class="form-control" placeholder="<?php echo _l('size_of_project'); ?>">
                            </td>
                            <td>
                                <?php
                                $new_item = true;
                                ?>
                                <button type="button" onclick="add_vendor_work_completed_item_to_table('undefined','undefined',<?php echo e($new_item); ?>); return false;"
                                    class="btn pull-right btn-primary"><i class="fa fa-check"></i>
                                </button>
                            </td>
                        </tr>

                        <?php
                        if(!empty($vendor_work_completed)) {
                            $items_indicator = 'workcompleteditems';
                                $i = 1;
                                foreach ($vendor_work_completed as $item) {
                                    $table_row = '<tr class="item">';
                                    $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][id]', $item['id']);
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][client]" value="' . $item['client'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][type_of_project]" value="' . $item['type_of_project'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][location]" value="' . $item['location'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][mini_contractor]" value="' . $item['mini_contractor'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][scope_of_works]" value="' . $item['scope_of_works'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][contract_prices]" value="' . $item['contract_prices'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][start_date]" value="' . $item['start_date'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][end_date]" value="' . $item['end_date'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][size_of_project]" value="' . $item['size_of_project'] . '" class="form-control"></td>';
                                    $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_work_completed(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                                    $table_row .= '</tr>';
                                    echo $table_row;
                                    $i++;
                                }
                            }
                        ?>
                    </tbody>
                </table>
                <div id="removed-work-completed"></div>
            </div>
        </div>

        <div class="col-md-12">
            <h4 class="mbot15 mtop20 company-profile-shipping-address-heading"><?php echo _l('details_of_current_work'); ?></h4>
            <div class="table-responsive s_table">
                <table class="table items no-mtop" style="font-size: 15px;">
                    <thead>
                        <tr>
                            <th align="center"><?php echo _l('client'); ?></th>
                            <th align="center"><?php echo _l('type_of_project'); ?></th>
                            <th align="center"><?php echo _l('location'); ?></th>
                            <th align="center"><?php echo _l('mini_contractor'); ?></th>
                            <th align="center"><?php echo _l('scope_of_works'); ?></th>
                            <th align="center"><?php echo _l('contract_prices'); ?></th>
                            <th align="center"><?php echo _l('start_date'); ?></th>
                            <th align="center"><?php echo _l('proposed_end_date'); ?></th>
                            <th align="center"><?php echo _l('building_height'); ?></th>
                            <th align="center"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody class="work_progress_main">
                        <tr class="item">
                            <td>
                                <input type="text" name="client" class="form-control" placeholder="<?php echo _l('client'); ?>">
                            </td>
                            <td>
                                <input type="text" name="type_of_project" class="form-control" placeholder="<?php echo _l('type_of_project'); ?>">
                            </td>
                            <td>
                                <input type="text" name="location" class="form-control" placeholder="<?php echo _l('location'); ?>">
                            </td>
                            <td>
                                <input type="text" name="mini_contractor" class="form-control" placeholder="<?php echo _l('mini_contractor'); ?>">
                            </td>
                            <td>
                                <input type="text" name="scope_of_works" class="form-control" placeholder="<?php echo _l('scope_of_works'); ?>">
                            </td>
                            <td>
                                <input type="text" name="contract_prices" class="form-control" placeholder="<?php echo _l('contract_prices'); ?>">
                            </td>
                            <td>
                                <input type="text" name="start_date" class="form-control" placeholder="<?php echo _l('start_date'); ?>">
                            </td>
                            <td>
                                <input type="text" name="end_date" class="form-control" placeholder="<?php echo _l('end_date'); ?>">
                            </td>
                            <td>
                                <input type="text" name="size_of_project" class="form-control" placeholder="<?php echo _l('size_of_project'); ?>">
                            </td>
                            <td>
                                <?php
                                $new_item = true;
                                ?>
                                <button type="button" onclick="add_vendor_work_progress_item_to_table('undefined','undefined',<?php echo e($new_item); ?>); return false;"
                                    class="btn pull-right btn-primary"><i class="fa fa-check"></i>
                                </button>
                            </td>
                        </tr>

                        <?php
                        if(!empty($vendor_work_progress)) {
                            $items_indicator = 'workprogressitems';
                                $i = 1;
                                foreach ($vendor_work_progress as $item) {
                                    $table_row = '<tr class="item">';
                                    $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][id]', $item['id']);
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][client]" value="' . $item['client'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][type_of_project]" value="' . $item['type_of_project'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][location]" value="' . $item['location'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][mini_contractor]" value="' . $item['mini_contractor'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][scope_of_works]" value="' . $item['scope_of_works'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][contract_prices]" value="' . $item['contract_prices'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][start_date]" value="' . $item['start_date'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][end_date]" value="' . $item['end_date'] . '" class="form-control"></td>';
                                    $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][size_of_project]" value="' . $item['size_of_project'] . '" class="form-control"></td>';
                                    $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_work_progress(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                                    $table_row .= '</tr>';
                                    echo $table_row;
                                    $i++;
                                }
                            }
                        ?>
                    </tbody>
                </table>
                <div id="removed-work-progress"></div>
            </div>
        </div>


        <?php if($contact->is_primary == 1){ ?>
            <div class="row p15 company-profile-save-section">
                <div class="col-md-12 text-right mtop20">
                    <div class="form-group">
                        <button type="submit" class="btn btn-info company-profile-save">
                            <?php echo _l('clients_edit_profile_update_btn'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>

<?php require 'modules/purchase/assets/js/file_managements/vendor_additional_work_js.php'; ?>
