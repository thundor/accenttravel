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
var required_page;
var shown_hotel_ids = [];
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
      startPage: required_page ? required_page : page,
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
        hotel_search_data.page = page;
        setHotelSearchStatus(false);
        loadResults();
      }
    });
  }
  var hotel_ids = [];
  var should_refresh = false;
  if(!shown_hotel_ids.length || !response.hotels.length){
    should_refresh = true;
  }
  for (var i=0; i<response.hotels.length; i++){
    var hotel = response.hotels[i];
    hotel_ids.push(hotel.Id);
  }
  if(!should_refresh){
	  var intersect = hotel_ids.filter(value => shown_hotel_ids.includes(value));
	  should_refresh = intersect.length != shown_hotel_ids.length;
  }
  shown_hotel_ids = hotel_ids;
  
  if(loading_partial_results && !response.total_items){
    setSearchProgress(1);
    $('.rezCount > .filterTitle').text('In curs de cautare oferte in ' + hotel_search_data.city_name + '.');
  } else{
    if(loading_partial_results){
      $('.rezCount > .filterTitle').text('Am gasit ' + response.total_items + ' hoteluri in ' + hotel_search_data.city_name + '. Se continua cautarea...');
    } else {
      $('.rezCount > .filterTitle').text('Am gasit ' + response.total_items + ' hoteluri in ' + hotel_search_data.city_name);
    }
  }
  $('.rezCount > .mapInfo > span').text(hotel_search_data.city_name.toUpperCase());
  var start_date = moment(hotel_search_data.start_date,'Y-MM-DD');
  $('.rezCount .selected_date_start').text(start_date.locale('ro').format("dddd, DD MMMM Y"));
  var end_date = moment(hotel_search_data.end_date,'Y-MM-DD');
  $('.rezCount .selected_date_end').text(end_date.locale('ro').format("dddd, DD MMMM Y"));
  $('.rezCount .selected_rooms').text(hotel_search_data.occupancy.length + ' camera');
  var travellers = 0;
  var adults = 0;
  var children = 0;
  for (var i=0; i<hotel_search_data.occupancy.length; i++){
    var occupants = hotel_search_data.occupancy[i];
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
  
  notification_title = hotel_search_data.city_name + ', ' + hotel_search_data.country_name;
  notification_title += ', ' + hotel_search_data.occupancy.length + ' ' + (hotel_search_data.occupancy.length > 1 ? 'camere' : 'camera');
  notification_title += ', ' + start_date.locale('ro').format("dddd, DD MMMM Y");
  notification_title += ', ' + travellers + ' ' + (travellers > 1 ? 'persoane' : 'persoana');
  notification_title += ', ' + nights + ' ' + (nights > 1 ? 'nopti' : 'noapte');
  console.log('response', response);
  if(should_refresh){
    $('#hotelResults').empty();
    for (var i=0; i<response.hotels.length; i++){
      var hotel = response.hotels[i];
      var $hotel_box = $hotel_box_model.clone();
      $('.hartaHotel', $hotel_box).attr('data-lat', hotel.Lat);
      $('.hartaHotel', $hotel_box).attr('data-lng', hotel.Lng);
      $('.hartaHotel', $hotel_box).attr('data-city', hotel_search_data.city_name);
      $('.hartaHotel', $hotel_box).attr('data-address', hotel.Address);
      $('.hartaHotel', $hotel_box).attr('data-name', hotel.Name);
      $('.hotel-image', $hotel_box).attr('href', hotel.link)
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
      $('.reserve-button', $hotel_box).attr('href',hotel.link);
      $('.notification-button', $hotel_box).attr({
        'id': 'button_notification_hotel_' + hotel.Id,
        'data-type': 'hotel',
        'data-ref_id': hotel.Id,
        'data-hotel_name': hotel.Name + (hotel.Stars ? ' (' + hotel.Stars + ' stele)' : '') + ' ' + hotel.Address,
        'data-amount': hotel.MinPrice,
        'data-currency': hotel.Currency,
        'data-link': hotel.link
      });
      $('.hotel-stars', $hotel_box).html(" " + Array(parseInt(hotel.Stars) + 1).join('<i class="fa fa-star"></i>'));
  //    $('.hotel-room-type', $hotel_box).append('camera standard');
  //    $('.hotel-dinner', $hotel_box).append('<i class="fa fa-bed"></i> Fara masa');
  //    $('.hotel-info', $hotel_box).remove();
      $('.initial-price', $hotel_box).remove();
      $('.hotel-location', $hotel_box).text(hotel.Address);
      $('.current-price', $hotel_box).text(format_price(Math.ceil(hotel.MinPrice), hotel.Currency));
      $('.hotel-info-accomodation', $hotel_box).html(Array(parseInt(adults) + 1).join('<i class="fa fa-user-o"></i>'))
          .append(" " + adults + ' '  + (adults == 1 ? 'adult' : 'adulti') + '/ ' + nights + ' ' + (nights == 1 ? 'noapte' : 'nopti') + ' / Camera');
      $('#hotelResults').append($hotel_box);
    }
  }
  $('.rezCount').show();
  $('.sortHotel').show();
  setHotelSearchStatus(true);
  $('#hotelResults .lazy').lazy();
  $('#hotelsResultsWrapper').show();
}
var show_warnings = true;
function interpretNoHotelsResponse(result,initial){
  setHotelSearchStatus(true);
  if(initial && result && result.data && result.data.hotels_expired){
    show_warnings = false;
  }
  if(show_warnings){
    $('#hotelWarnings').show();
  }
  show_warnings = true;
  $('#hotelsResultsWrapper').hide();
}
var hotel_results;
var loading_partial_results = true;
function loadResults(initial){
  if(!initial && loading_partial_results){
    required_page = hotel_search_data.page;
    return;
  }
  console.log('required_page',required_page);
  if(required_page){
    hotel_search_data.page = required_page;
    required_page = null;
  }
  $('#hotelWarnings').hide();
  console.log('hotel_search_data.page',hotel_search_data.page);
  $.ajax({
    url: '<?php echo site_url('trip/hotelsasync/loadResults'); ?>',
    method: 'post',
    dataType: 'json',
    data: hotel_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(result.response && result.response.summary && result.response.summary.progress){
        setSearchProgress(result.response.summary.progress);
      }
      if(!result.results.partial){
        if(!result.status || result.status !== 'success'){
          interpretNoHotelsResponse(result,initial);
          return;
        }
      }
      hotel_search_data = result.data;
      hotel_search_data.page = result.results.page;
      hotel_results = result;
      if(!result.results.partial){
        loading_partial_results = false;
      }
      interpretResults();
      if(result.results.partial){
        setTimeout(function(){loadResults(initial)}, 721);
        return;
      } else {
        if(required_page){
          loadResults(initial);
          return;
        }
        loadFilters();
      }
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR,textStatus,error);
      setHotelSearchStatus(true);
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
    url: '<?php echo site_url('trip/hotelsasync/loadMarkers'); ?>',
    method: 'post',
    dataType: 'json',
    data: hotel_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        setHotelSearchStatus(true);
        return;
      }
      hotel_markers = result.response;
    }
  });
}
var hotel_filters;
function loadFilters(){
  $.ajax({
    url: '<?php echo site_url('trip/hotelsasync/loadFilters'); ?>',
    method: 'post',
    dataType: 'json',
    data: hotel_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        setHotelSearchStatus(true);
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
      if(hotel_search_data.filters.min_price < filters.minPrice){
        hotel_search_data.filters.min_price = filters.minPrice;
      }
      if(!filters.maxPrice){
        filters.maxPrice = 0;
      }
      if(hotel_search_data.filters.max_price > filters.maxPrice || hotel_search_data.filters.max_price < hotel_search_data.filters.min_price){
        hotel_search_data.filters.max_price = filters.maxPrice;
      }
      var min_price = Math.ceil(parseFloat(filters.minPrice));
      var max_price = Math.ceil(parseFloat(filters.maxPrice));

      var $price_slider = $("#slider-range").slider('option',{
        min: min_price,
        max: max_price,
        values: [hotel_search_data.filters.min_price, hotel_search_data.filters.max_price],
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
        $('<input type="checkbox" name="stars" id="hotelStars' + star + '" value="' + star + '"/>').prop('checked', hotel_search_data.filters.stars.indexOf(star)>-1).appendTo(checkWrapper);
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
        $('<input type="checkbox" name="facilities" id="hotelFacilities' + facility_id + '" value="' + facility_id + '"/>').prop('checked', hotel_search_data.filters.facilities.indexOf(facility_id)>-1).appendTo(checkWrapper);
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
          .prop('checked', hotel_search_data.filters.activity_categories.indexOf(activity_id)>-1).appendTo(checkWrapper);
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
        $('<input type="checkbox" name="locations" id="hotellocations' + activity_id + '" value="' + activity_id + '"/>').prop('checked', hotel_search_data.filters.activity_categories.indexOf(activity_id)>-1).appendTo(checkWrapper);
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
        $('<input type="checkbox" name="pois" id="hotelpois' + poi_id + '" value="' + poi_id + '"/>').prop('checked', hotel_search_data.filters.pois.indexOf(poi_id)>-1).appendTo(checkWrapper);
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
      $('#allFilters').removeClass('d-none');
      setFilters();
    },
    error: function(jqXHR,textStatus,error){
      setHotelSearchStatus(true);
    }
  }).done(function(){
    setSearchProgress(false);
  });
}
var prev_what;
var prev_what_mod = 0;
function setSearchProgress(what){
  console.log('setSearchProgress', what);
  var $search_progress_bar = $('#search_progress_bar');
  if(what === false){
    prev_what_mod = 0;
    prev_what = false;
    $search_progress_bar.remove();
    return;
  }
  if(what > 100){
    what = 100;
  }
  if(prev_what > what){
    what = prev_what;
  }
  if(prev_what == what){
    prev_what_mod = prev_what_mod+3;
  }
  prev_what = what;
  var w = prev_what_mod + what;
  if(!$search_progress_bar.length){
    $search_progress_bar = $('\
    <div id="search_progress_bar" class="progress mb-3">\
      <div class="progress-bar" style="width: 0%"></div>\
    </div>');
    $search_progress_bar.insertAfter($('.rezCount').first());
  }
  $('>.progress-bar:nth-child(1)', $search_progress_bar).width(w + '%');
}
function setSearchAndInitiate(){
  $('#hotelWarnings').hide();
  $.ajax({
    url: '<?php echo site_url('trip/hotelsasync/setSearchAndInitiate'); ?>',
    method: 'post',
    dataType: 'json',
    data: hotel_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        interpretNoHotelsResponse(result);
        return;
      }
      hotel_search_data = result.data;
			loadResults(true);
      // hotel_results = result;
      // loadFilters();
      // loadMarkers();
      // interpretResults();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setHotelSearchStatus(true);
    }
  });
}
function setSort(){
  var sort_element = $('.hotel-sort-by').filter(function(){return $(this).val()>0;}).first();
  if(sort_element.length){
    hotel_search_data.sort_by = sort_element.attr('name');
    hotel_search_data.sort_order = parseInt(sort_element.val()) - 1;
  }
}
function setFilters(){
  hotel_search_data.filters.stars = [];
  $('.hotel-stars-filter input[type=checkbox]:checked').each(function(){
    hotel_search_data.filters.stars.push(parseInt(this.value));
  });
  hotel_search_data.filters.facilities = [];
  $('.hotel-facilities-filter input[type=checkbox]:checked').each(function(){
    hotel_search_data.filters.facilities.push(parseInt(this.value));
  });
  hotel_search_data.filters.activity_categories = [];
  hotel_search_data.filters.activities = [];
  $('.hotel-activitycategories-filter input[type=checkbox]:checked').each(function(){
    hotel_search_data.filters.activity_categories.push(parseInt(this.value));
    hotel_search_data.filters.activities = hotel_search_data.filters.activities.concat($(this).attr('data-activities').split(','));
  });
  hotel_search_data.filters.locations = [];
  $('.hotel-locations-filter input[type=checkbox]:checked').each(function(){
    hotel_search_data.filters.locations.push(parseInt(this.value));
  });
  hotel_search_data.filters.pois = [];
  $('.hotel-pois-filter input[type=checkbox]:checked').each(function(){
    hotel_search_data.filters.pois.push(parseInt(this.value));
  });
  var $price_slider = $("#slider-range").slider();
  var price_values = $price_slider.slider('values');
  hotel_search_data.filters.min_price = parseFloat(price_values[0]);
  hotel_search_data.filters.max_price = parseFloat(price_values[1]);
  <?php
  if($this->_ci->user->can('backend-access')){ ?>
  setHotelFiltersLink();
  <?php } ?>
}
function resetFilters(){
  $('.hotel-stars-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-facilities-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-activitycategories-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-locations-filter input[type=checkbox]:checked').prop('checked',false);
  $('.hotel-pois-filter input[type=checkbox]:checked').prop('checked',false);
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
  setHotelSearchStatus(false);
  setFilters();
  hotel_search_data.page = 1;
  loadResults();
});
$('.hotel-facilities-filter, .hotel-activitycategories-filter, .hotel-locations-filter, .hotel-pois-filter').on('change', 'input[type=checkbox]',function(){
  setHotelSearchStatus(false);
  setFilters();
  hotel_search_data.page = 1;
  loadResults();
});
hotel_submit_function = function (e){
  if(!search_is_over){
    console.log('A previous search is not complete. Ignoring request.');
    return;
  }
  $('#hotelsResultsWrapper').hide();
  $('#hotelResults').empty();
  $('.rezCount').hide();
  $('ul.pagination').empty();
  $('.sortHotel').hide();
  setHotelSearchStatus(false);
  setHotelData($(this));
  setHotelSearchAndRedirect();
};
$('#applyFilters').click(function(){
  setHotelSearchStatus(false);
  setFilters();
  hotel_search_data.page = 1;
  loadResults();
  var body = $("html, body");
  var pagination_top = $('h1.filterTitle').first().offset().top;
  body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
  });
});
$('#resetFilters').click(function(){
  setHotelSearchStatus(false);
  resetFilters();
  setFilters();
  hotel_search_data.page = 1;
  loadResults();
  var body = $("html, body");
  var pagination_top = $('h1.filterTitle').first().offset().top;
  body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
  });
});
$('.hotel-sort-by').prop('disabled', false).on('change', function(){
  setHotelSearchStatus(false);
  var $me = $(this);
  if($me.val() === '0'){
    $me.val('1');
  }
  $('.hotel-sort-by').filter(function(){return !$(this).is($me);}).val(0);
  setSort();
  hotel_search_data.page = 1;
  loadResults();
});
$(document).on("click",'.inchideH', function () {
  $(this).parents(".boxHotel").hide("slow");
});
var map_index=0;
var google_map;
var google_map_marker;
var google_map_location_markers;
function initMap(){
  console.log('googleMaps loaded');
}
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
$("#modalMapH .btn").on("click", function () {
  $('#modalMapH').hide();
});
var google_map1;
var google_map1_marker;
var google_map1_location_markers;
$(".mapInfo").on("click", function () {
  $('#modalMapCity #modalMapCityTitle').text(hotel_search_data.city_name + ', ' + hotel_search_data.country_name);
  $('#modalMapCity #modalMapCityName').text(hotel_search_data.city_name);
  $('#modalMapCity #modalMapCityHotelCount').text(hotel_results.results.total_items);
  $('#modalMapCity #modalMapCityAttractions').empty();
  
  var myLatLng = {
    lat: 0, 
    lng: 0
  };
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address': hotel_search_data.city_name + ', ' + hotel_search_data.country_name}, function(results, status) {
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
        title: hotel_search_data.city_name + ', ' + hotel_search_data.country_name
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
      // $('#modalMapCity iframe').attr('src','https://www.google.com/maps/embed/v1/search?q=' + encodeURIComponent(hotel_search_data.city_name) + '&key=<?php echo $google_maps_key; ?>' )
    }
  });
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
    currency : $this.data('currency'),
    data : JSON.stringify(hotel_search_data)
  };
  openNotificationModal(obj);
});
submitting_form = false;
$(document).ready(function(){
  $("#slider-range").on('slidestop', function (event, ui) {
    setHotelSearchStatus(false);
    $(this).trigger('updatePrice');
    setFilters();
    hotel_search_data.page = 1;
    loadResults();
  });
  console.log(hotel_search_data);
<?php if(isset($_GET['init'])){ ?>
  removeLocationParam('init');
  $('.hotel-search').first().submit();
<?php } elseif(!isset($_GET['n'])){ ?>
  if(hotel_search_data.index_id && hotel_search_data.index_id.length>0){
    setHotelSearchStatus(false);
    // show_warnings = false;
    loadResults(true);
  }
<?php } else { ?>
setHotelSearchStatus(false);
setSearchAndInitiate();
<?php } ?>
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>