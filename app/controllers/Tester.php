<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Tester extends MX_Controller {
  public function test_epay_mail() {
        $response = [
            'NAME' => 'test name',
            'EAN' => 'test ean',
        ];
        $func = 'get';
        $this->theme->set_theme('accent');
        $this->theme->set_layout('blank');
        $this->theme->set_sublayout('frontend/blank/index');
        if(isset($func) && in_array($func,array('get','activate'))){
            Modules :: run ('Mailer/epay_coupon_activate2', array('response' => $response));
        }
  }
  public function testphone() {
	  $phone = '+40 +40 0771255279';
	  echo preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', $phone);
	  die;
  }
  public function locations() {
	  $q = $this->db->query("SELECT iso_2, name, name_RO FROM `ac_country`");
	  $countries_alt = array_reduce($q->result(), function($carry, $item){
		  $carry[$item->iso_2] = (array)$item;
		  return $carry;
	  }, []);
	  
	  // echo '<pre>';
	  // print_r($countries_alt);
	  // die;
	 $file_location = realpath(APPPATH . '../') . '/resources/flight_locations.js';
	  
	$this->load->model('Trip_model');
	$this->api = $this->Trip_model->get_api();
	
	$resp = $this->api->call('index.php/static-data/countries', [
		'limit' => -1,
		// 'limit' => 10,
		// 'filter' => [['name' => 'Type', 'term' => 'airport']],
	]);
	// echo '<pre>';
	// print_r($resp->_embedded->countries);
	// die;
	$countries_naming_format = [
		'CountryName',
		'CountryISO',
		'CountryAltName',
		'CountryAltName1',
		'CountryAltName2',
	];
	$countries = array_reduce($resp->_embedded->countries, function($c, $v) use (&$countries_alt){
		$name = $v->Name;
		$nameAlt = [];
		if(isset($countries_alt[$v->ISO])){
			if(!empty($countries_alt[$v->ISO]['name']) && $countries_alt[$v->ISO]['name'] != $v->Name){
				$nameAlt[] = ucfirst(URLify::downcode('' . $countries_alt[$v->ISO]['name'], 'en'));
			}
			if(!empty($countries_alt[$v->ISO]['name_RO']) && $countries_alt[$v->ISO]['name_RO'] != $v->Name && $countries_alt[$v->ISO]['name_RO'] != $countries_alt[$v->ISO]['name']){
				$name = ucfirst(URLify::downcode('' . $countries_alt[$v->ISO]['name_RO'], 'en'));
				$nameAlt[] = $v->Name;
			}
		}
		switch($v->ISO){
			case 'GB': $nameAlt[] = 'Anglia'; $nameAlt[] = 'England'; break;
			case 'NL': $nameAlt[] = 'Olanda'; $nameAlt[] = 'Holland'; break;
		}
		$nameAlt = array_unique($nameAlt);
		$c[$v->Id] = [
			$name,
			$v->ISO,
			$nameAlt[0] ?? null,
			$nameAlt[1] ?? null,
			$nameAlt[2] ?? null,
		]; 
		return $c;
	},  []);
	// echo '<pre>';
	// print_r($countries);
	// die;
	
	$resp = $this->api->call('index.php/static-data/cities', [
		'limit' => -1,
		// 'limit' => 10,
		// 'filter' => [['name' => 'Type', 'term' => 'airport']],
	]);
	
	$cities_naming_format = [
		'CountryId',
		'CityName',
		'CityCode',
		'CityAltName',
		'CityAltName1',
	];
	// echo '<pre>';
	// print_r($resp->_embedded->cities);
	// die;
	$cities = array_reduce($resp->_embedded->cities, function($c, $v) use (&$countries){
		if(!isset($countries[$v->CountryId])) return $c;
		if(!preg_match('/\bair\b/', isset($v->SearchableOn) ? $v->SearchableOn : '')) return $c;
		$altName = $v->AltName ? json_decode($v->AltName, true) : [];
		$altName = array_shift($altName);
		$altName1 = $altName;
		$name = $v->Name;
		switch($v->Code){
			case 'LON': $altName = 'Londra'; break;
			case 'PRG': $altName = 'Praga'; break;
			case 'HAG': $altName = 'Haga'; break;
			case 'BEG': $altName = 'Belgrad'; break;
			case 'BUD': $altName = 'Budapesta'; break;
			case 'CPH': $altName = 'Copenhaga'; break;
			case 'MOW': $altName = 'Moscova'; break;
		}
		if(isset($altName)){
			$name = $altName;
			$altName = $v->Name;
		}
		$c[$v->Id] = [
			$v->CountryId,
			$name,
			$v->Code,
			$altName,
			$altName1 ?? null,
		];
		return $c;
	},  []);
	
	// echo '<pre>';
	// print_r($cities);
	// die;
	$resp = $this->api->call('index.php/static-data/locations', [
		'limit' => -1,
		'filter' => [['name' => 'Type', 'term' => 'airport']],
	]);
	$airports_naming_format = [
		'CityId',
		'AirportName',
		'AirportCode',
		'AirportAltName',
	];
	$airports = array_reduce($resp->_embedded->locations, function($c, $v) use (&$cities){
		if(!isset($cities[$v->CityId])) return $c;
		$altName = null;
		switch($v->Code){
			case 'OTP': $altName = 'Otopeni'; break;
		}
		$c[$v->Id] = [
			$v->CityId,
			$v->Name,
			$v->Code,
			$altName,
		];
		return $c;
	},  []);
	$airports_cities = array_unique(array_column($airports, array_search('CityId', $airports_naming_format)));
	
	// echo '<pre>';
	// var_dump($airports_cities);
	// die;
	// echo '<pre>';
	// var_dump($airports_cities);
	// die;
	$cities = array_intersect_key($cities, array_flip($airports_cities));
	$cities_countries = array_unique(array_column($cities, array_search('CountryId', $cities_naming_format)));
	$countries = array_intersect_key($countries, array_flip($cities_countries));
	
	$result = [
		'countries'=> ['format' => $countries_naming_format, 'values' => $countries],
		'cities'=> ['format' => $cities_naming_format, 'values' => $cities],
		'airports'=> ['format' => $airports_naming_format, 'values' => $airports],
	];
	
	file_put_contents($file_location, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	exit;
	// file_put_contents
	echo '<pre>';
	var_dump($result);
	die;
	  // echo file_get_contents('https://rest.accenttravel.ro/rest/app/clients/online/public/index.php/static-data/locations?limit=-1&filter[0][name]=Type&filter[0][term]=airport');
	  // die;
  }
  public function checkout_pay24($order_id = null) {
	  ini_set('display_errors', 1);
    themeFunctions::enableDebug();
    $order_id = $order_id ?? (int)$_GET['order_id'];
    $to = isset($_GET['to']) && strlen($_GET['to']) ? $_GET['to'] : null;
    $config = array();
	
	// var_dump($to); die;
    // $config['protocol'] = 'smtp';
    // $config['mailpath'] = '/usr/sbin/sendmail';
    // $config['mailtype'] = 'html';
    // $config['useragent'] = 'PHPMailer';
    // $config['smtp_host'] = 'mail.accenttravel.ro';
    // $config['smtp_user'] = 'vanzari@accenttravel.ro';
    // $config['smtp_pass'] = 'sNF4w8en';
    
    Modules :: run ('Mailer/checkout_auto', array(
      'order_id'=>$order_id, 
      'from_email'=>'pay24@accenttravel.ro', 
      // 'from_name'=>'Vanzari', 
      // 'config'=>$config, 
      'to'=>'tchirvasa@gmail.com', 
      // 'bcc'=>array(
        // 'tudor.chirvasa@lisal.ro',
      // ), 
      'prevent_send_email'=>true,
      'output_html'=>true
    ));
  }
  public function flightstuff2() {
	  $ro_dat = include ('/var/www/html/rest/app/clients/online/data/locations/flight-locations/non-rtl/locations-names/ro.php.dat');
	  echo json_encode($ro_dat);
  }
	public function orderdoc($trip_order_id = null) {
		$trip_order_id = (int)$trip_order_id;
		
		$this->load->model('TripOrder_model');
		// $trip_order_id = 623; // H
		// $trip_order_id = 622; // H
		// $trip_order_id = 629; // H
		// $trip_order_id = 436; // H
		// $trip_order_id = 621; // H
		$trip_order_invoices = $this->TripOrder_model->getOrderInvoice($trip_order_id);
		// echo '<pre>';
		// print_r($trip_order_invoices); die;
		if($trip_order_invoices && !empty($trip_order_invoices->_embedded) && !empty($trip_order_invoices->_embedded->invoice)){
			$document_ids = array();
			foreach($trip_order_invoices->_embedded->invoice as $invoice){
				if(!empty($invoice->Documents)){
					foreach($invoice->Documents as $document){
						$document_ids[] = $document->DocId;
					}
				}
			}
			// $document_ids = array_map(function($value){return $value->DocId;},$trip_order_invoices->_embedded->invoice);
			// print_r($document_ids);
			// die;
			sort($document_ids);
			$document_id = array_pop($document_ids);
			
			$this->db->where_in('trip_order_id', array($trip_order_id));
			$this->db->where_in('provider', array('trip'));
			$q = $this->db->get('ac_trip_order');
			$order = $q->row();
			$id = $order->id;
			// echo '<pre>';
			// print_r($order);
			// die;
			
			
			// echo 'test';
			$trip_order_invoice = $this->TripOrder_model->getOrderDocument($trip_order_id,$document_id);
			if($trip_order_invoice){
				echo "FACTURA OK!";
			/* 
				$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		
				$file_deposit_path = $facturi_path . $id . '.pdf';
				
				file_put_contents($file_deposit_path, $trip_order_invoice);
				
				echo $file_deposit_path; die;
			*/
			} else {
				echo "FARA FACTURA!";
			}
			
			// echo 'test';
			// print_r($trip_order_invoice);
			// print_r($this->Trip_model->get_api()->calls);
			// die;
			// print_r($trip_order_invoice); die;
			echo '<pre>';
			// print_r($trip_order_invoices);
			print_r(json_encode($trip_order_invoices, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			// print_r(json_encode($this->Trip_model->get_api()->calls, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			die;
		}
		echo '<pre>';
		// print_r($trip_order_invoices);
		// print_r(json_encode($trip_order_invoices, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		print_r(json_encode($this->Trip_model->get_api()->calls, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		die;
		// echo $this->getTripError('Comanda invalida');
	}
  public function flightstuff() {
	  die;
	  $country_name_to_id = array();
	  $city_id_to_city = array(
		
	  );
	  $countries = array();
	  $cities = array();
	  $locations = array();
	  $en_dat = include ('/var/www/html/rest/app/clients/online/data/locations/flight-locations/non-rtl/locations-names/en.php.dat');
	  $ro_dat = include ('/var/www/html/rest/app/clients/online/data/locations/flight-locations/non-rtl/locations-names/ro.php.dat');
	  foreach(glob("/var/www/html/rest/app/clients/online/data/locations/flight-locations/non-rtl/locations-alternate-names/*.php.dat") as $file){
		  $alt = include($file);
		  foreach($alt as $k=>$v){
			  $LocationId = isset($v['LocationId']) ? (int)$v['LocationId'] : 0;
			  $LocationCode = isset($v['LocationCode']) ? $v['LocationCode'] : 0;
			  $CityId = isset($v['CityId']) ? (int)$v['CityId'] : 0;
			  $CountryId = isset($v['CountryId']) ? (int)$v['CountryId'] : 0;
			  $SubItem = isset($v['SubItem']) ? (int)$v['SubItem'] : 0;
			  
			  if($SubItem){
				  if(!isset($en_dat['subItems'][$LocationId])){
					  continue;
				  }
				  if(!isset($ro_dat['subItems'][$LocationId])){
					  $ro_dat['subItems'][$LocationId] = $en_dat['subItems'][$LocationId];
					  continue;
				  }
			  }
			  if(!isset($en_dat['items'][$CityId])){
				  continue;
			  }
			  $en_dat['items'][$CityId]['CityName'] = ucwords(strtolower($en_dat['items'][$CityId]['CityName']));
			  $en_dat['items'][$CityId]['CityName'] = preg_replace_callback("/[[:punct:]]\w/", function($matches){return strtoupper($matches[0]); }, $en_dat['items'][$CityId]['CityName']);
			  $en_dat['items'][$CityId]['CityName'] = str_replace("'S", "'s", $en_dat['items'][$CityId]['CityName']);
			  $en_dat['items'][$CityId]['CityName'] = str_replace(" And ", " and ", $en_dat['items'][$CityId]['CityName']);
			  $en_dat['items'][$CityId]['CityName'] = str_replace(" Of ", " of ", $en_dat['items'][$CityId]['CityName']);
			  if(!isset($ro_dat['items'][$CityId])){
				  $ro_dat['items'][$CityId] = $en_dat['items'][$CityId];
				  continue;
			  }
			  $NoHotels = isset($v['NoHotels']) ? (int)$v['NoHotels'] : 0;
			  $CityCode = isset($v['CityCode']) ? $v['CityCode'] : '';
			  $AlternateNames = isset($v['AlternateNames']) ? trim($v['AlternateNames']) : '';
			  
			  if($CountryId){
				  if(!isset($countries[$CountryId])){
					  $countries[$CountryId] = array(
						'CountryName' => '',
						'AlternateNames' => '',
					  );
				  }
				  if($CityId){
					  if(empty($countries[$CountryId]['CountryName'])){
						  $countries[$CountryId]['CountryName'] = '' . ($ro_dat['items'][$CityId]['CountryName'] ?? ($en_dat['items'][$CityId]['CountryName'] ?? ''));
					  }
					  if(empty($countries[$CountryId]['AlternateNames'])){
						  $countries[$CountryId]['AlternateNames'] = $en_dat['items'][$CityId]['CountryName'] ?? '';
						  if(strtolower($countries[$CountryId]['AlternateNames']) == strtolower($countries[$CountryId]['CountryName'])){
							  $countries[$CountryId]['AlternateNames'] = '';
						  }
					  }
				  }
			  }
			  if($CityId){
				  if(!isset($cities[$CityId])){
					  $cities[$CityId] = array(
						'CountryId' => '0',
						'CityCode' => '',
						'CityName' => '',
						'AlternateNames' => '',
					  );
				  }
				  if(empty($cities[$CityId]['CountryId'])){
					  $cities[$CityId]['CountryId'] = '' . $CountryId;
				  }
				  if(empty($cities[$CityId]['CityCode'])){
					  $cities[$CityId]['CityCode'] = '' . $CityCode;
				  }
				  if(empty($cities[$CityId]['CityName'])){
					  $cities[$CityId]['CityName'] = '' . ($ro_dat['items'][$CityId]['CityName'] ?? ($en_dat['items'][$CityId]['CityName'] ?? ''));
				  }
				  if(empty($cities[$CityId]['AlternateNames'])){
					  $cities[$CityId]['AlternateNames'] = $en_dat['items'][$CityId]['CityName'] ?? '';
					  
					  if(strtolower($cities[$CityId]['AlternateNames']) == strtolower($cities[$CityId]['CityName'])){
						  $cities[$CityId]['AlternateNames'] = '';
					  }
				  }
			  }
			  if($LocationId){
				  $locations[$LocationId] = array(
					'CityId' => $CityId,
					'LocationId' => '' . $LocationId,
					'LocationCode' => '' . $LocationCode,
					'LocationName' => ($ro_dat['subItems'][$LocationId]['LocationName'] ?? (($en_dat['subItems'][$LocationId]['LocationName'] ?? ''))),
					// 'LocationName' => ($en_dat['subItems'][$LocationId]['LocationName'] ?? ''),
					// 'CityCode' => '',
					// 'CountryId' => '',
					// 'SubItem' => $SubItem,
					'AlternateNames' => $en_dat['subItems'][$LocationId]['LocationName'] ?? '',
					// 'NoHotels' => '' . $NoHotels,
				  );
				  
				  if(strtolower($locations[$LocationId]['AlternateNames']) == strtolower($locations[$LocationId]['LocationName'])){
					  $locations[$LocationId]['AlternateNames'] = '';
				  }
			  }
		  };
	  }
	  $result = array();
	  foreach($locations as $k=>$location){
		  // $locations[$k]['CityCode'] = $cities[$location['CityId']]['CityCode'];
		  // $locations[$k]['CountryId'] = $cities[$location['CityId']]['CountryId'];
	  }
	  // foreach($countries as $CountryId => $country){
		  // $data = $country;
		  // $data['CountryId'] = $CountryId;
		  // $this->db->insert('trip_flight_countries', $data);
	  // }
	  // foreach($cities as $CityId => $city){
		  // $data = $city;
		  // $data['CityId'] = $CityId;
		  // $this->db->insert('trip_flight_cities', $data);
	  // }
	  foreach($locations as $LocationId => $location){
		  $data = $location;
		  $data['LocationId'] = $LocationId;
		  $this->db->insert('trip_flight_locations', $data);
	  }
	  echo '<pre>';
	  print_r($countries);
	  print_r(count($countries));
	  die;
  }
  public function fsli() {
	$username = 'cUsHoF1*xPl0QUEKMbef';
	$password = 'Bh%@0W#0Ghz2h8ClQi6z';
	$host = 'https://snpetrom-web-production.herokuapp.com/verify_partner';
	$ch = curl_init($host);
	$payloadName = array(
		'search' => '0745075434',
	);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadName);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$return = curl_exec($ch);
	
	curl_close($ch);
	echo '<pre>';
	echo htmlspecialchars(print_r($return, true));
	die;
  }
  public function testSessionCouponAppliance() {
	  $coupons = $this->session->userdata('test/checkout/coupons');
	  echo '<pre>';
	  print_r($coupons);
	  $this->session->unset_userdata('test/checkout/coupons');
  }
  public function testCouponAppliance() {
	  $coupons = array();
	  $this->load->model('TripCoupon_model');
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(39)->code);
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(4)->code);
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(41)->code);
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(5)->code);
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(51)->code);
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(250)->code);
	  $coupons[] = $this->TripCoupon_model->getValidCouponTest($this->TripCoupon_model->getCouponById(251)->code);
	  
	  $coupons = $this->TripCoupon_model->orderCouponsByAppliance($coupons);
	  $this->session->set_userdata('test/checkout/coupons', $coupons);
	  echo '<pre>';
	  print_r($coupons);
  }
  public function account_register() {
    themeFunctions::enableDebug();
    Modules :: run ('Mailer/account_register', array('user'=>$this->user, 'to'=>'tudor.chirvasa@lisal.ro', 'password' => 'test', 'prevent_send_email'=>true, 'output_html' => true));
  }
  public function account_password() {
    themeFunctions::enableDebug();
    Modules :: run ('Mailer/account_password', array('user'=>$this->user, 'to'=>'tudor.chirvasa@lisal.ro', 'reset_url'=>site_url('/account/reset_password?hash=xzcv7122s'), 'prevent_send_email'=>true));
  }
  public function get_expired_pages() {
    
    $this->db->join('ac_cms_pages_content pc', 'p.page_id = pc.page_id');
    $this->db->where('`route` IS NOT NULL');
    $this->db->where('`params` IS NOT NULL');
    $q = $this->db->get('ac_cms_pages p');
    
    $result = $q->result();
    
    $pages = array();
    global $pages;
    function add_expired_page($page, $reason = 'unknown'){
      global $pages;
      // var_dump($pages);
      $page->_reason = $reason;
      $pages[] = $page;
    }
    
    echo '<pre>';
    $cur_date = new DateTime('today');
    foreach($result as $page_id => $page) try {
      $route = $page->route;
      $params = array();
      parse_str($page->params, $params);
      foreach(array('sdate','edate') as $param_name) if(array_key_exists($param_name,$params)){
        $date = new DateTime($params[$param_name]);
        if($cur_date > $date){
          add_expired_page($page, $param_name . ' in trecut');
          continue;
        }
      }
      list($controller, $view,$rest) = explode('/', $route);
      if($rest){
        continue;
      }
      switch($controller){
        case 'paralela45':
          break;
      }
      
    } catch(Exception $e){
      add_expired_page($page, $e->getMessage());
      continue;
    }
    
    echo '<pre>';
    print_r($pages);
    print_r($result);
    die;
  }
  public function clear_cache_trip_packages() {
    $cache_abs_path = getCacheAbsPath('trip/packages');
    
    $it = new RecursiveDirectoryIterator($cache_abs_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    // echo '<pre>';
    foreach($files as $file) {
        if ($file->isDir()){
          // echo 'rmdir(' . $file->getRealPath() . ');' . PHP_EOL;
          rmdir($file->getRealPath());
        } else {
          // echo 'unlink(' . $file->getRealPath() . ');' . PHP_EOL;
          unlink($file->getRealPath());
        }
    }
    // echo 'rmdir(' . $cache_abs_path . ');' . PHP_EOL;
    rmdir($cache_abs_path);
    // echo $cache_abs_path; 
    echo 'done';
    die;
  }
  public function clear_cache_trip_paralela45() {
		ini_set('display_errors', 1);
    $cache_abs_path = getCacheAbsPath('paralela45');
    
    $it = new RecursiveDirectoryIterator($cache_abs_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    // echo '<pre>';
    foreach($files as $file) {
        if ($file->isDir()){
          // echo 'rmdir(' . $file->getRealPath() . ');' . PHP_EOL;
          rmdir($file->getRealPath());
        } else {
          // echo 'unlink(' . $file->getRealPath() . ');' . PHP_EOL;
          unlink($file->getRealPath());
        }
    }
    // echo 'rmdir(' . $cache_abs_path . ');' . PHP_EOL;
    rmdir($cache_abs_path);
    // echo $cache_abs_path; 
    echo 'done';
    die;
  }
  public function clear_sessions() {
    $cache_abs_path = getCacheAbsPath('sessions');
    
    $it = new RecursiveDirectoryIterator($cache_abs_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    // echo '<pre>';
    foreach($files as $file) {
        if ($file->isDir()){
          // echo 'rmdir(' . $file->getRealPath() . ');' . PHP_EOL;
          rmdir($file->getRealPath());
        } else {
          // echo 'unlink(' . $file->getRealPath() . ');' . PHP_EOL;
          unlink($file->getRealPath());
        }
    }
    // echo 'rmdir(' . $cache_abs_path . ');' . PHP_EOL;
    rmdir($cache_abs_path);
    // echo $cache_abs_path; 
    echo 'done';
    die;
  }
  public function clear_cache_trip() {
    $cache_abs_path = getCacheAbsPath('trip');
    
    $it = new RecursiveDirectoryIterator($cache_abs_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    // echo '<pre>';
    foreach($files as $file) {
        if ($file->isDir()){
          // echo 'rmdir(' . $file->getRealPath() . ');' . PHP_EOL;
          rmdir($file->getRealPath());
        } else {
          // echo 'unlink(' . $file->getRealPath() . ');' . PHP_EOL;
          unlink($file->getRealPath());
        }
    }
    // echo 'rmdir(' . $cache_abs_path . ');' . PHP_EOL;
    rmdir($cache_abs_path);
    // echo $cache_abs_path; 
    echo 'done';
    die;
  }
  public function checkout_custom() {
    themeFunctions::enableDebug();
    $sent = Modules :: run ('Mailer/checkout_custom', array('order_id'=>366, 'to'=>'tudor.chirvasa@lisal.ro', 'prevent_send_email'=>false));
	var_dump($sent);
	var_dump($this->output->get_output());
  }
  public function checkout_auto() {
    themeFunctions::enableDebug();
    $order_id = (int)$_GET['order_id'];
    $to = isset($_GET['to']) && strlen($_GET['to']) ? $_GET['to'] : null;
    $config = array();
	
	// var_dump($to); die;
    // $config['protocol'] = 'smtp';
    // $config['mailpath'] = '/usr/sbin/sendmail';
    // $config['mailtype'] = 'html';
    // $config['useragent'] = 'PHPMailer';
    // $config['smtp_host'] = 'mail.accenttravel.ro';
    // $config['smtp_user'] = 'vanzari@accenttravel.ro';
    // $config['smtp_pass'] = 'sNF4w8en';
    
    Modules :: run ('Mailer/checkout_auto', array(
      'order_id'=>$order_id, 
      // 'from_email'=>'vanzari@accenttravel.ro', 
      // 'from_name'=>'Vanzari', 
      // 'config'=>$config, 
      // 'to'=>$to, 
      // 'bcc'=>array(
        // 'tudor.chirvasa@lisal.ro',
      // ), 
      // 'prevent_send_email'=>!isset($to)
    ));
  }
  public function download_vouchers() {
    themeFunctions::enableDebug();
    $this->load->model('TripOrder_model');
    $trip_order_id = 137; // H
    $trip_order = $this->TripOrder_model->getTripOrder($trip_order_id);
    echo '<pre>';
    print_r($trip_order);
    die;
    $tmp_path = config_item('tmp_path');
    $attachments = array();
    foreach($trip_order->Services as $service){
      $service_id = $service->Id;
      $service_type = $service->Type;
      $documents_response = $this->TripOrder_model->getDocuments($trip_order_id, $service_id);
      if(!$documents_response){
        echo $this->getTripError('Comanda invalida');
        die;
        // Pentru comenzi anulate apare Trip Error: (Cod 400) Bad Request: Incomplete order
      }
      foreach($documents_response->_embedded->documents as $document){
        $document_id = $document->Id;
        $document_name = $document->Name;
        $document_response = $this->TripOrder_model->downloadDocument($trip_order_id, $service_id, $document_id);
        file_put_contents($tmp_path . $document_name,$document_response);
        $attachments[] = array(
          'path' => $tmp_path . $document_name,
          'name' => $service_type . '-' . $document_name,
          'delete' => true,
        );
      }
    }
    Modules :: run ('Mailer/send_email', array(
      'subject'=>'Test atasamente', 
      'view'=>'email/test/attachments', 
      'from_email'=>'vanzari@accenttravel.ro', 
      'from_name'=>'Accent Travel', 
      'to'=>'tudor.chirvasa@lisal.ro', 
      'bcc'=>array(
        'alexandra.oprea@lisal.ro',
      ), 
      'attachments'=>$attachments,
      // 'prevent_send_email'=>true,
    ));
  }
}