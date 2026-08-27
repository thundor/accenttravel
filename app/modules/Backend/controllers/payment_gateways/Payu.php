<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class PayU extends MX_Controller {
  function __construct() {
    parent :: __construct();
    $this->payment_methods_blocks = array(
      'CARD PAYMENTS' => array(
        'CCVISAMC',
      ),
      'MASTERPASS PAYMENTS' => array(
        'MASTERPASS',
      ),
      // 'INSTALLMENTS PAYMENTS' => array(
        // 'BRDF',
        // 'BRD_INSTALLMENTS',
        // 'STAR_BT',
        // 'ALPHABANK_INSTALLMENTS',
        // 'CARD_AVANTAJ',
        // 'OPTIMO',
      // ),
      'ONLINE BANKING' => array(
        'ITRANSFER_BCR',
        'ITRANSFER_BT',
      ),
      'OTHER' => array(
        'ZEBRA_PAY',
        'PAYPAL',
        'WIRE',
      ),
    );
    
    $this->payment_methods = array();
    foreach($this->payment_methods_blocks as $methods_block => $block_payment_methods){
      $this->payment_methods = array_merge($this->payment_methods, $block_payment_methods);
    }
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('payment_gateways_settings',null,array(
      'payu_merchant_id'=>'',
      'payu_secret_key'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    
    $payment_methods = $this->Options_model->getKeys('payu_payment_methods');
    if(!$payment_methods || !is_array($payment_methods)){
      $payment_methods = array();
    }
    $settings['accepted_payment_methods_blocks'] = $this->payment_methods_blocks;
    $settings['accepted_payment_methods'] = $this->payment_methods;
    $settings['payment_methods'] = $payment_methods;
    $settings['payu_status'] = (int)$this->Options_model->get('payment_gateways_status','payu',0);
    
    $this->data = $settings;
    $this->theme->view('backend/payment_gateways/payu', $this->data);
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    
    $this->load->library('form_validation');
    $status = (int) $this->input->post('payu_status');
    
    $this->form_validation->set_rules('payu_status', 'Status', 'in_list[0,1,-1,-2]');
    $this->form_validation->set_rules('payu_merchant_id', 'Merchant Integration Code', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('payu_secret_key', 'Secret Key', 'trim' . ($status ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    $data['payu_merchant_id'] = trim($this->input->post('payu_merchant_id'));
    if(!strlen($data['payu_merchant_id'])){
      $data['payu_merchant_id'] = null;
    }
    $data['payu_secret_key'] = trim($this->input->post('payu_secret_key'));
    if(!strlen($data['payu_secret_key'])){
      $data['payu_secret_key'] = null;
    }
    $payment_methods = $this->input->post('payu_payment_methods');
    if(!$payment_methods || !is_array($payment_methods)){
      $payment_methods = array();
    }
    $payu_payment_methods = array();
    foreach($this->payment_methods as $payment_method){
      $payu_payment_methods[$payment_method] = in_array($payment_method, $payment_methods) ? 1 : null;
    }
    
    
    $this->load->model('Options_model');
    $this->Options_model->set('payment_gateways_settings',$data);
    $this->Options_model->set('payu_payment_methods',$payu_payment_methods);
    $this->Options_model->setValue('payment_gateways_status','payu',(int)$status);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}