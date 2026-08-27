<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/edit/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/edit/subheader_text.php'); ?>
<div style='text-align: left;'>
  A fost editat tichetul cu ID <?php echo $ticket_id; ?> de <?php echo $updater_name; ?><br>
  <?php include 'common/content.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>