<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Layouts extends MX_Controller {
  public $layout_path;
  function __construct() {
    parent :: __construct();
    $this->layout_path = $this->theme->theme_path . 'layouts/frontend/';
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-cms-layouts-access')){
      redirect('backend');
    }
    $this->theme->view('backend/cms/layouts', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Invalid access');
    }
    if(!$this->user->can('backend-cms-layouts-access')){
      $this->outputError('Invalid access');
    }
    $filters = array(
      'folder' => $this->layout_path
    );
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-cms-layouts-access');
    $user_can['view'] = $user_can['access'] && $this->user->can('backend-cms-layouts-view');
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-cms-layouts-edit');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-cms-layouts-delete');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    
    $this->load->model('CMS_Layouts_model');
    $this->data['total_layouts'] = $this->CMS_Layouts_model->getTotalLayouts($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_layouts'] / $filters['limit']) : 1;
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
    $layouts = $this->CMS_Layouts_model->getLayouts($filters);
    foreach($layouts as $k=>$layout){
      if($user_can['view']){
        $layout->can_view = true;
        $layout->view_link = site_url('backend/cms/layouts/view?slug=' . $layout->slug);
      }
      if($user_can['edit']){
        $layout->can_edit = true;
        $layout->edit_link = site_url('backend/cms/layouts/edit?slug=' . $layout->slug);
      }
      // if($layout->slug != 'default'){
        // if($user_can['delete']){
          // $layout->can_delete = true;
          // $layout->delete_link = site_url('backend/cms/layouts/delete?slug=' . $layout->slug);
        // }
      // }
    }
    $this->data['layouts'] = $layouts;
    $this->data['page'] = $current_page;
    
    $session_data = array();
    $session_data['page'] = $current_page;
    $session_data['ordering'] = $ordering;
    $session_data['search'] = $search;
    $session_data['limit'] = $limit;
    $this->session->set_userdata('backend/cms/layouts/list', $session_data);
    $this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-cms-layouts-access', 'backend-cms-layouts-add')){
      redirect('backend');
    }
    $this->load->library('CmsLayout');
    $this->data['layout'] = new CmsLayout;
    $this->theme->view('backend/cms/layout', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-cms-layouts-access', 'backend-cms-layouts-edit')){
      redirect('backend');
    }
    $slug = strtolower(trim($this->input->get('slug')));
    if(!$slug){
      redirect('backend/cms/layouts');
    }
    $this->load->model('CMS_Layouts_model');
    if(!is_dir($this->layout_path . $slug)){
      redirect('backend/cms/layouts');
    }
    $layout = $this->CMS_Layouts_model->getLayoutObj($slug, $this->layout_path);
    $this->data['layout'] = $layout;
    $this->theme->view('backend/cms/layout', $this->data);
  }
  
  public function view() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-cms-layouts-access', 'backend-cms-layouts-view')){
      redirect('backend');
    }
    $slug = strtolower(trim($this->input->get('slug')));
    if(!$slug){
      redirect('backend/cms/layouts/add');
    }
    $this->load->model('CMS_Layouts_model');
    
    $layout = $this->CMS_Layouts_model->getLayoutObj($slug, $this->layout_path);
    if(!$layout){
      redirect('backend/cms/layouts');
    }
    $this->data['layout'] = $layout;
    $this->theme->view('backend/cms/layout', $this->data);
  }
  
  public function delete() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-cms-layouts-access', 'backend-cms-layouts-delete')){
      redirect('backend');
    }
    $slug = strtolower(trim($this->input->get('slug')));
    if(!$slug){
      redirect('backend/cms/layouts/add');
    }
    $this->load->model('CMS_Layouts_model');
    
    $layout = $this->CMS_Layouts_model->getLayoutObj($slug, $this->layout_path);
    if(!$layout){
      redirect('backend/cms/layouts');
    }
    $this->CMS_Layouts_model->deleteLayoutBySlug($slug, $this->layout_path);
    redirect('backend/cms/layouts');
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Invalid access');
    }
    if(!$this->user->can('backend-cms-layouts-access')){
      $this->outputError('Invalid access');
    }
    if(!$this->validate()){
      $this->outputError(validation_errors());
    }
    $slug = strtolower(trim($this->input->post('slug')));
    $newslug = strtolower(trim($this->input->post('newslug')));
    if($newslug === ''){
      $this->outputError('Invalid access');
    }
    $data = array();
    $data['slug'] = '';
    $creating = $slug === '' && $newslug !== '';
    $editing = $slug !== '' && $newslug !== '';
    if(!$creating && !$editing){
      $this->outputError('Invalid operation');
    }
    $theslug = $slug;
    if($editing){
      $changed_slug = $newslug !== $slug;
      if($changed_slug){
        $theslug = $newslug;
      }
      if($changed_slug && $slug ==='default'){
        $this->outputError('Cannot change the alias of the default layout');
      }
      if($changed_slug && $newslug ==='default'){
        $this->outputError('Cannot change the alias to default');
      }
      if(!$this->user->can('backend-cms-layouts-edit')){
        $this->outputError('Invalid access');
      }
      if($changed_slug){
        if(!is_writable($this->layout_path)){
          $this->outputError('Layouts folder is not writable');
        }
      } else {
        if(!is_writable($this->layout_path . $theslug)){
          $this->outputError('Layout folder is not writable');
        }
        $info_file_path = $this->layout_path . $slug . '/info.json';
        if(file_exists($info_file_path) && !is_writable($info_file_path)){
          $this->outputError('Layout info is not writable');
        }
      }
      $this->load->model('CMS_Layouts_model');
      if(!is_dir($this->layout_path . $slug)){
        $this->outputError('Layout not found');
      }
      $data = $this->CMS_Layouts_model->getLayout($slug, $this->layout_path);
      if($changed_slug){
        $data['newslug'] = $newslug;
      }
    } else {
      $theslug = $newslug;
      if(!$this->user->can('backend-cms-layouts-add')){
        $this->outputError('Invalid access');
      }
      if(!is_writable($this->layout_path)){
        $this->outputError('Layouts folder is not writable');
      }
      $this->load->model('CMS_Layouts_model');
      if(is_dir($this->layout_path . $newslug)){
        $this->outputError('Layout already exists');
      }
      $data = $this->CMS_Layouts_model->getLayout($newslug, $this->layout_path);
      $data['slug'] = $newslug;
      $data['new'] = true;
      
    }
    
    $this->load->model('CMS_Layouts_model');
    if(in_array($theslug, $this->CMS_Layouts_model->ommit_layout_names)){
      $this->outputError('Invalid alias');
    }
    
    $data['name'] = trim($this->input->post('name'));
    $data['author'] = trim($this->input->post('author'));
    $data['version'] = trim($this->input->post('version'));
    $data['path'] = $this->layout_path;
    
    $slug = $this->CMS_Layouts_model->saveLayout($data);
    $this->data['slug'] = $slug;
    $this->data['edit_link'] = site_url('backend/cms/layouts/edit?slug='. $slug);
    $this->output();
  }
  private function validate($user=null){
    $this->load->library('form_validation');
    $slug = trim($this->input->post('slug'));
    if($slug !== ''){
      $this->form_validation->set_rules('slug', 'Alias', 'trim|required|regex_match[/^[a-z0-9_]+$/i]');
    }
    $this->form_validation->set_rules('newslug', 'Alias', 'trim|required|max_length[50]|regex_match[/^[a-z0-9_]+$/i]');
    $this->form_validation->set_rules('name', 'Nume', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('author', 'Autor', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('version', 'Versiune', 'trim|required|max_length[255]');
    return $this->form_validation->run();
  }
}