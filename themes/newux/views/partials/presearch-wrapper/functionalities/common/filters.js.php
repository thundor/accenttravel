let apply_timer;
let slider_apply_timer;
let availability_labels = {
	yes: "Disponibil",
	ask: "La cerere",
	no: "Indisponibil",
};
let filter_texts = {
	Type: "Tip",
	Quality: "Calitate",
	Beds: "Paturi",
	Layout: "Dispunere",
	Facility: "Facilitati oferta",
	Position: "Amplasare",
	Size: "Dimensiune",
	Price: "",
	View: "Priveliste",
	Meal: "Tip de masa",
	Merch: "Facilitati oferta",
	Other: "Facilitati hotel",
	Availability: "Disponibilitate",
	Restrict: "Restrictii",
	Hotel: "Facilitati hotel",
	Transfer: "Transfer",
	Tax: "Taxe",
	Transport: "Transport",
};

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	data: () => {
		return {
			force_opened_facilities_groups: [],
			hotel_name: '',
			opened_facilities_groups: [],
			slider_prices: [],
			priceset: [],
			applied: {
				hotel:[],
				facilities:{},
				availability:''
			},
			formatted_results: [],
			filtered_results: [],
			count: {
				hotel:{},
				stars:{},
				facilities:{},
			},
			count2: {
				stars:{},
			},
			prices: [],
			availabilities: [],
			stars: [],
			hotel: [],
			facilities: {},
			texts: Object.freeze(filter_texts),
			availability_label: Object.freeze(availability_labels),
		}
	},
	emits: ['filtered', 'applied'],
	props: {
      data: {
          type: Object,
          default: () => ({}),
      },
      results: {
          type: Array,
          default: () => ([]),
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
		<div v-if="searching">Searching...</div>
		<div v-else-if="!results.length">No results found...</div>
		<div v-else>
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
			<v-checkbox v-model="applied.stars" v-for="star in stars" :value="star" hide-details density="compact">
			  <template v-slot:label>
				<div class="text-h6">
					<v-icon v-if="star" icon="mdi-star" v-for="n in parseInt(star||0)" :color="this.count.stars[star] ? '#fcc200' : null"></v-icon>
					<v-icon v-else icon="mdi-star-off-outline" :color="this.count.stars[star] ? '#fcc200' : null"></v-icon>
				</div>
			  </template>
			  <template v-slot:append>
				<v-badge v-if="!this.count.stars[star]"
				  color="warning"
				  :content="this.count2.stars[star] || 0"
				  inline
				></v-badge>
				<v-badge v-else
				  :color="this.count.stars[star] && 'info' || this.count2.stars[star] && 'warning' || 'secondary'"
				  :content="this.count.stars[star] || this.count2.stars[star] || 0"
				  inline
				></v-badge>
			  </template>
			</v-checkbox>
			<v-radio-group v-model="applied.availability">
			<v-checkbox v-for="availability in Object.keys(availability_label).filter(a => -1 !== availabilities.indexOf(a))" :value="availability" hide-details density="compact" :label="availability_label[availability]">
			</v-checkbox>
			</v-radio-group>
			</template>
			<template v-if="Object.keys(facilities).length > 1">
			<v-expansion-panels v-model="opened_facilities_groups" multiple>
				<v-expansion-panel v-if="this.count.hotel" value="hotel"
					:title="texts.Hotel"
				>
					<template v-slot:actions="{ expanded }">
						<v-icon :icon="expanded ? 'mdi-close' : 'mdi-chevron-down'"></v-icon>
					</template>
					<v-expansion-panel-text>
					<div class="d-flex flex-column">
						<v-checkbox v-for="h in hotel" v-model="applied.hotel" v-show="!(!this.count.hotel || !this.count.hotel[h] && (!applied.facilities || !applied.hotel || -1 == applied.hotel.indexOf(h)))" :value="h" hide-details density="compact" :label="h" :class="{['order-' + (-1 === (this.applied.hotel || []).indexOf(h) && 1 || 0)]: 1}">
						  <template v-slot:label>
							<div class="text-h6" v-html="h"></div>
						  </template>
						  <template v-slot:append>
							<v-badge
							  color="info"
							  :content="this.count.hotel && this.count.hotel[h] || 0"
							  inline
							></v-badge>
						  </template>
						</v-checkbox>
						</div>
					</v-expansion-panel-text>
				</v-expansion-panel>
				<template v-for="(facs, key) in facilities">
			  <v-expansion-panel v-if="this.count.facilities[key]" :value="key"
					:title="texts[key]"
				>
				<template v-slot:actions="{ expanded }">
					<v-icon :icon="expanded ? 'mdi-close' : 'mdi-chevron-down'"></v-icon>
				</template>
				<v-expansion-panel-text>
					<div class="d-flex flex-column">
					<v-checkbox  v-for="f in facs" v-model="applied.facilities[key]" :value="f" hide-details density="compact" multiple v-show="!(!this.count.facilities[key] || !this.count.facilities[key][f] && (!applied.facilities || !applied.facilities[key] || -1 == applied.facilities[key].indexOf(f)))" :class="{['order-' + (-1 === (this.applied.facilities[key] || []).indexOf(f) && 1 || 0)]: 1}">
					  <template v-slot:label>
						<div class="text-h6" v-html="((search.merch_type[key] || {})[f] || ['', f])[1]"></div>
					  </template>
					  <template v-slot:append>
						<v-badge
						  color="info"
						  :content="this.count.facilities[key] && this.count.facilities[key][f] || 0"
						  inline
						></v-badge>
					  </template>
					</v-checkbox>
					</div>
				</v-expansion-panel-text>
			  </v-expansion-panel>
			  </template>
			</v-expansion-panels>
			</template>
		</div>
	</div>
	`,
	beforeCreate() {
	},
	mounted() {
		
	},
	computed: {
		sorted_facilities() {
			var facilities = Object.assign({}, this.facilities);
			console.warn('facilities', facilities);
			Object.keys(facilities).forEach((k) => {
				var facs = facilities[k] || [];
				facs.sort((a, b) => ((-1 !== (this.applied.facilities[k] || []).indexOf(b)) - (-1 !== (this.applied.facilities[k] || []).indexOf(a))));
				facilities[k] = facs;
			});
			console.warn('facilities', facilities);
			return facilities;
		}
	},
	methods: {
		filterResults(){
			console.warn('this.formatted_results', this.formatted_results);
			var applied_filters = this.applied;
			let no_star_filtered_results = Object.freeze(JSON.parse(JSON.stringify(this.toRaw(this.formatted_results ))).filter(h => {
				if(applied_filters){
					if(applied_filters.hotel && applied_filters.hotel.length){
						if(!h.Facilities || !h.Facilities.length) return false;
						if(applied_filters.hotel.find(f => -1 === h.Facilities.indexOf(f))) return false;
					}
				}
				return true;
			}).map(h => {
				if(applied_filters){
					h.Offers = h.Offers.filter(o => {
						if(applied_filters.availability){
							var i = ((o.Items || []).find((i) => (i.Merch || {}).type == 'Room') || {});
							var availability = i.Availability || 'no';
							if(applied_filters.availability != availability) return false;
						}
						if(applied_filters.prices){
							if(applied_filters.prices[0] && o.Price < applied_filters.prices[0]){
								return false;
							}
							if(applied_filters.prices[1] && o.Price > applied_filters.prices[1]){
								return false;
							}
						}
						if(-1 !== Object.keys(applied_filters.facilities).filter(k => applied_filters.facilities[k] && applied_filters.facilities[k].length).findIndex(k => !(o.facilities[k] && o.facilities[k].filter(j => -1 !== applied_filters.facilities[k].indexOf(j)).length == applied_filters.facilities[k].length))){
							return false;
						}
						return true;
					})
				}
				return h;
			}).filter(h => {
				if(applied_filters){
					if(applied_filters.hotel_name){
						if(!new RegExp(applied_filters.hotel_name.split(/\s+/).map(escapeRegExp).join('.*'), 'i').test(h.Name)){
							return false;
						}
					}
				}
				if(!h.Offers.length) return false;
				return true;
			}));
			this.count2.stars = {};
			no_star_filtered_results.forEach((h) => {
				this.count2.stars[h.Stars] = this.count2.stars[h.Stars] || 0;
				this.count2.stars[h.Stars]++;
			});
			
			this.filtered_results = no_star_filtered_results.filter(h => {
				if(applied_filters){
					if(applied_filters.stars){
						if(applied_filters.stars.length && -1 == applied_filters.stars.indexOf(h.Stars)){
							return false;
						}
					}
				}
				return true;
			})
			
			// console.warn('this.filtered_results', JSON.parse(JSON.stringify(this.filtered_results)));
			this.count.hotel = null;
			this.count.stars = {};
			this.count.facilities = {};
			this.filtered_results.forEach((h) => {
				if(h.Facilities && h.Facilities.length){
					h.Facilities.forEach(f => {
						this.count.hotel = this.count.hotel || {};
						this.count.hotel[f] = this.count.hotel[f] || 0;
						this.count.hotel[f]++;
					})
				}
				
				this.count.stars[h.Stars] = this.count.stars[h.Stars] || 0;
				this.count.stars[h.Stars]++;
				h.Offers.forEach(o => {
					Object.keys(o.facilities || {}).forEach((k) => {
						let fac = o.facilities[k];
						this.count.facilities[k] = this.count.facilities[k] || {};
						fac.forEach(f => {
							this.count.facilities[k][f] = this.count.facilities[k][f] || 0;
							this.count.facilities[k][f]++;
						})
					});
				});
			});
		},
	},
	watch: {
		'results': {
			handler: function(nv,ov){
				this.formatted_results = Object.freeze(JSON.parse(JSON.stringify(this.toRaw(nv))));
				
				this.availabilities = [];
				this.hotel = [];
				this.stars = [];
				this.prices = [];
				this.facilities = {};
				var availabilities = [];
				var hotel = [];
				var stars = [];
				var facilities = {};
				var prices = [undefined, undefined];
				
				let priceset = [];
				this.formatted_results.forEach((h) => {
					if(h.Facilities && h.Facilities.length){
						h.Facilities.forEach(f => {
							if(-1 == hotel.indexOf(f)){
								hotel.push(f)
							}
						})
					}
					h.Offers.forEach(o => {
						priceset.push(o.Price);
						if(!prices[0] || o.Price < prices[0]){
							prices[0] = Math.floor(o.Price);
						}
						if(!prices[1] || o.Price > prices[1]){
							prices[1] = Math.ceil(o.Price);
						}
					});
					if(undefined !== h.Stars && -1 == stars.indexOf(h.Stars)){
						stars.push(h.Stars);
					}
					stars.sort();
					h.Offers.forEach(o => {
						var i = ((o.Items || []).find((i) => (i.Merch || {}).type == 'Room') || {});
						var availability = i.Availability || 'no';
						if(-1 == availabilities.indexOf(availability)){
							availabilities.push(availability);
						}
						
						Object.keys(o.facilities || {}).forEach((k) => {
							let fac = o.facilities[k];
							if(fac && fac.length){
								facilities[k] = [...new Set((Object.values(facilities[k]||[])).concat(fac))].reduce((c, i) => (c[i.toLowerCase()] = i,c), {});
							}
						});
					});
				});
				priceset.sort(function(a, b){return a - b});
				this.priceset = [... new Set(priceset)];
				this.slider_prices = [0, this.priceset.length-1];
				console.log('priceset', this.priceset);
				console.log('this.search.merch_type', this.search);
				
				this.facilities = Object.keys(facilities).sort((a, b) => Object.keys(this.search.merch_type || {}).indexOf(a) - Object.keys(this.search.merch_type || {}).indexOf(b)).reduce((c,v) => (c[v] = Object.values(facilities[v]).sort((a, b) => Object.keys(this.search.merch_type[v] || {}).indexOf(a) - Object.keys(this.search.merch_type[v] || {}).indexOf(b)), c), {});
				console.log('facilities', facilities);
				this.prices = prices;
				this.stars = stars;
				this.hotel = hotel;
				this.availabilities = availabilities;
				
				this.applied.facilities = Object.keys(this.facilities).reduce((c, i) => (c[i] = [], c), {});
				this.filterResults();
				this.applied.stars = [];
				this.applied.prices = this.prices;
			},
			immediate: true,
		},
		'filtered_results': {
			handler: function(nv,ov){
				if(JSON.stringify(ov) === JSON.stringify(nv)) return;
				if(window.block_filtering) return;
				clearTimeout(apply_timer);
				apply_timer = setTimeout(()=>{
					this.$emit('applied', this.applied);
					this.$emit('filtered', nv);
					console.warn('filtering results', nv);
				},300);
			},
			immediate: true,
		},
		'applied': {
			handler: function(nv,ov){
				this.force_opened_facilities_groups = Object.keys(this.applied.facilities).filter(v => !!Object.values(this.applied.facilities[v]).length);
				if(this.applied.hotel && this.applied.hotel.length){
					this.force_opened_facilities_groups.push('hotel');
				}
				// clearTimeout(apply_timer);
				this.filterResults();
				// apply_timer = setTimeout(()=>{
					// this.applied.prices = prices;
				// },100);
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
				clearTimeout(slider_apply_timer);
				slider_apply_timer = setTimeout(()=>{
					this.applied.prices = [this.priceset[((nv || [])[0]||0)]||0, this.priceset[((nv || [])[1]||this.priceset.length-1)]||0];
				},500);
			}
		},
		'applied.hotel': {
			handler: function(nv,ov){
				console.warn('hotel', nv);
			},
			immediate: true,
			deep: true
		},
	}
}
