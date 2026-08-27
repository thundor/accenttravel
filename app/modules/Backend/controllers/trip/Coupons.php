<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Coupons extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/trip/coupons', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $type = $this->input->post('type');
    $filters['status'] = array(0,1,-1);
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-save');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-config-save');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    $join_child = $this->input->post('join_child');
    $filters['join_child'] = $join_child;
    if($simple){
      $filters['return_rows'] = true;
    }
	$parent_id = $this->input->post('parent_id');
	if(isset($parent_id)){
		$filters['parent_id'] = (int)$parent_id;
		$filters['type'] = 'child';
	} else {
		if($simple){
			$filters['type'] = array('singular', 'child');
		} else {
			$filters['type'] = array('singular', 'group');
		}
	}
    $this->load->model('TripCoupon_model');
    $this->load->model('TripOrder_model');
    $this->load->model('TripOrderCoupon_model');
    $this->data['total_items'] = $this->TripCoupon_model->getTotalCoupons($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_items'] / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    $this->data['max_pages'] = $max_pages;
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    $coupons = $this->TripCoupon_model->getCoupons($filters);
	// echo '<pre>';
	// print_r($coupons);
	// die;
    if(!$simple){
	foreach($coupons as $k=>$coupon){
		$coupon->orders = $this->TripOrderCoupon_model->getOrderCouponsByCouponId($coupon->id, array('join_order' => 1));
        $coupon->can_view = ($coupon->status>=-1) && $user_can['access'] && $user_can['view'];
        if($coupon->can_view){
          $coupon->view_link = base_url('backend/trip/coupons/view?id=' . $coupon->id);
        }
        $coupon->can_change_status = ($coupon->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($coupon->can_change_status){
          $coupon->publish_link = base_url('backend/trip/coupons/publish?id=' . $coupon->id);
          $coupon->unpublish_link = base_url('backend/trip/coupons/unpublish?id=' . $coupon->id);
          $coupon->archive_link = base_url('backend/trip/coupons/archive?id=' . $coupon->id);
        }
        $coupon->can_edit = ($coupon->status>=0 && !$coupon->nr_uses) && ($user_can['access'] && $user_can['edit']);
        if($coupon->can_edit){
          $coupon->edit_link = base_url('backend/trip/coupons/edit?id=' . $coupon->id);
        }
        $coupon->can_delete = ($coupon->status>=0 && !$coupon->nr_uses) && ($user_can['access'] && $user_can['delete']);
        if($coupon->can_delete){
          $coupon->delete_link = base_url('backend/trip/coupons/delete?id=' . $coupon->id);
        }
	}
	} else {
		ini_set('display_errors', 1);
		foreach($coupons as $k=>$coupon){
			if($coupon->type == 'child'){
				$coupon->orders = $this->TripOrderCoupon_model->getOrderCouponsByCouponId($coupon->parent_id, array('join_order' => 1, 'code' => $coupon->code));
			} else {
				$coupon->orders = $this->TripOrderCoupon_model->getOrderCouponsByCouponId($coupon->id, array('join_order' => 1));
			}
		}
	}
	// echo '<pre>';
	// print_r($coupons);
	// die;
    $this->data['coupons'] = $coupons;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/trip/coupons', $session_data);
    }
	
	if($simple && $this->input->post('xls')){
		require_once(APPPATH . 'third_party/psr_autoloader.php');
		require_once(APPPATH . 'third_party/php_spreadsheet_autoloader.php');
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		ini_set('display_errors', 1);
		
		$pc = $this->TripCoupon_model->getCouponById($parent_id);
		
		// $spreadsheet->getDefaultStyle()
			// ->getNumberFormat()
			// ->setDataType(
				// \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
			// );
			
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Detalii cupon');
		
		$data = array();
		$data[] = array('Nume',$pc->name);
		$data[] = array('Status',$pc->status == 1 ? 'Activ' : ($pc->status == -1 ? 'Anulat' : 'Inactiv'));
		$data[] = array('Cod','="' . $pc->code . '"');
		$data[] = array('EAN','="' . $pc->ean . '"');
		$data[] = array('Numar utilizari curente',$pc->nr_uses);
		$data[] = array('Numar maxim utilizari',$pc->max_uses);
		$data[] = array('Tip discount',$pc->discount_type == 'P' ? 'Procentaj' : 'Suma fixa');
		if($pc->discount_type == 'P'){
			$data[] = array('Discount',$pc->percentage . '%');
		} else {
			$data[] = array('Suma RON',$pc->fixed_ron);
			$data[] = array('Suma EUR',$pc->fixed_eur);
		}
		$data[] = array('Data Start',$pc->date_start);
		$data[] = array('Data Expirare',$pc->date_expire);
		$data[] = array('Disponibil pentru hoteluri',$pc->hotel);
		$data[] = array('Disponibil pentru pachete Romania',$pc->package);
		$data[] = array('Disponibil pentru zboruri',$pc->flight);
		$data[] = array('Disponibil pentru City Break',$pc->citybreak);
		$data[] = array('Disponibil pentru Paralela45 strainatate',$pc->paralela45_strainatate);
		$data[] = array('Disponibil pentru Paralela45 circuit',$pc->paralela45_circuit);
		$data[] = array('Disponibil pentru Travelfuse charter',$pc->travelfuse_charter);
		$data[] = array('Disponibil pentru Travelfuse circuit',$pc->travelfuse_circuit);
		$data[] = array('Disponibil pentru EPAY',$pc->epay);
		$data[] = array('Verificare FSLI',$pc->fsli);
		$data[] = array('Observatii',$pc->observation);
		
		$sheet->fromArray($data);
		
		
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Coduri');
		$data = array(array(
			'Status',
			'Cod',
			'Serial Number',
			'Nr. Utilizari',
			'Comenzi',
			'Servicii',
		));
		
		foreach($this->data['coupons'] as $c){
			$order_ids = array();
			$order_types = array();
			if($c->orders){
				foreach($c->orders as $c_order){
					$order_ids[] = $c_order->order_id;
					if(!in_array($c_order->type, $order_types)){
						$order_types[] = $c_order->type;
					}
				}
			}
			$data[] = array(
				$c->status == 1 ? 'Activ' : ($c->status == -1 ? 'Anulat' : 'Inactiv'),
				'="' . $c->code . '"',
				'="' . $c->pan . '"',
				$c->nr_uses,
				implode(', ', $order_ids),
				implode(', ', $order_types),
			);
		}
		$sheet->fromArray($data);
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=Cupon-" . htmlspecialchars($pc->name . '-' . $pc->ean) . ".xls");  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		$writer->save("php://output");
		exit;
	}
	
    $this->output();
  }
  public function test() {
	  ini_set('display_errors', 1);
	$this->load->model('TripCoupon_model');
	
	$code = 'TESTING';
	$code = 'TESTINGC658TANS9';
	// $code = '8BENEFIT';
	$valid_coupon = $this->TripCoupon_model->getValidCoupon($code);
	
	echo '<pre>';
	print_r($valid_coupon);
	die;
  }
  public function generate() {
	if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
		$this->outputError('Invalid access');
    }
	$parent_id = (int)$this->input->post('parent_id');
	$number = (int)$this->input->post('number');
	if(!$parent_id){
		$this->outputError('Invalid coupon ID');
	}
	if($number < 1){
		$this->outputError('Invalid coupon count');
	}
	$this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($parent_id);
	if(!$coupon){
		$this->outputError('Invalid coupon');
	}
	for($i = 1; $i <= $number; $i++){
		$existing_coupon = true;
		$max_iterations = 10;
		while($existing_coupon && ($max_iterations--)){
			$license = $this->TripCoupon_model->generateLicense();
			$coupon_code = substr($coupon->code . $license,0,16);
			$existing_coupon = $this->TripCoupon_model->getCouponByCode($coupon_code);
		}
		if($coupon->epay){
			$data['status'] = 0;
		} else {
			$data['status'] = 1;
		}
		$data['code'] = $coupon_code;
		$data['parent_id'] = $parent_id;
		$data['type'] = 'child';
		$id = $this->TripCoupon_model->saveCoupon($data);
	}
	
	$this->addMessage('Successfully created coupons');
	$this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->data['coupon'] = (object)array(
      'id'=>null,
      'code'=>'',
      'name'=>'',
      'percentage'=>null,
      'type'=>'singular',
      'discount_type'=>'P',
      'fixed_ron'=>null,
      'fixed_eur'=>null,
      'max_uses'=>null,
      'nr_uses'=>null,
      'status'=>1,
      'date_start'=>null,
      'date_expire'=>null,
      'created_by'=>$this->user->id,
      'time_created'=>date('Y-m-d H:i:s'),
      'modified_by'=>null,
      'time_modified'=>null,
      'observation'=>null,
      'hotel'=>1,
      'package'=>1,
      'flight'=>0,
      'citybreak'=>0,
      'paralela45_strainatate'=>0,
      'paralela45_circuit'=>0,
      'travelfuse_charter'=>0,
      'travelfuse_circuit'=>0,
      'epay'=>0,
      'fsli'=>0,
    );
    $this->theme->view('backend/trip/coupon', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon){
      $this->redirect('backend/trip/coupons','Acces invalid', 'error');
    }
    $this->data['coupon'] = $coupon;
    $this->theme->view('backend/trip/coupon', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon || ($coupon->status<-1)){
      $this->redirect('backend/trip/coupons','Acces invalid', 'error');
    }
    $this->data['coupon'] = $coupon;
    $this->theme->view('backend/trip/coupon', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon || ($coupon->status<0) || $coupon->nr_uses){
      $this->redirect('backend/trip/coupons','Acces invalid', 'error');
    }
    $this->TripCoupon_model->deleteCouponById($id);
    $this->redirect('backend/trip/coupons','Cuponul a fost sters', 'success');
  }
  public function change_status() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->outputError('Invalid access');
    }
    $id = (int)$this->input->post('id');
    $status = (int)$this->input->post('status');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon){
		$this->outputError('Coupon not found');
    }
	if($coupon->nr_uses){
		$this->outputError('Cuponul a fost folosit, nu se pot efectua alterari');
	}
	if($status == 0){
		if($coupon->status < -1){
			$this->outputError('Cuponul nu poate fi dezactivat');
		}
		$this->TripCoupon_model->unpublishCouponById($id);
		$this->addMessage('Cuponul a fost dezactivat');
	} elseif($status == 1){
		if($coupon->status < -1){
			$this->outputError('Cuponul nu poate fi publicat');
		}
		$this->TripCoupon_model->publishCouponById($id);
		$this->addMessage('Cuponul a fost activat');
	} elseif($status == -2){
		if($coupon->status < -1){
			$this->outputError('Cuponul nu poate fi sters');
		}
		$this->TripCoupon_model->deleteCouponById($id);
		$this->addMessage('Cuponul a fost sters');
	} elseif($status == -1){
		if($coupon->status < -1){
			$this->outputError('Cuponul nu poate fi anulat');
		}
		$this->TripCoupon_model->trashCouponById($id);
		$this->addMessage('Cuponul a fost anulat');
	}
	$this->output();
  }
  public function archive() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon || ($coupon->status<0)){
      $this->redirect('backend/trip/coupons','Acces invalid', 'error');
    }
    $this->TripCoupon_model->trashCouponById($id);
    $this->redirect('backend/trip/coupons','Cuponul a fost dezactivat', 'success');
  }
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon || ($coupon->status<-1)){
      $this->redirect('backend/trip/coupons','Acces invalid', 'error');
    }
    $this->TripCoupon_model->unpublishCouponById($id);
    $this->redirect('backend/trip/coupons','Cuponul a fost dezactivat', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripCoupon_model');
    $coupon = $this->TripCoupon_model->getCouponById($id);
    if(!$coupon || ($coupon->status<-1)){
      $this->redirect('backend/trip/coupons','Acces invalid', 'error');
    }
    $this->TripCoupon_model->publishCouponById($id);
    $this->redirect('backend/trip/coupons','Cuponul a fost activat', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/trip/coupons', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $coupon_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $coupon_id = 0;
    }
    $data = array();
    $this->load->model('TripCoupon_model');
    $code = trim($this->input->post('code'));
    if($coupon_id){
      $coupon = $this->TripCoupon_model->getCouponById($coupon_id);
      if(!$coupon || $coupon->type == 'child' || ($coupon->status<-1) || ($coupon->nr_uses && $coupon->type == 'singular')){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid coupon');
        } else {
          $this->redirect('backend/trip/coupons', 'Invalid coupon', 'error');
        }
      }
      $check_unique_code = $coupon->code !== $code;
      $data['modified_by'] = $this->user->id;
      $data['time_modified'] = date('Y-m-d H:i:s');
    } else {
      $check_unique_code = true;
      $coupon = (object)array(
        'id'=>null,
        'code'=>'',
        'name'=>'',
		'type'=>'singular',
		'discount_type'=>'P',
		'fixed_ron'=>null,
		'fixed_eur'=>null,
        'percentage'=>null,
        'max_uses'=>null,
        'nr_uses'=>null,
        'status'=>1,
        'date_start'=>null,
        'date_expire'=>null,
        'created_by'=>$this->user->id,
        'time_created'=>date('Y-m-d H:i:s'),
        'modified_by'=>null,
        'time_modified'=>null,
      );
      $data['created_by'] = $this->user->id;
      $data['time_created'] = date('Y-m-d H:i:s');
    }
	$coupon->type = $data['type'] = $this->input->post('type');
	$coupon->discount_type = $data['discount_type'] = $this->input->post('discount_type');
	
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('code', 'Cod', 'required' . ($check_unique_code ? '|is_unique[trip_coupon.code]' : ''));
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
	if($coupon->discount_type == 'P'){
		$this->form_validation->set_rules('percentage', 'Discount', 'required|is_numeric|is_greater_than[0]|is_less_than_or_equal_to[100]',array(
		  'is_numeric' => 'Discount invalid',
		  'is_greater_than' => 'Discountul trebuie sa fie strict pozitiv',
		  'is_less_than_or_equal_to' => 'Discountul trebuie sa fie mai mic sau egal cu 100',
		));
	}
	if($coupon->discount_type == 'F'){
		$this->form_validation->set_rules('fixed_ron', 'Suma RON', 'required|is_numeric|is_greater_than[0]',array(
		  'is_numeric' => 'Suma RON invalida',
		  'is_greater_than' => 'Suma RON trebuie sa fie strict pozitiva',
		));
		$this->form_validation->set_rules('fixed_eur', 'Suma EUR', 'required|is_numeric|is_greater_than[0]',array(
		  'is_numeric' => 'Suma EUR invalida',
		  'is_greater_than' => 'Suma EUR trebuie sa fie strict pozitiva',
		));
	}
	if($coupon->type == 'singular'){
		$this->form_validation->set_rules('max_uses', 'Numar maxim utilizari', 'validate_positive_int|is_greater_than_or_equal_to[0]',array(
		  'validate_positive_int' => 'Numarul maxim de utilizari trebuie sa fie un intreg',
		  'is_greater_than_or_equal_to' => 'Numar maxim utilizari trebuie sa fie pozitiv',
		));
	}
    $this->form_validation->set_rules('date_start', 'Data start disponibilitate', 'valid_date[Y-m-d]',array(
      'valid_date' => 'Data de start disponibilitate trebuie sa fie o data valida',
    ));
    $minimum_date_start = isset($_POST['date_start']) && strlen(trim($_POST['date_start'])) ? trim($_POST['date_start']) : date('Y-m-d');
    $this->form_validation->set_rules('date_expire', 'Data expirare', 'valid_date[Y-m-d]|is_greater_than_or_equal_to[' . $minimum_date_start . ']',array(
      'valid_date' => 'Data de expirare trebuie sa fie o data valida',
      'is_greater_than_or_equal_to' => 'Data de expirare este in trecut fata de data de start',
    ));
    $coupon->code = $data['code'] = $code;
    $coupon->name = $data['name'] = $this->input->post('name');
    $coupon->status = $data['status'] = $this->input->post('status');
    $coupon->observation = $data['observation'] = $this->input->post('observation');
    $percentage = floatval($this->input->post('percentage'));
    $percentage = abs($percentage);
    $fixed_eur = floatval($this->input->post('fixed_eur'));
    $fixed_eur = abs($fixed_eur);
    $fixed_ron = floatval($this->input->post('fixed_ron'));

	if($coupon->discount_type == 'F'){
		$percentage = null;
	} else {
		$fixed_eur = null;
		$fixed_ron = null;
	}
    $coupon->percentage = $data['percentage'] = $percentage;
    $coupon->fixed_eur = $data['fixed_eur'] = $fixed_eur;
    $coupon->fixed_ron = $data['fixed_ron'] = $fixed_ron;
	$max_uses = (int)$this->input->post('max_uses');
	if($coupon->type == 'group'){
		$max_uses = 1;
	}
    $coupon->max_uses = $data['max_uses'] = $max_uses > 0 ? $max_uses : null;
	foreach(array('hotel','package','flight','citybreak','paralela45_strainatate','paralela45_circuit','travelfuse_circuit','travelfuse_charter','epay','fsli') as $k){
		$v = (int)$this->input->post($k);
		$coupon->$k = $data[$k] = $v;
	}
    $date_start = isset($_POST['date_start']) && strlen(trim($_POST['date_start'])) ? trim($_POST['date_start']) : null; 
    $coupon->date_start = $data['date_start'] = $date_start;
    $date_expire = isset($_POST['date_expire']) && strlen(trim($_POST['date_expire'])) ? trim($_POST['date_expire']) : null; 
    $coupon->date_expire = $data['date_expire'] = $date_expire;
    if($coupon_id){
      $data['id'] = $coupon_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['coupon'] = $coupon;
      return $this->theme->view('backend/trip/coupon', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$coupon_id;

    $id = $this->TripCoupon_model->saveCoupon($data);
    $message = 'Cuponul a fost actualizat';
    if($is_new){
      $message = 'Cuponul a fost creat';
    }
    $redirect_url = 'backend/trip/coupons';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/trip/coupons/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/trip/coupons/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}