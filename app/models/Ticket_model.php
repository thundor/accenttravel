<?php
class Ticket_model extends CI_Model {
  function getTicketById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getTicket($filters);
  }
  function getTicketByOrderId($id, $filters=array()) {
    $filters['order_id'] = $id;
    return $this->getTicket($filters);
  }
  
  function getTicketHistoryByTicketID($id) {
    $this->db->select('*');
    $this->db->where('ticket_id', $id);
    $this->db->order_by('time_modified', 'DESC');
    $query = $this->db->get('ac_ticket_history');
    $results = $query->result();
    $this->load->model('Account_model');
    foreach ($results as &$result) {
      $result->user_name = '-';
      if ($result->user_id) {
        $user = $this->Account_model->getAccountById($result->user_id);
        if($user){
          $result->user_name = $user->getFullName();
        }
      }
    }
    
    return $results;
  }
  
  function getTicket($filters=array()) {
    $tickets = $this->getTickets($filters);
    if ($tickets) {
      return $tickets[0];
    }
    return false;
  }
  
  function getAllowedUsers() {
    $this->load->model('Account_model');
    $filter = array(
      'role' => 'consilier',
      'status' => 1,
    );
    return $this->Account_model->getAccounts($filter);
  }
  
  function applyFilters($filters = array()) {
    if (isset($filters['type'])) {
      $this->db->where_in('type', (array)$filters['type']);
    }
    if (isset($filters['status'])) {
      $this->db->where_in('status', (array)$filters['status']);
    }
    
    if (isset($filters['search']) && $filters['search'] !== '') {
      $search = $filters['search'];
      $this->db->like('`message`',$search);
    }
    
    if (isset($filters['id'])) {
      $ids = (array)$filters['id'];
      if(!empty($ids)) {
        $this->db->where_in('id', $ids);
      }
    }
    if (isset($filters['order_id'])) {
      $order_ids = (array)$filters['order_id'];
      if(!empty($order_ids)) {
        $this->db->where_in('trip_order_id', $order_ids);
      }
    }
    if (isset($filters['time_created'])) {
      $time_created = trim($filters['time_created']);
      $time_created_arr = explode(' - ', $time_created);
      if(isset($time_created_arr[0])){
       $this->db->where('time_created>=', $time_created_arr[0]); 
      }
      if(isset($time_created_arr[1])){
       $this->db->where('time_created<=', $time_created_arr[1]); 
      }
    }
  }
  
  function getTickets($filters = array()) {
    if (isset($filters['select']) && $filters['select']) {
      $this->db->select($filters['select']);
    }
    else {
      $this->db->select('*');
      $this->db->select('IFNULL(time_modified,time_created) as last_change');
    }
    $this->applyFilters($filters);
  
    if (isset($filters['ordering']) && $filters['ordering']) {
      list($sort_by,$sort_order) = explode(' ',$filters['ordering']);
      $sort_order = strtolower($sort_order);
      $sort_by = strtolower($sort_by);
      
      if (!in_array($sort_order,array('asc', 'desc'))) {
        $sort_order = false;
      }
      
      if ($sort_order && $sort_by) {
        $this->db->order_by($sort_by, $sort_order);
      }
    }
  
    $page = isset($filters['page']) && (int)$filters['page'] > 1 ? (int)$filters['page']: 1;
    $limit = isset($filters['limit']) && (int)$filters['limit'] > 0 ? (int)$filters['limit']: null;
    $offset = 0;
    
    if ($limit > 0) {
      $offset = ($page - 1) * $limit;
    }
    
    
    // $this->db->order_by('IFNULL(time_modified, time_created)', 'DESC');
    $q = $this->db->get('ac_ticket', $limit, $offset);
    
    if (isset($filters['return_query']) && $filters['return_query']) {
      return $q;
    }
    
    if (isset($filters['return_row']) && $filters['return_row']) {
      return $q->row();
    } 
    elseif (isset($filters['return_rows']) && $filters['return_rows']) {
      return $q->result();
    } 
    else {
      $this->load->library('Ticket');
      if (isset($filters['return_result']) && $filters['return_result']) {
        return $q->row('Ticket');
      }
      return $q->result('Ticket');
    }
  }
  
  function getTotalTickets($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_ticket');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  
  function saveTicket($data) {
    if (isset($data['id']) && $data['id']) {
      $this->db->where('id', $data['id']);
      $this->db->update('ac_ticket', $data);
      return $data['id'];
    }
    else {
      $this->db->insert('ac_ticket', $data);
      return $this->db->insert_id();
    }
  }
  function saveTicketWithHistory($ticket_data, $old_ticket=null) {
    $is_new = !(isset($ticket_data['id']) && $ticket_data['id']);
    if($is_new){
      $ticket_data['time_created'] = date('Y-m-d H:i:s');
      $ticket_data['time_assigned'] = null;
      $ticket_data['updated_by'] = null;
      $ticket_data['time_updated'] = null;
      $ticket_data['modified_by'] = null;
      $ticket_data['time_modified'] = null;
      $ticket_data['last_history_id'] = null;
      $ticket_data['first_history_id'] = null;
    } else {
      $unchangeable_items = array(
        'created_by', // ramane
        'time_created', // ramane
        'first_history_id', // se determina automat
        'last_history_id', // se determina automat
        'time_modified', // se determina automat
        'time_assigned', // se determina automat
        'updated_by', // se determina automat in functie de modified_by
        'time_updated', // se determina automat
      );
      // daca tichetul are deja comanda asociata, nu putem schimba comanda (trebuie sa se deschida un tichet nou)
      if($old_ticket && $old_ticket->trip_order_id){
        $unchangeable_items[] = 'trip_order_id';
      }
      foreach($unchangeable_items as $do_not_change){
        if(array_key_exists($do_not_change,$ticket_data)){
          unset($ticket_data[$do_not_change]);
        }
      }
      $ticket_data['time_modified'] = date('Y-m-d H:i:s');
      
      // La prima schimbare de status, se determina timpul de raspuns
      // if($old_ticket && isset($ticket_data['status']) && isset($ticket_data['modified_by']) && !$old_ticket->time_updated && ($old_ticket->status != $ticket_data['status'])){
      if($old_ticket && isset($ticket_data['status']) && isset($ticket_data['modified_by']) && !$old_ticket->time_updated && ($old_ticket->status != $ticket_data['status'])){
        $ticket_data['time_updated'] = date('Y-m-d H:i:s');
        $ticket_data['updated_by'] = $ticket_data['modified_by'];
      }
    }
    if(isset($ticket_data['user_id']) && !$ticket_data['user_id']){
      $ticket_data['time_assigned'] = null;
      $ticket_data['user_id'] = null;
    } elseif(isset($ticket_data['user_id']) && $ticket_data['user_id'] && ($is_new || (!$is_new && $old_ticket && ($ticket_data['user_id'] != $old_ticket->user_id)))){
      $ticket_data['time_assigned'] = date('Y-m-d H:i:s');
      $ticket_data['user_id'] = date('Y-m-d H:i:s');
    }
    $ticket_id = $this->saveTicket($ticket_data);
    
    $new_ticket = $this->getTicketById($ticket_id);
    
    $ticket_history_data = array();
    $ticket_history_data['id'] = null;
    $ticket_history_data['ticket_id'] = $ticket_id;
    $ticket_history_data['user_id'] = $new_ticket->user_id;
    $ticket_history_data['status'] = $new_ticket->status;
    $ticket_history_data['type'] = $new_ticket->type;
    $ticket_history_data['message'] = $new_ticket->message;
    $ticket_history_data['history_id'] = $new_ticket->last_history_id;
    
    // din istoricul anterior (modified_id poate fi 0 pentru clienti fara cont)
    $ticket_history_data['created_by'] = $old_ticket && isset($old_ticket->modified_by) ? $old_ticket->modified_by : null;
    $ticket_history_data['time_created'] = $old_ticket && isset($old_ticket->modified_by) ? $old_ticket->time_modified : null;
    
    $ticket_history_data['modified_by'] = isset($new_ticket->modified_by) ? $new_ticket->modified_by : $new_ticket->created_by;
    $ticket_history_data['time_modified'] = isset($new_ticket->modified_by) ? $new_ticket->time_modified : $new_ticket->time_created;
    
    $ticket_history_id = $this->saveTicketHistory($ticket_history_data);
    
    $history_data = array(
      'id' => $ticket_id,
      'last_history_id' => $ticket_history_id,
    );
    if($is_new){
      $history_data['first_history_id'] = $ticket_history_id;
    }
    $this->saveTicket($history_data);
    
    $mailer_data = array();
    $mailer_data['ticket_id'] = $ticket_id;
    $mailer_data['ticket_url'] = base_url('backend/ticketing/edit?id=' . $ticket_id);
    $assignee_id = $ticket_data['user_id'];
    $mailer_data['assignee'] = null;
    $mailer_data['assigned_name'] = null;
    
    if($assignee_id){
      $this->load->model('Account_model');
      $assignee = $this->Account_model->getAccountById($assignee_id);
      if($assignee){
        $mailer_data['assignee'] = $assignee;
        $mailer_data['assigned_name'] = $assignee->getFullName();
        $mailer_data['to'] = $assignee->email;
      }
    }
    $mailer_data['updater'] = null;
    $mailer_data['updater_name'] = null;
    $updater_id = null;
    if($is_new){
      $updater_id = $ticket_data['created_by'];
    } else {
      $updater_id = $ticket_data['modified_by'];
    }
    if($updater_id){
      $this->load->model('Account_model');
      $updater = $this->Account_model->getAccountById($updater_id);
      if($updater){
        $mailer_data['updater'] = $updater;
        $mailer_data['updater_name'] = $updater->getFullName();
      }
    }
    $mailer_data['reservation_id'] = null;
    $order_id = $ticket_data['trip_order_id'];
    if($order_id){
      $mailer_data['reservation_id'] = $order_id;
    }
    if($is_new){
      $mailer_data['subject'] = 'Tichet nou';
    } else {
      $mailer_data['subject'] = 'Tichet actualizat';
    }
    $mailer_data['message'] = $ticket_data['message'];
    $mailer_data['status'] = $ticket_data['status'];
    
    if($is_new){
      Modules :: run ('Mailer/ticketing_add', $mailer_data);
    } else {
      Modules :: run ('Mailer/ticketing_edit', $mailer_data);
    }
    
    return $ticket_id;
  }
  
  function saveTicketHistory($data) {
    $this->db->insert('ac_ticket_history', $data);
    return $this->db->insert_id();
  }
  
  function trashTicketById($id, $filters = array()) {
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('ac_ticket');
  }
  function deleteTicketById($id, $filters = array()) {
    $filters['id'] = $id;
    $this->deleteTicket($filters);
  }
  
  function deleteTicket($filters = array()) {
    $this->applyFilters($filters);
    $this->db->delete('ac_ticket');
  }
}