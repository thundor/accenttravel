<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php //themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/service_package/search_scripts.php'); ?>
<?php 
$start_date = '';
if($this->package_search_data['start_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->package_search_data['start_date']);
  $start_date = $date ? $date->format('d.m.Y') : '';
}
$end_date = '';
if($this->package_search_data['end_date']){
  $date = DateTime::createFromFormat('Y-m-d', $this->package_search_data['end_date']);
  $end_date = $date ? $date->format('d.m.Y') : '';
}
// echo '<pre>';
// print_r($this->package_search_data);
// echo '</pre>';
 ?>
<div class="row ml-0 mr-0">
  <div class="col-12 col-xl-5 mb-1 pl-1 pr-0">
    <div class="row">
      <div class="col-12 col-lg-6 col-xl-12">
        <div class="card rounded-0">
          <div class="card-header pl-2 pr-2 pb-2 pt-2" role="tab" id="service_package_form_header">
            <a data-toggle="collapse" href="#service_package_form_container" aria-expanded="true" aria-controls="service_package_form_container" class="nounderline d-flex align-items-center justify-content-between">
              <span><i class="fa fa-search"></i></span>
              <strong>Formular cautare</strong>
              <span><i class="fa fa-eye"></i></span>
            </a>
          </div>
          <div id="service_package_form_container" class="collapse show" role="tabpanel" aria-labelledby="service_package_form_header">
            <div class="card-block p-1">
            <?php /*
              <form id="service_package_form" name="service_package_form" action="<?php echo site_url('backend/trip/Packages/setSearch'); ?>" method="p_o_s_t" onsubmit="return false;">
                <?php if($this->_ci->config->item('csrf_protection') === true){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" id="service_package_search_city_id" name="city_id" value="<?php echo htmlspecialchars($this->package_search_data['city_id']); ?>"/>
                <input type="hidden" id="service_package_search_city_name" value="<?php echo htmlspecialchars($this->package_search_data['city_name']); ?>" />
                <input type="hidden" id="service_package_search_country_id" name="country_id" value="<?php echo htmlspecialchars($this->package_search_data['country_id']); ?>"/>
                <input type="hidden" id="service_package_search_country_name" name="country_name" value="<?php echo htmlspecialchars($this->package_search_data['country_name']); ?>"/>
                <div class="row no-gutters">
                  <div class="form-group col-12 col-sm-6 mb-1">
                    <label class="input-group mb-0">
                      <input type="text" maxlength="255" class="form-control" name="city_name" id="service_package_search_city" placeholder="Oras" value="<?php echo htmlspecialchars($this->package_search_data['city_name']); ?>"/>
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-map-o"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1">

                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 has-danger">
                    <label class="input-group mb-0">
                      <input type="text" maxlength="255" class="form-control" name="start_date" id="service_package_search_checkin" placeholder="Check-in" required value="<?php echo htmlspecialchars($start_date); ?>" />
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-calendar-check-o"></i>
                      </span>
                    </label>
                  </div>
                  <div class="form-group col-12 col-sm-6 mb-1 pl-sm-1 has-danger">
                    <label class="input-group mb-0">
                      <input type="text" maxlength="255" class="form-control" name="end_date" id="service_package_search_checkout" placeholder="Check-out" required value="<?php echo htmlspecialchars($end_date); ?>" />
                      <span class="input-group-addon adjust-addon-width">
                        <i class="fa fa-calendar-times-o"></i>
                      </span>
                    </label>
                  </div>
                </div>
                <div id="service_package_search_rooms_citybreak">
                </div>
                <div id="service_package_search_rooms_table_container" class="table-responsive">
                  <table id="service_package_search_rooms_table" class="table table-bordered table-striped ac mb-1">
                    <thead>
                      <td class="contains-form-control" colspan="4">
                        <div class="d-flex align-items-center justify-content-between">
                          <strong><i class="fa fa-tags"></i> Camere</strong>
                          <button id="service_package_search_add_room" type="button" class="btn btn-primary btn-add-room"><i class="fa fa-plus"></i> Adauga Camera</button>
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
                  <label for="service_package_search_add_room" class="btn btn-primary mb-0" style="cursor:pointer;"><i class="fa fa-plus"></i> Adauga camera</label>
                  <button type="submit" id="service_package_search_submit" class="btn btn-success ml-auto"><i class="fa fa-search"></i> Cauta Vacanta</button>
                </div>
              </form>
                  */ ?>
              <div id="service_package_search_rooms_table_outside_container" style="display:none;">
                <div id="service_package_search_rooms_citybreak_hidden_inputs"></div>
              </div>
            </div>
          </div>
        </div>
        <div id="result_service_package_form" class="form-group mb-1"></div>
        <?php /*
        <div class="form-group mb-1 has-warning" data-toggle="tooltip" title="Odata activat, packageul ales va fi depozitat in tab-ul CityBreak" style="display:none;">
          <label class="input-group mb-0">
            <span class="input-group-addon mb-0">
              <input type="checkbox" id="service_package_citybreak" value="1" checked>
            </span>
            <div class="form-control">Dezactivare CityBreak</div>
          </label>
        </div>
        */ ?>
      </div>
      
      <div id="service_package_form_fellows_form_wrapper" class="col-12 col-lg-6 col-xl-12" style="display:none">
        <div class="card rounded-0">
          <div class="card-header pt-1 pb-1 pl-2 pr-2" role="tab" id="service_package_form_fellows_form_header">
            <a data-toggle="collapse" href="#service_package_form_fellows_form_container" aria-expanded="true" aria-controls="service_package_form_fellows_form_container" class="d-flex align-items-center justify-content-between nounderline pb-2 pt-1">
              <span><i class="fa fa-users"></i></span> 
              <strong>Informatii persoane</strong>
              <span><i class="fa fa-eye"></i></span>
            </a>
            <div class="d-flex align-items-center justify-content-between">
              <div class="form-group mb-0">
                <div id="service_package_search_package_rooms"><i class="fa fa-tags"></i> <strong id="service_package_search_package_rooms_number" ></strong> <span class="plural hidden-lg-down">Camere</span><span class="singular hidden-lg-down">Camera</span></div>
              </div>
              <div class="form-group mb-0">
                <div id="service_package_search_package_adults"><i class="fa fa-male"></i> <strong id="service_package_search_package_adults_number" ></strong> <span class="plural hidden-lg-down">Adulti</span><span class="singular hidden-lg-down">Adult</span></div>
              </div>
              <div class="form-group mb-0">
                <div id="service_package_search_package_children"><i class="fa fa-child"></i> <strong id="service_package_search_package_children_number" ></strong> <span class="plural hidden-lg-down">Copii</span><span class="singular hidden-lg-down">Copil</span></div>
              </div>
              <div class="form-group mb-0">
                <div id="service_package_search_package_nights"><i class="fa fa-cloud"></i> <strong id="service_package_search_package_nights_number" ></strong> <span class="plural hidden-lg-down">Nopti</span><span class="singular hidden-lg-down">Noapte</span></div>
              </div>
            </div>
          </div>
          <div id="service_package_form_fellows_form_container" class="collapse show" role="tabpanel" aria-labelledby="service_package_form_fellows_form_header">
            <div class="card-block p-1">
              <form id="service_package_form_fellows_form" name="service_package_form_fellows_form" action="<?php echo site_url('backend/trip/Orders/addPackageService'); ?>" method="p_o_s_t" onsubmit="return false;">
                <div id="service_package_form_fellows">
                </div>
                <div id="service_package_package_details"></div>
                <div id="service_package_room_packages_loading" class="alert room-packages-loading alert-warning mb-0 fade show" role="alert" style="display:none;">
                  <div class="alert-message">Se incarca vacantele ... <i class="fa fa-spinner fa-spin"></i></div>
                </div>
                <div id="service_package_room_packages"></div>
                <?php if($this->_ci->config->item('csrf_protection') === true){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                <div class="form-group row mb-0">
                  <label for="service_package_comment" class="col-12 mb-0">Comentariu</label>
                  <div class="col-12">
                    <textarea class="form-control" name="comment" rows="3" id="service_package_comment"></textarea>
                  </div>
                </div>
                <button type="submit" id="service_package_reserve_submit" class="btn btn-block btn-success mt-2"><i class="fa fa-cube"></i> Adauga serviciul</button>
              </form>
              <div id="result_service_package_form_fellows_form" class="form-group mb-1"></div>
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
            <a class="nav-link active" data-toggle="tab" href="#service_package_search_results_tab" role="tab" aria-controls="service_package_search_results_tab"><i class="fa fa-search"></i> Rezultate cautare</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#service_package_search_filter_tab" role="tab" aria-controls="service_package_search_filter_tab"><i class="fa fa-filter"></i> Filtre cautare</a>
          </li>
        </ul>
      </div>
      <div class="tab-content card-block p-1">
        <div class="tab-pane active" id="service_package_search_results_tab" role="tabpanel">
          <div class="sort_package d-lg-flex align-items-lg-center justify-content-lg-between">
            <?php 
            // $this->_ci->load->model('Trip/Packages_model');
            // $data = $this->_ci->Hotels_model->get_search_data(0, false);
            // $sort_by = $data['sort_by'];
            // $sort_order = $data['sort_order'];
            ?>
            <form action="#" class="form-inline">
              <div class="input-group mb-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-sort-amount-asc"></i>
                </span>
                <select name="min_price" class="form-control package-sort-by" id="service_package_search_sort_price" disabled>
                <?php /*
                  <option value="0" <?php echo $sort_by != 'min_price' ? 'selected' : ''; ?>>Tarif</option>
                  <option value="1" <?php echo $sort_by == 'min_price' && !$sort_order ? 'selected' : ''; ?>>Mic &gt; Mare</option>
                  <option value="2" <?php echo $sort_by == 'min_price' && $sort_order ? 'selected' : ''; ?>>Mare &gt; Mic</option>
                  */ ?>
                </select>       
              </div>
              <div class="input-group mb-1 ml-sm-1">
                <span class="input-group-addon adjust-addon-width">
                  <i class="fa fa-star"></i>
                </span>
                <select name="Stars" class="form-control package-sort-by" id="service_package_search_sort_stars" disabled>
                <?php /*
                  <option value="0" <?php echo $sort_by != 'Stars' ? 'selected' : ''; ?>>Nr. Stele</option>
                  <option value="1" <?php echo $sort_by == 'Stars' && !$sort_order ? 'selected' : ''; ?>>1 &gt; 5</option>
                  <option value="2" <?php echo $sort_by == 'Stars' && $sort_order ? 'selected' : ''; ?>>5 &gt; 1</option>
                  */ ?>
                </select> 
              </div>
            </form>
            <div class="text-center mb-1">
              <nav aria-label="Navigare pagini">
                <ul id="service_package_results_navigation" class="pagination justify-content-center">
                </ul>
              </nav>
            </div>
          </div>
          <div id="service_package_results">
          </div>
        </div>
        <div class="tab-pane" id="service_package_search_filter_tab" role="tabpanel">
          <div id="service_package_form_filters">
            <?php /*
            <div class="row no-gutters">
              <div class="col-xl-4 mb-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-money"></i></span>
                    <strong>Tarif package</strong>
                  </div>
                  <div class="card-block">
                    <input type="text" id="package-services-search-filter-price-slider-amount" class="border-0 mb-1" readonly>
                    <div id="package-services-search-filter-price-slider-range"></div>
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
                    <div id="package-services-search-filter-stars" class="package-filter package-stars-filter">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 mb-1 pl-xl-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-sliders"></i></span>
                    <strong>Activitati</strong>
                  </div>
                  <div class="card-block">
                    <div id="package-services-search-filter-activitycategories" class="package-filter package-activitycategories-filter">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-6 mb-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-map-marker"></i></span>
                    <strong>Puncte de interes</strong>
                  </div>
                  <div class="card-block">
                    <div id="package-services-search-filter-pois" class="package-filter package-pois-filter">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-6 mb-1 pl-xl-1">
                <div class="card rounded-0">
                  <div class="card-header p-1">
                    <span class="ml-2 mr-2"><i class="fa fa-tasks"></i></span>
                    <strong>Facilitati</strong>
                  </div>
                  <div class="card-block">
                    <div id="package-services-search-filter-facilities" class="package-filter package-facilities-filter">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="package-filters-actions">
              <button name="package_reset_filters" id="package_reset_filters" class="btn btn-block btn-warning" type="submit">Sterge Filtre</button>
            </div>
            */ ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="package-models" style="display:none;">
  <div id="package-room-child-model" class="package-room-child input-group mt-1 input-group-sm">
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
  <div id="package-result-model" class="package-result card mb-2">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
      <input type="hidden" name="package_id" class="package-id" />
      <input type="hidden" name="code" class="result-code" />
      <span>
        <a target="_BLANK" class="package-link nounderline" title="Pagina package"><i class="fa fa-external-link"></i></a>
        <span class="package-name"></span>
        <span class="package-stars"></span>
      </span>
      <strong class="current-price"></strong>
    </div>
    <div class="card-block p-1">
      <div class="media">
        <img class="d-flex align-self-start mr-3 package-image" src="" alt="Generic placeholder image" width="64" height="64" />
        <div class="media-body">
          <h4 class="mt-0 mb-0 d-flex align-items-center justify-content-between">
            <span>
              <i class="fa fa-map-marker"></i>
              <span class="package-location"></span>
            </span>
            <span class="text-center">
              <button type="button" class="ml-2 room-options-toggle btn btn-success">
                <i class="fa fa-cubes"></i><span class="hidden-sm-down"> Vacante disponibile</span>
              </button>
              <?php /*
              <br />
              <span>
                <i class="fa fa-clock-o"></i>
                <small class="package-expiration"></small>
              </span>
              */ ?>
            </span>
          </h4>
          <div class="">
            <a class="package-info-phone"><i class="fa fa-phone"></i> <span></span></a>
            <span> | </span>
            <a class="package-info-fax"><i class="fa fa-fax"></i> <span></span></a>
            <span> | </span>
            <a class="package-info-email"><i class="fa fa-envelope"></i> <span></span></a>
          </div>
        </div>
      </div>
      <div class="mt-1 mb-1">
        <strong>Facilitati:</strong> <span class="package-info-facilities"></span>
      </div>
      <div class="package-result-accordion" role="tablist" aria-multiselectable="true">
        <div class="card rounded-0">
          <div class="card-header pt-1 pb-1 pl-2 pr-2 text-center" role="tab">
            <h5 class="mb-0">
              <a class="collapsed nounderline" data-toggle="collapse" href="#" aria-expanded="false">
                <i class="fa fa-comment-o"></i> Descriere
              </a>
            </h5>
          </div>
          <div class="collapse" role="tabpanel">
            <div class="card-block p-1">
              <div class="package-info-description"></div>
            </div>
          </div>
        </div>
        <?php /*
        <div class="card mt-1">
          <div class="card-header pt-1 pb-1 pl-2 pr-2 text-center" role="tab">
            <h5 class="mb-0">
              <a class="collapsed nounderline" data-toggle="collapse" href="#" aria-expanded="false">
                <i class="fa fa-map-o"></i> Harta
              </a>
            </h5>
          </div>
          <div class="collapse" role="tabpanel">
            <div class="card-block p-1">
              
            </div>
          </div>
        </div>
        */ ?>
      </div>
    </div>
  </div>
  <div id="package-room-packages-model" class="package-room-packages card">
    <a class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 bg-inverse text-white collapsed nounderline" data-toggle="collapse" href="#" aria-expanded="false" role="tab">
      <span class="package-name"><i class="fa fa-cube"></i> Vacanta #<strong class="package-number"></strong></span>
      <span class="package-rooms-selection"><i class="fa fa-list-ol"></i><small class="hidden-lg-down"> Camere</small></span>
      <span class="package-price-points">
        <span><strong class="package-points text-success"></strong><span class="hidden-sm-down"> puncte</span></span>
        <strong class="package-price"></strong>
      </span>
    </a>
    <div class="collapse" role="tabpanel">
      <div class="card-block p-1">
        <div class="package-rooms" role="tablist" aria-multiselectable="true">
        </div>
      </div>
      <div class="card-footer p-0">
        <div class="form-group package-toggle m-1">
          <div class="custom-controls-stacked d-block">
            <label class="custom-control custom-radio d-block mb-0 mr-0">
              <input type="radio" name="package_code" class="custom-control-input package-code-radio" required />
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description"><i class="fa fa-cube"></i> Utilizeaza aceasta vacanta</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="package-room-package-room-model" class="package-room-package-room card">
    <a class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 bg-primary text-white text-white collapsed nounderline" data-toggle="collapse" href="#" aria-expanded="false" role="tab">
      <span class="package-room-name"><i class="fa fa-tag"></i> Camera #<strong class="package-room-number"></strong></span>
      <span class="package-room-another"><i class="fa fa-list-ol"></i><small class="hidden-lg-down"> Optiuni</small></span>
      <span class="package-room-occupancy">
        <span class="package-room-occupancy-adults">
          <i class="fa fa-male"></i> <strong class="package-room-occupancy-adults-number"></strong> <small class="plural">Adulti</small><small class="singular">Adult</small>
        </span>
        <span class="package-room-occupancy-children">
          <i class="fa fa-child"></i> <strong class="package-room-occupancy-children-number"></strong> <small class="plural">Copii</small><small class="singular">Copil</small>
        </span>
      </span>
    </a>
    <div class="collapse" role="tabpanel">
      <div class="card-block package-room-options row ml-0 mr-0 pt-1 pl-1 pr-1 pb-0">
      </div>
    </div>
    <div class="card-footer package-room-selected-option row ml-0 mr-0 pt-1 pl-1 pr-1 pb-0 bg-success text-white">
    </div>
  </div>
  <label id="package-room-package-room-option-model" class="package-room-package-room-option btn btn-block btn-secondary d-flex align-items-center justify-content-start pt-0 pb-0 pl-1 pr-0 mt-0 mb-1" style="cursor:pointer;">
    <span class="mr-2">#<span class="package-room-option-number"></span></span>
    <span class="text-left" style="white-space:normal;">
      <strong class="package-room-option-name"></strong>
      <br />
      <span>
        <small class="package-room-option-board"></small>
        <small class="package-room-option-info"></small>
      </span>
    </span>
    <span class="ml-auto">
      <strong class="package-room-option-price"></strong><br/>
      <strong class="package-room-option-points"></strong><small class=""> puncte</small>
    </span>
    <span class="ml-2 custom-control custom-radio mr-0">
      <input type="radio" class="custom-control-input package-room-option-choice">
      <span class="custom-control-indicator"></span>
    </span>
  </label>
  <div id="package-filter-checkbox-model" class="package-filter-checkbox-model custom-controls-stacked d-block">
    <label class="custom-control custom-checkbox d-block">
      <input type="checkbox" class="filter-option-input custom-control-input">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description filter-option-description d-block"></span>
    </label>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>