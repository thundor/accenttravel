<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
$(function () {
  var $price_slider = $("#slider-range").slider({
    range: true,
    min: 150,
    max: 2500,
    values: [175, 1300],
    slide: function (event, ui) {
      $(this).trigger('updatePrice', ui);
    }
  }).on('updatePrice', function(e, ui){
    if(ui){
      var slider_values = ui.values;
    } else {
      var $price_slider = $(this).slider();
      var slider_values = $price_slider.slider('values');
    }
    $("#amount").val(parseFloat(slider_values[ 0 ]).toLocaleString('ro') + " € - " + parseFloat(slider_values[ 1 ]).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
  });
  $price_slider.trigger('updatePrice');
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>