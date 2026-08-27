<?php
$occupancies = $this->input->post('r', null) ?? [];
$_ins = 0;
$_chd = 0;
$_inf = 0;
$_adt = 0;
$_sen = 0;
$_yth = 0;
foreach($occupancies as $room_index => $occupancy){
	$all_chd = count($occupancy['CHD'] ?? []);
	$ins = (int)($occupancy['INS'] ?? 0);
	$chd = count(array_filter($occupancy['CHD'] ?? [], function($age){ return $age > 2 && $age < 12; }));
	$yth = count(array_filter($occupancy['CHD'] ?? [], function($age){ return $age >= 12; }));
	$inf = $all_chd - $yth - $chd - $ins;
	$yth += (int)($occupancy['YTH'] ?? 0);
	$adt = (int)($occupancy['ADT'] ?? 0);
	$sen = (int)($occupancy['SEN'] ?? 0);
	
	$_ins += $ins;
	$_chd += $chd;
	$_inf += $inf;
	$_adt += $adt;
	$_sen += $sen;
	$_yth += $yth;
}

$search_data = array(
      'type' => 1,
      'flex' => 1,
      'class' => $this->input->post('class', null),
      'adt' => $_adt,
      'sen' => $_sen,
      'yth' => $_yth,
      'chd' => $_chd,
      'inf' => $_inf,
      'ins' => $_ins,
      'r' => [],
);

$search_data['r'] = array();
$search_data['r'][0] = array();
$search_data['r'][0]['date'] = $this->input->post('dIn', null);
$search_data['r'][0]['oCityId'] = $this->input->post('depCityId', null);
$search_data['r'][0]['oLocId'] = $this->input->post('depLocationId', null);
$search_data['r'][0]['dCityId'] = $this->input->post('destCityId', null);
$search_data['r'][0]['dLocId'] = $this->input->post('destLocationId', null);

if($search_data['type'] == 1){
$search_data['r'][1] = array();
$search_data['r'][1]['date'] = $this->input->post('dOut', null);
$search_data['r'][1]['oCityId'] = $this->input->post('destCityId', null);
$search_data['r'][1]['oLocId'] = $this->input->post('destLocationId', null);
$search_data['r'][1]['dCityId'] = $this->input->post('depCityId', null);
$search_data['r'][1]['dLocId'] = $this->input->post('depLocationId', null);
}
include __DIR__ . "/../" . str_replace('citybreak', 'avion', basename(dirname(__FILE__))) . '/' . basename(__FILE__);