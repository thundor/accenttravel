<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Account extends MX_Controller {
  public function index() {
    $this->theme->set_sublayout('backend/login/index');
    $this->theme->view('login/backend', $this->data);
  }
  function login() {
    if ($this->input->is_ajax_request()) {
      $username = $this->input->post('username');
      $password = $this->input->post('password');
      $this->load->library('form_validation');
		$this->load->model('Fault_model');
		$fault = $this->Fault_model->getFaultByIp();
		if($fault && $fault->end_status){
			$this->outputError('Blocked access');
		}
	  
      $this->form_validation->set_rules('username', 'Username', 'trim|required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      if ($this->form_validation->run() == FALSE) {
        $this->outputError(validation_errors());
      } else {
        $this->load->model('Account_model');
        $user = $this->Account_model->getAccountLogin($username, $password);
        if ($user) {
			$this->Fault_model->insertFault(['faillogin' => 0]);
          if(!$user->can('backend-access')) {
            $this->outputError('Access blocked');
          } else {
            $prevurl = $this->session->userdata('prevURL');
            if (!empty($prevurl)) {
              $url = $prevurl;
            } else {
              $url = site_url('backend');
            }
            $this->session->set_userdata('logged_in', $user->id);
            $remember = $this->input->post('remember');
            if (empty($remember)) {
              $this->session->sess_expire_on_close = TRUE;
            }
            $this->data['url'] = $url;
            $this->output();
          }
        } else {
			$this->load->model('Fault_model');
			$this->Fault_model->insertFault(['faillogin' => 1]);
          $this->outputError('Invalid Login Credentials. ' . (3 - ($fault && $fault->faillogin < 3 ? $fault->faillogin + 1 : 3)) . ' retries left');
        }
      }
    }
    $this->theme->view('login/backend', $this->data);
  }
  function logout() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->session->sess_destroy();
    $this->redirect('backend','Deautentificare efectuata cu succes','success');
  }
  
  
  public function resetpass() {
    if ($this->input->is_ajax_request()) {
		$this->load->model('Account_model');
        $users = $this->Account_model->getAccountsByEmail($this->input->post('email'), array(
			'status' => 1,
		));
		
		if($users){
			$user = array_shift($users);
			try{
				if($user->token_expiry){
					$date = date_create($user->token_expiry);
					$interval1 = date_interval_create_from_date_string('15 minutes');
					$interval2 = date_interval_create_from_date_string((1 * pow($user->resets,2)) . ' minutes');
					$date = date_sub($date, $interval1);   
					$res = date_add($date, $interval2);   
					$d = new DateTime();
					if($res->format('Y-m-d H:i:s') >= $d->format('Y-m-d H:i:s')){
						// throw new Exception('Trebuie sa asteptati pana la ' . $res->format('Y-m-d H:i:s') . ' ' . $d->format('Y-m-d H:i:s'));
						throw new Exception('Trebuie sa asteptati pana la ' . $res->format('Y-m-d H:i:s'));
					}
				}
				$this->db->where('user_id', $user->id);
				$token = md5(sha1(date('Y-m-d H:i:s') . microtime(true) . '-' . $user->id));
				$this->db->update('ac_user', [
					'token' => $token,
					'token_expiry' => date('Y-m-d H:i:s', strtotime('+20 minutes')),
					'resets' => $user->resets + 1,
				]);
				
				$reset_url = site_url('/account/password?hash=' . $token);
				$d = Modules :: run ('Mailer/account_password', array(
					// 'output_html'=>1,
					// 'prevent_send_email'=>1,
					// 'to'=>'tchirvasa@gmail.com',
					'user'=>$user,
					'reset_url'=>$reset_url
				));
				// var_dump($d);
				// return;
				// echo 'test'; die;
				
			} catch(Exception $e){
				$this->outputError($e->getMessage());
			}
			// if($user->email === 'tudor.chirvasa@lisal.ro'){
			// }
		}
		$this->data['url'] = site_url('backend');
		$this->addMessage('Un email a fost trimis la aceasta adresa de email. Va rugam sa verificati casuta de e-mail si sa urmariti instructiunile.','success');
		$this->output();
    }
  }
  
  public function profile() {
    if(!$this->user->can('backend-access','backend-account-profile-access')){
      redirect('backend');
    }
    $this->load->model('Account_model');
    $this->data['user'] = $this->user;
    $this->theme->view('backend/account/profile', $this->data);
  }
  
  public function save() {
    if(!$this->user->can('backend-access','backend-account-profile-save')){
      $this->outputError('Invalid access');
    }
    $user = $this->user;
    $data = array();
    $should_validate = false;
    
    $this->load->library('form_validation');
    $password = $this->input->post('password');
    if(isset($password) && strlen($password)){
      $new_password = $this->input->post('new_password');
      $confirm_new_password = $this->input->post('confirm_new_password');
      $sh_password = sha1($password);
      if($sh_password !== $this->user->password){
        $this->data['errors'] = array(
          'password' => 'Parola introdusa este incorecta',
        );
        $this->outputError('Parola introdusa este incorecta');
      }
      $should_validate = true;
      $this->form_validation->set_rules('new_password', 'Parola noua', 'min_length[8]');
      $this->form_validation->set_rules('confirm_new_password', 'Confirmare parola', 'matches[new_password]',array(
        'matches' => 'Valorile introduse in cele doua campuri (Parola noua si Confirmare parola) trebuie sa coincida.',
      ));
      
      $sh_new_password = sha1($new_password);
      if($sh_password !== $sh_new_password){
        $data['user_password'] = $sh_new_password;
      }
    }
    $changed_username = false;
    $username = trim($this->input->post('username'));
    if($user->username != $username){
      $changed_username = true;
    }
    $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]' . ($changed_username ? '|is_unique[ac_user.user_username]' : ''),array(
      'is_unique' => 'Acest username este deja utilizat in platforma.',
    ));
    $changed_email = false;
    $email = trim($this->input->post('email'));
    if(!$user->id || $user->email != $email){
      $changed_email = true;
    }
    $this->form_validation->set_rules('email', 'Adresa email', 'trim|required|valid_email' . ($changed_email ? '|is_unique[ac_user.user_email]' : ''));
    
    $this->load->model('Account_model');
    $should_validate = true;
    $this->Account_model->applyGeneralFormValidation($this,$user,$data,$should_validate);
    
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data['user_modified_by'] = $this->user->id;
    $data['user_modified_datetime'] = date("Y-m-d H:i:s");
    $data['user_id'] = (int)$this->user->id;
    $data['user_username'] = trim($this->input->post('username'));
    $data['user_email'] = trim($this->input->post('email'));
    $data['user_firstname'] = trim($this->input->post('firstname'));
    $data['user_lastname'] = trim($this->input->post('lastname'));
    
    $this->Account_model->applyGeneralFormSaveAdaptation($this,$user,$data);
    
    $this->Account_model->saveAccount($data);
    $this->addMessage('Informatiile au fost actualizate','success');
    $this->output();
  }
}