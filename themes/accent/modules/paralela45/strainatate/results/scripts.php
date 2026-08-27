<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$meal_types = $this->_ci->Paralela45_model->mealTypesRequest(true);
$availabilities = $this->_ci->Paralela45_model->availabilitiesRequest(true);
$room_types = $this->_ci->Paralela45_model->getRoomTypes();
$service_types = $this->_ci->Paralela45_model->serviceTypesRequest(true);
?>
<script type="text/javascript">
;(function($){
  var paralela45_meal_types = <?php echo json_encode((object)$meal_types); ?>;
  var paralela45_room_types = <?php echo json_encode((object)$room_types); ?>;
  var paralela45_service_types = <?php echo json_encode((object)$service_types); ?>;
  var paralela45_availabilities = <?php echo json_encode((object)$availabilities); ?>;
  var setSearchStatus = window.setParalela45StrainatateSearchStatus;
  var $price_slider = $("#slider-range");
  var notification_title;
  var show_warnings = true;
  var paralela45_strainatate_results;
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
      url: '<?php echo site_url('paralela45/strainatate/loadResults'); ?>',
      method: 'post',
      dataType: 'json',
      data: paralela45_strainatate_search_data,
      async: true,
      success: function(result,status,xhr){
        if(!result.status || result.status !== 'success'){
          interpretNoPackagesResponse(result,initial);
          return;
        }
        paralela45_strainatate_search_data = result.data;
        if(typeof paralela45_strainatate_search_data.filters === 'undefined'){
          paralela45_strainatate_search_data.filters = {};
        }
        paralela45_strainatate_search_data.filters.cheapest = false;
        if(typeof paralela45_strainatate_search_data.filters.service_types === 'undefined'){
          paralela45_strainatate_search_data.filters.service_types = [];
        }
        if(typeof paralela45_strainatate_search_data.filters.room_types === 'undefined'){
          paralela45_strainatate_search_data.filters.room_types = [];
        }
        if(typeof paralela45_strainatate_search_data.filters.meal_types === 'undefined'){
          paralela45_strainatate_search_data.filters.meal_types = [];
        }
        if(typeof paralela45_strainatate_search_data.filters.availabilities === 'undefined'){
          paralela45_strainatate_search_data.filters.availabilities = [];
        }
        if(typeof paralela45_strainatate_search_data.filters.cities === 'undefined'){
          paralela45_strainatate_search_data.filters.cities = [];
        }
        if(typeof paralela45_strainatate_search_data.filters.name === 'undefined'){
          paralela45_strainatate_search_data.filters.name = "";
        }
        if(typeof paralela45_strainatate_search_data.filters.stars === 'undefined'){
          paralela45_strainatate_search_data.filters.stars = [];
        }
        paralela45_strainatate_results = result.results;
        loadFilters(false);
        paralela45_strainatate_search_data.filters.cheapest = true;
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
    if(typeof paralela45_strainatate_results.filtered_offers === 'undefined'){
      paralela45_strainatate_results.filtered_offers = paralela45_strainatate_results.offers;
    }
     console.log(paralela45_strainatate_results);
    var $navigation = $('ul.pagination');
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    var total_pages = paralela45_strainatate_results.filtered_offers.length / results_per_page;
    if(total_pages && total_pages>=results_page){
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
    $('.rezCount > .filterTitle').text('Am gasit ' + paralela45_strainatate_results.filtered_offers.length + ' oferte');
    var start_date = moment(paralela45_strainatate_search_data.start_date,'Y-MM-DD');
    $('.rezCount .selected_date_start').text(start_date.locale('ro').format("dddd, DD MMMM Y"));
    $('.rezCount .selected_rooms').text(paralela45_strainatate_search_data.occupancy.length + ' camera');
    var travellers = 0;
    var adults = 0;
    var children = 0;
    for (var i=0; i<paralela45_strainatate_search_data.occupancy.length; i++){
      var occupants = paralela45_strainatate_search_data.occupancy[i];
      adults += 1 * occupants.adt;
      if(occupants.chd && occupants.chd.length){
        children += 1 * occupants.chd.length;
      }
    }
    travellers = adults + children;
    $('.rezCount .selected_passengers').text(travellers + ' calatori');
    var $package_box_model = $('#packageResultModel').clone().removeAttr('id style');
    
    notification_title = paralela45_strainatate_search_data.occupancy.length + ' ' + (paralela45_strainatate_search_data.occupancy.length > 1 ? 'camere' : 'camera');
    notification_title += ', ' + start_date.locale('ro').format("dddd, DD MMMM Y");
    notification_title += ', ' + travellers + ' ' + (travellers > 1 ? 'persoane' : 'persoana');
    
    var offset = (results_page-1) * results_per_page;
    paralela45_strainatate_results.filtered_offers.sort(SortResults);
    for (var i=offset; i<paralela45_strainatate_results.filtered_offers.length; i++){
      if(i == offset + results_per_page){
        break;
      }
      var offer = paralela45_strainatate_results.filtered_offers[i];
      var product = paralela45_strainatate_results.products[offer.ProductCode];
      if(!paralela45_strainatate_routes.Cities[product.CityCode]) continue;
      var $package_box = $package_box_model.clone();
      $('.hartaPackage', $package_box).attr('data-lat', product.Lat);
      $('.hartaPackage', $package_box).attr('data-lng', product.Lng);
      $('.hartaPackage', $package_box).attr('data-city', paralela45_strainatate_routes.Cities[paralela45_strainatate_search_data.origin].CityName);
      // $('.hartaPackage', $package_box).attr('data-address', '');
      $('.hartaPackage', $package_box).attr('data-name', product.Name);
      $('.hotel-image', $package_box).attr('href', product.Link)
        .css('background-image',  'url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>)')
        .addClass('lazy')
        .attr('data-src', product.Image);
      $('.package-name', $package_box).text(product.Name);
      var stars = parseInt(product.Stars);
      stars = stars > 0 ? stars : 0;
      if(stars){
        $('.package-stars', $package_box).html(" " + Array(parseInt(stars) + 1).join('<i class="fa fa-star"></i>'));
      }
      $('.package-category', $package_box).text(paralela45_strainatate_routes.Cities[product.CityCode].CityName);
      // $('.package-project', $package_box).text();
      // $('.package-description', $package_box).text(package.Description);
      $('.hotel-info-short', $package_box).text(offer.OfferDescription ? offer.OfferDescription.substring(0,150) : '');
      $('.hotel-info-rest', $package_box).text(offer.OfferDescription ? offer.OfferDescription.substring(150): '').hide();
      if($('.hotel-info-rest', $package_box).is(':empty')){
        $('.package-description > a', $package_box).hide();
      } else {
        $('.package-description > a', $package_box).on('click', function(e){
          e.preventDefault();
          $(this).hide();
          $(this).next().show();
        });
      }
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
      for(var j=0; j<offer.Meals.length; j++){
        var meal = offer.Meals[j];
        var $li = $('<li />');
        // $li.append('<strong title="' + meal.Code + '">' + meal.Type + '</strong> ');
        $li.append($('<em>' + meal.Name + '</em>'));
        // $li.append(' <strong title="' + meal.CheckIn + ' - ' + meal.CheckOut  + '">' + format_price(meal.Price,offer.Currency) + '</strong>');
        $('.package-meals', $package_box).append($li);
      }
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
			var offer_start_date = paralela45_strainatate_search_data.start_date;
			var offer_end_date = moment(paralela45_strainatate_search_data.start_date,'Y-MM-DD').add(paralela45_strainatate_search_data.nights, 'days').format('Y-MM-DD');
			if(offer_start_date != offer.CheckIn){
				$('.package-project', $package_box).html('<div class="alert alert-warning"><div class="alert-heading"><i class="fa fa-warning"><\/i> Interval cazare: </div><div class="alert-content"><b>' + moment(offer.CheckIn,'Y-MM-DD').locale('ro').format("dddd, DD MMMM Y") + '<\/b> - <b>' + moment(offer.CheckOut,'Y-MM-DD').locale('ro').format("dddd, DD MMMM Y") + '<\/b><\/div><\/div>');
			}
      if(offer.block_payments){
        $('.reserve-button', $package_box).addClass('disabled').attr('title','Nu se mai pot efectua rezervari pentru aceasta oferta.');
      } else {
        $('.reserve-button', $package_box).attr({
          'data-booking-link': offer.Link,
          'data-package-variant-id': offer.PackageVariantId,
          'data-package-id': offer.PackageId,
          'data-check-in': offer_start_date,
          'data-check-out': offer_end_date
        });
      }
      $('.notification-button', $package_box).attr({
        'id': 'button_notification_package_' + offer.PackageVariantId,
        'data-type': 'package',
        'data-ref_id': offer.PackageVariantId,
        'data-package_name': product.Name + (stars ? ' (' + stars + ' stele)' : ''),
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
    // console.log(paralela45_strainatate_search_data.filters);
    results_page = 1;
    var filter_min_price = -1;
    var filter_max_price = -1;
    if(primary_filters.prices.length && typeof paralela45_strainatate_search_data.filters.min_price !== 'undefined'){
      filter_min_price = typeof primary_filters.prices[paralela45_strainatate_search_data.filters.min_price] !== undefined ? primary_filters.prices[paralela45_strainatate_search_data.filters.min_price] : -1;
      filter_max_price = typeof primary_filters.prices[paralela45_strainatate_search_data.filters.max_price] !== undefined ? primary_filters.prices[paralela45_strainatate_search_data.filters.max_price] : -1;
    }
    paralela45_strainatate_results.filtered_offers = [];
    
    var offer_intersection = [];
    var product_codes_with_first_offer = [];
    for (var i=0; i<paralela45_strainatate_results.offers.length; i++){
      var offer = paralela45_strainatate_results.offers[i];
      var product = paralela45_strainatate_results.products[offer.ProductCode];
      if(paralela45_strainatate_search_data.filters.cheapest){
        if(product_codes_with_first_offer.indexOf(offer.ProductCode) >= 0){
          continue;
        }
        product_codes_with_first_offer.push(offer.ProductCode);
      }
      if(filter_min_price > 0){
        if(offer.Price < filter_min_price){
          continue;
        }
        if(offer.Price > filter_max_price){
          continue;
        }
      }
      if(paralela45_strainatate_search_data.filters.name.length){
        var name = $.trim(paralela45_strainatate_search_data.filters.name).replace(/\s\s+/g, ' ');
        if(!Select2Matcher(name,product.Name)){
          continue;
        }
      }
      if(paralela45_strainatate_search_data.filters.stars.length){
        if(paralela45_strainatate_search_data.filters.stars.indexOf(parseInt(product.Stars) > 0 ? parseInt(product.Stars) : 0) < 0){
          continue;
        }
      }
      if(paralela45_strainatate_search_data.filters.availabilities.length){
        if(paralela45_strainatate_search_data.filters.availabilities.indexOf(offer.Availability) < 0){
          continue;
        }
      }
      var check_type = 1;
      
      if(paralela45_strainatate_search_data.filters.cities.length){
        if(paralela45_strainatate_search_data.filters.cities.indexOf(product.CityCode) < 0){
          continue;
        }
      }
      if(paralela45_strainatate_search_data.filters.room_types.length){
        if(offer.Rooms && offer.Rooms.length){
          var keep = 0;
          for (var j=0; j<offer.Rooms.length; j++){
            var room = offer.Rooms[j];
            var room_type = room.GCode;
            if(paralela45_strainatate_search_data.filters.room_types.indexOf(room_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_strainatate_search_data.filters.room_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_strainatate_search_data.filters.room_types.length))){
            continue;
          }
        } else if(paralela45_strainatate_search_data.filters.room_types.indexOf('') < 0){
          continue;
        }
      }
      if(paralela45_strainatate_search_data.filters.meal_types.length){
        if(offer.Meals && offer.Meals.length){
          var keep = 0;
          for (var j=0; j<offer.Meals.length; j++){
            var meal = offer.Meals[j];
            var meal_type = '' + meal.Type;
            if(paralela45_strainatate_search_data.filters.meal_types.indexOf(meal_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_strainatate_search_data.filters.service_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_strainatate_search_data.filters.service_types.length))){
            continue;
          }
        } else if(paralela45_strainatate_search_data.filters.meal_types.indexOf('') < 0){
          continue;
        }
      }
      if(paralela45_strainatate_search_data.filters.service_types.length){
        if(offer.Services && offer.Services.length){
          var keep = 0;
          for (var j=0; j<offer.Services.length; j++){
            var service = offer.Services[j];
            var service_type = '' + service.Type;
            if(paralela45_strainatate_search_data.filters.service_types.indexOf(service_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_strainatate_search_data.filters.service_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_strainatate_search_data.filters.service_types.length))){
            continue;
          }
        } else if(paralela45_strainatate_search_data.filters.service_types.indexOf('') < 0){
          continue;
        }
      }
      paralela45_strainatate_results.filtered_offers.push(offer);
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
      availabilities: {},
      meal_types: {},
      room_types: {},
      service_types: {},
      stars: {},
      prices: [],
      currency: '',
    };
    if(secondary){
      var offers = paralela45_strainatate_results.filtered_offers;
      secondary_filters = filters;
    } else {
      var offers = paralela45_strainatate_results.offers;
      primary_filters = filters;
    }
    var max_stars = 0;
    // var hide = {
      // 'availabilities' : true,
      // 'meal_types' : true,
      // 'room_types' : true,
      // 'service_types' : true,
    // };
    for (var i=0; i<offers.length; i++){
      var offer = offers[i];
      if(product_codes_with_first_offer.indexOf(offer.ProductCode) < 0){
        product_codes_with_first_offer.push(offer.ProductCode);
        filters.cheapest++;
      }
      var product = paralela45_strainatate_results.products[offer.ProductCode];
      if(filters.currency === ''){
        filters.currency = offer.Currency;
      }
      if(filters.prices.indexOf(offer.Price) < 0){
        filters.prices.push(offer.Price);
      }
      var product_stars = (parseInt(product.Stars) > 0 ? parseInt(product.Stars) : 0);
      if(typeof filters.stars[product_stars] === 'undefined'){
        filters.stars[product_stars] = 0;
      }
      if(product_stars > max_stars){
        max_stars = product_stars;
      }
      filters.stars[product_stars]++;
      
      if(typeof filters.cities[product.CityCode] === 'undefined'){
        filters.cities[product.CityCode] = 0;
      }
      filters.cities[product.CityCode]++;
      if(typeof filters.availabilities[offer.Availability] === 'undefined'){
        filters.availabilities[offer.Availability] = 0;
      }
      filters.availabilities[offer.Availability]++;
      if(offer.Meals && offer.Meals.length){
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
      }
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
    if(typeof paralela45_strainatate_search_data.filters === 'undefined'){
      paralela45_strainatate_search_data.filters = {};
    }
    $('.hotel-stars-filter > .hotel-filters-content').empty();
    if(max_stars < 5){
      max_stars = 5;
    }
    
    if($.isEmptyObject(primary_filters.stars)){
      $('.hotel-stars-filter').hide();
    } else {
      var any_filter_added = false;
      for(var star in primary_filters.stars){
        star = parseInt(star);
        star = star > 0 ? star : 0;
        var number_of_filtered_offers = typeof filters.stars[star] !== 'undefined' ? filters.stars[star] : 0;
        var all_have_it = primary_filters.stars[star] == paralela45_strainatate_results.offers.length;
        var is_checked = paralela45_strainatate_search_data.filters.stars.indexOf(star)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="stars" id="hotel_star_' + star + '" value="' + star + '"/>').prop('checked', is_checked).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_star_' + star + '" />').appendTo($checkWrapper);
        var $stars = $('<strong>');
        $stars.append(Array(star+1).join('<i class="fa fa-star yellowCol noFloat"></i>&nbsp;'));
        $stars.append(Array(max_stars-star+1).join('<i class="fa fa-star noFloat"></i>&nbsp;'));
        $checkLabel.append($stars);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.stars[star]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.stars[star] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.stars[star] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('.hotel-stars-filter > .hotel-filters-content').append($checkWrapper);
      }
      if(any_filter_added){
        $('.hotel-stars-filter').show();
      }
    }
    $('.hotel-cities-filter > .hotel-filters-content').empty();
    
    // BEGIN - cheapest
    $('.hotel-cheapest-filter > .hotel-filters-content').empty();
    var number_of_filtered_offers = filters.cheapest;
    var all_have_it = primary_filters.cheapest == paralela45_strainatate_results.offers.length;
    var all_filtered_have_it = number_of_filtered_offers == offers.length;
    var is_checked = paralela45_strainatate_search_data.filters.cheapest ? true : false;
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
    if($.isEmptyObject(primary_filters.cities)){
      $('.hotel-cities-filter').hide();
    } else {
      var any_filter_added = false;
      for(var city_code in primary_filters.cities){
        var number_of_filtered_offers = typeof filters.cities[city_code] !== 'undefined' ? filters.cities[city_code] : 0;
        var all_have_it = primary_filters.cities[city_code] == paralela45_strainatate_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_strainatate_search_data.filters.cities.indexOf(city_code)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="cities" id="hotel_city_code_' + city_code + '" value="' + city_code + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_city_code_' + city_code + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_strainatate_routes.Cities[city_code] !== 'undefined' ? paralela45_strainatate_routes.Cities[city_code].CityName : '');
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
    $('.hotel-availabilities-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.availabilities)){
      $('.hotel-availabilities-filter').hide();
    } else {
      var any_filter_added = false;
      for(var availability in primary_filters.availabilities){
        var number_of_filtered_offers = filters.availabilities[availability];
        var number_of_filtered_offers = typeof filters.availabilities[availability] !== 'undefined' ? filters.availabilities[availability] : 0;
        var all_have_it = primary_filters.availabilities[availability] == paralela45_strainatate_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_strainatate_search_data.filters.availabilities.indexOf(availability)>-1;
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
    $('.hotel-meal_types-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.meal_types)){
      $('.hotel-meal_types-filter').hide();
    } else {
      var any_filter_added = false;
      for(var meal_type in primary_filters.meal_types){
        var number_of_filtered_offers = filters.meal_types[meal_type];
        var number_of_filtered_offers = typeof filters.meal_types[meal_type] !== 'undefined' ? filters.meal_types[meal_type] : 0;
        var all_have_it = primary_filters.meal_types[meal_type] == paralela45_strainatate_results.offers.length;
        var is_checked = paralela45_strainatate_search_data.filters.meal_types.indexOf(meal_type)>-1;
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
    }
    $('.hotel-room_types-filter > .hotel-filters-content').empty();
    
    if($.isEmptyObject(primary_filters.room_types)){
      $('.hotel-room_types-filter').hide();
    } else {
      var any_filter_added = false;
      for(var room_type in primary_filters.room_types){
        var number_of_filtered_offers = filters.room_types[room_type];
        var number_of_filtered_offers = typeof filters.room_types[room_type] !== 'undefined' ? filters.room_types[room_type] : 0;
        var all_have_it = primary_filters.room_types[room_type] == paralela45_strainatate_results.offers.length;
        var is_checked = paralela45_strainatate_search_data.filters.room_types.indexOf(room_type)>-1;
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
        var all_have_it = primary_filters.service_types[service_type] == paralela45_strainatate_results.offers.length;
        var is_checked = paralela45_strainatate_search_data.filters.service_types.indexOf(service_type)>-1;
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
    if(typeof paralela45_strainatate_search_data.filters === 'undefined'){
      paralela45_strainatate_search_data.filters = {};
    }
    paralela45_strainatate_search_data.filters.name = $.trim($('#package_filter_by_name').val());
    paralela45_strainatate_search_data.filters.stars = [];
    $('.hotel-stars-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.stars.push((parseInt(this.value) > 0 ? parseInt(this.value) : 0));
    });
    paralela45_strainatate_search_data.filters.availabilities = [];
    $('.hotel-availabilities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.availabilities.push(this.value);
    });
    paralela45_strainatate_search_data.filters.availabilities = [];
    $('.hotel-availabilities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.availabilities.push(this.value);
    });
    paralela45_strainatate_search_data.filters.cities = [];
    $('.hotel-cities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.cities.push(this.value);
    });
    paralela45_strainatate_search_data.filters.cheapest = false;
    $('.hotel-cheapest-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.cheapest = true;
    });
    paralela45_strainatate_search_data.filters.service_types = [];
    $('.hotel-service_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.service_types.push(this.value);
    });
    paralela45_strainatate_search_data.filters.room_types = [];
    $('.hotel-room_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.room_types.push(this.value);
    });
    paralela45_strainatate_search_data.filters.meal_types = [];
    $('.hotel-meal_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_strainatate_search_data.filters.meal_types.push(this.value);
    });
    var price_values = $price_slider.slider('values');
    paralela45_strainatate_search_data.filters.min_price = parseFloat(price_values[0]);
    paralela45_strainatate_search_data.filters.max_price = parseFloat(price_values[1]);
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
    $('#offer-booking-checkin').val($(this).data('checkIn'));
    $('#offer-booking-checkout').val($(this).data('checkOut'));
    $('#offer-booking-package_id').val($(this).data('packageId'));
    $('#offer-booking-origin').val(paralela45_strainatate_search_data.origin);
    $('#offer-booking-occupancy').val(JSON.stringify(paralela45_strainatate_search_data.occupancy));
    $('#offer-booking-package_variant_id').val($(this).data('packageVariantId'));
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