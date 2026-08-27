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
        $companii_marketing = array();
        $placeholder_companie = $this->theme->theme_url . 'assets/images/placeholder_companie.png';
		array_walk($r['_embedded']['flights'], function(&$flight) use (&$companii_marketing, $placeholder_companie){
			foreach($flight['Routes'] ?? [] as $rk => &$route){
                foreach($route['Route'] ?? [] as $srk => &$subroute){
                  foreach($subroute['Segment'] ?? [] as $sk => $segment){
                    $companie_marketing = $segment['Carrier']['Marketing'] ?? '';
                    if($companie_marketing && is_array($companie_marketing)){
                        $companii_marketing[$companie_marketing['Code']] = array('code'=>$companie_marketing['Code'],'name'=>$companie_marketing['_'], 'img'=>$placeholder_companie);
                    }
                  }
                }
            }
		});
        if($companii_marketing){
          $company_codes = array_keys($companii_marketing);
          $this->load->model('Trip/Flights_airlines_model');
          $companies_from_db = $this->Flights_airlines_model->getAirlines(array('code' => $company_codes));
            foreach($companies_from_db as $k=>$company_from_db){
                $companii_marketing[$company_from_db->code]['e'] = 1;
            }
            
            $companies_not_from_db = array_filter(array_values($companii_marketing), function($v){ return empty($v['e']); });
            if(!empty($companies_not_from_db)){
                foreach($companies_not_from_db as $company_not_from_db){
                    try{
                        $this->Flights_airlines_model->addAirline(['code' => $company_not_from_db['code'], 'name' => $company_not_from_db['name'], 'original_name' => $company_not_from_db['name']]);
                    } catch(Exception $e){
                        // Do nothing
                    }
                }
            }
        }
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