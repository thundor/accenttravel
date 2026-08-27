<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
$services = unserialize($order->services);
?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/paralela45/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/paralela45/subheader_text.php'); ?>
<div style='text-align: left;'><?php 
  include 'paralela45/order_details.php'; 
  foreach($services as $service){
    if($service['type'] == 'strainatate'){
      include 'paralela45/strainatate.php';
    } 
    elseif($service['type'] == 'circuit'){
      include 'paralela45/circuit.php';
    }
  }
  include 'paralela45/billing_details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>