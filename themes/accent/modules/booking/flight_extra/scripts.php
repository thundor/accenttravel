<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
	$(document).ready(() => {
		if($('#modal_flight_ryanair').length){
			$('#modal_flight_ryanair').modal('show');
			
			var modal_flight_ryanair_timer = 7;
			var modal_flight_ryanair_timer_interval = setInterval(() => {
				modal_flight_ryanair_timer--;
				$('.modal_flight_ryanair_timer').text('(' + modal_flight_ryanair_timer + ')');
				if(!modal_flight_ryanair_timer){
					$('.modal_flight_ryanair_button').prop('disabled', false);
					$('.modal_flight_ryanair_timer').text('');
					clearInterval(modal_flight_ryanair_timer_interval);
				}
			}, 1000)
		}
	})
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>