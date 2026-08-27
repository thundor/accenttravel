<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php 
?>
<?php if($order->user_title == 'mr'){ ?>
Stimate Domn <?php echo trim($order->user_firstname . ' ' . $order->user_lastname); ?>,
<?php } else { ?>
Stimată Doamnă <?php echo trim($order->user_firstname . ' ' . $order->user_lastname); ?>,
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>