<?php
/* 
if(!empty($_GET['test'])){
	if(preg_match('~^email/~', $this->view_tpl)){
		$this->set_theme('accent');
		include(dirname(__DIR__) . "/accent/index.php");
		$this->set_theme('newux');
		return;
	}
} */

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
/* $this->_ci->session->set_userdata('log/trip/flights/search_data', null);
$this->_ci->load->model('TripLog_model');
$this->_ci->TripLog_model->saveLog([
	'search_data' => null,
	'url' => (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
]);
 */
// $this->_ci->load->model('TravelFuse_model');
// $this->providers = $this->_ci->TravelFuse_model->getProviders();
// $this->countries = $this->_ci->TravelFuse_model->getCountries();
// $this->search_countries = $this->_ci->TravelFuse_model->searchCountries();
// $this->cities = $this->_ci->TravelFuse_model->getCities(['CountryId' => 126]);
// $this->search_cities = $this->_ci->TravelFuse_model->searchCities(['Country' => 126]);
// $this->hotels = $this->_ci->TravelFuse_model->getHotels(['CityCode' => 193, 'DestinationType' => 'county']);
// $this->hotels_details = $this->_ci->TravelFuse_model->getHotelsDetails(['HotelIds' => '81255,81276']);

// echo '<pre>';
// var_dump($this->countries);
// die;

$this->_ci->load->model('Options_model');
$this->general_settings = $this->_ci->Options_model->get('general_settings');

$layout_class = str_replace('/','_',$layout);

include(__DIR__ . '/../accent/verify.php');

ob_start();
// themeFunctions::loadModule('helpers/recaptcha','captcha');
include 'includes/body.php';
$body = ob_get_contents();
ob_end_clean();
$segments = $this->_ci->uri->segments;
// dd($segments);
$classes = [];
$rclass = '';
foreach($segments as $segment){
	if($rclass){
		$rclass .= '-';
	}
	$rclass .= $segment;
}
$classes[] = 'route-' . $rclass;

$html_file_path = dirname(themeFunctions::dirPath(themeFunctions::$absolute_theme_path . 'layouts' . '/' . $this->config('sublayout') . '.php')) . '/html.php';
if($this->config('sublayout') && file_exists($html_file_path)){
	include $html_file_path;
} else {
// if($this->config('sublayout') && is_file(themeFunctions::$absolute_theme_path . ))
?><!DOCTYPE html>
<html ng-app="accentTravelApp" class="l-<?php echo $layout_class; ?> sl-<?php echo $sublayout_class; ?> <?php echo implode(' ', $classes); ?>">
  <head>
    <?php include 'includes/head.php'; ?>
  </head>
  <body>
    <?php echo $body; ?>
  </body>
</html><?php 
}