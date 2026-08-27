<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="trip-menu">
  <ul id="trip-citybreak-menu-list" class="side-menu list-unstyled"> 
    <li>
      <a href="#trip-citybreak-nav-list" data-toggle="collapse" aria-expanded="false">
        <div class="arrow pull-right">
          <i class="fa fa-angle-down"></i>
        </div>
        <i class="fa fa-map-marker"></i>
        <span>City Break</span>
      </a>
      <ul id="trip-citybreak-nav-list" class="collapse list-unstyled">
        <li class="<?php echo $this->_ci->router->class == 'Citybreak_settings' ? ' current' : ''; ?>">
          <a href="<?php echo site_url('backend/trip/citybreak_settings'); ?>">
            <i class="fa fa-cogs"></i>
            <span>Setari</span>
          </a>
        </li>
      </ul>
    </li>
  </ul>
  <?php themeFunctions::loadAddons(__FILE__); ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>