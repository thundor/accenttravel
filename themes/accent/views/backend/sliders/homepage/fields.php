<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$zone_data = array(); 
$zone_data['status'] = isset($data['status'], $data['status'][$zone-1]) ? $data['status'][$zone-1] : 0;
$zone_data['title'] = isset($data['title'], $data['title'][$zone-1]) ? $data['title'][$zone-1] : '';
$zone_data['description'] = isset($data['description'], $data['description'][$zone-1]) ? $data['description'][$zone-1] : '';
$zone_data['button'] = isset($data['button'], $data['button'][$zone-1]) ? $data['button'][$zone-1] : '';
$zone_data['url'] = isset($data['url'], $data['url'][$zone-1]) ? $data['url'][$zone-1] : '';
?>
<div class="form-group row">
  <label for="sliders_homepage_zone_<?php echo $zone; ?>_status_active" class="<?php echo $label_class; ?>">Status:</label>
  <div class="<?php echo $value_class; ?>">
    <div class="i-checks">
      <input id="sliders_homepage_zone_<?php echo $zone; ?>_status_active" type="radio" value="1" name="data[status][<?php echo $zone -1 ; ?>]" <?php echo $zone_data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
      <label for="sliders_homepage_zone_<?php echo $zone; ?>_status_active"><?php echo lang('option_active'); ?></label>
    </div>
    <div class="i-checks">
      <input id="sliders_homepage_zone_<?php echo $zone; ?>_status_inactive" type="radio" value="0" name="data[status][<?php echo $zone -1 ; ?>]" <?php echo !$zone_data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
      <label for="sliders_homepage_zone_<?php echo $zone; ?>_status_inactive"><?php echo lang('option_inactive'); ?></label>
    </div>
  </div>
</div>
<div class="form-group row">
  <label for="sliders_homepage_zone_<?php echo $zone; ?>_title" class="<?php echo $label_class; ?>">Titlu:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="sliders_homepage_zone_<?php echo $zone; ?>_title" name="data[title][<?php echo $zone -1 ; ?>]" type="text" placeholder="Titlu" class="form-control" value="<?php echo htmlspecialchars($zone_data['title']); ?>" />
  </div>
</div>
<div class="form-group row">
  <label for="sliders_homepage_zone_<?php echo $zone; ?>_description" class="<?php echo $label_class; ?>">Descriere:</label>
  <div class="<?php echo $value_class; ?>">
    <textarea id="sliders_homepage_zone_<?php echo $zone; ?>_description" name="data[description][<?php echo $zone -1 ; ?>]" placeholder="Descriere" class="form-control" ><?php echo htmlspecialchars($zone_data['description']); ?></textarea>
  </div>
</div>
<div class="form-group row">
  <label for="sliders_homepage_zone_<?php echo $zone; ?>_button" class="<?php echo $label_class; ?>">Titlu buton:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="sliders_homepage_zone_<?php echo $zone; ?>_button" name="data[button][<?php echo $zone -1 ; ?>]" type="text" placeholder="Titlu buton" class="form-control" value="<?php echo htmlspecialchars($zone_data['button']); ?>" />
  </div>
</div>
<div class="form-group row">
  <label for="sliders_homepage_zone_<?php echo $zone; ?>_url" class="<?php echo $label_class; ?>">URL:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="sliders_homepage_zone_<?php echo $zone; ?>_url" name="data[url][<?php echo $zone -1 ; ?>]" type="text" placeholder="URL" class="form-control" value="<?php echo htmlspecialchars($zone_data['url']); ?>" />
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>