<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('format_price')) {
  function format_price($amount = 0, $currency = 'EUR', $with_symbol=true){
    $amount_formatted = number_format($amount,2,',','.');
	if(!$with_symbol){
		return $amount_formatted;
	}
    $symbol = $currency;
    if($currency == 'RON'){
      $symbol = 'Lei';
    } elseif($currency == 'EUR'){
      $symbol = '€';
    }
    return $amount_formatted . ' ' . $symbol;
  }
}