<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = $this->view_data; ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/attachments/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/attachments/subheader_text.php'); ?>
<div style='text-align: left;'>
  Test atasamente:
<?php
$attachments = isset($data['attachments']) ? $data['attachments'] : array();
if($attachments){ ?>
  <ol><?php
  foreach($attachments as $i=>$attachment){ /* ?>
    <li>Atasament #<?php echo $i+1; ?>: <a href="cid:<?php echo $this->_ci->email->attachment_cid($attachment['path']); ?>"><?php echo $attachment['name']; ?></a></li>
  <?php */
  ?>
    <li>Atasament #<?php echo $i+1; ?>: <span><?php echo $attachment['name']; ?></span></li>
  <?php
  } ?>
  </ol><?php
}
?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>