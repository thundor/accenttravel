<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/special_assistance.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
foreach($this->special_assistance_selections as $k=>$v){ ?>
  <option value="<?php echo htmlspecialchars($k); ?>" <?php echo $selected_value == $k ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($v); ?></option>
<?php
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>