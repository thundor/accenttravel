<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Weekend extends MX_Controller {
  function __construct() {
    parent :: __construct();
  }
  
  public function cron($type = '') {
    // php index.php backend offers weekend cron "hotel"
    $this->load->model('Options_model');
    $this->load->model('Trip/Offer_weekend_model');
    $this->settings = $this->Options_model->get('offers_weekend_settings');
    if(!$this->settings){
      $this->settings = array();
    }
    if($type){
      if(method_exists($this,'cron_' . $type)){
        $this->{'cron_' . $type}();
      }
    } else {
      $types = array(
        'hotel',
        'package',
      );
      foreach($types as $type){
        $this->{'cron_' . $type}();
      }
    }
    exit;
  }
  protected function cron_hotel(){
    $this->load->helper('cron');
    cron_lock_file('hotel_weekend_lock_file_cron', '30 minutes');
    ini_set('max_execution_time', 30 * 60);
    echo "Cron hotel".PHP_EOL;
    $this->load->model('Trip/Hotels_model');
    if(!session_id()){
      session_start();
    }
    // $this->Trip_model->api->setUseApi(false);
    $time = time();
    $yesterday = strtotime('20 minutes ago', $time);
    $next_friday = strtotime('next friday', $time);
    $next_sunday = strtotime('next sunday', $next_friday);
    
    echo "Interval " . date('Y-m-d', $next_friday) . ' - ' . date('Y-m-d', $next_sunday) .PHP_EOL;
    $this->db->where('(`time_modified` IS NULL OR `time_modified` < "' . date('Y-m-d H:i:s', $yesterday) . '")');
    // $this->db->where('(`time_modified` IS NULL)');
    $offers = $this->Offer_weekend_model->getOffers(array(
      'type' => 'hotel',
    ));
    $hotels = array();
    $occupancy = array(
      array(
        'adt' => 2,
      ),
    );
    foreach($offers as $k=>$offer){
      $container_id = 'cli_weekend_' . $time;
      // if($k){
        // echo 'Force break'. PHP_EOL;
        // break;
      // }
      $hotel_id = $offer->type_id;
      $zone = $offer->zone;
      echo 'Checking hotel ID ' . $hotel_id . PHP_EOL;
      
      $retries = 30;
      $max_retries = $retries;
      $response = null;
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $this->Trip_model->get_api()->generateToken();
        $response = $this->Hotels_model->initiateSearch(array(
          'occupancy' => $occupancy,
          'start_date' => '' . date('Y-m-d', $next_friday),
          'end_date' => '' . date('Y-m-d', $next_sunday),
          'hotel_id' => $hotel_id,
          'container_id' => $container_id,
        ));
        $max_retries --;
        if (!$response) {
          echo 'TRIP error: search initiation returned no response.' . PHP_EOL;
          continue;
        }
        if (property_exists($response,'Status')) {
          echo 'TRIP error: response is not interpretable.' . PHP_EOL;
          $response = null;
          continue;
        }
        if (empty($response->{$container_id})) {
          echo 'TRIP error: container not found.' . PHP_EOL;
          $response = null;
          continue;
        }
        break;
      }
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $search_response = array_pop($response->{$container_id});
      if(!$search_response->Status){
        echo 'The search failed.' . PHP_EOL;
        continue;
      }
      $index_id = $search_response->Id;
      echo 'Search index ' . $index_id . PHP_EOL;
      
      $max_retries = $retries;
      $response = null;
      while($max_retries){
        if($max_retries < $retries){
          sleep(2);
        }
        $max_retries --;
        $this->Trip_model->get_api()->generateToken();
        $response = $this->Hotels_model->inspectSearchIndex($index_id);
        if (!$response) {
          echo 'TRIP error: search index inspect returned no response' . PHP_EOL;
          continue;
        }
        if (empty($response->code)) {
          echo 'TRIP error: code parameter missing in search index result, reinitating.' . PHP_EOL;
          $response = null;
          continue;
        }
        break;
      }
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $code = $response->code;
      echo 'Search inspected ' . $code . PHP_EOL;
      
      $max_retries = $retries;
      $response = null;
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $max_retries --;
        $response = $this->Hotels_model->loadHotels($code, true);
        if (!$response) {
          echo 'TRIP error: search index inspect returned no response' . PHP_EOL;
          continue;
        }
        if(property_exists($response, 'status')){
          if (empty($response->status)) {
            echo 'TRIP error: loading failed.' . PHP_EOL;
            $response = null;
            break;
          }
          if($response->status == 2){
            $this->addMessage('TRIP loading: search is ' . $response->message);
            $response = null;
            continue;
          }
        }
        if(empty($response->total_items)){
          // print_r($response); die;
          $response = null;
          echo 'Hotel not found' . PHP_EOL;
          break;
        }
        break;
      }
      // print_r($this->Trip_model->api->call);
      // die;
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $list_hotel = $response->_embedded->hotels[0];
      /* 
      $response = $this->Hotels_model->loadRoomPackages($code,$hotel_id);
      if(!$response){
        echo 'TRIP error: could not retrieve room packages' . PHP_EOL;
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $price = 0;
      $occupancy = array();
      foreach($response->_embedded->packages as $package){
        $price = $package->Price->Amount;
        $occupancy = $package->PackageRooms->PackageRoom[0]->Occupancy;
        break;
      } */
      $hotel = $this->Hotels_model->loadHotelDetails($hotel_id);
      $hotel_data = new stdClass;
      $hotel_data->Code = $code;
      $hotel_data->Occupancy = $occupancy;
      $hotel_data->MinPrice = $list_hotel->MinPrice;
      $hotel_data->Currency = $list_hotel->Currency;
      $hotel_data->Image = $hotel->Image;
      $hotel_data->Address = $hotel->Address;
      $hotel_data->Name = $hotel->Name;
      $hotel_data->Stars = $hotel->Stars;
      $hotel_data->Type = $hotel->Type;
      $hotel_data->CountryId = $hotel->CountryId;
      $hotel_data->CityId = $hotel->CityId;
      $hotel_data->CountryName = $hotel->CountryName;
      $hotel_data->CountryCode = $hotel->CountryCode;
      $hotel_data->CityName = $hotel->CityName;
      $hotel_data->Phone = $hotel->Phone;
      $hotel_data->Fax = $hotel->Fax;
      $hotel_data->Email = $hotel->Email;
      $hotel_data->Lat = $hotel->Lat;
      $hotel_data->Lng = $hotel->Lng;
      
      $offer_data = array(
        'id' => $offer->id,
        'price' => $hotel_data->MinPrice,
        'stars' => $hotel->Stars,
        'city_name' => $hotel->CityName,
        'data' => serialize($hotel_data),
        'time_modified' => date('Y-m-d H:i:s'),
      );
      $this->Offer_weekend_model->updateOffer($offer_data);
      echo 'Hotel actualizat cu succes' . PHP_EOL;
    }
    echo 'DONE Weekend Hotels' . PHP_EOL;
    cron_unlock_file('hotel_weekend_lock_file_cron');
  }
  protected function cron_package(){
    $this->load->helper('cron');
    cron_lock_file('package_weekend_lock_file_cron', '30 minutes');
    ini_set('max_execution_time', 30 * 60);
    echo "Cron package".PHP_EOL;
    $this->load->model('Trip/Packages_model');
    if(!session_id()){
      session_start();
    }
    // $this->Trip_model->api->setUseApi(false);
    $time = time();
    $yesterday = strtotime('20 minutes ago', $time);
    $next_friday = strtotime('next friday', $time);
    $next_sunday = strtotime('next sunday', $next_friday);
    
    echo "Interval " . date('Y-m-d', $next_friday) . ' - ' . date('Y-m-d', $next_sunday) .PHP_EOL;
    
    // $this->db->where('(`time_modified` IS NULL OR `time_modified` < "' . date('Y-m-d H:i:s', $yesterday) . '")');
    $offers = $this->Offer_weekend_model->getOffers(array(
      'type' => 'package',
    ));
    // print_R($offers);
    // die;
    $hotels = array();
    foreach($offers as $k=>$offer){
      $container_id = 'cli_' . $time;
      // if($k){
        // echo 'Force break'. PHP_EOL;
        // break;
      // }
      $package_id = $offer->type_id;
      // if($package_id == 4874){
        // continue;
      // }
      $zone = $offer->zone;
      echo 'Checking package ID ' . $package_id . PHP_EOL;
      
      // for cache purposes
      $this->Packages_model->loadPackageDetails($package_id);
      
      $retries = 10;
      $max_retries = $retries;
      $response = null;
      $nights = 3;
      $occupancy = array(
        array(
          'adt' => 2,
        ),
      );
      $search_data = array(
        'occupancy' => $occupancy,
        'start_date' => '' . date('Y-m-d', $next_friday),
        'end_date' => '' . date('Y-m-d', $next_sunday),
        'package_id' => $package_id,
        'nights' => $nights,
        'container_id' => $container_id,
      );
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $this->Trip_model->get_api()->generateToken();
        $response = $this->Packages_model->initiateSearch($search_data);
        $max_retries --;
        if (!$response) {
          echo 'TRIP error: search initiation returned no response.' . PHP_EOL;
          continue;
        }
        if (property_exists($response,'Status')) {
          echo 'TRIP error: response is not interpretable.' . PHP_EOL;
          $response = null;
          continue;
        }
        if (empty($response->{$container_id})) {
          echo 'TRIP error: container not found.' . PHP_EOL;
          $response = null;
          continue;
        }
        break;
      }
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      
      $search_response = array_pop($response->{$container_id});
      if(!$search_response->Status){
        echo 'The search failed.' . PHP_EOL;
        continue;
      }
      $index_id = $search_response->Id;
      echo 'Search index ' . $index_id . PHP_EOL;
      
      $max_retries = $retries;
      $response = null;
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $max_retries --;
        $response = $this->Packages_model->inspectSearch($container_id);
        if (!$response) {
          echo 'TRIP error: search inspect returned no response' . PHP_EOL;
          continue;
        }
        $search_index_response = array_pop($response);
        if (empty($search_index_response->Status)) {
          echo 'TRIP error: the search failed.' . PHP_EOL;
          $response = null;
          break;
        }
        $response = $search_index_response;
        if ($search_index_response->Status == 1) {
          break;
        }
      }
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $retries = 10;
      $max_retries = $retries;
      $response = null;
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $max_retries --;
        // $this->Trip_model->get_api()->generateToken();
        $response = $this->Packages_model->inspectSearchIndex($index_id);
        if (!$response) {
          // print_r($this->Trip_model->api->call);
          // die;
          echo 'TRIP error: search index inspect returned no response' . PHP_EOL;
          continue;
        }
        if (empty($response->code)) {
          // echo '<pre>';
          // print_r($this->Trip_model->api->calls);
          // die;
          echo 'TRIP error: code parameter missing in search index result, reinitating.' . PHP_EOL;
          $response = null;
          continue;
        }
        break;
      }
      if(!$response || empty($response->code)){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      
      $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
      $cache_request = array(
        $search_data['start_date'],
        $search_data['occupancy'],
        (int)$search_data['nights'],
      );
      $cache_storage_path = 'trip/package/' . (int)$package_id . '/search/';
      $cache_hash = crc32(json_encode($cache_request));
      $cache_storage_code_path = 'trip/package/' . (int)$package_id . '/code/';
      if(strlen($code)){
        $cache_time = strtotime('tomorrow') - time();
        if($cache_time > 0){
          setCacheStorage($cache_storage_code_path . crc32($code));
          $cached_search_data = new stdClass;
          $cached_search_data->container_id = $container_id;
          $cached_search_data->index_id = $index_id;
          $this->cache->save($cache_storage_code_path . crc32($code) . '/search', $cached_search_data, $cache_time);
          
          setCacheStorage($cache_storage_path);
          $this->cache->save($cache_storage_path . $cache_hash, $code, $cache_time);
        }
      }
      echo 'Search inspected ' . $code . PHP_EOL;
      
      $packages = $this->Packages_model->loadPackages($code);
      if(!$packages){
        echo 'TRIP error: could not retrieve result packages' . PHP_EOL;
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $package = $packages->_embedded->packages[0];
      $stars = 0;
      $city_name = '';
      $entries = $this->Packages_model->loadPackageEntries($package->Id,$code);
      if($entries){
        foreach($entries->_embedded->entries as $entry){
          $entry_id = $entry->EntryId;
          $entry_details = $this->Packages_model->loadPackageEntryDetails($package->Id,$code, $entry->EntryId, $entry->RateGroupId);
          if($entry_details){
            foreach($entry_details->Accommodation as $accommodations){
              foreach($accommodations as $accommodation){
                foreach($accommodation->Services as $service){
                  $city_name = $service->CityName;
                  $stars = $service->UnitStars;
                  break;
                }
              }
            }
            break;
          }
        }
      }
      $package->Occupancy = $occupancy;
      $package->Nights = $nights;
      
      $offer_data = array(
        'id' => $offer->id,
        'price' => $package->MinPrice,
        'stars' => $stars,
        'category' => $package->Category,
        'city_name' => $city_name,
        'data' => serialize($package),
        'time_modified' => date('Y-m-d H:i:s'),
      );
      $this->Offer_weekend_model->updateOffer($offer_data);
      echo 'Pachet actualizat cu succes' . PHP_EOL;
    }
    echo 'DONE Weekend Packages' . PHP_EOL;
    cron_unlock_file('package_weekend_lock_file_cron');
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('offers_weekend_settings');
    if(!$settings){
      $settings = array();
    }
    
    $this->data = $settings;
    $this->theme->view('backend/offers/weekend', $this->data);
  }
  public function loadHotel() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    $hotel_id = (int) $this->input->get('id');
    
    $this->load->model('Trip/Offer_weekend_model');
    
    $offers = $this->Offer_weekend_model->getOffers(array(
      'type_id' => $hotel_id,
      'type' => 'hotel',
    ));
    if($offers){
      $this->outputError('Hotelul este deja in lista');
    }
    
    
    $this->load->model('Trip/Hotels_model');
    $hotel = $this->Hotels_model->loadHotelDetails($hotel_id);
    if(!$hotel){
      $this->outputTripError('Nu s-a putut prelua hotelul');
    }
    
    $hotel->Name = html_entity_decode($hotel->Name,ENT_QUOTES); 
    $hotel->ShortDesc = str_replace('\n', "\n", html_entity_decode($hotel->ShortDesc,ENT_QUOTES)); 
    $hotel->Address = str_replace('\n', "\n", html_entity_decode($hotel->Address,ENT_QUOTES)); 
    
    $this->load->model('Country_model');
    $country = $this->Country_model->getCountries(array(
      'iso_2' => trim($hotel->CountryCode),
      'select' => array(
        '*',
        'IFNULL(`name_RO`,`name`) as output_name',
      ),
      'return_row' => true,
    ));
    $this->data = array(
      'hotel' => $hotel,
      'country' => $country,
    );
    $this->output();
  }
  public function loadPackage() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    $package_id = (int) $this->input->get('id');
    
    $this->load->model('Trip/Offer_weekend_model');
    
    $offers = $this->Offer_weekend_model->getOffers(array(
      'type_id' => $package_id,
      'type' => 'package',
    ));
    if($offers){
      $this->outputError('Packetul este deja in lista');
    }
    
    $this->load->model('Trip/Packages_model');
    $package = $this->Packages_model->loadPackageDetails($package_id);
    if(!$package){
      $this->outputTripError('Nu s-a putut prelua pachetul');
    }
    $package->Name = html_entity_decode($package->Name,ENT_QUOTES); 
    $package->Description = str_replace('\n', "\n", html_entity_decode($package->Description,ENT_QUOTES)); 

    $this->data = array(
      'package' => $package,
    );
    $this->output();
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-save')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    $this->load->library('form_validation');
    
    $zones_post = $this->input->post('zone');
    if(!$zones_post || !is_array($zones_post)){
      $zones_post = array();
    }
    $should_validate = false;
    
    $zones_data = array();
    $delete_items = array();
    $add_items = array();
    $zones = array();
    foreach($zones_post as $zone => $zone_info){
      if($zone == 'delete'){
        $delete_items = $zone_info;
        continue;
      }
      $zones[] = $zone;
      $fake_post_prefix = 'zone_' . $zone . '_';
      $should_validate = true;
      $_POST[$fake_post_prefix . 'name'] = isset($zone_info['name']) ? $zone_info['name'] : null;
      $this->form_validation->set_rules($fake_post_prefix . 'name', 'Zona ' . $zone . ' Nume', 'trim|required');
      $zone_data = array(
        'name'=>isset($zone_info['name']) ? trim($zone_info['name']) : '',
        'text'=>isset($zone_info['text']) ? trim($zone_info['text']) : '',
        'order'=>isset($zone_info['order']) && $zone_info['order'] >=0 ? (int)$zone_info['order'] : '',
        'enabled'=>isset($zone_info['enabled']) && $zone_info['enabled'] ? 1 : 0,
      );
      $zones_data[$zone] = $zone_data;
      if(isset($zone_info['hotels']) && $zone_info['hotels'] && is_array($zone_info['hotels'])){
        foreach($zone_info['hotels'] as $hotel_id => $hotel_data){
          $add_items[] = array(
            'type_id' => (int)$hotel_id,
            'type' => 'hotel',
            'zone' => $zone,
            'name' => trim($hotel_data['n']),
            'stars' => (int)$hotel_data['s'],
          );
        }
      }
      if(isset($zone_info['packages']) && $zone_info['packages'] && is_array($zone_info['packages'])){
        foreach($zone_info['packages'] as $package_id => $package_data){
          $add_items[] = array(
            'type_id' => (int)$package_id,
            'type' => 'package',
            'zone' => $zone,
            'name' => trim($package_data['n']),
            'category' => trim($package_data['c']),
          );
        }
      }
    }
    if ($should_validate && $this->form_validation->run() == FALSE) {
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->data = $zones_post;
      $this->saveMessagesInSession();
      return $this->theme->view('backend/offers/weekend', $this->data);
    }
    
    $this->load->model('Trip/Offer_weekend_model');
    $offers = $this->Offer_weekend_model->getOffers(array(
      'select' => 'DISTINCT(`zone`) as zone'
    ));
    $db_zones = array();
    if($offers){
      foreach($offers as $offer){
        $db_zones[] = $offer->zone;
      }
      $deleted_zones = array_diff($db_zones, $zones);
      foreach($deleted_zones as $deleted_zone){
        $zones_data[$deleted_zone] = null;
        $this->Offer_weekend_model->deleteOfferByZone($deleted_zone);
      }
    }
    
    
    foreach($add_items as $add_item){
      $offers = $this->Offer_weekend_model->getOffers(array(
        'type_id' => $add_item['type_id'],
        'type' => $add_item['type'],
      ));
      if($offers){
        continue;
      }
      $add_item['time_created'] = date('Y-m-d H:i:s');
      $this->Offer_weekend_model->addOffer($add_item);
    }
    foreach($delete_items as $delete_item){
      $this->Offer_weekend_model->deleteOfferById($delete_item);
    }
    
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('offers_weekend_settings');
    if($settings){
      foreach($settings as $zone => $zone_data){
        if(!isset($zones_data[$zone])){
          $zones_data[$zone] = null;
        }
      }
    }
    $this->Options_model->set('offers_weekend_settings',$zones_data);
    $this->redirect('backend/offers/weekend', 'Informatiile au fost salvate', 'success');
  }
}