<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/index/stylesheets.php'); ?>
<?php 

$this->_ci->load->model('Trip/Hotels_model');
$special_layout = $this->_controller=='Hotelsasync';
$hotel_search_data = [];
if($special_layout){
  $city_name = $this->_ci->uri->segment(3);
  if($city_name){
    $_GET['city_name'] = str_replace('_', ' ',$city_name);
  }
  $filters = array();
  $a = $this->_ci->input->get('a');
  if(isset($a) && is_string($a)){
    $this->_ci->load->model('Trip/Hotel_activities_model');
    $activity_categories = explode(',', '' . $a);
    $activities = $this->_ci->Hotel_activities_model->getActivitiesByCategories($activity_categories);
    $activity_ids = array();
    foreach($activities as $activity){
      $activity_ids[] = $activity->id;
    }
    $filters['activity_categories'] = $activity_categories;
    $filters['activities'] = $activity_ids;
  }
  $hotel_name = $this->_ci->input->get('hotel');
  if(isset($hotel_name) && is_string($hotel_name)){
    $hotel_search_data['hotel_name'] = trim($hotel_name);
  }
  $s = $this->_ci->input->get('s');
  if(isset($s) && is_string($s)){
    $filters['stars'] = explode(',', '' . $s);
  }
  $p = $this->_ci->input->get('p');
  if(isset($p) && is_string($p)){
    $filters['pois'] = explode(',', '' . $p);
  }
  $f = $this->_ci->input->get('f');
  if(isset($f) && is_string($f)){
    $filters['facilities'] = explode(',', '' . $f);
  }
  $hotel_search_data['filters'] = $filters;
	$start_date = $this->_ci->input->get('sdate');
  $d2 = null;
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$hotel_search_data['check-in'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$end_date = $this->_ci->input->get('edate');
  if(isset($end_date)){
    if(!isset($d2) || !$d2){
      $d2 = new DateTime('today midnight');
    }
		try{
			$d2->modify($end_date);
			$hotel_search_data['check-out'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
	  $ri = -1;
	  foreach($occupancy as $room_index => $room){
		$ri++;
		foreach($room as $k => $v){
		  $k = strtoupper($k);
		  if('ADT' === $k){
			  $hotel_search_data['travellers'][$ri][$k] = intval($v);
		  } elseif('CHD' === $k){
			  $chds = array_values(array_map('intval', array_values($v)));
			  $hotel_search_data['travellers'][$ri][$k] = $chds;
		  }
		}
	  }
    // $hotel_search_data['occupancy'] = $occupancy;
  }
	// $city_id = $this->_ci->input->get('city_id');
  // if(isset($city_id)){
    // $hotel_search_data['city_id'] = (int)$city_id;
  // }
	$city_name = $this->_ci->input->get('city_name');
  if(isset($city_name)){
	$hotel_search_data['destination-city'] = $city_name;
    /* $response = $this->_ci->Hotels_model->loadLocations($city_name);
    if($response){
      $first_city = $response[0];
      $hotel_search_data['destination-city'] = $first_city->CityId;
      // $hotel_search_data['city_id'] = $first_city->CityId;
      // $hotel_search_data['city_code'] = $first_city->CityCode;
      // $hotel_search_data['city_name'] = $first_city->Name;
      // $hotel_search_data['country_id'] = $first_city->CountryId;
      // $hotel_search_data['country_name'] = $first_city->CountryName;
    } */
  }
}

$submit = !empty($this->_ci->input->get('n'));
if($submit){
	$hotel_search_data['submit'] = true;
}
 ?>
<v-container class="pa-0" fluid>
<v-window class="" :touch="false">
	<v-window-item :value="0" class="w-100 fill-height">
		<v-card
			class="w-100 fill-height d-flex flex-column"
		>
			<component :is="loadViewAsync('partials/search-wrapper')" activate_menu="trip-hoteluri" :defaults="<?php echo htmlspecialchars(json_encode(['trip-hoteluri' => $hotel_search_data]), ENT_QUOTES); ?>">
				<module id="search-wrapper-inner-module"></module>
			</component>
		</v-card>
		
	</v-window-item>
	<v-window-item :value="1" class="w-100 fill-height">
		
	</v-window-item>
</v-window>
</v-container>
<?php themeFunctions::debugFileLine('end'); ?>