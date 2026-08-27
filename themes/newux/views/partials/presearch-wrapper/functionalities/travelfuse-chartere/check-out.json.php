<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
$cities = [];
$sql = "SELECT co.Date CheckOut, GROUP_CONCAT(DISTINCT co.Transport) as Transport, JSON_ARRAYAGG(JSON_OBJECT(pr.Id, JSON_OBJECT('Id', pr.Id, 'Caption', pr.Caption))) AS Providers" . require(__DIR__ . '/sql.php');
$sql .= " GROUP BY co.Date";
// if(IS_LISAL_IP){
	// echo $sql; die;
// }
$cities = $this->query($sql)->result('array');
/* 
$cities = $this->TravelFuse_model->searchCharterCheckOut([
	'Transport' => $this->input->post('Transport', null),
	'destination' => $this->input->post('destination', null),
	'destinationType' => $this->input->post('destinationType', null),
	'departureCity' => $this->input->post('departureCity', null),
	'departureDate' => $this->input->post('departureDate', null),
]); */
// print_r($cities); die;
echo json_encode($cities ? array_map(function($city){
	$city['Date'] = $city['CheckOut'];
	$city['Id'] = html_entity_decode($city['Date'], ENT_QUOTES);
	$city['Name'] = html_entity_decode($city['Date'], ENT_QUOTES);
	$city['Providers'] = json_decode($city['Providers'], true);
	$city['Transport'] = preg_split('/,/', $city['Transport']);
	// $city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	return $city;
}, $cities) : []);