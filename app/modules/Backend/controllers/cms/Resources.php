<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Resources extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->canAny('backend-cms-resources-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/cms/resources', $this->data);
  }
}