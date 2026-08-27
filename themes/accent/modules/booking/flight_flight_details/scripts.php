<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
function getFlightStopDurationString(string){
  var str_arr = string.split(':');
  var h = parseInt(str_arr[0]);
  var m = parseInt(str_arr[1]);
  var s = parseInt(str_arr[2]);
  return (h ? h + 'h ' : '') + m + 'min' + (s ? ' ' + s + 's' : '');
}
function getFlightDurationString(string){
  var str_arr = string.split(':');
  var h = parseInt(str_arr[0]);
  var m = parseInt(str_arr[1]);
  return (h ? h + 'h ' : '') + m + 'min';
}
function getUniversalString(string, def){
  if(typeof def == 'undefined' || def === null){
    def = '-';
  }
  if(!string || string==''){
    return def;
  }
  return string;
}
function getSegmentFlightNumberString(segment){
  var flight_number = segment.Flight.Number;
  var string = '<b>' + segment.Carrier.Marketing.Code + flight_number + '</b>';
  if(segment.Carrier.Operating && segment.Carrier.Marketing.Code != segment.Carrier.Operating.Code){
    string += ' Operat de ' + segment.Carrier.Operating._;
  }
  return string;
}
function getFlightNumberString(flight,type){
  var segment = flight.Routes[type].Segment[0];
  return getSegmentFlightNumberString(segment);
}
var does_not_include_baggage = false;
function getSegmentBaggageAllowanceString(segment_index,flight){
  var strings = [];
  var strings_obj = {};
  for(var i=0; i<flight.FareDetails.PaxFare.length; i++){
    var person_type_details = flight.FareDetails.PaxFare[i];
    var person_type = person_type_details.PTC;
    if(!person_type_details.BaggageAllowance){
      does_not_include_baggage = true;
      string = 'Fara bagaj';
    } else {
      var string = person_type_details.BaggageAllowance[segment_index].Baggage.Allowed;
    }
    if(strings.indexOf(string)<0){
      strings.push(string);
      strings_obj[string] = [];
    }
    var person_type_plural;
    switch(person_type){
      case 'ADT': person_type_plural = 'adulti'; break;
      case 'SEN': person_type_plural = 'seniori'; break;
      case 'CHD': person_type_plural = 'copii'; break;
      case 'INF': person_type_plural = 'infanti in brate'; break;
      case 'INS': person_type_plural = 'infanti in scaun'; break;
    }
    
    strings_obj[string].push(person_type_plural);
  }
  var return_strings = [];
  for (var i=0;i<strings.length;i++){
    var string = strings[i];
    var matches = string.match(/(\d+) PC/);
    if(matches){
      var nr = matches[1];
      if(!nr || nr=='0'){
        string = 'fara bagaj';
      } else if(nr == 1){
        string = 'cate 1 bagaj/pasager';
      } else {
        string = 'cate ' + nr + ' bagaje/pasager';
      }
    }
    if(strings.length>1){
      string += ' (' + strings_obj[strings[i]].join(', ') + ')';
    }
    return_strings.push(string);
  }
  return return_strings.join(', ');
}
function getBaggageAllowanceString(flight,type){
  var segment_index = 0;
  if(type == 1){
    segment_index = flight.Routes[0].Segment.length;
  }
  return getSegmentBaggageAllowanceString(segment_index,flight);
}
function sablonDouaEscale(segments,flight,flight_type){
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
          <span>\
            <strong>\
              Durata zbor\
            </strong>\
            <i class="fa fa-clock-o"></i> ' + getFlightDurationString(flight_1.Flight.Duration) + '\
            <br />\
            <strong>\
              Tip avion\
            </strong>:\
            ' + ( flight_1.Aircraft ? flight_1.Aircraft._ : '' ) + '\
            <br />\
            <strong>\
              Numar zbor\
            </strong>:\
            ' + getSegmentFlightNumberString(flight_1) + '\
            <br />\
            <strong>\
              Locuri disponibile\
            </strong>:\
            ' + getUniversalString(flight_1.Flight.NumberOfSeats) + '\
            <br />\
            <strong>\
              Tip masa\
            </strong>:\
            ' + getUniversalString(flight_1.Flight.Meal,'fara masa') + '\
            <br />\
            <strong>\
              Bagaj cala\
            </strong>:\
            ' + getSegmentBaggageAllowanceString((flight_type == 1 ? (flight.Routes[0].Segment.length) : 0) + 1 - 1,flight) + '\
            <br />\
          </span>\
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
          <span>' + getFlightStopDurationString(flight_2.Flight.StopTime) + '</span>\
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
          <span>\
            <strong>\
              Durata zbor\
            </strong>\
            <i class="fa fa-clock-o"></i> ' + getFlightDurationString(flight_2.Flight.Duration) + '\
            <br />\
            <strong>\
              Tip avion\
            </strong>:\
            ' + ( flight_2.Aircraft ? flight_2.Aircraft._ : '' ) + '\
            <br />\
            <strong>\
              Numar zbor\
            </strong>:\
            ' + getSegmentFlightNumberString(flight_2) + '\
            <br />\
            <strong>\
              Locuri disponibile\
            </strong>:\
            ' + getUniversalString(flight_2.Flight.NumberOfSeats) + '\
            <br />\
            <strong>\
              Tip masa\
            </strong>:\
            ' + getUniversalString(flight_2.Flight.Meal,'fara masa') + '\
            <br />\
            <strong>\
              Bagaj cala\
            </strong>:\
            ' + getSegmentBaggageAllowanceString((flight_type == 1 ? (flight.Routes[0].Segment.length) : 0) + 2 - 1,flight) + '\
            <br />\
          </span>\
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
          <span>' + getFlightStopDurationString(flight_3.Flight.StopTime) + '</span>\
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
          <span>\
            <strong>\
              Durata zbor\
            </strong>\
            <i class="fa fa-clock-o"></i> ' + getFlightDurationString(flight_3.Flight.Duration) + '\
            <br />\
            <strong>\
              Tip avion\
            </strong>:\
            ' + ( flight_3.Aircraft ? flight_3.Aircraft._ : '' ) + '\
            <br />\
            <strong>\
              Numar zbor\
            </strong>:\
            ' + getSegmentFlightNumberString(flight_3) + '\
            <br />\
            <strong>\
              Locuri disponibile\
            </strong>:\
            ' + getUniversalString(flight_3.Flight.NumberOfSeats) + '\
            <br />\
            <strong>\
              Tip masa\
            </strong>:\
            ' + getUniversalString(flight_3.Flight.Meal,'fara masa') + '\
            <br />\
            <strong>\
              Bagaj cala\
            </strong>:\
            ' + getSegmentBaggageAllowanceString((flight_type == 1 ? (flight.Routes[0].Segment.length) : 0) + 3 - 1,flight) + '\
            <br />\
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
function sablonOEscala(segments,flight,flight_type){
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
          <span>\
            <strong>\
              Durata zbor\
            </strong>\
            <i class="fa fa-clock-o"></i> ' + getFlightDurationString(flight_1.Flight.Duration) + '\
            <br />\
            <strong>\
              Tip avion\
            </strong>:\
            ' + ( flight_1.Aircraft ? flight_1.Aircraft._ : '' ) + '\
            <br />\
            <strong>\
              Numar zbor\
            </strong>:\
            ' + getSegmentFlightNumberString(flight_1) + '\
            <br />\
            <strong>\
              Locuri disponibile\
            </strong>:\
            ' + getUniversalString(flight_1.Flight.NumberOfSeats) + '\
            <br />\
            <strong>\
              Tip masa\
            </strong>:\
            ' + getUniversalString(flight_1.Flight.Meal,'fara masa') + '\
            <br />\
            <strong>\
              Bagaj cala\
            </strong>:\
            ' + getSegmentBaggageAllowanceString((flight_type == 1 ? (flight.Routes[0].Segment.length) : 0) + 1 - 1,flight) + '\
            <br />\
          </span>\
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
          <span>' + getFlightStopDurationString(flight_2.Flight.StopTime) + '</span>\
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
          <span>\
            <strong>\
              Durata zbor\
            </strong>\
            <i class="fa fa-clock-o"></i> ' + getFlightDurationString(flight_2.Flight.Duration) + '\
            <br />\
            <strong>\
              Tip avion\
            </strong>:\
            ' + ( flight_2.Aircraft ? flight_2.Aircraft._ : '' ) + '\
            <br />\
            <strong>\
              Numar zbor\
            </strong>:\
            ' + getSegmentFlightNumberString(flight_2) + '\
            <br />\
            <strong>\
              Locuri disponibile\
            </strong>:\
            ' + getUniversalString(flight_2.Flight.NumberOfSeats) + '\
            <br />\
            <strong>\
              Tip masa\
            </strong>:\
            ' + getUniversalString(flight_2.Flight.Meal,'fara masa') + '\
            <br />\
            <strong>\
              Bagaj cala\
            </strong>:\
            ' + getSegmentBaggageAllowanceString((flight_type == 1 ? (flight.Routes[0].Segment.length) : 0) + 2 - 1,flight) + '\
            <br />\
          </span>\
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

function getCompanyImageByCode(company_code){
  var company_image;
  var company_index = flight_data.companies_indexes[company_code];
  if(typeof company_index !== 'undefined'){
    company_image = flight_data.companies[company_index].img;
  }
  return company_image;
}
function showFlightsResults(){
  var flight = flight_data;
  if(!flight){
    return;
  }
  var $flight_results = $('.boxTicket').first();
  $flight_results.empty();
  i = 0;
  var route_types = flight.Routes;
  var flights_str_arr = [];
  for(var j=0; j<route_types.length; j++){
    var route = route_types[j];
    var k = route.Index;
    var cabin_types = [];
    var company_code = route.Segment[0].Carrier.Marketing.Code;
    var company_name = route.Segment[0].Carrier.Marketing._;
    var departure_date = moment(route.Segment[0].Origin.Date + ' ' + route.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss');
    var arrival_date = moment(route.Segment[route.Segment.length-1].Destination.Date + ' ' + route.Segment[route.Segment.length-1].Destination.Time,'Y-MM-DD HH:mm:ss');
    var company_image = getCompanyImageByCode(company_code);
    var escale = route.Segment.length - 1;
    for(var l=0; l<=escale; l++){
      var escala = route.Segment[l];
      var stupid_cabin_types = escala.Flight.CabinType ? escala.Flight.CabinType.split(',') : [];
      for(var a=0; a<stupid_cabin_types.length;a++){
        var cabin_type = stupid_cabin_types[a];
        if(!cabin_type || !cabin_type.length){
          continue;
        }
        if(cabin_types.indexOf(cabin_type) < 0){
          cabin_types.push(cabin_type);
        }
      }
    }
    var box_ticket_row_html = '';
    box_ticket_row_html += '<hr />';
    if(!j){
      var ischecked = true;
      var is_available = true;
      box_ticket_row_html += '<div class="row tur" \
          data-company="' + company_code + '"\
          data-stops="' + escale + '"\
          data-price="' + flight.Price + '"\
          >\
          <div class="col-11 col-sm-3 dataPL">\
            <div class="iconsFlight">\
              <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="Data plecare" />' +
              (company_image ? '<img src="' + company_image + '" alt="' + company_name + '" title="' + company_name + '" />' : '') +
            '</div>\
            <p class="mb-0" style="text-transform:capitalize;">\
              <span>PLECARE</span>\
              <br/>' +
              departure_date.locale('ro').format('dddd') +
              '<br/>\
              <strong style="text-transform:capitalize;">' + departure_date.locale('ro').format('D MMM') + '</strong>\
              , ' + departure_date.format('Y') +
            '</p>\
          </div>\
          <div class="col-4 col-sm-2 oraPL">\
            <p class="mb-0">\
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
            <p class="mb-0">\
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
            <p class="text-center mb-0">\
              <input style="display:inline-block;vertical-align:middle;" type="radio" ' + (ischecked ? 'checked' : '') + ' name="flight_choose[' + j + ']" value="' + route.Index + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" />\
              <label style="display:inline-block;vertical-align:middle;" class="mb-0" for="flight_result_choose_' + i + '_'+ j + '_' + k + '">Ales</label>\
            </p>\
          </div>' +
          (escale === 1 ? sablonOEscala(route.Segment,flight,j) : '') + 
          (escale === 2 ? sablonDouaEscale(route.Segment,flight,j) : '') + 
        '</div>';
    } else {
      var ischecked = true;
      var is_available = true;
      box_ticket_row_html += '<div class="row retur' + (j && !k ? ' mt-4' : '') + (is_available ? '' : ' nocomb') + '">\
          <div class="col-11 col-sm-3 dataPL">\
            <div class="iconsFlight">\
              <img src="<?php echo $this->theme_url; ?>assets/images/plecare.png" alt="Data plecare" />' +
              (company_image ? '<img src="' + company_image + '" alt="' + company_name + '" title="' + company_name + '" />' : '') +
            '</div>\
            <p class="mb-0" style="text-transform:capitalize;">\
              <span>RETUR</span>\
              <br/>\
              <small>' + departure_date.locale('ro').format('dddd') + '</small>\
              <br/>\
              <strong>' + departure_date.locale('ro').format('D MMM') + '</strong>\
              , ' + departure_date.format('Y') +
            '</p>\
          </div>\
          <div class="col-4 col-sm-2 oraPL">\
            <p class="mb-0">\
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
            <p class="mb-0">\
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
            <p class="text-center mb-0">\
              <input style="display:inline-block;vertical-align:middle;" type="radio" ' + (ischecked ? 'checked' : '') + ' name="flight_choose[' + j + ']" value="' + route.Index + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" />\
              <label style="display:inline-block;vertical-align:middle;" class="mb-0" for="flight_result_choose_' + i + '_'+ j + '_' + k + '">Ales</label>\
            </p>\
          </div>' +
          (escale === 1 ? sablonOEscala(route.Segment,flight,j) : '') + 
          (escale === 2 ? sablonDouaEscale(route.Segment,flight,j) : '') + 
        '</div>';
    }
    if(!escale){
      box_ticket_row_html += '<span class="familyLight fontSize12 "> Locuri Disponibile: ' + getUniversalString(route.Segment[0].Flight.NumberOfSeats) + ' | Tip Masa: ' + getUniversalString(route.Segment[0].Flight.Meal,'fara masa') + ' | Bagaj Cala: ' + getBaggageAllowanceString(flight,j) + ' | Nr.zbor: ' + getFlightNumberString(flight,j) + '</span><br />';
    }
	box_ticket_row_html += '<span class="familyLight fontSize12 ">';
	
    box_ticket_row_html += (cabin_types.length ? '<b>Clasa</b>: ' + cabin_types.join(', ') + '<br />' : '');
	if(flight.FareDetails && flight.FareDetails.BrandedFare && flight.FareDetails.BrandedFare.BrandDetails  && flight.FareDetails.BrandedFare.BrandDetails.length && flight.FareDetails.BrandedFare.BrandDetails[j] && (flight.FareDetails.BrandedFare.BrandDetails[j].Code || flight.FareDetails.BrandedFare.BrandDetails[j].Description)){
		box_ticket_row_html +=  (flight.FareDetails.BrandedFare.BrandDetails[j].Code ? '<b>Fare Family</b>: ' + flight.FareDetails.BrandedFare.BrandDetails[j].Code + '' : '')
		+ (flight.FareDetails.BrandedFare.BrandDetails[j].Description ? ', ' + flight.FareDetails.BrandedFare.BrandDetails[j].Description + '' : '')
	}
	box_ticket_row_html +=  '</span><br />';
    flights_str_arr.push(box_ticket_row_html);
  }
  var route = flight.Routes[0];
  var box_html = '\
      <div class="row">\
        <div class="col-12 col-sm-12 col-lg-10">\
          <div class="">\
            <h4>\
              <strong>' + route.Segment[0].Origin.Airport._ +', ' + route.Segment[0].Origin.Airport.City + '</strong>\
              -\
              <strong>' + route.Segment[route.Segment.length-1].Destination.Airport._ +', ' + route.Segment[route.Segment.length-1].Destination.Airport.City + '</strong>\
            </h4>\
          </div>' +
          flights_str_arr.join('') +
        '</div>\
        <div class="col-12 col-sm-12 col-lg-2 blD">\
          <p class="text-center pretFL">\
            <br />\
            <br />\
            <br />\
            <br />\
            <span class="pretSrcH">' + (parseFloat(flight.Price).toLocaleString('ro')) + ' <?php echo $this->_ci->currency_symbol; ?></span>\
            </p>\
        </div>\
      </div>\
    ';
  
  if(false && does_not_include_baggage){
    box_html += '<div class="alert alert-danger familyLight fontSize12 mt-4 mb-0">\
    <p class="mb-2 fontSize14"><strong><i class="fa fa-warning"></i> Atentie: bagajele de cala nu se pot rezerva online pentru majoritatea biletelor de avion emise pe platforma <a href="//www.accenttravel.ro">www.accenttravel.ro</a>.</strong></p>\
    <p class="mb-1 fontSize12">Pentru a adauga bagaje de cala zborului dumneavoastra, va rugam sa adresati solicitarea la adresa: vanzari@accenttravel.ro. In cel mai scurt timp un operator Accent Travel & Events va opera cererea dumneavoastra.</p>\
    <p class="mb-1 fontSize12">De asemenea, pentru a adauga servicii auxiliare zborului dumneavoastra (variante personalizate de bagaj, loc in avion, tip de masa, etc) va rugam sa ne adresati solicitarea la adresa: <a href="mailto:vanzari@accenttravel.ro">vanzari@accenttravel.ro</a></p>\
    <p class="mb-1 fontSize12">Retineti faptul ca tarifele pentru bagaje sunt valabile in momentul rezervarii.</p>\
    <p class="mb-1 fontSize12">Dupa emiterea biletelor de avion, tarifele pentru bagaje se pot modifica. Companiile aeriene pot percepe o taxa suplimentara pentru bagajele adaugate cu mai putin de 24 de ore inaintea datei calatoriei.</p>\
    <p class="mb-1 fontSize12">Pentru unele rute, adaugarea de bagaje este posibila numai la aeroport. In acest caz, consultantii Accent Travel & Events va vor contacta.</p>\
    </div><br />';
  }
  $flight_results.append(box_html);
}
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
showFlightsResults();
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>