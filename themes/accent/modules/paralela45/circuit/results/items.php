<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<form id="offer-booking-form" action="" method="POST">
  <input id="offer-booking-package_id" name="offer[package_id]" value="" type="hidden" />
  <input id="offer-booking-occupancy" name="offer[occupancy]" value="" type="hidden" />
  <input id="offer-booking-package_search_id" name="offer[package_search_id]" value="" type="hidden" />
  <input id="offer-booking-package_variant_id" name="offer[package_variant_id]" value="" type="hidden" />
  <input id="offer-booking-departure_city_code" name="offer[departure_city_code]" value="" type="hidden" />
  <input id="offer-booking-destination_city_code" name="offer[destination_city_code]" value="" type="hidden" />
  <input id="offer-booking-destination_country_code" name="offer[destination_country_code]" value="" type="hidden" />
  <input id="offer-booking-start_date" name="offer[start_date]" value="" type="hidden" />
  <input id="offer-booking-nights" name="offer[nights]" value="" type="hidden" />
  <input id="offer-booking-hotel_name" name="offer[hotel_name]" value="" type="hidden" />
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
</form>
<div id="packageResults">
</div>
<div id="packageResultModel" class="boxHotel" style="display:none;">
  <div class="row">
    <div class="col-sm-12 col-lg-4">
      <a class="hotel-image" href="#"></a>
    </div>
    <div class="col-sm-8 col-lg-6">
      <h2><a href="#" class="package-name"></a><br /><span class="package-stars"></span></h2>
      <p><strong>Disponibilitate:</strong> <span class="package-availability"></span></p>
      <p><strong>Plecare din:</strong> <span class="package-origin"></span></p>
      <p><strong>Intoarcere din:</strong> <span class="package-destination"></span></p>
      <p><strong>Durata circuit:</strong> <span class="package-days"></span> zile</p>
      <p><strong>Destinatii:</strong> <span class="package-destinations"></span></p>
      <strong>Servicii</strong>
      <ul class="package-services" style="white-space:pre-wrap;"></ul>
    </div>
    <div class="col-sm-4 col-lg-2">
      <p>
        <br />
        de la <span class="pretSrcH current-price"></span>
        <br /> 
      </p>
      <a href="javascript:void(0);" class="reserve-button btn btn-block bg-primary rounded-0">REZERVA</a>
      <a class="notification-button btn btn-block btn-success rounded-0" data-toggle="tooltip" title="Notifica-ma cand pretul va scadea cu cel putin 10%"><i class="fa fa-bell" style="color:#fff;"></i> Alerta pret</a>
    </div>
  </div>
  <i class="fa fa-close inchideH"></i>
</div>
<?php //themeFunctions::loadModule('trip/notifications',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>