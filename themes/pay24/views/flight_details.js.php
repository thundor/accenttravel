let search_axios, search_axios_timer, search_axios_cancel;
const CancelToken = axios.CancelToken;

export default {
  emits: ['edit', 'selected_ancillery', 'flight_data'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      company_images: {
          type: Object,
          default: () => ({}),
      },
      filters: {
          type: Object,
          default: () => (undefined),
      },
  },
  components : {
		// 'FlightsSearchResultsItem' : loadViewAsync('flights_search_results_item'),
		'FlightUpsell' : loadViewAsync('flight_upsell'),
	},
	data: () => ({
    loading: {
      ancillery: {},
    },
    fare_rules_show: {},
    ancillery_requests: {},
    store_results: false,
    search_loading: false,
    selected_upsell: '',
    ancillery: undefined,
    flight_data: undefined,
    fare_rules: undefined,
    upsells: undefined,
    ancilleries: {},
  }),
	template : `
<div class="flight_item v-theme--dark">
  
  <FlightsLoader v-if="search_loading" ></FlightsLoader>
  <template v-if="upsells && upsells.upsell && upsells.upsell.length">
    <FlightUpsell v-model="selected_upsell" :upsells="upsells" :flight_data="ancilleryOrFlight" :base_flight_data="flight_data" :loading="loading.ancillery"></FlightUpsell>
  </template>
  <v-list v-if="ancilleryOrFlight" class="w-100">
    <v-list-item v-for="(route, routeIndex) in ancilleryOrFlight.Routes||[]" class="bg-background rounded-theme mb-4 text-left">
      <div class="d-flex w-100 mt-2 mb-4">
        <v-icon :icon="!routeIndex ? 'mdi-airplane-takeoff' : 'mdi-airplane-landing'" class="mr-4"></v-icon>
        <div class="flex-grow-1">
          <v-list-item-title>
          <div v-if="route.Segment[0].Origin && route.Segment[route.Segment.length-1].Destination" class="text-wrap">
            <strong v-html="route.Segment[0].Origin.Airport.City + ' - ' + route.Segment[route.Segment.length-1].Destination.Airport.City"></strong>
            <small v-if="route.direct" class="pl-2" v-html="'(Direct)'"></small>
          </div>
          </v-list-item-title>
          <v-list-item-subtitle>
          <div class="d-flex justify-space-between flex-wrap w-100">
            <strong class="text-capitalize" v-html="route.Segment.length == 1 ? 'Direct' : (route.Segment.length == 2 ? '1 Escala' : (route.Segment.length -1) + ' Escale')"></strong>
            <strong class="text-capitalize" v-html="durationToFormatted(route.Duration)"></strong>
          </div>
          </v-list-item-subtitle>
        </div>
      </div>
	  <hr class="my-4" style="border-color: transparent;
    margin: 0 -15px;
    border-bottom-width: 0;
    height: 1px !important;
    box-shadow: 0px 0px 2px rgb(var(--v-theme-surface)) inset;
    border-left-width: 0;
    border-right-width: 0;"/>
      <template v-for="segment in route.Segment">
        <div v-if="segment.Flight.StopTime" class="v-theme--dark bg-surface rounded-theme mb-4 py-2 text-center d-flex justify-space-between px-4">
          {{ durationToFormatted(segment.Flight.StopTime) }} <v-icon icon="mdi-clock"></v-icon>
        </div>
        <div class="d-flex justify-start align-center mb-4" style="gap:15px;">
          <img v-if="company_images[segment.Carrier.Marketing.Code]" :src="company_images[segment.Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 40px;background: #fff;padding: 2px; flex:0;" />
          <div class="d-flex flex-column" style="flex:50%;">
            <strong v-text="segment.Carrier.Marketing._"></strong>
            <small v-if="segment.Flight && segment.Flight.CabinType" class="">{{ segment.Flight.CabinType }}</small>
          </div>
          <div class="d-flex flex-column ml-auto" style="flex:50%;">
            <strong v-if="segment.Aircraft" v-text="segment.Aircraft._"></strong>
            <div v-if="segment.Origin.Terminal || getObjectDotPathValue(segment,'Secured',false)">
              <span v-if="segment.Origin.Terminal" class="">Terminal: {{ segment.Origin.Terminal }}</span>
              <span v-if="getObjectDotPathValue(segment,'Secured',false)" class=""><v-icon icon="mdi-shield"></v-icon></span>
            </div>
          </div>
        </div>
        <?php /*
        <div class="d-flex justify-start align-center mb-2" style="gap:15px;">
          <strong>Operator</strong>
          <img v-if="company_images[segment.Carrier.Operating.Code]" :src="company_images[segment.Carrier.Operating.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
          <strong v-text="segment.Carrier.Operating._"></strong>
        </div>
        */ ?>
        
        <div class="d-flex flex-column line-before mb-4">
          <div class="d-flex flex-column mb-4">
            <span class="">{{ segment.Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }} - {{ dateIntervalFormatted(segment.Origin.Date) }}</span>
            <span class="color-dark-light">{{ segment.Origin.Airport.Code }}, {{ segment.Origin.Airport._ }}, {{ segment.Origin.Airport.CityCode }}, {{ segment.Origin.Airport.City }}</span>
          </div>
          <div class="d-flex flex-column">
            <span class="">{{ segment.Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }} - {{ dateIntervalFormatted(segment.Destination.Date) }}</span>
            <span class="color-dark-light">{{ segment.Destination.Airport.Code }}, {{ segment.Destination.Airport._ }}, {{ segment.Destination.Airport.City }}</span>
          </div>
        </div>
      </template>
	  
		<small class="d-flex flex-column" v-if="ancilleryOrFlight.FareDetails && ancilleryOrFlight.FareDetails.BrandedFare && ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails && ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex]">
			<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Cabin"><b>Clasa:</b> {{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Cabin }}</span>
			<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Code"><b>Fare Family:</b> {{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Code }}</span>
			<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Description">{{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Description }}</span>
		</small>
    </v-list-item>
  </v-list>
  <template v-else-if="!search_loading">
    <div class="bg-background pa-4 rounded-theme mb-4">
		<h6 class="text-left mb-4" style="
            font-weight: normal;
        ">Zborul nu a putut fi incarcat. Va rugam sa alegeti alt zbor sau reincepeti cautarea</h6>
	</div>
  </template>
  <template v-if="fare_rules && fare_rules.basises_arr">
    <div class="bg-background pa-4 rounded-theme mb-4">
    <h6 class="text-left mb-4" style="
            font-weight: normal;
        ">Reguli si informatii zbor</h6>
    <div v-if="fare_rules.general_categories" class="text-left">
      <h6 class="open-fare-detail" @click="fare_rules_show['general'] = !fare_rules_show['general']">General valabile</h6>
      <div v-show="fare_rules_show['general']" class="bg-background rounded-theme py-2">
      <div v-for="(category, title) in fare_rules.general_categories">
        <div class="open-fare-detail" v-text="capitalizeWords(title, true)" @click="fare_rules_show[('general-' + '-' + title)] = !fare_rules_show[('general-' + '-' + title)]"></div>
        <div style="white-space: pre;overflow-x:auto;font-size:9px;" touchless class="pl-0" v-show="fare_rules_show[('general-' + '-' + title)]">
        <template v-if="category.Url">
          <a :href="category.Url" target="_BLANK" @click="ob" v-text="fixCategory(category._)"></a>
        </template>
        <template v-else>
          <span v-text="fixCategory(category._)"></span>
        </template>
        </div>
      </div>
      </div>
    </div>
    <div v-if="fare_rules.basises_arr" class="text-left">
      <template v-for="basis in fare_rules.basises_arr">
		<div v-if="fare_rules.particular_per_basis[basis] || fare_rules.general_per_basis[basis]">
        <h6 class="open-fare-detail" v-text="basisDescription(basis)" @click="fare_rules_show[('basis-' + basis)] = !fare_rules_show[('basis-' + basis)]"></h6>
        <div v-show="fare_rules_show[('basis-' + basis)]" class="bg-background rounded-theme py-2">
        <template v-if="fare_rules.general_per_basis[basis]">
        <div v-for="(category, title) in fare_rules.general_per_basis[basis]">
          <div class="open-fare-detail" v-text="capitalizeWords(title, true)" @click="fare_rules_show[('basis-' + basis + '-' + title)] = !fare_rules_show[('basis-' + basis + '-' + title)]"></div>
          <div style="white-space: pre;overflow-x:auto;font-size:9px;" touchless class="pl-0" v-show="fare_rules_show[('basis-' + basis + '-' + title)]">
          <template v-if="category.Url">
            <a :href="category.Url" target="_BLANK" @click="ob" v-text="fixCategory(category._)"></a>
          </template>
          <template v-else>
            <span v-text="fixCategory(category._)"></span>
          </template>
          </div>
        </div>
        </template>
        <template v-if="fare_rules.particular_per_basis[basis]">
        <div v-for="(categories, ptc) in fare_rules.particular_per_basis[basis]">
          <strong class="open-fare-detail" v-text="general_translate_ptc[ptc] && general_translate_ptc[ptc][0] || ptc" @click="fare_rules_show[('basis-' + ptc + '-' + basis)] = !fare_rules_show[('basis-' + ptc + '-' + basis)]"></strong>
          <div v-show="fare_rules_show[('basis-' + basis)]" class="bg-background rounded-theme py-2">
          <div v-for="(category, title) in categories">
            <div class="open-fare-detail" v-text="capitalizeWords(title, true)" @click="fare_rules_show[('basis-' + ptc + '-' + basis + '-' + '-' + title)] = !fare_rules_show[('basis-' + ptc + '-' + basis + '-' + '-' + title)]"></div>
            <div style="white-space: pre;overflow-x:auto;font-size:9px;" touchless class="pl-0" v-show="fare_rules_show[('basis-' + ptc + '-' + basis + '-' + '-' + title)]">
            <template v-if="category.Url">
              <a :href="category.Url" target="_BLANK" @click="ob" v-text="category._"></a>
            </template>
            <template v-else>
              <span v-text="fixCategory(category._)"></span>
            </template>
            </div>
          </div>
          </div>
        </div>
        </template>
        
        </div>
        </div>
      </template>
    </div>
    </div>
  </template>
</div>
	`,
  methods: {
    basisDescription(b){
		if(!this.fare_rules || !this.fare_rules.basis_description || !this.fare_rules.basis_description[b]) return 'Regula ' + b;
		var basis = this.fare_rules.basis_description[b];
		var keys = Object.keys(basis);
		
		return Object.keys(basis).reduce((a, k) => {
			var r = basis[k];
			a.push('' 
				+ (r.AirportCode && (r.AirportName && r.AirportName != r.AirportCode ? r.AirportName + ' (' + r.AirportCode + ')' : r.AirportCode))
				+ (r.OriginCode && (' ' + (r.OriginCityName && r.OriginCityName != r.OriginCode ? r.OriginCode + ' (' + r.OriginCode + ')' : r.OriginCode)) || '')
				+ (r.DestinationCode && r.DestinationCode != r.OriginCode && (' - ' + (r.DestinationCityName && r.DestinationCityName != r.DestinationCode ? r.DestinationCode + ' (' + r.DestinationCode + ')' : r.DestinationCode)) || '')
			);
			return a;
		}, []).join(', ') + ' (' + b + ')'
	},
    fixCategory(r){
      r = ('' + (r || '')).trim();
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,1})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,2})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,3})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,4})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,5})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,6})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,7})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,8})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,9})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,10})(\s*\2)+/g, '$2');
	  return r;
    },
    clearValidations(){
      this.errors = [];
    },
    reset(){
    },
    loadUpsells(){
      var url = '<?php echo site_url('trip/flight/upsell?force_ajax=1&pay_24=1'); ?>';
      this.upsells = undefined;
      this.ancilleries = {};
      this.search_loading = true;
      var s_params = {
        code: this.modelValue.SearchCode,
        itinerary_code: this.modelValue.Flight.ItineraryCode,
        combination_index: this.modelValue.CombinationIndex,
      };
      return axios.post(url,new customURLSearchParams({
        <?php if ($this->config->item('csrf_protection')){ ?>
        <?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
        <?php } ?>
          ...s_params
        }).URLSearchParams, {
        headers: {
          'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
        },
        validateStatus: function (status) {return status == 200},
        cancelToken: new CancelToken(function executor(c) {
          search_axios_cancel = c;
        })
      }).then((result) => {
        this.upsells = {
          itinerary_brand_details: getObjectDotPathValue(result,'data.data.itinerary_brand_details', [],[],[]),
          upsell: getObjectDotPathValue(result,'data.data._embedded.upsell', [],[],[]).filter((v) => !!v.Code),
        };
        console.warn('upsells', this.upsells);
      })
    },
    loadDetails(){
      this.flight_data = undefined;
      this.fare_rules_show = {};
      this.upsells = undefined;
      this.ancilleries = {};
      this.search_loading = true;
      var s_params = {
        code: this.modelValue.SearchCode,
        itinerary_code: this.modelValue.Flight.ItineraryCode,
        combination_index: this.modelValue.CombinationIndex,
      };

      var s_params_version = JSON.stringify(s_params);
      var storageItem = this.store_results && getStorage('pay24.flight.flight_data','', {}, {}, {}) || {};

      if(storageItem.version == s_params_version){
        this.flight_data = getObjectDotPathValue(storageItem,'flight_data', {},{},{});
        console.warn('flight_data', this.flight_data);
        if(this.flight_data.UpsellSupport){
          this.upsells = getObjectDotPathValue(storageItem,'upsells', {},{},{});
          console.warn('upsells', this.upsells);
          this.ancilleries = getObjectDotPathValue(storageItem,'ancilleries', {},{},{});

          if(this.ancilleries && Object.keys(this.ancilleries).length){
            Object.keys(this.ancilleries).forEach((k) => {
              this.ancillery_requests[k] = new Promise((resolve) => {
                resolve(this.ancilleries[k]);
              })
            });
            console.warn(this.ancillery_requests);
          }
        }
        this.search_loading = false;
        return;
      }

      var url = '<?php echo site_url('trip/flight/details?force_ajax=1&pay_24=1'); ?>';
      return axios.post(url,new customURLSearchParams({
        <?php if ($this->config->item('csrf_protection')){ ?>
        <?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
        <?php } ?>
          ...s_params
        }).URLSearchParams, {
        headers: {
          'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
        },
        validateStatus: function (status) {return status == 200},
        cancelToken: new CancelToken(function executor(c) {
          search_axios_cancel = c;
        })
      }).then((result) => {
        this.flight_data = getObjectDotPathValue(result,'data.data', {},{},{});
        console.warn('flight_data', this.flight_data);
        if(this.flight_data.UpsellSupport){
          return this.loadUpsells()
        }
      }).finally(() => {
        this.saveStorage();
        this.search_loading = false;
      })
    },
    saveStorage(){
        var s_params = {
          code: this.modelValue.SearchCode,
          itinerary_code: this.modelValue.Flight.ItineraryCode,
          combination_index: this.modelValue.CombinationIndex,
        };

        var s_params_version = JSON.stringify(s_params);

        var to_store = {
            version: s_params_version,
            flight_data: this.flight_data,
            upsells: this.upsells,
            ancilleries: this.ancilleries,
        };
        console.warn('saving flight_data', to_store);
		if(this.store_results){
			saveStorage('pay24.flight.flight_data',to_store);
		}
    },
    edit(){
      // console.warn('emitting', this.modelValue);
      // this.$emit('edit');
      return true;
    },
    computeFareRules(){
      this.fare_rules = undefined;
      var af = this.ancilleryOrFlight;
      if(!af) return;

      var f = {};
      var ptcs = af && Object.keys(af.FareRules) || [];
	  
	var fare_rules = JSON.parse(JSON.stringify(af.FareRules));
	var basis_description = {};
	for (var ptc in fare_rules){
		for (var rule_index in fare_rules[ptc]){
			var fare_rule = fare_rules[ptc][rule_index];
			var fare_basis = getObjectDotPathValue(fare_rule, 'FareBasis') || '';
			var k = [getObjectDotPathValue(fare_rule,'Airline.Code'), getObjectDotPathValue(fare_rule,'Origin.Code'), getObjectDotPathValue(fare_rule,'Origin.Code')]
			basis_description[fare_basis] = basis_description[fare_basis] || {};
			basis_description[fare_basis][k] = basis_description[fare_basis][k] || {};
			basis_description[fare_basis][k]['AirportCode'] = basis_description[fare_basis][k]['AirportCode'] || getObjectDotPathValue(fare_rule,'Airline.Code');
			basis_description[fare_basis][k]['AirportName'] = basis_description[fare_basis][k]['AirportName'] || getObjectDotPathValue(fare_rule,'Airline.Name');
			basis_description[fare_basis][k]['OriginCityName'] = basis_description[fare_basis][k]['OriginCityName'] || getObjectDotPathValue(fare_rule,'Origin.City');
			basis_description[fare_basis][k]['DestinationCityName'] = basis_description[fare_basis][k]['DestinationCityName'] || getObjectDotPathValue(fare_rule,'Destination.City');
			basis_description[fare_basis][k]['OriginCode'] = basis_description[fare_basis][k]['OriginCode'] || getObjectDotPathValue(fare_rule,'Origin.Code');
			basis_description[fare_basis][k]['DestinationCode'] = basis_description[fare_basis][k]['DestinationCode'] || getObjectDotPathValue(fare_rule,'Destination.Code');
			basis_description[fare_basis][k]['OriginCityCode'] = basis_description[fare_basis][k]['OriginCityCode'] || getObjectDotPathValue(fare_rule,'Origin.CityCode');
			basis_description[fare_basis][k]['DestinationCityCode'] = basis_description[fare_basis][k]['DestinationCityCode'] || getObjectDotPathValue(fare_rule,'Destination.CityCode');
			
			var cats_cnt = {};
			// console.warn(fare_rules[ptc]);
			for (var category_index in fare_rules[ptc][rule_index].Category){
				var cat = fare_rules[ptc][rule_index].Category[category_index];
				if(0 == category_index){
					var index, found=1;
					while(found && (-1 != (index = fare_rules[ptc][rule_index].Category.slice(parseInt(category_index) + 1).findIndex((c) => c.Name == cat.Name)))){
						var c = fare_rules[ptc][rule_index].Category[1 + parseInt(index) + parseInt(category_index)];
						found = false;
						if(c.Url === cat.Url && c._ === cat._){
							if(JSON.stringify(fare_rules[ptc][rule_index].Category.slice(parseInt(category_index), parseInt(category_index) + 1 + parseInt(index))) == JSON.stringify(fare_rules[ptc][rule_index].Category.slice(1 + parseInt(index) + parseInt(category_index), parseInt(category_index) + 2 * (1 + parseInt(index))))){
								fare_rules[ptc][rule_index].Category.splice(1 + parseInt(index) + parseInt(category_index), parseInt(category_index) + 2 * (1 + parseInt(index)));
								found = 1;
							}
						}
					}
				}
				if(cats_cnt[cat.Name]){
					cat.Name += " (" + cats_cnt[cat.Name] + ")";
				}
				
				cats_cnt[cat.Name] = (cats_cnt[cat.Name] || 0) + 1;
			}
		}
	}
	  
      var cats = (getObjectDotPathValue(fare_rules,'*.*.Category') || []).flat(1);
      var airline = (getObjectDotPathValue(fare_rules,'*.*.Airline') || []).flat(1);
      var all_categories = [...new Set((getObjectDotPathValue(fare_rules,'*.*.Category.*.Name') || []).flat(1))];
      var category_name_to_general = all_categories.reduce((o, v, y, z, d) => (o[v] = [...new Set(((d = cats.filter((a) => a.Name == v).map(a => a._)) && d.length == airline.length ? d : []))].length==1 , o), {});
	  var basises_arr = [...new Set((getObjectDotPathValue(fare_rules,'*.*.FareBasis') || []).flat(1))];
      if(!basises_arr.length) return;
	  if(basises_arr.length == 1){
		  for(var i in category_name_to_general){
			  category_name_to_general[i] = false;
		  }
	  }
      var general_categories_arr = Object.keys(category_name_to_general).filter((v) => !!category_name_to_general[v])
      var particular_categories_arr = Object.keys(category_name_to_general).filter((v) => !category_name_to_general[v])
      var all_fare_rules_arr = (getObjectDotPathValue(fare_rules,'*') || []).flat(1) || [];
      
	   var general_per_basis = basises_arr.reduce((o, b, d) => ((d = particular_categories_arr.filter((c, e) => ((e = all_fare_rules_arr.filter(r => r.FareBasis == b).reduce((p,r) => p.concat((r.Category || []).filter(c2 => c2.Name == c).map(c2 => c2._)), [])), e.length == ptcs.length && [...new Set(e)].length == 1) )), d.length && (o[b] = d.reduce((o2,c) => ((o2[c] = all_fare_rules_arr.filter(r => r.FareBasis == b).reduce((p,r) => p.concat((r.Category || []).filter(c2 => c2.Name == c)), [])[0]), o2), {})), o), {});
      var general_categories = general_categories_arr.reduce((o, c) => ((o[c] = all_fare_rules_arr.reduce((p,r,y,z) => (z.splice(y+1),p.concat((r.Category || []).filter(c2 => c2.Name == c)[0])), [])[0]), o), {});
      var particular_per_basis = basises_arr.reduce((o, b, d) => {
        var p = ptcs.reduce((pr,pi) => (pr[pi] = {}, pr), {});
        o[b] = p;
        return o;
      },{});
      if(af){
        for(var ptc in fare_rules){
          var ptc_fareRule = fare_rules[ptc];
          for(var fi in ptc_fareRule){
            var ptc_f_fare_rule = ptc_fareRule[fi];
            // console.warn(ptc_f_fare_rule);
            for(var ci in ptc_f_fare_rule.Category){
              var c_categ = ptc_f_fare_rule.Category[ci];
              if(!general_categories[c_categ.Name] && (!general_per_basis[ptc_f_fare_rule.FareBasis] || !general_per_basis[ptc_f_fare_rule.FareBasis][c_categ.Name])){
                particular_per_basis[ptc_f_fare_rule.FareBasis][ptc][c_categ.Name] = c_categ;
              }
            }
          }
		}
		for(var ptc in particular_per_basis){
          if(particular_per_basis[ptc]){
            for(var k in particular_per_basis[ptc]){
              if(!particular_per_basis[ptc][k] || !Object.keys(particular_per_basis[ptc][k]).length){
                delete(particular_per_basis[ptc][k]);
              }
            }
          }
		  if(!particular_per_basis[k] || !Object.keys(particular_per_basis[ptc]).length){
            delete(particular_per_basis[ptc]);
          }
        }
      }



      this.fare_rules = {
        basises_arr: basises_arr,
        general_per_basis: general_per_basis,
        particular_per_basis: particular_per_basis,
        general_categories: !Object.keys(general_categories).length ? undefined : general_categories,
        basis_description: basis_description,
      };
      console.warn('fare_rules', this.fare_rules);
    },
    loadAncillery(ancillery_code) {
      console.warn('should load ancillery', ancillery_code);
				return this.ancillery_requests[[ancillery_code]] || (this.ancillery_requests[[ancillery_code]] = fetch('<?php echo site_url('/trip/flight/ancillery'); ?>?pay24=1&code=' + this.flight_data.FlightsCode + '&itinerary_code=' + this.flight_data.ItineraryCode + '&ancillery_code=' + ancillery_code).then(response => response.json()).then((a) => {
					if (!a.data) {
						throw "Could not load";
					}
          a.data['upsellCode'] = ancillery_code;
					this.ancilleries[ancillery_code] = a.data;
					this.saveStorage();
					return a.data;
				}).catch((e,a,b) => {
					redirectToFlightsWithError('Could not load the ancillery', e, a, b);
				}))
    },
    validate(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(this.texts[v]);
        return !v;
      })
      return !this.errors.length;
    }
  },
  computed: {
    ancilleryOrFlight:{
      get() { 
        return this.ancillery || this.flight_data;
      },
    },
  },
  mounted: function() {
		setTimeout(() => {
			
		},1000)
	},
  watch:{
    'modelValue': {
      handler(newValue, oldValue){
        this.selected_upsell = '';
        console.warn('selected flight', newValue);
        this.loadDetails();
      },
      immediate: true
    },
    'ancilleryOrFlight': {
      handler(newValue, oldValue){
        this.$emit('flight_data', newValue)
        this.computeFareRules();
      },
      immediate: true
    },
    'selected_upsell': {
      handler(newValue, oldValue){
        console.warn('changed_upsell', newValue);
        this.ancillery = undefined;
        this.$emit('flight_data', undefined);
        if(newValue){
          this.loading.ancillery[newValue] = 1;
          this.loadAncillery(newValue).then((d) => {
            if(this.selected_upsell != newValue) return;
            this.ancillery = d;
            console.warn('selected_ancillery', this.ancillery);
            this.$emit('flight_data', this.ancillery)
          }).finally(() => {
            delete(this.loading.ancillery[newValue]);
          });
        } else {
          console.warn('selected_ancillery', this.flight_data);
          this.$emit('flight_data', this.flight_data)
        }
      },
      immediate: true
    },
  }
}
