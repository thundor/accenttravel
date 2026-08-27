<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('accounts_item_customer'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/customer/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/common/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/customer/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/customer/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/customer/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$user = $this->view_data['user'];
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <form id="customerForm" name="customerForm" action="<?php echo site_url('backend/accounts/customer/save'); ?>" method="POST" onsubmit="return false;">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="action" name="action" value="" />
      <input type="hidden" name="id" value="<?php echo $user->id; ?>" />
      <?php if($user->id){ ?>
      <input type="text" style="display:none;" />
      <input type="password" name="password" style="display:none;" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('authentication_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="email" class="<?php echo $label_class; ?>"><?php echo lang('email_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="email" type="email" name="email" placeholder="<?php echo lang('email_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->email); ?>" required />
                  <small class="text-muted"><?php echo lang('email_field_help/html'); ?></small>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($user->email); ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php if($can_write){ ?>
              <div class="form-group row">
                <label for="password" class="<?php echo $label_class; ?>"><?php echo lang('password_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input id="password" type="password" name="password" placeholder="<?php echo lang('password_field_placeholder'); ?>" class="form-control"/>
                    <span class="input-group-addon">
                      <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                    </span>
                  </div>
                  <small class="text-muted"><?php echo lang('password_field_help/html'); ?></small>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('account_information_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="status" class="<?php echo $label_class; ?>"><?php echo lang('status_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="status_active" type="radio" value="1" name="status" <?php echo $user->status == 1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_active"><?php echo lang('status_option_active/html'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="status_blocked" type="radio" value="0" name="status" <?php echo $user->status == 0 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_blocked"><?php echo lang('status_option_blocked/html'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $user->status == 1 ? lang('status_option_active/html') : lang('status_option_blocked/html'); ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('contact_information_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="lastname" class="<?php echo $label_class; ?>"><?php echo lang('lastname_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="lastname" type="text" name="lastname" placeholder="<?php echo lang('lastname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->lastname); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($user->lastname); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="firstname" class="<?php echo $label_class; ?>"><?php echo lang('firstname_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="firstname" type="text" name="firstname" placeholder="<?php echo lang('firstname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->firstname); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($user->firstname); ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="result_customerForm" class="form-group"></div>
      <?php 
      include 'common/fields.php'; 
      ?>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>