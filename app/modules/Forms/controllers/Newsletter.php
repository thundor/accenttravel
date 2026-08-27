<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Newsletter extends MX_Controller {
  public function index() {
	register_shutdown_function(array($this,'logStuff'));
    return $this->subscribe();
  }
  public function logStuff() {
	  $cdate = date('YmdHis');
	  $response_dir_path = APPPATH.'logs/newsletter/';
	  if(!is_dir($response_dir_path)){
		mkdir($response_dir_path,0777,true);
	  }
	  $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	  $ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	  $content = file_get_contents('php://input');
	  $data = [
		'url' => $url,
		'ip' => $ip,
		'server' => $_SERVER,
		'get' => $_GET,
		'post' => $_POST,
		'headers' => getallheaders(),
		'sent_headers' => headers_list(),
		'content' => $content,
	  ];
	  file_put_contents($response_dir_path . $cdate . '.json',json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), FILE_APPEND);
  }
  private function ishuman() {
	  return !(!$this->session->userdata('is_human') || ($this->session->userdata('is_human') != get_cookie('is_human')));
  }
  public function subscribe() {
    $status = $this->input->post('status');
    if(!isset($status)){
      $status = 1;
    }
    if(!$status){
      return $this->unsubscribe();
    }
	if(!$this->ishuman() && empty($this->input->post('g-recaptcha-response'))){
		die;
	}
	register_shutdown_function(array($this,'logStuff'));
	if (!$this->input->is_ajax_request()) {
		$this->redirect('');
	}
    $this->load->library('form_validation');
	
	
    $this->form_validation->set_rules('email', 'Adresa de Email', 'trim|required|max_length[255]|valid_email|min_length[5]', array(
      'required'=>'Campul Adresa de Email este obligatoriu',
      'min_length'=>'Minim 5 caractere',
      'valid_email'=>'Adresa de email invalida',
    ));
	$newsletter_subscribe = $this->session->userdata('newsletter_subscribe');

	$_POST['session_newsletter_subscribe'] = !$newsletter_subscribe || (time() - $newsletter_subscribe) > 600 ? 1 : null;
    $this->form_validation->set_rules('session_newsletter_subscribe', 'Timp asteptare', 'required', array(
      'required'=>'Cererile succesive nu se permit din motive de spam. Va rugam sa asteptati',
    ));
	if(!$this->ishuman()){
    $recaptcha = $this->input->post('g-recaptcha-response');
	$_POST['captcha'] = null;
    if (!empty($recaptcha)) {
      // $_POST['captcha'] = 1;
      // if (!$this->input->is_ajax_request()) {
        $response = $this->recaptcha->verifyResponse($recaptcha);
        if (isset($response['success']) and $response['success'] === true) {
          $_POST['captcha'] = 1;
        }
      // }
    }
    $this->form_validation->set_rules('captcha', 'Captcha', 'required', array(
      'required'=>'Va rugam sa bifati verificarea',
    ));
	}
    $should_validate = true;
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
    
    if ($this->input->is_ajax_request()) {
      if(!$valid){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      } else {
        // $this->addMessage('Validat cu succes.');
        // $this->output();
      }
    }
    if(!$valid){
      $this->redirect('', $this->form_validation->error_string(), 'error');
	  exit;
    }
    $email = trim($this->input->post('email'));
    if($this->user->id && ($this->user->email === $email)){
      $user_data = array();
      $user_data['user_id'] = $this->user->id;
      $user_data['newsletter'] = 1;
      $this->db->where('user_id', $user_data['user_id']);
      $this->db->update('ac_user', $user_data);
    } else {
      $this->db->where('user_email', $email);
      $q = $this->db->get('ac_user');
      $existing_user = $q->row();
      /* if($existing_user){
        $this->addMessage('Este necesar sa va conectati cu acest utilizator pentru a va abona.', 'error');
        if ($this->input->is_ajax_request()) {
          $this->output('error');
        }
        $this->redirect('');
      } */
    }
    $data = array();
    $data['email'] = $email;
    $data['user_id'] = 0;
    $data['status'] = 1;
    $data['time_created'] = date('Y-m-d H:i:s');
    
    $sql = $this->db->insert_string('ac_newsletter', $data) . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)";
    $this->db->query($sql);
    
    $this->load->model('WhiteImage_model');
    $search = array(
      'email|' . $email . '|1'
    );
    $return_fields = 'all';
    $response = $this->WhiteImage_model->select_one($search,$return_fields);
    if($response){
      $response_decoded = json_decode($response);
      if($response_decoded){
        if(!empty($response_decoded->count) && ($response_decoded->subscriber->subscribe_status=='no')){
          $emailid = $response_decoded->subscriber->emailid;
          $this->WhiteImage_model->resubscribe($emailid);
        } else {
          $data = array();
          $data['email'] = $email;
          $data['sursa'] = 'AccentTravel&Events';
          $response = $this->WhiteImage_model->save($data);
        }
      } 
    }
    Modules :: run ('Mailer/newsletter_subscribe', array(
      'to'=>$email,
    ));
	$this->session->set_userdata('newsletter_subscribe', time());
    if (!$this->input->is_ajax_request()) {
      return $this->redirect('newsletter/subscribe');
    }
    $this->addMessage('Abonare efectuata cu succes. Va multumim!','succes');
    if ($this->input->is_ajax_request()) {
      $this->output();
    }
    $this->theme->set_sublayout('frontend/concurs/index');
    $this->theme->view('forms/newsletter/subscribe', $this->data);
  }
  public function unsubscribe($encrypted_email = null) {
	  
    $status = $this->input->post('status');
    if(!isset($status)){
      $status = 0;
    }
    if($status){
      return $this->subscribe();
    }
	if (!$this->input->is_ajax_request()) {
		$this->redirect('');
		exit;
	}
	if(!$this->ishuman() && empty($this->input->post('g-recaptcha-response'))){
		die;
	}
	register_shutdown_function(array($this,'logStuff'));
    $email = $this->input->post('email');
    $skip_validation = false;
    if(!isset($email)){
      if(!isset($encrypted_email)){
        $encrypted_email = $this->input->get('v');
      }
      if(isset($encrypted_email)){
        $this->load->library('encryption');
        $email = $this->encryption->decrypt($encrypted_email);
        $_POST['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'POST';
      }
    }
    $should_validate = false;
	
	$this->load->library('form_validation');
    if(!strlen($email) && $this->user->id){
      $email = $this->user->email;
    } else {
      $this->form_validation->set_rules('email', 'Adresa de Email', 'trim|required|max_length[255]|valid_email|min_length[5]', array(
        'required'=>'Campul Adresa de Email este obligatoriu',
        'min_length'=>'Minim 5 caractere',
        'valid_email'=>'Adresa de email invalida',
      ));
      $should_validate = true;
    }
	
	$should_validate = true;
	$newsletter_subscribe = $this->session->userdata('newsletter_subscribe');
	$_POST['session_newsletter_subscribe'] = !$newsletter_subscribe || (time() - $newsletter_subscribe) > 600 ? 1 : null;
    $this->form_validation->set_rules('session_newsletter_subscribe', 'Timp asteptare', 'required', array(
      'required'=>'Cererile succesive nu se permit din motive de spam. Va rugam sa asteptati',
    ));
	if(!$this->ishuman()){
    $recaptcha = $this->input->post('g-recaptcha-response');
	$_POST['captcha'] = null;
    if (!empty($recaptcha)) {
      // $_POST['captcha'] = 1;
      // if (!$this->input->is_ajax_request()) {
        $response = $this->recaptcha->verifyResponse($recaptcha);
        if (isset($response['success']) and $response['success'] === true) {
          $_POST['captcha'] = 1;
        }
      // }
    }
    $this->form_validation->set_rules('captcha', 'Captcha', 'required', array(
      'required'=>'Va rugam sa bifati verificarea',
    ));
	}
	
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
    if ($this->input->is_ajax_request()) {
      if(!$valid){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      } else {
        // $this->addMessage('Validat cu succes.');
        // $this->output();
      }
    }
    if(!$valid){
      $this->redirect('', $this->form_validation->error_string(), 'error');
	  exit;
    }
    $email = trim($email);
    if($this->user->id && ($this->user->email === $email)){
      $user_data = array();
      $user_data['user_id'] = $this->user->id;
      $user_data['newsletter'] = 0;
      $this->db->where('user_id', $user_data['user_id']);
      $this->db->update('ac_user', $user_data);
    } else {
      $this->db->where('user_email', $email);
      $q = $this->db->get('ac_user');
      $existing_user = $q->row();
      /* if($existing_user){
        $this->addMessage('Este necesar sa va conectati cu acest utilizator pentru a va dezabona.', 'error');
        if ($this->input->is_ajax_request()) {
          $this->output('error');
        }
        $this->redirect('');
      } */
    }
    $data = array();
    $data['email'] = $email;
    $data['status'] = 0;
    
    $sql = $this->db->insert_string('ac_newsletter', $data) . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)";
    $this->db->query($sql);
    
    $this->load->model('WhiteImage_model');
    $search = array(
      'email|' . $email . '|1'
    );
    $return_fields = 'all';
    $response = $this->WhiteImage_model->select_one($search,$return_fields);
    if($response){
      $response_decoded = json_decode($response);
      if($response_decoded && $response_decoded->count){
        $emailid = $response_decoded->subscriber->emailid;
        $this->WhiteImage_model->unsubscribe($emailid);
      }
    }
	$this->session->set_userdata('newsletter_subscribe', time());
    Modules :: run ('Mailer/newsletter_unsubscribe', array(
      'to'=>$email,
    ));
    if (!$this->input->is_ajax_request()) {
      return $this->redirect('newsletter/unsubscribe');
    }
    $this->addMessage('Dezabonare efectuata cu succes.','succes');
    if ($this->input->is_ajax_request()) {
      $this->output();
    }
    $this->theme->set_sublayout('frontend/concurs/index');
    $this->theme->view('forms/newsletter/unsubscribe', $this->data);
  }
}