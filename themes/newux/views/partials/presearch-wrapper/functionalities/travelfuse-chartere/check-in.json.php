<?php
// ini_set('display_errors', 1);
if(empty($_POST)){
	$_POST = [
		'destination'=> '980',
		'destinationType'=> 'county',
		'departureCity'=> '3302',
		'Transport'=> 'plane',
	];
}
$this->load->model('TravelFuse_model');
$cities = [];
$sql = "SELECT ci.Date, GROUP_CONCAT(DISTINCT co.Transport) as Transport" . require(__DIR__ . '/sql.php');

$sql .= " GROUP BY ci.Date";
// $sql .= "ORDER BY ci.Date ASC";
$cities = $this->query($sql)->result('array');

/* 
$cities = $this->TravelFuse_model->searchCharterCheckIn([
	'Transport' => $this->input->post('Transport', null),
	'destination' => $this->input->post('destination', null),
	'destinationType' => $this->input->post('destinationType', null),
	'departureCity' => $this->input->post('departureCity', null),
]); 
*/
// print_r($cities); die;
echo json_encode($cities ? array_map(function($city){
	if(!is_array($city)){
		$city = ['Date' => $city];
	}
	$city['Id'] = html_entity_decode($city['Date'], ENT_QUOTES);
	$city['Name'] = html_entity_decode($city['Date'], ENT_QUOTES);
	$city['Transport'] = preg_split('/,/', $city['Transport']);
	// $city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	return $city;
}, $cities) : []);