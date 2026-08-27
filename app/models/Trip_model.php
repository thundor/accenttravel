<?php

class Trip_model extends CI_Model {

  public $api;
  protected $dbo;
  
  function __construct() {
    parent::__construct();
    $this->load->helper("trip",'Trip');  
  }

  function get_front_settings($force=false) {
    static $settings;
    if($settings && !$force){
      return $settings;
    }
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('trip_settings',null,array(
      'trip_app_id'=>null,
      'trip_app_secret'=>null,
      'trip_username'=>null,
      'trip_password'=>null,
      'trip_endpoint'=>null,
    ));
    if(!$settings){
      $settings = array();
    }
    $this->load->library('encryption');
    if(isset($settings['trip_app_id'])){
      $settings['trip_app_id'] = $this->encryption->decrypt($settings['trip_app_id']);
    }
    if(isset($settings['trip_app_secret'])){
      $settings['trip_app_secret'] = $this->encryption->decrypt($settings['trip_app_secret']);
    }
    if(isset($settings['trip_username'])){
      $settings['trip_username'] = $this->encryption->decrypt($settings['trip_username']);
    }
    if(isset($settings['trip_password'])){
      $settings['trip_password'] = $this->encryption->decrypt($settings['trip_password']);
    }
    if(isset($settings['trip_endpoint'])){
      $settings['trip_endpoint'] = $this->encryption->decrypt($settings['trip_endpoint']);
    }
    return $settings;
  }

  public function get_api(){
    if($this->api){
      return $this->api;
    }
    $settings = $this->get_front_settings();
    $this->api = new TRIP($settings['trip_endpoint'],$settings['trip_app_id'],$settings['trip_app_secret'],$settings['trip_username'],$settings['trip_password']);
    $this->api->setSession($this->session, 'trip/token/' . md5(json_encode($settings)));
    return $this->api;
  }
  public function clean(&$data){
    foreach($data as $k => &$v){
      if(is_array($v)){
        $this->clean($v);
        if(empty($v)){
          unset($data[$k]);
          continue;
        }
      }
      if(!isset($data[$k])){
        unset($data[$k]);
      }
    }
  }
}