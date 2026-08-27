<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Page extends MX_Controller {
  public function index() {
    $page_id = (int)$this->input->get('id');
    $args = func_get_args();
    if($args){
      if(count($args) > 1){
        $page_route = implode('/', $args);
      } else {
        $page_slug = $args[0];
      }
    }
    if(!isset($page_slug)){
      $page_slug =$this->input->get('slug');
    }
    if(!isset($page_route)){
      $page_route =$this->input->get('route');
    }
    
    $lang = 'ro';
    $this->load->model('CMS_Pages_model');
    $filters = array();

    if(isset($page_route)){
      $filters['route'] = '' . $page_route;
    }elseif(isset($page_slug)){
      $filters['slug'] = '' . $page_slug;
    } else {
      $filters['page_id'] = $page_id;
    }
    $filters['join_content'] = true;
    $filters['limit'] = 1;
    $filters['status'] = 1;
    $filters['ordering'] = '(pc.language="' . $lang . '") DESC';
    $filters['return_row'] = true;
    $page = $this->CMS_Pages_model->getPages($filters);
	
	if(!empty($page->images)){
		$images = array_filter($page->images, function($image){
			if(!empty($image['hide'])) return false;
			return true;
		});
		$allimages = [];
		foreach($images as $image=>$image_details){
			$image_details['src'] = $image;
			$allimages[] = $image_details;
		}
		$page->images = $allimages;
	}
    
    if($page){
      Modules::$page = &$page;
      $this->data['page'] = $page;
      if ($this->input->is_ajax_request()) {
        $this->output();
      }
      if(strlen(trim($page->layout))){
        $layout = trim($page->layout);
        if(strpos($layout,'/') === false){
          $layout .= '/index';
        }
        $layout = 'frontend/' . $layout;
        $this->theme->set_sublayout($layout);
      }
      
      $route = $page->route;
      if($route && strpos($route,'cms/page')!==0){
        CI::$APP->data = $this->data;
        $ran = Modules :: run($route);
        return;
      }
      $this->theme->view('cms/page', $this->data, $this);
      return;
    }
    if ($this->input->is_ajax_request()) {
      $this->outputError('Pagina nu a fost gasita');
    }
    $this->theme->view('404', $this->data, $this);
  }
  public function not_found(){
		/* if(filter_var(
			$this->Fault_model->getIp(), 
			FILTER_VALIDATE_IP, 
			FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
		)){
			$this->Fault_model->insertFault(['page404' => 1]);
		} */
    return $this->index('cms','page','not_found');
  }
}