<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/admin/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/admin/subheader_text.php'); ?>
<div style='text-align: left;'>
  Primiti acest mail deoarece un utilizator a completat formularul de Feedback</a>
  <br />
  <br />
  Informatiile trimise de utilizator sunt urmatoarele: <br />
<ul>
<?php /*
  <li>Nume: <b><?php echo $lastname; ?></b></li>
  <li>Prenume: <b><?php echo $firstname; ?></b></li>
  <li>Telefon: <b><?php echo $phone; ?></b></li>
*/ ?>
  <li>Adresa de Email: <b><?php echo $email; ?></b></li>
</ul>
<br />
<ul>
  <li>ID inregistrare: <b><?php echo $id; ?></b></li>
  <li>Data inregistrare: <b><?php echo $date; ?></b></li>
  <li>ID utilizator: <b><?php echo $user_id > 0 ? $user_id : '-neautentificat-'; ?></b></li>
</ul>
<br />
<ul><?php /*
  <li>Tip feedback: <b><?php echo $category_text; ?></b></li>
  <li>Subiect: <b><?php echo $subject; ?></b></li>
  <li>Continut: </li>
*/ ?>
</ul>
<?php echo nl2br($body); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>