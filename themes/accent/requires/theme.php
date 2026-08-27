<?php
defined('ENVIRONMENT') OR die('Invalid access');
/**
 * Set of functions needed for the theme
 */
abstract class themeFunctions {
  /**
   * Theme debug enabled / disabled
   * @var bool 
   */
  static $debug = false;

  /**
   * Full theme folder path using DIRECTORY_SEPARATOR
   * @var string 
   */
  static $absolute_theme_path = '';

  /**
   * $absolute_theme_path character length
   * @var int 
   */
  static $absolute_theme_path_length = 0;

  /**
   * Custom file includes for addons
   * @var array => (theme_file => include_file_path)
   */
  static $includes = array();
  static $includes_params = array();
  /**
   * Addons loaded
   * @var array => (addon_name => include_file_path)
   */
  static $addons = array();
  /**
   * Modules loaded
   * @var array => (module_name => include_file_path)
   */
  static $modules = array();
  
  /**
   * Modules prevended from being loaded
   * @var array => (module_name => include_file_path)
   */
  static $blocked_modules = array();
  
  /**
   * Layouts loaded
   * @var array => (layout_name => include_file_path)
   */
  static $layouts = array();

  /**
   * Javascript language strings
   * @var array => (key => translation)
   */
  static $js_lang = array();

  /**
   * Theme object
   */
  static $theme_obj;

  
  static function clear() {
    static::$includes = array();
    static::$includes_params = array();
    static::$addons = array();
    static::$modules = array();
    static::$blocked_modules = array();
    static::$layouts = array();
    static::$js_lang = array();
  }
  /**
   * Enable Theme debugging
   */
  static function enableDebug() {
    static::$debug = true;
  }

  /**
   * Disable Theme debugging
   */
  static function disableDebug() {
    static::$debug = false;
  }

  /**
   * Echoes a HTML comment with a message
   * @param mixed $message
   * @return void
   */
  static function debug($message) {
    if (!static::$debug)
      return;
    ?><!-- <?php print_r($message); ?> --><?php
    echo "\n";
  }

  /**
   * Echoes a HTML comment debug message containing the file and line of caller
   * @param mixed $message
   * @return void
   */
  static function debugFileLine($message) {
    if (!static::$debug) {
      return;
    }
    $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
    $message .= " File: " . static::relativeUrlPath($debug[0]['file']);
    $message .= " Line: " . $debug[0]['line'];
    static::debug($message);
  }

  /**
   * Set the theme's path as a static variable (in order to be accessed by addons)
   * @param string $path Must have ending slash !
   */
  static function setBasePath($path) {
    static::$absolute_theme_path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    static::$absolute_theme_path_length = strlen($path);
  }
  /**
   * Set the theme's path as a static variable (in order to be accessed by addons)
   * @param string $path Must have ending slash !
   */
  static function setThemeObj(&$obj) {
    static::$theme_obj = $obj;
  }

  /**
   * Replaces / with DIRECTORY_SEPARATOR for safe file/folder paths
   * @param string $path
   * @return string
   */
  static function dirPath($path) {
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
  }

  /**
   * Replaces DIRECTORY_SEPARATOR with / for safe file/folder URL paths
   * @param string $path
   * @return string
   */
  static function urlPath($path) {
    return str_replace(DIRECTORY_SEPARATOR, '/', $path);
  }

  /**
   * Delete the theme's folder paths from the path given
   * @param string $path Must be the return value of static::dirPath($path)
   * @return string
   */
  static function relativePath($path) {
	// $r = substr($path, static::$absolute_theme_path_length);
	$rel = substr($path, strlen(dirname(static::$absolute_theme_path) . '/'));
	$r2 = preg_replace('/^.*?\//', '', $rel);
	return $r2;
	 /*  if(!empty($_GET['asdasd'])){
			dump('---');
			dump($path);
			dump($rel);
			dump($r2);
			dump($r);
	  }
    return substr($path, static::$absolute_theme_path_length); */
  }

  /**
   * The relative url path of the path
   * @param string $path Must be the return value of static::dirPath($path)
   * @return string
   */
  static function relativeUrlPath($path) {
    return static::urlPath(static::relativePath($path));
  }

  /**
   * Add include path of addon
   * @param string $path Must be the return value of static::dirPath($path)
   * @param string $file_path Must be an absolute file path, the file must exist!
   * @return string
   */
  static function addIncludePath($path, $file_path, $data=array()) {
    static::$includes[$path][] = static::dirPath($file_path);
    static::$includes_params[$path][] = $data;
  }

  /**
   * Include the index file of the specified addon
   */
  static function includeAddon($addon_name, $data=array()) {
    if(isset(static::$addons[$addon_name])){
      return;
    }
    $addon_path = static::dirPath(static::$absolute_theme_path . 'addons' . '/' . $addon_name . '/index.php');
    static::$theme_obj->includeFile($addon_path, $data, true);
    static::$addons[$addon_name] = $addon_path;
  }
  /**
   * Search all addons and include the index file of each.
   */
  static function includeAllAddons($data=array()) {
    $addons_dir = static::dirPath(static::$absolute_theme_path . 'addons');
    foreach (new DirectoryIterator($addons_dir) as $item) {
      if (!$item->isDot() && $item->isDir()) {
        $addon_name = $item->getFilename();
        if(isset(static::$addons[$addon_name])){
          continue;
        }
        $addon_path = static::dirPath($item->getRealPath() . '/index.php');
        $addon_data = isset($data[$addon_name]) ? $data[$addon_name] : array();
        static::$theme_obj->includeFile($addon_path, $addon_data, true);
        static::$addons[$addon_name] = $addon_path;
      }
    }
  }

  /**
   * Loads all includes for a specific path
   * @param string $path The path must be an absolute file path.
   * @return void
   */
  static function loadAddons($path, $data=array(), $once=false) {
    $rel_path = static::relativeUrlPath($path);
    if (!isset(static::$includes[$rel_path])) {
      return;
    }
    foreach (static::$includes[$rel_path] as $k=>$abs_path) {
      $module_data = array_replace($data, static::$includes_params[$rel_path][$k]);
      if(isset($module_data['once'])){
        $once = $module_data['once'];
      }
      static::$theme_obj->includeFile($abs_path, $module_data, $once);
      if(isset($module_data['one'])){
        break;
      }
    }
  }
  
  /**
   * Check if there are any includes registered for the specified path
   * @param type $file_path The file path of caller
   * @return bool
   */
  static function hasIncludes($file_path) {
    $path = static::dirPath($file_path);
    $rel_path = static::relativeUrlPath($path);
    
    return isset(static::$includes[$rel_path]) && !empty(static::$includes[$rel_path]);
  }
  /**
   * Include the index file of the specified module
   * @param type $module_name The module name to be loaded
   * @param type $path The file path of caller
   * @return void
   */
  static function blockModule($module_name, $path=null) {
    if(!isset(static::$blocked_modules[$module_name])){
      static::$blocked_modules[$module_name] = array();
    }
    if(!$path){
      static::$blocked_modules[$module_name] = array();
      return;
    }
    if($path === true){
      static::$blocked_modules[$module_name] = true;
      return;
    }
    if(!is_array(static::$blocked_modules[$module_name])){
      static::$blocked_modules[$module_name] = array();
    }
    if(in_array($path,static::$blocked_modules[$module_name])){
      return;
    }
    static::$blocked_modules[$module_name][] = $path;
  }
  /**
   * Include the index file of the specified module
   * @param type $module_name The module name to be loaded
   * @param type $path The file path of caller
   * @return void
   */
  static function loadModule($module_name, $path=null, $data=array()) {
    $data['module_path'] = static::dirPath(static::$absolute_theme_path . 'modules' . '/' . $module_name . '/index.php');
    if(!is_null($path)){
      $data['include_path'] = static::relativeUrlPath($path);
    }
    if(!isset($data['include_path'])){
      $data['include_path'] = null;
    }
    if(isset(static::$blocked_modules[$module_name]) && (true === static::$blocked_modules[$module_name] || (is_array(static::$blocked_modules[$module_name]) && in_array($data['include_path'], static::$blocked_modules[$module_name])))){
      return false;
    }
    static::$theme_obj->includeFile($data['module_path'], $data);
    if(!isset(static::$modules[$module_name])){
      static::$modules[$module_name] = array();
    }
    static::$modules[$module_name][] = $data['module_path'];
    return true;
  }
  /**
   * Include the index file of the specified layout
   * @param type $layout_name The layout name to be loaded
   * @param type $path The file path of caller
   * @return void
   */
  static function loadLayout($layout_name, $path, $data=array()) {
    $data['layout_path'] = static::dirPath(static::$absolute_theme_path . 'layouts' . '/' . $layout_name . '.php');
    $data['include_path'] = static::relativeUrlPath($path);
    static::$theme_obj->includeFile($data['layout_path'], $data);
    static::$layouts[$layout_name] = $data['layout_path'];
  }
  /**
   * Include the index file of the specified layout
   * @param type $layout_name The layout name to be loaded
   * @param type $path The file path of caller
   * @return void
   */
  static function loadLang($lang_name, $path=null) {
    static::$theme_obj->_ci->lang->load($lang_name,'',false,false,array_unique([static::$theme_obj->theme_path, config_item('theme')['path'] . 'accent/']));
  }
  static function jsLang($string) {
    static::$js_lang[$string] = lang($string);
    if(false === static::$js_lang[$string]){
      static::$js_lang[$string] = $string;
    }
  }
}