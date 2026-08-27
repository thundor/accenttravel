<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){
themeFunctions::jsLang('warning_form_not_ready');
themeFunctions::loadModule('helpers/countries/json', __FILE__ . '/json_selections');
themeFunctions::loadModule('helpers/titles/json', __FILE__ . '/json_selections');
?>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<script>
(function($){
  function htmlDecode(input){
    var e = document.createElement('div');
    e.innerHTML = input;
    // handle case of empty input
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
  }
  var order = <?php echo json_encode($order); ?>;
  console.log(order);
  var $action_buttons = $('button[type=submit][form=customerForm]');
  function enableActionButtons(){
    $action_buttons.prop('disabled',false);
  }
  function disableActionButtons(){
    $action_buttons.prop('disabled',true);
  }
  var form_ready = true;
  var editing = <?php echo $editing = $order->id != 0 ? 1 : 0; ?>;
  $action_buttons.on('click',function(e){
    var action = this.value;
    var validate_as = editing ? 'edit' : 'add';
    if(editing){
      if(action === 'save_as_new'){
        validate_as = 'add';
      }
    }
    $('#password').prop('required',validate_as === 'add');
    this.form.action.value = action;
    return true;
  });
  $action_buttons.prop('disabled', false);
  function submitCallback($form,resp,$error_container){
    form_ready = true;
    if(resp.status !== 'success'){
      return true;
    }
    var form = $form[0];
    var action = form.action.value;
    if(action == 'save_and_back'){
      window.top.location = '<?php echo site_url('backend/trip/orders'); ?>';
      return;
    }
    if(action == 'save_and_new'){
      window.top.location = '<?php echo site_url('backend/trip/orders/add/paralela45'); ?>';
      return;
    }
    if((!editing && action == 'apply') || (action == 'save_as_new')){
      window.top.location = resp.data.edit_link;
      return;
    }
    window.top.location.reload();
    return false;
  }
  $('#orderForm').on('submit',function(e){
    e.preventDefault();
    if(!form_ready){
      alert(js_lang.warning_form_not_ready);
      return false;
    }
    form_ready = false;
    var action = this.action.value;
    var id = this.id.value;
    if(!editing || (editing && action == 'save_as_new')){
      this.id.value = 0;
    }
    basicFormPostSubmit(this,"<?php echo site_url('backend/trip/orders/save'); ?>",submitCallback,true);
    this.id.value = id;
  });
  var order_services = [];
  var order_currency = 'RON';
  function loadOrderServicesSubmitCallback($form,resp,$error_container){
    $('#result_order_services_form').empty();
    $('#order_services_form_submit').prop('disabled',false);
    console.log('loadOrderServicesSubmitCallback', resp);
    if(resp.status !== 'success'){
      $('#order_services_total').html('<i class="fa fa-warning"></i>');
      return true;
    }
    $('#result_order_payment_methods_form').empty();
    $('#order_services_total').text(resp.data.services.length);
    var $order_services =  $('#order_services');
    var total_services_price = 0;
    var booking_id = resp.data.trip_order_id;
    var already_booked = booking_id ? true : false;
    var something_expired = false;
    order_services = resp.data.services;
    order_currency = resp.data.currency_code;
    for(var i=0; i<resp.data.services.length; i++){
      var order_service = resp.data.services[i];
      
      var service_type = order_service.type;
      var service_price = parseFloat(order_service.price);
      total_services_price += service_price;
      var service_number = i+1;
      var $order_service_model = $('#order-service-model').clone().removeAttr('id');
      $('.service-number', $order_service_model).text(service_number);
      $('.service-types > .service-type:not(.service-type-'+ service_type +')', $order_service_model).remove();
      $('.service-price', $order_service_model).text(format_price(Math.ceil(service_price), order_currency));
      $('.service-points', $order_service_model).text(Math.floor(service_price * 2/100));
      var service_id_prefix = 'order-service-' + i;
      
      var $card_header = $('>.card-header', $order_service_model);
      $card_header.attr({
        'href' : '#' + service_id_prefix + '_collapse',
        'data-loaded_details' : false,
        'data-service_id' : i,
        'data-order_service_index' : i,
        'aria-controls' : '#' + service_id_prefix + '_collapse',
        'id': service_id_prefix + '_header'
      });
      var $card_content = $card_header.next('div');
      $card_content.attr({
        'id' : service_id_prefix + '_collapse',
        'aria-labelledby' :  service_id_prefix + '_header'
      });
      $('form.order-service-details-form',$card_content).attr({
        'name': service_id_prefix + '-form',
        'id': service_id_prefix + '-form'
      });
      $('.result-order-service-details-form',$card_content).attr({
        'id': 'result_' + service_id_prefix + '-form'
      });
      $card_header.on('click', function(e){
        if(!$(this).hasClass('collapsed')){
          return true;
        }
        if(!$(this).data('loaded_details')){
          $(this).data('loaded_details',true);
          var service_id = parseInt($(this).data('service_id'));
          var $form = $('#order-service-' + service_id + '-form');
          openOrderService(service_id,$form);
        }
      });
      $order_service_model.appendTo($order_services);
      
      $('.btn-remove-service',$order_service_model).attr('data-service_id',i).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var service_id = $(this).data('service_id');
        swal({
          title: 'Aceasta actiune este permanenta!',
          text: 'Sunteti sigur ca doriti sa eliminati acest serviciu?',
          icon: 'warning',
          buttons: {
            cancel: "Nu... m-am razgandit.",
            delete: {
              text: "Da. Elimina!",
              value: service_id,
              className: 'btn-danger'
            }
          },
          dangerMode: true
        }).then(function(value){
          if(value !== null){
            $('#order_service_remove_service_id').val(value);
            $('#order_services_remove_form').submit();
          }
        });
      });
    }
    if(already_booked){
      $('#order_set_payment_method_form_wrapper').show();
      $('#order_set_payment_method_form').hide();
      $('#order_book_services_form').hide();
      // $('#order_set_payment_status_form').hide();
    } else if(something_expired || !resp.data.services.length){
      $('#order_services_wrapper').show();
      // $('#order_set_payment_method_form_wrapper').hide();
      // $('#order_set_payment_method_form').hide();
      $('#order_book_services_form').hide();
    } else {
      $('#order_book_services_form').show();
      // $('#order_payment_methods_form').submit();
    }
    
    if(resp.data.total_items <=0 ){
      $('#order_services_no_services').show();
    }
    return false;
  }
  $('#order_services_form').on('submit', function(){
    // $('#order_set_payment_method_form_wrapper').hide();
    $('#order_services').empty();
    $('#order_services_form_submit').prop('disabled',true);
    $('#order_services_no_services').hide();
    $('#order_services_total').html('<i class="fa fa-spinner fa-spin"></i>');
    basicFormPostSubmit(this,this.action,loadOrderServicesSubmitCallback,true);
  });
  function openOrderService(service_index, $form){
    var service = order_services[service_index];
    var $order_service_details = $form.parent();
    $order_service_details.empty();
    console.log($order_service_details);
    if(service.type == 'strainatate'){
      interpretGetServiceTypeStrainatate(service_index,service, $order_service_details);
    }
    if(service.type == 'circuit'){
      interpretGetServiceTypeCircuit(service_index,service, $order_service_details);
    }
    $('.lazy',$order_service_details).lazy();
  }
  function interpretGetServiceTypeCircuit(service_index,service_data, $container){
    var $model = $('#order-service-hotel-model').clone().removeAttr('id');
    var service_id = service_index;
    var $card_header = $('>.card-header',$model);
    var $card_block = $('>.card-block',$model);
    var id_suffix = '_' + service_id;
    
    if(!service_data.extra_services){
      $('a.nav-link[data-tab=order_service_hotel_extra_services_tab]', $card_header).remove();
      $('>.tab-content>.order_service_hotel_extra_services_tab', $card_block).remove();
    }
    $('a.nav-link', $card_header).each(function(){
      var data_tab = $(this).data('tab');
      $(this).attr({
        'id': data_tab + id_suffix + '_link',
        'aria-controls': data_tab + id_suffix + '_tab',
        'href': '#' + data_tab + id_suffix + '_tab',
      }).removeAttr('data-tab');
      $('>.tab-content>.' + data_tab, $card_block).attr({
        'id': data_tab + id_suffix + '_tab',
      })
    });
    
    if(service_data.offer.Services){
      for(var i=0; i<service_data.offer.Services.length;i++){
        var own_service = service_data.offer.Services[i];
        var $li = $('<li class="list-group-item"/>');
        var price_text = '';
        if(own_service.Price){
          var price_text = ' (' + format_price(own_service.Price, order_currency) + ')';
        }
        $li.addClass(own_service.TypeName).text(own_service.Name + price_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_services_tab .services-list', $card_block));
      }
    }
    
    if(service_data.extra_services){
      for(var service_key in service_data.extra_services){
        if (!service_data.extra_services.hasOwnProperty(service_key)) {
          continue;
        }
        var extra_service = service_data.extra_services[service_key];
        var $li = $('<li class="list-group-item"/>');
        var price_text = '';
        if(extra_service.price){
          var price_text = ' (' + format_price(extra_service.price.Gross, order_currency) + ')';
        }
        $li.addClass(extra_service.TypeName).text(extra_service.Name + price_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_extra_services_tab .extra_services-list', $card_block));
      }
    }
    if(service_data.cancellation_policies && service_data.cancellation_policies.length){
      for(var i=0;i<service_data.cancellation_policies.length;i++){
        var cancellation_policy = service_data.cancellation_policies[i];
        var penalty_start_date = moment(cancellation_policy.from_date,'Y-MM-DD');
        if(cancellation_policy.percentage){
          var price_formatted = format_price(cancellation_policy.price, '%');
        } else {
          var price_formatted = format_price(cancellation_policy.price, order_currency);
        }
        var cancellation_text = "Anularea dupa data " + penalty_start_date.locale('ro').format("DD/MM/Y (dddd, D MMMM)") + " presupune o penalizare de " + price_formatted;
        var $li = $('<li class="list-group-item"/>');
        $li.text(cancellation_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_cancel_tab .cancellation-policies-list', $card_block));
      }
    }
    var $order_service_hotel_other_tab = $('>.tab-content>.order_service_hotel_other_tab', $card_block);
    
    // $('.service-hotel-error_message', $order_service_hotel_other_tab).text((service_data.ErrorMessage ? service_data.ErrorMessage : '') + String.fromCharCode(160));
    // $('.service-hotel-status_message', $order_service_hotel_other_tab).text((service_data.StatusMessage ? service_data.StatusMessage : '') + String.fromCharCode(160));
    // $('.service-hotel-confirmation_no', $order_service_hotel_other_tab).text((service_data.ConfirmationNo ? service_data.ConfirmationNo : '') + String.fromCharCode(160));
    // $('.service-hotel-system', $order_service_hotel_other_tab).text(service_data.System);
    // $('.service-hotel-service_id', $order_service_hotel_other_tab).text(service_data.Id);
    // $('.service-hotel-reservation_id', $order_service_hotel_other_tab).text((service_data.ReservationId ? service_data.ReservationId : '') + String.fromCharCode(160));
    
    $('.service-adults-number', $order_service_hotel_other_tab).text(service_data.total_adults);
    $('.service-children-number', $order_service_hotel_other_tab).text(service_data.total_children);
    $('.service-hotel-checkin', $order_service_hotel_other_tab).text(moment(service_data.checkin,'Y-MM-DD').format('DD.MM.Y'));
    $('.service-hotel-checkout', $order_service_hotel_other_tab).text(moment(service_data.checkout,'Y-MM-DD').format('DD.MM.Y'));
    // $('.service-hotel-comments', $order_service_hotel_other_tab).text((service_data.Comments ? service_data.Comments : '') + String.fromCharCode(160));
    $('.service-hotel-price', $order_service_hotel_other_tab).text(format_price(Math.ceil(parseFloat(service_data.price)), order_currency));
    
    var $order_service_hotel_rooms_tab = $('>.tab-content>.order_service_hotel_rooms_tab', $card_block);
    for(var i=0;i<service_data.offer.Rooms.length;i++){
      var room_number = i+1;
      var room = service_data.offer.Rooms[i];
      var $room_model = $('#order-service-hotel-room-model').clone().removeAttr('id');
      var $room_card_header = $('>.card-header',$room_model);
      var $room_card_block = $('>.card-block',$room_model);
      
      $('.room-number', $room_card_header).text(room_number);
      var room_price = parseFloat(room.Price);
      $('.room-price', $room_card_header).text(format_price(Math.ceil(room_price), order_currency));
      $('.room-points', $room_card_header).text(Math.floor(room_price * 2/100));
      
      $('.room-name', $room_card_block).text(room._);
      // $('.room-board', $room_card_block).text(room.Board);
      // $('.room-info', $room_card_block).text(room.Info);
      // $('.room-status', $room_card_block).text(room.Status);
      
      var room_adults = 0;
      var room_children = 0;
      
      var $guests_container = $('.room-occupancy-guests', $room_card_block);
      var room_guests = service_data.service_rooms[i];
      for(var guest_type in room_guests){
        if (!room_guests.hasOwnProperty(guest_type)) {
          continue;
        }
        for(var j=0; j<room_guests[guest_type].length;j++){
          var service_id_prefix = 'service_hotel_' + id_suffix + '_room_' + i + '_guest_' + guest_type +  '_' + j;
          var guest = room_guests[guest_type][j];
          if(guest_type == 'adt'){
            room_adults++;
            var $guest_model = $('#order-service-hotel-room-guest-adult-model').clone().removeAttr('id');
            // $('.guest-email', $guest_model).text(guest.Email);
            $('.guest-type-number', $guest_model).text(room_adults);
          } else {
            var $guest_model = $('#order-service-hotel-room-guest-child-model').clone().removeAttr('id');
            room_children++;
            $('.guest-type-number', $guest_model).text(room_children);
            
            var age = guest.age;
            $('.guest-age', $guest_model).text(age);
          }
          var moment_birthdate = moment(guest.birth_date,'Y-MM-DD');
          $('.guest-birth_date', $guest_model).text(moment_birthdate.format('DD.MM.Y'));
          $('.guest-number', $guest_model).text(room_adults+room_children);
          $('.guest-title', $guest_model).text(guest.title);
          $('.guest-lastname', $guest_model).text(guest.lastname);
          $('.guest-firstname', $guest_model).text(guest.firstname);
          
          var $guest_card_header = $('>.card-header', $guest_model);
          $guest_card_header.attr({
            'href' : '#' + service_id_prefix + '_collapse',
            'aria-controls' : '#' + service_id_prefix + '_collapse',
            'id': service_id_prefix + '_header'
          });
          var $guest_card_content = $guest_card_header.next('div');
          $guest_card_content.attr({
            'id' : service_id_prefix + '_collapse',
            'aria-labelledby' :  service_id_prefix + '_header'
          });
          $guest_model.appendTo($guests_container);
        }
      }
      
      $('.room-occupancy-adults-number', $room_card_header).text(room_adults);
      if(room_adults == 1){
        $('.room-occupancy-adults .plural', $room_card_header).remove();
      } else {
        $('.room-occupancy-adults .singular', $room_card_header).remove();
      }
      
      $('.room-occupancy-children-number', $room_card_header).text(room_children);
      if(room_children == 1){
        $('.room-occupancy-children .plural', $room_card_header).remove();
      } else {
        $('.room-occupancy-children .singular', $room_card_header).remove();
      }
      
      $room_model.appendTo($order_service_hotel_rooms_tab);
    }
    
    var $order_service_hotel_info_tab = $('>.tab-content>.order_service_hotel_info_tab', $card_block);
    var hotel = service_data.product_info;
    if(hotel.Pictures && hotel.Pictures.Picture && hotel.Pictures.Picture[0])
    $('.hotel-image', $order_service_hotel_info_tab).attr({
      'data-src': hotel.Pictures.Picture[0],
    });
    $('.hotel-name', $order_service_hotel_info_tab).text(hotel.ProductName);
    $('.hotel-location', $order_service_hotel_info_tab).text(hotel.CityName + ', ' + hotel.CountryName);
    $('.hotel-link', $order_service_hotel_info_tab).attr('href',service_data.product_info.Link);
    // $('.hotel-stars', $order_service_hotel_info_tab).addClass('text-warning').html(" " + Array(parseInt(service_data.product.Stars) + 1).join('<i class="fa fa-star"></i>'));
    
    $('.hotel-info-description', $order_service_hotel_info_tab).append('<h1>Descriere</h1>');
    $('.hotel-info-description', $order_service_hotel_info_tab).append(htmlDecode(hotel.Description));
    $('.hotel-info-description', $order_service_hotel_info_tab).append('<h1>Alte informatii</h1>');
    $('.hotel-info-description', $order_service_hotel_info_tab).append('<div style="white-space:pre-line;">' + hotel.DayDescriptions.DayDescription.join('<br /><br />') + '</div>');
    // if(hotel.Facilities && hotel.Facilities.Facility){
      // $('.hotel-info-facilities', $order_service_hotel_info_tab).text(hotel.Facilities.Facility.join(', '));
    // }
    // if(hotel.Phone && $.trim(hotel.Phone).length){
      // $('.hotel-info-phone', $order_service_hotel_info_tab).attr('href','tel:' + $.trim(hotel.Phone));
      // $('.hotel-info-phone>span', $order_service_hotel_info_tab).text($.trim(hotel.Phone));
    // }
    // if(hotel.Fax && $.trim(hotel.Fax).length){
      // $('.hotel-info-fax', $order_service_hotel_info_tab).attr('href','fax:' + $.trim(hotel.Fax));
      // $('.hotel-info-fax>span', $order_service_hotel_info_tab).text($.trim(hotel.Fax));
    // }
    // if(hotel.Email && $.trim(hotel.Email).length){
      // $('.hotel-info-email', $order_service_hotel_info_tab).attr('href','mailto:' + $.trim(hotel.Email));
      // $('.hotel-info-email>span', $order_service_hotel_info_tab).text($.trim(hotel.Email));
    // }
    $model.appendTo($container);
  }
  function interpretGetServiceTypeStrainatate(service_index,service_data, $container){
    var $model = $('#order-service-hotel-model').clone().removeAttr('id');
    var service_id = service_index;
    var $card_header = $('>.card-header',$model);
    var $card_block = $('>.card-block',$model);
    var id_suffix = '_' + service_id;
    
    if(!service_data.extra_services){
      $('a.nav-link[data-tab=order_service_hotel_extra_services_tab]', $card_header).remove();
      $('>.tab-content>.order_service_hotel_extra_services_tab', $card_block).remove();
    }
    $('a.nav-link', $card_header).each(function(){
      var data_tab = $(this).data('tab');
      $(this).attr({
        'id': data_tab + id_suffix + '_link',
        'aria-controls': data_tab + id_suffix + '_tab',
        'href': '#' + data_tab + id_suffix + '_tab',
      }).removeAttr('data-tab');
      $('>.tab-content>.' + data_tab, $card_block).attr({
        'id': data_tab + id_suffix + '_tab',
      })
    });
    
    if(service_data.offer.Services){
      for(var i=0; i<service_data.offer.Services.length;i++){
        var own_service = service_data.offer.Services[i];
        var $li = $('<li class="list-group-item"/>');
        var price_text = '';
        if(own_service.Price){
          var price_text = ' (' + format_price(own_service.Price, order_currency) + ')';
        }
        $li.addClass(own_service.TypeName).text(own_service.Name + price_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_services_tab .services-list', $card_block));
      }
    }
    if(service_data.offer.Meals){
      for(var i=0; i<service_data.offer.Meals.length;i++){
        var own_service = service_data.offer.Meals[i];
        var $li = $('<li class="list-group-item"/>');
        var price_text = '';
        if(own_service.Price){
          var price_text = ' (' + format_price(own_service.Price, order_currency) + ')';
        }
        $li.addClass(own_service.TypeName).text(own_service.Name + price_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_services_tab .services-list', $card_block));
      }
    }
    if(service_data.extra_services){
      for(var service_key in service_data.extra_services){
        if (!service_data.extra_services.hasOwnProperty(service_key)) {
          continue;
        }
        var extra_service = service_data.extra_services[service_key];
        var $li = $('<li class="list-group-item"/>');
        var price_text = '';
        if(extra_service.price){
          var price_text = ' (' + format_price(extra_service.price.Gross, order_currency) + ')';
        }
        $li.addClass(extra_service.TypeName).text(extra_service.Name + price_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_extra_services_tab .extra_services-list', $card_block));
      }
    }
    if(service_data.cancellation_policies && service_data.cancellation_policies.length){
      for(var i=0;i<service_data.cancellation_policies.length;i++){
        for(var j=0;j<service_data.cancellation_policies[i].length;j++){
          var cancellation_policy = service_data.cancellation_policies[i][j];
          var penalty_start_date = moment(cancellation_policy.from_date,'Y-MM-DD');
          if(cancellation_policy.percentage){
            var price_formatted = format_price(cancellation_policy.price, '%');
          } else {
            var price_formatted = format_price(cancellation_policy.price, order_currency);
          }
          if(cancellation_policy.type=='cancellation'){
            var cancellation_text = "Anularea dupa data " + penalty_start_date.locale('ro').format("DD/MM/Y (dddd, D MMMM)") + " presupune o penalizare de " + price_formatted;
          } else {
            var cancellation_text = "Dupa data " + penalty_start_date.locale('ro').format("DD/MM/Y (dddd, D MMMM)") + " se inpune o taxa aditionala de " + price_formatted + " (" + cancellation_policy.type + ")";
          }
          var $li = $('<li class="list-group-item"/>');
          $li.text(cancellation_text);
          $li.appendTo($('>.tab-content>.order_service_hotel_cancel_tab .cancellation-policies-list', $card_block));
        }
      }
    }
    var $order_service_hotel_other_tab = $('>.tab-content>.order_service_hotel_other_tab', $card_block);
    
    // $('.service-hotel-error_message', $order_service_hotel_other_tab).text((service_data.ErrorMessage ? service_data.ErrorMessage : '') + String.fromCharCode(160));
    // $('.service-hotel-status_message', $order_service_hotel_other_tab).text((service_data.StatusMessage ? service_data.StatusMessage : '') + String.fromCharCode(160));
    // $('.service-hotel-confirmation_no', $order_service_hotel_other_tab).text((service_data.ConfirmationNo ? service_data.ConfirmationNo : '') + String.fromCharCode(160));
    // $('.service-hotel-system', $order_service_hotel_other_tab).text(service_data.System);
    // $('.service-hotel-service_id', $order_service_hotel_other_tab).text(service_data.Id);
    // $('.service-hotel-reservation_id', $order_service_hotel_other_tab).text((service_data.ReservationId ? service_data.ReservationId : '') + String.fromCharCode(160));
    
    $('.service-adults-number', $order_service_hotel_other_tab).text(service_data.total_adults);
    $('.service-children-number', $order_service_hotel_other_tab).text(service_data.total_children);
    $('.service-hotel-checkin', $order_service_hotel_other_tab).text(moment(service_data.checkin,'Y-MM-DD').format('DD.MM.Y'));
    $('.service-hotel-checkout', $order_service_hotel_other_tab).text(moment(service_data.checkout,'Y-MM-DD').format('DD.MM.Y'));
    // $('.service-hotel-comments', $order_service_hotel_other_tab).text((service_data.Comments ? service_data.Comments : '') + String.fromCharCode(160));
    $('.service-hotel-price', $order_service_hotel_other_tab).text(format_price(Math.ceil(parseFloat(service_data.price)), order_currency));
    
    var $order_service_hotel_rooms_tab = $('>.tab-content>.order_service_hotel_rooms_tab', $card_block);
    for(var i=0;i<service_data.offer.Rooms.length;i++){
      var room_number = i+1;
      var room = service_data.offer.Rooms[i];
      var $room_model = $('#order-service-hotel-room-model').clone().removeAttr('id');
      var $room_card_header = $('>.card-header',$room_model);
      var $room_card_block = $('>.card-block',$room_model);
      
      $('.room-number', $room_card_header).text(room_number);
      var room_price = parseFloat(room.Price);
      $('.room-price', $room_card_header).text(format_price(Math.ceil(room_price), order_currency));
      $('.room-points', $room_card_header).text(Math.floor(room_price * 2/100));
      
      $('.room-name', $room_card_block).text(room.Name);
      // $('.room-board', $room_card_block).text(room.Board);
      // $('.room-info', $room_card_block).text(room.Info);
      // $('.room-status', $room_card_block).text(room.Status);
      
      var room_adults = 0;
      var room_children = 0;
      
      var $guests_container = $('.room-occupancy-guests', $room_card_block);
      var room_guests = service_data.service_rooms[i];
      for(var guest_type in room_guests){
        if (!room_guests.hasOwnProperty(guest_type)) {
          continue;
        }
        for(var j=0; j<room_guests[guest_type].length;j++){
          var service_id_prefix = 'service_hotel_' + id_suffix + '_room_' + i + '_guest_' + guest_type +  '_' + j;
          var guest = room_guests[guest_type][j];
          if(guest_type == 'adt'){
            room_adults++;
            var $guest_model = $('#order-service-hotel-room-guest-adult-model').clone().removeAttr('id');
            // $('.guest-email', $guest_model).text(guest.Email);
            $('.guest-type-number', $guest_model).text(room_adults);
          } else {
            var $guest_model = $('#order-service-hotel-room-guest-child-model').clone().removeAttr('id');
            room_children++;
            $('.guest-type-number', $guest_model).text(room_children);
            
            var age = guest.age;
            $('.guest-age', $guest_model).text(age);
          }
          var moment_birthdate = moment(guest.birth_date,'Y-MM-DD');
          $('.guest-birth_date', $guest_model).text(moment_birthdate.format('DD.MM.Y'));
          $('.guest-number', $guest_model).text(room_adults+room_children);
          $('.guest-title', $guest_model).text(guest.title);
          $('.guest-lastname', $guest_model).text(guest.lastname);
          $('.guest-firstname', $guest_model).text(guest.firstname);
          
          var $guest_card_header = $('>.card-header', $guest_model);
          $guest_card_header.attr({
            'href' : '#' + service_id_prefix + '_collapse',
            'aria-controls' : '#' + service_id_prefix + '_collapse',
            'id': service_id_prefix + '_header'
          });
          var $guest_card_content = $guest_card_header.next('div');
          $guest_card_content.attr({
            'id' : service_id_prefix + '_collapse',
            'aria-labelledby' :  service_id_prefix + '_header'
          });
          $guest_model.appendTo($guests_container);
        }
      }
      
      $('.room-occupancy-adults-number', $room_card_header).text(room_adults);
      if(room_adults == 1){
        $('.room-occupancy-adults .plural', $room_card_header).remove();
      } else {
        $('.room-occupancy-adults .singular', $room_card_header).remove();
      }
      
      $('.room-occupancy-children-number', $room_card_header).text(room_children);
      if(room_children == 1){
        $('.room-occupancy-children .plural', $room_card_header).remove();
      } else {
        $('.room-occupancy-children .singular', $room_card_header).remove();
      }
      
      $room_model.appendTo($order_service_hotel_rooms_tab);
    }
    
    var $order_service_hotel_info_tab = $('>.tab-content>.order_service_hotel_info_tab', $card_block);
    var hotel = service_data.product_info;
    if(hotel.Pictures && hotel.Pictures.Picture && hotel.Pictures.Picture[0])
    $('.hotel-image', $order_service_hotel_info_tab).attr({
      'data-src': hotel.Pictures.Picture[0]._,
    });
    $('.hotel-name', $order_service_hotel_info_tab).text(hotel.ProductName);
    $('.hotel-location', $order_service_hotel_info_tab).text(hotel.CityName + ', ' + hotel.CountryName);
    $('.hotel-link', $order_service_hotel_info_tab).attr('href',service_data.product.Link);
    $('.hotel-stars', $order_service_hotel_info_tab).addClass('text-warning').html(" " + Array(parseInt(service_data.product.Stars) + 1).join('<i class="fa fa-star"></i>'));
    
    $('.hotel-info-description', $order_service_hotel_info_tab).text(hotel.Description);
    if(hotel.Facilities && hotel.Facilities.Facility){
      $('.hotel-info-facilities', $order_service_hotel_info_tab).text(hotel.Facilities.Facility.join(', '));
    }
    // if(hotel.Phone && $.trim(hotel.Phone).length){
      // $('.hotel-info-phone', $order_service_hotel_info_tab).attr('href','tel:' + $.trim(hotel.Phone));
      // $('.hotel-info-phone>span', $order_service_hotel_info_tab).text($.trim(hotel.Phone));
    // }
    // if(hotel.Fax && $.trim(hotel.Fax).length){
      // $('.hotel-info-fax', $order_service_hotel_info_tab).attr('href','fax:' + $.trim(hotel.Fax));
      // $('.hotel-info-fax>span', $order_service_hotel_info_tab).text($.trim(hotel.Fax));
    // }
    // if(hotel.Email && $.trim(hotel.Email).length){
      // $('.hotel-info-email', $order_service_hotel_info_tab).attr('href','mailto:' + $.trim(hotel.Email));
      // $('.hotel-info-email>span', $order_service_hotel_info_tab).text($.trim(hotel.Email));
    // }
    $model.appendTo($container);
  }
  function removeOrderServicesSubmitCallback($form,resp,$error_container){
    console.log('removeOrderServicesSubmitCallback', resp); 
    if(resp.status !== 'success'){
      return true;
    }
    loadOrderServices();
    return true;
  }
  function bookServicesSubmitCallback($form,resp,$error_container){
    console.log('bookServicesSubmitCallback', resp); 
    if(resp.status !== 'success'){
      return true;
    }
    $('#order_book_services_form').hide();
    return true;
  }
  $('#order_book_services_form').on('submit', function(){
    basicFormPostSubmit(this,this.action,bookServicesSubmitCallback,true);
  });
  $('#order_services_remove_form').on('submit', function(){
    basicFormPostSubmit(this,this.action,removeOrderServicesSubmitCallback,true, $('#result_order_services_form'));
  });
  window.loadOrderServices = function(){
    $('#order_services_form').submit();
  }
  loadOrderServices();
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  