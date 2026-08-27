<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$google_maps_key = 'AIzaSyBEBBKL4GwgmqVIN5cbc7KpSPapec8jmxo';
?>
<div id="modalMapCity">
  <div class="row">
    <div class="col-sm-12">
      <span class="btn btn-outline-danger float-right mb-3">Inchide</span>
      <h4 id="modalMapCityTitle" class="float-left">&nbsp;</h4>
    </div>
    <?php /*
    <div class="col-sm-12 col-lg-3 mb-3">
      <img src="<?php echo $this->theme_url; ?>assets/images/barcelona.jpg"  alt="Barcelona, Spania" style="width:100%;" />
    </div>
    <div class="col-sm-12 col-lg-6  mb-3 modalMapCityDescription">
    <p>Barcelona is the capital of Catalonia and the second largest city in Spain, after Madrid, with a population of 1,621,537, being the sixth-most populous urban area in the European Union after Paris, London, the Ruhr, Madrid and Milan. It is located on the Mediterranean coast between the mouths of the rivers Llobregat and Besòs and is bounded to the west by the Serra de Collserola ridge.</p>
    <p>Barcelona is the capital of Catalonia and the second largest city in Spain, after Madrid, with a population of 1,621,537, being the sixth-most populous urban area in the European Union after Paris, London, the Ruhr, Madrid and Milan. It is located on the Mediterranean coast between the mouths of the rivers Llobregat and Besòs and is bounded to the west by the Serra de Collserola ridge.</p>
    </div>

    <div class="col-sm-12 col-lg-3  mb-3">
      <h4>Număr hoteluri: <span id="modalMapCityHotelCount">2416</span></h4>
      <h4>Atractii in <span id="modalMapCityName">Barcelona</span></h4>
      <ul class="list-unstyled" id="modalMapCityAttractions">
        <li>Atractia 1 </li>
        <li>Atractia 2 </li>
        <li>Atractia 3 </li>
        <li>Atractia 4 </li>
        <li>Atractia 5</li>
      </ul>
    </div>
    */ ?>
    <div class="col-sm-12">
      <div id="googleMap1" style="height:450px;"></div>
      <?php /* <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2859.3589151938504!2d28.629450015918646!3d44.22026847910596!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40baf06f7feeed13%3A0x3df8e75b98a91ec1!2sHotel+Perla!5e0!3m2!1sen!2sro!4v1503488624252" height="450" style="border:0; width:100%;" allowfullscreen></iframe> */ ?>
    </div>
  </div>
</div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key; ?>&callback=initMap">
</script>
<script type="text/javascript">
var notification_title;
function initMap(){
  console.log('googleMaps loaded');
}
var flights_result;
(function($){
$(document).on("click",'.inchideH', function () {
  $(this).parents(".boxHotel, .boxTicket").hide("slow");
});
function getFlightDurationString(string){
  var str_arr = string.split(':');
  var h = parseInt(str_arr[0]);
  var m = parseInt(str_arr[1]);
  return (h ? h + 'h ' : '') + m + 'min';
}
function getSegmentFlightNumberString(segment){
  var flight_number = segment.Flight.Number;
  var string = '<b>' + segment.Carrier.Marketing.Code + flight_number + '</b>';
  if(segment.Carrier.Operating && segment.Carrier.Marketing.Code != segment.Carrier.Operating.Code){
    string += ' Operat de ' + segment.Carrier.Operating._;
  }
  return string;
}
function interpretRouteAvailability(flight_type,route_ref, $box_ticket, flight_index){
  var comb = flight_type + route_ref;
  $('.row.' + (flight_type == 0 ? 'retur' : 'tur') + ' input[type=radio][name^=flight_choose]', $box_ticket).each(function(){
    var rmatches = this.name.match(/flight_choose\[(\d+)\]/);
    var rflight_type = rmatches[1];
    var rroute_ref = this.value;
    var rcomb = rflight_type + rroute_ref;
    var combination = flight_type == 0 ? comb + '|' + rcomb : rcomb + '|' + comb;
    var has_no_comb = flights_result.flights[flight_index].Combinations.indexOf(combination) < 0;
    var $parent_row = $(this).closest('.row.' + (flight_type == 0 ? 'retur' : 'tur'));
    if(!has_no_comb){
      $parent_row.removeClass('nocomb').removeAttr('title');
    } else {
      $parent_row.addClass('nocomb');
      $parent_row.attr('title','Aceasta ruta nu este compatibila cu ruta de ' + ( rflight_type == 0 ? 'retur' : 'plecare') + ' aleasa.');
      $(this).prop('checked', false).removeAttr('checked');
    }
  });
}
$('#flightResults').on('change','input[type=radio][name^=flight_choose]', function(){
  if(!$(this).is(':checked')){
    return;
  }
  if(flights_result.data.go_only){
    return;
  }
  var matches = this.name.match(/flight_choose\[(\d+)\]/);
  var flight_type = matches[1];
  var flight_index = $(this.form).data('flight_index');
  var route_ref = this.value;
  var $box_ticket = $(this).closest('.boxTicket').first();
  var $tichet_row = $(this).closest('.row.' + (flight_type == 0 ? 'tur' : 'retur')).first();
  if($tichet_row.hasClass('nocomb')){
    var $rows = $('.row.' + (flight_type == 0 ? 'tur' : 'retur'), $box_ticket);
    $rows.removeClass('nocomb');
    $rows.each(function(){
      var $row = $(this);
      if($row.data('uiTooltipOpen')){
        $row.removeData('uiTooltipTitle');
        $('#' + $row.data('uiTooltipId')).remove();
        $row.removeData('uiTooltipId');
      }
    });
  }
  interpretRouteAvailability(flight_type,route_ref, $box_ticket, flight_index);
});
function sablonDouaEscale(segments){
  var flight_1 = segments[0];
  var flight_1_company_code = flight_1.Carrier.Marketing.Code;
  var flight_1_company_image = getCompanyImageByCode(flight_1_company_code);
  var flight_1_company_name = flight_1.Carrier.Marketing._;
  var flight_1_departure_date = moment(flight_1.Origin.Date + ' ' + flight_1.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_arrival_date = moment(flight_1.Destination.Date + ' ' + flight_1.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_duration_in_minutes = flight_1_arrival_date.diff(flight_1_departure_date,'minutes');
  var flight_1_duration_hours = parseInt(flight_1_duration_in_minutes/60);
  var flight_1_duration_minutes = flight_1_duration_in_minutes - 60 * flight_1_duration_hours;
  var flight_2 = segments[1];
  var flight_2_company_code = flight_2.Carrier.Marketing.Code;
  var flight_2_company_image = getCompanyImageByCode(flight_2_company_code);
  var flight_2_company_name = flight_2.Carrier.Marketing._;
  var flight_2_departure_date = moment(flight_2.Origin.Date + ' ' + flight_2.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_2_wait_duration_in_minutes = flight_2_departure_date.diff(flight_1_arrival_date,'minutes');
  var flight_1_2_wait_duration_hours = parseInt(flight_1_2_wait_duration_in_minutes/60);
  var flight_1_2_wait_duration_minutes = flight_1_2_wait_duration_in_minutes - 60 * flight_1_2_wait_duration_hours;
  var flight_2_arrival_date = moment(flight_2.Destination.Date + ' ' + flight_2.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_2_duration_in_minutes = flight_2_arrival_date.diff(flight_2_departure_date,'minutes');
  var flight_2_duration_hours = parseInt(flight_2_duration_in_minutes/60);
  var flight_2_duration_minutes = flight_2_duration_in_minutes - 60 * flight_2_duration_hours;
  var flight_3 = segments[2];
  var flight_3_company_code = flight_3.Carrier.Marketing.Code;
  var flight_3_company_image = getCompanyImageByCode(flight_3_company_code);
  var flight_3_company_name = flight_3.Carrier.Marketing._;
  var flight_3_departure_date = moment(flight_3.Origin.Date + ' ' + flight_3.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_2_3_wait_duration_in_minutes = flight_3_departure_date.diff(flight_2_arrival_date,'minutes');
  var flight_2_3_wait_duration_hours = parseInt(flight_2_3_wait_duration_in_minutes/60);
  var flight_2_3_wait_duration_minutes = flight_2_3_wait_duration_in_minutes - 60 * flight_2_3_wait_duration_hours;
  var flight_3_arrival_date = moment(flight_3.Destination.Date + ' ' + flight_3.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_3_duration_in_minutes = flight_3_arrival_date.diff(flight_3_departure_date,'minutes');
  var flight_3_duration_hours = parseInt(flight_3_duration_in_minutes/60);
  var flight_3_duration_minutes = flight_3_duration_in_minutes - 60 * flight_3_duration_hours;
  
  return '\
  <div class="infoEscale col-12" style="display: none;">\
    <div class="infoPE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/plecareW.png" alt="plecare">\
      <span>\
        <strong>' + flight_1_departure_date.format('HH:mm') + '</strong>\
        <br />' + 
        flight_1.Origin.Airport._ +
        '<br />' + 
        flight_1.Origin.Airport.City +
      '</span>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector"><br>\
        <span>' +
          (flight_1_company_image ? '<img src="' + flight_1_company_image + '" title="' + flight_1_company_name + '" alt="' + flight_1_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_1.Aircraft ? flight_1.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_1) + '\
        </span>\
      </div>\
    </div>\
    <div class="timeEsc1">\
      <div class="row">\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/sosire.png" alt="sosire">\
          <br />\
          <span>' + flight_1_arrival_date.format('HH:mm') + '</span>\
          <br />\
          <span>Sosire in</span>\
        </div>\
        <div class="col-6">\
          <img src="<?php echo $this->theme_url; ?>assets/images/waiting.png" alt="asteptare">\
          <br />\
          <span>' + (flight_1_2_wait_duration_hours ? flight_1_2_wait_duration_hours + 'h:' : '') + flight_1_2_wait_duration_minutes + 'min' + '</span>\
          <br />\
          <span><strong>' + flight_1.Destination.Airport.City + ', ' + flight_1.Destination.Airport._ + '</strong></span>\
        </div>\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="sosire">\
          <br />\
          <span>' + flight_2_departure_date.format('HH:mm') + '</span>\
          <br />\
          <span>Plecare din</span>\
        </div>\
      </div>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector" alt="Conector">\
        <br />\
        <span>' +
          (flight_2_company_image ? '<img src="' + flight_2_company_image + '" title="' + flight_2_company_name + '" alt="' + flight_2_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_2.Aircraft ? flight_2.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_2) + '\
        </span>\
      </div>\
    </div>\
    <div class="timeEsc1">\
      <div class="row">\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/sosire.png" alt="sosire">\
          <br />\
          <span>' + flight_2_arrival_date.format('HH:mm') + '</span>\
          <br />\
          <span>Sosire in</span>\
        </div>\
        <div class="col-6">\
          <img src="<?php echo $this->theme_url; ?>assets/images/waiting.png" alt="asteptare">\
          <br />\
          <span>' + (flight_2_3_wait_duration_hours ? flight_2_3_wait_duration_hours + 'h:' : '') + flight_2_3_wait_duration_minutes + 'min' + '</span>\
          <br />\
          <span><strong>' + flight_2.Destination.Airport.City + ', ' + flight_2.Destination.Airport._ + '</strong></span>\
        </div>\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="sosire">\
          <br />\
          <span>' + flight_3_departure_date.format('HH:mm') + '</span>\
          <br />\
          <span>Plecare din</span>\
        </div>\
      </div>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector" alt="Conector">\
        <br />\
        <span>' +
          (flight_3_company_image ? '<img src="' + flight_3_company_image + '" title="' + flight_3_company_name + '" alt="' + flight_3_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_3.Aircraft ? flight_3.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_3) + '\
        </span>\
      </div>\
    </div>\
    <div class="infoSE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/sosireW.png" alt="sosire">\
      <span>\
        <strong>' + flight_3_arrival_date.format('HH:mm') + '</strong>\
        <br />' +
        flight_3.Destination.Airport.City + 
        ',\
        <br />'
        + flight_3.Destination.Airport._ +
      '</span>\
    </div>\
  </div>\
';
}
function sablonOEscala(segments){
  var flight_1 = segments[0];
  var flight_1_company_code = flight_1.Carrier.Marketing.Code;
  var flight_1_company_image = getCompanyImageByCode(flight_1_company_code);
  var flight_1_company_name = flight_1.Carrier.Marketing._;
  var flight_1_departure_date = moment(flight_1.Origin.Date + ' ' + flight_1.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_arrival_date = moment(flight_1.Destination.Date + ' ' + flight_1.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_duration_in_minutes = flight_1_arrival_date.diff(flight_1_departure_date,'minutes');
  var flight_1_duration_hours = parseInt(flight_1_duration_in_minutes/60);
  var flight_1_duration_minutes = flight_1_duration_in_minutes - 60 * flight_1_duration_hours;
  var flight_2 = segments[1];
  var flight_2_company_code = flight_2.Carrier.Marketing.Code;
  var flight_2_company_name = flight_2.Carrier.Marketing._;
  var flight_2_company_image = getCompanyImageByCode(flight_2_company_code);
  var flight_2_departure_date = moment(flight_2.Origin.Date + ' ' + flight_2.Origin.Time,'Y-MM-DD HH:mm:ss');
  var flight_1_2_wait_duration_in_minutes = flight_2_departure_date.diff(flight_1_arrival_date,'minutes');
  var flight_1_2_wait_duration_hours = parseInt(flight_1_2_wait_duration_in_minutes/60);
  var flight_1_2_wait_duration_minutes = flight_1_2_wait_duration_in_minutes - 60 * flight_1_2_wait_duration_hours;
  var flight_2_arrival_date = moment(flight_2.Destination.Date + ' ' + flight_2.Destination.Time,'Y-MM-DD HH:mm:ss');
  var flight_2_duration_in_minutes = flight_2_arrival_date.diff(flight_2_departure_date,'minutes');
  var flight_2_duration_hours = parseInt(flight_2_duration_in_minutes/60);
  var flight_2_duration_minutes = flight_2_duration_in_minutes - 60 * flight_2_duration_hours;
  return '<div class="infoEscale" style="display: none;">\
    <div class="infoPE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/plecareW.png" alt="plecare">\
      <span>\
        <strong>' + flight_1_departure_date.format('HH:mm') + '</strong>\
        <br />' + 
        flight_1.Origin.Airport._ +
        '<br />' + 
        flight_1.Origin.Airport.City +
      '</span>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector">\
        <br />\
        <span>' +
          (flight_1_company_image ? '<img src="' + flight_1_company_image + '" title="' + flight_1_company_name + '" alt="' + flight_1_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_1.Aircraft ? flight_1.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_1) + '\
        </span>\
      </div>\
    </div>\
    <div class="timeEsc1" style="width: 60%;">\
      <div class="row">\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/sosire.png" alt="sosire">\
          <br />\
          <span>' + flight_1_arrival_date.format('HH:mm') + '</span>\
          <br />\
          <span>Sosire in</span>\
        </div>\
        <div class="col-6">\
          <img src="<?php echo $this->theme_url; ?>assets/images/waiting.png" alt="asteptare">\
          <br />\
          <span>' + (flight_1_2_wait_duration_hours ? flight_1_2_wait_duration_hours + 'h:' : '') + flight_1_2_wait_duration_minutes + 'min' + '</span>\
          <br />\
          <span><strong>' + flight_1.Destination.Airport.City + ', ' + flight_1.Destination.Airport._ + '</strong></span>\
        </div>\
        <div class="col-3">\
          <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="sosire">\
          <br />\
          <span>' + flight_2_departure_date.format('HH:mm') + '</span>\
          <br />\
          <span>Plecare din</span>\
        </div>\
      </div>\
      <div class="durataZbor">\
        <img src="<?php echo $this->theme_url; ?>assets/images/conector.png" class="conector" alt="Conector">\
        <br />\
        <span>' +
          (flight_2_company_image ? '<img src="' + flight_2_company_image + '" title="' + flight_2_company_name + '" alt="' + flight_2_company_name + '" />' : '') +
          '<br />\
          <strong>\
            Tip avion\
          </strong>:\
          ' + ( flight_2.Aircraft ? flight_2.Aircraft._ : '' ) + '\
          <br />\
          <strong>\
            Numar zbor\
          </strong>:\
          ' + getSegmentFlightNumberString(flight_2) + '\
        </span>\
      </div>\
    </div>\
    <div class="infoSE">\
      <img src="<?php echo $this->theme_url; ?>assets/images/sosireW.png" alt="sosire">\
      <span>\
        <strong>' + flight_2_arrival_date.format('HH:mm') + '</strong>\
        <br />' +
        flight_2.Destination.Airport.City + 
        ',\
        <br />'
        + flight_2.Destination.Airport._ +
      '</span>\
    </div>\
  </div>';
}
function createCalendar(){
  var go_only = flights_result.data.go_only;
  var $table = $('<table cellpadding="0" cellspacing="0" class="table3Days ' + (go_only ? 'go_only' : 'with_return') + '" />');
  $('#calendarFlights').empty();
  $table.appendTo($('#calendarFlights'));
  var $tbody = $('<tbody />');
  $tbody.appendTo($table);
  var departure_date = moment(flights_result.data.departure_date);
  if(!go_only){
    var return_date = moment(flights_result.data.return_date);
  }
  var $tr_model = $('<tr />');
  var $th_model = $('<th />');
  var $td_model = $('<td />');
  
  var calendar_flights = {};
  var calendar_flights_cheapest;
  
  var response = flights_result.response;
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
      if(!go_only && route_types[1]){
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
        $cell.css('text-transform','capitalize');
      } else {
        $cell = $td_model.clone();
      }
      if(x===-4 && y===-4){
        $cell.addClass('showDir');
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
          var $cell_a = $('<a />').html(format_price(Math.ceil(flight.price),flight.currency));
          $cell_a.appendTo($cell);
          var $cell_tooltip = $('\
          <div class="toolTipPrice" style="display: none;">\
            <div class="topTitle">\
              <p>Tarif total zbor ' + (go_only ? 'dus' : 'dus-intors') + '</p>\
            </div>\
            <div class="priceTool">\
              <span class="price">' + format_price(Math.ceil(flight.price),flight.currency) + '</span>\
            </div>\
            <div class="firstLine">\
              <span>' +
                ((company_image = getCompanyImageByCode(flight.departure.Segment[0].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.departure.Segment[0].Carrier.Marketing._ + '" title="' + flight.departure.Segment[0].Carrier.Marketing._ + '" />' : '') +
                '<br\>' +
                flight.departure.Segment[0].Carrier.Marketing._ +
              '</span>\
              <p>\
                <strong>' + flight.departure.Segment[0].Origin.Airport._ + ', ' + flight.departure.Segment[0].Origin.Airport.Code + '</strong><br>\
                <strong><i class="fa fa-plane"></i> Plecare</strong>: ' + (dep_dep = moment(flight.departure.Segment[0].Origin.Date + ' ' + flight.departure.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y') + ', <br><i class="fa fa-clock-o"></i> Ora: ' + dep_dep.format('HH:mm') + '\
              </p>\
            </div>\
            <div class="secondLine">\
              <span>' +
                ((company_image = getCompanyImageByCode(flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing._ + '" title="' + flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing._ + '" />' : '') +
                '<br\>' +
                flight.departure.Segment[flight.departure.Segment.length-1].Carrier.Marketing._ +
              '</span>\
              <p>\
                <strong>' + flight.departure.Segment[flight.departure.Segment.length-1].Destination.Airport._ + ', ' + flight.departure.Segment[flight.departure.Segment.length-1].Destination.Airport.Code + '</strong><br>\
                <strong><i class="fa fa-plane"></i> Sosire</strong>: ' + (dep_dep = moment(flight.departure.Segment[flight.departure.Segment.length-1].Destination.Date + ' ' + flight.departure.Segment[flight.departure.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y') + ', <br><i class="fa fa-clock-o"></i> Ora: ' + dep_dep.format('HH:mm') + '\
              </p>\
            </div>' + (go_only ? '' : '\
            <div class="firstLine">\
              <span>' +
                ((company_image = getCompanyImageByCode(flight.return.Segment[0].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.return.Segment[0].Carrier.Marketing._ + '" title="' + flight.return.Segment[0].Carrier.Marketing._ + '" />' : '') +
                '<br\>' +
                flight.return.Segment[0].Carrier.Marketing._ +
              '</span>\
              <p>\
                <strong>' + flight.return.Segment[0].Origin.Airport._ + ', ' + flight.return.Segment[0].Origin.Airport.Code + '</strong><br>\
                <strong><i class="fa fa-plane"></i> Plecare</strong>: ' + (dep_dep = moment(flight.return.Segment[0].Origin.Date + ' ' + flight.return.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y') + ', <br><i class="fa fa-clock-o"></i> Ora: ' + dep_dep.format('HH:mm') + '\
              </p>\
            </div>\
            <div class="secondLine">\
              <span>' +
                ((company_image = getCompanyImageByCode(flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing.Code) ) ? '<img src="' + company_image + '" alt="' + flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing._ + '" title="' + flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing._ + '" />' : '') +
                '<br\>' +
                flight.return.Segment[flight.return.Segment.length-1].Carrier.Marketing._ +
              '</span>\
              <p>\
                <strong>' + flight.return.Segment[flight.return.Segment.length-1].Destination.Airport._ + ', ' + flight.return.Segment[flight.return.Segment.length-1].Destination.Airport.Code + '</strong><br>\
                <strong><i class="fa fa-plane"></i> Sosire</strong>: ' + (dep_dep = moment(flight.return.Segment[flight.return.Segment.length-1].Destination.Date + ' ' + flight.return.Segment[flight.return.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss')).format('DD.MM.Y') + ', <br><i class="fa fa-clock-o"></i> Ora: ' + dep_dep.format('HH:mm') + '\
              </p>\
            </div>') + '\
          </div>');
          $cell_tooltip.appendTo($cell);
          if(typeof calendar_flights_cheapest !== 'undefined' && calendar_flights_cheapest == calendar_flights[dep_date_f][ret_date_f].price){
            $cell.addClass('lowestPrice').attr({
              'title':"Cel mai mic pret!",
              'data-toggle':"tooltip",
            });
          }
        }
      } 
      $cell.appendTo($tr);
    }
    $tr.appendTo($tbody);
  }
  var flex_date = $('#dateFlexZbor').is(':checked');
  if(flex_date){
    $('#flightsFilterForDateDeparture').val('');
    $('#flightsFilterForDateReturn').val('');
  } else {
    var $calendar_elem = $('td[data-return=' + (flights_result.data.go_only ? '0000-00-00' : flights_result.data.return_date) + '][data-departure=' + flights_result.data.departure_date + '] > a');
    if($calendar_elem.length && !$calendar_elem.hasClass('active')){
      activateCalendarDate($calendar_elem);
    }
  }
}

function getCompanyImageByCode(company_code){
  var company_image;
  var company_index = flights_result.results.companies_indexes[company_code];
  if(typeof company_index !== 'undefined'){
    company_image = flights_result.results.companies[company_index].img;
  }
  return company_image;
}
function bookFlightTicket(elem){
  var $box_ticket = $(elem).closest('.boxTicket');
  var flight_index = $box_ticket.data('flight_index');
  var flight = flights_result.flights[flight_index];
  var $departure_checked_elem = $('input[name^="flight_choose[0]"]:checked').first();
  if(!$departure_checked_elem.length){
    alert('Alegeti o optiune pentru plecare.');
    return false;
  }
  var combination = '0' + $departure_checked_elem.val();
  if(!flights_result.data.go_only){
    var $return_checked_elem = $('input[name^="flight_choose[1]"]:checked').first();
    if(!$return_checked_elem.length){
      alert('Alegeti o optiune pentru retur.');
      return false;
    }
    combination += '|1' + $return_checked_elem.val();
  }
  var combination_index = flight.Combinations.indexOf(combination);
  
  if(combination_index < 0){
    alert('Combinatie invalida');
    return false;
  }
  $(elem).attr('href', '/trip/flight?code=' + flights_result.data.code + '&itinerary_code=' + flight.ItineraryCode + ':' + combination_index);
  return true;
}
function showFlightsResults(){
  var flights = flights_result.flights;
  var $flight_results = $('#flightResults');
  $flight_results.empty();
  for(var i=0; i<flights.length;i++){
    var flight = flights[i];
    var combination_selected = flight.Combinations[0];
    var combination_arr = combination_selected.split('|');
    var route_types = flight.Routes;
    var flights_str_arr = [];
    for(var j=0; j<route_types.length; j++){
      var route_type = route_types[j];
      var routes = route_type.Route;
      for(var k=0; k<routes.length; k++){
        var route = routes[k];
        var cabin_types = [];
        var company_code = route.Segment[0].Carrier.Marketing.Code;
        var company_name = route.Segment[0].Carrier.Marketing._;
        var departure_date = moment(route.Segment[0].Origin.Date + ' ' + route.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss');
        var arrival_date = moment(route.Segment[route.Segment.length-1].Destination.Date + ' ' + route.Segment[route.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss');
        var company_image = getCompanyImageByCode(company_code);
        var escale = route.Segment.length - 1;
        for(var l=0; l<=escale; l++){
          var escala = route.Segment[l];
          if(cabin_types.indexOf(escala.Flight.CabinType) < 0){
            cabin_types.push(escala.Flight.CabinType);
          }
        }
        var ischecked = combination_arr[j] == '' + j + route.Ref;
        if(!j){
          var box_ticket_row_html = '<div class="row tur" \
              data-company="' + company_code + '"\
              data-stops="' + escale + '"\
              >\
              <div class="col-11 col-sm-3 dataPL">\
                <div class="iconsFlight">\
                  <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="Data plecare" />' +
                  (company_image ? '<img src="' + company_image + '" alt="' + company_name + '" title="' + company_name + '" />' : '') +
                '</div>\
                <p style="text-transform:capitalize;">\
                  <span>PLECARE</span>\
                  <br/>' +
                  departure_date.locale('ro').format('dddd') +
                  '<br/>\
                  <strong style="text-transform:capitalize;">' + departure_date.locale('ro').format('D MMM') + '</strong>\
                  , ' + departure_date.format('Y') +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 oraPL">\
                <p>\
                  <span>\
                    <strong>' +
                      departure_date.format('HH:mm') +
                    '</strong>\
                  </span>\
                  <br />' + 
                  route.Segment[0].Origin.Airport._ +
                  '<br />' + 
                  route.Segment[0].Origin.Airport.City +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 escale">' +
                (!escale ? '<span class="text-center">Zbor Direct</span>' :
                  '<span class="text-center">' + escale + ' ' + (escale > 1 ? 'Escale' : 'Escala') + '</span>\
                  <a class="warning detEscale" name="detEscale"><i class="fa fa-info-circle"></i> Detalii</a>') +
                '<br /><small class="text-center">Durata zbor <i class="fa fa-clock-o"></i> ' + getFlightDurationString(route.Duration) + '</small>' +
              '</div>\
              <div class="col-4 col-sm-3 oraPL">\
                <p>\
                  <span>\
                    <strong style="text-transform:capitalize;">' +
                      arrival_date.format('HH:mm') + '/' +arrival_date.locale('ro').format('D MMM') + 
                    '</strong>\
                    , ' + arrival_date.format('Y') + 
                  '</span>\
                  <br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport._ +
                  '<br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport.City +
                '</p>\
              </div>\
              <div class="col-12 col-sm-2 alegeBT">\
                <p class="text-center">\
                  <label for="flight_result_choose_' + i + '_'+ j + '_' + k + '">Alege</label>\
                  <br >\
                  <input type="radio" ' + (ischecked ? 'checked="checked"' : '') + ' name="flight_choose[' + j + ']" value="' + route.Ref + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" data-hash="' + route.Hash + '" required />\
                </p>\
              </div>' +
              (escale === 1 ? sablonOEscala(route.Segment) : '') + 
              (escale === 2 ? sablonDouaEscale(route.Segment) : '') + 
            '</div>';
			  box_ticket_row_html += '<span class="familyLight fontSize12 ">';
			  box_ticket_row_html += '<b>Clasa</b>: ' + cabin_types.join(', ') +',';
			if(flight && flight.BrandedFare && flight.BrandedFare.BrandDetails  && flight.BrandedFare.BrandDetails.length && flight.BrandedFare.BrandDetails[j] && (flight.BrandedFare.BrandDetails[j].Code || flight.BrandedFare.BrandDetails[j].Description)){
				box_ticket_row_html +=  (flight.BrandedFare.BrandDetails[j].Code ? ' <b>Fare Family</b>: ' + flight.BrandedFare.BrandDetails[j].Code + '' : '')
				+ (flight.BrandedFare.BrandDetails[j].Description ? ', ' + flight.BrandedFare.BrandDetails[j].Description + '' : '')
			}
			  box_ticket_row_html += '</span><br />';
          flights_str_arr.push(box_ticket_row_html);
        } else {
          var is_available = true;
          if(!ischecked){
            if(flight.Combinations.indexOf(combination_arr[0] + '|' + '1' + route.Ref) < 0){
              is_available = false;
            }
          }
          var box_ticket_row_html = '<div class="row retur' + (j && !k ? ' mt-4' : '') + (is_available ? '' : ' nocomb') + '"' + (is_available ? '' : ' title="Aceasta ruta nu este compatibila cu ruta de plecare aleasa."') + '>\
              <div class="col-11 col-sm-3 dataPL">\
                <div class="iconsFlight">\
                  <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="Data plecare" />' +
                  (company_image ? '<img src="' + company_image + '" alt="' + company_name + '" title="' + company_name + '" />' : '') +
                '</div>\
                <p style="text-transform:capitalize;">\
                  <span>RETUR</span>\
                  <br/>\
                  <small>' + departure_date.locale('ro').format('dddd') + '</small>\
                  <br/>\
                  <strong>' + departure_date.locale('ro').format('D MMM') + '</strong>\
                  , ' + departure_date.format('Y') +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 oraPL">\
                <p>\
                  <span>\
                    <strong>' +
                      departure_date.format('HH:mm') +
                    '</strong>\
                  </span>\
                  <br />' + 
                  route.Segment[0].Origin.Airport._ +
                  '<br />' + 
                  route.Segment[0].Origin.Airport.City +
                '</p>\
              </div>\
              <div class="col-4 col-sm-2 escale">' +
                (!escale ? '<span class="text-center">Zbor Direct</span>' :
                  '<span class="text-center">' + escale + ' ' + (escale > 1 ? 'Escale' : 'Escala') + '</span>\
                  <a class="warning detEscale" name="detEscale"><i class="fa fa-info-circle"></i> Detalii</a>') +
                '<br /><small class="text-center">Durata zbor <i class="fa fa-clock-o"></i> ' + getFlightDurationString(route.Duration) + '</small>' +
              '</div>\
              <div class="col-4 col-sm-3 oraPL">\
                <p>\
                  <span>\
                    <strong style="text-transform:capitalize;">' +
                      arrival_date.format('HH:mm') + '/' +arrival_date.locale('ro').format('D MMM') + 
                    '</strong>\
                    , ' + arrival_date.format('Y') + 
                  '</span>\
                  <br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport._ +
                  '<br />' + 
                  route.Segment[route.Segment.length-1].Destination.Airport.City +
                '</p>\
              </div>\
              <div class="col-12 col-sm-2 alegeBT">\
                <p class="text-center">\
                  <label for="flight_result_choose_' + i + '_'+ j + '_' + k + '">Alege</label>\
                  <br >\
                  <input type="radio" ' + (ischecked ? 'checked="checked"' : '') + ' name="flight_choose[' + j + ']" value="' + route.Ref + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" data-hash="' + route.Hash + '" required />\
                </p>\
              </div>' +
              (escale === 1 ? sablonOEscala(route.Segment) : '') + 
              (escale === 2 ? sablonDouaEscale(route.Segment) : '') + 
            '</div>';
			  box_ticket_row_html += '<span class="familyLight fontSize12 ">';
			  box_ticket_row_html += '<b>Clasa</b>: ' + cabin_types.join(', ') +',';
			if(flight && flight.BrandedFare && flight.BrandedFare.BrandDetails  && flight.BrandedFare.BrandDetails.length && flight.BrandedFare.BrandDetails[j] && (flight.BrandedFare.BrandDetails[j].Code || flight.BrandedFare.BrandDetails[j].Description)){
				box_ticket_row_html +=  (flight.BrandedFare.BrandDetails[j].Code ? ' <b>Fare Family</b>: ' + flight.BrandedFare.BrandDetails[j].Code + '' : '')
				+ (flight.BrandedFare.BrandDetails[j].Description ? ', ' + flight.BrandedFare.BrandDetails[j].Description + '' : '')
			}
			  box_ticket_row_html += '</span><br />';
          flights_str_arr.push(box_ticket_row_html);
        }
      }
    }
    
    var box_html = '<form target="_BLANK" class="boxTicket" action="<?php echo site_url('trip/flight/booking'); ?>" method="POST" data-flight_index="' + i + '" data-price="' + flight.Price + '">';
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    box_html +='<input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />';
    <?php } ?>
    box_html +='<div class="row">\
          <div class="col-12 col-sm-12 col-lg-10">\
            <div class="dashedBB mb-3">\
              <h4>\
                <strong>' + route_types[0].Route[0].Segment[0].Origin.Airport.City + '</strong>\
                -\
                <strong>' + route_types[0].Route[0].Segment[route_types[0].Route[0].Segment.length-1].Destination.Airport.City + '</strong>\
              </h4>\
            </div>' +
            flights_str_arr.join('<hr />') +
          '</div>\
          <div class="col-12 col-sm-12 col-lg-2 blD">\
            <p class="text-center pretFL">\
              <span class="bigPrice" style="display:none;">\
              Tarif initial\
              <br />\
              <strong>11.999 <?php echo $this->_ci->currency_symbol; ?>;</strong>\
              </span>\
              <span class="pretSrcH">' + format_price(flight.Price, flight.Currency) + '</span>\
              </p>\
              <input type="hidden" name="code" value="' + flights_result.data.code + '"/>\
              <input type="hidden" name="combinations" value="' + flight.Combinations.join(',') + '"/>\
              <input type="hidden" name="itinerary_code" value="' + flight.ItineraryCode + '"/>\
              <button type="submit" class="btn btn-block btn-primary btnTicket">REZERVA</button>\
              <a style="display:none" href="javascript:void(0)" onclick="window.location.href = $(this).closest(\'form\')[0].action + \'_backend?\' + (decodeURIComponent($(this).closest(\'form\').serialize().replace(/csrf_.*?(&|$)/, \'\')))" class="btn btn-block btn-danger btnTicket">REZERVA 2</a>\
              <div class="text-center mt-2">\
              <a class="notification-button btn btn-success rounded-0 text-white" data-toggle="tooltip" title="Notifica-ma cand pretul va scadea cu cel putin 10%"><i class="fa fa-bell" style="color:#fff;"></i> Alerta pret</a>\
              </div>\
          </div>\
          <i class="fa fa-close inchideH" />\
        </div>\
      </form>';
    $flight_box = $(box_html);
    $('.notification-button', $flight_box).attr({
      'id': 'button_notification_flight_' + i,
      'data-type': 'flight',
      'data-ref_id': flight.ItineraryCode,
      'data-amount': flight.Price,
      'data-currency': flight.Currency,
      'data-link': flight.link
    });
    $flight_results.append($flight_box);
  }
  setFlightsSearchStatus(true);
  $('#flightsResultsWrapper').show();
}
var show_warnings = true;
function interpretNoFlightsResponse(result,initial){
  setFlightsSearchStatus(true);
  if(initial && result && result.data && result.data.flights_expired){
    show_warnings = false;
  }
  if(show_warnings){
    $('#flightWarnings').show();
  }
  show_warnings = true;
  $('#flightsResultsWrapper').hide();
}
function interpretFlightsResponse(){
  if(!flights_result.data.offer){
    $('#flightsInfo > h1').html('Am gasit ' + (flights_result.response.total_items == 1 ? 'o singura oferta' : flights_result.response.total_items + ' oferte') + ' de zbor spre ' + flights_result.data.destination_city_name);
  } else {
    // $('#flightsInfo .mapInfo').remove();
    // $('#flightsInfo .location-details').remove();
    $('#flightsInfo > h1').html('Am gasit ' + (flights_result.response.total_items == 1 ? 'o singura oferta' : flights_result.response.total_items + ' oferte') + ' de zbor');
  }
  if(flights_result.data.destination_city_name && flights_result.data.origin_city_name){
    $('#flightsInfo .mapInfo > .destinationCity').html(flights_result.data.destination_city_name);
    $('#flightsInfoOriginLocation').html(flights_result.data.origin_city_name);
    $('#flightsInfoDestinationLocation').html(flights_result.data.destination_city_name);
  }
  $('#flightsInfoDepartureDate').html(moment(flights_result.data.departure_date,'Y-MM-DD').locale('ro').format("dddd, DD MMMM Y"));
  var passengers = flights_result.data.passengers_adult + flights_result.data.passengers_senior + flights_result.data.passengers_youth + flights_result.data.passengers_child + flights_result.data.passengers_infant_lap + flights_result.data.passengers_infant_seat;
  $('#flightsInfoPassengers').html(passengers == 1 ? '1 calator' : passengers + ' calatori');
  if(flights_result.data.go_only){
    $('#flightsInfoOriginLocation').next().attr('class','fa fa-long-arrow-right');
    $('#flightsInfoReturn').attr('style','visibility:hidden;');
    $('#flightsInfoReturnDate').hide();
  } else {
    $('#flightsInfoReturnDate').html(moment(flights_result.data.return_date,'Y-MM-DD').locale('ro').format("dddd, DD MMMM Y"));
    if(!flights_result.data.offer){
      $('#flightsInfoOriginLocation').next().attr('class','fa fa-exchange');
      $('#flightsInfoReturn').attr('style','visibility:visible;');
    } else {
      $('#flightsInfoReturn').attr('style','visibility:hidden;');
    }
    $('#flightsInfoReturnDate').show();
  }
  if(flights_result.data.go_only){
    notification_title = "Doar Dus";
  } else {
    notification_title = "Dus-Intors";
  }
  if(flights_result.data.cabine_type){
    notification_title += ', ' + $.trim($('#clasaZbor > option[value="' + flights_result.data.cabine_type + '"]').text());
  }
  notification_title += ', ' + flights_result.data.origin_city_name + ' (' +  flights_result.data.origin_country_name  + ') - ' + flights_result.data.destination_city_name + ' (' +  flights_result.data.destination_country_name  + ')';
  notification_title += ', ' + moment(flights_result.data.departure_date,'Y-MM-DD').locale('ro').format("dddd, DD MMMM Y");
  notification_title += ' - ' + moment(flights_result.data.return_date,'Y-MM-DD').locale('ro').format("dddd, DD MMMM Y");
  notification_title += ', ' + passengers + ' ' + (passengers > 1 ? 'persoane' : 'persoana');
}
function interpretFlightsResults(){
  var go_only = flights_result.data.go_only;
  var response = flights_result.response;
  setFlightsSearchStatus(false);
  flights_result.flights = [];
  var sort_by_company = parseInt($('#sortNumeH').val());
  var sort_by_price = parseInt($('#sortPret').val());
  var sort_by_duration = parseInt($('#sortSteleH').val());
  var filter_min_price = -1;
  var filter_max_price = -1;
  if(typeof flights_result.price_range !== 'undefined'){
    var $price_slider = $("#slider-range").slider();
    var price_values = $price_slider.slider('values');
    var filter_min_price = flights_result.price_range[price_values[0]];
    var filter_max_price = flights_result.price_range[price_values[1]];
  }
  flights_result.price_range = [];
  var for_date_departure = $('#flightsFilterForDateDeparture').val();
  if(!for_date_departure.length){
    for_date_departure = false;
  }
  var for_date_return = $('#flightsFilterForDateReturn').val();
  if(!for_date_return.length){
    for_date_return = false;
  }
  var filter_company_codes = [];
  $('input[name="filters[companies][]"]:checked').each(function(){ filter_company_codes.push(this.value)});
  var filter_stops = [];
  $('input[name="filters[stops][]"]:checked').each(function(){ filter_stops.push(this.value)});
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
      flights_result.flights.push(flight);
    }
    flights_result.price_range.push(parseFloat(flight.Price));
  }
  flights_result.price_range = $.uniqueSort(flights_result.price_range);
  flights_result.price_range = flights_result.price_range.sort(function(a,b) {
    return a - b;
  });
  if(sort_by_company > 0){
    flights_result.flights.sort(function(r1,r2){
      var modifier = sort_by_company === 2 ? -1 : 1;
      var a = r1.Routes[0].Route[0].Segment[0].Carrier.Marketing._.toLowerCase();
      var b = r2.Routes[0].Route[0].Segment[0].Carrier.Marketing._.toLowerCase();
      return a < b ? -1 * modifier : (a > b ? 1 * modifier : 0);
    });
  }
  <?php if(isset($this->view_data['offers']) && $this->view_data['offers']){ ?>
  if(sort_by_price > 0 || sort_by_company > 0){
  <?php } else { ?>
  if(sort_by_price === 2 || sort_by_company > 0){
  <?php } ?>
    flights_result.flights.sort(function(r1,r2){
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
    
  showFlightsResults();
}
function loadFlightsFilters(){
  
  var $price_slider = $("#slider-range").slider();
  $price_slider.slider('option',{
    min: 0,
    max: flights_result.price_range.length-1,
    values: [0, flights_result.price_range.length-1],
  });
  $price_slider.trigger('updatePrice');
  var $stopsBox = $('.flights-filter.flights-filter-stops');
  $stopsBox.empty();
  for(var i=0; i<flights_result.results.stops.length; i++){
    var numar_escale = flights_result.results.stops[i];
    var text = 'Zbor direct';
    if(numar_escale == 1){
      text = '1 Escala'; 
    } else if(numar_escale > 1){
      text = numar_escale + ' Escale'; 
    }
    var $wrapper = $('<div class="checkWrapper" />');
    var $input = $('<input type="checkbox" value="' + numar_escale + '" name="filters[stops][]" id="flights_filter_stops_' + numar_escale + '"/>');
    $input.appendTo($wrapper);
    var $label = $('<label for="flights_filter_stops_' + numar_escale + '">').html(text);
    $label.appendTo($wrapper);
    $wrapper.appendTo($stopsBox);
  }
  var $companiesBox = $('.flights-filter.flights-filter-companies');
  $companiesBox.empty();
  for(var i=0; i<flights_result.results.companies.length; i++){
    var company = flights_result.results.companies[i];
    var code = company.code;
    var text = company.name;
    var image = company.img;
    var $wrapper = $('<div class="checkWrapper" />')
    var $input = $('<input type="checkbox" value="' + code + '" name="filters[companies][]" id="flights_filter_companies_' + code + '"/>');
    $input.appendTo($wrapper);
    var $label = $('<label for="flights_filter_companies_' + code + '">');
    $label.html(text);
    if(image){
      var $image = $('<img />').attr('src', image);
      $label.prepend($image);
    }
    $label.appendTo($wrapper);
    $wrapper.appendTo($companiesBox);
  }
}

function loadResults(initial){
  console.log(flight_search_data);
  $('#flightWarnings').hide();
  $.ajax({
    url: '<?php echo site_url('trip/flights/loadResults'); ?>',
    method: 'post',
    dataType: 'json',
    data: flight_search_data,
    async: true,
    cache: false,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success' || !result.response.code){
        interpretNoFlightsResponse(result,initial);
        return;
      }
      flights_result = result;
      flight_search_data = result.data;
      interpretFlightsResponse();
      createCalendar();
      interpretFlightsResults();
      loadFlightsFilters();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setFlightsSearchStatus(true);
    }
  });
}
function setFlightsSearchAndInitiate(){
  $('#flightWarnings').hide();
  flights_result = null;
  $.ajax({
    url: '<?php echo site_url('trip/flights/setSearchAndInitiate'); ?>',
    method: 'post',
    dataType: 'json',
    data: flight_search_data,
    cache: false,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result || !result.status || result.status !== 'success' || !result.response.code){
        interpretNoFlightsResponse(result);
        return;
      }
      loadResults(true);
      // flights_result = result;
      // flight_search_data = result.data;
      // interpretFlightsResponse();
      // createCalendar();
      // interpretFlightsResults();
      // loadFlightsFilters();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setFlightsSearchStatus(true);
    }
  });
}

flights_submit_function = function (e){
  if(!flights_search_is_over){
    console.log('A previous search is not complete. Ignoring request.');
    return;
  }
  setFlightsSearchStatus(false);
  setFlightsSearchAndRedirect();
};
$(document).on("click", ".detEscale", function (e) {
  $(this).parents(".tur, .retur").children(".infoEscale").toggle('slow');
  $(this).toggleClass("warning, danger");
  if ($(this).hasClass("danger")) {
    $(this).html("<i class='fa fa-times-circle'></i> Inchide");
    $(".boxTicket > span, .boxTicket > img, .boxTicket p").addClass("opacity");
    $(".boxTicket").find(".detEscale, .escale > span, .escale > small").addClass("opacity");
    $(this).removeClass("opacity");
    var timeE = $(this).parents(".tur, .retur").children(".infoEscale").find(".timeEsc1").length;
    if (timeE < 2) {
      $(this).parents(".tur, .retur").children(".infoEscale").children(".timeEsc1").css("width", "60%");
    }
  } else {
    $(this).html("<i class='fa fa-info-circle'></i> Detalii");
    $(".boxTicket > span, .boxTicket > img, .boxTicket p").removeClass("opacity");
    $(".boxTicket").find(".detEscale, .escale > span, .escale > small").removeClass("opacity");
  }
});
function activateCalendarDate($elem){
  var $td = $elem.closest('td');
  var is_active = $td.hasClass('active');
  $('td, th',$td.closest('table')).removeClass('active');
  if(!is_active){
    $('#dateFlexZbor').prop('checked', false);
    $td.addClass('active');
    var td_index = $td.parent('tr').children().index($td);
    $('>th:first-child',$td.parent('tr')).addClass('active');
    $('>tr:first-child>th:nth-child('+ (td_index + 1) +')',$td.closest('tbody')).addClass('active');
    $('#flightsFilterForDateDeparture').val($td.data('departure'));
    $('#flightsFilterForDateReturn').val($td.data('return'));
  } else {
    $('#dateFlexZbor').prop('checked', true);
    $('#flightsFilterForDateDeparture').val('');
    $('#flightsFilterForDateReturn').val('');
  }
}
$('#dateFlexZbor').on('change', function(e){
  createCalendar();
  interpretFlightsResults();
  loadFlightsFilters();
});
$('#calendarFlights').on('click', 'td a', function(e){
  activateCalendarDate($(this));
  interpretFlightsResults();
});
$('#sortPret').change(function(){
  interpretFlightsResults();
});
$('#sortNumeH').change(function(){
  interpretFlightsResults();
});
$('#sortSteleH').change(function(){
  interpretFlightsResults();
});
$('button[name="applyFilters"]').click(function(){
  interpretFlightsResults();
});
$('#allFiltersT').on('click','input[type=checkbox]',function(){
  interpretFlightsResults();
});
$( "#slider-range" ).on( "slidestop", function( event, ui ) {
  interpretFlightsResults();
});
$('button[name="resetFilters"]').click(function(){
  var $price_slider = $("#slider-range").slider();
  $price_slider.slider('option',{
    min: 0,
    max: flights_result.price_range.length-1,
    values: [0, flights_result.price_range.length-1],
  });
  $price_slider.trigger('updatePrice');
  $('input[name="filters[companies][]"]:checked').prop('checked',false);
  $('input[name="filters[stops][]"]:checked').prop('checked',false);
  interpretFlightsResults();
});
var google_map1;
var google_map1_marker;
$(".mapInfo").on("click", function () {
  $('#modalMapCity #modalMapCityTitle').text(flights_result.data.destination_city_name + ', ' + flights_result.data.destination_country_name);
  $('#modalMapCity #modalMapCityName').text(flights_result.data.destination_city_name);
  
  var myLatLng = {
    lat: 0, 
    lng: 0
  };
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address': flights_result.data.destination_city_name + ', ' + flights_result.data.destination_country_name}, function(results, status) {
    $('#modalMapCity').show();
    if (status == google.maps.GeocoderStatus.OK){
      myLatLng = {
        lat: parseFloat(results[0].geometry.location.lat()), 
        lng: parseFloat(results[0].geometry.location.lng())
      }
      
      if(!google_map1){
        google_map1 = new google.maps.Map(document.getElementById('googleMap1'), {
          zoom: 11,
          center: myLatLng
        });
      }
      google_map1.setZoom(11);
      google_map1.setCenter(myLatLng);
      console.log(myLatLng);
      if(google_map1_marker){
        google_map1_marker.setMap(null);
        google_map1_marker = null;
      }
      google_map1_marker = new google.maps.Marker({
        position: myLatLng,
        map: google_map1,
        title: flights_result.data.destination_city_name + ', ' + flights_result.data.destination_country_name
      });
    }
  });
});
$("#modalMapCity .btn").on("click", function () {
  $('#modalMapCity').hide();
});
var notification_id;
$("#flightResults").on("click", ".notification-button", function () {
  notification_id = this.id;
  var itinerary_hashes = [];
  $('input[type=radio][name^=flight_choose]:checked',$(this).closest('.boxTicket')).each(function(){itinerary_hashes.push($(this).data('hash'))});
  var $this = $(this);
  var obj = {
    itinerary_code : itinerary_hashes.join('-'),
    type : $this.data('type'),
    title : notification_title,
    amount : $this.data('amount'),
    currency : $this.data('currency'),
    data : JSON.stringify(flight_search_data)
  };
  openNotificationModal(obj);
});
<?php if(isset($this->view_data['offers']) && $this->view_data['offers']){ ?>
var flights_search_is_over = true;
function setFlightsSearchStatus(search_status){
  $('#flightOffersWarnings').toggle(!search_status);
  flights_search_is_over = search_status;
}
function loadOffers(initial){
  setFlightsSearchStatus(false);
  $('#flightOffersMessages').show();
  $.ajax({
    url: '<?php echo site_url('trip/flights/loadOffers?airline=' . $this->_ci->input->get('airline')); ?>',
    dataType: 'json',
    cache: false,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success'){
        interpretNoFlightsResponse(result,initial);
        return;
      }
      flights_result = result;
      interpretFlightsResponse();
      createCalendar();
      interpretFlightsResults();
      loadFlightsFilters();
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      setFlightsSearchStatus(true);
    }
  });
}
loadOffers();
<?php } else { ?>
<?php if(isset($_GET['init'])){ ?>
window.history.pushState("", document.title, window.location.href.split("?")[0]);
$('#cautaZbor').trigger('click');
<?php } elseif(!isset($_GET['n'])){ ?>
if(flight_search_data.index_id && flight_search_data.index_id.length>0){
  setFlightsSearchStatus(false);
  // show_warnings = false;
  loadResults(true);
}
<?php } else { ?>
	setFlightsSearchStatus(false);
	setFlightsSearchAndInitiate();
<?php } ?>
<?php } ?>
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>