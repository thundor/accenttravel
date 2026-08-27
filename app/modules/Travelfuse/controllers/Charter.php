<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Charter extends MX_Controller {
  function __construct() {
    $this->load->model('TravelFuse_model');
	$this->load->model('Travelfuse/TravelFuseHotels_model');
    parent::__construct();
  }
  public function index() {
	$hotel_id = (int)$this->input->get('id');
    if($hotel_id <= 0){
      $hotel_id = (int)$this->uri->segment(3);
    }  
	
	$this->data['hotel_details'] = false;
    if($hotel_id){
		$hotel_details = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => $hotel_id], 986400, true, 'object');
		$hotel = null;
		if($hotel_details){
		  $hotel = $hotel_details[0];
		}
		if($hotel){
		  $hotels_overrides = $this->TravelFuseHotels_model->getTravelfuseOverrides([$hotel->Id], []);
		  
		  if(isset($hotels_overrides[$hotel->Id])){
			$hotel_override = $hotels_overrides[$hotel->Id];
			// if(empty($hotel_override->status)){
				// continue;
			// }
			
			$hotel->ShortContent = $hotel_override->ShortContent;
			$hotel->Name = $hotel_override->Name;
			$hotel->Stars = $hotel_override->Stars;
			$hotel->Facilities = $hotel_override->Facilities;
			$hotel->MainImage = $hotel_override->MainImage;
			$hotel->Content = (object)[];
			$hotel->Content->Content = $hotel_override->Content;
			$hotel->Content->ImageGallery = $hotel_override->ImageGallery;
			
		}
		  
		  $this->data['hotel_details'] = $hotel;
			return $this->theme->view('travelfuse/charter/index', $this->data, $this);
		  // dd($hotel);
		}
    }
	return $this->theme->view('travelfuse/charter/404', $this->data, $this);
  }
}