<?php
// ini_set('display_errors', 1);
$return_results = 1;
require(__DIR__ . '/offer-list.json.php');
if(!isset($r) && isset($response)){
	$r = json_decode($response, true);
}
$r['filters'] = [];

$min_price = null;
$max_price = null;
$escale = array();
$escale_tur = array();
$escale_retur = array();
$companii_marketing = array();
$classes = array();
$cabin_types = array();
$nonrefundables = array();

$this->load->library('image_lib');

$config['image_library'] = 'gd2';
$config['width'] = 25;
$config['height'] = 25;
$config['master_dim'] = 'height';

$theme_path = $this->theme->config('path');
$theme_name = $this->theme->config('theme');

/* $original_filename = 'placeholder_companie';
$original_file =  $original_filename . '.png';
$original_filepath = $theme_path . $theme_name . '/assets/images/' . $original_file;

$new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
$new_file =  $new_filename . '.png';
$cdn_image_path = FCPATH . 'cdn/airlines/images/';
$overwrite = false;
$placeholder_companie = null;
if(!file_exists($cdn_image_path . $new_file) || $overwrite){
  if(file_exists($original_filepath)){
	$config['source_image'] = $original_filepath;
	$config['new_image'] = $cdn_image_path . $new_file;
	$this->image_lib->initialize($config);
	$this->image_lib->resize();
  }
}
if(file_exists($cdn_image_path . $new_file)){
  $placeholder_companie = base_url() . 'cdn/airlines/images/' . $new_file;
} */
$placeholder_companie = $this->theme->theme_url . 'assets/images/placeholder_companie.png';

$din = null;
$dout = null;
if('trip-citybreak' == basename(dirname($a))){
	$din = $r['searchData']['Routes'][0]['Date'];
	if(1 == $r['searchData']['Type']){
		$dout = $r['searchData']['Routes'][1]['Date'];
	}
}
// dd([$din, $dout]);

foreach($r['_embedded']['flights'] as $flight_index => &$flight){
  if(is_null($min_price) || $flight['PriceDetail']['Amount'] < $min_price){
	$min_price = $flight['PriceDetail']['Amount'];
  }
  if(is_null($max_price) || $flight['PriceDetail']['Amount'] > $max_price){
	$max_price = $flight['PriceDetail']['Amount'];
  }
  $nonrefundable = !empty($flight['NonRefundable']) ? 1 : 0;
  $nonrefundables[$nonrefundable] = $nonrefundable;
  // print_r($flight);
  // die;
  $combs = [];
  foreach($flight['Combinations'] as $combination_index => $combination){
	  $cs = preg_split('/\|/', $combination);
	  foreach($cs as $ci => $c){
		  $combs[$ci][$c][] = $combination_index;
	  }
  }
  //dd($combs);
  foreach($flight['Routes'] as $rk => &$route){
	foreach($route['Route'] as $srk => &$subroute){
		$found_comb = false;
		if(!isset($combs[$rk][$rk . $subroute['Ref']])){
			continue;
			// dump($subroute['Ref']);
			// dump($combs);
			// dd($flight);
		}
		foreach($combs[$rk][$rk . $subroute['Ref']] as $ci){
			if(isset($flight['Combinations'][$ci])) {
				$found_comb = true;
				break;
			}
		}
		if(!$found_comb){
			continue;
		}
		$unset_route = false;
		$stops = count($subroute['Segment']) -1;
		if(isset($din) && !$rk){
			if($din != $subroute['Segment'][$stops]['Destination']['Date']){
				$unset_route = true;
			}
		}
		if($rk && !$unset_route && isset($dout)){
			if($dout != $subroute['Segment'][0]['Origin']['Date']){
				$unset_route = true;
			}
		}
		if($unset_route){
			foreach($combs[$rk][$rk . $subroute['Ref']] as $ci)
				unset($flight['Combinations'][$ci]);
			continue;
		}
	  $escale[$stops] = $stops;
	  if($rk == 0) $escale_tur[$stops] = $stops;
	  if($rk == 1) $escale_retur[$stops] = $stops;
	  foreach($subroute['Segment'] as $sk => $segment){
		$companie_marketing = $segment['Carrier']['Marketing'];
		$companii_marketing[$companie_marketing['Code']] = array('code'=>$companie_marketing['Code'],'name'=>$companie_marketing['_'], 'img'=>$placeholder_companie);
		$classes[$segment['Flight']['Class']] = $segment['Flight']['Class'];
		$cabin_types[strtolower($segment['Flight']['CabinType'])] = ucwords(strtolower($segment['Flight']['CabinType']));
	  }
	}
  }
  if(empty($flight['Combinations'])){
	  unset($r['_embedded']['flights'][$flight_index]);
	  continue;
  }
  $flight['Combinations'] = array_values($flight['Combinations']);
}
$r['_embedded']['flights'] = array_values($r['_embedded']['flights']);
$companies_from_db = array();
if($companii_marketing){
  $company_codes = array_keys($companii_marketing);
  $this->load->model('Trip/Flights_airlines_model');
  $companies_from_db = $this->Flights_airlines_model->getAirlines(array('code' => $company_codes));
}
foreach($companies_from_db as $k=>$company_from_db){
	$companii_marketing[$company_from_db->code]['e'] = 1;
  if($company_from_db->image){
	/* $original_filename = $company_from_db->image;
	$original_file =  $original_filename . '.png';
	$original_filepath = $theme_path . $theme_name . '/assets/images/' . $original_file;
	
	$new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
	$new_file =  $new_filename . '.png';
	$cdn_image_path = FCPATH . 'cdn/airlines/images/';
	$overwrite = false;
	if(!file_exists($cdn_image_path . $new_file) || $overwrite){
	  if(file_exists($original_filepath)){
		$config['source_image'] = $original_filepath;
		$config['new_image'] = $cdn_image_path . $new_file;
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
	  }
	}
	if(file_exists($cdn_image_path . $new_file)){
	  $companii_marketing[$company_from_db->code]['img'] = base_url() . 'cdn/airlines/images/' . $new_file;
	} */
	$companii_marketing[$company_from_db->code]['img'] = $this->theme->theme_url . 'assets/images/' . $company_from_db->image;
  }
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
asort($escale);
asort($escale_tur);
asort($escale_retur);
$r['filters']['companies'] = array_values($companii_marketing);
$r['filters']['companiesIndexes'] = array_combine(array_keys($companii_marketing), array_keys($r['filters']['companies']));
$r['filters']['stops'] = array_values($escale);
$r['filters']['stopsTur'] = array_values($escale_tur);
$r['filters']['stopsRetur'] = array_values($escale_retur);
$r['filters']['nonRefundables'] = array_values($nonrefundables);
$r['filters']['minPrice'] = &$min_price;
$r['filters']['maxPrice'] = &$max_price;
$r['filters']['classes'] = array_values($classes);
$r['filters']['cabinTypes'] = array_values($cabin_types);

echo json_encode($r);