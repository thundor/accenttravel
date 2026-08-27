<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<img src="<?php echo $this->theme_url; ?>assets/images/cards.png?v=1.0.1" alt="Vacante cu plata online" class="<?php echo isset($class) ? $class : ''; ?>" />
<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO" target="_blank"><img src="<?php echo $this->theme_url; ?>assets/images/sal.png" alt="Vacante cu plata online" style="max-width:250px; display:block;" class="<?php echo isset($class) ? $class : ''; ?>" /></a>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>