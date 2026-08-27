<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<script type="text/javascript">
;(function($){
var assuranceCal = $("#asigCal").prop('checked', false);
var assuranceSto = $("#asigSto").prop('checked', false);
$('#asigurareCalatorie').select2_4({theme:'bootstrap',width: '100%', minimumResultsForSearch:10});
$('#asigurareStorno').select2_4({theme:'bootstrap',width: '100%', minimumResultsForSearch:10});
$(assuranceCal).on("change", function(e) {
  $('#asigurareCalatorie').prop('required',true).attr('form', 'bookingCheckout');
  $('#asigurareStorno').prop('required',false).removeAttr('form');
  $("#rowIns").toggle();
  $(".firstP").toggleClass("greenBG");	
  $(".insuranceTBL .firstP i").toggle()	;
  $("#asigSto").toggle().attr('disabled');
  $("#asigSLB").toggle().attr('disabled');
});	

$(assuranceSto).on("change", function(e) {
  $('#asigurareCalatorie').prop('required',false).removeAttr('form');
  $('#asigurareStorno').prop('required',true).attr('form', 'bookingCheckout');
  $("#rowIns").toggle();
  $(".secondP").toggleClass("greenBG");	
  $(".insuranceTBL .secondP i").toggle();	
  $("#asigCal").toggle().attr('disabled');
  $("#asigCLB").toggle().attr('disabled');
}); 
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>