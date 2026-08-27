<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$can_write = $this->_method !='view';
if($can_write){
themeFunctions::jsLang('warning_form_not_ready');
$user = $this->view_data['user'];
$get_role = trim($this->_ci->input->get('role'));
$list_role = false;
if($get_role && $user->role == $get_role){
  $list_role = $get_role;
}
$editing = $user->id != 0 ? 1 : 0;
$this->_ci->load->model('Permission_model');
$roles = $this->_ci->Permission_model->roles;
$editable_roles = array();
foreach($roles as $role){
  $can_add = $this->_ci->user->can('backend-accounts-admin-add-' . $role);
  if(!($role == $user->role || $can_add)){
    continue;
  }
  $can_access = $this->_ci->user->can('backend-accounts-admin-access-' . $role);
  $can_edit = $can_access && $this->_ci->user->can('backend-accounts-admin-edit-' . $role);
  if(!$can_edit){
    $can_access_own = $can_access || $this->_ci->user->can('backend-accounts-admin-own-access-' . $role);
    $can_edit_own = $can_access_own && $this->_ci->user->can('backend-accounts-admin-own-edit-' . $role);
    if(!$editing || ($editing && $user->created_by == $this->_ci->user->id)){
      $can_edit = $can_edit_own;
    }
  }
  if($can_edit){
    $editable_roles[] = $role;
  }
}
?>
<script>
(function($){
  var editable_roles = <?php echo json_encode($editable_roles); ?>;
  var $action_buttons = $('button[type=submit][form=adminForm]');
  function treatRole(){
    var current_role = $('#role').val();
    console.log(current_role);
    var is_editable = editable_roles.indexOf(current_role) > -1;
    if(is_editable){
      $('button[form=adminForm][value=apply]').show();
    } else {
      $('button[form=adminForm][value=apply]').hide();
    }
  }
  $('#role').on('change',function(){treatRole();});
  treatRole()
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
      window.top.location = '<?php echo site_url('backend/accounts/admin'. ($list_role?'?role='.$list_role:'')); ?>';
      return;
    }
    if(action == 'save_and_new'){
      window.top.location = '<?php echo site_url('backend/accounts/admin/add'. ($list_role?'?role='.$list_role:'')); ?>';
      return;
    }
    if((!editing && action == 'apply') || (action == 'save_as_new')){
      window.top.location = resp.data.edit_link;
      return;
    }
    window.top.location.reload();
    return false;
  }
  $('#adminForm').on('submit',function(e){
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
    basicFormPostSubmit(this,"<?php echo site_url('backend/accounts/admin/save'); ?>",submitCallback,true);
    this.id.value = id;
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  