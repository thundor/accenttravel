<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/countries_phone_prefixes.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
foreach($this->countries_phone_prefixes_selections as $k=>$v){ ?>
  <option value="<?php echo htmlspecialchars($k); ?>" <?php echo $selected_value == $k ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($v . ' (' . $k . ')'); ?></option>
<?php
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>