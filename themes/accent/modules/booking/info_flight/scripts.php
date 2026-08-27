<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<script type="text/javascript">
jQuery("#veziText").on("click", function(e) {	
  jQuery("#veziText .fa-angle-down").toggleClass("fa-rotate-180");		
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>