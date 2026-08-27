<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$page = Modules::$page;
?>
<title>24 Pay - <?php echo htmlspecialchars($page->title); ?><?php echo lang('append_title'); ?></title>
<meta name="description" content="24 Pay - <?php echo htmlspecialchars($page->description); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($page->keywords); ?>">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>