<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Orders extends MX_Controller {
  public function index() {
    if(!$this->user->id){
      $this->redirect('','Acces restrictionat','error');
    }
    $order_id = $this->input->get('id');
    if(!$order_id){
      return $this->items();
    }
    return $this->item();
  }
  public function items() {
    if(!$this->user->id){
      $this->redirect('','Acces restrictionat','error');
    }
    return $this->theme->view('account/trip/orders', $this->data);
  }
  public function item() {
    if(!$this->user->id){
      $this->redirect('','Acces restrictionat','error');
    }
    $order_id = $this->input->get('id');
    if(!$order_id){
      $this->redirect('account/trip/orders', 'ID comanda nespecificat', 'error');
    }
    $this->load->model('TripOrder_model');
    $filters = array();
    $filters['created_by'] = $this->user->id;
    $filters['status'] = array(1,2,3,4,-1);
    $order = $this->TripOrder_model->getOrderById($order_id, $filters);
    if(!$order){
      $this->redirect('account/trip/orders', 'Comanda nu a fost gasita', 'error');
      return false;
    }
    if(!$order->trip_order_id){
      $this->redirect('account/trip/orders', 'Comanda nu a fost trimisa catre DCS.', 'error');
      return false;
    }
    $trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
    if(!$trip_order){
      $this->redirect('account/trip/orders', 'Comanda DCS nu a putut fi preluata.', 'error');
      return false;
    }
    $order->trip_order = $trip_order;
    if(empty($order->trip_order->Owner)){
      $this->redirect('account/trip/orders', 'Lipsesc informatiile utilizatorului', 'error');
      return false;
    }
    $service_types = array();
    foreach($order->trip_order->Services as $service){
      $service_types[] = $service->Type;
    }
    $total_service_types = count($service_types);
    $service_type = 'custom';
    if($total_service_types == 1){
      $service_type = $service_types[0];
    } elseif($total_service_types == 2){
      if(in_array('hotel', $service_types) && in_array('flight', $service_types) && $order->type == 'citybreak'){
        $service_type = 'citybreak';
      }
    }
    $this->data['order'] = $order;
    $this->data['service_type'] = $service_type;
    // order data
    return $this->theme->view('account/trip/order', $this->data);
  }
  public function download() {
    $order_id = (int)$this->input->get('id');
    if(!$order_id || $order_id<0){
      $this->redirect('account/trip/orders', 'ID comanda nespecificat', 'error');
    }
    $filters = array();
    $filters['created_by'] = $this->user->id;
    $filters['status'] = array(2);
    $this->load->model('TripOrder_model');
    $order = $this->TripOrder_model->getOrderById($order_id, $filters);
    if(!$order){
      $this->redirect('account/trip/orders', 'Comanda nu a fost gasita', 'error');
      return false;
    }
    if(!$order->trip_order_id){
      $this->redirect('account/trip/orders', 'Comanda nu a fost trimisa catre DCS.', 'error');
      return false;
    }
    $trip_order = $this->TripOrder_model->getTripOrder($order->trip_order_id);
    if(!$trip_order){
      $this->redirect('account/trip/orders', 'Comanda DCS nu a putut fi preluata.', 'error');
      return false;
    }
    $order->trip_order = $trip_order;
    
    $tmp_path = config_item('tmp_path');
    
    $zip = new ZipArchive();
    $file_name = "Vouchere comanda " . $order_id .".zip";
    $file_path = $tmp_path . '/' .$file_name;
    $zip->open($file_path,  ZipArchive::CREATE);
    
    $service_file = null;
    $no_documents = 0;
    foreach($order->trip_order->Services as $service){
      $service_id = $service->Id;
      $service_type = $service->Type;
      $documents_response = $this->TripOrder_model->getDocuments($order->trip_order->Id, $service_id);
      if(!$documents_response){
        $this->redirect('account/trip/orders?id=' . $order_id, 'Documentul nu a putut fi preluat.', 'error');
        return false;
      }
      foreach($documents_response->_embedded->documents as $document){
        $no_documents++;
        if(isset($service_file)){
          unlink($service_file);
        }
        $document_id = $document->Id;
        $document_name = $document->Name;
        $document_response = $this->TripOrder_model->downloadDocument($order->trip_order->Id, $service_id, $document_id);
        
        file_put_contents($tmp_path . $document_name,$document_response);
        
        $zip->addFile($tmp_path . $document_name, $service_type . '-' . $document_name);
        $service_file = $tmp_path . $document_name;
      }
    }
    $zip->close();
    if($no_documents > 1){
      $this->outputFile($file_path, $file_name);
      unlink($file_path);
    } elseif($no_documents==1) {
      $this->outputFile($service_file, $service_type . '-' . $document_name);
    }
    if(isset($service_file)){
      unlink($service_file);
    }
    exit;
  }
  protected function outputFile($file, $name, $mime_type=''){
    if(!is_readable($file)) die('File not found or inaccessible!');
    $size = filesize($file);
    $name = rawurldecode($name);
    $known_mime_types=array(
      "htm" => "text/html",
      "exe" => "application/octet-stream",
      "zip" => "application/zip",
      "doc" => "application/msword",
      "jpg" => "image/jpg",
      "php" => "text/plain",
      "xls" => "application/vnd.ms-excel",
      "ppt" => "application/vnd.ms-powerpoint",
      "gif" => "image/gif",
      "pdf" => "application/pdf",
      "txt" => "text/plain",
      "html"=> "text/html",
      "png" => "image/png",
      "jpeg"=> "image/jpg"
    );

    if($mime_type==''){
      $file_extension = strtolower(substr(strrchr($file,"."),1));
      if(array_key_exists($file_extension, $known_mime_types)){
        $mime_type=$known_mime_types[$file_extension];
      } else {
        $mime_type="application/force-download";
      }
    }
    @ob_end_clean();
    if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header("Content-Transfer-Encoding: binary");
    header('Accept-Ranges: bytes');

    if(isset($_SERVER['HTTP_RANGE'])){
      list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
      list($range) = explode(",",$range,2);
      list($range, $range_end) = explode("-", $range);
      $range=intval($range);
      if(!$range_end) {
        $range_end=$size-1;
      } else {
        $range_end=intval($range_end);
      }

      $new_length = $range_end-$range+1;
      header("HTTP/1.1 206 Partial Content");
      header("Content-Length: $new_length");
      header("Content-Range: bytes $range-$range_end/$size");
    }
    else {
      $new_length=$size;
      header("Content-Length: ".$size);
    }

    $chunksize = 1*(1024*1024);
    $bytes_send = 0;
    if ($file = fopen($file, 'r')){
      if(isset($_SERVER['HTTP_RANGE']))
        fseek($file, $range);
      while(!feof($file) && (!connection_aborted()) && ($bytes_send<$new_length) ) {
        $buffer = fread($file, $chunksize);
        echo($buffer);
        flush();
        $bytes_send += strlen($buffer);
      }
     fclose($file);
    }
    else die('Error - can not open file.');
    die();
  }
  public function getlist() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('','Acces invalid','error');
    }
    if(!$this->user->id){
      $this->outputError('Acces restrictionat');
    }
    $filters = array();
    $filters['created_by'] = $this->user->id;
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $filters['status'] = array(1,2,3,4,-1);
    
    
    $this->load->model('TripOrder_model');
    $this->data['total_orders'] = $this->TripOrder_model->getTotalOrders($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<1 || $limit>100){
      $limit = 20;
    }
    $filters['limit'] = $limit;
    // $ordering = trim('' . $this->input->post('ordering'));
    $ordering = 'id DESC';
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_orders'] / $filters['limit']) : 1;
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
    $orders = array();
    if($this->data['total_orders']){
      $orders = $this->TripOrder_model->getOrders($filters);
      foreach($orders as $k=>$order){
        $order->can_view = true;
        $order->view_link = site_url('account/trip/orders?id=' . $order->id);
      }
    }
    $this->data['orders'] = $orders;
    $this->data['page'] = $current_page;
    $this->output();
  }
}