<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php themeFunctions::loadLang('trip_item_orders_paralela45'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/paralela45/scripts.php'); ?>
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
              <form id="order_services_form" name="order_services_form" action="<?php echo site_url('backend/paralela45/strainatate/loadServices'); ?>" method="POST" onsubmit="return false;">
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
              </form>
              <div id="result_order_services_form" class="form-group mb-0"></div>
              <form id="order_services_remove_form" name="order_services_remove_form" action="<?php echo site_url('backend/paralela45/strainatate/removeServices'); ?>" method="POST" onsubmit="return false;">
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
              <form id="order_book_services_form" name="order_book_services_form" action="<?php echo site_url('backend/paralela45/strainatate/bookServices'); ?>" method="POST" onsubmit="return false;" style="display:none;">
                <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
                <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
                <?php } ?>
                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                <div class="form-group mb-0 mt-1">
                  <div class="text-center">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-check-square-o"></i><span class="hidden-sm-down">Confirma rezervarea in P45</span></button>
                  </div>
                </div>
              </form>
              <div id="result_order_book_services_form" class="form-group mb-0"></div>
              <?php } ?>
            </div>
          </div>
          <?php if($can_write){ ?>
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
                  <?php include 'paralela45/service_strainatate.php'; ?>
                </div>
                <div class="tab-pane pl-0 pt-1 pb-0 pr-1 " id="service_circuit_tab" role="tabpanel">
                  <?php include 'paralela45/service_circuit.php'; ?>
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
        <form class="order-service-details-form" action="<?php echo site_url('backend/paralela45/strainatate/getOrderService'); ?>" method="POST" onsubmit="return false;">
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
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>