<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$hotel_details = $this->view_data['hotel_details'];
$google_maps_key = 'AIzaSyBEBBKL4GwgmqVIN5cbc7KpSPapec8jmxo'; 
$data = &$this->hotel_search_data;
?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key; ?>&callback=initMap">
</script>
<script>
var hotel_search_data = <?php echo json_encode($data); ?>;
var hotel_details = <?php echo json_encode($hotel_details); ?>;
var google_map;
var google_map_marker;
var google_map_location_markers;
//(function($){
function initMap(){
  
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

function selectRoom(elem){
  var $package = $(elem).closest('.hotel-package');
  var total=0;
  var currency;
  $('input[type=radio]:checked', $package).each(function(){
    total+=parseFloat($(this).data('price'));
    currency = $(this).data('currency');
  });
  $('.total-price', $package).html(format_price(total, currency));
  $('.pretFullHotPag').html(format_price(total, currency));
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
  }).val(hotel_search_data.code);
  $('input[name=package_start_date]', $package).attr({
    'name' : inputname + '[start_date]'
  }).val(hotel_search_data.start_date);
  $('input[name=package_end_date]', $package).attr({
    'name' : inputname + '[end_date]'
  }).val(hotel_search_data.end_date);
  // if(package_results.length <= 1){
    $('>.chooseHead', $package).hide();
  // }
  // $('#top_booking_button').attr('form','Package-' + package_number);
  $package.attr({
    // id: 'Package-' + package_number,
    id: 'Package-1',
    // name: 'Package-' + package_number,
    name: 'Package-' + package_number,
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
  $('.total-price', $package).html(format_price(package.Price.Amount,package.Price.Currency));
  if(package.PackageRooms.PackageRoom.length == 1){
    $('.card-footer', $package).hide();
  }
  $package.appendTo($packages);
  $('.pretFullHotPag').html(format_price(lowest_price,package.Price.Currency));
  $('.pretHotPag').css('visibility','visible');
  $('#request_offer_wrapper').show();
}
$('#package_selector').on('change', function(){
  selectPackage(parseInt(this.value));
});
function loadRoomPackages(){
  $('#package_selector').html('<option>Se incarca...</option>');
  $.ajax({
    url: '<?php echo site_url('trip/hotels/loadRoomPackages'); ?>',
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
      hotel_search_data = result.data;
      setHotelSearchStatus(true);
      package_results = result.response._embedded.packages;
      $('#package_selector').empty();
      $('#package_selector_wrapper').toggle(package_results.length>1);
      for(var i=0; i<package_results.length; i++){
        $('#package_selector').append('<option value="' + i + '">Oferta #' + (i+1) + '</option>');
      }
      selectPackage(0);
    },
    error: function(jqXHR,textStatus,error){
      setHotelSearchStatus(true);
    }
  });
}
var hotel_filters;
function loadFilters(){
  $.ajax({
    url: '<?php echo site_url('trip/hotels/loadFilters'); ?>',
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
  if(initial && (!hotel_search_data.index_id || !hotel_search_data.index_id.length)){
    $('.hotel-search').first().submit();
    return false;
  }
  $.ajax({
    url: '<?php echo site_url('trip/hotels/loadResults'); ?>',
    method: 'post',
    dataType: 'json',
    data: hotel_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        if(initial){
          setSearchAndInitiate();
        } else {
          setHotelSearchStatus(true);
        }
        return;
      }
      hotel_search_data = result.data;
      hotel_results = result;
      loadFilters();
      loadRoomPackages();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setHotelSearchStatus(true);
    }
  });
}
function setSearchAndInitiate(){
  $('.pretFullHotPag').html('<i class="fa fa-spinner fa-spin"></i>');
  $('.packages-title').hide();
  var $packages = $('#hotel-packages');
  $packages.empty();
      
  $.ajax({
    url: '<?php echo site_url('trip/hotels/setSearchAndInitiate'); ?>',
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
      hotel_search_data = result.data;
      if(!result.response.total_items){
        setHotelSearchStatus(true);
        $packages.append('<h5 class="alert alert-danger">Acest hotel nu dispune de oferte disponibile conform cerintelor dumneavoastra.</h5>');
        return;
      }
      loadResults();
      // hotel_results = result;
      // loadFilters();
      // loadRoomPackages();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setHotelSearchStatus(true);
    }
  });
}
hotel_submit_function = function (e){
  if(!search_is_over){
    console.log('A previous search is not complete. Ignoring request.');
    return;
  }
  setHotelSearchStatus(false);
  setHotelData($(this));
  setSearchAndInitiate();
};
<?php if(isset($_GET['n'])){ ?>
  removeLocationParam('n');
<?php } ?>
$(document).ready(function(){
  loadResults(true);
});
//})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>