let search_axios, search_axios_timer, search_axios_cancel;
const CancelToken = axios.CancelToken;

export default {
  emits: ['edit','filters','summary','flights','search_loading','selected_flight'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      company_images: {
          type: Object,
          default: () => ({}),
      },
      applied_filters: {
          type: Object,
          default: () => ({}),
      },
  },
  components : {
		'FlightsSearchResultsItem' : loadViewAsync('flights_search_results_item'),
	},
	data: () => ({
    store_results: false,
    search_loading: false,
    shown_flights: {},
    show_details: undefined,
    selected_flight: undefined,
    dialog: false,
    limit: 10,
    kept: undefined,
    saved: {},
    filters: undefined,
    search_data: undefined,
    flights: undefined,
    filtered_flights: [],
    errors: [],
    texts: Object.freeze({
    //  at_least_one_sen_adt: "Calatoria trebuie sa contina cel putin un adult sau un senior",
    }),
    validations:Object.freeze([
    //  function(){ return this.adt+this.sen < 1 ? 'at_least_one_sen_adt' : null },
    ]),
  }),
	template : `
<div class="search-results-list">
	<FlightsLoader v-if="search_loading" ></FlightsLoader>
  <template v-else-if="filtered_flights.length">
    <v-lazy v-for="chunkIndex in Math.ceil(filtered_flights.length/limit)"
        v-model="shown_flights[(chunkIndex - 1)]"
        :min-height="300"
        transition="dialog-bottom-transition"
      >
      <div class="flights-results-next-page" :page="chunkIndex">
      <FlightsSearchResultsItem :company_images="company_images" v-for="(flight, flightIndex) in filtered_flights.slice((chunkIndex - 1) * limit, (chunkIndex) * limit)" :passenger_count="passenger_count" v-model="flight" :filters="filters" v-on:show_details="(f) => this.show_details = f" v-on:select_flight="(f) => (this.selected_flight = f, $emit('selected_flight', f))"></FlightsSearchResultsItem>
      </div>
    </v-lazy>
    
    <Modal v-model="dialog">
      <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pa-4" rounded="theme">
        <v-list-item-title class="pb-4 px-0 text-h5" v-text="'Detalii zbor'"></v-list-item-title>
      <template v-if="show_details">
        <template v-for="(route, routeIndex) in show_details.Combination||[]">
          <hr v-if="routeIndex" class="my-4"/>
          <template v-for="segment in route.Segment">
            <div v-if="segment.Flight.StopTime" class="v-theme--dark bg-highlight rounded-theme my-4 py-2 text-center">
              <small><strong>{{ durationToFormatted(segment.Flight.StopTime) }}</strong> escala in {{ segment.Origin.Airport._ }} {{ segment.Origin.Airport.City }} ({{ segment.Origin.Airport.Code }})</small>
            </div>
            <div class="d-flex justify-space-between">
              <div class="d-flex flex-row align-center justify-start" style="gap:15px;">
                <img v-if="company_images[segment.Carrier.Marketing.Code]" :src="company_images[segment.Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
                <strong v-text="segment.Carrier.Marketing._"></strong>
              </div>
            </div>
            <div class="d-flex justify-space-between">
              <div class="d-flex flex-column">
                <strong class="">{{ segment.Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</strong>
                <small class="">{{ formatDateDM(segment.Origin.Date) }}</small>
                <small class="">{{ segment.Origin.Airport.CityCode }}, {{ segment.Origin.Airport.City }}</small>
              </div>
              <div class="d-flex flex-column">
                <span class="my-auto duration-line"><strong>{{ durationToFormatted(route.Duration) }}</strong></span>
              </div>
              <div class="d-flex flex-column">
                <strong class="">{{ segment.Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</strong>
                <small class="">{{ formatDateDM(segment.Destination.Date) }}</small>
                <small class="">{{ segment.Destination.Airport.CityCode }}, {{ segment.Destination.Airport.City }}</small>
              </div>
            </div>
          </template>
        </template>
      </template>
      </v-list>
      <template v-slot:footer="{ props }">
        <v-btn class="d-flex text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="d-flex text-none font-weight-normal" style="flex:1" size="x-large" color="primary" rounded="theme" @click="(selected_flight = show_details, $emit('selected_flight', show_details), dialog = false)">Alege</v-btn>
      </template>
    </Modal>
  </template>
  <template v-else>
    <h5 cass="my-4 text-center">Niciun rezultat gasit</h5>
  </template>
</div>
	`,

  methods: {
    clearValidations(){
      this.errors = [];
    },
    reset(){
    },
    edit(){
      // console.warn('emitting', this.modelValue);
      this.$emit('edit');
      return true;
    },
    save(){
      // this.modelValue = Object.assign({}, this.saved);
      this.$emit('save', this.saved);
      return true;
      // emit
    },
    loadResults(){
      var url = '<?php echo site_url('trip/flights/loadResults?force_ajax=1&pay24=1'); ?>';
      this.flights = undefined;
      return axios.post(url,new customURLSearchParams(this.search_data).URLSearchParams, {
        headers: {
          'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
        },
        validateStatus: function (status) {return status == 200},
        cancelToken: new CancelToken(function executor(c) {
          search_axios_cancel = c;
        })
      }).then((result) => {
        this.flights = getObjectDotPathValue(result,'data.response._embedded.flights', [],[],[]);
        // console.warn('flights', this.flights, result);
      }).finally(() => {
        this.flights = undefined === this.flights ? [] : this.flights;
        this.search_loading = false;
      })
    },
    search(){
      console.log('search', this);
      this.search_loading = true;
      this.search_results = [];
      this.selected_location_index = undefined;
      var s = this.modelValue;
      var url = '<?php echo site_url('trip/flights/setSearchAndInitiate?force_ajax=1&pay24=1'); ?>';
      var d;
      this.filters = undefined;
      this.summary = undefined;
      this.search_data = undefined;
      this.passenger_count = this.modelValue && this.modelValue.passengers ? Object.values(this.modelValue.passengers).reduce((a,b) => a+b,0) : 0;

      var s_params = {
        departure_date: (new Date(s.date)).toISOString().split('T')[0],
        return_date: ((d = new Date(s.date), d.setDate(d.getDate() + (s.days||0)), d)).toISOString().split('T')[0],
        // go_only: s.type ==0 && 1 || 0,
        cabine_type: s.cabine && parseInt(s.cabine) > 0 && parseInt(s.cabine) || 1,
        direct_only: s.direct && 1 || 0,
        flex_dates: s.flex && 1 || 0,
        type: s.type && parseInt(s.type) || 0,
        passengers_adult: s.passengers && s.passengers.adt && parseInt(s.passengers.adt) > 0 && parseInt(s.passengers.adt) || 0,
        passengers_senior: s.passengers && s.passengers.sen && parseInt(s.passengers.sen) > 0 && parseInt(s.passengers.sen) || 0,
        passengers_child: s.passengers && s.passengers.chd && parseInt(s.passengers.chd) > 0 && parseInt(s.passengers.chd) || 0,
        passengers_youth: s.passengers && s.passengers.yth && parseInt(s.passengers.yth) > 0 && parseInt(s.passengers.yth) || 0,
        passengers_infant_lap: s.passengers && s.passengers.inf && parseInt(s.passengers.inf) > 0 && parseInt(s.passengers.inf) || 0,
        passengers_infant_seat: s.passengers && s.passengers.ins && parseInt(s.passengers.ins) > 0 && parseInt(s.passengers.ins) || 0,
        origin_city_id: s.origin && s.origin.CityId && parseInt(s.origin.CityId) > 0 && parseInt(s.origin.CityId) || 0,
        origin_city_name: s.origin && s.origin.CityId && parseInt(s.origin.CityId) > 0 && s.origin.CityName || '',
        origin_country_id: s.origin && s.origin.CountryId && parseInt(s.origin.CountryId) > 0 && parseInt(s.origin.CountryId) || 0,
        origin_country_name: s.origin && s.origin.CountryId && parseInt(s.origin.CountryId) > 0 && s.origin.CountryName || '',
        origin_location_id: s.origin && s.origin.LocationId && parseInt(s.origin.LocationId) > 0 && parseInt(s.origin.LocationId) || 0,
        origin_location_name: s.origin && s.origin.LocationId && parseInt(s.origin.LocationId) > 0 && s.origin.LocationName || '',
        destination_country_id: s.origin && s.destination.CountryId && parseInt(s.destination.CountryId) > 0 && parseInt(s.destination.CountryId) || 0,
        destination_country_name: s.origin && s.destination.CountryId && parseInt(s.destination.CountryId) > 0 && s.destination.CountryName || '',
        destination_city_id: s.origin && s.destination.CityId && parseInt(s.destination.CityId) > 0 && parseInt(s.destination.CityId) || 0,
        destination_city_name: s.origin && s.destination.CityId && parseInt(s.destination.CityId) > 0 && s.destination.CityName || '',
        destination_location_id: s.origin && s.destination.LocationId && parseInt(s.destination.LocationId) > 0 && parseInt(s.destination.LocationId) || 0,
        destination_location_name: s.origin && s.destination.LocationId && parseInt(s.destination.LocationId) > 0 && s.destination.LocationName || '',
        lang: 'ro',
        ignore_session: 1,
      }

      var s_params_version = JSON.stringify(s_params);
      
      var storageItem = this.store_results && getStorage('pay24.flight.results','', {}, {}, {}) || {};
      if(s && storageItem.version == s_params_version){
        this.filters = Object.freeze(getObjectDotPathValue(storageItem,'filters', {}));
        this.summary = Object.freeze(getObjectDotPathValue(storageItem,'summary', {}));
        this.search_data = Object.freeze(getObjectDotPathValue(storageItem,'search_data', {}));
        this.flights = Object.freeze(getObjectDotPathValue(storageItem,'flights', {}));
        this.search_loading = false;
        return;
      }

      search_axios = axios.post(url,new customURLSearchParams({
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
        this.filters = Object.freeze(getObjectDotPathValue(result,'data.results', {}));
        this.summary = Object.freeze(getObjectDotPathValue(result,'data.response', {}));
        this.search_data = Object.freeze(getObjectDotPathValue(result,'data.data', {}));
        // console.warn('initiate flights', result);
        return this.loadResults();
      }).catch(err => {alert(err)}).finally(() => {
        this.search_loading = false;
		if(this.store_results){
        saveStorage('pay24.flight.results',{
          version: s_params_version,
          filters: this.filters,
          summary: this.summary,
          search_data: this.search_data,
          flights: this.flights,
        });
		}
      })
    },
    filteredFlights(){
      var flights = this.flights || [];
        if(this.applied_filters.before){
          this.applied_filters.before.forEach((f) => flights = f(flights));
        }
        console.warn(flights);
        return flights.reduce((rs, r) => {
			if(this.applied_filters.flights){
			  if(!this.applied_filters.flights.every((f) => f(r))){
				  return rs;
			  }
			}
		
			r.Combinations.every((c, i) => {
			  if(!c) return true;
			  var ca = c.split('|');
        var ra = {Flight: r, Combination: [], CombinationIndex: i, CombinationCode: c, SearchCode: this.search_data.code};
			  if(!ca.every((v,i) => {
          var g = v.match(/^(\d)/)[1];
          var ri = v.match(/^\d(\d+)/)[1];
          var o = r.Routes[parseInt(g)].Route.find((j) => j.Ref == ri);
          
		  // console.warn('combi',g,ri,o);
          if(this.applied_filters.route){
            if(!this.applied_filters.route.every((f) => f(o))){
              return false;
            }
          }
          
          if(!o){
            return false;
          }
		  // console.error('combi good');
      
          ra.Combination.push({CombinationCode: c, CombinationIndex: i, RouteRef: o.Ref, RouteType: g, ...o});
          return true;
			  })){
				  return true;
			  }
			  if(!ra.Combination.length) return true;
        if(this.applied_filters.routes){
          if(!this.applied_filters.routes.every((f) => f(ra.Combination))){
            return false;
          }
        }
			  rs.push(ra);
			  return true;
			})
			return rs;
		},[]);
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
  watch:{
    'filters': {
      handler(newValue, oldValue){
        // console.warn('emitting filters', this.filters);
        this.$emit('filters', newValue);
      },
      immediate: true
    },
    'summary': {
      handler(newValue, oldValue){
        // console.warn('emitting summary', this.summary);
        this.$emit('summary', newValue);
      },
      immediate: true
    },
    'show_details': {
      handler(newValue, oldValue){
        if(newValue){
          this.dialog = true;
        }
      },
      immediate: true
    },
    'dialog': {
      handler(newValue, oldValue){
        if(!newValue){
          this.show_details = undefined;
        }
      },
      immediate: true
    },
    'search_loading': {
      handler(newValue, oldValue){
        // console.warn('emitting search_loading', newValue);
        this.$emit('search_loading', newValue);
      },
      immediate: true
    },
    'modelValue': {
      handler(newValue, oldValue){
        // console.warn('flights search', newValue);
        this.search();
      },
      immediate: true
    },
    'applied_filters': {
      handler(newValue, oldValue){
        this.filtered_flights = this.filteredFlights()

        this.selected_flight = this.filtered_flights[0];
        // TODO DELETE
        // this.$emit('selected_flight', this.selected_flight);
      },
      // immediate: true
    },
    'filtered_flights': {
      handler(newValue, oldValue){
        // console.warn('flights list', newValue);
        this.$emit('flights', newValue);
      },
      immediate: true
    },
    'saved': {
      handler(newValue, oldValue){
        this.clearValidations();
      },
      deep: true
    }
  }
}
