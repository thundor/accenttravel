<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('logo/image',__FILE__); ?>
<a href="<?php echo base_url(); ?>" class="mod-logo-link">
  <?php themeFunctions::loadAddons(__FILE__); ?>
</a>
<?php themeFunctions::debugFileLine('end'); ?>