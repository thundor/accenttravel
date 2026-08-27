<?php
defined('ENVIRONMENT') OR die('Invalid access');
$layout = $this->config('layout');
$sublayout = $this->config('sublayout');
if($this->config('sublayout')){
  themeFunctions::loadLayout($this->config('sublayout'),__FILE__);
}
$sublayout_class = str_replace('/','_',substr(strstr($sublayout,"/"), 1));
if (empty($this->detect)){
  $this->_ci->load->library('Mobile_Detect');
  $this->detect = new Mobile_Detect();
}
$this->_ci->session->set_userdata('log/trip/flights/search_data', null);
$this->_ci->load->model('TripLog_model');
$this->_ci->TripLog_model->saveLog([
	'search_data' => null,
	'url' => (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
]);

$this->_ci->load->model('Options_model');
$this->general_settings = $this->_ci->Options_model->get('general_settings');

$layout_class = str_replace('/','_',$layout);
ob_start();
// themeFunctions::loadModule('helpers/recaptcha','captcha');
include 'includes/body.php';
$body = ob_get_contents();
ob_end_clean();

$html_file_path = dirname(themeFunctions::dirPath(themeFunctions::$absolute_theme_path . 'layouts' . '/' . $this->config('sublayout') . '.php')) . '/html.php';
if($this->config('sublayout') && file_exists($html_file_path)){
	include $html_file_path;
} else {
// if($this->config('sublayout') && is_file(themeFunctions::$absolute_theme_path . ))
?><!DOCTYPE html>
<html ng-app="accentTravelApp" class="l-<?php echo $layout_class; ?> sl-<?php echo $sublayout_class; ?> fill-height" style="overflow:hidden;">
  <head>
    <?php include 'includes/head.php'; ?>
  </head>
  <body class="fill-height prevent-select" style="position:absolute; overflow:hidden;width:100%;">
    <?php echo $body; ?>
  </body>
</html><?php 
}