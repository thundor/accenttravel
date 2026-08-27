<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Ticketing extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAny('backend-ticketing-access','backend-accounts-own-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    // $this->getlist();
    $this->theme->view('backend/ticketing/tickets', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->canAny('backend-ticketing-access','backend-ticketing-own-access')){
      $this->outputError('Acces restrictionat');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $one = $this->input->post('one');
    
    $filter_id = $this->input->post('filter_id');
    if(isset($filter_id) && (!is_numeric($filter_id) || ((int)$filter_id <= 0))){
      $filter_id = null;
    }
    if($filter_id){
      $filters['id'] = (int)$filter_id;
    }
    $filter_trip_order_id = (int) $this->input->post('filter_trip_order_id');
    if(isset($filter_trip_order_id) && (!is_numeric($filter_trip_order_id) || ((int)$filter_trip_order_id <= 0))){
      $filter_trip_order_id = null;
    }
    if($filter_trip_order_id){
      $filters['order_id'] = (int)$filter_trip_order_id;
    }
    $filter_status = (array)$this->input->post('filter_status');
    if($filter_status){
      $filters['status'] = $filter_status;
    }
    $filter_time_created = $this->input->post('filter_time_created');
    if(isset($filter_time_created) && (!strlen(trim($filter_time_created)))){
      $filter_time_created = null;
    }
    if($filter_time_created){
      $filters['time_created'] = trim($filter_time_created);
    }
    $ids = $this->input->post('id');
    if($ids){
      $filters['id'] = $ids;
    }
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-ticketing-access');
    if(!$simple){
      $user_can['access_own'] = $user_can['access'] || $this->user->can('backend-ticketing-access');
      $user_can['view_own'] = $user_can['access_own'] && $this->user->can('backend-ticketing-own-view');
      $user_can['edit_own'] = $user_can['access_own'] && $this->user->can('backend-ticketing-own-edit');
      $user_can['delete_own'] = $user_can['access_own'] && $this->user->can('backend-ticketing-own-delete');
      $user_can['view'] = $user_can['access'] && $this->user->can('backend-ticketing-view');
      $user_can['edit'] = $user_can['access'] && $this->user->can('backend-ticketing-edit');
      $user_can['delete'] = $user_can['access'] && $this->user->can('backend-ticketing-delete');
    }
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    
    if(!$user_can['access']){
      $filters['user_id'] = $this->user->id;
    }
    
    $this->load->model('Ticket_model');
    $this->db->where('status>=0');
    $total_tickets = $this->Ticket_model->getTotalTickets($filters);
    if(!$one){
      $this->data['total_tickets'] = $total_tickets;
    }
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    if($one){
      $limit = 1;
    }
    if($simple){
      $select = $this->input->post('select');
      if($select){
        $filters['select'] = $select;
      }
      $filters['status'] = 1;
      if(!$limit || $limit > 200){
        $limit = 10;
      }
      if($one){
        $filters['return_row'] = true;
      } else {
        $filters['return_rows'] = true;
      }
    } elseif($one){
      $filters['return_result'] = true;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($total_tickets / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    if(!$one){
      $this->data['max_pages'] = $max_pages;
    }
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    if($total_tickets){
      $this->db->where('status>=0');
      $tickets = $this->Ticket_model->getTickets($filters);
    } elseif($one){
      $tickets = false;
    } else{
      $tickets = array();
    }
    if(!$simple){
      foreach($tickets as $k=>$ticket){
        $is_assignee = $ticket->user_id == $this->user->id;
        $is_own = $ticket->created_by == $this->user->id;
        $ticket->can_view = $ticket->trip_order_id && $is_assignee || ($user_can['access'] && $user_can['view']) || ($is_own && $user_can['view_own']);
        if($ticket->can_view){
          $ticket->view_link = base_url('backend/trip/orders/edit?id=' . $ticket->trip_order_id);
        }
        $ticket->can_edit = $is_assignee || ($user_can['access'] && ($user_can['edit'])) || ($is_own && $user_can['edit_own']);
        if($ticket->can_edit){
          $ticket->edit_link = base_url('backend/ticketing/edit?id=' . $ticket->id);
        }
        $ticket->can_delete = false;
        // $ticket->can_delete = ($user_can['access'] && ($user_can['delete'])) || ($is_own && $user_can['delete_own']);
        // if($ticket->can_delete){
          // $ticket->delete_link = base_url('backend/ticketing/delete?id=' . $ticket->id);
        // }
      }
    }
    $this->interpretTickets($tickets);
    
    if(!$one) {
      $this->data['limit'] = $limit;
      $this->data['tickets'] = $tickets;
      $this->data['page'] = $current_page;
    } else {
      $this->data['tickets'] = $tickets;
    }
    
    if(!$simple) {
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $session_data['filter_id'] = $filter_id;
      $session_data['filter_trip_order_id'] = $filter_trip_order_id;
      $session_data['filter_status'] = $filter_status;
      $session_data['filter_time_created'] = $filter_time_created;
      $this->session->set_userdata('backend/ticketing/tickets', $session_data);
    }
    $this->output();
  }
  protected function interpretTickets(&$tickets) {
    $this->load->model('Account_model');
    
    foreach($tickets as &$ticket) {
      $ticket->user_name = '-';
      if ($ticket->user_id) {
        $user = $this->Account_model->getAccountById($ticket->user_id);
        if($user){
          $ticket->user_name = $user->getFullname();
        }
      }
      $ticket->response_time = "-";
      if ($ticket->time_updated) {
        $date_updated = new DateTime($ticket->time_updated);
        $date_created = new DateTime($ticket->time_created);
        $diff = $date_updated->diff($date_created);
        
        if ($diff->format('%a') != 0) {
          $ticket->response_time = $diff->format('%a zile, %h ore, %i min');
        }
        else {
          $ticket->response_time = $diff->format('%h ore, %i min');
        }
      }
      
      if (!$ticket->time_modified) {
        $ticket->time_modified = "-";
      }
      
      if (!$ticket->time_updated) {
        $ticket->time_updated = "-";
      }
      
      if (!$ticket->trip_order_id) {
        $ticket->trip_order_id = "-";
      }
    }
  }
  public function getlisthistory() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->canAny('backend-ticketing-access','backend-ticketing-own-access')){
      $this->outputError('Acces restrictionat');
    }
    
    $id = $this->input->post('id');
    $this->load->model('Ticket_model');
    $tickets = $this->Ticket_model->getTicketHistoryByTicketID($id);
    $this->data['tickets'] = $tickets;
    $this->output();
  }
  
  public function add() {
    if (!$this->user->can('backend-access')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    if (!$this->user->canAny('backend-ticketing-access','backend-ticketing-own-access')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    if (!$this->user->can('backend-ticketing-add')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->library('ticket');
    $this->load->model('Ticket_model');
    
    $ticket = new Ticket;
    $this->data['ticket'] = &$ticket;
    $this->data['users'] = $this->Ticket_model->getAllowedUsers();
    
    $this->theme->view('backend/ticketing/ticket', $this->data);
  }
  public function delete() {
    if (!$this->user->can('backend-access')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    if (!$this->user->canAny('backend-ticketing-access','backend-ticketing-own-access')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    $id = (int)$this->input->get('id');
    $this->load->model('Ticket_model');
    $ticket = $this->Ticket_model->getTicketById($id);
    if (!$ticket) {
      $this->redirect('backend/ticketing','Tichet invalid','error');
    }
    
    $can_access = $this->user->can('backend-ticketing-access');
    $can_delete = $can_access && $this->user->can('backend-ticketing-delete');
    if (!$can_delete) {
      $can_access_own = $can_access || $this->user->can('backend-ticketing-own-access');
      $can_delete_own = $can_access_own && $this->user->can('backend-ticketing-own-delete');
      $can_delete = ($ticket->created_by == $this->user->id) && $can_delete_own;
    }
    if (!$can_edit) {
      $this->redirect('backend/ticketing','Acces restrictionat','error');
    }
    $this->Ticket_model->trashTicketById($id);
    $this->redirect('backend/ticketing','Tichetul a fost sters', 'success');
  }
  public function edit() {
    if (!$this->user->can('backend-access')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    if (!$this->user->canAny('backend-ticketing-access','backend-ticketing-own-access')) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    $id = (int)$this->input->get('id');
    $this->load->model('Ticket_model');
    $ticket = $this->Ticket_model->getTicketById($id);
    if (!$ticket) {
      $this->redirect('backend/ticketing','Tichet invalid','error');
    }
    
    $can_access = $this->user->can('backend-ticketing-access');
    $can_edit = $can_access && $this->user->can('backend-ticketing-edit');
    if (!$can_edit) {
      $can_access_own = $can_access || $this->user->can('backend-ticketing-own-access');
      $can_edit_own = $can_access_own && $this->user->can('backend-ticketing-own-edit');
      $can_edit = ($ticket->user_id == $this->user->id) || (($ticket->created_by == $this->user->id) && $can_edit_own);
    }
    if (!$can_edit) {
      $this->redirect('backend/ticketing','Acces restrictionat','error');
    }
    
    $this->data['ticket'] = $ticket;
    $this->data['users'] = $this->Ticket_model->getAllowedUsers();
    $this->data['history'] = $this->Ticket_model->getTicketHistoryByTicketID($ticket->id);
    
    $this->theme->view('backend/ticketing/ticket', $this->data);
  }
  private $saving_from_order = false;
  public function save_from_order() {
    $this->saving_from_order = true;
    $order_id = (int)$this->input->post('order_id');
    if(!$order_id){
      $this->outputError('Invalid access');
    }
    $this->load->model('Ticket_model');
    $ticket = $this->Ticket_model->getTicketByOrderId($order_id);
    if($ticket){
      $_POST['id'] = $ticket->id;
    }
    $this->save();
  }
  public function save() {
    if(!$this->user->can('backend-access') || !$this->user->canAny('backend-ticketing-access', 'backend-ticketing-own-access')) {
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/ticketing', 'Invalid access', 'error');
      }
    }
    $task = $this->input->post('task');
    $id = (int)$this->input->post('id');
    $ticket_id = $id;
    if($task == 'save_as_new'){
      $ticket_id = 0;
    }
    $is_new = !$id;
    $create_trip_order = $this->input->post('create_trip_order_id');
    
    $this->load->model('Ticket_model');
    $this->load->library('form_validation');
    $should_validate = false;
    if($create_trip_order){
      if(!$this->user->can('backend-trip-orders-add')){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid access');
        } else {
          $this->redirect('backend/ticketing', 'Invalid access', 'error');
        }
      }
    }
    $status = $this->input->post('status');
    if(isset($status)){
      $should_validate = true;
      $this->form_validation->set_rules('status', 'Status', 'in_list[1,2,3]',array(
        'in_list' => 'Status invalid',
      ));
    }
    if(!$is_new){
      if(!$this->user->canAny('backend-ticketing-edit', 'backend-ticketing-own-edit')) {
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid access');
        } else {
          $this->redirect('backend/ticketing', 'Invalid access', 'error');
        }
      }
      $ticket = $this->Ticket_model->getTicketById($ticket_id);
      if (!$ticket) {
        if ($this->input->is_ajax_request()) {
          $this->outputError('Tichet invalid');
        } else {
          $this->redirect('backend/ticketing', 'Invalid access', 'error');
        }
      }
      if($create_trip_order && $ticket->trip_order_id){
        $create_trip_order = false;
      }
      if(!$this->user->can('backend-ticketing-edit')) {
        if($ticket->user_id != $this->user->id){
          if(($ticket->created_by == $this->user->id) && !$this->user->can('backend-ticketing-own-edit')){
            if ($this->input->is_ajax_request()) {
              $this->outputError('Invalid access');
            } else {
              $this->redirect('backend/ticketing', 'Invalid access', 'error');
            }
          }
        }
      }
      
      
    } else {
      if (!$this->user->can('backend-ticketing-add')) {
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid access');
        } else {
          $this->redirect('backend/ticketing', 'Invalid access', 'error');
        }
      }
      $this->load->library('Ticket');
      $ticket = new Ticket;
      $ticket->id = null;
      $ticket->message = $this->input->post('message');
      $ticket->status = $status;
      $ticket->user_id = $this->input->post('user_id');
    }
    $should_validate = true;
    $this->form_validation->set_rules('message', 'Observatie', 'trim|required');
    
    $post_assignee_id = $this->input->post('user_id');
    $assignee_id = null;
    if(isset($post_assignee_id) && ((int)$post_assignee_id)){
      $_POST['assignee'] = null;
      $this->form_validation->set_rules('assignee', 'Consilier', 'required', array(
        'required' => 'Consilierul ales nu exista in baza de date'
      ));
      $this->load->model('Account_model');
      $assignee = $this->Account_model->getAccountById($post_assignee_id);
      if($assignee){
        $assignee_id = $assignee->id;
        $_POST['assignee'] = 1;
      }
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['ticket'] = &$ticket;
      $this->data['users'] = $this->Ticket_model->getAllowedUsers();
      return $this->theme->view('backend/ticketing/ticket', $this->data);
    }
    
    if (!$this->saving_from_order && $this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    $ticket_data = array();
    
    if ($create_trip_order) {
      /* Create the trip order and update the order_id. */
      // $order_id = 33; // remove when dev done
      $this->load->model('TripOrder_model');
      $trip_order = $this->TripOrder_model->createTripOrder();
      if(!$trip_order){
        if ($this->input->is_ajax_request()) {
          $this->outputError('TripError: Nu s-a putut crea comanda');
        } else {
          $this->redirect('backend/ticketing', 'TripError: Nu s-a putut crea comanda', 'error');
        }
      }
      $this->load->library('TripOrder');
      $this->load->model('TripOrder_model');
      $order_data = array();
      $order_data['trip_order_id'] = $trip_order->Id;
      $order_data['created_by'] = $this->user->id;
      $order_data['time_created'] = date('Y-m-d H:i:s');
      $order_id = $this->TripOrder_model->saveOrder($order_data);
      
      $ticket_data['trip_order_id'] = $order_id;
      $ticket->trip_order_id = $order_id;
    }
    
    $ticket_data['message'] = trim($this->input->post('message'));
    if($is_new){
      $ticket_data['created_by'] = $this->user->id;
      // La creare se stabileste tipul ticketului ca fiind adaugat manual
      $ticket_data['type'] = 1;
    } else {
      $ticket_data['id'] = $ticket->id; 
      $ticket_data['modified_by'] = $this->user->id;
    }
    if(isset($status)){
      $status = (int)$this->input->post('status');
      $ticket_data['status'] = $status;
    }
    
    if(isset($post_assignee_id)){
      $ticket_data['user_id'] = $assignee_id;
    }
    // echo '<pre>';
    // print_r($ticket_data);
    // die;
    $ticket_id = $this->Ticket_model->saveTicketWithHistory($ticket_data);
    
    $message = 'Tichetul a fost actualizat';
    if($is_new){
      $message = 'Tichetul a fost creat';
    }
    if ($this->saving_from_order && $this->input->is_ajax_request()) {
      $this->data['ticket_id'] = $ticket_id;
      $this->addMessage($message);
      $this->output();
    }
    switch($task){
      case 'save_and_back': $redirect_url = 'backend/ticketing'; break;
      case 'save_and_new': $redirect_url = 'backend/ticketing/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/ticketing/edit?id=' . $ticket_id; break;
      default:
      $redirect_url = 'backend/ticketing/edit?id=' . $ticket_id; 
      if($ticket->trip_order_id){
        $redirect_url = 'backend/trip/orders/edit?id=' . $ticket->trip_order_id; 
      }
      break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
  public function export() {
    if(!$this->user->can('backend-access') || !$this->user->canAny('backend-ticketing-access','backend-ticketing-own-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $session_data = $this->session->userdata('backend/ticketing/tickets');
    if(!$session_data){
      $session_data = array();
    }
    $page = isset($session_data['page']) && ((int)$session_data['page'] > 1) ? (int)$session_data['page'] : 1;
    $ordering = isset($session_data['ordering']) ? $session_data['ordering'] : null;
    $search = isset($session_data['search']) ? $session_data['search'] : null;
    $filter_id = isset($session_data['filter_id']) ? $session_data['filter_id'] : null;
    $filter_trip_order_id = isset($session_data['filter_trip_order_id']) ? $session_data['filter_trip_order_id'] : null;
    $filter_status = isset($session_data['filter_status']) ? $session_data['filter_status'] : null;
    $filter_time_created = isset($session_data['filter_time_created']) ? $session_data['filter_time_created'] : null;
    ob_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Export tichete.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'ID rezervare', 'Status', 'Data inregistrarii', 'Data preluare', 'Timp de raspuns', 'Ultima modificare', 'Consilier'));
    $this->load->model('Ticket_model');
    $filters = array();
    $filters['search'] = $search;
    $filters['ordering'] = $ordering;
    $filters['page'] = $page;
    $filters['id'] = $filter_id;
    $filters['order_id'] = $filter_trip_order_id;
    $filters['status'] = $filter_status;
    $filters['time_created'] = $filter_time_created;
    $this->db->where('status>=0');
    $tickets = $this->Ticket_model->getTickets($filters);
    $this->interpretTickets($tickets);
    foreach($tickets as $ticket){
      $status = '-';
      if($ticket->status == 1){
        $status = 'Nou';
      } else if($ticket->status == 2){
        $status = 'In lucru';
      } else if($ticket->status == 3){
        $status = 'Finalizata';
      }
      fputcsv($output, array(
        $ticket->id,
        $ticket->trip_order_id,
        $status,
        $ticket->time_created,
        $ticket->time_updated,
        $ticket->response_time,
        $ticket->time_modified,
        $ticket->user_name,
      ));
    }
    exit;
  }
}