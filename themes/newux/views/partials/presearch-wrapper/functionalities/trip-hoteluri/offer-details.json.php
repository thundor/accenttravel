<?php
// ini_set('display_errors', 1);
$this->load->model('Trip/Hotels_model');
$code = $this->input->post('Code', null);
$hotel_id = $this->input->post('HotelId', null);
$hotel = null;
$response = '[]';
if($hotel_id){
	
	$cache_key = 'newux/trip/hotel/package/' . md5(json_encode([$code, $hotel_id]));
	$reinit_search = !empty($_SERVER['HTTP_INIT_RESEARCH']);
	// $reinit_search = false;
	// dd($_SERVER);
	if($reinit_search){
		$response = null;
	} else {
		$response = $this->getCache($cache_key);
	}
	if(!isset($response)){
		$hotel = $this->Hotels_model->api->apiCall('index.php/v2/hotels/' . $code . '/' . $hotel_id . '/package', [], [], 'assoc');
		$response = '[]';
		if($hotel){
			unset($hotel['_links']);
			$response = json_encode($hotel);
			$this->setCache($cache_key, $response);
		} else {
			// dd($hotel = $this->Hotels_model->api->call);
		}
		
		// $container_id = 'hotel-' . session_id();
		// $inspections = $this->Hotels_model->inspectSearch($container_id);
		
		// $r = json_decode($response, true);
		// $r['inspection'] = array_filter($inspections, function($i) use ($code){ return $i->Code == $code; });
		// $r['inspection'] = array_shift($r['inspection']);
		// if($r['inspection']){
			// $r['inspect'] = $this->Hotels_model->api->apiCall('index.php/en/dynamic-package/sid/' . $r['inspection']->Id, [], [], 'assoc');
		// }
		// $response = json_encode($r);
		
	} else {
		$r = json_decode($response, true);
		$r['from_cache'] = true;
		$response = json_encode($r);
	}
}

echo $response;