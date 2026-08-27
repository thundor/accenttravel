<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/facebook/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/common/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/common/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/facebook/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/facebook/page_title.php'); ?>
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
            <h2 class="h5 display">Setari <strong>API</strong></h2>
          </div>
          <div class="card-block">
            <form id="facebookForm" name="facebookForm" class="social_network_settings" action="<?php echo site_url('backend/social_networks/facebook/save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label for="status" class="<?php echo $label_class; ?>">Status</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="status_active" type="radio" value="1" name="facebook_status" <?php echo $data['facebook_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="status_inactive" type="radio" value="0" name="facebook_status" <?php echo !$data['facebook_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control"><?php echo $data['facebook_status'] == 1 ? lang('option_active') : lang('option_inactive'); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">App ID</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="facebook_app_id" placeholder="App ID" class="form-control" value="<?php echo htmlspecialchars($data['facebook_app_id']); ?>" <?php echo $data['facebook_status'] ? 'required' : ''; ?>/>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">App Secret</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="facebook_app_secret" placeholder="App Secret" class="form-control" value="<?php echo htmlspecialchars($data['facebook_app_secret']); ?>" <?php echo $data['facebook_status'] ? 'required' : ''; ?>/>
                  </div>
                </div>
              </div>
            </form>
            <div id="result_facebookForm" class="form-group" ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>