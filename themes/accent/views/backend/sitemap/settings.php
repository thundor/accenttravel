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
            <h2 class="h5 display">Setari <strong>Sitemap</strong></h2>
          </div>
          <div class="card-block">
            <div id="result_settingsForm" class="form-group" ></div>
            <form id="settingsForm" name="settingsForm" class="sitemap_settings" action="<?php echo site_url('backend/sitemap/settings/save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Aliasuri pagina hoteluri</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="input-group">
                    <input type="text" name="hotels" placeholder="Aliasuri pagina" class="form-control" value="<?php echo htmlspecialchars($data['hotels']); ?>" />
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['hotels']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Aliasuri pagina citybreakuri</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="input-group">
                    <input type="text" name="citybreaks" placeholder="Aliasuri pagina" class="form-control" value="<?php echo htmlspecialchars($data['citybreaks']); ?>" />
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['citybreaks']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Aliasuri pagina zboruri</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="input-group">
                    <input type="text" name="flights" placeholder="Aliasuri pagina" class="form-control" value="<?php echo htmlspecialchars($data['flights']); ?>" />
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['flights']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Aliasuri pagina vacante</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="input-group">
                    <input type="text" name="packages" placeholder="Aliasuri pagina" class="form-control" value="<?php echo htmlspecialchars($data['packages']); ?>" />
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['packages']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <p>Puteti introduce mai multe aliasuri in caseta, despartie prin virgula. Acestea vor fi folosite pentru a crea linkuri separate in sitemap. Nu introduceti spatii goale in casete.</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>