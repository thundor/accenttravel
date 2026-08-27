<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');

(defined('EXT')) OR define('EXT', '.php');

global $CFG;

/* get module locations from config settings or use the default module location and offset */
is_array(Modules::$locations = $CFG->item('modules_locations')) OR Modules::$locations = array(
  APPPATH . 'modules/' => '../modules/',
  APPPATH . 'modules/integrations/' => '../modules/integrations/',
);

/* PHP5 spl_autoload */
spl_autoload_register('Modules::autoload');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library provides functions to load and instantiate controllers
 * and module controllers allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Modules.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.5
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * */
class Modules {

  public static $routes, $registry, $locations, $page;

  /**
   * Run a module controller method
   * Output from module is buffered and returned.
   * */
  public static function run($module) {
    $method = 'index';

    if (($pos = strrpos($module, '/')) != FALSE) {
      $method = substr($module, $pos + 1);
      $module = substr($module, 0, $pos);
    }

    if ($class = self::load($module)) {
      if (method_exists($class, $method)) {
        ob_start();
        $args = func_get_args();
        $output = call_user_func_array(array($class, $method), array_slice($args, 1));
        $buffer = ob_get_clean();
        return ($output !== NULL) ? $output : $buffer;
      }
    }

    // log_message('error', "Module controller failed to run: {$module}/{$method}");
  }

  /** Load a module controller * */
  public static function load($module) {
    (is_array($module)) ? list($module, $params) = each($module) : $params = NULL;

    /* get the requested controller class name */
    $alias = strtolower(basename($module));

    /* create or return an existing controller from the registry */
    if (!isset(self::$registry[$alias])) {
      /* find the controller */
      list($class) = CI::$APP->router->locate(explode('/', $module));

      /* controller cannot be located */
      if (empty($class))
        return;

      /* set the module directory */
      $path = APPPATH . 'controllers/' . CI::$APP->router->directory;

      /* load the controller class */
      $class = $class . CI::$APP->config->item('controller_suffix');
      self::load_file(ucfirst($class), $path);

      /* create and register the new controller */
      $controller = ucfirst($class);
      self::$registry[$alias] = new $controller($params);
      CI::$APP->data['page'] = &self::$page;
    }

    return self::$registry[$alias];
  }

  /** Library base class autoload * */
  public static function autoload($class) {
    /* don't autoload CI_ prefixed classes or those using the config subclass_prefix */
    if (strstr($class, 'CI_') OR strstr($class, config_item('subclass_prefix')))
      return;

    /* autoload Modular Extensions MX core classes */
    if (strstr($class, 'MX_')) {
      if (is_file($location = dirname(__FILE__) . '/' . substr($class, 3) . EXT)) {
        include_once $location;
        return;
      }
      show_error('Failed to load MX core class: ' . $class);
    }

    /* autoload core classes */
    if (is_file($location = APPPATH . 'core/' . ucfirst($class) . EXT)) {
      include_once $location;
      return;
    }

    /* autoload library classes */
    if (is_file($location = APPPATH . 'libraries/' . ucfirst($class) . EXT)) {
      include_once $location;
      return;
    }
  }

  /** Load a module file * */
  public static function load_file($file, $path, $type = 'other', $result = TRUE) {
    $file = str_replace(EXT, '', $file);
    $location = $path . $file . EXT;

    if ($type === 'other') {
      if (class_exists($file, FALSE)) {
        log_message('debug', "File already loaded: {$location}");
        return $result;
      }
      include_once $location;
    } else {
      /* load config or language array */
      include $location;

      if (!isset($$type) OR ! is_array($$type))
        show_error("{$location} does not contain a valid {$type} array");

      $result = $$type;
    }
    log_message('debug', "File loaded: {$location}");
    return $result;
  }

  /**
   * Find a file
   * Scans for files located within modules directories.
   * Also scans application directories for models, plugins and views.
   * Generates fatal error if file not found.
   * */
  public static function find($file, $module, $base) {
    $segments = explode('/', $file);

    $file = array_pop($segments);
    $file_ext = (pathinfo($file, PATHINFO_EXTENSION)) ? $file : $file . EXT;

    $path = ltrim(implode('/', $segments) . '/', '/');
    $module ? $modules[$module] = $path : $modules = array();

    if (!empty($segments)) {
      $modules[array_shift($segments)] = ltrim(implode('/', $segments) . '/', '/');
    }

    foreach (Modules::$locations as $location => $offset) {
      foreach ($modules as $module => $subpath) {

        $fullpath = $location . $module . '/' . $base . $subpath;

        if ($base == 'libraries/' OR $base == 'models/') {
          if (is_file($fullpath . ucfirst($file_ext)))
            return array($fullpath, $file);
        }
        else
        /* load non-class files */
        if (is_file($fullpath . $file_ext))
          return array($fullpath, $file);
      }
    }

    return array(FALSE, $file);
  }

  public static function parseSeoUri($uri = '', $routes = array()) {
    $uri_rest = '';
    if(($slash_pos = strpos($uri,'/')) !== false){
      $uri_rest = substr($uri,$slash_pos);
      $uri = substr($uri,0,$slash_pos);
    }
    require_once( BASEPATH .'database/DB'. EXT );
    $db =& DB();
    
    $db->select('*');
    $db->join('ac_cms_pages_content pc', 'p.page_id = pc.page_id');
    if($routes){
      $db->where_in('pc.route', $routes);
      $db->where('pc.params IS NULL');
    } else {
      $db->where('pc.slug', $uri);
    } 
    $db->where('p.status', 1);
    $page = $db->get('ac_cms_pages p')->row();
    if($page){
      $uri = 'cms/page/' . $page->slug;
      if($page->route){
        $uri = trim($page->route,'/');
      }
      /* $nesting = substr_count($uri,'/');
      $uri_parts = explode('/', $uri);
      if($nesting == 0){
        $uri .= '/index';
      } elseif($nesting == 1){
        if(file_exists(APPPATH . 'controllers/' . $uri_parts[0] . '/' . $uri_parts[1] . EXT)){
          $uri .= '/index';
        } else {
          if(file_exists(APPPATH . 'modules/' . $uri_parts[0] . '/controllers/' . $uri_parts[1] . EXT)){
            $uri .= '/index';
          }
          
        }
      } */
      self::$page = &$page;
			if(isset($page->params)){
				$get = array();
				parse_str($page->params, $get);
				$_GET = array_replace($get, $_GET);
				unset($get);
			}
    }
    $uri .= $uri_rest;
    return $uri;
  }
}