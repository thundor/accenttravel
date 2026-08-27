<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Checkout extends MX_Controller {
  public $on_rq = false;
  public function index() {
    $this->redirect('');
  }
  public function failure() {
    // $this->theme->set_sublayout('frontend/blank/index');
    $this->theme->view('trip/checkout/failure', $this->data, $this);
  }
  public function success() {
    // $this->theme->set_sublayout('frontend/blank/index');
    $this->theme->view('trip/checkout/success', $this->data, $this);
  }
  public function online() {
    $gateway = $this->input->post('gateway');
    if(!$gateway){
      $gateway = 'payu';
    }
    $order_id = $this->input->post('order_id');
    
    $this->data['gateway'] = $gateway;
    $this->data['order_id'] = $order_id;
    
    $this->theme->view('trip/checkout/online', $this->data, $this);
  }
  public function check_fsli($phone) {
	$username = 'cUsHoF1*xPl0QUEKMbef';
	$password = 'Bh%@0W#0Ghz2h8ClQi6z';
	$host = 'https://snpetrom-web-production.herokuapp.com/verify_partner';
	$ch = curl_init($host);
	$payloadName = array(
		'phone_number' => $phone,
	);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
	// curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadName);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$return = curl_exec($ch);
	
	return $return === 'true';
  }
  public function remove_coupon() {
	$coupon_code = trim($this->input->post('coupon_code'));
	$session_coupons =  $this->session->userdata('trip/checkout/coupons');
	if($session_coupons){
		$coupons = array();
		foreach($session_coupons as $k => $session_coupon){
			if($session_coupon['code'] == $coupon_code){
				continue;
			}
			$coupons[] = $session_coupon;
		}
		$session_coupons = $coupons;
		$this->session->set_userdata('trip/checkout/coupons', $session_coupons);
		$this->data['coupons'] = $session_coupons;
	}
	$this->addMessage('Cuponul a fost eliminat', 'success');
    $this->output();
  }
  public function validate_coupon() {
    $coupon_code = trim($this->input->post('coupon_code'));
    $coupon_type = trim($this->input->post('coupon_type'));
    $this->load->library('form_validation');
    $this->form_validation->set_rules('coupon_code', 'Cod cupon', 'trim|required',array(
      'required' => 'Introduceti codul cuponului',
    ));
    $this->form_validation->set_rules('coupon_type', 'Cod cupon', 'trim|required|in_list[hotel,package,flight,citybreak,paralela45_strainatate,paralela45_circuit]',array(
      'required' => 'Introduceti codul cuponului',
      'in_list' => 'Cupon indisponibil pentru aceasta oferta',
    ));
    
    if(strlen($coupon_code)){
      $_POST['coupon'] = null;
      $this->load->model('TripCoupon_model');
      $coupon = $this->TripCoupon_model->getValidCoupon($coupon_code, $coupon_type);
      if($coupon){
		$coupon_valid = true;
		if($coupon->fsli){
			$phone = trim($this->input->post('coupon_phone'));
			if('' !== $phone){
				$phone = preg_replace('/[^0-9]/','', $phone);
				if(!$this->check_fsli($phone)){
					$_POST['coupon_phone'] = '';
					if(strlen($phone) != 10){
						$this->form_validation->set_rules('coupon_phone', 'Nr. Telefon', 'trim|required',array(
						  'required' => 'Numarul de telefon nu a fost gasit in baza de date. Numarul de telefon trebuie sa fie de 10 cifre 07xxxxyyyy',
						));
					} else {
						$this->form_validation->set_rules('coupon_phone', 'Nr. Telefon', 'trim|required',array(
						  'required' => 'Numarul de telefon nu a fost gasit in baza de date.',
						));
					}
					$coupon_valid = false;
				}
			} else {
				$this->form_validation->set_rules('coupon_phone', 'Nr. Telefon', 'trim|required',array(
				  'required' => 'Pentru acest cod este necesar sa introduceti numarul de telefon de 10 cifre in campul <b>Telefon</b> de mai sus.',
				));
				$coupon_valid = false;
			}
		}
		if($coupon_valid){
			$_POST['coupon'] = 1;
			$session_coupons =  $this->session->userdata('trip/checkout/coupons');
			if(!$session_coupons){
				$session_coupons = array();
			}
			$session_coupon_codes = array();
			$coupons = array();
			foreach($session_coupons as $session_coupon){
				if(!isset($session_coupon['code'])){
					continue;
				}
				$session_coupon_codes[] = $session_coupon['code'];
				$coupons[] = $session_coupon;
			}
			if(!in_array($coupon->code, $session_coupon_codes)){
				$coupons[] = array(
					'id' => $coupon->id,
					'code' => $coupon->code,
					'discount' => $coupon->percentage,
					'discount_type' => $coupon->discount_type,
					'amount_ron' => $coupon->fixed_ron,
					'amount_eur' => $coupon->fixed_eur,
				);
			}
			$session_coupons = $this->TripCoupon_model->orderCouponsByAppliance($coupons);
			
			$this->session->set_userdata('trip/checkout/coupons', $session_coupons);
			$this->data['coupons'] = $session_coupons;
			
			/* BEGIN - TODO - remove */
			$this->data['coupon_code'] = $coupon->code;
			$this->data['coupon_discount'] = $coupon->percentage;
			$this->data['coupon_discount_type'] = $coupon->discount_type;
			$this->data['coupon_amount_ron'] = $coupon->fixed_ron;
			$this->data['coupon_amount_eur'] = $coupon->fixed_eur;
			$this->session->set_userdata('trip/checkout/coupon_code', $coupon->code);
			$this->session->set_userdata('trip/checkout/coupon_discount_type', $coupon->discount_type);
			$this->session->set_userdata('trip/checkout/coupon_discount', $coupon->percentage);
			$this->session->set_userdata('trip/checkout/coupon_amount_ron', $coupon->fixed_ron);
			$this->session->set_userdata('trip/checkout/coupon_amount_eur', $coupon->fixed_eur);
			/* END - TODO - remove */
		}
      }
      $this->form_validation->set_rules('coupon', 'Cupon', 'trim|required',array(
        'required' => 'Cupon invalid',
      ));
    }
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $this->addMessage('Cuponul a fost validat', 'success');
    $this->output();
  }
  
  protected function gateway($order_id, $type, $trip_order, $coupon_discount = null, $gateway=null) {
	  if(!isset($gateway)){
		  $gateway = $this->input->post('gateway');
	  }
	  if(!$gateway){
		  $gateway = 'payu';
	  }
	  if($gateway == 'pay24'){
		  return true;
	  }
    $order_info = array(
      'ref' => $order_id,
      'date' => $trip_order->Date,
      'currency' => $trip_order->Currency,
      'discount' => $coupon_discount,
      'p_code' => array(),
      'p_qty' => array(),
      'p_price' => array(),
      'p_vat' => array(),
      'p_name' => array(),
      'p_info' => array(),
    );
    $client_info = array(
      'fname' => $trip_order->Owner->FirstName,
      'lname' => $trip_order->Owner->LastName,
      'email' => $trip_order->Owner->Email,
      'phone' => $trip_order->Owner->Phone,
      'countrycode' => $trip_order->Owner->Address->CountryISO,
    );
    if($trip_order->Owner->CompanyId){
      $client_info['company'] = $trip_order->Owner->Company->CompanyName;
      $client_info['cui'] = $trip_order->Owner->Company->VatNumber;
      $client_info['regcom'] = $trip_order->Owner->Company->TradeRegistryNumber;
    }
    
    $processor_info = array(
      'order' => &$order_info,
      'client' => &$client_info,
      'success_url' => site_url('trip/checkout/' . $gateway . '?order_id=' . $order_id),
    );
    
    $payment_products = array();
    foreach($trip_order->Services as $service){
      $order_info['p_code'][] = 'trip#' . $service->Id;
      $order_info['p_qty'][] = 1;
      $order_info['p_vat'][] = 0;
      $service_discount = isset($this->service_discounts[$service->Type]) ? $this->service_discounts[$service->Type] : 0;
      $service_amount = $service->Amount - $service_discount;
      $order_info['p_price'][] = $service_amount;
      
      if($service->Type == 'hotel'){
        $order_info['p_name'][] = 'Cazare la Hotel ' . html_entity_decode($service->Hotel->Name,ENT_QUOTES);
      } elseif($service->Type == 'flight'){
        $total_routes = count($service->Routes);
        if($service->FlightType == 0){
          $order_info['p_name'][] = 'Zbor ' . $service->Routes[0]->OriginCityName . ' - ' . $service->Routes[$total_routes-1]->DestinationCityName;
        } elseif($service->FlightType == 1){
          foreach($service->Routes as $route){
            if($route->RouteType == 1){
              break;
            }
          }
          $order_info['p_name'][] = 'Zbor dus-intors ' . $service->Routes[0]->OriginCityName . ' - ' . $route->OriginCityName;
        }
      } elseif($service->Type == 'package'){
        $order_info['p_name'][] = 'Vacanta';
      } else {
        $order_info['p_name'][] = $service->Type;
      }
      $order_info['p_info'][] = 'Ord.ID: ' . $order_id . ' TOrd.ID: ' . $service->OrderId . ' Conf.NO: ' . $service->ConfirmationNo . ' Res.ID: ' . $service->ReservationId;
    }
    $payment_gateway = trim($this->input->post('payment_gateway'));
    
    return modules :: run('Trip/checkout/' . ucfirst($payment_gateway). '/checkout', $type, $processor_info);
  }
  protected function gateway_paralela45($order_data) {
    $order_info = array(
      'ref' => $order_data['id'],
      'date' => $order_data['time_created'],
      'currency' => $order_data['currency'],
      'discount' => $order_data['coupon_percentage'],
      'p_code' => array(),
      'p_qty' => array(),
      'p_price' => array(),
      'p_vat' => array(),
      'p_name' => array(),
      'p_info' => array(),
    );
    $client_info = array(
      'fname' => $order_data['user_firstname'],
      'lname' => $order_data['user_lastname'],
      'email' => $order_data['user_email'],
      'phone' => preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', $order_data['user_phone']),
      'countrycode' => $order_data['user_country'],
    );
    if($order_data['user_invoice'] == 'pj'){
      $client_info['company'] = $order_data['user_company_name'];
      $client_info['cui'] = $order_data['user_cui'];
      $client_info['regcom'] = $order_data['user_regcom'];
    }

    $payment_gateway = trim($this->input->post('payment_gateway'));
    
    $processor_info = array(
      'order' => &$order_info,
      'client' => &$client_info,
      'success_url' => site_url('trip/checkout/' . $payment_gateway . '?order_id=' . $order_data['id']),
    );
    
    $payment_products = array();
    $order_info['p_code'][] = 'p45#' . $this->service_info['offer_id'];
    $order_info['p_qty'][] = 1;
    $order_info['p_vat'][] = 0;
    $order_info['p_price'][] = $this->service_info['price'];
    $product_info = $this->service_info['product_info'];
    $total_people = $this->service_info['total_adults'] + $this->service_info['total_children'];
    $order_info['p_name'][] = 'Vacanta Hotel ' . html_entity_decode($product_info->ProductName . ', ' . $product_info->CityName . ', ' . $product_info->CountryName, ENT_QUOTES) . ' ' . $total_people . 'pers. ';
    
    $order_info['p_info'][] = 'Ord.ID: ' . $order_data['id'] . ' Off.ID: ' . $this->service_info['offer_id'] . '/' . $this->service_info['package_id'] . '/' . $this->service_info['package_variant_id'] . '/' . $this->service_info['checkin'] . '/' . $this->service_info['checkout'];
    
    return modules :: run('Trip/checkout/' . ucfirst($payment_gateway). '/checkout', 'paralela45_' . $order_data['type'], $processor_info);
  }
  protected function gateway_travelfuse($order_data) {
    $order_info = array(
      'ref' => $order_data['id'],
      'date' => $order_data['time_created'],
      'currency' => $order_data['currency'],
      'discount' => $order_data['coupon_percentage'],
      'p_code' => array(),
      'p_qty' => array(),
      'p_price' => array(),
      'p_vat' => array(),
      'p_name' => array(),
      'p_info' => array(),
    );
    $client_info = array(
      'fname' => $order_data['user_firstname'],
      'lname' => $order_data['user_lastname'],
      'email' => $order_data['user_email'],
      'phone' => preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', $order_data['user_phone']),
      'countrycode' => $order_data['user_country'],
    );
    if($order_data['user_invoice'] == 'pj'){
      $client_info['company'] = $order_data['user_company_name'];
      $client_info['cui'] = $order_data['user_cui'];
      $client_info['regcom'] = $order_data['user_regcom'];
    }

    $payment_gateway = trim($this->input->post('payment_gateway'));
    
    $processor_info = array(
      'order' => &$order_info,
      'client' => &$client_info,
      'success_url' => site_url('trip/checkout/' . $payment_gateway . '?order_id=' . $order_data['id']),
    );
    
    $payment_products = array();
    $order_info['p_code'][] = 'tf#' . $this->service_info['offer_id'];
    $order_info['p_qty'][] = 1;
    $order_info['p_vat'][] = 0;
    $order_info['p_price'][] = $this->service_info['price'];
    $SearchData = $this->service_info['SearchData'];
    $product_info = $this->service_info['result'];
    $total_people = $SearchData['Adults'][0] + count($SearchData['Children'][0] ?? []);
	
	switch($this->service_info['type']){
		case 'circuit':
			$order_info['p_name'][] = 'Circuit ' . html_entity_decode($product_info['Title'] . ', ' . ($product_info['Location']['City']['Name'] ?? '') . ', ' .  ($product_info['Location']['Country']['Name'] ?? ''), ENT_QUOTES) . ' ' . $total_people . 'pers. ';
			$order_info['p_info'][] = 'Ord.ID: ' . $order_data['id'] . ' Off.ID: ' . $product_info['Id'] . '/' . $SearchData['CheckIn'];
		break;
		case 'charter':
			$n = [];
			$n[] = $product_info['Name'];
			$n[] = $product_info['Address']['City']['Name'] ?? '';
			$n[] = $product_info['Address']['City']['County']['Name'] ?? '';
			$n[] = $product_info['Address']['City']['Country']['Name'] ?? '';
			$n[] = $product_info['Address']['Destination']['Name'] ?? '';
			$n = array_filter($n);
			
			$order_info['p_name'][] = 'Charter ' . html_entity_decode(implode(', ', $n), ENT_QUOTES) . ' ' . $total_people . 'pers. ';
			$order_info['p_info'][] = 'Ord.ID: ' . $order_data['id'] . ' Off.ID: ' . $product_info['Id'] . '/' . $SearchData['CheckIn'];
		break;
	}
    
    
    return modules :: run('Trip/checkout/' . ucfirst($payment_gateway). '/checkout', 'travelfuse_' . $order_data['type'], $processor_info);
  }
  protected function service($type, $run = true){
    $this->service_discount = 0;
    $this->service_discounts = array();
    $this->load->model('TripOrder_model');
    $this->makeResponseGlobal();
    if(method_exists($this,'service_' . $type)){
      $services = $this->{'service_' . $type}($run);
    } else {
      $this->addError('Metoda nu a fost implementata.');
      return false;
    }
    if(!$services && $run !== 'backend'){
		$this->addError('No services.');
      return false;
    }
    if(!$run){
      return true;
    }
	if('backend' === $run){
		return $services;
	}
	
	// if(!FLocker::acquire_nb('service_' . $this->session->session_id)){
		// $this->addError('O cerere este deja in procesare.');
		// return false;
	// }

	$session_coupons = $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), $type);
	if(!$session_coupons){
		$session_coupons = array();
	}
	/* 
    $coupon_code = $this->session->userdata('trip/checkout/coupon_code');
	
    $this->coupon_code = $coupon_code;
    $this->session->set_userdata('trip/checkout/coupon_code', null);
    $this->session->set_userdata('trip/checkout/coupon_discount', null);
	$this->session->set_userdata('trip/checkout/coupon_discount_type', null);
	$this->session->set_userdata('trip/checkout/coupon_discount', null);
	$this->session->set_userdata('trip/checkout/coupon_amount_ron', null);
	$this->session->set_userdata('trip/checkout/coupon_amount_eur', null); */
	
    $_POST['contact_title'] = $this->adult_title;
    $_POST['contact_birthdate'] = $this->adult_birthday;
    $user_id = $this->getOrCreateUser();
    $data = array();
    $data['created_by'] = $user_id;
    $data['user_id'] = $user_id;
    $data['type'] = $type;
    $data['user_invoice'] = trim($this->input->post('invoice'));
    $data['user_title'] = $this->adult_title;
    $data['user_birth_date'] = $this->adult_birthday;
    $data['user_gender'] = $data['user_title'] == 'mr' ? 'm' : 'f';
    $data['user_country'] = trim($this->input->post('contact_country'));
    $data['user_city'] = trim($this->input->post('contact_city'));
    $data['user_phone'] = trim($this->input->post('contact_phone'));
    $data['user_phone_prefix'] = $this->input->post('contact_phone_prefix');
    $data['user_email'] = trim($this->input->post('contact_email'));
    $data['user_firstname'] = trim($this->input->post('contact_firstname'));
    $data['user_lastname'] = trim($this->input->post('contact_lastname'));
    $data['user_street'] = trim($this->input->post('contact_street'));
    $data['user_street_no'] = trim($this->input->post('contact_street_no'));
    $data['user_bank'] = trim($this->input->post('contact_bank'));
    $data['user_iban'] = trim($this->input->post('contact_iban'));
    $data['user_cui'] = trim($this->input->post('contact_cui'));
    $data['user_company_name'] = trim($this->input->post('contact_company_name'));
    $data['user_regcom'] = trim($this->input->post('contact_regcom'));
    $data['user_postal_code'] = trim($this->input->post('contact_postal_code'));
    $data['user_address'] = trim($this->input->post('contact_address'));
    $data['payment_method'] = trim($this->input->post('payment_method'));
    $data['payment_gateway'] = $data['payment_method'] == 'online' ? trim($this->input->post('payment_gateway')) : null;
    $data['time_created'] = date('Y-m-d H:i:s');
    $provider = 'trip';
    if(strpos($type,'paralela45_') === 0){
      $provider = 'paralela45';
    }
    if(strpos($type,'travelfuse_') === 0){
      $provider = 'travelfuse';
    }
    $data['provider'] = $provider;
    if($provider=='travelfuse'){
      $services = array();
      $services[]=$this->service_info;
      $data['provider'] = 'travelfuse';
      $data['services'] = serialize($services);
      $total = $this->service_info['price'];
      $currency_code = $this->service_info['currency_code'];
    } elseif($provider=='paralela45'){
      $services = array();
      $services[]=$this->service_info;
      $data['provider'] = 'paralela45';
      $data['services'] = serialize($services);
      $total = $this->service_info['price'];
      $currency_code = $this->service_info['currency_code'];
    } elseif($provider=='trip'){
      $data['services_' . $type] = 1;
      if($type == 'citybreak'){
        $data['services_hotel'] = 1;
        $data['services_flight'] = 1;
      }
      $data['calls'] = json_encode($this->Trip_model->get_api()->calls);
      
      $order_id = $this->TripOrder_model->saveOrder($data);
      
      if(!$order_id){
        $this->addError('Nu s-a putut crea comanda');
        return false;
      }
	  
	  $_GET['order_id'] = $order_id;
	  
      $this->load->model('TripOrder_model');
      $trip_order = $this->TripOrder_model->createTripOrderFull($data,$services);
      if(!$trip_order){
		  // echo '<pre>';
		  // print_r($this->Trip_model->get_api()->calls);
		  // die;
		  
        $message = $this->getTripError('TripError: Nu s-a putut crea comanda');
        $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
        $this->addError($message);
        return false;
      }
      $total = $trip_order->Amount;
      $currency_code = $trip_order->Currency;
    }
    // $coupon_discount = null;
    $full_coupon_amount = 0;
    $undiscounted_amount = $total;
    $total -= $this->service_discount;
	$this->load->model('TripCoupon_model');
	$this->load->model('TripOrderCoupon_model');
	
	$coupons = array();
	if($session_coupons){
		foreach($session_coupons as $k=>$coupon){
			$coupon_code = $coupon['code'];
			$coupon_amount = 0;
			if($total > 0){
				if($coupon['discount_type'] == 'P'){
					$coupon_amount = ($total * $coupon['discount'])/100;
				} else {
					if($currency_code == 'RON'){
						$coupon_amount = $coupon['amount_ron'];
					} elseif($currency_code == 'EUR'){
						$coupon_amount = $coupon['amount_eur'];
					}
				}
				$coupon_amount = max(min($coupon_amount, $total),0);
				$total = $total - $coupon_amount;
				$full_coupon_amount += $coupon_amount;
			}
			$coupon['amount'] = $coupon_amount;
			$coupon['subtotal'] = $total;
			$coupons[] = $coupon;
		}
	}
	
  if($data['payment_method'] == 'free'){
	if($total > 0.00001){
		$message = $this->getTripError('TripError: Metoda de plata invalida.');
		$this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
		$this->addError($message);
		return false;
	}
  } elseif($total <= 0.00001 && $data['payment_gateway'] != 'pay24'){
	$data['payment_method'] = 'free';
  }
   /*   else {
      $coupon_code = null;
    } */
    if($provider == 'travelfuse'){
      $data['type'] = substr($type,11);
      $data['amount'] = $total;
      $data['total'] = $undiscounted_amount;
	  
      $data['currency'] = $currency_code;
      // $data['coupon_code'] = $coupon_code;
      $data['coupon_percentage'] = $undiscounted_amount > 0 ? $full_coupon_amount/$undiscounted_amount * 100 : 100;
      $data['coupon_amount'] = $full_coupon_amount;
      $data['service_discount'] = $this->service_discount;
      $data['service_discounts'] = json_encode($this->service_discounts);
      $data['message'] = 'Creat comanda Travelfuse intern';
      if($data['payment_method'] !== 'online'){
        $data['status'] = 1;
      }
      $order_id = $this->TripOrder_model->saveOrder($data);
	  foreach($coupons as $coupon){
		$this->TripOrderCoupon_model->saveOrderCoupon(array(
			'order_id' => $order_id,
			'coupon_id' => $coupon['id'],
			'coupon_code' => $coupon['code'],
			'coupon_discount_type' => $coupon['discount_type'],
			'coupon_percentage' => $coupon['discount'],
			'coupon_fixed_ron' => $coupon['amount_ron'],
			'coupon_fixed_eur' => $coupon['amount_eur'],
			'coupon_amount' => $coupon['amount'],
			'order_subtotal' => $coupon['subtotal'],
			'coupon_currency' => $currency_code,
			'time_created' => date('Y-m-d H:i:s'),
		));
	  }
      $data['id'] = $order_id;
	  $_GET['order_id'] = $order_id;
      if($data['payment_method'] === 'online'){
        $response = $this->gateway_travelfuse($data);
        if(false === $response){
          $message = $this->message;
          $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message));
        }
        Modules :: run ('Mailer/checkout_nefinalizat', array('order_id'=>$order_id));
        return $response;
      } else {
		foreach($coupons as $coupon){
			$this->TripCoupon_model->useCoupon($coupon['code']);
		}
        Modules :: run ('Mailer/checkout_auto', array('order_id'=>$order_id));
      }
    } elseif($provider == 'paralela45'){
      $data['type'] = substr($type,11);
      $data['amount'] = $total;
      $data['total'] = $undiscounted_amount;
	  
      $data['currency'] = $currency_code;
      // $data['coupon_code'] = $coupon_code;
      $data['coupon_percentage'] = $undiscounted_amount > 0 ? $full_coupon_amount/$undiscounted_amount * 100 : 100;
      $data['coupon_amount'] = $full_coupon_amount;
      $data['service_discount'] = $this->service_discount;
      $data['service_discounts'] = json_encode($this->service_discounts);
      $data['message'] = 'Creat comanda Paralela45 intern';
      if($data['payment_method'] !== 'online'){
        $data['status'] = 1;
      }
      $order_id = $this->TripOrder_model->saveOrder($data);
	  foreach($coupons as $coupon){
		$this->TripOrderCoupon_model->saveOrderCoupon(array(
			'order_id' => $order_id,
			'coupon_id' => $coupon['id'],
			'coupon_code' => $coupon['code'],
			'coupon_discount_type' => $coupon['discount_type'],
			'coupon_percentage' => $coupon['discount'],
			'coupon_fixed_ron' => $coupon['amount_ron'],
			'coupon_fixed_eur' => $coupon['amount_eur'],
			'coupon_amount' => $coupon['amount'],
			'order_subtotal' => $coupon['subtotal'],
			'coupon_currency' => $currency_code,
			'time_created' => date('Y-m-d H:i:s'),
		));
	  }
      $data['id'] = $order_id;
	  $_GET['order_id'] = $order_id;
      if($data['payment_method'] === 'online'){
        $response = $this->gateway_paralela45($data);
        if(false === $response){
          $message = $this->message;
          $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message));
        }
        Modules :: run ('Mailer/checkout_nefinalizat', array('order_id'=>$order_id));
        return $response;
      } else {
		foreach($coupons as $coupon){
			$this->TripCoupon_model->useCoupon($coupon['code']);
		}
        Modules :: run ('Mailer/checkout_auto', array('order_id'=>$order_id));
      }
    } elseif($provider == 'trip'){
      $this->TripOrder_model->saveOrder(array(
        'id' => $order_id,
        'amount' => $total,
        'total' => $undiscounted_amount,
        'currency' => $currency_code,
        // 'coupon_code' => $coupon_code,
        'coupon_percentage' => $undiscounted_amount > 0 ? $full_coupon_amount/$undiscounted_amount * 100 : 100,
        'coupon_amount' => $full_coupon_amount,
        'service_discount' => $this->service_discount,
        'service_discounts' => json_encode($this->service_discounts),
        'message' => 'Creat comanda TRIP',
        'time_created' => $trip_order->Date,
        'trip_order_id' => $trip_order->Id, 
        'calls'=>json_encode($this->Trip_model->get_api()->calls),
      ));
	  foreach($coupons as $coupon){
		$this->TripOrderCoupon_model->saveOrderCoupon(array(
			'order_id' => $order_id,
			'coupon_id' => $coupon['id'],
			'coupon_code' => $coupon['code'],
			'coupon_discount_type' => $coupon['discount_type'],
			'coupon_percentage' => $coupon['discount'],
			'coupon_fixed_ron' => $coupon['amount_ron'],
			'coupon_fixed_eur' => $coupon['amount_eur'],
			'coupon_amount' => $coupon['amount'],
			'order_subtotal' => $coupon['subtotal'],
			'coupon_currency' => $currency_code,
			'time_created' => date('Y-m-d H:i:s'),
		));
	  }
      if(!$this->on_rq){
        $trip_order = $this->TripOrder_model->getTripPaymentMethods($trip_order->Id);
        if(!$trip_order){
          $message = $this->getTripError('Trip Error: Nu s-au putut prelua metodele de plata ale comenzii');
          $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
          $this->addError($message);
          return false;
        }
        $payment_methods = $trip_order->PaymentMethods;
        if(!$payment_methods){
          $message = 'Nu sunt disponibile metode de plata';
          $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
          $this->addError($message);
          return false;
        }
            
        if(in_array('credit',$payment_methods)){
          $payment_method = 'credit';
        } elseif(in_array('bank',$payment_methods)){
          $payment_method = 'bank';
        } else {
          $payment_method = $payment_methods[0];
        }
        $response = $this->TripOrder_model->setTripPaymentMethod($trip_order->Id, $payment_method);
        if(!$response){
          $message = $this->getTripError('Nu s-a putut stabili metoda de plata');
          $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
          $this->addError($message);
          return false;
        }
        if($data['payment_method'] === 'online'){
          if(!config_item('trip_no_booking')){
            // Book the flight
            $trip_order = $this->TripOrder_model->getTripOrder($trip_order->Id);
            if(!$trip_order){
              $message = $this->getTripError('Trip Error: Nu s-a putut prelua rezervarea');
              $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
              $this->addError($message);
              return false;
            }
            if($trip_order->Status == 2){
              $message = 'Rezervarea a esuat si a fost anulata: ';
              $service_errors = array();
              foreach($trip_order->Services as $service){
                if($service->ErrorStatus){
                  $service_errors[] = $service->ErrorMessage;
                }
              }
              $message .= implode('; ', $service_errors);
              $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
              $this->addError($message);
              return false;
            }
			$should_book = false;
			if('pay24' != $data['payment_gateway']){
				foreach($trip_order->Services as $service){
					  if(!empty($service->PaymentBefore)){
						  continue;
					  }
					  if(!empty($service->FareDetails) && !empty($service->FareDetails->IsAutoTicketable)){
						  continue;
					  }
					  $should_book = true;
				}
			}
			if($should_book){
				if(count($trip_order->Services) > 1){
					foreach($trip_order->Services as $service){
					  if(!empty($service->PaymentBefore)){
						  continue;
					  }
					  if(!empty($service->FareDetails) && !empty($service->FareDetails->IsAutoTicketable)){
						  continue;
					  }
					  $booking_response = $this->TripOrder_model->bookTripService($trip_order->Id, $service->Id);
					  if(!$booking_response){
						$message = $this->getTripError('Trip Error: Nu s-a putut rezerva serviciul');
						$this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
						$this->addError($message);
						return false;
					  }
					}
				} else {
				  $booking_response = $this->TripOrder_model->bookAllTripServices($trip_order->Id);
				  if(!$booking_response){
					$message = $this->getTripError('Trip Error: Nu s-a putut efectua rezervarea');
					$data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
					// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
					$this->TripOrder_model->saveOrder($data);
					$this->addError($message);
						return false;
				  }
				}
			}
          }
          $response = $this->gateway($order_id, $type, $trip_order, $full_coupon_amount, $data['payment_gateway']);
          if(false === $response){
            $message = $this->message;
            $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
          }
          Modules :: run ('Mailer/checkout_nefinalizat', array('order_id'=>$order_id));
          return $response;
        }
        /* 
        // $response = $this->TripOrder_model->setTripPaymentStatus($trip_order->Id, 1);
        // if(!$response){
          // $message = $this->getTripError('Nu a putut fi stabilit statusul platii');
          // $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
          // $this->addError($message);
          // return false;
        // }
		 */
        if(!config_item('trip_no_booking')){
			$should_book = false;
			foreach($trip_order->Services as $service){
				  if(!empty($service->PaymentBefore)){
					  continue;
				  }
				  if(!empty($service->FareDetails) && !empty($service->FareDetails->IsAutoTicketable)){
					  continue;
				  }
				  $should_book = true;
			}
			if($should_book){
				if(count($trip_order->Services) > 1){
					foreach($trip_order->Services as $service){
					  if(!empty($service->PaymentBefore)){
						  continue;
					  }
					  if(!empty($service->FareDetails) && !empty($service->FareDetails->IsAutoTicketable)){
						  continue;
					  }
					  $booking_response = $this->TripOrder_model->bookTripService($trip_order->Id, $service->Id);
					  if(!$booking_response){
						$message = $this->getTripError('Trip Error: Nu s-a putut rezerva serviciul');
						$this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
						$this->addError($message);
						return false;
					  }
					}
				} else {
				  $booking_response = $this->TripOrder_model->bookAllTripServices($trip_order->Id);
				  if(!$booking_response){
					$message = $this->getTripError('Trip Error: Nu s-a putut efectua rezervarea dupa plata');
					$data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
					// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
					$this->TripOrder_model->saveOrder($data);
					$this->addError($message);
						return false;
				  }
				}
			}
			/* 
          // $booking_response = $this->TripOrder_model->bookAllTripServices($trip_order->Id);
          // if(!$booking_response){
            // $message = $this->getTripError('Trip Error: Nu s-a putut finaliza rezervarea');
            // $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
            // $this->addError($message);
            // return false;
          // }
		   */
          $trip_order = $this->TripOrder_model->getTripOrder($trip_order->Id);
          if(!$trip_order){
            $message = $this->getTripError('Trip Error: Nu s-a putut prelua rezervarea dupa rezervare');
            $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
            $this->addError($message);
            return false;
          }
          if($trip_order->Status == 2){
            $message = 'Rezervarea a esuat dupa rezervare si a fost anulata: ';
            $service_errors = array();
            foreach($trip_order->Services as $service){
              if($service->ErrorStatus){
                $service_errors[] = $service->ErrorMessage;
              }
            }
            $message .= implode('; ', $service_errors);
            $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
            $this->addError($message);
            return false;
          }
        }
      }
	  foreach($coupons as $coupon){
		$this->TripCoupon_model->useCoupon($coupon['code']);
	  }
	  $_GET['order_id'] = $order_id;
      $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>1,'message'=> ($this->on_rq ? 'ON Request! ' : '') . 'Se asteapta confirmarea platii.', 'calls'=>json_encode($this->Trip_model->get_api()->calls)));
      Modules :: run ('Mailer/checkout_auto', array('order_id'=>$order_id));
    }
    return true;
  }
  private $service_discount = 0;
  private $service_discounts = array();
  private function addServiceRemarks(&$service){
	$payment_gateway = trim($this->input->post('payment_gateway'));
	$payment_method = trim($this->input->post('payment_method'));
	if('pay24' == $payment_gateway){
		$service['BackOfficeRemarks'][] = array(
			'Code' => 'website',
			'Delimiter' => '=',
			'Value' => '24PAY',
		);
		$service['BackOfficeRemarks'][] = array(
			'Code' => 'plata',
			'Delimiter' => '=',
			'Value' => $payment_method,
		);
	} else {
		$service['BackOfficeRemarks'][] = array(
			'Code' => 'website',
			'Delimiter' => '=',
			'Value' => 'ACCENTTRAVEL',
		);
		$service['BackOfficeRemarks'][] = array(
			'Code' => 'plata',
			'Delimiter' => '=',
			'Value' => $payment_method,
		);
	}
  }
  private function service_flight($run = true){
	  $_post = $_POST;
	  $is_flight2 = $this->input->post('is_flight2');
	  $fk_prefix = $is_flight2 ? 'f2_' : '';
    $this->load->model('Flights_model');
    $flight_code = trim($this->input->post('flight_code'));
    $itinerary_code = trim($this->input->post('itinerary_code'));
    $flight_details = $this->Flights_model->loadFlightDetails($flight_code, $itinerary_code);
	
    $auto_ticketable = false;
    if(isset($flight_details->FareDetails,$flight_details->FareDetails->IsAutoTicketable) && filter_var($flight_details->FareDetails->IsAutoTicketable,FILTER_VALIDATE_BOOLEAN)){
      $auto_ticketable = true;
    }
    /* if(!$auto_ticketable){
      $supplier = '';
      if($supplier == ''){
        $auto_ticketable = true;
      }
    } */

    
    if($auto_ticketable && $run != 'backend'){
      if($this->input->post('payment_method') !== 'online' && $this->input->post('payment_method') !== 'free'){
        $this->addError('Pentru aceasta oferta se poate plati doar online.');
        return false;
      }
    }
    
    if(!$flight_details){
      $this->addError('Cererea de booking a expirat, va rugam sa reactualizati pagina.');
      return false;
    }
    $start_date = $flight_details->Routes[0]->Segment[0]->Origin->Date;
    if(!$this->validatePaymentMethod($start_date)){
      return false;
    }
    $email = $this->input->post('contact_email');
    $phone = preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', '' . $this->input->post('contact_phone'));
    $service = array(
      'serviceType' => 'flight',
      'resultCode' => $flight_code,
      'itineraryCode' => $itinerary_code,
      'comments' => trim($this->input->post('comment')),
      'amount' => $flight_details->Price,
      'currency' => $flight_details->Currency,
      'type' => count($flight_details->Routes)-1,
      'passenger' => array(),
    );
	
	if($this->Trip_model->get_api()->getAccountId()){
        $service['accountId'] = $this->Trip_model->get_api()->getAccountId();
	}
	
	$this->addServiceRemarks($service);

    $upsellCode = $this->input->post('upsellCode');
    $paidSeats = $this->input->post('paidSeats');
    $paidSeats = $paidSeats && is_array($paidSeats) ? $paidSeats : array();
    $optionalServices = $this->input->post('optionalServices');
    $optionalServices = $optionalServices && is_array($optionalServices) ? $optionalServices : array();
    $preferredSeats = $this->input->post('preferredSeats');
    $preferredSeats = $preferredSeats && is_array($preferredSeats) ? $preferredSeats : array();
    
    
    $flight_passengers = array();
    foreach($flight_details->FareDetails->PaxFare as $item){
      $flight_passengers[$item->PTC] = $item->Count;
    }
    $passengers = $this->input->post('passenger');
    $total_specified_passengers = count($passengers['title']);
    
    $insurance_travel = $this->input->post('insurance_travel');
    $insurance_storno = $this->input->post('insurance_storno');
    if(isset($insurance_travel) || isset($insurance_storno)){
      $this->load->model('Options_model');
      $flights_settings = $this->Options_model->get('trip_flights_settings');
      if(isset($insurance_travel)){
        $travel_prices = isset($flights_settings['travel_prices']) ? (array)$flights_settings['travel_prices'] : array();
        $travel_price = $travel_prices[$insurance_travel];
        $service['comments'] .= ' + Asigurare Calatorie interval ' . $travel_price['interval'] . ' ' . $total_specified_passengers . ' x ' . $travel_price['price'] . ' ' . $this->currency_symbol . ' = ' . number_format($total_specified_passengers * $travel_price['price'],2,'.','') . ' ' . $this->currency_symbol;
      }
      if(isset($insurance_storno)){
        $storno_prices = isset($flights_settings['storno_prices']) ? (array)$flights_settings['storno_prices'] : array();
        $storno_price = $storno_prices[$insurance_storno];
        $service['comments'] .= ' + Asigurare Premium Plus interval ' . $storno_price['interval'] . ' ' . $total_specified_passengers . ' x ' . $storno_price['price'] . ' ' . $this->currency_symbol . ' = ' . number_format($total_specified_passengers * $storno_price['price'],2,'.','') . ' ' . $this->currency_symbol;
      }
    }
    $total_flight_passengers = array_sum($flight_passengers);

    if($total_flight_passengers == $total_specified_passengers){
      $flight_adt = isset($flight_passengers['ADT']) ? (int)$flight_passengers['ADT'] : 0;
      $flight_sen = isset($flight_passengers['SEN']) ? (int)$flight_passengers['SEN'] : 0;
      $total_adults = $flight_adt + $flight_sen;
      $total_infants_f = isset($flight_passengers['INF']) ? (int)$flight_passengers['INF'] : 0;
      $total_infants_s = isset($flight_passengers['INS']) ? (int)$flight_passengers['INS'] : 0;
      $total_infants = $total_infants_f + $total_infants_s;
      $total_children = isset($flight_passengers['CHD']) ? (int)$flight_passengers['CHD'] : 0;
      $total_yth = isset($flight_passengers['YTH']) ? (int)$flight_passengers['YTH'] : 0;
      
      $routes_count = count($flight_details->Routes);
      $last_route = $flight_details->Routes[$routes_count-1];
      $last_route_segments_count = count($last_route->Segment);
      $last_route_segment = $last_route->Segment[$last_route_segments_count-1];
      $reference_date = $last_route_segment->Origin->Date;
      
      $today_date = DateTime::createFromFormat('Y-m-d',$reference_date);
      $total_post_seniors = 0;
      $total_post_adults = 0;
      $total_post_yth = 0;
      $total_post_children = 0;
      $total_post_infants = 0;
      $pass = array();
      $remaining_adults = $total_adults;
      $this->adult_birthday = null;
      $this->adult_title = null;
      
      foreach($passengers['birth_date'] as $k=>$passenger_birth_date){
        $birth_date = DateTime::createFromFormat('d.m.Y', $passenger_birth_date);
        $passenger_title = $passengers['title'][$k];
        $passenger = array(
          'title' => $passenger_title,
          'firstName' => $passengers['firstname'][$k],
          'lastName' => $passengers['lastname'][$k],
          'birthDate' => $birth_date->format('Y-m-d'),
          'email' => !empty($passengers['email'][$k]) ? $passengers['email'][$k] :  $email,
          'phone' => preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', !empty($passengers['phone'][$k]) ? $passengers['phone'][$k] :  $phone),
        );
		if(empty($email) && !empty($passenger['email'])){
			$email = $passenger['email'];
		}
		if(empty($phone) && !empty($passenger['phone'])){
			$phone = $passenger['phone'];
		}
        $age = (int) $today_date->diff($birth_date)->y;
        if($age > 60){
          $total_post_seniors ++;
          if(!isset($pass['SEN'])){
            $pass['SEN'] = array();
          }
          $pass['SEN'][] = $passenger;
        }
        if($age >= 18){
          if(!$this->adult_birthday){
            $this->adult_birthday = $passenger['birthDate'];
            $this->adult_title = $passengers['title'][$k];
          }
          $total_post_adults ++;
          if($age <= 60){
            if(!isset($pass['ADT'])){
              $pass['ADT'] = array();
            }
            $pass['ADT'][] = $passenger;
          }
        } elseif($age<3) {
          $total_post_infants ++;
          if($remaining_adults){
            if(!isset($pass['INF'])){
              $pass['INF'] = array();
            }
            $pass['INF'][] = $passenger;
            $remaining_adults--;
          } else {
            if(!isset($pass['INS'])){
              $pass['INS'] = array();
            }
            $pass['INS'][] = $passenger;
          }
        } elseif(defined('PAY24') && PAY24 && $age > 12) {
			$total_post_yth ++;
			if(!isset($pass['YTH'])){
				$pass['YTH'] = array();
			}
			$pass['YTH'][] = $passenger;
        } else {
          $total_post_children ++;
          if(!isset($pass['CHD'])){
            $pass['CHD'] = array();
          }
          $pass['CHD'][] = $passenger;
        }
      }
      $service['passenger'] = $pass;
    }
    
    if($total_flight_passengers != $total_specified_passengers){
      $this->addMessage('Numarul de pasageri (' . $total_specified_passengers . ') difera de cel al rezervarii (' . $total_flight_passengers . ').', 'error');
      return false;
    } elseif($total_post_adults != $total_adults){
      $this->addMessage('Numarul de adulti calculat din datele de nastere ale calatorilor (' . $total_post_adults . ') difera de cel al rezervarii (' . $total_adults . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif($flight_sen && $flight_sen < $total_post_seniors){
      $this->addMessage('Numarul de seniori calculat din datele de nastere ale calatorilor (' . $total_post_seniors . ') difera de cel al rezervarii (' . $flight_sen . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif(defined('PAY24') && PAY24 && $total_post_yth != $total_yth){
		$this->addMessage('Numarul de tineri calculat din datele de nastere ale calatorilor (' . $total_post_yth . ') difera de cel al rezervarii (' . $total_yth . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
		return false;
    } elseif($total_post_children != $total_children){
      $this->addMessage('Numarul de copii calculat din datele de nastere ale calatorilor (' . $total_post_children . ') difera de cel al rezervarii (' . $total_children . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif($total_post_infants != $total_infants){
      $this->addMessage('Numarul de copii < 2 ani calculat din datele de nastere ale calatorilor (' . $total_post_infants . ') difera de cel al rezervarii (' . $total_infants . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    }

    if($optionalServices){
      foreach($optionalServices as $optionalService){
        if(!isset($optionalService['bookingCode'], $optionalService['selectedOptionCode']) || count($optionalService) != 2){
          $this->addMessage('optionalServices incorrect', 'error');
          return false;
        }
      }
      $service['optionalServices'] = $optionalServices;
    }
    if($preferredSeats){
      foreach($preferredSeats as $psPTC => $preferredSeatArr){
        if(!is_array($preferredSeatArr)){
          $this->addMessage('preferredSeat incoherent', 'error');
        }
        foreach($preferredSeatArr as $psCNT => $preferredSeat){
          if(!isset($preferredSeat['details']) || !is_array($preferredSeat['details'])){
            $this->addMessage('preferredSeat incorrect', 'error');
            return false;
          }
          if(!isset($service['passenger'][$psPTC], $service['passenger'][$psPTC][$psCNT])){
            $this->addMessage('preferredSeat passenger incorrect', 'error');
          }
          $service['passenger'][$psPTC][$psCNT]['details'] = $preferredSeat['details'];
        }
      }
    }
    if($paidSeats){
      $routeIndexes = array_combine(array_map(function($a){ return $a->Index; },$flight_details->Routes), $flight_details->Routes);
      foreach($paidSeats as $paidSeat){
        if(!isset($paidSeat['passengerIndex']
          , $paidSeat['segmentIndex']
          , $paidSeat['legIndex']
          , $paidSeat['seatColumn']
          , $paidSeat['seatNumber']
          , $paidSeat['amount']
          , $paidSeat['currency']) || count($paidSeat) != 7){
          $this->addMessage('paidSeats incorrect', 'error');
          return false;
        }
        if(!isset($passengers['birth_date'][$paidSeat['passengerIndex']])){
          $this->addMessage('paidSeats passengerIndex incorrect', 'error');
          return false;
        }
        if(empty($paidSeat['amount'])){
          $this->addMessage('paidSeats amount incorrect', 'error');
          return false;
        }
        
        if(!isset($routeIndexes[$paidSeat['legIndex']])){
          $this->addMessage('paidSeats legIndex incorrect', 'error');
          return false;
        }

        if(!isset($routeIndexes[$paidSeat['legIndex']]->Segment[$paidSeat['segmentIndex']])){
          $this->addMessage('paidSeats segmentIndex incorrect', 'error');
          return false;
        }

        if($paidSeat['currency'] != $flight_details->Currency){
          $this->addMessage('paidSeats segmentIndex incorrect', 'error');
          return false;
        }
        
      }
      $service['paidSeats'] = $paidSeats;
    }

    if($upsellCode){
      $service['upsellCode'] = $upsellCode;
    }

    $validated_flight = $this->Flights_model->validateFlight($flight_code, $service);
    if(!$validated_flight){
      $this->addMessage('The itinerary code is no longer available', 'error');
      return false;
    }
    $service['amount'] = $validated_flight->Price;
    $expected_flight_price = $this->input->post('expectedFlightPrice');
    // echo '<pre>';
	// var_dump($expected_flight_price);
    // print_r($service);
    // print_r($validated_flight);
    // print_r($validated_flight);
    // print_r($this->Trip_model->get_api()->calls);
    // die;
    if(number_format($validated_flight->Price,2,'.','') . $validated_flight->Currency != $expected_flight_price){
      $this->addMessage('The price has changed meantime ' . ($validated_flight->Price . ' - ' . $expected_flight_price) . '. Please refresh the page', 'error');
      return false;
    }
	$service_flight2 = null;
	$flight2 = $_POST['flight2'] ?? null;
	if($flight2){
		$post = $_POST;
		$_POST = array_diff_key($post, array_flip([
			'expectedFlightPrice',
			'itinerary_code',
			'flight_code',
			'upsellCode',
			'paidSeats',
			'optionalServices',
			'passenger',
			'preferredSeats',
		]));
		$_POST = array_merge($post['flight2'], $_POST);
		unset($_POST['flight2']);
		$_POST['is_flight2'] = 1;
		$service_flight2 = $this->service_flight($run);
		$p = $_POST;
		$_POST = $post;
		if(!$service_flight2){
			return false;
		}
	}
	
	$to_return = array(
      'flight' => array($service)
    );
	if($service_flight2){
		foreach($service_flight2 as $i => $j){
			foreach($j as $k => $l){
				$to_return[$i][] = $l;
			}
		}
	}
	if($is_flight2){
		return $to_return;
	}
    if(!$run){
      return true;
    }
	return $to_return;
  }
  private function service_citybreak($run = true){
    $service_hotel = $this->service_hotel($run);
    if(!$service_hotel){
      return false;
    }
    $service_flight = $this->service_flight($run);
    if(!$service_flight){
      return false;
    }
    if(!$run){
      return true;
    }
    return array_merge($service_hotel,$service_flight);
  }
  private function service_package($run = true){
    $package_id = (int)$this->input->post('package_id');
    $code = '' . $this->input->post('code');
    $entry_id = (int)$this->input->post('entry_id');
    $rate_group_id = (int)$this->input->post('rate_group_id');
    $occupations = (array)$this->input->post('occupations');
    $extra_services = (array)$this->input->post('extra-services');
    $entry_details = $this->Packages_model->loadPackageEntryDetails($package_id,$code, $entry_id, $rate_group_id);
    if(!$entry_details){
      $this->outputTripError('Vacanta nu a putut fi validata');
    }
    
    $package_availability = $this->Packages_model->checkPackageAvailability($package_id,$code, $entry_id, $rate_group_id, $occupations, $extra_services);
    if(!$package_availability){
      $this->outputTripError('Vacanta nu a putut fi validata');
    }
    $this->data['entry_details'] = $entry_details;
    $this->data['package_availability'] = $package_availability;
    $this->data['post'] = $this->input->post();
    $trip_payment_method = '';
    if($package_availability->PaymentMethods){
      $trip_payment_methods = array_values($package_availability->PaymentMethods);
      $trip_payment_method = array_shift($trip_payment_methods);
    }
    $this->adult_birthday = null;
    $this->adult_title = null;
    $passengers = $this->input->post('passenger');
    $total_specified_passengers = count($passengers['title']);
    $total_adults = 0;
    $total_children = 0;
    $children_ages = array();
    $on_rq = false;
    foreach($entry_details->Accommodation as $room_key => $packages){
      foreach($packages as $k=>$package){
        if($package->AvailabilityStatus == 'RQ'){
          $on_rq = true;
        }
        $total_adults += 1*$package->Adults;
        $total_children += 1*$package->Children;
        if($package->ChildrenAges){
          foreach($package->ChildrenAges as $j=>$age){
            if(!isset($children_ages[(int)$age])){
              $children_ages[(int)$age] = 0;
            }
            $children_ages[(int)$age] ++;
          }
        }
        break;
      }
    }
    $cancellation_policies = $entry_details->CancelationPolicies;
    $after_cancellation = false;
    if($cancellation_policies){ 
      $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
      foreach($cancellation_policies as $cancellation_policy){
        if(!isset($cancellation_policy->Charge, $cancellation_policy->Charge->Amount)){
          continue;
        }
        $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:sP", $cancellation_policy->StartDate);
        if($min_cancellation_date_for_block > $cancellation_date){
          $after_cancellation = true;
          break;
        }
      }
    }
    $start_date = $entry_details->StartDate;
    if(!$this->validatePaymentMethod($start_date, $on_rq, $after_cancellation)){
      return false;
    }
    
    $total_passengers = $total_adults + $total_children;
    
    $full_amount = isset($package_availability->full_price) ? $package_availability->full_price : $package_availability->Amount;
    $discounted_amount = $package_availability->Amount;
    $this->service_discount += $full_amount - $discounted_amount;
    $this->service_discounts['package'] = $full_amount - $discounted_amount;
    $service = array(
      'serviceType' => 'package',
      'packageId' => $package_id,
      'resultCode' => $code, // resultsCode ?
      'entryId' => $entry_id,
      'rateId' => $rate_group_id,
      'comments' => trim($this->input->post('comment')),
      'remarks' => '',
      'amount' => $full_amount,
      'currency' => $package_availability->Currency,
      'occupations' => &$occupations,
      'extra-services' => &$extra_services,
    );
	if($this->Trip_model->get_api()->getAccountId()){
        $service['accountId'] = $this->Trip_model->get_api()->getAccountId();
	}
	$this->addServiceRemarks($service);
    foreach($extra_services as $k=>$extra_service){
      $extra_services[$k]['occupants'] = array();
    }
    if($total_passengers == $total_specified_passengers){
      $expected_children_ages = $children_ages;
      $today_date = DateTime::createFromFormat('Y-m-d',$entry_details->EndDate);
      $total_post_adults = 0;
      $total_post_children = 0;
      $incorrect_children_ages = false;
      $post_children_ages = array();
      $email = $this->input->post('contact_email');
      $phone = preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', '' . $this->input->post('contact_phone'));
      
      $adult_passengers = array();
      $child_passengers = array();
      foreach($passengers['birth_date'] as $k => $passenger_birth_date){
        $birth_date = DateTime::createFromFormat('d.m.Y', $passenger_birth_date);
        $passenger = array(
          'idx' => $k+1,
          'title' => $passengers['title'][$k],
          'firstName' => $passengers['firstname'][$k],
          'lastName' => $passengers['lastname'][$k],
          'email' => !empty($passengers['email'][$k]) ? $passengers['email'][$k] :  $email,
          'phone' => preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', !empty($passengers['phone'][$k]) ? $passengers['phone'][$k] :  $phone),
          'birthDate' => $birth_date->format('Y-m-d'),
        );
		if(empty($email) && !empty($passenger['email'])){
			$email = $passenger['email'];
		}
		if(empty($phone) && !empty($passenger['phone'])){
			$phone = $passenger['phone'];
		}
        $age = (int) $today_date->diff($birth_date)->y;
        if($age >= 18){
          $passenger['type'] = 'a';
          if(is_null($this->adult_birthday)){
            $this->adult_birthday = $birth_date->format('Y-m-d');
            $this->adult_title = $passenger['title'];
          }
          $adult_passengers[] = $passenger;
          $total_post_adults ++;
        } else {
          $passenger['type'] = 'c';
          if(!isset($post_children_ages[(int)$age])){
            $post_children_ages[(int)$age] = 0;
          }
          if(!isset($child_passengers[(int)$age])){
            $child_passengers[(int)$age] = array();
          }
          $child_passengers[(int)$age][] = $passenger;
          $post_children_ages[(int)$age] ++;
          $total_post_children ++;
          if(!isset($children_ages[$age]) || !$children_ages[$age]){
            $incorrect_children_ages = true;
          } else {
            $children_ages[$age] --;
          }
        }
      }
      foreach($entry_details->Accommodation as $room_key => $packages){
        $occupations[$room_key]['occupants'] = array();
        $occupants_extra_services = array();
        if(isset($occupations[$room_key]['extra-services'])){
          if(is_array($occupations[$room_key]['extra-services'])){
            $occupants_extra_services = $occupations[$room_key]['extra-services'];
          }
          unset($occupations[$room_key]['extra-services']);
        }
        foreach($packages as $k=>$package){
          for($i=1; $i<=$package->Adults;$i++){
            if($adult_passengers){
              $adult_passenger = array_shift($adult_passengers);
              $occupations[$room_key]['occupants'][] = $adult_passenger;
              foreach($occupants_extra_services as $service_key => $occupants_extra_service){
                if(isset($occupants_extra_service['a']) && $occupants_extra_service['a']){
                  $extra_services[$service_key]['occupants'][] = $adult_passenger['idx'];
                }
              }
            }
          }
          if($package->Children){
            foreach($package->ChildrenAges as $j=>$age){
              if(isset($child_passengers[(int)$age]) && $child_passengers[(int)$age]){
                $child_passenger = array_shift($child_passengers[(int)$age]);
                $occupations[$room_key]['occupants'][] = $child_passenger;
                foreach($occupants_extra_services as $service_key => $occupants_extra_service){
                  if(isset($occupants_extra_service['c']) && $occupants_extra_service['c']){
                    if(in_array($age,$occupants_extra_service['c'])){
                      $extra_services[$service_key]['occupants'][] = $child_passenger['idx'];
                    }
                  }
                }
              }
            }
          }
          break;
        }
      }
    }
    foreach($extra_services as $service_key => $extra_service){
      if(empty($extra_service['occupants'])){
        unset($extra_services[$service_key]);
      }
    }
    if($total_passengers != $total_specified_passengers){
      $this->addMessage('Numarul de pasageri (' . $total_specified_passengers . ') difera de cel al rezervarii (' . $total_passengers . ').', 'error');
      return false;
    } elseif($total_post_adults != $total_adults){
      $this->addMessage('Numarul de adulti calculat din datele de nastere ale calatorilor (' . $total_post_adults . ') difera de cel al rezervarii (' . $total_adults . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif($total_post_children != $total_children){
      $this->addMessage('Numarul de copii calculat din datele de nastere ale calatorilor (' . $total_post_children . ') difera de cel al rezervarii (' . $total_children . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif($incorrect_children_ages){
      $total_children_ages_str = '';
      foreach($expected_children_ages as $expected_age => $children_count){
        if($total_children_ages_str){
          $total_children_ages_str .= ', ';
        }
        $total_children_ages_str .= $children_count . 'x' . (!$expected_age ? '<1 an' : $expected_age . ' ' . ($expected_age == 1 ? 'an' : 'ani'));
      }
      $total_post_children_ages_str = '';
      foreach($post_children_ages as $expected_age => $children_count){
        if($total_post_children_ages_str){
          $total_post_children_ages_str .= ', ';
        }
        $total_post_children_ages_str .= $children_count . 'x' . (!$expected_age ? '<1 an' : $expected_age . ' ' . ($expected_age == 1 ? 'an' : 'ani'));
      }
      $this->addMessage('Varstele copilor (' . $total_post_children_ages_str . ') difera de cel al rezervarii (' . $total_children_ages_str . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    }
    if(!$run){
      return true;
    }
    $this->on_rq = $on_rq;
    return array(
      'package' => array($service)
    );
  }
  private function validatePaymentMethod($start_date, $on_rq = false, $after_cancellation=false){
    $payment_method = trim($this->input->post('payment_method'));
	if($payment_method == 'backend'){
		return true;
	}
    $today = new DateTime();
    $block_online = false;
    $block_payments = false;
    
    $because_of_cancellation_policy = false;
    if($after_cancellation && $payment_method != 'free'){
      $block_payments = true;
      $because_of_cancellation_policy = true;
    }
    
    $because_weekend = false;
    // sambata && duminica
    if($today->format('N') >= 6 && $payment_method != 'free'){
      $block_payments = true;
      $because_weekend = true;
    }
    // ore nelucratoare
    $because_no_working_hours = false;
    // if((int)$today->format('H') < 6 || (int)$today->format('H') >= 18){
      // $block_payments = true;
      // $because_no_working_hours = true;
    // }
    $date_start_date = DateTime::createFromFormat('Y-m-d', $start_date);
    $days_till_start = $today->diff($date_start_date);
    $days_till_start_formatted = intval($days_till_start->format('%a'));
    $because_too_early = false;
    // checkin azi sau maine
    if($days_till_start_formatted < 2 && $payment_method != 'free'){
      $block_payments = true;
      $because_too_early = true;
    }
    $because_on_request = false;
    if($on_rq && $payment_method != 'free'){
      $block_online = true;
      $because_on_request = true;
    }
    $error = false;
    if(($payment_method == 'online') && $block_online){
      $error = true;
      if($because_on_request){ 
        $message = 'Nu se poate efectua plata online pentru cazare in camere cu disponibilitate: La cerere';
      }
    } elseif(($payment_method != 'online') && $block_payments){
      $error = true;
      if($because_of_cancellation_policy){
        $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
        $message = 'Deoarece data minima de anulare este inaintea datei ' . $min_cancellation_date_for_block->format('d.m.Y h:i:s A') . ' se poate plati doar online';
      } elseif($because_too_early){ 
        $message = 'Pentru rezervari cu data de checkin astazi sau maine se poate plati doar online.';
      } elseif($because_weekend){
        $message = 'Pentru rezervari efectuate in weekend se poate plati doar online.';
      } elseif($because_no_working_hours){ 
        $message = 'Pentru rezervari efectuate in intervalul orar 18:00 - 06:00 se poate plati doar online.';
      }
    }
    
    if($error){
      $this->addError($message);
      return false;
    }
    return true;
  }
  private function service_hotel($run = true){
    $code = trim($this->input->post('code'));
    $hotel_id = (int)$this->input->post('hotel_id');
    $package_code = trim($this->input->post('package_code'));
    $rooms_combinations = trim($this->input->post('rooms_combinations'));
    $this->load->model('Hotels_model');
    $rooms_for_package = $this->Hotels_model->loadRoomPackageRooms($code, $hotel_id, $package_code, $rooms_combinations);
    if(!$rooms_for_package){
      $this->addError('Cererea de booking a expirat, va rugam sa reactualizati pagina.');
      return false;
    }

    $this->adult_birthday = null;
    $this->adult_title = null;
    $passengers = $this->input->post('passenger');
    $total_specified_passengers = count($passengers['title']);
    $total_adults = 0;
    $total_children = 0;
    $children_ages = array();
    $package_rooms = array();
    $on_rq = false;
    foreach($rooms_for_package->PackageRooms->PackageRoom as $ref_index => $package_room){
      $total_adults += $package_room->Occupancy->Adults;
      $total_children += $package_room->Occupancy->Children;
      foreach($package_room->Occupancy->ChildAge as $age){
        if(!isset($children_ages[(int)$age])){
          $children_ages[(int)$age] = 0;
        }
        $children_ages[(int)$age] ++;
      }
      foreach($package_room->RoomRefs->RoomRef as $room_ref){
        if(!$room_ref->Selected){
          continue;
        }
        if($room_ref->Status == 'RQ'){
          $on_rq = true;
        }
        $r_ref = clone $room_ref;
        $r_ref->pkg = $package_room;
        if(empty($r_ref->Price)){
          $r_ref->Price = (object)$rooms_for_package->Price;
        }
        $package_rooms[$package_room->PackageRoomCode] = $r_ref;
      }
    }
    $cancellation_policies = $rooms_for_package->CancellationPolicy->Policy;
    $after_cancellation = false;
    if($cancellation_policies){ 
      $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
      foreach($cancellation_policies as $cancellation_policy){
        if(!isset($cancellation_policy->Charge, $cancellation_policy->Charge->Amount)){
          continue;
        }
        $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:sP", $cancellation_policy->Limit);
        if($min_cancellation_date_for_block > $cancellation_date){
          $after_cancellation = true;
          break;
        }
      }
    }
    $start_date = $rooms_for_package->AccommodationPeriod->StartDate;
    if(!$this->validatePaymentMethod($start_date, $on_rq, $after_cancellation)){
      return false;
    }

    $service = array(
      'serviceType' => 'hotel',
      'hotelId' => $hotel_id,
      'resultCode' => $code,
      'packageCode' => $package_code,
      'comments' => trim($this->input->post('comment')),
      'amount' => $rooms_for_package->Price->Amount,
      'currency' => $rooms_for_package->Price->Currency,
      'checkin' => $rooms_for_package->AccommodationPeriod->StartDate,
      'checkout' => $rooms_for_package->AccommodationPeriod->EndDate,
      'adultsNr' => $total_adults,
      'childrenNr' => $total_children,
      'room' => array(),
    );
	if($this->Trip_model->get_api()->getAccountId()){
        $service['accountId'] = $this->Trip_model->get_api()->getAccountId();
	}
	$this->addServiceRemarks($service);
    $total_passengers = $total_adults + $total_children;
    if($total_passengers == $total_specified_passengers){
      $expected_children_ages = $children_ages;
      $today_date = DateTime::createFromFormat('Y-m-d',$rooms_for_package->AccommodationPeriod->EndDate);
      $total_post_adults = 0;
      $total_post_children = 0;
      $incorrect_children_ages = false;
      $post_children_ages = array();
      $email = $this->input->post('contact_email');
      $phone = preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', '' . $this->input->post('contact_phone'));
	  
	  $phone_country = $this->input->post('contact_phone_prefix');
	  $phone_prefix = '+40';
	  $this->load->model('Country_model');
		if(isset($phone_country) && strlen($phone_country)){
			$phone_prefix_country = $this->Country_model->getCountries(array(
				'iso_2' => trim($phone_country),
				'select' => 'phone_prefix',
				'return_row' => true,
			));
			if($phone_prefix_country){
				$phone_prefix = '+' . $phone_prefix_country->phone_prefix . ' ';
			}
		}
      
      $adult_passengers = array();
      $child_passengers = array();
      foreach($passengers['title'] as $k => $title){
		  $birth_date = !empty($passengers['birth_date'][$k]) ? $passengers['birth_date'][$k] :  null;
		  if($birth_date){
			$birth_date = DateTime::createFromFormat('d.m.Y', $birth_date);
		  }
        $passenger = array(
          'title' => $passengers['title'][$k],
          'firstname' => $passengers['firstname'][$k],
          'lastname' => $passengers['lastname'][$k],
          'birthdate' => $birth_date ? $birth_date->format('Y-m-d') : '',
          'email' => !empty($passengers['email'][$k]) ? $passengers['email'][$k] :  $email,
          'phone' => preg_replace('/[^0-9]/', '', preg_replace('/.*?\s+/', '', preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', !empty($passengers['phone'][$k]) ? $passengers['phone'][$k] :  $phone))),
          'phonePrefix' => $phone_prefix,
        );
		
		if(empty($email) && !empty($passenger['email'])){
			$email = $passenger['email'];
		}
		if(empty($phone) && !empty($passenger['phone'])){
			$phone = $passenger['phone'];
		}
        $age = $birth_date ? (int) $today_date->diff($birth_date)->y : 18;
        if($age >= 18){
          if(is_null($this->adult_birthday) && $birth_date){
            $this->adult_birthday = $birth_date->format('Y-m-d');
            $this->adult_title = $passenger['title'];
          }
          $adult_passengers[] = $passenger;
          $total_post_adults ++;
        } else {
          if(!isset($post_children_ages[(int)$age])){
            $post_children_ages[(int)$age] = 0;
          }
          if(!isset($child_passengers[(int)$age])){
            $child_passengers[(int)$age] = array();
          }
          $child_passengers[(int)$age][] = $passenger;
          $post_children_ages[(int)$age] ++;
          $total_post_children ++;
          if(!isset($children_ages[$age]) || !$children_ages[$age]){
            $incorrect_children_ages = true;
          } else {
            $children_ages[$age] --;
          }
        }
      }
      
      foreach($package_rooms as $room_ref){
        $pkg = $room_ref->pkg;
        $room = array(
          'code' => $room_ref->RoomCode,
          'price' => $room_ref->Price->Amount,
          'currency' => $room_ref->Price->Currency,
          'name' => $room_ref->Name,
          'board' => $room_ref->Board,
          'info' => $room_ref->Info,
          'status' => $room_ref->Status,
          'packageCode' => $pkg->PackageRoomCode,
          'adults' => array(),
          'children' => array(),
        );
        for($i=1; $i<=$package_room->Occupancy->Adults;$i++){
          if($adult_passengers){
            $room['adults'][] = array_shift($adult_passengers);
          }
        }
        if($package_room->Occupancy->Children){
          foreach($package_room->Occupancy->ChildAge as $age){
            if(isset($child_passengers[(int)$age]) && $child_passengers[(int)$age]){
              $room['children'][] = array_shift($child_passengers[(int)$age]);
            }
          }
        }
        $service['room'][] = $room;
      }
    }
    if($total_passengers != $total_specified_passengers){
      $this->addMessage('Numarul de pasageri (' . $total_specified_passengers . ') difera de cel al rezervarii (' . $total_passengers . ').', 'error');
      return false;
    } elseif($total_post_adults != $total_adults){
      $this->addMessage('Numarul de adulti calculat din datele de nastere ale calatorilor (' . $total_post_adults . ') difera de cel al rezervarii (' . $total_adults . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif($total_post_children != $total_children){
      $this->addMessage('Numarul de copii calculat din datele de nastere ale calatorilor (' . $total_post_children . ') difera de cel al rezervarii (' . $total_children . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    } elseif($incorrect_children_ages){
      $total_children_ages_str = '';
      foreach($expected_children_ages as $expected_age => $children_count){
        if($total_children_ages_str){
          $total_children_ages_str .= ', ';
        }
        $total_children_ages_str .= $children_count . 'x' . (!$expected_age ? '<1 an' : $expected_age . ' ' . ($expected_age == 1 ? 'an' : 'ani'));
      }
      $total_post_children_ages_str = '';
      foreach($post_children_ages as $expected_age => $children_count){
        if($total_post_children_ages_str){
          $total_post_children_ages_str .= ', ';
        }
        $total_post_children_ages_str .= $children_count . 'x' . (!$expected_age ? '<1 an' : $expected_age . ' ' . ($expected_age == 1 ? 'an' : 'ani'));
      }
      $this->addMessage('Varstele copilor (' . $total_post_children_ages_str . ') difera de cel al rezervarii (' . $total_children_ages_str . '). Data de referinta: ' . $today_date->format('d.m.Y'), 'error');
      return false;
    }
    if(!$run){
      return true;
    }
    $this->on_rq = $on_rq;
    return array(
      'hotel' => array($service)
    );
  }
  private function getOrCreateUser(){
    $user_id = 0;
    if($this->user->id){
      $user_id = $this->user->id;
    } else {
      $create_account = $this->input->post('create_account');
      if($create_account){
        $data = array();
        $data['user_type'] = 'customer';
        $data['user_status'] = 1;
        $data['user_created_datetime'] = date("Y-m-d H:i:s");
        $data['user_email'] = trim($this->input->post('contact_email'));
        $data['user_username'] = $data['user_email'];
        $data['user_firstname'] = trim($this->input->post('contact_firstname'));
        $data['user_lastname'] = trim($this->input->post('contact_lastname'));
        $data['birth_date'] = trim($this->input->post('contact_birthdate'));
        $data['title'] = trim($this->input->post('contact_title'));
        $data['gender'] = $data['title'] == 'mr' ? 'm' : 'f';
        $data['country'] = trim($this->input->post('contact_country'));
        $data['city'] = trim($this->input->post('contact_city'));
        $data['invoice'] = $this->input->post('invoice');
        if($data['invoice'] == 'pj'){
          $data['pj_bank'] = trim($this->input->post('contact_bank'));
          $data['pj_cui'] = trim($this->input->post('contact_cui'));
          $data['pj_company_name'] = trim($this->input->post('contact_company_name'));
          $data['pj_regcom'] = trim($this->input->post('contact_regcom'));
          $data['pj_iban'] = trim($this->input->post('contact_iban'));
        }
        $data['phone'] = preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', trim($this->input->post('contact_phone')));
        $data['phone_prefix'] = $this->input->post('contact_phone_prefix');
        $data['pf_street'] = trim($this->input->post('contact_street'));
        $data['pf_street_no'] = trim($this->input->post('contact_street_no'));
        $data['tos'] = 1;
        
        $data['pf_phone_prefix'] = $data['country'];
        $data['pj_phone_prefix'] = $data['country'];
        $data['pf_email'] = $data['user_email'];
        $data['pj_email'] = $data['user_email'];
        $data['contact_lastname'] = $data['user_lastname'];
        $data['pf_lastname'] = $data['user_lastname'];
        $data['pj_lastname'] = $data['user_lastname'];
        $data['contact_firstname'] = $data['user_firstname'];
        $data['pf_firstname'] = $data['user_firstname'];
        $data['pj_firstname'] = $data['user_firstname'];
        $data['pf_country'] = $data['country'];
        $data['pj_country'] = $data['country'];
        $data['contact_phone'] = $data['phone'];
        $data['pf_phone'] = $data['phone'];
        $data['pj_phone'] = $data['phone'];
        
        $data['user_password'] = sha1($this->input->post('password'));
        $this->load->model('Account_model');
        $user_id = $this->Account_model->saveAccount($data);
        $this->session->set_userdata('logged_in', $user_id);
      }
    }
    return $user_id;
  }
  protected function validate($type = null){
	$this->makeResponseGlobal();
    $this->form_validation->set_message('validate_alpha_spaces', 'Campurile nume si prenume trebuie sa contina doar litere si spatii');
    if(isset($type) && method_exists($this,'validate_' . $type)){
      $valid = $this->{'validate_' . $type}();
      if(!$valid){
        return false;
      }
    }
    $payment_method = trim($this->input->post('payment_method'));
    $this->load->model('Options_model');
    $settings_payment_methods = $this->Options_model->get('payment_methods_status');
    if(!$settings_payment_methods){
      $settings_payment_methods = array();
    }
    $allowed_statuses = array(1,-2);
    if($this->user->can('backend-config-save')){
      $allowed_statuses[] = -1;
    }
    $allowed_payment_methods = array();
    foreach($settings_payment_methods as $setting_payment_method => $setting_payment_status){
      if(in_array((int)$setting_payment_status,$allowed_statuses)){
        $allowed_payment_methods[] = $setting_payment_method;
      }
    }
	$allowed_payment_methods[] = 'free';
	
    $this->data['allowed_payment_methods'] = $allowed_payment_methods;
    $this->form_validation->set_rules('payment_method', 'Metoda de plata', 'trim|required' . ($allowed_payment_methods ? '|in_list[' . implode(',', $allowed_payment_methods) . ']' : ''),array(
      'in_list' => 'Alegere invalida',
    ));
    if($payment_method === 'online'){
      $payment_gateway = trim($this->input->post('payment_gateway'));
      $settings_payment_gateways = $this->Options_model->get('payment_gateways_status');
      if(!$settings_payment_gateways){
        $settings_payment_gateways = array();
      }
      $allowed_payment_gateways = array();
      foreach($settings_payment_gateways as $setting_payment_gateway => $setting_payment_status){
		if($setting_payment_gateway == 'pay24' && defined('PAY24') && PAY24 && $setting_payment_status){
			$setting_payment_status = 1;
		}
        if(in_array((int)$setting_payment_status,$allowed_statuses)){
          $allowed_payment_gateways[] = $setting_payment_gateway;
        }
      }
      $this->form_validation->set_rules('payment_gateway', 'Procesator plata online', 'trim|required' . ($allowed_payment_gateways ? '|in_list[' . implode(',', $allowed_payment_gateways) . ']' : ''),array(
        'in_list' => 'Informatii invalide',
      ));

      if(in_array($payment_gateway, $allowed_payment_gateways)){
        modules :: run('Trip/checkout/' . ucfirst($payment_gateway). '/validate', $type);
      }
    }
    
    $this->form_validation->set_rules('invoice', 'Facturare PF/PJ', 'trim|required|in_list[pf,pj]',array(
      'in_list' => 'Alegere invalida',
    ));
    
    $this->form_validation->set_rules('contact_lastname', 'Nume familie', 'trim|required|validate_alpha_spaces|max_length[255]');
    $this->form_validation->set_rules('contact_firstname', 'Prenume', 'trim|required|validate_alpha_spaces|max_length[255]');
    $this->form_validation->set_rules('contact_country', 'Tara', 'trim|required|valid_country[iso_2]',array(
      'valid_country' => 'Tara invalida',
    ));
    $this->form_validation->set_rules('contact_city', 'Oras', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('contact_street', 'Strada', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('contact_street_no', 'Nr. Strada', 'trim|required|max_length[20]');
    $this->form_validation->set_rules('contact_phone_prefix', 'Prefix telefon', 'trim|required|valid_country[iso_2]',array(
      'valid_country' => 'Prefix invalid',
    ));
    $this->form_validation->set_rules('contact_phone', 'Telefon', 'trim|required|max_length[100]');
    
    $this->form_validation->set_rules('passenger', 'Pasageri', 'is_array',array(
      'is_array' => 'Campul Pasageri este obligatoriu'
    ));
    $invoice = $this->input->post('invoice');
    if($invoice == 'pj'){
      $this->form_validation->set_rules('contact_company_name', 'Nume companie', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('contact_bank', 'Banca', 'trim|max_length[255]');
      $this->form_validation->set_rules('contact_regcom', 'Nr.Reg.Com.', 'trim|max_length[255]');
      $this->form_validation->set_rules('contact_cui', 'CUI', 'trim|required|max_length[50]|validate_CIF_or_CUI',array(
        'validate_CIF_or_CUI' => 'Codul CUI introdus este invalid',
      ));
      $this->form_validation->set_rules('contact_iban', 'IBAN', 'trim|max_length[50]|valid_iban',array(
        'valid_iban' => 'Codul IBAN introdus este invalid'
      ));
    }
    
    $passengers = $this->input->post('passenger');
    if(isset($passengers)){
      if(is_array($passengers)){
        $passenger_fields = array(
          'title',
          'firstname',
          'lastname',
          'birth_date',
          'country',
        );
        foreach($passenger_fields as $passenger_field){
          if(!isset($passengers[$passenger_field])){
            $this->addError('Invalid passengers data');
            return false;
          }
          $expected_passenger_index = 0;
          foreach($passengers[$passenger_field] as $passenger_index =>$passenger_field_value){
            if($passenger_index !== $expected_passenger_index){
              $this->addError('Invalid passengers data - expected index fail');
              return false;
            }
            $expected_passenger_index ++;
            
            $fake_post_index = 'f_passenger_' . $passenger_field . '_' . $passenger_index;
            $_POST[$fake_post_index] = $passenger_field_value;
            if($passenger_field == 'title'){
              $this->form_validation->set_rules($fake_post_index, 'Titlu', 'trim|required|in_list[mr,mrs,ms,chd]',array(
                'in_list' => 'Titlu invalid pentru calatorul #' . ($passenger_index+1),
              ));
            } elseif($passenger_field == 'firstname'){
              $this->form_validation->set_rules($fake_post_index, 'Prenume', 'trim|required|validate_alpha_spaces|max_length[255]',array(
                'validate_alpha_spaces' => 'Prenumele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
                'max_length' => 'Prenumele introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
              ));
            } elseif($passenger_field == 'lastname'){
              $this->form_validation->set_rules($fake_post_index, 'Nume familie', 'trim|validate_alpha_spaces|required|max_length[255]',array(
                'validate_alpha_spaces' => 'Numele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
                'required' => 'Nume necompletat pentru calatorul #' . ($passenger_index+1),
                'max_length' => 'Nume de familie introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
              ));
            } elseif($passenger_field == 'birth_date'){
              $this->form_validation->set_rules($fake_post_index, 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
                'required' => 'Data nastere necompletata pentru calatorul #' . ($passenger_index+1),
                'valid_date' => 'Formatul datei este invalid pentru calatorul #' . ($passenger_index+1),
              ));
            } elseif($passenger_field == 'country'){
              $this->form_validation->set_rules($fake_post_index, 'Nationalitate', 'trim|required|valid_country[iso_2]',array(
                'valid_country' => 'Nationalitate invalida pentru calatorul #' . ($passenger_index+1),
              ));
            }
          }
        }
      }
    }
    if(!$this->user->id){
      $this->form_validation->set_rules('create_account', 'Rezervare cu/fara cont', 'trim|required|in_list[0,1]');
      $create_account = $this->input->post('create_account');
      if($create_account){
        $this->form_validation->set_rules('password', 'Parola', 'min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirmare parola', 'matches[password]',array(
          'matches' => 'Parolele nu coincid',
        ));
        $this->form_validation->set_rules('contact_email', 'Email', 'trim|required|max_length[255]|valid_email|is_unique[ac_user.user_username]|is_unique[ac_user.user_email]|is_unique[trip_blockemail.code]', array(
          'is_unique' => 'Acest email este deja utilizat in platforma.',
        ));
      } else {
        $this->form_validation->set_rules('contact_email', 'Email', 'trim|required|max_length[255]|valid_email|is_unique[trip_blockemail.code]', array(
          'is_unique' => 'Acest email este deja utilizat in platforma.',
        ));
      }
    } else {
      $this->form_validation->set_rules('contact_email', 'Email', 'trim|required|max_length[255]|valid_email|is_unique[trip_blockemail.code]', array(
          'is_unique' => 'Acest email este deja utilizat in platforma.2',
        ));
    }
    $this->form_validation->set_rules('tos', 'Termeni si conditii', 'trim|required|in_list[1]',array(
      'required' => 'Termenii si conditiile trebuie acceptate',
      'in_list' => 'Termenii si conditiile trebuie acceptate',
    ));
    $this->form_validation->set_rules('tpc', 'Acord prelucrare date caracter personal', 'trim|required|in_list[1]',array(
      'required' => 'Necesar Acord prelucrare date caracter personal',
      'in_list' => 'Necesar Acord prelucrare date caracter personal',
    ));
    return true;
  }
  private function validate_package(){
    $this->form_validation->set_rules('code', 'Cod', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('package_id', 'ID Vacanta', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('entry_id', 'ID Perioada', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('rate_group_id', 'ID Grup interval', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    return true;
  }
  private function validate_hotel(){
    $this->form_validation->set_rules('code', 'Cod', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('hotel_id', 'Hotel ID', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('package_code', 'Cod pachet', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('rooms_combinations', 'Combinatie camere', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    return true;
  }
  private function validate_flight(){
    $this->form_validation->set_rules('flight_code', 'Cod zbor', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('itinerary_code', 'Cod itinerar', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $insurance_travel = $this->input->post('insurance_travel');
    $insurance_storno = $this->input->post('insurance_storno');
    if(isset($insurance_travel) || isset($insurance_storno)){
      $this->load->model('Options_model');
      $flights_settings = $this->Options_model->get('trip_flights_settings');
      if(isset($insurance_travel)){
        $travel_prices = isset($flights_settings['travel_prices']) ? (array)$flights_settings['travel_prices'] : array();
        $travel_keys = array_keys($travel_prices);
        $this->form_validation->set_rules('insurance_travel', 'Asigurare calatorie', 'in_list[' . implode(',', $travel_keys) . ']',array(
          'in_list' => 'Alegere invalida',
        ));
      }
      if(isset($insurance_storno)){
        $storno_prices = isset($flights_settings['storno_prices']) ? (array)$flights_settings['storno_prices'] : array();
        $storno_keys = array_keys($storno_prices);
        $this->form_validation->set_rules('insurance_storno', 'Asigurare storno', 'in_list[' . implode(',', $storno_keys) . ']',array(
          'in_list' => 'Alegere invalida',
        ));
      }
    }
	$flight2 = $_POST['flight2'] ?? null;
	if($flight2){
		$_POST['f2_flight_code'] = $flight2['flight_code'];
		$this->form_validation->set_rules('f2_flight_code', 'Cod zbor 2', 'trim|required',array(
		  'required' => 'Informatii invalide',
		));
		$_POST['f2_itinerary_code'] = $flight2['itinerary_code'];
		$this->form_validation->set_rules('f2_itinerary_code', 'Cod itinerar 2', 'trim|required',array(
		  'required' => 'Informatii invalide',
		));
	}
    return true;
  }
  private function validate_citybreak(){
    return $this->validate_hotel() && $this->validate_flight();
  }
  
  function __call($method, $args){
    if($this->router->class == get_class($this)){
      throw new Exception("Direct access forbidden");
    }
    if (!method_exists($this, $method)) {
      throw new Exception("Unknown method [$method]");
    }
    return call_user_func_array(
      array($this, $method),
      $args
    );
  }
  private function validate_paralela45_strainatate(){
    $this->form_validation->set_rules('product_code', 'Cod produs', 'trim|required|max_length[6]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('destination_city_code', 'Cod oras destinatie', 'trim|required|max_length[10]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('departure_city_code', 'Cod oras plecare', 'trim|required|max_length[10]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('package_id', 'Cod pachet', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('package_variant_id', 'Cod oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('checkin', 'Data checkin', 'trim|required|valid_date[Y-m-d]',array(
      'required' => 'Informatii invalide',
      'valid_date' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('occupancy', 'Persoane in camere', 'trim|required',array(
      'required' => 'Informatii invalide',
      'json_decode' => 'Informatii invalide',
    ));
    $checkin = $this->input->post('checkin');
    $checkout = $this->input->post('checkout');
    $minimum_date_start = isset($checkin) && strlen(trim($checkin)) ? trim($checkin) : date('Y-m-d');
    $this->form_validation->set_rules('checkout', 'Data checkout', 'trim|required|valid_date[Y-m-d]|is_greater_than[' . $minimum_date_start . ']',array(
      'required' => 'Informatii invalide',
      'valid_date' => 'Informatii invalide',
      'is_greater_than' => 'Informatii invalide',
    ));
    $post_occupancy = $this->input->post('occupancy');
    $occupancy = isset($post_occupancy) ? json_decode($post_occupancy) : array();
    $found_error = false;
    $error_message = "Informatii invalide";
    $total_rooms = 0;
    $total_adults = 0;
    $total_children = 0;
    $children_ages = array();
    if(empty($occupancy) || !is_array($occupancy)){
      $_POST['occupancy'] = null;
      $occupancy = array();
    }
    
    $passengers = $this->input->post('passenger');
    $today = new DateTime();
    $rooms_occupants = array(
      'adults' => array(),
      'children' => array(),
    );
    if(isset($passengers) && is_array($passengers)){
      $passenger_fields = array(
        'title',
        'firstname',
        'lastname',
        'birth_date',
        'country',
      );
      
      foreach($passenger_fields as $passenger_field){
        if(!isset($passengers[$passenger_field])){
          $error_message = "Invalid passengers data";
          $found_error = true;
          break;
        }
        $expected_passenger_index = 0;
        foreach($passengers[$passenger_field] as $passenger_index =>$passenger_field_value){
          if($passenger_index !== $expected_passenger_index){
            $error_message = "Invalid passengers data - expected index fail";
            $found_error = true;
            break;
          }
          $expected_passenger_index ++;
          
          $fake_post_index = 'f_passenger_' . $passenger_field . '_' . $passenger_index;
          $_POST[$fake_post_index] = $passenger_field_value;
          if($passenger_field == 'title'){
            $this->form_validation->set_rules($fake_post_index, 'Titlu', 'trim|required|in_list[mr,mrs,ms,chd]',array(
              'in_list' => 'Titlu invalid pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'firstname'){
            $this->form_validation->set_rules($fake_post_index, 'Prenume', 'trim|required|validate_alpha_spaces|max_length[255]',array(
              'validate_alpha_spaces' => 'Prenumele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
              'max_length' => 'Prenumele introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'lastname'){
            $this->form_validation->set_rules($fake_post_index, 'Nume familie', 'trim|validate_alpha_spaces|required|max_length[255]',array(
              'validate_alpha_spaces' => 'Numele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
              'required' => 'Nume necompletat pentru calatorul #' . ($passenger_index+1),
              'max_length' => 'Nume de familie introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'birth_date'){
            $this->form_validation->set_rules($fake_post_index, 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
              'required' => 'Data nastere necompletata pentru calatorul #' . ($passenger_index+1),
              'valid_date' => 'Formatul datei este invalid pentru calatorul #' . ($passenger_index+1),
            ));
            $birth_date = DateTime::createFromFormat('d.m.Y', $passenger_field_value);
            if(!$birth_date || $birth_date->format('d.m.Y') != $passenger_field_value){
              $error_message = "Invalid passenger birthdate format";
              $found_error = true;
              break;
            }
            $age = (int) $today->diff($birth_date)->y;
            $title = isset($passengers['title'][$passenger_index]) ? $passengers['title'][$passenger_index] : 'mr';
            $gender = $title == 'mr' ? 'B' : 'F';
            $lastname = isset($passengers['lastname'][$passenger_index]) ? $passengers['lastname'][$passenger_index] : '';
            $firstname = isset($passengers['firstname'][$passenger_index]) ? $passengers['firstname'][$passenger_index] : '';
            $country = isset($passengers['country'][$passenger_index]) ? $passengers['country'][$passenger_index] : '';
            if($age >= 18){
              if(!isset($this->adult_birthday)){
                $this->adult_title = $title;
                $this->adult_birthday = $birth_date->format('Y-m-d');
              }
              $total_adults ++;
              $room_occupant = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'title' => $gender,
                'country' => $country,
                'birth_date' => $birth_date->format('Y-m-d'),
              );
              $rooms_occupants['adults'][] = $room_occupant;
            } else {
              if(!isset($children_ages[(int)$age])){
                $children_ages[(int)$age] = 0;
              }
              $children_ages[(int)$age]++;
              $total_children ++;
              $room_occupant = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'title' => $gender,
                'country' => $country,
                'age' => $age,
                'birth_date' => $birth_date->format('Y-m-d'),
              );
              if(!isset($rooms_occupants['children'][$age])){
                $rooms_occupants['children'][$age] = array();
              }
              $rooms_occupants['children'][$age][] = $room_occupant;
            }
          } elseif($passenger_field == 'country'){
            $this->form_validation->set_rules($fake_post_index, 'Nationalitate', 'trim|required|valid_country[iso_2]',array(
              'valid_country' => 'Nationalitate invalida pentru calatorul #' . ($passenger_index+1),
            ));
          }
        }
        if($found_error){
          break;
        }
      }
    }
    
    $remaining_adults = $total_adults;
    $remaining_children = $total_children;
    $remaining_child_ages = $children_ages;
    $remaining_rooms_occupants = $rooms_occupants;
    $rooms_occupancy = array();
    $service_rooms = array();
    if(!$found_error){
      foreach($occupancy as $room_index => $assigned_room){
        $room_occupancy = array(
          'adt' => 0,
          'chd' => array(),
        );
        $service_room = array(
          'adt' => array(),
          'chd' => array(),
        );
        $total_rooms++;
        if(!is_object($assigned_room)){
          $error_message = "Informatii invalide calatori";
          $found_error = true;
          break;
        }
        if(!property_exists($assigned_room, 'adt') || $assigned_room->adt<1 || $assigned_room->adt>6){
          $error_message = "Numar invalid de adulti in camera " . $total_rooms;
          $found_error = true;
          break;
        }
        $remaining_adults -= $assigned_room->adt;
        for($i=1; $i<=$assigned_room->adt; $i++){
          $adult = array_shift($rooms_occupants['adults']);
          if(!$adult){
            $error_message = "Numar invalid de adulti";
            $found_error = true;
            break;
          }
          $room_occupancy['adt']++;
          $service_room['adt'][] = $adult;
        }
        if($found_error){
          break;
        }
        if(property_exists($assigned_room,'chd')){
          if(!is_array($assigned_room->chd)){
            $error_message = "Informatii invalide copii";
            $found_error = true;
            break;
          }
          
          foreach($assigned_room->chd as $child_age){
            if(!is_numeric($child_age) || ('' . (int)$child_age !== '' . $child_age)){
              $error_message = "Informatii invalide varsta copil";
              $found_error = true;
              break;
            }
            if($child_age < 0 || $child_age > 17){
              $error_message = "Varsta invalida copil";
              $found_error = true;
              break;
            }
            $age = $child_age;
            if(!isset($rooms_occupants['children'][$age]) || !($child = array_shift($rooms_occupants['children'][$age]))){
              $error_message = "Varsta copil (" . $age . ") incompatibila";
              $found_error = true;
              break;
            }
            $service_room['chd'][] = $child;
            $room_occupancy['chd'][]=$age;

            $remaining_children--;
            if(isset($remaining_child_ages[$age])){
              $remaining_child_ages[$age] --;
              if(!$remaining_child_ages[$age]){
                unset($remaining_child_ages[$age]);
              }
            }
          }
        }
        if($found_error){
          break;
        }
        $rooms_occupancy[] = $room_occupancy;
        $service_rooms[] = $service_room;
      }
    }
    $_POST['rooms_occupancy'] = $rooms_occupancy;
    $_POST['service_rooms'] = $service_rooms;
    if(!$found_error && !empty($remaining_adults)){
      $error_message = "Numarul de pasageri adulti calculat din varsta de nastere difera de cel al ofertei";
      $found_error = true;
    }
    if(!$found_error && !empty($remaining_child_ages)){
      $error_message = "Numarul de pasageri copii calculat din varsta de nastere difera de cel al ofertei";
      $found_error = true;
    }
    $_POST['no_errors'] = true;
    if($found_error){
      $_POST['no_errors'] = null;
    }
    $this->form_validation->set_rules('no_errors', 'Repartizare in camere', 'required',array(
      'required' => $error_message,
    ));
    return true;
  }
  private function service_paralela45_strainatate($run = true){
    $offer_id = $this->input->post('offer_id');
    $total_expected_price = floatval($this->input->post('total_expected_price')) + 0;
    
    $service_info = array();
    $service_info['type'] = 'strainatate';
    $service_info['offer_id'] = $offer_id;
    $service_info['package_id'] = trim($this->input->post('package_id'));
    $service_info['package_variant_id'] = trim($this->input->post('package_variant_id'));
    $service_info['departure_city_code'] = trim($this->input->post('departure_city_code'));
    $service_info['checkin'] = trim($this->input->post('checkin'));
    $service_info['checkout'] = trim($this->input->post('checkout'));
    $service_info['comment'] = trim($this->input->post('comment'));
    $service_info['destination_city_code'] = trim($this->input->post('destination_city_code'));
    $occupancy = $this->input->post('rooms_occupancy'); // TODO - json - to array
    $service_rooms = $this->input->post('service_rooms'); // TODO - person details per room
    $service_info['product_code'] = trim($this->input->post('product_code'));
    $service_info['occupancy'] = $occupancy;
    $service_info['service_rooms'] = $service_rooms;
    $extra_services = $this->input->post('extra_services');
    $service_info['selected_extra_services'] = (array)$extra_services;
    try{
      $this->Paralela45_Strainatate_model->getBookingService($service_info);
    } catch (Exception $e){
      $this->addError($e->getMessage());
    }
    if($service_info['price'] != $total_expected_price){
      $this->data['service_info'] = $service_info;
      $this->addError('Pretul ofertei s-a modificat intre timp. Va rugam sa reincarcati pagina.');
      return false;
    }
    $this->service_info = $service_info;
    return true;
  }
  private function validate_paralela45_circuit(){
    $this->form_validation->set_rules('offer_id', 'Cod oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('package_id', 'Cod cautare oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('package_variant_id', 'Cod varianta oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('occupancy', 'Persoane in camere', 'trim|required',array(
      'required' => 'Informatii invalide',
      'json_decode' => 'Informatii invalide',
    ));
    $post_occupancy = $this->input->post('occupancy');
    $occupancy = isset($post_occupancy) ? json_decode($post_occupancy) : array();
    $found_error = false;
    $error_message = "Informatii invalide";
    $total_rooms = 0;
    $total_adults = 0;
    $total_children = 0;
    $children_ages = array();
    if(empty($occupancy) || !is_array($occupancy)){
      $_POST['occupancy'] = null;
      $occupancy = array();
    }
    
    $passengers = $this->input->post('passenger');
    $today = new DateTime();
    $rooms_occupants = array(
      'adults' => array(),
      'children' => array(),
    );
    if(isset($passengers) && is_array($passengers)){
      $passenger_fields = array(
        'title',
        'firstname',
        'lastname',
        'birth_date',
        'country',
      );
      
      foreach($passenger_fields as $passenger_field){
        if(!isset($passengers[$passenger_field])){
          $error_message = "Invalid passengers data";
          $found_error = true;
          break;
        }
        $expected_passenger_index = 0;
        foreach($passengers[$passenger_field] as $passenger_index =>$passenger_field_value){
          if($passenger_index !== $expected_passenger_index){
            $error_message = "Invalid passengers data - expected index fail";
            $found_error = true;
            break;
          }
          $expected_passenger_index ++;
          
          $fake_post_index = 'f_passenger_' . $passenger_field . '_' . $passenger_index;
          $_POST[$fake_post_index] = $passenger_field_value;
          if($passenger_field == 'title'){
            $this->form_validation->set_rules($fake_post_index, 'Titlu', 'trim|required|in_list[mr,mrs,ms,chd]',array(
              'in_list' => 'Titlu invalid pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'firstname'){
            $this->form_validation->set_rules($fake_post_index, 'Prenume', 'trim|required|validate_alpha_spaces|max_length[255]',array(
              'validate_alpha_spaces' => 'Prenumele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
              'max_length' => 'Prenumele introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'lastname'){
            $this->form_validation->set_rules($fake_post_index, 'Nume familie', 'trim|validate_alpha_spaces|required|max_length[255]',array(
              'validate_alpha_spaces' => 'Numele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
              'required' => 'Nume necompletat pentru calatorul #' . ($passenger_index+1),
              'max_length' => 'Nume de familie introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'birth_date'){
            $this->form_validation->set_rules($fake_post_index, 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
              'required' => 'Data nastere necompletata pentru calatorul #' . ($passenger_index+1),
              'valid_date' => 'Formatul datei este invalid pentru calatorul #' . ($passenger_index+1),
            ));
            $birth_date = DateTime::createFromFormat('d.m.Y', $passenger_field_value);
            if(!$birth_date || $birth_date->format('d.m.Y') != $passenger_field_value){
              $error_message = "Invalid passenger birthdate format";
              $found_error = true;
              break;
            }
            $age = (int) $today->diff($birth_date)->y;
            $title = isset($passengers['title'][$passenger_index]) ? $passengers['title'][$passenger_index] : 'mr';
            $gender = $title == 'mr' ? 'B' : 'F';
            $lastname = isset($passengers['lastname'][$passenger_index]) ? $passengers['lastname'][$passenger_index] : '';
            $firstname = isset($passengers['firstname'][$passenger_index]) ? $passengers['firstname'][$passenger_index] : '';
            $country = isset($passengers['country'][$passenger_index]) ? $passengers['country'][$passenger_index] : '';
            if($age >= 18){
              if(!isset($this->adult_birthday)){
                $this->adult_title = $title;
                $this->adult_birthday = $birth_date->format('Y-m-d');
              }
              $total_adults ++;
              $room_occupant = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'title' => $gender,
                'country' => $country,
                'birth_date' => $birth_date->format('Y-m-d'),
              );
              $rooms_occupants['adults'][] = $room_occupant;
            } else {
              if(!isset($children_ages[(int)$age])){
                $children_ages[(int)$age] = 0;
              }
              $children_ages[(int)$age]++;
              $total_children ++;
              $room_occupant = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'title' => $gender,
                'country' => $country,
                'age' => $age,
                'birth_date' => $birth_date->format('Y-m-d'),
              );
              if(!isset($rooms_occupants['children'][$age])){
                $rooms_occupants['children'][$age] = array();
              }
              $rooms_occupants['children'][$age][] = $room_occupant;
            }
          } elseif($passenger_field == 'country'){
            $this->form_validation->set_rules($fake_post_index, 'Nationalitate', 'trim|required|valid_country[iso_2]',array(
              'valid_country' => 'Nationalitate invalida pentru calatorul #' . ($passenger_index+1),
            ));
          }
        }
        if($found_error){
          break;
        }
      }
    }
    
    $remaining_adults = $total_adults;
    $remaining_children = $total_children;
    $remaining_child_ages = $children_ages;
    $remaining_rooms_occupants = $rooms_occupants;
    $rooms_occupancy = array();
    $service_rooms = array();
    if(!$found_error){
      foreach($occupancy as $room_index => $assigned_room){
        $room_occupancy = array(
          'adt' => 0,
          'chd' => array(),
        );
        $service_room = array(
          'adt' => array(),
          'chd' => array(),
        );
        $total_rooms++;
        if(!is_object($assigned_room)){
          $error_message = "Informatii invalide calatori";
          $found_error = true;
          break;
        }
        if(!property_exists($assigned_room, 'adt') || $assigned_room->adt<1 || $assigned_room->adt>6){
          $error_message = "Numar invalid de adulti in camera " . $total_rooms;
          $found_error = true;
          break;
        }
        $remaining_adults -= $assigned_room->adt;
        for($i=1; $i<=$assigned_room->adt; $i++){
          $adult = array_shift($rooms_occupants['adults']);
          if(!$adult){
            $error_message = "Numar invalid de adulti";
            $found_error = true;
            break;
          }
          $room_occupancy['adt']++;
          $service_room['adt'][] = $adult;
        }
        if($found_error){
          break;
        }
        if(property_exists($assigned_room,'chd')){
          if(!is_array($assigned_room->chd)){
            $error_message = "Informatii invalide copii";
            $found_error = true;
            break;
          }
          
          foreach($assigned_room->chd as $child_age){
            if(!is_numeric($child_age) || ('' . (int)$child_age !== '' . $child_age)){
              $error_message = "Informatii invalide varsta copil";
              $found_error = true;
              break;
            }
            if($child_age < 0 || $child_age > 17){
              $error_message = "Varsta invalida copil";
              $found_error = true;
              break;
            }
            $age = $child_age;
            if(!isset($rooms_occupants['children'][$age]) || !($child = array_shift($rooms_occupants['children'][$age]))){
              $error_message = "Varsta copil (" . $age . ") incompatibila";
              $found_error = true;
              break;
            }
            $service_room['chd'][] = $child;
            $room_occupancy['chd'][]=$age;

            $remaining_children--;
            if(isset($remaining_child_ages[$age])){
              $remaining_child_ages[$age] --;
              if(!$remaining_child_ages[$age]){
                unset($remaining_child_ages[$age]);
              }
            }
          }
        }
        if($found_error){
          break;
        }
        $rooms_occupancy[] = $room_occupancy;
        $service_rooms[] = $service_room;
      }
    }
    $_POST['rooms_occupancy'] = $rooms_occupancy;
    $_POST['service_rooms'] = $service_rooms;
    if(!$found_error && !empty($remaining_adults)){
      $error_message = "Numarul de pasageri adulti calculat din varsta de nastere difera de cel al ofertei";
      $found_error = true;
    }
    if(!$found_error && !empty($remaining_child_ages)){
      $error_message = "Numarul de pasageri copii calculat din varsta de nastere difera de cel al ofertei";
      $found_error = true;
    }
    $_POST['no_errors'] = true;
    if($found_error){
      $_POST['no_errors'] = null;
    }
    $this->form_validation->set_rules('no_errors', 'Repartizare in camere', 'required',array(
      'required' => $error_message,
    ));
    return true;
  }
  private function service_paralela45_circuit($run = true){
    $offer_id = $this->input->post('offer_id');
    $total_expected_price = floatval($this->input->post('total_expected_price')) + 0;
    
    $service_info = array();
    $service_info['type'] = 'circuit';
    $service_info['offer_id'] = $offer_id;
    $service_info['package_id'] = trim($this->input->post('package_id'));
    $service_info['package_variant_id'] = trim($this->input->post('package_variant_id'));
    $service_info['comment'] = trim($this->input->post('comment'));
    $service_info['departure_city_code'] = trim($this->input->post('departure_city_code'));
    $service_info['destination_city_code'] = trim($this->input->post('destination_city_code'));
    $service_info['destination_country_code'] = trim($this->input->post('destination_country_code'));
    $service_info['start_date'] = trim($this->input->post('start_date'));
    $service_info['nights'] = trim($this->input->post('nights'));
    $service_info['hotel_name'] = trim($this->input->post('hotel_name'));
    $occupancy = $this->input->post('rooms_occupancy'); // TODO - json - to array
    $service_rooms = $this->input->post('service_rooms'); // TODO - person details per room
    $service_info['occupancy'] = $occupancy;
    $service_info['service_rooms'] = $service_rooms;
    $extra_services = $this->input->post('extra_services');
    $service_info['selected_extra_services'] = (array)$extra_services;
    try{
      $this->Paralela45_Circuit_model->getBookingService($service_info);
    } catch (Exception $e){
      $this->addError($e->getMessage());
    }
    
    if($service_info['price'] != $total_expected_price){
      $this->data['service_info'] = $service_info;
      $this->addError('Pretul ofertei s-a modificat intre timp. Va rugam sa reincarcati pagina.' . $service_info['price']);
      return false;
    }
    $this->service_info = $service_info;
    return true;
  }
  private function validate_travelfuse(){
    $this->form_validation->set_rules('OfferId', 'Cod oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('SearchId', 'Cod cautare oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('ProductCode', 'Cod produs', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
	$travellers = $this->input->post('travellers');
	$_POST['passenger'] = null;
	if(!empty($travellers)){
		$passengers = [];
		foreach($travellers as $travellerIndex => $traveller){
			$passengers['title'][] = ($traveller['Gender'] ?? 1) == 1 ? 'mr' : 'mrs';
			$passengers['firstname'][] = $traveller['Firstname'];
			$passengers['lastname'][] = $traveller['Name'];
			$dob = preg_split('/\-/', $traveller['Birthdate']);
			$dob = array_reverse($dob);
			$dob = implode('.', $dob);
			$passengers['birth_date'][] = $dob;
			$passengers['country'][] = $traveller['Country'] ?? 'RO';
		}
		$_POST['passenger'] = $passengers;
	}
	
    $this->form_validation->set_rules('passenger', 'Calatori', 'required',array(
      'required' => 'Informatii invalide',
    ));
	
	$occupancy = [];
	$search_data = $this->input->post('search_data');
	if(isset($search_data[0])){
		$search_data = array_merge($search_data[0], $search_data);
	}
	if(empty($search_data) || !is_array($search_data)) $search_data = [];
	$ADT = !empty($search_data['Adults']) ? (int)($search_data['Adults'][0] ?? 0) : 0;
	$CHD = !empty($search_data['ChildrenAge']) ? ($search_data['ChildrenAge'][0] ?? []) : [];
	$room = [];
	$room['adt'] = $ADT;
	$room['chd'] = $CHD;
	$occupancy[] = (object)$room;
	$_POST['occupancy'] = $occupancy;
	
    $found_error = false;
    $error_message = "Informatii invalide";
    $total_rooms = 0;
    $total_adults = 0;
    $total_children = 0;
    $children_ages = array();
    
    $passengers = $this->input->post('passenger');
	// dd($passengers);
	$checkin = $search_data['CheckIn'] ?? '';
    $today = new DateTime($checkin);
    $rooms_occupants = array(
      'adults' => array(),
      'children' => array(),
    );
    if(isset($passengers) && is_array($passengers)){
      $passenger_fields = array(
        'title',
        'firstname',
        'lastname',
        'birth_date',
        'country',
      );
      
      foreach($passenger_fields as $passenger_field){
        if(!isset($passengers[$passenger_field])){
          $error_message = "Invalid passengers data";
          $found_error = true;
          break;
        }
        $expected_passenger_index = 0;
        foreach($passengers[$passenger_field] as $passenger_index =>$passenger_field_value){
          if($passenger_index !== $expected_passenger_index){
            $error_message = "Invalid passengers data - expected index fail";
            $found_error = true;
            break;
          }
          $expected_passenger_index ++;
          
          $fake_post_index = 'f_passenger_' . $passenger_field . '_' . $passenger_index;
          $_POST[$fake_post_index] = $passenger_field_value;
          if($passenger_field == 'title'){
            $this->form_validation->set_rules($fake_post_index, 'Titlu', 'trim|required|in_list[mr,mrs,ms,chd]',array(
              'in_list' => 'Titlu invalid pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'firstname'){
            $this->form_validation->set_rules($fake_post_index, 'Prenume', 'trim|required|validate_alpha_spaces|max_length[255]',array(
              'validate_alpha_spaces' => 'Prenumele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
              'max_length' => 'Prenumele introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'lastname'){
            $this->form_validation->set_rules($fake_post_index, 'Nume familie', 'trim|validate_alpha_spaces|required|max_length[255]',array(
              'validate_alpha_spaces' => 'Numele introdus contine caractere nepermise pentru calatorul #' . ($passenger_index+1),
              'required' => 'Nume necompletat pentru calatorul #' . ($passenger_index+1),
              'max_length' => 'Nume de familie introdus depaseste limita admisa pentru calatorul #' . ($passenger_index+1),
            ));
          } elseif($passenger_field == 'birth_date'){
            $this->form_validation->set_rules($fake_post_index, 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
              'required' => 'Data nastere necompletata pentru calatorul #' . ($passenger_index+1),
              'valid_date' => 'Formatul datei este invalid pentru calatorul #' . ($passenger_index+1),
            ));
            $birth_date = DateTime::createFromFormat('d.m.Y', $passenger_field_value);
            if(!$birth_date || $birth_date->format('d.m.Y') != $passenger_field_value){
              $error_message = "Invalid passenger birthdate format";
              $found_error = true;
              break;
            }
            $age = (int) $today->diff($birth_date)->y;
            $title = isset($passengers['title'][$passenger_index]) ? $passengers['title'][$passenger_index] : 'mr';
            $gender = $title == 'mr' ? 1 : 2;
            $lastname = isset($passengers['lastname'][$passenger_index]) ? $passengers['lastname'][$passenger_index] : '';
            $firstname = isset($passengers['firstname'][$passenger_index]) ? $passengers['firstname'][$passenger_index] : '';
            $country = isset($passengers['country'][$passenger_index]) ? $passengers['country'][$passenger_index] : '';
            if($age >= 18){
              if(!isset($this->adult_birthday)){
                $this->adult_title = $title;
                $this->adult_birthday = $birth_date->format('Y-m-d');
              }
              $total_adults ++;
              $room_occupant = array(
                'Gender' => $gender,
                'Name' => $lastname,
                'Firstname' => $firstname,
                'Birthdate' => $birth_date->format('Y-m-d'),
              );
              $rooms_occupants['adults'][] = $room_occupant;
            } else {
              if(!isset($children_ages[(int)$age])){
                $children_ages[(int)$age] = 0;
              }
              $children_ages[(int)$age]++;
              $total_children ++;
              $room_occupant = array(
                'Name' => $lastname,
                'Firstname' => $firstname,
                'Birthdate' => $birth_date->format('Y-m-d'),
              );
              if(!isset($rooms_occupants['children'][$age])){
                $rooms_occupants['children'][$age] = array();
              }
              $rooms_occupants['children'][$age][] = $room_occupant;
            }
          } elseif($passenger_field == 'country'){
            $this->form_validation->set_rules($fake_post_index, 'Nationalitate', 'trim|required|valid_country[iso_2]',array(
              'valid_country' => 'Nationalitate invalida pentru calatorul #' . ($passenger_index+1),
            ));
          }
        }
        if($found_error){
          break;
        }
      }
    }
    
    $remaining_adults = $total_adults;
    $remaining_children = $total_children;
    $remaining_child_ages = $children_ages;
    $remaining_rooms_occupants = $rooms_occupants;
    $rooms_occupancy = array();
    $service_rooms = array();
	// dd($occupancy);
    if(!$found_error){
      foreach($occupancy as $room_index => $assigned_room){
        $room_occupancy = array(
          'adt' => 0,
          'chd' => array(),
        );
        $service_room = array(
          'Adults' => array(),
          'Children' => array(),
        );
        $total_rooms++;
        if(!is_object($assigned_room)){
          $error_message = "Informatii invalide calatori";
          $found_error = true;
          break;
        }
        if(!property_exists($assigned_room, 'adt') || $assigned_room->adt<1 || $assigned_room->adt>5){
          $error_message = "Numar invalid de adulti in camera " . $total_rooms;
          $found_error = true;
          break;
        }
        $remaining_adults -= $assigned_room->adt;
        for($i=1; $i<=$assigned_room->adt; $i++){
          $adult = array_shift($rooms_occupants['adults']);
          if(!$adult){
            $error_message = "Numar invalid de adulti";
            $found_error = true;
            break;
          }
          $room_occupancy['adt']++;
          $service_room['Adults'][] = $adult;
        }
        if($found_error){
          break;
        }
        if(property_exists($assigned_room,'chd')){
          if(!is_array($assigned_room->chd)){
            $error_message = "Informatii invalide copii";
            $found_error = true;
            break;
          }
          if(count($assigned_room->chd) > 4){
			$error_message = "Numar invalid de copii";
            $found_error = true;  
			break;
		  }
          foreach($assigned_room->chd as $child_age){
            if(!is_numeric($child_age) || ('' . (int)$child_age !== '' . $child_age)){
              $error_message = "Informatii invalide varsta copil";
              $found_error = true;
              break;
            }
            if($child_age < 0 || $child_age > 17){
              $error_message = "Varsta invalida copil";
              $found_error = true;
              break;
            }
            $age = $child_age;
            if(!isset($rooms_occupants['children'][$age]) || !($child = array_shift($rooms_occupants['children'][$age]))){
              $error_message = "Varsta copil (" . $age . ") incompatibila";
              $found_error = true;
              break;
            }
            $service_room['Children'][] = $child;
            $room_occupancy['chd'][]=$age;

            $remaining_children--;
            if(isset($remaining_child_ages[$age])){
              $remaining_child_ages[$age] --;
              if(!$remaining_child_ages[$age]){
                unset($remaining_child_ages[$age]);
              }
            }
          }
        }
        if($found_error){
          break;
        }
        $rooms_occupancy[] = $room_occupancy;
        $service_rooms[] = $service_room;
      }
    }
    $_POST['rooms_occupancy'] = $rooms_occupancy;
    $_POST['service_rooms'] = $service_rooms;
    if(!$found_error && !empty($remaining_adults)){
      $error_message = "Numarul de pasageri adulti calculat din varsta de nastere difera de cel al ofertei";
      $found_error = true;
    }
    if(!$found_error && !empty($remaining_child_ages)){
      $error_message = "Numarul de pasageri copii calculat din varsta de nastere difera de cel al ofertei";
      $found_error = true;
    }
    $_POST['no_errors'] = true;
    if($found_error){
      $_POST['no_errors'] = null;
    }
    $this->form_validation->set_rules('no_errors', 'Repartizare in camere', 'required',array(
      'required' => $error_message,
    ));
    return true;
  }
  private function validate_travelfuse_circuit(){
	  return $this->validate_travelfuse();
  }
  private function validate_travelfuse_charter(){
	  return $this->validate_travelfuse();
  }
  private function service_travelfuse_circuit($run = true){
	  $service = $this->service_travelfuse('circuit', $run);
	  return $service;
  }
  private function service_travelfuse_charter($run = true){
	  $service = $this->service_travelfuse('charter', $run);
	  return $service;
  }
  private function service_travelfuse($type, $run = true){
    $offer_id = $this->input->post('OfferId');
    $total_expected_price = floatval($this->input->post('total_expected_price')) + 0;
    
	$search_data = $this->input->post('search_data');
	if(isset($search_data[0])){
		$search_data = array_merge($search_data[0], $search_data);
	}
	if(empty($search_data) || !is_array($search_data)) $search_data = [];
	
    $service_info = array();
    $service_info['offer_id'] = $offer_id;
    $service_info['type'] = $type;
	
	$service_info['SearchData'] = [];
    $service_info['SearchData']['Transport'] = isset($search_data['Transport']) ? $search_data['Transport'] : null;
    $service_info['SearchData']['DestinationType'] = isset($search_data['DestinationType']) ? $search_data['DestinationType'] : null;
    $service_info['SearchData']['DepCityCode'] = isset($search_data['DepCityCode']) ? $search_data['DepCityCode'] : null;
    $service_info['SearchData']['CheckIn'] = isset($search_data['CheckIn']) ? $search_data['CheckIn'] : null;
    $service_info['SearchData']['Adults'] = isset($search_data['Adults']) ? $search_data['Adults'] : null;
    $service_info['SearchData']['Children'] = isset($search_data['Children']) ? $search_data['Children'] : null;
    $service_info['SearchData']['Provider'] = isset($search_data['Provider']) ? $search_data['Provider'] : null;
    $service_info['SearchData']['ChildrenAge'] = isset($search_data['ChildrenAge']) ? $search_data['ChildrenAge'] : null;
    $service_info['SearchData']['ProductCode'] = trim($this->input->post('ProductCode'));
    $service_info['SearchData']['OfferId'] = $offer_id;
	
	$this->load->model('TravelFuse_model');
	$real_hotel = [];
	$hotel = [];
	if($type == 'circuit'){
		$service_info['SearchData']['TourCountryCode'] = isset($search_data['Destination']) ? $search_data['Destination'] : null;
		$search_d = array_diff_key($service_info['SearchData'], array_flip(['OfferId', 'ProductCode']));
		$hotels = $this->TravelFuse_model->tourOfferList($search_d,[], false, false, 'object');
		if(!$hotels){
			$this->addError("Oferta a expirat");
			return false;
		}
		foreach($hotels as $h){
			if($h->Id == $service_info['SearchData']['ProductCode']){
				$hotel = $h;
				break;
			}
		}
		if(!$hotel){
			$this->addError("Oferta a expirat");
			return false;
		}
		
		$this->load->model('Travelfuse/TravelFuseTours_model');
		$offer = $this->TravelFuse_model->tourOfferDetails($service_info['SearchData'], [], false, false, 'array');
	}
	if($type == 'charter'){
		$service_info['SearchData']['Destination'] = isset($search_data['Destination']) ? $search_data['Destination'] : null;
		$service_info['SearchData']['CheckOut'] = isset($search_data['CheckOut']) ? $search_data['CheckOut'] : null;
		$this->load->model('Travelfuse/TravelFuseHotels_model');
		$offer = $this->TravelFuse_model->charterOfferDetails($service_info['SearchData'], [], false, false, 'array');
	}
	if(!$offer){
		$this->addError("Oferta a expirat");
		return false;
	}
	$offer = array_shift($offer);
	if(!$offer){
		$this->addError("Oferta a expirat");
		return false;
	}
	$offer = $offer['Offers'][0] ?? null;
	if(!$offer){
		$this->addError("Oferta a expirat");
		return false;
	}
	
	// $this->addError("Oferta e buna");
	// return false;
	
	if($type == 'circuit'){
		$this->load->model('Travelfuse/TravelFuseTours_model');
		$hotels_overrides = $this->TravelFuseTours_model->getTravelfuseOverrides([$hotel->Id], []);
		if(isset($hotels_overrides[$hotel->Id])){
			$hotel_override = $hotels_overrides[$hotel->Id];
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
	}
	
	if($type == 'charter'){
		$hotel = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => (int)$service_info['SearchData']['ProductCode']]);
		if(!$hotel){
			$this->addError("Oferta a expirat");
			return false;
		}
		$hotel = array_shift($hotel);
		if(!$hotel){
			$this->addError("Oferta a expirat");
			return false;
		}
		$hotels_overrides = $this->TravelFuseHotels_model->getTravelfuseOverrides([$hotel->Id], []);
		if(isset($hotels_overrides[$hotel->Id])){
			$hotel_override = $hotels_overrides[$hotel->Id];
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
	}
	
	$service_info['price'] = $offer['Price'];
	$service_info['currency_code'] = $offer['Currency']['Code'] ?? null;
	$_POST['SearchId'] = $offer['SearchId'];
	
	$service_info['BookingData'] = [];
    $service_info['BookingData']['SearchId'] = trim($this->input->post('SearchId'));
	$service_info['BookingData']['Offers'] = [$offer_id];
    // $service_info['comment'] = trim($this->input->post('comment'));
	
	$billing_person = $this->input->post('billing_person');
	if(empty($billing_person) || !is_array($billing_person)) $billing_person = [];
    $service_rooms = $this->input->post('service_rooms'); // TODO - person details per room
    $service_info['BookingData'] = [
		'BookingInfo' => $service_rooms,
		'Person' => [
			'Name' => $billing_person['Name'] ?? null,
			'Firstname' => $billing_person['Firstname'] ?? null,
			'Email' => $billing_person['Email'] ?? null,
			'Phone' => $billing_person['Phone'] ?? null,
			'UniqueIdentifier' => $billing_person['UniqueIdentifier'] ?? null,
			'IdentityCardSeries' => $billing_person['IdentityCardSeries'] ?? null,
			'IdentityCardNumber' => $billing_person['IdentityCardNumber'] ?? null,
			'Address' => [
				'City' => $billing_person['Address']['City'] ?? null,
				'Details' => $billing_person['Address']['Details'] ?? null,
			],
		],
		'Billing' => [
			'BillCompany' => filter_var($billing_person['BillCompany'] ?? null, FILTER_VALIDATE_BOOLEAN),
			'Company' => [
				'Name' => $billing_person['Company']['Name'] ?? null,
				'TaxIdentificationNo' => $billing_person['Company']['TaxIdentificationNo'] ?? null,
				'RegistrationNo' => $billing_person['Company']['RegistrationNo'] ?? null,
				'Bank' => $billing_person['Company']['Bank'] ?? null,
				'BankAccount' => $billing_person['Company']['BankAccount'] ?? null,
				'HeadOffice' => [
					'Details' => $billing_person['Company']['HeadOffice']['Details'] ?? null,
				],
			],
		],
	];
	
    $occupancy = $this->input->post('rooms_occupancy'); // TODO - json - to array
    $service_info['occupancy'] = $occupancy;
    $service_info['service_rooms'] = $service_rooms;
    $service_info['offer'] = $offer;
    $service_info['result'] = json_decode(json_encode($hotel), true);
	
	if(($service_info['price'] . $service_info['currency_code']) !== $this->input->post('expectedPrice')){
		$this->addError("Oferta a suferit modificari intre timp");
		return false;
	}
	
    $this->service_info = $service_info;
	// prd($this->service_info);
    return true;
  }
}