<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
function calculateTotal(){
  var pax_price_total = 0;
  <?php foreach($this->package_availability->Accommodation as $room_index => $selected_packages){
    foreach($selected_packages as $selected_package){
  ?>
  var room_price = parseFloat(<?php echo floatval($selected_package->Price); ?>);
  pax_price_total += room_price;
  $('.package-price-room-<?php echo $room_index+1; ?> .package-price-item-total').text(format_price(room_price,'<?php echo $this->package_availability->Currency; ?>'));
  <?php }
  } ?>
  var has_extra_services = <?php echo $this->package_has_extra_services ? 'true' : 'false'; ?>;
  if(has_extra_services || (<?php echo floatval($this->package_availability->Amount); ?> - pax_price_total > 1)){
    $('.package-price-extra').show();
    $('.package-price-extra-total').text(format_price(<?php echo floatval($this->package_availability->Amount); ?> - pax_price_total, '<?php echo $this->package_availability->Currency; ?>'));
  }
  pax_price_total = <?php echo floatval($this->package_availability->Amount); ?>;
  pax_price_total = applyCoupon(pax_price_total,  '<?php echo $this->package_availability->Currency; ?>', $('.package-price-total'));
  $('.package-price-total span.package-price-item-total').text(format_price(pax_price_total, '<?php echo $this->package_availability->Currency; ?>'));
}
calculateTotal();
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>