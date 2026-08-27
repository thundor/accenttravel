<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Requestoffer extends MX_Controller {
  public function view() {
    $sc = $this->input->get('s_c');
    
    $this->db->where('status', 1);
    $this->db->where('code', $sc);
    
    $q = $this->db->get('trip_request_offer', 1, 0);
    
    $row = $q->row();
    
    $active = $row->status > 0;
    
    if($row->type == 'hotel'){
      $data_decoded = json_decode($row->data_hotel, true);
      $this->load->model('Trip/Hotels_model');
      $data_decoded['hotel_id'] = (int)$row->hotel_id;
      $this->Hotels_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/hotel/' . $row->hotel_id));
    } elseif($row->type == 'citybreak'){
      $hotel_data_decoded = json_decode($row->data_hotel, true);
      $flight_data_decoded = json_decode($row->data_flight, true);
      $data_decoded = $hotel_data_decoded + $flight_data_decoded;
      $this->load->model('Trip/Citybreaks_model');
      $data_decoded['hotel_id'] = (int)$row->hotel_id;
      $origin_location_id = isset($data_decoded['origin_location_id']) ? $data_decoded['origin_location_id'] : 0;
      $data_decoded['origin_full_location_name'] = ($origin_location_id > 0 ? $data_decoded['origin_location_name'] . ', ' : '') . $data_decoded['origin_city_name'];
      $destination_location_id = isset($data_decoded['destination_location_id']) ? $data_decoded['destination_location_id'] : 0;
      $data_decoded['destination_full_location_name'] = ($destination_location_id > 0 ? $data_decoded['destination_location_name'] . ', ' : '') . $data_decoded['destination_city_name'];
      $this->Citybreaks_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/citybreak/' . $row->hotel_id));
    } elseif($row->type == 'package'){
      $data_decoded = json_decode($row->data_package, true);
      $this->load->model('Trip/Packages_model');
      $data_decoded['package_id'] = (int)$row->package_id;
      
      $end_date = new DateTime($data_decoded['start_date']);
      $end_date = $end_date->modify('+1 years');
      
      $data_decoded['end_date'] = $end_date->format('Y-m-d');
      
      $this->Packages_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/package/' . $row->package_id));
    } elseif($row->type == 'flight'){
      $data_decoded = json_decode($row->data_flight, true);
      $this->load->model('Trip/Flights_model');
      $this->Flights_model->setSearchData($data_decoded);
      $this->redirect(site_url('trip/flights/search'));
    }
    die('Unable to perform action. Invalid request offer.');
  }
}