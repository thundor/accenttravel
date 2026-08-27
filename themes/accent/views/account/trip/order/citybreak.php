<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
$trip_order = &$order->trip_order;
foreach($trip_order->Services as $service){
  if($service->Type == 'hotel'){
    $service_hotel = $service;
  } elseif($service->Type == 'flight'){
    $service_flight = $service;
  }
}
$owner = $trip_order->Owner;
?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/common/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/common/subheader_text.php'); ?>
<div style='text-align: left;'>
  <?php include 'common/order_details.php'; ?>
  <?php include 'common/hotel_details.php'; ?>
  <?php include 'common/flight_details.php'; ?>
  <?php include 'common/flight_itinerary.php'; ?>
  <?php include 'common/billing_details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>