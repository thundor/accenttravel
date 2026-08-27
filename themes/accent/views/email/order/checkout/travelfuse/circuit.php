<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $product_info = $service['result']; ?>
<?php $SearchData = $service['SearchData']; ?>
<?php $offer = $service['offer']; ?>
<br />
<h3>DETALII CIRCUIT</h3>
<ul>
  <li>Nume: <b><?php echo $product_info['Title']; ?></b></li>
  <li>Adresa: <b><?php echo ($product_info['Location']['City']['Name'] ?? '') . ', ' .  ($product_info['Location']['Country']['Name'] ?? ''); ?></b></li>
  <li>Data plecare: <b><?php echo $SearchData['CheckIn']; ?></b></li>
  <li>Numar persoane: <b><?php echo $SearchData['Adults'][0] + ($SearchData['Children'][0] ?? 0); ?></b></li>
  <?php foreach($service['service_rooms'] as $room_index => $service_room) {
  ?>
  <li>Persoane:<br />
	<ol><?php 
	foreach($service_room as $guest_type => $guests) {
	  foreach($guests as $guest_index => $guest) { ?>
	  <li><?php echo $guest['Firstname'] . ' ' . $guest['Name']; ?></li><?php 
	  }
	} ?>
	</ol>
  </li>
  <?php } ?>
</ul>
<?php if(!empty($offer['Installments'])){ ?>
<b>Conditii plata</b>
<ul><?php 
  foreach($offer['Installments'] as $cancellation_policy){
    if(!isset($cancellation_policy['Amount']) || $cancellation_policy['Amount']<=0){
      continue;
    }
    $cancellation_date = DateTime::createFromFormat("Y-m-d H:i:s", $cancellation_policy['PayUntil']);
	$cancellation_price_formatted = format_price($cancellation_policy['Amount'], $order->currency);
    ?>
  <li>Inainte de data <?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?> se va plati suma de <?php echo $cancellation_price_formatted; ?></li><?php 
  } ?>
</ul>
<?php } ?>
<?php if(!empty($offer['CancelFees'])){ ?>
<b>Conditii anulare</b>
<ul><?php 
  foreach($offer['CancelFees'] as $cancellation_policy){
    if(!isset($cancellation_policy['Price']) || $cancellation_policy['Price']<=0){
      continue;
    }
    $cancellation_date = DateTime::createFromFormat("Y-m-d H:i:s", $cancellation_policy['DateStart']);
	$cancellation_price_formatted = format_price($cancellation_policy['Price'], $order->currency);
    ?>
  <li>Anularea dupa data <?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?> presupune o penalizare de <?php echo $cancellation_price_formatted; ?></li><?php 
  } ?>
</ul>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>