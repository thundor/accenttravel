<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
$trip_order = &$order->trip_order;
$owner = $trip_order->Owner;
?>
<?php if($owner->Title == 'mr'){ ?>
Stimate Domn <?php echo $owner->FirstName; ?> <?php echo $owner->LastName; ?>,
<?php } else { ?>
Stimată Doamnă <?php echo $owner->FirstName; ?> <?php echo $owner->LastName; ?>,
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>