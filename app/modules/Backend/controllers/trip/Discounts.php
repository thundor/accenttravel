<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Discounts extends MX_Controller {
  private $default_type = 'package';
  private $allowed_types = array(
    'package'
  );
  function __construct() {
    parent :: __construct();
  }
  public function general() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('trip_discounts',null,array(
      'trip_discount_package'=>null,
    ));
    if(!$settings){
      $settings = array();
    }
    $this->data = $settings;
    $this->theme->view('backend/trip/discounts_general', $this->data);
  }
  public function general_save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    
    $this->load->library('form_validation');
    $discount_prefix = 'trip_discount_';
    $this->form_validation->set_rules($discount_prefix . 'package', 'Discount vacante', 'trim|is_numeric|greater_than_equal_to[0]|less_than_equal_to[100]',array(
      'is_numeric' => 'Ati introdus caractere nepermise',
    ));
    
    $this->load->model('Options_model');
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $data = array();
    $data['trip_discount_package'] = trim($this->input->post('trip_discount_package'));
    if(!strlen($data['trip_discount_package'])){
      $data['trip_discount_package'] = null;
    }
    
    $this->Options_model->set('trip_discounts',$data);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
  public function index($type = null) {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $type = $type && in_array($type, $this->allowed_types) ? $type : null;
    $this->data['type'] = $type;
    $this->theme->view('backend/trip/discounts', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $type = $this->input->post('type');
    $filters['status'] = array(0,1);
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-save');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-config-save');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    if($simple){
      $filters['return_rows'] = true;
    }
    $this->load->model('TripDiscount_model');
    $this->data['total_items'] = $this->TripDiscount_model->getTotalDiscounts($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['type'] = $type;
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
    $discounts = $this->TripDiscount_model->getDiscounts($filters);
    if(!$simple){
      foreach($discounts as $k=>$discount){
        $discount->can_view = ($discount->status>=-1) && $user_can['access'] && $user_can['view'];
        if($discount->can_view){
          $discount->view_link = base_url('backend/trip/discounts/view?id=' . $discount->id);
        }
        $discount->can_change_status = ($discount->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($discount->can_change_status){
          $discount->publish_link = base_url('backend/trip/discounts/publish?id=' . $discount->id);
          $discount->unpublish_link = base_url('backend/trip/discounts/unpublish?id=' . $discount->id);
        }
        $discount->can_edit = ($discount->status>=0) && ($user_can['access'] && $user_can['edit']);
        if($discount->can_edit){
          $discount->edit_link = base_url('backend/trip/discounts/edit?id=' . $discount->id);
        }
        $discount->can_delete = ($discount->status>=0) && ($user_can['access'] && $user_can['delete']);
        if($discount->can_delete){
          $discount->delete_link = base_url('backend/trip/discounts/delete?id=' . $discount->id);
        }
      }
    }
    $this->data['discounts'] = $discounts;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/trip/discounts', $session_data);
    }
    $this->output();
  }
  public function add($type = null) {
    $type = $type && in_array($type, $this->allowed_types) ? $type : null;
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->data['discount'] = (object)array(
      'id'=>null,
      'type'=>$type,
      'type_id'=>0,
      'name'=>'',
      'percentage'=>null,
      'status'=>1,
      'date_start'=>null,
      'date_expire'=>null,
      'created_by'=>$this->user->id,
      'time_created'=>date('Y-m-d H:i:s'),
      'modified_by'=>null,
      'time_modified'=>null,
    );
    $this->theme->view('backend/trip/discount', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripDiscount_model');
    $discount = $this->TripDiscount_model->getDiscountById($id);
    if(!$discount){
      $this->redirect('backend/trip/discounts','Acces invalid', 'error');
    }
    $this->data['discount'] = $discount;
    $this->theme->view('backend/trip/discount', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripDiscount_model');
    $discount = $this->TripDiscount_model->getDiscountById($id);
    if(!$discount || ($discount->status<-1)){
      $this->redirect('backend/trip/discounts','Acces invalid', 'error');
    }
    $this->data['discount'] = $discount;
    $this->theme->view('backend/trip/discount', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripDiscount_model');
    $discount = $this->TripDiscount_model->getDiscountById($id);
    if(!$discount || ($discount->status<0)){
      $this->redirect('backend/trip/discounts','Acces invalid', 'error');
    }
    $this->TripDiscount_model->deleteDiscountById($id);
    $this->redirect('backend/trip/discounts','Discountul a fost sters', 'success');
  }
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripDiscount_model');
    $discount = $this->TripDiscount_model->getDiscountById($id);
    if(!$discount || ($discount->status<0)){
      $this->redirect('backend/trip/discounts','Acces invalid', 'error');
    }
    $this->TripDiscount_model->unpublishDiscountById($id);
    $this->redirect('backend/trip/discounts','Discountul a fost dezactivat', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripDiscount_model');
    $discount = $this->TripDiscount_model->getDiscountById($id);
    if(!$discount || ($discount->status<0)){
      $this->redirect('backend/trip/discounts','Acces invalid', 'error');
    }
    $this->TripDiscount_model->publishDiscountById($id);
    $this->redirect('backend/trip/discounts','Discountul a fost activat', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/trip/discounts', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $discount_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $discount_id = 0;
    }
    $data = array();
    $this->load->model('TripDiscount_model');
    if($discount_id){
      $discount = $this->TripDiscount_model->getDiscountById($discount_id);
      if(!$discount || ($discount->status<-1)){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid discount');
        } else {
          $this->redirect('backend/trip/discounts', 'Invalid discount', 'error');
        }
      }
      $data['modified_by'] = $this->user->id;
      $data['time_modified'] = date('Y-m-d H:i:s');
    } else {
      $discount = (object)array(
        'id'=>null,
        'type'=>'',
        'type_id'=>0,
        'name'=>'',
        'percentage'=>null,
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
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('type', 'Tip', 'required|in_list[' . implode(',', $this->allowed_types) . ']');
    $this->form_validation->set_rules('type_id', 'ID Tip', 'required');
    $this->form_validation->set_rules('name', 'Nume', 'trim|required');
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $this->form_validation->set_rules('percentage', 'Discount', 'required|is_numeric|is_greater_than_or_equal_to[0]|is_less_than_or_equal_to[100]',array(
      'is_numeric' => 'Discount invalid',
      'is_greater_than' => 'Discountul trebuie sa fie strict pozitiv',
      'is_less_than_or_equal_to' => 'Discountul trebuie sa fie mai mic sau egal cu 100',
    ));
    $this->form_validation->set_rules('date_start', 'Data start disponibilitate', 'valid_date[Y-m-d]',array(
      'valid_date' => 'Data de start disponibilitate trebuie sa fie o data valida',
    ));
    $minimum_date_start = isset($_POST['date_start']) && strlen(trim($_POST['date_start'])) ? trim($_POST['date_start']) : date('Y-m-d');
    $this->form_validation->set_rules('date_expire', 'Data expirare', 'valid_date[Y-m-d]|is_greater_than_or_equal_to[' . $minimum_date_start . ']',array(
      'valid_date' => 'Data de expirare trebuie sa fie o data valida',
      'is_greater_than_or_equal_to' => 'Data de expirare este in trecut fata de data de start',
    ));
    $discount->type = $data['type'] = $this->input->post('type');
    $discount->type_id = $data['type_id'] = $this->input->post('type_id');
    $discount->status = $data['status'] = $this->input->post('status');
    $discount->name = $data['name'] = trim($this->input->post('name'));
    $percentage = floatval($this->input->post('percentage'));
    $discount->percentage = $data['percentage'] = abs($percentage);
    $date_start = isset($_POST['date_start']) && strlen(trim($_POST['date_start'])) ? trim($_POST['date_start']) : null; 
    $discount->date_start = $data['date_start'] = $date_start;
    $date_expire = isset($_POST['date_expire']) && strlen(trim($_POST['date_expire'])) ? trim($_POST['date_expire']) : null; 
    $discount->date_expire = $data['date_expire'] = $date_expire;
    if($discount_id){
      $data['id'] = $discount_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['discount'] = $discount;
      return $this->theme->view('backend/trip/discount', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$discount_id;
    $id = $this->TripDiscount_model->saveDiscount($data);
    $message = 'Discountul a fost actualizat';
    if($is_new){
      $message = 'Discountul a fost creat';
    }
    $redirect_url = 'backend/trip/discounts/' . $discount->type;
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/trip/discounts/add/' . $discount->type; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/trip/discounts/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}