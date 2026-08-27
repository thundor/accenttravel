<?php
// ini_set('display_errors', 1);
$return_results = 1;
require(__DIR__ . '/offer-list.json.php');
if(!isset($r) && isset($response)){
	$r = json_decode($response, true);
}
$sql = "SELECT Name as '0', COALESCE(fa._name_ro, fa._name_en, fa.Name) as '1' FROM `tf_facilities` fa WHERE `type` = 'facility'";
$facilities_names = $this->query($sql)->result('array');
$facilities_names = array_combine(array_column($facilities_names, '0'), array_column($facilities_names, '1'));
$pois_names = [];
$r['filters'] = [];

foreach($r['_embedded']['hotels'] as &$hotel){
	$stars = intval($hotel['Stars'] ?? 0);
	if(!isset($r['filters']['stars'])){
		$r['filters']['stars'] = [];
	}
	
	if(!isset($r['filters']['nonRefundables'])){
		$r['filters']['nonRefundables'] = [];
	}
	
	if(!empty($hotel['NonRefundable'])){
		$r['filters']['nonRefundables'][1] = 1;
	} elseif(!empty($hotel['Refundable'])){
		$r['filters']['nonRefundables'][0] = 0;
	}
	
	$price = floatval($hotel['MinPrice']);
	if(!isset($r['filters']['minPrice'])){
		$r['filters']['minPrice'] = $price;
		$r['filters']['maxPrice'] = $price;
	} else {
		if($price < $r['filters']['minPrice']){
			$r['filters']['minPrice'] = $price;
		}
		if($price > $r['filters']['minPrice']){
			$r['filters']['maxPrice'] = $price;
		}
	}
	if(!in_array($stars, $r['filters']['stars'])){
		$r['filters']['stars'][] = $stars;
	}
	
	$facilities = preg_split('/\s*[,]\s*/', $hotel['Facilities'] ?? '');
	$facilities = array_filter($facilities);
	foreach($facilities as $facility){
		$facility = trim($facility);
		// $hotel['facilities'][$facility] = $facilities_names[$facility] ?? $facility;
		$r['filters']['facilities'][$facility] = $facilities_names[$facility] ?? $facility;
	}
	
	$pois = preg_split('/\s*[,]\s*/', $hotel['Pois'] ?? '');
	$pois = array_filter($pois);
	foreach($pois as $poi){
		// $hotel['pois'][$poi] = $pois_names[$poi] ?? $poi;
		$r['filters']['pois'][$poi] = $pois_names[$poi] ?? $poi;
	}
}
if(isset($r['filters']['stars'])){
	sort($r['filters']['stars']);
	$r['filters']['stars'] = array_values($r['filters']['stars']);
}
if(isset($r['filters']['nonRefundables'])){
	sort($r['filters']['nonRefundables']);
	$r['filters']['nonRefundables'] = array_values($r['filters']['nonRefundables']);
}
if(isset($r['filters']['facilities'])){
	asort($r['filters']['facilities']);
}
if(isset($r['filters']['pois'])){
	asort($r['filters']['pois']);
}

echo json_encode($r);