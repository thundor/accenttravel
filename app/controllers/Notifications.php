<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Notifications extends MX_Controller {
  public function cron($type = '') {
    $testing = false;
    $this->load->helper('cron');
    echo '<pre>';
    cron_unlock_file('notification_lock_file_cron');
    cron_lock_file('notification_lock_file_cron', '30 minutes');
    ini_set('max_execution_time', 30 * 60);
    // php index.php backend trip notifications cron
    // $this->load->model('Trip_notifications_model');
    $today_date = new DateTime();
    $this->today_date_formatted = $today_date->format('Y-m-d');
    $this->today_time_formatted = $today_date->format('Y-m-d H:i:s');
    $minimum_time_created = new DateTime('1 day ago');
    $this->minimum_time_created_formatted = $minimum_time_created->format('Y-m-d H:i:s');
    
    // schimbare status in expirat in functie de data expirarii
    $this->db->where_in('status', array(1,2));
    $this->db->where('date_expire < ', $this->today_date_formatted);
    $this->db->set('status', -1);
    $this->db->set('code', 'CONCAT(code,"-",id)', FALSE);
    $this->db->update('trip_notification');
    
    if($type){
      if(method_exists($this,'cron_' . $type)){
        $this->{'cron_' . $type}();
      }
    } else {
      $types = array(
        'package',
        'hotel',
        'flight',
      );
      foreach($types as $type){
        $this->{'cron_' . $type}();
      }
    }
    
    $this->db->select(array(
      'email', 'fullname','phone'
    ));
    $this->db->where('status', 2);
    $this->db->group_by('email');
    $this->db->order_by('email', 'ASC');
    $this->db->order_by('time_created', 'ASC');
    
    $q = $this->db->get('trip_notification');
    $rows = $q->result();
    foreach($rows as $row){
      // Se iau cautarile de lista grupate
      $this->db->select('*');
      $this->db->where('status', 2);
      $this->db->where('email', $row->email);
      $this->db->order_by('email', 'ASC');
      $this->db->order_by('time_created', 'DESC');
      
      $q = $this->db->get('trip_notification');
      $searches = $q->result();
      if(!$testing){
        // marcare ca trimis, inainte de a trimite email-ul (pentru a minimiza lag-ul)
        $this->db->where('status', 2);
        $this->db->where('email', $row->email);
        $this->db->set('status', -2);
        $checking_message = "Email trimis";
        $this->db->set('message', $checking_message);
        $this->db->set('code', 'CONCAT(code,"-",id)', FALSE);
        $this->db->update('trip_notification');
        
        foreach($searches as $search){
          // pentru cele care expira azi, expira recurenta
          if($search->date_expire == $this->today_date_formatted){
            continue;
          }
          $data = array();
          $data['title'] = $search->title;
          $data['fullname'] = $search->fullname;
          $data['email'] = $search->email;
          $data['phone'] = $search->phone;
          $data['type'] = $search->type;
          $data['hotel_id'] = $search->hotel_id;
          $data['package_id'] = $search->package_id;
          $data['flight_itinerary_code'] = $search->flight_itinerary_code;
          $data['amount_hotel'] = $search->amount_hotel;
          $data['amount_package'] = $search->amount_package;
          $data['amount_flight'] = $search->amount_flight;
          $data['amount'] = $search->amount_new;
          $data['amount_new'] = null;
          $data['times_checked'] = null;
          $data['currency'] = $search->currency;
          $data['data_hotel'] = $search->data_hotel;
          $data['data_package'] = $search->data_package;
          $data['data_flight'] = $search->data_flight;
          $data['time_created'] = date('Y-m-d H:i:s');
          $data['date_expire'] = $search->date_expire;
          $data['code'] = $search->code;
          $data['hash_hotel'] = $search->hash_hotel;
          $data['hash_package'] = $search->hash_package;
          $data['hash_flight'] = $search->hash_flight;
          $data['message'] = 'Urmeaza reverificare';
          $data['status'] = 1;
          
          $this->db->insert('trip_notification', $data);
        }
      }
      Modules :: run ('Mailer/trip_notification', array('to'=>$row->email, 'searches'=>$searches));
      
      
    }
    
    cron_unlock_file('notification_lock_file_cron');
    exit;
  }
  protected function cron_hotel() {
    $this->load->model('Trip/Hotels_model');
    if(!session_id()){
      session_start();
    }
    // $this->Trip_model->api->setUseApi(false);
    // Se iau cautarile de lista grupate
    $this->db->select(array(
      'hash_hotel','data_hotel',
      // 'COUNT(*) AS total',
    ));
    $this->db->where_in('type', array('hotel','citybreak'));
    $this->db->where('status', 1);
    $this->db->where('IFNULL(time_last_checked,time_created)<', $this->minimum_time_created_formatted);
    $this->db->group_by('hash_hotel');
    $this->db->order_by('time_last_checked', 'ASC');
    
    $q = $this->db->get('trip_notification');
    $rows = $q->result();

    $time = time();
    foreach($rows as $row){
      // $total = $row->total;
      $search_data = json_decode($row->data_hotel, true);
      
      // Se preiau id-urile hotelurilor
      $this->db->select(array(
        'DISTINCT(hotel_id)',
      ));
      $this->db->where_in('type', array('hotel','citybreak'));
      $this->db->where('status', 1);
      $this->db->where('hash_hotel', $row->hash_hotel);
      $this->db->order_by('time_last_checked', 'ASC');
      
      $q2 = $this->db->get('trip_notification');
      $hotel_ids_result = $q2->result();
      
      // Stabileste mesaj implicit pentru toate
      $this->db->where_in('type', array('hotel','citybreak'));
      $this->db->where('status', 1);
      $this->db->where('hash_hotel', $row->hash_hotel);
      $this->db->set('time_last_checked', $this->today_time_formatted);
      $this->db->set('times_checked', 'IFNULL(times_checked,0)+1', FALSE);
      $checking_message = "In curs de verificare...";
      $this->db->set('message', $checking_message); // atentie, se foloseste ca verificare mai jos
      
      $this->db->update('trip_notification');
      
      
      $container_id = 'notif_hotel_' . $row->hash_hotel . '_' . $time;
      $search_data['container_id'] = $container_id;
      
      $retries = 10;
      $max_retries = $retries;
      $response = null;
      
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $this->Trip_model->get_api()->generateToken();
        $response = $this->Hotels_model->initiateSearch($search_data);
        
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
          $response = null;
          echo 'Hotels not found' . PHP_EOL;
          break;
        }
        break;
      }
      
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $hotel_ids = array();
      foreach($hotel_ids_result as $hotel_id_result){
        $hotel_id = (int)$hotel_id_result->hotel_id;
        $hotel_ids[] = $hotel_id;
      }
      $total_hotel_ids = count($hotel_ids);
      $limit = 1000;
      $total_items = $response->total_items;
      $page = 1;
      $total_pages = ceil($total_items / $limit);
      $total_found_hotel_ids = 0;
      unset($response);
      
      while(($page <= $total_pages) && ($total_found_hotel_ids < $total_hotel_ids)){
        $response = $this->Hotels_model->loadHotels($code, null,$page,null,null,null, $limit);
        if (!$response) {
          echo 'TRIP error: loading hotels result returned no response' . PHP_EOL;
          continue;
        }
        if(empty($response->total_items) || empty($response->_embedded->hotels)){
          echo 'No hotels not found in results' . PHP_EOL;
          continue;
        }
        foreach($response->_embedded->hotels as $hotel){
          $hotel_id = (int)$hotel->Id;
          if(!in_array($hotel_id,$hotel_ids)){
            continue;
          }
          // $found_hotels[$hotel_id] = array($hotel->MinPrice,$hotel->Currency);
          $total_found_hotel_ids++;
        
          // Pentru intrari a caror valuta difera, stabileste mesaj, si pastreaza pentru o noua verificare
          $this->db->where_in('type', array('hotel','citybreak'));
          $this->db->where('status', 1);
          $this->db->where('hotel_id', $hotel_id);
          $this->db->where('currency!=', $hotel->Currency);
          $this->db->where('hash_hotel', $row->hash_hotel);
          $this->db->set('message', "Valuta de comparatie difera de cea din rezultat(" . $hotel->Currency . ").");
          
          $this->db->update('trip_notification');
          
          // Pentru intrari a caror valuta e aceeasi, actualizeaza campurile si pregateste pentru trimitere de email unde este cazul
          $this->db->where_in('type', array('hotel','citybreak'));
          $this->db->where('status', 1);
          $this->db->where('hotel_id', $hotel_id);
          $this->db->where('hash_hotel', $row->hash_hotel);
          $this->db->where('currency', $hotel->Currency);
          
          $this->db->set('amount_hotel', $hotel->MinPrice, FALSE);
          $this->db->set('amount_new', 'IFNULL(amount_package,0) + IFNULL(amount_flight,0) + '  . $hotel->MinPrice, FALSE);
          $this->db->set('status', 'IF(amount*90/100 >= IFNULL(amount_package,0) + IFNULL(amount_flight,0) + '  . $hotel->MinPrice . ',2,status)', FALSE);
          $this->db->set('message', "IF(amount*90/100 >= IFNULL(amount_package,0) + IFNULL(amount_flight,0) + "  . $hotel->MinPrice . "," . $this->db->escape("Urmeaza trimiterea de email") . "," . $this->db->escape("Nu se trimite email") . ")", FALSE);
          
          $this->db->update('trip_notification');
        }
        $page++;
        unset($response);
      }
      
      // Stabileste mesaj implicit pentru toate
      $this->db->where_in('type', array('hotel','citybreak'));
      $this->db->where('status', 1);
      $this->db->where('hash_hotel', $row->hash_hotel);
      $this->db->where('message', $checking_message);
      $this->db->set('message', "Nu au fost gasite rezultate...");
      
      $this->db->update('trip_notification');
    }
    return;
  }
  protected function cron_package() {
    $this->load->model('Trip/Packages_model');
    if(!session_id()){
      session_start();
    }
    // $this->Trip_model->api->setUseApi(false);
    // Se iau cautarile de lista grupate
    $this->db->select(array(
      'hash_package','data_package',
      // 'COUNT(*) AS total',
    ));
    $this->db->where('type', 'package');
    $this->db->where('status', 1);
    $this->db->where('IFNULL(time_last_checked,time_created)<', $this->minimum_time_created_formatted);
    $this->db->group_by('hash_package');
    $this->db->order_by('time_last_checked', 'ASC');
    
    $q = $this->db->get('trip_notification');
    $rows = $q->result();

    $time = time();
    foreach($rows as $row){
      // $total = $row->total;
      $search_data = json_decode($row->data_package, true);
      
      // Se preiau id-urile pachetelor
      $this->db->select(array(
        'DISTINCT(package_id)',
      ));
      $this->db->where('type', 'package');
      $this->db->where('status', 1);
      $this->db->where('hash_package', $row->hash_package);
      $this->db->order_by('time_last_checked', 'ASC');
      
      $q2 = $this->db->get('trip_notification');
      $package_ids_result = $q2->result();
      
      // Stabileste mesaj implicit pentru toate
      $this->db->where('type', 'package');
      $this->db->where('status', 1);
      $this->db->where('hash_package', $row->hash_package);
      $this->db->set('time_last_checked', $this->today_time_formatted);
      $this->db->set('times_checked', 'IFNULL(times_checked,0)+1', FALSE);
      $checking_message = "In curs de verificare...";
      $this->db->set('message', $checking_message); // atentie, se foloseste ca verificare mai jos
      
      $this->db->update('trip_notification');
      
      
      $container_id = 'notif_package_' . $row->hash_package . '_' . $time;
      $search_data['container_id'] = $container_id;
      
      $start_date = new DateTime($search_data['start_date']);
      $end_date = $start_date->modify('+1 years');
      
      $search_data['end_date'] = $end_date->format('Y-m-d');
      $retries = 10;
      $max_retries = $retries;
      $response = null;
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
          sleep(2);
        }
        $max_retries --;
        $response = $this->Packages_model->inspectSearchIndex($index_id);
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
      
      $response = $this->Packages_model->loadPackages($code);
      if (!$response) {
        echo 'TRIP error: search index inspect returned no response' . PHP_EOL;
        continue;
      }
      if(empty($response->total_items)){
        $response = null;
        echo 'Packages not found' . PHP_EOL;
        break;
      }
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $package_ids = array();
      foreach($package_ids_result as $package_id_result){
        $package_id = (int)$package_id_result->package_id;
        $filter = array(
          array(
            'name'=>'Id',
            'type'=>'equal',
            'term'=>$package_id,
          )
        );
        $response = $this->Packages_model->loadPackageResults($code, 1, $filter, 1);
        if (!$response) {
          echo 'TRIP error: loading packages result returned no response' . PHP_EOL;
          continue;
        }
        if(empty($response->total_items) || empty($response->_embedded->packages)){
          echo 'No packages not found in results' . PHP_EOL;
          continue;
        }
        $package = $response->_embedded->packages[0];
        $min_price_for_email = $package->MinPrice * (100 + 10) / 100; // +10%
        
        // Pentru intrari a caror valuta difera, stabileste mesaj, si pastreaza pentru o noua verificare
        $this->db->where('type', 'package');
        $this->db->where('status', 1);
        $this->db->where('package_id', $package_id);
        $this->db->where('currency!=', $package->Currency);
        $this->db->where('hash_package', $row->hash_package);
        $this->db->set('message', "Valuta de comparatie difera de cea din rezultat(" . $package->Currency . ").");
        
        $this->db->update('trip_notification');
        
        // Pentru intrari a caror valuta e aceeasi, actualizeaza campurile si pregateste pentru trimitere de email unde este cazul
        $this->db->where('type', 'package');
        $this->db->where('status', 1);
        $this->db->where('package_id', $package_id);
        $this->db->where('hash_package', $row->hash_package);
        $this->db->where('currency', $package->Currency);
        
        $this->db->set('amount_package', $package->MinPrice, FALSE);
        $this->db->set('amount_new', 'IFNULL(amount_hotel,0) + IFNULL(amount_flight,0) + '  . $package->MinPrice, FALSE);
        $this->db->set('status', 'IF(amount*90/100 >= IFNULL(amount_hotel,0) + IFNULL(amount_flight,0) + '  . $package->MinPrice . ',2,status)', FALSE);
        $this->db->set('message', "IF(amount*90/100 >= IFNULL(amount_hotel,0) + IFNULL(amount_flight,0) + "  . $package->MinPrice . "," . $this->db->escape("Urmeaza trimiterea de email") . "," . $this->db->escape("Nu se trimite email") . ")", FALSE);
        
        $this->db->update('trip_notification');
      }
      
      // Stabileste mesaj implicit pentru toate
      $this->db->where('type', 'package');
      $this->db->where('status', 1);
      $this->db->where('hash_package', $row->hash_package);
      $this->db->where('message', $checking_message);
      $this->db->set('message', "Nu au fost gasite rezultate...");
      
      $this->db->update('trip_notification');
    }
    return;
  }
  protected function cron_flight() {
    $this->load->model('Trip/Flights_model');
    if(!session_id()){
      session_start();
    }
    // $this->Trip_model->api->setUseApi(false);
    // Se iau cautarile de lista grupate
    $this->db->select(array(
      'hash_flight','data_flight',
      // 'COUNT(*) AS total',
    ));
    $this->db->where_in('type', array('flight','citybreak'));
    $this->db->where('status', 1);
    $this->db->where("IF(type='citybreak',time_created,IFNULL(time_last_checked,time_created))<", $this->minimum_time_created_formatted);
    $this->db->group_by('hash_flight');
    $this->db->order_by('time_last_checked', 'ASC');
    
    $q = $this->db->get('trip_notification');
    $rows = $q->result();

    $time = time();
    foreach($rows as $k=>$row){
      // $total = $row->total;
      $search_data = json_decode($row->data_flight, true);
      
      // Se preiau id-urile flighturilor
      $this->db->select(array(
        'DISTINCT(flight_itinerary_code)',
      ));
      $this->db->where_in('type', array('flight'));
      $this->db->where('status', 1);
      $this->db->where('hash_flight', $row->hash_flight);
      $this->db->order_by('time_last_checked', 'ASC');
      
      $q2 = $this->db->get('trip_notification');
      $flight_itinerary_codes_result = $q2->result();
      
      // Stabileste mesaj implicit pentru toate
      $this->db->where_in('type', array('flight','citybreak'));
      $this->db->where('status', 1);
      $this->db->where('hash_flight', $row->hash_flight);
      $this->db->set('time_last_checked', $this->today_time_formatted);
      $this->db->set('times_checked', 'IFNULL(times_checked,0)+1', FALSE);
      $checking_message = "In curs de verificare...";
      $this->db->set('message', $checking_message); // atentie, se foloseste ca verificare mai jos
      
      $this->db->update('trip_notification');
      
      
      $container_id = 'notif_flight_' . $row->hash_flight . '_' . $time;
      $search_data['container_id'] = $container_id;
      
      $retries = 10;
      $max_retries = $retries;
      $response = null;
        // echo '<pre>';
        // print_r($search_data);
        // die;
      while($max_retries){
        if($max_retries < $retries){
          sleep(1);
        }
        $this->Trip_model->get_api()->generateToken();
        $response = $this->Flights_model->initiateSearch($search_data);
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
        $response = $this->Flights_model->inspectSearch($container_id);
        if(!$response){
          echo 'Trip Error: search inspection returned no response.' . PHP_EOL;
          continue;
        }
        if(!is_array($response)){
          $response = null;
          echo 'Trip Error: response is not array.' . PHP_EOL;
          break;
        }
        $search_response = array_pop($response);
        if($search_response->Status == 0){
          $response = null;
          echo 'TRIP error: The search failed.' . PHP_EOL;
          break;
        } elseif($search_response->Status == 1){
          $response = $search_response;
          break;
        } else {
          echo 'Inspecting again...' . PHP_EOL;
        }
      }
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      
      $max_retries = $retries;
      $response = null;
      while($max_retries){
        if($max_retries < $retries){
          sleep(2);
        }
        $max_retries --;
        $response = $this->Flights_model->inspectSearchIndex($index_id);
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
      
      $response = $this->Flights_model->loadFlights($code);
      if(!$response){
        echo 'ABORTING' . PHP_EOL;
        continue;
      }
      $flight_itinerary_codes = array();
      foreach($flight_itinerary_codes_result as $flight_itinerary_code_result){
        $flight_itinerary_code = $flight_itinerary_code_result->flight_itinerary_code;
        $flight_itinerary_codes[] = $flight_itinerary_code;
      }
      $total_flight_itinerary_codes = count($flight_itinerary_codes);
      $total_found_flight_itinerary_codes = 0;
      foreach($response->_embedded->flights as $k=>$flight){
        if(!$k){
          $this->db->where_in('type', 'citybreak');
          $this->db->where('status', 1);
          $this->db->where('hash_flight', $row->hash_flight);
          $this->db->where('currency', $flight->Currency);
          
          $this->db->set('amount_flight', $flight->Price, FALSE);
          $this->db->set('amount_new', 'IFNULL(amount_package,0) + IFNULL(amount_hotel,0) + '  . $flight->Price, FALSE);
          $this->db->set('status', 'IF(amount*90/100 >= IFNULL(amount_package,0) + IFNULL(amount_hotel,0) + '  . $flight->Price . ',2,status)', FALSE);
          $this->db->set('message', "IF(amount*90/100 >= IFNULL(amount_package,0) + IFNULL(amount_hotel,0) + "  . $flight->Price . "," . $this->db->escape("Urmeaza trimiterea de email") . "," . $this->db->escape("Nu se trimite email") . ")", FALSE);
          $this->db->update('trip_notification');
        }
        $itinerary_codes = array();
        $found_hash = false;
        foreach($flight->Routes[0]->Route as $dep_route){
          if(isset($flight->Routes[1])){
            foreach($flight->Routes[1]->Route as $ret_route){
              if(in_array($dep_route->Hash . '-' . $ret_route->Hash,$flight_itinerary_codes)){
                $found_hash = true;
                $itinerary_codes[] = $dep_route->Hash . '-' . $ret_route->Hash;
              }
            }
          } else {
            if(in_array($dep_route->Hash,$flight_itinerary_codes)){
              $found_hash = true;
              $itinerary_codes[] = $dep_route->Hash;
            }
          }
        }
        if(!$itinerary_codes){
          continue;
        }
        $total_found_flight_itinerary_codes++;
      
        // Pentru intrari a caror valuta difera, stabileste mesaj, si pastreaza pentru o noua verificare
        
        $this->db->where_in('type', array('flight','citybreak'));
        $this->db->where('status', 1);
        $this->db->where_in('flight_itinerary_code', $itinerary_codes);
        $this->db->where('currency!=', $flight->Currency);
        $this->db->where('hash_flight', $row->hash_flight);
        $this->db->set('message', "Valuta de comparatie difera de cea din rezultat(" . $flight->Currency . ").");
        
        $this->db->update('trip_notification');
        
        // Pentru intrari a caror valuta e aceeasi, actualizeaza campurile si pregateste pentru trimitere de email unde este cazul
        $this->db->where_in('type', 'flight');
        $this->db->where('status', 1);
        $this->db->where_in('flight_itinerary_code', $itinerary_codes);
        $this->db->where('hash_flight', $row->hash_flight);
        $this->db->where('currency', $flight->Currency);
        
        $this->db->set('amount_flight', $flight->Price, FALSE);
        $this->db->set('amount_new', 'IFNULL(amount_package,0) + IFNULL(amount_hotel,0) + '  . $flight->Price, FALSE);
        $this->db->set('status', 'IF(amount*90/100 >= IFNULL(amount_package,0) + IFNULL(amount_hotel,0) + '  . $flight->Price . ',2,status)', FALSE);
        $this->db->set('message', "IF(amount*90/100 >= IFNULL(amount_package,0) + IFNULL(amount_hotel,0) + "  . $flight->Price . "," . $this->db->escape("Urmeaza trimiterea de email") . "," . $this->db->escape("Nu se trimite email") . ")", FALSE);
        $this->db->update('trip_notification');
        
        if($total_found_flight_itinerary_codes >= $total_flight_itinerary_codes){
          break;
        }
      }
      unset($response);
      
      // Stabileste mesaj implicit pentru toate
      $this->db->where_in('type', array('flight','citybreak'));
      $this->db->where('status', 1);
      $this->db->where('hash_flight', $row->hash_flight);
      $this->db->where('message', $checking_message);
      $this->db->set('message', "Nu au fost gasite rezultate...");
      
      $this->db->update('trip_notification');
    }
    return;
  }
  protected function get_email() {
    $encrypted_email = $this->input->get('v_c');
    if(!isset($encrypted_email) && $this->user->id){
      $email = $this->user->email;
      $this->data['notifications_get_link'] = base_url('notifications/getlist');
      $this->data['notifications_link'] = base_url('notifications');
      $this->data['delete_all_link'] = base_url('notifications/delete_all');
    } else {
      $this->load->library('encryption');
      // $encrypted_email = $this->encryption->encrypt('asdfasdf.asdf.ro');
      // echo urlencode($encrypted_email);
      // echo '<br/>';
      // $get_query = http_build_query(array('v_c' => $encrypted_email));
      // echo '<a href="' . base_url('notifications?' . $get_query) . '">Click</a>';
      // echo base_url('notifications?' . $get_query);
      // echo $encrypted_email;
      // die;
      $hash = $this->input->get('hash');
      $decrypted_email = $this->encryption->decrypt($encrypted_email);
      $sanitized_email = filter_var($decrypted_email, FILTER_SANITIZE_EMAIL);
      
      $email = filter_var($sanitized_email, FILTER_VALIDATE_EMAIL);
      
      if(!$decrypted_email || ($decrypted_email !== $email)){
        $this->redirect('','Cheia de validare este invalida','error');
      }
      $get_query = http_build_query(array('v_c' => $encrypted_email));
      $this->data['notifications_get_link'] = base_url('notifications/getlist?' . $get_query);
      $this->data['notifications_link'] = base_url('notifications?' . $get_query);
      $this->data['delete_all_link'] = base_url('notifications/delete_all?' . $get_query);
    }
    $this->data['email'] = $email;
  }
  public function index() {
    $this->get_email();
    return $this->theme->view('trip/notifications/index', $this->data);
  }
  public function delete_all() {
    $this->get_email();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $this->db->where_in('status', array(1,2));
      $this->db->where('email', $this->data['email']);
      $this->db->set('status', 0);
      $this->db->set('message', "Notificare anulata bulk");
      
      $this->db->update('trip_notification');
      
      $this->redirect($this->data['notifications_link'], 'Notificarile au fost eliminate', 'success');
    }
    return $this->theme->view('trip/notifications/delete_all', $this->data);
  }
  public function delete() {
    $this->get_email();
    $sc = $this->input->get('s_c');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $this->db->where_in('status', array(1,2));
      $this->db->where('code', $sc);
      $this->db->set('status', 0);
      $this->db->set('message', "Notificare anulata");
      
      $this->db->update('trip_notification');
      
      $this->redirect($this->data['notifications_link'], 'Notificarea a fost eliminata', 'success');
    }
    return $this->theme->view('trip/notifications/delete', $this->data);
  }
  public function view() {
    $this->get_email();
    $sc = $this->input->get('s_c');
    
    $this->db->where('status', 1);
    $this->db->where('code', $sc);
    
    $q = $this->db->get('trip_notification', 1, 0);
    
    $row = $q->row();
    
    $active = $row->status > 0;
    
    if($row->type == 'hotel'){
      $data_decoded = json_decode($row->data_hotel, true);
      $this->load->model('Trip/Hotels_model');
      $data_decoded['hotel_id'] = (int)$row->hotel_id;
      $this->Hotels_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/hotel/' . $row->hotel_id));
    } elseif($row->type == 'citybreak'){
      $hotel_data_decoded = json_decode($row->data_hotel, true);
      $flight_data_decoded = json_decode($row->data_flight, true);
      $data_decoded = $hotel_data_decoded + $flight_data_decoded;
      $this->load->model('Trip/Citybreaks_model');
      $data_decoded['hotel_id'] = (int)$row->hotel_id;
      $origin_location_id = isset($data_decoded['origin_location_id']) ? $data_decoded['origin_location_id'] : 0;
      $data_decoded['origin_full_location_name'] = ($origin_location_id > 0 ? $data_decoded['origin_location_name'] . ', ' : '') . $data_decoded['origin_city_name'];
      $destination_location_id = isset($data_decoded['destination_location_id']) ? $data_decoded['destination_location_id'] : 0;
      $data_decoded['destination_full_location_name'] = ($destination_location_id > 0 ? $data_decoded['destination_location_name'] . ', ' : '') . $data_decoded['destination_city_name'];
      $this->Citybreaks_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/citybreak/' . $row->hotel_id));
    } elseif($row->type == 'package'){
      $data_decoded = json_decode($row->data_package, true);
      $this->load->model('Trip/Packages_model');
      $data_decoded['package_id'] = (int)$row->package_id;
      
      $end_date = new DateTime($data_decoded['start_date']);
      $end_date = $end_date->modify('+1 years');
      
      $data_decoded['end_date'] = $end_date->format('Y-m-d');
      
      $this->Packages_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/package/' . $row->package_id));
    } elseif($row->type == 'flight'){
      $data_decoded = json_decode($row->data_flight, true);
      $this->load->model('Trip/Flights_model');
      $this->Flights_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/flights/search'));
    }
    
    return $this->theme->view('trip/notifications/delete', $this->data);
  }
  public function getlist() {
    $encrypted_email = $this->input->get('v_c');
    $this->get_email();
    if (!$this->input->is_ajax_request()) {
      $this->redirect('','Acces invalid','error');
    }
    $filters = array();
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $filters['email'] = $this->data['email'];
    $filters['select'] = array(
      'code', 'title', 'type', 'amount', 'amount_new', 'currency', 'date_expire'
    );
    
    
    $filters['status'] = array(1);
    
    $this->load->model('TripNotification_model');
    $this->data['total_notifications'] = $this->TripNotification_model->getTotalNotifications($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<1 || $limit>100){
      $limit = 20;
    }
    $filters['limit'] = $limit;
    // $ordering = trim('' . $this->input->post('ordering'));
    $ordering = 'id DESC';
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_notifications'] / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    $this->data['max_pages'] = $max_pages;
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    $notifications = array();
    if($this->data['total_notifications']){
      $notifications = $this->TripNotification_model->getNotifications($filters);
      foreach($notifications as $k=>$notification){
        $notification->can_delete = true;
        $get_query = http_build_query(array('v_c' => $encrypted_email, 's_c' => $notification->code));
        $notification->delete_link = base_url('notifications/delete?' . $get_query);
        $notification->view_link = base_url('notifications/view?' . $get_query);
      }
    }
    $this->data['notifications'] = $notifications;
    $this->data['page'] = $current_page;
    $this->output();
  }
}