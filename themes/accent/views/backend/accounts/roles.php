<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('accounts_roles'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/roles/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/roles/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/roles/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/roles/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/roles/stylesheets.php'); ?>
<?php 
$showing_all_roles = $this->view_data['path'];
$path = $this->view_data['path'];
$roles_permissions = $this->view_data['roles_permissions'];
$roles = $this->view_data['roles'];
global $role, $can_save;
$role = $this->view_data['role'];
$all_permissions = $this->view_data['all_permissions'];
$can_save = $this->_ci->user->can('backend-accounts-roles-save');
?>
<section class="forms">
  <form id="rolesForm" name="rolesForm" action="<?php echo site_url('backend/accounts/roles/save'); ?>" method="POST">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <input type="hidden" name="path" value="<?php echo htmlspecialchars($path); ?>" />
    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>" />
    <div class="col-12">
      <div class="row ml-0 mr-1">
        <?php foreach($roles as $subrole) { 
        ?>
        <div class="col-lg-4 pl-1 pr-0 pb-1" id="role_<?php echo $subrole; ?>">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('role/' . $subrole . '/html'); ?></h2>
            </div>
            <div class="card-block">
              <input type="hidden" name="permission[<?php echo $subrole?>]" />
              <div class="list-group root-item">
              <?php 
              $assigned_role_permissions = isset($roles_permissions[$subrole]) ? $roles_permissions[$subrole] : array();
              if(!function_exists('displayPermissions')){
              function displayPermissions($currole,$all_permissions, $assigned_permissions,$nesting='', $parents = array()){
                global $role, $can_save;
                $user = CI::$APP->user;
                foreach($all_permissions as $k=>$v){
                  if(is_array($v)){
                    $current_nesting = $nesting . $k;
                      if(!$user->canUnder($current_nesting,'any',3)){
                      continue;
                    }
                    $title = lang('permissions/' . $current_nesting);
                    $parents[] = $title;
                    $params = array();
                    $params['path'] = $current_nesting;
                    if($role){
                      $params['role'] = $role;
                    }
                    $section_link = site_url('backend/accounts/roles?' . http_build_query($params));
                    ?>
                    <div class="list-group-item rolegroup-<?php echo $current_nesting; ?>">
                      <div class="display"><a href="<?php echo $section_link; ?>" title="<?php echo lang('zone_permissions'); ?>"><i class="fa fa-angle-double-right"></i></a> <?php echo lang('permissions/' . $current_nesting . '/html'); ?></div>
                      <div class="list-group">
                      <?php
                      displayPermissions($currole,$all_permissions[$k], $assigned_permissions, $current_nesting . '-', $parents);
                      ?>
                      </div>
                    </div>
                    <?php
                  } else {
                    $permission = $nesting . $v;
                    if(!$user->can($permission)){
                      continue;
                    }
                    if($permission)
                    ?>
                    <div class="list-group-item role-<?php echo $currole; ?> pb-0">
                      <div class="i-checks">
                        <input id="permission_<?php echo $currole; ?>_<?php echo $permission; ?>" <?php if($can_save){ ?>name="permission[<?php echo $currole; ?>][]"<?php } else { ?> disabled <?php } ?> type="checkbox" <?php echo in_array($permission, $assigned_permissions) ? 'checked' : ''; ?> value="<?php echo $permission; ?>" class="form-control-custom">
                        <label for="permission_<?php echo $currole; ?>_<?php echo $permission; ?>"><small><?php echo lang('permission/' . $permission . '/html'); ?></small></label>
                      </div>
                    </div>
                    <?php
                  }
                }
              }
              }
              
              displayPermissions($subrole,$all_permissions,$assigned_role_permissions);
              ?>
              </div>
            </div>
            <div class="card-footer">
              <div class="i-checks">
                <input id="permission_<?php echo $subrole; ?>" type="checkbox" class="form-control-custom check-all">
                <label for="permission_<?php echo $subrole; ?>"><?php echo lang('option_all_or_none/html'); ?></label>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </form>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>