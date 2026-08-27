<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/index/stylesheets.php'); ?>
<?php 

$this->_ci->load->model('Trip/Citybreaks_model');

$special_layout = $this->_controller=='Citybreaksasync';
$search_data = [];
if($special_layout){
  $origin = $this->_ci->uri->segment(3);
  if($origin){
    $_GET['origin'] = str_replace('_', ' ',$origin);
  }
  $destination = $this->_ci->uri->segment(4);
  if($destination){
    $_GET['destination'] = str_replace('_', ' ',$destination);
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
    $search_data['hotel_name'] = trim($hotel_name);
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
  $search_data['filters'] = $filters;
	$start_date = $this->_ci->input->get('sdate');
  $d2 = null;
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$search_data['check-in'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$end_date = $this->_ci->input->get('edate');
  if(isset($end_date)){
    if(!isset($d2) || !$d2){
      $d2 = new DateTime('today midnight');
    }
		try{
			$d2->modify($end_date);
			$search_data['check-out'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
  $class = $this->_ci->input->get('class');
  if(isset($class) && is_numeric($class) && isset($this->_ci->Flights_model->cabin_type_map[(int)$class])){
    $search_data['cabin'] = $this->_ci->Flights_model->cabin_type_map[(int)$class];
    // $flights_search_data['cabine_type'] = (int)$class;
  }
  
  $occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
	  $ri = -1;
	  foreach($occupancy as $room_index => $room){
		$ri++;
		foreach($room as $k => $v){
		  $k = strtoupper($k);
		  if('ADT' === $k){
			  $search_data['travellers'][$ri][$k] = intval($v);
		  } elseif('CHD' === $k){
			  $chds = array_values(array_map('intval', array_values($v)));
			  $search_data['travellers'][$ri][$k] = $chds;
		  }
		}
	  }
    // $hotel_search_data['occupancy'] = $occupancy;
  }
  
  foreach(array('origin', 'destination') as $param){
    $g_param = $this->_ci->input->get($param);
	$param2 = $param;
	if($param === 'origin'){
		$param2 = 'departure';
	}
    if(isset($g_param)){
		$search_data[$param2 . '-' . 'city'] = $g_param;
      /* $this->_ci->load->model('Trip/Flights_model');
      $response = $this->_ci->Flights_model->loadLocations($g_param);
      if($response){
        $first_location = $response[0];
        $search_data[$param2 . '-' . 'city'] = (isset($first_location->LocationId) ? $first_location->LocationId : 0) . ',' . $first_location->CityId;
        // $search_data[$param . '_' . 'country_id'] = $first_location->CountryId;
        // $search_data[$param . '_' . 'location_id'] = isset($first_location->LocationId) ? $first_location->LocationId : 0;
        // $search_data[$param . '_' . 'location_name'] = isset($first_location->LocationName) ? $first_location->LocationName : '';
        // $search_data[$param . '_' . 'city_name'] = $first_location->CityName;
        // $search_data[$param . '_' . 'country_name'] = $first_location->CountryName;
        // $search_data[$param . '_' . 'full_location_name'] = ($first_location->LocationId ? $first_location->LocationName . ', ' : '') . $first_location->CityName;
      } */
    }
  }
}
$submit = !empty($this->_ci->input->get('n'));
if($submit){
	$search_data['submit'] = true;
}
// dd($search_data); 
?>
<v-container class="pa-0" fluid>
<v-window class="" :touch="false">
	<v-window-item :value="0" class="w-100 fill-height">
		<v-card
			class="w-100 fill-height d-flex flex-column"
		>
			<component :is="loadViewAsync('partials/search-wrapper')" activate_menu="trip-citybreak" :defaults="<?php echo htmlspecialchars(json_encode(['trip-citybreak' => $search_data]), ENT_QUOTES); ?>">
				<module id="search-wrapper-inner-module"></module>
			</component>
		</v-card>
		
	</v-window-item>
	<v-window-item :value="1" class="w-100 fill-height">
		
	</v-window-item>
</v-window>
</v-container>
<?php themeFunctions::debugFileLine('end'); ?>