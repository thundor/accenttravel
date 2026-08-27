<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = $this->view_data; ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/requestoffer/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/requestoffer/subheader_text.php'); ?>
<?php 
$maildata = $data['maildata'];
?>
<div style='text-align: left;'>
  <p>Primiti acest email deoarece ati solicitat o oferta.</p>
  <p><strong>Nume client:</strong> <?php echo $maildata['fullname']; ?></p>
  <p><strong>Email client:</strong> <?php echo $maildata['email']; ?></p>
  <p><strong>Telefon client:</strong> <?php echo $maildata['phone']; ?></p>
  <br />
  <p><strong>Titlu oferta:</strong> <?php echo $maildata['title']; ?></p>
  <?php /* <p><strong>Link oferta:</strong> <a target="_BLANK" href="<?php echo base_url('requestoffer/view?' . http_build_query(array('s_c' => $maildata['code']))); ?>"><?php echo base_url('requestoffer/view?' . http_build_query(array('s_c' => $maildata['code']))); ?></a></p> */ ?>
  <br />
  <p><strong>Mesaj trimis de client:</strong></p>
  <?php echo nl2br($maildata['message']); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>