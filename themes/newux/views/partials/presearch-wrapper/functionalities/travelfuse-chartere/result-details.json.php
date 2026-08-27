<?php
// ini_set('display_errors', 1);
$this->load->model('TravelFuse_model');
$this->load->model('Travelfuse/TravelFuseHotels_model');
$hotel_id = $this->input->post('ProductCode', null);
$hotel = [];
if(!empty($hotel_id) && is_numeric($hotel_id) && '' . (int)$hotel_id === '' . $hotel_id){
	$hotels = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => (int)$hotel_id]);
	if($hotels){
		$hotel = array_shift($hotels);
	}
}
$hotels = [];
if($hotel){
	$hotels_overrides = [];
	for($i=1;$i<=1;$i++){
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
		$this->TravelFuse_model->parseHotelOfferFacilities($hotel);
		
		$hotels[] = $hotel;
	}
}
$hotel = array_shift($hotels);
echo json_encode($hotel ? $hotel : []);