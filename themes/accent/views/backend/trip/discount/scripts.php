<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$discount = $data['discount'];
$discount = $this->view_data['discount'];
$can_write = $this->_method !='view';
$editing = trim($discount->id) !== '';
if($can_write){ ?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=discountsForm]');
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
  $('#discountsForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
<?php if($discount->id) { ?>
  var min_date = null;
<?php } else { ?>
  var min_date = moment().startOf( 'day' );
<?php } ?>
  $('input[type=text].input-date_start').makeCaleranDatepicker({minDate: min_date, format: 'Y-MM-DD'}).makeInputmaskDate3();
  $('input[type=text].input-date_expire').makeCaleranDatepicker({minDate: min_date, format: 'Y-MM-DD'}).makeInputmaskDate3();
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  