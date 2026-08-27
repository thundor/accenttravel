<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
(function($){
  var $error_container = $('#result_service_citybreak_form');
  var $service_citybreak_search_passenger_birthdates_table_tbody = $('#service_citybreak_search_passenger_birthdates_table_tbody');
  var $service_citybreak_search_add_passenger_room = $('#service_citybreak_search_add_passenger_room');
  var $service_citybreak_search_hotel_room_info = $('#service_citybreak_search_hotel_room_info');
  var $service_citybreak_form = $('#service_citybreak_form');
  var $service_citybreak_search_return = $('#service_citybreak_search_return');
  var $service_citybreak_search_return_date = $('#service_citybreak_search_return_date');
  var $service_citybreak_search_departure_date = $('#service_citybreak_search_departure_date');
  var $service_citybreak_form_fellows = $('#service_citybreak_form_fellows');
  var $service_citybreak_form_fellows_form = $('#service_citybreak_form_fellows_form');
  var $service_citybreak_search_origin = $('#service_citybreak_search_origin');
  $('#service_citybreak_search_origin_location, #service_citybreak_search_destination_location').on('change', function(){
    var $this = $(this);
    var prefix = $this.attr('id').indexOf('destination') > -1 ? 'destination' : 'origin';
    var $selected_option = $('>option:selected', $this);
    $('#service_citybreak_search_' + prefix  + '_' + 'location_id').val($selected_option.data('location_id'));
    $('#service_citybreak_search_' + prefix  + '_' + 'location_name').val($selected_option.data('location_name'));
    $('#service_citybreak_search_' + prefix  + '_' + 'city_id').val($selected_option.data('city_id'));
    $('#service_citybreak_search_' + prefix  + '_' + 'city_name').val($selected_option.data('city_name'));
    $('#service_citybreak_search_' + prefix  + '_' + 'country_id').val($selected_option.data('country_id'));
    $('#service_citybreak_search_' + prefix  + '_' + 'country_name').val($selected_option.data('country_name'));
    if(!this.value || this.value===''){
      var full_location_name = '';
    } else {
      var full_location_name = (parseInt($selected_option.data('location_id'))>0 ? $selected_option.data('location_name') + ', ' : '') + $selected_option.data('city_name');
    }
    $('#service_citybreak_search_' + prefix  + ', #service_citybreak_search_' + prefix  + '_' + 'full_location_name').val(full_location_name);
  });
  var done_recalculating = true;
  var room_occupancy = [];
  var room_occupancy_persons = [];
  function recalculatePassengerBirthdateClasses(){
    if(!done_recalculating){
      console.log('not done recalculating');
      return false;
    }
    setSearchStatus(false);
    room_occupancy = [];
    room_occupancy_persons = [];
    done_recalculating = false;
    console.log('recalculatePassengerBirthdateClasses');
    var service_citybreak_search_departure_date = $('#service_citybreak_search_departure_date').val();
    if(service_citybreak_search_departure_date && service_citybreak_search_departure_date !== ''){
      var reference_moment = moment(service_citybreak_search_departure_date,'DD.MM.Y').startOf('day');
    } else {
      var reference_moment = moment().startOf('day');
    }
    var $birth_dates = $('input.passenger-birth-date', $service_citybreak_search_passenger_birthdates_table_tbody);
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
    var room_count = 0;
    $('input.passenger-age', $service_citybreak_search_passenger_birthdates_table_tbody).each(function(){
      var age_in_years = this.value;
      var $tr = $(this).closest('tr');
      if($tr.hasClass('citybreak-passenger-room')){
        var occupancy = {
          'adults': 0,
          'child_ages': [],
          'child_birth_dates': []
        };
        room_occupancy.push(occupancy);
        room_count++;
      }
      if(isNaN(age_in_years) || age_in_years === '' || age_in_years > 150){
        return;
      }
      var room_index = room_count-1;
      if(age_in_years >= 18){
        room_occupancy[room_index].adults++;
        adult_passengers++;
      } else {
        var birth_date = $('.passenger-birth-date',$tr).val();
        room_occupancy[room_index].child_ages.push(age_in_years);
        room_occupancy[room_index].child_birth_dates.push(birth_date);
      }
    });
    var remaining_adults = adult_passengers;
    console.log('start',remaining_adults);
    var infant_counter = 0;
    
    var passenger_adult = 0;
    var passenger_senior = 0;
    var passenger_child = 0;
    var passenger_youth = 0;
    var passenger_infant_lap = 0;
    var passenger_infant_seat = 0;
    
    var room_count = 0;
    $('input.passenger-age', $service_citybreak_search_passenger_birthdates_table_tbody).each(function(){
      var age_in_years = this.value;
      var $tr = $(this).closest('tr');
      if($tr.hasClass('citybreak-passenger-room')){
        room_occupancy_persons.push([]);
        room_count++;
      }
      if(isNaN(age_in_years) || age_in_years === '' || age_in_years > 150){
        $('.passenger-type-indeterminate',$tr).show();
        return;
      }
      var room_index = room_count-1;
      
      var birth_date = $('.passenger-birth-date',$tr).val();
      var person = {
        birth_date: birth_date,
        type: '',
        age: age_in_years
      };
      
      $('.passenger-type-indeterminate',$tr).hide();
      if(age_in_years > 60){
        $('.passenger-type-senior', $tr).show();
        passenger_senior++;
        person.type = 'sen';
        room_occupancy_persons[room_index].push(person);
        return;
      } else if(age_in_years >= 18){
        passenger_adult++;
        $('.passenger-type-adult', $tr).show();
        person.type = 'adt';
        room_occupancy_persons[room_index].push(person);
        return;
      } else if(age_in_years >= 3){
        passenger_child++;
        $('.passenger-type-child', $tr).show();
        person.type = 'chd';
        room_occupancy_persons[room_index].push(person);
        return;
      }
      
      $('.passenger-type-infant-seat, .passenger-type-infant-lap, .passenger-type-infant-changed, .passenger-type-infant-selector, .passenger-type-infant-change, .passenger-type-infant-lack-adults', $tr).hide();
      $('.passenger-type-infant', $tr).show();
      infant_counter ++;
      if(infant_counter > 2*adult_passengers){
        $('.passenger-type-infant-lack-adults', $tr).show();
      }
      if(!remaining_adults){
        passenger_infant_seat++;
        console.log('check seat',remaining_adults);
        $('.passenger-type-infant-selector', $tr).val(null);
        $('.passenger-type-infant-seat', $tr).show();
        person.type = 'ins';
      } else {
        console.log('check lap',remaining_adults);
        var forced_type = $('.passenger-type-infant-selector', $tr).val();
        console.log($('.passenger-type-infant-selector', $tr));
        if(forced_type && forced_type !== ''){
          if(forced_type == 'seat'){
            $('.passenger-type-infant-seat', $tr).show();
            passenger_infant_seat++;
            person.type = 'ins';
          } else if(forced_type == 'lap'){
            $('.passenger-type-infant-lap', $tr).show();
            passenger_infant_lap++;
            remaining_adults--;
            person.type = 'inf';
          }
          $('.passenger-type-infant-changed', $tr).show();
          console.log('should show');
        } else {
          $('.passenger-type-infant-lap', $tr).show();
          remaining_adults--;
          passenger_infant_lap++;
          person.type = 'inf';
        }
        $('.passenger-type-infant-change', $tr).show();
      }
      room_occupancy_persons[room_index].push(person);
    });
    
    $('#service_citybreak_search_room_count').text(room_count);
    $('#service_citybreak_search_passengers_adult').val(passenger_adult);
    $('#service_citybreak_search_adult_count').text(passenger_adult);
    $('#service_citybreak_search_passengers_senior').val(passenger_senior);
    $('#service_citybreak_search_senior_count').text(passenger_senior);
    $('#service_citybreak_search_passengers_child').val(passenger_child);
    $('#service_citybreak_search_child_count').text(passenger_child);
    $('#service_citybreak_search_passengers_infant_lap').val(passenger_infant_lap);
    $('#service_citybreak_search_infant_lap_count').text(passenger_infant_lap);
    $('#service_citybreak_search_passengers_infant_seat').val(passenger_infant_seat);
    $('#service_citybreak_search_infant_seat_count').text(passenger_infant_seat);
    
    done_recalculating = true;
    setSearchStatus(true);
  }
  $service_citybreak_search_add_passenger_room.on('click', function(){
    console.log('adding-room');
    var $citybreak_passenger_room = $('#citybreak_passenger_room_model').clone().removeAttr('id');
    var $citybreak_passenger_birthdate = $('#citybreak_passenger_birthdate_model').clone().removeAttr('id');
    // $('input.passenger-age', $citybreak_passenger_birthdate).val(18);
    $citybreak_passenger_birthdate.children().insertAfter($('td:first-child', $citybreak_passenger_room));
    $citybreak_passenger_room.appendTo($service_citybreak_search_passenger_birthdates_table_tbody);
    var reference_moment = moment().startOf('day');
    var min_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_moment = reference_moment;
    var start_moment = moment(reference_moment).add(-18,'years');
    $('input.passenger-birth-date', $citybreak_passenger_room).makeCaleranDatepicker({
      minDate: min_moment,
      maxDate: max_moment,
      startDate: start_moment,
      startEmpty: false
    }).makeInputmaskDate();
    recalculatePassengerBirthdateClasses();
  });
  $service_citybreak_search_passenger_birthdates_table_tbody.on('click', '.btn-add-passenger-birthdate', function(){
    console.log('adding-birthdate');
    var $tr = $(this).closest('tr');
    var $first_td = $('td:first-child', $tr);
    var $last_td = $('td:last-child', $tr);
    var rowspan = parseInt($first_td.attr('rowspan'));
    $first_td.attr({
      'rowspan' : rowspan + 1
    });
    $last_td.attr({
      'rowspan' : rowspan + 1
    });
    var $citybreak_passenger_birthdate = $('#citybreak_passenger_birthdate_model').clone().removeAttr('id');
    $citybreak_passenger_birthdate.insertAfter($tr);
    var reference_moment = moment().startOf('day');
    var min_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_moment = reference_moment;
    $('input.passenger-birth-date', $citybreak_passenger_birthdate).makeCaleranDatepicker({
      minDate: min_moment,
      maxDate: max_moment,
      startDate: max_moment,
      startEmpty: false
    }).makeInputmaskDate();
    recalculatePassengerBirthdateClasses();
  }).on('click', '.btn-delete-passenger-birthdate', function(){
    console.log('removing-birthdate');
    var $tr = $(this).closest('tr');
    if($tr.hasClass('citybreak-passenger-room')){
      $('input', $tr).val(null);
      recalculatePassengerBirthdateClasses();
      return;
    }
    $parent_tr = $tr.prevAll('.citybreak-passenger-room:first').first();
    console.log($parent_tr);
    var $first_td = $('td:first-child', $parent_tr);
    var $last_td = $('td:last-child', $parent_tr);
    var rowspan = parseInt($first_td.attr('rowspan'));
    $first_td.attr({
      'rowspan' : rowspan - 1
    });
    $last_td.attr({
      'rowspan' : rowspan - 1
    });
    $tr.remove();
    recalculatePassengerBirthdateClasses();
  }).on('click', '.btn-delete-passenger-room', function(){
    var $tr = $(this).closest('tr');
    $tr.nextUntil('.citybreak-passenger-room','.citybreak-passenger-birthdate').remove();
    $tr.remove();
    if($service_citybreak_search_passenger_birthdates_table_tbody.children().length == 0){
      $service_citybreak_search_add_passenger_room[0].click();
    }
    recalculatePassengerBirthdateClasses();
  }).on('change', 'input.passenger-birth-date', function(){
    recalculatePassengerBirthdateClasses();
  }).on('input change', 'input.passenger-age', function(){
    var age = this.value;
    var new_val = null;
    
    var $birth_date = $('input.passenger-birth-date', $(this).closest('tr'));
    if(age !== ''){
      age = parseInt(age);
      if(age<=150){
        var today = moment().startOf('day')
        var birth_date = $birth_date.val();
        if(!birth_date || birth_date === ''){
          birth_date = moment(today).add(-1 * age,'years');
          new_val = birth_date.format('DD.MM.Y');
        } else {
          var service_citybreak_search_departure_date = $('#service_citybreak_search_departure_date').val();
          if(service_citybreak_search_departure_date && service_citybreak_search_departure_date !== ''){
            var reference_moment = moment(service_citybreak_search_departure_date,'DD.MM.Y').startOf('day');
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
  }).on('click', '.btn-passenger-type-infant-change', function(){
    $(this).closest('.passenger-type-infant-detail').hide().next('select').show().focus();
  }).on('blur', 'select.passenger-type-infant-selector', function(){
    $(this).hide().prev('.passenger-type-infant-detail').show();
  }).on('change', 'select.passenger-type-infant-selector', function(){
    $(this).hide().prev('.passenger-type-infant-detail').show();
    recalculatePassengerBirthdateClasses();
  });
  $service_citybreak_search_add_passenger_room[0].click();
  
  
  var submitted_button = null;
  var submit_button = null;
  $('button[type=submit][name]', $service_citybreak_form).on('click', function(){
    $('#service_citybreak_search_search_type').val(this.name);
    submit_button = this.name;
  });
  var search_is_over = true;  
  function setSearchStatus(search_status){
    $('#service_citybreak_reserve_submit').prop('disabled',!search_status);
    $('button[type=submit][name]:not(.disabled)', $service_citybreak_form).prop('disabled', !search_status);
    search_is_over = search_status;
  }
  var citybreak_search_data;
  function serviceCitybreakFormSubmitCallback($form,resp,$err_container){
    setSearchStatus(true);
    if(resp.status !== 'success'){
      return true;
    }
    citybreak_search_data = resp.data;
    console.log('serviceCitybreakFormSubmitCallback',submitted_button, citybreak_search_data);
    if(submitted_button == 'hotel'){
      $('#service_hotel_citybreak').prop('checked', false).trigger('change');
      $('#service_hotel_search_city_id').val(citybreak_search_data.destination_city_id);
      $('#service_hotel_search_city_name').val(citybreak_search_data.destination_city_name);
      $('#service_hotel_search_city').val(citybreak_search_data.destination_city_name);
      $('#service_hotel_search_country_id').val(citybreak_search_data.destination_country_id);
      $('#service_hotel_search_country_name').val(citybreak_search_data.destination_country_name);
      $('#service_hotel_search_checkin').val(moment(citybreak_search_data.start_date,'Y-MM-DD').format('DD.MM.Y'));
      $('#service_hotel_search_checkout').val(moment(citybreak_search_data.end_date,'Y-MM-DD').format('DD.MM.Y'));
      $('#service_hotel_search_rooms_citybreak_hidden_inputs').empty();
      $('#service_hotel_search_rooms_citybreak_hidden_inputs').append($('#service_citybreak_search_hotel_room_info').clone().removeAttr('id'));
      
      var $service_hotel_tab = $('a[href="#service_hotel_tab"]:not(.active)');
      if($service_hotel_tab.length){
        $service_hotel_tab[0].click();
      }
      $('#service_hotel_search_submit')[0].click();
      var $search_container_header = $('a[href="#service_hotel_form_container"].collapsed');
      if($search_container_header.length){
        $search_container_header[0].click();
      }
      
    } else if(submitted_button == 'flight'){
      $('#service_flight_citybreak').prop('checked', false).trigger('change');
      
      $('#service_flight_search_passenger_birthdates_table > tbody').empty();
      $('#service_flight_search_passenger_birthdates_table > tfoot').hide();
      
      $('#service_flight_search_origin_location_id').val(citybreak_search_data.origin_location_id);
      $('#service_flight_search_origin_location_name').val(citybreak_search_data.origin_location_name);
      $('#service_flight_search_origin_full_location_name').val(citybreak_search_data.origin_full_location_name);
      $('#service_flight_search_origin_city_id').val(citybreak_search_data.origin_city_id);
      $('#service_flight_search_origin_city_name').val(citybreak_search_data.origin_city_name);
      $('#service_flight_search_origin_country_id').val(citybreak_search_data.origin_country_id);
      $('#service_flight_search_origin_country_name').val(citybreak_search_data.origin_country_name);
      $('#service_flight_search_origin').val(citybreak_search_data.origin_full_location_name);
      
      $('#service_flight_search_destination_location_id').val(citybreak_search_data.destination_location_id);
      $('#service_flight_search_destination_location_name').val(citybreak_search_data.destination_location_name);
      $('#service_flight_search_destination_full_location_name').val(citybreak_search_data.destination_full_location_name);
      $('#service_flight_search_destination_city_id').val(citybreak_search_data.destination_city_id);
      $('#service_flight_search_destination_city_name').val(citybreak_search_data.destination_city_name);
      $('#service_flight_search_destination_country_id').val(citybreak_search_data.destination_country_id);
      $('#service_flight_search_destination_country_name').val(citybreak_search_data.destination_country_name);
      $('#service_flight_search_destination').val(citybreak_search_data.destination_full_location_name);
      

      $('#service_flight_search_return').prop('checked',!citybreak_search_data.go_only);
      
      $('#service_flight_search_direct_only').prop('checked',citybreak_search_data.direct_only);
      $('#service_flight_search_flex_dates').prop('checked',citybreak_search_data.flex_dates);
      $('#service_flight_search_flexible_dates').prop('checked',citybreak_search_data.flex_dates);
      
      $('#service_flight_search_departure_date').val(moment(citybreak_search_data.departure_date,'Y-MM-DD').format('DD.MM.Y'));
      $('#service_flight_search_return_date').val(moment(citybreak_search_data.return_date,'Y-MM-DD').format('DD.MM.Y'));
      $('#service_flight_search_return_date').prop('disabled',citybreak_search_data.go_only);
      $('#service_flight_search_cabine_type').val(citybreak_search_data.cabine_type);
      
      $('#service_flight_search_passengers_adult').val(citybreak_search_data.passengers_adult);
      $('#service_flight_search_passengers_senior').val(citybreak_search_data.passengers_senior);
      $('#service_flight_search_passengers_child').val(citybreak_search_data.passengers_child);
      $('#service_flight_search_passengers_infant_lap').val(citybreak_search_data.passengers_infant_lap);
      $('#service_flight_search_passengers_infant_seat').val(citybreak_search_data.passengers_infant_seat);
      $('#service_flight_search_passengers_infant').val(citybreak_search_data.passengers_infant_seat + citybreak_search_data.passengers_infant_lap);
      
      $('#service_flight_search_passengers_infant').prop('checked', false);
      $('#service_flight_search_passengers_infant_toggle').prop('checked', false);
      
      var $service_flight_tab = $('a[href="#service_flight_tab"]:not(.active)');
      if($service_flight_tab.length){
        $service_flight_tab[0].click();
      }
      $('#service_flight_search_submit')[0].click();
      var $search_container_header = $('a[href="#service_flight_form_container"].collapsed');
      if($search_container_header.length){
        $search_container_header[0].click();
      }
      
    }
  }
  $service_citybreak_form.on('submit',function(){
    if(!done_recalculating){
      console.log('service_citybreak_form', 'submit','Calculating is not over, aborting');
      return false;
    }
    if(!search_is_over){
      console.log('service_citybreak_form','submit','Search is not over, aborting');
      return false;
    }
    if(!submit_button){
      console.log('service_citybreak_form','submit','No submit button, aborting');
      return false;
    }
    setSearchStatus(false);
    
    $('#service_citybreak_search_start_date, #service_citybreak_search_end_date').prop('required', submit_button == 'hotel');
    
    submitted_button = '' + submit_button;
    submit_button = null;
    $service_citybreak_search_hotel_room_info.empty();
        
    for(var i=0; i<room_occupancy_persons.length; i++){
      var room_persons = room_occupancy_persons[i];
      for(var j=0; j<room_persons.length; j++){
        var room_person = room_persons[j];
        var $input = $('<input type="hidden" name="persons[' + i + '][' + j + '][type]">');
        $input.val(room_person.type);
        $input.appendTo($service_citybreak_search_hotel_room_info);
        var $input = $('<input type="hidden" name="persons[' + i + '][' + j + '][birth_date]">');
        $input.val(room_person.birth_date);
        $input.appendTo($service_citybreak_search_hotel_room_info);
        var $input = $('<input type="hidden" name="persons[' + i + '][' + j + '][age]">');
        $input.val(room_person.age);
        $input.appendTo($service_citybreak_search_hotel_room_info);
      }
    }
    for(var i=0; i<room_occupancy.length; i++){
      var occupancy = room_occupancy[i];
      var $input = $('<input type="hidden" name="occupancy[' + i + '][adt]">');
      $input.val(occupancy.adults);
      $input.appendTo($service_citybreak_search_hotel_room_info);
      for(var j=0; j<occupancy.child_ages.length; j++){
        var child_age = occupancy.child_ages[j];
        var $input = $('<input type="hidden" name="occupancy[' + i + '][chd][age][]">');
        $input.val(child_age);
        $input.appendTo($service_citybreak_search_hotel_room_info);
      }
      for(var j=0; j<occupancy.child_birth_dates.length; j++){
        var child_birth_date = occupancy.child_birth_dates[j];
        var $input = $('<input type="hidden" name="occupancy[' + i + '][chd][birth_date][]">');
        $input.val(child_birth_date);
        $input.appendTo($service_citybreak_search_hotel_room_info);
      }
    }
    
    var service_citybreak_search_departure_date = $service_citybreak_search_departure_date.val();
    if(!service_citybreak_search_departure_date){
      return true;
    }
    var service_citybreak_search_return_date = $service_citybreak_search_return_date.val();
    if(!service_citybreak_search_return_date){
      return true;
    }
    var start_date = moment(service_citybreak_search_departure_date,'DD.MM.Y');
    var end_date = moment(service_citybreak_search_return_date,'DD.MM.Y');
    if(end_date && start_date){
      if(end_date.isBefore(start_date)){
        $service_citybreak_search_departure_date.val(service_citybreak_search_return_date);
        $service_citybreak_search_return_date.val(service_citybreak_search_departure_date);
      }
    }
    
    basicFormPostSubmit(this,this.action,serviceCitybreakFormSubmitCallback);
  });
  function loadPassengerDetails(secured){
    $service_citybreak_form_fellows.empty();
    var $flight_passenger_model = $('#flight_passenger_model').clone().removeAttr('id');
    if(!secured){
      $('.flight-passenger-info-secured', $flight_passenger_model).remove();
    }
    var reference_moment = moment(citybreak_search_data.departure_date,'Y-MM-DD').startOf('day');
    var min_senior_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_senior_moment = moment(reference_moment).add(-61,'years').startOf('day');
    var min_adult_moment = moment(max_senior_moment).add(1,'day');
    var max_adult_moment = moment(reference_moment).add(-18,'years').startOf('day');
    var min_child_moment = moment(max_adult_moment).add(1,'day');
    var max_child_moment = moment(reference_moment).add(-3,'years');
    var min_infant_moment = moment(max_child_moment).add(1,'day');
    var max_infant_moment = moment().startOf('day');
    
    for(var i=0;i<citybreak_search_data.persons.length; i++){
      var room_number = i+1;
      var room_adults = 0;
      var room_children = 0;
      var room_persons = citybreak_search_data.persons[i];
      var $hotel_room_fellows = $('#hotel-room-fellows-model').clone().removeAttr('id').hide();
      $hotel_room_fellows.appendTo($service_citybreak_form_fellows);
      if(i>0){
        $hotel_room_fellows.addClass('mt-3');
      }
      var $hotel_room_fellow_container = $('.room-occupancy-fellows', $hotel_room_fellows);
      var occupant_numbers = {
        'adt': 0,
        'sen': 0,
        'chd': 0,
        'inf': 0,
        'ins': 0,
      };
      for(var j=0; j<room_persons.length; j++){
        var room_person = room_persons[j];
        var occupant_index = j+1;
        occupant_numbers[room_person.type]++;
        var occupant_number = occupant_numbers[room_person.type];
        var $flight_passenger;
        if(room_person.type == 'sen'){
          room_adults++;
          var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-senior');
          $('.passenger-type:not(.passenger-type-senior)', $flight_passenger).remove();
          $('.passenger-info-field', $flight_passenger).each(function(){
            $(this).attr({
              'name': 'room_passenger[' + i + '][' + j + '][SEN][' + $(this).attr('name') + ']'
            });
            $flight_passenger.appendTo($hotel_room_fellow_container);
          });
          $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
            minDate: min_senior_moment,
            maxDate: max_senior_moment,
            startDate: max_senior_moment,
            startEmpty: false
          }).makeInputmaskDate();
          $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
        } else if(room_person.type == 'adt'){
          room_adults++;
          var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-adult');
          $('.passenger-type:not(.passenger-type-adult)', $flight_passenger).remove();
          $('.passenger-info-field', $flight_passenger).each(function(){
            $(this).attr({
              'name': 'room_passenger[' + i + '][' + j + '][ADT][' + $(this).attr('name') + ']'
            });
          });
          $flight_passenger.appendTo($hotel_room_fellow_container);
          $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
            minDate: min_adult_moment,
            maxDate: max_adult_moment,
            startDate: max_adult_moment,
            startEmpty: false
          }).makeInputmaskDate();
          $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
        } else if(room_person.type == 'chd'){
          room_children++;
          var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-child');
          $('.flight-passenger-info-adult', $flight_passenger).remove();
          $('.passenger-type:not(.passenger-type-child)', $flight_passenger).remove();
          $('.passenger-info-field', $flight_passenger).each(function(){
            $(this).attr({
              'name': 'room_passenger[' + i + '][' + j + '][CHD][' + $(this).attr('name') + ']'
            });
          });
          $flight_passenger.appendTo($hotel_room_fellow_container);
          $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
            minDate: min_child_moment,
            maxDate: max_child_moment,
            startDate: max_child_moment,
            startEmpty: false
          }).makeInputmaskDate();
          $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
        } else if(room_person.type == 'inf'){
          room_children++;
          var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-infant');
          $('.flight-passenger-info-adult', $flight_passenger).remove();
          $('.passenger-type:not(.passenger-type-infant)', $flight_passenger).remove();
          $('.passenger-type-infant-seat', $flight_passenger).remove();
          $('.passenger-info-field', $flight_passenger).each(function(){
            $(this).attr({
              'name': 'room_passenger[' + i + '][' + j + '][INF][' + $(this).attr('name') + ']'
            });
          });
          $flight_passenger.appendTo($hotel_room_fellow_container);
          $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
            minDate: min_infant_moment,
            maxDate: max_infant_moment,
            startDate: max_infant_moment,
            startEmpty: false
          }).makeInputmaskDate();
          $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
        } else if(room_person.type == 'ins'){
          room_children++;
          var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-infant');
          $('.flight-passenger-info-adult', $flight_passenger).remove();
          $('.passenger-type:not(.passenger-type-infant)', $flight_passenger).remove();
          $('.passenger-type-infant-lap', $flight_passenger).remove();
          $('.passenger-info-field', $flight_passenger).each(function(){
            $(this).attr({
              'name': 'room_passenger[' + i + '][' + j + '][INS][' + $(this).attr('name') + ']'
            });
          });
          $flight_passenger.appendTo($hotel_room_fellow_container);
          $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
            minDate: min_infant_moment,
            maxDate: max_infant_moment,
            startDate: max_infant_moment,
            startEmpty: false
          }).makeInputmaskDate();
          $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
        }
        
        if(!$flight_passenger){
          return;
        }
        if(room_person.birth_date && room_person.birth_date!==''){
          $('.passenger-birth_date',$flight_passenger).val(room_person.birth_date);
        }
        $('.occupant-index-container', $flight_passenger).show();
        $('.occupant-index', $flight_passenger).text(occupant_index);
        $('.passenger-index', $flight_passenger).text(occupant_number);
        $('>.card-header', $flight_passenger).removeClass('bg-inverse').addClass('bg-primary');
      }
      
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
    
    /* if(citybreak_search_data.passengers_senior){
      for(var i=0; i< citybreak_search_data.passengers_senior; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-senior');
        $('.passenger-type:not(.passenger-type-senior)', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(i+1);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'room_passenger[SEN][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_citybreak_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_senior_moment,
          maxDate: max_senior_moment,
          startDate: max_senior_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
      }
    }
    if(citybreak_search_data.passengers_adult){
      for(var i=0; i< citybreak_search_data.passengers_adult; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-adult');
        $('.passenger-type:not(.passenger-type-adult)', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(i+1);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'room_passenger[ADT][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_citybreak_form_fellows);
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
    if(citybreak_search_data.passengers_child){
      for(var i=0; i< citybreak_search_data.passengers_child; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-child');
        $('.passenger-type:not(.passenger-type-child)', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(i+1);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'room_passenger[SEN][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_citybreak_form_fellows);
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
    if(citybreak_search_data.passengers_infant_lap){
      for(var i=0; i < citybreak_search_data.passengers_infant_lap; i++){
        infant_index++;
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-infant');
        $('.passenger-type:not(.passenger-type-infant)', $flight_passenger).remove();
        $('.passenger-type-infant-seat', $flight_passenger).remove();
        $('.passenger-index', $flight_passenger).text(infant_index);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'room_passenger[INF][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_citybreak_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_infant_moment,
          maxDate: max_infant_moment,
          startDate: max_infant_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
      }
    }
    if(citybreak_search_data.passengers_infant_seat){
      for(var i=0; i < citybreak_search_data.passengers_infant_seat; i++){
        var $flight_passenger = $flight_passenger_model.clone().addClass('flight-passenger-infant');
        $('.passenger-type:not(.passenger-type-infant)', $flight_passenger).remove();
        $('.passenger-type-infant-lap', $flight_passenger).remove();
        infant_index++;
        $('.passenger-index', $flight_passenger).text(infant_index);
        $('.passenger-info-field', $flight_passenger).each(function(){
          $(this).attr({
            'name': 'room_passenger[INS][' + i + '][' + $(this).attr('name') + ']'
          });
        });
        $flight_passenger.appendTo($service_citybreak_form_fellows);
        $('.passenger-birth_date',$flight_passenger).makeCaleranDatepicker({
          minDate: min_infant_moment,
          maxDate: max_infant_moment,
          startDate: max_infant_moment,
          startEmpty: false
        }).makeInputmaskDate();
        $('.passenger-title', $flight_passenger).select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_children_titles_prefix_selections, width: '100%'});
      }
    } */
    if(secured){
      $('.passenger-passport_issuing_country', $service_flight_form_fellows).select2_4({language:'ro',theme:'bootstrap',placeholder:'Tara emitere', data: select2_countries_selections, width: '100%'});
      $('.passenger-passport_nationality', $service_flight_form_fellows).select2_4({language:'ro',theme:'bootstrap',placeholder:'Nationalitate', data: select2_countries_selections, width: '100%'});
    }
    
    $('.passenger-lastname', $service_citybreak_form_fellows).each(function(i){
      this.value = $('#client_lastname').val() + ' ' + i;
    });
    $('.passenger-firstname', $service_citybreak_form_fellows).each(function(i){
      this.value = $('#client_firstname').val() + ' ' + i;
    });
    $('.passenger-email', $service_citybreak_form_fellows).each(function(i){
      this.value = $('#client_email').val();
    });
    var client_phone = $('#client_phone').val();
    var client_phone_prefix_country = $('#client_phone_prefix').val();
    if(client_phone_prefix_country && typeof(countries_selections[client_phone_prefix_country]) !== 'undefined' && countries_selections[client_phone_prefix_country].prefix){
      client_phone = '+' + countries_selections[client_phone_prefix_country].prefix + ' ' + client_phone;
    }
    $('.passenger-phone', $service_citybreak_form_fellows).each(function(i){
      this.value = client_phone;
    });
    console.log('loadPassengerDetails', secured);
  }
  var today_moment = moment().startOf('day');
  var tomorrow_moment = moment(today_moment).add(1, 'days');
  $('#service_citybreak_search_end_date').makeCaleranDatepicker({
    startEmpty: true,
    minDate: tomorrow_moment,
    endDate: tomorrow_moment,
    startDate: tomorrow_moment
  }).makeInputmaskDate();
  $('#service_citybreak_search_start_date').makeCaleranDatepicker({
    startEmpty: true,
    minDate: today_moment,
    startDate: today_moment
  }).makeInputmaskDate().on('change', function(){
    var val_moment = moment(this.value, 'DD.MM.Y');
    if(!val_moment.isValid()){
      return;
    }
    var val_tomorrow_moment = val_moment.add(1,'day');
    var $checkout_caleran = $('#service_citybreak_search_end_date').data("caleran");
    $checkout_caleran.config.minDate = val_tomorrow_moment;
    var checkout_val = $('#service_citybreak_search_end_date').val();
    var checkout_val_moment = moment(checkout_val, 'DD.MM.Y');
    if(!checkout_val_moment.isValid() || checkout_val_moment.isBefore(val_tomorrow_moment)){
      $checkout_caleran.config.startDate = val_tomorrow_moment;
      $checkout_caleran.config.endDate = val_tomorrow_moment;
      checkout_val_moment = val_tomorrow_moment;
      checkout_val = checkout_val_moment.format('DD.MM.Y');
      $('#service_citybreak_search_end_date').val(checkout_val);
      $('#service_citybreak_search_end_date').focus();
    }
  });
  $('#service_citybreak_chosen_flight').on('updated-flight', function(){
    $('#service_hotel_citybreak').prop('checked', true).trigger('change');
    var flight_secured = $(this).data('secured');
    var start_date;
    var end_date;
    if(!$('#service_citybreak_chosen_hotel').is(':empty')){
      showMessage($error_container,'Pentru adaugarea unei rezervari de hotel realizati o noua cautare de hoteluri.', 'danger');
    }
    $('.flight-result-route', this).each(function(){
      var $this = $(this);
      var flight_type = $this.data('flight_type');
      var departure_arrival_date = $this.data('departure_arrival_date');
      var departure_arrival_time = $this.data('departure_arrival_time');
      if(flight_type == 0){
        start_date = moment(departure_arrival_date,'Y-MM-DD');
      } else {
        end_date = moment(departure_arrival_date,'Y-MM-DD');
      }
    });
    
    $('#service_citybreak_search_start_date').val(start_date.format('DD.MM.Y'));
    var $checkin_caleran = $('#service_citybreak_search_start_date').data("caleran");
    var $checkout_caleran = $('#service_citybreak_search_end_date').data("caleran");
    $checkin_caleran.config.startEmpty = false;
    $checkin_caleran.config.minDate = start_date;
    $checkin_caleran.config.startDate = start_date;
    $checkin_caleran.config.endDate = start_date;
    var min_end_date = moment(start_date).add(1,'days');
    if(end_date){
      $checkin_caleran.config.maxDate = moment(end_date).add(-1,'days');
      $checkout_caleran.config.maxDate = end_date;
      $('#service_citybreak_search_end_date').val(end_date.format('DD.MM.Y'));
    } else {
      end_date = min_end_date;
      $checkout_caleran.config.maxDate = null;
      $('#service_citybreak_search_end_date').val(end_date.format('DD.MM.Y'));
    }
    $checkout_caleran.config.minDate = min_end_date;
    $checkout_caleran.config.startEmpty = false;
    $checkout_caleran.config.startDate = end_date;
    $checkout_caleran.config.endDate = end_date;
    
    $('#service_citybreak_search_start_date, #service_citybreak_search_end_date').prop('readonly', false).closest('.form-group').addClass('has-warning');
    
    loadPassengerDetails(flight_secured);
    $('#service_citybreak_chosen_hotel').empty();
    $('#service_citybreak_chosen_hotel_packages').empty();
    $('#service_citybreak_search_submit_hotel').prop('disabled', false).removeClass('disabled').removeAttr('title');
  });
  $service_citybreak_search_return.change(function(){
    var is_checked = $(this).is(':checked');
    $service_citybreak_search_return_date.prop('disabled', !is_checked).prop('required', is_checked);
    $service_citybreak_search_return_date.closest('.input-group').toggleClass('has-danger', is_checked);
    if(is_checked){
      $service_citybreak_search_return_date.select();
    }
  });
  var today_moment = moment().startOf('day');
  var tomorrow_moment = moment(today_moment).add(1, 'days');
  $('#service_citybreak_search_return_date').makeCaleranDatepicker({
    startEmpty: false,
    minDate: tomorrow_moment,
    endDate: tomorrow_moment,
    startDate: tomorrow_moment
  }).makeInputmaskDate();
  $('#service_citybreak_search_departure_date').makeCaleranDatepicker({
    startEmpty: false,
    minDate: today_moment,
    startDate: today_moment
  }).makeInputmaskDate().on('change', function(){
    var val_moment = moment(this.value, 'DD.MM.Y');
    if(!val_moment.isValid()){
      return;
    }
    var val_tomorrow_moment = val_moment.add(1,'day');
    var $checkout_caleran = $('#service_citybreak_search_return_date').data("caleran");
    $checkout_caleran.config.minDate = val_tomorrow_moment;
    var checkout_val = $('#service_citybreak_search_return_date').val();
    var checkout_val_moment = moment(checkout_val, 'DD.MM.Y');
    if(!checkout_val_moment.isValid() || checkout_val_moment.isBefore(val_tomorrow_moment)){
      $checkout_caleran.config.startDate = val_tomorrow_moment;
      $checkout_caleran.config.endDate = val_tomorrow_moment;
      checkout_val_moment = val_tomorrow_moment;
      checkout_val = checkout_val_moment.format('DD.MM.Y');
      $('#service_citybreak_search_return_date').val(checkout_val);
      $('#service_citybreak_search_return_date').focus();
    }
  });
  
  function serviceCitybreakFormFellowsFormCallback($form,resp,$err_container){
    console.log('serviceCitybreakFormFellowsFormCallback',resp);
    if(resp.status !== 'success'){
      return true;
    }
    loadOrderServices();
    return true;
  }
  $service_citybreak_form_fellows_form.on('submit',function(){
    if(!search_is_over){
      console.log('serviceHotelFormFellowsForm','submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,serviceCitybreakFormFellowsFormCallback);
  });
  
  $('#service_citybreak_search_origin, #service_citybreak_search_destination').autocomplete({
    source: function(request, response){
      $.ajax({
        url: "<?php echo site_url('trip/flights/loadLocations'); ?>",
        dataType: "json",
        data: {
          q: request.term
        }
      }).done(function( resp ) {
        console.log('citybreak autocomplete', resp);
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
      var is_origin = $(this).is($service_citybreak_search_origin);
      var prefix = is_origin ? 'origin' : 'destination';
      $('#service_citybreak_search_' + prefix + '_location_id').val(ui.item.location_id);
      $('#service_citybreak_search_' + prefix + '_location_name').val(ui.item.location_name);
      $('#service_citybreak_search_' + prefix + '_city_id').val(ui.item.city_id);
      $('#service_citybreak_search_' + prefix + '_city_name').val(ui.item.city_name);
      $('#service_citybreak_search_' + prefix + '_country_id').val(ui.item.country_id);
      $('#service_citybreak_search_' + prefix + '_country_name').val(ui.item.country_name);
      $('#service_citybreak_search_' + prefix + '_full_location_name').val(ui.item.value);
    }
  }).on('blur',function(){
    var is_origin = $(this).is($service_citybreak_search_origin);
    var prefix = is_origin ? 'origin' : 'destination';
    if(!this.value.length || this.value !== $('#service_citybreak_search_' + prefix + '_full_location_name').val()){
      this.value = '';
      $('#service_citybreak_search_' + prefix + '_location_id').val(null);
      $('#service_citybreak_search_' + prefix + '_location_name').val(null);
      $('#service_citybreak_search_' + prefix + '_city_id').val(null);
      $('#service_citybreak_search_' + prefix + '_city_name').val(null);
      $('#service_citybreak_search_' + prefix + '_country_id').val(null);
      $('#service_citybreak_search_' + prefix + '_country_name').val(null);
      $('#service_citybreak_search_' + prefix + '_full_location_name').val(null);
    }
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  