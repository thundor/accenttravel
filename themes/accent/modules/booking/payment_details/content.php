<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$u0 = $this->_ci->uri->segment(0);
$u1 = $this->_ci->uri->segment(1);
$u2 = $this->_ci->uri->segment(2);
$allow_coupon = true;
$construct_key = '';
if($u0 == 'paralela45'){
	$construct_key = $u0 . '_' .  $u1;
} elseif($u0 == 'trip'){
	$construct_key = $u1;
}
if(in_array($construct_key, array('flight','citybreak'))){
	$allow_coupon = false;
}
if(!$allow_coupon && in_array($construct_key, array('hotel','package','flight','citybreak','paralela45_strainatate','paralela45_circuit'))){
  $allow_coupon = false;
  $this->_ci->db->select('*');
  $this->_ci->db->where($construct_key, 1);
  $this->_ci->db->where('status', 1);
  $q = $this->_ci->db->get('trip_coupon', 1, 0);
  $any_coupon = $q->row();
  if($any_coupon){
	$allow_coupon = true;
  }
}

?>
<?php themeFunctions::loadModule('booking/coupon',__FILE__ . ($allow_coupon ? '' : '_blocked'), array('once' => true)); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<h3 class="subSecTicket col-12 mb-4"> DETALII PLATA</h3>       
<div class="col-md-4 col-12 mb-2">
  <div class="row">
    <div class="col-12"><?php 
    if($u0 == 'trip') {
      if($u1=='hotel') {
        themeFunctions::loadModule('booking/payment_details/hotel',__FILE__ . '/payment_details');
      } elseif($u1=='flight') {
        themeFunctions::loadModule('booking/payment_details/flight',__FILE__ . '/payment_details');
      } elseif($u1=='citybreak') {
        themeFunctions::loadModule('booking/payment_details/citybreak',__FILE__ . '/payment_details');
      } elseif($u1=='package') {
        themeFunctions::loadModule('booking/payment_details/package',__FILE__ . '/payment_details');
      }
    } elseif($u0 == 'paralela45'){
      if($u1=='strainatate') {
        themeFunctions::loadModule('booking/payment_details/paralela45/strainatate',__FILE__ . '/payment_details');
      }
      elseif($u1=='circuit') {
        themeFunctions::loadModule('booking/payment_details/paralela45/circuit',__FILE__ . '/payment_details');
      }
    }
    themeFunctions::loadAddons(__FILE__ . '/payment_details'); ?>
    </div>
  </div>  
</div>               
<div class="col-md-8 col-12 mb-4">
  <?php themeFunctions::loadModule('booking/payment_methods',__FILE__ . '/payment_methods');
   themeFunctions::loadAddons(__FILE__ . '/payment_methods');
   themeFunctions::loadModule('booking/checkout',__FILE__ . '/checkout');
   themeFunctions::loadAddons(__FILE__ . '/checkout'); ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>