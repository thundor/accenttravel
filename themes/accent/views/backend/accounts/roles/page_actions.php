<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Permission_model');
$roles = $this->_ci->Permission_model->roles;
$path = trim($this->_ci->input->get('path'));
$get_role = trim($this->_ci->input->get('role'));
$accessible_roles = array();
foreach($roles as $role){
  $can_access = $this->_ci->user->can('backend-accounts-roles-access-' . $role);
  if($can_access){
    $accessible_roles[] = $role;
  }
}
$total_accessible_roles = count($accessible_roles);
?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    if($this->_ci->user->can('backend-accounts-roles-save')) { ?>
    <button type="submit" form="rolesForm" class="btn btn-success">
      <i class="fa fa-save"></i> <?php echo lang('action_save'); ?>
    </button>
    <?php } ?>
    <?php if($this->_ci->user->canAnyUnder('backend-accounts-roles-access') && ($total_accessible_roles>1 || $path)) { ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="permissions_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="permissions_button">
        <?php 
      if($total_accessible_roles > 1){
        foreach($accessible_roles as $accessible_role){
          $params = array();
          if($path){
            $params['path'] = $path;
          }
          $params['role'] = $accessible_role;
        ?>
        <a href="<?php echo site_url('backend/accounts/roles?' . http_build_query($params)); ?>" class="dropdown-item">
          <?php echo lang('menu_accounts_' . $accessible_role . '/html'); ?>
        </a>
        <?php 
        }
        if($get_role){ 
          $params = array();
          if($path){
            $params['path'] = $path;
          }
        ?>
          <a href="<?php echo site_url('backend/accounts/roles?' . http_build_query($params)); ?>" class="dropdown-item">
            <?php echo lang('all_roles_permissions/html'); ?>
          </a>
        <?php 
        }
      } 
      if($path){ 
        $params = array();
        if($get_role){
          $params['role'] = $get_role;
        }
      ?>
        <a href="<?php echo site_url('backend/accounts/roles?' . http_build_query($params)); ?>" class="dropdown-item">
          <?php echo lang('global_permissions/html'); ?>
        </a>
      <?php 
      }
      ?>
      </div>
    </div>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>