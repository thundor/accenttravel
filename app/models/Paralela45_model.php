<?php

class Paralela45_model extends CI_Model {

  public $api;
  
  function __construct() {
    parent::__construct();
    $this->load->helper("xmlarr");
    $this->load->helper("paralela45");
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
  }
  
  public function getApi(){
    if($this->api){
      return $this->api;
    }
    $settings = array(
      'endpoint' => 'http://rezervari.paralela45.ro/server_xml/server.php',
      'username' => 'XML_ACCENTEVENTS',
//      'password' => 'attvsww13268',
      'password' => 'accent123',
    );
    $this->api = new Paralela45_API($settings['endpoint'],$settings['username'],$settings['password']);
    return $this->api;
  }
  
  public function request($request, $request_type, $request_lang = null, $cache_time = false, $retrieve_from_cache = true){
    $api = $this->getApi();
    $cache_storage_path = 'paralela45/' . $request_type . '/';
    $cache_hash = $cache_storage_path . crc32($request);
    if($cache_time !== false && $retrieve_from_cache !== false){
      if($response = $this->cache->get($cache_hash, $cache_time)){
        $simple_xml_element = $this->interpretXmlResponse($response);
        if($simple_xml_element){
          return $simple_xml_element;
        } else {
          echo 'Invalid xml element';
        }
      }
    }
    
    $request_data = array();
    $request_data['request_type'] = $request_type;
    $request_data['user_id'] = $this->user->id;
    $request_data['client_ip'] = $this->input->ip_address();
    $request_data['request_time'] = date("Y-m-d\TH:i:s");
    if(true == $request_lang){
      $request_lang = $api->lang;
    }
    $request_data['request_lang'] = $request_lang ? $request_lang : null;
    $request_data['request'] = $request;
    $this->db->insert('ac_paralela45', $request_data);
    $request_id = $this->db->insert_id();
    $request_data['id'] = $request_id;
    
    $api->test = true;
    $response = $api->request((object)$request_data);
    $response_dir_path = APPPATH.'logs/paralela45/' . $request_type . '/';
    if(!is_dir($response_dir_path)){
      mkdir($response_dir_path,0777,true);
    }
    file_put_contents($response_dir_path . $request_id . '_response.txt',$response);
    
    $simple_xml_element = $this->interpretXmlResponse($response);
    if(!$simple_xml_element){
      return false;
    }
    $response_data = array();
    $response_data['response_type'] = (string)$simple_xml_element[0]['ResponseType'];
    $response_data['response_id'] = (string)$simple_xml_element->AuditInfo->ResponseId;
    $response_data['response_time'] = (string)$simple_xml_element->AuditInfo->ResponseTime;
    $this->db->where('id', $request_id);
    $this->db->update('ac_paralela45', $response_data);
    if($cache_time !== false){
      if (!$cache_check = $this->cache->get($cache_storage_path . 'cache_check')){
        clearExpiredCache($cache_storage_path, $this->cache);
        setCacheStorage($cache_storage_path);
        $this->cache->save($cache_storage_path . 'cache_check', 1, $cache_time);
      }
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_hash, $response, $cache_time);
    }
    return $simple_xml_element;
  }
  public function interpretXmlResponse($string){
    if(!$string){
      return;
    }
    $simple_xml_element = simplexml_load_string($string);
    return $simple_xml_element;
  }
  public function getRequest($request_id){
    $this->db->select('client_ip');
    $this->db->select('request_type');
    $this->db->select('response_type');
    $this->db->select('response');
    $this->db->where('id', $request_id);
    $q = $this->db->get('ac_paralela45');
    if($q->num_rows()){
      $request = $q->row();
      if($request->response_type && strpos($request->response_type,'Response') && method_exists($this, $request->response_type)){
        $response_dir_path = APPPATH.'logs/paralela45/' . $request->request_type . '/';
        if(!is_file($response_dir_path . $request_id . '.txt')){
          return false;
        }
        $response = file_get_contents($response_dir_path . $request_id . '.txt');
        $simple_xml_element = $this->interpretXmlResponse($response);
        if(!$simple_xml_element){
          return false;
        }
        return $this->{$request->response_type}($simple_xml_element);
      }
    }
    return false;
  }
  public function getCountryRequest(){
    $data = array(
      __FUNCTION__ => array(),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getCountryResponse($response);
  }
  public function getCountryResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Country' => true,
      ),
    ));
  }
  public function getCityRequest($country_code){
    $data = array(
      __FUNCTION__ => array(
        '@' => array(
          'CountryCode' => $country_code,
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getCityResponse($response);
  }
  public function getCityResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'City' => true,
      ),
    ));
  }
  public function getOwnCityRequest(){
    $data = array(
      __FUNCTION__ => array(),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getOwnCityResponse($response);
  }
  public function getOwnCityResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'City' => true,
      ),
    ));
  }
  public function getOwnHotelsRequest($city){
    $data = array(
      __FUNCTION__ => array(
        'CityCode' => $city,
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getOwnHotelsResponse($response);
  }
  public function getOwnHotelsResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Hotel' => array(
          0 => true,
          'Rooms' => array(
            'Room' => true,
          ),
        ),
      ),
    ));
  }
  public function getRoomTypes($cache_time = 86400){
    $response = $this->getRoomRequest($cache_time);
    $room_types = array();
    if($response){
      if(isset($response->getRoomResponse->Room)){
        foreach($response->getRoomResponse->Room as $room_type){
          $room_types[$room_type->Code] = $room_type->_;
        }
      }
    }
    return $room_types;
  }
  public function getRoomRequest($cache_time = 86400){
    $data = array(
      __FUNCTION__ => array(),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->getRoomResponse($response);
  }
  public function getRoomResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Room' => true,
      ),
    ));
  }
  public function getTagOffersRequest(){
    $data = array(
      __FUNCTION__ => array(),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getTagOffersResponse($response);
  }
  public function getTagOffersResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Tag' => true,
      ),
    ));
  }
  public function availabilitiesRequest($simple = false){
    $availabilities = array(
      'OR' => 'la cerere',
      'IM' => 'disponibil',
      'ST' => 'expirat',
    );
    if($simple){
      return $availabilities;
    }
    $data = array(
      'Request' => array(
        'ResponseDetails' => array(
          'availabilitiesResponse' => array(
            'availabilities' => array(
              'availability' => array_map(
                function ($k, $v) {
                  return array(
                    'Code' => $k,
                    '_' => $v,
                  ); 
                },
                array_keys($availabilities), $availabilities
              ),
            ),
          ),
        ),
      ),
    );
    $response = arr2xml($data);
    $simple_xml_element = $this->interpretXmlResponse($response);
    return $this->availabilitiesResponse($simple_xml_element);
  }
  public function availabilitiesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'availabilities' => array(
          'availability' => true,
        ),
      ),
    ));
  }
  public function mealTypesRequest($simple = false){
    $meal_types = array(
      0 => 'fara masa',
      1 => 'all inclusive',
      2 => 'mic dejun',
      3 => 'demipensiune',
      4 => 'pensiune completa',
      5 => 'bonuri valorice',
      6 => 'selfcatering',
    );
    if($simple){
      return $meal_types;
    }
    $data = array(
      'Request' => array(
        'ResponseDetails' => array(
          'mealTypesResponse' => array(
            'mealTypes' => array(
              'mealType' => array_map(
                function ($k, $v) {
                  return array(
                    'Code' => $k,
                    '_' => $v,
                  ); 
                },
                array_keys($meal_types), $meal_types
              ),
            ),
          ),
        ),
      ),
    );
    $response = arr2xml($data);
    $simple_xml_element = $this->interpretXmlResponse($response);
    return $this->mealTypesResponse($simple_xml_element);
  }
  public function mealTypesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'mealTypes' => array(
          'mealType' => true,
        ),
      ),
    ));
  }
  public function serviceTypesRequest($simple = false){
    $service_types = array(
      '' => 'fara servicii',
      5 => '5',
      6 => '6',
      7 => 'Transport',
      '7s' => 'Taxa',
      8 => 'Comision',
      26 => '26',
      27 => '27',
    );
    if($simple){
      return $service_types;
    }
    $data = array(
      'Request' => array(
        'ResponseDetails' => array(
          'serviceTypesResponse' => array(
            'serviceTypes' => array(
              'serviceType' => array_map(
                function ($k, $v) {
                  return array(
                    'Code' => $k,
                    '_' => $v,
                  ); 
                },
                array_keys($service_types), $service_types
              ),
            ),
          ),
        ),
      ),
    );
    $response = arr2xml($data);
    $simple_xml_element = $this->interpretXmlResponse($response);
    return $this->serviceTypesResponse($simple_xml_element);
  }
  public function serviceTypesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'serviceTypes' => array(
          'serviceType' => true,
        ),
      ),
    ));
  }
  public function FacilitiesRequest($simple = false){
    $facilities = array(
      1 => 'Restaurant',
      2 => 'Bucatarie la dispozitie',
      3 => 'Sala de conferinte',
      4 => 'Centru tratament balneo',
      5 => 'Internet',
      6 => 'Aer conditionat',
      7 => 'Parcare',
      8 => 'Spa-welness',
      9 => 'Piscina interioara',
      10 => 'Piscina exterioara',
      11 => 'Piscina cu apa termala',
      12 => 'Fitness',
      13 => 'Teren de sport',
      14 => 'Sauna',
      15 => 'Jacuzzi',
      16 => 'Internet in camera',
      17 => 'Gratar in curte',
      18 => 'Hotel pentru cupluri (+18)',
      19 => 'Hotel pentru familii cu copii',
    );
    if($simple){
      return $facilities;
    }
    $data = array(
      'Request' => array(
        'ResponseDetails' => array(
          'FacilitiesResponse' => array(
            'Facilities' => array(
              'Facility' => array_map(
                function ($k, $v) {
                  return array(
                    'Code' => $k,
                    '_' => $v,
                  ); 
                },
                array_keys($facilities), $facilities
              ),
            ),
          ),
        ),
      ),
    );
    $response = arr2xml($data);
    $simple_xml_element = $this->interpretXmlResponse($response);
    return $this->FacilitiesResponse($simple_xml_element);
  }
  public function FacilitiesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Facilities' => array(
          'Facility' => true,
        ),
      ),
    ));
  }
  public function getHotelPriceRequest(){
    $data = array(
      __FUNCTION__ => array(
        'CountryCode' => 'RO',
        'CityCode' => 'ROMM',
        'CurrencyCode' => 'EUR',
        'PeriodOfStay' => array(
          'CheckIn' => '2018-08-23',
          'CheckOut' => '2018-08-30',
        ),
        'Rooms' => array(
          'Room' => array(
            array(
              '@' => array(
                'Code' => 'DB',
                'NoAdults' => 2,
              ),
            ),
            array(
              '@' => array(
                'Code' => 'DB',
                'NoAdults' => 1,
                'NoChildren' => 1,
              ),
              'Children' => array(
                'Age' => array(
                  3
                ),
              ),
            ),
          ),
        ),
        // 'TourOpCode' => 'EU',
        // 'ProductName' => 'condor',
        // 'ProductCategory' => '2',
        // 'MealTypes' => array(
          // 'MealType' => array(
            // 1,
          // ),
        // ),
        // 'Facilities' => array(
          // 'Facility' => array(
            // 6,
            // 10,
          // ),
        // ),
        'Language' => 'RO',
        'OfferType' => 'TOATE',
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getHotelPriceResponse($response);
  }
  public function getHotelPriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Hotel' => array(
          0 => true,
          'Offers' => array(
            'Offer' => array(
              0 => true,
              'BookingRoomTypes' => array(
                'Room' => true,
              ),
              'Meals' => array(
                'Meal' => true,
              ),
              'PriceDetails' => array(
                'Services' => array(
                  'Service' => true,
                ),
              ),
            ),
          ),
        ),
      ),
    ));
  }
  public function getProductInfoUpdateRequest(){
    $data = array(
      __FUNCTION__ => array(
        'ProductList' => array(
          'Product' => array(
            array(
              'ProductType' => 'hotel',
              'CountryCode' => 'IT',
              'CityCode' => 'ITSRR1',
              'TourOpCode' => 'P45',
              'ProductCode' => 'IT0285',
              'LastUpdateDate' => '2012-09-05',
              'LastUpdateTime' => '16:10:56',
            ),
          ),
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getProductInfoUpdateResponse($response);
  }
  public function getProductInfoUpdateResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(),
    ));
  }
  public function getProductInfoRequest($search_data = array(), $cache_time = 86400){
    if(!$search_data){
      $search_data = array(
        'ProductType' => 'hotel',
        'CountryCode' => 'IT',
        'CityCode' => 'ITSRR1',
        'TourOpCode' => 'P45',
        'ProductCode' => 'IT0285',
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->getProductInfoResponse($response);
  }
  public function getProductInfoResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Product' => array(
          'Pictures' => array(
            'Picture' => true,
          ),
          'Facilities' => array(
            'Facility' => true,
          ),
          'RoomFacilities' => array(
            'RoomFacility' => true,
          ),
          'DayDescriptions' => array(
            'DayDescription' => true,
          ),
          'Destinations' => array(
            'Destination' => true,
          ),
        ),
      ),
    ));
  }
  public function getHotelServiceTypesRequest($search_data = array(), $cache_time = 3600){
    if(!$search_data){
      $search_data = array(
        'CountryCode' => 'RO',
        'CityCode' => 'ROMM',
        'TourOpCode' => 'P45',
        'ProductCode' => 'RO0518',
        'VariantId' => '0|91579589_7985_1',
        'Language' => 'RO',
        'PeriodOfStay' => array(
          'CheckIn' => '2018-08-23',
          'CheckOut' => '2018-08-30',
        ),
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->getHotelServiceTypesResponse($response);
  }
  public function getHotelServiceTypesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Services' => array(
          'Service' => true,
        ),
      ),
    ));
  }
  public function getHotelServicePriceRequest($search_data = array(), $cache_time = 3600){
    if(!$search_data){
      $search_data = array(
        'CountryCode' => 'RO',
        'CityCode' => 'ROMM',
        'TourOpCode' => 'P45',
        'ProductCode' => 'RO0518',
        'CurrencyCode' => 'EUR',
        'VariantId' => '0|91579589_7985_1',
        'Language' => 'RO',
        'Services' => array(
          'Service' => array(
            array(
              'ServiceType' => '2',
              'ServiceCode' => '18',
              'PeriodOfStay' => array(
                'CheckIn' => '2018-08-23',
                'CheckOut' => '2018-08-30',
              ),
              'PaxNames' => array(
                'PaxName' => array(
                  array(
                    'PaxType' => 'adult',
                    '_' => 'TEST/TEST',
                  ),
                  array(
                    'PaxType' => 'child',
                    'ChildAge' => 3,
                    '_' => 'TEST/TEST',
                  ),
                ),
              ),
            ),
          ),
        ),
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->getHotelServicePriceResponse($response);
  }
  public function getHotelServicePriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Services' => array(
          'Service' => true,
        ),
      ),
    ));
  }
  
  public function getItemFeesRequest($search_data = array(), $cache_time = 3600){
    if(!$search_data){
      $search_data = array(
        '@' => array(
          'CurrencyCode' => 'EUR',
        ),
        'BookingItems' => array(
          'BookingItem' => array(
            array(
              '@' => array(
                'ProductType' => 'hotel',
              ),
              'TourOpCode' => 'P45',
              'HotelItem' => array(
                'CountryCode' => 'RO',
                'CityCode' => 'ROMM',
                'ProductCode' => 'RO0518',
                'PeriodOfStay' => array(
                  'CheckIn' => '2018-08-23',
                  'CheckOut' => '2018-08-30',
                ),
                'VariantId' => '0|91579589_7985_1',
                'Rooms' => array(
                  'Room' => array(
                    array(
                      '@' => array(
                        'Code' => 'DB',
                        'NoAdults' => 1,
                        'NoChildren' => 1,
                      ),
                      'PaxNames' => array(
                        'PaxName' => array(
                          array(
                            'PaxType' => 'adult',
                            '_' => 'TEST/TEST',
                          ),
                          array(
                            'PaxType' => 'child',
                            'ChildAge' => 3,
                            '_' => 'TEST/TEST',
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__, true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->getItemFeesResponse($response);
  }
  public function getItemFeesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'ItemFees' => array(
          'ItemFee' => array(
            0 => true,
            'Fees' => array(
              'Fee' => true,
            ),
          ),
        ),
      ),
    ));
  }
  
  public function AddBookingRequest($search_data = array()){
    if(!$search_data){
      $search_data = array(
        '@' => array(
          'CurrencyCode' => 'EUR',
        ),
        'BookingName' => 'int1234',
        'BookingClientId' => 'int1234',
        
        'BookingItems' => array(
          'BookingItem' => array(
            array(
              '@' => array(
                'ProductType' => 'hotel',
              ),
              'ItemClientId' => 1,
              'TourOpCode' => 'P45',
              'HotelItem' => array(
                'BookingAgent' => 'MARIAN GRAMA,Agency Tour SRL,Agency Tour SRL,marian.grama@touringit.ro',
                'BookingClient' => 'TEST TEST',
                'CountryCode' => 'RO',
                'CityCode' => 'ROMM',
                'ProductCode' => 'RO0518',
                'Language' => 'RO',
                'PeriodOfStay' => array(
                  'CheckIn' => '2018-08-23',
                  'CheckOut' => '2018-08-30',
                ),
                'VariantId' => '0|91579589_7985_1',
                'SuppServices' => array(
                  'Service' => array(
                    array(
                      'Code' => '1113',
                      'Type' => 2,
                      'PeriodOfStay' => array(
                        'CheckIn' => '2018-08-23',
                        'CheckOut' => '2018-08-30',
                      ),
                      'PaxIds' => array(
                        'PaxId' => array(
                          1,
                          2,
                        ),
                      ),
                    ),
                  ),
                ),
                'Rooms' => array(
                  'Room' => array(
                    array(
                      '@' => array(
                        'Code' => '817',
                        'NoAdults' => 1,
                        'NoChildren' => 1,
                      ),
                      'PaxNames' => array(
                        'PaxName' => array(
                          array(
                            'PaxType' => 'adult',
                            'TGender' => 'B',
                            'DOB' => '1980-08-24',
                            'CIT' => 'RO',
                            'NATIONALITATE' => 'RO',
                            'PASS' => '--',
                            'TARA_EMITERE' => 'RO',
                            '_' => 'TEST/TEST',
                          ),
                          array(
                            'PaxType' => 'child',
                            'ChildAge' => 3,
                            'TGender' => 'C',
                            'DOB' => '2012-08-24',
                            '_' => 'TEST/TEST',
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      );
    }
    $data = array(
      __FUNCTION__ => $search_data
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->AddBookingResponse($response);
  }
  public function AddBookingResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'BookingReferences' => array(
          'BookingReference' => true,
        ),
        'BookingItems' => array(
          'BookingItem' => true,
        ),
      ),
    ));
  }
  public function getBookingRequest($search_data){
    if(!$search_data){
      $search_data = array(
        'BookingReference' => array(
          '_' => 'int1234',
          'Source' => 'client',
        ),
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getBookingResponse($response);
  }
  public function getBookingResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'BookingReferences' => array(
          'BookingReference' => true,
        ),
        'BookingItems' => array(
          'BookingItem' => array(
            0 => true,
            'Remarks' => array(
              'Remark' => true,
            ),
            'HotelItem' => array(
              'Rooms' => array(
                'Room' => array(
                  0 => true,
                  'PaxNames' => array(
                    'PaxName' => true,
                  ),
                ),
              ),
              'Services' => array(
                'Service' => array(
                  0 => true,
                  'PaxIds' => array(
                    'PaxId' => true,
                  ),
                ),
              ),
              'Meals' => array(
                'Meal' => array(
                  0 => true,
                  'PaxIds' => array(
                    'PaxId' => true,
                  ),
                ),
              ),
            ),
            'CircuitItem' => array(
              'Rooms' => array(
                'Room' => array(
                  0 => true,
                  'PaxNames' => array(
                    'PaxName' => true,
                  ),
                ),
              ),
              'Services' => array(
                'Service' => array(
                  0 => true,
                  'PaxIds' => array(
                    'PaxId' => true,
                  ),
                ),
              ),
            ),
            'CharterItem' => array(
              'Services' => array(
                'Service' => array(
                  0 => true,
                  'PaxIds' => array(
                    'PaxId' => true,
                  ),
                ),
              ),
              'PaxNames' => array(
                'PaxName' => array(
                  0 => true,
                ),
              ),
            ),
            'TicketItem' => array(
              'PaxNames' => array(
                'PaxName' => array(
                  0 => true,
                ),
              ),
            ),
          ),
        ),
      ),
    ));
  }
  public function getBookingFeesRequest(){
    $data = array(
      __FUNCTION__ => array(
        'BookingReference' => array(
          '_' => 'int1234',
          'Source' => 'client',
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getBookingFeesResponse($response);
  }
  public function getBookingFeesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'BookingReferences' => array(
          'BookingReference' => true,
        ),
        'BookingFees' => array(
          'BookingItemFee' => array(
            0 => true,
            'Fees' => array(
              'Fee' => true,
            ),
          ),
        ),
      ),
    ));
  }
  public function CancelBookingRequest(){
    $data = array(
      __FUNCTION__ => array(
        'BookingReference' => array(
          '_' => 'int1234',
          'Source' => 'client',
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->CancelBookingResponse($response);
  }
  public function CancelBookingResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'BookingReferences' => array(
          'BookingReference' => true,
        ),
        'BookingItems' => array(
          'BookingItem' => true,
        ),
      ),
    ));
  }
  public function ModBookingItemPaxRequest(){
    $data = array(
      __FUNCTION__ => array(
        'BookingReference' => array(
          '_' => 'int1234',
          'Source' => 'client',
        ),
        'BookingItems' => array(
          'BookingItem' => array(
            array(
              'ItemNr' => '',
              'CharterItem' => array(
                'PaxNames' => array(
                  'PaxName' => array(
                    array(
                      'PaxType' => 'adult',
                      'TGender' => 'B',
                      'DOB' => '1980-08-24',
                      'CIT' => 'RO',
                      'NATIONALITATE' => 'RO',
                      'PASS' => '--',
                      'TARA_EMITERE' => 'RO',
                      '_' => 'TEST/TEST',
                    ),
                    array(
                      'PaxType' => 'child',
                      'ChildAge' => 3,
                      'TGender' => 'C',
                      'DOB' => '2012-08-24',
                      '_' => 'TEST/TEST',
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->ModBookingItemPaxResponse($response);
  }
  public function ModBookingItemPaxResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'BookingReferences' => array(
          'BookingReference' => true,
        ),
        'BookingItems' => array(
          'BookingItem' => true,
        ),
      ),
    ));
  }
  
  public function getCircuitSearchCity($cache_time = 86400, $retrieve_from_cache = true){
    setCacheStorage('paralela45');
    $cache_hash = 'paralela45/' . __FUNCTION__;
    // $cache_time = false;
    $version = '1.0.2';
    if($retrieve_from_cache !== false){
      if($response = $this->cache->get($cache_hash, $cache_time)){
        $response = json_decode($response, true);
        if($response){
          $response_version = isset($response['Version']) ? $response['Version'] : null;
          if($response_version === $version){
            return $response;
          }
        }
      }
    }
    $paralela45_data = array(
      'Countries' => array(),
      'Cities' => array(),
      'CityLinks' => array(
        'Departure' => array(),
        'Destination' => array(),
      ),
      'Aliases' => array(),
      'Dates' => array(),
      'Months' => array(),
      'Version' => $version,
    );
    // $response = $this->CircuitSearchCityRequest($cache_time, false);
    $response = $this->CircuitSearchCityRequest($cache_time);
    if(empty($response) || empty($response->CircuitSearchCityResponse) || empty($response->CircuitSearchCityResponse->Country)){
      return $paralela45_data;
    }
    if(!isset($paralela45_data['Countries']['RO'])){
      $paralela45_data['Countries']['RO'] = array(
        'CountryName' => 'Romania',
        'CityCodes' => array(),
      );
    }
    foreach($response->CircuitSearchCityResponse->Country as $response_country){
      if(empty($response_country)){
        continue;
      }
      if(!isset($paralela45_data['Countries'][$response_country->CountryCode])){
        $paralela45_data['Countries'][$response_country->CountryCode] = array(
          'CountryName' => $response_country->CountryName,
          'CityCodes' => array(),
        );
      }
      if(empty($response_country->Cities) || empty($response_country->Cities->City)){
        continue;
      }
      foreach($response_country->Cities->City as $response_destination){
        if(empty($response_destination)){
          continue;
        }
        $paralela45_data['Cities'][$response_destination->CityCode] = array(
          'CityName' => $response_destination->CityName,
          'CountryCode' => $response_country->CountryCode,
        );
        $paralela45_data['Countries'][$response_country->CountryCode]['CityCodes'][] = $response_destination->CityCode;
        $destination_city_alias = trim(preg_replace('/\W+/', '_', strtolower($response_destination->CityName)), ' _');
        $paralela45_data['Aliases'][$destination_city_alias][] = $response_destination->CityCode;
      }
    }
    foreach($response->CircuitSearchCityResponse->Country as $response_country){
      if(empty($response_country)){
        continue;
      }
      if(empty($response_country->Cities) || empty($response_country->Cities->City)){
        continue;
      }
      $search_data = array(
        'CircuitSearchRequest' => array(
          'CountryCode' => $response_country->CountryCode,
          'CurrencyCode' => 'EUR',
          'Year'  => 13,
          'Month' => 13,
          'Rooms' => array(
            'Room' => array(
              array(
                '@' => array(
                  'Code' => 'DB',
                ),
              ),
            ),
          ),
        ),
      );
      // $response_variants = $this->CircuitSearchRequest($search_data, false);
      $response_variants = $this->CircuitSearchRequest($search_data);
      
      if(empty($response_variants->CircuitSearchResponse) || empty($response_variants->CircuitSearchResponse->Circuit)){
        continue;
      }
      
      foreach($response_variants->CircuitSearchResponse->Circuit as $response_departure){
        if(empty($response_departure)){
          continue;
        }
        if(empty($response_departure->Destinations) || empty($response_departure->Destinations->CircuitDestination)){
          continue;
        }
        foreach($response_departure->Destinations->CircuitDestination as $response_destination){
          if(!$response_destination){
            continue;
          }
          if(!$response_destination->CityCode){
            continue;
          }
          if(!isset($paralela45_data['Cities'][$response_destination->CityCode])){
            continue;
          }
          if(!isset($paralela45_data['CityLinks']['Destination'][$response_destination->CityCode])){
            $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode] = array();
          }
        }
        if(empty($response_departure->Variants) || empty($response_departure->Variants->Variant)){
          continue;
        }
        foreach($response_departure->Variants->Variant as $response_variant){
          if(!$response_variant){
            continue;
          }
          if(!$response_variant->InfoCharter){
            continue;
          }
          if(!isset($paralela45_data['Cities'][$response_variant->InfoCharter->DepArrCodLoc])){
            $paralela45_data['Cities'][$response_variant->InfoCharter->DepArrCodLoc] = array(
              'CityName' => $response_variant->InfoCharter->DepArrLoc,
              'CountryCode' => 'RO',
            );
            $paralela45_data['Countries']['RO']['CityCodes'][] = $response_variant->InfoCharter->DepArrCodLoc;
            $departure_city_alias = trim(preg_replace('/\W+/', '_', strtolower($response_variant->InfoCharter->DepArrLoc)), ' _');
            $paralela45_data['Aliases'][$departure_city_alias][] = $response_variant->InfoCharter->DepArrCodLoc;
          }
          if(!isset($paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc])){
            $paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc] = array();
          }
          foreach($response_departure->Destinations->CircuitDestination as $response_destination){
            if(!$response_destination){
              continue;
            }
            if(!$response_destination->CityCode){
              continue;
            }
            if(!isset($paralela45_data['Cities'][$response_destination->CityCode])){
              continue;
            }
            if(!in_array($response_destination->CityCode, $paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc])){
              $paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc][] = $response_destination->CityCode;
            }
            if(!in_array($response_variant->InfoCharter->DepArrCodLoc, $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode])){
              $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode][] = $response_variant->InfoCharter->DepArrCodLoc;
            }
            $dep_date_arr = explode(' ', $response_variant->InfoCharter->DepDate);
            $dep_date = $dep_date_arr[0];
            $paralela45_data['Dates'][$response_variant->InfoCharter->DepArrCodLoc][$response_destination->CityCode][$dep_date][] = array(
              'TourOpCode' => property_exists($response_departure,'TourOpCode') ? $response_departure->TourOpCode : '0',
              'Nights' => property_exists($response_departure,'Period') ? $response_departure->Period : '0',
              'Sosire' => $response_variant->InfoCharter->RetDate,
            );
            $paralela45_data['Months'][$response_variant->InfoCharter->DepArrCodLoc][$response_destination->CityCode][substr($dep_date,0,7)][] = array(
              'TourOpCode' => property_exists($response_departure,'TourOpCode') ? $response_departure->TourOpCode : '0',
              'Nights' => property_exists($response_departure,'Period') ? $response_departure->Period : '0',
              'Sosire' => $response_variant->InfoCharter->RetDate,
            );
          }
        }
      }
    }
	if(false !== $cache_time){
		$this->cache->save($cache_hash, json_encode($paralela45_data), $cache_time);
	}
    return $paralela45_data;
  }
  public function getCircuitSearchCityItem($product,$cache_time = 86400){
    $paralela45_data = array(
      'Countries' => array(),
      'Cities' => array(),
      'CityLinks' => array(
        'Departure' => array(),
        'Destination' => array(),
      ),
      'Aliases' => array(),
      'Dates' => array(),
      'Months' => array(),
    );
    foreach($product->Destinations->Destination as $destination){
      if(!isset($paralela45_data['Countries'][$destination->CountryCode])){
        $paralela45_data['Countries'][$destination->CountryCode] = array(
          'CountryName' => $destination->CountryName,
          'CityCodes' => array(),
        );
      }
      if(!in_array($destination->CityCode,$paralela45_data['Countries'][$destination->CountryCode]['CityCodes'])){
        $paralela45_data['Countries'][$destination->CountryCode]['CityCodes'][] = $destination->CityCode;
        $paralela45_data['Cities'][$destination->CityCode] = array(
          'CityName' => $destination->CityName,
          'CountryCode' => $destination->CountryCode,
        );
      }
    }
    if(!isset($paralela45_data['Countries']['RO'])){
      $paralela45_data['Countries']['RO'] = array(
        'CountryName' => 'Romania',
        'CityCodes' => array(),
      );
    }
    $search_data = array(
      'CircuitSearchRequest' => array(
        'CountryCode' => $product->CountryCode,
        'CurrencyCode' => 'EUR',
        'Year'  => 13,
        'Month' => 13,
        'Rooms' => array(
          'Room' => array(
            array(
              '@' => array(
                'Code' => 'DB',
              ),
            ),
          ),
        ),
      ),
    );
    // $response_variants = $this->CircuitSearchRequest($search_data, false);
    $response_variants = $this->CircuitSearchRequest($search_data);
    
    if(empty($response_variants->CircuitSearchResponse) || empty($response_variants->CircuitSearchResponse->Circuit)){
      return $paralela45_data;
    }
    foreach($response_variants->CircuitSearchResponse->Circuit as $response_departure){
      if($response_departure->CircuitId != $product->ProductCode){
        continue;
      }
      if(empty($response_departure)){
        continue;
      }
      if(empty($response_departure->Destinations) || empty($response_departure->Destinations->CircuitDestination)){
        continue;
      }
      foreach($response_departure->Destinations->CircuitDestination as $response_destination){
        if(!$response_destination){
          continue;
        }
        if(!$response_destination->CityCode){
          continue;
        }
        if(!isset($paralela45_data['Cities'][$response_destination->CityCode])){
          continue;
        }
        if(!isset($paralela45_data['CityLinks']['Destination'][$response_destination->CityCode])){
          $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode] = array();
        }
      }
      if(empty($response_departure->Variants) || empty($response_departure->Variants->Variant)){
        continue;
      }
      foreach($response_departure->Variants->Variant as $response_variant){
        if(!$response_variant){
          continue;
        }
        if(!$response_variant->InfoCharter){
          continue;
        }
        if(!isset($paralela45_data['Cities'][$response_variant->InfoCharter->DepArrCodLoc])){
          $paralela45_data['Cities'][$response_variant->InfoCharter->DepArrCodLoc] = array(
            'CityName' => $response_variant->InfoCharter->DepArrLoc,
            'CountryCode' => 'RO',
          );
          $paralela45_data['Countries']['RO']['CityCodes'][] = $response_variant->InfoCharter->DepArrCodLoc;
          $departure_city_alias = trim(preg_replace('/\W+/', '_', strtolower($response_variant->InfoCharter->DepArrLoc)), ' _');
          $paralela45_data['Aliases'][$departure_city_alias][] = $response_variant->InfoCharter->DepArrCodLoc;
        }
        if(!isset($paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc])){
          $paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc] = array();
        }
        foreach($response_departure->Destinations->CircuitDestination as $response_destination){
          if(!$response_destination){
            continue;
          }
          if(!$response_destination->CityCode){
            continue;
          }
          if(!isset($paralela45_data['Cities'][$response_destination->CityCode])){
            continue;
          }
          if(!in_array($response_destination->CityCode, $paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc])){
            $paralela45_data['CityLinks']['Departure'][$response_variant->InfoCharter->DepArrCodLoc][] = $response_destination->CityCode;
          }
          if(!in_array($response_variant->InfoCharter->DepArrCodLoc, $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode])){
            $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode][] = $response_variant->InfoCharter->DepArrCodLoc;
          }
          $dep_date_arr = explode(' ', $response_variant->InfoCharter->DepDate);
          $dep_date = $dep_date_arr[0];
          $paralela45_data['Dates'][$response_variant->InfoCharter->DepArrCodLoc][$response_destination->CityCode][$dep_date][] = array(
            'TourOpCode' => property_exists($response_departure,'TourOpCode') ? $response_departure->TourOpCode : '0',
            'Nights' => property_exists($response_departure,'Period') ? $response_departure->Period : '0',
            'Sosire' => $response_variant->InfoCharter->RetDate,
          );
          $paralela45_data['Months'][$response_variant->InfoCharter->DepArrCodLoc][$response_destination->CityCode][substr($dep_date,0,7)][] = array(
            'TourOpCode' => property_exists($response_departure,'TourOpCode') ? $response_departure->TourOpCode : '0',
            'Nights' => property_exists($response_departure,'Period') ? $response_departure->Period : '0',
            'Sosire' => $response_variant->InfoCharter->RetDate,
          );
        }
      }
    }
    return $paralela45_data;
  }
  public function getPackageNVRoutes($cache_time = 3600, $retrieve_from_cache = true){
    setCacheStorage('paralela45');
    $cache_hash = 'paralela45/' . __FUNCTION__;
    $version = '1.0.4';
    if($retrieve_from_cache !== false){
      if($response = $this->cache->get($cache_hash, $cache_time)){
        $response = json_decode($response, true);
        if($response){
          $response_version = isset($response['Version']) ? $response['Version'] : null;
          if($response_version === $version){
            return $response;
          }
        }
      }
    }
    $paralela45_data = array(
      'Countries' => array(),
      'Cities' => array(),
      'CityLinks' => array(
        'Departure' => array(),
        'Destination' => array(),
      ),
      'ZoneLinks' => array(
        'Departure' => array(),
        'Destination' => array(),
      ),
      'Aliases' => array(),
      'Zones' => array(),
      'Dates' => array(),
      'Version' => $version,
    );
    $response = $this->getPackageNVRoutesRequest($cache_time, $retrieve_from_cache);
    if(empty($response) || empty($response->getPackageNVRoutesResponse) || empty($response->getPackageNVRoutesResponse->Country)){
      return $paralela45_data;
    }
    foreach($response->getPackageNVRoutesResponse->Country as $response_country){
      if(empty($response_country)){
        continue;
      }
      if(!isset($paralela45_data['Countries'][$response_country->CountryCode])){
        $paralela45_data['Countries'][$response_country->CountryCode] = array(
          'CountryName' => $response_country->CountryName,
          'CityCodes' => array(),
          'Zones' => array(),
        );
      }
      if(empty($response_country->Destinations) || empty($response_country->Destinations->Destination)){
        continue;
      }
      foreach($response_country->Destinations->Destination as $response_destination){
        if(empty($response_destination)){
          continue;
        }
        if(!isset($paralela45_data['Cities'][$response_destination->CityCode])){
          $paralela45_data['Cities'][$response_destination->CityCode] = array(
            'CityName' => $response_destination->CityName,
            'CountryCode' => $response_country->CountryCode,
            'ZoneCode' => $response_destination->ZoneCode,
          );
          $paralela45_data['Countries'][$response_country->CountryCode]['CityCodes'][] = $response_destination->CityCode;
          $destination_city_alias = trim(preg_replace('/\W+/', '_', strtolower($response_destination->CityName)), ' _');
          $paralela45_data['Aliases'][$destination_city_alias][] = $response_destination->CityCode;
        }
        if(empty($response_destination->Departures) || empty($response_destination->Departures->Departure)){
          continue;
        }
        
        if(!isset($paralela45_data['Zones'][$response_destination->ZoneCode])){
          $paralela45_data['Zones'][$response_destination->ZoneCode] = array(
            'ZoneName' => $response_destination->ZoneName,
            'CountryCode' => $response_country->CountryCode,
          );
          $destination_zone_alias = trim(preg_replace('/\W+/', '_', strtolower($response_destination->ZoneName)), ' _');
          $paralela45_data['Aliases'][$destination_zone_alias][] = $response_destination->ZoneCode;
          $paralela45_data['Countries'][$response_country->CountryCode]['Zones'][] = $response_destination->CityCode;
        }
        $paralela45_data['ZoneLinks']['Destination'][$response_destination->ZoneCode][] = $response_destination->CityCode;
        foreach($response_destination->Departures->Departure as $response_departure){
          if(empty($response_departure)){
            continue;
          }
          if(!isset($paralela45_data['Countries'][$response_departure->CountryCode])){
            $paralela45_data['Countries'][$response_departure->CountryCode] = array(
              'CountryName' => $response_departure->CountryName,
              'CityCodes' => array(),
            );
          }
          if(!isset($paralela45_data['Cities'][$response_departure->CityCode])){
            $paralela45_data['Cities'][$response_departure->CityCode] = array(
              'CityName' => $response_departure->CityName,
              'CountryCode' => $response_departure->CountryCode,
            );
            $paralela45_data['Countries'][$response_departure->CountryCode]['CityCodes'][] = $response_departure->CityCode;
            $departure_city_alias = trim(preg_replace('/\W+/', '_', strtolower($response_departure->CityName)), ' _');
            $paralela45_data['Aliases'][$departure_city_alias][] = $response_departure->CityCode;
          }
          if(empty($response_departure->Dates) || empty($response_departure->Dates->Date)){
            continue;
          }
          $paralela45_data['CityLinks']['Departure'][$response_departure->CityCode][] = $response_destination->CityCode;
          $paralela45_data['CityLinks']['Destination'][$response_destination->CityCode][] = $response_departure->CityCode;
          $paralela45_data['ZoneLinks']['Departure'][$response_destination->ZoneCode][] = $response_departure->CityCode;
          foreach($response_departure->Dates->Date as $response_date){
            if(!$response_date){
              continue;
            }
            $paralela45_data['Dates'][$response_departure->CityCode][$response_destination->CityCode][$response_date->_] = array(
              'TourOpCode' => property_exists($response_date,'TourOpCode') ? $response_date->TourOpCode : '0',
              'Nights' => property_exists($response_date,'Nights') ? $response_date->Nights : '0',
              'Companie' => property_exists($response_date,'Companie') ? $response_date->Companie : '-',
              'Ora' => property_exists($response_date,'Ora') ? $response_date->Ora : '-',
              'Nrzbor' => property_exists($response_date,'Nrzbor') ? $response_date->Nrzbor : '-',
              'Sosire' => property_exists($response_date,'Sosire') ? $response_date->Sosire : '-',
            );
          }
        }
      }
    }
	if(false !== $cache_time){
		$this->cache->save($cache_hash, json_encode($paralela45_data), $cache_time);
	}
    return $paralela45_data;
  }
  public function getPackageNVRoutesRequest($cache_time = 3600, $retrieve_from_cache = true){
    $data = array(
      __FUNCTION__ => array(
        'Transport' => 'search',
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time, $retrieve_from_cache);
    if(!$response){
      return array();
    }
    return $this->getPackageNVRoutesResponse($response);
  }
  public function getPackageNVRoutesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Country' => array(
          0 => true,
          'Destinations' => array(
            'Destination' => array(
              0 => true,
              'Departures' => array(
                'Departure' => array(
                  0 => true,
                  'Dates' => array(
                    'Date' => array(
                      0 => true,
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ));
  }
  public function getPackageNVPriceRequest($search_data = array(), $cache_time = 3600){
    if(!$search_data){
      $search_data = array(
        'CountryCode' => 'HR',
        'CityCode' => 'HRDBR',
        'DepCountryCode' => 'RO',
        'DepCityCode' => 'ROBCH1',
        'Transport' => 'plane',
        'CurrencyCode' => 'EUR',
        'PeriodOfStay' => array(
          'CheckIn' => '2018-08-01',
          'CheckOut' => '2018-08-08',
        ),
        'Rooms' => array(
          'Room' => array(
            array(
              '@' => array(
                'Code' => 'DB',
                'NoAdults' => 1,
                'NoChildren' => 1,
              ),
              'Children' => array(
                'Age' => array(
                  7
                ),
              ),
            ),
          ),
        ),
        // 'Days' => 7,
        // 'TourOpCode' => 'P45',
        // 'ProductName' => 'condor',
        // 'ProductCategory' => '2',
        // 'MealTypes' => array(
          // 'MealType' => array(
            // 1,
          // ),
        // ),
        // 'Facilities' => array(
          // 'Facility' => array(
            // 6,
            // 10,
          // ),
        // ),
        'Language' => 'RO',
        'OfferType' => 'TOATE',
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->getPackageNVPriceResponse($response);
  }
  public function getPackageNVPriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Hotel' => array(
          0 => true,
          'Offers' => array(
            'Offer' => array(
              0 => true,
              'BookingRoomTypes' => array(
                'Room' => true,
              ),
              'Meals' => array(
                'Meal' => true,
              ),
              'PriceDetails' => array(
                'Services' => array(
                  'Service' => true,
                ),
              ),
            ),
          ),
        ),
      ),
    ));
  }
  public function getCharterCitiesRequest(){
    $data = array(
      __FUNCTION__ => array(
        'DepartureType' => 'departure',
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getCharterCitiesResponse($response);
  }
  public function getCharterCitiesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Country' => array(
          0 => true,
          'Cities' => array(
            'City' => true,
          ),
        ),
      ),
    ));
  }
  public function getCharterPriceRequest(){
    $data = array(
      __FUNCTION__ => array(
        'Transport' => 'search',
        'Departure' => array(
          'CountryCode' => 'HR',
          'CityCode' => 'HRDBR',
        ),
        'Destination' => array(
          'CountryCode' => 'RO',
          'CityCode' => 'ROBCH1',
        ),
        'DepartureDate' => '2018-08-01',
        'CurrencyCode' => 'EUR',
        'ReturnDate' => '2018-08-08',
        'FlexibleDate' => true,
        'Language' => 'RO',
        'PaxNames' => array(
          'PaxName' => array(
            array(
              'PaxType' => 'adult',
              '_' => 'TEST/TEST',
            ),
            array(
              'PaxType' => 'child',
              'ChildAge' => 3,
              '_' => 'TEST/TEST',
            ),
          ),
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getCharterPriceResponse($response);
  }
  public function getCharterPriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Charter' => array(
          0 => true,
          'Offers' => array(
            'Offer' => true,
          ),
        ),
      ),
    ));
  }
  public function getCharterServiceRequest(){
    $data = array(
      __FUNCTION__ => array(
        'TourOpCode' => 'P45',
        'CharterId' => '3291|ASTEPTARE|7276|1',
        'Date' => '2018-08-01',
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getCharterServiceResponse($response);
  }
  public function getCharterServiceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Services' => array(
          'Service' => true,
        ),
      ),
    ));
  }
  public function getCharterServicePriceRequest(){
    $data = array(
      __FUNCTION__ => array(
        'TourOpCode' => 'P45',
        'CurrencyCode' => 'EUR',
        'CharterId' => '3291|ASTEPTARE|7276|1',
        'Date' => '2018-08-01',
        'ServiceId' => 5,
        'PaxNames' => array(
          'PaxName' => array(
            array(
              'PaxType' => 'adult',
              '_' => 'TEST/TEST',
            ),
            array(
              'PaxType' => 'child',
              'ChildAge' => 3,
              '_' => 'TEST/TEST',
            ),
          ),
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getCharterServicePriceResponse($response);
  }
  public function getCharterServicePriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Services' => array(
          'Service' => true,
        ),
      ),
    ));
  }
  public function CircuitSearchCityRequest($cache_time = 7200, $retrieve_from_cache = true){
    $data = array(
      __FUNCTION__ => array(),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time, $retrieve_from_cache = true);
    if(!$response){
      return array();
    }
    return $this->CircuitSearchCityResponse($response);
  }

  public function CircuitSearchCityResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Country' => array(
          0 => true,
          'Cities' => array(
            'City' => true,
          ),
        ),
      ),
    ));
  }
  public function CircuitSearchRequest($search_data = array(), $cache_time = 7200){
    // print_r($search_data);die;
    if(!$search_data){
      $search_data = array(
        'CountryCode' => 'AT',
        'CityCode' => 'ATVNN1',
        'CurrencyCode' => 'EUR',
        'Year' => 13,
        'Month' => 13,
        'Rooms' => array(
          'Room' => array(
            array(
              '@' => array(
                'Code' => 'DB',
                'NoAdults' => 1,
              ),
            ),
          ),
        ),
      );
    }
    
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->CircuitSearchResponse($response);
  }
  public function CircuitSearchResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Circuit' => array(
          0 => true,
          'DayDescriptions' => array(
            'DayDescription' => true,
          ),
          'Destinations' => array(
            'CircuitDestination' => true,
          ),
          'Variants' => array(
            'Variant' => array(
              0 => true,
              'Rooms' => array(
                'Room' => true,
              ),
              'Services' => array(
                'Service' => true,
              ),
            ),
          ),
        ),
      ),
    ));
  }
  public function CircuitSearchServiceRequest($data = array(), $cache_time = 7200){
    if(!$data){
      $data = array(
        __FUNCTION__ => array(
          'CircuitId' => '1727|P45',
          'CircuitDep' => 21753,
        ),
      );
    }
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->CircuitSearchServiceResponse($response);
  }
  public function CircuitSearchServiceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Services' => array(
          'Service' => true,
        ),
      ),
    ));
  }
  public function CircuitSearchServicePriceRequest($search_data = array(), $cache_time=7200){
    if(!$search_data){
      $search_data = array(
        'CircuitId' => '1560|P45',
        'CircuitDep' => 1,
        'SuppType' => 'charters',
        'Service' => 1,
        'CurrencyCode' => 'EUR',
        'PaxNames' => array(
          'PaxName' => array(
            array(
              'PaxType' => 'adult',
              '_' => 'TEST/TEST',
            ),
            array(
              'PaxType' => 'child',
              'ChildAge' => 3,
              '_' => 'TEST/TEST',
            ),
          ),
        ),
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->CircuitSearchServicePriceResponse($response);
  }
  public function CircuitSearchServicePriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Services' => array(
          'Service' => true,
        ),
      ),
    ));
  }
  public function CircuitFeesRequest($search_data = array(), $cache_time = 1800){
    if(!$search_data){
      $search_data = array(
        'UniqueId' => '18184_196_1|EU',
      );
    }
    $data = array(
      __FUNCTION__ => $search_data,
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true, $cache_time);
    if(!$response){
      return array();
    }
    return $this->CircuitFeesResponse($response);
  }
  public function CircuitFeesResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Service' => true,
      ),
    ));
  }
  public function getTicketPriceRequest(){
    $data = array(
      __FUNCTION__ => array(
        'CountryCode' => 'RO',
        'CityCode' => 'debrl1', // 'CityName' => '',
        'CheckIn' => '2018-08-01',
        'CheckOut' => '2018-08-08',
        'CurrencyCode' => 'EUR',
        'PaxNames' => array(
          'PaxName' => array(
            array(
              'PaxType' => 'adult',
              '_' => 'TEST/TEST',
            ),
            array(
              'PaxType' => 'child',
              'ChildAge' => 3,
              '_' => 'TEST/TEST',
            ),
          ),
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getTicketPriceResponse($response);
  }
  public function getTicketPriceResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Ticket' => array(
          0 => true,
          'Product' => array(
            'Pictures' => array(
              'Picture' => true,
            ),
          ),
          'Offers' => array(
            'Offer' => array(
              0 => true,
              'Availabilities' => array(
                'Availability' => true,
              ),
            ),
          ),
        ),
      ),
    ));
  }
  public function getTicketDetailsRequest(){
    $data = array(
      __FUNCTION__ => array(
        'PackageId' => '688124|7',
        'VariantId' => 1,
        'CurrencyCode' => 'EUR',
        'PeriodOfStay' => array(
          'CheckIn' => '2018-08-01',
          'CheckOut' => '2018-08-08',
        ),
      ),
    );
    $request = arr2xml($data);
    $response = $this->request($request, __FUNCTION__ , true);
    if(!$response){
      return array();
    }
    return $this->getTicketDetailsResponse($response);
  }
  public function getTicketDetailsResponse($response){
    return xml2obj($response->ResponseDetails->{__FUNCTION__}, array(
      __FUNCTION__ => array(
        'Product' => array(
          0 => true,
          'Descriptions' => array(
            'Description' => true,
          ),
          'Pictures' => array(
            'Picture' => true,
          ),
          'Features' => array(
            'Feature' => true,
          ),
          'Comments' => array(
            'Comment' => true,
          ),
          'Remarks' => array(
            'Remark' => true,
          ),
        ),
      ),
    ));
  }
}
