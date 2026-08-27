<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<pre>
<?php //print_r($service_flight); ?>
</pre>
<br />
<h4>ITINERARIU ZBOR</h4>
<hr />
<?php if(($service_flight->FlightType == 1)){ ?>
<b>TUR</b><br/>
<?php } ?>
<?php 
$found_retur = false;
foreach($service_flight->Routes as $route){ 
if(($service_flight->FlightType == 1) && ($route->RouteType == 1) && !$found_retur){
$found_retur = true;
?>
<b>RETUR</b><br/>
<?php 
} ?>
<?php if($route->FlightStopTime){ ?>
Stationare: <b><?php echo intval($route->FlightStopTime/60) . 'h ' . ($route->FlightStopTime%60) . 'min'; ?></b>
<?php } ?>
<ul>
  <li>Plecare din: <b><?php echo $route->OriginAirportName . ' (' . $route->OriginAirportCode . '-' . $route->OriginCityName . ') ' . $route->OriginDate . ' ' . str_pad(intval($route->OriginTime/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($route->OriginTime%60, 2, '0', STR_PAD_LEFT) . ($route->OriginTerminal ? ' Terminal ' . $route->OriginTerminal : ''); ?></b></li>
  <li>Sosire in: <b><?php echo $route->DestinationAirportName . ' (' . $route->DestinationAirportCode . '-' . $route->DestinationCityName . ') ' . $route->OriginDate . ' ' . str_pad(intval($route->DestinationTime/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($route->DestinationTime%60, 2, '0', STR_PAD_LEFT) . ($route->DestinationTerminal ? ' Terminal ' . $route->DestinationTerminal : ''); ?></b></li>
  <li>Numar zbor: <b><?php echo $route->CarrierMarketingCode . $route->FlightNumber . ($route->CarrierMarketingCode != $route->CarrierOperatingCode ? ' Operat de ' . $route->CarrierOperatingName : ''); ?></b></li>
  <li>Tip avion: <b><?php echo $route->AircraftName; ?></b></li>
  <li>Companie aeriana: <b><?php echo $route->CarrierMarketingName; ?></b></li>
  <?php /*<li>Locuri disponibile: <b><?php echo $route->FlightNumberOfSeats; ?></b></li> */ ?>
</ul>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>