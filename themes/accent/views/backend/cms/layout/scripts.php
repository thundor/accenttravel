<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$layout = $this->view_data['layout'];
$can_write = $this->_method !='view';
$editing = trim($layout->slug) !== '';
if($can_write){
themeFunctions::jsLang('warning_form_not_ready');
?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=layoutForm]');
  function enableActionButtons(){
    $action_buttons.prop('disabled',false);
  }
  function disableActionButtons(){
    $action_buttons.prop('disabled',true);
  }
  var form_ready = true;
  var editing = <?php echo $editing ? 1 : 0; ?>;
  $action_buttons.on('click',function(e){
    var action = this.value;
    var validate_as = editing ? 'edit' : 'add';
    if(editing){
      if(action === 'save_as_new'){
        validate_as = 'add';
      }
    }
    this.form.action.value = action;
    return true;
  });
  $action_buttons.prop('disabled', false);
  $("#layoutForm").on('submit', function(e){
    var data = {};
    $.each($(this).serializeArray(), function(_, kv) {
      data[kv.name] = kv.value;
    });
    var action = this.action.value;
    if(!this.action.value){
      return false;
    }
    if(!form_ready){
      alert(js_lang.warning_form_not_ready);
      return false;
    }
    // form_ready = false;
    var $form_copy = $(this).clone();
    if(!editing || (editing && action == 'save_as_new')){
      data.slug = '';
    }
    $.ajax({
      url: "<?php echo site_url('backend/cms/layouts/save'); ?>",
      method: 'POST',
      dataType: 'json',
      data: data
    }).done(function(msg){
      form_change = false;
      form_ready = true;
      if(msg.status == 'success'){
        if(action == 'save_and_back'){
          window.top.location = '<?php echo site_url('backend/cms/layouts'); ?>';
          return;
        }
        if(action == 'save_and_new'){
          window.top.location = '<?php echo site_url('backend/cms/layouts/add'); ?>';
          return;
        }
        if((action == 'apply') || (action == 'save_as_new')){
          window.top.location = msg.data.edit_link;
          return;
        }
        window.top.location.reload();
      } else {
        alert(msg.message);
      }
    });
    return false;
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  