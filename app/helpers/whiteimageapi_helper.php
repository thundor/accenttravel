<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class WhiteImageApi {
  /**
   * The API endpoint url
   * @var string 
   */
  protected $base_url;

  /**
   * The Service Key
   * @var string 
   */
  protected $service_key;

  /**
   * The List ID
   * @var int 
   */
  protected $list_id;

  /**
   * Constructor
   * @param string $base_url API endpoint URL
   * @param string $service_key Service Key
   * @param int $list_id List ID
   */
  public function __construct($base_url, $service_key, $list_id) {
    assert(is_string($base_url) && filter_var($base_url, FILTER_VALIDATE_URL), 'The base_url parameter is mandatory and must be a valid URL');
    assert(is_string($service_key) && ($service_key !== ''), 'The service_key parameter is mandatory');
    assert(is_numeric($list_id) && (abs($list_id) === (int)$list_id), 'The list_id parameter must be a positive integer');

    $this->base_url = $base_url;
    $this->service_key = $service_key;
    $this->list_id = $list_id;
  }
  /**
   * Safe call API.
   * @param string $method Method to be appended to the request URL
   * @param array $data Extra parameters sent in request
   * @return stdClass The entire object returned by the API
   */
  public function apiCall($method, $data = array()) {
    $data['service_key'] = $this->service_key;
    $data['list_id'] = $this->list_id;
    $data['method'] = $method;
    return $this->call($data);
  }
  /**
   * Call API
   * @param string $path Path to be appended to the request URL
   * @param array $get $_GET parameters sent in request
   * @param array $post $_POST parameters sent in request
   * @return stdClass The entire object returned by the API
   */
  public $calls = array();
  public $call;
  public function call($data = array(), $send_as_post = true) {
    $data_string = http_build_query($data);
    $ch = curl_init();

    $url_append = '?';
    $url = $this->base_url;
    if (!$send_as_post) {
      $url .= $url_append . $data_string;
      $url_append = '&';
    }
    // $header = array();
    // $header[] = "Accept: application/json, text/javascript, */*; q=0.01";
    // $header[] = "x-hash: " . $x_hash;
    // $header[] = "Authorization: " . $this->auth_Value;
    // $header[] = "X-Requested-With: XMLHttpRequest";
    curl_setopt($ch, CURLOPT_URL, $url);
    $send_type = 'GET';
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if ($send_as_post) {
      $send_type = 'POST';
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    }
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = false;
    if (in_array($http_code, array(200))) {
      $response = $result;
    }
    $decode_response = false;
    if($decode_response && false !== $response){
      $response = json_decode($response);
    }
    $object = new stdClass;
    $object->send_type = $send_type;
    $object->data_string = $data_string;
    $object->http_code = $http_code;
    $object->response = $response;
    $object->base_url = $this->base_url;
    $object->data = $data;;
    $object->url = $url;
    $object->result = $result;
    $object->result_decoded = json_decode($result);
    // $object->headers = $header;
    $this->call = &$object;
    $this->calls[] = $this->call;
    return $response;
  }
}