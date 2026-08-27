let apply_timer;
let slider_apply_timer;
let slider_apply_timer2;
let availability_labels = {
	yes: "Disponibil",
	ask: "La cerere",
	no: "Indisponibil",
};
let filter_texts = {
	companies: "Companie",
	airports: "Aeroporturi",
	cabins: "Clasa zbor",
};

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	data: () => {
		return {
			initial_set_slider_prices: false,
			embeded_var: 'flights',
			expansion_panel_keys: {'airports': ['Id', 'Airports'], 'cabins': ['PoiId', 'Cabins'], 'companies': ['Code', 'Companies']},
			force_opened_facilities_groups: [],
			hotel_name: '',
			opened_facilities_groups: [],
			slider_hours: {
				tur: {
					decolare: {inverse: false, interval: [0,287]},
					aterizare: {inverse: false, interval: [0,287]},
				},
				retur: {
					decolare: {inverse: false, interval: [0,287]},
					aterizare: {inverse: false, interval: [0,287]},
				},
			},
			slider_prices: [],
			priceset: [],
			applied: {
				SliderPrices:null,
				MinPrice:null,
				Stops: null,
				NonRefundable: null,
				hotel_name:'',
				FacilitiesId:[],
				PointOfInterestsId:[],
				Companies:[],
				Cabins:[],
				Airports:[]
			},
			formatted_results: [],
			count: {
			},
			prices: [],
			availabilities: [],
			nonRefundables: [],
			stops: [],
			hotel: [],
			facilities: {},
			pois: {},
			companies: {},
			cabins: {},
			airports: {},
			texts: Object.freeze(filter_texts),
			availability_label: Object.freeze(availability_labels),
		}
	},
	emits: ['filtered', 'applied'],
	props: {
      results: {
          type: Object,
          default: () => ({}),
      },
      data: {
          type: Object,
          default: () => ({}),
      },
      applied_filters: {
          type: Object,
          default: () => ({}),
      },
      searching: {
          type: Boolean,
          default: () => (false),
      },
  },
	template : `
	<div class="bg-background">
		<div class="filters-header text-h6 font-weight-bold">
			<i class="fa fa-sliders"></i> Filtre cautare
		</div>
		<div v-if="searching && undefined === results.Id">Searching...</div>
		<div v-else-if="!((results._embedded||{})[embeded_var] || []).length && undefined === results.Id">No results found...</div>
		<div v-else :class="{'loading-section': searching}">
			<template v-if="priceset.length > 0">
			<span v-text="texts.Price"></span>
			<v-range-slider
				color="info"
				thumb-label="always"
				v-model="slider_prices"
				:ticks="priceset"
				min="0"
				:max="priceset.length -1"
				step="1"
				strict
				class="px-2 mt-6"
			>
			<template v-slot:thumb-label="{ modelValue }">
			  <span class="text-pre" v-text="format_price(priceset[modelValue], 'EUR', true)"></span>
			</template>
			</v-range-slider>
			</template>
			
			<template v-if="stops.length > 0">
			<v-checkbox v-model="applied.Stops" v-for="stop in stops" :value="parseInt(stop)" hide-details density="compact">
			  <template v-slot:label>
				<div class="text-h6">
					<span v-if="1 == stop">Maxim 1 escala</span>
					<span v-else-if="stop">Maxim {{ stop }} escale</span>
					<span v-else>Zbor direct</span>
				</div>
			  </template>
			</v-checkbox>
			</template>
			
			<template v-if="nonRefundables.length > 0">
			<v-checkbox v-model="applied.NonRefundable" v-for="refun in nonRefundables" :value="parseInt(refun)" hide-details density="compact">
			  <template v-slot:label>
				<div class="text-h6">
					<span v-if="1 == refun">Non-Refundable</span>
					<span v-else>Refundable</span>
				</div>
			  </template>
			</v-checkbox>
			</template>
			<v-expansion-panels v-model="opened_facilities_groups" multiple>
				<v-expansion-panel value="hours" title="Ore">
					<v-expansion-panel-text>
					<template v-for="rtype in (results?.searchData?.Type ? ['tur', 'retur'] : ['tur'])">
						<legend v-text="rtype.toUpperCase()" v-if="results?.searchData?.Type"></legend>
						<template v-for="htype in ['decolare', 'aterizare']">
							<div class="d-flex justify-space-between"><span v-text="'Interval ' + htype"></span><span v-text="hoursset[slider_hours[rtype][htype].interval[(slider_hours[rtype][htype].inverse ? 1 : 0)]] + ' - ' + hoursset[slider_hours[rtype][htype].interval[(slider_hours[rtype][htype].inverse ? 0 : 1)]]"></span></div>
							<v-range-slider
								color="info"
								thumb-label="always"
								v-model="slider_hours[rtype][htype].interval"
								:ticks="hoursset"
								min="0"
								:max="hoursset.length -1"
								step="1"
								class="pe-2 mt-7"
								<?php /* :reverse="!!slider_hours[rtype][htype].inverse" */ ?>
								:class="{'slider-inversed': !!slider_hours[rtype][htype].inverse}"
							>
							<template v-slot:prepend>
								<v-icon icon="mdi-hours-24" :color="slider_hours[rtype][htype].inverse ? 'warning' : 'primary'" @click="slider_hours[rtype][htype].inverse = !slider_hours[rtype][htype].inverse"></v-icon>
							</template>
							<template v-slot:thumb-label="{ modelValue }">
							  <span class="text-pre" v-text="hoursset[modelValue]"></span>
							</template>
							</v-range-slider>
						</template>
					</template>
					</v-expansion-panel-text>
				</v-expansion-panel>
				
			<template v-for="(filv, filk) in expansion_panel_keys">
				<v-expansion-panel v-if="this[filk] && Object.keys(this[filk]).length" :value="filv[1]"
					:title="texts[filk]"
				>
					<template v-slot:actions="{ expanded }">
						<v-icon :icon="expanded ? 'mdi-close' : 'mdi-chevron-down'"></v-icon>
					</template>
					<v-expansion-panel-text>
					<div class="d-flex flex-column">
						<v-checkbox v-if="'companies' == filk" v-for="(j,h) in this[filk]" multiple v-model="applied[filv[1]]" :value="j.code" hide-details density="compact" :label="j.name" v-show="-1 !== applied[filv[1]].indexOf(h) || !count[filk] || count[filk][h]">
						  <template v-slot:label>
							<img v-if="j.img" :src="j.img" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
							<span class="text-h6 mt-1 ms-1" v-html="j.name"></span>
						  </template>
						</v-checkbox>
						<v-checkbox v-else-if="'airports' == filk" v-for="(j,h) in this[filk]" multiple v-model="applied[filv[1]]" :value="j.Code" hide-details density="compact">
						  <template v-slot:label>
							<div class="text-h6" v-html="j.Name + ' ' + j.City"></div>
						  </template>
						</v-checkbox>
						<v-checkbox v-else-if="'cabins' == filk" v-for="(j,h) in this[filk]" multiple v-model="applied[filv[1]]" :value="j" hide-details density="compact" :label="j" v-show="-1 !== applied[filv[1]].indexOf(j) || !count[filk] || count[filk][j]">
						  <template v-slot:label>
							<div class="text-h6" v-html="j"></div>
						  </template>
						</v-checkbox>
						<v-checkbox v-else v-for="(j,h) in this[filk]" multiple v-model="applied[filv[1]]" :value="h" hide-details density="compact" :label="j" v-show="-1 !== applied[filv[1]].indexOf(h) || !count[filk] || count[filk][h]">
						  <template v-slot:label>
							<div class="text-h6" v-html="j"></div>
						  </template>
						</v-checkbox>
						</div>
					</v-expansion-panel-text>
				</v-expansion-panel>
			</template>
			</v-expansion-panels>
		</div>
	</div>
	`,
	beforeCreate() {
	},
	mounted() {
		// console.log('filters', this);
	},
	computed: {
	},
	methods: {
		filterResults(){},
	},
	watch: {
		'results': {
			handler: function(nv,ov){
				var filters = this.results.filters || {};
				this.nonRefundables = (filters.nonRefundables || []).map(v => parseInt(v));
				this.stops = (filters.stops || []).map(v => parseInt(v));
				this.facilities = filters.facilities || [];
				this.companies = filters.companies || [];
				this.cabins = filters.cabinTypes || [];
				var airports = (filters.airports || this.results?._embedded?.[this.embeded_var] || []).reduce((airports, flight) => {
					flight.Routes.forEach((route, rk) => route.Route.forEach((subroute) => subroute.Segment.forEach((segment, segmentIndex) => {
						var airport = segment.Origin.Airport || {};
						if(airport.Code && !airports[airport.Code]){
							airports[airport.Code] = {Code: airport.Code, Name: airport._, City: airport.City, Type: flight.Routes.length};
							if(!segmentIndex){
								airports[airport.Code].Type = (flight.Routes.length - 1) - rk;
							}
						}
						if(segmentIndex == subroute.Segment.length-1){
							var airport = segment.Destination.Airport || {};
							if(airport.Code && !airports[airport.Code]){
								airports[airport.Code] = {Code: airport.Code, Name: airport._, City: airport.City, Type: rk};
							}
						}
					})))
					return airports;
				}, {});
				this.airports = Object.values(airports).sort((a,b) => a.Type - b.Type);
				// console.warn('this.airports', this.airports);
				this.pois = filters.pois || [];
				this.hoursset = [...Array(288)].map((i,a) => (''+parseInt(a * 5/60)).padStart(2,'0') + ':' + ('' + parseInt((a*5)%60)).padStart(2, '0'));
				this.priceset = [];
				if(filters.minPrice && filters.maxPrice && (filters.maxPrice != filters.minPrice)){
					this.prices = [filters.minPrice, filters.maxPrice];
					var len = 10;
					var diff = (parseFloat(filters.maxPrice) - parseFloat(Math.floor(filters.minPrice))) / len;
					this.priceset = [...Array(len + 1)].map((a,i) => Math.ceil(Math.floor(filters.minPrice) + (i * diff)));
					// console.warn('this.priceset', this.priceset, [filters.minPrice, filters.maxPrice, diff]);
				}
				this.slider_prices = [0, this.priceset.length-1];
				this.initial_set_slider_prices = true;
				// console.warn('this.slider_prices', this.slider_prices);
				
				Object.assign(this.applied, this.applied_filters);
				
				if(this.applied_filters.SliderPrices){
					this.slider_prices = this.applied_filters.SliderPrices;
				}
				console.warn('this.applied', JSON.parse(JSON.stringify(this.applied)));
			},
			immediate:true,
		},
		'applied': {
			handler: function(nv,ov){
				this.force_opened_facilities_groups = Object.keys(this.expansion_panel_keys).filter(v => !!Object.values((this.applied[v[1]] || {})).length);
				
				clearTimeout(apply_timer);
				apply_timer = setTimeout(()=>{
					var once = false;
					var filtered_results = {...this.results, _embedded: {
						[this.embeded_var]: (!this.results?._embedded && [] || [...JSON.parse(JSON.stringify(this.results._embedded?.[this.embeded_var]))]).filter((f => {
							f.RealCombinations = f.RealCombinations || [...f.Combinations];
							var o = once;
							once = true;
							if(false !== this.applied.Stops && null !== this.applied.Stops && !isNaN(this.applied.Stops)){
								// console.warn("this.applied.Stops", this.applied.Stops);
								var enabled_rs = [];
								
								f.Routes = f.Routes.filter((route, rk) => { return (route.Route = route.Route.filter((subroute, srk) => (((subroute.Segment.length - 1) <= this.applied.Stops) && (enabled_rs.push('' + rk + '' + subroute.Ref), true)))).length });
								
								if(!f.Routes.length) return false;
								f.Combinations = f.Combinations.filter((combination) => !(combination.split('|').find(r => -1 == enabled_rs.indexOf(r))));
								if(!f.Combinations.length) return false;
							}
							
							if(this.applied.Companies && this.applied.Companies.length){
								// console.warn("this.applied.Companies", this.applied.Companies.length);
								var enabled_rs = [];
								
								f.Routes.filter((route, rk) => { return (route.Route.filter((subroute, srk) => ((subroute.Segment.find(segment => -1 !== this.applied.Companies.indexOf(segment.Carrier.Marketing.Code))) && (enabled_rs.push('' + rk + '' + subroute.Ref), true)))).length });
								
								// if(!f.Routes.length) return false;
								f.Combinations = f.Combinations.filter((combination) => (combination.split('|').find(r => -1 !== enabled_rs.indexOf(r))));
								if(!f.Combinations.length) return false;
							}
							
							if(this.applied.Cabins && this.applied.Cabins.length){
								// console.warn("this.applied.Cabins", this.applied.Cabins);
								var enabled_rs = [];
								var cabins = this.applied.Cabins.map(c => c.toLowerCase());
								
								f.Routes.filter((route, rk) => { return (route.Route.filter((subroute, srk) => ((subroute.Segment.find(segment => -1 !== cabins.indexOf(segment.Flight.CabinType.toLowerCase()))) && (enabled_rs.push('' + rk + '' + subroute.Ref), true)))).length });
								
								// if(!f.Routes.length) return false;
								f.Combinations = f.Combinations.filter((combination) => (combination.split('|').find(r => -1 !== enabled_rs.indexOf(r))));
								if(!f.Combinations.length) return false;
							}
							
							if(this.applied.Airports && this.applied.Airports.length){
								// console.warn("this.applied.Airports", this.applied.Airports.length, this.applied.Airports);
								var enabled_rs = [];
								
								f.Routes.filter((route, rk) => { return (route.Route.filter((subroute, srk) => ((subroute.Segment.find((segment, segmentIndex) => ((-1 !== this.applied.Airports.indexOf((segment.Destination.Airport || {}).Code)) || (!segmentIndex && -1 !== this.applied.Airports.indexOf((segment.Origin.Airport || {}).Code))))) && (enabled_rs.push('' + rk + '' + subroute.Ref), true)))).length });
								
								// if(!f.Routes.length) return false;
								f.Combinations = f.Combinations.filter((combination) => (combination.split('|').find(r => -1 !== enabled_rs.indexOf(r))));
								if(!f.Combinations.length) return false;
							}
							
							if(this.applied.Hours && this.applied.Hours.length){
								// console.warn("this.applied.Airports", this.applied.Airports.length, this.applied.Airports);
								var enabled_rs = [];
								
								f.Routes.filter((route, rk) => { return this.applied.Hours[rk] && (route.Route.filter((subroute, srk) => {
									// console.warn(rk, this.applied.Hours[rk], subroute.Segment[0].Origin.Time, subroute.Segment[subroute.Segment.length-1].Destination.Time);
									if(this.applied.Hours[rk][0]){
										if(this.applied.Hours[rk][0][0] < this.applied.Hours[rk][0][1]){
											if(subroute.Segment[0].Origin.Time < this.applied.Hours[rk][0][0] || subroute.Segment[0].Origin.Time > this.applied.Hours[rk][0][1]){
												// console.error('EXCLUDED');
												return false;
											}
										} else {
											if(!(subroute.Segment[0].Origin.Time < this.applied.Hours[rk][0][1] || subroute.Segment[0].Origin.Time > this.applied.Hours[rk][0][0])){
												// console.error('EXCLUDED');
												return false;
											}
										}
									}
									if(this.applied.Hours[rk][1]){
										if(this.applied.Hours[rk][1][0] < this.applied.Hours[rk][1][1]){
											if(subroute.Segment[subroute.Segment.length-1].Destination.Time < this.applied.Hours[rk][1][0] || subroute.Segment[subroute.Segment.length-1].Destination.Time > this.applied.Hours[rk][1][1]){
												// console.error('EXCLUDED');
												return false;
											}
										} else {
											if(!(subroute.Segment[subroute.Segment.length-1].Destination.Time < this.applied.Hours[rk][1][1] || subroute.Segment[subroute.Segment.length-1].Destination.Time > this.applied.Hours[rk][1][0])){
												// console.error('EXCLUDED');
												return false;
											}
										}
									}
									// console.log('ENABLED');
									
									return (enabled_rs.push('' + rk + '' + subroute.Ref), true)
								})).length });
								
								// if(!f.Routes.length) return false;
								f.Combinations = f.Combinations.filter((combination) => !(combination.split('|').find(r => -1 === enabled_rs.indexOf(r))));
								if(!f.Combinations.length) return false;
								// console.warn('applied.Hours flight', f);
							}
							
							var pricerange = (this.applied.MinPrice || {}).range || [];
							if(pricerange.length){
								// console.warn("pricerange.length", pricerange.length);
								if(pricerange[0] && parseFloat(f.Price) < pricerange[0]){
									// console.warn("EXCLUDED", f);
									return false;
								}
								if(pricerange[1] && parseFloat(f.Price) > pricerange[1]){
									// console.warn("EXCLUDED", f);
									return false;
								}
							}
							
							if(false !== this.applied.NonRefundable && null !== this.applied.NonRefundable && !isNaN(this.applied.NonRefundable)){
								return !f.NonRefundable === !this.applied.NonRefundable;
							}
							
							return true;
						}))
					}};
					this.count.facilities = {};
					this.count.pois = {};
					filtered_results._embedded[this.embeded_var].forEach(h => {
						if(h.Facilities){
							if(typeof h.Facilities !== 'string'){
								console.warn(h);
							}
							h.Facilities.split(/\s*,\s*/).forEach(f => {
								this.count.facilities[f] = 1;
							});
						}
						if(h.Pois){
							h.Pois.split(/\s*,\s*/).forEach(f => {
								this.count.pois[f] = 1;
							});
						}
					});
					filtered_results.total_items = filtered_results._embedded[this.embeded_var].length;
					this.$emit('applied', nv);
					this.$emit('filtered', filtered_results);
					// console.warn('filtering results', JSON.parse(JSON.stringify(nv)), JSON.parse(JSON.stringify(filtered_results)));
				},300);
				
			},
			deep: true,
			immediate: true
		},
		'opened_facilities_groups': {
			handler: function(nv,ov){
				// console.warn('opened_facilities_groups', nv);
				if(this.force_opened_facilities_groups.filter(v => -1 === (nv||[]).indexOf(v)).length){
					this.opened_facilities_groups = [...this.force_opened_facilities_groups].concat((nv || []).filter(v => -1 == this.force_opened_facilities_groups.indexOf(v)));
				}
			}
		},
		'slider_prices': {
			handler: function(nv,ov){
				var initial = this.initial_set_slider_prices;
				this.initial_set_slider_prices = false;
				if(initial) return;
				clearTimeout(slider_apply_timer);
				this.applied.SliderPrices = nv;
				slider_apply_timer = setTimeout(()=>{
					this.applied.MinPrice = {range: [this.priceset[((nv || [])[0]||0)]||0, this.priceset[((nv || [])[1]||this.priceset.length-1)]||0]};
				},500);
			}
		},
		'slider_hours': {
			handler: function(nv,ov){;
				clearTimeout(slider_apply_timer2);
				slider_apply_timer2 = setTimeout(()=>{
					var hours = [];
					['tur', 'retur'].forEach((htype, i) => {
						var h = [];
						hours.push(h);
						['decolare', 'aterizare'].forEach((rtype, j) => {
							var r;
							if((nv?.[htype]?.[rtype]?.interval ?? []).length){
								r = [...nv[htype][rtype].interval].map(k => this.hoursset[k]);
								(nv[htype][rtype].inverse) && r.reverse();
							}
							h.push(r);
						});
					})
					// console.warn('this.applied.Hours', hours, nv);
					this.applied.Hours = hours;
				},500);
			},
			deep: true
		},
	}
}
