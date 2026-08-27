<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script>
$('form.trip_form').on('change input','input', function(){
  form_change = true;
});
$('form.trip_form').on('submit',function(e){
  e.preventDefault();basicFormPostSubmit(this,this.action);
});
$('#trip_password_toggle').change(function(){
  var new_type = $(this).is(':checked') ? 'text' : 'password';
  $('#trip_password').attr('type', new_type);
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>