<?php

class Cdn {
  protected $_ci = NULL;
  protected $_config = array();
  
  /**
   * CDN::__construct()
   * @return void
   */
  function __construct() {
    $this->_ci = &get_instance();
    $this->_config = config_item('cdn');
    if($this->_config['ftp']){
      $this->load->library('Ftp', $this->_config['ftp']);
    }
  }
  
  function getUrl($file) {
    return $this->_config['url'] . $file;
  }
  function uploadLocal($local_file_path, $cdn_folder) {
    $file_name = basename($local_file_path);
    if($this->_config['ftp']){
    }
    return $this->_config['url'] . $cdn_folder . $file_name;
  }
  
}