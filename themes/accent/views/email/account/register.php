<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/register/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/register/subheader_text.php'); ?>
<div style='text-align: left;'>
  Primiti acest mail deoarece ati solicitat activarea unui cont in interfata website-ului <a href="<?php echo $site_url;?>"><?php echo $site_url;?></a>
  <br />
  <br />
  Contul dumneavoastra fost activat, coordonatele de logare sunt urmatoarele: <br />
  ACCENT TRAVEL & EVENTS <br />
<ul>
  <li>Utilizator: <b><?php echo $username; ?></b></li>
  <li>Parola: <b><?php echo $password; ?></b></li>
</ul>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>