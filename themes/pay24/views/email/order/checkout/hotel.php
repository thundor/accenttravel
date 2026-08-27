<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
// print_r($order);
$trip_order = &$order->trip_order;
$service_hotel = $trip_order->Services[0];
$owner = $trip_order->Owner;
?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/common/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/common/subheader_text.php'); ?>
<div style='text-align: left;'>
  <?php include 'common/order_details.php'; ?>
  <?php include 'common/hotel_details.php'; ?>
  <?php include 'common/billing_details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>