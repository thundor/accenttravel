<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 

$loaded = false; 
$this->_ci->load->model('Options_model');

$allowed_statuses = array(1,-2);
if($this->_ci->user->can('backend-config-save')){
  $allowed_statuses[] = -1;
}

$trip_24_pay = config_item('trip_24_pay');

$agency_status = $this->_ci->Options_model->get('payment_methods_status','agency');
if(!$trip_24_pay && in_array($agency_status,$allowed_statuses)){
  $loaded = themeFunctions::loadModule('booking/payment_methods/agency',__FILE__ . '/payment_methods', array('nav'=>'/nav', 'content'=>'/content', 'active'=>true));
}
$bank_status = (int)$this->_ci->Options_model->get('payment_methods_status','bank');
if(!$trip_24_pay && in_array($bank_status,$allowed_statuses)){
  $loaded = themeFunctions::loadModule('booking/payment_methods/bank',__FILE__ . '/payment_methods', array('nav'=>'/nav', 'content'=>'/content', 'active'=>!$loaded));
}
$online_status = (int)$this->_ci->Options_model->get('payment_methods_status','online');
if(in_array($online_status,$allowed_statuses)){
  $this->_ci->db->where_in('option_value',$allowed_statuses);
  $static_settings = $this->_ci->Options_model->getKeys('payment_gateways_status');
  if($static_settings){
    themeFunctions::loadModule('booking/payment_methods/online',__FILE__ . '/payment_methods', array('nav'=>'/nav', 'content'=>'/content', 'active'=>!$loaded));
  }
  $this->available_payment_gateways = $static_settings;
  
	if($trip_24_pay){
		$this->available_payment_gateways = array_intersect($this->available_payment_gateways, array('pay24'));
	}
}
$has_payment_methods = themeFunctions::hasIncludes(__FILE__ . '/payment_methods/nav');
if($has_payment_methods){
$loaded = themeFunctions::loadModule('booking/payment_methods/free',__FILE__ . '/payment_methods', array('nav'=>'/nav', 'content'=>'/content', 'active'=>!$loaded));
?>
<div id="payment_methods">
  <ul class="nav nav-tabs" id="payment_methods_nav">
    <?php themeFunctions::loadAddons(__FILE__ . '/payment_methods/nav'); ?>
  </ul>
  <div class="tab-content">
    <?php themeFunctions::loadAddons(__FILE__ . '/payment_methods/content'); ?>
  </div>
</div>
<?php } else {
themeFunctions::blockModule('booking/checkout', true);
?>
<h4>Platforma nu dispune de metode de plata potrivite acestei cereri. Pentru informatii suplimentare, va rugam sa contactati echipa de suport tehnic.</h4>
<?php if(isset($this->general_settings['contact_phone_number']) && strlen($this->general_settings['contact_phone_number'])) { ?>
<div class="w-100 text-center mt-4 mb-4"><a href="tel:<?php echo $this->general_settings['contact_phone_number']; ?>" class="btn btn-primary"><i class="fa fa-phone"></i> Suna pentru suport la <br><?php echo isset($this->general_settings['contact_phone_text']) ? $this->general_settings['contact_phone_text'] : $this->general_settings['contact_phone_number'] ?>!</a></div>
<?php } ?>
<?php } ?>
<?php themeFunctions::debugFileLine('end'); ?>