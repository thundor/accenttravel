<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
  var form_ready = true;
  function hotelCheckoutSubmitCallback($form,resp,$error_container){
    form_ready = true;
    console.log(resp);
    if(resp.status !== 'success'){
      return true;
    }
    $form[0].submit();
    return true;
  }
  $('#confirmButton').on('click', function(){
    addRestOfPassengers();
    return true;
  });
  $('#bookingCheckout').on('submit',function(){
    if(!form_ready){
      return false;
    }
    form_ready = false;
    basicFormPostSubmit(this,this.action,hotelCheckoutSubmitCallback, true);
    return false;
  });
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>