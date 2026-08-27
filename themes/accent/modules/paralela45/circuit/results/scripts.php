<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
// $meal_types = $this->_ci->Paralela45_model->mealTypesRequest(true);
$availabilities = $this->_ci->Paralela45_model->availabilitiesRequest(true);
$room_types = $this->_ci->Paralela45_model->getRoomTypes();
$service_types = $this->_ci->Paralela45_model->serviceTypesRequest(true);
?>
<script type="text/javascript">
;(function($){
  // var paralela45_meal_types = <?php // echo json_encode((object)$meal_types); ?>;
  var paralela45_room_types = <?php echo json_encode((object)$room_types); ?>;
  var paralela45_service_types = <?php echo json_encode((object)$service_types); ?>;
  var paralela45_availabilities = <?php echo json_encode((object)$availabilities); ?>;
  var setSearchStatus = window.setParalela45CircuitSearchStatus;
  var $price_slider = $("#slider-range");
  var notification_title;
  var show_warnings = true;
  var paralela45_circuit_results;
  var all_cities_names = {};
  function interpretNoPackagesResponse(result,initial){
    setSearchStatus(true);
    if(initial && result && result.data && result.data.packages_expired){
      show_warnings = false;
    }
    if(show_warnings){
      $('#packageWarnings').show();
    }
    show_warnings = true;
    $('#packagesResultsWrapper').hide();
  }
  function loadResults(initial){
    setSearchStatus(false);
    $('#packageWarnings').hide();
    $.ajax({
      url: '<?php echo site_url('paralela45/circuit/loadResults'); ?>',
      method: 'post',
      dataType: 'json',
      data: paralela45_circuit_search_data,
      async: true,
      success: function(result,status,xhr){
        if(!result.status || result.status !== 'success'){
          interpretNoPackagesResponse(result,initial);
          return;
        }
        paralela45_circuit_search_data = result.data;
        if(typeof paralela45_circuit_search_data.filters === 'undefined'){
          paralela45_circuit_search_data.filters = {};
        }
        paralela45_circuit_search_data.filters.cheapest = false;
        if(typeof paralela45_circuit_search_data.filters.service_types === 'undefined'){
          paralela45_circuit_search_data.filters.service_types = [];
        }
        if(typeof paralela45_circuit_search_data.filters.room_types === 'undefined'){
          paralela45_circuit_search_data.filters.room_types = [];
        }
        // if(typeof paralela45_circuit_search_data.filters.meal_types === 'undefined'){
          // paralela45_circuit_search_data.filters.meal_types = [];
        // }
        if(typeof paralela45_circuit_search_data.filters.dates === 'undefined'){
          paralela45_circuit_search_data.filters.dates = [];
        }
        if(typeof paralela45_circuit_search_data.filters.availabilities === 'undefined'){
          paralela45_circuit_search_data.filters.availabilities = [];
        }
        if(typeof paralela45_circuit_search_data.filters.cities === 'undefined'){
          paralela45_circuit_search_data.filters.cities = [];
        }
        if(typeof paralela45_circuit_search_data.filters.origin_cities === 'undefined'){
          paralela45_circuit_search_data.filters.origin_cities = [];
        }
        if(typeof paralela45_circuit_search_data.filters.name === 'undefined'){
          paralela45_circuit_search_data.filters.name = "";
        }
        if(typeof paralela45_circuit_search_data.filters.period === 'undefined'){
          paralela45_circuit_search_data.filters.period = [];
        }
        paralela45_circuit_results = result.results;
        loadFilters(false);
        paralela45_circuit_search_data.filters.cheapest = true;
        if(paralela45_circuit_search_data.destination){
          paralela45_circuit_search_data.filters.cities.push(paralela45_circuit_search_data.destination);
        }
        if(paralela45_circuit_search_data.origin){
          paralela45_circuit_search_data.filters.origin_cities.push(paralela45_circuit_search_data.origin);
        }
        if(paralela45_circuit_search_data.start_date){
          paralela45_circuit_search_data.filters.dates.push(paralela45_circuit_search_data.start_date);
        }
        if(paralela45_circuit_search_data.nights){
          paralela45_circuit_search_data.filters.period.push(parseInt(paralela45_circuit_search_data.nights));
        }
        filterResults();
        setSearchStatus(true);
      },
      error: function(jqXHR,textStatus,error){
        console.log(jqXHR,textStatus,error);
        setSearchStatus(true);
      }
    }).done(function(){
      /* if(google_map_location_markers){
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
      } */
    });
  }
  var results_page = 1;
  var results_per_page = 10;
  var items_sort = {
    order: 'Price',
    direction: 'ASC'
  };
  function SortPrices(a, b){
    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
  }
  function SortResults(a, b){
    if(items_sort.order == 'Price'){
      var aName = parseFloat(a.Price);
      var bName = parseFloat(b.Price); 
    }
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0)) * (items_sort.direction == 'ASC' ? 1 : -1);
  }
  function interpretResults(){
    if(typeof paralela45_circuit_results.filtered_offers === 'undefined'){
      paralela45_circuit_results.filtered_offers = paralela45_circuit_results.offers;
    }
     console.log(paralela45_circuit_results);
    var $navigation = $('ul.pagination');
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    var total_pages = Math.ceil(paralela45_circuit_results.filtered_offers.length / results_per_page);
    if(total_pages){
      $navigation.twbsPagination({
        startPage: results_page,
        totalPages: total_pages,
        visiblePages: 20,
        first: "<<",
        prev: "<",
        next: ">",
        last: ">>",
        onPageClick: function (evt, page) {
          if(page == results_page){
            return;
          }
          results_page = page;
          interpretResults();
        }
      });
    }
    $('#packageResults').empty();
    var res = paralela45_circuit_results.filtered_offers.length;
    $('.rezCount > .filterTitle').text('Am gasit ' + (res == 1 ? '1 oferta' : res + ' oferte'));
    $('.rezCount .selected_date_start').text();
    $('.rezCount .selected_rooms').text(paralela45_circuit_search_data.occupancy.length + ' camera');
    var travellers = 0;
    var adults = 0;
    var children = 0;
    for (var i=0; i<paralela45_circuit_search_data.occupancy.length; i++){
      var occupants = paralela45_circuit_search_data.occupancy[i];
      adults += 1 * occupants.adt;
      if(occupants.chd && occupants.chd.length){
        children += 1 * occupants.chd.length;
      }
    }
    travellers = adults + children;
    $('.rezCount .selected_passengers').text(travellers + ' calatori');
    var $package_box_model = $('#packageResultModel').clone().removeAttr('id style');
    
    notification_title = paralela45_circuit_search_data.occupancy.length + ' ' + (paralela45_circuit_search_data.occupancy.length > 1 ? 'camere' : 'camera');
    if(paralela45_circuit_search_data.start_date){
      var start_date = moment(paralela45_circuit_search_data.start_date,'Y-MM-DD');
      notification_title += ', ' + start_date.locale('ro').format("dddd, DD MMMM Y");
    }
    notification_title += ', ' + travellers + ' ' + (travellers > 1 ? 'persoane' : 'persoana');
    
    var offset = (results_page-1) * results_per_page;
    paralela45_circuit_results.filtered_offers.sort(SortResults);
    for (var i=offset; i<paralela45_circuit_results.filtered_offers.length; i++){
      if(i == offset + results_per_page){
        break;
      }
      var offer = paralela45_circuit_results.filtered_offers[i];
      var product = paralela45_circuit_results.products[offer.CircuitId];
      
      var $package_box = $package_box_model.clone();
      // $('.hartaPackage', $package_box).attr('data-lat', product.Lat);
      // $('.hartaPackage', $package_box).attr('data-lng', product.Lng);
      // $('.hartaPackage', $package_box).attr('data-city', paralela45_circuit_routes.Cities[paralela45_circuit_search_data.origin].CityName);
      // $('.hartaPackage', $package_box).attr('data-address', '');
      $('.hartaPackage', $package_box).attr('data-name', product.Name);
      $('.hotel-image', $package_box).attr('href', product.Link)
        .css('background-image',  'url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>)')
        .addClass('lazy')
        .attr('data-src', product.Image);
      $('.package-name', $package_box).text(product.Name);
      /* var stars = parseInt(product.Stars);
      if(stars){
        $('.package-stars', $package_box).html(" " + Array(parseInt(stars) + 1).join('<i class="fa fa-star"></i>'));
      } */
      // $('.package-category', $package_box).text(paralela45_circuit_routes.Cities[product.CityCode].CityName);
      // $('.package-project', $package_box).text();
      // $('.package-description', $package_box).text(package.Description);
      
      if(product.Destinations && product.Destinations.length){
        var city_names = [];
        for (var j=0; j<product.Destinations.length; j++){
          if(!product.Destinations[j].CityName){
            continue;
          }
          city_names.push(product.Destinations[j].CityName);
        }
        $('.package-destinations', $package_box).html(city_names.join(', '));
      }
      $('.package-origin', $package_box).html(offer.InfoCharter.DepArrLoc + ' ' + moment(offer.InfoCharter.DepArrDate,'Y-MM-DD HH:mm:ss').locale('ro').format("DD/MM/Y (dddd, D MMMM)") + '');
      $('.package-destination', $package_box).html(offer.InfoCharter.RetArrLoc + ' ' + moment(offer.InfoCharter.RetArrDate,'Y-MM-DD HH:mm:ss').locale('ro').format("DD/MM/Y (dddd, D MMMM)") + '');
      $('.package-days', $package_box).html(product.Period);
      for(var j=0; j<offer.Services.length; j++){
        var service = offer.Services[j];
        var $li = $('<li />');
        // $li.append('<strong title="' + service.Code + '">' + service.Type + '</strong> ');
        $li.append($('<em>' + service.Name + '</em>'));
        if(service.Availability !== 'IM'){
          $li.append(' <span>(' + paralela45_availabilities[service.Availability] + ')</span> ');
        }
        // $li.append(' <strong title="' + service.CheckIn + ' - ' + service.CheckOut  + '">' + format_price(service.Price,offer.Currency) + '</strong>');
        $('.package-services', $package_box).append($li);
      }
      /* for(var j=0; j<offer.Meals.length; j++){
        var meal = offer.Meals[j];
        var $li = $('<li />');
        // $li.append('<strong title="' + meal.Code + '">' + meal.Type + '</strong> ');
        $li.append($('<em>' + meal.Name + '</em>'));
        // $li.append(' <strong title="' + meal.CheckIn + ' - ' + meal.CheckOut  + '">' + format_price(meal.Price,offer.Currency) + '</strong>');
        $('.package-meals', $package_box).append($li);
      } */
      for(var j=0; j<offer.Rooms.length; j++){
        var room = offer.Rooms[j];
        var $li = $('<li />');
        // $li.append('<strong title="' + room.Code + '">' + room.GCode + '</strong> ');
        if(room.ExtraBed){
          $li.append('<span>Pat extra</span> ');
        }
        // $li.append($('<em>' + room.Quantity + '</em> x '));
        $li.append($('<em>' + room.Name + '</em>'));
        // $li.append(' <strong>' + format_price(room.Price,offer.Currency) + '</strong>');
        $('.package-rooms', $package_box).append($li);
      }
      $('.package-availability', $package_box).html(paralela45_availabilities[offer.Availability]);
      $('.package-name', $package_box).attr('href', product.Link);
      $('.reserve-button', $package_box).attr('href', product.Link);
      if(offer.block_payments){
        $('.reserve-button', $package_box).addClass('disabled').attr('title','Nu se mai pot efectua rezervari pentru aceasta oferta.');
      } else {
        $('.reserve-button', $package_box).attr({
          'data-booking-link': offer.Link,
          'data-package-variant-id': offer.DepartureCharter,
          'data-package-id': offer.CircuitId,
          'data-package-search-id': product.SearchId
        });
      }
      $('.notification-button', $package_box).attr({
        'id': 'button_notification_package_' + offer.PackageVariantId,
        'data-type': 'package',
        'data-ref_id': offer.PackageVariantId,
        'data-package_name': product.Name,
        'data-category': product.Class,
        // 'data-project_name': package.ProjectName,
        'data-amount': offer.Price,
        'data-currency': offer.Currency,
        'data-link': offer.Link
      });
      if(offer.Price){
        $('.current-price', $package_box).text(format_price(Math.ceil(offer.Price), offer.Currency));
      } else {
        $('.current-price', $package_box).remove();
      }
      $('#packageResults').append($package_box);
    }
    $('.rezCount').show();
    $('.sortPackage').show();
    $('#packageResults .lazy').lazy();
    $('#packagesResultsWrapper').show();
  }
  function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
  }
  function Select2Matcher(term, text) {
    if(!term.length){
      return true;
    }
    var terms = term.split(" ");
    for (var i=0; i < terms.length; i++){
      var tester = new RegExp("\\b" + escapeRegExp(terms[i]), 'i');
      if (tester.test(text) == false){
        return false;
      }
    }
    return true;
  };
  function filterResults(){
    // console.log(paralela45_circuit_search_data.filters);
    results_page = 1;
    var filter_min_price = -1;
    var filter_max_price = -1;
    if(primary_filters.prices.length && typeof paralela45_circuit_search_data.filters.min_price !== 'undefined'){
      filter_min_price = typeof primary_filters.prices[paralela45_circuit_search_data.filters.min_price] !== undefined ? primary_filters.prices[paralela45_circuit_search_data.filters.min_price] : -1;
      filter_max_price = typeof primary_filters.prices[paralela45_circuit_search_data.filters.max_price] !== undefined ? primary_filters.prices[paralela45_circuit_search_data.filters.max_price] : -1;
    }
    paralela45_circuit_results.filtered_offers = [];
    
    var offer_intersection = [];
    var product_codes_with_first_offer = [];
    for (var i=0; i<paralela45_circuit_results.offers.length; i++){
      var offer = paralela45_circuit_results.offers[i];
      var product = paralela45_circuit_results.products[offer.CircuitId];
      if(paralela45_circuit_search_data.filters.cheapest){
        if(product_codes_with_first_offer.indexOf(offer.CircuitId) >= 0){
          continue;
        }
        product_codes_with_first_offer.push(offer.CircuitId);
      }
      if(filter_min_price > 0){
        if(offer.Price < filter_min_price){
          continue;
        }
        if(offer.Price > filter_max_price){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.name.length){
        var name = $.trim(paralela45_circuit_search_data.filters.name).replace(/\s\s+/g, ' ');
        if(!Select2Matcher(name,product.Name)){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.period.length){
        if(paralela45_circuit_search_data.filters.period.indexOf(parseInt(product.Period)) < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.availabilities.length){
        if(paralela45_circuit_search_data.filters.availabilities.indexOf(offer.Availability) < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.dates.length){
        if(paralela45_circuit_search_data.filters.dates.indexOf(offer.InfoCharter.DepArrDate.substring(0,7)) < 0){
          continue;
        }
      }
      var check_type = 1;
      
      if(paralela45_circuit_search_data.filters.cities.length){
        if(product.Destinations && product.Destinations.length){
          var found_city = false;
          for (var j=0; j<product.Destinations.length; j++){
            if(!product.Destinations[j].CityCode){
              continue;
            }
            if(paralela45_circuit_search_data.filters.cities.indexOf(product.Destinations[j].CityCode) > -1){
              found_city = true;
              break;
            }
          }
          if(!found_city){
            continue;
          }
        } else {
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.origin_cities.length){
        if(paralela45_circuit_search_data.filters.origin_cities.indexOf(offer.InfoCharter.DepArrCodLoc) < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.room_types.length){
        if(offer.Rooms && offer.Rooms.length){
          var keep = 0;
          for (var j=0; j<offer.Rooms.length; j++){
            var room = offer.Rooms[j];
            var room_type = room.GCode;
            if(paralela45_circuit_search_data.filters.room_types.indexOf(room_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_circuit_search_data.filters.room_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_circuit_search_data.filters.room_types.length))){
            continue;
          }
        } else if(paralela45_circuit_search_data.filters.room_types.indexOf('') < 0){
          continue;
        }
      }
      /* if(paralela45_circuit_search_data.filters.meal_types.length){
        if(offer.Meals && offer.Meals.length){
          var keep = 0;
          for (var j=0; j<offer.Meals.length; j++){
            var meal = offer.Meals[j];
            var meal_type = '' + meal.Type;
            if(paralela45_circuit_search_data.filters.meal_types.indexOf(meal_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_circuit_search_data.filters.service_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_circuit_search_data.filters.service_types.length))){
            continue;
          }
        } else if(paralela45_circuit_search_data.filters.meal_types.indexOf('') < 0){
          continue;
        }
      } */
      if(paralela45_circuit_search_data.filters.service_types.length){
        if(offer.Services && offer.Services.length){
          var keep = 0;
          for (var j=0; j<offer.Services.length; j++){
            var service = offer.Services[j];
            var service_type = '' + service.Type;
            if(paralela45_circuit_search_data.filters.service_types.indexOf(service_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_circuit_search_data.filters.service_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_circuit_search_data.filters.service_types.length))){
            continue;
          }
        } else if(paralela45_circuit_search_data.filters.service_types.indexOf('') < 0){
          continue;
        }
      }
      paralela45_circuit_results.filtered_offers.push(offer);
    }
    loadFilters(true);
    interpretResults();
  }
  var primary_filters;
  var secondary_filters;
  function loadFilters(secondary){
    if(typeof secondary === 'undefined'){
      secondary = false;
    }
    var product_codes_with_first_offer = [];
    var filters = {
      cheapest: 0,
      cities: {},
      origin_cities: {},
      availabilities: {},
      dates: {},
      room_types: {},
      service_types: {},
      period: {},
      prices: [],
      currency: '',
    };
    if(secondary){
      var offers = paralela45_circuit_results.filtered_offers;
      secondary_filters = filters;
    } else {
      var offers = paralela45_circuit_results.offers;
      primary_filters = filters;
    }
    // var max_stars = 0;
    // var hide = {
      // 'availabilities' : true,
      // 'meal_types' : true,
      // 'room_types' : true,
      // 'service_types' : true,
    // };
    for (var i=0; i<offers.length; i++){
      var offer = offers[i];
      if(product_codes_with_first_offer.indexOf(offer.CircuitId) < 0){
        product_codes_with_first_offer.push(offer.CircuitId);
        filters.cheapest++;
      }
      var product = paralela45_circuit_results.products[offer.CircuitId];
      if(filters.currency === ''){
        filters.currency = offer.Currency;
      }
      if(filters.prices.indexOf(offer.Price) < 0){
        filters.prices.push(offer.Price);
      }
      if(typeof filters.period[parseInt(product.Period)] === 'undefined'){
        filters.period[parseInt(product.Period)] = 0;
      }
      filters.period[parseInt(product.Period)]++;
      if(product.Destinations && product.Destinations.length){
        for (var j=0; j<product.Destinations.length; j++){
          if(!product.Destinations[j].CityCode){
            continue;
          }
          if(typeof filters.cities[product.Destinations[j].CityCode] === 'undefined'){
            filters.cities[product.Destinations[j].CityCode] = 0;
          }
          if(typeof all_cities_names[product.Destinations[j].CityCode] === 'undefined'){
            all_cities_names[product.Destinations[j].CityCode] = product.Destinations[j].CityName;
          }
          filters.cities[product.Destinations[j].CityCode] ++;
        }
      }
      if(typeof filters.origin_cities[offer.InfoCharter.DepArrCodLoc] === 'undefined'){
        filters.origin_cities[offer.InfoCharter.DepArrCodLoc] = 0;
      }
      if(typeof all_cities_names[offer.InfoCharter.DepArrCodLoc] === 'undefined'){
        all_cities_names[offer.InfoCharter.DepArrCodLoc] = offer.InfoCharter.DepArrLoc;
      }
      filters.origin_cities[offer.InfoCharter.DepArrCodLoc] ++;
      if(typeof filters.dates[offer.InfoCharter.DepArrDate.substring(0,7)] === 'undefined'){
        filters.dates[offer.InfoCharter.DepArrDate.substring(0,7)] = 0;
      }
      filters.dates[offer.InfoCharter.DepArrDate.substring(0,7)]++;
      if(typeof filters.availabilities[offer.Availability] === 'undefined'){
        filters.availabilities[offer.Availability] = 0;
      }
      filters.availabilities[offer.Availability]++;
      /* if(offer.Meals && offer.Meals.length){
        var meal_types = [];
        for (var j=0; j<offer.Meals.length; j++){
          var meal = offer.Meals[j];
          var meal_type = parseInt(meal.Type);
          if(typeof filters.meal_types[meal_type] === 'undefined'){
            filters.meal_types[meal_type] = 0;
          }
          if(meal_types.indexOf(meal_type) < 0){
            meal_types.push(meal_type);
            filters.meal_types[meal_type]++;
          }
        }
      } else {
        if(typeof filters.meal_types[0] === 'undefined'){
          filters.meal_types[0] = 0;
        }
        filters.meal_types[0]++;
      } */
      if(offer.Rooms && offer.Rooms.length){
        var room_types = [];
        for (var j=0; j<offer.Rooms.length; j++){
          var room = offer.Rooms[j];
          var room_type = room.GCode;
          if(typeof filters.room_types[room_type] === 'undefined'){
            filters.room_types[room_type] = 0;
          }
          if(room_types.indexOf(room_type) < 0){
            room_types.push(room_type);
            filters.room_types[room_type]++;
          }
        }
      } else {
        if(typeof filters.room_types[''] === 'undefined'){
          filters.room_types[''] = 0;
        }
        filters.room_types['']++;
      }
      if(offer.Services && offer.Services.length){
        var service_types = [];
        for (var j=0; j<offer.Services.length; j++){
          var service = offer.Services[j];
          var service_type = '' + service.Type;
          if(typeof filters.service_types[service_type] === 'undefined'){
            filters.service_types[service_type] = 0;
          }
          if(service_types.indexOf(service_type) < 0){
            service_types.push(service_type);
            filters.service_types[service_type]++;
          }
        }
      } else {
        if(typeof filters.service_types[''] === 'undefined'){
          filters.service_types[''] = 0;
        }
        filters.service_types['']++;
      }
    }
    if(typeof paralela45_circuit_search_data.filters === 'undefined'){
      paralela45_circuit_search_data.filters = {};
    }
    $('.hotel-period-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.period)){
      $('.hotel-period-filter').hide();
    } else {
      var any_filter_added = false;
      for(var per in primary_filters.period){
        per = parseInt(per);
        var number_of_filtered_offers = typeof filters.period[per] !== 'undefined' ? filters.period[per] : 0;
        var all_have_it = primary_filters.period[per] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.period.indexOf(per)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="period" id="hotel_period_' + per + '" value="' + per + '"/>').prop('checked', is_checked).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_period_' + per + '" />').appendTo($checkWrapper);
        $checkLabel.append(per + ' zile');
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.period[per]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.period[per] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.period[per] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-period-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-period-filter').show();
      }
    }
    
    // BEGIN - cheapest
    $('.hotel-cheapest-filter > .hotel-filters-content').empty();
    var number_of_filtered_offers = filters.cheapest;
    var all_have_it = primary_filters.cheapest == paralela45_circuit_results.offers.length;
    var all_filtered_have_it = number_of_filtered_offers == offers.length;
    var is_checked = paralela45_circuit_search_data.filters.cheapest ? true : false;
    var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
    var $checkWrapper = $('<div class="checkWrapper" />');
    $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="cheapest" id="hotel_cheapest" value="1"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
    var $checkLabel = $('<label for="hotel_cheapest" />').appendTo($checkWrapper);
    $checkLabel.append('Cele mai avantajoase oferte');
    if(!secondary){
      $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
    } else {
      if(number_of_filtered_offers != primary_filters.cheapest){
        $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.cheapest + '</small></span>');
      } else {
        $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.cheapest + (is_checked ? '</strong>' : '</small>') + '</span>');
      }
    }
    any_filter_added = true;
    $('.hotel-cheapest-filter > .hotel-filters-content').append($checkWrapper);
    $('.hotel-cheapest-filter').show();
    // END - cheapest
    
    $('.hotel-cities-filter > .hotel-filters-content').empty();
    if($.isEmptyObject(primary_filters.cities)){
      $('.hotel-cities-filter').hide();
    } else {
      var any_filter_added = false;
      for(var city_code in primary_filters.cities){
        var number_of_filtered_offers = typeof filters.cities[city_code] !== 'undefined' ? filters.cities[city_code] : 0;
        var all_have_it = primary_filters.cities[city_code] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.cities.indexOf(city_code)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="cities" id="hotel_city_code_' + city_code + '" value="' + city_code + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_city_code_' + city_code + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof all_cities_names[city_code] !== 'undefined' ? all_cities_names[city_code] : '');
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.cities[city_code]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.cities[city_code] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.cities[city_code] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-cities-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-cities-filter').show();
      }
    }
    $('.hotel-origin_cities-filter > .hotel-filters-content').empty();
    if($.isEmptyObject(primary_filters.origin_cities)){
      $('.hotel-origin_cities-filter').hide();
    } else {
      var any_filter_added = false;
      for(var city_code in primary_filters.origin_cities){
        var number_of_filtered_offers = typeof filters.origin_cities[city_code] !== 'undefined' ? filters.origin_cities[city_code] : 0;
        var all_have_it = primary_filters.origin_cities[city_code] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.origin_cities.indexOf(city_code)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="origin_cities" id="hotel_origin_city_code_' + city_code + '" value="' + city_code + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_origin_city_code_' + city_code + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof all_cities_names[city_code] !== 'undefined' ? all_cities_names[city_code] : '');
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.origin_cities[city_code]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.origin_cities[city_code] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.origin_cities[city_code] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-origin_cities-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-origin_cities-filter').show();
      }
    }
    $('.hotel-dates-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.dates)){
      $('.hotel-dates-filter').hide();
    } else {
      var any_filter_added = false;
      for(var date in primary_filters.dates){
        var number_of_filtered_offers = filters.dates[date];
        var number_of_filtered_offers = typeof filters.dates[date] !== 'undefined' ? filters.dates[date] : 0;
        var all_have_it = primary_filters.dates[date] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.dates.indexOf(date)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="dates" id="hotel_date_' + date + '" value="' + date + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_date_' + date + '" />').appendTo($checkWrapper);
        $checkLabel.append(moment(date,'Y-MM').locale('ro').format('MMMM Y'));
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.dates[date]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.dates[date] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.dates[date] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-dates-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-dates-filter').show();
      }
    }
    $('.hotel-availabilities-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.availabilities)){
      $('.hotel-availabilities-filter').hide();
    } else {
      var any_filter_added = false;
      for(var availability in primary_filters.availabilities){
        var number_of_filtered_offers = filters.availabilities[availability];
        var number_of_filtered_offers = typeof filters.availabilities[availability] !== 'undefined' ? filters.availabilities[availability] : 0;
        var all_have_it = primary_filters.availabilities[availability] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.availabilities.indexOf(availability)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="availabilities" id="hotel_availability_' + availability + '" value="' + availability + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_availability_' + availability + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_availabilities[availability] !== 'undefined' ? paralela45_availabilities[availability] : availability);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.availabilities[availability]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.availabilities[availability] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.availabilities[availability] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-availabilities-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-availabilities-filter').show();
      }
    }
    // $('.hotel-meal_types-filter > .hotel-filters-content').empty();
    
    /* if($.isEmptyObject(primary_filters.meal_types)){
      $('.hotel-meal_types-filter').hide();
    } else {
      var any_filter_added = false;
      for(var meal_type in primary_filters.meal_types){
        var number_of_filtered_offers = filters.meal_types[meal_type];
        var number_of_filtered_offers = typeof filters.meal_types[meal_type] !== 'undefined' ? filters.meal_types[meal_type] : 0;
        var all_have_it = primary_filters.meal_types[meal_type] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.meal_types.indexOf(meal_type)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="meal_types" id="hotel_meal_type_' + meal_type + '" value="' + meal_type + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_meal_type_' + meal_type + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_meal_types[meal_type] !== 'undefined' ? paralela45_meal_types[meal_type] : meal_type);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.meal_types[meal_type]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked && !all_have_it ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked && !all_have_it ? '</strong>' : '</small>') + '/<small>' + primary_filters.meal_types[meal_type] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked && !all_have_it ? '<strong>' : '<small>') + primary_filters.meal_types[meal_type] + (is_checked && !all_have_it ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-meal_types-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-meal_types-filter').show();
      }
    } */
    $('.hotel-room_types-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.room_types)){
      $('.hotel-room_types-filter').hide();
    } else {
      var any_filter_added = false;
      for(var room_type in primary_filters.room_types){
        var number_of_filtered_offers = filters.room_types[room_type];
        var number_of_filtered_offers = typeof filters.room_types[room_type] !== 'undefined' ? filters.room_types[room_type] : 0;
        var all_have_it = primary_filters.room_types[room_type] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.room_types.indexOf(room_type)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="room_types" id="hotel_room_type_' + room_type + '" value="' + room_type + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_room_type_' + room_type + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_room_types[room_type] !== 'undefined' ? paralela45_room_types[room_type] : room_type);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.room_types[room_type]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked && !all_have_it ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked && !all_have_it ? '</strong>' : '</small>') + '/<small>' + primary_filters.room_types[room_type] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked && !all_have_it ? '<strong>' : '<small>') + primary_filters.room_types[room_type] + (is_checked && !all_have_it ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-room_types-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-room_types-filter').show();
      }
    }
    $('.hotel-service_types-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.service_types)){
      $('.hotel-service_types-filter').hide();
    } else {
      var any_filter_added = false;
      for(var service_type in primary_filters.service_types){
        var number_of_filtered_offers = filters.service_types[service_type];
        var number_of_filtered_offers = typeof filters.service_types[service_type] !== 'undefined' ? filters.service_types[service_type] : 0;
        var all_have_it = primary_filters.service_types[service_type] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.service_types.indexOf(service_type)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="service_types" id="hotel_service_type_' + service_type + '" value="' + service_type + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_service_type_' + service_type + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_service_types[service_type] !== 'undefined' ? paralela45_service_types[service_type] : service_type);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.service_types[service_type]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked && !all_have_it ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked && !all_have_it ? '</strong>' : '</small>') + '/<small>' + primary_filters.service_types[service_type] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked && !all_have_it ? '<strong>' : '<small>') + primary_filters.service_types[service_type] + (is_checked && !all_have_it ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-service_types-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-service_types-filter').show();
      }
    }
    if(!secondary){
      filters.prices.sort(SortPrices);
      $price_slider.slider({
        range: true,
        min: 0,
        max: filters.prices.length-1,
        values: [0, filters.prices.length-1],
        slide: function (event, ui) {
          $(this).trigger('updatePrice',ui);
        },
        stop: function (event, ui) {
          setFilters();
          filterResults();
        }
      }).on('updatePrice', function(e, ui){
        if(ui){
          var slider_values = ui.values;
        } else {
          var slider_values = $price_slider.slider('values');
        }
        $("#amount").val(format_price(Math.ceil(filters.prices[slider_values[ 0 ]]), filters.currency) + " - " + format_price(Math.ceil(filters.prices[slider_values[ 1 ]]), filters.currency));
      });
      $price_slider.trigger('updatePrice');
    }
  }
  function setFilters(){
    if(typeof paralela45_circuit_search_data.filters === 'undefined'){
      paralela45_circuit_search_data.filters = {};
    }
    paralela45_circuit_search_data.filters.name = $.trim($('#package_filter_by_name').val());
    paralela45_circuit_search_data.filters.period = [];
    $('.hotel-period-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.period.push(parseInt(this.value));
    });
    paralela45_circuit_search_data.filters.availabilities = [];
    $('.hotel-availabilities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.availabilities.push(this.value);
    });
    paralela45_circuit_search_data.filters.dates = [];
    $('.hotel-dates-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.dates.push(this.value);
    });
    paralela45_circuit_search_data.filters.cities = [];
    $('.hotel-cities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.cities.push(this.value);
    });
    paralela45_circuit_search_data.filters.origin_cities = [];
    $('.hotel-origin_cities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.origin_cities.push(this.value);
    });
    paralela45_circuit_search_data.filters.cheapest = false;
    $('.hotel-cheapest-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.cheapest = true;
    });
    paralela45_circuit_search_data.filters.service_types = [];
    $('.hotel-service_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.service_types.push(this.value);
    });
    paralela45_circuit_search_data.filters.room_types = [];
    $('.hotel-room_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.room_types.push(this.value);
    });
    // paralela45_circuit_search_data.filters.meal_types = [];
    // $('.hotel-meal_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      // paralela45_circuit_search_data.filters.meal_types.push(this.value);
    // });
    var price_values = $price_slider.slider('values');
    paralela45_circuit_search_data.filters.min_price = parseFloat(price_values[0]);
    paralela45_circuit_search_data.filters.max_price = parseFloat(price_values[1]);
  }
  $('#package_filter_by_name').on('change',function(){
    results_page = 1;
    setFilters();
    filterResults();
  });
  $('.hotel-filters-content').on('change', 'input[type=checkbox]:not(.all_have_it)',function(){
    results_page = 1;
    setFilters();
    filterResults();
  });
  function resetFilters(){
    $('.hotel-filters-content input[type=checkbox]:checked:not(.all_have_it)').prop('checked',false);
    $('#package_filter_by_name').val(null);
    $price_slider.slider('values',[$price_slider.slider('option','min'),$price_slider.slider('option','max')]);
    $price_slider.trigger('updatePrice'); 
  }
  $('#packageResults').on('click','a.reserve-button', function(e){
    e.preventDefault();
    $('#offer-booking-form').attr('action', $(this).data('bookingLink'));
    $('#offer-booking-package_id').val($(this).data('packageId'));
    $('#offer-booking-occupancy').val(JSON.stringify(paralela45_circuit_search_data.occupancy));
    $('#offer-booking-package_variant_id').val($(this).data('packageVariantId'));
    $('#offer-booking-package_search_id').val($(this).data('packageSearchId'));
    $('#offer-booking-departure_city_code').val(paralela45_circuit_search_data.origin);
    $('#offer-booking-destination_city_code').val(paralela45_circuit_search_data.destination);
    $('#offer-booking-destination_country_code').val(paralela45_circuit_search_data.country);
    $('#offer-booking-start_date').val(paralela45_circuit_search_data.start_date);
    $('#offer-booking-nights').val(paralela45_circuit_search_data.nights);
    $('#offer-booking-hotel_name').val(paralela45_circuit_search_data.hotel_name);
    return $('#offer-booking-form').submit();
  });
  $('#resetFilters').click(function(){
    resetFilters();
    setFilters();
    filterResults();
    var body = $("html, body");
    var pagination_top = $('h1.filterTitle').first().offset().top;
    body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
    });
  });
  $(document).on("click",'.inchideH', function () {
    $(this).parents(".boxHotel").hide("slow");
  });
  loadResults(true);
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>