<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$zone_data = array(); 
$zone_data['status'] = isset($data['status'], $data['status'][$zone-1]) ? $data['status'][$zone-1] : 0;
$zone_data['company_code'] = isset($data['company_code'], $data['company_code'][$zone-1]) ? $data['company_code'][$zone-1] : '';
$zone_data['title'] = isset($data['title'], $data['title'][$zone-1]) ? $data['title'][$zone-1] : '';
$zone_data['departure'] = isset($data['departure'], $data['departure'][$zone-1]) ? $data['departure'][$zone-1] : 55;
$zone_data['return'] = isset($data['return'], $data['return'][$zone-1]) ? $data['return'][$zone-1] : 5;
$zone_data['image'] = isset($data['image'], $data['image'][$zone-1]) ? $data['image'][$zone-1] : '';
?>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_status_active" class="<?php echo $label_class; ?>">Status:</label>
  <div class="<?php echo $value_class; ?>">
    <div class="i-checks">
      <input id="offers_recommended_zone_<?php echo $zone; ?>_status_active" type="radio" value="1" name="data[status][<?php echo $zone -1 ; ?>]" <?php echo $zone_data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
      <label for="offers_recommended_zone_<?php echo $zone; ?>_status_active"><?php echo lang('option_active'); ?></label>
    </div>
    <div class="i-checks">
      <input id="offers_recommended_zone_<?php echo $zone; ?>_status_inactive" type="radio" value="0" name="data[status][<?php echo $zone -1 ; ?>]" <?php echo !$zone_data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
      <label for="offers_recommended_zone_<?php echo $zone; ?>_status_inactive"><?php echo lang('option_inactive'); ?></label>
    </div>
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_company_code" class="<?php echo $label_class; ?>">Companie:</label>
  <div class="<?php echo $value_class; ?>">
    <label class="input-group mb-0">
      <input id="offers_recommended_zone_<?php echo $zone; ?>_company_code" type="hidden" name="data[company_code][<?php echo $zone -1 ; ?>]" class="" value="<?php echo htmlspecialchars($zone_data['company_code']); ?>"/>
    </label>
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_title" class="<?php echo $label_class; ?>">Nume companie:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="offers_recommended_zone_<?php echo $zone; ?>_title" name="data[title][<?php echo $zone -1 ; ?>]" type="text" placeholder="Titlu" class="form-control" value="<?php echo htmlspecialchars($zone_data['title']); ?>" />
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_image" class="<?php echo $label_class; ?>">Imagine companie:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="offers_recommended_zone_<?php echo $zone; ?>_image" name="data[image][<?php echo $zone -1 ; ?>]" type="text" placeholder="Imagine" class="form-control" value="<?php echo htmlspecialchars($zone_data['image']); ?>" />
    <input type="text" class="form-control border-0" disabled value="<?php echo $this->theme_url ?>images/"/>
    <input type="file" name="image[<?php echo $zone -1 ; ?>]" id="offers_recommended_zone_<?php echo $zone; ?>_image_upload" class="form-control" accept="image/gif, image/jpeg, image/png" />
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>