<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/titles.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
if(isset($this->titles_selections[$selected_value])){
  $v = $this->titles_selections[$selected_value];
  $k = $selected_value;
  ?>
  <option value="<?php echo htmlspecialchars($k); ?>" <?php echo $selected_value == $k ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($v); ?></option>
<?php
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>