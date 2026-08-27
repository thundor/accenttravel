<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<a id="toggle-btn" href="#" class="menu-btn" ><i class="icon-bars"></i></a>
<a href="<?php echo site_url('backend'); ?>" class="menu-btn" title="Catre backend" data-toggle="tooltip"><i class="icon-home"></i></a>
<a target="_BLANK" href="<?php echo site_url(''); ?>" class="menu-btn" title="Catre frontend" data-toggle="tooltip"><i class="icon-presentation"></i></a>
<?php themeFunctions::loadModule('backend/headbar',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>