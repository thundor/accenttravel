<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$get_role = trim($this->_ci->input->get('role'));
?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    $role = $data['role'];
    if(!$role){
      $can_add = $this->_ci->user->canAnyUnder('backend-accounts-admin-add');
    } else {
      $can_add = $this->_ci->user->can('backend-accounts-admin-add-' . $role);
    }
    if($can_add) { ?>
    <a href="<?php echo site_url('backend/accounts/admin/add' . ($role ? '?role=' . $role : '')); ?>" class="btn btn-success">
      <?php echo lang('action_add/html'); ?>
    </a>
    <?php } elseif($get_role){ ?>
    <a href="<?php echo site_url('backend/accounts/admin'); ?>" class="btn btn-secondary">
      <?php echo lang('menu_all_accounts/html'); ?>
    </a>
    <?php } ?>
    <?php if(($can_add && $get_role) || $this->_ci->user->canAnyUnder('backend-accounts-roles-access')) { ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="permissions_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="permissions_button">
        <?php if($can_add && $get_role){ ?>
        <a href="<?php echo site_url('backend/accounts/admin'); ?>" class="dropdown-item">
          <?php echo lang('menu_all_accounts/html'); ?>
        </a>
        <?php } ?>
        <?php if($this->_ci->user->canAnyUnder('backend-accounts-roles-access')){ ?>
        <a href="<?php echo site_url('backend/accounts/roles?path=backend-accounts-admin'); ?>" class="dropdown-item">
          <?php echo lang('accounts_permissions/html'); ?>
        </a>
        <?php } ?>
        <?php if($get_role && $this->_ci->user->can('backend-accounts-roles-access-' . $get_role)){ ?>
        <a href="<?php echo site_url('backend/accounts/roles?path=backend-accounts-admin&role=' . $get_role); ?>" class="dropdown-item">
          <?php echo lang('accounts_role_permissions/html'); ?>
        </a>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>