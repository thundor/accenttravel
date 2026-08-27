<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = &$this->citybreak_search_data;
$data['hotel_id'] = '';
$special_layout = $this->_controller=='Citybreaksasync';
$this->_ci->load->model('Options_model');
$citybreak_settings = $this->_ci->Options_model->get('trip_citybreak_settings');
$arival_locations = array();
if(isset($citybreak_settings['arival_locations']) && is_array($citybreak_settings['arival_locations'])){
  $arival_locations = $citybreak_settings['arival_locations'];
}
$departure_locations = array();
if(isset($citybreak_settings['departure_locations']) && is_array($citybreak_settings['departure_locations'])){
  $departure_locations = $citybreak_settings['departure_locations'];
}
if($special_layout){
  $arrival_index = '' . $this->_ci->input->get('arrival');
  if($arrival_index && $data['arrival'] != $arrival_index){
    $data['arrival'] = $arrival_index;
    $data['index_id'] = '';
    $data['code'] = '';
    if(isset($arival_locations[$arrival_index])){
      list($destination_country_id,$destination_city_id,$destination_location_id) = explode('-', $arrival_index);
      $arrival_location = $arival_locations[$arrival_index];
      $data['destination_country_id'] = $destination_country_id;
      $data['destination_city_id'] = $destination_city_id;
      $data['destination_location_id'] = $destination_location_id;
      
      $data['destination_location_name'] = $arrival_location['location'];
      $data['destination_city_name'] = $arrival_location['city'];
      $data['destination_country_name'] = $arrival_location['country'];
      $data['destination_full_location_name'] = ($destination_location_id > 0 ? $data['destination_location_name'] . ', ' : '') . $data['destination_city_name'];
    }
  }
  $departure_index = '' . $this->_ci->input->get('departure');
  if($departure_index && $data['departure'] != $departure_index){
    $data['departure'] = $departure_index;
    $data['index_id'] = '';
    $data['code'] = '';
    if(isset($departure_locations[$departure_index])){
      list($origin_country_id,$origin_city_id,$origin_location_id) = explode('-', $departure_index);
      $departure_location = $departure_locations[$departure_index];
      $data['origin_country_id'] = $origin_country_id;
      $data['origin_city_id'] = $origin_city_id;
      $data['origin_location_id'] = $origin_location_id;
      
      $data['origin_location_name'] = $departure_location['location'];
      $data['origin_city_name'] = $departure_location['city'];
      $data['origin_country_name'] = $departure_location['country'];
      $data['origin_full_location_name'] = ($origin_location_id > 0 ? $data['origin_location_name'] . ', ' : '') . $data['origin_city_name'];
    }
  }
}
?>
<div class="tab-pane <?php echo $special_layout ? 'active' : ''; ?>" id="city-break" role="tabpanel">
  <form action="#" method="post" class="citybreak-search">
    <input type="hidden" id="citybreakId" value="<?php echo $data['hotel_id']; ?>" />
    <input type="hidden" id="numeHotelCB" value="<?php echo htmlspecialchars($data['hotel_name']); ?>" />
    <div class="row">
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="plecareCB" placeholder="Plecare din" required value="<?php echo htmlspecialchars($data['origin_full_location_name']); ?>">
        <?php /*
        <select id="plecareCB" class="form-control form-control-lg" required name="plecareCB">
          <option value="" selected>Plecare din</option><?php 
          foreach($departure_locations as $location_index=>$departure_location) {
            list($country_id, $city_id, $location_id) = explode('-',$location_index);
            $option_value = $city_id . '-' . $location_id;
            $option_text = ($location_id > 0 ? $departure_location['location'] . ', ' : '') . $departure_location['city'];
            $custom_text = trim($departure_location['text']);
            $selected_expression = $data['departure'] == $option_value ? 'selected="selected"' : '';
            if(strlen($custom_text)>0){
              $option_text = $custom_text;
            } ?>
          <option 
            value="<?php echo $option_value; ?>" 
            <?php echo $selected_expression; ?> 
            data-country_id="<?php echo htmlspecialchars($country_id); ?>"
            data-country="<?php echo htmlspecialchars($departure_location['country']); ?>"
            data-city="<?php echo htmlspecialchars($departure_location['city']); ?>"
            data-location="<?php echo htmlspecialchars($departure_location['location']); ?>"
            ><?php echo $option_text; ?></option>
          <?php } ?>
        </select>
        */ ?>
      </div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="sosireCB" placeholder="Sosire in" required value="<?php echo htmlspecialchars($data['destination_full_location_name']); ?>">
        <?php /*
        <select id="sosireCB" class="form-control  form-control-lg" required name="sosireCB" >
          <option value="" selected>Sosire in</option><?php 
          foreach($arival_locations as $location_index=>$arrival_location) {
            list($country_id, $city_id, $location_id) = explode('-',$location_index);
            $option_value = $city_id . '-' . $location_id;
            $option_text = ($location_id > 0 ? $arrival_location['location'] . ', ' : '') . $arrival_location['city'];
            $custom_text = trim($arrival_location['text']);
            $selected_expression = $data['arrival'] == $option_value ? 'selected="selected"' : '';
            if(strlen($custom_text)>0){
              $option_text = $custom_text;
            } ?>
          <option 
            value="<?php echo $option_value; ?>" 
            <?php echo $selected_expression; ?> 
            data-country_id="<?php echo htmlspecialchars($country_id); ?>"
            data-country="<?php echo htmlspecialchars($arrival_location['country']); ?>"
            data-city="<?php echo htmlspecialchars($arrival_location['city']); ?>"
            data-location="<?php echo htmlspecialchars($arrival_location['location']); ?>"
            ><?php echo $option_text; ?></option>
          <?php } ?>
        </select>
        */ ?>
      </div>
      <?php /*
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="numeHotelCB" placeholder="Nume hotel (optional)" value="<?php echo htmlspecialchars($data['hotel_name']); ?>" />
      </div>
      */ ?>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="dateZborCB" placeholder="Data plecare" required autocomplete="off" />
      </div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="dateZborCB2" placeholder="Data retur" required autocomplete="off" />
      </div>                             
      <?php include 'form/rooms.php'; ?>
			<?php
			if($this->_ci->user->can('backend-access')){ ?>
			<div class="form-group col-0<?php echo $special_layout ? ' col-sm-6 col-md-8' : ' col-sm-6 col-md-8'; ?>">
				<input type="text" class="form-control trip_citybreak_search_link" readonly />
			</div>
			<?php
			}
			?>
      <div class="form-group col-12 <?php echo $special_layout ? 'col-sm-6 col-md-4' . ($this->_ci->user->can('backend-access') ? '' : ' offset-sm-3 offset-md-4') : 'col-md-4' . ($this->_ci->user->can('backend-access') ? ' col-sm-6 ' : ' col-sm-9 offset-sm-3 offset-md-8'); ?> btnCaut">
        <button class=" btn  btn-block btn-lg bg-primary" id="cautaZborCB" name="cautaZborCB"><i class="fa fa-search"></i> Cauta City Break</button>
      </div>
    </div>
  </form>
</div>
<div id="citybreaks-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>