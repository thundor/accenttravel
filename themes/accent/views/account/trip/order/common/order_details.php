<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<br />
<h3>STATUS REZERVARE</h3>
<?php 
if($order->status == 2){ ?>
Rezervarea este confirmata.<br/><?php 
} elseif($order->status == 3){ ?>
Rezervarea este anulata.<br/><?php 
} elseif($order->status == 1){ ?>
Rezervarea este inregistrata.<br/><?php 
} elseif($order->status == -1){ ?>
Rezervarea este eronata.<br/><?php 
} else { ?>
Rezervarea este creata.<br/><?php 
} ?>
<br />
<h3>DETALII REZERVARE</h3>
<ul>
  <li>Număr comanda: <b><?php echo $trip_order->Id; ?></b></li>
  <li>Nume client: <b><?php echo $owner->FirstName . ' ' . $owner->LastName; ?></b></li>
  <?php if(floatval($order->coupon_percentage) > 0) { ?>
  <li>Cupon aplicat<?php echo (strlen($order->coupon_code) ? (' (' . $order->coupon_code . ')') : ''); ?>: reducere de <b><?php echo $order->coupon_percentage; ?>%</b></li>
  <?php } ?>
  <li>Cost total: <b><?php echo format_price($order->amount, $order->currency); ?></b></li>
</ul>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>