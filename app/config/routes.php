<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

$route['actmanager241'] = "backend/allowlogin";
$route['default_controller'] = "home";
$route['404_override'] = 'cms/page/not_found';
$route['translate_uri_dashes'] = FALSE;
$route['sitemap.xml'] = 'sitemap';
// $route['trip/hotels'] = 'trip/hotelsasync';
// $route['trip/(hotel|package|citybreak)/(:num)'] = 'trip/$1/index/$2';
// $route['hoteluri'] = 'trip/hotels';
// $route['zboruri'] = 'trip/flights';
// $route['pachete'] = 'trip/packages';
// $route['citybreak'] = 'trip/citybreaks';
// $route['profil'] = 'account/profile';
// $route['deconectare'] = 'account/logout';
$route['trimite-formular'] = 'forms/submit';
$route['trimite-cerere-de-oferta-personalizata'] = 'trip/requestoffer/custom';

include __DIR__ . "/promoted_hotels.php";

if(isset($custom_routes)){
	foreach($custom_routes as $cr_alias => $cr_route){
		$route[$cr_alias] = $cr_route;
	}
}