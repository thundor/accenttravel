<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('cleanArray')) {
  // Clean array of empty data
  function cleanArray(&$data){
    foreach($data as $k => &$v){
      if(is_array($v)){
        cleanArray($v);
        if(empty($v)){
          unset($data[$k]);
          continue;
        }
      }
      if(!isset($data[$k])){
        unset($data[$k]);
      }
    }
  }
}
if(!function_exists('dlog')){
	function dlog(){
		echo date("Y-m-d H:i:s");
		foreach(func_get_args() as $arg){
			echo ' ' . htmlspecialchars(print_r($arg, true));
		}
		echo '<br/>';
		flush();ob_flush();
	}
}
if ( ! function_exists('pr')) {
	function pr(){
		echo '<pre>';
		foreach(func_get_args() as $arg){
			echo print_r($arg, true);
		}
		echo '</pre>';
		flush();ob_flush();
	}
}
if ( ! function_exists('dump')) {
	function dump(){
		echo '<pre>';
		foreach(func_get_args() as $arg){
			echo htmlspecialchars(print_r($arg, true));
		}
		echo '</pre>';
		flush();ob_flush();
	}
}
if ( ! function_exists('prd')) {
	// Clean array of empty data
	function prd(){
		pr(...func_get_args());
		die;
	}
}
if ( ! function_exists('dd')) {
	// Clean array of empty data
	function dd(){
		dump(...func_get_args());
		die;
	}
}

if ( ! function_exists('is_internal_ip')) {
  /**
   * True for private / reserved ranges (LAN, localhost, link-local).
   * Uses ONLY REMOTE_ADDR by default — never X-Forwarded-For / Client-IP (spoofable).
   */
  function is_internal_ip($ip = '') {
    if ($ip === '' || $ip === null) {
      $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    $ip = trim((string) $ip);
    if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
      return false;
    }
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
  }
}

if ( ! function_exists('ensure_human_for_internal_ip')) {
  /**
   * For LAN / localhost: set is_human session + cookie so captcha gate passes.
   * @return bool true if internal IP was handled
   */
  function ensure_human_for_internal_ip($CI = null) {
    if (!is_internal_ip()) {
      return false;
    }
    if (!$CI) {
      $CI =& get_instance();
    }
    $CI->load->helper(array('cookie', 'string'));
    $existing = $CI->session->userdata('is_human');
    $cookie = get_cookie('is_human');
    if ($existing && $cookie && $existing === $cookie) {
      return true;
    }
    $human_token = $existing ?: random_string();
    $CI->session->set_userdata('is_human', $human_token);
    set_cookie('is_human', $human_token, 86400);
    // Available on current request (set_cookie only affects the response header)
    $_COOKIE['is_human'] = $human_token;
    return true;
  }
}

if ( ! function_exists('gzCompressFile')) {
function gzCompressFile($source, $level = 9, $destfolder=null){
    $dest = $source . '.gz'; 
	if(isset($destfolder)){
		$dest = rtrim($destfolder,'/') . '/' . basename($source) . '.gz';
	}
    $mode = 'wb' . $level; 
    $error = false; 
    if ($fp_out = gzopen($dest, $mode)) { 
        if ($fp_in = fopen($source,'rb')) { 
            while (!feof($fp_in)) 
                gzwrite($fp_out, fread($fp_in, 1024 * 512)); 
            fclose($fp_in); 
        } else {
            $error = true; 
        }
        gzclose($fp_out); 
    } else {
        $error = true; 
    }
    if ($error)
        return false; 
    else
        return $dest; 
}
}