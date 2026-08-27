<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('travelfuse_' . basename(__FILE__, '.php') . ''); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('twigjs'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php //themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/meta.php'); ?>
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
$facility = $this->view_data['facility'];
$editing = $facility->id != 0;
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
	<div id="result_facilitiesForm"></div>
    <form id="facilitiesForm" name="facilitiesForm" action="<?php echo site_url('backend/travelfuse/travelfuse_facilities/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="task" name="task" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $facility->id; ?>" />
      <?php } ?>
      <?php } ?>
      <div id="facility-details" class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('facility_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
			  <div class="form-group row">
                <label class="<?php echo $label_class; ?> text-center">Tip</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo htmlspecialchars($facility->type); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label class="<?php echo $label_class; ?> text-center">Nume</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo htmlspecialchars($facility->namefinal); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="facility_name-ro" class="<?php echo $label_class; ?> text-center">Nume RO</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="facility_name-ro" type="text" maxlength="255" name="_name_ro" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($facility->_name_ro); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($facility->_name_ro); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="facility_name-en" class="<?php echo $label_class; ?> text-center">Nume EN</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="facility_name-en" type="text" maxlength="255" name="_name_en" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($facility->_name_en); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($facility->_name_en); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="facility_status" class="<?php echo $label_class; ?> text-center"><?php echo lang('status_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="facility_status_active" type="radio" value="1" name="status" <?php echo $facility->status ==1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="facility_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="facility_status_inactive" type="radio" value="0" name="status" <?php echo !$facility->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="facility_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $facility->status == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
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