<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script>
$('form.social_network_settings').on('change input','input[type=radio][name=status]', function(){
  var status = $('form.social_network_settings input[type=radio][name=status]:checked').val();
  $('input[type=text]', $('form.social_network_settings')).prop('required', status==1);
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>