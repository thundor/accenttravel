<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$page = $data['page'];
$editing = $page->page_id !== 0;

$can_save = false;
$can_access = $this->_ci->user->can('backend-cms-pages-access');
$can_create = $can_access && $this->_ci->user->can('backend-cms-pages-add');
$can_edit = $can_access && $this->_ci->user->can('backend-cms-pages-edit');
$can_save = (($editing && $can_edit) || (!$editing && $can_create));
$viewing = $this->_method =='view';
?>
<li class="nav-item">
  <div class="btn-group">
    <?php
    ?>
    <?php if(!$viewing && $can_save) { ?>
    <button type="submit" disabled form="pageForm" value="save_and_back" class="btn btn-success">
      <?php echo lang('action_save_and_back_to_list/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <button disabled type="submit" form="pageForm" value="apply" class="dropdown-item">
          <?php echo lang('action_apply/html'); ?>
        </button>
        <?php } ?>
        <?php if($can_create) { ?>
        <button disabled type="submit" form="pageForm" value="save_and_new" class="dropdown-item">
          <?php echo lang('action_save_and_new/html'); ?>
        </button>
        <?php } ?>
        <?php if($editing && $can_create) { ?>
        <button disabled type="submit" form="pageForm" value="save_as_new" class="dropdown-item">
          <?php echo lang('action_save_as_new/html'); ?>
        </button>
        <?php } ?>
        <a href="<?php echo site_url('backend/cms/pages'); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } elseif(!$viewing && $can_create){ ?>
    <button disabled type="submit" form="pageForm" value="save_as_new" class="btn btn-success">
      <?php echo lang('action_save_as_new/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="<?php echo site_url('backend/cms/pages'); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } else { ?>
    <a href="<?php echo site_url('backend/cms/pages'); ?>" class="btn btn-secondary">
      <?php echo lang('action_back_to_list/html'); ?>
    </a>
    <?php if($can_create || $can_edit){ ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <a href="<?php echo site_url('backend/cms/pages/edit?id=' . $page->page_id); ?>" class="dropdown-item">
          <?php echo lang('action_edit/html'); ?>
        </a>
        <?php } ?>
        <?php if($can_create){ ?>
        <a href="<?php echo site_url('backend/cms/pages/add'); ?>" class="dropdown-item">
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