<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$this->_ci->load->model('Options_model');
$citybreak_settings = $this->_ci->Options_model->get('trip_citybreak_settings');
$arival_locations = array();
if(isset($citybreak_settings['arival_locations']) && is_array($citybreak_settings['arival_locations'])){
  $arival_locations = $citybreak_settings['arival_locations'];
}


$in_city_breaks = $this->_module=='Trip' && $this->_controller=='Citybreaks';
if($in_city_breaks){
  $this->_ci->load->model('Trip/Citybreaks_model');
  $search_data = $this->_ci->Citybreaks_model->getSearchData();
  
  $get_location_index = '' . $this->_ci->input->get('arrival');
  if(!$get_location_index){
    $get_location_index = $search_data['arrival'];
  }
}
?>

<li class="nav-item <?php echo $in_city_breaks ? 'active' : ''; ?> dropdown clickable">
  <a class="nav-link dropdown-toggle" href="<?php echo site_url('trip/citybreaks'); ?>" id="cityBreakMN" aria-haspopup="true" aria-expanded="false">City Break</a>
  <div class="dropdown-menu" aria-labelledby="cityBreakMN" style="margin-top: -1px;">
    <?php
    foreach ($arival_locations as $location_index =>$arrival_location){
      // $lindex = substr(strstr($location_index,"-"), 1);
      $lindex = $location_index;
      if(isset($arrival_location['menu']) && $arrival_location['menu']){
        list($country_id, $city_id, $location_id) = explode('-',$location_index);
        $option_text = ($location_id > 0 ? $arrival_location['location'] . ', ' : '') . $arrival_location['city'];
        $custom_text = trim($arrival_location['text']);
        if(strlen($custom_text)>0){
          $option_text = $custom_text;
        }
      ?>
        <a class="dropdown-item <?php echo $in_city_breaks && ($get_location_index == $lindex) ? 'active' : ''; ?>" href="<?php echo site_url('trip/citybreaks?arrival=' . $lindex); ?>"><?php echo $option_text; ?></a>
      <?php
      }
    }
    ?>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>