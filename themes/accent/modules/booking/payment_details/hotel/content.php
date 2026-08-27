<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php
$total_rooms = count($this->room_codes);
foreach($this->room_codes as $rk => $room_code){ ?>
<div class="rowDet hotel-price-room hotel-price-room-<?php echo $rk+1; ?>">
  <p>Cazare<?php echo $total_rooms>1 ? ' camera ' . ($rk+1) : ''; ?> <span class="hotel-price-item-total"></span></p>
</div>
<?php } ?>
<div class="totalDet hotel-price-total">
  <p><span>Total</span>: <span class="hotel-price-item-total"></span></p>
</div>
<p class=" alert-info p-3">Daca optati pentru plata in sediul agentiilor Accent Trave &amp; Events sau prin transfer bancar, aveti 48 de ore din momentul efectuarii acestei rezervari pentru a realiza plata. Dupa 48 de ore, daca plata nu s-a inregistrat, rezervarea va fi anulata si veti fi instiintat in acest sens.</p>
<?php themeFunctions::debugFileLine('end'); ?>