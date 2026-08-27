<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$discount = $data['discount'];
$editing = $discount->id != 0;

$can_save = false;
$can_access = $this->_ci->user->can('backend-config-access');
$can_edit = $can_access && $this->_ci->user->can('backend-config-save');
$can_save = $can_edit;
$viewing = $this->_method =='view';
?>
<li class="nav-item">
  <div class="btn-group">
    <?php if(!$viewing && $can_save) { ?>
    <button type="submit" disabled form="discountsForm" value="save_and_back" class="btn btn-success">
      <?php echo lang('action_save_and_back_to_list/html'); ?>
    </button>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <button disabled type="submit" form="discountsForm" value="apply" class="dropdown-item">
          <?php echo lang('action_apply/html'); ?>
        </button>
        <?php } ?>
        <a href="<?php echo site_url('backend/trip/discounts'); ?>" class="dropdown-item">
          <?php echo lang('action_back_to_list/html'); ?>
        </a>
      </div>
    </div>
    <?php } else { ?>
    <a href="<?php echo site_url('backend/trip/discounts'); ?>" class="btn btn-secondary">
      <?php echo lang('action_back_to_list/html'); ?>
    </a>
    <?php if($can_edit){ ?>
    <div class="dropdown btn-group">
      <button class="btn-group btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if($can_edit){ ?>
        <a href="<?php echo site_url('backend/trip/discounts/edit?id=' . $discount->id); ?>" class="dropdown-item">
          <?php echo lang('action_edit/html'); ?>
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