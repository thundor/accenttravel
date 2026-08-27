<?php
defined('ENVIRONMENT') OR die('Invalid access');
$show_popup = true;

if(!$this->_ci->session->userdata('completat_formular_inregistrare')){
  $show_popup = true;
  if($this->_ci->user->id){
    $db = $this->_ci->db;
    $db->or_where('user_id', $this->_ci->user->id);
    $db->or_where('emailReg', $this->_ci->user->email);
    $q = $db->get('ac_concurs', 1, 0);
    $found = $q->result();
    if($found){
      $show_popup = false;
    }
  }
}
$this->_ci->load->model('Options_model');
$dont_show_home_popup = $this->_ci->Options_model->get('general_settings', 'dont_show_home_popup'); 
$show_popup = empty($dont_show_home_popup);

if(isset($this->general_settings['dont_show_home_popup']) && strlen($this->general_settings['dont_show_home_popup'])){
  $show_popup = false;
}
if($this->_ci->session->userdata('dont_show_home_popup')){
  $show_popup = false;
}
if($show_popup){
  themeFunctions::includeAddon('lazy-loading');
  themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php');
  themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
  themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
}