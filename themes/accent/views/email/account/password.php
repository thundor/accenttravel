<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/password/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/password/subheader_text.php'); ?>
<div style='text-align: left;'>
  Primiti acest mail deoarece ati solicitat recuperarea parolei pe interfata website-ului <a href="<?php echo $site_url; ?>"><?php echo $site_url; ?></a><br />
  In cazul in care doriti acest lucru, va rugam sa urmati pasii de activare:<br />
  <ul>
    <li>Accesati linkul: <a href="<?php echo $reset_url;?>"><?php echo $reset_url; ?></a></li>
    <li>Completati formularul cu parola noua.</li>
  </ul>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>