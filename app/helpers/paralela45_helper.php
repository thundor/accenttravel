<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Paralela45_API {
  /**
   * The API endpoint url
   * @var string 
   */
  public $api_url;

  /**
   * The Application Username
   * @var string 
   */
  public $username;
  
  /**
   * The Application Password
   * @var string 
   */
  public $password;

  /**
   * API language url
   * @var string 
   */
  public $lang;

  /**
   * The session Object
   * @var stdClass 
   */
  public $session;
  /**
   * The session save path
   * @var string
   */
  public $session_path;
  // public $test = false;

  /**
   * Constructor
   * @param string $api_url API endpoint URL
   * @param string $app_id Application ID
   * @param string $secret_key Application Secret Key
   * @param string $lang Application language (e.g. EN)
   * @param string $auth_Value Authorization Token
   * @param int $auth_TTL Authorization TTL
   * @param int $auth_Lifes Authorization lives
   * @param int $auth_Time Authorization generation timestamp
   */
  public function __construct($api_url, $username, $password, $lang = 'RO') {
    assert(is_string($api_url) && filter_var($api_url, FILTER_VALIDATE_URL), 'The api_url parameter is mandatory and must be a valid URL');
    assert(is_string($username) && ($username !== ''), 'The username parameter is mandatory');
    assert(is_string($password) && ($password !== ''), 'The password parameter is mandatory');
    assert(is_string($lang) && ($lang !== ''), 'The lang parameter is mandatory');

    $this->api_url = $api_url;
    $this->username = $username;
    $this->password = $password;
    $this->lang = $lang;
  }
  /**
   * Call API
   * @param string $path Path to be appended to the request URL
   * @param array $get $_GET parameters sent in request
   * @param array $post $_POST parameters sent in request
   * @return stdClass The entire object returned by the API
   */
  public $requests = array();
  public $request;
  public function request($request) {
    $postfields = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $postfields .= '<Request RequestType="' . $request->request_type . '">' . PHP_EOL;
    $postfields .= '  <AuditInfo>' . PHP_EOL;
    $postfields .= '    <RequestId>' . $request->id . '</RequestId>' . PHP_EOL;
    $postfields .= '    <RequestUser>' . $this->username . '</RequestUser>' . PHP_EOL;
    $postfields .= '    <RequestPass>' . $this->password . '</RequestPass>' . PHP_EOL;
    $postfields .= '    <RequestTime>' . $request->request_time . '</RequestTime>' . PHP_EOL;
    if($request->request_lang){
      $postfields .= '    <RequestLang>' . $request->request_lang . '</RequestLang>' . PHP_EOL;
    }
    $postfields .= '  </AuditInfo>' . PHP_EOL;
    $postfields .= '  <RequestDetails>' . PHP_EOL;
    $postfields .= $request->request;
    $postfields .= '  </RequestDetails>' . PHP_EOL;
    $postfields .= '</Request>' . PHP_EOL;
    $response_dir_path = APPPATH.'logs/paralela45/' . $request->request_type . '/';
    if(!is_dir($response_dir_path)){
      mkdir($response_dir_path,0777,true);
    }
    file_put_contents($response_dir_path . $request->id . '_request.txt',$postfields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_FAILONERROR, true);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // if($this->test){
      // echo '<pre>';
      // var_dump($result);
      // var_dump($http_code);
      // var_dump(curl_error($ch));
      // var_dump(curl_getinfo($ch));
      // die;
    // }
    $response = false;
    if (in_array($http_code, array(200))) {
      $response = $result;
    }
    
    $object = new stdClass;
    $object->http_code = $http_code;
    $object->request = $request;
    $object->response = $response;
    $this->request = &$object;
    $this->requests[] = $this->request;
    return $response;
  }
}