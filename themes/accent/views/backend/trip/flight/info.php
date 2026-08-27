<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/info/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/info/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/info/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/info/page_title.php'); ?>
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
            <h2 class="h5 display">Setari <strong>Informatii zbor</strong></h2>
          </div>
          <div id="result_flightInfoForm" class="form-group" ></div>
          <div class="card-block">
            <form id="flightInfoForm" name="flightInfoForm" class="trip_flight_info" action="<?php echo site_url('backend/trip_flight/flight_info/save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label for="status" class="<?php echo $label_class; ?>">Status</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="status_inactive" type="radio" value="0" name="status" <?php echo !$data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="status_active" type="radio" value="1" name="status" <?php echo $data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['status'] ? lang('option_active') : lang('option_inactive'); ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php /* <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Titlu</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input type="text" name="title" placeholder="Titlu" class="form-control" value="<?php echo htmlspecialchars($data['title']); ?>" <?php echo $data['status'] ? 'required' : ''; ?>/>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['title']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div> */ ?>
              <div class="form-group row">
                <label class="col-12">Descriere</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <textarea name="description" placeholder="Descriere" class="form-control make-htmleditor"><?php echo htmlspecialchars($data['description']); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['description']; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <?php /*
              <div class="form-group row">
                <label class="col-12">Titlu asigurare turist 1</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <input name="insurance1_title" placeholder="Titlu asigurare turist 1" class="form-control" value="<?php echo htmlspecialchars($data['insurance1_title']); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['insurance1_title']; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div> */ ?>
			  
              <div class="form-group row">
                <label class="col-12">Descriere asigurare turist 1</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <textarea name="insurance1_desc" placeholder="Descriere asigurare turist 1" class="form-control make-htmleditor"><?php echo htmlspecialchars($data['insurance1_desc']); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['insurance1_desc']; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <?php /*
              <div class="form-group row">
                <label class="col-12">Titlu asigurare turist 2</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <input name="insurance2_title" placeholder="Titlu asigurare turist 2" class="form-control" value="<?php echo htmlspecialchars($data['insurance2_title']); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['insurance2_title']; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  */ ?>
              <div class="form-group row">
                <label class="col-12">Descriere asigurare turist 2</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <textarea name="insurance2_desc" placeholder="Descriere asigurare turist 2" class="form-control make-htmleditor"><?php echo htmlspecialchars($data['insurance2_desc']); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $data['insurance2_desc']; ?>&nbsp;</div>
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