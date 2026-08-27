<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<br />
<h3>DATE DE FACTURARE</h3>
<ul>
  <li>Persoană de contact: <b><?php echo trim($order->user_firstname . ' ' . $order->user_lastname); ?></b></li>
  <?php
  $phone_number = $order->user_phone;
  /* if($order->user_phone_prefix){
    $this->_ci->load->model('Country_model');
    $phone_country = $this->_ci->Country_model->getCountryByIso2($order->user_phone_prefix);
    if($phone_country && strlen($phone_country->phone_prefix)){
      $phone_number = '+' . $phone_country->phone_prefix . ' ' . $phone_number;
    }
  } */
  ?>
  <li>Nr telefon: <b><a href="tel:<?php echo $phone_number; ?>"><?php echo $phone_number; ?></a></b></li>
  <li>Adresa e-mail: <b><a href="mailto:<?php echo $order->user_email; ?>"><?php echo $order->user_email; ?></a></b></li>
  <li>Adresă facturare: <b><?php 
  $address = array(trim($order->user_address), trim($order->user_street . ' ' . $order->user_street_no), trim($order->user_city), trim($order->user_country));
  $address = array_diff($address,array(''));
  echo implode(', ', $address) ; ?></b></li>
</ul>
<br />
<p>Pentru intrebări suplimentare legate de rezervare, va rugăm contactaţi serviciul de call center Accent Travel&amp;Events  prin telefon la <a href="tel:+40372999006">0372 999 006</a> sau prin e-mail la <a href="mailto:vanzari@accenttravel.ro">vanzari@accenttravel.ro</a>.</p>
<p>Produsele/serviciile achiziţionate vor fi livrate în concordanţă cu termenii şi condiţiile publicate pe site-ul <a href="<?php echo $site_url; ?>"><?php echo $site_url; ?></a> şi agreaţi în momentul plăţii.</p>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>