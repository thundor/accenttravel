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
$label_size['xl'] = 4;
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
    <div id="result_tripForm" class="form-group" ></div>
    <form id="tripForm" name="tripForm" class="trip_form" action="<?php echo site_url('backend/general/settings/save'); ?>" method="POST">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <div class="row">
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display">Telefon <strong>Contact</strong></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Numar cu prefix</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="contact_phone_number" placeholder="+40771255279" class="form-control" value="<?php echo htmlspecialchars($data['contact_phone_number']); ?>" />
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Text afisat</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="contact_phone_text" placeholder="0771 255 279" class="form-control" value="<?php echo htmlspecialchars($data['contact_phone_text']); ?>" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display">Setari <strong>Homepage</strong></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Ascundere popup</label>
                <div class="<?php echo $value_class; ?>">
                  <label class="input-group m-0">
                    <span class="input-group-addon">
                      <input type="checkbox" name="dont_show_home_popup" <?php echo $data['dont_show_home_popup'] ? ' checked="checked"' : ''; ?> />
                    </span>
                    <span class="form-control">
                      Nu mai afisa acest popup
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
		
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display">Tema <strong>NewUX</strong></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Versiune resurse ( a-z A-Z 0-9 - _ .)</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="text" name="newux_version" placeholder="1.0.2" class="form-control" value="<?php echo htmlspecialchars($data['newux_version']); ?>" />
                  </div>
                </div>
				<p>Browserele cache-uiesc resursele/modulele introduse prin tema newux (de la a 2-a accesare). Adaugand acest parametru de versiune la preluarea lor provoaca preluarea noua a resurselor. Modificati aceasta valoare de fiecare data cand finalizati modificarile implementate.</p>
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