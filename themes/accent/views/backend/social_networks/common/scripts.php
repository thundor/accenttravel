<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script>
$('form.social_network_settings').on('change input','input', function(){
  form_change = true;
});
$('form.social_network_settings').on('submit',function(e){
  console.log('asdfasdf');
  e.preventDefault();basicFormPostSubmit(this,this.action);
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>