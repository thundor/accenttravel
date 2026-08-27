<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('datepicker');
themeFunctions::includeAddon('font-icons/font-awesome');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Trip/Packages_model');

$special_layout = $this->_controller=='Packages';

$this->_ci->load->model('Options_model');
$settings = $this->_ci->Options_model->get('trip_packages_settings');
if(!$settings){
  $settings = array();
}
$this->_ci->load->model('Trip/Packages_model');
$load_from_session = true;
$package_search_data = $this->_ci->Packages_model->getSearchData();
if($special_layout){
  $origin = $this->_ci->uri->segment(3);
  if(isset($origin)){
    $_GET['origin'] = str_replace('_', ' ',$origin);
  }
  $destination = $this->_ci->uri->segment(4);
  if(isset($destination)){
    $_GET['destination'] = str_replace('_', ' ',$destination);
  }
}
if($special_layout && !empty($_GET)){
  $load_from_session = false;
	$package_search_data = $this->_ci->Packages_model->getSearchDefaultData();
}
$package_categories = array();
$include_package_categories = array();
if(isset($settings['categories']) && !empty($settings['categories'])){
  $include_package_categories = explode(',', $settings['categories']);
}
if($special_layout){
  $origin = $this->_ci->input->get('origin');
  $destination = $this->_ci->input->get('destination');
}
$package_categories_result = $this->_ci->Packages_model->loadPackageCategories();
if($package_categories_result){
  foreach($package_categories_result->_embedded->categories as $package_category){
    if(strpos($package_category->Name,'!') !== false){
      continue;
    }
    if($include_package_categories && !in_array($package_category->Id, $include_package_categories)){
      continue;
    }
    if($special_layout && isset($origin)){
      if(strtolower($origin) == strtolower($package_category->Code)){
        $_GET['category'] = $package_category->Code;
        $_GET['city_id'] = null;
      }
    }
    $package_categories[] = $package_category;
  }
}
$this->package_categories = &$package_categories;
$include_package_destinations = array();
if(isset($settings['destinations']) && !empty($settings['destinations'])){
  $include_package_destinations = explode(',', $settings['destinations']);
}
$package_destinations = array();
$package_destinations_result = $this->_ci->Packages_model->loadPackageDestinations();

if($package_destinations_result){
  foreach($package_destinations_result->_embedded->cities as $package_destination){
    if($include_package_destinations && !in_array($package_destination->Id, $include_package_destinations)){
      continue;
    }
    if($special_layout && isset($destination)){
      if(strtolower($destination) == strtolower($package_destination->Name)){
        $_GET['category'] = null;
        $_GET['city_id'] = $package_destination->Id;
      }
    }
    $package_destinations[] = $package_destination;
  }
}
$this->package_destinations = &$package_destinations;
if($special_layout){
  $filters = array();
  $project_id = $this->_ci->input->get('project_id');
  if(isset($project_id)){
    $package_search_data['project_id'] = (int)$project_id;
  }
  $hotel_name = $this->_ci->input->get('hotel');
  if(isset($hotel_name)){
    $package_search_data['hotel_name'] = '' . $hotel_name;
  }
  $category = $this->_ci->input->get('category');
  if(isset($category)){
    $package_search_data['category'] = '' . $category;
  }
  $city_id = $this->_ci->input->get('city_id');
  if(isset($city_id)){
    $package_search_data['city_id'] = (int)$city_id;
  }
  $nights = $this->_ci->input->get('nights');
  if(isset($nights)){
    $package_search_data['nights'] = (int)$nights;
  }
  $occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
    $package_search_data['occupancy'] = $occupancy;
  }
  $start_date = $this->_ci->input->get('sdate');
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$package_search_data['start_date'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
}
if($_GET){
  $package_search_data['index_id'] = null;
}
$this->package_search_data = &$package_search_data;