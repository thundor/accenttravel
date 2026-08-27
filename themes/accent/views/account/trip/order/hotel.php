<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
// print_r($order);
$trip_order = &$order->trip_order;
$service_hotel = $trip_order->Services[0];
$owner = $trip_order->Owner;
?>
<div style='text-align: left;'>
  <?php include 'common/order_details.php'; ?>
  <?php include 'common/hotel_details.php'; ?>
  <?php include 'common/billing_details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>