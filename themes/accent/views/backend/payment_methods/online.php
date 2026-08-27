<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/online/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/common/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/common/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/online/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/online/page_title.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['lg'] = 4;
$label_size['md'] = 3;
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
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Setari <strong>Plata online</strong></h2>
          </div>
          <div id="result_onlineForm" class="form-group" ></div>
          <div class="card-block">
            <form id="onlineForm" name="onlineForm" class="payment_method_settings" action="<?php echo site_url('backend/payment_methods/online/save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label for="status" class="<?php echo $label_class; ?>">Status</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="status_inactive" type="radio" value="0" name="online_status" <?php echo !$data['online_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="status_live" type="radio" value="1" name="online_status" <?php echo $data['online_status'] === 1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_live">Live</label>
                  </div>
                  <div class="i-checks">
                    <input id="status_test" type="radio" value="-1" name="online_status" <?php echo $data['online_status'] === -1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_test">Sandbox</label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['online_status'] < 0 ? 'Sandbox' : ($data['online_status'] == 1 ? 'Live' : lang('option_inactive')); ?></div>
                  <?php } ?>
                  <?php if($can_write){ ?>
                  <small class="help-block text-muted">
                  <p><b>Sandbox</b>: Optiunea de plata va fi disponibila doar <b>utilizatorilor autentificati cu permisiunea de <a href="<?php echo site_url('backend/accounts/roles?path=backend-config'); ?>">Salvare Setari generale</a></b>. Pentru ceilalti utilizatori, statusul va fi considerat drept <b>Inactiv</b> si nu va apare ca si optiune de plata in interfata.</p>
                  </small>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12">Descriere</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <textarea name="online_text" placeholder="Descriere" class="form-control make-htmleditor" <?php echo $data['online_status'] ? 'required' : ''; ?>><?php echo htmlspecialchars($data['online_text']); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['online_text']; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>