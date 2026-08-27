<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TestPayU extends MX_Controller {
  public function index() {
	$order_id = 'ipn_order_id-' . 124;
    $key = $order_id;
	
	$maxAcquire = 1;
	$permissions =0666;
	$autoRelease = 1;
	echo '<pre>';
	echo 'Data acces: '; $now = DateTime::createFromFormat('U.u', microtime(true)); echo $now->format("m-d-Y H:i:s.u"); echo PHP_EOL;
	try{
	$semaphore = sem_get(crc32($key), $maxAcquire, $permissions, $autoRelease);
	var_dump($semaphore);
	if(!$semaphore) {
		echo "Failed on sem_get().\n";
		exit;
	}
	} catch (Exception $e){
		echo $e->getMessage();
	}
	
	var_dump(sem_acquire($semaphore));
	echo 'Data permitere functionalitate: '; $now = DateTime::createFromFormat('U.u', microtime(true)); echo $now->format("m-d-Y H:i:s.u"); echo PHP_EOL;
	
	sleep(3);
	
	sem_release($semaphore);
	echo 'Data sfarsit functionalitate: '; $now = DateTime::createFromFormat('U.u', microtime(true)); echo $now->format("m-d-Y H:i:s.u"); echo PHP_EOL;
  }
}