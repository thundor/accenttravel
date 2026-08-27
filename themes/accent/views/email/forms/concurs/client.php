<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/client/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/client/subheader_text.php'); ?>
<div style='text-align: left;'>
  Primiti acest mail deoarece ati completat formularul de <a href="<?php echo site_url('inregistrare-concurs'); ?>">Inregistrare la concurs</a>
  <br />
  <br />
  Informatiile trimise de catre dumneavoastra sunt urmatoarele: <br />
<ul>
  <li>Nume: <b><?php echo $numeReg; ?></b></li>
  <li>Prenume: <b><?php echo $pnumeReg; ?></b></li>
  <li>Adresa de Email: <b><?php echo $emailReg; ?></b></li>
  <li>Oras Domiciliu: <b><?php echo $domiciliuReg; ?></b></li>
  <li>Telefon: <b><?php echo $telReg; ?></b></li>
</ul>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>