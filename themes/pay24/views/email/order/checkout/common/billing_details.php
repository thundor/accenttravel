<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
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
					
<h3>DATE DE FACTURARE</h3>
<ul style="list-style:none;padding-left:10px;">
  <li>Persoană de contact: <b><?php echo ucfirst($owner->Title) . ' ' . $owner->FirstName . ' ' . $owner->LastName; ?></b></li>
  <li>Nr telefon: <b><a href="tel:<?php echo preg_replace('/^(\+[0-9]+ )\1+/', '\1', $owner->Phone); ?>"><?php echo preg_replace('/^(\+[0-9]+ )\1+/', '\1', $owner->Phone); ?></a></b></li>
  <li>Adresa e-mail: <b><a href="mailto:<?php echo $owner->Email; ?>"><?php echo $owner->Email; ?></a></b></li>
  <li>Adresă facturare: <b><?php echo $owner->Address->CountryISO . ', ' . $owner->Address->CityName . ' ' . $owner->Address->Province . ', ' . $owner->Address->Street . ' ' . $owner->Address->HouseNr . ', ' . $owner->Address->ZipCode ; ?></b></li>
</ul>
				  </td>
				</tr>
			</tbody>
		</table>
	  </td>
	</tr>
  </tbody>
</table>
<br />
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>