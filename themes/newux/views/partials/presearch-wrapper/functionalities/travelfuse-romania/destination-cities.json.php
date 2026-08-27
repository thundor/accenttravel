<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
$cities = $this->TravelFuse_model->searchCities([
	'Country' => 126,
	// 'Transport' => $this->input->post('Transport', null),
	// 'departureCity' => $this->input->post('departureCity', null),
]);
// print_r($cities); die;
echo json_encode($cities ? array_map(function($city){
	$city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
	$city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	return $city;
}, $cities) : []);