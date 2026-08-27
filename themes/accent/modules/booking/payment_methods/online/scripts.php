<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
  $('#payment_methods input[type=radio][name=payment_method]').on('click', function(){
    if(!$(this).is(':checked')){
      return;
    }
    $('#payment_gateways').toggleClass('show', this.value === 'online');
  });
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>