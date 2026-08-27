<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/countries.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
$with_prefix = isset($data['with_prefix']) ? $data['with_prefix'] : false;
if(isset($this->countries_selections[$selected_value])){
  $v = $this->countries_selections[$selected_value];
  $k = $selected_value;
  ?>
  <option value="<?php echo htmlspecialchars($k); ?>" <?php echo $selected_value == $k ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($v->text . ($with_prefix ? ' (' . $v->prefix . ')' : '')); ?></option>
<?php
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>