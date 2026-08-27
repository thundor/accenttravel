<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php themeFunctions::loadLang('trip_item_orders_travelfuse'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/travelfuse/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/orders/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/orders/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/orders/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/orders/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 2;
$label_size['lg'] = 3;
$label_size['md'] = 3;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
$value_offset_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <?php if($can_write){ ?>
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" value="<?php echo $order->id; ?>" />
    <?php } ?>
    <div class="card">
      <div class="card-header pt-1 pr-3 pl-3">
        <ul class="nav nav-tabs card-header-tabs nav-justified">
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#client_info_tab" role="tab" aria-controls="client_info_tab">
              <strong><i class="fa fa-user"></i> Informatii client</strong>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#services_tab" role="tab" aria-controls="services_tab">
              <strong><i class="fa fa-cogs"></i> Servicii</strong>
            </a>
          </li>
          <?php if($order->id){ ?>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#general_tab" role="tab" aria-controls="general_tab">
              <strong><i class="fa fa-cog"></i> General</strong>
            </a>
          </li>
          <?php } ?>
          <?php if(isset($this->view_data['ticket'])){ ?>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#ticket_tab" role="tab" aria-controls="ticket_tab">
              <strong><i class="fa fa-cog"></i> Tichet</strong>
            </a>
          </li>
          <?php } ?>
        </ul>
      </div>
      <div class="tab-content card-block p-1">
        <div class="tab-pane" id="client_info_tab" role="tabpanel">
          <?php include 'orders/client_info.php'; ?>
        </div>
        <div class="tab-pane active" id="services_tab" role="tabpanel">
          <div class="card mb-2 card-primary rounded-0">
            <h2 class="bg-white text-center pt-2 pb-2">Servicii incluse in comanda</h2>
            <div class="card-header pt-1 pb-1 d-flex align-items-center justify-content-between text-white">
              <span><i class="fa fa-cogs"></i><span class="hidden-sm-down">  Servicii comanda</span></span>
              <button id="order_services_form_submit" type="submit" form="order_services_form" class="btn btn-primary"><i class="fa fa-refresh"></i><span class="hidden-sm-down"> Reincarca</span></button>
              <span>Total servicii <strong id="order_services_total">0</strong></span>
            </div>
            <div class="card-block pl-1 pr-1 pt-0 pb-1 bg-white">
              <div id="order_services_no_services" class="alert order_services_no_services alert-info mb-0 fade show mt-1" role="alert" style="display:none;">
                <div class="alert-message">Niciun serviciu adaugat</div>
              </div>
              <form id="order_services_form" name="order_services_form" action="<?php echo site_url('backend/travelfuse/Travelfuse_order/loadServices'); ?>" method="POST" onsubmit="return false;">
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
              </form>
              <div id="result_order_services_form" class="form-group mb-0"></div>
              <form id="order_services_remove_form" name="order_services_remove_form" action="<?php echo site_url('backend/travelfuse/Travelfuse_order/removeServices'); ?>" method="POST" onsubmit="return false;">
                <?php if($can_write){ ?>
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                <?php } ?>
                <input type="hidden" id="order_service_remove_service_id" name="service_id" value="" required/>
              </form>
              <div id="order_services">
              </div>
              <?php if($can_write){ ?>
              <form id="order_book_services_form" name="order_book_services_form" action="<?php echo site_url('backend/travelfuse/Travelfuse_order/bookServices'); ?>" method="POST" onsubmit="return false;" style="display:none;">
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                <div class="form-group mb-0 mt-1">
                  <div class="text-center">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-check-square-o"></i><span class="hidden-sm-down">Confirma rezervarea in TravelFuse</span></button>
                  </div>
                </div>
              </form>
              <div id="result_order_book_services_form" class="form-group mb-0"></div>
              <?php } ?>
            </div>
          </div>
          <?php if($can_write && false){ ?>
          <div id="order_services_wrapper" class="card card-warning rounded-0 mt-5">
            <h2 class="bg-white text-center pt-2 pb-2">Zona de cautare servicii</h2>
            <div class="card-header pt-1 pr-3 pl-3">
              <ul class="nav nav-tabs card-header-tabs nav-justified nav-inverse text-white">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#service_strainatate_tab" role="tab" aria-controls="service_strainatate_tab">
                    <strong><i class="fa fa-building"></i><span class="hidden-sm-down"> Strainatate</span></strong>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link " data-toggle="tab" href="#service_circuit_tab" role="tab" aria-controls="service_circuit_tab">
                    <strong><i class="fa fa-plane"></i><span class="hidden-sm-down"> Circuite</span></strong>
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-block pt-1 pl-1 pr-1 pb-0 bg-white">
              <div class="tab-content">
                <div class="tab-pane pl-0 pt-1 pb-0 pr-1 active" id="service_strainatate_tab" role="tabpanel">
                  <?php include 'travelfuse/service_strainatate.php'; ?>
                </div>
                <div class="tab-pane pl-0 pt-1 pb-0 pr-1 " id="service_circuit_tab" role="tabpanel">
                  <?php include 'travelfuse/service_circuit.php'; ?>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php if($order->id){ ?>
        <div class="tab-pane " id="general_tab" role="tabpanel">
          <?php include 'orders/general.php'; ?>
        </div>
        <?php } ?>
        <?php if(isset($this->view_data['ticket'])){ ?>
        <div class="tab-pane " id="ticket_tab" role="tabpanel">
          <?php include 'orders/ticket.php'; ?>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>
<div id="order-service-models" style="display:none;">
  <div id="order-service-model" class="order-service-model card mt-1">
    <a class="card-header d-flex align-items-center justify-content-between pt-1 pb-1 pl-2 pr-2 collapsed nounderline bg-success text-white" data-toggle="collapse" href="#" aria-expanded="false" role="tab">
      <span class="service-name"><i class="fa fa-cog"></i><span class="hidden-sm-down"> Vezi serviciul</span> #<strong class="service-number"></strong></span>
      <span class="service-types">
        <span class="service-type-strainatate service-type">
          <i class="fa fa-building"></i><span class="hidden-sm-down"> Strainatate</span>
        </span>
        <span class="service-type-circuit service-type">
          <i class="fa fa-plane"></i><span class="hidden-sm-down"> Circuit</span>
        </span>
      </span>
      <span class="service-info">
        <i class="fa fa-info"></i> <span class="hidden-sm-down"> Informatii</span>
      </span>
      <span class="service-price-detail">
        <strong class="service-price"></strong>
      </span>
      <button type="button" class="btn-remove-service btn btn-sm btn-danger"><i class="fa fa-times"></i> <span class="hidden-sm-down"> Elimina</span></button>
    </a>
    <div class="collapse" role="tabpanel">
      <div class="card-block order-service-details pl-1 pr-1 pb-1 pt-0">
        <form class="order-service-details-form" action="<?php echo site_url('backend/travelfuse/Travelfuse_order/getOrderService'); ?>" method="POST" onsubmit="return false;">
          <?php if($can_write){ ?>
          <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
          <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
          <?php } ?>
          <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
          <?php } ?>
          <input type="hidden" name="service_id" value="" required/>
        </form>
        <div class="result-order-service-details-form form-group mb-0"></div>
      </div>
    </div>
  </div>
</div>
<div id="hotel-models" style="display:none;">
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
            <label class="<?php echo $label_class; ?> pt-1 text-center">Transport</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-transport"></strong>
                </div>
                <span class="input-group-addon">
                  <span style="display:flex"><i class="fa fa-plane"></i>/<i class="fa fa-bus"></i></span>
                </span>
              </div>
            </div>
          </div>
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
            <label class="<?php echo $label_class; ?> pt-1 text-center">Perioada</label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div class="form-control">
                  <strong class="service-hotel-period"></strong>
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