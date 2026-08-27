<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Sitemap extends MX_Controller {
  public function index() {
    header('Content-Type: application/xml');
      
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
    
    $this->db->select('*');
    $this->db->join('ac_cms_pages_content pc', 'p.page_id = pc.page_id');
    $this->db->where('p.status', 1);
    
    $pages = $this->db->get('ac_cms_pages p')->result();
    
    foreach($pages as $page){
      $uri = $page->slug;
      echo '<url>';
      echo '<loc>' . site_url($uri) . '</loc>';
      echo '<changefreq>weekly</changefreq>';
      echo '<priority>0.5</priority>';
      echo '</url>' . PHP_EOL;
      unset($uri, $page);
    }
    unset($pages);
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('sitemap_settings',null,array(
      'hotels'=>'',
      'flights'=>'',
      'citybreaks'=>'',
      'packages'=>'',
    ));
    $hotels_path = isset($settings['hotels']) && strlen($settings['hotels']) ? $settings['hotels'] : 'trip/hotels';
    $flights_path = isset($settings['flights']) && strlen($settings['flights']) ? $settings['flights'] : 'trip/flights';
    $citybreaks_path = isset($settings['citybreaks']) && strlen($settings['citybreaks']) ? $settings['citybreaks'] : 'trip/citybreaks';
    $packages_path = isset($settings['packages']) && strlen($settings['packages']) ? $settings['packages'] : 'trip/packages';
    
    $hotels_path_arr = explode(',', $hotels_path);
    $citybreaks_path_arr = explode(',', $citybreaks_path);
    $flights_path_arr = explode(',', $flights_path);
    $packages_path_arr = explode(',', $packages_path);
    
    $hotel_settings = $this->Options_model->get('trip_hotel_settings');
    $departure_locations = isset($hotel_settings['departure_locations']) && is_array($hotel_settings['departure_locations']) ? $hotel_settings['departure_locations'] : array();
    foreach($departure_locations as $departure_location){
      $departure = $departure_location['city'];
      $departure = $this->safeUrlItem($departure);
      foreach($hotels_path_arr as $hotels_path_item){
        echo '<url>';
        echo '<loc>' . base_url($hotels_path_item . '/' . $departure) . '</loc>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.5</priority>';
        echo '</url>' . PHP_EOL;
      }
    }
    $citybreak_settings = $this->Options_model->get('trip_citybreak_settings');
    $departure_locations = isset($citybreak_settings['departure_locations']) && is_array($citybreak_settings['departure_locations']) ? $citybreak_settings['departure_locations'] : array();
    $arrival_locations = isset($citybreak_settings['arival_locations']) && is_array($citybreak_settings['arival_locations']) ? $citybreak_settings['arival_locations'] : array();
    
    
    foreach($departure_locations as $departure_location){
      $departure = isset($departure_location['location']) && strlen($departure_location['location']) ? $departure_location['location'] : $departure_location['city'];
      $departure = $this->safeUrlItem($departure);
      foreach($arrival_locations as $arrival_location){
        $arrival = isset($arrival_location['location']) && strlen($arrival_location['location']) ? $arrival_location['location'] : $arrival_location['city'];
        $arrival = $this->safeUrlItem($arrival);
        if($departure === $arrival){
          continue;
        }
        foreach($citybreaks_path_arr as $citybreaks_path_item){
          echo '<url>';
          echo '<loc>' . base_url($citybreaks_path_item . '/' . $departure . '/' . $arrival) . '</loc>';
          echo '<changefreq>weekly</changefreq>';
          echo '<priority>0.5</priority>';
          echo '</url>' . PHP_EOL;
        }
        foreach($flights_path_arr as $flights_path_item){
          echo '<url>';
          echo '<loc>' . base_url($flights_path_item . '/' . $departure . '/' . $arrival) . '</loc>';
          echo '<changefreq>weekly</changefreq>';
          echo '<priority>0.5</priority>';
          echo '</url>' . PHP_EOL;
        }
        unset($arrival, $arrival_location);
      } 
      unset($departure, $departure_location);
    }
    unset($citybreak_settings, $departure_locations, $arrival_locations);
    
    $packages_settings = $this->Options_model->get('trip_packages_settings');
    if($packages_settings){
      $include_package_categories = array();
      if(isset($packages_settings['categories']) && !empty($packages_settings['categories'])){
        $include_package_categories = explode(',', $packages_settings['categories']);
      }
      $this->load->model('Trip/Packages_model');
      $package_categories_result = $this->Packages_model->loadPackageCategories();
      if($package_categories_result){
        foreach($package_categories_result->_embedded->categories as $package_category){
          if(strpos($package_category->Name,'!') !== false){
            continue;
          }
          if($include_package_categories && !in_array($package_category->Id, $include_package_categories)){
            continue;
          }
          foreach($packages_path_arr as $packages_path_item){
            echo '<url>';
            echo '<loc>' . base_url($packages_path_item . '/' . $this->safeUrlItem($package_category->Code)) . '</loc>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.5</priority>';
            echo '</url>' . PHP_EOL;
          }
          
        }
      }
      $include_package_destinations = array();
      if(isset($packages_settings['destinations']) && !empty($packages_settings['destinations'])){
        $include_package_destinations = explode(',', $packages_settings['destinations']);
      }
      $package_destinations_result = $this->Packages_model->loadPackageDestinations();

      if($package_destinations_result){
        foreach($package_destinations_result->_embedded->cities as $package_destination){
          if($include_package_destinations && !in_array($package_destination->Id, $include_package_destinations)){
            continue;
          }
          foreach($packages_path_arr as $packages_path_item){
            echo '<url>';
            echo '<loc>' . base_url($packages_path_item . '/oras/' . $this->safeUrlItem($package_destination->Name)) . '</loc>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.5</priority>';
            echo '</url>' . PHP_EOL;
          }
        }
      }
    }
    echo '</urlset>';
    exit;
  }
  public function promoted_hotels() {
    header('Content-Type: application/xml');
      
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
    
    include APPPATH . "config/promoted_hotels.php";
		if(isset($custom_routes)){
			foreach($custom_routes as $cr_alias => $cr_route){
				echo '<url>';
				echo '<loc>' . site_url($cr_alias) . '</loc>';
				echo '<changefreq>weekly</changefreq>';
				echo '<priority>0.5</priority>';
				echo '</url>' . PHP_EOL;
				unset($uri, $page);
			}
		}
    
    echo '</urlset>';
    exit;
  }
  private function safeUrlItem($val){
    return urlencode(strtolower(str_replace(' ','_', $val)));
  }
}