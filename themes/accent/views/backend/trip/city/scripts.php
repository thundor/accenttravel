<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$airline = $this->view_data['airline'];
$can_write = $this->_method !='view';
$editing = trim($airline->code) !== '';
if($can_write){
  themeFunctions::loadModule('helpers/countries/json', __FILE__ . '/json_selections');
?>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<script>
(function($){
  $('#country_iso_2').select2_4({theme:'bootstrap',placeholder:'Alegeti', data: select2_countries_selections, width: '100%', allowClear:true, minimumResultsForSearch:1});
  // $('#parent_id').select2_4({theme:'bootstrap',placeholder:'Alegeti', data: select2_countries_selections, width: '100%'});
  $('#trip_city').autocomplete({
    source: function(request, response){
      $.ajax({
        url: "<?php echo site_url('backend/trip/cities/loadTripCities'); ?>",
        dataType: "json",
        data: {
          q: request.term
        },
        success: function( result ) {
          console.log(result);
          if(!result.status || result.status !== 'success'){
            return;
          }
          var data = result.data.results;
          var response_data = [];
          if(data && data.length){
            for (var i=0; i < data.length; i++){
              var item = data[i];
              var label = item.TripCityName + ' (' + item.CountryName + ')';
              var response_item = {
                id: item.TripCityId,
                country_id: item.CountryId,
                country_name: item.CountryName,
                country_code: item.CountryCode,
                trip_city_id: item.TripCityId,
                trip_country_id: item.TripCountryId,
                trip_city_name: item.TripCityName,
                trip_country_name: item.TripCountryName,
                value: item.TripCityName,
                label: label
              };
              response_data.push(response_item);
            }
          }
          response( response_data );
        }
      });
    },
    minLength: 2,
    select: function( event, ui ) {
      $('#trip_city_id').val(ui.item.trip_city_id);
      $('#trip_country_id').val(ui.item.trip_country_id);
      $('#trip_city_name').val(ui.item.trip_city_name);
      $('#trip_country_name').val(ui.item.trip_country_name);
      if(ui.item.country_id){
        $('#country_id').val(ui.item.country_id);
        $('#country_name').val(ui.item.country_name);
        $('#country_iso_2').val(ui.item.country_code).trigger('change.select2_4');
      }
    }
  }).blur(function(){
    if(!this.value.length || this.value !== $('#trip_city_name').val()){
      this.value = '';
    }
  });
  function determineAidaCityVisibility(){
    $('#aida_city').prop('readonly', !($('#aida_country_id').val()>0));
  }
  determineAidaCityVisibility();
  function determineAidaCityDescriptionButtonVisibility(){
    $('#aida_city_button_get_description').prop('disabled', !($('#aida_city_id').val()>0));
  }
  determineAidaCityDescriptionButtonVisibility();
  $('#aida_country').autocomplete({
    source: function(request, response){
      $.ajax({
        url: "<?php echo site_url('backend/trip/cities/loadAidaCountries'); ?>",
        dataType: "json",
        data: {
          q: request.term
        },
        success: function( result ) {
          console.log(result);
          if(!result.status || result.status !== 'success'){
            return;
          }
          var data = result.data.results;
          var response_data = [];
          if(data && data.length){
            for (var i=0; i < data.length; i++){
              var item = data[i];
              var response_item = {
                id: item.Id,
                country_id: item.CountryId,
                country_code: item.CountryCode,
                country_name: item.CountryName,
                value: item.Name,
                label: item.Name
              };
              response_data.push(response_item);
            }
          }
          response( response_data );
        }
      });
    },
    minLength: 0,
    select: function( event, ui ) {
      $('#aida_country_name').val(ui.item.value);
      $('#aida_country_id').val(ui.item.id);
      if(ui.item.country_id){
        $('#country_id').val(ui.item.country_id);
        $('#country_name').val(ui.item.country_name);
        $('#country_iso_2').val(ui.item.country_code).trigger('change.select2_4');
      }
      determineAidaCityVisibility();
    }
  }).blur(function(){
    if(!this.value.length || this.value !== $('#aida_country_name').val()){
      this.value = '';
    }
    determineAidaCityVisibility();
  }).on('focus', function() { $(this).keydown(); });
  $('#aida_city_button_get_description').on('click',function(){
    $.ajax({
      url: "<?php echo site_url('backend/trip/cities/loadAidaCity'); ?>",
      dataType: "json",
      data: {
        city_id: $('#aida_city_id').val(),
        country_id: $('#aida_country_id').val()
      }
    }).done(function(result){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        return;
      }
      $('#aida_city_description textarea').val(result.data.Description);
      $('#aida_city_description').show();
      scrollToIfNecessary($('#aida_city_description'));
    })
  });
  $('#aida_city').autocomplete({
    source: function(request, response){
      $.ajax({
        url: "<?php echo site_url('backend/trip/cities/loadAidaCities'); ?>",
        dataType: "json",
        data: {
          q: request.term,
          country_id: $('#aida_country_id').val()
        },
        success: function( result ) {
          console.log(result);
          if(!result.status || result.status !== 'success'){
            return;
          }
          var data = result.data.results;
          var response_data = [];
          if(data && data.length){
            for (var i=0; i < data.length; i++){
              var item = data[i];
              var response_item = {
                id: item.Id,
                value: item.Name,
                label: item.Name
              };
              response_data.push(response_item);
            }
          }
          response( response_data );
        }
      });
    },
    minLength: 0,
    select: function( event, ui ) {
      $('#aida_city_name').val(ui.item.value);
      $('#aida_city_id').val(ui.item.id);
      determineAidaCityDescriptionButtonVisibility();
    }
  }).blur(function(){
    if(!this.value.length || this.value !== $('#aida_city_name').val()){
      this.value = '';
    }
    determineAidaCityDescriptionButtonVisibility();
  }).on('focus', function() { $(this).keydown(); });
  
  var $action_buttons = $('button[type=submit][form=citiesForm]');
  $action_buttons.prop('disabled', false);
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  