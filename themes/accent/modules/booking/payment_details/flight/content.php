<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$this->_ci->load->model('Options_model');
$flights_settings = $this->_ci->Options_model->get('trip_flights_settings');
?>
<div class="flight-price-items">
  <div class="rowDet flight-price-adt" style="display:none;">
    <p>Adult <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-sen" style="display:none;">
    <p>Senior <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-chd" style="display:none;">
    <p>Copil <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-inf" style="display:none;">
    <p>Bebelus <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-baggage" style="display:none;">
    <p>Bagaje <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-extra-options" style="display:none;">
    <p>Optiuni extra <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-extra-seats" style="display:none;">
    <p>Pret locuri <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-ins_travel" style="display:none;">
    <p>Asigurare Calatorie <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-ins_storno" style="display:none;">
    <p>Asigurare Storno <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-company_tax" style="display:none;">
    <p>Taxa de companie aeriana <span class="flight-price-item-total"></span></p>
  </div>
  <div class="totalDet flight-price-total" style="display:none;">
    <p><span>Total</span>: <span class="flight-price-item-total"></span></p>
    <div class="flight-service-price" style="display:none;">
      <p>Pretul include <span class="blue"> taxa de serviciu</span> in valoare de <strong class="flight-price-service-value"></strong> / pasager.</p>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>