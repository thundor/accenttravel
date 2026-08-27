<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('backend/sidebar',__FILE__); ?>
<?php /*
<div class="trip-menu">
  <ul id="trip-flights-menu-list" class="side-menu list-unstyled"> 
    <li>
      <a href="#trip-flights-nav-list" data-toggle="collapse" aria-expanded="false">
        <i class="fa fa-plane"></i>
        <span>Bilete avion</span>
        <div class="arrow pull-right">
          <i class="fa fa-angle-down"></i>
        </div>
      </a>
      <ul id="trip-flights-nav-list" class="collapse list-unstyled">
        <li>
          <a href="<?php echo site_url('backend/trip/flights_insurance'); ?>">
            <i class="fa fa-cogs"></i>
            <span>Informatii asigurare</span>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</div>
*/
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>