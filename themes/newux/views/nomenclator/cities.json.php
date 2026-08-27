<?php
$this->load->model('TravelFuse_model');
$cities = $this->TravelFuse_model->searchCities(['Country' => (int)$this->input->get('country_id')]);
echo json_encode($cities ? array_map(function($city){
	$city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
	$city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	return $city;
}, $cities) : []);