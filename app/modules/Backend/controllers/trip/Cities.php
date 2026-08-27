<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Cities extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access')){
      redirect('backend');
    }
    $this->theme->view('backend/trip/cities', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces invalid');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces invalid');
    }
    $filters = array();
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-save');
    $user_can['delete'] = false;
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    
    $this->load->model('Trip/Trip_cities_model');
    $this->data['total_cities'] = $this->Trip_cities_model->getTotalCities($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_cities'] / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    $this->data['max_pages'] = $max_pages;
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    $filters['select'] = '*';
    $cities = $this->Trip_cities_model->getCities($filters);
    foreach($cities as $k=>$city){
      if($user_can['view']){
        $city->can_view = true;
        $city->view_link = site_url('backend/trip/cities/view?id=' . $city->id);
      }
      if($user_can['edit']){
        $city->can_edit = true;
        $city->edit_link = site_url('backend/trip/cities/edit?id=' . $city->id);
      }
    }
    $this->data['cities'] = $cities;
    $this->data['page'] = $current_page;
    
    $session_data = array();
    $session_data['page'] = $current_page;
    $session_data['ordering'] = $ordering;
    $session_data['search'] = $search;
    $session_data['limit'] = $limit;
    $this->session->set_userdata('backend/trip/cities', $session_data);
    $this->output();
  }
  public function getParents() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces invalid');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces invalid');
    }
    $id = (int)$this->input->get('id');
    $filters = array();
    $filters['parent_id'] = 0;
    $this->load->model('Trip/Trip_cities_model');
    $this->db->where_not_in('id', array($id));
    $this->data['total_cities'] = $this->Trip_cities_model->getTotalCities($filters);
    $limit = 10;
    $filters['limit'] = $limit;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_cities'] / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    $this->data['max_pages'] = $max_pages;
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    $filters['select'] = 'id,name,country_name';
    $this->db->where_not_in('id', array($id));
    $cities = $this->Trip_cities_model->getCities($filters);
    $this->data['cities'] = $cities;
    $this->data['page'] = $current_page;
    
    $session_data = array();
    $session_data['page'] = $current_page;
    $session_data['ordering'] = $ordering;
    $session_data['search'] = $search;
    $session_data['limit'] = $limit;
    $this->session->set_userdata('backend/trip/cities', $session_data);
    $this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access', 'backend-config-save')){
      redirect('backend');
    }
    $city = new stdClass;
    $city->id = null;
    $city->name = null;
    $city->country_id = null;
    $city->country_iso_2 = null;
    $city->country_name = null;
    $city->image = null;
    $city->description = null;
    $city->trip_city_id = null;
    $city->trip_city_name = null;
    $city->trip_country_id = null;
    $city->trip_country_name = null;
    $city->aida_city_id = null;
    $city->aida_city_name = null;
    $city->aida_country_id = null;
    $city->aida_country_name = null;
    $this->data['city'] = $city;
    $this->theme->view('backend/trip/city', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access', 'backend-config-save')){
      redirect('backend');
    }
    $id = strtolower(trim($this->input->get('id')));
    if(!$id){
      redirect('backend/trip/cities');
    }
    $this->load->model('Trip/Trip_cities_model');
    $city = $this->Trip_cities_model->getCityById($id);
    $this->data['city'] = $city;
    $this->theme->view('backend/trip/city', $this->data);
  }
  
  public function view() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access')){
      redirect('backend');
    }
    $id = strtolower(trim($this->input->get('id')));
    if(!$id){
      redirect('backend/trip/cities');
    }
    $this->load->model('Trip/Trip_cities_model');
    $city = $this->Trip_cities_model->getCityById($id);
    $this->data['city'] = $city;
    $this->theme->view('backend/trip/city', $this->data);
  }
  
  /* public function delete() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access', 'backend-config-save')){
      redirect('backend');
    }
    $id = strtolower(trim($this->input->get('id')));
    if(!$id){
      redirect('backend/trip/cities/add');
    }
    $this->load->model('Trip/Trip_cities_model');
    
    $city = $this->Trip_cities_model->getCityObj($id, $this->city_path);
    if(!$city){
      redirect('backend/trip/cities');
    }
    $this->Trip_cities_model->deleteCityBySlug($id, $this->city_path);
    redirect('backend/trip/cities');
  } */
  public function loadTripCities() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    $q = '' . $this->input->get('q');
    
    $this->load->model('Trip/Flights_model');
    $flight_response = $this->Flights_model->loadLocations($q);
    if(!$flight_response){
      $this->outputTripError('Nu s-au putut prelua locatiile de zbor');
    }
    $this->data['flight_response'] = $flight_response;
    
    $this->load->model('Trip/Hotels_model');
    $hotel_response = $this->Hotels_model->loadLocations($q);
    if(!$hotel_response){
      $this->outputTripError('Nu s-au putut prelua locatiile de hotel');
    }
    $this->data['hotel_response'] = $hotel_response;
    
    $countries = array();
    $cities = array();
    foreach(array($flight_response, $hotel_response) as $response){
      foreach($response as $location){
        if(isset($cities[$location->CityId])){
          continue;
        }
        $city = new stdClass;
        $city->CountryId = 0;
        $city->CountryName = $location->CountryName;
        $city->CountryCode = '';
        $city->TripCountryId = $location->CountryId;
        $city->TripCountryName = $location->CountryName;
        $city->TripCityId = $location->CityId;
        if(isset($location->LocationId)){
          $city->TripCityName = $location->CityName;
        } else {
          $city->TripCityName = $location->Name;
        }
        $cities[$location->CityId] = $city;
        $countries[$location->CountryId][] = $location->CityId;
      }
    }
    if($countries){
      $country_ids = array_keys($countries);
      $this->load->model('Country_model');
      $filters = array(
        'select' => 'trip_id,iso_2,country_id,IFNULL(`name_RO`,`name`) as text',
        'trip_id' => $country_ids,
        'return_rows' => 1,
      );
      $result_countries = $this->Country_model->getCountries($filters);
      foreach($result_countries as $result_country){
        foreach($countries[$result_country->trip_id] as $city_id){
          $cities[$city_id]->CountryId = $result_country->country_id;
          $cities[$city_id]->CountryCode = $result_country->iso_2;
          $cities[$city_id]->CountryName = $result_country->text;
        }
      }
    }
    
    $this->data['results'] = array_values($cities);
    
    $this->output();
  }
  public function loadAidaCountries() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    $q = '' . $this->input->get('q');
    $this->load->model('Trip/Packages_model');
    $filter = array();
    $filter[] = array(
      'name' => 'Name',
      'type' => 'like',
      'term' => $q . '%',
    );
    $get = array(
      'filter' => $filter,
      'limit' => 10,
    );
    $package_response = $this->Packages_model->loadCountries($get);
    if(!$package_response){
      $this->outputTripError('Nu s-au putut prelua tarile');
    }
    $countries = array();
    foreach($package_response->_embedded->countries as $k=>$country){
      $country->CountryId = 0;
      $country->CountryCode = $country->ISO2;
      $country->CountryName = $country->Name;
      if($country->ISO2){
        continue;
      }
      $countries[$country->Id] = $k;
    }
    if($countries){
      $country_ids = array_keys($countries);
      $this->load->model('Country_model');
      $filters = array(
        'select' => 'aida_id,iso_2,country_id,IFNULL(`name_RO`,`name`) as text',
        'aida_id' => $country_ids,
        'return_rows' => 1,
      );
      $result_countries = $this->Country_model->getCountries($filters);
      foreach($result_countries as $result_country){
        $package_country = &$package_response->_embedded->countries[$countries[$result_country->aida_id]];
        $package_country->CountryId = $result_country->country_id;
        $package_country->CountryCode = $result_country->iso_2;
        $package_country->CountryName = $result_country->text;
      }
    }
    $this->data['package_response'] = $package_response;
    $this->data['results'] = $package_response->_embedded->countries;
    $this->output();
  }
  public function loadAidaCities() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    $q = '' . $this->input->get('q');
    $country_id = (int)$this->input->get('country_id');
    $this->load->model('Trip/Packages_model');
    $filter = array();
    $filter[] = array(
      'name' => 'Name',
      'type' => 'like',
      'term' => $q . '%',
    );
    $get = array(
      'filter' => $filter,
      'limit' => 10,
    );
    $package_response = $this->Packages_model->loadCountryCities($country_id, $get);
    if(!$package_response){
      $this->outputTripError('Nu s-au putut prelua orasele');
    }
    $this->data['package_response'] = $package_response;
    $this->data['results'] = $package_response->_embedded->cities;
    $this->output();
  }
  public function loadAidaCity() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    $city_id = (int)$this->input->get('city_id');
    $country_id = (int)$this->input->get('country_id');
    $this->load->model('Trip/Packages_model');
    $response = $this->Packages_model->loadCityDetails($country_id, $city_id);
    if(!$response){
      $this->outputTripError('Nu s-au putut prelua orasele');
    }
    $this->data = $response;
    $this->output();
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->redirect('','Acces invalid','error');
    }
    if(!$this->user->can('backend-config-access','backend-config-save')){
      $this->redirect('','Acces invalid','error');
    }
    // echo '<pre>';
    // print_r($this->input->post());
    // die;
    $task = $this->input->post('task');
    $id = $this->input->post('id');
    
    $this->load->model('Trip/Trip_cities_model');
    if($id){
      $city = $this->Trip_cities_model->getCityById($id);
      if(!$city){
        $this->redirect('backend/trip/cities','Orasul nu a fost gasit','error');
      }
    } else {
      $city = new stdClass;
    }
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name', 'Nume oras', 'trim|required|max_length[255]');
    
    $data = array();
    $city->id = $data['id'] = $id;
    $city->name = $data['name'] = $this->input->post('name');
    $city->description = $data['description'] = $this->input->post('description');
    $city->image = $data['image'] = $this->input->post('image');
    $city->parent_id = $data['parent_id'] = $this->input->post('parent_id');
    $city->country_id = $data['country_id'] = $this->input->post('country_id');
    $city->country_name = $data['country_name'] = $this->input->post('country_name');
    $city->country_iso_2 = $data['country_iso_2'] = $this->input->post('country_iso_2');
    $city->trip_city_id = $data['trip_city_id'] = $this->input->post('trip_city_id');
    $city->trip_country_id = $data['trip_country_id'] = $this->input->post('trip_country_id');
    $city->trip_city_name = $data['trip_city_name'] = $this->input->post('trip_city_name');
    $city->trip_country_name = $data['trip_country_name'] = $this->input->post('trip_country_name');
    $city->aida_city_id = $data['aida_city_id'] = $this->input->post('aida_city_id');
    $city->aida_country_id = $data['aida_country_id'] = $this->input->post('aida_country_id');
    $city->aida_city_name = $data['aida_city_name'] = $this->input->post('aida_city_name');
    $city->aida_country_name = $data['aida_country_name'] = $this->input->post('aida_country_name');
    
    $files_image = isset($_FILES['image']) ? $_FILES['image'] : array();
    $image_tmp_name = isset($files_image['tmp_name']) ? $files_image['tmp_name'] : '';
    if($files_image && strlen($image_tmp_name)){
      $_POST['image_upload'] = null;
      $message = 'Imaginea incarcata este invalida';
      if($files_image['error']){
        $message = 'Nu s-a putut incarca imaginea';
      } else {
        $image_name = isset($files_image['name']) ? $files_image['name'] : '';
        
        $image_ext = strrchr($image_name, ".");
        $image_extension = substr($image_ext, 1);
        $image = false;
        if($image_extension === $image_name || '.' . $image_extension === $image_name){
          $image_extension = false;
        }
        if($image_extension){
          $safe_image_name = trim(preg_replace("/[^a-zA-Z0-9\.\-\_]/", '', $image_name),'. ');
          $image_basename = basename($safe_image_name,$image_ext);
          if(strlen($image_basename) && $image_extension && in_array(strtolower($image_extension), array('jpg','png', 'gif'))){
            $image = getimagesize($image_tmp_name);
          }
        }
        if($image){
          $image_size = isset($image['size']) ? (int)$image['size'] : 0;
          $image_size_kb = $image_size / 1024;
          if($image_size_kb > 10 * 1024){
            $message = 'Imaginea incarcata depaseste 10 MB';
          } else {
            $file_deposit_path = $this->theme->theme_path . 'assets/images/icons/' . $safe_image_name;
            $data['image'] = 'icons/' . $safe_image_name;
            if(file_exists($file_deposit_path)){
              $_POST['image_upload'] = 'icons/' . $safe_image_name;
              // $message = 'Imaginea incarcata deja exista pe server';
            } else {
              move_uploaded_file($image_tmp_name, $file_deposit_path);
              $_POST['image_upload'] = 'icons/' . $safe_image_name;
            }
          }
        }
      }
      $this->form_validation->set_rules('image_upload', 'Imagine companie', 'required', array(
        'required' => $message
      ));
    }
    if ($this->form_validation->run() == FALSE) {
      print_r($this->form_validation->error_string());
      print_r($city);
      die;
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->saveMessagesInSession();
      $this->data['city'] = $city;
      return $this->theme->view('backend/trip/city', $this->data);
    }
    if(!$data['id']){
      $data['time_created'] = date('Y-m-d H:i:s');
    } else {
      $data['time_modified'] = date('Y-m-d H:i:s');
    }
    $id = $this->Trip_cities_model->saveCity($data);
    if($task === 'apply'){
      $this->redirect('backend/trip/cities/edit?id=' . $id, 'Informatiile companiei au fost salvate', 'success');
    }
    $this->redirect('backend/trip/cities', 'Informatiile companiei au fost salvate', 'success');
  }
}