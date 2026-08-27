<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
// $time = microtime(true);
$sql = "SELECT cit_dep.Id, GROUP_CONCAT(DISTINCT co.Transport) as Transport, cit_dep.type, COALESCE((SELECT COALESCE(ci._name_ro, ci._name_en, ci.Name) FROM tf_cities ci WHERE ci.Id = cit_dep.Id AND ci.type = cit_dep.type),cit_dep.Name) Name, cit_dep.CountryId, COALESCE((SELECT COALESCE(cn._name_ro, acn.name_RO, cn._name_en, acn.name, cn.Name) FROM tf_countries cn LEFT JOIN ac_country acn ON (cn.Code = acn.iso_2) WHERE cn.Id = cit_dep.CountryId),cnt_dep.Name) Country, CONCAT_WS('-', cit_dep.Name, cnt_dep.Code) alias" . require(__DIR__ . '/sql.php');
$sql .= " GROUP BY cit_dep.Id ";
$sql .= " ORDER BY Country ASC, Name ASC, type ASC";
$cities = $this->query($sql)->result('array');
// $time2 = microtime(true);
// dd($time2 - $time);
// print_r($cities); 
// $cities = $this->TravelFuse_model->searchCharterDepartureCities([
	// 'Transport' => $this->input->post('Transport', null),
	// 'destination' => $this->input->post('destination', null),
	// 'destinationType' => $this->input->post('destinationType', null),
// ]);
// print_r($cities); 
// die;
echo json_encode($cities ? array_map(function($city){
	$city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
	$city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	$city['Transport'] = preg_split('/,/', $city['Transport']);
	return $city;
}, $cities) : []);