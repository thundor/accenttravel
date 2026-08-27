<?php
// ini_set('display_errors', 1);
// echo 'test'; die;
ini_set('memory_limit', '512M');
$this->load->model('Trip/Flights_model');
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
	'code' => $code,
	'page' => $this->input->post('page', 1),
];

$cache_key = 'newux/trip/flights/list/' . md5(json_encode($search_data));
$response = $this->getCache($cache_key);
// dd($response);
// $response = null;
if(!isset($response)){
	// print_r($search_data);
	// die;
	$r = $this->Flights_model->api->apiCall('index.php/v3/flights', $search_data, [], 'assoc');
	$r['_embedded'] = $r['_embedded'] ?? [];
	$r['search_data'] = $search_data;
	// $r['call'] = $this->Flights_model->api->call;
	unset($r['_links']);
	if(!empty($r['_embedded']['flights'])){
		/* array_walk($r['_embedded']['flights'], function(&$h){
			return $h;
		}); */
	}
	// $r['_embedded']['hotels'] = array_slice($r['_embedded']['hotels'], 0, 10);
	/* 
	$hotel_ids = array_column(($r['_embedded']['hotels'] ?? []), 'Id');
	foreach($r['_embedded']['hotels'] ?? [] as $k => $hotel){
		$hotel_id = $hotel['Id'];
		$r2 = $this->Flights_model->api->apiCall('index.php/v3/hotels/' . $hotel_id, [
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