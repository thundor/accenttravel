<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/special_assistance.php'); 
$selected_values = isset($data['selected']) ? $data['selected'] : array();
foreach($this->special_assistance_selections as $k=>$v){ ?>
  <option value="<?php echo htmlspecialchars($k); ?>" <?php echo in_array($k,$selected_values) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($v); ?></option>
<?php
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>