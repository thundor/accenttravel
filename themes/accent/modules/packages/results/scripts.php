<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
  var notification_title;
  console.log('package_search_data',package_search_data);
  function interpretResults(){
    var response = package_results.results;
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
          setPackageSearchStatus(false);
          package_search_data.page = page;
          loadResults();
        }
      });
    }
    $('#packageResults').empty();
    // $('.rezCount > .filterTitle').text('Am gasit ' + response.total_items + ' vacante in ' + package_search_data.city_name);
    $('.rezCount > .filterTitle').text('Am gasit ' + response.total_items + ' vacante');
    // $('.rezCount > .mapInfo > span').text(package_search_data.city_name.toUpperCase());
    var start_date = moment(package_search_data.start_date,'Y-MM-DD');
    $('.rezCount .selected_date_start').text(start_date.locale('ro').format("dddd, DD MMMM Y"));
    // var end_date = moment(package_search_data.end_date,'Y-MM-DD');
    // $('.rezCount .selected_date_end').text(end_date.locale('ro').format("dddd, DD MMMM Y"));
    $('.rezCount .selected_rooms').text(package_search_data.occupancy.length + ' camera');
    var travellers = 0;
    var adults = 0;
    var children = 0;
    for (var i=0; i<package_search_data.occupancy.length; i++){
      var occupants = package_search_data.occupancy[i];
      adults += 1 * occupants.adt;
      if(occupants.chd && occupants.chd.length){
        children += 1 * occupants.chd.length;
      }
    }
    travellers = adults + children;
    $('.rezCount .selected_passengers').text(travellers + ' calatori');
    // var nights = end_date.diff(start_date,'days');
    // $('.rezCount .selected_date_interval').text('(' + nights + ' nopti)');
    
    var $package_box_model = $('#packageResultModel').clone().removeAttr('id style');
    
    notification_title = package_search_data.occupancy.length + ' ' + (package_search_data.occupancy.length > 1 ? 'camere' : 'camera');
    notification_title += ', ' + start_date.locale('ro').format("dddd, DD MMMM Y");
    notification_title += ', ' + travellers + ' ' + (travellers > 1 ? 'persoane' : 'persoana');
    
    for (var i=0; i<response.packages.length; i++){
      var package = response.packages[i];
      var $package_box = $package_box_model.clone();
      $('.hartaPackage', $package_box).attr('data-lat', package.Lat);
      $('.hartaPackage', $package_box).attr('data-lng', package.Lng);
      $('.hartaPackage', $package_box).attr('data-city', package_search_data.city_name);
      $('.hartaPackage', $package_box).attr('data-address', package.Address);
      $('.hartaPackage', $package_box).attr('data-name', package.Name);
      $('.hotel-image', $package_box).attr('href', package.link)
        .css('background-image',  'url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>)')
        .addClass('lazy')
        .attr('data-src', package.Image);
      $('.package-name', $package_box).text(package.Name);
      var stars = 0;
      if(package.AccommodationStars && package.AccommodationStars.length){
        for(var j=0; j<package.AccommodationStars.length; j++){
          var star = parseInt(package.AccommodationStars[j]);
          if(star > stars){
            stars = star;
          }
        }
      }
      if(stars){
        $('.package-stars', $package_box).html(" " + Array(parseInt(stars) + 1).join('<i class="fa fa-star"></i>'));
      }
      $('.package-category', $package_box).text(package.Category);
      $('.package-project', $package_box).text(package.ProjectName);
      // $('.package-description', $package_box).text(package.Description);
      $('.hotel-info-short', $package_box).text(package.Description ? package.Description.substring(0,150) : '');
      $('.hotel-info-rest', $package_box).text(package.Description ? package.Description.substring(150): '').hide();
      if($('.hotel-info-rest', $package_box).is(':empty')){
        $('.package-description > a', $package_box).hide();
      } else {
        $('.package-description > a', $package_box).on('click', function(e){
          e.preventDefault();
          $(this).hide();
          $(this).next().show();
        });
      }
      $('.package-name', $package_box).attr('href', package.link);
      $('.reserve-button', $package_box).attr('href', package.link);
      $('.notification-button', $package_box).attr({
        'id': 'button_notification_package_' + package.Id,
        'data-type': 'package',
        'data-ref_id': package.Id,
        'data-package_name': package.Name + (stars ? ' (' + stars + ' stele)' : ''),
        'data-category': package.Category,
        'data-project_name': package.ProjectName,
        'data-amount': package.MinPrice,
        'data-currency': package.Currency,
        'data-link': package.link
      });
      if(package.MinPrice){
        $('.current-price', $package_box).text(format_price(Math.ceil(package.MinPrice), package.Currency));
      } else {
        $('.current-price', $package_box).remove();
      }
      $('#packageResults').append($package_box);
    }
    $('.rezCount').show();
    $('.sortPackage').show();
    setPackageSearchStatus(true);
    $('#packageResults .lazy').lazy();
    $('#packagesResultsWrapper').show();
  }
  var show_warnings = true;
  function interpretNoPackagesResponse(result,initial){
    setPackageSearchStatus(true);
    if(initial && result && result.data && result.data.packages_expired){
      show_warnings = false;
    }
    if(show_warnings){
      $('#packageWarnings').show();
    }
    show_warnings = true;
    $('#packagesResultsWrapper').hide();
  }
  var package_results;
  function loadResults(initial){
    $('#packageWarnings').hide();
    $.ajax({
      url: '<?php echo site_url('trip/packages/loadResults'); ?>',
      method: 'post',
      dataType: 'json',
      data: package_search_data,
      async: true,
      success: function(result,status,xhr){
        console.log(result);
        if(!result.status || result.status !== 'success'){
          interpretNoPackagesResponse(result,initial);
          return;
        }
        package_search_data = result.data;
        package_results = result;
        interpretResults();
        if(initial){
          loadFilters();
        }
      },
      error: function(jqXHR,textStatus,error){
        console.log(jqXHR,textStatus,error);
        setPackageSearchStatus(true);
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
  /* var package_markers;
  function loadMarkers(){
    $.ajax({
      url: '<?php echo site_url('trip/packages/loadMarkers'); ?>',
      method: 'post',
      dataType: 'json',
      data: package_search_data,
      async: true,
      success: function(result,status,xhr){
        console.log(result);
        if(!result.status || result.status !== 'success'){
          setPackageSearchStatus(true);
          return;
        }
        package_markers = result.response;
      }
    });
  } */
  // var package_filters;
  function loadFilters(){
    var filters = package_search_data.filters;
    console.log('loading_filters', filters);
    if(!filters){
      filters = {};
    }
    if(typeof filters.Name !== 'string' ){
      filters.Name = '';
      if(typeof package_search_data.hotel_name === 'string' ){
        filters.Name = package_search_data.hotel_name;
      }
    }
    $('#package_filter_by_name').val(filters.Name);
    return;
  }
  function setSearchAndInitiate(){
    $('#packageWarnings').hide();
    $.ajax({
      url: '<?php echo site_url('trip/packages/setSearchAndInitiate'); ?>',
      method: 'post',
      dataType: 'json',
      data: package_search_data,
      async: true,
      success: function(result,status,xhr){
        console.log(result);
        if(!result.status || result.status !== 'success'){
          interpretNoPackagesResponse(result);
          return;
        }
        package_search_data = result.data;
				loadResults();
        // package_results = result;
        loadFilters();
        // loadMarkers();
        // interpretResults();
      },
      error: function(jqXHR,textStatus,error){
        console.log(jqXHR, textStatus, error);
        setPackageSearchStatus(true);
      }
    });
  }
  function setSort(){
    var sort_element = $('.package-sort-by').filter(function(){return $(this).val()>0;}).first();
    if(sort_element.length){
      package_search_data.sort_by = sort_element.attr('name');
      package_search_data.sort_order = parseInt(sort_element.val()) - 1;
    }
  }
  function setFilters(){
    package_search_data.filters.Name = $.trim($('#package_filter_by_name').val());
    /* package_search_data.filters.stars = [];
    $('.package-stars-filter input[type=checkbox]:checked').each(function(){
      package_search_data.filters.stars.push(parseInt(this.value));
    });
    package_search_data.filters.facilities = [];
    $('.package-facilities-filter input[type=checkbox]:checked').each(function(){
      package_search_data.filters.facilities.push(parseInt(this.value));
    });
    package_search_data.filters.activity_categories = [];
    package_search_data.filters.activities = [];
    $('.package-activitycategories-filter input[type=checkbox]:checked').each(function(){
      package_search_data.filters.activity_categories.push(parseInt(this.value));
      package_search_data.filters.activities = package_search_data.filters.activities.concat($(this).attr('data-activities').split(','));
    });
    package_search_data.filters.locations = [];
    $('.package-locations-filter input[type=checkbox]:checked').each(function(){
      package_search_data.filters.locations.push(parseInt(this.value));
    });
    package_search_data.filters.pois = [];
    $('.package-pois-filter input[type=checkbox]:checked').each(function(){
      package_search_data.filters.pois.push(parseInt(this.value));
    });
    var $price_slider = $("#slider-range").slider();
    var price_values = $price_slider.slider('values');
    package_search_data.filters.min_price = parseFloat(price_values[0]);
    package_search_data.filters.max_price = parseFloat(price_values[1]); */
  }
  function resetFilters(){
    $('#package_filter_by_name').val('');
    /* $('.package-stars-filter input[type=checkbox]:checked').prop('checked',false);
    $('.package-facilities-filter input[type=checkbox]:checked').prop('checked',false);
    $('.package-activitycategories-filter input[type=checkbox]:checked').prop('checked',false);
    $('.package-locations-filter input[type=checkbox]:checked').prop('checked',false);
    $('.package-pois-filter input[type=checkbox]:checked').prop('checked',false);
    var $price_slider = $("#slider-range").slider();
    var min_price = $price_slider.slider('option','min');
    var max_price = $price_slider.slider('option','max');
    
    $price_slider.slider('option',{
      min: min_price,
      max: max_price,
      values: [min_price, max_price],
    }); */
  }
  $('#package_filter_by_name_button').on('click',function(){
    setPackageSearchStatus(false);
    setFilters();
    package_search_data.page = 1;
    loadResults();
  });
  $('.package-stars-filter').on('change', 'input[type=checkbox]',function(){
    setPackageSearchStatus(false);
    setFilters();
    package_search_data.page = 1;
    loadResults();
  });
  $('.package-facilities-filter, .package-activitycategories-filter, .package-locations-filter, .package-pois-filter').on('change', 'input[type=checkbox]',function(){
    setPackageSearchStatus(false);
    setFilters();
    package_search_data.page = 1;
    loadResults();
  });
  package_submit_function = function (e){
    if(!search_is_over){
      console.log('A previous search is not complete. Ignoring request.');
      return;
    }
    $('#packagesResultsWrapper').hide();
    $('#packageResults').empty();
    $('.rezCount').hide();
    $('ul.pagination').empty();
    $('.sortPackage').hide();
    setPackageSearchStatus(false);
    setPackageData($(this));
    setPackageSearchAndRedirect();
  };
  $('#applyFilters').click(function(){
    setPackageSearchStatus(false);
    setFilters();
    package_search_data.page = 1;
    loadResults();
    var body = $("html, body");
    var pagination_top = $('h1.filterTitle').first().offset().top;
    body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
    });
  });
  $('#resetFilters').click(function(){
    setPackageSearchStatus(false);
    resetFilters();
    setFilters();
    package_search_data.page = 1;
    loadResults();
    var body = $("html, body");
    var pagination_top = $('h1.filterTitle').first().offset().top;
    body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
    });
  });
  $('.package-sort-by').prop('disabled', false).on('change', function(){
    setPackageSearchStatus(false);
    var $me = $(this);
    if($me.val() === '0'){
      $me.val('1');
    }
    $('.package-sort-by').filter(function(){return !$(this).is($me);}).val(0);
    setSort();
    package_search_data.page = 1;
    loadResults();
  });
  $(document).on("click",'.inchideH', function () {
    $(this).parents(".boxHotel").hide("slow");
  });
  var notification_id;
  $("#packageResults").on("click", ".notification-button", function () {
    notification_id = this.id;
    var $this = $(this);
    var title = $this.data('package_name') + ', ' + notification_title;
    title += ' (' + $this.data('category') + ')';
    var obj = {
      ref_id : $this.data('ref_id'),
      type : $this.data('type'),
      title : title,
      amount : $this.data('amount'),
      currency : $this.data('currency'),
      data : JSON.stringify(package_search_data)
    };
    openNotificationModal(obj);
  });
  $(document).ready(function(){
    $("#slider-range").on('slidestop', function (event, ui) {
      setPackageSearchStatus(false);
      $(this).trigger('updatePrice');
      setFilters();
      package_search_data.page = 1;
      loadResults();
    });
    console.log(package_search_data);
  <?php if(isset($_GET['init'])){ ?>
    removeLocationParam('init');
    $('.package-search').first().submit();
  <?php } elseif(!isset($_GET['n'])){ ?>
    if(package_search_data.index_id && package_search_data.index_id.length>0){
      setPackageSearchStatus(false);
      // show_warnings = false;
      loadResults(true);
    }
  <?php } else { ?>
	setPackageSearchStatus(false);
	setSearchAndInitiate();
  <?php } ?>
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>