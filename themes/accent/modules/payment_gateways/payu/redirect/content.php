<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model'); 
$payu_status = (int)$this->_ci->Options_model->get('payment_gateways_status','payu',0);
$settings = $this->_ci->Options_model->get('payment_gateways_settings',null,array(
  'payu_merchant_id'=>'',
  'payu_secret_key'=>'',
));
if(!$settings){
  $settings = array();
}
$processor_data = $this->view_data['processor_data'];
$payment_method = $this->view_data['payu_payment_method'];

$hidden_inputs = array();
$hidden_inputs['MERCHANT'] = $settings['payu_merchant_id'];
$hidden_inputs['ORDER_REF'] = $processor_data['order']['ref'];
$hidden_inputs['ORDER_DATE'] = $processor_data['order']['date'];
// $hidden_inputs['AUTOMODE'] = 1;
$hidden_inputs['LANGUAGE'] = 'RO';
$hidden_inputs['BACK_REF'] = $processor_data['success_url'];

$url = 'https://secure.payu.ro/';
if($payu_status < 0){
  $url = 'https://sandbox.payu.ro/';
  // $hidden_inputs['TESTORDER'] = 'TRUE';
}

$hidden_inputs['PRICES_CURRENCY'] = $processor_data['order']['currency'];
$hidden_inputs['DISCOUNT'] = $processor_data['order']['discount'];
$hidden_inputs['PAY_METHOD'] = $payment_method;
$hidden_inputs['ORDER_TIMEOUT'] = 600;
$hidden_inputs['TIMEOUT_URL'] = site_url('');

$hidden_inputs['BILL_FNAME'] = $processor_data['client']['fname'];
$hidden_inputs['BILL_LNAME'] = $processor_data['client']['lname'];
$hidden_inputs['BILL_EMAIL'] = $processor_data['client']['email'];
$hidden_inputs['BILL_PHONE'] = $processor_data['client']['phone'];
$hidden_inputs['BILL_COUNTRYCODE'] = $processor_data['client']['countrycode'];

if(isset($processor_data['client']['company'])){
  $hidden_inputs['BILL_COMPANY'] = $processor_data['client']['company'];
  $hidden_inputs['BILL_FISCALCODE'] = $processor_data['client']['cui'];
  $hidden_inputs['BILL_REGNUMBER'] = $processor_data['client']['regcom'];
}
$hidden_inputs['ORDER_PNAME[]'] = array();
$hidden_inputs['ORDER_PCODE[]'] = array();
$hidden_inputs['ORDER_PINFO[]'] = array();
$hidden_inputs['ORDER_PRICE[]'] = array();
$hidden_inputs['ORDER_PRICE_TYPE[]'] = array();
$hidden_inputs['ORDER_QTY[]'] = array();
$hidden_inputs['ORDER_VAT[]'] = array();
foreach($processor_data['order']['p_name'] as $k=>$p_name){
  $hidden_inputs['ORDER_PNAME[]'][] = $p_name;
  $hidden_inputs['ORDER_PCODE[]'][] = $processor_data['order']['p_code'][$k];
  $hidden_inputs['ORDER_PINFO[]'][] = $processor_data['order']['p_info'][$k];
  $hidden_inputs['ORDER_PRICE[]'][] = $processor_data['order']['p_price'][$k];
  $hidden_inputs['ORDER_PRICE_TYPE[]'][] = 'GROSS';
  $hidden_inputs['ORDER_QTY[]'][] = $processor_data['order']['p_qty'][$k];
  $hidden_inputs['ORDER_VAT[]'][] = $processor_data['order']['p_vat'][$k];
}
$signature_fields = array(
  'MERCHANT',
  'ORDER_REF',
  'ORDER_DATE',
  'ORDER_PNAME[]',
  'ORDER_PCODE[]',
  'ORDER_PINFO[]',
  'ORDER_PRICE[]',
  'ORDER_QTY[]',
  'ORDER_VAT[]',
  'ORDER_SHIPPING',
  'PRICES_CURRENCY',
  'DISCOUNT',
  'DESTINATION_CITY',
  'DESTINATION_STATE',
  'DESTINATION_COUNTRY',
  'PAY_METHOD',
  'ORDER_PRICE_TYPE[]',
  'SELECTED_INSTALLMENTS_NO',
  'TESTORDER'
);
$signature = '';
foreach($signature_fields as $signature_field){
  if(!isset($hidden_inputs[$signature_field])){
    // $signature .= '0';
    continue;
  }
  $fields = (array)$hidden_inputs[$signature_field];
  foreach($fields as $field){
    $signature .= mb_strlen($field,'UTF-8');
    $signature .= $field;
  }
}
$hidden_inputs['ORDER_HASH'] = hash_hmac('md5',$signature,$settings['payu_secret_key']);
$url .= 'order/lu.php';
$hidden_inputs['POST_URL'] = $url;
// if(IS_LISAL_IP){
	// dump($hidden_inputs);
	// dd($this->view_data);
// }
?>
<form id="onlineForm" name="onlineForm" action="<?php echo $url?>" method="POST" target="_top">
  <?php 
  foreach($hidden_inputs as $k=>$values){
    $values = (array)$values;
    foreach($values as $j=>$v){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>" /><?php 
      }
    }
  ?>
</form>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>