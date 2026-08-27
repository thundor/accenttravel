<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Circuite extends MX_Controller {
  function __construct() {
    // $this->load->model('Travelfuse_model');
    // $this->load->model('Travelfuse/Travelfuse_Circuit_model');
    parent::__construct();
  }
  public function index() {
    // $this->setData();
    $this->theme->view('travelfuse/circuite/index', $this->data, $this);
  }
}