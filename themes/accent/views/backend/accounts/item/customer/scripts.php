<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$user = $this->view_data['user'];
$can_write = $this->_method !='view';
if($can_write){
themeFunctions::jsLang('warning_form_not_ready');
?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=customerForm]');
  function enableActionButtons(){
    $action_buttons.prop('disabled',false);
  }
  function disableActionButtons(){
    $action_buttons.prop('disabled',true);
  }
  var form_ready = true;
  var editing = <?php echo $editing = $data['user']->id != 0 ? 1 : 0; ?>;
  $action_buttons.on('click',function(e){
    var action = this.value;
    var validate_as = editing ? 'edit' : 'add';
    if(editing){
      if(action === 'save_as_new'){
        validate_as = 'add';
      }
    }
    $('#password').prop('required',validate_as === 'add');
    this.form.action.value = action;
    return true;
  });
  $action_buttons.prop('disabled', false);
  function submitCallback($form,resp,$error_container){
    form_ready = true;
    if(resp.status !== 'success'){
      return true;
    }
    var form = $form[0];
    var action = form.action.value;
    if(action == 'save_and_back'){
      window.top.location = '<?php echo site_url('backend/accounts/customer'); ?>';
      return;
    }
    if(action == 'save_and_new'){
      window.top.location = '<?php echo site_url('backend/accounts/customer/add'); ?>';
      return;
    }
    if((!editing && action == 'apply') || (action == 'save_as_new')){
      window.top.location = resp.data.edit_link;
      return;
    }
    window.top.location.reload();
    return false;
  }
  $('#customerForm').on('submit',function(e){
    e.preventDefault();
    if(!form_ready){
      alert(js_lang.warning_form_not_ready);
      return false;
    }
    form_ready = false;
    var action = this.action.value;
    var id = this.id.value;
    if(!editing || (editing && action == 'save_as_new')){
      this.id.value = 0;
    }
    basicFormPostSubmit(this,"<?php echo site_url('backend/accounts/customer/save'); ?>",submitCallback,true);
    this.id.value = id;
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  