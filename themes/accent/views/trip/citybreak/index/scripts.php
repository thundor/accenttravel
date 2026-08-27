<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$hotel_details = $this->view_data['hotel_details'];
$google_maps_key = 'AIzaSyBEBBKL4GwgmqVIN5cbc7KpSPapec8jmxo'; 
$data = &$this->citybreak_search_data;
?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key; ?>&callback=initMap">
</script>
<script>
var citybreak_search_data = <?php echo json_encode($data); ?>;
var hotel_details = <?php echo json_encode($hotel_details); ?>;
console.log(citybreak_search_data);
var google_map;
var google_map_marker;
var google_map_location_markers;
//(function($){
function initMap(){
  console.log('googleMaps loaded');
}
function loadGoogleMap(){
  var $me = $('#smallMapH > iframe');
  $('#modalMapH h4').text('Harta Hotel ' + $me.data('name') + ', ' + $me.data('address'));
  var src = 'https://www.google.com/maps/embed/v1/';
  var addFlight = $('#addAvionHotel').is(":checked");
  var flightCity = $('#inpZborHot').val();
  if(addFlight){
    src += 'directions?origin=' + encodeURIComponent(flightCity) + '&destination=' + encodeURIComponent($me.data('name')) + ', ' + encodeURIComponent($me.data('address')) + '&';
  } else {
    src += 'place?';
    src += 'q=' + encodeURIComponent($me.data('name')) + ', ' + encodeURIComponent($me.data('address')) + '&';
  }
  src += 'center=' + encodeURIComponent($me.data('lat')) + ',' + encodeURIComponent($me.data('lng')) + '&';
  src += 'zoom=10' + '&';
  src += 'key=<?php echo $google_maps_key; ?>';
  $me.attr('src',src);
}
$(document).ready(function(){
  // loadGoogleMap();
  // var $me = $('#smallMapH > iframe');
  var myLatLng = {
    lat: parseFloat(hotel_details.Lat), 
    lng: parseFloat(hotel_details.Lng)
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
    title: hotel_details.Name + ', ' + hotel_details.Address
  });
  // $('.hartaHotel').click(function(){
    // $('#modalMapH').show();
  // });
  // $("#modalMapH .btn").on("click", function () {
    // $('#modalMapH').hide();
  // });
});

function selectRoom(elem){
  if(elem){
    var $package = $(elem).closest('.hotel-package');
  } else {
    var $package = $('.hotel-package').first();
  }
  var total=0;
  var currency;
  $('input[type=radio]:checked', $package).each(function(){
    total+=parseFloat($(this).data('price'));
    currency = $(this).data('currency');
  })
  $('.total-price', $package).html(format_price(total + flight_price, currency) + ' (' + format_price(total, currency) + ' + ' + format_price(flight_price, currency) + ')' );
  $('.pretFullHotPag').html(format_price(total + flight_price, currency));
}

var package_results;
var selected_package_index;
function selectPackage(i){
  $('#request_offer_wrapper').hide();
  var $packages = $('#hotel-packages');
  $packages.empty();
  var $package_model = $('#package-model').clone().removeAttr('style id');
  var lowest_price = 0;
  if(package_results.length > 1){
    // $('.packages-title').show();
  }
  var inputname = 'package';
  var package_number = i+1;
  var package = package_results[i];
  selected_package_index = i;
  var package_price = parseFloat(package.Price.Amount);
  if(!lowest_price || lowest_price>package_price){
    lowest_price = package_price;
  }
  var $package = $package_model.clone();
  $('input[name=package_name]', $package).attr({
    'name' : inputname + '[name]'
  }).val(package.PackageCode);
  $('input[name=package_code]', $package).attr({
    'name' : inputname + '[code]'
  }).val(citybreak_search_data.code);
  $('input[name=package_start_date]', $package).attr({
    'name' : inputname + '[start_date]'
  }).val(citybreak_search_data.start_date);
  $('input[name=package_end_date]', $package).attr({
    'name' : inputname + '[end_date]'
  }).val(citybreak_search_data.end_date);
  // if(package_results.length <= 1){
    $('>.chooseHead', $package).hide();
  // }
  // $('#top_booking_button').attr('form','Package-' + package_number);
  // $('.flightResults input[form^="Package"]').attr('form','Package-' + package_number);
  $package.attr({
    // id: 'Package-' + package_number,
    id: 'Package-1',
    // name: 'Package-' + package_number,
    name: 'Package-1',
    'data-package-code': package.PackageCode
  });
  $('.package-number',$package).html(package_number);
  var $room_option_model = $('>.room-options>.room-option',$package).first().clone();
  $('>.room-options', $package).empty();
  var $package_room_model = $('>.package-rooms>.roomShow', $room_option_model).clone();
  $('>.package-rooms > .row', $room_option_model).remove();
  var inputname_rooms = inputname + '[rooms]';
  for(var j=0; j<package.PackageRooms.PackageRoom.length; j++){
    var room = package.PackageRooms.PackageRoom[j];
    var inputname_room = inputname_rooms + '[' + room.PackageRoomCode + ']';
    var adults = parseInt(room.Occupancy.Adults);
    var children = parseInt(room.Occupancy.Children);
    var childrenAges = room.Occupancy.ChildAge;
    var children_ages = [];
    var children_ages_arr = [];
    for (var o=0; o<childrenAges.length; o++){
      var age = parseInt(childrenAges[o]);
      children_ages_arr.push(age);
      if(age == 0){
        children_ages.push('<1 an');
      } else if(age == 1){
        children_ages.push('1 an');
      } else {
        children_ages.push(age + ' ani');
      }
    }
    var numar_camera = j+1;
    var $room_option = $room_option_model.clone();
    if(package.PackageRooms.PackageRoom.length <= 1){
      $('>.chooseHead .choose-room-name', $room_option).html('Alege camera');
    }
    $('.room-number', $room_option).html(numar_camera + ' (' + adults + ' ' + (adults==1?'adult':'adulti') + (children ? (' + ' + children + ' ' + (children==1?'copil':'copii') + ' ' + children_ages.join(',')) : '') + ')' );
    $room_option.attr({
      id: 'Package-' + package_number + '-Camera-' + numar_camera,
      'data-package-room-code': room.PackageRoomCode
    });
    $('input[name=adt]', $room_option).attr({
      name: inputname_room + '[adt]'
    }).val(adults);
    $('input[name=chdages]', $room_option).attr({
      name: inputname_room + '[chdages]'
    }).val(children_ages_arr.join(','));
    var inputname_room_option = inputname_room + '[option]';
    for(var k=0; k<room.RoomRefs.RoomRef.length; k++){
      var numar_alegere = k+1;
      var ref = room.RoomRefs.RoomRef[k];
      if(!ref.Price){
        ref.Price = package.Price;
      }
      var $package_room = $package_room_model.clone();
      $package_room.attr({
        id: 'Package-' + package_number + '-Camera-' + numar_camera + '-Alegere-' + numar_alegere,
        'data-room-code': ref.RoomCode
      });
      $('>div:nth-child(1)>p>strong', $package_room).html(titleCase(ref.Name));
      $('>div:nth-child(1)>p>small', $package_room).html('Board: ' + titleCase(ref.Board));
      // $('>div:nth-child(2)>p', $package_room).html(Math.floor(ref.Price.Amount * 2/100) + ' puncte');
      $('>div:nth-child(2)>p', $package_room).html(ref.Status == 'RQ' ? 'La cerere' : 'Disponibil');
      $('>div:nth-child(3)>p', $package_room).html(ref.Info);
      $('>div:nth-child(4)>p', $package_room).html(format_price(ref.Price.Amount,ref.Price.Currency));
      if(package.PackageRooms.PackageRoom.length == 1){
        $('>div:nth-child(5)>p:nth-of-type(1)', $package_room).hide();
      } else {
        $('>div:nth-child(5)>p:nth-of-type(2)', $package_room).hide();
      }
      $('>div:nth-child(5) input[name=p]', $package_room).attr({
        name: inputname_room_option, 
        value: ref.RoomCode,
        'data-price': ref.Price.Amount,
        'data-currency': ref.Price.Currency,
        id: 'p-' + package_number + '-c-' + numar_camera + '-a-' + numar_alegere
      }).prop('checked', !k)
      .on('change', function(){selectRoom(this);})
      .next('label').attr('for', 'p-' + package_number + '-c-' + numar_camera + '-a-' + numar_alegere);
      $package_room.appendTo($('>.package-rooms', $room_option));
    }
    $room_option.appendTo($('>.room-options', $package));
  }
  $('.total-price', $package).html(format_price(parseFloat(package.Price.Amount) + flight_price, package.Price.Currency) + ' (' + format_price(parseFloat(package.Price.Amount), package.Price.Currency) + ' + ' + format_price(flight_price, package.Price.Currency) + ')' );
  if(package.PackageRooms.PackageRoom.length == 1){
    $('.card-footer', $package).hide();
  }
  $package.appendTo($packages);
  $('.pretFullHotPag').html(format_price(lowest_price + flight_price,package.Price.Currency));
  $('.pretHotPag').css('visibility','visible');
  $('#request_offer_wrapper').show();
}
$('#package_selector').on('change', function(){
  selectPackage(parseInt(this.value));
});
function loadRoomPackages(){
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/loadRoomPackages'); ?>',
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
      citybreak_search_data = result.data;
      setCityBreakSearchStatus(true);
      package_results = result.response._embedded.packages;
      $('#package_selector').empty();
      $('#package_selector_wrapper').toggle(package_results.length>1);
      for(var i=0; i<package_results.length; i++){
        $('#package_selector').append('<option value="' + i + '">Oferta #' + (i+1) + '</option>');
      }
      selectPackage(0);
    },
    error: function(jqXHR,textStatus,error){
      setCityBreakSearchStatus(true);
    }
  });
}
function setCityBreakData($form){
  citybreak_search_data.hotel_id = $('#hotelId', $form).val();
  citybreak_search_data.city_id = $('#hotelSearchCityId', $form).val();
  citybreak_search_data.start_date = $('#hotelSearchStartDate', $form).val();
  citybreak_search_data.end_date = $('#hotelSearchEndDate', $form).val();
  citybreak_search_data.city_name = $('#destinatie', $form).val();
  citybreak_search_data.hotel_name = $('#numeHotel', $form).val();
  citybreak_search_data.min_stars = $('#categHotel', $form).val();
  citybreak_search_data.add_flight = $('#addAvionHotel', $form).is(':checked');
  citybreak_search_data.depart_city = $('#inpZborHot', $form).val();
  citybreak_search_data.weekend = $('#weekendSearch', $form).is(':checked');
  citybreak_search_data.occupancy = [];
  
  var ocuppancy_1 = {};
  ocuppancy_1.adt = $('#adultiCam1').val();
  var children = $('#copiiCam1').val();
  var ages = [];
  if(children>1){
    ocuppancy_1.chd = {age:[]};
    ocuppancy_1.chd.age.push($('#varstaCop1Cam1').val());
    if(children>2){
      ocuppancy_1.chd.age.push($('#varstaCop2Cam1').val());
    }
  }
  citybreak_search_data.occupancy.push(ocuppancy_1);
  
  if($('#cam2Hotel').is(':visible')){
    var ocuppancy_2 = {};
    ocuppancy_2.adt = $('#adultiCam2').val();
    var children = $('#copiiCam2').val();
    var ages = [];
    if(children>1){
      ocuppancy_2.chd = {age:[]};
      ocuppancy_2.chd.age.push($('#varstaCop1Cam2').val());
      if(children>2){
        ocuppancy_2.chd.age.push($('#varstaCop2Cam2').val());
      }
    }
    citybreak_search_data.occupancy.push(ocuppancy_2);
  }
  if($('#cam3Hotel').is(':visible')){
    var ocuppancy_3 = {};
    ocuppancy_3.adt = $('#adultiCam3').val();
    var children = $('#copiiCam3').val();
    var ages = [];
    if(children>1){
      ocuppancy_3.chd = {age:[]};
      ocuppancy_3.chd.age.push($('#varstaCop1Cam3').val());
      if(children>2){
        ocuppancy_3.chd.age.push($('#varstaCop2Cam3').val());
      }
    }
    citybreak_search_data.occupancy.push(ocuppancy_3);
  }
  
  citybreak_search_data.sort_by = 'MinPrice';
  citybreak_search_data.sort_order = 0;
  
  var sort_element = $('.hotel-sort-by').filter(function(){return $(this).val()>0;}).first();
  if(sort_element.length){
    citybreak_search_data.sort_by = sort_element.attr('name');
    citybreak_search_data.sort_order = parseInt(sort_element.val()) - 1;
  }
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
      hotel_filters = result.results;
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
      };
    }
  });
}
var hotel_results;
function loadResults(initial){
  if(initial && (!citybreak_search_data.index_id || !citybreak_search_data.index_id.length)){
    $('.citybreak-search').first().submit();
    return false;
  }
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/loadResults'); ?>',
    method: 'post',
    dataType: 'json',
    data: citybreak_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        if(initial){
          setSearchAndInitiate();
        } else {
          setCityBreakSearchStatus(true);
        }
        return;
      }
      citybreak_search_data = result.data;
      hotel_results = result;
      loadFilters();
      showFlightsResults();
      loadRoomPackages();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setCityBreakSearchStatus(true);
    }
  });
}
function setSearchAndInitiate(){
  $('.pretFullHotPag').html('<i class="fa fa-spinner fa-spin"></i>');
  $('.packages-title').hide();
  var $packages = $('#hotel-packages');
  $packages.empty();
      
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/setSearchAndInitiate'); ?>',
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
      citybreak_search_data = result.data;
      if(!result.response.total_items){
        setCityBreakSearchStatus(true);
        $packages.append('<h5 class="alert alert-danger">Acest hotel nu dispune de oferte disponibile conform cerintelor dumneavoastra.</h5>');
        return;
      }
      loadResults();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setCityBreakSearchStatus(true);
    }
  });
}
citybreak_submit_function = function (e){
  if(!search_is_over){
    console.log('A previous search is not complete. Ignoring request.');
    return;
  }
  setCityBreakSearchStatus(false);
  setCityBreakData($(this));
  setSearchAndInitiate();
};
<?php if(isset($_GET['n'])){ ?>
  removeLocationParam('n');
<?php } ?>
$(document).ready(function(){
  // setCityBreakSearchStatus(true);
  loadResults(true);
});
//})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>