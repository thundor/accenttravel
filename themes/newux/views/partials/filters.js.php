let apply_timer;
let slider_apply_timer;
let filter_texts = {
	Price: "Buget",
	Room: "Tip camera",
	Meal: "Tip de masa",
	Merch: "Facilitati oferta",
	Other: "Facilitati hotel",
	Availability: "Disponibilitate",
};

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	data: () => {
		return {
			slider_prices: [],
			priceset: [],
			applied: {
				facilities:{}
			},
			formatted_results: [],
			filtered_results: [],
			count: {
				stars:{},
				facilities:{},
			},
			count2: {
				stars:{},
			},
			prices: [],
			stars: [],
			facilities: {},
			texts: Object.freeze(filter_texts),
		}
	},
	emits: ['filtered'],
	props: {
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
		<div v-if="searching">Searching...</div>
		<div v-else-if="!results.length">No results found...</div>
		<div v-else>
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
				class="px-2"
			>
			<template v-slot:thumb-label="{ modelValue }">
			  <span class="text-pre" v-text="format_price(priceset[modelValue], 'RON', true)"></span>
			</template>
			</v-range-slider>
			
			<span v-text="texts.Hotel"></span>
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
			<v-expansion-panels>
			  <v-expansion-panel v-for="(facs, key) in facilities"
					:title="texts[key]"
				>
				<template v-slot:actions="{ expanded }">
					<v-icon :icon="expanded ? 'mdi-close' : 'mdi-chevron-down'"></v-icon>
				</template>
				<v-expansion-panel-text>
					<v-checkbox  v-for="f in facs" v-model="applied.facilities[key]" :value="f" hide-details density="compact" multiple v-show="!(!this.count.facilities[key] || !this.count.facilities[key][f] && (!applied.facilities || !applied.facilities[key] || !applied.facilities[key][f] || -1 == applied.facilities[key][f].indexOf(f)))">
					  <template v-slot:label>
						<div class="text-h6" v-html="search.merch_type[key][f][1]"></div>
					  </template>
					  <template v-slot:append>
						<v-badge
						  color="info"
						  :content="this.count.facilities[key] && this.count.facilities[key][f] || 0"
						  inline
						></v-badge>
					  </template>
					</v-checkbox>
				</v-expansion-panel-text>
			  </v-expansion-panel>
			</v-expansion-panels>
		</div>
	</div>
	`,
	beforeCreate() {
	},
	mounted() {
		
	},
	computed: {},
	methods: {
		filterResults(){
			
			let no_star_filtered_results = Object.freeze(JSON.parse( JSON.stringify( this.formatted_results ) ).map(h => {
				if(this.applied && this.applied.prices){
					h.Offers = h.Offers.filter(o => {
						if(this.applied.prices[0] && this.applied.prices[0] != this.prices[0] && o.Price < this.applied.prices[0]){
							return false;
						}
						if(this.applied.prices[1] && this.applied.prices[1] != this.prices[1] && o.Price > this.applied.prices[1]){
							return false;
						}
						if(-1 !== Object.keys(this.applied.facilities).filter(k => this.applied.facilities[k] && -1 != this.applied.facilities[k].length).findIndex(k => !(o.facilities[k] && o.facilities[k].filter(j => -1 !== this.applied.facilities[k].indexOf(j)).length == this.applied.facilities[k].length))){
							return false;
						}
						return true;
					})
				}
				return h;
			}).filter(h => {
				if(!h.Offers.length) return false;
				return true;
			}));
			
			this.count2.stars = {};
			no_star_filtered_results.forEach((h) => {
				this.count2.stars[h.Stars] = this.count2.stars[h.Stars] || 0;
				this.count2.stars[h.Stars]++;
			});
			
			this.filtered_results = no_star_filtered_results.filter(h => {
				if(this.applied){
					if(this.applied.stars){
						if(this.applied.stars.length && -1 == this.applied.stars.indexOf(h.Stars)){
							return false;
						}
					}
				}
				return true;
			})
			
			this.count.stars = {};
			this.count.facilities = {};
			this.filtered_results.forEach((h) => {
				this.count.stars[h.Stars] = this.count.stars[h.Stars] || 0;
				this.count.stars[h.Stars]++;
				h.Offers.forEach(o => {
					Object.keys(o.facilities).forEach((k) => {
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
				this.formatted_results = Object.freeze(nv);
				
				this.stars = [];
				this.prices = [];
				this.facilities = {};
				var stars = [];
				var facilities = {};
				var prices = [undefined, undefined];
				
				let priceset = [];
				this.formatted_results.forEach((h) => {
					h.Offers.forEach(o => {
						priceset.push(o.Price);
						if(!prices[0] || o.Price < prices[0]){
							prices[0] = Math.floor(o.Price);
						}
						if(!prices[1] || o.Price > prices[1]){
							prices[1] = Math.ceil(o.Price);
						}
					});
					if(-1 == stars.indexOf(h.Stars)){
						stars.push(h.Stars);
					}
					stars.sort();
					h.Offers.forEach(o => {
						Object.keys(o.facilities).forEach((k) => {
							let fac = o.facilities[k];
							if(fac && fac.length){
								facilities[k] = [...new Set((facilities[k]||[]).concat(fac))];
							}
						});
					});
				});
				priceset.sort(function(a, b){return a - b});
				this.priceset = [... new Set(priceset)];
				this.slider_prices = [0, this.priceset.length-1];
				console.log('priceset', this.priceset);
				
				Object.keys(facilities).map(v => facilities[v].sort((a, b) => Object.keys(this.search.merch_type[v]).indexOf(a) - Object.keys(this.search.merch_type[v]).indexOf(b)));
				this.facilities = facilities;
				this.prices = prices;
				this.stars = stars;
				
				this.filterResults();
				this.applied.stars = [];
				this.applied.prices = this.prices;
			}
		},
		'filtered_results': {
			handler: function(nv,ov){
				clearTimeout(apply_timer);
				apply_timer = setTimeout(()=>{
					this.$emit('filtered', nv);
					console.warn('filtering results', nv);
				},300);
			},
		},
		'applied': {
			handler: function(nv,ov){
				// clearTimeout(apply_timer);
				this.filterResults();
				// apply_timer = setTimeout(()=>{
					// this.applied.prices = prices;
				// },100);
			},
			deep: true
		},
		'slider_prices': {
			handler: function(nv,ov){
				clearTimeout(slider_apply_timer);
				slider_apply_timer = setTimeout(()=>{
					this.applied.prices = [this.priceset[((nv || [])[0]||0)]||0, this.priceset[((nv || [])[1]||this.priceset.length-1)]||0];
				},500);
			}
		},
	}
}
