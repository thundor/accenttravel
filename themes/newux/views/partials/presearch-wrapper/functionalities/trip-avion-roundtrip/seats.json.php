<?php
// ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
$this->load->model('Trip/Flights_model');
$code = $this->input->post_get('code');
$itinerary_code = $this->input->post_get('itinerary_code');
$ocode = $this->input->post_get('ocode');
$dcode = $this->input->post_get('dcode');
$rindex = $this->input->post_get('rindex');
$req_paid_seat = $this->input->post_get('pseat') ? 'True' : 'False';

$cache_key = 'newux/trip/flight/seats/' . md5(json_encode([$code, $itinerary_code, $ocode, $dcode, $rindex, $req_paid_seat]));
$reinit_search = !empty($_SERVER['HTTP_INIT_RESEARCH']);

if($reinit_search){
	$response = null;
} else {
	$response = $this->getCache($cache_key);
}
if(!isset($response)){
	$seats = $this->Flights_model->loadFlightSeats($code, $itinerary_code, $ocode, $dcode, $rindex, $req_paid_seat);
	$response = json_encode($seats);
	$this->setCache($cache_key, $response);
}
echo $response;