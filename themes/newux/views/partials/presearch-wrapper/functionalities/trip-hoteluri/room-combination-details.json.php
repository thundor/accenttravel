<?php
// ini_set('display_errors', 1);
// echo 'test';
// die;
$this->load->model('Trip/Hotels_model');
$Code = $this->input->post('Code', null);
$PackageCode = $this->input->post('PackageCode', null);
$RoomsCombination = $this->input->post('RoomsCombination', null);
$HotelId = $this->input->post('HotelId', null);
$hotel = null;
$response = '[]';
if($HotelId){
	
	$cache_key = 'newux/trip/hotel/roomscombination/' . md5(json_encode([$Code, $HotelId, $PackageCode, $RoomsCombination]));
	$response = $this->getCache($cache_key);
	if(!isset($response)){
		$hotel = $this->Hotels_model->api->apiCall('index.php/v2/hotels/' . $Code . '/' . $HotelId . '/package/' . $PackageCode . '/' . $RoomsCombination, [], [], 'assoc');
		$response = '[]';
		if($hotel){
			unset($hotel['_links']);
			$response = json_encode($hotel);
			$this->setCache($cache_key, $response);
		} else {
			dd($hotel = $this->Hotels_model->api->call);
		}
	}
}

echo $response;