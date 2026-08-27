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

$this->_ci->load->model('Options_model');
$this->general_settings = $this->_ci->Options_model->get('general_settings');

$layout_class = str_replace('/','_',$layout);
if('index' === $layout_class && 'index' === $sublayout_class){
// if(defined('TUDORTESTING') && TUDORTESTING){
	include_once(__DIR__ . '/verify.php');
// }
	themeFunctions::loadModule('helpers/recaptcha','captcha');
}
ob_start();


include 'includes/body.php';
$body = ob_get_contents();
ob_end_clean();
?><!DOCTYPE html>
<html ng-app="accentTravelApp" class="l-<?php echo $layout_class; ?> sl-<?php echo $sublayout_class; ?>">
  <head>
    <?php include 'includes/head.php'; ?>
  </head>
  <body>
    <?php echo $body; ?>
  </body>
</html>