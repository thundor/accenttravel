<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="trip-menu">
  <ul id="trip-flights-menu-list" class="side-menu list-unstyled"> 
    <li>
      <a href="#trip-flights-nav-list" data-toggle="collapse" aria-expanded="false">
        <div class="arrow pull-right">
          <i class="fa fa-angle-down"></i>
        </div>
        <i class="fa fa-plane"></i>
        <span>Bilete avion</span>
      </a>
      <ul id="trip-flights-nav-list" class="collapse list-unstyled">
        <li class="<?php echo $this->_ci->router->class === 'Flights_settings' ? ' current' : ''; ?>">
          <a href="<?php echo site_url('backend/trip/flights_settings'); ?>">
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