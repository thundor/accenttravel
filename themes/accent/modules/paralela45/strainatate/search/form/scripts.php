<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$this->_ci->load->model('Paralela45_model');

$data = $this->paralela45_strainatate_search_data;
?>
<script type="text/javascript">
var paralela45_strainatate_search_data = <?php echo json_encode($data); ?>;
var paralela45_strainatate_submit_function;
var paralela45_strainatate_routes = <?php echo json_encode($this->getPackageNVRoutesResponse); ?>;
var paralela45_strainatate_routes_request = <?php echo json_encode($this->getPackageNVRoutesRequest); ?>;
// console.log(paralela45_strainatate_routes_request);
(function($){
  if(typeof search_is_over === 'undefined'){
    search_is_over = true;
  }
  var initting_data = true;
  <?php
  if($this->_ci->user->can('backend-access')){ ?>
  function setFiltersLink(){
    var my_obj = {};
    my_obj.n = 1;
    if(paralela45_strainatate_search_data.origin){
      my_obj.origin = paralela45_strainatate_search_data.origin;
    }
    if(paralela45_strainatate_search_data.destination){
      my_obj.destination = paralela45_strainatate_search_data.destination;
    } else {
      if(paralela45_strainatate_search_data.zone){
        my_obj.zone = paralela45_strainatate_search_data.zone;
      }
    }
    if(paralela45_strainatate_search_data.hotel){
      my_obj.hotel = paralela45_strainatate_search_data.hotel_name;
    }
    if(paralela45_strainatate_search_data.start_date){
      my_obj.sdate = paralela45_strainatate_search_data.start_date;
    }
    if(paralela45_strainatate_search_data.nights){
      my_obj.nights = paralela45_strainatate_search_data.nights;
    }
    if(paralela45_strainatate_search_data.occupancy.length){
      my_obj.o = paralela45_strainatate_search_data.occupancy;
    }
    var recursiveDecoded = decodeURIComponent( $.param( my_obj ) );
    var filters_link = 'paralela45/strainatate?' + recursiveDecoded;
    $('.paralela45_strainatate_search_link').val(filters_link);
  }
  <?php } ?>
  function setSearchStatus(search_status){
    if(search_status){
      $('#paralela45-strainatate-loading-screen').addClass('inactive');
      $('#cautaPax1').bootstrap_button('loading');
    } else {
      $('#paralela45-strainatate-loading-screen').removeClass('inactive');
      $('#cautaPax1').bootstrap_button('reset');
    }
    search_is_over = search_status;
  }
  window.setParalela45StrainatateSearchStatus = setSearchStatus;
  
  function setData(){
    paralela45_strainatate_search_data.origin = $.trim($('#categoriePax1').val());
    paralela45_strainatate_search_data.destination = $.trim($('#destinatiePax1').val());
    paralela45_strainatate_search_data.zone = '';
    if(paralela45_strainatate_search_data.destination === ''){
      paralela45_strainatate_search_data.zone = $('#zonePax1').val();
    }
    paralela45_strainatate_search_data.start_date = $('#datePax1').val();
    paralela45_strainatate_search_data.nights = $('#categPax1').val();
    paralela45_strainatate_search_data.hotel_name = $.trim($('#numeHotelPax1Abr').val());
    paralela45_strainatate_search_data.occupancy = [];
    
    var ocuppancy_1 = {};
    ocuppancy_1.adt = $('#adultiCam1Pax1').val();
    var children = $('#copiiCam1Pax1').val();
    var ages = [];
    if(children>1){
      ocuppancy_1.chd = [];
      ocuppancy_1.chd.push($('#varstaCop1Cam1Pax1').val());
      if(children>2){
        ocuppancy_1.chd.push($('#varstaCop2Cam1Pax1').val());
      }
    }
    paralela45_strainatate_search_data.occupancy.push(ocuppancy_1);
    
    if($('#cam2Pax1').is(':visible')){
      var ocuppancy_2 = {};
      ocuppancy_2.adt = $('#adultiCam2Pax1').val();
      var children = $('#copiiCam2Pax1').val();
      var ages = [];
      if(children>1){
        ocuppancy_2.chd = [];
        ocuppancy_2.chd.push($('#varstaCop1Cam2Pax1').val());
        if(children>2){
          ocuppancy_2.chd.push($('#varstaCop2Cam2Pax1').val());
        }
      }
      paralela45_strainatate_search_data.occupancy.push(ocuppancy_2);
    }
    if($('#cam3Pax1').is(':visible')){
      var ocuppancy_3 = {};
      ocuppancy_3.adt = $('#adultiCam3Pax1').val();
      var children = $('#copiiCam3Pax1').val();
      var ages = [];
      if(children>1){
        ocuppancy_3.chd = [];
        ocuppancy_3.chd.push($('#varstaCop1Cam3Pax1').val());
        if(children>2){
          ocuppancy_3.chd.push($('#varstaCop2Cam3Pax1').val());
        }
      }
      paralela45_strainatate_search_data.occupancy.push(ocuppancy_3);
    }
    
    paralela45_strainatate_search_data.sort_by = 'MinPrice';
    paralela45_strainatate_search_data.sort_order = 0;
    
    var sort_element = $('.package-sort-by').filter(function(){return $(this).val()>0;}).first();
    if(sort_element.length){
      paralela45_strainatate_search_data.sort_by = sort_element.attr('name');
      paralela45_strainatate_search_data.sort_order = parseInt(sort_element.val()) - 1;
    }
    <?php
    if($this->_ci->user->can('backend-access')){ ?>
    setFiltersLink();
    <?php } ?>
  }
  window.setParalela45StrainatateData = setData;
  function setSearchAndRedirect(){
    $.ajax({
      url: '<?php echo site_url('paralela45/strainatate/setSearch'); ?>',
      method: 'post',
      dataType: 'json',
      data: paralela45_strainatate_search_data,
      async: true,
      success: function(result,status,xhr){
        if(!result.status || result.status !== 'success'){
          setSearchStatus(true);
          return;
        }
        paralela45_strainatate_search_data = result.data;
        
        setSearchStatus(true);
        window.location.href="<?php echo site_url('paralela45/strainatate'); ?>";
      },
      error: function(jqXHR,textStatus,error){
        console.log(jqXHR, textStatus, error);
        setSearchStatus(true);
      }
    });
  }
  window.setParalela45StrainatateSearchAndRedirect = setSearchAndRedirect;
  if(typeof paralela45_strainatate_submit_function === 'undefined'){
    window.paralela45_strainatate_submit_function = function (e){
      if(!search_is_over){
        console.log('A previous search is not complete. Ignoring request.');
        return;
      }
      setSearchStatus(false);
      setData();
      setSearchAndRedirect();
    };
  }
  $(document).on('change click', 'form.pachete-strainatate-search', function(e){
    setData();
  });
  $(document).on('submit', 'form.pachete-strainatate-search', function(e){
    e.preventDefault();
    paralela45_strainatate_submit_function.call(this, e);
  });
  function SortByText(a, b){
    var aName = a.text.toLowerCase();
    var bName = b.text.toLowerCase(); 
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
  }
  function SortByNights(a, b){
    var aName = a.id-1;
    var bName = b.id-1; 
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
  }
  function SortById(a, b){
    var aName = a.id;
    var bName = b.id; 
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
  }
  function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
  }
  function Select2Matcher(term, text) {
    if(!term.length){
      return true;
    }
    var compare_text = removeDiacritics(text);
    var terms = term.split(" ");
    for (var i=0; i < terms.length; i++){
      var tester = new RegExp("\\b" + escapeRegExp(terms[i]), 'i');
      if (tester.test(compare_text) == false){
        return false;
      }
    }
    return true;
  };
  var $opened_select2;
  function filterMatchPush(filter_type, id, totals, filter_data, results, params, grouping, info){
    if(filter_data.indexOf(id) > -1){
      return;
    }
    if(filter_type === 'oras_plecare'){
      if(typeof paralela45_strainatate_routes.CityLinks.Departure[id] === 'undefined'){
        console.log('Departure city undefined:' + id);
        return;
      }
    }
    else if(filter_type === 'oras_destinatie'){
      if(typeof paralela45_strainatate_routes.CityLinks.Destination[id] === 'undefined'){
        console.log('Destination city undefined:' + id);
        return;
      }
    }
    else if(filter_type === 'tara_destinatie'){
      if(typeof paralela45_strainatate_routes.Countries[id] === 'undefined'){
        console.log('Destination country undefined:' + id);
        return;
      }
      var country = paralela45_strainatate_routes.Countries[id];
    }
    else if(filter_type === 'zona_destinatie'){
      if(typeof paralela45_strainatate_routes.Zones[id] === 'undefined'){
        console.log('Destination zone undefined:' + id);
        return;
      }
      var zone = paralela45_strainatate_routes.Zones[id];
    }
    if(filter_type === 'oras_plecare' || filter_type === 'oras_destinatie'){
      if(typeof paralela45_strainatate_routes.Cities[id] === 'undefined'){
        console.log('City undefined:' + id);
        return;
      }
      var city = paralela45_strainatate_routes.Cities[id];
    }
    filter_data.push(id);
    if(filter_type === 'oras_plecare'){
      var text = city.CityName;
      if(Select2Matcher(params.data.term, text)){
        totals.push(id);
        var item = {
          id: id,
          text: text
        };
        results.push(item);
      }
    }
    else if(filter_type === 'tara_destinatie'){
      var text = country.CountryName;
      if(Select2Matcher(params.data.term, text)){
        totals.push(id);
        var item = {
          id: id,
          text: text
        };
        results.push(item);
      }
    }
    else if(filter_type === 'zona_destinatie'){
      if(typeof zone.CountryCode === 'undefined'){
        console.log('Invalid country code for zone:' + id);
        return;
      }
      if(typeof paralela45_strainatate_routes.Countries[zone.CountryCode] === 'undefined'){
        console.log('Invalid country for zone:' + id);
        return;
      }
      var country = paralela45_strainatate_routes.Countries[zone.CountryCode];
      
      var text = zone.ZoneName + ', ' + country.CountryName;
      if(Select2Matcher(params.data.term, text)){
        if(typeof grouping[zone.CountryCode] === 'undefined'){
          var country_result = {
            id: zone.CountryCode,
            text: country.CountryName,
            total: 0,
            children: [],
          };
          results.push(country_result);
          grouping[zone.CountryCode] = results.length-1;
        }
        totals.push(id);
        var item = {
          id: id,
          text: text,
          country: country.CountryName,
          zone: zone.ZoneName
        };
        results[grouping[zone.CountryCode]].total++;
        results[grouping[zone.CountryCode]].children.push(item);
      }
    }
    else if(filter_type === 'oras_destinatie'){
      if(typeof city.CountryCode === 'undefined'){
        console.log('Invalid country code for city:' + id);
        return;
      }
      if(typeof paralela45_strainatate_routes.Countries[city.CountryCode] === 'undefined'){
        console.log('Invalid country for city:' + id);
        return;
      }
      var country = paralela45_strainatate_routes.Countries[city.CountryCode];
      
      var text = city.CityName + ', ' + country.CountryName;
      if(Select2Matcher(params.data.term, text)){
        if(typeof grouping[city.CountryCode] === 'undefined'){
          var country_result = {
            id: city.CountryCode,
            text: country.CountryName,
            total: 0,
            children: [],
          };
          results.push(country_result);
          grouping[city.CountryCode] = results.length-1;
        }
        totals.push(id);
        var item = {
          id: id,
          text: text,
          country: country.CountryName,
          city: city.CityName
        };
        results[grouping[city.CountryCode]].total++;
        results[grouping[city.CountryCode]].children.push(item);
      }
    }
    else {
      var text = id;
      if(filter_type === 'date_plecare'){
        var data_plecare = moment(id,'Y-MM-DD');
        text = data_plecare.locale('ro').format("DD/MM/Y (dddd, D MMMM)");
      }
      if(Select2Matcher(params.data.term, text)){
        totals.push(id);
        var item = {
          id: id,
          text: text
        };
        results.push(item);
      }
    }
  }
  function filterSelect2Field(filter_type, params, success, failure){
    var grouping;
    if(filter_type === 'oras_destinatie' || filter_type === 'zona_destinatie'){
      grouping = {};
    }
    params.data.term = removeDiacritics($.trim(params.data.term).replace(/\s\s+/g, ' '));
    var oras_destinatie = $.trim($('#destinatiePax1').val());
    if(oras_destinatie !== '' && !paralela45_strainatate_routes.Cities[oras_destinatie]){
      oras_destinatie = '';
    }
    var zona_destinatie = $.trim($('#zonePax1').val());
    if(zona_destinatie !== '' && !paralela45_strainatate_routes.Zones[zona_destinatie] || (oras_destinatie !== '' && zona_destinatie != paralela45_strainatate_routes.Cities[oras_destinatie].ZoneCode)){
      zona_destinatie = '';
    }
    var tara_destinatie = $.trim($('#taraPax1').val());
    if(tara_destinatie !== '' && !paralela45_strainatate_routes.Countries[tara_destinatie] || (oras_destinatie !== '' && tara_destinatie != paralela45_strainatate_routes.Cities[oras_destinatie].CountryCode) || (zona_destinatie !== '' && tara_destinatie != paralela45_strainatate_routes.Zones[zona_destinatie].CountryCode)){
      tara_destinatie = '';
    }
    var oras_plecare = $.trim($('#categoriePax1').val());
    if(oras_plecare !== '' && !paralela45_strainatate_routes.Cities[oras_plecare]){
      oras_plecare = '';
    }
    var numar_nopti = $.trim($('#categPax1').val());
    if(numar_nopti !== '' && parseInt(numar_nopti)<=0){
      numar_nopti = '';
    }
    var data_plecare = $.trim($('#datePax1').val());
    
    if(filter_type === 'oras_plecare' || oras_plecare === ''){
      orase_p = paralela45_strainatate_routes.Dates;
    } else {
      orase_p = {};
      if(typeof paralela45_strainatate_routes.Dates[oras_plecare] !== 'undefined'){
        orase_p[oras_plecare] = paralela45_strainatate_routes.Dates[oras_plecare];
      }
    }
    var info = {
      oras_destinatie: oras_destinatie,
      tara_destinatie: tara_destinatie,
      zona_destinatie: zona_destinatie,
      oras_plecare: oras_plecare,
      numar_nopti: numar_nopti,
      data_plecare: data_plecare,
    };
    var totals = [];
    var results = [];
    var filter_data = [];
    LoopOrasePlecare:
    for(var oras_p in orase_p){
      if (!orase_p.hasOwnProperty(oras_p)) {
        continue;
      }
      if(filter_type === 'oras_plecare' && oras_destinatie === '' && numar_nopti === '' && data_plecare === '' && tara_destinatie === '' && zona_destinatie === ''){
        filterMatchPush(filter_type, oras_p, totals, filter_data, results, params, grouping, info);
        continue;
      }
      var orase_d;
      if(filter_type === 'oras_destinatie' || oras_destinatie === ''){
        orase_d = orase_p[oras_p];
      }
      else {
        if(typeof orase_p[oras_p][oras_destinatie] === 'undefined'){
          continue;
        }
        if(filter_type === 'oras_plecare' && numar_nopti === '' && data_plecare === ''){
          filterMatchPush(filter_type, oras_p, totals, filter_data, results, params, grouping, info);
          continue;
        }
        orase_d = {};
        orase_d[oras_destinatie] = orase_p[oras_p][oras_destinatie];
      }
      LoopOraseDestinatie:
      for(var oras_d in orase_d){
        if (!orase_d.hasOwnProperty(oras_d)) {
          continue;
        }
        if(filter_type !== 'zona_destinatie' && zona_destinatie !== '' && paralela45_strainatate_routes.Cities[oras_d].ZoneCode !== zona_destinatie){
          continue;
        }
        if(filter_type !== 'tara_destinatie' && tara_destinatie !== '' && paralela45_strainatate_routes.Cities[oras_d].CountryCode !== tara_destinatie){
          continue;
        }
        if(filter_type === 'oras_destinatie' && numar_nopti === '' && data_plecare === ''){
          filterMatchPush(filter_type, oras_d, totals, filter_data, results, params, grouping, info);
          continue;
        }
        if(filter_type === 'tara_destinatie' && numar_nopti === '' && data_plecare === '' && oras_destinatie === ''){
          filterMatchPush(filter_type, paralela45_strainatate_routes.Cities[oras_d].CountryCode, totals, filter_data, results, params, grouping, info);
          continue;
        }
        if(filter_type === 'zona_destinatie' && numar_nopti === '' && data_plecare === '' && oras_destinatie === ''){
          filterMatchPush(filter_type, paralela45_strainatate_routes.Cities[oras_d].ZoneCode, totals, filter_data, results, params, grouping, info);
          continue;
        }
        var orase_dp;
        if(filter_type === 'date_plecare' || data_plecare === ''){
          orase_dp = orase_d[oras_d];
        }
        else {
          if(typeof orase_d[oras_d][data_plecare] === 'undefined'){
            continue;
          }
          if(filter_type === 'oras_plecare' && numar_nopti === ''){
            filterMatchPush(filter_type, oras_p, totals, filter_data, results, params, grouping, info);
            break LoopOraseDestinatie;
          }
          else if(filter_type === 'tara_destinatie' && numar_nopti === ''){
            filterMatchPush(filter_type, paralela45_strainatate_routes.Cities[oras_d].CountryCode, totals, filter_data, results, params, grouping, info);
            continue;
          }
          else if(filter_type === 'zona_destinatie' && numar_nopti === ''){
            filterMatchPush(filter_type, paralela45_strainatate_routes.Cities[oras_d].ZoneCode, totals, filter_data, results, params, grouping, info);
            continue;
          }
          else if(filter_type === 'oras_destinatie' && numar_nopti === ''){
            filterMatchPush(filter_type, oras_d, totals, filter_data, results, params, grouping, info);
            continue;
          }
          orase_dp = {};
          orase_dp[data_plecare] = orase_d[oras_d][data_plecare];
        }
        LoopDate:
        for(var oras_date in orase_dp){
          if (!orase_dp.hasOwnProperty(oras_date)) {
            continue;
          }
          if(filter_type === 'nopti' || numar_nopti !== ''){
            var date_nights_arr = orase_d[oras_d][oras_date].Nights.split(',');
          }
          if(filter_type !== 'nopti'){
            if(numar_nopti !== '' && date_nights_arr.indexOf(numar_nopti) < 0){
              continue;
            }
            if(filter_type === 'oras_plecare'){
              filterMatchPush(filter_type, oras_p, totals, filter_data, results, params, grouping, info);
              break LoopOraseDestinatie;
            } else if(filter_type === 'tara_destinatie'){
              filterMatchPush(filter_type, paralela45_strainatate_routes.Cities[oras_d].CountryCode, totals, filter_data, results, params, grouping, info);
              break LoopDate;
            } else if(filter_type === 'zona_destinatie'){
              filterMatchPush(filter_type, paralela45_strainatate_routes.Cities[oras_d].ZoneCode, totals, filter_data, results, params, grouping, info);
              break LoopDate;
            } else if(filter_type === 'oras_destinatie'){
              filterMatchPush(filter_type, oras_d, totals, filter_data, results, params, grouping, info);
              break LoopDate;
            } else if(filter_type === 'date_plecare'){
              filterMatchPush(filter_type, oras_date, totals, filter_data, results, params, grouping, info);
              continue;
            }
          }
          else {
            for(var i=0; i<date_nights_arr.length;i++){
              var date_nights = date_nights_arr[i];
              filterMatchPush(filter_type, date_nights, totals, filter_data, results, params, grouping, info);
            }
          }
        }
      }
    }
    if(results.length){
      if(filter_type === 'oras_plecare' || filter_type === 'tara_destinatie'){
        results.sort(SortByText);
      }
      else if(filter_type === 'date_plecare'){
        results.sort(SortById);
      }
      else if(filter_type === 'nopti'){
        results.sort(SortByNights);
      }
      else if(filter_type === 'oras_destinatie'){
        results.sort(SortByText);
        for(var i=0; i<results.length; i++){
          results[i].children.sort(SortByText);
        }
      }
    }
    if(!totals.length){
      var all_results_item = {
        disabled: true,
        text: 'Nu au fost gasite rezultate'
      };
      results.push(all_results_item);
    }
    if(!totals.length || filter_data.length === totals.length){
      if((filter_type === 'oras_destinatie' && (zona_destinatie !== '' || tara_destinatie !== '' || oras_plecare !== '' || numar_nopti !== '' || data_plecare !== ''))
      || (filter_type === 'oras_plecare' && (zona_destinatie !== '' || tara_destinatie !== '' || oras_destinatie !== '' || numar_nopti !== '' || data_plecare !== ''))
      || (filter_type === 'date_plecare' && (zona_destinatie !== '' || tara_destinatie !== '' || oras_destinatie !== '' || oras_plecare !== '' || numar_nopti !== ''))
      || (filter_type === 'nopti' && (zona_destinatie !== '' || tara_destinatie !== '' || oras_destinatie !== '' || oras_plecare !== '' || data_plecare !== ''))
      || (filter_type === 'tara_destinatie' && (zona_destinatie !== '' || oras_destinatie !== '' || oras_plecare !== '' || data_plecare !== '' || numar_nopti !== ''))
      || (filter_type === 'zona_destinatie' && (tara_destinatie !== '' || oras_destinatie !== '' || oras_plecare !== '' || data_plecare !== '' || numar_nopti !== ''))
      ){
        var all_results_item = {
          id: '-',
          text: '<div class="select2_4-all-options" ><strong class="btn btn-block btn-sm">- Vezi toate optiunile -</strong></div>'
        };
        results.push(all_results_item);
      }
    }
    success_data = {
      results: results,
      pagination: {
        more: false
      }
    };
    success(success_data);
  }
  $('#categoriePax1').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Plecare din', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#categoriePax1');
        var $select2_data = $this.data('select2');
        if($select2_data && typeof $select2_data.$results){
          $select2_data.$results.empty();
        }
        filterSelect2Field('oras_plecare', params, success, failure);
      },
      processResults: function (data, params) {
        return data;
      }
    },
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      return item.text;
    },
    templateSelection: function(item) {
      if(item.id === ''){
        return item.text;
      }
      return 'Plecare din: <strong>' + item.text + '</strong>';
    }
  });
  $('#taraPax1').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Tara', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#taraPax1');
        var $select2_data = $this.data('select2');
        if($select2_data && typeof $select2_data.$results){
          $select2_data.$results.empty();
        }
        filterSelect2Field('tara_destinatie', params, success, failure);
      },
      processResults: function (data, params) {
        return data;
      }
    },
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      return item.text;
    },
    templateSelection: function(item) {
      if(item.id === ''){
        return item.text;
      }
      return 'Tara: <strong>' + item.text + '</strong>';
    }
  });
  $('#zonePax1').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Destinatie', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#zonePax1');
        var $select2_data = $this.data('select2');
        if($select2_data && typeof $select2_data.$results){
          $select2_data.$results.empty();
        }
        filterSelect2Field('zona_destinatie', params, success, failure);
      },
      processResults: function (data, params) {
        return data;
      }
    },
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      var tara_destinatie = $('#taraPax1').val();
      if(!item.hasOwnProperty('id')){
        return item.text;
      }
      if(item.id === '-' || item.id === ''){
        return item.text;
      }
      if(item.hasOwnProperty('children')){
        return item.text.toUpperCase() + '<span class="float-right">(' + item.children.length + ')</span>';
      }
      if(tara_destinatie !== ''){
        return item.zone;
      }
      return item.zone + ', <small>' + item.country + '</small>';
    },
    templateSelection: function(item) {
      var tara_destinatie = $('#taraPax1').val();
      if(item.id === ''){
        return item.text;
      }
      if(tara_destinatie !== ''){
        return 'Destinatie: <strong>' + item.zone + '</strong>';
      }
      return 'Destinatie: <strong>' + item.zone + '</strong>, <small>' + item.country + '</small>';
    }
  });
  $('#destinatiePax1').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Oras destinatie (optional)', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#destinatiePax1');
        var $select2_data = $this.data('select2');
        if($select2_data && typeof $select2_data.$results){
          $select2_data.$results.empty();
        }
        filterSelect2Field('oras_destinatie', params, success, failure);
      },
      processResults: function (data, params) {
        return data;
      }
    },
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      var tara_destinatie = $('#taraPax1').val();
      if(!item.hasOwnProperty('id')){
        return item.text;
      }
      if(item.id === '-' || item.id === ''){
        return item.text;
      }
      if(item.hasOwnProperty('children')){
        return item.text.toUpperCase() + '<span class="float-right">(' + item.children.length + ')</span>';
      }
      if(tara_destinatie !== ''){
        return item.city;
      }
      return item.city + ', <small>' + item.country + '</small>';
    },
    templateSelection: function(item) {
      var tara_destinatie = $('#taraPax1').val();
      if(item.id === ''){
        return item.text;
      }
      if(tara_destinatie !== ''){
        return 'Oras destinatie: <strong>' + item.city + '</strong>';
      }
      return 'Oras destinatie: <strong>' + item.city + '</strong>, <small>' + item.country + '</small>';
    }
  });
  $('#datePax1').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Alege data', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#datePax1');
        var $select2_data = $this.data('select2');
        if($select2_data && typeof $select2_data.$results){
          $select2_data.$results.empty();
        }
        filterSelect2Field('date_plecare', params, success, failure);
      },
      processResults: function (data, params) {
        return data;
      }
    },
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      if(!item.hasOwnProperty('id')){
        return item.text;
      }
      if(item.id === '' || item.id === '-'){
        return item.text;
      }
      if(item.hasOwnProperty('children')){
        return item.text.toUpperCase() + '<span class="float-right">(' + item.children.length + ')</span>';
      }
      return item.text;// + ', <small>' + item.Companie + '</small>';
    },
    templateSelection: function(item) {
      if(item.id === ''){
        return item.text;
      }
      return 'La data: <strong>' + item.text + '</strong>'//, <small>' + item.Companie + '</small>';
    }
  });
  $('#categPax1').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'- Numar nopti -', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#categPax1');
        var $select2_data = $this.data('select2');
        if($select2_data && typeof $select2_data.$results){
          $select2_data.$results.empty();
        }
        filterSelect2Field('nopti', params, success, failure);
      },
      processResults: function (data, params) {
        return data;
      }
    },
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      if(!item.hasOwnProperty('id')){
        return item.text;
      }
      if(item.id === '-'){
        return item.text;
      }
      if(item.text === '1'){
        return '1 noapte';
      }
      return item.text + ' nopti';
    },
    templateSelection: function(item) {
      if(item.id === '' || item.id === '0'){
        return item.text;
      }
      return '<strong>' + item.text + '</strong> nopti';
    }
  });
  // console.log(paralela45_strainatate_routes);
  $('#categoriePax1,#destinatiePax1,#datePax1,#categPax1,#taraPax1,#zonePax1').on('change', function(event){
    var prevent_close = $(this).data('prevent_close');
    if(initting_data){
      event.preventDefault();
      event.stopPropagation();
      return;
    }
    var oras_destinatie = $.trim($('#destinatiePax1').val());
    var zona_destinatie = $.trim($('#zonePax1').val());
    $('#zonePax1,#destinatiePax1').prop('required', false);
    if(oras_destinatie !== ''){
      $('#zonePax1').prop('required', false);
    } else if(zona_destinatie !== ''){
      $('#destinatiePax1').prop('required', false);
    }
    if(!prevent_close && $.trim(this.value) !== '' && $.trim(this.value) !== '-'){
      if(this.id === 'destinatiePax1'){
        var city_code = this.value;
        if(paralela45_strainatate_routes.CityLinks.Destination[city_code]){
          var zone_code = paralela45_strainatate_routes.Cities[city_code].ZoneCode;
          var zone = paralela45_strainatate_routes.Zones[zone_code];
          var item = {
            id: zone_code,
            text: zone.ZoneName + ' ' + paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName,
            zone: zone.ZoneName,
            country: paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName
          };
          $('#zonePax1').select2_4('trigger','select', {
            data: item
          });
          var item = {
            id: paralela45_strainatate_routes.Cities[city_code].CountryCode,
            text: paralela45_strainatate_routes.Countries[paralela45_strainatate_routes.Cities[city_code].CountryCode].CountryName
          };
          $('#taraPax1').select2_4('trigger','select', {
            data: item
          });
        }
      } else if(this.id === 'zonePax1'){
        var zone_code = this.value;
        if(paralela45_strainatate_routes.Zones[zone_code]){
          var item = {
            id: paralela45_strainatate_routes.Zones[zone_code].CountryCode,
            text: paralela45_strainatate_routes.Countries[paralela45_strainatate_routes.Zones[zone_code].CountryCode].CountryName
          };
          $('#taraPax1').select2_4('trigger','select', {
            data: item
          });
        }
      }
      var $that = $(this);
      $('#categoriePax1,#destinatiePax1,#datePax1,#categPax1,#taraPax1,#zonePax1').filter(function(){
        var check = !$that.is(this) && $.trim(this.value) === '';
        if(check){
          if(this.id==='destinatiePax1' && $.trim($('#zonePax1')==='')){
            check = false;
          }
        }
        return check;
      }).first().select2_4('open');
    }
  }).on('select2_4:close',function(event){
    if(initting_data){
      event.preventDefault();
      event.stopPropagation();
      return;
    }
    if($.trim(this.value) === '-'){
      $('#destinatiePax1,#categoriePax1,#datePax1,#categPax1,#taraPax1,#zonePax1').val(null).trigger('change.select2_4');
      $(this).select2_4('open');
    }
    if($opened_select2 && $opened_select2.length && $opened_select2.is(this)){
      $opened_select2 = null;
    }
  }).on('select2_4:closing',function(event){
    var prevent_close = $(this).data('prevent_close');
    $(this).data('prevent_close', null);
    if(prevent_close){
      event.preventDefault();
      event.stopPropagation();
      return false;
    }
  }).on('select2_4:open',function(event){
    $opened_select2 = $(this);
  }).on('select2_4:unselecting',function(event){
    if($opened_select2 && $opened_select2.length && !$opened_select2.is(this)){
      $opened_select2.select2_4('close');
    }
  });
  $("#copiiCam1Pax1").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#cam1Pax1 .varsteCopii").show();
        $("#varstaCop1Cam1Pax1").show();
        $("#varstaCop2Cam1Pax1").hide();
        $("#cam1Pax1 .varsteCopii p#v1Pax11").show();
        $("#cam1Pax1 .varsteCopii p#v2Pax11").hide();
      }
      if (optionValue == 3) {
        $("#cam1Pax1 .varsteCopii").show();
        $("#varstaCop2Cam1Pax1").show();
        $("#varstaCop1Cam1Pax1").show();
        $("#cam1Pax1 .varsteCopii p#v1Pax11").show();
        $("#cam1Pax1 .varsteCopii p#v2Pax11").show();
      }
      if (optionValue == 1) {
        $("#cam1Pax1 .varsteCopii").hide();
        $("#varstaCop1Cam1Pax1").hide();
        $("#varstaCop2Cam1Pax1").hide();
        $("#cam1Pax1 .varsteCopii p#v1Pax11").hide();
        $("#cam1Pax1 .varsteCopii p#v2Pax11").hide();
      }
    });
  });
  //adaugare & stergere camera 2 hotel
  $("#addCam2Pax1").on("click", function () {
    $("#cam2Pax1").show();
    $("#addCam2Pax1").parent().hide();
    $("#addCam3Pax1").parent().show();
    $("#remCam3Pax1").parent().show();
  });
  $("#remCam2Pax1").on("click", function () {
    $("#cam2Pax1").hide();
    $("#addCam2Pax1").parent().show();
    $("#cam2Pax1 .varsteCopii").hide();
    $('#copiiCam2Pax1 option').prop('selected', function () {
      return this.defaultSelected;
    });
  });
  $("#copiiCam2Pax1").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#cam2Pax1 .varsteCopii").show();
        $("#varstaCop1Cam2Pax1").show();
        $("#varstaCop2Cam2Pax1").hide();
        $("#cam2Pax1 .varsteCopii p#v1Pax12").show();
        $("#cam2Pax1 .varsteCopii p#v2Pax12").hide();
      }
      if (optionValue == 3) {
        $("#cam2Pax1 .varsteCopii").show();
        $("#varstaCop2Cam2Pax1").show();
        $("#varstaCop1Cam2Pax1").show();
        $("#cam2Pax1 .varsteCopii p#v1Pax12").show();
        $("#cam2Pax1 .varsteCopii p#v2Pax12").show();
      }
      if (optionValue == 1) {
        $("#cam2Pax1 .varsteCopii").hide();
        $("#varstaCop1Cam2Pax1").hide();
        $("#varstaCop2Cam2Pax1").hide();
        $("#cam2Pax1 .varsteCopii p#v1Pax12").hide();
        $("#cam2Pax1 .varsteCopii p#v2Pax12").hide();
      }
    });
  });
  //adaugare & stergere camera 3 hotel
  $("#addCam3Pax1").on("click", function () {
    $("#cam3Pax1").show();
    $("#addCam3Pax1").parent().hide();
    $("#remCam2Pax1").parent().hide();
  });
  $("#remCam3Pax1").on("click", function () {
    $("#cam3Pax1").hide();
    $("#addCam3Pax1").parent().show();
    $("#remCam2Pax1").parent().show();
    $("#cam3Pax1 .varsteCopii").hide();
    $('#copiiCam3Pax1 option').prop('selected', function () {
      return this.defaultSelected;
    });
  });
  $("#copiiCam3Pax1").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#cam3Pax1 .varsteCopii").show();
        $("#varstaCop1Cam3Pax1").show();
        $("#varstaCop2Cam3Pax1").hide();
        $("#cam3Pax1 .varsteCopii p#v1Pax13").show();
        $("#cam3Pax1 .varsteCopii p#v2Pax13").hide();
      }
      if (optionValue == 3) {
        $("#cam3Pax1 .varsteCopii").show();
        $("#varstaCop2Cam3Pax1").show();
        $("#varstaCop1Cam3Pax1").show();
        $("#cam3Pax1 .varsteCopii p#v1Pax13").show();
        $("#cam3Pax1 .varsteCopii p#v2Pax13").show();
      }
      if (optionValue == 1) {
        $("#cam3Pax1 .varsteCopii").hide();
        $("#varstaCop1Cam3Pax1").hide();
        $("#varstaCop2Cam3Pax1").hide();
        $("#cam3Pax1 .varsteCopii p#v1Pax13").hide();
        $("#cam3Pax1 .varsteCopii p#v2Pax13").hide();
      }
    });
  });
  
  if(paralela45_strainatate_search_data){
    // console.log(paralela45_strainatate_search_data,JSON.parse(JSON.stringify(paralela45_strainatate_search_data)));
    if($.trim(paralela45_strainatate_search_data.destination).length){
      var city_code = $.trim(paralela45_strainatate_search_data.destination);
      if(paralela45_strainatate_routes.CityLinks.Destination[city_code]){
        var item = {
          id: city_code,
          text: paralela45_strainatate_routes.Cities[city_code].CityName + ' ' + paralela45_strainatate_routes.Countries[paralela45_strainatate_routes.Cities[city_code].CountryCode].CountryName,
          city: paralela45_strainatate_routes.Cities[city_code].CityName,
          country: paralela45_strainatate_routes.Countries[paralela45_strainatate_routes.Cities[city_code].CountryCode].CountryName
        };
        $('#destinatiePax1').select2_4('trigger','select', {
          data: item
        });
        var zone_code = paralela45_strainatate_routes.Cities[city_code].ZoneCode;
        var zone = paralela45_strainatate_routes.Zones[zone_code];
        var item = {
          id: zone_code,
          text: zone.ZoneName + ' ' + paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName,
          zone: zone.ZoneName,
          country: paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName
        };
        $('#zonePax1').select2_4('trigger','select', {
          data: item
        });
        var item = {
          id: paralela45_strainatate_routes.Cities[city_code].CountryCode,
          text: paralela45_strainatate_routes.Countries[paralela45_strainatate_routes.Cities[city_code].CountryCode].CountryName
        };
        $('#taraPax1').select2_4('trigger','select', {
          data: item
        });
        $('#zonePax1').prop('required', false);
      }
    }
    if($.trim(paralela45_strainatate_search_data.zone).length){
      var zone_code = $.trim(paralela45_strainatate_search_data.zone);
      if(paralela45_strainatate_routes.Zones[zone_code]){
        var zone = paralela45_strainatate_routes.Zones[zone_code];
        var item = {
          id: zone_code,
          text: zone.ZoneName + ' ' + paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName,
          zone: zone.ZoneName,
          country: paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName
        };
        $('#zonePax1').select2_4('trigger','select', {
          data: item
        });
        var item = {
          id: zone.CountryCode,
          text: paralela45_strainatate_routes.Countries[zone.CountryCode].CountryName
        };
        $('#taraPax1').select2_4('trigger','select', {
          data: item
        });
        $('#destinatiePax1').prop('required', false);
      }
    }
    if($.trim(paralela45_strainatate_search_data.origin).length){
      var city_code = $.trim(paralela45_strainatate_search_data.origin);
      if(paralela45_strainatate_routes.CityLinks.Departure[city_code]){
        var item = {
          id: city_code,
          text: paralela45_strainatate_routes.Cities[city_code].CityName
        };
        $('#categoriePax1').select2_4('trigger','select', {
          data: item
        });
      }
    }
    
    $('#numeHotelPax1Abr').val(paralela45_strainatate_search_data.hotel_name);
    if($.trim(paralela45_strainatate_search_data.start_date) !== ''){
      var data_plecare = moment(paralela45_strainatate_search_data.start_date,'Y-MM-DD');
      var item = {
        id: paralela45_strainatate_search_data.start_date,
        text: data_plecare.locale('ro').format("DD/MM/Y (dddd, D MMMM)")
      };
      $('#datePax1').select2_4('trigger','select', {
        data: item
      });
    }
    if($.trim(paralela45_strainatate_search_data.nights).length){
      var nights = $.trim(paralela45_strainatate_search_data.nights);
      if(nights === '0'){
      } else {
        var item = {
          id: nights,
          text: nights
        };
        $('#categPax1').select2_4('trigger','select', {
          data: item
        });
      }
    }
    if(paralela45_strainatate_search_data.occupancy && paralela45_strainatate_search_data.occupancy.length){
      for (var i=0; i<paralela45_strainatate_search_data.occupancy.length; i++){
        var room_index = i+1;
        if(room_index > 3){
          break;
        }
        var occupancy = paralela45_strainatate_search_data.occupancy[i];
        $('#cam' + room_index + 'Pax1').show();
        $('#adultiCam' + room_index + 'Pax1').val(occupancy.adt);
        if(typeof paralela45_strainatate_search_data.occupancy[room_index] !== 'undefined'){
          if(room_index == 3 || room_index == paralela45_strainatate_search_data.occupancy){
            $('#addCam' + (room_index + 1) + 'Pax1').parent().show();
            $('#remCam' + (room_index) + 'Pax1').parent().show();
          } else {
            $('#addCam' + (room_index + 1) + 'Pax1').parent().hide();
            $('#remCam' + (room_index) + 'Pax1').parent().hide();
          }
        }
        $('#cam' + room_index + 'Pax1 .varsteCopii').hide();
        $('#cam' + room_index + 'Pax1 .varsteCopii #varstaCop1Cam' + room_index + 'Pax1').hide();
        $('#cam' + room_index + 'Pax1 .varsteCopii #varstaCop2Cam' + room_index + 'Pax1').hide();
        $('#cam' + room_index + 'Pax1 .varsteCopii p#v1Pax1' + room_index + '').hide();
        $('#cam' + room_index + 'Pax1 .varsteCopii p#v2Pax1' + room_index + '').hide();
        if(occupancy.chd){
          $('#copiiCam' + room_index + 'Pax1').val(occupancy.chd.length+1);
          $('#cam' + room_index + 'Pax1 .varsteCopii').show();
          for (var j=0; j<occupancy.chd.length; j++){
            var child_index = j+1;
            if(child_index>2){
              break;
            }
            var child_age = occupancy.chd[j];
            $('#v' + child_index + 'Pax1' + room_index + '').show();
            $('#varstaCop' + child_index + 'Cam' + room_index + 'Pax1').val(parseInt(child_age)+1).show();
          }
        }
      }
    }
  }
  initting_data = false;
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>