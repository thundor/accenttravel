<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$zone_data = array(); 
$zone_data['status'] = isset($data['status'], $data['status'][$zone-1]) ? $data['status'][$zone-1] : 0;
$zone_data['image'] = isset($data['image'], $data['image'][$zone-1]) ? $data['image'][$zone-1] : '';
?>
<div class="form-group row">
  <label for="sliders_homebackground_zone_<?php echo $zone; ?>_status_active" class="<?php echo $label_class; ?>">Status:</label>
  <div class="<?php echo $value_class; ?>">
    <div class="i-checks">
      <input id="sliders_homebackground_zone_<?php echo $zone; ?>_status_active" type="radio" value="1" name="data[status][<?php echo $zone -1 ; ?>]" <?php echo $zone_data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
      <label for="sliders_homebackground_zone_<?php echo $zone; ?>_status_active"><?php echo lang('option_active'); ?></label>
    </div>
    <div class="i-checks">
      <input id="sliders_homebackground_zone_<?php echo $zone; ?>_status_inactive" type="radio" value="0" name="data[status][<?php echo $zone -1 ; ?>]" <?php echo !$zone_data['status'] ? 'checked' : ''; ?> class="form-control-custom radio-custom">
      <label for="sliders_homebackground_zone_<?php echo $zone; ?>_status_inactive"><?php echo lang('option_inactive'); ?></label>
    </div>
  </div>
</div>
<div class="form-group row">
  <label for="sliders_homebackground_zone_<?php echo $zone; ?>_image" class="<?php echo $label_class; ?>">Imagine:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="sliders_homebackground_zone_<?php echo $zone; ?>_image" name="data[image][<?php echo $zone -1 ; ?>]" type="text" placeholder="Imagine" class="form-control" value="<?php echo htmlspecialchars($zone_data['image']); ?>" />
    <input type="text" class="form-control border-0" disabled value="<?php echo $this->theme_url ?>images/hphs/"/>
    <input type="file" name="image[<?php echo $zone -1 ; ?>]" id="sliders_homebackground_zone_<?php echo $zone; ?>_image_upload" class="form-control" accept="image/gif, image/jpeg, image/png" />
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>