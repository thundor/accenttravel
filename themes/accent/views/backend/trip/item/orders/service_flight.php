<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/service_flight/search_scripts.php'); ?>
<?php 
$departure_date = '';
if($this->flight_search_data['departure_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->flight_search_data['departure_date']);
  $departure_date = $date ? $date->format('d.m.Y') : '';
}
$return_date = '';
if($this->flight_search_data['return_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->flight_search_data['return_date']);
  $return_date = $date ? $date->format('d.m.Y') : '';
}

 ?>
<div class="row ml-0 mr-0">
  <div class="col-12 col-xl-5 mb-1 pl-1 pr-0">
    <div class="row">
      <div class="col-12 col-lg-6 col-xl-12">
        <div class="card">
          <div class="card-header pl-2 pr-2 pb-2 pt-2" role="tab" id="service_flight_form_header">
            <a data-toggle="collapse" href="#service_flight_form_container" aria-expanded="true" aria-controls="service_flight_form_container" class="nounderline d-flex align-items-center justify-content-between">
              <span><i class="fa fa-search"></i></span>
              <strong>Formular cautare</strong>
              <span><i class="fa fa-eye"></i></span>
            </a>
          </div>
          <div id="service_flight_form_container" class="collapse show" role="tabpanel" aria-labelledby="service_flight_form_header">
            <div class="card-block p-1">
              <form id="service_flight_form" name="service_flight_form" action="<?php echo site_url('backend/trip/flights/setSearch'); ?>" method="POST" onsubmit="return false;">
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" id="service_flight_search_origin_location_id" name="origin_location_id" value="<?php echo htmlspecialchars($this->flight_search_data['origin_location_id']); ?>" />
                <input type="hidden" id="service_flight_search_origin_location_name" name="origin_location_name" value="<?php echo htmlspecialchars($this->flight_search_data['origin_location_name']); ?>" />
                <input type="hidden" id="service_flight_search_origin_city_id" name="origin_city_id" value="<?php echo htmlspecialchars($this->flight_search_data['origin_city_id']); ?>" />
                <input type="hidden" id="service_flight_search_origin_city_name" name="origin_city_name" value="<?php echo htmlspecialchars($this->flight_search_data['origin_city_name']); ?>" />
                <input type="hidden" id="service_flight_search_origin_country_id" name="origin_country_id" value="<?php echo htmlspecialchars($this->flight_search_data['origin_country_id']); ?>" />
                <input type="hidden" id="service_flight_search_origin_country_name" name="origin_country_name" value="<?php echo htmlspecialchars($this->flight_search_data['origin_country_name']); ?>" />
                <input type="hidden" id="service_flight_search_origin_full_location_name" value="<?php echo htmlspecialchars($this->flight_search_data['origin_full_location_name']); ?>" />
                
                <input type="hidden" id="service_flight_search_destination_location_id" name="destination_location_id" value="<?php echo htmlspecialchars($this->flight_search_data['destination_location_id']); ?>" />
                <input type="hidden" id="service_flight_search_destination_location_name" name="destination_location_name" value="<?php echo htmlspecialchars($this->flight_search_data['destination_location_name']); ?>" />
                <input type="hidden" id="service_flight_search_destination_city_id" name="destination_city_id" value="<?php echo htmlspecialchars($this->flight_search_data['destination_city_id']); ?>" />
                <input type="hidden" id="service_flight_search_destination_city_name" name="destination_city_name" value="<?php echo htmlspecialchars($this->flight_search_data['destination_city_name']); ?>" />
                <input type="hidden" id="service_flight_search_destination_country_id" name="destination_country_id" value="<?php echo htmlspecialchars($this->flight_search_data['destination_country_id']); ?>" />
                <input type="hidden" id="service_flight_search_destination_country_name" name="destination_country_name" value="<?php echo htmlspecialchars($this->flight_search_data['destination_country_name']); ?>" />
                <input type="hidden" id="service_flight_search_destination_full_location_name" value="<?php echo htmlspecialchars($this->flight_search_data['destination_full_location_name']); ?>" />
                <div class="row no-gutters">
                  <div class="form-group col-12 col-sm-6 mb-1 has-danger">
                    <label class="input-group mb-0">
                      <input type="text" maxlength="255" class="form-control" name="origin_full_location_name" id="service_flight_search_origin" placeholder="Plecare din" required value="<?php echo htmlspecialchars($this->flight_search_data['origin_full_location_name']); ?>" />
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-upload"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1 has-danger">
                    <label class="input-group mb-0">
                      <input type="text" maxlength="255" class="form-control" name="destination_full_location_name" id="service_flight_search_destination" placeholder="Sosire in" required value="<?php echo htmlspecialchars($this->flight_search_data['destination_full_location_name']); ?>" />
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-download"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 has-danger">
                    <label class="input-group mb-0">
                      <input type="text" maxlength="255" class="form-control" name="departure_date" id="service_flight_search_departure_date" placeholder="Data plecare" required value="<?php echo htmlspecialchars($departure_date); ?>" />
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-calendar-check-o"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1">
                    <div class="input-group">
                      <label class="input-group-addon mb-0">
                        <input type="hidden"  name="go_only" value="1" />
                        <input type="checkbox" id="service_flight_search_return" value="0" name="go_only" <?php echo $this->flight_search_data['go_only'] ? '' : 'checked'; ?>/>
                      </label>
                      <label class="input-group mb-0 <?php echo $this->flight_search_data['go_only'] ? '' : 'has-danger'; ?>">
                        <input type="text" maxlength="255" class="form-control rounded-0" name="return_date" id="service_flight_search_return_date" placeholder="Data retur" <?php echo $this->flight_search_data['go_only'] ? 'disabled' : ''; ?> value="<?php echo htmlspecialchars($return_date); ?>" />
                        <span class="input-group-addon adjust-addon-width">
                          <i class="fa fa-calendar-times-o"></i>
                        </span>
                      </label>
                    </div>
                  </div>
                  <div class="form-group col-12 col-sm-4 mb-1 has-danger">
                    <select class="form-control" name="cabine_type" id="service_flight_search_cabine_type" required>
                      <option value="1" <?php echo $this->flight_search_data['cabine_type'] == 1 ? 'selected' : ''; ?>>Economy</option>
                      <option value="2" <?php echo $this->flight_search_data['cabine_type'] == 2 ? 'selected' : ''; ?>>First class</option>
                      <option value="3" <?php echo $this->flight_search_data['cabine_type'] == 3 ? 'selected' : ''; ?>>Business</option>
                      <option value="4" <?php echo $this->flight_search_data['cabine_type'] == 4 ? 'selected' : ''; ?>>Premium</option>
                    </select>
                  </div>
                  <div class="form-group col-12 col-sm-8 mb-1 pl-sm-1 d-flex align-items-center justify-content-center">
                    <div class="form-check form-check-inline mb-0">
                      <label class="form-check-label mb-0">
                        <input class="form-check-input" type="checkbox" id="service_flight_search_direct_only" name="direct_only" <?php echo $this->flight_search_data['direct_only'] ? 'checked' : ''; ?> value="1" />
                        <span>Fara escale</span>
                      </label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                      <label class="form-check-label mb-0">
                        <input class="form-check-input" type="checkbox" id="service_flight_search_flex_dates" name="flex_dates" <?php echo $this->flight_search_data['flex_dates'] ? 'checked' : ''; ?> value="1" />
                        <span>Date flexibile</span>
                      </label>
                    </div>
                  </div>
                  <div class="form-group col-12 col-sm-4 mb-1 has-warning" data-toggle="tooltip" title="Adulti">
                    <label class="input-group mb-0">
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-male"></i>
                      </span>
                      <input type="number" step="1" min="0" max="999" class="form-control pr-0 pt-0 pb-0" name="passengers_adult" value="<?php echo htmlspecialchars($this->flight_search_data['passengers_adult']); ?>" id="service_flight_search_passengers_adult" placeholder="Adulti" style="min-width:70px;line-height: 35px;"/>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-4 mb-1 pl-sm-1 has-warning">
                    <label class="input-group mb-0" data-toggle="tooltip" title="Seniori">
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-user-secret"></i>
                      </span>
                      <input type="number" step="1" min="0" max="999" class="form-control pr-0 pt-0 pb-0" name="passengers_senior" value="<?php echo htmlspecialchars($this->flight_search_data['passengers_senior']); ?>" id="service_flight_search_passengers_senior" placeholder="Seniori" style="min-width:70px;line-height: 35px;"/>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-4 mb-1 pl-sm-1">
                    <label class="input-group mb-0" data-toggle="tooltip" title="Copii">
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-child"></i>
                      </span>
                      <input type="number" step="1" min="0" max="999" class="form-control pr-0 pt-0 pb-0" name="passengers_child" value="<?php echo htmlspecialchars($this->flight_search_data['passengers_child']); ?>" id="service_flight_search_passengers_child" placeholder="Copii" style="min-width:70px;line-height: 35px;"/>
                    </label>
                  </div>
                  <div class="form-group col-12 mb-1">
                    <div class="input-group">
                      <div class="input-group">
                        <label class="input-group mb-0" data-toggle="tooltip" title="Infanti">
                          <span class="input-group-addon adjust-addon-width rounded-0">
                            <i class="fa fa-bug"></i>
                          </span>
                          <input type="number" step="1" min="0" max="999" class="form-control rounded-0 pr-0 pt-0 pb-0" id="service_flight_search_passengers_infant" value="<?php echo htmlspecialchars($this->flight_search_data['passengers_infant_lap'] + $this->flight_search_data['passengers_infant_seat']); ?>" placeholder="Infant" style="min-width:70px;line-height: 35px;" />
                        </label>
                        <span class="input-group-addon border-left-0 rounded-0" data-toggle="tooltip" title="Determinare manuala brate/scaun">
                          <input type="checkbox" id="service_flight_search_passengers_infant_toggle" value="1"/>
                        </span>
                      </div>
                      <div class="input-group" data-toggle="tooltip" title="Infanti in brate">
                        <input readonly type="number" step="1" min="0" max="999" class="form-control rounded-0 border-left-0 pr-0 pt-0 pb-0" name="passengers_infant_lap" value="<?php echo htmlspecialchars($this->flight_search_data['passengers_infant_lap']); ?>" id="service_flight_search_passengers_infant_lap" placeholder="In brate" style="line-height: 35px;" />
                      </div>
                      <div class="input-group" data-toggle="tooltip" title="Infanti in scaun">
                        <input readonly type="number" step="1" min="0" max="999" class="form-control rounded-0 border-left-0 pr-0 pt-0 pb-0" name="passengers_infant_seat" value="<?php echo htmlspecialchars($this->flight_search_data['passengers_infant_seat']); ?>" id="service_flight_search_passengers_infant_seat" placeholder="In scaun" style="line-height: 35px;" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                  <button type="submit" id="service_flight_search_submit" class="btn btn-success"><i class="fa fa-search"></i> Cauta Zbor</button>
                </div>
              </form>
              <form id="service_flight_search_passenger_birthdates_form" name="service_flight_search_passenger_birthdates_form" onsubmit="return false;">
                <div class="table-responsive">
                  <table id="service_flight_search_passenger_birthdates_table" class="table table-bordered table-striped ac mt-1 mb-0">
                    <thead>
                      <tr>
                        <th style="width:1%" data-toggle="tooltip" class="text-center" title="Pasager nr." >#</th>
                        <th class="text-center" style="width:70px;"><i class="fa fa-user hidden-sm-up"></i><span class="hidden-xs-down"> Varsta</span></th>
                        <th class="text-center" width="140px;"><i class="fa fa-calendar hidden-sm-up"></i><span class="hidden-xs-down"> Data nastere</span></th>
                        <th class="text-center" style="min-width:150px;"><i class="fa fa-user hidden-sm-up"></i><span class="hidden-xs-down"> Clasa</span></th>
                        <th style="width:1%" class="contains-form-control"><button id="service_flight_search_add_passenger_birthdate" type="button" class="btn btn-primary btn-add-room"><i class="fa fa-plus"></i></button></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot style="display:none;">
                      <tr>
                        <td colspan="5" class="contains-form-control">
                          <div class="d-flex align-items-center justify-content-between">
                            <div class="form-check form-check-inline mb-0">
                              <label class="form-check-label mb-0">
                                <input class="form-check-input" type="checkbox" id="service_flight_passengers_auto_determine" checked value="1">
                                <span>Auto-determinare</span>
                              </label>
                            </div>
                            <button type="submit" id="service_flight_search_passenger_birthdates_submit" class="btn btn-primary"><i class="fa fa-arrow-circle-o-down"></i>Completeaza</button>
                          </div>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div id="result_service_flight_form" class="form-group mb-1"></div>
        <div class="form-group mb-1 has-warning" data-toggle="tooltip" title="Odata activat, zborul ales va fi depozitat in tab-ul CityBreak" style="display:none;">
          <label class="input-group mb-0">
            <span class="input-group-addon mb-0">
              <input type="checkbox" id="service_flight_citybreak" value="1" checked>
            </span>
            <div class="form-control">Dezactivare CityBreak</div>
          </label>
        </div>
      </div>
      <div id="service_flight_form_fellows_wrapper" class="col-12 col-lg-6 col-xl-12" style="display:none">
        <div class="card">
          <div class="card-header pt-1 pb-1 pl-2 pr-2" role="tab" id="service_flight_form_fellows_form_header">
            <a data-toggle="collapse" href="#service_flight_form_fellows_form_container" aria-expanded="true" aria-controls="service_flight_form_fellows_form_container" class="d-flex align-items-center justify-content-between nounderline pb-2 pt-1">
              <span><i class="fa fa-users"></i></span> 
              <strong>Informatii rezervare</strong>
              <span><i class="fa fa-eye"></i></span>
            </a>
          </div>
          <div id="service_flight_form_fellows_form_container" class="collapse show" role="tabpanel" aria-labelledby="service_flight_form_fellows_form_header">
            <div class="card-block p-1">
              <form id="service_flight_form_fellows_form" name="service_flight_form_fellows_form" action="<?php echo site_url('backend/trip/orders/addFlightService'); ?>" method="POST" onsubmit="return false;">
				<div id="service_flight_form_fellows"></div>
                <div id="service_flight_chosen_details"></div>
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                <div class="form-group row mb-0">
                  <label for="service_flight_comment" class="col-12 mb-0">Comentariu</label>
                  <div class="col-12">
                    <textarea class="form-control" name="comment" rows="3" id="service_flight_comment"></textarea>
                  </div>
                </div>
                <button type="submit" id="service_flight_reserve_submit" class="btn btn-block btn-success mt-2"><i class="fa fa-cube"></i> Adauga serviciul</button>
              </form>
              <div id="result_service_flight_form_fellows_form" class="form-group mb-1"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-7 mb-1 pl-1 pr-0">
    <div class="card">
      <div class="card-header pt-1 pl-3 pr-3">
        <ul class="nav nav-tabs card-header-tabs nav-justified">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#service_flight_search_results_tab" role="tab" aria-controls="service_flight_search_results_tab"><i class="fa fa-search"></i> Rezultate cautare</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#service_flight_search_filter_tab" role="tab" aria-controls="service_flight_search_filter_tab"><i class="fa fa-filter"></i> Filtre cautare</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#service_flight_search_calendar_tab" role="tab" aria-controls="service_flight_search_calendar_tab"><i class="fa fa-calendar"></i> Calendar tarife</a>
          </li>
        </ul>
      </div>
      <div class="tab-content card-block pl-1 pt-1 pr-1 pb-0">
        <div class="tab-pane active" id="service_flight_search_results_tab" role="tabpanel">
          <div class="sortFlight d-lg-flex align-items-lg-center justify-content-lg-between">
            <form action="#" class="form-inline">
              <div class="input-group mb-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-sort-amount-asc"></i>
                </span>
                <select class="form-control flight-sort-by" id="service_flight_search_sort_price" disabled>
                  <option value="0">Tarif</option>
                  <option value="1">Mic &gt; Mare</option>
                  <option value="2">Mare &gt; Mic</option>
                </select>
              </div>
              <div class="input-group mb-1 ml-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-star"></i>
                </span>
                <select class="form-control flight-sort-by" id="service_flight_search_sort_company" disabled>
                  <option value="0">Companie</option>
                  <option value="1">Alfabetic A &gt; Z</option>
                  <option value="2">Alfabetic Z &gt; A</option>
                </select> 
              </div>
              <div class="input-group mb-1 ml-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-history"></i>
                </span>
                <select class="form-control flight-sort-by" id="service_flight_search_sort_duration" disabled>
                  <option value="0">Durata zbor</option>
                  <option value="1">Scurta &gt; Lunga</option>
                  <option value="2">Lunga &gt; Scurta</option>
                </select> 
              </div>
            </form>
          </div>
          <div id="service_flight_results">
          </div>
        </div>
        <div class="tab-pane" id="service_flight_search_filter_tab" role="tabpanel">
          <div id="service_flight_form_filters">
            <div class="row no-gutters">
              <div class="col-xl-6">
                <div class="card rounded-0 mb-1">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-cogs"></i></span>
                    <strong>General</strong>
                  </div>
                  <div class="card-block">
                    <div class="form-check form-check-inline mb-0">
                      <label class="form-check-label mb-0">
                        <input class="form-check-input" type="checkbox" id="service_flight_search_flexible_dates" name="flexible_dates" value="1" <?php echo $this->flight_search_data['flexible_dates'] ? 'checked' : ''; ?>/>
                        <span>Date flexibile</span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="card rounded-0 mb-1">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-money"></i></span>
                    <strong>Tarif zbor</strong>
                  </div>
                  <div class="card-block">
                    <input type="text" id="flight-services-search-filter-price-slider-amount" class="border-0 mb-1" readonly>
                    <div id="flight-services-search-filter-price-slider-range"></div>
                  </div>
                </div>
                <div class="card rounded-0 mb-1">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-list-ol"></i></span>
                    <strong>Escale</strong>
                  </div>
                  <div class="card-block">
                    <div id="flight-services-search-filter-stops" class="flight-filter flight-stops-filter">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-6 pl-xl-1">
                <div class="card rounded-0 mb-1">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-plane"></i></span>
                    <strong>Comanii</strong>
                  </div>
                  <div class="card-block">
                    <div id="flight-services-search-filter-companies" class="flight-filter flight-companies-filter">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="flight-filters-actions">
              <button name="flight_reset_filters" id="flight_reset_filters" class="btn btn-block btn-warning" type="submit">Sterge Filtre</button>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="service_flight_search_calendar_tab" role="tabpanel">
          <input type="hidden" id="service_flight_search_filter_for_date_departure" />
          <input type="hidden" id="service_flight_search_filter_for_date_return" />
          <input type="hidden" id="flightsFilterForDateReturn" />
          <div id="service_flight_search_calendar_flights">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="flight-models" style="display:none;">
  <table>
    <tr id="flight-passenger-birthdate-model" class="ac_inc flight-passenger-birthdate">
      <td class="ac_dis text-center pt-2 pb-1">&nbsp;</td>
      <td class="text-center contains-form-control has-danger">
        <div class="input-group input-group-sm">
          <input type="number" min="0" step="1" max="150" class="passenger-age form-control text-center" />
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
        <div class="passenger-type passenger-type-indeterminate input-group input-group-sm" style="display:none;">
          <span class="input-group-addon">
            <i class="fa fa-question"></i>
          </span>
          <div class="form-control">
            <small><strong>-nedeterminat-</strong></small>
          </div>
        </div>
      </td>
      <td class="contains-form-control">
        <button type="button" class="btn btn-block btn-danger btn-delete-passenger-birthdate btn-sm"><i class="fa fa-times"></i></button>
      </td>
    </tr>
  </table>
  <form id="flight_result_model" class="flight-result card card-primary mb-1 rounded-0" onsubmit="return false;">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between text-white">
      <input type="hidden" name="itinerary_code" class="result-flight-itinerary-code" />
      <input type="hidden" name="flight_code" class="result-flight-code" />
	  <input type="hidden" class="flight-expected-price" name="expectedFlightPrice">
      <span class="flight-name">Pret #<strong class="flight-index"></strong></span>
      <strong class="flight-price"></strong>
    </div>
    <div class="card-block flight-result-items p-1">
    </div>
    <div class="card-footer p-1">
      <button type="submit" class="flight-options-toggle btn btn-success">
        <i class="fa fa-check-square-o"></i> Rezerva
      </button>
    </div>
  </form>
  <div id="flight_result_departure_return_model" class="flight-result-departure-return">
    <div class="card flight-result-departure rounded-0">
      <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
        <strong class="flight-type-text">Tur</strong>
      </div>
      <div class="card-block pt-1 pb-0 pl-1 pr-1 flight-result-departure-items">
      </div>
    </div>
    <div class="card flight-result-return mt-1 rounded-0">
      <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
        <strong class="flight-type-text">Retur</strong>
      </div>
      <div class="card-block pt-1 pb-0 pl-1 pr-1 flight-result-return-items">
      </div>
    </div>
  </div>
  <div id="flight_result_route_model" class="flight-result-route card mb-1 rounded-0">
    <label class="card-header pt-1 pb-1 pl-1 pr-1 m-0 d-flex align-items-center justify-content-between" style="cursor:pointer;">
      <div class="mr-2">
        <div><img src="<?php echo $this->theme_url; ?>/assets/images/plecare.png" /></div>
        <div class="leaving-company-details">
          <img class="leaving-company-image" style="max-height:50px;max-width:50px;" />
          <small class="leaving-company-name" ></small>
        </div>
      </div>
      <div class="text-center mr-auto">
        <div><strong><small><i class="fa fa-calendar"></i> <span class="leaving-date"></span> <i class="fa fa-clock-o"></i> <span class="leaving-hour"></span></small></strong></div>
        <div class="leaving-location">
          <small>
            <i class="fa fa-map-marker"></i>
            <span class="leaving-airport-city" ></span>
            <span class="leaving-airport-name" ></span>
          </small>
        </div>
      </div>
      <div class="text-center btn btn-primary btn-toggle-segments" data-toggle="collapse" aria-expanded="false" >
        <div>
          <span class="flight-with-stops"><strong class="flight-stops"></strong> <small class="singular hidden-lg-down">escala</small><small class="plural hidden-lg-down">escale</small></span>
          <span class="flight-without-stops"><span class="hidden-lg-down">Zbor direct</span><span class="hidden-lg-up">-</span></span>
          <i class="fa fa-info"></i>
        </div>
        <div>
          <i class="fa fa-clock-o"></i> <strong class="flight-duration"></strong>
        </div>
      </div>
      <div class="text-center ml-auto">
        <div><strong><small><i class="fa fa-calendar"></i> <span class="arriving-date"></span> <i class="fa fa-clock-o"></i> <span class="arriving-hour"></span></small></strong></div>
        <div class="arriving-location">
          <small>
            <i class="fa fa-map-marker"></i>
            <span class="arriving-airport-city" ></span>
            <span class="arriving-airport-name" ></span>
          </small>
        </div>
      </div>
      <div class="text-right ml-2">
        <div><img src="<?php echo $this->theme_url; ?>/assets/images/sosire.png" /></div>
        <div class="arriving-company-details">
          <img class="arriving-company-image pull-right ml-1" />
          <small class="arriving-company-name" ></small>
        </div>
      </div>
      <div class="ml-2 flight-route-option-choice-container">
        <span class="ml-0 custom-control custom-radio mr-0 mt-1">
          <input type="radio" class="custom-control-input flight-route-option-choice" required>
          <span class="custom-control-indicator"></span>
        </span>
      </div>
    </label>
    <div class="collapse flight-stops-container bg-info" role="tabpanel">
      <div class="card-block pt-1 pb-0 pl-1 pr-1 flight-stops-block">
      </div>
    </div>
  </div>
  <div id="flight_result_item_stop_model" class="card flight-result-item-stop rounded-0 mb-1">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
      <div class="leaving-stop-duration mr-2">
        <div><img src="<?php echo $this->theme_url; ?>/assets/images/waiting.png" /></div>
        <div><small><strong class="flight-stop-duration"></strong></small></div>
      </div>
      <div class="mr-2">
        <div><img src="<?php echo $this->theme_url; ?>/assets/images/plecare.png" /></div>
        <div class="company-details">
          <img class="company-image" />
          <small class="company-name" ></small>
        </div>
      </div>
      <div class="text-center mr-auto">
        <div><strong><small><i class="fa fa-calendar"></i> <span class="leaving-date"></span> <i class="fa fa-clock-o"></i> <span class="leaving-hour"></span></small></strong></div>
        <div class="leaving-location">
          <small>
            <i class="fa fa-map-marker"></i>
            <span class="leaving-airport-city" ></span>
            <span class="leaving-airport-name" ></span>
          </small>
        </div>
      </div>
      <div class="text-center">
        <div><i class="fa fa-plane"></i></div>
        <div><small class="aircraft-name"></small></div>
      </div>
      <div class="text-center ml-auto">
        <div><strong><small><i class="fa fa-calendar"></i> <span class="arriving-date"></span> <i class="fa fa-clock-o"></i> <span class="arriving-hour"></span></small></strong></div>
        <div class="arriving-location">
          <small>
            <i class="fa fa-map-marker"></i>
            <span class="arriving-airport-city" ></span>
            <span class="arriving-airport-name" ></span>
          </small>
        </div>
      </div>
      <div class="text-right ml-2">
        <div><img src="<?php echo $this->theme_url; ?>/assets/images/sosire.png" /></div>
      </div>
    </div>
  </div>
  <div id="flight_passenger_model" class="flight-passenger card rounded-0">
    <div class="card-header d-flex align-items-center justify-content-between bg-inverse text-white pt-1 pb-1 pl-2 pr-2">
      <span class="passenger-types">
        <span class="occupant-index-container" style="display:none;">#<strong class="occupant-index"></strong></span>
        <span class="passenger-type passenger-type-adult"><i class="fa fa-male"></i> <strong>Adult</strong></span>
        <span class="passenger-type passenger-type-senior"><i class="fa fa-user-secret"></i> <strong>Senior</strong></span>
        <span class="passenger-type passenger-type-child"><i class="fa fa-child"></i> <strong>Copil</strong></span>
        <span class="passenger-type passenger-type-infant"><i class="fa fa-bug"></i> <strong>Infant</strong></span>
        <span class="passenger-index-container">#<strong class="passenger-index"></strong></span>
      </span>
      <span class="passenger-type passenger-type-infant">
        <span class="passenger-type-infant-lap">In brate</span>
        <span class="passenger-type-infant-seat">In scaun</span>
      </span>
    </div>
    <div class="card-block pt-1 pl-0 pr-1 pb-0 flight-passenger-info">
      <div class="row ml-0 mr-0 flight-passenger-info-general">
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <label class="input-group mb-0">
            <input name="birthDate" type="text" maxlength="10" class="form-control passenger-info-field passenger-birth_date" placeholder="Data nasterii" required />
            <span class="input-group-addon adjust-addon-width">
              <i class="fa fa-calendar"></i>
            </span>
          </label>
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <select name="title" class="form-control passenger-info-field passenger-title" required>
            <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/passenger_title_options',array('selected'=>'mr')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger_title_options'); ?>
          </select>
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input name="lastName" type="text" maxlength="255" class="form-control passenger-info-field passenger-lastname" placeholder="Nume" required  />
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input name="firstName" type="text" maxlength="255" class="form-control passenger-info-field passenger-firstname" placeholder="Prenume" required  />
        </div>
      </div>
      <div class="row ml-0 mr-0 flight-passenger-info-adult">
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input name="email" type="email" maxlength="255" class="form-control passenger-info-field passenger-email" placeholder="Email"  />
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input name="phone" type="text" maxlength="100" class="form-control passenger-info-field passenger-phone" placeholder="Telefon"  />
        </div>
      </div>
      <div class="row ml-0 mr-0 flight-passenger-info-secured">
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input name="idDocNumber" type="text" maxlength="255" class="form-control passenger-info-field passenger-passport_number" placeholder="Nr. Pasaport" required  />
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input name="idDocExpiryDate" type="text" maxlength="255" class="form-control passenger-info-field passenger-passport_exp_date" placeholder="Exp. Pasaport" required  />
        </div>
        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/passenger-country'); ?>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <select name="idDocIssuingCountry" required class="form-control passenger-info-field passenger-passport_issuing_country passenger-country" >
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger-country'); ?>
          </select>
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <select name="idDocPaxNationality" required class="form-control passenger-info-field passenger-passport_nationality passenger-country" >
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger-country'); ?>
          </select>
        </div>
      </div>
    </div>
  </div>
  <div id="flight_fellow_adult_model" class="flight-fellow flight-fellow-adult card">
    <div class="card-header d-flex align-items-center justify-content-between bg-primary text-white pt-1 pb-1 pl-2 pr-2">
      <span class="fellow-type"><span class="fellow-type-adult"><i class="fa fa-male"></i> <strong>Adult</strong></span> #<strong class="fellow-index"></strong></span>
    </div>
    <div class="card-block room-fellow-info pt-1 pl-0 pr-1 pb-0">
      <div class="row ml-0 mr-0">
        
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input type="email" maxlength="255" class="form-control passenger-email" placeholder="Email" required  />
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input type="text" maxlength="100" class="form-control passenger-phone" placeholder="Telefon" required  />
        </div>
        
        <?php /*
        <div class="form-group col-sm-4 mb-1 pl-1 pr-0">
          <select required="" class="form-control passenger-country" >
            <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/passenger-country', array('selected'=>'RO')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger-country'); ?>
          </select>
        </div>
        */ ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>