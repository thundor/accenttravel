<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.loading-state{
	opacity:0.5;
	cursor:wait;
}
.loading-state *{
	pointer-events:none !important;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>