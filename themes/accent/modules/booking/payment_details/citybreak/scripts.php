<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$flights_settings = $this->_ci->Options_model->get('trip_flights_settings');
?>
<script type="text/javascript">
function calculateTotal(){
  var fdata = flight_data;
  var v = false;
  if('object' === typeof vue_upsell && vue_upsell){
    var v = true;
    if(vue_upsell.upsell){
      fdata = vue_upsell.upsell;
    }
  }
  var pax_price_total = parseFloat(v && vue_upsell.upsell ? fdata.Price.Amount : fdata.Price);
  var total_passengers = 0;
  $('.flight-price-items > .rowDet').hide();
  <?php foreach($this->room_codes as $rk => $room_code){
    $room = $this->room_objects[$room_code];
  ?>
  var room_price = parseFloat(<?php echo floatval($room->Price->Amount); ?>);
  pax_price_total += room_price;
  $('.hotel-price-room-<?php echo $rk+1; ?> .hotel-price-item-total').text(room_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
  $('.hotel-price-room-<?php echo $rk+1; ?>').show();
  <?php } ?>
  
  $('.flight-price-total').hide();
  var flights_settings = <?php echo json_encode($flights_settings); ?>;
  var service_price = 0;
  for(var i = 0; i<flight_data.FareDetails.PaxFare.length; i++){
    var pax_item = flight_data.FareDetails.PaxFare[i];
    var pax_type = pax_item.PTC.toLowerCase();
    var pax_count = parseInt(pax_item.Count);
    total_passengers += pax_count;
    var pax_price = parseFloat(pax_item.FullFare) + parseFloat(pax_item.ServiceFee);
    // pax_price_total += pax_count * pax_price;
    service_price = parseFloat(pax_item.ServiceFee);
    $('.flight-price-' + pax_type + ' span.flight-price-item-total').text(pax_count + ' x ' + pax_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    $('.flight-price-' + pax_type).show();
  }
  if(v && vue_upsell.priceOptions){
    var pax_type = 'extra-options';
    var pax_count = 1;
    var pax_price = vue_upsell.priceOptions;
    pax_price_total += pax_count * pax_price;
    $('.flight-price-' + pax_type + ' span.flight-price-item-total').text(pax_count + ' x ' + pax_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    $('.flight-price-' + pax_type).show();
  }
  if(v && vue_upsell.priceSeats){
    var pax_type = 'extra-seats';
    var pax_count = 1;
    var pax_price = vue_upsell.priceSeats;
    pax_price_total += pax_count * pax_price;
    $('.flight-price-' + pax_type + ' span.flight-price-item-total').text(pax_count + ' x ' + pax_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    $('.flight-price-' + pax_type).show();
  }
  if($('#asigCal').is(':checked')){
    var pax_type = 'ins_travel';
    var pax_count = total_passengers;
    var ins_price_selected = parseInt($('#asigurareCalatorie').val());
    var pax_price = flights_settings.travel_prices[ins_price_selected].price;
    pax_price_total += pax_count * pax_price;
    $('.flight-price-' + pax_type + ' span.flight-price-item-total').text(pax_count + ' x ' + pax_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    $('.flight-price-' + pax_type).show();
  }
  if($('#asigSto').is(':checked')){
    var pax_type = 'ins_storno';
    var pax_count = total_passengers;
    var ins_price_selected = parseInt($('#asigurareStorno').val());
    var pax_price = flights_settings.storno_prices[ins_price_selected].price;
    pax_price_total += pax_count * pax_price;
    $('.flight-price-' + pax_type + ' span.flight-price-item-total').text(pax_count + ' x ' + pax_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    $('.flight-price-' + pax_type).show();
  }
  // var service_price = parseFloat(flights_settings.service_price);
  if(service_price > 0){
    $('.flight-price-service-value').text(service_price.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    $('.flight-service-price').show();
  } else {
    $('.flight-service-price').hide();
  }
  pax_price_total = applyCoupon(pax_price_total, '<?php echo $this->_ci->currency_symbol; ?>', $('.flight-price-total'));
  $('.flight-price-total span.flight-price-item-total').text(pax_price_total.toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
  $('.flight-price-total').show();
}
calculateTotal();
$('#asigCal,#asigSto,#asigurareCalatorie,#asigurareStorno').on('change', function(){
  calculateTotal();
});
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>