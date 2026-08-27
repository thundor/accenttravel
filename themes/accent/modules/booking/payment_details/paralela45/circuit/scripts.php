<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $offer = $this->view_data['offer']; ?>
<?php $currency = $this->view_data['currency_code']; ?>
<script type="text/javascript">
function calculateTotal(){
  var pax_price_total = 0;<?php 
  /* foreach($offer->Rooms as $room_index => $room){ ?>
  var room_price = parseFloat(<?php echo floatval($room->Price); ?>);
  pax_price_total += room_price;
  $('.package-price-room-<?php echo $room_index+1; ?> .package-price-item-total').text(format_price(room_price,'<?php echo $currency; ?>')); <?php 
  } */
  if(!empty($offer->Services)){
    $service_price = 0;
    foreach($offer->Services as $service_index => $service){
      $service_price += floatval($service->Gross);
    } ?>
  var service_prices = <?php echo $service_price; ?>;
  $('.package-price-services').show();
  $('.package-price-services-total').text(format_price(service_prices, '<?php echo $currency; ?>'));<?php
  } ?>
  pax_price_total = <?php echo floatval($offer->Price); ?>;<?php
  if(!empty($this->view_data['extra_services'])){ ?>
  var extra_services_price = 0;
  var $checked_services = $('input.offer-extra-service:checked');
  if(!$checked_services.length){
    $('.package-price-extra-services').hide();
  } else {
    $checked_services.each(function(){
      extra_services_price+=parseFloat($(this).data('price'));
    });
    $('.package-price-extra-services').show();
    $('.package-price-extra-services-total').text(format_price(extra_services_price, '<?php echo $currency; ?>'));
    pax_price_total+=extra_services_price;
  }<?php
  } ?>
  pax_price_total = applyCoupon(pax_price_total,  '<?php echo $currency; ?>', $('.package-price-total'));
  $('.package-price-total span.package-price-item-total').text(format_price(pax_price_total, '<?php echo $currency; ?>'));
  $('#totalExpectedPrice').val(pax_price_total);
}
$('input.offer-extra-service').on('change',function(){
  calculateTotal();
});
calculateTotal();
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>