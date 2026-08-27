<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('warning_form_not_ready');
?>
<script>
(function($){
  function submitCallback($form,resp,$error_container){
    form_ready = true;
    return true;
  }
  var form_ready = true;
  $('#profileForm').on('submit',function(e){
    e.preventDefault();
    if(!form_ready){
      alert(js_lang.warning_form_not_ready);
      return false;
    }
    form_ready = false;
    basicFormPostSubmit(this,"<?php echo site_url('backend/account/save'); ?>",submitCallback,true);
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>