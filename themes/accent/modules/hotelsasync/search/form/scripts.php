<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$data = $this->hotel_search_data;
?>
<script type="text/javascript">
var hotel_search_data = <?php echo json_encode($data); ?>;
var hotel_submit_function;
var search_is_over = true;
(function($){
  $("#addAvionHotel").on("change", function () {
    $(this).is(':checked') ? $("#inpZborHot").show() : $("#inpZborHot").hide();
  });
	<?php
	if($this->_ci->user->can('backend-access')){ ?>
	function setFiltersLink(){
		var my_obj = {};
		my_obj.n = 1;
		// if(parseInt(hotel_search_data.city_id)){
      // my_obj.city_id = parseInt(hotel_search_data.city_id);
    // }
    if(hotel_search_data.hotel_name){
      my_obj.hotel = hotel_search_data.hotel_name;
    }
    if(hotel_search_data.city_name){
      my_obj.city_name = hotel_search_data.city_name;
    }
    if(hotel_search_data.start_date){
      my_obj.sdate = hotel_search_data.start_date;
    }
    if(hotel_search_data.end_date){
      my_obj.edate = hotel_search_data.end_date;
    }
    if(hotel_search_data.occupancy.length){
      my_obj.o = hotel_search_data.occupancy;
    }
		if(hotel_search_data.filters){
			if(hotel_search_data.filters.activity_categories && hotel_search_data.filters.activity_categories.length){
				my_obj.a = hotel_search_data.filters.activity_categories.join(',');
			}
			if(hotel_search_data.filters.stars && hotel_search_data.filters.stars.length){
				my_obj.s =  hotel_search_data.filters.stars.join(',');
			}
			if(hotel_search_data.filters.pois && hotel_search_data.filters.pois.length){
				my_obj.p =  hotel_search_data.filters.pois.join(',');
			}
			if(hotel_search_data.filters.facilities && hotel_search_data.filters.facilities.length){
				my_obj.f =  hotel_search_data.filters.facilities.join(',');
			}
		}
		var recursiveDecoded = decodeURIComponent( $.param( my_obj ) );
		var filters_link = 'trip/hotelsasync?' + recursiveDecoded;
		$('.trip_hotel_search_link').val(filters_link);
	}
	window.setHotelFiltersLink = setFiltersLink;
	setFiltersLink();
	<?php } ?>
	function setSearchStatus(search_status){
		if(search_status){
			$('#loading-screen').addClass('inactive');
			$('#cautaHotel').bootstrap_button('loading');
		} else {
			$('#loading-screen').removeClass('inactive');
			$('#cautaHotel').bootstrap_button('reset');
		}
		search_is_over = search_status;
	}
	window.setHotelSearchStatus = setSearchStatus;
	function setData($form){
		hotel_search_data.hotel_id = $('#hotelId', $form).length ? $('#hotelId', $form).val() : '';
		// hotel_search_data.city_id = $('#hotelSearchCityId', $form).length ? $('#hotelSearchCityId', $form).val() : '';
		// hotel_search_data.start_date = $('#hotelSearchStartDate', $form).length ? $('#hotelSearchStartDate', $form).val() : '';
		// hotel_search_data.end_date = $('#hotelSearchEndDate', $form).length ? $('#hotelSearchEndDate', $form).val() : '';
		// hotel_search_data.city_name = $('#destinatie', $form).length ? $('#destinatie', $form).val() : null;
		hotel_search_data.hotel_name = $('#numeHotel', $form).length ? $('#numeHotel', $form).val() : null;
		hotel_search_data.min_stars = $('#categHotel', $form).length ? $('#categHotel', $form).val() : null;
		// hotel_search_data.add_flight = $('#addAvionHotel', $form).is(':checked');
		// hotel_search_data.depart_city = $('#inpZborHot', $form).val();
		// hotel_search_data.weekend = $('#weekendSearch', $form).is(':checked');
		hotel_search_data.occupancy = [];
		
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
		hotel_search_data.occupancy.push(ocuppancy_1);
		
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
			hotel_search_data.occupancy.push(ocuppancy_2);
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
			hotel_search_data.occupancy.push(ocuppancy_3);
		}
		
		hotel_search_data.sort_by = 'MinPrice';
		hotel_search_data.sort_order = 0;
		
		var sort_element = $('.hotel-sort-by').filter(function(){return $(this).val()>0;}).first();
		if(sort_element.length){
			hotel_search_data.sort_by = sort_element.attr('name');
			hotel_search_data.sort_order = parseInt(sort_element.val()) - 1;
		}
		<?php
		if($this->_ci->user->can('backend-access')){ ?>
		setFiltersLink();
		<?php } ?>
	}
	window.setHotelData = setData;
	$(document).on('change click', 'form.hotel-search', function(e){
    setData();
  });

	function setSearchAndRedirect(){
		hotel_search_data.filters.min_price=null;
		hotel_search_data.filters.max_price=null;
		console.log(hotel_search_data);
		$.ajax({
			url: '<?php echo site_url('trip/hotelsasync/setSearch'); ?>',
			method: 'post',
			dataType: 'json',
			data: hotel_search_data,
			async: true,
			success: function(result,status,xhr){
				console.log(result);
				if(!result.status || result.status !== 'success'){
					setSearchStatus(true);
					return;
				}
				hotel_search_data = result.data;
				window.location.href="<?php echo site_url('trip/hotelsasync/search'); ?>";
			},
			error: function(jqXHR,textStatus,error){
				console.log(jqXHR, textStatus, error);
				setSearchStatus(true);
			}
		});
	}
	window.setHotelSearchAndRedirect = setSearchAndRedirect;
	$('#destinatie').autocomplete({
		source: function(request, response){
			$.ajax({
				url: "<?php echo site_url('trip/hotelsasync/loadLocations'); ?>",
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
							var label = item.Name + ' (' + item.CountryName + ')';
							var response_item = {
								id: item.CityId,
								value: item.Name,
								country: item.CountryName,
								country_id: item.CountryId,
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
			hotel_search_data.country_name = ui.item.country;
			hotel_search_data.city_name = ui.item.value;
			hotel_search_data.city_id = ui.item.id;
			hotel_search_data.country_id = ui.item.country_id;
		}
	}).blur(function(){
		if(this.value !== hotel_search_data.city_name){
			this.value = '';
			hotel_search_data.country_name = "";
			hotel_search_data.city_name = "";
			hotel_search_data.city_id = 0;
			hotel_search_data.country_id = 0;
      setData();
		}
	});
  
  function updateCalendar(){
    if(caleranEnd){
      caleranEnd.config.minDate = caleranStart.config.endDate;
      if(caleranEnd.config.endDate.isBefore(caleranEnd.config.minDate)){
        caleranEnd.config.startDate = caleranEnd.config.endDate = caleranEnd.config.minDate;
        caleranEnd.updateInput(false);
      }
    }
  }
  var caleranStart, caleranEnd, startSelected = null, endSelected = null;
  $("#dateHotel").caleran({
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
    startEmpty: hotel_search_data.start_date === '',
    showFooter: false,
    autoCloseOnSelect: true,
    format: 'DD/MM/Y',
    minDate: moment().startOf('day'),
    startDate: moment(hotel_search_data.start_date,'Y-MM-DD'),
    endDate: moment(hotel_search_data.start_date,'Y-MM-DD'),
    onafterselect: function(caleran, startDate, endDate){
      if(!caleran.globals.firstValueSelected){
        return;
      }
      hotel_search_data.start_date = endDate.format("Y-MM-DD");
      caleranEnd.$elem.focus();
      var e = new Event('blur');
      e.target = caleran.elem;
      caleranEnd.showDropdown(e);
    }
  }).on('change',function(){
    updateCalendar();
  });
  $("#dateHotel2").caleran({
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
    startEmpty: hotel_search_data.end_date === '',
    showFooter: false,
    autoCloseOnSelect: true,
    format: 'DD/MM/Y',
    minDate: moment().startOf('day'),
    startDate: moment(hotel_search_data.end_date,'Y-MM-DD'),
    endDate: moment(hotel_search_data.end_date,'Y-MM-DD'),
    onafterselect: function(caleran, startDate, endDate){
      if(!caleran.globals.firstValueSelected){
        return;
      }
      hotel_search_data.end_date = endDate.format("Y-MM-DD");
    }
  });
  
	/* $("#dateHotel").caleran({
    startOnMonday: true,
		locale: 'ro',
		startEmpty: hotel_search_data.start_date === '' || hotel_search_data.end_date === '',
		showFooter: false,
		autoCloseOnSelect: true,
		format: 'DD/MM/Y',
		minDate: moment(),
		startDate: moment(hotel_search_data.start_date,'Y-MM-DD'),
		endDate: moment(hotel_search_data.end_date,'Y-MM-DD'),
		onafterselect: function(caleran, startDate, endDate){
      if(!caleran.globals.firstValueSelected){
        return;
      }
			hotel_search_data.start_date = startDate.format("Y-MM-DD");
			hotel_search_data.end_date = endDate.format("Y-MM-DD");
		}
	}).on('change',function(){
		updateCalendar();
	});
	updateCalendar(); */
	$("#copiiCam1").on("change", function () {
		$(this).find("option:selected").each(function () {
			var optionValue = $(this).attr("value");
			if (optionValue == 2) {
				$("#cam1Hotel .varsteCopii").show();
				$("#varstaCop1Cam1").show();
				$("#varstaCop2Cam1").hide();
				$("#cam1Hotel .varsteCopii p#v1Cam1").show();
				$("#cam1Hotel .varsteCopii p#v2Cam1").hide();
			}
			if (optionValue == 3) {
				$("#cam1Hotel .varsteCopii").show();
				$("#varstaCop2Cam1").show();
				$("#varstaCop1Cam1").show();
				$("#cam1Hotel .varsteCopii p#v1Cam1").show();
				$("#cam1Hotel .varsteCopii p#v2Cam1").show();
			}
			if (optionValue == 1) {
				$("#cam1Hotel .varsteCopii").hide();
				$("#varstaCop1Cam1").hide();
				$("#varstaCop2Cam1").hide();
				$("#cam1Hotel .varsteCopii p#v1Cam1").hide();
				$("#cam1Hotel .varsteCopii p#v2Cam1").hide();
			}
		});
	});
	//adaugare & stergere camera 2 hotel
	$("#addCam2").on("click", function () {
		$("#cam2Hotel").show();
		$("#addCam2").parent().hide();
		$("#addCam3").parent().show();
		$("#remCam3").parent().show();
	});
	$("#remCam2").on("click", function () {
		$("#cam2Hotel").hide();
		$("#addCam2").parent().show();
		$("#cam2Hotel .varsteCopii").hide();
		$('#copiiCam2 option').prop('selected', function () {
			return this.defaultSelected;
		});
	});
	$("#copiiCam2").on("change", function () {
		$(this).find("option:selected").each(function () {
			var optionValue = $(this).attr("value");
			if (optionValue == 2) {
				$("#cam2Hotel .varsteCopii").show();
				$("#varstaCop1Cam2").show();
				$("#varstaCop2Cam2").hide();
				$("#cam2Hotel .varsteCopii p#v1Cam2").show();
				$("#cam2Hotel .varsteCopii p#v2Cam2").hide();
			}
			if (optionValue == 3) {
				$("#cam2Hotel .varsteCopii").show();
				$("#varstaCop2Cam2").show();
				$("#varstaCop1Cam2").show();
				$("#cam2Hotel .varsteCopii p#v1Cam2").show();
				$("#cam2Hotel .varsteCopii p#v2Cam2").show();
			}
			if (optionValue == 1) {
				$("#cam2Hotel .varsteCopii").hide();
				$("#varstaCop1Cam2").hide();
				$("#varstaCop2Cam2").hide();
				$("#cam2Hotel .varsteCopii p#v1Cam2").hide();
				$("#cam2Hotel .varsteCopii p#v2Cam2").hide();
			}
		});
	});
	//adaugare & stergere camera 3 hotel
	$("#addCam3").on("click", function () {
		$("#cam3Hotel").show();
		$("#addCam3").parent().hide();
		$("#remCam2").parent().hide();
	});
	$("#remCam3").on("click", function () {
		$("#cam3Hotel").hide();
		$("#addCam3").parent().show();
		$("#remCam2").parent().show();
		$("#cam3Hotel .varsteCopii").hide();
		$('#copiiCam3 option').prop('selected', function () {
			return this.defaultSelected;
		});
	});
	$("#copiiCam3").on("change", function () {
		$(this).find("option:selected").each(function () {
			var optionValue = $(this).attr("value");
			if (optionValue == 2) {
				$("#cam3Hotel .varsteCopii").show();
				$("#varstaCop1Cam3").show();
				$("#varstaCop2Cam3").hide();
				$("#cam3Hotel .varsteCopii p#v1Cam3").show();
				$("#cam3Hotel .varsteCopii p#v2Cam3").hide();
			}
			if (optionValue == 3) {
				$("#cam3Hotel .varsteCopii").show();
				$("#varstaCop2Cam3").show();
				$("#varstaCop1Cam3").show();
				$("#cam3Hotel .varsteCopii p#v1Cam3").show();
				$("#cam3Hotel .varsteCopii p#v2Cam3").show();
			}
			if (optionValue == 1) {
				$("#cam3Hotel .varsteCopii").hide();
				$("#varstaCop1Cam3").hide();
				$("#varstaCop2Cam3").hide();
				$("#cam3Hotel .varsteCopii p#v1Cam3").hide();
				$("#cam3Hotel .varsteCopii p#v2Cam3").hide();
			}
		});
	});
	setSearchStatus(true);
	if(typeof hotel_submit_function === 'undefined'){
		window.hotel_submit_function = function (e){
			if(!search_is_over){
				console.log('A previous search is not complete. Ignoring request.');
				return;
			}
			setSearchStatus(false);
			setData(this);
			setSearchAndRedirect();
		};
	}
	console.log($('form.hotel-search'));
	$(document).on('submit', 'form.hotel-search', function(e){
		e.preventDefault();
		hotel_submit_function.call(this, e);
	});
	$('#cautaHotel').attr('data-loading-text','<i class="fa fa-spinner fa-spin"></i> Se incarca ...');
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>