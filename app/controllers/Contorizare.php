<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Contorizare extends MX_Controller {
  public function dontshow() {
    $status = $this->input->get('status');
    $this->session->set_userdata('dont_show_home_popup', $status == 'true' ? 1 : null);
  }
  public function index() {
    $file_path = APPPATH . 'logs' . DIRECTORY_SEPARATOR . 'contorizare.csv';
    $f = fopen($file_path,'a+');
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    fputcsv($f,array(date('Y-m-d H:i:s'), $ip, $this->user->username));
    fclose($f);
  }
}