<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<br />
<h3>DATE DE FACTURARE</h3>
<ul>
  <li>Persoană de contact: <b><?php echo ucfirst($owner->Title) . ' ' . $owner->FirstName . ' ' . $owner->LastName; ?></b></li>
  <li>Nr telefon: <b><a href="tel:<?php echo $owner->Phone; ?>"><?php echo $owner->Phone; ?></a></b></li>
  <li>Adresa e-mail: <b><a href="mailto:<?php echo $owner->Email; ?>"><?php echo $owner->Email; ?></a></b></li>
  <li>Adresă facturare: <b><?php echo $owner->Address->CountryISO . ', ' . $owner->Address->CityName . ' ' . $owner->Address->Province . ', ' . $owner->Address->Street . ' ' . $owner->Address->HouseNr . ', ' . $owner->Address->ZipCode ; ?></b></li>
</ul>
<br />
<p>Pentru intrebări suplimentare legate de rezervare, va rugăm contactaţi Accent Travel&amp;Events prin telefon la <a href="tel:+40213141980">021 314 19 80</a> sau prin e-mail la <a href="mailto:vanzari@accenttravel.ro">vanzari@accenttravel.ro</a>.</p>
<p>Produsele/serviciile achiziţionate vor fi livrate în concordanţă cu termenii şi condiţiile publicate pe site-ul <a href="<?php echo $site_url; ?>"><?php echo $site_url; ?></a> şi agreaţi în momentul plăţii.</p>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>