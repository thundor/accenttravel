<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$route_departure = $service_flight->Routes[0];
$total_routes = count($service_flight->Routes);
$return_routes = array_filter($service_flight->Routes, function($r){
	return !empty($r->RouteType);
});
$return_routes_nk = array_values($return_routes);
?>
<?php /* if(!empty($_GET['vaaad'])){ ?>
<pre>
<?php print_r($return_routes); ?>
</pre>
<?php } */ ?>
<?php /*
<pre><?php print_r($service_flight); ?>
</pre> */ ?>
<br />
<table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="E6F6FF" style="border-radius:4px;overflow:hidden;">
  <tbody>
	<tr>
	  <td style="border-collapse: collapse;" height="60" valign="middle" bgcolor="E6F6FF">
		<br />
		<table class="table600" width="585" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="E6F6FF" style="border-radius:4px;overflow:hidden;padding-left:15px">
			<tbody>
				<tr>
				  <td style="border-collapse: collapse;" height="60" valign="middle" bgcolor="E6F6FF">
					
<h3>DETALII ZBOR</h3>
<ul style="list-style:none;padding-left:10px;">
  <li>Numar rezervare: <b><?php echo $service_flight->ReservationId; ?></b></li>
  <li>Numar confirmare: <b><?php echo $service_flight->ConfirmationNo; ?></b></li>
  <li>Tip: <b><?php echo $service_flight->FlightType == 1 ? 'Dus-Intors' : 'Doar dus'; ?></b></li>
  <li>Plecare<br/>
    <ul style="list-style:none;">
      <li>Data: <b><?php echo $route_departure->OriginDate  . ' ' . str_pad(intval($route_departure->OriginTime/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($route_departure->OriginTime%60, 2, '0', STR_PAD_LEFT); ?></b></li>
      <li>Locatie: <b><?php echo $route_departure->OriginAirportName . ' (' . $route_departure->OriginAirportCode . '-' . $route_departure->OriginCityName . ') '; ?></b></li>
    </ul>
  </li>
  <?php if(!empty($return_routes_nk)) { 
  $route_return = $return_routes_nk[0]; ?>
  <li>Intoarcere<br/>
    <ul style="list-style:none;">
      <li>Data: <b><?php echo $route_return->OriginDate  . ' ' . str_pad(intval($route_return->OriginTime/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($route_return->OriginTime%60, 2, '0', STR_PAD_LEFT); ?></b></li>
      <li>Locatie: <b><?php echo $route_return->OriginAirportName . ' (' . $route_return->OriginAirportCode . '-' . $route_return->OriginCityName . ') '; ?></b></li>
    </ul>
  </li>
  <?php } ?>
  <li>Pasageri:<br />
    <ol style="list-style:none;">
      <?php foreach($service_flight->Passengers as $passenger_index => $passenger) { ?>
      <li><?php echo ucfirst($passenger->Title) . ' ' . $passenger->FirstName . ' ' . $passenger->LastName . ' (' . ($passenger->Type == 'ADT' ? 'adult' : ($passenger->Type == 'SEN' ? 'senior' : ($passenger->Type == 'CHD' ? 'copil' : ($passenger->Type == 'INF' ? 'infant brate' : ($passenger->Type == 'INS' ? 'infant scaun' : 'tanar'))))) . ', ' . ($passenger->BirthDate) . ')'; ?></li>
      <?php } ?>
    </ol>
  </li>
  <?php if(isset($service_flight->PaidSeats) && !empty($service_flight->PaidSeats)){ ?>
  <li>Locuri platite: <br/>
    <ol style="list-style:none;">
      <?php foreach($service_flight->PaidSeats as $PaidSeat) { ?>
      <li><?php echo 'Zbor #' . (1 + $PaidSeat->LegIndex + $PaidSeat->SegmentIndex) . ' Pasager #' . (1 + $PaidSeat->PassengerIndex) . ' Loc ' . $PaidSeat->SeatNumber . '' . $PaidSeat->SeatColumn . ' (' . format_price($PaidSeat->Amount, $PaidSeat->Currency) . ')'; ?></li>
      <?php } ?>
    </ol>
  <?php } ?>
  <?php if(isset($service_flight->OptionalServices) && !empty($service_flight->OptionalServices)){ ?>
  <li>Optiuni: <br/>
    <ol style="list-style:none;">
      <?php foreach($service_flight->OptionalServices as $OptionalService) { ?>
      <li><?php echo 'Ruta ' . $OptionalService->Departure . '-' . $OptionalService->Arrival . ', Pasager ' . $OptionalService->Target . '#' . (1 + $OptionalService->PassengerIndex) . ' Optiune: ' . $OptionalService->Name . ' (' . (empty($OptionalService->Amount) ? 'Inclus' : format_price($OptionalService->Amount, $OptionalService->Currency)) . ')'; ?></li>
      <?php } ?>
    </ol>
  <?php } ?>
  <?php $SeatDetails = array(); ?>
  <?php if(isset($service_flight->Details) && !empty($service_flight->Details)){ 
    foreach($service_flight->Details as $DetailKey => $DetailValue) {
      $DetailArr = explode(':', $DetailKey); 
      $Detail2Arr = explode('_', $DetailArr[1]); 
      if($DetailArr[0] == 'SEAT'){
        $SeatDetails[$Detail2Arr[1] . '_' . $Detail2Arr[2]][$DetailArr[2]] = $DetailValue;
      }
    }
  } ?>
  <?php if(!empty($SeatDetails)){ ?>
  <li>Locuri preferate: <br/>
    <ol style="list-style:none;">
      <?php foreach($SeatDetails as $SeatDetailKey => $DetailValue) { $legNumber = 1 + array_sum(explode('_', $SeatDetailKey)); ?>
      <li><?php echo 'Zbor #' . $legNumber . ' Ruta ' . $DetailValue['ORIGIN'] . '-' . $DetailValue['DESTINATION'] . ' ' . (!empty($DetailValue['PREFERENCE']) ? ('A' == $DetailValue['PREFERENCE'] ? 'Culoar' : 'Fereastra') : 'Loc ' . $DetailValue['NUMBER'] . $DetailValue['CODE']); ?></li>
      <?php } ?>
    </ol>
  <?php } ?>
  </li>
  <?php if(isset($service_flight->Comments) && strlen($service_flight->Comments)){ ?>
  <li>Alte informatii: <b><?php echo $service_flight->Comments; ?></b></li>
  <?php } ?>
</ul>
<?php /* if(!empty($_GET['vaaad']) && !empty($SeatDetails)){ ?>
<pre>
<?php print_r($SeatDetails); ?>
<?php print_r($service_flight); ?>
</pre>
<?php } */ ?>
				  </td>
				</tr>
			</tbody>
		</table>
	  </td>
	</tr>
  </tbody>
</table>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>