<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$city = $this->view_data['city'];
$can_write = $this->_method !='view';
$editing = trim($city->id) !== '';
if($can_write){ ?>
<script>
(function($){
	var form_ready = true;
  var $action_buttons = $('button[type=submit][form=citiesForm]');
  $action_buttons.prop('disabled', false);
  $action_buttons.click(function(){
    this.form.task.value = this.value;
  });
  function submitFormSubmitCallback($form,resp,$error_container){
    form_ready = true;
    if(resp.status !== 'success'){
      return true;
    }
    form_change = false;
    $form[0].submit();
    $form[0].task.value = '';
    return true;
  }
  $('#citiesForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  