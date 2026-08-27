<?php
// ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
$this->load->model('Trip/Hotels_model');
$container_id = 'hotel-' . session_id();
$time = time();

$occupancy = $this->input->post('r', null);
array_walk($occupancy, function(&$room){
	$room = array_intersect_key($room, array_flip(['ADT', 'CHD']));
	if(!empty($room['CHD'])){
		$room['CHD']['AGE'] = $room['CHD'];
	}
});
$search_data = array(
      'dIn' => $this->input->post('dIn', null),
      'dOut' => $this->input->post('dOut', null),
      'cityId' => $this->input->post('cityId', null),
      'r' => $occupancy,
      'onRq' => 1,
      // 'hotel' => ['stars' => 4],
      'full' => 1,
);

$hotel_id = $this->input->post('hotelId', null);
if($hotel_id){
	$search_data['hotel']['id'] = $hotel_id;
}
$cache_key = 'newux/trip/hotel/initiate/' . md5(json_encode($search_data));
$reinit_search = !empty($_SERVER['HTTP_INIT_RESEARCH']);
// $reinit_search = false;
// dd($_SERVER);
if($reinit_search){
	$response = null;
} else {
	$response = $this->getCache($cache_key);
}
$last_inspection = null;
if($reinit_search || !$response){ // When reiniting we allow 5 minutes of cache time so not to span the trip server
	if(isset($response)){
		$last_inspection = json_decode($response);
	} else {
		$inspections = $this->Hotels_model->inspectSearch($container_id);
		if($inspections){
			$last_inspection = array_pop($inspections);
		}
	}
} elseif($response) {
	$cached_inspection = json_decode($response);
	if(!empty($cached_inspection->Timestamp) && $cached_inspection->Timestamp > (time() - 86400)){ // Not older than 1 day
		$cached = $this->getCache('newux/trip/hotel/inspect/' . md5(json_encode($cached_inspection->Id)));
		if($cached){ // Must have cached inspect
			$r = json_decode($cached, true);
			if($r){
				if(!empty($r['life']) && $r['life'] > 60){ // If the life is lower than 60 there is a high risk that the results are empty
					echo $response;
					return;
				}
			}
			// dd($decoded);
		}
	}
	
}

if($last_inspection){
	$r = $this->Hotels_model->api->apiCall('index.php/en/dynamic-package/sid/' . $last_inspection->Id, [], [], 'assoc');
	$inspect_search = array_intersect_key($r, $search_data);
	if(md5(json_encode($inspect_search, JSON_NUMERIC_CHECK)) === md5(json_encode($search_data, JSON_NUMERIC_CHECK))){
		$this->deleteCache('newux/trip/hotel/inspect/' . md5(json_encode($last_inspection->Id)));
		if(!empty($r['life']) && $r['life'] > (1800 - 60*5)){ // max-life - 5 minutes
			echo json_encode($last_inspection);
			return;
		}
	}
}

// dd('not good');

if($this->Hotels_model->api->getAccountId()){
	$search_data['accId'] = $this->Hotels_model->api->getAccountId();
}
$this->Hotels_model->api->generateToken();

$result = $this->Hotels_model->api->apiCall('index.php/en/dynamic-package/search', array(
	'_s' => array(
	  $container_id => array(
		'h' => array(
		  0 => $search_data
		)
	  )
	),
), [], 'assoc');
if($result){
	$inspection = array_pop($result[$container_id]);
	$this->deleteCache('newux/trip/hotel/inspect/' . md5(json_encode($inspection['Id'])));
	$inspection['Timestamp'] = $time;
	$response = json_encode($inspection);
	$this->setCache($cache_key, $response);
}

echo $response;