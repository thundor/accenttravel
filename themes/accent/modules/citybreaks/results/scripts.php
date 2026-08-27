<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$google_maps_key = 'AIzaSyBEBBKL4GwgmqVIN5cbc7KpSPapec8jmxo';
?>
<div id="modalMapCity">
  <div class="row">
    <div class="col-sm-12">
      <span class="btn btn-outline-danger float-right mb-3">Inchide</span>
      <h4 id="modalMapCityTitle" class="float-left">&nbsp;</h4>
    </div>
    <?php /*
    <div class="col-sm-12 col-lg-3 mb-3">
      <img src="<?php echo $this->theme_url; ?>assets/images/barcelona.jpg"  alt="Barcelona, Spania" style="width:100%;" />
    </div>
    <div class="col-sm-12 col-lg-6  mb-3 modalMapCityDescription">
    <p>Barcelona is the capital of Catalonia and the second largest city in Spain, after Madrid, with a population of 1,621,537, being the sixth-most populous urban area in the European Union after Paris, London, the Ruhr, Madrid and Milan. It is located on the Mediterranean coast between the mouths of the rivers Llobregat and Besòs and is bounded to the west by the Serra de Collserola ridge.</p>
    <p>Barcelona is the capital of Catalonia and the second largest city in Spain, after Madrid, with a population of 1,621,537, being the sixth-most populous urban area in the European Union after Paris, London, the Ruhr, Madrid and Milan. It is located on the Mediterranean coast between the mouths of the rivers Llobregat and Besòs and is bounded to the west by the Serra de Collserola ridge.</p>
    </div>

    <div class="col-sm-12 col-lg-3  mb-3">
      <h4>Număr hoteluri: <span id="modalMapCityHotelCount">2416</span></h4>
      <h4>Atractii in <span id="modalMapCityName">Barcelona</span></h4>
      <ul class="list-unstyled" id="modalMapCityAttractions">
        <li>Atractia 1 </li>
        <li>Atractia 2 </li>
        <li>Atractia 3 </li>
        <li>Atractia 4 </li>
        <li>Atractia 5</li>
      </ul>
    </div>
    */ ?>
    <div class="col-sm-12">
      <div id="googleMap1" style="height:450px;"></div>
      <?php /* <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2859.3589151938504!2d28.629450015918646!3d44.22026847910596!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40baf06f7feeed13%3A0x3df8e75b98a91ec1!2sHotel+Perla!5e0!3m2!1sen!2sro!4v1503488624252" height="450" style="border:0; width:100%;" allowfullscreen></iframe> */ ?>
    </div>
  </div>
</div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key; ?>&callback=initMap">
</script>
<script type="text/javascript">
var notification_title;
var hotel_results;
var map_index=0;
var google_map;
var google_map_marker;
var google_map_location_markers;
function initMap(){
  console.log('googleMaps loaded');
}
(function($){
function getFlightDurationString(string){
  var str_arr = string.split(':');
  var h = parseInt(str_arr[0]);
  var m = parseInt(str_arr[1]);
  return (h ? h + 'h ' : '') + m + 'min';
}
function getSegmentFlightNumberString(segment){
  var flight_number = segment.Flight.Number;
  var string = '<b>' + segment.Carrier.Marketing.Code + flight_number + '</b>';
  if(segment.Carrier.Operating && segment.Carrier.Marketing.Code != segment.Carrier.Operating.Code){
    string += ' Operat de ' + segment.Carrier.Operating._;
  }
  return string;
}

function interpretRouteAvailability(flight_type,route_ref, $box_ticket, flight_index){
  var comb = flight_type + route_ref;
  $('.row.' + (flight_type == 0 ? 'retur' : 'tur') + ' input[type=radio][name^=flight_choose]', $box_ticket).each(function(){
    var rmatches = this.name.match(/flight_choose\[(\d+)\]/);
    var rflight_type = rmatches[1];
    var rroute_ref = this.value;
    var rcomb = rflight_type + rroute_ref;
    var combination = flight_type == 0 ? comb + '|' + rcomb : rcomb + '|' + comb;
    var has_no_comb = hotel_results.results.flights[flight_index].Combinations.indexOf(combination) < 0;
    var $parent_row = $(this).closest('.row.' + (flight_type == 0 ? 'retur' : 'tur'));
    if(!has_no_comb){
      $parent_row.removeClass('nocomb').removeAttr('title');
    } else {
      $parent_row.addClass('nocomb');
      $parent_row.attr('title','Aceasta ruta nu este compatibila cu ruta de ' + ( rflight_type == 0 ? 'retur' : 'plecare') + ' aleasa.');
      $(this).prop('checked', false).removeAttr('checked');
    }
  });
}
$('#hotelResults').on('change','input[type=radio][name^=flight_choose]', function(){
  if(!$(this).is(':checked')){
    return;
  }
  if(hotel_results.data.go_only){
    return;
  }
  var matches = this.name.match(/flight_choose\[(\d+)\]/);
  var flight_type = matches[1];
  var $box_ticket = $(this).closest('.boxTicket').first();
  var flight_index = $box_ticket.data('flight_index');
  var route_ref = this.value;
  var $tichet_row = $(this).closest('.row.' + (flight_type == 0 ? 'tur' : 'retur')).first();
  if($tichet_row.hasClass('nocomb')){
    var $rows = $('.row.' + (flight_type == 0 ? 'tur' : 'retur'), $box_ticket);
    $rows.removeClass('nocomb');
    $rows.each(function(){
      var $row = $(this);
      if($row.data('uiTooltipOpen')){
        $row.removeData('uiTooltipTitle');
        $('#' + $row.data('uiTooltipId')).remove();
        $row.removeData('uiTooltipId');
      }
    });
  }
  interpretRouteAvailability(flight_type,route_ref, $box_ticket, flight_index);
});
function interpretResults(){
  var response = hotel_results.results;
  var placeholder_image = response.placeholder_image;
  var $navigation = $('ul.pagination');
  if($navigation.data("twbs-pagination")){
    $navigation.twbsPagination('destroy');
  }
  var page = parseInt(response.page);
  var total_pages = parseInt(response.page_count);
  if(total_pages && total_pages>=page){
    $navigation.twbsPagination({
      startPage: page,
      totalPages: total_pages,
      visiblePages: 20,
      first: "<<",
      prev: "<",
      next: ">",
      last: ">>",
      onPageClick: function (evt, page) {
        if(page == response.page){
          return;
        }
        setCityBreakSearchStatus(false);
        citybreak_search_data.page = page;
        loadResults();
      }
    });
  }
  $('#hotelResults').empty();
  
  $('.rezCount > .filterTitle').text('Am gasit ' + response.total_items + ' oferte de City Break in ' + citybreak_search_data.destination_full_location_name);
  $('.rezCount > .mapInfo > span').text(citybreak_search_data.destination_full_location_name.toUpperCase());
  var start_date = moment(citybreak_search_data.start_date,'Y-MM-DD');
  $('.rezCount .selected_date_start').text(start_date.locale('ro').format("dddd, DD MMMM Y"));
  var end_date = moment(citybreak_search_data.end_date,'Y-MM-DD');
  $('.rezCount .selected_date_end').text(end_date.locale('ro').format("dddd, DD MMMM Y"));
  $('.rezCount .selected_rooms').text(citybreak_search_data.occupancy.length + ' camera');
  var travellers = 0;
  var adults = 0;
  var children = 0;
  for (var i=0; i<citybreak_search_data.occupancy.length; i++){
    var occupants = citybreak_search_data.occupancy[i];
    adults += 1 * occupants.adt;
    if(occupants.chd && occupants.chd.age){
      children += 1 * occupants.chd.age.length;
    }
  }
  travellers = adults + children;
  $('.rezCount .selected_passengers').text(travellers + ' calatori');
  var nights = end_date.diff(start_date,'days');
  $('.rezCount .selected_date_interval').text('(' + nights + ' nopti)');
  
  var $hotel_box_model = $('#hotelResultModel').clone().removeAttr('id style');
  
  notification_title = citybreak_search_data.destination_city_name + ', ' + citybreak_search_data.destination_country_name;
  notification_title += ', ' + citybreak_search_data.occupancy.length + ' ' + (citybreak_search_data.occupancy.length > 1 ? 'camere' : 'camera');
  notification_title += ', ' + start_date.locale('ro').format("dddd, DD MMMM Y");
  notification_title += ', ' + travellers + ' ' + (travellers > 1 ? 'persoane' : 'persoana');
  notification_title += ', ' + nights + ' ' + (nights > 1 ? 'nopti' : 'noapte');
  
  for (var i=0; i<response.hotels.length; i++){
    var hotel = response.hotels[i];
    $hotel_box_model.attr('action',hotel.link);
    var $hotel_box = $hotel_box_model.clone();
    $('.hartaHotel', $hotel_box).attr('data-lat', hotel.Lat);
    $('.hartaHotel', $hotel_box).attr('data-lng', hotel.Lng);
    $('.hartaHotel', $hotel_box).attr('data-city', citybreak_search_data.destination_full_location_name);
    $('.hartaHotel', $hotel_box).attr('data-address', hotel.Address);
    $('.hartaHotel', $hotel_box).attr('data-name', hotel.Name);
    $('.hotel-image', $hotel_box).attr('href',hotel.link)
        .css('background-image',  'url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>)')
        .addClass('lazy')
        .attr('data-src', hotel.Image);
//        .children('img').attr({'src' : hotel.Image ? hotel.Image : placeholder_image, alt: hotel.Name });
    $('.hotel-name', $hotel_box).text(hotel.Name);
    $('.hotel-info-short', $hotel_box).text(hotel.ShortDesc ? hotel.ShortDesc.substring(0,150) : '');
    $('.hotel-info-rest', $hotel_box).text(hotel.ShortDesc ? hotel.ShortDesc.substring(150): '').hide();
    if($('.hotel-info-rest', $hotel_box).is(':empty')){
      $('.hotel-info > a', $hotel_box).hide();
    } else {
      $('.hotel-info > a', $hotel_box).on('click', function(e){
        e.preventDefault();
        $(this).hide();
        $(this).next().show();
      });
    }
    $('.hotel-name', $hotel_box).attr('href',hotel.link);
    // $('.reserve-button', $hotel_box).attr('href',hotel.link);
    $('.hotel-stars', $hotel_box).html(" " + Array(parseInt(hotel.Stars) + 1).join('<i class="fa fa-star"></i>'));
    $('.notification-button', $hotel_box).attr({
      'id': 'button_notification_hotel_' + hotel.Id,
      'data-type': 'citybreak',
      'data-ref_id': hotel.Id,
      'data-hotel_name': hotel.Name + (hotel.Stars ? ' (' + hotel.Stars + ' stele)' : '') + ' ' + hotel.Address,
      'data-amount': hotel.MinPrice,
      'data-amount_hotel': hotel.Price,
      'data-amount_flight': hotel.FlightPrice,
      'data-currency': hotel.Currency,
      'data-link': hotel.link
    });
//    $('.hotel-room-type', $hotel_box).append('camera standard');
//    $('.hotel-dinner', $hotel_box).append('<i class="fa fa-bed"></i> Fara masa');
//    $('.hotel-info', $hotel_box).remove();
    $('.initial-price', $hotel_box).remove();
    $('.hotel-location', $hotel_box).text(hotel.Address);
    $('.current-price', $hotel_box).text(format_price(Math.ceil(hotel.MinPrice), hotel.Currency));
    $('.hotel-info-accomodation', $hotel_box).html(Array(parseInt(adults) + 1).join('<i class="fa fa-user-o"></i>'))
        .append(" " + adults + ' '  + (adults == 1 ? 'adult' : 'adulti') + ' / ' + nights + ' ' + (nights == 1 ? 'noapte' : 'nopti') + ' / Camera standard');
    $('#hotelResults').append($hotel_box);
  }
  $('.rezCount').show();
  $('.sortHotel').show();
  setCityBreakSearchStatus(true);
  showFlightsResults();
  $('#hotelResults .lazy').lazy();
  $('#hotelsResultsWrapper').show();
}
var show_warnings = true;
function interpretNoHotelsResponse(result,initial){
  setCityBreakSearchStatus(true);
  if(initial && result && result.data && (result.data.hotels_expired || result.data.flights_expired)){
    show_warnings = false;
  }
  if(show_warnings){
    $('#hotelWarnings').show();
  }
  show_warnings = true;
  $('#hotelsResultsWrapper').hide();
}
function loadResults(initial){
  $('#hotelWarnings').hide();
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/loadResults'); ?>',
    method: 'post',
    dataType: 'json',
    data: citybreak_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        interpretNoHotelsResponse(result,initial);
        return;
      }
      citybreak_search_data = result.data;
      hotel_results = result;
      interpretResults();
      if(initial){
        loadFilters();
      }
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR,textStatus,error);
      setCityBreakSearchStatus(true);
    }
  }).done(function(){
    if(google_map_location_markers){
      for(var i=0;i<google_map_location_markers.length;i++){
        google_map_location_markers[i].setMap(null);
        google_map_location_markers.splice(i--,1);
      }
    }
    if(google_map1_location_markers){
      for(var i=0;i<google_map1_location_markers.length;i++){
        google_map1_location_markers[i].setMap(null);
        google_map1_location_markers.splice(i--,1);
      }
    }
  });
}
var hotel_markers;
function loadMarkers(){
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/loadMarkers'); ?>',
    method: 'post',
    dataType: 'json',
    data: citybreak_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        setCityBreakSearchStatus(true);
        return;
      }
      hotel_markers = result.response;
    }
  });
}
var hotel_filters;
function loadFilters(){
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/loadFilters'); ?>',
    method: 'post',
    dataType: 'json',
    data: citybreak_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        setCityBreakSearchStatus(true);
        return;
      }
      var filters = result.results;
      hotel_filters = filters;
      if(!filters){
        filters = {};
      }
      if(!filters.minPrice){
        filters.minPrice = 0;
      }
      if(citybreak_search_data.filters.min_price < filters.minPrice){
        citybreak_search_data.filters.min_price = filters.minPrice;
      }
      if(!filters.maxPrice){
        filters.maxPrice = 0;
      }
      if(citybreak_search_data.filters.max_price > filters.maxPrice || citybreak_search_data.filters.max_price < citybreak_search_data.filters.min_price){
        citybreak_search_data.filters.max_price = filters.maxPrice;
      }
      var min_price = Math.ceil(parseFloat(filters.minPrice));
      var max_price = Math.ceil(parseFloat(filters.maxPrice));

      var $price_slider = $("#slider-range").slider('option',{
        min: min_price,
        max: max_price,
        values: [citybreak_search_data.filters.min_price, citybreak_search_data.filters.max_price],
      });
      $price_slider.trigger('updatePrice');
      
      if(!filters.stars){
        filters.stars = [];
      }
      $('.hotel-stars-filter .checkWrapper').remove();
      var max_stars = parseInt(filters.stars[filters.stars.length-1]);
      if(max_stars < 5){
        max_stars = 5;
      }
      for(var i=0; i<filters.stars.length; i++){
        var star = parseInt(filters.stars[i]);
        var checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox" name="stars" id="hotelStars' + star + '" value="' + star + '"/>').prop('checked', citybreak_search_data.filters.stars.indexOf(star)>-1).appendTo(checkWrapper);
        var checkLabel = $('<label for="hotelStars' + star + '" />').appendTo(checkWrapper);
        checkLabel.append(Array(star+1).join('<i class="fa fa-star yellowCol noFloat"></i>&nbsp;'));
        checkLabel.append(Array(max_stars-star+1).join('<i class="fa fa-star noFloat"></i>&nbsp;'));
        $('.hotel-stars-filter > .hotel-filters-content').append(checkWrapper);
      }
      if(!filters.facilities){
        filters.facilities = [];
      }
      $('.hotel-facilities-filter .checkWrapper').remove();
      for(var i=0; i<filters.facilities.length; i++){
        var facility = filters.facilities[i];
        var facility_id = parseInt(facility.Id);
        var facility_name = facility.Name;
        var facility_icon = facility.Icon ? facility.Icon : false;
        var facility_icon_src = facility.IconSrc ? facility.IconSrc : false;
        var checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox" name="facilities" id="hotelFacilities' + facility_id + '" value="' + facility_id + '"/>').prop('checked', citybreak_search_data.filters.facilities.indexOf(facility_id)>-1).appendTo(checkWrapper);
        var checkLabel = $('<label for="hotelFacilities' + facility_id + '" />').appendTo(checkWrapper);
        if(facility_icon){
          var facility_stack_icons = facility_icon.split('|');
          if(facility_stack_icons.length > 1){
            var stack = $('<span class="fa-stack" />');
            for(var j=0; j<facility_stack_icons.length; j++){
              var stack_icon = facility_stack_icons[j];
              var icon = $('<i />');
              icon.addClass(stack_icon);
              stack.append(icon);
            }
            checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(facility_icon);
            checkLabel.append(icon);
          }
        }
        if(facility_icon_src){
          var icon = $('<img />');
          icon.attr('src',facility_icon_src);
          icon.attr('alt',facility_name);
          checkLabel.append(icon);
        }
        checkLabel.append(facility_name);
//        checkLabel.append(' (' + facility_id + ')');
        $('.hotel-facilities-filter > .hotel-filters-content').append(checkWrapper);
      }
      if(!filters.activity_categories){
        filters.activity_categories = [];
      }
      $('.hotel-activitycategories-filter .checkWrapper').remove();
      for(var i=0; i<filters.activity_categories.length; i++){
        var activity = filters.activity_categories[i];
        var activity_id = parseInt(activity.id);
        var activity_name = activity.name;
        var Icon = activity.icon ? activity.icon : {};
        var activity_icon = Icon.i ? Icon.i : false;
        var activity_icon_src = Icon.Src ? Icon.Src : false;
        var checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox" name="activity_categories" id="hotelactivitycategories' + activity_id + '" value="' + activity_id + '"/>')
          .attr('data-activities', activity.activity_ids.join(','))
          .prop('checked', citybreak_search_data.filters.activity_categories.indexOf(activity_id)>-1).appendTo(checkWrapper);
        var checkLabel = $('<label for="hotelactivitycategories' + activity_id + '" />').appendTo(checkWrapper);
        if(activity_icon){
          var activity_stack_icons = activity_icon.split('|');
          if(activity_stack_icons.length > 1){
            var stack = $('<span class="fa-stack" />');
            for(var j=0; j<activity_stack_icons.length; j++){
              var stack_icon = activity_stack_icons[j];
              var icon = $('<i />');
              icon.addClass(stack_icon);
              stack.append(icon);
            }
            checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(activity_icon);
            checkLabel.append(icon);
          }
        }
        if(activity_icon_src){
          var icon = $('<img />');
          icon.attr('src',activity_icon_src);
          icon.attr('alt',activity_name);
          checkLabel.append(icon);
        }
        checkLabel.append(activity_name);
//        checkLabel.append(' (' + activity_id + ')');
        $('.hotel-activitycategories-filter > .hotel-filters-content').append(checkWrapper);
      }
      var $stopsBox = $('.hotel-stops-filter > .hotel-filters-content');
      if($stopsBox.length){
        $stopsBox.empty();
        for(var i=0; i<hotel_results.results.stops.length; i++){
          var numar_escale = hotel_results.results.stops[i];
          var text = 'Zbor direct';
          if(numar_escale == 1){
            text = '1 Escala'; 
          } else if(numar_escale > 1){
            text = numar_escale + ' Escale'; 
          }
          var $wrapper = $('<div class="checkWrapper" />');
          var $input = $('<input type="checkbox" value="' + numar_escale + '" name="filters[stops][]" id="flights_filter_stops_' + numar_escale + '"/>');
          $input.appendTo($wrapper);
          var $label = $('<label for="flights_filter_stops_' + numar_escale + '">').html(text);
          $label.appendTo($wrapper);
          $wrapper.appendTo($stopsBox);
        }
      }
      /* if(!filters.locations){
        filters.locations = [];
      }
      $('.hotel-locations-filter .checkWrapper').remove();
      for(var i=0; i<filters.locations.length; i++){
        var activity = filters.locations[i];
        var activity_id = parseInt(activity.id);
        var activity_name = activity.name;
        var Icon = activity.icon ? activity.icon : {};
        var activity_icon = Icon.i ? Icon.i : false;
        var activity_icon_src = Icon.Src ? Icon.Src : false;
        var checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox" name="locations" id="hotellocations' + activity_id + '" value="' + activity_id + '"/>').prop('checked', citybreak_search_data.filters.activity_categories.indexOf(activity_id)>-1).appendTo(checkWrapper);
        var checkLabel = $('<label for="hotellocations' + activity_id + '" />').appendTo(checkWrapper);
        if(activity_icon){
          var activity_stack_icons = activity_icon.split('|');
          if(activity_stack_icons.length > 1){
            var stack = $('<span class="fa-stack" />');
            for(var j=0; j<activity_stack_icons.length; j++){
              var stack_icon = activity_stack_icons[j];
              var icon = $('<i />');
              icon.addClass(stack_icon);
              stack.append(icon);
            }
            checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(activity_icon);
            checkLabel.append(icon);
          }
        }
        if(activity_icon_src){
          var icon = $('<img />');
          icon.attr('src',activity_icon_src);
          icon.attr('alt',activity_name);
          checkLabel.append(icon);
        }
        checkLabel.append(activity_name);
        checkLabel.append($('<p />').append(filters.location_names[activity_id].join(',')));
//        checkLabel.append(' (' + activity_id + ')');
        $('.hotel-locations-filter > .hotel-filters-content').append(checkWrapper);
      }
      */
      if(!filters.pois){
        filters.pois = [];
      }
      $('.hotel-pois-filter .checkWrapper').remove();
      for(var i=0; i<filters.pois.length; i++){
        var poi = filters.pois[i];
        var poi_id = parseInt(poi.PoiId);
        var poi_name = poi.Name;
        var poi_icon = poi.Icon ? poi.Icon : false;
        var poi_icon_src = poi.IconSrc ? poi.IconSrc : false;
        var checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox" name="pois" id="hotelpois' + poi_id + '" value="' + poi_id + '"/>').prop('checked', citybreak_search_data.filters.pois.indexOf(poi_id)>-1).appendTo(checkWrapper);
        var checkLabel = $('<label for="hotelpois' + poi_id + '" />').appendTo(checkWrapper);
        if(poi_icon){
          var poi_stack_icons = poi_icon.split('|');
          if(poi_stack_icons.length > 1){
            var stack = $('<span class="fa-stack" />');
            for(var j=0; j<poi_stack_icons.length; j++){
              var stack_icon = poi_stack_icons[j];
              var icon = $('<i />');
              icon.addClass(stack_icon);
              stack.append(icon);
            }
            checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(poi_icon);
            checkLabel.append(icon);
          }
        }
        if(poi_icon_src){
          var icon = $('<img />');
          icon.attr('src',poi_icon_src);
          icon.attr('alt',poi_name);
          checkLabel.append(icon);
        }
        checkLabel.append(poi_name);
//        checkLabel.append(' (' + poi_id + ')');
        $('.hotel-pois-filter > .hotel-filters-content').append(checkWrapper);
      }
      setFilters();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR,textStatus,error);
      setCityBreakSearchStatus(true);
    }
  });
}
function setSearchAndInitiate(){
  $('#hotelWarnings').hide();
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/setSearchAndInitiate'); ?>',
    method: 'post',
    dataType: 'json',
    data: citybreak_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        interpretNoHotelsResponse(result);
        return;
      }
      citybreak_search_data = result.data;
			loadResults(true);
      // hotel_results = result;
      // loadFilters();
      // loadMarkers();
      // interpretResults();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setCityBreakSearchStatus(true);
    }
  });
}
function setSort(){
  var sort_element = $('.hotel-sort-by').filter(function(){return $(this).val()>0;}).first();
  if(sort_element.length){
    citybreak_search_data.sort_by = sort_element.attr('name');
    citybreak_search_data.sort_order = parseInt(sort_element.val()) - 1;
  }
}
function setFilters(){
  citybreak_search_data.filters.stars = [];
  $('.hotel-stars-filter input[type=checkbox]:checked').each(function(){
    citybreak_search_data.filters.stars.push(parseInt(this.value));
  });
  citybreak_search_data.filters.facilities = [];
  $('.hotel-facilities-filter input[type=checkbox]:checked').each(function(){
    citybreak_search_data.filters.facilities.push(parseInt(this.value));
  });
  citybreak_search_data.filters.activity_categories = [];
  citybreak_search_data.filters.activities = [];
  $('.hotel-activitycategories-filter input[type=checkbox]:checked').each(function(){
    citybreak_search_data.filters.activity_categories.push(parseInt(this.value));
    citybreak_search_data.filters.activities = citybreak_search_data.filters.activities.concat($(this).attr('data-activities').split(','));
  });
  citybreak_search_data.filters.locations = [];
  $('.hotel-locations-filter input[type=checkbox]:checked').each(function(){
    citybreak_search_data.filters.locations.push(parseInt(this.value));
  });
  citybreak_search_data.filters.pois = [];
  $('.hotel-pois-filter input[type=checkbox]:checked').each(function(){
    citybreak_search_data.filters.pois.push(parseInt(this.value));
  });
  citybreak_search_data.filters.stops = [];
  $('.hotel-stops-filter input[type=checkbox]:checked').each(function(){
    citybreak_search_data.filters.stops.push(parseInt(this.value));
  });
  var $price_slider = $("#slider-range").slider();
  var price_values = $price_slider.slider('values');
  citybreak_search_data.filters.min_price = parseFloat(price_values[0]);
  citybreak_search_data.filters.max_price = parseFloat(price_values[1]);
  <?php
  if($this->_ci->user->can('backend-access')){ ?>
  setCityBreakFiltersLink();
  <?php } ?>
}
function resetFilters(){
  $('.hotel-stars-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-facilities-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-activitycategories-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-locations-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-pois-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-stops-filter input[type=checkbox]:checked').prop('checked',false);
  var $price_slider = $("#slider-range").slider();
  var min_price = $price_slider.slider('option','min');
  var max_price = $price_slider.slider('option','max');
  
  $price_slider.slider('option',{
    min: min_price,
    max: max_price,
    values: [min_price, max_price],
  });
}
$('.hotel-stars-filter').on('change', 'input[type=checkbox]',function(){
  setCityBreakSearchStatus(false);
  setFilters();
  citybreak_search_data.page = 1;
  loadResults();
});
$('.hotel-facilities-filter, .hotel-activitycategories-filter, .hotel-locations-filter, .hotel-pois-filter, .hotel-stops-filter').on('change', 'input[type=checkbox]',function(){
  setCityBreakSearchStatus(false);
  setFilters();
  citybreak_search_data.page = 1;
  loadResults();
});
citybreak_submit_function = function (e){
  if(!search_is_over){
    console.log('A previous search is not complete. Ignoring request.');
    return;
  }
  $('#hotelsResultsWrapper').hide();
  $('#hotelResults').empty();
  $('.rezCount').hide();
  $('ul.pagination').empty();
  $('.sortHotel').hide();
  setCityBreakSearchStatus(false);
  setCityBreakData($(this));
  setCityBreakSearchAndRedirect();
};
$('#applyFilters').click(function(){
  setCityBreakSearchStatus(false);
  setFilters();
  citybreak_search_data.page = 1;
  loadResults();
  var body = $("html, body");
  var pagination_top = $('h1.filterTitle').first().offset().top;
  body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
  });
});
$('#resetFilters').click(function(){
  setCityBreakSearchStatus(false);
  resetFilters();
  setFilters();
  citybreak_search_data.page = 1;
  loadResults();
  var body = $("html, body");
  var pagination_top = $('h1.filterTitle').first().offset().top;
  body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
  });
});
$('.hotel-sort-by').prop('disabled', false).on('change', function(){
  setCityBreakSearchStatus(false);
  var $me = $(this);
  if($me.val() === '0'){
    $me.val('1');
  }
  $('.hotel-sort-by').filter(function(){return !$(this).is($me);}).val(0);
  setSort();
  citybreak_search_data.page = 1;
  loadResults();
});
$(document).on("click",'.inchideH', function () {
  $(this).parents(".boxHotel").hide("slow");
});
$("#hotelResults").on("click", ".hartaHotel", function () {
  $('#modalMapH').show();
  var $me = $(this);
  var myLatLng = {
    lat: parseFloat($me.data('lat')), 
    lng: parseFloat($me.data('lng'))
  };
  if(!google_map){
    google_map = new google.maps.Map(document.getElementById('googleMap'), {
      zoom: 10,
      center: myLatLng
    });
  }
  google_map.setZoom(15);
  google_map.setCenter(myLatLng);
  if(google_map_marker){
    google_map_marker.setMap(null);
    google_map_marker = null;
  }
  google_map_marker = new google.maps.Marker({
    position: myLatLng,
    map: google_map,
    title: $me.data('name') + ', ' + $me.data('address')
  });
  if(!google_map_location_markers){
    google_map_location_markers = [];
    if(hotel_filters){
      if(hotel_filters.pois && hotel_filters.pois.length)
      for(var i=0; i<hotel_filters.pois.length; i++){
        var poi = hotel_filters.pois[i];
        if(poi.Latitude && poi.Longitude){
          var poiLatLang = {
            lat: parseFloat(poi.Latitude),
            lng: parseFloat(poi.Longitude)
          };
          google_map_location_markers.push(new google.maps.Marker({
            icon: {
              path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
              strokeColor:"#B40404",
              strokeWeight:2,
              scale: 5
            },
            position: poiLatLang,
            map: google_map,
            title: poi.Name
          }));
        }
      }
      if(hotel_filters.activities && hotel_filters.activities.length)
      for(var i=0; i<hotel_filters.activities.length; i++){
        var poi = hotel_filters.activities[i];
        if(poi.Latitude && poi.Longitude){
          var poiLatLang = {
            lat: parseFloat(poi.Latitude),
            lng: parseFloat(poi.Longitude)
          };
          google_map_location_markers.push(new google.maps.Marker({
            icon: {
              path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
              strokeColor:"#0275d8",
              strokeWeight:2,
              scale: 5
            },
            position: poiLatLang,
            map: google_map,
            title: poi.Name
          }));
        }
      }
    }
  }
  $('#modalMapH h4').text('Harta Hotel ' + $me.data('name') + ', ' + $me.data('address'));
//  initializeGoogleMaps($me.data('lat'), $me.data('lng'),$me.data('name'));
  /* var src = 'https://www.google.com/maps/embed/v1/';
  var addFlight = $('#addAvionHotel').is(":checked");
  var flightCity = $('#inpZborHot').val();
  var destinationCity = $('#destinatie').val();
  if(addFlight){
    src += 'directions?origin=' + encodeURIComponent(flightCity) + '&destination=' + encodeURIComponent($me.data('name')) + ', ' + encodeURIComponent($me.data('address')) + '&';
  } else {
    src += 'place?';
    src += 'q=' + encodeURIComponent($me.data('name')) + ', ' + encodeURIComponent($me.data('address')) + '&';
  }
  src += 'center=' + encodeURIComponent($me.data('lat')) + ',' + encodeURIComponent($me.data('lng')) + '&';
  src += 'zoom=10' + '&';
  src += 'key=<?php echo $google_maps_key; ?>';
  $('#modalMapH iframe').attr('src',src); */
});
function sablonDouaEscale(segments){
  var flight_1 = segments[0];
  var flight_1_company_code = flight_1.Carrier.Marketing.Code;
  var flight_1_company_image = getCompanyImageByCode(flight_1_company_code);
  var flight_1_company_name = flight_1.Carrier.Marketing._;
  var flight_1_departure_date = moment(flight_1.Origin.Date + ' ' + flight_1.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_arrival_date = moment(flight_1.Destination.Date + ' ' + flight_1.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_duration_in_minutes = flight_1_arrival_date.diff(flight_1_departure_date,'minutes');
  var flight_1_duration_hours = parseInt(flight_1_duration_in_minutes/60);
  var flight_1_duration_minutes = flight_1_duration_in_minutes - 60 * flight_1_duration_hours;
  var flight_2 = segments[1];
  var flight_2_company_code = flight_2.Carrier.Marketing.Code;
  var flight_2_company_image = getCompanyImageByCode(flight_2_company_code);
  var flight_2_company_name = flight_2.Carrier.Marketing._;
  var flight_2_departure_date = moment(flight_2.Origin.Date + ' ' + flight_2.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_2_wait_duration_in_minutes = flight_2_departure_date.diff(flight_1_arrival_date,'minutes');
  var flight_1_2_wait_duration_hours = parseInt(flight_1_2_wait_duration_in_minutes/60);
  var flight_1_2_wait_duration_minutes = flight_1_2_wait_duration_in_minutes - 60 * flight_1_2_wait_duration_hours;
  var flight_2_arrival_date = moment(flight_2.Destination.Date + ' ' + flight_2.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_2_duration_in_minutes = flight_2_arrival_date.diff(flight_2_departure_date,'minutes');
  var flight_2_duration_hours = parseInt(flight_2_duration_in_minutes/60);
  var flight_2_duration_minutes = flight_2_duration_in_minutes - 60 * flight_2_duration_hours;
  var flight_3 = segments[2];
  var flight_3_company_code = flight_3.Carrier.Marketing.Code;
  var flight_3_company_image = getCompanyImageByCode(flight_3_company_code);
  var flight_3_company_name = flight_3.Carrier.Marketing._;
  var flight_3_departure_date = moment(flight_3.Origin.Date + ' ' + flight_3.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_2_3_wait_duration_in_minutes = flight_3_departure_date.diff(flight_2_arrival_date,'minutes');
  var flight_2_3_wait_duration_hours = parseInt(flight_2_3_wait_duration_in_minutes/60);
  var flight_2_3_wait_duration_minutes = flight_2_3_wait_duration_in_minutes - 60 * flight_2_3_wait_duration_hours;
  var flight_3_arrival_date = moment(flight_3.Destination.Date + ' ' + flight_3.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_3_duration_in_minutes = flight_3_arrival_date.diff(flight_3_departure_date,'minutes');
  var flight_3_duration_hours = parseInt(flight_3_duration_in_minutes/60);
  var flight_3_duration_minutes = flight_3_duration_in_minutes - 60 * flight_3_duration_hours;
  
  return '\
  <div class="infoEscale col-12" style="display: none;">\
    <div class="infoPE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/plecareW.png" alt="plecare">\
      <span>\
        <strong>' + flight_1_departure_date.format('HH:mm') + '</strong>\
        <br />' + 
        flight_1.Origin.Airport._ +
        '<br />' + 
        flight_1.Origin.Airport.City +
      '</span>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector"><br>\
        <span>' +
          (flight_1_company_image ? '<img src="' + flight_1_company_image + '" title="' + flight_1_company_name + '" alt="' + flight_1_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_1.Aircraft ? flight_1.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_1) + '\
        </span>\
      </div>\
    </div>\
    <div class="timeEsc1">\
      <div class="row">\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/sosire.png" alt="sosire">\
          <br />\
          <span>' + flight_1_arrival_date.format('HH:mm') + '</span>\
          <br />\
          <span>Sosire in</span>\
        </div>\
        <div class="col-6">\
          <img src="<?php echo $this->theme_url; ?>assets/images/waiting.png" alt="asteptare">\
          <br />\
          <span>' + (flight_1_2_wait_duration_hours ? flight_1_2_wait_duration_hours + 'h:' : '') + flight_1_2_wait_duration_minutes + 'min' + '</span>\
          <br />\
          <span><strong>' + flight_1.Destination.Airport.City + ', ' + flight_1.Destination.Airport._ + '</strong></span>\
        </div>\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="sosire">\
          <br />\
          <span>' + flight_2_departure_date.format('HH:mm') + '</span>\
          <br />\
          <span>Plecare din</span>\
        </div>\
      </div>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector" alt="Conector">\
        <br />\
        <span>' +
          (flight_2_company_image ? '<img src="' + flight_2_company_image + '" title="' + flight_2_company_name + '" alt="' + flight_2_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_2.Aircraft ? flight_2.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_2) + '\
        </span>\
      </div>\
    </div>\
    <div class="timeEsc1">\
      <div class="row">\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/sosire.png" alt="sosire">\
          <br />\
          <span>' + flight_2_arrival_date.format('HH:mm') + '</span>\
          <br />\
          <span>Sosire in</span>\
        </div>\
        <div class="col-6">\
          <img src="<?php echo $this->theme_url; ?>assets/images/waiting.png" alt="asteptare">\
          <br />\
          <span>' + (flight_2_3_wait_duration_hours ? flight_2_3_wait_duration_hours + 'h:' : '') + flight_2_3_wait_duration_minutes + 'min' + '</span>\
          <br />\
          <span><strong>' + flight_2.Destination.Airport.City + ', ' + flight_2.Destination.Airport._ + '</strong></span>\
        </div>\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="sosire">\
          <br />\
          <span>' + flight_3_departure_date.format('HH:mm') + '</span>\
          <br />\
          <span>Plecare din</span>\
        </div>\
      </div>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector" alt="Conector">\
        <br />\
        <span>' +
          (flight_3_company_image ? '<img src="' + flight_3_company_image + '" title="' + flight_3_company_name + '" alt="' + flight_3_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_3.Aircraft ? flight_3.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_3) + '\
        </span>\
      </div>\
    </div>\
    <div class="infoSE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/sosireW.png" alt="sosire">\
      <span>\
        <strong>' + flight_3_arrival_date.format('HH:mm') + '</strong>\
        <br />' +
        flight_3.Destination.Airport.City + 
        ',\
        <br />'
        + flight_3.Destination.Airport._ +
      '</span>\
    </div>\
  </div>\
';
}
function sablonOEscala(segments){
  var flight_1 = segments[0];
  var flight_1_company_code = flight_1.Carrier.Marketing.Code;
  var flight_1_company_image = getCompanyImageByCode(flight_1_company_code);
  var flight_1_company_name = flight_1.Carrier.Marketing._;
  var flight_1_departure_date = moment(flight_1.Origin.Date + ' ' + flight_1.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_arrival_date = moment(flight_1.Destination.Date + ' ' + flight_1.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_duration_in_minutes = flight_1_arrival_date.diff(flight_1_departure_date,'minutes');
  var flight_1_duration_hours = parseInt(flight_1_duration_in_minutes/60);
  var flight_1_duration_minutes = flight_1_duration_in_minutes - 60 * flight_1_duration_hours;
  var flight_2 = segments[1];
  var flight_2_company_code = flight_2.Carrier.Marketing.Code;
  var flight_2_company_name = flight_2.Carrier.Marketing._;
  var flight_2_company_image = getCompanyImageByCode(flight_2_company_code);
  var flight_2_departure_date = moment(flight_2.Origin.Date + ' ' + flight_2.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_2_wait_duration_in_minutes = flight_2_departure_date.diff(flight_1_arrival_date,'minutes');
  var flight_1_2_wait_duration_hours = parseInt(flight_1_2_wait_duration_in_minutes/60);
  var flight_1_2_wait_duration_minutes = flight_1_2_wait_duration_in_minutes - 60 * flight_1_2_wait_duration_hours;
  var flight_2_arrival_date = moment(flight_2.Destination.Date + ' ' + flight_2.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_2_duration_in_minutes = flight_2_arrival_date.diff(flight_2_departure_date,'minutes');
  var flight_2_duration_hours = parseInt(flight_2_duration_in_minutes/60);
  var flight_2_duration_minutes = flight_2_duration_in_minutes - 60 * flight_2_duration_hours;
  return '<div class="infoEscale" style="display: none;">\
    <div class="infoPE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/plecareW.png" alt="plecare">\
      <span>\
        <strong>' + flight_1_departure_date.format('HH:mm') + '</strong>\
        <br />' + 
        flight_1.Origin.Airport._ +
        '<br />' + 
        flight_1.Origin.Airport.City +
      '</span>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector">\
        <br />\
        <span>' +
          (flight_1_company_image ? '<img src="' + flight_1_company_image + '" title="' + flight_1_company_name + '" alt="' + flight_1_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_1.Aircraft ? flight_1.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_1) + '\
        </span>\
      </div>\
    </div>\
    <div class="timeEsc1" style="width: 60%;">\
      <div class="row">\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/sosire.png" alt="sosire">\
          <br />\
          <span>' + flight_1_arrival_date.format('HH:mm') + '</span>\
          <br />\
          <span>Sosire in</span>\
        </div>\
        <div class="col-6">\
          <img src="<?php echo $this->theme_url; ?>assets/images/waiting.png" alt="asteptare">\
          <br />\
          <span>' + (flight_1_2_wait_duration_hours ? flight_1_2_wait_duration_hours + 'h:' : '') + flight_1_2_wait_duration_minutes + 'min' + '</span>\
          <br />\
          <span><strong>' + flight_1.Destination.Airport.City + ', ' + flight_1.Destination.Airport._ + '</strong></span>\
        </div>\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="sosire">\
          <br />\
          <span>' + flight_2_departure_date.format('HH:mm') + '</span>\
          <br />\
          <span>Plecare din</span>\
        </div>\
      </div>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector" alt="Conector">\
        <br />\
        <span>' +
          (flight_2_company_image ? '<img src="' + flight_2_company_image + '" title="' + flight_2_company_name + '" alt="' + flight_2_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_2.Aircraft ? flight_2.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_2) + '\
        </span>\
      </div>\
    </div>\
    <div class="infoSE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/sosireW.png" alt="sosire">\
      <span>\
        <strong>' + flight_2_arrival_date.format('HH:mm') + '</strong>\
        <br />' +
        flight_2.Destination.Airport.City + 
        ',\
        <br />'
        + flight_2.Destination.Airport._ +
      '</span>\
    </div>\
  </div>';
}

function getCompanyImageByCode(company_code){
  var company_image;
  var company_index = hotel_results.results.companies_indexes[company_code];
  if(typeof company_index !== 'undefined'){
    company_image = hotel_results.results.companies[company_index].img;
  }
  return company_image;
}
function showFlightsResults(){
  var flights = hotel_results.results.flights;
  var $flight_results = $('.flightResults');
  $flight_results.empty();
  for(var i=0; i<flights.length;i++){
    var flight = flights[i];
    var route_types = flight.Routes;
    var flights_str_arr = [];
    var combination_selected = flight.Combinations[0];
    var combination_arr = combination_selected.split('|');
    for(var j=0; j<route_types.length; j++){
      var route_type = route_types[j];
      var routes = route_type.Route;
      var k_index = -1;
      for(var k=0; k<routes.length; k++){
        var route = routes[k];
        var route_ref = parseInt(route.Ref);
        if(!j && typeof flight.DepRoutes[route_ref] === 'undefined'){
          continue;
        }
        if(j && typeof flight.RetRoutes[route_ref] === 'undefined'){
          continue;
        }
        k_index++;
        var cabin_types = [];
        var company_code = route.Segment[0].Carrier.Marketing.Code;
        var company_name = route.Segment[0].Carrier.Marketing._;
        var departure_date = moment(route.Segment[0].Origin.Date + ' ' + route.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss');
        var arrival_date = moment(route.Segment[route.Segment.length-1].Origin.Date + ' ' + route.Segment[route.Segment.length-1].Origin.Time,'Y-MM-DD HH:mm:ss');
        var company_image = getCompanyImageByCode(company_code);
        var escale = route.Segment.length - 1;
        for(var l=0; l<=escale; l++){
          var escala = route.Segment[l];
          if(cabin_types.indexOf(escala.Flight.CabinType) < 0){
            cabin_types.push(escala.Flight.CabinType);
          }
        }
        var ischecked = combination_arr[j] == '' + j + route.Ref;
        if(!j){
          var box_ticket_row_html = '<div class="row tur" \
              data-company="' + company_code + '"\
              data-stops="' + escale + '"\
              >\
              <div class="col-11 col-sm-3 dataPL">\
                <div class="iconsFlight">\
                  <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="Data plecare" />' +
                  (company_image ? '<img src="' + company_image + '" alt="' + company_name + '" title="' + company_name + '" />' : '') +
                '</div>\
                <p style="text-transform:capitalize;">\
                  <span>PLECARE</span>\
                  <br/>' +
                  departure_date.locale('ro').format('dddd') +
                  '<br/>\
                  <strong style="text-transform:capitalize;">' + departure_date.locale('ro').format('D MMM') + '</strong>\
                  , ' + departure_date.format('Y') +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 oraPL">\
                <p>\
                  <span>\
                    <strong>' +
                      departure_date.format('HH:mm') +
                    '</strong>\
                  </span>\
                  <br />' + 
                  route.Segment[0].Origin.Airport._ +
                  '<br />' + 
                  route.Segment[0].Origin.Airport.City +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 escale">' +
                (!escale ? '<span class="text-center">Zbor Direct</span>' :
                  '<span class="text-center">' + escale + ' ' + (escale > 1 ? 'Escale' : 'Escala') + '</span>\
                  <a class="warning detEscale" name="detEscale"><i class="fa fa-info-circle"></i> Detalii</a>') +
                '<br /><small class="text-center">Durata zbor <i class="fa fa-clock-o"></i> ' + getFlightDurationString(route.Duration) + '</small>' +
              '</div>\
              <div class="col-4 col-sm-3 oraPL">\
                <p>\
                  <span>\
                    <strong style="text-transform:capitalize;">' +
                      arrival_date.format('HH:mm') + '/' +arrival_date.locale('ro').format('D MMM') + 
                    '</strong>\
                    , ' + arrival_date.format('Y') + 
                  '</span>\
                  <br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport._ +
                  '<br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport.City +
                '</p>\
              </div>\
              <div class="col-12 col-sm-2 alegeBT">\
                <p class="text-center">\
                  <label for="flight_result_choose_' + i + '_'+ j + '_' + k + '">Alege</label>\
                  <br >\
                  <input type="radio" ' + (ischecked ? 'checked="checked"' : '') + ' name="flight_choose[' + j + ']" value="' + route.Ref + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" />\
                </p>\
              </div>' +
              (escale === 1 ? sablonOEscala(route.Segment) : '') + 
              (escale === 2 ? sablonDouaEscale(route.Segment) : '') + 
            '</div>';
			box_ticket_row_html += '<span class="familyLight fontSize12 ">';
			  box_ticket_row_html += '<b>Clasa</b>: ' + cabin_types.join(', ') +',';
			if(flight && flight.BrandedFare && flight.BrandedFare.BrandDetails  && flight.BrandedFare.BrandDetails.length && flight.BrandedFare.BrandDetails[j] && (flight.BrandedFare.BrandDetails[j].Code || flight.BrandedFare.BrandDetails[j].Description)){
				box_ticket_row_html +=  (flight.BrandedFare.BrandDetails[j].Code ? ' <b>Fare Family</b>: ' + flight.BrandedFare.BrandDetails[j].Code + '' : '')
				+ (flight.BrandedFare.BrandDetails[j].Description ? ', ' + flight.BrandedFare.BrandDetails[j].Description + '' : '')
			}
			  box_ticket_row_html += '</span><br />';
          flights_str_arr.push(box_ticket_row_html);
        } else {
          var is_available = true;
          if(!ischecked){
            if(flight.Combinations.indexOf(combination_arr[0] + '|' + '1' + route.Ref) < 0){
              is_available = false;
            }
          }
          var box_ticket_row_html = '<div class="row retur' + (j && !k_index ? ' mt-4' : '') + (is_available ? '' : ' nocomb') + '"' + (is_available ? '' : ' title="Aceasta ruta nu este compatibila cu ruta de plecare aleasa."') + '>\
              <div class="col-11 col-sm-3 dataPL">\
                <div class="iconsFlight">\
                  <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="Data plecare" />' +
                  (company_image ? '<img src="' + company_image + '" alt="' + company_name + '" title="' + company_name + '" />' : '') +
                '</div>\
                <p style="text-transform:capitalize;">\
                  <span>RETUR</span>\
                  <br/>\
                  <small>' + departure_date.locale('ro').format('dddd') + '</small>\
                  <br/>\
                  <strong>' + departure_date.locale('ro').format('D MMM') + '</strong>\
                  , ' + departure_date.format('Y') +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 oraPL">\
                <p>\
                  <span>\
                    <strong>' +
                      departure_date.format('HH:mm') +
                    '</strong>\
                  </span>\
                  <br />' + 
                  route.Segment[0].Origin.Airport._ +
                  '<br />' + 
                  route.Segment[0].Origin.Airport.City +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 escale">' +
                (!escale ? '<span class="text-center">Zbor Direct</span>' :
                  '<span class="text-center">' + escale + ' ' + (escale > 1 ? 'Escale' : 'Escala') + '</span>\
                  <a class="warning detEscale" name="detEscale"><i class="fa fa-info-circle"></i> Detalii</a>') +
                '<br /><small class="text-center">Durata zbor <i class="fa fa-clock-o"></i> ' + getFlightDurationString(route.Duration) + '</small>' +
              '</div>\
              <div class="col-4 col-sm-3 oraPL">\
                <p>\
                  <span>\
                    <strong style="text-transform:capitalize;">' +
                      arrival_date.format('HH:mm') + '/' +arrival_date.locale('ro').format('D MMM') + 
                    '</strong>\
                    , ' + arrival_date.format('Y') + 
                  '</span>\
                  <br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport._ +
                  '<br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport.City +
                '</p>\
              </div>\
              <div class="col-12 col-sm-2 alegeBT">\
                <p class="text-center">\
                  <label for="flight_result_choose_' + i + '_'+ j + '_' + k + '">Alege</label>\
                  <br >\
                  <input type="radio" ' + (ischecked ? 'checked="checked"' : '') + ' name="flight_choose[' + j + ']" value="' + route.Ref + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" />\
                </p>\
              </div>' +
              (escale === 1 ? sablonOEscala(route.Segment) : '') + 
              (escale === 2 ? sablonDouaEscale(route.Segment) : '') + 
            '</div>';
			box_ticket_row_html += '<span class="familyLight fontSize12 ">';
			  box_ticket_row_html += '<b>Clasa</b>: ' + cabin_types.join(', ') +',';
			if(flight && flight.BrandedFare && flight.BrandedFare.BrandDetails  && flight.BrandedFare.BrandDetails.length && flight.BrandedFare.BrandDetails[j] && (flight.BrandedFare.BrandDetails[j].Code || flight.BrandedFare.BrandDetails[j].Description)){
				box_ticket_row_html +=  (flight.BrandedFare.BrandDetails[j].Code ? ' <b>Fare Family</b>: ' + flight.BrandedFare.BrandDetails[j].Code + '' : '')
				+ (flight.BrandedFare.BrandDetails[j].Description ? ', ' + flight.BrandedFare.BrandDetails[j].Description + '' : '')
			}
			  box_ticket_row_html += '</span><br />';
          flights_str_arr.push(box_ticket_row_html);
        }
      }
    }
    
    var box_html = '<div class="boxTicket" data-flight_index="' + i + '" data-price="' + flight.Price + '" >\
        <div class="row">\
          <div class="col-12 col-sm-12 col-lg-12">\
            <div class="dashedBB mb-3">\
              <h4>Info zbor</h4>\
            </div>' +
            flights_str_arr.join('<hr />') +
          '</div>\
        </div>\
        <input type="hidden" name="flight_code" value="' + hotel_results.data.flight_code + '"/>\
        <input type="hidden" name="combinations" value="' + flight.Combinations.join(',') + '"/>\
        <input type="hidden" name="itinerary_code" value="' + flight.ItineraryCode + '"/>\
      </div>';
    $flight_results.each(function(index){
      $(this).append(box_html
        .replace(new RegExp(/flight_result_choose_/,'g'), 'flight_result_choose_' + index + '_')
      );
    });
  }
  // setFlightsSearchStatus(true);
  // $('#flightsResultsWrapper').show();
}
$("#modalMapH .btn").on("click", function () {
  $('#modalMapH').hide();
});
var google_map1;
var google_map1_marker;
var google_map1_location_markers;
$(".mapInfo").on("click", function () {
  $('#modalMapCity #modalMapCityTitle').text(citybreak_search_data.destination_full_location_name + ', ' + citybreak_search_data.destination_country_name);
  $('#modalMapCity #modalMapCityName').text(citybreak_search_data.destination_full_location_name);
  $('#modalMapCity #modalMapCityHotelCount').text(hotel_results.results.total_items);
  $('#modalMapCity #modalMapCityAttractions').empty();
  
  var myLatLng = {
    lat: 0, 
    lng: 0
  };
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address': citybreak_search_data.destination_full_location_name + ', ' + citybreak_search_data.destination_country_name}, function(results, status) {
    $('#modalMapCity').show();
    if (status == google.maps.GeocoderStatus.OK){
      myLatLng = {
        lat: parseFloat(results[0].geometry.location.lat()), 
        lng: parseFloat(results[0].geometry.location.lng())
      }
      
      if(!google_map1){
        google_map1 = new google.maps.Map(document.getElementById('googleMap1'), {
          zoom: 11,
          center: myLatLng
        });
      }
      google_map1.setZoom(11);
      google_map1.setCenter(myLatLng);
      console.log(myLatLng);
      if(google_map1_marker){
        google_map1_marker.setMap(null);
        google_map1_marker = null;
      }
      google_map1_marker = new google.maps.Marker({
        position: myLatLng,
        map: google_map1,
        title: citybreak_search_data.destination_full_location_name + ', ' + citybreak_search_data.destination_country_name
      });
      if(!google_map1_location_markers){
        google_map1_location_markers = [];
        if(hotel_markers){
          for(var i=0; i<hotel_markers._embedded.markers.length; i++){
            var hotel_marker = hotel_markers._embedded.markers[i];
            var hotelLatLang = {
              lat: parseFloat(hotel_marker.Lat),
              lng: parseFloat(hotel_marker.Lng)
            };
            google_map1_location_markers.push(new google.maps.Marker({
              icon: {
                path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                strokeColor:"#5cb85c",
                strokeWeight:2,
                scale: 5
              },
              position: hotelLatLang,
              map: google_map1,
              title: hotel_marker.Name
            }));
          }
        }
        if(hotel_filters){
          if(hotel_filters.pois && hotel_filters.pois.length)
          for(var i=0; i<hotel_filters.pois.length; i++){
            var poi = hotel_filters.pois[i];
            if(poi.Latitude && poi.Longitude){
              var poiLatLang = {
                lat: parseFloat(poi.Latitude),
                lng: parseFloat(poi.Longitude)
              };
              google_map1_location_markers.push(new google.maps.Marker({
                icon: {
                  path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                  strokeColor:"#B40404",
                  strokeWeight:2,
                  scale: 5
                },
                position: poiLatLang,
                map: google_map1,
                title: poi.Name
              }));
            }
          }
        }
        if(hotel_filters.activities && hotel_filters.activities.length)
        for(var i=0; i<hotel_filters.activities.length; i++){
          var poi = hotel_filters.activities[i];
          if(poi.Latitude && poi.Longitude){
            var poiLatLang = {
              lat: parseFloat(poi.Latitude),
              lng: parseFloat(poi.Longitude)
            };
            google_map1_location_markers.push(new google.maps.Marker({
              icon: {
                path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                strokeColor:"#0275d8",
                strokeWeight:2,
                scale: 5
              },
              position: poiLatLang,
              map: google_map1,
              title: poi.Name
            }));
          }
        }
      }
      
      
      if(hotel_filters){
        if(hotel_filters.pois && hotel_filters.pois.length)
        for(var i=0; i<hotel_filters.pois.length; i++){
          var poi = hotel_filters.pois[i];
          if(i>0 && i%5 === 0){
            $('#modalMapCity #modalMapCityAttractions').append($('<li class="hotelshowmore closed" onclick="$(this).toggleClass(\'closed\')"/>')
            .append($('<a href="javascript:void(0);"/>').text('Arata mai multe'))
            .append($('<a href="javascript:void(0);"/>').text('Arata mai putine')));
          }
          $('#modalMapCity #modalMapCityAttractions').append($('<li/>').text(poi.Name));
        }
      }
      // $('#modalMapCity iframe').attr('src','https://www.google.com/maps/embed/v1/search?q=' + encodeURIComponent(citybreak_search_data.destination_full_location_name) + '&key=<?php echo $google_maps_key; ?>' )
    }
  });
});
$(document).on("click", ".detEscale", function (e) {
  $(this).parents(".tur, .retur").children(".infoEscale").toggle('slow');
  $(this).toggleClass("warning, danger");
  if ($(this).hasClass("danger")) {
    $(this).html("<i class='fa fa-times-circle'></i> Inchide");
    $(".boxTicket > span, .boxTicket > img, .boxTicket p").addClass("opacity");
    $(".boxTicket").find(".detEscale, .escale > span, .escale > small").addClass("opacity");
    $(this).removeClass("opacity");
    var timeE = $(this).parents(".tur, .retur").children(".infoEscale").find(".timeEsc1").length;
    if (timeE < 2) {
      $(this).parents(".tur, .retur").children(".infoEscale").children(".timeEsc1").css("width", "60%");
    }
  } else {
    $(this).html("<i class='fa fa-info-circle'></i> Detalii");
    $(".boxTicket > span, .boxTicket > img, .boxTicket p").removeClass("opacity");
    $(".boxTicket").find(".detEscale, .escale > span, .escale > small").removeClass("opacity");
  }
});
$("#modalMapCity .btn").on("click", function () {
  $('#modalMapCity').hide();
});
var notification_id;
$("#hotelResults").on("click", ".notification-button", function () {
  notification_id = this.id;
  var $this = $(this);
  var obj = {
    ref_id : $this.data('ref_id'),
    type : $this.data('type'),
    title : $this.data('hotel_name') + ', ' + notification_title,
    amount : $this.data('amount'),
    amount_hotel : $this.data('amount_hotel'),
    amount_flight : $this.data('amount_flight'),
    currency : $this.data('currency'),
    data : JSON.stringify(citybreak_search_data)
  };
  openNotificationModal(obj);
});
$(document).ready(function(){
  $("#slider-range").on('slidestop', function (event, ui) {
    setCityBreakSearchStatus(false);
    $(this).trigger('updatePrice');
    setFilters();
    citybreak_search_data.page = 1;
    loadResults();
  });
  console.log(citybreak_search_data);
  <?php if(isset($_GET['init'])){ ?>
  removeLocationParam('init');
  $('.citybreak-search').first().submit();
  <?php } elseif(!isset($_GET['n'])){ ?>
  if(citybreak_search_data.index_id && citybreak_search_data.index_id.length>0){
    setCityBreakSearchStatus(false);
    // show_warnings = false;
    loadResults(true);
  }
	<?php } else { ?>
	setCityBreakSearchStatus(false);
  setCityBreakData();
	setSearchAndInitiate();
  <?php } ?>
});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>