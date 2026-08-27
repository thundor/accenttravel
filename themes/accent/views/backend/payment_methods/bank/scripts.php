<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script type="text/javascript">
;(function($){
$('form.payment_method_settings').on('change input','input,textarea', function(){
  form_change = true;
});
function paymentFormSubmitCallback($form,resp,$error_container){
  if(resp.status !== 'success'){
    return true;
  }
  form_change = false;
  return true;
}
$('form.payment_method_settings').on('submit',function(e){
  e.preventDefault();basicFormPostSubmit(this,this.action,paymentFormSubmitCallback,true);
});
$('form.payment_method_settings').on('change input','input[type=radio][name=status]', function(){
  var status = $('form.payment_method_settings input[type=radio][name=status]:checked').val();
  $('input[type=text],textarea', $('form.payment_method_settings')).prop('required', status!==0);
});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>