<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/preferred_spots.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
foreach($this->preferred_spots_selections as $k=>$v){ ?>
  <option value="<?php echo htmlspecialchars($k); ?>" <?php echo $selected_value == $k ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($v); ?></option>
<?php
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>