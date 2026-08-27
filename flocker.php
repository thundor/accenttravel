<?php
abstract class FLocker {
	static $locker_dir = __DIR__ . '/app/tmp/';
	static $locks = [];
	static $registered_shutdown;
	static function acquire_nb($key){
		try{
			$acquired = self::acquire($key, false);
		} catch(Exception $e){
			$acquired = false;
		}
		return $acquired;
	}
	static function attempt_lock($fp, $block = true){
		$count = 0;
		$timeout_secs = 60; //number of seconds of timeout
		$got_lock = true;
		while (!flock($fp, LOCK_EX | LOCK_NB, $wouldblock)) {
			if ($wouldblock && $count++ < $timeout_secs) {
				sleep(1);
			} else {
				$got_lock = false;
				break;
			}
		}
		return $got_lock;
	}
	static function acquire($key, $block = true){
		$file = static::$locker_dir . $key . ".lock";
		$fp = @fopen($file, "c");
		if(!$fp){
			throw new Exception("Couldn't open lockfile!");
		}
		$lock_style = LOCK_EX;
		if(!$block){
			$lock_style = LOCK_EX | LOCK_NB;
		}
		if (@flock($fp, $lock_style)) {  // acquire an exclusive lock
			static::$locks[' ' . $key] = $fp;
			@ftruncate($fp, 0);      // truncate file
			@fwrite($fp, getmypid());
			@fflush($fp);            // flush output before releasing the lock
			return $key;
		} else {
			throw new Exception("Couldn't get the lock!");
		}
		return false;
	}
	static function release($key){
		if(!isset(static::$locks[' ' . $key])){
			return;
		}
		$fp = static::$locks[' ' . $key];
		@flock($fp, LOCK_UN);    // release the lock
		@fclose($fp);
		$file = static::$locker_dir . $key . ".lock";
		if(is_file($file) && file_get_contents($file) == getmypid()){
			@unlink($file);
		}
	}
	static function release_all(){
		foreach(array_keys(static::$locks) as $k){
			static::release(substr($k, 1));
		}
	}
}

register_shutdown_function('FLocker::release_all');