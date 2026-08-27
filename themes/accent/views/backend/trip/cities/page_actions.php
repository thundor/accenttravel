<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    if($this->_ci->user->can('backend-config-save')) { ?>
    <a href="<?php echo site_url('backend/trip/cities/add'); ?>" class="btn btn-success">
      <?php echo lang('action_add/html'); ?>
    </a>
    <?php } ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>