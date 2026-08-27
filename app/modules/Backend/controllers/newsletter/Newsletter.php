<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Newsletter extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access','backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->redirect('backend/newsletter/subscribers');
  }
}