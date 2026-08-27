<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
$availabilities = $this->_ci->Paralela45_model->availabilitiesRequest(true);
$service_types = $this->_ci->Paralela45_model->serviceTypesRequest(true);
if($can_write){ ?>
<script>
(function($){
  var paralela45_service_types = <?php echo json_encode((object)$service_types); ?>;
  var paralela45_availabilities = <?php echo json_encode((object)$availabilities); ?>;
  var initting_data = true;
  var results_page = 1;
  var results_per_page = 10;
  var items_sort = {
    order: 'Price',
    direction: 'ASC'
  };
  var paralela45_circuit_search_data = <?php echo json_encode($this->paralela45_circuit_search_data); ?>;
  var paralela45_circuit_submit_function;
  var paralela45_circuit_routes = <?php echo json_encode($this->getCircuitSearchCityResponse); ?>;
  var paralela45_circuit_results;
  function SortPrices(a, b){
    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
  }
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
  function SortResults(a, b){
    if(items_sort.order == 'Price'){
      var aName = parseFloat(a.Price);
      var bName = parseFloat(b.Price); 
    }
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0)) * (items_sort.direction == 'ASC' ? 1 : -1);
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
  function filterMatchPush(filter_type, id, totals, filter_data, results, params, grouping){
    if(filter_data.indexOf(id) > -1){
      return;
    }
    if(filter_type === 'oras_plecare'){
      if(typeof paralela45_circuit_routes.CityLinks.Departure[id] === 'undefined'){
        console.log('Departure city undefined:' + id);
        return;
      }
    }
    else if(filter_type === 'oras_destinatie'){
      if(typeof paralela45_circuit_routes.CityLinks.Destination[id] === 'undefined'){
        console.log('Destination city undefined:' + id);
        return;
      }
    }
    else if(filter_type === 'tara_destinatie'){
      if(typeof paralela45_circuit_routes.Countries[id] === 'undefined'){
        console.log('Destination country undefined:' + id);
        return;
      }
      var country = paralela45_circuit_routes.Countries[id];
    }
    else if(filter_type === 'zona_destinatie'){
      if(typeof paralela45_circuit_routes.Zones[id] === 'undefined'){
        console.log('Destination zone undefined:' + id);
        return;
      }
      var zone = paralela45_circuit_routes.Zones[id];
    }
    if(filter_type === 'oras_plecare' || filter_type === 'oras_destinatie'){
      if(typeof paralela45_circuit_routes.Cities[id] === 'undefined'){
        console.log('City undefined:' + id);
        return;
      }
      var city = paralela45_circuit_routes.Cities[id];
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
      if(typeof paralela45_circuit_routes.Countries[zone.CountryCode] === 'undefined'){
        console.log('Invalid country for zone:' + id);
        return;
      }
      var country = paralela45_circuit_routes.Countries[zone.CountryCode];
      
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
      if(typeof paralela45_circuit_routes.Countries[city.CountryCode] === 'undefined'){
        console.log('Invalid country for city:' + id);
        return;
      }
      var country = paralela45_circuit_routes.Countries[city.CountryCode];
      
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
    else if(filter_type === 'date_plecare'){
      var mid = id.substring(0,7);
      if(totals.indexOf(mid) < 0){
        var data_plecare = moment(mid,'Y-MM');
        var text = data_plecare.locale('ro').format("MMMM Y");
        if(Select2Matcher(params.data.term, text)){
          totals.push(mid);
          var item = {
            id: mid,
            text: text
          };
          results.push(item);
        }
      }
      // var data_plecare = moment(id,'Y-MM-DD');
      // var text = data_plecare.locale('ro').format("DD/MM/Y (dddd, D MMMM)");
      // if(Select2Matcher(params.data.term, text)){
        // totals.push(id);
        // var item = {
          // id: id,
          // text: text
        // };
        // results.push(item);
      // }
    }
    else {
      var text = id;
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
    var oras_destinatie = $.trim($('#destinatiePax2').val());
    if(oras_destinatie !== '' && !paralela45_circuit_routes.Cities[oras_destinatie]){
      oras_destinatie = '';
    }
    var zona_destinatie = $.trim($('#zonePax2').val());
    if(zona_destinatie !== '' && !paralela45_circuit_routes.Zones[zona_destinatie] || (oras_destinatie !== '' && zona_destinatie != paralela45_circuit_routes.Cities[oras_destinatie].ZoneCode)){
      zona_destinatie = '';
    }
    var tara_destinatie = $.trim($('#taraPax2').val());
    if(tara_destinatie !== '' && !paralela45_circuit_routes.Countries[tara_destinatie] || (oras_destinatie !== '' && tara_destinatie != paralela45_circuit_routes.Cities[oras_destinatie].CountryCode) || (zona_destinatie !== '' && tara_destinatie != paralela45_circuit_routes.Zones[zona_destinatie].CountryCode)){
      tara_destinatie = '';
    }
    var oras_plecare = $.trim($('#categoriePax2').val());
    if(oras_plecare !== '' && !paralela45_circuit_routes.Cities[oras_plecare]){
      oras_plecare = '';
    }
    var numar_nopti = $.trim($('#categPax2').val());
    if(numar_nopti !== '' && parseInt(numar_nopti)<=0){
      numar_nopti = '';
    }
    var data_plecare = $.trim($('#datePax2').val());
    if(data_plecare === '13-13'){
      data_plecare = '';
    }
    if(filter_type === 'oras_plecare' || oras_plecare === ''){
      orase_p = paralela45_circuit_routes.Months;
    } else {
      orase_p = {};
      if(typeof paralela45_circuit_routes.Months[oras_plecare] !== 'undefined'){
        orase_p[oras_plecare] = paralela45_circuit_routes.Months[oras_plecare];
      }
      /* if(data_plecare.length == 7){
      } else {
        if(typeof paralela45_circuit_routes.Dates[oras_plecare] !== 'undefined'){
          orase_p[oras_plecare] = paralela45_circuit_routes.Dates[oras_plecare];
        }
      } */
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
        if(filter_type !== 'zona_destinatie' && zona_destinatie !== '' && paralela45_circuit_routes.Cities[oras_d].ZoneCode !== zona_destinatie){
          continue;
        }
        if(filter_type !== 'tara_destinatie' && tara_destinatie !== '' && paralela45_circuit_routes.Cities[oras_d].CountryCode !== tara_destinatie){
          continue;
        }
        if(filter_type === 'oras_destinatie' && numar_nopti === '' && data_plecare === ''){
          filterMatchPush(filter_type, oras_d, totals, filter_data, results, params, grouping, info);
          continue;
        }
        if(filter_type === 'tara_destinatie' && numar_nopti === '' && data_plecare === '' && oras_destinatie === ''){
          filterMatchPush(filter_type, paralela45_circuit_routes.Cities[oras_d].CountryCode, totals, filter_data, results, params, grouping, info);
          continue;
        }
        if(filter_type === 'zona_destinatie' && numar_nopti === '' && data_plecare === '' && oras_destinatie === ''){
          filterMatchPush(filter_type, paralela45_circuit_routes.Cities[oras_d].ZoneCode, totals, filter_data, results, params, grouping, info);
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
            filterMatchPush(filter_type, paralela45_circuit_routes.Cities[oras_d].CountryCode, totals, filter_data, results, params, grouping, info);
            continue;
          }
          else if(filter_type === 'zona_destinatie' && numar_nopti === ''){
            filterMatchPush(filter_type, paralela45_circuit_routes.Cities[oras_d].ZoneCode, totals, filter_data, results, params, grouping, info);
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
          for(var l=0; l<orase_dp[oras_date].length;l++){
            var oras_data = orase_dp[oras_date][l];
            if(filter_type === 'nopti' || numar_nopti !== ''){
              var date_nights_arr = oras_data.Nights.split(',');
            }
            if(filter_type !== 'nopti'){
              if(numar_nopti !== '' && date_nights_arr.indexOf(numar_nopti) < 0){
                continue;
              }
              if(filter_type === 'oras_plecare'){
                filterMatchPush(filter_type, oras_p, totals, filter_data, results, params, grouping, info);
                break LoopOraseDestinatie;
              } else if(filter_type === 'tara_destinatie'){
                filterMatchPush(filter_type, paralela45_circuit_routes.Cities[oras_d].CountryCode, totals, filter_data, results, params, grouping, info);
                break LoopDate;
              } else if(filter_type === 'zona_destinatie'){
                filterMatchPush(filter_type, paralela45_circuit_routes.Cities[oras_d].ZoneCode, totals, filter_data, results, params, grouping, info);
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
  $('#categoriePax2').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Plecare din (optional)', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#categoriePax2');
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
  $('#taraPax2').select2_4({
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
        var $this = $('#taraPax2');
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
  }).on('select2_4:unselecting',function(){
    $('#destinatiePax2').val(null).trigger('change.select2_4');
  });
  $('#zonePax2').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Destinatie (optional)', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#zonePax2');
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
      var tara_destinatie = $('#taraPax2').val();
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
      var tara_destinatie = $('#taraPax2').val();
      if(item.id === ''){
        return item.text;
      }
      if(tara_destinatie !== ''){
        return 'Destinatie: <strong>' + item.zone + '</strong>';
      }
      return 'Destinatie: <strong>' + item.zone + '</strong>, <small>' + item.country + '</small>';
    }
  });
  $('#destinatiePax2').select2_4({
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
        var $this = $('#destinatiePax2');
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
      var tara_destinatie = $('#taraPax2').val();
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
      var tara_destinatie = $('#taraPax2').val();
      if(item.id === ''){
        return item.text;
      }
      if(tara_destinatie !== ''){
        return 'Oras destinatie: <strong>' + item.city + '</strong>';
      }
      return 'Oras destinatie: <strong>' + item.city + '</strong>, <small>' + item.country + '</small>';
    }
  });
  $('#datePax2').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Alege data (optional)', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#datePax2');
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
  $('#categPax2').select2_4({
    language:'ro',
    theme:'bootstrap',
    placeholder:'Numar nopti (optional)', 
    searchInputPlaceholder: 'Cautare...',
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      delay: 250,
      transport: function (params, success, failure) {
        var $this = $('#categPax2');
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
  // console.log(paralela45_circuit_routes);
  $('#categoriePax2,#destinatiePax2,#datePax2,#categPax2,#taraPax2,#zonePax2').on('change', function(event){
    var prevent_close = $(this).data('prevent_close');
    if(initting_data){
      event.preventDefault();
      event.stopPropagation();
      return;
    }
    var oras_destinatie = $.trim($('#destinatiePax2').val());
    var zona_destinatie = $.trim($('#zonePax2').val());
    $('#zonePax2,#destinatiePax2').prop('required', false);
    if(oras_destinatie !== ''){
      $('#zonePax2').prop('required', false);
    } else if(zona_destinatie !== ''){
      $('#destinatiePax2').prop('required', false);
    }
    if(!prevent_close && $.trim(this.value) !== '' && $.trim(this.value) !== '-'){
      if(this.id === 'destinatiePax2'){
        var city_code = this.value;
        if(paralela45_circuit_routes.CityLinks.Destination[city_code]){
          /* var zone_code = paralela45_circuit_routes.Cities[city_code].ZoneCode;
          var zone = paralela45_circuit_routes.Zones[zone_code];
          var item = {
            id: zone_code,
            text: zone.ZoneName + ' ' + paralela45_circuit_routes.Countries[zone.CountryCode].CountryName,
            zone: zone.ZoneName,
            country: paralela45_circuit_routes.Countries[zone.CountryCode].CountryName
          };
          $('#zonePax2').select2_4('trigger','select', {
            data: item
          }); */
          var item = {
            id: paralela45_circuit_routes.Cities[city_code].CountryCode,
            text: paralela45_circuit_routes.Countries[paralela45_circuit_routes.Cities[city_code].CountryCode].CountryName
          };
          $('#taraPax2').select2_4('trigger','select', {
            data: item
          });
        }
      } else if(this.id === 'zonePax2'){
        var zone_code = this.value;
        if(paralela45_circuit_routes.Zones[zone_code]){
          var item = {
            id: paralela45_circuit_routes.Zones[zone_code].CountryCode,
            text: paralela45_circuit_routes.Countries[paralela45_circuit_routes.Zones[zone_code].CountryCode].CountryName
          };
          $('#taraPax2').select2_4('trigger','select', {
            data: item
          });
        }
      }
      var $that = $(this);
      var found_this = false;
      var found_empty_after_this = false;
      // var $next_selects = $('#taraPax2,#destinatiePax2,#categoriePax2,#datePax2,#categPax2,#zonePax2').filter(function(){
      var $next_selects = $('#taraPax2').filter(function(){
        var check = !$that.is(this) && $.trim(this.value) === '';
        if(!found_this){
          if($that.is(this)){
            found_this = true;
          }
        }
        else {
          if(!found_empty_after_this){
            if(check){
              found_empty_after_this = true;
            }
          } else {
            check = false;
          }
        }
        return check;
      });
      if(found_empty_after_this){
        $next_selects.last().select2_4('open');
      } else {
        $next_selects.first().select2_4('open');
      }
    }
  }).on('select2_4:close',function(event){
    if(initting_data){
      event.preventDefault();
      event.stopPropagation();
      return;
    }
    if($.trim(this.value) === '-'){
      $('#destinatiePax2,#categoriePax2,#datePax2,#categPax2,#taraPax2,#zonePax2').val(null).trigger('change.select2_4');
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
  
  var $error_container = $('#result_serviceCircuitForm');
  var hotel_room_index = 0;
  $('#serviceCircuitForm').on('click','.btn-add-room', function(){
    var $tbody = $(this).closest('table').children('tbody');
    var $new_tr = $('#circuit-room-model').clone().removeAttr('id').data('index',hotel_room_index);
    $('>td:nth-child(3)>div', $new_tr).remove();
    $('input', $new_tr).val(1).attr('name','occupancy[' + hotel_room_index + '][adt]');
    hotel_room_index++;
    $new_tr.appendTo($tbody);
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
    var $tr = $(this).closest('tr');
    var index = $tr.data('index')
    var $td = $(this).closest('td');
    var $new_child = $('#circuit-room-child-model').clone().removeAttr('id');
    $('input.child-age', $new_child).attr('name','occupancy[' + index + '][chd][age][]');
    $('input.child-birth_date', $new_child).attr('name','occupancy[' + index + '][chd][birth_date][]');
    
    // var service_circuit_search_checkin = $.trim($('#datePax2').val());
    // var service_circuit_search_nights = parseInt($('#categPax2').val());
    var service_circuit_search_checkin = moment().format('Y-MM-DD');
    var service_circuit_search_nights = 0;
    if(service_circuit_search_nights > 0 && service_circuit_search_checkin !== ''){
      var reference_moment = moment(service_circuit_search_checkin,'Y-MM-DD').add(service_circuit_search_nights,'days').startOf('day');
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
      // var service_circuit_search_checkin = $.trim($('#datePax2').val());
      // var service_circuit_search_nights = parseInt($('#categPax2').val());
      var service_circuit_search_checkin = moment().format('Y-MM-DD');
      var service_circuit_search_nights = 0;
      if(service_circuit_search_nights > 0 && service_circuit_search_checkin !== ''){
        var reference_moment = moment(service_circuit_search_checkin,'Y-MM-DD').add(service_circuit_search_nights,'days').startOf('day');
      } else {
        var reference_moment = moment().startOf('day');
      }
      var val_moment = moment(this.value,'DD.MM.Y').startOf('day');
      var age_in_years = reference_moment.diff(val_moment,'years');
      $('input.child-age',$(this).closest('.circuit-room-child')).val(age_in_years);
    });
    $new_child.appendTo($td);
  }).on('click','.btn-remove-child', function(){
    var $child = $(this).closest('div.input-group');
    $child.remove();
  });
  
  if(paralela45_circuit_search_data){
    console.log(paralela45_circuit_search_data);
    if($.trim(paralela45_circuit_search_data.country).length){
      var country_code = $.trim(paralela45_circuit_search_data.country);
      if(paralela45_circuit_routes.Countries[country_code]){
        var item = {
          id: country_code,
          text: paralela45_circuit_routes.Countries[country_code].CountryName
        };
        $('#taraPax2').select2_4('trigger','select', {
          data: item
        });
      }
    }
    if($.trim(paralela45_circuit_search_data.destination).length){
      var city_code = $.trim(paralela45_circuit_search_data.destination);
      if(paralela45_circuit_routes.CityLinks.Destination[city_code]){
        var item = {
          id: city_code,
          text: paralela45_circuit_routes.Cities[city_code].CityName + ' ' + paralela45_circuit_routes.Countries[paralela45_circuit_routes.Cities[city_code].CountryCode].CountryName,
          city: paralela45_circuit_routes.Cities[city_code].CityName,
          country: paralela45_circuit_routes.Countries[paralela45_circuit_routes.Cities[city_code].CountryCode].CountryName
        };
        $('#destinatiePax2').select2_4('trigger','select', {
          data: item
        });
        /* var zone_code = paralela45_circuit_routes.Cities[city_code].ZoneCode;
        var zone = paralela45_circuit_routes.Zones[zone_code];
        var item = {
          id: zone_code,
          text: zone.ZoneName + ' ' + paralela45_circuit_routes.Countries[zone.CountryCode].CountryName,
          zone: zone.ZoneName,
          country: paralela45_circuit_routes.Countries[zone.CountryCode].CountryName
        };
        $('#zonePax2').select2_4('trigger','select', {
          data: item
        }); */
        var item = {
          id: paralela45_circuit_routes.Cities[city_code].CountryCode,
          text: paralela45_circuit_routes.Countries[paralela45_circuit_routes.Cities[city_code].CountryCode].CountryName
        };
        $('#taraPax2').select2_4('trigger','select', {
          data: item
        });
        /* $('#zonePax2').prop('required', false); */
      }
    }
    if($.trim(paralela45_circuit_search_data.origin).length){
      var city_code = $.trim(paralela45_circuit_search_data.origin);
      if(paralela45_circuit_routes.CityLinks.Departure[city_code]){
        var item = {
          id: city_code,
          text: paralela45_circuit_routes.Cities[city_code].CityName
        };
        $('#categoriePax2').select2_4('trigger','select', {
          data: item
        });
      }
    }
    
    $('#numeHotelPaxAbr').val(paralela45_circuit_search_data.hotel_name);
    if($.trim(paralela45_circuit_search_data.start_date) !== ''){
      if(paralela45_circuit_search_data.start_date.length == 7){
        var data_plecare = moment(paralela45_circuit_search_data.start_date,'Y-MM');
        if(data_plecare.isValid()){
          var item = {
            id: paralela45_circuit_search_data.start_date,
            text: data_plecare.locale('ro').format("MMMM Y")
          };
          $('#datePax2').select2_4('trigger','select', {
            data: item
          });
        }
      } else {
        var data_plecare = moment(paralela45_circuit_search_data.start_date,'Y-MM-DD');
        if(data_plecare.isValid()){
          var item = {
            id: paralela45_circuit_search_data.start_date,
            text: data_plecare.locale('ro').format("DD/MM/Y (dddd, D MMMM)")
          };
          $('#datePax2').select2_4('trigger','select', {
            data: item
          });
        }
      }
    }
    if($.trim(paralela45_circuit_search_data.nights).length){
      var nights = $.trim(paralela45_circuit_search_data.nights);
      if(nights === '0'){
      } else {
        var item = {
          id: nights,
          text: nights
        };
        $('#categPax2').select2_4('trigger','select', {
          data: item
        });
      }
    }
    if(paralela45_circuit_search_data.occupancy && paralela45_circuit_search_data.occupancy.length){
      for (var i=0; i<paralela45_circuit_search_data.occupancy.length; i++){
        var room_index = i+1;
        if(room_index > 3){
          break;
        }
        $('#serviceCircuitForm .btn-add-room:first')[0].click();
        var $tr = $('#service_circuit_search_rooms_table > tbody > tr:last');
        var occupancy = paralela45_circuit_search_data.occupancy[i];
        $('input[name$="[adt]"]', $tr).val(occupancy.adt);
        if(occupancy.chd){
          for (var j=0; j<occupancy.chd.length; j++){
            if(j>1){
              break;
            }
            $('.btn-add-child:first',$tr)[0].click();
            if(occupancy.birth_date && occupancy.birth_date.length && occupancy.birth_date[j]){
              $('input[name$="[chd][birth_date][]"]:last', $tr).val(occupancy.birth_date[j]);
            }
            $('input[name$="[chd][age][]"]:last', $tr).val(occupancy.chd[j]-1);
          }
        }
      }
    }
  }
  
  var $fellow_info_container = $('#serviceCircuitFormFellows');
  var $fellow_info_wrapper = $('#serviceCircuitFormFellowsFormWrapper');
  var $room_packages_loading = $('#service-circuit-room-packages-loading');
  var $room_packages = $('#service-circuit-room-packages');
  var $hotel_details = $('#service-circuit-circuit-details');
  var $service_circuit_tab = $('#service_circuit_tab');
  var $navigation = $('#serviceCircuitResultsNavigation');
  
  var $price_slider = $("#circuit-services-search-filter-price-slider-range");
  
  function loadRoomOccupancyDetails(){
    // var service_circuit_search_checkin = paralela45_circuit_search_data.start_date;
    // var service_circuit_search_nights = parseInt(paralela45_circuit_search_data.nights);
    var service_circuit_search_checkin = moment().format('Y-MM-DD');
    var service_circuit_search_nights = 0;
    var service_circuit_search_checkin_moment = moment(service_circuit_search_checkin,'Y-MM-DD').startOf('day');
    var service_circuit_search_checkout_moment = moment(service_circuit_search_checkin,'Y-MM-DD').add(service_circuit_search_nights,'days').startOf('day');

    var reference_moment = service_circuit_search_checkout_moment;
    var min_adult_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
    var max_adult_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).startOf('day');
    
    var min_child_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).add(1,'days').startOf('day');
    var max_child_moment = moment().startOf('day');
    var index = 0;
    var adults = 0;
    var children = 0;
    var rooms = paralela45_circuit_search_data.occupancy.length;
    $fellow_info_container.empty();
    $room_packages_loading.hide();
    $room_packages.empty();
    $fellow_info_wrapper.hide();
    for(var rindex=0; rindex<paralela45_circuit_search_data.occupancy.length; rindex++){
      var input_name_prefix = 'room[' + rindex + ']';
      var room_occupancy = paralela45_circuit_search_data.occupancy[rindex];
      var room_number = rindex+1;
      var $hotel_room_fellows = $('#circuit-room-fellows-model').clone().removeAttr('id').hide();
      $hotel_room_fellows.appendTo($fellow_info_container);
      if(rindex>0){
        $hotel_room_fellows.addClass('mt-3');
      }
      var $hotel_room_fellow_container = $('.room-occupancy-fellows', $hotel_room_fellows);
      var room_adults = parseInt(room_occupancy.adt);
      
      var input_name_prefix_adt = input_name_prefix + '[adt]';
      for(var adt_index=1; adt_index<=room_occupancy.adt; adt_index++){
        var input_name_prefix_adt_i = input_name_prefix_adt + '[' + (adt_index-1) +']';
        var $hotel_room_fellow = $('#circuit-room-fellow-adult-model').clone().removeAttr('id');
        if(adt_index>1){
          $hotel_room_fellow.addClass('mt-1');
        }
        $hotel_room_fellow.appendTo($hotel_room_fellow_container);
        var $passenger_title = $('.passenger-title',$hotel_room_fellow);
        $passenger_title.attr({
          name: input_name_prefix_adt_i + '[title]'
        });
        $passenger_title.select2_4({theme:'bootstrap', minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
        
        var $passenger_birth_date = $('.passenger-birth_date',$hotel_room_fellow);
        $passenger_birth_date.attr({
          name: input_name_prefix_adt_i + '[birth_date]'
        });
        var min_child_moment = moment([parseInt(reference_moment.format('Y')) - (child_age+1), parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).add(1,'days').startOf('day');
        $passenger_birth_date.makeCaleranDatepicker({
          minDate: min_adult_moment,
          maxDate: max_adult_moment,
          startDate: max_adult_moment,
          startEmpty: false
        }).makeInputmaskDate();
        var $passenger_lastname = $('.passenger-lastname',$hotel_room_fellow);
        $passenger_lastname.attr({
          name: input_name_prefix_adt_i + '[lastname]'
        });
        var $passenger_firstname = $('.passenger-firstname',$hotel_room_fellow);
        $passenger_firstname.attr({
          name: input_name_prefix_adt_i + '[firstname]'
        });
        if(!rindex && adt_index==1){
          var client_title = $('#client_title').val();
          if(typeof(countries_selections[client_title]) !== 'undefined'){
            $passenger_title.val(client_title).trigger('change.select2_4');
          }
          $passenger_firstname.val($('#client_firstname').val());
          $passenger_lastname.val($('#client_lastname').val());
        }
        <?php if(ENVIRONMENT !== 'production') { ?>
        // BEGIN - TODO - REMOVE
        $passenger_firstname.val('TEST');
        $passenger_lastname.val('TEST');
        $passenger_birth_date.val(max_adult_moment.format('DD.MM.Y'));
        // END - TODO - REMOVE
        <?php } ?>
        $('>.card-header>.fellow-age', $hotel_room_fellow).remove();
        $('>.card-header>.fellow-type>.fellow-index', $hotel_room_fellow).text(i);
      }
      var room_children = 0;
      if(room_occupancy.chd){
        var children_ages = room_occupancy.chd;
        room_children = children_ages.length;
        var children_birth_dates = room_occupancy.birth_date;
        for(var i=0; i<children_ages.length; i++){
          var child_birth_date = children_birth_dates && typeof children_birth_dates[i] !== 'undefined' ? children_birth_dates[i] : '';
          var child_age = children_ages[i];
          var input_name_prefix_chd = input_name_prefix + '[chd]';
          var input_name_prefix_chd_i = input_name_prefix_chd + '[' + (room_children-1) +']';
          var $hotel_room_fellow = $('#circuit-room-fellow-child-model').clone().removeAttr('id');
          $hotel_room_fellow.addClass('mt-1');
          $hotel_room_fellow.appendTo($hotel_room_fellow_container);
          var $passenger_age = $('input.passenger-age',$hotel_room_fellow);
          $passenger_age.attr({
            name: input_name_prefix_chd_i + '[age]'
          }).val(child_age);
          var $passenger_birth_date = $('.passenger-birth_date',$hotel_room_fellow);
          $passenger_birth_date.attr({
            name: input_name_prefix_chd_i + '[birth_date]'
          });
          $passenger_birth_date.val(child_birth_date);
          var min_child_moment = moment([parseInt(reference_moment.format('Y')) - (child_age+1), parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).add(1,'days').startOf('day');
          $passenger_birth_date.makeCaleranDatepicker({
            minDate: min_child_moment,
            maxDate: max_child_moment,
            startDate: max_child_moment,
            startEmpty: false
          }).makeInputmaskDate();
          
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
          <?php if(ENVIRONMENT !== 'production') { ?>
          // BEGIN - TODO - REMOVE
          $passenger_firstname.val('TEST');
          $passenger_lastname.val('TEST');
          $passenger_birth_date.val(min_child_moment.format('DD.MM.Y'));
          // END - TODO - REMOVE
          <?php } ?>
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
    var nights = service_circuit_search_checkout_moment.diff(service_circuit_search_checkin_moment,'days');
    $('#service_circuit_search_hotel_rooms_number').html(rooms);
    if(rooms == 1){
      $('#service_circuit_search_hotel_rooms > .singular').show();
      $('#service_circuit_search_hotel_rooms > .plural').hide();
    } else {
      $('#service_circuit_search_hotel_rooms > .singular').hide();
      $('#service_circuit_search_hotel_rooms > .plural').show();
    }
    $('#service_circuit_search_hotel_adults_number').html(adults);
    if(adults == 1){
      $('#service_circuit_search_hotel_adults > .singular').show();
      $('#service_circuit_search_hotel_adults > .plural').hide();
    } else {
      $('#service_circuit_search_hotel_adults > .singular').hide();
      $('#service_circuit_search_hotel_adults > .plural').show();
    }
    $('#service_circuit_search_hotel_children_number').html(children);
    if(children == 1){
      $('#service_circuit_search_hotel_children > .singular').show();
      $('#service_circuit_search_hotel_children > .plural').hide();
    } else {
      $('#service_circuit_search_hotel_children > .singular').hide();
      $('#service_circuit_search_hotel_children > .plural').show();
    }
    $('#service_circuit_search_hotel_nights_number').html(nights);
    if(nights == 1){
      $('#service_circuit_search_hotel_nights > .singular').show();
      $('#service_circuit_search_hotel_nights > .plural').hide();
    } else {
      $('#service_circuit_search_hotel_nights > .singular').hide();
      $('#service_circuit_search_hotel_nights > .plural').show();
    }
    $fellow_info_wrapper.show();
    $('#overview_serviceCircuitForm').show();
    
    var $search_container_header = $('a[href="#serviceCircuitFormContainer"]:not(.collapsed)');
    if($search_container_header.length){
      $search_container_header[0].click();
    }
    return true;
  }
  $('#service_circuit_search_submit').on('click',function(){
    $('#service_circuit_search_rooms_table .child-birth_date').trigger('update-child-birth_date');
    // var service_circuit_search_checkin = $.trim($('#datePax2').val());
    // var service_circuit_search_nights = parseInt($('#categPax2').val());
    var service_circuit_search_checkin = moment().format('Y-MM-DD');
    var service_circuit_search_nights = 0;
    // if(service_circuit_search_checkin === '' || service_circuit_search_nights <= 0){
      // return true;
    // }
    index = 0;
    $('#service_circuit_search_rooms_table > tbody > tr').each(function(rindex){
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
    $('#service_circuit_search_submit', $service_circuit_tab).prop('disabled',!search_status);
    $('#service_circuit_reserve_submit').prop('readonly',!search_status);
    $('.circuit-sort-by', $service_circuit_tab).prop('readonly',!search_status);
    search_is_over = search_status;
  }
  
  function interpretNoHotelsResponse(result){
    var $search_container_header = $('a[href="#serviceCircuitFormContainer"].collapsed');
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
    $('#serviceCircuitResults').empty();
  }
  
  var hotel_filters;
  function setSort(){
    var sort_element = $('.circuit-sort-by', $service_circuit_tab).filter(function(){return $(this).val()>0;}).first();
    if(sort_element.length){
      paralela45_circuit_search_data.sort_by = sort_element.attr('name');
      paralela45_circuit_search_data.sort_order = parseInt(sort_element.val()) - 1;
    }
  }

  function setFilters(){
    if(typeof paralela45_circuit_search_data.filters === 'undefined'){
      paralela45_circuit_search_data.filters = {};
    }
    paralela45_circuit_search_data.filters.name = $.trim($('#package_filter_by_name').val());
    paralela45_circuit_search_data.filters.period = [];
    $('.circuit-period-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.period.push(parseInt(this.value));
    });
    paralela45_circuit_search_data.filters.availabilities = [];
    $('.circuit-availabilities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.availabilities.push(this.value);
    });
    paralela45_circuit_search_data.filters.dates = [];
    $('.circuit-dates-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.dates.push(this.value);
    });
    paralela45_circuit_search_data.filters.cities = [];
    $('.circuit-cities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.cities.push(this.value);
    });
    paralela45_circuit_search_data.filters.origin_cities = [];
    $('.circuit-origin_cities-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.origin_cities.push(this.value);
    });
    paralela45_circuit_search_data.filters.cheapest = false;
    $('.circuit-cheapest-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.cheapest = true;
    });
    paralela45_circuit_search_data.filters.service_types = [];
    $('.circuit-service_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.service_types.push(this.value);
    });
    paralela45_circuit_search_data.filters.room_types = [];
    $('.circuit-room_types-filter input[type=checkbox]:checked:not(.all_have_it)').each(function(){
      paralela45_circuit_search_data.filters.room_types.push(this.value);
    });
    var price_values = $price_slider.slider('values');
    paralela45_circuit_search_data.filters.min_price = parseFloat(price_values[0]);
    paralela45_circuit_search_data.filters.max_price = parseFloat(price_values[1]);
  }
  function resetFilters(){
    $('.circuit-filter input[type=checkbox]:checked:not(.all_have_it)', $filters_container).prop('checked',false);
    $('#package_filter_by_name').val(null);
    $price_slider.slider('values',[$price_slider.slider('option','min'),$price_slider.slider('option','max')]);
    $price_slider.trigger('updatePrice'); 
  }
  var $filters_container = $('#serviceCircuitFormFilters');
  $('#package_filter_by_name').on('change',function(){
    results_page = 1;
    setFilters();
    filterResults();
  });
  $('.circuit-filter', $filters_container).on('change', 'input[type=checkbox]:not(.all_have_it)',function(){
    results_page = 1;
    setFilters();
    filterResults();
  });
  $('#circuit_reset_filters', $filters_container).click(function(){
    if(!search_is_over){
      console.log('resetFilters','click','Search is not over, aborting');
      return false;
    }
    resetFilters();
    setFilters();
    results_page = 1;
    filterResults();
    var body = $("html, body");
    var pagination_top = $('h1.filterTitle').first().offset().top;
    body.stop().animate({scrollTop:pagination_top}, 200, 'swing', function() { 
    });
  });
  $("#circuit-services-search-filter-price-slider-range", $filters_container).on('slidestop', function (event, ui) {
    if(!search_is_over){
      console.log('circuit-price-slider','change','Search is not over, aborting');
      return false;
    }
    $(this).trigger('updatePrice');
    setFilters();
    results_page = 1;
    filterResults();
  });
  $('.circuit-sort-by', $service_circuit_tab).on('change', function(){
    if(!search_is_over){
      console.log('circuit-sort-by','change','Search is not over, aborting');
      return false;
    }
    var $me = $(this);
    if($me.val() === '0'){
      $me.val('1');
    }
    $('.circuit-sort-by', $service_circuit_tab).filter(function(){return !$(this).is($me);}).val(0);
    setSort();
    results_page = 1;
    interpretResults();
  });
  function clearFilters(){
    $('.circuit-filter', $filters_container).empty();
  }
  var $filter_checkbox_model = $('#circuit-filter-checkbox-model').clone().removeAttr('id').removeClass('circuit-filter-checkbox-model');
  var primary_filters;
  var secondary_filters;
  
  function filterResults(){
    // console.log(paralela45_circuit_search_data.filters);
    results_page = 1;
    var filter_min_price = -1;
    var filter_max_price = -1;
    if(primary_filters.prices.length && typeof paralela45_circuit_search_data.filters.min_price !== 'undefined'){
      filter_min_price = typeof primary_filters.prices[paralela45_circuit_search_data.filters.min_price] !== undefined ? primary_filters.prices[paralela45_circuit_search_data.filters.min_price] : -1;
      filter_max_price = typeof primary_filters.prices[paralela45_circuit_search_data.filters.max_price] !== undefined ? primary_filters.prices[paralela45_circuit_search_data.filters.max_price] : -1;
    }
    paralela45_circuit_results.filtered_offers = [];
    
    var offer_intersection = [];
    var product_codes_with_first_offer = [];
    for (var i=0; i<paralela45_circuit_results.offers.length; i++){
      var offer = paralela45_circuit_results.offers[i];
      var product = paralela45_circuit_results.products[offer.CircuitId];
      if(paralela45_circuit_search_data.filters.cheapest){
        if(product_codes_with_first_offer.indexOf(offer.CircuitId) >= 0){
          continue;
        }
        product_codes_with_first_offer.push(offer.CircuitId);
      }
      if(filter_min_price > 0){
        if(offer.Price < filter_min_price){
          continue;
        }
        if(offer.Price > filter_max_price){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.name.length){
        var name = $.trim(paralela45_circuit_search_data.filters.name).replace(/\s\s+/g, ' ');
        if(!Select2Matcher(name,product.Name)){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.period.length){
        if(paralela45_circuit_search_data.filters.period.indexOf(parseInt(product.Period)) < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.availabilities.length){
        if(paralela45_circuit_search_data.filters.availabilities.indexOf(offer.Availability) < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.dates.length){
        if(paralela45_circuit_search_data.filters.dates.indexOf(offer.InfoCharter.DepArrDate.substring(0,7)) < 0){
          continue;
        }
      }
      var check_type = 1;
      
      if(paralela45_circuit_search_data.filters.cities.length){
        if(product.Destinations && product.Destinations.length){
          var found_city = false;
          for (var j=0; j<product.Destinations.length; j++){
            if(!product.Destinations[j].CityCode){
              continue;
            }
            if(paralela45_circuit_search_data.filters.cities.indexOf(product.Destinations[j].CityCode) > -1){
              found_city = true;
              break;
            }
          }
          if(!found_city){
            continue;
          }
        } else {
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.origin_cities.length){
        if(paralela45_circuit_search_data.filters.origin_cities.indexOf(offer.InfoCharter.DepArrCodLoc) < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.room_types.length){
        if(offer.Rooms && offer.Rooms.length){
          var keep = 0;
          for (var j=0; j<offer.Rooms.length; j++){
            var room = offer.Rooms[j];
            var room_type = room.GCode;
            if(paralela45_circuit_search_data.filters.room_types.indexOf(room_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_circuit_search_data.filters.room_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_circuit_search_data.filters.room_types.length))){
            continue;
          }
        } else if(paralela45_circuit_search_data.filters.room_types.indexOf('') < 0){
          continue;
        }
      }
      if(paralela45_circuit_search_data.filters.service_types.length){
        if(offer.Services && offer.Services.length){
          var keep = 0;
          for (var j=0; j<offer.Services.length; j++){
            var service = offer.Services[j];
            var service_type = '' + service.Type;
            if(paralela45_circuit_search_data.filters.service_types.indexOf(service_type) > -1){
              keep++;
              if(!check_type || (keep == paralela45_circuit_search_data.filters.service_types.length)){
                break;
              }
            }
          }
          if((!check_type && !keep) || (check_type && (keep != paralela45_circuit_search_data.filters.service_types.length))){
            continue;
          }
        } else if(paralela45_circuit_search_data.filters.service_types.indexOf('') < 0){
          continue;
        }
      }
      paralela45_circuit_results.filtered_offers.push(offer);
    }
    loadFilters(true);
    interpretResults();
  }
  var all_cities_names = {};
  function loadFilters(secondary){
    if(typeof secondary === 'undefined'){
      secondary = false;
    }
    var product_codes_with_first_offer = [];
    var filters = {
      cheapest: 0,
      cities: {},
      origin_cities: {},
      availabilities: {},
      dates: {},
      room_types: {},
      service_types: {},
      period: {},
      prices: [],
      currency: '',
    };
    if(secondary){
      var offers = paralela45_circuit_results.filtered_offers;
      secondary_filters = filters;
    } else {
      var offers = paralela45_circuit_results.offers;
      primary_filters = filters;
    }
    for (var i=0; i<offers.length; i++){
      var offer = offers[i];
      if(product_codes_with_first_offer.indexOf(offer.CircuitId) < 0){
        product_codes_with_first_offer.push(offer.CircuitId);
        filters.cheapest++;
      }
      var product = paralela45_circuit_results.products[offer.CircuitId];
      if(filters.currency === ''){
        filters.currency = offer.Currency;
      }
      if(filters.prices.indexOf(offer.Price) < 0){
        filters.prices.push(offer.Price);
      }
      if(typeof filters.period[parseInt(product.Period)] === 'undefined'){
        filters.period[parseInt(product.Period)] = 0;
      }
      filters.period[parseInt(product.Period)]++;
      if(product.Destinations && product.Destinations.length){
        for (var j=0; j<product.Destinations.length; j++){
          if(!product.Destinations[j].CityCode){
            continue;
          }
          if(typeof filters.cities[product.Destinations[j].CityCode] === 'undefined'){
            filters.cities[product.Destinations[j].CityCode] = 0;
          }
          if(typeof all_cities_names[product.Destinations[j].CityCode] === 'undefined'){
            all_cities_names[product.Destinations[j].CityCode] = product.Destinations[j].CityName;
          }
          filters.cities[product.Destinations[j].CityCode] ++;
        }
      }
      if(typeof filters.origin_cities[offer.InfoCharter.DepArrCodLoc] === 'undefined'){
        filters.origin_cities[offer.InfoCharter.DepArrCodLoc] = 0;
      }
      if(typeof all_cities_names[offer.InfoCharter.DepArrCodLoc] === 'undefined'){
        all_cities_names[offer.InfoCharter.DepArrCodLoc] = offer.InfoCharter.DepArrLoc;
      }
      filters.origin_cities[offer.InfoCharter.DepArrCodLoc] ++;
      if(typeof filters.dates[offer.InfoCharter.DepArrDate.substring(0,7)] === 'undefined'){
        filters.dates[offer.InfoCharter.DepArrDate.substring(0,7)] = 0;
      }
      filters.dates[offer.InfoCharter.DepArrDate.substring(0,7)]++;
      if(typeof filters.availabilities[offer.Availability] === 'undefined'){
        filters.availabilities[offer.Availability] = 0;
      }
      filters.availabilities[offer.Availability]++;
      
      if(offer.Rooms && offer.Rooms.length){
        var room_types = [];
        for (var j=0; j<offer.Rooms.length; j++){
          var room = offer.Rooms[j];
          var room_type = room.GCode;
          if(typeof filters.room_types[room_type] === 'undefined'){
            filters.room_types[room_type] = 0;
          }
          if(room_types.indexOf(room_type) < 0){
            room_types.push(room_type);
            filters.room_types[room_type]++;
          }
        }
      } else {
        if(typeof filters.room_types[''] === 'undefined'){
          filters.room_types[''] = 0;
        }
        filters.room_types['']++;
      }
      if(offer.Services && offer.Services.length){
        var service_types = [];
        for (var j=0; j<offer.Services.length; j++){
          var service = offer.Services[j];
          var service_type = '' + service.Type;
          if(typeof filters.service_types[service_type] === 'undefined'){
            filters.service_types[service_type] = 0;
          }
          if(service_types.indexOf(service_type) < 0){
            service_types.push(service_type);
            filters.service_types[service_type]++;
          }
        }
      } else {
        if(typeof filters.service_types[''] === 'undefined'){
          filters.service_types[''] = 0;
        }
        filters.service_types['']++;
      }
    }
    if(typeof paralela45_circuit_search_data.filters === 'undefined'){
      paralela45_circuit_search_data.filters = {};
    }
    $('#circuit-services-search-filter-period').empty();
    if(typeof paralela45_circuit_search_data.filters.name === 'undefined'){
      paralela45_circuit_search_data.filters.name = "";
    }
    if(typeof paralela45_circuit_search_data.filters.period === 'undefined'){
      paralela45_circuit_search_data.filters.period = [];
    }
    if($.isEmptyObject(primary_filters.period)){
      $('#circuit-services-search-filter-period').hide();
    } else {
      var any_filter_added = false;
      for(var per in primary_filters.period){
        per = parseInt(per);
        var number_of_filtered_offers = typeof filters.period[per] !== 'undefined' ? filters.period[per] : 0;
        var all_have_it = primary_filters.period[per] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.period.indexOf(per)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="period" id="hotel_period_' + per + '" value="' + per + '"/>').prop('checked', is_checked).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_period_' + per + '" />').appendTo($checkWrapper);
        $checkLabel.append(per + ' zile');
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.period[per]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.period[per] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.period[per] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-period').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-period').show();
      }
    }
    
    // BEGIN - cheapest
    $('#circuit-services-search-filter-cheapest').empty();
    var number_of_filtered_offers = filters.cheapest;
    var all_have_it = primary_filters.cheapest == paralela45_circuit_results.offers.length;
    var all_filtered_have_it = number_of_filtered_offers == offers.length;
    var is_checked = paralela45_circuit_search_data.filters.cheapest ? true : false;
    var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
    var $checkWrapper = $('<div class="checkWrapper" />');
    $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="cheapest" id="hotel_cheapest" value="1"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
    var $checkLabel = $('<label for="hotel_cheapest" />').appendTo($checkWrapper);
    $checkLabel.append('Cele mai avantajoase oferte');
    if(!secondary){
      $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
    } else {
      if(number_of_filtered_offers != primary_filters.cheapest){
        $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.cheapest + '</small></span>');
      } else {
        $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.cheapest + (is_checked ? '</strong>' : '</small>') + '</span>');
      }
    }
    any_filter_added = true;
    $('#circuit-services-search-filter-cheapest').append($checkWrapper);
    $('.circuit-cheapest-filter').show();
    // END - cheapest
    if(typeof paralela45_circuit_search_data.filters.cities === 'undefined'){
      paralela45_circuit_search_data.filters.cities = [];
    }
    $('#circuit-services-search-filter-cities').empty();
    if($.isEmptyObject(primary_filters.cities)){
      // $('#circuit-services-search-filter-cities').hide();
    } else {
      var any_filter_added = false;
      for(var city_code in primary_filters.cities){
        var number_of_filtered_offers = typeof filters.cities[city_code] !== 'undefined' ? filters.cities[city_code] : 0;
        var all_have_it = primary_filters.cities[city_code] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.cities.indexOf(city_code)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="cities" id="hotel_city_code_' + city_code + '" value="' + city_code + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_city_code_' + city_code + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof all_cities_names[city_code] !== 'undefined' ? all_cities_names[city_code] : '');
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.cities[city_code]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.cities[city_code] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.cities[city_code] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-cities').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-cities').show();
      }
    }
    if(typeof paralela45_circuit_search_data.filters.origin_cities === 'undefined'){
      paralela45_circuit_search_data.filters.origin_cities = [];
    }
    $('#circuit-services-search-filter-origin_cities').empty();
    if($.isEmptyObject(primary_filters.origin_cities)){
      // $('#circuit-services-search-filter-origin_cities').hide();
    } else {
      var any_filter_added = false;
      for(var city_code in primary_filters.origin_cities){
        var number_of_filtered_offers = typeof filters.origin_cities[city_code] !== 'undefined' ? filters.origin_cities[city_code] : 0;
        var all_have_it = primary_filters.origin_cities[city_code] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.origin_cities.indexOf(city_code)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="origin_cities" id="hotel_origin_city_code_' + city_code + '" value="' + city_code + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_origin_city_code_' + city_code + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof all_cities_names[city_code] !== 'undefined' ? all_cities_names[city_code] : '');
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.origin_cities[city_code]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.origin_cities[city_code] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.origin_cities[city_code] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-origin_cities').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-origin_cities').show();
      }
    }
    $('#circuit-services-search-filter-dates').empty();
    if(typeof paralela45_circuit_search_data.filters.dates === 'undefined'){
      paralela45_circuit_search_data.filters.dates = [];
    }
    if($.isEmptyObject(primary_filters.dates)){
      // $('#circuit-services-search-filter-dates').hide();
    } else {
      var any_filter_added = false;
      for(var date in primary_filters.dates){
        var number_of_filtered_offers = filters.dates[date];
        var number_of_filtered_offers = typeof filters.dates[date] !== 'undefined' ? filters.dates[date] : 0;
        var all_have_it = primary_filters.dates[date] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.dates.indexOf(date)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="dates" id="hotel_date_' + date + '" value="' + date + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_date_' + date + '" />').appendTo($checkWrapper);
        $checkLabel.append(moment(date,'Y-MM').locale('ro').format('MMMM Y'));
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.dates[date]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.dates[date] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.dates[date] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-dates').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-dates').show();
      }
    }
    $('#circuit-services-search-filter-availabilities').empty();
    if(typeof paralela45_circuit_search_data.filters.availabilities === 'undefined'){
      paralela45_circuit_search_data.filters.availabilities = [];
    }
    if($.isEmptyObject(primary_filters.availabilities)){
      // $('#circuit-services-search-filter-availabilities').hide();
    } else {
      var any_filter_added = false;
      for(var availability in primary_filters.availabilities){
        var number_of_filtered_offers = filters.availabilities[availability];
        var number_of_filtered_offers = typeof filters.availabilities[availability] !== 'undefined' ? filters.availabilities[availability] : 0;
        var all_have_it = primary_filters.availabilities[availability] == paralela45_circuit_results.offers.length;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_checked = paralela45_circuit_search_data.filters.availabilities.indexOf(availability)>-1;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="availabilities" id="hotel_availability_' + availability + '" value="' + availability + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_availability_' + availability + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_availabilities[availability] !== 'undefined' ? paralela45_availabilities[availability] : availability);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.availabilities[availability]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked ? '</strong>' : '</small>') + '/<small>' + primary_filters.availabilities[availability] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked ? '<strong>' : '<small>') + primary_filters.availabilities[availability] + (is_checked ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-availabilities').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-availabilities').show();
      }
    }
    $('#circuit-services-search-filter-room_types').empty();
    /* if(typeof paralela45_circuit_search_data.filters.room_types === 'undefined'){
      paralela45_circuit_search_data.filters.room_types = [];
    }
    if($.isEmptyObject(primary_filters.room_types)){
      // $('#circuit-services-search-filter-room_types').hide();
    } else {
      var any_filter_added = false;
      for(var room_type in primary_filters.room_types){
        var number_of_filtered_offers = filters.room_types[room_type];
        var number_of_filtered_offers = typeof filters.room_types[room_type] !== 'undefined' ? filters.room_types[room_type] : 0;
        var all_have_it = primary_filters.room_types[room_type] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.room_types.indexOf(room_type)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="room_types" id="hotel_room_type_' + room_type + '" value="' + room_type + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_room_type_' + room_type + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_room_types[room_type] !== 'undefined' ? paralela45_room_types[room_type] : room_type);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.room_types[room_type]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked && !all_have_it ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked && !all_have_it ? '</strong>' : '</small>') + '/<small>' + primary_filters.room_types[room_type] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked && !all_have_it ? '<strong>' : '<small>') + primary_filters.room_types[room_type] + (is_checked && !all_have_it ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-room_types').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-room_types').show();
      }
    } */
    $('#circuit-services-search-filter-services').empty();
    if(typeof paralela45_circuit_search_data.filters.service_types === 'undefined'){
      paralela45_circuit_search_data.filters.service_types = [];
    }
    if($.isEmptyObject(primary_filters.service_types)){
      // $('#circuit-services-search-filter-services').hide();
    } else {
      var any_filter_added = false;
      for(var service_type in primary_filters.service_types){
        var number_of_filtered_offers = filters.service_types[service_type];
        var number_of_filtered_offers = typeof filters.service_types[service_type] !== 'undefined' ? filters.service_types[service_type] : 0;
        var all_have_it = primary_filters.service_types[service_type] == paralela45_circuit_results.offers.length;
        var is_checked = paralela45_circuit_search_data.filters.service_types.indexOf(service_type)>-1;
        var all_filtered_have_it = number_of_filtered_offers == offers.length;
        var is_disabled = all_have_it || (!is_checked && all_filtered_have_it);
        var $checkWrapper = $('<div class="checkWrapper" />');
        $('<input type="checkbox"'+(is_disabled ? ' disabled' : '')+' class="'+(all_have_it ? ' all_have_it' : 'some_have_it')+'" name="service_types" id="hotel_service_type_' + service_type + '" value="' + service_type + '"/>').prop('checked', is_checked || all_have_it).appendTo($checkWrapper);
        var $checkLabel = $('<label for="hotel_service_type_' + service_type + '" />').appendTo($checkWrapper);
        $checkLabel.append(typeof paralela45_service_types[service_type] !== 'undefined' ? paralela45_service_types[service_type] : service_type);
        if(!secondary){
          $checkLabel.append('<span><small>' + number_of_filtered_offers + '</small></span>');
        } else {
          if(number_of_filtered_offers != primary_filters.service_types[service_type]){
            $checkLabel.append('<span class="filter_results_number_overlap">' + (is_checked && !all_have_it ? '<strong>' : '<small>') + number_of_filtered_offers + (is_checked && !all_have_it ? '</strong>' : '</small>') + '/<small>' + primary_filters.service_types[service_type] + '</small></span>');
          } else {
            $checkLabel.append('<span>' + (is_checked && !all_have_it ? '<strong>' : '<small>') + primary_filters.service_types[service_type] + (is_checked && !all_have_it ? '</strong>' : '</small>') + '</span>');
          }
        }
        any_filter_added = true;
        $('#circuit-services-search-filter-services').append($checkWrapper);
      }
      if(any_filter_added){
        $('#circuit-services-search-filter-services').show();
      }
    }
    if(!secondary){
      filters.prices.sort(SortPrices);
      $price_slider.slider({
        range: true,
        min: 0,
        max: filters.prices.length-1,
        values: [0, filters.prices.length-1],
        slide: function (event, ui) {
          $(this).trigger('updatePrice',ui);
        },
        stop: function (event, ui) {
          setFilters();
          filterResults();
        }
      }).on('updatePrice', function(e, ui){
        if(ui){
          var slider_values = ui.values;
        } else {
          var slider_values = $price_slider.slider('values');
        }
        $("#circuit-services-search-filter-price-slider-amount").val(format_price(Math.ceil(filters.prices[slider_values[ 0 ]]), filters.currency) + " - " + format_price(Math.ceil(filters.prices[slider_values[ 1 ]]), filters.currency));
      });
      $price_slider.trigger('updatePrice');
    }
  }
  var paralela45_circuit_search_data = <?php echo json_encode($this->paralela45_circuit_search_data); ?>, paralela45_circuit_results;
  
  function loadRoomPackages($hotel_result,data){
    console.log('loadRoomPackages', data);
    if(!search_is_over){
      console.log('loadRoomPackages','Search is not over, aborting');
      return false;
    }
    var request_data = {
      offer : {
        package_variant_id: data.packageVariantId,
        package_id: data.packageId,
        package_search_id: data.packageSearchId,
        occupancy: paralela45_circuit_search_data.occupancy
      }
    };
    var currency = data.currency;
    $('#result_serviceCircuitFormFellowsForm').empty();
    $room_packages_loading.show();
    var $room_pkg = $room_packages;
    
    $room_pkg.empty();
    setSearchStatus(false);
    request_data = $.extend(true, paralela45_circuit_search_data,request_data);
    $('#service_circuit_reserve_submit').prop('disabled',false);
    $.ajax({
      url: '<?php echo site_url('paralela45/circuit/booking'); ?>',
      method: 'post',
      dataType: 'json',
      data: request_data,
    }).done(function(resp, textStatus, jqXHR){
      console.log('loadRoomPackages',resp);
      setSearchStatus(true);
      $error_container.empty();
      if(!resp.status || resp.status !== 'success'){
        showMessage($error_container,'Eroare in preluarea vacantelor', 'danger');
        return;
      }
      var data = resp.data;
      $room_packages_loading.hide();
      var cancellation_policies = data.cancellation_policies;
      if(cancellation_policies){
        $('.card-block',$hotel_result).append('<div class="mt-1 mb-1">\
          <strong>Conditii anulare:</strong> <ul class="cancellation-policies pl-3"></ul>\
        </div>');
        var $cancellation_policies_ul = $('ul.cancellation-policies',$hotel_result);
        for(var i=0; i<cancellation_policies.length; i++){
          var cancellation_policy = cancellation_policies[i];
          var $li = $('<li />');
          var penalty_start_date = moment(cancellation_policy.from_date,'Y-MM-DD');
          if(cancellation_policy.percentage){
            var price_formatted = format_price(cancellation_policy.price, '%');
          } else {
            var price_formatted = format_price(cancellation_policy.price, currency);
          }
          var text = "Anularea dupa data " + penalty_start_date.locale('ro').format("DD/MM/Y (dddd, D MMMM)") + " presupune o penalizare de " + price_formatted;
          $li.text(text);
          $li.appendTo($cancellation_policies_ul);
        }
      }
      var extra_services = data.extra_services;
      if(extra_services){
        $('.card-block',$hotel_result).append('<div class="mt-1 mb-1">\
          <strong>Servicii extra:</strong> <div class="extra_services pl-3"></div>\
        </div>');
        var $extra_services_ul = $('div.extra_services',$hotel_result);
        for(var extra_service_key in extra_services){
          if (!extra_services.hasOwnProperty(extra_service_key)) {
            continue;
          }
          var extra_service = extra_services[extra_service_key];
          var $li = $filter_checkbox_model.clone();
          var penalty_start_date = moment(extra_service.from_date,'Y-MM-DD');
          var service_value = extra_service.Type + '-' + extra_service.Code + '-' + extra_service.CharterId;
          var service_price = 0;
          if(extra_service.price){
            var service_price = extra_service.price.Gross;
          }
          $('input.filter-option-input', $li).attr({
            'name':'extra_services[]',
            'price':service_price
          }).val(service_value);
          
          var text = extra_service.Name + ' ' + format_price(service_price,currency);
          $('.filter-option-description',$li).text(text);
          $li.appendTo($extra_services_ul);
        }
      }
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('loadRoomPackages',jqXHR, textStatus, errorThrown);
      $error_container.empty();
      $room_packages_loading.hide();
      showMessage($error_container,'Eroare in preluarea vacantelor', 'danger');
      setSearchStatus(true);
    });
  }
  
  function interpretResults(){
    $fellow_info_wrapper.show();
    loadRoomOccupancyDetails();
    $('.circuit-sort-by', $service_circuit_tab).prop('disabled', false);
    $error_container.empty();
    
    if(typeof paralela45_circuit_results.filtered_offers === 'undefined'){
      paralela45_circuit_results.filtered_offers = paralela45_circuit_results.offers;
    }
    console.log(paralela45_circuit_results);
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    var total_pages = Math.ceil(paralela45_circuit_results.filtered_offers.length / results_per_page);
    if(total_pages){
      $navigation.twbsPagination({
        startPage: results_page,
        totalPages: total_pages,
        visiblePages: 20,
        first: "<<",
        prev: "<",
        next: ">",
        last: ">>",
        onPageClick: function (evt, page) {
          if(page == results_page){
            return;
          }
          results_page = page;
          interpretResults();
        }
      });
    }
    $('#serviceCircuitResults').empty();
    
    var $package_box_model = $('#circuit-result-model').clone().removeAttr('id style');
    
    var offset = (results_page-1) * results_per_page;
    paralela45_circuit_results.filtered_offers.sort(SortResults);
    for (var i=offset; i<paralela45_circuit_results.filtered_offers.length; i++){
      if(i == offset + results_per_page){
        break;
      }
      var offer = paralela45_circuit_results.filtered_offers[i];
      var product = paralela45_circuit_results.products[offer.CircuitId];
      
      var $package_box = $package_box_model.clone();
      $('input[name=offer_id]',$package_box).val(product.CircuitId);
      $('input[name=package_id]',$package_box).val(product.SearchId);
      $('input[name=package_variant_id]',$package_box).val(offer.DepartureCharter);
      
      $('.circuit-image', $package_box).attr('href', product.Link)
        .css('background-image',  'url(<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>)')
        .addClass('lazy')
        .attr('data-src', product.Image);
      $('.circuit-name', $package_box).text(product.Name);
      $('.circuit-link', $package_box).attr('href',product.Link);
      $('.reserve-button', $package_box).attr('href',offer.Link);
      // var stars = parseInt(product.Stars);
      // if(stars){
        // $('.circuit-stars', $package_box).html(" " + Array(parseInt(stars) + 1).join('<i class="fa fa-star"></i>'));
      // }
      // $('.package-category', $package_box).text(product.Class);
      // $('.circuit-info-description', $package_box).html(offer.Description);
      $('.circuit-origin', $package_box).html(offer.InfoCharter.DepArrLoc + ' ' + moment(offer.InfoCharter.DepArrDate,'Y-MM-DD HH:mm:ss').locale('ro').format("DD/MM/Y (dddd, D MMMM)") + '');
      $('.circuit-destination', $package_box).html(offer.InfoCharter.RetArrLoc + ' ' + moment(offer.InfoCharter.RetArrDate,'Y-MM-DD HH:mm:ss').locale('ro').format("DD/MM/Y (dddd, D MMMM)") + '');
      $('.circuit-days', $package_box).html(product.Period);
      for(var j=0; j<offer.Services.length; j++){
        var service = offer.Services[j];
        var $li = $('<li />');
        $li.append($('<em>' + service.Name + '</em>').attr('title',service.Provider));
        if(service.Availability !== 'IM'){
          $li.append(' <span>(' + paralela45_availabilities[service.Availability] + ')</span> ');
        }
        $('.package-services', $package_box).append($li);
      }
      
      /* for(var j=0; j<offer.Rooms.length; j++){
        var room = offer.Rooms[j];
        var $li = $('<li />');
        // $li.append('<strong title="' + room.Code + '">' + room.GCode + '</strong> ');
        if(room.ExtraBed){
          $li.append('<span>Pat extra</span> ');
        }
        // $li.append($('<em>' + room.Quantity + '</em> x '));
        $li.append($('<em>' + room.Name + '</em>').attr('title',room.Provider));
        $('.package-rooms', $package_box).append($li);
      } */
      $('.package-availability', $package_box).html(paralela45_availabilities[offer.Availability]);
      $('.circuit-name', $package_box).attr('href', product.Link);
      $('.reserve-button', $package_box).attr('href', product.Link);
      if(offer.block_payments){
        $('.room-options-toggle', $package_box).addClass('disabled').attr('title','Nu se mai pot efectua rezervari pentru aceasta oferta.');
      } else {
        $('.room-options-toggle', $package_box).attr({
          'data-package-variant-id': offer.DepartureCharter,
          'data-package-search-id': product.SearchId,
          'data-package-id': product.CircuitId,
          'data-currency': offer.Currency
        }).on('click', function(e){
          var $hotel_result = $(this).closest('.circuit-result').clone();
          $('.action-buttons',$hotel_result).remove();
          
          $hotel_details.empty();
          $hotel_result.appendTo($hotel_details);
          $hotel_details.show();
          
          loadRoomPackages($hotel_result,$(this).data());
          return false;
        });
      }
      if(product.Destinations && product.Destinations.length){
        var city_names = [];
        for (var j=0; j<product.Destinations.length; j++){
          if(!product.Destinations[j].CityName){
            continue;
          }
          city_names.push(product.Destinations[j].CityName);
        }
        $('.circuit-location', $package_box).html(city_names.join(', '));
      }
      if(offer.Price){
        $('.current-price', $package_box).text(format_price(Math.ceil(offer.Price), offer.Currency));
      } else {
        $('.current-price', $package_box).remove();
      }
      $('#serviceCircuitResults').append($package_box);
    }
    $('#serviceCircuitResults .lazy').lazy();
  }
  function loadResults(initial){
    $.ajax({
      url: '<?php echo site_url('paralela45/circuit/loadResults'); ?>',
      method: 'post',
      dataType: 'json',
      data: paralela45_circuit_search_data,
      success: function(result,status,xhr){
        if(!result.status || result.status !== 'success'){
          interpretNoHotelsResponse(result,initial);
          return;
        }
        paralela45_circuit_search_data = result.data;
        paralela45_circuit_results = result.results;
        if(initial){
          if(typeof paralela45_circuit_search_data.filters === 'undefined'){
            paralela45_circuit_search_data.filters = {};
          }
          paralela45_circuit_search_data.filters.cheapest = false;
          paralela45_circuit_search_data.filters.service_types = [];
          paralela45_circuit_search_data.filters.room_types = [];
          paralela45_circuit_search_data.filters.dates = [];
          paralela45_circuit_search_data.filters.availabilities = [];
          paralela45_circuit_search_data.filters.cities = [];
          paralela45_circuit_search_data.filters.origin_cities = [];
          paralela45_circuit_search_data.filters.name = "";
          paralela45_circuit_search_data.filters.period = [];
          
          paralela45_circuit_search_data.filters.cheapest = true;
          if(paralela45_circuit_search_data.destination){
            paralela45_circuit_search_data.filters.cities.push(paralela45_circuit_search_data.destination);
          }
          if(paralela45_circuit_search_data.origin){
            paralela45_circuit_search_data.filters.origin_cities.push(paralela45_circuit_search_data.origin);
          }
          if(paralela45_circuit_search_data.start_date){
            paralela45_circuit_search_data.filters.dates.push(paralela45_circuit_search_data.start_date);
          }
          if(paralela45_circuit_search_data.nights){
            paralela45_circuit_search_data.filters.period.push(parseInt(paralela45_circuit_search_data.nights));
          }
        }
        interpretResults();
        if(initial){
          loadFilters();
        }
        setSearchStatus(true);
      },
      error: function(jqXHR,textStatus,error){
        console.log(jqXHR,textStatus,error);
        setSearchStatus(true);
      }
    });
  }
  function setHotelSearchAndInitiate(){
    if(!search_is_over){
      console.log('setHotelSearchAndInitiate','Search is not over, aborting');
      return false;
    }
    clearFilters();
    $('.circuit-sort-by', $service_circuit_tab).prop('disabled', true);
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    $('#result_serviceCircuitFormFellowsForm').empty();
    $('#serviceCircuitResults').empty();
    $('#service_circuit_reserve_submit').prop('disabled',true);
    $('#service_circuit_search_sort_price').val('1');
    $('#service_circuit_search_sort_stars').val('0');
    setSearchStatus(false);
    $error_container.empty();
    $fellow_info_wrapper.hide();
    $hotel_details.empty();
    
    showMessage($error_container,'Se cauta hoteluri <i class="fa fa-spinner fa-spin"></i>','warning');
    $.ajax({
      url: '<?php echo site_url('paralela45/circuit/setSearch'); ?>',
      method: 'post',
      dataType: 'json',
      data: paralela45_circuit_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('setHotelSearchAndInitiate',resp);
      $error_container.empty();
      if(!resp.status || resp.status !== 'success'){
        interpretNoHotelsResponse(resp);
        return;
      }
      paralela45_circuit_search_data = resp.data;
      loadResults(true);
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('setHotelSearchAndInitiate',jqXHR, textStatus, errorThrown);
      var resp = {status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR};
      interpretNoHotelsResponse(resp);
    });
  }
  function serviceCircuitFormSubmitCallback($form,resp,$err_container){
    if(resp.status !== 'success'){
      return true;
    }
    paralela45_circuit_search_data = resp.data;
    console.log('serviceCircuitFormSubmitCallback',paralela45_circuit_search_data);
    setHotelSearchAndInitiate();
  }
  $('#serviceCircuitForm').on('submit',function(){
    if(!search_is_over){
      console.log('serviceCircuitForm', 'submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,serviceCircuitFormSubmitCallback);
  });
  
  function serviceCircuitFormFellowsFormCallback($form,resp,$err_container){
    console.log('serviceCircuitFormFellowsFormCallback',resp);
    if(resp.status !== 'success'){
      return true;
    }
    <?php if(!$order->id){ ?>
    $form[0].submit();
    <?php } else { ?>
    loadOrderServices();
    <?php } ?>
    return true;
  }
  $('#serviceCircuitFormFellowsForm').on('submit',function(){
    if(!search_is_over){
      console.log('serviceCircuitFormFellowsForm','submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,serviceCircuitFormFellowsFormCallback);
  });
  // if(paralela45_circuit_search_data.index_id.length>0){
  loadResults(true);
  // }
  initting_data = false;
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  