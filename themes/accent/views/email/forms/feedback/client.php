<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/client/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/client/subheader_text.php'); ?>
<div style='text-align: left;'>
  Primiti acest mail deoarece ati completat formularul de Feedback
  <br />
  <br />
  Informatiile trimise de catre dumneavoastra sunt urmatoarele: <br />
<ul>
<?php /*
  <li>Nume: <b><?php echo $lastname; ?></b></li>
  <li>Prenume: <b><?php echo $firstname; ?></b></li>
  <li>Telefon: <b><?php echo $phone; ?></b></li>
  <li>Tip feedback: <b><?php echo $category_text; ?></b></li>
  <li>Subiect: <b><?php echo $subject; ?></b></li>
  <li>Continut: </li>
*/ ?>
  <li>Adresa de Email: <b><?php echo $email; ?></b></li>
</ul>
<?php echo nl2br($body); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>