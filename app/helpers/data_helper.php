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