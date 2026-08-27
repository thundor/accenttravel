<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/genders.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
$id_prefix = isset($data['id_prefix']) ? $data['id_prefix'] : 'gender_';
$name = isset($data['name']) ? $data['name'] : 'gender';
$required = isset($data['required']) ? $data['required'] : false;
?>
<div class="custom-controls-stacked d-inline-block">
<?php
foreach($this->genders_selections as $k=>$v){ ?>
<label class="custom-control custom-radio">
  <input id="<?php echo $id_prefix; ?><?php echo htmlspecialchars($k); ?>" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($k); ?>" type="radio" class="custom-control-input" <?php echo $required ? 'required' : ''; ?> <?php echo $selected_value == $k ? 'checked' : ''; ?>>
  <span class="custom-control-indicator"></span>
  <span class="custom-control-description"><?php echo htmlspecialchars($v); ?></span>
</label>
<?php
}
?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>