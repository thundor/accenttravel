<?php
// ini_set('display_errors', 1);
$this->load->model('Trip/Hotels_model');
$hotel_id = $this->input->post('HotelId', null);
$hotel = null;
$response = '[]';
if($hotel_id){
	
	$cache_key = 'newux/trip/hotel/hotel/' . md5(json_encode($hotel_id));
	$response = $this->getCache($cache_key);
	// $response = null;
	if(!isset($response)){
		$response = '[]';
		$hotel = $this->Hotels_model->api->apiCall('index.php/v3/hotels/' . $hotel_id, [], [], 'assoc');
		if(isset($hotel['FullDesc'])){
			$hotel['FullDesc'] = htmlspecialchars_decode($hotel['FullDesc'],ENT_COMPAT);
		}
		
		if($hotel){
			$response = json_encode($hotel);
			$this->setCache($cache_key, $response);
		}
	}
}

echo $response;