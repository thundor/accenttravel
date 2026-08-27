<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TRIP {
  static $max_generates = 5;
  protected $use_api = false;
  // protected $use_api = false;
  /**
   * The API endpoint url
   * @var string 
   */
  protected $api_url;

  /**
   * The Application ID
   * @var string 
   */
  protected $app_id;

  /**
   * The Application Secret key
   * @var string 
   */
  protected $secret_key;
  
  /**
   * The Application Username
   * @var string 
   */
  protected $username;
  
  /**
   * The Application Password
   * @var string 
   */
  protected $password;
  
  /**
   * The Logged-In User ID
   * @var string 
   */
  protected $login_id;

  /**
   * API language url
   * @var string 
   */
  protected $lang;

  /**
   * The Token used for secured api calls
   * @var string 
   */
  protected $auth_Value;

  /**
   * TTL (token time to live in seconds). After that specific period of time, you need to make a refresh token request;
   * @var int 
   */
  protected $auth_TTL;

  /**
   * number of lives, that represents the number of requests you can make with this token before you need to make a refresh token request.
   * @var int 
   */
  protected $auth_Lifes;

  /**
   * The timestamp when the token has been generated
   * @var int 
   */
  protected $auth_Time;
  /**
   * The session Object
   * @var stdClass 
   */
  protected $session;
  /**
   * The session save path
   * @var string
   */
  protected $session_path;
  protected $acc_id;

  /**
   * Constructor
   * @param string $api_url API endpoint URL
   * @param string $app_id Application ID
   * @param string $secret_key Application Secret Key
   * @param string $lang Application language (e.g. en)
   * @param string $auth_Value Authorization Token
   * @param int $auth_TTL Authorization TTL
   * @param int $auth_Lifes Authorization lives
   * @param int $auth_Time Authorization generation timestamp
   */
  public function __construct($api_url, $app_id, $secret_key, $username, $password, $lang = 'en', $auth_Value = '', $auth_TTL = 0, $auth_Lifes = 0, $auth_Time = 0, $acc_id = null) {
    assert(is_string($api_url) && filter_var($api_url, FILTER_VALIDATE_URL), 'The api_url parameter is mandatory and must be a valid URL');
    assert(is_string($app_id) && ($app_id !== ''), 'The app_id parameter is mandatory');
    assert(is_string($secret_key) && ($secret_key !== ''), 'The secret_key parameter is mandatory');
    assert(is_string($username) && ($username !== ''), 'The username parameter is mandatory');
    assert(is_string($password) && ($password !== ''), 'The password parameter is mandatory');
    assert(is_string($lang) && ($lang !== ''), 'The lang parameter is mandatory');
    assert(is_string($auth_Value), 'The auth_Value parameter must be a string');
    assert(is_numeric($auth_TTL) && (abs($auth_TTL * 1) === $auth_TTL), 'The auth_TTL parameter must be a positive integer');
    assert(is_numeric($auth_Lifes) && (abs($auth_Lifes * 1) === $auth_Lifes), 'The auth_Lifes parameter must be a positive integer');
    assert(is_numeric($auth_Time) && (abs($auth_Time * 1) === $auth_Time), 'The auth_Lifes parameter must be a positive integer');

    $this->api_url = $api_url;
    $this->app_id = $app_id;
    $this->secret_key = $secret_key;
    $this->username = $username;
    $this->password = $password;
    $this->lang = $lang;
    $this->auth_Value = $auth_Value;
    $this->auth_TTL = $auth_TTL;
    $this->auth_Lifes = $auth_Lifes;
    $this->auth_Time = $auth_Time;
	if(!isset($acc_id)){
		if(defined('PAY24') && PAY24){
			$acc_id = 1;
		}
	}
    $this->acc_id = $acc_id;
  }

  public function getAccountId() {
	  return $this->acc_id;
  }
  public function getSecretKey() {
	  return $this->secret_key;
  }
  /**
   * LogIn user
   * @return boolean
   */
  private $logins = 0;
  private $logouts = 0;
  public function logIn() {
	  if($this->logins >= 1) return false;
	  $this->logins++;
    $response = $this->call('index.php/auth/login', array(), array(
      'Username' => $this->username,
      'Password' => $this->password,
    ), true, true);
    if ($response && $response->Status == 1) {
      $this->login_id = $response->Id;
      $this->saveSession();
      return true;
    }
    $this->saveSession();
    return false;
  }
  
  /**
   * LogOut user
   * @return boolean
   */
  public function logOut() {
	  // if($this->logouts >= 1) return false;
	  // $this->logouts++;
    $response = $this->call('index.php/auth/logout', array(), array(), true, true);
    if ($response) {
      $this->login_id = null;
      $this->saveSession();
      return true;
    }
    $this->saveSession();
    return false;
  }

  /**
   * Check if user is logged in
   * @return boolean
   */
  public function isLoggedIn($force = false) {
    if(!$this->login_id){
      return false;
    } 
    elseif(!$force){
      return true;
    }
    $response = $this->call('index.php/auth/login/' . $this->login_id, array(), array(), true, false);
	// if(php_sapi_name() == 'cli'){
		// echo '<pre>';
		// print_r($response);
		// die;
	// }
    if ($response) {
      $this->login_id = $response->Id;
      $this->saveSession();
      return true;
    }
    $this->login_id = null;
    $this->saveSession();
    return false;
  }

  /**
   * Generate Authorization token
   * @return boolean
   */
  public function generateToken($internal = false) {
    if($internal && (--static::$max_generates <= 0)){
      // log_message('DEBUG', __FILE__ . ':' . __LINE__ . ' TOO MANY GENERATES');
      return false;
    }
    if($this->use_api){
      return true;
    }
    $timestamp = time();
    $this->auth_Value = '';
    $this->auth_TTL = 0;
    $this->auth_Lifes = 0;
    $this->auth_Time = 0;
    $response = $this->call('index.php/' . $this->lang . '/authentication/token/generate', array('timestamp' => $timestamp));
    if ($response && $response->Status == 1) {
      $auth_obj = $response->Object;
      $this->auth_Value = $auth_obj->Value;
      $this->auth_TTL = $auth_obj->TTL;
      $this->auth_Lifes = $auth_obj->Lifes;
      $this->auth_Time = $timestamp;
      $this->saveSession();
      return true;
    }
    return false;
  }

  /**
   * Re-generate Authorization token
   * @return boolean
   */
  public function refreshToken($internal = false) {
    if($this->use_api){
      return true;
    }
    if (!$this->auth_Lifes) {
      return $this->generateToken($internal);
    }
    if($internal && (--static::$max_generates <= 0)){
      // log_message('DEBUG', __FILE__ . ':' . __LINE__ . ' TOO MANY GENERATES');
      return false;
    }
    $timestamp = time();
    $response = $this->call('index.php/' . $this->lang . '/authentication/token/refresh', array('timestamp' => $timestamp));
    if ($response && $response->Status == 1) {
      $auth_obj = $response->Object;
      $this->auth_Value = $auth_obj->Value;
      $this->auth_TTL = $auth_obj->TTL;
      $this->auth_Lifes = $auth_obj->TTL;
      $this->auth_Time = $timestamp;
      $this->saveSession();
      return true;
    }
    return false;
  }

  /**
   * refreshToken alias - Re-generate Authorization token
   */
  public function regenerateToken($internal = false) {
    return $this->refreshToken($internal);
  }

  /**
   * Check current token's availability
   * @param int $time_tol (Tolerance seconds) If set to e.g. "5", if the current token expires within 5 seconds, then refresh it
   * @param int $life_tol (Tolerance lives) If set to e.g. "5", if the current token has 5 lives left, then refresh it
   */
  public function isTokenExpired($time_tol = 0, $life_tol = 0) {
    if ($this->auth_Time + $this->auth_TTL - $time_tol < time()) {
      return true;
    }
    if ($this->auth_Lifes - $life_tol < 1) {
      return true;
    }
    return false;
  }

  /**
   * Safe call API with login necessary
   */
  public function loginApiCall($path, $get = array(), $post = array(), $decode_response=true, $send_as_post = false) {
    return $this->apiCall($path, $get, $post, $decode_response, $send_as_post, true);
  }
  /**
   * Safe call API. Auto-(re)generates token when necessary.
   * @param string $path Path to be appended to the request URL
   * @param array $get $_GET parameters sent in request
   * @param array $post $_POST parameters sent in request
   * @return stdClass The entire object returned by the API
   */
  public function apiCall($path, $get = array(), $post = array(), $decode_response=true, $send_as_post = false, $require_login=false) {
    if ($this->isTokenExpired(10, 5) && !$this->refreshToken()) {
      return false;
    }
    if($require_login && !$this->isLoggedIn()){
      $logged_in = $this->logIn();
      // if(!$logged_in){
        // return false;
      // }
    }
    return $this->call($path, $get, $post, $decode_response, $send_as_post, $require_login);
  }
  public function setSession(&$session, $session_path, $load=true) {
    $this->session = $session;
    $this->session_path = $session_path;
    // if(isset($_GET['testt'])){
      // if($_GET['testt']){
        // $this->use_api = true;
      // } else {
        // $this->use_api = false;
      // }
    // }
    if(!$load){
      return;
    }
    $this->loadFromSession();
  }
  public function loadFromSession() {
    $data = $this->session->userdata($this->session_path);
    if(!$data){
      return;
    }
    $this->auth_Value = $data['value'];
    $this->auth_TTL = $data['ttl'];
    $this->auth_Lifes = $data['lifes'];
    $this->auth_Time = $data['time'];
    $this->login_id = isset($data['login_id']) ? $data['login_id'] : null;
    // $this->use_api = isset($data['use_api']) && isset($data['use_api']);
    // if(isset($_GET['testt'])){
      // if($_GET['testt']){
        // $this->use_api = true;
      // } else {
        // $this->use_api = false;
      // }
    // }
  }
  public function setUseApi($state) {
    $this->use_api = $state ? true : false;
  }
  public function saveSession() {
    if(!$this->session){
      return;
    }
    $data = array(
      'value' => $this->auth_Value,
      'ttl' => $this->auth_TTL,
      'lifes' => $this->auth_Lifes,
      'time' => $this->auth_Time,
      'login_id' => $this->login_id,
      // 'use_api' => $this->use_api ? 1 : 0,
    );
    $this->session->set_userdata($this->session_path,$data);
  }

  /**
   * Call API
   * @param string $path Path to be appended to the request URL
   * @param array $get $_GET parameters sent in request
   * @param array $post $_POST parameters sent in request
   * @return stdClass The entire object returned by the API
   */
  public $calls = array();
  public $cookies = array();
  public $call;
  public function call($path, $get = array(), $post = array(), $decode_response=true, $send_as_post = false, $require_login = false) {
	
	/* if(php_sapi_name() == 'cli'){
		echo '<pre>';
		print_r(__METHOD__);
		print_r(func_get_args());
		// die;
		echo '</pre>';
	} */
	  
    $_post = $post;
    ksort($_post);
    $_get = $get;
    if(!$this->use_api){
      $_get['timestamp'] = isset($_get['timestamp']) ? $_get['timestamp'] : time();
      $_get['applicationId'] = $this->app_id;
    }

    $post_string = http_build_query($_post);
    $get_string = http_build_query($_get);
    $ch = curl_init();

    $url_append = '?';
    $url = $this->api_url;
    // if($this->use_api){
      // if(strpos($url,'https')===0){
        // $url = 'http' . substr($url,5);
      // }
    // }
	if(false !== strpos($path, '?')){
		$url_append = '&';
	}
    if($this->use_api && strpos($path,'index.php')===0){
      $path = 'api.php' . substr($path,9);
    }
    $url .= $path;
    if ($get_string) {
      $url .= $url_append . $get_string;
      $url_append = '&';
    }
    $full_url = urldecode($url . ($post_string ? $url_append . $post_string : ''));
    $x_hash = sha1($full_url . $this->secret_key);

    $header = array();
    if(!$this->use_api){
      $header[] = "Accept: application/json, text/javascript, */*; q=0.01";
      $header[] = "x-hash: " . $x_hash;
      $header[] = "Authorization: " . $this->auth_Value;
      $header[] = "X-Requested-With: XMLHttpRequest";
    }
	if($this->acc_id){
      $header[] = "x-accid: " . $this->acc_id;
	}
    curl_setopt($ch, CURLOPT_URL, $url);
    $send_type = 'GET';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if (true === $send_as_post || (!empty($post_string) && is_bool($send_as_post))) {
      $send_type = 'POST';
      curl_setopt($ch, CURLOPT_POST, true);
    } elseif($send_as_post && is_string($send_as_post)){
      $send_type = $send_as_post;
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $send_as_post);
    }
    if (!empty($post_string)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if($this->use_api){
      curl_setopt($ch, CURLOPT_HEADER, 1);
    }
    // curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie-name');  //could be empty, but cause problems on some hosts
    // curl_setopt($ch, CURLOPT_COOKIEFILE, APPPATH . 'tmp/' . $x_hash);
    
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	if(php_sapi_name() == 'cli'){
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	} else {
		curl_setopt($ch, CURLOPT_TIMEOUT, 240);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	}
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $cookies = array();
	if(php_sapi_name() == 'cli'){
		foreach ($this->cookies as $key => $value){
		  if ($key != 'Array'){
			$cookies[] = $key . '=' . $value;
		  }
		}
	} else {
		foreach ($_COOKIE as $key => $value){
		  if ($key != 'Array'){
			$cookies[] = $key . '=' . $value;
		  }
		}
	}
    $cookies[] = 'PHPSESSID=' . session_id();
    $sent_cookies = $cookies;
    curl_setopt( $ch, CURLOPT_COOKIE, implode(';', $cookies) );

    $start_date = date('Y-m-d H:i:s');
    $start_time = microtime(true);
    // log_message('DEBUG', __FILE__ . ':' . __LINE__ . ' CURL BEGIN: ' . $url);
    $result = curl_exec($ch);
    $received_cookies = array();
	$headers = '';
    if($this->use_api){
      if($result){
        $res = explode("\r\n\r\n", $result);
        // Seperate header and body
        $result = array_pop($res);
        $headers = implode('',$res);

        // extract cookies form curl and forward them to browser
		if(php_sapi_name() == 'cli'){
			preg_match_all('/^Set-Cookie:\s*([^\n=]+)=([^\n]+)$/mi', $headers, $cookies);
			// echo '<pre>';
			// print_r('$headers&$cookies');
			// print_r($headers);
			// print_r($cookies);
			// echo '</pre>';
			// die;
			$received_cookies = $cookies;
			$this->cookies = [];
			foreach($cookies[1] AS $ck=>$cookie_name){
			   $this->cookies[$cookie_name] =$cookies[2][$ck];
			}
		} else {
			preg_match_all('/^(Set-Cookie:\s*[^\n]*)$/mi', $headers, $cookies);
			// echo '<pre>';
			// print_r($cookies);
			// die;
			$received_cookies = $cookies;
			foreach($cookies[0] AS $cookie){
			   header($cookie, false);
			}
		}
      }
    }
    $end_date = date('Y-m-d H:i:s');
    $end_time = microtime(true);
    $duration_time = $end_time - $start_time;
    // log_message('DEBUG', __FILE__ . ':' . __LINE__ . ' CURL END: ' . $url);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = false;
    if (in_array($http_code, array(200,201,204))) {
      $response = $result;
    } elseif ($http_code == 511) {
      if($this->generateToken(true)){
        return $this->call($path, $get, $post, $decode_response, $send_as_post, $require_login);
      } else {
        $response = $result;
      }
    } elseif ($http_code == 403) {
      if(!$this->logins && $this->logOut() && $this->logIn()){
        return $this->call($path, $get, $post, $decode_response, $send_as_post, false);
      }
      else {
        $response = $result;
      }
    }
    
    if($decode_response && false !== $response){
      $response = json_decode($response, $decode_response === 'assoc');
    }
    $object = new stdClass;
    $object->send_type = $send_type;
    $object->url_path = $path;
    $object->get_string = $get_string;
    $object->http_code = $http_code;
    $object->response = $response;
    $object->api_url = $this->api_url;
    $object->get = $_get;
    $object->post = $_post;
    $object->url = $url;
    $object->post_string = $post_string;
    $object->url_full = $full_url;
    $object->result = $result;
    $object->start_date = $start_date;
    $object->end_date = $end_date;
    $object->duration_time = $duration_time;
    $object->result_decoded = json_decode($result);
    $object->headers = $header;
    $object->headers2 = $headers;
    $object->sent_cookies = $sent_cookies;
    $object->received_cookies = $received_cookies;
    $this->call = &$object;
    $this->calls[] = $this->call;
    return $response;
  }
}