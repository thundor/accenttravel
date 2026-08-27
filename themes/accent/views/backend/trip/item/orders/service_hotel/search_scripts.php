<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
(function($){
  var $error_container = $('#result_serviceHotelForm');
  var $service_hotel_citybreak = $('#service_hotel_citybreak');
  var $service_hotel_search_rooms_citybreak = $('#service_hotel_search_rooms_citybreak');
  var $service_hotel_search_rooms_table_container = $('#service_hotel_search_rooms_table_container');
  var $service_hotel_search_rooms_table_outside_container = $('#service_hotel_search_rooms_table_outside_container');
  var $service_citybreak_chosen_hotel = $('#service_citybreak_chosen_hotel');
  var $service_citybreak_chosen_hotel_packages = $('#service_citybreak_chosen_hotel_packages');
  $('#service_hotel_search_city').autocomplete({
    source: function(request, response){
      $.ajax({
        url: "<?php echo site_url('trip/hotels/loadLocations'); ?>",
        dataType: "json",
        data: {
          q: request.term
        }
      }).done(function(resp, textStatus, jqXHR){
        console.log('hotel autocomplete', resp);
        $error_container.empty();
        if(!resp.status || resp.status !== 'success'){
          showMessage($error_container,'Eroare in cautarea oraselor', 'danger');
          return;
        }
        var data = resp.response;
        var response_data = [];
        if(data && data.length){
          for (var i=0; i < data.length; i++){
            var item = data[i];
            var label = item.Name + ' (' + item.CountryName + ')';
            var response_item = {
              id: item.CityId,
              value: item.Name,
              city_id: item.CityId,
              city_name: item.Name,
              country_id: item.CountryId,
              country_name: item.CountryName,
              label: label
            };
            response_data.push(response_item);
          }
        }
        response( response_data );
      }).fail(function(jqXHR, textStatus, errorThrown){
        console.log('service_hotel_search_city','autocomplete',jqXHR, textStatus, errorThrown);
        $error_container.empty();
        showMessage($error_container,'Eroare in cautarea oraselor', 'danger');
      });
    },
    minLength: 2,
    select: function( event, ui ) {
      $('#service_hotel_search_city_id').val(ui.item.city_id);
      $('#service_hotel_search_city_name').val(ui.item.city_name);
      $('#service_hotel_search_country_id').val(ui.item.country_id);
      $('#service_hotel_search_country_name').val(ui.item.country_name);
    }
  }).blur(function(){
    if(this.value !== $('#service_hotel_search_city_name').val()){
      this.value = '';
      $('#service_hotel_search_city_id').val(0);
      $('#service_hotel_search_city_name').val('');
      $('#service_hotel_search_country_id').val(0);
      $('#service_hotel_search_country_name').val('');
    }
  });
  var today_moment = moment().startOf('day');
  var tomorrow_moment = moment(today_moment).add(1, 'days');
  $('#service_hotel_search_checkout').makeCaleranDatepicker({
    startEmpty: false,
    minDate: tomorrow_moment,
    endDate: tomorrow_moment,
    startDate: tomorrow_moment
  }).makeInputmaskDate();
  $('#service_hotel_search_checkin').makeCaleranDatepicker({
    startEmpty: false,
    minDate: today_moment,
    startDate: today_moment
  }).makeInputmaskDate().on('change', function(){
    var val_moment = moment(this.value, 'DD.MM.Y');
    if(!val_moment.isValid()){
      return;
    }
    var val_tomorrow_moment = val_moment.add(1,'day');
    var $checkout_caleran = $('#service_hotel_search_checkout').data("caleran");
    $checkout_caleran.config.minDate = val_tomorrow_moment;
    var checkout_val = $('#service_hotel_search_checkout').val();
    var checkout_val_moment = moment(checkout_val, 'DD.MM.Y');
    if(!checkout_val_moment.isValid() || checkout_val_moment.isBefore(val_tomorrow_moment)){
      $checkout_caleran.config.startDate = val_tomorrow_moment;
      $checkout_caleran.config.endDate = val_tomorrow_moment;
      checkout_val_moment = val_tomorrow_moment;
      checkout_val = checkout_val_moment.format('DD.MM.Y');
      $('#service_hotel_search_checkout').val(checkout_val);
      $('#service_hotel_search_checkout').focus();
    }
  });
  $('#service_hotel_search_checkout').on('change blur', function(){
    $('#service_hotel_search_rooms_table .child-birth_date').trigger('update-child-birth_date');
  });
  var hotel_room_index = 0;
  $('#serviceHotelForm').on('click','.btn-add-room', function(){
    var $tbody = $(this).closest('table').children('tbody');
    var $new_tr = $('#hotel-room-model').clone().removeAttr('id').data('index',hotel_room_index);
    $('>td:nth-child(3)>div', $new_tr).remove();
    $('input', $new_tr).val(1).attr('name','occupancy[' + hotel_room_index + '][adt]');
    hotel_room_index++;
    $new_tr.appendTo($tbody);
    // if(!first_room){
      // $('input', $new_tr).select();
    // }
    first_room = false;
  }).on('click','.btn-delete-room', function(){
    var $tbody = $(this).closest('tbody');
    if($tbody.children('tr').length == 1){
      var $new_tr = $('tr:first-child',$tbody);
      $('>td:nth-child(3)>div', $new_tr).remove();
      $('input', $new_tr).val(1);
    } else {
      $(this).closest('tr').remove();
    }
  }).on('click','.btn-add-child', function(){
    fixHotelSearchDates();
    var $tr = $(this).closest('tr');
    var index = $tr.data('index')
    var $td = $(this).closest('td');
    var $new_child = $('#hotel-room-child-model').clone().removeAttr('id');
    $('input.child-age', $new_child).attr('name','occupancy[' + index + '][chd][age][]');
    $('input.child-birth_date', $new_child).attr('name','occupancy[' + index + '][chd][birth_date][]');
    
    var service_hotel_search_checkout = $('#service_hotel_search_checkout').val();
    if(service_hotel_search_checkout && service_hotel_search_checkout !== ''){
      var reference_moment = moment(service_hotel_search_checkout,'DD.MM.Y').startOf('day');
    } else {
      var reference_moment = moment().startOf('day');
    }
    var min_child_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).add(1,'days').startOf('day');
    var max_child_moment = moment().startOf('day');
    $('input.child-birth_date', $new_child).makeCaleranDatepicker({
      startEmpty: true,
      minDate: min_child_moment,
      maxDate: max_child_moment,
      startDate: max_child_moment
    }).makeInputmaskDate().on('change blur', function(e){
      $(this).trigger('update-child-birth_date');
    }).on('update-child-birth_date', function(e){
      console.log('updating-child-value', this, this.value);
      if(!this.value || this.value===''){
        return;
      }
      var service_hotel_search_checkout = $('#service_hotel_search_checkout').val();
      if(service_hotel_search_checkout && service_hotel_search_checkout !== ''){
        var reference_moment = moment(service_hotel_search_checkout,'DD.MM.Y').startOf('day');
      } else {
        var reference_moment = moment().startOf('day');
      }
      var val_moment = moment(this.value,'DD.MM.Y').startOf('day');
      var age_in_years = reference_moment.diff(val_moment,'years');
      $('input.child-age',$(this).closest('.hotel-room-child')).val(age_in_years);
    });
    $new_child.appendTo($td);
    // $('input.child-birth_date', $new_child).select();
  }).on('click','.btn-remove-child', function(){
    var $child = $(this).closest('div.input-group');
    $child.remove();
  });
  var first_room = true;
  $('#serviceHotelForm .btn-add-room')[0].click();
  
  var $fellow_info_container = $('#serviceHotelFormFellows');
  var $fellow_info_wrapper = $('#serviceHotelFormFellowsFormWrapper');
  var $room_packages_loading = $('#service-hotel-room-packages-loading');
  var $room_packages = $('#service-hotel-room-packages');
  var $hotel_details = $('#service-hotel-hotel-details');
  var $service_hotel_tab = $('#service_hotel_tab');
  var $navigation = $('#serviceHotelResultsNavigation');
  
  var $price_slider = $("#hotel-services-search-filter-price-slider-range").slider({
    range: true,
    min: 150,
    max: 2500,
    values: [175, 1300],
    slide: function (event, ui) {
      $(this).trigger('updatePrice', ui);
    }
  }).on('updatePrice', function(e, ui){
    if(ui){
      var slider_values = ui.values;
    } else {
      var $price_slider = $(this).slider();
      var slider_values = $price_slider.slider('values');
    }
    $("#hotel-services-search-filter-price-slider-amount").val(parseFloat(slider_values[ 0 ]).toLocaleString('ro') + " <?php echo $this->_ci->currency_symbol; ?> - " + parseFloat(slider_values[ 1 ]).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
  });
  $price_slider.trigger('updatePrice');
  
  function loadRoomOccupancyDetails(){
    var service_hotel_search_checkin = hotel_results.data.start_date;
    var service_hotel_search_checkout = hotel_results.data.end_date;
    var service_hotel_search_checkin_moment = moment(service_hotel_search_checkin,'Y.MM.DD').startOf('day');
    var service_hotel_search_checkout_moment = moment(service_hotel_search_checkout,'Y.MM.DD').startOf('day');

    var reference_moment = service_hotel_search_checkout_moment;
    var min_adult_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_adult_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).startOf('day');
    var index = 0;
    var adults = 0;
    var children = 0;
    var rooms = hotel_results.data.occupancy.length;
    $fellow_info_container.empty();
    $room_packages_loading.hide();
    $room_packages.empty();
    $fellow_info_wrapper.hide();
    for(var rindex=0; rindex<hotel_results.data.occupancy.length; rindex++){
      var input_name_prefix = 'room[' + rindex + ']';
      var room_occupancy = hotel_results.data.occupancy[rindex];
      var room_number = rindex+1;
      var $hotel_room_fellows = $('#hotel-room-fellows-model').clone().removeAttr('id').hide();
      $hotel_room_fellows.appendTo($fellow_info_container);
      if(rindex>0){
        $hotel_room_fellows.addClass('mt-3');
      }
      var $hotel_room_fellow_container = $('.room-occupancy-fellows', $hotel_room_fellows);
      var room_adults = parseInt(room_occupancy.adt);
      
      var input_name_prefix_adt = input_name_prefix + '[adt]';
      for(var adt_index=1; adt_index<=room_occupancy.adt; adt_index++){
        var input_name_prefix_adt_i = input_name_prefix_adt + '[' + (adt_index-1) +']';
        var $hotel_room_fellow = $('#hotel-room-fellow-adult-model').clone().removeAttr('id');
        if(adt_index>1){
          $hotel_room_fellow.addClass('mt-1');
        }
        $hotel_room_fellow.appendTo($hotel_room_fellow_container);
        var $passenger_title = $('.passenger-title',$hotel_room_fellow);
        $passenger_title.attr({
          name: input_name_prefix_adt_i + '[title]'
        });
        $passenger_title.select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
        
        var $passenger_lastname = $('.passenger-lastname',$hotel_room_fellow);
        $passenger_lastname.attr({
          name: input_name_prefix_adt_i + '[lastname]'
        });
        var $passenger_firstname = $('.passenger-firstname',$hotel_room_fellow);
        $passenger_firstname.attr({
          name: input_name_prefix_adt_i + '[firstname]'
        });
        var $passenger_email = $('.passenger-email',$hotel_room_fellow);
        $passenger_email.attr({
          name: input_name_prefix_adt_i + '[email]'
        });
        $passenger_email.val($('#client_email').val());
        var $passenger_phone = $('.passenger-phone',$hotel_room_fellow);
        $passenger_phone.attr({
          name: input_name_prefix_adt_i + '[phone]'
        });
        var client_phone = $('#client_phone').val();
        var client_phone_prefix_country = $('#client_phone_prefix').val();
        if(client_phone_prefix_country && typeof(countries_selections[client_phone_prefix_country]) !== 'undefined' && countries_selections[client_phone_prefix_country].prefix){
          client_phone = '+' + countries_selections[client_phone_prefix_country].prefix + ' ' + client_phone;
        }
        $passenger_phone.val(client_phone);
        
        if(!rindex && adt_index==1){
          var client_title = $('#client_title').val();
          if(typeof(countries_selections[client_title]) !== 'undefined'){
            $passenger_title.val(client_title).trigger('change.select2_4');
          }
          $passenger_firstname.val($('#client_firstname').val());
          $passenger_lastname.val($('#client_lastname').val());
        }
        $('>.card-header>.fellow-age', $hotel_room_fellow).remove();
        $('>.card-header>.fellow-type>.fellow-index', $hotel_room_fellow).text(i);
      }
      var room_children = 0;
      if(room_occupancy.chd){
        var children_ages = room_occupancy.chd.age;
        room_children = children_ages.length;
        var children_birth_dates = room_occupancy.chd.birth_date;
        for(var i=0; i<children_ages.length; i++){
          var child_birth_date = children_birth_dates && typeof children_birth_dates[i] !== 'undefined' ? children_birth_dates[i] : '';
          var child_age = children_ages[i];
          var input_name_prefix_chd = input_name_prefix + '[chd]';
          var input_name_prefix_chd_i = input_name_prefix_chd + '[' + (room_children-1) +']';
          var $hotel_room_fellow = $('#hotel-room-fellow-child-model').clone().removeAttr('id');
          $hotel_room_fellow.addClass('mt-1');
          $hotel_room_fellow.appendTo($hotel_room_fellow_container);
          var $passenger_birth_date = $('.passenger-birth_date',$hotel_room_fellow);
          $passenger_birth_date.attr({
            name: input_name_prefix_chd_i + '[birth_date]'
          });
          $passenger_birth_date.val(child_birth_date);
          var $passenger_title = $('.passenger-title',$hotel_room_fellow);
          $passenger_title.attr({
            name: input_name_prefix_chd_i + '[title]'
          });
          $passenger_title.select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
          var $passenger_lastname = $('.passenger-lastname',$hotel_room_fellow);
          $passenger_lastname.attr({
            name: input_name_prefix_chd_i + '[lastname]'
          });
          var $passenger_firstname = $('.passenger-firstname',$hotel_room_fellow);
          $passenger_firstname.attr({
            name: input_name_prefix_chd_i + '[firstname]'
          });
          $('>.card-header>.fellow-type>.fellow-index', $hotel_room_fellow).text(room_children);
          $('>.card-header>.fellow-age>.fellow-child-age-number', $hotel_room_fellow).text(child_age);
        }
      }
      adults += room_adults;
      children += room_children;
      $('.room-number', $hotel_room_fellows).text(room_number);
      $('.room-occupancy-adults-number', $hotel_room_fellows).text(room_adults);
      if(room_adults == 1){
        $('.room-occupancy-adults .plural', $hotel_room_fellows).remove();
      } else {
        $('.room-occupancy-adults .singular', $hotel_room_fellows).remove();
      }
      if(room_children){
        $('.room-occupancy-children-number', $hotel_room_fellows).text(room_children);
        if(room_children == 1){
          $('.room-occupancy-children .plural', $hotel_room_fellows).remove();
        } else {
          $('.room-occupancy-children .singular', $hotel_room_fellows).remove();
        }
      } else {
        $('.room-occupancy-children', $hotel_room_fellows).remove();
      }
      $hotel_room_fellows.show();
    }
    var nights = service_hotel_search_checkout_moment.diff(service_hotel_search_checkin_moment,'days');
    $('#service_hotel_search_hotel_rooms_number').html(rooms);
    if(rooms == 1){
      $('#service_hotel_search_hotel_rooms > .singular').show();
      $('#service_hotel_search_hotel_rooms > .plural').hide();
    } else {
      $('#service_hotel_search_hotel_rooms > .singular').hide();
      $('#service_hotel_search_hotel_rooms > .plural').show();
    }
    $('#service_hotel_search_hotel_adults_number').html(adults);
    if(adults == 1){
      $('#service_hotel_search_hotel_adults > .singular').show();
      $('#service_hotel_search_hotel_adults > .plural').hide();
    } else {
      $('#service_hotel_search_hotel_adults > .singular').hide();
      $('#service_hotel_search_hotel_adults > .plural').show();
    }
    $('#service_hotel_search_hotel_children_number').html(children);
    if(children == 1){
      $('#service_hotel_search_hotel_children > .singular').show();
      $('#service_hotel_search_hotel_children > .plural').hide();
    } else {
      $('#service_hotel_search_hotel_children > .singular').hide();
      $('#service_hotel_search_hotel_children > .plural').show();
    }
    $('#service_hotel_search_hotel_nights_number').html(nights);
    if(nights == 1){
      $('#service_hotel_search_hotel_nights > .singular').show();
      $('#service_hotel_search_hotel_nights > .plural').hide();
    } else {
      $('#service_hotel_search_hotel_nights > .singular').hide();
      $('#service_hotel_search_hotel_nights > .plural').show();
    }
    $fellow_info_wrapper.show();
    $('#overview_serviceHotelForm').show();
    
    var $search_container_header = $('a[href="#serviceHotelFormContainer"]:not(.collapsed)');
    if($search_container_header.length){
      $search_container_header[0].click();
    }
    return true;
  }
  function fixHotelSearchDates(){
    var service_hotel_search_checkin = $('#service_hotel_search_checkin').val();
    var service_hotel_search_checkout = $('#service_hotel_search_checkout').val();
    var start_date = moment(service_hotel_search_checkin,'DD.MM.Y');
    var end_date = moment(service_hotel_search_checkout,'DD.MM.Y');
    if(end_date && start_date){
      if(end_date.isBefore(start_date)){
        $('#service_hotel_search_checkin').val(service_hotel_search_checkout);
        $('#service_hotel_search_checkout').val(service_hotel_search_checkin);
      }
    }
  }
  $('#service_hotel_search_submit').on('click',function(){
    $('#service_hotel_search_rooms_table .child-birth_date').trigger('update-child-birth_date');
    var service_hotel_search_checkin = $('#service_hotel_search_checkin').val();
    var service_hotel_search_checkout = $('#service_hotel_search_checkout').val();
    if(!service_hotel_search_checkin || !service_hotel_search_checkout){
      return true;
    }
    fixHotelSearchDates();
    index = 0;
    $('#service_hotel_search_rooms_table > tbody > tr').each(function(rindex){
      var tr_index = $(this).data('index');
      var room_number = index+1;
      $('input[name]',this).each(function(){
        $(this).attr('name',$(this).attr('name').replace('occupancy[' + tr_index + ']', 'occupancy[' + index + ']'));
      });
      index++;
    });
    return true;
  });
  var search_is_over = true;  
  function setSearchStatus(search_status){
    $('#service_hotel_search_submit', $service_hotel_tab).prop('disabled',!search_status);
    $('#service_hotel_reserve_submit').prop('readonly',!search_status);
    $('.hotel-sort-by', $service_hotel_tab).prop('readonly',!search_status);
    search_is_over = search_status;
  }
  
  function interpretNoHotelsResponse(result){
    var $search_container_header = $('a[href="#serviceHotelFormContainer"].collapsed');
    if($search_container_header.length){
      $search_container_header[0].click();
    }
    $fellow_info_wrapper.hide();
    setSearchStatus(true);
    $error_container.empty();
    clearFilters();
    if(result.status == 'fail'){
      showMessage($error_container,'Eroare in cautarea hotelurilor','warning');
    } else {
      showMessage($error_container,'Nu au fost gasite rezultate','warning');
    }
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    $('#serviceHotelResults').empty();
  }
  function interpretResults(){
    var citybreak_mode = !$service_hotel_citybreak.is(':checked');
    $fellow_info_wrapper.show();
    loadRoomOccupancyDetails();
    $('.hotel-sort-by', $service_hotel_tab).prop('disabled', false);
    var response = hotel_results.results;
    var placeholder_image = response.placeholder_image;
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    $error_container.empty();
    if(response.total_items == 1){
      showMessage($error_container,'A fost gasit un hotel','success');
    } else {
      showMessage($error_container,'Au fost gasite ' + response.total_items + ' hoteluri','success');
    }
    
    var page = parseInt(response.page);
    var total_pages = parseInt(response.page_count);
    if(total_pages && total_pages>=page){
      $navigation.twbsPagination({
        startPage: page,
        totalPages: total_pages,
        visiblePages: 10,
        first: "<<",
        prev: "<",
        next: ">",
        last: ">>",
        onPageClick: function (evt, page) {
          if(page == response.page){
            return;
          }
          if(!search_is_over){
            console.log('hotelpagenav','Search is not over, aborting');
            return false;
          }
          hotel_search_data.page = page;
          loadResults();
        }
      });
    }
    $('#serviceHotelResults').empty();
    for (var i=0; i<response.hotels.length; i++){
      var hotel = response.hotels[i];
      var $hotel_box = $('#hotel-result-model').clone().attr('id','hotel-result-' + hotel.Id);
      // $('.hartaHotel', $hotel_box).attr('data-lat', hotel.Lat);
      // $('.hartaHotel', $hotel_box).attr('data-lng', hotel.Lng);
      // $('.hartaHotel', $hotel_box).attr('data-city', hotel_search_data.city_name);
      // $('.hartaHotel', $hotel_box).attr('data-address', hotel.Address);
      // $('.hartaHotel', $hotel_box).attr('data-name', hotel.Name);
      $('.hotel-image', $hotel_box)
        .addClass('lazy')
        .attr('data-src', hotel.Image);
        $('.hotel-name', $hotel_box).text(hotel.Name);
      // $('.hotel-info-short', $hotel_box).text(hotel.FullDesc ? hotel.FullDesc.substring(0,150) : '');
      // $('.hotel-info-rest', $hotel_box).text(hotel.FullDesc ? hotel.FullDesc.substring(150): '').hide();
      // if($('.hotel-info-rest', $hotel_box).is(':empty')){
        // $('.hotel-info > a', $hotel_box).hide();
      // } else {
        // $('.hotel-info > a', $hotel_box).on('click', function(e){
          // e.preventDefault();
          // $(this).hide();
          // $(this).next().show();
        // });
      // }
      $('.hotel-id', $hotel_box).val(hotel.Id);
      $('.result-code', $hotel_box).val(hotel_results.data.code);
      $('.hotel-link', $hotel_box).attr('href',hotel.link);
      $('.reserve-button', $hotel_box).attr('href',hotel.link);
      $('.hotel-stars', $hotel_box).html(" " + Array(parseInt(hotel.Stars) + 1).join('<i class="fa fa-star"></i>'));
      $('.hotel-stars', $hotel_box).addClass('text-warning');
      /* if(hotel.Stars <= 1){
        $('.hotel-stars', $hotel_box).addClass('text-danger');
      } else if(hotel.Stars <= 2){
        $('.hotel-stars', $hotel_box).addClass('text-warning');
      } else if(hotel.Stars <= 3){
        $('.hotel-stars', $hotel_box).addClass('text-muted');
      } else if(hotel.Stars <= 4){
        $('.hotel-stars', $hotel_box).addClass('text-info');
      } else if(hotel.Stars <= 5){
        $('.hotel-stars', $hotel_box).addClass('text-success');
      } else if(hotel.Stars <= 6){
        $('.hotel-stars', $hotel_box).addClass('text-primary');
      } */
      var accordion_id = 'room_packages_' + hotel.Id;
      var $accordion = $('.hotel-result-accordion', $hotel_box);
      $accordion.attr('id',accordion_id);
      $('>.card', $accordion).each(function(index){
        var accordion_tab_id = accordion_id + '_' + index;
        var $card_header = $('>.card-header', this);
        $card_header.attr('id', accordion_tab_id + '_header');
        $card_header.next('div').attr({
          'id' : accordion_tab_id + '_collapse',
          'aria-labelledby' : accordion_tab_id + '_header'
        });
        $('>h5>a', $card_header).attr({
          'href' : '#' + accordion_tab_id + '_collapse',
          'data-parent' : '#' + accordion_id,
          'aria-controls' : '#' + accordion_tab_id + '_collapse'
        });
      });
      $('.hotel-info-description', $accordion).text(hotel.FullDesc ? hotel.FullDesc : hotel.ShortDesc);
      $('.hotel-info-facilities', $hotel_box).text(hotel.Facilities);
      if(hotel.Phone && $.trim(hotel.Phone).length){
        $('.hotel-info-phone', $hotel_box).attr('href','tel:' + $.trim(hotel.Phone));
        $('.hotel-info-phone>span', $hotel_box).text($.trim(hotel.Phone));
      }
      if(hotel.Fax && $.trim(hotel.Fax).length){
        $('.hotel-info-fax', $hotel_box).attr('href','fax:' + $.trim(hotel.Fax));
        $('.hotel-info-fax>span', $hotel_box).text($.trim(hotel.Fax));
      }
      if(hotel.Email && $.trim(hotel.Email).length){
        $('.hotel-info-email', $hotel_box).attr('href','mailto:' + $.trim(hotel.Email));
        $('.hotel-info-email>span', $hotel_box).text($.trim(hotel.Email));
      }
      $('.room-options-toggle', $hotel_box).attr('data-hotel_id', hotel.Id).on('click', function(e){
        var citybreak_mode = !$service_hotel_citybreak.is(':checked');
        var $hotel_result = $(this).closest('.hotel-result');
        var $hotel_header = $('>.card-header',$hotel_result).clone();
        
        if(!citybreak_mode){
          $hotel_details.empty();
          $hotel_header.appendTo($hotel_details);
          $hotel_details.show();
        } else {
          $service_citybreak_chosen_hotel.empty();
          $hotel_header.appendTo($service_citybreak_chosen_hotel);
        }
        
        loadRoomPackages($(this).data('hotel_id'));
        return false;
      });
      
      $('.hotel-location', $hotel_box).text(hotel.Address);
      // $('.hotel-expiration', $hotel_box).text(moment.unix(parseInt(hotel.ExpireTime)).format('DD.MM.Y HH:mm:ss'));
      $('.current-price', $hotel_box).text(Math.ceil(hotel.MinPrice).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
      $('#serviceHotelResults').append($hotel_box);
    }
    $('#hotelsResultsWrapper').show();
    $('#serviceHotelResults .lazy').lazy();
  }
  var hotel_filters;
  function setSort(){
    var sort_element = $('.hotel-sort-by', $service_hotel_tab).filter(function(){return $(this).val()>0;}).first();
    if(sort_element.length){
      hotel_search_data.sort_by = sort_element.attr('name');
      hotel_search_data.sort_order = parseInt(sort_element.val()) - 1;
    }
  }
  function setFilters(){
    hotel_search_data.filters.stars = [];
    $('.hotel-stars-filter input[type=checkbox]:checked', $filters_container).each(function(){
      hotel_search_data.filters.stars.push(parseInt(this.value));
    });
    hotel_search_data.filters.facilities = [];
    $('.hotel-facilities-filter input[type=checkbox]:checked', $filters_container).each(function(){
      hotel_search_data.filters.facilities.push(parseInt(this.value));
    });
    hotel_search_data.filters.activity_categories = [];
    hotel_search_data.filters.activities = [];
    $('.hotel-activitycategories-filter input[type=checkbox]:checked', $filters_container).each(function(){
      hotel_search_data.filters.activity_categories.push(parseInt(this.value));
      hotel_search_data.filters.activities = hotel_search_data.filters.activities.concat($(this).attr('data-activities').split(','));
    });
    hotel_search_data.filters.locations = [];
    $('.hotel-locations-filter input[type=checkbox]:checked', $filters_container).each(function(){
      hotel_search_data.filters.locations.push(parseInt(this.value));
    });
    hotel_search_data.filters.pois = [];
    $('.hotel-pois-filter input[type=checkbox]:checked', $filters_container).each(function(){
      hotel_search_data.filters.pois.push(parseInt(this.value));
    });
    var price_values = $price_slider.slider('values');
    hotel_search_data.filters.min_price = parseFloat(price_values[0]);
    hotel_search_data.filters.max_price = parseFloat(price_values[1]);
  }
  function resetFilters(){
    if(!search_is_over){
      console.log('resetFilters','Search is not over, aborting');
      return false;
    }
    $('.hotel-stars-filter input[type=checkbox]:checked', $filters_container).prop('checked',false);
    $('.hotel-facilities-filter input[type=checkbox]:checked', $filters_container).prop('checked',false);
    $('.hotel-activitycategories-filter input[type=checkbox]:checked', $filters_container).prop('checked',false);
    $('.hotel-locations-filter input[type=checkbox]:checked', $filters_container).prop('checked',false);
    $('.hotel-pois-filter input[type=checkbox]:checked', $filters_container).prop('checked',false);
    var min_price = $price_slider.slider('option','min');
    var max_price = $price_slider.slider('option','max');
    
    $price_slider.slider('option',{
      min: min_price,
      max: max_price,
      values: [min_price, max_price],
    });
  }
  var $filters_container = $('#serviceHotelFormFilters');
  $('.hotel-stars-filter', $filters_container).on('change', 'input[type=checkbox]',function(){
    if(!search_is_over){
      console.log('hotel-stars-filter','change','Search is not over, aborting');
      return false;
    }
    setFilters();
    hotel_search_data.page = 1;
    loadResults();
  });
  $('.hotel-facilities-filter, .hotel-activitycategories-filter, .hotel-locations-filter, .hotel-pois-filter', $filters_container).on('change', 'input[type=checkbox]',function(){
    if(!search_is_over){
      console.log('hotel-filters','change','Search is not over, aborting');
      return false;
    }
    setFilters();
    hotel_search_data.page = 1;
    loadResults();
  });
  $('#hotel_reset_filters', $filters_container).click(function(){
    if(!search_is_over){
      console.log('resetFilters','click','Search is not over, aborting');
      return false;
    }
    resetFilters();
    setFilters();
    hotel_search_data.page = 1;
    loadResults();
    var body = $("html, body");
    var pagination_top = $('h1.filterTitle').first().offset().top;
    body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
    });
  });
  $("#hotel-services-search-filter-price-slider-range", $filters_container).on('slidestop', function (event, ui) {
    if(!search_is_over){
      console.log('hotel-price-slider','change','Search is not over, aborting');
      return false;
    }
    $(this).trigger('updatePrice');
    setFilters();
    hotel_search_data.page = 1;
    loadResults();
  });
  $('.hotel-sort-by', $service_hotel_tab).on('change', function(){
    if(!search_is_over){
      console.log('hotel-sort-by','change','Search is not over, aborting');
      return false;
    }
    var $me = $(this);
    if($me.val() === '0'){
      $me.val('1');
    }
    $('.hotel-sort-by', $service_hotel_tab).filter(function(){return !$(this).is($me);}).val(0);
    setSort();
    hotel_search_data.page = 1;
    loadResults();
  });
  function clearFilters(){
    $('.hotel-filter', $filters_container).empty();
  }
  var $filter_checkbox_model = $('#hotel-filter-checkbox-model');
  function loadFilters(){
    if(!search_is_over){
      console.log('loadFilters','Search is not over, aborting');
      return false;
    }
    setSearchStatus(false);
    clearFilters();
    $.ajax({
      url: '<?php echo site_url('trip/hotels/loadFilters'); ?>',
      method: 'post',
      dataType: 'json',
      data: hotel_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('loadFilters', resp);
      if(!resp.status || resp.status !== 'success'){
        showMessage($error_container,'Eroare in preluarea filtrelor', 'danger');
        setSearchStatus(true);
        return;
      }
      var filters = resp.results;
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

      $price_slider.slider('option',{
        min: min_price,
        max: max_price,
        values: [hotel_search_data.filters.min_price, hotel_search_data.filters.max_price],
      });
      $price_slider.trigger('updatePrice');
      
      if(!filters.stars){
        filters.stars = [];
      }
      
      var max_stars = parseInt(filters.stars[filters.stars.length-1]);
      if(max_stars < 5){
        max_stars = 5;
      }
      for(var i=0; i<filters.stars.length; i++){
        var star = parseInt(filters.stars[i]);
        var $checkWrapper = $filter_checkbox_model.clone().removeAttr('id');
        $('.filter-option-input',$checkWrapper).attr('name','stars').val(star).prop('checked', hotel_search_data.filters.stars.indexOf(star)>-1);
        var $checkLabel = $('.filter-option-description', $checkWrapper);
        $checkLabel.append(Array(star+1).join('<i class="fa fa-star"></i>&nbsp;'));
        $checkLabel.append(Array(max_stars-star+1).join('<i class="fa fa-star-o"></i>&nbsp;'));
        $('.hotel-stars-filter', $filters_container).append($checkWrapper);
      }
      if(!filters.facilities){
        filters.facilities = [];
      }
      
      for(var i=0; i<filters.facilities.length; i++){
        var facility = filters.facilities[i];
        var facility_id = parseInt(facility.Id);
        var facility_name = facility.Name;
        var facility_icon = facility.Icon ? facility.Icon : false;
        var facility_icon_src = facility.IconSrc ? facility.IconSrc : false;
        var $checkWrapper = $filter_checkbox_model.clone().removeAttr('id');
        $('.filter-option-input',$checkWrapper).attr('name','facilities').val(facility_id).prop('checked', hotel_search_data.filters.facilities.indexOf(facility_id)>-1);
        var $checkLabel = $('.filter-option-description', $checkWrapper);
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
            $checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(facility_icon);
            $checkLabel.append(icon);
          }
        }
        if(facility_icon_src){
          var icon = $('<img />');
          icon.attr('src',facility_icon_src);
          icon.attr('alt',facility_name);
          $checkLabel.append(icon);
        }
        $checkLabel.append(facility_name);
        $('.hotel-facilities-filter', $filters_container).append($checkWrapper);
      }
      if(!filters.activity_categories){
        filters.activity_categories = [];
      }
      
      for(var i=0; i<filters.activity_categories.length; i++){
        var activity = filters.activity_categories[i];
        var activity_id = parseInt(activity.id);
        var activity_name = activity.name;
        var Icon = activity.icon ? activity.icon : {};
        var activity_icon = Icon.i ? Icon.i : false;
        var activity_icon_src = Icon.Src ? Icon.Src : false;
        var $checkWrapper = $filter_checkbox_model.clone().removeAttr('id');
        $('.filter-option-input',$checkWrapper).attr('name','activity_categories').val(activity_id).attr('data-activities', activity.activity_ids.join(',')).prop('checked', hotel_search_data.filters.activity_categories.indexOf(activity_id)>-1);
        var $checkLabel = $('.filter-option-description', $checkWrapper);
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
            $checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(activity_icon);
            $checkLabel.append(icon);
          }
        }
        if(activity_icon_src){
          var icon = $('<img />');
          icon.attr('src',activity_icon_src);
          icon.attr('alt',activity_name);
          $checkLabel.append(icon);
        }
        $checkLabel.append(activity_name);
        $('.hotel-activitycategories-filter', $filters_container).append($checkWrapper);
      }
      if(!filters.pois){
        filters.pois = [];
      }
      
      for(var i=0; i<filters.pois.length; i++){
        var poi = filters.pois[i];
        var poi_id = parseInt(poi.PoiId);
        var poi_name = poi.Name;
        var poi_icon = poi.Icon ? poi.Icon : false;
        var poi_icon_src = poi.IconSrc ? poi.IconSrc : false;
        
        var $checkWrapper = $filter_checkbox_model.clone().removeAttr('id');
        $('.filter-option-input',$checkWrapper).attr('name','pois').val(poi_id).prop('checked', hotel_search_data.filters.pois.indexOf(poi_id)>-1);
        var $checkLabel = $('.filter-option-description', $checkWrapper);
        
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
            $checkLabel.append(stack);
          } else {
            var icon = $('<i />');
            icon.addClass(poi_icon);
            $checkLabel.append(icon);
          }
        }
        if(poi_icon_src){
          var icon = $('<img />');
          icon.attr('src',poi_icon_src);
          icon.attr('alt',poi_name);
          $checkLabel.append(icon);
        }
        $checkLabel.append(poi_name);
        $('.hotel-pois-filter', $filters_container).append($checkWrapper);
      }
      setFilters();
      $filters_container.show();
      setSearchStatus(true);
      loadResults();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadFilters',jqXHR, textStatus, errorThrown);
      setSearchStatus(true);
    });
  }
  var hotel_search_data = <?php echo json_encode($this->hotel_search_data); ?>, hotel_results;
  function loadResults(initial){
    if(!search_is_over){
      console.log('loadResults','Search is not over, aborting');
      return false;
    }
    setSearchStatus(false);
    $error_container.empty();
    showMessage($error_container,'Se cauta hoteluri <i class="fa fa-spinner fa-spin"></i>','warning');
    $.ajax({
      url: '<?php echo site_url('trip/hotels/loadResults'); ?>',
      method: 'post',
      dataType: 'json',
      data: hotel_search_data
    }).done(function(resp, textStatus, jqXHR){
      setSearchStatus(true);
      $error_container.empty();
      if(!resp.status || resp.status !== 'success' || !resp.results.total_items){
        interpretNoHotelsResponse(resp);
        return;
      }
      hotel_search_data = resp.data;
      hotel_results = resp;
      interpretResults();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadResults',jqXHR, textStatus, errorThrown);
      var resp = {status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR};
      interpretNoHotelsResponse(resp);
    });
  }
  
  function loadRoomPackages(hotel_id){
    if(!search_is_over){
      console.log('loadRoomPackages','Search is not over, aborting');
      return false;
    }
    var citybreak_mode = !$service_hotel_citybreak.is(':checked');
    
    $('#result_serviceHotelFormFellowsForm').empty();
    $room_packages_loading.show();
    var $room_pkg = $room_packages;
    if(citybreak_mode){
      $room_pkg = $service_citybreak_chosen_hotel_packages;
    }
    $room_pkg.empty();
    setSearchStatus(false);
    var data = $.extend(hotel_search_data,{hotel_id:hotel_id});
    if(!citybreak_mode){
      $('#service_hotel_reserve_submit').prop('disabled',false);
    }
    $.ajax({
      url: '<?php echo site_url('trip/hotels/loadRoomPackages'); ?>',
      method: 'post',
      dataType: 'json',
      data: data,
    }).done(function(resp, textStatus, jqXHR){
      console.log('loadRoomPackages',resp);
      setSearchStatus(true);
      $error_container.empty();
      if(!resp.status || resp.status !== 'success'){
        showMessage($error_container,'Eroare in preluarea pachetelor', 'danger');
        return;
      }
      if(citybreak_mode){
        var $service_citybreak_tab = $('a[href="#service_citybreak_tab"]:not(.active)');
        if($service_citybreak_tab.length){
          $service_citybreak_tab[0].click();
        }
      }
      $room_packages_loading.hide();
       
      data = resp.data;
      var inputname = 'package';
      for(var i=0; i<resp.response._embedded.packages.length; i++){
        var package_number = i+1;
        var package = resp.response._embedded.packages[i];
        var $package = $('#hotel-room-packages-model').clone().removeAttr('id');
        $package.appendTo($room_pkg);
        var accordion_id = 'hotel-result-' + hotel_id + '-' + i;
        var $card_header = $('>.card-header', $package);
        $card_header.attr({
          'data-parent': accordion_id,
          'href' : '#' + accordion_id + '_package_' + package_number + '_collapse',
          'aria-controls' : '#' + accordion_id + '_package_' + package_number + '_collapse',
          'id': accordion_id + '_package_' + package_number + '_header'
        }).next('div').attr({
          'id' : accordion_id + '_package_' + package_number + '_collapse',
          'aria-labelledby' :  accordion_id + '_package_' + package_number + '_header'
        });
        var $package_code = $('.package-code-radio', $package);
        $package_code.val(package.PackageCode);
        
        if(!i){
          $package_code.prop('checked',true);
          $card_header.removeClass('collapsed').attr({
            'aria-expanded': 'true'
          }).next('div').addClass('show');
        }
        var $room_container = $('.package-rooms', $package);
        $room_container.attr({
          'id': accordion_id + '_results'
        });
        $('.package-number',$package).text(package_number);
        var package_price = parseFloat(package.Price.Amount);
        $('.package-price',$package).text(Math.ceil(package_price).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
        $('.package-points',$package).text(Math.floor(package_price * 2/100));
        
        var inputname_rooms = 'rooms[' + package.PackageCode + ']';
        for(var j=0; j<package.PackageRooms.PackageRoom.length; j++){
          var room_number = j+1;
          var room = package.PackageRooms.PackageRoom[j];
          var inputname_room = inputname_rooms + '[' + room.PackageRoomCode + ']';
          var room_adults = parseInt(room.Occupancy.Adults);
          var room_children = parseInt(room.Occupancy.Children);
          
          var $room_option = $('#hotel-room-package-room-model').clone().removeAttr('id');
          $room_option.appendTo($room_container);
          
          var accordion_id_room = accordion_id + '_results';
          var $card_header_room = $('>.card-header', $room_option);
          var $card_footer_room = $('>.card-footer', $room_option);
          $card_header_room.attr({
            'data-parent': accordion_id_room,
            'href' : '#' + accordion_id_room + '_room_' + room_number + '_collapse',
            'aria-controls' : '#' + accordion_id_room + '_room_' + room_number + '_collapse',
            'id': accordion_id_room + '_room_' + room_number + '_header'
          }).next('div').attr({
            'id' : accordion_id_room + '_room_' + room_number + '_collapse',
            'aria-labelledby' :  accordion_id_room + '_room_' + room_number + '_header'
          });
          var $room_option_container = $('.package-room-options',$room_option);
          $('.package-room-number', $room_option).text(room_number);
          $('.package-room-occupancy-adults-number', $room_option).text(room_adults);
          if(room_adults == 1){
            $('.package-room-occupancy-adults .plural', $room_option).remove();
          } else {
            $('.package-room-occupancy-adults .singular', $room_option).remove();
          }
          if(room_children){
            $('.package-room-occupancy-children-number', $room_option).text(room_children);
            if(room_children == 1){
              $('.package-room-occupancy-children .plural', $room_option).remove();
            } else {
              $('.package-room-occupancy-children .singular', $room_option).remove();
            }
          } else {
            $('.package-room-occupancy-children-number', $room_option).text(0);
            $('.package-room-occupancy-children .singular', $room_option).remove();
          }
          for(var k=0; k<room.RoomRefs.RoomRef.length; k++){
            var choice_number = k+1;
            var ref = room.RoomRefs.RoomRef[k];
            if(!ref.Price){
              ref.Price = package.Price;
            }
            var $package_room = $('#hotel-room-package-room-option-model').clone().removeAttr('id');
            $package_room.appendTo($room_option_container);
            $('.package-room-option-number', $package_room).text(choice_number);
            $('.package-room-option-name', $package_room).text(ref.Name);
            $('.package-room-option-board', $package_room).text(ref.Board);
            $('.package-room-option-info', $package_room).text(ref.Info);
            var option_price = parseFloat(ref.Price.Amount);
            $('.package-room-option-price', $package_room).text(Math.ceil(option_price).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
            $('.package-room-option-points', $package_room).text(Math.floor(option_price * 2/100));
            $('.package-room-option-choice', $package_room).attr({
              name: inputname_room, 
              value: ref.RoomCode,
              'data-price': option_price,
            }).prop('checked', !k)
            .on('change', function(){
              var $this = $(this);
              if(!$this.is(':checked')){
                return;
              }
              var $option = $this.closest('.hotel-room-package-room-option');
              var $package_room = $option.closest('.hotel-room-package-room');
              var $card_footer_room = $('>.card-footer', $package_room);
              $card_footer_room.empty();
              
              var $option_clone = $option.clone();
              $('.custom-radio',$option_clone).remove();
              $option_clone.removeAttr('style').removeClass('btn btn-secondary');
              $option_clone.appendTo($card_footer_room);
              
              var $package = $(this).closest('.hotel-room-packages');
              var package_price = 0;
              $('input[type=radio]:checked', $package).each(function(){
                var option_price = $(this).data('price');
                if(option_price){
                  package_price += parseFloat(option_price);
                }
              });
              $('.package-price',$package).text(Math.ceil(package_price).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
              $('.package-points',$package).text(Math.floor(package_price * 2/100));
            });
            if(!k){
              var $package_room_clone = $package_room.clone();
              $('.custom-radio',$package_room_clone).remove();
              $package_room_clone.removeAttr('style').removeClass('btn btn-secondary');
              $package_room_clone.appendTo($card_footer_room);
            }
          }
        }
      }
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadRoomPackages',jqXHR, textStatus, errorThrown);
      $error_container.empty();
      $room_packages_loading.hide();
      showMessage($error_container,'Eroare in preluarea pachetelor', 'danger');
      setSearchStatus(true);
    });
  }
  function setHotelSearchAndInitiate(){
    if(!search_is_over){
      console.log('setHotelSearchAndInitiate','Search is not over, aborting');
      return false;
    }
    clearFilters();
    $('.hotel-sort-by', $service_hotel_tab).prop('disabled', true);
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    $('#result_serviceHotelFormFellowsForm').empty();
    $('#serviceHotelResults').empty();
    $('#service_hotel_reserve_submit').prop('disabled',true);
    $('#service_hotel_search_sort_price').val('1');
    $('#service_hotel_search_sort_stars').val('0');
    setSearchStatus(false);
    $error_container.empty();
    $fellow_info_wrapper.hide();
    $hotel_details.empty();
    
    showMessage($error_container,'Se cauta hoteluri <i class="fa fa-spinner fa-spin"></i>','warning');
    
    $.ajax({
      url: '<?php echo site_url('trip/hotels/setSearchAndInitiate'); ?>',
      method: 'post',
      dataType: 'json',
      data: hotel_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('setHotelSearchAndInitiate',resp);
      $error_container.empty();
      if(!resp.status || resp.status !== 'success' || !resp.response.total_items){
        interpretNoHotelsResponse(resp);
        return;
      }
      hotel_search_data = resp.data;
      setSearchStatus(true);
      loadFilters();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('setHotelSearchAndInitiate',jqXHR, textStatus, errorThrown);
      var resp = {status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR};
      interpretNoHotelsResponse(resp);
    });
  }
  function serviceHotelFormSubmitCallback($form,resp,$err_container){
    if(resp.status !== 'success'){
      return true;
    }
    hotel_search_data = resp.data;
    console.log('serviceHotelFormSubmitCallback',hotel_search_data);
    setHotelSearchAndInitiate();
  }
  $('#serviceHotelForm').on('submit',function(){
    if(!search_is_over){
      console.log('serviceHotelForm', 'submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,serviceHotelFormSubmitCallback);
  });
  
  function serviceHotelFormFellowsFormCallback($form,resp,$err_container){
    console.log('serviceHotelFormFellowsFormCallback',resp);
    if(resp.status !== 'success'){
      return true;
    }
    loadOrderServices();
    return true;
  }
  $('#serviceHotelFormFellowsForm').on('submit',function(){
    if(!search_is_over){
      console.log('serviceHotelFormFellowsForm','submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,serviceHotelFormFellowsFormCallback);
  });
  $service_hotel_citybreak.on('change', function(){
    var is_checked = !$(this).is(':checked');
    if(is_checked){
      $room_packages.empty();
      $hotel_details.empty();
    }
    $fellow_info_wrapper.toggleClass('hidden-xl-down', is_checked);
    $('#service_hotel_search_city').prop('readonly', is_checked);
    $('#service_hotel_search_checkin').prop('readonly', is_checked);
    $('#service_hotel_search_checkout').prop('readonly', is_checked);
    $('label[for=service_hotel_search_add_room]').toggle(!is_checked);
    $('#service_hotel_search_rooms_table').toggle(!is_checked);
    if(is_checked){
      $('#service_hotel_search_rooms_table>tbody').empty();
      $('#service_hotel_search_rooms_citybreak_hidden_inputs').appendTo($service_hotel_search_rooms_citybreak);
    } else {
      $('#service_hotel_search_rooms_citybreak_hidden_inputs').appendTo($service_hotel_search_rooms_table_outside_container);
      $('#service_hotel_search_add_room')[0].click();
    }
    $(this).closest('.form-group').toggle(is_checked);
  });
  
  if(hotel_search_data.index_id.length>0){
    loadResults();
    // $service_hotel_citybreak.prop('checked', false).trigger('change');
  }
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  