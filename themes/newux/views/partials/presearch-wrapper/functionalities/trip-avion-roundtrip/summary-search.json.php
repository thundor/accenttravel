<?php
// ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
$this->load->model('Trip/Hotels_model');
$code = $this->input->post('code', null);

$cache_key = 'newux/trip/hotel/summary/' . md5(json_encode($code));
$response = $this->getCache($cache_key);
if(!isset($response)){
	$r = $this->Hotels_model->api->apiCall('index.php/v3/hotels', [
		'code' => $code,
		'summary' => 1,
		'limit' => 5,
	], [], 'assoc');
	unset($r['_links']);
	$response = json_encode($r);
	
	if(!empty($r['summary']['progress']) && 100 == $r['summary']['progress']){
		$this->setCache($cache_key, $response);
	}
}
echo $response;