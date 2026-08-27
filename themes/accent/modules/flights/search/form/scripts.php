<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$data = &$this->flights_search_data;
?>
<script type="text/javascript">
var flight_search_data = <?php echo json_encode($data); ?>;
var flights_search_is_over = true;
(function($){
function updateFlightsSearchCalendar(){
  if(caleranEnd){
    caleranEnd.$elem.prop('disabled', flight_search_data.go_only);
    caleranEnd.config.minDate = caleranStart.config.endDate;
    if(caleranEnd.config.endDate.isBefore(caleranEnd.config.minDate)){
      caleranEnd.config.startDate = caleranEnd.config.endDate = caleranEnd.config.minDate;
      caleranEnd.updateInput(false);
    }
  }
}
<?php
	if($this->_ci->user->can('backend-access')){ ?>
	function setFiltersLink(){
		console.log(flight_search_data);
		var my_obj = {};
		my_obj.n = 1;
		/* if(parseInt(flight_search_data.city_id)){
      my_obj.city_id = parseInt(flight_search_data.city_id);
    } */
    if(flight_search_data.origin_location_name || flight_search_data.origin_city_name){
      my_obj.origin = flight_search_data.origin_location_name ? flight_search_data.origin_location_name : flight_search_data.origin_city_name;
    }
    if(flight_search_data.origin_location_name || flight_search_data.destination_city_name){
      my_obj.destination = flight_search_data.destination_location_name ? flight_search_data.destination_location_name : flight_search_data.destination_city_name;
    }
    if(flight_search_data.departure_date){
      my_obj.sdate = flight_search_data.departure_date;
    }
    if(flight_search_data.return_date){
      my_obj.edate = flight_search_data.return_date;
    }
    if(flight_search_data.passengers_adult){
      my_obj.a = flight_search_data.passengers_adult;
    }
    if(flight_search_data.passengers_senior){
      my_obj.s = flight_search_data.passengers_senior;
    }
    if(flight_search_data.varste_copii && flight_search_data.varste_copii.length){
      my_obj.c = flight_search_data.varste_copii.join(',');
    }
    if(flight_search_data.cabine_type){
      my_obj.class = flight_search_data.cabine_type;
    }
    if(flight_search_data.direct_only){
      my_obj.direct = 1;
    }
    if(flight_search_data.go_only){
      my_obj.go = 1;
    }
    if(flight_search_data.flexible_dates){
      my_obj.flex = 1;
    }
		var recursiveDecoded = decodeURIComponent( $.param( my_obj ) );
		var filters_link = 'trip/flights?' + recursiveDecoded;
		$('.trip_flight_search_link').val(filters_link);
	}
	window.setFlightFiltersLink = setFiltersLink;
	setFiltersLink();
	<?php } ?>

<?php
if($this->_ci->user->can('backend-access')){ ?>
$(document).on('change click', 'form.flight-search', function(e){
  setFlightFiltersLink();
});
<?php } ?>
var caleranStart, caleranEnd, startSelected = null, endSelected = null;
$("#dateZborAvion").caleran({
  startOnMonday: true,
  oninit: function (instance) {
    caleranStart = instance;
  },
  onbeforeshow: function () {
    caleranStart.$elem.val("");
  },
  onafterhide: function () {
    caleranStart.config.startDate = caleranStart.config.endDate;
    caleranStart.config.startSelected = caleranStart.config.startDate !== null;
    caleranStart.globals.keyboardHoverDate = null;
    caleranStart.updateInput(false);
  },
  singleDate: true,
  locale: 'ro',
  startEmpty: flight_search_data.departure_date === '',
  showFooter: false,
  autoCloseOnSelect: true,
  format: 'DD/MM/Y',
  minDate: moment().startOf('day'),
  startDate: moment(flight_search_data.departure_date,'Y-MM-DD'),
  endDate: moment(flight_search_data.departure_date,'Y-MM-DD'),
  onafterselect: function(caleran, startDate, endDate){
    if(!caleran.globals.firstValueSelected){
      return;
    }
    flight_search_data.departure_date = endDate.format("Y-MM-DD");
    caleranEnd.$elem.focus();
    var e = new Event('blur');
    e.target = caleran.elem;
    caleranEnd.showDropdown(e);
  }
}).on('change',function(){
  updateFlightsSearchCalendar();
});
$("#dateZborAvion2").caleran({
  startOnMonday: true,
  oninit: function (instance) {
    caleranEnd = instance;
  },
  onbeforeshow: function () {
    caleranEnd.$elem.val("");
  },
  onafterhide: function () {
    caleranEnd.config.startDate = caleranEnd.config.endDate;
    caleranEnd.config.startSelected = caleranEnd.config.startDate !== null;
    caleranEnd.globals.keyboardHoverDate = null;
    caleranEnd.updateInput(false);
  },
  singleDate: true,
  locale: 'ro',
  startEmpty: flight_search_data.return_date === '',
  showFooter: false,
  autoCloseOnSelect: true,
  format: 'DD/MM/Y',
  minDate: moment().startOf('day'),
  startDate: moment(flight_search_data.return_date,'Y-MM-DD'),
  endDate: moment(flight_search_data.return_date,'Y-MM-DD'),
  onafterselect: function(caleran, startDate, endDate){
    if(!caleran.globals.firstValueSelected){
      return;
    }
    flight_search_data.return_date = endDate.format("Y-MM-DD");
  }
});
function updateFlightsSearchInputs(){
  console.log(flight_search_data);
  $('#plecare').val(flight_search_data.origin_full_location_name);
  $('#sosire').val(flight_search_data.destination_full_location_name);
  $('#clasaZbor').val(flight_search_data.cabine_type);
  /* $('#tipComp').val(flight_search_data.company_type); */
  $('#doarDus').prop('checked',flight_search_data.go_only === true);
  $('#doarDirect').prop('checked',flight_search_data.direct_only === true);
  $('#dateFlexZbor').prop('checked',flight_search_data.flexible_dates === true);
  $("#adultiCam1Av").val(flight_search_data.passengers_adult);
  $("#adultisenioriCam1Av").val(flight_search_data.passengers_senior);
  var copii = 0 + flight_search_data.passengers_youth + flight_search_data.passengers_child + flight_search_data.passengers_infant_lap + flight_search_data.passengers_infant_seat;
  $('#copiiZbor').val(1 + copii).trigger('update');
  if(flight_search_data.varste_copii[0]){
    $('#aniCop1Zbor').val(1 + parseInt(flight_search_data.varste_copii[0]));
  }
  if(flight_search_data.varste_copii[1]){
    $('#aniCop2Zbor').val(1 + parseInt(flight_search_data.varste_copii[1]));
  }
  updateFlightsSearchCalendar();
}
// varste copii rezervare Bilete Avion
$("#copiiZbor").on("update", function () {
  $(this).find("option:selected").each(function () {
    var optionValue = $(this).attr("value");
    if (optionValue == 2) {
      $("#copiiZborArea .varsteCopii").show();
      $("#aniCop1Zbor").show();
      $("#aniCop2Zbor").hide();
      $("#copiiZborArea .varsteCopii p#v1Z").show();
      $("#copiiZborArea .varsteCopii p#v2Z").hide();
    }
    if (optionValue == 3) {
      $("#copiiZborArea .varsteCopii").show();
      $("#aniCop2Zbor").show();
      $("#aniCop1Zbor").show();
      $("#copiiZborArea .varsteCopii p#v1Z").show();
      $("#copiiZborArea .varsteCopii p#v2Z").show();
    }
    if (optionValue == 1) {
      $("#copiiZborArea .varsteCopii").hide();
      $("#aniCop2Zbor").hide();
      $("#aniCop1Zbor").hide();
      $("#copiiZborArea .varsteCopii p#v1Z").hide();
      $("#copiiZborArea .varsteCopii p#v2Z").hide();
    }
  });
});
$("#copiiZbor").on('change',function(){
  $(this).trigger('update');
});
$("#clasaZbor").on("change", function (e) {
  flight_search_data.cabine_type = parseInt($(this).val());
});
/* $("#tipComp").on("change", function (e) {
  flight_search_data.company_type = parseInt($(this).val());
}); */
$("#doarDirect").on("change", function (e) {
  flight_search_data.direct_only = $(this).prop('checked');
});
$('#doarDus').on('change',function(){
  flight_search_data.go_only = $(this).is(':checked');
  updateFlightsSearchCalendar();
});
$("#dateFlexZbor").on("change", function (e) {
  flight_search_data.flexible_dates = $(this).prop('checked');
});
function setSearchPassengerParameters(){
  flight_search_data.passengers_adult = parseInt($("#adultiCam1Av").val());
  flight_search_data.passengers_senior = parseInt($("#adultisenioriCam1Av").val());
  flight_search_data.passengers_youth = 0;
  flight_search_data.passengers_child = 0;
  flight_search_data.passengers_infant_lap = 0;
  flight_search_data.passengers_infant_seat = 0;
  flight_search_data.varste_copii = [];
  var copii = parseInt($('#copiiZbor').val()) -1;
  if(copii >= 1){
    var varsta_copil = parseInt($('#aniCop1Zbor').val()) - 1;
    flight_search_data.varste_copii.push(varsta_copil);
    if(varsta_copil < 3){
      if(flight_search_data.passengers_adult > flight_search_data.passengers_infant_lap){
        flight_search_data.passengers_infant_lap ++;
      } else {
        flight_search_data.passengers_child ++;
      }
    } else if(varsta_copil < 18){
      flight_search_data.passengers_child ++;
    }
  }
  if(copii >= 2){
    var varsta_copil = parseInt($('#aniCop2Zbor').val()) - 1;
    flight_search_data.varste_copii.push(varsta_copil);
    if(varsta_copil < 3){
      if(flight_search_data.passengers_adult > flight_search_data.passengers_infant_lap){
        flight_search_data.passengers_infant_lap ++;
      } else {
        flight_search_data.passengers_child ++;
      }
    } else if(varsta_copil < 18){
      flight_search_data.passengers_child ++;
    }
  }
  var total_adults = flight_search_data.passengers_adult + flight_search_data.passengers_senior;
  if(total_adults < flight_search_data.passengers_infant_lap){
    var inf_ad_diff = flight_search_data.passengers_infant_lap - total_adults;
    flight_search_data.passengers_infant_lap -= inf_ad_diff;
    flight_search_data.passengers_infant_seat += inf_ad_diff;
  }
  console.log(flight_search_data);
}
$("#adultiCam1Av").on("change", function (e) {
  setSearchPassengerParameters();
});
$("#adultisenioriCam1Av").on("change", function (e) {
  setSearchPassengerParameters();
});
$("#copiiZbor, #aniCop1Zbor, #aniCop2Zbor").on("change", function (e) {
  setSearchPassengerParameters();
});
updateFlightsSearchInputs();
$('#plecare, #sosire').autocomplete({
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
              country_id: item.CountryId,
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
    var is_origin = this.id === 'plecare';
    var prefix = is_origin ? 'origin' : 'destination';
    flight_search_data[prefix + '_city_name'] = ui.item.city_name;
    flight_search_data[prefix + '_country_name'] = ui.item.country_name;
    flight_search_data[prefix + '_location_name'] = ui.item.location_name;
    flight_search_data[prefix + '_full_location_name'] = ui.item.value;
    flight_search_data[prefix + '_location_id'] = ui.item.location_id;
    flight_search_data[prefix + '_city_id'] = ui.item.city_id;
    flight_search_data[prefix + '_country_id'] = ui.item.country_id;
  }
}).blur(function(){
  var is_origin = this.id === 'plecare';
  var prefix = is_origin ? 'origin' : 'destination';
  if(!this.value.length || this.value !== flight_search_data[prefix + '_full_location_name']){
    this.value = '';
    flight_search_data[prefix + '_city_name'] = '';
    flight_search_data[prefix + '_country_name'] = '';
    flight_search_data[prefix + '_location_name'] = '';
    flight_search_data[prefix + '_full_location_name'] = '';
    flight_search_data[prefix + '_location_id'] = 0;
    flight_search_data[prefix + '_city_id'] = 0;
    flight_search_data[prefix + '_country_id'] = 0;
  }
});
function setSearchStatus(search_status){
  if(search_status){
    $('#flights-loading-screen').addClass('inactive');
    $('#cautaZbor').bootstrap_button('loading');
  } else {
    $('#flights-loading-screen').removeClass('inactive');
    $('#cautaZbor').bootstrap_button('reset');
  }
  flights_search_is_over = search_status;
}
window.setFlightsSearchStatus = setSearchStatus;
function setSearchAndRedirect(){
  console.log(flight_search_data);
  $.ajax({
    url: '<?php echo site_url('trip/flights/setSearch'); ?>',
    method: 'post',
    dataType: 'json',
    data: flight_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        setFlightsSearchStatus(true);
        return;
      }
      flight_search_data = result.data;
      window.location.href="<?php echo site_url('trip/flights/search'); ?>";
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setFlightsSearchStatus(true);
    }
  });
}
window.setFlightsSearchAndRedirect = setSearchAndRedirect;
if(typeof flights_submit_function === 'undefined'){
  window.flights_submit_function = function (e){
    if(!flights_search_is_over){
      console.log('A previous search is not complete. Ignoring request.');
      return;
    }
    setFlightsSearchStatus(false);
    setFlightsSearchAndRedirect();
  };
}
$(document).on('submit', 'form.flight-search', function(e){
  e.preventDefault();
  console.log('submitting', flights_submit_function);
  flights_submit_function.call(this, e);
});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>