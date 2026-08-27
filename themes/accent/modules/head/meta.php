<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Lisal Expert">
<link rel="shortcut icon" href="<?php echo $this->theme_url . 'assets/images/favicon.png';?>">
<?php themeFunctions::loadAddons(__FILE__, array('one' => true)); ?>
<?php themeFunctions::loadAddons(__FILE__ . 'end'); ?>
<?php themeFunctions::debugFileLine('end'); ?>