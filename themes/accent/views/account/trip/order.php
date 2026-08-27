<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('frontend/account_profile'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/order/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/order/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/order/meta.php'); ?>
<?php $this->view_data['site_url'] = base_url(''); ?>
<div class="container">
<?php include 'order/breadcrumbs.php'; ?>
<?php
$can_download_vouchers = false; // TODO
require_once(__DIR__ . '/order/' . $this->view_data['service_type'] . '.php');
?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>