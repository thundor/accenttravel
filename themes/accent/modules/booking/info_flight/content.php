<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model'); 
$flight_info_status = $this->_ci->Options_model->get('trip_flight_info','status');
if($flight_info_status){
  $flight_info_description = $this->_ci->Options_model->get('trip_flight_info','description');
  echo $flight_info_description;
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>