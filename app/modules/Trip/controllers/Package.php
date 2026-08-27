<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Package extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Packages_model');
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    parent::__construct();
  }
  public function index() {
    $package_id = (int)$this->input->get('id');
    if($package_id <= 0){
      $package_id = (int)$this->uri->segment(3);
    }
    
    if (!$cache_check = $this->cache->get('trip/package/' . (int)$package_id . ' /cache_check')){
      setCacheStorage('trip/package/' . (int)$package_id);
      clearExpiredCache('trip/package/' . (int)$package_id, $this->cache);
      $this->cache->save('trip/package/' . (int)$package_id . ' /cache_check', 1, 86400);
    }
    
    $this->data['package_details'] = $this->Packages_model->loadPackageDetails($package_id);
    $this->load->library('image_lib');
    
    $package = &$this->data['package_details'];
    if($package){
      $type = $this->input->get('type');
      if($type == 'offer'){
        $time = time();
        $next_friday = strtotime('next friday', $time);
        $next_sunday = strtotime('next sunday', $next_friday);
        $data = $this->Packages_model->getSearchData($package_id);
        $data['start_date'] = date('Y-m-d', $next_friday);
        $data['end_date'] = date('Y-m-d', $next_sunday);
        $data['nights'] = 2;
        $data['occupancy'] = array(
          array(
            'adt' => 2
          )
        );
        $this->Packages_model->setSearchData($data);
      }
      
      $package->Name = html_entity_decode($package->Name,ENT_QUOTES); 
      $package->Description = str_replace('\n', "\n", html_entity_decode($package->Description,ENT_QUOTES)); 
      $package->Gallery = array(
        $package->Image
      );
      return $this->theme->view('trip/package/index', $this->data, $this);
    }
    return $this->theme->view('trip/package/404', $this->data, $this);
  }
  private function initiateSearch() {
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    $cache_request = array(
      $this->package_search_data['start_date'],
      $this->package_search_data['occupancy'],
      (int)$this->package_search_data['nights'],
    );
    $package_id = (int)$this->package_search_data['package_id'];
    $container_id = $this->package_search_data['container_id'];
    $cache_storage_path = 'trip/package/' . (int)$package_id . '/search/';
    $cache_hash = crc32(json_encode($cache_request));
    $cache_storage_code_path = 'trip/package/' . (int)$package_id . '/code/';
    if ($cached_code = $this->cache->get($cache_storage_path . $cache_hash)){
      if ($cached_search_data = $this->cache->get($cache_storage_code_path . crc32($cached_code) . '/search')){
        // $response = $this->Packages_model->inspectSearchIndex($cached_search_data->index_id);
        // if ($response && !empty($response->code) && $response->code === $cached_code) {
        $this->package_search_data['container_id'] = $cached_search_data->container_id;
        $this->package_search_data['index_id'] = $cached_search_data->index_id;
        $this->package_search_data['code'] = $cached_code;
        return;
        // } else {
          // $this->cache->delete($cache_storage_code_path . crc32($cached_code));
          // $this->cache->delete($cache_storage_path . $cache_hash);
        // }
      } else {
        $this->cache->delete($cache_storage_path . $cache_hash);
      }
    }
    $retries = 10;
    $max_retries = $retries;
    $response = null;
    while($max_retries){
      if($max_retries < $retries){
        sleep(1);
      }
      $response = $this->Packages_model->initiateSearch(array(
        'occupancy' => $this->package_search_data['occupancy'],
        'start_date' => $this->package_search_data['start_date'],
        'end_date' => $this->package_search_data['end_date'],
        'package_id' => $this->package_search_data['package_id'],
        'city_id' => $this->package_search_data['city_id'],
        'nights' => $this->package_search_data['nights'],
        'container_id' => $container_id,
      ));
      $max_retries --;
      if (!$response) {
        $this->addMessage('TRIP error: search initiation returned no response. Retrying.', 'warning');
        continue;
      }
      if (property_exists($response,'Status')) {
        $this->addMessage('TRIP error: response is not interpretable. Retrying.', 'warning');
        $response = null;
        continue;
      }
      if (empty($response->{$container_id})) {
        $this->addMessage('TRIP error: container not found. Retrying.', 'warning');
        $response = null;
        continue;
      }
      break;
    }
    if(!$response){
      $this->outputTripError('TRIP error: could not initiate search.');
    }
    $search_response = $response->{$container_id}[0];
    if(!$search_response->Status){
      $this->outputTripError('The search failed.');
    }
    $index_id = $search_response->Id;
    
    $this->package_search_data['index_id'] = $index_id;
    
    $max_retries = $retries;
    $response = null;
    while($max_retries){
      if($max_retries < $retries){
        sleep(1);
      }
      $max_retries --;
      $response = $this->Packages_model->inspectSearch($container_id);
      if (!$response) {
        $this->addMessage('TRIP error: search inspect returned no response. Retrying.', 'warning');
        continue;
      }
      if (empty($response[0]->Status)) {
        $this->outputError('Nu au fost gasite rezultate.');
        break;
      }
      if($response[0]->Status == 1){
        break;
      }
    }
    if(!$response){
      $this->outputError('The search failed. Too many retries');
    }
    $max_retries = $retries;
    $response = null;
    while($max_retries){
      if($max_retries < $retries){
        sleep(1);
      }
      $max_retries --;
      $response = $this->Packages_model->inspectSearchIndex($index_id);
      if (!$response) {
        $this->addMessage('TRIP error: search index inspect returned no response. Retrying.', 'warning');
        continue;
      }
      if (empty($response->code)) {
        $this->addMessage('TRIP error: code parameter missing in search index result. Retrying.', 'warning');
        $response = null;
        continue;
      }
      break;
    }
    if(!$response){
      $this->outputError('The search failed. Code is missing.');
    }
    $code = $response->code;
    $this->package_search_data['code'] = $code;
    if(strlen($code)){
      $cache_time = strtotime('tomorrow') - time();
      if($cache_time > 0){
        setCacheStorage($cache_storage_code_path . crc32($code));
        $cached_search_data = new stdClass;
        $cached_search_data->container_id = $container_id;
        $cached_search_data->index_id = $index_id;
        $this->cache->save($cache_storage_code_path . crc32($code) . '/search', $cached_search_data, $cache_time);
        
        setCacheStorage($cache_storage_path);
        $this->cache->save($cache_storage_path . $cache_hash, $code, $cache_time);
      }
    }
  }
  private $loadEntriesRetries = 0;
  public function loadEntries() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('');
    }
    $this->package_search_data = $this->input->post();
    $this->package_search_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    $this->Packages_model->setSearchData($this->package_search_data);
    
    $package_id = intval($this->package_search_data['package_id']);
    $code = trim($this->package_search_data['code']);
    if(!strlen($code)){
      $this->initiateSearch();
      $code = $this->package_search_data['code'];
      $this->Packages_model->setSearchData($this->package_search_data);
    }
    $entries = $this->Packages_model->loadPackageEntries($package_id,$code);
    if(!$entries){
      if(!$this->loadEntriesRetries){
        $call = $this->Trip_model->api->call;
        if($call && $call->http_code === 417){
          $this->loadEntriesRetries++;
          $_POST['code'] = null;
          $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
          $cache_storage_path = 'trip/package/' . (int)$package_id . '/code/' . crc32($code) . '/';
          $this->cache->delete($cache_storage_path);
          return $this->loadEntries();
        }
      }
      $this->outputTripError('Nu s-au putut prelua perioadele');
    }
   
    $this->data['entries'] = $entries;
    $this->data['package_search_data'] = $this->package_search_data;
    if($entries->total_items == 1){
      $this->addMessage('A fost gasita o singura perioada', 'success');
    } elseif($entries->total_items > 1){
      $this->addMessage('Au fost gasite ' . $entries->total_items . ' perioade', 'success');
    } else {
      $this->addMessage('Nu au fost gasite perioade', 'warning');
    }
    $this->output();
  }
  
  public function loadEntryDetails() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('');
    }
    $this->package_search_data = $this->input->post();
    $this->package_search_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    $package_id = intval($this->package_search_data['package_id']);
    
    $EntryId = $this->input->post('entry_id');
    if(isset($EntryId)){
      unset($this->package_search_data['entry_id']);
    } else {
      $this->outputError('EntryId not specified');
    }
    $RateGroupId = $this->input->post('rate_group_id');
    if(isset($RateGroupId)){
      unset($this->package_search_data['rate_group_id']);
    } else {
      $this->outputError('RateGroupId not specified');
    }
    
    $code = trim($this->package_search_data['code']);
    if(!strlen($code)){
      $this->initiateSearch();
      $code = $this->package_search_data['code'];
      $this->Packages_model->setSearchData($this->package_search_data);
    }
    $entry_details = $this->Packages_model->loadPackageEntryDetails($package_id,$code, $EntryId, $RateGroupId);
    if(!$entry_details){
      $this->outputTripError('Nu s-au putut prelua detaliile perioadei');
    }
    $this->data['entry_details'] = $entry_details;
    $this->data['package_search_data'] = $this->package_search_data;
    $this->output();
  }
  public function loadEntryDetailsExtra() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('');
    }
    $this->package_search_data = $this->input->post();
    $this->package_search_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    $package_id = intval($this->package_search_data['package_id']);
    
    $EntryId = $this->input->post('entry_id');
    if(isset($EntryId)){
      unset($this->package_search_data['entry_id']);
    } else {
      $this->outputError('EntryId not specified');
    }
    $RateGroupId = $this->input->post('rate_group_id');
    if(isset($RateGroupId)){
      unset($this->package_search_data['rate_group_id']);
    } else {
      $this->outputError('RateGroupId not specified');
    }
    $code = trim($this->package_search_data['code']);
    if(!strlen($code)){
      $this->initiateSearch();
      $code = $this->package_search_data['code'];
      $this->Packages_model->setSearchData($this->package_search_data);
    }
    $entry_details_extra = $this->Packages_model->loadPackageEntryDetailsExtra($package_id,$code, $EntryId, $RateGroupId);
    if(!$entry_details_extra){
      $this->outputTripError('Nu s-au putut prelua serviciile extra');
    }
    $this->data['entry_details_extra'] = $entry_details_extra;
    $this->data['package_search_data'] = $this->package_search_data;
    $this->output();
  }
  protected function validateBooking() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('');
    }
    $package_id = (int)$this->input->post('package_id');
    $code = '' . $this->input->post('code');
    $EntryId = (int)$this->input->post('entry_id');
    $RateGroupId = (int)$this->input->post('rate_group_id');
    $occupations = (array)$this->input->post('occupations');
    $extra_services = (array)$this->input->post('extra-services');
    $this->data['post'] = $this->input->post();
    
    $package_availability = $this->Packages_model->checkPackageAvailability($package_id,$code, $EntryId, $RateGroupId, $occupations, $extra_services);
    if(!$package_availability){
      $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
      $cache_storage_path = 'trip/package/' . (int)$package_id . '/code/' . crc32($code) . '/';
      $this->cache->delete($cache_storage_path);
      $this->outputTripError('Vacanta nu a putut fi validata. Va rugam sa reincarcati pagina.');
    }
    $entry_details = $this->Packages_model->loadPackageEntryDetails($package_id,$code, $EntryId, $RateGroupId);
    if(!$entry_details){
      $this->redirect('trip/package/' . $package_id, $this->getTripError('Vacanta nu a putut fi validata'),'error');
    }
    
    $loaded = false; 
    $this->load->model('Options_model');
    
    $block_payments = false;
    $today = new DateTime();
    $because_weekend = false;
    // sambata && duminica
    if($today->format('N') >= 6){
      $block_payments = true;
      $because_weekend = true;
    }
    // ore nelucratoare
    $because_no_working_hours = false;
    // if((int)$today->format('H') < 6 || (int)$today->format('H') >= 18){
      // $block_payments = true;
      // $because_no_working_hours = true;
    // }
    $start_date = $entry_details->StartDate;
    $date_start_date = DateTime::createFromFormat('Y-m-d', $start_date);

    $days_till_start = $today->diff($date_start_date);
    $days_till_start_formatted = intval($days_till_start->format('%a'));
    $because_too_early = false;
    // checkin azi sau maine
    if($days_till_start_formatted < 2){
      $block_payments = true;
      $because_too_early = true;
    }
    $block_online = false;
    $because_on_request = false;
    if($package_availability->Status == 'RQ'){
      $block_online = true;
      $because_on_request = true;
    }
    $because_of_cancellation_policy = false;
    $cancellation_policies = $package_availability->CancelationPolicies;
    if($cancellation_policies){
      $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
      foreach($cancellation_policies as $cancellation_policy){
        $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:s", $cancellation_policy->StartDate);
        if($min_cancellation_date_for_block > $cancellation_date){
          $block_payments = true;
          $because_of_cancellation_policy = true;
        }
        break;
      }
    }
    $allowed_statuses = array(1,-2);
    if($this->user->can('backend-config-save')){
      $allowed_statuses[] = -1;
    }
    $this->data['payment_methods'] = array();
    if(!$block_payments){
      $agency_status = $this->Options_model->get('payment_methods_status','agency');
      if(in_array($agency_status,$allowed_statuses)){
        $this->data['payment_methods'][] = 'agency';
      }
      $bank_status = (int)$this->Options_model->get('payment_methods_status','bank');
      if(in_array($bank_status,$allowed_statuses)){
        $this->data['payment_methods'][] = 'bank';
      }
    }
    if(!$block_online){
      $online_status = (int)$this->Options_model->get('payment_methods_status','online');
      if(in_array($online_status,$allowed_statuses)){
        $this->db->where_in('option_value',$allowed_statuses);
        $static_settings = $this->Options_model->getKeys('payment_gateways_status');
        if($static_settings){
          $this->data['payment_methods'][] = 'online';
          $this->data['online_payment_gateways'] = $static_settings;
        }
      }
    }
    $this->data['package_availability'] = $package_availability;
    $motive = '';
    if($block_payments){ 
      $motive .= '<p>';
      $motive .= 'Nu se poate plati <b>direct la agentie</b> sau prin <b>transfer bancar</b> deoarece ';
      if($because_of_cancellation_policy){
        $motive .= 'data minima de anulare este inaintea datei <b>' . $min_cancellation_date_for_block->format('d.m.Y h:i:s A') . '</b>';
      } elseif($because_too_early){
        $motive .= 'pentru rezervari cu data de checkin astazi sau maine se poate plati doar online.';
      } elseif($because_weekend){
        $motive .= 'pentru rezervari efectuate in weekend se poate plati doar online.';
      } elseif($because_no_working_hours){
        $motive .= 'pentru rezervari efectuate in intervalul orar 18:00 - 06:00 se poate plati doar online.';
      }
      $motive .= '</p>';
    }
    if($block_online){
      $motive .= '<p>';
      $motive .= 'Nu se poate plati <b>online</b> deoarece ';
      if($because_on_request){
        $motive .= 'camerele au disponibilitate: <b>La cerere</b>';
      }
      $motive .= '</p>';
    }
    $this->load->model('Options_model');
    $this->general_settings = $this->Options_model->get('general_settings');
	$buton = '';
    if(isset($this->general_settings['contact_phone_number']) && strlen($this->general_settings['contact_phone_number'])) {
      $buton = '<div class="w-100 text-center mt-4 mb-4"><a href="tel:' . $this->general_settings['contact_phone_number'] . '" class="btn btn-primary"><i class="fa fa-phone"></i> Suna pentru suport la <br>' . (isset($this->general_settings['contact_phone_text']) ? $this->general_settings['contact_phone_text'] : $this->general_settings['contact_phone_number']) . '!</a></div>';
    }
	$motive .= $buton;
    if(!$this->data['payment_methods']){
	  if($because_on_request){
		$this->addMessage('<h4 class="request-offer"><i class="fa fa-warning"></i> Aceasta oferta este disponibila la cerere, nu se poate rezerva online in acest moment.</h4><p>Apasati butonul "SOLICITA OFERTA" si veti primi in cel mai scurt timp un mesaj din partea consultantilor Accent Travel & Events cu privire la disponibilitatile pentru aceasta oferta sau cea mai buna oferta similara.</p>
		<p>Va multumim pentru intelegere.</p>', 'success custom');
      } else {
		$this->addMessage('<h4><i class="fa fa-warning text-danger"></i> Din pacate platforma nu dispune de metode de plata potrivite acestei cereri.</h4>' . $motive, 'warning');
	  }
      $this->output('warning');
    } else {
      $this->addMessage('Validat cu succes');
    }
    $this->output();
  }
  public function booking() {
	$this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'package'));
	
    $package_id = (int)$this->input->post('package_id');
    $this->package_search_data = $this->Packages_model->getSearchData($package_id);
    if ($this->input->is_ajax_request()) {
      return $this->validateBooking();
    }
    $code = '' . $this->input->post('code');
    $entry_id = (int)$this->input->post('entry_id');
    $rate_group_id = (int)$this->input->post('rate_group_id');
    $occupations = (array)$this->input->post('occupations');
    $selected_extra_services = (array)$this->input->post('extra-services');
    $package_availability = $this->Packages_model->checkPackageAvailability($package_id,$code, $entry_id, $rate_group_id, $occupations, $selected_extra_services);
    
    if(!$package_availability){
      $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
      $cache_storage_path = 'trip/package/' . (int)$package_id . '/code/' . crc32($code) . '/';
      $this->cache->delete($cache_storage_path);
      $this->redirect('trip/package/' . $package_id, $this->getTripError('Vacanta nu a putut fi validata'),'error');
    }
    $entry_details = $this->Packages_model->loadPackageEntryDetails($package_id,$code, $entry_id, $rate_group_id);
    if(!$entry_details){
      $this->redirect('trip/package/' . $package_id, $this->getTripError('Vacanta nu a putut fi validata'),'error');
    }
    $entry_details_extra = $this->Packages_model->loadPackageEntryDetailsExtra($package_id,$code, $entry_id, $rate_group_id);
    if(!$entry_details_extra){
      $this->redirect('trip/package/' . $package_id, $this->getTripError('Nu s-au putut prelua serviciile extra'),'error');
    }
    $this->data['entry_details_extra'] = $entry_details_extra->_embedded->extra_services;
    $this->data['package_id'] = $package_id;
    $this->data['code'] = $code;
    $this->data['entry_id'] = $entry_id;
    $this->data['rate_group_id'] = $rate_group_id;
    $this->data['occupations'] = $occupations;
    $this->data['selected_extra_services'] = $selected_extra_services;
    $this->data['package_details'] = $this->Packages_model->loadPackageDetails($package_id);
    $this->data['package_availability'] = $package_availability;
    $this->data['entry_details'] = $entry_details;
    $this->theme->view('trip/package/booking', $this->data, $this);
  }
  public function checkout() {
	$this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'package'));
	
    $this->makeResponseGlobal();
    if ($this->input->is_ajax_request()) {
      $this->load->library('form_validation');
      $response = modules :: run('Trip/checkout/Checkout/validate', 'package');
      if(!$response){
        $this->output('error');
      }
      if (false === $this->form_validation->run()) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      $response = modules :: run('Trip/checkout/Checkout/service', 'package', false);
      if(!$response){
        $this->output('error');
      }
      $this->addMessage('Serviciul a fost validat.');
      return $this->output();
    }
    $status = modules :: run('Trip/checkout/Checkout/service', 'package', true);
    if(false === $status){
      $this->saveMessagesInSession();
      $this->redirect('trip/checkout/failure');
    }
    if(true === $status){
      $this->redirect('trip/checkout/success');
    }
  }
  protected $response = null;
  protected $results = array();
  protected function output($status = 'success') {
    $response = array(
      'status' => $status,
      'response' => $this->response,
      'calls' => $this->Packages_model->api->calls,
      'results' => $this->results,
      'message' => $this->message,
      'message_type' => $this->message_type,
      'messages' => $this->messages,
      'data' => $this->data,
    );

    echo json_encode($response);
    die;
  }
}