<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<a href="#" class="navbar-brand">
  <div class="brand-text d-none d-md-inline-block">
    <?php echo lang('page_title/html'); ?>
  </div>
</a>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>