<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Chartere extends MX_Controller {
  function __construct() {
    // $this->load->model('Travelfuse_model');
    // $this->load->model('Travelfuse/Travelfuse_Chartere_model');
    parent::__construct();
  }
  public function index() {
    // $this->setData();
    $this->theme->view('travelfuse/chartere/index', $this->data, $this);
  }
}