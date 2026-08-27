<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script>
$('form.social_contact_mobile_footer').on('change input','input', function(){
  form_change = true;
});
$('form.social_contact_mobile_footer').on('submit',function(e){
  e.preventDefault();basicFormPostSubmit(this,this.action);
});
$('form.social_contact_mobile_footer').on('change input','input[type=radio][name=status]', function(){
  var status = $('form.social_contact_mobile_footer input[type=radio][name=status]:checked').val();
  $('input[type=text]', $('form.social_contact_mobile_footer')).prop('required', status==1);
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>