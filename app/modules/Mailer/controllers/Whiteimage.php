<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class WhiteImage extends MX_Controller {
  private function __setData($key, $value, $overwrite = false) {
    if(!$overwrite && isset($this->data[$key])){
      return;
    }
    $this->data[$key] = &$value;
  }
  private function __getData($key, &$default_value = null) {
    return isset($this->data[$key]) ? $this->data[$key] : $default_value;
  }
  public function count() {
    $this->load->model('WhiteImage_model');
    $search = array();
    $response = $this->WhiteImage_model->count($search);
    print_r($response);
  }
  public function save() {
    $this->load->model('WhiteImage_model');
    $data = array();
    $data['email'] = 'tudor.chirvasa@lisal.ro';
    $data['firstname'] = 'Tudor';
    $data['lastname'] = 'Chirvasa';
    $data['city'] = 'Bucharest';
    $response = $this->WhiteImage_model->save($data);
    print_r($response);
  }
  public function update() {
    $this->load->model('WhiteImage_model');
    $update_by = 'emailid';
    $$update_by = '18913';
    $data = array();
    $data['email'] = 'tudorx.chirvasa@lisal.ro';
    $data['firstname'] = 'T';
    $data['lastname'] = 'C';
    $data['data_nastere'] = '2';
    $data['telefon'] = '2';
    $data['from_analytics'] = 'no';
    $data['sursa'] = '1';
    $data['fullname'] = '1';
    $data['city'] = '2';
    $data['check'] = '2';
    $response = $this->WhiteImage_model->update($$update_by, $data, $update_by);
    print_r($response);
  }
  public function mobile_save() {
    $this->load->model('WhiteImage_model');
    $update_by = 'emailid';
    $$update_by = '18913';
    $data = array();
    $data['email'] = 'tudor.chirvasa@lisal.ro';
    $data['firstname'] = 'Tudor';
    $data['lastname'] = 'Chirvasa';
    $data['data_nastere'] = '1989-11-23';
    $data['telefon'] = '0771255279';
    $data['from_analytics'] = 'no';
    $data['age'] = '28';
    $data['sursa'] = 'Test';
    $data['fullname'] = 'Chirvasa Tudor';
    $data['city'] = 'Bucharest';
    $data['check'] = 'test';
    $response = $this->WhiteImage_model->mobile_save($$update_by, $data, $update_by);
    print_r($response);
  }
  public function select() {
    $this->load->model('WhiteImage_model');
    $offset = 0;
    $limit = 10;
    $search = array(
      'email|tudor.chirvasa@lisal.ro|1'
    );
    $return_fields = 'all';
    $response = $this->WhiteImage_model->select($offset, $limit, $search, $return_fields);
    print_r($response);
  }
  public function select_one() {
    $this->load->model('WhiteImage_model');
    $search = array(
      'email|tudor.chirvasa@lisal.ro|1'
    );
    $return_fields = 'all';
    $response = $this->WhiteImage_model->select_one($search,$return_fields);
    print_r($response);
  }
  public function unsubscribe() {
    $this->load->model('WhiteImage_model');
    $emailid = '18913';
    $response = $this->WhiteImage_model->unsubscribe($emailid);
    print_r($response);
  }
  public function resubscribe() {
    $this->load->model('WhiteImage_model');
    $emailid = '18913';
    $response = $this->WhiteImage_model->resubscribe($emailid);
    print_r($response);
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