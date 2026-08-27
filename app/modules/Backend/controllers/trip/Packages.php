<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Packages extends MX_Controller {
  function __construct() {
    parent::__construct();
  }
  public function loadAidaPackages() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    $q = '' . $this->input->post('search');
    $page = (int)$this->input->post('page');
    if($page < 1){
      $page = 1;
    }
    $limit = (int)$this->input->post('limit');
    if($limit < 1 || $limit > 1000){
      $limit = 10;
    }
    $this->load->model('Trip/Packages_model');
    $filter = array();
    $filter[] = array(
      'name' => 'Name',
      'type' => 'like',
      'term' => '%' . $q . '%',
    );
    $packages_response = $this->Packages_model->loadPackageResults('', $page, $filter, $limit);
    if(!$packages_response){
      $this->outputTripError('Nu s-au putut prelua vacantele');
    }
    $this->data = $packages_response;
    $this->output();
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('trip_packages_settings');
    if(!$settings){
      $settings = array();
    }
    if(!isset($settings['categories'])){
      $settings['categories'] = '';
    }
    if(!isset($settings['destinations'])){
      $settings['destinations'] = '';
    }
    $this->data = $settings;
    $this->theme->view('backend/trip/packages_settings', $this->data);
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    $post_data = $this->input->post('data');
    $data = array(
      'categories' => null,
      'destinations' => null,
    );
    if($post_data){
      foreach($post_data as $k=>$str){
        $data[$k] = implode(',', array_unique(explode(',', trim($str,','))));
      }
    }
    $this->load->model('Options_model');
    $this->Options_model->set('trip_packages_settings',$data);
    $this->redirect('backend/trip/packages', 'Informatiile au fost salvate', 'success');
  }
  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $this->load->model('Trip/Packages_model');
      $package_id = (int)$this->input->post('package_id');
      $this->data = $this->Packages_model->getSearchData($package_id, '/backend/order');
      
      $this->load->library('form_validation');
      $this->form_validation->set_rules('start_date', 'Checkin', 'trim|required|max_length[10]|valid_date[d.m.Y]',array(
        'valid_date' => 'Formatul datei este invalid',
      ));
      $this->form_validation->set_rules('end_date', 'Checkout', 'trim|required|max_length[10]|valid_date[d.m.Y]',array(
        'valid_date' => 'Formatul datei este invalid',
      ));
      // $this->form_validation->set_rules('hotel_name', 'Package', 'trim|max_length[255]');
      $this->form_validation->set_rules('city_name', 'Oras', 'trim|max_length[255]');
      $this->form_validation->set_rules('city_id', 'Oras', 'trim|greater_than_equal_to[0]');
      $this->form_validation->set_rules('country_id', 'Tara', 'trim|greater_than_equal_to[0]');
      $this->form_validation->set_rules('country_name', 'Tara', 'trim|max_length[255]');
      
      $reference_date = false;
      $end_date = $this->input->post('end_date');
      if(isset($end_date)){
        $date_reference_date = DateTime::createFromFormat('d.m.Y', trim($end_date));
        if ($date_reference_date && $date_reference_date->format('d.m.Y') == $end_date) {
          $reference_date = $date_reference_date;
        }
      }
      
      $occupancy = $this->input->post('occupancy');
      $room_occupancy = array();
      if(isset($occupancy) && is_array($occupancy)){
        $expected_room_index = 0;
        foreach($occupancy as $room_index => $room){
          $room_nr = $room_index+1;
          if($room_index !== $expected_room_index){
            $this->outputError('Index camera invalid la camera #' . $room_nr);
          }
          $expected_room_index++;
          if(!is_array($room)){
            $this->outputError('Informatii invalide la camera #' . $room_nr);
          }
          if(!isset($room['adt']) || !is_numeric($room['adt']) || $room['adt']<1 || $room['adt'] > 1000 || ('' . (int)$room['adt'] !== '' . $room['adt'])){
            $this->outputError('Numar invalid de adulti la camera #' . $room_nr);
          }
          $room_occupancy[$room_index] = array(
            'adt' => (int)$room['adt']
          );
          if(isset($room['chd'])){
            $room_occupancy[$room_index]['chd']=array(
              'age'=>array(),
              'birth_date'=>array(),
            );
            if(!isset($room['chd']['age']) || !is_array($room['chd']['age']) || empty($room['chd']['age']) || count($room['chd']['age']) > 1000){
              $this->outputError('Valori invalide pentru copii la camera #' . $room_nr);
            }
            if(!isset($room['chd']['birth_date']) || !is_array($room['chd']['birth_date']) || empty($room['chd']['birth_date']) || count($room['chd']['birth_date']) > 1000){
              $this->outputError('Valori invalide pentru date de nastere copii la camera #' . $room_nr);
            }
            
            $expected_child_age_index = 0;
            foreach($room['chd']['age'] as $child_age_index => $child_age){
              $child_index = $child_age_index + 1;
              if($child_age_index !== $expected_child_age_index){
                $this->outputError('Index invalid copil la camera #' . $room_nr . ' copil #' . $child_index);
              }
              if(false !== $reference_date){
                $birth_date = isset($room['chd']['birth_date'][$child_age_index]) ? $room['chd']['birth_date'][$child_age_index] : false;
                if($birth_date){
                  $date_birth_date = DateTime::createFromFormat('d.m.Y', trim($birth_date));
                  if (!($date_birth_date && $date_birth_date->format('d.m.Y') == $birth_date)) {
                    $this->outputError('Data nastere invalida pentru copil la camera #' . $room_nr . ' copil #' . $child_index);
                  }
                  $years = $reference_date->diff($date_birth_date)->format('%y');
                  if($years != $child_age){
                    $this->outputError('Varsta invalida la camera #' . $room_nr . ' copil #' . $child_index);
                  }
                }
              }
              $expected_child_age_index++;
              if($child_age < 0 || $child_age > 17 || ('' . (int)$child_age !== '' . $child_age)){
                $this->outputError('Varsta invalida la camera #' . $room_nr . ' copil #' . $child_index);
              }
              $room_occupancy[$room_index]['chd']['age'][$child_age_index] = $child_age + 1;
              if(false !== $reference_date){
                $room_occupancy[$room_index]['chd']['birth_date'][$child_age_index] = $birth_date;
              }
            }
          }
        }
      } else {
        $this->outputError('Nu ati introdus camere');
      }
      
      if($this->form_validation->run() == FALSE){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      
      
      $start_date = trim($this->input->post('start_date'));
      $end_date = trim($this->input->post('end_date'));

      $this->data['start_date'] = DateTime::createFromFormat('d.m.Y', trim($this->input->post('start_date')))->format('Y-m-d');
      $this->data['end_date'] = DateTime::createFromFormat('d.m.Y', trim($this->input->post('end_date')))->format('Y-m-d');

      // $this->data['hotel_name'] = trim($this->input->post('hotel_name'));
      $this->data['package_id'] = $this->input->post('package_id');
      $this->data['city_name'] = trim($this->input->post('city_name'));
      $this->data['country_name'] = trim($this->input->post('country_name'));
      $this->data['country_id'] = (int)$this->input->post('country_id');
      $this->data['city_id']= (int)$this->input->post('city_id');
      
      $this->data['occupancy'] = $room_occupancy;
      // $this->data['ignore_session'] = 1;
      $this->data['session'] = '/backend/order';
      
      $this->Packages_model->setSearchData($this->data);
      if ($return) {
        return;
      }
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
}