<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
$this->load->model('Travelfuse/TravelFuseHotels_model');

if(empty($this->input->post())){
	$_POST = [
		'Transport' => 'plane',
		'Destination' => 140,
		'DestinationType' => 'county',
		'DepCityCode' => 3302,
		'CheckIn' => '2025-04-30',
		'CheckOut' => '2025-05-28',
		'Adults' => [2],
		'Children' => 0,
		'Provider' => NULL,
		'ChildrenAge' => NULL,
		'ProductCode' => 104476,
		'OfferId' => NULL,
	];
}

$hotels_machine = $this->TravelFuse_model->charterOfferDetails([
	'Transport' => $this->input->post('Transport', null),
	'Destination' => $this->input->post('Destination', null),
	'DestinationType' => $this->input->post('DestinationType', null),
	'DepCityCode' => $this->input->post('DepCityCode', null),
	'CheckIn' => $this->input->post('CheckIn', null),
	'CheckOut' => $this->input->post('CheckOut', null),
	'Adults' => $this->input->post('Adults', null),
	'Children' => $this->input->post('Children', null),
	'Provider' => $this->input->post('Provider', null),
	'ChildrenAge' => $this->input->post('ChildrenAge', null),
	'ProductCode' => $this->input->post('ProductCode', null),
	'OfferId' => $this->input->post('OfferId', null),
]);
$hotels = [];
if($hotels_machine){
	$hotels_overrides = [];
	foreach ($hotels_machine as $key => $hotel) {
		$hotels_overrides = array_replace($hotels_overrides, $this->TravelFuseHotels_model->getTravelfuseOverrides([$hotel->Id], []));
		if(!$hotels_overrides){
			// $hotels_details = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => $hotel->Id]);
			// if($hotels_details){
				// $hotel = (object)array_replace((array)$hotel, (array)$hotels_details[0]);
			// }
		}
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
			$hotel->Content = (object)[];
			$hotel->Content->Content = $hotel_override->Content;
			$hotel->Content->ImageGallery = $hotel_override->ImageGallery;
			
		}
		if($hotel->Offers){
			if(is_object($hotel->Offers)) $hotel->Offers = array_values((array)$hotel->Offers);
			// dd($hotel->Offers);
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
			
			$hotel->Offers = array_values($hotel->Offers);
		}
		$this->TravelFuse_model->parseHotelOfferFacilities($hotel);
		$hotels[] = $hotel;
	}
}
$offer_details = null;
if(empty($save_data)){
	echo json_encode($hotels ? $hotels : []);
} else {
	$offer_details = array_shift($hotels);
	$offer_details['Offer'] = null;
	if(isset($offer_details['Offers'][0])){
		$offer_details['Offer'] = $offer_details['Offers'][0];
	}
}