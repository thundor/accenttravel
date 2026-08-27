<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
    console.log('form_data', $('#onlineForm').serializeArray());
	var processor_data = <?php echo json_encode($this->view_data['processor_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
	if('function' == typeof parent['processorCallback']){
		parent['processorCallback'](processor_data);
	}
	console.warn('processor_data', processor_data);
  // setTimeout(function(){
  // },500);
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>