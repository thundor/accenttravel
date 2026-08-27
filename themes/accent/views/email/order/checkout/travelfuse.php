<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
$services = unserialize($order->services);
?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/travelfuse/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/travelfuse/subheader_text.php'); ?>
<div style='text-align: left;'><?php 
  include 'travelfuse/order_details.php'; 
  foreach($services as $service){
    if($service['type'] == 'charter'){
      include 'travelfuse/charter.php';
    } 
    elseif($service['type'] == 'circuit'){
      include 'travelfuse/circuit.php';
    }
  }
  include 'travelfuse/billing_details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>