<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Pay24 extends MX_Controller {
	
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
    $_GET['testmail'] = 1;
    Modules :: run ('Mailer/checkout_auto', array(
      'order_id'=>$order_id, 
      // 'from_email'=>'24pay@accenttravel.ro', 
      // 'from_name'=>'Vanzari', 
      // 'config'=>$config, 
      'to'=>'tchirvasa@gmail.com',
		'from_email' => '24pay@accenttravel.ro',
      'bcc'=>array(
        'oprea.alex@gmail.com',
      ), 
      // 'prevent_send_email'=>true,
      // 'output_html'=>true
    ));
  }
  
  
	public function shutdownOrderCron() {
		$retry_order_ids = $this->remaining_order_ids;
		if($retry_order_ids){
			foreach($retry_order_ids as $order_id => $retries){
				echo 'Retry ' . $order_id . '<br />';
				$this->db->where_in('id', $order_id);
				$this->db->update('ac_trip_order', array(
					'invoice_retries' => $retries + 1,
				));
			}
		}
	}
	private $remaining_order_ids = array();
	public function ordercron() {
        echo 'START ' . '<br />';
        // ini_set('display_errors', 1);
        // error_reporting(-1);
		if(php_sapi_name() == 'cli'){
			session_start();
			// session_id(md5('cli'));
		}
		$this->load->model('TripOrder_model');
		// echo 'test';
		// $this->db->where_in('trip_order_id', array($trip_order_id));
		$this->db->where_in('provider', array('trip'));
		$this->db->where('payment_gateway', 'pay24');
		// $this->db->where_in('invoice_notified', array(0));
		$this->db->where("(`invoice_notified` = 0 OR (`type` IN ('flight', 'citybreak') AND `ticket_notified` = 0))");
		$this->db->where_in('status', array(2));
		$this->db->where('`invoice_retries` >= 0');
		$this->db->where('`invoice_retries` < 200');
		$this->db->where("`time_created` >= DATE_SUB('" .  date('Y-m-d H:i:s') . "', INTERVAL 12 HOUR)");
		// data curenta trebuie sa fie > time_created + ((1 + invoice_retries) * 5 minute)
		$this->db->where("'" .  date('Y-m-d H:i:s') . "' >= DATE_ADD(`time_created`, INTERVAL (6 * (1 + invoice_retries)) MINUTE)");
		
		// php /var/www/html/www_accenttravel_ro/index.php notifications cron >/dev/null 2>&1
		
		// echo 'test';
		// die;
		// $this->db->where_in('invoice_notified', array(0));
		
		$sort_order = 'asc';
		$sort_by = 'trip_order_id';

		$this->db->order_by($sort_by, $sort_order);
// ini_set('error_reporting', -1);
// ini_set('display_errors', 1);
		$q = $this->db->get('ac_trip_order', 5, 0);
		
		// echo '<pre>';
		// print_r($this->db);
		// die;
		$orders = $q->result();
		$order_ids = array_map(function($order){return $order->id; }, $orders);
		if(!$order_ids) {
            echo 'NOTHING ' . '<br />';
            return;
        }
		
		$this->db->where_in('id', $order_ids);
		$this->db->update('ac_trip_order', array(
			'invoice_retries' => -1
		));
		
		$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		
		// $retry_order_ids = [];
		$report_problem_mail_send = [];
		$non_initial_mail_sent = [];
		foreach($orders as $order){
			$this->remaining_order_ids[$order->id] = $order->invoice_retries;
            if(!$order->initial_mail_sent){
                $non_initial_mail_sent[] = $order->id;
            }
            if(2 == $order->ticket_retries && !$order->ticket_notified){
                $report_problem_mail_send[$order->id] = 'bilete';
            } elseif(2 == $order->invoice_retries && !$order->invoice_notified){
                $report_problem_mail_send[$order->id] = 'factura';
            }
		}
		
		register_shutdown_function(array($this,'shutdownOrderCron'));
		
		
		// print_r($orders);
		foreach($orders as $order){
			echo 'CHECKING ' . $order->id . '<br />';
			$invoice = false;
			if($facturi_path && is_file($facturi_path . $order->id . '.pdf')){
				$invoice = true;
			}
			// if($invoice){
			if(false){
				$this->db->where_in('id', $order->id);
				$this->db->update('ac_trip_order', array(
					'invoice_notified' => 1,
					'invoice_retries' => $order->invoice_retries + 1,
				));
				unset($this->remaining_order_ids[$order->id]);
				echo 'Update ok ' . $order->id . '<br />';
				
			} else {
				try{
					$trip_order_invoices = $this->TripOrder_model->getOrderInvoice($order->trip_order_id);
				} catch(Exception $e){
					echo 'Exception: ' . $e->getMessage();
				}
				// echo '<pre>';
				// var_dump($trip_order_invoices);
				// die;
				$document_id = null;
				if($trip_order_invoices && !empty($trip_order_invoices->_embedded) && !empty($trip_order_invoices->_embedded->invoice)){
					$document_ids = array();
					foreach($trip_order_invoices->_embedded->invoice as $invoice){
						if(!empty($invoice->Documents)){
							foreach($invoice->Documents as $document){
								$document_ids[] = $document->DocId;
							}
						}
					}
					sort($document_ids);
					$document_id = array_pop($document_ids);
					
				}
				$trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
				
				$ticket_id = null;
				foreach($trip_order->Services as $service){
					$documents_response = $this->TripOrder_model->getDocuments($trip_order->Id, $service->Id);
					
					if(empty($documents_response) || empty($documents_response->_embedded) || empty($documents_response->_embedded->documents)){
						  continue;
					}
					$ticket_id = 1;
				}
				
				if($order->ticket_notified){
					if(!$document_id){
						continue;
					}
				}
				
				if($document_id || $ticket_id){
					if($document_id){
						$trip_order_invoice = $this->TripOrder_model->getOrderDocument($order->trip_order_id,$document_id);
						if($trip_order_invoice){
							$file_deposit_path = $facturi_path . $order->id . '.pdf';
							
							file_put_contents($file_deposit_path, $trip_order_invoice);
							
							$this->db->where_in('id', $order->id);
							$this->db->update('ac_trip_order', array(
								'invoice_notified' => 1,
								'invoice_retries' => $order->invoice_retries + 1,
							));
						} else {
							if($order->ticket_notified){
								continue;
							}
						}
					}
					unset($this->remaining_order_ids[$order->id]);
					echo 'Sent mail ' . $order->id . '<br />';
					Modules :: run ('Mailer/checkout_auto', array(
						'subject'=>'Rezervare confirmata - documente emise',
						'from_email'=>'24pay@accenttravel.ro', 
						// 'to'=>'tchirvasa@gmail.com',
						// 'prevent_send_email'=>1,
						// 'output_html'=>1,
						'order_id'=>$order->id,
					));
					$this->db->where_in('id', $order->id);
					$this->db->update('ac_trip_order', array(
                        'initial_mail_sent' => 1,
						'invoice_retries' => $order->invoice_retries + 1,
					));
                    $order = $this->TripOrder_model->getOrderById($order->id);
                    if($order->ticket_notified){
                        if(isset($report_problem_mail_send[$order->id])){
                            $report_problem_mail_send[$order->id] = array_diff($report_problem_mail_send[$order->id], ['bilete']);
                        }
                    }
					// die;
					continue;
				}
				
				// $retry_order_ids[$order->id] = $order->invoice_retries;
			}
		}
		/* if($retry_order_ids){
			foreach($retry_order_ids as $order_id => $retries){
				echo 'Retry ' . $order->id . '<br />';
				$this->db->where_in('id', $order_id);
				$this->db->update('ac_trip_order', array(
					'invoice_retries' => $retries + 1,
				));
			}
		} */
        foreach($orders as $order){
            if(!isset($this->remaining_order_ids[$order->id])) continue;
            if(!in_array($order->id, $non_initial_mail_sent)) continue;
            $this->db->where_in('id', $order->id);
            $this->db->update('ac_trip_order', array(
                'initial_mail_sent' => 1,
            ));
            
            echo 'Sent mail - non-invoice ' . $order->id . '<br />';
            Modules :: run ('Mailer/checkout_auto', array(
                'subject'=>'Rezervare confirmata',
                'from_email'=>'24pay@accenttravel.ro', 
                // 'to'=>'tchirvasa@gmail.com',
                // 'prevent_send_email'=>1,
                // 'output_html'=>1,
                'order_id'=>$order->id,
            ));
        }
        foreach($orders as $order){
            if(!isset($this->remaining_order_ids[$order->id])) continue;
            if(empty($report_problem_mail_send[$order->id])) continue;
            
            echo 'Sent mail - problem ' . $order->id . '<br />';
            Modules :: run ('Mailer/checkout_auto', array(
                'subject'=>'Rezervare confirmata - problema preluare ' . implode(',', $report_problem_mail_send[$order->id]),
                'from_email'=>'24pay@accenttravel.ro', 
                'to'=>'24pay@accenttravel.ro', 
                // 'to'=>'tchirvasa@gmail.com',
                // 'prevent_send_email'=>1,
                // 'output_html'=>1,
                'order_id'=>$order->id,
            ));
        }
		echo 'DONE';
		// echo '<pre>';
		// print_r($orders);
		die;
	}
	public function test($subview, $subflight='') {
		// echo '<pre>';
		// print_r($this->theme->theme_path . '/test/flight_data.json');
		// die;
		$this->data = array(
			'homeview' => 'test', 
			'subview' => 'flight_' . $subview, 
			'flight_data' => file_get_contents($this->theme->theme_path . '/test/flight_data' . $subflight . '.json'),
		);
		$this->theme->view('test', $this->data, $this);
	}
	private $override_input;
	public function test_ipn2($id = null) {
		$this->load->library('encryption');
		$order_id = $id;
		$order_id_hashed = $this->encryption->encrypt($order_id);
		echo $order_id_hashed; die;
	}
	public function test_ipn($id = null) {
		$this->load->library('encryption');
		$order_id = '482';
		$order_id_hashed = $this->encryption->encrypt($order_id);
		// echo '<pre>';
		// var_dump($order_id);
		// print_r($order_id_hashed);
		// echo PHP_EOL;
			
		$this->override_input = '{"order_id": "ac786de54d502c7f44a1c7d9d370f0c3ed9d04c25d9bb3f58440a756472133267f5130d945535b05008b8a89fea2d0e8bf1d74a0f2e3db3ae826447195965519$1W76Qfks4tK+fybyEhYlB5yCwhEzNiRVTkm8Vw9Ra8=", "payment_ok": true ,"date":"2023-04-12T11:01:17.993Z" }';
		$this->ipn();
	}
	public function ipn($id = null) {
		// ignore_user_abort(true);
		$cdate = date('YmdHis');
		$response_dir_path = APPPATH.'logs/pay24/' . $cdate . '/';
		$content = $this->override_input;
		if(!isset($this->override_input)){
			$ip = '';
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$content = file_get_contents('php://input');
			if(!is_dir($response_dir_path)){
			  mkdir($response_dir_path,0777,true);
			}
			file_put_contents($response_dir_path . 'server.json',json_encode($_SERVER, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
			file_put_contents($response_dir_path . 'post.json',json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
			file_put_contents($response_dir_path . 'headers.json',json_encode(getallheaders(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
			file_put_contents($response_dir_path . 'get.json',json_encode($_GET, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
			file_put_contents($response_dir_path . 'ip.txt',$ip, FILE_APPEND);
			file_put_contents($response_dir_path . 'input.xml',$content, FILE_APPEND);
		}
		
		$content = str_replace('True ,', 'true, ', $content);
		$content = str_replace('False ,', 'false, ', $content);
		
		$obj = json_decode($content, true);
		
		$error = null;
		$error_code = 401;
		$error_title = null;
		$allow_booking = true;
		$key = false;
		for(;;){
			if(!$obj){
				$error_code = 403;
				$error = 'Object could not be decoded';
				break;
			}
			if(empty($obj['order_id'])){
				$error_code = 406;
				$error = 'Required parameter missing or contains invalid data (oid)';
				break;
			}
			if(!isset($obj['amount'])){
				$error_code = 406;
				$error = 'Required parameter missing or contains invalid data (amount)';
				break;
			}
			if(!isset($obj['payment_ok'])){
				$error_code = 406;
				$error = 'Required parameter missing or contains invalid data (pok)';
				break;
			}
			if(!isset($obj['date'])){
				$error_code = 406;
				$error = 'Required parameter missing or contains invalid data (d)';
				break;
			}
			$order_id_hashed = $obj['order_id'];
			$this->load->library('encryption');
			$order_id_hashed = preg_replace('/\$/','/', $order_id_hashed);
			$order_id = $this->encryption->decrypt($order_id_hashed);
			
			if(!$order_id || !is_numeric($order_id) || ('' . intval($order_id) !== '' . $order_id)){
				$error_code = 417;
				$error = 'OID could not be determined';
				break;
			}
			$d =@ DateTime::createFromFormat('Y-m-d\TH:i:s.v\Z', $obj['date']);
			if(!$d || ('' . $d->format('Y-m-d\TH:i:s.v\Z') !== '' . $obj['date'])){
				$error_code = 417;
				$error = 'D could not be determined ' . $obj['date'];
				break;
			}
			if(!(is_bool($obj['payment_ok']) || is_numeric($obj['payment_ok']))){
				$error_code = 417;
				$error = 'POK could not be determined';
				break;
			}
			$this->load->model('TripOrder_model');
			$this->load->model('TripCoupon_model');
			$this->load->model('TripOrderCoupon_model');
			
			$key = $order_id;
			FLocker::acquire($key);
			
			$this->db->where('payment_gateway', 'pay24');
			$order = $this->TripOrder_model->getOrderById($order_id);
			if(!$order){
				$error_code = 404;
				$error = 'Order not found';
				break;
			}
			
			if(floatval($obj['amount']) < floatval($order->amount)){
				$error_code = 407;
				$error = 'Amount paid is less than minimum amount expected (Expected at least: ' . $order->amount . ', Got: ' . $obj['amount'] . ')';
				break;
			}
			
			if(!isset($this->override_input)){
				file_put_contents($response_dir_path . 'order_id.txt', $order_id, FILE_APPEND);
				file_put_contents($response_dir_path . 'order.json', json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
			}
			$status = 1;
			if(empty($obj['payment_ok'])){
				$status = 3;
			}
			$old_status = (int)$order->status;
			if(!in_array($old_status, array(0,1))){ // Draft, Pending
				$error_code = 412;
				$error = 'Order status is not expecting changes';
				break;
			}
			$data = array(
			  'id'=>$order->id,
			  'status'=>$status,
			  'gateway_ref'=>$order_id_hashed,
			  'gateway_status'=>$gateway_status,
			  'gateway_data'=>$content,
			  'message'=> 'Primit IPN valid: ' . $gateway_status,
			);
			// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
			$this->TripOrder_model->saveOrder($data);
			
			if($status == 1) {
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
					  if($allow_booking){
						$paralela45_model->bookServices($order);
					  }
					$booked = true;
					$message = 'Confirmat Plata';
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
					$error_code = 412;
					$error = 'Could not book order (100)';
					break;
				  }
				  // Modules :: run ('Mailer/checkout_auto', array(
					// 'order_id'=>$order_id,
					// 'from_email' => '24pay@accenttravel.ro'));
				} else {
				  $trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
				  if(!$trip_order){
					$message = $this->getTripError('Trip Error: Nu s-a putut prelua rezervarea dupa plata');
					$data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
					// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
					$this->TripOrder_model->saveOrder($data);
					
					$error_code = 412;
					$error = 'Could not book order (200)';
					break;
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
					
					$error_code = 412;
					$error = 'Could not book order (300)';
					break;
				  }
				  if($allow_booking){
				  if(empty($trip_order->Payment) || empty($trip_order->Payment->Status) || $trip_order->Payment->Status != 1){
					  $response = $this->TripOrder_model->setTripPaymentStatus($trip_order->Id, 1);
					  if(!$response){
						$message = $this->getTripError('Nu a putut fi stabilit statusul platii dupa plata');
						$data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
						// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
						$this->TripOrder_model->saveOrder($data);
						
						$error_code = 412;
						$error = 'Could not book order (400)';
						break;
					  }
				  }
				  if(!config_item('trip_no_booking')){
					  $booked_cnt = 0;
					  foreach($trip_order->Services as $service){
						  if($service->Status == 1){
							  $booked_cnt ++;
						  }
					  }
					  
					  $already_booked = $booked_cnt == count($service->Services);
					  if(!$already_booked){
						$separate_booking = !!$booked_cnt;
						if(count($trip_order->Services) > 1){
							foreach($trip_order->Services as $service){
							  if($service->Type == 'flight'){
								$separate_booking = true;
								break;
							  }
							}
						}
						if($separate_booking){
						  foreach($trip_order->Services as $service){
							if($service->Type === 'flight'){
							  continue;
							}
							if($service->Status == 1) continue;
							$booking_response = $this->TripOrder_model->bookTripService($trip_order->Id, $service->Id);
							if(!$booking_response){
							  $message = $this->getTripError('Trip Error: Nu s-a putut rezerva serviciul de tip ' . $service->Type . ' dupa plata');
							  $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
							  // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
							  $this->TripOrder_model->saveOrder($data);
								$error_code = 412;
								$error = 'Could not book order (500)';
								break;
							}
						  }
						} else {
						  $booking_response = $this->TripOrder_model->bookAllTripServices($trip_order->Id);
						  if(!$booking_response){
							$message = $this->getTripError('Trip Error: Nu s-a putut efectua rezervarea dupa plata');
							$data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
							// log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
							$this->TripOrder_model->saveOrder($data);
							$error_code = 412;
							$error = 'Could not book order (600)';
							break;
						  }
						}
						$trip_order = $this->TripOrder_model->getTripOrder($trip_order->Id);
						if(!$trip_order){
						  $message = $this->getTripError('Trip Error: Nu s-a putut prelua rezervarea dupa plata si rezervare');
						  $data = array('id'=>$order_id,'status'=>-1,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
						  $this->TripOrder_model->saveOrder($data);
						  // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
						  
							$error_code = 412;
							$error = 'Could not book order (700)';
							break;
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
							$error_code = 412;
							$error = 'Could not book order (800)';
							break;
						}
					  }
				  }
				  }
				  $booked = true;
				  $message = 'Confirmat Plata';
				  $data = array('id'=>$order_id,'status'=>2,'message'=> $message, 'calls'=>json_encode($this->Trip_model->get_api()->calls));
				  // log_message('error', 'FILE: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD:' . __METHOD__ . ' MESSAGE: ' . json_encode($data));
				  $this->TripOrder_model->saveOrder($data);
				  if($booked){
					$coupons = $this->TripOrderCoupon_model->getOrderCouponsByOrderId($order_id);
					foreach($coupons as $coupon){
						$this->TripCoupon_model->useCoupon($coupon->code);
					}
				  }
				  // sleep(5);
				  /* if($order->trip_order_id && $order->provider == 'trip'){
					  $trip_order_invoices = $this->TripOrder_model->getOrderInvoice($order->trip_order_id);
					  if($trip_order_invoices && !empty($trip_order_invoices->_embedded) && !empty($trip_order_invoices->_embedded->invoice)){
						  $document_ids = array();
							foreach($trip_order_invoices->_embedded->invoice as $invoice){
								if(!empty($invoice->Documents)){
									foreach($invoice->Documents as $document){
										$document_ids[] = $document->DocId;
									}
								}
							}
							sort($document_ids);
							$document_id = array_pop($document_ids);
							
							if($document_id){
								$trip_order_invoice = $this->TripOrder_model->getOrderDocument($order->trip_order_id,$document_id);
								if($trip_order_invoice){
									$facturi_path = realpath(APPPATH . '../../facturi') . '/';
							
									$file_deposit_path = $facturi_path . $order_id . '.pdf';
									
									file_put_contents($file_deposit_path, $trip_order_invoice);
								}
							}
					  }
				  } */
				  // Modules :: run ('Mailer/checkout_auto', array(
					// 'order_id'=>$order_id,
					// 'from_email' => '24pay@accenttravel.ro'
				// ));
				}
			}
			
			break;
		}
		if($key){
			FLocker::release($key);
		}
		if($error){
			if(!$error_title)
			switch($error_code){
				case 401: $error_title = 'Unauthorized'; break;
				case 403: $error_title = 'Forbidden'; break;
				case 406: $error_title = 'Not Acceptable'; break;
				case 417: $error_title = 'Expectation Failed'; break;
				case 412: $error_title = 'Precondition Failed'; break;
			}
			
			header('HTTP/1.0 ' . $error_code . ' ' . $error_title);
			echo $error . ' LogReport:' . $cdate;
			if(!isset($this->override_input)){
				file_put_contents($response_dir_path . 'response.txt',$error . ' LogReport:' . $cdate, FILE_APPEND);
			}
			exit;
		}
		// https://accenttravel.ro/pay24/ipn
		echo 'Rezervare inregistrata cu succes';
		if(!isset($this->override_input)){
			file_put_contents($response_dir_path . 'response.txt','Rezervare inregistrata cu succes', FILE_APPEND);
		}
		exit;
	}
	public function test_order_details($id,$f1=null) {
		$this->load->library('encryption');
		$id = $this->encryption->encrypt($id);
		// echo $id; die;
		$id_hashed = preg_replace('/\//','$', $id);
		$this->redirect(site_url('pay24/order_details/' . urlencode($id_hashed)));
	}
	public function order_details($id,$f1=null) {
		$id_hashed = $id;
		$id = urldecode($id);
		$id = preg_replace('/\$/','/', $id);
		$this->load->library('encryption');
		$id = $this->encryption->decrypt($id);
		
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		if($order){
			if($order->provider == 'trip' && $order->trip_order_id){
				$trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
				if($trip_order){
					$order->trip_order = $trip_order;
				}
			}
		}
		$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		
		$invoice = false;
		if($facturi_path && is_file($facturi_path . $id . '.pdf')){
			$invoice = true;
		}
		if($f1 == 'download'){
			if($order){
				$args = func_get_args();
				
				if($args[2] == 'order'){
					$file_path = $facturi_path . $id . '.pdf';
				} else {
					$file_path = "/var/www/html/rest/app/clients/online/data/documents/" . $args[2] . '/' . $args[3];
				}
				if(is_file($file_path)){
					header('Content-Type: application/octet-stream');
		  
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file_path));
					
					readfile($file_path);
					exit;
				}
			}
			header('HTTP/1.0 404 Not found');
			echo 'The download was not found';
			exit;
		}
		
		if($order){
			unset($order->calls);
			unset($order->gateway_data);
			unset($order->gateway_ref);
			unset($order->gateway_status);
			unset($order->ip);
			
			$order->invoice = $invoice;
			$order->id_hashed = $id_hashed;
		}
		$this->data = array(
			'homeview' => 'order_summary', 
			'order' => $order,
			'invoice' => $invoice,
		);
		$this->theme->view('order_summary', $this->data, $this);
	}
	
	protected function checkout($type,$processor_data) {
	// $this->data['type'] = $type;
	// $this->data['gateway'] = 'pay24';
	// $this->data['processor_data'] = $processor_data;
	// $this->data['pay24_payment_method'] = trim($this->input->post('pay24_payment_method'));
	// $this->theme->view('trip/checkout/online', $this->data, $this);
	return true;
  }
  protected function validate($type){
	// $allowed_payment_methods = $this->Options_model->getKeys('pay24_payment_method');
	// if(!$allowed_payment_methods || !is_array($allowed_payment_methods)){
	  // $allowed_payment_methods = array();
	// }
	// $this->form_validation->set_rules('pay24_payment_method', 'Optiune plata pay24', 'trim|required' . ($allowed_payment_methods ? '|in_list[' . implode(',', $allowed_payment_methods) . ']' : ''),array(
	  // 'in_list' => 'Alegere invalida',
	// ));
  }
	public function index($a) {
		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		header('Content-Type: application/javascript');
		
		$provizoriu = false;
		if($ip == '82.76.174.47'){
			$_GET['testtudor'] = 1;
			$provizoriu = true;
		}
		if($provizoriu){
			if(file_exists($this->theme->theme_path . 'views/provizoriu/' . $a . '.php')){
				include $this->theme->theme_path . 'views/provizoriu/' . $a . '.php';
				return;
			}
		}
		include $this->theme->theme_path . 'views/' . $a . '.php';
	}
	
  public function accounts() {
	$this->load->model('Trip_model');
	$this->api = $this->Trip_model->get_api();
	
	
	$d = $this->api->apiCall('index.php/tbs/accounts');
	echo '<pre>';
	print_r($d);
	print_r($this->api->calls);
	die;

  }
  public function setStep() {
	$step = intval(isset($_POST['step']) ? $_POST['step'] : 0);
	$device = isset($_POST['device']) ? $_POST['device'] : null;
	$step_data = isset($_POST['step_data']) && is_string($_POST['step_data']) ? json_decode($_POST['step_data'], true) : [];
	if(!$step_data) $step_data = [];
	
	$this->load->model('TripLog_model');
	$step_string = '';
	switch($step){
		case 2: $step_string = 'results'; break;
		case 3: $step_string = 'details'; break;
		case 4: $step_string = 'passengers'; break;
		case 5: $step_string = 'billing'; break;
		case 6: $step_string = 'checkout'; break;
		case 7: $step_string = 'summary'; break;
		case 8: $step_string = 'pay'; break;
	}
	$this->TripLog_model->saveLog(['date_' . $step_string => date('Y-m-d H:i:s')]);
	if(isset($device)){
		$this->TripLog_model->saveLog(['device' => $device]);
		
	}
		// var_dump($_POST);
	if($step_data){
		if(isset($step_data['account'])){
			$customer_data = $step_data['account'];
			if($customer_data){
				$customer_data = json_encode($customer_data);
				$this->TripLog_model->saveLog(['customer_data' => $customer_data]);
			}
		}
		if(isset($step_data['billing'])){
			$billing_data = $step_data['billing'];
			if($billing_data){
				$billing_data = json_encode($billing_data);
				$this->TripLog_model->saveLog(['billing_data' => $billing_data]);
			}
		}
		if(isset($step_data['flight'])){
			$flight_data = $step_data['flight'];
			if($flight_data){
				$flight_data = json_encode($flight_data);
				$this->TripLog_model->saveLog(['flight_data' => $flight_data]);
			}
		}
		if(isset($step_data['checkout'])){
			$checkout_data = $step_data['checkout'];
			if($checkout_data){
				$checkout_data = json_encode($checkout_data);
				$this->TripLog_model->saveLog(['checkout_data' => $checkout_data]);
			}
		}
	}
  }
  public function setCust() {
	$this->load->model('TripLog_model');
	$customer_data = $_POST['account'] ?? null;
	if($customer_data){
		$customer_data = json_decode($customer_data);
		if($customer_data){
			$customer_data = json_encode($customer_data);
			$this->TripLog_model->saveLog(['customer_data' => $customer_data]);
		}
	}
  }
  public function create($type) {
  
	$this->load->model('TripLog_model');
	$this->TripLog_model->saveLog([
		'date_pay' => date('Y-m-d H:i:s'),
		'order_data' => json_encode($_POST),
	]);
	  // echo 'BLOCKED!';
	  // die;
	  if('flight' != $type){
		  echo '<pre>';
		  echo 'Should not enter here';
		  // print_r(debug_backtrace(false));
		  // $this->output();
		  die;
	  }
	  $this->load->model('Trip_model');
	  // echo '<pre>';
	  //var_dump($type);
	  // print_r($_POST);
	  // die;
	  $post = $_POST;
	  // array_walk_recursive($post, function(&$value){
		  // $value = urldecode($value);
	  // });
	  // echo htmlspecialchars(print_r($_POST,true));
	  // die;
	  $billing = $post['billing'] ?? array();
	  $_POST = array();
	  $_POST['tos'] = $post['tos'] ?? 1; 
	  $_POST['tpc'] = $post['tpc'] ?? 1; 
	  $_POST['invoice'] = $billing['invoice'] ?? 'pf';
	  $_POST['create_account'] = $billing['create_account'] ?? 0; // TODO
	  $_POST['password'] = $billing['password'] ?? null; // TODO
	  
	  $_POST['contact_country'] = $billing['country'] ?? '';
	  $_POST['contact_city'] = $billing['city'] ?? '';
	  $_POST['contact_phone'] = $billing['phone_prefix'] . ' ' . $billing['phone'] ?? '';
	  $_POST['contact_phone_prefix'] = $billing['phone_prefix_country'] ?? '';
	  $_POST['contact_email'] = $billing['email'] ?? '';
	  $_POST['contact_firstname'] = $billing['firstname'] ?? '';
	  $_POST['contact_lastname'] = $billing['lastname'] ?? '';
	  $_POST['contact_street'] = $billing['street'] ?? '';
	  $_POST['contact_street_no'] = $billing['street_no'] ?? '';
	  $_POST['contact_postal_code'] = $billing['postal_code'] ?? '';
	  $_POST['contact_bank'] = $billing['bank'] ?? '';
	  $_POST['contact_bank'] = $_POST['contact_bank'] ? $_POST['contact_bank'] : '-';
	  $_POST['contact_iban'] = $billing['iban'] ?? '';
	  $_POST['contact_iban'] = $_POST['contact_iban'] ? $_POST['contact_iban'] : '-';
	  $_POST['contact_cui'] = $billing['cui'] ?? '';
	  $_POST['contact_company_name'] = $billing['company'] ?? '';
	  $_POST['contact_regcom'] = $billing['regcom'] ?? '';
	  $_POST['contact_regcom'] = $_POST['contact_regcom'] ? $_POST['contact_regcom'] : '-';
	  // $_POST['contact_address'] = $billing['address'] ?? ''; // TODO
	  
	  $_POST['payment_method'] = 'online';
	  $_POST['payment_gateway'] = 'pay24';
	  $_POST['gateway'] = 'pay24';
	  
	  if($type == 'flight'){
		  $flight = $post['flight'] ?? array();
		  
		  $_POST['expectedFlightPrice'] = number_format(($flight['expected_price'] ?? ($flight['price'] ?? null)),2,'.','') . ($flight['currency'] ?? null);
		  $_POST['itinerary_code'] = $flight['itinerary_code'] ?? null;
		  $_POST['flight_code'] = $flight['code'] ?? null;
		  $_POST['upsellCode'] = $flight['upsellCode'] ?? null;
		  
		  $_POST['paidSeats'] = $flight['paidSeats'] ?? null;
		  $_POST['optionalServices'] = $flight['optionalServices'] ?? null;
		  $_POST['insurance_travel'] = null; // TODO
		  $_POST['insurance_storno'] = null; // TODO
		  
		  $_POST['passenger'] = null; // TODO
		  $_POST['preferredSeats'] = null; // TODO
		  
		  foreach($flight['passenger'] as $ptc => $ptc_passengers){
			foreach($ptc_passengers as $ptc_passenger_index => $flight_passenger){
			  
			  $_POST['passenger']['title'][] = $flight_passenger['title'] ?? 'mr';
			  $_POST['passenger']['birth_date'][] = date("d.m.Y", strtotime($flight_passenger['birthDate'] ?? date('Y-m-d')));
			  $_POST['passenger']['firstname'][] = $flight_passenger['firstName'] ?? '';
			  $_POST['passenger']['lastname'][] = $flight_passenger['lastName'] ?? '';
			  $_POST['passenger']['country'][] = $flight_passenger['country'] ?? '';
			  
			  if(isset($flight_passenger['details']) && is_array($flight_passenger['details'])){
				  $_POST['preferredSeats'][$ptc][$ptc_passenger_index]['details'] = $flight_passenger['details'];
			  }
			}
		  }
		  
	  }
    $this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), $type));
    
    $this->makeResponseGlobal();
  $this->load->library('form_validation');
  $response = modules :: run('Trip/checkout/Checkout/validate', $type);
  if(!$response){
	  // echo '<pre>';
	  // var_dump($type);
	  // var_dump($response);
	  // print_r(CI::$APP->__messages);
	  // var_dump($post);
	  // die;
	  /* if($system_messages = $this->session->flashdata('flashmsgs')){
		  
		print_r($system_messages);
		$this->session->set_flashdata('flashmsg', null);
		$this->session->set_flashdata('flashmsgtype', null);
		$this->session->set_flashdata('flashmsgs', []);
	  } */
	  // var_dump($this->input->post('test'));
		$this->output('error');
	  // die;
  }
  if (false === $this->form_validation->run()) {
	$this->data['errors'] = $this->form_validation->error_array();
	file_put_contents($response_dir_path . 'error_create_order.json', json_encode($this->data['errors'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
	$this->outputTripError($this->form_validation->error_string());
  }
  $response = modules :: run('Trip/checkout/Checkout/service', $type, true);
  if(!$response){
	  $cdate = date('YmdHis');
		$response_dir_path = APPPATH.'logs/pay24/' . $cdate . '/';
		if(!is_dir($response_dir_path)){
		  mkdir($response_dir_path,0777,true);
		}
		file_put_contents($response_dir_path . 'error_create_order.json', json_encode($this->Trip_model->get_api()->calls, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
		file_put_contents($response_dir_path . 'error_create_order.json', json_encode(array(
		  'response' => $this->response,
		  'message' => $this->message,
		  'message_type' => $this->message_type,
		  'messages' => $this->messages,
		  'data' => $this->data,
		), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
		
	$this->output('error');
  }
  // $this->outputError('Blocked temporary');
  $id = $_GET['order_id'];
  $this->load->model('TripOrder_model');
  $order = $this->TripOrder_model->getOrderById($id);
  
  $trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
  
  $ReservationId = [];
  foreach($trip_order->Services as $service){
	  if($service->Type === 'flight'){
		$ReservationId[] = $service->ReservationId;
	  }
  }
  
  
	$this->load->library('encryption');
	$id_hashed = $this->encryption->encrypt($id);
	$id_hashed = preg_replace('/\//','$', $id_hashed);
	$reference = $order->provider . '_' . $order->trip_order_id;
  $this->data['real_order_id'] = $_GET['order_id'];
  $this->data['order_link'] = site_url('pay24/order_details/' . urlencode($id_hashed) . '?pay24=1');
  $this->data['order_id'] = $id_hashed;
  $this->data['accent_id'] = $id;
  $this->data['reference'] = $reference;
  $this->data['ReservationId'] = implode(',',$ReservationId);
  $this->addMessage('Serviciul a fost validat.');
  $this->TripLog_model->saveLog(['order_id' => $id]);
  
  return $this->output();
  }
}