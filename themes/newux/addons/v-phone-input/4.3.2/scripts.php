<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">window.components = Vuetify.components</script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/<?php echo basename(dirname(__DIR__))?>.umd.cjs"></script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>