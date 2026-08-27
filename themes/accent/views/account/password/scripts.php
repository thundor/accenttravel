<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
// $can_write = $this->_ci->user->canAny('frontend-account-profile-save','backend-account-profile-save');
$can_write = 1;
?>
<script>
(function($){
function submitCallback($form,resp,$error_container){
    if(resp.status !== 'success'){
      return true;
    }
    $error_container.empty();
    var form = $form[0];
    var message = resp.message;
    if(form.name == 'profilePasswordForm'){
      message = 'Parola a fost actualizata.';
      form.reset();
	  window.location.href = '<?php echo site_url(''); ?>';
    }
    showMessage($error_container,message,'success');
    return false;
  }
$('form.profile_form').on('submit',function(e){e.preventDefault();$('#csrfToken').attr('form',this.name);basicFormPostSubmit(this,"<?php echo site_url('account/password?hash=' . $data['hash']); ?>",submitCallback);});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>