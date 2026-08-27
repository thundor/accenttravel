<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Popular extends MX_Controller {
  function __construct() {
    parent :: __construct();
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('offers_popular_settings');
    if(!$settings){
      $settings = array();
    }
    
    $this->data = $settings;
    $this->theme->view('backend/offers/popular', $this->data);
  }
  public function cron(){
    $this->load->helper('cron');
    cron_lock_file('flights_popular_lock_file_cron', '30 minutes');

    ini_set('max_execution_time', 30 * 60);
    if(!session_id()){
      session_start();
    }
    echo "Cron popular".PHP_EOL;    
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('offers_popular_settings');
    if(!$settings){
      $settings = array();
    }
    $locations = isset($settings['locations']) && is_array($settings['locations']) ? $settings['locations'] : array();
    if(!$locations){
      echo "No locations. ABORTING.".PHP_EOL;
      exit;
    }
    $locations_departure = isset($locations['departure']) && is_array($locations['departure']) ? $locations['departure'] : array();
    if(!$locations_departure){
      echo "No departure locations. ABORTING.".PHP_EOL;
      exit;
    }
    $locations_arrival = isset($locations['arrival']) && is_array($locations['arrival']) ? $locations['arrival'] : array();
    if(!$locations_arrival){
      echo "No arrival locations. ABORTING.".PHP_EOL;
      exit;
    }
    $airline_company_codes = isset($settings['company_code']) && is_array($settings['company_code']) ? $settings['company_code'] : array();
    if(!$airline_company_codes){
      echo "No company codes. ABORTING.".PHP_EOL;
      exit;
    }
    $interval = isset($settings['interval']) && is_array($settings['interval']) ? $settings['interval'] : array();
    if(!$interval){
      echo "No interval. ABORTING.".PHP_EOL;
      exit;
    }
    $interval_departure = isset($interval['departure']) ? (int)$interval['departure'] : 0;
    $interval_arrival = isset($interval['arrival']) ? $interval['arrival'] : '';
    
    $go_only = $interval_arrival === '';
    $time = time();
    $departure_time = strtotime('+' . $interval_departure . ' days', $time);
    $departure_date = date('Y-m-d', $departure_time);
    $return_date = '';
    if(!$go_only){
      $return_date = date('Y-m-d', strtotime('+' . $interval_arrival . ' days', $departure_time));
    }
    $this->load->model('Trip/Flights_model');
    
    // $this->Trip_model->api->setUseApi(false);
    
    $result_routes = array();
    foreach($airline_company_codes as $airline_company_code){
      $result_routes[$airline_company_code] = array();
    }
    /* 
    $search_data = array(
      // 'origin_city_id' => $departure_city_id,
      // 'origin_location_id' => $departure_location_id,
      // 'go_only' => $go_only,
      'type' => 1,
      'passengers_adult' => 1,
      'cabine_type' => 1,
      // 'direct_only' => true,
      // 'departure_date' => $departure_date,
      // 'return_date' => $return_date,
      'airlines' => $airline_company_codes,
      'container_id' => $container_id,
      'r' => array(),
    ); */
    $this->load->model('Trip/Offer_popular_model');
    // print_r($settings);
    // die;
    $search_data = array(
      // 'origin_city_id' => $departure_city_id,
      // 'origin_location_id' => $departure_location_id,
      // 'go_only' => $go_only,
      'type' => $go_only ? 0 : 1,
      // 'code' => $code,
      'passengers_adult' => 1,
      'cabine_type' => 1,
      // 'direct_only' => true,
      // 'departure_date' => $departure_date,
      // 'return_date' => $return_date,
      // 'airlines' => $airline_company_codes,
      'r' => array(),
    );
    $this->load->model('Trip/Offer_popular_model');
    $this->db->where(array('status'=>'0'));
    $this->Offer_popular_model->deleteOffers();
    foreach($locations_departure as $location_departure_key => $location_departure){
      echo "DEPARTURE-LOCATION: " . $location_departure_key . PHP_EOL;
      list($departure_country_id, $departure_city_id, $departure_location_id, $departure_location_code) = explode('-', $location_departure_key);
      $r_item = array();
      $r_item['date'] = $departure_date;
      $r_item['oCityId'] = $departure_city_id;
      $r_item['oLocId'] = $departure_location_id;
      
      // $found_arival = false;
      
      foreach($locations_arrival as $location_arrival_key => $location_arrival){
        // if($location_arrival_key == '3-577-2-CDG'){
          // $found_arival = true;
        // }
        // if(!$found_arival){
          // continue;
        // }
        echo "ARRIVAL-LOCATION: " . $location_arrival_key . PHP_EOL;
        list($arrival_country_id, $arrival_city_id, $arrival_location_id, $arrival_location_code) = explode('-', $location_arrival_key);
        
        $r_item['dCityId'] = $arrival_city_id;
        $r_item['dLocId'] = $arrival_location_id;
        $search_data['r'] = array();
        $search_data['r'][] = $r_item;
        if(!$go_only){
          $r_item2 = array();
          $r_item2['date'] = $return_date;
          $r_item2['oCityId'] = $arrival_city_id;
          $r_item2['oLocId'] = $arrival_location_id;
          $r_item2['dCityId'] = $departure_city_id;
          $r_item2['dLocId'] = $departure_location_id;
          $search_data['r'][] = $r_item2;
        }
        $container_id = 'cli_popular_' . $time;
        $search_data['container_id'] = $container_id;
        
        $this->Trip_model->get_api()->generateToken();
        $response = $this->Flights_model->initiateSearch($search_data);
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
        
        $search_response = array_pop($response->{$container_id});
        if(!$search_response->Status){
          // print_r($search_data);
          echo 'TRIP error: The search failed.' . PHP_EOL;
          continue;
        }
        $index_id = $search_response->Id;
        echo 'Search index ' . $index_id . PHP_EOL;
        
        $retries = 30;
        $max_retries = $retries;
        
        $response = null;
        while($max_retries){
          if($max_retries < $retries){
            sleep(2);
          }
          $max_retries --;
          // $this->Trip_model->get_api()->generateToken();
          $response = $this->Flights_model->inspectSearch($container_id);
          // print_r($this->Trip_model->api->calls);
          // die;
          if(!$response){
            echo 'Trip Error: search inspection returned no response.' . PHP_EOL;
            continue;
          }
          if(!is_array($response)){
            print_r($response);
            die;
          }
          $search_response = array_pop($response);
          if($search_response->Status == 0){
            $response = null;
            print_r($search_data);
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
            sleep(1);
          }
          $max_retries --;
          $this->Trip_model->get_api()->generateToken();
          $response = $this->Flights_model->inspectSearchIndex($index_id);
          if(!$response){
            $response = null;
            echo 'Trip Error: search index inspection returned no response.' . PHP_EOL;
            break;
          }
          if(empty($response->code)){
            $response = null;
            echo 'Trip Error: the search code is missing.' . PHP_EOL;
            continue;
          }
        }
        if(!$response){
          print_r($this->Trip_model->api->call);
          echo 'ABORTING' . PHP_EOL;
          continue;
        }
        $code = $response->code;
        $response = $this->Flights_model->loadFlights($code);
        
        // file_put_contents(__DIR__ . '/calls.json', json_encode($this->Trip_model->api->calls, JSON_PRETTY_PRINT));
        // file_put_contents(__DIR__ . '/results.json', json_encode($response, JSON_PRETTY_PRINT));
        // die;
        // echo '<pre>';
        // print_R($response->searchData);
        // die;
        // $go_only = $response->searchData->Type == 0;
        // print_R(count($response->_embedded->flights)); die;
        foreach($response->_embedded->flights as $flight){
          foreach($result_routes as $k=> $result_route){
            $result_routes[$k] = array();
          }
          $price = $flight->Price;
          foreach($flight->Combinations as $combination_index => $combination){
            $flight_data = clone $flight;
            $flight_data->Routes = array();
            $flight_data->Combinations = array($combination);
            $flight_data->Routes[0] = array();
            $combination_departure = $combination;
            if(!$go_only){
              $flight_data->Routes[1] = array();
              $combination_arr = explode('|', $combination);
              $combination_departure = $combination[0];
              $combination_return = isset($combination[1]) ? $combination[1] : null;
              $route_arrival_index = (int) substr($combination_return,1);
            }
            $route_departure_index = (int) substr($combination_departure,1);
            $route_departure = null;
            foreach($flight->Routes[0]->Route as $route_departure){
              if($route_departure->Ref == $route_departure_index){
                break;
              }
              $route_departure = null;
            }
            if(!$route_departure){
              // echo 'Departure route not found. SKIPPING.' . PHP_EOL;
              continue;
            }
            $flight_data->Routes[0] = $route_departure;
            if(!$go_only){
              $route_arrival = null;
              foreach($flight->Routes[1]->Route as $route_arrival){
                if($route_arrival->Ref == $route_arrival_index){
                  break;
                }
                $route_arrival = null;
              }
              if(!$route_arrival){
                echo 'Arrival route not found. SKIPPING.' . PHP_EOL;
                continue;
              }
              $flight_data->Routes[1] = $route_arrival;
            }
            $route_airline_code = $route_departure->Segment[0]->Carrier->Marketing->Code;
            if(!in_array($route_airline_code, $airline_company_codes)){
              echo 'Airline is not according filters. SKIPPING.' . PHP_EOL;
              continue;
            }
            $itinerary_code = $flight->ItineraryCode . ':' . $combination_index;
            if(isset($result_routes[$route_airline_code][$itinerary_code])){
              echo 'Airline result is already set.' . PHP_EOL;
              continue;
            }            
            echo 'Adding offer.' . PHP_EOL;
            $result_routes[$route_airline_code][$itinerary_code] = true;
            $add_item = array();
            $add_item['time_created'] = date('Y-m-d H:i:s');
            $add_item['code'] = $route_airline_code;
            $add_item['flight_code'] = $code;
            $add_item['itinerary_code'] = $itinerary_code;
            $add_item['price'] = $price;
            
            $result = new stdClass;
            
            $result->departure = array(
              'country_id' => $departure_country_id,
              'city_id' => $departure_city_id,
              'location_id' => $departure_location_id,
              'location_code' => $departure_location_code,
              'key' => $location_departure_key,
              'data' => $location_departure,
            );
            $result->arrival = array(
              'country_id' => $arrival_country_id,
              'city_id' => $arrival_city_id,
              'location_id' => $arrival_location_id,
              'location_code' => $arrival_location_code,
              'key' => $location_arrival_key,
              'data' => $location_arrival,
            );
            $result->flight = $flight_data;
            // echo '<pre>';
            // print_R($result);
            // die;
            $add_item['data'] = serialize($result);
            $this->Offer_popular_model->addOffer($add_item);
          }
        }
        // break;
      }
      // break;
    }
    
    
    
    $this->db->where('`status` = 1');
    $this->Offer_popular_model->deleteOffers();
    
    $this->db->where('`status` = 0');
    $this->db->update('trip_offer_popular', array('status' => 1));
    
    echo 'DONE Popular Flights' . PHP_EOL;
    // $this->Trip_model->get_api()->generateToken();
    // print_r($settings);
    // die;
    cron_unlock_file('flights_popular_lock_file_cron');
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    
    $this->load->library('form_validation');
    
    $post = $this->input->post('data');
    $images = isset($_FILES['image']) ? $_FILES['image'] : array();
    $data = array(
      'status' => array(),
      'title' => array(),
      'company_code' => array(),
      // 'departure' => array(),
      // 'return' => array(),
      'image' => array(),
      'locations' => array(
        'departure' =>array(),
        'arrival' =>array(),
      ),
      'interval' => array(
        'departure' => 0,
        'arrival' => 0,
      ),
    );
    
    $locations = $this->input->post('locations');
    if($locations && is_array($locations)){
      if(isset($locations['departure']) && $locations['departure'] && is_array($locations['departure'])){
        $data['locations']['departure'] = $locations['departure'];
      }
      if(isset($locations['arrival']) && $locations['arrival'] && is_array($locations['arrival'])){
        $data['locations']['arrival'] = $locations['arrival'];
      }
    }
    $interval = $this->input->post('interval');
    if(!$interval || !is_array($interval)){
      $interval = array();
    }
    $_POST['interval_departure'] = isset($interval['departure']) ? $interval['departure'] : null;
    $_POST['interval_arrival'] = isset($interval['arrival']) ? $interval['arrival'] : null;
    
    $data['interval']['departure'] = trim($_POST['interval_departure']);
    $data['interval']['arrival'] = trim($_POST['interval_arrival']);
    
    $this->form_validation->set_rules('interval_departure', '+Zile Plecare', 'trim|greater_than_equal_to[0]');
    $this->form_validation->set_rules('interval_arrival', '+Zile Intoarcere', 'trim|greater_than_equal_to[0]');
    
    $should_validate = false;
    $statuses = isset($post['status']) ? $post['status'] : array();
    $k = -1;
    foreach($statuses as $i => $status){
      $should_validate = true;
      $k++;
      $data['status'][$k] = $status = isset($post['status'][$i]) ? $post['status'][$i] : null;
      $data['title'][$k] = $title = isset($post['title'][$i]) ? $post['title'][$i] : null;
      // $data['departure'][$k] = $departure = isset($post['departure'][$i]) ? $post['departure'][$i] : null;
      // $data['return'][$k] = $return = isset($post['return'][$i]) ? $post['return'][$i] : null;
      $data['company_code'][$k] = $airline_company_codes = isset($post['company_code'][$i]) ? $post['company_code'][$i] : null;
      $data['image'][$k] = $image = isset($post['image'][$i]) ? $post['image'][$i] : null;
      
      $fake_post_prefix = 'zone_' . $i . '_';
      $_POST[$fake_post_prefix . 'status'] = $status;
      $_POST[$fake_post_prefix . 'title'] = $title;
      // $_POST[$fake_post_prefix . 'departure'] = $departure;
      // $_POST[$fake_post_prefix . 'return'] = $return;
      $_POST[$fake_post_prefix . 'company_code'] = $airline_company_codes;
      $_POST[$fake_post_prefix . 'image'] = $image;
      $this->form_validation->set_rules($fake_post_prefix . 'status', 'Zona ' . ($k + 1) . ' Status', 'in_list[0,1]');
      $this->form_validation->set_rules($fake_post_prefix . 'title', 'Zona ' . ($k + 1) . ' Titlu', 'trim' . ($status ? '|required' : '') . '|max_length[255]');
      $this->form_validation->set_rules($fake_post_prefix . 'company_code', 'Zona ' . ($k + 1) . ' Companie', 'trim' . ($status ? '|required' : '') . '');
      // $this->form_validation->set_rules($fake_post_prefix . 'departure', 'Zona ' . ($k + 1) . ' Plecare', 'trim' . ($status ? '|required' : '') . '|greater_than_equal_to[0]');
      // $this->form_validation->set_rules($fake_post_prefix . 'return', 'Zona ' . ($k + 1) . ' Intoarcere', 'trim' . ($status ? '|required' : '') . '|greater_than_equal_to[0]');
      $this->form_validation->set_rules($fake_post_prefix . 'image', 'Zona ' . ($k + 1) . ' Imagine', 'trim' . ($status ? '|required' : '') . '');
      
      $uploaded_image = isset($images['tmp_name'][$i]) ? $images['tmp_name'][$i] : false;
      if($uploaded_image){
        $image_name = isset($images['name'][$i]) ? $images['name'][$i] : '';
        $image_ext = strrchr($image_name, ".");
        $image_extension = substr($image_ext, 1);
        $image = false;
        if($image_extension === $image_name || '.' . $image_extension === $image_name){
          $image_extension = false;
        }
        if($image_extension){
          $safe_image_name = trim(preg_replace("/[^a-zA-Z0-9\.\-\_]/", '', $image_name),'. ');
          $image_basename = basename($safe_image_name,$image_ext);
          if(strlen($image_basename) && $image_extension && in_array(strtolower($image_extension), array('jpg','png', 'gif'))){
            $image = getimagesize($uploaded_image);
          }
        }
        $_POST[$fake_post_prefix . 'image_upload'] = null;
        if(!$image){
          $this->form_validation->set_rules($fake_post_prefix . 'image_upload', 'Zona ' . ($k + 1) . ' Imagine incarcata', 'required', array(
            'required' => 'Zona ' . ($k + 1) . ' Imaginea incarcata este invalida'
          ));
        } else {
          $image_size = isset($images['size'][$i]) ? (int)$images['size'][$i] : 0;
          $image_size_kb = $image_size / 1024;
          
          if($image_size_kb > 10 * 1024){
            $this->form_validation->set_rules($fake_post_prefix . 'image_upload', 'Zona ' . ($k + 1) . ' Imagine incarcata', 'required', array(
              'required' => 'Zona ' . ($k + 1) . ' Imaginea incarcata depaseste 10 MB'
            ));
          } else {
            $file_deposit_path = $this->theme->theme_path . 'assets/images/' . $safe_image_name;
            $data['image'][$k] = $safe_image_name;
            if(file_exists($file_deposit_path)){
              // $this->form_validation->set_rules($fake_post_prefix . 'image_upload', 'Zona ' . ($k + 1) . ' Imagine incarcata', 'required', array(
                // 'required' => 'Zona ' . ($k + 1) . ' Imaginea incarcata deja exista pe server'
              // ));
            } else {
              move_uploaded_file($uploaded_image, $file_deposit_path);
              $data['image'][$k] = $safe_image_name;
              $_POST[$fake_post_prefix . 'image'] = $safe_image_name;
            }
          }
        }
      }
    }
    
    if ($should_validate && $this->form_validation->run() == FALSE) {
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->data = $data;
      $this->saveMessagesInSession();
      return $this->theme->view('backend/offers/popular', $this->data);
    }
    
    $this->load->model('Options_model');
    
    $this->Options_model->set('offers_popular_settings',$data);
    $this->redirect('backend/offers/popular', 'Informatiile au fost salvate', 'success');
  }
}