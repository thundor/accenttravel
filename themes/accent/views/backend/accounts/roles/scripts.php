<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$can_save = $this->_ci->user->can('backend-accounts-roles-save');
if($can_save){
?>
<script>
(function($){
  $('input[type=checkbox].check-all').on('click', function(){
    var checked = $(this).is(':checked');
    var $checkboxes = $('input[type=checkbox][id^=' + this.id + ']').prop('checked',checked);
    treatRolePermissions();
  });
  $('input[type=checkbox][name^="permission["]').on('click', function(){
    treatRolePermissions();
  });
  function treatRolePermission(role,permission,checked,disabled){
    var enabled = checked && !disabled;
    if(permission.indexOf('backend-accounts-admin-access-') === 0){
      var zone_role = permission.replace('backend-accounts-admin-access-','');
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-edit-' + zone_role + '"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-delete-' + zone_role + '"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-view-' + zone_role + '"]').prop('disabled',!enabled);
      
      var $own_access = $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-access-' + zone_role + '"]');
      $own_access.prop('disabled',enabled);

      var owned_enabled = $own_access.is(':checked') && !$own_access.is(':disabled');
      
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-add-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-edit-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-delete-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-view-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);

      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-add-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-edit-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-delete-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-view-' + zone_role + '"]').prop('disabled',!enabled && !owned_enabled);

    }
    else if(permission.indexOf('backend-accounts-roles-access-') === 0){
      if(!$('input[type=checkbox][name^="permission[' + role + ']"][value^="backend-accounts-roles-access-"]:checked').length){
        $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-roles-save"]').prop('disabled',!enabled);
      }
    }
    else if(permission.indexOf('backend-accounts-admin-view-') === 0){
      var zone_role = permission.replace('backend-accounts-admin-view-','');
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-view-' + zone_role + '"]:enabled').prop('disabled',enabled);
    }
    else if(permission.indexOf('backend-accounts-admin-edit-') === 0){
      var zone_role = permission.replace('backend-accounts-admin-edit-','');
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-edit-' + zone_role + '"]:enabled').prop('disabled',enabled);
    }
    else if(permission.indexOf('backend-accounts-admin-delete-') === 0){
      var zone_role = permission.replace('backend-accounts-admin-delete-','');
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-delete-' + zone_role + '"]:enabled').prop('disabled',enabled);
    }
    else if(permission.indexOf('backend-accounts-admin-own-access-') === 0){
      var zone_role = permission.replace('backend-accounts-admin-own-access-','');
      
      var $all_access = $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-access-' + zone_role + '"]');
      var all_enabled = $all_access.is(':checked') && !$all_access.is(':disabled');
      
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-add-' + zone_role + '"]').prop('disabled',!enabled && !all_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-edit-' + zone_role + '"]').prop('disabled',!enabled && !all_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-delete-' + zone_role + '"]').prop('disabled',!enabled && !all_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-admin-own-view-' + zone_role + '"]').prop('disabled',!enabled && !all_enabled);
    }
    else if(permission == 'backend-account-profile-access'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-account-profile-save"]').prop('disabled',!enabled);
    }
    else if(permission == 'backend-accounts-customer-access'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-view"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-edit"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-delete"]').prop('disabled',!enabled);
      
      
      var $own_access = $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-access"]');
      $own_access.prop('disabled',enabled);

      var owned_enabled = $own_access.is(':checked') && !$own_access.is(':disabled');
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-add"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-view"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-edit"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-delete"]').prop('disabled',!enabled && !owned_enabled);
    }
    else if(permission == 'backend-accounts-customer-view'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-view"]:enabled').prop('disabled',enabled);
    }
    else if(permission == 'backend-accounts-customer-edit'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-edit"]:enabled').prop('disabled',enabled);
    }
    else if(permission == 'backend-accounts-customer-delete'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-accounts-customer-own-delete"]:enabled').prop('disabled',enabled);
    }
    else if(permission == 'backend-cms-pages-access'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-view"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-edit"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-delete"]').prop('disabled',!enabled);
      
      
      var $own_access = $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-access"]');
      $own_access.prop('disabled',enabled);

      var owned_enabled = $own_access.is(':checked') && !$own_access.is(':disabled');
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-add"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-view"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-edit"]').prop('disabled',!enabled && !owned_enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-delete"]').prop('disabled',!enabled && !owned_enabled);
    }
    else if(permission == 'backend-cms-pages-view'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-view"]:enabled').prop('disabled',enabled);
    }
    else if(permission == 'backend-cms-pages-edit'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-edit"]:enabled').prop('disabled',enabled);
    }
    else if(permission == 'backend-cms-pages-delete'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-pages-own-delete"]:enabled').prop('disabled',enabled);
    }
    else if(permission == 'backend-cms-layouts-access'){
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-layouts-view"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-layouts-add"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-layouts-edit"]').prop('disabled',!enabled);
      $('input[type=checkbox][name^="permission[' + role + ']"][value="backend-cms-layouts-delete"]').prop('disabled',!enabled);
    }
  }
  function treatRolePermissions(){
    $('input[type=checkbox][name^="permission["]').each(function(index){
      var role = this.name.replace('permission[','').replace('][]','');
      treatRolePermission(role,this.value,$(this).is(':checked'), $(this).is(':disabled'));
    })
  }
  treatRolePermissions();
})(jQuery)
</script>
<?php } ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>