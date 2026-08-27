<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Pages extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/cms/pages', $this->data);
  }
  public function parseDate() {
		if(!$this->user->can('backend-access')){
      $this->outputError('Invalid access');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->outputError('Invalid access');
    }
		$dates = $this->input->get('dates');
    if(!$dates){
      $this->outputError('Dates not specified');
    }
		foreach($dates as $param=>$date){
      $this->data[$param] = array();
      if($param != 'edate'){
        $d1 = new DateTime('today midnight');
        try{
          $d2 = new DateTime($date);
        } catch(Exception $e){
          $d2 = $d1;
        }
        $d = $d2->format('Y-m-d');
        $this->data[$param][$d] = $d . ' (Data exacta)';
        if($d1 > $d2){
          $this->addError('Data de checkin/plecare este in trecut');
          continue;
        }

        $datediff = $d2->diff($d1);
        $day_diff = intval($datediff->format("%a"));
        
        $dowMap = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $dowMapRo = array('Duminica', 'Lunea', 'Martea', 'Miercurea', 'Joia', 'Vinerea', 'Sambata');
        
        $dow_numeric = $d2->format('w');
        if(!$day_diff){
          $this->data[$param]['today'] = 'Ziua curenta (Astazi)';
        }
        if($day_diff == 1){
          $this->data[$param][$day_diff . 'days'] = 'Peste o zi (Maine)';
        }
        if($day_diff > 1){
          $this->data[$param][$day_diff . 'days'] = 'Peste ' . $day_diff . ' zile';
        }
        /* 
        $weeks = intval($day_diff / 7);
        if($day_diff >=7 && ($day_diff % 7 === 0)){
          if($weeks > 1){
            $this->data[$param][$weeks . 'weeks'] = 'Peste ' . $weeks . ' saptamani';
          } else {
            $this->data[$param][$weeks . 'weeks'] = 'Peste o saptamana';
          }
        } 
        */
        if($day_diff < 7){
          $this->data[$param][$dowMap[$dow_numeric]] = $dowMapRo[$dow_numeric] . ' aceasta (include ziua curenta)';
        }
        if($day_diff > 0 && $day_diff <= 7){
          $this->data[$param]['next ' . $dowMap[$dow_numeric]] = $dowMapRo[$dow_numeric] . ' urmatoare (omite ziua curenta)';
        }
        if($day_diff > 7 && $day_diff < 14){
          $this->data[$param][$dowMap[$dow_numeric] . ' 1weeks'] = $dowMapRo[$dow_numeric] . ' din saptamana urmatoare';
        }
      } else {
        if(!isset($d2)){
          $d2 = new DateTime('today midnight');
        }
        $d1 = clone $d2;
        try{
          $d2->modify($date);
        } catch(Exception $e){
          $d2 = $d1;
        }
        $d = $d2->format('Y-m-d');
        $this->data[$param][$d] = $d . ' (Data exacta)';
        if($d1 > $d2){
          $this->addError('Data de checkout/sosire este inaintea datei de plecare.');
          continue;
        }

        $datediff = $d2->diff($d1);
        $day_diff = intval($datediff->format("%a"));
        
        if(!$day_diff){
          $this->data[$param][$day_diff . 'days'] = 'Aceeasi zi ca data de checkin/plecare';
        }
        if($day_diff == 1){
          $this->data[$param][$day_diff . 'days'] = 'Ziua urmatoare datei de checkin/plecare' . $day_diff;
        }
        if($day_diff > 1){
          $this->data[$param][$day_diff . 'days'] = $day_diff . ' zile dupa data de checkin/plecare';
        }
      }
      
      if(!isset($this->data[$param][$date])){
        $this->data[$param][$date] = 'Custom: ' . $date;
      }
		}
    $this->output();
    exit;
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Invalid access');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->outputError('Invalid access');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $type = $this->input->post('type');
    $filters['type'] = $type;
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-cms-pages-access');
    $user_can['access_own'] = $user_can['access'] || $this->user->can('backend-cms-pages-access');
    $user_can['view_own'] = $user_can['access_own'] && $this->user->can('backend-cms-pages-own-view');
    $user_can['edit_own'] = $user_can['access_own'] && $this->user->can('backend-cms-pages-own-edit');
    $user_can['delete_own'] = $user_can['access_own'] && $this->user->can('backend-cms-pages-own-delete');
    $user_can['view'] = $user_can['access'] && $this->user->can('backend-cms-pages-view');
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-cms-pages-edit');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-cms-pages-delete');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    if(!$user_can['access']){
      $filters['created_by'] = $this->user->id;
    }
    $filters['join_content'] = true;
    if($simple){
      $filters['return_rows'] = true;
    }
    $this->load->model('CMS_Pages_model');
    $this->data['total_items'] = $this->CMS_Pages_model->getTotalPages($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    $blog = trim('' . $this->input->post('blog'));
    $filters['blog'] = $blog;
    $type = trim('' . $this->input->post('type'));
    $filters['type'] = $type;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_items'] / $filters['limit']) : 1;
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
    // $this->db->group_by('p.page_id');
    $pages = $this->CMS_Pages_model->getPages($filters);
    if(!$simple){
      foreach($pages as $k=>$page){
        $page->can_view = ($user_can['access'] && $user_can['view']) || ($page->created_by == $this->user->id && $user_can['view_own']);
        if($page->can_view){
          $page->view_link = site_url('backend/cms/pages/view?id=' . $page->page_id);
        }
        $page->can_edit = ($user_can['access'] && $user_can['edit']) || ($page->created_by == $this->user->id && $user_can['edit_own']);
        if($page->can_edit){
          $page->edit_link = site_url('backend/cms/pages/edit?id=' . $page->page_id);
        }
        $page->can_delete = ($user_can['access'] && $user_can['delete']) || ($page->created_by == $this->user->id && $user_can['delete_own']);
        if($page->can_delete){
          $page->delete_link = site_url('backend/cms/pages/delete?id=' . $page->page_id);
        }
      }
    }
    $this->data['pages'] = $pages;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['type'] = $type;
      $session_data['blog'] = $blog;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/cms/pages/list', $session_data);
    }
    $this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->can('backend-cms-pages-add')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->load->library('CmsPage');
    $this->data['page'] = new CmsPage;
    $this->theme->view('backend/cms/page', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('CMS_Pages_model');
    $page = $this->CMS_Pages_model->getPageById($id);
    if(!$page){
      $this->redirect('backend/cms/pages','Acces invalid', 'error');
    }
    $can_access = $this->user->can('backend-cms-pages-access');
    $can_edit = $can_access && $this->user->can('backend-cms-pages-edit');
    if(!$can_edit){
      $can_access_own = $can_access || $this->user->can('backend-cms-pages-own-access');
      $can_edit_own = $can_access_own && $this->user->can('backend-cms-pages-own-edit');
      $can_edit = ($page->created_by == $this->user->id) && $can_edit_own;
    }
    if(!$can_edit){
      $this->redirect('backend/cms/pages','Acces invalid', 'error');
    }
    $page->languages = $this->CMS_Pages_model->getPageLanguages($id);
    $this->data['page'] = $page;
    $this->theme->view('backend/cms/page', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('CMS_Pages_model');
    $page = $this->CMS_Pages_model->getPageById($id);
    if(!$page){
      $this->redirect('backend/cms/pages','Acces invalid', 'error');
    }
    $can_access = $this->user->can('backend-cms-pages-access');
    $can_view = $can_access && $this->user->can('backend-cms-pages-view');
    if(!$can_view){
      $can_access_own = $can_access || $this->user->can('backend-cms-pages-own-access');
      $can_view_own = $can_access_own && $this->user->can('backend-cms-pages-own-view');
      $can_view = ($page->created_by == $this->user->id) && $can_view_own;
    }
    if(!$can_view){
      $this->redirect('backend/cms/pages','Acces invalid', 'error');
    }
    $page->languages = $this->CMS_Pages_model->getPageLanguages($id);
    $this->data['page'] = $page;
    $this->theme->view('backend/cms/page', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('CMS_Pages_model');
    $page = $this->CMS_Pages_model->getPageById($id);
    if(!$page){
      $this->redirect('backend/cms/pages','Acces invalid', 'error');
    }
    $can_access = $this->user->can('backend-cms-pages-access');
    $can_delete = $can_access && $this->user->can('backend-cms-pages-delete');
    if(!$can_delete){
      $can_access_own = $can_access || $this->user->can('backend-cms-pages-own-access');
      $can_delete_own = $can_access_own && $this->user->can('backend-cms-pages-own-delete');
      $can_delete = ($user->created_by == $this->user->id) && $can_delete_own;
    }
    if(!$can_delete){
      $this->redirect('backend/cms/pages','Acces invalid', 'error');
    }
    $this->load->model('CMS_Pages_model');
    $this->CMS_Pages_model->deletePageById($id);
    redirect('backend/cms/pages');
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/cms/pages', 'Invalid access', 'error');
      }
    }
    if(!$this->user->canAny('backend-cms-pages-access','backend-cms-pages-own-access')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/cms/pages', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $page_id = $id;
    if($task == 'save_as_new'){
      $page_id = 0;
    }
    $data = array();
    if($page_id){
      $this->load->model('CMS_Pages_model');
      $page = $this->CMS_Pages_model->getPageById($page_id);
      if(!$page){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid page');
        } else {
          $this->redirect('backend/cms/pages', 'Invalid page', 'error');
        }
      }
      $can_access = $this->user->can('backend-cms-pages-access');
      $can_edit = $can_access && $this->user->can('backend-cms-pages-edit');
      if(!$can_edit){
        $can_access_own = $can_access || $this->user->can('backend-cms-pages-own-access');
        $can_edit_own = $can_access_own && $this->user->can('backend-cms-pages-own-edit');
        $can_edit = ($page->created_by == $this->user->id) && $can_edit_own;
      }
      if(!$can_edit){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid access');
        } else {
          $this->redirect('backend/cms/pages', 'Invalid access', 'error');
        }
      }
      $page->languages = $this->CMS_Pages_model->getPageLanguages($page_id);
      $data['modified_by'] = $this->user->id;
      $data['time_modified'] = date('Y-m-d H:i:s');
    } else {
      if(!$this->user->can('backend-cms-pages-add')){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid access');
        } else {
          $this->redirect('backend/cms/pages', 'Invalid access', 'error');
        }
      }
      $this->load->library('CmsPage');
      $page = new CmsPage;
      $page->languages = array();
      $data['created_by'] = $this->user->id;
      $data['time_created'] = date('Y-m-d H:i:s');
    }
    $this->load->library('form_validation');
    $should_validate = true;
    $languages = $this->input->post('languages');
    
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $page->status = $data['status'] = $this->input->post('status');
    $this->form_validation->set_rules('blog', 'Blog', 'required|in_list[0,1]');
    $page->blog = $data['blog'] = $this->input->post('blog');
    $page->sort_order = $data['sort_order'] = intval($this->input->post('sort_order'));
	
	$images = (array)$this->input->post('images');
	$page->images = [];
	foreach($images as $image){
		$page->images[$image['name']] = [];
		if(isset($image['hide'])) $page->images[$image['name']]['hide'] = 1;
		if(isset($image['custom'])) $page->images[$image['name']]['custom'] = 1;
		if(isset($image['alt'])) $page->images[$image['name']]['alt'] = $image['alt'];
	}
	$data['images'] = $page->images ? $page->images : [];
	
    $data['languages'] = array();
    if($languages && is_array($languages)){
      foreach($languages as $language_key => $language_data){
        $fake_post_key_prefix = 'lang_' . $language_key . '_';
        
        $language_data['title'] = $_POST[$fake_post_key_prefix . 'title'] = isset($language_data['title']) ? trim($language_data['title']) : null;
        $_POST[$fake_post_key_prefix . 'language'] = $language_key;
        $_POST[$fake_post_key_prefix . 'slug'] = isset($language_data['slug']) && strlen($language_data['slug']) ? trim($language_data['slug']) : $_POST[$fake_post_key_prefix . 'title'];
        $language_data['slug'] = $_POST[$fake_post_key_prefix . 'slug'] = url_title($_POST[$fake_post_key_prefix . 'slug'], 'dash', true);
        $language_data['layout'] = $_POST[$fake_post_key_prefix . 'layout'] = isset($language_data['layout']) && strlen(trim($language_data['layout'])) ? trim($language_data['layout']) : null;
        $url = isset($language_data['route']) && strlen(trim($language_data['route'])) ? trim($language_data['route']) : null;
				
				$parsed_url = parse_url($url);
				$_POST[$fake_post_key_prefix . 'route'] = $language_data['route'] = isset($parsed_url['path']) && strlen(trim($parsed_url['path'], ' /')) ? trim($parsed_url['path'], ' /') : null;
				$language_data['params'] = isset($parsed_url['query']) && strlen($parsed_url['query']) ? $parsed_url['query'] : null;
				
        $language_data['params'] = $_POST[$fake_post_key_prefix . 'params'] = isset($language_data['params']) && strlen(trim($language_data['params'])) ? $language_data['params'] : null;
        $language_data['description'] = $_POST[$fake_post_key_prefix . 'description'] = isset($language_data['description']) ? trim($language_data['description']) : null;
        $language_data['keywords'] = $_POST[$fake_post_key_prefix . 'keywords'] = isset($language_data['keywords']) ? trim($language_data['keywords']) : null;
        $language_data['content'] = $_POST[$fake_post_key_prefix . 'content'] = isset($language_data['content']) ? trim($language_data['content']) : null;
        
        $check_unique_slug = !$page->page_id || (!isset($page->languages[$language_key]) || $page->languages[$language_key]->slug !== $_POST[$fake_post_key_prefix . 'slug']);

        $this->form_validation->set_rules($fake_post_key_prefix . 'title', 'Titlu', 'required|trim|max_length[255]');
        $this->form_validation->set_rules($fake_post_key_prefix . 'slug', 'Alias', 'trim|regex_match[/^[a-z0-9_\-]+$/i]' . ($check_unique_slug ? '|is_unique[ac_cms_pages_content.slug]' : ''));
        $this->form_validation->set_rules($fake_post_key_prefix . 'language', 'Limba', 'required|max_length[5]');
        $this->form_validation->set_rules($fake_post_key_prefix . 'layout', 'Sablon', 'max_length[255]');
        $this->form_validation->set_rules($fake_post_key_prefix . 'description', 'Descriere', 'trim|max_length[1024]');
        $this->form_validation->set_rules($fake_post_key_prefix . 'keywords', 'Cuvinte cheie', 'trim|max_length[1024]');
        
        $data['languages'][$language_key] = $language_data;
        $page->languages[$language_key] = (object)$language_data;
      }
    } else {
      $this->form_validation->set_rules('languages', 'Informatii pagina', 'required|is_array',array(
        'is_array' => 'Informatii invalide',
      ));
    }
    if($page_id){
      $data['page_id'] = $page_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['page'] = $page;
      return $this->theme->view('backend/cms/page', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $this->load->model('CMS_Pages_model');
    $is_new = !$page_id;
    $id = $this->CMS_Pages_model->savePage($data);
    $message = 'Pagina a fost actualizata';
    if($is_new){
      $message = 'Pagina a fost creata';
    }
    $redirect_url = 'backend/cms/pages';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/cms/pages/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/cms/pages/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}