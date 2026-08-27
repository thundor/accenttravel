<?php 
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::debugFileLine('start');

$this->_ci->load->model('Paralela45/Paralela45_Strainatate_model');
$this->_ci->load->model('Paralela45_model');
$this->paralela45_strainatate_search_data = $this->_ci->Paralela45_Strainatate_model->getSearchData(0);
$this->getPackageNVRoutesResponse = $this->_ci->Paralela45_model->getPackageNVRoutes();

themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/service_strainatate/search_scripts.php');

$start_date = '';
$this->hotel_search_data = array(
  'start_date' => '',
  'end_date' => '',
);
if($this->hotel_search_data['start_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->hotel_search_data['start_date']);
  $start_date = $date ? $date->format('d.m.Y') : '';
}
$end_date = '';
if($this->hotel_search_data['end_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->hotel_search_data['end_date']);
  $end_date = $date ? $date->format('d.m.Y') : '';
}
 ?>
<div class="row ml-0 mr-0">
  <div class="col-12 col-xl-5 mb-1 pl-1 pr-0">
    <div class="row">
      <div class="col-12 col-lg-6 col-xl-12">
        <div class="card rounded-0">
          <div class="card-header pl-2 pr-2 pb-2 pt-2" role="tab" id="serviceHotelFormHeader">
            <a data-toggle="collapse" href="#serviceHotelFormContainer" aria-expanded="true" aria-controls="serviceHotelFormContainer" class="nounderline d-flex align-items-center justify-content-between">
              <span><i class="fa fa-search"></i></span>
              <strong>Formular cautare</strong>
              <span><i class="fa fa-eye"></i></span>
            </a>
          </div>
          <div id="serviceHotelFormContainer" class="collapse show" role="tabpanel" aria-labelledby="serviceHotelFormHeader">
            <div class="card-block p-1">
              <form id="serviceHotelForm" name="serviceHotelForm" action="<?php echo site_url('backend/paralela45/strainatate/setSearch'); ?>" method="POST" onsubmit="return false;">
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="hotel_name" value="" />
                <div class="row no-gutters">
                  <div class="form-group col-12 col-sm-6 mb-1 has-danger">
                    <label class="input-group mb-0">
                      <select id="categoriePax1" class="form-control form-control-lg" required name="origin">
                        <option value="" selected>Plecare din</option>
                      </select>
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-map-o"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1 has-danger">
                    <label class="input-group mb-0">
                      <select id="destinatiePax1" class="form-control form-control-lg" required name="destination">
                        <option value="" selected>Destinatie</option>
                      </select>
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-map-o"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-8 col-md-6 col-lg-6 col-xl-8 mb-1 has-danger">
                    <label class="input-group mb-0">
                      <select id="datePax1" class="form-control form-control-lg" required name="start_date">
                        <option value="" selected>Alege data</option>
                      </select>
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-calendar-check-o"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-4 col-md-6 col-lg-6 col-xl-4 mb-1 pl-sm-1 has-danger">
                    <label class="input-group mb-0">
                      <select id="categPax1" class="form-control form-control-lg" required name="nights">
                        <option value="">Numar nopti</option>
                      </select>
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-moon-o"></i>
                      </span>
                    </label>
                  </div>
                </div>
                <div id="service_hotel_search_rooms_table_container" class="table-responsive">
                  <table id="service_hotel_search_rooms_table" class="table table-bordered table-striped ac mb-1">
                    <thead>
                      <td class="contains-form-control" colspan="4">
                        <div class="d-flex align-items-center justify-content-between">
                          <strong><i class="fa fa-tags"></i> Camere</strong>
                          <button id="service_hotel_search_add_room" type="button" class="btn btn-primary btn-add-room"><i class="fa fa-plus"></i> Adauga Camera</button>
                        </div>
                      </td>
                      <tr>
                        <th style="width:1%" data-toggle="tooltip" class="text-center" title="Camera nr." >#</th>
                        <th class="text-center" style="width:70px;"><i class="fa fa-male hidden-sm-up"></i><span class="hidden-xs-down"> Adulti</span></th>
                        <th style="min-width:190px;" data-toggle="tooltip" class="text-center" title="Copii - varsta copilului depinde de data de Check-Out"><i class="fa fa-child hidden-sm-up"></i><span class="hidden-xs-down"> Copii</span> (Date nastere / Ani impliniti) <i class="fa fa-question-circle-o"></i></th>
                        <th style="width:1%">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <label for="service_hotel_search_add_room" class="btn btn-primary mb-0" style="cursor:pointer;"><i class="fa fa-plus"></i> Adauga camera</label>
                  <button type="submit" id="service_hotel_search_submit" class="btn btn-success ml-auto"><i class="fa fa-search"></i> Cauta Hotel</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div id="result_serviceHotelForm" class="form-group mb-1"></div>
      </div>
      <div id="serviceHotelFormFellowsFormWrapper" class="col-12 col-lg-6 col-xl-12" style="display:none">
        <div class="card rounded-0">
          <div class="card-header pt-1 pb-1 pl-2 pr-2" role="tab" id="serviceHotelFormFellowsFormHeader">
            <a data-toggle="collapse" href="#serviceHotelFormFellowsFormContainer" aria-expanded="true" aria-controls="serviceHotelFormFellowsFormContainer" class="d-flex align-items-center justify-content-between nounderline pb-2 pt-1">
              <span><i class="fa fa-users"></i></span> 
              <strong>Informatii persoane</strong>
              <span><i class="fa fa-eye"></i></span>
            </a>
            <div class="d-flex align-items-center justify-content-between">
              <div class="form-group mb-0">
                <div id="service_hotel_search_hotel_rooms"><i class="fa fa-tags"></i> <strong id="service_hotel_search_hotel_rooms_number" ></strong> <span class="plural hidden-lg-down">Camere</span><span class="singular hidden-lg-down">Camera</span></div>
              </div>
              <div class="form-group mb-0">
                <div id="service_hotel_search_hotel_adults"><i class="fa fa-male"></i> <strong id="service_hotel_search_hotel_adults_number" ></strong> <span class="plural hidden-lg-down">Adulti</span><span class="singular hidden-lg-down">Adult</span></div>
              </div>
              <div class="form-group mb-0">
                <div id="service_hotel_search_hotel_children"><i class="fa fa-child"></i> <strong id="service_hotel_search_hotel_children_number" ></strong> <span class="plural hidden-lg-down">Copii</span><span class="singular hidden-lg-down">Copil</span></div>
              </div>
              <div class="form-group mb-0">
                <div id="service_hotel_search_hotel_nights"><i class="fa fa-cloud"></i> <strong id="service_hotel_search_hotel_nights_number" ></strong> <span class="plural hidden-lg-down">Nopti</span><span class="singular hidden-lg-down">Noapte</span></div>
              </div>
            </div>
          </div>
          <div id="serviceHotelFormFellowsFormContainer" class="collapse show" role="tabpanel" aria-labelledby="serviceHotelFormFellowsFormHeader">
            <div class="card-block p-1">
              <form id="serviceHotelFormFellowsForm" name="serviceHotelFormFellowsForm" action="<?php echo site_url('backend/paralela45/strainatate/addService'); ?>" method="POST" onsubmit="return false;">
                <div id="serviceHotelFormFellows">
                </div>
                <div id="service-hotel-hotel-details"></div>
                <div id="service-hotel-room-packages-loading" class="alert room-packages-loading alert-warning mb-0 fade show" role="alert" style="display:none;">
                  <div class="alert-message">Se incarca vacantele ... <i class="fa fa-spinner fa-spin"></i></div>
                </div>
                <div id="service-hotel-room-packages"></div>
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                <div class="form-group row mb-0">
                  <label for="service_hotel_comment" class="col-12 mb-0">Comentariu</label>
                  <div class="col-12">
                    <textarea class="form-control" name="comment" rows="3" id="service_hotel_comment"></textarea>
                  </div>
                </div>
                <button type="submit" id="service_hotel_reserve_submit" class="btn btn-block btn-success mt-2"><i class="fa fa-cube"></i> Adauga serviciul</button>
              </form>
              <div id="result_serviceHotelFormFellowsForm" class="form-group mb-1"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-7 mb-1 pl-1 pr-0">
    <div class="card rounded-0">
      <div class="card-header pt-1 pl-3 pr-3">
        <ul class="nav nav-tabs card-header-tabs nav-justified">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#service_hotel_search_results_tab" role="tab" aria-controls="service_hotel_search_results_tab"><i class="fa fa-search"></i> Rezultate cautare</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#service_hotel_search_filter_tab" role="tab" aria-controls="service_hotel_search_filter_tab"><i class="fa fa-filter"></i> Filtre cautare</a>
          </li>
        </ul>
      </div>
      <div class="tab-content card-block p-1">
        <div class="tab-pane active" id="service_hotel_search_results_tab" role="tabpanel">
          <div class="sortHotel d-lg-flex align-items-lg-center justify-content-lg-between">
            <?php 
            /* $this->_ci->load->model('Trip/Hotels_model');
            $data = $this->_ci->Hotels_model->getSearchData(0, false);
            $sort_by = $data['sort_by'];
            $sort_order = $data['sort_order'];
            ?>
            <form action="#" class="form-inline">
              <div class="input-group mb-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-sort-amount-asc"></i>
                </span>
                <select name="MinPrice" class="form-control hotel-sort-by" id="service_hotel_search_sort_price" disabled>
                  <option value="0" <?php echo $sort_by != 'MinPrice' ? 'selected' : ''; ?>>Tarif</option>
                  <option value="1" <?php echo $sort_by == 'MinPrice' && !$sort_order ? 'selected' : ''; ?>>Mic &gt; Mare</option>
                  <option value="2" <?php echo $sort_by == 'MinPrice' && $sort_order ? 'selected' : ''; ?>>Mare &gt; Mic</option>
                </select>       
              </div>
              <div class="input-group mb-1 ml-sm-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-star"></i>
                </span>
                <select name="Stars" class="form-control hotel-sort-by" id="service_hotel_search_sort_stars" disabled>
                  <option value="0" <?php echo $sort_by != 'Stars' ? 'selected' : ''; ?>>Nr. Stele</option>
                  <option value="1" <?php echo $sort_by == 'Stars' && !$sort_order ? 'selected' : ''; ?>>1 &gt; 5</option>
                  <option value="2" <?php echo $sort_by == 'Stars' && $sort_order ? 'selected' : ''; ?>>5 &gt; 1</option>
                </select> 
              </div>
            </form>
             */ ?>
            <div class="text-center mb-1">
              <nav aria-label="Navigare pagini">
                <ul id="serviceHotelResultsNavigation" class="pagination justify-content-center">
                </ul>
              </nav>
            </div>
          </div>
          <div id="serviceHotelResults">
          </div>
        </div>
        <div class="tab-pane" id="service_hotel_search_filter_tab" role="tabpanel">
          <div id="serviceHotelFormFilters">
            <div class="row no-gutters">
              <div class="col-xl-4 mb-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-money"></i></span>
                    <strong>Tarif hotel</strong>
                  </div>
                  <div class="card-block">
                    <input type="text" id="hotel-services-search-filter-price-slider-amount" class="border-0 mb-1" readonly>
                    <div id="hotel-services-search-filter-price-slider-range"></div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 mb-1 pl-xl-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-star-o"></i></span>
                    <strong>Numar de stele</strong>
                  </div>
                  <div class="card-block">
                    <div id="hotel-services-search-filter-stars" class="hotel-filter hotel-stars-filter">
                    </div>
                  </div>
                </div>
              </div>
              <?php /* 
              <div class="col-xl-4 mb-1 pl-xl-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-sliders"></i></span>
                    <strong>Servicii incluse</strong>
                  </div>
                  <div class="card-block">
                    <div id="hotel-services-search-filter-services" class="hotel-filter hotel-services-filter">
                    </div>
                  </div>
                </div>
              </div>
              */ ?>
              <div class="col-xl-4 mb-1 pl-xl-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-tasks"></i></span>
                    <strong>Disponibilitate</strong>
                  </div>
                  <div class="card-block">
                    <div id="hotel-services-search-filter-availabilities" class="hotel-filter hotel-availabilities-filter">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="hotel-filters-actions">
              <button name="hotel_reset_filters" id="hotel_reset_filters" class="btn btn-block btn-warning" type="submit">Sterge Filtre</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="hotel-models" style="display:none;">
  <div id="hotel-room-child-model" class="hotel-room-child input-group mt-1 input-group-sm">
    <label class="input-group mb-0 input-group-sm">
      <input type="text" class="child-birth_date form-control" placeholder="Data nastere" />
      <span class="input-group-addon adjust-addon-width">
        <i class="fa fa-calendar"></i>
      </span>
    </label>
    <div class="input-group has-danger" style="width:60px;">
      <input type="number" class="child-age form-control pr-0 pt-0 pb-0" value="" step="1" min="0" max="17" required style="line-height:25px;"/>
    </div>
    <div class="input-group-btn">
      <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-danger btn-remove-child"><i class="fa fa-minus"></i></button>
      </div>
    </div>
  </div>
  <table>
    <tr id="hotel-room-model" class="ac_inc">
      <td class="ac_dis text-center pt-2 pb-1">&nbsp;</td>
      <td class="contains-form-control has-danger">
        <div class="input-group input-group-sm">
          <input type="number" class="form-control pr-0 pt-0 pb-0" value="1" step="1" min="1" max="1000" required style="line-height:25px; min-width:30px;"/>
        </div>
      </td>
      <td class="contains-form-control text-center">
        <button type="button" class="btn btn-primary btn-block btn-sm btn-add-child pl-2 pr-2"><small><i class="fa fa-plus"></i> Adauga Copil</small></button>
      </td>
      <td class="contains-form-control">
        <button type="button" class="btn btn-block btn-danger btn-delete-room btn-sm"><i class="fa fa-times"></i></button>
      </td>
    </tr>
  </table>
  <div id="hotel-room-fellows-model" class="hotel-room-fellows card">
    <div class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 bg-inverse text-white">
      <span class="room-name"><i class="fa fa-tag"></i> Camera #<strong class="room-number"></strong></span>
      <span class="room-occupancy">
        <span class="room-occupancy-adults">
          <i class="fa fa-male"></i> <strong class="room-occupancy-adults-number"></strong> <small class="plural">Adulti</small><small class="singular">Adult</small>
        </span>
        <span class="room-occupancy-children">
          <i class="fa fa-child"></i> <strong class="room-occupancy-children-number"></strong> <small class="plural">Copii</small><small class="singular">Copil</small>
        </span>
      </span>
    </div>
    <div class="card-block room-occupancy-fellows p-1">
    </div>
  </div>
  <div id="hotel-room-fellow-child-model" class="hotel-room-fellow hotel-room-fellow-child card">
    <div class="card-header d-flex align-items-center justify-content-between bg-primary text-white pt-1 pb-1 pl-2 pr-2">
      <span class="fellow-type"><span class="fellow-type-child"><i class="fa fa-child"></i> <strong>Copil</strong></span> #<strong class="fellow-index"></strong></span>
      <span class="fellow-age"><strong class="fellow-child-age-number"></strong> ani</span>
    </div>
    <div class="card-block room-fellow-info pt-1 pl-0 pr-1 pb-0">
      <input type="hidden" class="form-control passenger-age" />
      <div class="row ml-0 mr-0">
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <label class="input-group mb-0">
            <input type="text" maxlength="10" class="form-control passenger-birth_date" placeholder="Data nasterii" required />
            <span class="input-group-addon adjust-addon-width">
              <i class="fa fa-calendar"></i>
            </span>
          </label>
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <select class="form-control passenger-title" required>
            <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/passenger_title_options2',array('selected'=>'mr')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger_title_options2'); ?>
          </select>
        </div>
        <?php /*
        <div class="form-group col-sm-4 mb-1 pl-1 pr-0">
          <select required="" class="form-control passenger-country" >
            <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/passenger-country', array('selected'=>'RO')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger-country'); ?>
          </select>
        </div>
        */ ?>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input type="text" maxlength="255" class="form-control passenger-lastname" placeholder="Nume" required  />
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input type="text" maxlength="255" class="form-control passenger-firstname" placeholder="Prenume" required  />
        </div>
      </div>
    </div>
  </div>
  <div id="hotel-room-fellow-adult-model" class="hotel-room-fellow hotel-room-fellow-adult card">
    <div class="card-header d-flex align-items-center justify-content-between bg-primary text-white pt-1 pb-1 pl-2 pr-2">
      <span class="fellow-type"><span class="fellow-type-adult"><i class="fa fa-male"></i> <strong>Adult</strong></span> #<strong class="fellow-index"></strong></span>
    </div>
    <div class="card-block room-fellow-info pt-1 pl-0 pr-1 pb-0">
      <div class="row ml-0 mr-0">
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <label class="input-group mb-0">
            <input type="text" maxlength="10" class="form-control passenger-birth_date" placeholder="Data nasterii" required />
            <span class="input-group-addon adjust-addon-width">
              <i class="fa fa-calendar"></i>
            </span>
          </label>
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <select class="form-control passenger-title" required>
            <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/passenger_title_options',array('selected'=>'mr')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger_title_options'); ?>
          </select>
        </div>
        <?php /*
        <div class="form-group col-sm-4 mb-1 pl-1 pr-0">
          <select required="" class="form-control passenger-country" >
            <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/passenger-country', array('selected'=>'RO')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/passenger-country'); ?>
          </select>
        </div>
        */ ?>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input type="text" maxlength="255" class="form-control passenger-lastname" placeholder="Nume" required  />
        </div>
        <div class="form-group col-sm-6 mb-1 pl-1 pr-0">
          <input type="text" maxlength="255" class="form-control passenger-firstname" placeholder="Prenume" required  />
        </div>
      </div>
    </div>
  </div>
  <div id="hotel-result-model" class="hotel-result card mb-2">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
      <input type="hidden" name="offer_id" class="service-offer-id" />
      <input type="hidden" name="package_id" class="service-package-id" />
      <input type="hidden" name="package_variant_id" class="service-package-variant-id" />
      <input type="hidden" name="origin" class="service-origin" />
      <input type="hidden" name="destination" class="service-destination" />
      <input type="hidden" name="checkin" class="service-checkin" />
      <input type="hidden" name="checkout" class="service-checkout" />
      <span>
        <a target="_BLANK" class="hotel-link nounderline" title="Pagina hotel"><i class="fa fa-external-link"></i></a>
        <span class="hotel-name"></span>
        <span class="package-availability"></span>
        <span class="hotel-stars"></span>
      </span>
      <strong class="current-price"></strong>
    </div>
    <div class="card-block p-1">
      <div class="media">
        <div class="d-flex align-self-start mr-3 hotel-image" style="background-image:url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>);" ></div>
        <div class="media-body">
          <h4 class="mt-0 mb-0 d-flex align-items-center justify-content-between">
            <span>
              <i class="fa fa-map-marker"></i>
              <span class="hotel-location"></span>
            </span>
            <span class="text-center action-buttons">
              <button type="button" class="ml-2 room-options-toggle btn btn-success">
                <i class="fa fa-cubes"></i><span class="hidden-sm-down"> Vacante disponibile</span>
              </button>
            </span>
          </h4>
        </div>
      </div>
      <div class="mt-1 mb-1">
        <strong>Servicii:</strong> <ul class="package-services pl-3"></ul>
      </div>
      <div class="mt-1 mb-1">
        <strong>Descriere:</strong> <div class="hotel-info-description"></div>
      </div>
      <div class="mt-1 mb-1">
        <strong>Camere:</strong> <ul class="package-rooms pl-3"></ul>
      </div>
    </div>
  </div>
  <div id="hotel-filter-checkbox-model" class="hotel-filter-checkbox-model custom-controls-stacked d-block">
    <label class="custom-control custom-checkbox d-block">
      <input type="checkbox" class="filter-option-input custom-control-input">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description filter-option-description d-block"></span>
    </label>
  </div>
  <div id="order-service-hotel-model" class="order-service-hotel card mt-1">
    <div class="card-header pt-1 pl-3 pr-3">
      <ul class="nav nav-tabs card-header-tabs nav-justified">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" role="tab" data-tab="order_service_hotel_info_tab"><i class="fa fa-if"></i><span class="hidden-sm-down"> Informatii hotel</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link " data-toggle="tab" role="tab" data-tab="order_service_hotel_rooms_tab"><i class="fa fa-tags"></i><span class="hidden-sm-down"> Camere</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" role="tab" data-tab="order_service_hotel_services_tab"><i class="fa fa-list"></i><span class="hidden-sm-down"> Servicii Incluse</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" role="tab" data-tab="order_service_hotel_extra_services_tab"><i class="fa fa-list"></i><span class="hidden-sm-down"> Servicii Extra</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" role="tab" data-tab="order_service_hotel_other_tab"><i class="fa fa-question-circle-o"></i><span class="hidden-sm-down"> Alte informatii</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" role="tab" data-tab="order_service_hotel_cancel_tab"><i class="fa fa-ban"></i><span class="hidden-sm-down"> Conditii anulare</span></a>
        </li>
      </ul>
    </div>
    <div class="card-block p-1">
      <div class="tab-content">
        <div class="order_service_hotel_info_tab tab-pane p-0 active" role="tabpanel">
          <div class="media">
            <div class="d-flex align-self-start mr-3 hotel-image lazy" style="background-image:url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>);width:200px; height:200px" ></div>
            <div class="media-body mt-2">
              <div class="row">
                <div class="col-12 col-lg-6">
                  <h4>
                    <a target="_BLANK" class="hotel-link nounderline" title="Pagina hotel"><i class="fa fa-external-link"></i></a>
                    <span class="hotel-name"></span>
                    <span class="hotel-stars"></span>
                  </h4>
                  <div class="">
                    <i class="fa fa-map-marker"></i>
                    <span class="hotel-location"></span>
                  </div>
                  <?php /*
                  <div class="">
                    <a class="hotel-info-phone"><i class="fa fa-phone"></i> <span></span></a>
                    <span> | </span>
                    <a class="hotel-info-fax"><i class="fa fa-fax"></i> <span></span></a>
                    <span> | </span>
                    <a class="hotel-info-email"><i class="fa fa-envelope"></i> <span></span></a>
                  </div>
                  */ ?>
                </div>
                <div class="col-12 col-lg-6">
                  <div class="mt-1 mb-1">
                    <strong>Facilitati:</strong> <span class="hotel-info-facilities"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-control mt-1">
            <div class="hotel-info-description"></div>
          </div>
        </div>
        <div class="order_service_hotel_rooms_tab tab-pane p-0" role="tabpanel">
        </div>
        <div class="order_service_hotel_services_tab tab-pane p-0" role="tabpanel">
          <ul class="services-list list-group">
          </ul>
        </div>
        <div class="order_service_hotel_extra_services_tab tab-pane p-0" role="tabpanel">
          <ul class="extra_services-list list-group">
          </ul>
        </div>
        <div class="order_service_hotel_other_tab tab-pane p-0" role="tabpanel">
          <?php /*
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">ID serviciu</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-service_id"></div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Status</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-status_message"></div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Mesaje eroare</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-error_message"></div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Sistem</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-system"></div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Nr. Confirmare</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-confirmation_no"></div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">ID rezervare</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-reservation_id"></div>
            </div>
          </div>
          */ ?>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Adulti</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-adults-number"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-male"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Copii</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-children-number"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-child"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Checkin</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-hotel-checkin"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-calendar-check-o"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Checkout</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-hotel-checkout"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-calendar-times-o"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Pret</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-hotel-price"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-0">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Comentarii</label>
            <div class="<?php echo $value_class; ?>">
              <div class="form-control service-hotel-comments">
              </div>
            </div>
          </div>
        </div>
        <div class="order_service_hotel_cancel_tab tab-pane p-0" role="tabpanel">
          <ul class="cancellation-policies-list list-group">
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div id="order-service-hotel-room-guest-adult-model" class="order-service-hotel-room-guest-adult card">
    <a class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 bg-inverse nounderline text-white" data-toggle="collapse" aria-expanded="false" role="tab">
      <span class="guest-name"><small class="hidden-sm-down">Oaspete </small>#<strong class="guest-number"></strong></span>
      <span class="guest-type">
        #<strong class="guest-type-number"></strong>
        <i class="fa fa-male"></i> <small class="hidden-sm-down">Adult</small>
      </span>
    </a>
    <div class="collapse" role="tabpanel">
      <div class="card-block p-1">
        <div class="row ">
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Nume</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-lastname"></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Prenume</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-firstname"></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Titlu</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-title"></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Data nastere</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-birth_date"></strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="order-service-hotel-room-guest-child-model" class="order-service-hotel-room-guest-child card">
    <a class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 bg-inverse nounderline text-white" data-toggle="collapse" aria-expanded="false" role="tab">
      <span class="guest-name"><small class="hidden-sm-down">Oaspete </small>#<strong class="guest-number"></strong></span>
      <span><strong class="guest-age"></strong> ani</span>
      <span class="guest-type">
        #<strong class="guest-type-number"></strong>
        <i class="fa fa-child"></i> <small class="hidden-sm-down">Copil</small>
      </span>
    </a>
    <div class="collapse" role="tabpanel">
      <div class="card-block p-1">
        <div class="row ">
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Nume</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-lastname"></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Prenume</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-firstname"></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Titlu</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-title"></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-12 mb-1">
            <div class="form-group row mb-0 ">
              <label class="<?php echo $label_class; ?> pt-1 text-center">Data nastere</label>
              <div class="<?php echo $value_class; ?>">
                <div class="form-control">
                  <strong class="guest-birth_date"></strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="order-service-hotel-room-model" class="order-service-hotel-room-model card">
    <div class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 bg-inverse text-white">
      <span class="room-title"><i class="fa fa-tag"></i> Camera #<strong class="room-number"></strong></span>
      <span class="room-occupancy">
        <span class="room-occupancy-adults">
          <i class="fa fa-male"></i> <strong class="room-occupancy-adults-number"></strong> <small class="plural">Adulti</small><small class="singular">Adult</small>
        </span>
        <span class="room-occupancy-children">
          <i class="fa fa-child"></i> <strong class="room-occupancy-children-number"></strong> <small class="plural">Copii</small><small class="singular">Copil</small>
        </span>
      </span>
      <span class="room-price-points">
        <span><strong class="room-points"></strong><span class="hidden-sm-down"> puncte</span></span>
        <strong class="room-price"></strong>
      </span>
    </div>
    <div class="card-block p-1">
      <div class="row ml-0 mr-0">
        <div class="col-12 col-sm-5 mb-1 pl-0 pl-md-1 pr-0">
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Nume</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="room-name"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-tag"></i>
                </span>
              </div>
            </div>
          </div>
          <?php /*
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Board</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="room-board"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-cutlery"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Info</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="room-info"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-info-circle"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-1">
            <label class="<?php echo $label_class; ?> pt-1 text-center">Status</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="room-status"></strong>
                </div>
                <span class="input-group-addon">
                  <i class="fa fa-check-square-o"></i>
                </span>
              </div>
            </div>
          </div>
          */ ?>
        </div>
        <div class="col-12 col-sm-7 mb-1 pl-0 pl-md-1 pr-0">
          <div class="room-occupancy-guests">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>