<?php
defined('ENVIRONMENT') OR die('Invalid access');
$layout = $this->config('layout');
$sublayout = $this->config('sublayout');
$sublayout_class = str_replace('/','_',substr(strstr($sublayout,"/"), 1));
if($this->config('sublayout')){
  themeFunctions::loadLayout($this->config('sublayout'),__FILE__);
}

$this->_ci->load->model('Options_model');
$this->general_settings = $this->_ci->Options_model->get('general_settings');

$layout_class = str_replace('/','_',$layout);
ob_start();
include 'includes/body.php';
$body = ob_get_contents();
ob_end_clean();
?><!DOCTYPE html>
<html class="l-<?php echo $layout_class; ?> sl-<?php echo $sublayout_class; ?>">
  <head>
    <?php include 'includes/head.php'; ?>
  </head>
  <body>
    <?php echo $body; ?>
  </body>
</html>