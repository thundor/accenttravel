<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('getCacheAbsPath')) {
  // Clean array of empty data
  function getCacheAbsPath($rel_path){
    $CI =& get_instance();
    $cache_path = $CI->config->item('cache_path');
    $cache_path = ($cache_path === '') ? APPPATH.'cache/' : $cache_path;
    $rel_path = trim($rel_path,'/' . DIRECTORY_SEPARATOR);
    $abs_path = $cache_path . $rel_path;
    return $abs_path;
  }
}
if ( ! function_exists('setCacheStorage')) {
  // Clean array of empty data
  function setCacheStorage($rel_path){
    $abs_path = getCacheAbsPath($rel_path);
    if(file_exists($abs_path)){
      if(is_file($abs_path)){
        throw new Exception('Unable to create cache storage folder. Matching name File was found.');
      }
      if(!is_writable($abs_path)){
        throw new Exception('Unable to write in storage folder.');
      }
      return;
    }
    if(!mkdir($abs_path,0775, true)){
      throw new Exception('Unable to create cache storage folder. Check folder permissions.' . $abs_path);
    }
  }
}
if ( ! function_exists('clearExpiredCacheInDirectory')) {
  // Clean array of empty data
  function clearExpiredCacheInDirectory($abs_path, $rel_path, $cache_path, $cache){
    $empty = true;
    if(!file_exists($abs_path)){
      return $empty;
    }
    foreach (new DirectoryIterator($abs_path) as $fileInfo) {
      if($fileInfo->isDot()) {
        continue;
      }
      if($fileInfo->isDir()) {
        $is_empty = clearExpiredCacheInDirectory($fileInfo->getPathname(), ltrim($rel_path . '/' . $fileInfo->getFilename(),'/'), $cache_path, $cache);
        if(!$is_empty){
          $empty = false;
        }
        continue;
      }
      if ( ! $fp = @fopen($fileInfo->getPathname(), 'r')){
        $empty = false;
				continue;
			}
      
      flock($fp, LOCK_SH);
      $created_time_str = '';
      $time_to_live_str = '';
      $total_char_counter = 0;
      $char_counter = 0;
      $start_created_time = false;
      $start_time_to_live = false;
      while (($char = fgetc($fp)) !== FALSE){
        if(++$total_char_counter > 100){
          break;
        }
        if (($char === ';') && ++$char_counter == 4){
          break;
        }
        if($char_counter === 1){
          if($char === ':'){
            $start_created_time = true;
          } elseif($start_created_time){
            $created_time_str .= $char;
          }
        } elseif($char_counter === 3){
          if($char === ':'){
            $start_time_to_live = true;
          } elseif($start_time_to_live){
            $time_to_live_str .= $char;
          }
        }
      }
      flock($fp, LOCK_UN);
      fclose($fp);
      if(('' . $created_time_str === '' . (int)$created_time_str) && ('' . $time_to_live_str === '' . (int)$time_to_live_str)){
        if($created_time_str + $time_to_live_str < time()){
          $cache->delete(ltrim($rel_path . '/' . $fileInfo->getFilename(),'/'));
        } else {
          $empty = false;
        }
      } else {
        $empty = false;
      }
    }
    if($empty){
      rmdir($abs_path);
    }
    return $empty;
  }
}

if ( ! function_exists('clearExpiredCache')) {
  // Clean array of empty data
  function clearExpiredCache($rel_path = '', $cache){
    $rel_path = trim($rel_path,'/' . DIRECTORY_SEPARATOR);
    if(!strlen($rel_path)){
      throw new Exception('Invalid cache path');
    }
    $CI =& get_instance();
    $cache_path = $CI->config->item('cache_path');
    $cache_path = ($cache_path === '') ? APPPATH.'cache/' : $cache_path;
    $abs_path = $cache_path . $rel_path;
    clearExpiredCacheInDirectory($abs_path, $rel_path, $cache_path, $cache);
  }
}

if ( ! function_exists('deleteCacheByFile')) {
function deleteCacheByFile($file){
	$cache_folder = APPPATH.'cache/';
	if(is_file($cache_folder . $file)){
		@unlink($cache_folder . $file);
	}
}
}
if ( ! function_exists('getCacheFileByFile')) {
function getCacheFileByFile($file){
	$cache_folder = APPPATH.'cache/';
	if(is_file($cache_folder . $file)){
		return $cache_folder . $file;
	}
}
}
if ( ! function_exists('getCacheByFile')) {
function getCacheByFile($file, $default = null){
	$cache_folder = APPPATH.'cache/';
	if(is_file($cache_folder . $file)){
		return file_get_contents($cache_folder . $file);
	}
	return $default;
}
}
if ( ! function_exists('setCacheByFile')) {
function setCacheByFile($file, $value = null){
	if('' === $file) return;
	$cache_folder = APPPATH.'cache/';
	if(!is_dir($cache_folder . dirname($file))){
		mkdir($cache_folder . dirname($file), 0775, true);
	}
	file_put_contents($cache_folder . $file, $value);
	chmod($cache_folder . $file, 0664);
}
}