<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item">
  <div class="btn-group">
    <?php if($this->_ci->user->can('backend-account-profile-save')) { ?>
    <button type="submit" form="profileForm" class="btn btn-success">
      <?php echo lang('action_save/html'); ?>
    </button>
    <?php } ?>
    <?php if($this->_ci->user->canAnyUnder('backend-accounts-roles-access') || $this->_ci->user->can('backend-account-profile-save')) { ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="permissions_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="permissions_button">
        <?php if($this->_ci->user->canAnyUnder('backend-accounts-roles-access')) { ?>
        <a href="<?php echo site_url('backend/accounts/roles?path=backend-account-profile'); ?>" class="dropdown-item">
          <?php echo lang('profile_permissions/html'); ?>
        </a>
        <?php } ?>
        <?php if($this->_ci->user->can('backend-account-profile-save')) { ?>
        <a href="<?php echo site_url('account/profile'); ?>" class="dropdown-item">
          <i class="icon-presentation"></i> Setari profil frontend
        </a>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>