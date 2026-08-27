<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
	.modal-backdrop{
		display: none !important;
	}
	#modal_fileman{
		position:relative;
	}
	.close.custom-close{
		display: none;
	}
	.modal-dialog.modal-lg{
		margin: 0 !important;
		max-height: 100% !important;
		max-width: 100% !important;
		width: 100%;
	}
	#fileman_iframe{
		width: 100% !important;
		height: calc(100vh - 240px) !important;
	}
</style>
<?php themeFunctions::debugFileLine('end'); ?>