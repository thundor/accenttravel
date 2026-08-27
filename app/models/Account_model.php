<?php

class Account_model extends CI_Model {
  function getAccountById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getAccount($filters);
  }
  function getAccountLogin($username, $password, $filters=array()) {
    $filters['username'] = $username;
    $filters['password'] = $password;
    return $this->getAccount($filters);
  }
  function getAccountByUsername($username, $filters=array()) {
    $filters['username'] = $username;
    return $this->getAccount($filters);
  }
  function getAccountsByEmail($email, $filters=array()) {
    $filters['email'] = $email;
    return $this->getAccounts($filters);
  }
  function getAccountsByHash($hash, $filters=array()) {
    $filters['token'] = $hash;
    return $this->getAccounts($filters);
  }
  function getAccountByHash($hash, $filters=array()) {
    $accounts = $this->getAccountsByHash($hash, $filters);
	if(count($accounts) != 1) return false;
	$user = array_shift($accounts);
	$date = date_create($user->token_expiry);
	$d = new DateTime();
	if($date->format('Y-m-d H:i:s') <= $d->format('Y-m-d H:i:s')){
		return false;
	}
	return $user;
  }
  function getAccount($filters=array()) {
    $accounts = $this->getAccounts($filters);
    if($accounts){
      return $accounts[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('user_status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      // $this->db->group_start();
      $this->db->like('CONCAT_WS(" ",`user_lastname`, `user_firstname`, `user_email`,`user_username`,`phone`)',$search);
      // $this->db->group_end();
    }
    if(isset($filters['email'])){
      $emails = (array)$filters['email'];
      if(!empty($emails)){
        $this->db->where_in('user_email', $emails);
      }
    }
    if(isset($filters['token'])){
      $tokens = (array)$filters['token'];
      if(!empty($tokens)){
        $this->db->where_in('token', $tokens);
      }
    }
    if(isset($filters['username'])){
      $usernames = (array)$filters['username'];
      if(!empty($usernames)){
        $this->db->where_in('user_username', $usernames);
      }
    }
    if(isset($filters['password'])){
      $this->db->where('user_password', sha1($filters['password']));
    }
    if(isset($filters['type'])){
      $types = (array)$filters['type'];
      if(!empty($types)){
        $this->db->where_in('user_type', $types);
      }
    }
    if(isset($filters['type'])){
      $types = (array)$filters['type'];
      if(!empty($types)){
        $this->db->where_in('user_type', $types);
      }
    }
    if(isset($filters['created_by'])){
      $created_by = (array)$filters['created_by'];
      if(!empty($created_by)){
        $this->db->where_in('user_created_by', $created_by);
      }
    }
    $roles = false;
    if(isset($filters['role'])){
      $roles = (array)$filters['role'];
    }
    $own_roles = false;
    $own_created_bys = false;
    if(isset($filters['own_role']) && isset($filters['own_created_by'])){
      $own_roles = (array)$filters['own_role'];
      $own_created_bys = (array)$filters['own_created_by'];
    }
    
    $has_own_roles = !empty($own_roles) && !empty($own_created_bys);
    $has_roles = !empty($roles);
    if($has_roles && $has_own_roles){
      $this->db->group_start();
    }
    if($has_roles){
      $this->db->where_in('user_role', $roles);
    }
    if($has_own_roles){
      if($has_roles){
        $this->db->or_group_start();
      }
      $this->db->where_in('user_role', $own_roles);
      $this->db->where_in('user_created_by', $own_created_bys);
      if($has_roles){
        $this->db->group_end();
      }
    }
    if($has_roles && $has_own_roles){
      $this->db->group_end();
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('user_id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('user_id', $except_ids);
      }
    }
  }
  function getAccounts($filters = array()) {
    /* $fields = array(
      'user_id',
      'user_role',
      'user_username',
      'user_firstname',
      'user_lastname',
      'user_email',
      'user_password',
      'user_status',
      'user_type',
      'user_created_by',
      'user_created_datetime',
      'user_modified_by',
      'social_login',
      'facebook_id',
      'user_modified_datetime',
      'title',
      'gender',
      'birth_date',
      'phone_prefix',
      'phone',
      'country',
      'city',
      'contact_firstname',
      'contact_phone_prefix',
      'contact_lastname',
      'flight_departure_airport',
      'flight_prefered_spot',
      'passport_country',
      'pf_firstname',
      'pf_lastname',
      'pf_country',
      'pf_city',
      'pf_street',
      'pf_street_no',
      'pf_phone_prefix',
      'pf_phone',
      'pf_email',
      'pf_address',
      'pf_postal_code',
      'pj_firstname',
      'pj_lastname',
      'pj_company_name',
      'pj_cui',
      'pj_iban',
      'pj_country',
      'pj_city',
      'pj_street',
      'pj_street_no',
      'pj_phone_prefix',
      'pj_phone',
      'pj_email',
      'pj_address',
      'pj_postal_code',
      'newsletter',
      'fellows',
      'invoice',
      'passport_number',
      'flight_special_assistance',
      'contact_phone'
    ); */
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('*');
    }
    $this->applyFilters($filters);
    
    if(isset($filters['ordering']) && $filters['ordering']){
      list($sort_by,$sort_order) = explode(' ',$filters['ordering']);
      $sort_order = strtolower($sort_order);
      $sort_by = strtolower($sort_by);
      if(!in_array($sort_order,array(
        'asc',
        'desc'
      ))){
        $sort_order = false;
      }
      if($sort_order && $sort_by){
        $this->db->order_by($sort_by, $sort_order);
      }
    }
    
    $page = isset($filters['page']) && (int)$filters['page'] > 1 ? (int)$filters['page']: 1;
    $limit = isset($filters['limit']) && (int)$filters['limit'] > 0 ? (int)$filters['limit']: null;
    $offset = 0;
    if($limit > 0){
      $offset = ($page - 1) * $limit;
    }

    $q = $this->db->get('ac_user', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    } else {
      $this->load->library('User');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $q->row('User');
      }
      return $q->result('User');
    }
  }
  function getTotalAccounts($filters = array()) {
    $this->db->select('COUNT(user_id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_user');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveAccount($data) {
    if(isset($data['user_id']) && $data['user_id']){
      $this->db->where('user_id', $data['user_id']);
      $this->db->update('ac_user', $data);
      $user_id = $data['user_id'];
    } else {
      // la crearea unui cont se verifica daca este abonat la newsletter, si se actualizeaza campul de profil
      if(!isset($data['newsletter']) && isset($data['user_email']) && strlen(trim($data['user_email']))){
        $this->db->select('id,status');
        $this->db->where('email', trim($data['user_email']));
        $q = $this->db->get('ac_newsletter', 1, 0);
        $newsletter_item = $q->row();
        if($newsletter_item){
          $data['newsletter'] = $newsletter_item->status ? 1 : 0;
        }
      }
      $data['user_id'] = null;
      $this->db->insert('ac_user', $data);
      $user_id = $this->db->insert_id();
    }
    $this->saveNewsletterSetting($data);
    
    return $user_id;
  }
  function saveNewsletterSetting($data) {
    if(!isset($data['newsletter'])){
      return;
    }
    $newsletter_status = !empty($data['newsletter']) ? 1 : 0;
    $newsletter_data = array();
    $this->load->model('WhiteImage_model');
    if(isset($data['user_id']) && $data['user_id']){
      $newsletter_data['user_id'] = $data['user_id'];
      if(!isset($data['user_email']) || (trim($data['user_email']) === '')){
        $this->db->select('user_email');
        $this->db->where('user_id', $data['user_id']);
        $q = $this->db->get('ac_user', 1, 0);
        $user_item = $q->row();
        if(!$user_item){
          return;
        }
        $data['user_email'] = $user_item->user_email;
      }
      // daca si-a schimbat email-ul, dezabonam intrarea din newsletter asociata id-ului utilizatorului
      $this->db->where('user_id', $data['user_id']);
      $this->db->where('`email` !=', $data['user_email']);
      $this->db->where('status', 1);
      $q = $this->db->get('ac_newsletter');
      if($q->num_rows() ){
        foreach($q->result() as $item){
          $search = array(
            'email|' . $item->email . '|1'
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
          Modules :: run ('Mailer/newsletter_unsubscribe', array('email'=>$item->email));
        }
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('email !=', $data['user_email']);
        $this->db->update('ac_newsletter', array('status'=>0));
      }
    }
    if(!isset($data['user_email']) || (trim($data['user_email']) === '')){
      return;
    }
    $newsletter_data['email'] = trim($data['user_email']);
    $newsletter_data['status'] = $newsletter_status;
    $newsletter_data['time_created'] = date('Y-m-d H:i:s');
    
    $sql = $this->db->insert_string('ac_newsletter', $newsletter_data) . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `user_id` = VALUES(`user_id`)";
    $this->db->query($sql);
    
    $search = array(
      'email|' . $newsletter_data['email'] . '|1'
    );
    $return_fields = 'all';
    $response = $this->WhiteImage_model->select_one($search,$return_fields);
    if($response){
      $response_decoded = json_decode($response);
      if($response_decoded){
        if(!empty($response_decoded->count)){
          if($newsletter_status && ($response_decoded->subscriber->subscribe_status == 'no')){
            $emailid = $response_decoded->subscriber->emailid;
            $this->WhiteImage_model->resubscribe($emailid);
          } elseif(!$newsletter_status && ($response_decoded->subscriber->subscribe_status != 'no')){
            $emailid = $response_decoded->subscriber->emailid;
            $this->WhiteImage_model->unsubscribe($emailid);
          } 
        } else {
          $data = array();
          $data['email'] = $newsletter_data['email'];
          $data['sursa'] = 'AccentTravel&Events';
          $response = $this->WhiteImage_model->save($data);
        }
      }
    }
    if($newsletter_status){
      Modules :: run ('Mailer/newsletter_subscribe', array('to'=>$newsletter_data['email']));
    } else {
      Modules :: run ('Mailer/newsletter_unsubscribe', array('to'=>$newsletter_data['email']));
    }
    return;
  }
  function deleteAccountById($user_id, $filters = array()) {
    $filters['id'] = $user_id;
    $this->deleteAccount($filters);
  }
  function deleteAccount($filters = array()) {
    $this->applyFilters($filters);
    $this->db->delete('ac_user');
  }
  function applyGeneralFormSaveAdaptation(&$ci, &$user, &$data) {
    if(isset($data['birth_date'])){
      if(strlen($data['birth_date'])){
        $date = DateTime::createFromFormat('d.m.Y', $data['birth_date']);
        $data['birth_date'] = $date ? $date->format('Y-m-d') : null;
      } else {
        $data['birth_date'] = null;
      }
    }
    if(isset($data['fellows'])){
      foreach($data['fellows'] as &$fellow){
        if(isset($fellow->birth_date) && strlen($fellow->birth_date)){
          $date = DateTime::createFromFormat('d.m.Y', $fellow->birth_date);
          $fellow->birth_date = $date ? $date->format('Y-m-d') : null;
        } else {
          $fellow->birth_date = null;
        }
      }
      $data['fellows'] = empty($data['fellows']) ? null : serialize($data['fellows']);
    }
    if(isset($data['flight_departure_airport'])){
      $data['flight_departure_airport'] = empty($data['flight_departure_airport']) ? null : serialize((object)$data['flight_departure_airport']);
    }
    if(isset($data['flight_special_assistance'])){
      $data['flight_special_assistance'] = empty($data['flight_special_assistance']) ? null : implode(',',$data['flight_special_assistance']);
    }
    if(isset($data['social_login'])){
      $data['social_login'] = empty($data['social_login']) ? null : implode(',',$data['social_login']);
    }
  }
  function applyGeneralFormValidation(&$ci, &$user, &$data, &$should_validate) {
    $title = $ci->input->post('title');
    if(isset($title)){
      $title = trim($title);
      $should_validate = true;
      $ci->form_validation->set_rules('title', 'Titlu', 'trim|in_list[mr,mrs,ms]',array(
        'in_list' => 'Titlu invalid',
      ));
      $data['title'] = $title;
      
      if(in_array($data['title'],array('mr'))){
        $data['gender'] = 'm';
      } elseif(in_array($data['title'],array('mrs','ms'))){
        $data['gender'] = 'f';
      }
    }
    $firstname = $ci->input->post('firstname');
    if(isset($firstname)){
      $should_validate = true;
      $ci->form_validation->set_rules('firstname', 'Prenume', 'trim|max_length[255]');
      $data['user_firstname'] = trim($firstname);
    }
    $lastname = $ci->input->post('lastname');
    if(isset($lastname)){
      $should_validate = true;
      $ci->form_validation->set_rules('lastname', 'Nume familie', 'trim|required|max_length[255]');
      $data['user_lastname'] = trim($lastname);
    }
    $gender = $ci->input->post('gender');
    if(isset($gender)){
      $gender=trim($gender);
      if(strlen($gender)){
        $should_validate = true;
        $ci->form_validation->set_rules('gender', 'Gen', 'trim|in_list[m,f]',array(
          'in_list' => 'Alegere invalida',
        ));
      }
      $data['gender'] = $gender;
    }
    $birth_date = $ci->input->post('birth_date');
    if(isset($birth_date)){
      $should_validate = true;
      $ci->form_validation->set_rules('birth_date', 'Data nastere', 'trim|valid_date[d.m.Y]',array(
        'valid_date' => 'Formatul datei este invalid',
      ));
      $data['birth_date'] = trim($birth_date);
    }
    $country = $ci->input->post('country');
    if(isset($country)){
      $country=trim($country);
      if(strlen($country)){
        $should_validate = true;
        $ci->form_validation->set_rules('country', 'Nationalitate', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['country'] = $country;
    }
    $city = $ci->input->post('city');
    if(isset($city)){
      $city=trim($city);
      if(strlen($city)){
        $should_validate = true;
        $ci->form_validation->set_rules('city', 'Oras', 'trim|max_length[255]');
      }
      $data['city'] = $city;
    }
    $phone_prefix = $ci->input->post('phone_prefix');
    if(isset($phone_prefix)){
      $phone_prefix=trim($phone_prefix);
      if(strlen($phone_prefix)){
        $should_validate = true;
        $ci->form_validation->set_rules('phone_prefix', 'Prefix telefon', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['phone_prefix'] = $phone_prefix;
    }
    $phone = $ci->input->post('phone');
    if(isset($phone)){
      $phone=trim($phone);
      if(strlen($phone)){
        $should_validate = true;
        $ci->form_validation->set_rules('phone', 'Telefon', 'trim|max_length[100]');
      }
      $data['phone'] = $phone;
    }
    $contact_firstname = $ci->input->post('contact_firstname');
    if(isset($contact_firstname)){
      $contact_firstname=trim($contact_firstname);
      if(strlen($contact_firstname)){
        $should_validate = true;
        $ci->form_validation->set_rules('contact_firstname', 'Prenume', 'trim|max_length[255]');
      }
      $data['contact_firstname'] = $contact_firstname;
    }
    $contact_lastname = $ci->input->post('contact_lastname');
    if(isset($contact_lastname)){
      $contact_lastname=trim($contact_lastname);
      if(strlen($contact_lastname)){
        $should_validate = true;
        $ci->form_validation->set_rules('contact_lastname', 'Nume familie', 'trim|max_length[255]');
      }
      $data['contact_lastname'] = $contact_lastname;
    }
    $contact_phone_prefix = $ci->input->post('contact_phone_prefix');
    if(isset($contact_phone_prefix)){
      $contact_phone_prefix=trim($contact_phone_prefix);
      if(strlen($contact_phone_prefix)){
        $should_validate = true;
        $ci->form_validation->set_rules('contact_phone_prefix', 'Prefix telefon', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['contact_phone_prefix'] = $contact_phone_prefix;
    }
    $contact_phone = $ci->input->post('contact_phone');
    if(isset($contact_phone)){
      $contact_phone=trim($contact_phone);
      if(strlen($contact_phone)){
        $should_validate = true;
        $ci->form_validation->set_rules('contact_phone', 'Telefon', 'trim|max_length[100]');
      }
      $data['contact_phone'] = $contact_phone;
    }
    
    
    $passport_country = $ci->input->post('passport_country');
    if(isset($passport_country)){
      $passport_country=trim($passport_country);
      if(strlen($passport_country)){
        $should_validate = true;
        $ci->form_validation->set_rules('passport_country', 'Tara', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida'
        ));
      }
      $data['passport_country'] = $passport_country;
    }
    $passport_number = $ci->input->post('passport_number');
    if(isset($passport_number)){
      $passport_number=trim($passport_number);
      if(strlen($passport_number)){
        $should_validate = true;
        $ci->form_validation->set_rules('passport_number', 'Numar pasaport', 'trim|max_length[255]');
      }
      $data['passport_number'] = $passport_number;
    }
    
    
    $newsletter = $ci->input->post('newsletter');
    if(isset($newsletter)){
      $newsletter=(int)$newsletter;
      $data['newsletter'] = $newsletter ? 1 : 0;
    }
    
    $pf_firstname = $ci->input->post('pf_firstname');
    if(isset($pf_firstname)){
      $pf_firstname=trim($pf_firstname);
      if(strlen($pf_firstname)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_firstname', 'Prenume', 'trim|max_length[255]');
      }
      $data['pf_firstname'] = $pf_firstname;
    }
    $pf_lastname = $ci->input->post('pf_lastname');
    if(isset($pf_lastname)){
      $pf_lastname=trim($pf_lastname);
      if(strlen($pf_lastname)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_lastname', 'Nume familie', 'trim|max_length[255]');
      }
      $data['pf_lastname'] = $pf_lastname;
    }
    $pf_country = $ci->input->post('pf_country');
    if(isset($pf_country)){
      $pf_country=trim($pf_country);
      if(strlen($pf_country)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_country', 'Tara', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['pf_country'] = $pf_country;
    }
    $pf_city = $ci->input->post('pf_city');
    if(isset($pf_city)){
      $pf_city=trim($pf_city);
      if(strlen($pf_city)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_city', 'Oras', 'trim|max_length[255]');
      }
      $data['pf_city'] = $pf_city;
    }
    $pf_street = $ci->input->post('pf_street');
    if(isset($pf_street)){
      $pf_street=trim($pf_street);
      if(strlen($pf_street)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_street', 'Strada', 'trim|max_length[255]');
      }
      $data['pf_street'] = $pf_street;
    }
    $pf_street_no = $ci->input->post('pf_street_no');
    if(isset($pf_street_no)){
      $pf_street_no=trim($pf_street_no);
      if(strlen($pf_street_no)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_street_no', 'Numar strada', 'trim|max_length[20]');
      }
      $data['pf_street_no'] = $pf_street_no;
    }
    $pf_phone_prefix = $ci->input->post('pf_phone_prefix');
    if(isset($pf_phone_prefix)){
      $pf_phone_prefix=trim($pf_phone_prefix);
      if(strlen($pf_phone_prefix)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_phone_prefix', 'Prefix telefon', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['pf_phone_prefix'] = $pf_phone_prefix;
    }
    $pf_phone = $ci->input->post('pf_phone');
    if(isset($pf_phone)){
      $pf_phone=trim($pf_phone);
      if(strlen($pf_phone)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_phone', 'Telefon', 'trim|max_length[100]');
      }
      $data['pf_phone'] = $pf_phone;
    }
    $pf_email = $ci->input->post('pf_email');
    if(isset($pf_email)){
      $pf_email=trim($pf_email);
      if(strlen($pf_email)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_email', 'Email', 'trim|max_length[255]|valid_email');
      }
      $data['pf_email'] = $pf_email;
    }
    $pf_address = $ci->input->post('pf_address');
    if(isset($pf_address)){
      $pf_address=trim($pf_address);
      if(strlen($pf_address)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_address', 'Adresa facturare', 'trim|max_length[255]');
      }
      $data['pf_address'] = $pf_address;
    }
    $pf_postal_code = $ci->input->post('pf_postal_code');
    if(isset($pf_postal_code)){
      $pf_postal_code=trim($pf_postal_code);
      if(strlen($pf_postal_code)){
        $should_validate = true;
        $ci->form_validation->set_rules('pf_postal_code', 'Cod postal', 'trim|max_length[50]');
      }
      $data['pf_postal_code'] = $pf_postal_code;
    }
    
    $pj_firstname = $ci->input->post('pj_firstname');
    if(isset($pj_firstname)){
      $pj_firstname=trim($pj_firstname);
      if(strlen($pj_firstname)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_firstname', 'Prenume', 'trim|max_length[255]');
      }
      $data['pj_firstname'] = $pj_firstname;
    }
    $pj_lastname = $ci->input->post('pj_lastname');
    if(isset($pj_lastname)){
      $pj_lastname=trim($pj_lastname);
      if(strlen($pj_lastname)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_lastname', 'Nume familie', 'trim|max_length[255]');
      }
      $data['pj_lastname'] = $pj_lastname;
    }
    $pj_company_name = $ci->input->post('pj_company_name');
    if(isset($pj_company_name)){
      $pj_company_name=trim($pj_company_name);
      if(strlen($pj_company_name)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_company_name', 'Nume companie', 'trim|max_length[255]');
      }
      $data['pj_company_name'] = $pj_company_name;
    }
    $pj_cui = $ci->input->post('pj_cui');
    if(isset($pj_cui)){
      $pj_cui=trim($pj_cui);
      if(strlen($pj_cui)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_cui', 'CUI', 'trim|max_length[50]|validate_CIF_or_CUI',array(
          'validate_CIF_or_CUI' => 'Codul CUI introdus este invalid',
        ));
      }
      $data['pj_cui'] = $pj_cui;
    }
    $pj_bank = $ci->input->post('pj_bank');
    if(isset($pj_bank)){
      $pj_bank=trim($pj_bank);
      if(strlen($pj_bank)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_bank', 'IBAN', 'trim|max_length[255]');
      }
      $data['pj_bank'] = $pj_bank;
    }
    $pj_iban = $ci->input->post('pj_iban');
    if(isset($pj_iban)){
      $pj_iban=trim($pj_iban);
      if(strlen($pj_iban)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_iban', 'IBAN', 'trim|max_length[50]|valid_iban',array(
          'valid_iban' => 'Codul IBAN introdus este invalid'
        ));
      }
      $data['pj_iban'] = $pj_iban;
    }
    $pj_regcom = $ci->input->post('pj_regcom');
    if(isset($pj_regcom)){
      $pj_regcom=trim($pj_regcom);
      if(strlen($pj_regcom)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_regcom', 'IBAN', 'trim|max_length[255]');
      }
      $data['pj_regcom'] = $pj_regcom;
    }
    $pj_country = $ci->input->post('pj_country');
    if(isset($pj_country)){
      $pj_country=trim($pj_country);
      if(strlen($pj_country)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_country', 'Tara', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['pj_country'] = $pj_country;
    }
    $pj_city = $ci->input->post('pj_city');
    if(isset($pj_city)){
      $pj_city=trim($pj_city);
      if(strlen($pj_city)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_city', 'Oras', 'trim|max_length[255]');
      }
      $data['pj_city'] = $pj_city;
    }
    $pj_street = $ci->input->post('pj_street');
    if(isset($pj_street)){
      $pj_street=trim($pj_street);
      if(strlen($pj_street)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_street', 'Strada', 'trim|max_length[255]');
      }
      $data['pj_street'] = $pj_street;
    }
    $pj_street_no = $ci->input->post('pj_street_no');
    if(isset($pj_street_no)){
      $pj_street_no=trim($pj_street_no);
      if(strlen($pj_street_no)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_street_no', 'Numar strada', 'trim|max_length[20]');
      }
      $data['pj_street_no'] = $pj_street_no;
    }
    $pj_phone_prefix = $ci->input->post('pj_phone_prefix');
    if(isset($pj_phone_prefix)){
      $pj_phone_prefix=trim($pj_phone_prefix);
      if(strlen($pj_phone_prefix)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_phone_prefix', 'Prefix telefon', 'trim|valid_country[iso_2]',array(
          'valid_country' => 'Tara invalida',
        ));
      }
      $data['pj_phone_prefix'] = $pj_phone_prefix;
    }
    $pj_phone = $ci->input->post('pj_phone');
    if(isset($pj_phone)){
      $pj_phone=trim($pj_phone);
      if(strlen($pj_phone)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_phone', 'Telefon', 'trim|max_length[100]');
      }
      $data['pj_phone'] = $pj_phone;
    }
    $pj_email = $ci->input->post('pj_email');
    if(isset($pj_email)){
      $pj_email=trim($pj_email);
      if(strlen($pj_email)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_email', 'Email', 'trim|max_length[255]|valid_email');
      }
      $data['pj_email'] = $pj_email;
    }
    $pj_address = $ci->input->post('pj_address');
    if(isset($pj_address)){
      $pj_address=trim($pj_address);
      if(strlen($pj_address)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_address', 'Adresa facturare', 'trim|max_length[255]');
      }
      $data['pj_address'] = $pj_address;
    }
    $pj_postal_code = $ci->input->post('pj_postal_code');
    if(isset($pj_postal_code)){
      $pj_postal_code=trim($pj_postal_code);
      if(strlen($pj_postal_code)){
        $should_validate = true;
        $ci->form_validation->set_rules('pj_postal_code', 'Cod postal', 'trim|max_length[50]');
      }
      $data['pj_postal_code'] = $pj_postal_code;
    }
    
    $invoice = $ci->input->post('invoice');
    if(isset($invoice)){
      $invoice=trim($invoice);
      if(strlen($invoice)){
        $should_validate = true;
        $ci->form_validation->set_rules('invoice', 'Facturare implicita', 'trim|in_list[pf,pj]',array(
          'in_list' => 'Alegere invalida'
        ));
      }
      $data['invoice'] = $invoice;
    }
    $fellows = $ci->input->post('fellows');
    if(isset($fellows)){
      $data_fellows = array();
      if(is_array($fellows)){
        $fellow_fields = array(
          'title',
          'firstname',
          'lastname',
          'birth_date',
          'country',
          'passport_number',
        );
        foreach($fellow_fields as $fellow_field){
          if(!isset($fellows[$fellow_field])){
            $ci->outputError('Invalid fellows data');
          }
          $expected_fellow_index = 0;
          foreach($fellows[$fellow_field] as $fellow_index =>$fellow_field_value){
            if($fellow_index !== $expected_fellow_index){
              $ci->outputError('Invalid fellows data - expected index fail');
            }
            $expected_fellow_index ++;
            if(!isset($data_fellows[$fellow_index])){
              $data_fellows[$fellow_index] = new stdClass;
            }
            
            $fake_post_index = 'fellows_' . $fellow_field . '_' . $fellow_index;
            $_POST[$fake_post_index] = $fellow_field_value;
            if($fellow_field == 'title'){
              $field_save_value = trim($fellow_field_value);
              if(strlen($field_save_value)){
                $should_validate = true;
                $ci->form_validation->set_rules($fake_post_index, 'Titlu', 'trim|in_list[mr,mrs,ms,chd]',array(
                  'in_list' => 'Titlu invalid pentru insotitorul #' . ($fellow_index+1),
                ));
              }
              $data_fellows[$fellow_index]->$fellow_field = $field_save_value;
            } elseif($fellow_field == 'firstname'){
              $field_save_value = trim($fellow_field_value);
              $should_validate = true;
              $ci->form_validation->set_rules($fake_post_index, 'Prenume', 'trim|max_length[255]',array(
                'max_length' => 'Prenumele introdus depaseste limita admisa pentru insotitorul #' . ($fellow_index+1),
              ));
              $data_fellows[$fellow_index]->$fellow_field = $field_save_value;
            } elseif($fellow_field == 'lastname'){
              $field_save_value = trim($fellow_field_value);
              if(strlen($field_save_value)){
                $should_validate = true;
                $ci->form_validation->set_rules($fake_post_index, 'Nume familie', 'trim|max_length[255]',array(
                  'required' => 'Nume necompletat pentru insotitorul #' . ($fellow_index+1),
                  'max_length' => 'Nume de familie introdus depaseste limita admisa pentru insotitorul #' . ($fellow_index+1),
                ));
              }
              $data_fellows[$fellow_index]->$fellow_field = $field_save_value;
            } elseif($fellow_field == 'birth_date'){
              $field_save_value = trim($fellow_field_value);
              $should_validate = true;
              $ci->form_validation->set_rules($fake_post_index, 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
                'required' => 'Data nastere necompletata pentru insotitorul #' . ($fellow_index+1),
                'valid_date' => 'Formatul datei este invalid pentru insotitorul #' . ($fellow_index+1),
              ));
              $data_fellows[$fellow_index]->$fellow_field = $field_save_value;
            } elseif($fellow_field == 'country'){
              $field_save_value = trim($fellow_field_value);
              if(strlen($field_save_value)){
                $should_validate = true;
                $ci->form_validation->set_rules($fake_post_index, 'Nationalitate', 'trim|valid_country[iso_2]',array(
                  'valid_country' => 'Nationalitate invalida pentru insotitorul #' . ($fellow_index+1),
                ));
              }
              $data_fellows[$fellow_index]->$fellow_field = $field_save_value;
            } elseif($fellow_field == 'passport_number'){
              $field_save_value = trim($fellow_field_value);
              if(strlen($field_save_value)){
                $should_validate = true;
                $ci->form_validation->set_rules($fake_post_index, 'Numar pasaport', 'trim|max_length[255]',array(
                  'max_length' => 'Numarul de pasaport introdus depaseste limita admisa pentru insotitorul #' . ($fellow_index+1),
                ));
              }
              $data_fellows[$fellow_index]->$fellow_field = $field_save_value;
            }
          }
        }
      }
      $data['fellows'] = $data_fellows;
    }
    $flight_departure_airport = $ci->input->post('flight_departure_airport');
    if(isset($flight_departure_airport)){
      $data['flight_departure_airport'] = array();
      if(is_array($flight_departure_airport)){
        if(isset(
          $flight_departure_airport['location_id'],
          $flight_departure_airport['location_code'],
          $flight_departure_airport['location_name'],
          $flight_departure_airport['city_id'],
          $flight_departure_airport['city_code'],
          $flight_departure_airport['city_name'],
          $flight_departure_airport['country_id'],
          $flight_departure_airport['country_name']
        )){
          $location_id = (int)$flight_departure_airport['location_id'];
          $location_code = trim($flight_departure_airport['location_code']);
          $location_name = trim($flight_departure_airport['location_name']);
          $city_id = (int)$flight_departure_airport['city_id'];
          $city_code = trim($flight_departure_airport['city_code']);
          $city_name = trim($flight_departure_airport['city_name']);
          $country_id = (int)$flight_departure_airport['country_id'];
          $country_name = trim($flight_departure_airport['country_name']);
          
          if($location_id >= 0 && $city_id>0 && $country_id>0 && strlen($city_name) && (!$location_id || ($location_id && strlen($location_name)))){
            $data['flight_departure_airport']['location_id'] = $location_id;
            $data['flight_departure_airport']['location_code'] = $location_code;
            $data['flight_departure_airport']['location_name'] = $location_name;
            $data['flight_departure_airport']['city_id'] = $city_id;
            $data['flight_departure_airport']['city_code'] = $city_code;
            $data['flight_departure_airport']['city_name'] = $city_name;
            $data['flight_departure_airport']['country_id'] = $country_id;
            $data['flight_departure_airport']['country_name'] = $country_name;
          }
        }
      }
    }
    $flight_prefered_spot = $ci->input->post('flight_prefered_spot');
    if(isset($flight_prefered_spot)){
      $flight_prefered_spot = trim($flight_prefered_spot);
      if(strlen($flight_prefered_spot)){
        $should_validate = true;
        $ci->form_validation->set_rules('flight_prefered_spot', 'Preferinta loc', 'trim|in_list[window,corridor]',array(
          'in_list' => 'Preferinta invalida',
        ));
      }
      $data['flight_prefered_spot'] = $flight_prefered_spot;
    }
    $flight_special_assistance = $ci->input->post('flight_special_assistance');
    if(isset($flight_special_assistance)){
      $data['flight_special_assistance'] = array();
      if(is_array($flight_special_assistance)){
        $expected_assistance_index = 0;
        $allowed_special_assistances = array(
          'blind',
          'deaf',
          'wheelchair',
          'baby',
          'baggage',
          'sports',
        );
        foreach($flight_special_assistance as $assistance_index =>$assistance_selected){
          if($assistance_index !== $expected_assistance_index){
            $ci->outputError('Invalid flight special assistance data - expected index fail');
          }
          $expected_assistance_index ++;
          if(is_string($assistance_selected) && in_array($assistance_selected,$allowed_special_assistances)){
            $data['flight_special_assistance'][] = $assistance_selected;
          }
        }
      }
    }
    if($user->type === 'customer'){
      $social_login = $ci->input->post('social_login');
      if(isset($social_login)){
        $data['social_login'] = array();
        if(is_array($social_login)){
          $expected_social_index = 0;
          $allowed_social_networks = array(
            'fb',
          );
          foreach($social_login as $social_index =>$network_selected){
            if($social_index !== $expected_social_index){
              $ci->outputError('Invalid social networks data - expected index fail');
            }
            $expected_social_index ++;
            if(is_string($network_selected) && in_array($network_selected,$allowed_social_networks)){
              $data['social_login'][] = $network_selected;
            }
          }
        }
      }
    }
  }
}