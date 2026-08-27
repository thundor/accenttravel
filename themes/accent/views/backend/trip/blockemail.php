<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_blockemail'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php //themeFunctions::includeAddon('select2'); ?>
<?php //themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/blockemail/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/blockemail/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/blockemail/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/blockemail/meta.php'); ?>
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
$blockemail = $this->view_data['blockemail'];
$editing = $blockemail->id != 0;
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <form id="blockemailsForm" name="blockemailsForm" action="<?php echo site_url('backend/trip/blockemails/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="task" name="task" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $blockemail->id; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('blockemail_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="blockemail_code" class="<?php echo $label_class; ?> text-center"><?php echo lang('code_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="blockemail_code" type="text" maxlength="255" name="code" placeholder="<?php echo lang('code_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($blockemail->code); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($blockemail->code); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_status" class="<?php echo $label_class; ?> text-center"><?php echo lang('status_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="blockemail_status_active" type="radio" value="1" name="status" <?php echo $blockemail->status ==1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="blockemail_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="blockemail_status_inactive" type="radio" value="0" name="status" <?php echo !$blockemail->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="blockemail_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $blockemail->status == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <?php /*
              <div class="form-group row">
                <label for="blockemail_percentage" class="<?php echo $label_class; ?> text-center"><?php echo lang('percentage_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="blockemail_percentage" type="number" min="0" max="100" step="0.01" name="percentage" placeholder="<?php echo lang('percentage_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($blockemail->percentage); ?>" required/>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($blockemail->percentage); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">%</span>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_max_uses" class="<?php echo $label_class; ?> text-center"><?php echo lang('max_uses_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="blockemail_max_uses" type="number" min="0" max="99999999999" step="1" name="max_uses" placeholder="<?php echo lang('max_uses_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($blockemail->max_uses); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($blockemail->max_uses); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_nr_uses" class="<?php echo $label_class; ?> text-center"><?php echo lang('nr_uses_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo intval($blockemail->nr_uses); ?>&nbsp;</div>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_date_start" class="<?php echo $label_class; ?> text-center"><?php echo lang('date_start_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <label class="input-group mb-0">
                    <input id="blockemail_date_start" maxlength="10" type="text" name="date_start" placeholder="<?php echo lang('date_start_field_placeholder'); ?>" class="form-control input-date_start" value="<?php echo htmlspecialchars($blockemail->date_start); ?>" />
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </label>
                  <?php } else { ?>
                  <div id="blockemail_date_start" class="form-control" readonly><?php echo htmlspecialchars($blockemail->date_start); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_date_expire" class="<?php echo $label_class; ?> text-center"><?php echo lang('date_expire_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <label class="input-group mb-0">
                    <input id="blockemail_date_expire" maxlength="10" type="text" name="date_expire" placeholder="<?php echo lang('date_expire_field_placeholder'); ?>" class="form-control input-date_expire" value="<?php echo htmlspecialchars($blockemail->date_expire); ?>" />
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </label>
                  <?php } else { ?>
                  <div id="blockemail_date_expire" class="form-control" readonly><?php echo htmlspecialchars($blockemail->date_expire); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_hotel_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru hoteluri</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="blockemail_hotel_active" type="checkbox" value="1" name="hotel" <?php echo $blockemail->hotel ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="blockemail_hotel_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $blockemail->hotel == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="blockemail_package_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru pachete Romania</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="blockemail_package_active" type="checkbox" value="1" name="package" <?php echo $blockemail->package ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="blockemail_package_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $blockemail->package == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              */ ?>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>