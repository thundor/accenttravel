<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/contact_mobile_footer/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/contact_mobile_footer/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/contact_mobile_footer/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/contact_mobile_footer/page_title.php'); ?>
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
    <div id="result_contactMobileForm" class="form-group" ></div>
    <form id="contactMobileForm" name="contactMobileForm" class="social_contact_mobile_footer" action="<?php echo site_url('backend/static/contactMobileFooter/save'); ?>" method="POST">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <div class="row">
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display">Contact <strong>Whatsapp</strong></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="whatsapp_status" class="<?php echo $label_class; ?>">Status</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="whatsapp_status_active" type="radio" value="1" name="whatsapp_status" <?php echo $data['whatsapp_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="whatsapp_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="whatsapp_status_inactive" type="radio" value="0" name="whatsapp_status" <?php echo !$data['whatsapp_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="whatsapp_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control"><?php echo $data['whatsapp_status'] == 1 ? lang('option_active') : lang('option_inactive'); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Numar telefon</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="number" min="0" max="9999999999999999" step="1" name="whatsapp_phone_number" placeholder="40771255279" class="form-control" value="<?php echo htmlspecialchars($data['whatsapp_phone_number']); ?>" <?php echo $data['whatsapp_status'] ? 'required' : ''; ?>/>
                  </div>
                  <p class="text muted">Pentru +40 771 255 279 introduceti 40771255279</p>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Text afisat</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="tel" name="whatsapp_text" placeholder="<?php echo htmlspecialchars($data['whatsapp_phone_number']); ?>" class="form-control" value="<?php echo htmlspecialchars($data['whatsapp_text']); ?>" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display">Contact <strong>telefonic</strong></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="contact_status" class="<?php echo $label_class; ?>">Status</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="contact_status_active" type="radio" value="1" name="contact_status" <?php echo $data['contact_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="contact_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="contact_status_inactive" type="radio" value="0" name="contact_status" <?php echo !$data['contact_status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="contact_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control"><?php echo $data['contact_status'] == 1 ? lang('option_active') : lang('option_inactive'); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Numar telefon</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="tel" name="contact_phone_number" placeholder="" class="form-control" value="<?php echo htmlspecialchars($data['contact_phone_number']); ?>" <?php echo $data['contact_status'] ? 'required' : ''; ?>/>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Text afisat</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="tel" name="contact_text" placeholder="<?php echo htmlspecialchars($data['contact_phone_number']); ?>" class="form-control" value="<?php echo htmlspecialchars($data['contact_text']); ?>" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>