<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');
$is_backend = preg_match('#^/(backend|actmanager241)(/|$)#', $_SERVER['REQUEST_URI']);
// $config['trip_no_booking'] = !$is_backend && !empty($_GET['newux']);
$config['trip_no_booking'] = false;
$config['trip_24_pay'] = defined('PAY24') && PAY24;
$config['theme_newux'] = !$is_backend && !empty($_GET['newux']);
$config['base_url'] = 'https://accenttravel.ro/';
$config['index_page'] = '';
$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language'] = 'ro';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = TRUE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.,:_\-\'';
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd'; // experimental not currently in use
$config['log_threshold'] = 2;
$config['log_path'] = APPPATH.'logs/';
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = APPPATH.'modules/Error/views/';
$config['tmp_path'] = APPPATH.'tmp/';
$config['cache_path'] = APPPATH.'cache/';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = '73L1DmftGB1OOCYOTb3KPPhAJJz8J5jiH';
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 0;
$config['sess_save_path'] = APPPATH . 'cache/sessions/';
$config['sess_expire_on_close'] = FALSE;
$config['sess_encrypt_cookie'] = FALSE;
$config['sess_use_database'] = TRUE;
$config['sess_table_name'] = 'pt_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_match_useragent'] = TRUE;
$config['sess_time_to_update'] = 86400;
$config['sess_regenerate_destroy'] = FALSE;
$config['cookie_prefix'] = "";
$config['cookie_domain'] = "";
$config['cookie_path'] = "/";
$config['cookie_secure'] = FALSE;
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = !(defined('PAY24') && PAY24 && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'com.twentyfourpay.dev' === $_SERVER['HTTP_X_REQUESTED_WITH']);

$config['captcha_validate_page'] = $config['csrf_protection'];

// if($_SERVER['REQUEST_METHOD'] == 'POST'){
	// $status = '404 Not Found';
    // header("HTTP/1.1 $status");
	// echo json_encode($_SERVER['HTTP_X_REQUESTED_WITH'] . ' ' . $config['csrf_protection']);
	// echo '<pre>';
	// print_r($_SERVER);
	// die;
// }
$ip = md5(microtime(true));
if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif(isset($_SERVER['REMOTE_ADDR'])){
  $ip = $_SERVER['REMOTE_ADDR'];
}
if($ip == '82.76.174.47'){
	// echo '<pre>';
	// print_r();
	// die;
	// $config['csrf_protection'] = false;
}
$useragent = crc32(microtime(true));
if(isset($_SERVER['HTTP_USER_AGENT'])){
  $useragent = $_SERVER['HTTP_USER_AGENT'];
}

$config['csrf_token_name'] = 'csrf_' . md5(crc32(md5($ip . $useragent . $config['encryption_key'])));
$config['csrf_cookie_name'] = 'csrf_' . md5(crc32(md5($useragent . $ip . $config['encryption_key']))) ;

$config['captcha_exclude_uris'] = array(
  'trip/checkout/\w+/ipn',
  'epay/endpoint',
);
$config['csrf_exclude_uris'] = array(
  'trip/checkout/\w+/ipn',
  'backend/file_manager/files',
  'backend/file_manager/upload',
  'trimite-formular',
  'forms/submit',
  'epay/endpoint',
);
$config['csrf_expire'] = 7200;
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
$config['is_offline'] = TRUE;
$config['modules_locations'] = array(
  APPPATH . 'modules/' => '../modules/',
);
