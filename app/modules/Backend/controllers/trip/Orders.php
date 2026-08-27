<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Orders extends MX_Controller
{
	public function index()
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$this->theme->view('backend/trip/list/orders', $this->data);
	}
	public function log()
	{
		$page = intval($this->input->post('get'));
		$page = $page <= 0 ? 1 : $page;
		
		$limit = intval($this->input->post('limit'));
		$max_limit = 10000;
		$limit = $limit <= 0 || $limit > $max_limit ? $max_limit : $limit;
		$offset = ($page - 1) * $limit;
		
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		
		$sql = <<<SQL
		SELECT id, `session` as identificatorSesiune, app as aplicatie, results_count as numarRezultate, order_id as idComanda, `ip`, timp_raspuns_cautare as timpRaspunsCautare, timp_raspuns_rezultate as timpRaspunsRezultate, IFNULL(timp_raspuns_cautare,0) + IFNULL(timp_raspuns_rezultate,0) as timpTotalRaspunsCautareRezultate, timp_raspuns_item as timpRaspunsDetalii, date_added as dataAdaugarii, date_modified as dataModificarii
		, REPLACE(JSON_EXTRACT(search_data, "$.origin_country_name"),'"','') as taraPlecare
		, REPLACE(JSON_EXTRACT(search_data, "$.origin_city_name"),'"','') as orasPlecare
		, REPLACE(JSON_EXTRACT(search_data, "$.origin_location_name"),'"','') as locatiePlecare
		, REPLACE(JSON_EXTRACT(search_data, "$.destination_country_name"),'"','') as taraSosire
		, REPLACE(JSON_EXTRACT(search_data, "$.destination_city_name"),'"','') as orasSosire
		, REPLACE(JSON_EXTRACT(search_data, "$.destination_location_name"),'"','') as locatieSosire
		, REPLACE(JSON_EXTRACT(search_data, "$.departure_date"),'"','') as dataPlecare
		, REPLACE(JSON_EXTRACT(search_data, "$.return_date"),'"','') as dataSosire
		, (@prenume_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.first_name"),'"','')) as prenumeClient
		, (@nume_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.last_name"),'"','')) as numeClient
		, (@email_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.email"),'"','')) as emailClient
		, (@telefon_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.phone"),'"','')) as telefonClient
		, (@adresa_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.adress"),'"','')) as adresaClient
		, (@ocupatie_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.ocupation"),'"','')) as ocupatieClient
		, (@nationalitate_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.citizenship"),'"','')) as nationalitateClient
		, (@data_nastere_client:= REPLACE(JSON_EXTRACT(customer_data, "$.profile.personal_data.birth_date"),'"','')) as data_nastereClient
		, date_results as dataAccesarePasPreluareRezultate
		, date_details as dataAccesarePasDetalii
		, date_passengers as dataAccesarePasPasageri
		, date_billing as dataAccesarePasFacturare
		, date_checkout as dataAccesarePasCheckoutSumarPlata
		, date_summary as dataAccesarePasDetaliiSumar
		, date_pay as dataAccesarePlata
		, error_message as mesajUltimaEroare
		, device
		,(@adt:= (0 + JSON_EXTRACT(search_data, "$.passengers_adult"))) as adulti
		,(@sen:= (0 + JSON_EXTRACT(search_data, "$.passengers_senior"))) as seniori
		,(@chd:= (0 + JSON_EXTRACT(search_data, "$.passengers_child"))) as copii
		,(@yth:= (0 + JSON_EXTRACT(search_data, "$.passengers_youth"))) as tineri
		,(@inf:= (0 + JSON_EXTRACT(search_data, "$.passengers_infant_lap"))) as bebeBrate
		,(@ins:= (0 + JSON_EXTRACT(search_data, "$.passengers_infant_seat"))) as bebeScaun
		, (@adt + @sen) as totalAdulti
		, (@yth + @chd + @ins + @inf) as totalCopii
		, (@adt + @sen + @yth + @chd + @ins + @inf) as totalPasageri
		, CASE (0 + JSON_EXTRACT(search_data, "$.cabine_type")) WHEN 1 THEN 'Economy' WHEN 2 THEN 'First class' when 3 THEN 'Business' WHEN 4 THEN 'Premium' ELSE NULL END as clasaZbor
		, CASE JSON_EXTRACT(search_data, "$.go_only") WHEN '"false"' THEN 'Nu' WHEN '"true"' THEN 'Da' ELSE NULL END as zborDirect
		FROM `trip_log` 
		WHERE ip <> '82.76.174.47' and app = 'pay24'
		ORDER BY `date_modified` DESC
		LIMIT $offset, $limit
		SQL;
		$q = $this->db->query($sql);
		
		require_once(APPPATH . 'third_party/psr_autoloader.php');
		require_once(APPPATH . 'third_party/php_spreadsheet_autoloader.php');
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		$sheet = $spreadsheet->getActiveSheet();
		
		$spreadsheet->getDefaultStyle()
		->getNumberFormat()
		->setFormatCode('@');
		
		$sheet->setTitle('Logs ' . date("Y-m-d H-i-s"));
		
		$sheet->getStyle('W')
		->getNumberFormat()
		->setFormatCode('@');
		
		$result = $q->result();
		
		if(!empty($result)){
			$header = array_keys((array)($result[0] ?? []));
			// $data = array_map(function($v){return array_map(function($i){ return !empty($i) ? '"' . $i : $i; }, array_values((array)$v));}, $result);
			$data = array_map(function($v){return array_values((array)$v);}, $result);
			array_unshift($data, $header);
		}
		$sheet->fromArray($data);
		// die;
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=Logs " . date("Y-m-d H-i-s") . ".xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		$writer->save("php://output");
		exit;
		
		echo '<pre>';
		print_r($q->result());
		die;
	}
	public function getlist()
	{
		if ($this->input->is_ajax_request() || $this->input->post('xls')) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$filters = array(
				'type' => 'orders',
			);

			$user_can = array();
			$user_can['access'] = $this->user->can('backend-trip-orders-access');
			$user_can['access_own'] = $user_can['access'] || $this->user->can('backend-trip-orders-access');
			$user_can['view_own'] = $user_can['access_own'] && $this->user->can('backend-trip-orders-own-view');
			$user_can['edit_own'] = $user_can['access_own'] && $this->user->can('backend-trip-orders-own-edit');
			$user_can['delete_own'] = $user_can['access_own'] && $this->user->can('backend-trip-orders-own-delete');
			$user_can['view'] = $user_can['access'] && $this->user->can('backend-trip-orders-view');
			$user_can['edit'] = $user_can['access'] && $this->user->can('backend-trip-orders-edit');
			$user_can['delete'] = $user_can['access'] && $this->user->can('backend-trip-orders-delete');

			$search = trim('' . $this->input->post('search'));
			$filters['search'] = $search;

			if (!$user_can['access']) {
				$filters['created_by'] = $this->user->id;
			}

			$this->load->model('TripOrder_model');
			
			if($this->user->user_role == 'pay24_comenzi' ){
				$filters['payment_gateway'] = 'pay24';
			}
			$this->data['total_orders'] = $this->TripOrder_model->getTotalOrders($filters);

			$limit = (int)$this->input->post('limit');
			if ($limit < 0) {
				$limit = 0;
			}
			$filters['limit'] = $limit;
			$ordering = trim('' . $this->input->post('ordering'));
			$filters['ordering'] = $ordering;

			$max_pages = $filters['limit'] ? ceil($this->data['total_orders'] / $filters['limit']) : 1;
			if ($max_pages < 1) {
				$max_pages = 1;
			}
			$this->data['max_pages'] = $max_pages;

			$current_page = (int)$this->input->post('page');
			if ($current_page > $max_pages) {
				$current_page = $max_pages;
			}
			if ($current_page < 1) {
				$current_page = 1;
			}

			$filters['page'] = $current_page;
			$orders = $this->TripOrder_model->getOrders($filters);
			
			
			if($this->input->post('xls')){
				require_once(APPPATH . 'third_party/psr_autoloader.php');
				require_once(APPPATH . 'third_party/php_spreadsheet_autoloader.php');
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				ini_set('display_errors', 1);
				
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle('Comenzi');
				
				$data = array(array(
					'ID',
					'Furnizor',
					'ID Furnizor',
					'Status',
					'Tip',
					'Nume',
					'Email',
					'Plata',
					'Pret',
					'Valuta',
					'Data',
				));
				
				
				foreach($orders as $c){
					$data[] = array(
						$c->id,
						$c->provider,
						$c->trip_order_id,
						$c->status == 1 ? 'In procesare' : ($c->status == -1 ? '- Eroare -' : ($c->status == 3 ? 'Anulata' : ($c->status == 2 ? 'Confirmata' : 'Nefinalizata'))),
						$c->type,
						trim($c->user_lastname . ' ' . $c->user_firstname),
						$c->user_email,
						$c->payment_method . ' ' . $c->payment_gateway,
						$c->amount,
						$c->currency,
						$c->time_created,
					);
				}
				
				$sheet->fromArray($data);
				
				$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
				header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=" . htmlspecialchars('Comenzi ' . date('Y-m-d H-i-s')). ".xls");  //File name extension was wrong
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false);
				$writer->save("php://output");
				exit;
			}
			
			foreach ($orders as $k => $order) {
				$order->can_view = ($user_can['access'] && $user_can['view']) || ((($order->created_by == $this->user->id || $this->user->user_role == 'pay24_comenzi' && $order->payment_gateway == 'pay24')) && $user_can['view_own']);
				if ($order->can_view) {
					$order->view_link = site_url('backend/trip/orders/view?id=' . $order->id);
				}
				$order->can_edit = ($user_can['access'] && $user_can['edit']) || (($order->created_by == $this->user->id || $this->user->user_role == 'pay24_comenzi' && $order->payment_gateway == 'pay24') && $user_can['edit_own']);
				if ($order->can_edit) {
					$order->edit_link = site_url('backend/trip/orders/edit?id=' . $order->id);
				}
				$order->can_delete = false;
				// $order->can_delete = !$order->trip_order_id && (($user_can['access'] && $user_can['delete']) || ($order->created_by == $this->user->id && $user_can['delete_own']));
				// if($order->can_delete){
				// $order->delete_link = site_url('backend/trip/orders/delete?id=' . $order->id);
				// }
			}
			$this->data['orders'] = $orders;
			$this->data['page'] = $current_page;

			$session_data = array();
			$session_data['page'] = $current_page;
			$session_data['ordering'] = $ordering;
			$session_data['search'] = $search;
			$session_data['limit'] = $limit;
			$this->session->set_userdata('backend/trip/orders/list', $session_data);
			$this->output();
		}
		$this->redirect('', 'Acces invalid', 'error');
	}
	public function add($provider = 'trip')
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->can('backend-trip-orders-add')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if ($provider == 'trip') {
			$this->load->model('TripOrder_model');
			$trip_order = $this->TripOrder_model->createTripOrder();
			if (!$trip_order) {
				$this->redirect('backend/trip/orders', 'TripError: Nu s-a putut crea comanda', 'error');
			}
			$this->load->library('TripOrder');
			$data['trip_order_id'] = $trip_order->Id;
			$data['created_by'] = $this->user->id;
			$data['time_created'] = $trip_order->Date;

			$order_id = $this->TripOrder_model->saveOrder($data);
			$this->redirect('backend/trip/orders/edit?id=' . $order_id, 'Comanda goala a fost creata.');
		} else {
			$order = new TripOrder;
			$order->provider = $provider;
			$this->data['order'] = $order;
			$this->theme->view('backend/trip/item/paralela45', $this->data);
			return;
		}
	}
	public function edit()
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		$provider = $order->provider;

		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		if (!$can_edit) {
			$can_access_own = $can_access || $this->user->can('backend-trip-orders-own-access');
			$can_edit_own = $can_access_own && $this->user->can('backend-trip-orders-own-edit');
			$can_edit = ($user->created_by == $this->user->id) && $can_edit_own;
		}
		if (!$can_edit) {
			$this->redirect('backend/trip/orders', 'Acces restrictionat', 'error');
		}
		$this->data['order'] = $order;
		$this->load->model('TripOrderCoupon_model');
		$this->data['coupons'] = $this->TripOrderCoupon_model->getOrderCouponsByOrderId($order->id);

		$this->load->model('Ticket_model');
		$ticket = $this->Ticket_model->getTicketByOrderId($order->id);
		if (!$ticket) {
			$this->load->library('Ticket');
			$ticket = new Ticket;
			$ticket->trip_order_id = $order->id;
		}

		$this->data['ticket'] = &$ticket;
		$this->data['users'] = $this->Ticket_model->getAllowedUsers();
		if ($provider === 'paralela45') {
			$this->theme->view('backend/trip/item/paralela45', $this->data);
			return;
		} elseif ($provider === 'trip') {
			$trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
			$order->trip_order = $trip_order;
			$this->theme->view('backend/trip/item/orders', $this->data);
			return;
		} elseif ($provider === 'travelfuse') {
			$this->theme->view('backend/trip/item/travelfuse', $this->data);
			return;
		}
		$this->redirect('backend/trip/orders', 'Furnizor invalid', 'error');
	}
	public function upload_invoice(){
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			echo 'Comanda invalida'; exit;
			$this->redirect('backend/trip/orders/invoices?id=' . $id, 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		
		if (!$can_edit) {
			$can_edit_own = $can_access_own && $this->user->can('backend-trip-orders-own-edit');
			$can_edit = (($order->created_by == $this->user->id || $this->user->user_role == 'pay24_comenzi' && $order->payment_gateway == 'pay24')) && $can_edit_own;
		}
		if (!$can_edit) {
			echo 'Acces restrictionat'; exit;
			// $this->redirect('backend/trip/orders/invoices?id=' . $id, 'Acces restrictionat', 'error');
		}
		$pdf = isset($_FILES['pdf']) ? $_FILES['pdf'] : array();
		
		$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		
		$file_deposit_path = $facturi_path . $id . '.pdf';
		
		$ext = substr(strrchr($pdf['name'], '.'), 1);
		
		$error = null;
		for(;;){
			if($error) break;
			if(!$pdf) { $error = 'No file uploaded'; break; }
			if($pdf['error']) { $error = 'Error uploading to tmp ' . $pdf['error'] . ''; break; }
			if($pdf['type'] != 'application/pdf') { $error = 'Mime ' . $pdf['type'] . ' not supported. Only application/pdf allowed'; break; }
			if($ext != 'pdf') { $error = 'Extension ' . $ext . ' not supported. Only pdf allowed'; break; }
			break;
		}
		if(!$error){
			$renamed = false;
			$exists = false;
			if(is_file($file_deposit_path)){
				$exists = true;
				if(sha1_file($pdf['tmp_name']) != sha1_file($file_deposit_path)){
					$renamed = rename($file_deposit_path, $facturi_path . $id . '-' . date('Y-m-d H:i:s',filectime($file_deposit_path)) . '.pdf');
				}
			}
			$message = 'Factura incarcata cu succes';
			$message_type = 'success';
			if(!$exists || $renamed){
				$moved = move_uploaded_file($pdf['tmp_name'], $file_deposit_path);
				if(!$moved){
					$error = 'Failed to move file from tmp to storage folder';
				}
			} else {
				$message = 'Factura incarcata este echivalenta cu cea deja incarcata.';
				$message_type = 'info';
			}
			
			if(!$error){
				$this->redirect('backend/trip/orders/invoices?id=' . $id, $message, $message_type);
			}
		}
		if($error){
			$this->redirect('backend/trip/orders/invoices?id=' . $id, $error, 'error');
		}
	}
	public function remove_invoice(){
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders/invoices?id=' . $id, 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		if (!$can_edit) {
			$can_edit_own = $can_access_own && $this->user->can('backend-trip-orders-own-edit');
			$can_edit = (($order->created_by == $this->user->id || $this->user->user_role == 'pay24_comenzi' && $order->payment_gateway == 'pay24')) && $can_edit_own;
		}
		if (!$can_edit) {
			$this->redirect('backend/trip/orders/invoices?id=' . $id, 'Acces restrictionat', 'error');
		}
		$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		
		$file_deposit_path = $facturi_path . $id . '.pdf';
		
		$ext = substr(strrchr($pdf['name'], '.'), 1);
		
		$error = null;
		if(!$error){
			$renamed = false;
			$exists = false;
			if(is_file($file_deposit_path)){
				$exists = true;
				if(sha1_file($pdf['tmp_name']) != sha1_file($file_deposit_path)){
					$renamed = rename($file_deposit_path, $facturi_path . $id . '-' . date('Y-m-d H:i:s',filectime($file_deposit_path)) . '.pdf');
				}
			}
			$message = 'Factura eliminata cu succes';
			$message_type = 'success';
			if(!$error){
				$this->redirect('backend/trip/orders/invoices?id=' . $id, $message, $message_type);
			}
		}
		if($error){
			$this->redirect('backend/trip/orders/invoices?id=' . $id, $error, 'error');
		}
	}
	public function invoices()
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		
		$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		if(!$facturi_path || !is_dir($facturi_path)){
			$facturi_path = false;
		} 
		themeFunctions::loadLang('general/alert');
		?>
		<div id="system_messages" class="container"><?php if($system_messages = $this->session->flashdata('flashmsgs')){
		  foreach($system_messages as $type=>$messages){
			$message_type = $type=='error' ? 'danger' : (in_array($type, array('success','danger','info','warning')) ? $type : 'info');
			foreach($messages as $message){
		  ?>
		  <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
			<strong><?php echo lang('alert_' . $message_type . '/html'); ?></strong> <?php echo $message; ?>
		  </div>
		  <?php
			}
		  }
			$this->session->set_flashdata('flashmsg', null);
			$this->session->set_flashdata('flashmsgtype', null);
			$this->session->set_flashdata('flashmsgs', []);
		} ?>
		</div>
		<?php
		
		if($facturi_path && is_file($facturi_path . $id . '.pdf')){
			$url = site_url('backend/trip/orders/invoice/' . $id . '.pdf');
			echo '<div><a href="' . $url . '" target="_BLANK">Vezi factura incarcata (link accesibil doar conectat in administrare, cu drept de acces pe comanda)</a></div>';
			$url = site_url('backend/trip/orders/remove_invoice?id=' . $id);
			echo '<div><a href="' . $url . '" onclick="if(!confirm(\'Esti pe cale sa elimini factura din comanda. Esti sigur?\')){ event.preventDefault(); event.stopPropagation(); return false;}">Elimina factura incarcata</a></div>';
		} else {
			echo '<div>Nicio factura incarcata</div>';
		}
	}
	public function invoice($filename)
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$filename_arr = explode('.', '' . $filename);
		
		$id = $filename_arr[0];
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		
		$facturi_path = realpath(APPPATH . '../../facturi') . '/';
		if(!$facturi_path || !is_dir($facturi_path)){
			$facturi_path = false;
		}
		if($facturi_path && is_file($facturi_path . $id . '.pdf')){
			header('Content-Description: ' . htmlspecialchars('Factura_' . $id . '.pdf'));
			header('Content-Disposition: inline; filename='.htmlspecialchars('Factura_' . $id . '.pdf'));
			header('Content-Type: application/pdf');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			ob_clean();
			readfile($facturi_path . $id . '.pdf');
		} else {
			header('HTTP/1.0 404 Not found');
			echo 'The download was not found';
			exit;
		}
	}
	
	public function upload_bilet(){
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			echo 'Comanda invalida'; exit;
			$this->redirect('backend/trip/orders/bilets?id=' . $id, 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		
		if (!$can_edit) {
			$can_edit_own = $can_access_own && $this->user->can('backend-trip-orders-own-edit');
			$can_edit = (($order->created_by == $this->user->id || $this->user->user_role == 'pay24_comenzi' && $order->payment_gateway == 'pay24')) && $can_edit_own;
		}
		if (!$can_edit) {
			echo 'Acces restrictionat'; exit;
			// $this->redirect('backend/trip/orders/bilets?id=' . $id, 'Acces restrictionat', 'error');
		}
		$pdf = isset($_FILES['pdf']) ? $_FILES['pdf'] : array();
		
		$facturi_path = realpath(APPPATH . '../../') . '/bilete/';
		
		$file_deposit_path = $facturi_path . $id . '.pdf';
		
		$ext = substr(strrchr($pdf['name'], '.'), 1);
		
		$error = null;
		
		if(!is_dir($facturi_path) ){
			if(!mkdir($facturi_path)){
				$error = 'Could not create bilete folder';
			}
		}
		for(;;){
			if($error) break;
			if(!$pdf) { $error = 'No file uploaded'; break; }
			if($pdf['error']) { $error = 'Error uploading to tmp ' . $pdf['error'] . ''; break; }
			if($pdf['type'] != 'application/pdf') { $error = 'Mime ' . $pdf['type'] . ' not supported. Only application/pdf allowed'; break; }
			if($ext != 'pdf') { $error = 'Extension ' . $ext . ' not supported. Only pdf allowed'; break; }
			break;
		}
		if(!$error){
			$renamed = false;
			$exists = false;
			if(is_file($file_deposit_path)){
				$exists = true;
				if(sha1_file($pdf['tmp_name']) != sha1_file($file_deposit_path)){
					$renamed = rename($file_deposit_path, $facturi_path . $id . '-' . date('Y-m-d H:i:s',filectime($file_deposit_path)) . '.pdf');
				}
			}
			$message = 'Bilet incarcat cu succes';
			$message_type = 'success';
			if(!$exists || $renamed){
				$moved = move_uploaded_file($pdf['tmp_name'], $file_deposit_path);
				if(!$moved){
					$error = 'Failed to move file from tmp to storage folder';
				}
			} else {
				$message = 'Biletul incarcat este echivalent cu cel deja incarcat.';
				$message_type = 'info';
			}
			
			if(!$error){
				$this->redirect('backend/trip/orders/bilets?id=' . $id, $message, $message_type);
			}
		}
		if($error){
			$this->redirect('backend/trip/orders/bilets?id=' . $id, $error, 'error');
		}
	}
	public function remove_bilet(){
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders/bilets?id=' . $id, 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		if (!$can_edit) {
			$can_edit_own = $can_access_own && $this->user->can('backend-trip-orders-own-edit');
			$can_edit = (($order->created_by == $this->user->id || $this->user->user_role == 'pay24_comenzi' && $order->payment_gateway == 'pay24')) && $can_edit_own;
		}
		if (!$can_edit) {
			$this->redirect('backend/trip/orders/bilets?id=' . $id, 'Acces restrictionat', 'error');
		}
		$facturi_path = realpath(APPPATH . '../../bilete') . '/';
		
		$file_deposit_path = $facturi_path . $id . '.pdf';
		
		$ext = substr(strrchr($pdf['name'], '.'), 1);
		
		$error = null;
		if(!$error){
			$renamed = false;
			$exists = false;
			if(is_file($file_deposit_path)){
				$exists = true;
				if(sha1_file($pdf['tmp_name']) != sha1_file($file_deposit_path)){
					$renamed = rename($file_deposit_path, $facturi_path . $id . '-' . date('Y-m-d H:i:s',filectime($file_deposit_path)) . '.pdf');
				}
			}
			$message = 'Bilet eliminat cu succes';
			$message_type = 'success';
			if(!$error){
				$this->redirect('backend/trip/orders/bilets?id=' . $id, $message, $message_type);
			}
		}
		if($error){
			$this->redirect('backend/trip/orders/bilets?id=' . $id, $error, 'error');
		}
	}
	public function bilets()
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		
		$facturi_path = realpath(APPPATH . '../../bilete') . '/';
		if(!$facturi_path || !is_dir($facturi_path)){
			$facturi_path = false;
		} 
		themeFunctions::loadLang('general/alert');
		?>
		<div id="system_messages" class="container"><?php if($system_messages = $this->session->flashdata('flashmsgs')){
		  foreach($system_messages as $type=>$messages){
			$message_type = $type=='error' ? 'danger' : (in_array($type, array('success','danger','info','warning')) ? $type : 'info');
			foreach($messages as $message){
		  ?>
		  <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
			<strong><?php echo lang('alert_' . $message_type . '/html'); ?></strong> <?php echo $message; ?>
		  </div>
		  <?php
			}
		  }
			$this->session->set_flashdata('flashmsg', null);
			$this->session->set_flashdata('flashmsgtype', null);
			$this->session->set_flashdata('flashmsgs', []);
		} ?>
		</div>
		<?php
		
		if($facturi_path && is_file($facturi_path . $id . '.pdf')){
			$url = site_url('backend/trip/orders/bilet/' . $id . '.pdf');
			echo '<div><a href="' . $url . '" target="_BLANK">Vezi biletul incarcat (link accesibil doar conectat in administrare, cu drept de acces pe comanda)</a></div>';
			$url = site_url('backend/trip/orders/remove_bilet?id=' . $id);
			echo '<div><a href="' . $url . '" onclick="if(!confirm(\'Esti pe cale sa elimini biletul din comanda. Esti sigur?\')){ event.preventDefault(); event.stopPropagation(); return false;}">Elimina biletul incarcat</a></div>';
		} else {
			echo '<div>Niciun bilet incarcat</div>';
		}
	}
	public function bilet($filename)
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$filename_arr = explode('.', '' . $filename);
		
		$id = $filename_arr[0];
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
		
		$facturi_path = realpath(APPPATH . '../../bilete') . '/';
		if(!$facturi_path || !is_dir($facturi_path)){
			$facturi_path = false;
		}
		if($facturi_path && is_file($facturi_path . $id . '.pdf')){
			header('Content-Description: ' . htmlspecialchars('Bilet_' . $id . '.pdf'));
			header('Content-Disposition: inline; filename='.htmlspecialchars('Bilet_' . $id . '.pdf'));
			header('Content-Type: application/pdf');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			ob_clean();
			readfile($facturi_path . $id . '.pdf');
		} else {
			header('HTTP/1.0 404 Not found');
			echo 'The download was not found';
			exit;
		}
	}
	public function view()
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		$provider = $order->provider;

		$can_access = $this->user->can('backend-trip-orders-access');
		$can_view = $can_access && $this->user->can('backend-trip-orders-view');
		if (!$can_view) {
			$can_access_own = $can_access || $this->user->can('backend-trip-orders-own-access');
			$can_view_own = $can_access_own && $this->user->can('backend-trip-orders-own-view');
			$can_view = ($user->created_by == $this->user->id) && $can_view_own;
		}
		if (!$can_view) {
			$this->redirect('backend/trip/orders', 'Acces restrictionat', 'error');
		}
		$this->data['order'] = $order;
		$this->load->model('TripOrderCoupon_model');
		$this->data['coupons'] = $this->TripOrderCoupon_model->getOrderCouponsByOrderId($order->id);

		$this->load->model('Ticket_model');
		$ticket = $this->Ticket_model->getTicketByOrderId($order->id);
		if (!$ticket) {
			$this->load->library('Ticket');
			$ticket = new Ticket;
			$ticket->trip_order_id = $order->id;
		}

		$this->data['ticket'] = &$ticket;
		$this->data['users'] = $this->Ticket_model->getAllowedUsers();
		if ($provider === 'paralela45') {
			$this->theme->view('backend/trip/item/paralela45', $this->data);
			return;
		} elseif ($provider === 'trip') {
			$trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
			$order->trip_order = $trip_order;
			$this->theme->view('backend/trip/item/orders', $this->data);
			return;
		}
		$this->redirect('backend/trip/orders', 'Furnizor invalid', 'error');
	}
	public function delete()
	{
		if (!$this->user->can('backend-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->redirect('backend', 'Acces restrictionat', 'error');
		}
		$id = (int)$this->input->get('id');
		$this->load->model('TripOrder_model');
		$order = $this->TripOrder_model->getOrderById($id);
		if (!$order || !$order->id) {
			$this->redirect('backend/trip/orders', 'Comanda invalida', 'error');
		}
		$can_access = $this->user->can('backend-trip-orders-access');
		$can_delete = $can_access && $this->user->can('backend-trip-orders-delete');
		if (!$can_delete) {
			$can_access_own = $can_access || $this->user->can('backend-trip-orders-own-access');
			$can_delete_own = $can_access_own && $this->user->can('backend-trip-orders-own-delete');
			$can_delete = ($user->created_by == $this->user->id) && $can_delete_own;
		}
		if (!$can_delete) {
			$this->redirect('backend/trip/orders', 'Acces restrictionat', 'error');
		}
		$this->TripOrder_model->deleteOrderById($id);
		$this->redirect('backend/trip/orders', 'Comanda a fost stersa', 'success');
	}
	/* public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
      $this->outputError('Acces restrictionat');
    }
    $id = (int)$this->input->post('id');
    $data = array();
    $data['user_id'] = 0;
    if($id){
      $this->load->model('Account_model');
      $user = $this->Account_model->getAccountById($id);
      $invalid_user = !$user || ($user->type !== 'orders') || ($user->id == $this->user->id);
      if($invalid_user){
        $this->outputError('Invalid user');
      }
      $can_access = $this->user->can('backend-trip-orders-access');
      $can_edit = $can_access && $this->user->can('backend-trip-orders-edit');
      if(!$can_edit){
        $can_access_own = $can_access || $this->user->can('backend-trip-orders-own-access');
        $can_edit_own = $can_access_own && $this->user->can('backend-trip-orders-own-edit');
        $can_edit = ($user->created_by == $this->user->id) && $can_edit_own;
      }
      if(!$can_edit){
        $this->outputError('Acces restrictionat');
      }
      $data['user_id'] = $user->id;
    } else {
      if(!$this->user->can('backend-trip-orders-add')){
        $this->outputError('Acces restrictionat');
      }
      $this->load->library('user');
      $user = new User;
      $user->type = 'orders';
    }
    
    $this->load->library('form_validation');
    
    $changed_email = false;
    $email = trim($this->input->post('email'));
    if(!$user->id || $user->username != $email){
      $changed_email = true;
    }
    $this->form_validation->set_rules('email', 'Adresa email', 'trim|required|valid_email' . ($changed_email ? '|is_unique[ac_user.user_username]' : ''));
    
    $password = $this->input->post('password');
    if (!$user->id || !empty($password)) {
      $this->form_validation->set_rules('password', 'Parola', 'min_length[8]');
    }
    $this->form_validation->set_rules('status', 'Status', 'in_list[0,1]');
    
    $this->load->model('Account_model');
    
    $should_validate = true;
    $this->Account_model->applyGeneralFormValidation($this,$user,$data,$should_validate);
    
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $data['user_type'] = 'orders';
    $data['user_username'] = trim($this->input->post('email'));
    $data['user_email'] = trim($this->input->post('email'));
    $data['user_status'] = (int)$this->input->post('status');
    $data['user_role'] = null;
    $data['user_firstname'] = trim($this->input->post('firstname'));
    $data['user_lastname'] = trim($this->input->post('lastname'));
    if($this->input->post('password')){
      $data['user_password'] = sha1($this->input->post('password'));
    }
    $new_user = false;
    if(!$id){
      $new_user = true;
      $data['user_created_by'] = $this->user->id;
      $data['user_created_datetime'] = date("Y-m-d H:i:s");
    } else {
      $data['user_modified_by'] = $this->user->id;
      $data['user_modified_datetime'] = date("Y-m-d H:i:s");
    }
    $this->Account_model->applyGeneralFormSaveAdaptation($this,$user,$data);
    
    $id = $this->Account_model->saveAccount($data);
    
    
    $this->data['id'] = $id;
    $this->data['edit_link'] = site_url('backend/trip/orders/edit?id='. $id);
    if($new_user){
      $this->addMessage('Utilizatorul a fost creat','success');
    } else {
      $this->addMessage('Informatiile au fost actualizate','success');
    }
    $this->saveMessagesInSession();
    $this->output();
  } */
	public function save_client()
	{
		$is_ajax_request = $this->input->is_ajax_request();
		if (!$this->user->can('backend-access')) {
			$this->outputError('Acces restrictionat');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->outputError('Acces restrictionat');
		}

		$this->load->library('form_validation');
		$id = $this->input->post('order_id');
		$provider = $this->input->post('provider');
		if ($id) {
			$this->form_validation->set_rules('order_id', 'Comanda', 'required|valid_order_id', array(
				'required' => 'Comanda invalida',
				'valid_order_id' => 'Comanda invalida',
			));
		}
		$this->form_validation->set_rules('user_id', 'Client', 'valid_user_id', array(
			'valid_user_id' => 'User invalid',
		));
		$this->form_validation->set_rules('provider', 'Furnizor', 'trim|required|in_list[trip,paralela45]', array(
			'in_list' => 'Alegere invalida',
		));
		$this->form_validation->set_rules('user_invoice', 'Facturare', 'trim|required|in_list[pf,pj]', array(
			'in_list' => 'Alegere invalida',
		));
		$invoice = $this->input->post('user_invoice');
		if ($invoice == 'pj') {
			$this->form_validation->set_rules('user_company_name', 'Companie', 'trim|required|max_length[255]');
			$this->form_validation->set_rules('user_cui', 'CUI', 'trim|max_length[50]|validate_CIF_or_CUI', array(
				'validate_CIF_or_CUI' => 'Codul CUI introdus este invalid',
			));
			$this->form_validation->set_rules('user_iban', 'IBAN', 'trim|required|max_length[50]|valid_iban', array(
				'valid_iban' => 'Codul IBAN introdus este invalid',
			));
			$this->form_validation->set_rules('user_bank', 'Banca', 'trim|required|max_length[255]');
			$this->form_validation->set_rules('user_regcom', 'Nr.Reg.Com.', 'trim|required|max_length[255]');
		}
		$this->form_validation->set_rules('user_title', 'Titlu', 'trim|required|in_list[mr,mrs,ms]', array(
			'in_list' => 'Titlu invalid',
		));
		$this->form_validation->set_rules('user_lastname', 'Nume', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('user_firstname', 'Prenume', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('user_country', 'Tara', 'trim|required|valid_country[iso_2]', array(
			'valid_country' => 'Tara invalida',
		));
		$this->form_validation->set_rules('user_city', 'Oras', 'trim|max_length[255]');
		$this->form_validation->set_rules('user_address', 'Adresa facturare', 'trim|max_length[255]');
		$this->form_validation->set_rules('user_street', 'Strada', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('user_street_no', 'Numar strada', 'trim|max_length[20]');
		$this->form_validation->set_rules('user_postal_code', 'Cod postal', 'trim|max_length[50]');
		$this->form_validation->set_rules('user_phone_prefix', 'Prefix telefon', 'trim|valid_country[iso_2]', array(
			'valid_country' => 'Tara invalida',
		));
		$this->form_validation->set_rules('user_phone', 'Telefon', 'trim|max_length[100]');
		$this->form_validation->set_rules('user_email', 'Email', 'trim|required|max_length[255]|valid_email');
		$this->form_validation->set_rules('birth_date', 'Data nastere', 'trim|valid_date[d.m.Y]', array(
			'valid_date' => 'Formatul datei este invalid',
		));
		if ($this->form_validation->run() == FALSE) {
			$this->data['errors'] = $this->form_validation->error_array();
			$this->outputError($this->form_validation->error_string());
		}
		$id = $this->input->post('order_id');

		$data = array();
		$data['id'] = null;
		$data['provider'] = $provider;
		if ($id) {
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Invalid order');
			}
			if ($order->provider != $provider) {
				$this->outputError('Invalid order provider');
			}

			$data['id'] = $order->id;
			$data['provider'] = $order->provider;
		}
		$user_id = $this->input->post('user_id');
		$data['user_id'] = $user_id ? $user_id : null;

		$fields = array(
			'user_invoice',
			'user_title',
			'user_lastname',
			'user_firstname',
			'user_country',
			'user_city',
			'user_address',
			'user_street',
			'user_street_no',
			'user_postal_code',
			'user_phone_prefix',
			'user_phone',
			'user_email',
			'user_birth_date',
		);
		$user_invoice = $this->input->post('user_invoice');
		$data['user_company_name'] = null;
		$data['user_cui'] = null;
		$data['user_iban'] = null;
		$data['user_regcom'] = null;
		$data['user_bank'] = null;
		if ($user_invoice == 'pj') {
			$fields[] = 'user_company_name';
			$fields[] = 'user_cui';
			$fields[] = 'user_iban';
			$fields[] = 'user_regcom';
			$fields[] = 'user_bank';
		}
		foreach ($fields as $field) {
			$data[$field] = null;
			$value = $this->input->post($field);
			if (isset($value) && strlen(trim($value))) {
				$data[$field] = trim($value);
			}
		}
		if (isset($data['user_birth_date'])) {
			$date = DateTime::createFromFormat('d.m.Y', $data['user_birth_date']);
			$data['user_birth_date'] = $date ? $date->format('Y-m-d') : null;
		}
		$this->data['results'] = &$data;
		if (!$id && $is_ajax_request) {
			$this->addMessage('Informatiile au fost validate', 'success');
			$this->output();
		}
		$this->load->model('TripOrder_model');
		if ($provider === 'trip') {
			$trip_order_id = $order->trip_order_id;
			if ($trip_order_id) {
				$response = $this->TripOrder_model->saveTripClient($trip_order_id, $data);
				if (!$response) {
					$this->outputTripError('Trip Error: Nu s-au putut actualiza informatiile clientului');
				}
				$trip_order = $this->TripOrder_model->getTripOrder($trip_order_id);
				$this->response = $trip_order;
			}
		}
		$order_id = $this->TripOrder_model->saveOrder($data);
		$data['id'] = $order_id;
		if ($is_ajax_request) {
			$this->addMessage('Informatiile au fost salvate', 'success');
			$this->output();
		}
		$this->redirect('backend/trip/orders/edit?id=' . $order_id, 'Informatiile clientului au fost asociate acestei comenzi noi.', 'success');
	}

	public function save_order()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$id = $this->input->post('order_id');
			if (!$id) {
				$this->outputError('Comanda invalida.');
			}
			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$this->load->model('TripCoupon_model');
			$this->load->model('TripOrderCoupon_model');
			
			$coupon_codes = $this->input->post('coupon_codes');
			$apply_coupons = false;
			if(isset($coupon_codes)){
				if(empty($coupon_codes)){
					$coupon_codes = array();
				}
				$apply_coupons = true;
				
				$coupons = $this->TripOrderCoupon_model->getOrderCouponsByOrderId($order->id);
				$previous_coupon_codes = array();
				foreach ($coupons as $coupon) {
					$previous_coupon_codes[] = strtoupper($coupon->coupon_code);
				}

				$coupon_codes = array_map('trim', $coupon_codes);
				$coupon_codes = array_map('strtoupper', $coupon_codes);
				$coupon_codes = array_unique($coupon_codes);
				$coupon_codes = array_values($coupon_codes);

				$new_coupon_codes = array_diff($coupon_codes, $previous_coupon_codes);
				$remove_coupon_codes = array_diff($previous_coupon_codes, $coupon_codes);

				$order_coupons = array();
				$valid_new_coupon_codes = array();
				foreach ($new_coupon_codes as $k => $new_coupon_code) {
					$coupon = $this->TripCoupon_model->getCouponByCode($new_coupon_code, array('join_child' => 1));
					if ($coupon) {
						$valid_new_coupon_codes[] = $new_coupon_code;
						$order_coupons[] = array(
							'id' => $coupon->id,
							'code' => $coupon->code,
							'discount' => $coupon->percentage,
							'discount_type' => $coupon->discount_type,
							'amount_ron' => $coupon->fixed_ron,
							'amount_eur' => $coupon->fixed_eur,
						);
					}
				}
				foreach ($coupons as $coupon) {
					if (!in_array(strtoupper($coupon->coupon_code), $remove_coupon_codes)) {
						$order_coupons[] = array(
							'id' => $coupon->coupon_id,
							'code' => $coupon->coupon_code,
							'discount' => $coupon->coupon_percentage,
							'discount_type' => $coupon->coupon_discount_type,
							'amount_ron' => $coupon->coupon_fixed_ron,
							'amount_eur' => $coupon->coupon_fixed_eur,
						);
					}
				}

				$order_coupons = $this->TripCoupon_model->orderCouponsByAppliance($order_coupons);

				$coupons = array();
				$currency_code = $order->currency;
				$total = $order->total - $order->service_discount;
				$undiscounted_amount = $total;
				$full_coupon_amount = 0;
				foreach ($order_coupons as $k => $coupon) {
					$coupon_code = $coupon['code'];
					$coupon_amount = 0;
					if ($total > 0) {
						if ($coupon['discount_type'] == 'P') {
							$coupon_amount = ($total * $coupon['discount']) / 100;
						} else {
							if ($currency_code == 'RON') {
								$coupon_amount = $coupon['amount_ron'];
							} elseif ($currency_code == 'EUR') {
								$coupon_amount = $coupon['amount_eur'];
							}
						}
						$coupon_amount = max(min($coupon_amount, $total), 0);
						$total = $total - $coupon_amount;
						$full_coupon_amount += $coupon_amount;
					}
					$coupon['amount'] = $coupon_amount;
					$coupon['subtotal'] = $total;
					$coupons[] = $coupon;
				}
			}


			$this->load->library('form_validation');
			$should_validate = false;
			$data = array();

			$status = $this->input->post('status');
			if (isset($status)) {
				$should_validate = true;
				$this->form_validation->set_rules('status', 'Status', 'in_list[0,1,2,3]', array(
					'in_list' => 'Status invalid',
				));
				$data['status'] = $status;
				$data['message'] = 'Modificat manual';
			}
			$payment_method = $this->input->post('payment_method');
			if (isset($payment_method)) {
				$should_validate = true;
				$this->form_validation->set_rules('payment_method', 'payment_method', 'in_list[' . ($total <= 0.00001 ? 'free' : 'bank,agency,online') . ']', array(
					'in_list' => 'Metoda de plata invalida' . ($total <= 0.00001 ? '. Alegeti gratuit. totalul este 0' : ''),
				));
				$data['payment_method'] = $payment_method;
			}
			if($apply_coupons){
				$data['amount'] = $total;

				$data['coupon_percentage'] = $undiscounted_amount > 0 ? $full_coupon_amount / $undiscounted_amount * 100 : 100;
				$data['coupon_amount'] = $full_coupon_amount;
			}

			$notify_customer = filter_var($this->input->post('notify_customer'), FILTER_VALIDATE_BOOLEAN);

			if ($should_validate && $this->form_validation->run() == FALSE) {
				$this->data['errors'] = $this->form_validation->error_array();
				$this->outputError($this->form_validation->error_string());
			}

			if ($data) {
				$data['id'] = (int)$id;
				$this->TripOrder_model->saveOrder($data);
				if($apply_coupons){
					foreach ($remove_coupon_codes as $remove_coupon_code) {
						$this->TripCoupon_model->unUseCoupon($remove_coupon_code);
					}
					foreach ($valid_new_coupon_codes as $valid_new_coupon_code) {
						$this->TripCoupon_model->useCoupon($valid_new_coupon_code);
					}
					$this->TripOrderCoupon_model->deleteOrderCouponByOrderId($order->id);

					foreach ($coupons as $coupon) {
						$this->TripOrderCoupon_model->saveOrderCoupon(array(
							'order_id' => $id,
							'coupon_id' => $coupon['id'],
							'coupon_code' => $coupon['code'],
							'coupon_discount_type' => $coupon['discount_type'],
							'coupon_percentage' => $coupon['discount'],
							'coupon_fixed_ron' => $coupon['amount_ron'],
							'coupon_fixed_eur' => $coupon['amount_eur'],
							'coupon_amount' => $coupon['amount'],
							'order_subtotal' => $coupon['subtotal'],
							'coupon_currency' => $order->currency,
							'time_created' => date('Y-m-d H:i:s'),
						));
					}
				}
				$this->data['results'] = $data;
				if ($notify_customer) {
					Modules::run('Mailer/checkout_auto', array('order_id' => (int)$id));
				}
				$this->addMessage('Informatiile au fost salvate', 'success');
			} else {
				$this->addMessage('Nicio modificare efectuata', 'info');
			}
			$this->output();
		}
		$this->redirect('', 'Acces invalid', 'error');
	}

	public function setPaymentMethod()
	{
		if (!$this->input->is_ajax_request()) {
			$this->redirect('backend', 'Acces invalid', 'error');
		}
		if (!$this->user->can('backend-access')) {
			$this->outputError('Acces restrictionat');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->outputError('Acces restrictionat');
		}
		$this->load->model('TripOrder_model');
		$id = (int)$this->input->post('order_id');
		$order = $this->TripOrder_model->getOrderById($id);
		if (!$order) {
			$this->outputError('Comanda invalida.');
		}
		$trip_order_id = $order->trip_order_id;
		if (!$trip_order_id) {
			$this->outputError('Comanda nu are asociat ID in TRIP');
		}
		$payment_method = $this->input->post('payment_method');
		$response = $this->TripOrder_model->setTripPaymentMethod($trip_order_id, $payment_method);
		if (!$response) {
			$this->outputTripError('Trip Error: Nu s-a putut stabili metoda de plata a comenzii');
		}
		$this->data = $response;
		$this->addMessage('Metoda de plata a fost sabilita', 'success');
		$this->output();
	}
	public function setPaymentStatus()
	{
		if (!$this->input->is_ajax_request()) {
			$this->redirect('backend', 'Acces invalid', 'error');
		}
		if (!$this->user->can('backend-access')) {
			$this->outputError('Acces restrictionat');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->outputError('Acces restrictionat');
		}
		$this->load->model('TripOrder_model');
		$id = (int)$this->input->post('order_id');
		$order = $this->TripOrder_model->getOrderById($id);
		if (!$order) {
			$this->outputError('Comanda invalida.');
		}
		$trip_order_id = $order->trip_order_id;
		if (!$trip_order_id) {
			$this->outputError('Comanda nu are asociat ID in TRIP');
		}
		$payment_status = (int)$this->input->post('payment_status');
		$payment_status_message = trim($this->input->post('payment_status_message'));
		$response = $this->TripOrder_model->setTripPaymentStatus($trip_order_id, $payment_status, $payment_status_message);
		if (!$response) {
			$this->outputTripError('Trip Error: Nu s-a putut stabili statusul platii comenzii');
		}
		$this->data = $response;
		$this->addMessage('Statusul platii a fost sabilit', 'success');
		$this->output();
	}
	public function getPaymentMethods()
	{
		if (!$this->input->is_ajax_request()) {
			$this->redirect('backend', 'Acces invalid', 'error');
		}
		if (!$this->user->can('backend-access')) {
			$this->outputError('Acces restrictionat');
		}
		if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
			$this->outputError('Acces restrictionat');
		}
		$this->load->model('TripOrder_model');
		$id = (int)$this->input->post('order_id');
		$order = $this->TripOrder_model->getOrderById($id);
		if (!$order) {
			$this->outputError('Comanda invalida.');
		}
		$trip_order_id = $order->trip_order_id;
		if (!$trip_order_id) {
			$this->outputError('Comanda nu are asociat ID in TRIP');
		}
		$payment_methods = $this->TripOrder_model->getTripPaymentMethods($trip_order_id);
		if (!$payment_methods) {
			$this->outputTripError('Trip Error: Nu s-au putut prelua metodele de plata ale comenzii');
		}
		$this->data = $payment_methods;
		$this->output();
	}
	public function getOrderService()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$id = (int)$this->input->post('order_id');
			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}
			$service_id = $this->input->post('service_id');
			if (!$service_id) {
				$this->outputError('Nu ati specificat ID serviciu');
			}
			$trip_service = $this->TripOrder_model->getTripService($trip_order_id, $service_id);
			if (!$trip_service) {
				$this->outputTripError('Trip Error: Nu s-a putut prelua serviciul comenzii');
			}
			switch ($trip_service->Type) {
				case 'hotel':
					$trip_service->Hotel->link = site_url('trip/hotel/' . $trip_service->Hotel->Id);
					break;
				case 'package':
					$trip_service->Package->link = site_url('trip/package/' . $trip_service->Package->Id);
					break;
			}
			$this->data = &$trip_service;
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
	public function bookServices()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$id = (int)$this->input->post('order_id');
			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}
			Modules::run('Mailer/checkout_auto', array('order_id' => $id));
			if (!config_item('trip_no_booking')) {
				$booking_response = $this->TripOrder_model->bookAllTripServices($trip_order_id);
				if (!$booking_response) {
					$this->outputTripError('Trip Error: Nu s-a putut efectua rezervarea');
				}
				$this->addMessage('Rezervarea a fost efectuata', 'success');
			} else {
				$this->addMessage('Rezervarea a fost efectuata. Booking-ul a fost temporar dezactivat.', 'success');
			}
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
	public function removeOrderServices()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$id = (int)$this->input->post('order_id');
			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}
			$service_id = $this->input->post('service_id');
			$trip_services = $this->TripOrder_model->removeTripService($trip_order_id, $service_id);

			if (false === $trip_services) {
				$this->outputTripError('Trip Error: Nu s-au putut sterge serviciul comenzii');
			}

			$trip_services = $this->TripOrder_model->getTripServices($trip_order_id);
			if (!$trip_services) {
				$this->outputTripError('Trip Error: Nu s-au putut prelua serviciile comenzii');
			}

			$data = array();
			$data['id'] = $id;
			$data['services_citybreak'] = null;
			$data['services_flight'] = null;
			$data['services_hotel'] = null;
			$data['services_package'] = null;
			$types = array();
			foreach ($trip_services->_embedded->services as $serv) {
				$serv_type = $serv->Type;
				if (!isset($types[$serv_type])) {
					$types[$serv_type] = 0;
					$data['services_' . $serv_type] = 0;
				}
				$types[$serv_type]++;
				$data['services_' . $serv_type]++;
			}
			$data['type'] = implode(',', array_keys($types));

			$trip_order = $this->TripOrder_model->getTripOrder($trip_order_id);
			$data['amount'] = $trip_order->Amount * (1 - $order->coupon_percentage / 100);
			$data['total'] = $trip_order->Amount;
			$data['currency'] = $trip_order->Currency;

			$this->TripOrder_model->saveOrder($data);

			$this->addMessage('Serviciul a fost eliminat', 'success');
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
	public function loadOrderServices()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$id = (int)$this->input->post('order_id');
			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}
			$trip_services = $this->TripOrder_model->getTripServices($trip_order_id);
			if (!$trip_services) {
				$this->outputTripError('Trip Error: Nu s-a putut prelua serviciul comenzii');
			}
			$this->data = $trip_services;
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
	public function addFlightService()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$this->load->library('form_validation');
			$this->form_validation->set_rules('order_id', 'ID comanda', 'required');
			$this->form_validation->set_rules('flight_code', 'Cod cautare', 'required');
			$this->form_validation->set_rules('itinerary_code', 'Cod itinerar', 'required', array(
				'required' => 'Alegeti un zbor'
			));

			$id = (int)$this->input->post('order_id');

			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}

			$flight_code = trim($this->input->post('flight_code'));
			$itinerary_code = trim($this->input->post('itinerary_code'));
			$comment = trim($this->input->post('comment'));

			$this->form_validation->set_rules('comment', 'Comentariu', 'trim|max_length[1024]');

			if ($this->form_validation->run() == FALSE) {
				$this->data['errors'] = $this->form_validation->error_array();
				$this->outputError($this->form_validation->error_string());
			}
			$passengers = isset($_POST['passenger']) && is_array($_POST['passenger']) ? $_POST['passenger'] : array();
			$_POST['passenger'] = [];
			foreach($passengers as $ptc => $ptc_passengers){
				foreach($ptc_passengers as $ptc_passenger_index => $flight_passenger){
				  
				  $_POST['passenger']['title'][] = $flight_passenger['title'] ?? 'mr';
				  $_POST['passenger']['birth_date'][] = date("d.m.Y", strtotime($flight_passenger['birthDate'] ?? date('Y-m-d')));
				  $_POST['passenger']['firstname'][] = $flight_passenger['firstName'] ?? '';
				  $_POST['passenger']['lastname'][] = $flight_passenger['lastName'] ?? '';
				  $_POST['passenger']['country'][] = $flight_passenger['country'] ?? '';
				  $_POST['passenger']['email'][] = $flight_passenger['email'] ?? '';
				  $_POST['passenger']['phone'][] = $flight_passenger['phone'] ?? '';
				  
				  if(isset($flight_passenger['details']) && is_array($flight_passenger['details'])){
					  $_POST['preferredSeats'][$ptc][$ptc_passenger_index]['details'] = $flight_passenger['details'];
				  }
				}
			}
			
			$_POST['payment_gateway'] = 'manual';
			$_POST['payment_method'] = 'backend';
			$this->makeResponseGlobal();
			$response = modules :: run('Trip/checkout/Checkout/service', 'flight', 'backend');
			if(!$response){
				$this->outputTripError(null);
			}
			$service = $response['flight'];
			
			// echo '<pre>';
			// print_r($service);
			// die;
			
			$add_trip_service_response = $this->TripOrder_model->addTripService($trip_order_id, $service);
			if (!$add_trip_service_response) {
				$this->outputTripError('Trip Error: Nu s-a putut adauga serviciul comenzii');
			}

			$trip_services = $this->TripOrder_model->getTripServices($trip_order_id);
			if (!$trip_services) {
				$this->outputTripError('Trip Error: Nu s-au putut prelua serviciile comenzii');
			}

			$data = array();
			$data['id'] = $id;
			$data['services_citybreak'] = null;
			$data['services_flight'] = null;
			$data['services_hotel'] = null;
			$data['services_package'] = null;
			$types = array();
			foreach ($trip_services->_embedded->services as $serv) {
				$serv_type = $serv->Type;
				if (!isset($types[$serv_type])) {
					$types[$serv_type] = 0;
					$data['services_' . $serv_type] = 0;
				}
				$types[$serv_type]++;
				$data['services_' . $serv_type]++;
			}
			$data['type'] = implode(',', array_keys($types));
			$trip_order = $this->TripOrder_model->getTripOrder($trip_order_id);
			$data['amount'] = $trip_order->Amount * (1 - $order->coupon_percentage / 100);
			$data['total'] = $trip_order->Amount;
			$data['currency'] = $trip_order->Currency;

			$this->data['service'] = $service;
			$this->data['added_service'] = $add_trip_service_response;
			$this->data['services'] = $trip_services;
			$this->data['flight_code'] = $flight_code;
			$this->data['itinerary_code'] = $itinerary_code;
			$this->data['passenger'] = $passenger;
			$this->data['comment'] = $comment;


			$this->TripOrder_model->saveOrder($data);

			$this->addMessage('Serviciul a fost adaugat', 'success');
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
	public function addCitybreakService()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$this->load->library('form_validation');
			$this->form_validation->set_rules('order_id', 'ID comanda', 'required');
			$this->form_validation->set_rules('flight_code', 'Cod cautare', 'required');
			$this->form_validation->set_rules('itinerary_code', 'Cod itinerar', 'required', array(
				'required' => 'Alegeti un zbor'
			));

			$id = (int)$this->input->post('order_id');

			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}

			$flight_code = trim($this->input->post('flight_code'));
			$itinerary_code = trim($this->input->post('itinerary_code'));
			$comment = trim($this->input->post('comment'));

			$passenger = isset($_POST['passenger']) && is_array($_POST['passenger']) ? $_POST['passenger'] : array();
			$room_passenger = isset($_POST['room_passenger']) && is_array($_POST['room_passenger']) ? $_POST['room_passenger'] : null;
			if (!isset($room_passenger)) {
				$this->form_validation->set_rules('room_passenger', 'Detalii pasageri', 'required', array(
					'required' => 'Completati informatiile pasagerilor'
				));
			}

			$this->form_validation->set_rules('comment', 'Comentariu', 'trim|max_length[1024]');

			$package_code = $this->input->post('package_code');
			if (!isset($package_code)) {
				$this->form_validation->set_rules('package_code', 'Cod pachet', 'required', array(
					'required' => 'Alegeti un hotel si selectati cate o optiune pentru fiecare camera'
				));
			} else {
				$this->form_validation->set_rules('hotel_id', 'ID hotel', 'required');
				$this->form_validation->set_rules('code', 'Cod cautare', 'required');
			}
			$hotel_id = (int)$this->input->post('hotel_id');
			$code = trim($this->input->post('code'));
			$package_code = trim($this->input->post('package_code'));

			$rooms = isset($_POST['rooms']) && is_array($_POST['rooms']) && isset($_POST['rooms'][$package_code]) && is_array($_POST['rooms'][$package_code]) ? $_POST['rooms'][$package_code] : null;
			if ($package_code && !isset($rooms)) {
				$this->form_validation->set_rules('rooms', 'Optiuni pachet', 'required', array(
					'required' => 'Nu ati ales optiuni camere'
				));
			}
			$flight_passenger = [];
			$hotel_passenger = [];
			$rooms_combinations = '';
			$room_index = 0;
			foreach ($rooms as $package_room_code => $room_code) {
				if ($rooms_combinations) {
					$rooms_combinations .= '-';
				}
				$rooms_combinations .= $package_room_code . ':' . $room_code;
				$assigned_room = $room_passenger[$room_index];
				$room_number = $room_index + 1;
				
				$occupant_numbers = array();
				foreach ($assigned_room as $occupant_index => $occupant) {
					foreach ($occupant as $occupant_type => $occupant_details) {
						if (!isset($occupant_numbers[$occupant_type])) {
							$occupant_numbers[$occupant_type] = 0;
						}
						$occupant_numbers[$occupant_type]++;
						$is_adult = in_array($occupant_type, array('SEN', 'ADT'));
						
						$fake_post_index = 'passenger_' . $room_index . '_' . $occupant_index;
						$_POST[$fake_post_index . '_' . 'email'] = isset($occupant_details['email']) ? $occupant_details['email'] : null;
						$_POST[$fake_post_index . '_' . 'phone'] = isset($occupant_details['phone']) ? $occupant_details['phone'] : null;
						
						$suffix = ' pentru camera # ' . $room_number . ' #' . ($occupant_index + 1) . ' (' . (isset($occupant_type_texts[$occupant_type]) ? $occupant_type_texts[$occupant_type] : '?') . ' #' . $occupant_numbers[$occupant_type] . ')';
						
						$this->form_validation->set_rules($fake_post_index . '_' . 'email', 'Email', 'trim' . ($is_adult ? '|required' : '') . '|max_length[255]|valid_email', array(
							'required' => 'Email-ul neintrodus ' . $suffix,
							'max_length' => 'Email-ul introdus depaseste limita admisa ' . $suffix,
							'valid_email' => 'Email-ul introdus depaseste limita admisa ' . $suffix,
						));
						$this->form_validation->set_rules($fake_post_index . '_' . 'phone', 'Telefon', 'trim' . ($is_adult ? '|required' : '') . '|max_length[100]', array(
							'required' => 'Telefonul neintrodus ' . $suffix,
							'max_length' => 'Telefonul introdus depaseste limita admisa ' . $suffix,
						));
						if (!isset($flight_passenger[$occupant_type])) {
							$flight_passenger[$occupant_type] = array();
						}
						$hotel_passenger['title'][] = $occupant_details['title'] ?? '';
						$hotel_passenger['firstname'][] = $occupant_details['firstName'] ?? '';
						$hotel_passenger['lastname'][] = $occupant_details['lastName'] ?? '';
						$hotel_passenger['birth_date'][] = $occupant_details['birthDate'] ?? '';
						$hotel_passenger['email'][] = $occupant_details['email'] ?? '';
						$hotel_passenger['phone'][] = $occupant_details['phone'] ?? '';
						$fk = count($flight_passenger[$occupant_type]);
						$flight_passenger[$occupant_type][] = $occupant_details;
						if(isset($passenger[$occupant_type], $passenger[$occupant_type][$fk])){
							$flight_passenger[$occupant_type][$fk] = array_merge($passenger[$occupant_type][$fk], $flight_passenger[$occupant_type][$fk]);
							$room_passenger[$room_index][$occupant_index] = array_merge($room_passenger[$room_index][$occupant_index], $passenger[$occupant_type][$fk]);
						}
					}
				}
				$room_index++;
			}

			if ($this->form_validation->run() == FALSE) {
				$this->data['errors'] = $this->form_validation->error_array();
				$this->outputError($this->form_validation->error_string());
			}
			
			$_POST['payment_gateway'] = 'manual';
			$_POST['payment_method'] = 'backend';
			$this->makeResponseGlobal();
			$_POST['passenger'] = [];
			foreach($flight_passenger as $ptc => $ptc_passengers){
				foreach($ptc_passengers as $ptc_passenger_index => $flight_passenger){
				  
				  $_POST['passenger']['title'][] = $flight_passenger['title'] ?? 'mr';
				  $_POST['passenger']['birth_date'][] = date("d.m.Y", strtotime($flight_passenger['birthDate'] ?? date('Y-m-d')));
				  $_POST['passenger']['firstname'][] = $flight_passenger['firstName'] ?? '';
				  $_POST['passenger']['lastname'][] = $flight_passenger['lastName'] ?? '';
				  $_POST['passenger']['country'][] = $flight_passenger['country'] ?? '';
				  $_POST['passenger']['email'][] = $flight_passenger['email'] ?? '';
				  $_POST['passenger']['phone'][] = $flight_passenger['phone'] ?? '';
				  
				  if(isset($flight_passenger['details']) && is_array($flight_passenger['details'])){
					  $_POST['preferredSeats'][$ptc][$ptc_passenger_index]['details'] = $flight_passenger['details'];
				  }
				}
			}
			
			$response = modules :: run('Trip/checkout/Checkout/service', 'flight', 'backend');
			if(!$response){
				$this->outputTripError(null);
			}
			$flight_service = $response['flight'];
			$_POST['passenger'] = $hotel_passenger;
			$_POST['rooms_combinations'] = $rooms_combinations;
			$response = modules :: run('Trip/checkout/Checkout/service', 'hotel', 'backend');
			if(!$response){
				$this->outputTripError(null);
			}
			$hotel_service = $response['hotel'];
			
			$service = array(
				'services' => array(
					'flight' => $flight_service,
					'hotel' => $hotel_service,
				)
			);
			
			// echo '<pre>';
			// print_r($service);
			// die;
			
			$this->data['service'] = $service;
			$add_trip_service_response = $this->TripOrder_model->addTripService($trip_order_id, $flight_service);
			if (!$add_trip_service_response) {
				$this->outputTripError('Trip Error: Nu s-a putut adauga serviciul zbor al comenzii citybreak');
			}
			$add_trip_service_response = $this->TripOrder_model->addTripService($trip_order_id, $hotel_service);
			if (!$add_trip_service_response) {
				$this->outputTripError('Trip Error: Nu s-a putut adauga serviciul hotel al comenzii citybreak');
			}

			$trip_services = $this->TripOrder_model->getTripServices($trip_order_id);
			if (!$trip_services) {
				$this->outputTripError('Trip Error: Nu s-au putut prelua serviciile comenzii');
			}

			$data = array();
			$data['id'] = $id;
			$data_type = 'citybreak';
			$types = array();
			$total_serv = 0;
			$data['services_citybreak'] = 1;
			$data['services_flight'] = null;
			$data['services_hotel'] = null;
			$data['services_package'] = null;
			foreach ($trip_services->_embedded->services as $serv) {
				$total_serv++;
				$serv_type = $serv->Type;
				if (!isset($types[$serv_type])) {
					$types[$serv_type] = 0;
					$data['services_' . $serv_type] = 0;
				}
				$types[$serv_type]++;
				$data['services_' . $serv_type]++;
			}
			if ($total_serv > 2) {
				$data['type'] = implode(',', array_keys($types));
			}
			$trip_order = $this->TripOrder_model->getTripOrder($trip_order_id);
			$data['amount'] = $trip_order->Amount * (1 - $order->coupon_percentage / 100);
			$data['total'] = $trip_order->Amount;
			$data['currency'] = $trip_order->Currency;

			$this->data['service'] = $service;
			$this->data['added_service'] = $add_trip_service_response;
			$this->data['services'] = $trip_services;
			$this->data['flight_code'] = $flight_code;
			$this->data['itinerary_code'] = $itinerary_code;
			$this->data['passenger'] = $room_passenger;
			$this->data['comment'] = $comment;
			$this->data['hotel_id'] = $hotel_id;
			$this->data['code'] = $code;
			$this->data['package_code'] = $package_code;
			$this->data['rooms'] = $rooms;

			$this->TripOrder_model->saveOrder($data);

			$this->addMessage('Serviciul a fost adaugat', 'success');
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
	public function addHotelService()
	{
		if ($this->input->is_ajax_request()) {
			if (!$this->user->can('backend-access')) {
				$this->outputError('Acces restrictionat');
			}
			if (!$this->user->canAny('backend-trip-orders-access', 'backend-trip-orders-own-access')) {
				$this->outputError('Acces restrictionat');
			}
			$this->load->library('form_validation');
			$this->form_validation->set_rules('order_id', 'ID comanda', 'required');
			$this->form_validation->set_rules('hotel_id', 'ID hotel', 'required');
			$this->form_validation->set_rules('code', 'Cod cautare', 'required');
			$this->form_validation->set_rules('package_code', 'Cod pachet', 'required', array(
				'required' => 'Alegeti un hotel si selectati cate o optiune pentru fiecare camera'
			));

			$id = (int)$this->input->post('order_id');

			$this->load->model('TripOrder_model');
			$order = $this->TripOrder_model->getOrderById($id);
			if (!$order) {
				$this->outputError('Comanda invalida.');
			}
			$trip_order_id = $order->trip_order_id;
			if (!$trip_order_id) {
				$this->outputError('Comanda nu are asociat ID in TRIP');
			}

			$hotel_id = (int)$this->input->post('hotel_id');
			$code = trim($this->input->post('code'));
			$package_code = trim($this->input->post('package_code'));
			$comment = trim($this->input->post('comment'));

			$rooms = isset($_POST['rooms']) && is_array($_POST['rooms']) && isset($_POST['rooms'][$package_code]) && is_array($_POST['rooms'][$package_code]) ? $_POST['rooms'][$package_code] : null;
			if ($package_code && !isset($rooms)) {
				$this->form_validation->set_rules('rooms', 'Optiuni pachet', 'required', array(
					'required' => 'Nu ati ales optiuni camere'
				));
			}
			$room = isset($_POST['room']) && is_array($_POST['room']) ? $_POST['room'] : null;
			if (!isset($room)) {
				$this->form_validation->set_rules('room', 'Detalii persoane', 'required', array(
					'required' => 'Completati informatiile persoanelor'
				));
			}

			$hotel_passenger = [];
			$rooms_combinations = '';
			if ($room && $rooms) {
				$room_index = 0;
				foreach ($rooms as $package_room_code => $room_code) {
					if ($rooms_combinations) {
						$rooms_combinations .= '-';
					}
					$rooms_combinations .= $package_room_code . ':' . $room_code;
					$assigned_room = $room[$room_index];
					$room_number = $room_index + 1;

					foreach ($assigned_room['adt'] as $adult_index => $adult_details) {
						$fake_post_index = 'adults_' . $room_index . '_' . $adult_index;
						$_POST[$fake_post_index . '_' . 'email'] = isset($adult_details['email']) ? $adult_details['email'] : null;
						$_POST[$fake_post_index . '_' . 'phone'] = isset($adult_details['phone']) ? $adult_details['phone'] : null;

						$this->form_validation->set_rules($fake_post_index . '_' . 'email', 'Email', 'trim|required|max_length[255]|valid_email', array(
							'required' => 'Email-ul neintrodus pentru adult #' . ($adult_index + 1) . ' camera #' . $room_number,
							'max_length' => 'Email-ul introdus depaseste limita admisa pentru adult #' . ($adult_index + 1) . ' camera #' . $room_number,
							'valid_email' => 'Email-ul introdus depaseste limita admisa pentru adult #' . ($adult_index + 1) . ' camera #' . $room_number,
						));
						$this->form_validation->set_rules($fake_post_index . '_' . 'phone', 'Telefon', 'trim|required|max_length[100]', array(
							'required' => 'Telefonul neintrodus pentru adult #' . ($adult_index + 1) . ' camera #' . $room_number,
							'max_length' => 'Telefonul introdus depaseste limita admisa pentru adult #' . ($adult_index + 1) . ' camera #' . $room_number,
						));
						
						$hotel_passenger['title'][] = $adult_details['title'] ?? '';
						$hotel_passenger['firstname'][] = $adult_details['firstname'] ?? '';
						$hotel_passenger['lastname'][] = $adult_details['lastname'] ?? '';
						$hotel_passenger['birth_date'][] = $adult_details['birth_date'] ?? '';
						$hotel_passenger['email'][] = $adult_details['email'] ?? '';
						$hotel_passenger['phone'][] = $adult_details['phone'] ?? '';
					}
					if (isset($assigned_room['chd'])) {
						if (!is_array($assigned_room['chd'])) {
							$this->outputError('Variabila de tip incorect');
						}
						foreach ($assigned_room['chd'] as $child_index => $child_details) {
							$hotel_passenger['title'][] = $adult_details['title'] ?? '';
							$hotel_passenger['firstname'][] = $adult_details['firstname'] ?? '';
							$hotel_passenger['lastname'][] = $adult_details['lastname'] ?? '';
							$hotel_passenger['birth_date'][] = $adult_details['birth_date'] ?? '';
							$hotel_passenger['email'][] = $adult_details['email'] ?? '';
							$hotel_passenger['phone'][] = $adult_details['phone'] ?? '';
						}
					}
					$room_index++;
				}
			}
			$this->form_validation->set_rules('comment', 'Comentariu', 'trim|max_length[1024]');

			if ($this->form_validation->run() == FALSE) {
				$this->data['errors'] = $this->form_validation->error_array();
				$this->outputError($this->form_validation->error_string());
			}
			
			$_POST['passenger'] = $hotel_passenger;
			$_POST['rooms_combinations'] = $rooms_combinations;
			$_POST['payment_gateway'] = 'manual';
			$_POST['payment_method'] = 'backend';
			
		// ini_set('display_errors', 1);
			$this->makeResponseGlobal();
			$response = modules :: run('Trip/checkout/Checkout/service', 'hotel', 'backend');
			if(!$response){
				$this->outputTripError(null);
			}
			$service = $response['hotel'];
			
			// echo '<pre>';
			// print_r($service);
			// die;

			$this->data['calls'] = &$this->Trip_model->api->calls;
			$add_trip_service_response = $this->TripOrder_model->addTripService($trip_order_id, $service);
			if (!$add_trip_service_response) {
				$this->outputTripError('Trip Error: Nu s-a putut adauga serviciul comenzii');
			}

			$trip_services = $this->TripOrder_model->getTripServices($trip_order_id);
			if (!$trip_services) {
				$this->outputTripError('Trip Error: Nu s-au putut prelua serviciile comenzii');
			}

			$data = array();
			$data['id'] = $id;
			$data['services_citybreak'] = null;
			$data['services_flight'] = null;
			$data['services_hotel'] = null;
			$data['services_package'] = null;
			$types = array();
			foreach ($trip_services->_embedded->services as $serv) {
				$serv_type = $serv->Type;
				if (!isset($types[$serv_type])) {
					$types[$serv_type] = 0;
					$data['services_' . $serv_type] = 0;
				}
				$types[$serv_type]++;
				$data['services_' . $serv_type]++;
			}
			$data['type'] = implode(',', array_keys($types));
			$trip_order = $this->TripOrder_model->getTripOrder($trip_order_id);
			$data['amount'] = $trip_order->Amount * (1 - $order->coupon_percentage / 100);
			$data['total'] = $trip_order->Amount;
			$data['currency'] = $trip_order->Currency;

			$this->data['service'] = $service;
			$this->data['added_service'] = $add_trip_service_response;
			$this->data['services'] = $trip_services;
			$this->data['hotel_id'] = $hotel_id;
			$this->data['code'] = $code;
			$this->data['package_code'] = $package_code;
			$this->data['room'] = $room;
			$this->data['rooms'] = $rooms;
			$this->data['comment'] = $comment;
			$this->TripOrder_model->saveOrder($data);

			$this->addMessage('Serviciul a fost adaugat', 'success');
			$this->output();
		}
		$this->redirect('backend', 'Acces invalid', 'error');
	}
}
