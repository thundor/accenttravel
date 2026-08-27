<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if ( ! function_exists('valid_date')) {
  function valid_date($date, $format='Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
  }
}
if ( ! function_exists('is_greater_than')) {
  function is_greater_than($str, $min){
		return ($str > $min);
	}
}
if ( ! function_exists('is_less_than')) {
  function is_less_than($str, $min){
		return ($str < $min);
	}
}
if ( ! function_exists('is_greater_than_or_equal_to')) {
  function is_greater_than_or_equal_to($str, $min){
		return ($str >= $min);
	}
}
if ( ! function_exists('is_less_than_or_equal_to')) {
  function is_less_than_or_equal_to($str, $min){
		return ($str <= $min);
	}
}
if ( ! function_exists('valid_country')) {
  function valid_country($value, $field='iso_2'){
    if($field == 'iso_2'){
      return strlen($value) == 2 && strtoupper($value) === $value;
    }
    if($field == 'iso_3'){
      return strlen($value) == 3 && strtoupper($value) === $value;
    }
    return false;
  }
}
if ( ! function_exists('valid_iban')) {
  function valid_iban($value){
	if('' === trim($value)) return true;
	if('-' === trim($value)) return true;
    $iban = strtolower(str_replace(' ','',$value));
    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);
    if (isset($Countries[substr($iban,0,2)]) && strlen($iban) == $Countries[substr($iban,0,2)]){
      $MovedChar = substr($iban, 4).substr($iban,0,4);
      $MovedCharArray = str_split($MovedChar);
      $NewString = "";
                        
      foreach($MovedCharArray AS $key => $value){
        if(!is_numeric($MovedCharArray[$key])){
          $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
        }
        $NewString .= $MovedCharArray[$key];
      }
      if(bcmod($NewString, '97') == 1) {
        return TRUE;
      }
    }
    return false;
  }
}
if ( ! function_exists('validate_CIF')) {
  function validate_CIF($value){
    if(!is_int($value)){
      $value = strtoupper($value);
      if(strpos($value, 'RO') === 0){
        $value = substr($value, 2);
      }
      $value = (int) trim($value);
    }

    if(strlen($value) > 10 || strlen($value) < 6){
      return false;
    }
    $v = 753217532;

    $c1 = $value % 10;
    $value = (int) ($value / 10);

    $t = 0;
    while($value > 0){
      $t += ($value % 10) * ($v % 10);
      $value = (int) ($value / 10);
      $v = (int) ($v / 10);
    }

    $c2 = $t * 10 % 11;
    if($c2 == 10){
      $c2 = 0;
    }
    return $c1 === $c2;
  }
}
if ( ! function_exists('validate_CUI')) {
  function validate_CUI($value){
    $regex = '^([Rr]?[0-9]{5,8})|((1|2)([1-9]{1}[0-9]{1})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])([0-9]{6}))$';
    $modifiers = 'i';
    if (preg_match(chr(1) . $regex . chr(1) . $modifiers, $value)){
      return true;
    }
    return false;
  }
}
if ( ! function_exists('validate_CIF_or_CUI')) {
  function validate_CIF_or_CUI($value){
    return validate_CUI($value) || validate_CIF($value);
  }
}
if ( ! function_exists('valid_user_id')) {
  function valid_user_id($value){
    if(!is_numeric($value) || ('' . (int)$value !== '' . $value)){
      return false;
    }
    $ci = get_instance();
    $ci->load->model('Account_model');
    $user = $ci->Account_model->getAccounts(array(
      'id' => (int)$value,
      'status' => 1,
      'select' => array('user_id'),
      'return_row' => true,
    ));
    if($user){
      return true;
    }
    return false;
  }
}
if ( ! function_exists('valid_order_id')) {
  function valid_order_id($value){
    if(!is_numeric($value) || ('' . (int)$value !== '' . $value)){
      return false;
    }
    $ci = get_instance();
    $ci->load->model('TripOrder_model');
    $order = $ci->TripOrder_model->getOrders(array(
      'id' => (int)$value,
      'select' => array('id'),
      'return_row' => true,
    ));
    if($order && $order->id){
      return true;
    }
    return false;
  }
}
if ( ! function_exists('validate_positive_int')) {
  function validate_positive_int($value){
    return ('' . $value === '' . (int)$value) && $value>=0;
  }
}
if ( ! function_exists('validate_positive_int_strict')) {
  function validate_positive_int_strict($value){
    return ('' . $value === '' . (int)$value) && $value>0;
  }
}
if ( ! function_exists('validate_alpha_spaces')) {
  function validate_alpha_spaces($str){
    return (bool) preg_match('/^[A-Z \-]+$/i', $str);
  }
}