<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Home extends MX_Controller {
  public function index() {
    $this->theme->view('home/index', $this->data, $this);
  }
}