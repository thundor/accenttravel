<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_flight_airline'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/airline/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/airline/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/airline/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/airline/meta.php'); ?>
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
$airline = $this->view_data['airline'];
$editing = trim($airline->code) !== '';
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <form id="airlinesForm" name="airlinesForm" action="<?php echo site_url('backend/trip_flight/airlines/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="action" name="action" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="code" value="<?php echo $airline->code; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('airline_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
				<?php if(empty($airline->code)){ ?>
				<input type="hidden" name="is_new" value="1" />
              <div class="form-group row">
                <label for="code" class="<?php echo $label_class; ?> text-center"><?php echo lang('code_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="code" type="text" maxlength="50" name="newcode" placeholder="<?php echo lang('code_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($airline->code); ?>" required />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($airline->code); ?></div>
                  <?php } ?>
                  <small class="text-muted"><?php echo lang('code_field_help/html'); ?></small>
                </div>
              </div>
				<?php } ?>
              <div class="form-group row">
                <label for="airline_name" class="<?php echo $label_class; ?> text-center"><?php echo lang('name_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="airline_name" type="text" maxlength="255" name="name" placeholder="<?php echo lang('name_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($airline->name); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($airline->name); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="airline_image" class="<?php echo $label_class; ?> text-center">Imagine:
                  <?php if($airline->image){ ?>
                  <br />
                  <img class="img-thumbnail" src="<?php echo $this->theme_url; ?>/assets/images/<?php echo $airline->image; ?>" alt="-fisierul nu exista-" />
                  <?php } ?>
                </label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                    <input id="airline_image" name="image" type="text" placeholder="Imagine" class="form-control" value="<?php echo htmlspecialchars($airline->image); ?>" />
                    <input type="text" class="form-control border-0" disabled value="<?php echo $this->theme_url ?>images/icons/"/>
                    <input type="file" name="image" id="airline_image_upload" class="form-control" accept="image/gif, image/jpeg, image/png" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($airline->image); ?>&nbsp;</div>
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