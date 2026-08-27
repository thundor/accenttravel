<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$user = $data['user'];
$editing = $user->id != 0 ? 1 : 0;
$can_save = false;
$can_access_create = $this->_ci->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access');
$can_create = $can_access_create && $this->_ci->user->can('backend-accounts-customer-add');

$can_access = $this->_ci->user->can('backend-accounts-customer-access');
$can_edit = $can_access && $this->_ci->user->can('backend-accounts-customer-access') && $this->_ci->user->can('backend-accounts-customer-edit');
$can_access_own = $can_access || $this->_ci->user->can('backend-accounts-customer-own-access');
$can_edit_own = $can_access_own && $this->_ci->user->can('backend-accounts-customer-own-edit');
if($editing){
  $can_edit_own = $user->created_by == $this->_ci->user->id && $can_edit_own;
}
if(!$can_edit){
  $can_edit = $can_edit_own;
}
$can_save = (($editing && $can_edit) || (!$editing && $can_create));
$viewing = $this->_method =='view';
?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    ?>
    <?php if(!$viewing && $can_save) { ?>
    <button type="submit" disabled form="customerForm" value="save_and_back" class="btn btn-success">
      <?php echo lang('action_save_and_back_to_list/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <button disabled type="submit" form="customerForm" value="apply" class="dropdown-item">
          <?php echo lang('action_apply/html'); ?>
        </button>
        <?php } ?>
        <?php if($can_create) { ?>
        <button disabled type="submit" form="customerForm" value="save_and_new" class="dropdown-item">
          <?php echo lang('action_save_and_new/html'); ?>
        </button>
        <?php } ?>
        <?php if($editing && $can_create) { ?>
        <button disabled type="submit" form="customerForm" value="save_as_new" class="dropdown-item">
          <?php echo lang('action_save_as_new/html'); ?>
        </button>
        <?php } ?>
        <a href="<?php echo site_url('backend/accounts/customer'); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } elseif(!$viewing && $can_create){ ?>
    <button disabled type="submit" form="customerForm" value="save_as_new" class="btn btn-success">
      <?php echo lang('action_save_as_new/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="<?php echo site_url('backend/accounts/customer'); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } else { ?>
    <a href="<?php echo site_url('backend/accounts/customer'); ?>" class="btn btn-secondary">
      <?php echo lang('action_back_to_list/html'); ?>
    </a>
    <?php if($can_create || $can_edit){ ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <a href="<?php echo site_url('backend/accounts/customer/edit?id=' . $data['user']->id); ?>" class="dropdown-item">
          <?php echo lang('action_edit/html'); ?>
        </a>
        <?php } ?>
        <?php if($can_create){ ?>
        <a href="<?php echo site_url('backend/accounts/customer/add'); ?>" class="dropdown-item">
          <?php echo lang('action_add/html'); ?>
        </a>
        <?php } ?>
      </div>
    </div>
    <?php }
    } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>