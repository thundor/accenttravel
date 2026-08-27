<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
(function($){
  $.fn.makeEditor = $.fn.makeCKEditor;
  $('.make-htmleditor').removeClass('make-htmleditor').makeEditor();
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>