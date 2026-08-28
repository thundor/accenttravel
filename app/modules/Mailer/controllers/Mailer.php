<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Mailer extends MX_Controller {
	
  public function logStuff() {
	  $cdate = date('YmdHis');
	  $response_dir_path = APPPATH.'logs/mailer/';
	  if(!is_dir($response_dir_path)){
		mkdir($response_dir_path,0777,true);
	  }
	  $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	  $ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	  $content = file_get_contents('php://input');
	  $data = [
		'url' => $url,
		'ip' => $ip,
		'server' => $_SERVER,
		'get' => $_GET,
		'post' => $_POST,
		'headers' => getallheaders(),
		'sent_headers' => headers_list(),
		'content' => $content,
	  ];
	  file_put_contents($response_dir_path . $cdate . '.json',json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
  }
  private function setData($key, $value, $overwrite = false) {
    if(!$overwrite && isset($this->data[$key])){
      return;
    }
    $this->data[$key] = &$value;
  }
  private function getData($key, &$default_value = null) {
    return isset($this->data[$key]) ? $this->data[$key] : $default_value;
  }
  protected function common() {
    $this->theme->set_sublayout('email/default/index');
    $this->setData('date', date('d.m.Y H:i:s'));
    $this->setData('site_url', site_url(''));
  }
  protected function ticketing_common() {
    $this->setData('ticket_id', 1);
    $this->setData('reservation_id', 1);
    $this->setData('ticket_url', site_url('backend/ticketing/edit?id=1'));
    $this->setData('assigned_name', $this->user->firstname . ' ' . $this->user->lastname);
    $this->common();
  }
  protected function ticketing_add() {
    $this->ticketing_common();
    $this->setData('view', 'email/ticketing/add');
    return $this->send_email();
  }
  protected function ticketing_edit() {
    $this->ticketing_common();
    $this->setData('view', 'email/ticketing/edit');
    return $this->send_email();
  }
  protected function account_common() {
    $user_id = $this->getData('user_id');
    $user = $this->getData('user');
    if(!$user || !is_object($user)){
      if(!$user_id){
        throw new Exception('Eroare la trimiterea emailului: Lipseste ID utilizator.');
        return false;
      }
      $this->load->model('Account_model');
      $user = $this->Account_model->getAccountById($user_id);
      if(!$user){
        throw new Exception('Eroare la trimiterea emailului: Utilizatorul nu a fost gasit.');
        return false;
      }
      $this->setData('user', $user, true);
      $this->setData('user_id', $user->id, true);
    }
    if(!$user || !is_object($user) || empty($user->id)){
      throw new Exception('Eroare la trimiterea emailului: Lipsesc informatiile utilizatorului');
      return false;
    }
    $this->setData('to', $this->data['user']->email);
    $this->setData('from_email', 'marketing@accenttravel.ro');
    $default_bcc = array(
      'suport@lisal.ro',
      'marketing@accenttravel.ro',
    );
	// if(config_item('trip_24_pay')){
		// $default_bcc = array(
		  // 'tudor.chirvasa@lisal.ro',
		// );
	// }
    $this->setData('bcc', $default_bcc);
    $this->common();
  }
  protected function account_register() {
    $this->account_common();
    
    $user = $this->getData('user');
    $this->setData('username', $user->username);
    $this->setData('password', $user->password);
    
    $this->setData('subject', 'Contul a fost activat!');
    $this->setData('view', 'email/account/register');
    return $this->send_email();
  }
  protected function account_password() {
    $this->account_common();
    $reset_url = $this->getData('reset_url');
    // $this->setData('reset_url', site_url('/account/reset_password?hash=xzcv7122s'));
    if(!$reset_url){
      throw new Exception('Eroare la trimiterea emailului: Lipseste url resetare.');
      return false;
    }
    $user = $this->getData('user');
    $this->setData('username', $user->username);
    
    $username = $this->getData('username');
    if(!isset($username) || !strlen($username)){
      throw new Exception('Eroare la trimiterea emailului: Lipseste username.');
      return false;
    }
    
    $this->setData('subject', 'Solicitare resetare parola');
    $this->setData('view', 'email/account/password');
    
    return $this->send_email();
  }
  protected function checkout_common() {
    $order_id = $this->getData('order_id');
    $order = $this->getData('order');
    if(!$order || !is_object($order)){
      if(!$order_id){
        throw new Exception('Eroare la trimiterea emailului: Lipseste ID comanda.');
        return false;
      }
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($order_id);
      if(!$order){
        throw new Exception('Eroare la trimiterea emailului: Comanda nu a fost gasita.');
        return false;
      }
      $this->setData('order', $order, true);
      $this->setData('order_id', $order->id, true);
    }
    $this->setData('to', $order->user_email);
    if($order->provider == 'trip'){
      if(!$order->trip_order_id){
        throw new Exception('Eroare la trimiterea emailului: Comanda nu a fost trimisa catre DCS.');
        return false;
      }
      if(empty($order->trip_order)){
        $trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
        if(!$trip_order){
          throw new Exception('Eroare la trimiterea emailului: ' . $this->getTripError('Comanda nu putut fi preluata.'));
          return false;
        }
        $order->trip_order = $trip_order;
      }
      if(empty($order->trip_order->Owner)){
        throw new Exception('Eroare la trimiterea emailului: Lipsesc informatiile utilizatorului');
        return false;
      }
      $this->setData('to', $order->trip_order->Owner->Email);
    }
    $this->setData('order', $order);
    if($order->status==2){
      $this->setData('subject', 'Rezervarea a fost confirmata! Voucher emis.');
    } elseif($order->status==3){
      $this->setData('subject', 'Rezervarea a fost anulata.');
    } elseif($order->status) {
      $this->setData('subject', 'Rezervarea a fost inregistrata! Aceasta va fi confirmata de un agent.');
    } else {
      $this->setData('subject', 'Rezervare nefinalizata');
    }
	
	$this->setData('has_invoice', 0);
	$this->setData('has_ticket', 0);
	
    if($order->provider == 'trip'){
      $this->attach_order_services_vouchers();
    }
    $this->common();
  }
  protected function checkout_hotel() {
    $this->setData('order_id', 66); // 52, 50, 49, 48 H
    $this->checkout_common();
    $order = $this->getData('order');
    if(count($order->trip_order->Services) !== 1 || $order->trip_order->Services[0]->Type !== 'hotel'){
      throw new Exception('Eroare la trimiterea emailului: Tip serviciu incorect');
      return false;
    }
    
    $this->setData('view', 'email/order/checkout/hotel');
    return $this->send_email();
  }
  protected function checkout_citybreak() {
    $this->setData('order_id', 60); // 39, 54, 53, 51 CB
    $this->checkout_common();
    $order = $this->getData('order');
    if(count($order->trip_order->Services) !== 2 || !in_array($order->trip_order->Services[0]->Type, array('hotel','flight')) || !in_array($order->trip_order->Services[1]->Type, array('hotel','flight'))){
      throw new Exception('Eroare la trimiterea emailului: Tip serviciu incorect');
      return false;
    }
    $this->setData('view', 'email/order/checkout/citybreak');
    return $this->send_email();
  }
  protected function checkout_flight() {
    $this->setData('order_id', 68); // 55, 43, 30, 29 F
    $this->checkout_common();
    $order = $this->getData('order');
    if(count($order->trip_order->Services) !== 1 || $order->trip_order->Services[0]->Type !== 'flight'){
      throw new Exception('Eroare la trimiterea emailului: Tip serviciu incorect');
      return false;
    }
    $this->setData('view', 'email/order/checkout/flight');
    return $this->send_email();
  }
  protected function checkout_package() {
    $this->setData('order_id', 47);  // 69, 57, 55, 47 P
    $this->checkout_common();
    $order = $this->getData('order');
    if(count($order->trip_order->Services) !== 1 || $order->trip_order->Services[0]->Type !== 'package'){
      throw new Exception('Eroare la trimiterea emailului: Tip serviciu incorect');
      return false;
    }
    $this->setData('view', 'email/order/checkout/package');
    return $this->send_email();
  }
  protected function checkout_custom() {
    $this->setData('order_id', 47);  // 69, 57, 55, 47 P
    $this->checkout_common();
    $this->setData('view', 'email/order/checkout/custom');
    return $this->send_email();
  }
  protected function attach_order_services_vouchers() {
    $order = $this->getData('order');
    if(!$order || empty($order->trip_order) || ($order->status != 2)){
      return;
    }
    if(!isset($this->data['attachments'])){
      $this->data['attachments'] = array();
    }
    $tmp_path = config_item('tmp_path');
    foreach($order->trip_order->Services as $service){
      $service_id = $service->Id;
      $service_type = $service->Type;
      $documents_response = $this->TripOrder_model->getDocuments($order->trip_order->Id, $service_id);
	  
	  if(empty($documents_response) || empty($documents_response->_embedded) || empty($documents_response->_embedded->documents)){
		  continue;
	  }
	  // if(!empty($_GET['testt'])){
		  // echo '<pre>';
		  // print_r($documents_response);
		  // die;
	  // }
      foreach($documents_response->_embedded->documents as $document){
        $document_id = $document->Id;
        $document_name = $document->Name;
        $document_response = $this->TripOrder_model->downloadDocument($order->trip_order->Id, $service_id, $document_id);
        file_put_contents($tmp_path . $document_name,$document_response);
        $this->data['attachments'][] = array(
          'path' => $tmp_path . $document_name,
          'name' => $service_type . '-' . $document_name,
          'delete' => true,
        );
      }
	  
	  if(($service->Type == 'flight')){
			$this->db->where_in('id', $order->id);
			$this->db->update('ac_trip_order', array(
				'ticket_retries' => $order->ticket_retries + 1,
			));
	  }
	  
	  if(($service->Type == 'flight') && !empty($documents_response->_embedded->documents)){
			$this->setData('has_ticket', 1, true);
			$this->db->where_in('id', $order->id);
			$this->db->update('ac_trip_order', array(
				'ticket_notified' => 1,
			));
	  }
    }
	
	$facturi_path = realpath(APPPATH . '../../facturi') . '/';
	if(!$facturi_path || !is_dir($facturi_path)){
		$facturi_path = false;
	}
	if($facturi_path && is_file($facturi_path . $order->id . '.pdf')){
		$this->setData('has_invoice', 1, true);
		$this->data['attachments'][] = array(
          'path' => $facturi_path . $order->id . '.pdf',
          'name' => 'Factura_' . $order->id . '.pdf',
        );
	}
	
	$bilete_path = realpath(APPPATH . '../../bilete') . '/';
	if(!$bilete_path || !is_dir($bilete_path)){
		$bilete_path = false;
	}
	if($bilete_path && is_file($bilete_path . $order->id . '.pdf')){
		$this->setData('has_ticket', 1, true);
		$this->data['attachments'][] = array(
          'path' => $bilete_path . $order->id . '.pdf',
          'name' => 'Bilet_' . $order->id . '.pdf',
        );
	}
  }
  protected function checkout_nefinalizat() {
    $this->setData('order_id', 47);
    $this->checkout_common();
    $order = $this->getData('order');
	if($order->payment_gateway == 'pay24'){
		// Pentru pay24 sa nu se trimita mail de checkout nefinalizat, convorm cerinta in data 2023-09-05 Fwd:FW: Rezervare nefinalizata
		return;
		$this->setData('to', '24pay@accenttravel.ro');
	} else {
		if(!empty($_GET['newux'])){
			$this->setData('to', 'tudor.chirvasa@lisal.ro');
		} else {
			$this->setData('to', 'vanzari@accenttravel.ro');
		}
	}
    if($order->provider == 'travelfuse'){
      $this->setData('view', 'email/order/checkout/travelfuse');
    } elseif($order->provider == 'paralela45'){
      $this->setData('view', 'email/order/checkout/paralela45');
    } elseif($order->provider == 'trip'){
      $service_types = array();
      foreach($order->trip_order->Services as $service){
        $service_types[] = $service->Type;
      }
      $total_service_types = count($service_types);
      $service_type = 'custom';
      if($total_service_types == 1){
        $service_type = $service_types[0];
      } elseif($total_service_types == 2){
        if(in_array('hotel', $service_types) && in_array('flight', $service_types) && $order->type == 'citybreak'){
          $service_type = 'citybreak';
        }
      }
      $this->setData('view', 'email/order/checkout/' . $service_type);
    }
    return $this->send_email();
  }
  protected function checkout_auto() {
    $this->setData('order_id', 47);
    $this->checkout_common();
    $order = $this->getData('order');
    if($order->provider == 'travelfuse'){
      $this->setData('view', 'email/order/checkout/travelfuse');
    } elseif($order->provider == 'paralela45'){
      $this->setData('view', 'email/order/checkout/paralela45');
    } elseif($order->provider == 'trip'){
      $service_types = array();
      foreach($order->trip_order->Services as $service){
        $service_types[] = $service->Type;
      }
      $total_service_types = count($service_types);
      $service_type = 'custom';
      if($total_service_types == 1){
        $service_type = $service_types[0];
      } elseif($total_service_types == 2){
        if(in_array('hotel', $service_types) && in_array('flight', $service_types) && $order->type == 'citybreak'){
          $service_type = 'citybreak';
        }
      }
      $this->setData('view', 'email/order/checkout/' . $service_type);
    }
    return $this->send_email();
  }
  protected function trip_notification() {
    $this->common();
    
    $this->setData('subject', 'Accent Travel & Events - Alertare pret oferta');
    $this->setData('view', 'email/trip/notification');
    // $this->setData('bcc', array(
      // 'alexandra.oprea@lisal.ro'
    // ));
    // $this->setData('prevent_send_email', true);
    return $this->send_email();
  }
  protected function epay_coupon_activate() {
    $this->common();
	$this->setData('response', array('- problema -'));
	$response = $this->getData('response');
	$coupon_name = @$response['NAME'];
	$coupon_ean = @$response['EAN'];
    $this->setData('to', 'giftcard@accenttravel.ro');
    $this->setData('subject', 'Cupon activat: ' . $coupon_name . ' ' . $coupon_ean);
    $this->setData('view', 'email/epay_coupon/activate');
    $this->setData('bcc', array(
      'suport@lisal.ro'
    ));
    // $this->setData('prevent_send_email', true);
    return $this->send_email();
  }
  protected function epay_coupon_activate2() {
    $this->common();
	$this->setData('response', array('- problema -'));
	$response = $this->getData('response');
	$coupon_name = @$response['NAME'];
	$coupon_ean = @$response['EAN'];
    $this->setData('to', 'tudor.chirvasa@lisal.ro');
    $this->setData('subject', 'Cupon activat: ' . $coupon_name . ' ' . $coupon_ean);
    $this->setData('view', 'email/epay_coupon/activate');
    $this->setData('bcc', array(
      'suport@lisal.ro'
    ));
    
    // $this->setData('prevent_send_email', true);
    // $this->setData('output_html', true);
    ini_set('display_errors', 1);
    ini_set('error_reporting', -1);
    return $this->send_email();
  }
  protected function epay_coupon_deactivate() {
    $this->common();
	$this->setData('response', array('- problema -'));
	$response = $this->getData('response');
	$coupon_ean = @$response['EAN'];
    $this->setData('to', 'giftcard@accenttravel.ro');
    $this->setData('subject', 'Cupon dezactivat: ' . $coupon_ean);
    $this->setData('view', 'email/epay_coupon/deactivate');
    $this->setData('bcc', array(
      'suport@lisal.ro'
    ));
    // $this->setData('prevent_send_email', true);
    return $this->send_email();
  }
  protected function trip_requestoffer() {
    $this->common();
    $this->setData('from_email', 'vanzari@accenttravel.ro');
    $this->setData('from_name', 'Accent Travel & Events');
    
    $this->setData('subject', 'Accent Travel & Events - Cerere oferta');
    $this->setData('view', 'email/trip/requestoffer');
    $this->setData('bcc', array(
      'suport@lisal.ro',
      'vanzari@accenttravel.ro'
    ));
    return $this->send_email();
  }
  protected function newsletter_subscribe() {
	  register_shutdown_function(array($this,'logStuff'));
	  
    $this->common();
    
    $this->setData('subject', 'Accent Travel & Events - Abonare newsletter');
    $this->setData('view', 'email/newsletter/subscribe');
    $this->setData('from_email', 'marketing@accenttravel.ro');
    $default_bcc = array(
      // 'alexandra.oprea@lisal.ro',
      'suport@lisal.ro',
      'marketing@accenttravel.ro',
    );
	// if(config_item('trip_24_pay')){
		// $default_bcc = array(
		  // 'tudor.chirvasa@lisal.ro',
		// );
	// }
    $this->setData('bcc', $default_bcc);
    return $this->send_email();
  }
  protected function newsletter_unsubscribe() {
    $this->common();
    
    $this->setData('subject', 'Accent Travel & Events - Dezabonare newsletter');
    $this->setData('view', 'email/newsletter/unsubscribe');
    $this->setData('from_email', 'marketing@accenttravel.ro');
    $default_bcc = array(
      // 'alexandra.oprea@lisal.ro',
      'suport@lisal.ro',
      'marketing@accenttravel.ro',
    );
	// if(config_item('trip_24_pay')){
		// $default_bcc = array(
		  // 'tudor.chirvasa@lisal.ro',
		// );
	// }
    $this->setData('bcc', $default_bcc);
    return $this->send_email();
  }
  protected function send_email(){
	$set_theme_default = false;
	$theme = $this->theme->config('theme');
	if('newux' == $theme){
		$this->theme->set_theme('accent');
		$set_theme_default = true;
	}
    $this->load->helper('url');
    $this->load->library('o365_mailer');
    $email_config = $this->o365_mailer->get_config();

    if(!$this->getData('from_email')){
      $this->setData('from_email', !empty($email_config['email_default_from']) ? $email_config['email_default_from'] : 'vanzari@accenttravel.ro');
    }
    if(!$this->getData('from_name')){
      $this->setData('from_name', !empty($email_config['email_default_from_name']) ? $email_config['email_default_from_name'] : 'Accent Travel & Events');
    }

    if(ENVIRONMENT == 'production'){
      $default_bcc = !empty($email_config['email_default_bcc']) ? (array)$email_config['email_default_bcc'] : array();
      $from_email = $this->getData('from_email');
      if($from_email == '24pay@accenttravel.ro'){
        $this->setData('to', '24pay@accenttravel.ro');
        $default_bcc[] = '24pay@accenttravel.ro';
      } elseif($from_email == 'marketing@accenttravel.ro'){
        $this->setData('to', 'marketing@accenttravel.ro');
        $default_bcc[] = 'marketing@accenttravel.ro';
      } else {
        $this->setData('to', 'vanzari@accenttravel.ro');
        $default_bcc[] = 'vanzari@accenttravel.ro';
      }
      $this->setData('bcc', $default_bcc);
    } else {
      $this->setData('to', !empty($email_config['email_dev_to']) ? $email_config['email_dev_to'] : 'tudor.chirvasa@lisal.ro');
      $this->setData('bcc', array());
    }

    $attachments = array();
    $this->setData('attachments', $attachments);
    $attachments = $this->getData('attachments');

    $view = $this->getData('view');
    $output = $this->output->get_output();
    if($view){
      $this->common();
      $this->theme->view($view, $this->data, $this);
    }
    $html_message = $this->output->get_output();

    $sent = true;
    $no_email = $this->getData('prevent_send_email');
    if(!$no_email){
      $sent = $this->dispatch_email($email_config, array(
        'from_email' => $this->getData('from_email'),
        'from_name' => $this->getData('from_name'),
        'to' => $this->getData('to'),
        'bcc' => $this->getData('bcc'),
        'subject' => $this->getData('subject'),
        'html' => $html_message,
        'attachments' => $attachments,
      ));
    }

    $output_html = $this->getData('output_html');
    if($output_html){
      echo $html_message;
      die;
    }
    foreach($attachments as $attachment){
      if(!is_array($attachment)){
        continue;
      }
      if(isset($attachment['delete']) && $attachment['delete']){
        unlink($attachment['path']);
      }
    }
    $this->output->set_output($output);

	if($set_theme_default){
	  $this->theme->set_theme($theme);
	}
	if(!empty($_GET['testmail'])){
		echo '<pre>';
    if(!empty($this->o365_mailer)){
      print_r($this->o365_mailer);
    } else {
      print_r($this->email);
    }
		die;
	}
	$this->data = [];
    if(!empty($this->email)){
      $this->email->clear(true);
    }
    return $sent;
  }
  protected function dispatch_email($email_config, $mail) {
    $driver = !empty($email_config['email_driver']) ? $email_config['email_driver'] : 'o365';

    if(ENVIRONMENT != 'production'){
      log_message('info', 'Mailer dev redirect to ' . json_encode($mail['to']));
      return true;
    }

    if($driver === 'o365'){
      if(empty($this->o365_mailer)){
        $this->load->library('o365_mailer');
      }
      if($this->o365_mailer->is_configured()){
        return $this->o365_mailer->send($mail);
      }
      log_message('error', 'Mailer: Office365 neconfigurat — ' . $this->o365_mailer->get_last_error());
      if(!empty($email_config['smtp_accounts'][$mail['from_email']])){
        return $this->dispatch_smtp_email($email_config, $mail);
      }
      return false;
    }

    if($driver === 'smtp'){
      return $this->dispatch_smtp_email($email_config, $mail);
    }

    log_message('error', 'Mailer: driver email necunoscut — ' . $driver);
    return false;
  }
  protected function dispatch_smtp_email($email_config, $mail) {
    $this->load->helper('email');
    $this->load->library('email');

    $from_email = $mail['from_email'];
    $user = '';
    $pass = '';
    $smtp_accounts = !empty($email_config['smtp_accounts']) ? $email_config['smtp_accounts'] : array();
    if(!empty($smtp_accounts[$from_email]['user'])){
      $user = $smtp_accounts[$from_email]['user'];
      $pass = isset($smtp_accounts[$from_email]['pass']) ? $smtp_accounts[$from_email]['pass'] : '';
    } elseif(!empty($email_config['smtp_user'])){
      $user = $email_config['smtp_user'];
      $pass = isset($email_config['smtp_pass']) ? $email_config['smtp_pass'] : '';
    }

    $crypto = isset($email_config['smtp_crypto']) ? $email_config['smtp_crypto'] : 'tls';
    if($crypto === 'none'){
      $crypto = '';
    }

    $config = array(
      'protocol' => 'smtp',
      'mailtype' => 'html',
      'charset' => 'utf-8',
      'newline' => "\r\n",
      'crlf' => "\r\n",
      'useragent' => 'Accent Travel & Events',
      'smtp_host' => !empty($email_config['smtp_host']) ? $email_config['smtp_host'] : 'mail4.rodax.ro',
      'smtp_port' => !empty($email_config['smtp_port']) ? (int)$email_config['smtp_port'] : 587,
      'smtp_crypto' => $crypto,
      'smtp_user' => $user,
      'smtp_pass' => $pass,
    );

    $this->email->initialize($config);
    $this->email->from($mail['from_email'], $mail['from_name']);
    $this->email->to($mail['to']);
    if(!empty($mail['bcc'])){
      $this->email->bcc($mail['bcc']);
    }
    $this->email->subject($mail['subject']);

    foreach((array) $mail['attachments'] as $attachment){
      $file_path = $attachment;
      $file_name = null;
      if(is_array($attachment)){
        if(!isset($attachment['path'])){
          continue;
        }
        $file_path = $attachment['path'];
        if(isset($attachment['name'])){
          $file_name = $attachment['name'];
        }
      }
      $this->email->attach($file_path, '', $file_name);
    }

    $this->email->message($mail['html']);
    return $this->email->send();
  }
  function __call($method, $args){
    if($this->router->class == get_class($this)){
      throw new Exception("Direct access forbidden");
    }
    if (!method_exists($this, $method)) {
      throw new Exception("Unknown method [$method]");
    }
    $this->data = isset($args[0]) ? $args[0] : array();
    return call_user_func_array(
      array($this, $method),
      $args
    );
  }
}