<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/social_networks.php'); 
$selected_values = isset($data['selected']) ? (array)$data['selected'] : array();
$id_prefix = isset($data['id_prefix']) ? $data['id_prefix'] : 'social_';
$name = isset($data['name']) ? $data['name'] : 'social_networks';
$required = isset($data['required']) ? $data['required'] : false;
$form = isset($data['form']) ? $data['form'] : false;
?>
<div class="custom-controls-stacked d-block">
<?php
foreach($this->social_networks_selections as $k=>$a){
  $v = $a['text'];
  $icon = isset($a['icon']) ? $a['icon'] . ' ' : '';
  ?>
<label class="custom-control custom-checkbox">
  <input <?php echo $form ? 'form="' . $form . '"' : ''; ?> id="<?php echo $id_prefix; ?>_<?php echo htmlspecialchars($k); ?>" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($k); ?>" type="checkbox" class="custom-control-input" <?php echo $required ? 'required' : ''; ?> <?php echo in_array($k,$selected_values) ? 'checked' : ''; ?>>
  <span class="custom-control-indicator"></span>
  <span class="custom-control-description"><?php echo $icon . htmlspecialchars($v); ?></span>
</label>
<?php
}
?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>