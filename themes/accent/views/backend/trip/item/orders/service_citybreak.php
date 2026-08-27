<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/service_citybreak/search_scripts.php'); ?>
<?php 
$this->_ci->load->model('Options_model');
$citybreak_settings = $this->_ci->Options_model->get('trip_citybreak_settings');
$arival_locations = array();
if(isset($citybreak_settings['arival_locations']) && is_array($citybreak_settings['arival_locations'])){
  $arival_locations = $citybreak_settings['arival_locations'];
}
$departure_locations = array();
if(isset($citybreak_settings['departure_locations']) && is_array($citybreak_settings['departure_locations'])){
  $departure_locations = $citybreak_settings['departure_locations'];
}
$departure_date = '';
if($this->citybreak_search_data['departure_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->citybreak_search_data['departure_date']);
  $departure_date = $date ? $date->format('d.m.Y') : '';
}
$return_date = '';
if($this->citybreak_search_data['return_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->citybreak_search_data['return_date']);
  $return_date = $date ? $date->format('d.m.Y') : '';
}
?>
<div class="row ml-0 mr-0">
  <div class="col-12 col-xl-6 mb-1 pl-1 pr-0">
    <div class="card">
      <div class="card-header pl-2 pr-2 pb-2 pt-2" role="tab" id="service_citybreak_form_header">
        <a data-toggle="collapse" href="#service_citybreak_form_container" aria-expanded="true" aria-controls="service_citybreak_form_container" class="nounderline d-flex align-items-center justify-content-between">
          <span><i class="fa fa-search"></i></span>
          <strong>Formular cautare</strong>
          <span><i class="fa fa-eye"></i></span>
        </a>
      </div>
      <div id="service_citybreak_form_container" class="collapse show" role="tabpanel" aria-labelledby="service_citybreak_form_header">
        <div class="card-block p-1">
          <form id="service_citybreak_form" name="service_citybreak_form" action="<?php echo site_url('backend/trip/citybreaks/setSearch'); ?>" method="POST" onsubmit="return false;">
            <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
            <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
            <?php } ?>
            <input type="hidden" id="service_citybreak_search_origin_location_id" name="origin_location_id" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_location_id']); ?>" />
            <input type="hidden" id="service_citybreak_search_origin_location_name" name="origin_location_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_location_name']); ?>" />
            <input type="hidden" id="service_citybreak_search_origin_city_id" name="origin_city_id" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_city_id']); ?>" />
            <input type="hidden" id="service_citybreak_search_origin_city_name" name="origin_city_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_city_name']); ?>" />
            <input type="hidden" id="service_citybreak_search_origin_country_id" name="origin_country_id" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_country_id']); ?>" />
            <input type="hidden" id="service_citybreak_search_origin_country_name" name="origin_country_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_country_name']); ?>" />
            <input type="hidden" id="service_citybreak_search_origin_full_location_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_full_location_name']); ?>" />
            
            <input type="hidden" id="service_citybreak_search_destination_location_id" name="destination_location_id" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_location_id']); ?>" />
            <input type="hidden" id="service_citybreak_search_destination_location_name" name="destination_location_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_location_name']); ?>" />
            <input type="hidden" id="service_citybreak_search_destination_city_id" name="destination_city_id" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_city_id']); ?>" />
            <input type="hidden" id="service_citybreak_search_destination_city_name" name="destination_city_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_city_name']); ?>" />
            <input type="hidden" id="service_citybreak_search_destination_country_id" name="destination_country_id" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_country_id']); ?>" />
            <input type="hidden" id="service_citybreak_search_destination_country_name" name="destination_country_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_country_name']); ?>" />
            <input type="hidden" id="service_citybreak_search_destination_full_location_name" value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_full_location_name']); ?>" />

            <input type="hidden" id="service_citybreak_search_search_type" name="search_type" />
            
            <div id="service_citybreak_search_hotel_room_info">
            </div>
            <div class="row no-gutters">
              <div class="form-group col-12 col-sm-6 mb-1">
                <label class="input-group mb-0">
                  <select id="service_citybreak_search_origin_location" class="form-control">
                    <option value="">Plecare din</option><?php 
                    foreach($departure_locations as $location_index=>$departure_location) {
                      list($country_id, $city_id, $location_id) = explode('-',$location_index);
                      $option_value = $city_id . '-' . $location_id;
                      $option_text = ($location_id > 0 ? $departure_location['location'] . ', ' : '') . $departure_location['city'];
                      $custom_text = trim($departure_location['text']);
                      $selected_expression = ($this->citybreak_search_data['origin_city_id'] . '-' . $this->citybreak_search_data['origin_location_id']) == $option_value ? 'selected="selected"' : '';
                      if(strlen($custom_text)>0){
                        $option_text = $custom_text;
                      } ?>
                    <option 
                      value="<?php echo $option_value; ?>" 
                      <?php echo $selected_expression; ?> 
                      data-country_id="<?php echo htmlspecialchars($country_id); ?>"
                      data-country_name="<?php echo htmlspecialchars($departure_location['country']); ?>"
                      data-city_id="<?php echo htmlspecialchars($city_id); ?>"
                      data-city_name="<?php echo htmlspecialchars($departure_location['city']); ?>"
                      data-location_id="<?php echo htmlspecialchars($location_id); ?>"
                      data-location_name="<?php echo htmlspecialchars($departure_location['location']); ?>"
                      ><?php echo $option_text; ?></option>
                    <?php } ?>
                  </select>
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-upload"></i>
                  </span>
                </label>
              </div>
              <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1">
                <label class="input-group mb-0">
                  <select id="service_citybreak_search_destination_location" class="form-control" >
                    <option value="">Sosire in</option><?php 
                    foreach($arival_locations as $location_index=>$arival_location) {
                      list($country_id, $city_id, $location_id) = explode('-',$location_index);
                      $option_value = $city_id . '-' . $location_id;
                      $option_text = ($location_id > 0 ? $arival_location['location'] . ', ' : '') . $arival_location['city'];
                      $custom_text = trim($arival_location['text']);
                      $selected_expression = ($this->citybreak_search_data['destination_city_id'] . '-' . $this->citybreak_search_data['destination_location_id']) == $option_value ? 'selected="selected"' : '';
                      if(strlen($custom_text)>0){
                        $option_text = $custom_text;
                      } ?>
                    <option 
                      value="<?php echo $option_value; ?>" 
                      <?php echo $selected_expression; ?> 
                      data-country_id="<?php echo htmlspecialchars($country_id); ?>"
                      data-country_name="<?php echo htmlspecialchars($arival_location['country']); ?>"
                      data-city_id="<?php echo htmlspecialchars($city_id); ?>"
                      data-city_name="<?php echo htmlspecialchars($arival_location['city']); ?>"
                      data-location_id="<?php echo htmlspecialchars($location_id); ?>"
                      data-location_name="<?php echo htmlspecialchars($arival_location['location']); ?>"
                      ><?php echo $option_text; ?></option>
                    <?php } ?>
                  </select>
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-download"></i>
                  </span>
                </label>
              </div>
              <div class="form-group col-12 col-sm-6 mb-1 has-danger">
                <label class="input-group mb-0">
                  <input type="text" maxlength="255" class="form-control" name="origin_full_location_name" id="service_citybreak_search_origin" placeholder="Plecare din" required value="<?php echo htmlspecialchars($this->citybreak_search_data['origin_full_location_name']); ?>" />
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-upload"></i>
                  </span>
                </label>
              </div>
              <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1 has-danger">
                <label class="input-group mb-0">
                  <input type="text" maxlength="255" class="form-control" name="destination_full_location_name" id="service_citybreak_search_destination" placeholder="Sosire in" required value="<?php echo htmlspecialchars($this->citybreak_search_data['destination_full_location_name']); ?>" />
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-download"></i>
                  </span>
                </label>
              </div>
              <div class="form-group col-12 col-sm-6 mb-1 has-danger">
                <label class="input-group mb-0">
                  <input type="text" maxlength="255" class="form-control" name="departure_date" id="service_citybreak_search_departure_date" placeholder="Data plecare" required value="<?php echo htmlspecialchars($departure_date); ?>" />
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-calendar-check-o"></i>
                  </span>
                </label>
              </div>
              <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1">
                <div class="input-group">
                  <label class="input-group-addon mb-0" data-toggle="tooltip" title="Zbor tur-retur">
                    <input type="hidden"  name="go_only" value="1" />
                    <input type="checkbox" id="service_citybreak_search_return" name="go_only" value="0" <?php echo $this->citybreak_search_data['go_only'] ? '' : 'checked'; ?>/>
                  </label>
                  <label class="input-group mb-0 <?php echo $this->citybreak_search_data['go_only'] ? '' : 'has-danger'; ?>" data-toggle="tooltip" title="Data retur" >
                    <input type="text" maxlength="255" class="form-control rounded-0" name="return_date" id="service_citybreak_search_return_date" required placeholder="Data retur" <?php echo $this->citybreak_search_data['go_only'] ? 'disabled' : ''; ?> value="<?php echo htmlspecialchars($return_date); ?>" />
                    <span class="input-group-addon adjust-addon-width">
                      <i class="fa fa-calendar-times-o"></i>
                    </span>
                  </label>
                </div>
              </div>
              <div class="form-group col-12 col-sm-4 mb-1 has-danger">
                <select class="form-control" name="cabine_type" id="service_citybreak_search_cabine_type" required>
                  <option value="1" <?php echo $this->citybreak_search_data['cabine_type'] == 1 ? 'selected' : ''; ?>>Economy</option>
                  <option value="2" <?php echo $this->citybreak_search_data['cabine_type'] == 2 ? 'selected' : ''; ?>>First class</option>
                  <option value="3" <?php echo $this->citybreak_search_data['cabine_type'] == 3 ? 'selected' : ''; ?>>Business</option>
                  <option value="4" <?php echo $this->citybreak_search_data['cabine_type'] == 4 ? 'selected' : ''; ?>>Premium</option>
                </select>
              </div>
              <div class="form-group col-12 col-sm-8 mb-1 pl-sm-1 d-flex align-items-center justify-content-center">
                <div class="form-check form-check-inline mb-0">
                  <label class="form-check-label mb-0">
                    <input class="form-check-input" type="checkbox" id="service_citybreak_search_direct_only" name="direct_only" <?php echo $this->citybreak_search_data['direct_only'] ? 'checked' : ''; ?> value="1" />
                    <span>Fara escale</span>
                  </label>
                </div>
                <div class="form-check form-check-inline mb-0">
                  <label class="form-check-label mb-0">
                    <input class="form-check-input" type="checkbox" id="service_citybreak_search_flex_dates" name="flex_dates" <?php echo $this->citybreak_search_data['flex_dates'] ? 'checked' : ''; ?> value="1" />
                    <span>Date flexibile</span>
                  </label>
                </div>
              </div>
            </div>
            <input type="hidden" name="passengers_adult" value="<?php echo htmlspecialchars($this->citybreak_search_data['passengers_adult']); ?>" id="service_citybreak_search_passengers_adult" />
            <input type="hidden" name="passengers_senior" value="<?php echo htmlspecialchars($this->citybreak_search_data['passengers_senior']); ?>" id="service_citybreak_search_passengers_senior" />
            <input type="hidden" name="passengers_child" value="<?php echo htmlspecialchars($this->citybreak_search_data['passengers_child']); ?>" id="service_citybreak_search_passengers_child" />
            <input type="hidden" name="passengers_infant_lap" value="<?php echo htmlspecialchars($this->citybreak_search_data['passengers_infant_lap']); ?>" id="service_citybreak_search_passengers_infant_lap" />
            <input type="hidden" name="passengers_infant_seat" value="<?php echo htmlspecialchars($this->citybreak_search_data['passengers_infant_seat']); ?>" id="service_citybreak_search_passengers_infant_seat" />
            <div class="table-responsive">
              <table id="service_citybreak_search_passenger_birthdates_table" class="table table-bordered table-striped ac mb-1 mb-0">
                <thead>
                  <tr>
                    <th style="width:1%" data-toggle="tooltip" class="text-center" title="Camera nr." ><i class="fa fa-tag"></i></th>
                    <th style="width:1%" data-toggle="tooltip" class="text-center" title="Pasager nr." ><i class="fa fa-male"></i></th>
                    <th class="text-center" style="width:70px;"><i class="fa fa-user hidden-sm-up"></i><span class="hidden-xs-down"> Varsta</span></th>
                    <th class="text-center" width="140px;"><i class="fa fa-calendar hidden-sm-up"></i><span class="hidden-xs-down"> Data nastere</span></th>
                    <th class="text-center" style="min-width:150px;"><i class="fa fa-user hidden-sm-up"></i><span class="hidden-xs-down"> Clasa</span></th>
                    <th colspan="2" style="width:1%" class="contains-form-control"><button id="service_citybreak_search_add_passenger_room" type="button" class="btn btn-primary btn-block btn-add-room"><i class="fa fa-plus"></i></button></th>
                  </tr>
                </thead>
                <tbody id="service_citybreak_search_passenger_birthdates_table_tbody"></tbody>
                <tfoot id="service_citybreak_search_passenger_birthdates_table_tfoot">
                  <tr>
                    <td colspan="7" class="pt-1 pb-0 pl-0 pr-1">
                      <div class="row ml-0 mr-0">
                        <div data-toggle="tooltip" title="Nr. camere" class="col-md-4 pl-1 pr-0 pb-1">
                          <div class="input-group">
                            <span class="input-group-addon adjust-addon-width">
                              <i class="fa fa-tags"></i>
                            </span>
                            <div class="form-control" readonly><strong id="service_citybreak_search_room_count">0</strong></div>
                          </div>
                        </div>
                        <div data-toggle="tooltip" title="Nr. adulti" class="col-md-4 pl-1 pr-0 pb-1">
                          <div class="input-group">
                            <span class="input-group-addon adjust-addon-width">
                              <i class="fa fa-male"></i>
                            </span>
                            <div class="form-control" readonly><strong id="service_citybreak_search_adult_count">0</strong></div>
                          </div>
                        </div>
                        <div data-toggle="tooltip" title="Nr. seniori" class="col-md-4 pl-1 pr-0 pb-1">
                          <div class="input-group">
                            <span class="input-group-addon adjust-addon-width">
                              <i class="fa fa-user-secret"></i>
                            </span>
                            <div class="form-control" readonly><strong id="service_citybreak_search_senior_count">0</strong></div>
                          </div>
                        </div>
                        <div data-toggle="tooltip" title="Nr. copii" class="col-md-4 pl-1 pr-0 pb-1">
                          <div class="input-group">
                            <span class="input-group-addon adjust-addon-width">
                              <i class="fa fa-child"></i>
                            </span>
                            <div class="form-control" readonly><strong id="service_citybreak_search_child_count">0</strong></div>
                          </div>
                        </div>
                        <div data-toggle="tooltip" title="Nr. infanti in brate" class="col-md-4 pl-1 pr-0 pb-1">
                          <div class="input-group">
                            <span class="input-group-addon adjust-addon-width">
                              <i class="fa fa-bug"></i>
                            </span>
                            <div class="form-control" readonly><strong id="service_citybreak_search_infant_lap_count">0</strong></div>
                          </div>
                        </div>
                        <div data-toggle="tooltip" title="Nr. infanti in scaun" class="col-md-4 pl-1 pr-0 pb-1">
                          <div class="input-group">
                            <span class="input-group-addon adjust-addon-width">
                              <i class="fa fa-bug"></i>
                            </span>
                            <div class="form-control" readonly><strong id="service_citybreak_search_infant_seat_count">0</strong></div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="d-flex align-items-center justify-content-end">
              <button type="submit" id="service_citybreak_search_submit_flight" name="flight" class="btn btn-success"><i class="fa fa-plane"></i> Cauta Zbor</button>
            </div>
            <h3>Check-in/out Hotel</h3>
            <div id="service_citybreak_search_hotel_dates" class="row no-gutters mt-1">
              <div class="form-group col-12 col-sm-6 mb-1">
                <label class="input-group mb-0">
                  <input type="text" maxlength="255" class="form-control" name="start_date" id="service_citybreak_search_start_date" placeholder="Check-in" readonly/>
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-calendar-check-o"></i>
                  </span>
                </label>
              </div>
              <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1">
                <label class="input-group mb-0">
                  <input type="text" maxlength="255" class="form-control" name="end_date" id="service_citybreak_search_end_date" placeholder="Check-out" readonly/>
                  <span class="input-group-addon adjust-addon-width">
                    <i class="fa fa-calendar-times-o"></i>
                  </span>
                </label>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
              <button disabled type="submit" id="service_citybreak_search_submit_hotel" name="hotel" class="btn btn-success disabled" title="Cautati un zbor intai"><i class="fa fa-building"></i> Cauta hotel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div id="result_service_citybreak_form" class="form-group mb-1"></div>
  </div>
  <div class="col-12 col-xl-6 mb-1 pl-1 pr-0">
    <div class="card">
      <div class="card-header pt-1 pb-1 pl-2 pr-2" role="tab" id="service_citybreak_form_fellows_form_header">
        <a data-toggle="collapse" href="#service_citybreak_form_fellows_form_container" aria-expanded="true" aria-controls="service_citybreak_form_fellows_form_container" class="d-flex align-items-center justify-content-between nounderline pb-2 pt-1">
          <span><i class="fa fa-users"></i></span> 
          <strong>Informatii rezervare</strong>
          <span><i class="fa fa-eye"></i></span>
        </a>
      </div>
      <div id="service_citybreak_form_fellows_form_container" class="collapse show" role="tabpanel" aria-labelledby="service_citybreak_form_fellows_form_header">
        <div class="card-block p-1">
          <form id="service_citybreak_form_fellows_form" name="service_citybreak_form_fellows_form" action="<?php echo site_url('backend/trip/orders/addCitybreakService'); ?>" method="POST" onsubmit="return false;">
            <div id="service_citybreak_chosen_hotel"></div>
            <div id="service_citybreak_chosen_hotel_packages"></div>
            <div id="service_citybreak_chosen_flight"></div>
            <div id="service_citybreak_form_fellows"></div>
            <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
            <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
            <?php } ?>
            <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
            <div class="form-group row mb-0">
              <label for="service_citybreak_comment" class="col-12 mb-0">Comentariu</label>
              <div class="col-12">
                <textarea class="form-control" name="comment" rows="3" id="service_citybreak_comment"></textarea>
              </div>
            </div>
            <button type="submit" id="service_citybreak_reserve_submit" class="btn btn-block btn-success mt-2"><i class="fa fa-cube"></i> Adauga serviciul</button>
          </form>
          <div id="result_service_citybreak_form_fellows_form" class="form-group mb-1"></div>
        </div>
      </div>
    </div>
   </div>
</div>
<div id="citybreak-models" style="display:none;">
  <table>
    <tr id="citybreak_passenger_room_model" class="ac_inc citybreak-passenger-room ac_res2">
      <td rowspan="1" class="ac_dis text-center pt-2 pb-1 ac_inc2">&nbsp;</td>
      <td rowspan="1" class="contains-form-control">
        <div class="btn-group btn-group-sm">
          <button type="button" class="btn btn-success btn-add-passenger-birthdate btn-sm"><i class="fa fa-plus"></i></button>
          <button type="button" class="btn btn-danger btn-delete-passenger-room btn-sm"><i class="fa fa-trash"></i></button>
        </div>
      </td>
    </tr>
    <tr id="citybreak_passenger_birthdate_model" class="ac_inc2 citybreak-passenger-birthdate">
      <td class="ac_dis2 text-center pt-2 pb-1">&nbsp;</td>
      <td class="text-center contains-form-control has-danger">
        <div class="input-group input-group-sm">
          <input type="number" min="0" step="1" max="150" class="passenger-age form-control text-center" required />
        </div>
      </td>
      <td class="contains-form-control">
        <label class="input-group input-group-sm mb-0">
          <input type="text" maxlength="10" class="form-control passenger-birth-date" placeholder="Data nasterii" />
          <span class="input-group-addon adjust-addon-width">
            <i class="fa fa-calendar"></i>
          </span>
        </label>
      </td>
      <td class="text-center passenger-types contains-form-control">
        <div class="passenger-type passenger-type-adult input-group input-group-sm" style="display:none;">
          <span class="input-group-addon">
            <i class="fa fa-male"></i>
          </span>
          <div class="form-control">
            <small><strong>Adult</strong></small>
          </div>
        </div>
        <div class="passenger-type passenger-type-senior input-group input-group-sm" style="display:none;">
          <span class="input-group-addon">
            <i class="fa fa-user-secret"></i>
          </span>
          <div class="form-control">
            <small><strong>Senior</strong></small>
          </div>
        </div>
        <div class="passenger-type passenger-type-child input-group input-group-sm" style="display:none;">
          <span class="input-group-addon">
            <i class="fa fa-child"></i>
          </span>
          <div class="form-control">
            <small><strong>Copil</strong></small>
          </div>
        </div>
        <div class="passenger-type passenger-type-infant input-group input-group-sm" style="display:none;">
          <span class="input-group-addon">
            <i class="fa fa-bug"></i>
          </span>
          <div class="input-group input-group-sm passenger-type-infant-detail">
            <div class="form-control">
              <small><strong>Infant</strong> <span class="passenger-type-infant-lap">brate</span><span class="passenger-type-infant-seat">scaun</span><strong data-toggle="tooltip" class="passenger-type-infant-changed text-warning" title="Impus in brate/scaun" style="display:none;"> <i class="fa fa-warning"></i></strong><strong data-toggle="tooltip" class="passenger-type-infant-lack-adults text-warning" title="Insuficienti adulti/seniori" style="display:none;"> <i class="fa fa-ban"></i></strong></small>
            </div>
            <span class="input-group-btn passenger-type-infant-change">
              <button type="button" class="hasTooltip btn btn-sm btn-passenger-type-infant-change" title="Impune in scaun/brate"><i class="fa fa-refresh"></i></button>
            </span>
          </div>
          <select class="form-control passenger-type-infant-selector" style="display:none;">
            <option value="">- implicit -</option>
            <option value="lap">In brate</option>
            <option value="seat">In scaun</option>
          <select>
        </div>
        <div class="passenger-type passenger-type-indeterminate input-group input-group-sm">
          <span class="input-group-addon">
            <i class="fa fa-question"></i>
          </span>
          <div class="form-control">
            <small><strong>-nedeterminat-</strong></small>
          </div>
        </div>
      </td>
      <td class="contains-form-control">
        <button type="button" class="btn btn-block btn-warning btn-delete-passenger-birthdate btn-sm"><i class="fa fa-times"></i></button>
      </td>
    </tr>
  </table>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>