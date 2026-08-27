<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$package = $service_package->Package;
$rooms = &$service_package->Rooms;
foreach($rooms as $room_index =>$room){
  $room->Guests = array();
}
$occupants = array();
foreach($service_package->Occupants as $occupant){
  $occupant->ExtraServices = array();
  $occupants[$occupant->PaxIdx] = $occupant;
}
$total_occupants = count($occupants);
$extra_services_occupants_no = array();
$common_extra_services = array();
$extra_services = array();
if(empty($service_package->ExtraServices)){
  $service_package->ExtraServices = array();
}
foreach($service_package->ExtraServices as &$extra_service){
  $extra_services_occupants_no[$extra_service->Id] = 0;
  $extra_services[$extra_service->Id] = $extra_service;
  foreach($extra_service->Occupants as $occupant){
    $extra_services_occupants_no[$extra_service->Id] ++;
    if($extra_services_occupants_no[$extra_service->Id] == $total_occupants){
      $common_extra_services[$extra_service->Id] = $extra_service;
    }
    $occupants[$occupant->PaxIdx]->ExtraServices[] = $extra_service->Id;
  }
}
foreach($occupants as $occupant_key =>$occupant){
  $rooms[$occupant->OccupationIdx]->Guests[] = $occupant;
}
$first_room = $rooms[0];
$total_rooms = count($rooms);

?>
<br />
<h3>DETALII VACANTA</h3>
<ul>
  <li>Numar confirmare: <b><?php echo $service_package->ConfirmationNo; ?></b></li>
  <li>Nume: <b><?php echo $package->Name; ?></b></li>
  <li>Categorie: <b><?php echo $package->ProjectName; ?></b></li>
</ul>
<br />
<h3>DETALII HOTEL</h3>
<ul>
  <li>Nume: <b><?php echo $first_room->UnitName; ?></b></li>
  <li>Adresa: <b><?php echo $first_room->CityName; ?></b></li>
  <li>Check-in: <b><?php echo $service_package->StartDate; ?></b></li>
  <li>Check-out: <b><?php echo $service_package->EndDate; ?></b></li>
  <li>Numar persoane: <b><?php echo $service_package->Adults + $service_package->Children; ?></b></li>
  <?php foreach($rooms as $room_index => $room) { ?>
  <li>Tip camera<?php echo $total_rooms > 1 ? ' #' . ($room_index + 1) : ''; ?>: <b><?php echo $room->RoomTypeDescription; ?></b><br />
    <ul>
      <li>Board: <b><?php echo $room->RoomFeature; ?></b></li>
      <li>Persoane: <b><?php echo count($room->Guests); ?></b><br />
        <ol>
          <?php foreach($room->Guests as $guest_index => $guest) { ?>
          <li><?php echo ucfirst($guest->Title) . ' ' . $guest->FirstName . ' ' . $guest->LastName . ' (' . ($guest->Type == 'a' ? 'adult' : 'copil') . ', ' . ($guest->BirthDate) . ')'; ?>
          <?php 
            $guest_extra_services_ids = array_diff($guest->ExtraServices, array_keys($common_extra_services));
            if($guest_extra_services_ids){ ?>
            <br />Servicii alese:<br />
            <ul><?php
            foreach($guest_extra_services_ids as $guest_extra_services_id){
              $extra_service = $extra_services[$guest_extra_services_id]; ?>
              <li><?php echo $extra_service->Name ?><?php /*  <b><?php echo $extra_service->Mandatory ? 'inclus' : 'ales'; ?></b> */ ?></li><?php
            } ?>
            </ul><?php
            } ?>
          </li><?php
          } ?>
        </ol>
      </li>
    </ul>
  </li>
  <?php } ?>
  <li>Servicii extra comune:
    <ul>
      <?php foreach($common_extra_services as $extra_service){ ?>
      <li><?php echo $extra_service->Name ?><?php /* <b><?php echo $extra_service->Mandatory ? 'inclus' : 'ales'; ?></b> */ ?></li>
      <?php } ?>
    </ul>
  </li>
</ul>
  <?php /* 
<ul>
  <li>Numar rezervare: <b><?php echo $service_package->ReservationId; ?></b></li> */ ?>
  <?php /*
  
  <li>Numar confirmare: <b><?php echo $service_package->ConfirmationNo; ?></b></li>
  <li>Nume: <b><?php echo $package->Name; ?></b></li>
  <li>Adresa: <b><?php echo $package->Address; ?></b></li>
  <li>Check-in: <b><?php echo $service_package->Checkin; ?></b></li>
  <li>Check-out: <b><?php echo $service_package->Checkout; ?></b></li>
  <li>Numar persoane: <b><?php echo $service_package->Adults + $service_package->Children; ?></b></li>
  <?php foreach($rooms as $room_index => $room) { ?>
  <li>Tip camera<?php echo $total_rooms > 1 ? ' #' . ($room_index + 1) : ''; ?>: <b><?php echo $room->Name; ?></b><br />
    <ul>
      <li>Board: <b><?php echo $room->Board; ?></b></li>
      <li>Extra-info: <b><?php echo $room->Info; ?></b></li>
      <li>Pasageri: <b><?php echo count($room->Guests); ?></b><br />
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
  <?php foreach($service_package->Remarks as $remark) {
  ?>
  <li><?php echo nl2br($remark->Name); ?></li>
  <?php } ?>
</ul>

 */ ?>
<br />
<b>Conditii anulare</b>
<ul>
  <?php foreach($service_package->CancellationPolicies as $cancellation_policy) { 
  $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:s", $cancellation_policy->Limit);
  ?>
  <li>Anularea dupa <b><?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?></b> atrage o penalizare de <b><?php echo format_price($cancellation_policy->Amount, $cancellation_policy->Currency); ?></b></li>
  <?php } ?>
</ul>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>