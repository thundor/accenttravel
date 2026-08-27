<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = &$this->package_search_data; ?>
<script>
var package_search_data = <?php echo json_encode($data); ?>;
(function($){$(function() {
  var $error_container = $('#package_message_container');
  var $package_entries = $('#package_entries');
  var $package_period_select = $('#package_period_select');
  
  var entries;
  $package_period_select.on('change', function(){
    var entry_index = parseInt(this.value);
    $package_entries.empty();
    var $package_entry = $('#package_entry_model').clone().removeAttr('id');
    var entry = entries[entry_index];
    
    $package_entry.appendTo($package_entries);
    var $rooms_container = $('.package-entry-rooms', $package_entry);
    loadEntryDetails($rooms_container, entry);
  });
  function loadEntries(){
    $package_period_select.val(null).empty();
    if($package_period_select.data('select2_4')){
      $package_period_select.select2_4('destroy');
    }
    var select2_options = [];
    $package_period_select.select2_4({
      theme:'bootstrap',
      containerCssClass:'input-lg',
      minimumResultsForSearch: 1,
      placeholder:'<i class="fa fa-spinner fa-spin fa-pulse"></i> Se incarca perioadele', 
      data: select2_options, 
      width: '100%',
      escapeMarkup: function (markup) { 
        return markup; 
      }
    });
    $package_entries.empty();
    $error_container.empty();
    showMessage($error_container,'Se incarca perioadele ... <i class="fa fa-spinner fa-spin"></i>', 'info');
    $.ajax({
      url: "<?php echo site_url('trip/package/loadEntries'); ?>",
      dataType: "json",
      method: "post",
      data: package_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('loadEntries', resp);
      $error_container.empty();
      
      if(resp.message){
        showMessage($error_container,resp.message, resp.message_type);
      }
      if(!resp.status || resp.status !== 'success'){
        return;
      }
      
      package_search_data = resp.data.package_search_data;
      
      var accordion_id = $package_entries.attr('id');
      entries = resp.data.entries._embedded.entries;
      
      select2_options = [];
      for(var i=0; i<entries.length; i++){
        var entry = entries[i];
        var start_date_moment = moment(entry.StartDate,'Y-MM-DD').locale('ro');
        var start_date = start_date_moment.format('DD.MM.Y');
        var end_date_moment = moment(entry.EndDate,'Y-MM-DD').locale('ro');
        var end_date = moment(entry.EndDate,'Y-MM-DD').format('DD.MM.Y');
        var duration_unit = entry.Duration == 1 ? 'zi' : 'zile';
        if(entry.DurationUnit == 'night'){
          duration_unit = entry.Duration == 1 ? 'noapte' : 'nopti';
        }
        var select2_option = {
          id: i,
          text: start_date + ' - ' + end_date + ' (' + entry.Duration + ' ' + duration_unit + ')',
          start_date: start_date,
          end_date: end_date,
          duration: entry.Duration,
          unit: duration_unit
        };
        select2_options.push(select2_option);
      }
      $package_period_select.select2_4({
        theme:'bootstrap',
        containerCssClass:'input-lg',
        placeholder:'Alegeti perioada', 
        minimumResultsForSearch: 1,
        data: select2_options, 
        width: '100%',
        escapeMarkup: function (markup) { 
          return markup; 
        },
        templateResult: function (item) {
          if(!item.id){
            return item.text;
          }
          return '<div class="d-flex align-items-center justify-content-between"><span>' + item.start_date + ' - ' + item.end_date + '</span><small>' + item.duration + ' ' + duration_unit + '</small></div>' ;
        },
        templateSelection: function(item) {
          if(!item.id){
            return item.text;
          }
          return '<div class="d-flex align-items-center justify-content-between"><span><b>Perioada</b> ' + item.start_date + ' - ' + item.end_date + '</span><small class="text-muted">' + item.duration + ' ' + duration_unit + '</small></div>' ;
        }
      });
      $package_period_select.change();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadEntries','autocomplete',jqXHR, textStatus, errorThrown);
      $error_container.empty();
      showMessage($error_container,'Eroare in cautarea perioadelor', 'danger');
    });
  }
  loadEntries();
  
  
  function loadEntryDetails($wrapper, entry){
    var $form = $wrapper.closest('.package-entry');
    $('.package-reservation', $form).hide();
    showMessage($wrapper,'Se incarca informatiile perioadei ... <i class="fa fa-spinner fa-spin"></i>', 'info');
    package_search_data['entry_id'] = entry.EntryId;
    package_search_data['rate_group_id'] = entry.RateGroupId;
    
    var start_date_moment = moment(entry.StartDate,'Y-MM-DD').locale('ro');
    var end_date_moment = moment(entry.EndDate,'Y-MM-DD').locale('ro');
    var start_date_format = 'DD.MM.Y';
    if(start_date_moment.year() == end_date_moment.year()){
      var start_date_format = 'DD.MM';
      if(start_date_moment.month() == end_date_moment.month()){
        var start_date_format = 'DD';
      }
    }
    $error_container.empty();
    var entry_period = start_date_moment.format(start_date_format) + ' - ' + end_date_moment.format('DD.MM.Y');
    var $hidden_inputs = $('>.hidden-inputs', $form);
    $.ajax({
      url: "<?php echo site_url('trip/package/loadEntryDetails'); ?>",
      dataType: "json",
      method: "post",
      data: package_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('loadEntryDetails', resp);
      $wrapper.empty();
      if(resp.message){
        showMessage($wrapper,resp.message, resp.message_type);
      }
      if(!resp.status || resp.status !== 'success'){
        return;
      }
      
      package_search_data = resp.data.package_search_data;
      var entry_details = resp.data.entry_details;
      
      $('input[name=package_id]',$form).val(package_search_data.package_id);
      $('input[name=code]',$form).val(package_search_data.code);
      $('input[name=entry_id]',$form).val(entry.EntryId);
      $('input[name=rate_group_id]',$form).val(entry.RateGroupId);
      
      var accordion_id = 'package_entries';
      
      for(var i=0; i<entry_details.Accommodation.length; i++){
        var room = entry_details.Accommodation[i];
        var first_package = room[0];
        var children = parseInt(first_package.Children);
        var adults = parseInt(first_package.Adults);
        var children_ages = [];
        if(children){
          var childrenAges = first_package.ChildrenAges;
          for (var o=0; o<childrenAges.length; o++){
            var age = parseInt(childrenAges[o]);
            if(age == 0){
              children_ages.push('<1 an');
            } else if(age == 1){
              children_ages.push('1 an');
            } else {
              children_ages.push(age + ' ani');
            }
          }
        }
        var $room = $('#package_entry_room_model').clone().removeAttr('id');
        if(entry_details.Accommodation.length > 1){
          $('.room-number', $room).html(' Camera ' + (i+1));
        }
        $('.package-room-number', $room).html(i+1);
        $('.package-room-occupancy-adults-number', $room).html(adults);
        if(adults == 1){
          $('.package-room-occupancy-adults .plural', $room).remove();
        } else {
          $('.package-room-occupancy-adults .singular', $room).remove();
        }
        $('.package-room-occupancy-children-number', $room).html(children);
        if(children == 1){
          $('.package-room-occupancy-children .plural', $room).remove();
        } else {
          $('.package-room-occupancy-children .singular', $room).remove();
        }
        if(children){
          $('.child-ages', $room).text(' (' + children_ages.join(',') + ')');
        }
        $('.package-room-price', $room).text(format_price(Math.ceil(first_package.Price),first_package.Currency));
        $('.package-room-entry-interval', $room).text(entry_period);
        $('.package-room-availability', $room).text(first_package.AvailabilityStatus == 'RQ' ? 'La cerere' : 'Disponibil');
        
        $room.attr({
          'data-room_index' : i,
        });
        var accordion_tab_id = accordion_id + '_room_' + i;
        var $card_header = $('>.card-header', $room);
        $card_header.attr({
          'id': accordion_tab_id + '_header',
          'href' : '#' + accordion_tab_id + '_collapse',
          'data-parent' : '#' + accordion_id,
          'aria-controls' : '#' + accordion_tab_id + '_collapse'
        });
        $card_header.next('div').attr({
          'id' : accordion_tab_id + '_collapse',
          'aria-labelledby' : accordion_tab_id + '_header'
        })
        if(!i){
          $card_header.removeClass('collapsed').attr({
            'aria-expanded': 'true'
          }).next('div').addClass('show');
        }
        var $room_option_selector = $('.package-entry-room-option', $room);
        $room_option_selector.attr('name', 'selected[' + i + ']');
        for(var j=0; j<room.length; j++){
          var package = room[j];
          for (var k=0; k<package.Services.length; k++){
            var service = package.Services[k];
            
            var $selector_option = $('<option />').val(j + '-' + k).text(unescape(service.RoomName)).data({
              'RoomName' : unescape(service.RoomName),
              'Price' : package.Price,
              'AvailabilityStatus' : package.AvailabilityStatus,
              'Currency' : package.Currency,
              'RoomId' : service.RoomId,
              'roomIndex' : i,
              'optionIndex' : j,
              'serviceIndex' : k
            });
            $selector_option.appendTo($room_option_selector);
            if(k || j) continue;
            var $hidden_input = $('<input type="hidden" />').attr({
              name: 'occupations[' + i + '][rooms][' + k + ']'
            }).val(service.RoomId);
            $hidden_input.appendTo($room);
            var stars = parseInt(service.UnitStars);
            $('#package_hotel_address').text(unescape(service.CityName) + ', ' + unescape(service.CountryName));
            $('#package_hotel_stars').html(Array(stars+1).join('<i class="fa fa-star"></i>&nbsp;'));
          }
        }
        
        $room.appendTo($wrapper);
      }
      $('.total-package-price').text();
      console.log(entry_details);
      loadEntryDetailRoomExtraServices($wrapper, entry);
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadEntryDetails','autocomplete',jqXHR, textStatus, errorThrown);
      $container.empty();
      showMessage($container,'Eroare in preluarea detaliilor', 'danger');
      $wrapper.data('is_loading', false);
    });
  }
  var submitting_package = false;
  var submitting_button = false;
  $('#package_entries').on('click', 'form.package-entry button[type=submit][name=task]', function(){
    if(submitting_package){
      console.log('already submitting package');
      return false;
    }
    submitting_button = this.value;
  });
  function hashCode (str){
    var hash = 0;
    if (str.length == 0) return hash;
    for (i = 0; i < str.length; i++) {
      char = str.charCodeAt(i);
      hash = ((hash<<5)-hash)+char;
      hash = hash & hash;
    }
    return hash;
  }
  $('#package_entries').on('change', 'form.package-entry input[type=checkbox], form.package-entry select', function(){
    var $form = $(this).closest('form');
    var $checked_checkboxes = $('#package_entries>form.package-entry input[type=checkbox]:checked');
    var $room_selections = $('#package_entries>form.package-entry select').filter(function(){
      var split = $(this).val().split('-');
      return split[0] !== '0' || split[1] !== '0';
    });
    console.log($room_selections);
    var hash = null;
    if($checked_checkboxes.length || $room_selections.length){
      hash = '' + hashCode($checked_checkboxes.serialize() + $room_selections.serialize());
    }
    var $price_update_button = $('button[type=submit][name=task][value=price_update]', $form);
    $price_update_button.data('new_hash', hash);
    console.log($price_update_button.data('hash'), hash);
    var current_hash = $price_update_button.data('hash');
    if(typeof current_hash === 'undefined'){
      current_hash = null;
    }
    $price_update_button.prop('disabled',current_hash === hash);
  });
  $('#package_entries').on('change', '.package-entry-room-option', function(e){
    var $room = $(this).closest('.package-entry-room');
    var $option = $('option:selected',this);
    var Price = $option.data('Price');
    var AvailabilityStatus = $option.data('AvailabilityStatus');
    var Currency = $option.data('Currency');
    var RoomName = $option.data('RoomName');
    var serviceIndex = $option.data('serviceIndex');
    var roomIndex = $option.data('roomIndex');
    var optionIndex = $option.data('optionIndex');
    var RoomId = $option.data('RoomId');
    var $hidden_input = $('input[type="hidden"][name*="[rooms]"]', $room);
    $hidden_input.attr({
      name: 'occupations[' + roomIndex + '][rooms][' + serviceIndex + ']'
    }).val(RoomId);
    $('.package-room-availability', $room).text(AvailabilityStatus == 'RQ' ? 'La cerere' : 'Disponibil');
    $('.package-room-price', $room).text(format_price(Math.ceil(Price),Currency));
  });
  $('#package_entries').on('submit', 'form.package-entry', function(e){
    if(submitting_package){
      console.log('already submitting package');
      return false;
    }
    submitting_package = true;
    console.log(submitting_button);
    if(submitting_button == 'price_update'){
      var $price_update_button = $('button[type=submit][name=task][value=price_update]', $form);
      $price_update_button.prop('disabled', true).data('hash',$price_update_button.data('new_hash')).data('new_hash',null);
      $('.total-package-price', $form).html('<span class="text-warning"><i class="fa fa-spinner fa-spin fa-pulse"></i> Se incarca...</span>')
    }
    var $form = $(this);
    var $hidden_inputs = $('>.hidden-inputs', $form);
    $hidden_inputs.empty();
    var occupations = [];
    console.log('submitting package', e);
    $('input[type=hidden][name^="fake_occupations"],input[type=checkbox]:checked[name^="fake_occupations"]', $form).each(function(){
      var input_name = $(this).attr('name');
      var new_name = input_name.replace('fake_','').replace('[]','');
      if(occupations.indexOf(input_name)>=0){
        var $hidden_input = $('input[name="' + new_name + '"]', $form);
        $hidden_input.val(parseInt($hidden_input.val())+1);
        return;
      }
      occupations.push(input_name);
      var $hidden_input = $(this).clone().removeAttr('class disabled');
      $hidden_input.attr({
        type: 'hidden',
        name: new_name
      }).val(1);
      $hidden_input.appendTo($hidden_inputs);
    });
    basicFormPostSubmit(this,this.action,servicePackageBookingValidationCallback, true, $error_container);
    return false;
  });
  function servicePackageBookingValidationCallback($form,resp,$err_container){
    submitting_package = false;
    console.log('servicePackageBookingValidationCallback',resp);
    if(typeof resp.data.package_availability === 'undefined'){
      return true;
    }
    if(submitting_button == 'submit'){
      if($form.data('do-not-book')){
        console.log('not bookable');
        return false;
      }
      $form[0].submit();
    } else {
      var $price_update_button = $('button[type=submit][name=task][value=price_update]', $form);
      $('.total-package-price', $form).text(format_price(Math.ceil(resp.data.package_availability.Amount),resp.data.package_availability.Currency));
      if($price_update_button.data('new_hash')){
        $price_update_button.prop('disabled', false);
      }
      if(resp.status !== 'success' && resp.message){
        $form.data('do-not-book', 1);
        $('.booking_button_wrapper').hide();
        showMessage($err_container,resp.message, resp.message_type);
      } else {
        $form.data('do-not-book', null);
        $('.booking_button_wrapper').show();
        showMessage($err_container,'Pretul a fost actualizat', 'success');
      }
      return false;
    }
    return true;
  }
  function addExtraServices(extra_services,type,$container, child_age, room_index){
    for(var i=0; i<extra_services.length; i++){
      var extra_service = extra_services[i];
      var is_mandatory = extra_service.Mandatory == 'true' ? true : false;
      var has_multiple_entries = extra_service.MultipleEntries == 'true' ? true : false;
      if($.inArray(type,extra_service.BookingRules.AllowedOccupantsTypes) < 0){
        continue;
      }
      var $package_extra_service = $('#package_extra_service_model').clone().removeAttr('id');
      if(extra_services.length == 1){
      } else if(extra_services.length == 2){
        $package_extra_service.addClass('col-md-6');
      } else if(extra_services.length == 3){
        $package_extra_service.addClass('col-md-4');
      } else {
        $package_extra_service.addClass('col-xl-3 col-md-6');
      }
      if(is_mandatory && (typeof extra_service.BookingRules.MandatoryGuests !== 'undefined') && (typeof extra_service.BookingRules.MandatoryGuests[room_index]) !== 'undefined'){
        for(var j=0; j<extra_service.BookingRules.MandatoryGuests[room_index].length; j++){
          is_mandatory = false;
          var mandatory_guest = extra_service.BookingRules.MandatoryGuests[room_index][j];
          if(mandatory_guest.AgeQualifyingCode == type && mandatory_guest.Count > 0 && (!mandatory_guest.Age || ('' + mandatory_guest.Age === '' + child_age))){
            mandatory_guest.Count --;
            is_mandatory = true;
            break;
          }
        }
      }
      $('.extra-service-name', $package_extra_service).html(unescape(extra_service.Name));
      $('.extra-service-description', $package_extra_service).html(extra_service.Description ? unescape(nl2br(extra_service.Description)) : '');
      var $checkbox_choice = $('input[type=checkbox][name=option]',$package_extra_service);
      $checkbox_choice.attr({
        'name': (type === 'a' ? 'fake_' : '') + 'occupations[' + room_index + '][extra-services][' + extra_service.Id + '][' + type + '][]'
      }).val(child_age).prop('checked', is_mandatory);
      if(is_mandatory){
        var $hidden_input = $checkbox_choice.clone().attr('type','hidden').removeAttr('class');
        $hidden_input.insertBefore($checkbox_choice);
        $checkbox_choice.removeAttr('name');
        $checkbox_choice.prop('disabled',true);
      }
      for(var j=0; j<extra_service.Entries.length; j++){
        var entry = extra_service.Entries[j];
        var $hidden_input = $('<input type="hidden" />').attr({
          name: 'extra-services[' + extra_service.Id + '][entries][' + j + ']'
        }).val(entry.ID);
        $hidden_input.insertBefore($checkbox_choice);
        if(!has_multiple_entries){
          break;
        }
      }
      for(var j=0; j<extra_service.PriceSets.length; j++){
        var price_set = extra_service.PriceSets[j];
        var $hidden_input = $('<input type="hidden" />').attr({
          name: 'extra-services[' + extra_service.Id + '][entries][price-set]'
        }).val(price_set.ID);
        break;
        $hidden_input.insertBefore($checkbox_choice);
      }
      $package_extra_service.appendTo($container);
    }
  }
  function loadEntryDetailRoomExtraServices($wrapper, entry){
    $entry = $wrapper.parent();
    $containers = $('.package-entry-extra', $wrapper);
    $containers.empty();
    $error_container.empty();
    showMessage($error_container,'Se incarca serviciile extra ... <i class="fa fa-spinner fa-spin fa-pulse"></i>', 'info');
    package_search_data['entry_id'] = entry.EntryId;
    package_search_data['rate_group_id'] = entry.RateGroupId;
    $.ajax({
      url: "<?php echo site_url('trip/package/loadEntryDetailsExtra'); ?>",
      dataType: "json",
      method: "post",
      data: package_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('loadEntryDetailsExtra', resp);
      var $form = $entry.closest('form');
      $error_container.empty();
      if(resp.message){
        showMessage($containers,resp.message, resp.message_type);
      }
      if(!resp.status || resp.status !== 'success'){
        return;
      }
      package_search_data = resp.data.package_search_data;
      var entry_details_extra = resp.data.entry_details_extra;
      
      var show_price_update = true;
      if(entry_details_extra.total_items>0){
        var extra_services = entry_details_extra._embedded.extra_services;
        $.each($containers,function(room_index){
          var $container = $(this);
          var occupancy = package_search_data.occupancy[room_index];
          var index = 0;
          for(var i=1; i<=occupancy.adt;i++){
            index++;
            var $occupancy = $('#package_entry_accommodation_room_package_occupancy_model').clone().removeAttr('id');
            $('.for-children', $occupancy).remove();
            $('.type-index', $occupancy).text(i);
            $occupancy.appendTo($container);
            addExtraServices(extra_services,'a',$('.package-entry-accommodation-package-extra-services', $occupancy), 1, room_index);
          }
          if(occupancy.chd){
            for(var i=0; i<occupancy.chd.length;i++){
              var child_age = parseInt(occupancy.chd[i])-1;
              index++;
              var $occupancy = $('#package_entry_accommodation_room_package_occupancy_model').clone().removeAttr('id');
              $('.package-room-occupancy-adult', $occupancy).remove();
              var age = child_age + ' ani';
              var child_type = 'c';
              if(child_age < 3){
                child_type = 'c';
              }
              if(child_age == 1){
                age = child_age + ' an';
              } else if(!child_age){
                age = '< 1 an';
              }
              $('.child-age', $occupancy).text(age);
              $('.type-index', $occupancy).text(i+1);
              $occupancy.appendTo($container);
              addExtraServices(extra_services,child_type,$('.package-entry-accommodation-package-extra-services', $occupancy), child_age, room_index);
            }
          }
        });
        show_price_update = $('input[type=checkbox]:not(:disabled)', $form).length > 0;
      }
      var $price_update_button = $('button[type=submit][name=task][value=price_update]', $form);
      $price_update_button.parent().toggle(show_price_update);
      console.log(entry_details_extra);
      
      $wrapper.data('is_loading', false);
      $wrapper.data('is_loaded', true);
      
      $('.package-reservation', $wrapper.closest('.package-entry')).show();
      $price_update_button.prop('disabled',false);
      $price_update_button[0].click();
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadEntryDetailsExtra','autocomplete',jqXHR, textStatus, errorThrown);
      $error_container.empty();
      showMessage($containers,'Eroare in preluarea detaliilor', 'danger');
      $wrapper.data('is_loading', false);
    });
  }
  package_submit_function = function (e){
    if(!search_is_over){
      console.log('A previous search is not complete. Ignoring request.');
      return;
    }
    setPackageData($(e.target));
    package_search_data.code='';
    loadEntries();
  };
  <?php if(isset($_GET['n'])){ ?>
  removeLocationParam('n');
  <?php } ?>
  <?php if($this->_ci->input->get('init')){ ?>
  removeLocationParam('init');
  <?php } ?>
})})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>