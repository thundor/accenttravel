<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$route_departure = $service_flight->Routes[0];
$total_routes = count($service_flight->Routes);
$route_return = $service_flight->Routes[$total_routes-1];
?>
<div class="d-flex justify-content-between">
  <h3>DETALII ZBOR</h3><?php 
  if($can_download_vouchers) { ?>
  <a href="" class="btn btn-primary"><i class="fa fa-download"></i> Descarca voucher</a><?php 
  } ?>
</div>
<ul>
  <li>Numar rezervare: <b><?php echo $service_flight->ReservationId; ?></b></li>
  <li>Numar confirmare: <b><?php echo $service_flight->ConfirmationNo; ?></b></li>
  <li>Tip: <b><?php echo $service_flight->FlightType == 1 ? 'Dus-Intors' : 'Doar dus'; ?></b></li>
  <li>Plecare<br/>
    <ul>
      <li>Data: <b><?php echo $route_departure->OriginDate  . ' ' . str_pad(intval($route_departure->OriginTime/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($route_departure->OriginTime%60, 2, '0', STR_PAD_LEFT); ?></b></li>
      <li>Locatie: <b><?php echo $route_departure->OriginAirportName . ' (' . $route_departure->OriginAirportCode . '-' . $route_departure->OriginCityName . ') '; ?></b></li>
    </ul>
  </li>
  <li>Intoarcere<br/>
    <ul>
      <li>Data: <b><?php echo $route_return->OriginDate  . ' ' . str_pad(intval($route_return->OriginTime/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($route_return->OriginTime%60, 2, '0', STR_PAD_LEFT); ?></b></li>
      <li>Locatie: <b><?php echo $route_return->OriginAirportName . ' (' . $route_return->OriginAirportCode . '-' . $route_return->OriginCityName . ') '; ?></b></li>
    </ul>
  </li>
  <li>Pasageri:<br />
    <ol>
      <?php foreach($service_flight->Passengers as $passenger_index => $passenger) { ?>
      <li><?php echo ucfirst($passenger->Title) . ' ' . $passenger->FirstName . ' ' . $passenger->LastName . ' (' . ($passenger->Type == 'ADT' ? 'adult' : ($passenger->Type == 'SEN' ? 'senior' : ($passenger->Type == 'CHD' ? 'copil' : ($passenger->Type == 'INF' ? 'infant brate' : ($passenger->Type == 'INS' ? 'infant scaun' : 'tanar'))))) . ', ' . ($passenger->BirthDate) . ')'; ?></li>
      <?php } ?>
    </ol>
  </li>
  <?php if(isset($service_flight->Comments) && strlen($service_flight->Comments)){ ?>
  <li>Alte informatii: <b><?php echo $service_flight->Comments; ?></b></li>
  <?php } ?>
</ul>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>