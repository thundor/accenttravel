<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Forms extends MX_Controller {
  public function submit() {
    // $this->outputError('Perioada de inscriere a expirat!');
    if(date('Y-m-d H:i:s') >= '2019-07-18 20:59:59'){
      $this->outputError('Perioada de inscriere a expirat!');
    }
    
    $this->load->library('form_validation');
    $should_validate = $this->validate();
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
    if ($this->input->is_ajax_request()) {
      if($valid){
        $this->addMessage('Formularul este valid','succes');
        $this->output();
      } else {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
    }
    if(!$valid){
      $this->redirect('inregistrare-concurs-vacanta', $this->form_validation->error_string(), 'error');
    }
    
    $data = array();
    $data['numeReg'] = trim($this->input->post('numeReg'));
    $data['pnumeReg'] = trim($this->input->post('pnumeReg'));
    $data['emailReg'] = trim($this->input->post('emailReg'));
    $data['domiciliuReg'] = trim($this->input->post('domiciliuReg'));
    $data['telReg'] = trim($this->input->post('telReg'));
    $data['user_id'] = $this->user->id;
    $data['time_created'] = date('Y-m-d H:i:s');
    
    $this->db->insert('ac_concurs', $data);
    $this->data = $data;
    $this->data['id'] = $this->db->insert_id();
    $this->data['date'] = $data['time_created'];
    $this->theme->set_sublayout('email/concurs/index');
    $this->theme->view('email/forms/concurs/admin', $this->data, $this);

    Modules :: run ('Mailer/send_email', array(
      'subject'=>'[Concurs] ' . $data['numeReg'] . ' ' . $data['pnumeReg'],
      'to'=>array(
        'office@accenttravel.ro',
        // 'tudor.chirvasa@lisal.ro',
       ),
      'bcc'=>array(
        // 'alexandra.oprea@lisal.ro',
      ),
      'from_email'=>'vanzari@accenttravel.ro',
      'from_name'=>'Accent Travel & Events',
    ));
    $this->theme->view('email/forms/concurs/client', $this->data, $this);
    Modules :: run ('Mailer/send_email', array(
      'subject'=>'Concurs - Confirmare participare',
      'to'=>$data['emailReg'],
      // 'bcc'=>array(
        // 'alexandra.oprea@lisal.ro',
      // ),
      'from_email'=>'vanzari@accenttravel.ro',
      'from_name'=>'Accent Travel & Events',
    ));
    $this->session->set_userdata('completat_formular_inregistrare', true);
    $this->redirect('','Va multumim pentru inregistrare si va uram succes!', 'success');
  }
  /* public function submit() {
    $this->outputError('Perioada de inscriere a expirat!');
    $this->load->library('form_validation');
    $should_validate = $this->validate();
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
    if ($this->input->is_ajax_request()) {
      if($valid){
        $this->addMessage('Formularul este valid','succes');
        $this->output();
      } else {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
    }
    if(!$valid){
      $this->redirect('inregistrare-concurs', $this->form_validation->error_string(), 'error');
    }
    
    $data = array();
    $data['numeReg'] = trim($this->input->post('numeReg'));
    $data['pnumeReg'] = trim($this->input->post('pnumeReg'));
    $data['emailReg'] = trim($this->input->post('emailReg'));
    $data['domiciliuReg'] = trim($this->input->post('domiciliuReg'));
    $data['telReg'] = trim($this->input->post('telReg'));
    $data['user_id'] = $this->user->id;
    $data['time_created'] = date('Y-m-d H:i:s');
    
    $this->db->insert('ac_concurs', $data);
    $this->data = $data;
    $this->data['id'] = $this->db->insert_id();
    $this->data['date'] = $data['time_created'];
    $this->theme->set_sublayout('email/default/index');
    $this->theme->view('email/forms/concurs/admin', $this->data, $this);

    Modules :: run ('Mailer/send_email', array(
      'subject'=>'[Concurs-SKI] ' . $data['numeReg'] . ' ' . $data['pnumeReg'],
      'to'=>array(
        'office@accenttravel.ro',
        // 'tudor.chirvasa@lisal.ro',
       ),
      'bcc'=>array(
        'alexandra.oprea@lisal.ro',
      ),
      'from_email'=>'vanzari@accenttravel.ro',
      'from_name'=>'Accent Travel & Events',
    ));
    $this->theme->view('email/forms/concurs/client', $this->data, $this);
    Modules :: run ('Mailer/send_email', array(
      'subject'=>'Concurs SKI - Confirmare participare',
      'to'=>$data['emailReg'],
      // 'bcc'=>array(
        // 'alexandra.oprea@lisal.ro',
      // ),
      'from_email'=>'vanzari@accenttravel.ro',
      'from_name'=>'Accent Travel & Events',
    ));
    $this->session->set_userdata('completat_formular_inregistrare', true);
    $this->redirect('','Va multumim pentru inregistrare si va uram succes!', 'success');
  } */
  protected function validate() {
    $this->form_validation->set_rules('numeReg', 'Nume', 'trim|required|min_length[3]|max_length[255]', array(
      'required'=>'Campul este obligatoriu',
      'min_length'=>'Minim 3 litere',
    ));
    $this->form_validation->set_rules('pnumeReg', 'Prenume', 'trim|required|max_length[255]', array(
      'required'=>'Campul este obligatoriu',
    ));
    $this->form_validation->set_rules('emailReg', 'Adresa de Email', 'trim|required|max_length[255]|valid_email|min_length[5]|is_unique[ac_concurs.emailReg]', array(
      'required'=>'Campul este obligatoriu',
      'min_length'=>'Minim 5 caractere',
      'valid_email'=>'Adresa de email invalida',
      'is_unique'=>'Acest email a fost deja inregistrat',
    ));
    $this->form_validation->set_rules('domiciliuReg', 'Oras Domiciliu', 'trim|required|min_length[3]|max_length[100]', array(
      'required'=>'Campul este obligatoriu',
      'min_length'=>'Minim 3 litere',
      'max_length'=>'Maxim 100 litere',
    ));
    $this->form_validation->set_rules('telReg', 'Telefon', 'trim|required|min_length[4]|max_length[100]', array(
      'required'=>'Campul este obligatoriu',
      'min_length'=>'Minim 4 caractere',
      'max_length'=>'Maxim 100 caractere',
    ));
    $this->form_validation->set_rules('acordReg', 'Regulament concurs', 'required', array(
      'required'=>'Trebuie sa fiti de acord cu Regulamentul Concursului',
    ));
    return true;
  }
  public function newsletter() {
    $status = $this->input->post('status');
    if(!isset($status)){
      $status = 1;
    }
    if(!$status){
      return $this->unsubscribe();
    }
    $this->load->library('form_validation');
    $this->form_validation->set_rules('email', 'Adresa de Email', 'trim|required|max_length[255]|valid_email|min_length[5]', array(
      'required'=>'Campul Adresa de Email este obligatoriu',
      'min_length'=>'Minim 5 caractere',
      'valid_email'=>'Adresa de email invalida',
    ));
    $should_validate = true;
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
    if ($this->input->is_ajax_request()) {
      if(!$valid){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
    }
    if(!$valid){
      $this->redirect('', $this->form_validation->error_string(), 'error');
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
      if($existing_user){
        $this->addMessage('Este necesar sa va conectati cu acest utilizator pentru a va abona.', 'error');
        if ($this->input->is_ajax_request()) {
          $this->output('error');
        }
        $this->redirect('');
      }
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
        if($response_decoded->count && ($response_decoded->subscriber->subscribe_status=='no')){
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
    
    $this->addMessage('Abonare efectuata cu succes. Va multumim!','succes');
    if ($this->input->is_ajax_request()) {
      $this->output();
    }
    
    $this->redirect('');
  }
  public function unsubscribe() {
    $status = $this->input->post('status');
    if(!isset($status)){
      $status = 0;
    }
    if($status){
      return $this->newsletter();
    }
    $this->load->library('form_validation');
    $email = $this->input->post('email');
    if(!isset($email)){
      $email = $this->input->get('email');
      $_POST['email'] = $email;
    }
    $this->form_validation->set_rules('email', 'Adresa de Email', 'trim|required|max_length[255]|valid_email|min_length[5]', array(
      'required'=>'Campul Adresa de Email este obligatoriu',
      'min_length'=>'Minim 5 caractere',
      'valid_email'=>'Adresa de email invalida',
    ));
    $should_validate = true;
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
    if ($this->input->is_ajax_request()) {
      if(!$valid){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
    }
    if(!$valid){
      $this->redirect('', $this->form_validation->error_string(), 'error');
    }
    $email = trim($this->input->post('email'));
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
      if($existing_user){
        $this->addMessage('Este necesar sa va conectati cu acest utilizator pentru a va dezabona.', 'error');
        if ($this->input->is_ajax_request()) {
          $this->output('error');
        }
        $this->redirect('');
      }
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
    
    $this->addMessage('Dezabonare efectuata cu succes.','succes');
    if ($this->input->is_ajax_request()) {
      $this->output();
    }
    
    $this->redirect('');
  }
}