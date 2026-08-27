<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php
$offer = $this->view_data['offer'];
$total_rooms = $this->view_data['total_rooms'];
/* foreach($offer->Rooms as $room_index => $room){ ?>
<div class="rowDet package-price-room package-price-room-<?php echo $room_index+1; ?>">
  <p>Cazare<?php echo $total_rooms>1 ? ' camera ' . ($room_index+1) : ''; ?> <span class="package-price-item-total"></span></p>
</div>
<?php } */ ?>
<div class="rowDet package-price-services" style="<?php echo !empty($offer->Services) ? '' : 'display:none;'; ?>">
  <p>Servicii incluse <span class="package-price-services-total"></span></p>
</div>
<div class="rowDet package-price-extra-services" style="display:none;">
  <p>Servicii extra alese <span class="package-price-extra-services-total"></span></p>
</div>
<div class="totalDet package-price-total">
  <p><span>Total</span>: <span class="package-price-item-total"></span></p>
</div>
<p class=" alert-info p-3">Daca optati pentru plata in sediul agentiilor Accent Travel &amp; Events sau prin transfer bancar, aveti 48 de ore din momentul efectuarii acestei rezervari pentru a realiza plata. Dupa 48 de ore, daca plata nu s-a inregistrat, rezervarea va fi anulata si veti fi instiintat in acest sens.</p>
<?php themeFunctions::debugFileLine('end'); ?>