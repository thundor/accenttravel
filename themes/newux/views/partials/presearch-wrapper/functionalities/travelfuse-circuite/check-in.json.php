<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
themeFunctions::loadLang('common');
$cities = [];
$sql = "SELECT ci.Date, GROUP_CONCAT(DISTINCT ci.Transport) as Transport" . require(__DIR__ . '/sql.php');

$sql .= "GROUP BY ci.Date";
$cities = $this->query($sql)->result('array');
// $cities[] = ['Date' => '2024-12-01'];
/* $cities = $this->TravelFuse_model->searchTourCheckIn([
	'Transport' => $this->input->post('Transport', null),
	'destination' => $this->input->post('destination', null),
	'destinationType' => $this->input->post('destinationType', null),
	'departureCity' => $this->input->post('departureCity', null),
]); */
// print_r($cities); die;
echo json_encode($cities ? array_map(function($city){
	if(!is_array($city)){
		$city = ['Date' => $city];
	}
	$city['Id'] = html_entity_decode($city['Date'], ENT_QUOTES);
	$date_arr = preg_split('/[^0-9]+/', $city['Date']);
	$name = lang('month_' . intval($date_arr[1]));
	$name .= ' ' . $date_arr[0];
	$city['Name'] = html_entity_decode($name, ENT_QUOTES);
	$city['Transport'] = preg_split('/,/', $city['Transport']);
	// $city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	return $city;
}, $cities) : []);