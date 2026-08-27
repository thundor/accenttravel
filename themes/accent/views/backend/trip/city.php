<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_city'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/city/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/city/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/city/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/city/meta.php'); ?>
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
$city = $this->view_data['city'];
$editing = $city->id != 0;
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <form id="citiesForm" name="citiesForm" action="<?php echo site_url('backend/trip/cities/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="action" name="action" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $city->id; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('city_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="city_name" class="<?php echo $label_class; ?> text-center"><?php echo lang('name_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="city_name" type="text" maxlength="255" name="name" placeholder="<?php echo lang('name_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($city->name); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($city->name); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="parent_id" class="<?php echo $label_class; ?> text-center">Oras parinte</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <select id="parent_id" name="parent_id" class="form-control" >
                    <option value="">Alege</option>
                    <?php if(isset($city->parent)){ ?>
                    <option value="<?php echo htmlspecialchars($city->parent->id); ?>"><?php echo htmlspecialchars($city->parent->name); ?></option>
                    <?php } ?>
                  </select>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo isset($city->parent) ? htmlspecialchars($city->parent->name) : '-'; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="country_iso_2" class="<?php echo $label_class; ?> text-center"><?php echo lang('country_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input type="hidden" id="country_id" name="country_id" value="<?php echo htmlspecialchars($city->country_id); ?>" />
                  <input type="hidden" id="country_name" name="country_name" value="<?php echo htmlspecialchars($city->country_name); ?>" />
                  <select id="country_iso_2" name="country_iso_2" class="form-control" >
                    <option value="">Alege</option>
                    <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/country_options', array('selected'=>$city->country_iso_2)); ?>
                    <?php themeFunctions::loadAddons(__FILE__ . '/country_options'); ?>
                  </select>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($city->country_name); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="trip_city" class="<?php echo $label_class; ?> text-center"><?php echo lang('trip_city_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input type="hidden" id="trip_city_id" name="trip_city_id" value="<?php echo htmlspecialchars($city->trip_city_id); ?>" />
                  <input type="hidden" id="trip_country_code" value="<?php echo htmlspecialchars($city->country_iso_2); ?>" />
                  <input type="hidden" id="trip_country_id" name="trip_country_id" value="<?php echo htmlspecialchars($city->trip_country_id); ?>" />
                  <input type="hidden" id="trip_city_name" name="trip_city_name" value="<?php echo htmlspecialchars($city->trip_city_name); ?>" />
                  <div class="input-group">
                    <input id="trip_city" type="text" maxlength="255" placeholder="<?php echo lang('name_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($city->trip_city_name); ?>" />
                    <input id="trip_country_name" readonly type="text" maxlength="255" name="trip_country_name" placeholder="Tara" class="form-control" value="<?php echo htmlspecialchars($city->trip_country_name); ?>" />
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($city->trip_city_name); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="aida_city" class="<?php echo $label_class; ?> text-center"><?php echo lang('aida_city_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input type="hidden" id="aida_city_id" name="aida_city_id" value="<?php echo htmlspecialchars($city->aida_city_id); ?>" />
                  <input type="hidden" id="aida_country_code" value="<?php echo htmlspecialchars($city->country_iso_2); ?>" />
                  <input type="hidden" id="aida_country_id" name="aida_country_id" value="<?php echo htmlspecialchars($city->aida_country_id); ?>" />
                  <input type="hidden" id="aida_city_name" name="aida_city_name" value="<?php echo htmlspecialchars($city->aida_city_name); ?>" />
                  <input type="hidden" id="aida_country_name" name="aida_country_name" value="<?php echo htmlspecialchars($city->aida_country_name); ?>" />
                  <div class="input-group">
                    <input id="aida_country" type="text" maxlength="255" placeholder="Tara" class="form-control" value="<?php echo htmlspecialchars($city->aida_country_name); ?>" />
                    <input id="aida_city" type="text" maxlength="255" placeholder="<?php echo lang('name_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($city->aida_city_name); ?>" />
                    <span class="input-group-btn">
                      <button id="aida_city_button_get_description" type="button" class="btn"><i class="fa fa-info"></i> <span class="hidden-lg-down">Descriere</span></button>
                    </span>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($city->aida_city_name); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="city_image" class="<?php echo $label_class; ?> text-center">Imagine:
                  <?php if($city->image){ ?>
                  <br />
                  <img class="img-thumbnail" src="<?php echo $this->theme_url; ?>/assets/images/<?php echo $city->image; ?>" alt="-fisierul nu exista-" />
                  <?php } ?>
                </label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                    <input id="city_image" name="image" type="text" placeholder="Imagine" class="form-control" value="<?php echo htmlspecialchars($city->image); ?>" />
                    <input type="text" class="form-control border-0" disabled value="<?php echo $this->theme_url ?>images/icons/"/>
                    <input type="file" name="image" id="city_image_upload" class="form-control" accept="image/gif, image/jpeg, image/png" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($city->image); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row" id="aida_city_description" style="display:none;">
                <div class="col-12">
                  <textarea placeholder="Descriere Aida" class="form-control" readonly ></textarea>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12">Descriere</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
                  <textarea name="description" placeholder="Descriere" class="form-control make-htmleditor" ><?php echo htmlspecialchars($city->description); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $city->description; ?>&nbsp;</div>
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