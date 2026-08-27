<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('cron_lock_file')) {
  //Create lock file to prevent simultaneous runs
  function cron_lock_file($file_name='cron_lock_file', $duration = '30 seconds'){
    $lock_file_folder = dirname(__DIR__) . '/tmp/'; // app/tmp
    $lock_file = $lock_file_folder . $file_name;
    if(!file_exists($lock_file)) {
      $ourFileHandle = fopen($lock_file, 'w') or die("can't open file");
      fclose($ourFileHandle);
      return true;
    } else {
      //Remove a lock file over one hour old
      $file_age = strtotime($duration . ' ago') - filemtime($lock_file);
      if($file_age >= 0) {
        return true;
      } else {
        die("cron_lock file present. A cron is already running. (Lock file is cleared after " . $duration . " or you can remove the cron_lock in your app/tmp folder)\n");
      }
    }
    return false;
  }
}
if ( ! function_exists('cron_unlock_file')) {
  //Unlock file after execution
  function cron_unlock_file($file_name='cron_lock_file'){
    $lock_file_folder = dirname(__DIR__) . '/tmp/'; // app/tmp
    $lock_file = $lock_file_folder . $file_name;
    if(file_exists($lock_file)) {
      unlink($lock_file);
    }
  }
}