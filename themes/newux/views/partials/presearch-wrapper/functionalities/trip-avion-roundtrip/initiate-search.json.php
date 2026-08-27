<?php // echo 'a'; die;
// ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
$this->load->model('Trip/Flights_model');
$container_id = 'flight-' . session_id();
$time = time();
if(!isset($search_data)){
	$occupancy = $this->input->post('r', null) ?? [];
	$all_chd = count($occupancy['CHD'] ?? []);
	$ins = (int)($occupancy['INS'] ?? 0);
	$chd = count(array_filter($occupancy['CHD'] ?? [], function($age){ return $age > 2 && $age < 12; }));
	$yth = count(array_filter($occupancy['CHD'] ?? [], function($age){ return $age >= 12; }));
	$inf = $all_chd - $yth - $chd - $ins;
	$yth += (int)($occupancy['YTH'] ?? 0);
	$adt = (int)($occupancy['ADT'] ?? 0);
	$sen = (int)($occupancy['SEN'] ?? 0);

	$search_data = array(
		  'type' => $this->input->post('type', null),
		  'class' => $this->input->post('class', null),
		  'adt' => $adt,
		  'sen' => $sen,
		  'yth' => $yth,
		  'chd' => $chd,
		  'inf' => $inf,
		  'ins' => $ins,
		  'r' => [],
	);

	$search_data['r'] = array();
	$search_data['r'][0] = array();
	$search_data['r'][0]['date'] = $this->input->post('dIn', null);
	// $search_data['r'][0]['tMode'] = 'arrival';
	$search_data['r'][0]['oCityId'] = $this->input->post('depCityId', null);
	$search_data['r'][0]['oLocId'] = $this->input->post('depLocationId', null);
	$search_data['r'][0]['dCityId'] = $this->input->post('destCityId', null);
	$search_data['r'][0]['dLocId'] = $this->input->post('destLocationId', null);

	if($search_data['type'] == 1){
	$search_data['r'][1] = array();
	$search_data['r'][1]['date'] = $this->input->post('dOut', null);
	$search_data['r'][1]['oCityId'] = $this->input->post('destCityId', null);
	$search_data['r'][1]['oLocId'] = $this->input->post('destLocationId', null);
	$search_data['r'][1]['dCityId'] = $this->input->post('depCityId', null);
	$search_data['r'][1]['dLocId'] = $this->input->post('depLocationId', null);
	}
}

// dd($search_data);
$cache_key = 'newux/trip/flights/initiate/' . md5(json_encode($search_data));
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
		$inspections = $this->Flights_model->inspectSearch($container_id);
		if($inspections){
			$last_inspection = array_pop($inspections);
		}
	}
} elseif($response) {
	$cached_inspection = json_decode($response);
	if(!empty($cached_inspection->Timestamp) && $cached_inspection->Timestamp > (time() - 86400)){ // Not older than 7 days
		$cached = $this->getCache('newux/trip/flight/inspect/' . md5(json_encode($cached_inspection->Id)));
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
	$r = $this->Flights_model->api->apiCall('index.php/en/dynamic-package/sid/' . $last_inspection->Id, [], [], 'assoc');
	$inspect_search = array_intersect_key($r, $search_data);
	if(md5(json_encode($inspect_search, JSON_NUMERIC_CHECK)) === md5(json_encode($search_data, JSON_NUMERIC_CHECK))){
		$this->deleteCache('newux/trip/flight/inspect/' . md5(json_encode($last_inspection->Id)));
		if(!empty($r['life']) && $r['life'] > (1800 - 60*5)){ // max-life - 5 minutes
			echo json_encode($last_inspection);
			return;
		}
	}
}

if($this->Flights_model->api->getAccountId()){
	$search_data['accId'] = $this->Flights_model->api->getAccountId();
}
$this->Flights_model->api->generateToken();

$result = $this->Flights_model->api->apiCall('index.php/en/dynamic-package/search', array(
	'_s' => array(
	  $container_id => array(
		'f' => array(
		  0 => $search_data
		)
	  )
	),
), [], 'assoc');
if($result){
	$inspection = array_pop($result[$container_id]);
	$this->deleteCache('newux/trip/flight/inspect/' . md5(json_encode($inspection['Id'])));
	$inspection['Timestamp'] = $time;
	$response = json_encode($inspection);
	$this->setCache($cache_key, $response);
} else {
	prd($this->Flights_model->api->calls);
}
// var_dump($response);
echo $response;