<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Config class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Config.php
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
class MX_Config extends CI_Config {

  public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE, $_module = '') {
    if (in_array($file, $this->is_loaded, TRUE))
      return $this->item($file);

    $_module OR $_module = CI::$APP->router->fetch_module();
    list($path, $file) = Modules::find($file, $_module, 'config/');

    if ($path === FALSE) {
      parent::load($file, $use_sections, $fail_gracefully);
      return $this->item($file);
    }

    if ($config = Modules::load_file($file, $path, 'config')) {
      /* reference to the config array */
      $current_config = & $this->config;

      if ($use_sections === TRUE) {
        if (isset($current_config[$file])) {
          $current_config[$file] = array_merge($current_config[$file], $config);
        } else {
          $current_config[$file] = $config;
        }
      } else {
        $current_config = array_merge($current_config, $config);
      }

      $this->is_loaded[] = $file;
      unset($config);
      return $this->item($file);
    }
  }
  public function site_url($uri = '', $protocol = NULL) {
    $route = $uri;
    $params = '';
    if (($offset = strpos($uri, '?')) !== FALSE) {
      $route = substr($uri, 0, $offset);
      $params = substr($uri, $offset);
    }
    if(strpos($uri,'/') !== false){
      $ci = get_instance();
      $ci->load->database();
      $db = $ci->db;
      $db->select('pc.slug, pc.route');
      $db->join('ac_cms_pages_content pc', 'p.page_id = pc.page_id');
      $routes = array();
      $routes[]= $route;
      $route_split = explode('/', $route);
      $total_route_items = count($route_split);
      for($i = $total_route_items-1; $i>1; $i--){
        $routes[] = implode('/', array_slice($route_split, 0, $i));
      }
      $db->where_in('pc.route', $routes);
      $db->where('pc.params IS NULL');
      $db->order_by('LENGTH(route)','DESC');
      $page = $db->get('ac_cms_pages p')->row();
      
      if($page){
        $uri = $page->slug . substr($route,strlen($page->route)) . $params;
      }
    }
    return parent::site_url($uri, $protocol);
	}
}