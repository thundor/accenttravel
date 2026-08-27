<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Travelfuse_Order extends MX_Controller {
  function __construct() {
    $this->load->model('TravelFuse_model');
    parent::__construct();
  }
  public function loadServices() {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $id = (int)$this->input->post('order_id');
      if(!$id){
        $this->outputError('Comanda noua. Va rugam adaugati un serviciu.');
      }
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Comanda invalida.');
      }
      $trip_services = array();
      if($order->provider !== 'travelfuse'){
        $this->outputError('Invalid order provider');
      }
      if(isset($order->services)){
        $trip_services = unserialize($order->services);
      }
      if(empty($trip_services)){
        $this->outputError('Niciun serviciu adaugat in comanda.');
      }
      $this->data['services'] = $trip_services;
      $this->data['currency_code'] = $order->currency;
      $this->data['trip_order_id'] = $order->trip_order_id;
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
  public function removeServices() {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $id = (int)$this->input->post('order_id');
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Comanda invalida.');
      }
      if($order->provider !== 'travelfuse'){
        $this->outputError('Invalid order provider');
      }
      if($order->trip_order_id){
        $this->outputError('Nu se poate elimina serviciul odata ce a fost rezervat.');
      }
	  
	  $this->outputError('Nu se poate elimina serviciul. Functionalitate blocata!');
	  
      $trip_services = array();
      if(isset($order->services)){
        $trip_services = unserialize($order->services);
      }
      $service_id = (int)$this->input->post('service_id');
      if(isset($trip_services[$service_id])){
        array_splice($trip_services, $service_id);
      }
      $this->data['services'] = $trip_services;
      $data = array();
      $data['id'] = $id;
      $services = serialize($trip_services);
      $data['services'] = $services;
      if(empty($trip_services)){
        $data['total'] = 0;
        $data['amount'] = 0;
      }
      $this->TripOrder_model->saveOrder($data);
      
      $this->addMessage('Serviciul a fost eliminat', 'success');
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
  public function bookServices() {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $id = (int)$this->input->post('order_id');
      if(!$id){
        $this->outputError('Comanda noua. Va rugam adaugati un serviciu.');
      }
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Comanda invalida.');
      }
      $trip_services = array();
      if($order->provider !== 'travelfuse'){
        $this->outputError('Invalid order provider');
      }
      if(isset($order->services)){
        $trip_services = unserialize($order->services);
      }
      if(empty($trip_services)){
        $this->outputError('Niciun serviciu adaugat in comanda.');
      }
	  
      try{
        $this->TravelFuse_model->bookServices($order, $trip_services);
      } catch(Exception $e){
        $this->outputError($e->getMessage());
      }
      $order = $this->TripOrder_model->getOrderById($id);
      $this->data['trip_order_id'] = $order->trip_order_id;
      $this->addMessage('Rezervarea a fost creata cu succes');
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
}