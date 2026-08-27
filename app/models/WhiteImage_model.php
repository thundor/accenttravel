<?php

class WhiteImage_model extends CI_Model {

  public $api;
  
  function __construct() {
    parent::__construct();
  }
  
  public function get_api(){
    if($this->api){
      return $this->api;
    }
    $base_url = 'https://www.whiteimage.eu/clients/wlm/services/subscriber_service.php';
    $service_key = 'df12bbea305e1428e46f13a92b7bd727';
    $list_id = 5743;
    $this->load->helper("whiteimageapi",'WhiteImageApi');  
    $this->api = new WhiteImageApi($base_url,$service_key,$list_id);
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
  public function count($search = array()){
    $api = $this->get_api();
    $data = array();
    $data['search'] = $search;
    return $api->apiCall(__FUNCTION__, $data);
  }
  public function select($offset=0, $limit=10, $search = array(), $return_fields = 'all'){
    $api = $this->get_api();
    $data = array();
    $data['offset'] = $offset;
    $data['limit'] = $limit;
    $data['search'] = $search;
    $data['return_fields'] = $return_fields;
    return $api->apiCall(__FUNCTION__, $data);
  }
  
  public function select_one($search = array(), $return_fields = 'all'){
    $api = $this->get_api();
    $data = array();
    $data['search'] = $search;
    $data['return_fields'] = $return_fields;
    return $api->apiCall(__FUNCTION__, $data);
  }
  public function save($fields){
    $api = $this->get_api();
    $data['fv'] = $fields;
    return $api->apiCall(__FUNCTION__, $data);
  }
  public function update($value, $fields = array(), $update_by = 'emailid'){
    $api = $this->get_api();
    $data['update_by'] = $update_by;
    $data[$update_by] = $value;
    $data['fv'] = $fields;
    return $api->apiCall(__FUNCTION__, $data);
  }
  public function unsubscribe($emailid){
    $api = $this->get_api();
    $data['emailid'] = $emailid;
    return $api->apiCall(__FUNCTION__, $data);
  }
  public function resubscribe($emailid){
    $api = $this->get_api();
    $data['emailid'] = $emailid;
    return $api->apiCall(__FUNCTION__, $data);
  }
}