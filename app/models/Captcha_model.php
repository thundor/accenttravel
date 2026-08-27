<?php
/* 
CREATE TABLE ac_captcha (
  captcha_id bigint(13) unsigned NOT NULL auto_increment,
  date DATETIME,
  ip_address varchar(45) NOT NULL,
  keyword varchar(45) NOT NULL,
  filename varchar(45) NOT NULL,
  word varchar(20) NOT NULL,
  PRIMARY KEY `captcha_id` (`captcha_id`),
  KEY `word` (`word`),
  KEY `keyword` (`keyword`)
);
*/
class Captcha_model extends CI_Model {
  
  function validate($keyword = '', $word='', $vals=array()) {
    $default_vals = array(
      'img_path'      => APPPATH . 'tmp' . DIRECTORY_SEPARATOR,
    );
    $vals = array_replace($default_vals, $vals);
    $this->db->select('*');
    $this->db->where('keyword', $keyword);
    $this->db->where('ip_address', $this->input->ip_address());
    
    $q = $this->db->get('ac_captcha');
    if($q->num_rows()){
      foreach($q->result() as $item){
        $file_name = $item->filename;
        if(preg_match("/^[\d]+\.[\d]+\.(png|jpg)$/u", $file_name)){
          $file_path = $vals['img_path'] . $file_name;
          @unlink($file_path);
        }
        $this->db->where('captcha_id', $item->captcha_id);
        $this->db->delete('ac_captcha');
        if($item->word === $word){
          return true;
        }
        return false;
      }
    }
    return false;
  }
  function create($keyword = '', $vals=array()) {
    $default_vals = array(
      'img_path'      => APPPATH . 'tmp' . DIRECTORY_SEPARATOR,
      'img_url'       => base_url('captcha/image/'),
      'word_length'   => 4,
      'img_width'     => 100,
      'img_height'     => 40,
      'pool'   => '123456789ABCDEFGHIJKLMNPRSTUVWXYZ',
			'colors'	=> array(
				'background'	=> array(255,255,255),
				'border'	=> array(153,102,102),
				'text'		=> array(204,153,153),
				'grid'		=> array(255,182,182)
			)
    );
    $vals = array_replace($default_vals, $vals);
    
    $this->load->helper('captcha');
    
    $this->db->select('*');
    $this->db->where('keyword', $keyword);
    $this->db->where('ip_address', $this->input->ip_address());
    
    $q = $this->db->get('ac_captcha');
    
    if($q->num_rows()){
      foreach($q->result() as $item){
        $file_name = $item->filename;
        if(preg_match("/^[\d]+\.[\d]+\.(png|jpg)$/u", $file_name)){
          $file_path = $vals['img_path'] . $file_name;
          @unlink($file_path);
        }
        $this->db->where('captcha_id', $item->captcha_id);
        $this->db->delete('ac_captcha');
      }
    }
    
    $cap = create_captcha($vals);
    
    $data = array(
      'keyword'  => $keyword,
      'date'  => date('Y-m-d H:i:s',$cap['time']),
      'ip_address' => $this->input->ip_address(),
      'word' => $cap['word'],
      'filename' => $cap['filename'],
    );

    $query = $this->db->insert_string('ac_captcha', $data);
    $this->db->query($query);
    
    return $cap['image'];
  }
}