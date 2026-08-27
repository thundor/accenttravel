<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    if($this->_ci->user->can('backend-config-save')) { ?>
    <a href="<?php echo site_url('backend/trip/coupons/add'); ?>" class="btn btn-success">
      <?php echo lang('action_add/html'); ?>
    </a>
    <?php } ?>
    <?php if($this->_ci->user->canAnyUnder('backend-accounts-roles-access')) { ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="permissions_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="permissions_button">
        <a href="<?php echo site_url('backend/accounts/roles?path=backend-config'); ?>" class="dropdown-item">
          <?php echo lang('coupons_permissions/html'); ?>
        </a>
      </div>
    </div>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>