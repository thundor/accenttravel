<?php
require_once(__DIR__ . '/flocker.php');
$newux_version = null;
if(is_file(__DIR__ . '/newux_version.txt')){
	$newux_version = file_get_contents(__DIR__ . '/newux_version.txt');
	if(!$newux_version || preg_match('~[^a-zA-Z0-9\-_\.]~', $newux_version)){
		$newux_version = null;
	}
}
define('NEWUX_VERSION', $newux_version ?? '1.0.2');
// if(!empty($_GET['force_ajax'])){
	// echo '<pre>';
// echo htmlspecialchars(print_r($_POST,true)); die;
// }
// if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
	// echo '<pre>';
	// print_r($_POST);	
	// die;
// }
// if(!empty($_GET['t'])){
	// echo '<pre>';
	// print_r(getallheaders());
	// print_r($_SERVER);
	// echo '</pre>';
	// die;
// }
// if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'com.twentyfourpay.dev' === $_SERVER['HTTP_X_REQUESTED_WITH']){
// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';
// die;
// }
if(!empty($_GET['newux']) && !(is_string($_GET['newux']) && !preg_match('/[^0-9\.]/', $_GET['newux']))){
	$_GET['newux'] = NEWUX_VERSION;
}
if(!empty($_COOKIE['newuxtheme'])){
	$_GET['newux'] = !empty($_GET['newux']) ? (string)$_GET['newux'] : NEWUX_VERSION;
}
if(!empty($_GET['testt'])){
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';
// die;
}
$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
define('IS_LISAL_IP', $ip == '82.76.174.47');
if (!function_exists('getallheaders')) {
    function getallheaders() {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
    }
}

$h = getallheaders();
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&$_SERVER['HTTP_X_REQUESTED_WITH'] == 'com.twentyfourpay.uat'){
	$_SERVER['HTTP_X_REQUESTED_WITH'] = 'com.twentyfourpay.dev';
}
if((isset($h['ok']) && $h['ok'] == 'iOS') || (isset($h['OK']) && $h['OK'] == 'iOS')){
	$_SERVER['HTTP_X_REQUESTED_WITH'] = 'com.twentyfourpay.dev';
}
// if(!empty($_GET['test'])){
	// ini_set('display_errors', 1);
// }
// if(php_sapi_name() == 'cli'){
	// echo '<pre>';
	// print_r($argv);
	// die;
// }
if(true && (!empty($_GET['pay24']) || !empty($_COOKIE['pay24theme']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'com.twentyfourpay.dev') || (isset($_SERVER['QUERY_STRING']) && 0 === strpos($_SERVER['QUERY_STRING'], '/pay24')) || (php_sapi_name() == 'cli' && $argv[1] == 'pay24') )){
	// define('ENVIRONMENT', 'development');
	define('ENVIRONMENT', 'production');
	define('PAY24', 1);
	$_SERVER['HTTP_X_REQUESTED_WITH'] = 'com.twentyfourpay.dev';
	// if(!empty($_GET['testtt'])){
		// var_dump(strpos($_SERVER['QUERY_STRING'], '/tester/checkout_auto')); die;
	// }
	if((isset($_SERVER['QUERY_STRING']) && (0 === strpos($_SERVER['QUERY_STRING'], '/pay24/ipn') || 0 === strpos($_SERVER['QUERY_STRING'], '/tester/checkout_auto')))){
		// define('NO_THEME_CHANGE', 1);
		// $_GET['test'] =1;
		// define('HV3', 1);
		// unset($_GET['test']);
	} else {
		// define('ENVIRONMENT', 'production');
	}
} else {
	define('ENVIRONMENT', 'production');
}
if(!(defined('PAY24') || php_sapi_name() == 'cli')){
	$_GET['newux'] = !empty($_GET['newux']) ? (string)$_GET['newux'] : NEWUX_VERSION;
}
/* if(!empty($_GET['testt'])){
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';
// die;
} */
if($_SERVER['REQUEST_METHOD'] !== 'GET' && php_sapi_name() != 'cli' && empty($_GET['newux'])){
	$key = $ip;
	FLocker::acquire($key);
	// $maxAcquire = 1;
	// $permissions =0666;
	// $autoRelease = 1;
	// $semaphore = sem_get(crc32($key), $maxAcquire, $permissions, $autoRelease);
	// sem_acquire($semaphore);
}
// if(isset($_GET['test'])){
	// echo '<pre>';
	// print_r($_SERVER);
	// die;
// }
	// define('ENVIRONMENT', 'development');
// } else {
	// define('ENVIRONMENT', 'production');
// }
	// define('ENVIRONMENT', 'production');
	// define('ENVIRONMENT', 'production');
// if(isset($_GET['dev'])){
	// define('ENVIRONMENT', 'development');
// } else {
	// define('ENVIRONMENT', 'maintenance');
// }

switch (ENVIRONMENT)
{
	case 'maintenance':
    include('index.html');
    exit;
	break;
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}
// if(php_sapi_name() == 'cli' || isset($_GET['dev'])){
if(isset($_GET['dev'])){
	error_reporting(-1);
		ini_set('display_errors', 1);
}
if($ip == '82.76.174.47'){
	define('TUDORTESTING', 1);
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
}
/* 
if($ip == '82.76.174.47'){
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
} */

/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" directory.
 * Set the path if it is not in the same directory as this file.
 */
	$system_path = __DIR__ . '/system';

/*
 *---------------------------------------------------------------
 * APPLICATION DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * directory than the default one you can set its name here. The directory
 * can also be renamed or relocated anywhere on your server. If you do,
 * use an absolute (full) server path.
 * For more info please see the user guide:
 *
 * https://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 */
	$application_folder = __DIR__ . '/app';

/*
 *---------------------------------------------------------------
 * VIEW DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view directory out of the application
 * directory, set the path to it here. The directory can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application directory.
 * If you do move this, use an absolute (full) server path.
 *
 * NO TRAILING SLASH!
 */
	$view_folder = __DIR__;


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 */
	// The directory name, relative to the "controllers" directory.  Leave blank
	// if your controller is not in a sub-directory within the "controllers" one
	// $routing['directory'] = '';

	// The controller class file name.  Example:  mycontroller
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath($system_path)) !== FALSE)
	{
		$system_path = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		// Ensure there's a trailing slash
		$system_path = strtr(
			rtrim($system_path, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system directory
	define('BASEPATH', $system_path);


	// Path to the front controller (this file) directory
	define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

	// Name of the "system" directory
	define('SYSDIR', basename(BASEPATH));

	// The path to the "application" directory
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = $_temp;
		}
		else
		{
			$application_folder = strtr(
				rtrim($application_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
	{
		$application_folder = BASEPATH.strtr(
			trim($application_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

	// The path to the "views" directory
	if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
	{
		$view_folder = APPPATH.'views';
	}
	elseif (is_dir($view_folder))
	{
		if (($_temp = realpath($view_folder)) !== FALSE)
		{
			$view_folder = $_temp;
		}
		else
		{
			$view_folder = strtr(
				rtrim($view_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
	{
		$view_folder = APPPATH.strtr(
			trim($view_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);
/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 */
require_once BASEPATH.'core/CodeIgniter.php';
// if(!empty($semaphore)){
	// sem_release($semaphore);
// }