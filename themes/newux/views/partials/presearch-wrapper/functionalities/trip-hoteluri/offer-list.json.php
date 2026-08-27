<?php
// ini_set('display_errors', 1);
// echo 'test'; die;
ini_set('memory_limit', '512M');
$this->load->model('Trip/Hotels_model');
$code = $this->input->post('code', null);
// $_POST['filter'][] = [
	// 'name' => 'Name',
	// 'type' => 'startsWith',
	// 'term' => 'Tranzzit',
// ];
// $_POST['filter']['Name']['startsWith'] = 'Tranzzit';
// $filter = $this->input->post('filter', null) ?? [];
// $filter['hotel'] = 'Razvan';
// $_POST['filter'] = $filter;
$search_data = [
	'part' => 'Facilities,ShortDesc,Pois',
	'code' => $code,
	// 'summary' => 1,
	// 'filter' => $this->input->post('filter', null),
	// 'page' => $this->input->post('page', 1),
	// 'limit' => 3,
	'sortType' => $this->input->post('sortType', null),
	'sortOrder' => $this->input->post('sortOrder', null),
];

$cache_key = 'newux/trip/hotel/list/' . md5(json_encode($search_data));
$response = $this->getCache($cache_key);
// dd($response);
// $response = null;
if(!isset($response)){
	// print_r($search_data);
	// die;
	$r = $this->Hotels_model->api->apiCall('index.php/v3/hotels', $search_data, [], 'assoc');
	$r['_embedded'] = $r['_embedded'] ?? [];
	$r['search_data'] = $search_data;
	// $r['call'] = $this->Hotels_model->api->call;
	unset($r['_links']);
	if(!empty($r['_embedded']['hotels'])){
		array_walk($r['_embedded']['hotels'], function(&$h){
			if(!empty($h['Facilities'])){
				$h['Facilities'] = preg_split('/\s*,\s*/', $h['Facilities']);
				$h['Facilities'] = array_unique($h['Facilities']);
				$h['Facilities'] = array_filter($h['Facilities']);
				$h['Facilities'] = implode(', ', $h['Facilities']);
			}
			if(!empty($h['Pois'])){
				$h['Pois'] = preg_split('/\s*,\s*/', $h['Pois']);
				$h['Pois'] = array_unique($h['Pois']);
				$h['Pois'] = array_filter($h['Pois']);
				$h['Pois'] = implode(', ', $h['Pois']);
			}
			return $h;
		});
	}
	// $r['_embedded']['hotels'] = array_slice($r['_embedded']['hotels'], 0, 10);
	/* 
	$hotel_ids = array_column(($r['_embedded']['hotels'] ?? []), 'Id');
	foreach($r['_embedded']['hotels'] ?? [] as $k => $hotel){
		$hotel_id = $hotel['Id'];
		$r2 = $this->Hotels_model->api->apiCall('index.php/v3/hotels/' . $hotel_id, [
			'lang' => 'ro_RO'
		], [], 'assoc');
		$r['_embedded']['hotels'][$k] = array_replace($hotel, $r2);
		// print_r($r['_embedded']['hotels'][$k]); die;
	} */
	$response = json_encode($r);
	$this->setCache($cache_key, $response);
	if(!empty($return_results)){
		return;
	}
} else {
	if(!empty($return_results)){
		return;
	}
}

echo $response;