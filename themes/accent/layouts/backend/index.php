<?php
defined('ENVIRONMENT') OR die('Invalid access');
if($this->_method == 'login'){
  include 'login/index.php';
} else {
  include 'default/index.php';
}
?>