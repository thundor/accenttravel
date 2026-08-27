<?php

class Theme {

  public $view_data = array(); //the data (variables passed to the theme and views)
  public $view_tpl = ''; //the view tpl
  public $view_file = ''; //the view file
  public $view_content; //the view file
  public $theme_url = ''; //the theme URL
  public $theme_path = ''; //the theme URL

  public $_ci = NULL;    //codeigniter instance
  public $_config = array(); //the theme config
  public $_content = '';      //the content (filled by the view/theme function)
  public $_messages = array(); //messages to display
  public $_module = NULL;          //current module
  public $_controller = NULL;      //current controller
  public $_method = NULL;          //current method
  public $_template_locations = array();
  public $_can_edit = false;
  
  /**
   * Theme::__construct()
   * @return void
   */
  function __construct() {

    //get the CI instance
    $this->_ci = &get_instance();
    //get the config
    $this->_config = config_item('theme');


    if (method_exists($this->_ci->router, 'fetch_module')) {
      $this->_module = $this->_ci->router->fetch_module();
    }

    // What controllers or methods are in use
    $this->_controller = $this->_ci->router->fetch_class();
    $this->_method = $this->_ci->router->fetch_method();
    
    $this->set_default_theme();
  }
  
  /**
   * Get the theme used by the current module
   * @return void;
   */
  protected function set_default_theme(){
    $theme = $this->_config['theme'];
    if($this->_ci->session->userdata('pt_theme_preview')){
      $theme = $this->_ci->session->userdata('pt_theme_preview');
    }
    $this->set_theme($theme);
    $layout = 'index';
    $this->set_layout($layout);
    $sublayout = 'frontend/index';
    if($this->_module === 'Backend'){
      $sublayout = 'backend/index';
    }
    $this->set_sublayout($sublayout);
  }
  
  /**
   * Theme::set_theme()
   *
   * Sets the theme
   *
   * @param string $theme The theme
   * @return void
   */
  function set_theme($theme = 'default') {
    $this->set_config('theme', $theme);
    $this->theme_url = $this->config('url') . $theme . '/';
    $this->theme_path = $this->config('path') . $theme . '/';
    $functions = $this->config('path') . $this->config('theme') . '/functions.php';
    if (file_exists($functions)) {
      require_once($functions);
    }

    $this->_template_locations = array(
		$this->config('path') . $this->config('theme') . '/views/modules/' . $this->_module . '/',
		$this->config('path') . $this->config('theme') . '/views/',
    );
	
	if(('pay24'!= $this->config('theme')) && ($this->config('theme') !== 'accent')){
		$this->_template_locations[] = $this->config('path') . 'accent/views/modules/' . $this->_module . '/';
		$this->_template_locations[] = $this->config('path') . 'accent/views/';
	}
	
	if(is_dir($this->config('path') . 'default')){
		if($this->config('theme') !== 'default'){
			$this->_template_locations[] = $this->config('path') . 'default/views/modules/' . $this->_module . '/';
			$this->_template_locations[] = $this->config('path') . 'default/views/';
		}
	}
	
	$this->_template_locations[] = APPPATH . 'modules/' . $this->_module . '/views/';
  }

  /**
   * Theme::set_layout()
   *
   * Sets the layout for the current theme (default: index => index.php)
   *
   * @param string $layout The layout for the theme
   * @return void
   */
  function set_layout($layout = 'index') {
    $path = $this->config('path') . $this->config('theme') . '/' . $layout . '.php';
    if (!file_exists($path)) {
      $layout = 'index';
    }
    $this->set_config('layout', $layout);
  }
  /**
   * Theme::set_sublayout()
   *
   * Sets the sublayout for the current theme (default: index => index.php)
   *
   * @param string $layout The layout for the theme
   * @return void
   */
  function set_sublayout($sublayout = 'default') {
    if(isset(Modules::$page)){
      $allow_page_override = true;
      if(isset($this->_ci->theme)){
        $full_route = strtolower($this->_ci->router->module . '/' . $this->_ci->router->class . '/' . $this->_ci->router->method);
        if(Modules::$page->route !== $full_route){
          $allow_page_override = false;
        }
      }
      if($allow_page_override && Modules::$page->layout){
        $sublayout = Modules::$page->layout;
        if(strpos($sublayout,'/') === false){
          $sublayout .= '/index';
        }
        $sublayout = 'frontend/' . $sublayout;
      }
    }
    $path = $this->config('path') . $this->config('theme') . '/layouts/' . $sublayout . '.php';
    if (!file_exists($path)) {
      $sublayout = 'frontend/index';
      if($this->_module === 'Backend'){
        $sublayout = 'backend/index';
      }
    }
    $this->set_config('sublayout', $sublayout);
  }

  /**
   * Theme::add_message()
   *
   * Adds a message to the queue
   *
   * @param string $message The message to display
   * @param string $type Can be anything: info,success,error,warning
   * @return void
   */
  function add_message($message, $type = 'info') {
    $this->_messages[] = array(
      'message' => $message,
      'type' => $type,
    );
  }

  /**
   * Theme::set_messages()
   *
   * Sets all messages (handy for flash ops)
   *
   * @param array $messages Messages to be set
   * @return void
   */
  function set_messages($messages) {
    $messages = is_array($messages) ? $messages : array();
    $this->_messages = $messages;
  }

  /**
   * Theme::clear_messages()
   *
   * Removes all messages
   *
   * @return void
   */
  function clear_messages() {
    $this->_messages = array();
  }

  /**
   * Theme::config()
   *
   * Returns an item from the config array
   *
   * @param string $name
   * @param bool $default (optional: FALSE)
   * @return mixed or $default if not found
   */
  function config($name, $default = FALSE) {
    return isset($this->_config[$name]) ? $this->_config[$name] : $default;
  }

  /**
   * Theme::set_config()
   *
   * Sets an item in the config array
   * e.g. $this->theme->set_config('theme', 'other_theme');
   *
   * @param mixed $name
   * @param mixed $value
   * @return void
   */
  function set_config($name, $value) {
    $this->_config[$name] = $value;
  }

  /**
   * Theme::get()
   *
   * Gets an item from the data array
   * e.g. $this->theme->get('current_user');
   *
   * @param string $name The value to get
   * @param bool $default (optional: FALSE)
   * @return mixed or $default if not found
   */
  function get($name, $default = FALSE) {
    return isset($this->view_data[$name]) ? $this->view_data[$name] : $default;
  }
  
  /**
   * Theme::set()
   *
   * Sets an item in the data array
   * e.g. $this->theme->set('current_user', $this->user);
   *
   * @param string $name The item to set
   * @param mixed $value The value to set
   * @return void
   */
  function set($name, $value) {
    $this->view_data[$name] = $value;
  }

  /**
   * Theme::messages()
   *
   * Returns an unordered list (HTML) for the message or
   * the message array. depending on the $html variable
   *
   * @param bool $html Return it as html? (false=array)
   * @return string(html) or array
   */
  function messages($html = TRUE) {
    if (!$html) {
      return $this->_messages;
    }

    $html = '';
    $html .= '<ul class="messages">';
    foreach ($this->_messages as $message) {
      $html .= sprintf('<li class="%s">%s</li>', $message['type'], $message['message']);
    }
    $html .= '</ul>';
    return $html;
  }

  function clear() {
    $this->view_tpl = '';
    $this->view_content = null;
    $this->view_file = '';
    themeFunctions::clear();
  }
  /**
   * Theme::content()
   *
   * Returns the content variable (filled by the view/theme function)
   *
   * @return string
   */
  function content() {
    if(!is_null($this->view_content)){
      return $this->view_content;
    }
    $this->view_content = null;
    if($this->view_file){
      ob_start();
      include $this->view_file;
      $this->view_content = ob_get_contents();
      ob_end_clean();
    }
    return $this->view_content;
  }
  
  /**
   * Includes a theme file
   *
   * @return string
   */
  function includeFile($file_name, $data = array(), $once=false) {
    if(!$file_name){
      return;
    }
    if(isset($data['data'])){
      unset($data['data']);
    }
    extract($data);
	if(!is_file($file_name)){
		if($this->config('theme') != 'accent'){
			if(0 === strpos($file_name, $this->theme_path)){
				$file_name = $this->config('path') . 'accent' . '/' . substr($file_name, strlen($this->theme_path));
			}
		}
	}
    if($once){
      include_once $file_name;
    } else {
      include $file_name;
    }
  }

  /**
   * Theme::view()
   *
   * Loads the view just as CI would normally do and
   * passed it to the theme function wrapping the view into the theme
   *
   * @param string $view The view to load
   * @param array $data The data array to pass to the view
   * @param bool $return (optional) Return the output?
   * @return void or the HTML
   */
  function view($view, &$data = array()) {
    $this->clear();
    $this->view_tpl = $view;
	$this->view_data = $data;
    foreach ($this->_template_locations as $location) {
      if (file_exists($location . $view . '.php')) {
        $this->view_file = $location . $view . '.php';
        break;
      }
    }
	// if(!empty($_GET['testtt'])){
		// var_dump($this->view_file); die;
	// }
    $theme = $this->config('path') . $this->config('theme') . '/' . $this->config('layout') . '.php';
    ob_start();
    include $theme;
    $html = ob_get_contents();
    ob_end_clean();
    if(!empty($_GET['testtt'])){
		echo $html; die;
	}
    get_instance()->output->set_output($html);
  }
}