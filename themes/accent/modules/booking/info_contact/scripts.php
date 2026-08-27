<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
$("#facturaPJ").on("change", function(e) {
  var checked = $(this).is(':checked');
  if(checked){
    $('select,input', $("#infoPlataPers")).removeAttr('form');
    $('select,input', $("#infoPlataFirma")).attr('form','bookingCheckout');
    $("#infoPlataFirma").show("slow");
    $("#infoPlataPers").hide("slow");
  } else {
    $('select,input', $("#infoPlataFirma")).removeAttr('form');
    $('select,input', $("#infoPlataPers")).attr('form','bookingCheckout');
    $("#infoPlataFirma").hide("slow");
    $("#infoPlataPers").show("slow");
  }
});
$('#copyFirstPas').on('click',function(){
  $(this).prop('checked', false);
  var $first_passenger_row = $('#infoPasagerForm > div:first-child');
  var $infoPlataPers = $('#infoPlataPers');
  var $infoPlataFirma = $('#infoPlataFirma');
  var facturaPJ = $('#facturaPJ').is(':checked');
  var $activeInfoPlata = $infoPlataPers;
  if(facturaPJ){
    $activeInfoPlata = $infoPlataFirma;
  }
  $('input[name="contact_lastname"]', $activeInfoPlata).val( $('input.passenger-lastname', $first_passenger_row).val() );
  $('input[name="contact_firstname"]', $activeInfoPlata).val( $('input.passenger-firstname', $first_passenger_row).val() );
  $('select[name="contact_country"]', $activeInfoPlata).val( $('select.passenger-country', $first_passenger_row).val() ).trigger('change.select2_4');
  $('select[name="contact_phone_prefix"]', $activeInfoPlata).val( $('select.passenger-country', $first_passenger_row).val() ).trigger('change.select2_4');
});
<?php if(!$this->_ci->user->id) { ?>
$(document).on('click', 'input[type=radio][name="create_account"]', function(){
  var $this = $(this);
  if(!$this.is(':checked')){
    return;
  }
  var $form = $(this).closest('.contact-account');
  var value = $this.val();
  if(value == '1'){
    $(".passSet, .passConf", $form).show();
    $('input', $(".passSet, .passConf", $form)).attr('form','bookingCheckout').prop('required',true);
  } else {
    $(".passSet, .passConf", $form).hide();
    $('input', $(".passSet, .passConf", $form)).removeAttr('form').prop('required',false);
  }
});
<?php } ?>
$('#contact_pf_country').select2_4({theme:'bootstrap',placeholder:'Alegeti', data: select2_countries_selections, width: '100%'});
$('#contact_pj_country').select2_4({theme:'bootstrap',placeholder:'Alegeti', data: select2_countries_selections, width: '100%'});
$('#contact_pf_phone_prefix').select2_4({theme:'bootstrap',placeholder:'Alegeti', data: select2_countries_prefix_selections, width: '100%'});
$('#contact_pj_phone_prefix').select2_4({theme:'bootstrap',placeholder:'Alegeti', data: select2_countries_prefix_selections, width: '100%'});

$("input[name='contact_phone']").on("mouseenter  focus", function(e) {
  $(this).next(".infoTEL").show(); 
}).on("mouseleave", function(e) { 
  $(this).next(".infoTEL").hide(); 
});
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>