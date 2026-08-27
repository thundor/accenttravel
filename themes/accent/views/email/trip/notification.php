<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = $this->view_data; ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/notification/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/notification/subheader_text.php'); ?>
<?php 
$email = $data['to'];
$this->_ci->load->library('encryption');
$encrypted_email = $this->_ci->encryption->encrypt($email);
$get_query = http_build_query(array('v_c' => $encrypted_email));
$notifications_link = base_url('notifications?' . $get_query);
$delete_notifications_link = base_url('notifications/delete_all?' . $get_query);
 ?>
<div style='text-align: left;'>
  <p>Primiti acest email deoarece ati optat sa fiti notificat de modificarea pretului la una sau mai multe oferte alese de catre dumneavoastra.</p>
  <p>Va vom tine la curent in continuare pe masura ce pretul va scadea.</p>
  <ul><?php
  foreach($data['searches'] as $search){
    $reducere = ($search->amount - $search->amount_new) / $search->amount * 100;
    $reducere_formatted = ceil($reducere);
    $old_price = ceil($search->amount);
    $old_price_formatted = format_price($old_price,$search->currency);
    $price = ceil($search->amount_new);
    $price_formatted = format_price($price,$search->currency);
    $get_query = http_build_query(array('v_c' => $encrypted_email, 's_c' => $search->code));
    $delete_notification_link = base_url('notifications/delete?' . $get_query);
    ?>
    <li>
      <strong><?php echo $search->title; ?></strong>
      <small style="text-decoration:line-through;color:red;"><?php echo $old_price_formatted; ?></small>
      <span style="font-size:20px;"><strong><?php echo $price_formatted; ?></strong></span>
      <span>Reducere de <strong style="font-size:20px;"><?php echo $reducere_formatted; ?>%</strong></span>
      <br />
      <a href="<?php echo $delete_notification_link; ?>">[Elimina]</a>
    </li><?php
  } ?>
  </ul>
  <p>Pentru a vizualiza lista de alerte, dati click pe butonul urmator <strong><a href="<?php echo $notifications_link; ?>">[Vezi toate notificarile]</a></strong></p>
  <p>Daca doriti sa nu mai primiti notificari pentru oricare din ofertele alese, dati click pe butonul <strong>[Elimina]</strong> din dreptul ofertei respective.</p>
  <p>Daca doriti sa nu mai primiti notificari pentru niciuna din ofertele alese, dati click pe butonul urmator <strong><a href="<?php echo $delete_notification_link; ?>">[Elimina toate notificarile]</a></strong></p>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>