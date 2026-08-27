let search_axios, search_axios_timer, search_axios_cancel, results_timer;
const CancelToken = axios.CancelToken;

export default {
  emits: ['save'],
  props: {
      searches: {
          type: Object,
          default: () => ([]),
      },
      origin: {
          type: Object,
          default: () => ({}),
      },
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      airportUseVueFuse: {
          type: Object,
          default: () => (undefined),
      },
      airportLocations: {
          type: Array,
          default: () => (undefined),
      },
  },
  components : {
		'FlightsSearchRecentDestinations' : loadViewAsync('flights_search_recent_destinations'),
		'FlightsSearchPopularDestinations' : loadViewAsync('flights_search_popular_destinations'),
	},
	data: () => ({
    search_loading: false,
    selected_location_index: undefined,
    search_text: "",
    search_results: [],
    saved: {},
    dialog: false,
    errors: [],
    texts: Object.freeze({
      // at_least_one_sen_adt: "Calatoria trebuie sa contina cel putin un adult sau un senior",
    }),
    validations:Object.freeze([
      // function(){ return this.adt+this.sen < 1 ? 'at_least_one_sen_adt' : null },
    ]),
  }),
	template : `
  <Modal v-model="dialog">
    <template v-slot:activator="{ props }">
      <v-text-field v-bind="props"  class="mb-4 mx-4 bg-none"
						label="Sosire in"
						placeholder="Alegeti destinatia"
            readonly
            :active="dialog"
            :focused="dialog"
            v-model="destination_text"
            hide-details
            variant="underlined"
					></v-text-field>
    </template>
    <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
      <v-list-item-title class="pa-4 pb-2 pt-0 text-h5">Unde vrei sa zbori</v-list-item-title>
      <v-text-field @click="dialog=true"  class="mb-2 mx-4 mt-2 bg-none rounded-theme" ref="search_field"
          autofocus
          theme="dark"
          placeholder="Sosire in"
          v-model="search_text"
          hide-details
          color="primary"
          variant="outlined"
        ></v-text-field>
		<FlightsLoader v-if="search_loading" ></FlightsLoader>
      <div v-if="!search_loading && (!search_results || !search_results.length) && search_text">
        <v-list-subheader class="pl-4">Nicio sugestie gasita</v-list-subheader>
      </div>
      <div v-if="search_results && search_results.length">
        <v-list-subheader class="pl-4">Sugestii</v-list-subheader>
      <v-list
        class="mx-4"
        :items="groupedSortedResults"
        item-value="index"
        @click:select="(item) => ((item.value = selected_location_index != item.id), selected_location_index = item.id, !item.value && (validate() ? save() && (dialog = false) : null) )"
        density="compact"
        mandatory
        :item-props="(item) => {
          return {
            active: item.index == selected_location_index,
          }
        }"
      >
        <template v-slot:title="{ item }">
          <div v-if="item.first">
            <strong v-html="item.CityName"></strong>, <span v-html="item.CountryName"></span> <span v-if="item.CityCode" v-html="'(' + item.CityCode + ')'"></span>
          </div>
        </template>
        <template v-slot:subtitle="{ item }">
          <div class="pl-5 ml-5" :class="{'pt-4 pb-2': item.first}">
            <template v-if="item.LocationId">
              <strong v-html="item.LocationCode"></strong>, <span v-html="item.LocationName"></span>
            </template>
            <template v-else>
              <span v-html="'Toate aeroporturile'"></span>
            </template>
          </div>
        </template>
      </v-list>
      </div>
      <div>
        <FlightsSearchRecentDestinations v-model="searches"  v-on:save="saveDestination" />
      </div>
    </v-list>
    <template v-slot:footer="{ props }">
        <v-btn class="d-flex text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="'primary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
    </template>
  </Modal>
	`,

  methods: {
    saveDestination(newValue){
      // console.warn(newValue);
      if(!newValue){
        this.search_results = [];
      } else {
        this.search_results = [newValue];
        this.selected_location_index = newValue.LocationId && newValue.LocationId > 0 ? 1 : 0;
		this.save();
        this.dialog = false;
      }
    },
    clearValidations(){
      this.errors = [];
    },
    save(){
      // console.log('saved destination', this.selected_location);
      this.$emit('save', this.selected_location);
      return true;
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
    destination_text:{
      get() {
        if(this.selected_location){
          var l = this.selected_location;
          return (l.LocationId ? l.LocationName + ' (' + l.LocationCode + '), ' : '') + l.CityName + (!l.LocationId && l.CityCode ? ' (' + l.CityCode + ')' : '') + ', ' + l.CountryName;
        }
      }
    },
    groupedSortedResults:{
      get() {
        var r = {
          grouped: {},
          results: [],
        };
        if(this.search_results){
          this.search_results.forEach((i) => {
            // var first=false;
            if(!r.grouped[i.CountryId + '-' + i.CityId]){
              // first = true;
              r.grouped[i.CountryId + '-' + i.CityId] = [];
            }

            if(0 === r.grouped[i.CountryId + '-' + i.CityId].length){
              r.grouped[i.CountryId + '-' + i.CityId].push(0)
              r.results.push({
                first:true,
                index: r.results.length,
                LocationId: 0,
                LocationName: '',
                LocationCode: '',
                CityId: (i.CityId && i.CityId>0 && parseInt(i.CityId) ||0),
                CityName: (i.LinkedTo || i.CityName||''),
                CityCode: (i.CityCode||''),
                CountryId: (i.CountryId && i.CountryId>0 && parseInt(i.CountryId) ||0),
                CountryName: (i.CountryName||''),
              })
            }
            if((i.LocationId && i.LocationId>0)){
              // if(r.grouped[i.CountryId + '-' + i.CityId].length){
              //   return;
              // }
				r.grouped[i.CountryId + '-' + i.CityId].push(i.LocationId||0)
				r.results.push({
					first: false,
					index: r.results.length,
					LocationId: (i.LocationId && i.LocationId>0 && parseInt(i.LocationId) ||0),
					LocationName: (i.LocationName||''),
					LocationCode: (i.LocationCode||''),
					CityId: (i.CityId && i.CityId>0 && parseInt(i.CityId) ||0),
					CityName: (i.LinkedTo || i.CityName||''),
					CityCode: (i.CityCode||''),
					CountryId: (i.CountryId && i.CountryId>0 && parseInt(i.CountryId) ||0),
					CountryName: (i.CountryName||''),
				})
            }
          })
        }
		console.warn('groupedSortedResults0', r.grouped);
		console.warn('groupedSortedResults', r.results);
        return r.results;
      },
    },
    selected_location:{
      get() { return undefined === this.selected_location_index ? undefined : this.groupedSortedResults[this.selected_location_index] },
    },
    year_range:{
      get() { 
        var currentYear = new Date().getFullYear();
        return [currentYear, currentYear+2]
      },
    },
    multicalendars:{
      get() { return window.innerHeight > 650 || window.innerWidth > 650 },
    },
    date:{
      get() { var d; var r = !this.saved.date ? undefined : [this.saved.date, (d = new Date(this.saved.date), d.setDate(d.getDate() + (this.saved.days||0)), d)]; return r;},
      set(newValue){ false ? 0 : (!newValue || !newValue.length ? (this.saved.date = undefined,this.saved.days=0) : (this.saved.date = new Date(!newValue[1] ? newValue[0] : Math.min(newValue[0], newValue[1])), this.saved.days = !newValue[1] ? 0 : Math.floor((Math.max(newValue[0], newValue[1]) - this.saved.date) / 86400000)));},
    },
  },
  watch:{
    'search_text': {
      handler(newValue, oldValue){
        if(newValue != oldValue){
          search_axios_cancel && search_axios_cancel();
          clearTimeout(search_axios_timer);
		  clearTimeout(results_timer);
          this.search_loading = false;
          if(newValue){  
            this.search_loading = true;
            this.search_results = [];
            this.selected_location_index = undefined;
			  clearTimeout(results_timer);
			  results_timer = setTimeout(() => {
				  this.airportUseVueFuse.search = newValue;
				  setTimeout(() => {
					  console.warn('results', this.airportUseVueFuse.resultsRaw); 
					  var res = JSON.parse(JSON.stringify(this.airportUseVueFuse.results.slice(0,20)));
					  this.search_results = res;
					  this.selected_location_index = 0;
					  this.search_loading = false;
				  }, 10)
				  return;
			  },250);
			  return;
            this.search_loading = true;
            this.search_results = [];
            this.selected_location_index = undefined;
            search_axios_timer = setTimeout(() => {
              var url = '<?php echo site_url('trip/flights/loadLocations',); ?>';
              search_axios = axios.get(url,{
                params:{q: newValue, lang: 'ro', force_ajax: 1, pay24: 1},
              }, {
                validateStatus: function (status) {return status == 200},
                cancelToken: new CancelToken(function executor(c) {
                  search_axios_cancel = c;
                })
              }).then((result) => {
                if(result && result.data && result.data.response && Array.isArray(result.data.response) && result.data.response.length){
                  this.search_results = Object.freeze(result.data.response);
                  this.selected_location_index = 0;
                }
              }).finally(() => {
                this.search_loading = false;
              })
            },1000)
          }
        }
      },
    },
    'dialog': {
      handler(newValue, oldValue){
        if(!this.kept){
          this.saved = {}
        } else {
          this.saved = Object.assign({}, this.kept);
        }
      },
    },
    'selected_location_index': {
      handler(newValue, oldValue){
        // console.warn(newValue);
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        this.search_text = undefined;
        this.selected_location_index = undefined;
        if(!newValue){
          this.search_results = [];
        } else {
          this.search_results = [newValue];
          this.selected_location_index = newValue.LocationId && newValue.LocationId > 0 ? 1 : 0;
        }
      }
    },
    'saved': {
      handler(newValue, oldValue){
        this.clearValidations();
      },
      deep: true
    }
  }
}
