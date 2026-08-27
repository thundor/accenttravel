<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
$sql = "SELECT cit_dest.Id, GROUP_CONCAT(DISTINCT co.Transport) as Transport, cit_dest.type, COALESCE((SELECT COALESCE(ci._name_ro, ci._name_en, ci.Name) FROM tf_cities ci WHERE ci.Id = cit_dest.Id AND ci.type = cit_dest.type),cit_dest.Name) Name, cit_dest.CountryId, COALESCE((SELECT COALESCE(cn._name_ro, acn.name_RO, cn._name_en, acn.name, cn.Name) FROM tf_countries cn LEFT JOIN ac_country acn ON (cn.Code = acn.iso_2) WHERE cn.Id = cit_dest.CountryId),cnt_dest.Name) Country, CONCAT_WS('-', cit_dest.Name, cnt_dest.Code) alias" . require(__DIR__ . '/sql.php');
$sql .= " GROUP BY cit_dest.Id";
$sql .= " ORDER BY Country ASC, Name ASC, type ASC";
$cities = $this->query($sql)->result('array');
// $cities = $this->TravelFuse_model->searchCharterCities(['Transport' => $this->input->post('Transport', null)]);
// print_r($cities); die;
echo json_encode($cities ? array_map(function($city){
	$city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
	$city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	$city['Transport'] = preg_split('/,/', $city['Transport']);
	return $city;
}, $cities) : []);