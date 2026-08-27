<?php
// ini_set('display_errors', 1);
$this->load->model('Trip/Flights_model');
$Code = $this->input->post('Code', null);
$ItineraryCode = $this->input->post('ItineraryCode', null);
$CombinationIndex = $this->input->post('CombinationIndex', null);

$flight = null;
$response = '[]';
if($Code && $ItineraryCode && strlen($CombinationIndex ?? '')){
	
	$cache_key = 'newux/trip/flights/upsell/' . md5(json_encode([$Code, $ItineraryCode, $CombinationIndex]));
	$response = $this->getCache($cache_key);
	// $response = null;
	if(!isset($response)){
		$response = '[]';
		$flight = $this->Flights_model->api->apiCall('index.php/v3/flights/' . $Code . '/flight/' . $ItineraryCode . ':' . $CombinationIndex . '/upsell', [], [], 'assoc');
		if($flight){
			if(!empty($flight['_embedded']['upsell'])){
				$flight['_embedded']['upsell'] = array_filter($flight['_embedded']['upsell'], function($v){ return !empty($v['Code']); });
				$flight['_embedded']['upsell'] = array_values($flight['_embedded']['upsell']);
			}
			unset($flight['_links']);
			$response = json_encode($flight);
			$this->setCache($cache_key, $response);
		}
	}
}

echo $response;