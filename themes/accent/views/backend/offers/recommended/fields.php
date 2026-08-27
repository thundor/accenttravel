<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$zone_data = array(); 
$zone_data['status'] = isset($data['status'], $data['status'][$zone-1]) ? $data['status'][$zone-1] : 0;
$zone_data['title'] = isset($data['title'], $data['title'][$zone-1]) ? $data['title'][$zone-1] : '';
$zone_data['discount'] = isset($data['discount'], $data['discount'][$zone-1]) ? $data['discount'][$zone-1] : 0;
$zone_data['no'] = isset($data['no'], $data['no'][$zone-1]) ? $data['no'][$zone-1] : 0;
$zone_data['icons'] = isset($data['icons'], $data['icons'][$zone-1]) ? $data['icons'][$zone-1] : '';
$zone_data['image'] = isset($data['image'], $data['image'][$zone-1]) ? $data['image'][$zone-1] : '';
$zone_data['url'] = isset($data['url'], $data['url'][$zone-1]) ? $data['url'][$zone-1] : '';
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
  <label for="offers_recommended_zone_<?php echo $zone; ?>_title" class="<?php echo $label_class; ?>">Titlu:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="offers_recommended_zone_<?php echo $zone; ?>_title" name="data[title][<?php echo $zone -1 ; ?>]" type="text" placeholder="Titlu" class="form-control" value="<?php echo htmlspecialchars($zone_data['title']); ?>" />
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_discount" class="<?php echo $label_class; ?>">Discount:</label>
  <div class="<?php echo $value_class; ?>">
    <label class="input-group mb-0">
      <input id="offers_recommended_zone_<?php echo $zone; ?>_discount" name="data[discount][<?php echo $zone -1 ; ?>]" type="number" min="0" max="100" step="0.01" placeholder="Discount" class="form-control pr-0 pb-0 pt-0" style="line-height:35px;" value="<?php echo htmlspecialchars($zone_data['discount']); ?>" />
      <span class="input-group-addon">%</span>
    </label>
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_no" class="<?php echo $label_class; ?>">Numar oferte:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="offers_recommended_zone_<?php echo $zone; ?>_no" name="data[no][<?php echo $zone -1 ; ?>]" type="number" min="0" max="999999" step="0.01" placeholder="no" class="form-control pr-0 pb-0 pt-0" style="line-height:35px;" value="<?php echo htmlspecialchars($zone_data['no']); ?>" />
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_icons" class="<?php echo $label_class; ?>">Iconite:</label>
  <div class="<?php echo $value_class; ?>">
    <label class="input-group mb-0">
      <input id="offers_recommended_zone_<?php echo $zone; ?>_icons" type="hidden" name="data[icons][<?php echo $zone -1 ; ?>]" class="fontawesome-icons-select" value="<?php echo htmlspecialchars($zone_data['icons']); ?>"/>
    </label>
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_url" class="<?php echo $label_class; ?>">URL:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="offers_recommended_zone_<?php echo $zone; ?>_url" name="data[url][<?php echo $zone -1 ; ?>]" type="text" placeholder="URL" class="form-control" value="<?php echo htmlspecialchars($zone_data['url']); ?>" />
  </div>
</div>
<div class="form-group row">
  <label for="offers_recommended_zone_<?php echo $zone; ?>_image" class="<?php echo $label_class; ?>">Imagine:</label>
  <div class="<?php echo $value_class; ?>">
    <input id="offers_recommended_zone_<?php echo $zone; ?>_image" name="data[image][<?php echo $zone -1 ; ?>]" type="text" placeholder="Imagine" class="form-control" value="<?php echo htmlspecialchars($zone_data['image']); ?>" />
    <input type="text" class="form-control border-0" disabled value="<?php echo $this->theme_url ?>images/"/>
    <input type="file" name="image[<?php echo $zone -1 ; ?>]" id="offers_recommended_zone_<?php echo $zone; ?>_image_upload" class="form-control" accept="image/gif, image/jpeg, image/png" />
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>