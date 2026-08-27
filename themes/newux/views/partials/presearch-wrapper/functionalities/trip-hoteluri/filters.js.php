let apply_timer;
let slider_apply_timer;
let availability_labels = {
	yes: "Disponibil",
	ask: "La cerere",
	no: "Indisponibil",
};
let filter_texts = {
	pois: "Puncte de interes",
	activities: "Activitati",
	facilities: "Facilitati",
};

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	data: () => {
		return {
			initial_set_slider_prices: false,
			embeded_var: 'hotels',
			expansion_panel_keys: {'facilities': ['Id', 'FacilitiesId'], 'pois': ['PoiId', 'PointOfInterestsId'], 'activities': ['ActivityId', 'ActivitiesId']},
			force_opened_facilities_groups: [],
			hotel_name: '',
			opened_facilities_groups: [],
			slider_prices: [],
			priceset: [],
			applied: {
				MinPrice:null,
				NonRefundable: null,
				Stars:[],
				hotel_name:'',
				FacilitiesId:[],
				PointOfInterestsId:[],
				ActivitiesId:[]
			},
			formatted_results: [],
			count: {
			},
			prices: [],
			availabilities: [],
			nonRefundables: [],
			stars: [],
			hotel: [],
			facilities: {},
			pois: {},
			activities: {},
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
			
			<template v-if="stars.length > 0">
			<div class="d-flex justify-space-between align-center">
			<v-text-field
				class="mb-4"
				v-model="hotel_name"
				type="text"
				label="Nume hotel"
				hide-details
				clearable
				@blur="this.applied.hotel_name = (hotel_name || '').toLowerCase().replace(/\\s+/g, ' ').trim()"
			>
			</v-text-field>
			 <v-btn
				class="ms-2"
				density="compact"
				icon="mdi-magnify"
			  ></v-btn>
			</div>
			<v-checkbox v-model="applied.Stars" v-for="star in stars" :value="star" multiple hide-details density="compact">
			  <template v-slot:label>
				<div class="text-h6">
					<v-icon v-if="star" icon="mdi-star" v-for="n in parseInt(star||0)" color="#fcc200"></v-icon>
					<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
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
			<template v-for="(filv, filk) in expansion_panel_keys">
				<v-expansion-panel v-if="this[filk] && Object.keys(this[filk]).length" :value="filv[1]"
					:title="texts[filk]"
				>
					<template v-slot:actions="{ expanded }">
						<v-icon :icon="expanded ? 'mdi-close' : 'mdi-chevron-down'"></v-icon>
					</template>
					<v-expansion-panel-text>
					<div class="d-flex flex-column">
						<v-checkbox v-for="(j,h) in this[filk]" multiple v-model="applied[filv[1]]" :value="h" hide-details density="compact" :label="j" v-show="-1 !== applied[filv[1]].indexOf(h) || !count[filk] || count[filk][h]">
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
		console.log('filters', this);
	},
	computed: {
	},
	methods: {
		filterResults(){},
	},
	watch: {
		'results.filters': {
			handler: function(nv,ov){
				var filters = this.results.filters || {};
				this.nonRefundables = (filters.nonRefundables || []).map(v => parseInt(v));
				this.stars = (filters.stars || []).map(v => parseInt(v));
				this.facilities = filters.facilities || [];
				this.activities = filters.activities || [];
				this.pois = filters.pois || [];
				this.priceset = [];
				if(filters.minPrice && filters.maxPrice && (filters.maxPrice != filters.minPrice)){
					this.prices = [filters.minPrice, filters.maxPrice];
					var len = 10;
					var diff = (parseFloat(filters.maxPrice) - parseFloat(Math.floor(filters.minPrice))) / len;
					this.priceset = [...Array(len + 1)].map((a,i) => Math.ceil(Math.floor(filters.minPrice) + (i * diff)));
					// console.warn('this.priceset', this.priceset);
				}
				this.slider_prices = [0, this.priceset.length-1];
				this.initial_set_slider_prices = true;
			},
		},
		'applied': {
			handler: function(nv,ov){
				console.warn('applied', JSON.stringify(nv), JSON.stringify(ov));
				this.force_opened_facilities_groups = Object.keys(this.expansion_panel_keys).filter(v => !!Object.values((this.applied[v[1]] || {})).length);
				
				clearTimeout(apply_timer);
				apply_timer = setTimeout(()=>{
					var filtered_results = {...this.results, _embedded: {
						[this.embeded_var]: [...this.results._embedded[this.embeded_var]].filter((h => {
							if(this.applied.Stars && this.applied.Stars.length){
								if(-1 == this.applied.Stars.indexOf(parseInt(h.Stars))){
									return false;
								}
							}
							var pricerange = (this.applied.MinPrice || {}).range || [];
							if(pricerange.length){
								// console.warn('pricerange', pricerange, h.MinPrice);
								if(pricerange[0] && parseFloat(h.MinPrice) < pricerange[0]){
									return false;
								}
								if(pricerange[1] && parseFloat(h.MinPrice) > pricerange[1]){
									return false;
								}
							}
							if(this.applied.FacilitiesId && this.applied.FacilitiesId.length){
								if(!h.Facilities) return false;
								if(this.applied.FacilitiesId.find(f => !(new RegExp('(^|,)\\s*' + escapeRegExp(f) + '\\s*(,|$)')).test(h.Facilities))) return false;
							}
							if(this.applied.PointOfInterestsId && this.applied.PointOfInterestsId.length){
								if(!h.Pois) return false;
								if(this.applied.PointOfInterestsId.find(f => !(new RegExp('(^|,)\\s*' + escapeRegExp(f) + '\\s*(,|$)')).test(h.Pois))) return false;
							}
							if(this.applied.hotel_name){
								if(!new RegExp(this.applied.hotel_name.split(/\s+/).map(escapeRegExp).join('.*'), 'i').test(h.Name)){
									return false;
								}
							}
							if(false !== this.applied.NonRefundable && null !== this.applied.NonRefundable && !isNaN(this.applied.NonRefundable)){
								return (this.applied.NonRefundable && !!h.NonRefundable) || (!this.applied.NonRefundable && !!h.Refundable);
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
					console.warn('filtering results', JSON.parse(JSON.stringify(nv)), JSON.parse(JSON.stringify(filtered_results)));
				},300);
				
			},
			deep: true
		},
		'opened_facilities_groups': {
			handler: function(nv,ov){
				console.warn('opened_facilities_groups', nv);
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
				slider_apply_timer = setTimeout(()=>{
					this.applied.MinPrice = {range: [this.priceset[((nv || [])[0]||0)]||0, this.priceset[((nv || [])[1]||this.priceset.length-1)]||0]};
				},500);
			}
		},
	}
}
