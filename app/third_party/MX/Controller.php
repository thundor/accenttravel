<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');

/** load the CI class for Modular Extensions * */
require dirname(__FILE__) . '/Base.php';

class MX_Controller {

  public $autoload = array();
  public $parameters = array();
  public $data = array();
  public $user;
  public $currency_symbol;

  public function __construct() {
	  
	
	$this->load->model('Fault_model');
	$this->load->helper('cookie');
			
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if ($this->config->item('captcha_validate_page')){
			$excluded_uri = false;
			if ($exclude_uris = config_item('captcha_exclude_uris'))
			{
				$uri = load_class('URI', 'core');
				foreach ($exclude_uris as $excluded)
				{
					if (preg_match('#^'.$excluded.'$#i'.(UTF8_ENABLED ? 'u' : ''), $uri->uri_string()))
					{
						$excluded_uri = true;
						break;
					}
				}
			}
			if(!$excluded_uri && get_class($this) !== 'Captcha' && (!$this->session->userdata('is_human') || ($this->session->userdata('is_human') != get_cookie('is_human')))){
				$this->Fault_model->insertFault(['forbidden' => 1]);
				header('HTTP/1.0 403 Forbidden');
				echo 'Forbidden access.';
				exit;
			}
		}
	}
	$fault = $this->Fault_model->getFaultByIp();
	if($fault && $fault->end_status){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden access. Your ip ' . $this->Fault_model->getIp() . ' has been marked as malicious and blocked permanently.';
		exit;
	}
	  
    $class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	if($class != 'Backend' && preg_match('~^/?backend(/|\?|$)~', $request_uri)){
		if(!$this->session->userdata('allowbk')){
			header('HTTP/1.0 403 Forbidden');
			echo 'Forbidden access.';
			exit;
		}
	}
    log_message('debug', $class . " MX_Controller Initialized");
    Modules::$registry[strtolower($class)] = $this;

    /* copy a loader instance and initialize */
    $this->load = clone load_class('Loader');
    $this->load->initialize($this);

    /* autoload module items */
    $this->load->_autoloader($this->autoload);
    $session_user_id = $this->session->userdata('logged_in');
    $this->load->library('User');
    if($session_user_id){
      $this->load->model('Account_model');
      CI::$APP->user = $this->Account_model->getAccountById($session_user_id);
    }
    
    CI::$APP->__messages = array();
    CI::$APP->__message = null;
    CI::$APP->__message_type = null;
    CI::$APP->__response = null;
    CI::$APP->__call = null;
    CI::$APP->__calls = null;
    CI::$APP->__results = null;
    
    CI::$APP->currency_symbol = '€';
    $this->user = &CI::$APP->user;
    $this->currency_symbol = &CI::$APP->currency_symbol;
	
	$this->theme->_can_edit = CI::$APP->user->can('backend-access');
  }
  public $response_is_global = false;
  public function makeResponseGlobal($global = true) {
    if((bool)$global === $this->response_is_global){
      return;
    }
    $this->response_is_global = $global;
    if($global){
      $this->messages = &CI::$APP->__messages;
      $this->message = &CI::$APP->__message;
      $this->message_type = &CI::$APP->__message_type;
      $this->response = &CI::$APP->__response;
      $this->call = &CI::$APP->__call;
      $this->calls = &CI::$APP->__calls;
      $this->results = &CI::$APP->__results;
    } else {
      $this->messages = CI::$APP->__messages;
      $this->message = CI::$APP->__message;
      $this->message_type = CI::$APP->__message_type;
      $this->response = CI::$APP->__response;
      $this->call = CI::$APP->__call;
      $this->calls = CI::$APP->__calls;
      $this->results = CI::$APP->__results;
    }
  }
  public function __get($class) {
    return CI::$APP->$class;
  }
  
  protected function requireCapability($permission='', $redirect=true) {
    if($this->user->can($permission)){
      return;
    }
    if(!$redirect){
      echo '{status:"error";message:"You do not have permission to access this"}';
      exit();
    }
    redirect('backend/account/login');
  }
  protected function requireCapabilities() {
    $capabilities = func_get_args();
    $last_capability = array_pop($capabilities);
    $redirect = true;
    if(is_string($last_capability)){
      $capabilities[] = $last_capability;
    } else {
      $redirect = $last_capability;
    }
    $this->requireCapability($capabilities,$redirect);
  }
  
  protected $messages = array();
  protected $message = null;
  protected $message_type = null;
  protected $response = null;
  protected $call = null;
  protected $calls = null;
  protected $results = array();

  protected function addError($message) {
    $this->addMessage($message,'error');
  }
  protected function addMessage($message, $type = 'info') {
    if (!isset($this->messages[$type])) {
      $this->messages[$type] = array();
    }
    $this->messages[$type][] = $message;
    $this->message = $message;
    $this->message_type = $type;
  }

  protected function outputError($message) {
    $this->addError($message);
    $this->output('error');
  }
  protected function getTripError($message) {
	$this->load->model('Trip_model');
    if(1){
      $call_response = $this->Trip_model->api && $this->Trip_model->api->call ? $this->Trip_model->api->call->result_decoded : null;
      $this->calls =  $this->Trip_model->api ? $this->Trip_model->api->calls : [];
      $this->call =  $this->Trip_model->api ? $this->Trip_model->api->call : null;
      if($this->call && !$this->call->response && $call_response){
        if(isset($call_response->Status)){
          $message = 'Trip Error: (Cod ' . $call_response->Status . ') ' . $call_response->Message;
        } else {
          $message = 'Trip Error: (Cod ' . $call_response->status . ') ' . $call_response->title . ': ' . $call_response->detail;
        }
      }
    }
    return $message;
  }
  protected function outputTripError($message='Eroare Trip') {
	  if(null === $message){
		  $message = $this->message;
	  }
    $message = $this->getTripError($message);
	if($message){
		$this->outputError($message);
	} else {
		$this->output();
	}
  }

  protected function output($status = 'success') {
    $response = array(
      'status' => $status,
      'call' => $this->call,
      'calls' => $this->calls,
      'response' => $this->response,
      'message' => $this->message,
      'message_type' => $this->message_type,
      'messages' => $this->messages,
      'data' => $this->data,
    );

    echo json_encode($response);
    die;
  }
  
  protected function saveMessagesInSession() {
    $this->session->set_flashdata('flashmsg', $this->message);
    $this->session->set_flashdata('flashmsgtype', $this->message_type);
    $this->session->set_flashdata('flashmsgs', $this->messages);
  }
  protected function redirect($route, $message=null, $type='info') {
    if(!$route){
      $route = '';
    }
    if($this->user && $this->user->id && $this->user->can('backend-access')){
      $message = $this->getTripError($message);
      if($trip_error !== $message){
        $message = '' . $message . ' ' . $trip_error;
      }
    }
    if(!is_null($message)){
      $this->addMessage($message, $type);
    }
    $this->saveMessagesInSession();
    redirect($route);
	exit;
  }
  public function _remap($method, $params) {
    $this->parameters = &$params;
    $this->uri->rsegments = array_values($this->uri->rsegments);
    if(method_exists($this, $method)){
      if(!in_array($method, $this->uri->segments)){
        $this->uri->segments[] = $method;
      }
      $reflection = new ReflectionMethod($this, $method);
      if ($reflection->isPublic() && !$reflection->isConstructor()){
        return call_user_func_array(array(&$this, $method), $params);
      }
    }
    array_splice( $params, 0, 0, array($method) );
    $method = 'index';
    array_splice( $this->uri->segments, count($this->uri->segments) - count($params), 0, array($method) );
    array_splice( $this->uri->rsegments, count($this->uri->rsegments) - count($params), 0, array($method) );
    if(method_exists($this, $method)){
      $this->router->method = $method;
      return call_user_func_array(array(&$this, $method), $params);
    }
  }
	protected function deleteCache($file){
		deleteCacheByFile($file);
	}
	protected function getCache($file, $default = null){
		return getCacheByFile($file, $default);
	}
	protected function setCache($file, $value = null){
		setCacheByFile($file, $value);
	}
}