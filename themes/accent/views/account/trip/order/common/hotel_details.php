<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$hotel = $service_hotel->Hotel;
$rooms = $service_hotel->Products;
$total_rooms = count($rooms);
?>
<br />
<div class="d-flex justify-content-between">
  <h3>DETALII HOTEL</h3><?php 
  if($can_download_vouchers) { ?>
  <a href="" class="btn btn-primary"><i class="fa fa-download"></i> Descarca voucher</a><?php 
  } ?>
</div>
<ul>
  <?php /* <li>Numar rezervare: <b><?php echo $service_hotel->ReservationId; ?></b></li> */ ?>
  <li>Numar confirmare: <b><?php echo $service_hotel->ConfirmationNo; ?></b></li>
  <li>Nume: <b><?php echo $hotel->Name; ?></b></li>
  <li>Adresa: <b><?php echo $hotel->Address; ?></b></li>
  <li>Check-in: <b><?php echo $service_hotel->Checkin; ?></b></li>
  <li>Check-out: <b><?php echo $service_hotel->Checkout; ?></b></li>
  <li>Numar persoane: <b><?php echo $service_hotel->Adults + $service_hotel->Children; ?></b></li>
  <?php foreach($rooms as $room_index => $room) { ?>
  <li>Tip camera<?php echo $total_rooms > 1 ? ' #' . ($room_index + 1) : ''; ?>: <b><?php echo $room->Name; ?></b><br />
    <ul>
      <li>Board: <b><?php echo $room->Board; ?></b></li>
      <li>Extra-info: <b><?php echo $room->Info; ?></b></li>
      <li>Persoane: <b><?php echo count($room->Guests); ?></b><br />
        <ol>
          <?php foreach($room->Guests as $guest_index => $guest) { ?>
          <li><?php echo ucfirst($guest->Title) . ' ' . $guest->FirstName . ' ' . $guest->LastName; ?></li>
          <?php } ?>
        </ol>
      </li>
    </ul>
  </li>
  <?php } ?>
</ul>
<br />
<b>Informatii specifice hotel</b>
<ul>
  <?php foreach($service_hotel->Remarks as $remark) {
  ?>
  <li><?php echo nl2br($remark->Name); ?></li>
  <?php } ?>
</ul>
<br />
<b>Conditii anulare</b>
<ul>
  <?php foreach($service_hotel->CancellationPolicies as $cancellation_policy) { 
  $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:sP", $cancellation_policy->Limit);
  ?>
  <li>Anularea dupa <b><?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?></b> atrage o penalizare de <b><?php echo format_price($cancellation_policy->Amount, $cancellation_policy->Currency); ?></b></li>
  <?php } ?>
</ul>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>