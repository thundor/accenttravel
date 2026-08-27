<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('frontend/account_profile'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/password/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/password/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/password/meta.php'); ?>
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
// $can_write = $this->_ci->user->canAny('frontend-account-profile-save','backend-account-profile-save');
$can_write = 1;
?>
<div class="container mt-1 mb-5">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <h1>Schimbare parola</h1>
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h2 class="h5 display">Parola noua</h2>
        </div>
        <div class="card-block">
          <div id="result_profilePasswordForm" class="form-group"></div>
          <form id="profilePasswordForm" name="profilePasswordForm" class="profile_form" method="POST" onsubmit="return false;">
            <input type="text" id="current_email_input" style="display:none;" value="<?php echo htmlspecialchars($user->type=='customer' ? $user->email : $user->username, ENT_QUOTES, false); ?>" />
            <div class="form-group row">
              <label for="new_password" class="<?php echo $label_class; ?>"><?php echo lang('new_password_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <div class="input-group">
                  <input id="new_password" type="password" name="new_password" placeholder="<?php echo lang('new_password_field_placeholder'); ?>" class="form-control"/>
                  <span class="input-group-addon">
                    <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#new_password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="confirm_new_password" class="<?php echo $label_class; ?>"><?php echo lang('confirm_new_password_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <div class="input-group">
                  <input id="confirm_new_password" type="password" name="confirm_new_password" placeholder="<?php echo lang('confirm_new_password_field_placeholder'); ?>" class="form-control"/>
                  <span class="input-group-addon">
                    <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#confirm_new_password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                  </span>
                </div>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="password_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <p class="text-muted"><?php echo lang('password_field_help/html'); ?></p>
                <button type="submit" id="password_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>