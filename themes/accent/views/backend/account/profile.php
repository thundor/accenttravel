<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('account_profile'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/profile/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', dirname(__DIR__) . '/accounts/item/common/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/profile/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/profile/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/profile/meta.php'); ?>
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
$can_write = $this->_ci->user->can('backend-account-profile-save');
?>
<section class="forms">
  <div class="col-12">
    <form id="profileForm" name="profileForm" action="<?php echo site_url('backend/accounts/profile/save'); ?>" method="POST" onsubmit="return false;">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="text" style="display:none;" />
      <input type="password" style="display:none;" />
      <?php } ?>
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('authentication_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="username" class="<?php echo $label_class; ?>"><?php echo lang('username_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="username" type="text" name="username" placeholder="<?php echo lang('username_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->username); ?>" required />
                  <small class="text-muted"><?php echo lang('username_field_help/html'); ?></small>
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($user->username); ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php if($can_write){ ?>
              <div class="form-group row">
                <label for="password" class="<?php echo $label_class; ?>"><?php echo lang('password_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <span class="btn-group">
                    <button type="button" class="btn btn-primary btn-toggle passwordtoggler collapsed" aria-expanded="false" data-toggle="collapse" data-target="#password_change"><i class="fa fa-pencil"></i> <span class="hidden-xs-down">Modifica</span></button>
                  </span>
                </div>
              </div>
              <div id="password_change" class="collapse">
                <div class="form-group row">
                  <label for="password" class="<?php echo $label_class; ?>"><?php echo lang('current_password_field_label/html'); ?></label>
                  <div class="<?php echo $value_class; ?>">
                    <div class="input-group">
                      <input id="password" type="password" name="password" placeholder="<?php echo lang('current_password_field_placeholder'); ?>" class="form-control"/>
                      <span class="input-group-addon">
                        <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                      </span>
                    </div>
                  </div>
                </div>
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
                    <small class="text-muted"><?php echo lang('password_field_help/html'); ?></small>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('contact_information_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="email" class="<?php echo $label_class; ?>"><?php echo lang('email_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="email" type="email" name="email" placeholder="<?php echo lang('email_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->email); ?>" required />
                  <small class="text-muted"><?php echo lang('email_field_help/html'); ?></small>
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($user->email); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="lastname" class="<?php echo $label_class; ?>"><?php echo lang('lastname_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="lastname" type="text" name="lastname" placeholder="<?php echo lang('lastname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->lastname); ?>" required />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($user->lastname); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="firstname" class="<?php echo $label_class; ?>"><?php echo lang('firstname_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="firstname" type="text" name="firstname" placeholder="<?php echo lang('firstname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->firstname); ?>" />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($user->firstname); ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="result_profileForm" class="form-group"></div>
      <?php 
      include dirname(__DIR__) . '/accounts/item/common/fields.php'; 
      ?>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>