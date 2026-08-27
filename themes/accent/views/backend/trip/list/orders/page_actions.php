<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    if($this->_ci->user->can('backend-trip-orders-add')) { ?>
    <a href="<?php echo site_url('backend/trip/orders/add/trip'); ?>" class="btn btn-success">
      <?php echo lang('action_add/html'); ?> TRIP
    </a>
    <a href="<?php echo site_url('backend/trip/orders/add/paralela45'); ?>" class="btn btn-success">
      <?php echo lang('action_add/html'); ?> P45
    </a>
    <?php } ?>
    <?php if($this->_ci->user->canAnyUnder('backend-trip-roles-access')) { ?>
    <div class="dropdown btn-group">
      <button class="btn btn-success dropdown-toggle" type="button" id="permissions_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="permissions_button">
        <a href="<?php echo site_url('backend/trip/roles?path=backend-trip-orders'); ?>" class="dropdown-item">
          <?php echo lang('orders_permissions/html'); ?>
        </a>
      </div>
    </div>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>