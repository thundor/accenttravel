<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item dropdown<?php echo $this->_module=='Trip' && $this->_controller=='Packages' ? ' active' : ''; ?>">
  <a class="nav-link" href="<?php echo site_url('trip/packages'); ?>">Vacante</a>
  <?php /*
  <a class="nav-link dropdown-toggle" href="#" id="pacheteMN" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Vacante</a>
  <div class="dropdown-menu" aria-labelledby="cityBreakMN">
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Litoral Romania <span>(rezervare online)</span></a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Balneo/SPA Romania <span>(rezervare online)</span></a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Cipru</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Croatia</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Emiratele Arabe Unite</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Grecia</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Muntenegru</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Portugalia</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Spania</a>
    <a class="dropdown-item" href="<?php echo site_url('trip/packages'); ?>">Vacante Turcia</a>
  </div>
  */ ?>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>