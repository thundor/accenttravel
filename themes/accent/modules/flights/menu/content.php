<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item <?php echo $this->_module=='Trip' && $this->_controller=='Flights' ? 'active' : ''; ?>">
  <a class="nav-link" href="<?php echo site_url('trip/flights'); ?>">Bilete avion</a>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>