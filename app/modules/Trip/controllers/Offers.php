<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Offers extends MX_Controller {
  public function index() {
    $this->redirect('trip/offers/weekend');
  }
  public function weekendApplyFilters() {
    $time = time();
    $yesterday = strtotime('-1 month', $time);
    // $this->db->where('`time_modified` >= "' . date('Y-m-d H:i:s', $yesterday) . '"');
    
    $search = $this->input->post('search');
    if(isset($search)){
      $search = trim($search);
      if(strlen($search)){
        $this->db->like('CONCAT_WS(" ",`city_name`, `name`, `category`)',$search);
      }
    }
    
    $zone = $this->input->post('zone');
    if(isset($zone)){
      $zone = preg_replace("/[^A-Z_]/", '', $zone);
      if(strlen($zone)){
        if(strpos($zone,'_') === false){
          $this->db->where("`zone` LIKE '" . $zone . "\_%'");
        } else {
          $this->db->where("`zone` = '" . $zone . "'");
        }
      }
    }
    $this->db->where('LENGTH(data)>0');
  }
  public function weekend() {
    if ($this->input->is_ajax_request()) {
      
      $sort_by = $this->input->post('sort_by');
      $sort_order = $this->input->post('sort_order');
      $page = $this->input->post('page');
      
      if(!$page || $page<1){
        $page = 1;
      }
      
      if(!is_null($sort_by)){
        $sort_by = strtolower(trim($sort_by));
        if(!in_array($sort_by, array('price','stars','category','name'))){
          $sort_by = null;
        }
      }
      
      if(is_null($sort_by)){
        $sort_by = 'price';
      }
      if(is_null($sort_order)){
        $sort_order = 'ASC';
      }
      
      $this->weekendApplyFilters();
      
      $this->load->model('Trip/Offer_weekend_model');
      $total_offers = $this->Offer_weekend_model->getTotalOffers();
      $this->weekendApplyFilters();
      $this->db->order_by("SUBSTRING_INDEX(`time_modified`, ' ', 1) DESC");
      $this->db->order_by($sort_by, $sort_order);
      $limit = null;
      $offers = $this->Offer_weekend_model->getOffers(array(
        'limit'=>$limit,
        'page'=>$page,
      ));
      $placeholder_image = $this->theme->theme_url . 'assets/images/placeholder.png';
      foreach($offers as $offer){
        $offer_data = unserialize($offer->data);
        $offer->image = $placeholder_image;
        if($offer_data->Image){
          $offer->image = $offer_data->Image;
        }
        $offer->price = !empty($offer_data->MinPrice) ? $offer_data->MinPrice : null;
        $offer->currency = $offer_data->Currency;
        if($offer->type == 'hotel'){
          $offer->address =  str_replace('\n', "\n",html_entity_decode($offer_data->Address,ENT_QUOTES));
          $offer->link = site_url('trip/hotel/' . $offer->type_id . '?type=offer');
        } elseif($offer->type == 'package'){
          $offer->link = site_url('trip/package/' . $offer->type_id . '?type=offer');
        }
        unset($offer->data);
      }
      
      $this->data['total_offers'] = $total_offers;
      $this->data['search'] = $this->input->post('search');
      $this->data['zone'] = $this->input->post('zone');
      $this->data['page'] = $page;
      $this->data['limit'] = $limit;
      $this->data['sort_by'] = $sort_by;
      $this->data['sort_order'] = $sort_order;
      $this->data['offers'] = $offers;
      if(!$total_offers){
        $this->addMessage('Nu au fost gasite oferte','warning');
      } elseif($total_offers==1){
        $this->addMessage('A fost gasita o singura oferta' );
      } else {
        $this->addMessage('Au fost gasite ' . $total_offers . ' oferte' );
      }
      $this->output();
      return;
    }
    $zone = $this->input->get('zone');
    if(!$zone){
      $zone = $this->uri->segment(3);
    }
    $search = $this->input->get('search');
    $this->data['zone'] = trim($zone);
    $this->data['search'] = trim($search);
    $this->theme->view('trip/offers/weekend', $this->data, $this);
  }
}