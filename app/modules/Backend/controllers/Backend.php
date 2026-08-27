<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Backend extends MX_Controller {
  function __construct() {
    parent :: __construct();
  }
  public function allowlogin($a = null) {
	$this->session->set_userdata('allowbk', 1);
	$this->index();
  }
  public function index($a = null) {
	  if($a){
		if(filter_var(
			$this->Fault_model->getIp(), 
			FILTER_VALIDATE_IP, 
			FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
		)){
			$this->Fault_model->insertFault(['forbidden' => 1]);
		}
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden access.';
		exit;
	  }
	  if(!$this->session->userdata('allowbk')){
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden access.';
		exit;
	  }
    $this->requireCapability('backend-access');
    // $this->redirect('/backend/trip/orders');
    $this->theme->view('backend/index', $this->data);
  }
}