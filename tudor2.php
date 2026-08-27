<?php
// echo 'test';
// die;
// phpinfo();
// die;
ini_set('display_errors',1 );
function searchRecursively($path)
{ 

    if(!is_dir($path) and !file_exists($path)) return false;

        $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($paths as $item) {
            if ($item->isDir()) 
            {
                
            } 
            else 
            {
                if(!in_array($item->getExtension(), array('php'))){
                  continue;
                }
                $content = file_get_contents($item->__toString());
				// $word = 'currency_symbol';
				// $word = 'agency';
				// $word = 'trip/checkout/coupon';
				// $word = 'coupon_code';
				// $word = '40372999006';
				// $word = 'getValidCoupon';
				// $word = 'TripOrderCoupon_model';
				// $word = 'applyCoupon';
				$word = 'trip_cities';
                // if(!preg_match('/\b' . preg_quote($word,'/') . '\b/',$content)){
                if(strpos($content,$word) === false){
                  continue;
                }
                var_dump($item->__toString());
            }
    }
    return true;
} 
echo 'begin';
echo '<pre>';
searchRecursively(__DIR__ . '/app/controllers');
searchRecursively(__DIR__ . '/app/models');
searchRecursively(__DIR__ . '/app/helpers');
searchRecursively(__DIR__ . '/app/modules');
searchRecursively(__DIR__ . '/themes');
echo 'end';
die;