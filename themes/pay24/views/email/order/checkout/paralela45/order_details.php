<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
Multumim pentru comanda efectuată pe <a href="<?php echo $site_url; ?>"><?php echo $site_url; ?></a><br /><?php 
if($order->status == 2){ ?>
Rezervarea cu ID <?php echo $order->id; ?> a fost confirmata.<br/>
Atasat, gasiti voucher-ul electronic pentru fiecare element din comanda.<?php 
} elseif($order->status == 3){ ?>
Rezervarea cu ID <?php echo $order->id; ?> a fost anulata.<br/><?php 
} else { ?>
Rezervarea cu ID <?php echo $order->id; ?> a fost inregistrata.<br/><?php 
  if(isset($service_hotel) || isset($service_package)){ ?>
Aceasta este realizata automat daca hotelul are camere disponibile sau va fi realizata si confirmata de  catre un agent care va va contacta in cel mai scurt timp prin telefon sau email daca hotelul are doar camere la cerere.<br /><?php 
  } ?>
Voucher-ul electronic va fi emis și transmis la adresa de email <?php echo $order->user_email; ?> în momentul confirmării plăţii.<br /><?php 
} ?>
<br />
<h3>DETALII REZERVARE</h3>
<ul>
  <?php if($order->trip_order_id) { ?>
  <li>Număr comanda: <b><?php echo $order->trip_order_id; ?></b></li>
  <?php } ?>
  <li>Nume client: <b><?php echo trim($order->user_firstname . ' ' . $order->user_lastname); ?></b></li>
  <?php if(floatval($order->coupon_percentage) > 0) { ?>
  <li>Cupon aplicat<?php echo (strlen($order->coupon_code) ? (' (' . $order->coupon_code . ')') : ''); ?>: reducere de <b><?php echo $order->coupon_percentage; ?>%</b></li>
  <?php } ?>
  <li>Cost total: <b><?php echo format_price($order->amount, $order->currency); ?></b></li>
</ul>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>