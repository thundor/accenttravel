<?php
$this->theme->set_theme('accent');
$this->theme->set_layout('blank');
$this->theme->set_sublayout('frontend/blank/index');
// ini_set('display_errors', 1);
$this->load->model('TripLog_model');
$this->TripLog_model->saveLog([
	'date_pay' => date('Y-m-d H:i:s'),
	'order_data' => json_encode($_POST),
]);
$response = '[]';
$this->load->model('Trip_model');

$post_data = $this->input->post();
$coupon_code = isset($post_data['coupon_code']) && !empty($post_data['coupon_code']) ? $post_data['coupon_code'] : NULL;
$payment_method = isset($post_data['payment_method']) && !empty($post_data['payment_method']) ? $post_data['payment_method'] : 'free';
$payment_gateway = isset($post_data['payment_gateway']) && !empty($post_data['payment_gateway']) ? $post_data['payment_gateway'] : NULL;
$data = isset($post_data['data']) && !empty($post_data['data']) ? (array)$post_data['data'] : [];
$billing_person = isset($post_data['billing_person']) && !empty($post_data['billing_person']) ? (array)$post_data['billing_person'] : [];
$BillCompany = filter_var(isset($billing_person['BillCompany']) && !empty($billing_person['BillCompany']) ? $billing_person['BillCompany'] : NULL, FILTER_VALIDATE_BOOLEAN);

$user_invoice = !$BillCompany ? 'pf' : 'pj';

$Address = isset($billing_person['Address']) && !empty($billing_person['Address']) ? (array)$billing_person['Address'] : [];
$AddressCountry = isset($Address['Country']) && !empty($Address['Country']) ? $Address['Country'] : 'RO';
$AddressCity = isset($Address['City']) && !empty($Address['City']) ? $Address['City'] : '';
$AddressAddress = isset($Address['Details']) && !empty($Address['Details']) ? $Address['Details'] : '';
$AddressStreet = isset($Address['Street']) && !empty($Address['Street']) ? $Address['Street'] : '';
$AddressStreetNo = isset($Address['StreetNo']) && !empty($Address['StreetNo']) ? $Address['StreetNo'] : '';
$AddressPostalCode = isset($Address['PostalCode']) && !empty($Address['PostalCode']) ? $Address['PostalCode'] : '';

$Firstname = isset($billing_person['Firstname']) && !empty($billing_person['Firstname']) ? $billing_person['Firstname'] : '';
$Name = isset($billing_person['Name']) && !empty($billing_person['Name']) ? $billing_person['Name'] : '';
$Email = isset($billing_person['Email']) && !empty($billing_person['Email']) ? $billing_person['Email'] : '';
$Phone = isset($billing_person['Phone']) && !empty($billing_person['Phone']) ? $billing_person['Phone'] : '';

$Company = isset($billing_person['Company']) && !empty($billing_person['Company']) ? (array)$billing_person['Company'] : [];

$CompanyName = isset($Company['Name']) && !empty($Company['Name']) ? $Company['Name'] : '';
$CompanyCUI = isset($Company['TaxIdentificationNo']) && !empty($Company['TaxIdentificationNo']) ? $Company['TaxIdentificationNo'] : '';
$CompanyONRC = isset($Company['RegistrationNo']) && !empty($Company['RegistrationNo']) ? $Company['RegistrationNo'] : '-';
$CompanyBANK = isset($Company['Bank']) && !empty($Company['Bank']) ? $Company['Bank'] : '-';
$CompanyIBAN = isset($Company['BankAccount']) && !empty($Company['BankAccount']) ? $Company['BankAccount'] : '-';

$CompanyHeadOffice = isset($Company['HeadOffice']) && !empty($Company['HeadOffice']) ? $Company['HeadOffice'] : [];
$CompanyHeadOfficeAddress = isset($CompanyHeadOffice['Details']) && !empty($CompanyHeadOffice['Details']) ? $CompanyHeadOffice['Details'] : '';

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
$_POST['invoice'] = $user_invoice;
$_POST['create_account'] = $billing['create_account'] ?? 0; // TODO
$_POST['password'] = $billing['password'] ?? null; // TODO

$_POST['contact_country'] = $AddressCountry;
$_POST['contact_city'] = $AddressCity;
$_POST['contact_phone'] = $Phone;
$_POST['contact_phone_prefix'] = 'RO';
$_POST['contact_email'] = $Email;
$_POST['contact_firstname'] = $Firstname;
$_POST['contact_lastname'] = $Name;
$_POST['contact_street'] = $AddressStreet;
$_POST['contact_street_no'] = $AddressStreetNo;
$_POST['contact_postal_code'] = $AddressPostalCode;
$_POST['contact_bank'] = $CompanyBANK;
$_POST['contact_iban'] = $CompanyIBAN;
$_POST['contact_cui'] = $CompanyCUI;
$_POST['contact_company_name'] = $CompanyName;
$_POST['contact_regcom'] = $CompanyONRC;
// $_POST['contact_address'] = $billing['address'] ?? ''; // TODO

if('online' == $payment_method){
	$payment_gateway = $payment_gateway ?? 'payu';
	$_POST['payu_payment_method'] = $post['payu_payment_method'] ?? ''; 
} else {
	$payment_gateway = '';
}

$_POST['payment_method'] = $payment_method ?? '';
$_POST['payment_gateway'] = $payment_gateway;
$_POST['gateway'] = $payment_gateway;

$type = 'flight';
if(!empty($post['hotel'])){
	$type = 'citybreak';
	
	$hotel = $post['hotel'] ?? array();

	$_POST['expectedHotelPrice'] = number_format(($hotel['expected_price'] ?? ($post['price'] ?? null)),2,'.','') . ($post['currency'] ?? null);
	$_POST['code'] = $hotel['code'] ?? null;
	$_POST['hotel_id'] = $hotel['hotel_id'] ?? null;
	$_POST['package_code'] = $hotel['package_code'] ?? null;
	$_POST['rooms_combinations'] = $hotel['rooms_combinations'] ?? null;
}

$flight = $post['flight'] ?? array();

$_POST['expectedFlightPrice'] = number_format(($flight['expected_price'] ?? ($post['price'] ?? null)),2,'.','') . ($post['currency'] ?? null);
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

$this->load->model('TripCoupon_model');
$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), $type));

$this->makeResponseGlobal();
$this->load->library('form_validation');
$response = modules :: run('Trip/checkout/Checkout/validate', $type);
if(!$response){
	$this->output('error');
}

$cdate = date('YmdHis');
$response_dir_path = APPPATH.'logs/trip/travelfuse/' . $cdate . '/';
if(!is_dir($response_dir_path)){
	mkdir($response_dir_path,0777,true);
}
if (false === $this->form_validation->run()) {
	$this->data['errors'] = $this->form_validation->error_array();
	file_put_contents($response_dir_path . 'error_create_order.json', json_encode($this->data['errors'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
	$this->outputTripError($this->form_validation->error_string());
}
$response = modules :: run('Trip/checkout/Checkout/service', $type, false);
if(!$response){
	$this->output('error');
}
// dump($type);
// prd(json_encode($_POST));
// dd('good');
ob_start(); /* ?>
<form action="https://accenttravel.ro/" target="_BLANK">
	<button type="submit">Trimite</button>
</form>
<?php */
// $this->output('success');
// return;
// dd($response);
$response = modules :: run('Trip/checkout/Checkout/service', $type, true);
$html = ob_get_clean();
if('' === $html){
	$html = get_instance()->output->get_output();
}
$this->data['html'] = trim($html);
if(false === $response){
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
$this->data['order_link'] = site_url('newux/order_details/' . urlencode($id_hashed) . '?newux=1');
$this->data['order_id'] = $id_hashed;
$this->data['accent_id'] = $id;
$this->data['reference'] = $reference;
$this->data['ReservationId'] = implode(',',$ReservationId);
$this->addMessage('Serviciul a fost validat.');
$this->TripLog_model->saveLog(['order_id' => $id]);

return $this->output();
echo $response;