<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/js/forms.js?v=1.0.13"></script>
<script type="text/javascript">
;(function($){
  function commonSubmitFormSubmitCallback($form,resp,$error_container){
    submitting_form = false;
    if(resp.status !== 'success'){
      return true;
    }
    if(!$form.hasClass('no-submit')){
      $form[0].submit();
    }
    return true;
  }
  var submitting_form = false;
  $(document).on('submit', 'form.form-validate', function(e){
    e.preventDefault();
    if(submitting_form){
      return false;
    }
    if(!this.name || !this.name.length){
      return false;
    }
    submitting_form = true;
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    if(!$('input[type=hidden][name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>"]', this).length){
      var csrf_hidden = '<input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />';
      $(this).append(csrf_hidden);
    }
    <?php } ?>
    var response_div_id = 'result_' + this.name;
    if(!$('#' + response_div_id).length){
      $response_div = $('<div class="form-group"/>').attr('id',response_div_id);
      $response_div.insertAfter(this);
    }
    basicFormPostSubmit(this,this.action,commonSubmitFormSubmitCallback,true);
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>