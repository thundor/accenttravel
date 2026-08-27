<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php // themeFunctions::loadModule('booking/flight_flight_details',__FILE__ . '/pos1'); ?>
<?php themeFunctions::loadModule('booking/flight_extra',__FILE__ . '/pos1'); ?>
<?php // themeFunctions::loadModule('booking/info_insurance',__FILE__ . '/pos1'); ?>
<?php // themeFunctions::loadModule('booking/travel_options',__FILE__ . '/pos1'); ?>
<?php // themeFunctions::loadModule('booking/info_contact',__FILE__ . '/pos1'); ?>
<?php // themeFunctions::loadModule('booking/info_flight',__FILE__ . '/pos1'); ?>
<?php //themeFunctions::loadModule('booking/coupon',__FILE__ . '/pos_block', array('once' => true)); ?>
<?php // themeFunctions::loadModule('booking/flight_details',__FILE__ . '/pos1'); ?>
<?php // themeFunctions::loadModule('booking/payment_details',__FILE__ . '/pos1'); ?>
<script>
function calculateFinal(vue_upsell, loading){
	var form = document.getElementById('bookingCheckout');
	if(window.parent !== window){
		if('function' == typeof window.parent['applyBookingOptions']){
			window.parent.applyBookingOptions({
				form: document.getElementById('bookingCheckout'),
				vue_upsell: vue_upsell,
				loading: loading,
			});
		} else {
			console.warn('no applyBookingOptions function');
		}
	} else {
		console.warn('same window');
	}
	// var obj = {
		// upsellCode: vue_upsell.upsell.Code,
		// selectedBookingCodes: vue_upsell.selectedBookingCodes2,
	// }
}
</script>
<div class="row mr-0 bilete">
	<form name="bookingCheckout" id="bookingCheckout"></form>
  <?php themeFunctions::loadAddons(__FILE__ . '/pos1'); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>