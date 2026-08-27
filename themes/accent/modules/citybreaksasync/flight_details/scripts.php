<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">

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
    var has_no_comb = hotel_results.results.flights[flight_index].Combinations.indexOf(combination) < 0;
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
$('.flightResults').on('change','input[type=radio][name^=flight_choose]', function(){
  if(!$(this).is(':checked')){
    return;
  }
  var matches = this.name.match(/flight_choose\[(\d+)\]/);
  var flight_type = matches[1];
  var $box_ticket = $(this).closest('.boxTicket').first();
  var flight_index = $box_ticket.data('flight_index');
  var route_ref = this.value;
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
function getCompanyImageByCode(company_code){
  var company_image;
  var company_index = hotel_results.results.companies_indexes[company_code];
  if(typeof company_index !== 'undefined'){
    company_image = hotel_results.results.companies[company_index].img;
  }
  return company_image;
}
var flight_price = 0;
function showFlightsResults(){
  var flights = hotel_results.results.flights;
  var $flight_results = $('.flightResults');
  $flight_results.empty();
  var combination_selected = '<?php echo isset($this->view_data['combination']) ? $this->view_data['combination'] : ''; ?>';
  for(var i=0; i<flights.length;i++){
    var flight = flights[i];
    var combinations = flight.Combinations;
    if(combinations.indexOf(combination_selected) < 1){
      combination_selected = flight.Combinations[0];
    }
    var combination_arr = combination_selected.split('|');
    flight_price = parseFloat(flight.Price);
    var route_types = flight.Routes;
    var flights_str_arr = [];
    for(var j=0; j<route_types.length; j++){
      var route_type = route_types[j];
      var routes = route_type.Route;
      var k_index = -1;
      for(var k=0; k<routes.length; k++){
        var route = routes[k];
        var route_ref = parseInt(route.Ref);
        if(!j && typeof flight.DepRoutes[route_ref] === 'undefined'){
          continue;
        }
        if(j && typeof flight.RetRoutes[route_ref] === 'undefined'){
          continue;
        }
        k_index++;
        var cabin_types = [];
        var company_code = route.Segment[0].Carrier.Marketing.Code;
        var company_name = route.Segment[0].Carrier.Marketing._;
        var departure_date = moment(route.Segment[0].Origin.Date + ' ' + route.Segment[0].Origin.Time,'Y-MM-DD HH:mm:ss');
        var arrival_date = moment(route.Segment[route.Segment.length-1].Origin.Date + ' ' + route.Segment[route.Segment.length-1].Origin.Time,'Y-MM-DD HH:mm:ss');
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
              data-price="' + flight.Price + '"\
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
                  <input form="Package-1" type="radio" ' + (ischecked ? 'checked="checked"' : '') + ' name="flight_choose[' + j + ']" value="' + route.Ref + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" required />\
                </p>\
              </div>' +
              (escale === 1 ? sablonOEscala(route.Segment) : '') + 
              (escale === 2 ? sablonDouaEscale(route.Segment) : '') + 
            '</div>';
		
	box_ticket_row_html += '<span class="familyLight fontSize12 ">';
	
    box_ticket_row_html += (cabin_types.length ? '<b>Clasa</b>: ' + cabin_types.join(', ') + '<br />' : '');
	if(flight.FareDetails && flight.FareDetails.BrandedFare && flight.FareDetails.BrandedFare.BrandDetails  && flight.FareDetails.BrandedFare.BrandDetails.length && flight.FareDetails.BrandedFare.BrandDetails[j] && (flight.FareDetails.BrandedFare.BrandDetails[j].Code || flight.FareDetails.BrandedFare.BrandDetails[j].Description)){
		box_ticket_row_html +=  (flight.FareDetails.BrandedFare.BrandDetails[j].Code ? '<b>Fare Family</b>: ' + flight.FareDetails.BrandedFare.BrandDetails[j].Code + '' : '')
		+ (flight.FareDetails.BrandedFare.BrandDetails[j].Description ? ', ' + flight.FareDetails.BrandedFare.BrandDetails[j].Description + '' : '')
	}
	box_ticket_row_html +=  '</span><br />';
          flights_str_arr.push(box_ticket_row_html);
        } else {
          var is_available = true;
          if(!ischecked){
            if(flight.Combinations.indexOf(combination_arr[0] + '|' + '1' + route.Ref) < 0){
              is_available = false;
            }
          }
          var box_ticket_row_html = '<div class="row retur' + (j && !k_index ? ' mt-4' : '') + (is_available ? '' : ' nocomb') + '"' + (is_available ? '' : ' title="Aceasta ruta nu este compatibila cu ruta de plecare aleasa."') + '>\
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
                  <input form="Package-1" type="radio" ' + (ischecked ? 'checked="checked"' : '') + ' name="flight_choose[' + j + ']" value="' + route.Ref + '" id="flight_result_choose_' + i + '_'+ j + '_' + k + '" required />\
                </p>\
              </div>' +
              (escale === 1 ? sablonOEscala(route.Segment) : '') + 
              (escale === 2 ? sablonDouaEscale(route.Segment) : '') + 
            '</div>';
			
	box_ticket_row_html += '<span class="familyLight fontSize12 ">';
	
    box_ticket_row_html += (cabin_types.length ? '<b>Clasa</b>: ' + cabin_types.join(', ') + '<br />' : '');
	if(flight.FareDetails && flight.FareDetails.BrandedFare && flight.FareDetails.BrandedFare.BrandDetails  && flight.FareDetails.BrandedFare.BrandDetails.length && flight.FareDetails.BrandedFare.BrandDetails[j] && (flight.FareDetails.BrandedFare.BrandDetails[j].Code || flight.FareDetails.BrandedFare.BrandDetails[j].Description)){
		box_ticket_row_html +=  (flight.FareDetails.BrandedFare.BrandDetails[j].Code ? '<b>Fare Family</b>: ' + flight.FareDetails.BrandedFare.BrandDetails[j].Code + '' : '')
		+ (flight.FareDetails.BrandedFare.BrandDetails[j].Description ? ', ' + flight.FareDetails.BrandedFare.BrandDetails[j].Description + '' : '')
	}
	box_ticket_row_html +=  '</span><br />';
          flights_str_arr.push(box_ticket_row_html);
        }
      }
    }
    
    var box_html = '<div class="boxTicket mb-0 bb0 col-12" data-flight_index="' + i + '">\
        <div class="row">\
          <div class="col-12 col-sm-12 col-lg-12">\
            <div class="dashedBB mb-3">\
              <h4>\
                <strong>' + hotel_results.data.origin_full_location_name + '</strong>\
                -\
                <strong>' + hotel_results.data.destination_full_location_name + '</strong>\
              </h4>\
            </div>' +
            flights_str_arr.join('<hr />') +
          '</div>\
        </div>\
        <input form="Package-1" type="hidden" name="flight_code" value="' + hotel_results.data.flight_code + '"/>\
        <input form="Package-1" type="hidden" name="combinations" value="' + flight.Combinations.join(',') + '"/>\
        <input form="Package-1" type="hidden" name="itinerary_code" value="' + flight.ItineraryCode + '"/>\
      </div>';
    $flight_results.each(function(index){
      $(this).append(box_html
        .replace(new RegExp(/flight_result_choose_/,'g'), 'flight_result_choose_' + index + '_')
      );
    });
  }
  // setFlightsSearchStatus(true);
  // $('#flightsResultsWrapper').show();
}
$(document).on("click", ".detEscale", function (e) {
  $(this).parents(".tur, .retur").children(".infoEscale").toggle('slow');
  $(this).toggleClass("warning, danger");
  if ($(this).hasClass("danger")) {
    $(this).html("<i class='fa fa-times-circle'></i> Inchide");
    $(".boxTicket > span, .boxTicket > img, .boxTicket p").addClass("opacity");
    $(".boxTicket").find(".detEscale, .escale > span").addClass("opacity");
    $(this).removeClass("opacity");
    var timeE = $(this).parents(".tur, .retur").children(".infoEscale").find(".timeEsc1").length;
    if (timeE < 2) {
      $(this).parents(".tur, .retur").children(".infoEscale").children(".timeEsc1").css("width", "60%");
    }
  } else {
    $(this).html("<i class='fa fa-info-circle'></i> Detalii");
    $(".boxTicket > span, .boxTicket > img, .boxTicket p").removeClass("opacity");
    $(".boxTicket").find(".detEscale, .escale > span").removeClass("opacity");
  }
});
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>