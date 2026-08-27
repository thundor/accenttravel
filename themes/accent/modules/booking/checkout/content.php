<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$u0 = $this->_ci->uri->segment(0);
$u1 = $this->_ci->uri->segment(1);
if($u0 == 'trip') {
  if($u1=='hotel') {
    themeFunctions::loadModule('booking/checkout/hotel',__FILE__);
  } elseif($u1=='flight') {
    themeFunctions::loadModule('booking/checkout/flight',__FILE__);
  } elseif($u1=='citybreak') {
    themeFunctions::loadModule('booking/checkout/citybreak',__FILE__);
  } elseif($u1=='package') {
    themeFunctions::loadModule('booking/checkout/package',__FILE__);
  }
} elseif($u0 == 'paralela45'){
  if($u1=='strainatate') {
    themeFunctions::loadModule('booking/checkout/paralela45/strainatate',__FILE__);
  }
  elseif($u1=='circuit') {
    themeFunctions::loadModule('booking/checkout/paralela45/circuit',__FILE__);
  }
} ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>