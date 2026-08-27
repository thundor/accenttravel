<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = $this->view_data; ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/activate/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/activate/subheader_text.php'); ?>
<?php 
$email = $data['to'];
 ?>
<div style='text-align: left;'>
  <p>Cupon activat cu informatiile:</p>
  <pre><?php print_r(json_encode(@$data['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></pre>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>