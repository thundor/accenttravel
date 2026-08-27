<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<img src="<?php echo $this->theme_url; ?>assets/images/certificates.png" alt="Certificari IATA" class="<?php echo isset($class) ? $class : ''; ?>"/>
<a href="https://anpc.ro/ce-este-sal/" target="_blank"><img src="<?php echo $this->theme_url; ?>assets/images/anpc.png" alt="ANPC" style="max-width:250px; display:block;" class="<?php echo isset($class) ? $class : ''; ?>"/></a>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>