<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$can_save = false;
$can_access = $this->_ci->user->can('backend-config-access');
$can_save = $this->_ci->user->can('backend-config-save');
$viewing = $this->_method =='view';
?>
<li class="nav-item">
  <div class="btn-group">
    <?php if(!$viewing && $can_save) { ?>
    <button type="submit" disabled form="menuForm" value="apply" class="btn btn-success">
      <?php echo lang('action_apply/html'); ?>
    </button>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>