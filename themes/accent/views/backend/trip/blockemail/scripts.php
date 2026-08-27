<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$blockemail = $this->view_data['blockemail'];
$can_write = $this->_method !='view';
$editing = trim($blockemail->id) !== '';
if($can_write){ ?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=blockemailsForm]');
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
  $('#blockemailsForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  
  $('input[type=text].input-date_start').makeCaleranDatepicker({format: 'Y-MM-DD'}).makeInputmaskDate3();
  $('input[type=text].input-date_expire').makeCaleranDatepicker({format: 'Y-MM-DD'}).makeInputmaskDate3();
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  