<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$zones = $this->view_data;
$this->_ci->load->model('Country_model');
$romania = $this->_ci->Country_model->getCountries(array(
  'iso_2' => 'RO',
  'select' => array(
    '*',
    'IFNULL(`name_RO`,`name`) as output_name',
  ),
  'return_row' => true,
));
$loaded_zones = array();
$this->_ci->load->model('Trip/Offer_weekend_model');
foreach($zones as $zone => $zone_data){
  if(strpos($zone,'_') == false){
    continue;
  }
  $hotel_offers = $this->_ci->Offer_weekend_model->getOffers(array(
    'select' => '`id`,`type_id`, `name` as "n", `stars` as "s"',
    'type' => 'hotel',
    'zone' => $zone,
  ));
  $package_offers = $this->_ci->Offer_weekend_model->getOffers(array(
    'select' => '`id`,`type_id`, `name` as "n", `category` as "c"',
    'type' => 'package',
    'zone' => $zone,
  ));
  $loaded_zones[$zone] = array(
    'hotels' => $hotel_offers,
    'packages' => $package_offers,
  );
}
?>
<script>
;(function($){
  var zones = <?php echo json_encode($zones); ?>;
  var loaded_zones = <?php echo json_encode($loaded_zones); ?>;
  console.log(zones);
  var romania = <?php echo json_encode($romania); ?>;
  var $offers_weekend_form = $('#offers_weekend_form');
  var $weekend_result = $('#weekend_result');
  var $weekend_add_hotel_id = $('#weekend_add_hotel_id');
  var $weekend_add_hotel = $('#weekend_add_hotel');
  var $weekend_add_package_id = $('#weekend_add_package_id');
  var $weekend_add_package = $('#weekend_add_package');
  var $hotel_result_model = $('#hotel_result_model');
  var $package_result_model = $('#package_result_model');
  var $weekend_results = $('#weekend_results');
  var $zone_model = $('#zone_model');
  var loaded_result = true;
  
  function fixZone($zone, zone, zone_code, open){
    var accordion_tab_id = 'zone_' + zone_code;
    var accordion_id = accordion_tab_id.split('_').splice(-1,1).join('_');
    var total_ = accordion_tab_id.split('_').length;
    if(accordion_id == 'zone'){
      accordion_id = 'weekend_results'
    }
    var $card_header = $('>.card-header', $zone);
    
    var placeholder = zone.city_name;
    if(total_ == 2){
      placeholder = zone.continent_name;
    } else if(total_ == 3){
      placeholder = zone.country_name;
    }
    $('input[name=name]', $card_header).attr({
      'name': 'zone[' + zone_code +'][name]',
    }).val(placeholder);
    $('input[name=text]', $card_header).attr({
      'name': 'zone[' + zone_code +'][text]',
      'placeholder': placeholder
    }).val(zone.text);
    $('input[name=zone_ordering]', $card_header).attr({
      'name': 'zone[' + zone_code +'][order]'
    }).val(zone.order);
    $('input[name=enabled]', $card_header).attr({
      'name': 'zone[' + zone_code +'][enabled]'
    }).prop('checked', zone.enabled);
    $card_header.attr('id', accordion_tab_id + '_header');
    $card_header.next('div').attr({
      'id' : accordion_tab_id + '_collapse',
      'aria-labelledby' : accordion_tab_id + '_header'
    }).toggleClass('show', open ? true : false);
    $('a.zone-toggler', $card_header).attr({
      'href' : '#' + accordion_tab_id + '_collapse',
      'data-parent' : '#' + accordion_id,
      'aria-controls' : '#' + accordion_tab_id + '_collapse',
      'aria-expanded' : open ? 'true' : 'false',
    }).toggleClass('collapsed', !open ? true : false);
  }
  function getOrCreateZone(zone, open){
    if(zone.country_code){
      var zone_id = zone.continent_code + '_' + zone.country_code;
      var $existing_zone = $('#zone_' + zone_id + '_content');
      if($existing_zone.length){
        return $existing_zone;
      }
    }
    
    var $existing_continent_zone = $('#zone_' + zone.continent_code + '_content');
    if(!$existing_continent_zone.length){
      var $new_zone = $zone_model.clone().removeAttr('id').addClass('zone-continent');
      $new_zone.addClass('alert-info');
      $('.zone-name', $new_zone).text(zone.continent_name);
      fixZone($new_zone, zone, zone.continent_code, open);
      $new_zone.prependTo($weekend_results);
      $existing_continent_zone = $('.zone-content', $new_zone);
      $existing_continent_zone.attr('id', 'zone_' + zone.continent_code + '_content');
    }
    if(!zone.country_code){
      return $existing_continent_zone;
    }
    
    var $new_zone = $zone_model.clone().removeAttr('id').addClass('zone-country');
    $new_zone.addClass('alert-success');
    $('.zone-name', $new_zone).text(zone.country_name);
    fixZone($new_zone, zone, zone.continent_code + '_'  + zone.country_code, open);
    $new_zone.prependTo($existing_continent_zone);
    $existing_country_zone = $('.zone-content', $new_zone);
    $existing_country_zone.attr('id', 'zone_' + zone.continent_code + '_'  + zone.country_code + '_content');
    return $existing_country_zone;
  }
  
  $weekend_add_hotel.on('click', function(){
    if(!loaded_result){
      showMessage($weekend_result, 'Asteptati sa se finalizeze cautarea precedenta', 'warning');
      return;
    }
    loaded_result = false;
    $weekend_result.empty();
    var hotel_id = $weekend_add_hotel_id.val();
    if(!hotel_id || isNaN(hotel_id) || hotel_id<0){
      loaded_result = true;
      showMessage($weekend_result, 'Introduceti un ID hotel valid', 'danger');
      return;
    }
    $.ajax({
      url: "<?php echo site_url('backend/offers/weekend/loadHotel'); ?>",
      dataType: "json",
      data: {
        id: parseInt(hotel_id)
      }
    }).done(function( resp ) {
      loaded_result = true;
      if(resp.status !== 'success'){
        showMessage($weekend_result, resp.message, 'danger');
        return;
      }
      var hotel = resp.data.hotel;
      var country = resp.data.country;
      if(country){
        var zone_details = {
          continent_code: country.continent,
          continent_name: country.continent_name,
          country_code: country.iso_2,
          country_name: country.output_name
        }
      } else {
        var zone_details = {
          continent_code: '',
          continent_name: 'Necunoscut',
          country_code: hotel.CountryCode,
          country_name: hotel.CountryName
        }
      }
      zone_details.enabled = 1;
      zone_details.text = '';
      var $zone = getOrCreateZone(zone_details, true);
      var zone_code = zone_details.continent_code + '_' + zone_details.country_code;
      
      var $hotel_box = $hotel_result_model.clone().removeAttr('id');
      $('input[name=hotel_name]', $hotel_box).attr({
        'name': 'zone[' + zone_code +'][hotels][' + hotel.Id + '][n]',
      }).val(hotel.Name);
      $('input[name=hotel_stars]', $hotel_box).attr({
        'name': 'zone[' + zone_code +'][hotels][' + hotel.Id + '][s]',
      }).val(hotel.Stars);
      $('.hotel-name', $hotel_box).text(hotel.Name);
      $('.hotel-stars', $hotel_box).html(" " + Array(parseInt(hotel.Stars || 0) + 1).join('<i class="fa fa-star"></i>'));
      $('.hotel-stars', $hotel_box).addClass('text-warning');
      $('.hotel-link', $hotel_box).attr('href',"<?php echo site_url('trip/hotel'); ?>?id=" + hotel.Id);
      $hotel_box.prependTo($zone);
      
    }).fail(function(jqXHR, textStatus, errorThrown){
      loaded_result = true;
      console.log('loadHotel',jqXHR, textStatus, errorThrown);
      showMessage($weekend_result, 'Eroare in preluarea hotelului', 'danger');
    });
  });
  $weekend_add_package.on('click', function(){
    if(!loaded_result){
      showMessage($weekend_result, 'Asteptati sa se finalizeze cautarea precedenta', 'warning');
      return;
    }
    loaded_result = false;
    $weekend_result.empty();
    var package_id = $weekend_add_package_id.val();
    if(!package_id || isNaN(package_id) || package_id<0){
      loaded_result = true;
      showMessage($weekend_result, 'Introduceti un ID package valid', 'danger');
      return;
    }
    $.ajax({
      url: "<?php echo site_url('backend/offers/weekend/loadPackage'); ?>",
      dataType: "json",
      data: {
        id: parseInt(package_id)
      }
    }).done(function( resp ) {
      loaded_result = true;
      if(resp.status !== 'success'){
        showMessage($weekend_result, resp.message, 'danger');
        return;
      }
      console.log(resp);
      
      var package = resp.data.package;
      var country = romania;
      var zone_details = {
        continent_code: country.continent,
        continent_name: country.continent_name,
        country_code: country.iso_2,
        country_name: country.output_name
      }
      zone_details.enabled = 1;
      zone_details.text = '';
      var $zone = getOrCreateZone(zone_details, true);
      var zone_code = zone_details.continent_code + '_' + zone_details.country_code;
      var $package_box = $package_result_model.clone().removeAttr('id');
      
      $('input[name=package_name]', $package_box).attr({
        'name': 'zone[' + zone_code +'][packages][' + package.Id + '][n]',
      }).val(package.Name);
      $('input[name=package_category]', $package_box).attr({
        'name': 'zone[' + zone_code +'][packages][' + package.Id + '][c]',
      }).val(package.Category);
      
      $('.package-name', $package_box).text(package.Name);
      $('.package-category', $package_box).text(package.Category);
      $('.package-link', $package_box).attr('href',"<?php echo site_url('trip/package'); ?>?id=" + package.Id);
      
      $package_box.prependTo($zone);
    }).fail(function(jqXHR, textStatus, errorThrown){
      loaded_result = true;
      console.log('loadPackage',jqXHR, textStatus, errorThrown);
      showMessage($weekend_result, 'Eroare in preluarea vacantei', 'danger');
    });
  });
  $weekend_results.on('click', '.remove-card', function(){
    var offer_id = $(this).data('offer_id');
    if(offer_id && offer_id>0){
      var $input = $('<input type="hidden" name="zone[delete][]" />').val(offer_id);
      $input.appendTo($('#offers_delete'));
    }
    $(this).closest('.card').remove();
  });
  for(var zone_code in zones){
    if(!zones.hasOwnProperty(zone_code)) {
      continue;
    }
    var zone = zones[zone_code];
    console.log(zone);
    var zone_code_split = zone_code.split('_');
    var total_ = zone_code_split.length;
    var open = false;
    if(total_ == 1){
      var open = true;
      var zone_details = {
        continent_code: zone_code,
        continent_name: zone.name
      }
    } else {
      var zone_details = {
        continent_code: zone_code_split[0],
        country_code: zone_code_split[1],
        country_name: zone.name
      }
    }
    zone_details.order = zone.order;
    zone_details.text = zone.text ? zone.text : '';
    zone_details.enabled = zone.enabled && zone.enabled == '1' ? 1 : 0;
    var $zone = getOrCreateZone(zone_details, open);
    if(zone.hotels){
      for(var hotel_id in zone.hotels){
        if(!zone.hotels.hasOwnProperty(hotel_id)) {
          continue;
        }
        var hotel = zone.hotels[hotel_id];
        var $hotel_box = $hotel_result_model.clone().removeAttr('id');
        $('input[name=hotel_name]', $hotel_box).attr({
          'name': 'zone[' + zone_code +'][hotels][' + hotel_id + '][n]',
        }).val(hotel.n);
        $('input[name=hotel_stars]', $hotel_box).attr({
          'name': 'zone[' + zone_code +'][hotels][' + hotel_id + '][s]',
        }).val(hotel.s);
        $('.hotel-name', $hotel_box).text(hotel.n);
        $('.hotel-stars', $hotel_box).html(" " + Array(parseInt(hotel.s) + 1).join('<i class="fa fa-star"></i>'));
        $('.hotel-stars', $hotel_box).addClass('text-warning');
        $('.hotel-link', $hotel_box).attr('href',"<?php echo site_url('trip/hotel'); ?>?id=" + hotel_id);
        $hotel_box.appendTo($zone);
      }
    }
    if(zone.packages){
      for(var package_id in zone.packages){
        if(!zone.packages.hasOwnProperty(package_id)) {
          continue;
        }
        var package = zone.packages[package_id];
        var $package_box = $package_result_model.clone().removeAttr('id');
        $('input[name=package_name]', $package_box).attr({
          'name': 'zone[' + zone_code +'][packages][' + package_id + '][n]',
        }).val(package.n);
        $('input[name=package_category]', $package_box).attr({
          'name': 'zone[' + zone_code +'][packages][' + package_id + '][c]',
        }).val(package.c);
        $('.package-name', $package_box).text(package.n);
        $('.package-category', $package_box).text(package.c);
        $('.package-link', $package_box).attr('href',"<?php echo site_url('trip/package'); ?>?id=" + package_id);
        $package_box.appendTo($zone);
      }
    }
  }
  for(var zone_code in loaded_zones){
    if(!loaded_zones.hasOwnProperty(zone_code)) {
      continue;
    }
    var zone = loaded_zones[zone_code];
    console.log(zone);
    var zone_code_split = zone_code.split('_');
    var total_ = zone_code_split.length;
    var open = false;
    if(total_ == 1){
      var open = true;
      var zone_details = {
        continent_code: zone_code,
        continent_name: zone.name
      }
    } else {
      var zone_details = {
        continent_code: zone_code_split[0],
        country_code: zone_code_split[1],
        country_name: zone.name
      }
    }
    zone_details.order = zone.order;
    zone_details.text = zone.text ? zone.text : '';
    zone_details.enabled = zone.enabled && zone.enabled == '1' ? 1 : 0;
    var $zone = getOrCreateZone(zone_details, open);
    if(zone.hotels){
      for(var id in zone.hotels){
        if(!zone.hotels.hasOwnProperty(id)) {
          continue;
        }
        var hotel = zone.hotels[id];
        var hotel_id = hotel.type_id;
        var $hotel_box = $hotel_result_model.clone().removeAttr('id');
        $('.remove-card',  $hotel_box).attr('data-offer_id', hotel.id);
        $('input[name=hotel_name]', $hotel_box).remove();
        $('input[name=hotel_stars]', $hotel_box).remove();
        $('.hotel-name', $hotel_box).text(hotel.n);
        $('.hotel-stars', $hotel_box).html(" " + Array(parseInt(hotel.s || 0) + 1).join('<i class="fa fa-star"></i>'));
        $('.hotel-stars', $hotel_box).addClass('text-warning');
        $('.hotel-link', $hotel_box).attr('href',"<?php echo site_url('trip/hotel'); ?>?id=" + hotel_id);
        $hotel_box.appendTo($zone);
      }
    }
    if(zone.packages){
      for(var id in zone.packages){
        if(!zone.packages.hasOwnProperty(id)) {
          continue;
        }
        var package = zone.packages[id];
        var package_id = package.type_id;
        var $package_box = $package_result_model.clone().removeAttr('id');
        $('.remove-card',  $package_box).attr('data-offer_id', package.id);
        $('input[name=package_name]', $package_box).remove();
        $('input[name=package_category]', $package_box).remove();
        $('.package-name', $package_box).text(package.n);
        $('.package-category', $package_box).text(package.c);
        $('.package-link', $package_box).attr('href',"<?php echo site_url('trip/package'); ?>?id=" + package_id);
        $package_box.appendTo($zone);
      }
    }
  }
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>