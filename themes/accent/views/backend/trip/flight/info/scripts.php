<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script type="text/javascript">
;(function($){
$('form.trip_flight_info').on('change input','input,textarea', function(){
  form_change = true;
});
function paymentFormSubmitCallback($form,resp,$error_container){
  if(resp.status !== 'success'){
    return true;
  }
  form_change = false;
  return true;
}
$('form.trip_flight_info').on('submit',function(e){
  e.preventDefault();basicFormPostSubmit(this,this.action,paymentFormSubmitCallback,true);
});
// $('form.trip_flight_info').on('change input','input[type=radio][name=status]', function(){
  // var status = $('form.trip_flight_info input[type=radio][name=status]:checked').val();
  // $('input[type=text],textarea', $('form.trip_flight_info')).prop('required', status!==0);
// });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>