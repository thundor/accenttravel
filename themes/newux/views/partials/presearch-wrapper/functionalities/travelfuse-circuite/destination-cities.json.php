<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
$sql = "SELECT DISTINCT c.Id, GROUP_CONCAT(DISTINCT c.Transport) as Transport, c.type, IF(cnt_dest.Id IS NOT NULL, COALESCE((SELECT COALESCE(cn._name_ro, acn.name_RO, cn._name_en, acn.name, cn.Name) FROM tf_countries cn LEFT JOIN ac_country acn ON (cn.Code = acn.iso_2) WHERE cn.Id = cnt_dest.Id),c.Name), IF(dest.Id IS NOT NULL, COALESCE(dest._name_ro, dest._name_en,c.Name), c.Name)) AS Name, CONCAT_WS('-', c.Name, cnt_dest.Code) alias" . require(__DIR__ . '/sql.php');
$sql .= " GROUP BY c.Id ";
$sql .= " ORDER BY Name ASC, type ASC";
$cities = $this->query($sql)->result('array');
// $cities = $this->TravelFuse_model->searchTourCities([
	// 'Transport' => $this->input->post('Transport', null),
	// 'departureCity' => $this->input->post('departureCity', null),
// ]);
// print_r($cities); die;
echo json_encode($cities ? array_map(function($city){
	$city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
	$city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	$city['Transport'] = preg_split('/,/', $city['Transport']);
	return $city;
}, $cities) : []);