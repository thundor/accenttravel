<?php
$this->load->model('TravelFuse_model');
$search_data = [
	'CityCode' => (int)$this->input->post('CityCode'),
	'DestinationType' => $this->input->post('DestinationType'),
	'CheckIn' => $this->input->post('CheckIn'),
	'CheckOut' => $this->input->post('CheckOut'),
	'Adults' => array_map('intval', $this->input->post('Adults')),
	'Children' => array_map('count', $this->input->post('ChildrenAge') ?? []),
	'ChildrenAge' => array_map(function($v){ return array_map('intval', $v); }, $this->input->post('ChildrenAge') ?? []),
];
// echo json_encode($search_data);
// return;
$hotels = $this->TravelFuse_model->individualOfferList($search_data);
if($hotels){
	$hotels = array_combine(array_column($hotels, 'Id'), $hotels);
	$ids = array_keys($hotels);
	$hotels_details = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => implode(',',$ids)]);
	$hotels_details = array_combine(array_column($hotels_details, 'Id'), $hotels_details);
	$hotels = array_replace_recursive($hotels, $hotels_details);
	array_walk_recursive($hotels, function(&$v){
		if($v && !is_numeric($v)){
			$v = trim(htmlspecialchars_decode($v));
		}
		return $v;
	});
	$hotels = array_values($hotels);
}

// $search_data['ProductCode'] = 33306;
// $hotels = $this->TravelFuse_model->individualOfferDetails($search_data);
echo json_encode($hotels ? $hotels : []);