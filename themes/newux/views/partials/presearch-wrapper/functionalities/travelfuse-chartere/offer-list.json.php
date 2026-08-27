<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
$this->load->model('Travelfuse/TravelFuseHotels_model');
$this->load->model('TravelFuse_model');
$cities = [];

if(empty($this->input->post())){
	$_POST = [
		'Transport' => 'plane',
		'Destination' => 980,
		'DestinationType' => 'county',
		'DepCityCode' => 3302,
		'CheckIn' => '2025-06-27',
		'CheckOut' => '2025-07-04',
		'Adults' => [2],
	];
}

// dd(BASEPATH);

$response_file = $this->TravelFuse_model->charterOfferList([
	'Transport' => $this->input->post('Transport', null),
	'Destination' => $this->input->post('Destination', null),
	'DestinationType' => $this->input->post('DestinationType', null),
	'DepCityCode' => $this->input->post('DepCityCode', null),
	'CheckIn' => $this->input->post('CheckIn', null),
	'CheckOut' => $this->input->post('CheckOut', null),
	'Adults' => $this->input->post('Adults', null),
	'Children' => intval($this->input->post('Children', null) ?? 0),
	'ChildrenAge' => $this->input->post('ChildrenAge', null) ?? [],
	'Provider' => $this->input->post('Provider', null),
// ], [], 986400, true, false);
], [], 21600, true, false);


$hotels = [];
if($response_file){
	if(true || !is_file(dirname($response_file) . '/parsed-file.json.gz')){
	// if(!is_file(dirname($response_file) . '/parsed-file.json.gz')){
		$fp = fopen(dirname($response_file) . '/parsed-file-partial.json', 'w');
		fwrite($fp, '[');
		spl_autoload_register(require BASEPATH . 'vendor/json-machine-master/src/autoloader.php');
		$hotel_ids = [];
		/* $hotels_machine = \JsonMachine\Items::fromFile($response_file, [
			'decoder' => new \JsonMachine\JsonDecoder\PassThruDecoder(true),
			'pointer' => [
				'/-/Id',
			]
		]); */
		/* foreach ($hotels_machine as $key => $hotel_id) {
			$hotel_ids[] = $hotel_id;
		} */
		$hotels_overrides = [];
		/* if($hotel_ids){
			$hotels_overrides = $this->TravelFuseHotels_model->getTravelfuseOverrides($hotel_ids, [
				'no_content' => 1,
				'first_image_only' => 1,
			]);
		} */
		
		
		// $hotels = array_combine(array_column($hotels, 'Id'), $hotels);
		
		$hotels_machine = \JsonMachine\Items::fromFile($response_file, [
			// 'decoder' => new \JsonMachine\JsonDecoder\ExtJsonDecoder(true),
		]);
		$hotels = [];
		$hotel_index = 0;
		foreach ($hotels_machine as $key => $hotel) {
			if(empty($hotel->Id)){
				continue;
			}
			$hotels_overrides = array_replace($hotels_overrides, $this->TravelFuseHotels_model->getTravelfuseOverrides([$hotel->Id], [
				'no_content' => 1,
				'first_image_only' => 1,
			]));
			if(isset($hotels_overrides[$hotel->Id])){
				$hotel_override = $hotels_overrides[$hotel->Id];
				if(empty($hotel_override->status)){
					continue;
				}
				$hotel->ShortContent = $hotel_override->ShortContent;
				$hotel->Name = $hotel_override->Name;
				$hotel->Stars = $hotel_override->Stars;
				$hotel->Facilities = $hotel_override->Facilities;
				$hotel->MainImage = $hotel_override->MainImage;
			}
			$hotel->Content = null;
			if(!empty($hotel->Offers)){
				$hotel->Offers = array_map(function($o){
					$o->_Special = 0;
					$o->_SpecialPercent = 0;
					if(!empty($o->InitialPrice) && !empty($o->Price) && (floatval($o->InitialPrice) > floatval($o->Price))){
						$o->_Special = 1;
						$o->_SpecialPercent = 0 + number_format(ceil(10000 * (floatval($o->InitialPrice) - floatval($o->Price))/floatval($o->InitialPrice))/100,2, '.', '');
					}
					return $o;
				},$hotel->Offers);
				
				$hotel->Offers = array_filter($hotel->Offers, function($o){
					if(!empty($o->Items)){
						$keep_offer = true;
						$o->Items = array_filter($o->Items, function($i) use (&$keep_offer, $o){
							if(!$keep_offer) return false;
							if(empty($i->Merch)) return false;
							if(empty($i->Merch->type)) return false;
							if($i->Merch->type == 'Room'){
								if(empty($i->Availability) || 'no' == $i->Availability){
									$keep_offer = false;
									return false;
								}
							} else {
								if(!empty($i->Availability) && 'no' == $i->Availability) return false;
							}
							return true;
						});
						$o->Items = array_values($o->Items);
						if(!$keep_offer) return false;
					}
					if(empty($o->Items)) return false;
					return true;
				});
			}
			if(empty($hotel->Offers)) continue;
			$hotel->Offers = array_values($hotel->Offers);
			$this->TravelFuse_model->parseHotelOfferFacilities($hotel);
			
			// dd($hotel);
			if($hotel_index){
				fwrite($fp, ',');
			}
			fwrite($fp, json_encode($hotel));
			$hotel_index ++;
			// $hotels[] = $hotel;
			// dd($hotel);
		}
		fwrite($fp, ']');
		fclose($fp);
		rename(dirname($response_file) . '/parsed-file-partial.json', dirname($response_file) . '/parsed-file.json');
		gzCompressFile(dirname($response_file) . '/parsed-file.json');
	}
	// dd(array_slice($hotels, 0, 5));
	
	header("Content-Encoding: gzip");
	$fp = fopen(dirname($response_file) . '/parsed-file.json.gz', 'rb');
	fpassthru($fp);
	fclose($fp);
	return;
} else {
	// $this->outputError();
}


echo '[]';
return;
$filters = [];


if($hotels){
	// $hotels = array_filter($hotels, function($city){
		// return !empty($city['Offers']);
	// });
	$this->TravelFuse_model->parseHotelsOfferFacilities($hotels);
	// dd($hotels[0]);
	$hotels = array_combine(array_column($hotels, 'Id'), $hotels);
	$ids = array_keys($hotels);
	// $hotels_details = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => implode(',',$ids)]);
	// $hotels_details = array_combine(array_column($hotels_details, 'Id'), $hotels_details);
	$hotels_2 = $this->TravelFuseHotels_model->getTravelfuseOverrides($ids);
	
	foreach($hotels_2 as $hotel){
		if(isset($hotels[$hotel->Id])){
			if(empty($hotel->status)){
				unset($hotels[$hotel->Id]);
				// unset($hotels_details[$hotel->Id]);
				continue;
			}
			$hotels[$hotel->Id]['ShortContent'] = $hotel->ShortContent;
			$hotels[$hotel->Id]['Content']['Content'] = $hotel->Content;
			$hotels[$hotel->Id]['Name'] = $hotel->Name;
			$hotels[$hotel->Id]['Stars'] = $hotel->Stars;
			$hotels[$hotel->Id]['Facilities'] = $hotel->Facilities;
			$hotels[$hotel->Id]['MainImage'] = $hotel->MainImage;
			$hotels[$hotel->Id]['Content']['ImageGallery'] = $hotel->ImageGallery;
			/* if($hotel->Id == 10944){
			echo json_encode($hotel);
			// echo json_encode($hotels_details[$hotel->Id]);
			exit;
			} */
			// dd($hotels[$hotel->Id]);
		}
	}	
	// $hotels = array_replace_recursive($hotels_details, $hotels);
	array_walk_recursive($hotels, function(&$v){
		if($v && !is_numeric($v)){
			$v = trim(htmlspecialchars_decode($v));
		}
		return $v;
	});
	$hotels = array_values($hotels);
}

echo json_encode($hotels ? array_map(function($city){
			// dd($city);
	// $city['Id'] = html_entity_decode($city['Date'], ENT_QUOTES);
	// $city['Name'] = html_entity_decode($city['Date'], ENT_QUOTES);
	// $city['Name'] = preg_replace('~\\\+~','',$city['Name']);
	return $city;
}, $hotels) : []);