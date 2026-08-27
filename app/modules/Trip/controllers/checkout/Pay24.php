<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Pay24 extends MX_Controller {
  public function index() {
    /* $order_id = (int) $this->input->get('order_id');
    $ctrl = $this->input->get('ctrl');
    
    $should_url = site_url('trip/checkout/pay24?order_id=' . $order_id);
    $length = mb_strlen($should_url,'UTF-8');
    
    $signature = $length . $should_url;
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('payment_gateways_settings',null,array(
      'pay24_secret_key'=>'',
    ));
    
    $should_ctrl = hash_hmac('md5',$signature,$settings['pay24_secret_key']);
    if($ctrl === $should_ctrl){
      $this->load->model('TripOrder_model');
      $this->db->where('IFNULL(`status`,0) = 0');
      $this->TripOrder_model->saveOrder(array('id'=>$order_id,'status'=>1,'message'=> 'Se asteapta confirmarea platii.'));
      $this->data['order_id'] = $order_id;
      return $this->theme->view('trip/checkout/success', $this->data, $this);
    }
    $this->addMessage('Semnatura primita de procesator nu a fost validata', 'error');
    $this->saveMessagesInSession();
    return $this->theme->view('trip/checkout/failure', $this->data, $this); */
  }
  public function idn($order_id) {
    /* // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . $order_id);
    $this->load->model('TripOrder_model');
	$this->load->model('TripCoupon_model');
	$this->load->model('TripOrderCoupon_model');
    $order = $this->TripOrder_model->getOrderById($order_id);
    // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($order));
    $gateway_data = json_decode($order->gateway_data);
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('payment_gateways_settings',null,array(
      'pay24_secret_key'=>'',
      'pay24_merchant_id'=>'',
    ));
    $post = array(
      'MERCHANT' => $settings['pay24_merchant_id'],
      'ORDER_REF' => $order->gateway_ref,
      'ORDER_AMOUNT' => array_sum($gateway_data->IPN_PRICE),
      'ORDER_CURRENCY' => $gateway_data->CURRENCY,
      'IDN_DATE' => date('Y-m-d H:i:s'),
    );
    // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($post));
    $signature='';
    foreach($post as $k=>$v){
      $signature .= mb_strlen($v,'UTF-8');
      $signature .= $v;
    }
    $post['ORDER_HASH'] = hash_hmac('md5',$signature,$settings['pay24_secret_key']);
    $pay24_status = (int)$this->Options_model->get('payment_gateways_status','pay24',0);
    
    $url = 'https://secure.pay24.ro/';
    if($pay24_status < 0){
      $url = 'https://sandbox.pay24.ro/';
    }
    $url .= 'order/idn.php';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    $post_string = http_build_query($post);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/secure.pay24.ro.crt');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $arr = explode('|', $result);
    // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . $result);
    if(in_array((int)$arr[1], array(1,7))){
      if(in_array((int)$order->status, array(1))){
        if($order->provider == 'paralela45'){
          $this->load->model('Paralela45_model');
          if($order->type == 'strainatate'){
            $this->load->model('Paralela45/Paralela45_Strainatate_model');
            $paralela45_model = $this->Paralela45_Strainatate_model;
          } elseif($order->type == 'circuit'){
            $this->load->model('Paralela45/Paralela45_Circuit_model');
            $paralela45_model = $this->Paralela45_Circuit_model;
          } 
          $booked = false;
          try{
            $paralela45_model->bookServices($order);
            $booked = true;
            $message = 'Confirmat Plata : ' . $arr[2];
          } catch(Exception $e){
            $message = 'Eroare booking : ' . $e->getMessage();
          }
          // TODO: book item
          $data = array('id'=>$order_id,'status'=>$booked ? 2 : -1,'message'=> $message);
		  if($booked){
			$coupons = $this->TripOrderCoupon_model->getOrderCouponsByOrderId($order_id);
			foreach($coupons as $coupon){
				$this->TripCoupon_model->useCoupon($coupon->code);
			}
		  }
          $this->TripOrder_model->saveOrder($data);
          if(!$booked){
            return;
          }
          Modules :: run ('Mailer/checkout_auto', array('order_id'=>$order_id));
        } else {
          $trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
          if(!$trip_order){
            $message = $this->getTripError('Trip Error: Nu s-a putut prelua rezervarea dupa plata');
            $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
            // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
            $this->TripOrder_model->saveOrder($data);
            return;
          }
          if($trip_order->Status == 2){
            $message = 'Rezervarea a esuat dupa plata si a fost anulata: ';
            $service_errors = array();
            foreach($trip_order->Services as $service){
              if($service->ErrorStatus){
                $service_errors[] = $service->ErrorMessage;
              }
            }
            $message .= implode('; ', $service_errors);
            $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
            // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
            $this->TripOrder_model->saveOrder($data);
            return;
          }
		  
		  if(empty($trip_order->Payment) || empty($trip_order->Payment->Status) || $trip_order->Payment->Status != 1){
			  $response = $this->TripOrder_model->setTripPaymentStatus($trip_order->Id, 1);
			  if(!$response){
				$message = $this->getTripError('Nu a putut fi stabilit statusul platii dupa plata');
				$data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
				// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
				$this->TripOrder_model->saveOrder($data);
				return;
			  }
		  }
          if(!config_item('trip_no_booking')){
            $separate_booking = false;
            foreach($trip_order->Services as $service){
              if($service->Type == 'flight'){
                $separate_booking = true;
                break;
              }
            }
            if($separate_booking){
              foreach($trip_order->Services as $service){
                if($service->Type === 'flight'){
                  continue;
                }
                $booking_response = $this->TripOrder_model->bookTripService($trip_order->Id, $service->Id);
                if(!$booking_response){
                  $message = $this->getTripError('Trip Error: Nu s-a putut rezerva serviciul de tip ' . $service->Type . ' dupa plata');
                  $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
                  // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
                  $this->TripOrder_model->saveOrder($data);
                  return;
                }
              }
            } else {
              $booking_response = $this->TripOrder_model->bookAllTripServices($trip_order->Id);
              if(!$booking_response){
                $message = $this->getTripError('Trip Error: Nu s-a putut efectua rezervarea dupa plata');
                $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
                // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
                $this->TripOrder_model->saveOrder($data);
                return;
              }
            }
            $trip_order = $this->TripOrder_model->getTripOrder($trip_order->Id);
            if(!$trip_order){
              $message = $this->getTripError('Trip Error: Nu s-a putut prelua rezervarea dupa plata si rezervare');
              $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
              $this->TripOrder_model->saveOrder($data);
              // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
              return;
            }
            if($trip_order->Status == 2){
              $message = 'Rezervarea a esuat dupa plata si rezervare si a fost anulata: ';
              $service_errors = array();
              foreach($trip_order->Services as $service){
                if($service->ErrorStatus){
                  $service_errors[] = $service->ErrorMessage;
                }
              }
              $message .= implode('; ', $service_errors);
              $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
              // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
              $this->TripOrder_model->saveOrder($data);
              return;
            }
          }
          $message = 'Confirmat Plata : ' . $arr[2];
          $data = array('id'=>$order_id,'status'=>2,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
          // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
          $this->TripOrder_model->saveOrder($data);
		  if($booked){
			$coupons = $this->TripOrderCoupon_model->getOrderCouponsByOrderId($order_id);
			foreach($coupons as $coupon){
				$this->TripCoupon_model->useCoupon($coupon->code);
			}
		  }
          Modules :: run ('Mailer/checkout_auto', array('order_id'=>$order_id));
        }
      }
    } else {
      $message = 'Eroare Confirmare Plata : ' . $arr[2];
      $data = array('id'=>$order_id,'status'=>-1,'message'=> $message);
      // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
      $this->TripOrder_model->saveOrder($data);
    } */
  }
  public function ipn() {
    /* // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__);
    $post = $this->input->post();
    if(empty($post) || !isset($post['HASH'])){
      // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: Invalid request');
      echo 'invalid request';
      die;
    }
    $signature='';
    foreach($post as $k=>$val){
      if($k == 'HASH'){
        break;
      }
      foreach((array)$val as $v){
        $signature .= mb_strlen($v,'UTF-8');
        $signature .= $v;
      }
    }
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('payment_gateways_settings',null,array(
      'pay24_secret_key'=>'',
      'pay24_merchant_id'=>'',
    ));
    $hash = hash_hmac('md5',$signature,$settings['pay24_secret_key']);
    if($hash !== $this->input->post('HASH')){
      // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: Invalid hash');
      echo 'invalid hash';
      die;
    }
    
	$order_id = 'ipn_order_id-' . (int)$this->input->post('REFNOEXT');
    $key = $order_id;
	
	$maxAcquire = 1;
	$permissions =0666;
	$autoRelease = 1;
	$semaphore = sem_get(crc32($key), $maxAcquire, $permissions, $autoRelease);
	sem_acquire($semaphore);
    // TEST ?
    // REVERSED , REFUND ?
    $orders = array();
    $gateway_status = $this->input->post('ORDERSTATUS');
    if(in_array($gateway_status, array('PAYMENT_AUTHORIZED','PAYMENT_RECEIVED','COMPLETE', 'REVERSED', 'REFUND' ))){
      $order_id = (int)$this->input->post('REFNOEXT');
      $status = 1;
      if(in_array($gateway_status, array('REVERSED', 'REFUND' ))){
        $status = 3;
      }
      $this->load->model('TripOrder_model');
      $this->db->where('payment_gateway', 'pay24');
      $order = $this->TripOrder_model->getOrderById($order_id);
      $gateway_ref = $this->input->post('REFNO');
      
      if($order){
        $orders[]= $order;
        $old_status = (int)$order->status;
        if($status == 1){
          if($old_status != 1){
            echo 'order not pending';
            die;
          }
        }
        $data = array(
          'id'=>$order->id,
          'status'=>$status,
          'gateway_ref'=>$gateway_ref,
          'gateway_status'=>$gateway_status,
          'gateway_data'=>json_encode($post),
          'message'=> 'Primit IPN valid: ' . $gateway_status,
        );
        // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
        $this->TripOrder_model->saveOrder($data);
      } else {
        echo 'invalid order';
        die;
      }
    }
    $signature='';
    $signature .= mb_strlen($post['IPN_PID'][0],'UTF-8');
    $signature .= $post['IPN_PID'][0];
    $signature .= mb_strlen($post['IPN_PNAME'][0],'UTF-8');
    $signature .= $post['IPN_PNAME'][0];
    $signature .= mb_strlen($post['IPN_DATE'],'UTF-8');
    $signature .= $post['IPN_DATE'];
    $date = date('YmdHis');
    $signature .= mb_strlen($date,'UTF-8');
    $signature .= $date;
    
    $output_hash = hash_hmac('md5',$signature,$settings['pay24_secret_key']);
    echo '<EPAYMENT>' . $date . '|' . $output_hash . '</EPAYMENT>';
    foreach($orders as $order){
      $this->idn($order->id);
    }
	sem_release($semaphore);
    die; */
  }
  
  public function checkout($type,$processor_data) {
    /* $this->data['type'] = $type;
    $this->data['gateway'] = 'pay24';
    $this->data['processor_data'] = $processor_data;
    $this->data['pay24_payment_method'] = trim($this->input->post('pay24_payment_method'));
    $this->theme->view('trip/checkout/online', $this->data, $this); */
    return true;
  }
  public function validate($type){
    $allowed_payment_methods = $this->Options_model->getKeys('pay24_payment_method');
    if(!$allowed_payment_methods || !is_array($allowed_payment_methods)){
      $allowed_payment_methods = array();
    }
    // $this->form_validation->set_rules('pay24_payment_method', 'Optiune plata pay24', 'trim|required' . ($allowed_payment_methods ? '|in_list[' . implode(',', $allowed_payment_methods) . ']' : ''),array(
      // 'in_list' => 'Alegere invalida',
    // ));
  }
}