<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script type="text/javascript">
;(function($){
$('form.sitemap_settings').on('change input','input', function(){
  form_change = true;
});
function sitemapSettingsFormSubmitCallback($form,resp,$error_container){
  if(resp.status !== 'success'){
    return true;
  }
  form_change = false;
  return true;
}
$('form.sitemap_settings').on('submit',function(e){
  e.preventDefault();basicFormPostSubmit(this,this.action,sitemapSettingsFormSubmitCallback,true);
});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>