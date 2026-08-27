<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$airline = $this->view_data['airline'];
$can_write = $this->_method !='view';
$editing = trim($airline->code) !== '';
if($can_write){
?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=airlinesForm]');
  $action_buttons.prop('disabled', false);
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  