<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Hotel extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Hotels_model');
    parent::__construct();
  }
  public function test() {
	  $hotel_id = (int)$this->input->get('id');
		if($hotel_id <= 0){
		  $hotel_id = (int)$this->uri->segment(3);
		}
	  $h = $this->Hotels_model->loadHotel2($hotel_id);
	  echo '<pre>';
	  print_r($h);
	  die;
  }
  public function index() {
    $hotel_id = (int)$this->input->get('id');
    if($hotel_id <= 0){
      $hotel_id = (int)$this->uri->segment(3);
    }
    $this->data['hotel_details'] = false;
    if($hotel_id){
      $this->data['hotel_details'] = $this->Hotels_model->loadHotel($hotel_id);
    }
    $hotel = &$this->data['hotel_details'];
    if($hotel){
      $type = $this->input->get('type');
      if($type == 'offer'){
        $time = time();
        $next_friday = strtotime('next friday', $time);
        $next_sunday = strtotime('next sunday', $next_friday);
        $data = $this->Hotels_model->getSearchData($hotel_id);
        $data['city_id'] = $hotel->CityId;
        $data['country_id'] = $hotel->CountryId;
        $data['start_date'] = date('Y-m-d', $next_friday);
        $data['end_date'] = date('Y-m-d', $next_sunday);
        $data['occupancy'] = array(
          array(
            'adt' => 2
          )
        );
        $this->Hotels_model->setSearchData($data);
      }
      $hotel->Name = html_entity_decode($hotel->Name,ENT_QUOTES); 
      $hotel->ShortDesc = str_replace('\n', "\n",html_entity_decode($hotel->ShortDesc,ENT_QUOTES)); 
      $hotel->Address = str_replace('\n', "\n",html_entity_decode($hotel->Address,ENT_QUOTES)); 
      
      /* $config['image_library'] = 'gd2';
      $config['width'] = 635;
      $config['height'] = 400;
      $config['master_dim'] = 'height';
      $hotel_image_path = FCPATH . 'cdn/hotels/images/';

      $tmp_path = config_item('tmp_path');

      $theme_path = $this->theme->config('path');
      $theme_name = $this->theme->config('theme');
      $theme_url = $this->theme->theme_url;

      $original_filename = 'placeholder';
      $original_file = $original_filename . '.png';
      $original_filepath = $theme_path . $theme_name . '/assets/images/' . $original_file;

      $this->data['placeholder_image'] = $theme_url . '/assets/images/' . $original_file;

      $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
      $new_file =  $new_filename . '.png';
      $overwrite = false;
      if(!file_exists($hotel_image_path . $new_file) || $overwrite){
        $config['source_image'] = $original_filepath;
        $config['new_image'] = $hotel_image_path . $new_file;
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
      }
      if(file_exists($hotel_image_path . $new_file)){
        $this->data['placeholder_image'] = base_url() . 'cdn/hotels/images/' . $new_file;
      }
      $hotel->OrigImage = $hotel->Image;
      if($hotel->Image){
        $image = $hotel->Image;
        $original_filename = $hotel->Id . '-' . md5($image);
        $original_file = $original_filename . '.png';
        $original_filepath = $tmp_path . $original_file;
        $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
        $new_file =  $new_filename . '.png';
        if(!file_exists($hotel_image_path . $new_file) || $overwrite){
          if(!file_exists($original_filepath) || $overwrite){
            file_put_contents($original_filepath, fopen($image, 'r'));
          }
          $config['source_image'] = $original_filepath;
          $config['new_image'] = $hotel_image_path . $new_file;
          $this->image_lib->initialize($config);
          $this->image_lib->resize();
        }

        $hotel->Image = base_url() . 'cdn/hotels/images/' . $new_file;
      } else {
        $hotel->Image = $this->data['placeholder_image'];
      }
      $hotel->OrigGallery = $hotel->Gallery;
      $max_images = 8;
      shuffle($hotel->Gallery);
      foreach($hotel->Gallery as $k => $image){
        if(!$max_images || empty($image)){
          unset($hotel->Gallery[$k]);
          continue;
        }
        $max_images--;
        $original_filename = $hotel->Id . '-' . md5($image);
        $original_file = $original_filename . '.png';
        $original_filepath = $tmp_path . $original_file;
        $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
        $new_file =  $new_filename . '.png';
        if(!file_exists($hotel_image_path . $new_file) || $overwrite){
          if(!file_exists($original_filepath) || $overwrite){
            file_put_contents($original_filepath, fopen($image, 'r'));
          }
          $config['source_image'] = $original_filepath;
          $config['new_image'] = $hotel_image_path . $new_file;
          $this->image_lib->initialize($config);
          $this->image_lib->resize();
        }

        $hotel->Gallery[$k] = base_url() . 'cdn/hotels/images/' . $new_file;
      }
      
       */
      if(empty($hotel->Gallery)){
        $hotel->Gallery = array();
        $hotel->Gallery[] = $hotel->Image;
      }
      return $this->theme->view('trip/hotel/index', $this->data, $this);
    }
    return $this->theme->view('trip/hotel/404', $this->data, $this);
  }
  protected function validateBooking($hotel_id) {
    $hotel = $this->Hotels_model->loadHotelDetails($hotel_id);
    if(!$hotel){
      $this->outputError('Nu au putut fi preluate informatiile hotelului','warning');
    }
    $packages = $this->input->post('package');
    $code = isset($packages['code']) ? (string)$packages['code'] : '';
    $package_code = isset($packages['name']) ? (string)$packages['name'] : '';
    $rooms = isset($packages['rooms']) ? (array)$packages['rooms'] : array();
    $rooms_combinations = '';
    $error = false;
    foreach($rooms as $package_room_code => $room_data){
      if(!isset($room_data['option'], $room_data['adt'],$room_data['chdages'])){
        $error = true;
        break;
      }
      $option = (string)$room_data['option'];
      if( '' . $option === ''){
        $error = true;
        break;
      }
      if($rooms_combinations){
        $rooms_combinations .= '-';
      }
      $rooms_combinations .= $package_room_code . ':' . $option;
    }
    if($error){
      $this->outputError('Informatii invalide', 'error');
    }
    $rooms_for_package = $this->Hotels_model->loadRoomPackageRooms($code, $hotel_id, $package_code, $rooms_combinations);
    if(!$rooms_for_package){
      $this->outputError('Cererea de booking a expirat.','info');
    }
    $room_codes = array();
    $room_objects = array();

    foreach($rooms_for_package->PackageRooms->PackageRoom as $ref_index => $package_room){
      foreach($package_room->RoomRefs->RoomRef as $room_ref){
        if(!$room_ref->Selected){
          continue;
        }
        $room_codes[] = $room_ref->RoomCode;
        $room_object = new stdClass;
        $room_object->RoomCode = $room_ref->RoomCode;
        $room_object->PackageRoom_index = $ref_index;
        $room_object->PackageRoomCode = $package_room->PackageRoomCode;
        $room_object->Occupancy = $package_room->Occupancy;
        foreach($room_ref as $k=>$v){
          $room_object->$k = $v;
        }
        if(empty($room_object->Price)){
          $room_object->Price = (object)$rooms_for_package->Price;
        }
        $room_objects[$room_ref->RoomCode] = $room_object;
        break;
      }
    }
    
    $cancellation_policies = $rooms_for_package->CancellationPolicy->Policy;
    $block_payments = false;
    $block_online = false;
    $because_on_request = false;
    foreach($room_objects as $room_object){
      if($room_object->Status == 'RQ'){
        $block_online = true;
        $because_on_request = true;
        break;
      }
    }
    $today = new DateTime();
    $because_weekend = false;
    // sambata && duminica
    if($today->format('N') >= 6){
      $block_payments = true;
      $because_weekend = true;
    }
    // ore nelucratoare
    $because_of_cancellation_policy = false;
    $because_no_working_hours = false;
    $start_date = $rooms_for_package->AccommodationPeriod->StartDate;
    $date_start_date = DateTime::createFromFormat('Y-m-d', $start_date);
    $days_till_start = $today->diff($date_start_date);
    $days_till_start_formatted = intval($days_till_start->format('%a'));
    $because_too_early = false;
    // checkin azi sau maine
    if($days_till_start_formatted < 2){
      $block_payments = true;
      $because_too_early = true;
    }
    if($cancellation_policies){ 
      $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
      $because_of_cancellation_policy = false;
      foreach($cancellation_policies as $cancellation_policy){
        if(!isset($cancellation_policy->Charge, $cancellation_policy->Charge->Amount)){
          continue;
        }
        $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:sP", $cancellation_policy->Limit);
        if($min_cancellation_date_for_block > $cancellation_date){
          $block_payments = true;
          $because_of_cancellation_policy = true;
        }
      } 
    }
    $motive = '';
    if($block_payments){ 
      $motive .= '<p>';
      $motive .= 'Nu se poate plati <b>direct la agentie</b> sau prin <b>transfer bancar</b> deoarece ';
      if($because_of_cancellation_policy){
        $motive .= 'data minima de anulare este inaintea datei <b>' . $min_cancellation_date_for_block->format('d.m.Y h:i:s A') . '</b>';
      } elseif($because_too_early){
        $motive .= 'pentru rezervari cu data de checkin astazi sau maine se poate plati doar online.';
      } elseif($because_weekend){
        $motive .= 'pentru rezervari efectuate in weekend se poate plati doar online.';
      } elseif($because_no_working_hours){
        $motive .= 'pentru rezervari efectuate in intervalul orar 18:00 - 06:00 se poate plati doar online.';
      }
      $motive .= '</p>';
    }
    if($block_online){
      $motive .= '<p>';
      $motive .= 'Nu se poate plati <b>online</b> deoarece ';
      if($because_on_request){
        $motive .= 'camerele au disponibilitate: <b>La cerere</b>';
      }
      $motive .= '</p>';
    }
    $this->load->model('Options_model');
    $this->general_settings = $this->Options_model->get('general_settings');
    if(isset($this->general_settings['contact_phone_number']) && strlen($this->general_settings['contact_phone_number'])) {
      $motive .= '<div class="w-100 text-center mt-4 mb-4"><a href="tel:' . $this->general_settings['contact_phone_number'] . '" class="btn btn-primary"><i class="fa fa-phone"></i> Suna pentru suport la <br>' . (isset($this->general_settings['contact_phone_text']) ? $this->general_settings['contact_phone_text'] : $this->general_settings['contact_phone_number']) . '!</a></div>';
    }
    if($block_payments && $block_online){
	  if($because_on_request){
		$this->addMessage('<h4 class="request-offer"><i class="fa fa-warning"></i> Aceasta oferta este disponibila la cerere, nu se poate rezerva online in acest moment.</h4><p>Apasati butonul "SOLICITA OFERTA" si veti primi in cel mai scurt timp un mesaj din partea consultantilor Accent Travel & Events cu privire la disponibilitatile pentru aceasta oferta sau cea mai buna oferta similara.</p>
		<p>Va multumim pentru intelegere.</p>', 'success custom');
      } else {
		$this->addMessage('<h4><i class="fa fa-warning text-danger"></i> Din pacate platforma nu dispune de metode de plata potrivite acestei cereri.</h4>' . $motive, 'warning');
	  }
      $this->output('warning');
    } else {
      $this->addMessage('Validat cu succes');
    }
    $this->output();
  }
  public function booking() {
    $this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'hotel'));
	
    // $this->session->set_userdata('customer/trip/hotels/order_id', null);
    // $this->session->set_userdata('customer/trip/hotels/trip_order_id', null);
    $hotel_id = (int)$this->input->get('id');
    if($hotel_id <= 0){
      $hotel_id = (int)$this->uri->segment(3);
    }
    if ($this->input->is_ajax_request()) {
      return $this->validateBooking($hotel_id);
    }
    $hotel = $this->Hotels_model->loadHotelDetails($hotel_id);
    if(!$hotel){
      $this->redirect('trip/hotels','Nu au putut fi preluate informatiile hotelului','warning');
    }
    $hotel->Name = html_entity_decode($hotel->Name,ENT_QUOTES); 
    $hotel->ShortDesc = str_replace('\n', "\n",html_entity_decode($hotel->ShortDesc,ENT_QUOTES)); 
    $hotel->Address = str_replace('\n', "\n",html_entity_decode($hotel->Address,ENT_QUOTES)); 
    
    $this->data['hotel_details'] = &$hotel;
    $packages = $this->input->post('package');
    $code = isset($packages['code']) ? (string)$packages['code'] : '';
    $package_code = isset($packages['name']) ? (string)$packages['name'] : '';
    $rooms = isset($packages['rooms']) ? (array)$packages['rooms'] : array();
    $rooms_combinations = '';
    $error = false;
    foreach($rooms as $package_room_code => $room_data){
      if(!isset($room_data['option'], $room_data['adt'],$room_data['chdages'])){
        $error = true;
        break;
      }
      $option = (string)$room_data['option'];
      if( '' . $option === ''){
        $error = true;
        break;
      }
      if($rooms_combinations){
        $rooms_combinations .= '-';
      }
      $rooms_combinations .= $package_room_code . ':' . $option;
    }
    if($error){
      $this->redirect('trip/hotel/' . $hotel_id,'Informatii invalide', 'error');
    }
    // $room_package = $this->Hotels_model->loadRoomPackage($code, $hotel_id, $package_code);
    $rooms_for_package = $this->Hotels_model->loadRoomPackageRooms($code, $hotel_id, $package_code, $rooms_combinations);
    if(!$rooms_for_package){
      // echo '<pre>';
      // print_r($this->Trip_model->get_api()->calls);
      // die;
			// $this->redirect('trip/hotel/' . $hotel_id, $this->getTripError('Cererea de booking a expirat.'),'info');
      $this->redirect('trip/hotel/' . $hotel_id,'Cererea de booking a expirat.','info');
    }
    $room_codes = array();
    $room_objects = array();
    /* if(782506 == $hotel_id && $this->user->can('zxcvzxcv')){
      echo '<pre>';
      print_r($rooms_for_package);
      die;
    } */
    
    foreach($rooms_for_package->PackageRooms->PackageRoom as $ref_index => $package_room){
      foreach($package_room->RoomRefs->RoomRef as $room_ref){
        if(!$room_ref->Selected){
          continue;
        }
        $room_codes[] = $room_ref->RoomCode;
        $room_object = new stdClass;
        $room_object->RoomCode = $room_ref->RoomCode;
        $room_object->PackageRoom_index = $ref_index;
        $room_object->PackageRoomCode = $package_room->PackageRoomCode;
        $room_object->Occupancy = $package_room->Occupancy;
        foreach($room_ref as $k=>$v){
          $room_object->$k = $v;
        }
        if(empty($room_object->Price) || empty($room_object->Price->Amount)){
          $room_object->Price = (object)$rooms_for_package->Price;
        }
        $room_objects[$room_ref->RoomCode] = $room_object;
        break;
      }
    }
    $this->data['rooms_for_package'] = &$rooms_for_package;
    $this->data['room_objects'] = &$room_objects;
    $this->data['room_codes'] = &$room_codes;
    $this->data['code'] = $code;
    $this->data['hotel_id'] = $hotel_id;
    $this->data['package_code'] = $package_code;
    $this->data['rooms_combinations'] = $rooms_combinations;
/* if(defined('TUDORTESTING') && TUDORTESTING){
	
	echo '<pre>';
	print_r($rooms_for_package);
	// print_r($this->Trip_model->get_api()->calls);
	// print_r(htmlspecialchars(json_encode($this->Trip_model->get_api()->calls)));
	die;
} */
    $this->theme->view('trip/hotel/booking', $this->data, $this);
  }
  public function checkout() {
	$this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'hotel'));
	
    $this->makeResponseGlobal();
    if ($this->input->is_ajax_request()) {
      $this->load->library('form_validation');
      $response = modules :: run('Trip/checkout/Checkout/validate', 'hotel');
      if(!$response){
        $this->output('error');
      }
      if (false === $this->form_validation->run()) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      $response = modules :: run('Trip/checkout/Checkout/service', 'hotel', false);
      if(!$response){
        $this->output('error');
      }
      $this->addMessage('Serviciul a fost validat.');
      return $this->output();
    }
    $status = modules :: run('Trip/checkout/Checkout/service', 'hotel', true);
    if(false === $status){
      $this->saveMessagesInSession();
      $this->redirect('trip/checkout/failure');
    }
    if(true === $status){
      $this->redirect('trip/checkout/success');
    }
    // $this->outputError('Blocat intentionat pentru adaugare de functionalitate');
  }
}