<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Epay extends MX_Controller {
	/* public function testbug(){
		$content = '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>
<REQUEST TYPE="GET">
   <CARD>
      <EAN>3691861830201</EAN>
   </CARD>
</REQUEST>';
		$xml = simplexml_load_string($content);
		if(!isset($xml->CARD)){
			die('Invalid request');
			// throw new EpayException('Invalid request', 200);
		}
		$card = $xml->CARD;
		if(!isset($card->EAN)){
			die('Invalid request');
			// throw new EpayException('Invalid request', 200);
		}
		$this->load->model('TripCoupon_model');

		$coupon_group_id = $this->TripCoupon_model->fromEAN($card->EAN);
		// $coupon_id = $this->TripCoupon_model->fromEAN($card->SNR);
		
		$parent_coupon = $this->TripCoupon_model->getCoupon(array(
			'status' => 1,
			'id' => $coupon_group_id,
			'type' => 'group',
			'epay' => 1,
			'ean' => $card->EAN,
			'active' => 1,
		));
		
		if(!$parent_coupon){
			die('Coupon expired');
			throw new EpayException('Coupon expired', 100);
		}
		$coupon = $this->TripCoupon_model->getCoupons(array(
			'status' => 0,
			'parent_id' => $parent_coupon->id,
			'pan' => isset($card->SNR) && strlen('' . $card->SNR) ? $card->SNR : null,
			'join_child' => 1,
			'active' => 1,
			// 'limit' => 1,
			'ordering' => 'RAND()',
		));
		echo '<pre>';
		print_r($coupon); die;
		if(!$coupon){
			throw new EpayException('Coupon expired', 100);
		}
		die;
	} */
	/* public function testbug2(){
		$content = '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>
<REQUEST TYPE="CANCEL">
   <CARD>
      <EAN>6006160813027</EAN>
      <SNR>1912111724</SNR>
   </CARD>
</REQUEST>
';
		$xml = simplexml_load_string($content);
		if(!isset($xml->CARD)){
			throw new EpayException('Invalid request', 200);
		}
		$card = $xml->CARD;
		if(!isset($card->EAN)){
			throw new EpayException('Invalid request', 200);
		}
		if(!isset($card->SNR)){
			throw new EpayException('Invalid request', 200);
		}
		$this->load->model('TripCoupon_model');

		$coupon_group_id = $this->TripCoupon_model->fromEAN($card->EAN);
		$coupon_id = $this->TripCoupon_model->fromPAN($card->SNR);
		
		$coupon = $this->TripCoupon_model->getCoupon(array(
			'join_child' => 1,
			'parent_status' => 1,
			'status' => 1,
			'id' => $coupon_id,
			'parent_id' => $coupon_group_id,
			'ean' => $card->EAN,
			'pan' => $card->SNR,
		));
		echo '<pre>';
		print_r($coupon); die;
		if(!$coupon){
			throw new EpayException('Coupon expired', 100);
		}
		die;
	} */
	/* public function fix(){
		$this->load->model('TripCoupon_model');
		$q = $this->db->get('trip_coupon c');
		$result = $q->result();
		ini_set('error_reporting', -1);
		ini_set('display_errors', 1);
		$this->load->model('TripCoupon_model');
		foreach($result as $k=>$item){
			$coupon_id = $item->id;
			$up_data = array();
			if($item->type == 'child'){
				if('' == '' . $item->pan){
					$up_data = array(
						'pan' => $this->TripCoupon_model->generatePAN($coupon_id),
					);
				}
			} elseif($item->type == 'group'){
				if('' == '' . $item->ean){
					$up_data = array(
						'ean' => $this->TripCoupon_model->generateEAN($coupon_id),
					);
				}
			}
			if($up_data){
				$this->db->where('id', $coupon_id);
				$this->db->update('trip_coupon', $up_data);
			}
		}
	} */
	/* public function test(){
		$this->load->model('TripCoupon_model');
		$number = rand(1, 100000);
		echo $number . '<br />';
		$pan = $this->TripCoupon_model->generatePAN($number);
		echo $pan . '<br />';
		$number_back = $this->TripCoupon_model->fromPAN($pan);
		echo $number_back . '<br />';
	} */
	protected function request($input_xml){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://accenttravel.ro/epay/endpoint?test=1');
		$headers = array(
			"Content-type: text/xml"
			,"Content-length: ".mb_strlen($input_xml)
			,"Connection: close"
		);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
                    $input_xml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		$output = curl_exec($ch);
		curl_close($ch);
		header('Content-Type: application/xml; charset=iso-8859-1');
		echo $output;
		// echo '<pre>';
		// echo htmlspecialchars($output);
		// echo '</pre>';
	}
	/* public function test_invalid_request(){
		$input_xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="ASD">
<CARD>
<EAN>5808008053814</EAN>
</CARD>
</REQUEST>';
		return $this->request($input_xml);
	}
	public function test_activate(){
		$input_xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="ACTIVATE">
<CARD>
<EAN>5808008053814</EAN>
</CARD>
</REQUEST>';

		return $this->request($input_xml);
	}
	public function test_check(){
		$input_xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="CHECK">
<CARD>
<EAN>5808008053814</EAN>
</CARD>
</REQUEST>';

		return $this->request($input_xml);
	}
	public function test_deactivate(){
		$input_xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="DEACTIVATE">
<CARD>
<SNR>9761556724</SNR>
<EAN>5808008053814</EAN>
</CARD>
</REQUEST>';

		return $this->request($input_xml);
	}
	public function test_get(){
		$input_xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="GET">
<CARD>
<SNR>9761556724</SNR>
<EAN>5808008053814</EAN>
</CARD>
</REQUEST>';

		return $this->request($input_xml);
	}
	public function test_cancel(){
		$input_xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="CANCEL">
<CARD>
<SNR>9761556724</SNR>
<EAN>5808008053814</EAN>
</CARD>
</REQUEST>';

		return $this->request($input_xml);
	} */
	private $test;
/*
<!-- FAIL -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="UNKNOWN">
  <RESULT INDEX="129">200</RESULT>
  <RESULTTEXT>Invalid request</RESULTTEXT>
  <SERVERDATETIME>2021-04-22 12:15:43</SERVERDATETIME>
</RESPONSE>
*/
	public function endpoint(){
		$valid_passwords = array ("epayaccess" => "x{+]uzkF3.m/\\6\"7"); // x{+]uzkF3.m/\6"7
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($valid_passwords[$_SERVER['PHP_AUTH_USER']]) || !isset($_SERVER['PHP_AUTH_PW']) || ($_SERVER['PHP_AUTH_PW'] !== $valid_passwords[$_SERVER['PHP_AUTH_USER']])) {
			header("WWW-Authenticate: Basic realm=\"Private Area\"");
			header("HTTP/1.0 401 Unauthorized");
			print "Sorry - you need valid credentials to be granted access!\n";
			exit;
		}
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if(!in_array($ip, array(
			'82.76.174.47',
			'5.2.255.10',
			'195.145.98.211', // Munich
			'195.226.126.201', // Speyer
		))){
			header('HTTP/1.0 403 Forbidden');
			die('You are not allowed to access this file.');
		}
		
		// ini_set('display_errors', 1);
		$content = file_get_contents('php://input');
		
		$response_dir_path = APPPATH.'logs/epay/' . date('YmdHis') . '/';
		if(!is_dir($response_dir_path)){
		  mkdir($response_dir_path,0777,true);
		}
		$this->test = !empty($_GET['test']);
		file_put_contents($response_dir_path . 'server.json',json_encode($_SERVER, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		file_put_contents($response_dir_path . 'post.json',json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		file_put_contents($response_dir_path . 'headers.json',json_encode(getallheaders(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		file_put_contents($response_dir_path . 'get.json',json_encode($_GET, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		file_put_contents($response_dir_path . 'ip.txt',$ip);
		file_put_contents($response_dir_path . 'input.xml',$content);
		
		$type = 'UNKNOWN';
		try{
			$this->load->helper("xmlarr");
			$xml = simplexml_load_string($content);
			if(!$xml){
				throw new EpayException('Invalid request', 200);
			}
			$attributes = $xml->attributes();
			// if(!in_array((string)$attributes->TYPE ?? '', array('GET','CANCEL','ACTIVATE','DEACTIVATE','CHECK'))){
			if(!in_array((string)$attributes->TYPE ?? '', array('GET','CANCEL'))){
				throw new EpayException('Invalid request', 200);
			}
			$type = (string)$attributes->TYPE;
			$func = strtolower($attributes->TYPE);
			$response = $this->$func($xml);
			$response = array_merge(
				array(
					'RESULT' => 0,
					'SERVERDATETIME' => date('Y-m-d H:i:s'),
				),
				$response,
			);
		} catch(EpayException $e){
			$response = array(
				'RESULT' => array(
					'_' => $e->getCode(),
					'INDEX' => $e->getLine(),
				),
				'RESULTTEXT' => $e->getMessage(),
				'SERVERDATETIME' => date('Y-m-d H:i:s'),
			);
		}
		
		header('Content-Type: application/xml; charset=iso-8859-1');
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>' . PHP_EOL;
		echo arr2xml(array(
			'RESPONSE' => array_merge(array(
				'@' => array(
					'type' => $type,
				),
			), $response),
		));
		file_put_contents($response_dir_path . 'response.xml',ob_get_contents());
		try{
			$this->theme->set_theme('accent');
			$this->theme->set_layout('blank');
			$this->theme->set_sublayout('frontend/blank/index');
			if(isset($func) && in_array($func,array('get','activate'))){
				Modules :: run ('Mailer/epay_coupon_activate', array('response' => $response));
			}
			if(isset($func) && in_array($func,array('cancel','deactivate'))){
				Modules :: run ('Mailer/epay_coupon_deactivate', array('response' => $response));
			}
		} catch(Exception $e){
			// DO nothing
		}
	}
/* 
<!-- Activates a random/specific SNR. EAN is mandatory. If SNR is specified, it activates that specific SNR. If SNR is not specified, it activates a random SNR. The response contains the Coupon <CODE> -->
<!-- REQUEST -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="ACTIVATE">
<CARD>
<EAN>5808008053814</EAN>
<SNR>9761556724</SNR> <!-- OPTIONAL. If not specified, a random Coupon code will be provided -->
</CARD>
</REQUEST>

<!-- RESPONSE -->

<!-- SUCCESS -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="ACTIVATE">
  <RESULT>0</RESULT>
  <SERVERDATETIME>2021-04-22 12:00:26</SERVERDATETIME>
  <EAN>5808008053814</EAN>
  <SNR>9761556724</SNR>
  <CODE>rewePM2BWT754D73</CODE>
  <DISCOUNT>
    <TYPE>FIXED</TYPE>
    <AMOUNT>250.00</AMOUNT>
    <CURRENCY SYMBOL="Lei">RON</CURRENCY>
    <FORMATTED>250 Lei</FORMATTED>
  </DISCOUNT>
  <AVAILABILITY>
    <START>2021-03-30</START>
    <EXPIRE/>
  </AVAILABILITY>
  <NAME>Coupon de reducere</NAME>
  <DESCRIPTION>Coupons rewe</DESCRIPTION>
  <RESULTTEXT>Coupon activated</RESULTTEXT>
</RESPONSE>

<!-- FAIL -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="ACTIVATE">
  <RESULT INDEX="223">100</RESULT>
  <RESULTTEXT>Coupon expired</RESULTTEXT>
  <SERVERDATETIME>2021-04-22 12:01:01</SERVERDATETIME>
</RESPONSE>
*/
	protected function get($xml){
		return $this->activate($xml);
	}
	protected function activate($xml){
		if(!isset($xml->CARD)){
			throw new EpayException('Invalid request', 200);
		}
		$card = $xml->CARD;
		if(!isset($card->EAN)){
			throw new EpayException('Invalid request', 200);
		}
		$this->load->model('TripCoupon_model');

		$coupon_group_id = $this->TripCoupon_model->fromEAN($card->EAN);
		// $coupon_id = $this->TripCoupon_model->fromEAN($card->SNR);
		
		$parent_coupon = $this->TripCoupon_model->getCoupon(array(
			'status' => 1,
			'id' => $coupon_group_id,
			'type' => 'group',
			'epay' => 1,
			'ean' => $card->EAN,
			'active' => 1,
		));
		if(!$parent_coupon){
			throw new EpayException('Coupon expired', 100);
		}
		$coupon = $this->TripCoupon_model->getCoupon(array(
			'status' => 0,
			'parent_id' => $parent_coupon->id,
			'pan' => isset($card->SNR) && strlen('' . $card->SNR) ? $card->SNR : null,
			'join_child' => 1,
			'active' => 1,
			'limit' => 1,
			'ordering' => 'RAND()',
		));
		if(!$coupon){
			throw new EpayException('Coupon expired', 100);
		}
		
		$this->TripCoupon_model->publishCouponById($coupon->id);
		$return = array(
			'EAN' => $coupon->ean,
			'SNR' => $coupon->pan,
			'CODE' => $coupon->code,
			'DISCOUNT' => array(
				'TYPE' => $coupon->discount_type == 'F' ? 'FIXED' : 'PERCENTAGE',
			),
			'AVAILABILITY' => array(
				'START' => trim($coupon->date_start),
				'EXPIRE' => trim($coupon->date_expire),
			),
			'NAME' => mb_convert_encoding(trim($coupon->name), 'ISO-8859-1', 'UTF-8'),
			'DESCRIPTION' => mb_convert_encoding(trim($coupon->observation), 'ISO-8859-1', 'UTF-8'),
			'RESULTTEXT' => 'Coupon activated',
		);
		if($coupon->discount_type == 'F'){
			if(!empty(0 + $coupon->fixed_ron)){
				$return['DISCOUNT']['AMOUNT'] = $coupon->fixed_ron;
				$return['DISCOUNT']['CURRENCY'] = array(
					'_'=>'RON',
					'SYMBOL' => 'Lei',
				);
				$return['DISCOUNT']['FORMATTED'] = (0 + $coupon->fixed_ron) . ' Lei';
			} else {
				$return['DISCOUNT']['AMOUNT'] = $coupon->fixed_eur;
				$return['DISCOUNT']['CURRENCY'] = array(
					'_'=>'EUR',
					'SYMBOL' => '€',
				);
				$return['DISCOUNT']['FORMATTED'] = (0 + $coupon->fixed_eur) . ' €';
			}
		} else {
			$return['DISCOUNT']['AMOUNT'] = $coupon->percentage;
			$return['DISCOUNT']['FORMATTED'] = (0 + $coupon->percentage) . ' %';
		}
		return $return;
	}
/* 
<!-- Deactivates a SNR. EAN and SNR are mandatory -->
<!-- REQUEST -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="DEACTIVATE">
<CARD>
<EAN>5808008053814</EAN>
<SNR>9761556724</SNR>
</CARD>
</REQUEST>

<!-- RESPONSE -->

<!-- SUCCESS -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="DEACTIVATE">
  <RESULT>0</RESULT>
  <SERVERDATETIME>2021-04-22 12:00:27</SERVERDATETIME>
  <EAN>5808008053814</EAN>
  <SNR>9761556724</SNR>
  <RESULTTEXT>Coupon deactivated</RESULTTEXT>
</RESPONSE>

<!-- FAIL -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="DEACTIVATE">
  <RESULT INDEX="307">100</RESULT>
  <RESULTTEXT>Coupon expired</RESULTTEXT>
  <SERVERDATETIME>2021-04-22 12:00:16</SERVERDATETIME>
</RESPONSE>
*/
	protected function cancel($xml){
		return $this->deactivate($xml);
	}
	protected function deactivate($xml){
		if(!isset($xml->CARD)){
			throw new EpayException('Invalid request', 200);
		}
		$card = $xml->CARD;
		if(!isset($card->EAN)){
			throw new EpayException('Invalid request', 200);
		}
		if(!isset($card->SNR)){
			throw new EpayException('Invalid request', 200);
		}
		$this->load->model('TripCoupon_model');

		$coupon_group_id = $this->TripCoupon_model->fromEAN($card->EAN);
		$coupon_id = $this->TripCoupon_model->fromPAN($card->SNR);
		
		$coupon = $this->TripCoupon_model->getCoupon(array(
			'join_child' => 1,
			'parent_status' => 1,
			'status' => 1,
			'id' => $coupon_id,
			'parent_id' => $coupon_group_id,
			'ean' => $card->EAN,
			'pan' => $card->SNR,
		));
		
		if(!$coupon){
			throw new EpayException('Coupon expired', 100);
		}
		$this->TripCoupon_model->unpublishCouponById($coupon->id);
		
		$return = array(
			'EAN' => $coupon->ean,
			'SNR' => $coupon->pan,
			'RESULTTEXT' => 'Coupon deactivated',
		);
		return $return;
	}
/* 
<!-- Check if the Coupon EAN has any activable PANs and returns a random SNR. If SNR is specified in the request, it checks that specific SNR. -->
<!-- REQUEST -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<REQUEST TYPE="CHECK">
<CARD>
<EAN>5808008053814</EAN>
<SNR>9761556724</SNR> <!-- OPTIONAL. If not specified, a random Coupon SNR will be provided -->
</CARD>
</REQUEST>

<!-- RESPONSE -->

<!-- SUCCESS -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="CHECK">
  <RESULT>0</RESULT>
  <SERVERDATETIME>2021-04-22 11:59:25</SERVERDATETIME>
  <EAN>5808008053814</EAN>
  <SNR>9761556724</SNR>
  <DISCOUNT>
    <TYPE>FIXED</TYPE>
    <AMOUNT>250.00</AMOUNT>
    <CURRENCY SYMBOL="Lei">RON</CURRENCY>
    <FORMATTED>250 Lei</FORMATTED>
  </DISCOUNT>
  <AVAILABILITY>
    <START>2021-03-30</START>
    <EXPIRE/>
  </AVAILABILITY>
  <NAME>Coupon de reducere</NAME>
  <DESCRIPTION>Coupons rewe</DESCRIPTION>
  <RESULTTEXT>Coupon found</RESULTTEXT>
</RESPONSE>

<!-- FAIL -->
<?xml version="1.0" encoding="ISO-8859-1" ?>
<RESPONSE type="CHECK">
  <RESULT INDEX="373">100</RESULT>
  <RESULTTEXT>Coupon expired</RESULTTEXT>
  <SERVERDATETIME>2021-04-22 11:58:34</SERVERDATETIME>
</RESPONSE>
*/
	protected function check($xml){
		if(!isset($xml->CARD)){
			throw new EpayException('Invalid request', 200);
		}
		$card = $xml->CARD;
		if(!isset($card->EAN)){
			throw new EpayException('Invalid request', 200);
		}
		$this->load->model('TripCoupon_model');

		$coupon_group_id = $this->TripCoupon_model->fromEAN($card->EAN);
		
		$parent_coupon = $this->TripCoupon_model->getCoupon(array(
			'status' => 1,
			'id' => $coupon_group_id,
			'type' => 'group',
			'epay' => 1,
			'ean' => $card->EAN,
			'active' => 1,
		));
		if(!$parent_coupon){
			throw new EpayException('Coupon expired', 100);
		}
		$coupon = $this->TripCoupon_model->getCoupon(array(
			'status' => 0,
			'parent_id' => $parent_coupon->id,
			'pan' => isset($card->SNR) && strlen('' . $card->SNR) ? $card->SNR : null,
			'join_child' => 1,
			'active' => 1,
			'limit' => 1,
		));
		if(!$coupon){
			throw new EpayException('Coupon expired', 100);
		}
		
		$return = array(
			'EAN' => $coupon->ean,
			'SNR' => $coupon->pan,
			'DISCOUNT' => array(
				'TYPE' => $coupon->discount_type == 'F' ? 'FIXED' : 'PERCENTAGE',
			),
			'AVAILABILITY' => array(
				'START' => trim($coupon->date_start),
				'EXPIRE' => trim($coupon->date_expire),
			),
			'NAME' => mb_convert_encoding(trim($coupon->name), 'ISO-8859-1', 'UTF-8'),
			'DESCRIPTION' => mb_convert_encoding(trim($coupon->observation), 'ISO-8859-1', 'UTF-8'),
			'RESULTTEXT' => 'Coupon found',
		);
		if($coupon->discount_type == 'F'){
			if(!empty(0 + $coupon->fixed_ron)){
				$return['DISCOUNT']['AMOUNT'] = $coupon->fixed_ron;
				$return['DISCOUNT']['CURRENCY'] = array(
					'_'=>'RON',
					'SYMBOL' => 'Lei',
				);
				$return['DISCOUNT']['FORMATTED'] = (0 + $coupon->fixed_ron) . ' Lei';
			} else {
				$return['DISCOUNT']['AMOUNT'] = $coupon->fixed_eur;
				$return['DISCOUNT']['CURRENCY'] = array(
					'_'=>'EUR',
					'SYMBOL' => '€',
				);
				$return['DISCOUNT']['FORMATTED'] = (0 + $coupon->fixed_eur) . ' €';
			}
		} else {
			$return['DISCOUNT']['AMOUNT'] = $coupon->percentage;
			$return['DISCOUNT']['FORMATTED'] = (0 + $coupon->percentage) . ' %';
		}
		return $return;
	}
}
class EpayException extends Exception { }