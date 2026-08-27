<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/subscribe/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/subscribe/subheader_text.php'); ?>
<?php 
$this->_ci->load->library('encryption');
$encrypted_email = $this->_ci->encryption->encrypt($to);
$unsubscribe_link = base_url('forms/newsletter/unsubscribe/' . $encrypted_email);
; ?>
<div style='text-align: left;'>
  <p>Va multumim pentru abonarea la newsletterul nostru. Astfel, acum vei fi informat cu privire la vacantele si ofertele noastre turistice, la stiri si noutati din lumea calatoriilor in cele mai frumoase si dorite destinatii.</p>
  <p>Daca nu ati solicitat abonarea la newsletter-ul Accent Travel va rugam sa acceptati scuzele noastre si sa va dezabonati prin click pe linkul urmator: <a href="<?php echo $unsubscribe_link; ?>">Dezabonare newsletter</a></p>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>