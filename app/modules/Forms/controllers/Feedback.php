<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Feedback extends MX_Controller {
  /* public function test() {
	  $data = array();
    $data['lastname'] = trim($this->input->post('lastname'));
    $data['category'] = trim($this->input->post('category'));
    $data['firstname'] = trim($this->input->post('firstname'));
    $data['email'] = trim($this->input->post('email'));
    $data['phone'] = trim($this->input->post('phone'));
    $data['subject'] = trim($this->input->post('subject'));
    $data['body'] = trim($this->input->post('body'));
    $data['user_id'] = $this->user->id;
    $data['time_created'] = date('Y-m-d H:i:s');
    
    $this->db->insert('ac_feedback', array_replace($data, ['accessibility' => json_encode($this->input->post('accessibility'))]));
    $this->data = $data;
	$category_text = [
		'accessibility' => 'Accesibilitate',
		'observation' => 'Observatie',
		'complaint' => 'Reclamatie',
		'suggestion' => 'Sugestie',
		'other' => 'Alt tip',
	];
    $this->data['category_text'] = $category_text[$data['category']] ?? $data['category'];
    $this->data['id'] = $this->db->insert_id();
    $this->data['date'] = $data['time_created'];
	$this->theme->set_theme('accent');
	$this->theme->set_layout('email');
	$this->theme->set_sublayout('email/feedback/index');
	$this->theme->view('email/forms/feedback/admin', $this->data, $this);
	
	 Modules :: run ('Mailer/send_email', array(
      'subject'=>'[Feedback] ' . $data['lastname'] . ' ' . $data['firstname'],
      'to'=>array(
        // 'office@accenttravel.ro',
        'tudor.chirvasa@lisal.ro',
       ),
      'bcc'=>array(
        // 'alexandra.oprea@lisal.ro',
      ),
      'from_email'=>'vanzari@accenttravel.ro',
      'from_name'=>'Accent Travel & Events',
		'prevent_send_email'=>true,
      'output_html'=>true
    ));
  } */
  public function submit() {
	if (!$this->input->is_ajax_request()) {
		var_dump('Not ajax request');
		die;
		$this->redirect('');
	}
	if($_SERVER['REQUEST_METHOD'] !== 'POST'){
		var_dump('Not post request');
		die;
		$this->redirect('');
	}
    $this->load->library('form_validation');
    $should_validate = $this->validate();
    $valid = !$should_validate || ($this->form_validation->run() !== FALSE);
	if(!$valid){
		$this->data['errors'] = $this->form_validation->error_array();
		$this->outputError($this->form_validation->error_string());
	}
    
    $data = array();
    $data['lastname'] = trim($this->input->post('lastname'));
    $data['category'] = trim($this->input->post('category'));
    $data['firstname'] = trim($this->input->post('firstname'));
    $data['email'] = trim($this->input->post('email'));
    $data['phone'] = trim($this->input->post('phone'));
    $data['subject'] = trim($this->input->post('subject'));
    $data['body'] = trim($this->input->post('body'));
    $data['user_id'] = $this->user->id;
    $data['time_created'] = date('Y-m-d H:i:s');
    
    $this->db->insert('ac_feedback', array_replace($data, ['accessibility' => json_encode($this->input->post('accessibility'))]));
    $this->data = $data;
	$category_text = [
		'accessibility' => 'Accesibilitate',
		'observation' => 'Observatie',
		'complaint' => 'Reclamatie',
		'suggestion' => 'Sugestie',
		'other' => 'Alt tip',
	];
    $this->data['category_text'] = $category_text[$data['category']] ?? $data['category'];
    $this->data['id'] = $this->db->insert_id();
    $this->data['date'] = $data['time_created'];
	$this->theme->set_theme('accent');
	$this->theme->set_layout('email');
    $this->theme->set_sublayout('email/feedback/index');
    $this->theme->view('email/forms/feedback/admin', $this->data, $this);
if(1){
    Modules :: run ('Mailer/send_email', array(
      'subject'=>'[Feedback] ' . $data['lastname'] . ' ' . $data['firstname'],
      'to'=>array(
        // 'office@accenttravel.ro',
        'marketing@accenttravel.ro',
       ),
      'bcc'=>array(
        // 'alexandra.oprea@lisal.ro',
      ),
      'from_email'=>'vanzari@accenttravel.ro',
      'from_name'=>'Accent Travel & Events',
    ));
	if($data['email']){
		$this->theme->view('email/forms/feedback/client', $this->data, $this);
		Modules :: run ('Mailer/send_email', array(
		  'subject'=>'Feedback - Confirmare receptionare feedback',
		  'to'=>$data['email'],
		  // 'bcc'=>array(
			// 'alexandra.oprea@lisal.ro',
		  // ),
		  'from_email'=>'vanzari@accenttravel.ro',
		  'from_name'=>'Accent Travel & Events',
		));
	}
}
	$this->addMessage('Feedback trimis cu succes.','succes');
	$this->output();
	return;
  }
  protected function validate() {
    $this->form_validation->set_rules('lastname', 'Nume', 'trim|max_length[255]', array(
      'max_length'=>'Maxim 255 caractere Nume',
    ));
    $this->form_validation->set_rules('firstname', 'Prenume', 'trim|max_length[255]', array(
      'max_length'=>'Maxim 255 caractere Prenume',
    ));
    $this->form_validation->set_rules('email', 'Adresa de Email', 'trim|max_length[255]|valid_email', array(
      'max_length'=>'Maxim 255 caractere',
      'valid_email'=>'Adresa de email invalida',
    ));
    $this->form_validation->set_rules('phone', 'Telefon', 'trim|max_length[20]', array(
      'max_length'=>'Maxim 20 caractere Telefon',
    ));
    $this->form_validation->set_rules('subject', 'Subiect', 'trim|max_length[255]', array(
      'max_length'=>'Maxim 255 caractere',
    ));
    $this->form_validation->set_rules('category', 'Tip feedback', 'required', array(
      'required'=>'Camp necesar Subiect',
    ));
    $this->form_validation->set_rules('body', 'Continut', 'required', array(
      'required'=>'Camp necesar Continut',
    ));
    $this->form_validation->set_rules('tos', 'Termeni si conditii', 'required', array(
      'required'=>'Trebuie sa fiti de acord cu Termenii si conditiile',
    ));
    $this->form_validation->set_rules('tpc', 'Prelucrarea datelor', 'required', array(
      'required'=>'Trebuie sa fiti de acord cu Prelucrarea datelor',
    ));
    return true;
  }
}