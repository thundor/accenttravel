<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<br>
Acesta poate fi accesat aici - <a href="<?php echo $ticket_url; ?>"><?php echo $ticket_url; ?></a><br>
<br>
<?php if(isset($assigned_name)){ ?>
Acesta este asignat lui: <?php echo $assigned_name; ?><br>
<?php } else { ?>
Acesta nu este asociat la niciun consilier.<br>
<?php } ?>
<?php if(isset($reservation_id)){ ?>
Acesta are asociata rezervarea: <?php echo $reservation_id; ?><br>
<?php } else { ?>
Acesta nu are rezervare asociata.<br>
<?php } ?>
<?php if(isset($status)){ ?>
Status tichet: <?php 
if($status == 1){
  echo 'Nou';
} elseif($status == 2){
  echo 'In lucru';
} elseif($status == 3){
  echo 'Finalizat';
}
 ?><br>
<?php } ?>

<?php if(isset($message)){ ?>
Mesaj tichet: <?php echo $message; ?><br>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>