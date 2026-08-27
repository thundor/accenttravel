<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Login extends MX_Controller {
  function index() {
    $this->load->library('Facebook');
    $authenticated = $this->facebook->is_authenticated();
    $prevurl = $this->session->userdata('prevURL');
    if($authenticated){
      $this->load->model('Options_model');
      $allowed_social_networks = $this->Options_model->getKeys('social_networks_status');
      if(!in_array('fb', $allowed_social_networks)){
        $this->redirect('','Facebook social login is disabled','error');
      }
      $fb_user = $this->facebook->get_user();
      if (isset($fb_user['error'])){
        $this->redirect('','Eroare intalnita in timpul autentificarii','error');
      }
      if(!isset($fb_user['verified']) || !$fb_user['verified']){
        $this->redirect('','Contul dumneavoastra de facebook nu este verificat','error');
      }
      if(!isset($fb_user['email']) || !strlen(trim($fb_user['email']))){
        $this->redirect('','Email-ul este obligatoriu','error');
      }
      // check for existing customer
      $this->load->model('Account_model');
      $email = trim($fb_user['email']);
      $filters = array(
        'username' => $email
      );
      $user = $this->Account_model->getAccount($filters);
      if($user){
        if($user->is_blocked()){
          $this->redirect('','Contul dvs. este blocat','error');
        }
        if($user->type !== 'customer'){
          $this->redirect('','Social login is restricted for your account','error');
        }
        $allowed_social_networks = $user->getSocialLoginNetworks();
        if(!in_array('fb', $allowed_social_networks)){
          $this->redirect('','Facebook social login is restricted for your account','error');
        }
        $data = array();
        $data['user_id'] = $user->id;
        $data['facebook_id'] = $fb_user['id'];
        $this->Account_model->saveAccount($data);
        
        $this->session->set_userdata('logged_in', $user->id);
        $this->redirect('','Autentificare efecutata cu succes','success');
      } else {
        if(!isset($fb_user['last_name']) || !strlen(trim($fb_user['last_name']))){
          $this->redirect('','Numele de familie este obligatoriu');
        }
        $data = array();
        $data['social_login'] = 'fb';
        $data['facebook_id'] = $fb_user['id'];
        $data['user_firstname'] = isset($fb_user['first_name']) ? trim($fb_user['first_name']) . (isset($fb_user['middle_name']) ? ' - ' . trim($fb_user['middle_name']) : '') : null;
        $data['user_lastname'] = $fb_user['last_name'];
        $data['user_email'] = $email;
        $data['user_username'] = $email;
        if(isset($fb_user['gender'])){
          $gender = $fb_user['gender'] == 'female' ? 'f' : ($fb_user['gender'] == 'male' ? 'm' : null);
          if($gender){
            $data['gender'] = $gender;
            $data['title'] = $gender == 'm' ? 'mr' : 'mrs';
          }
        }
        $data['user_type'] = 'customer';
        $data['user_status'] = 1;
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
        $this->load->helper('string');
        $password = random_string('alnum',8);
        $data['user_password'] = sha1($password);
        
        $user_id = $this->Account_model->saveAccount($data);
        
        Modules :: run ('Mailer/account_register', array('user_id'=>$user_id, 'password' => $password));
        
        $this->session->set_userdata('logged_in', $user_id);
        $this->redirect('account/profile','Contul a fost creat si autentificarea a fost efectuata cu succes.','success');
      }
    }
    $this->redirect($prevurl,'Deja sunteti autentificat','warning');
  }
}