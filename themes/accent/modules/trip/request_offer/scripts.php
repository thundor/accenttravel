<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
?>
<div class="modal fade" id="modal_cerere_oferta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="d-flex flex-column justify-content-center" style="height:100%;pointer-events:none;">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 90%;pointer-events:auto;">
      <div class="modal-content">
        <div class="modal-header pt-2 pl-3 pb-2 pr-3">
          <h4 class="mb-0 mr-3">Cerere oferta <small id="cerere_oferta_title" style="font-weight:bold;"></small></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Inchide">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-2 pr-3 pl-3 pb-0">
          <form id="requestOfferForm" name="requestOfferForm" action="<?php echo site_url('trip/requestoffer/add');?>" class="request_offer_form" method="POST">
            <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
            <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
            <?php } ?>
            <input type="hidden" name="currency" value=""/>
            <input type="hidden" name="amount" value=""/>
            <input type="hidden" name="amount_hotel" value=""/>
            <input type="hidden" name="amount_flight" value=""/>
            <input type="hidden" name="ref_id" value=""/>
            <input type="hidden" name="itinerary_code" value=""/>
            <input type="hidden" name="type" value=""/>
            <input type="hidden" name="title" value=""/>
            <input type="hidden" name="data" value=""/>
            <div class="form-group row">
              <label for="request_offer_fullname" class="<?php echo $label_class; ?>">Numele dvs.</label>
              <div class="<?php echo $value_class; ?>">
                <input id="request_offer_fullname" required type="text" maxlength="255" name="fullname" placeholder="" class="form-control" value="<?php echo htmlspecialchars(trim($this->_ci->user->firstname . ' ' . $this->_ci->user->lastname)); ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="request_offer_email" class="<?php echo $label_class; ?>">Adresa email</label>
              <div class="<?php echo $value_class; ?>">
                <input id="request_offer_email" required type="email" maxlength="255" name="email" placeholder="" class="form-control" value="<?php echo htmlspecialchars($this->_ci->user->email); ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="request_offer_phone" class="<?php echo $label_class; ?>">Numar telefon</label>
              <div class="<?php echo $value_class; ?>">
                <input id="request_offer_phone" required type="text" maxlength="100" name="phone" placeholder="" class="form-control" value="<?php echo htmlspecialchars($this->_ci->user->phone); ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="request_offer_message" class="<?php echo $label_class; ?>">Informații</label>
              <div class="<?php echo $value_class; ?>">
                <textarea rows="8" id="request_offer_message" required name="message" placeholder="Introduceți informații relevante referitoare la cererea dumneavoastră" class="form-control"></textarea>
              </div>
            </div>
            <div class="form-group">
              <input type="hidden" name="newsletter" value="0">
              <label class="custom-control custom-checkbox">
                <input id="newsletter_enable" type="checkbox" name="newsletter" value="1" class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Vreau sa ma abonez la newsletterele saptamanale Accent Travel & Events</span>
              </label>
            </div>
            <p class="text-primary">Un consultant va procesa în cel mai scurt timp posibil cererea dumneavoastră și veți fi contactat pentru a primi cea mai buna ofertă.</p>
			<?php themeFunctions::loadAddons('captcha'); ?>
            <div class="form-group row">
              <label for="request_offer_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?> text-sm-right">
                <button type="submit" id="request_offer_submit" class="btn btn-success"><i class="fa fa-bell"></i> Trimite</button>
              </div>
            </div>
          </form>
          <div id="result_requestOfferForm" class="form-group mb-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
;(function($){
var request_offer_id;
var request_offer_obj = {
  ref_id : '',
  type : '',
  title : '',
  amount : '',
  currency : '',
  message : '',
  data : ''
};
function updateRequestOfferInfo(){
<?php 
$u0 = $this->_ci->uri->segment(0);
$u1 = $this->_ci->uri->segment(1);
if($u0 == 'trip') {
  if($u1=='hotel') {$hotel_details = $this->view_data['hotel_details'];
    ?>
    var selected_package = package_results[selected_package_index];
    request_offer_obj.type = 'hotel';
    request_offer_obj.currency = selected_package.Price.Currency;
    request_offer_obj.ref_id = <?php echo json_encode($hotel_details->Id); ?>;
    request_offer_obj.title = <?php echo json_encode($hotel_details->Name); ?>;
    request_offer_obj.amount = selected_package.Price.Amount;
    request_offer_obj.data = JSON.stringify(hotel_search_data);
    request_offer_obj.message = "Sunt interesat de o oferta cazare hotel " + <?php echo json_encode($hotel_details->Name); ?> + ": ";
    var start_date = moment(hotel_search_data.start_date,'Y-MM-DD');
    var end_date = moment(hotel_search_data.end_date,'Y-MM-DD');
    request_offer_obj.message += start_date.locale('ro').format("dddd, DD MMMM Y") + ' - ' + end_date.locale('ro').format("dddd, DD MMMM Y");
    var nights = end_date.diff(start_date,'days');
    request_offer_obj.message += ' (' + nights + ' ' + (nights == 1 ? 'noapte' : 'nopti') + ')';
    
    request_offer_obj.message += ', ';
    request_offer_obj.message += hotel_search_data.occupancy.length + ' ' + (hotel_search_data.occupancy.length == 1 ? 'camera' : 'camere');
    request_offer_obj.message += ', ';
    var adults = 0;
    var children = [];
    for(var i = 0; i<hotel_search_data.occupancy.length; i++){
      adults += parseInt(hotel_search_data.occupancy[i].adt);
      if(hotel_search_data.occupancy[i].chd){
        for(var j=0; j<hotel_search_data.occupancy[i].chd.length; j++){
          children.push(parseInt(hotel_search_data.occupancy[i].chd[j]) - 1);
        }
      }
    }
    request_offer_obj.message += adults + ' ' + (adults == 1 ? 'adult' : 'adulti');
    if(children.length){
      request_offer_obj.message += ', ';
      request_offer_obj.message += children.length + ' ' + (children.length == 1 ? 'copil' : 'copii');
      request_offer_obj.message += ' (' + (children.length == 1 ? 'varsta' : 'varste') + ': ' + children.join(', ') + ' ani)';
    }
	
    request_offer_obj.message += "\n";
    request_offer_obj.message += "Adresa: " + <?php echo json_encode($hotel_details->Address . ', ' . $hotel_details->CityName . ', ' . $hotel_details->CountryName); ?> + "";
    request_offer_obj.message += "\n";
	
    var $room_selections = $('#hotel-packages input[type=radio][name$="[option]"]:checked');
    request_offer_obj.message += "\n";
    for(var i = 0; i<hotel_search_data.occupancy.length; i++){
      request_offer_obj.message += "\n";
      request_offer_obj.message += "Camera " + (i+1) + ": ";
      var room_adults = parseInt(hotel_search_data.occupancy[i].adt);
      var room_children = [];
      if(hotel_search_data.occupancy[i].chd){
        for(var j=0; j<hotel_search_data.occupancy[i].chd.length; j++){
          room_children.push(parseInt(hotel_search_data.occupancy[i].chd[j]) - 1);
        }
      }
      request_offer_obj.message += room_adults + ' ' + (room_adults == 1 ? 'adult' : 'adulti');
      if(room_children.length){
        request_offer_obj.message += ', ';
        request_offer_obj.message += room_children.length + ' ' + (room_children.length == 1 ? 'copil' : 'copii');
        request_offer_obj.message += ' (' + (room_children.length == 1 ? 'varsta' : 'varste') + ': ' + room_children.join(', ') + ' ani)';
      }
      if(typeof $room_selections[i] !== 'undefined'){
        var room_selection = $room_selections[i];
        var $room_show = $(room_selection).closest('.roomShow');
        var room_code = $room_show.data('roomCode');
        var $package_room = $room_show.closest('.room-option');
        var package_room_code = $package_room.data('packageRoomCode');
        
        for (var i2 = 0; i2<selected_package.PackageRooms.PackageRoom.length; i2++){
          var package_room = selected_package.PackageRooms.PackageRoom[i2];
          if(package_room.PackageRoomCode == package_room_code){
            for (var j2 = 0; j2<package_room.RoomRefs.RoomRef.length; j2++){
              var room_ref = package_room.RoomRefs.RoomRef[j2];
              if(room_ref.RoomCode == room_code){
                request_offer_obj.message += ' - ' + room_ref.Name + ' (' + room_ref.Info + ')';
                break;
              }
            }
            break;
          }
        }
      }
    }
    <?php
    
  } elseif($u1=='flight') {
    
  } elseif($u1=='citybreak') {
    $hotel_details = $this->view_data['hotel_details'];
    ?>
    var selected_package = package_results[selected_package_index];
    request_offer_obj.type = 'citybreak';
    request_offer_obj.currency = selected_package.Price.Currency;
    request_offer_obj.ref_id = <?php echo json_encode($hotel_details->Id); ?>;
    request_offer_obj.title = <?php echo json_encode($hotel_details->Name); ?>;
    request_offer_obj.amount_hotel = selected_package.Price.Amount;
    request_offer_obj.amount_flight = flight_price;
    request_offer_obj.amount = parseFloat(selected_package.Price.Amount) + flight_price;
    request_offer_obj.data = JSON.stringify(citybreak_search_data);
    request_offer_obj.message = "Sunt interesat de o oferta city break " + citybreak_search_data.origin_city_name + " - " + citybreak_search_data.destination_city_name + ", hotel " + <?php echo json_encode($hotel_details->Name); ?> + ": ";
    var start_date = moment(citybreak_search_data.start_date,'Y-MM-DD');
    var end_date = moment(citybreak_search_data.end_date,'Y-MM-DD');
    request_offer_obj.message += start_date.locale('ro').format("dddd, DD MMMM Y") + ' - ' + end_date.locale('ro').format("dddd, DD MMMM Y");
    var nights = end_date.diff(start_date,'days');
    request_offer_obj.message += ' (' + nights + ' ' + (nights == 1 ? 'noapte' : 'nopti') + ')';
    
    request_offer_obj.message += ', ';
    request_offer_obj.message += citybreak_search_data.occupancy.length + ' ' + (citybreak_search_data.occupancy.length == 1 ? 'camera' : 'camere');
    request_offer_obj.message += ', ';
    var adults = 0;
    var children = [];
    for(var i = 0; i<citybreak_search_data.occupancy.length; i++){
      adults += parseInt(citybreak_search_data.occupancy[i].adt);
      if(citybreak_search_data.occupancy[i].chd){
        for(var j=0; j<citybreak_search_data.occupancy[i].chd.length; j++){
          children.push(parseInt(citybreak_search_data.occupancy[i].chd[j]) - 1);
        }
      }
    }
    request_offer_obj.message += adults + ' ' + (adults == 1 ? 'adult' : 'adulti');
    if(children.length){
      request_offer_obj.message += ', ';
      request_offer_obj.message += children.length + ' ' + (children.length == 1 ? 'copil' : 'copii');
      request_offer_obj.message += ' (' + (children.length == 1 ? 'varsta' : 'varste') + ': ' + children.join(', ') + ' ani)';
    }
	
    request_offer_obj.message += "\n";
    request_offer_obj.message += "Adresa: " + <?php echo json_encode($hotel_details->Address . ', ' . $hotel_details->CityName . ', ' . $hotel_details->CountryName); ?> + "";
    request_offer_obj.message += "\n";
	
    var $room_selections = $('#hotel-packages input[type=radio][name$="[option]"]:checked');
    request_offer_obj.message += "\n";
    for(var i = 0; i<citybreak_search_data.occupancy.length; i++){
      request_offer_obj.message += "\n";
      request_offer_obj.message += "Camera " + (i+1) + ": ";
      var room_adults = parseInt(citybreak_search_data.occupancy[i].adt);
      var room_children = [];
      if(citybreak_search_data.occupancy[i].chd){
        for(var j=0; j<citybreak_search_data.occupancy[i].chd.length; j++){
          room_children.push(parseInt(citybreak_search_data.occupancy[i].chd[j]) - 1);
        }
      }
      request_offer_obj.message += room_adults + ' ' + (room_adults == 1 ? 'adult' : 'adulti');
      if(room_children.length){
        request_offer_obj.message += ', ';
        request_offer_obj.message += room_children.length + ' ' + (room_children.length == 1 ? 'copil' : 'copii');
        request_offer_obj.message += ' (' + (room_children.length == 1 ? 'varsta' : 'varste') + ': ' + room_children.join(', ') + ' ani)';
      }
      if(typeof $room_selections[i] !== 'undefined'){
        var room_selection = $room_selections[i];
        var $room_show = $(room_selection).closest('.roomShow');
        var room_code = $room_show.data('roomCode');
        var $package_room = $room_show.closest('.room-option');
        var package_room_code = $package_room.data('packageRoomCode');
        
        for (var i2 = 0; i2<selected_package.PackageRooms.PackageRoom.length; i2++){
          var package_room = selected_package.PackageRooms.PackageRoom[i2];
          if(package_room.PackageRoomCode == package_room_code){
            for (var j2 = 0; j2<package_room.RoomRefs.RoomRef.length; j2++){
              var room_ref = package_room.RoomRefs.RoomRef[j2];
              if(room_ref.RoomCode == room_code){
                request_offer_obj.message += ' - ' + room_ref.Name + ' (' + room_ref.Info + ')';
                break;
              }
            }
            break;
          }
        }
      }
    }
    <?php
    
  } elseif($u1=='package') {
    $package_details = $this->view_data['package_details'];
    ?>
    request_offer_obj.type = 'package';
    request_offer_obj.currency = <?php echo json_encode($package_details->Currency); ?>;
    request_offer_obj.ref_id = <?php echo json_encode($package_details->Id); ?>;
    request_offer_obj.title = <?php echo json_encode($package_details->Name); ?>;
    request_offer_obj.amount = <?php echo json_encode($package_details->full_price); ?>;
    request_offer_obj.data = JSON.stringify(package_search_data);
    request_offer_obj.message = "Sunt interesat de o oferta vacanta " + <?php echo json_encode($package_details->Name); ?> + ": ";
    var $package_period_select = $('#package_period_select').select2_4('data')[0];
    request_offer_obj.message += $package_period_select.text;
    request_offer_obj.message += ', ';
    request_offer_obj.message += package_search_data.occupancy.length + ' ' + (package_search_data.occupancy.length == 1 ? 'camera' : 'camere');
    request_offer_obj.message += ', ';
    var adults = 0;
    var children = [];
    for(var i = 0; i<package_search_data.occupancy.length; i++){
      adults += parseInt(package_search_data.occupancy[i].adt);
      if(package_search_data.occupancy[i].chd){
        for(var j=0; j<package_search_data.occupancy[i].chd.length; j++){
          children.push(parseInt(package_search_data.occupancy[i].chd[j]) - 1);
        }
      }
    }
    request_offer_obj.message += adults + ' ' + (adults == 1 ? 'adult' : 'adulti');
    if(children.length){
      request_offer_obj.message += ', ';
      request_offer_obj.message += children.length + ' ' + (children.length == 1 ? 'copil' : 'copii');
      request_offer_obj.message += ' (' + (children.length == 1 ? 'varsta' : 'varste') + ': ' + children.join(', ') + ' ani)';
    }
    var $room_selections = $('#package_entries .package-entry-room-option');
    request_offer_obj.message += "\n";
	
    request_offer_obj.message += "Categorie: " + <?php echo json_encode($package_details->ProjectName); ?> + "";
    request_offer_obj.message += "\n";
    request_offer_obj.message += "Destinatie: " + $('#package_hotel_address').html() + "";
    request_offer_obj.message += "\n";
	
    for(var i = 0; i<package_search_data.occupancy.length; i++){
      request_offer_obj.message += "\n";
      request_offer_obj.message += "Camera " + (i+1) + ": ";
      var room_adults = parseInt(package_search_data.occupancy[i].adt);
      var room_children = [];
      if(package_search_data.occupancy[i].chd){
        for(var j=0; j<package_search_data.occupancy[i].chd.length; j++){
          room_children.push(parseInt(package_search_data.occupancy[i].chd[j]) - 1);
        }
      }
      request_offer_obj.message += room_adults + ' ' + (room_adults == 1 ? 'adult' : 'adulti');
      if(room_children.length){
        request_offer_obj.message += ', ';
        request_offer_obj.message += room_children.length + ' ' + (room_children.length == 1 ? 'copil' : 'copii');
        request_offer_obj.message += ' (' + (room_children.length == 1 ? 'varsta' : 'varste') + ': ' + room_children.join(', ') + ' ani)';
      }
      if(typeof $room_selections[i] !== 'undefined'){
        var room_selection = $room_selections[i];
        var roomType = $('option:selected', room_selection).text();
        request_offer_obj.message += ' - ' + roomType;
      }
    }
    var $extra_services = $('#package_entries .package-entry-extra input[type=checkbox]:checked');
    if($extra_services.length){
      request_offer_obj.message += "\n";
      request_offer_obj.message += "\n";
      request_offer_obj.message += "Servicii:";
      request_offer_obj.message += "\n";
      var services_names = {};
      for(var i=0; i<$extra_services.length; i++){
        var service_name = $.trim($($extra_services[i]).next('.extra-service-name').text());
        if(typeof services_names[service_name] === 'undefined'){
          services_names[service_name] = {
            adt: 0,
            chd: [],
          };
        }
        if($extra_services[i].name.indexOf('[c]') > -1){
          services_names[service_name].chd.push(parseInt($extra_services[i].value));
        } else{
          services_names[service_name].adt += 1;
        }
      }
      $.each(services_names, function(service_name){
        if(!services_names.hasOwnProperty(service_name)){
          return;
        }
        request_offer_obj.message += "\n";
        request_offer_obj.message += service_name;
        request_offer_obj.message += ': ';
        request_offer_obj.message += this.adt + ' ' + (this.adt == 1 ? 'adult' : 'adulti');
        if(this.chd.length){
          request_offer_obj.message += ', ';
          request_offer_obj.message += this.chd.length + ' ' + (this.chd.length == 1 ? 'copil' : 'copii');
          request_offer_obj.message += ' (' + (this.chd.length == 1 ? 'varsta' : 'varste') + ': ' + this.chd.join(', ') + ' ani)';
        }
      });
    }
    <?php
  }
} ?>
}
var submitting_form;
window.openRequestOfferModal = function (){
  updateRequestOfferInfo();
  var obj = request_offer_obj;
  request_offer_id = obj.id;
  var $this = $(this);
  $('#result_requestOfferForm').empty();
  var $form = $('form#requestOfferForm');
  var form = $form[0];
  form.ref_id.value = obj.type != 'flight' ? obj.ref_id : '';
  form.ref_id.value = obj.ref_id;
  form.type.value = obj.type;
  form.itinerary_code.value = ((obj.type == 'flight') || (obj.type == 'citybreak')) ? obj.itinerary_code : '';
  form.title.value = obj.title;
  form.amount.value = obj.amount;
  form.amount_hotel.value = obj.type == 'citybreak' ? obj.amount_hotel : '';
  form.amount_flight.value = obj.type == 'citybreak' ? obj.amount_flight : '';
  form.currency.value = obj.currency;
  form.data.value = obj.data;
  form.message.value = obj.message;
  
  $('#cerere_oferta_title').text(form.title.value);
  $('#modal_cerere_oferta').modal('show');
};
function requestOfferSubmitFormSubmitCallback($form,resp,$error_container){
  submitting_form = false;
  if(resp.status !== 'success'){
    return true;
  }
  setTimeout(function(){
    $('#modal_cerere_oferta').modal('hide');
  },1000);
  $('#' + request_offer_id).prop('disabled', true).addClass('disabled');
  return true;
}
$("#page-content").on("click", ".request-offer-button", function () {
  request_offer_id = this.id;
  var $this = $(this);
  openRequestOfferModal();
});
$(document).on('submit', 'form#requestOfferForm', function(e){
  e.preventDefault();
  if(submitting_form){
    return false;
  }
  if(!this.name || !this.name.length){
    return false;
  }
  submitting_form = true;
  basicFormPostSubmit(this,this.action,requestOfferSubmitFormSubmitCallback,true);
});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>