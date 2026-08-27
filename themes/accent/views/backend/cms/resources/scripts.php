<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script type="text/javascript">
(function($){
	window.filemanUpdate = function(file){
		$('#resource_file').attr('href', window.location.origin + file.fullPath).text(window.location.origin + file.fullPath);
	};
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>