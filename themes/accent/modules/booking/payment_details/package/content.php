<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php
$package_availability = $this->package_availability;
foreach($package_availability->Accommodation as $room_index => $selected_packages){ ?>
<div class="rowDet package-price-room package-price-room-<?php echo $room_index+1; ?>">
  <p>Cazare<?php echo $this->total_rooms>1 ? ' camera ' . ($room_index+1) : ''; ?> <span class="package-price-item-total"></span></p>
</div>
<?php } ?>
<div class="rowDet package-price-extra" style="<?php echo $this->package_has_extra_services ? '' : 'display:none;'; ?>">
  <p>Servicii extra alese <span class="package-price-extra-total"></span></p>
</div>
<div class="totalDet package-price-total">
  <p><span>Total</span>: <span class="package-price-item-total"></span></p>
</div>
<p class=" alert-info p-3">Daca optati pentru plata in sediul agentiilor Accent Trave &amp; Events sau prin transfer bancar, aveti 48 de ore din momentul efectuarii acestei rezervari pentru a realiza plata. Dupa 48 de ore, daca plata nu s-a inregistrat, rezervarea va fi anulata si veti fi instiintat in acest sens.</p>
<?php themeFunctions::debugFileLine('end'); ?>