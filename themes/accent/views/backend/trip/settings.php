<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/settings/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/settings/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/settings/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/settings/page_title.php'); ?>
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
$can_write = $this->_ci->user->can('backend-config-save');
$data = $this->view_data;
?>
<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Configurari <strong>API</strong></h2>
          </div>
          <div class="card-block">
            <form id="tripForm" name="tripForm" class="trip_form" action="<?php echo site_url('backend/trip/settings/save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Endpoint URL</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="trip_endpoint" placeholder="Endpoint URL" class="form-control" value="<?php echo htmlspecialchars($data['trip_endpoint']); ?>" required />
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">App ID</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="trip_app_id" placeholder="App ID" class="form-control" value="<?php echo htmlspecialchars($data['trip_app_id']); ?>" required />
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">App Secret</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="trip_app_secret" placeholder="App Secret" class="form-control" value="<?php echo htmlspecialchars($data['trip_app_secret']); ?>" required />
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Username</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="trip_username" placeholder="Username" class="form-control" value="<?php echo htmlspecialchars($data['trip_username']); ?>" required />
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Password</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="password" id="trip_password" name="trip_password" placeholder="Password" class="form-control" value="<?php echo htmlspecialchars($data['trip_password']); ?>" required />
                    <span class="input-group-addon">
                      <input type="checkbox" id="trip_password_toggle" />
                    </span>
                  </div>
                </div>
              </div>
            </form>
            <div id="result_tripForm" class="form-group" ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>