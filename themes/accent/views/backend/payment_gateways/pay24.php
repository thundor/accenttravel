<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::loadLang('payment_gateways/pay24'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/pay24/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/common/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/common/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/pay24/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/pay24/page_title.php'); ?>
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
            <h2 class="h5 display">Setari <strong>API</strong></h2>
          </div>
          <div class="card-block">
            <div id="result_pay24Form" class="form-group" ></div>
            <form id="pay24Form" name="pay24Form" class="payment_gateway_settings" action="<?php echo site_url('backend/payment_gateways/pay24/save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Link IPN</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo htmlspecialchars(site_url('/trip/checkout/pay24/ipn')); ?></div>
                </div>
              </div>
              <div class="form-group row">
                <label for="status" class="<?php echo $label_class; ?>">Status</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="status_inactive" type="radio" value="0" name="pay24_status" <?php echo !$data['pay24_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="status_live" type="radio" value="1" name="pay24_status" <?php echo $data['pay24_status'] === 1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_live">Live</label>
                  </div>
                  <div class="i-checks">
                    <input id="status_test_public" type="radio" value="-1" name="pay24_status" <?php echo $data['pay24_status'] === -1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_test_public">Sandbox(privat)</label>
                  </div>
                  <div class="i-checks">
                    <input id="status_test_privat" type="radio" value="-2" name="pay24_status" <?php echo $data['pay24_status'] === -2 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_test_privat">Sandbox(public)</label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['pay24_status'] < 0 ? 'Sandbox(' . ($data['pay24_status'] == -1 ? 'privat' : 'public') . ')' : ($data['pay24_status'] == 1 ? 'Live' : lang('option_inactive')); ?></div>
                  <?php } ?>
                  <?php if($can_write){ ?>
                  <small class="help-block text-muted">
                  <p>In mediul <b>Sandbox</b>, operatiile de plata si raspunsurile generate de procesator vor fi fictive, dar workflow-ul intern va trata raspunsul in acelasi mod ca si cand ar fi "Live".</p>
                  <p><b>Sandbox PRIVAT</b>: Optiunea de plata va fi disponibila doar <b>utilizatorilor autentificati cu permisiunea de <a href="<?php echo site_url('backend/accounts/roles?path=backend-config'); ?>">Salvare Setari generale</a></b>. Pentru ceilalti utilizatori, statusul va fi considerat drept <b>Inactiv</b> si nu va apare ca si optiune de plata in interfata.</p>
                  <p><b>Sandbox PUBLIC</b>: Optiunea de plata va fi disponibila tuturor. (<b class="text-danger"><i class="fa fa-warning"></i> A nu se utiliza in productie</b>)</p>
                  </small>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Merchant Integration Code</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="input-group">
                    <input type="text" name="pay24_merchant_id" placeholder="Merchant Integration Code" class="form-control" value="<?php echo htmlspecialchars($data['pay24_merchant_id']); ?>" <?php echo $data['pay24_status'] ? 'required' : ''; ?>/>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['pay24_merchant_id']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Secret Key</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="input-group">
                    <input type="text" name="pay24_secret_key" placeholder="Secret Key" class="form-control" value="<?php echo htmlspecialchars($data['pay24_secret_key']); ?>" <?php echo $data['pay24_status'] ? 'required' : ''; ?>/>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['pay24_secret_key']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Metode de plata</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <select name="pay24_payment_methods[]" class="form-control" multiple size="17">
                  <?php foreach($data['accepted_payment_methods_blocks'] as $group_key => $group_payment_methods){ ?>
                    <optgroup label="<?php echo htmlspecialchars(lang($group_key)); ?>">
                    <?php foreach($group_payment_methods as $group_payment_method_key => $group_payment_method){ ?>
                      <option value="<?php echo htmlspecialchars($group_payment_method); ?>" <?php echo in_array($group_payment_method, $data['payment_methods']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($group_payment_method); ?> - <?php echo htmlspecialchars(lang($group_payment_method)); ?></option>
                    <?php } ?>
                    </optgroup>
                  <?php } ?>
                  </select>
                  <p class="text-info"><i class="fa fa-info-circle"></i> Folositi tastele <b>Shift</b> / <b>Ctrl</b> pentru selectare multipla</p>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['pay24_secret_key']); ?>&nbsp;</div>
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