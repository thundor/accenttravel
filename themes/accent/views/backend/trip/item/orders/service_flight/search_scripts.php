<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
(function($){
  var $error_container = $('#result_service_flight_form');
  var $service_flight_citybreak = $('#service_flight_citybreak');
  var $service_flight_form_fellows_wrapper = $('#service_flight_form_fellows_wrapper');
  var $service_flight_results = $('#service_flight_results');
  var $service_flight_tab = $('#service_flight_tab');
  var $service_flight_form_header = $('#service_flight_form_header');
  var $service_flight_search_submit = $('#service_flight_search_submit');
  var $service_flight_search_origin = $('#service_flight_search_origin');
  var $service_flight_search_destination = $('#service_flight_search_destination');
  var $service_flight_search_departure_date = $('#service_flight_search_departure_date');
  var $service_flight_search_return_date = $('#service_flight_search_return_date');
  var $service_flight_search_return = $('#service_flight_search_return');
  var $service_flight_search_cabine_type = $('#service_flight_search_cabine_type');
  var $service_flight_search_passengers_infant = $('#service_flight_search_passengers_infant');
  var $service_flight_search_passengers_infant_toggle = $('#service_flight_search_passengers_infant_toggle');
  var $service_flight_search_passengers_infant_lap = $('#service_flight_search_passengers_infant_lap');
  var $service_flight_search_passengers_infant_seat = $('#service_flight_search_passengers_infant_seat');
  var $service_flight_search_calendar_flights = $('#service_flight_search_calendar_flights');
  var $service_flight_search_filter_for_date_departure = $('#service_flight_search_filter_for_date_departure');
  var $service_flight_search_filter_for_date_return = $('#service_flight_search_filter_for_date_return');
  var $service_flight_search_flexible_dates = $('#service_flight_search_flexible_dates');
  var $service_flight_form_filters = $('#service_flight_form_filters');
  var $service_flight_search_sort_price = $('#service_flight_search_sort_price');
  var $service_flight_search_sort_company = $('#service_flight_search_sort_company');
  var $service_flight_search_sort_duration = $('#service_flight_search_sort_duration');
  var $service_flight_form_fellows_form = $('#service_flight_form_fellows_form');
  var $service_flight_search_flex_dates = $('#service_flight_search_flex_dates');
  var $result_service_flight_form_fellows_form = $('#result_service_flight_form_fellows_form');
  var $filter_checkbox_model = $('#hotel-filter-checkbox-model');
  var $flight_reset_filters = $('#flight_reset_filters');
  var $service_flight_search_results_tab = $('#service_flight_search_results_tab');
  var $service_flight_chosen_details = $('#service_flight_chosen_details');
  var $service_flight_form_fellows = $('#service_flight_form_fellows');
  var $price_slider = $("#flight-services-search-filter-price-slider-range").slider({
    range: true,
    min: 0,
    max: 0,
    values: [0, 0],
    slide: function (event, ui) {
      $(this).trigger('updatePrice',ui);
    }
  }).on('updatePrice', function(e, ui){
    if(flight_results && flight_results.price_range){
      if(ui){
        var slider_values = ui.values;
      } else {
        var $price_slider = $(this).slider();
        var slider_values = $price_slider.slider('values');
      }
      $("#flight-services-search-filter-price-slider-amount").val(parseFloat(flight_results.price_range[slider_values[ 0 ]]).toLocaleString('ro') + " <?php echo $this->_ci->currency_symbol; ?> - " + parseFloat(flight_results.price_range[slider_values[ 1 ]]).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    }
  }).on( "slidestop", function( event, ui ) {
    if(ui){
      var slider_values = ui.values;
    }
    interpretResults();
  });
  $service_flight_search_return.change(function(){
    var is_checked = $(this).is(':checked');
    $service_flight_search_return_date.prop('disabled', !is_checked).prop('required', is_checked);
    $service_flight_search_return_date.closest('.input-group').toggleClass('has-danger', is_checked);
    if(is_checked){
      $service_flight_search_return_date.select();
    }
  });
  $service_flight_search_passengers_infant_toggle.change(function(){
    var is_checked = $(this).is(':checked');
    $service_flight_search_passengers_infant.prop('disabled', is_checked);
    $service_flight_search_passengers_infant_lap.prop('readonly', !is_checked);
    $service_flight_search_passengers_infant_seat.prop('readonly', !is_checked);
  });

  var today_moment = moment().startOf('day');
  $('#service_flight_search_return_date').makeCaleranDatepicker({
    startEmpty: false,
    minDate: today_moment,
    endDate: today_moment,
    startDate: today_moment
  }).makeInputmaskDate();
  $('#service_flight_search_departure_date').makeCaleranDatepicker({
    startEmpty: false,
    minDate: today_moment,
    startDate: today_moment
  }).makeInputmaskDate().on('change', function(){
    var val_moment = moment(this.value, 'DD.MM.Y');
    if(!val_moment.isValid()){
      return;
    }
    var $checkout_caleran = $('#service_flight_search_return_date').data("caleran");
    $checkout_caleran.config.minDate = val_moment;
    var checkout_val = $('#service_flight_search_return_date').val();
    var checkout_val_moment = moment(checkout_val, 'DD.MM.Y');
    if(!checkout_val_moment.isValid() || checkout_val_moment.isBefore(val_moment)){
      $checkout_caleran.config.startDate = val_moment;
      $checkout_caleran.config.endDate = val_moment;
      checkout_val_moment = val_moment;
      checkout_val = checkout_val_moment.format('DD.MM.Y');
      $('#service_flight_search_return_date').val(checkout_val);
      $('#service_flight_search_return_date').focus();
    }
  });
  
  $('#service_flight_search_origin, #service_flight_search_destination').autocomplete({
    source: function(request, response){
      $.ajax({
        url: "<?php echo site_url('trip/flights/loadLocations'); ?>",
        dataType: "json",
        data: {
          q: request.term
        }
      }).done(function( resp ) {
        console.log('flight autocomplete', resp);
        if(!resp.status || resp.status !== 'success'){
          showMessage($error_container,'Eroare in cautarea locatiilor', 'danger');
          return;
        }
        var data = resp.response;
        var response_data = [];
        if(data && data.length){
          for (var i=0; i < data.length; i++){
            var item = data[i];
            var label = (item.LocationId>0 ? item.LocationName + ', ' : '') + item.CityName + ' (' + item.CountryName + ')';
            var response_item = {
              id: item.LocationId + '-' + item.CityId,
              location_id: item.LocationId,
              city_id: item.CityId,
              country_id: item.CountryId,
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
      });
    },
    minLength: 2,
    select: function( event, ui ) {
      var is_origin = $(this).is($service_flight_search_origin);
      var prefix = is_origin ? 'origin' : 'destination';
      $('#service_flight_search_' + prefix + '_location_id').val(ui.item.location_id);
      $('#service_flight_search_' + prefix + '_location_name').val(ui.item.location_name);
      $('#service_flight_search_' + prefix + '_city_id').val(ui.item.city_id);
      $('#service_flight_search_' + prefix + '_city_name').val(ui.item.city_name);
      $('#service_flight_search_' + prefix + '_country_id').val(ui.item.country_id);
      $('#service_flight_search_' + prefix + '_country_name').val(ui.item.country_name);
      $('#service_flight_search_' + prefix + '_full_location_name').val(ui.item.value);
    }
  }).on('blur',function(){
    var is_origin = $(this).is($service_flight_search_origin);
    var prefix = is_origin ? 'origin' : 'destination';
    if(!this.value.length || this.value !== $('#service_flight_search_' + prefix + '_full_location_name').val()){
      this.value = '';
      $('#service_flight_search_' + prefix + '_location_id').val(null);
      $('#service_flight_search_' + prefix + '_location_name').val(null);
      $('#service_flight_search_' + prefix + '_city_id').val(null);
      $('#service_flight_search_' + prefix + '_city_name').val(null);
      $('#service_flight_search_' + prefix + '_country_id').val(null);
      $('#service_flight_search_' + prefix + '_country_name').val(null);
      $('#service_flight_search_' + prefix + '_full_location_name').val(null);
    }
  });
  
  var search_is_over = true;  
  function setSearchStatus(search_status){
    $service_flight_search_submit.prop('disabled',!search_status);
    $('.flight-sort-by', $service_flight_search_results_tab).prop('disabled', !search_status);
    search_is_over = search_status;
  }
  
  var flight_search_data = <?php echo json_encode($this->flight_search_data); ?>, flight_results;
  console.log(flight_search_data);
  function createCalendar(){
    console.log('createCalendar');
    var go_only = flight_results.data.go_only;
    var $table = $('<table cellpadding="0" cellspacing="0" class="table mb-0 ' + (go_only ? 'go_only' : 'with_return') + '" />');
    $service_flight_search_calendar_flights.empty();
    console.log($service_flight_search_calendar_flights);
    $table.appendTo($service_flight_search_calendar_flights);
    var $tbody = $('<tbody />');
    $tbody.appendTo($table);
    var departure_date = moment(flight_results.data.departure_date);
    if(!go_only){
      var return_date = moment(flight_results.data.return_date);
    }
    var $tr_model = $('<tr />');
    var $th_model = $('<th />');
    var $td_model = $('<td />');
    
    var calendar_flights = {};
    var calendar_flights_cheapest;
    
    var response = flight_results.response;
    var flights = response._embedded.flights;
    
    for(var i=0; i<flights.length;i++){
      var flight = flights[i];
      if(typeof calendar_flights_cheapest === 'undefined' || flight.Price < calendar_flights_cheapest){
        calendar_flights_cheapest = flight.Price;
      }
      var route_types = flight.Routes;
      var combinations = flight.Combinations;
      for(var k=0; k<route_types[0].Route.length; k++){
        var route_dep = route_types[0].Route[k];
        var date_dep = route_dep.Segment[0].Origin.Date;
        if(typeof calendar_flights[date_dep] === 'undefined'){
          calendar_flights[date_dep] = {};
        }
        if(go_only){
          var date_ret = '0000-00-00';
        }
        if(!go_only){
          var combination_start = '0' + route_dep.Ref;
          for(var l=0; l<route_types[1].Route.length; l++){
            var route_ret = route_types[1].Route[l];
            var combination_end = '1' + route_ret.Ref;
            var combination = combination_start + '|' + combination_end;
            if(combinations.indexOf(combination) < 0){
              continue;
            }
            var date_ret = route_ret.Segment[0].Origin.Date;
            if(typeof calendar_flights[date_dep][date_ret] === 'undefined' || flight.Price < calendar_flights[date_dep][date_ret].price){
              calendar_flights[date_dep][date_ret] = {
                'price': flight.Price,
                'currency': flight.Currency,
                'departure': route_dep,
                'return': route_ret
              };
            }
          }
        } else if(typeof calendar_flights[date_dep][date_ret] === 'undefined' || flight.Price < calendar_flights[date_dep][date_ret].price) {
          calendar_flights[date_dep][date_ret] = {
            'price': flight.Price,
            'currency': flight.Currency,
            'departure': route_dep
          };
        }
      }
    }
    for(var x=-4; x<=3; x++){
      var $tr = $tr_model.clone();
      for(var y=-4; y<=(go_only?-3:3); y++){
        var $cell;
        if(x===-4 || y===-4){
          $cell = $th_model.clone();
          $cell.css({
            'text-transform':'capitalize',
            'vertical-align':'middle',
          });
        } else {
          $cell = $td_model.clone();
        }
        if(x===-4 && y===-4){
          $cell.addClass('bg-primary text-white');
          if(go_only){
            $cell.html('<i class="fa fa-hand-o-down"></i> Plecare');
          } else {
            $cell.html('Sosire <i class="fa fa-hand-o-right"></i><br /><i class="fa fa-hand-o-down"></i> Plecare');
          }
        }
        if(y!==-4){
          if(go_only) {
            if(x===-4){
              $cell.html('Preturi');
            }
            var ret_date_f = '0000-00-00';
          } else {
            var ret_date;
            if(y<0){
              ret_date = moment(return_date).subtract(-y,'days');
            } else if(y>0){
              ret_date = moment(return_date).add(y,'days');
            } else {
              ret_date = return_date;
            }
            if(x===-4){
              $cell.html(ret_date.locale('ro').format('ddd[,] D MMM'));
            }
            var ret_date_f = ret_date.format('Y-MM-DD');
          }
          $cell.attr({
            'data-return': ret_date_f
          });
        }
        if(x!==-4){
          var dep_date;
          if(x<0){
            dep_date = moment(departure_date).subtract(-x,'days');
          } else if(x>0){
            dep_date = moment(departure_date).add(x,'days');
          } else {
            dep_date = departure_date;
          }
          if(y===-4){
            $cell.html(dep_date.locale('ro').format('ddd[,] D MMM'));
          }
          var dep_date_f = dep_date.format('Y-MM-DD');
          $cell.attr({
            'data-departure': dep_date_f
          });
        }
        if(x!==-4 && y!==-4){
          if(typeof calendar_flights[dep_date_f] !== 'undefined' && typeof calendar_flights[dep_date_f][ret_date_f] !== 'undefined'){
            var flight = calendar_flights[dep_date_f][ret_date_f];
            var $cell_a = $('<a class="btn btn-secondary btn-block" href="#"/>').html(format_price(flight.price,flight.currency));
            $cell.addClass('p-1');
            $cell_a.appendTo($cell);
            $cell_a.popover({
              content: function(){
                return $(this).siblings('.toolTipPrice').html();
              },
              trigger: 'hover',
              container: 'body',
              animation: false,
              placement: 'top',
              html: true
            });
            var $cell_tooltip = $('\
            <div class="toolTipPrice" style="display: none;">\
              <table class="table table-bordered table-striped m-0" style="min-width:400px;">\
                <tbody>\
                  <tr>\
                    <td class="p-1 text-center">\
                      <strong>Plecare tur</strong>\
                    </td class="p-1">\
                    <td class="p-1 text-center">' +
                      ((company_image = getCompanyImageByCode(flight.departure.Segment[0].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.departure.Segment[0].Carrier.Marketing._ + '" title="' + flight.departure.Segment[0].Carrier.Marketing._ + '" />' : '') +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.departure.Segment[0].Carrier.Marketing._ +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.departure.Segment[0].Origin.Airport._ + ', ' + flight.departure.Segment[0].Origin.Airport.Code +
                    '</td>\
                    <td class="p-1 text-center">\
                      ' + (dep_dep = moment(flight.departure.Segment[0].Origin.Date + ' ' + flight.departure.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y HH:mm') + '\
                    </td>\
                  </tr>\
                  <tr>\
                    <td class="p-1 text-center">\
                      <strong>Sosire tur</strong>\
                    </td>\
                    <td class="p-1 text-center">' +
                      ((company_image = getCompanyImageByCode(flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing._ + '" title="' + flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing._ + '" />' : '') +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing._ +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.departure.Segment[flight.departure.Segment.length-1].Destination.Airport._ + ', ' + flight.departure.Segment[flight.departure.Segment.length-1].Destination.Airport.Code +
                    '</td>\
                    <td class="p-1 text-center">\
                      ' + (dep_dep = moment(flight.departure.Segment[flight.departure.Segment.length-1].Destination.Date + ' ' + flight.departure.Segment[flight.departure.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y HH:mm') + '\
                    </td>\
                  </tr>' + (go_only ? '' : '\
                  <tr>\
                    <td class="p-1 text-center">\
                      <strong>Plecare retur</strong>\
                    </td class="p-1">\
                    <td class="p-1 text-center">' +
                      ((company_image = getCompanyImageByCode(flight.return.Segment[0].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.return.Segment[0].Carrier.Marketing._ + '" title="' + flight.return.Segment[0].Carrier.Marketing._ + '" />' : '') +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.return.Segment[0].Carrier.Marketing._ +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.return.Segment[0].Origin.Airport._ + ', ' + flight.return.Segment[0].Origin.Airport.Code +
                    '</td>\
                    <td class="p-1 text-center">\
                      ' + (dep_dep = moment(flight.return.Segment[0].Origin.Date + ' ' + flight.return.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y HH:mm') + '\
                    </td>\
                  </tr>\
                  <tr>\
                    <td class="p-1 text-center">\
                      <strong>Sosire retur</strong>\
                    </td>\
                    <td class="p-1 text-center">' +
                      ((company_image = getCompanyImageByCode(flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing._ + '" title="' + flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing._ + '" />' : '') +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing._ +
                    '</td>\
                    <td class="p-1 text-center">' +
                      flight.return.Segment[flight.return.Segment.length-1].Destination.Airport._ + ', ' + flight.return.Segment[flight.return.Segment.length-1].Destination.Airport.Code +
                    '</td>\
                    <td class="p-1 text-center">\
                      ' + (dep_dep = moment(flight.return.Segment[flight.return.Segment.length-1].Destination.Date + ' ' + flight.return.Segment[flight.return.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y HH:mm') + '\
                    </td>\
                  </tr>') + '\
                </tbody>\
              </table>\
            </div>');
            $cell_tooltip.appendTo($cell);
            if(typeof calendar_flights_cheapest !== 'undefined' && calendar_flights_cheapest == calendar_flights[dep_date_f][ret_date_f].price){
              $cell.addClass('lowestPrice bg-success text-center');
            }
          }
        }
        $cell.addClass('text-center');
        $cell.appendTo($tr);
      }
      $tr.appendTo($tbody);
    }
    var flex_date = $service_flight_search_flexible_dates.is(':checked');
    if(flex_date){
      $service_flight_search_filter_for_date_departure.val('');
      $service_flight_search_filter_for_date_return.val('');
    } else {
      var $calendar_elem = $('td[data-return=' + (flight_results.data.go_only ? '0000-00-00' : flight_results.data.return_date) + '][data-departure=' + flight_results.data.departure_date + '] > a');
      if($calendar_elem.length && !$calendar_elem.hasClass('active')){
        activateCalendarDate($calendar_elem);
      }
    }
  }
  function getCompanyImageByCode(company_code){
    var company_image;
    var company_index = flight_results.results.companies_indexes[company_code];
    if(typeof company_index !== 'undefined'){
      company_image = flight_results.results.companies[company_index].img;
    }
    return company_image;
  }
  function activateCalendarDate($elem){
    var $td = $elem.closest('td');
    var is_active = $td.hasClass('active');
    $('td, th',$td.closest('table')).removeClass('active bg-danger bg-warning');
    if(!is_active){
      $service_flight_search_flexible_dates.prop('checked', false);
      $td.addClass('active bg-danger');
      var td_index = $td.parent('tr').children().index($td);
      $('>th:first-child',$td.parent('tr')).addClass('active bg-warning');
      $('>tr:first-child>th:nth-child('+ (td_index + 1) +')',$td.closest('tbody')).addClass('active bg-warning');
      $service_flight_search_filter_for_date_departure.val($td.data('departure'));
      $service_flight_search_filter_for_date_return.val($td.data('return'));
    } else {
      $service_flight_search_flexible_dates.prop('checked', true);
      $service_flight_search_filter_for_date_departure.val('');
      $service_flight_search_filter_for_date_return.val('');
    }
  }
  function interpretResults(){
    if(!flight_results){
      return;
    }
    var go_only = flight_results.data.go_only;
    var response = flight_results.response;
    setSearchStatus(false);
    flight_results.flights = [];
    var sort_by_company = parseInt($service_flight_search_sort_company.val());
    var sort_by_price = parseInt($service_flight_search_sort_price.val());
    var sort_by_duration = parseInt($service_flight_search_sort_duration.val());
    var filter_min_price = -1;
    var filter_max_price = -1;
    if(typeof flight_results.price_range !== 'undefined'){
      var price_values = $price_slider.slider('values');
      var filter_min_price = flight_results.price_range[price_values[0]];
      var filter_max_price = flight_results.price_range[price_values[1]];
    }
    flight_results.price_range = [];
    var for_date_departure = $service_flight_search_filter_for_date_departure.val();
    if(!for_date_departure.length){
      for_date_departure = false;
    }
    var for_date_return = $service_flight_search_filter_for_date_return.val();
    if(!for_date_return.length){
      for_date_return = false;
    }
    var filter_company_codes = [];
    $('input[name="filters[companies][]"]:checked', $service_flight_form_filters).each(function(){ filter_company_codes.push(this.value)});
    var filter_stops = [];
    $('input[name="filters[stops][]"]:checked', $service_flight_form_filters).each(function(){ filter_stops.push(this.value)});
    for(var i=0; i<response._embedded.flights.length;i++){
      var skip_flight = false;
      if(filter_min_price>=0 && response._embedded.flights[i].Price < filter_min_price){
        skip_flight = true;
      }
      if(!skip_flight && filter_max_price>=0 && response._embedded.flights[i].Price > filter_max_price){
        skip_flight = true;
      }
      var flight = $.extend(true, {}, response._embedded.flights[i]);
      var route_types = flight.Routes;
      if(!go_only){
        var combinations = flight.Combinations;
        var return_combinations = {};
        var departure_combinations = {};
        for(var h=0; h<combinations.length; h++){
          var combination = combinations[h];
          var combination_split = combination.split('|');
          var departure_index = combination_split[0];
          var return_index = combination_split[1];
          if(typeof return_combinations[return_index] === 'undefined'){
            return_combinations[return_index] = [];
          }
          if(typeof departure_combinations[departure_index] === 'undefined'){
            departure_combinations[departure_index] = [];
          }
          return_combinations[return_index].push(departure_index);
          departure_combinations[departure_index].push(return_index);
        }
      }
      for(var j=0; j<route_types.length; j++){
        var route_type = route_types[j];
        var routes = route_type.Route;
        for(var k=0; k<routes.length; k++){
          var route = routes[k];
          var unset_route = false;
          if(!go_only && j==1){
            var return_combination = '' + j + route.Ref;
            if(typeof return_combinations[return_combination] === 'undefined' || !return_combinations[return_combination].length){
              unset_route = true;
            }
          }
          if(!unset_route){
            var route_stops = route.Segment.length - 1;
            route.escale = route_stops;
            if(filter_stops.length && filter_stops.indexOf('' + route_stops) < 0){
              unset_route = true;
            }
          }
          if(!unset_route){
            if(!unset_route && j==0 && for_date_departure && route.Segment[0].Origin.Date != for_date_departure){
              unset_route = true;
            }
            if(!unset_route && j==1 && for_date_return && route.Segment[0].Origin.Date != for_date_return){
              unset_route = true;
            }
            if(!unset_route){
              if(filter_company_codes.length){
                unset_route = true;
                for(var l=0; l<route.Segment.length; l++){
                  var segment=route.Segment[l];
                  if(filter_company_codes.indexOf(segment.Carrier.Marketing.Code) >= 0){
                    unset_route = false;
                    break;
                  }
                }
              }
            }
          }
          if(!route.Segment.length){
            unset_route = true;
          }
          if(unset_route){
            if(!go_only){
              if(j==0){
                var departure_combination = '' + j + route.Ref;
                if(typeof departure_combinations[departure_combination] !== 'undefined'){
                  if(departure_combinations[departure_combination].length){
                    for(var h=0; h<departure_combinations[departure_combination].length;h++){
                      var return_combination = departure_combinations[departure_combination][h];
                      if(typeof return_combinations[return_combination] !== 'undefined' && return_combinations[return_combination].length){
                        var return_departure_index = return_combinations[return_combination].indexOf(departure_combination);
                        if(return_departure_index>-1){
                          return_combinations[return_combination].splice(return_departure_index, 1);
                          combinations.splice(combinations.indexOf(departure_combination + '|' + return_combination),1);
                        }
                      }
                    }
                  }
                  delete departure_combinations[departure_combination];
                }
              } else if(j==1){
                var return_combination = '' + j + route.Ref;
                if(typeof return_combinations[return_combination] !== 'undefined'){
                  if(return_combinations[return_combination].length){
                    for(var h=0; h<return_combinations[return_combination].length;h++){
                      var departure_combination = return_combinations[return_combination][h];
                      if(typeof departure_combinations[departure_combination] !== 'undefined' && departure_combinations[departure_combination].length){
                        var departure_return_index = departure_combinations[departure_combination].indexOf(return_combination);
                        if(departure_return_index>-1){
                          departure_combinations[departure_combination].splice(departure_return_index, 1);
                          combinations.splice(combinations.indexOf(departure_combination + '|' + return_combination),1);
                        }
                      }
                    }
                  }
                  delete return_combinations[return_combination];
                }
              }
            }
            routes.splice(k--, 1);
            continue;
          }
        }
        if(!routes.length){
          skip_flight = true;
          break;
        }
        if(!skip_flight && sort_by_duration > 0){
          routes.sort(function(r1,r2){
            var modifier = sort_by_duration === 2 ? -1 : 1;
            var a = r1.Duration;
            var b = r2.Duration;
            return a < b ? -1 * modifier : (a > b ? 1 * modifier : 0);
          });
        }
      }
      if(!skip_flight && route_types[0].Route.length && (!route_types[1] || route_types[1].Route.length)){
        flight_results.flights.push(flight);
      }
      flight_results.price_range.push(flight.Price);
    }
    flight_results.price_range = flight_results.price_range.sort(function(a,b) {
      return a - b;
    });
    if(sort_by_company > 0){
      flight_results.flights.sort(function(r1,r2){
        var modifier = sort_by_company === 2 ? -1 : 1;
        var a = r1.Routes[0].Route[0].Segment[0].Carrier.Marketing._.toLowerCase();
        var b = r2.Routes[0].Route[0].Segment[0].Carrier.Marketing._.toLowerCase();
        return a < b ? -1 * modifier : (a > b ? 1 * modifier : 0);
      });
    }
    if(sort_by_price === 2 || sort_by_company > 0){
      flight_results.flights.sort(function(r1,r2){
        var modifier = sort_by_price === 2 ? -1 : 1;
        var a = r1.Price;
        var b = r2.Price;
        if(sort_by_company > 0){
          var c1 = r1.Routes[0].Route[0].Segment[0].Carrier.Marketing._.toLowerCase();
          var c2 = r2.Routes[0].Route[0].Segment[0].Carrier.Marketing._.toLowerCase();
          if(c1 !== c2){
             modifier = sort_by_company === 2 ? -1 : 1;
             a = c1;
             b = c2;
          }
        }
        return a < b ? -1 * modifier : (a > b ? 1 * modifier : 0);
      });
    }
    $error_container.empty();
    if(flight_results.flights.length == 1){
      showMessage($error_container,'A fost gasit un zbor','success');
    } else {
      showMessage($error_container,'Au fost gasite ' + flight_results.flights.length + ' zboruri','success');
    }
    showResults();
  }
  function loadFilters(){
    clearFilters();
    console.log('loadFilters', flight_results);
    $price_slider.slider('option',{
      min: 0,
      max: flight_results.price_range.length-1,
      values: [0, flight_results.price_range.length-1],
    });
    var flex_dates = flight_results.data.flex_dates;
    $service_flight_search_flexible_dates.prop('checked', flex_dates);
    $service_flight_search_flexible_dates.closest('.card').toggle(flex_dates);
    $price_slider.trigger('updatePrice');
    var $stopsBox = $('.flights-filter.flights-filter-stops');
    $stopsBox.empty();
    for(var i=0; i<flight_results.results.stops.length; i++){
      var numar_escale = flight_results.results.stops[i];
      var text = 'Zbor direct';
      if(numar_escale == 1){
        text = '1 Escala'; 
      } else if(numar_escale > 1){
        text = numar_escale + ' Escale'; 
      }
      var $checkWrapper = $filter_checkbox_model.clone().removeAttr('id');
      $('.filter-option-input',$checkWrapper).attr('name','filters[stops][]').val(numar_escale);
      var $checkLabel = $('.filter-option-description', $checkWrapper);
      $checkLabel.text(text);
      $('.flight-stops-filter', $service_flight_form_filters).append($checkWrapper);
    }
    for(var i=0; i<flight_results.results.companies.length; i++){
      var company = flight_results.results.companies[i];
      var code = company.code;
      var text = company.name;
      var image = company.img;
      var $checkWrapper = $filter_checkbox_model.clone().removeAttr('id');
      $('.filter-option-input',$checkWrapper).attr('name','filters[companies][]').val(code);
      var $checkLabel = $('.filter-option-description', $checkWrapper);
      $checkLabel.text(text);
      if(image){
        var $image = $('<img class="pull-right" style="max-height:30px;"/>').attr('src', image);
        $image.appendTo($checkLabel);
      }
      $('.flight-companies-filter', $service_flight_form_filters).append($checkWrapper);
    }
  }
  function clearFilters(){
    console.log('clearFilters');
    $('.flight-filter', $service_flight_form_filters).empty();
  }
  var flight_results;
  function loadResults(){
    if(!search_is_over){
      console.log('loadResults','Search is not over, aborting');
      return false;
    }
    setSearchStatus(false);
    // console.log('loadResults', flight_search_data);
    // $('#flightWarnings').hide();
    showMessage($error_container,'Se cauta zboruri <i class="fa fa-spinner fa-spin"></i>','warning');
    $.ajax({
      url: '<?php echo site_url('trip/flights/loadResults'); ?>',
      method: 'post',
      dataType: 'json',
      data: flight_search_data
    }).done(function(resp, textStatus, jqXHR){
      setSearchStatus(true);
      $error_container.empty();
      console.log('loadResults', resp);
      if(!resp || !resp.status || resp.status !== 'success' || !resp.response.code){
        interpretNoFlightsResponse(resp);
        return;
      }
      var response = resp.response;
      resp.data = flight_search_data;
      flight_results = resp;
      createCalendar();
      interpretResults();
      loadFilters();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadResults',jqXHR, textStatus, errorThrown);
      var resp = {status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR};
      interpretNoFlightsResponse(resp);
    });
  }
  function setSearchAndInitiate(){
    if(!search_is_over){
      console.log('setSearchAndInitiate','Search is not over, aborting');
      return false;
    }
    $error_container.empty();
    $('#service_flight_form_fellows').empty();
    console.log('setSearchAndInitiate');
    showMessage($error_container,'Se cauta zboruri <i class="fa fa-spinner fa-spin"></i>','warning');
    setSearchStatus(false);
    $.ajax({
      url: '<?php echo site_url('trip/flights/setSearchAndInitiate'); ?>',
      method: 'post',
      dataType: 'json',
      data: flight_search_data
    }).done(function(resp, textStatus, jqXHR){
      setSearchStatus(true);
      $error_container.empty();
      if(!resp || !resp.status || resp.status !== 'success' || !resp.response.code){
        interpretNoFlightsResponse(resp);
        return;
      }
      var response = resp.response;
      if(response.total_items == 1){
        showMessage($error_container,'A fost gasit un zbor','success');
      } else {
        showMessage($error_container,'Au fost gasite ' + response.total_items + ' zboruri','success');
      }
      flight_results = resp;
      createCalendar();
      interpretResults();
      loadFilters();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('setSearchAndInitiate',jqXHR, textStatus, errorThrown);
      var resp = {status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR};
      interpretNoFlightsResponse(resp);
    });
  }
  function showResults(){
    console.log('TODO','showResults');
    var flights = flight_results.flights;
    $service_flight_results.empty();
    for(var i=0; i<flights.length;i++){
      var flight = flights[i];
      var flight_index = i+1;
      var flight_price = parseFloat(flight.Price);
      var $flight_result = $('#flight_result_model').clone().removeAttr('id');
      $flight_result.attr({
        'data-flight_index' : i,
        'name' : 'flight_result_' + flight.ItineraryCode,
        'id' : 'flight_result_' + flight.ItineraryCode
      });
      $('.result-flight-code', $flight_result).val(flight_results.data.code);
      $('.result-flight-itinerary-code', $flight_result).val(flight.ItineraryCode);
      $('.flight-price', $flight_result).text(format_price(flight.Price,flight.Currency));
	  $('.flight-expected-price', $flight_result).val(flight.Price + '' + flight.Currency);
      $('.flight-index', $flight_result).text(flight_index);
      $flight_result.appendTo($service_flight_results);
      
      var combination_selected = flight.Combinations[0];
      var combination_arr = combination_selected.split('|');
      var route_types = flight.Routes;
      var flights_str_arr = [];
      
      var $flight_result_container = $('.flight-result-items', $flight_result);
      
      if(route_types.length == 2){
        var $flight_result_departure_return = $('#flight_result_departure_return_model').clone().removeAttr('id');
        $flight_result_departure_return.appendTo($flight_result_container);
        var $flight_result_departure_container = $('.flight-result-departure-items', $flight_result_departure_return);
        var $flight_result_return_container = $('.flight-result-return-items', $flight_result_departure_return);
      } else {
        var $flight_result_departure_container = $flight_result_container;
      }
      for(var j=0; j<route_types.length; j++){
        var $container = !j ? $flight_result_departure_container : $flight_result_return_container;
        var route_type = route_types[j];
        var routes = route_type.Route;
        for(var k=0; k<routes.length; k++){
          var route = routes[k];
          var route_duration = route.Duration;
          var cabin_types = [];
          var leaving_date = moment(route.Segment[0].Origin.Date + ' ' + route.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss');
          var arriving_date = moment(route.Segment[route.Segment.length-1].Destination.Date + ' ' + route.Segment[route.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss');
          var escale = route.Segment.length - 1;
          for(var l=0; l<=escale; l++){
            var escala = route.Segment[l];
            if(cabin_types.indexOf(escala.Flight.CabinType) < 0){
              cabin_types.push(escala.Flight.CabinType);
            }
          }
          var ischecked = combination_arr[j] == '' + j + route.Ref;
          var $route_model = $('#flight_result_route_model').clone().removeAttr('id');
          var $segment_card_header = $('>.card-header', $route_model);
          var segments_card_id_prefix = 'flight_' + i + '_type_' + j + '_route_' + k + '_segments';
          $('.btn-toggle-segments', $segment_card_header).attr({
            'data-target': '#' + segments_card_id_prefix + '_collapse',
            'aria-controls' : '#' + segments_card_id_prefix + '_collapse',
            'id': segments_card_id_prefix + '_header'
          });
          $segment_card_header.next('div').attr({
            'id' : segments_card_id_prefix + '_collapse',
            'aria-labelledby' :  segments_card_id_prefix + '_header'
          });
          var route_secured = 0;
          for(var l=0; l<route.Segment.length;l++){
            var segment = route.Segment[l];
            if(segment.Secured){
              route_secured = 1;
              break;
            }
          }
          if(!j){
            var departure_arrival_date = route.Segment[route.Segment.length-1].Destination.Date;
            var departure_arrival_time = route.Segment[route.Segment.length-1].Destination.Time;
          } else {
            var departure_arrival_date = route.Segment[0].Origin.Date;
            var departure_arrival_time = route.Segment[0].Origin.Time;
          }
          $('.flight-route-option-choice', $route_model).prop('checked',ischecked).attr({
            'name': 'option[' + j + ']',
            'data-secured': route_secured,
          }).val(route.Ref);
          
          $route_model.attr({
            'data-flight_type': j,
            'data-departure_arrival_date': departure_arrival_date,
            'data-departure_arrival_time': departure_arrival_time
          });
          
          $('.flight-duration', $route_model).text(route_duration);
          $('.leaving-date', $route_model).text(leaving_date.format('DD.MM.Y'));
          $('.leaving-hour', $route_model).text(leaving_date.format('HH:mm'));
          $('.arriving-date', $route_model).text(arriving_date.format('DD.MM.Y'));
          $('.arriving-hour', $route_model).text(arriving_date.format('HH:mm'));
          var leaving_airport_city = route.Segment[0].Origin.Airport.City;
          var leaving_airport_name = route.Segment[0].Origin.Airport._;
          var same_city = leaving_airport_city.toLowerCase() === ( !j ? flight_results.data.origin_city_name : flight_results.data.destination_city_name).toLowerCase();
          var same_airport = leaving_airport_name.toLowerCase() === ( !j ? flight_results.data.origin_location_name : flight_results.data.destination_location_name).toLowerCase();
          if(same_city && same_airport){
            $('.leaving-location', $route_model).remove;
          } else if(same_city){
            $('.leaving-airport-city', $route_model).remove();
          } else if(same_airport){
            $('.leaving-airport-name', $route_model).remove();
          }
          if(!same_city){
            $('.leaving-airport-city', $route_model).text(leaving_airport_city);
          }
          if(!same_airport){
            $('.leaving-airport-name', $route_model).text(leaving_airport_name);
          }
          var leaving_company_name = route.Segment[0].Carrier.Marketing._;
          $('.leaving-company-name', $route_model).text(leaving_company_name);
          var leaving_company_image = getCompanyImageByCode(route.Segment[0].Carrier.Marketing.Code);
          if(leaving_company_image){
            $('.leaving-company-image', $route_model).attr({
              'src': getCompanyImageByCode(route.Segment[0].Carrier.Marketing.Code)
            });
          } else {
            $('.leaving-company-image', $route_model).remove();
          }
          
          var arriving_airport_city = route.Segment[route.Segment.length-1].Destination.Airport.City;
          var arriving_airport_name = route.Segment[route.Segment.length-1].Destination.Airport._;
          
          var same_city = arriving_airport_city.toLowerCase() === ( j ? flight_results.data.origin_city_name : flight_results.data.destination_city_name).toLowerCase();
          var same_airport = arriving_airport_name.toLowerCase() === ( j ? flight_results.data.origin_location_name : flight_results.data.destination_location_name).toLowerCase();
          if(same_city && same_airport){
            $('.arriving-location', $route_model).remove;
          } else if(same_city){
            $('.arriving-airport-city', $route_model).remove();
          } else if(same_airport){
            $('.arriving-airport-name', $route_model).remove();
          }
          if(!same_city){
            $('.arriving-airport-city', $route_model).text(arriving_airport_city);
          }
          if(!same_airport){
            $('.arriving-airport-name', $route_model).text(arriving_airport_name);
          }
          var arriving_company_name = route.Segment[route.Segment.length-1].Carrier.Marketing._;
          var same_airline = arriving_company_name === leaving_company_name;
          if(same_airline){
            $('.arriving-company-details', $route_model).remove();
          } else {
            $('.arriving-company-name', $route_model).text(arriving_company_name);
            var arriving_company_image = getCompanyImageByCode(route.Segment[route.Segment.length-1].Carrier.Marketing.Code);
            if(arriving_company_image){
              $('.arriving-company-image', $route_model).attr({
                'src': getCompanyImageByCode(route.Segment[0].Carrier.Marketing.Code)
              });
            } else {
              $('.arriving-company-image', $route_model).remove();
            }
          }
          if(escale>0){
            $('.flight-without-stops', $route_model).remove();
            $('.flight-stops', $route_model).text(escale);
            if(escale>1){
              $('.flight-with-stops>.singular').remove();
            } else {
              $('.flight-with-stops>.plural').remove();
            }
          } else {
            $('.flight-with-stops', $route_model).remove();
            // $('.flight-stops-container', $route_model).remove();
          }
          
          var $segment_container = $('.flight-stops-block', $route_model);
          var prev_company_code = route.Segment[0].Carrier.Marketing.Code;
          for(var l=0; l<route.Segment.length;l++){
            var segment = route.Segment[l];
            var $segment = $('#flight_result_item_stop_model').clone().removeAttr('id');
            $segment.appendTo($segment_container);
            var leaving_date = moment(segment.Origin.Date + ' ' + segment.Origin.Time,'Y-MM-DD HH:mm:ss');
            var arriving_date = moment(segment.Destination.Date + ' ' + segment.Destination.Time,'Y-MM-DD HH:mm:ss');
            var stop_time = segment.Flight.StopTime;
            if(!stop_time){
              $('.leaving-stop-duration', $segment).remove();
            } else {
              $('.flight-stop-duration', $segment).text(moment(stop_time,'HH:mm:ss').format('HH:mm'));
            }
            $('.leaving-date', $segment).text(leaving_date.format('DD.MM.Y'));
            $('.leaving-hour', $segment).text(leaving_date.format('HH:mm'));
            $('.arriving-date', $segment).text(arriving_date.format('DD.MM.Y'));
            $('.arriving-hour', $segment).text(arriving_date.format('HH:mm'));
            var leaving_airport_city = segment.Origin.Airport.City;
            var leaving_airport_name = segment.Origin.Airport._;
            var segment_same_city = leaving_airport_city.toLowerCase() === ( !j ? flight_results.data.origin_city_name : flight_results.data.destination_city_name).toLowerCase();
            var segment_same_airport = leaving_airport_name.toLowerCase() === ( !j ? flight_results.data.origin_location_name : flight_results.data.destination_location_name).toLowerCase();
            if(segment_same_city && segment_same_airport){
              $('.leaving-location', $segment).remove;
            } else if(segment_same_city){
              $('.leaving-airport-city', $segment).remove();
            } else if(segment_same_airport){
              $('.leaving-airport-name', $segment).remove();
            }
            if(!segment_same_city){
              $('.leaving-airport-city', $segment).text(leaving_airport_city);
            }
            if(!segment_same_airport){
              $('.leaving-airport-name', $segment).text(leaving_airport_name);
            }
            $('.aircraft-name', $segment).text(( segment.Aircraft ? segment.Aircraft._ : '' ));
            
            
            var same_company = segment.Carrier.Marketing.Code == prev_company_code;
            prev_company_code = segment.Carrier.Marketing.Code;
            if(same_company){
              $('.company-details', $segment).remove();
            } else {
              var company_name = segment.Carrier.Marketing._;
              $('.company-name', $segment).text(company_name);
              var company_image = getCompanyImageByCode(segment.Carrier.Marketing.Code);
              if(company_image){
                $('.company-image', $segment).attr({
                  'src': getCompanyImageByCode(segment.Carrier.Marketing.Code)
                });
              } else {
                $('.company-image', $segment).remove();
              }
            }
            var segment_arriving_airport_city = segment.Destination.Airport.City;
            var segment_arriving_airport_name = segment.Destination.Airport._;
            
            var segment_same_city = segment_arriving_airport_city.toLowerCase() === ( j ? flight_results.data.origin_city_name : flight_results.data.destination_city_name).toLowerCase();
            var segment_same_airport = segment_arriving_airport_name.toLowerCase() === ( j ? flight_results.data.origin_location_name : flight_results.data.destination_location_name).toLowerCase();
            if(segment_same_city && segment_same_airport){
              $('.arriving-location', $segment).remove;
            } else if(segment_same_city){
              $('.arriving-airport-city', $segment).remove();
            } else if(segment_same_airport){
              $('.arriving-airport-name', $segment).remove();
            }
            if(!segment_same_city){
              $('.arriving-airport-city', $segment).text(segment_arriving_airport_city);
            }
            if(!segment_same_airport){
              $('.arriving-airport-name', $segment).text(segment_arriving_airport_name);
            }
          }
          if(j){
            var is_available = true;
            if(!ischecked){
              if(flight.Combinations.indexOf(combination_arr[0] + '|' + '1' + route.Ref) < 0){
                is_available = false;
              }
            }
            if(!is_available){
              $route_model.addClass('card-danger nocomb');
            }
          }
          $route_model.appendTo($container);
        }
      }
    }
    setSearchStatus(true);
  }
  function interpretNoFlightsResponse(result){
    $('.flight-sort-by', $service_flight_search_results_tab).prop('disabled', true);
    var $search_container_header = $('a[href="#service_flight_form_container"].collapsed');
    if($search_container_header.length){
      $search_container_header[0].click();
    }
    // $fellow_info_wrapper.hide();
    setSearchStatus(true);
    $error_container.empty();
    clearFilters();
    $service_flight_search_calendar_flights.empty();
    if(result.status == 'fail'){
      showMessage($error_container,result.message ? result.message : 'Eroare in cautarea zborurilor','warning');
    } else {
      showMessage($error_container,result.message ? result.message : 'Nu au fost gasite rezultate','warning');
    }
  }
  function serviceFlightFormSubmitCallback($form,resp,$err_container){
    console.log('serviceFlightFormSubmitCallback', resp);
    if(resp.status !== 'success'){
      return true;
    }
    flight_results = null;
    flight_search_data = resp.data;
    console.log('serviceFlightFormSubmitCallback',flight_search_data);
    setSearchAndInitiate();
  }
  $service_flight_search_submit.on('click',function(){
    var service_flight_search_departure_date = $service_flight_search_departure_date.val();
    if(!service_flight_search_departure_date){
      return true;
    }
    if($service_flight_search_return.is(':checked')){
      var service_flight_search_return_date = $service_flight_search_return_date.val();
      if(!service_flight_search_return_date){
        return true;
      }
      var start_date = moment(service_flight_search_departure_date,'DD.MM.Y');
      var end_date = moment(service_flight_search_return_date,'DD.MM.Y');
      if(end_date && start_date){
        if(end_date.isBefore(start_date)){
          $service_flight_search_departure_date.val(service_flight_search_return_date);
          $service_flight_search_return_date.val(service_flight_search_departure_date);
        }
      }
    }
    
    if(!$service_flight_search_passengers_infant_toggle.is(':checked')){
      var infants = parseInt($('#service_flight_search_passengers_infant').val());
      var ins = 0;
      var inf = 0;
      if(!isNaN(infants) && infants>0){
        var adults = 0;
        var adt = parseInt($('#service_flight_search_passengers_adult').val());
        if(!isNaN(adt)){
          adults+=adt;
        }
        var sen = parseInt($('#service_flight_search_passengers_senior').val());
        if(!isNaN(sen)){
          adults+=sen;
        }
        ins = infants > adults ? infants - adults : 0;
        inf = infants - ins;
      }
      $('#service_flight_search_passengers_infant_lap').val(inf);
      $('#service_flight_search_passengers_infant_seat').val(ins);
    }
    
    return true;
  });
  $('#service_flight_form').on('submit',function(){
    if(!search_is_over){
      console.log('service_flight_form', 'submit','Search is not over, aborting');
      return false;
    }
    $service_flight_results.empty();
    $error_container.empty();
    clearFilters();
    $service_flight_search_calendar_flights.empty();
    basicFormPostSubmit(this,this.action,serviceFlightFormSubmitCallback);
  });
  var done_recalculating = true;
  function recalculatePassengerBirthdateClasses(){
    if(!done_recalculating){
      console.log('not done recalculating');
      return false;
    }
    done_recalculating = false;
    console.log('recalculatePassengerBirthdateClasses');
    var service_flight_search_departure_date = $('#service_flight_search_departure_date').val();
    if(service_flight_search_departure_date && service_flight_search_departure_date !== ''){
      var reference_moment = moment(service_flight_search_departure_date,'DD.MM.Y').startOf('day');
    } else {
      var reference_moment = moment().startOf('day');
    }
    var $birth_dates = $('#service_flight_search_passenger_birthdates_table input.passenger-birth-date');
    if(!$birth_dates.length){
      $('#service_flight_search_passenger_birthdates_table>tfoot').hide();
      return;
    }
    $('#service_flight_search_passenger_birthdates_table>tfoot').css('display','');
    $birth_dates.each(function(){
      var age_in_years = null;
      var $tr = $(this).closest('tr');
      if(this.value){
        var val_moment = moment(this.value,'DD.MM.Y').startOf('day');
        var age_in_years = reference_moment.diff(val_moment,'years');
        $('.passenger-age', $tr).val(age_in_years);
      }
      $('.passenger-type',$tr).hide();
      $('.passenger-type-indeterminate',$tr).show();
    });
    var adult_passengers = 0;
    $('#service_flight_search_passenger_birthdates_table input.passenger-age').each(function(){
      var age_in_years = this.value;
      var $tr = $(this).closest('tr');
      if(isNaN(age_in_years) || age_in_years === '' || age_in_years > 150){
        return;
      }
      if(age_in_years >= 18){
        adult_passengers++;
      }
    });
    var remaining_adults = adult_passengers;
    console.log('start',remaining_adults);
    var infant_counter = 0;
    $('#service_flight_search_passenger_birthdates_table input.passenger-age').each(function(){
      var age_in_years = this.value;
      var $tr = $(this).closest('tr');
      if(isNaN(age_in_years) || age_in_years === '' || age_in_years > 150){
        $('.passenger-type-indeterminate',$tr).show();
        return;
      }
      $('.passenger-type-indeterminate',$tr).hide();
      if(age_in_years > 60){
        $('.passenger-type-senior', $tr).show();
        return;
      } else if(age_in_years >= 18){
        $('.passenger-type-adult', $tr).show();
        return;
      } else if(age_in_years >= 3){
        $('.passenger-type-child', $tr).show();
        return;
      }
      
      $('.passenger-type-infant-seat, .passenger-type-infant-lap, .passenger-type-infant-changed, .passenger-type-infant-selector, .passenger-type-infant-change, .passenger-type-infant-lack-adults', $tr).hide();
      $('.passenger-type-infant', $tr).show();
      infant_counter ++;
      if(infant_counter > 2*adult_passengers){
        $('.passenger-type-infant-lack-adults', $tr).show();
      }
      if(!remaining_adults){
        console.log('check seat',remaining_adults);
        $('.passenger-type-infant-selector', $tr).val(null);
        $('.passenger-type-infant-seat', $tr).show();
      } else {
        console.log('check lap',remaining_adults);
        var forced_type = $('.passenger-type-infant-selector', $tr).val();
        console.log($('.passenger-type-infant-selector', $tr));
        if(forced_type && forced_type !== ''){
          if(forced_type == 'seat'){
            $('.passenger-type-infant-seat', $tr).show();
          } else if(forced_type == 'lap'){
            $('.passenger-type-infant-lap', $tr).show();
            remaining_adults--;
          }
          $('.passenger-type-infant-changed', $tr).show();
          console.log('should show');
        } else {
          $('.passenger-type-infant-lap', $tr).show();
          remaining_adults--;
        }
        $('.passenger-type-infant-change', $tr).show();
      }
    });
    done_recalculating = true;
    if($('#service_flight_passengers_auto_determine').is(':checked')){
      $('#service_flight_search_passenger_birthdates_submit')[0].click();
    }
  }
  $('#service_flight_search_passenger_birthdates_form').on('submit', function(e){
    e.preventDefault();
    e.stopPropagation();
    console.log('Autodeterminare');
    var passenger_adult = 0;
    var passenger_senior = 0;
    var passenger_child = 0;
    var passenger_youth = 0;
    var passenger_infant_lap = 0;
    var passenger_infant_seat = 0;
    $('#service_flight_search_passenger_birthdates_table .passenger-age').each(function(){
      var age_in_years = this.value;
      if(isNaN(age_in_years) || age_in_years === '' || age_in_years > 150){
        return;
      }
      if(age_in_years > 60){
        passenger_senior++;
      } else if(age_in_years >= 18){
        passenger_adult++;
        return;
      } else if(age_in_years >= 3){
        passenger_child++;
        return;
      }
    });
    var passenger_adults = passenger_adult + passenger_senior;
    var remaining_adults = passenger_adults;
    $('#service_flight_search_passenger_birthdates_table .passenger-age').each(function(){
      var age_in_years = this.value;
      if(isNaN(age_in_years) || age_in_years === '' || age_in_years >= 3){
        return;
      }
      if(age_in_years >= 3){
        return;
      }
      var $tr = $(this).closest('tr');
      var forced_type = $('.passenger-type-infant-selector', $tr).val();
      if(!remaining_adults){
        passenger_infant_seat++;
        return;
      }
      if(forced_type && forced_type !== ''){
        if(forced_type == 'seat'){
          passenger_infant_seat++;
        } else if(forced_type == 'lap'){
          passenger_infant_lap++;
          remaining_adults--;
        }
      } else {
        passenger_infant_lap++;
        remaining_adults--;
      }
    });
    $('#service_flight_search_passengers_adult').val(passenger_adult);
    $('#service_flight_search_passengers_senior').val(passenger_senior);
    $('#service_flight_search_passengers_child').val(passenger_child);
    $('#service_flight_search_passengers_infant_lap').val(passenger_infant_lap);
    $('#service_flight_search_passengers_infant_seat').val(passenger_infant_seat);
    $('#service_flight_search_passengers_infant').val(passenger_infant_lap + passenger_infant_seat);
    return false;
  });
  $('#service_flight_search_add_passenger_birthdate').on('click', function(){
    var $tbody = $(this).closest('table').children('tbody');
    var $new_tr = $('#flight-passenger-birthdate-model').clone().removeAttr('id');
    $new_tr.appendTo($tbody);
    
    var reference_moment = moment().startOf('day');
    var min_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_moment = reference_moment;
    $('input.passenger-birth-date', $new_tr).makeCaleranDatepicker({
      minDate: min_moment,
      maxDate: max_moment,
      startDate: max_moment,
      startEmpty: false
    }).makeInputmaskDate();
    recalculatePassengerBirthdateClasses();
  });
  $('#service_flight_search_passenger_birthdates_table').on('click', '.btn-delete-passenger-birthdate', function(){
    $(this).closest('tr').remove();
    recalculatePassengerBirthdateClasses();
  });
  $('#service_flight_search_passenger_birthdates_table').on('click', '.btn-passenger-type-infant-change', function(){
    $(this).closest('.passenger-type-infant-detail').hide().next('select').show().focus();
  });
  $('#service_flight_search_passenger_birthdates_table').on('blur', 'select.passenger-type-infant-selector', function(){
    $(this).hide().prev('.passenger-type-infant-detail').show();
  }).on('change', 'select.passenger-type-infant-selector', function(){
    $(this).hide().prev('.passenger-type-infant-detail').show();
    recalculatePassengerBirthdateClasses();
  });
  $('#service_flight_search_passenger_birthdates_table').on('change', 'input.passenger-birth-date', function(){
    recalculatePassengerBirthdateClasses();
  });
  $flight_reset_filters.click(function(){
    $price_slider.slider('option',{
      min: 0,
      max: flight_results.price_range.length-1,
      values: [0, flight_results.price_range.length-1],
    });
    $price_slider.trigger('updatePrice');
    $('input[name="filters[companies][]"]:checked', $service_flight_form_filters).prop('checked',false);
    $('input[name="filters[stops][]"]:checked', $service_flight_form_filters).prop('checked',false);
    interpretResults();
  });
  $service_flight_search_flexible_dates.on('change', function(e){
    createCalendar();
    interpretResults();
    loadFilters();
  });
  $service_flight_search_calendar_flights.on('click', 'td a', function(e){
    activateCalendarDate($(this));
    interpretResults();
  });
  $service_flight_search_sort_price.change(function(){
    interpretResults();
  });
  $service_flight_search_sort_company.change(function(){
    interpretResults();
  });
  $service_flight_search_sort_duration.change(function(){
    interpretResults();
  });
  $service_flight_form_filters.on('click','.flight-filter input[type=checkbox]',function(){
    interpretResults();
  });
  function interpretRouteAvailability(flight_type,route_ref, $box_ticket, flight_index){
    var comb = flight_type + route_ref;
    $('.flight-result-' + (flight_type == 0 ? 'return' : 'departure') + '-items input[type=radio][name^="option["]', $box_ticket).each(function(){
      var rmatches = this.name.match(/option\[(\d+)\]/);
      var rflight_type = rmatches[1];
      var rroute_ref = this.value;
      var rcomb = rflight_type + rroute_ref;
      var combination = flight_type == 0 ? comb + '|' + rcomb : rcomb + '|' + comb;
      var has_no_comb = flight_results.flights[flight_index].Combinations.indexOf(combination) < 0;
      var $parent_row = $(this).closest('.flight-result-route');
      if(!has_no_comb){
        $parent_row.removeClass('card-danger nocomb');
      } else {
        $parent_row.addClass('card-danger nocomb');
        $(this).prop('checked', false).removeAttr('checked');
      }
    });
  }
  $service_flight_results.on('change','input[type=radio][name^="option["]', function(){
    if(!$(this).is(':checked')){
      return;
    }
    if(flight_results.data.go_only){
      return;
    }
    var matches = this.name.match(/option\[(\d+)\]/);
    var flight_type = matches[1];
    var flight_index = parseInt($(this.form).data('flight_index'));
    var route_ref = this.value;
    $(this).closest('.flight-result-route').parent().children().removeClass('card-danger nocomb');
    interpretRouteAvailability(flight_type,route_ref, $(this.form), flight_index);
  });
  function loadPassengerDetails(secured){
    $service_flight_form_fellows.empty();
    var $flight_passenger_model = $('#flight_passenger_model').clone().removeAttr('id');
    if(!secured){
      $('.flight-passenger-info-secured', $flight_passenger_model).remove();
    }
    var reference_moment = moment(flight_results.data.departure_date,'Y-MM-DD').startOf('day');
    var min_senior_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_senior_moment = moment(reference_moment).add(-61,'years').startOf('day');
    var min_adult_moment = moment(max_senior_moment).add(1,'day');
    var max_adult_moment = moment(reference_moment).add(-18,'years').startOf('day');
    var min_child_moment = moment(max_adult_moment).add(1,'day');
    var max_child_moment = moment(reference_moment).add(-3,'years');
    var min_infant_moment = moment(max_child_moment).add(1,'day');
    var max_infant_moment = moment().startOf('day');
    if(flight_results.data.passengers_senior){
      for(var i=0; i< flight_results.data.passengers_senior; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-senior');
        $('.passenger-type:not(.passenger-type-senior)', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(i+1);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'passenger[SEN][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_flight_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_senior_moment,
          maxDate: max_senior_moment,
          startDate: max_senior_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
      }
    }
    if(flight_results.data.passengers_adult){
      for(var i=0; i< flight_results.data.passengers_adult; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-adult');
        $('.passenger-type:not(.passenger-type-adult)', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(i+1);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'passenger[ADT][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_flight_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_adult_moment,
          maxDate: max_adult_moment,
          startDate: max_adult_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
      }
    }
    $('.flight-passenger-info-adult', $flight_passenger_model).remove();
    if(flight_results.data.passengers_child){
      for(var i=0; i< flight_results.data.passengers_child; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-child');
        $('.passenger-type:not(.passenger-type-child)', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(i+1);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'passenger[CHD][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_flight_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_child_moment,
          maxDate: max_child_moment,
          startDate: max_child_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
      }
    }
    var infant_index = 0;
    if(flight_results.data.passengers_infant_lap){
      for(var i=0; i < flight_results.data.passengers_infant_lap; i++){
        infant_index++;
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-infant');
        $('.passenger-type:not(.passenger-type-infant)', $flight_passenger).remove();
        $('.passenger-type-infant-seat', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(infant_index);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'passenger[INF][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_flight_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_infant_moment,
          maxDate: max_infant_moment,
          startDate: max_infant_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
      }
    }
    if(flight_results.data.passengers_infant_seat){
      for(var i=0; i < flight_results.data.passengers_infant_seat; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-infant');
        $('.passenger-type:not(.passenger-type-infant)', $flight_passenger).remove();
        $('.passenger-type-infant-lap', $flight_passenger).remove();
        infant_index++;
        $('.passenger-index', $flight_passenger).text(infant_index);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'passenger[INS][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_flight_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_infant_moment,
          maxDate: max_infant_moment,
          startDate: max_infant_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
      }
    }
    if(secured){
      $('.passenger-passport_issuing_country', $service_flight_form_fellows).select2_4({language:'ro',theme:'bootstrap',placeholder:'Tara emitere', data: select2_countries_selections, width: '100%'});
      $('.passenger-passport_nationality', $service_flight_form_fellows).select2_4({language:'ro',theme:'bootstrap',placeholder:'Nationalitate', data: select2_countries_selections, width: '100%'});
    }
    
    $('.passenger-lastname', $service_flight_form_fellows).each(function(i){
      this.value = $('#client_lastname').val() + ' ' + i;
    });
    $('.passenger-firstname', $service_flight_form_fellows).each(function(i){
      this.value = $('#client_firstname').val() + ' ' + i;
    });
    $('.passenger-email', $service_flight_form_fellows).each(function(i){
      this.value = $('#client_email').val();
    });
    var client_phone = $('#client_phone').val();
    var client_phone_prefix_country = $('#client_phone_prefix').val();
    if(client_phone_prefix_country && typeof(countries_selections[client_phone_prefix_country]) !== 'undefined' && countries_selections[client_phone_prefix_country].prefix){
      client_phone = '+' + countries_selections[client_phone_prefix_country].prefix + ' ' + client_phone;
    }
    $('.passenger-phone', $service_flight_form_fellows).each(function(i){
      this.value = client_phone;
    });
    console.log('loadPassengerDetails', secured);
  }
  $service_flight_citybreak.on('change', function(){
    var is_checked = !$(this).is(':checked');
    $('#service_flight_search_origin').prop('readonly', is_checked);
    $('#service_flight_search_destination').prop('readonly', is_checked);
    $('#service_flight_search_departure_date').prop('readonly', is_checked);
    $('#service_flight_search_return').prop('readonly', is_checked).attr('onclick', is_checked ? 'return false;' : null);
    $('#service_flight_search_return_date').prop('readonly', is_checked);
    $('#service_flight_search_cabine_type').prop('readonly', is_checked).attr({'readonly' : is_checked ? '' : null});
    $('#service_flight_search_direct_only').prop('readonly', is_checked).attr('onclick', is_checked ? 'return false;' : null);
    $('#service_flight_search_flex_dates').prop('readonly', is_checked).attr('onclick', is_checked ? 'return false;' : null);
    $('#service_flight_search_passengers_adult').prop('readonly', is_checked);
    $('#service_flight_search_passengers_senior').prop('readonly', is_checked);
    $('#service_flight_search_passengers_child').prop('readonly', is_checked);
    $('#service_flight_search_passengers_infant_toggle').prop('readonly', is_checked).attr('onclick', is_checked ? 'return false;' : null);
    var infant_toggle_checked = $('#service_flight_search_passengers_infant_toggle').is(':checked');
    $('#service_flight_search_passengers_infant_lap').prop('readonly', is_checked || (!is_checked && !infant_toggle_checked));
    $('#service_flight_search_passengers_infant_seat').prop('readonly', is_checked || (!is_checked && !infant_toggle_checked));
    $('#service_flight_search_passengers_infant').prop('readonly', is_checked || (!is_checked && infant_toggle_checked)).attr('onclick', is_checked ? 'return false;' : null);
    $('#service_flight_search_passenger_birthdates_form').toggle(!is_checked);
    $(this).closest('.form-group').toggle(is_checked);
    $service_flight_form_fellows_wrapper.toggle(!is_checked);
  });
  $service_flight_results.on('submit','form.flight-result', function(){
    var citybreak_mode = !$service_flight_citybreak.is(':checked');
    if(!citybreak_mode){
      $service_flight_form_fellows_wrapper.hide();
      $service_flight_chosen_details.empty();
      var $search_container_header = $('a[href="#service_flight_form_container"]:not(.collapsed)');
      if($search_container_header.length){
        $search_container_header[0].click();
      }
    }
    var flight_secured = 0;
    
    if($('input.flight-route-option-choice:checked[data-secured=1]', this).length){
      flight_secured = 1;
    }
    var flight_index = parseInt($(this).data('flight_index'));
    var flight = flight_results.flights[flight_index];
    var itinerary_code_choice = '';
    $('input.flight-route-option-choice:checked', this).each(function(){
      // $(this).attr('checked','');
      if(itinerary_code_choice){
        itinerary_code_choice += '|';
      }
      var matches = this.name.match(/option\[(\d+)\]/);
      var flight_type = matches[1];
      itinerary_code_choice += flight_type + this.value;
    });
    var itinerary_code = this.itinerary_code.value + ':' + flight.Combinations.indexOf(itinerary_code_choice);
    console.log('submit','form.flight-result', $(this).serializeArray());
    
    var $flight_chosen_details = $('<div />').addClass($(this).attr('class'));
    var $form_clone = $(this).clone();
    $('>.card-footer',$form_clone).remove();
    $('input.flight-route-option-choice:not(:checked)', $form_clone).closest('.flight-result-route').remove();
    $('.flight-route-option-choice-container',$form_clone).remove();
    
    $form_clone[0].itinerary_code.value = itinerary_code;
    $flight_chosen_details.append($form_clone.children());
    var $togglers = $('.btn-toggle-segments', $flight_chosen_details);
    $.each($togglers, function(){
      var $toggler = $(this);
      $toggler.attr({
        'data-target': $toggler.attr('data-target') + '_chosen',
        'aria-controls': $toggler.attr('aria-controls') + '_chosen',
        'id': $toggler.attr('id') + '_chosen',
      });
      var $card_header = $toggler.closest('.card-header');
      var $toggled = $card_header.next('.flight-stops-container');
      $toggled.attr({
        'id': $toggled.attr('id') + '_chosen',
        'aria-labelledby': $toggled.attr('aria-labelledby') + '_chosen',
      });
    });
	$service_flight_form_options = $('<div id="service_flight_form_options"></div>');
	$service_flight_form_options.appendTo( $flight_chosen_details);
	$flightOptionsButton = $('<button type="button" class="btn btn-success" data-toggle="modal" data-target="#flightOptionsModal">Incarca zbor actualizat cu optiuni</button>');
	$flightOptionsButton.appendTo( $flight_chosen_details);
    if(!citybreak_mode){
      $flight_chosen_details.appendTo($service_flight_chosen_details);
    } else {
      $service_citybreak_form_fellows = $('#service_citybreak_form_fellows');
      $service_citybreak_form_fellows.empty();
      $service_citybreak_chosen_flight = $('#service_citybreak_chosen_flight');
      $service_citybreak_chosen_flight.empty();
      $service_citybreak_chosen_flight.attr('data-secured', flight_secured ? 1 : 0);
      $flight_chosen_details.appendTo($service_citybreak_chosen_flight);
      $flight_chosen_details.trigger('updated-flight');
    }
    if(citybreak_mode){
      var $service_citybreak_tab = $('a[href="#service_citybreak_tab"]:not(.active)');
      if($service_citybreak_tab.length){
        $service_citybreak_tab[0].click();
      }
      return false;
    }
    $service_flight_form_fellows_wrapper.show();
    loadPassengerDetails(flight_secured);
    scrollToIfNecessary($service_flight_form_header,10);
    return false;
  });
  $('#service_flight_search_passenger_birthdates_table').on('input change', 'input.passenger-age', function(){
    var age = this.value;
    var new_val = null;
    
    var $birth_date = $('input.passenger-birth-date', $(this).closest('tr'));
    if(age !== ''){
      if(age<=150){
        var today = moment().startOf('day')
        var birth_date = $birth_date.val();
        if(!birth_date || birth_date === ''){
          birth_date = moment(today).add(-1 * age,'years');
          new_val = birth_date.format('DD.MM.Y');
        } else {
          var service_flight_search_departure_date = $('#service_flight_search_departure_date').val();
          if(service_flight_search_departure_date && service_flight_search_departure_date !== ''){
            var reference_moment = moment(service_flight_search_departure_date,'DD.MM.Y').startOf('day');
          } else {
            var reference_moment = today;
          }
          new_val = birth_date;
          var moment_birth_date = moment(birth_date,'DD.MM.Y').startOf('day');
          var age_in_years = parseInt(reference_moment.diff(moment_birth_date,'years'));
          if(age != age_in_years){
            var new_moment_birth = moment_birth_date.add(age_in_years-age, 'years');
            if(new_moment_birth.isValid()){
              new_val = new_moment_birth.format('DD.MM.Y');
            } else {
              console.log('new age not valid', new_moment_birth);
            }
          }
        }
      }
    }
    $birth_date.val(new_val);
    recalculatePassengerBirthdateClasses();
    return true;
  });
  if(flight_search_data.index_id.length>0){
    loadResults();
  }
  function serviceFlightFormFellowsFormCallback($form,resp,$err_container){
    console.log('serviceFlightFormFellowsFormCallback',resp);
    if(resp.status !== 'success'){
      return true;
    }
    loadOrderServices();
    return true;
  }
  $service_flight_form_fellows_form.on('submit',function(){
    if(!search_is_over){
      console.log('service_flight_form_fellows_form','submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,serviceFlightFormFellowsFormCallback);
  });
  $('#flightOptionsModal').on('show.bs.modal', function (event) {
    var $button = $(event.relatedTarget);
	$form = $button.closest('form');
	var code = $form[0].flight_code.value;
	var itinerary_code = $form[0].itinerary_code.value;
	var action = '<?php echo site_url('trip/flight/booking_backend'); ?>?code=' + encodeURIComponent(code) + '&itinerary_code=' + encodeURIComponent(itinerary_code);
	var $iframe = $('#flightOptionsIframe');
	if($iframe[0].src != action){
		$iframe[0].parentElement.classList.add('loading');
		$iframe[0].src = action;
	}
    console.warn('Should open modal', $button);
  });
  var applyBookingOptions_timer;
  window.applyBookingOptions = function(obj){
	  $('#service_flight_reserve_submit').prop('disabled', true);
	  clearTimeout(applyBookingOptions_timer);
	  if(obj.loading){
		  $('#service_flight_form_options').html('');
	  }
	  applyBookingOptions_timer = setTimeout(() => {
		  console.warn(obj);
		  $('#service_flight_form_options').html(Array.from(obj.form.elements).map(v => {
			  var v2 =   v.cloneNode(true);
				v2.removeAttribute('form')
				return v2;
			}));
		  if(obj.vue_upsell){
			$('#service_flight_form_fellows_form .flight-price').html(obj.vue_upsell.format_price(obj.vue_upsell.finalPrice));
		  }
		  $('#service_flight_reserve_submit').prop('disabled', false);
	  }, 1000)
  }
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  