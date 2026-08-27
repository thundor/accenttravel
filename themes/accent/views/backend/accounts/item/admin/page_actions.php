<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$user = $data['user'];
$editing = $user->id != 0; 
$get_role = trim($this->_ci->input->get('role'));
$list_role = false;
if($get_role && $user->role == $get_role){
  $list_role = $get_role;
}
?>
<li class="nav-item">
  <div class="btn-group">
    <?php
      $can_save = false;
      $can_create = $this->_ci->user->canAnyUnder('backend-accounts-admin-access') || $this->_ci->user->canAnyUnder('backend-accounts-admin-own-access');
      if($editing){
        $can_access = $this->_ci->user->can('backend-accounts-admin-access-' . $user->role);
        $can_edit = $can_access && $this->_ci->user->can('backend-accounts-admin-edit-' . $user->role);
        if(!$can_edit){
          $can_access_own = $can_access || $this->_ci->user->can('backend-accounts-admin-own-access-' . $user->role);
          $can_edit_own = $can_access_own && $this->_ci->user->can('backend-accounts-admin-own-edit-' . $user->role);
          $can_edit = $user->created_by == $this->_ci->user->id && $can_edit_own;
        }
      } else {
        $can_edit = true;
      }
      if($editing){
        $can_save = $can_edit;
      } else {
        $can_save = $can_create;
      }
      $viewing = $this->_method =='view';
    ?>
    <?php if(!$viewing && $can_save) { ?>
    <button type="submit" disabled form="adminForm" value="save_and_back" class="btn btn-success">
      <?php echo lang('action_save_and_back_to_list/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <button disabled type="submit" form="adminForm" value="apply" class="dropdown-item">
          <?php echo lang('action_apply/html'); ?>
        </button>
        <?php } ?>
        <?php if($can_create) { ?>
        <button disabled type="submit" form="adminForm" value="save_and_new" class="dropdown-item">
          <?php echo lang('action_save_and_new/html'); ?>
        </button>
        <?php } ?>
        <?php if($editing && $can_create) { ?>
        <button disabled type="submit" form="adminForm" value="save_as_new" class="dropdown-item">
          <?php echo lang('action_save_as_new/html'); ?>
        </button>
        <?php } ?>
        <a href="<?php echo site_url('backend/accounts/admin'. ($list_role?'?role='.$list_role:'')); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } elseif(!$viewing && $can_create){ ?>
    <button disabled type="submit" form="adminForm" value="save_as_new" class="btn btn-success">
      <?php echo lang('action_save_as_new/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="<?php echo site_url('backend/accounts/admin'. ($list_role?'?role='.$list_role:'')); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } else { ?>
    <a href="<?php echo site_url('backend/accounts/admin'. ($list_role?'?role='.$list_role:'')); ?>" class="btn btn-secondary">
      <?php echo lang('action_back_to_list/html'); ?>
    </a>
    <?php if($can_create || $can_edit){ ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <a href="<?php echo site_url('backend/accounts/admin/edit?id=' . $data['user']->id); ?>" class="dropdown-item">
          <?php echo lang('action_edit/html'); ?>
        </a>
        <?php } ?>
        <?php if($can_create){ ?>
        <a href="<?php echo site_url('backend/accounts/admin/add'); ?>" class="dropdown-item">
          <?php echo lang('action_add/html'); ?>
        </a>
        <?php } ?>
      </div>
    </div>
    <?php }
    }
  ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>