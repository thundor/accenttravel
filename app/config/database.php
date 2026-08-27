<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => "192.168.128.13",
	'username' => "accenttravel_site",
	'password' => "WrIv5tR+P3as",
	'database' => "accenttravel_site",
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production') || $ip == '82.76.174.47',
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
