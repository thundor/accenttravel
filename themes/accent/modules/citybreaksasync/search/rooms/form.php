<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = &$this->citybreak_search_data;
$hotel_details = $this->view_data['hotel_details'];
$data['hotel_id'] = $hotel_details->Id;
$data['destination_city_id'] = $hotel_details->CityId;
$data['min_stars'] = $hotel_details->Stars;
$data['hotel_name'] = '';

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

?>
<form action="#" class="citybreak-search">
  <div class="row">
    <h5 class="pl-3 mt- mb-4 greyCol text-uppercase familyBold">Actualizeaza date cautare</h5>
    <input type="hidden" id="numeHotelCB" value="<?php echo htmlspecialchars($data['hotel_name']); ?>" />
    <input type="hidden" id="citybreakId" value="<?php echo $data['hotel_id']; ?>" />
    <?php /*
    <div style="display:none">
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
    </div>
    */ ?>
    <div class="form-group col-sm-12">
      <input type="text" class="form-control form-control-lg" id="dateZborCB" placeholder="Data plecare" required>
    </div>
    <div class="form-group col-sm-12">
      <input type="text" class="form-control form-control-lg" id="dateZborCB2" placeholder="Data retur" required>
    </div>
    <?php include 'form/rooms.php'; ?>
    <div class="col-12 col-md-6 offset-md-6 btnCaut">
      <button type="submit" class="btn btn-block btn-lg bg-primary fontSize12" id="cautaHotel" name="cautaHotel" role="button"><i class="fa fa-refresh"></i> Actualizeaza date</button>
    </div>
  </div>
</form>
<div id="citybreaks-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>