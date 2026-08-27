<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$data = &$this->citybreak_search_data;
// echo '<pre>';
// print_r($data);
// die;
?>
<script type="text/javascript">
var citybreak_search_data = <?php echo json_encode($data); ?>;
var citybreak_submit_function;
var search_is_over = true;
(function($){
	<?php
	if($this->_ci->user->can('backend-access')){ ?>
	function setFiltersLink(){
		console.log(citybreak_search_data);
		var my_obj = {};
		my_obj.n = 1;
		/* if(parseInt(citybreak_search_data.city_id)){
      my_obj.city_id = parseInt(citybreak_search_data.city_id);
    } */
    if(citybreak_search_data.hotel_name){
      my_obj.hotel = citybreak_search_data.hotel_name;
    }
    if(citybreak_search_data.origin_location_name || citybreak_search_data.origin_city_name){
      my_obj.origin = citybreak_search_data.origin_location_name ? citybreak_search_data.origin_location_name : citybreak_search_data.origin_city_name;
    }
    if(citybreak_search_data.origin_location_name || citybreak_search_data.destination_city_name){
      my_obj.destination = citybreak_search_data.destination_location_name ? citybreak_search_data.destination_location_name : citybreak_search_data.destination_city_name;
    }
    if(citybreak_search_data.start_date){
      my_obj.sdate = citybreak_search_data.start_date;
    }
    if(citybreak_search_data.end_date){
      my_obj.edate = citybreak_search_data.end_date;
    }
    if(citybreak_search_data.occupancy.length){
      my_obj.o = citybreak_search_data.occupancy;
    }
		if(citybreak_search_data.filters){
			if(citybreak_search_data.filters.activity_categories && citybreak_search_data.filters.activity_categories.length){
				my_obj.a = citybreak_search_data.filters.activity_categories.join(',');
			}
			if(citybreak_search_data.filters.stars && citybreak_search_data.filters.stars.length){
				my_obj.s =  citybreak_search_data.filters.stars.join(',');
			}
			if(citybreak_search_data.filters.pois && citybreak_search_data.filters.pois.length){
				my_obj.p =  citybreak_search_data.filters.pois.join(',');
			}
			if(citybreak_search_data.filters.facilities && citybreak_search_data.filters.facilities.length){
				my_obj.f =  citybreak_search_data.filters.facilities.join(',');
			}
		}
		var recursiveDecoded = decodeURIComponent( $.param( my_obj ) );
		var filters_link = 'trip/citybreaks?' + recursiveDecoded;
		$('.trip_citybreak_search_link').val(filters_link);
	}
	window.setCityBreakFiltersLink = setFiltersLink;
	setFiltersLink();
	<?php } ?>
function updateCalendar(){
  var caleran = $("#dateZborCB").data("caleran");
  caleran.fetchInputs();
  caleran.validateDates();
  if(caleran.config.startDate.isBefore(caleran.config.minDate)){
    caleran.config.startDate = caleran.config.minDate;
  }
  if(caleran.config.endDate.isBefore(caleran.config.minDate)){
    caleran.config.endDate = caleran.config.minDate;
  }
  caleran.updateInput();
  caleran.config.onafterselect(caleran, caleran.config.startDate, caleran.config.endDate);
}
window.updateCityBreakCalendar = updateCalendar;
function setSearchStatus(search_status){
  if(search_status){
    $('#citybreaks-loading-screen').addClass('inactive');
    $('#cautaZborCB').bootstrap_button('loading');
  } else {
    $('#citybreaks-loading-screen').removeClass('inactive');
    $('#cautaZborCB').bootstrap_button('reset');
  }
  search_is_over = search_status;
}
window.setCityBreakSearchStatus = setSearchStatus;
function setData($form){
  citybreak_search_data.hotel_id = $('#citybreakId', $form).val();
  citybreak_search_data.hotel_name = $('#numeHotelCB', $form).val();
  citybreak_search_data.min_stars = 0;
  // var departure = $('#plecareCB', $form).val();
  // citybreak_search_data.departure = departure;
  // var departure_arr = departure.split('-');
  // citybreak_search_data.origin_city_id = departure_arr[0];
  // citybreak_search_data.origin_location_id = departure_arr[1];
  // var $selected_origin = $('#plecareCB > option:selected', $form);
  // citybreak_search_data.origin_country_id = $selected_origin.data('country_id');
  // citybreak_search_data.origin_country_name = $selected_origin.data('country');
  // citybreak_search_data.origin_city_name = $selected_origin.data('city');
  // citybreak_search_data.origin_location_name = $selected_origin.data('location');
  // citybreak_search_data.origin_full_location_name = $selected_origin.text();
  
  // var arrival = $('#sosireCB', $form).val();
  // citybreak_search_data.arrival = arrival;
  // var arrival_arr = arrival.split('-');
  // citybreak_search_data.destination_city_id = arrival_arr[0];
  // citybreak_search_data.destination_location_id = arrival_arr[1];
  // var $selected_destination = $('#sosireCB > option:selected', $form);
  // citybreak_search_data.destination_country_id = $selected_destination.data('country_id');
  // citybreak_search_data.destination_country_name = $selected_destination.data('country');
  // citybreak_search_data.destination_city_name = $selected_destination.data('city');
  // citybreak_search_data.destination_location_name = $selected_destination.data('location');
  // citybreak_search_data.destination_full_location_name = $selected_destination.text();
  
  citybreak_search_data.passengers_adult = 0;
  citybreak_search_data.passengers_senior = 0;
  citybreak_search_data.passengers_youth = 0;
  citybreak_search_data.passengers_child = 0;
  citybreak_search_data.passengers_infant_lap = 0;
  citybreak_search_data.passengers_infant_seat = 0;
  
  citybreak_search_data.occupancy = [];
  
  var ocuppancy_1 = {};
  ocuppancy_1.adt = parseInt($('#adultiCam1CB').val());
  var children = $('#copiiCam1CB').val();
  var ages = [];
  if(children>1){
    ocuppancy_1.chd = {age:[]};
    ocuppancy_1.chd.age.push($('#varstaCop1Cam1CB').val());
    if(children>2){
      ocuppancy_1.chd.age.push($('#varstaCop2Cam1CB').val());
    }
  }
  citybreak_search_data.occupancy.push(ocuppancy_1);
  
  if($('#cam2CB').is(':visible')){
    var ocuppancy_2 = {};
    ocuppancy_2.adt = parseInt($('#adultiCam2CB').val());
    var children = $('#copiiCam2CB').val();
    var ages = [];
    if(children>1){
      ocuppancy_2.chd = {age:[]};
      ocuppancy_2.chd.age.push($('#varstaCop1Cam2CB').val());
      if(children>2){
        ocuppancy_2.chd.age.push($('#varstaCop2Cam2CB').val());
      }
    }
    citybreak_search_data.occupancy.push(ocuppancy_2);
  }
  if($('#cam3CB').is(':visible')){
    var ocuppancy_3 = {};
    ocuppancy_3.adt = parseInt($('#adultiCam3CB').val());
    var children = $('#copiiCam3CB').val();
    var ages = [];
    if(children>1){
      ocuppancy_3.chd = {age:[]};
      ocuppancy_3.chd.age.push($('#varstaCop1Cam3CB').val());
      if(children>2){
        ocuppancy_3.chd.age.push($('#varstaCop2Cam3CB').val());
      }
    }
    citybreak_search_data.occupancy.push(ocuppancy_3);
  }
  for(var i=0; i<citybreak_search_data.occupancy.length; i++){
    var room_occupancy = citybreak_search_data.occupancy[i];
    citybreak_search_data.passengers_adult += room_occupancy.adt;
    if(room_occupancy.chd && room_occupancy.chd.age && room_occupancy.chd.age.length){
      for(var j=0; j<room_occupancy.chd.age.length; j++){
        var varsta_copil = parseInt(room_occupancy.chd.age[j]);
        if(varsta_copil < 3){
          citybreak_search_data.passengers_infant_lap ++;
        } else if(varsta_copil < 18){
          citybreak_search_data.passengers_child ++;
        }
      }
    }
  }
  var total_adults = citybreak_search_data.passengers_adult + citybreak_search_data.passengers_senior;
  if(total_adults < citybreak_search_data.passengers_infant_lap){
    var inf_ad_diff = citybreak_search_data.passengers_infant_lap - total_adults;
    citybreak_search_data.passengers_infant_lap -= inf_ad_diff;
    citybreak_search_data.passengers_infant_seat += inf_ad_diff;
  }
  
  citybreak_search_data.sort_by = 'MinPrice';
  citybreak_search_data.sort_order = 0;
  
  var sort_element = $('.citybreak-sort-by').filter(function(){return $(this).val()>0;}).first();
  if(sort_element.length){
    citybreak_search_data.sort_by = sort_element.attr('name');
    citybreak_search_data.sort_order = parseInt(sort_element.val()) - 1;
  }
	<?php
	if($this->_ci->user->can('backend-access')){ ?>
	setFiltersLink();
	<?php } ?>
}
$('#plecareCB, #sosireCB').autocomplete({
  source: function(request, response){
    $.ajax({
      url: "<?php echo site_url('trip/flights/loadLocations'); ?>",
      dataType: "json",
      data: {
        q: request.term
      },
      success: function( result ) {
        console.log(result);
        if(!result.status || result.status !== 'success'){
          return;
        }
        var data = result.response;
        var response_data = [];
        if(data && data.length){
          for (var i=0; i < data.length; i++){
            var item = data[i];
            var label = (item.LocationId>0 ? item.LocationName + ', ' : '') + item.CityName + ' (' + item.CountryName + ')';
            var response_item = {
              id: item.LocationId + '-' + item.CityId,
              location_id: item.LocationId,
              city_id: item.CityId,
              city_name: item.CityName,
              country_name: item.CountryName,
              location_name: (item.LocationId>0 ? item.LocationName : ''),
              value: (item.LocationId>0 ? item.LocationName + ', ' : '') + item.CityName,
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
    var is_origin = this.id === 'plecareCB';
    var prefix = is_origin ? 'origin' : 'destination';
    citybreak_search_data[prefix + '_city_name'] = ui.item.city_name;
    citybreak_search_data[prefix + '_country_name'] = ui.item.country_name;
    citybreak_search_data[prefix + '_location_name'] = ui.item.location_name;
    citybreak_search_data[prefix + '_full_location_name'] = ui.item.value;
    citybreak_search_data[prefix + '_location_id'] = ui.item.location_id;
    citybreak_search_data[prefix + '_city_id'] = ui.item.city_id;
  }
}).blur(function(){
  var is_origin = this.id === 'plecareCB';
  var prefix = is_origin ? 'origin' : 'destination';
  if(!this.value.length || this.value !== citybreak_search_data[prefix + '_full_location_name']){
    this.value = '';
    citybreak_search_data[prefix + '_city_name'] = '';
    citybreak_search_data[prefix + '_country_name'] = '';
    citybreak_search_data[prefix + '_location_name'] = '';
    citybreak_search_data[prefix + '_full_location_name'] = '';
    citybreak_search_data[prefix + '_location_id'] = 0;
    citybreak_search_data[prefix + '_city_id'] = 0;
    setData();
  }
});
window.setCityBreakData = setData;
$(document).on('change click', 'form.citybreak-search', function(e){
	setData();
});
function setSearchAndRedirect(){
  citybreak_search_data.filters.min_price=null;
  citybreak_search_data.filters.max_price=null;
  console.log(citybreak_search_data);
  $.ajax({
    url: '<?php echo site_url('trip/citybreaks/setSearch'); ?>',
    method: 'post',
    dataType: 'json',
    data: citybreak_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        setSearchStatus(true);
        return;
      }
      citybreak_search_data = result.data;
      window.location.href="<?php echo site_url('trip/citybreaks/search'); ?>";
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setSearchStatus(true);
    }
  });
}
window.setCityBreakSearchAndRedirect = setSearchAndRedirect;
$("#dateZborCB").caleran({
  startOnMonday: true,
  locale: 'ro',
  startEmpty: citybreak_search_data.start_date === '' || citybreak_search_data.end_date === '',
  showFooter: false,
  autoCloseOnSelect: true,
  format: 'DD/MM/Y',
  minDate: moment(),
  startDate: moment(citybreak_search_data.start_date,'Y-MM-DD'),
  endDate: moment(citybreak_search_data.end_date,'Y-MM-DD'),
  onafterselect: function(caleran, startDate, endDate){
    if(!caleran.globals.firstValueSelected){
      return;
    }
    citybreak_search_data.start_date = startDate.format("Y-MM-DD");
    citybreak_search_data.end_date = endDate.format("Y-MM-DD");
  }
}).on('change',function(){
  updateCalendar();
});
updateCalendar();
$("#copiiCam1CB").on("change", function () {
  $(this).find("option:selected").each(function () {
    var optionValue = $(this).attr("value");
    if (optionValue == 2) {
      $("#cam1CB .varsteCopii").show();
      $("#varstaCop1Cam1CB").show();
      $("#varstaCop2Cam1CB").hide();
      $("#cam1CB .varsteCopii p#v1CB1").show();
      $("#cam1CB .varsteCopii p#v2CB1").hide();
    }
    if (optionValue == 3) {
      $("#cam1CB .varsteCopii").show();
      $("#varstaCop2Cam1CB").show();
      $("#varstaCop1Cam1CB").show();
      $("#cam1CB .varsteCopii p#v1CB1").show();
      $("#cam1CB .varsteCopii p#v2CB1").show();
    }
    if (optionValue == 1) {
      $("#cam1CB .varsteCopii").hide();
      $("#varstaCop1Cam1CB").hide();
      $("#varstaCop2Cam1CB").hide();
      $("#cam1CB .varsteCopii p#v1CB1").hide();
      $("#cam1CB .varsteCopii p#v2CB1").hide();
    }
  });
});
//adaugare & stergere camera 2 citybreak
$("#addCam2CB").on("click", function () {
  $("#cam2CB").show();
  $("#addCam2CB").parent().hide();
  $("#addCam3CB").parent().show();
  $("#remCam3CB").parent().show();
});
$("#remCam2CB").on("click", function () {
  $("#cam2CB").hide();
  $("#addCam2CB").parent().show();
  $("#cam2CB .varsteCopii").hide();
  $('#copiiCam2CB option').prop('selected', function () {
    return this.defaultSelected;
  });
});
$("#copiiCam2CB").on("change", function () {
  $(this).find("option:selected").each(function () {
    var optionValue = $(this).attr("value");
    if (optionValue == 2) {
      $("#cam2CB .varsteCopii").show();
      $("#varstaCop1Cam2CB").show();
      $("#varstaCop2Cam2CB").hide();
      $("#cam2CB .varsteCopii p#v1CB2").show();
      $("#cam2CB .varsteCopii p#v2CB2").hide();
    }
    if (optionValue == 3) {
      $("#cam2CB .varsteCopii").show();
      $("#varstaCop2Cam2CB").show();
      $("#varstaCop1Cam2CB").show();
      $("#cam2CB .varsteCopii p#v1CB2").show();
      $("#cam2CB .varsteCopii p#v2CB2").show();
    }
    if (optionValue == 1) {
      $("#cam2CB .varsteCopii").hide();
      $("#varstaCop1Cam2CB").hide();
      $("#varstaCop2Cam2CB").hide();
      $("#cam2CB .varsteCopii p#v1CB2").hide();
      $("#cam2CB .varsteCopii p#v2CB2").hide();
    }
  });
});
//adaugare & stergere camera 3 citybreak
$("#addCam3CB").on("click", function () {
  $("#cam3CB").show();
  $("#addCam3CB").parent().hide();
  $("#remCam2CB").parent().hide();
});
$("#remCam3CB").on("click", function () {
  $("#cam3CB").hide();
  $("#addCam3CB").parent().show();
  $("#remCam2CB").parent().show();
  $("#cam3CB .varsteCopii").hide();
  $('#copiiCam3CB option').prop('selected', function () {
    return this.defaultSelected;
  });
});
$("#copiiCam3CB").on("change", function () {
  $(this).find("option:selected").each(function () {
    var optionValue = $(this).attr("value");
    if (optionValue == 2) {
      $("#cam3CB .varsteCopii").show();
      $("#varstaCop1Cam3CB").show();
      $("#varstaCop2Cam3CB").hide();
      $("#cam3CB .varsteCopii p#v1CB3").show();
      $("#cam3CB .varsteCopii p#v2CB3").hide();
    }
    if (optionValue == 3) {
      $("#cam3CB .varsteCopii").show();
      $("#varstaCop2Cam3CB").show();
      $("#varstaCop1Cam3CB").show();
      $("#cam3CB .varsteCopii p#v1CB3").show();
      $("#cam3CB .varsteCopii p#v2CB3").show();
    }
    if (optionValue == 1) {
      $("#cam3CB .varsteCopii").hide();
      $("#varstaCop1Cam3CB").hide();
      $("#varstaCop2Cam3CB").hide();
      $("#cam3CB .varsteCopii p#v1CB3").hide();
      $("#cam3CB .varsteCopii p#v2CB3").hide();
    }
  });
});
setSearchStatus(true);
if(typeof citybreak_submit_function === 'undefined'){
  window.citybreak_submit_function = function (e){
    if(!search_is_over){
      console.log('A previous search is not complete. Ignoring request.');
      return;
    }
    setSearchStatus(false);
    setData();
    setSearchAndRedirect();
  };
}
$(document).on('submit', 'form.citybreak-search', function(e){
  e.preventDefault();
  citybreak_submit_function.call(this, e);
});
$('#cautaZborCB').attr('data-loading-text','<i class="fa fa-spinner fa-spin"></i> Se incarca ...');
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>