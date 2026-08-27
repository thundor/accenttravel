<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $page = Modules::$page; ?>
<?php if(strlen($page->content)){ ?>
<div id="cms_page_content">
  <div class="container">
    <div class="row">
<?php echo $page->content; ?>
    </div>
  </div>
</div>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>