<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
  var $emailTestResult = $('#result_emailTest');

  function syncEmailDriverPanels(){
    var driver = $('input[name=email_driver]:checked').val() || 'o365';
    $('.js-panel-o365').toggle(driver === 'o365');
    $('.js-panel-smtp').toggle(driver === 'smtp');
  }

  $(document).on('change', 'input.js-email-driver', syncEmailDriverPanels);
  syncEmailDriverPanels();

  $('form.email_settings_form').on('submit', function(e){
    e.preventDefault();
    basicFormPostSubmit(this, this.action, null, true);
  });

  $('form.email_test_form').on('submit', function(e){
    e.preventDefault();
    basicFormPostSubmit(this, this.action, null, true, $emailTestResult);
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
