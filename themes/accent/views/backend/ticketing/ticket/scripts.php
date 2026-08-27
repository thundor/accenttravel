<?php 
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::debugFileLine('start');
$data = $this->view_data;
$ticket = $this->view_data['ticket'];
$can_write = $this->_method !='view';
if($can_write) {
themeFunctions::jsLang('warning_form_not_ready');
?>
<script>
(function($) {
  var form_ready = true;
  var $message_container = $('#result_ticketForm');
  var $action_buttons = $('button[type=submit][form=ticketForm]');
  $action_buttons.click(function(){
    this.form.task.value = this.value;
  });
  $action_buttons.prop('disabled', false);
  
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
  $('#ticketForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  
  $('#ticket_user_id').select2_4({theme:'bootstrap',placeholder:'Alegeti consilierul', width: '100%'});
})(jQuery);
</script>
<?php
}
themeFunctions::debugFileLine('end');
themeFunctions::loadAddons(__FILE__);
?>
  