<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('booking/citybreak_flight_details',__FILE__ . '/pos1'); ?>
<?php themeFunctions::loadModule('booking/flight_extra',__FILE__ . '/pos1'); ?>
<?php themeFunctions::loadModule('booking/hotel_details',__FILE__ . '/pos1'); ?>
<?php themeFunctions::loadModule('booking/info_insurance',__FILE__ . '/pos1'); ?>
<?php themeFunctions::loadModule('booking/travel_options',__FILE__ . '/pos2'); ?>
<?php themeFunctions::loadModule('booking/info_contact',__FILE__ . '/pos2'); ?>
<?php themeFunctions::loadModule('booking/payment_details',__FILE__ . '/pos2'); ?>
<?php themeFunctions::loadAddons(__FILE__ . '/pos1'); ?>
<div class="row mt-4">
  <?php themeFunctions::loadAddons(__FILE__ . '/pos2'); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>