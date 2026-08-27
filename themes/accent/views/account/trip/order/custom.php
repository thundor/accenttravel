<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
$trip_order = &$order->trip_order;
$service_hotel = null;
$service_flight = null;
$service_package = null;
foreach($trip_order->Services as $service){
  if($service->Type == 'hotel'){
    $service_hotel = $service;
  } elseif($service->Type == 'flight'){
    $service_flight = $service;
  } elseif($service->Type == 'package'){
    $service_package = $service;
  }
}
$owner = $trip_order->Owner;
?>
<div style='text-align: left;'><?php 
  include 'common/order_details.php'; 
  if(isset($service_hotel)){
    include 'common/hotel_details.php';
  }
  if(isset($service_flight)){
    include 'common/flight_details.php'; 
    include 'common/flight_itinerary.php'; 
  }
  if(isset($service_package)){
    include 'common/package_details.php';
  }
  include 'common/billing_details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>