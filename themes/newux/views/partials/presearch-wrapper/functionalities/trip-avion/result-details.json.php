<?php
// ini_set('display_errors', 1);
$this->load->model('Trip/Flights_model');
$Code = $this->input->post('Code', null);
$ItineraryCode = $this->input->post('ItineraryCode', null);
$CombinationIndex = $this->input->post('CombinationIndex', null);

$flight = null;
$response = '[]';
if($Code && $ItineraryCode && strlen($CombinationIndex ?? '')){
	
	$cache_key = 'newux/trip/flights/flight/' . md5(json_encode([$Code, $ItineraryCode, $CombinationIndex]));
	$response = $this->getCache($cache_key);
	// $response = null;
	if(!isset($response)){
		$response = '[]';
		$flight = $this->Flights_model->api->apiCall('index.php/v3/flights/' . $Code . '/flight/' . $ItineraryCode . ':' . $CombinationIndex, [], [], 'assoc');
		if($flight){
			unset($flight['_links']);
			$response = json_encode($flight);
			$this->setCache($cache_key, $response);
		}
	}
}

echo $response;