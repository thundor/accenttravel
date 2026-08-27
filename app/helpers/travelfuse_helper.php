<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TravelFuse_API {
  /**
   * The API endpoint url
   * @var string 
   */
  public $api_url;

  /**
   * The Application Api Key
   * @var string 
   */
  public $api_key;

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
  public function __construct($api_url, $api_key) {
    assert(is_string($api_url) && filter_var($api_url, FILTER_VALIDATE_URL), 'The api_url parameter is mandatory and must be a valid URL');
    assert(is_string($api_key) && ($api_key !== ''), 'The api key parameter is mandatory');

    $this->api_url = $api_url;
    $this->api_key = $api_key;
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
  
  function isValidJsonFile($filePath, $check_empty = false) {
    // Check if the file exists and is readable
    if (!is_readable($filePath)) {
        return false;
    }

    // Open the file for reading
    $file = fopen($filePath, 'r');
    if (!$file) {
        return false;
    }

    // Read the first character
    $firstChar = fgetc($file);
	
	$return = true;
	if('{' === $firstChar || '[' === $firstChar){
		if($check_empty){
			$char = '';
			while(trim($char) === ''){
				$char = fgetc($file);
			}
			if(($firstChar === '{' && $char === '}') || ($firstChar === '[' && $char === ']')){
				$return = false;
			}
			if(!is_bool($check_empty)){
				if(is_array($check_empty)){
					if(isset($check_empty[0]) && !in_array($char, $check_empty)){
						$return = false;
					} else {
						if(!isset($check_empty[$firstChar])){
							$return = false;
						}
						if($firstChar == $check_empty[$firstChar] && !in_array($char, (array)$check_empty[$firstChar])) $return = false;
					}
				} else {
					if($char != $check_empty){
						$return = false;
					}
				}
			}
		}
		if($return){
			// Seek to the end of the file and read the last character
			fseek($file, -1, SEEK_END);
			$lastChar = fgetc($file);
		}
	}

    // Close the file
    fclose($file);
	if(!$return) return false;
    // Check if it starts with '{' and ends with '}'
    return ($firstChar === '{' && $lastChar === '}') || ($firstChar === '[' && $lastChar === ']');
  }
  
  public function request($get=[], $post=[], $return_response = true) {
	$request_type = $get['type'] ?? '-';
	$request_call = $get['call'] ?? '-';
	$request_id = intval(microtime(true) * 1000) . '-'. substr(md5(json_encode([$get,$post])),0,12);
    $response_rel_path = 'logs/travelfuse/' . $request_type . '/' . $request_call . '/' . $request_id . '/';
    $response_dir_path = APPPATH . $response_rel_path;
    if(!is_dir($response_dir_path)){
      mkdir($response_dir_path,0777,true);
    }
	
	$url = $this->api_url . '?' . http_build_query($get);
	$request = [
		'url' => $url,
		'post' => $post,
		'get' => $get,
	];
    file_put_contents($response_dir_path . 'request.json', json_encode($request, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
	$fp = fopen($response_dir_path . 'file_partial.json', 'w');
	
	flock($fp, LOCK_EX);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
	$post['auth_key'] = $this->api_key;
	$postfields = http_build_query($post);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_FILE, $fp);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 360);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    
    $result = curl_exec($ch);
	flock($fp, LOCK_UN);
	fclose($fp);
	
	
	
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_info = curl_getinfo($ch);
	curl_close($ch);
	
	$good_file = false;
	if($this->isValidJsonFile($response_dir_path . 'file_partial.json')){
		$good_file = true;
		rename($response_dir_path . 'file_partial.json',$response_dir_path . 'file.json');
	}
	
    $response = false;
    if (in_array($http_code, array(200))) {
      $response = $result;
    }
	
	file_put_contents($response_dir_path . 'http_code.json', json_encode($curl_info, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'response.json',$result);
    
    $object = new stdClass;
    $object->http_code = $http_code;
    $object->request = $request;
    $object->result = $result;
	$object->response = $response;
	if($return_response){
		if($good_file){
			$response = $object->response = file_get_contents($response_dir_path . 'file.json');
		}
	}
	if($good_file){
		$object->file = $response_rel_path . 'file.json';
	}
    $this->request = &$object;
	if(!$return_response){
		if($good_file){
			return $object->file;
		}
		$response = false;
	}
    // $this->requests[] = $this->request;
    return $response;
  }
}