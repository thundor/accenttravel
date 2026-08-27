<?php
$save_data['provider'] = 'travelfuse';
$save_data['type'] = 'charter';

include(__DIR__ . '/offer-details.json.php');

if(!empty($offer_details)){
	$post_data['offer_details'] = $offer_details;
	
	if(!empty($offer_details['Offer'])){
		$save_data['amount'] = $offer_details['Offer']['Price'];
		$save_data['currency'] = $offer_details['Offer']['Currency']['Code'];
		$allow_save = true;
	}
}

