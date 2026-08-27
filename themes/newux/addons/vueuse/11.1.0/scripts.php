<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/shared/index.iife.min.js"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/core/index.iife.min.js"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/components/index.iife.min.js"></script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>