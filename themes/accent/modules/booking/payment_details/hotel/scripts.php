<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
function calculateTotal(){
  var pax_price_total = 0;
  <?php foreach($this->room_codes as $rk => $room_code){
    $room = $this->room_objects[$room_code];
  ?>
  var room_price = parseFloat(<?php echo floatval($room->Price->Amount); ?>);
  console.log('room_price', room_price);
  pax_price_total += room_price;
  $('.hotel-price-room-<?php echo $rk+1; ?> .hotel-price-item-total').text(room_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
  <?php } ?>
  pax_price_total = applyCoupon(pax_price_total, '<?php echo $this->_ci->currency_symbol; ?>', $('.hotel-price-total'));
  $('.hotel-price-total span.hotel-price-item-total').text(pax_price_total.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
}
calculateTotal();
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>