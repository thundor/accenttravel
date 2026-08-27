<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $product_info = $service['product_info']; ?>
<?php $total_rooms = $service['total_rooms']; ?>
<br />
<h3>DETALII CIRCUIT</h3>
<ul>
  <li>Nume: <b><?php echo $product_info->ProductName; ?></b></li>
  <li>Adresa: <b><?php echo $product_info->CityName . ', ' . $product_info->CountryName; ?></b></li>
  <li>Data plecare: <b><?php echo $service['checkin']; ?></b></li>
  <li>Data intoarcere: <b><?php echo $service['checkout']; ?></b></li>
  <li>Numar persoane: <b><?php echo $service['total_adults'] + $service['total_children']; ?></b></li>
  <?php foreach($service['service_rooms'] as $room_index => $service_room) { 
  $room = $service['offer']->Rooms[$room_index];
  ?>
  <li>Tip camera<?php echo $total_rooms > 1 ? ' #' . ($room_index + 1) : ''; ?>: <b><?php echo $room->_; ?></b><br />
    <ul>
      <li>Persoane:<br />
        <ol><?php 
        foreach($service_room as $guest_type => $guests) {
          foreach($guests as $guest_index => $guest) { ?>
          <li><?php echo $guest['firstname'] . ' ' . $guest['lastname']; ?></li><?php 
          }
        } ?>
        </ol>
      </li>
    </ul>
  </li>
  <?php } ?>
</ul>
<?php if($service['extra_services']) { ?>
<b>Servicii optionale alese</b>
<ul><?php 
foreach($service['extra_services'] as $extra_service){ ?>
  <li><?php echo $extra_service->Name; ?></li><?php 
} ?>
</ul><?php
} ?>
<?php if($service['cancellation_policies']){ ?>
<b>Conditii anulare</b>
<ul><?php 
  foreach($service['cancellation_policies'] as $cancellation_policy){
    if(!isset($cancellation_policy['price']) || $cancellation_policy['price']<=0){
      continue;
    }
    $cancellation_date = DateTime::createFromFormat("Y-m-d", $cancellation_policy['from_date']);
    if($cancellation_policy['percentage']){
      $cancellation_price_formatted = format_price($cancellation_policy['price'], '%');
    } else {
      $cancellation_price_formatted = format_price($cancellation_policy['price'], $order->currency);
    }
    ?>
  <li>Anularea dupa data <?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?> presupune o penalizare de <?php echo $cancellation_price_formatted; ?></li><?php 
  } ?>
</ul>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>