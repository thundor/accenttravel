<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<div class="flight-price-items">
  <?php
  $total_rooms = count($this->room_codes);
  foreach($this->room_codes as $rk => $room_code){ ?>
  <div class="rowDet hotel-price-room hotel-price-room-<?php echo $rk+1; ?>">
    <p>Cazare<?php echo $total_rooms>1 ? ' camera ' . ($rk+1) : ''; ?> <span class="hotel-price-item-total"></span></p>
  </div>
  <?php } ?>
  <div class="rowDet flight-price-adt" style="display:none;">
    <p>Bilet Avion Adult <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-sen" style="display:none;">
    <p>Bilet Avion Senior <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-chd" style="display:none;">
    <p>Bilet Avion Copil <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-inf" style="display:none;">
    <p>Bilet Avion Bebelus <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-baggage" style="display:none;">
    <p>Bagaje <span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-extra-options" style="display:none;">
    <p>Optiuni extra Zbor<span class="flight-price-item-total"></span></p>
  </div>
  <div class="rowDet flight-price-extra-seats" style="display:none;">
    <p>Pret locuri Zbor<span class="flight-price-item-total"></span></p>
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
<p class=" alert-info p-3">Daca optati pentru plata in sediul agentiilor Accent Trave &amp; Events sau prin transfer bancar, aveti 48 de ore din momentul efectuarii acestei rezervari pentru a realiza plata. Dupa 48 de ore, daca plata nu s-a inregistrat, rezervarea va fi anulata si veti fi instiintat in acest sens.</p>
<?php themeFunctions::debugFileLine('end'); ?>