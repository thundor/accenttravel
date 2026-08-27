<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
Vă multumim pentru comanda efectuată pe <b>aplicatia 24pay</b><br /><?php 
if($order->status == 2){ ?>
<br />
<table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="0DA0FF" style="border-radius:4px;overflow:hidden;">
  <tbody>
	<tr>
	  <td style="border-collapse: collapse;" height="60" valign="middle" bgcolor="0DA0FF">
		<p style="font-family: Arial, Tahoma, Verdana, sans-serif;font-size:16px;color:#ffffff;text-align:center;">Rezervarea cu ID <?php echo $order->id; ?> a fost inregistrata cu succes.</p>
	  </td>
	</tr>
  </tbody>
</table>
<?php if(isset($service_hotel) || isset($service_package)){ ?>
<br />Atasat, gasiti voucher-ul electronic pentru fiecare element din comanda.<?php 
}
if(isset($service_flight)){ 
?>
<br/>Pentru check in online acceseaza site-ul companiei aeriene pe stocul careia s-a emis biletul de avion, selecteaza opțiunea "Check-in online" și acceseaza rezervarea ta utilizând numărul de rezervare sau numărul biletului de avion și numele pasagerului. Este recomandat sa printezi cartile de imbarcare si sa te prezinti cu acestea in aeroport.
<br/>
<?php
	if(empty($has_ticket) && empty($has_invoice)){
?>
<br/><b>Biletul electronic si factura</b> vor fi emise și transmise la adresa de email <b><a href="mailto:<?php echo $owner->Email; ?>"><?php echo $owner->Email; ?></a></b> în momentul confirmării plăţii.
<?php
	} else { 
		if(!empty($has_ticket) && !empty($has_invoice)){
?>
<br/><b>Biletul electronic</b> si <b>factura electronica</b> corespunzatoare rezervarii tale sunt atasate acestui email.
<?php
		} elseif(!empty($has_ticket)){
?>
<br/><b>Biletul electronic</b> este atasat acestui email.
<?php
		} elseif(!empty($has_invoice)){
?>
<br/><b>Factura electronica</b> este atasata acestui email.
<?php
		}
	}
}
} elseif($order->status == 3){ ?>
Rezervarea cu ID <?php echo $order->id; ?> a fost anulata.<br/><?php 
} else { ?>
Rezervarea cu ID <?php echo $order->id; ?> a fost inregistrata.<br/><?php 
  if(isset($service_hotel) || isset($service_package)){ ?>
Aceasta este realizata automat daca hotelul are camere disponibile sau va fi realizata si confirmata de  catre un agent care va va contacta in cel mai scurt timp prin telefon sau email daca hotelul are doar camere la cerere.<br /><?php 
  } ?>
Voucher-ul electronic va fi emis și transmis la adresa de email <?php echo $owner->Email; ?> în momentul confirmării plăţii.<br /><?php 
} ?>
<br />
<br />

<table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="E6F6FF" style="border-radius:4px;overflow:hidden;">
  <tbody>
	<tr>
	  <td style="border-collapse: collapse;" height="60" valign="middle" bgcolor="E6F6FF">
		<br />
		<table class="table600" width="585" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="E6F6FF" style="border-radius:4px;overflow:hidden;padding-left:15px">
			<tbody>
				<tr>
				  <td style="border-collapse: collapse;" height="60" valign="middle" bgcolor="E6F6FF">
					<h3>DETALII REZERVARE</h3>
					<ul style="list-style:none;padding-left:10px;">
					  <li>Număr comanda: <b><?php echo $trip_order->Id; ?></b></li>
					  <li>Nume client: <b><?php echo $owner->FirstName . ' ' . $owner->LastName; ?></b></li>
					  <?php if(floatval($order->coupon_percentage) > 0) { ?>
					  <li>Cupon aplicat<?php echo (strlen($order->coupon_code) ? (' (' . $order->coupon_code . ')') : ''); ?>: reducere de <b><?php echo $order->coupon_percentage; ?>%</b></li>
					  <?php } ?>
					  <li>Cost total: <b><?php echo format_price($order->amount, $order->currency); ?></b></li>
					</ul>
				  </td>
				</tr>
			</tbody>
		</table>
	  </td>
	</tr>
  </tbody>
</table>

<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>