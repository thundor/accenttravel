<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if(1){
themeFunctions::jsLang('warning_form_not_ready');
themeFunctions::loadModule('helpers/countries/json', __FILE__ . '/json_selections');
themeFunctions::loadModule('helpers/titles/json', __FILE__ . '/json_selections');
$this->_ci->load->model('Trip/Flights_airlines_model');
$this->_ci->db->where('`image` IS NOT NULL');
$this->_ci->db->where('LENGTH(`image`)>0');
$airlines = $this->_ci->Flights_airlines_model->getAirlines(array(
  'select' => array(
    '`code`',
    '`image`',
  ),
));
$airlines_images = array();
foreach($airlines as $airline){
  $airlines_images[$airline->code] = $airline->image;
}
?>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<script>
(function($){
  var airlines_images = <?php echo json_encode($airlines_images); ?>;
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
      window.top.location = '<?php echo site_url('backend/trip/orders/add/trip'); ?>';
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
  function loadOrderServicesSubmitCallback($form,resp,$error_container){
    $('#result_order_services_form').empty();
    $('#order_services_form_submit').prop('disabled',false);
    console.log('loadOrderServicesSubmitCallback', resp);
    if(resp.status !== 'success'){
      $('#order_services_total').html('<i class="fa fa-warning"></i>');
      return true;
    }
    $('#result_order_payment_methods_form').empty();
    $('#order_services_total').text(resp.data.total_items);
    var $order_services =  $('#order_services');
    var total_services_price = 0;
    var already_booked = false;
    var something_expired = false;
    for(var i=0; i<resp.data.total_items; i++){
      var order_service = resp.data._embedded.services[i];
      
      var service_type = order_service.Type;
      var service_price = parseFloat(order_service.Amount);
      total_services_price += service_price;
      var service_number = i+1;
      var $order_service_model = $('#order-service-model').clone().removeAttr('id');
      $('.service-number', $order_service_model).text(service_number);
      $('.service-types > .service-type:not(.service-type-'+ service_type +')', $order_service_model).remove();
      $('.service-price', $order_service_model).text(format_price(service_price, order_service.Currency));
      $('.service-points', $order_service_model).text(Math.floor(service_price * 2/100));
      var service_id_prefix = 'order-service-' + i;
      
      var $card_header = $('>.card-header', $order_service_model);
      $card_header.attr({
        'href' : '#' + service_id_prefix + '_collapse',
        'data-loaded_details' : false,
        'data-service_id' : order_service.Id,
        'data-order_service_index' : i,
        'aria-controls' : '#' + service_id_prefix + '_collapse',
        'id': service_id_prefix + '_header'
      });
      if(order_service.Status == 2){
        $card_header.removeClass('bg-success').addClass('bg-danger');
        something_expired = true;
      }
      if(order_service.Status == 1){
        $card_header.removeClass('bg-success').removeClass('text-white');
        already_booked = true;
        $('.btn-remove-service', $card_header).remove();
      }
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
          var service_id = $(this).data('service_id');
          var order_service_index = $(this).data('order_service_index');
          var $form = $('#order-service-' + order_service_index + '-form');
          $form[0].service_id.value = service_id;
          console.log($form[0]);
          $form.submit();
        }
      });
      $order_service_model.appendTo($order_services);
      
      $('.btn-remove-service',$order_service_model).attr('data-service_id',order_service.Id).on('click', function(e){
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
          if(value){
            $('#order_service_remove_service_id').val(value);
            $('#order_services_remove_form').submit();
          }
        });
      });
    }
	var selected_payment_method;
	var selected_payment_status;
	if(order.trip_order && order.trip_order.Payment){
	  selected_payment_method = order.trip_order.Payment.Method;
	  selected_payment_status = parseInt(order.trip_order.Payment.Status);
	}
    if(already_booked){
      $('#order_set_payment_method_form_wrapper').show();
      $('#order_set_payment_method_form').hide();
      $('#order_payment_method_set').show();
      $('#order_book_services_form').hide();
      $('#order_set_payment_status_form').hide();
	  
		if(!selected_payment_status){
		  $('#order_set_payment_status_form').show();
		}
    } else if(something_expired || !resp.data.total_items){
      $('#order_services_wrapper').show();
      $('#order_set_payment_method_form_wrapper').hide();
      $('#order_set_payment_method_form').hide();
      $('#order_book_services_form').hide();
    } else {
      $('#order_payment_methods_form').submit();
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
  function removeOrderServicesSubmitCallback($form,resp,$error_container){
    console.log('removeOrderServicesSubmitCallback', resp); 
    if(resp.status !== 'success'){
      return true;
    }
    loadOrderServices();
    return true;
  }
  function interpretGetServiceTypeFlight(service_data, $container){
    var $model = $('#order-service-flight-model').clone().removeAttr('id');
    $model.appendTo($container);
    var service_id = service_data.Id;
    var $card_header = $('>.card-header',$model);
    var $card_block = $('>.card-block',$model);
    var id_suffix = '_' + service_id;
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
    
    var $order_service_flight_other_tab = $('>.tab-content>.order_service_flight_other_tab', $card_block);
    var passenger_adt = 0;
    var passenger_sen = 0;
    var passenger_chd = 0;
    var passenger_inf = 0;
    var passenger_ins = 0;
    for(var i=0; i<service_data.Passengers.length; i++){
      var passenger = service_data.Passengers[i];
      if(passenger.Type == 'ADT'){
        passenger_adt++;
      }
      if(passenger.Type == 'SEN'){
        passenger_sen++;
      }
      if(passenger.Type == 'CHD'){
        passenger_chd++;
      }
      if(passenger.Type == 'INF'){
        passenger_inf++;
      }
      if(passenger.Type == 'INS'){
        passenger_ins++;
      }
    }
    $('.service-flight-error_message', $order_service_flight_other_tab).text((service_data.ErrorMessage ? service_data.ErrorMessage : '') + String.fromCharCode(160));
    $('.service-flight-status_message', $order_service_flight_other_tab).text((service_data.StatusMessage ? service_data.StatusMessage : '') + String.fromCharCode(160));
    $('.service-flight-flight_type', $order_service_flight_other_tab).text((service_data.FlightType == 0 ? 'Doar dus' : (service_data.FlightType == 1 ? 'Tur-Retur' : 'Destinatii multiple')));
    $('.service-flight-time_limit', $order_service_flight_other_tab).text(service_data.TimeLimit);
    $('.service-flight-confirmation_no', $order_service_flight_other_tab).text((service_data.ConfirmationNo ? service_data.ConfirmationNo : '') + String.fromCharCode(160));
    $('.service-flight-system', $order_service_flight_other_tab).text(service_data.System);
    $('.service-flight-service_id', $order_service_flight_other_tab).text(service_data.Id);
    $('.service-flight-reservation_id', $order_service_flight_other_tab).text((service_data.ReservationId ? service_data.ReservationId : '') + String.fromCharCode(160));
    $('.service-adt-number', $order_service_flight_other_tab).text(passenger_adt);
    $('.service-sen-number', $order_service_flight_other_tab).text(passenger_sen);
    $('.service-chd-number', $order_service_flight_other_tab).text(passenger_chd);
    $('.service-inf-number', $order_service_flight_other_tab).text(passenger_inf);
    $('.service-ins-number', $order_service_flight_other_tab).text(passenger_ins);
    $('.service-flight-comments', $order_service_flight_other_tab).text((service_data.Comments ? service_data.Comments : '') + String.fromCharCode(160));
    $('.service-flight-price', $order_service_flight_other_tab).text(format_price(Math.ceil(parseFloat(service_data.Amount)),service_data.Currency));
    
    
    var $order_service_flight_routes_tab = $('>.tab-content>.order_service_flight_routes_tab', $card_block);
    var found_return = false;
    for(var i=0; i<service_data.Routes.length; i++){
      var route = service_data.Routes[i];
      if(!found_return && route.RouteType == 1){
        found_return = true;
        $('<hr />').appendTo($order_service_flight_routes_tab);
      }
      var $route_model = $('#order_service_flight_result_item_model').clone().removeAttr('id');
      if(!route.FlightStopTime){
        $('.leaving-stop-duration', $route_model).remove();
      } else {
        var stop_mins = route.FlightStopTime;
        var stop_h = stop_mins / 60 | 0,
            stop_m = stop_mins % 60 | 0;
        $('.flight-stop-duration', $route_model).text(stop_h + '\'' + stop_m + '"');
      }
      var company_image = typeof airlines_images[route.CarrierMarketingCode] !== 'undefined' ? (airlines_images[route.CarrierMarketingCode]) : 'placeholder_companie.png';
      $('.company-image', $route_model).attr({
        'src': '<?php echo $this->theme_url; ?>assets/images/' + company_image
      });
      $('.company-name', $route_model).text(route.CarrierMarketingName);
      var leaving_mins = route.OriginTime;
      var leaving_h = leaving_mins / 60 | 0,
          leaving_m = leaving_mins % 60 | 0;
      $('.leaving-date', $route_model).text(moment(route.OriginDate,'Y-MM-DD').format('DD.MM.Y'));
      $('.leaving-hour', $route_model).text(moment.utc().hours(leaving_h).minutes(leaving_m).format("hh:mm A"));
      $('.leaving-airport-city', $route_model).text(route.OriginCityName);
      $('.leaving-airport-name', $route_model).text(route.OriginAirportName);
      $('.aircraft-name', $route_model).text(route.AircraftName).attr('title', route.AircraftCode);
      var arriving_mins = parseInt(route.DestinationTime);
      var arriving_h = arriving_mins / 60 | 0,
          arriving_m = arriving_mins % 60 | 0;
      $('.arriving-date', $route_model).text(moment(route.DestinationDate,'Y-MM-DD').format('DD.MM.Y'));
      $('.arriving-hour', $route_model).text(moment.utc().hours(arriving_h).minutes(arriving_m).format("hh:mm A"));
      $('.arriving-airport-city', $route_model).text(route.DestinationCityName);
      $('.arriving-airport-name', $route_model).text(route.DestinationAirportName);
      $route_model.appendTo($order_service_flight_routes_tab);
    }
    var $order_service_flight_passengers_tab = $('>.tab-content>.order_service_flight_passengers_tab', $card_block);
    var found_return = false;
    for(var i=0; i<service_data.Passengers.length; i++){
      var passenger = service_data.Passengers[i];
      var passenger_type = passenger.Type.toLowerCase();
      var $passenger = $('#order_service_flight_passenger_model').clone().removeAttr('id');
      $('.passenger-index', $passenger).text(i+1);
      $('.passenger-type:not(.passenger-type-' + passenger_type + ')',$passenger).remove();
      $('.passenger-title', $passenger).text(passenger.Title);
      $('.passenger-lastname', $passenger).text(passenger.LastName);
      $('.passenger-firstname', $passenger).text(passenger.FirstName);
      $('.passenger-birth_date', $passenger).text(passenger.BirthDate);
      $('.passenger-email', $passenger).text(passenger.Email);
      $('.passenger-phone', $passenger).text(passenger.Phone);
      if(!passenger.IdDocExpiryDate){
        $('.flight-secured', $passenger).remove();
      } else {
        $('.passenger-passport-number', $passenger).text(passenger.IdDocNumber);
        $('.passenger-passport-issuing_country', $passenger).text(passenger.IdDocIssuingCountry);
        $('.passenger-passport-expiry_date', $passenger).text(passenger.IdDocExpiryDate);
        $('.passenger-passport-pax_nationality', $passenger).text(passenger.IdDocPaxNationality);
      }
      $passenger.appendTo($order_service_flight_passengers_tab);
    }
	
	var $order_service_flight_options_tab = $('>.tab-content>.order_service_flight_options_tab', $card_block);
	var $ul = $(' > ul', $order_service_flight_options_tab);
	if(service_data.PaidSeats && service_data.PaidSeats.length){
		var $li = $('<li>');
		$li.appendTo($ul);
		$li.html("Locuri platite: <br/>");
		var $ol = $('<ol>');
		$ol.appendTo($li);
		for(var i=0; i<service_data.PaidSeats.length; i++){
			var PaidSeat = service_data.PaidSeats[i];
			var $li = $('<li>');
			$li.appendTo($ol);
			$li.html(
				"Zbor #" + (1 + parseInt(PaidSeat.LegIndex) + parseInt(PaidSeat.SegmentIndex))
				+ ' Pasager #' + (1 + parseInt(PaidSeat.PassengerIndex))
				+ ' Loc ' + PaidSeat.SeatNumber + '' + PaidSeat.SeatColumn
				+ ' (' + format_price(PaidSeat.Amount, PaidSeat.Currency) + ')'
			);
		}
	}
	
	if(service_data.OptionalServices && service_data.OptionalServices.length){
		var $li = $('<li>');
		$li.appendTo($ul);
		$li.html("Optiuni: <br/>");
		var $ol = $('<ol>');
		$ol.appendTo($li);
		for(var i=0; i<service_data.OptionalServices.length; i++){
			var OptionalService = service_data.OptionalServices[i];
			var $li = $('<li>');
			$li.appendTo($ol);
			$li.html(
				"Ruta " + OptionalService.Departure + '-' + OptionalService.Arrival
				+ ', Pasager ' + OptionalService.Target + '#' +  (1 + parseInt(OptionalService.PassengerIndex))
				+ ' Optiune ' + OptionalService.Name
				+ ' (' + (!OptionalService.Amount ? 'Inclus' : format_price(OptionalService.Amount, OptionalService.Currency)) + ')'
			);
		}
	}
	if(service_data.Details && Object.keys(service_data.Details).length){
		
		var SeatDetails = Object.keys(service_data.Details).reduce((carry, DetailKey) => {
			var DetailArr = ('' + DetailKey).split(':');
			var Detail2Arr = (DetailArr[1]||'').split('_');
			if(DetailArr[0] == 'SEAT'){
				carry[Detail2Arr[1] + '_' + Detail2Arr[2]] = carry[Detail2Arr[1] + '_' + Detail2Arr[2]] || {};
				carry[Detail2Arr[1] + '_' + Detail2Arr[2]][DetailArr[2]] = service_data.Details[DetailKey];
			}
			return carry;
		}, {});
		
		var $li = $('<li>');
		$li.appendTo($ul);
		$li.html("Locuri preferate: <br/>");
		var $ol = $('<ol>');
		$ol.appendTo($li);
		Object.keys(SeatDetails).forEach((SeatDetailKey) => {
			var DetailValue = SeatDetails[SeatDetailKey];
			var legNumber = 1 + SeatDetailKey.split('_').reduce((c, i) => c+=parseInt(i),0);
			var $li = $('<li>');
			$li.appendTo($ol);
			$li.html(
				"Zbor #" + legNumber
				+ ' Ruta ' + DetailValue.ORIGIN + ' - ' +  DetailValue.DESTINATION
				+ ' ' + (DetailValue.PREFERENCE ? (DetailValue.PREFERENCE == 'A' ? 'Culoar': 'Fereastra') : 'Loc ' + DetailValue.NUMBER + '' + DetailValue.CODE)
			);
		})
	}
	
  }
  function interpretGetServiceTypePackage(service_data, $container){
    console.log(service_data, $container);
    var $model = $('#order-service-package-model').clone().removeAttr('id');
    var package = service_data.Package;
    var service_id = service_data.Id;
    var $card_header = $('>.card-header',$model);
    var $card_block = $('>.card-block',$model);
    var id_suffix = '_' + service_id;
    
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
    
    if(service_data.CancellationPolicies && service_data.CancellationPolicies.length){
      for(var i=0;i<service_data.CancellationPolicies.length;i++){
        var policy = service_data.CancellationPolicies[i];
        var cancellation_text = 'Anularea dupa data ' + moment(policy.Limit).format("dddd, MMMM DD.MM.Y, hh:mm:ss A") + ' presupune o penalizare de ' + format_price(parseFloat(policy.Amount), policy.Currency);
        var $li = $('<li class="list-group-item"/>');
        $li.text(cancellation_text);
        $li.appendTo($('>.tab-content>.order_service_package_cancel_tab .cancellation-policies-list', $card_block));
      }
    }
    var reference_moment = moment(service_data.EndDate,'Y-MM-DD');
    
    var $order_service_package_other_tab = $('>.tab-content>.order_service_package_other_tab', $card_block);
    
    $('.service-package-error_message', $order_service_package_other_tab).text((service_data.ErrorMessage ? service_data.ErrorMessage : '') + String.fromCharCode(160));
    $('.service-package-status_message', $order_service_package_other_tab).text((service_data.StatusMessage ? service_data.StatusMessage : '') + String.fromCharCode(160));
    $('.service-package-confirmation_no', $order_service_package_other_tab).text((service_data.ConfirmationNo ? service_data.ConfirmationNo : '') + String.fromCharCode(160));
    $('.service-package-service_id', $order_service_package_other_tab).text(service_data.Id);
    $('.service-package-reservation_id', $order_service_package_other_tab).text((service_data.ReservationId ? service_data.ReservationId : '') + String.fromCharCode(160));
    
    $('.service-adults-number', $order_service_package_other_tab).text(service_data.Adults);
    $('.service-children-number', $order_service_package_other_tab).text(service_data.Children);
    $('.service-package-checkin', $order_service_package_other_tab).text(moment(service_data.StartDate,'Y-MM-DD').format('DD.MM.Y'));
    $('.service-package-checkout', $order_service_package_other_tab).text(moment(service_data.EndDate,'Y-MM-DD').format('DD.MM.Y'));
    $('.service-package-comments', $order_service_package_other_tab).text((service_data.Comments ? service_data.Comments : '') + String.fromCharCode(160));
    $('.service-package-price', $order_service_package_other_tab).text(format_price(Math.ceil(parseFloat(service_data.Amount)), service_data.Currency));
    
    var $order_service_package_rooms_tab = $('>.tab-content>.order_service_package_rooms_tab', $card_block);
    if(service_data.Rooms && service_data.Rooms.length){
      for(var i=0;i<service_data.Rooms.length;i++){
        var room_number = i+1;
        var room = service_data.Rooms[i];
        var $room_model = $('#order-service-package-room-model').clone().removeAttr('id');
        var $room_card_header = $('>.card-header',$room_model);
        var $room_card_block = $('>.card-block',$room_model);
        
        $('.room-number', $room_card_header).text(room_number);
        // var room_price = parseFloat(room.Price);
        // $('.room-price', $room_card_header).text(format_price(Math.ceil(room_price), room.Currency));
        // $('.room-points', $room_card_header).text(Math.floor(room_price * 2/100));
        // $('.room-price-points', $room_card_header).hide();
        
        $('.room-name', $room_card_block).text(room.RoomName);
        $('.room-board', $room_card_block).text(room.RoomTypeDescription);
        $('.room-info', $room_card_block).text(room.CityName);
        $('.room-status', $room_card_block).text(room.UnitName);
        
        var room_adults = 0;
        var room_children = 0;
        
        var $guests_container = $('.room-occupancy-guests', $room_card_block);
        for(var j=0; j<service_data.Occupants.length;j++){
          var guest = service_data.Occupants[j];
          if(guest.OccupationIdx != i){
            continue;
          }
          var service_id_prefix = 'service_package_' + id_suffix + '_room_' + i + '_guest_' + j;
          if(guest.Type == 'a'){
            room_adults++;
            var $guest_model = $('#order-service-hotel-room-guest-adult-model').clone().removeAttr('id');
            $('.guest-email', $guest_model).text(guest.Email);
            $('.guest-type-number', $guest_model).text(room_adults);
          } else {
            var $guest_model = $('#order-service-hotel-room-guest-child-model').clone().removeAttr('id');
            room_children++;
            $('.guest-type-number', $guest_model).text(room_children);
            
            var moment_birthdate = moment(guest.BirthDate,'Y-MM-DD');
            $('.guest-birth_date', $guest_model).text(moment_birthdate.format('DD.MM.Y'));
            var age = reference_moment.diff(moment_birthdate, 'years');
            $('.guest-age', $guest_model).text(age);
          }
          $('.guest-number', $guest_model).text(j+1);
          $('.guest-title', $guest_model).text(guest.Title);
          $('.guest-lastname', $guest_model).text(guest.LastName);
          $('.guest-firstname', $guest_model).text(guest.FirstName);
          
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
        
        $room_model.appendTo($order_service_package_rooms_tab);
      }
    } else {
      $('<h2>Nicio camera aleasa</h2>').appendTo($order_service_package_rooms_tab);
    }
    
    var $order_service_package_services_tab = $('>.tab-content>.order_service_package_services_tab', $card_block);
    if(service_data.ExtraServices && service_data.ExtraServices.length){
      for(var i=0;i<service_data.ExtraServices.length;i++){
        var room_number = i+1;
        var room = service_data.ExtraServices[i];
        var $room_model = $('#order-service-package-service-model').clone().removeAttr('id');
        var $room_card_header = $('>.card-header',$room_model);
        var $room_card_block = $('>.card-block',$room_model);
        
        var occupant_idxs = [];
        for(var kj=0; kj<room.Occupants.length; kj++){
          occupant_idxs.push(room.Occupants[kj].OccupationIdx);
        }
        
        $('.room-number', $room_card_header).text(room_number);
        // var room_price = parseFloat(room.Price);
        // $('.room-price', $room_card_header).text(format_price(Math.ceil(room_price), room.Currency));
        // $('.room-points', $room_card_header).text(Math.floor(room_price * 2/100));
        $('.room-price-points', $room_card_header).hide();
        
        $('.room-name', $room_card_block).text(room.Name);
        $('.room-board', $room_card_block).text(room.TypeDescription);
        // $('.room-info', $room_card_block).text(room.CityName);
        // $('.room-status', $room_card_block).text(room.UnitName);
        
        var room_adults = 0;
        var room_children = 0;
        
        var $guests_container = $('.room-occupancy-guests', $room_card_block);
        for(var j=0; j<service_data.Occupants.length;j++){
          var guest = service_data.Occupants[j];
          if(occupant_idxs.indexOf(guest.OccupationIdx) < 0){
            continue;
          }
          var service_id_prefix = 'service_package_' + id_suffix + '_service_' + i + '_guest_' + j;
          if(guest.Type == 'a'){
            room_adults++;
            var $guest_model = $('#order-service-hotel-room-guest-adult-model').clone().removeAttr('id');
            $('.guest-email', $guest_model).text(guest.Email);
            $('.guest-type-number', $guest_model).text(room_adults);
          } else {
            var $guest_model = $('#order-service-hotel-room-guest-child-model').clone().removeAttr('id');
            room_children++;
            $('.guest-type-number', $guest_model).text(room_children);
            
            var moment_birthdate = moment(guest.BirthDate,'Y-MM-DD');
            $('.guest-birth_date', $guest_model).text(moment_birthdate.format('DD.MM.Y'));
            var age = reference_moment.diff(moment_birthdate, 'years');
            $('.guest-age', $guest_model).text(age);
          }
          $('.guest-number', $guest_model).text(j+1);
          $('.guest-title', $guest_model).text(guest.Title);
          $('.guest-lastname', $guest_model).text(guest.LastName);
          $('.guest-firstname', $guest_model).text(guest.FirstName);
          
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
        
        $room_model.appendTo($order_service_package_services_tab);
      }
    } else {
      $('<h2>Niciun serviciu extra ales</h2>').appendTo($order_service_package_services_tab);
    }
    
    var $order_service_package_info_tab = $('>.tab-content>.order_service_package_info_tab', $card_block);
    $('.package-image', $order_service_package_info_tab).attr({
      'data-src': package.Image,
    });
    $('.package-name', $order_service_package_info_tab).text(package.Name);
    // $('.package-location', $order_service_package_info_tab).text(package.Address);
    $('.package-link', $order_service_package_info_tab).attr('href',package.link);
    // $('.package-stars', $order_service_package_info_tab).addClass('text-warning').html(" " + Array(parseInt(package.Stars) + 1).join('<i class="fa fa-star"></i>'));
    
    $('.package-info-description', $order_service_package_info_tab).text(package.Description);
    $('.package-info-facilities', $order_service_package_info_tab).text(package.ProjectName);
    // if(package.Phone && $.trim(package.Phone).length){
      // $('.package-info-phone', $order_service_package_info_tab).attr('href','tel:' + $.trim(package.Phone));
      // $('.package-info-phone>span', $order_service_package_info_tab).text($.trim(package.Phone));
    // }
    // if(package.Fax && $.trim(package.Fax).length){
      // $('.package-info-fax', $order_service_package_info_tab).attr('href','fax:' + $.trim(package.Fax));
      // $('.package-info-fax>span', $order_service_package_info_tab).text($.trim(package.Fax));
    // }
    // if(package.Email && $.trim(package.Email).length){
      // $('.package-info-email', $order_service_package_info_tab).attr('href','mailto:' + $.trim(package.Email));
      // $('.package-info-email>span', $order_service_package_info_tab).text($.trim(package.Email));
    // }
    $model.appendTo($container);
  }
  function interpretGetServiceTypeHotel(service_data, $container){
    var $model = $('#order-service-hotel-model').clone().removeAttr('id');
    var hotel = service_data.Hotel;
    var service_id = service_data.Id;
    var $card_header = $('>.card-header',$model);
    var $card_block = $('>.card-block',$model);
    var id_suffix = '_' + service_id;
    
    if(!service_data.Remarks || !service_data.Remarks.length){
      $('a.nav-link[data-tab=order_service_hotel_remarks_tab]', $card_header).remove();
      $('>.tab-content>.order_service_hotel_remarks_tab', $card_block).remove();
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
    
    if(service_data.Remarks && service_data.Remarks.length){
      for(var i=0;i<service_data.Remarks.length;i++){
        var remark = service_data.Remarks[i];
        var $li = $('<li class="list-group-item"/>');
        $li.addClass(remark.Category).text(remark.Name);
        $li.appendTo($('>.tab-content>.order_service_hotel_remarks_tab .remarks-list', $card_block));
      }
    }
    if(service_data.CancellationPolicies && service_data.CancellationPolicies.length){
      for(var i=0;i<service_data.CancellationPolicies.length;i++){
        var policy = service_data.CancellationPolicies[i];
        var cancellation_text = 'Anularea dupa data ' + moment(policy.Limit).format("dddd, MMMM DD.MM.Y, hh:mm:ss A") + ' presupune o penalizare de ' + format_price(parseFloat(policy.Amount), policy.Currency);
        var $li = $('<li class="list-group-item"/>');
        $li.text(cancellation_text);
        $li.appendTo($('>.tab-content>.order_service_hotel_cancel_tab .cancellation-policies-list', $card_block));
      }
    }
    var reference_moment = moment(service_data.Checkout,'Y-MM-DD');
    var $order_service_hotel_other_tab = $('>.tab-content>.order_service_hotel_other_tab', $card_block);
    
    $('.service-hotel-error_message', $order_service_hotel_other_tab).text((service_data.ErrorMessage ? service_data.ErrorMessage : '') + String.fromCharCode(160));
    $('.service-hotel-status_message', $order_service_hotel_other_tab).text((service_data.StatusMessage ? service_data.StatusMessage : '') + String.fromCharCode(160));
    $('.service-hotel-confirmation_no', $order_service_hotel_other_tab).text((service_data.ConfirmationNo ? service_data.ConfirmationNo : '') + String.fromCharCode(160));
    $('.service-hotel-system', $order_service_hotel_other_tab).text(service_data.System);
    $('.service-hotel-service_id', $order_service_hotel_other_tab).text(service_data.Id);
    $('.service-hotel-reservation_id', $order_service_hotel_other_tab).text((service_data.ReservationId ? service_data.ReservationId : '') + String.fromCharCode(160));
    
    $('.service-adults-number', $order_service_hotel_other_tab).text(service_data.Adults);
    $('.service-children-number', $order_service_hotel_other_tab).text(service_data.Children);
    $('.service-hotel-checkin', $order_service_hotel_other_tab).text(moment(service_data.Checkin,'Y-MM-DD').format('DD.MM.Y'));
    $('.service-hotel-checkout', $order_service_hotel_other_tab).text(moment(service_data.Checkout,'Y-MM-DD').format('DD.MM.Y'));
    $('.service-hotel-comments', $order_service_hotel_other_tab).text((service_data.Comments ? service_data.Comments : '') + String.fromCharCode(160));
    $('.service-hotel-price', $order_service_hotel_other_tab).text(format_price(Math.ceil(parseFloat(service_data.Amount)), service_data.Currency));
    
    var $order_service_hotel_rooms_tab = $('>.tab-content>.order_service_hotel_rooms_tab', $card_block);
    for(var i=0;i<service_data.Products.length;i++){
      var room_number = i+1;
      var room = service_data.Products[i];
      var $room_model = $('#order-service-hotel-room-model').clone().removeAttr('id');
      var $room_card_header = $('>.card-header',$room_model);
      var $room_card_block = $('>.card-block',$room_model);
      
      $('.room-number', $room_card_header).text(room_number);
      var room_price = parseFloat(room.Price);
      $('.room-price', $room_card_header).text(format_price(Math.ceil(room_price), room.Currency));
      $('.room-points', $room_card_header).text(Math.floor(room_price * 2/100));
      
      $('.room-name', $room_card_block).text(room.Name);
      $('.room-board', $room_card_block).text(room.Board);
      $('.room-info', $room_card_block).text(room.Info);
      $('.room-status', $room_card_block).text(room.Status);
      
      var room_adults = 0;
      var room_children = 0;
      
      var $guests_container = $('.room-occupancy-guests', $room_card_block);
      for(var j=0; j<room.Guests.length;j++){
        var service_id_prefix = 'service_hotel_' + id_suffix + '_room_' + i + '_guest_' + j;
        var guest = room.Guests[j];
        if(guest.Type == 'adult'){
          room_adults++;
          var $guest_model = $('#order-service-hotel-room-guest-adult-model').clone().removeAttr('id');
          $('.guest-email', $guest_model).text(guest.Email);
          $('.guest-type-number', $guest_model).text(room_adults);
        } else {
          var $guest_model = $('#order-service-hotel-room-guest-child-model').clone().removeAttr('id');
          room_children++;
          $('.guest-type-number', $guest_model).text(room_children);
          
          var moment_birthdate = moment(guest.BirthDate,'Y-MM-DD');
          $('.guest-birth_date', $guest_model).text(moment_birthdate.format('DD.MM.Y'));
          var age = reference_moment.diff(moment_birthdate, 'years');
          $('.guest-age', $guest_model).text(age);
        }
        $('.guest-number', $guest_model).text(j+1);
        $('.guest-title', $guest_model).text(guest.Title);
        $('.guest-lastname', $guest_model).text(guest.LastName);
        $('.guest-firstname', $guest_model).text(guest.FirstName);
        
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
    $('.hotel-image', $order_service_hotel_info_tab).attr({
      'data-src': hotel.Image,
    });
    $('.hotel-name', $order_service_hotel_info_tab).text(hotel.Name);
    $('.hotel-location', $order_service_hotel_info_tab).text(hotel.Address);
    $('.hotel-link', $order_service_hotel_info_tab).attr('href',hotel.link);
    $('.hotel-stars', $order_service_hotel_info_tab).addClass('text-warning').html(" " + Array(parseInt(hotel.Stars) + 1).join('<i class="fa fa-star"></i>'));
    
    $('.hotel-info-description', $order_service_hotel_info_tab).text(hotel.FullDesc ? hotel.FullDesc : hotel.ShortDesc);
    $('.hotel-info-facilities', $order_service_hotel_info_tab).text(hotel.Facilities);
    if(hotel.Phone && $.trim(hotel.Phone).length){
      $('.hotel-info-phone', $order_service_hotel_info_tab).attr('href','tel:' + $.trim(hotel.Phone));
      $('.hotel-info-phone>span', $order_service_hotel_info_tab).text($.trim(hotel.Phone));
    }
    if(hotel.Fax && $.trim(hotel.Fax).length){
      $('.hotel-info-fax', $order_service_hotel_info_tab).attr('href','fax:' + $.trim(hotel.Fax));
      $('.hotel-info-fax>span', $order_service_hotel_info_tab).text($.trim(hotel.Fax));
    }
    if(hotel.Email && $.trim(hotel.Email).length){
      $('.hotel-info-email', $order_service_hotel_info_tab).attr('href','mailto:' + $.trim(hotel.Email));
      $('.hotel-info-email>span', $order_service_hotel_info_tab).text($.trim(hotel.Email));
    }
    $model.appendTo($container);
  }
  function getOrderServiceDetailsSubmitCallback($form,resp,$error_container){
    console.log('getOrderServiceDetailsSubmitCallback', resp); 
    var $card = $form.closest('.order-service-model');
    var $card_header = $('>.card-header',$card);
    if(resp.status !== 'success'){
      $card_header.data('loaded_details', false);
      return true;
    }
    var $order_service_details = $form.parent();
    $order_service_details.empty();
    var service_data = resp.data;
    var service_type = service_data.Type;
    if(service_type == 'hotel'){
      interpretGetServiceTypeHotel(resp.data, $order_service_details);
    } else if(service_type == 'flight'){
      interpretGetServiceTypeFlight(resp.data, $order_service_details);
    } else if(service_type == 'package'){
      interpretGetServiceTypePackage(resp.data, $order_service_details);
    }
    $('.lazy',$order_service_details).lazy();
    return true;
  }
  $('#order_services').on('submit', 'form.order-service-details-form', function(){
    basicFormPostSubmit(this,this.action,getOrderServiceDetailsSubmitCallback,true);
  });
  function getOrderPaymentMethodsSubmitCallback($form,resp,$error_container){
    console.log('getOrderPaymentMethodsSubmitCallback', resp); 
    if(resp.status !== 'success'){
      $('#order_services_wrapper').show();
      $('#order_set_payment_method_form_wrapper').hide();
      $('#order_set_payment_method_form').hide();
      return true;
    }
    order.trip_order = resp.data;
    $('#order_set_payment_method_form_wrapper').show();
    
    // $('#order_set_payment_method_form_wrapper').show();
    $('#order_set_payment_method_form_payment_method > option[value]').remove();
    
    var selected_payment_method;
    var selected_payment_status;
    if(order.trip_order && order.trip_order.Payment){
      selected_payment_method = order.trip_order.Payment.Method;
      selected_payment_status = parseInt(order.trip_order.Payment.Status);
    }
    if(!selected_payment_status){
      $('#order_book_services_form').hide();
      $('#order_payment_method_set').hide();
      $('#order_set_payment_method_form').show();
      $('#order_set_payment_status_form').show();
      $('#order_payment_status_set').hide();
      $('#order_services_wrapper').show();
    } else {
      $('#order_services_wrapper').hide();
    }
    if(!selected_payment_method){
      $('#order_set_payment_status_form').hide();
    }
    if(selected_payment_method && selected_payment_status){
      $('#order_set_payment_method_form').hide();
      $('#order_payment_method_set_value').text(selected_payment_method);
      $('#order_payment_method_set').show();
      $('#order_set_payment_status_form').hide();
      $('#order_payment_status_set').show();
      
      if(selected_payment_status == 1){
        $('#order_book_services_form').show();
        $('#order_payment_status_set_1').show();
        $('#order_payment_status_set_2').hide();
      } else {
        $('#order_book_services_form').hide();
        $('#order_payment_status_set_1').hide();
        $('#order_payment_status_set_2').show();
      }
    }
    for(var i=0; i<resp.data.PaymentMethods.length; i++){
      var payment_method = resp.data.PaymentMethods[i];
      var $option = $('<option />').val(payment_method).text(payment_method);
      if(payment_method == selected_payment_method){
        $option.prop('selected', true);
      }
      $('#order_set_payment_method_form_payment_method').append($option);
    }
    return false;
  }
  $('#order_payment_methods_form').on('submit', function(){
    basicFormPostSubmit(this,this.action,getOrderPaymentMethodsSubmitCallback,true);
  });
  function setOrderPaymentMethodSubmitCallback($form,resp,$error_container){
    console.log('setOrderPaymentMethodSubmitCallback', resp); 
    if(resp.status !== 'success'){
      return true;
    }
    $('#order_payment_methods_form').submit();
    return true;
  }
  $('#order_set_payment_method_form').on('submit', function(){
    basicFormPostSubmit(this,this.action,setOrderPaymentMethodSubmitCallback,true);
  });
  function setOrderPaymentStatusSubmitCallback($form,resp,$error_container){
    console.log('setOrderPaymentStatusSubmitCallback', resp); 
    if(resp.status !== 'success'){
      return true;
    }
    // $('#order_payment_methods_form').submit();
    return true;
  }
  $('#order_set_payment_status_form').on('submit', function(){
    basicFormPostSubmit(this,this.action,setOrderPaymentStatusSubmitCallback,true);
  });
  function bookServicesSubmitCallback($form,resp,$error_container){
    console.log('bookServicesSubmitCallback', resp); 
    if(resp.status !== 'success'){
      return true;
    }
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
  