<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Account extends MX_Controller {
  function index() {
    if(!$this->user->can('frontend-login')){
      redirect('');
    }
    redirect('account/profile');
  }
  function login() {
    if ($this->input->is_ajax_request()) {
      $username = $this->input->post('username');
      $password = $this->input->post('password');
      
      $this->load->library('form_validation');
      $this->form_validation->set_rules('username', 'Username', 'trim|required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      if ($this->form_validation->run() == FALSE) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      } else {
        $this->load->model('Account_model');
        $user = $this->Account_model->getAccountLogin($username, $password);
        if ($user) {
          if(!$user->can('frontend-login')) {
            $this->outputError('Acces interzis');
          } else {
            $this->_performLogin($user->id);
          }
        } else {
          $this->outputError('Credentiale invalide.');
        }
      }
    }
    $this->outputError('Invalid Data');
  }
  function enable_cookies() {
    $this->session->set_userdata('cookie_accepted', 1);
    $this->output();
  }
  
  protected function _performLogin($user_id) {
    $prevurl = $this->session->userdata('prevURL');
    if (!empty($prevurl)) {
      $url = $prevurl;
    } else {
      $url = site_url('');
    }
    $this->session->set_userdata('logged_in', $user_id);
    $remember = $this->input->post('remember');
    if (empty($remember)) {
      $this->session->sess_expire_on_close = TRUE;
    }
    $this->data['url'] = $url;
    $this->addMessage('Autentificare reusita...','success');
    $this->output();
  }
  function register() {
    if ($this->input->is_ajax_request()) {
      $data = array();
      
      $this->load->library('form_validation');
      $this->form_validation->set_rules('title', 'Titlu', 'trim|in_list[mr,mrs,ms]',array(
        'in_list' => 'Titlu invalid',
      ));
      $title = trim($this->input->post('title'));
      $data['title'] = $title;
      
      $this->form_validation->set_rules('email', 'Email nou', 'trim|required|max_length[255]|valid_email|is_unique[ac_user.user_username]|is_unique[ac_user.user_email]', array(
        'is_unique' => 'Acest email este deja utilizat in platforma.',
      ));
      $email = trim($this->input->post('email'));
      $data['user_email'] = $email;
      $data['user_username'] = $email;
	  
	  if(preg_match('~\.ru$~i', $email)){
		  $this->outputError('A fost intalnita o problema la crearea contului.');
	  }
      
      $password = $this->input->post('password');
      $confirm_password = $this->input->post('confirm_password');
      
      $phone = trim($this->input->post('phone'));
      $this->form_validation->set_rules('phone', 'Telefon', 'trim|required|max_length[100]');
      $data['phone'] = $phone;
      
      $this->form_validation->set_rules('password', 'Parola', 'min_length[8]');
      $this->form_validation->set_rules('confirm_password', 'Confirmare parola', 'matches[password]',array(
        'matches' => 'Parolele nu coincid',
      ));
      $sh_password = sha1($password);
      $data['user_password'] = $sh_password;
      
      $this->form_validation->set_rules('firstname', 'Prenume', 'trim|max_length[255]');
      $firstname = trim($this->input->post('firstname'));
      $data['user_firstname'] = $firstname;

      $this->form_validation->set_rules('lastname', 'Nume familie', 'trim|required|max_length[255]');
      $lastname = trim($this->input->post('lastname'));
      $data['user_lastname'] = $lastname;
      
      $this->form_validation->set_rules('tos', 'Termeni si conditii', 'required', array(
        'required'=>'Pentru a putea continua cu crearea contului este necesar sa vizualizati si sa fiti de acord cu Termenii si conditiile Accent Travel & Events',
      ));
      
      $newsletter = $this->input->post('newsletter');
      $data['newsletter'] = $newsletter ? 1 : 0;
      
      if ($this->form_validation->run() == FALSE) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      if(in_array($data['title'],array('mr'))){
        $data['gender'] = 'm';
      } elseif(in_array($data['title'],array('mrs','ms'))){
        $data['gender'] = 'f';
      }
      $data['user_type'] = 'customer';
      $data['user_status'] = 1;
      $data['tos'] = 1;
      $data['user_created_datetime'] = date("Y-m-d H:i:s");
      
      $data['country'] = 'RO';
      $data['phone_prefix'] = $data['country'];
      $data['contact_phone_prefix'] = $data['country'];
      $data['pf_phone_prefix'] = $data['country'];
      $data['pj_phone_prefix'] = $data['country'];
      $data['pf_email'] = $data['user_email'];
      $data['pj_email'] = $data['user_email'];
      $data['contact_lastname'] = $data['user_lastname'];
      $data['pf_lastname'] = $data['user_lastname'];
      $data['pj_lastname'] = $data['user_lastname'];
      $data['contact_firstname'] = $data['user_firstname'];
      $data['pf_firstname'] = $data['user_firstname'];
      $data['pj_firstname'] = $data['user_firstname'];
      $data['pf_country'] = $data['country'];
      $data['pj_country'] = $data['country'];
      $data['contact_phone'] = $data['phone'];
      $data['pf_phone'] = $data['phone'];
      $data['pj_phone'] = $data['phone'];
      $data['invoice'] = 'pf';
      
      $this->load->model('Account_model');
      
      $user_id = $this->Account_model->saveAccount($data);
      if(!$user_id){
        $this->outputError('A fost intalnita o problema la crearea contului.');
      }
      Modules :: run ('Mailer/account_register', array('user_id'=>$user_id, 'password' => $password));
      unset($this->form_validation);
      return $this->_performLogin($user_id);
    }
    $this->outputError('Invalid Data');
  }
  function logout() {
    $this->session->sess_destroy();
    redirect('');
  }
  
  function password() {
	
	$hash = $this->input->get('hash');
	
    if ($this->input->is_ajax_request()) {
		if(empty($hash)){
			$this->outputError('Lipsa cheie de resetare parola');
		}
		
        $this->load->model('Account_model');
		$user = $this->Account_model->getAccountByHash($hash);
		if(!$user){
			$this->outputError('Cod de resetare expirat');
		}
		
      $this->load->library('form_validation');
	  
      $new_password = $this->input->post('new_password');
      $confirm_new_password = $this->input->post('confirm_new_password');
      $sh_new_password = sha1($confirm_new_password);
	  
      $should_validate = true;
      $this->form_validation->set_rules('new_password', 'Parola noua', 'min_length[8]');
      $this->form_validation->set_rules('confirm_new_password', 'Confirmare parola', 'matches[new_password]',array(
        'matches' => 'Valorile introduse in cele doua campuri (Parola noua si Confirmare parola) trebuie sa coincida.',
      ));

      if ($this->form_validation->run() == FALSE) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      } else {
		$message = 'Parola a fost schimbata, va puteti autentifica cu noua parola.';
		$type = 'success';
		$this->addMessage($message, $type);
		$this->saveMessagesInSession();
		
		$this->db->where('user_id', $user->id);
		$token = md5(sha1(date('Y-m-d H:i:s') . microtime(true) . '-' . $user->id));
		
		// Create new Token in order to delay future resets from this user, preventing mail spam
		$this->db->update('ac_user', [
			'user_password' => $sh_new_password,
			'token' => md5(sha1(date('Y-m-d H:i:s') . microtime(true) . '-' . $user->id)),
			'token_expiry' => date('Y-m-d H:i:s', strtotime('+20 minutes')),
			'resets' => 1,
		]);
		
        // $user = $this->Account_model->getAccountLogin($username, $password);
        
		$this->output();
      }
    }
	
	if(empty($hash)){
		$this->redirect('', 'Lipsa cheie de resetare parola', 'error');
	}
	$this->load->model('Account_model');
	$user = $this->Account_model->getAccountByHash($hash);
	if(!$user){
		$this->redirect('', 'Cod de resetare expirat', 'error');
	}
	
	$this->data['hash'] = $hash;
	
	$this->theme->view('account/password', $this->data);
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
		$this->data['url'] = site_url('');
		$this->addMessage('Un email a fost trimis la aceasta adresa de email. Va rugam sa verificati casuta de e-mail si sa urmariti instructiunile.','success');
		$this->output();
    }
  }
  public function profile() {
    if(!$this->user->canAny('frontend-account-profile-access','backend-account-profile-access')){
      redirect('frontend');
    }
    $this->load->model('Account_model');
    $this->data['user'] = $this->user;
    $this->theme->view('account/profile', $this->data);
  }
  
  public function save() {
    if(!$this->user->canAny('frontend-account-profile-access','backend-account-profile-access')){
      redirect('frontend');
    }
    if(!$this->user->canAny('frontend-account-profile-save','backend-account-profile-save')){
      $this->outputError('Invalid access');
    }
    $user = $this->user;
    
    $data = array();
    $should_validate = false;
    
    $this->load->library('form_validation');
    $password = $this->input->post('password');
    if(isset($password)){
      $new_password = $this->input->post('new_password');
      $confirm_new_password = $this->input->post('confirm_new_password');
      $sh_password = sha1($password);
      if($sh_password !== $user->password){
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
    if($user->type !== 'customer'){
      $username = $this->input->post('username');
      if(isset($username)){
        $changed_username = false;
        $username = trim($username);
        if($user->username != $username){
          $changed_username = true;
        }
        $should_validate = true;
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]' . ($changed_username ? '|is_unique[ac_user.user_username]' : ''),array(
          'is_unique' => 'Acest username este deja utilizat in platforma.',
        ));
        $data['user_username'] = $username;
        $this->data['username'] = $username;
      }
    }
    $email = $this->input->post('email');
    if(isset($email)){
      $email = trim($email);
      $should_validate = true;
      $this->form_validation->set_rules('email', 'Email nou', 'trim' . ($email !== $user->email ? '|required|max_length[255]|valid_email|is_unique[ac_user.user_username]|is_unique[ac_user.user_email]' : ''), array(
        'is_unique' => 'Acest email este deja utilizat in platforma.',
      ));
      $this->form_validation->set_rules('email_confirm', 'Confirmare adresa email', 'trim|matches[email]',array(
        'matches' => 'Valorile introduse in cele doua campuri (Email nou si Confirmare email) trebuie sa coincida.'
      ));
      if($email !== $user->email){
        if($user->type === 'customer'){
          $data['user_username'] = $email;
        }
        $data['user_email'] = $email;
      }
      $this->data['email'] = $email;
    }
    
    $this->load->model('Account_model');
    $this->Account_model->applyGeneralFormValidation($this,$user,$data,$should_validate);
      
    if ($should_validate && $this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $this->addMessage('Nicio modificare efectuata','info');
    if(!empty($data)){
      $data['user_id'] = (int)$user->id;
      $data['user_modified_by'] = $user->id;
      $data['user_modified_datetime'] = date("Y-m-d H:i:s");
      
      $this->Account_model->applyGeneralFormSaveAdaptation($this,$user,$data);
      $this->Account_model->saveAccount($data);
      $this->addMessage('Informatiile au fost actualizate');
    }
    $this->output();
  }
}